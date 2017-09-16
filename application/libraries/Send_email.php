
<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Send_email {

    protected  $CI;
    protected $CCEmail = 'jason@prodata.media';
    //protected $CCEmail_me = 'hovhannes.zhamharyan.bw@gmail.com';


    public function __construct() {

        $this->CI =& get_instance();
        $this->CI->load->library('email');
        $this->CI->load->library("parser");
        $this->CI->load->model('v2_domains_model');
    }

    /**
     *
     * @param string $to
     * @param string $cammaign_io
     * @param string $cammaign_name
     *
     * Global function for send email
     */

    public function send_completed($to, $cammaign_io, $cammaign_name,$campaign_type, $link_edit, $disapproval_reasons = null){


        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = "html";
        $config['priority'] = 1;

        $this->CI->email->initialize($config);


        $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
        $this->CI->email->to($to);
        $this->CI->email->cc($this->CCEmail);
        //$this->CI->email->cc($this->CCEmail_me);
        $this->CI->email->subject("$cammaign_io _ $cammaign_name was completed");
        $data = array(
            'campaign_io' => $cammaign_io,
            'campaign_name' => $cammaign_name,
            'link_edit'    => $link_edit,
            'campaign_type'     => $campaign_type
        );

        $domainData = $this->CI->v2_domains_model->getDataByName(substr(base_url(), 0, -1));
        if($domainData) {
            $data['domain'] = $domainData->domain;
            $data['background_color'] = $domainData->background_color;
            $data['logo'] = $domainData->logo;
        }
//        var_dump($data);

    $message = $this->CI->parser->parse("v2/email/campaign_was_completed.tpl", $data, true);
//        var_dump($message);die;

        // $this->CI->email->message("$cammaign_io _ $cammaign_name was completed");
        $this->CI->email->message($message);
        $this->CI->email->send();



    }
    public function send_for_extend_end_date($to=null, $cammaign_io, $cammaign_name,$campaign_type, $link_edit, $disapproval_reasons = null){


        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = "html";
        $config['priority'] = 1;

        $this->CI->email->initialize($config);


        $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');

        if($to) {
            $this->CI->email->to($to);
            $this->CI->email->cc($this->CCEmail);
        } else {
            $this->CI->email->to($this->CCEmail);
        }

        $this->CI->email->subject("$cammaign_io _ $cammaign_name was completed");
        $data = array(
            'campaign_io' => $cammaign_io,
            'campaign_name' => $cammaign_name,
            'link_edit'    => $link_edit,
            'campaign_type'     => $campaign_type
        );

        $domainData = $this->CI->v2_domains_model->getDataByName(substr(base_url(), 0, -1));
        if($domainData) {
            $data['domain'] = $domainData->domain;
            $data['background_color'] = $domainData->background_color;
            $data['logo'] = $domainData->logo;
        }
//        var_dump($data);

    $message = $this->CI->parser->parse("v2/email/extend_end_date.tpl", $data, true);
//        var_dump($message);die;

        // $this->CI->email->message("$cammaign_io _ $cammaign_name was completed");
        $this->CI->email->message($message);
        $this->CI->email->send();



    }

    public function send_rejected($to, $cammaign_io, $cammaign_name, $campaign_type, $link_edit, $disapproval_reasons){

        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = "html";
        $config['priority'] = 1;

        $this->CI->email->initialize($config);


        $this->CI->email->from('no-reply@reporting.prodata.media', 'Report-Site No Reply');
        $this->CI->email->to($to);
        $this->CI->email->cc($this->CCEmail);
//        $this->CI->email->cc($this->CCEmail_me);
        $this->CI->email->subject("$cammaign_io _ $cammaign_name rejected");
        $data = array(
            'campaign_io' => $cammaign_io,
            'campaign_name' => $cammaign_name,
            'campaign_type'     => $campaign_type,
            'link_edit'    => $link_edit,
            'disaproval_reasons' => $disapproval_reasons

        );

        $domainData = $this->CI->v2_domains_model->getDataByName(substr(base_url(), 0, -1));
        if($domainData) {
            $data['domain'] = $domainData->domain;
            $data['background_color'] = $domainData->background_color;
            $data['logo'] = $domainData->logo;
        }

//        var_dump($data);
            $message = $this->CI->parser->parse("v2/email/campaign_was_rejected.tpl", $data, true);
//        var_dump($message);die;
        // $this->CI->email->message("$cammaign_io _ $cammaign_name was rejected By network");
        $this->CI->email->message($message);
        var_dump($this->CI->email->send());

    }

    public function send_approved($to, $campaign_io, $campaign_name, $campaign_type, $link_edit = null, $disapproval_reasons = null){

        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = "html";
        $config['priority'] = 1;

        $this->CI->email->initialize($config);

        $this->CI->email->from('no-reply@reporting.prodata.media', 'Report-Site No Reply');
        $this->CI->email->to($to);
        $this->CI->email->cc($this->CCEmail);
//        $this->CI->email->cc($this->CCEmail_me);
        $this->CI->email->subject("$campaign_io _ $campaign_name with type $campaign_type was approved");
        $data = array(
            'campaign_io' => $campaign_io,
            'campaign_name' => $campaign_name,
            'campaign_type' => $campaign_type
        );
        $domainData = $this->CI->v2_domains_model->getDataByName(substr(base_url(), 0, -1));
        if($domainData) {
            $data['domain'] = $domainData->domain;
            $data['background_color'] = $domainData->background_color;
            $data['logo'] = $domainData->logo;
        }


        $message = $this->CI->parser->parse("v2/email/campaign_was_approved.tpl", $data, true);
//        var_dump($message); exit();

        //$this->CI->email->message("$cammaign_io _ $cammaign_name was approved by network");
        $this->CI->email->message($message);

        $this->CI->email->send();

    }

    public function send_lead_reporting($to, $campaign_io, $campaign_name, $lead_type, $csv_file){

        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = "html";
        $config['priority'] = 1;

        $this->CI->email->initialize($config);

        $this->CI->email->from('no-reply@reporting.prodata.media', 'Report-Site No Reply');
        $this->CI->email->to($to);
        //$this->CI->email->cc($this->CCEmail);
//        $this->CI->email->cc($this->CCEmail_me);
        $this->CI->email->subject("Leads reporting for $campaign_io _ $campaign_name with type FB-LEAD");
        $data = array(
            'campaign_io' => $campaign_io,
            'campaign_name' => $campaign_name,
            'campaign_type' => 'FB-LEAD',
            'lead_type' => $lead_type
        );
        $domainData = $this->CI->v2_domains_model->getDataByName(substr(base_url(), 0, -1));
        if($domainData) {
            $data['domain'] = $domainData->domain;
            $data['background_color'] = $domainData->background_color;
            $data['logo'] = $domainData->logo;
        }


        $message = $this->CI->parser->parse("v2/email/campaign_lead_reporting.tpl", $data, true);
//        var_dump($message); exit();

        //$this->CI->email->message("$cammaign_io _ $cammaign_name was approved by network");
        $this->CI->email->message($message);
        $this->CI->email->attach($csv_file);

        $this->CI->email->send();

    }

    public function send_daily_report($csv_file) {
        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['priority'] = 1;
        $this->CI->email->initialize($config);
        $this->CI->email->from('no-reply@reporting.prodata.media', 'Report-Site No Reply');
        $this->CI->email->to('jason@prodata.media');
        $this->CI->email->subject("Daily Report");
        $this->CI->email->message('Dayli Report');
        $this->CI->email->attach($csv_file);
        $this->CI->email->send();

    }

    public function send_disapproved_ad($to, $cammaign_io, $cammaign_name, $campaign_id,$creative_name,$creative_width, $creative_height,$campaign_type, $message){


        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = "html";
        $config['priority'] = 1;

        $this->CI->email->initialize($config);



        $this->CI->email->from('no-reply@reporting.prodata.media', 'Report-Site No Reply');
        $this->CI->email->to($to);
        $this->CI->email->cc($this->CCEmail);
//        $this->CI->email->cc($this->CCEmail_me);
        $this->CI->email->subject("$cammaign_io _ $cammaign_name ad was rejected");

        $link_edit = 'http://reporting.prodata.media/v2/campaign/ad_list/'. $campaign_id;

        $data = array(
            'ad_rejected_message' => $message,
            'link_edit'           => $link_edit,
            'creative_name'       => $creative_name,
            'creative_width'      => $creative_width,
            'creative_height'     => $creative_height,
            'campaign_io'         => $cammaign_io,
            'campaign_name'       => $cammaign_name,
            'campaign_type'       => $campaign_type


        );

        $domainData = $this->CI->v2_domains_model->getDataByName(substr(base_url(), 0, -1));
        if($domainData) {
            $data['domain'] = $domainData->domain;
            $data['background_color'] = $domainData->background_color;
            $data['logo'] = $domainData->logo;
        }

        $message = $this->CI->parser->parse("v2/email/ad_was_rejected.tpl", $data, true);

        //$this->CI->email->message($email_message);
        $this->CI->email->message($message);
        $this->CI->email->send();
    }

    public function send_welcome_message($to, $subject, $message)
    {

        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = "html";
        $config['priority'] = 1;

        $this->CI->email->initialize($config);


        $this->CI->email->from('no-reply@reporting.prodata.media', 'Report-Site No Reply');
        $this->CI->email->to($to);

        $this->CI->email->subject($subject);

        $this->CI->email->message($message);

        $this->CI->email->send();
    }

}
