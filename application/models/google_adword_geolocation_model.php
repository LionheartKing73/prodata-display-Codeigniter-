<?php

class Google_adword_geolocation_model extends CI_Model	{

	protected $CI;
	protected $table = 'google_adword_geolocations';

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
	}

	public function get_states() {
	    $r = $this->CI->db->query("SELECT DISTINCT(name), state FROM states WHERE country='{$this->country}' ORDER BY state ASC");

	    if ($r->num_rows() > 0)    {
	        return $r->result_array();
	    } else {
	        return array();
	    }
	}

	public function insert_batch(array $data) {
		return $this->CI->db->insert_batch($this->table, $data);
	}

	public function insert(array $data) {
		return $this->CI->db->insert($this->table, $data);
	}

	public function get_criteria_id_by_location_name($location_name, array $conditions = [])
	{
		if ( empty($location_name) ) return $location_name;

		$params = array_merge(array('location_name' => $location_name), $conditions);

		return $this->CI->db->get_where($this->table, $params)->row_array();
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
