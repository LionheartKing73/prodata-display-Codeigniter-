<?php 

class Email_Seeds_Model extends CI_Model	{

	protected $CI;
	
    private $id;
    private $io;
    private $email;
    private $fname;
    private $lname;
    private $purl_data = array();
	
	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		
	}
	
	public function create()   {
	    $insert = array(
	        "io" => $this->io,
	        "email" => $this->email,
	        "fname" => $this->fname,
	        "lname" => $this->lname,
	        "purl_data" => serialize($this->purl_data)
	    );
	    
	    $this->CI->db->insert("email_seeds", $insert);
	    $this->id = $this->CI->db->insert_id();
	    
	    return $this->id;
	}

	public function update()   {
	    $update = array(
	        "io" => $this->io,
	        "email" => $this->email,
	        "fname" => $this->fname,
	        "lname" => $this->lname,
	        "purl_data" => serialize($this->purl_data)
	    );
	     
	    $this->CI->db->update("email_seeds", $update, array("id" => $this->id));
	}
	
	public function get_by_io()  {
	    $r = $this->CI->db->query("SELECT * FROM email_seeds WHERE io='{$this->io}'");
	    if ($r->num_rows() > 0)    {
	        return $r->result_array();
	    } else {
	        return false;
	    }
	}
	
	public function remove()   {
	    $this->CI->db->query("DELETE FROM email_seeds WHERE id='{$this->id}'");
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