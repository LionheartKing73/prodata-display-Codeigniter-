<?php 

class Take5_Clicktrack365_Model extends CI_Model	{

	protected $CI;
	
	private $id;
	private $username = "jfurman@take5s.com";
	private $password = "2c11d968559640878252b18e0d8ba381";
	private $api_endpoint_url = "http://services.clicktrack365.com/gcapi.asmx";

    // create campaign variables
	private $campaign_type = "listlinktrack";
	private $group_id;
	private $campaignName;
	private $campaignDesc;
	private $customerID = "c41ccf4d-667e-4fab-a2aa-39ec804a5762";
	private $date_start;
	private $date_end;
	private $max_clicks;
	
	// create links
	private $campaign_id;
	private $link_max_clicks;
	private $target_url;
	

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
	}
	
	public function create_campaign()  {
	    $post = array(
	        "user" => $this->username,
	        "password" => $this->password,
	        "CampaignName" => $this->campaignName,
	        "CampaignDescription" => $this->campaignDesc,
	        "CustomerID" => $this->customerID,
	        "StartDateTime" => $this->date_start,
	        "EndDateTime" => $this->date_end,
	        "ServedLinkLimit" => $this->max_clicks,
	        "CampaignType" => $this->campaign_type,
	        "GroupID" => $this->group_id
	    );
	    
	    $postStr = "";
	    foreach($post as $k => $v)   {
	        $postStr .= "&{$k}=" . urlencode($v);
	    }
	    
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $this->api_endpoint_url . "/GCCreateCampaign");
	    curl_setopt($ch, CURLOPT_USERAGENT, "PRODATAFEED REPORT-SITE AUTOMATION TOOL");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $postStr);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	    
	    $response = curl_exec($ch);
	    
	    $json = json_decode(json_encode(simplexml_load_string($response)));
	     
	    if ($json->Success == "true")   {
	        return $json->Value;
	    } else {
	        return false;
	    }
	}
	
	public function create_link()  {
	    $post = array(
	        "user" => $this->username,
	        "password" => $this->password,
	        "CampaignID" => $this->campaign_id,
	        "ServedLinkLimit" => $this->link_max_clicks,
	        "TargetURL" => $this->target_url
	    );
	    
	    $postStr = "";
	    foreach($post as $k => $v)   {
	        $postStr .= "&{$k}=" . urlencode($v);
	    }
	    
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $this->api_endpoint_url . "/GCAddCampaignLink");
	    curl_setopt($ch, CURLOPT_USERAGENT, "PRODATAFEED REPORT-SITE AUTOMATION TOOL");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $postStr);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	     
	    $response = curl_exec($ch);
	     
	    $json = json_decode(json_encode(simplexml_load_string($response)));
	    
	    if ($json->Success == "true")   {
	        return $json->Value;
	    } else {
	        return false;
	    }
	}

	
	
	public function __get($name)	{
		return $this->$name;
	}

	public function __set($name, $value)	{
		$this->$name = $value;
	}

	public function __isset($name)	{
		return isset($this->$name);
	}
	
}

?>