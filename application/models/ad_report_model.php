<?php

class Ad_report_model extends CI_Model {

    function __construct() {
        $this->load->database();
        // Call the Model constructor
        parent::__construct();
    }

    /*
     * @description This function is used to create a new image ad
     */

    public function create_report($data) {
        $this->db->insert('ad_report', $data);
    }


    public function last_record_for_ad($ad_id){
        $result=$this->db->where("ad_id", $ad_id)
                        ->order_by("date_created", "asc")
                        ->limit(1)
                        ->get("ad_report");



        return $result->num_rows() ? $result->result_array() : [];
    }
}

?>
