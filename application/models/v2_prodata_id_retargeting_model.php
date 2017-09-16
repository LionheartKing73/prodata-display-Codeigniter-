<?php

class V2_prodata_id_retargeting_model extends CI_Model	{

	protected $CI;

	private $prodata_id;
	private $campaign_id;
	private $iab_category;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();

		$this->CI->load->model('v2_campaign_category_model');
	}

	public function save() {

		$assoc_iab_categories = $this->CI->v2_campaign_category_model->get_associated_iab_categories_by_campaign_id($this->campaign_id);
		if ( !empty($assoc_iab_categories) && is_array($assoc_iab_categories) ) {
			$iab_categories = array_column($assoc_iab_categories, 'iab_category_id');
		}

		if ( empty($iab_categories) ) return false;
		$iab_categories = json_encode($iab_categories);

	    return $this->CI->db->insert('v2_prodata_id_retargeting', array(
	        'prodata_id' => $this->prodata_id,
	        'campaign_id' => $this->campaign_id,
	        'iab_category' => $iab_categories
	    ));

	}

	public function get_associate_prodata_ids_by_campaign_id($campaign_id)
	{
		if ( empty($campaign_id) ) return false;

		$sql = "SELECT
					count(DISTINCT prodata_id) as total,
					GROUP_CONCAT(DISTINCT prodata_id SEPARATOR ',') as prodata_ids
				FROM `v2_prodata_id_retargeting`
				WHERE campaign_id = ?
				GROUP BY campaign_id;";

		$results = $this->CI->db->query($sql, array($campaign_id))->row_array();
		return $results;
	}

	public function get_grouped_campaign_ids_by_prodata_id()
	{
		//SELECT count(id), prodata_id, GROUP_CONCAT(DISTINCT campaign_id SEPARATOR ',') FROM `v2_prodata_id_retargeting` group by prodata_id having count(id) > 1
	}

	public function __set($name, $value) {
	    $this->{$name} = $value;
	}

	public function __get($name) {
	    return $this->{$name};
	}
}

?>