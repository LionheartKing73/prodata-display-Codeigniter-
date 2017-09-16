<?php 

class Cookie_vertical_model extends CI_Model	{

	private $collection = "cookie_vertical";
	protected $CI;

	private $id;
	private $name;
	private $vendor_reference_id;
	private $is_active;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		
	}

	public function create()	{
		$required = array("name", "is_active", "vendor_reference_id");
		foreach($required as $k)	{
			$insert[$k] = $this->$k;
		}
		
		$this->CI->db->insert($this->collection, $insert);
		$this->id = $this->CI->db->insert_id();
		return $this->id;
	}
	
	public function delete()	{
		$this->CI->db->delete($this->collection, array("id"=>$this->id));
	}
	
	public function get_cookie_vertical()	{
		$r = $this->CI->db->query("SELECT * FROM {$this->collection} WHERE id='{$this->id}' LIMIT 1");
		if ($r->num_rows() > 0)	{
			return $r->row_array();
		} else {
			return false;
		}
	}
	
	public function get_cookie_vertical_list($is_active = "Y")	{
		$r = $this->CI->db->query("SELECT * FROM {$this->collection} WHERE is_active='{$is_active}' ORDER BY name ASC");
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
