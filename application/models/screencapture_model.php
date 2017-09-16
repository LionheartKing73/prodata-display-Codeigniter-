<?php 

class Screencapture_model extends CI_Model	{

	protected $CI;
	private $url;
	private $width;
	private $height;
	private $filename;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
	}
	
	private function preparePostFields($array) {
	    $params = array();
	
	    foreach ($array as $key => $value) {
	        $params[] = $key . '=' . urlencode($value);
	    }
	
	    return implode('&', $params);
	}
	
	public function capture() {

	    $data = array(
	        "url" => $this->url,
	        "width" => $this->width,
	        "height" => $this->height,
	        "filename" => $this->filename
        );
        
        $query = $this->preparePostFields($data);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://45.33.7.188:3000/screenshot");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $report = curl_exec($ch);
        
        //print_r($report);
	        
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
