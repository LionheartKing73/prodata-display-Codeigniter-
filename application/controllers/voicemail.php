<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Voicemail extends CI_Controller	{
	public $viewArray = array();
	
    public function __construct()	{
		parent::__construct();

		$this->load->helper("url");
		
		$this->load->library("parser");
		$this->load->library("session");
		
		$this->load->model("Voicemail_model");
    }
    
	public function index()	{
		// build the extension list
		$extension_list = "";
		for($x=7700; $x<=7734; $x++)	{
			$extension_list .= $x . ",";
		}
		$extension_list = trim($extension_list, ",");
		
		$this->viewArray['voicemail'] = $this->Voicemail_model->get_voicemails($extension_list);
		$this->parser->parse("voicemail/voicemail.php", $this->viewArray);
	}	
}

?>
