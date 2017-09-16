<?php 

class Take5_Pending_Campaign_Openpixel_Model extends CI_Model	{

	protected $CI;
	
	private $id = 0;
	private $pending_campaign_id;
	private $pixel_url;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
	}
	
	public function create()   {
	    $insert = array(
	        "pending_campaign_id" => $this->pending_campaign_id,
	        "pixel_url" => $this->pixel_url
	    );
	    
	    if ($this->pixel_url != "")    {
	        $this->id = $this->CI->db->insert("take5_pending_campaign_openpixel", $insert);
	    }
	    
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
	    $this->CI->db->delete("take5_pending_campaign_openpixel", array("id" => $this->id));
	}
	
	public function remove_campaign()  {
	    $this->CI->db->delete("take5_pending_campaign_openpixel", array("pending_campaign_id" => $this->pending_campaign_id));
	}
	
	public function get_pixels($io = "")   {
	    if ($io == "")
	        throw new exception("get_pixels: io required");
	    
	    $r = $this->CI->db->query("SELECT take5_pending_campaign_openpixel.id, pixel_url FROM take5_pending_campaign_openpixel JOIN take5_pending_campaigns ON take5_pending_campaigns.id=take5_pending_campaign_openpixel.pending_campaign_id WHERE io='{$io}'");
	    return $r->result_array();
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