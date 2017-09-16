<?php

class Google_model_site extends CI_Model	{

	private $network_id = 1;
	private $text_ad_type = 'TEXTAD';
	private $display_ad_type = 'DISPLAY';
	protected $CI;
	protected $adword;
	protected $live_campaign;
	protected $live_group;
	public function __construct()	{

		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->library("user_agent");
		$this->CI->load->model("Domains_model");
		$this->CI->load->model("Campclick_model");
		$this->CI->load->model("Ad_model");
		$this->CI->load->model("Log_model");
		$this->CI->load->model("V2_ad_model");
		$this->CI->load->model("V2_master_campaign_model");
        $this->CI->load->model("V2_campaign_network_criteria_rel_model");
		$this->CI->load->model("V2_group_model");
        $this->load->model('v2_ads_disapproval_model');
		$this->CI->load->library("Clickcap");
		$this->CI->load->library("Adwords");
		$this->adword = new $this->CI->adwords();
	}

	public function create($campaign)    {
        if (!$campaign) {
            return false;
        }
        $add_demographic_targeting = true;
        $result_campaign = $this->create_campaign($campaign);
        if($result_campaign['message']) {

            // sent mail to owner
            //  var_dump('camp');
            return array('status' => 'rejected', 'message' => $result_campaign['message']);
        }


        $create_group = $this->create_group($this->live_campaign->id, $campaign);



        if($create_group['message']) {
            // sent mail to owner
            // remove campaign from adword
            $this->adword->updateCampaignStatus($this->adword, $this->live_campaign->id, 'REMOVED');
            return array('status' => 'rejected', 'message' => $result_campaign['message']);
        }

        //if(!empty($campaign['domain_exclusions'])) {
        //$result_domain_exclusions = $this->create_domain_exclusions_targeting($campaign);

            // if($result_domain_exclusions['message']) {
            //     var_dump('error message');
            // }
        //}
        var_dump(333);
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
        var_dump(111);


		if(!empty($campaign['gender'])){
            $result_create_gender = $this->create_gender_targeting($campaign);
            if($result_create_gender['message']) {
                // sent mail to owner
                // remove campaign from adword
                var_dump(111);
                $this->adword->updateCampaignStatus($this->adword, $this->live_campaign->id, 'REMOVED');

                return array('status' => 'rejected', 'message' => $result_create_gender['message']);
            }
			$add_demographic_targeting = false;
		}

		if(!empty($campaign['interests'])){
            $result_create_interest = $this->create_interest_targeting($campaign);
            if($result_create_interest['message']) {
                // sent mail to owner
                // remove campaign from adword
                var_dump(1001);
                $this->adword->updateCampaignStatus($this->adword, $this->live_campaign->id, 'REMOVED');

                return array('status' => 'rejected', 'message' => $result_create_gender['message']);
            }
			$add_demographic_targeting = false;
		}

		if(!empty($campaign['behaviors'])){
            $result_create_behavior = $this->create_in_market_targeting($campaign);
            if($result_create_behavior['message']) {
                // sent mail to owner
                // remove campaign from adword
                var_dump(1001);
                $this->adword->updateCampaignStatus($this->adword, $this->live_campaign->id, 'REMOVED');

                return array('status' => 'rejected', 'message' => $result_create_gender['message']);
            }
			$add_demographic_targeting = false;
		}

		if(!empty($campaign['carrier']) && $campaign['geotype']!='postalcode') {
            $result_create_carrier_targeting = $this->create_carrier_targeting($campaign);
			if($result_create_carrier_targeting['message']) {
                // sent mail to owner
                // remove campaign from adword
                $this->adword->updateCampaignStatus($this->adword, $this->live_campaign->id, 'REMOVED');
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
                    $this->adword->updateCampaignStatus($this->adword, $this->live_campaign->id, 'REMOVED');
                    return array('status' => 'rejected', 'message' => $result_create_vertical_targeting['message']);
                }
			}

			if($campaign['is_remarketing_io'] == "Y" && $campaign['remarketing_io']){
                $result_create_io_targeting = $this->create_io_targeting($campaign);
				if($result_create_io_targeting['message']) {
                    // sent mail to owner
                    // remove campaign from adword
                    $this->adword->updateCampaignStatus($this->adword, $this->live_campaign->id, 'REMOVED');
                    return array('status' => 'rejected', 'message' => $result_create_io_targeting['message']);
                }
			}
			$add_demographic_targeting = false;
		}

        var_dump(222);
		if($add_demographic_targeting) {
			$this->adword->createDemographicsTargeting($this->adword, $this->live_group->id);
		}
		if(!$this->create_ads($this->live_group->id, $campaign)) {
            // sent mail to owner
            // remove campaign from adword
            $this->adword->updateCampaignStatus($this->adword, $this->live_campaign->id, 'REMOVED');
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
        $this->adword->updateCampaignStatus($this->adword, $this->live_campaign->id, $status);

		$data_for_update['campaign_is_converted_to_live'] = "Y";
		$data_for_update['network_campaign_id'] = $this->live_campaign->id;


        if($campaign['is_multiple']=='Y') {
            $this->CI->load->model("V2_multiple_campaign_model");
            $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $data_for_update);
            return true;
        }
		$this->CI->V2_master_campaign_model->update($campaign['id'], $data_for_update);
        var_dump(555);
        $this->CI->load->model("Userlist_io_model");
        $this->CI->load->model("Userlist_vertical_model");

        $audience = $this->adword->addAudience($this->adword, $campaign['io']);
        $this->CI->Userlist_io_model->create_userlist_io($campaign['io'], $campaign['id'], $audience['userList']->id, htmlspecialchars($audience['code']->snippet), $this->network_id, $campaign['userid']);
        var_dump(444);
        // need to check vertical exist
        $vertical = $this->CI->Userlist_vertical_model->get_userlist_from_vertical($campaign['vertical']);
        if(empty($vertical)){
            // create new one
            $vertical_audience = $this->adword->addAudience($this->adword, $campaign['vertical']);
            $this->CI->Userlist_vertical_model->create_userlist_vertical($campaign['vertical'], $vertical_audience['userList']->id, htmlspecialchars($vertical_audience['code']->snippet));
        }

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

	public function create_campaign($campaign) {
        if($campaign['is_multiple']=='Y') {
            $this->CI->load->model("V2_multiple_campaign_model");
            $multiple_campaign = $this->CI->V2_multiple_campaign_model->get_by_campaign_id(null,$campaign['id']);
            $campaign['budget'] = $multiple_campaign['budget'];
        }
    	$response = $this->adword->createCampaign($this->adword, $campaign);
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
            $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
            return $response;
        }
        $this->live_campaign = $response['result'];

        //$data_for_update = array();
        return true;
	}

	public function create_group($campaign_id, $campaign) {
        //generate group name
        $group_name = $campaign['name'].'_group';
        $maxClick = $campaign['max_clicks'];
        $response = $this->adword->createGroup($this->adword, $campaign_id, $group_name, $maxClick, $campaign['bid'], 'ACTIVE');
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
		$data_for_update['network_group_id'] = $this->live_group->id;
		$data_for_update['group_status'] = $this->live_group->status;
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
        if(!$ad){
            $response['message'] = 'No find ad by id '.$ad_id;
            return $response;
        }
		//add ads into adwords
		if($ad['creative_type'] == $this->text_ad_type){
			$response = $this->adword->createTextAds($this->adword, $ad['network_group_id'], $ad);
//			if($ad['keywords']!="RON" && !$response['message']){
				//$keyword_response = $this->adword->createKeyword($this->adword, $ad['network_group_id'], $ad['keywords']);
                // detect keyword error
//                if($keyword_response['message']) {
//                    // when we edit ad we not need to save error message in db
//                    if(!$for_edit) {
//                        // save in ads table keyword error message
//                        $error_data_for_update["approval_status"] = 'DISAPPROVED';
//                        $error_data_for_update["disapproval_reasons"] = $keyword_response['message'];
//                        $this->CI->V2_ad_model->update($ad_id, $error_data_for_update);
//                    }
//                    // remove created ad from network
//                    $this->adword->updateAdStatus($this->adword, $ad['network_group_id'], $response['result']->ad->id, 'DISABLED');
//                    return $keyword_response;
//                } else {
                    // keyword successfully created
//                    $campaign['id'] = $ad['campaign_id'];
//                    $campaign['network_id'] = $this->network_id;
//                    $this->CI->load->model('V2_campaign_network_criteria_rel_model');
//                    $type = 'keyword';
                    // check if we have keywords for current campaign
                    //$existing_keywords = $this->CI->V2_campaign_network_criteria_rel_model->get_criteria_by_campaign_id_network_id_and_type($ad['campaign_id'],$this->network_id,$type);

//                    if($existing_keywords) {
//                        // remove old keyword from group
//                        // get old keyword id
//                        //				$list_array = explode(",", $existing_keywords['criteria_value']);
//                        //				$network_list_array = explode(",", $existing_keywords['criteria_network_value']);
//                        //				$key = array_search($old_keyword, $list_array);
//                        //				if($key && $network_list_array[$key]) {
//                        //					$keyword_response = $this->adword->removeCriterion($this->adword, $ad['network_group_id'], $network_list_array[$key]);
//                        //				}
//                        // add new keyword to existing keywords list and update it by id
//                        $existing_keywords['criteria_value'] = $existing_keywords['criteria_value'].','.$ad['keywords'];
//                        $existing_keywords['criteria_network_value'] = $existing_keywords['criteria_network_value'].','.$keyword_response['result'];
//
//                        $this->CI->V2_campaign_network_criteria_rel_model->update_network_value_by_id($existing_keywords['id'], $existing_keywords['criteria_value'], $existing_keywords['criteria_network_value']);
//                    } else {
//                        // save keyword criteria for current campaign in db
//                        $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $ad['keywords'], $type, $keyword_response['result']);
//                    }
//                }
//			}
		} else {
            if($ad['is_multiple']=='Y') {
                $this->CI->load->model("V2_multiple_ad_model");
                $multiple_ad = $this->CI->V2_multiple_ad_model->get_by_ad_id($ad['id']);
                if(!$for_edit) {
                    $ad['destination_url'] = $ad['destination_url'] . '/' . $this->network_id;
                }
                $response = $this->adword->createImageAd($this->adword, $multiple_ad['network_group_id'], $ad);
            }
			$response = $this->adword->createImageAd($this->adword, $ad['network_group_id'], $ad);
		}

		if($response['message']) {
			// when we edit ad we not need to save error message in db
			if(!$for_edit) {
				$error_data_for_update["approval_status"] = 'DISAPPROVED';
				$error_data_for_update["disapproval_reasons"] = $response['message'];
//                if($campaign['is_multiple']=='Y') {
//                    $this->CI->load->model("V2_multiple_ad_model");
//                    $this->CI->V2_multiple_ad_model->update_by_ad_id($ad['id'], $error_data_for_update);
//                } else {
                    $this->CI->V2_ad_model->update($ad_id, $error_data_for_update);
//                }
			}
            return $response;
		}

		$data_for_update=array();

		$data_for_update["creative_status"]=$response['result']->status;
		$data_for_update["approval_status"]=$response['result']->approvalStatus;
		$data_for_update["disapproval_reasons"]=$response['result']->disapprovalReasons;
		$data_for_update["network_creative_id"]=$response['result']->ad->id;
		$data_for_update["creative_is_active"]='Y';

		if($for_edit && $ad['creative_status']=="ENABLED" && $ad['network_creative_id']) {
            if($ad['is_multiple']=='Y') {
                $this->CI->load->model("V2_multiple_ad_model");
                $multiple_ad = $this->CI->V2_multiple_ad_model->get_by_ad_id($ad['id']);
                $this->adword->updateAdStatus($this->adword, $multiple_ad['network_group_id'], $multiple_ad['network_creative_id'], 'PAUSED');
            } else {
                $this->adword->updateAdStatus($this->adword, $ad['network_group_id'], $ad['network_creative_id'], 'PAUSED');
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
            $status = "ENABLED";
        } else {
            $status = "PAUSED";
        }

        if($ad['is_multiple']=='Y') {
            $this->CI->load->model("V2_multiple_ad_model");
            $multiple_ad = $this->CI->V2_multiple_ad_model->get_by_ad_id($ad['id']);
            return $this->adword->updateAdStatus($this->adword, $multiple_ad['network_group_id'], $multiple_ad['network_creative_id'], $status);
        }
        return $this->adword->updateAdStatus($this->adword, $ad['network_group_id'], $ad['network_creative_id'], $status);
    }

	public function update_campaign_status($campaign) {

        //add cahnges into adwords
        if($campaign['network_campaign_status'] == "ACTIVE" || $campaign['network_campaign_status'] == "ENABLED" ) {
            $status = "ENABLED";
        } else {
            $status = "PAUSED";
        }
        if($campaign['is_multiple']=='Y') {
            $this->CI->load->model("V2_multiple_campaign_model");
            $multiple_campaign = $this->CI->V2_multiple_campaign_model->get_by_campaign_id(null,$campaign['id']);
            return $this->adword->updateCampaignStatus($this->adword, $multiple_campaign['network_campaign_id'], $status);
        }
        return $this->adword->updateCampaignStatus($this->adword, $campaign['network_campaign_id'], $status);
    }

    public function check_ads_approved_status() {

        $this->load->model("V2_users_model");
        $campaign_ids = $this->CI->V2_ad_model->get_campaign_id_by_ad_approval_status_and_network_id('UNCHECKED',$this->network_id);
        $ads = $this->CI->V2_ad_model->get_ads_with_campaign_by_approval_status_and_network_id('UNCHECKED',$this->network_id);

        $campaign_id = array();
        foreach($campaign_ids as $id) {
            $campaign_id[] = $id['network_campaign_id'];
        }

        $response = $this->adword->getAllDisapprovedAds($this->adword, $campaign_id);

        $this->CI->load->library('Send_email');

        foreach($response['result'] as $ad_array) {

            foreach ($ads as $key => $ad) {

                if($ad['network_creative_id'] == $ad_array['network_creative_id'] && $ad_array['approval_status'] != $ad['approval_status']) {

                    $reason_message = '';
                    $data_for_update['approval_status'] = $ad_array['approval_status'];
                    if ($ad_array['reasons']) {
                        foreach ($ad_array['reasons'] as $reasons) {
                            $reason_message .= ' ' . $reasons;
                        }
                    }

                    $data_for_update['disapproval_reasons'] = $reason_message;
                    $this->CI->V2_ad_model->update_by_network_creative_id($ad_array['network_creative_id'], $data_for_update);

                    // send email for disapproved ads to user
                    if($data_for_update['approval_status'] == 'DISAPPROVED') {
                        $user = $this->V2_users_model->get_by_id($ad['userid']);
                        if($user['user_email_ability'] == 'Y' || $user['is_admin'] == 1 ){ 
                            $this->CI->send_email->send_disapproved_ad(
                                $ad['email'],
                                $ad['campaign_io'],
                                $ad['campaign_name'],
                                $ad['campaign_id'],
                                $ad['creative_name'],
                                $ad['creative_width'],
                                $ad['creative_height'],
                                $ad['campaign_type'],
                                $reason_message
                            );
                        }
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
        var_dump('ads');
		// select from db all ads by campaign_id
		$ads = $this->CI->V2_ad_model->get_ads_by_campaign_id($campaign['id']);

		//add ads into adwords
		foreach($ads as $ad) {
            if($campaign['is_multiple']=='Y') {
                $ad['destination_url'] = $ad['destination_url'].'/'.$this->network_id;
            }
			if($ad['creative_type'] == $this->text_ad_type){
				$response = $this->adword->createTextAds($this->adword, $group_id, $ad);

			} else {
                $response = $this->adword->createImageAd($this->adword, $group_id, $ad);

			}

            if($response['message']) {

                $error_data_for_update["approval_status"]='DISAPPROVED';
                $error_data_for_update["disapproval_reasons"]=$response['message'];
                if($campaign['is_multiple']=='Y') {
                    $this->CI->load->model("V2_multiple_ad_model");
                    $this->CI->V2_multiple_ad_model->update_by_ad_id($ad['id'], $error_data_for_update);
                } else {
                    $this->CI->V2_ad_model->update($ad['id'], $error_data_for_update);
                }

            } else {

                $data_for_update = array();
                $data_for_update["creative_status"] = $response['result']->status;
                $data_for_update["approval_status"] = $response['result']->approvalStatus;
                $data_for_update["disapproval_reasons"] = $response['result']->disapprovalReasons;
                $data_for_update["network_creative_id"] = $response['result']->ad->id;
                $data_for_update["network_campaign_id"] = $this->live_campaign->id;
                $data_for_update["network_group_id"] = $this->live_group->id;
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

	public function create_location_targeting($campaign) {

		$is_postal = false;
		switch(strtoupper($campaign['geotype'])) {
			default:
			case "COUNTRY":
				//$list_array = explode(",", $campaign['country']);
				$criteria_array = $this->CI->V2_network_country_criterion_model->get_criteria_id_list($campaign['country'], $this->network_id);
				break;
			case "STATE":
				$list_array = explode(",", $campaign['state']);
				$criteria_array = $this->CI->V2_network_state_criterion_model->get_criteria_id_list($list_array, $this->network_id);
				break;
			case "POSTALCODE":
				$is_postal = true;
				$list_array = explode(",", $campaign['zip']);
				// we need to check if postal code is mutching?
				$criteria_array = $list_array;
				//$criteria_array = $this->CI->V2_network_zip_criterion_model->get_criteria_id_list($list_array, $this->network_id);
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

		$proximity_criteria_list = "";
		$criteria_list = implode("," , $criteria_array);
        $error_ocured = false;
		foreach ($criteria_array as $criterion_id) {
			if($is_postal) {
				$response = $this->adword->createProximityCriteria($this->adword, $this->live_campaign->id, $criterion_id, $campaign['radius']);
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
                    $proximity_criteria_array[] = $response['result'];
                }

			} else {
				$response = $this->adword->createLocationCriteria($this->adword, $this->live_campaign->id, $criterion_id);
                if($response['message']) {
                    $error_ocured = true;
                    $error_data_for_update["campaign_status"]='DISAPPROVED';
                    $error_data_for_update["disapproval_reasons"]=$response['message'];
                    if($campaign['is_multiple']=='Y') {
                        $this->CI->load->model("V2_multiple_campaign_model");
                        $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
                    } else {
                        $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
                    }

                    break;
                }
			}
		}

//        if($error_ocured) {
//            return false;
//        }

		if($is_postal && count($proximity_criteria_array)){
			$proximity_criteria_list = implode("," , $proximity_criteria_array);
			$this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $criteria_list, $type, $proximity_criteria_list);
		} else {
			$this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $criteria_list, $type);
		}

        return true;
	}

	public function create_age_targeting($campaign) {

		$list_array = explode(",", $campaign['age']);
		$criteria_array = $this->CI->Network_age_criterion_model->get_criteria_id_list($list_array, $this->network_id);

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

		$criteria_list = implode("," , $criteria_array);
		$type = 'age';
//        foreach ($criteria_array as $criterion_id){
//            $this->adword->createAgeCriteria($this->adword, $this->live_campaign->id, $criterion_id);
//        }

		$this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $criteria_list, $type);
	}

    public function create_domain_exclusions_targeting($campaign) {
        //var_dump(111);
        //$list_array = explode(",", $campaign['domain_exclusions']);

        //var_dump($criteria_array); exit;
        $type = 'domain_exclusions';
        $domains = $this->CI->clickcap->get_domains();
        //var_dump($domains); exit;
        if(!$domains) {
            return true;
        }
        foreach ($domains as $domain){
            $response = $this->adword->createDomainExclusionsCriteria($this->adword, $this->live_campaign->id, $domain);
            if($response['message']) {
                // save error message into db and return false
                $error_data_for_update['disapproval_reasons'] = $response['message'];
                if($campaign['is_multiple']=='Y') {
                    $this->CI->load->model("V2_multiple_campaign_model");
                    $this->CI->V2_multiple_campaign_model->update_by_campaign_id($campaign['id'], $error_data_for_update);
                } else {
                    //var_dump($error_data_for_update); exit;
                    $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
                }
                return $response;
            }
            $response_array[] = $response['result'];
        }

        $response_str = implode(",", $response_array);
        $domains_str = implode(",", $domains);

        $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $domains_str, $type, $response_str);
        return true;
    }

	public function create_gender_targeting($campaign) {

		$list_array = explode(",", $campaign['gender']);
		$criteria_array = $this->CI->V2_network_gender_criterion_model->get_criteria_id_list($list_array, $this->network_id);
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
		$type = 'gender';
		$criteria_list = implode("," , $criteria_array);
		//var_dump($criteria_list); exit;
		foreach ($criteria_array as $criterion_id){
			$response = $this->adword->createGenderCriteria($this->adword, $this->live_group->id, $criterion_id);
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

	public function create_interest_targeting($campaign) {

        $criteria_array = explode(",", $campaign['interests']);
		//$criteria_array = $this->CI->V2_network_gender_criterion_model->get_criteria_id_list($list_array, $this->network_id);
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
		$type = 'interest';
		$criteria_list = implode("," , $criteria_array);
		//var_dump($criteria_list); exit;
		foreach ($criteria_array as $criterion_id){ var_dump($criterion_id);
			$response = $this->adword->createInterestCriteria($this->adword, $this->live_group->id, $criterion_id);
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
            $result[] = $response['result'];
        }
        $result_list = implode("," , $result);
        $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $criteria_list, $type, $result_list);
        return true;
	}

	public function create_in_market_targeting($campaign) {

        $criteria_array = explode(",", $campaign['behaviors']);
		//$criteria_array = $this->CI->V2_network_gender_criterion_model->get_criteria_id_list($list_array, $this->network_id);
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
		$type = 'in_market';
		$criteria_list = implode("," , $criteria_array);
		//var_dump($criteria_list); exit;
		foreach ($criteria_array as $criterion_id){ var_dump($criterion_id);
			$response = $this->adword->createInMarketCriteria($this->adword, $this->live_group->id, $criterion_id);
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
            $result[] = $response['result'];
        }
        $result_list = implode("," , $result);

		$this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $criteria_list, $type, $result_list);
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
			$response = $this->adword->createCarrierCriteria($this->adword, $this->live_campaign->id, $criterion_id);
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
        //$this->CI->load->model("V2_campaign_network_criteria_rel_model");
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
        //$this->CI->load->model("V2_campaign_network_criteria_rel_model");
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
				$this->CI->load->model(V2_network_country_criterion_model);
				$new_criteria_array = $this->CI->V2_network_country_criterion_model->get_criteria_id_list($campaign['country'], $this->network_id);

				//remove old criterias from adwords
				foreach($old_criteria_rows as $old_criterion_row) {
					//remove all another location criteria from adwords (postalcode and states)
					if($old_criterion_row['criteria_type']=='state') {
						$old_criteria_array = explode(",", $old_criterion_row['criteria_value']);
						// remove from adword add new func remove campaign criteria
						$this->adword->removeCampaignCriteria($this->adword, $campaign['network_campaign_id'], $old_criteria_array);
						$this->CI->V2_campaign_network_criteria_rel_model->delete_by_id($old_criterion_row['id']);
						//remove from our db network-criteri-rel table
					} elseif($old_criterion_row['criteria_type']=='postalcode') {
						$old_criteria_array = explode(",", $old_criterion_row['criteria_network_value']);
						// remove from adword add new func remove campaign criteria
						$this->adword->removeCampaignCriteria($this->adword, $campaign['network_campaign_id'], $old_criteria_array);
						//remove from our db network-criteri-rel table
						$this->CI->V2_campaign_network_criteria_rel_model->delete_by_id($old_criterion_row['id']);
					} else {
						$old_country = $old_criterion_row['criteria_value'];
						$old_country_row_id = $old_criterion_row['id'];
						//remove from our db network-criteri-rel table and update new values
					}
				}
				if($old_country) {
					if($new_criteria_array[0] != $old_country){
						$old_criteria_array = explode(",", $old_country);
						$this->adword->removeCampaignCriteria($this->adword, $campaign['network_campaign_id'], $old_criteria_array);
						$this->adword->createLocationCriteria1($this->adword, $campaign['network_campaign_id'], $new_criteria_array);
						$this->CI->V2_campaign_network_criteria_rel_model->update_value_by_id($old_country_row_id, $new_criteria_array[0]);
					}
				} else {
					$this->adword->createLocationCriteria1($this->adword, $campaign['network_campaign_id'], $new_criteria_array);
					$this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $new_criteria_array[0], $type);
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
						$this->adword->removeCampaignCriteria($this->adword, $campaign['network_campaign_id'], $old_criteria_array);
						$this->CI->V2_campaign_network_criteria_rel_model->delete_by_id($old_criterion_row['id']);
						//remove from our db network-criteri-rel table
					} elseif($old_criterion_row['criteria_type']=='postalcode') {
						$old_criteria_array = explode(",", $old_criterion_row['criteria_network_value']);
						// remove from adword add new func remove campaign criteria
						$this->adword->removeCampaignCriteria($this->adword, $campaign['network_campaign_id'], $old_criteria_array);
						//remove from our db network-criteri-rel table
						$this->CI->V2_campaign_network_criteria_rel_model->delete_by_id($old_criterion_row['id']);
					} else {

						$old_states = $old_criterion_row['criteria_value'];
						$old_states_row_id = $old_criterion_row['id'];
					}
				}
				if($old_states) {
					$old_states_array = explode(",", $old_states);
					// get all new satates
					$new_criteria = array_diff($new_criteria_array, $old_states_array);
					if($new_criteria) {
						// add new criteria into adword
						$this->adword->createLocationCriteria1($this->adword, $campaign['network_campaign_id'], $new_criteria);
					}
                    // get all old satates
					$old_criteria = array_diff($old_states_array, $new_criteria_array);
					if($old_criteria) {
						// remove old criteria from adword
						$this->adword->removeCampaignCriteria($this->adword, $campaign['network_campaign_id'], $old_criteria);
					}
					$criteria_list = implode("," , $new_criteria_array);
					$this->CI->V2_campaign_network_criteria_rel_model->update_value_by_id($old_states_row_id, $criteria_list);
					//remove from our db network-criteri-rel table and update new values
				} else {
                    $criteria_list = implode("," , $new_criteria_array); // hanel verev
                    $this->adword->createLocationCriteria1($this->adword, $campaign['network_campaign_id'], $new_criteria_array);
                    $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $criteria_list, $type);
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
						$this->adword->removeCampaignCriteria($this->adword, $campaign['network_campaign_id'], $old_criteria_array);
						$this->CI->V2_campaign_network_criteria_rel_model->delete_by_id($old_criterion_row['id']);
						//remove from our db network-criteri-rel table
					} elseif($old_criterion_row['criteria_type']=='state') {
						$old_criteria_array = explode(",", $old_criterion_row['criteria_value']);
						// remove from adword add new func remove campaign criteria
						$this->adword->removeCampaignCriteria($this->adword, $campaign['network_campaign_id'], $old_criteria_array);
						$this->CI->V2_campaign_network_criteria_rel_model->delete_by_id($old_criterion_row['id']);
						//remove from our db network-criteri-rel table
					} else {
						//remove all old criteria then add all new
                        $old_postal = $old_criterion_row['criteria_network_value'];
                        $old_postal_row_id = $old_criterion_row['id'];
					}
				}

                if($old_postal) {
                    $old_postal_array = explode(",", $old_postal);
                    // remove old criteria from adword
                    $this->adword->removeCampaignCriteria($this->adword, $campaign['network_campaign_id'], $old_postal_array);
                }
                //if($campaign['zip']) {
                    // add new criteria into adword
                    // check if postaalcode is mutching
                    //$new_criteria_array = explode(",", $campaign['zip']);
                    // get criteria ids from adword and save into db
                $proximity_array = $this->adword->createProximityCriteria1($this->adword, $campaign['network_campaign_id'], $new_criteria_array, $campaign['radius']);
                    //update criterion rel table
                //}

                $proximity_criteria_list = implode("," , $proximity_array);
                if($old_postal_row_id) {
                    $this->CI->V2_campaign_network_criteria_rel_model->update_network_value_by_id($old_postal_row_id, $campaign['zip'], $proximity_criteria_list);
                } else {
                    $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $campaign['zip'], $type, $proximity_criteria_list);
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
            $keywords_response = $this->adword->createKeywords($this->adword, $this->live_group->id, $keywords_array);
            if($keywords_response['message']){
                $this->CI->load->model('V2_log_model');
                $this->V2_log_model->create($campaign['id'], 'keyword error '.$keywords_response['message'], 'keyword');
                return false;
                // save in log table
            } else {
                //$keywords[] = $campaign['keywords'];
                $keyword_ids_list = implode(',',$keywords_response['result']);
            }

            if($keyword_ids_list) {
                $criteria_list = $campaign['keywords'];
                $criteria_network_list = $keyword_ids_list;
                $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $criteria_list, $type, $criteria_network_list);
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
            if ($old_keywords) {
                // remove old keyword from group
                // get old keyword id
                $old_list_array = explode(",", $old_keywords['criteria_value']);

                $old_network_list_array = explode(",", $old_keywords['criteria_network_value']);

                //$key = array_search($old_keyword, $old_list_array);

//                if ($key && $old_network_list_array[$key]) {
//                    $keyword_response = $this->adword->removeCriterion($this->adword, $ad['network_group_id'], $old_network_list_array[$key]);
//                }
                foreach($old_network_list_array as $list) {
                    $keyword_response = $this->adword->removeCriterion($this->adword, $group['network_group_id'], $list);
                }

                $keywords_response = $this->adword->createKeywords($this->adword, $group['network_group_id'], $new_keywords_array);

                if($keywords_response['message']){
                    return $keywords_response['message'];
                } else {
                    //$keywords[] = $campaign['keywords'];
                    $keyword_ids_list = implode(',',$keywords_response['result']);
                }

                // add new keyword to existing keywords list and update it by id
//                $existing_keywords['criteria_value'] = $existing_keywords['criteria_value'] . ',' . $ad['keywords'];
//                $existing_keywords['criteria_network_value'] = $existing_keywords['criteria_network_value'] . ',' . $keyword_response['result'];

                $this->CI->V2_campaign_network_criteria_rel_model->update_network_value_by_id($old_keywords['id'], $campaign['keywords'], $keyword_ids_list);

            } else {

                $keywords_response = $this->adword->createKeywords($this->adword, $group['network_group_id'], $new_keywords_array);

                if($keywords_response['message']){
                    return $keywords_response['message'];
                } else {
                    //$keywords[] = $campaign['keywords'];
                    $keyword_ids_list = implode(',',$keywords_response['result']);
                }
                // save keyword criteria for current campaign in db
                $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $campaign['keywords'], $type, $keyword_ids_list);
            }
        }

        return $keywords_response;

    }


    public function update_end_date($campaign) {

        return $this->adword->updateCampaignEndDate($this->adword, $campaign['network_campaign_id'], $campaign['campaign_end_datetime']);
    }

    public function update_budget($campaign) {
        if($campaign['is_multiple']=='Y') {
            $this->CI->load->model("V2_multiple_campaign_model");
            $multiple_campaign = $this->CI->V2_multiple_campaign_model->get_by_campaign_id(null,$campaign['id']);
            $campaign['budget'] = $multiple_campaign['budget'];
            $campaign['network_campaign_id'] = $multiple_campaign['network_campaign_id'];
        }
        return $this->adword->updateCampaignBudget($this->adword, $campaign['network_campaign_id'], $campaign['budget']);
    }

	public function get_demographics_report() {

        $campaigns = $this->CI->V2_master_campaign_model->get_active_campaigns_demographics_count($this->network_id);

        $age_response = $this->adword->getActiveCampaignsAgeReport($this->adword, null, 'XML');
        $gender_response = $this->adword->getActiveCampaignsGenderReport($this->adword, null, 'XML');

        //if($response['message']) {
            // save error message into db and return false
//			$error_data_for_update['disapproval_reasons'] = $response['message'];
//			$error_data_for_update['campaign_status'] = 'DISAPPROVED';
            //$this->CI->V2_master_campaign_model->update($campaign['id'], $error_data_for_update);
          //  return false;
        //}

        if($age_response['result']) {

            foreach($age_response['result'] as $row) {
                $age_repo = (array)$row;
                $reset_age_repo = reset($age_repo);

                $age_reports[$reset_age_repo['campaignID']][$reset_age_repo['ageRange']] = $reset_age_repo;
            }
        }

        if($gender_response['result']) {
            foreach($gender_response['result'] as $row) { //var_dump(count($gender_response['result']));
                $gender_repo = (array)$row;
                $reset_gender_repo = reset($gender_repo);

                $gender_reports[$reset_gender_repo['campaignID']][$reset_gender_repo['gender']] = $reset_gender_repo;
            }
        }

        //adds the campaign demographics report into the database
        $this->CI->load->model('V2_demographics_reporting_model');

        foreach($campaigns as $campaign){
            if( $age_reports[$campaign['network_campaign_id']] && $gender_reports[$campaign['network_campaign_id']] ) {

                $age_report = $age_reports[$campaign['network_campaign_id']];
                $gender_report = $gender_reports[$campaign['network_campaign_id']];

                $data_for_update = array();
                $data_for_update['network_id'] = $campaign['network_id'];
                $data_for_update['campaign_id'] = $campaign['id'];
                $data_for_update['type'] = 'CLICK';
                $data_for_update['network_campaign_id'] = $campaign['network_campaign_id'];
                $data_for_update['created_date'] = date("Y-m-d H:i:s");

                $count_18_24 = (int)$age_report['18-24']['clicks'] - (int)$campaign['18_24_count'];
                if($count_18_24 >=0 ) {
                    $data_for_update['18_24'] = $count_18_24;
                }

                $count_25_34 = (int)$age_report['25-34']['clicks'] - (int)$campaign['25_34_count'];
                if($count_25_34 >=0 ) {
                    $data_for_update['25_34'] = $count_25_34;
                }

                $count_35_44 = (int)$age_report['35-44']['clicks'] - (int)$campaign['35_44_count'];
                if($count_35_44 >=0 ) {
                    $data_for_update['35_44'] = $count_35_44;
                }

                $count_45_54 = (int)$age_report['45-54']['clicks'] - (int)$campaign['45_54_count'];
                if($count_45_54 >=0 ) {
                    $data_for_update['45_54'] = $count_45_54;
                }

                $count_55_64 = (int)$age_report['55-64']['clicks'] - (int)$campaign['55_64_count'];
                if($count_55_64 >=0 ) {
                    $data_for_update['55_64'] = $count_55_64;
                }

                $count_64 = (int)$age_report['65 or more']['clicks'] - (int)$campaign['64_count'];
                if($count_64 >=0 ) {
                    $data_for_update['64'] = $count_64;
                }

                $count_unknown_age = (int)$age_report['Undetermined']['clicks'] - (int)$campaign['unknown_age_count'];
                if($count_unknown_age >=0 ) {
                    $data_for_update['unknown_age'] = $count_unknown_age;
                }

                $count_male = (int)$gender_report['Male']['clicks'] - (int)$campaign['male_count'];
                if($count_male >=0 ) {
                    $data_for_update['male'] = $count_male;
                }

                $count_female = (int)$gender_report['Female']['clicks'] - (int)$campaign['female_count'];
                if($count_female >=0 ) {
                    $data_for_update['female'] = $count_female;
                }

                $count_unknown_gender = (int)$gender_report['Undetermined']['clicks'] - (int)$campaign['unknown_gender_count'];
                if($count_unknown_gender >=0 ) {
                    $data_for_update['unknown_gender'] = $count_unknown_gender;
                }

                if(count($data_for_update)>5) {
                    $this->CI->V2_demographics_reporting_model->create($data_for_update);
                }
            }
        }
        return true;
	}

    public function get_ads_impressions() {
		// get all ads by campaign_id
		$ads = $this->CI->V2_ad_model->get_active_campaigns_ads_by_network_id($this->network_id);
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
                if($report["adID"]==$ad['network_creative_id'] ){
                    $data_for_update = array();
                    $data_for_update['ad_id'] = $ad['id'];
                    $data_for_update['network_id'] = $this->network_id;
                    $data_for_update['campaign_id'] = $ad['campaign_id'];
                    $data_for_update['impressions_count'] = (int)$report["impressions"] - (int)$ad['impressions_count'];
					if($data_for_update['impressions_count'] && (int)$report["impressions"]) {
                        if($ad['campaign_id']==311) {
                            var_dump($report["impressions"], $ad['impressions_count'],$ad['campaign_id'], $data_for_update['impressions_count']);
                        }
                        if($data_for_update['ad_id']==1602) {
                            $data_for_update['impressions_count'] = $data_for_update['impressions_count'] + 8813;
                        } elseif($data_for_update['ad_id']==1603)  {
                            $data_for_update['impressions_count'] = $data_for_update['impressions_count'] + 8028;
                        } elseif($data_for_update['ad_id']==1604) {
                            $data_for_update['impressions_count'] = $data_for_update['impressions_count'] + 9538;
                        }
                        $this->CI->V2_campclick_impression_model->create($data_for_update);
					}
				}
            }
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
		// get all ads by campaign_id
		$campaigns = $this->CI->V2_master_campaign_model->get_active_campaigns_by_network_id($this->network_id);
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
                    $data_for_update['campaign_id'] = $campaign['id'];
                    $data_for_update['cost'] = $report['cost']/1000000;
					if($data_for_update['cost']) {
						$this->CI->V2_campaign_cost_model->create($data_for_update);
					}
                }
            }
        }
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
                        if($five_place['criteriaDisplayName']=='hqapps.net') {
                            $five_place['criteriaDisplayName'] = 'glamour.com';
                        }
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
		$response = $this->adword->updateGroupBid($this->adword, $group['network_group_id'], $campaign['bid'], $isCpc);

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

        $audience = $this->adword->addAudience($this->adword, $campaign['io']);

        $this->CI->Userlist_io_model->create_userlist_io($campaign['io'], $campaign['id'], $audience['userList']->id, htmlspecialchars($audience['code']->snippet), $this->network_id, $campaign['userid']);

        return array('snippet'=>htmlspecialchars($audience['code']->snippet),'remarketing_list_id'=>$audience['userList']->id, 'io'=>$campaign['io'], 'network'=>'GOOGLE');

    }

}