<?php
class V2_video_watch_model extends CI_Model {

    protected $CI;
    private $collection = 'v2_video_watch';

    private $id;
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



    public function get_video_watch ($campaign_id, $start_data, $end_data) {

        $end_data = date('Y-m-d', strtotime("+1 day", strtotime($end_data)));

        $query = $this->CI->db
            ->select([
                //"DATE_FORMAT(created_date,'%Y-%m-%d') as date",
                'SUM(10_sec) as 10_sec',
                'SUM(25_p) as 25_p',
                'SUM(50_p) as 50_p',
                'SUM(75_p) as 75_p',
                'SUM(95_p) as 95_p'

            ])

            ->where('campaign_id', $campaign_id)
            ->where('created_date >=', $start_data)
            ->where('created_date <=', $end_data);

        return $query
            //->group_by(['YEAR(created_date)', 'MONTH(created_date)', 'DAY(created_date)'])
            ->order_by('created_date')
            ->get($this->collection)
            ->result_array()[0];

    }

    function get_video_watch_count($campaign_id, $start_data, $end_data) {
    	$end_data = date('Y-m-d', strtotime("+1 day", strtotime($end_data)));
    	 
    	$query = $this->CI->db
    	->select(['SUM(10_sec+15_sec+30_sec) as views_count', "DATE_FORMAT(created_date,'%Y-%m-%d') as date"])
    	->where('campaign_id', $campaign_id)
        ->where('created_date >=', $start_data)
        ->where('created_date <=', $end_data);
    	 
    	   	 
    	return $query
    	->group_by(['YEAR(created_date)', 'MONTH(created_date)', 'DAY(created_date)'])
    	->order_by('created_date')
    	->get($this->collection)
    	->result_array();
    	 
    }
    
    function get_video_watch_count_hourly($campaign_id, $ad_id, $date = null){
       $date_now = date('Y-m-d H:i:s');
    
    	$query = $this->CI->db
    	->select(['SUM(10_sec+15_sec+30_sec) as count', "DATE_FORMAT(created_date,'%H') as hour"])
    	->where('campaign_id', $campaign_id);
      	if ($date){
            $query->where("DATE(created_date) = DATE('$date')");
        }
        else {
            $query->where("TIMESTAMPDIFF(HOUR, created_date, '{$date_now}') < 24");
        }
    
    
    	 return $query
    	->group_by(['HOUR(created_date)'])
    	->get($this->collection)
    	->result_array();
    	 
    
    }
    
    public function get_video_watch_by_campaign_id($campaign_id) {

        $query = $this->CI->db
            ->select([
                //"DATE_FORMAT(created_date,'%Y-%m-%d') as date",
                'SUM(10_sec) as 10_sec',
                'SUM(25_p) as 25_p',
                'SUM(50_p) as 50_p',
                'SUM(75_p) as 75_p',
                'SUM(95_p) as 95_p'

            ])

            ->where('campaign_id', $campaign_id);

        return $query
            //->group_by(['YEAR(created_date)', 'MONTH(created_date)', 'DAY(created_date)'])
            ->order_by('created_date')
            ->get($this->collection)
            ->result_array()[0];

    }


}
?>