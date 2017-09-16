<?php
class Userlist_io_model extends CI_Model {
    private $collection = "userlist_io";
    function __construct()
    {
        $this->load->database();
        // Call the Model constructor
        parent::__construct();
    }

    /*
     * @description This function is used to get all users
     * @return array users list
     */
    public function get_userlist_from_io($io) {
        $query = $this->db->query("SELECT * FROM userlist_io WHERE io = ?", array($io));

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }
    
    public function get_userlist_from_io_by_user_id($user_id){
        return $this->db->get_where("userlist_io", ["user_id"=>$user_id])->result_array();
    }

    public function get_userlist_by_user_id_and_network_id($data){
        return $this->db->get_where("userlist_io", $data)->result_array();
    }

    public function get_userlist_by_campaign_id($id){
        $query = $this->db->query("SELECT * FROM userlist_io WHERE campaign_id = ?", array($id));

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }
    public function get_lookalike_userlist_by_campaign_id($id){
        $query = $this->db->query("SELECT * FROM userlist_io WHERE campaign_id = ? AND type != 'CUSTOM' AND type != 'EMAIL'", array($id));

        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return array();
        }
    }
    public function get_email_userlist_by_campaign_id($id){
        $query = $this->db->query("SELECT * FROM userlist_io WHERE campaign_id = ? AND type = 'EMAIL'", array($id));

        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return array();
        }
    }

    /*
     * @description This function is used to create new user list
     * @param IO insertion order id
     * @return boolean
     */
    public function create_userlist_io($io, $campaignId, $remarketingListId, $snipedCode, $networkId, $userId, $date_created=null){
        $sql = "INSERT INTO userlist_io (io, campaign_id, remarketing_list_id, sniped_code, network_id, user_id) VALUES('".$this->db->escape_str($io)."', '".$this->db->escape_str($campaignId)."', '".$this->db->escape_str($remarketingListId)."', '".$this->db->escape_str($snipedCode)."', '".$this->db->escape_str($networkId)."', '".$this->db->escape_str($userId)."')";
        $response = $this->db->query($sql);
        return $response;
    }

    public function create_lookalike_userlist_io($data){
        $this->db->insert($this->collection, $data);
        $id = $this->db->insert_id();

        if ($id > 0)  {
            $this->id = $id;
            return $this->id;
        } else {
            return false;
        }
    }
    public function create_email_userlist_io($data){
        $this->db->insert($this->collection, $data);
        $id = $this->db->insert_id();

        if ($id > 0)  {
            $this->id = $id;
            return $this->id;
        } else {
            return false;
        }
    }

    public function get_all_users_from_io(){
        $result=$this->db->get("userlist_io");

        return $result->num_rows()? $result->result_array(): [];
    }

    public function get_all_io($like){
        $result=$this->db->select('io')->like('io', $like )->get("userlist_io");
        return $result->num_rows()? $result->result_array(): [];
    }

    public function get_io_by_name($io){
        $result=$this->db->get_where("userlist_io", ["io"=>$io]);

        return $result->num_rows()? $result->result_array(): [];
    }

    public function get_criteria_id_list($list) {
        $result = $this->db->select('remarketing_list_id')->where_in('io', $list)->order_by('id DESC')->group_by('io')->get($this->collection)->result_array();

        foreach($result as $criteria){
            $criteria_array[] = $criteria['remarketing_list_id'];
        }
        return $criteria_array;
    }

    public function get_criteria_id_list_for_fb($list) {
        return $this->db->select('remarketing_list_id as id')->where_in('io', $list)->order_by('id DESC')->group_by('io')->get($this->collection)->result_array();
    }

    public function get_lookalike_criteria_id_list_for_fb($list) {
        return $this->db->select('remarketing_list_id as id')->where_in('name', $list)->order_by('id DESC')->group_by('name')->get($this->collection)->result_array();
    }

    public function get_email_criteria_id_list_for_fb($list) {
        return $this->db->select('remarketing_list_id as id')->where_in('name', $list)->order_by('id DESC')->group_by('name')->get($this->collection)->result_array();
    }

    public function get_snippet_code($user_id, $campaign_id = null, $origin_domain) {
        if(!$campaign_id) {
            $origin_domain_with_www = 'www.'.$origin_domain;
            $origin_domain_with_http = 'http://'.$origin_domain;
            $origin_domain_with_slash = $origin_domain.'/';

            $sql = "SELECT `sniped_code` FROM (`userlist_io`) INNER JOIN `v2_master_campaigns` ON `userlist_io`.`campaign_id` = `v2_master_campaigns`.`id` AND userlist_io.user_id = $user_id AND v2_master_campaigns.campaign_status = 'ACTIVE' 
                    INNER JOIN `v2_ads` ON `v2_ads`.`campaign_id` = `v2_master_campaigns`.`id` AND (`v2_ads`.`display_url` = '$origin_domain' OR `v2_ads`.`display_url` = '$origin_domain_with_www' OR `v2_ads`.`display_url` = '$origin_domain_with_http' OR `v2_ads`.`display_url` = '$origin_domain_with_slash')";
            $query = $this->db->query($sql);

        } else {
            $this->db->select('sniped_code');
            $this->db->join('v2_master_campaigns', "userlist_io.campaign_id = v2_master_campaigns.id AND v2_master_campaigns.id IN ($campaign_id) AND userlist_io.user_id = $user_id AND v2_master_campaigns.campaign_status = 'ACTIVE'", 'inner');
            $query = $this->db->get($this->collection);
        }

        return $query->result_array();
    }

    public function update($id, $data) {
        $result = $this->db->where("id", $id)->update($this->collection, $data);
        return $result;
    }
//
//SELECT sniped_code FROM userlist_io
//JOIN v2_master_campaigns
//ON userlist_io.`campaign_id` = v2_master_campaigns.id
//AND userlist_io.`user_id` = 32
//AND v2_master_campaigns.`campaign_status` = 'ACTIVE'
}
?>
