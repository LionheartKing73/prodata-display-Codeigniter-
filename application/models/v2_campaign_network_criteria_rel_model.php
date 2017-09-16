<?php 

class V2_campaign_network_criteria_rel_model extends CI_Model	{

	protected $CI;
    private $type_array = array(
        "age",
        "gender",
        "state",
        "country",
        "postalcode",
        "carrier",
        "platform",
        "remarketing_io",
        "remarketing_vertical",
		"keyword",
		"interest",
		"domain_exclusions",
		"in_market",
    );
	private $id;
	private $campaign_id;
	private $network_id;
	private $criteria_value;
	private $criteria_network_value;
	private $criteria_type;

	private $collection = "v2_campaign_network_criteria_rel";

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		
	}

	public function insert_by_type($campaign, $criteria_value, $type, $criteria_network_value = null)   {
        
            
        if(!in_array($type, $this->type_array)){
            return false;
        }
		$insert = array(
			"campaign_id" => $campaign['id'],
			"criteria_type" => $type,
			"criteria_value" => $criteria_value
		);
		if($campaign['is_multiple'] == 'Y') {
			$insert["network_id"] = 1;
		} else {
			$insert["network_id"] = $campaign['network_id'];
		}
		if($criteria_network_value){
			$insert['criteria_network_value'] = $criteria_network_value;
		}
		$this->CI->db->insert($this->collection, $insert);
		$this->id = $this->CI->db->insert_id();

		return $this->id;
	}

//	public function insert_age($campaign, $age_value, $age_network_value = null)   {
//
//		$sql = "INSERT INTO {$this->collection} (campaign_id, network_id, age_value) VALUES ({$campaign['id']},{$campaign['network_id']},'{$age_value}')
//				ON DUPLICATE KEY UPDATE age_value = '{$age_value}'";
//
//		if($age_network_value){
//			$sql = "INSERT INTO {$this->collection} (campaign_id, network_id, age_value, age_network_value) VALUES ({$campaign['id']},{$campaign['network_id']},'{$age_value}','{$age_network_value}')
//					ON DUPLICATE KEY UPDATE age_value = '{$age_value}', age_network_value = '{$age_network_value}'";
//		}
//
//
//		//$this->id = $this->CI->db->insert_id();
//		var_dump($this->CI->db->query($sql)); exit;
//		return $this->id;
//	}

	public function get_all_locations_by_campaign_id($campaign_id,$network_id) {

		$type_array = ['country','state','postalcode'];
		$result = $this->CI->db->where('campaign_id',$campaign_id)->where('network_id',$network_id)->where_in('criteria_type',$type_array)->get($this->collection);
		return $result->result_array();
	}

	public function get_criteria_by_campaign_id_network_id_and_type($campaign_id,$network_id,$type) {

		$result = $this->CI->db->where('campaign_id',$campaign_id)->where('network_id',$network_id)->where('criteria_type',$type)->get($this->collection);
		return $result->row_array();
	}

	public function delete_by_id($id) {
		$result = $this->CI->db->where('id',$id)->delete($this->collection);
		return $result;
	}

	public function update_value_by_id($id, $value) {
		$result = $this->CI->db->where("id", $id)->update($this->collection, array('criteria_value'=>$value));
		//var_dump($result);
		return $result;
	}

	public function update_network_value_by_id($id, $value, $network_value) {
		$result = $this->CI->db->where("id", $id)->update($this->collection, array('criteria_network_value'=>$network_value,'criteria_value'=>$value));
		//var_dump($result);
		return $result;
	}

}

?>
