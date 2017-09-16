<?php 

class Ppcnetworks_model extends CI_Model	{

	protected $CI;
	
    private $id;
	private $io;
	private $ppc_network_id; // this is the remote ad/campaign ID on the ppc network
	private $ppc_network;  // FIQ, EXOCLICK, FACEBOOK, GOOGLE, BING
	private $status; // (P)aused, (A)ctive, (C)ompleted
	private $bid_rate = 0.0000; // 0.0020 -- bid in cost per click (relates to CPM eventually on network) 
	private $ad_id;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();

		$this->CI->load->model("Campclick_model");
		$this->CI->load->model("Ad_model");
		$this->CI->load->model("Finditquick_model");
	}

	public function create()   {
	    $insert = array(
	        "io" => $this->io,
	        "ppc_network_id" => $this->ppc_network_id, // this is the ad ID on the network
	        "ppc_network" => $this->ppc_network, // this is the PPC network the ad is on (FIQ, FACEBOOK, BING, GOOGLE, EXOCLICK)
	        "status" => $this->status,
	        "bid_rate" => $this->bid_rate,
	        "ad_id" => $this->ad_id
	    );
	    $this->CI->db->insert("map_campaign_ppc_network", $insert);
	}
	
	public function update()   {
	    $update = array(
	        "ppc_network_id" => $this->ppc_network_id, // this is the ad ID on the network
	        "ppc_network" => $this->ppc_network, // this is the PPC network the ad is on (FIQ, FACEBOOK, BING, GOOGLE, EXOCLICK)
	        "status" => $this->status,
	        "bid_rate" => $this->bid_rate,
	        "ad_id" => $this->ad_id
	    );
	    $this->CI->db->update("map_campaign_ppc_network", $update, array("io" => $this->io));
	}
	
	public function get_by_io()  {
	    $r = $this->CI->db->query("SELECT * FROM map_campaign_ppc_network WHERE io='{$this->io}'");
	    if ($r->num_rows() > 0)    {
	        return $r->row_array();
	    } else {
	        return false;
	    }
	}

	public function set_ad()   {
	    if (! $this->ad_id > 0)
	        throw new exception("ad_id required");
	    
	    if ($this->io == "")
	        throw new exception("io required");
	    
	    $this->CI->db->update("map_campaign_ppc_network", array("ad_id" => $this->ad_id), array("io" => $this->io));
	}
	
	public function set_bid()  {
	    if (! $this->ad_id > 0)
	        throw new exception("ad_id required");
	     
	    if ($this->io == "")
	        throw new exception("io required");
	     
	    if (! $this->bid_rate > 0)
	        throw new exception("bid_rate required");
	    
	    $this->CI->db->update("map_campaign_ppc_network", array("bid_rate" => $this->bid_rate), array("io" => $this->io));
	    $this->CI->Ad_model->id = $this->ad_id;
	    $this->CI->Ad_model->set_bid();
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