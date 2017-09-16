<?php

class v2_rich_media_survey_model extends CI_Model	{

	protected $CI;

	private $id;
	private $campaign_id;
	private $answer;
	private $ip_address;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		
		$this->CI->load->model("V2_ad_model");
	}
	
	public function get_survey_results()   {
	    if (! $this->campaign_id)
	        throw new exception("campaign_id required");
	    
        $ad = $this->CI->V2_ad_model->get_by_id($this->campaign_id);
        
        $results = array("rm_answere1", "rm_answere2", "rm_answere3", "rm_answere4", "rm_answere5");
        
        $answer_result = array();
        $total_count = 0;
        foreach($results as $a) {

            $r = $this->CI->db->query("SELECT COUNT(*) AS cnt FROM v2_rich_media_survey_results WHERE campaign_id='{$this->campaign_id}' AND answer='{$a}'");
            
            $row = $r->row_array();
            
            $answer_result[] = array("answer" => $a, "count" => (int)$row['cnt'], "ad_answer" => $ad[$a]);
            $total_count += (int)$row['cnt'];
        }

        return array("answer_result" => $answer_result, "total_count" => $total_count, "destination_url" => $ad['destination_url']);
	}
	
	public function set_survey_results()   {
	    if (! $this->campaign_id)
	        throw new exception("campaign_id required");
	        
        if (! $this->answer)
            throw new exception("answer required");
	            
        if (! $this->ip_address)
            throw new exception("ip_address required");
	                
	    $insert = array(
	        "campaign_id" => $this->campaign_id,
	        "answer" => $this->answer,
	        "ip_address" => $this->ip_address,
	    );
	    
	    $this->CI->db->insert("v2_rich_media_survey_results", $insert);
	}

	public function __set($name, $value) {
	    $this->{$name} = $value;
	}

	public function __get($name) {
	    return $this->{$name};
	}
}

?>