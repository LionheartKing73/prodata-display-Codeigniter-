<?php 

class Take5_Pending_Campaign_Links_Model extends CI_Model	{

	protected $CI;
	
	private $id;
	private $pending_campaign_id;
	private $click_count;
	private $destination_url;
	private $original_url;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
	}
	
	public function create()   {
	    $insert = array(
	        "pending_campaign_id" => $this->pending_campaign_id,
	        "click_count" => $this->click_count,
	        "destination_url" => $this->destination_url,
	        "original_url" => $this->original_url
	    );
	    
	    $this->id = $this->CI->db->insert("take5_pending_campaign_links", $insert);
	    
	    if ($this->id > 0) {
	        return $this->id;
	    } else {
	        return false;
	    }
	}
	
	public function update()   {
	    return false;
	}
	
	public function remove()   {
	    $this->CI->db->delete("take5_pending_campaign_links", array("id" => $this->id));
	}
	
	public function remove_campaign()  {
	    $this->CI->db->delete("take5_pending_campaign_links", array("pending_campaign_id" => $this->pending_campaign_id));
	}
	
	public function get_links_by_pending_id()  {
	    $r = $this->CI->db->query("SELECT * FROM take5_pending_campaign_links WHERE pending_campaign_id='{$this->pending_campaign_id}'");
	    if ($r->num_rows() > 0)    {
	        return $r->result_array();
	    } else {
	        return array();
	    }
	}

	public function get_primary_campaign_link()    {
	    $r = $this->CI->db->query("select * from take5_pending_campaign_links where pending_campaign_id='{$this->pending_campaign_id}' order by click_count desc limit 1");
	    if ($r->num_rows() > 0)
	        return $r->row_array();
	    else
	        return false;
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