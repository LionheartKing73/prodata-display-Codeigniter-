<?php 

class Report_model extends CI_Model	{

	private $collection = "campclick_campaigns";
	protected $CI;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->library("user_agent");
		$this->CI->load->model("Domains_model");
		$this->CI->load->model("Campclick_model");
	}
	
	public function progress_update($offset = "7")	{
		$time_offset = date("Y-m-d 00:00:00", strtotime("{$offset} days ago"));

		$r = $this->CI->db->query("SELECT io, max_clicks, campaign_start_datetime, v.name AS vendor_name, campclick_campaigns.name AS camp_name FROM campclick_campaigns LEFT JOIN vendor v ON v.id=campclick_campaigns.vendor_id WHERE campclick_campaigns.is_active='Y' AND campaign_is_started='Y' AND campaign_is_complete='N' AND campaign_start_datetime >= '{$time_offset}'");

		$finalArry = array();
		if (count($r->result_array() > 0))	{
			foreach($r->result_array() as $c)	{
				$result = $this->CI->db->query("SELECT COUNT(*) AS cnt, DATE(timestamp) AS dt FROM campclick_clicks WHERE io='{$c['io']}' GROUP BY dt");
				
				if (count($result->result_array()) > 0)	{
					$sum = 0;
					foreach($result->result_array() as $sc)	{
						$sum += $sc['cnt'];
					}
					
					$is_complete = ($sum >= $c['max_clicks']) ? true : false;

					$finalArry[] = array(
						"io" => $c['io'],
						"max_clicks" => $c['max_clicks'],
						"dates" => $result->result_array(),
						"is_complete" => $is_complete,
						"campaign_start_datetime" => $c['campaign_start_datetime'],
						"total_clicks" => $sum,
						"vendor" => $c['vendor_name'],
						"camp_name" => $c['camp_name']
					);
				}
			}
		}
		
		return $finalArry;
	}
}