<?php

class V2_users_model extends CI_Model {

    protected $CI;
    private $collection = 'users';

    public function __construct() {
        parent::__construct();
        $this->CI = & get_instance();
        $this->CI->load->database();
    }
    
    public function get_by_id($user_id){
        return $this->CI->db->get_where($this->collection, ['id' => $user_id])->row_array();
    }

    public function get_all_viewers_by_userid($id){
        return $this->CI->db->get_where($this->collection, ['parent_customer' => $id])->result_array();
    }

    public function get_all_users_by_financial_manager_id($id){
        return $this->CI->db->get_where($this->collection, ['financial_manager_id' => $id])->result_array();
    }

    public function get_all_users(){
        return $this->CI->db->get($this->collection)->result_array();
    }

    public function get_active_customers() {
        //return $this->CI->db->get_where($this->collection, ['active' => 1, 'user_type' => 'customer'])->result_array();
    	$query = $this->CI->db->where('active',1);
    	$query->where("user_type","customer");
    	$query->order_by('company', 'ASC');
    	return $query->get($this->collection)->result_array();
    	
    }

    public function get_all_email_linked_users(){
        return $this->CI->db->get_where($this->collection, ['is_email' => 'Y'])->result_array();
    }

    public function get_all_users_with_domain(){

        return $this->CI->db
            ->select(['users.*', 'domains.logo', 'domains.domain as domain_domain'])
            ->where(["users.user_type !="=>'viewer'])
            ->join('domains', 'domains.id = users.domain_id', 'left')
            ->get($this->collection)
            ->result_array();
    }
    
    public function update($id, $arr_update){
        return $this->CI->db->where('id', $id)->update($this->collection, $arr_update); 
    }

    public function check_email($email, $user_id) {

        return $this->CI->db->where("email = '$email' and id != $user_id")->get($this->collection)->result_array();

    }


}

?>
