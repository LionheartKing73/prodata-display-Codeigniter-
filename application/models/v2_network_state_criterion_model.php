<?php 

class V2_network_state_criterion_model extends CI_Model	{

	protected $CI;

	private $collection = "v2_network_state_criterion";

	private $id;
	private $network_id;
	private $network_country_id;
	private $state_name;
	private $state_code;
	private $criterion_id;


	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
	}

	public function create($data){
		$this->CI->db->insert($this->collection, $data);
		$id=$this->CI->db->insert_id();

		return $id;
	}

	public function get_criteria_id_list($list, $network_id)  {

		$result = $this->CI->db->select('criterion_id')->where('network_id', $network_id)->where_in('state_code', $list)->get($this->collection)->result_array();
        foreach($result as $criteria){
                $criteria_array[] = $criteria['criterion_id'];
        }
    	return $criteria_array;
	}

	public function get_state($state, $network_id)  {

		$criteria_array = array();
		$result = $this->CI->db->select('*')->where('network_id', $network_id)->where('state_name', $state)->get($this->collection)->result_array();
//        foreach($result as $criteria){
//                $criteria_array[] = $criteria['criterion_id'];
//        }

		return $result;
	}

}

?>