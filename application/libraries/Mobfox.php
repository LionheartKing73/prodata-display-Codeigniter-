<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."third_party/predis/autoload.php";

class Mobfox	{
    
    private $apikey = "894e102a95214fa70ae54e7d6f9b3a86";
    private $period = "yesterday"; // today,. yesterday, week_to_day, month_to_day, last_month
    private $from;
    private $to;
    private $tz = "America/New_York"; // EST or UTC
    private $group = "ex_buyerseat";
    private $totals = "total_served,total_impressions,total_clicks,total_ex_bids,total_ex_bid_requests,total_ex_nobids,total_ex_bids_won,ex_total_impression_price"; // total_requests, total_served, total_impressions, total_clicks, total_earnings, fill_rate, render_rate, ctr
    private $timegroup = "interval"; // month, week, day

	public function __construct()	{
	}

	public function getIP()	{
		$this->client = new Predis\Client();

		return $this->client->get($this->ipaddress . $this->io);
	}

	
	public function report()   {
	    $url = "http://api-v2.mobfox.com/dsppartner/report?apikey={$this->apikey}&tz={$this->tz}&timegroup={$this->timegroup}&period={$this->period}&group={$this->group}&totals={$this->totals}";
	    $timeout = 2;
	    
	    print $url;
	    
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    
	    $response = json_decode(curl_exec($ch));
	    
	    print_r($response);
	}

	public function __get($name)	{
		return $this->$name;
	}

	public function __set($name, $value)	{
		$this->$name = $value;
	} 
}

?>