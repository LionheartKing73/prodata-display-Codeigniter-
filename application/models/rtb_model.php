<?php

class Rtb_model extends CI_Model	{

	private $network_id = 7;
	protected $CI;
	public function __construct()	{

		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->library("user_agent");
		$this->CI->load->model("Domains_model");
		$this->CI->load->model("Campclick_model");
		$this->CI->load->model("Ad_model");
		$this->CI->load->model("Log_model");
		$this->CI->load->model("V2_ad_model");
		$this->CI->load->model("V2_master_campaign_model");
        $this->CI->load->model("V2_campaign_network_criteria_rel_model");
		$this->CI->load->model("V2_group_model");
	}

	public function create($campaign)    {
        if (!$campaign) {
            return false;
        }

        return array('status' => 'approved', 'message' => '');
	}

	/**
	 * Processing for THIRD PARTY AD
	 * @param  array $campaign
	 */
	public function process_third_party_and_rich_media_campaign($campaign)
	{
		if (!$campaign){
            throw new exception("campaign required");
        }
        $this->load->model("V2_master_campaign_model");
        $this->load->model("V2_ad_model");
        $this->load->model("V2_ads_disapproval_model");
        $this->CI->load->library('Send_email');
        $data_campaign = array(
        	'network_campaign_status' => 'PAUSED',
        	'campaign_status' => 'ACTIVE',
        );
        $this->V2_master_campaign_model->update($campaign['id'], $data_campaign);

        if ($campaign['campaign_type'] == 'RICH_MEDIA_SURVEY') {
        	$data_ads = array(
        		'creative_is_active' => 'Y',
                //'creative_type'      => 'RICH_MEDIA_SURVEY',
        		'creative_type'      => 'RICH_MEDIA',
        	);
        } else if($campaign['campaign_type'] == 'THIRD-PARTY-AD-TRACK'){
        	$data_ads = array(
        		'creative_is_active' => 'Y',
        		'creative_type'      => 'THIRD-PARTY-AD-TRACK',
        	);
        }

        $this->V2_ad_model->update_all_by_campaign_id($campaign['id'], $data_ads);

        $data_ads_disapproval = array(
        	'status' => 'APPROVED'
        );
        $this->V2_ads_disapproval_model->update_by_campaign_io($campaign['io'], $data_ads_disapproval);

        $link_edit = 'http://reporting.prodata.media/v2/campaign/edit_campaign/'. $campaign['id'];
        $this->send_email->send_approved($campaign['email'], $campaign['io'], $campaign['name'],$campaign['campaign_type'], $link_edit, 'APPROVED');
	}

	public function update_campaign_status($campaign)   {
	    return array();
	}
}