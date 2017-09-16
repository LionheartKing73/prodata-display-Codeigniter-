<?php
class V2_map_users_network_model extends CI_Model {

    protected $CI;
    private $collection = 'v2_map_user_network';

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

    public function get_all_by_network_id($id){
        $result= $this->CI->db->get_where($this->collection, array("network_id"=>$id));

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function get_networks($userId) {

        return $this->CI->db
            ->select('v2_map_user_network.*, v2_networks.name as network_name')
            ->join('v2_networks', 'v2_networks.id = v2_map_user_network.network_id')
            ->get_where($this->collection, array("v2_map_user_network.user_id" => $userId, 'v2_networks.is_active' => 'Y'))
            ->result_array();

    }

    public function check_network_exist($user_id, $network_id, $campaign_type) {

        return $this->CI->db
            ->get_where($this->collection, array('user_id' => $user_id, 'network_id' => $network_id, 'campaign_type' => $campaign_type))
            ->result_array();


    }

    public function delete_network($id) {

        $this->CI->db->delete($this->collection, array('id' => $id));

    }
}
?>
