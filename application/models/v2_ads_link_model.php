<?php

class v2_ads_link_model extends CI_Model	{

    protected $CI;
    private $collection = 'v2_ads_links';

    private $id;
    private $campaign_id;
    private $ad_id;
    private $click_count;
    private $destination_url;
    private $original_url;

    public function __construct(){

        parent::__construct();
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    public function create($ad, $campaign_id, $ad_id = null)   {

        $insert = array(
            "campaign_id" => $campaign_id
        );

        if(!empty($ad['tracking_url'])) {
                $url = $ad['tracking_url'];
                $url = str_replace("prodataretargeting.com", "reporting.prodata.media", $url);
                $url = str_replace("www.", "", $url);
		$ad['tracking_url'] = $url;
            $insert["destination_url"] = $ad['tracking_url'];
        } else {
            $insert["destination_url"] = $ad['destination_url'];
        }

        if($ad_id) {
            $insert["ad_id"] = $ad_id;
        }

        if( !empty($ad['max_clicks']) ) {
            $insert["max_clicks"] = $ad['max_clicks'];
        }

        $this->CI->db->insert($this->collection, $insert);
        $this->id = $this->CI->db->insert_id();

        if ($this->id > 0) {
            return $this->id;
        }
        else {
            return false;
        }
    }

    public function update($id, $data){
        $result = $this->CI->db->where("id", $id)->update($this->collection, $data);
        return $result;
    }

    public function remove(){
        $this->CI->db->delete($this->collection, array("id" => $this->id));
    }

    public function get_link_by_campaign_id($campaign_id){
        $result = $this->CI->db->where("campaign_id",$campaign_id)->get($this->collection);
        return $result->row_array();
    }

    public function __get($name){
        return $this->$name;
    }

    public function __set($name, $value){
        $this->$name = $value;
    }

    public function __isset($name){
        return isset($this->$name);
    }

    public function get_primary_campaign_link(){

        $r = $this->CI->db->query("select * from {$this->collection} where campaign_id ='{$this->pending_campaign_id}' order by click_count desc limit 1");
        return ($r->num_rows() > 0) ? $r->row_array() : false;
    }

    public function get_by_id($id){

        $link = $this->CI->db->where(['id' => $id])
                    ->limit(1)
                    ->get($this->collection)
                    ->result_array();

        return $link[0];
    }

}

?>
