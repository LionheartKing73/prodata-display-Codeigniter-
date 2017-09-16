<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Fiq_model
 *
 */


class Airpush_model extends CI_Model {

    protected $CI;
    protected $network_id = 4;
    protected $live_campaign_id;
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
    public function __construct()	{

        parent::__construct();

        $this->CI =& get_instance();
        $this->CI->load->database();

        $this->CI->load->library("Airpush");
        $this->CI->load->model('V2_master_campaign_model');
        $this->CI->load->model('V2_ad_model');
    }

    public function rest(){

        $this->airpush->rest();
    }

    public function create($campaign=null) {


//        $ad_array['creativeId'] = '4015229';
//
//        $ad_json = json_encode($ad_array);
//        echo '<pre>';
//        var_dump($ad_json); //exit;
//
//        $ad_json = urlencode($ad_json);
//
//        $response = $this->airpush->get_ad_status($ad_json);

//        $str = 'Creative Id 4015293 has been created successfully for the campaign Id 1986857';
//        preg_match('!\d+!', $str, $matches);
//        print_r($matches);
//
//        $str = 'Creative Id 4015293 has been created successfully for the campaign Id 1986857';
//        $int = filter_var($str, FILTER_SANITIZE_NUMBER_INT);
//        var_dump($int); exit;


        //$campaign = $this->CI->V2_master_campaign_model->get_by_id(null, 158);
//        var_dump($campaign); exit;
        $result_campaign = $this->create_campaign($campaign);
        if($result_campaign['message']) {

            // sent mail to owner
            //  var_dump('camp');
            return array('status' => 'rejected', 'message' => $result_campaign['message']);
        }

        if(!$this->create_ads($this->live_campaign_id, $campaign)){
            // sent mail to owner
            var_dump('ads');
            return 'rejected';
        }

        if(false) {
            var_dump($campaign['campaign_start_datetime'], date("Y-m-d H:i:s"));
            $status = 'SCHEDULED';
            $data_for_update['campaign_status'] = 'SCHEDULED';

            $data_for_update['network_campaign_status'] = 'PAUSED';
        } else {
            $status = 'active';
            $data_for_update['campaign_status'] = 'ACTIVE';
            $data_for_update['network_campaign_status'] = 'ACTIVE';
        }
        // make campaign status enabled and set converted to live to Y
        //$this->update_campaign_status($this->live_campaign_id, $status);

        $data_for_update['campaign_is_converted_to_live'] = "Y";
        $data_for_update['network_campaign_id'] = $this->live_campaign_id;


        $this->CI->V2_master_campaign_model->update($campaign['id'], $data_for_update);

        return array('status' => 'approved', 'message' => '');
    }

    public function create_campaign($campaign=null){

        $campaign_array["campaign_info"]["name"]= $campaign['io'].' '.$campaign['name'].' '.$campaign['id'];
        $campaign_array["campaign_info"]["dailyBudget"] = $campaign['budget'];
        $campaign_array["campaign_info"]["type"] = strtolower($campaign['campaign_type']);
        //$campaign_array["campaign_info"]["startDate"] = "2016-03-18 10:25";
        $campaign_array["campaign_info"]["startDate"] = date('Y-m-d H:i', strtotime($campaign['campaign_start_datetime']));
//        $campaign_array["campaign_info"]["endDate"]=> string(1) " "
        $campaign_array["campaign_info"]["category"] = "Automotive";
//        $campaign_array["campaign_info"]["category"] = $campaign['vertical'];
        $campaign_array["campaign_info"]["platformType"] = "Android";
        $campaign_array["campaign_info"]["status"] = "active";
        $campaign_array["targetting_details"]["osVersion"] = "1.6";
        $campaign_array["targetting_details"]["bid"] = $campaign['bid'];
        $campaign_array["targetting_details"]["dayOfWeek"] = "Everyday";
//        $campaign_array["targetting_details"]["scheduleTime"]=> array(3) { ["Mon"]=> string(7) "3-4,5-9" ["Wed"]=> string(14) "0-3,4-10,19-24" }


        $this->CI->load->model("V2_network_country_criterion_model");

        $country_array = $this->CI->V2_network_country_criterion_model->get_criteria_id_list($campaign['country'], $this->network_id);

        $country_id = $country_array[0];

        if($campaign['state']) {
            $this->CI->load->model("V2_network_state_criterion_model");
            $list_array = explode(",", $campaign['state']);
            $states_array = $this->CI->V2_network_state_criterion_model->get_criteria_id_list($list_array, $this->network_id);
            $states_list = implode("," , $states_array);
            $states = array($country_id =>$states_list);
        } else {
            $states = array($country_id =>'ALL');
        }

        $campaign_array["targetting_details"]["countryId"] = $country_id;
        $campaign_array["targetting_details"]["stateId"] = $states;

        $ads = $this->CI->V2_ad_model->get_ads_by_campaign_id($campaign['id']);

        $campaign_array["targetting_details"]["domain_name"] = $ads[0]['display_url'];
        //$campaign_array["targetting_details"]["domain_name"] = 'vk.com';
        $campaign_array["targetting_details"]["manufacturerId"] = "99999";

        $campaign_array["targetting_details"]["deviceId"] = array('99999'=>'999999');
        $campaign_array["targetting_details"]["carrierId"] = array($country_id=>'ALL');
        $campaign_json = json_encode($campaign_array);
        echo '<pre>';
        var_dump($campaign_json); //exit;

        $campaign_json = urlencode($campaign_json);

        $response = $this->CI->airpush->create_campaign($campaign_json);

        // check if campaign created successfully
        if($response['message']) {
            // save error message into db and return false
            $error_data_for_update['disapproval_reasons'] = $response['message'];
            $error_data_for_update['campaign_status'] = 'DISAPPROVED';
            // if this is multiple campaign then save errors in multiple table
            $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
            return $response;
        }

        $this->live_campaign_id = $response['result'];

        return true;
    }

    public function create_ads($live_campaign_id, $campaign) {

        $campaignType = strtolower($campaign['campaign_type']);

        // select from db all ads by campaign_id
        $ads = $this->CI->V2_ad_model->get_ads_by_campaign_id($campaign['id']);
        //add ads into airpush
        foreach($ads as $key=>$ad) {
            $ad_array['campaignId'] = $live_campaign_id;
            $ad_array['creativeName'] =  $campaign['name'].' ad '.$key;
//            $ad_array['creativeName'] =  $campaign['name'].' ad '.$key;
            //$ad_array['title'] = $ad['title'];
//            $ad_array['creativeType'] = $ad['creative_type'];
            if($campaignType == 'in_app') {
                $creativeType = 'in_app_banner';
            }
            elseif($campaignType == 'rich_media_interstitial') {
                $creativeType = 'media_full_page';
                $ad_array['tagType'] = 3;
                $ad_array['textContent'] = $ad['script'];
            } else {
                $creativeType = $campaignType;
            }
            $ad_array['creativeType'] = $creativeType;
            //$ad_array['destination'] = 5;
            if($campaignType != 'rich_media_interstitial') {
                $ad_array['title'] = $ad['title'];
                $ad_array['text'] = $ad['description_1'];
                $ad_array['url'] = $ad['destination_url'];
                //$ad_array['bottomText'] = $ad['description_1'];
                if($campaignType == 'in_app' || $campaignType == 'appwall' || $campaignType == 'overlay_ad') {

                    $ad_array['image'] = $ad['creative_url'];
                } else if($campaignType == 'dialog_click_to_call') {

                    $ad_array['buttonText'] = 'Call Now';
                    $ad_array['destination'] = $ad['destination'];
                } else {

                    $ad_array['image'] = $ad['airpush_image_type'];
                    $ad_array['destination'] = $ad['destination'];
                }
                $ad_array['tagType'] = 0;
            }

            //$ad_array['iconLabel'] = 'pic';
            //$ad_array['textColor'] = '#FF0000';
            //$ad_array['textBgColor'] = '#808000';

            //$ad_array['textContent'] = 'click for you';

            $ad_array['category'] = $campaign['device_type'];

            $ad_json = json_encode($ad_array);
            echo '<pre>';
            var_dump($ad_json); //exit;

            $ad_json = urlencode($ad_json);
            //$result = $this->create_campaign($campaign);
            $response = $this->CI->airpush->create_ad($ad_json);
            //var_dump($result); exit;

            if($response['message']) {

                $error_data_for_update["approval_status"]='DISAPPROVED';
                $error_data_for_update["disapproval_reasons"]=$response['message'];

                $this->CI->V2_ad_model->update($ad['id'], $error_data_for_update);
                return $response;
            }
            else {

                $data_for_update = array();
                //$data_for_update["creative_status"] = $response['result']->status;
//                $data_for_update["approval_status"] = $response['result']->approvalStatus;
//                $data_for_update["disapproval_reasons"] = $response['result']->disapprovalReasons;
                $data_for_update["network_creative_id"] = $response['result'];
                $data_for_update["network_campaign_id"] = $this->live_campaign_id;
                //$data_for_update["network_group_id"] = $this->live_group->id;
                $data_for_update["creative_is_active"] = 'Y';

                if($campaign['is_multiple']=='Y') {
                    //$this->CI->load->model("V2_multiple_ad_model");
                    //$this->CI->V2_multiple_ad_model->update_by_ad_id($ad['id'], $data_for_update);
                }

                $this->CI->V2_ad_model->update($ad['id'], $data_for_update);

            }

        }

        return true;
    }

    public function create_ad($ad_id, $for_edit = false) {

        // select from db all ads by campaign_id
        $ad = $this->CI->V2_ad_model->get_by_id($ad_id);
        if(!$ad){
            $response['message'] = 'No find ad by id '.$ad_id;
            return $response;
        }
        $campaign = $this->CI->V2_master_campaign_model->get_by_id(null, $ad['campaign_id']);
        //add ad into airpush

        $campaignType = strtolower($campaign['campaign_type']);

        //add ads into airpush

        $ad_array['campaignId'] = $ad['network_campaign_id'];
        $ad_array['creativeName'] =  $ad['creative_name'];

        //$ad_array['title'] = $ad['title'];
        $ad_array['url'] = $ad['destination_url'];
//            $ad_array['creativeType'] = $ad['creative_type'];
        if($campaignType == 'in_app') {
            $creativeType = 'in_app_banner';
        }
        else {
            $creativeType = $campaignType;
        }
        $ad_array['creativeType'] = $creativeType;
        //$ad_array['destination'] = 5;
        $ad_array['title'] = $ad['title'];
        $ad_array['text'] = $ad['description_1'];
        //$ad_array['bottomText'] = $ad['description_1'];
        if($campaignType == 'in_app' || $campaignType == 'appwall' || $campaignType == 'overlay_ad') {

            $ad_array['image'] = $ad['creative_url'];
        }
        else {
            $ad_array['image'] = $ad['airpush_image_type'];
            $ad_array['destination'] = $ad['destination'];
        }
        //$ad_array['iconLabel'] = 'pic';
        //$ad_array['textColor'] = '#FF0000';
        //$ad_array['textBgColor'] = '#808000';

        //$ad_array['textContent'] = 'click for you';
        $ad_array['tagType'] = 0;
        $ad_array['category'] = $campaign['device_type'];

        $ad_json = json_encode($ad_array);
        echo '<pre>';
        var_dump($ad_json); //exit;

        $ad_json = urlencode($ad_json);
        //$result = $this->create_campaign($campaign);
        $response = $this->CI->airpush->create_ad($ad_json);
        var_dump($response['result']);

        if($response['message']) {
            // when we edit ad we not need to save error message in db ????? ha vor
            $error_data_for_update["approval_status"]='DISAPPROVED';
            $error_data_for_update["disapproval_reasons"]=$response['message'];

            $this->CI->V2_ad_model->update($ad['id'], $error_data_for_update);

        } else {

            $data_for_update = array();
            //$data_for_update["creative_status"] = $response['result']->status;
//                $data_for_update["approval_status"] = $response['result']->approvalStatus;
//                $data_for_update["disapproval_reasons"] = $response['result']->disapprovalReasons;
            $data_for_update["network_creative_id"] = $response['result'];
            $data_for_update["network_campaign_id"] = $ad['network_campaign_id'];
            //$data_for_update["network_group_id"] = $this->live_group->id;
            $data_for_update["creative_is_active"] = 'Y';

            if($for_edit && $ad['creative_status']=="ACTIVE") {
                $this->update_ad_status($ad, 'pause');
            }
            var_dump($data_for_update);
            $this->CI->V2_ad_model->update($ad['id'], $data_for_update);
        }


        return $response;
    }

    public function update_ad($ad_id) {
        return $this->create_ad($ad_id, true);

    }

    public function update_campaign_status($campaign, $status=null) {

        if(!$status) {
            //add cahnges into airpush
            if($campaign['network_campaign_status'] == "ACTIVE" || $campaign['network_campaign_status'] == "ENABLED" ) {
                $status = "active";
            } else {
                $status = "pause";
            }
            $campaign_array['campaignId'] = $campaign['network_campaign_id'];
        } else {
            $campaign_array['campaignId'] = $campaign;
        }

        $campaign_array['status'] = $status;
        $campaign_json = json_encode($campaign_array);
        echo '<pre>';
        var_dump($campaign_json); //exit;

        $campaign_json = urlencode($campaign_json);

        return $this->airpush->update_campaign_status($campaign_json);
    }

    public function update_campaign($campaign, $status=null) {


        $campaign_array['campaignId'] = $campaign['network_campaign_id'];
        //initializing edited data
//        $campaign_array['bid'] = $bid;

        $campaign_json = json_encode($campaign_array);
        echo '<pre>';
        var_dump($campaign_json); //exit;

        $campaign_json = urlencode($campaign_json);

        return $this->airpush->update_campaign_status($campaign_json);
    }

    public function update_ad_status($ad, $status=null) {

        if(!$status) {
            //add cahnges into airpush
            if($ad['creative_status'] == "ACTIVE" || $ad['creative_status'] == "ENABLED" ) {
                $status = "active";
            } else {
                $status = "pause";
            }
        }

        //add cahnges into adwords


        $ad_array['campaignId'] = $ad['network_campaign_id'];
        $ad_array['creativeId'] = $ad['network_creative_id'];
        $ad_array['status'] = $status;
        $ad_json = json_encode($ad_array);
        echo '<pre>';
        var_dump($ad_json); //exit;

        $ad_json = urlencode($ad_json);

        return $this->airpush->update_ad_status($ad_json);
    }

    public function check_ads_approved_status() {

        $ads = $this->CI->V2_ad_model->get_ads_with_campaign_by_approval_status_and_network_id('UNCHECKED',$this->network_id);

        $this->CI->load->library('Send_email');
        $this->CI->load->model("V2_users_model");

        foreach ($ads as $key => $ad) {

            $ad_array['creativeId'] = $ad['network_creative_id'];
            $ad_json = json_encode($ad_array);

            echo '<pre>';
            var_dump($ad_json); //exit;

            $ad_json = urlencode($ad_json);

            $response = $this->airpush->get_ad_status($ad_json);

            $ad_status = $response['result'];

            var_dump($ad_status);

            if($ad_status['status'] != 'CREATIVE_PENDING_REVIEW') {

                $reason_message = '';
                if($ad_status['status'] == "CREATIVE_DISAPPROVED") {
                    $ad_status['status'] = "DISAPPROVED";
                    $reason_message = "Ad disapproved from network";
                }
                $data_for_update['approval_status'] = $ad_status['status'];
//                if ($ad_status['reasons']) {
//                    foreach ($ad_status['reasons'] as $reasons) {
//                        $reason_message .= ' ' . $reasons;
//                    }
//                }
                $data_for_update['disapproval_reasons'] = $reason_message;
                $this->CI->V2_ad_model->update_by_network_creative_id($ad['network_creative_id'], $data_for_update);

                if($data_for_update['approval_status'] == 'DISAPPROVED') {
                    $user = $this->V2_users_model->get_by_id($ad['userid']);
                    if($user['user_email_ability'] == 'Y' || $user['is_admin'] == 1 ){ 
                        $this->CI->send_email->send_disapproved_ad($ad['email'], $ad['campaign_io'], $ad['campaign_name'], $ad['campaign_id'],$ad['creative_name'],$ad['creative_width'],$ad['creative_height'],$ad['campaign_type'], $reason_message);
                    }
                }
            }
        }

    }

    public function get_ads_impressions() {

        // get all ads by campaign_id
        $ads = $this->CI->V2_ad_model->get_active_campaigns_ads_by_network_id($this->network_id);

//        $active_campaigns_id = $this->CI->V2_master_campaign_model->get_active_campaigns_id_by_network_id($this->network_id);
        //$json_campaigns = implode(',',$active_campaigns_id);

        //var_dump($json_campaigns,$active_campaigns_id); exit


        // check if campaign created successfully
//        if($response['message']) {
//            // save error message into db and return false
//            return false;
//        }
//        var_dump($response); exit;
//        $report = $response['result'];

        //adds the ad performance report into the database
        $this->CI->load->model('V2_campclick_impression_model');
        $this->CI->load->model('V2_campclick_click_model');

        foreach($ads as $ad){

            $time = strtotime($ad['create_date']);
            $start_date = date("Y-m-d", $time);
            $end_date = date("Y-m-d");
            $response = $this->airpush->get_ads_impressions_by_campaign_id($ad['network_campaign_id'], $start_date, $end_date);

            $report = $response['result']; //var_dump($report);

            $impresion = 0;
            foreach($report as $campaignData) {
                if($campaignData['creativeid']==$ad['network_creative_id']){
                    $impresion += $campaignData['impression'] * 1;
                    if($campaignData['clicks'] && $ad['creative_type'] == 'RICH_MEDIA') {

                        $click_data_for_update['ad_id'] = $ad['id'];
                        $click_data_for_update['network_id'] = $this->network_id;
                        $click_data_for_update['campaign_id'] = $ad['campaign_id'];
                        $click_data_for_update['timestamp'] = date("Y-m-d H:i:s");

                        $existing_clicks = $this->CI->V2_campclick_click_model->get_ad_click_count($ad['id']);
                        $clicks_diff = (int)$campaignData['clicks'] - $existing_clicks;

                        if((int)$clicks_diff) {
                            for($i=0; $i < (int)$clicks_diff; $i++) {
                                $this->CI->V2_campclick_click_model->create($click_data_for_update);
                            }
                        }
                    }
                }
            }
            var_dump(777,$impresion); //exit;
            if($impresion){
                $data_for_update = array();
                $data_for_update['ad_id'] = $ad['id'];
                $data_for_update['network_id'] = $this->network_id;
                $data_for_update['campaign_id'] = $ad['campaign_id'];
                $data_for_update['impressions_count'] = $impresion - (int)$ad['impressions_count'];
                if($data_for_update['impressions_count']) {
                    $this->CI->V2_campclick_impression_model->create($data_for_update);
                }
            }
        }

        return true;
    }

    public function get_campaigns_cost() {

        // get all ads by campaign_id
        $campaigns = $this->CI->V2_master_campaign_model->get_active_campaigns_by_network_id($this->network_id);
        //var_dump($campaigns);
        //adds the ad performance report into the database
        $this->CI->load->model('V2_campaign_cost_model');

        foreach($campaigns as $campaign){


            $time = strtotime($campaign['create_date']);
            $start_date = date("Y-m-d", $time);
            $end_date = date("Y-m-d");

            $response = $this->airpush->get_campaign_cost($campaign['network_campaign_id'], $start_date, $end_date);

            $campaignDataArra = $response['result']; //var_dump($campaignDataArra);
            $spend = 0;
            foreach($campaignDataArra as $campaignData) {
                $spend += $campaignData['Spent'] * 1;
            }
            //var_dump($spend);

            $data_for_update = array();
            $data_for_update['network_id'] = $campaign['network_id'];
            $data_for_update['campaign_id'] = $campaign['id'];
            $data_for_update['cost'] = round($spend, 2);

            if($data_for_update['cost']) {
                $this->CI->V2_campaign_cost_model->create($data_for_update);
            }

        }
        return true;
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

    public function update_location_targeting($campaign) {

        //initializing edited data
        $campaign_array['campaignId'] = $campaign['network_campaign_id'];

        $this->CI->load->model("V2_network_country_criterion_model");

        $country_array = $this->CI->V2_network_country_criterion_model->get_criteria_id_list($campaign['country'], $this->network_id);

        $country_id = $country_array[0];

        if($campaign['state']) {
            $this->CI->load->model("V2_network_state_criterion_model");
            $list_array = explode(",", $campaign['state']);
            $states_array = $this->CI->V2_network_state_criterion_model->get_criteria_id_list($list_array, $this->network_id);
            $states_list = implode("," , $states_array);
            $states = array($country_id =>$states_list);
        } else {
            $states = array($country_id =>'ALL');
        }

        $campaign_array["countryId"] = $country_id;
        $campaign_array["stateId"] = $states;

        $campaign_json = json_encode($campaign_array);
        echo '<pre>';
        var_dump($campaign_json); //exit;

        $campaign_json = urlencode($campaign_json);

        return $this->airpush->update_campaign($campaign_json);

    }

    public function update_budget($campaign) {

        //initializing edited data
        $campaign_array['campaignId'] = $campaign['network_campaign_id'];
        $campaign_array['dailyBudget'] = $campaign['budget'];

        $campaign_json = json_encode($campaign_array);
        echo '<pre>';
        var_dump($campaign_json); //exit;

        $campaign_json = urlencode($campaign_json);

        return $this->airpush->update_campaign($campaign_json);
    }

    public function update_end_date($campaign) {
        return true;
    }

    public function update_bid($campaign) {

        //initializing edited data
        $campaign_array['campaignId'] = $campaign['network_campaign_id'];
        $campaign_array['bid'] = $campaign['bid'];

        $campaign_json = json_encode($campaign_array);
        echo '<pre>';
        var_dump($campaign_json); //exit;

        $campaign_json = urlencode($campaign_json);

        $response = $this->airpush->update_campaign($campaign_json);

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




}