<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order extends CI_Controller	{
	public $viewArray = array();
	
    public function __construct()	{
		parent::__construct();

		$this->load->helper("url");
		$this->load->helper('cookie');
		
		$this->load->library("parser");
		$this->load->library("session");
		$this->load->library('ion_auth');
		$this->load->model("Campclick_model");
		$this->load->model("Orders_model");
		
		$this->viewArray['current_url'] = current_url();
		$this->viewArray['base_url'] = base_url();
		$this->viewArray['site_url'] = site_url();
		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');	
		} else {
			if($this->ion_auth->is_admin())	{
				$this->viewArray['manage_users'] = true;
			} else {
				$this->viewArray['manage_users'] = false;
			}
			
			$this->viewArray['show_top_menu'] = true;
		}
    }
    
	public function index()	{
		$this->viewArray['country'] = array();
		$this->parser->parse("order/order_form.php", $this->viewArray);
	}
	
	public function voicemail()	{
		$this->parser->parse("order/voicemail.php", $this->viewArray);
	}
}

?>