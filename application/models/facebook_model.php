<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Facebook_model
 *
 */

class Facebook_model extends CI_Model {

    protected $CI;
    protected $network_id = 5;
    protected $live_campaign;
    protected $live_group;

    public function __construct()	{

        parent::__construct();

        $this->CI =& get_instance();

        $this->CI->load->database();

        $this->CI->load->library("facebookAd");

        $this->CI->load->model("V2_group_model");
        $this->CI->load->model('V2_master_campaign_model');
        $this->load->model('V2_ad_model');

    }

    public function test_video() {
        $this->facebookad->test_video();
    }

    public function get_first_url() {

        $firstUrl = $this->facebookad->create_url();
        return $firstUrl;
    }

    public function get_access_token($code) {

        $result = $this->facebookad->get_access_token($code);
        return $result;

    }

    public function set_token($token) {

        $this->facebookad->refresh_set_access_taken($token);
    }

    public function create($campaign=null)    {

        // $user_id = null;
        // $campaign = $this->CI->V2_master_campaign_model->get_by_id($user_id, 119);
        if (!$campaign) {
            return false;
        }

        // $this->facebookad->array_for_interests($campaign);
        $result_campaign = $this->create_campaign($campaign);
        if($result_campaign['message']) {
            // sent mail to owner
            return array('status' => 'rejected', 'message' => $result_campaign['message']);
        }

        $io_audiences = null;

        if($campaign['is_remarketing'] == "Y" && $campaign['is_remarketing_io'] == "Y" && $campaign['remarketing_io']){
            $io_audiences = $this->create_io_targeting($campaign);
        }
        // var_dump($io_audiences);
        $create_new_lookalike = false;
        $lookalike_audiences = null;
        if($campaign['is_lookalike_audience'] == "Y"){
            if($campaign['lookalike_audiences']) {
                $lookalike_audiences = $this->create_lookalike_targeting($campaign);
            } else {
                $create_new_lookalike = true;
            }
        }

        $create_new_email = false;
        $email_audiences = null;
        if($campaign['is_email_audience'] == "Y"){
            if($campaign['email_audiences']) {
                $email_audiences = $this->create_email_targeting($campaign);
            } else {
                $create_new_email = true;
            }
        }

        if($lookalike_audiences && $io_audiences) {
            $io_audiences = array_merge($io_audiences,$lookalike_audiences);
        } else {
            if($lookalike_audiences) {
                $io_audiences = $lookalike_audiences;
            }
        }

        if($email_audiences && $io_audiences) {
            $io_audiences = array_merge($io_audiences,$email_audiences);
        } else {
            if($email_audiences) {
                $io_audiences = $email_audiences;
            }
        }
        // print_r($io_audiences, 888);
        $result_group = $this->create_group($this->live_campaign, $campaign, $io_audiences);
        if($result_group['message']) {
            // sent mail to owner
            //  var_dump('camp');
            return array('status' => 'rejected', 'message' => $result_group['message']);
        }

        if($campaign['campaign_type'] == 'FB-LEAD') {
            if(!$this->create_form($campaign)) {
                // sent mail to owner
                return 'rejected';
            }
        }

        if(!$this->create_ads($this->live_group, $campaign, $result_group['locations'])) {
            // sent mail to owner
            return 'rejected';
        }

        // clarify date diff check
        if($campaign['campaign_start_datetime']>date("Y-m-d H:i:s")) {
          //  var_dump($campaign['campaign_start_datetime'], date("Y-m-d H:i:s"));
            $status = 'SCHEDULED';
            $data_for_update['campaign_status'] = 'SCHEDULED';
            $data_for_update['network_campaign_status'] = 'PAUSED';
        } else {
            $status = 'ACTIVE';
            $data_for_update['campaign_status'] = 'ACTIVE';
            $data_for_update['network_campaign_status'] = 'ACTIVE';
        }
        // make campaign status enabled and set converted to live to Y
        $this->facebookad->updateCampaignStatus($this->live_campaign, $status);
        $data_for_update['campaign_is_converted_to_live'] = "Y";
        $data_for_update['network_campaign_id'] = $this->live_campaign;
        $this->CI->V2_master_campaign_model->update($campaign['id'], $data_for_update);

        // create custome audience
        $this->CI->load->model("Userlist_io_model");
        $this->CI->load->model("Userlist_vertical_model");

        $audience = $this->facebookad->create_audience($campaign['io']);

        $this->CI->Userlist_io_model->create_userlist_io($campaign['io'], $campaign['id'], $audience['id'], htmlspecialchars($audience['code']), $this->network_id, $campaign['userid']);
        // ad custome audience
        if($campaign['gender']) {
            $gender_id = $this->create_gender_targeting($campaign['gender']);
        } else {
            $gender_id = null;
        }

        if($create_new_lookalike) {
            $audience = $this->create_lookalike_audience($campaign);
        }

        if($create_new_email) {
            $audience = $this->create_custom_audience($campaign);
        }

        $edit_locations = $this->create_location_targeting($campaign);

    //     $response_add_audience_to_group = $this->facebookad->add_audience_to_group($this->live_group, $audience['id'], $campaign, $edit_locations, $gender_id);
    //    if($response_add_audience_to_group['message']) {
    //        // save error message into db and return false
    //        // make status REJECTED
    //        $error_data_for_update['disapproval_reasons'] = $response_add_audience_to_group['message'];
    //        $error_data_for_update['campaign_status'] = 'DISAPPROVED';
    //        $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
    //        return false;
    //    }
//
//        // need to check vertical exist
//        $vertical = $this->CI->Userlist_vertical_model->get_userlist_from_vertical($campaign['vertical']);
//        if(empty($vertical)){
//            // create new one
//            $vertical_audience = $this->adword->addAudience($this->adword, $campaign['vertical']);
//            $this->CI->Userlist_vertical_model->create_userlist_vertical($campaign['vertical'], $vertical_audience['userList']->id, htmlspecialchars($vertical_audience['code']->snippet));
//        }


        return array('status' => 'approved', 'message' => '');
    }

    public function create_campaign($campaign) {
        $response = $this->facebookad->create_campaign($campaign);
        $this->live_campaign = $response['result'];


        // check if campaign created successfully
        if($response['message']) {

            // save error message into db and return false
            $error_data_for_update['disapproval_reasons'] = $response['message'];
            $error_data_for_update['campaign_status'] = 'DISAPPROVED';
            // if this is multiple campaign then save errors in multiple table
            $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
            var_dump(444);
            return $response;
        }



        return true;
    }

    public function create_group($campaign_id, $campaign, $io_audiences) {

        $locations = $this->create_location_targeting($campaign);

	    $ads = $this->CI->V2_ad_model->get_ads_by_campaign_id($campaign['id']);

        if($campaign['gender']) {
            $gender_id = $this->create_gender_targeting($campaign['gender']);
        } else {
            $gender_id = null;
        }

        $response = $this->facebookad->create_adSet($campaign_id, $campaign, $locations, $gender_id, $ads, $io_audiences);
        // echo "<pre>";
        // print_r($response);
        // die;

        // check if campaign created successfully
        if($response['message']) {
            // save error message into db and return false
            // make status REJECTED
            $error_data_for_update['disapproval_reasons'] = $response['message'];
            $error_data_for_update['campaign_status'] = 'DISAPPROVED';
            $this->CI->V2_master_campaign_model->update($campaign_id, $error_data_for_update);
            return $response;
        }


        $data_for_update['network_campaign_id'] = $campaign_id;
        $data_for_update['network_group_id'] = $response['result'];
        $this->CI->V2_group_model->update($campaign['group_id'], $data_for_update);
        $this->live_group = $response['result'];

        $response['locations'] = $locations;
        return $response;

    }

    public function create_ads($adSetId, $campaign) {

        // select from db all ads by campaign_id
        $ads = $this->CI->V2_ad_model->get_ads_by_campaign_id($campaign['id']);

        if($campaign['campaign_type']=='FB-CAROUSEL-AD') {
            $creative = $this->facebookad->create_creative($ads, $campaign['campaign_type']);
            if ($creative['message']) {
                //    var_dump(777, $creative['message']);
                $error_data_for_update["approval_status"] = 'DISAPPROVED';
                $error_data_for_update["disapproval_reasons"] = $creative['message'];
                $this->CI->V2_ad_model->update($ads[0]['id'], $error_data_for_update);


            }
            $creativeId = $creative['result'];

            $response = $this->facebookad->createAd($adSetId, $creativeId, $ads[0]['title']);

            if ($response['message']) {
                // var_dump(888,$response['message']);
                $error_data_for_update["approval_status"] = 'DISAPPROVED';
                $error_data_for_update["disapproval_reasons"] = $response['message'];
                $this->CI->V2_ad_model->update($ads[0]['id'], $error_data_for_update);


            }

            $data_for_update["network_creative_id"] = $response['result'];
            $data_for_update["network_campaign_id"] = $this->live_campaign;
            $data_for_update["network_group_id"] = $this->live_group;
            $data_for_update["facebook_creative_id"] = $creativeId;
            $data_for_update["creative_is_active"] = 'Y';


            $this->CI->V2_ad_model->update($ads[0]['id'], $data_for_update);
        } else {
            if($campaign['campaign_type']=='FB-LEAD'){
                $this->CI->load->model("V2_fb_form_model");
                $form = $this->CI->V2_fb_form_model->get_by_id($campaign['form_id']);
            }
            foreach ($ads as $ad) {
                if($campaign['campaign_type']=='FB-LEAD'){
                    $this->CI->load->model("V2_fb_form_model");
                    $ad['form_id'] = $form['form_network_id'];
                }
                $creative = $this->facebookad->create_creative($ad, $campaign['campaign_type']);
                if ($creative['message']) {
                    //    var_dump(777, $creative['message']);
                    $error_data_for_update["approval_status"] = 'DISAPPROVED';
                    $error_data_for_update["disapproval_reasons"] = $creative['message'];
                    $this->CI->V2_ad_model->update($ad['id'], $error_data_for_update);


                }
                $creativeId = $creative['result'];

                $response = $this->facebookad->createAd($adSetId, $creativeId, $ad['title']);

                if ($response['message']) {
                    // var_dump(888,$response['message']);
                    $error_data_for_update["approval_status"] = 'DISAPPROVED';
                    $error_data_for_update["disapproval_reasons"] = $response['message'];
                    $this->CI->V2_ad_model->update($ad['id'], $error_data_for_update);


                }

                $data_for_update["network_creative_id"] = $response['result'];
                $data_for_update["network_campaign_id"] = $this->live_campaign;
                $data_for_update["network_group_id"] = $this->live_group;
                $data_for_update["facebook_creative_id"] = $creativeId;
                $data_for_update["creative_is_active"] = 'Y';


                $this->CI->V2_ad_model->update($ad['id'], $data_for_update);
            }
        }
        return true;

    }

    public function create_form($campaign) {
        var_dump(777);
        $this->CI->load->model("V2_fb_form_model");
        $form = $this->CI->V2_fb_form_model->get_by_id($campaign['form_id']);
        var_dump(444);
        $response = $this->facebookad->create_form($form);

        if ($response['message']) {
            // var_dump(888,$response['message']);
            $error_data_for_update['disapproval_reasons'] = $response['message'];
            $error_data_for_update['campaign_status'] = 'DISAPPROVED';
            // if this is multiple campaign then save errors in multiple table
            $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);

            return false;
        }

        $data_for_update["form_network_id"] = $response['result'];

        $this->CI->V2_fb_form_model->update($form['id'], $data_for_update);

        return true;

    }

    public function geo_locations_by_kordinate($zip, $radius) {

        $this->CI->load->model('zip_model');
        $results = $this->CI->zip_model->get_kordinate_by_zip($zip, $radius);

        foreach ($results as $key => $result){

            $results[$key]['radius'] = $radius;
            $results[$key]['distance_unit'] = 'mile';

        }
        return $results;

    }

    public function create_location_targeting($campaign=null) {
//        $this->CI->load->model('V2_master_campaign_model');
//        $user_id = null;
//        $campaign = $this->CI->V2_master_campaign_model->get_by_id($user_id, 99);


        $is_postal = false;
        switch(strtoupper($campaign['geotype'])) {
            default:
            case "COUNTRY":
                //$list_array = explode(",", $campaign['country']);
                //$criteria_array = $this->CI->V2_network_country_criterion_model->get_criteria_id_list($campaign['country'], $this->network_id);

                $criteria_array = array(
                    'countries' => array($campaign['country']),


                );
                break;
            case "STATE":

                $list_array = explode(",", $campaign['state']);

                $this->CI->load->model('V2_network_state_criterion_model');
                $criteria_keys = $this->CI->V2_network_state_criterion_model->get_criteria_id_list($list_array, $this->network_id);

                foreach ($criteria_keys as  $criteria_key) {

                    $criteria_array['regions'][]['key'] = $criteria_key;

                }

                break;
            case "POSTALCODE":

                $locations = $this->geo_locations_by_kordinate($campaign['zip'], $campaign['radius']);
                $criteria_array = array('custom_locations' => $locations);
                break;
        }

        if(empty($criteria_array)) {

            $error_data_for_update['disapproval_reasons'] = "criteria_array is empty";
            $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
            return false;
        }

        return $criteria_array;
    }

    public function update_location_targeting($campaign=null) {


//        $this->CI->load->model('V2_master_campaign_model');
//        $user_id = null;
//        $campaign = $this->CI->V2_master_campaign_model->get_by_id($user_id, 99);


        $is_postal = false;
        switch(strtoupper($campaign['geotype'])) {
            default:
            case "COUNTRY":
                //$list_array = explode(",", $campaign['country']);
                //$criteria_array = $this->CI->V2_network_country_criterion_model->get_criteria_id_list($campaign['country'], $this->network_id);

                $criteria_array = array(
                    'countries' => array($campaign['country']),


                );
                break;
            case "STATE":

                $list_array = explode(",", $campaign['state']);

                $this->CI->load->model('V2_network_state_criterion_model');
                $criteria_keys = $this->CI->V2_network_state_criterion_model->get_criteria_id_list($list_array, $this->network_id);

                foreach ($criteria_keys as  $criteria_key) {

                    $criteria_array['regions'][]['key'] = $criteria_key;

                }

                break;
            case "POSTALCODE":

                $locations = $this->geo_locations_by_kordinate($campaign['zip'], $campaign['radius']);
                $criteria_array = array('custom_locations' => $locations);
                break;
        }

        if(empty($criteria_array)) {

            $error_data_for_update['disapproval_reasons'] = "criteria_array is empty";
            $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
            return false;
        }

        $this->CI->load->model('V2_group_model');
        $group = $this->CI->V2_group_model->get_group_by_campaign_id($campaign['id']);

        if($campaign['gender']) {
            $gender_id = $this->create_gender_targeting($campaign['gender']);
        } else {
            $gender_id = null;
        }
        $audience = $this->facebookad->create_audience($campaign['io']);

        if ($campaign['campaign_type'] == 'FB-PAGE-LIKE') {
            $ads = $this->CI->V2_ad_model->get_ads_by_campaign_id($campaign['id']);
        }



        return $this->facebookad->edit_location($criteria_array, $group['network_group_id'], $campaign, $gender_id, $audience['id'], $ads);
    }

    public function update($campaign, $type) {


        switch($type) {
            case "location":
            $response = $this->update_location_targeting($campaign);
               // $this->facebookad->edit_location($edit_locations, $this->live_group);
                break;
            case "budget":
            $response =  $this->update_budget($campaign);
                break;
            case "end_date":
            $response =  $this->update_end_date($campaign);
                break;
        }

        return $response;
    }

    public function create_gender_targeting($gender) {

        $this->CI->load->model('V2_network_gender_criterion_model');
        $list_array = explode(",", $gender);
        $criteria_array = $this->CI->V2_network_gender_criterion_model->get_criteria_id_list($list_array, $this->network_id);

        if(isset($criteria_array)) {

            return $criteria_array[0];
        }
        else {
            return false;
        }

    }

    public function get_ads_impressions() {
        //return true;
        // get all ads by campaign_id
        $ads = $this->CI->V2_ad_model->get_active_campaigns_ads_by_network_id($this->network_id);
        // echo '<pre>';
        // var_dump($ads); //exit;
        for($i = 0;$i <= count($ads); $i++){
            if($ads[$i]['campaign_type'] == 'FB-VIDEO-VIEWS' || $ads[$i]['campaign_type'] == 'FB-PAGE-LIKE'){
                var_dump(333);
                $this->get_ads_likes($ads[$i]['campaign_type']);
            }
        }
        //var_dump(111);
        $response = $this->facebookad->getAdsImpressionsByActiveCampaigns($ads);
        //var_dump(222);
        // check if campaign created successfully
        if($response['message']) {
            // save error message into db and return false
            var_dump($response['message']);
            return false;
        }

        $report = $response['result'];

        //var_dump($report);

        //adds the ad performance report into the database
        $this->CI->load->model('V2_campclick_impression_model');
        $this->CI->load->model('V2_campclick_click_model');
        $this->CI->load->library("user_agent");
        foreach($ads as $ad){
         //echo '<pre>';
        // //var_dump($ad["campaign_type"]);
         //print_r($ad);
        // print_r($_SERVER);
            if($ad['network_creative_id'] && $report[$ad['network_creative_id']] && $ad["campaign_type"] != "FB-VIDEO-CLICKS"){
                $data_for_update = array();
                $data_for_update['ad_id'] = $ad['id'];
                $data_for_update['network_id'] = $this->network_id;
                $data_for_update['campaign_id'] = $ad['campaign_id'];
                $data_for_update['impressions_count'] = (int)$report[$ad['network_creative_id']] - (int)$ad['impressions_count'];
                //var_dump($data_for_update['impressions_count'], $ad['impressions_count']);
                if($data_for_update['impressions_count']) {
                    $this->CI->V2_campclick_impression_model->create($data_for_update);
                }
            }
        }

        return true;
    }

    public function get_ads_likes($campaign_type) {


        // get all ads by campaign_id
        $ads = $this->CI->V2_ad_model->get_active_campaigns_ads_by_network_id_likes($this->network_id);
        //var_dump($ads); exit;
        if($campaign_type == 'FB-PAGE-LIKE') {

            $response = $this->facebookad->getAdsLikesByActiveCampaigns($ads);
        }
        else {
            $response = $this->facebookad->getAdsVideoViewsByActiveCampaigns($ads);
        }


        // check if campaign created successfully
        if($response['message']) {



            // save error message into db and return false
            return false;
        }



        $report = $response['result'];


        //adds the ad performance report into the database
        $this->CI->load->model('V2_campclick_like_model');

        foreach($ads as $ad){


            if($report[$ad['network_creative_id']]){

                $data_for_update = array();
                $data_for_update['ad_id'] = $ad['id'];
                $data_for_update['network_id'] = $this->network_id;
                $data_for_update['campaign_id'] = $ad['campaign_id'];
                $data_for_update['likes_count'] = (int)$report[$ad['network_creative_id']] - (int)$ad['likes_count'];
                if($data_for_update['likes_count']) {
                    $this->CI->V2_campclick_like_model->create($data_for_update);
                }
            }
        }

        return true;
    }

    public function check_ads_approved_status() {

        $ads = $this->CI->V2_ad_model->get_ads_with_campaign_by_approval_status_and_network_id('UNCHECKED',$this->network_id);
        //var_dump($ads); exit;
        $response = $this->facebookad->getAllDisapprovedAds($ads);
        var_dump($response);
        $this->CI->load->model("V2_users_model");
        $this->CI->load->library('Send_email');

        $ads_status = $response['result'];
       // var_dump($ads_status);//exit;

        foreach ($ads as $key => $ad) {

            if($ad['network_creative_id'] && $ads_status[$ad['network_creative_id']]['approval_status'] != 'PENDING_REVIEW') {

                $reason_message = '';
                $data_for_update['approval_status'] = $ads_status[$ad['network_creative_id']]['approval_status'];
                if ($ads_status[$ad['network_creative_id']]['reasons']) {
                    foreach ($ads_status[$ad['network_creative_id']]['reasons'] as $reasons) {
                        $reason_message .= ' ' . $reasons;
                    }
                }
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


    public function get_demographics_report() {

        $campaigns = $this->CI->V2_master_campaign_model->get_active_campaigns_demographics_count($this->network_id);

        //adds the campaign demographics report into the database
        $this->CI->load->model('V2_demographics_reporting_model');
       // echo '<pre>'; print_r($campaigns);die;
        foreach($campaigns as $campaign) {

            $age_response = $this->facebookad->getCampaignAgeReport($campaign);

            $gender_response = $this->facebookad->getCampaignGenderReport($campaign);

            $age_report = $age_response['result'];
            $gender_report = $gender_response['result'];
            //echo '<pre>'; var_dump($age_report, $gender_report);
            if ($age_report && $gender_report) {
                if ($campaign['campaign_type'] == 'FB-PAGE-LIKE') {


                    $data_for_update = array();
                    $data_for_update['network_id'] = $campaign['network_id'];
                    $data_for_update['campaign_id'] = $campaign['id'];
                    $data_for_update['type'] = 'CLICK';
                    $data_for_update['network_campaign_id'] = $campaign['network_campaign_id'];
                    $data_for_update['created_date'] = date("Y-m-d H:i:s");
                    if ((int)$age_report[0]['actions'][0]['value']) {
                        $count_18_24 = (int)$age_report[0]['actions'][0]['value'] - (int)$campaign['18_24_count'];
                        if ($count_18_24 >= 0) {
                            $data_for_update['18_24'] = $count_18_24;
                        }
                    }
                    if ((int)$age_report[1]['actions'][0]['value']) {
                        $count_25_34 = (int)$age_report[1]['actions'][0]['value'] - (int)$campaign['25_34_count'];
                        if ($count_25_34 >= 0) {
                            $data_for_update['25_34'] = $count_25_34;
                        }
                    }
                    if ((int)$age_report[2]['actions'][0]['value']) {
                        $count_35_44 = (int)$age_report[2]['actions'][0]['value'] - (int)$campaign['35_44_count'];
                        if ($count_35_44 >= 0) {
                            $data_for_update['35_44'] = $count_35_44;
                        }
                    }
                    if ((int)$age_report[3]['actions'][0]['value']) {
                        $count_45_54 = (int)$age_report[3]['actions'][0]['value'] - (int)$campaign['45_54_count'];
                        if ($count_45_54 >= 0) {
                            $data_for_update['45_54'] = $count_45_54;
                        }
                    }
                    if ((int)$age_report[4]['actions'][0]['value']) {
                        $count_55_64 = (int)$age_report[4]['actions'][0]['value'] - (int)$campaign['55_64_count'];
                        if ($count_55_64 >= 0) {
                            $data_for_update['55_64'] = $count_55_64;
                        }
                    }
                    if ((int)$age_report[5]['actions'][0]['value']) {
                        $count_64 = (int)$age_report[5]['actions'][0]['value'] - (int)$campaign['64_count'];
                        if ($count_64 >= 0) {
                            $data_for_update['64'] = $count_64;
                        }
                    }
                    if ((int)$age_report['Undetermined']['actions'][0]['value']) {
                        $count_unknown_age = (int)$age_report['Undetermined']['actions'][0]['value'] - (int)$campaign['unknown_age_count'];
                        if ($count_unknown_age >= 0) {
                            $data_for_update['unknown_age'] = $count_unknown_age;
                        }
                    }
                    if ((int)$gender_report[1]['actions'][0]['value']) {
                        $count_male = (int)$gender_report[1]['actions'][0]['value'] - (int)$campaign['male_count'];
                        if ($count_male >= 0) {
                            $data_for_update['male'] = $count_male;
                        }
                    }
                    if ((int)$gender_report[0]['actions'][0]['value']) {
                        $count_female = (int)$gender_report[0]['actions'][0]['value'] - (int)$campaign['female_count'];
                        if ($count_female >= 0) {
                            $data_for_update['female'] = $count_female;
                        }
                    }
                    if ((int)$gender_report[2]['actions'][0]['value']) {
                        $count_unknown_gender = (int)$gender_report[2]['actions'][0]['value'] - (int)$campaign['unknown_gender_count'];
                        if ($count_unknown_gender >= 0) {
                            $data_for_update['unknown_gender'] = $count_unknown_gender;
                        }
                    }

                    if (count($data_for_update) > 5) {
                        $this->CI->V2_demographics_reporting_model->create($data_for_update);
                    }
                }
                else if ($campaign['campaign_type'] == 'FB-VIDEO-VIEWS' || $campaign['campaign_type'] == 'FB-VIDEO-CLICKS') {

                    $this->CI->load->model('V2_video_watch_model');

                    $video_watch_response = $this->facebookad->getCampaignVideoWatchMetricReportWithFields($campaign);
                    $video_watch = $video_watch_response['result'];
                    //echo '<pre>';
                    //print_r($video_watch);


                    $data_for_update_video_watch = array();
                    $data_for_update_video_watch['network_id'] = $campaign['network_id'];
                    $data_for_update_video_watch['campaign_id'] = $campaign['id'];
                    $data_for_update_video_watch['network_campaign_id'] = $campaign['network_campaign_id'];
                    $data_for_update_video_watch['type'] = 'watch';
                    $data_for_update_video_watch['created_date'] = date("Y-m-d H:i:s");

                    if ((int)$video_watch[0]['video_10_sec_watched_actions'][0]['value']) {
                        $count_10_sec = (int)$video_watch[0]['video_10_sec_watched_actions'][0]['value'] - (int)$campaign['10_sec_count'];
                        if ($count_10_sec >= 0) {
                            $data_for_update_video_watch['10_sec'] = $count_10_sec;
                        }
                    }
                    if ((int)$video_watch[0]['video_15_sec_watched_actions'][0]['value']) {
                    	$count_15_sec = (int)$video_watch[0]['video_15_sec_watched_actions'][0]['value'] - (int)$campaign['15_sec_count'];
                    	if ($count_15_sec >= 0) {
                    		$data_for_update_video_watch['15_sec'] = $count_15_sec;
                    	}
                    }
                    if ((int)$video_watch[0]['video_30_sec_watched_actions'][0]['value']) {
                    	$count_30_sec = (int)$video_watch[0]['video_30_sec_watched_actions'][0]['value'] - (int)$campaign['30_sec_count'];
                    	if ($count_30_sec >= 0) {
                    		$data_for_update_video_watch['30_sec'] = $count_30_sec;
                    	}
                    }
                    if ((int)$video_watch[0]['video_p25_watched_actions'][0]['value']) {
                        $count_25_p = (int)$video_watch[0]['video_p25_watched_actions'][0]['value'] - (int)$campaign['25_p_count'];
                        if ($count_25_p >= 0) {
                            $data_for_update_video_watch['25_p'] = $count_25_p;
                        }
                    }
                    if ((int)$video_watch[0]['video_p50_watched_actions'][0]['value']) {
                        $count_50_p = (int)$video_watch[0]['video_p50_watched_actions'][0]['value'] - (int)$campaign['50_p_count'];
                        if ($count_50_p >= 0) {
                            $data_for_update_video_watch['50_p'] = $count_50_p;
                        }
                    }
                    if ((int)$video_watch[0]['video_p75_watched_actions'][0]['value']) {
                        $count_75_p = (int)$video_watch[0]['video_p75_watched_actions'][0]['value'] - (int)$campaign['75_p_count'];
                        if ($count_75_p >= 0) {
                            $data_for_update_video_watch['75_p'] = $count_75_p;
                        }
                    }
                    if ((int)$video_watch[0]['video_p95_watched_actions'][0]['value']) {
                        $count_95_p = (int)$video_watch[0]['video_p95_watched_actions'][0]['value'] - (int)$campaign['95_p_count'];
                        if ($count_95_p >= 0) {
                            $data_for_update_video_watch['95_p'] = $count_95_p;
                        }
                    }
                    if (count($data_for_update_video_watch) > 5) {
                        $this->CI->V2_video_watch_model->create($data_for_update_video_watch);
                    }


                    $data_for_update = array();
                    $data_for_update['network_id'] = $campaign['network_id'];
                    $data_for_update['campaign_id'] = $campaign['id'];
                    $data_for_update['type'] = 'CLICK';
                    $data_for_update['network_campaign_id'] = $campaign['network_campaign_id'];
                    $data_for_update['created_date'] = date("Y-m-d H:i:s");
                    if ((int)$age_report[0]['actions'][2]['value']) {
                        $count_18_24 = (int)$age_report[0]['actions'][2]['value'] - (int)$campaign['18_24_count'];
                        if ($count_18_24 >= 0) {
                            $data_for_update['18_24'] = $count_18_24;
                        }
                    }
                    if ((int)$age_report[1]['actions'][2]['value']) {
                        $count_25_34 = (int)$age_report[1]['actions'][2]['value'] - (int)$campaign['25_34_count'];
                        if ($count_25_34 >= 0) {
                            $data_for_update['25_34'] = $count_25_34;
                        }
                    }
                    if ((int)$age_report[2]['actions'][2]['value']) {
                        $count_35_44 = (int)$age_report[2]['actions'][2]['value'] - (int)$campaign['35_44_count'];
                        if ($count_35_44 >= 0) {
                            $data_for_update['35_44'] = $count_35_44;
                        }
                    }
                    if ((int)$age_report[3]['actions'][2]['value']) {
                        $count_45_54 = (int)$age_report[3]['actions'][2]['value'] - (int)$campaign['45_54_count'];
                        if ($count_45_54 >= 0) {
                            $data_for_update['45_54'] = $count_45_54;
                        }
                    }
                    if ((int)$age_report[4]['actions'][2]['value']) {
                        $count_55_64 = (int)$age_report[4]['actions'][2]['value'] - (int)$campaign['55_64_count'];
                        if ($count_55_64 >= 0) {
                            $data_for_update['55_64'] = $count_55_64;
                        }
                    }
                    if ((int)$age_report[5]['actions'][2]['value']) {
                        $count_64 = (int)$age_report[5]['actions'][2]['value'] - (int)$campaign['64_count'];
                        if ($count_64 >= 0) {
                            $data_for_update['64'] = $count_64;
                        }
                    }
                    if ((int)$age_report['Undetermined']['actions'][2]['value']) {
                        $count_unknown_age = (int)$age_report['Undetermined']['actions'][2]['value'] - (int)$campaign['unknown_age_count'];
                        if ($count_unknown_age >= 0) {
                            $data_for_update['unknown_age'] = $count_unknown_age;
                        }
                    }
                    if ((int)$gender_report[1]['actions'][2]['value']) {
                        $count_male = (int)$gender_report[1]['actions'][2]['value'] - (int)$campaign['male_count'];
                        if ($count_male >= 0) {
                            $data_for_update['male'] = $count_male;
                        }
                    }
                    if ((int)$gender_report[0]['actions'][2]['value']) {
                        $count_female = (int)$gender_report[0]['actions'][2]['value'] - (int)$campaign['female_count'];
                        if ($count_female >= 0) {
                            $data_for_update['female'] = $count_female;
                        }
                    }
                    if ((int)$gender_report[2]['actions'][2]['value']) {
                        $count_unknown_gender = (int)$gender_report[2]['actions'][2]['value'] - (int)$campaign['unknown_gender_count'];
                        if ($count_unknown_gender >= 0) {
                            $data_for_update['unknown_gender'] = $count_unknown_gender;
                        }
                    }

                    if (count($data_for_update) > 5) {
                        $this->CI->V2_demographics_reporting_model->create($data_for_update);
                    }
                } else {


                    $data_for_update = array();
                    $data_for_update['network_id'] = $campaign['network_id'];
                    $data_for_update['campaign_id'] = $campaign['id'];
                    $data_for_update['type'] = 'CLICK';
                    $data_for_update['network_campaign_id'] = $campaign['network_campaign_id'];
                    $data_for_update['created_date'] = date("Y-m-d H:i:s");

                    $count_18_24 = (int)$age_report[0]['impressions'] - (int)$campaign['18_24_count'];
                    if ($count_18_24 >= 0) {
                        $data_for_update['18_24'] = $count_18_24;
                    }

                    $count_25_34 = (int)$age_report[1]['impressions'] - (int)$campaign['25_34_count'];
                    if ($count_25_34 >= 0) {
                        $data_for_update['25_34'] = $count_25_34;
                    }

                    $count_35_44 = (int)$age_report[2]['impressions'] - (int)$campaign['35_44_count'];
                    if ($count_35_44 >= 0) {
                        $data_for_update['35_44'] = $count_35_44;
                    }

                    $count_45_54 = (int)$age_report[3]['impressions'] - (int)$campaign['45_54_count'];
                    if ($count_45_54 >= 0) {
                        $data_for_update['45_54'] = $count_45_54;
                    }

                    $count_55_64 = (int)$age_report[4]['impressions'] - (int)$campaign['55_64_count'];
                    if ($count_55_64 >= 0) {
                        $data_for_update['55_64'] = $count_55_64;
                    }

                    $count_64 = (int)$age_report[5]['impressions'] - (int)$campaign['64_count'];
                    if ($count_64 >= 0) {
                        $data_for_update['64'] = $count_64;
                    }

                    $count_unknown_age = (int)$age_report['Undetermined']['impressions'] - (int)$campaign['unknown_age_count'];
                    if ($count_unknown_age >= 0) {
                        $data_for_update['unknown_age'] = $count_unknown_age;
                    }

                    $count_male = (int)$gender_report[1]['impressions'] - (int)$campaign['male_count'];
                    if ($count_male >= 0) {
                        $data_for_update['male'] = $count_male;
                    }

                    $count_female = (int)$gender_report[0]['impressions'] - (int)$campaign['female_count'];
                    if ($count_female >= 0) {
                        $data_for_update['female'] = $count_female;
                    }

                    $count_unknown_gender = (int)$gender_report[2]['impressions'] - (int)$campaign['unknown_gender_count'];
                    if ($count_unknown_gender >= 0) {
                        $data_for_update['unknown_gender'] = $count_unknown_gender;
                    }

                    if (count($data_for_update) > 5) {
                        $this->CI->V2_demographics_reporting_model->create($data_for_update);
                    }
                }
            }
        }
        return true;
    }

    public function get_campaigns_placements_report() {

        $campaigns = $this->CI->V2_master_campaign_model->get_active_campaigns_by_network_id($this->network_id);

        //adds the ad performance report into the database
        $this->CI->load->model('V2_placements_reporting_model');

        foreach($campaigns as $campaign){

            $response = $this->facebookad->get_campaigns_placements_report($campaign);

            $places_array = $response['result'];

            if($places_array) {

                $i = 1;
                foreach($places_array as $key=>$value) {
                    if($i<=5){
                        $five_place = $places_array[$key];


                        $data_for_insert = array();

                        $data_for_insert['network_id'] = $campaign['network_id'];
                        $data_for_insert['campaign_id'] = $campaign['id'];
                        $data_for_insert['network_campaign_id'] = $campaign['network_campaign_id'];
                        $data_for_insert['cost'] = round($five_place['cost_per_inline_link_click'],2);

                        if( $campaign['campaign_type'] = 'FB-PAGE-LIKE' ) {
                            if ($five_place['actions'][0]['value']) {
                                $data_for_insert['clicks'] = $five_place['actions'][0]['value'];

                            } else {
                                $data_for_insert['clicks'] = 0;
                            }
                        } else {
                            $data_for_insert['clicks'] = $five_place['inline_link_clicks'];
                        }

                        $data_for_insert['impressions'] = $five_place['impressions'];
                        $data_for_insert['placement'] = $five_place['placement'];
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

    public function get_campaigns_cost() {

        // get all ads by campaign_id
        $campaigns = $this->CI->V2_master_campaign_model->get_active_campaigns_by_network_id($this->network_id);

        //adds the ad performance report into the database
        $this->CI->load->model('V2_campaign_cost_model');

        foreach($campaigns as $campaign){

            $response = $this->facebookad->get_campaigns_cost($campaign);

            $data_for_update = array();
            $data_for_update['network_id'] = $campaign['network_id'];
            $data_for_update['campaign_id'] = $campaign['id'];
            $data_for_update['cost'] = round($response['result']['spend'], 2);

            if($data_for_update['cost']) {
                $this->CI->V2_campaign_cost_model->create($data_for_update);
            }

        }
        return true;
    }

    public function update_campaign_status($campaign) {

        return $this->facebookad->updateCampaignStatus($campaign['network_campaign_id'], $campaign['network_campaign_status']);
    }

    public function update_ad_status($ad) {

//        $ad['network_campaign_id'] = '6036095550608';
//        $ad['network_campaign_status'] = 'PAUSED';

        return $this->facebookad->update_ad_status($ad);

    }

    public function update_bid($campaign){
        // need to get group id
        $this->CI->load->model('V2_group_model');
        $group = $this->CI->V2_group_model->get_group_by_campaign_id($campaign['id']);

//        $group['network_group_id'] = '6036088403608';
//        $campaign['bid'] = '0.15';


        $response = $this->facebookad->update_bid($group['network_group_id'], $campaign['bid']);

        return true;

    }

    public function update_budget($campaign = null){
        // need to get group id
        $this->CI->load->model('V2_group_model');
        $group = $this->CI->V2_group_model->get_group_by_campaign_id($campaign['id']);

//        $group['network_group_id'] = '6036459371608';
//        $campaign['budget'] = '8';


        return $this->facebookad->update_budget($group['network_group_id'], $campaign['budget']);

    }

    public function update_end_date($campaign) {
        return true;
    }

    public function create_ad($ad_id, $for_edit = false) {

        // select from db all ads by campaign_id
        $ad = $this->CI->V2_ad_model->get_by_id($ad_id);

        if (!$ad) {
            $response['message'] = 'No find ad by id ' . $ad_id;
            return $response;
        }

        if($ad['creative_type'] == 'FB-CAROUSEL-AD') {
            $ads = $this->CI->V2_ad_model->get_ads_by_campaign_id($ad['campaign_id']);

            $creative = $this->facebookad->create_creative($ads, $ad['creative_type']);

            if ($creative['message']) {

                $error_data_for_update["approval_status"] = 'DISAPPROVED';
                $error_data_for_update["disapproval_reasons"] = $creative['message'];
                $this->CI->V2_ad_model->update($ads[0]['id'], $error_data_for_update);
                return false;

            }
            $creativeId = $creative['result'];

            $response = $this->facebookad->createAd($ads[0]['network_group_id'], $creativeId, $ads[0]['creative_name']);


            if ($response['message']) {

                $error_data_for_update["approval_status"] = 'DISAPPROVED';
                $error_data_for_update["disapproval_reasons"] = $response['message'];
                $this->CI->V2_ad_model->update($ads[0]['id'], $error_data_for_update);
                return false;

            }

            // when we edit ad we should pause last ad
            if ($for_edit && $ads[0]['approval_status'] != "DISAPPROVED" && ($ads[0]['creative_status'] == "ACTIVE" || $ads[0]['creative_status'] == "ENABLED")) {

                $ad['creative_status'] = "PAUSED";
                $this->facebookad->update_ad_status($ads[0]);

            }

            $data_for_update["network_creative_id"] = $response['result'];
            $data_for_update["facebook_creative_id"] = $creativeId;
            $data_for_update["creative_is_active"] = 'Y';
            if ($ads[0]['approval_status'] == "DISAPPROVED") {
                $data_for_update['approval_status'] = "UNCHECKED";
                $data_for_update['disapproval_reasons'] = "";
            }

            if ($response['message']) {
                // when we edit ad we not need to save error message in db
                if (!$for_edit) {
                    $error_data_for_update["approval_status"] = 'DISAPPROVED';
                    $error_data_for_update["disapproval_reasons"] = $response['message'];

                    $this->CI->V2_ad_model->update($ads[0]['id'], $error_data_for_update);

                }
                return $response;
            }

            $this->CI->V2_ad_model->update($ads[0]['id'], $data_for_update);

            return $response;
        } else {


            $campaign = $this->CI->V2_master_campaign_model->get_by_id(null, $ad['campaign_id']);
            if($campaign['campaign_type']=='FB-LEAD'){
                $this->CI->load->model("V2_fb_form_model");
                $form = $this->CI->V2_fb_form_model->get_by_id($campaign['form_id']);
                $ad['form_id'] = $form['form_network_id'];
            }
            $creative = $this->facebookad->create_creative($ad, $campaign['campaign_type']);

            if ($creative['message']) {

                $error_data_for_update["approval_status"] = 'DISAPPROVED';
                $error_data_for_update["disapproval_reasons"] = $creative['message'];
                $this->CI->V2_ad_model->update($ad['id'], $error_data_for_update);
                return false;

            }
            $creativeId = $creative['result'];

            $response = $this->facebookad->createAd($ad['network_group_id'], $creativeId, $ad['creative_name']);


            if ($response['message']) {

                $error_data_for_update["approval_status"] = 'DISAPPROVED';
                $error_data_for_update["disapproval_reasons"] = $response['message'];
                $this->CI->V2_ad_model->update($ad['id'], $error_data_for_update);
                return false;

            }

            // when we edit ad we should pause last ad
            if ($for_edit && $ad['approval_status'] != "DISAPPROVED" && ($ad['creative_status'] == "ACTIVE" || $ad['creative_status'] == "ENABLED")) {

                $ad['creative_status'] = "PAUSED";
                $this->facebookad->update_ad_status($ad);

            }

            $data_for_update["network_creative_id"] = $response['result'];
            $data_for_update["facebook_creative_id"] = $creativeId;
            $data_for_update["creative_is_active"] = 'Y';
            if ($ad['approval_status'] == "DISAPPROVED") {
                $data_for_update['approval_status'] = "UNCHECKED";
                $data_for_update['disapproval_reasons'] = "";
            }

            if ($response['message']) {
                // when we edit ad we not need to save error message in db
                if (!$for_edit) {
                    $error_data_for_update["approval_status"] = 'DISAPPROVED';
                    $error_data_for_update["disapproval_reasons"] = $response['message'];

                    $this->CI->V2_ad_model->update($ad_id, $error_data_for_update);

                }
                return $response;
            }

            $this->CI->V2_ad_model->update($ad['id'], $data_for_update);

            return $response;
        }
    }

    public function update_ad($ad_id) {

        return $this->create_ad($ad_id, true);

    }

    public function edit_location_by_country(){
        $this->facebookad->edit_location_by_country();
    }

    public function is_user_exists($user_id) {
        $this->CI->db->select('fb_user_id');
        $this->CI->db->where('fb_user_id', $user_id);
        $query = $this->CI->db->get('users');

        $result =  $query->result_array();
        return $result;
    }

    public function is_linked_to_facebook($user_id) {
        $this->CI->db->select('fb_user_id');
        $this->CI->db->where('id', $user_id);
        $query = $this->CI->db->get('users');

        $result =  $query->result_array();
        return $result[0]['fb_user_id'];
    }

    public function save_fb_user($user_id, $fb_user_id, $access_token) {
        $data = [
            'fb_user_id' => $fb_user_id,
            'fb_access_token' => (string)$access_token
        ];

//        echo '<pre>'; print_r($data); die;

        $this->CI->db->where('id', $user_id);
        $query = $this->CI->db->update('users', $data);

        if (!$query) {
            die('not query');
        }

        return true;
    }

    public function is_page_exists($user_id) {
        $this->CI->db->select('user_id');
        $this->CI->db->where('user_id', $user_id);
        $query = $this->CI->db->get('v2_fb_pages');

        return $query->result_array()[0]['user_id'];
    }

    public function update_fb_pages($user_id, $data) {
        $this->CI->db->where('user_id', $user_id);
        $query = $this->CI->db->update('v2_fb_pages', $data);
    }

    public function save_fb_pages($data) {
        $query = $this->CI->db->insert('v2_fb_pages', $data);
    }

    public function get_fb_pages($user_id) {

        $this->CI->db->select('*');
        $this->CI->db->where('user_id', $user_id);
        $query = $this->CI->db->get('v2_fb_pages');

        return $query->result_array();
    }

    public function get_access_token_from_db($user_id) {
        $this->CI->db->select('fb_access_token');
        $this->CI->db->where('id', $user_id);
        $query = $this->CI->db->get('users');

        return $query->result_array()[0]['fb_access_token'];
    }

    public function delete_linked_data($user_id) {
        $this->CI->db->where('id', $user_id);

        $data = [
            'fb_user_id' => null,
            'fb_access_token' => null
        ];
        $query1 = $this->CI->db->update('users', $data);

        $query2 = $this->CI->db->delete('v2_fb_pages', array('user_id' => $user_id));

        if (!$query1 || !$query2) {
            return false;
        } else {
            return true;
        }
    }

    public function get_interests() {
        return $this->facebookad->get_interests();
    }

    public function get_behaviors() {
        return $this->facebookad->get_behaviors();
    }

    public function get_demographics_by_type($value, $type ) {
        return $this->facebookad->get_demographics_by_type($value, $type);
    }

    public function get_demographics() {
        return $this->facebookad->get_demographics();
    }

    public function create_io_targeting($campaign) {

        $list_array = explode(",", $campaign['remarketing_io']);
        $this->CI->load->model("Userlist_io_model");
        $criteria_array = $this->CI->Userlist_io_model->get_criteria_id_list_for_fb($list_array);

        if(!$criteria_array) {
            return null;
        }

        return $criteria_array;

    }
    public function create_lookalike_targeting($campaign) {

        $list_array = explode(",", $campaign['lookalike_audiences']);
        $this->CI->load->model("Userlist_io_model");
        $criteria_array = $this->CI->Userlist_io_model->get_lookalike_criteria_id_list_for_fb($list_array);

        if(!$criteria_array) {
            return null;
        }

        return $criteria_array;

    }
    public function create_email_targeting($campaign) {

        $list_array = explode(",", $campaign['email_audiences']);
        $this->CI->load->model("Userlist_io_model");
        $criteria_array = $this->CI->Userlist_io_model->get_email_criteria_id_list_for_fb($list_array);

        if(!$criteria_array) {
            return null;
        }

        return $criteria_array;

    }

    public function create_audience($campaign) {

        $this->CI->load->model("Userlist_io_model");

        $audience = $this->facebookad->create_audience($campaign['io']);

        $this->CI->Userlist_io_model->create_userlist_io($campaign['io'], $campaign['id'], $audience['id'], htmlspecialchars($audience['code']), $this->network_id, $campaign['userid']);

        return array('snippet'=>htmlspecialchars($audience['code']),'remarketing_list_id'=>$audience['id'], 'io'=>$campaign['io'], 'network'=>'FACEBOOK');

    }

    public function create_lookalike_audience($campaign) {

        $this->CI->load->model("Userlist_io_model");
        $lookalike = $this->CI->Userlist_io_model->get_lookalike_userlist_by_campaign_id($campaign['id']);
        $audience = $this->facebookad->create_lookalike_audience($lookalike);
        $update_data = array('remarketing_list_id'=>$audience['id'], 'sniped_code'=>htmlspecialchars($audience['code']));
        $this->CI->Userlist_io_model->update($lookalike['id'], $update_data);

        return array('snippet'=>htmlspecialchars($audience['code']),'remarketing_list_id'=>$audience['id'], 'io'=>$campaign['io'], 'network'=>'FACEBOOK');

    }

    public function create_custom_audience($campaign) {

        $this->CI->load->model("Userlist_io_model");
        $email = $this->CI->Userlist_io_model->get_email_userlist_by_campaign_id($campaign['id']);
        $audience = $this->facebookad->create_custom_audience($email);
        $update_data = array('remarketing_list_id'=>$audience['id'], 'sniped_code'=>htmlspecialchars($audience['code']));
        $this->CI->Userlist_io_model->update($email['id'], $update_data);

        return array('snippet'=>htmlspecialchars($audience['code']),'remarketing_list_id'=>$audience['id'], 'io'=>$campaign['io'], 'network'=>'FACEBOOK');

    }

    public function get_campaigns_leads($ads) {

        // get all ads by campaign_id

        //$campaigns = $this->CI->V2_master_campaign_model->get_active_campaigns_by_network_id_and_type($this->network_id, 'FB-LEAD');

        //var_dump($ads); exit;
        //adds the ad performance report into the database
        $this->CI->load->model('V2_fb_lead_model');
        $this->CI->load->model("V2_fb_form_model");
        $current_form_id = null;
        $csv_data = [];
        foreach($ads as $ad){

            if($current_form_id != $ad['form_id']) {
                $form = $this->CI->V2_fb_form_model->get_by_id($ad['form_id']);
                $current_form_id = $form['id'];
            }

            //var_dump($form); exit;
            //$response = $this->facebookad->get_leads($form['form_network_id']);
            $response = $this->facebookad->get_leads_by_ad_id($ad['network_creative_id']);
            var_dump($response);
//            $leads = $this->CI->V2_fb_lead_model->get_leads_by_campaign_id($ad['id']);
            $leads = $this->CI->V2_fb_lead_model->get_leads_by_ad_id($ad['id']);
            $existing_leads =[];
            var_dump($leads);
            foreach ($leads as $lead) {
                $existing_leads[]=$lead['lead_network_id'];
            }
            //var_dump($existing_leads); exit;

            if($response['result']) {
                foreach ($response['result'] as $new_lead) {
                    if (!in_array($new_lead['id'], $existing_leads)) {
                        var_dump(777);
                        $data_for_insert = array();
                        foreach ($new_lead["field_data"] as $option) { //var_dump($option); exit;
                            $data_for_insert[$option['name']] = $option['values'][0];
                        }

                        $data_for_insert['lead_network_id'] = $new_lead['id'];
                        $data_for_insert['created_time'] = $new_lead['created_time'];
                        $data_for_insert['created_date'] = date("Y-m-d H:i:s");
                        $data_for_insert['campaign_id'] = $ad['campaign_id'];
                        $data_for_insert['ad_id'] = $ad['id'];
                        $data_for_insert['form_id'] = $form['form_network_id'];
                        $data_for_insert['page_id'] = $form['page_id'];

                        //if($data_for_insert['cost']) {
                        $this->CI->V2_fb_lead_model->create($data_for_insert);
                        //}

                        if($form['export_type'] == 'email_address' && $form['email_type'] == 'immediately') {
                            $data_for_insert['campaign_name'] = $ad['campaign_name'];
                            $data_for_insert['io'] = $ad['io'];
                            $csv_data[$form['email']][] = $data_for_insert;
                        }
                    }
                }
            }

        }

        if($csv_data) {
            $this->CI->load->library('Send_email');
            foreach ($csv_data as $key=>$value) {
                // create csv file
                $file_name = 'lead reporting between for '.date("Y-m-d H:i:s").'.csv';
                $fp = fopen('v2/files/tmp/'.$file_name, 'w');
                fputcsv($fp, $value);
                fclose($fp);
                //$full_name = base_url().'v2/files/tmp/'.$file_name;
                $full_name = 'v2/files/tmp/'.$file_name;
                $this->CI->send_email->send_lead_reporting($key, $value['io'], $value['campaign_name'], 'NEW', $full_name);
            }
        }

        return true;
    }
}
