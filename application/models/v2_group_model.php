<?php
class V2_group_model extends CI_Model {

    protected $CI;
    private $collection = 'v2_groups';

    private $id;
    private $network_group_id;
    private $campaign_id;
    private $network_campaign_id;
    private $group_name;
    private $group_status;
    private $date_created;

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
        $this->CI->db->where("id", $id)
            ->update($this->collection, $data);
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

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function get_all_by_group_id($group_id){
        $result= $this->CI->db->get_where($this->collection, array("group_id"=>$group_id));

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function get_group_by_campaign_id($id){
        $result = $this->CI->db->get_where($this->collection, array("campaign_id"=>$id))->row_array();

        return $result;
    }
    public function copy_new_group($new_group){
        $new_group['id']=null;
        $this->CI->db->insert($this->collection, $new_group);
        $this->id = $this->CI->db->insert_id();
        return $this->id;
    }
}
?>
