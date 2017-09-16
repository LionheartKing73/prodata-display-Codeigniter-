<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH.'third_party/ffmpeg-php-master/FFmpegAutoloader.php';
class Campaign extends CI_Controller    {
    public $viewArray = array();
    private $userid;
    private $user;
    protected $CI;
    protected $multiple_budget = 2;
    protected $campaign_budget = 98;
    private $view_file = null;
    private $parent_customer;
    private $user_type;
    private $tp = array(
        'is_adtrack' => 'Third Party / Ad-Track'
    );
    private $campaign_types = array(
        'TEXTAD' => 'TEXTAD',
        'DISPLAY' => 'DISPLAY',
        'DISPLAY-RETARGET' => 'DISPLAY-RETARGET',
        'THIRD-PARTY/AD-TRACK' => 'THIRD-PARTY/AD-TRACK',
        'RICH_MEDIA_SURVEY' => 'RICH_MEDIA_SURVEY',
        'FB-MOBILE-NEWS-FEED' => 'FB-MOBILE-NEWS-FEED',
        'FB-DESKTOP-RIGHT-COLUMN' => 'FB-DESKTOP-RIGHT-COLUMN',
        'FB-DESKTOP-NEWS-FEED' => 'FB-DESKTOP-NEWS-FEED',
        'FB-PAGE-LIKE' =>'FB-PAGE-LIKE',
        'YAHOO_CAROUSEL' => 'YAHOO_CAROUSEL',
        'FB-VIDEO-VIEWS' =>'FB-VIDEO-VIEWS',
        'FB-VIDEO-CLICKS' =>'FB-VIDEO-CLICKS',
        'FB-LOCAL-AWARENESS' =>'FB-LOCAL-AWARENESS',
        'FB-PROMOTE-EVENT' => 'FB-PROMOTE-EVENT',
        'FB-MOBILE-APP-INSTALLS' => 'FB-MOBILE-APP-INSTALLS',
        'IN_APP' => 'IN_APP',
        'OVERLAY_AD' => 'OVERLAY_AD',
        'PUSH_CLICK_TO_CALL' => 'PUSH_CLICK_TO_CALL',
        'RICH_MEDIA_INTERSTITIAL' => 'RICH_MEDIA_INTERSTITIAL',
        'DIALOG_CLICK_TO_CALL' => 'DIALOG_CLICK_TO_CALL',
    );
    private $campaign_type_names = array(
        'TEXTAD'=>'Text Ad',
        'DISPLAY'=>'Display Ad',
        'RICH_MEDIA_SURVEY' => 'Rich Media Survey',
        'DISPLAY_YAHOO'=>'Display Ad',
        'VIDEO_YAHOO'=>'Video Ad',
        'APP_INSTALL_YAHOO'=>'App Install Ad',
        'YAHOO'=>'Yahoo Gemini',
        'DISPLAY-RETARGET'=>'Display-Retargeting Ad',
        'FB-MOBILE-NEWS-FEED'=>'Mobile News Feed',
        'FB-DESKTOP-RIGHT-COLUMN'=>'Desktop Right Column',
        'FB-DESKTOP-NEWS-FEED'=>'Desktop News Feed',
        'FB-PAGE-LIKE'=>'Page Likes',
        'FB-LOCAL-AWARENESS'=>'Local Awareness',
        'FB-VIDEO-VIEWS'=>'Video Views',
        'FB-VIDEO-CLICKS'=>'Video Clicks',
        'FB-CAROUSEL-AD'=>'Carousel Ad',
        'YAHOO_CAROUSEL'=>'Carousel Ad',
        'FB-LEAD'=>'Lead Ad',
        'IN_APP'=>'In-App Display',
        'OVERLAY_AD'=>'Overlay Ad Display',
        'PUSH_CLICK_TO_CALL'=>'Push Click to Call',
        'DIALOG_CLICK_TO_CALL'=>'Dialog Click to Call',
        'RICH_MEDIA_INTERSTITIAL'=>'Rich Media Ad',
        'FB-PROMOTE-EVENT'=>'Promote Event',
        'FB-MOBILE-APP-INSTALLS'=>'Install Mobile App',
        'FB-INSTAGRAM'=>'Instagram',
        'FB-INSTAGRAM-VIDEO'=>'Instagram Video',
    );
    public function __construct()   {
        parent::__construct();
        $this->load->helper("url");
        $this->load->helper('cookie');
        $this->load->library('user_agent');
        $this->load->library('pagination');
        $this->load->library('form_validation');
        $this->load->model('Monitor_model');
        $this->load->model("V2_domains_model");
        $this->load->model("Vendor_model");
        $this->load->model("Country_model");
        $this->load->model("Facebook_model");
        $this->load->model("V2_master_campaign_model");
        $this->load->model("V2_ad_model");
        $this->load->model('v2_iab_category_model');
        $this->load->model('v2_campaign_category_model');
        $this->load->model("Zip_model");
        $this->load->model("Report_model");
        $this->load->model("Log_model");
        $this->load->model("Finditquick_model");
        $this->load->model("facebook_model");
        $this->load->model("v2_google_adx_model");
        $this->load->library("parser");
        $this->load->library("session");
        $this->load->library('ion_auth');
        $this->load->library('facebook');
        $this->load->library('ip_address');
        $this->load->library('google_adx');

         //is checking accessable domains
        $this->load->library("domain");
        $domain_data = $this->domain->filterDom();
        $this->viewArray['domain_data'] = $this->session->userdata['assets'];

        if($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
            $this->require_auth();
        }
        if ($this->ion_auth->logged_in()) {
            $this->user = $this->ion_auth->user()->row_array();
        }
       $this->userid = $this->user['id'];
       $this->viewArray['user'] = $this->user;
       $this->parent_customer = $this->user['parent_customer'];
       $this->user_type = $this->user['user_type'];
       $this->viewArray['user_type'] = $this->user_type;
          if (!empty($this->user['is_admin'])){
            $this->viewArray['is_admin'] = true;
        }
         if($this->user['domain_id']) {
            $domain = $this->V2_domains_model->get_domain($this->user['domain_id']);
            if($domain['domain'] == $_SERVER["HTTP_HOST"]) {
                $this->viewArray['domain_data'] = $domain;
            }
        }
        $this->viewArray['current_url'] = current_url();
        $this->viewArray['base_url'] = base_url();
        $this->viewArray['site_url'] = site_url();
        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';
        $config['priority'] = 1;
        $this->load->library('email');
        $this->email->initialize($config);
        $this->view_file = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/' .$this->uri->segment(3);
    }
    public function index() {
        $this->parser->parse('take5/trackreport.php', $this->viewArray);
    }
    public function getLatLng() {
        if($this->input->is_ajax_request()) {
            $addr = $this->input->post('address');
            $url = "http://maps.google.com/maps/api/geocode/json?address=$addr";
            $address = str_replace(" ", "+", $addr);
            $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address");
            $json = json_decode($json);
            $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
            $lng = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
            if($lat && $lng) {
                print json_encode(array("status" => "SUCCESS", "message" => ['lat' => $lat, 'lng' => $lng]));
            } else {
                print json_encode(array("status" => "ERROR", "message" => "Wrong address"));
            }
        } else {
            redirect(base_url());
        }
    }
    public function campaign_list($page=0,$status=null) {
        $this->require_auth();
        $params = array();
        $limit = 0;
        $offset = 25;
        $url_string = '';
        if($this->input->post()){
            $params = $this->input->post();
            $url_string = http_build_query($params);
            $params = array_filter($params);
        } else {
            $last_week = strtotime("-30 day");
            $next_week = strtotime("+0 day");
            $params['campaign_end_datetime'] = Date("Y-m-d", $next_week);
            $params['campaign_start_datetime'] = Date("Y-m-d", $last_week);
            if($status) {
                $params['campaign_status'] = $status;
            } else {
                $params['campaign_status'] = 'ACTIVE';
            }
        }
        $limit = $offset*$page;
        if($this->user_type == 'viewer') {
            //$total_count = $this->V2_master_campaign_model->get_list_count($params, $this->parent_customer);
            //$campaigns = $this->V2_master_campaign_model->get_list($params, $this->parent_customer, $page, $offset);
            $this->viewArray['user_type'] = 'viewer';
            $this->load->model('V2_viewer_campaign_model');
            $campaign_ids = $this->V2_viewer_campaign_model->get_campaigns_id_by_parent_id_and_viewer_id($this->parent_customer,$this->userid);
            foreach($campaign_ids as $key => $ids){
                $campaign_ids_list = '';
                $campaign_ids_list .= $ids['campaign_id'].',';
            }
            $campaign_ids_list = rtrim($campaign_ids_list, ",");
            $total_count = $this->V2_master_campaign_model->get_list_count($params, $this->parent_customer, $campaign_ids_list);
            $campaigns = $this->V2_master_campaign_model->get_list($params, $this->parent_customer, $page, $offset, $campaign_ids_list);
        }
        else{
            $total_count = $this->V2_master_campaign_model->get_list_count($params, $this->userid, null, $this->user['is_admin']);
            $campaigns = $this->V2_master_campaign_model->get_list($params, $this->userid, $page, $offset, null, $this->user['is_admin']);
        }
        foreach($campaigns as $key => $campaign){
            $cost = 0;
            $clicks_count = null;
            $impressions_count = null;
            if($campaign['max_budget']) {
                $this->load->model('V2_campclick_impression_model');
                $network_cost = $this->V2_master_campaign_model->get_campaign_cost($campaign['id'], $campaign['network_name']);
                $rtb_cost = $this->V2_campclick_impression_model->get_campaign_rtb_cost($campaign['id']);
                $cost = $network_cost + $rtb_cost;
            }
            if($campaign['max_clicks']  || $campaign['is_thru_guarantee']) {
                $this->load->model('V2_campclick_click_model');
                $clicks_count = $this->V2_campclick_click_model->get_campaign_click_count($campaign['id']);
            }
            if($campaign['max_impressions']) {
                $this->load->model('V2_campclick_impression_model');
                $impressions_count = $this->V2_campclick_impression_model->get_campaign_impressions_count($campaign['id']);
            }
            if($campaign['campaign_type'] == "EMAIL") {
                $this->load->model("V2_email_campaign_reporting_model");
                $this->load->model("V2_email_campaign_link_reporting_model");
                $campaigns[$key]['total_report'] = $this->V2_email_campaign_reporting_model->get_total_clicks_count_by_campaign_id($campaign['id']);
                $_clicks = $this->V2_email_campaign_link_reporting_model->get_total_clicks_count_by_campaign_id($campaign['id']);
                $campaigns[$key]['total_report']['clicks_count'] = !empty($_clicks['clicks_count']) ? $_clicks['clicks_count'] : 0;
            }
            $campaigns[$key]['cost'] = $cost;
            //print_r($cost);
            $campaigns[$key]['total_impressions_count'] = $impressions_count;
            $campaigns[$key]['total_clicks_count'] = $clicks_count;
        }
        $this->viewArray['campaigns'] = $campaigns;
        $this->viewArray['params'] = $params;
        $base_url = '/v2/campaign/campaign_list';
        $this->pagination_list($total_count,$offset,4,$base_url);
        $this->viewArray['links'] = $this->pagination->create_links();
    //print_r($this->viewArray);die;
        if(!$this->input->post()){
            // echo "<pre>";
            // print_r($this->viewArray);
            // die;
            $this->parser->parse($this->view_file, $this->viewArray);
        } else {
            $html = $this->parser->parse('v2/campaign/campaign_list/content', $this->viewArray, true);
            echo json_encode(array("status"=>"SUCCESS", "html" => $html));
        }
    }

    public function ad_list($campaign_id, $show_ad_id = false){
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        if (!$campaign_id || !is_numeric($campaign_id)){
            redirect(base_url());
        }
        if($this->user_type == 'viewer') {
            redirect(base_url());
        }
        $campaign = $this->V2_master_campaign_model->get_all_with_clicks_by_id($this->userid, $campaign_id);
        $this->load->model('V2_ad_model');
        $check_campaign_type = $this->V2_master_campaign_model->check_campaign_type_by_campaign_id($campaign_id);
        if(($check_campaign_type[0]['campaign_type'] == 'FB-PAGE-LIKE') || ($check_campaign_type[0]['campaign_type'] == 'FB-VIDEO-VIEWS')) {
            $ads = $this->V2_ad_model->get_with_likes_by_campaign_id($campaign_id);
        }else{
            $ads = $this->V2_ad_model->get_with_clicks_by_campaign_id($campaign_id);
        }

        //print_r($campaign);

        /*
        if ($campaign['campaign_is_converted_to_live']=="Y" && $campaign['network_campaign_status'] != "ACTIVE" && $campaign['campaign_status'] != 'SCHEDULED'){
            $this->viewArray['editable'] = false;
        } else {
            $this->viewArray['editable'] = true;
        }
        */

        if ($campaign['campaign_status'] != "COMPLETED") {
            $this->viewArray['editable'] = true;
        } else {
            $this->viewArray['editable'] = false;
        }
        $this->viewArray['ads'] = $ads;
        $this->viewArray['campaign'] = $campaign;
        $this->viewArray['show_ad_id'] = $show_ad_id;
        $this->parser->parse($this->view_file, $this->viewArray);
    }

    public function edit_campaign($id = null) {
        if (!$id || !is_numeric($id)){
            redirect(base_url());
        }
        if($this->user_type == 'viewer' || $this->user['edit_campaign'] == 'N') {
            redirect(base_url());
        }
        $original_user_id = $this->userid;
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        $campaign = $this->V2_master_campaign_model->get_all_with_clicks_and_impressions_by_id($this->userid, $id);
        if (!$campaign){
            redirect(base_url());
        }
        $this->load->model('V2_campclick_impression_model');
        $this->load->model('Userlist_io_model');
        $this->viewArray['campclick'] = $this->V2_campclick_impression_model->get_campaign_rtb_cost($id);
        $campaign['cost'] = $this->V2_master_campaign_model->get_campaign_cost($id, $campaign['network_name']);
        // get states for campaign
        $this->load->model('Country_model');
        if($campaign['geotype'] == "state"){
            $states = $this->Country_model->get_states_by_country($campaign['country']);
            $campaign['state_array'] = explode(",", $campaign['state']);
            $this->viewArray['states'] = $states;
        }
        // get links for EMAIL campaigns
        if($campaign['campaign_type']=="EMAIL") {
            $this->load->model('V2_ads_link_model');
            $link = $this->V2_ads_link_model->get_link_by_campaign_id($campaign['id']);
            $this->viewArray['link'] = $link;
        }
        if($campaign['campaign_type']=="FB-LEAD") {
            $this->load->model("V2_fb_form_model");
            $form = $this->V2_fb_form_model->get_by_id($campaign['form_id']);
            $this->viewArray['form'] = $form;
        }
        // check if campaign converted to live and have status active then we can edit it
        if ($campaign['campaign_is_converted_to_live']=="Y" && $campaign['network_campaign_status'] != "ACTIVE"){
            $this->viewArray['editable'] = false;
        } else {
            $this->viewArray['editable'] = true;
        }
        $campaign['keywords'] = explode(',',$campaign['keywords']);
        $this->viewArray['campaign'] = $campaign;

        // IAB categories List
        $this->viewArray['iab_categories'] = $this->v2_iab_category_model->get_all_categories();

        // get associated IAB categories with this campaign
        $this->viewArray['campaign_associated_iab_categories'] = $this->v2_campaign_category_model->get_associated_iab_categories_by_campaign_id($id);

        // get IO list for Retargeting
        $this->viewArray['campaign']['user_id_to_pick_io'] = $original_user_id;
        $this->viewArray['io_list'] = $this->Userlist_io_model->get_userlist_from_io_by_user_id($original_user_id);
        $this->viewArray['campaign']['retargeting_ios_array'] = explode(',', $this->viewArray['campaign']['retargeting_io']);

        $this->parser->parse($this->view_file, $this->viewArray);
    }
    public function edit_location() {
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        $location_data = $this->input->post();
        $id = $location_data['campaign_id'];
        //var_dump($id, $location_data); exit;
//        $active = $this->V2_master_campaign_model->campaign_is_active($id);
//        if(!$active) {
//            print json_encode(array("status"=>"ERROR", "message" => 'You can not edit PAUSED campaign'));
//            exit;
//        }
        if(isset($location_data['from_admin']) && $location_data['from_admin']) {
            $this->userid = null;
        }
        $campaign = $this->V2_master_campaign_model->get_by_id($this->userid, $id);
        $active = false;
        $scheduled = false;
        if($campaign['campaign_is_converted_to_live'] == 'Y' && $campaign['network_campaign_status'] == 'ACTIVE') {
            $active = true;
        } elseif($campaign['campaign_status'] == 'DISAPPROVED') {
            $active = false;
            $disapproved = true;
        } elseif($campaign['campaign_status'] == 'SCHEDULED') {
            $scheduled = true;
        }
        $geo_type = $this->input->post('geotype');
        $rules = array(
            array('field' => 'campaign_id', 'label' => 'Campaign id', 'rules' => 'required|numeric'),
        );
        if($geo_type=='country') {
            $rules[] = array('field' => 'country', 'label' => 'Country', 'rules' => 'required|max_length[2]');
        } elseif($geo_type=='state') {
            $rules[] = array('field' => 'country', 'label' => 'Country', 'rules' => 'required|max_length[2]');
            $rules[] = array('field' => 'state[]', 'label' => 'States', 'rules' => 'required');
        } else {
            $rules[] = array('field' => 'zip', 'label' => 'Postal code', 'rules' => 'required');
            $rules[] = array('field' => 'radius', 'label' => 'Radius', 'rules' => 'required|numeric|min_length[2]');
        }
        $this->form_validation->set_rules($rules);
        if($this->form_validation->run() == FALSE) {
            print json_encode(array("status"=>"ERROR", "message" => validation_errors()));
            exit;
        }
        $type = 'location';
        $result = $this->V2_master_campaign_model->validate_location($location_data);
        if(count($result['messages'])){
            $messages = '';
            foreach($result['messages'] as $message){
                $messages .=' '.$message;
            }
            // get messages texts and send json
            print json_encode(array("status"=>"ERROR", "message" => $messages));
            exit;
        }
        if($disapproved) {
            $result['valide_geo']['campaign_status'] = 'SCHEDULED';
            $result['valide_geo']['campaign_is_converted_to_live'] = 'N';
            $result['valide_geo']['campaign_start_datetime'] = date("Y-m-d H:i:s");
        }
        if($scheduled) {
            $result['valide_geo']['campaign_start_datetime'] = date("Y-m-d H:i:s");
        }
        $updated = $this->V2_master_campaign_model->update($id,$result['valide_geo']);
        // save into log table
        //check if campaign converted to live then make changes in network too
        if($updated){
            $this->load->model('V2_log_model');
            $campaign = $this->V2_master_campaign_model->get_by_id($this->userid, $id);
//            if($campaign['is_multiple']=='Y') {
//                $this->load->model('V2_multiple_campaign_model');
//                $multiple_update = $this->V2_multiple_campaign_model->update_by_campaign_id($id,$result['valide_geo']);
//                if(!$multiple_update) {
//                    print json_encode(array("status" => "ERROR", "message" => "Campaign location criteria didn't update"));
//                    exit;
//                }
//            }
            switch ($result['valide_campaign']['geotype']) {
                case "country":
                    $this->V2_log_model->create($campaign['id'], $result['valide_campaign']['country'], $result['valide_campaign']['geotype']);
                    break;
                case "state":
                    $this->V2_log_model->create($campaign['id'], $result['valide_campaign']['state'], $result['valide_campaign']['geotype']);
                    break;
                case "postalcode":
                    $this->V2_log_model->create($campaign['id'], $result['valide_campaign']['zip'], $result['valide_campaign']['geotype']);
                    break;
            }
            // checking if campaign converted to live
            if ($active) {
                $this->load->model('Common_model');
                $result = $this->Common_model->update($campaign, $type);
                if ($result['message']) {
                    echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                    exit;
                }
                if($campaign['is_multiple']=='Y') {
                    $campaign['network_name'] = 'GOOGLE';
                    $result = $this->Common_model->update($campaign, $type);
                    if ($result['message']) {
                        echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                        exit;
                    }
                }
            }
            print json_encode(array("status" => "SUCCESS", "message" => "Campaign location criteria successfully updated"));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Campaign location criteria didn't update"));
            exit;
        }
    }
    public function edit_budget()
    {
        //debug($this->user);
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        $data = $this->input->post();
        $id = $data['campaign_id'];
        $type = 'budget';
//        if($this->user['is_billing_type'] == 'FLAT'){
//            echo json_encode(array('status' => 'ERROR', "message" => 'Invalid user type'));
//            exit;
//        }
        $rules = array(
            array('field' => 'campaign_id', 'label' => 'Campaign id', 'rules' => 'required|numeric'),
            array('field' => 'budget', 'label' => 'Budget', 'rules' => 'required|numeric|max_length[9]'),
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE) {
            print json_encode(array("status" => "ERROR", "message" => validation_errors()));
            exit;
        }
        $campaign = $this->V2_master_campaign_model->get_by_id($this->userid, $id);
//        if ($campaign['campaign_is_converted_to_live']=="Y" && $campaign['network_campaign_status'] != "ACTIVE"){
//            print json_encode(array("status"=>"ERROR", "message" => 'You can not edit PAUSED campaign'));
//            exit;
//        }
        if (!$campaign){
            echo json_encode(array('status' => 'ERROR', "message" => 'Invalid campaign ID'));
            exit;
        }
        if($this->user['is_admin'] && $campaign['userid'] != $this->user['id']) {
            $this->load->model('V2_users_model');
            $this->user = $this->V2_users_model->get_by_id($campaign['userid']);
        }
        $campaign_budget = $data['budget'];
        if($this->user['is_billing_type'] == 'PERCENTAGE') {
            // for percentage users we need to calculate percentage budget
            if ($campaign["max_budget"]) {
                $new_max_budget = $data['budget'] + $campaign['max_budget'];
                $values['max_budget'] = $new_max_budget;
                $new_percentage_max_budget = $new_max_budget * $this->user['budget_percentage'] / 100;
                $values['percentage_max_budget'] = $new_percentage_max_budget;
                $message[] = " WE ADD $campaign_budget ADDITIONAL BUDGET FOR YOUR CAMPAIGN";
            }
            if ($campaign["max_clicks"]) {
                //calculate how clicks we need to add for campaign
                $clisks_count = $campaign_budget/$this->user['display_click'];
                $clisks_count = round($clisks_count);
                $values['max_clicks'] = $campaign['max_clicks'] + $clisks_count;
                $message[] = " WE ADD $clisks_count ADDITIONAL CLICKS FOR YOUR CAMPAIGN";
            }
            if ($campaign["max_impressions"]) {
                //calculate how clicks we need to add for campaign
                $imp_count = $campaign_budget/$this->user['display_imp'] * 1000;
                $imp_count = round($imp_count);
                $values['max_impressions'] = $campaign['max_impressions'] + $imp_count;
                $message[] = " WE ADD $imp_count ADDITIONAL IMPRESSIONS FOR YOUR CAMPAIGN";
            }
//            else {
//
//                print json_encode(array("status" => "ERROR", "message" => "Campaign budget didn't update"));
//                exit;
//
//            }
        } else {
            $new_max_budget = $campaign_budget + $campaign['max_budget'];
            $values['max_budget'] = $new_max_budget;
            $new_percentage_max_budget = $campaign['percentage_max_budget'] + $campaign_budget * $this->user['flat_percentage_budget'] / 100;
            $values['percentage_max_budget'] = $new_percentage_max_budget;
            if ($campaign["max_clicks"]) {
                //calculate how clicks we need to add for campaign
                $tier = 'display_click_'.$campaign['campaign_tier'];
                $clisks_count = $campaign_budget/$this->user[$tier];
                $clisks_count = round($clisks_count);
                $values['max_clicks'] = $campaign['max_clicks'] + $clisks_count;
                $message[] = " WE ADD $clisks_count ADDITIONAL CLICKS FOR YOUR CAMPAIGN";
            }
            if ($campaign["max_impressions"]) {
                //calculate how clicks we need to add for campaign
                $tier = 'display_imp_'.$campaign['campaign_tier'];
                $imp_count = $campaign_budget / $this->user[$tier] * 1000;
                $imp_count = round($imp_count);
                $values['max_impressions'] = $campaign['max_impressions'] + $imp_count;
                $message[] = " WE ADD $imp_count ADDITIONAL IMPRESSIONS FOR YOUR CAMPAIGN";
            }
//            else {
//
//                print json_encode(array("status" => "ERROR", "message" => "Campaign budget didn't update flat"));
//                exit;
//
//            }
        }
        $full_message = 'Campaign Budget successfully updated. '.implode(',',$message);
        // update campaign clicks or impr count
        $updated = $this->V2_master_campaign_model->update($id, $values);
        if($campaign['is_multiple']=='Y') {
            // clearyfy multiple percentage budget
            //$campaign_budget = $data['budget']*$this->campaign_budget/100;
            $multiple_budget = $data['budget']*$this->multiple_budget/100;
        }
        // need to calculate old budget + new budget
        if ($campaign['network_name'] == "FIQ" && $campaign['campaign_type'] == "TEXTAD") {
            $ads = $this->V2_ad_model->get_ads_by_campaign_id($campaign['id']);
            $daily_cap = ($campaign['budget'] + $campaign_budget)/ count($ads);
            $campaign['budget'] = $daily_cap;
            $this->V2_ad_model->update_all_by_campaign_id($campaign['id'], ['daily_cap' => $daily_cap]);
        }
        //check if campaign converted to live then make changes in network too
        if ($updated) {
            //save in log table
            $this->load->model('V2_log_model');
            $this->V2_log_model->create($id, $data['budget'], $type);
            // send to the user new invoice
            if ($this->user['is_billing'] == "Y" && $this->user['is_qb_invoicing'] == 'Y' ) {
                $this->load->model("Billing_model");
                $invoice_data = array(
                    "io" => $campaign['io'],
                    "quickbooks_list_id" => $this->user['quickbooks_list_id'],
                    "additional_budget" => $campaign_budget
                );
                $this->Billing_model->build_additional_invoice_queue($invoice_data);
            }
            if($campaign['is_multiple']=='Y') {
                $this->load->model('V2_multiple_campaign_model');
                // reset multiple ad network data
                $multiple_update = $this->V2_multiple_campaign_model->update_budget_by_campaign_id($id, $multiple_budget);
                if(!$multiple_update) {
                    print json_encode(array("status" => "ERROR", "message" => "Campaign budget didn't update fiq"));
                    exit;
                }
            }
            if ($campaign['network_name'] == "FIQ") {
                $this->load->model('Common_model');
                $result = $this->Common_model->update($campaign, $type);
                if ($result['message']) {
                    echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                    exit;
                }
                // if campaign is multiple then save changes into second network
                if ($campaign['is_multiple'] == 'Y') {
                    $campaign['network_name'] = 'GOOGLE';
                    $result = $this->Common_model->update($campaign, $type);
                    if ($result['message']) {
                        echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                        exit;
                    }
                }
            } elseif($campaign['campaign_status'] == "COMPLETED") {
                $update = $this->V2_master_campaign_model->update($id, array("network_campaign_status" => "ACTIVE", "campaign_status" => "ACTIVE"));
                $campaign['network_campaign_status'] = "ACTIVE";
                $campaign['campaign_status'] = "ACTIVE";
                $this->load->model('Common_model');
                $result = $this->Common_model->update_campaign_status($campaign);
                if ($result['message']) {
                    echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                    exit;
                }
            }
            $full_message = 'Campaign Budget successfully updated. '.implode(',',$message);
            print json_encode(array("status" => "SUCCESS", "message" => $full_message));
            exit;
        }
        else {
            print json_encode(array("status" => "ERROR", "message" => "Campaign Budget didn't update"));
            exit;
        }
    }
    public function edit_end_date()
    {
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        //var_dump($this->input->post()); exit;
        $data = $this->input->post();
        $id = $data['campaign_id'];
//        $active = $this->V2_master_campaign_model->campaign_is_active($id);
//        if(!$active) {
//            print json_encode(array("status"=>"ERROR", "message" => 'You can not edit PAUSED campaign'));
//            exit;
//        }
        $type = 'end_date';
        $rules = array(
            array('field' => 'campaign_id', 'label' => 'Campaign id', 'rules' => 'required|numeric'),
            array('field' => 'campaign_end_datetime', 'label' => 'End date', 'rules' => 'required'),
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE) {
            print json_encode(array("status" => "ERROR", "message" => validation_errors()));
            exit;
        }
        $new_date = date("Y-m-d H:i:s", strtotime($data['campaign_end_datetime']));
        // call validate for date time
        $updated = $this->V2_master_campaign_model->update($id, array('campaign_end_datetime' => $new_date));
        // save into log table
        //check if campaign converted to live then make changes in network too
        if ($updated) {
            $this->load->model('V2_log_model');
            $campaign = $this->V2_master_campaign_model->get_by_id($this->userid, $id);
            $this->V2_log_model->create($id, $new_date, $type);
//            if ($campaign['campaign_is_converted_to_live'] == 'Y') {
//                $this->load->model('Common_model');
//                $result = $this->Common_model->update($campaign, $type);
//                if ($result['message']) {
//                    echo json_encode(array("status" => "ERROR", "message" => $result['message']));
//                    exit;
//                }
//            }
            print json_encode(array("status" => "SUCCESS", "message" => "Campaign end date criteria successfully updated"));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Campaign end date criteria didn't update"));
            exit;
        }
    }
    public function edit_fb_form()
    {
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        $data = $this->input->post();
        $id = $data['form_id'];
        $type = 'fb_form';
        $rules = array(
            array('field' => 'campaign_id', 'label' => 'Campaign id', 'rules' => 'required|numeric'),
            array('field' => 'form_id', 'label' => 'Form id', 'rules' => 'required|numeric'),
            array('field' => 'export_type', 'label' => 'Export type', 'rules' => 'required'),
        );
        if($data['export_type'] == 'email_address') {
            $rules[] = array('field' => 'email', 'label' => 'Email', 'rules' => 'required');
            $rules[] = array('field' => 'email_type', 'label' => 'Delivery type', 'rules' => 'required');
        }
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE) {
            print json_encode(array("status" => "ERROR", "message" => validation_errors()));
            exit;
        }
        $this->load->model("V2_fb_form_model");
        if($data['export_type'] == 'email_address') {
            $data_for_update = array('email' => $data['email'], 'email_type'=>$data['email_type'], 'export_type'=>$data['export_type']);
        } else {
            $data_for_update = array('export_type'=>$data['export_type']);
        }
        $updated = $this->V2_fb_form_model->update($id, $data_for_update);
        // save into log table
        if ($updated) {
            $this->load->model('V2_log_model');
            $this->V2_log_model->create($id, json_encode($data), $type);
            print json_encode(array("status" => "SUCCESS", "message" => "Campaign Form successfully updated"));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Campaign Form didn't update"));
            exit;
        }
    }
    public function update_end_date()
    {
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        //var_dump($this->input->post()); exit;
        $data = $this->input->post();
        $id = $data['id'];
        $type = 'end_date';
        if (!$data['end_date'] || !$data['id']) {
            print json_encode(array("status" => "ERROR", "message" => 'End date or campaign id is empty'));
            exit;
        }
        $new_date = date("Y-m-d H:i:s", strtotime($data['end_date']));
        // call validate for date time
        $updated = $this->V2_master_campaign_model->update($id, array('campaign_end_datetime' => $new_date));
        // save into log table
        //check if campaign converted to live then make changes in network too
        if ($updated) {
            $this->load->model('V2_log_model');
            $this->V2_log_model->create($id, $new_date, $type);
            print json_encode(array("status" => "SUCCESS", "message" => "Campaign end date criteria successfully updated"));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Campaign end date criteria didn't update"));
            exit;
        }
    }
    public function extend_campaign_end_date($campaign_id)
    {
        $type = 'end_date';
        $campaign = $this->V2_master_campaign_model->get_by_id_and_status(null, $campaign_id, 'COMPLETED');
        if(!$campaign) {
            echo '<p>Invalid campaign id or campaign already EXTENDED</p>'; exit;
        }
        if( floor((strtotime('now') - strtotime($campaign['campaign_end_datetime']))/(60*60)) >= 36 ) {
            echo '<p>This link was EXPIRED</p>'; exit;
        }
        $campaign['network_campaign_status'] = 'ACTIVE';
        $campaign['campaign_status'] = 'ACTIVE';
        $new_date = date("Y-m-d H:i:s", strtotime("+3 days"));
        // call validate for date time
        $campaign['campaign_end_datetime'] = $new_date;
        $updated = $this->V2_master_campaign_model->update($campaign_id, array('campaign_end_datetime' => $new_date, 'network_campaign_status' => 'ACTIVE', 'campaign_status' => 'ACTIVE'));
        // save into log table
        //check if campaign converted to live then make changes in network too
        if ($updated) {
            $this->load->model('V2_log_model');
            $this->V2_log_model->create($campaign_id, $new_date, $type);
            if ($campaign['campaign_is_converted_to_live'] == 'Y') {
                $this->load->model('Common_model');
                $result = $this->Common_model->update_campaign_status($campaign);
                if ($result['message']) {
                    echo '<p>'.$result['message'].'</p>'; exit;
                }
            }
            echo '<p> Campaign '.$campaign['name'].' turned on until '.$new_date.'</p>'; exit;
        } else {
            echo '<p> Campaign end date criteria didn`t update </p>'; exit;
        }
    }
    public function edit_start_date()
    {
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        $data = $this->input->post();
        $id = $data['campaign_id'];
        $campaign = $this->V2_master_campaign_model->get_by_id($this->userid, $id);
        if($campaign['campaign_status'] == "COMPLETED") {
            print json_encode(array("status"=>"ERROR", "message" => 'You can not edit COMPLETED campaign'));
            exit;
        }
        $type = 'start_date';
        $rules = array(
            array('field' => 'campaign_id', 'label' => 'Campaign id', 'rules' => 'required|numeric'),
            array('field' => 'campaign_start_datetime', 'label' => 'Start date', 'rules' => 'required'),
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE) {
            print json_encode(array("status" => "ERROR", "message" => validation_errors()));
            exit;
        }
        $new_date = date("Y-m-d H:i:s", strtotime($data['campaign_start_datetime']));
        // call validate for date time
        $update = array('campaign_start_datetime' => $new_date);
        $update_live = false;
        //check if campaign converted to live then make changes in network too
        if (strtotime('now')<strtotime($data['campaign_start_datetime'])) {
            //change campaign status to schedule for future camps
            $update['campaign_status'] = 'SCHEDULED';
            $campaign['campaign_status'] = 'SCHEDULED';
            if($campaign['campaign_status'] == "ACTIVE" && $campaign['campaign_is_converted_to_live'] == 'Y' ) {
                //check if campaign converted to live then make changes in network too
                $update['network_campaign_status'] = 'PAUSED';
                $campaign['network_campaign_status'] = 'PAUSED';
                $update_live = true;
            }
        }
        $updated = $this->V2_master_campaign_model->update($id, $update);
        if(!$updated){
            print json_encode(array("status" => "ERROR", "message" => "Campaign end date criteria didn't update"));
            exit;
        }
        // save into log table
        $this->load->model('V2_log_model');
        $this->V2_log_model->create($id, $new_date, $type);
        if ($update_live) {
            //check if campaign converted to live then make PAUSE in network
            $this->load->model('Common_model');
            $result = $this->Common_model->update_campaign_status($campaign);
            if ($result['message']) {
                echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                exit;
            }
        }
        print json_encode(array("status" => "SUCCESS", "message" => "Campaign start date criteria successfully updated"));
        exit;
    }
    public function edit_campaign_name()
    {
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        $data = $this->input->post();
        if( ( !$data['pk'] || !is_numeric($data['pk']) ) || ( empty($data['value']) ) )  {
            print json_encode(array("status"=>"ERROR", "message" => 'Can not find campaign or name is empty'));
            exit;
        }
        $id = $data['pk'];
        $name = $data['value'];
        $type = 'name';
        $updated = $this->V2_master_campaign_model->update($id, array('name' => $name));
        // save into log table
        if ($updated) {
            $this->load->model('V2_log_model');
            $campaign = $this->V2_master_campaign_model->get_by_id($this->userid, $id);
            $this->V2_log_model->create($id, 'campaign name was changed to '.$name, $type);
            print json_encode(array("status" => "SUCCESS", "message" => "Campaign name successfully updated"));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Campaign name didn't update"));
            exit;
        }
    }
    public function edit_campaign_categories()
    {
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        $data = $this->input->post();
        if( ( !$data['campaign_id'] || !is_numeric($data['campaign_id']) ) || ( empty($data['verticals']) ) )  {
            die(json_encode(array("status"=>"ERROR", "message" => 'Can not find campaign or vertical is empty')));
        }
        $verticals = $data['verticals'];
        $is_ok = $this->v2_campaign_category_model->update_campaign_categories_assocation($data['campaign_id'], $verticals);
        if ( $is_ok ) {
            die(json_encode(array("status" => "SUCCESS", "message" => "Campaign verticals successfully updated")));
        } else {
            die(json_encode(array("status" => "SUCCESS", "message" => "Campaign verticals didn't updated")));
        }
    }
    public function edit_campaign_retargeting_ips()
    {
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        $data = $this->input->post();
        if( empty($data['campaign_id']) || empty($data['ip_targeting_ips_json']) )  {
            die(json_encode(array("status"=>"ERROR", "message" => 'Can not find campaign or Retargeting IPs are empty')));
        }
        $retargeting_ips_data = $data['ip_targeting_ips_json'];
        $res = $this->validate_retargeting_ip_file($retargeting_ips_data, $return = true);
        $retargeting_ips = [];
        // If All IP addresses are not VALID
        if ( !$res['all_valid'] ) {
            $invalid_ips = implode(', ', array_column($res['invalids'], 'ip'));
            die(json_encode(array("status" => "ERROR", "message" => $invalid_ips . ' are INVALID IPs')));
        }
        // All Retargeting IPs are valid
        else {
            foreach ( $retargeting_ips_data as $ip ) {
                $retargeting_ips[] = $this->ip_address->get_ip_range($ip);
            }
        }
        $is_ok = $this->v2_retargeting_ip_model->update_retargeting_ips_assocation($data['campaign_id'], $retargeting_ips);
        if ( $is_ok ) {
            die(json_encode(array("status" => "SUCCESS", "message" => "Campaign Retargeting IPs successfully updated")));
        }
        die(json_encode(array("status" => "SUCCESS", "message" => "Campaign Retargeting IPs didn't updated")));
    }
    public function edit_max_budget()
    {
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        //var_dump($this->input->post()); exit;
        $max_budget = $this->input->post('max_budget');
        $id = $this->input->post('id');
        $active = $this->V2_master_campaign_model->campaign_is_active($id);
        $campaign = $this->V2_master_campaign_model->get_by_id($this->userid, $id);
        if(!$active) {
            print json_encode(array("status"=>"ERROR", "message" => 'You can not edit PAUSED campaign'));
            exit;
        }
        if($this->user['is_admin'] && $campaign['userid'] != $this->user['id']) {
            $this->load->model('V2_users_model');
            $this->user = $this->V2_users_model->get_by_id($campaign['userid']);
        }
        $campaign_budget = $max_budget;
        if($this->user['is_billing_type'] == 'PERCENTAGE') {
            // for percentage users we need to calculate percentage budget
            if ($campaign["max_budget"]) {
                $values['max_budget'] = $max_budget;
                $new_percentage_max_budget = $max_budget * $this->user['budget_percentage'] / 100;
                $values['percentage_max_budget'] = $new_percentage_max_budget;
                $message[] = " WE ADD $campaign_budget ADDITIONAL BUDGET FOR YOUR CAMPAIGN";
                $budget_percent = ($max_budget - $campaign['max_budget']) * 100 / $campaign['max_budget'];
            } else {
                $values['max_budget'] = $max_budget;
                $new_percentage_max_budget = $max_budget * $this->user['budget_percentage'] / 100;
                $values['percentage_max_budget'] = $new_percentage_max_budget;
                $message[] = " WE ADD $campaign_budget ADDITIONAL BUDGET FOR YOUR CAMPAIGN";
                $budget_percent = 0;
            }
            if($budget_percent > 0) {
                if ($campaign["max_clicks"]) {
                    //calculate how clicks we need to add for campaign
                    $clisks_count = $campaign["max_clicks"] * $budget_percent / 100;
                    $clisks_count = round($clisks_count);
                    $values['max_clicks'] = $campaign["max_clicks"] + $clisks_count;
                    $message[] = " WE ADD $clisks_count ADDITIONAL CLICKS FOR YOUR CAMPAIGN";
                }
                if ($campaign["max_impressions"]) {
                    //calculate how clicks we need to add for campaign
                    $imp_count = $campaign["max_impressions"] * $budget_percent / 100;
                    $imp_count = round($imp_count);
                    $values['max_impressions'] = $campaign["max_impressions"] + $imp_count;
                    $message[] = " WE ADD $imp_count ADDITIONAL IMPRESSIONS FOR YOUR CAMPAIGN";
                }
            }
//            else {
//
//                print json_encode(array("status" => "ERROR", "message" => "Campaign budget didn't update"));
//                exit;
//
//            }
        } else {
            $values['max_budget'] = $max_budget;
            $new_percentage_max_budget = $campaign_budget * $this->user['flat_percentage_budget'] / 100;
            $values['percentage_max_budget'] = $new_percentage_max_budget;
            if ($campaign["max_clicks"]) {
                //calculate how clicks we need to add for campaign
                $tier = 'display_click_'.$campaign['campaign_tier'];
                $clisks_count = $campaign_budget/$this->user[$tier];
                $clisks_count = round($clisks_count);
                $values['max_clicks'] = $clisks_count;
                $message[] = " WE ADD $clisks_count ADDITIONAL CLICKS FOR YOUR CAMPAIGN";
            } else if ($campaign["max_impressions"]) {
                //calculate how clicks we need to add for campaign
                $tier = 'display_imp_'.$campaign['campaign_tier'];
                $imp_count = $campaign_budget / $this->user[$tier] * 1000;
                $imp_count = round($imp_count);
                $values['max_impressions'] = $imp_count;
                $message[] = " WE ADD $imp_count ADDITIONAL IMPRESSIONS FOR YOUR CAMPAIGN";
            }
//            else {
//
//                print json_encode(array("status" => "ERROR", "message" => "Campaign budget didn't update flat"));
//                exit;
//
//            }
        }
        //var_dump($values);
        $full_message = 'Campaign Budget successfully updated. '.implode(',',$message);
        // update campaign clicks or impr count
        $updated = $this->V2_master_campaign_model->update($id, $values);
        $type = 'max_budget';
        //$updated = $this->V2_master_campaign_model->update($id, array('max_budget' => $max_budget));
        // save into log table
        //check if campaign converted to live then make changes in network too
        if ($updated) {
            $this->load->model('V2_log_model');
            $this->V2_log_model->create($id, $max_budget, $type);
            print json_encode(array("status" => "SUCCESS", "message" => $full_message, "max_budget" => sprintf("%.2f", $max_budget)));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Campaign end date criteria didn't update"));
            exit;
        }
    }
    public function edit_max_clicks()
    {
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        //var_dump($this->input->post()); exit;
        $max_clicks = $this->input->post('max_clicks');
        $id = $this->input->post('id');
        $active = $this->V2_master_campaign_model->campaign_is_active($id);
        if(!$active) {
            print json_encode(array("status"=>"ERROR", "message" => 'You can not edit PAUSED campaign'));
            exit;
        }
        $type = 'max_clicks';
        $updated = $this->V2_master_campaign_model->update($id, array('max_clicks' => $max_clicks));
        // save into log table
        //check if campaign converted to live then make changes in network too
        if ($updated) {
            $this->load->model('V2_log_model');
            //$campaign = $this->V2_master_campaign_model->get_by_id($this->userid, $id);
            $this->V2_log_model->create($id, $max_clicks, $type);
            print json_encode(array("status" => "SUCCESS", "message" => "Campaign end date criteria successfully updated", "max_clicks" => $max_clicks));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Campaign end date criteria didn't update"));
            exit;
        }
    }
    public function edit_max_impressions()
    {
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        //var_dump($this->input->post()); exit;
        $max_impressions = $this->input->post('max_impressions');
        $id = $this->input->post('id');
        $active = $this->V2_master_campaign_model->campaign_is_active($id);
        if(!$active) {
            print json_encode(array("status"=>"ERROR", "message" => 'You can not edit PAUSED campaign'));
            exit;
        }
        $type = 'max_clicks';
        $updated = $this->V2_master_campaign_model->update($id, array('max_impressions' => $max_impressions));
        // save into log table
        //check if campaign converted to live then make changes in network too
        if ($updated) {
            $this->load->model('V2_log_model');
            //$campaign = $this->V2_master_campaign_model->get_by_id($this->userid, $id);
            $this->V2_log_model->create($id, $max_impressions, $type);
            print json_encode(array("status" => "SUCCESS", "message" => "Campaign end date criteria successfully updated", "max_impressions" => $max_impressions));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Campaign end date criteria didn't update"));
            exit;
        }
    }
    public function edit_keywords()
    {
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        //var_dump($this->input->post()); exit;
        $keywords = $this->input->post('keywords');
        $keywords_list = implode(',',$keywords);
        $id = $this->input->post('campaign_id');
//        $active = $this->V2_master_campaign_model->campaign_is_active($id);
//        if(!$active) {
//            print json_encode(array("status"=>"ERROR", "message" => 'You can not edit PAUSED campaign'));
//            exit;
//        }
        $type = 'keywords';
        $updated = $this->V2_master_campaign_model->update($id, array('keywords' => $keywords_list));
        // save into log table
        //check if campaign converted to live then make changes in network too
        if ($updated) {
            $this->load->model('V2_log_model');
            $campaign = $this->V2_master_campaign_model->get_by_id($this->userid, $id);
            $this->V2_log_model->create($id, $keywords_list, $type);
            if ($campaign['campaign_is_converted_to_live']=="Y" && $campaign['network_campaign_status'] == "ACTIVE") {
                $this->load->model('Common_model');
                $result = $this->Common_model->update_keywords($campaign);
                if ($result['message']) {
                    echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                    exit;
                }
            }
            print json_encode(array("status" => "SUCCESS", "message" => "Campaign keywords successfully updated"));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Campaign keywords didn't update"));
            exit;
        }
    }
    public function edit_link()
    {
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        $data = $this->input->post();
        $type = 'link';
        $rules = array(
            array('field' => 'link_id', 'label' => 'Link id', 'rules' => 'required|numeric'),
            array('field' => 'destination_url', 'label' => 'Destination url', 'rules' => 'required'),
            array('field' => 'max_clicks', 'label' => 'Max clicks', 'rules' => 'required|numeric|max_length[9]'),
            //array('field' => 'old_max_clicks', 'label' => 'Old Max clicks', 'rules' => 'required|numeric|max_length[9]'),
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE) {
            print json_encode(array("status" => "ERROR", "message" => validation_errors()));
            exit;
        }
        // we need to validate post data
        $id = $data['link_id'];
        $this->load->model('V2_ads_link_model');
        $data_for_update = array('destination_url' => $data['destination_url'], 'max_clicks' => $data['max_clicks']);
        $old_ad = $this->V2_ads_link_model->get_by_id($id);
        if ($old_ad['max_clicks'] < $data['max_clicks']) {
            $data_for_update['is_fulfilled'] = "N";
        }
        $updated = $this->V2_ads_link_model->update($id, $data_for_update);
        // save into log table
        //check if campaign converted to live then make changes in network too
        if ($updated) {
            // save cahnges in log table
            //$this->load->model('V2_log_model');
            // generate message and save it in log table
            //$this->V2_log_model->create($id, $data['destination_url'], $type);
            print json_encode(array("status" => "SUCCESS", "message" => "Campaign link successfully updated"));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Campaign link didn't update"));
            exit;
        }
    }
    public function edit_ad_status()
    {
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        $data = $this->input->post();
/*
        $campaign_id = $data['campaign_id']; var_dump($campaign_id);
        $active = $this->V2_master_campaign_model->campaign_is_active($campaign_id);var_dump($active);exit;
        if(!$active) {
            print json_encode(array("status"=>"ERROR", "message" => 'You can not edit PAUSED campaign'));
            exit;
        }
*/
        $type = 'ad_status';
//        $rules = array(
//            array('field' => 'campaign_id', 'label' => 'Campaign id', 'rules' => 'required|numeric'),
//            array('field' => 'budget', 'label' => 'Budget', 'rules' => 'required|numeric|max_length[9]'),
//        );
//
//        $this->form_validation->set_rules($rules);
//
//        if($this->form_validation->run() == FALSE) {
//
//            print json_encode(array("status"=>"ERROR", "message" => validation_errors()));
//            exit;
//        }
        $id = $data['ad_id'];
        // need to calculate old budget + new budget
        $updated = $this->V2_ad_model->update($id, array('creative_status' => $data['status']));
//        // save into log table
//        //check if campaign converted to live then make changes in network too
//        //if()
        if ($updated) {
            $ad = $this->V2_ad_model->get_with_network_name_by_id($id);
            if($ad['is_multiple']=='Y') {
                $this->load->model('V2_multiple_ad_model');
                $multiple_update = $this->V2_multiple_ad_model->update_by_ad_id($id,array('creative_status' => $data['status']));
                if(!$multiple_update) {
                    print json_encode(array("status" => "ERROR", "message" => "Campaign ad status didn't update"));
                    exit;
                }
            }
            if ($ad['creative_is_active'] == 'Y') {
                $this->load->model('Common_model');
                $result = $this->Common_model->update_ad_status($ad, $ad['network_name']);
                if ($result['message']) {
                    echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                    exit;
                }
                if($ad['is_multiple']=='Y') {
                    $result = $this->Common_model->update_ad_status($ad, 'GOOGLE');
                    if ($result['message']) {
                        echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                        exit;
                    }
                }
            }
            print json_encode(array("status" => "SUCCESS", "message" => "Campaign ad status successfully updated"));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Campaign ad status didn't update"));
            exit;
        }
    }
    public function edit_ad()
    {
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        $ad_json = $this->input->post('ad');
        $ad = json_decode($ad_json, true);
        //var_dump($ad); exit;
        $campaign_id = $ad['campaign_id'];
//        $active = $this->V2_master_campaign_model->campaign_is_active($campaign_id);
//        if(!$active) {
//            print json_encode(array("status"=>"ERROR", "message" => 'You can not edit PAUSED campaign'));
//            exit;
//        }
        $campaign = $this->V2_master_campaign_model->get_all_with_network_by_id($this->userid, $campaign_id);
        // we need to get with group id
        if ($campaign['campaign_is_converted_to_live']=="Y" && $campaign['network_campaign_status'] != "ACTIVE" && $campaign['campaign_status'] != 'SCHEDULED'){
            print json_encode(array("status"=>"ERROR", "message" => 'You can not edit PAUSED campaign'));
            exit;
        }
        //create campaign ads
        $this->load->model('V2_ad_model');
        $this->load->model('V2_ads_link_model');
        //upload images
        if(($ad['creative_type']=='VIDEO' || $ad['creative_type']=='VIDEO-CLICKS' || $ad['creative_type']=='VIDEO_YAHOO') && $ad['video_url']){
            $result = $this->move_uploaded_tmp_video($ad['video_url']);
            if (count($result['messages'])) {
                $messages = '';
                foreach ($result['messages'] as $message) {
                    $messages .= ' ' . $message;
                }
                $this->db->trans_rollback();
                // get messages texts and send json
                print json_encode(array("status" => "ERROR", "message" => $messages));
                exit;
            }
            $ad['video_url'] = $this->config->base_url() . $result['new_url'];
            $image_result = $this->get_image_from_video($result['new_url'], $ad['creative_type']=='VIDEO_YAHOO');
            if($ad['creative_type']=='VIDEO_YAHOO') {
                $ad['square_creative_url'] = $this->config->base_url() . $image_result['new_square_url'];
            }
            if (count($image_result['messages'])) {
                $messages = '';
                foreach ($image_result['messages'] as $message) {
                    $messages .= ' ' . $message;
                }
                // get messages texts and send json
                print json_encode(array("status" => "ERROR", "message" => $messages));
                exit;
            }
            $movie = new FFmpegMovie($result['new_url']);
            $movie_dureation_sec = $movie->getDuration();
            $movie_dureation_min = gmdate("i:s", $movie_dureation_sec);
            $ad['video_duration'] = $movie_dureation_min;
            $ad['creative_url'] = $this->config->base_url() . $image_result['new_url'];
        } else if ($ad['creative_url']) {
            $result = $this->move_uploaded_tmp_image($ad['creative_url']);
            if (count($result['messages'])) {
                $messages = '';
                foreach ($result['messages'] as $message) {
                    $messages .= ' ' . $message;
                }
                //$this->db->trans_rollback();
                // get messages texts and send json
                print json_encode(array("status" => "ERROR", "message" => $messages));
                exit;
            }
            $ad['creative_url'] = $this->config->base_url() . $result['new_url'];
        }
        $result = $this->V2_ad_model->validate($ad, true);
        if (count($result['messages'])) {
            $messages = '';
            foreach ($result['messages'] as $message) {
                $messages .= ' ' . $message;
            }
            // $this->db->trans_rollback();
            // get messages texts and send json
            print json_encode(array("status" => "ERROR", "message" => $messages));
            exit;
        }
        if($campaign['network_name']=='GOOGLE') {
            $result['valide_ad']['approval_status'] = 'UNCHECKED';
            $result['valide_ad']['disapproval_reasons'] = '';
        }
        //var_dump($result['valide_ad'], 777); exit;
        $ad_id = $this->V2_ad_model->update($ad['ad_id'], $result['valide_ad']);
        if ($ad_id) {
            if($campaign['is_multiple']=='Y') {
                $this->load->model('V2_multiple_ad_model');
                // reset multiple ad network data
                $multiple_update = $this->V2_multiple_ad_model->reset_network_data_by_ad_id($ad_id);
                if(!$multiple_update) {
                    print json_encode(array("status" => "ERROR", "message" => "Campaign ad status didn't update"));
                    exit;
                }
            }
            if(!empty($ad['tracking_url'])){
                $url = $ad['tracking_url'];
            $url = str_replace("prodataretargeting.com", "reporting.prodata.media", $url);
            $url = str_replace("www.", "", $url);
            } else {
                $url = $ad['destination_url'];
            }
            $link_id = $this->V2_ads_link_model->update($ad['ad_link_id'], array('destination_url' => $url));
            if (!$link_id) {
                // send error message and rollback
                //$this->db->trans_rollback();
                print json_encode(array("status" => "ERROR", "message" => "We can't edit ad links. Please try again"));
                exit;
            }
        } else {
            // send error message and rollback
            //$this->db->trans_rollback();
            print json_encode(array("status" => "ERROR", "message" => "We can't edit ads. Please try again"));
            exit;
        }
        //}
        $this->load->model('Common_model');
        $result = $this->Common_model->update_ad($campaign, $ad['ad_id']);
        if ($result['message']) {
            echo json_encode(array("status" => "ERROR", "message" => $result['message']));
            exit;
        }
        if($campaign['is_multiple']=='Y') {
            $campaign['network_name'] = 'GOOGLE';
            $result = $this->Common_model->update_ad($campaign, $ad['ad_id']);
            if ($result['message']) {
                echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                exit;
            }
        }
        print json_encode(array("status" => "SUCCESS", "message" => "AD is edited"));
        exit;
    }

    public function create_ad()
    {
        $ad_json = $this->input->post('ad');
        $ad = json_decode($ad_json, true);
        $campaign_id = $ad['campaign_id'];

        if($this->user['is_admin']) {
            $this->userid = null;
        }
        //$multiple_campaign_id = $ad['multiple_campaign_id'];
        $campaign = $this->V2_master_campaign_model->get_all_with_network_by_id($this->userid, $campaign_id);
        // we need to get with group id
        if ($campaign['campaign_is_converted_to_live']=="Y" && $campaign['network_campaign_status'] != "ACTIVE" && $campaign['campaign_status'] != 'SCHEDULED'){
            print json_encode(array("status"=>"ERROR", "message" => 'You can not edit PAUSED campaign'));
            exit;
        }
        //create campaign ads
        $this->load->model('V2_ad_model');
        $this->load->model('V2_ads_link_model');
        $destination_url = $this->V2_master_campaign_model->generate_destination_url($campaign['id'], $campaign['domain'], $campaign['campaign_type']);
        //foreach ($data['ads'] as $ad_json) {
        // initialize group id in ads if network is google and group is created
        if ($campaign['campaign_is_converted_to_live'] == 'Y') {
            if ($campaign['group_id']) {
                $ad['group_id'] = $campaign['group_id'];
                $ad['network_group_id'] = $campaign['network_group_id'];
            }
            $ad['network_campaign_id'] = $campaign['network_campaign_id'];
        }
        $ad['creative_name'] = $campaign['name'] . ' ' . uniqid();
        $ad['network_id'] = $campaign['network_id'];
        //$ad['network_id'] = $campaign['network_id'];
        //$ad['campaign_id'] = $campaign['id'];
        //var_dump($ad); exit;
        //upload images
        if(($ad['creative_type']=='VIDEO' || $ad['creative_type']=='VIDEO-CLICKS' || $ad['creative_type']=='VIDEO_YAHOO') && $ad['video_url']){
            $result = $this->move_uploaded_tmp_video($ad['video_url']);
            if (count($result['messages'])) {
                $messages = '';
                foreach ($result['messages'] as $message) {
                    $messages .= ' ' . $message;
                }
                $this->db->trans_rollback();
                // get messages texts and send json
                print json_encode(array("status" => "ERROR", "message" => $messages));
                exit;
            }
            $ad['video_url'] = $this->config->base_url() . $result['new_url'];
            $image_result = $this->get_image_from_video($result['new_url'],$ad['creative_type']=='VIDEO_YAHOO');
            if($ad['creative_type']=='VIDEO_YAHOO') {
                $ad['square_creative_url'] = $this->config->base_url() . $image_result['new_square_url'];
            }
            if (count($image_result['messages'])) {
                $messages = '';
                foreach ($image_result['messages'] as $message) {
                    $messages .= ' ' . $message;
                }
                // get messages texts and send json
                print json_encode(array("status" => "ERROR", "message" => $messages));
                exit;
            }
            $movie = new FFmpegMovie($result['new_url']);
            $movie_dureation_sec = $movie->getDuration();
            $movie_dureation_min = gmdate("i:s", $movie_dureation_sec);
            $ad['video_duration'] = $movie_dureation_min;
            $ad['creative_url'] = $this->config->base_url() . $image_result['new_url'];
        } else if ($ad['creative_url']) {
            $result = $this->move_uploaded_tmp_image($ad['creative_url']);
            if (count($result['messages'])) {
                $messages = '';
                foreach ($result['messages'] as $message) {
                    $messages .= ' ' . $message;
                }
                //$this->db->trans_rollback();
                // get messages texts and send json
                print json_encode(array("status" => "ERROR", "message" => $messages));
                exit;
            }
            $ad['creative_url'] = $this->config->base_url() . $result['new_url'];
        }
//        $campaign_budget = $campaign['budget'];
        if ($campaign['network_name'] == "FIQ" && $campaign['campaign_type'] == "TEXTAD") {
            $ads = $this->V2_ad_model->get_ads_by_campaign_id($campaign['id']);
            $daily_cap = $campaign['budget']/(count($ads)+1);
            $this->V2_ad_model->update_all_by_campaign_id($campaign['id'], ['daily_cap' => $daily_cap]);
            $ad['bid'] = $campaign['bid'];
            $ad['creative_status'] = 'ACTIVE';
            $ad['approval_status'] = 'ACTIVE';
            $ad['daily_cap'] = $daily_cap;
            $ad['keywords'] = 'RON';
        }
        $result = $this->V2_ad_model->validate($ad);
        if (count($result['messages'])) {
            $messages = '';
            foreach ($result['messages'] as $message) {
                $messages .= ' ' . $message;
            }
            // $this->db->trans_rollback();
            // get messages texts and send json
            print json_encode(array("status" => "ERROR", "message" => $messages));
            exit;
        }
        if($campaign['is_multiple']=='Y') {
            $result['valide_ad']['is_multiple'] = 'Y';
        }
        $ad_id = $this->V2_ad_model->create($result['valide_ad'], $destination_url);
        if ($ad_id) {

            /**
             * Create an entry to Google AdX
             */
            $ad['id'] = $ad_id;
            $campaign['destination_url'] = $destination_url;
            $campaign['id'] = $campaign_id;
            $creative = $this->v2_google_adx_model->insert_creative([
                'campaign' => $campaign,
                'ad' => $ad
            ]);
            // Google AdX entry end $!!

            if($campaign['is_multiple']=='Y') {
                $this->load->model('V2_multiple_ad_model');
                $multiple_campaign = $this->V2_multiple_ad_model->get_by_campaign_id($campaign_id);
                $multiple_ad_id = $this->V2_multiple_ad_model->create(
                    $result['valide_ad'],
                    $multiple_campaign['id'],
                    $multiple_campaign['group_id'],
                    $ad_id,
                    $multiple_campaign['network_campaign_id']
                );
            }
            $link_id = $this->V2_ads_link_model->create($result['valide_ad'], $campaign_id, $ad_id);
            if (!$link_id) {
                // send error message and rollback
                // $this->db->trans_rollback();
                print json_encode(array("status" => "ERROR", "message" => "We can't create ad links. Please try again"));
                exit;
            }
        } else {
            // send error message and rollback
            // $this->db->trans_rollback();
            print json_encode(array("status" => "ERROR", "message" => "We can't create ads. Please try again"));
            exit;
        }
        //}
        $this->load->model('Common_model');
        $result = $this->Common_model->create_ad($campaign, $ad_id); //var_dump($result); exit;
        if ($result['message']) {
            echo json_encode(array("status" => "ERROR", "message" => $result['message']));
            exit;
        }
        if($campaign['is_multiple']=='Y') {
            $campaign['network_name'] = 'GOOGLE';
            $result = $this->Common_model->create_ad($campaign, $ad_id); //var_dump($result); exit;
            if ($result['message']) {
                echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                exit;
            }
        }
        print json_encode(array("status" => "SUCCESS", "message" => "AD is created"));
        exit;
    }
    public function pagination_list($total_count, $offset, $segment,$base_url,$url_string = null) {
        $config['base_url'] = $base_url;
        $config['total_rows'] = $total_count;
        if ($url_string) {
            $config['suffix'] = '?' . $url_string;
        }
        $config['per_page'] = $offset;
        $config['uri_segment'] = $segment;
        $config['full_tag_open'] = '<nav class="theme-report-pagination-nav" role="nav"><ul class="pagination theme-paginaton theme-display-table" role="menu">';
        $config['full_tag_close'] = '</ul></nav><!--pagination-->';
        $config['first_link'] = '&laquo; First';
        $config['first_tag_open'] = '<li class="prev page end_page">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last &raquo;';
        $config['last_tag_open'] = '<li class="next page end_page">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = '&raquo;';
        $config['next_tag_open'] = '<li class="next page">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo;';
        $config['prev_tag_open'] = '<li class="prev page">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="numbers page">';
        $config['num_tag_close'] = '</li>';
        $this->pagination->initialize($config);
    }

    protected function createAjaxPagination($total, $page = 1, $per_page = 10)
        {
            $start = 1;
            $visiblePagesCnt = 5;
            $pagesCnt = ceil($total/$per_page);
            //var_dump($pagesCnt);
            $end = $pagesCnt < $visiblePagesCnt ? $pagesCnt : $visiblePagesCnt;
            $prev = $page - 1;
            $next = $page + 1;
            $pages = "";
            if ($pagesCnt > 1) {
                if ($page > 1 && $pagesCnt > $visiblePagesCnt) {
                    $pages.= "<li><a href='#' data-page='1' style='width:50px'>FIRST</a></li>";
                    $pages.= "<li><a href='#' data-page='$prev'> << </a></li>";
                }
                if($page > floor($visiblePagesCnt/2) && $pagesCnt > $visiblePagesCnt) {
                    if($page <= $pagesCnt - floor($visiblePagesCnt/2)) {
                        $start = $page - floor($visiblePagesCnt/2);
                        $end = $page + floor($visiblePagesCnt/2);
                    } else {
                        $start = $pagesCnt - ($visiblePagesCnt - 1);
                        $end = $pagesCnt;
                    }
                }
                for($counter = $start; $counter <= $end; $counter++) {
                    if ($counter == $page) {
                        $pages.= "<li class='active'><a>$counter</a></li>";
                    } else {
                        $pages.= "<li><a href='#' data-page='$counter'>$counter</a></li>";
                    }
                }
                if ($page < $pagesCnt && $pagesCnt > $visiblePagesCnt) {
                    $pages.= "<li><a href='#' data-page='$next'> >> </a></li>";
                    $pages.= "<li><a href='#' data-page='$pagesCnt' style='width:50px'>LAST</a></li>";
                }
            }
            return '<nav class="theme-report-pagination-nav" role="nav"><ul class="pagination theme-paginaton theme-display-table" role="menu">' . $pages . '</ul></nav>';
        }
    public function require_auth()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        } else {
            if ($this->ion_auth->is_admin())
                $this->viewArray['manage_users'] = true;
            else
                $this->viewArray['manage_users'] = false;
            $this->viewArray['show_top_menu'] = true;
        }
    }
    public function get_states_list($country = "")
    {
        $this->Country_model->country = $country;
        print json_encode(array("status" => "SUCCESS", "states" => $this->Country_model->get_states()));
    }

    // create_campaign
    public function create_campaign()
    {
        $should_create_google_adx = false;

         $rules = array(
            array('field' => 'io', 'label' => 'IO', 'rules' => 'required|alpha_dash'),
            array('field' => 'campaign_type', 'label' => 'Campaign type', 'rules' => 'required'),
            array('field' => 'name', 'label' => 'Campaign name', 'rules' => 'required'),
            array('field' => 'campaign_start_datetime', 'label' => 'Campaign start date', 'rules' => 'required'),
            array('field' => 'domain', 'label' => 'Domain', 'rules' => 'required'),
            array('field' => 'geotype', 'label' => 'Geo-Location Type', 'rules' => 'required'),
            );
        if ($this->input->post('campaign_type') == 'EMAIL') {
            $rules[] = array('field' => 'ppc_links', 'label' => 'links', 'rules' => 'required');
            $rules[] = array('field' => 'message_result', 'label' => 'Message result', 'rules' => 'required');
            $fire_open_pixel = $this->input->post('fire_open_pixel');
            if ($fire_open_pixel && $fire_open_pixel == "Y") {
                $rules[] = array('field' => 'open_pixel_src', 'label' => 'image pixel', 'rules' => 'required');
            }
        }else if($this->input->post('campaign_network') != 'FACEBOOK'){
            $rules[] = array('field' => 'vertical', 'label' => 'Campaign vertical', 'rules' => 'required');
        }else{
            if($this->input->post('budget')){
                $rules[] = array('field' => 'budget', 'label' => 'Budget', 'rules' => 'required|numeric|max_length[9]');
            }
            $rules[] = array('field' => 'ads[]', 'label' => 'ADS', 'rules' => 'required');
        }
        $more_options = $this->input->post('more_options');
        if ($more_options ) {
            if($more_options == 'Y') {
                if (empty($this->input->post('max_clicks')) && empty($this->input->post('max_impressions')) && empty($this->input->post('campaign_end_datetime'))) {
                    $rules[] = array('field' => 'max_budget', 'label' => 'Max budget', 'rules' => 'required|numeric|max_length[9]');
                    $rules[] = array('field' => 'max_clicks', 'label' => 'Max clicks', 'rules' => 'numeric|max_length[9]');
                    $rules[] = array('field' => 'max_impressions', 'label' => 'Max Impressions', 'rules' => 'numeric|max_length[9]');
                }
                if (empty($this->input->post('max_clicks')) && empty($this->input->post('max_budget')) && empty($this->input->post('campaign_end_datetime'))) {
                    $rules[] = array('field' => 'max_budget', 'label' => 'Max budget', 'rules' => 'numeric|max_length[9]');
                    $rules[] = array('field' => 'max_clicks', 'label' => 'Max clicks', 'rules' => 'numeric|max_length[9]');
                    $rules[] = array('field' => 'max_impressions', 'label' => 'Max Impressions', 'rules' => 'required|numeric|max_length[9]');
                }
                if (empty($this->input->post('max_impressions')) && empty($this->input->post('max_budget')) && empty($this->input->post('campaign_end_datetime'))) {
                    $rules[] = array('field' => 'max_budget', 'label' => 'Max budget', 'rules' => 'numeric|max_length[9]');
                    $rules[] = array('field' => 'max_clicks', 'label' => 'Max clicks', 'rules' => 'required|numeric|max_length[9]');
                    $rules[] = array('field' => 'max_impressions', 'label' => 'Max Impressions', 'rules' => 'numeric|max_length[9]');
                }
                if (empty($this->input->post('max_impressions')) && empty($this->input->post('max_budget')) && empty($this->input->post('max_clicks'))) {
                    $rules[] = array('field' => 'campaign_end_datetime', 'label' => 'Campaign end date', 'rules' => 'required');
                    $rules[] = array('field' => 'max_budget', 'label' => 'Max budget', 'rules' => 'numeric|max_length[9]');
                    $rules[] = array('field' => 'max_clicks', 'label' => 'Max clicks', 'rules' => 'numeric|max_length[9]');
                    $rules[] = array('field' => 'max_impressions', 'label' => 'Max Impressions', 'rules' => 'numeric|max_length[9]');
                }
            }
        } elseif($this->user['is_billing_type'] == 'FLAT') {
            $rules[] = array('field' => 'max_budget', 'label' => 'Max budget', 'rules' => 'required|numeric|max_length[9]');
            $rules[] = array('field' => 'campaign_end_datetime', 'label' => 'Campaign end date', 'rules' => 'required');
            if (empty($this->input->post('max_clicks'))) {
                $rules[] = array('field' => 'max_clicks', 'label' => 'Max clicks', 'rules' => 'numeric|max_length[9]');
                $rules[] = array('field' => 'max_impressions', 'label' => 'Max Impressions', 'rules' => 'required|numeric|max_length[9]');
            } else {
                $rules[] = array('field' => 'max_clicks', 'label' => 'Max clicks', 'rules' => 'required|numeric|max_length[9]');
                $rules[] = array('field' => 'max_impressions', 'label' => 'Max Impressions', 'rules' => 'numeric|max_length[9]');
            }
        }
        $geo_type = $this->input->post('geotype');
        if ($geo_type == 'country') {
            $rules[] = array('field' => 'country', 'label' => 'Country', 'rules' => 'required|max_length[2]');
        } elseif ($geo_type == 'state') {
            $rules[] = array('field' => 'country', 'label' => 'Country', 'rules' => 'required|max_length[2]');
            $rules[] = array('field' => 'state[]', 'label' => 'States', 'rules' => 'required');
        } else {
            $rules[] = array('field' => 'zip', 'label' => 'Postal code', 'rules' => 'required');
            $rules[] = array('field' => 'radius', 'label' => 'Radius', 'rules' => 'required|numeric|min_length[2]');
        }
        $remarketing_options = $this->input->post('is_remarketing');
        if ($remarketing_options && $remarketing_options == 'Y') {
            $rules[] = array('field' => 'is_remarketing_io', 'label' => 'Remarketing IO', 'rules' => 'min_length[1]|max_length[1]|required');
            $rules[] = array('field' => 'remarketing_io[]', 'label' => 'Remarketing io', 'rules' => 'required');
        }

        // IO based retargeting
        $is_io_based_retargeting = $this->input->post('is_io_based_retargeting');
        if ($is_io_based_retargeting && $is_io_based_retargeting == 'Y') {
            $rules[] = array('field' => 'is_io_based_retargeting', 'label' => 'Retargeting Campaign IO', 'rules' => 'min_length[1]|max_length[1]|required');
            $rules[] = array('field' => 'io_based_retargeting_ios[]', 'label' => 'Retargeting Campaign IO(s)', 'rules' => 'required');
        }

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE) {
            print json_encode(array("status" => "ERROR", "message" => validation_errors()));
            exit;
        }
        $campaign_data = $this->input->post();
        $group_id = false;
        // filter out the non-alpha numeric stuff (spaces too)
        $campaign_data['io'] = preg_replace("/[^A-Za-z0-9 ]/", '', $campaign_data['io']);
        // detect campaign type and network
        $this->load->model('V2_network_model'); //var_dump($this->userid,$campaign_data['campaign_type']); exit;
        //var_dump($campaign_data['campaign_type']);die;

        $network = $this->V2_network_model->get_all_by_user_id_and_campaign_type($this->userid, $campaign_data['campaign_type']);
            if ($network) {
                if ($campaign_data['campaign_type'] == "THIRD-PARTY-AD-TRACK" && $campaign_data['campaign_type'] == "RICH_MEDIA_SURVEY") {
                    $campaign_data['network_id'] = 7;
                }else{
                    $campaign_data['network_id'] = $network['id'];
                }
                $campaign_data['network_name'] = $network['name'];
            } else {
                print json_encode(array("status" => "ERROR", "message" => 'We can not detect campaign network. Try again with another campaign type'));
                exit;
            }



        $user = $this->user;
        if(!empty($this->input->post('assign_user')) && $this->user['is_admin']){
            $campaign_data['userid'] = $this->input->post('assign_user');
            if($campaign_data['userid'] != $this->user['id']) {
                $this->load->model('V2_users_model');
                $user = $this->V2_users_model->get_by_id($campaign_data['userid']);
            }
        } else {
            $campaign_data['userid'] = $user['id'];
        }
        if(!$campaign_data['budget'] && $campaign_data['campaign_tier']){
            // for FLAT users we need to calculate daily budget from max_budget
            $start_date = new DateTime($campaign_data['campaign_start_datetime']);
            $end_date = new DateTime($campaign_data['campaign_end_datetime']);
            $days_count = $end_date->diff($start_date)->days;
            $campaign_budget = $campaign_data['max_budget']/$days_count;
            $campaign_data['budget'] = $campaign_budget;
            // need to calculate percentage budget FOR FLAT USERS TOO
            $flat_percentage_budget = $campaign_data['budget']*$user['flat_percentage_budget']/100;
            $campaign_data['percentage_budget'] = $flat_percentage_budget;
            $percentage_max_budget = $campaign_data['max_budget']*$user['flat_percentage_budget']/100;
            $campaign_data['percentage_max_budget'] = $percentage_max_budget;
        } else {
            // user is percentage
            $campaign_budget = $campaign_data['budget'];
            // need to calculate percentage budget
            $percentage_budget = $campaign_data['budget']*$user['budget_percentage']/100;
            $campaign_data['percentage_budget'] = $percentage_budget;
            if($more_options && $more_options='Y'){
                if ($campaign_data["max_budget"]) {
                    $cost = $campaign_data["max_budget"];
                } elseif ($campaign_data["max_clicks"]) {
                    $cost = $campaign_data["max_clicks"] * $user['display_click'];
                } elseif ($campaign_data["max_impressions"]) {
                    $cost = $campaign_data["max_impressions"] * $user['display_imp'] / 1000;
                } elseif ($campaign_data["campaign_end_datetime"]) {
                    $start_date = new DateTime($campaign_data['campaign_start_datetime']);
                    $end_date = new DateTime($campaign_data['campaign_end_datetime']);
                    $days_count = $end_date->diff($start_date)->days;
                    $cost = $campaign_data["budget"] * $days_count;
                }
                $campaign_data['max_budget'] = $cost;
                $percentage_max_budget = $cost*$user['budget_percentage']/100;
                $campaign_data['percentage_max_budget'] = $percentage_max_budget;
            }
        }
        // var_dump($campaign_budget); exit;
        $result = $this->V2_master_campaign_model->validate($campaign_data);
        if (count($result['messages'])) {
            $messages = '';
            foreach ($result['messages'] as $message) {
                $messages .= ' ' . $message;
            }
            // get messages texts and send json
            print json_encode(array("status" => "ERROR", "message" => $messages));
            exit;
        }
        /**
         * Validate IP Addresses for IP Targeting
         */
        if ( !empty($campaign_data['ip_targeting_ips_json']) ) {
            $targeting_ips = json_decode($campaign_data['ip_targeting_ips_json'], true);
            if ( !empty($targeting_ips) && is_array($targeting_ips) ) {
                $res = $this->validate_retargeting_ip_file($targeting_ips, $return = true);

                // If All IP addresses are not VALID
                if ( !$res['all_valid'] ) {
                    $invalid_ips = implode(', ', array_column($res['invalids'], 'ip'));
                    die(json_encode(array("status" => "ERROR", "message" => $invalid_ips . ' are INVALID IPs')));
                }
                // All Retargeting IPs are valid
                else {
                    $retargeting_ips_data = [];
                    foreach ( $targeting_ips as $ip ) {
                        $retargeting_ips_data[] = $this->ip_address->get_ip_range($ip);
                    }
                    $result['valide_campaign']['ip_targeting_ips_json'] = $retargeting_ips_data;
                }
            }
        }
        $is_multiple = false;
        if(isset($result['valide_campaign']['max_clicks'])) {
            $result['valide_campaign']['bid'] = $network['bid'];
        } else {
            $result['valide_campaign']['bid'] = $network['cpm_bid'];
        }

        if($user['is_multiple'] && $campaign_data['network_name'] == 'AIRPUSH' && false) {
            $this->load->model("V2_users_multiple_networks_model");
            $multiple_networks = $this->V2_users_multiple_networks_model->get_multiple_networks($user['id']);
            $multiple_network = $multiple_networks[0];
            $multiple_budget = $campaign_data['percentage_max_budget']*$multiple_network['percent_of_budget']/100;
            if($multiple_budget < $multiple_network['min_budget']) {
                $multiple_budget = $multiple_network['min_budget'];
            }
            $campaign_budget = $campaign_data['percentage_max_budget'] - $multiple_budget;
            // $campaign_data['percentage_max_budget'] = $this->campaign_budget;
            $is_multiple = true;
            $result['valide_campaign']['is_multiple'] = "Y";
        }
        if($campaign_data['campaign_type'] == "RICH_MEDIA_INTERSTITIAL") {
            $result['valide_campaign']['bid'] = 3;
        }
//        $result['valide_campaign']['bid'] = 0.12;
//
//        if ($campaign_data['network_name'] == "GOOGLE") {
//            $result['valide_campaign']['bid'] = 0.12;
//        } elseif($campaign_data['campaign_type'] == "DISPLAY") {
//            $result['valide_campaign']['is_multiple'] = "Y";
//            $is_multiple = true;
//        }
//
//
//        if($is_multiple) {
//            // maybe need to save 98% of budget for default network not 100%
//            if($user['is_billing_type']=="PERCENTAGE") {
//                $campaign_budget = $campaign_data['percentage_budget']*$this->campaign_budget/100;
//                $multiple_budget = $campaign_data['percentage_budget']*$this->multiple_budget/100;
//            } else {
//                $campaign_budget = $campaign_data['budget']*$this->campaign_budget/100;
//                $multiple_budget = $campaign_data['budget']*$this->multiple_budget/100;
//            }
//        }
        if ($campaign_data['network_name'] == "FIQ" && $campaign_data['campaign_type'] == "TEXTAD") {
            $ads_count = count($campaign_data['ads']);
            // need to calculate daily_budget, bid and cap_per_hour
            $result['valide_campaign']['cap_per_hour'] = ceil($result['valide_campaign']['max_clicks'] * 0.15);
            if ($campaign_data['geotype'] == 'country') {
                $bid = 0.0018;
            } else {
                $bid = 0.0028;
            }
            $daily_cap = $campaign_budget / $ads_count;
            $result['valide_campaign']['bid'] = $bid;
            $result['valide_campaign']['daily_cap'] = $daily_cap;
        }

        // set campaign status to SCHEDULED (in db we set campaign status SCHEDULED by default)
        // $result['valide_campaign']['campaign_status'] = 'SCHEDULED';
        // var_dump($result['valide_campaign']); exit;
        // begin transaction
        $this->db->trans_begin();
        if ($campaign_data['is_fb_form']) { //var_dump($campaign_data['form']);
            if ($campaign_data['form'] && $campaign_data['form']["form_type"]=='new') {
                unset($campaign_data['form']["form_type"]);
                unset($campaign_data['form']["form_id"]);
//                $campaign_data['form']["campaign_id"] = 11;
//                $campaign_data['form']["network_id"] = 5;
                //$campaign_data['form']["campaign_id"] = $campaign_id;
                $campaign_data['form']["network_id"] = $network['id'];
                $campaign_data['form']["user_id"] = $this->userid;
                $campaign_data['form']["page_id"] = $campaign_data['fb_page_id'];
                if($campaign_data['form']["context_type"]=='bullets') {
                    $campaign_data['form']["bullets"] = json_encode(array_filter($campaign_data['form']["bullets"]));
                } else {
                    unset($campaign_data['form']["bullets"]);
                }
                if($campaign_data['form']["export_type"]=='export_as_csv') {
                    unset($campaign_data['form']["email"]);
                    unset($campaign_data['form']["email_type"]);
                } else {
                    if(empty($campaign_data['form']["email"])) {
                        $campaign_data['form']["email"] = $user['email'];
                    }
                }
                //upload images
                if ($campaign_data['form']['image']) {
                    $move_result = $this->move_uploaded_tmp_image($campaign_data['form']['image']); // var_dump($result);
                    if (count($move_result['messages'])) {
                        $messages = '';
                        foreach ($move_result['messages'] as $message) {
                            $messages .= ' ' . $message;
                        }
                        $this->db->trans_rollback();
                        // get messages texts and send json
                        print json_encode(array("status" => "ERROR", "message" => $messages));
                        exit;
                    }
                    $campaign_data['form']['image'] = $this->config->base_url() . $result['new_url'];
                }
                //var_dump(777);
                $this->load->model('V2_fb_form_model');
                $form_id = $this->V2_fb_form_model->create($campaign_data['form']);
                $result['valide_campaign']['form_id'] = $form_id;
            }
            //var_dump($form_id); exit;
        }

        /**
         * Creating Master Campaign
         */
        $campaign_id = $this->V2_master_campaign_model->create($result['valide_campaign']);
        if (!$campaign_id) {
            $this->db->trans_rollback();
            print json_encode(array("status" => "SUCCESS", "message" => "Campaign isn't created"));
            exit;
        }
        if( !empty($campaign_data['access_viewers']) ) {
            $this->load->model("V2_viewer_campaign_model");
            $this->V2_viewer_campaign_model->insert_viewers($campaign_data['access_viewers'],$campaign_id,$this->userid);
        }
        if ($campaign_data['is_lookalike_audience']) {
            if ($campaign_data['audience_type'] == 'new') {
                if (!empty($campaign_data['lookalike_name'])) {
                    $insert_lookalike["name"] = $campaign_data['lookalike_name'];
                } else {
                    //$message[] = 'behaviors is empty';
                }
                if ($campaign_data['lookalike_type'] == 'page') {
                    $insert_lookalike["page_id"] = $campaign_data['lookalike_page_id'];
                } else {
                    $insert_lookalike["pixel"] = '700571266746502';
                }
            }
            $insert_lookalike["type"] = $campaign_data['lookalike_type'];
            $insert_lookalike["io"] = $campaign_data['io'].'_lookalike';
            $insert_lookalike["campaign_id"] = $campaign_id;
            $insert_lookalike["network_id"] = $network['id'];
            $insert_lookalike["user_id"] = $this->userid;
            //var_dump(777);
            $this->load->model('userlist_io_model');
            $this->userlist_io_model->create_lookalike_userlist_io($insert_lookalike);
            //var_dump(111);
        }
        if ($campaign_data['is_email_audience']) {
            if ($campaign_data['email_audience_type'] == 'new' && $campaign_data['email_audience_file'] && !empty($campaign_data['custom_name'])) {
                $insert_email["name"] = $campaign_data['custom_name'];
                $insert_email["file"] = $campaign_data['email_audience_file'];
                $insert_email["type"] = 'EMAIL';
                $insert_email["io"] = $campaign_data['io'] . '_email';
                $insert_email["campaign_id"] = $campaign_id;
                $insert_email["network_id"] = $network['id'];
                $insert_email["user_id"] = $this->userid;
                //var_dump(777);
                $this->load->model('userlist_io_model');
                $this->userlist_io_model->create_email_userlist_io($insert_email);
                //var_dump(111);
            }
        }
        $this->load->model('V2_time_parting_model');
        foreach ($campaign_data['timing'] as $key=>$value) {
            $time = explode(';',$value);
            $start_time = $time[0];
            $end_time = $time[1];
            $insert = array(
                "campaign_id" => $campaign_id,
                "day_of_week" => $key,
                "created_date" => date("Y-m-d H:i:s"),
                "start_time" => $start_time,
                "end_time" => $end_time,
            );
            $this->V2_time_parting_model->create($insert);
        }
        if($is_multiple) {
            $this->load->model('V2_multiple_campaign_model');
            $result['valid_campaign']['budget'] = $multiple_budget;
            $result['valide_campaign']['id'] = $campaign_id;
            $multiple_campaign_id = $this->V2_multiple_campaign_model->create($result['valide_campaign']);
        }

        //create group for GOOGLE network
        // validate group
        if ($network['has_group'] == "Y") {
            $group_data['campaign_id'] = $campaign_id;
            $group_data['group_name'] = $campaign_data['name'] . '_group';
            $group_data['group_status'] = 'ACTIVE';
            $group_data['date_created'] = date("Y-m-d H:i:s");
            $this->load->model('V2_group_model');
            $group_id = $this->V2_group_model->create($group_data);
            if (!$group_id) {
                // remove last insert campaign
                $this->db->trans_rollback();
                print json_encode(array("status" => "ERROR", "message" => "We can't create group. Please try again"));
                exit;
            }
        }

        if ($is_multiple) {
            $group_data['campaign_id'] = $campaign_id;
            $group_data['multiple_campaign_id'] = $multiple_campaign_id;
            $group_data['group_name'] = $campaign_data['name'] . '_group';
            $group_data['group_status'] = 'ACTIVE';
            $group_data['date_created'] = date("Y-m-d H:i:s");
            $this->load->model('V2_group_model');
            $multiple_group_id = $this->V2_group_model->create($group_data);
            if (!$multiple_group_id) {
                // remove last insert campaign
                $this->db->trans_rollback();
                print json_encode(array("status" => "ERROR", "message" => "We can't create group. Please try again"));
                exit;
            }
        }

        // `EMAIL` type Campaign Handling
        if ($campaign_data['campaign_type'] == 'EMAIL') {
            if ($campaign_data['fire_open_pixel'] == "Y" && count($campaign_data['open_pixel_src'])) {
                $this->load->model('V2_campaign_openpixel_model');
                $ids = $this->V2_campaign_openpixel_model->create($campaign_data['open_pixel_src'], $campaign_id);

                // do check if saving data are correctly saved
                if (!$ids) {
                    // remove last insert campaign
                    $this->db->trans_rollback();
                    print json_encode(array("status" => "ERROR", "message" => "We can't create pixel images. Please try again"));
                    exit;
                }
            }
            $this->load->model('V2_ads_link_model');
            $ppc_links = json_decode($campaign_data['ppc_links'], true);
            $clicks_sum = 0;
            foreach ($ppc_links as $ppc_link) {
                $link_id = $this->V2_ads_link_model->create($ppc_link, $campaign_id);
                if (!$link_id) {
                    // send error message and rollback
                    $this->db->trans_rollback();
                    print json_encode(array("status" => "ERROR", "message" => "We can't create ad links. Please try again"));
                    exit;
                }
                $clicks_sum = $clicks_sum + $ppc_link['max_clicks'];
            }
            // if ppc links clicks sum < total_clicks then show error
            if ($clicks_sum < $campaign_data['total_clicks']) {
                $this->db->trans_rollback();
                print json_encode(array("status" => "ERROR", "message" => "Total clicks can't be greater than sum of links clicks. Please try again"));
                exit;
            }
        }
        // Non EMAIL type campaign ads
        else {

            //create campaign ads
            $this->load->model('V2_ad_model');
            $this->load->model('V2_ads_link_model');

            // make DESTINATION URL
            $destination_url = $this->V2_master_campaign_model->generate_destination_url($campaign_id, $campaign_data['domain'], $campaign_data['campaign_type']);

            foreach ( $campaign_data['ads'] as $ad_json ) {

                // initialize group id in ads if network is google and group is created
                $ad = json_decode($ad_json, true);
                if ($group_id) {
                    $ad['group_id'] = $group_id;
                }

                // Upload Attached Ad Images ( if applicable )
                if( empty( $ad['airpush_internal_image'] )
                    && !( $ad['creative_type']=='VIDEO_YAHOO'
                        || $ad['creative_type']=='VIDEO'
                        || $ad['creative_type']=='VIDEO-CLICKS'
                        || $ad['creative_type']=='RICH_MEDIA'
                        || $ad['creative_type']=='FB-PROMOTE-EVENT'
                        || $ad['creative_type']=='RICH_MEDIA_SURVEY'
                        )
                ) {

                    //upload images
                    if ($ad['creative_url']) {
                        $result = $this->move_uploaded_tmp_image($ad['creative_url']);
                        if (count($result['messages'])) {
                            $messages = '';
                            foreach ($result['messages'] as $message) {
                                $messages .= ' ' . $message;
                            }
                            $this->db->trans_rollback();
                            // get messages texts and send json
                            print json_encode(array("status" => "ERROR", "message" => $messages));
                            exit;
                        }
                    if($ad['creative_type']=='YAHOO_CAROUSEL') {
                            $image_result = $this->get_tmp_from_image($result['new_url'],$ad['creative_type']=='YAHOO_CAROUSEL');
                            $ad['square_creative_url'] = $this->config->base_url() . $image_result['new_square_url'];
                        }
                        $ad['creative_url'] = $this->config->base_url() . $result['new_url'];
                    }
                }

                // Upload Attached Ad Videos ( if applicable )
                if( $ad['creative_type']=='VIDEO'
                    || $ad['creative_type']=='VIDEO-CLICKS'
                    || $ad['creative_type']=='VIDEO_YAHOO'
                ) {
                    $result = $this->move_uploaded_tmp_video($ad['creative_url']);
                    if (count($result['messages'])) {
                        $messages = '';
                        foreach ($result['messages'] as $message) {
                            $messages .= ' ' . $message;
                        }
                        $this->db->trans_rollback();
                        // get messages texts and send json
                        print json_encode(array("status" => "ERROR", "message" => $messages));
                        exit;
                    }
                    $ad['video_url'] = $this->config->base_url() . $result['new_url'];
                    $image_result = $this->get_image_from_video($result['new_url'], $ad['creative_type']);
                    if (count($image_result['messages'])) {
                        $messages = '';
                        foreach ($image_result['messages'] as $message) {
                            $messages .= ' ' . $message;
                        }
                        $this->db->trans_rollback();
                        // get messages texts and send json
                        print json_encode(array("status" => "ERROR", "message" => $messages));
                        exit;
                    }

                    $ad['creative_url'] = $this->config->base_url() . $image_result['new_url'];
                    if( $ad['creative_type'] == 'VIDEO_YAHOO' ) {
                        $ad['square_creative_url'] = $this->config->base_url()
                                                    . $image_result['new_square_url'];
                    }
                    $movie = new FFmpegMovie($result['new_url']);
                    $movie_dureation_sec = $movie->getDuration();
                    $movie_dureation_min = gmdate("i:s", $movie_dureation_sec);
                    $ad['video_duration'] = $movie_dureation_min;
                }

                // diff add attributes
                $ad['creative_name'] = $campaign_data['name'] . ' ' . uniqid();
                if ($campaign_data['campaign_type'] == "THIRD-PARTY-AD-TRACK" && $campaign_data['campaign_type'] == "RICH_MEDIA_SURVEY") {
                    $ad['network_id'] = 7;
                }
                else{
                    $ad['network_id'] = $network['id'];
                }
                $ad['campaign_id'] = $campaign_id;
                if($campaign_data['fb_page_id']){
                    $ad['fb_page_id'] = $campaign_data['fb_page_id'];
                }
                if($campaign_data['address']){
                    $ad['address'] = $campaign_data['address'];
                }
                if($campaign_data['lat']){
                    $ad['lat'] = $campaign_data['lat'];
                }
                if($campaign_data['lng']){
                    $ad['lng'] = $campaign_data['lng'];
                }
                if($campaign_data['fb_page_like']){
                    $ad['fb_page_like'] = $campaign_data['fb_page_like'];
                }
                if ($campaign_data['network_name'] == "FIQ" && $campaign_data['campaign_type'] == "TEXTAD") {
                    $ad['bid'] = $bid;
                    $ad['daily_cap'] = $daily_cap;
                    $ad['approval_status'] = 'ACTIVE';
                    $ad['creative_status'] = 'ACTIVE';
                }


                // validate Ad
                $result = $this->V2_ad_model->validate($ad);

                if ( count($result['messages']) ) {
                    $messages = '';
                    foreach ($result['messages'] as $message) {
                        $messages .= ' ' . $message;
                    }
                    $this->db->trans_rollback();
                    // get messages texts and send json
                    print json_encode(array("status" => "ERROR", "message" => $messages));
                    exit;
                }
                if($is_multiple) {
                    $result['valide_ad']['is_multiple'] = 'Y';
                }

                /**
                 * Save the Ad
                 */

                $ad_id = $this->V2_ad_model->create($result['valide_ad'], $destination_url);
                if($is_multiple) {
                    $this->load->model('V2_multiple_ad_model');
                    $multiple_ad_id = $this->V2_multiple_ad_model->create($result['valide_ad'], $multiple_campaign_id, $multiple_group_id, $ad_id);
                }

                if ($ad_id) {

                    /**
                     * Create ad entry to Google AdX
                     */
                    $should_create_google_adx = true;
                    $campaign_data['destination_url'] = $destination_url;
                    $campaign_data['id'] = $campaign_id;
                    $this->_create_google_adx_entry($ad_id, $campaign_data);
                    // Google AdX entry end $!!

                    $link_id = $this->V2_ads_link_model->create($result['valide_ad'], $campaign_id, $ad_id);
                    if (!$link_id) {
                        // send error message and rollback
                        $this->db->trans_rollback();
                        print json_encode(array("status" => "ERROR", "message" => "We can't create ad links. Please try again"));
                        exit;
                    }
                } else {
                    // send error message and rollback
                    $this->db->trans_rollback();
                    print json_encode(array("status" => "ERROR", "message" => "We can't create ads. Please try again"));
                    exit;
                }
            }
        }

        $this->db->trans_commit();

        print json_encode(array("status" => "SUCCESS", "message" => "Campaign is created"));
        $this->db->trans_complete();
        exit;
    }

    private function _create_google_adx_entry($ad_id, $campaign_data)
    {
        $ad = $this->V2_ad_model->get_by_id($ad_id);
        $creative = $this->v2_google_adx_model->insert_creative([
            'campaign' => $campaign_data,
            'ad' => $ad
        ]);
    }

    public function create_image(){
        $campaign_data = $this->input->post();
        $campaign_data['campaign_type'] = 'REAL_ESTATE_PROFESSIONAL_CAMPAIGN';
        // detect campaign type and network
        $this->load->model('V2_network_model'); //var_dump($this->userid,$campaign_data['campaign_type']); exit;
            $network = $this->V2_network_model->get_all_by_user_id_and_campaign_type($this->userid, $campaign_data['campaign_type']);
            if ($network) {
                if ($campaign_data['campaign_type'] == "THIRD-PARTY-AD-TRACK" && $campaign_data['campaign_type'] == "RICH_MEDIA_SURVEY") {
                    $campaign_data['network_id'] = 7;
                }else{
                    $campaign_data['network_id'] = $network['id'];
                }
                $campaign_data['network_name'] = $network['name'];
            } else {
                print json_encode(array("status" => "ERROR", "message" => 'We can not detect campaign network. Try again with another campaign type'));
                exit;
            }

        $user = $this->user;
        $campaign_data['userid'] = $this->userid;
        if($campaign_data['campaign_tier']) {
            $campaign_data['max_budget'] = $user['budget_'.$campaign_data['campaign_tier']];
            $campaign_data['campaign_start_datetime'] = date("Y-m-d H:i:s");
            $campaign_data['campaign_end_datetime'] = date("Y-m-d H:i:s", strtotime("+30 days"));
            $days_count = 30;
            $campaign_budget = $campaign_data['max_budget']/$days_count;
            $campaign_data['budget'] = $campaign_budget;
            // need to calculate percentage budget FOR FLAT USERS TOO
//            $flat_percentage_budget = $campaign_data['budget']*$user['flat_percentage_budget']/100;
            $campaign_data['percentage_budget'] = $campaign_data['budget']/2;
//
//            $percentage_max_budget = $campaign_data['max_budget']*$user['flat_percentage_budget']/100;
            $campaign_data['percentage_max_budget'] = $campaign_data['max_budget']/2;;
            $campaign_data['max_clicks'] = $user['clicks_count_'.$campaign_data['campaign_tier']];
            $campaign_data['max_impressions'] = $user['impressions_count_'.$campaign_data['campaign_tier']];
        }
        //var_dump($campaign_budget); exit;
        $campaign_data['geotype'] = 'postalcode';
        $campaign_data['radius'] = 25;
        $campaign_data['domain'] = 'reporting.prodata.media';
        $campaign_data['vertical'] = 'home';
//        $result = $this->V2_master_campaign_model->validate($campaign_data);
//
//        if (count($result['messages'])) {
//            $messages = '';
//            foreach ($result['messages'] as $message) {
//                $messages .= ' ' . $message;
//            }
//            // get messages texts and send json
//            print json_encode(array("status" => "ERROR", "message" => $messages));
//            exit;
//        }
//
//        $result['valide_campaign']['bid'] = $network['bid'];
//
//        $this->db->trans_begin();
//
//        $campaign_id = $this->V2_master_campaign_model->create($result['valide_campaign']);
//
//        if (!$campaign_id) {
//            $this->db->trans_rollback();
//            print json_encode(array("status" => "SUCCESS", "message" => "Campaign isn't created"));
//            exit;
//        }
//
//        if ($network['has_group'] == "Y") {
//            $group_data['campaign_id'] = $campaign_id;
//            $group_data['group_name'] = $campaign_data['name'] . '_group';
//            $group_data['group_status'] = 'ACTIVE';
//            $group_data['date_created'] = date("Y-m-d H:i:s");
//            $this->load->model('V2_group_model');
//            $group_id = $this->V2_group_model->create($group_data);
//            if (!$group_id) {
//                // remove last insert campaign
//                $this->db->trans_rollback();
//                print json_encode(array("status" => "ERROR", "message" => "We can't create group. Please try again"));
//                exit;
//            }
//        }
        $this->load->library('SimpleImage');
        $image = new SimpleImage();
        $sizes = array(
            array(
                'width'=>728,
                'height'=>90,
            ),
            array(
                'width'=>468,
                'height'=>60,
            ),
            array(
                'width'=>300,
                'height'=>250,
            ),
            array(
                'width'=>160,
                'height'=>600,
            ),
        );
        $paths = array();
        foreach($sizes as $size) {
            $paths[] = $image->createImageBySize($size['width'],$size['height'],$campaign_data);
        }
        $this->db->trans_rollback();
        print json_encode(array("status" => "SUCCESS", "message" => "Campaign is created", 'data' => $paths));
        exit;
        //create campaign ads
        $this->load->model('V2_ad_model');
        $this->load->model('V2_ads_link_model');
        $destination_url = $this->V2_master_campaign_model->generate_destination_url($campaign_id, $campaign_data['domain'], $campaign_data['campaign_type']);
        foreach($sizes as $size) {
            // initialize group id in ads if network is google and group is created
            $image_path = $image->createImageBySize($size['width'],$size['height'],$campaign_data);
            $ad = array();
            if ($image_path) {
                $result = $this->move_uploaded_tmp_image($image_path);
                if (count($result['messages'])) {
                    $messages = '';
                    foreach ($result['messages'] as $message) {
                        $messages .= ' ' . $message;
                    }
                    $this->db->trans_rollback();
                    // get messages texts and send json
                    print json_encode(array("status" => "ERROR", "message" => $messages));
                    exit;
                }
                $ad['creative_url'] = $this->config->base_url() . $result['new_url'];
            } else {
                $this->db->trans_rollback();
                print json_encode(array("status" => "ERROR", "message" => 'Can not save images, try again'));
                exit;
            }
            if ($group_id) {
                $ad['group_id'] = $group_id;
            }
            $ad['creative_name'] = $campaign_data['name'] . ' ' . uniqid();
            $ad['network_id'] = $network['id'];
            $ad['campaign_id'] = $campaign_id;
            $ad['creative_type'] = 'DISPLAY';
            $ad['destination_url'] = $campaign_data['destination_url'];
            $ad['creative_width'] = $size['width'];
            $ad['creative_height'] = $size['height'];
            $result = $this->V2_ad_model->validate($ad);
            if (count($result['messages'])) {
                $messages = '';
                foreach ($result['messages'] as $message) {
                    $messages .= ' ' . $message;
                }
                $this->db->trans_rollback();
                // get messages texts and send json
                print json_encode(array("status" => "ERROR", "message" => $messages));
                exit;
            }
            $ad_id = $this->V2_ad_model->create($result['valide_ad'], $destination_url);
            if ($ad_id) {
                $link_id = $this->V2_ads_link_model->create($result['valide_ad'], $campaign_id, $ad_id);
                if (!$link_id) {
                    // send error message and rollback
                    $this->db->trans_rollback();
                    print json_encode(array("status" => "ERROR", "message" => "We can't create ad links. Please try again"));
                    exit;
                }
            } else {
                // send error message and rollback
                $this->db->trans_rollback();
                print json_encode(array("status" => "ERROR", "message" => "We can't create ads. Please try again"));
                exit;
            }
        }
        $this->db->trans_commit();
        $this->db->trans_complete();
        print json_encode(array("status" => "SUCCESS", "message" => "Campaign is created"));
        exit;
    }
    private function move_uploaded_tmp_image($url)
    {
        $new_url = '';
        $message = [];
        if (!empty($url)) {
            $destination_dir = 'uploads/permanent/';
            $file_types = ["jpeg", "jpg", "png", "gif"];
            if (file_exists($url)) {
                if (in_array(strtolower(pathinfo($url)['extension']), $file_types)) {
                    $name = pathinfo($url)['basename'];
                    if (rename($url, $destination_dir . $name)) {
                        $new_url = $destination_dir . $name;
                    } else {
                        $message[] = "Can't move uploaded image";
                    }
                } else {
                    $message[] = "The uploaded image type doesn't allow";
                }
            } else {
                $message[] = "Can't find image by Url";
            }
        } else {
            $message[] = 'image Url is empty';
        }
        return array('messages' => $message, 'new_url' => $new_url);
    }
    private function move_uploaded_tmp_video($url)
    {
        $new_url = '';
        $message = [];
         $destination_dir = 'uploads/permanent/';
        if (!empty($url)) {
            $destination_dir = 'uploads/permanent/';
            $file_types = ["mov", "mp4"];
            if (file_exists($url)) {
                if (in_array(strtolower(pathinfo($url)['extension']), $file_types)) {
                    $name = pathinfo($url)['basename'];
                    if (rename($url, $destination_dir . $name)) {
                        $new_url = $destination_dir . $name;
                    } else {
                        $message[] = "Can't move uploaded video";
                    }
                } else {
                    $message[] = "The uploaded video type doesn't allow";
                }
            } else {
                $message[] = "Can't find video by Url";
            }
        } else {
            $message[] = 'Video Url is empty';
        }
        return array('messages' => $message, 'new_url' => $new_url);
    }

     private function get_tmp_from_image($url, $campaign_type=null){
        $new_url = '';
        $message = [];
        preg_match('/^.*\.(jpg|jpeg|png|gif)$/i', $url, $file_format);
        $name_thumb = md5(microtime());
        $this->load->library('image_lib');
    if (!empty($url)) {
         $new_square_url = 'uploads/permanent/thumb_'. $name_thumb .'.'.$file_format[1];
         }
        $config = array(
        'image_library' => 'gd2',
        'source_image' => $url,
        'new_image' => $new_square_url,
        'maintain_ratio' => '',
        'create_thumb' => TRUE,
        'thumb_marker' => '',
        'width' => 627,
        'height' => 627
    );
    $this->image_lib->initialize($config);
    if (!$this->image_lib->resize()) {
        echo $this->image_lib->display_errors();
    }
    // clear //
    $this->image_lib->clear();
        return array('messages' => $message, 'new_url' => $new_url, 'new_square_url' => $new_square_url);
    }


    private function get_image_from_video($url, $campaign_type=null)
    {
        $new_url = '';
        $message = [];
        if (!empty($url)) {
            $destination_dir = 'uploads/permanent/';
            if (file_exists($url)) {
                $name = pathinfo($url)['filename'].'.jpg';
                $movie = new FFmpegMovie($url);
                if($campaign_type == 'VIDEO_YAHOO') {
                    $height = $movie->getFrameHeight();
                    $width = $movie->getFrameWidth();
                    if(round($width/$height, 2) <= 1.95 && round($width/$height, 2) >= 1.90) {
                        $frame = $movie->getFrame(3);
                        $image = $frame->toGDImage();
                        if (imagejpeg($image, $destination_dir . $name)) {
                            $new_url = $destination_dir . $name;
                        } else {
                            $message[] = "Can't create image from video";
                        }
                    } else if(round($width/$height, 2) < 1.90) {
                        $new_height = round($width/1.9);
                        $height_crop_pixels = $height-$new_height;
                        $crop_from_top = floor($height_crop_pixels/2);
                        $crop_from_bottom = ceil($height_crop_pixels/2);
                        $frame = $movie->getFrame(3);
                        $frame->crop($crop_from_top, $crop_from_bottom);
                        $image = $frame->toGDImage();
                        if (imagejpeg($image, $destination_dir . $name)) {
                            $new_url = $destination_dir . $name;
                        } else {
                            $message[] = "Can't create image from video";
                        }
                    }
                    $width_crop_pixels = $width-$height;
                    $crop_from_left = floor($width_crop_pixels/2);
                    $crop_from_right = ceil($width_crop_pixels/2);
                    $frame = $movie->getFrame(3);
                    $frame->crop(null, null, $crop_from_left, $crop_from_right);
                    $image = $frame->toGDImage();
                    $name_square = pathinfo($url)['filename'].'_square.jpg';
                    if (imagejpeg($image, $destination_dir . $name_square)) {
                        $new_square_url = $destination_dir . $name_square;
                    } else {
                        $message[] = "Can't create square image from video";
                    }
                } else {
                    $frame = $movie->getFrame(3);
                    $image = $frame->toGDImage();
                    if (imagejpeg($image, $destination_dir . $name)) {
                        $new_url = $destination_dir . $name;
                    } else {
                        $message[] = "Can't create image from video";
                    }
                }
            } else {
                $message[] = "Can't find video by Url";
            }
        } else {
            $message[] = 'Video Url is empty';
        }
        return array('messages' => $message, 'new_url' => $new_url, 'new_square_url' => $new_square_url);
    }
    private function sort_interests($interests_unsorted) {
        $interests_sorted = [];
        foreach($interests_unsorted as $key=>$interest){
            if(count($interest['path'])==1) {
                continue;
            }
            if( isset($interest['path'][2]) ) {
                if( empty($interests_sorted[$interest['path'][0]][$interest['path'][1]])
                    || !in_array($interest['path'][2], $interests_sorted[$interest['path'][0]][$interest['path'][1]]) ) {
                    $interests_sorted[$interest['path'][0]][$interest['path'][1]][] = $interest['path'][2];
                }
            }
            else {
                if(!isset($interests_sorted[$interest['path'][0]][$interest['path'][1]])) {
                    $interests_sorted[$interest['path'][0]][$interest['path'][1]] = [];
                }
                continue;
            }
            for($i = $key+1; $i < count($interests_unsorted); $i++){
                if($interests_unsorted[$i]['path'][0] == $interest['path'][0]){
                    if(isset($interests_unsorted[$i]['path'][2]) ) {
                        if( empty($interests_sorted[$interest['path'][0]][$interests_unsorted[$i]['path'][1]])
                            || !in_array($interests_unsorted[$i]['path'][2], $interests_sorted[$interest['path'][0]][$interests_unsorted[$i]['path'][1]]) ) {
                            $interests_sorted[$interest['path'][0]][$interests_unsorted[$i]['path'][1]][] = $interests_unsorted[$i]['path'][2];
                        }
                    }
                }
            }
        }
        return $interests_sorted;
    }
    private function sort_demographics($interests_unsorted) {
        $interests_sorted = [];
        foreach($interests_unsorted as $key=>$interest){
            if(isset($interest['path'][1]) ) {
                $interests_sorted[$interest['type']][$interest['path'][0]][$interest['name']]['name'] = $interest['name'];
                $interests_sorted[$interest['type']][$interest['path'][0]][$interest['name']]['id'] = $interest['id'];
            }
            else {
                $interests_sorted[$interest['type']][$interest['name']]['name'] = $interest['name'];
                $interests_sorted[$interest['type']][$interest['name']]['id'] = $interest['id'];
            }
        }
        return $interests_sorted;
    }

    public function new_campaign()
    {
        if (!$this->userid){
            return redirect(base_url());
        }
        if($this->user_type == 'viewer' || $this->user['create_campaign'] == 'N') {
            return redirect(base_url());
        }
        $this->load->model('Userlist_vertical_model');
        $this->load->model('Userlist_io_model');
        $this->load->model('V2_affinity_model');
        $this->load->model('V2_users_model');
        $this->load->model('V2_fb_form_model');
        $this->load->model('V2_map_users_network_model');
        $affinity = $this->V2_affinity_model->get_by_type('affinity'); //echo '<pre>'; var_dump($affinity); exit;
        $in_market = $this->V2_affinity_model->get_by_type('in_market');// var_dump($in_market); exit;
        $yahoo_interest = $this->V2_affinity_model->get_by_type('interest');// var_dump($in_market); exit;
        $sorted_in_market = [];
        foreach ($in_market as $key => $value) {
            if($value['down4_name']) { // echo $in_market[$value['parent_category_id']-1]['category'];
                $sorted_in_market[$value['root_name']][$value['down1_name']][$value['down2_name']][$value['down3_name']][$value['down4_name']] = $value['down4_criterion_id'];
            } else if($value['down3_name']) {
                $sorted_in_market[$value['root_name']][$value['down1_name']][$value['down2_name']][$value['down3_name']] = $value['down3_criterion_id'];
            } else if($value['down2_name']) {
                $sorted_in_market[$value['root_name']][$value['down1_name']][$value['down2_name']] = $value['down2_criterion_id'];
            } else if($value['down1_name']) {
                $sorted_in_market[$value['root_name']][$value['down1_name']] = $value['down1_criterion_id'];
            }
        }
        $demographics_unsorted = $this->facebook_model->get_demographics();
        $demographics_sorted = $this->sort_demographics($demographics_unsorted);
        $interests_unsorted = $this->facebook_model->get_interests();
        $interests_sorted = $this->sort_interests($interests_unsorted);
        $behaviors_unsorted = $this->facebook_model->get_behaviors();
        $behaviors_sorted = $this->sort_interests($behaviors_unsorted);
        $this->viewArray['affinity_list'] = $affinity;
        $this->viewArray['yahoo_interest_list'] = $yahoo_interest;
        $this->viewArray['in_market_list'] = $sorted_in_market;
        $this->viewArray['interest_list'] = $interests_sorted;
        $this->viewArray['behavior_list'] = $behaviors_sorted;
        $this->viewArray['demographics_list'] = $demographics_sorted;
        $this->viewArray['vertical_list'] = $this->Userlist_vertical_model->get_all_users_from_vertical();
        $this->viewArray['form_list'] = $this->V2_fb_form_model->get_all_by_user_id($this->userid);
        $this->viewArray["domain_list"] = [['name' => $_SERVER['HTTP_HOST']]];//$this->Domains_model->get_all_by_user_id($this->userid); //var_dump($this->viewArray["domain_list"]); exit;
        $this->viewArray['io_list'] = $this->Userlist_io_model->get_userlist_from_io_by_user_id($this->userid);
        $this->viewArray['user_networks'] = $this->V2_map_users_network_model->get_networks($this->userid);
        $so_numbers = $this->V2_master_campaign_model->get_so_numbers($this->userid);
        $so_numbers_sorted = [];
        foreach ($so_numbers as $number) {
            $so_numbers_sorted[] = $number['so'];
        }
        $this->viewArray['so_numbers'] = $so_numbers_sorted;
        $this->viewArray["files_uploaded"] = null;
        $this->viewArray["campaign_type_names"] = $this->campaign_type_names;
        if($this->user['is_adtrack'] == 'Y') {
            $this->viewArray["campaign_type_names"]['THIRD-PARTY-AD-TRACK'] = 'Third Party-Ad-Track';
        }
        $this->viewArray["viewers"] = $this->V2_users_model->get_all_viewers_by_userid($this->userid);
        if($this->user['is_admin']) {
            $this->viewArray['customers'] = $this->V2_users_model->get_active_customers();
        }
        $this->viewArray["fb_pages"] = $this->facebook_model->get_fb_pages($this->userid);
        // IAB categories
        $this->viewArray['iab_categories'] = $this->v2_iab_category_model->get_all_categories();
        $this->viewArray['is_simple'] = $this->v2_iab_category_model->get_all_categories();
        // echo "<pre>";
        // print_r($this->viewArray);
        // die;
        $this->parser->parse($this->view_file, $this->viewArray);
        // {if $user['is_adtrack'] == "Y"}
        // <div class="theme-tabbed-form-group">
        //     <input type_class="ADTRACK" name="campaign_network" type="radio" value="ADTRACK" class="theme-tabbed-form-control check_type" id="adtrack">
        //     <label class="theme-tabbed-form-label" for="adtrack">Third Party / Ad Track</label>
        // </div>
        // {/if}
    }

    public function new_professional_campaign()
    {
        if (!$this->userid){
            return redirect(base_url());
        }
        if($this->user_type == 'viewer') {
            redirect(base_url());
        }
        $this->load->model('Userlist_vertical_model');
        $this->load->model('Userlist_io_model');
        $this->load->model('V2_users_model');
        $this->load->model('V2_map_users_network_model');
        $this->viewArray['vertical_list'] = $this->Userlist_vertical_model->get_all_users_from_vertical();
        $this->viewArray["domain_list"] = [['name' => 'reporting.prodata.media']];//$this->Domains_model->get_all_by_user_id($this->userid); //var_dump($this->viewArray["domain_list"]); exit;
        $this->viewArray['io_list'] = $this->Userlist_io_model->get_all_users_from_io();
        $this->viewArray['user_networks'] = $this->V2_map_users_network_model->get_networks($this->viewArray['user']['id']);
        $this->viewArray["files_uploaded"] = null;
        $this->viewArray['campaign_types'] = $this->campaign_types;
        $this->viewArray["viewers"] = $this->V2_users_model->get_all_viewers_by_userid($this->userid);
        $this->parser->parse($this->view_file, $this->viewArray);
    }
    public function get_io()
    {
        $io = $this->Userlist_io_model->get_all_users_from_io();
        foreach ($io as $item) {
            $io_array[] = $item['io'];
        }
        print json_encode(array("status" => "SUCCESS", "options" => $io_array));
        exit;
    }
    public function get_demographics_by_type()
    {   //var_dump($value); exit;
        if ($this->input->is_ajax_request()) {
            $post_data = $this->input->post();
            $data = $this->facebook_model->get_demographics_by_type($post_data['value'], $post_data['type']);
            echo $data; exit;
        }
        return false;
    }
    /*
     *
     * @description This function gets the files came by the Ajax Post request and saves them into the targetDir directory. Calls by ajax.
     * @return ajax response
     *
     */
    public function get_states_by_country()
    {
        $country = $this->input->post('country');
        $states = [];
        if ($country == "US" || $country == "CA") {
            $states = $this->Country_model->get_states_by_country($country);
        }
        if (count($states)) {
            print json_encode(array("status" => "SUCCESS", "states" => $states));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "NO states for this country"));
            exit;
        }
    }
    public function get_carriers_by_country()
    {
        $country = $this->input->post('country');
        $carriers = [];
        //if($country == "US" || $country == "CA"){
        $this->load->model("V2_carriers_list_model");
        $carriers = $this->V2_carriers_list_model->get_carriers_list_by_country($country, 1); //1 is id for google network
        //}
        if (count($carriers)) {
            print json_encode(array("status" => "SUCCESS", "carriers" => $carriers));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "NO carriers for this country"));
            exit;
        }
    }
    public function check_io()
    {
        $io = $this->input->post('io');
        //$this->load()->model('V2_master_campaign');
        $io_exist = $this->V2_master_campaign_model->check_io($io);
        if ($io_exist) {
            print json_encode(array("status" => "ERROR"));
            exit;
        } else {
            print json_encode(array("status" => "SUCCESS"));
            exit;
        }
    }
    /**
     * Validate the Uploaded text file
     * consists of Retargeting IPs ( one per line )
     *
     * @return array
     */
    public function validate_retargeting_ip_file(array $ips = [], $return = false)
    {
        $file_content = $this->input->post();
        $ip_validations = ['all_valid' => true, 'ip_addresses' => [], 'invalids' => []];
        if ( !empty($file_content['data']) ) {
            $contents = trim($file_content['data']);
            $ips = array_map('trim', explode("\n", $contents));
        }
        if ( !empty($ips) ) {
            foreach ( $ips as $ip ) {
                $is_valid = $this->ip_address->is_valid_ip($ip);
                $ip_validations['ip_addresses'][] = $ip;
                if ( !$is_valid ) {
                    $ip_validations['invalids'][] = [
                        'ip' => $ip,
                        'is_valid' => $is_valid
                    ];
                }
            }
            $ip_validations['all_valid'] = count($ip_validations['invalids']) == 0;
        }
        if ( $return === true ) return $ip_validations;
        die(json_encode($ip_validations));
    }
    public function uploadFile()
    {
        $targetDir = 'uploads/tmp';
        $name = $_POST["name"];
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds
        //
        // Create target dir
        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
            @chmod($targetDir, 0777);
        }
        // Get a file name
//            if (isset($_REQUEST["name"])) {
//                $fileName = $_REQUEST["name"];
//            } elseif (!empty($_FILES)) {
//                $fileName = $_FILES["file"]["name"];
//            } else {
//                $fileName = uniqid("file_");
//            }
        // Chunking might be enabled
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
        $fileNameArray = pathinfo($this->input->post('name')); //var_dump($_FILES); exit;
        $fileExtension = $fileNameArray['extension'];
        if($chunks){
            //var_dump($chunk,$chunks);
            $fileName = $fileNameArray['filename'];
            if ($chunk == $chunks - 1) {
                $fileFinalName = md5(microtime());
                $fileFinalName = $fileFinalName . "." . $fileExtension;
                $fileFinalPath = $targetDir . DS . $fileFinalName;
            }
        } else {
            $fileName = md5(microtime());
            $fileName = $fileName . "." . $fileExtension;
        }
        //var_dump($fileName);
        $filePath = $targetDir . DS . $fileName;
//        echo '<pre>';
//        var_dump($chunk,$chunks);
        // Remove old temp files
        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
            }
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DS . $file;
                // If temp file is current file proceed to the next
                if ($tmpfilePath == "{$filePath}.part") {
                    continue;
                }
                // Remove temp file if it is older than the max age and is not the current file
                if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }
        // Open temp file
        if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
            die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }
        if (!empty($_FILES)) {
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
            }
            // Read binary input stream and append it to temp file
            if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        } else {
            if (!$in = @fopen("php://input", "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        }
        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }
        @fclose($out);
        @fclose($in);
        //var_dump($chunks, $chunk);
        // Check if file has been uploaded
        if (!$chunks || $chunk == $chunks - 1) {
            // Strip the temp .part suffix off
            rename("{$filePath}.part", $fileFinalPath);
            $filePath = $fileFinalPath;
        }
        @chmod($filePath, 0777);
        //var_dump(777);
        $campaignType = $this->input->post('type');
        $device = $this->input->post('platform');
        $campaignSubType = $this->input->post('campaignSubType');
        if (!$campaignType && !$device && !$campaignSubType) { //var_dump($size); exit;
            echo json_encode(['jsonrpc' => '2.0', 'title' => $name, 'status' => true, 'file_dir' => $filePath]);
            die;
        }
        if($fileExtension == 'txt' || $fileExtension == 'csv') {
            $emails = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $invalid = false;
            foreach ($emails as $email) {
                if(!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                    $invalid = true;
                    break;
                }
            }
            if($invalid) {
                echo json_encode(["jsonrpc" => "2.0", "title" => $name, "status" => false, "message" => 'The file contains invalid email addresses']);die;
            }
            echo json_encode(['jsonrpc' => '2.0', 'title' => $name, 'status' => true, 'file_dir' => $filePath, 'extension' => $fileExtension]);
            die;
        }
        if($campaignSubType == 'FB-VIDEO-VIEWS' || $campaignSubType == 'FB-VIDEO-CLICKS' || (($campaignSubType == 'FB-LOCAL-AWARENESS' || $campaignSubType == 'VIDEO_YAHOO') && ($fileExtension == 'mov' || $fileExtension == 'mp4'))) {
            if($campaignSubType == 'VIDEO_YAHOO') {
                $movie = new FFmpegMovie($filePath);
                $height = $movie->getFrameHeight();
                $width = $movie->getFrameWidth();
                if($width>=1200 && $height>=627) {
                    echo json_encode(['jsonrpc' => '2.0', 'title' => $name, 'status' => true, 'file_dir' => $filePath, 'creative_width' => $width, 'creative_height' => $height, 'extension' => $fileExtension]);
                    die;
                } else {
                    $errorMessage = "The Video dimensions doesn't correspond to Network's required sizes.";
                    print json_encode(["jsonrpc" => "2.0", "title" => $name, "status" => false, "message" => $errorMessage]);die;
                }
            }
            echo json_encode(['jsonrpc' => '2.0', 'title' => $name, 'status' => true, 'file_dir' => $filePath, 'creative_width' => 600, 'creative_height' => 600, 'extension' => $fileExtension]);
            die;
        }
        if($campaignType == 'DISPLAY_PROFILE') {
            echo json_encode(['jsonrpc' => '2.0', 'title' => $name, 'status' => true, 'file_dir' => $filePath, 'creative_width' => 160, 'creative_height' => 200]);
            die;
        }
        $size = $this->checkSize($filePath, $campaignType, $device, $campaignSubType);
        if ($size) {
            echo json_encode(['jsonrpc' => '2.0', 'title' => $name, 'status' => true, 'file_dir' => $filePath, 'creative_width' => $size['0'], 'creative_height' => $size['1']]);
            die;
        } else {
            unlink($targetDir . $_FILES["file"]["name"]);
            $errorMessage = "The image size doesn't correspond to Network's required sizes.";
            print json_encode(["jsonrpc" => "2.0", "title" => $name, "status" => false, "message" => $errorMessage]);die;
        }
    }
    /**
     * @description this function deletes Uploaded Files. Calls by Ajax
     * @return false on error
     */
    public function deleteUploadedFiles()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            $targetDir = 'uploads/tmp';
            $fname = $targetDir . $data["fname"];
            if (file_exists($fname)) {
                unlink($fname);
            } else {
                echo 'Files deleted!';
                die();
            }
        }
        return false;
    }
    /*
     *  @description this function checks wheather the image corresponds to the size or not
     *  @param targetDir is the image directory where is stored the image came by the porst
     *  @param size is the id of the size
     *  @returns true if the image's size corresponds to the size, otherwise false
     *
     */
    private function checkSize($targetDir, $campaignType, $device, $campaignSubType )
    {
        if(!$campaignType){
            $campaignType = 'DISPLAY';
        }
        $requiredSize = [];
        switch ($campaignType) {
            case 'DISPLAY':
                $requiredSize = [[1200, 628],[728, 90], [468, 60], [234, 60], [125, 125], [120, 600], [160, 600], [180, 150], [120, 240], [200, 200], [250, 250],
                [300, 250], [336, 280], [300, 600], [300, 1050], [320, 50], [970, 90], [970, 250], [627, 627], [1200, 627]];
                if($campaignSubType == "THIRD-PARTY-AD-TRACK") {
                    $requiredSize = 'unlimit';
                } else if($campaignSubType == 'DISPLAY-RETARGET') {
                    $requiredSize = [[728, 90], [468, 60], [234, 60], [125, 125], [120, 600], [160, 600], [180, 150], [120, 240], [200, 200], [250, 250],
                        [300, 250], [336, 280], [300, 600], [300, 1050], [320, 50], [970, 90], [970, 250]];
                }
                break;
            case 'FACEBOOK':
                if($campaignSubType == 'FB-MOBILE-NEWS-FEED') {
                    $requiredSize = [[560, 292], [1200, 628]];
                }
                if($campaignSubType == 'FB-MOBILE-APP-INSTALLS') {
                    $requiredSize = [[560, 292], [1200, 628]];
                }
                if($campaignSubType == 'FB-DESKTOP-RIGHT-COLUMN') {
                    $requiredSize = [[254, 133], [1200, 628]];
                }
                if($campaignSubType == 'FB-DESKTOP-NEWS-FEED') {
                    $requiredSize = [[470, 246], [1200, 628]];
                }
                if($campaignSubType == 'FB-PAGE-LIKE') {
                    $requiredSize = [[470, 246], [1200, 628]];
                }
                if($campaignSubType == 'FB-LOCAL-AWARENESS') {
                    $requiredSize = [[470, 246], [1200, 628]];
                }
                if($campaignSubType == 'FB-INSTAGRAM') {
                    $requiredSize = [[1200, 628]];
                }
                if($campaignSubType == 'FB-CAROUSEL-AD') {
                    $requiredSize = [[1200, 628]];
                }
                if($campaignSubType == 'FB-LEAD') {
                    $requiredSize = [[1200, 628]];
                }
                break;
            case 'AIRPUSH':
                if($campaignSubType == 'IN_APP') {
                    if($device == 'Mobile') {
                        $requiredSize = [[320, 50],[468, 60]];
                    }
                    else {
                        $requiredSize = [[728, 90],[300, 250]];
                    }
                }
                if($campaignSubType == 'OVERLAY_AD') {
                    if($device == 'Mobile') {
                        $requiredSize = [[300, 250],[320, 480]];
                    }
                    else {
                        $requiredSize = [[550, 480]];
                    }
                }
                if($campaignSubType == 'APPWALL') {
                    $requiredSize = [[72, 72]];
                }
                break;
            case 'YAHOO':
                if($campaignSubType == 'DISPLAY_YAHOO' || $campaignSubType == 'APP_INSTALL_YAHOO') {
                    $requiredSize = [[1200, 627]];
                }
                if($campaignSubType == 'OVERLAY_AD') {
                    if($device == 'Mobile') {
                        $requiredSize = [[300, 250],[320, 480]];
                    }
                    else {
                        $requiredSize = [[550, 480]];
                    }
                }
                if($campaignSubType == 'YAHOO_CAROUSEL') {
                    $requiredSize = [[1200, 627]];
                }
                if($campaignSubType == 'APPWALL') {
                    $requiredSize = [[72, 72]];
                }
                break;
        }
        $this->load->library('SimpleImage');
        $image = new SimpleImage();
        $image->load($targetDir);
        $height = $image->getHeight();
        $width = $image->getWidth();
        if (in_array([$width, $height], $requiredSize) || $requiredSize == 'unlimit') {
            return [$width, $height];
        } else {
            return false;
        }
    }
    public function clickmap_ajax()
    {
        //libxml_use_internal_errors(true);
        $cnt = 0;
        $parsedLinks = array();
        $xml = new DOMDocument();
        //die('dada');
        $xml->loadHTML($this->input->post("message"));
        $head = $xml->getElementsByTagName("head")->item(0);
        $documentHead = $this->DOMInnerHTML($head);
        $documentHead = "<head>" . $documentHead . "</head>";
        $body = $xml->getElementsByTagName('body')->item(0);
        $documentBody = $this->DOMInnerHTML($body);
        $documentBody = str_ireplace("<body>", "", $documentBody);
        $documentBody = str_ireplace("</body>", "", $documentBody);
        $bodyAttr = "";
        if ($body->hasAttributes()) {
            foreach($body->attributes as $attr) {
                $bodyAttr .= "{$attr->nodeName}='{$attr->nodeValue}' ";
            }
        }
        $documentBody = "<style>.click_border { outline: thick solid #64FE2E !important }</style><body {$bodyAttr}><div id='prodatafeed_hm_master_id'>" . $documentBody . "</div></body>";
        $documentFinal = "<html><head>" . $documentHead . "</head>" . $documentBody . "</html>";
        // lets reset and start over
        $xml->loadHTML($documentFinal);
        //$body->innerHTML = $documentBody;
        // create the click_border
        //$styleDom = $xml->createElement("style", ".click_border { outline: thick solid #64FE2E !important }");
        foreach($xml->getElementsByTagName('a') as $link)  {
            $oldLink = $link->getAttribute("href");
            $link->setAttribute('data-id', $cnt);
            $link->setAttribute('id', "hm_link_" . $cnt);
            $parsedLinks[] = array('href' => $link->getAttribute('href'), 'text' => $text, "link_id" => $cnt);
            $cnt++;
        }
        //$xml->appendChild($styleDom);
        $message = $xml->saveHtml();
        print json_encode(array("status" => "SUCCESS", "content" => $message, "links" => $parsedLinks));
    }
    public function reporting($campaign_id = null, $pdf = null){

       if($pdf == 1) {
            $this->viewArray['pdf'] = 1;
        }
        if (!$campaign_id){
            exit();
        }
        if($_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'] || $this->user['is_admin']) {
            $this->userid = null; //hard code for test;
        }
        $this->load->model('V2_campclick_click_model');
        $this->load->model('V2_campclick_impression_model');
        $this->load->model('V2_placements_reporting_model');
        $this->load->model('V2_campclick_like_model');
        $check_campaign_type = $this->V2_master_campaign_model->check_campaign_type_by_campaign_id($campaign_id);
    if($this->user_type == 'viewer') {
        if(($check_campaign_type[0]['campaign_type'] == 'FB-PAGE-LIKE') || ($check_campaign_type[0]['campaign_type'] == 'FB-VIDEO-VIEWS')) {
            $campaign = $this->V2_master_campaign_model->get_all_with_likes_and_impressions_by_id($this->parent_customer, $campaign_id);
        } else {
            $campaign = $this->V2_master_campaign_model->get_all_with_clicks_and_impressions_by_id($this->parent_customer, $campaign_id);
           }
        }
    else{
        if(($check_campaign_type[0]['campaign_type'] == 'FB-PAGE-LIKE') || ($check_campaign_type[0]['campaign_type'] == 'FB-VIDEO-VIEWS') || ($check_campaign_type[0]['campaign_type'] == 'FB-LEAD')) {
            if($check_campaign_type[0]['campaign_type'] == 'FB-LEAD') {
                $campaign = $this->V2_master_campaign_model->get_all_with_clicks_and_impressions_by_id($this->userid, $campaign_id);
                $this->load->model("V2_fb_lead_model");
                $leads = $this->V2_fb_lead_model->get_leads_by_campaign_id($campaign['id']);
                $this->viewArray['leads'] = $leads;
            } else {
                $campaign = $this->V2_master_campaign_model->get_all_with_likes_and_impressions_by_id($this->userid, $campaign_id);
            }
        } else {
            $campaign = $this->V2_master_campaign_model->get_all_with_clicks_and_impressions_by_id($this->userid, $campaign_id);
        }
    }
        if(!$campaign['id']){
           return redirect(base_url());
        }
        $campaign['cost'] = $this->V2_master_campaign_model->get_campaign_cost($campaign_id, $campaign['network_name']);
        if ($campaign['geotype'] == 'postalcode'){
            $this->viewArray['geotype_array'] = $this->V2_campclick_click_model->get_top_5_count_by_zip($campaign_id);
        }
        elseif($campaign['geotype'] == 'state'){
            $this->viewArray['geotype_array'] = explode(',', $campaign['state']);
        }
        else {
            $this->viewArray['geotype_array'] = explode(',', $campaign['country']);
        }
        if($campaign['geotype'] != 'postalcode') {
            $this->viewArray['geotype_array'] = array_unique($this->viewArray['geotype_array']);
        }
        $start_date = new DateTime($campaign['campaign_start_datetime']);
        $end_date = new DateTime($campaign['campaign_end_datetime']);
        $date_now = new DateTime(date('Y-m-d'));
        $campaign['total_days'] = $end_date->diff($start_date)->days;
        $campaign['rem_days'] = $date_now->diff($end_date)->days;
        $this->viewArray['date_now'] = date('m/d/Y');
        $this->viewArray['start_data'] = date('m/d/Y', strtotime($campaign['campaign_start_datetime']));
        $this->viewArray['js_date_now'] = date('Y-m-d');
        $this->viewArray['js_start_data'] = date('Y-m-d', strtotime($campaign['campaign_start_datetime']));
        $this->viewArray['ads'] = $this->V2_ad_model->get_ads_by_campaign_id($campaign_id);
        $this->viewArray['click_count'] = $campaign['total_clicks_count'];
        $this->viewArray['impression_count'] = $campaign['total_impressions_count'];
        $campaign['cost'] = $campaign['cost'] ? $campaign['cost'] : 0;
        $this->viewArray['campaign'] = $campaign;

        if($campaign['network_id'] == 5 ){
            $limit = 3;
        } else {
            $limit = 5;
        }
        $this->viewArray['places'] = $this->V2_placements_reporting_model->get_campaign_places_by_campaign_id($campaign_id, $limit);

        $browser = $this->agent->browser();
        if ($browser == 'Safari') {
            $this->viewArray['browser'] = 'Safari';
        }
        if($check_campaign_type[0]['campaign_type'] == 'FB-LEAD') {
            $this->parser->parse('v2/campaign/lead_reporting', $this->viewArray);
            return;
        }
        $this->parser->parse($this->view_file, $this->viewArray);
    }
    public function pdf_download($id) {
        $url = base_url().'/v2/campaign/reporting/'.$id.'/1';
        $fileName = 'report_'.time();
        chdir('../../../home');
        shell_exec("phantomjs pdf.js '".$url."' ".$fileName.".pdf");
        $file = $fileName.".pdf";
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
    }
    public function accounting_report($stime = "", $etime = "")
    {
//         $this->load->model('V2_campclick_impression_model');
//         $this->require_auth();
//         if($this->user_type == 'viewer') {
//             redirect(base_url());
//         }
//         // need to correction
//         $user = $this->ion_auth->user()->row();
//         if (!$this->viewArray['is_admin']) {
//             redirect(base_url());
//         }
//         $status = $this->input->post("status");
//         if(!$status) {
//             $status = "ACTIVE";
//         }
//         $campaigns = $this->V2_master_campaign_model->fulfillment_summary(null, $stime, $etime, $status);
// //        foreach($campaigns as $key => $campaign){
// //            if($campaign['max_budget']) {
// //                $cost = $this->V2_master_campaign_model->get_campaign_cost($campaign['id'], $campaign['network_name']);
// //            } else {
// //                $cost = 0;
// //            }
// //            $campaigns[$key]['cost'] = $cost;
// //        }
//         foreach($campaigns as $key => $campaign){
//             $cost = 0;
//             $rtb_cost = 0;
//             $clicks_count = null;
//             $impressions_count = null;
//             //$this->load->model('V2_campaign_cost_model');
//             //$daily_cost = $this->V2_campaign_cost_model->get_daily_cost_by_campaign_id($campaign['id']);
// //            if($daily_cost['cost']) {
// //                $daily_cost = $daily_cost['cost'];
// //            } else {
// //                $daily_cost = 0;
// //            }
//             //var_dump($daily_cost['cost']); exit;
//             if($campaign['max_budget']) {
//                 $cost = $this->V2_master_campaign_model->get_campaign_cost($campaign['id'], $campaign['network_name']);
//                 //$rtb_cost = $this->V2_campclick_impression_model->get_campaign_rtb_cost($campaign['id']); // comment this out as it was doubling the cost of the campaign on the system!
//             }
//             if($campaign['max_clicks']) {
//                 $this->load->model('V2_campclick_click_model');
//                 $clicks_count = $this->V2_campclick_click_model->get_campaign_click_count($campaign['id']);
//             }
//             if($campaign['max_impressions']) {
//                 $this->load->model('V2_campclick_impression_model');
//                 $impressions_count = $this->V2_campclick_impression_model->get_campaign_impressions_count($campaign['id']);
//             }
//             $campaigns[$key]['cost'] = $cost + $rtb_cost;
//             //$campaigns[$key]['daily_cost'] = $daily_cost;
//             if($campaign['is_thru_guarantee'] == 'Y' && !empty($campaign['max_impressions']) && ($impressions_count >= (int)$campaign['max_impressions'])){
//                     $campaigns[$key]['total_impressions_count'] = $campaign['max_impressions'];
//                 }else{
//                     $campaigns[$key]['total_impressions_count'] = $impressions_count;
//                 }
//             $campaigns[$key]['total_clicks_count'] = $clicks_count;
//         }
//         $this->viewArray['campaigns'] = $campaigns;
//         $this->viewArray['status'] = $status;
//         $this->parser->parse($this->view_file, $this->viewArray);
    }
    public function bidadjustment($stime = "", $etime = "")
    {
        $this->load->model('V2_campclick_impression_model');
        $this->require_auth();
        if($this->user_type == 'viewer') {
            redirect(base_url());
        }
        // need to correction
        $user = $this->ion_auth->user()->row();
        if (!$this->viewArray['is_admin']) {
            redirect(base_url());
        }
        $status = $this->input->post("status");
        if(!$status) {
            $status = "ACTIVE";
        }
        $campaigns = $this->V2_master_campaign_model->fulfillment_summary(null, $stime, $etime, $status);
        //var_dump($campaigns); exit;
        foreach($campaigns as $key => $campaign){
            $cost = 0;
            $clicks_count = null;
            $impressions_count = null;
            $this->load->model('V2_campaign_cost_model');
            $daily_cost = $this->V2_campaign_cost_model->get_daily_cost_by_campaign_id($campaign['id']);
//            if($daily_cost['cost']) {
//                $daily_cost = $daily_cost['cost'];
//            } else {
//                $daily_cost = 0;
//            }
            //var_dump($daily_cost['cost']); exit;
            if($campaign['max_budget']) {
                $cost = $this->V2_master_campaign_model->get_campaign_cost($campaign['id'], $campaign['network_name']);
            }
            $rtb_cost = $this->V2_campclick_impression_model->get_campaign_rtb_cost($campaign['id']);
            // if($campaign['max_clicks']) {
                $this->load->model('V2_campclick_click_model');
                $clicks_count = $this->V2_campclick_click_model->get_campaign_click_count($campaign['id']);
            // }
            if($campaign['max_impressions']) {
                $impressions_count = $this->V2_campclick_impression_model->get_campaign_impressions_count($campaign['id']);
            }
            $campaigns[$key]['cost'] = $cost + $rtb_cost;
            $campaigns[$key]['daily_cost'] = $daily_cost;
            $campaigns[$key]['total_impressions_count'] = $impressions_count;
            $campaigns[$key]['total_clicks_count'] = $clicks_count;
        }
        $this->viewArray['campaigns'] = $campaigns;
        $this->viewArray['status'] = $status;
        // echo "<pre>";
        // print_r($this->viewArray['campaigns']);
        // die;
        $this->parser->parse($this->view_file, $this->viewArray);
    }
    public function bid_up($campaign_id = "")
    {
        $campaign = $this->V2_master_campaign_model->get_by_id(null, $campaign_id);
        if (!$campaign ) {
            print json_encode(array("status" => "ERROR", "message" => "Invalid campaign ID" ));
            exit;
        }
        $new_bid = $campaign['bid'] + 0.0001;
        $update_campaign = $this->V2_master_campaign_model->update($campaign_id, array('bid' => $new_bid));
        $update = $this->V2_ad_model->update_all_by_campaign_id($campaign['id'], ['bid' => $new_bid]);
        if($update && $update_campaign) {
            $this->load->model('V2_log_model');
            $this->V2_log_model->create($campaign_id, $new_bid, 'bid');
            $this->load->model('Common_model');
            $result = $this->Common_model->update_bid($campaign);
            if($result['message']) {
                echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                exit;
            }
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Bid didn't update in db" ));
            exit;
        }
        print json_encode(array("status" => "SUCCESS", "message" => "Bid Updated to $ {$new_bid}", "bid" => $new_bid));
    }
    public function bid_down($campaign_id = "")
    {
        $campaign = $this->V2_master_campaign_model->get_by_id(null, $campaign_id);
        if (!$campaign ) {
            print json_encode(array("status" => "ERROR", "message" => "Invalid campaign ID" ));
            exit;
        }
        $new_bid = $campaign['bid'] - 0.0001;
        $update_campaign = $this->V2_master_campaign_model->update($campaign_id, array('bid' => $new_bid));
        $update = $this->V2_ad_model->update_all_by_campaign_id($campaign['id'], ['bid' => $new_bid]);
        if($update && $update_campaign) {
            $this->load->model('V2_log_model');
            $this->V2_log_model->create($campaign_id, $new_bid, 'bid');
            $this->load->model('Common_model');
            $result = $this->Common_model->update_bid($campaign);
            if($result['message']) {
                echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                exit;
            }
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Bid didn't update in db" ));
            exit;
        }
        print json_encode(array("status" => "SUCCESS", "message" => "Bid Updated to $ {$new_bid}", "bid" => $new_bid));
    }
    public function edit_bid($campaign_id = "", $new_bid = null)
    {
        if(!$new_bid) {
            print json_encode(array("status" => "ERROR", "message" => "New BID is empty" ));
            exit;
        }
        $campaign = $this->V2_master_campaign_model->get_by_id(null, $campaign_id);
        if (!$campaign ) {
            print json_encode(array("status" => "ERROR", "message" => "Invalid campaign ID" ));
            exit;
        }
        $update_campaign = $this->V2_master_campaign_model->update($campaign_id, array('bid' => $new_bid));
        $update = $this->V2_ad_model->update_all_by_campaign_id($campaign['id'], ['bid' => $new_bid]);
        if($update && $update_campaign) {
            $this->load->model('V2_log_model');
            $this->V2_log_model->create($campaign_id, $new_bid, 'bid');
            $campaign['bid'] = $new_bid;
            $this->load->model('Common_model');
            $result = $this->Common_model->update_bid($campaign);
            if($result['message']) {
                echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                exit;
            }
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Bid didn't update in db" ));
            exit;
        }
        print json_encode(array("status" => "SUCCESS", "message" => "Bid Updated to $ {$new_bid}", "bid" => $new_bid));
    }
    public function cap_save()
    {
        $campaign_id = $this->input->post("id");
        $daily_cap = $this->input->post("cap");
        $campaign = $this->V2_master_campaign_model->get_by_id(null, $campaign_id);
        if (!$campaign ) {
            print json_encode(array("status" => "ERROR", "message" => "Invalid campaign ID" ));
            exit;
        }
        $campaign_budget = $daily_cap;
        if($campaign['is_multiple']=='Y') {
            $campaign_budget = $daily_cap*$this->campaign_budget/100;
            $multiple_budget = $daily_cap*$this->multiple_budget/100;
        }
        if ($campaign['network_name'] == "FIQ" && $campaign['campaign_type'] == "TEXTAD") {
            $ads = $this->V2_ad_model->get_ads_by_campaign_id($campaign['id']);
            $daily_cap = $campaign_budget / count($ads);
            $this->V2_ad_model->update_all_by_campaign_id($campaign['id'], ['daily_cap' => $daily_cap]);
        } else {
            $update_campaign = $this->V2_master_campaign_model->update($campaign_id, array('budget' => $campaign_budget));
        }
        if ($update_campaign) {
            $this->load->model('V2_log_model');
            $this->V2_log_model->create($campaign_id, $daily_cap, 'daily cap');
            if($campaign['is_multiple']=='Y') {
                $this->load->model('V2_multiple_campaign_model');
                // reset multiple ad network data
                $multiple_update = $this->V2_multiple_campaign_model->update_by_campaign_id($campaign_id, array('budget'=>$multiple_budget));
                if(!$multiple_update) {
                    print json_encode(array("status" => "ERROR", "message" => "Campaign DAILY cap didn't update"));
                    exit;
                }
            }
            if ($campaign['network_campaign_status'] == 'ACTIVE') {
                $this->load->model('Common_model');
                $campaign['budget'] = $campaign_budget;
                $result = $this->Common_model->update($campaign, 'budget');
                if ($result['message']) {
                    echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                    exit;
                }
                if($campaign['is_multiple']=='Y') {
                    $campaign['network_name'] = 'GOOGLE';
                    $result = $this->Common_model->update($campaign, 'budget');
                    if ($result['message']) {
                        echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                        exit;
                    }
                }
            }
            print json_encode(array("status" => "SUCCESS", "message" => "OK", "cap" => sprintf("%.2f", $this->input->post("cap"))));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Daily cap didn't update in db"));
            exit;
        }
//                $this->Finditquick_model->set_cap($campaign['ppc_network_ad_id'], sprintf("%.2f", $this->input->post("cap")));
//                $this->Finditquick_model->pause_ad($campaign['ppc_network_ad_id']);
//                $this->Finditquick_model->resume_ad($campaign['ppc_network_ad_id']);
    }
    public function rolling_count($campaign_id = "", $duration = 10)
    {
        $start_date = date("Y-m-d H:i:s", strtotime("-{$duration} minutes"));
        $this->load->model('V2_campclick_click_model');
        $this->load->model('V2_campclick_impression_model');
        $clicks_count = $this->V2_campclick_click_model->get_campaign_click_count_between_date($campaign_id, $start_date);
        //$impressions_count = $this->V2_campclick_impression_model->get_campaign_impression_count_between_date($campaign_id, $start_date);
        //$count = $impressions_count + $clicks_count;
        $count = $clicks_count;
        //$this->load->model('V2_log_model');
        //$this->V2_log_model->create($campaign_id, $count, 'ROLLING_COUNT');
        print json_encode(array("status" => "SUCCESS", "count" => $count));
    }
    public function edit_campaign_status($campaign_id = "", $status)
    {
        $updated = $this->V2_master_campaign_model->update($campaign_id, array('network_campaign_status' => $status, 'campaign_status' => $status));
        //check if campaign updated in db and make changes in network too
        if ($updated) {
            // save into log table
            $this->load->model('V2_log_model');
            $this->V2_log_model->create($campaign_id, $status, 'status');
            $campaign = $this->V2_master_campaign_model->get_by_id(null, $campaign_id);
            if($campaign['is_multiple']=='Y') {
                $this->load->model('V2_multiple_campaign_model');
                // reset multiple ad network data
                $multiple_update = $this->V2_multiple_campaign_model->update_by_campaign_id($campaign_id, array('network_campaign_status' => $status, 'campaign_status' => $status));
                if(!$multiple_update) {
                    print json_encode(array("status" => "ERROR", "message" => "Campaign status didn't update"));
                    exit;
                }
            }
            // checking if campaign converted to live
            if ($campaign['campaign_is_converted_to_live'] == 'Y') {
                $this->load->model('Common_model');
                $result = $this->Common_model->update_campaign_status($campaign);
                if ($result['message']) {
                    echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                    exit;
                }
                if($campaign['is_multiple']=='Y') {
                    $campaign['network_name'] = 'GOOGLE';
                    $result = $this->Common_model->update_campaign_status($campaign);
                    if ($result['message']) {
                        echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                        exit;
                    }
                }
            }
            print json_encode(array("status" => "SUCCESS", "message" => "Campaign status successfully updated"));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Campaign status didn't update"));
            exit;
        }
    }
    public function make_campaign_completed($campaign_id = "")
    {
        $updated = $this->V2_master_campaign_model->update($campaign_id, array('network_campaign_status' => 'PAUSED', 'campaign_status' => 'COMPLETED'));
        //check if campaign converted to live then make changes in network too
        if ($updated) {
            // save into log table
            $this->load->model('V2_log_model');
            $this->V2_log_model->create($campaign_id, 'COMPLETED', 'status');
            // checking if campaign converted to live
            $campaign = $this->V2_master_campaign_model->get_by_id(null, $campaign_id);
            if($campaign['is_multiple']=='Y') {
                $this->load->model('V2_multiple_campaign_model');
                // reset multiple ad network data
                $multiple_update = $this->V2_multiple_campaign_model->update_by_campaign_id($campaign_id, array('network_campaign_status' => 'PAUSED', 'campaign_status' => 'COMPLETED'));
                if(!$multiple_update) {
                    print json_encode(array("status" => "ERROR", "message" => "Campaign didn't completed"));
                    exit;
                }
            }
            //if ($campaign['network_campaign_status'] == 'ACTIVE') {
                $this->load->model('Common_model');
                $result = $this->Common_model->update_campaign_status($campaign);
                if ($result['message']) {
                    echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                    exit;
                }
                if($campaign['is_multiple']=='Y') {
                    $campaign['network_name'] = 'GOOGLE';
                    $result = $this->Common_model->update_campaign_status($campaign);
                    if ($result['message']) {
                        echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                        exit;
                    }
                }
            //}
            print json_encode(array("status" => "SUCCESS", "message" => "Campaign status successfully updated"));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Campaign status didn't update"));
            exit;
        }
    }
    public function check_cap($campaign_id = "") {
        $campaign = $this->V2_master_campaign_model->get_by_id(null, $campaign_id);
        if (!$campaign || $campaign['campaign_type'] != "EMAIL") {
            print json_encode(array("status" => "ERROR", "message" => "Invalid campaign ID or campaign type is not EMAIL" ));
            exit;
        }
        $ad = $this->V2_ad_model->get_by_campaign_id($campaign_id)[0];
        if (!$ad) {
            print json_encode(array("status" => "ERROR", "message" => "Invalid AD ID" ));
            exit;
        }
        if ($ad['creative_is_active'] == 'Y') {
            $this->load->model('Common_model');
            $result = $this->Common_model->get_ad_report($ad, $campaign['network_name'], date("Y-m-d"));
            if ($result['message']) {
                echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                exit;
            }
        }
        if ($result['result'] != "") {
            $report = json_decode($result['result']);
            $report = $report->{date("Y-m-d")};
        }
        print json_encode(array("status"=>"SUCCESS", "report" => $report));
    }
    public function chart_data(){
        if ($this->input->is_ajax_request()){
            $campaign_id = $this->input->post('campaign_id');
            if (!$campaign_id){
                echo json_encode(['success' => false, 'message' => 'Campaign not found']);die;
            }
            // $campaign = $this->V2_master_campaign_model->get_by_id($this->userid, $campaign_id);
            if($this->user_type == 'viewer') {
                $campaign = $this->V2_master_campaign_model->get_by_id($this->parent_customer, $campaign_id);
            }
            else {
                if($this->user['is_admin']) {
                    $campaign = $this->V2_master_campaign_model->get_by_id(null, $campaign_id);
                } else {
                    $campaign = $this->V2_master_campaign_model->get_by_id($this->userid, $campaign_id);
                }
            }
            if (!$campaign){
                echo json_encode(['success' => false, 'message' => 'Campaign not found']);die;
            }
            $start_date = $this->input->post('start_date') ? $this->input->post('start_date') : date('Y-m-d', strtotime($campaign['campaign_start_datetime']));
            $end_date = $this->input->post('end_date') ? $this->input->post('end_date') : date('Y-m-d');
            $ad_id = $this->input->post('ad_id') ? $this->input->post('ad_id') : null;
            $this->load->model('V2_campclick_click_model');
            $this->load->model('v2_campclick_impression_model');
            $this->load->model('V2_campclick_like_model');
            $this->load->model('V2_demographics_reporting_model');
            $chart_array = [];
            if ($start_date == $end_date){
                $hours = [];
                if ($start_date == date('Y-m-d')){
                    $now_date = null;
                    for($i = 0; $i < 24; $i++ ){
                        $hours[] = date('H', strtotime("+{$i} hour"));
                    }
                }
                else {
                    for($i = 0; $i < 24; $i++ ){
                        $hours[] = date('H', strtotime("+{$i} hour", date('Y-m-d')));
                    }
                }
                if(($campaign['campaign_type'] == 'FB-PAGE-LIKE') || ($campaign['campaign_type'] == 'FB-VIDEO-VIEWS')) {
                    $click_data = $this->V2_campclick_like_model->get_impression_count_hourly($campaign_id, $ad_id, $now_date);
                } else if($campaign['campaign_type'] == 'FB-LEAD') {
                    $this->load->model("V2_fb_lead_model");
                    $click_data = $this->V2_fb_lead_model->get_click_count_hourly($campaign_id, $now_date, $ad_id);
                    //$click_data = $this->V2_campclick_click_model->get_click_count_hourly($campaign_id, $ad_id, $now_date);
                } else {
                    if($campaign['campaign_type'] == 'FB-VIDEO-CLICKS'){
                        $this->load->model('V2_video_watch_model');
                        $video_views = $this->V2_video_watch_model->get_video_watch_count_hourly($campaign_id, $start_date, $end_date);
                    }
                    $click_data = $this->V2_campclick_click_model->get_click_count_hourly($campaign_id, $ad_id, $now_date);
                }
                $click_array = [];
                $imp_array = [];
                $chart_array = [];
                $view_array = [];
                foreach ($click_data as $v){
                    $click_array[$v['hour']] = $v['count'];
                }
                $impression_data = $this->v2_campclick_impression_model->get_impression_count_hourly($campaign_id, $ad_id, $now_date);
                foreach ($impression_data as $v){
                    $imp_array[$v['hour']] = $v['count'];
                }
                foreach ($video_views as $v){
                    $view_array[$v['hour']] = $v['count'];
                }
                foreach ($hours as $hour){
                    if($campaign['campaign_type'] != 'FB-VIDEO-CLICKS'){
                        $chart_array[] = [
                                'date' => $hour,
                                'click_count' => isset($click_array[$hour]) ? $click_array[$hour] : 0,
                                'impression_count' => isset($imp_array[$hour]) ? $imp_array[$hour] : 0,
                        ];
                    }else{
                        $chart_array[] = [
                                'date' => $hour,
                                'click_count' => isset($click_array[$hour]) ? $click_array[$hour] : 0,
                                'views_count' => isset($view_array[$hour]) ? $view_array[$hour] : 0,
                        ];
                    }
                }
            }
            else {
                // print json_encode(array("status" => "eeeeeeeee", "message" => "Invalid campaign ID or campaign type is not EMAIL" ));
                if(($campaign['campaign_type'] == 'FB-PAGE-LIKE') || ($campaign['campaign_type'] == 'FB-VIDEO-VIEWS')) {
                    $click_data = $this->V2_campclick_like_model->get_impression_count($campaign_id, $start_date, $end_date, $ad_id);
                } else if($campaign['campaign_type'] == 'FB-LEAD') {
                    $this->load->model("V2_fb_lead_model");
                    $click_data = $this->V2_fb_lead_model->get_click_count($campaign_id, $start_date, $end_date, $ad_id);
                } else {
                    if($campaign['campaign_type'] == 'FB-VIDEO-CLICKS'){
                        $this->load->model('V2_video_watch_model');
                        $video_views = $this->V2_video_watch_model->get_video_watch_count($campaign_id, $start_date, $end_date);
//                      print_r($video_views);die;
                    }
                    $click_data = $this->V2_campclick_click_model->get_click_count($campaign_id, $start_date, $end_date, $ad_id);
                }
                $impression_data = $this->v2_campclick_impression_model->get_impression_count($campaign_id, $start_date, $end_date, $ad_id);
                $click_array = [];
                $imp_array = [];
                $view_array = [];
                foreach ($click_data as $v){
                    $click_array[$v['date']] = $v['click_count'];
                }
                foreach ($impression_data as $v){
                    $imp_array[$v['date']] = $v['impression_count'];
                }
                foreach ($video_views as $v){
                    $view_array[$v['date']] = $v['views_count'];
                }
                foreach ($this->createDateRangeArray($start_date, $end_date) as $data){
                    if($campaign['campaign_type'] != 'FB-VIDEO-CLICKS'){
                        $chart_array[]=[
                                'date' => date('Y-m-d', strtotime($data)),
                                'click_count' => isset($click_array[$data]) ? $click_array[$data] : 0,
                                'impression_count' => isset($imp_array[$data]) ? $imp_array[$data] : 0,
                        ];
                    }else{
                        $chart_array[]=[
                                'date' => date('Y-m-d', strtotime($data)),
                                'click_count' => isset($click_array[$data]) ? $click_array[$data] : 0,
                                'views_count' => isset($view_array[$data]) ? $view_array[$data] : 0,
                        ];
                    }
                }
            }
            $pie_charts_data = $this->V2_campclick_click_model->get_pie_chart_data($campaign_id, $start_date, $end_date, $ad_id);
            $geo_data = [];
            if ($campaign['geotype'] == 'postalcode'){
                $geo_data = $this->geolocation_ajax(true, $campaign['zip'], $campaign['radius']);
                $geo_data['source_location'] = $this->V2_campclick_click_model->get_all_count_by_zip_and_radius($geo_data['source_location'], $campaign_id, $ad_id);
                // this method very slow for using don't use it
                //                foreach ($geo_data['source_location'] as $key => $v){
                //                    $geo_data['source_location'][$key]['click_count'] = $this->V2_campclick_click_model->get_count_by_radius($campaign_id, $v['radius'], $v['latitude'], $v['longitude'], $ad_id);
                    //                }
                }
                $clicks_state = [];
                if ($campaign['geotype'] == 'state' && $campaign['country'] == 'US'){
                    $data = $this->V2_campclick_click_model->get_count_by_state($campaign_id, $start_date, $end_date, $ad_id);
                    foreach ($data as $click){
                        $clicks_state[$click['state']] = $click['count'];
                    }
                }
                if ($campaign['campaign_type'] == 'FB-VIDEO-VIEWS' || $campaign['campaign_type'] == 'VIDEO_YAHOO' || $campaign['campaign_type'] == 'FB-VIDEO-CLICKS') {
                    $demograpics_data = $this->V2_demographics_reporting_model->get_chart_data($campaign_id, $start_date, $end_date);
                    $this->load->model('V2_video_watch_model');
                    $video_data = $this->V2_video_watch_model->get_video_watch($campaign_id, $start_date, $end_date);
                    echo json_encode(['success' => true, 'click_data' => $chart_array, 'pie_chart' => $pie_charts_data, 'geo_data' => $geo_data, 'clicks_state' => $clicks_state, 'demograpics_data' => $demograpics_data, 'video_data' => $video_data ]);die;
                } else {
                    $demograpics_data = $this->V2_demographics_reporting_model->get_chart_data($campaign_id, $start_date, $end_date);
                    echo json_encode(['success' => true, 'click_data' => $chart_array, 'pie_chart' => $pie_charts_data, 'geo_data' => $geo_data, 'clicks_state' => $clicks_state, 'demograpics_data' => $demograpics_data]);die;
                }
        }
        else {
            redirect(base_url());
        }
    }
    public function chart_data1(){
        if ($this->input->is_ajax_request()){
            $start_date = $this->input->post('start_date') ? $this->input->post('start_date') :  date('Y-m-d');
            $end_date = $this->input->post('end_date') ? $this->input->post('end_date') : date('Y-m-d');
            $so = $this->input->post('so') ? $this->input->post('so') : null;
            $this->load->model('V2_campclick_click_model');
            $this->load->model('v2_campclick_impression_model');
            $this->load->model('V2_campclick_like_model');
            $this->load->model('V2_demographics_reporting_model');
            $this->load->model('V2_master_campaign_model');
            $this->load->model('V2_email_campaign_reporting_model');
            if($this->user['is_admin']) {
                $this->userid = null;
            }
            $chart_array = [];
            $channels = [
                'DISPLAY' => [
                    'DISPLAY',
                    'DISPLAY_YAHOO',
                    'APP_INSTALL_YAHOO'
                ],
                'DISPLAY-RETARGET' => [
                    'DISPLAY-RETARGET',
                ],
                'SOCIAL' => [
                    'FB-MOBILE-NEWS-FEED',
                    'FB-DESKTOP-RIGHT-COLUMN',
                    'FB-DESKTOP-NEWS-FEED',
                    'FB-PAGE-LIKE',
                    'FB-CAROUSEL-AD',
                    'FB-LEAD',
                    'FB-MOBILE-APP-INSTALLS',
                    'FB-PROMOTE-EVENT',
                    'FB-INSTAGRAM',
                    'FB-LOCAL-AWARENESS'
                ],
                'VIDEO' => [
                    'FB-VIDEO-CLICKS',
                    'FB-VIDEO-VIEWS',
                    'VIDEO_YAHOO',
                    'FB-INSTAGRAM-VIDEO'
                ],
                'TEXTAD' => [
                    'TEXTAD',
                ],
                'RICH_MEDIA' => [
                    'DIALOG_CLICK_TO_CALL',
                    'APPWALL',
                    'LANDING_PAGE',
                    'IN_APP',
                    'OVERLAY_AD',
                    'PUSH_CLICK_TO_CALL',
                    'ABSTRACT_BANNER_LARGE',
                    'ABSTRACT_BANNER_SMALL',
                    'ABSTRACT_BANNER_LARGE_CC',
                    'ABSTRACT_BANNER_LARGE_CM',
                    'ABSTRACT_BANNER_SMALL_CM',
                    'ABSTRACT_BANNER_SMALL_CC',
                    'RICH_MEDIA_INTERSTITIAL'
                ],
                'EMAIL' => [
                    'EMAIL',
                ],
            ];

            $so_compaign = $this->V2_master_campaign_model->get_so($so,$start_date,$end_date,10);
            $email_reporting = $this->V2_master_campaign_model->get_email_campaigns_by_so($this->userid,$start_date,$end_date,$so,10);
            if ($start_date == $end_date){
                $hours = [];
                if ($start_date == date('Y-m-d')){
                    $now_date = null;
                    for($i = 0; $i < 24; $i++ ){
                        $hours[] = date('H', strtotime("+{$i} hour"));
                    }
                }
                else {
                    for($i = 0; $i < 24; $i++ ){
                        $hours[] = date('H', strtotime("+{$i} hour", date('Y-m-d')));
                    }
                }
                $so_numbers = $this->V2_master_campaign_model->get_so($so,$start_date);
                $click_data = $this->V2_campclick_click_model->get_click_count_hourly_for_combine($this->userid, $so, $now_date);
                $email_click_data = $this->V2_email_campaign_reporting_model->get_click_count_hourly_for_combine($this->userid, $so, $now_date);
                $click_array = [];
                $click_array_email = [];
                $imp_array = [];
                $chart_array = [];
                foreach ($click_data as $v){
                    $click_array[$v['campaign_type']][$v['hour']] = $v['count'];
                }
                foreach ($email_click_data as $w){
                    $click_array_email[$w['campaign_type']][$w['hour']] = $w['count'];
                }
                $impression_data = $this->v2_campclick_impression_model->get_impression_count_hourly_for_combine($this->userid, $so, $now_date);
                foreach ($impression_data as $v){
                    $imp_array[$v['hour']] = $v['count'];
                }
                $data_rep = [];
                foreach ($hours as $hour){
                    $chart_array = [];
                    $chart_array['date'] = $hour;
                    $chart_array['impression_count'] = isset($imp_array[$hour]) ? $imp_array[$hour] : 0;
                    foreach ($channels as $chanel_type => $chanel) {
                        $click_count = 0;
                        foreach ($chanel as $campaign_type){
                            if($campaign_type == 'EMAIL'){
                                $email_click = isset($click_array_email[$campaign_type][$hour]) ? $click_array_email[$campaign_type][$hour] : 0;
                                $click_count = $click_count + $email_click;
                            }else{
                                $click = isset($click_array[$campaign_type][$hour]) ? $click_array[$campaign_type][$hour] : 0;
                                $click_count = $click_count + $click;
                            }
                        }
                        $chart_array[strtolower($chanel_type).'_click_count'] = $click_count;
                    }
                    $data_rep[] = $chart_array;
                }
            }
            else {
                $click_data = $this->V2_campclick_click_model->get_click_count_for_combine($this->userid, $so, $start_date, $end_date);
                $email_click_data = $this->V2_email_campaign_reporting_model->get_click_count_for_combine($this->userid, $so, $start_date, $end_date);
                $impression_data = $this->v2_campclick_impression_model->get_impression_count_for_combine($this->userid, $so, $start_date, $end_date);
                $so_numbers = $this->V2_master_campaign_model->get_so($so,$start_date,$end_date);
                $click_array = [];
                $imp_array = [];
                $chart_array = [];
                $click_array_email = [];
                foreach ($click_data as $v){
                    $click_array[$v['campaign_type']][$v['date']] = $v['count'];
                }
                foreach ($impression_data as $v){
                    $imp_array[$v['date']] = $v['count'];
                }
                foreach ($email_click_data as $w){
                    $click_array_email[$w['campaign_type']][$w['date']] = $w['count'];
                }
                foreach ($this->createDateRangeArray($start_date, $end_date) as $data){
                    $chart_array[]=[
                        'date' => date('Y-m-d', strtotime($data)),
                        'click_count' => isset($click_array[$data]) ? $click_array[$data] : 0,
                        'impression_count' => isset($imp_array[$data]) ? $imp_array[$data] : 0,
                    ];
                }
                $data_rep = [];
                foreach ($this->createDateRangeArray($start_date, $end_date) as $data){
                    $chart_array = [];
                    $chart_array = [];
                    $chart_array['date'] = date('Y-m-d', strtotime($data));
                    $chart_array['impression_count'] = isset($imp_array[$data]) ? $imp_array[$data] : 0;
                    foreach ($channels as $chanel_type => $chanel) {
                        $click_count = 0;
                        foreach ($chanel as $campaign_type){
                            if($campaign_type == 'EMAIL'){
                                $email_click = isset($click_array_email[$campaign_type][$data]) ? $click_array_email[$campaign_type][$data] : 0;
                                $click_count = $click_count + $email_click;
                            }else{
                                $click = isset($click_array[$campaign_type][$data]) ? $click_array[$campaign_type][$data] : 0;
                                $click_count = $click_count + $click;
                            }
                        }
                        $chart_array[strtolower($chanel_type).'_click_count'] = $click_count;
                    }
                    $data_rep[] = $chart_array;
                }
            }
            $geo_data = [];
            $clicks_state = [];
            $demograpics_data = $this->V2_demographics_reporting_model->get_chart_data_for_combine($this->userid, $so, $start_date, $end_date);
            $this->load->model("V2_placements_reporting_model");
            $places = $this->V2_placements_reporting_model->get_places($this->userid, $so, $start_date, $end_date, 5);
            echo json_encode(['success' => true, 'click_data' => $data_rep, 'demograpics_data' => $demograpics_data, 'places' => $places , 'so_numbers' => $so_numbers ,'email_reporting' =>$email_reporting ]);die;
        }
        else {
            redirect(base_url());
        }
    }
    public function set_schedule($campaign_id = "") {
        $campaign = $this->V2_master_campaign_model->get_by_id(null, $campaign_id);
        if (!$campaign || $campaign['network_name'] != "FIQ") {
            print json_encode(array("status" => "ERROR", "message" => "Invalid campaign ID or campaign type is not EMAIL" ));
            exit;
        }
        $ads = $this->V2_ad_model->get_by_campaign_id($campaign_id)[0];
        if (!$ads) {
            print json_encode(array("status" => "ERROR", "message" => "Invalid AD ID" ));
            exit;
        }
        foreach($ads as $ad) {
            if ($ad['creative_is_active'] == 'Y') {
                $this->load->model('Common_model');
                $result = $this->Common_model->update_schedule($ad, $campaign['network_name']);
                if ($result['message']) {
                    echo json_encode(array("status" => "ERROR", "message" => $result['message']));
                    exit;
                }
            }
        }
        //$this->Finditquick_model->set_schedule($id, ""); // we keep the schedule blank as it will give us 8AM-Midnight clicks
        print json_encode(array("status" => "SUCCESS", "message" => "Schedule Cleared"));
    }
    public function clickcap($campaign_id = "", $cap_per_hour = 100)   {
        $campaign = $this->V2_master_campaign_model->get_by_id(null, $campaign_id);
        if (!$campaign || $campaign['campaign_type'] != "EMAIL") {
            print json_encode(array("status" => "ERROR", "message" => "Invalid campaign ID or campaign type is not EMAIL" ));
            exit;
        }
        $updated = $this->V2_master_campaign_model->update($campaign_id, array('cap_per_hour' => $cap_per_hour));
        //check if campaign converted to live then make changes in network too
        if ($updated) {
            // save into log table
            $this->load->model('V2_log_model');
            $this->V2_log_model->create($campaign_id, $cap_per_hour, 'cap per hour');
            print json_encode(array("status" => "SUCCESS", "message" => "OK", "click_cap" => $cap_per_hour ));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Campaign cap_per_hour didn't update"));
            exit;
        }
    }
    public function geolocation($campaign_id = "")  {
        $campaign = $this->V2_master_campaign_model->get_by_id(null, $campaign_id);
        $campaigns = $this->V2_master_campaign_model->get_by_geotype_and_status(null, 'postalcode', 'ACTIVE');
        $this->viewArray['campaigns'] = $campaigns;
        $this->viewArray['selected_campaign'] = $campaign;
        $this->parser->parse($this->view_file, $this->viewArray);
    }
    public function geolocation_ajax($return = false, $zip_codes = null, $radius = null) {
        $zip_codes = $zip_codes ? $zip_codes : $this->input->post("zip");
        $radius = $radius ? $radius : $this->input->post("radius");
        $zipcodes = array_map('trim', explode(",", $zip_codes));
        $resultGeo = array();
        $source_locations = array();
        $this->load->model('Zip_model');
        if($return) {
            $source_locations = $this->Zip_model->match_all_zip_to_geo($zip_codes, $radius);
            return ['locations' => $resultGeo, 'source_location' => $source_locations];
        }
        foreach($zipcodes as $zip) {
            if ($zip == "" || $zip == "undefined")
                continue;
            $r = $this->Zip_model->find_locations($zip, $radius);
            $resultGeo = array_merge($resultGeo, $r);
            $r = $this->Zip_model->match_zip_to_geo($zip, $radius);
            if($r) {
                $source_locations[] = $r;
            }
        }
        print json_encode(array("status" => "SUCCESS", "locations" => $resultGeo, "source_location" => $source_locations));
    }
    public function geolocation_ad($campaign_id = "") {
        $campaign = $this->V2_master_campaign_model->get_by_id(null, $campaign_id);
        if (!$campaign || $campaign['geotype'] != "postalcode") {
            print json_encode(array("status" => "ERROR", "message" => "Invalid campaign ID or campaign geotype is not POSTALCODE" ));
            exit;
        }
//        $zipcode = $this->Zip_model->get_campaign_zipcode($io);
//        $radius = $this->Zip_model->get_campaign_radius($io);
        print json_encode(array("status" => "SUCCESS", "zip" => $campaign['zip'], "radius" => $campaign['radius']));
    }
    private function get_address($lat, $lng){
        $response = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lng&sensor=true");
        $response_array = json_decode($response, true);
        if (isset($response_array['status']) && $response_array['status'] == 'OK'){
            return $response_array['results'][0]['address_components'][5]['short_name'];
        }
    }
    public function make_campaign_multiple($campaign_id = "") {
        $campaign = $this->V2_master_campaign_model->get_by_id(null, $campaign_id);
        $this->load->model('V2_multiple_campaign_model');
        $this->V2_multiple_campaign_model->create($campaign);
        if (!$campaign || $campaign['geotype'] != "postalcode") {
            print json_encode(array("status" => "ERROR", "message" => "Invalid campaign ID or campaign geotype is not POSTALCODE" ));
            exit;
        }
//        $zipcode = $this->Zip_model->get_campaign_zipcode($io);
//        $radius = $this->Zip_model->get_campaign_radius($io);
        print json_encode(array("status" => "SUCCESS", "zip" => $campaign['zip'], "radius" => $campaign['radius']));
    }
    public function createDateRangeArray($strDateFrom, $strDateTo){
        // takes two dates formatted as YYYY-MM-DD and creates an
        // inclusive array of the dates between the from and to dates.
        // could test validity of dates here but I'm already doing
        // that in the main script
        $aryRange=array();
        $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
        $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));
        if ($iDateTo>=$iDateFrom)
        {
            array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
            while ($iDateFrom<$iDateTo)
            {
                $iDateFrom+=86400; // add 24 hours
                array_push($aryRange,date('Y-m-d',$iDateFrom));
            }
        }
        return $aryRange;
    }
    public function email_reporting($campaign_id = "", $offset = 0, $range = "hour", $start_date = "", $end_date = "")  {
        $this->require_auth();
        $this->load->model("V2_master_campaign_model");
        $this->load->model("V2_email_campaign_reporting_model");
        $this->load->model("V2_email_campaign_additional_reporting_model");
        $this->load->model("V2_email_campaign_link_reporting_model");
        $campaign = $this->V2_master_campaign_model->get_by_id($this->user_id, $campaign_id);
        $this->viewArray['campaign'] = $campaign;
        $this->viewArray['io'] = $campaign['io'];
//        $this->load->library('pagination');
//        $config['base_url'] = base_url()."campclick/report/$io";
//        $config['num_links'] = 4;
//        $config['uri_segment'] = 4;
//        $config['per_page'] = 20;
//        $config['total_rows'] = $this->Campclick_model->get_all_data($io, $range, '', '', '', '', '', true);
//        $this->pagination->initialize($config);
//        $this->viewArray['pagination_link'] = $this->pagination->create_links();
//        $this->viewArray['domain_name'] = $this->session->userdata("domain_name");
        $this->viewArray['range'] = $range;
        $this->viewArray['start_date'] = $start_date;
        $this->viewArray['end_date'] = $end_date;
        $this->viewArray['offset'] = (int)$offset;
        // check to see if the image exists, if not, lets trigger an update
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://45.33.7.188:3000/screenshots/{$campaign['io']}.png");
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (curl_exec($ch) === false) {
            // image does not exist
            $this->load->model("Screencapture_model");
            $this->Screencapture_model->width = 600;
            $this->Screencapture_model->height = 800;
            $this->Screencapture_model->url = "http://www.report-site.com/campclick/screencapture/{$campaign['io']}";
            $this->Screencapture_model->filename = "{$campaign['io']}.png";
            $this->Screencapture_model->capture();
        }
        curl_close($ch);
//        $this->viewArray['report'] = $this->Campclick_model->report_by_io($io, $range, $start_date, $end_date);
//        $this->viewArray['all_data'] = $this->Campclick_model->get_all_data($io, $range, $start_date, $end_date,'',$offset,$config['per_page'], false);
        //$this->Campclick_model->get_click_count_by_hour('2013-03-05');
//        $this->viewArray['moreinfo_url'] = current_url();
        if($range=='hour') {
            $campaign_click_data = $this->V2_email_campaign_reporting_model->get_clicks_count_hourly($campaign_id);
            $click_data = $this->V2_email_campaign_link_reporting_model->get_clicks_count_hourly($campaign_id);
            $type = 'hour';
            $title = "Last 24-Hours";
        } elseif($range=='month') {
            $start_date = date('Y-m-d h:i:s',strtotime("-30 day"));
            $end_date = date('Y-m-d h:i:s');
            $campaign_click_data = $this->V2_email_campaign_reporting_model->get_clicks_count($campaign_id, $start_date, $end_date);
            $click_data = $this->V2_email_campaign_link_reporting_model->get_clicks_count($campaign_id, $start_date, $end_date);
            //var_dump($click_data); exit;
            $type = 'date';
            $title = "Last 30-Days";
        } else {
            $campaign_click_data = $this->V2_email_campaign_reporting_model->get_clicks_count($campaign_id, $start_date, $end_date);
            $click_data = $this->V2_email_campaign_link_reporting_model->get_clicks_count($campaign_id, $start_date, $end_date);
            $title = $start_date.' to '.$end_date;
            $type = 'date';
        }
        // var_dump($click_data);
        $click_array = [];
        $imp_array = [];
        if($type == 'date') {
            foreach ($click_data as $v){
                $click_array[date('"M-d"',strtotime($v[$type]))] = $v['clicks_count'];
            }
            foreach ($campaign_click_data as $v){
                $mobile_array[date('"M-d"',strtotime($v[$type]))] = $v['mobile_clicks_count'];
                $unique_clicks_array[date('"M-d"',strtotime($v[$type]))] = $v['unique_clicks_count'];
                $impressions_array[date('"M-d"',strtotime($v[$type]))] = $v['impressions_count'];
            }
        } else {
            foreach ($click_data as $v){
                $click_array[$v[$type]] = $v['clicks_count'];
            }
            foreach ($campaign_click_data as $v){
                $mobile_array[$v[$type]] = $v['mobile_clicks_count'];
                $unique_clicks_array[$v[$type]] = $v['unique_clicks_count'];
                $impressions_array[$v[$type]] = $v['impressions_count'];
            }
        }
        $click_data = implode(',',$click_array);
        $mobile_data = implode(',',$mobile_array);
        $unique_clicks_data = implode(',',$unique_clicks_array);
        $impressions_data = implode(',',$impressions_array);
        $rep = array(
            "click_data" => $click_data,
            "mobile_data" => $mobile_data,
            "unique_clicks_data" => $unique_clicks_data,
            "impressions_data" => $impressions_data,
            "date" => implode(',',array_keys($click_array)),
            "title" => $title,
        );
        $this->viewArray['rep'] = $rep;
        $this->viewArray['report'] = $this->V2_email_campaign_link_reporting_model->get_clicks_count_by_campaign_id($campaign_id);
        $this->viewArray['total_report'] = $this->V2_email_campaign_reporting_model->get_total_clicks_count_by_campaign_id($campaign_id);
        $this->viewArray['total_report']['clicks_count'] = $this->V2_email_campaign_link_reporting_model->get_total_clicks_count_by_campaign_id($campaign_id)['clicks_count'];
        $this->viewArray['additional_report'] = $this->V2_email_campaign_additional_reporting_model->get_all_by_campaign_id($campaign_id);
        //$this->viewArray['all_data'] = $this->Campclick_model->get_all_data($io, $range, $start_date, $end_date,'',$offset,$config['per_page'], false);
        $this->parser->parse($this->view_file, $this->viewArray);
    }
    public function email_reporting_for_pdf($campaign_id)   {
        if(!filter_var($campaign_id, FILTER_VALIDATE_INT)) {
            throw new exception("campaign_id has to be integer number");
            exit;
        }
        $this->load->model("V2_users_model");
        $this->load->model("V2_email_campaign_reporting_model");
        $this->load->model("V2_email_campaign_additional_reporting_model");
        $this->load->model("V2_email_campaign_link_reporting_model");
        $campaign = $this->V2_master_campaign_model->get_by_id($this->user_id, $campaign_id); //var_dump($campaign); exit;
        // get user
        $user = $this->V2_users_model->get_by_id($campaign['userid']);
        if($user['domain_id']) {
            $domain = $this->V2_domains_model->get_domain($user['domain_id']);
            $this->viewArray['domain_data'] = $domain;
        }
        $this->viewArray['campaign'] = $campaign;
        $this->viewArray['io'] = $campaign['io'];
        // check to see if the image exists, if not, lets trigger an update
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://45.33.7.188:3000/screenshots/{$campaign['io']}.png");
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (curl_exec($ch) === false) {
            // image does not exist
            $this->load->model("Screencapture_model");
            $this->Screencapture_model->width = 600;
            $this->Screencapture_model->height = 800;
            $this->Screencapture_model->url = "http://www.report-site.com/campclick/screencapture/{$campaign['io']}";
            $this->Screencapture_model->filename = "{$campaign['io']}.png";
            $this->Screencapture_model->capture();
        }
        curl_close($ch);
        $this->viewArray['report'] = $this->V2_email_campaign_link_reporting_model->get_clicks_count_by_campaign_id($campaign_id);
        $this->viewArray['total_report'] = $this->V2_email_campaign_reporting_model->get_total_clicks_count_by_campaign_id($campaign_id);
        $this->viewArray['so'] = $this->V2_email_campaign_reporting_model->get_so_by_campaign_id($campaign_id);
        $this->viewArray['so'] = $this->viewArray['so'][0]["campaign_so"];
        $this->viewArray['additional_report'] = $this->V2_email_campaign_additional_reporting_model->get_all_by_campaign_id($campaign_id);
        $this->parser->parse($this->view_file, $this->viewArray);
    }

    public function generate_report($campaign_id,$reportrange = null) {


        $this->load->library('Wkhtmltopdf');
        $this->load->model('V2_log_model');

        if(is_null($reportrange))
        $reportrange = "all";
        $tmpcampaign = $this->V2_master_campaign_model->get_by_id(null, $campaign_id);
        $name = $tmpcampaign['name'].' '.$tmpcampaign['io'];
        $name = str_replace('/','',$name);
        $path = '/var/www/html/v2/pdf/'.$name;
        $url = base_url()."v2/campaign/reporting_for_pdf/".$campaign_id."/".$reportrange;

        $this->wkhtmltopdf->__set('url', $url);
        $this->wkhtmltopdf->__set('mode', 'MODE_DOWNLOAD');
        $this->wkhtmltopdf->__set('title', 'ProData Media Campaign Report');
        if(is_null($reportrange))
            $reportrange = "all";
        switch ($reportrange) {
            case "24":
                $this->wkhtmltopdf->__set('path', $path.' for 24H.pdf');
                break;
            case "48":
                $this->wkhtmltopdf->__set('path', $path.' for 48H.pdf');
                break;
            case "96":
                $this->wkhtmltopdf->__set('path', $path.' for 96H.pdf');
                break;
            default:
                $this->wkhtmltopdf->__set('path', $path.' for all time.pdf');
                break;
        }
        $result = $this->wkhtmltopdf->downloadPDF();



    }

    public function reporting_for_pdf($campaign_id, $reportrange = null) {
        if(is_null($reportrange))
            $reportrange = "all";

        $tmpcampaign = $this->V2_master_campaign_model->get_by_id(null, $campaign_id);
        $start_time = strtotime($tmpcampaign['campaign_start_datetime']);

        $start_date = date('Y-m-d 00:00:00', $start_time);

        switch ($reportrange) {
            case "24":
                $end_date = date('Y-m-d 23:59:59',strtotime('+1 day',$start_time));
                break;

            case "48":
                $end_date = date('Y-m-d 23:59:59',strtotime('+2 days', $start_time));
                break;

            case "96":
                $end_date = date('Y-m-d 23:59:59',strtotime('+4 days', $start_time));
                break;

            default:
                $end_date = date('Y-m-d 23:59:59');
                break;
        }
        //echo "<pre>";print_r($start_date);print_r($end_date);die;
        if($_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'] || $this->user['is_admin']) {

            $this->userid = null; //hard code for test;
        }

        $this->load->model('V2_campclick_click_model');
        $this->load->model('V2_campclick_impression_model');
        $this->load->model('V2_placements_reporting_model');

        $this->load->model('V2_campclick_like_model');
        $this->load->model('V2_demographics_reporting_model');
        $this->load->model('V2_ad_model');

        $check_campaign_type = $this->V2_master_campaign_model->check_campaign_type_by_campaign_id($campaign_id);

        if($this->user_type == 'viewer') {
            if(($check_campaign_type[0]['campaign_type'] == 'FB-PAGE-LIKE') || ($check_campaign_type[0]['campaign_type'] == 'FB-VIDEO-VIEWS')) {
                $campaign = $this->V2_master_campaign_model->get_all_with_likes_and_impressions_by_id_with_range_date($this->parent_customer, $campaign_id,$start_date,$end_date);
            } else {
                $campaign = $this->V2_master_campaign_model->get_all_with_clicks_and_impressions_by_id_with_range_date($this->parent_customer, $campaign_id,$start_date,$end_date);
            }
        }
        else{
            if(($check_campaign_type[0]['campaign_type'] == 'FB-PAGE-LIKE') || ($check_campaign_type[0]['campaign_type'] == 'FB-VIDEO-VIEWS') || ($check_campaign_type[0]['campaign_type'] == 'FB-LEAD')) {

                if($check_campaign_type[0]['campaign_type'] == 'FB-LEAD') {
                    $campaign = $this->V2_master_campaign_model->get_all_with_clicks_and_impressions_by_id_with_range_date($this->userid, $campaign_id,$start_date,$end_date);
                    $this->load->model("V2_fb_lead_model");
                    $leads = $this->V2_fb_lead_model->get_leads_by_campaign_id($campaign['id']);
                    $this->viewArray['leads'] = $leads;
                } else {
                    $campaign = $this->V2_master_campaign_model->get_all_with_likes_and_impressions_by_id_with_range_date($this->userid, $campaign_id,$start_date,$end_date);
                }

            } else {
                $campaign = $this->V2_master_campaign_model->get_all_with_clicks_and_impressions_by_id_with_range_date($this->userid, $campaign_id,$start_date,$end_date);
            }
        }

        if(!$campaign['id']){
            return redirect(base_url());
        }
        $campaign['cost'] = $this->V2_master_campaign_model->get_campaign_cost($campaign_id, $campaign['network_name']);

        if(($check_campaign_type[0]['campaign_type'] == 'FB-PAGE-LIKE') || ($check_campaign_type[0]['campaign_type'] == 'FB-VIDEO-VIEWS')) {
            $ads = $this->V2_ad_model->get_with_likes_by_campaign_id($campaign_id);
        } else {
            $ads = $this->V2_ad_model->get_with_clicks_by_campaign_id($campaign_id);
        }
        $this->viewArray['ads'] = $ads; //$this->V2_ad_model->get_ads_by_campaign_id($campaign_id);

        $this->viewArray['click_count'] = $campaign['total_clicks_count'];

        if($campaign['is_thru_guarantee'] == 'Y' && !empty($campaign['max_impressions']) && ($campaign['total_impressions_count'] >= $campaign['max_impressions'])){
                $this->viewArray['impression_count'] = $campaign['max_impressions'];
        }else{
            $this->viewArray['impression_count'] = $campaign['total_impressions_count'];
        }
        $campaign['cost'] = $campaign['cost'] ? $campaign['cost'] : 0;
        $this->viewArray['campaign'] = $campaign;
        $places = $this->V2_placements_reporting_model->get_campaign_places_by_campaign_id_with_range_date($campaign_id, 10,$start_date,$end_date);
        usort($places,function($a, $b) {
            return $b['impressions'] - $a['impressions'];
        });

        $this->viewArray['places'] = $places;
        $this->viewArray['additional_report'] = $this->V2_campclick_click_model->get_pie_chart_data($campaign_id, $start_date, $end_date, $ad_id);
        $this->viewArray['demograpics_data'] = $this->V2_demographics_reporting_model->get_chart_data($campaign_id, $start_date, $end_date);

        //Chart data
        if(($campaign['campaign_type'] == 'FB-PAGE-LIKE') || ($campaign['campaign_type'] == 'FB-VIDEO-VIEWS')) {
            $click_data = $this->V2_campclick_like_model->get_impression_count($campaign_id, $start_date, $end_date, $ad_id);
        } else if($campaign['campaign_type'] == 'FB-LEAD') {
            $this->load->model("V2_fb_lead_model");
            $click_data = $this->V2_fb_lead_model->get_click_count($campaign_id, $start_date, $end_date, $ad_id);
        } else {
            if($campaign['campaign_type'] == 'FB-VIDEO-CLICKS'){
                $this->load->model('V2_video_watch_model');
                $video_views = $this->V2_video_watch_model->get_video_watch_count($campaign_id, $start_date, $end_date);
            }
            $click_data = $this->V2_campclick_click_model->get_click_count($campaign_id, $start_date, $end_date, $ad_id);
        }

        $impression_data = $this->V2_campclick_impression_model->get_impression_count($campaign_id, $start_date, $end_date, $ad_id);
        $click_array = [];
        $imp_array = [];
        $view_array = [];
        $chart_array = [];
        $res1  = [];
        $res2 = [];
        foreach ($click_data as $v){
            $click_array[$v['date']] = $v['click_count'];
        }
        foreach ($impression_data as $v){
            $imp_array[$v['date']] = $v['impression_count'];
        }
        foreach ($video_views as $v){
            $view_array[$v['date']] = $v['views_count'];
        }
        foreach ($this->createDateRangeArray($start_date, $end_date) as $data){
            if($campaign['campaign_type'] != 'FB-VIDEO-CLICKS'){
                $res1 [] = [intval(strtotime($data)*1000 ), isset($click_array[$data]) ? intval($click_array[$data]) : 0];
                $res2 [] = [intval(strtotime($data)*1000) , isset($imp_array[$data]) ? intval($imp_array[$data]) : 0];
            }else{
                $res1 [] = [intval(strtotime($data)*1000), isset($click_array[$data]) ? intval($click_array[$data]) : 0];
                $res2 [] = [intval(strtotime($data)*1000), isset($view_array[$data]) ? intval($view_array[$data]) : 0];
            }
        }
        $this->viewArray['click_data1'] = $res1;
        $this->viewArray['click_data2'] = $res2;
        if ($campaign['campaign_type'] == 'FB-VIDEO-VIEWS' || $campaign['campaign_type'] == 'VIDEO_YAHOO' || $campaign['campaign_type'] == 'FB-VIDEO-CLICKS') {
            $this->load->model('V2_video_watch_model');
            $this->viewArray['video_data'] = $this->V2_video_watch_model->get_video_watch($campaign_id, $start_date, $end_date);
        }
        $browser = $this->agent->browser();
        if ($browser == 'Safari') {
            $this->viewArray['browser'] = 'Safari';
        }
        $this->viewArray['start_date'] = $start_date;
        $this->viewArray['end_date'] = $end_date;
        if($check_campaign_type[0]['campaign_type'] == 'FB-LEAD') {
            $this->parser->parse('v2/campaign/lead_reporting', $this->viewArray);
            return;
        }

        //print("<pre>".print_r($this->viewArray,true)."</pre>");die;
        $this->parser->parse($this->view_file, $this->viewArray);


    }
    public function get_userlist_from_io()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model("Userlist_io_model");
            $user_id = $this->input->get('user_id');
            $userlists = $this->Userlist_io_model->get_userlist_from_io_by_user_id($user_id);
            echo json_encode(['message'=>'success', 'data' => $userlists]);
        } else {
            redirect(base_url());
        }
    }
    public function get_userlist_io()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model("Userlist_io_model");
            $data = $this->input->post();
            $userlists = $this->Userlist_io_model->get_userlist_by_user_id_and_network_id($data);
            echo json_encode(['message'=>'success', 'data' => $userlists]);
        } else {
            redirect(base_url());
        }
    }
    public function url_builder() {
        $this->parser->parse($this->view_file, $this->viewArray);
    }
    public function url_metadata()
    {
        $this->parser->parse($this->view_file, $this->viewArray);
    }
    public function url_test($url)
    {
        $timeout = 10;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $http_respond = curl_exec($ch);
        $http_respond = trim(strip_tags($http_respond));
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (($http_code == "200") || ($http_code == "302")) {
            curl_close($ch);
            return true;
        } else {
            // return $http_code;, possible too
            curl_close($ch);
            return false;
        }
    }

    public function save_question_form(){

    }

    public function get_meta_data_by_url()
    {
        if ($this->input->is_ajax_request()) {
            $url = $this->input->post('metadata_url');
            $arrAllKeywords = [];
            //checks if the request is post or get
            //checks if the inputted url is a valid url
            if (!stristr($url, 'http://') && !stristr($url, 'https://')) {
                $url = 'http://' . $url;
            }
            if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
                try {
                    //checks if a URL exists or not
                    $url_test = true; ///// will be changed and call function url_test
                    if ($url_test) {
                        //gets the content of the web page defined by the $url
                        $contents = file_get_contents($url);
                        if($contents==false){
                            $User_Agent = 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31';
                            $request_headers = array();
                            $request_headers[] = 'User-Agent: '. $User_Agent;
                            $request_headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
                            $ch = curl_init($url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
                            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
                            curl_setopt($ch, CURLOPT_FAILONERROR, false);
                            curl_setopt($ch, CURLOPT_HTTP200ALIASES, (array)400);
                            $contents = curl_exec($ch);
                            curl_close($ch);
                        }
                        $DOM = new \DOMDocument;
                        @$DOM->loadHTML($contents);
                        $finder = new \DomXPath($DOM);
                        //gets all <script> tags and removes
                        $scripts = $finder->query('//script');
                        foreach ($scripts as $script) {
                            $script->parentNode->removeChild($script);
                        }
                        //gets all <style> tags and removes
                        $styles = $finder->query('//style');
                        foreach ($styles as $style) {
                            $style->parentNode->removeChild($style);
                        }
                        //gets the body as a string
                        $body = ($DOM->saveHTML($finder->query('//html')->item(0)));
                        $body = preg_replace('#<[^>]+>#', ' ', $body);
                        $body = mb_convert_encoding($body, 'HTML-ENTITIES', "UTF-8");
                        $body = str_replace("&rsquo;", "'", $body);
                        $body = str_replace("&rdquo;", " ", $body);
                        $body = str_replace("&rsaquo;", " ", $body);
                        $body = str_replace("&ldquo;", " ", $body);
                        $body = str_replace("&ndash;", " ", $body);
                        $body = str_replace("&hellip;", " ", $body);
                        $body = str_replace("&nbsp;", " ", $body);
                        $body = preg_replace('/[,;.!:—\/]/', " ", $body);
                        $body = preg_replace("/[^a-zA-Z\s:']/", " ", $body);
                        $body = preg_replace("/[\n\r]/", " ", $body);
                        $body = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $body);
                        $body = html_entity_decode($body);
                        //converts body to an array
                        $body = explode(" ", $body);
                        $arrRealWords = [];
                        //Remove spaces and take only real words
                        foreach ($body as $key => $val) {
                            if (trim($val) && $val != ' ') {
                                $arrRealWords[] = trim(strtolower($val));
                            }
                        }
                        $word_count = count($arrRealWords);
                        $keywords = ["a", "about", "above", "after", "again", "against", "all", "also", "am", "an", "and", "any", "are", "aren't", "as", "at", "be", "because", "been", "before", "being", "below", "between", "both", "but", "by", "can", "can't", "cannot", "could", "couldn't", "did", "didn't", "do", "does", "doesn't", "doing", "don't", "down", "during", "each", "even", "few", "from", "further", "go", "had", "hadn't", "has", "hasn't", "have", "haven't", "having", "he", "he'd", "he'll", "he's", "her", "here", "here's", "her", "herself", "him", "himself", "his", "how", "how's", "i", "i'd", "i'll", "i've", "if", "in", "into", "is", "isn't", "it", "it's", "its", "itself", "just", "know", "like", "let", "let's", "me", "more", "most", "my", "myself", "no", "not", "now", "of", "off", "on", "one", "only", "or", "other", "our", "ours", "ourselves", "out", "over", "own", "re", "same", "she", "she'd", "she'll", "she's", "should", "shouldn't", "so", "some", "such", "than", "that", "that's", "the", "their", "theirs", "them", "then", "there", "there's", "these", "they", "they'd", "they'll", "they're", "they've", "this", "those", "to", "two", "under", "until", "up", "us", "was", "wasn't", "way", "we", "we'd", "we'll", "we're", "we've", "were", "weren't", "well", "what", "what's", "when", "when's", "where", "where's", "which", "while", "who", "who's", "whose", "why", "why's", "will", "with", "won't", "would", "wouldn't", "you", "you'd", "you'll", "you're", "you've", "your", "yours", "yourself", "yourselves", "b", "c", "d", "e", "f", "g", "h", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "w", "x", "y", "z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
                        for ($i = 0; $i < $word_count - 1; $i++) {
                            if (trim($arrRealWords[$i]) && $arrRealWords[$i] != ' '
                                && trim($arrRealWords[$i + 1]) && $arrRealWords[$i + 1] != ' '
                                && !in_array($arrRealWords[$i], $keywords) && !in_array($arrRealWords[$i + 1], $keywords)
                                && $arrRealWords[$i] != $arrRealWords[$i + 1]
                            ) {
                                $arrRealWords2[] = $arrRealWords[$i] . ' ' . $arrRealWords[$i + 1];
                            }
                        }
                        for ($i = 0; $i < $word_count - 2; $i++) {
                            if (trim($arrRealWords[$i]) && $arrRealWords[$i] != ' '
                                && trim($arrRealWords[$i + 1]) && $arrRealWords[$i + 1] != ' '
                                && trim($arrRealWords[$i + 2]) && $arrRealWords[$i + 2] != ' '
                                && !in_array($arrRealWords[$i], $keywords)
                                && !in_array($arrRealWords[$i + 1], $keywords)
                                && !in_array($arrRealWords[$i + 2], $keywords)
                                && $arrRealWords[$i] != $arrRealWords[$i + 1]
                                && $arrRealWords[$i] != $arrRealWords[$i + 2]
                                && $arrRealWords[$i + 1] != $arrRealWords[$i + 2]
                            ) {
                                $arrRealWords3[] = $arrRealWords[$i] . ' ' . $arrRealWords[$i + 1] . ' ' . $arrRealWords[$i + 2];
                            }
                        }
                        // Count duplicated keywords
                        $arrKeywords = $arrAllKeywords = array_count_values($arrRealWords);
                        $arrKeywords2 = array_count_values($arrRealWords2);
                        $arrKeywords3 = array_count_values($arrRealWords3);
                        arsort($arrKeywords);
                        arsort($arrKeywords2);
                        arsort($arrKeywords3);
                        arsort($arrAllKeywords);
                        // Remove needless keywords, which is not key sensitive
                        foreach ($keywords as $keyword) {
                            unset($arrKeywords[strtoupper($keyword)]);
                            unset($arrKeywords[strtolower($keyword)]);
                            unset($arrKeywords[ucwords($keyword)]);
                            unset($arrKeywords2[strtoupper($keyword)]);
                            unset($arrKeywords2[strtolower($keyword)]);
                            unset($arrKeywords2[ucwords($keyword)]);
                            unset($arrKeywords3[strtoupper($keyword)]);
                            unset($arrKeywords3[strtolower($keyword)]);
                            unset($arrKeywords3[ucwords($keyword)]);
                        }
                        // take only 10 more duplicated word
                        $arrDuplicatedKeywords = array_slice($arrKeywords, 0, 50, true);
                        $arrDuplicatedKeywords2 = array_slice($arrKeywords2, 0, 50, true);
                        $arrDuplicatedKeywords3 = array_slice($arrKeywords3, 0, 50, true);
                    } else {
                        $word_count = null;
                        $url = null;
                        $arrDuplicatedKeywords = null;
                        $arrDuplicatedKeywords2 = null;
                        $arrDuplicatedKeywords3 = null;
                    }
                } catch (Exception $e) {
                    $word_count = null;
                    $url = null;
                    $arrDuplicatedKeywords = null;
                    $arrDuplicatedKeywords2 = null;
                    $arrDuplicatedKeywords3 = null;
                }
            } else {
                //sets an error session if the inputted url isn't valid
                $word_count = null;
                $url = null;
                $arrDuplicatedKeywords = null;
                $arrDuplicatedKeywords2 = null;
                $arrDuplicatedKeywords3 = null;
            }
            if (is_array($arrDuplicatedKeywords)) {
                foreach ($arrDuplicatedKeywords as $arr_k => $arr_v) {
                    if (strlen($arr_k) == 1) {
                        unset($arrDuplicatedKeywords[$arr_k]);
                    }
                }
            }
            if (is_array($arrDuplicatedKeywords2)) {
                foreach ($arrDuplicatedKeywords2 as $arr_k => $arr_v) {
                    if (strlen($arr_k) == 1) {
                        unset($arrDuplicatedKeywords2[$arr_k]);
                    }
                }
            }
            if (is_array($arrDuplicatedKeywords3)) {
                foreach ($arrDuplicatedKeywords3 as $arr_k => $arr_v) {
                    if (strlen($arr_k) == 1) {
                        unset($arrDuplicatedKeywords3[$arr_k]);
                    }
                }
            }
            $quantity = 0;
            $forChecks = array();
            $table_str = '<div style="width:100%; height:1px; background: #000"></div><br><table class="table table-striped"><thead><tr><th width="40%">Word</th><th width="20%">Count</th><th width="20%">Percent</th><th width="20%">Select</th></tr></thead><tbody><tr>';
            $i = 0;
            foreach ($arrDuplicatedKeywords as $key => $value) {
                $i++;
                if($i<16) {
                    $forChecks[]=$key;
                    $quantity++;
                    $density = $value / $word_count * 100;
                    $table_str = $table_str . '<td>' . $key . '</td><td>' . $value . '</td><td>' . round($density, 2) . '%</td><td><input class="forminput" type="checkbox" name="phrases" value="'.$key.'"><br> </td></tr>';
                }
            }
            $table_str = $table_str . '</tbody></table>';
            $table_str2 = '<div style="width:100%; height:1px; background: #000"></div><br><br><table class="table table-striped"><thead><tr><th width="40%">2 Word phrase</th><th width="20%">Count</th><th width="20%">Percent</th><th width="20%">Select</th></tr></thead><tbody><tr>';
            $i = 0;
            foreach ($arrDuplicatedKeywords2 as $key => $value) {
                $i++;
                if($i<16) {
                    $forChecks[]=$key;
                    $quantity++;
                    $density = $value / $word_count * 100;
                    $table_str2 = $table_str2 . '<td>' . $key . '</td><td>' . $value . '</td><td>' . round($density, 2) . '%</td><td><input class="forminput" type="checkbox" name="phrases" value="'.$key.'"><br> </td></tr>';
                }
            }
            $table_str2 = $table_str2 . '</tbody></table>';
            $table_str3 = '<div style="width:100%; height:1px; background: #000"></div><br><br><table class="table table-striped"><thead><tr><th width="40%">3 Word phrase</th><th width="20%">Count</th><th width="20%">Percent</th><th width="20%">Select</th></tr></thead><tbody><tr>';
            $i=0;
            foreach ($arrDuplicatedKeywords3 as $key => $value) {
                $i++;
                if($i<16) {
                    $forChecks[]=$key;
                    $quantity++;
                    $density = $value / $word_count * 100;
                    $table_str3 = $table_str3 . '<td>' . $key . '</td><td>' . $value . '</td><td>' . round($density, 2) . '%</td><td><input class="forminput" type="checkbox" name="phrases" value="'.$key.'"><br> </td></tr>';
                }
            }
            $table_str3 = $table_str3 . '</tbody></table>';
            $metaData = [
                "word_count" => $word_count,
                "url" => $url,
                "table_str" => $table_str,
                "table_str2" => $table_str2,
                "table_str3" => $table_str3,
                "quantity" => $quantity,
                "forChecks" => $forChecks
            ];
            echo json_encode(array("status" => "SUCCESS", "metadata" => $metaData));
            exit;
        }
    }
    public function rolling_count_click($campaign_id, $duration )
    {
        $start_date = date("Y-m-d H:i:s", strtotime("-{$duration} minutes"));
        $this->load->model('V2_campclick_click_model');
        $clicks_count = $this->V2_campclick_click_model->get_campaign_click_count_between_date($campaign_id, $start_date);
//        echo $clicks_count;
//        var_dump($clicks_count);die;
        return $clicks_count;
//        print json_encode(array("status" => "SUCCESS", "count" => $clicks_count));
    }
    public function tracking_report($stime = "", $etime = "")
    {
        $this->load->model('V2_campclick_impression_model');
        $this->require_auth();
        $status = "ACTIVE";
        if ($this->user_type == 'viewer') {
            redirect(base_url());
        }
        if (!$this->viewArray['is_admin']) {
            redirect(base_url());
        }
        $campaigns = $this->V2_master_campaign_model->get_active_campaigns_all_data();
        //$campaigns = $this->V2_master_campaign_model->fulfillment_summary(null, $stime, $etime, $status);
        foreach ($campaigns as $key => $campaign) {
//            if ($campaign['max_budget']) {
//                $cost = $this->V2_master_campaign_model->get_campaign_cost($campaign['id'], $campaign['network_name']);
//            } else {
//                $cost = 0;
//            }
//            $campaigns[$key]['cost'] = $cost;
            $campaigns[$key]['six'] = $this->rolling_count_click($campaign['id'],360);
            $campaigns[$key]['twelve'] = $this->rolling_count_click($campaign['id'],720);
            $campaigns[$key]['twentyfour'] = $this->rolling_count_click($campaign['id'],1440);
//            $campaigns[$key]['m_clicks'] = $campaign['total_clicks_count']/$campaign['max_clicks'];
//            $campaigns[$key]['m_impressions']  = $campaign['total_opens']/$campaign['max_impressions'];
//            $campaigns[$key]['m_budget'] = $cost/$campaign['mas_budget'];
        }
        $this->viewArray['rtb_cost'] = $this->V2_campclick_impression_model->get_campaign_rtb_cost($campaign['id']);
        $this->viewArray['campaigns'] = $campaigns;
        $this->viewArray['status'] = $status;
        $this->parser->parse($this->view_file, $this->viewArray);
    }
    public function email_tracking_report($stime = "", $etime = "")
    {
        $this->load->model("V2_master_campaign_model");
        $this->load->model("V2_email_campaign_reporting_model");
        $this->load->model("V2_email_campaign_additional_reporting_model");
        $this->load->model("V2_email_campaign_link_reporting_model");
        $this->require_auth();
        $status = "ACTIVE";
        if ($this->user_type == 'viewer') {
            redirect(base_url());
        }
        // need to correction
        $user = $this->ion_auth->user()->row();
        if (!$this->viewArray['is_admin']) {
            redirect(base_url());
        }
        $campaigns = $this->V2_master_campaign_model->get_email_campaigns_with_clicks_and_impressions();
        foreach ($campaigns as $key => $campaign) {
            $campaign_click_data = $this->V2_email_campaign_reporting_model->get_clicks_count_hourly($campaign['id']);
            $click_data = $this->V2_email_campaign_link_reporting_model->get_clicks_count_hourly($campaign['id']);
            $type = 'hour';
//
            $click_array = [];
            $imp_array = [];
            foreach ($click_data as $v) {
                $click_array[$v[$type]] = $v['clicks_count'];
            }
            foreach ($campaign_click_data as $v) {
                $mobile_array[$v[$type]] = $v['mobile_clicks_count'];
                $unique_clicks_array[$v[$type]] = $v['unique_clicks_count'];
                $impressions_array[$v[$type]] = $v['impressions_count'];
            }
            $campaigns[$key]['total_report'] = $this->V2_email_campaign_reporting_model->get_total_clicks_count_by_campaign_id($campaign['id']);
            $campaigns[$key]['total_report']['clicks_count'] = $this->V2_email_campaign_link_reporting_model->get_total_clicks_count_by_campaign_id($campaign['id'])['clicks_count'];
            $campaigns[$key]['additional_report'] = $this->V2_email_campaign_additional_reporting_model->get_all_by_campaign_id($campaign['id']);
            $click6 = $this->V2_email_campaign_reporting_model->get_clicks_count_hourly_by_hour($campaign['id'],'','',6);
            $click12 = $this->V2_email_campaign_reporting_model->get_clicks_count_hourly_by_hour($campaign['id'],'','',12);
            $click24 = $this->V2_email_campaign_reporting_model->get_clicks_count_hourly_by_hour($campaign['id'],'','',24);
            $campaigns[$key]['six'] = 0;
            $campaigns[$key]['twelve'] = 0;
            $campaigns[$key]['twentyfour']=0;
            foreach ($click6 as $click){
                $campaigns[$key]['six'] += $click['total'];
            }
            foreach ($click12 as $click){
                $campaigns[$key]['twelve'] += $click['total'];
            }
            foreach ($click24 as $click){
                $campaigns[$key]['twentyfour'] += $click['total'];
            }
            $campaigns[$key]['percent_diff']= date_diff(new DateTime($campaigns[$key]['campaign_start_datetime']),new DateTime(date("Y-m-d H:i:s")))->d;
        }
        $this->viewArray['campaigns'] = $campaigns;
        $this->viewArray['status'] = $status;
        $this->parser->parse($this->view_file, $this->viewArray);
    }
    public function financial_report()
    {
        $this->require_auth();
        if($this->input->is_ajax_request()) {
            $stime = $this->input->post('campaign_start_datetime');
            $etime = $this->input->post('campaign_end_datetime');
            $user_id = $this->input->post('user');
        } else {
            $stime = date("Y-m-d 00:00:00", strtotime('first day of last month'));
            $etime = date("Y-m-d 23:59:59", strtotime('last day of last month'));
            $user_id = null;
        }
        if($this->user_type == 'financial_manager' || $this->user['is_admin']) {
            if($this->user['is_admin']) {
                $campaigns = $this->V2_master_campaign_model->financial_report(null, $stime, $etime, $user_id);
            } else {
                $campaigns = $this->V2_master_campaign_model->financial_report($this->userid, $stime, $etime, $user_id); //var_dump($campaigns); exit;
            }
            $this->viewArray['campaigns'] = $campaigns;
            if($this->input->is_ajax_request()) {
                $html = $this->parser->parse('v2/campaign/financial_report_content', $this->viewArray, true);
                echo json_encode(array("status"=>"SUCCESS", "html" => $html)); exit;
            }
            $this->load->model("V2_users_model");
            if($this->user['is_admin']) {
                $users = $this->V2_users_model->get_active_customers();
            } else {
                $users = $this->V2_users_model->get_all_users_by_financial_manager_id($this->userid);
            }
            $this->viewArray['users'] = $users;
            $this->viewArray['params'] = ['campaign_start_datetime'=>$stime, 'campaign_end_datetime'=>$etime];
            $this->parser->parse($this->view_file, $this->viewArray);
        } else {
            redirect(base_url());
        }
    }
    public function download_leads() {
        $campaign = [];
        $campaign_id = $this->input->post('campaign_id');
        $start_date = $this->input->post('start_date') ? $this->input->post('start_date') : date('Y-m-d');
        $end_date = $this->input->post('end_date') ? $this->input->post('end_date') : date('Y-m-d');
        $ad_id = $this->input->post('ad_id') ? $this->input->post('ad_id') : null;
        $file_name = 'lead reporting between '.$start_date.' and '.$end_date.' '.$campaign_id.'.csv';
        $this->load->model("V2_fb_lead_model");
        $leads = $this->V2_fb_lead_model->get_all_by_campaign_id_and_date($campaign_id, $start_date, $end_date, $ad_id);
        if(!$leads) {
            echo json_encode(['status'=>'error','msg'=>'No leads between those dates']); exit;
        }
        $path = '/v2/files/tmp/';
        $fp = fopen('v2/files/tmp/'.$file_name, 'w');
        foreach ($leads as $fields) {
            fputcsv($fp, $fields);
        }
        //fseek($fp, 0);
        fclose($fp);

        echo json_encode(['status'=>'success','url'=>$path.$file_name]); exit;
    }
    public function download_pdf($file_name) { //var_dump($this->input->get('file_name')); exit;
//        $file_name = urldecode($file_name);
        $file_name = $this->input->get('file_name');
        $file = 'v2/pdf/'.$file_name;
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
    }
    public function download_pdf_for_all_time($campaign_id) { //var_dump($campaign_id); exit;
        $campaign = $this->V2_master_campaign_model->get_by_id(null, $campaign_id);
        $name = $campaign['name'].' '.$campaign['io'];
        $name = str_replace('/','',$name);
        //$path = '/var/www/html/v2/pdf/'.$name;

        $path = "/var/www/html/v2/pdf/{$campaign_id}_all_time.pdf";

        unlink($path);

        $url = base_url()."v2/campaign/email_reporting_for_pdf/";
        $this->load->library('Wkhtmltopdf');
        $this->load->model('V2_log_model');
        $this->wkhtmltopdf->__set('url', $url.$campaign_id);
        $this->wkhtmltopdf->__set('mode', 'MODE_DOWNLOAD');
        //$this->wkhtmltopdf->__set('path', $path.' for all time.pdf');
        $this->wkhtmltopdf->__set('path', $path);
        $this->wkhtmltopdf->__set('title', 'ProData Media Campaign Report');
        $result = $this->wkhtmltopdf->downloadPDF();
        exit;
    }
    public function combine_report()
    {
        $this->require_auth();
        $status = "ACTIVE";
        if ($this->user_type == 'viewer') {
            redirect(base_url());
        }
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        $start_date = $this->input->post('start_date') ? $this->input->post('start_date') : date('Y-m-d');
        $end_date = $this->input->post('end_date') ? $this->input->post('end_date') : date('Y-m-d');
        $so = $this->input->post('so') ? $this->input->post('so') : null;
        $page =  $this->input->post('page') ? $this->input->post('page') : 1;
        $offset = ($page - 1) * 10;
        $email_reporting = $this->V2_master_campaign_model->get_email_campaigns_by_so($this->userid,$start_date,$end_date,$so, 10, $offset);
        $email_reporting_lenght = $this->V2_master_campaign_model->get_email_campaigns_by_so($this->userid,$start_date,$end_date,$so);
        if ($start_date == $end_date){
            $so_compaign = $this->V2_master_campaign_model->get_so($so,$start_date, 10, $offset);
            $so_compaign_lenght = $this->V2_master_campaign_model->get_so($so,$start_date);
            $so_numbers = $this->V2_master_campaign_model->get_so_numbers($this->userid,$start_date);
        }else {
            $so_compaign = $this->V2_master_campaign_model->get_so($so,$start_date,$end_date, 10, $offset);
            $so_compaign_lenght = $this->V2_master_campaign_model->get_so($so,$start_date,$end_date);
            $so_numbers = $this->V2_master_campaign_model->get_so_numbers($this->userid,$start_date, $end_date);
        }
        $total = count($email_reporting_lenght) + count($so_compaign_lenght);
        $p = $this->createAjaxPagination($total, $page, 20 );
        if($this->input->is_ajax_request()) {
            echo json_encode(['success' => true, 'link' => $p, 'so_compaign' => $so_compaign, 'email_reporting' => $email_reporting]);
            return;
        }

        $this->load->model("V2_master_campaign_model");
        $this->load->model("V2_campclick_click_model");
        $this->load->model("V2_campclick_impression_model");
        $this->load->model("V2_email_campaign_reporting_model");
        $email_click = $this->V2_email_campaign_reporting_model->get_clicks_count_all($start_date, $end_date, $so);
        $clicks = $this->V2_campclick_click_model->get_clicks_count_by_campaigns_types($this->userid, $start_date, $end_date, $so);
        $impressions = $this->V2_campclick_impression_model->get_impressions_count_by_campaigns_types($this->userid, $start_date, $end_date, $so);
        $data_sorted = [];
        foreach ($impressions as $impression){
            $data_sorted[$impression['campaign_type']]['total_impressions_count'] = $impression['total_impressions_count'];
        }
        foreach ($clicks as $click){
            $data_sorted[$click['campaign_type']]['total_clicks_count'] = $click['total_clicks_count'];
        }
        $channels = [
            'DISPLAY' => [
                'DISPLAY',
                'DISPLAY_YAHOO',
                'APP_INSTALL_YAHOO'
            ],
            'DISPLAY-RETARGET' => [
                'DISPLAY-RETARGET',
            ],
            'SOCIAL' => [
                'FB-MOBILE-NEWS-FEED',
                'FB-DESKTOP-RIGHT-COLUMN',
                'FB-DESKTOP-NEWS-FEED',
                'FB-PAGE-LIKE',
                'FB-CAROUSEL-AD',
                'FB-LEAD',
                'FB-MOBILE-APP-INSTALLS',
                'FB-PROMOTE-EVENT',
                'FB-INSTAGRAM',
                'FB-LOCAL-AWARENESS'
            ],
            'VIDEO' => [
                'FB-VIDEO-CLICKS',
                'FB-VIDEO-VIEWS',
                'VIDEO_YAHOO',
                'FB-INSTAGRAM-VIDEO'
            ],
            'TEXTAD' => [
                'TEXTAD',
            ],
            'RICH_MEDIA' => [
                'DIALOG_CLICK_TO_CALL',
                'APPWALL',
                'LANDING_PAGE',
                'IN_APP',
                'OVERLAY_AD',
                'PUSH_CLICK_TO_CALL',
                'ABSTRACT_BANNER_LARGE',
                'ABSTRACT_BANNER_SMALL',
                'ABSTRACT_BANNER_LARGE_CC',
                'ABSTRACT_BANNER_LARGE_CM',
                'ABSTRACT_BANNER_SMALL_CM',
                'ABSTRACT_BANNER_SMALL_CC',
                'RICH_MEDIA_INTERSTITIAL'
            ],
            'EMAIL' => [
                'EMAIL',
            ],
            'TOTAL' => [
                'TOTAL',
            ],
        ];
        $data = [];
        $total_impressions_count = 0;
        $max_impressions = $this->V2_master_campaign_model->get_max_impressions_count($so);
        $max_impressions_count = $max_impressions['max_impressions'];
        $total_clicks_count = 0;
        foreach ($channels as $chanel_type => $chanel) {
            $impressions_count = 0;
            $clicks_count = 0;
            foreach ($chanel as $campaign_type){
                $clicks_count += $data_sorted[$campaign_type]['total_clicks_count'];
                $impressions_count += $data_sorted[$campaign_type]['total_impressions_count'];
            }
            $total_clicks_count += $clicks_count;
            $total_impressions_count += $impressions_count;
            if($chanel_type == 'EMAIL'){
                 $data[$chanel_type]['clicks_count'] = $email_click[0]['total_clicks_count'];
                 $data[$chanel_type]['impressions_count'] = $email_click[0]['total_impressions'];
            }
            elseif($chanel_type == 'TOTAL'){
                $data[$chanel_type]['clicks_count'] = $total_clicks_count + $email_click[0]['total_clicks_count'];
                $data[$chanel_type]['impressions_count'] = $total_impressions_count + $email_click[0]['total_impressions'];;
            }else{
                $data[$chanel_type]['clicks_count'] = $clicks_count;
                $data[$chanel_type]['impressions_count'] = $impressions_count;
            }
        }
        $impressions_percent = $total_impressions_count*100/$max_impressions_count;
        if($impressions_percent > 100) {
            $impressions_percent = 100;
        }
        $impressions_diff = $max_impressions_count - $total_impressions_count;
        if($impressions_diff<0) {
            $impressions_diff = 0;
        }
        $pie_data['impressions']['total_impressions'] = $total_impressions_count;
        $pie_data['impressions']['max_impressions'] = $max_impressions_count;
        $pie_data['impressions']['impressions_percent'] = sprintf ("%.2f",$impressions_percent);
        $pie_data['impressions']['impressions_diff'] = sprintf ("%.2f", $impressions_diff);
        $pie_data['clicks']['total_clicks'] = $total_clicks_count;
        $clicks_percent = $total_clicks_count*100/$total_impressions_count;
        $pie_data['clicks']['clicks_percent'] = sprintf ("%.2f", $clicks_percent);
        $pie_data['clicks']['clicks_diff'] = 100 - $pie_data['clicks']['clicks_percent'];
        $this->viewArray['date_now'] = date('m/d/Y', strtotime($end_date));
        $this->viewArray['start_date'] = date('m/d/Y', strtotime($start_date));
        $this->viewArray['js_date_now'] = date('Y-m-d');
        $this->viewArray['js_start_data'] = date('Y-m-d', strtotime($start_date));
        $this->viewArray['js_end_data'] = date('Y-m-d', strtotime($end_date));
        $this->viewArray['donats_chart_data'] = $pie_data;
        $this->viewArray['data'] = $data;
        $this->viewArray['so'] = $so;
        $this->viewArray['so_numbers'] = $so_numbers;
        $this->viewArray['links'] = $p;
        $this->parser->parse($this->view_file, $this->viewArray);
    }
    public function copy_campaign($id){
        if ($this->user_type == 'viewer') {
            redirect(base_url());
        }
        if(!$id){
            return redirect(base_url());
        }

        $this->load->model("V2_master_campaign_model");
        $this->load->model("V2_ad_model");
        $this->load->model('V2_ads_link_model');
        $this->load->model("V2_group_model");
        $this->load->model("V2_ads_disapproval_model");
        $this->load->model("V2_campaign_category_model");
        $this->load->model("V2_time_parting_model");
        $this->load->model("V2_campaign_cost_model");
        $this->load->model('userlist_io_model');
        $campaign = $this->V2_master_campaign_model->get_by_id(null,$id);
        if($campaign['campaign_status'] == 'SCHEDULED'){
            return redirect(base_url());
        }
        $destination_url = $this->V2_master_campaign_model->generate_destination_url($campaign_id, $campaign_data['domain'], $campaign_data['campaign_type']);
        $get_campaign_category = $this->V2_campaign_category_model->get_associated_iab_categories_by_campaign_id($campaign['id']);

        $start_date_old = new DateTime($campaign['campaign_start_datetime']);
        $end_date_old = new DateTime($campaign['campaign_end_datetime']);
        $total_days = $end_date_old->diff($start_date_old)->days;
        $campaign['campaign_start_datetime'] = date('Y-m-d H:i:s');
        $campaign['last_geo_expanded_update'] = date('Y-m-d H:i:s');
        $campaign['create_date'] = date('Y-m-d H:i:s');
        $campaign['campaign_status'] = 'SCHEDULED';
        $campaign['copied'] = 'Y';
        $campaign['campaign_is_converted_to_live']='N';
        $start_date = new DateTime($campaign['campaign_start_datetime']);
        $end_date = new DateTime($campaign['campaign_end_datetime']);
        $campaign['campaign_end_datetime'] = date('Y-m-d H:i:s', mktime() + (86400 * $total_days));
        $disapproval_ad_io=$campaign['io'];
        $campaign['io'] .= "(c)";
        $campaign['name'] .= "(c)";
        $campaign['copied_from_id'] = $id;
        $new_campaign_id = $this->V2_master_campaign_model->copy_new_campaign($campaign);
        $destination_url = $this->V2_master_campaign_model->generate_destination_url($new_campaign_id, $campaign['domain'], $campaign_data['campaign_type']);
        $get_cost= $this->V2_campaign_cost_model->get_all_by_campaign_id($id);
        $get_cost[0]['id']=null;
        $get_cost[0]['network_id']=$campaign['network_id'];
        $get_cost[0]['cost']='0.0000';
        $get_cost[0]['date_updated']=$campaign['last_geo_expanded_update'];
        $get_cost[0]['campaign_id']=$new_campaign_id;
        $create_cost_id=$this->V2_campaign_cost_model->create($get_cost[0]);
        $get_time_parting=$this->V2_time_parting_model->get_by_campaign_id($campaign['id']);
        foreach($get_time_parting as $value2){
            $value2['id']=null;
            $value2['campaign_id']=$new_campaign_id;
            $value2['created_date']=$campaign['create_date'];
            $this->V2_time_parting_model->create($value2);
        }
        $insert_lookalike = $this->userlist_io_model->get_userlist_by_campaign_id($campaign['id']);
        if (count($insert_lookalike) > 0) {
            $insert_lookalike[0]["id"] = null;
            $insert_lookalike[0]["io"] =$campaign['io'];
            $insert_lookalike[0]["campaign_id"] = $new_campaign_id;
            $insert_lookalike[0]["network_id"] = $campaign['network_id'];
            $insert_lookalike[0]["user_id"] = $this->userid;
            $lookalike_id = $this->userlist_io_model->create_lookalike_userlist_io($insert_lookalike[0]);
        }
        $group = $this->V2_group_model->get_group_by_campaign_id($campaign['id']);
        $group['campaign_id'] = $new_campaign_id;
        $group['date_created'] = date('Y-m-d');
        $new_group_id = $this->V2_group_model->copy_new_group($group);
        $copy_campaign_category = $this->V2_campaign_category_model->copy_batch_insert($new_campaign_id, $get_campaign_category);
        $ads = $this->V2_ad_model->get_ads_by_campaign_id($campaign['id']);

        if ($ads[0]) {
            foreach ($ads as $value) {
                $value['group_id'] = $new_group_id;
                $value['campaign_id'] = $new_campaign_id;
                $value['create_date'] = date('Y-m-d H:i:s');
                $ad_id = $this->V2_ad_model->copy_new_ad($value);
                $value['new_ad_id'] = $ad_id;
                $value['destination_url'] = $value["original_url"];
                $link_id = $this->V2_ads_link_model->create($value, $new_campaign_id, $ad_id);
                $dest_url= $destination_url.'/'.$link_id;
                $this->V2_ad_model->update_destination_url_by_campaign_id($ad_id, $dest_url);
                $value['destination_url'] = $dest_url; // overwrite old destination URl with updated URL
                $get_disapproval_ads[] = $this->V2_ads_disapproval_model->is_exists($value['id'],$disapproval_ad_io);

                // Make Ad entry to Google AdX
                $creative = $this->v2_google_adx_model->insert_creative([
                    'campaign' => $campaign,
                    'ad' => $value
                ], $create_disapproval_entry = false);
            }
            foreach ($get_disapproval_ads as $value1) {
                if(count($value1)>0){
                    $value1['id']=null;
                    $value1['io']=$campaign['io'];
                    $value1['ad_id']=$ad_id;
                    $value1['status']='PENDING';
                    $value1['date_creative']=$campaign['create_date'];
                    $value1['date_update']=$campaign['last_geo_expanded_update'];
                    $disapproval_id=$this->V2_ads_disapproval_model->create($value1);
                }
            }
        }else{
                $ads['group_id'] = $new_group_id;
                $ads['campaign_id'] = $new_campaign_id;
                $ads['create_date'] = date('Y-m-d H:i:s');
                $ad_id = $this->V2_ad_model->copy_new_ad($ads);
                $ads['destination_url'] = $ads["original_url"];
                $link_id = $this->V2_ads_link_model->create($ads, $new_campaign_id, $ad_id);
                $dest_url['destination_url']= $destination_url.'/'.$link_id;
                $this->V2_ad_model->update_destination_url_by_campaign_id($ad_id, $dest_url);
                $ads['destination_url'] = $dest_url; // overwrite old destination URL with new URL

                // Make Ad entry to Google AdX
                $creative = $this->v2_google_adx_model->insert_creative([
                    'campaign' => $campaign,
                    'ad' => $ads
                ], $create_disapproval_entry = false);

                $get_disapproval_ads = $this->V2_ads_disapproval_model->is_exists($ads['id'],$disapproval_ad_io);
                $get_disapproval_ads['id']=null;
                $get_disapproval_ads['io']=$campaign['io'];
                $get_disapproval_ads['ad_id']=$ad_id;
                $get_disapproval_ads['status']='PENDING';
                $get_disapproval_ads['date_creative']=$campaign['create_date'];
                $get_disapproval_ads['date_update']=$campaign['last_geo_expanded_update'];
                $disapproval_id=$this->V2_ads_disapproval_model->create($get_disapproval_ads);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, base_url()."v2/cron/make_campaign_live/".$new_campaign_id);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $out = curl_exec($ch);
        curl_close($ch);

        $url=base_url()."v2/campaign/edit_campaign/".$new_campaign_id;
        echo $url;
    }

    public function edit_io_based_retargeting_io()
    {
        if($this->user['is_admin']) {
            $this->userid = null;
        }
        $data = $this->input->post();
        $id = $data['campaign_id'];
        $ios = $data['io_based_retargeting_ios'];

        $campaign = $this->V2_master_campaign_model->get_by_id($this->userid, $id);
        if($campaign['campaign_status'] == "COMPLETED") {
            print json_encode(array("status"=>"ERROR", "message" => 'You can not edit COMPLETED campaign'));
            exit;
        }
        $type = 'retargeting_io';
        $rules = array(
            array('field' => 'campaign_id', 'label' => 'Campaign id', 'rules' => 'required|numeric')
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE) {
            print json_encode(array("status" => "ERROR", "message" => validation_errors()));
            exit;
        }
        $ios_list = NULL;
        if ( is_array($ios) ) $ios_list = implode(',', array_map('trim', $ios));

        // call validate for date time
        $update = array('retargeting_io' => $ios_list, 'is_io_based_retargeting' => 'Y');
        if ( empty($ios) ) {
            $update['is_io_based_retargeting'] = 'N';
        }

        $updated = $this->V2_master_campaign_model->update($id, $update);
        if(!$updated){
            print json_encode(array("status" => "ERROR", "message" => "Retargeting IO list didn't update"));
            exit;
        }

        // save into log table
        $this->load->model('V2_log_model');
        $this->V2_log_model->create($id, $ios_list, $type);
        print json_encode(array("status" => "SUCCESS", "message" => "Retargeting IO successfully updated"));
        exit;
    }
}
?>
