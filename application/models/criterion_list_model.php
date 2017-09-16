<?php
class Criterion_list_model extends CI_Model {
    function __construct()
    {
        $this->load->database(); 
        // Call the Model constructor
        parent::__construct();
    }
         
    /*
     * @description This function is used to create a new criterion, which is the object which adds the audience into the ad group
    */
    public function createCriterion(array $data){
        $this->db->insert('criterion_list', $data); 
    }
    
     public function removeCriterion($criterion_id){               
        $query=$this->db->query("DELETE FROM `criterion_list` WHERE `criterion_id`='".$this->db->escape_str($criterion_id)."'");
    }

     public function select_all(){
        $result=$this->db->get("criterion_list");
        
        return $result->num_rows() ? $result->result_array() : [];
    }
    public function select_oldest_remarketing(){
        $today=$today=date("Y-m-d H:i:s");
      
        $result=$this->db->query("SELECT TIMESTAMPDIFF(MONTH, (SELECT MIN(date_created) FROM criterion_list), '".$today."') AS months");
        
        return $result->num_rows() ? $result->result_array() : [];
    }


    public function update($id, $data){
        $this->db->where("id", $id)
            ->update("criterion_list", $data);
    }

    public function get_remarketing_by_group_id($group_id){
        $result= $this->db->get_where("criterion_list", ["group_id_in_db"=>$group_id]);

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function wheatherIOHasRemarketing($io){
        $result= $this->db->get_where("criterion_list", ["audience_id"=>$io]);

        return $result->num_rows() ? true : false;
    }

    public function removeCriterionById($id){
        $this->db->delete('criterion_list', array('id' => $id));
    }
    public function getIoForThreeMonths($min_date){
        $result_display_ad=$this->db->query("SELECT DISTINCT remarketing_list_id, userlist_io.io as us_io, CONCAT(group_list.io, ' - ', group_list.campaign) as io FROM (criterion_list)
        JOIN userlist_io ON criterion_list.audience_id=userlist_io.remarketing_list_id
        JOIN group_list ON group_list.id=criterion_list.group_id_in_db WHERE criterion_list.audience_type = 'io' AND group_list.status = 'active' AND criterion_list.date_created >= '".$min_date."'");



        $result_text_ad=$this->db->select("userlist_io.remarketing_list_id, userlist_io.io")
            ->join("userlist_io", "take5_pending_campaigns.io=userlist_io.io")
            ->where("take5_pending_campaigns.record_created >=", $min_date)
            ->get("take5_pending_campaigns");



        $result_display_ad=$result_display_ad->num_rows() ? $result_display_ad->result_array() : [];
        $result_text_ad=$result_text_ad->num_rows() ? $result_text_ad->result_array() : [];



        foreach ($result_display_ad as $key=>$disp_ad){
            for($i=$key+1; $i<count($result_display_ad); $i++){
                if($disp_ad["remarketing_list_id"]==$result_display_ad[$i]["remarketing_list_id"]){
                    $result_display_ad[$key]=null;
                }
            }
        }


        foreach ($result_text_ad as $key=>$disp_ad){
            for($i=$key+1; $i<count($result_text_ad); $i++){
                if($disp_ad["remarketing_list_id"]==$result_text_ad[$i]["remarketing_list_id"]){
                    $result_text_ad[$key]=null;
                }
            }
        }


        foreach ($result_text_ad as $key=>$disp_ad){
            for($i=0; $i<count($result_display_ad); $i++){
                if($disp_ad["remarketing_list_id"]==$result_display_ad[$i]["remarketing_list_id"]){
                    $result_text_ad[$key]=null;
                }
            }
        }


        foreach ($result_display_ad as $key=>$disp_ad){
           if(!$disp_ad){
               unset($result_display_ad[$key]);
           }
        }


        foreach ($result_text_ad as $key=>$disp_ad){
            if(!$disp_ad){
                unset($result_text_ad[$key]);
            }
        }



//           foreach($io_list as $key_io=>$io){
//                foreach($result_display_ad as $key=>$display_ad){
//                    if($display_ad["remarketing_list_id"]== $io["remarketing_list_id"] && $display_ad["us_io"]== $io["io"]){
//                        unset($io_list[$key_io]);
//                    }
//                }
//
//                foreach($result_text_ad as $text_ad){
//                    if($io["remarketing_list_id"]== $text_ad["remarketing_list_id"] && $io["io"]== $text_ad["io"]){
//                        unset($io_list[$key_io]);
//                    }
//                }
//        }

        $result=array_merge($result_display_ad, $result_text_ad);
//        $result=array_merge($result, $io_list);

//var_dump(count($result)); exit;
        return $result;
    }
}
?>
