<?php 

class V2_network_country_criterion_model extends CI_Model	{

	protected $CI;
	
    private $collection = 'v2_network_country_criterion';

	private $id;
	private $network_id;
	private $country_name;
	private $country_code;
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

	public function get_criteria_id_list($country, $network_id)  {

        $criteria_array = array();
		$result = $this->CI->db->select('criterion_id')->where('network_id', $network_id)->where('country_code', $country)->get($this->collection)->result_array();
        foreach($result as $criteria){
                $criteria_array[] = $criteria['criterion_id'];
        }

    	return $criteria_array;
	}

	public function get_country($country, $network_id)  {

        $criteria_array = array();
		$result = $this->CI->db->select('*')->where('network_id', $network_id)->where('country_name', $country)->get($this->collection)->result_array();
//        foreach($result as $criteria){
//                $criteria_array[] = $criteria['criterion_id'];
//        }

    	return $result;
	}

	public function get_all_country_by_network_id($network_id)  {

		$criteria_array = array();
		$result = $this->CI->db->where('network_id', $network_id)->get($this->collection)->result_array();
//        foreach($result as $criteria){
//                $criteria_array[] = $criteria['criterion_id'];
//        }

		return $result;
	}
}

?>