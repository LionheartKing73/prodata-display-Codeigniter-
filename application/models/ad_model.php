<?php 

class Ad_model extends CI_Model	{

	protected $CI;
	
    private $id;
	private $title;
	private $description;
	private $category = "104"; // 55-AAA Top Sites, 40 Adult, 19 Automotive, 85 Beauty, 7 Health, 76 Medical, 69 Pets, 104 Run of Network, 66 Shopping, 74 Sports, 87 Toys, 11 Travel
	private $campaign_name;
	private $destination_url;
	private $display_url;
	private $target_radius;    // in miles ex: 25
	private $target_zones;     // space separated list (NATIONWIDE: US) (STATE: FL NH) (ZIPCODE: 33458 03102)
	private $target_type;      // NATIONWIDE, STATE, ZIPCODE
	private $creative_url;     // this is where the banner ad image exists
	private $bid = 0.0025;     // cost per click (bid)
	private $daily_cap = 0;    // daily MAX cap (in dollars)
    private $ppc_network_ad_active = "Y"; // whether the ad is active/inactive on the PPC network.
	
	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		
		$this->CI->load->model("Monitor_model");
	}
	
	public function lookup_category($id) {
	    $category = array(
	        "55" => "AAA Top Sites",
	        "40" => "Adult",
	        "19" => "Automotive",
	        "85" => "Beauty",
	        "7" => "Health",
	        "76" => "Medical",
	        "69" => "Pets",
	        "104" => "Run of Network",
	        "66" => "Shopping",
	        "74" => "Sports",
	        "87" => "Toys",
	        "11" => "Travel"
	    );
	    
	    return $category[$id];
	}

	public function create()   {
	    $insert = array(
	        "title" => $this->title,
	        "description" => $this->description,
	        "category" => $this->category,
	        "campaign_name" => $this->campaign_name,
	        "destination_url" => $this->destination_url,
	        "display_url" => $this->display_url,
	        "target_radius" => $this->target_radius,
	        "creative_url" => $this->creative_url,
	        "bid" => $this->bid,
	        "daily_cap" => $this->daily_cap,
	        "ppc_network_ad_active" => "Y",
	    );
	    $this->CI->db->insert("ads", $insert);
	    $this->id = $this->CI->db->insert_id();
	    
	    return $this->id;
	}
	
	public function update()   {
	    $update = array(
	        "title" => $this->title,
	        "description" => $this->description,
	        "category" => $this->category,
	        "campaign_name" => $this->campaign_name,
	        "destination_url" => $this->destination_url,
	        "display_url" => $this->display_url,
	        "target_radius" => $this->target_radius,
	        "creative_url" => $this->creative_url,
	        "bid" => $this->bid,
	        "daily_cap" => $this->daily_cap,
	        "ppc_network_ad_active" => "Y",
	    );
	    $this->CI->db->update("ads", $update, array("id" => $this->id));
	}
	
	public function get_by_id()  {
	    $r = $this->CI->db->query("SELECT * FROM ads WHERE id='{$this->id}'");
	    if ($r->num_rows() > 0)    {
	        return $r->row_array();
	    } else {
	        return false;
	    }
	}
	
	public function set_bid()  {
	    $this->CI->db->update("ads", array("bid" => $this->bid), array("id" => $this->id));
	}
	
	public function set_daily_cap()    {
	    $this->CI->db->update("ads", array("daily_cap" => $this->daily_cap), array("id" => $this->id));
	}
	
	public function set_ppc_network_ad_active() {
	    $this->CI->db->update("ads", array("ppc_network_ad_active" => $this->ppc_network_ad_active), array("id" => $this->id));
	}
	
	public function auto_generate_ad_content($url = "") {
	    if ($url == "")
	        throw new exception("url required");

	    $old_url = $url;
	    if (strpos($url, "cdqr") !== false)    {
	        $url = $this->CI->Monitor_model->retrieve_remote_url($url);
	    }

	    if ($url == "")
	        $url = $old_url;

	    // get page title
	    //$urlContents = file_get_contents($url);

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0");
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
	    $urlContents = curl_exec($ch);
	    curl_close($ch);
	    
	    /*
	    preg_match("/<title>(.*)<\/title>/i", $urlContents, $matches);
	    $title = $matches[1];
        */

	    $doc = new DOMDocument();
	    @$doc->loadHTML($urlContents);
	    $nodes = $doc->getElementsByTagName('title');
	    $title = trim($nodes->item(0)->nodeValue);

	    $meta = get_meta_tags($url);
	    $parse = parse_url($url);
	    //print_r($meta);

        $title = ($title != "") ? substr($title, 0, 25) : "Special Offer for You";
	    $description = ($meta['description'] != "") ? (substr($meta['description'],0,59) . " Click Now!") : "To learn more about this special offer, click now!";
	    
	    if ($parse['host'] == "")  {
	        $parse['host'] = "www.specialdiscounts.com";
	    }

	    $ad = array(
	        "display_url" => "http://" . $parse['host'],
	        "title" => $title,
	        "description" => $description
	    );

	    return $ad;
	}
	
	public function remove()   {
	    $this->CI->db->query("DELETE FROM ads WHERE id='{$this->id}'");
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