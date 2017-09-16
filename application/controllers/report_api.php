<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * 
 * EXAMPLE OF HOW REST WORKS:
 * 
 *  index.php/report_api/tracking/id/1/format/json
 *             /           /     \         \
 *     controller     resource   param    output format
 *
 *
 *  Supported Output format: xml, json, csv, html, php, serialize
 *
 *
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Report_api extends REST_Controller    {
	function __construct()
    {
        // Construct our parent class
        parent::__construct();
        
        $this->load->model("Campclick_model");
        $this->load->model("Monitor_model");
        
        // Configure limits on our controller methods. Ensure
        // you have created the 'limits' table and enabled 'limits'
        // within application/config/rest.php
        $this->methods['tracking_get']['limit'] = 500; //500 requests per hour per user/key
        $this->methods['user_post']['limit'] = 100; //100 requests per hour per user/key
        $this->methods['user_put']['limit'] = 500; //500 requests per hour per user/key
        $this->methods['user_delete']['limit'] = 50; //50 requests per hour per user/key
    }
    
    function tracking_get()    {
        // update user in system
        if (! $this->get("io")) {
            $this->response(array("status" => "ERROR"), 404);
        } else {
            $get = $this->get();
            $io = $get['io'];
            $wildcard = (isset($get['wildcard']) && $get['wildcard'] != "") ? $get['wildcard'] : "0";

            $report = array();
            $data = $this->Campclick_model->io_tracking_report($io, $wildcard);
            
            if ($data === false) {
                $this->response(array("status" => "ERROR", "message" => "Invalid IO"), 404);
            }
            
            $report = array();
            foreach($data as $d)    {
                $myUrl = $this->Monitor_model->retrieve_remote_url($d['dest_url']);
            
                $line = array(
                    "IO" => $d['io'],
                    "Reportsite_URL" => "http://report-site.com/c/{$d['io']}/{$d['counter']}",
                    "Destination_URL" => $d['dest_url'],
                    "CampaignStartDate" => $d['campaign_start_datetime'],
                    "ClickCount" => $d['click_count'],
                    "CampaignName" => $d['campaign_name'],
                    //"RealURL" => $myUrl[0],
                    //"UniqueCnt" => (int)$this->Campclick_model->io_tracking_unique($d['io']),
                    //"MobileCnt" => (int)$this->Campclick_model->io_tracking_mobile($d['io']),
                );
            
                //$report[] = $line;
                //$report["report_" . $d['io']][] = $line;
                $report["report"][] = $line;
            }
            
            // update the record
            $this->response(array("status" => "SUCCESS", "reports" => $report, "timestamp" => date("Y-m-d H:i:s")), 200);
        }
    }
    
    function user_delete()  {
        // opt out of system
        if (! $this->get("id")) {
            $this->response(array("status" => "ERROR"), 404);
        } else {
            $this->Patient_model->remove($this->get('id'));
            
            $message = array('id' => $this->get('id'), "status" => "SUCCESS");
            $this->response($message, 200); // 200 being the HTTP response code
        }
    }
    
    function user_put() {
        // create new user.
        $required = array("id", "campaign_id", "phone_number", "medication_id");
        
        $err = "";
        foreach($required as $k) {
            if ($this->input->post($k) == "")
                $err .= $k . ",";
        }
        
        if (! empty($err))  {
            $this->response(array("error" => "{$err} required fields missing"), 404);
        } else {
            // load the user into db.
            $id = $this->Patient_model->create($user);
            
            $this->response(array("status" => "SUCCESS", "id" => $id), 200);
        }
    }
    
    function schedule_post() {
        if (! $this->get("id")) {
            $this->response(null, 404);
        } else {
            $schedule = array(
                "sun_1", "sun_2", "sun_3", "sun_4", "sun_5",
                "mon_1", "mon_2", "mon_3", "mon_4", "mon_5",
                "tue_1", "tue_2", "tue_3", "tue_4", "tue_5",
                "wed_1", "wed_2", "wed_3", "wed_4", "wed_5",
                "thu_1", "thu_2", "thu_3", "thu_4", "thu_5",
                "fri_1", "fri_2", "fri_3", "fri_4", "fri_5",
                "sat_1", "sat_2", "sat_3", "sat_4", "sat_5",
                "start_date",
                "end_date"
            );
            
            foreach($schedule as $k)    {
                if ($this->input->post($k) != "")   {
                    $insert[$k] = $this->input->post($k);
                } else {
                    $insert[$k] = ""; // blank it out
                }
            }
            
            $this->Patient_model->set_schedule($this->get("id"), $insert);
        }
    }
    
    function user_get() {
        // get the user from db
        
        if (! $this->get('id')) {
            $this->response(null, 404);
        } else {
            $user = array(); // this will be the populated array of data from the db.
            $this->response($user, 200);
        }
    }
    
    function call_put() {
        $required = array("phone_number", "campaign_id", "medication_id");
        
        // inject record in for immediate call
    }

	public function send_post()
	{
		var_dump($this->request->body);
	}


	public function send_put()
	{
		var_dump($this->put('foo'));
	}
}