<?php

class Yahoo_model extends CI_Model	{

    private $network_id = 6;
    private $text_ad_type = 'TEXTAD';
    private $display_ad_type = 'DISPLAY';
    private $categories = array(
        1=>'Arts & Entertainment',
        2=>'Automotive',
        3=>'Business',
        4=>'Careers',
        5=>'Education',
        6=>'Family & Parenting',
        7=>'Health & Fitness',
        8=>'Food & Drink',
        9=>'Hobbies & Interests',
        10=>'Home & Garden',
        11=>"Law, Gov't & Politics",
        12=>'News',
        13=>'Finance Personal',
        14=>'Society',
        15=>'Science',
        16=>'Pets',
        17=>'Sports',
        18=>'Style & Fashion',
        19=>'Technology & Computing',
        20=>'Travel',
        21=>'Real Estate',
        22=>'Shopping',
        23=>'Religion & Spirituality',
        24=>'Uncategorized',
        25=>'Non-Standard Content',
        26=>'Illegal Content',
        393=>'Games',
        227=>'Dating',
    );
    protected $CI;
    protected $yahoo;
    protected $live_campaign;
    protected $advertiser_id = "1493170";
    protected $advertiser_name = "ProData Media Digital Advertising";
    protected $live_group;
    public function __construct()	{

        parent::__construct();
        $this->CI =& get_instance();
        $this->CI->load->database();
        //$this->CI->load->library("user_agent");
        $this->CI->load->model("V2_group_model");
        $this->CI->load->model("V2_ad_model");
        $this->CI->load->model("V2_master_campaign_model");
        $this->CI->load->model("V2_campaign_network_criteria_rel_model");
        $this->CI->load->model("V2_group_model");
        $this->CI->load->library("yahoo");
        $this->yahoo = $this->CI->yahoo;

    }

    public function create($campaign)    {
        if (!$campaign) {
            return false;
        }
        $add_demographic_targeting = true;
        $result_campaign = $this->create_campaign($campaign);
        if($result_campaign['message']) {

            // sent mail to owner
            return array('status' => 'rejected', 'message' => $result_campaign['message']);
        }


        $create_group = $this->create_group($this->live_campaign, $campaign);
        if($create_group['message']) {
            // sent mail to owner
            // remove campaign from adword
            //$this->adword->updateCampaignStatus($this->adword, $this->live_campaign->id, 'REMOVED');
            return array('status' => 'rejected', 'message' => $result_campaign['message']);
        }

        if($campaign['keywords']!="RON") {
            if ($this->create_keywords($campaign)) {
                // sent mail to owner
                // remove campaign from adword
//            $this->adword->updateCampaignStatus($this->adword, $this->live_campaign->id, 'REMOVED');
//            return 'rejected';
            }
        }
        if(isset($campaign['geotype'])){
            if(!$this->create_location_targeting($campaign)) {
                // sent mail to owner
                // remove campaign from adword
//                $this->adword->updateCampaignStatus($this->adword, $this->live_campaign->id, 'REMOVED');
//                return 'rejected';
            }
        }

        if(!empty($campaign['gender'])){
            $result_create_gender = $this->create_gender_targeting($campaign);
            if($result_create_gender['message']) {
                // sent mail to owner
                // remove campaign from adword
                //$this->adword->updateCampaignStatus($this->adword, $this->live_campaign->id, 'REMOVED');

                return array('status' => 'rejected', 'message' => $result_create_gender['message']);
            }
            $add_demographic_targeting = false;
        }
        if(!empty($campaign['interests'])){
            $result_create_interest = $this->create_interest_targeting($campaign);
            if($result_create_interest['message']) {
                // sent mail to owner
                // remove campaign from adword
                //$this->adword->updateCampaignStatus($this->adword, $this->live_campaign->id, 'REMOVED');

                return array('status' => 'rejected', 'message' => $result_create_gender['message']);
            }
            $add_demographic_targeting = false;
        }

        if(!empty($campaign['carrier']) && $campaign['geotype']!='postalcode') {
            $result_create_carrier_targeting = $this->create_carrier_targeting($campaign);
            if($result_create_carrier_targeting['message']) {
                // sent mail to owner
                // remove campaign from adword
                //$this->adword->updateCampaignStatus($this->adword, $this->live_campaign->id, 'REMOVED');
                return array('status' => 'rejected', 'message' => $result_create_carrier_targeting['message']);
            }
            $add_demographic_targeting = false;
        }

        if($campaign['is_remarketing'] == "Y") {
            if(!empty($campaign['vertical'])){
                $result_create_vertical_targeting = $this->create_vertical_targeting($campaign);
                if($result_create_vertical_targeting['message']) {
                    // sent mail to owner
                    // remove campaign from adword
                    //$this->adword->updateCampaignStatus($this->adword, $this->live_campaign->id, 'REMOVED');
                    return array('status' => 'rejected', 'message' => $result_create_vertical_targeting['message']);
                }
            }

            if($campaign['is_remarketing_io'] == "Y" && $campaign['remarketing_io']){
                $result_create_io_targeting = $this->create_io_targeting($campaign);
                if($result_create_io_targeting['message']) {
                    // sent mail to owner
                    // remove campaign from adword
                    //$this->adword->updateCampaignStatus($this->adword, $this->live_campaign->id, 'REMOVED');
                    return array('status' => 'rejected', 'message' => $result_create_io_targeting['message']);
                }
            }
            $add_demographic_targeting = false;
        }
        if(!$this->create_ads($this->live_group, $campaign)) {
            // sent mail to owner
            // remove campaign from adword
            //$this->adword->updateCampaignStatus($this->adword, $this->live_campaign->id, 'REMOVED');
            return 'rejected';
        }

        // clarify date diff check
        if(strtotime($campaign['campaign_start_datetime'])>strtotime(date("Y-m-d H:i:s"))) {
            $status = 'PAUSED';
            $data_for_update['campaign_status'] = 'SCHEDULED';
            $data_for_update['network_campaign_status'] = 'PAUSED';
        } else {
            $status = 'ENABLED';
            $data_for_update['campaign_status'] = 'ACTIVE';
            $data_for_update['network_campaign_status'] = 'ACTIVE';
        }
        // make campaign status enabled and set converted to live to Y
        //$this->adword->updateCampaignStatus($this->adword, $this->live_campaign->id, $status);

        $data_for_update['campaign_is_converted_to_live'] = "Y";
        $data_for_update['network_campaign_id'] = $this->live_campaign;

        $this->update_campaign_status($data_for_update);

        if($campaign['is_multiple']=='Y') {
            $this->CI->load->model("V2_multiple_campaign_model");
            $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $data_for_update);
            return true;
        }
        $this->CI->V2_master_campaign_model->update($campaign['id'], $data_for_update);

        $this->create_audience($campaign);

        return array('status' => 'approved', 'message' => '');
    }

    public function update($campaign, $type)    {

        switch($type) {
            case "location":
                $this->update_location_targeting($campaign);
                break;
            case "budget":
                $this->update_budget($campaign);
                break;
            case "end_date":
                $this->update_end_date($campaign);
                break;
        }

    }

    public function create_campaign($campaign=null) {
        if($campaign['is_multiple']=='Y') {
            $this->CI->load->model("V2_multiple_campaign_model");
            $multiple_campaign = $this->CI->V2_multiple_campaign_model->get_by_campaign_id(null,$campaign['id']);
            
            $campaign['budget'] = $multiple_campaign['budget'];
        }

        $campaign_array["campaignName"]= $campaign['io'].' '.$campaign['name'].' '.$campaign['id'];
        $campaign_array["budget"] = $campaign['budget'];
        $campaign_array["budgetType"] = "DAILY";
        if($campaign['geotype'] == 'postalcode') {
            $campaign_array["channel"] = "SEARCH";
        } else {
            $campaign_array["channel"] = "SEARCH_AND_NATIVE";
        }
        $campaign_array["advertiserId"] = $this->advertiser_id;
        if($campaign['campaign_type'] == 'APP_INSTALL_YAHOO') {
            $campaign_array["channel"] = "NATIVE";
            $campaign_array["objective"] = "INSTALL_APP";
            $campaign_array["defaultLandingUrl"] = $campaign['app_url'];
            $campaign_array["trackingPartner"] = "none";
        } else {

            $campaign_array["objective"] = "VISIT_WEB";
        }
        $campaign_array["advancedGeoPos"] = "LOCATION_OF_PRESENCE";
        $campaign_array["status"] = "PAUSED";

        $campaign_json = json_encode($campaign_array);
        $response = $this->yahoo->create_campaign($campaign_json); var_dump($response);
        // check if campaign created successfully
        if($response['message']) {
            // save error message into db and return false
            $error_data_for_update['disapproval_reasons'] = $response['message'];
            $error_data_for_update['campaign_status'] = 'DISAPPROVED';
            // if this is multiple campaign then save errors in multiple table
            if($campaign['is_multiple']=='Y') {
                $this->CI->load->model("V2_multiple_campaign_model");
                $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
                return $response;
            }
            var_dump(123,  $response['message']);
            $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
            return $response;
        }
        $this->live_campaign = $response['result'];
        return true;
    }

    public function create_group($campaign_id, $campaign) {
        //generate group name
        $group_name = $campaign['name'].'_group';
        $bids = [];
        $bids[0]["priceType"] = "CPC";
        $bids[0]["value"] = $campaign['bid'];
        $bids[0]["channel"] = "SEARCH";

        $bids[1]["priceType"] = "CPC";
        $bid = $campaign['budget'] * 2 / 100;
        if($campaign['bid'] > $bid) {
            $campaign['bid'] = sprintf ("%.2f", $bid);
        }
        $bids[1]["value"] = $campaign['bid'];
        $bids[1]["channel"] = "NATIVE";

        $group_array["adGroupName"]= $group_name;
        $group_array["advertiserId"] = $this->advertiser_id;
        $group_array["campaignId"] = $campaign_id;
        //$group_array["objective"] = "VISIT_WEB";
        //$group_array["advancedGeoPos"] = "LOCATION_OF_PRESENCE";
        if($campaign['campaign_type'] == 'APP_INSTALL_YAHOO') {
            $group_array["ecpaGoal"] = 10;
            $group_array["biddingStrategy"] = "OPT_CONVERSION";
            $bids=[];
            $bids[0]["priceType"] = "CPC";
            $bids[0]["value"] = $campaign['bid'];
            $bids[0]["channel"] = "NATIVE";
        }

        $group_array["bidSet"]['bids'] = $bids;
        $group_array["status"] = "ACTIVE";
        $group_array["startDateStr"] = date('Y-m-d', strtotime($campaign['campaign_start_datetime']));
        $maxClick = $campaign['max_clicks'];
        $group_json = json_encode($group_array);
        $response = $this->yahoo->create_group($group_json);
        // check if campaign created successfully
        if($response['message']) {
            // save error message into db and return false
            // make status REJECTED
            $error_data_for_update['disapproval_reasons'] = $response['message'];
            $error_data_for_update['campaign_status'] = 'DISAPPROVED';
            // if this is multiple campaign then save errors in multiple table
            if($campaign['is_multiple']=='Y') {
                $this->CI->load->model("V2_multiple_campaign_model");
                $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
                return $response;
            }
            $this->CI->V2_master_campaign_model->update($campaign_id, $error_data_for_update);
            return $response;
        }

        $this->live_group = $response['result'];

        $data_for_update['network_campaign_id'] = $campaign_id;
        $data_for_update['network_group_id'] = $this->live_group;
        $data_for_update['group_status'] = 'ACTIVE';
        if($campaign['is_multiple']=='Y') {

            $this->CI->V2_group_model->update_multiple($campaign['id'], $data_for_update);
            return true;
        }
        $this->CI->V2_group_model->update($campaign['group_id'], $data_for_update);
        return true;
    }

    public function create_ad($ad_id, $for_edit = false) {
        // select from db all ads by campaign_id
        $ad = $this->CI->V2_ad_model->get_by_id($ad_id);
        // $ad_network =  $this->CI->V2_group_model->get_group_by_campaign_id($ad_id);
        if(!$ad){
            $response['message'] = 'No find ad by id '.$ad_id;
            return $response;
        }
        //add ads into adwords
        if($ad['creative_type'] == $this->text_ad_type){
            $response = $this->yahoo->create_text_ad($ad);
        } else {
            $ad_array['campaignId'] = $ad['network_campaign_id'];
            $ad_array['adGroupId'] = $ad['network_group_id'];
            $ad_array["advertiserId"] = $this->advertiser_id;

            $ad_array['callToActionText'] = $ad['action_buttons'];
            $ad_array['adName'] =  $ad['name'];
            $ad_array['title'] = $ad['title'];
            $ad_array['title2'] = '';
            $ad_array['sponsoredBy'] = $this->advertiser_name;
            $ad_array['description'] = $ad['description_1'];
            $ad_array['displayUrl'] = $ad['display_url'];
            $ad_array['landingUrl'] = $ad['original_url'];
            $ad_array['status'] = 'ACTIVE';
            $ad_array['imageUrlHQ'] = $ad['creative_url'];
            if($ad['creative_type']=='VIDEO_YAHOO') {
                $ad_array['imageUrl'] = $ad['square_creative_url'];
                $ad_array['videoPrimaryUrl'] = $ad['video_url'];
            }
            if($ad['creative_type']=='APP_INSTALL') {
                if($ad['tumblr_post_url']) {
                    $ad_array['contentUrl'] = $ad['tumblr_post_url'];
                }
                $ad_array['sponsoredBy'] = $ad['title'];
                $campaign = $this->CI->V2_master_campaign_model->get_by_id(null, $ad['campaign_id']);
                $ad_array['landingUrl'] = $campaign['app_url'];
            }  

            $ad_json = json_encode($ad_array);
            if($ad['is_multiple']=='Y') {
                $this->CI->load->model("V2_multiple_ad_model");
                $multiple_ad = $this->CI->V2_multiple_ad_model->get_by_ad_id($ad['id']);
                if(!$for_edit) {
                    $ad_array['landingUrl'] = $ad['destination_url'] . '/' . $this->network_id;
                }
                $response = $this->yahoo->create_ad($ad_json);
            }

            $response = $this->yahoo->create_ad($ad_json);
        }

        if($response['message']) {
            // when we edit ad we not need to save error message in db
            if(!$for_edit) {
                $error_data_for_update["approval_status"] = 'DISAPPROVED';
                $error_data_for_update["disapproval_reasons"] = $response['message'];
                $this->CI->V2_ad_model->update($ad_id, $error_data_for_update);
            }
            return $response;
        }

        $data_for_update=array();

        $data_for_update["network_creative_id"]=$response['result'];
        $data_for_update["creative_is_active"]='Y';

        if($for_edit && $ad['creative_status']=="ENABLED" && $ad['network_creative_id']) {
            if($ad['is_multiple']=='Y') {
                $this->CI->load->model("V2_multiple_ad_model");
                $multiple_ad = $this->CI->V2_multiple_ad_model->get_by_ad_id($ad['id']);
                $multiple_ad['creative_status'] = 'PAUSED';
                $this->update_ad_status($multiple_ad);
            } else {
                $ad['creative_status'] = 'PAUSED';
                $this->update_ad_status($ad);
            }
        }

        if($ad['is_multiple']=='Y') {
            $this->CI->load->model("V2_multiple_ad_model");
            $this->CI->V2_multiple_ad_model->updaet_by_ad_id($ad['id'], $data_for_update);
        } else {
            $this->CI->V2_ad_model->update($ad['id'], $data_for_update);
        }

        return $response;
    }

    public function update_ad($ad_id) {
        return $this->create_ad($ad_id, true);

    }

    public function update_ad_status($ad) {

        //add cahnges into adwords
        if($ad['creative_status'] == "ACTIVE" || $ad['creative_status'] == "ENABLED" ) {
            $status = "ACTIVE";
        } else {
            $status = "PAUSED";
        }

        if($ad['is_multiple']=='Y') {
            $this->CI->load->model("V2_multiple_ad_model");
            $multiple_ad = $this->CI->V2_multiple_ad_model->get_by_ad_id($ad['id']);
            $ad_array['id'] = $multiple_ad['network_creative_id'];
            $ad_array['status'] = $status;
            $ad_json = json_encode($ad_array);

            return $this->yahoo->update_ad($ad_json);
        }

        $ad_array['id'] = $ad['network_creative_id'];
        $ad_array['status'] = $status;
        $ad_json = json_encode($ad_array);

        return $this->yahoo->update_ad($ad_json);
    }

    public function update_campaign_status($campaign) {

        //add cahnges into adwords
        if($campaign['network_campaign_status'] == "ACTIVE" || $campaign['network_campaign_status'] == "ENABLED" ) {
            $status = "ACTIVE";
        } else {
            $status = "PAUSED";
        }
        if($campaign['is_multiple']=='Y') {
            $this->CI->load->model("V2_multiple_campaign_model");
            $multiple_campaign = $this->CI->V2_multiple_campaign_model->get_by_campaign_id(null,$campaign['id']);
            $campaign_array['id'] = $multiple_campaign['network_campaign_id'];
            $campaign_array['status'] = $status;
            $campaign_json = json_encode($campaign_array);
            return $this->yahoo->update_campaign($campaign_json);
        }

        $campaign_array['id'] = $campaign['network_campaign_id'];
        $campaign_array['status'] = $status;
        $campaign_json = json_encode($campaign_array);

        return $this->yahoo->update_campaign($campaign_json);
    }

    public function check_ads_approved_status() {

        $ads = $this->CI->V2_ad_model->get_ads_with_campaign_by_approval_status_and_network_id('UNCHECKED',$this->network_id);

        $ad_params_query = '?';
        $simbol = '';
        foreach($ads as $key=>$ad) {
            if($key != 0) {
                $simbol = '&';
            }
            $ad_params_query .= $simbol.'id='.$ad['network_creative_id'];

            $ads_sorted_array[$ad['network_creative_id']] = $ad;
        }

        $response = $this->yahoo->get_ads($ad_params_query); var_dump('<pre>',$ads_sorted_array, $response); //exit;

        $this->CI->load->model("V2_users_model");
        $this->CI->load->library('Send_email');

        foreach($response['result'] as $ad_array) {

            if(isset($ads_sorted_array[$ad_array['id']]) && $ad_array['editorialStatus'] != 'PENDING_REVIEW') {

                $reason_message = '';
                if($ad_array['editorialStatus'] == 'REJECTED') {
                    $data_for_update['approval_status'] = 'DISAPPROVED';
                } else {
                    $data_for_update['approval_status'] = $ad_array['editorialStatus'];
                }
//                if ($ad_array['reasons']) {
//                    foreach ($ad_array['reasons'] as $reasons) {
//                        $reason_message .= ' ' . $reasons;
//                    }
//                }

                $data_for_update['disapproval_reasons'] = $reason_message;
                $this->CI->V2_ad_model->update_by_network_creative_id($ad_array['id'], $data_for_update);

                if($data_for_update['approval_status'] == 'REJECTED') {
                    $user = $this->V2_users_model->get_by_id($ad['userid']);
                    if($user['user_email_ability'] == 'Y' || $user['is_admin'] == 1 ){ 
                        $this->CI->send_email->send_disapproved_ad($ad['email'], $ad['campaign_io'], $ad['campaign_name'], $ad['campaign_id'],$ad['creative_name'],$ad['creative_width'],$ad['creative_height'],$ad['campaign_type'], $reason_message);
                    }
                }
            }

        }
    }

    public function check_multiple_ads_approved_status($ads) {

        if ($ads == "")
            throw new exception("ad id required");

        $campaign_ids = array();

        foreach($ads as $ad) {
            if(!in_array($ad['network_campaign_id'],$campaign_ids)) {
                $campaign_ids[] = $ad['network_campaign_id'];
            }
        }
        $response = $this->adword->getAllDisapprovedAds($this->adword, $campaign_ids);
        foreach($response['result'] as $ad_array) {
            $reason_message = '';
            $data_for_update['approval_status'] = $ad_array['approval_status'];
            if($ad_array['reasons']) {
                foreach ($ad_array['reasons'] as $reasons) {
                    $reason_message .= ' ' . $reasons;
                }
            }
            $data_for_update['disapproval_reasons'] = $reason_message;
            $this->CI->V2_multiple_ad_model->update_by_network_creative_id($ad_array['network_creative_id'], $data_for_update);
        }
    }

    public function create_ads($group_id, $campaign) {

        // select from db all ads by campaign_id
        $ads = $this->CI->V2_ad_model->get_ads_by_campaign_id($campaign['id']);

        //add ads into adwords
        if($campaign['campaign_type'] == 'YAHOO_CAROUSEL') {

            $grp = array();
            foreach($ads as $key => $ad) {
                if($key == 0){
                    $displayUrl = $ad['display_url'];
                    $description = $ad['description_1'];
                    $adName = $campaign['name'].' ad ';
                    $landingUrl = $ad['original_url'];
                    $title = $ad['title'];
                }
               array_push($grp,array('imageUrl' => $ad['square_creative_url'], 
                            'type'=> 'MULTI_IMAGE',
                            'index'=> $key ,
                            'description'=> $ad['description_1'] ,
                            'imageUrlHQ'=> $ad['creative_url'],
                            'landingUrl'=> $ad['original_url'] ,
                            'title'=> $ad['title'] ,
                            'callToActionText'=> $ad['action_buttons'] )
               );
                $ad_array = [
                        'displayUrl' => $displayUrl,
                        'description'=> $description ,
                        'campaignId' => $this->live_campaign,
                        'title'=> $title,
                        'advertiserId' => $this->advertiser_id,
                        'adGroupId' => $group_id,
                        'sponsoredBy' => $this->advertiser_name,
                        'status' => 'ACTIVE',
                        'adName' => $adName,
                        'landingUrl' => $landingUrl,
                        'assets' => $grp
                    ];
                }
                 $ad_json = json_encode($ad_array); var_dump('ad array',$ad_array);
                    $response = $this->yahoo->create_ad($ad_json);

                    if($response['message']) {

                        $error_data_for_update["approval_status"]='DISAPPROVED';
                        $error_data_for_update["disapproval_reasons"]=$response['message']['message'];
                        if($campaign['is_multiple']=='Y') {
                            $this->CI->load->model("V2_multiple_ad_model");
                            $this->CI->V2_multiple_ad_model->update_by_ad_id($ad['id'], $error_data_for_update);
                        } else {
                            $this->CI->V2_ad_model->update($ad['id'], $error_data_for_update);
                        }

                    } else {

                        $data_for_update = array();
        //                $data_for_update["creative_status"] = '';
                        $data_for_update["approval_status"] = 'UNCHECKED';
                        $data_for_update["disapproval_reasons"] = '';
                        $data_for_update["network_creative_id"] = $response['result'];
                        $data_for_update["network_campaign_id"] = $this->live_campaign;
                        $data_for_update["network_group_id"] = $this->live_group;
                        $data_for_update["creative_is_active"] = 'Y';

                        if($campaign['is_multiple']=='Y') {
                            $this->CI->load->model("V2_multiple_ad_model");
                            $this->CI->V2_multiple_ad_model->update_by_ad_id($ad['id'], $data_for_update);
                        } else {
                            $this->CI->V2_ad_model->update($ad['id'], $data_for_update);
                        }
                    }

                    return true;
            }else{
                foreach($ads as $key => $ad) {
                    if($campaign['is_multiple']=='Y') {
                        $ad['destination_url'] = $ad['destination_url'].'/'.$this->network_id;
                    }

                    $ad_array['campaignId'] = $this->live_campaign;
                    $ad_array['adGroupId'] = $group_id;
                    $ad_array["advertiserId"] = $this->advertiser_id;

                    $ad_array['adName'] =  $campaign['name'].' ad '.$key;

                    $ad_array['callToActionText'] = $ad['action_buttons'];
                    $ad_array['title'] = $ad['title'];
                    $ad_array['title2'] = '';
                    $ad_array['sponsoredBy'] = $this->advertiser_name;
                    $ad_array['description'] = $ad['description_1'];
                    $ad_array['displayUrl'] = $ad['display_url'];
                    $ad_array['landingUrl'] = $ad['original_url'];
                    //$ad_array['url'] = $ad['destination_url'];
                    $ad_array['status'] = 'ACTIVE';
                    $ad_array['imageUrlHQ'] = $ad['creative_url'];

                    
                    if($ad['creative_type']=='VIDEO_YAHOO') {
                        $ad_array['imageUrl'] = $ad['square_creative_url'];
                        $ad_array['videoPrimaryUrl'] = $ad['video_url'];
                    }
                    if($ad['creative_type']=='APP_INSTALL') {
                        if($ad['tumblr_post_url']) {
                            $ad_array['contentUrl'] = $ad['tumblr_post_url'];
                        }
                        $ad_array['sponsoredBy'] = $ad['title'];
                        $ad_array['landingUrl'] = $campaign['app_url'];
                    }


                    $ad_json = json_encode($ad_array); var_dump('ad json',$ad_array);
                    $response = $this->yahoo->create_ad($ad_json);

                    if($response['message']) {

                        $error_data_for_update["approval_status"]='DISAPPROVED';
                        $error_data_for_update["disapproval_reasons"]=$response['message']['message'];
                        if($campaign['is_multiple']=='Y') {
                            $this->CI->load->model("V2_multiple_ad_model");
                            $this->CI->V2_multiple_ad_model->update_by_ad_id($ad['id'], $error_data_for_update);
                        } else {
                            $this->CI->V2_ad_model->update($ad['id'], $error_data_for_update);
                        }

                    } else {

                        $data_for_update = array();
        //                $data_for_update["creative_status"] = '';
                        $data_for_update["approval_status"] = 'UNCHECKED';
                        $data_for_update["disapproval_reasons"] = '';
                        $data_for_update["network_creative_id"] = $response['result'];
                        $data_for_update["network_campaign_id"] = $this->live_campaign;
                        $data_for_update["network_group_id"] = $this->live_group;
                        $data_for_update["creative_is_active"] = 'Y';

                        if($campaign['is_multiple']=='Y') {
                            $this->CI->load->model("V2_multiple_ad_model");
                            $this->CI->V2_multiple_ad_model->update_by_ad_id($ad['id'], $data_for_update);
                        } else {
                            $this->CI->V2_ad_model->update($ad['id'], $data_for_update);
                        }
                    }

                }

        return true;
        }
    }

    public function create_location_targeting($campaign) {

        $is_postal = false;
        switch(strtoupper($campaign['geotype'])) {
            default:
            case "COUNTRY":
                //$list_array = explode(",", $campaign['country']);
                $this->CI->load->model('V2_network_country_criterion_model');
                $criteria_array = $this->CI->V2_network_country_criterion_model->get_criteria_id_list($campaign['country'], $this->network_id);
                break;
            case "STATE":
                $list_array = explode(",", $campaign['state']);
                $this->CI->load->model('V2_network_state_criterion_model');
                $criteria_array = $this->CI->V2_network_state_criterion_model->get_criteria_id_list($list_array, $this->network_id);
                break;
            case "POSTALCODE":
                $is_postal = true;
                $criteria_array = $this->geo_locations_by_kordinate($campaign['zip'], $campaign['radius']);
                break;
        }

        if(!$criteria_array) {
            // check if criteria found in db
            $error_data_for_update['disapproval_reasons'] = "criteria_array is empty";
            if($campaign['is_multiple']=='Y') {
                $this->CI->load->model("V2_multiple_campaign_model");
                $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
            } else {
                $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
            }
            return false;
        }

        $type = $campaign['geotype'];
        $criteria_list = implode("," , $criteria_array);
        $error_ocured = false;
        foreach ($criteria_array as $criterion_id) {
            if($is_postal) {
                $location_array['value'] = implode(',', $criterion_id) . ',' . $campaign['radius'];
                $location_array['type'] = 'RADIUS';
            } else {
                $location_array['value'] = $criterion_id;
                $location_array['type'] = 'WOEID';
            }
            $location_array['status'] = 'ACTIVE';
            $location_array['advertiserId'] = $this->advertiser_id;
            $location_array['parentId'] = $this->live_campaign;
            $location_array['parentType'] = 'CAMPAIGN';
            $location_array['exclude'] = 'FALSE';
//			    $location_array['bidModifier'] = $criterion_id;
            $location_json = json_encode($location_array);
            $response = $this->yahoo->create_location_targeting($location_json);
            if($response['message']) {
                $error_ocured = true;
                $error_data_for_update["campaign_status"]='DISAPPROVED';
                $error_data_for_update["disapproval_reasons"]=$response['message'];
                if($campaign['is_multiple']=='Y') {
                    $this->CI->load->model("V2_multiple_campaign_model");
                    $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
                } else {
                    $this->CI->load->model('V2_log_model');
                    $this->V2_log_model->create($campaign['id'], 'postal error '.$criterion_id, 'zip');
                    //$this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
                }
                //break;
            } else {
                $response_array[] = $response['result'];
            }

        }

        if($is_postal && count($response_array)){
            $response_list = implode("," , $response_array);
            $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $campaign['zip'], $type, $response_list);
        } else {
            $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $criteria_list, $type);
        }

        return true;
    }

    public function geo_locations_by_kordinate($zip, $radius) {

        $this->CI->load->model('zip_model');
        $results = $this->CI->zip_model->get_kordinate_by_zip($zip, $radius);
        return $results;

    }

    public function create_age_targeting($campaign) {

        $criteria_array = explode(",", $campaign['age']);

        if(!$criteria_array) {
            // check if criteria found in db
            $error_data_for_update['disapproval_reasons'] = "criteria_array is empty";
            if($campaign['is_multiple']=='Y') {
                $this->CI->load->model("V2_multiple_campaign_model");
                $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
            } else {
                $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
            }
            return false;
        }

        $type = 'age';
        foreach ($criteria_array as $criterion_id){
            $age_array['value'] = $criterion_id;
            $age_array['type'] = 'AGE';
            $age_array['status'] = 'ACTIVE';
            $age_array['advertiserId'] = $this->advertiser_id;
            $age_array['parentId'] = $this->live_campaign;
            $age_array['parentType'] = 'CAMPAIGN';
            $age_array['exclude'] = 'FALSE';

            $age_json = json_encode($age_array);
            $response = $this->yahoo->create_targeting($age_json);
            if($response['message']) {
                $error_ocured = true;
                $error_data_for_update["campaign_status"]='DISAPPROVED';
                $error_data_for_update["disapproval_reasons"]=$response['message'];
                if($campaign['is_multiple']=='Y') {
                    $this->CI->load->model("V2_multiple_campaign_model");
                    $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
                } else {
                    $this->CI->load->model('V2_log_model');
                    $this->V2_log_model->create($campaign['id'], 'postal error '.$criterion_id, 'zip');
                    //$this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
                }
                //break;
            } else {
                $response_array[] = $response['result'];
            }
        }

        $response_list = implode("," , $response_array);

        $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $campaign['age'], $type, $response_list);
        return true;
    }

    public function create_gender_targeting($campaign) {

        $criteria_array = explode(",", $campaign['gender']);

        if(!$criteria_array) {
            // check if criteria found in db
            $error_data_for_update['disapproval_reasons'] = "criteria_array is empty";
            if($campaign['is_multiple']=='Y') {
                $this->CI->load->model("V2_multiple_campaign_model");
                $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
            } else {
                $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
            }
            return false;
        }

        $type = 'gender';
        foreach ($criteria_array as $criterion_id){
            $gender_array['value'] = strtoupper($criterion_id);
            $gender_array['type'] = 'GENDER';
            $gender_array['status'] = 'ACTIVE';
            $gender_array['advertiserId'] = $this->advertiser_id;
            $gender_array['parentId'] = $this->live_campaign;
            $gender_array['parentType'] = 'CAMPAIGN';
            $gender_array['exclude'] = 'FALSE';

            $gender_json = json_encode($gender_array);
            $response = $this->yahoo->create_targeting($gender_json);
            if($response['message']) {
                $error_ocured = true;
                $error_data_for_update["campaign_status"]='DISAPPROVED';
                $error_data_for_update["disapproval_reasons"]=$response['message'];
                if($campaign['is_multiple']=='Y') {
                    $this->CI->load->model("V2_multiple_campaign_model");
                    $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
                } else {
                    $this->CI->load->model('V2_log_model');
                    $this->V2_log_model->create($campaign['id'], 'postal error '.$criterion_id, 'zip');
                    //$this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
                }
                //break;
            } else {
                $response_array[] = $response['result'];
            }
        }

        $response_list = implode("," , $response_array);

        $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $campaign['age'], $type, $response_list);
        return true;
    }

    public function create_interest_targeting($campaign) {

        $criteria_array = explode(",", $campaign['interests']);

        if(!$criteria_array) {
            // check if criteria found in db
            $error_data_for_update['disapproval_reasons'] = "criteria_array is empty";
            if($campaign['is_multiple']=='Y') {
                $this->CI->load->model("V2_multiple_campaign_model");
                $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
            } else {
                $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
            }
            return array('message' => 'criteria_array is empty');
        }

        $type = 'interest';
        $criteria_list = implode("," , $criteria_array);

        foreach ($criteria_array as $criterion_id){ var_dump($criterion_id);
            $interest_array['value'] = $criterion_id;
            $interest_array['type'] = 'SEGMENT';
            $interest_array['status'] = 'ACTIVE';
            $interest_array['advertiserId'] = $this->advertiser_id;
            $interest_array['parentId'] = $this->live_campaign;
            $interest_array['parentType'] = 'CAMPAIGN';
            $interest_array['exclude'] = 'FALSE';

            $interest_json = json_encode($interest_array);
            $response = $this->yahoo->create_targeting($interest_json);

            if($response['message']) { var_dump($response['message']);
                // save error message into db and return false
                $error_data_for_update['disapproval_reasons'] = $response['message'];
                if($campaign['is_multiple']=='Y') {
                    $this->CI->load->model("V2_multiple_campaign_model");
                    $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
                } else {
                    $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
                }
                return $response;
            }
            $response_array[] = $response['result'];
        }
        $response_list = implode("," , $response_array);
        $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $criteria_list, $type, $response_list);
        return true;
    }

    public function create_carrier_targeting($campaign) {

        $list_array = explode(",", $campaign['carrier']);
        $criteria_array = $this->CI->V2_network_carrier_criterion_model->get_criteria_id_list($list_array, $campaign['country'], $this->network_id);
        //var_dump($criteria_array); exit;
        if(!$criteria_array) {
            // check if criteria found in db
            $error_data_for_update['disapproval_reasons'] = "criteria_array is empty";
            if($campaign['is_multiple']=='Y') {
                $this->CI->load->model("V2_multiple_campaign_model");
                $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
            } else {
                $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
            }
            return array('message' => 'criteria_array is empty');
        }
        //var_dump($criteria_array); exit;
        $criteria_list = implode("," , $criteria_array);
        $type = 'carrier';
        foreach ($criteria_array as $criterion_id){
            $response = $this->adword->createCarrierCriteria($this->adword, $this->live_campaign, $criterion_id);
            if($response['message']) {
                // save error message into db and return false
                $error_data_for_update['disapproval_reasons'] = $response['message'];
                if($campaign['is_multiple']=='Y') {
                    $this->CI->load->model("V2_multiple_campaign_model");
                    $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
                } else {
                    $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
                }
                return $response;
            }
        }

        $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $criteria_list, $type);
        return true;
    }

    public function create_io_targeting($campaign) {

        $list_array = explode(",", $campaign['remarketing_io']);
        $this->CI->load->model("Userlist_io_model");
        $this->CI->load->model("V2_campaign_network_criteria_rel_model");
        $criteria_array = $this->CI->Userlist_io_model->get_criteria_id_list($list_array);

        if(!$criteria_array) {
            // check if criteria found in db
            $error_data_for_update['disapproval_reasons'] = "criteria_array is empty";
            if($campaign['is_multiple']=='Y') {
                $this->CI->load->model("V2_multiple_campaign_model");
                $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
            } else {
                $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
            }
            return array('message' => 'criteria_array is empty');
        }
        $type = 'remarketing_io';
        $criteria_list = implode("," , $criteria_array);

        foreach ($criteria_array as $criterion_id){
            $response = $this->adword->createCriterion($this->adword, $this->live_group->id, $criterion_id);
            if($response['message']) {
                // save error message into db and return false
                $error_data_for_update['disapproval_reasons'] = $response['message'];
                if($campaign['is_multiple']=='Y') {
                    $this->CI->load->model("V2_multiple_campaign_model");
                    $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
                } else {
                    $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
                }
                return $response;
            }
            $result[] = $response['result'];
        }
        $result_list = implode("," , $result);
        $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $criteria_list, $type, $result_list);
    }

    public function create_vertical_targeting($campaign) {

        $list_array = explode(",", $campaign['vertical']);
        $this->CI->load->model("Userlist_vertical_model");
        $this->CI->load->model("V2_campaign_network_criteria_rel_model");
        $criteria_array = $this->CI->Userlist_vertical_model->get_criteria_id_list($list_array);

        if(!$criteria_array) {
            // check if criteria found in db
            $error_data_for_update['disapproval_reasons'] = "criteria_array is empty";
            if($campaign['is_multiple']=='Y') {
                $this->CI->load->model("V2_multiple_campaign_model");
                $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
            } else {
                $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
            }
            return array('message' => 'criteria_array is empty');
        }
        $type = 'remarketing_vertical';
        $criteria_list = implode("," , $criteria_array);

        foreach ($criteria_array as $criterion_id){
            $response = $this->adword->createCriterion($this->adword, $this->live_group->id, $criterion_id);
            if($response['message']) {
                // save error message into db and return false
                $error_data_for_update['disapproval_reasons'] = $response['message'];
                if($campaign['is_multiple']=='Y') {
                    $this->CI->load->model("V2_multiple_campaign_model");
                    $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
                } else {
                    $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
                }
                return $response;
            }
            $result[] = $response['result'];
        }
        $result_list = implode("," , $result);
        $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $criteria_list, $type, $result_list);
    }

    public function update_location_targeting($campaign) {

        if($campaign['is_multiple']=='Y') {
            $this->CI->load->model("V2_multiple_campaign_model");
            $multiple_campaign = $this->CI->V2_multiple_campaign_model->get_by_campaign_id(null,$campaign['id']);
//            $campaign['budget'] = $multiple_campaign['budget'];
            $campaign['network_campaign_id'] = $multiple_campaign['network_campaign_id'];
        }
        $is_postal = false;
        $this->CI->load->model(V2_campaign_network_criteria_rel_model);
        $old_criteria_rows = $this->CI->V2_campaign_network_criteria_rel_model->get_all_locations_by_campaign_id($campaign['id'],$this->network_id);
        $type = $campaign['geotype'];

        // transfer current criteria mutching code from foreach last else statment
        switch(strtoupper($campaign['geotype'])) {

            case "COUNTRY":
                //$list_array = explode(",", $campaign['country']);
                $old_country = '';
                $this->CI->load->model('V2_network_country_criterion_model');
                $new_criteria_array = $this->CI->V2_network_country_criterion_model->get_criteria_id_list($campaign['country'], $this->network_id);

                //remove old criterias from adwords
                foreach($old_criteria_rows as $old_criterion_row) {
                    //remove all another location criteria from adwords (postalcode and states)
                    if($old_criterion_row['criteria_type']=='state') {
                        $old_criteria_array = explode(",", $old_criterion_row['criteria_value']);
                        // remove from adword add new func remove campaign criteria
                        foreach ($old_criteria_array as $criteria) {
                            $array['id'] = $criteria;
                            $array['status'] = 'DELETED';
                            $removable_array[] = $array;
                        }
                        $removable_json = json_encode($removable_array);
                        $this->yahoo->remove_location_targeting($removable_json);
                        $this->CI->V2_campaign_network_criteria_rel_model->delete_by_id($old_criterion_row['id']);
                        //remove from our db network-criteri-rel table
                    } elseif($old_criterion_row['criteria_type']=='postalcode') {
                        $old_criteria_array = explode(",", $old_criterion_row['criteria_network_value']);
                        // remove from adword add new func remove campaign criteria
                        foreach ($old_criteria_array as $criteria) {
                            $array['id'] = $criteria;
                            $array['status'] = 'DELETED';
                            $removable_array[] = $array;
                        }
                        $removable_json = json_encode($removable_array);
                        $this->yahoo->remove_location_targeting($removable_json);
                        //remove from our db network-criteri-rel table
                        $this->CI->V2_campaign_network_criteria_rel_model->delete_by_id($old_criterion_row['id']);
                    } else {
                        $old_country = $old_criterion_row['criteria_value'];
                        $old_country_row_id = $old_criterion_row['id'];
                    }
                }

                if($old_country) {
                    if($new_criteria_array[0] != $old_criterion_row['criteria_value']){
                        $old_criteria_array = explode(",", $old_country);
                        foreach ($old_criteria_array as $criteria) {
                            $array['id'] = $criteria;
                            $array['status'] = 'DELETED';
                            $removable_array[] = $array;
                        }
                        $removable_json = json_encode($removable_array);
                        $this->yahoo->remove_location_targeting($removable_json);

                        foreach ($new_criteria_array as $criterion_id) {
                            $location_array['value'] = $criterion_id;
                            $location_array['type'] = 'WOEID';

                            $location_array['status'] = 'ACTIVE';
                            $location_array['advertiserId'] = $this->advertiser_id;
                            $location_array['parentId'] = $campaign['network_campaign_id'];
                            $location_array['parentType'] = 'CAMPAIGN';
                            $location_array['exclude'] = 'FALSE';
                            $location_json = json_encode($location_array);

                            $response = $this->yahoo->create_location_targeting($location_json);

                            if ($response['message']) {
                                $error_ocured = true;
                                $error_data_for_update["campaign_status"] = 'DISAPPROVED';
                                $error_data_for_update["disapproval_reasons"] = $response['message'];
                                if ($campaign['is_multiple'] == 'Y') {
                                    $this->CI->load->model("V2_multiple_campaign_model");
                                    $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
                                } else {
                                    $this->CI->load->model('V2_log_model');
                                    $this->V2_log_model->create($campaign['id'], 'postal error ' . $criterion_id, 'zip');
                                    //$this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
                                }
                                //break;
                            } else {
                                $response_array[] = $response['result'];
                            }
                        }
                        $response_list = implode("," , $response_array);
                        $this->CI->V2_campaign_network_criteria_rel_model->update_value_by_id($old_country_row_id, $new_criteria_array[0], $response_list);
                    }
                } else {
                    foreach ($new_criteria_array as $criterion_id) {
                        $location_array['value'] = $criterion_id;
                        $location_array['type'] = 'WOEID';

                        $location_array['status'] = 'ACTIVE';
                        $location_array['advertiserId'] = $this->advertiser_id;
                        $location_array['parentId'] = $campaign['network_campaign_id'];
                        $location_array['parentType'] = 'CAMPAIGN';
                        $location_array['exclude'] = 'FALSE';
                        $location_json = json_encode($location_array);

                        $response = $this->yahoo->create_location_targeting($location_json);

                        if ($response['message']) {
                            $error_ocured = true;
                            $error_data_for_update["campaign_status"] = 'DISAPPROVED';
                            $error_data_for_update["disapproval_reasons"] = $response['message'];
                            if ($campaign['is_multiple'] == 'Y') {
                                $this->CI->load->model("V2_multiple_campaign_model");
                                $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
                            } else {
                                $this->CI->load->model('V2_log_model');
                                $this->V2_log_model->create($campaign['id'], 'postal error ' . $criterion_id, 'zip');
                                //$this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
                            }
                            //break;
                        } else {
                            $response_array[] = $response['result'];
                        }
                    }
                    $response_list = implode("," , $response_array);
                    $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $new_criteria_array[0], $type, $response_list);
                }

                break;
            case "STATE":

                $old_states = '';
                $this->CI->load->model(V2_network_state_criterion_model);
                $list_array = explode(",", $campaign['state']);
                $new_criteria_array = $this->CI->V2_network_state_criterion_model->get_criteria_id_list($list_array, $this->network_id);
                //remove old criterias from adwords
                foreach($old_criteria_rows as $old_criterion_row) {
                    //remove all another location criteria from adwords (postalcode and states)
                    if($old_criterion_row['criteria_type']=='country') {
                        $old_criteria_array = explode(",", $old_criterion_row['criteria_value']);
                        // remove from adword add new func remove campaign criteria
                        foreach ($old_criteria_array as $criteria) {
                            $array['id'] = $criteria;
                            $array['status'] = 'DELETED';
                            $removable_array[] = $array;
                        }
                        $removable_json = json_encode($removable_array);
                        $this->yahoo->remove_location_targeting($removable_json);
                        $this->CI->V2_campaign_network_criteria_rel_model->delete_by_id($old_criterion_row['id']);
                        //remove from our db network-criteri-rel table
                    } elseif($old_criterion_row['criteria_type']=='postalcode') {
                        $old_criteria_array = explode(",", $old_criterion_row['criteria_network_value']);
                        // remove from adword add new func remove campaign criteria
                        foreach ($old_criteria_array as $criteria) {
                            $array['id'] = $criteria;
                            $array['status'] = 'DELETED';
                            $removable_array[] = $array;
                        }
                        $removable_json = json_encode($removable_array);
                        $this->yahoo->remove_location_targeting($removable_json);
                        //remove from our db network-criteri-rel table
                        $this->CI->V2_campaign_network_criteria_rel_model->delete_by_id($old_criterion_row['id']);
                    } else {

                        $old_states = $old_criterion_row['criteria_value'];
                        $old_states_row_id = $old_criterion_row['id'];
                    }
                }
                if($old_states) {

                    if($new_criteria_array[0] != $old_criterion_row['criteria_value']){
                        $old_criteria_array = explode(",", $old_states);
                        foreach ($old_criteria_array as $criteria) {
                            $array['id'] = $criteria;
                            $array['status'] = 'DELETED';
                            $removable_array[] = $array;
                        }
                        $removable_json = json_encode($removable_array);
                        $this->yahoo->remove_location_targeting($removable_json);

                        foreach ($new_criteria_array as $criterion_id) {
                            $location_array['value'] = $criterion_id;
                            $location_array['type'] = 'WOEID';

                            $location_array['status'] = 'ACTIVE';
                            $location_array['advertiserId'] = $this->advertiser_id;
                            $location_array['parentId'] = $campaign['network_campaign_id'];
                            $location_array['parentType'] = 'CAMPAIGN';
                            $location_array['exclude'] = 'FALSE';
                            $location_json = json_encode($location_array);

                            $response = $this->yahoo->create_location_targeting($location_json);

                            if ($response['message']) {
                                $error_ocured = true;
                                $error_data_for_update["campaign_status"] = 'DISAPPROVED';
                                $error_data_for_update["disapproval_reasons"] = $response['message'];
                                if ($campaign['is_multiple'] == 'Y') {
                                    $this->CI->load->model("V2_multiple_campaign_model");
                                    $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
                                } else {
                                    $this->CI->load->model('V2_log_model');
                                    $this->V2_log_model->create($campaign['id'], 'postal error ' . $criterion_id, 'zip');
                                    //$this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
                                }
                                //break;
                            } else {
                                $response_array[] = $response['result'];
                            }
                        }
                        $response_list = implode("," , $response_array);
                        $this->CI->V2_campaign_network_criteria_rel_model->update_value_by_id($old_states_row_id, $new_criteria_array[0], $response_list);
                    }
                    //remove from our db network-criteri-rel table and update new values
                } else {
                    foreach ($new_criteria_array as $criterion_id) {
                        $location_array['value'] = $criterion_id;
                        $location_array['type'] = 'WOEID';

                        $location_array['status'] = 'ACTIVE';
                        $location_array['advertiserId'] = $this->advertiser_id;
                        $location_array['parentId'] = $campaign['network_campaign_id'];
                        $location_array['parentType'] = 'CAMPAIGN';
                        $location_array['exclude'] = 'FALSE';
                        $location_json = json_encode($location_array);

                        $response = $this->yahoo->create_location_targeting($location_json);

                        if ($response['message']) {
                            $error_ocured = true;
                            $error_data_for_update["campaign_status"] = 'DISAPPROVED';
                            $error_data_for_update["disapproval_reasons"] = $response['message'];
                            if ($campaign['is_multiple'] == 'Y') {
                                $this->CI->load->model("V2_multiple_campaign_model");
                                $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
                            } else {
                                $this->CI->load->model('V2_log_model');
                                $this->V2_log_model->create($campaign['id'], 'postal error ' . $criterion_id, 'zip');
                                //$this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
                            }
                            //break;
                        } else {
                            $response_array[] = $response['result'];
                        }
                    }
                    $response_list = implode("," , $response_array);
                    $criteria_list = implode("," , $new_criteria_array); // hanel verev
                    $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $criteria_list, $type, $response_list);
                }

                break;
            case "POSTALCODE":

                $old_postal = '';
                $new_criteria_array = explode(",", $campaign['zip']);
                //$new_criteria_array = $this->CI->V2_network_zip_criterion_model->get_criteria_id_list($list_array, $this->network_id);

                foreach($old_criteria_rows as $old_criterion_row) {
                    //remove all another location criteria from adwords (postalcode and states)
                    if($old_criterion_row['criteria_type']=='country') {
                        $old_criteria_array = explode(",", $old_criterion_row['criteria_value']);
                        // remove from adword add new func remove campaign criteria
                        foreach ($old_criteria_array as $criteria) {
                            $array['id'] = $criteria;
                            $array['status'] = 'DELETED';
                            $removable_array[] = $array;
                        }
                        $removable_json = json_encode($removable_array);
                        $this->yahoo->remove_location_targeting($removable_json);
                        $this->CI->V2_campaign_network_criteria_rel_model->delete_by_id($old_criterion_row['id']);
                        //remove from our db network-criteri-rel table
                    } elseif($old_criterion_row['criteria_type']=='state') {
                        $old_criteria_array = explode(",", $old_criterion_row['criteria_value']);
                        // remove from adword add new func remove campaign criteria
                        foreach ($old_criteria_array as $criteria) {
                            $array['id'] = $criteria;
                            $array['status'] = 'DELETED';
                            $removable_array[] = $array;
                        }
                        $removable_json = json_encode($removable_array);
                        $this->yahoo->remove_location_targeting($removable_json);
                        $this->CI->V2_campaign_network_criteria_rel_model->delete_by_id($old_criterion_row['id']);
                        //remove from our db network-criteri-rel table
                    } else {
                        //remove all old criteria then add all new
                        $old_postal = $old_criterion_row['criteria_network_value'];
                        $old_postal_row_id = $old_criterion_row['id'];
                    }
                }

                if($old_postal) {
                    if($new_criteria_array[0] != $old_criterion_row['criteria_value']){
                        $old_criteria_array = explode(",", $old_postal);
                        foreach ($old_criteria_array as $criteria) {
                            $array['id'] = $criteria;
                            $array['status'] = 'DELETED';
                            $removable_array[] = $array;
                        }
                        $removable_json = json_encode($removable_array);
                        $this->yahoo->remove_location_targeting($removable_json);

                        foreach ($new_criteria_array as $criterion_id) {
                            $location_array['value'] = $criterion_id;
                            $location_array['type'] = 'WOEID';

                            $location_array['status'] = 'ACTIVE';
                            $location_array['advertiserId'] = $this->advertiser_id;
                            $location_array['parentId'] = $campaign['network_campaign_id'];
                            $location_array['parentType'] = 'CAMPAIGN';
                            $location_array['exclude'] = 'FALSE';
                            $location_json = json_encode($location_array);

                            $response = $this->yahoo->create_location_targeting($location_json);

                            if ($response['message']) {
                                $error_ocured = true;
                                $error_data_for_update["campaign_status"] = 'DISAPPROVED';
                                $error_data_for_update["disapproval_reasons"] = $response['message'];
                                if ($campaign['is_multiple'] == 'Y') {
                                    $this->CI->load->model("V2_multiple_campaign_model");
                                    $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
                                } else {
                                    $this->CI->load->model('V2_log_model');
                                    $this->V2_log_model->create($campaign['id'], 'postal error ' . $criterion_id, 'zip');
                                    //$this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
                                }
                                //break;
                            } else {
                                $response_array[] = $response['result'];
                            }
                        }
                        $response_list = implode("," , $response_array);
                        $this->CI->V2_campaign_network_criteria_rel_model->update_value_by_id($old_postal_row_id, $new_criteria_array[0], $response_list);
                    }
                    //remove from our db network-criteri-rel table and update new values
                } else {
                    foreach ($new_criteria_array as $criterion_id) {
                        $location_array['value'] = $criterion_id;
                        $location_array['type'] = 'WOEID';

                        $location_array['status'] = 'ACTIVE';
                        $location_array['advertiserId'] = $this->advertiser_id;
                        $location_array['parentId'] = $campaign['network_campaign_id'];
                        $location_array['parentType'] = 'CAMPAIGN';
                        $location_array['exclude'] = 'FALSE';
                        $location_json = json_encode($location_array);

                        $response = $this->yahoo->create_location_targeting($location_json);

                        if ($response['message']) {
                            $error_ocured = true;
                            $error_data_for_update["campaign_status"] = 'DISAPPROVED';
                            $error_data_for_update["disapproval_reasons"] = $response['message'];
                            if ($campaign['is_multiple'] == 'Y') {
                                $this->CI->load->model("V2_multiple_campaign_model");
                                $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
                            } else {
                                $this->CI->load->model('V2_log_model');
                                $this->V2_log_model->create($campaign['id'], 'postal error ' . $criterion_id, 'zip');
                                //$this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
                            }
                            //break;
                        } else {
                            $response_array[] = $response['result'];
                        }
                    }
                    $response_list = implode("," , $response_array);
                    $criteria_list = implode("," , $new_criteria_array); // hanel verev
                    $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $criteria_list, $type, $response_list);
                }

                //remove from our db network-criteri-rel table and update new values
                break;
        }
    }

    public function create_keywords($campaign) {

        if($campaign['is_multiple']=='Y') {
            $this->CI->load->model("V2_multiple_campaign_model");
            $multiple_campaign = $this->CI->V2_multiple_campaign_model->get_by_campaign_id(null,$campaign['id']);
//            $campaign['budget'] = $multiple_campaign['budget'];
            $campaign['network_campaign_id'] = $multiple_campaign['network_campaign_id'];
        }

        $keywords_array = explode(',',$campaign['keywords']);
        if($keywords_array[0]!="RON"){

            $type = 'keyword';
            foreach ($keywords_array as $keyword) {

                $keyword_array['value'] = $keyword;
                $keyword_array['bidSet'] = null;
                $keyword_array['matchType'] = 'BROAD';
                $keyword_array['status'] = 'ACTIVE';
                $keyword_array['advertiserId'] = $this->advertiser_id;
                $keyword_array['parentId'] = $this->live_group;
                $keyword_array['parentType'] = 'ADGROUP';
                $keyword_array['exclude'] = 'FALSE';

                $keyword_json = json_encode($keyword_array);
                $response = $this->yahoo->create_keyword($keyword_json);

                if($response['message']){
                    $this->CI->load->model('V2_log_model');
                    $this->V2_log_model->create($campaign['id'], 'keyword error '.$response['message'], 'keyword');
                    return false;
                    // save in log table
                } else {
                    $response_array[] = $response['result'];
                }

            }


            if($response_array) {
                $criteria_list = $campaign['keywords'];
                $response_list = implode("," , $response_array);
                $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $criteria_list, $type, $response_list);
            }
        }

        return true;

    }

    public function update_keywords($campaign) {

        if($campaign['is_multiple']=='Y') {
            $this->CI->load->model("V2_multiple_campaign_model");
            $multiple_campaign = $this->CI->V2_multiple_campaign_model->get_by_campaign_id(null,$campaign['id']);
//            $campaign['budget'] = $multiple_campaign['budget'];
            $campaign['network_campaign_id'] = $multiple_campaign['network_campaign_id'];
        }

        $this->CI->load->model('V2_campaign_network_criteria_rel_model');
        $group = $this->CI->V2_group_model->get_group_by_campaign_id($campaign['id']);
        $type = 'keyword';

        // check if we have keywords for current campaign
        $old_keywords = $this->CI->V2_campaign_network_criteria_rel_model->get_criteria_by_campaign_id_network_id_and_type($campaign['id'],$this->network_id,$type);
        $new_keywords_array = explode(',',$campaign['keywords']);
        if($new_keywords_array[0]!="RON") {

            foreach ($new_keywords_array as $keyword) {

                $keyword_array['value'] = $keyword;
                $keyword_array['bidSet'] = null;
                $keyword_array['matchType'] = 'BROAD';
                $keyword_array['status'] = 'ACTIVE';
                $keyword_array['advertiserId'] = $this->advertiser_id;
                $keyword_array['parentId'] = $group['network_group_id'];
                $keyword_array['parentType'] = 'ADGROUP';
                $keyword_array['exclude'] = 'FALSE';

                $keyword_json = json_encode($keyword_array);
                $response = $this->yahoo->create_keyword($keyword_json);

                if($response['message']){
                    $this->CI->load->model('V2_log_model');
                    $this->V2_log_model->create($campaign['id'], 'keyword error '.$response['message'], 'keyword');
                    return false;
                    // save in log table
                } else {
                    $response_array[] = $response['result'];
                }

            }

            $response_list = implode("," , $response_array);

            if ($old_keywords) {
                // remove old keyword from group
                // get old keyword id
                $old_list_array = explode(",", $old_keywords['criteria_value']);

                $old_network_list_array = explode(",", $old_keywords['criteria_network_value']);

                foreach($old_network_list_array as $list) {
                    $keyword_array['id'] = $list;
                    $keyword_array['status'] = 'DELETED';
                    $keyword_json = json_encode($keyword_array);
                    $keyword_response = $this->yahoo->remove_keyword($keyword_json);
                }

                $this->CI->V2_campaign_network_criteria_rel_model->update_network_value_by_id($old_keywords['id'], $campaign['keywords'], $response_list);

            } else {
                // save keyword criteria for current campaign in db
                $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $campaign['keywords'], $type, $response_list);
            }
        }

        return true;

    }

    public function update_budget($campaign) {

        if($campaign['is_multiple']=='Y') {
            $this->CI->load->model("V2_multiple_campaign_model");
            $multiple_campaign = $this->CI->V2_multiple_campaign_model->get_by_campaign_id(null,$campaign['id']);
            $campaign['budget'] = $multiple_campaign['budget'];
            $campaign['network_campaign_id'] = $multiple_campaign['network_campaign_id'];
        }

        $campaign_array["id"] = $campaign['network_campaign_id'];
        $campaign_array["budget"] = $campaign['budget'];
        $campaign_json = json_encode($campaign_array);

        return $this->yahoo->update_campaign($campaign_json);
    }

    public function get_demographics_report() {
        //return true;
        $reporting_type = [
            'device_type'=>'Device Type',
            'gender'=>'Gender',
            'age'=>'Age',
        ];
        $this->CI->load->model('V2_yahoo_job_model');
        $existing_jobs = $this->CI->V2_yahoo_job_model->get_demographic_jobs_by_status('submitted'); var_dump('this is yahoo',$existing_jobs); //exit;
        if(count($existing_jobs) == 3) { var_dump(555);
            return true;
        }
        if($existing_jobs) {
            foreach ($existing_jobs as $existing_job) {
                unset($reporting_type[$existing_job['type']]);
            }
        }
        //var_dump($reporting_type); exit;
        $campaigns = $this->CI->V2_master_campaign_model->get_active_campaigns_by_network_id($this->network_id); //var_dump($campaigns); exit;

        $campaign_ids = [];
        foreach ($campaigns as $campaign){
            $campaign_ids[] = $campaign['network_campaign_id'];
        }
//        $campaign_ids[]=353378171;
//        $campaign_ids[]=353082826;
        var_dump($campaign_ids);
        $this->CI->load->model('V2_yahoo_job_model');
        if($campaign_ids) {
            foreach ($reporting_type as $key=>$type) {

                $fields_array = [
                    ['field' => "Campaign ID"],
                    ['field' => "Impressions"],
                    ['field' => "Clicks"],
                    ['field' => "Day"],
                    ['field' => $type],
                ];

                $filters_array = [
                    ['field' => "Advertiser ID", 'operator' => "=", "value" => $this->advertiser_id],
                    ['field' => "Campaign ID", 'operator' => "IN", "values" => $campaign_ids],
                    //['field' => "Day", 'operator' => "between", "from" => date('Y-m-d', strtotime($campaigns[0]['campaign_start_datetime'])), 'to' => date('Y-m-d')],
                    ['field' => "Day", 'operator' => "between", "from" =>'2016-10-17', 'to' =>  date('Y-m-d')],
                ];
                $report_array['cube'] = "user_stats";
                $report_array['fields'] = $fields_array;
                $report_array['filters'] = $filters_array;
                $report_json = json_encode($report_array);

                $response = $this->yahoo->get_reporting($report_json);
                var_dump($response);
                if ($response['result']) {
                    $insert_array['type'] = $key;
                    $insert_array['job_id'] = $response['result']['jobId'];
                    $insert_array['status'] = $response['result']['status'];
                    $insert_array['created_date'] = date('Y-m-d h:i:s');
                    $this->CI->V2_yahoo_job_model->create($insert_array);
                }
            }
        }
        return true;
    }

    public function get_ads_impressions2() { return true; var_dump(345);
        // get all ads by campaign_id
        $ads = $this->CI->V2_ad_model->get_active_campaigns_ads_by_network_id($this->network_id);
//        var_dump((int)$ads[0]['impressions_count']); exit;
        echo '<pre>';
        var_dump($ads);
        foreach ($ads as $ad){
            $ad_ids[] = $ad['network_creative_id'];
        }
        $fields_array = [
            ['field' => "Ad ID"],
            ['field' => "Day"],
            ['field' => "Impressions"],
            ['field' => "Clicks"],
            ['field' => "CTR"],
        ];

        $filters_array = [
            ['field' => "Advertiser ID", 'operator' => "=", "value" => $this->advertiser_id ],
            ['field' => "Campaign ID", 'operator' => "=", "value" => '353082826' ],
            ['field' => "Ad Group ID", 'operator' => "=", "value" => '9102021378' ],
            ['field' => "Day", 'operator' => "between", "from" => '2016-09-01', 'to' => '2016-10-12'],
        ];
        $report_array['cube'] =  "performance_stats";
        $report_array['fields'] =  $fields_array;
        $report_array['filters'] =  $filters_array;
        var_dump($report_array);
        $report_json = json_encode($report_array);
//        var_dump($report_json, $this->yahoo);
        $response = $this->yahoo->get_reporting($report_json);
        var_dump('yahoo',$response);
        // check if campaign created successfully
//		if($response['message']) {
//			return false;
//		}
//
//		if($response['result']) {
//			foreach($response['result'] as $row) {
//				$repo = (array)$row;
//				$reports[] = reset($repo);
//			}
//		}
//		//var_dump($reports); exit;
//		//adds the ad performance report into the database
//        $this->CI->load->model('V2_campclick_impression_model');
//        foreach ($reports as $report){
//            foreach($ads as $ad){
//                if($report["adID"]==$ad['network_creative_id']){
//                    $data_for_update = array();
//                    $data_for_update['ad_id'] = $ad['id'];
//                    $data_for_update['network_id'] = $this->network_id;
//                    $data_for_update['campaign_id'] = $ad['campaign_id'];
//                    $data_for_update['impressions_count'] = (int)$report["impressions"] - (int)$ad['impressions_count'];
//					if($data_for_update['impressions_count'] && (int)$report["impressions"]) {
////                        if($ad['campaign_id']==311) {
////                            var_dump($report["impressions"], $ad['impressions_count'],$ad['campaign_id'], $data_for_update['impressions_count']);
////                        }
//                        if($data_for_update['ad_id']==763) {
//                            $data_for_update['impressions_count'] = $data_for_update['impressions_count'] + 384624;
//                        } elseif($data_for_update['ad_id']==762)  {
//                            $data_for_update['impressions_count'] = $data_for_update['impressions_count'] + 500680;
//                        } elseif($data_for_update['ad_id']==761) {
//                            $data_for_update['impressions_count'] = $data_for_update['impressions_count'] + 40241;
//                        }
//                        $this->CI->V2_campclick_impression_model->create($data_for_update);
//					}
//				}
//            }
//        }
        return true;
    }

    public function get_ads_impressions() {
        $this->CI->load->model('V2_yahoo_job_model');
        $existing_jobs = $this->CI->V2_yahoo_job_model->get_jobs_count_by_status_and_type('submitted','performance_stats'); //var_dump($existing_jobs); exit;
        // get all ads by campaign_id
        if($existing_jobs) {
            return true;
        }

        $ads = $this->CI->V2_ad_model->get_active_campaigns_ads_by_network_id($this->network_id);
        echo '<pre>';
        //var_dump($ads);
        foreach ($ads as $ad){
            if($ad['approval_status'] != 'DISAPPROVED') {
                $ad_ids[] = $ad['network_creative_id'];
            }
        }
        var_dump($ad_ids);
//        $ad_ids[] = 32455128691;
//        $ad_ids[] = 32459816347;
        $fields_array = [
            ['field' => "Ad ID"],
            ['field' => "Day"],
            ['field' => "Impressions"],
            ['field' => "Clicks"],
            ['field' => "CTR"],
            ['field' => "Spend"],
//          ['field' => "Video 25% Complete"],
//          ['field' => "Video 50% Complete"],
//          ['field' => "Video 75% Complete"],
//          ['field' => "Video 100% Complete"],
//          ['field' => "Video Views"],
//          ['field' => "Video Starts"],
//          ['field' => "Video Closed"],
//          ['field' => "Video Skipped"],
//          ['field' => "Video after 30 seconds view"],
        ];

        $filters_array = [
            ['field' => "Advertiser ID", 'operator' => "=", "value" => $this->advertiser_id ],
            ['field' => "Ad ID", 'operator' => "IN", "values" => $ad_ids ],
            //['field' => "Ad Group ID", 'operator' => "=", "value" => '9102021378' ],
//          ['field' => "Campaign ID", 'operator' => "=", "value" => '9102021378' ],
            ['field' => "Day", 'operator' => "between", "from" => '2016-10-17', 'to' =>  date('Y-m-d')],
        ];
        $report_array['cube'] =  "performance_stats";
        $report_array['fields'] =  $fields_array;
        $report_array['filters'] =  $filters_array;

        //var_dump($report_array);
        $report_json = json_encode($report_array);
//        var_dump($report_json, $this->yahoo);
        $response = $this->yahoo->get_reporting($report_json);
        var_dump($response);
        // check if campaign created successfully
        if($response['message']) {
            return false;
        }
//
        if($response['result']) {
            $insert_array['type'] = $report_array['cube'];
            $insert_array['job_id'] = $response['result']['jobId'];
            $insert_array['status'] = $response['result']['status'];
            $insert_array['created_date'] = date('Y-m-d h:i:s');
            $this->CI->V2_yahoo_job_model->create($insert_array);
        }

        return true;
    }

    public function get_campaigns_video_report()
    {
        $this->CI->load->model('V2_yahoo_job_model');
        $existing_jobs = $this->CI->V2_yahoo_job_model->get_jobs_count_by_status_and_type('submitted', 'video_stats'); //var_dump($existing_jobs); exit;
        // get all ads by campaign_id
        if ($existing_jobs) {
            return true;
        }

        $campaigns = $this->CI->V2_master_campaign_model->get_active_video_campaigns_by_network_id($this->network_id); //var_dump($campaigns); exit;

        $campaign_ids = [];
        foreach ($campaigns as $campaign) {
            $campaign_ids[] = $campaign['network_campaign_id'];
        }

        $fields_array = [
            ['field' => "Campaign ID"],
            ['field' => "Day"],
            ['field' => "Impressions"],
            ['field' => "Clicks"],
            ['field' => "CTR"],
            ['field' => "Spend"],
            ['field' => "Video 25% Complete"],
            ['field' => "Video 50% Complete"],
            ['field' => "Video 75% Complete"],
            ['field' => "Video 100% Complete"],
            ['field' => "Video Views"],
            ['field' => "Video Starts"],
            ['field' => "Video Closed"],
            ['field' => "Video Skipped"],
            ['field' => "Video after 30 seconds view"],
        ];

        $filters_array = [
            ['field' => "Advertiser ID", 'operator' => "=", "value" => $this->advertiser_id],
            ['field' => "Campaign ID", 'operator' => "IN", "values" => $campaign_ids],
            ['field' => "Day", 'operator' => "between", "from" => '2016-10-17', 'to' =>  date('Y-m-d')],
        ];
        $report_array['cube'] = "performance_stats";
        $report_array['fields'] = $fields_array;
        $report_array['filters'] = $filters_array;

        //var_dump($report_array);
        $report_json = json_encode($report_array);
//        var_dump($report_json, $this->yahoo);
        $response = $this->yahoo->get_reporting($report_json);
        var_dump($response);
        // check if campaign created successfully
        if ($response['message']) {
            return false;
        }
//
        if ($response['result']) {
            $insert_array['type'] = 'video_stats';
            $insert_array['job_id'] = $response['result']['jobId'];
            $insert_array['status'] = $response['result']['status'];
            $insert_array['created_date'] = date('Y-m-d h:i:s');
            $this->CI->V2_yahoo_job_model->create($insert_array);
        }

        return true;
    }

    public function get_ads_impressions1() {
        echo '<pre>';
        $reporting_type = [
            'device_type'=>'Device Type',
            'gender'=>'Gender',
            'age'=>'Age',
        ];
        $this->CI->load->model('V2_yahoo_job_model');
        $existing_jobs = $this->CI->V2_yahoo_job_model->get_demographic_jobs_by_status('submitted'); var_dump('this is yahoo',$existing_jobs); //exit;
        if(count($existing_jobs) == 3) { var_dump(555);
            return true;
        }
        if($existing_jobs) {
            foreach ($existing_jobs as $existing_job) {
                unset($reporting_type[$existing_job['type']]);
            }
        }
        //var_dump($reporting_type); exit;
//        $campaigns = $this->CI->V2_master_campaign_model->get_active_campaigns_by_network_id($this->network_id); //var_dump($campaigns); exit;
//
//        $campaign_ids = [];
//        foreach ($campaigns as $campaign){
//            $campaign_ids[] = $campaign['network_campaign_id'];
//        }
        $campaign_ids[]=354285239;
//        $campaign_ids[]=353082826;
        var_dump($campaign_ids);
        $this->CI->load->model('V2_yahoo_job_model');
        if($campaign_ids) {
            foreach ($reporting_type as $key=>$type) {

                $fields_array = [
                    ['field' => "Campaign ID"],
                    ['field' => "Impressions"],
                    ['field' => "Clicks"],
                    ['field' => "Day"],
                    ['field' => $type],
                ];

                $filters_array = [
                    ['field' => "Advertiser ID", 'operator' => "=", "value" => $this->advertiser_id],
                    ['field' => "Campaign ID", 'operator' => "IN", "values" => $campaign_ids],
                    //['field' => "Day", 'operator' => "between", "from" => date('Y-m-d', strtotime($campaigns[0]['campaign_start_datetime'])), 'to' => date('Y-m-d')],
                    ['field' => "Day", 'operator' => "between", "from" =>'2016-11-01', 'to' => '2016-12-01'],
                ];
                $report_array['cube'] = "user_stats";
                $report_array['fields'] = $fields_array;
                $report_array['filters'] = $filters_array;
                var_dump($report_array);
                $report_json = json_encode($report_array);
                var_dump($report_json);
                $response = $this->yahoo->get_reporting($report_json);
                var_dump($response);
                if ($response['result']) {
                    $insert_array['type'] = $key;
                    $insert_array['job_id'] = $response['result']['jobId'];
                    $insert_array['status'] = $response['result']['status'];
                    $insert_array['created_date'] = date('Y-m-d h:i:s');
                    $this->CI->V2_yahoo_job_model->create($insert_array);
                }
            }
        }
        return true;




        $this->CI->load->model('V2_yahoo_job_model');
//        $existing_jobs = $this->CI->V2_yahoo_job_model->get_jobs_count_by_status_and_type('submitted','performance_stats'); //var_dump($existing_jobs); exit;
//        // get all ads by campaign_id
//        if($existing_jobs) {
//            return true;
//        }
//
//		$ads = $this->CI->V2_ad_model->get_active_campaigns_ads_by_network_id($this->network_id);
//        echo '<pre>';
//        var_dump($ads);
//        foreach ($ads as $ad){
//            if(ad['approval_status'] != 'DISAPPROVED') {
//                $ad_ids[] = $ad['network_creative_id'];
//            }
//        }
//        $ad_ids[] = 32455128691;
        //$ad_ids[] = 32459816347;
        $ad_ids[] = 32546119763;
        $fields_array = [
            ['field' => "Ad ID"],
            ['field' => "Day"],
            ['field' => "Impressions"],
            ['field' => "Clicks"],
            ['field' => "CTR"],
            ['field' => "Spend"],
            ['field' => "Video 25% Complete"],
            ['field' => "Video 50% Complete"],
            ['field' => "Video 75% Complete"],
            ['field' => "Video 100% Complete"],
            ['field' => "Video Views"],
            ['field' => "Video Starts"],
            ['field' => "Video Closed"],
            ['field' => "Video Skipped"],
            ['field' => "Video after 30 seconds view"],
        ];

        $filters_array = [
            ['field' => "Advertiser ID", 'operator' => "=", "value" => $this->advertiser_id ],
            ['field' => "Ad ID", 'operator' => "IN", "values" => $ad_ids ],
            //['field' => "Ad Group ID", 'operator' => "=", "value" => '9102021378' ],
          //['field' => "Campaign ID", 'operator' => "=", "value" => '353786145' ],
            ['field' => "Day", 'operator' => "between", "from" => '2016-11-01', 'to' => '2016-11-13'],
        ];
        $report_array['cube'] =  "performance_stats";
        $report_array['fields'] =  $fields_array;
        $report_array['filters'] =  $filters_array;

        //var_dump($report_array);
        $report_json = json_encode($report_array);
//        var_dump($report_json, $this->yahoo);
        $response = $this->yahoo->get_reporting($report_json);
        var_dump($response);
        // check if campaign created successfully
        if($response['message']) {
            return false;
        }
//
        if($response['result']) {
            $insert_array['type'] = $report_array['cube'];
            $insert_array['job_id'] = $response['result']['jobId'];
            $insert_array['status'] = $response['result']['status'];
            $insert_array['created_date'] = date('Y-m-d h:i:s');
            $this->CI->V2_yahoo_job_model->create($insert_array);
        }

        return true;
    }

    public function get_job() { var_dump(date('Y-m-d'));

        $this->CI->load->model('V2_yahoo_job_model');
        $jobs = $this->CI->V2_yahoo_job_model->get_by_status('submitted');
        $reporting_type = [];
        foreach ($jobs as $job) {

            $url = 'https://api.gemini.yahoo.com/v2/rest/reports/custom/' . $job['job_id'] . '?advertiserId=' . $this->advertiser_id;
            //'https://api.gemini.yahoo.com/v2/rest/reports/custom/3cf63929ba6bd45202abb5aee311e17730999b181084274912?advertiserId=1493170'
            $response = $this->yahoo->get_job($url);
            //$response = $this->yahoo->get_job('https://api.gemini.yahoo.com/v2/rest/reports/custom/035f9f29519bceaed8a8b5c88db939a0551679831084277769?advertiserId=1493170');
            echo '<pre>';
            var_dump('yahoo', $response);
            // check if campaign created successfully
            if ($response['message']) {
                return false;
            }
            if ($response['result']['status'] == 'completed') {
                $json = file_get_contents($response['result']['jobResponse']);
                $result_array = json_decode($json, true);

                var_dump('222222', $result_array['rows']); //exit;
                if(!$result_array['rows']){
                    $this->CI->V2_yahoo_job_model->update($job['id'], ['status'=>'completed']);
                    continue;
                }
                if($job['type']=='performance_stats') {
                    $ads = $this->CI->V2_ad_model->get_active_campaigns_ads_by_network_id($this->network_id);

                    foreach ($ads as $ad) {
                        if (ad['approval_status'] != 'DISAPPROVED') {
                            $ads_sorted[$ad['network_creative_id']] = $ad;
                        }
                    }

                    foreach ($result_array['rows'] as $row) {
                        if(!empty($performance_stats[$row[0]]['clicks'])) {
                            $performance_stats[$row[0]]['clicks'] = $performance_stats[$row[0]]['clicks']+$row['3'];
                        } else {
                            $performance_stats[$row[0]]['clicks'] = $row['3'];
                        }
                        if(!empty($performance_stats[$row[0]]['impressions'])) {
                            $performance_stats[$row[0]]['impressions'] = $performance_stats[$row[0]]['impressions']+$row['2'];
                        } else {
                            $performance_stats[$row[0]]['impressions'] = $row['2'];
                        }
                    }
                    var_dump($performance_stats);
                    //adds the ad performance report into the database
                    $this->CI->load->model('V2_campclick_impression_model');
                    foreach ($performance_stats as $key=>$report) {

                        if (isset($ads_sorted[$key])) {
                            var_dump(142);
                            $current_ad = $ads_sorted[$key];
                            $data_for_update = array();
                            $data_for_update['ad_id'] = $current_ad['id'];
                            $data_for_update['network_id'] = $this->network_id;
                            $data_for_update['campaign_id'] = $current_ad['campaign_id'];
                            $data_for_update['impressions_count'] = (int)$report['impressions'] - (int)$current_ad['impressions_count'];
                            $data_for_update['timestamp'] = date('Y-m-d H:i:s');
                            if ($data_for_update['impressions_count'] && (int)$report['impressions']) {
                                $this->CI->V2_campclick_impression_model->create($data_for_update);
                            }
                            $this->CI->load->model('V2_campclick_click_model');
                            $current_clicks_count = $this->CI->V2_campclick_click_model->get_ad_click_count($current_ad['id']); var_dump($current_clicks_count);
                            $click_count = (int)$report['clicks'] - (int)$current_clicks_count;
                            var_dump($click_count);
                            $link_id = 1;
                            $ad_id = $current_ad['id'];
                            $campaign_id = $current_ad['campaign_id'];;
                            $ip_address = '37.157.218.36';
                            $browser = array(
                                'Mozilla',
                                'Chrome',
                                'Safari',
                            );
                            $mobile = array(
                                'Y',
                                'N',
                            );
                            $platform = array(
                                'Windows 7',
                                'Mac OS X',
                                'Unknown Platform',
                                'Linux',
                            );
                            $device = array(
                                'Samsung',
                                'Apple iPhone',
                                'Generic Mobile',
                                'LG',
                                'HTC',
                                'iPad',
                            );
                            $date = date('Y-m-d h:i:s');

                            for($i=0; $i<$click_count; $i++) {
                                $insert_array = array(
                                    "link_id" => $link_id,
                                    'campaign_id' => $campaign_id,
                                    'ad_id' => $ad_id,
                                    "ip_address" => $ip_address,
                                    "user_agent" => $browser[array_rand($browser)],
                                    "timestamp" => $date,
                                    "is_mobile" => $mobile[array_rand($mobile)],
                                    "web_browser" => $browser[array_rand($browser)],
                                    "mobile_device" => $device[array_rand($device)],
                                    "platform" => $platform[array_rand($platform)],
                                    "referrer" => 'aaa',
                                    "referrer_host" => 'aaa',
                                    'is_fraud' => "Y",
                                    'network_id' => 6,
                                );
                                $this->CI->V2_campclick_click_model->create($insert_array);
                            }
                        }
                    }
                    $this->CI->V2_yahoo_job_model->update($job['id'], ['status'=>'completed']);
                }
                else if($job['type']=='video_stats') {
                    $campaigns = $this->CI->V2_master_campaign_model->get_active_campaigns_video_count($this->network_id);
                    //var_dump($campaigns);
                    foreach ($campaigns as $campaign){
                        $campaign_sorted[$campaign['network_campaign_id']] = $campaign;
                        //$campaign_sorted[32499043442] = $campaign;
                    }

                    foreach ($result_array['rows'] as $row) {
                        if(!empty($video_stats[$row[0]]['25_p_count'])) {
                            $video_stats[$row[0]]['25_p_count'] = $video_stats[$row[0]]['25_p_count']+$row['6'];
                        } else {
                            $video_stats[$row[0]]['25_p_count'] = $row['6'];
                        }

                        if(!empty($video_stats[$row[0]]['50_p_count'])) {
                            $video_stats[$row[0]]['50_p_count'] = $video_stats[$row[0]]['50_p_count']+$row['7'];
                        } else {
                            $video_stats[$row[0]]['50_p_count'] = $row['7'];
                        }

                        if(!empty($video_stats[$row[0]]['75_p_count'])) {
                            $video_stats[$row[0]]['75_p_count'] = $video_stats[$row[0]]['75_p_count']+$row['8'];
                        } else {
                            $video_stats[$row[0]]['75_p_count'] = $row['8'];
                        }

                        if(!empty($video_stats[$row[0]]['100_p_count'])) {
                            $video_stats[$row[0]]['100_p_count'] = $video_stats[$row[0]]['100_p_count']+$row['9'];
                        } else {
                            $video_stats[$row[0]]['100_p_count'] = $row['9'];
                        }


                        if(!empty($video_stats[$row[0]]['starts'])) {
                            $video_stats[$row[0]]['starts'] = $video_stats[$row[0]]['starts']+$row['11'];
                        } else {
                            $video_stats[$row[0]]['starts'] = $row['11'];
                        }
                    }
                    var_dump($video_stats);
                    $this->CI->load->model('V2_video_watch_model');
                    foreach ($video_stats as $key=>$report) {

                        if (isset($campaign_sorted[$key])) {
                            var_dump(142);
                            $current_campaign = $campaign_sorted[$key];

                                $data_for_update_video_watch = array();
                                $data_for_update_video_watch['network_id'] = $this->network_id;
                                $data_for_update_video_watch['campaign_id'] = $current_campaign['id'];
                                $data_for_update_video_watch['network_campaign_id'] = $current_campaign['network_campaign_id'];
                                $data_for_update_video_watch['type'] = 'watch';
                                $data_for_update_video_watch['created_date'] = date("Y-m-d H:i:s");

                                if ((int)$report) {
                                    $count_10_sec = (int)$report['starts'] - (int)$campaign['10_sec_count'];
                                    if ($count_10_sec >= 0) {
                                        $data_for_update_video_watch['10_sec'] = $count_10_sec;
                                    }

                                }
                                if ((int)$report['25_p_count']) {
                                    $count_25_p = (int)$report['25_p_count'] - (int)$campaign['25_p_count'];
                                    if ($count_25_p >= 0) {
                                        $data_for_update_video_watch['25_p'] = $count_25_p;
                                    }
                                }
                                if ((int)$report['50_p_count']) {
                                    $count_50_p = (int)$report['50_p_count'] - (int)$campaign['50_p_count'];
                                    if ($count_50_p >= 0) {
                                        $data_for_update_video_watch['50_p'] = $count_50_p;
                                    }
                                }
                                if ((int)$report['75_p_count']) {
                                    $count_75_p = (int)$report['75_p_count'] - (int)$campaign['75_p_count'];
                                    if ($count_75_p >= 0) {
                                        $data_for_update_video_watch['75_p'] = $count_75_p;
                                    }
                                }
                                if ((int)$report['100_p_count']) {
                                    $count_95_p = (int)$report['100_p_count'] - (int)$campaign['95_p_count'];
                                    if ($count_95_p >= 0) {
                                        $data_for_update_video_watch['95_p'] = $count_95_p;
                                    }
                                }
                                var_dump('video',$data_for_update_video_watch);
                                if (count($data_for_update_video_watch) > 5) {
                                    $this->CI->V2_video_watch_model->create($data_for_update_video_watch);
                                }

                        }
                    }
                    $this->CI->V2_yahoo_job_model->update($job['id'], ['status'=>'completed']);
                }
                else if($job['type']=='adjustment_stats') {

                    foreach ($result_array['rows'] as $row) {
                        if(!empty($adjustment_stats[$row[0]]['cost'])) {
                            $adjustment_stats[$row[0]]['cost'] = $adjustment_stats[$row[0]]['cost']+$row['5'];
                        } else {
                            $adjustment_stats[$row[0]]['cost'] = $row['5'];
                        }
                    }
                    var_dump('ttt',$adjustment_stats);
                    $campaigns = $this->CI->V2_master_campaign_model->get_active_campaigns_by_network_id($this->network_id);

                    foreach ($campaigns as $campaign){
                        $campaign_sorted[$campaign['network_campaign_id']] = $campaign;
                    }
                    //adds the ad performance report into the database
                    $this->CI->load->model('V2_campaign_cost_model');
                    foreach ($adjustment_stats as $key=>$report){
                        var_dump($report);

                        if(isset($campaign_sorted[$key])){
                            $current_campaign = $campaign_sorted[$key];
                            $data_for_update = array();
                            $data_for_update['network_id'] = $current_campaign['network_id'];
                            $data_for_update['campaign_id'] = $current_campaign['id'];
                            $data_for_update['cost'] = $report['cost'];
                            $data_for_update['date_updated'] = date('Y-m-d h:i:s');
                            if($data_for_update['cost']) {
                                $this->CI->V2_campaign_cost_model->create($data_for_update);
                            }
                        }
                    }
                    $this->CI->V2_yahoo_job_model->update($job['id'], ['status'=>'completed']);
                }
                else {
                    foreach ($result_array['rows'] as $row) {
                        if(!empty($reporting_type[$job['type']][$row[0]][$row['4']]['clicks'])) {
                            $reporting_type[$job['type']][$row[0]][$row['4']]['clicks'] = $reporting_type[$job['type']][$row[0]][$row['4']]['clicks']+$row['2'];
                        } else {
                            $reporting_type[$job['type']][$row[0]][$row['4']]['clicks'] = $row['2'];
                        }
                        if(!empty($reporting_type[$job['type']][$row[0]][$row['4']]['impressions'])) {
                            $reporting_type[$job['type']][$row[0]][$row['4']]['impressions'] = $reporting_type[$job['type']][$row[0]][$row['4']]['impressions']+$row['1'];
                        } else {
                            $reporting_type[$job['type']][$row[0]][$row['4']]['impressions'] = $row['1'];
                        }
                    }
                }
            }
        }

        var_dump(99999999,$reporting_type);
        if(count($reporting_type)==3) { var_dump(911);
            $campaigns = $this->CI->V2_master_campaign_model->get_active_campaigns_demographics_count($this->network_id);
            //adds the campaign demographics report into the database
            $this->CI->load->model('V2_demographics_reporting_model');
            var_dump($campaigns);
            foreach($campaigns as $campaign){
                //if( $age_reports[$campaign['network_campaign_id']] && $reporting_type['gender'][$campaign['network_campaign_id']] ) {

                $age_report = $reporting_type['age'][$campaign['network_campaign_id']];
                $gender_report = $reporting_type['gender'][$campaign['network_campaign_id']];
                $device_report = $reporting_type['device_type'][$campaign['network_campaign_id']];

                $data_for_update = array();
                $data_for_update['network_id'] = $campaign['network_id'];
                $data_for_update['campaign_id'] = $campaign['id'];
                $data_for_update['type'] = 'CLICK';
                $data_for_update['network_campaign_id'] = $campaign['network_campaign_id'];
                $data_for_update['created_date'] = date("Y-m-d H:i:s");

                $count_18_24 = (int)$age_report['18-24']['clicks'] - (int)$campaign['18_24_count'];
                if($count_18_24 >0 ) {
                    $data_for_update['18_24'] = $count_18_24;
                }

                $count_25_34 = (int)$age_report['25-34']['clicks'] - (int)$campaign['25_34_count'];
                if($count_25_34 >0 ) {
                    $data_for_update['25_34'] = $count_25_34;
                }

                $count_35_44 = (int)$age_report['35-44']['clicks'] - (int)$campaign['35_44_count'];
                if($count_35_44 >0 ) {
                    $data_for_update['35_44'] = $count_35_44;
                }

                $count_45_54 = (int)$age_report['45-54']['clicks'] - (int)$campaign['45_54_count'];
                if($count_45_54 >0 ) {
                    $data_for_update['45_54'] = $count_45_54;
                }

                $count_55_64 = (int)$age_report['55-64']['clicks'] - (int)$campaign['55_64_count'];
                if($count_55_64 >0 ) {
                    $data_for_update['55_64'] = $count_55_64;
                }

                $count_64 = (int)$age_report['65-125']['clicks'] - (int)$campaign['64_count'];
                if($count_64 >0 ) {
                    $data_for_update['64'] = $count_64;
                }

                $unknown_age_count = (int)$age_report['OTHER']['clicks'] - (int)$campaign['unknown_age_count'];
                if($unknown_age_count >0 ) {
                    $data_for_update['unknown_age'] = $unknown_age_count;
                }

                $count_male = (int)$gender_report['M']['clicks'] - (int)$campaign['male_count'];
                if($count_male >0 ) {
                    $data_for_update['male'] = $count_male;
                }

                $count_female = (int)$gender_report['F']['clicks'] - (int)$campaign['female_count'];
                if($count_female >0 ) {
                    $data_for_update['female'] = $count_female;
                }

                $count_unknown_gender = (int)$gender_report['U']['clicks'] - (int)$campaign['unknown_gender_count'];
                if($count_unknown_gender >0 ) {
                    $data_for_update['unknown_gender'] = $count_unknown_gender;
                }

                $count_desktop = (int)$device_report['DESKTOP']['clicks'] - (int)$campaign['desktop_count'];
                if($count_desktop >0 ) {
                    $data_for_update['desktop'] = $count_desktop;
                }

                $count_smartphone = (int)$device_report['SMARTPHONE']['clicks'] - (int)$campaign['smartphone_count'];
                if($count_smartphone >0 ) {
                    $data_for_update['smartphone'] = $count_smartphone;
                }

                $count_tablet = (int)$device_report['TABLET']['clicks'] - (int)$campaign['tablet_count'];
                if($count_tablet >0 ) {
                    $data_for_update['tablet'] = $count_tablet;
                }

                $count_unknown_device = (int)$device_report['UNKNOWN']['clicks'] - (int)$campaign['unknown_device_count'];
                if($count_unknown_device >0 ) {
                    $data_for_update['unknown_device'] = $count_unknown_device;
                }
                var_dump($data_for_update);
                if(count($data_for_update)>5) {
                    $this->CI->V2_demographics_reporting_model->create($data_for_update);
                }
            }
            var_dump(32323232);
            $this->CI->V2_yahoo_job_model->complete_demographic_jobs(); var_dump(54321);
        }
        return true;
    }

    public function get_multiple_ads_impressions() {
        // get all ads by campaign_id
        $this->CI->load->model('V2_multiple_ad_model');
        $ads = $this->CI->V2_multiple_ad_model->get_active_campaigns_ads_by_network_id($this->network_id);
//        var_dump((int)$ads[0]['impressions_count']); exit;
//
        //var_dump($ads); exit;
        $response = $this->adword->getAdsImpressionsByActiveCampaigns($this->adword, null, 'XML');

        // check if campaign created successfully
        if($response['message']) {
            // save error message into db and return false
//			$error_data_for_update['disapproval_reasons'] = $response['message'];
//			$error_data_for_update['campaign_status'] = 'DISAPPROVED';
            //$this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
            return false;
        }
//        $response['result'] = array(
//            array('adID'=>86319125185,'impressions'=>150),
//            array('adID'=>86319126145,'impressions'=>250),
//        );
        if($response['result']) {
            foreach($response['result'] as $row) {
                $repo = (array)$row;
                $reports[] = reset($repo);
            }
        }
        //var_dump($reports); exit;
        //adds the ad performance report into the database
        $this->CI->load->model('V2_campclick_impression_model');
        foreach ($reports as $report){
            foreach($ads as $ad){
                if($report["adID"]==$ad['network_creative_id']){
                    $data_for_update = array();
                    $data_for_update['ad_id'] = $ad['ad_id'];
                    $data_for_update['network_id'] = $this->network_id;
                    $data_for_update['campaign_id'] = $ad['campaign_id'];
                    $data_for_update['impressions_count'] = (int)$report["impressions"] - (int)$ad['impressions_count'];
                    if($data_for_update['impressions_count']) {
                        $this->CI->V2_campclick_impression_model->create($data_for_update);
                    }
                }
            }
        }
        return true;
    }

    public function get_campaigns_cost() {
        //return true;
        // get all ads by campaign_id

        $this->CI->load->model('V2_yahoo_job_model');
        $existing_jobs = $this->CI->V2_yahoo_job_model->get_jobs_count_by_status_and_type('submitted','adjustment_stats'); //var_dump($existing_jobs); exit;
        // get all ads by campaign_id
        if($existing_jobs) {
            return true;
        }
        //var_dump($reporting_type); exit;
        $campaigns = $this->CI->V2_master_campaign_model->get_active_campaigns_by_network_id($this->network_id); //var_dump($campaigns); exit;

        //$campaign_ids = [353082826];
        foreach ($campaigns as $campaign){
            $campaign_ids[] = $campaign['network_campaign_id'];
        }

        if($campaign_ids) {

            $fields_array = [
                ['field' => "Campaign ID"],
                ['field' => "Impressions"],
                ['field' => "Clicks"],
                ['field' => "Day"],
                ['field' => 'Is Adjustment'],
                //['field' => 'Adjustment type'],
                ['field' => 'Spend'],
            ];

            $filters_array = [
                ['field' => "Advertiser ID", 'operator' => "=", "value" => $this->advertiser_id],
                ['field' => "Campaign ID", 'operator' => "IN", "values" => $campaign_ids],
                //['field' => "Day", 'operator' => "between", "from" => date('Y-m-d', strtotime($campaigns[0]['campaign_start_datetime'])), 'to' => date('Y-m-d')],
                ['field' => "Day", 'operator' => "between", "from" => '2016-10-17', 'to' => date('Y-m-d')],
            ];
            $report_array['cube'] = "adjustment_stats";
            $report_array['fields'] = $fields_array;
            $report_array['filters'] = $filters_array;
            $report_json = json_encode($report_array);

            $response = $this->yahoo->get_reporting($report_json);
            var_dump($response);
            if ($response['result']) {
                $insert_array['type'] = 'adjustment_stats';
                $insert_array['job_id'] = $response['result']['jobId'];
                $insert_array['status'] = $response['result']['status'];
                $insert_array['created_date'] = date('Y-m-d h:i:s');
                $this->CI->V2_yahoo_job_model->create($insert_array);
            }

        }
        return true;
        //var_dump($reports); exit;
        //adds the ad performance report into the database
//        $this->CI->load->model('V2_campaign_cost_model');
//        foreach ($reports as $report){
//            foreach($campaigns as $campaign){
//                if($report["campaignID"]==$campaign['network_campaign_id']){
//                    $data_for_update = array();
//                    $data_for_update['network_id'] = $campaign['network_id'];
//                    $data_for_update['campaign_id'] = $campaign['id'];
//                    $data_for_update['cost'] = $report['cost']/1000000;
//					if($data_for_update['cost']) {
//						$this->CI->V2_campaign_cost_model->create($data_for_update);
//					}
//                }
//            }
//        }
        return true;
    }

    public function get_multiple_campaigns_cost() {
        // get all ads by campaign_id
        $campaigns = $this->CI->V2_multiple_campaign_model->get_active_campaigns_by_network_id($this->network_id);
//        var_dump((int)$ads[0]['impressions_count']); exit;
//
        //var_dump($ads); exit;
        $response = $this->adword->getActiveCampaignsCosts($this->adword, null, 'XML');

        // check if campaign created successfully
        if($response['message']) {
            // save error message into db and return false
//			$error_data_for_update['disapproval_reasons'] = $response['message'];
//			$error_data_for_update['campaign_status'] = 'DISAPPROVED';
            //$this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
            return false;
        }
//        $response['result'] = array(
//            array('adID'=>86319125185,'impressions'=>150),
//            array('adID'=>86319126145,'impressions'=>250),
//        );
        if($response['result']) {
            foreach($response['result'] as $row) {
                $repo = (array)$row;
                $reports[] = reset($repo);
            }
        }
        //var_dump($reports); exit;
        //adds the ad performance report into the database
        $this->CI->load->model('V2_campaign_cost_model');
        foreach ($reports as $report){
            foreach($campaigns as $campaign){
                if($report["campaignID"]==$campaign['network_campaign_id']){
                    $data_for_update = array();
                    $data_for_update['network_id'] = $campaign['network_id'];
                    $data_for_update['campaign_id'] = $campaign['campaign_id'];
                    $data_for_update['cost'] = $report['cost']/1000000;
                    if($data_for_update['cost']) {
                        $this->CI->V2_campaign_cost_model->create($data_for_update);
                    }
                }
            }
        }
        return true;
    }

    public function get_campaigns_placements_report() {
        return true;
        // get all ads by campaign_id
        $campaigns = $this->CI->V2_master_campaign_model->get_active_campaigns_by_network_id($this->network_id);

        $response = $this->adword->getActiveCampaignsPlacementReport($this->adword, null, 'XML');

        // check if campaign created successfully
        if($response['message']) {
            // save error message into db and return false
            return false;
        }

        if($response['result']) {
            foreach($response['result'] as $row){
                $row_repo = (array)$row;
                $place = reset($row_repo);

                if($place['clicks']>0) {

                    $places[$place['campaignID']][] = $place;
                    $sorting_array[$place['campaignID']][] = $place['clicks'];
                }

            }

        }

        //var_dump($reports); exit;
        //adds the ad performance report into the database
        $this->CI->load->model('V2_placements_reporting_model');

        foreach($campaigns as $campaign){

            if($sorting_array[$campaign['network_campaign_id']]) {

                arsort($sorting_array[$campaign['network_campaign_id']]);
                $i = 1;
                foreach($sorting_array[$campaign['network_campaign_id']] as $key=>$value) {
                    if($i<=5){
                        $five_place = $places[$campaign['network_campaign_id']][$key];

                        $data_for_insert = array();

                        $data_for_insert['network_id'] = $campaign['network_id'];
                        $data_for_insert['campaign_id'] = $campaign['id'];
                        $data_for_insert['network_campaign_id'] = $campaign['network_campaign_id'];
                        $data_for_insert['cost'] = $five_place['cost']/1000000;
                        $data_for_insert['clicks'] = $five_place['clicks'];
                        $data_for_insert['impressions'] = $five_place['impressions'];
                        $data_for_insert['placement'] = $five_place['criteriaDisplayName'];
                        $data_for_insert['created_date'] = date("Y-m-d H:i:s");
                        $data_for_insert['type'] = 'CLICK';

                        $this->CI->V2_placements_reporting_model->create($data_for_insert);

                    } else {
                        break;
                    }
                    $i++;
                }
            }
        }

        return true;
    }

    public function update_bid($campaign) {
        // need to get group id
        $this->CI->load->model('V2_group_model');
        $group = $this->CI->V2_group_model->get_group_by_campaign_id($campaign['id']);
        if($campaign['max_clicks']) {
            $isCpc = true;
        }
        else {
            $isCpc = false;
        }

        $bids = [];
        $bids[0]["priceType"] = "CPC";
        $bids[0]["value"] = $campaign['bid'];
        $bids[0]["channel"] = "SEARCH";

        $bids[1]["priceType"] = "CPC";
        $bids[1]["value"] = $campaign['bid'];
        $bids[1]["channel"] = "NATIVE";


        $group_array["bidSet"]['bids'] = $bids;
        $group_array["id"] = $group['network_group_id'];

        $group_json = json_encode($group_array);

        $response = $this->yahoo->update_group($group_json);
        var_dump($campaign['bid'],$response);
        // check if campaign created successfully
        if($response['message']) {
            // save error message into db and return false
//			$error_data_for_update['disapproval_reasons'] = $response['message'];
//			$error_data_for_update['campaign_status'] = 'DISAPPROVED';
            //$this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
            return false;
        }
        return true;
    }

    public function create_audience($campaign) {

        $this->CI->load->model("Userlist_io_model");
        $tag_array["name"] = $campaign['io'];
        $tag_array["advertiserId"] = $this->advertiser_id;
        $tag_json = json_encode($tag_array);
        $tag_response = $this->yahoo->create_tag($tag_json);

        $audience_array["name"] = $campaign['io'];
        $audience_array["tagId"] = $tag_response['result']['id'];
        $audience_array["retentionDays"] = 360;
        $audience_array["advertiserId"] = $this->advertiser_id;
        $audience_array["type"] = "WEBSITE";
        $audience_array["description"] = "click from website";
        $audience_array["rule"] = ["url"=>["i_contains"=>""]];

        $audience_json = json_encode($audience_array);
        $response = $this->yahoo->create_audience($audience_json);
        //var_dump($audience); exit;
        $tag_details = $this->yahoo->get_tag($tag_response['result']['id']);
        //$snip_code = htmlspecialchars('<script type="application/javascript">(function(w,d,t,r,u){w[u]=w[u]||[];w[u].push({"projectId":"10000","properties":{"pixelId":"10014388"}});var s=d.createElement(t);s.src=r;s.async=true;s.onload=s.onreadystatechange=function(){var y,rs=this.readyState,c=w[u];if(rs&&rs!="complete"&&rs!="loaded"){return}try{y=YAHOO.ywa.I13N.fireBeacon;w[u]=[];w[u].push=function(p){y([p])};y(c)}catch(e){}};var scr=d.getElementsByTagName(t)[0],par=scr.parentNode;par.insertBefore(s,scr)})(window,document,"script","https://s.yimg.com/wi/ytc.js","dotq");</script>');
        $snip_code = htmlspecialchars($tag_details['result']['codes'][1]["instrumentationCode"]);
        $this->CI->Userlist_io_model->create_userlist_io($campaign['io'], $campaign['id'], $response['result'], $snip_code, $this->network_id, $campaign['userid']);
        if($response['message']) {
            return false;
        }
        return array('snip_code'=>$snip_code,'id'=>$response['result'], 'io'=>$campaign['io'], 'network'=>'YAHOO');

    }

}