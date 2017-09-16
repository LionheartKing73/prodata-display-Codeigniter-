<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');




    function send_email($to, $message, $subject, $cc = null, $from = null) {

        $CI =& get_instance();

        $CI->load->library('email');

        if(!$from){
            $from = 'hovhannes.zhamharyan.bw@gmail.com';
        }
        $config['useragent']        = 'PHPMailer';
        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = "html";
        $config['priority'] = 1;

        $CI->email->initialize($config);
        $CI->email->set_newline("\r\n");
        $CI->email->from($from, 'No Reply');
        $CI->email->to($to);
        if($cc){
            $CI->email->cc($cc);
        }
        $CI->email->subject($subject);
        $CI->email->message($message);
        $CI->email->send();

    }




?>