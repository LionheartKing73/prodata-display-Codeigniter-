<?php

class V2_network_model extends CI_Model	{

	protected $CI;
	private $collection = 'v2_networks';

    private $id;
	private $name; // FIQ, EXOCLICK, FACEBOOK, GOOGLE, BING
	private $is_active; // (N) inactive, (Y) active

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();

//		$this->CI->load->model("Campclick_model");
//		$this->CI->load->model("Ad_model");
//		$this->CI->load->model("Finditquick_model");
	}

	public function update_bid($id, $arr_update){
		return $this->CI->db->where('id', $id)->update($this->collection, $arr_update);
	}


	public function create()   {
//	    $insert = array(
//	        "io" => $this->io,
//	        "ppc_network_id" => $this->ppc_network_id, // this is the ad ID on the network
//	        "ppc_network" => $this->ppc_network, // this is the PPC network the ad is on (FIQ, FACEBOOK, BING, GOOGLE, EXOCLICK)
//	        "status" => $this->status,
//	        "bid_rate" => $this->bid_rate,
//	        "ad_id" => $this->ad_id
//	    );
//	    $this->CI->db->insert("map_campaign_ppc_network", $insert);
	}

	public function update()   {
//	    $update = array(
//	        "ppc_network_id" => $this->ppc_network_id, // this is the ad ID on the network
//	        "ppc_network" => $this->ppc_network, // this is the PPC network the ad is on (FIQ, FACEBOOK, BING, GOOGLE, EXOCLICK)
//	        "status" => $this->status,
//	        "bid_rate" => $this->bid_rate,
//	        "ad_id" => $this->ad_id
//	    );
//	    $this->CI->db->update("map_campaign_ppc_network", $update, array("io" => $this->io));
	}

	public function get_all_by_user_id_and_campaign_type($user_id, $campaign_type)  {
//	    $r = $this->CI->db->query("SELECT * FROM V2_networks n JOIN V2_map_user_network m ON m.network_id=n.id
//									WHERE m.user_id='{$user_id}' AND n.is_active ='Y' AND m.campaign_type='{$campaign_type}'");
		$r = $this->CI->db->select("v2_networks.*")
			->join("v2_map_user_network", "v2_map_user_network.network_id = v2_networks.id")
			->where("v2_networks.is_active = 'Y'")
			->where("v2_map_user_network.campaign_type = '{$campaign_type}'")
			->where("v2_map_user_network.user_id = '{$user_id}'")
			->get($this->collection);
		if ($r->num_rows() > 0)    {
	        return $r->row_array();
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

	public function get_all_networks() {

		return $this->CI->db
			->get_where($this->collection, array("is_active" => 'Y'))
			->result_array();

	}

	public function get_network_by_name($name)
	{
		$name = strtoupper(trim($name));
		return $this->CI->db
			->get_where(
				$this->collection,
				["name" => $name]
			)
			->row_array();
	}
	
	public function get_network_name_by_id($id)    {
	    $network = $this->CI->db->get_where($this->collection, ['id' => $id])->row_array();
	    
	    return $network['name'];
	}

}

?>