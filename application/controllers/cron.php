<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends CI_Controller	{
	public $viewArray = array();
	
    public function __construct()	{
		parent::__construct();

		$this->load->helper("url");
		$this->load->helper('cookie');
		$this->load->library('user_agent');
		
		$this->load->library("parser");
		$this->load->library("session");
		$this->load->library('ion_auth');
		$this->load->model("Campclick_model");
		$this->load->model("Domains_model");
		$this->load->model("Vendor_model");
		$this->load->model("Report_model");
		$this->load->model("Monitor_model");
		$this->load->model("Finditquick_model");
		$this->load->model("Billing_model");
		
		$this->viewArray['current_url'] = current_url();
		$this->viewArray['base_url'] = base_url();
		$this->viewArray['site_url'] = site_url();
		
    }
    
    public function update_trafficshape()   {
        $this->load->model("Trafficshape_model");
        
        $processing = $this->Campclick_model->get_campaign_list(null, "Y", "AND campaign_is_started='Y' AND campaign_is_complete='N'");
        
        foreach($processing as $p)  {
            // skip the campaigns which are NOT traffic shaped
            if ($p['is_traffic_shape'] == "N")
                continue;
            
            print_r($p);
        }
    }
    
    public function queue_invoices_to_quickbooks()  {
        $this->Billing_model->build_invoice_queue();
    }
    
    public function get_ads_progression()   {
        
    }
    
    public function mark_ads_completed()	{
    	//$this->load->model("api_model");
    	//$this->api_model->mark_ads_completed(); // this is for api ads
    	$this->Finditquick_model->mark_ads_completed();
        $this->Campclick_model->mark_ads_completed(); // this is for offline ads
    }
    
    public function mark_ads_processing()	{
    	//$this->load->model("api_model");
    	//$this->api_model->mark_ads_processing();
    	$this->Finditquick_model->mark_ads_processing();
    }
    
    public function check_clicks()	{
    	$click_report = $this->Report_model->progress_update();
    	
    	$filename = "ClickBreakdownReport-" . date("Y-m-d");
    	    	
    	$msg = "IO,Campaign Name,Ordered Clicks,Total Clicks,Start Date,Complete?,Vendor,Dates (Clicks)\n";
    	foreach($click_report as $c)	{
    		$msg .= $c['io'] . ",";
    		$msg .= $c['camp_name'] . ",";
    		$msg .= $c['max_clicks'] . ",";
    		$msg .= $c['total_clicks'] . ",";
    		$msg .= $c['campaign_start_datetime'] . ",";
    		$msg .= $c['is_complete'] . ",";
    		$msg .= $c['vendor'] . ",";
    		
    		foreach($c['dates'] as $d)	{
    			$msg .= $d['dt'] . " (" . $d['cnt'] . "),";
    		}
    		$msg .= "\n";
    	}

    	// write out the file
    	file_put_contents("/tmp/{$filename}.csv", $msg);

    	$config['protocol'] = 'sendmail';
    	$config['mailpath'] = '/usr/sbin/sendmail';
    	$config['charset'] = 'utf-8';
    	$config['wordwrap'] = TRUE;
    	$config['mailtype'] = 'html';
    	$config['priority'] = 1;

    	$this->load->library('email');
    	$this->email->initialize($config);

    	$this->email->from('noreply@report-site.com', 'Report-Site No Reply');
    	$this->email->to('jkorkin@safedatatech.onmicrosoft.com');
    	$this->email->cc('fulfillment@take5s.com', 'orderCR@take5s.com');
    	$this->email->subject('ProDataFeed - Report-Site: Daily Campaign Update');
    	$this->email->message('Attached is the daily report campaign update from Report-Site.com.  This is a CSV DELIMITED file; open in Excel.');
    	$this->email->attach("/tmp/{$filename}.csv");
    	$this->email->send();
    }
    
    public function load_zips()	{
    	$this->load->model("zip_model");
    	$this->zip_model->load_zip();
    }

    public function load_geo()	{
    	$this->load->model("zip_model");
    	$this->zip_model->load_geo();
    }
    
    public function match_zip_to_geo($zip = "")	{
    	$this->load->model("zip_model");
    	$geo = $this->zip_model->match_zip_to_geo($zip);
    	
    	print_r($geo);
    }

    public function find_locations($zip = "", $distance = 30)	{
    	$this->load->model("zip_model");
    	$geo = $this->zip_model->find_locations($zip, $distance);
    	 
    	print_r($geo);
    }
    
    public function mark_offline_ads_completed()	{
    	$this->Campclick_model->mark_ads_completed();
    }
    
    public function check_fulfilled_status()    {
        $this->Campclick_model->check_fulfilled_status();
    }
    
    public function make_campaign_live($io = "")    {
        
        $this->load->model("Take5_Campaign_Pending_Model");
        $this->Take5_Campaign_Pending_Model->make_campaign_live($io);
    }
    
    public function update_link_fulfilled_status()   {
        $this->Campclick_model->update_fulfilled_status();
    }
    
    /**
     * Controls the REAL TIME BIDDING functionality across all networks
     * 
     * Add new networks here when we add them to the system
     */
    public function network_realtime_bid()  {
        $this->Finditquick_model->real_time_bid_adjustment();
    }
    
}
