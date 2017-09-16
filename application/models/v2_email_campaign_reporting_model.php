<?php
class V2_email_campaign_reporting_model extends CI_Model {

    protected $CI;
    private $collection = 'v2_email_campaign_reporting';

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
     public function get_so_by_campaign_id($id){
        $r = $this->CI->db->query("SELECT DISTINCT(campaign_so) FROM v2_email_campaign_reporting WHERE campaign_id ='{$id}' AND `campaign_so` is not null ORDER BY campaign_so ASC");

        if ($r->num_rows() > 0) {
            return $r->result_array();
        } else {
            return array();
        }
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
    
    public function get_clicks_count($campaign_id, $start_data, $end_data, $ad_id=null){
        
        $end_data = date('Y-m-d', strtotime("+1 day", strtotime($end_data)));

        $query = $this->CI->db
            ->select(["SUM(clicks_count) AS clicks_count", "SUM(impressions_count) AS impressions_count", "SUM(unique_clicks_count) AS unique_clicks_count",  "SUM(mobile_clicks_count) AS mobile_clicks_count", "DATE_FORMAT(date_created,'%Y-%m-%d') as date"])
            // ->where('campaign_id', $campaign_id)
            ->where('date_created >=', $start_data)
            ->where('date_created <=', $end_data)
            ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id');
        
        if ($ad_id){
            $query->where('ad_id', $ad_id);
        }
        
        return $query
            ->group_by(['YEAR(date_created)', 'MONTH(date_created)', 'DAY(date_created)'])
            ->order_by('date_created')
            ->get($this->collection)
            ->result_array();
                  
    }
    // need to check if this function use in platform
    public function get_campaign_click_count ($id){
        
        $impressions = $this->CI->db->select("SUM(impressions_count) as count")
            ->where('campaign_id', $id)
            ->get($this->collection)->row_array();
        
        return (int)$impressions['count'];
    }

    public function get_campaign_impressions_count ($id){

        $impressions = $this->CI->db->select("SUM(impressions_count) as count")
            ->where('campaign_id', $id)
            ->get($this->collection)->row_array();

        return (int)$impressions['count'];
    }
    public function get_clicks_count_by_campaign_id ($id){

        $clicks = $this->CI->db->select("SUM(clicks_count) AS clicks_count, SUM(impressions_count) AS impressions_count, SUM(unique_clicks_count) AS unique_clicks_count, SUM(mobile_clicks_count) AS mobile_clicks_count, reportsite_url, destination_url")
            ->where('campaign_id', $id)
            ->get($this->collection)->row_array();

        return $clicks;
    }

    public function get_total_clicks_count_by_campaign_id ($id){

        $clicks = $this->CI->db->select("SUM(clicks_count) AS clicks_count, SUM(impressions_count) AS impressions_count, SUM(unique_clicks_count) AS unique_clicks_count, SUM(mobile_clicks_count) AS mobile_clicks_count, reportsite_url, destination_url")
            ->where('campaign_id', $id)
            ->group_by('campaign_id')
            ->get($this->collection)->row_array();

        return $clicks;
    }

    public function get_clicks_count_hourly($campaign_id, $ad_id=null, $date = null){
        
        $date_now = date('Y-m-d H:i:s');
        
        $query = $this->CI->db
            ->select(["SUM(impressions_count) AS impressions_count", "SUM(unique_clicks_count) AS unique_clicks_count", "SUM(mobile_clicks_count) AS mobile_clicks_count", "DATE_FORMAT(date_created,'%H') as hour"])
            ->where('campaign_id', $campaign_id);
        
        if ($date){
            $query->where("DATE(date_created) = DATE('$date')");
        }
        else {
            $query->where("TIMESTAMPDIFF(HOUR, date_created, '{$date_now}') < 24");
        }
            
        
        if ($ad_id){
            $query->where('ad_id', $ad_id);
        }
        
        return $query
            ->group_by(['HOUR(date_created)'])
            ->get($this->collection)
            ->result_array();
        
    }

    public function get_clicks_count_hourly_by_hour($campaign_id, $ad_id=null, $date = null,$hour){

        $date_now = date('Y-m-d H:i:s');

        $query = $this->CI->db
            ->select(["SUM(unique_clicks_count) + SUM(mobile_clicks_count) AS total"])
            ->where('campaign_id', $campaign_id);

        if ($date){
            $query->where("DATE(date_created) = DATE('$date')");
        }
        else {
            $query->where("TIMESTAMPDIFF(HOUR, date_created, '{$date_now}') < ".$hour);
        }


        if ($ad_id){
            $query->where('ad_id', $ad_id);
        }

        return $query
            ->group_by(['HOUR(date_created)'])
            ->get($this->collection)
            ->result_array();

    }

    public function get_click_count_hourly_for_combine($user_id, $so, $date = null){

        $date_now = date('Y-m-d H:i:s');

        $query = $this->CI->db
            ->select(['COUNT(*) as count', "DATE_FORMAT(date_created,'%H') as hour", 'v2_master_campaigns.campaign_type as campaign_type'])
//            ->where('campaign_id', $campaign_id);
            ->from($this->collection)
            ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id');

        if ($date){
            $query->where("DATE(date_created) = DATE('$date')");
        }
        else {
            $query->where("TIMESTAMPDIFF(HOUR, date_created, '{$date_now}') < 24");
        }

        $group_by = ['HOUR(date_created)','v2_master_campaigns.campaign_type'];

        if ($so){
            $query->where('v2_master_campaigns.so', $so);
            $group_by[] = 'v2_master_campaigns.so';
            if($user_id) {
                $query->where('v2_master_campaigns.userid', $user_id);
            }
        }

        $result = $query->group_by($group_by)->get()->result_array();

        return $result;

    }

    public function get_click_count_for_combine($user_id, $so, $start_date, $end_date){

        $end_data = date('Y-m-d', strtotime("+1 day", strtotime($end_date)));

        $query = $this->CI->db
            ->select(['COUNT('.$this->collection.'.id) as count', 'DATE_FORMAT('.$this->collection.'.date_created,"%Y-%m-%d") as date', 'v2_master_campaigns.campaign_type as campaign_type'])
            ->from($this->collection)
            ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id');

        $group_by = ['YEAR(date_created)', 'MONTH(date_created)', 'DAY(date_created)', 'v2_master_campaigns.campaign_type'];


        if ($so){
            $query->where('v2_master_campaigns.so', $so);
            $group_by[] = 'v2_master_campaigns.so';
            if($user_id) {
                $query->where('v2_master_campaigns.userid', $user_id);
            }
        }

        $query->where('date_created >=', $start_date)->where('date_created <=', $end_date);

        return $query
            ->group_by($group_by)
            ->order_by('date_created')
            ->get()
            ->result_array();

    }

    public function get_total_clicks_count_by_campaign_id_for_tracking ($id){

        $clicks = $this->CI->db->select("SUM(v2_email_campaign_link_reporting.clicks_count) AS clicks_count, SUM(impressions_count) AS impressions_count, SUM(unique_clicks_count) AS unique_clicks_count, SUM(mobile_clicks_count) AS mobile_clicks_counts, reportsite_url, destination_url")
            ->where('campaign_id', $id)
            ->group_by('campaign_id')
            ->get($this->collection)
            ->join('v2_campclick_clicks','v2_email_campaign_link_reporting.campaign_id = '.$this->collection.'.id', 'left')
            ->row_array();

        return $clicks;
    }

    public function get_clicks_count_all($start_data, $end_data, $so){


         $query = $this->CI->db->select('count(*) as total_clicks_count,Sum(impressions_count) as total_impressions, v2_master_campaigns.campaign_type')
        ->from($this->collection)
        ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id');

        if($start_data == $end_data) {
            $query->where('date_created >=',$start_data);
        } else {
            $query->where('date_created >=', $start_data)->where('date_created <=', $end_data);
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