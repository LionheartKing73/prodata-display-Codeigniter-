<?php
class V2_users_multiple_networks_model extends CI_Model {

    protected $CI;
    private $collection = 'v2_users_multiple_networks';

    private $id;
    private $user_id;
    private $network_id;
    private $campaign_type;

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

    public function select_all(){
        $result= $this->CI->db->get($this->collection);

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function get_by_id($id){
        $result= $this->CI->db->get_where($this->collection, array("id"=>$id));

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function get_networks_by_user_id($id){
        $result= $this->CI->db->get_where($this->collection, array("user_id"=>$id));

        return $result->num_rows() ? $result->result_array() : [];

        return $this->CI->db
            ->select($this->collection.'.*, v2_networks.name as network_name, v2_networks.min_daily_budget as min_budget')
            ->join('v2_networks', 'v2_networks.id = '.$this->collection.'.network_id')
            ->get_where($this->collection, array($this->collection.".user_id" => $userId, $this->collection.'.is_active' => 1))
            ->result_array();
    }

    public function get_all_by_network_id($id){
        $result= $this->CI->db->get_where($this->collection, array("network_id"=>$id));

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function get_multiple_networks($userId) {

        return $this->CI->db
            ->select($this->collection.'.*, v2_networks.name as network_name, v2_networks.min_daily_budget as min_budget')
            ->join('v2_networks', 'v2_networks.id = '.$this->collection.'.general_network_id')
            ->get_where($this->collection, array($this->collection.".user_id" => $userId, $this->collection.'.is_active' => 1))
            ->result_array();

    }

    public function check_network_exist($user_id, $network_id, $multiple_network_id) {

        return $this->CI->db
            ->get_where($this->collection, array('user_id' => $user_id, 'general_network_id' => $network_id, 'multiple_network_id' => $multiple_network_id))
            ->result_array();


    }

    public function delete($id) {

        $this->CI->db->delete($this->collection, array('id' => $id));

    }
}
?>
