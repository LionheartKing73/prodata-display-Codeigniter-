<?php

class V2_conversion_model extends CI_Model	{

	protected $CI;

	private $guid;
	private $conversionValue;
	private $cookieName = "ProDataMediaConversionTracker";
	private $userAgent;
	private $pageUrl;
	private $apiKey;
	private $campaign_id;
	private $ip_address;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
	}

	public function lookup_guid()  {
	    list($guid, $campaign_id) = explode("--", $this->guid);

	    $r = $this->CI->db->query("SELECT * FROM v2_cookie_tracking WHERE uuid='{$guid}' AND campaign_id='{$campaign_id}' LIMIT 1");

	    if ($r->num_rows() > 0) {
	        return $r->row_array();
	    } else {
	        return false;
	    }
	}

	public function store_cookie($cookie_status = false) {

	    $sql = "INSERT INTO v2_cookie_tracking (uuid, campaign_id, ip_address) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE ip_address=VALUES(ip_address), count=count+1";

	    $this->CI->db->query($sql, array(
	        $this->guid,
	        $this->campaign_id,
	        $this->ip_address
	    ));

	}

	public function store_conversion() {
	    $conversion = array(
	        "campaign_id" => $this->campaign_id,
	        "conversionValue" => $this->conversionValue,
	        "userAgent" => $this->userAgent,
	        "pageUrl" => $this->pageUrl,
	        "apiKey" => $this->apiKey
	    );

	    $this->CI->db->insert("v2_conversions", $conversion);
	}

	public function __set($name, $value) {
	    $this->{$name} = $value;
	}

	public function __get($name) {
	    return $this->{$name};
	}
}

?>