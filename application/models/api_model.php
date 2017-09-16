<?php 

class api_model extends CI_Model	{

	private $username = "safedata";
	private $password = "safedata123";
	
	protected $CI;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->library("user_agent");
		$this->CI->load->model("Domains_model");
		$this->CI->load->model("Campclick_model");
	}
	
	/**
	 * List All Ads from FIQ
	 * @param string $status (active, paused)
	 */
	public function get_all_ads($status = "active")	{
		$url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/getAds&status={$status}";
		
		$output = json_decode($this->sendRequest($url));
		
		return $output;
	}
	
	public function pause_ad($id = "")	{
		if ($id == "")
			throw new exception("id required");
		
		$url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/pause&id={$id}";

		$output = json_decode($this->sendRequest($url));
		
		return $output;
	}
	
	public function mark_ads_completed()	{
		$ads = $this->get_all_ads();
		
		$this->load->library('email');
    	
		$config['protocol'] = 'sendmail';
    	$config['mailpath'] = '/usr/sbin/sendmail';
    	$config['charset'] = 'utf-8';
    	$config['wordwrap'] = TRUE;
    	$config['mailtype'] = 'html';
    	$config['priority'] = 1;
		
    	$this->email->initialize($config);
		
    	$msg = "";
		foreach($ads as $a)	{
			list($junk, $io) = explode("/r/", $a->Ad->url, 2);
			
			if ($io == "DR2245")
				continue;
			
			$this->Campclick_model->io = $io;
			$id = $this->Campclick_model->get_campaign_id_from_io();
			$this->Campclick_model->id = $id;
			$campaign = $this->Campclick_model->get_campaign();
			$count = $this->Campclick_model->get_campaign_clicks();

			if ($count >= $campaign['max_clicks'] && $count > 0 && $campaign['max_clicks'] > 0)	{
				$status = $this->pause_ad($a->Ad->id);
				
				if ($status->paused != 1)	{
					$msg .= "UNABLE TO PAUSE CAMPAIGN: {$io}<br/>";
					$msg .= "IO: {$io}, AD_ID: {$a->Ad->id}<br/>";
					$msg .= "Camp Name: {$a->Ad->campaign_name}<br/>";

					$this->email->from('noreply@report-site.com', 'Report-Site No Reply');
					$this->email->to('jkorkin@safedatatech.onmicrosoft.com');
					$this->email->subject("Report-Site: Pause Error {$io}");
					$this->email->message($msg);
					$this->email->send();
				} else {
					$msg = "IO: ({$io}) {$a->Ad->campaign_name}<br/>";
					$msg .= "Total Clicks: {$count}<br/>";
					$msg .= "Ordered Clicks: {$campaign['max_clicks']}<br/>";

					$this->email->from('noreply@report-site.com', 'Report-Site No Reply');
					$this->email->to('jkorkin@safedatatech.onmicrosoft.com');
					$this->email->subject("Report-Site: Campaign Complete {$io}");
					$this->email->message($msg);
					$this->email->send();

					$this->Campclick_model->campaign_complete();
				}
			}
		}
		
		// Mark the manual ads completed (e.g. bundle associatiates, manual process ads, and Other-vendor not using API-model ads)
		$this->Campclick->mark_ads_completed();
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
	    
	    $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/setBid&id={$id}";
	    
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	    curl_setopt($ch, CURLOPT_USERAGENT, "api");
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, array("bid" => $bid));
	    $output = curl_exec($ch);
	    curl_close($ch);
	}
	
	public function get_ad($id = "")   {
	    if ($id == "")
	        throw new exception("id required");
	    
	    $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/getAd&id={$id}";
	    
	    $output = json_decode($this->sendRequest($url));
	    
	    return $output;
	     
	}
	
	public function set_target($id = "", $targets = array("US"))   {
	    if ($id == "")
	        throw new exception("set_target: invalid ID");
	    
	    if (empty($targets))
	        throw new exception("set_target: invalid target");
	    
	    $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/setTargeting&id={$id}";
	    
	    $poststring = "";
	    foreach($targets as $t)    {
	        $poststring .= "targets[]={$t}&";
	    }
	    
	    $poststring = trim($poststring, "&");
	    
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	    curl_setopt($ch, CURLOPT_USERAGENT, "api");
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $poststring);

	    $output = curl_exec($ch);
	    
	    curl_close($ch);
	     
	    return $output;
	}
	
	public function set_cap($id = "", $cap_amount = 0) {
	    if ($id == "")
	        throw new exception("set_cap: invalid ID");
	    
	    if (! $cap_amount > 0)
	        throw new exception("set_cap: invalid cap_amount");
	    
	    $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/setCap&id={$id}";
	    
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	    curl_setopt($ch, CURLOPT_USERAGENT, "api");
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, array("cap" => $cap_amount));
	    
	    $output = curl_exec($ch);
	    curl_close($ch);

	    print_r($output);
	    
	    return $output;
	     
	}

	private function sendRequest($url = "")	{
		if ($url == "")
			throw new exception("url required");
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERAGENT, "api");
		$output = curl_exec($ch);
		curl_close($ch);
		
		return $output;
	}

}