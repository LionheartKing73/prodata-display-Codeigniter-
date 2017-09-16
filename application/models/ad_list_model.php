<?php

class Ad_list_model extends CI_Model {

    function __construct() {
        $this->load->database();
        // Call the Model constructor
        parent::__construct();
    }

    /*
     * @description This function is used to create a new image ad
     */

    public function createAd($data) {
        $this->db->insert('ad_list', $data);
    }

    public function removeAd($id) {
        $this->db->delete('ad_list', array('ad_id' => $id));
    }

    public function updateAdStatus($adId, $adStatus) {
        $this->db->where('ad_id', $adId);
        $this->db->update('ad_list', array("ad_status"=>$adStatus));
    }
    public function select_all(){
        $result=$this->db->get("ad_list");
        
        return $result->num_rows() ? $result->result_array() : [];
    }
    public function update_ad_status_by_group_id($group_id, $ad_status){
        $this->db->where('group_id', $group_id)
            ->update('ad_list', ["ad_status"=>$ad_status]);
    }
    public function update_approval_stats_by_id($id, $app_stat, $disapp_res){
        $this->db->where('id', $id)
            ->update('ad_list', ["approval_status"=>$app_stat, "disapproval_reasons"=>$disapp_res]);
    }
    public function get_ad_by_group_id($group_id){
        $result=$this->db->get_where("ad_list", ["group_id"=>$group_id]);

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function update($id, $data){
        $this->db->where("id", $id)
            ->update("ad_list", $data);
    }
    public function select_all_active_ads(){
        $result=$this->db->select("ad_list.id, ad_list.adword_group_id, ad_list.ad_name, ad_list.ad_id, ad_list.ad_status, ad_list.disapproval_reasons,
        ad_list.approval_status, ad_list.img_name")
            ->join("group_list", "group_list.id=ad_list.group_id")
            ->where("group_list.group_status !=", "REMOVED")
            ->where("ad_list.ad_status != ", 'REMOVED')
            ->get("ad_list");

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function select_by_pk($id){
        $result = $this->db->get_where("ad_list", ["id"=>$id]);

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function select_by_pk_joined_with_groups($id){
        $result=$this->db->select("*")
            ->join("group_list", "group_list.id=ad_list.group_id")
            ->where("ad_list.id", $id)
            ->get("ad_list");

        return $result->num_rows() ? $result->result_array() : [];
    }
    public function get_existing_ad_by_group_id($group_id){
        $result=$this->db->where("group_id", $group_id)
            ->where("ad_status !=", "REMOVED")
            ->get("ad_list");


        return $result->num_rows() ? $result->result_array() : [];
    }

}

?>
