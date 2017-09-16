<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Monitor extends CI_Controller	{
	
    public function __construct()	{
		parent::__construct();

		$this->load->library("session");
		$this->load->library('ion_auth');
		$this->load->model("Monitor_model");
		
		$this->no_fraud = array(
			"66.0.218.10",
			"76.110.227.216",
			"50.198.249.13",
			"76.110.217.139"
		);
    }
    
    public function mobile($min_clicks_per_hour = 1200, $max_clicks_per_hour = 6000, $hour_low = 8, $hour_high = 23)    {
        $current_hour = date("H");
        
        // only operate within these monitoring times
        if ($current_hour >= $hour_low && $current_hour <= $hour_high)  {
            
            // 1- delete "old" data
            $this->Monitor_model->delete_old_data(12); // 3 hrs ago
            
            // 2- get clicks per (current) hour count
            $count = $this->Monitor_model->get_count();
            
            if ($count >= $min_clicks_per_hour && $count <= $max_clicks_per_hour) {
                // we're good, do nothing
            } else {
                mail("jkorkin@safedatatech.onmicrosoft.com", "Report-Site: MOBILE FEED ERROR", "Clicks Per Hour: {$count} of {$min_clicks_per_hour} / {$max_clicks_per_hour}.");
            }
        }
    }
    
    public function urlcheck()  {
        $this->Monitor_model->test_url();
    }
}
