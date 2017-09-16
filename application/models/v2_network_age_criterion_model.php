<?php 

class V2_network_age_criterion_model extends CI_Model	{

	protected $CI;

	private $collection = "v2_network_age_criterion";

	private $id;
	private $network_id;
	private $age_range;
	private $criterion_id;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
	}

	public function get_criteria_id_list($list, $network_id)  {

		$result = $this->CI->db->select('criterion_id')->where('network_id', $network_id)->where_in('age_range', $list)->get($this->collection)->result_array();
		foreach($result as $criteria){
                $criteria_array[] = $criteria['criterion_id'];
        }
    	return $criteria_array;
	}
}

?>