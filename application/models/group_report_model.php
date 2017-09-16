<?php

class Group_report_model extends CI_Model {

    function __construct() {
        $this->load->database();
        // Call the Model constructor
        parent::__construct();
    }

    /*
     * @description This function is used to create a new image ad
     */

    public function create_report($data) {
        $this->db->insert('group_report', $data);
    }

    public function report_for_day($group_id, $day, $time){
        $result=$this->db->where("group_id", $group_id)
            ->where("DATE(date_created)", $day)
            ->where("HOUR(date_created)", $time)
            ->get("group_report");


        return $result->num_rows() ? $result->result_array() : [];
    }

    public function report_for_month($group_id, $date){
        $result=$this->db->where("group_id", $group_id)
            ->where("DATE(date_created)", $date)
            ->get("group_report");


        return $result->num_rows() ? $result->result_array() : [];
    }

    public function last_record_for_group($group_id){
        $result=$this->db->where("group_id", $group_id)
                        ->order_by("date_created", "asc")
                        ->limit(1)
                        ->get("group_report");


        return $result->num_rows() ? $result->result_array() : [];
    }
}

?>
