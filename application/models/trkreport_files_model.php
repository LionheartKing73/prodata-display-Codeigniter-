<?php

class Trkreport_files_model extends CI_Model	{

	protected $CI;

	// campaign info
	private $id;
	private $campaign_id;
	private $name;
	private $size;
	private $datetime;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();

	}


	public function create()  {
		$insert = array(
			"campaign_id" => $this->campaign_id,
			"name" => $this->name,
			"original_name" => $this->original_name,
			"size" => $this->size,
			"datetime" => $this->datetime
		);

		$this->CI->db->insert("trkreport_files", $insert);
		$this->id = $this->CI->db->insert_id();

		if ($this->id > 0) {
			return $this->id;
		} else {
			return false;
		}
	}

	public function get_trkreport_files($id = null) {
		$r = $this->CI->db->query("SELECT * FROM trkreport_files WHERE campaign_id='$id'");

		if ($r->num_rows() > 0) {
			$campaign = $r->result_array();
		} else {
			$campaign = array();
		}

		return $campaign;
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
