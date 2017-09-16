<?php 

class Domains_model extends CI_Model	{

    private $collection = "domains";
    protected $CI;

    private $id;
    private $name;
    private $is_active;
    private $user_id;

    public function __construct()	{
            parent::__construct();
            $this->CI =& get_instance();
            $this->CI->load->database();

    }

    public function create($insert)	{

        $this->CI->db->insert($this->collection, $insert);
        return $this->CI->db->insert_id();
    }

    public function delete($id){
        return $this->CI->db->where('id', $id)->delete($this->collection);
    }

    public function get_domain($id = null)	{
        return $this->CI->db->get_where($this->collection, ['id' => $id])->row_array();     
    }
    
    public function get_domain_logo_by_name($name = null)	{
        return $this->CI->db->get_where($this->collection, ['domain' => $name])->row_array()['logo'];
    }
    
    public function update($id, $update){
        return $this->CI->db->where('id', $id)->update($this->collection, $update);
    }

    public function get_all_by_user_id($user_id)	{
        $r = $this->CI->db->query("SELECT * FROM {$this->collection} WHERE user_id='{$user_id}' AND is_active='Y'");
        if ($r->num_rows() > 0)	{
                return $r->result_array();
        } else {
                return false;
        }
    }

    public function get_domain_list(){      
        return $this->CI->db->get($this->collection)->result_array();
    }

    public function __get($name)	{
            return $this->$name;
    }

    public function __set($name, $value)	{
            $this->$name = $value;
    }

    public function __isset($name)	{
            return isset($this->$name);
    }
	
}

?>
