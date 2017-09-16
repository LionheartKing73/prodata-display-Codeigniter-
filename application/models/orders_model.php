<?php 

class Orders_model extends CI_Model	{

	private $collection = "campclick_campaigns";
	protected $CI;

	private $id;
	private $io;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->library("user_agent");
		
	}
}