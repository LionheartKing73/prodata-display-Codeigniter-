<?php

class v2_retargeting_ip_model extends CI_Model	{

	protected $CI;

	private $id;
	private $campaign_id;

	protected $table = 'v2_retargeting_ips';

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
	}

	public function batch_insert($campaign_id, array $retargeting_ips)
	{
		$is_ok = false;

		if ( !empty($retargeting_ips) ) {
			foreach ( $retargeting_ips as $k => $r ) {
				$retargeting_ips[$k]['campaign_id'] = $campaign_id;
			}
			$is_ok = $this->CI->db->insert_batch($this->table, $retargeting_ips);
		}

		return $is_ok;
	}

	public function update_retargeting_ips_assocation($campaign_id, array $retargeting_ips)
	{
		if ( empty($retargeting_ips) ) return false;

		// remove existing maps
		$get_ip_maps = $this->get_associated_retargeting_ips_by_campaign_id($campaign_id);
		if ( !empty($get_ip_maps) ) {
			$this->CI->db->delete($this->table, ['campaign_id' => $campaign_id]);
		}

		// save associations
		$is_inserted = $this->batch_insert($campaign_id, $retargeting_ips);
		return $is_inserted;
	}

	public function get_associated_retargeting_ips_by_campaign_id($campaign_id)
	{
		$retargeting_ips = $this->CI->db
			->get_where($this->table, ['campaign_id' => $campaign_id])
			->result_array();
		return $retargeting_ips;
	}

	public function __set($name, $value) {
	    $this->{$name} = $value;
	}

	public function __get($name) {
	    return $this->{$name};
	}
}

?>