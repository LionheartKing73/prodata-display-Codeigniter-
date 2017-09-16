<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tracking extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */

    private $FORENSIQ_COST = 0.00058; // this is to offset the cost for using Forensiq -- was 0.00078 until 07/05
    private $OVERHEAD_COST = 0.00035; // this is to offset the cost for servers, office space, etc -- was 0.00050 until 07/05

    function __construct(){
        parent::__construct();
        $this->load->model('v2_ad_model');
        $this->load->model('V2_campclick_impression_model');
        $this->load->model("V2_prodata_id_retargeting_model");
        $this->load->library("user_agent");
        $this->load->library("Clickcap");
    }

    private $networks = [
        'FIQ' => '2',
        'GOOGLE' => '1',
        'BING' => '3',
        'AIRPUSH' => '4',
        'FACEBOOK' => '5',
        'YAHOO' => '6',
        'RTB' => '7',
    ];

    private $conversionTrackingCookieName = "ProDataMediaConversionTracker"; // this is our cookie name for conversion tracking

    public function redirect($id = null, $link_id = null, $network_id = null){
        $this->load->model('V2_log_model');
        $this->load->model('Userlist_vertical_model');
        $this->load->model('Userlist_io_model');
        //$this->input->get();
        //$te = json_encode($this->input->get());
        //$this->V2_log_model->create($id, 'click for link '.$link_id, 'click');
        if (!$id){
            $this->V2_log_model->create($id, ''.$link_id.' NO ID', 'click');
            throw new exception("Id required");
        }

        if (!$link_id){
            throw new exception("Link required");
        }

        $this->load->model('V2_campclick_click_model');
        $this->load->model('V2_campclick_impression_model');
        $this->load->model('V2_ads_link_model');
        $this->load->model('V2_master_campaign_model');

        $this->load->library(["user_agent", 'Fraudfiltering', 'Geolookup', 'Forensiq']);
        $this->load->helper('url');
        $this->load->helper('cookie');

        $link = $this->V2_ads_link_model->get_by_id($link_id);

        $campaign = $this->V2_master_campaign_model->get_by_id(false, $id);

        if (!$campaign){
            $this->V2_log_model->create($id, ''.$link_id.' NO CAMPAIGN' , 'click');
            return false;
        }

        if ($campaign['max_clicks']){

            $count_campaign_clicks = $this->V2_campclick_click_model->get_campaign_click_count($id) + 1;

            if ($count_campaign_clicks >= $campaign['max_clicks']){

                if($campaign['max_impressions']) {
                    $count_campaign_impressions = $this->V2_campclick_impression_model->get_campaign_impressions_count($id);

                    if ($count_campaign_impressions >= $campaign['max_impressions']) {
                        $updated = $this->V2_master_campaign_model->update($id, array('network_campaign_status' => 'PAUSED', 'campaign_status' => 'COMPLETED'));

                        if ($updated) {
                            $campaign['network_campaign_status'] = 'PAUSED';
                            $this->load->model('Common_model');
                            $this->Common_model->update_campaign_status($campaign);
                            $this->V2_log_model->create($id, 'COMPLETED', 'status');
                        }
                    }
                } else {
                    $updated = $this->V2_master_campaign_model->update($id, array('network_campaign_status' => 'PAUSED', 'campaign_status' => 'COMPLETED'));

                    if ($updated) {
                        $campaign['network_campaign_status'] = 'PAUSED';
                        $this->load->model('Common_model');
                        $this->Common_model->update_campaign_status($campaign);
                        $this->V2_log_model->create($id, 'COMPLETED', 'status');
                    }
                }
            }
        }

        $referrer = $this->agent->referrer();
        $p = parse_url($referrer);
        $host = (isset($p['host'])) ? $p['host'] : '';
        //$this->V2_log_model->create($id, 'click for link '.$link_id.' host'. $host , 'click');

        //need to fix
        $this->fraudfiltering->ipaddress = $this->input->ip_address();
        //$this->fraudfiltering->io = $io;
        $this->fraudfiltering->io = $link_id;
        $is_fraud = $this->fraudfiltering->checkFraud();

        //$this->V2_log_model->create($id, 'click for link '.$link_id.' is_fraud'. $is_fraud , 'click');

        $ip_address = $this->input->ip_address();

        $this->geolookup->ip_address = $ip_address;

        $geo = $this->geolookup->lookup();

        //$this->V2_log_model->create($id, 'click for link '.$link_id.' geolookup'. $geo , 'click');

        $this->forensiq->campaign_id = $id;
        $this->forensiq->user_agent = $this->input->user_agent();
        $this->forensiq->referral_url = $referrer;
        $this->forensiq->ip_address = $ip_address;
        $this->forensiq->network = "PRODATA";
        $this->forensiq->subsource = "DISPLAY_CLICK";
        $this->forensiq->io = $id;
        $this->forensiq->session_id = uniqid("PDM", true);
        $fraud_score = $this->forensiq->analyze_ip();

        $insert_array = array(
            "link_id" => $link_id,
            'campaign_id' => $id,
            'ad_id' => $link['ad_id'],
            "ip_address" => $ip_address,
            "user_agent" => $this->input->user_agent(),
            "timestamp" => date("Y-m-d H:i:s"),
            "is_mobile" => ($this->agent->is_mobile()) ? "Y" : "N",
            "web_browser" => $this->agent->browser(),
            "mobile_device" => $this->agent->mobile(),
            "platform" => $this->agent->platform(),
            "referrer" => $referrer,
            "referrer_host" => $host,
            'is_fraud' => ($is_fraud === true) ? "Y" : "N",
            'network_id' => $network_id ? $network_id : $campaign['network_id'],
            'fraud_score' => $fraud_score
        );
        if($geo) {
            $insert_array['geo_lat'] = $geo['lat'];
            $insert_array['geo_lon'] = $geo['lng'];
            $insert_array['country'] = $geo['country'];
            $insert_array['state'] = $geo['state'];
            $insert_array['city'] = $geo['city'];
            $insert_array['postal_code'] = $geo['postal_code'];
        } else {
            //$this->V2_log_model->create($id, ''.$link_id.' GEO IS EMPTY' , 'click');
        }

//        $json = json_encode($insert_array);
        //$this->V2_log_model->create($id, 'click for link '.$link_id.' and insert array is'. $json , 'click');
        $this->V2_campclick_click_model->create($insert_array);
        //$this->V2_log_model->create($id, 'click for link '.$link_id.' finish' , 'click');

        /*$this->load->model('V2_campclick_conversion_model');
        $this->V2_campclick_conversion_model->test();
        exit;*/

        $cookie = array(
            "name" => "trafficPingTracker",
            "value" => $id,
            "expire" => 1825*86400,
            "domain" => ".reporting.prodata.media",
            "path" => "/",
        );

        $arrIoList = $this->Userlist_io_model->get_userlist_by_campaign_id($id);
        $arrVerticalList = $this->Userlist_vertical_model->get_userlist_from_vertical($campaign['vertical']);

        $arrVerticalList = isset($arrVerticalList[0])?$arrVerticalList[0]:array();
        $arrIoList = isset($arrIoList[0])?$arrIoList[0]:array();

        $verticalScriptTag = html_entity_decode($arrVerticalList['sniped_code']);
        $ioScriptTag = html_entity_decode($arrIoList['sniped_code']);
        // end adwords user mapping code

        //set_cookie($cookie);

        header("Referer: http://reporting.prodata.media/c2/{$id}/{$link_id}");
        //header("Referer: {$link['destination_url']}");


        $javascriptGA = "
                        <script>
                          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

                          ga('create', 'UA-54038238-1', 'auto');
                          ga('send', 'pageview');

                        </script>
                        ";

        $redirectTime = 0;

        /*
         * Below we will setup our cookie (set cookie AND store cookie to DB), used for conversion tracking.
         *
         */
        $this->conversion_cookie($id);

        $snip = "<html><head><meta http-equiv='refresh' content=\"{$redirectTime};URL='{$link['destination_url']}'\">{$javascriptGA}</head><body><div style=\"height: 100%;background: url(/v2/images/load.gif) no-repeat;background-position: center;\"></div>
        {$ioScriptTag}{$verticalScriptTag}</body></html>";
        //$this->V2_log_model->create($id, 'sniped '.$link_id.' is '.$snip , 'snip');
        //print $snip;
        header("Location: {$link['destination_url']}");
        exit;

    }

    public function retarget($campaign_id = 0) {
        /*if ($campaign_id != "") {
            print json_encode(array("status" => "ERROR", "message" => "Campaign ID Required"));
            exit;
        }*/

        $this->conversion_cookie($campaign_id);

        print json_encode(array("status" => "SUCCESS"));
        exit;
    }

    private function conversion_cookie($campaign_id = "")    {

        // look to see if we've already set a cookie for this user before (uuid + campaign id must match this campaign ID)
        $cookie = $this->input->cookie('ProDataMediaConversionTracker', true);

        if ($cookie !== false) {
            list($uuid, $campaign_id) = explode("--", $cookie);
            $cookie_status = true;
        } else {
            $uuid = uniqid("PDM", true);

            $cookie = array(
                "name" => $this->conversionTrackingCookieName,
                "value" => $uuid . "--" . (int)$campaign_id, // guid goes here
                "expire" => (86400 * 365), // one year out
                "domain" => ".prodata.media",
                "path" => "/"
            );

            $this->input->set_cookie($cookie);
            $cookie_status = false;
        }


        $this->load->model("v2_conversion_model");
        $this->v2_conversion_model->guid = $uuid;
        $this->v2_conversion_model->campaign_id = $campaign_id;
        $this->v2_conversion_model->ip_address = $this->input->ip_address();
        $this->v2_conversion_model->store_cookie($cookie_status);

        // store the prodata_id info
        $user_agent = $this->input->user_agent();
        $user_ip = $this->input->ip_address();
        $args = escapeshellarg($user_ip) . " " . escapeshellarg($user_agent) . " " . "cc4c39422669789b2169e4b8cc1fb3d822cb7c2b105843913a3cc0e606002b87";
        $script_path = "/var/www/html/application/cityhashtest.py ";
        $command = '/usr/bin/python3 ' . $script_path . $args;
        //$command = escapeshellcmd('/usr/bin/python3 ' . $script_path . $args);
        try {
            $prodata_id = shell_exec($command);
            $prodata_id = trim($prodata_id);
            if ( !$prodata_id || !preg_match('/^\d+$/', $prodata_id) ) return;
            $this->V2_prodata_id_retargeting_model->prodata_id = $prodata_id;
            $this->V2_prodata_id_retargeting_model->campaign_id = $campaign_id;
            $this->V2_prodata_id_retargeting_model->save();
        } catch (Exception $e) {}

    }

    public function second_redirect($id = null, $link_id = null, $network_id = null)
    {
        $this->load->model('V2_log_model');
        $this->load->model('Userlist_vertical_model');
        $this->load->model('Userlist_io_model');
        $this->V2_log_model->create(1, 'second click', 'redir');
//        $url_encode = urlencode('https://www.facebook.com/islandrootstuart/?utm_source=prodata&utm_medium=display&utm_campaign=december');
//        $url_d_encode = urlencode($url_encode);
//        $destination = 'http://adclick.g.doubleclick.net/aclk?sa=l&ai=C8qeK&adurl='.$url_d_encode;
        header("Location: {$this->input->get('final_url')} "); exit;
        var_dump($this->input->get('redir'));
        exit;

    }

    public function redirect_with_macros($id = null, $link_id = null, $network_id = null){
        $this->load->model('V2_log_model');
        $this->load->model('Userlist_vertical_model');
        $this->load->model('Userlist_io_model');
        $this->V2_log_model->create($id, 'first click '.$link_id.' '.$this->input->get('redir'), 'redir');

        if (!$id){
            $this->V2_log_model->create($id, ''.$link_id.' NO ID', 'click');
            throw new exception("Id required");
        }

        if (!$link_id){
            throw new exception("Link required");
        }

        $this->load->model('V2_campclick_click_model');
        $this->load->model('V2_campclick_impression_model');
        $this->load->model('V2_ads_link_model');
        $this->load->model('V2_master_campaign_model');

        $this->load->library(["user_agent", 'Fraudfiltering', 'Geolookup', 'Forensiq']);
        $this->load->helper('url');
        $this->load->helper('cookie');

        $link = $this->V2_ads_link_model->get_by_id($link_id);

        $campaign = $this->V2_master_campaign_model->get_by_id(false, $id);

        if (!$campaign){
            $this->V2_log_model->create($id, ''.$link_id.' NO CAMPAIGN' , 'click');
            return false;
        }

        if ($campaign['max_clicks']){

            $count_campaign_clicks = $this->V2_campclick_click_model->get_campaign_click_count($id) + 1;

            if ($count_campaign_clicks >= $campaign['max_clicks']){

                if($campaign['max_impressions']) {
                    $count_campaign_impressions = $this->V2_campclick_impression_model->get_campaign_impressions_count($id);

                    if ($count_campaign_impressions >= $campaign['max_impressions']) {
                        $updated = $this->V2_master_campaign_model->update($id, array('network_campaign_status' => 'PAUSED', 'campaign_status' => 'COMPLETED'));

                        if ($updated) {
                            $campaign['network_campaign_status'] = 'PAUSED';
                            $this->load->model('Common_model');
                            $this->Common_model->update_campaign_status($campaign);
                            $this->V2_log_model->create($id, 'COMPLETED', 'status');
                        }
                    }
                } else {
                    $updated = $this->V2_master_campaign_model->update($id, array('network_campaign_status' => 'PAUSED', 'campaign_status' => 'COMPLETED'));

                    if ($updated) {
                        $campaign['network_campaign_status'] = 'PAUSED';
                        $this->load->model('Common_model');
                        $this->Common_model->update_campaign_status($campaign);
                        $this->V2_log_model->create($id, 'COMPLETED', 'status');
                    }
                }
            }
        }

        $referrer = $this->agent->referrer();
        $p = parse_url($referrer);
        $host = (isset($p['host'])) ? $p['host'] : '';
        //$this->V2_log_model->create($id, 'click for link '.$link_id.' host'. $host , 'click');

        //need to fix
        $this->fraudfiltering->ipaddress = $this->input->ip_address();
        //$this->fraudfiltering->io = $io;
        $this->fraudfiltering->io = $link_id;
        $is_fraud = $this->fraudfiltering->checkFraud();

        //$this->V2_log_model->create($id, 'click for link '.$link_id.' is_fraud'. $is_fraud , 'click');

        $ip_address = $this->input->ip_address();

        $this->geolookup->ip_address = $ip_address;

        $geo = $this->geolookup->lookup();

        $this->forensiq->campaign_id = $id;
        $this->forensiq->user_agent = $this->input->user_agent();
        $this->forensiq->referral_url = $referrer;
        $this->forensiq->ip_address = $ip_address;
        $this->forensiq->network = "PRODATA";
        $this->forensiq->subsource = "DISPLAY_CLICK";
        $this->forensiq->io = $id;
        $this->forensiq->session_id = uniqid("PDM", true);
        $fraud_score = $this->forensiq->analyze_ip();

        //$this->V2_log_model->create($id, 'click for link '.$link_id.' geolookup'. $geo , 'click');

        $insert_array = array(
            "link_id" => $link_id,
            'campaign_id' => $id,
            'ad_id' => $link['ad_id'],
            "ip_address" => $ip_address,
            "user_agent" => $this->input->user_agent(),
            "timestamp" => date("Y-m-d H:i:s"),
            "is_mobile" => ($this->agent->is_mobile()) ? "Y" : "N",
            "web_browser" => $this->agent->browser(),
            "mobile_device" => $this->agent->mobile(),
            "platform" => $this->agent->platform(),
            "referrer" => $referrer,
            "referrer_host" => $host,
            'is_fraud' => ($is_fraud === true) ? "Y" : "N",
            'network_id' => $network_id ? $network_id : $campaign['network_id'],
            'fraud_score' => $fraud_score,
        );

        if($geo) {
            $insert_array['geo_lat'] = $geo['lat'];
            $insert_array['geo_lon'] = $geo['lng'];
            $insert_array['country'] = $geo['country'];
            $insert_array['state'] = $geo['state'];
            $insert_array['city'] = $geo['city'];
            $insert_array['postal_code'] = $geo['postal_code'];
        } else {
            //$this->V2_log_model->create($id, ''.$link_id.' GEO IS EMPTY' , 'click');
        }

//        $json = json_encode($insert_array);
        //$this->V2_log_model->create($id, 'click for link '.$link_id.' and insert array is'. $json , 'click');
        $this->V2_campclick_click_model->create($insert_array);
        //$this->V2_log_model->create($id, 'click for link '.$link_id.' finish' , 'click');


        $cookie = array(
            "name" => "trafficPingTracker",
            "value" => $id,
            "expire" => 1825*86400,
            "domain" => ".reporting.prodata.media",
            "path" => "/",
        );

        $arrIoList = $this->Userlist_io_model->get_userlist_by_campaign_id($id);
        $arrVerticalList = $this->Userlist_vertical_model->get_userlist_from_vertical($campaign['vertical']);

        $arrVerticalList = isset($arrVerticalList[0])?$arrVerticalList[0]:array();
        $arrIoList = isset($arrIoList[0])?$arrIoList[0]:array();

        $verticalScriptTag = html_entity_decode($arrVerticalList['sniped_code']);
        $ioScriptTag = html_entity_decode($arrIoList['sniped_code']);
        // end adwords user mapping code

        //set_cookie($cookie);

        //header("Referer: http://reporting.prodata.media/c2/{$id}/{$link_id}");
        //header("Referer: {$link['destination_url']}");


        $javascriptGA = "
                        <script>
                          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

                          ga('create', 'UA-54038238-1', 'auto');
                          ga('send', 'pageview');

                        </script>
                        ";

        $redirectTime = 0;

        /*
         * Below we will setup our cookie (set cookie AND store cookie to DB), used for conversion tracking.
         *
         */
        $this->conversion_cookie($id);

        if($campaign['network_name'] == 'AIRPUSH') {
            $snip = "<html><head>{$javascriptGA}</head><body>{$ioScriptTag}{$verticalScriptTag}</body></html>";
            print $snip;
            header("Location: {$this->input->get('redir')} "); exit;
        }

        $snip = "<html><head><meta http-equiv='refresh' content=\"{$redirectTime};URL='{$this->input->get('redir')}'\">{$javascriptGA}</head><body><div style=\"height: 100%;background: url(/v2/images/load.gif) no-repeat;background-position: center;\"></div>
        {$ioScriptTag}{$verticalScriptTag}</body></html>";
        //$this->V2_log_model->create($id, 'sniped '.$link_id.' is '.$snip , 'snip');
        print $snip;
        exit;

    }

    public function second($id = null, $link_id = null, $network_id = null){
        $this->load->model('V2_log_model');
//        $this->load->model('Userlist_vertical_model');
//        $this->load->model('Userlist_io_model');

        $this->V2_log_model->create($id, 'click for link '.$link_id, 'second');
        if (!$id){
            $this->V2_log_model->create($id, ''.$link_id.' NO ID', 'second');
            throw new exception("Id required");
        }

        if (!$link_id){
            throw new exception("Link required");
        }

//        $this->load->model('V2_campclick_click_model');
//        $this->load->model('V2_ads_link_model');
//        $this->load->model('V2_master_campaign_model');
//
//        $this->load->library(["user_agent", 'Fraudfiltering', 'Geolookup']);
//        $this->load->helper('url');
//        $this->load->helper('cookie');


        $link = $this->V2_ads_link_model->get_by_id($link_id);

//        $campaign = $this->V2_master_campaign_model->get_by_id(false, $id);
//
//        if (!$campaign){
//            $this->V2_log_model->create($id, ''.$link_id.' NO CAMPAIGN' , 'click');
//            return false;
//        }
//
//        if ($campaign['max_clicks'] && !$campaign['max_budget']){
//
//            $count_campaign_clicks = $this->V2_campclick_click_model->get_campaign_click_count($id) + 1;
//            //$count_campaign_clicks++;
//            if ($count_campaign_clicks >= $campaign['max_clicks']){
//
//                $updated = $this->V2_master_campaign_model->update($id, array('network_campaign_status' => 'PAUSED', 'campaign_status' => 'COMPLETED'));
//
//                if($updated) {
//                    $campaign['network_campaign_status'] = 'PAUSED';
//                    $this->load->model('Common_model');
//                    $this->Common_model->update_campaign_status($campaign);
//                    $this->V2_log_model->create($id, 'COMPLETED', 'status');
//                }
//            }
//        }
//
//        $referrer = $this->agent->referrer();
//        $p = parse_url($referrer);
//        $host = (isset($p['host'])) ? $p['host'] : '';
//        //$this->V2_log_model->create($id, 'click for link '.$link_id.' host'. $host , 'click');
//
//        //need to fix
//        $this->fraudfiltering->ipaddress = $this->input->ip_address();
//        //$this->fraudfiltering->io = $io;
//        $this->fraudfiltering->io = $link_id;
//        $is_fraud = $this->fraudfiltering->checkFraud();
//
//        //$this->V2_log_model->create($id, 'click for link '.$link_id.' is_fraud'. $is_fraud , 'click');
//
//        $ip_address = $this->input->ip_address();
//
//        $this->geolookup->ip_address = $ip_address;
//
//        $geo = $this->geolookup->lookup();
//
//        //$this->V2_log_model->create($id, 'click for link '.$link_id.' geolookup'. $geo , 'click');
//
//        $insert_array = array(
//            "link_id" => $link_id,
//            'campaign_id' => $id,
//            'ad_id' => $link['ad_id'],
//            "ip_address" => $ip_address,
//            "user_agent" => $this->input->user_agent(),
//            "timestamp" => date("Y-m-d H:i:s"),
//            "is_mobile" => ($this->agent->is_mobile()) ? "Y" : "N",
//            "web_browser" => $this->agent->browser(),
//            "mobile_device" => $this->agent->mobile(),
//            "platform" => $this->agent->platform(),
//            "referrer" => $referrer,
//            "referrer_host" => $host,
//            'is_fraud' => ($is_fraud === true) ? "Y" : "N",
//            'network_id' => $network_id ? $network_id : $campaign['network_id'],
//        );
//        if($geo) {
//            $insert_array['geo_lat'] = $geo['lat'];
//            $insert_array['geo_lon'] = $geo['lng'];
//            $insert_array['country'] = $geo['country'];
//            $insert_array['state'] = $geo['state'];
//            $insert_array['city'] = $geo['city'];
//            $insert_array['postal_code'] = $geo['postal_code'];
//        } else {
//            $this->V2_log_model->create($id, ''.$link_id.' GEO IS EMPTY' , 'click');
//        }
//
//        $json = json_encode($insert_array);
//        //$this->V2_log_model->create($id, 'click for link '.$link_id.' and insert array is'. $json , 'click');
//        $this->V2_campclick_click_model->create($insert_array);
//        //$this->V2_log_model->create($id, 'click for link '.$link_id.' finish' , 'click');
//
//
//        $cookie = array(
//            "name" => "trafficPingTracker",
//            "value" => $id,
//            "expire" => 1825*86400,
//            "domain" => ".reporting.prodata.media",
//            "path" => "/",
//        );
//
//        $arrIoList = $this->Userlist_io_model->get_userlist_by_campaign_id($id);
//        $arrVerticalList = $this->Userlist_vertical_model->get_userlist_from_vertical($campaign['vertical']);
//
//        $arrVerticalList = isset($arrVerticalList[0])?$arrVerticalList[0]:array();
//        $arrIoList = isset($arrIoList[0])?$arrIoList[0]:array();
//
//        $verticalScriptTag = html_entity_decode($arrVerticalList['sniped_code']);
//        $ioScriptTag = html_entity_decode($arrIoList['sniped_code']);
        // end adwords user mapping code

        //set_cookie($cookie);

        header("Referer: http://reporting.prodata.media/c2/{$id}/{$link_id}");
        //header("Referer: {$link['destination_url']}");


//        $javascriptGA = "
//                        <script>
//                          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
//                          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
//                          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
//                          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
//
//                          ga('create', 'UA-54038238-1', 'auto');
//                          ga('send', 'pageview');
//
//                        </script>
//                        ";
//
//        $redirectTime = 1;
//        //<meta http-equiv='refresh' content=\"{$redirectTime};URL='{$link['destination_url']}'\">
//        $snip = "<html><head>{$javascriptGA}</head><body>{$ioScriptTag}{$verticalScriptTag}</body></html>";
//        //$this->V2_log_model->create($id, 'sniped '.$link_id.' is '.$snip , 'snip');
//        print $snip;

        header("Location: {$link['destination_url']} "); exit;

        $snip = "<html><head><meta http-equiv='refresh' content=\"{$redirectTime};URL='{$link['destination_url']}'\">{$javascriptGA}</head><body>{$ioScriptTag}{$verticalScriptTag}</body></html>";
        //$this->V2_log_model->create($id, 'sniped '.$link_id.' is '.$snip , 'snip');
        print $snip;
        exit;

    }

    public function iframe_ad()
    {
        $ad_id = $this->input->get('ad_id');
        $data = $this->v2_ad_model->get_by_id($ad_id);

        $this->log_impression($ad_id, $data);

        $view_data = [
            'ad_id' => $data['id'],
            'creative_url' => $data['creative_url'],
        ];

        $this->load->view('tracking/index', $view_data);
    }

    public function beacon($campaign_id, $ad_id)    {
        header('Access-Control-Allow-Origin: *');

        $data = $this->v2_ad_model->get_by_id($ad_id);
//        var_dump($data);die;
//        $is_ad_valid = $this->V2_campclick_impression_model->is_ad_valid($ad_id, $canmpaign_id);

//        if(!$is_ad_valid) {
//            die('Rejected');
//        }

        $data['is_retarget'] = "N";

        if ($this->input->get("win_price")) {
            $data['win_price'] = $this->input->get("win_price");
            $data['win_price'] = $data['win_price'] / 1000;
            $data['win_price'] = $data['win_price'] + $this->FORENSIQ_COST + $this->OVERHEAD_COST;
        }

        if ($this->input->get("network")) {
            //'SMAATO','SSPHWY','UNKNOWN'
            switch (strtoupper($this->input->get("network"))) {
                case "PRODATA_SMAATO":
                    $data['network'] = "SMAATO";
                    break;

                case "PRODATA_SSPHWY":
                    $data['network'] = "SSPHWY";
                    break;

                case "PRODATA_GOOGLEADX":
                    $data['network'] = "GOOGLEADX";
                    break;

                case "PRODATA_MOBFOX":
                    $data['network'] = "MOBFOX";
                    break;

                default:
                    $data['network'] = "UNKNOWN";
                    break;
            }
        }

        if ($this->input->get("provider") != "") {
            $data['provider'] = $this->input->get("provider");
        } else {
            $data['provider'] = $data['network'];
        }

        $this->log_impression($ad_id, $data);

        /**
         * Increment the amount of the daily spend into redis for fast lookup
         *
         */
        if ( is_numeric($data['win_price']) ){
            $this->clickcap->update_campaign_spend($data['win_price'], $campaign_id);
        }

        /**
         * Below we will setup our cookie (set cookie AND store cookie to DB), used for conversion tracking.
         *
         */
        $this->conversion_cookie($campaign_id);

        /**
         * Update Impression counter
         */
        $prodata_id = $this->input->get('prodata_id');
        if ( !empty($prodata_id) && !empty($campaign_id) ) {
            $count = $this->clickcap->increase_impression_counter('creativesImpCounter', $campaign_id . ":" . $prodata_id, 1);
        }

        header('Content-Type: image/gif');
        echo base64_decode("R0lGODdhAQABAIAAAPxqbAAAACwAAAAAAQABAAACAkQBADs=");
    }

    private function log_impression($ad_id, $data)
    {
        if ($this->agent->is_browser())
        {
            $agent = $this->agent->browser().' '.$this->agent->version();
        }
        elseif ($this->agent->is_robot())
        {
            $agent = $this->agent->robot();
        }
        elseif ($this->agent->is_mobile())
        {
            $agent = $this->agent->mobile();
        }
        else
        {
            $agent = 'Unidentified User Agent';
        }
//        var_dump(666);
        $insert = array(
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $agent,
            'ad_id' => $data['id'],
            'campaign_id' => $data['campaign_id'],
            'referrer' => $data['original_url'],
            'is_openrtb' => 1,
            'impressions_count' => 1,
            'timestamp' => date("Y-m-d H:i:s"),
            'win_price' => $data['win_price'],
            'rtb_network' => $data['network'],
            'provider' => $data['provider'],
            'is_retarget' => $data['is_retarget'],
        );

        //echo '<pre>'; print_r($insert); echo "ad id: $ad_id"; //die;
        //$this->V2_campclick_impression_model->is_ad_exists($ad_id);
//        var_dump(444);  exit;
        //if (!$this->V2_campclick_impression_model->is_ad_exists($ad_id)) {
            $this->V2_campclick_impression_model->log_impressions_insert($ad_id, $insert);

        //} else {
//            $impression_count = $this->V2_campclick_impression_model->get_impression_count_openrtb($ad_id);
//            $impression_count += 1;
//            $insert['impressions_count'] = $impression_count;
//            $this->V2_campclick_impression_model->log_impressions_update($ad_id, $insert);
//            $this->V2_campclick_impression_model->log_impressions_insert($ad_id, $insert);
//        }
    }
    public function ad_video_view($id) {

        $this->load->model('V2_ad_model');
        $ad = $this->V2_ad_model->get_by_id($id);
        $this->viewArray['ad'] = $ad;
        $redir = $this->input->get('redir');
        $redir = urlencode($redir);
        $url_encode = urlencode($ad['original_url']);
        $url_d_encode = urlencode($url_encode);
        $url = str_replace('http://', 'https://', $ad['destination_url'] );
        $url = str_replace('/c2/', '/c3/', $url );
        $this->viewArray['destination_url'] = $url.'?redir='.$redir.$url_d_encode;
        $creative_url = str_replace("http://", "https://", $ad['creative_url']);
        $this->viewArray['creative_url'] = $creative_url;
        $this->viewArray['campaign_id'] = $ad['campaign_id'];
        $this->viewArray['ad_id'] = $id;
        $this->viewArray['hidden_image'] = 'https://'.$_SERVER['SERVER_NAME'].'/tracking/beacon/'.$ad['campaign_id'].'/'.$ad['id'];
        //var_dump($this->viewArray);die;
        $this->load->view('tracking/ad_iframe_video_view', $this->viewArray);

    }

    public function ad_iframe_view($id) {

        $this->load->model('V2_ad_model');
        $ad = $this->V2_ad_model->get_by_id($id);
        $redir = $this->input->get('redir');
        $redir = urlencode($redir);
        //$this->viewArray['destination_url'] = 'http://reporting.prodata.media/c3/1877/2243?redir=%%CLICK_URL_ESC%%%%DEST_URL_ESC_ESC%%';
        //http://adclick.g.doubleclick.net/aclk?sa=l&ai=C8qeK&adurl=http://www.google.com/img.gif%3Fparam1%3Dred%26param2%3Dblue
        $url_encode = urlencode($ad['original_url']);
        $url_d_encode = urlencode($url_encode);
        //$this->viewArray['destination_url'] = 'http://reporting.prodata.media/c3/1877/2243?redir=http://adclick.g.doubleclick.net/aclk?sa=l&ai=C8qeK&adurl='.$url_d_encode;

        $url = str_replace('http://', 'https://', $ad['destination_url'] );

        $url = str_replace('/c2/', '/c3/', $url );
        //$this->viewArray['destination_url'] = $url.'?redir=%%CLICK_URL_ESC%%'.$url_d_encode;
        $this->viewArray['destination_url'] = $url.'?redir='.$redir.$url_d_encode;
        //$this->viewArray['destination_url'] = 'https://reporting.prodata.media/c3/'.$ad['campaign_id'].'/2243?redir=https://reporting.prodata.media/tracking/second_redirect?final_url='.$url_d_encode;

        $creative_url = str_replace("http://", "https://", $ad['creative_url']);
        //$this->viewArray['creative_url'] = $ad['creative_url'];
        $this->viewArray['creative_url'] = $creative_url;
        $this->viewArray['campaign_id'] = $ad['campaign_id'];
        $this->viewArray['ad_id'] = $id;
        //$this->viewArray['redir'] = $redir;

        $this->viewArray['hidden_image'] = 'https://'.$_SERVER['SERVER_NAME'].'/tracking/beacon/'.$ad['campaign_id'].'/'.$ad['id'];

//        echo  '<pre>';
//        print_r($this->viewArray);die;
        $this->load->view('tracking/ad_iframe_view', $this->viewArray);
    }

//    public function random(){
//        //Jason code need to change v2
//        $this->Campclick_model->io = $io;
//
//        //redirect("http://report-site.com/campaigns/landing/index3.php?utm_campaign=prodata-test-2015-06-10&utm_medium=ppc&utm_source=prodatafeed");
//
//        $link = $this->Campclick_model->select_random_link_improved();
////	    $link = $this->Campclick_model->select_random_link();
//
//        if ($link !== false)	{
//            $this->Campclick_model->update_click_cap();
//            $count = $this->Campclick_model->get_current_click_cap();
//            $campaign = $this->Campclick_model->get_campaign_by_io();
//
//            // THIS WILL SET CAMPAIGN CAPS PER HOUR IF WE ENABLE IT
//            if ($campaign['cap_per_hour'] == "0")   {
//                $this->redirect($io, $link['counter'], $link['link_id'], $crap);
//                exit;
//            } else {
//                if ($count <= $campaign['cap_per_hour'])  {
//                    $this->redirect($io, $link['counter'], $link['link_id'], $crap);
//                    exit;
//                }
//            }
//        }
//
//        if ($crap == "")
//            print "blah";
//        //$this->reinjector(); // if the campaign exceeds its cap per hour, reinject back into the system as a new campaign
//        //redirect("http://report-site.com/campclick/verizoninjector");
//        else
//            redirect("http://www.facebook.com/cheapdeals4me");
//        exit;
//    }
}
