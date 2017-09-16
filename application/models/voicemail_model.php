<?php 

class Voicemail_model extends CI_Model	{

	protected $CI;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
	}
	
	public function get_voicemails($extension_list = "")	{
		if ($extension_list == "")	{
			return array();
		} else {
			$r = $this->CI->db->query("SELECT * FROM voicemail_track WHERE extension IN ({$extension_list}) ORDER BY extension ASC");
			return $r->result_array();
		}
	}
}