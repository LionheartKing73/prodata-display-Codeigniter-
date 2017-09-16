<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rtb extends CI_Controller	{
	public $viewArray = array();
	
    public function __construct()	{
		parent::__construct();

		$this->load->model("Trafficshape_model");
		$this->load->model("Ppcnetworks_model");
		$this->load->model("Ad_model");
		$this->load->model("Finditquick_model");
		$this->load->model("Campclick_model");
		
		$this->load->library("parser");
		$this->load->library("session");
		$this->load->library('ion_auth');
    }
    
    public function campaign_list() {
        $this->parser->parse("rtb/campaign_list.php", $this->viewArray);
    }
    
    public function create_ad() {
        // create the PPC advertisement
        /*
        $this->Ad_model->title = "Test Title for Campaign";
        $this->Ad_model->description = "This is a test description. Its Longer.";
        $this->Ad_model->category = "104";
        $this->Ad_model->campaign_name = "** TEST CAMPAIGN **";
        $this->Ad_model->destination_url = "http://report-site.com/r/JASONTEST1";
        $this->Ad_model->display_url = "http://www.safedatatech.com";
        $this->Ad_model->target_radius = "25";
        $this->Ad_model->bid = 0.0025;
        $this->Ad_model->daily_cap = 20.00;
        $ad_id = $this->Ad_model->create();
        
        // create the campaign (on report-site)
        $this->Campclick_model->name = "** TEST CAMPAIGN **";
        $this->Campclick_model->io = "JASONTEST1";
        $this->Campclick_model->message = "none";
        $this->Campclick_model->conversion_tracking = "N";
        $this->Campclick_model->is_geo = "N";
        $this->Campclick_model->vendor_id = 1;
        $this->Campclick_model->campaign_start_datetime = "2014-10-29 13:53:00";
        $this->Campclick_model->max_clicks = 1000;
        $this->Campclick_model->userid = 5;
        $this->Campclick_model->is_traffic_shape = "Y";
        $this->Campclick_model->ppc_network = "FIQ";
        $campaignId = $this->Campclick_model->create();
        //$campaignId = 3800;
        
        $this->Ppcnetworks_model->io = "JASONTEST1";
        $this->Ppcnetworks_model->ppc_network_id = null;
        $this->Ppcnetworks_model->ppc_network = "FIQ";
        $this->Ppcnetworks_model->status = "P";
        $this->Ppcnetworks_model->bid_rate = 0.0001;
        $this->Ppcnetworks_model->ad_id = $ad_id;
        $this->Ppcnetworks_model->create();
        */
        
        $response = $this->Finditquick_model->create_ad("JASONTEST1", 1);
        print_r($response);
    }
    
    public function link_ad_to_io($io = "", $id = "") {
        $this->Ppcnetworks_model->io = (string)$io;
        $this->Ppcnetworks_model->ad_id = (int)$id;
        $this->Ppcnetworks_model->set_ad();
        
        
    }
    
    /*
    public function build() {
        $this->Trafficshape_model->create_db_table();
    }
    */

    public function detect_traffic_and_adjust_bid() {
        $this->Trafficshape_model->detect_traffic_and_adjust_bid();
    }
    
    public function click_cap($io = "") {
        $this->Campclick_model->io = $io;
        $c = $this->Campclick_model->get_current_click_cap();
        
        print_r($c);
    }
}

?>