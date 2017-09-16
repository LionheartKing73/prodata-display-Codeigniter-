<?php 

class Vendor_model extends CI_Model	{

	private $collection = "vendor";
	protected $CI;

	private $id;
	private $name;
	private $email;
	private $is_active;
	private $user_id;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		
	}

	public function create()	{
		$required = array("name", "is_active", "user_id", "email");
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
	
	public function get_vendor()	{
		$r = $this->CI->db->query("SELECT * FROM {$this->collection} WHERE id='{$this->id}' LIMIT 1");
		if ($r->num_rows() > 0)	{
			return $r->row_array();
		} else {
			return false;
		}
	}
	
	public function get_vendor_list($userid=NULL, $is_active = "Y")	{
		$extra_sql = ($userid!='')?' AND user_id='.$userid:'';
		$r = $this->CI->db->query("SELECT * FROM {$this->collection} WHERE is_active='{$is_active}' $extra_sql ORDER BY name ASC");
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
