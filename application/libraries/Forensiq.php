<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Forensiq	{

    private $ORG_ID = "wDQXmhQGvRlxsg5kIyo8";
    private $API_KEY = "vq5NifIBK870PcNyi5Jc";
    private $format = "JSON";
    
    // MUST BE SET FOR USAGE WITH THIS LIBRARY
	private $io; // THIS IS OUR IO# (CAMPAIGN)
	private $campaign_id; // THIS IS OUR CAMPAIGN_ID
	private $ip_address; // THIS IS THE IP ADDRESS OF USER
	private $session_id; // THIS IS OUR COOKIE ID FOR USER
	private $network; // FIQ, GOOGLE, ETC.
	private $subsource; // FEED ID, ETC
	private $referral_url; // THIS IS THE REFERRAL URL
	private $user_agent; // THIS IS THE USER AGENT
	
	protected $CI;
	
	public function __construct()	{
	    $this->CI = get_instance();
	    $this->CI->load->database();
	}
	
	public function analyze_ip()   {
	    //return 10;
	    
	    if ($this->ip_address == "")
	        throw new exception("IP Address required");
	    
	    if ($this->io == "")
	        throw new exception("IO required");
	    
	    if ($this->network == "")
	        throw new exception("Network required");
	    
	    if ($this->subsource == "")
	        throw new exception("Subsource required");
	    
	    $referral = urlencode($this->referral_url);
	    $useragent = urlencode($this->user_agent);
	    
	    $url = "http://2pth.com/check?ck={$this->API_KEY}&rt=click&output={$this->format}&ip={$this->ip_address}&s={$this->session_id}&p={$this->network}&a={$this->subsource}&cmp={$this->io}&rf={$referral}&ua={$useragent}";
	    
	    //$response = file_get_contents($url);
	    
	    $ch = curl_init();
	    $timeout = 2;
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    $response = curl_exec($ch);
	    curl_close($ch);
	    
	    $json = json_decode($response);
	    $score = (int)$json->items[0]->riskScore;

	    return $score;
	}

	public function get_clicks()	{
		return (int)$this->client->get($this->io . "_hourly_cap_count");
	}

	public function __get($name)	{
		return $this->$name;
	}

	public function __set($name, $value)	{
		$this->$name = $value;
	} 
}

?>