<?php 

class V2_time_parting_model extends CI_Model	{

	protected $CI;
	private $collection = 'v2_time_parting';

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
	}

	public function create($insert) {

	    $this->CI->db->insert($this->collection, $insert);
	}
	
	public function get_by_id($id)  {
        return $this->CI->db->get_where($this->collection, ["id"=>$id])->row_array();
	}

	public function get_by_campaign_id($campaign_id)  {
        return $this->CI->db->get_where($this->collection, ["campaign_id"=>$campaign_id])->result_array();
	}
	
	public function get_by_campaign_id_dow($campaign_id, $dow) {
	    return $this->CI->db->get_where($this->collection, ["campaign_id"=>$campaign_id, "day_of_week"=>$dow])->row_array();
	}
	
	public function how_many_run_hours_today($campaign_id, $dow) {
	    $campaign = $this->get_by_campaign_id_dow($campaign_id, $dow);
	    $hour_run = 0;
	    
	    if ($campaign) {
	        $start_date = strtotime($campaign['start_time']);
	        $end_date = strtotime($campaign['end_time']);
	        
	        $delta = ($end_date - $start_date);
	        
	        $hour_run = ($delta / 60) / 60;
	    }
	    
	    return $hour_run;
	}

}

?>