<?php
class V2_campclick_like_model extends CI_Model {

    protected $CI;
    private $collection = 'v2_campclick_likes';

    private $id;
    private $ip_address;
    private $user_agent;
    private $timestamp;
    private $is_mobile;
    private $web_browser;
    private $mobile_device;
    private $platform;
    private $referrer;
    private $pixel_id;
    private $conversion_value;
    private $is_geo;
    private $geo_country;
    private $geo_region;
    private $is_fraud;
    private $geo_lat;
    private $referrer_host;
    private $geo_lon;
    private $campaign_id;
    private $ad_id;

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

    public function get_group_by_campaign_id($id){

        $result= $this->CI->db->get_where("group_list", array("campaign_id"=>$id));
        return $result->num_rows() ? $result->result_array() : [];
    }

    public function get_campaign_impression_count_between_date ($campaign_id, $start_date){

        $impressions = $this->CI->db->select("COUNT(*) AS impressions_count")
            ->where('campaign_id',$campaign_id)
            ->where("timestamp BETWEEN '{$start_date}' AND NOW()")
            ->get($this->collection)->row_array();

        return (int)$impressions['impressions_count'];

    }
    
    public function get_impression_count($campaign_id, $start_data, $end_data, $ad_id){
        
        $end_data = date('Y-m-d', strtotime("+1 day", strtotime($end_data)));

        $query = $this->CI->db
            ->select(['SUM(likes_count) as click_count', "DATE_FORMAT(timestamp,'%Y-%m-%d') as date"])
            ->where('campaign_id', $campaign_id)
            ->where('timestamp >=', $start_data)
            ->where('timestamp <=', $end_data);
        
        if ($ad_id){
            $query->where('ad_id', $ad_id);
        }
        
        return $query
            ->group_by(['YEAR(timestamp)', 'MONTH(timestamp)', 'DAY(timestamp)'])
            ->order_by('timestamp')
            ->get($this->collection)
            ->result_array();
                  
    }
    
    public function get_campaign_click_count ($id){
        
        $impressions = $this->CI->db->select("SUM(impressions_count) as count")
            ->where('campaign_id', $id)
            ->get($this->collection)->row_array();
        
        return (int)$impressions['count'];
    }
    
    public function get_impression_count_hourly($campaign_id, $ad_id, $date = null){
        
        $date_now = date('Y-m-d H:i:s');
        
        $query = $this->CI->db
            ->select(['SUM(likes_count) as count', "DATE_FORMAT(timestamp,'%H') as hour"])
            ->where('campaign_id', $campaign_id);
        
        if ($date){
            $query->where("DATE(timestamp) = DATE('$date')");
        }
        else {
            $query->where("TIMESTAMPDIFF(HOUR, timestamp, '{$date_now}') < 24");
        }
            
        
        if ($ad_id){
            $query->where('ad_id', $ad_id);
        }
        
        return $query
            ->group_by(['HOUR(timestamp)'])
            ->get($this->collection)
            ->result_array();
        
    }
        

}
?>