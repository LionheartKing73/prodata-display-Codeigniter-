<?php 

class V2_log_model extends CI_Model	{

	protected $CI;
	private $collection = 'v2_audit_log';
    private $id;
	private $io;
	private $action;
	private $note;
	private $timestamp;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
	}

	public function create($id, $data, $type) {
	    $insert = array(
			"campaign_id" => $id,
	        "action" => 'update',
	        "note" => $data,
	        "timestamp" => date("Y-m-d H:i:s"),
			"type" => $type
	    );
	    $this->CI->db->insert($this->collection, $insert);
	}
	
	public function get_by_io()  {
	    $r = $this->CI->db->query("SELECT * FROM audit_log WHERE io='{$this->io}'");
	    if ($r->num_rows() > 0)    {
	        return $r->result_array();
	    } else {
	        return false;
	    }
	}

	public function get_by_campaign_id($campaign_id)  {
	    $r = $this->CI->db->query("SELECT * FROM v2_audit_log WHERE campaign_id='{$campaign_id}' AND type='xml finish'");
	    if ($r->num_rows() > 0)    {
	        return $r->result_array();
	    } else {
	        return false;
	    }
	}
	
	public function purge_old_entries()    {
	    $date = date("Y-m-d 00:00:00", strtotime("-45 days"));
	    $this->CI->db->query("DELETE FROM audit_log WHERE timestamp <= '{$date}'");
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