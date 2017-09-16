<?php 

class V2_carriers_list_model extends CI_Model	{

	protected $CI;

	private $collection = "v2_carriers_list";

	private $id;
	private $carrier;
	private $country_code;
	private $country_name;
	private $criterion_id;
	private $network_id;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
	}

	public function get_criteria_id_list($list, $network_id)  {
//
//		$result = $this->CI->db->select('criterion_id')->where('network_id', $network_id)->where_in('age_range', $list)->get($this->collection)->result_array();
//		foreach($result as $criteria){
//                $criteria_array[] = $criteria['criterion_id'];
//        }
//    	return $criteria_array;
	}

	public function get_carriers_list_by_country($country_code, $network_id)  {

		$result = $this->CI->db->select('carrier')->where('network_id', $network_id)->where('country_code', $country_code)->get($this->collection);
		if($result->num_rows() > 0){
			return $result->result_array();
        } else {
			return false;
		}
	}

}

?>