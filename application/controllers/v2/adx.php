<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Adx extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('google_adx');
        $this->load->model([
            'v2_ads_disapproval_model',
            'v2_google_adx_model',
            'v2_ad_model'
        ]);
    }

    /**
     * Check for all non-approved ads in DB
     * for each ad, its get info from Google AdX and
     * update DB record according to AdX status
     *
     * @return void
     */
    public function update_creatives()
    {
        $ads = $this->v2_ads_disapproval_model->get_non_approved_ads();

        while( !empty($ads) ) {
            $ad = array_pop($ads);

            $disapproval_id = $ad['id'];
            $io = $ad['io'];
            $ad_id = $ad['ad_id'];

            $buyer_creative_id = 'ProData-' . $io . '-' . $ad_id;
            $creative = $this->google_adx->get_creative($buyer_creative_id);
            if ( $creative['status'] == 'SUCCESS' ) {
                $creative = $creative['creative'];

                $auction_status = $creative['openAuctionStatus'];
                $deal_status = $creative['dealsStatus'];
                $restrictions = $creative['servingRestrictions'];

                /**
                 * NOTE:
                 * Here we're now using `openAuctionStatus` field's value
                 * for update ad status in DB
                 *
                 * Possible values of `openAuctionStatus` are:
                 *  "APPROVED"
                 *  "CONDITIONALLY_APPROVED"
                 *  "DISAPPROVED"
                 *  "NOT_CHECKED"
                 *  "UNKNOWN"
                 */
                $status = $auction_status == 'APPROVED' || $auction_status == 'CONDITIONALLY_APPROVED'
                            ? 'APPROVED'
                            : (
                                $auction_status == 'DISAPPROVED'
                                    ? 'DISAPPROVED'
                                    : 'PENDING'
                            );

                $restrictions = json_encode($restrictions);
                if ( $status == 'APPROVED' ) {
                    $restrictions = null;
                }

                $this->v2_ads_disapproval_model->update($disapproval_id, [
                    'status' => $status,
                    'disapproval_reasons' => $restrictions
                ]);
            }
        }
    }

    public function resubmit_creatives()
    {
        //$creatives = $this->v2_ad_model->get_all_ads_with_campaign_info();
        $creatives = $this->v2_ad_model->get_active_campaigns_ads_for_adx_submit();
        foreach ( $creatives as $creative ) {
            $adx_response = $this->v2_google_adx_model->insert_creative([
                'campaign' => [
                    'io' => $creative['campaign_io'],
                    'network_name' => $creative['network_name'],
                    'campaign_type' => $creative['campaign_type'],
                ],
                'ad' => [
                    'id' => $creative['id'],
                    'creative_width' => $creative['creative_width'],
                    'creative_height' => $creative['creative_height'],
                    'destination_url' => $creative['destination_url'],
                ]
            ]);

            debug($adx_response, 0);
        }
    }

    public function pretargeting()
    {
        $this->google_adx->get_pretargeting_configs_list();
        exit;
    }

    public function load_google_adword_geo_table_csv()
    {
        $this->load->model('google_adword_geolocation_model');

        $path = FCPATH . 'AdWords_API_Location_Criteria_2017-04-20.csv';

        if ( !file_exists($path) ) die(basename($path) . " file does not exists");

        if ( is_resource($handle = fopen($path, 'r')) ) {
            $c = 0;
            $rows = [];
            while( ($line = fgetcsv($handle)) !== false ) {
                /*if ( $c > 0 ) {
                    $row = [
                        'criteria_id' => trim($line[0]), // Criteria ID
                        'location_name' => trim($line[1]), // Name
                        'country_code' => trim($line[4]), // Country Code
                        'target_type' => trim($line[5]), // Target type
                        'is_active' => strtolower(trim($line[6])) == 'active' ? 'Y' : 'N', // Status
                    ];
                    $status = $this->google_adword_geolocation_model->insert($row);
                }
                $c++;*/

                if ( $c > 0 ) {
                    $rows[] = [
                        'criteria_id' => trim($line[0]), // Criteria ID
                        'location_name' => trim($line[1]), // Name
                        'country_code' => trim($line[4]), // Country Code
                        'target_type' => trim($line[5]), // Target type
                        'is_active' => strtolower(trim($line[6])) == 'active' ? 'Y' : 'N', // Status
                    ];
                }

                if ( count($rows) > 200 ) {
                    $stats = $this->google_adword_geolocation_model->insert_batch($rows);
                    $rows = [];
                }

                $c++;
            }
        } else{
            die("Invalid Resource.");
        }
    }
}