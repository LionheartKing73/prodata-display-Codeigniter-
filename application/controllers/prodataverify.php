<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prodataverify extends CI_Controller {
    private $viewArray = array();
     
    public function __construct()   {
        parent::__construct();

        $this->load->helper("url");
        $this->load->helper('cookie');
        $this->load->library('user_agent');
        $this->load->library("parser");
        $this->load->library('ion_auth');
        
        $this->load->model("Email_platform_model");
        $this->load->model("Domains_model");

        if ($this->ion_auth->logged_in()) {
            $user = $this->ion_auth->user()->row();
            $user_id = $user->id;
            
            if ($user->is_take5 == "Y")
                $this->viewArray['take5_user'] = true;
            
            $this->viewArray['logo'] = $user->logo;
            $this->viewArray['user_id'] = $user->id;
        }
        
        $this->userid = $user->id;
        $this->session->set_userdata("domain_name", $user->domain_name);
        $this->viewArray['domain_name'] = $this->session->userdata("domain_name");

        $this->Domains_model->user_id = $user_id;
        $this->viewArray['domain'] = $this->Domains_model->get_domain_list($user_id);
        
        $this->viewArray['current_url'] = current_url();
        $this->viewArray['base_url'] = base_url();
        $this->viewArray['site_url'] = site_url();
    }
    
    public function bounce()    {
        $this->Email_platform_model->recipient = $this->input->post("recipient");
        $this->Email_platform_model->bounce_code = $this->input->post("code");
        $this->Email_platform_model->bounce();
    }
    
    public function complaint() {
        $this->Email_platform_model->recipient = $this->input->post("recipient");
        $this->Email_platform_model->complaint();
    }
    
    public function unsubscribe()   {
        $this->Email_platform_model->recipient = $this->input->post("recipient");
        $this->Email_platform_model->optout();
    }
    
    public function process($io = "")   {
        $this->Email_platform_model->io = $io;
        $this->Email_platform_model->send();
    }
    
    public function validate($io = "")  {
        $this->Email_platform_model->io = $io;
        $this->Email_platform_model->validate();
    }
    
    public function send($io = "")  {
        $this->require_auth();
        
        if ($io == "")  {
            $this->viewArray['next_date'] = date("Y-m-d H:i:s");
            $this->parser->parse("email/setup.tpl", $this->viewArray);
        } else {
            if ($_POST) {
                $id = $this->Email_platform_model->create_campaign($this->input->post());
                if ($id !== false)  {
                    print json_encode(array("status" => "SUCCESS"));
                } else {
                    print json_encode(array("status" => "ERROR"));
                }
            }
        }
    }
    
    public function loadfile($io = "", $file = "")    {
        $data = file_get_contents("/var/www/" . $file);
        
        $lines = explode("\n", $data);
        
        foreach($lines as $l)   {
            $l = trim($l);
            $this->Email_platform_model->recipient_add($io, $l);
            print $l;
        }
    }
    
    public function dropped()   {
        $recipient = $this->input->post("recipient");
    }
    
    public function delivered() {
        $recipient = $this->input->post("recipient");
    }
    
    public function require_auth()	{
        if (!$this->ion_auth->logged_in())	{
            redirect('auth/login', 'refresh');
        } else {
            if($this->ion_auth->is_admin())
                $this->viewArray['manage_users'] = true;
            else
                $this->viewArray['manage_users'] = false;
    
            $this->viewArray['show_top_menu'] = true;
        }
    }
}
