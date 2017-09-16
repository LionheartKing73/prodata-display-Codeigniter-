<?php
class V2_placements_reporting_model extends CI_Model {

    protected $CI;
    private $collection = 'v2_placement_reporting';

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

    public function get_campaign_places_by_campaign_id($campaign_id,$limit=10) {

        $result = $this->CI->db
            ->select('*')
            ->where('campaign_id', $campaign_id)
            ->where('type', 'CLICK')
            ->order_by('created_date DESC')
            ->limit($limit,0)
            ->get($this->collection)
            ->result_array();
        return $result;

    }
    public function get_campaign_places_by_campaign_id_with_range_date($campaign_id,$limit=10,$start_date=null,$end_date=null) {
    
    	$result = $this->CI->db
    	        ->select('*, sum(impressions) as impressions_sum')
            ->where('campaign_id', $campaign_id)
            ->where('type', 'CLICK');
            if($start_date){
            	$result->where('created_date >=', $start_date);
    		}
            if($end_date){
            	$result->where('created_date <=', $end_date);
            }
            $res = $result->order_by('created_date DESC')
            ->limit($limit,0)
            ->group_by('placement')
            ->get($this->collection)
            ->result_array();
            
    	return $res;
    
    }

    public function get_places($user_id, $so, $start_date, $end_date, $limit=5) {

        $query = $this->CI->db
            ->select('sum(`impressions`) as impressions,`placement`')
            ->from($this->collection);

        if ($so){
            $query->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id')->where('v2_master_campaigns.so', $so);
            if($user_id) {
                $query->where('v2_master_campaigns.userid', $user_id);
            }
        }
        if($start_date == $end_date) {
            $query->where($this->collection.'.created_date >=', $start_date);
        } else {
            $query->where($this->collection.'.created_date >=', $start_date)->where($this->collection.'.created_date <=', $end_date);
        }
        $query->where('type', 'CLICK')->group_by($this->collection.'.placement')->order_by('impressions DESC')->limit($limit,0);
        $result = $query->get();

        return $result->result_array();

    }

}
?>