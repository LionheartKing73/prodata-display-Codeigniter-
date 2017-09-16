<?php 

class Organization_model extends CI_Model	{

	private $collection = "organization";
	protected $CI;

	private $id;
	private $name;
	private $user_id;
	private $io;
	private $node_id;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();		
	}

	public function create_node()	{
		$required = array("name", "user_id");
		foreach($required as $k)	{
			$insert[$k] = $this->$k;
		}

		$this->CI->db->insert($this->collection . "_node", $insert);
		$this->id = $this->CI->db->insert_id();
		return $this->id;
	}

	public function create_leaf()	{
		$required = array("io", "node_id", "user_id", "name");
		foreach($required as $k)	{
			$insert[$k] = $this->$k;
		}
		
		$this->CI->db->insert($this->collection . "_leaf", $insert);
		$this->id = $this->CI->db->insert_id();
		return $this->id;
	}
	
	public function update_node()	{
		$required = array("name", "user_id");
		foreach($required as $k)	{
			$update[$k] = $this->$k;
		}

		$this->CI->db->update($this->collection . "_node", $update, array("id" => $this->id));
	}

	public function update_leaf()	{
		$required = array("io", "node_id", "user_id", "name");
		foreach($required as $k)	{
			$update[$k] = $this->$k;
		}

		$this->CI->db->update($this->collection . "_leaf", $update, array("id" => $this->id));
	}
	
	public function delete_node()	{
		$this->CI->db->delete($this->collection . "_node", array("id"=>$this->id));
	}
	
	public function delete_leaf()	{
		$this->CI->db->delete($this->collection . "_leaf", array("id"=>$this->id));
	}

	public function get_nodes()	{
		if ($this->user_id > 0)	{
			$user_query = " AND user_id='{$this->user_id}'";
		}
		$r = $this->CI->db->query("SELECT * FROM {$this->collection}_node WHERE 1 {$user_query}");
		if ($r->num_rows() > 0)	{
			return $r->result_array();
		} else {
			return array();
		}
	}
	
	public function get_leafs()	{
		$r = $this->CI->db->query("SELECT * FROM {$this->collection}_leaf WHERE node_id='{$this->node_id}'");
		if ($r->num_rows() > 0)	{
			return $r->result_array();
		} else {
			return array();
		}
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
