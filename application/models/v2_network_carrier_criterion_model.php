<?php 

class V2_network_carrier_criterion_model extends CI_Model	{

	protected $CI;

	private $collection = "v2_network_carrier_criterion";

	private $id;
	private $network_id;
	private $carrier_id;
	private $country_code;
	private $criterion_id;
	private $carrier;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
	}

	public function get_criteria_id_list($list, $country_code, $network_id)  {
		// we need to select carrier with country code
		$result = $this->CI->db->select('criterion_id')->where('country_code', $country_code)->where('network_id', $network_id)->where_in('carrier', $list)->get($this->collection)->result_array();

		foreach($result as $criteria){
                $criteria_array[] = $criteria['criterion_id'];
        }
    	return $criteria_array;
	}
}

?>