<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

ignore_user_abort(true);

class Conversion extends CI_Controller	{
	public $viewArray = array();
	
    public function __construct()	{
		parent::__construct();

		$this->load->helper("url");
                return redirect('v2/campaign/campaign_list');
		$this->load->helper('cookie');
		$this->load->library('user_agent');
		
		$this->load->library("parser");
		$this->load->library("session");
		
		$this->viewArray['current_url'] = current_url();
		$this->viewArray['base_url'] = base_url();
		$this->viewArray['site_url'] = site_url();
    }
    

    public function index($io = "") {
        // turn on output buffering if necessary
        if (ob_get_level() == 0) {
            ob_start();
        }
        
        header('Content-encoding: none', true);
        
        if ($_SERVER['REQUEST_METHOD'] === "POST" || $io == "")  {
            // do nothing
            echo ' ';
        } else {
            // return the 1x1 pixel
            header("Content-type: image/gif");
            header("Content-Length: 42");
            header("Cache-Control: private, no-cache, no-cache=Set-Cookie, proxy-revalidate");
            header("Expires: Wed, 11 Jan 2000 12:59:00 GMT");
            header("Last-Modified: Wed, 11 Jan 2006 12:59:00 GMT");
            header("Pragma: no-cache");
            //echo sprintf('%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%',71,73,70,56,57,97,1,0,1,0,128,255,0,192,192,192,0,0,0,33,249,4,1,0,0,0,0,44,0,0,0,0,1,0,1,0,0,2,2,68,1,0,59);
            echo base64_decode('R0lGODlhAQABAID/AMDAwAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEA');
            
            $ip = $_SERVER['REMOTE_ADDR'];
            $referer = $_SERVER['HTTP_REFERER'];
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            $browser = get_browser(null, true);
            
        }
    }
	
}
