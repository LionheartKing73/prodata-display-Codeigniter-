<?php
class V2_fb_form_model extends CI_Model {

    protected $CI;
    private $collection = 'v2_fb_forms';

    function __construct() {
        parent::__construct();
        $this->CI =& get_instance();
        $this->CI->load->database();
    }
         
    /*
     * @description This function is used to create a new group
     */
    public function create($data){
        $this->CI->db->insert($this->collection, $data);
        $id=$this->CI->db->insert_id();

        return $id;
    }

    public function update($id, $data){
        return $this->CI->db->where("id", $id)->update($this->collection, $data);
    }

    public function update_multiple($campaign_id, $data){
        $this->CI->db->where("campaign_id", $campaign_id)->where("multiple_campaign_id IS NOT NULL")
            ->update($this->collection, $data);
    }

    public function select_all(){
        $result= $this->CI->db->get($this->collection);

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function get_by_id($id){
        $result= $this->CI->db->get_where($this->collection, array("id"=>$id));

        return $result->num_rows() ? $result->row_array() : [];
    }

    public function get_all_by_group_id($group_id){
        $result= $this->CI->db->get_where($this->collection, array("group_id"=>$group_id));

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function get_all_by_user_id($user_id){
        $result= $this->CI->db->get_where($this->collection, array("user_id"=>$user_id));

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function get_form_by_campaign_id($id){
        $result = $this->CI->db->get_where($this->collection, array("campaign_id"=>$id))->row_array();

        return $result;
    }
}
?>
