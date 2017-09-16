<?php
class CriterionListModel extends CI_Model {
    function __construct()
    {
        $this->load->database(); 
        // Call the Model constructor
        parent::__construct();
    }
         
    /*
     * @description This function is used to create a new criterion, which is the object which adds the audience into the ad group
    */
    public function createCriterion($criterion){  
        $data=array("audience_id"=>$criterion["user_list_id"], "criterion_id"=>$criterion["id"], "group_id"=>$criterion["group_id"]);
        
        $this->db->insert('criterion_list', $data); 
    }
    
     public function removeCriterion($criterion_id){               
        $query=$this->db->query("DELETE FROM `criterion_list` WHERE `criterion_id`='".$this->db->escape_str($criterion_id)."'");
    }
}
?>
