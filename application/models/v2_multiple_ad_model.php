<?php 

class V2_multiple_ad_model extends CI_Model	{

	protected $CI;
	private $collection = 'v2_multiple_ads';

    private $id;
	private $title;
	private $description_1;
	private $description_2;
	private $creative_name;
	private $destination_url;
	private $display_url;
	private $creative_width;
    private $creative_url; // this is where the banner ad image exists
	private $creative_height;
	private $creative_status;
	private $create_date;
	private $creative_is_active;
	private $creative_type;
	private $approval_status;
	private $disapproval_reasons;
	private $network_group_id;
	private $network_campaign_id;
	private $network_creative_id;
	private $network_id;
	private $campaign_id;
	private $group_id;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		
		//$this->CI->load->model("Monitor_model");
	}
	
    public function create($data, $multiple_campaign_id, $group_id, $ad_id, $campaign_network_id=null) {
        // rewrite dest_url to our reporting site
        $result = $this->validate($data);
        if($result['message']){
            return false;
        }
        $result['valide_ad']['multiple_campaign_id'] = $multiple_campaign_id;
        $result['valide_ad']['group_id'] = $group_id;
        $result['valide_ad']['ad_id'] = $ad_id;
		$result['valide_ad']['network_name'] = 'GOOGLE';
        if($campaign_network_id) {
            $result['valide_ad']['campaign_network_id'] = $campaign_network_id;
        }

        $this->CI->db->insert($this->collection, $result['valide_ad']);
        $this->id = $this->CI->db->insert_id();

        return $this->id;
    }

    public function validate($data, $for_edit = false) {

        $message=[];

        $insert["network_id"] = 1;

        if(!empty($data['campaign_id'])){
            $insert["campaign_id"] = $data['campaign_id'];
        } else {
            $message[] = 'AD campaign_id is empty';
        }

        return array('messages' => $message, 'valide_ad' => $insert);
    }

	public function update_by_ad_id($ad_id, $data){
		return $this->CI->db->where("ad_id", $ad_id)->update($this->collection, $data);
	}

    public function reset_network_data_by_ad_id($ad_id){
        $reset = array(
            //'network_creative_id'=>'',
            'approval_status'=>'UNCHECKED',
            'disapproval_reasons'=>'',
        );
        return $this->CI->db->where("ad_id", $ad_id)->update($this->collection, $reset);
    }

    public function update_by_network_creative_id($id, $data){
        return $this->CI->db->where("network_creative_id", $id)->update($this->collection, $data);
    }

	public function get_ads_by_campaign_id($campaign_id){
		$result=$this->CI->db->get_where($this->collection, ["campaign_id"=>$campaign_id]);

		return $result->num_rows() ? $result->result_array() : [];
	}

    public function get_active_campaigns_ads_by_network_id($network_id){
        $result = $this->CI->db->select([$this->collection.'.*','SUM(v2_campclick_impressions.impressions_count) AS impressions_count'])
                                ->from($this->collection)
                                ->join('v2_multiple_campaigns','v2_multiple_campaigns.id = '.$this->collection.'.multiple_campaign_id')
                                ->join('v2_campclick_impressions','v2_campclick_impressions.ad_id = '.$this->collection.'.ad_id AND v2_campclick_impressions.network_id = '.$network_id.'', 'left')
                                ->where(["v2_multiple_campaigns.network_campaign_status"=>'ACTIVE'])
                                ->where(["v2_multiple_campaigns.network_id"=>$network_id])
                                ->group_by($this->collection.'.id')
                                ->order_by($this->collection.'.campaign_id')
                                ->get();

        return $result->num_rows() ? $result->result_array() : [];
    }

	public function get_by_ad_id($ad_id)  {

	    return $this->CI->db->get_where($this->collection, ["ad_id"=>$ad_id])->row_array();
	}
	
	public function auto_generate_ad_content($url = "") {
	    if ($url == "")
	        throw new exception("url required");

	    $old_url = $url;
	    if (strpos($url, "cdqr") !== false)    {
			$url = $this->CI->Monitor_model->retrieve_remote_url($url);
		}

	    if ($url == "")
	        $url = $old_url;

	    // get page title
	    //$urlContents = file_get_contents($url);

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0");
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
	    $urlContents = curl_exec($ch);
	    curl_close($ch);
	    
	    /*
	    preg_match("/<title>(.*)<\/title>/i", $urlContents, $matches);
	    $title = $matches[1];
        */

	    $doc = new DOMDocument();
	    @$doc->loadHTML($urlContents);
	    $nodes = $doc->getElementsByTagName('title');
	    $title = trim($nodes->item(0)->nodeValue);

	    $meta = get_meta_tags($url);
	    $parse = parse_url($url);
	    //print_r($meta);

        $title = ($title != "") ? substr($title, 0, 25) : "Special Offer for You";
	    $description = ($meta['description'] != "") ? (substr($meta['description'],0,59) . " Click Now!") : "To learn more about this special offer, click now!";
	    
	    if ($parse['host'] == "")  {
	        $parse['host'] = "www.specialdiscounts.com";
	    }

	    $ad = array(
	        "display_url" => "http://" . $parse['host'],
	        "title" => $title,
	        "description" => $description
	    );

	    return $ad;
	}
	
	public function remove()   {
	    $this->CI->db->query("DELETE FROM ads WHERE id='{$this->id}'");
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
	
    public function get_by_campaign_id($campaign_id){
                
        return $this->CI->db->get_where($this->collection, ['campaign_id' => $campaign_id])->result_array();
    }

    public function get_with_clicks_by_campaign_id($campaign_id){

        $result = $this->CI->db->select($this->collection.'.*, count(v2_campclick_clicks.id) as clicks_count, v2_ads_links.destination_url as redirect_url,  v2_ads_links.id as ad_link_id ')
            ->from($this->collection)
            ->join('v2_campclick_clicks','v2_campclick_clicks.ad_id = '.$this->collection.'.id', 'left')
            ->join('v2_ads_links','v2_ads_links.ad_id = '.$this->collection.'.id')
            ->where(''.$this->collection.'.campaign_id',$campaign_id)
            ->group_by('v2_ads.id')
            ->order_by('v2_ads.create_date', DESC)
            ->get();
        return $result->result_array();
    }

    public function get_by_approval_status($status){

        return $this->CI->db->select($this->collection.'.*')
            ->from($this->collection)
            //->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id')
            ->where(''.$this->collection.'.approval_status',$status)
            ->where(''.$this->collection.'.creative_is_active',"Y")
            ->get()->result_array();
    }

    public function get_with_network_name_by_id($id){

        return $this->CI->db->select($this->collection.'.*, v2_master_campaigns.network_name')
            ->from($this->collection)
            ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id')
            ->where(''.$this->collection.'.id',$id)
            ->get()->row_array();
    }
    
    public function update_all_by_campaign_id($campaign_id, $data){
            return $this->CI->db->where("campaign_id", $campaign_id)->update($this->collection, $data);
    }
}

?>