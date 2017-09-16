<?php
class V2_campclick_click_model extends CI_Model {

    protected $CI;
    private $collection = 'v2_campclick_clicks';

    private $id;
    private $ip_address;
    private $user_agent;
    private $timestamp;
    private $is_mobile;
    private $web_browser;
    private $mobile_device;
    private $platform;
    private $referrer;
    private $link_id;
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
    private $fraud_score = 0;

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
        $id = $this->CI->db->insert_id();

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
    
    public function get_campaign_click_count ($id){

        return $this->CI->db->get_where($this->collection, ['campaign_id' => $id])->num_rows();

    }

    public function get_ad_click_count ($id){

        return $this->CI->db->get_where($this->collection, ['ad_id' => $id])->num_rows();

    }

    public function get_campaign_click_count_between_date ($campaign_id, $start_date = null){

        $query = $this->CI->db->select("COUNT(*) AS click_count")
            ->where('campaign_id',$campaign_id);
        
        if ($start_date){
            $query->where("timestamp BETWEEN '{$start_date}' AND NOW()");
        }
        
                               
        $clicks = $query->get($this->collection)->row_array();

        return (int)$clicks['click_count'];

    }
    
    public function get_click_count($campaign_id, $start_data, $end_data, $ad_id){
        
        $end_data = date('Y-m-d', strtotime("+1 day", strtotime($end_data)));
        
        $query = $this->CI->db
            ->select(['COUNT(id) as click_count', "DATE_FORMAT(timestamp,'%Y-%m-%d') as date"])
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
    public function get_click_count_for_combine($user_id, $so, $start_date, $end_date){

        $end_data = date('Y-m-d', strtotime("+1 day", strtotime($end_date)));

        $query = $this->CI->db
            ->select(['COUNT('.$this->collection.'.id) as count', 'DATE_FORMAT('.$this->collection.'.timestamp,"%Y-%m-%d") as date', 'v2_master_campaigns.campaign_type as campaign_type'])
            ->from($this->collection)
            ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id');

        $group_by = ['YEAR(timestamp)', 'MONTH(timestamp)', 'DAY(timestamp)', 'v2_master_campaigns.campaign_type'];

        if ($so){
            $query->where('v2_master_campaigns.so', $so);
            $group_by[] = 'v2_master_campaigns.so';
            if($user_id) {
                $query->where('v2_master_campaigns.userid', $user_id);
            }
        }

        $query->where('timestamp >=', $start_date)->where('timestamp <=', $end_date);

        return $query
            ->group_by($group_by)
            ->order_by('timestamp')
            ->get()
            ->result_array();

    }
    
    public function get_pie_chart_data($campaign_id, $start_data, $end_data, $ad_id){
        
        $end_data = date('Y-m-d', strtotime("+1 day", strtotime($end_data)));
        
        $query = $this->CI->db 
            ->select(['COUNT(id) as click_count', "web_browser as data_filed"])
            ->where('campaign_id', $campaign_id)
            ->where('timestamp >=', $start_data)
            ->where('timestamp <=', $end_data);

        if ($ad_id){
            $query->where('ad_id', $ad_id);
        }
        
        $return_data['browsers_data'] = $query
            ->group_by(['web_browser'])
            ->order_by('web_browser')
            ->get($this->collection)
            ->result_array();
        
        $query = $this->CI->db 
            ->select(['COUNT(id) as click_count', "mobile_device as data_filed"])
            ->where(['campaign_id' => $campaign_id, 'is_mobile' => 'Y'])
            ->where('timestamp >=', $start_data)
            ->where('timestamp <=', $end_data);
        

        if ($ad_id){
            $query->where('ad_id', $ad_id);
        }
        
        $return_data['mobile_device'] = $query
            ->group_by(['mobile_device'])
            ->order_by('mobile_device')
            ->get($this->collection)
            ->result_array();
        
        $query = $this->CI->db 
            ->select(['COUNT(id) as click_count', "platform as data_filed"])
            ->where(['campaign_id' => $campaign_id])
            ->where('timestamp >=', $start_data)
            ->where('timestamp <=', $end_data);

        if ($ad_id){
            $query->where('ad_id', $ad_id);
        }
        
        $return_data['platforms'] = $query
            ->group_by(['platform'])
            ->order_by('platform')
            ->get($this->collection)
            ->result_array();
        
        /*
        $return_data['mobile_devices'] = $mobile_query
            ->select(['COUNT(id) as click_count', "mobile_device"])
            ->group_by(['mobile_device'])
            ->order_by('mobile_device')
            ->get($this->collection)
            ->result_array();
                
         * 
         */
        return $return_data;
        
    }
    
    public function get_count_by_radius($campaign_id, $radius, $lat, $lng, $ad_id = null){
        
        $having = ['distance <=' => $radius, 'campaign_id' => $campaign_id];
        
        if ($ad_id){
            $having['ad_id'] = $ad_id; 
        }
        
        $query = $this->CI->db
            ->select(["((acos(sin((" 
                . $lat . "*pi()/180)) * sin((`geo_lat`*pi()/180))+cos((" 
                . $lat . "*pi()/180)) * cos((`geo_lat`*pi()/180)) * cos(((" 
                . $lng . "- `geo_lon`)* pi()/180))))*180/pi())*60*1.1515  as distance",
                'ad_id',
                'campaign_id'
            ])
            ->having($having)
            ->get($this->collection)
            ->result_array();
        
        return count($query);
        
    }

    public function get_all_count_by_zip_and_radius($locations, $campaign_id, $ad_id = null){

        $params = ['campaign_id' => $campaign_id];

        if($ad_id) {
            $params['ad_id'] = $ad_id;
        }

        $clicks = $this->CI->db->where($params)->where('country IS NOT NULL')->get($this->collection)->result_array();
        
        foreach($locations as $key => $location) {
            $count = 0;
            foreach($clicks as $click) {

                $distance = ((acos(sin(($location['latitude'] * M_PI / 180)) * sin(($click['geo_lat'] * M_PI / 180)) + cos(($location['latitude'] * M_PI / 180)) * cos(($click['geo_lat'] * M_PI / 180)) * cos((($location['longitude'] - $click['geo_lon']) * M_PI / 180)))) * 180 / M_PI) * 60 * 1.1515;

                if($distance<=$location['radius']){
                    $count++;
                }

            }
            $locations[$key]['click_count'] = $count;
        }

        return $locations;
    }

    public function get_count_by_state($campaign_id, $start_data, $end_data, $ad_id){
        
        $end_data = date('Y-m-d', strtotime("+1 day", strtotime($end_data)));
        
        $query = $this->CI->db
            ->select(['COUNT(id) count', 'state'])
            ->where('campaign_id', $campaign_id)
            ->where('timestamp >=', $start_data)
            ->where('timestamp <=', $end_data);
        
        if ($ad_id){
            $query->where('ad_id', $ad_id);
        }
        
        return $query
            ->group_by(['state'])
            ->get($this->collection)
            ->result_array();
        
    }

    public function get_top_5_count_by_zip($campaign_id){

        $query = $this->CI->db
            ->select(['COUNT(id) clicks_count', 'postal_code'])
            ->where('campaign_id', $campaign_id)
            ->where('postal_code IS NOT NULL')
            ->where('postal_code != ""');

        return $query
            ->group_by(['postal_code'])
            ->order_by('clicks_count DESC')
            ->limit(5)
            ->get($this->collection)
            ->result_array();

    }
    
    public function get_click_count_hourly($campaign_id, $ad_id, $date = null){
        
        $date_now = date('Y-m-d H:i:s');

        $query = $this->CI->db
            ->select(['COUNT(id) as count', "DATE_FORMAT(timestamp,'%H') as hour", ""])
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

    public function get_click_count_hourly_for_combine($user_id, $so, $date = null){

        $date_now = date('Y-m-d H:i:s');
        //$date_now = '2016-10-21 16:00:00';

        $query = $this->CI->db
            ->select(['COUNT('.$this->collection.'.id) as count', "DATE_FORMAT(timestamp,'%H') as hour", 'v2_master_campaigns.campaign_type as campaign_type'])
//            ->where('campaign_id', $campaign_id);
            ->from($this->collection)
            ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id');

        if ($date){
            $query->where("DATE(timestamp) = DATE('$date')");
        }
        else {
            $query->where("TIMESTAMPDIFF(HOUR, timestamp, '{$date_now}') < 24");
        }

        $group_by = ['HOUR(timestamp)','v2_master_campaigns.campaign_type'];

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
    
    public function get_campaign_click_count_by_zip($campaign_id){
    
        $query = $this->CI->db
            ->select(['COUNT(id) count', 'postal_code']);
        
        return $query
            ->group_by(['postal_code'])
            ->get($this->collection)
            ->result_array();
        
    }

    public function get_clicks_count_by_campaigns_types($user_id, $start_date, $end_date, $so = null){

        $query = $this->CI->db->select('count(*) as total_clicks_count, v2_master_campaigns.campaign_type')
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