<?php
class V2_campaign_cost_model extends CI_Model {

    protected $CI;
    private $collection = 'v2_campaign_costs';

    private $id;
    private $campaign_id;
    private $network_id;
    private $cost;
    private $date_updated;

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

    public function get_all_by_campaign_id($id){
        $result= $this->CI->db->get_where($this->collection, array("campaign_id"=>$id));

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function get_cost_by_campaign_id($id){
        $result= $this->CI->db->get_where($this->collection, array("campaign_id"=>$id));

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function get_daily_cost_by_campaign_id($id){

        $today = date("Y-m-d 00:00:00");
        $yesterday = date("Y-m-d 00:00:00", strtotime('-1 day'));
//        $result = $this->CI->db->select('max(cost) as cost')->from($this->collection)->where(array("campaign_id"=>$id, 'date_updated >=' => $today))->get();
        $result = $this->CI->db->query("SELECT MAX(cost) AS cost FROM {$this->collection} WHERE campaign_id='{$id}' AND date_updated >= '{$today}' AND type <> 'RTB'");
        $today_cost = $result->row_array()['cost'];
        if($today_cost) {
            //$result_yesterday = $this->CI->db->select('max(cost) as cost')->from($this->collection)->where(array("campaign_id"=>$id, 'date_updated >=' => $yesterday, 'date_updated <' => $today))->get();
            $result_yesterday = $this->CI->db->query("SELECT MAX(cost) AS cost FROM {$this->collection} WHERE campaign_id='{$id}' AND date_updated >= '{$yesterday}' AND date_updated < '{$today}' AND type <> 'RTB'");
            
            $today_cost = $result->row_array()['cost'];
            $yesterday_cost = $result_yesterday->row_array()['cost'];
            return $today_cost - $yesterday_cost;
        } else {
            return 0;
        }
    }
    
    public function get_todays_campaign_spend($id = 0) {
        if ($id == 0) {
            return false;
        }
        
        $date_start = date("Y-m-d 00:00:00");
        $date_end = date("Y-m-d 23:59:59");
        
        $network_cost = $this->get_daily_cost_by_campaign_id($id);
        
        //print "NETWORK COST: {$network_cost} FOR {$date_start} to {$date_end}\n";
        
        //$rtb_cost_result = $this->CI->db->query("SELECT SUM(win_price) AS rtb_cost FROM v2_campclick_impressions WHERE campaign_id='{$id}' AND (timestamp BETWEEN '{$date_start}' AND '{$date_end}') LIMIT 1");
        
        $rtb_cost = $this->clickcap->get_campaign_spend($id);
        
        print "RTB_COST: {$rtb_cost}\n";
        
        /*
        if ($rtb_cost_result->num_rows() > 0) {
            $result = $rtb_cost_result->row_array();
            $rtb_cost = $result['rtb_cost'];
        } else {
            $rtb_cost = 0.00;
        }
        */
        
        
        
        //print "RTB COST: {$result['rtb_cost']}\n";
        
        return $rtb_cost + $network_cost;
    }
    
    public function get_hourly_spend_by_campaign($campaign_id = 0) {
        $this->CI->load->model("V2_time_parting_model");
        
        $hours_run = $this->CI->V2_time_parting_model->how_many_run_hours_today($campaign_id, strtolower(date("l")));
        $time_part_info = $this->CI->V2_time_parting_model->get_by_campaign_id_dow($campaign_id, strtolower(date("l")));
        
        $time_delta = ((time() - strtotime($time_part_info['start_time'])) / 60 / 60);
        
        $r = $this->CI->db->query("SELECT budget FROM v2_master_campaigns where id='{$campaign_id}' LIMIT 1");
        
        $accumulated_spend = $this->get_todays_campaign_spend($campaign_id);
        
        if ($r->num_rows() > 0 && $hours_run && $accumulated_spend) {
            $c = $r->row_array();
            
            $max_hour_spend = sprintf("%.6f", ($c['budget'] / $hours_run));
            
            return array(
                "max_hour_spend" => sprintf("%.6f", ($c['budget'] / $hours_run)),
                "hours_run" => $hours_run,
                "accumulated_spend" => $accumulated_spend,            
                "max_spend_for_time" => sprintf("%.6f", ($time_delta * $max_hour_spend)),
                "current_hours_run" => $time_delta,
            );
        } else {
            return array(
                "max_hour_spend" => 0.00,
                "hours_run" => $hours_run,
                "accumulated_spend" => $accumulated_spend,
                "max_spend_for_time" => sprintf("%.6f", ($time_delta * $max_hour_spend)),
                "current_hours_run" => $time_delta,
            );
        }
        
    }
}
?>
