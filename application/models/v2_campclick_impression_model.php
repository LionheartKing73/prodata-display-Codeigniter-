<?php
class V2_campclick_impression_model extends CI_Model {

    protected $CI;
    private $collection = 'v2_campclick_impressions';

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
            ->select(['SUM(impressions_count) as impression_count', "DATE_FORMAT(timestamp,'%Y-%m-%d') as date"])
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

    public function get_impression_count_for_combine($user_id, $so, $start_data, $end_data){

        $end_data = date('Y-m-d', strtotime("+1 day", strtotime($end_data)));

        $query = $this->CI->db->select(['SUM('.$this->collection.'.impressions_count) as impression_count', 'DATE_FORMAT('.$this->collection.'.timestamp,"%Y-%m-%d") as date'])->from($this->collection);

        $group_by = ['YEAR(timestamp)', 'MONTH(timestamp)', 'DAY(timestamp)'];

        if ($so){
            $query->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id')->where('v2_master_campaigns.so', $so);
            $group_by[] = 'v2_master_campaigns.so';
            if($user_id) {
                $query->where('v2_master_campaigns.userid', $user_id);
            }
        }

        $query->where('timestamp >=', $start_data)->where('timestamp <=', $end_data);

        return $query
            ->group_by($group_by)
            ->order_by('timestamp')
            ->get()
            ->result_array();

    }
    // need to check if this function use in platform
    public function get_campaign_click_count ($id){
        
        $impressions = $this->CI->db->select("SUM(impressions_count) as count")
            ->where('campaign_id', $id)
            ->get($this->collection)->row_array();
        
        return (int)$impressions['count'];
    }

    public function get_campaign_rtb_cost ($id){

        $impressions = $this->CI->db->select("SUM(win_price) as rtb_cost")
            ->where('campaign_id', $id)
            ->get($this->collection)->row_array();

        return $impressions['rtb_cost'];
    }

    public function get_campaign_impressions_count ($id){

        $impressions = $this->CI->db->select("SUM(impressions_count) as count")
            ->where('campaign_id', $id)
            ->get($this->collection)->row_array();

        return (int)$impressions['count'];
    }
    
    public function get_impression_count_hourly($campaign_id, $ad_id, $date = null){
        
        $date_now = date('Y-m-d H:i:s');
        
        $query = $this->CI->db
            ->select(['SUM(impressions_count) as count', "DATE_FORMAT(timestamp,'%H') as hour"])
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

    public function get_impression_count_hourly_for_combine($user_id, $so, $date = null){

        $date_now = date('Y-m-d H:i:s');
        //$date_now = '2016-10-21 16:00:00';

        $query = $this->CI->db->select(['SUM('.$this->collection.'.impressions_count) as count', 'DATE_FORMAT('.$this->collection.'.timestamp,"%H") as hour'])->from($this->collection);

        $group_by = [
            'HOUR(timestamp)'
        ];

        if ($so){
            $query->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id')->where('v2_master_campaigns.so', $so);
            $group_by[] = 'v2_master_campaigns.so';
            if($user_id) {
                $query->where('v2_master_campaigns.userid', $user_id);
            }
        }

        if ($date){
            $query->where("DATE(".$this->collection.".timestamp) = DATE('$date')");
        }
        else {
            $query->where("TIMESTAMPDIFF(HOUR, ".$this->collection.".timestamp, '{$date_now}') < 24");
        }

        return $query
            ->group_by($group_by)
            ->get()
            ->result_array();

    }

    // log count impressions got from iframe

    public function is_ad_exists($ad_id)
    {
        $query = $this->CI->db->get_where($this->collection, ['ad_id' => $ad_id, 'is_openrtb' => 1]);
        //var_dump($query->result_array()); exit;
        return $query->result_array()[0];
    }

    public function is_ad_valid($ad_id, $campaign_id)
    {
        $query = $this->CI->db->get_where($this->collection, ['ad_id' => $ad_id, 'campaign_id' => $campaign_id, 'is_openrtb' => 1]);

        return $query->result_array()[0];
    }

    public function get_impression_count_openrtb($id)
    {
        $this->CI->db->select('impressions_count');
        $this->CI->db->where('ad_id', $id);
        $this->CI->db->where('is_openrtb', '1');
        $query = $this->CI->db->get($this->collection);
        return $query->result_array()[0]['impressions_count'];
        
    }
    public function get_greate_then_1_impression_count_openrtb($id)
    {
        //$this->CI->db->select('impressions_count');
        $this->CI->db->where('ad_id', $id);
        $this->CI->db->where('is_openrtb', '1');
        $this->CI->db->where('impressions_count >', '1');
        $query = $this->CI->db->get($this->collection);
        return $query->row_array();

    }

    public function log_impressions_insert($id, $data)
    {
        $this->CI->db->insert($this->collection, $data);
    }

    public function log_impressions_update($id, $data)
    {
        $this->CI->db->where('ad_id', $id);
        $this->CI->db->where('is_openrtb', 1);
        $this->CI->db->update($this->collection, $data);
    }

    public function get_impressions_count_by_campaigns_types($user_id, $start_date, $end_date, $so){

        $query = $this->CI->db->select('SUM('.$this->collection.'.impressions_count) as total_impressions_count, v2_master_campaigns.campaign_type')
            ->from($this->collection)
            ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id');

        if($user_id) {
            $query->where('v2_master_campaigns.userid',$user_id);
        }
        if($start_date == $end_date) {
            $query->where('timestamp >=',$start_date);
        } else {
            $query->where('timestamp >=', $start_date)->where('timestamp <=', $end_date);
        }

        $group_by = ['v2_master_campaigns.campaign_type'];

        if ($so){
            $query->where('v2_master_campaigns.so', $so);
            $group_by[] = 'v2_master_campaigns.so';
        }
        $query->group_by($group_by);

        $result = $query->get();

        return $result->result_array();

    }

    
}
?>