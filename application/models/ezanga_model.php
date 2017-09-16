<?php 

class Ezanga_model extends CI_Model	{

	private $username = "jason@prodatafeed.com";
	private $password = "safedata123";
	private $apikey = "52cd3121bf4ac87dc04cd126b88daad8";
	
	protected $CI;

	public function __construct()	{
	    parent::__construct();
	    $this->CI =& get_instance();
	    $this->CI->load->database();
	    $this->CI->load->library("user_agent");
	    $this->CI->load->library("Ezanga");
	    
	    $this->CI->load->model("Domains_model");
	    $this->CI->load->model("Campclick_model");
	    $this->CI->load->model("Ad_model");
	    $this->CI->load->model("Log_model");
	
	    $this->CI->load->library("Clickcap");
	    
	    require_once(dirname(__FILE__) . "/adpad_api.php");
	    
	    // Set our API credentials
	    $this->CI->Ezanga->$api_key = "52cd3121bf4ac87dc04cd126b88daad8";
	    $this->CI->Ezanga->$username = "jason@prodatafeed.com";

	}
	
	public function create_ad($io = "", $ad_id = 0)    {
	    if ($io == "")
	        throw new exception("io required");
	    
	    if ($ad_id == 0)
	        throw new exception("ad required");
	    
	    $this->CI->Ad_model->id = $ad_id;
	    $ad = $this->CI->Ad_model->get_by_id();
	    
	    $category = $this->CI->Ad_model->lookup_category($ad['category']);
	    
	    $parameters = array(
	        /* Timestamps must be passed as YYYY-MM-DD HH:MM:SS */
	        "cp_start_timestamp" => date("Y-m-d H:i:s"),
	        "cp_stop_timestamp" => date("Y-m-d H:i:s", strtotime("+30 days")),
	        "cp_name" => $ad['campaign_name'], // Campaign Name
	        "cp_customer_status" => "enabled", // Campaign Status
	        "cp_daily_budget" => 4.00, // Budget ($1,000.00 in this case)
	        "cp_max_cpc" => $ad['bid'], // Cost Per Click ($0.25)
	        "media_type" => "ppc", // PPC based campaign
	        /* Geotargeting notes:
	         ONLY set values you need. For example if you set a country code of US and a ZIP code. You will get traffic from ALL of the US.
	    If you just want to target by zip. Pass nothing in any of the geo parameters EXCEPT the zip codes.
	    */
	        "geocc" => "",
	        "geostates" => "",
	        "geocities" => "",
	        "geozipcodes" => "", // Target by zip code (seperated by commas)
	        "geodmacodes" => "",
	        "ca_geostates" => "",
	        "ca_geocities" => "",
	        "ca_geozipcodes" => "",
	        "au_geocities" => "",
	        "au_geostates" => "",
	        "adgroup_name" => $ad['campaign_name'],
	        "adgroup_adult_flag" => 0,
	        "adv_keywords" => "", // Keywords seperated by comma (all keywords)
	        "adv_title" => $ad['title'], // Ad title
	        "adv_description" => $ad['description'], // Ad description
	        "adv_visible_url" => $ad['display_url'], // Ad display URL
	        "adv_click_url" => $ad['destination_url'], // Ad click URL
	        /* We are doing a PPC campaign so no need for phone information, leave these blank but they must be passed */
	        "phn_name" => "",
	        "phn_address" => "",
	        "phn_city" => "",
	        "phn_state" => "",
	        "phn_zip" => "",
	        "phn_phone_number" => ""
	    );
	    
	    $result = $this->CI->Ezanga->validate_and_request("campaigns","wizard",$parameters);
	    
	    if(count($this->CI->Ezanga->$errors) > 0) {
	        return false;
	    } else {
	        return $result->rows->cid;
	    }
	}
	
	/**
	 * List All Ads from Ezanga
	 * @param string $status (enabled, disabled, deleted)
	 */
	public function get_all_ads($status = "enabled")	{
	    $parameters = array(
	        "filterflags" => $status // enabled, disabled, deleted
	    );
	    
	    $result = $this->CI->Ezanga->validate_and_request("adgroups","list",$parameters);

	    return $result;
	}
	
	public function pause_ad($id = "")	{
		if ($id == "")
			throw new exception("id required");
		
		$parameters = array(
		    "campaignid" => $id,
		    "customer_status" => "disabled"
		);

		$result = $this->CI->Ezanga->validate_and_request("campaigns","update",$parameters);
		
		return $output;
	}
	
	public function resume_ad($id = "")	{
	    if ($id == "")
	        throw new exception("id required");
	
	    $parameters = array(
	        "campaignid" => $id,
	        "customer_status" => "enabled"
	    );

	    $result = $this->CI->Ezanga->validate_and_request("campaigns","update",$parameters);

	    return $output;
	}
	
	public function mark_ads_completed()	{
		$ads = $this->get_all_ads();
		
		$this->CI->load->library('email');
    	
		$config['protocol'] = 'sendmail';
    	$config['mailpath'] = '/usr/sbin/sendmail';
    	$config['charset'] = 'utf-8';
    	$config['wordwrap'] = TRUE;
    	$config['mailtype'] = 'html';
    	$config['priority'] = 1;
		
    	$this->CI->email->initialize($config);
		
    	$msg = "";
		foreach($ads as $a)	{
		    $all_links_fulfilled = false;
			list($junk, $io) = explode("/r/", $a->Ad->url, 2);
			
			if ($io == "DR2245")
				continue;
			
			$this->CI->Campclick_model->io = $io;
			$id = $this->CI->Campclick_model->get_campaign_id_from_io();
			$this->CI->Campclick_model->id = $id;
			$campaign = $this->CI->Campclick_model->get_campaign();
			$click_count = $this->CI->Campclick_model->get_campaign_clicks();
			$impression_count = $this->CI->Campclick_model->get_campaign_impressions();

			if ($campaign['fire_open_pixel'] == "Y")    {
			    $count = (int)$impression_count;
			} else {
			    $count = (int)$click_count;
			}
			
			$all_links_fulfilled = $this->CI->Campclick_model->all_links_fulfilled();
			
			//if (($count >= $campaign['max_clicks'] && $count > 0 && $campaign['max_clicks'] > 0) or ($all_links_fulfilled === true))	{
			if ($count >= $campaign['max_clicks'] && $count > 0 && $campaign['max_clicks'] > 0)	{
				$status = $this->pause_ad($a->Ad->id);
				
				if ($status->paused != 1)	{
					$msg .= "UNABLE TO PAUSE CAMPAIGN: {$io}<br/>";
					$msg .= "IO: {$io}, AD_ID: {$a->Ad->id}<br/>";
					$msg .= "Camp Name: {$a->Ad->campaign_name}<br/>";

					$this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
					$this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
					$this->CI->email->subject("Report-Site: Pause Error {$io}");
					$this->CI->email->message($msg);
					$this->CI->email->send();
				} else {
					$msg = "IO: ({$io}) {$a->Ad->campaign_name}<br/>";
					$msg .= "Total Clicks: {$count}<br/>";
					$msg .= "Ordered Clicks: {$campaign['max_clicks']}<br/>";

					$this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
					$this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
					$this->CI->email->subject("Report-Site: Campaign Complete {$io}");
					$this->CI->email->message($msg);
					$this->CI->email->send();

					$this->CI->Campclick_model->campaign_complete();
				}
			}
		}
		
		// Mark the manual ads completed (e.g. bundle associatiates, manual process ads, and Other-vendor not using API-model ads)
		$this->CI->Campclick_model->mark_ads_completed();
	}

	public function mark_ads_processing()	{
		$scheduled = $this->Campclick_model->get_campaign_list(null, "Y", "AND campaign_is_started='N' AND campaign_is_complete='N'");
		
		foreach($scheduled as $s)	{
			$this->Campclick_model->io = $s['io'];
			$id = $this->Campclick_model->get_campaign_id_from_io();
			$this->Campclick_model->id = $id;
			$campaign = $this->Campclick_model->get_campaign();
			$count = $this->Campclick_model->get_campaign_clicks();
			
			// mark the campaign as processing
			if ($count > 25 && date("Y-m-d H:i:s") >= $campaign['campaign_start_datetime'])	{
				$this->Campclick_model->campaign_start();
			}
		}
	}
	
	public function set_bid($id = "", $bid = 0.0025)  {
	    if (! $bid > 0)  {
	        throw new exception("set_bid: Invalid Bid");
	    }
	    
	    $parameters = array(
	        "campaignid" => $id,
	        "cp_max_cpc" => $bid
	    );
	    
	    $result = $this->CI->Ezanga->validate_and_request("campaigns","update",$parameters);
	}
	
	public function get_ad($id = "")   {
	    if ($id == "")
	        throw new exception("id required");
	    
	    $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/getAd&id={$id}";
	    
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	    curl_setopt($ch, CURLOPT_USERAGENT, "api");
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	    $output = curl_exec($ch);
	    
	    return $output;
	}
	
	public function set_target($id = "", $target = "", $targetType = "country")   {
	    if ($id == "")
	        throw new exception("set_target: invalid ID");
	    
	    if ($target == "")
	        throw new exception("set_target: invalid target");
	   
	    if ($targetType == "")
	        throw new exception("set_target: invalid targetType");

	    
	    $params['campaignid'] = $id;
	    
	    switch(strtoupper($targetType))    {
	        case "COUNTRY":
	            $params['geocc'] = $target;
	            break;
	            
	        case "POSTALCODE":
	            $params['geozipcodes'] = implode(",", $target);
	            break;
	            
	        case "STATE":
	            $params['geostates'] = $target;
	            break;
	    }
	    
	    $result = $this->CI->Ezanga->validate_and_request("campaigns","geotargeting",$params);

	    return $output;
	}
	
	public function set_cap($id = "", $cap_amount = 0) {
	    if ($id == "")
	        throw new exception("set_cap: invalid ID");
	    
	    if (! $cap_amount > 0)
	        throw new exception("set_cap: invalid cap_amount");
	    
	    $parameters = array(
	        "campaignid" => $id,
	        "cpc_daily_budget" => $cap_amount
	    );
	     
	    $result = $this->CI->Ezanga->validate_and_request("campaigns","update",$parameters);
	    
	}
	
	public function set_schedule($id = "", $schedule = "") {
	    if ($id == "")
	        throw new exception("set_schedule: invalid ID");

	    if ($schedule == "")   {
	        $schedule = "sun[7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]mon[8,9,10,11,12,13,14,15,16,17,18,19,20,21,22]tue[7,8,9,10,11,12,13,15,16,17,19,20,21,23]wed[7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]thu[8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]fri[7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]sat[8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]";
	    }
	    
	    $parameters = array(
	        "campaignid" => $id,
	        "scheduling01" => $schedule
	    );
	    
	    $result = $this->CI->Ezanga->validate_and_request("campaings", "timetargeting", $parameters);
	    
	    return $result;
	}

}