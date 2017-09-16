<?php

class V2_viewer_campaign_model extends CI_Model
{

    protected $CI;
    private $collection = 'v2_viewer_campaign';

    public function __construct()
    {
        parent::__construct();
        $this->CI = &get_instance();
        $this->CI->load->database();
    }

    public function get_by_id($user_id)
    {
        return $this->CI->db->get_where($this->collection, ['parent_id' => $user_id])->result_array();
    }

    public function get_campaigns_id_by_parent_id_and_viewer_id($parent_id,$user_id)
    {
        return $this->CI->db->select($this->collection.'.campaign_id')->where(['parent_id' => $parent_id])->where(['viewer_id' => $user_id])->get($this->collection)->result_array();
    }

 	


    public function get_campaigns_by_id($user_id)
    {
//        return $this->CI->db->select('*')
//            ->from('v2_master_campaigns')
//            ->join($this->collection,'v2_master_campaigns.id = '.$this->collection.'.campaign_id')
//            ->get()->result_array();

        return $this->CI->db->select([$this->collection.'.*','v2_master_campaigns.name AS name','users.username AS username'])
            ->from($this->collection)
            ->where($this->collection.'.parent_id',$user_id)
            ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id')
            ->join('users','users.id = '.$this->collection.'.viewer_id')
          //  ->join('users',$users_id .'= '.$this->collection.'.viewer_id', 'left')
            ->get()->result_array();
    }

    public function insert_viewers($viewers,$campaign_id, $user_id){
		
        foreach ($viewers as $viewer) {
            $insert = array(
                'viewer_id' => $viewer,
                'campaign_id' => $campaign_id,
                'parent_id' => $user_id

            );
            $this->db->insert($this->collection, $insert);

        }

    }

    public function insert_viewer($viewer,$campaign_id, $user_id){


            $insert = array(
                'viewer_id' => $viewer,
                'campaign_id' => $campaign_id,
                'parent_id' => $user_id

            );
            $this->db->insert($this->collection, $insert);



    }


    public function check_access_viewer_exist($viewer,$campaign_id, $user_id) {

        return $this->CI->db
                ->get_where($this->collection, array(
                'viewer_id' => $viewer,
                'campaign_id' => $campaign_id,
                'parent_id' => $user_id))
                ->result_array();


    }

    public function delete_access_viewer($id) {

        $this->CI->db->delete($this->collection, array('id' => $id));

    }



}
