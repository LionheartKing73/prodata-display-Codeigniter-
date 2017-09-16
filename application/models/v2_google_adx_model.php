<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class v2_google_adx_model extends CI_Model {

    protected $CI;

    public function __construct() {
        parent::__construct();
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->load->library('google_adx');
        $this->load->model('v2_ads_disapproval_model');
    }

    /**
     * Insert New Creative to Google AdX
     *
     * @param  array  $data               Ad Information
     * @return array  $creative_response  Response for AdX
     */
    public function insert_creative(array $data, $create_disapproval_entry = true)
    {
        $ad_id = !empty($data['ad']['id'])
                    ? $data['ad']['id']
                    : (!empty($data['ad']['new_ad_id'])
                        ? $data['ad']['new_ad_id']
                        : null);

        if ( empty($ad_id)
            || empty($data['campaign']['io'])
            || empty($data['ad']['creative_width'])
            || empty($data['ad']['creative_height'])
            || empty($data['ad']['destination_url']) )
            return;

        $buyer_creative_id = 'ProData-' . $data['campaign']['io'] . '-' . $ad_id;

        // rewrite ad's destination URL to our reporting site
        // if ( !empty($data['campaign']['destination_url']) ) {
        //     $data['ad']['destination_url'] = $data['campaign']['destination_url'];
        // }

        $html_snippet = '<iframe src="https://reporting.prodata.media/tracking/ad_iframe_view/'
        . $ad_id .'?redir=%%CLICK_URL_ESC_ESC%%&ord=%%CACHEBUSTER%%" scrolling=\'no\' marginheight="0" marginwidth="0" frameborder="0" width="'
        . $data['ad']['creative_width'] . '" height="'. $data['ad']['creative_height'] . '" > </iframe>';

        /*$html_snippet = '<html><body><a href="%%CLICK_URL_UNESC%%'.$data['ad']['destination_url'].'">'
        . $data['campaign']['io'] .'</a></body></html>';*/

        $new_creative = [
            'buyer_creative_id' => $buyer_creative_id,
            'advertiser_name' => 'google',
            'width' => $data['ad']['creative_width'],
            'height' => $data['ad']['creative_height'],
            'html_snippet' => $html_snippet,
            'click_through_urls' => [
                $data['ad']['destination_url']
            ]
        ];

        $creative_response = $this->google_adx->insert_creative($new_creative);

        // make DB entry
        if ( $creative_response['status'] == 'SUCCESS' && $create_disapproval_entry === true ) {
            $this->v2_ads_disapproval_model->create([
                'io' => $data['campaign']['io'],
                'ad_id' => $ad_id,
                'status' => 'PENDING',
                'network' => $data['campaign']['network_name']
            ]);
        }

        return $creative_response;
    }
}
?>
