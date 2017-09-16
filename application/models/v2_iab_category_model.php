<?php

class v2_iab_category_model extends CI_Model	{

	protected $CI;

	private $id;
	private $parent_id;
	private $category_id;
	private $parent_category_id;

	protected $table = "v2_iab_categories";

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
	}

	public function create(array $data)
	{
		$this->CI->db->insert($this->table, $data);
		return $this->CI->db->insert_id();
	}

	public function get_parent_categories()
	{
		$categories = $this->CI->db
			->where('parent_id IS NULL')
			->get($this->table)
			->result_array();

		return $categories;
	}

	public function get_all_categories()
	{
		$categories = $this->get_parent_categories();

		foreach ( $categories as $k => $parent ) {
			$children = $this->CI->db
				->get_where($this->table, ['parent_id' => $parent['id']])
				->result_array();

			$categories[$k]['children'] = $children;
		}

		return $categories;
	}

	public function get_by_iab_category_id($category_id, $include_children = true)
	{
		$parent = $this->CI->db
			->where(['category_id' => $category_id])
			->get($this->table)
			->row_array();

		if ( $include_children === true ) {
			$children = $this->CI->db
				->get_where($this->table, ['parent_id' => $parent['id']])
				->result_array();
			$parent['children'] = $children;
		}

		return $parent;
	}

	public function get_by_id($id, $include_children = true)
	{
		$parent = $this->CI->db
			->where(['id' => $id])
			->get($this->table)
			->row_array();

		if ( $include_children === true ) {
			$children = $this->CI->db
				->get_where($this->table, ['parent_id' => $parent['id']])
				->result_array();
			$parent['children'] = $children;
		}

		return $parent;
	}

	public function __set($name, $value) {
	    $this->{$name} = $value;
	}

	public function __get($name) {
	    return $this->{$name};
	}
}

?>