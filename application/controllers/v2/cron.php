<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH.'third_party/php-excel/PHPExcel.php';
class Cron extends CI_Controller    {
    public $viewArray = array();

    public function __construct()   {
        parent::__construct();

        $this->load->helper("url");
        $this->load->helper('cookie');
        $this->load->library('user_agent');

        $this->load->library("parser");
        $this->load->library("session");
        $this->load->library('ion_auth');
        $this->load->library('clickcap');
        $this->load->model("Campclick_model");
        $this->load->model("Domains_model");
        $this->load->model("Vendor_model");
        $this->load->model("Report_model");
        $this->load->model("Monitor_model");
        $this->load->model("Finditquick_model");
        $this->load->model("Billing_model");
        $this->load->model("Google_model_site");
        $this->load->model("Common_model");
        $this->load->model("Rtb_model");
        $this->load->model("V2_network_country_criterion_model");
        $this->load->model("V2_network_state_criterion_model");
        $this->load->model("V2_network_zip_criterion_model");
        $this->load->model("V2_network_carrier_criterion_model");
        $this->load->model("V2_network_gender_criterion_model");
        $this->load->model("V2_network_age_criterion_model");
        $this->load->model('Google_adword_geolocation_model');
        $this->load->model('Country_model');
        //$this->load->model("V2_campaign_network_location_rel_model");
//      $this->load->model("V2_campaign_network_carrier_rel_model");
//      $this->load->model("V2_campaign_network_gender_rel_model");
        $this->load->model("V2_campaign_network_criteria_rel_model");
//      $this->load->model("V2_campaign_network_remarketing_rel_model");
        $this->load->model("Userlist_io_model");
        $this->load->model("Userlist_vertical_model");
        $this->load->model("V2_time_parting_model");

        $this->viewArray['current_url'] = current_url();
        $this->viewArray['base_url'] = base_url();
        $this->viewArray['site_url'] = site_url();

    }

    private function log_active_ads_and_campaigns($log_type = 'log', $data)
    {
        $log_dir = FCPATH . 'logs/';
        $file_path = $log_dir . $log_type . '.log';
        $data = date('Y-m-d H:i:s') . "\n" . json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL;
        try {
            file_put_contents($file_path, $data);
        } catch(Exception $e){}
    }

    private function prepare_pretargeting_data(array $pretargeting)
    {
        $states_criteria_ids = [];
        $zips_criteria_ids = [];

        // $state_code = $this->Country_model->get_state_iso_code_from_state_name($state_name = 'south dakota');

        // get Google criteria Ids of States
        if ( !empty($pretargeting['states']) ) {
            foreach ( $pretargeting['states'] as $state_code ) {
                $state_name = $this->Country_model->get_state_name_from_state_iso_code($state_iso_code = $state_code);
                $criteria_id = $this->Google_adword_geolocation_model->get_criteria_id_by_location_name(
                    $location_name = $state_name,
                    $params = array(
                        'country_code' => 'US',
                        'target_type' => 'state'
                    )
                );
                if ( !empty($criteria_id['criteria_id']) ) $states_criteria_ids[] = $criteria_id['criteria_id'];
            }
        }

        // get Google criteria Ids of States
        if ( !empty($pretargeting['zips']) ) {
            foreach ( $pretargeting['zips'] as $zip ) {
                $criteria_id = $this->Google_adword_geolocation_model->get_criteria_id_by_location_name(
                    $location_name = $zip,
                    $params = array(
                        'country_code' => 'US',
                        'target_type' => 'postal code'
                    )
                );
                if ( !empty($criteria_id['criteria_id']) ) $zips_criteria_ids[] = $criteria_id['criteria_id'];
            }
        }

        $pretargeting['zips_criteria_ids'] = $zips_criteria_ids;
        $pretargeting['states_criteria_ids'] = $states_criteria_ids;

        return $pretargeting;
    }


    public function update_trafficshape()   {
        $this->load->model("Trafficshape_model");

        $processing = $this->Campclick_model->get_campaign_list(null, "Y", "AND campaign_is_started='Y' AND campaign_is_complete='N'");

        foreach($processing as $p)  {
            // skip the campaigns which are NOT traffic shaped
            if ($p['is_traffic_shape'] == "N")
                continue;

            print_r($p);
        }
    }


    public function map_active_ads_to_geo_redis($ads = array(), array $pretargeting = [])
    {
        set_time_limit(-1);

        if ( empty($ads) ) {
            $this->load->model("V2_ad_model");
            $this->load->model('v2_campaign_category_model');

            $ads = $this->V2_ad_model->get_active_campaigns_ads();
            foreach ($ads as $k => $value) {
                $iab_categories = "";
                $assoc_iab_categories = $this->v2_campaign_category_model->get_associated_iab_categories_by_campaign_id($ad['campaign_id']);
                if ( !empty($assoc_iab_categories) ) {
                    $iab_categories = array_column($assoc_iab_categories, 'iab_category_id');
                }
                $ads[$k]['camp_iabs'] = trim(implode(',', $iab_categories));
            }
        }

        // clean up
        //$this->clickcap->delete_all_active_ads_geomap();

        $start = microtime(true);
        $start_at = date('Y-m-d H:i:s');

        foreach ( $ads as $ad ) {
            $geo_data = [];

            // handle creative type
            $ad['creative_type'] = strtoupper(trim($ad['creative_type']));
            $ad['creative_type'] = $ad['creative_type'] == 'DISPLAY_ADS'
                                    ? 'DISPLAY'
                                    : $ad['creative_type'];

            // zip level ads
            if ( !empty($ad['zip']) ) {
                $zips = explode(',', $ad['zip']);
                $radius = trim($ad['radius']);
                $map = [];
                $geo_keys = [];
                $final_zips = [];

                // pull all zip addresses within
                // ad radius from current $zip
                // and make final zips list
                while ( !empty($zips) ) {
                    $zip = array_pop($zips);
                    $zip = (string)trim($zip);

                    $zips_in_radius = $this->clickcap->get_all_zips_within_ad_radius(
                        $zip,
                        $radius
                    );

                    if ( !empty($zips_in_radius) ) {
                        $final_zips = array_merge($final_zips, $zips_in_radius);
                    }
                    $final_zips[] = $zip;
                    $zips = array_diff($zips, $final_zips);
                }

                $cache_final_zips = $final_zips = array_unique($final_zips);

                while ( !empty($cache_final_zips) ) {
                    $zip = array_pop($cache_final_zips);
                    $zip = (string)trim($zip);

                    // pull state info and all zip within that state
                    $state_zips = $this->clickcap->get_state_by_zip($zip);
                    $state = $state_zips['state'];
                    $all_zips = $state_zips['all_zips'];

                    if ( !empty($state) ) {
                        $common_zips = array_intersect($cache_final_zips, $all_zips);
                        $common_zips[] = $zip;
                        $common_zips = array_unique($common_zips);
                        if ( !empty($common_zips) ) {
                            $geo_keys_tmp = array_map(function($zip) use ($state) {
                                return $zip . '-' . $state;
                            }, $common_zips);
                            $geo_keys = array_merge($geo_keys, $geo_keys_tmp);
                            $cache_final_zips = array_diff($cache_final_zips, $common_zips);
                        }
                    }
                }

                // cache zips for pretargeting config
                $pretargeting['zips'] = array_merge($pretargeting['zips'], $final_zips);
                unset($final_zips); // clear zip caches

                $geoposes = $this->clickcap->get_geopos_by_geokey($geo_keys);
                foreach ( $geo_keys as $index => $geo_key ) {
                    $geopos = $geoposes[$index];
                    if ( !empty($geopos) ) {
                        $ad_key = $ad['id']
                                . '-ZIP'
                                . '-' . $ad['creative_type']
                                . '-' . ($ad['creative_width'] . '_' . $ad['creative_height'])
                                . '-' . $geo_key;

                        // if IAB category exists, then make that as
                        // a part of the AD key
                        if ( !empty($ad['camp_iabs']) ) {
                            $ad_key .= '-(' . $ad['camp_iabs'] . ')';
                        }

                        $geo_data[] = [$geopos[0], $geopos[1], $ad_key];
                    }
                }
                $status = $this->clickcap->store_active_ad_geomap($geo_data);
            }
            // state level ads
            else if ( !empty($ad['state']) ) {
                $state = strtoupper(trim($ad['state']));
                $states = explode(',', $state);

                // cache states for pretargeting config
                $pretargeting['states'] = array_unique(array_merge($pretargeting['states'], $states));

                foreach ( $states as $state ) {
                    $zips = $this->clickcap->get_zips_by_state($state);
                    $geo_keys = array_map(function($zip) use ($state) {
                        return $zip . '-' . $state;
                    }, $zips);

                    $geoposes = $this->clickcap->get_geopos_by_geokey($geo_keys);

                    foreach ( $geo_keys as $index => $geo_key ) {
                        $geopos = $geoposes[$index];
                        if ( !empty($geopos) ) {
                            $ad_key = $ad['id']
                                    . '-STATE'
                                    . '-' . $ad['creative_type']
                                    . '-' . ($ad['creative_width'] . '_' . $ad['creative_height'])
                                    . '-' . $geo_key;

                            // if IAB category exists, then make that as
                            // a part of the AD key
                            if ( !empty($ad['camp_iabs']) ) {
                                $ad_key .= '-(' . $ad['camp_iabs'] . ')';
                            }

                            $geo_data[] = [$geopos[0], $geopos[1], $ad_key];
                        }
                    }
                }

                $status = $this->clickcap->store_active_ad_geomap($geo_data);
            }
            // country level ad
            else {
                $ad_key = $ad['id']
                        . '-COUNTRY'
                        . '-' . $ad['creative_type']
                        . '-' . ($ad['creative_width'] . '_' . $ad['creative_height']);

                // if IAB category exists, then make that as
                // a part of the AD key
                if ( !empty($ad['camp_iabs']) ) {
                    $ad_key .= '-(' . $ad['camp_iabs'] . ')';
                }

                $this->clickcap->store_active_country_level_ad($ad_key, $ad['id']);
            }
        }

        $this->clickcap->keep_cron_exec_time_track(
            'map_active_ads_to_geo_redis',
            [
                'startAt' => $start_at,
                'endAt' => date('Y-m-d H:i:s'),
                'execTime' => ((microtime(true) - $start) / 60) . ' minutes'
            ]
        );

        // Update Pretargeting Config
        $this->load->library('google_adx');
        $pretargeting = $this->prepare_pretargeting_data($pretargeting);
        //$this->google_adx->create_pretargeting_config($pretargeting);
        $res = $this->google_adx->patch_pretargeting_config($config_id = 10664, $pretargeting);

        // Log Pretargeting settings
        if ( $res ) {
            try {
                // log pretargeting request data to AdX
                $this->log_active_ads_and_campaigns('pretargeting_config_req', $pretargeting);

                // log pretargeting response from AdX
                $res = $res->toSimpleObject();
                $this->log_active_ads_and_campaigns('pretargeting_config_res', $res);
            } catch(Exception $e) {}
        }

    }

    public function add_active_ads_to_redis($campaign_id = null) {

        $this->load->model("V2_ad_model");
        $this->load->model('v2_campaign_category_model');
        $ads = $this->V2_ad_model->get_active_campaigns_ads($campaign_id); //echo '<pre>'; var_dump($ads); exit;

        //print_r($ads);
        //$keys = $this->clickcap->get_keys('*');
        //$deleted = $this->clickcap->delete_all_active_ads(); //var_dump($deleted); exit;
        echo '<pre>';

        $pretargeting = [
            'states' => [],
            'zips' => [],
            'dims' => []
        ];

        $whs = [];
        foreach($ads as $k => $ad){

            // cache ads dimensions for pretargeting config
            $wh = $ad['creative_width'] . 'x' . $ad['creative_height'];
            if ( !in_array($wh, $whs) ) {
                $whs[] = $wh;
                $pretargeting['dims'][] = ['width' => $ad['creative_width'], 'height' => $ad['creative_height']];
            }

            // get IAB Category associations
            $iab_categories = "";
            $assoc_iab_categories = $this->v2_campaign_category_model->get_associated_iab_categories_by_campaign_id($ad['campaign_id']);
            if ( !empty($assoc_iab_categories) ) {
                $iab_categories = array_column($assoc_iab_categories, 'iab_category_id');
            }
            $ad['camp_iabs'] = trim(implode(',', $iab_categories));
            $ads[$k]['camp_iabs'] = $ad['camp_iabs'];

            // Add Tracking pixel to ad object if creative_type = RICH_MEDIA
            if ( $ad['creative_type'] == 'RICH_MEDIA' ) {
                $tracking_pixel_url = $this->config->item('tracking_pixel_url');
                $tracking_pixel_url = str_replace("{{campaign_id}}", $ad['campaign_id'], $tracking_pixel_url);

                $tracking_pixel_html = $this->config->item('tracking_pixel_html');
                $tracking_pixel_html = str_replace("{{tracking_pixel_url}}", $tracking_pixel_url, $tracking_pixel_html);

                $ad['tracking_pixel'] = htmlspecialchars($tracking_pixel_html);
                $ad['tracking_pixel_url'] = $tracking_pixel_url;
            }

            if ($ad['zip'] == "" && $ad['state'] == "") {

                $ad['beacon_url'] = base_url().'tracking/beacon/'.$ad['campaign_id'].'/'.$ad['id'];

                 // FIX: use http for beacon_url instead of https protocol
                $ad['beacon_url'] = str_replace('https', 'http', $ad['beacon_url']);

                if($ad['radius']==0) {
                    $ad['radius']='';
                }
                $this->clickcap->set_ad($ad);
                //print_r($ad);
            } else {
                $ad['beacon_url'] = base_url().'tracking/beacon/'.$ad['campaign_id'].'/'.$ad['id'];

                // FIX: use http for beacon_url instead of https protocol
                $ad['beacon_url'] = str_replace('https', 'http', $ad['beacon_url']);

                // this is state and/or zip code
                if ($ad['zip'] != "") {
                    if($ad['radius'] && $ad['radius']<=50) { //var_dump($ad);
                        $ad['radius'] = 75;
                    }
                    $ad['zip'] = str_replace(",", "|", $ad['zip']);
                    $this->clickcap->set_ad($ad);
                } else if ($ad['state'] != "") {
                    $ad['state'] = str_replace(",", "|", $ad['state']);
                    $this->clickcap->set_ad($ad);
                } else {
                    // shouldnt get here!
                }
            }
            //var_dump($ad);
        }

        // Log active ads
        try {
            $this->log_active_ads_and_campaigns('add_active_ads_to_redis', $ads);
        } catch(Exception $e){}


        // update ads geo mapping
        $this->map_active_ads_to_geo_redis($ads, $pretargeting);

        //--exit;
    }

    public function add_active_campaigns_to_redis() {

        $this->load->model("V2_ad_model");
        $this->load->model("V2_master_campaign_model");
        $this->load->model('V2_campaign_cost_model');
        $this->load->model('v2_campaign_category_model');
        $this->load->model('v2_prodata_id_retargeting_model');

        $ads = $this->V2_ad_model->get_active_campaigns_ads(); //echo '<pre>'; var_dump($ads); //exit;
        $campaigns = $this->V2_master_campaign_model->get_active_campaigns_for_redis_with_cost();

        //debug($campaigns, 0);
        $deleted = $this->clickcap->delete_all_active_campaigns(); //var_dump($deleted); exit;
        $this->clickcap->cleanup_prodata_id_retargeting_data(); // cleanup prodata Id retargeting DB
        $this->clickcap->cleanup_retargeting_ip_data(); // cleanup retargeting IPs db form redis
        $this->clickcap->delete_all_active_ads_geomap(); // cleanup active ads geo map form redis
        $this->clickcap->delete_all_active_ads(); // delete all active ads form redis

        echo '<pre>';
        $ads_sorted = [];
        foreach($ads as $ad){
            $ads_sorted[$ad['campaign_id']][] = $ad['id'];
        }

        //var_dump($ads_sorted); exit;
        foreach($campaigns as $k => $campaign){
            $spend = $this->V2_campaign_cost_model->get_hourly_spend_by_campaign($campaign['id']);

            if ($campaign['campaign_status'] == "ACTIVE" && (($spend['accumulated_spend'] < $spend['max_spend_for_time']) || ($spend['accumulated_spend'] == 0))) {

                //
                // Ads are under our spend, add them into the system.
                //

                $daily_cost = $this->V2_campaign_cost_model->get_daily_cost_by_campaign_id($campaign['id']);

                // get IAB Category associations
                $iab_categories = "";
                $assoc_iab_categories = $this->v2_campaign_category_model->get_associated_iab_categories_by_campaign_id($campaign['id']);
                if ( !empty($assoc_iab_categories) ) {
                    $iab_categories = array_column($assoc_iab_categories, 'iab_category_id');
                }
                $campaign['iab_categories'] = json_encode($iab_categories);

                // Get Retargeting IP association and save to Redis
                //
                // NOTE:
                //  Here we're saving retargeting IPs data as JSON string with campaign hash
                //  Also we're saving individual IP as `zadd` to Redis DB of campaign
                //  `zadd` will help to search by `zrangebyscore` in future if we need
                //
                //  Redis Key is: campaignRetargetingIPs
                //
                $retargeting_ips = "";
                $assoc_retargeting_ips = $this->v2_retargeting_ip_model->get_associated_retargeting_ips_by_campaign_id($campaign['id']);
                if ( !empty($assoc_retargeting_ips) ) {
                    $retargeting_ips = json_encode($assoc_retargeting_ips);
                }
                $campaign['retargeting_ips'] = $retargeting_ips;

                //var_dump($daily_cost['cost']);
                $campaign['spend_today'] = $daily_cost;
                $campaign['ads'] = json_encode($ads_sorted[$campaign['id']]);

                $campaigns[$k]['retargeting_ips'] = $retargeting_ips;
                $campaigns[$k]['spend_today'] = $daily_cost;
                $campaigns[$k]['ads'] = $campaign['ads'];

                //debug($campaign, 0);
                //var_dump($campaign);
                $this->clickcap->set_campaign($campaign);

                // get associated ProData Ids of a campaign
                // and load to redis for retargeting from RTB API
                $campaign_id = $campaign['id'];
                $prodata_ids = $this->v2_prodata_id_retargeting_model->get_associate_prodata_ids_by_campaign_id($campaign_id);
                if ( !empty($prodata_ids) && !empty($prodata_ids['prodata_ids']) ) {
                    $prodata_ids = array_filter(explode(',', $prodata_ids['prodata_ids']));
                    foreach ( $prodata_ids as $prodata_id ) {
                        $this->clickcap->load_prodata_id_retargeting_data($prodata_id, array($campaign_id));
                    }
                }

                // Check for pre-loaded retargeting IO
                // and if the exists then load to redis for RTB API
                $retargeting_io = $campaign['retargeting_io'];
                if ( !empty($retargeting_io) ) {
                    $ios = explode(',', trim($retargeting_io));
                    foreach ( $ios as $io ) {
                        $campaign_id = $this->V2_master_campaign_model->get_campaign_id_by_io($io);
                        if ( !empty($campaign_id) ) {
                            $prodata_ids = $this->v2_prodata_id_retargeting_model->get_associate_prodata_ids_by_campaign_id($campaign_id);
                            if ( !empty($prodata_ids) && !empty($prodata_ids['prodata_ids']) ) {
                                $prodata_ids = array_filter(explode(',', $prodata_ids['prodata_ids']));
                                foreach ( $prodata_ids as $prodata_id ) {
                                    $this->clickcap->load_prodata_id_retargeting_data($prodata_id, array($campaign_id));
                                }
                            }
                            // when pre-selected campaign is not related to any ProData Id
                            else {
                                // TODO: Discuss with Jason
                            }
                        }
                    }
                }

                //
                // Load Active ads to redis
                //
                $this->add_active_ads_to_redis($campaign['id']);
            } else {
                //
                // Ads are beyond spend, skip.
                //
                print_r($campaign);
               continue;
            }
        }

        // Log active campaigns
        try {
            $this->log_active_ads_and_campaigns('add_active_campaigns_to_redis', $campaigns);
        } catch(Exception $e){}

        exit;
    }

    public function add_domains_to_redis() {

        $this->load->model('V2_log_model');
        $filePath = 'v2/files/domains_list.txt';

        $deleted = $this->clickcap->delete_all_domains();
        //$deleted = $this->clickcap->get_domains(); var_dump($deleted); exit;

        $domains = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        var_dump($domains);
        if(!$domains){
            return false;
        }

        $invalid = false;
        foreach ($domains as $key => $domain) {

            if(!filter_var('http://'.trim($domain), FILTER_VALIDATE_URL) || !substr_count(trim($domain), '.')) {
                $invalid = true;
                continue;
            }
            //var_dump(555, $domain, $key+1);
            $this->clickcap->set_domain($key+1,$domain);
        }

        if($invalid) {
            $this->V2_log_model->create(7777, 'Invalid data in file domains', 'domains');
        }

    }

    public function add_ips_to_redis() {

        $this->load->model('V2_log_model');
        $filePath = 'v2/files/ip_addresses_list.txt';

        $deleted = $this->clickcap->delete_all_ips();

        $ips = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if(!$ips){
            return false;
        }

        $invalid = false;
        foreach ($ips as $key => $ip) {
            $ip = trim($ip);
            $parts = explode("/", $ip);
            $only_ip = $parts[0];
            $netmask = $parts[1];
            if(!filter_var(trim($only_ip), FILTER_VALIDATE_IP)) {
                var_dump($ip);
                $invalid = true;
                continue;
            }
            if($netmask && $netmask >32) {
                var_dump($ip);
                $invalid = true;
                continue;
            }
            $this->clickcap->set_ip($key+1,$ip);
        }

        if($invalid) {
            $this->V2_log_model->create(7777, 'Invalid data in file ips', 'ips');
        }

    }

    public function queue_invoices_to_quickbooks()  {
        // get only campaigns with end criteria
        $this->Billing_model->build_invoice_queue("Y");
    }

    public function queue_invoices_to_quickbooks_without_end_criteria()  {
        // get only campaigns without end criteria
        $this->Billing_model->build_invoice_queue("N");
    }

    public function get_email_campaigns() {

        //exit;
        $this->load->model("V2_master_campaign_model");
        $this->load->model("V2_users_model");

        $users = $this->V2_users_model->get_all_email_linked_users();

        if(!$users){
            echo 'No user'; exit;
        }

        $existing_campaigns = $this->V2_master_campaign_model->get_email_campaigns();
        //var_dump($users, $existing_campaigns); exit;
        foreach($existing_campaigns as $existing_campaign) {
            $existing_io[] = $existing_campaign['io'];
        }

        foreach($users as $user) {

            $ch = curl_init();
            $url = 'http://report-site.com/report_api/iobyemail';

            $data = array(
                'email' => $user['email'],
            );

            $headers = array("X-ProDataFeed-Auth: accf71e711cedbd30e5accd0633d8b44");

            $options = array(
                CURLOPT_URL => $url,
                //CURLOPT_HEADER => true,
                //CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => TRUE,
                CURLOPT_POSTFIELDS => $data
            );

            curl_setopt_array($ch, $options);

            $response = curl_exec($ch);

            if (FALSE === $response) {
                $curlErr = curl_error($ch);
                $curlErrNum = curl_errno($ch);

                curl_close($ch);
                throw new Exception($curlErr, $curlErrNum);
            }
            echo '<pre>';
            $info = curl_getinfo($ch);
            curl_close($ch);
            $response = json_decode(substr($response, 0, -38), true); //var_dump($response); exit;
            if($response['status'] == 'SUCCESS') {
                $this->load->model('V2_log_model');
                foreach ($response['campaigns'] as $campaign) {
                    if (!in_array($campaign['io'], $existing_io)) {
                        $campaign['campaign_type'] = 'EMAIL';
                        $campaign['network_campaign_status'] = 'ACTIVE';
                        $campaign['campaign_status'] = 'ACTIVE';
                        $campaign['name'] = $campaign['create_name'];
                        $campaign['userid'] = $user['id'];
                        unset($campaign['create_name']);
                        if(isset($campaign['subject'])){
                            unset($campaign['subject']);
                        }
                        $id = $this->V2_master_campaign_model->create($campaign);
                        $campaign['id'] = $id;
                        $response_google = $this->Common_model->create_audience('GOOGLE',$campaign); var_dump($response_google);
                        // return snipped codes to report-site
                        $google_send_status = $this->send_audience_data_for_email_campaigns($response_google);
                        $response_facebook = $this->Common_model->create_audience('FACEBOOK',$campaign); var_dump($response_facebook);
                        if(!$google_send_status) {
                           $this->V2_log_model->create($campaign['id'], 'Campaign audience didnt send to report site '.$response_google['remarketing_list_id'], 'google_audience');
                        }
                        $fb_send_status = $this->send_audience_data_for_email_campaigns($response_facebook);
                        if(!$fb_send_status) {
                            $this->V2_log_model->create($campaign['id'], 'Campaign audience didnt send to report site '.$response_facebook['remarketing_list_id'], 'fb_audience');
                        }
//                        var_dump($response, $response1); exit;
                        //return snipped codes to report-site
                    } else {
                        echo 1;
                    }
                }
            } else {
                exit;
            }
        }
        var_dump($response); exit;

    }

    public function send_audience_data_for_email_campaigns($data= null) {
//        $data = array('snippet'=>'snip code','remarketing_list_id'=>46545646, 'io'=>'NYU0929', 'network'=>'GOOGLE');
        $ch = curl_init();
        $url = 'http://report-site.com/ads_api/jssnip/format/json';

        $headers = array("X-ProDataFeed-Auth: accf71e711cedbd30e5accd0633d8b44");

        $options = array(
            CURLOPT_URL => $url,
            //CURLOPT_HEADER => true,
            //CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $data
        );

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);

        if (FALSE === $response) {
            $curlErr = curl_error($ch);
            $curlErrNum = curl_errno($ch);

            curl_close($ch);
            throw new Exception($curlErr, $curlErrNum);
        }
        echo '<pre>';
        $info = curl_getinfo($ch);
        curl_close($ch);
        $response_array = json_decode($response, true);
        if($response_array['status']=='SUCCESS'){
            return true;
        } else {
            return false;
        }
        //var_dump($response);
    }

    public function update_email_campaigns_status() {

        $this->load->model("V2_master_campaign_model");

        $campaigns = $this->V2_master_campaign_model->get_email_campaigns();

        foreach($campaigns as $key=>$campaign) { echo $campaign['io'].'<br>';
//            $campaign['io'] = '33688';
//            $campaign['id'] = 1;

            $ch = curl_init();
            $url = 'http://report-site.com/report_api/campaignstatus/io/'.$campaign['io'].'.json';

            $headers = array("X-ProDataFeed-Auth: accf71e711cedbd30e5accd0633d8b44");

            $options = array(
                CURLOPT_URL => $url,
                //CURLOPT_HEADER => true,
                //CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => FALSE,
                //CURLOPT_POSTFIELDS => $data
            );

            curl_setopt_array($ch, $options);

            $response = curl_exec($ch);

            if (FALSE === $response) {
                $curlErr = curl_error($ch);
                $curlErrNum = curl_errno($ch);

                curl_close($ch);
                throw new Exception($curlErr, $curlErrNum);
            }
            echo '<pre>';

            curl_close($ch);
            //var_dump($url,$response); exit;
            $response = json_decode($response, true); var_dump($response);
            $statuses = ['ACTIVE'=>'ACTIVE', 'COMPLETE'=>'COMPLETED', 'PENDING'=>'SCHEDULED'];

            if($statuses[$response['status']] != $campaign['campaign_status']) {
                $data_for_update['campaign_status'] = $statuses[$response['status']];
                if($statuses[$response['status']] == 'ACTIVE') {
                    $data_for_update['network_campaign_status'] = $statuses[$response['status']];
                } else {
                    $data_for_update['network_campaign_status'] = 'PAUSED';
                }
                $data_for_update['campaign_end_datetime'] = date('Y-m-d H:i:s');
                $this->V2_master_campaign_model->update($campaign['id'],$data_for_update);
            }

        }
        var_dump($response); exit;

    }

    public function get_email_reporting() {

        $this->load->model("V2_master_campaign_model");
        $this->load->model("V2_email_campaign_reporting_model");
        $this->load->model("V2_email_campaign_link_reporting_model");
        $this->load->model("V2_email_campaign_additional_reporting_model");

        $campaigns = $this->V2_master_campaign_model->get_active_email_campaigns();

        foreach($campaigns as $key=>$campaign) { echo $campaign['io'].'<br>';
//            $campaign['io'] = '33688';
//            $campaign['id'] = 1;
            if($campaign['id'] == 1791) {
                continue;
            }
            $campaign['campaign_start_datetime'];
            $start_date = date('Y-m-d H:i:s', strtotime($campaign['campaign_start_datetime']));
            $end_date = date('Y-m-d H:i:s');
            echo $start_date, $end_date;
            $ch = curl_init();
            //$url = 'http://report-site.com/report_api/tracking/io/'.$campaign['io'].'.json';
            $url = 'http://report-site.com/report_api/additional/io/'.$campaign['io'].'/date_start/'.$start_date.'/date_end/'.$end_date.'.json';
            //$url = 'http://report-site.com/report_api/additional/io/NISMEL0623/date_start/2016-06-05 12:59:00/date_end/2016-06-26 12:59:00.json';
            //$url = urlencode($url); var_dump($url); exit;
            $headers = array("X-ProDataFeed-Auth: accf71e711cedbd30e5accd0633d8b44");

            $options = array(
                CURLOPT_URL => str_replace(' ', '%20', $url),
                //CURLOPT_HEADER => true,
                //CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => FALSE,
                //CURLOPT_POSTFIELDS => $data
            );

            curl_setopt_array($ch, $options);

            $response = curl_exec($ch);

            if (FALSE === $response) {
                $curlErr = curl_error($ch);
                $curlErrNum = curl_errno($ch);

                curl_close($ch);
                throw new Exception($curlErr, $curlErrNum);
            }
            echo '<pre>';

            curl_close($ch);
            //var_dump($url,$response); exit;
            $response = json_decode($response, true);
            if($response['status'] == 'SUCCESS') {

                $reports = $response['reports'];
                var_dump(666);
                $link_reports = $this->V2_email_campaign_link_reporting_model->get_clicks_count_by_campaign_id($campaign['id']);
                $campaign_reports = $this->V2_email_campaign_reporting_model->get_clicks_count_by_campaign_id($campaign['id']);
                $email_additional_reports = $this->V2_email_campaign_additional_reporting_model->get_all_by_campaign_id($campaign['id']);
                var_dump(555);
                foreach($link_reports as $key=>$value) {
                    $existing_report[$value['link_id']] = $value;
                }
                //var_dump($existing_report); exit;
//                foreach ($response['reports']['report'] as $report) {
//
//                    $data_for_insert['reportsite_url'] = $report['Reportsite_URL'];
//                    $data_for_insert['campaign_id'] = $campaign['id'];
//                    $data_for_insert['campaign_io'] = $campaign['io'];
//                    $data_for_insert['destination_url'] = $report['Destination_URL'];
//                    if($existing_report) {
//                        $data_for_insert['clicks_count'] = $report['ClickCount'] - $existing_report[$report['Reportsite_URL']]['clicks_count'];
//                        $data_for_insert['unique_clicks_count'] = $report['UniqueCnt'] - $existing_report[$report['Reportsite_URL']]['unique_clicks_count'];
//                        $data_for_insert['mobile_clicks_count'] = $report['MobileCnt'] - $existing_report[$report['Reportsite_URL']]['mobile_clicks_count'];
//                        if((int)$report['ImpressionCnt']>0){
//                            $data_for_insert['impressions_count'] = $report['ImpressionCnt'] - $existing_report[$report['Reportsite_URL']]['impressions_count'];
//
//                        }
//                    } else {
//                        $data_for_insert['clicks_count'] = $report['ClickCount'];
//                        $data_for_insert['unique_clicks_count'] = $report['UniqueCnt'];
//                        $data_for_insert['mobile_clicks_count'] = $report['MobileCnt'];
//                        $data_for_insert['impressions_count'] = (int)$report['ImpressionCnt'];
//                    }
//
//                    if($data_for_insert['clicks_count']>0){
//                        $this->V2_email_campaign_reporting_model->create($data_for_insert);
//                    }
                    $additional_data_for_insert['date_created'] = $end_date;
                    $additional_data_for_insert['campaign_id'] = $campaign['id'];
                    $additional_data_for_insert['campaign_io'] = $campaign['io'];
                    $additional_data_for_insert['campaign_so'] = $campaign['so'];
                //var_dump($campaign_reports);
                    if($campaign_reports) {
                        var_dump($reports["mobile_results"]['mobile'],$campaign_reports['mobile_clicks_count']);
                        $additional_data_for_insert['unique_clicks_count'] = $reports['unique_clickers'] - $campaign_reports['unique_clicks_count'];
                        $additional_data_for_insert['impressions_count'] = $reports['impressions_total'] - $campaign_reports['impressions_count'];
                        $additional_data_for_insert['mobile_clicks_count'] = $reports["mobile_results"]['mobile'] - $campaign_reports['mobile_clicks_count'];

                    } else {

                        $additional_data_for_insert['unique_clicks_count'] = $reports['unique_clickers'];
                        $additional_data_for_insert['impressions_count'] = $reports['impressions_total'];
                        $additional_data_for_insert['mobile_clicks_count'] = $reports["mobile_results"]['mobile'];

                    }
                    if((int)$additional_data_for_insert['unique_clicks_count'] > 0 || (int)$additional_data_for_insert['impressions_count'] > 0 || (int)$additional_data_for_insert['mobile_clicks_count'] > 0) {

                        $this->V2_email_campaign_reporting_model->create($additional_data_for_insert);
                    }

                    $additional_data_for_update['non_mobile_clicks_count'] = $reports["mobile_results"]['non_mobile'];
                    $additional_data_for_update['mobile_devices'] = json_encode($reports["mobile_devices"]);
                    $additional_data_for_update['browser_results'] = json_encode($reports["browser_results"]);
                    $additional_data_for_update['browsers_shares'] = json_encode($reports["browsers_shares"]);
                    $additional_data_for_update['platform_results'] = json_encode($reports["platform_results"]);
                    $additional_data_for_update['date_created'] = $end_date;

                    if($email_additional_reports) {
                        $this->V2_email_campaign_additional_reporting_model->update($email_additional_reports['id'],$additional_data_for_update);
                    } else {
                        $additional_data_for_update['campaign_id'] = $campaign['id'];
                        $additional_data_for_update['campaign_io'] = $campaign['io'];
                        $additional_data_for_insert['campaign_so'] = $campaign['so'];
                        var_dump(888);
                        $this->V2_email_campaign_additional_reporting_model->create($additional_data_for_update);
                    }



                foreach ($response['reports']['group_count_results'] as $report) {

                    $data_for_insert['date_created'] = $end_date;
                    $data_for_insert['link_id'] = $report['link_id'];
                    $data_for_insert['campaign_id'] = $campaign['id'];
                    $data_for_insert['campaign_io'] = $campaign['io'];
                    $data_for_insert['campaign_so'] = $campaign['so'];
                    $data_for_insert['counter'] = $report['counter'];
                    $data_for_insert['is_fulfilled'] = $report['is_fulfilled'];
                    $data_for_insert['destination_url'] = $report['dest_url'];
                    $data_for_insert['max_clicks'] = $report['max_clicks'];

                    if($existing_report) {
                        $data_for_insert['clicks_count'] = $report['group_count'] - $existing_report[$report['link_id']]['clicks_count'];

                    } else {
                        $data_for_insert['clicks_count'] = $report['group_count'];

                    }

                    if($data_for_insert['clicks_count']>0){
                        var_dump(999);
                        $this->V2_email_campaign_link_reporting_model->create($data_for_insert);
                    }

                }
            }
            //exit;
        }
        var_dump($response); exit;

    }

    public function make_campaign_live($id = null)    {
       // var_dump(8585);die;
        $this->load->model("V2_master_campaign_model");

        $pending_campaigns = $this->V2_master_campaign_model->make_campaign_live($id);

        echo "<pre>";
        var_dump($pending_campaigns);// exit;

        if($pending_campaigns){

            foreach($pending_campaigns as $campaign) {

                $updated = $this->V2_master_campaign_model->update($campaign['id'], array('campaign_is_converted_to_live' => 'P'));
            }

            foreach($pending_campaigns as $campaign) {

                // if user type is percentage then add percentage budget of campaign
                if($campaign['billing_type'] == 'PERCENTAGE') {
                    //$campaign['budget'] = $campaign['percentage_budget'];
                }

                /**
                 * SPECIAL case for THIRD PARTY campaigns ONLY
                 *
                 * Cause we don't need to send those campaign or ads
                 * to any network
                 */
                if ( $campaign['campaign_type'] == 'THIRD-PARTY-AD-TRACK' || $campaign['campaign_type'] == 'RICH_MEDIA_SURVEY') {
                    $this->Rtb_model->process_third_party_and_rich_media_campaign($campaign);
                } else {
                    $this->Common_model->create($campaign);

                    if($campaign['is_multiple']=='Y'){
                        $campaign['network_name'] = 'GOOGLE';
                        $this->Common_model->create($campaign);
                    }
                }
            }
        }
    }

    public function start_future_campaigns($io = null)    {

        $this->load->model("V2_master_campaign_model");

        $scheduled_campaigns = $this->V2_master_campaign_model->get_future_campaigns();
        //var_dump($scheduled_campaigns); exit;
        if($scheduled_campaigns){

            foreach($scheduled_campaigns as $campaign) {
                // if user type is percentage then add percentage budget of campaign
                $this->load->model('V2_log_model');
                $status = 'ACTIVE';
                $campaign['campaign_status'] = $status;
                $campaign['network_campaign_status'] = $status;
                $result = $this->Common_model->update_campaign_status($campaign);
                if ($result['message']) {

                    $this->V2_log_model->create($campaign['id'], 'Campaign status didnt updated by error', 'status');
                } else {
                    $updated = $this->V2_master_campaign_model->update($campaign['id'], array('network_campaign_status' => $status, 'campaign_status' => $status));
                    if($updated) {
                        $this->V2_log_model->create($campaign['id'], 'Campaign status updated successfully', 'status');
                    } else {
                        $this->V2_log_model->create($campaign['id'], 'Campaign status didnt updated in our db', 'status');
                    }
                }

                if($campaign['is_multiple']=='Y'){
//                    $campaign['network_name'] = 'GOOGLE';
//                    $this->Common_model->update_campaign_status($campaign);
                }
            }
        }
    }

    /*
     * TURNED OFF BY JKORKIN
     *
     *
    public function time_parting()    {

        $this->load->model("V2_master_campaign_model");
        $now = strtotime('now');
        $campaigns = $this->V2_master_campaign_model->get_campaigns_with_time_parting();

        if($campaigns){

            foreach($campaigns as $campaign) {

                $start_time = strtotime($campaign['start_time']);
                $end_time = strtotime($campaign['end_time']);

                if($now > $start_time && $now < $end_time) {
                    $status = 'ACTIVE';
                } else {
                    $status = 'PAUSED';
                }
                if($campaign['id']==1425) {
                    var_dump($campaign['time_parting_status'], $status, $now, $start_time, $end_time);
                }
                // if user type is percentage then add percentage budget of campaign
                $this->load->model('V2_log_model');
                if($campaign['time_parting_status'] != $status) {

                    $campaign['campaign_status'] = $status;
                    $campaign['network_campaign_status'] = $status;
                    $result = $this->Common_model->update_campaign_status($campaign);
                    if ($result['message']) {
                        $this->V2_log_model->create($campaign['id'], 'Campaign status didnt updated by error by time parting', 'status');
                    } else {
                        $updated = $this->V2_master_campaign_model->update($campaign['id'], array('time_parting_status' => $status));
                        if ($updated) {
                            $this->V2_log_model->create($campaign['id'], 'Campaign status updated successfully by time parting '.$status, 'status');
                        } else {
                            $this->V2_log_model->create($campaign['id'], 'Campaign status didnt updated in our db by time parting '.$status, 'status');
                        }
                    }

                    if ($campaign['is_multiple'] == 'Y') {
//                    $campaign['network_name'] = 'GOOGLE';
//                    $this->Common_model->update_campaign_status($campaign);
                    }
                }
            }
        }
    }
    */

    public function update_link_fulfilled_status()   {
        $this->Campclick_model->update_fulfilled_status();
    }

    /**
     * Controls the REAL TIME BIDDING functionality across all networks
     *
     * Add new networks here when we add them to the system
     */
    public function network_realtime_bid()  {
        $this->Finditquick_model->real_time_bid_adjustment();
    }

    public function check_ads_approved_status() {

        $this->load->model("V2_master_campaign_model");
        $network_names = $this->V2_master_campaign_model->get_active_campaigns_network_names_by_ad_status("UNCHECKED"); //var_dump($network_names); exit;

        if ($network_names) {
            foreach ($network_names as $network_name) {
                $this->Common_model->check_ads_approved_status($network_name['network_name']);
            }
        }
    }

    public function check_multiple_ads_approved_status() {

        $this->load->model("V2_multiple_ad_model");

        $pending_ads = $this->V2_multiple_ad_model->get_by_approval_status("UNCHECKED");

        if ($pending_ads) {
            // we use this function only for google adwords and get only google ads
            $network_name = $pending_ads[0]['network_name'];
            $this->Common_model->check_multiple_ads_approved_status($pending_ads, $network_name);
        }
    }

    public function get_reports(){

        die('dsada');
    }

    public function get_demographics_report() {

        $this->load->model("V2_master_campaign_model");

        // hard code for google

//        $campaigns = $this->V2_master_campaign_model->get_active_campaigns_by_network_id(1);
//        if($campaigns) {
//            $this->Common_model->get_demographics_report('GOOGLE');
//        }

        $network_names = $this->V2_master_campaign_model->get_active_campaigns_network_names(); //var_dump($network_names); exit;

        if ($network_names) {

            foreach ($network_names as $network_name) {

                if($network_name['network_name'] == 'GOOGLE' || $network_name['network_name'] == 'FACEBOOK' || $network_name['network_name'] == 'YAHOO') {

                    $this->Common_model->get_demographics_report($network_name['network_name']);

                }
            }
        }

    }

    public function get_placements_report() {

        $this->load->model("V2_master_campaign_model");
        // hard code for google

//        $campaigns = $this->V2_master_campaign_model->get_active_campaigns_by_network_id(1);
//        if($campaigns) {
//            $this->Common_model->get_placements_report('GOOGLE');
//        }

        $network_names = $this->V2_master_campaign_model->get_active_campaigns_network_names(); //var_dump($network_names); exit;
        if ($network_names) {

            foreach ($network_names as $network_name) {
                if($network_name['network_name'] == 'GOOGLE' || $network_name['network_name'] == 'FACEBOOK') {
                    $this->Common_model->get_placements_report($network_name['network_name']);
                }
            }
        }

    }

    public function get_campaigns_video_report() {

        $this->load->model("V2_master_campaign_model");
        // hard code for google
        $network_names = $this->V2_master_campaign_model->get_active_video_campaigns_network_names(); //var_dump($network_names); exit;
        if ($network_names) {

            foreach ($network_names as $network_name) {
                //if($network_name['network_name'] == 'GOOGLE' || $network_name['network_name'] == 'FACEBOOK') {
                    $this->Common_model->get_campaigns_video_report($network_name['network_name']);
                //}
            }
        }

    }

    public function get_campaigns_leads() {

        $this->load->model("V2_ad_model");
        // hard code for google
        $ads = $this->V2_ad_model->get_active_campaigns_ads_by_network_id_and_campaign_type(5, 'FB-LEAD');

        if ($ads) {
            $this->Common_model->get_campaigns_leads('FACEBOOK', $ads);
        }

    }

    public function get_ads_impressions() {

        $this->load->model("V2_master_campaign_model");

        $network_names = $this->V2_master_campaign_model->get_active_campaigns_network_names(); //var_dump($network_names); exit;

        if ($network_names) {
            foreach ($network_names as $network_name) {
                $this->Common_model->get_ads_impressions($network_name['network_name']);
            }
            // need to check impressions end criteria for all active campaigns
            $this->detect_impressions_end_criteria();
        }

    }

    public function get_multiple_ads_impressions() {

        $this->load->model("V2_multiple_campaign_model");

        $network_names = $this->V2_multiple_campaign_model->get_active_campaigns_network_names(); //var_dump($network_names); exit;

        if ($network_names) {

            foreach ($network_names as $network_name) {

                $this->Common_model->get_multiple_ads_impressions($network_name['network_name']);
            }
            // need to check impressions end criteria for all active campaigns
            //$this->detect_impressions_end_criteria();
        }

    }

    public function get_campaigns_cost() {

        $this->load->model("V2_master_campaign_model");

        $network_names = $this->V2_master_campaign_model->get_active_campaigns_network_names();
        // hardcode for google
        //$network_names = array('GOOGLE');
        if ($network_names) {

            foreach ($network_names as $network_name) {
                $this->Common_model->get_campaigns_cost($network_name['network_name']);
            }
            // need to check cost end criteria for all active campaigns
            $this->detect_cost_end_criteria();
        }
    }

    public function get_multiple_campaigns_cost() {

        $this->load->model("V2_multiple_campaign_model");

        $network_names = $this->V2_multiple_campaign_model->get_active_campaigns_network_names();
        // hardcode for google
        //$network_names = array('GOOGLE');
        if ($network_names) {

            foreach ($network_names as $network_name) {
                $this->Common_model->get_multiple_campaigns_cost($network_name['network_name']);
            }
            // need to check cost end criteria for all active campaigns
            //$this->detect_cost_end_criteria();
        }
    }

    public function detect_impressions_end_criteria() {

        $this->load->model("V2_master_campaign_model");
        $this->load->model("V2_users_model");
        $active_campaigns = $this->V2_master_campaign_model->get_active_campaigns_impressions_count();

        $this->load->library('Send_email');

        foreach($active_campaigns as $campaign) {
            if($campaign['is_thru_guarantee'] == 'Y'  && (!empty($campaign['max_impressions']) || !empty($campaign['total_clicks_count'])) && ($campaign['total_impressions_count'] >= $campaign['max_impressions'])){

                $total_clicks_count = $campaign['max_impressions'] * (int)$campaign['is_guarantee_percentage'] / 100 ;
                $campaign['max_impressions'] = $campaign['max_impressions'] * 2;

                if((int)$campaign['total_impressions_count'] >= $campaign['max_impressions'] || ( ( (int)$campaign['total_clicks_count'] >= $total_clicks_count ) )){

                     $updated = $this->V2_master_campaign_model->update($campaign['id'], array('network_campaign_status' => 'PAUSED', 'campaign_status' => 'COMPLETED'));

                    //check if campaign updated in db and make changes in network too
                    if ($updated) {
                        // save into log table
                        $this->load->model('V2_log_model');
                        $this->V2_log_model->create($campaign['id'], 'COMPLETED by impression end criteria', 'status');

                        $campaign['network_campaign_status'] = 'PAUSED';

                        $result = $this->Common_model->update_campaign_status($campaign);
                        if ($result['message']) {
                            $this->V2_log_model->create($campaign['id'], 'not campleted in live', 'status');
                        }
                        $user = $this->V2_users_model->get_by_id($campaign['userid']);
                        if($user['user_email_ability'] == 'Y' || $user['is_admin'] == 1 ){
                            $link_edit = 'http://reporting.prodata.media/v2/campaign/edit_campaign/'. $campaign['id'];
                            $this->send_email->send_completed($campaign['email'], $campaign['io'], $campaign['name'],$campaign['campaign_type'], $link_edit);
                        }
                    }

                }
            }else if( $campaign['total_impressions_count'] >= $campaign['max_impressions'] && ( ( !empty($campaign['max_clicks']) && $campaign['total_clicks_count'] >= $campaign['max_clicks'] ) || empty($campaign['max_clicks']) )) {

                // make campaign status paused and completed
                $updated = $this->V2_master_campaign_model->update($campaign['id'], array('network_campaign_status' => 'PAUSED', 'campaign_status' => 'COMPLETED'));

                //check if campaign updated in db and make changes in network too
                if ($updated) {
                    // save into log table
                    $this->load->model('V2_log_model');
                    $this->V2_log_model->create($campaign['id'], 'COMPLETED by impression end criteria', 'status');

                    $campaign['network_campaign_status'] = 'PAUSED';

                    $result = $this->Common_model->update_campaign_status($campaign);
                    if ($result['message']) {
                        $this->V2_log_model->create($campaign['id'], 'not campleted in live', 'status');
                    }
                    $user = $this->V2_users_model->get_by_id($campaign['userid']);
                    if($user['user_email_ability'] == 'Y' || $user['is_admin'] == 1 ){
                    $link_edit = 'http://reporting.prodata.media/v2/campaign/edit_campaign/'. $campaign['id'];
                    $this->send_email->send_completed($campaign['email'], $campaign['io'], $campaign['name'],$campaign['campaign_type'], $link_edit);
                    }
                }
            } else {

            }
        }
    }

    public function detect_cost_end_criteria() {
        /*
        $this->load->model("V2_master_campaign_model");
        $this->load->model("V2_users_model");
        $active_campaigns = $this->V2_master_campaign_model->get_active_campaigns_cost();
        $this->load->library('Send_email');


        foreach($active_campaigns as $campaign) {
            // need to clearyfy
            if ($campaign['network_name'] == 'FIQ' ){
                $campaign['total_cost'] = $this->V2_master_campaign_model->get_campaign_cost($campaign['id'], 'FIQ');
            }
            if($campaign['percentage_max_budget']){
                $campaign['max_budget'] = $campaign['percentage_max_budget'];
            }

            if($campaign['total_cost'] >= $campaign['max_budget']) {

                $campaign['network_campaign_status'] = 'PAUSED';
                $campaign['campaign_status'] = 'COMPLETED';
                // make campaign status paused and completed
                $updated = $this->V2_master_campaign_model->update($campaign['id'], array('network_campaign_status' => 'PAUSED', 'campaign_status' => 'COMPLETED'));

                //check if campaign updated in db and make changes in network too
                if ($updated) {

                    // save into log table
                    $this->load->model('V2_log_model');
                    $this->V2_log_model->create($campaign['id'], 'COMPLETED by max budget end criteria', 'status');

                    $result = $this->Common_model->update_campaign_status($campaign);
                    // send mail
                    if($campaign['is_billing'] == 'Y') {
                        $user = $this->V2_users_model->get_by_id($campaign['userid']);
                        if($user['user_email_ability'] == 'Y' || $user['is_admin'] == 1 ){
                            $link_edit = 'http://reporting.prodata.media/v2/campaign/edit_campaign/' . $campaign['id'];
                            $this->send_email->send_completed($campaign['email'], $campaign['io'], $campaign['name'], $campaign['campaign_type'], $link_edit);
                        }
                    }

                    if ($result['message']) {
                        $this->V2_log_model->create($campaign['id'], 'not campleted in live', 'status');
                    }
                }

            } else {

            }
        }
        */
    }

    public function detect_date_end_criteria() {

        $this->load->model("V2_master_campaign_model");
        $this->load->model("V2_users_model");
        $active_campaigns = $this->V2_master_campaign_model->get_ended_active_campaigns();
        $this->load->library('Send_email');

        foreach ($active_campaigns as $campaign) {

            // make campaign status paused and completed
            $updated = $this->V2_master_campaign_model->update($campaign['id'], array('network_campaign_status' => 'PAUSED', 'campaign_status' => 'COMPLETED'));
            //check if campaign updated in db and make changes in network too
            if ($updated) {
                // save into log table
                $this->load->model('V2_log_model');
                $this->V2_log_model->create($campaign['id'], 'COMPLETED by end date end criteria', 'status');

                $campaign['network_campaign_status'] = 'PAUSED';

                $result = $this->Common_model->update_campaign_status($campaign);

                if( empty($campaign['max_impressions']) && empty($campaign['max_clicks']) && empty($campaign['max_budget'])) {
                    $user = $this->V2_users_model->get_by_id($campaign['userid']);
                    if($user['user_email_ability'] == 'Y' || $user['is_admin'] == 1 ){
                        $link_edit = 'http://reporting.prodata.media/v2/campaign/edit_campaign/' . $campaign['id'];
                        $this->send_email->send_completed($campaign['email'], $campaign['io'], $campaign['name'], $campaign['campaign_type'], $link_edit);
                    }
                } else {
                    $link_edit = 'http://reporting.prodata.media/v2/campaign/extend_campaign_end_date/' . $campaign['id'];
                    if($campaign['can_extend_campaigns']=='N'){
                        $campaign['email'] = null;
                    }
                    $user = $this->V2_users_model->get_by_id($ad['userid']);
                    if($user['user_email_ability'] == 'Y' || $user['is_admin'] == 1 ){
                        $this->send_email->send_for_extend_end_date($campaign['email'], $campaign['io'], $campaign['name'], $campaign['campaign_type'], $link_edit);
                    }
                }

                if ($result['message']) {
                    $this->V2_log_model->create($campaign['id'], 'not campleted in live', 'status');
                }
            }
        }
    }

    public function delete_old_pdf() {

        $path = '../../../home/';
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                $fileTime = substr($file, 7, -3);
                if ((time()-$fileTime) > 86400) {
                    if (preg_match('/\.pdf$/i', $file)) {

                        unlink($path.$file);

                    }
                }
            }
        }


    }

    public function send_daily_report() {
        $this->load->library('Send_email');
        $this->load->model("V2_master_campaign_model");

        $active_campaigns = $this->V2_master_campaign_model->get_active_campaigns_by_time_range();
        $keys = array('Campaign Type', 'Campaign Name', 'Current Clicks', 'Current Impressions', 'Total Clicks', 'Total Impressions', 'Contracted impressions', 'Total cost');

        $path = 'v2/files/tmp/';
        $file_name = 'Daily_report.csv';
        $fp = fopen($path.$file_name, 'w');
        fputcsv($fp, $keys);

        foreach ($active_campaigns as $value) {
            foreach ($value as $item) {
                fputcsv($fp, $item);
            }
        }

        fclose($fp);
        $this->send_email->send_daily_report($path.$file_name);
    }

    public function get_domains_from_csv() {

        $file = "uploads/custom/DT_August_Final_List.csv";
        $row = 1;
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($domain = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($domain);
                $row++;
                for ($c=0; $c < $num; $c++) {
                    $this->clickcap->add_domain($domain[$c]);
                    echo $domain[$c] . "<br />\n";
                }
            }
            fclose($handle);
        }

    }

    public function get_ips_from_excel() {
        $file = 'uploads/custom/DCIP_August_Final_List.xlsx';

        try {
            $objPHPExcel = PHPExcel_IOFactory::load($file);
            $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();

            foreach ($cell_collection as $cell) {
                $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                $ip = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();

                //header will/should be in row 1 only. of course this can be modified to suit your need.
                if ($row != 1) {
                    $this->clickcap->add_ip($ip);
                    echo $ip;
                    echo "<br />\n";
                }
            }

        } catch (Exception $e) {
            die("Error loading file: ".$e->getMessage()."<br />\n");
        }
    }

    public function test() {

        $this->load->library('Send_email');

        $this->send_email->send('karine.hovhannisyan.bw@gmail.com', 'test', 'dasda');
        die('dasd');
        $this->load->model('V2_log_model');
        $this->V2_log_model->create(1, 'test cron', 'status');
    }

    public function run_yahoo_jobs() {
        $this->load->model('yahoo_model');
        $this->yahoo_model->get_job();
    }

    public function get_email_campaigns_pdfstatus_api() {

        //exit;
        $this->load->model("V2_master_campaign_model");
        $this->load->model("V2_users_model");

        $users = $this->V2_users_model->get_all_email_linked_users();

        if(!$users){
            echo 'No user'; exit;
        }

        $existing_campaigns = $this->V2_master_campaign_model->get_email_campaigns();
        //var_dump($users, $existing_campaigns); exit;
        foreach($existing_campaigns as $existing_campaign) {
            $existing_io[] = $existing_campaign['io'];
        }

        foreach($users as $user) {

            $ch = curl_init();
            $url = 'http://prodataverify.com/api/pdfstatus/iobyemail';

            $data = array(
                'email' => 'admin@admin.com',
            );
            var_dump($data);
            $headers = array("X-ProDataFeed-Auth: UHJvZGF0YUZlZWRTdGF0dXNDYW1wYWlnbnMgRm9yIEFQSSBSZXNwb25zZSBURVNUIEphc29uIEtvcmtpbg==");
            $options = array(
                CURLOPT_URL => $url,
                CURLOPT_HEADER => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => TRUE,
                CURLOPT_POSTFIELDS => $data
            );

            curl_setopt_array($ch, $options);

            $response = curl_exec($ch);
            //var_dump($response);

            if (FALSE === $response) {
                $curlErr = curl_error($ch);
                $curlErrNum = curl_errno($ch);

                curl_close($ch);
                throw new Exception($curlErr, $curlErrNum);
            }
            echo '<pre>';
            $info = curl_getinfo($ch);
            curl_close($ch);
            var_dump($response); continue;
            $response = json_decode($response, true);
            if($response['status'] == 'SUCCESS') {
                $this->load->model('V2_log_model');
                foreach ($response['campaigns'] as $campaign) {
                    if (!in_array($campaign['io'], $existing_io)) {
                        $campaign['campaign_type'] = 'EMAIL';
                        $campaign['network_campaign_status'] = 'ACTIVE';
                        $campaign['campaign_status'] = 'ACTIVE';
                        $campaign['name'] = $campaign['create_name'];
                        $campaign['userid'] = $user['id'];
                        unset($campaign['create_name']);
                        $id = $this->V2_master_campaign_model->create($campaign);
                        $campaign['id'] = $id;
                        $response_google = $this->Common_model->create_audience('GOOGLE',$campaign); var_dump($response_google);
                        // return snipped codes to report-site
                        $google_send_status = $this->send_audience_data_for_email_campaigns($response_google);
                        $response_facebook = $this->Common_model->create_audience('FACEBOOK',$campaign); var_dump($response_facebook);
                        if(!$google_send_status) {
                            $this->V2_log_model->create($campaign['id'], 'Campaign audience didnt send to report site '.$response_google['remarketing_list_id'], 'google_audience');
                        }
                        $fb_send_status = $this->send_audience_data_for_email_campaigns($response_facebook);
                        if(!$fb_send_status) {
                            $this->V2_log_model->create($campaign['id'], 'Campaign audience didnt send to report site '.$response_facebook['remarketing_list_id'], 'fb_audience');
                        }
//                        var_dump($response, $response1); exit;
                        //return snipped codes to report-site
                    } else {
                        echo 1;
                    }
                }
            } else {
                exit;
            }
        }
        var_dump($response); exit;

    }

    public function update_email_campaigns_status_pdfstatus_api() {

        $this->load->model("V2_master_campaign_model");

        $campaigns = $this->V2_master_campaign_model->get_email_campaigns();

        foreach($campaigns as $key=>$campaign) { echo $campaign['io'].'<br>';

            $ch = curl_init();
            $url = 'http://prodataverify.com/api/pdfstatus/campaignstatus/'.$campaign['io'].'.json';

            $headers = array("X-ProDataFeed-Auth: UHJvZGF0YUZlZWRTdGF0dXNDYW1wYWlnbnMgRm9yIEFQSSBSZXNwb25zZSBURVNUIEphc29uIEtvcmtpbg==");

            $options = array(
                CURLOPT_URL => $url,
                CURLOPT_HEADER => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => FALSE,
                //CURLOPT_POSTFIELDS => $data
            );

            curl_setopt_array($ch, $options);

            $response = curl_exec($ch);

            if (FALSE === $response) {
                $curlErr = curl_error($ch);
                $curlErrNum = curl_errno($ch);

                curl_close($ch);
                throw new Exception($curlErr, $curlErrNum);
            }
            echo '<pre>';

            curl_close($ch);
            var_dump($url,$response); //exit;
            $response = json_decode($response, true); var_dump($response);
            $statuses = ['ACTIVE'=>'ACTIVE', 'COMPLETE'=>'COMPLETED', 'PENDING'=>'SCHEDULED'];

            if($statuses[$response['status']] != $campaign['campaign_status']) {
                $data_for_update['campaign_status'] = $statuses[$response['status']];
                if($statuses[$response['status']] == 'ACTIVE') {
                    $data_for_update['network_campaign_status'] = $statuses[$response['status']];
                } else {
                    $data_for_update['network_campaign_status'] = 'PAUSED';
                }

                $this->V2_master_campaign_model->update($campaign['id'],$data_for_update);
            }

        }
        var_dump($response); exit;

    }

    public function send_leads_daily_report() {
        $this->load->model('V2_log_model');
        $this->V2_log_model->create(7777, 'Call cron', 'cron');
        $this->load->model("V2_master_campaign_model");
        $this->load->model("V2_users_model");
        $active_campaigns = $this->V2_master_campaign_model->get_active_campaigns_by_type_and_form_email_type('FB-LEAD', 'daily');
        $this->load->library('Send_email');
        echo '<pre>';
        var_dump( date('Y-m-d H:i'), $active_campaigns);// exit;
        if(!$active_campaigns) {
            return false;
        }
        foreach($active_campaigns as $campaign) {

            $start_date = date('Y-m-d 00:00:01');
            $end_date = date('Y-m-d 23:59:59'); //var_dump($end_date);
            $file_name = 'lead daily reporting between '.$start_date.' and '.$end_date.' '.$campaign['id'].'.csv';

            $this->load->model("V2_fb_lead_model");
            $leads = $this->V2_fb_lead_model->get_all_by_campaign_id_and_date($campaign['id'], $start_date, $end_date);

            if(!$leads) {
                continue;
            }

            $path = 'v2/files/tmp/';
            $fp = fopen($path.$file_name, 'w');

            foreach ($leads as $fields) {
                fputcsv($fp, $fields);
            }

            fclose($fp);
            $user = $this->V2_users_model->get_by_id($campaign['userid']);
            if($user['user_email_ability'] == 'Y' || $user['is_admin'] == 1 ){
                $this->send_email->send_lead_reporting($campaign['email'], $campaign['io'], $campaign['campaign_name'], 'DAILY', $path.$file_name);
            }
        }
    }

    public function detect_likes_end_criteria() {

        $this->load->model("V2_master_campaign_model");
        $this->load->model("V2_users_model");

        $active_campaigns = $this->V2_master_campaign_model->get_active_campaigns_likes_count_with_impressions();
        foreach($active_campaigns as $campaign) {

            if($campaign['total_likes_count'] >= $campaign['max_clicks'] && (!empty($campaign['max_impressions']) && $campaign['total_impressions_count'] >= $campaign['max_impressions']) || empty($campaign['max_impressions'])){

                // make campaign status paused and completed
                $updated = $this->V2_master_campaign_model->update($campaign['id'], array('network_campaign_status' => 'PAUSED', 'campaign_status' => 'COMPLETED'));

                //check if campaign updated in db and make changes in network too
                if ($updated) {
                    // save into log table
                    $this->load->model('V2_log_model');
                    $this->V2_log_model->create($campaign['id'], 'COMPLETED by likes end criteria', 'status');

                    $campaign['network_campaign_status'] = 'PAUSED';

                    $result = $this->Common_model->update_campaign_status($campaign);
                    if ($result['message']) {
                        $this->V2_log_model->create($campaign['id'], 'not campleted in live', 'status');
                    }
                    $user = $this->V2_users_model->get_by_id($campaign['userid']);
                    if($user['user_email_ability'] == 'Y' || $user['is_admin'] == 1 ){
                        $link_edit = 'http://reporting.prodata.media/v2/campaign/edit_campaign/'. $campaign['id'];
                        $this->send_email->send_completed($campaign['email'], $campaign['io'], $campaign['name'],$campaign['campaign_type'], $link_edit);
                    }
                }
            } else {

            }

        }
    }

    public function detect_liads_end_criteria() {

        $this->load->model("V2_master_campaign_model");
        $this->load->model("V2_users_model");

        $active_campaigns = $this->V2_master_campaign_model->get_active_campaigns_leads_count_with_impressions(); //var_dump($active_campaigns); exit;
        foreach($active_campaigns as $campaign) {

            if($campaign['total_leads_count'] >= $campaign['max_clicks'] && (!empty($campaign['max_impressions']) && $campaign['total_impressions_count'] >= $campaign['max_impressions']) || empty($campaign['max_impressions'])){

                // make campaign status paused and completed
                $updated = $this->V2_master_campaign_model->update($campaign['id'], array('network_campaign_status' => 'PAUSED', 'campaign_status' => 'COMPLETED'));

                //check if campaign updated in db and make changes in network too
                if ($updated) {
                    // save into log table
                    $this->load->model('V2_log_model');
                    $this->V2_log_model->create($campaign['id'], 'COMPLETED by Leads end criteria', 'status');

                    $campaign['network_campaign_status'] = 'PAUSED';

                    $result = $this->Common_model->update_campaign_status($campaign);
                    if ($result['message']) {
                        $this->V2_log_model->create($campaign['id'], 'not campleted in live', 'status');
                    }
                    $user = $this->V2_users_model->get_by_id($campaign['userid']);
                    if($user['user_email_ability'] == 'Y' || $user['is_admin'] == 1 ){
                        $link_edit = 'http://reporting.prodata.media/v2/campaign/edit_campaign/'. $campaign['id'];
                        $this->send_email->send_completed($campaign['email'], $campaign['io'], $campaign['name'],$campaign['campaign_type'], $link_edit);
                    }
                }
            } else {

            }

        }
    }

    public function trkreport_io_compare_change_status_deployed(){
        $this->load->model("V2_master_campaign_model");
        $this->load->model("Trkreport_model");
        $trkCampaigns = $this->Trkreport_model->get_campaign_not_deployed();
        $campaigns = $this->V2_master_campaign_model->get_all_campaigns();
        $ios = array_column($campaigns, 'io');

        foreach ($trkCampaigns as $trkCampaign){
            if (in_array($trkCampaign['io'], $ios)){
                $this->Trkreport_model->campaign_update_io($trkCampaign['id']);
            }
        }

    }

    public function generate_reporting() {

        $this->load->library('Wkhtmltopdf');
        $this->load->model('V2_log_model');

        $campaigns = $this->V2_master_campaign_model->get_active_email_campaigns();
        echo '<pre>';
        foreach ($campaigns as $campaign) {
            var_dump(date('Y-m-d H-i-s',strtotime('now')),$campaign['campaign_start_datetime'],$campaign['id']);
            $now = strtotime('now');
            $start_time = strtotime($campaign['campaign_start_datetime']);
            $end_time_24 = strtotime('+1 day', $start_time);
            $end_time_48 = strtotime('+2 days', $start_time);
            $end_time_96 = strtotime('+4 days', $start_time);

//            $now1 = date('Y-m-d H:i:s', $now);
//            $start_time1 = date('Y-m-d H:i:s', strtotime($campaign['campaign_start_datetime']));
//            $end_time1 = date('Y-m-d H:i:s', strtotime($campaign['campaign_start_datetime'].' +2 days'));
//            var_dump($now1,$start_time1,$end_time1); exit;

            $name = $campaign['name'].' '.$campaign['io'];
            $name = str_replace('/','',$name);
            $path = '/var/www/html/v2/pdf/'.$name;
            $url = "http://reporting.prodata.media/v2/campaign/email_reporting_for_pdf/";

            if($end_time_24 < $now && !file_exists($path.' for 24H.pdf')) {
                //create pdf for 24H

                var_dump($campaign['id'], 'report for 24');
                $this->wkhtmltopdf->__set('url', $url.$campaign['id']);
                $this->wkhtmltopdf->__set('mode', 'MODE_SAVE');
                $this->wkhtmltopdf->__set('path', $path.' for 24H.pdf');
                $this->wkhtmltopdf->__set('title', 'ProData Media Campaign Report');

                $result = $this->wkhtmltopdf->downloadPDF();

                if (!$result['status']) { var_dump($result['message']);
                    $this->V2_log_model->create($campaign['id'], 'Can not create pdf for 24H '.$result['message'], 'pdf');
                }
            }

            if($end_time_48 < $now  && !file_exists($path.' for 48H.pdf')) {
                //create pdf for 48H
                var_dump($campaign['id'], 'report for 48');
                $this->wkhtmltopdf->__set('url', $url.$campaign['id']);
                $this->wkhtmltopdf->__set('mode', 'MODE_SAVE');
                $this->wkhtmltopdf->__set('path', $path.' for 48H.pdf');
                $this->wkhtmltopdf->__set('title', 'ProData Media Campaign Report');

                $result = $this->wkhtmltopdf->downloadPDF();

                if (!$result['status']) { var_dump($result['message']);
                    $this->V2_log_model->create($campaign['id'], 'Can not create pdf for 48H '.$result['message'], 'pdf');
                }
            }

            if($end_time_96 < $now  && !file_exists($path.' for 96H.pdf') ) {
                //create pdf for 96H
                var_dump($campaign['id'], 'report for 96');
                $this->wkhtmltopdf->__set('url', $url.$campaign['id']);
                $this->wkhtmltopdf->__set('mode', 'MODE_SAVE');
                $this->wkhtmltopdf->__set('path', $path.' for 96H.pdf');
                $this->wkhtmltopdf->__set('title', 'ProData Media Campaign Report');

                $result = $this->wkhtmltopdf->downloadPDF();

                if (!$result['status']) {
                    var_dump($result['message']);
                    $this->V2_log_model->create($campaign['id'], 'Can not create pdf for 96H '.$result['message'], 'pdf');
                }
            }
        }
    }

    public function add_campaign_rtb_cost() {

        $this->load->model("V2_master_campaign_model");
        $this->load->model('V2_campaign_cost_model');
//        $last_15_mins = date('Y-m-d H:i:s', strtotime('-15 mins', strtotime('now')));
        $active_campaigns = $this->V2_master_campaign_model->get_active_campaigns_impressions_cost();
        echo '<pre>';
        //var_dump($active_campaigns); //exit;
        if($active_campaigns) {
            foreach ($active_campaigns as $campaign) {
                if (!$campaign['total_cost']) {
                    $campaign['total_cost'] = 0;
                }

                print "-----------------------------------\n";
                print_r($campaign);
                print "CAMPAIGN COST TODAY: " . $this->V2_campaign_cost_model->get_todays_campaign_spend($campaign['id']) . "\n";
                print "-----------------------------------\n";

                //$new_cost = $campaign['total_impressions_cost']/1000 + $campaign['total_cost'];
                $new_cost = $campaign['total_impressions_cost'] + $campaign['total_cost'];

                $data_for_update = array();
                $data_for_update['network_id'] = 7;
                $data_for_update['campaign_id'] = $campaign['id'];
                $data_for_update['cost'] = $new_cost;
                $data_for_update['type'] = 'RTB';
                if ($data_for_update['cost']) {
                    $this->V2_campaign_cost_model->create($data_for_update);
                }
            }

            $this->detect_cost_end_criteria();
        }
        exit;
    }

    /**
     *
     * Campaign Pacing Spend Functions (Display / Display-Retargeting / Third-Party-Ad-Tracking Only)
     * Version 2 - Improved
     *
     */
    public function campaign_pacing_by_budget_V2() {
        print "<pre>";
        print "START TIME: " . date("Y-m-d H:i:s") . "\n";

        $this->load->model("V2_master_campaign_model");
        $this->load->model('V2_campaign_cost_model');
        $this->load->model('V2_network_model');
        $this->load->model('V2_users_model');

        $active_campaigns = $this->V2_master_campaign_model->get_active_campaigns_impressions_cost();

        $pretargeting = [
            'states' => [],
            'zips' => [],
            'dims' => [],
            'whs' => []
        ];
        foreach($active_campaigns as $c) {
            $__CAMPAIGN_ACTIVE = false;

            // If the campaign is already marked as complete, skip it!
            if (strtoupper($c['campaign_status']) == "COMPLETE") {
                continue;
            }

            $spend = $this->V2_campaign_cost_model->get_hourly_spend_by_campaign($c['id']);

            print_r($c);

            //if ($c['campaign_status'] == "ACTIVE" && $spend['accumulated_spend'] < $spend['max_spend_for_time']) {
            if ($c['campaign_status'] == "ACTIVE" && (($spend['accumulated_spend'] < $spend['max_spend_for_time']) || ($spend['accumulated_spend'] == 0))) {
            //if ($c['campaign_status'] == "ACTIVE") {
                // add to redis
                print "ACTIVE: " . $c['io'] . "\t" . $c['name'] . "\n";
                $redis_result = $this->Common_model->add_campaign_and_assoc_ads_to_redis($c['id'], $pretargeting);

                // overwrite pretargeting
                if ( !empty($redis_result['pretargeting']) ) {
                    $pretargeting = $redis_result['pretargeting'];
                }
            } else {
                // remove from redis
                print "REMOVE: " . $c['io'] . "\t" . $c['name'] . "\n";
                $redis_result = $this->Common_model->remove_campaign_and_assoc_ads_from_redis($c['id']);
            }

            print_r($spend);
        }

        /**
         * Update Pretargeting Config
         */
        if ( !empty($pretargeting) && !empty($pretargeting['dims']) ) {
            $this->load->library('google_adx');
            $pretargeting = $this->prepare_pretargeting_data($pretargeting);
            $res = $this->google_adx->patch_pretargeting_config($config_id = 10664, $pretargeting);
        }

        print "END TIME: " . date("Y-m-d H:i:s") . "\n";
    }

    /**
     *
     * CAMPAIGN COMPLETION FUNCTION BY IMPRESSION (DISPLAY/DISPLAY-RETARGETING ONLY)
     *
     */
    public function campaign_completion_by_impression() {
        print "<pre>";

        $this->load->model("V2_master_campaign_model");
        $this->load->model('V2_campaign_cost_model');
        $this->load->model('V2_network_model');
        $this->load->model('V2_users_model');

        $active_campaigns = $this->V2_master_campaign_model->get_active_campaigns_impressions_cost();

        if ($active_campaigns)  {
            foreach($active_campaigns as $c) {
                $_CAMPAIGN_ACTIVE = false;
                $_CAMPAIGN_IMPRESSION_MAXED = false;

                if ($c['campaign_status'] == "COMPLETE") {
                    print "COMPLETE: {$c['id']} - {$c['name']}\n";
                    continue;
                }

                print_r($c);
                // do stuff here
            }
        }
    }

    /**
     * CAMPAIGN COMPLETION FUNCTION BY BUDGET (DISPLAY / RETARGETING ONLY)
     *
     */
    public function campaign_completion_by_budget_V2()  {
        $this->load->model("V2_master_campaign_model");
        $this->load->model('V2_campaign_cost_model');

        $active_campaigns = $this->V2_master_campaign_model->get_active_campaigns_impressions_cost();

        print_r($active_campaigns);

        foreach($active_campaigns as $c) {
            if ($c['total_cost'] >= $c['max_budget']) {
                // campaign is budget campaign. time to stop!

                $updated = $this->V2_master_campaign_model->update($c['id'], array('network_campaign_status' => 'PAUSED', 'campaign_status' => 'COMPLETED'));

                if ($updated)   {
                    $this->load->model("V2_log_model");
                    $this->V2_log_model->create($c['id'], 'COMPLETED by budget end criteria', 'status');

                    $c['network_campaign_status'] = 'PAUSED';
                    $result = $this->Common_model->update_campaign_status($c);

                    $redis_result = $this->Common_model->remove_campaign_and_assoc_ads_from_redis($c['id']);

                    $user = $this->V2_users_model->get_by_id($c['userid']);
                    if($user['user_email_ability'] == 'Y' || $user['is_admin'] == 1 )   {
                        $link_edit = 'http://reporting.prodata.media/v2/campaign/edit_campaign/'. $c['id'];
                        $this->send_email->send_completed($c['email'], $c['io'], $c['name'],$c['campaign_type'], $link_edit);
                    }
                }
            }
        }

    }


    /**
     * Run this DAILY at 12:01AM to reset our spend budgets!
     */
    public function clear_daily_spend() {
        $this->clickcap->clear_daily_spend();
    }

    /**
     * Clean-up DB#13 Impression count and rest
     * the key `creativesImpCounter`
     * @return void
     */
    public function reset_daily_impression_count()
    {
        $this->clickcap->reset_daily_impression_count();
    }

    /**
     * Run this ONLY if Redis and DB get our of sync for daily spend budgets!
     */
    public function reset_daily_spend() {
        $this->load->model("V2_master_campaign_model");
        $this->load->model("V2_campaign_cost_model");

        $active_campaigns = $this->V2_master_campaign_model->get_active_campaigns_impressions_cost();

        foreach($active_campaigns as $c) {
            $c['todays_spend'] = $this->V2_campaign_cost_model->get_todays_campaign_spend($c['id']);
            $this->clickcap->reset_daily_spend("CAMPAIGN_{$c['id']}", $c['todays_spend']);

            print "CAMPAIGN_{$c['id']} --> {$c['todays_spend']}\n";
        }
    }

}
