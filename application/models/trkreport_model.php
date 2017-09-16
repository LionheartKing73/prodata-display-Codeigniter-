<?php 

class Trkreport_model extends CI_Model	{

	protected $CI;

	// campaign info
	private $id;
	private $name;
	private $io;
	private $deployment;
	private $date_start;
	private $date_end;
	private $status_money_in_house;
	private $status_creative_approved;
	private $status_client_approved;
	private $status_deployed;
	private $channel_email;
	private $channel_display;
	private $channel_retarget;
	private $channel_social;
	private $client_id;
	private $geo_targeting;
	private $radius;
	private $demo_targeting;
	private $notes;
	private $budget_gross;
	private $budget_adspend;
	private $email_from_name;
	private $email_subject;
	private $email_count;
	private $email_click;
	private $email_open;
	private $display_impressions;
	private $display_clicks;
	private $sales_rep_id;
	
	// client indent info
	private $company;
	private $first_name;
	private $last_name;
	private $email;
	private $phone;
	private $address;
	private $city;
	private $state;
	private $zip;
	
	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		
	}
	
	public function client_create() {
	    $insert = array(
	       "company" => $this->company,
	        "first_name" => $this->first_name,
	        "last_name" => $this->last_name,
	        "email" => $this->email,
	        "phone" => $this->phone,
	        "address" => $this->address,
	        "city" => $this->city,
	        "state" => $this->state,
	        "zip" => $this->zip,
	    );
	    
	    $this->CI->db->insert("prodata_clients", $insert);
	    $id = $this->CI->db->insert_id();
	    
	    if ($id > 0) {
	        return $id;
	    } else {
	        return false;
	    }
	}
	
	public function get_clients()  {
	    $r = $this->CI->db->query("SELECT * FROM prodata_clients ORDER BY company");
	    
	    if ($r->num_rows() > 0) {
	        return $r->result_array();
	    } else {
	        return array();
	    }
	}
	
	public function get_schedule() {
	    $r = $this->CI->db->query("SELECT u.company, u.first_name, u.last_name, p.*, psr.fname AS sales_fname, psr.lname AS sales_lname, psr.id AS sales_id FROM prodata_campaign_schedule p JOIN prodata_clients u ON p.client_id=u.id JOIN prodata_salesreps psr ON psr.id = p.sales_rep_id WHERE p.status_deployed='N' ORDER BY date_start ASC");
	    
	    if ($r->num_rows() > 0) {
	        $campaigns = $r->result_array();
	    } else {
	        $campaigns = array();
	    }
	    
	    return $campaigns;
	    
	}
	
	public function get_campaign() {
	    $r = $this->CI->db->query("SELECT * FROM prodata_campaign_schedule WHERE id='{$this->id}'");
	    
	    if ($r->num_rows() > 0) {
	        $campaign = $r->row_array();
	    } else {
	        $campaign = array();
	    }
	    
	    return $campaign;
	}
	public function campaign_create()  {
	    $insert = array(
	        "client_id" => $this->client_id,
	        "io" => $this->io,
	        "name" => $this->name,
	        "date_start" => $this->date_start,
	        "date_end" => $this->date_end,
	        "geo_targeting" => $this->geo_targeting,
	        "radius" => $this->radius,
	        "demo_targeting" => $this->demo_targeting,
	        "channel_email" => $this->channel_email,
	        "channel_display" => $this->channel_display,
	        "channel_retarget" => $this->channel_retarget,
	        "channel_social" => $this->channel_social,
	        "status_money_in_house" => $this->status_money_in_house,
	        "status_creative_approved" => $this->status_creative_approved,
	        "status_client_approved" => $this->status_client_approved,
	        "status_deployed" => $this->status_deployed,
	        "notes" => $this->notes,
	        "budget_gross" => $this->budget_gross,
	        "budget_adspend" => $this->budget_adspend,
	        "email_from_name" => $this->email_from_name,
	        "email_subject" => $this->email_subject,
	        "email_count" => $this->email_count,
	        "email_click" => $this->email_click,
	        "email_open" => $this->email_open,
	        "display_impressions" => $this->display_impressions,
	        "display_clicks" => $this->display_clicks,
	        "sales_rep_id" => $this->sales_rep_id,
	    );
	    
	    $this->CI->db->insert("prodata_campaign_schedule", $insert);
	    $this->id = $this->CI->db->insert_id();
	    
	    if ($this->id > 0) {
	        return $this->id;
	    } else {
	        return false;
	    }
	}
	
	public function campaign_update() {
	    $update = array(
	        "client_id" => $this->client_id,
	        "io" => $this->io,
	        "name" => $this->name,
	        "date_start" => $this->date_start,
	        "date_end" => $this->date_end,
	        "geo_targeting" => $this->geo_targeting,
	        "radius" => $this->radius,
	        "demo_targeting" => $this->demo_targeting,
	        "channel_email" => $this->channel_email,
	        "channel_display" => $this->channel_display,
	        "channel_retarget" => $this->channel_retarget,
	        "channel_social" => $this->channel_social,
	        "status_money_in_house" => $this->status_money_in_house,
	        "status_creative_approved" => $this->status_creative_approved,
	        "status_client_approved" => $this->status_client_approved,
	        "status_deployed" => $this->status_deployed,
	        "notes" => $this->notes,
	        "budget_gross" => $this->budget_gross,
	        "budget_adspend" => $this->budget_adspend,
	        "email_from_name" => $this->email_from_name,
	        "email_subject" => $this->email_subject,
	        "email_count" => $this->email_count,
	        "email_click" => $this->email_click,
	        "email_open" => $this->email_open,
	        "display_impressions" => $this->display_impressions,
	        "display_clicks" => $this->display_clicks,
	        "sales_rep_id" => $this->sales_rep_id,
	    );
	     
	    $this->CI->db->update("prodata_campaign_schedule", $update, array("id" => $this->id));
	     
	    return $this->id;
	}
	
	public function get_sales_reps() {
	    $r = $this->CI->db->query("SELECT * FROM prodata_salesreps ORDER BY lname,fname");
	    $reps = $r->result_array();
	    
	    return $reps;
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


	public function get_campaign_not_deployed() {
		$r = $this->CI->db->query("SELECT * FROM prodata_campaign_schedule WHERE status_deployed='N'");

		if ($r->num_rows() > 0) {
			$campaign = $r->result_array();
		} else {
			$campaign = array();
		}

		return $campaign;
	}

	public function campaign_update_io($id = null) {
		$update = array(
			"status_deployed" => 'Y'
		);
		
		$this->CI->db->update("prodata_campaign_schedule", $update, array("id" => $id));

		return $this->id;
	}




}

?>
