<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."third_party/PHPExcel/Classes/PHPExcel.php";

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

class Excel	{
	
	private $obj;

	public function __construct()	{
		$this->obj = new PHPExcel();
	}

	public function __get($name)	{
		return $this->$name;
	}

	public function __set($name, $value)	{
		$this->$name = $value;
	}
}

?>