<?php
class Group_list_model extends CI_Model {
    function __construct()
    {
        $this->load->database(); 
        // Call the Model constructor
        parent::__construct();
    }
         
    /*
     * @description This function is used to create a new group
     */
    public function createGroup($data){
        $this->db->insert('group_list', $data);
        $id=$this->db->insert_id();

        return $id;
    }
    
    public function selectAllByAttributes($attributes){
        $this->db->select($attributes);
        $query = $this->db->get('group_list')->result_array();
      
       return $query;
    }

    public function wheatherIOExists($io){
        $result_display=$this->db->where("group_status !=", "REMOVED")
            ->where("io =", $io)
            ->get("group_list");

        $result_image=$this->db->where("io =", $io)
            ->get("take5_pending_campaigns");


        return ($result_display->num_rows() + $result_image->num_rows())? true: false;
    }

    public function get_scheduled_campaigns($date){
        $result=$this->db->where("status", 'scheduled')
            ->where("date <=", $date)
            ->get("group_list");

        return $result->num_rows() ? $result->result_array(): [];
    }

    public function update($id, $data){
        $this->db->where("id", $id)
            ->update("group_list", $data);
    }

    public function select_oldest_campaign(){
        $today=$today=date("Y-m-d H:i:s");

        $result=$this->db->query("SELECT TIMESTAMPDIFF(MONTH, (SELECT MIN(date_created) FROM group_list), '".$today."') AS months");

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function select_all_active_campaigns(){
        $result= $this->db->get_where("group_list", array("status"=>"active"));

        return $result->num_rows() ? $result->result_array() : [];
    }
    public function select_all(){
        $result= $this->db->get("group_list");

        return $result->num_rows() ? $result->result_array() : [];
    }
    public function select_all_disapproved_groups($user_id){
        $result=$this->db->select("*")
            ->join("group_list", "group_list.id=ad_list.group_id")
            ->where("group_list.user_id", $user_id)
            ->where("group_list.group_status !=", "REMOVED")
            ->where("ad_list.ad_status != ", 'REMOVED')
            ->where("ad_list.approval_status", "DISAPPROVED")
            ->get("ad_list");


        return $result->num_rows() ? $result->result_array() : [];
    }

    public function get_io_by_id($id){
        $result= $this->db->get_where("group_list", array("id"=>$id));

        return $result->num_rows() ? $result->result_array() : [];
    }
    public function get_io_by_group_id($group_id){
        $result= $this->db->get_where("group_list", array("group_id"=>$group_id));

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function get_group_id_by_io($io){
        $result= $this->db->get_where("group_list", array("io"=>$io));

        return $result->num_rows() ? $result->result_array() : [];
    }
    public function select_all_existing_groups($user_id){
        $result= $this->db->where("group_status != ", "REMOVED")
                ->where("group_list.user_id", $user_id)
                ->get("group_list");

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function getIoForThreeMonths($min_date){
        $result= $this->db->where("group_status != ", "REMOVED")
            ->where("group_list.date_created >=", $min_date)
            ->get("group_list");

        return $result->num_rows() ? $result->result_array() : [];
    }


}
?>
