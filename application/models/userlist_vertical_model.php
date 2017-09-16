<?php
class Userlist_vertical_model extends CI_Model {

    private $collection = "userlist_vertical";
    function __construct()
    {
        $this->load->database(); 
        // Call the Model constructor
        parent::__construct();
    }
         
    /*
     * @description This function is used to get all users
     * @return array users list
     */
    public function get_userlist_from_vertical($vertical){
     $query = $this->db->get_where('userlist_vertical', array('vertical' => $vertical));
        
        return $query? $query->result_array() :[];       
    }
    
    /*
     * @description This function is used to create new user list
     * @param IO insertion order id
     * @return boolean 
     */
    public function create_userlist_vertical($vertical, $remarketingListId, $snipedCode){
        $sql = "INSERT INTO userlist_vertical (vertical, remarketing_list_id, sniped_code) VALUES('".$this->db->escape_str($vertical)."', '".$this->db->escape_str($remarketingListId)."', '".$this->db->escape_str($snipedCode)."')";
        $response = $this->db->query($sql);
        
        return $response;
    }
    
     public function get_all_users_from_vertical(){
       $userList=$this->db->order_by("vertical", "asc")
           ->get("userlist_vertical");
       
       return $userList->result_array();
    }
    
    public function select_all_realketing(){
        
        $result = $this->db->select("remarketing_list_id")
                         ->get("userlist_vertical");

        return $result;
    }

    public function get_criteria_id_list($list) {

		$result = $this->db->select('remarketing_list_id')->where_in('vertical', $list)->get($this->collection)->result_array();
		foreach($result as $criteria){
                $criteria_array[] = $criteria['remarketing_list_id'];
        }
    	return $criteria_array;
    }
}
?>
