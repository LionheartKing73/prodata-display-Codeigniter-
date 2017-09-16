<?php

class Country_model extends CI_Model	{

	protected $CI;

	private $country = "US";
	private $state;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();

	}

	public function get_states()    {
	    $r = $this->CI->db->query("SELECT DISTINCT(name), state FROM states WHERE country='{$this->country}' ORDER BY state ASC");

	    if ($r->num_rows() > 0)    {
	        return $r->result_array();
	    } else {
	        return array();
	    }
	}

	public function get_states_by_country($country) {
		$r = $this->CI->db->query("SELECT DISTINCT(name), state FROM states WHERE country='{$country}' ORDER BY state ASC");

		if ($r->num_rows() > 0) {
			return $r->result_array();
		} else {
			return array();
		}
	}

	public function get_state_name_from_state_iso_code($state_iso_code, $country_code = 'US')
	{
		$r = $this->CI->db->get_where("states", array(
			'country' => strtoupper(trim($country_code)),
			'state' => strtoupper(trim($state_iso_code))
		))->row_array();
		return !empty($r) ? $r['name'] : NULL;
	}

	public function get_state_iso_code_from_state_name($state_name, $country_code = 'US')
	{
		$r = $this->CI->db
			->from("states")
			->where(array(
				'country' => $country_code
			))
			->like('name', trim($state_name))
			->get()
			->row_array();
		return !empty($r) ? $r['state'] : NULL;
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
