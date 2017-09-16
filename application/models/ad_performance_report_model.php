<?php
class Ad_performance_report_model extends CI_Model {
    function __construct()
    {
        $this->load->database(); 
        // Call the Model constructor
        parent::__construct();
    }
         
    /*
     * @description This function is used to insert the ad performance report into the ad_performance_report table
    */
    public function createPerformanceReport($ad_id, $clicks, $impressions){  
        $query=$this->db->query("INSERT INTO `ad_performance_report`(`ad_id`, `clicks`, `impressions`, `date`) "
               . "VALUES ('".$this->db->escape_str($ad_id)."','".$this->db->escape_str($clicks)."','".$this->db->escape_str($impressions)."', now())");    
    }
}
?>
