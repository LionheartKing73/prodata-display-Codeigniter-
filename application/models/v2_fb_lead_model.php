<?php
class V2_fb_lead_model extends CI_Model {

    protected $CI;
    private $collection = 'v2_fb_leads';

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

    public function get_leads_by_campaign_id($id){
        $result = $this->CI->db->get_where($this->collection, array("campaign_id"=>$id))->result_array();

        return $result;
    }

    public function get_leads_by_ad_id($id){
        $result = $this->CI->db->get_where($this->collection, array("ad_id"=>$id))->result_array();

        return $result;
    }

    public function get_click_count_hourly($campaign_id, $date = null, $ad_id){

        $date_now = date('Y-m-d H:i:s');

        $query = $this->CI->db
            ->select(['COUNT(id) as count', "DATE_FORMAT(created_date,'%H') as hour", ""])
            ->where('campaign_id', $campaign_id);

        if ($date){
            $query->where("DATE(timestamp) = DATE('$date')");
        }
        else {
            $query->where("TIMESTAMPDIFF(HOUR, created_date, '{$date_now}') < 24");
        }

        if ($ad_id){
            $query->where('ad_id', $ad_id);
        }

        return $query
            ->group_by(['HOUR(created_date)'])
            ->get($this->collection)
            ->result_array();

    }

    public function get_click_count($campaign_id, $start_data, $end_data, $ad_id){

        $end_data = date('Y-m-d', strtotime("+1 day", strtotime($end_data)));

        $query = $this->CI->db
            ->select(['COUNT(id) as click_count', "DATE_FORMAT(created_date,'%Y-%m-%d') as date"])
            ->where('campaign_id', $campaign_id)
            ->where('created_date >=', $start_data)
            ->where('created_date <=', $end_data);

        if ($ad_id){
            $query->where('ad_id', $ad_id);
        }

        return $query
            ->group_by(['YEAR(created_date)', 'MONTH(created_date)', 'DAY(created_date)'])
            ->order_by('created_date')
            ->get($this->collection)
            ->result_array();

    }

    public function get_all_by_campaign_id_and_date($campaign_id, $start_data, $end_data, $ad_id){

        //$end_data = date('Y-m-d', strtotime("+1 day", strtotime($end_data)));

        $query = $this->CI->db
            ->select(['*'])
            ->where('campaign_id', $campaign_id)
            ->where('created_date >=', $start_data)
            ->where('created_date <=', $end_data);

        if ($ad_id){
            $query->where('ad_id', $ad_id);
        }

        return $query
            ->order_by('created_date')
            ->get($this->collection)
            ->result_array();

    }
}
?>
