<?php
class V2_demographics_reporting_model extends CI_Model {

    protected $CI;
    private $collection = 'v2_demographics_reporting';

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
    
    public function get_chart_data ($campaign_id, $start_data, $end_data) {

        $end_data = date('Y-m-d', strtotime("+1 day", strtotime($end_data)));
        
        $query = $this->CI->db
            ->select([
                //"DATE_FORMAT(created_date,'%Y-%m-%d') as date",
                "SUM(18_24) as '18_24'",
                "SUM(25_34) as '25_34'",
                "SUM(35_44) as '35_44'",
                "SUM(45_54) as '45_54'",
                "SUM(55_64) as '55_64'",
                "SUM(`64`) as '64+'",
                'SUM(unknown_age) as unknown_age',
                'SUM(male) as male',
                'SUM(female) as female',
                'SUM(unknown_gender) as unknown_gender'
                ])
            ->where('campaign_id', $campaign_id)
            ->where('type', 'CLICK')
            ->where('created_date >=', $start_data)
            ->where('created_date <=', $end_data);
                
        return $query
            ->group_by('created_date')
            ->order_by('created_date')
            ->get($this->collection)
            ->result_array()[0];
        
    }
    public function get_chart_data_for_combine ($user_id, $so, $start_date, $end_date) {

        $end_date = date('Y-m-d', strtotime("+1 day", strtotime($end_date)));

        $query = $this->CI->db
            ->select([
                "DATE_FORMAT(created_date,'%Y-%m-%d') as date",
                "SUM(18_24) as '18_24'",
                "SUM(25_34) as '25_34'",
                "SUM(35_44) as '35_44'",
                "SUM(45_54) as '45_54'",
                "SUM(55_64) as '55_64'",
                "SUM(`64`) as '64+'",
                'SUM(unknown_age) as unknown_age',
                'SUM(male) as male',
                'SUM(female) as female',
                'SUM(unknown_gender) as unknown_gender'
                ])
            ->from($this->collection);

        //$group_by = [$this->collection.'.created_date'];
        if ($so){
            $query->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id')->where('v2_master_campaigns.so', $so);
            //$group_by[] = 'v2_master_campaigns.so';
            if($user_id) {
                $query->where('v2_master_campaigns.userid', $user_id);
            }
        }

        $query->where('type', 'CLICK');

        if($start_date == $end_date) {
            $query->where($this->collection.'.created_date >=', $start_date);
        } else {
            $query->where($this->collection.'.created_date >=', $start_date)->where($this->collection.'.created_date <=', $end_date);
        }

        return $query
            ->group_by('created_date')
            ->order_by('created_date')
            ->get()
            ->result_array()[0];

    }

}
?>