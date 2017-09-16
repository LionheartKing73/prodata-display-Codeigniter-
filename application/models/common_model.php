<?php

class Common_model extends CI_Model	{

	private $network;
	private $network_model;
	protected $CI;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->library("user_agent");
		$this->CI->load->model("Domains_model");
		$this->CI->load->model("Campclick_model");
		$this->CI->load->model("Ad_model");
		$this->CI->load->model("Log_model");
		$this->CI->load->library("Clickcap");

	}

    public function init($network_name) {

        if (!$network_name){
            throw new exception("network_name required");
        }

        //detect network type
        $model_name = strtolower($network_name);
        if($model_name == "google"){
        	$model_name = ucfirst($model_name).'_model_site';
        }else{
        	$model_name = ucfirst($model_name).'_model';
        }
        $this->CI->load->model($model_name);
        $this->network_model = $this->$model_name;

    }

    public function create($campaign)    {


        if (!$campaign){
            throw new exception("campaign required");
        }

        $this->CI->load->library('Send_email');

        $this->init($campaign['network_name']);
        $status = $this->network_model->create($campaign);



        if (in_array($status['status'], ['completed', 'rejected', 'approved'])){

            $function_name = 'send_' . $status['status'];

				$link_edit = 'http://reporting.prodata.media/v2/campaign/edit_campaign/'. $campaign['id'];
                $this->send_email->$function_name($campaign['email'], $campaign['io'], $campaign['name'],$campaign['campaign_type'], $link_edit, $status['message']);
             }
    }

	public function test_emails(){
		$this->CI->load->model('V2_master_campaign_model');
		$campaign = $this->CI->V2_master_campaign_model->get_by_id(null,923);
		$this->CI->load->library('Send_email');
		$status = array('status' => 'completed','message' => '');

		$function_name = 'send_' . $status['status'];
		//$disapproval_reasons = null;
		$link_edit = 'http://reporting.prodata.media/v2/campaign/edit_campaign/'.'308';

		//$link_edit = 'http://reporting.prodata.media/v2/campaign/edit_campaign/'. $campaign['id'];
//		$this->send_email->$function_name('hovhannes.zhamharyan.bw@gmail.com', 'io','name','type', $link_edit, $status['message']);
		$this->send_email->$function_name('Harutyun.Sardaryan.bw@gmail.com', $campaign['io'], $campaign['name'], $campaign['campaign_type'], $link_edit, $status['message']);

//		$this->send_email->$function_name('pitsport@mail.ru', $campaign['io'], $campaign['name'], $campaign['campaign_type'], $link_edit, $status['message']);

	}

    public function update($campaign, $type)    {

        if (!$campaign){
                throw new exception("campaign required");
        }

        $this->init($campaign['network_name']);
        return $this->network_model->update($campaign, $type);
    }

	public function update_keywords($campaign)    {

        if (!$campaign){
                throw new exception("campaign required");
        }

        $this->init($campaign['network_name']);
        $this->network_model->update_keywords($campaign);
    }

    public function create_location_targeting($campaign)    {
        if ($campaign == "")
                throw new exception("campaign required");

        $this->network_model->create_location_targeting($campaign);
    }

    // create update, remove, get active, get_reports (get clicks) function

    public function create_ad($campaign, $ad_id) {


		if (!$campaign){
			throw new exception("campaign required");
		}

	    if (!$ad_id) {
			throw new exception("ad id required");
		}
		$this->init($campaign['network_name']);

		if($campaign['network_name'] == 'FIQ') {
			return $this->network_model->create_ad($campaign, $ad_id);
		} else {
			return $this->network_model->create_ad($ad_id);
		}

	}



    public function update_ad($campaign, $ad_id) {

        if (!$campaign){
            throw new exception("campaign required");
        }

        if (!$ad_id) {
            throw new exception("ad id required");
            }

        $this->init($campaign['network_name']);


		if($campaign['network_name'] == 'FIQ') {
			return $this->network_model->update_ad($campaign, $ad_id);
		} else {

			return $this->network_model->update_ad($ad_id);
		}

    }

    /**
     *
     * @param array $campaign
     * @return update_ad status
     * @throws exception if campaign not foud
     * Call network model pause_campaign function
     */


    public function pause_campaign($campaign) {

        if (!$campaign){
            throw new exception("campaign required");
        }

        $this->init($campaign['network_name']);
        return $this->network_model->pause_campaign($campaign);
    }

	public function update_campaign_status($campaign) {

		$this->init($campaign['network_name']);
		return $this->network_model->update_campaign_status($campaign);
	}

	public function update_ad_status($ad, $network_name) {
		if (!$network_name){
			throw new exception("campaign required");
		}

		if (!$ad) {
			throw new exception("ad required");
		}
		$this->init($network_name);
		return $this->network_model->update_ad_status($ad);

	}

	public function check_ads_approved_status($network_name) {

		$this->init($network_name);
		$this->network_model->check_ads_approved_status();

	}

	public function check_multiple_ads_approved_status($ads, $network_name) {
		if (!$network_name){
			throw new exception("campaign required");
		}

		if (!$ads) {
			throw new exception("ad required");
		}
		$this->init($network_name);
		$this->network_model->check_multiple_ads_approved_status($ads);

	}

	public function update_cap_per_hour($campaign) {

		$this->init($campaign['network_name']);
		$this->network_model->update_cap_per_hour($campaign);

	}

	public function update_schedule($ad, $network_name) {

		$this->init($network_name);
		return $this->network_model->update_schedule($ad);

	}

	public function update_bid($campaign) {

		$this->init($campaign['network_name']);
		return $this->network_model->update_bid($campaign);
	}

	public function update_daily_cap($ad, $network_name) {

		$this->init($network_name);
		return $this->network_model->update_daily_cap($ad);

	}

	public function get_ad_report($ad, $network_name, $sdate = "", $edate = "") {

		$this->init($network_name);
		return $this->network_model->get_ad_report($ad, $sdate, $edate);

	}

	public function get_demographics_report($network_name) {

		$this->init($network_name);
		echo $network_name;
		return $this->network_model->get_demographics_report();

	}

	public function get_campaigns_cost($network_name) {

		$this->init($network_name);
		return $this->network_model->get_campaigns_cost();

	}

	public function get_placements_report($network_name) {

		$this->init($network_name);
		return $this->network_model->get_campaigns_placements_report();

	}

	public function get_ads_impressions($network_name) {

		$this->init($network_name);
		return $this->network_model->get_ads_impressions();

	}

    public function get_multiple_campaigns_cost($network_name) {

		$this->init($network_name);
		return $this->network_model->get_multiple_campaigns_cost();

	}

	public function get_multiple_ads_impressions($network_name) {

		$this->init($network_name);
		return $this->network_model->get_multiple_ads_impressions();

	}

	/**
	 * List All Ads from FIQ
	 * @param string $status (active, paused)
	 */
	public function get_all_ads($status = "active")	{

	}

	public function pause_ad($id = "")	{

	}

	public function resume_ad($id = "")	{

	}

	public function set_bid($id = "", $bid = 0.0025)  {
	    if (! $bid > 0)  {
	        throw new exception("set_bid: Invalid Bid");
	    }

	    $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/setBid&id={$id}";

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	    curl_setopt($ch, CURLOPT_USERAGENT, "api");
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, array("bid" => $bid));
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	    $output = curl_exec($ch);
	    curl_close($ch);
	}

	public function get_ad($id = "")   {
	    if ($id == "")
	        throw new exception("id required");
	}

	public function set_target($id = "", $targets = array("US"))   {
	    if ($id == "")
	        throw new exception("set_target: invalid ID");

	    if (empty($targets))
	        throw new exception("set_target: invalid target");

	}

	public function set_cap($id = "", $cap_amount = 0) {
	    if ($id == "")
	        throw new exception("set_cap: invalid ID");

	    if (! $cap_amount > 0)
	        throw new exception("set_cap: invalid cap_amount");
	}

	public function set_schedule($id = "", $schedule = "") {
	    if ($id == "")
	        throw new exception("set_schedule: invalid ID");

	}

	public function get_ad_reports()    {

	}

	public function create_audience($network_name, $campaign) {

		$this->init($network_name);
		return $this->network_model->create_audience($campaign);
	}

	public function get_campaigns_video_report($network_name) {

		$this->init($network_name);
		return $this->network_model->get_campaigns_video_report();
	}

	public function get_campaigns_leads($network_name, $ads) {

		$this->init($network_name);
		return $this->network_model->get_campaigns_leads($ads);
	}

	/**
	 * Add Campaign info to redis and its associated Ads
	 *
	 * @param  integer $campaign_id
	 * @return Void
	 *
	 * Usage:
	 * ------
   	 * $this->load->model("common_model");
     * $this->common_model->add_campaign_and_assoc_ads_to_redis($campaign_id = 2448);
	 */
    public function add_campaign_and_assoc_ads_to_redis($campaign_id, array $pretargeting = []) {

    	$this->load->library('clickcap');
        $this->load->model("V2_ad_model");
        $this->load->model("V2_master_campaign_model");
        $this->load->model('V2_campaign_cost_model');
        $this->load->model('v2_campaign_category_model');
        $this->load->model('v2_prodata_id_retargeting_model');

        // short circuit this if we have a match already in the system.
        $r = $this->clickcap->campaign_exists($campaign_id);
        if ($r[0] > 0) {
            return array('status' => false, 'pretargeting' => $pretargeting);
        }

        $ads = $this->V2_ad_model->get_active_campaigns_ads($campaign_id);
        $campaign = $this->V2_master_campaign_model->get_active_campaigns_for_redis_with_cost($campaign_id);
        $campaign = array_pop($campaign);

        $campaign['spend_today'] = $this->V2_campaign_cost_model->get_daily_cost_by_campaign_id($campaign['id']);
        $campaign['ads'] = json_encode(array_column($ads, 'id'));

        // Get associated IAB Categories of a campaign
        $iab_categories = "";
        $assoc_iab_categories = $this->v2_campaign_category_model->get_associated_iab_categories_by_campaign_id($campaign['id']);
        if ( !empty($assoc_iab_categories) ) {
            $iab_categories = array_column($assoc_iab_categories, 'iab_category_id');
        }
        $campaign['iab_categories'] = json_encode($iab_categories);

        /**
         * Get Retargeting IP association and save to Redis
         * Redis Key is: campaignRetargetingIPs
         */
        $retargeting_ips = "";
        $assoc_retargeting_ips = $this->v2_retargeting_ip_model->get_associated_retargeting_ips_by_campaign_id($campaign['id']);
        if ( !empty($assoc_retargeting_ips) ) {
            $retargeting_ips = json_encode($assoc_retargeting_ips);
        }
        $campaign['retargeting_ips'] = $retargeting_ips;

        // Save Campaign to Redis!!
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


        /**
         * Prepare Ad and Load to Redis
         */
        foreach( $ads as $k => $ad ) {

            // cache ads dimensions for pretargeting config
            $wh = $ad['creative_width'] . 'x' . $ad['creative_height'];
            if ( !in_array($wh, $pretargeting['whs']) ) {
                $pretargeting['whs'][] = $wh;
                $pretargeting['dims'][] = ['width' => $ad['creative_width'], 'height' => $ad['creative_height']];
            }

            // Set IAB categories of Campaign to ad object
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

            if ( $ad['zip'] == "" && $ad['state'] == "" ) {
                $ad['beacon_url'] = base_url().'tracking/beacon/'.$ad['campaign_id'].'/'.$ad['id'];

                // FIX: use http for beacon_url instead of https protocol
                $ad['beacon_url'] = str_replace('https', 'http', $ad['beacon_url']);

                if($ad['radius']==0) {
                    $ad['radius']='';
                }
                $this->clickcap->set_ad($ad);
            } else {
                $ad['beacon_url'] = base_url().'tracking/beacon/'.$ad['campaign_id'].'/'.$ad['id'];

                // FIX: use http for beacon_url instead of https protocol
                $ad['beacon_url'] = str_replace('https', 'http', $ad['beacon_url']);

                // this is state and/or zip code
                if ( $ad['zip'] != "" ) {
                    if( $ad['radius'] && $ad['radius'] <= 50 ) {
                        $ad['radius'] = 75;
                    }
                    $ad['zip'] = str_replace(",", "|", $ad['zip']);
                    $this->clickcap->set_ad($ad);
                } else if ( $ad['state'] != "" ) {
                    $ad['state'] = str_replace(",", "|", $ad['state']);
                    $this->clickcap->set_ad($ad);
                } else {
                    // shouldn't get here!
                }
            }
        }

        /**
         * Loads Ads Geo Mapping to Redis
         */
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
                        $common_zips = array_intersect($final_zips, $all_zips);
                        $common_zips[] = $zip;
                        $common_zips = array_unique($common_zips);
                        if ( !empty($common_zips) ) {
                            $geo_keys_tmp = array_map(function($zip) use ($state) {
                                return $zip . '-' . $state;
                            }, $common_zips);
                            $geo_keys = array_merge($geo_keys, $geo_keys_tmp);
                            $cache_final_zips = array_diff($final_zips, $common_zips);
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

        return array('status' => true, 'pretargeting' => $pretargeting);
    }

    /**
     * Remove A Campaign by ID and its associated Ads record from Redis
     * @param  integer $campaign_id [description]
     * @return void
     *
     * Usage:
     * ------
	 * $this->load->model("common_model");
     * $this->common_model->remove_campaign_and_assoc_ads_from_redis($campaign_id);
     */
    public function remove_campaign_and_assoc_ads_from_redis($campaign_id)
    {
    	$this->load->library('clickcap');
        $this->load->model("V2_ad_model");
        $this->load->model("V2_master_campaign_model");
        $this->load->model('V2_campaign_cost_model');
        $this->load->model('v2_campaign_category_model');
        $this->load->model('v2_prodata_id_retargeting_model');

        $campaign = $this->V2_master_campaign_model->get_active_campaigns_for_redis_with_cost($campaign_id);
        $campaign = array_pop($campaign);

        $ads = $this->V2_ad_model->get_active_campaigns_ads($campaign_id);
        $ad_ids = array_column($ads, 'id');

        // delete campaign
        $this->clickcap->del_item($db = 5, $key = $campaign['id']);

        // delete associated Ads
        foreach ( $ad_ids as $ad_id ) {
        	// Remove the ad
        	$this->clickcap->del_item($db = 7, $key = $ad_id);

        	// delete Ad's Geo Mappings
	        $cursor = 0;
	        while (true) {
	        	$res = $this->clickcap->get_ads_geo_map($cursor, $match = "{$ad_id}-", $count = 10000);
	        	$cursor = $res[0];
	        	if ( empty($res[1]) ) {
	        		break;
	        	}

	        	$ad_keys = array_keys($res[1]);
	        	$this->clickcap->remove_ads_geo_maps_by_key($ad_keys);
	        }
        }
    }
}
