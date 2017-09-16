<?php

class v2_campaign_category_model extends CI_Model	{

	protected $CI;

	private $id;
	private $campaign_id;
	private $category_id; // Ref to v2_iab_categories.id

	protected $table = 'v2_campaign_categories';

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
	}

	public function batch_insert($campaign_id, array $verticals)
	{
		$is_ok = false;

		if ( !empty($verticals) ) {
			$vertical_assoc = [];
			foreach ( $verticals as $vertical ) {
				$vertical_assoc[] = [
					'campaign_id' => $campaign_id,
					'iab_category_id' => $vertical['catid'],
					'name' => $vertical['vertical']
				];
			}

			$is_ok = $this->CI->db->insert_batch($this->table, $vertical_assoc);
		}

		return $is_ok;
	}

	public function update_campaign_categories_assocation($campaign_id, array $verticals)
	{
		// remove existing maps
		$get_category_maps = $this->get_associated_iab_categories_by_campaign_id($campaign_id);
		if ( !empty($get_category_maps) ) {
			$this->CI->db->delete($this->table, ['campaign_id' => $campaign_id]);
		}

		// Update Vertical
		if ( is_array($verticals) ) {
			$first_vertical = trim($verticals[0]['catid']);
		}

		$this->CI->db->update('v2_master_campaigns', [
			'vertical' => $first_vertical
		], [
			'id' => $campaign_id
		]);

		// save associations
		$is_inserted = $this->batch_insert($campaign_id, $verticals);
		return $is_inserted;
	}

	public function get_associated_iab_categories_by_campaign_id($campaign_id)
	{
		$categories = $this->CI->db
			->get_where($this->table, ['campaign_id' => $campaign_id])
			->result_array();
		return $categories;
	}

	public function copy_batch_insert($campaign_id, array $verticals)
	{
		$is_ok = false;

		if ( !empty($verticals) ) {
			foreach ( $verticals as $vertical ) {
				$vertical_assoc[] = [
					'campaign_id' => $campaign_id,
					'iab_category_id' => $vertical['iab_category_id'],
					'name' => $vertical['name']
				];
			}

			$is_ok = $this->CI->db->insert_batch($this->table, $vertical_assoc);
		}

		return $is_ok;
	}

	public function __set($name, $value) {
	    $this->{$name} = $value;
	}

	public function __get($name) {
	    return $this->{$name};
	}
}

?>