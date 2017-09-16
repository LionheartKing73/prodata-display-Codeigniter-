<?php
/**
 * Created by PhpStorm.
 * User: node
 * Date: 3/29/16
 * Time: 11:45 AM
 */


if (!defined('BASEPATH')) exit('No direct script access allowed');

class test_email extends CI_Controller
{

    protected $CI;
    protected $CCEmail = 'jason@prodatafeed.com';

//    protected $CCEmail = 'hovhannes.zhamharyan.bw@gmail.com';


    public function __construct()
    {

        parent::__construct();
       // $this->CI =& get_instance();
        //$this->load->library('email');
        //$this->load->library("parser");
        $this->load->library('Send_email');

    }


    public function send_rejected()
    {

        $to = 'Harutyun.Sardaryan.bw@gmail.com';
        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = "html";
        $config['priority'] = 1;

        $this->email->initialize($config);


        $this->email->from('noreply@report-site.com', 'Report-Site No Reply');
        $this->email->to($to);
        $cammaign_io = 'eeeee';
        $cammaign_name = 'name';
       // $this->CI->email->cc($this->CCEmail);
        $this->email->subject("$cammaign_io _ $cammaign_name rejected");

        $data = array(
            'campaign_io_campaign_name' => $cammaign_io . '_' . $cammaign_name

        );
       // var_dump($data);exit;
        $message = $this->parser->parse("v2/email/campaign_was_rejected.tpl", $data, true);
        //var_dump($message);exit;
       // $message = "dddddddddddddddddddddddddd";
        // $this->CI->email->message("$cammaign_io _ $cammaign_name was rejected By network");
        $this->email->message($message);

        $this->email->send();


    }
    public function tests_email() {
		exit;
        $this->send_email->send_disapproved_ad();
        echo 11111;
    }
    public function test() {
		$to =  'harutyun.sardaryan.bw@gmail.com';
		$campaign_io =  '11111';
        $csv_file = 'uploads/permanent/cd3c2d5c44a74e78a2bcc3fab4860b1b.jpg';
        $this->send_email->send_lead_reporting($to, $campaign_io, 'leadcamp', 'NEW', $csv_file);
        echo 11111; exit;
    }


}


