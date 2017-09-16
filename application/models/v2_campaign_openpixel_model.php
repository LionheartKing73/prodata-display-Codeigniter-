<?php 

class v2_campaign_openpixel_model extends CI_Model	{

	protected $CI;
	private $collection = 'v2_campaign_openpixel';

	private $id;
	private $campaign_id;
	private $pixel_url;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
	}
	
	public function create($pixel_images, $campaign_id) {
	// foreach() and save urls into db
		foreach($pixel_images as $pixel){
			if($pixel && $campaign_id){
				$insert = array(
					"campaign_id" => $campaign_id,
					"pixel_url" => $pixel
				);

				$new_id = $this->CI->db->insert($this->collection, $insert);
				if ($new_id > 0) {
					$ids[] = $new_id;
				} else {
					return false;
				}
			}
		}
		return $ids;
	}
	
	public function update()   {
	    return false;
	}
	
	public function remove()   {
	    $this->CI->db->delete($this->collection, array("id" => $this->id));
	}
	
	public function remove_campaign()  {
	    $this->CI->db->delete($this->collection, array("campaign_id" => $this->campaign_id));
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