<?php 

class V2_affinity_model extends CI_Model	{

	protected $CI;
	private $collection = 'v2_affinity';

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
	}

	public function create($insert) {

	    $this->CI->db->insert($this->collection, $insert);
	}
	
	public function get_by_id($id)  {
        return $this->CI->db->get_where($this->collection, ["id"=>$id])->row_array();
	}

	public function get_by_type($type)  {
        //return $this->CI->db->get_where($this->collection, ["type"=>$type])->result_array();

        $r = $this->CI->db->query(
            "select root.category  as root_name, root.criterion_id as root_criterion_id,
                  down1.category as down1_name, down1.criterion_id as down1_criterion_id,
                  down2.category as down2_name, down2.criterion_id as down2_criterion_id,
                  down3.category as down3_name, down3.criterion_id as down3_criterion_id,
                  down4.category as down4_name, down4.criterion_id as down4_criterion_id
                from v2_affinity as root
                left join v2_affinity as down1 on down1.parent_category_id = root.id
                left join v2_affinity as down2 on down2.parent_category_id = down1.id
                left join v2_affinity as down3 on down3.parent_category_id = down2.id
                left join v2_affinity as down4 on down4.parent_category_id = down3.id
                  where root.parent_category_id is null AND root.type = '".$type."'
                order by root_name, down1_name, down2_name, down3_name, down4_name"
        );

        return $r->result_array();

	}

    public function update()  {
        $data = $this->CI->db->get_where($this->collection, ["id >"=>107])->result_array();
        foreach ($data as $key=>$value) {
            $new = explode('/', $value['category']);
            //if($key<10) {var_dump(end($new));}

            $this->CI->db->where("id", $value['id'])->update($this->collection, ['category' => end($new)]);

        }
    }

}

?>