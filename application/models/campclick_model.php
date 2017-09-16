<?php 

class Campclick_model extends CI_Model	{

	private $collection = "campclick_campaigns";
	protected $CI;

	private $id;
	private $name;
	private $url;
	private $io;
	private $message;
	private $note;
	private $email;
	private $create_date;
	private $is_active = "Y";
	private $conversion_tracking = "N";
	private $max_clicks = 0;
	private $domain = "1";
	private $is_geo = "N";
	private $vendor_id;
	private $userid;
	private $campaign_start_datetime;
	private $campaign_is_started; //Y|N*
	private $campaign_is_complete; // Y|N*
	private $is_traffic_shape = "N";
	private $ppc_network = "FIQ"; // FIQ, EXOCLICK, FACEBOOK, GOOGLE, BING
	private $cap_per_hour = 0; // 0=NO CAP, Should be calculated as a MAX of 1.4% within 1-hour (on a 72-hour campaign)
    private $opens;
	private $MAX_CAP_PER_HOUR_MULTIPLE = 0.10; // 1.4% PER HOUR CAP OF TOTAL CAMPAIGN
	private $fire_open_pixel = "N";
	private $impression_clicks = 0;
	private $local_ad_id = 0;
	
	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->library("user_agent");
		$this->CI->load->model("Domains_model");	
		$this->CI->load->library("clickcap");
	}
	
	public function set_click_cap()    {
	    if ($this->io == "")
	        throw new exception("io required");
	    
	    if (! $this->cap_per_hour > 0)
	        throw new exception("cap_per_hour must be > 0");
	    
	    $this->CI->db->update("campclick_campaigns", array("cap_per_hour"=>$this->cap_per_hour), array("io" => $this->io));
	}
	
	public function update_click_cap() {
	    $this->CI->clickcap->io = $this->io;
	    $this->CI->clickcap->updateClicks();
	}
	
	public function get_current_click_cap()  {
	    /*
	    $date_start = date("Y-m-d H:00:00");
	    $date_end = date("Y-m-d H:59:59");
	    
	    $r = $this->CI->db->query("SELECT COUNT(*) AS cnt FROM campclick_clicks WHERE io='{$this->io}' AND timestamp BETWEEN '{$date_start}' AND '{$date_end}'");
	    
	    if ($r->num_rows() > 0)    {
	        $c = $r->row_array();
	        return (int)$c['cnt'];
	    } else {
	        return 0;
	    }
	    */
	    
	    $this->CI->clickcap->io = $this->io;
	    return (int)$this->CI->clickcap->get_clicks();
	}
	
	public function get_campaign_clicks()	{
		$r = $this->CI->db->query("SELECT COUNT(*) AS cnt FROM campclick_clicks WHERE io='{$this->io}'");
		$count = $r->row_array();
		return (int)$count['cnt'];
	}
	
	public function get_campaign_impressions()	{
	    $r = $this->CI->db->query("SELECT COUNT(*) AS cnt FROM campclick_impressions WHERE io='{$this->io}'");
	    $count = $r->row_array();
	    return (int)$count['cnt'];
	}

	public function campaign_start()	{
		$this->CI->db->update($this->collection, array("campaign_is_started" => "Y"), array("io" => $this->io));
	}

	public function campaign_complete()	{
		$this->CI->db->update($this->collection, array("campaign_is_complete" => "Y"), array("io" => $this->io));
	}
	
	public function mark_ads_completed()	{
		$this->CI->load->library('email');
		 
		$config['protocol'] = 'sendmail';
		$config['mailpath'] = '/usr/sbin/sendmail';
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = TRUE;
		$config['mailtype'] = 'html';
		$config['priority'] = 1;
		
		$this->CI->email->initialize($config);
		
		$runningCampaigns = $this->get_campaign_list(NULL, "Y", "AND campaign_is_started='Y' AND campaign_is_complete='N'");
		
		foreach($runningCampaigns as $r)	{
			$this->io = $r['io'];
			$id = $this->get_campaign_id_from_io();
			$this->id = $id;
			$campaign = $this->get_campaign();
			$click_count = $this->get_campaign_clicks();
			$impression_count = $this->get_campaign_impressions();
			
			if ($r['fire_open_pixel'] == "Y")    {
			    $count = (int)$impression_count;
			} else {
			    $count = (int)$click_count;
			}
			
			$all_links_fulfilled = false;
			$all_links_fulfilled = $this->all_links_fulfilled();

			if (($count >= $campaign['max_clicks'] && $count > 0 && $campaign['max_clicks'] > 0) || ($all_links_fulfilled === true))	{
			//if ($count >= $campaign['max_clicks'] && $count > 0 && $campaign['max_clicks'] > 0)	{
				$msg = "IO: ({$r['io']}) {$campaign['name']}<br/>";
				$msg .= "Total Clicks: {$count}<br/>";
				$msg .= "Ordered Clicks: {$campaign['max_clicks']}<br/>";
				$msg .= "Start Date: {$campaign['campaign_start_datetime']}<br/>";
				
				$this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
				$this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
				$this->CI->email->subject("Report-Site: Campaign Complete {$r['io']}");
				$this->CI->email->message($msg);
				$this->CI->email->send();

				$this->campaign_complete();
			}
		}
	}

	public function create()	{
		$required = array("name", "io", "message", "conversion_tracking", "is_geo", "vendor_id", "campaign_start_datetime", "fire_open_pixel", "impression_clicks");
		foreach($required as $k)	{
			$insert[$k] = $this->$k;
		}
		
		$insert['create_date'] = date("Y-m-d H:i:s");
		$insert['is_active'] = "Y";
		$insert['max_clicks'] = $this->max_clicks;
		$insert['userid'] = $this->userid;
		$insert['is_traffic_shape'] = $this->is_traffic_shape;
		$insert['ppc_network'] = $this->ppc_network;
		$insert['cap_per_hour'] = (int)($this->max_clicks * $this->MAX_CAP_PER_HOUR_MULTIPLE);
		$insert['opens'] = (int)$this->opens;
		$insert['local_ad_id'] = $this->local_ad_id;

		$this->CI->db->insert($this->collection, $insert);
		$this->id = $this->CI->db->insert_id();
		return $this->id;
	}
	
	public function delete()	{
		$this->CI->db->update($this->collection, array("status"=>"N"), array("id"=>$this->id));
	}
	
	public function get_campaign()	{
		$r = $this->CI->db->query("SELECT * FROM {$this->collection} WHERE id='{$this->id}' LIMIT 1");
		if ($r->num_rows() > 0)	{
			return $r->row_array();
		} else {
			return false;
		}
	}
	
	public function get_campaign_list($userid=NULL, $is_active = "Y", $where = "")	{
		$extra_sql = ($userid!='')?' AND c.userid='.$userid:'';
		
		$old_date = date("Y-m-d 23:59:59", strtotime("-3 months"));
		
		if ($extra_sql != "" && $where != "")
			$where = " AND " . $where;

		//$sql = "SELECT io, name, create_date, max_clicks, campaign_start_datetime, is_geo, is_traffic_shape, fire_open_pixel, ppc_network_ad_id FROM {$this->collection} WHERE is_active='{$is_active}' AND create_date >= '{$old_date}' {$extra_sql} {$where} ORDER BY create_date DESC";
		$sql = "SELECT c.io, c.name, c.create_date, c.max_clicks, c.campaign_start_datetime, c.is_geo, c.is_traffic_shape, c.fire_open_pixel, c.ppc_network_ad_id, tpc.is_geo_expanded FROM {$this->collection} c JOIN take5_pending_campaigns tpc ON tpc.io=c.io WHERE c.is_active='{$is_active}' AND c.create_date >= '{$old_date}' {$extra_sql} {$where} ORDER BY c.create_date DESC";
		
		$r = $this->CI->db->query($sql);
		return $r->result_array();
	}
	
	public function get_campaign_by_io()   {
	    $r = $this->CI->db->query("SELECT * FROM campclick_campaigns WHERE io='{$this->io}'");
	    if ($r->num_rows() > 0)    {
	        return $r->row_array();
	    } else {
	        return false;
	    }
	}

	public function get_campaign_id_from_io($loose = false)	{
	    if ($loose === true)   {
	        $r = $this->CI->db->query("SELECT id FROM {$this->collection} WHERE io LIKE '{$this->io}%' LIMIT 1");
	    } else {
    		$r = $this->CI->db->query("SELECT id FROM {$this->collection} WHERE io='{$this->io}' LIMIT 1");
	    }
	    
		$rr = $r->row_array();
		return (int)$rr['id'];
	}
	
	public function report_by_io($io, $mode="hour", $sDate="", $eDate="", $counter=NULL)	{
		
		switch($mode){
			case 'hour':
			case '':
			$date_range = date('Y-m-d h:i:s',strtotime("-1 day"));
			$timestamp_sql = "timestamp > '{$date_range}' ";
			break;
			case 'month':
			$date_range = date('Y-m-d h:i:s',strtotime("-30 day"));
			$timestamp_sql = "timestamp > '{$date_range}' ";
			break;
			case 'daterange':
			$timestamp_sql = "timestamp BETWEEN '{$sDate} 00:00:00' AND  '{$eDate} 23:59:59' ";
			//echo "SELECT COUNT(DISTINCT ip_address) AS unique_clickers FROM campclick_clicks WHERE $timestamp_sql AND io='{$io}'";
		}
		
		$group_count_results = array();
		$r = $this->CI->db->query("SELECT link_id, dest_url, counter, max_clicks, is_fulfilled FROM campclick_links WHERE io='{$io}'");
		foreach($r->result_array() as $l)	{
			$countr = $this->CI->db->query("SELECT COUNT(*) AS group_count FROM campclick_clicks WHERE io='{$io}' AND link_id='{$l['link_id']}' AND {$timestamp_sql}");
			$c = $countr->row_array();
			$l['group_count'] = $c['group_count'];
			$group_count_results[] = $l;
		}
		//$r = $this->CI->db->query("SELECT COUNT(*) AS group_count, c.link_id, l.dest_url, l.counter FROM campclick_clicks c JOIN campclick_links l ON l.link_id=c.link_id WHERE c.$timestamp_sql AND c.io='{$io}' GROUP BY c.link_id");
		//$group_count_results = $r->result_array();
				
		$r = $this->CI->db->query("SELECT COUNT(DISTINCT ip_address) AS unique_clickers FROM campclick_clicks WHERE $timestamp_sql AND io='{$io}'");
		$rr = $r->row_array();

		$unique_clickers = $rr['unique_clickers'];
		
		$r = $this->CI->db->query("SELECT COUNT(IF(is_mobile = 'Y', 1, null)) AS mobile, COUNT(IF(is_mobile = 'N', 1, null)) AS non_mobile FROM campclick_clicks WHERE $timestamp_sql AND io='{$io}' ");
		$mobile_results = $r->row_array();
		
		$r = $this->CI->db->query("select count(*) as cnt, web_browser from campclick_clicks where $timestamp_sql AND io='{$io}'  group by web_browser order by cnt desc");
		$browser_results = $r->result_array();
		
		$r = $this->CI->db->query("select count(*) as cnt, mobile_device from campclick_clicks where $timestamp_sql AND io='{$io}' and mobile_device <> '' group by mobile_device order by cnt desc");
		$mobile_devices = $r->result_array();
		
		$r = $this->CI->db->query("select count(*) as cnt, platform from campclick_clicks where $timestamp_sql AND io='{$io}' and platform <> '' group by platform order by cnt desc");
		$platform_results = $r->result_array();
		
		$r = $this->CI->db->query("select count(*) as cnt, SUM(conversion_value) AS conversion_value from campclick_clicks where $timestamp_sql AND io='{$io}' and conversion_value > 0");
		$conversion_results = $r->result_array();

		$hourly_total_clicks = $this->get_click_count_by_hour($io, $mode, false, $sDate, $eDate, $counter);
		$hourly_uniqueip_total_clicks = $this->get_click_count_by_hour($io, $mode, true, $sDate, $eDate, $counter);
		$hourly_impression_views = $this->get_click_count_by_hour($io, $mode, true, $sDate, $eDate, $counter, "campclick_impressions");

		$browser_shares = $this->get_browser_share($io, $mode, $sDate, $eDate, $counter);
		$platform_used = $this->get_platform($io, $mode, $sDate, $eDate, $counter);
		
		$r = $this->CI->db->query("SELECT count(*) as cnt FROM campclick_impressions WHERE io='{$io}' AND {$timestamp_sql}");
		$impression_results = $r->result_array();

		$impression_total = 0;
		foreach($impression_results as $ii)   {
		    $impression_total += $ii['cnt'];
		}
		
		return array(
			'group_count_results' => $group_count_results,
			'unique_clickers' => $unique_clickers,
			'mobile_results' => $mobile_results,
			'mobile_devices' => $mobile_devices,
			'browser_results' => $browser_results,
			'platform_results' => $platform_results,
			'conversion_results' => $conversion_results,
			'hourly_total_clicks' => $hourly_total_clicks,
			'hourly_unqiue_total_clicks' => $hourly_uniqueip_total_clicks,
			'browsers_shares' => $browser_shares,
			'platform'=>$platform_used,
		    'hourly_impressions' => $hourly_impression_views,
		    'impressions_total' => $impression_total,
		);
	}


	public function report_by_io_counter($io, $mode="hour", $sDate="", $eDate="", $counter=NULL)	{
		
		
		
		switch($mode){
			case 'hour':
			case '':
			$date_range = date('Y-m-d h:i:s',strtotime("-1 day"));
			$timestamp_sql = "timestamp > '{$date_range}' ";
			break;
			case 'month':
			$date_range = date('Y-m-d h:i:s',strtotime("-30 day"));
			$timestamp_sql = "timestamp > '{$date_range}' ";
			break;
			case 'daterange':
			$timestamp_sql = "timestamp BETWEEN '{$sDate} 00:00:00' AND  '{$eDate} 23:59:59' ";
		}
		$counter_sql = isset($counter)?" AND l.counter = $counter":'';
		
		$r = $this->CI->db->query("SELECT COUNT(*) AS group_count, c.link_id, l.dest_url, l.counter FROM campclick_clicks c JOIN campclick_links l ON l.link_id=c.link_id AND l.counter=$counter WHERE c.$timestamp_sql AND c.io='{$io}' GROUP BY c.link_id");
		$group_count_results = $r->result_array();
		
		
		$r = $this->CI->db->query("SELECT COUNT(DISTINCT c.ip_address) AS unique_clickers FROM campclick_clicks c, campclick_links l WHERE c.$timestamp_sql AND c.io='{$io}' AND c.link_id = l.link_id $counter_sql");
		$rr = $r->row_array();

		$unique_clickers = $rr['unique_clickers'];
		
		$r = $this->CI->db->query("SELECT COUNT(IF(c.is_mobile = 'Y', 1, null)) AS mobile, COUNT(IF(c.is_mobile = 'N', 1, null)) AS non_mobile FROM campclick_clicks c, campclick_links l WHERE c.$timestamp_sql AND c.io='{$io}' AND c.link_id = l.link_id  $counter_sql");
		$mobile_results = $r->row_array();
		
		$r = $this->CI->db->query("select count(*) as cnt, c.web_browser from campclick_clicks c, campclick_links l where c.$timestamp_sql AND c.io='{$io}' AND c.link_id = l.link_id $counter_sql group by web_browser order by cnt desc");
		$browser_results = $r->result_array();
		
		$r = $this->CI->db->query("select count(*) as cnt, c.mobile_device from campclick_clicks c, campclick_links l where c.$timestamp_sql AND c.io='{$io}' and c.mobile_device <> '' AND c.link_id = l.link_id $counter_sql group by c.mobile_device order by cnt desc");
		$mobile_devices = $r->result_array();
		
		$r = $this->CI->db->query("select count(*) as cnt, c.platform from campclick_clicks c, campclick_links l where c.$timestamp_sql AND c.io='{$io}' and c.platform <> '' AND c.link_id=l.link_id $counter_sql group by c.platform order by cnt desc");
		$platform_results = $r->result_array();
		
		$r = $this->CI->db->query("select count(*) as cnt, SUM(c.conversion_value) AS conversion_value from campclick_clicks c, campclick_links l where c.$timestamp_sql AND c.io='{$io}' and c.conversion_value > 0 AND c.link_id = l.link_id $counter_sql");
		$conversion_results = $r->result_array();

		$hourly_total_clicks = $this->get_link_click_count_by_hour($io, $mode, false, $sDate, $eDate, $counter);
		$hourly_uniqueip_total_clicks = $this->get_link_click_count_by_hour($io, $mode, true, $sDate, $eDate, $counter, null, "campclick_impressions");

		$browser_shares = $this->get_counter_browser_share($io, $mode, $sDate, $eDate, $counter);
		$platform_used = $this->get_counter_platform($io, $mode, $sDate, $eDate, $counter);

		return array(
			'group_count_results' => $group_count_results,
			'unique_clickers' => $unique_clickers,
			'mobile_results' => $mobile_results,
			'mobile_devices' => $mobile_devices,
			'browser_results' => $browser_results,
			'platform_results' => $platform_results,
			'conversion_results' => $conversion_results,
			'hourly_total_clicks' => $hourly_total_clicks,
			'hourly_unqiue_total_clicks' => $hourly_uniqueip_total_clicks,
			'browsers_shares' => $browser_shares,
			'platform'=>$platform_used
		);
	
	}
	
	public function log_conversion($io = "", $link_id = 0, $conversion_value = 1)	{
        $referrer = $this->CI->agent->referrer();
        $p = parse_url($referrer);
        $host = (isset($p['host'])) ? $p['host'] : '';

		$insert = array(
			"io" => $io,
			"link_id" => $link_id,
			"ip_address" => $this->CI->input->ip_address(),
			"user_agent" => $this->CI->input->user_agent(),
			"timestamp" => date("Y-m-d H:i:s"),
			"is_mobile" => ($this->CI->agent->is_mobile()) ? "Y" : "N",
			"web_browser" => $this->CI->agent->browser() . " - " . $this->CI->agent->version(),
			"mobile_device" => $this->CI->agent->mobile(),
			"platform" => $this->CI->agent->platform(),
            "referrer" => $referrer,
            "referrer_host" => $host,
			"conversion_value" => sprintf("%.2f", $conversion_value)
		);
		
		$this->CI->db->insert("campclick_clicks", $insert);		
	}
	
	public function log_click($io = "", $link_id = "", $is_fraud = false)	{
        $referrer = $this->CI->agent->referrer();
        $p = parse_url($referrer);
        $host = (isset($p['host'])) ? $p['host'] : '';

		$insert = array(
			"io" => $io,
			"link_id" => $link_id,
			"ip_address" => $this->CI->input->ip_address(),
			"user_agent" => $this->CI->input->user_agent(),
			"timestamp" => date("Y-m-d H:i:s"),
			"is_mobile" => ($this->CI->agent->is_mobile()) ? "Y" : "N",
			"web_browser" => $this->CI->agent->browser() . " - " . $this->CI->agent->version(),
			"mobile_device" => $this->CI->agent->mobile(),
			"platform" => $this->CI->agent->platform(),
			"referrer" => $referrer,
            "referrer_host" => $host
		);
		
		// fraud monitoring and filtering of duplicate IP's, $is_fraud is a forced entry as well
		if ($is_fraud === false)	{
			$this->CI->load->library("fraudfiltering");
			$this->CI->fraudfiltering->ipaddress = $this->CI->input->ip_address();
			$this->CI->fraudfiltering->io = $io;
			$this->CI->fraudfiltering->io = $link_id;
			$is_fraud = $this->CI->fraudfiltering->checkFraud();
		}

		$insert['is_fraud'] = ($is_fraud === true) ? "Y" : "N";

		$this->CI->db->insert("campclick_clicks", $insert);
		
		$this->CI->clickcap->io = $io;
		$this->CI->clickcap->link_id = $link_id;
		$this->CI->clickcap->linkClicks();
	}
	

	public function log_impression($io = "", $pixel_id, $is_fraud = false)	{
	    $referrer = $this->CI->agent->referrer();
	    $p = parse_url($referrer);
	    $host = (isset($p['host'])) ? $p['host'] : '';
	
	    $insert = array(
	        "io" => $io,
	        "pixel_id" => $pixel_id,
	        "ip_address" => $this->CI->input->ip_address(),
	        "user_agent" => $this->CI->input->user_agent(),
	        "timestamp" => date("Y-m-d H:i:s"),
	        "is_mobile" => ($this->CI->agent->is_mobile()) ? "Y" : "N",
	        "web_browser" => $this->CI->agent->browser() . " - " . $this->CI->agent->version(),
	        "mobile_device" => $this->CI->agent->mobile(),
	        "platform" => $this->CI->agent->platform(),
	        "referrer" => $referrer,
	        "referrer_host" => $host
	    );
	
	    // fraud monitoring and filtering of duplicate IP's, $is_fraud is a forced entry as well
	    if ($is_fraud === false)	{
	        $this->CI->load->library("fraudfiltering");
	        $this->CI->fraudfiltering->ipaddress = $this->CI->input->ip_address();
	        $this->CI->fraudfiltering->io = $io;
	        $this->CI->fraudfiltering->io = $link_id;
	        $is_fraud = $this->CI->fraudfiltering->checkFraud();
	    }
	
	    $insert['is_fraud'] = ($is_fraud === true) ? "Y" : "N";
	
	    $this->CI->db->insert("campclick_impressions", $insert);
	    
	    $this->CI->clickcap->io = $io;
	    $this->CI->clickcap->link_id = $link_id;
	    $this->CI->clickcap->linkImpressions();
	}

	public function create_links($url = "", $io = "", $counter = 0, $max_clicks = 9999999)	{
		$url = str_ireplace("(", "%28", $url);
		$url = str_ireplace(")", "%29", $url);
		$url = str_ireplace("[", "%5B", $url);
		$url = str_ireplace("]", "%5D", $url);
		$url = str_ireplace("{", "%7B", $url);
		$url = str_ireplace("}", "%7D", $url);
		$url = str_ireplace("'", "", $url);
		$url = str_ireplace('"', "", $url);
		
		$insert = array(
			"dest_url" => $url,
			"io" => $io,
			"counter" => $counter,
			"max_clicks" => $max_clicks,
			"is_fulfilled" => "N"
		);

		$this->CI->db->insert("campclick_links", $insert);
	}
	
	public function update_link($link_id, $url, $max_clicks, $is_fulfilled = "N")  {
	    $url = str_ireplace("(", "%28", $url);
	    $url = str_ireplace(")", "%29", $url);
	    $url = str_ireplace("[", "%5B", $url);
	    $url = str_ireplace("]", "%5D", $url);
	    $url = str_ireplace("{", "%7B", $url);
	    $url = str_ireplace("}", "%7D", $url);
	    $url = str_ireplace("'", "", $url);
	    $url = str_ireplace('"', "", $url);
	    
	    $update = array(
	        "dest_url" => $url,
	        "max_clicks" => $max_clicks,
	        "is_fulfilled" => $is_fulfilled
	    );
	    
	    $this->CI->db->update("campclick_links", $update, array("link_id" => (int)$link_id));
	}
	
	public function create_new_link($url = "", $io = "", $max_clicks = 9999999)	{
	    $url = str_ireplace("(", "%28", $url);
	    $url = str_ireplace(")", "%29", $url);
	    $url = str_ireplace("[", "%5B", $url);
	    $url = str_ireplace("]", "%5D", $url);
	    $url = str_ireplace("{", "%7B", $url);
	    $url = str_ireplace("}", "%7D", $url);
	    $url = str_ireplace("'", "", $url);
	    $url = str_ireplace('"', "", $url);
	
	    $counter = 0;
	    $r = $this->CI->db->query("SELECT MAX(counter) AS cnt FROM campclick_links WHERE io='{$io}'");
	    if ($r->num_rows() > 0)    {
	        $rr = $r->row_array();
	        $counter = (int)$rr['cnt'];
	    }
	    
	    $insert = array(
	        "dest_url" => $url,
	        "io" => $io,
	        "counter" => $counter,
	        "max_clicks" => $max_clicks,
	        "is_fulfilled" => "N"
	    );
	
	    $link_id = $this->CI->db->insert("campclick_links", $insert);
	    
	    return $link_id;
	}

	public function get_link($io = "", $counter = 0)	{
		$r = $this->CI->db->query("SELECT link_id,dest_url,max_clicks,is_fulfilled FROM campclick_links WHERE io='{$io}' AND counter='{$counter}' AND is_fulfilled='N' LIMIT 1");
		return $r->row_array();
	}
	
	public function get_link_by_id($link_id)	{
	    $r = $this->CI->db->query("SELECT link_id,dest_url,max_clicks,is_fulfilled FROM campclick_links WHERE link_id='{$link_id}'");
	    return $r->row_array();
	}
	
	public function get_link_count()    {
	    if ($this->io == "")
	        throw new exception("select_random_link(): io required");
	     
	    $r = $this->CI->db->query("SELECT COUNT(*) AS cnt FROM campclick_links WHERE io='{$this->io}' AND is_fulfilled='N'");
	    if ($r->num_rows() > 0)    {
	        $cnt = $r->row_array();
	        return (int)$cnt['cnt'];
	    } else {
	        return 0;
	    }
	}
	
	public function select_random_link()	{
		if ($this->io == "")
			throw new exception("select_random_link(): io required");

		$r = $this->CI->db->query("SELECT counter,link_id FROM campclick_links WHERE io='{$this->io}' AND is_fulfilled='N' AND link_id <> 0 AND dest_url <> '' ORDER BY RAND() LIMIT 1");
		//$r = $this->CI->db->query("SELECT counter,link_id FROM campclick_links WHERE link_id IN(SELECT link_id FROM campclick_links WHERE io='{$this->io}' ORDER BY RAND())")->result_array();
		if ($r->num_rows() > 0)	{
			return $r->row_array();
//			return $r[array_rand($r, 1);
		} else {
			return false;
		}
	}
	
	public function select_random_link_improved()	{
		if ($this->io == "")
			throw new exception("select_random_link(): io required");

		$r = $this->CI->db->query("SELECT counter,link_id FROM campclick_links WHERE link_id IN(SELECT link_id FROM campclick_links WHERE io='{$this->io}' AND is_fulfilled='N' ORDER BY RAND()) AND is_fulfilled='N' AND io='{$this->io}'");
		if ($r->num_rows() > 0)	{
			$rr = $r->result_array();
			return $rr[array_rand($rr, 1)];
		} else {
			return false;
		}
	}
	
	public function update_fulfilled_count($io = "", $link_id = 0)	{
	    /*
		$r = $this->CI->db->query("SELECT COUNT(*) AS cnt, cl.is_fulfilled, cl.max_clicks FROM campclick_clicks cc JOIN campclick_links cl ON cl.io=cc.io AND cc.link_id=cl.link_id WHERE cc.io='{$io}' AND cl.counter='{$link_id}'");
		if ($r->num_rows() > 0)	{
			$c = $r->row_array();
			if ($c['cnt'] >= $c['max_clicks'])	{
			    //
				//REMVOED DUE TO FORD PROBLEM
				//$this->CI->db->query("UPDATE campclick_links SET is_fulfilled='Y' WHERE counter='{$link_id}' AND io='{$io}' LIMIT 1");
				//
			}
		}
		*/
	}
	
	public function update_fulfilled_status()	{
		$create_date = date("Y-m-d 00:00:00", strtotime("-45 days"));

		$r = $this->CI->db->query("SELECT io FROM campclick_campaigns WHERE is_active='Y' AND campaign_is_started='Y' AND campaign_is_complete='N' AND create_date >= '{$create_date}'");
		if ($r->num_rows() > 0)	{
			foreach($r->result_array() as $row)	{
			    /*
				$r = $this->CI->db->query("SELECT cl.link_id, cl.max_clicks, COUNT(DISTINCT cc.ip_address) AS cnt, cl.is_fulfilled FROM campclick_clicks cc JOIN campclick_links cl ON cl.io=cc.io AND cc.link_id=cl.link_id WHERE cc.io='{$row['io']}' AND cl.is_fulfilled='N' GROUP BY cl.link_id");
				//$r = $this->CI->db->query("SELECT cl.link_id, cl.max_clicks, COUNT(*) AS cnt, cl.is_fulfilled FROM campclick_clicks cc JOIN campclick_links cl ON cl.io=cc.io AND cc.link_id=cl.link_id WHERE cc.io='{$row['io']}' AND cl.is_fulfilled='N' GROUP BY cl.link_id");
				if ($r->num_rows() > 0)	{
					foreach($r->result_array() as $rr)	{
						if ($rr['max_clicks'] <= $rr['cnt'] && $rr['max_clicks'] > 0 && $rr['cnt'] > 0)	{
						    //
							//REMOVED DUE TO FORD PROBLEM
						    //$this->CI->db->query("UPDATE campclick_links SET is_fulfilled='Y' WHERE link_id='{$rr['link_id']}'");
						    //
						}
					}
				}
				*/
			    
			    $rr = $this->CI->db->query("SELECT link_id, io, counter, max_clicks FROM campclick_links WHERE io='{$row['io']}' AND is_fulfilled='N'");
			    if ($rr->num_rows() > 0) {
			        foreach($rr->result_array() as $rrow)    {
			            if ($rrow['max_clicks'] != 9999999 || $rrow['max_clicks'] != 0)  {
			                $this->CI->clickcap->link_id = $rrow['link_id'];
			                $link_clicks = $this->CI->clickcap->get_link_clicks();
			                
			                // set this to fulfilled if we have exceeded 1.15% of max_clicks
			                if ($link_clicks >= ($rrow['max_clicks'] * 1.15)) {
			                    $this->CI->db->query("UPDATE campclick_links SET is_fulfilled='Y' WHERE link_id='{$rrow['link_id']}'");
			                }
			            }
			        }
			    }
			}
		}
	}
	
	public function check_fulfilled_status($io = "")	{
		$r = $this->CI->db->query("SELECT io FROM campclick_campaigns WHERE is_active='Y'");
		if ($r->num_rows() > 0)   {
		    foreach($r->result_array() as $row)   {
		        $rr = $this->CI->db->query("SELECT * FROM campclick_links WHERE io='{$row['io']}'");
		        
		        if ($rr->num_rows() > 0)  {
		            foreach($rr->result_array() as $rrow) {
		                $rrr = $this->CI->db->query("SELECT COUNT(*) AS cnt FROM campclick_clicks WHERE link_id='{$rrow['link_id']}' LIMIT 1");
		                
		                if ($rrr->num_rows() > 0) {
		                    $data = $rrr->row_array();
		                    
		                    if ($data['cnt'] == 0 && $rrow['is_fulfilled'] == "Y")    {
		                        $this->CI->db->query("UPDATE campclick_links SET is_fulfilled='N' WHERE link_id='{$rrow['link_id']}' LIMIT 1");
		                        print "{$row['io']} - {$rrow['link_id']}\n";
		                    }
		                }
		            }
		        }
		    }
		}
	}
	
	/**
	 * 
	 * IPInfo Database
	 * http://www.ipinfodb.com
	 * 
	 */
	public function geo_track_update($max = 60, $io = "")	{
		$last_month = date("Y-m-d 23:59:59", strtotime("-30 days"));
		
		if ($io != "")	{
			$io_where = " AND cp.io='{$io}' ";
		} else {
			$io_where = "";
		}
		
		$r = $this->CI->db->query("SELECT cc.ip_address, cp.io FROM campclick_clicks cc JOIN campclick_campaigns cp ON cp.io=cc.io WHERE cp.is_active='Y' AND cc.is_geo='N' AND cp.is_geo='Y' AND cp.create_date >= '{$last_month}' AND cc.timestamp >= '{$last_month}' {$io_where} LIMIT {$max}");
		if ($r->num_rows() > 0)	{
			foreach($r->result_array() as $rr)	{
print_r($rr);
				//$geo = file_get_contents("http://api.ipinfodb.com/v3/ip-city/?key=1c430670ed76c447693b01106fd1f22241dac55eb98c7840b00940c29b360b1e&ip={$rr['ip_address']}&format=json");
				$geo = file_get_contents("http://api.hostip.info/get_json.php?ip={$rr['ip_address']}&position=true");
				$geo = json_decode($geo);
				
				/*
				if (isset($geo->statusCode) && $geo->statusCode == "OK")	{
					$update = array(
						"geo_country" => $geo->countryCode,
						"geo_region" => $geo->regionName,
						"is_geo" => "Y",
						"geo_lat" => $geo->latitude,
						"geo_lon" => $geo->longitude
					);
					
					// update to geo
					$this->CI->db->update("campclick_clicks", $update, array("ip_address" => $rr['ip_address'], "is_geo" => "N"));
				}
				*/

				list($city, $state) = explode(", ", $geo->city, 2);
				$update = array("geo_country" => $geo->country_code, "geo_region" => $state, "geo_lat" => $geo->lat, "geo_lon" => $geo->lng, "is_geo" => "Y");
				$this->CI->db->update("campclick_clicks", $update, array("ip_address" => $rr['ip_address'], "is_geo" => "N", "io"=>$rr['io']));

				$geo->io = $rr['io'];
				print_r($geo);
			}
		}
	}


	public function get_click_count_by_hour($io, $mode="hour", $is_unique = false, $sDate="", $eDate="", $link_id=NULL, $table = "campclick_clicks"){
		switch($mode){
			case 'hour':
			case '':
			$categories = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$category_title = "Last 24-Hours";
			$date = isset($date)?$date:date('Y-m-d h:i:s',strtotime("-1 day"));
			$timestamp_sql = " timestamp > '{$date}' ";
			$group_by = "GROUP BY hour ORDER BY hour";
			break;
			case 'month':
			$categories = array();
			for($i = 30; $i>0; $i--){
				$categories[date('"M-d"',strtotime('-'.$i.' day'))] = 0;
			}
			$date = isset($date)?$date:date('Y-m-d h:i:s',strtotime("-30 day"));
			$timestamp_sql = " timestamp > '{$date}' ";
			$category_title = "Last 30-Days";
			$group_by = "GROUP BY day ORDER BY day";
			break;
			case 'daterange':
			$categories = array();
			$xaxis_categories = array();
			$date_count = $this->IntervalDays($sDate,$eDate);
			
			for($i = strtotime($sDate); $i<=strtotime($eDate); $i = $i+86400){
				//$categories[date('"M-d"',strtotime('-'.$i.' day'))] = 0;
				$categories[date('"M-d"',$i)] = 0;
				array_push($xaxis_categories,date('d',strtotime('-'.$i.' day')));
			}			
			$timestamp_sql = " timestamp BETWEEN '{$sDate} 00:00:00' AND  '{$eDate} 23:59:59' ";
			$category_title = $sDate.' to '.$eDate;		
			$group_by = "GROUP BY day ORDER BY day";
		}
		if($is_unique)
			$unique_sql = "DISTINCT ip_address";
		else
			$unique_sql = "*";
		
		$link_id = isset($link_id)?" AND link_id = '$link_id'":'';
		//echo "SELECT date(timestamp) AS day,  HOUR(timestamp) AS hour, COUNT($unique_sql) AS cnt FROM   campclick_clicks WHERE  $timestamp_sql  AND io='{$io}' $link_id $group_by";
		$r = $this->CI->db->query("SELECT date(timestamp) AS day,  HOUR(timestamp) AS hour, COUNT($unique_sql) AS cnt FROM {$table} WHERE  $timestamp_sql  AND io='{$io}' $link_id $group_by");
		$conversion_results = $r->result_array();	
		$clicks_of_hours = array();
		if($mode == "daterange")
		$date_count = $this->IntervalDays($sDate,$eDate);
		$c = 1;
		if(count($conversion_results)>0){
			foreach($conversion_results as $k=>$v){
				if($mode == "hour")
					$categories[$v['hour']] = $v['cnt'];
				else if($mode == "month"){
					$tmp_d = date('"M-d"',strtotime($v['day']));
					$categories[$tmp_d] = $v['cnt'];
				}
				else{
					$tmp_d = date('"M-d"',strtotime($v['day']));
					$categories[$tmp_d] = $v['cnt'];					
					if($date_count==$c)
					break;
				}
				$c++;
				
			}
		}
		
		$data = implode(',',$categories); 

		return array(
					 "data" => $data,
					 "categories" => implode(',',array_keys($categories)),
					 "category_title" => $category_title,
					 );

	}
	

	public function get_browser_share($io=NULL, $mode="hour", $sDate="", $eDate="", $link_id=NULL){
		$browsers = array();
		switch($mode){
			case 'hour':
			case '':
			$date_range = date('Y-m-d h:i:s',strtotime("-1 day"));
			$timestamp_sql = " timestamp > '{$date_range}' ";
			break;
			case 'month':
			$date_range = date('Y-m-d h:i:s',strtotime("-30 day"));
			$timestamp_sql = " timestamp > '{$date_range}' ";
			break;
			case 'daterange':
			$timestamp_sql = " timestamp BETWEEN '{$sDate} 00:00:00' AND  '{$eDate} 23:59:59' ";
		}		

		$link_id = isset($link_id)?" AND link_id = '$link_id'":'';
		$r = $this->CI->db->query("select count(*) as cnt, web_browser from campclick_clicks where  $timestamp_sql AND io='{$io}' AND web_browser LIKE '%Internet Explorer%' $link_id order by cnt desc");
		$ie_results = $r->result_array();
		$browsers['IE'] = $ie_results[0]['cnt'];

		
		$r = $this->CI->db->query("select count(*) as cnt, web_browser from campclick_clicks where $timestamp_sql AND io='{$io}' AND web_browser LIKE '%Chrome%' $link_id order by cnt desc");
		$chrome_results = $r->result_array();
		$browsers['Chrome'] = $chrome_results[0]['cnt'];
		
		$r = $this->CI->db->query("select count(*) as cnt, web_browser from campclick_clicks where $timestamp_sql AND io='{$io}' AND web_browser LIKE '%Firefox%' $link_id order by cnt desc");
		$firefox_results = $r->result_array();
		$browsers['Firefox'] = $firefox_results[0]['cnt'];
		
		
		$r = $this->CI->db->query("select count(*) as cnt, web_browser from campclick_clicks where $timestamp_sql AND io='{$io}' AND web_browser LIKE '%Safari%' $link_id order by cnt desc");
		$sarafi_results = $r->result_array();
		$browsers['Safari'] = $sarafi_results[0]['cnt'];
		
		
		$r = $this->CI->db->query("select count(*) as cnt, web_browser from campclick_clicks where $timestamp_sql AND io='{$io}' AND web_browser LIKE '%Opera%' $link_id order by cnt desc");
		$opera_results = $r->result_array();
		$browsers['Opera'] = $opera_results[0]['cnt'];
		
		
		$r = $this->CI->db->query("select count(*) as cnt, web_browser from campclick_clicks where $timestamp_sql AND io='{$io}' $link_id");
		$all_results = $r->result_array();
		$browsers['Others'] = $all_results[0]['cnt']- $ie_results[0]['cnt'] - $chrome_results[0]['cnt'] - $firefox_results[0]['cnt'] - $sarafi_results[0]['cnt'] ;
		
		return $browsers;
		
 		//return implode(',',$clicks_of_hours);

	}
	
	public function get_platform($io=NULL, $mode ="hour", $sDate="", $eDate="", $link_id=NULL){
		$platform = array();
		switch($mode){
			case 'hour':
			case '':
			$date_range = date('Y-m-d h:i:s',strtotime("-1 day"));
			$timestamp_sql = " timestamp > '{$date_range}' ";
			break;
			case 'month':
			$date_range = date('Y-m-d h:i:s',strtotime("-30 day"));
			$timestamp_sql = " timestamp > '{$date_range}' ";
			break;
			case 'daterange':
			$timestamp_sql = " timestamp BETWEEN '{$sDate} 00:00:00' AND  '{$eDate} 23:59:59' ";
			break;
		}
		
		$link_id = isset($link_id)?" AND link_id = '$link_id'":'';
		
		$r = $this->CI->db->query("select count(*) as cnt, web_browser from campclick_clicks where io='{$io}' $link_id");
		$all_results = $r->result_array();
		
		$r = $this->CI->db->query("SELECT count( * ) AS cnt, platform FROM campclick_clicks WHERE $timestamp_sql AND io='{$io}' AND platform LIKE '%Windows%' $link_id ORDER BY cnt DESC");
		$windows = $r->result_array();
		$platform['Windows'] =  $windows[0]['cnt'] ;
		
		$r = $this->CI->db->query("SELECT count( * ) AS cnt, platform FROM campclick_clicks WHERE  $timestamp_sql AND io='{$io}' AND platform LIKE '%Mac%' $link_id ORDER BY cnt DESC");
		$mac = $r->result_array();
		$platform['Mac'] =  $mac[0]['cnt'] ;


		$r = $this->CI->db->query("SELECT count( * ) AS cnt, platform FROM campclick_clicks WHERE $timestamp_sql AND io='{$io}' AND platform LIKE '%Solaris%' $link_id ORDER BY cnt DESC");
		$solaris = $r->result_array(); 
		$platform['Solaris'] =  $solaris[0]['cnt'] ;
		

		$r = $this->CI->db->query("SELECT count( * ) AS cnt, platform FROM campclick_clicks WHERE $timestamp_sql AND io='{$io}' AND platform LIKE '%FreeBSD%' $link_id ORDER BY cnt DESC");
		$freebsd = $r->result_array();
		$platform['FreeBSD'] =  $freebsd[0]['cnt'] ;
		
		$r = $this->CI->db->query("SELECT count( * ) AS cnt, platform FROM campclick_clicks WHERE  $timestamp_sql AND io='{$io}' AND platform LIKE '%Linux%' $link_id ORDER BY cnt DESC");
		$linux = $r->result_array();
		$platform['Linux'] = $linux[0]['cnt'] ;
		
		


		return $platform;
 		//return implode(',',$clicks_of_hours);

	}	
	
	public function get_click_count()  {
	    $r = $this->CI->db->query("SELECT COUNT(*) AS current_clicks FROM campclick_clicks WHERE io='{$this->io}'");
	    if ($r->num_rows() > 0)    {
	        $tmp = $r->row_array();
	        return (int)$tmp['current_clicks'];
	    } else {
	        return 0;
	    }
	}

	public function get_click_count_for_hour($is_impression = false)  {
	    $date_start = date("Y-m-d H:00:00");
	    $date_end = date("Y-m-d H:59:59");

	    if ($is_impression === false)  {
	        $r = $this->CI->db->query("SELECT COUNT(*) AS current_clicks_for_hour FROM campclick_clicks WHERE io='{$this->io}' AND timestamp BETWEEN '{$date_start}' AND '{$date_end}'");
	    } else {
	        $r = $this->CI->db->query("SELECT COUNT(*) AS current_clicks_for_hour FROM campclick_impressions WHERE io='{$this->io}' AND timestamp BETWEEN '{$date_start}' AND '{$date_end}'");
	    }
	    
	    if ($r->num_rows() > 0)    {
	        $tmp = $r->row_array();
            return array("clicks" => (int)$tmp['current_clicks_for_hour'], "hour" => date("H"));
	    } else {
	        return 0;
	    }
	}

	#MORE INFO
	public function get_link_click_count_by_hour($io, $mode="hour", $is_unique = false, $sDate="", $eDate="", $counter=NULL){
		switch($mode){
			case 'hour':
			case '':
			$categories = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$category_title = "Last 24-Hours";
			$date = isset($date)?$date:date('Y-m-d h:i:s',strtotime("-1 day"));
			$timestamp_sql = " timestamp > '{$date}' ";
			$group_by = "GROUP BY hour ORDER BY hour";
			break;
			case 'month':
			$categories = array();
			for($i = 30; $i>0; $i--){
				$categories[date('"M-d"',strtotime('-'.$i.' day'))] = 0;
			}
			$date = isset($date)?$date:date('Y-m-d h:i:s',strtotime("-30 day"));
			$timestamp_sql = " c.timestamp > '{$date}' ";
			$category_title = "Last 30-Days";
			$group_by = "GROUP BY day ORDER BY day";
			break;
			case 'daterange':
			$categories = array();
			$date_count = $this->IntervalDays($sDate,$eDate);
			
			for($i = strtotime($sDate); $i<=strtotime($eDate); $i = $i+86400){
				//$categories[date('"M-d"',strtotime('-'.$i.' day'))] = 0;
				$categories[date('"M-d"',$i)] = 0;
				
			}		
			$timestamp_sql = "c.timestamp BETWEEN '{$sDate} 00:00:00' AND  '{$eDate} 23:59:59' ";
			$category_title = $sDate.' to '.$eDate;		
			$group_by = "GROUP BY day ORDER BY day";
		}
		if($is_unique)
			$unique_sql = "DISTINCT c.ip_address";
		else
			$unique_sql = "*";
		
		$link_id = isset($link_id)?" AND link_id = '$link_id'":'';
		$r = $this->CI->db->query("SELECT date(c.timestamp) AS day,  HOUR(c.timestamp) AS hour, COUNT($unique_sql) AS cnt FROM   campclick_clicks c, campclick_links l WHERE  $timestamp_sql  AND c.io='{$io}' AND c.link_id=l.link_id AND l.counter={$counter} $group_by");
		$conversion_results = $r->result_array();	
		$clicks_of_hours = array();
		if($mode == "daterange")
		$date_count = $this->IntervalDays($sDate,$eDate);
		$c = 1;
		if(count($conversion_results)>0){
			foreach($conversion_results as $k=>$v){
				if($mode == "hour")
					$categories[$v['hour']] = $v['cnt'];
				else if($mode == "month"){
					$tmp_d = date('"M-d"',strtotime($v['day']));
					$categories[$tmp_d] = $v['cnt'];
				}
				else{
					$tmp_d = date('"M-d"',strtotime($v['day']));
					$categories[$tmp_d] = $v['cnt'];					
					if($date_count==$c)
					break;
				}
				$c++;
				
			}
		}
		
		$data = implode(',',$categories); 
		
		
		return array(
					 "data" => $data,
					 "categories" => implode(',',array_keys($categories)),
					 "category_title" => $category_title,
					 );

	}
	

	public function get_counter_browser_share($io=NULL, $mode="hour", $sDate="", $eDate="", $counter=NULL){
		$browsers = array();
		switch($mode){
			case 'hour':
			case '':
			$date_range = date('Y-m-d h:i:s',strtotime("-1 day"));
			$timestamp_sql = " c.timestamp > '{$date_range}' ";
			break;
			case 'month':
			$date_range = date('Y-m-d h:i:s',strtotime("-30 day"));
			$timestamp_sql = " c.timestamp > '{$date_range}' ";
			break;
			case 'daterange':
			$timestamp_sql = " c.timestamp BETWEEN '{$sDate} 00:00:00' AND  '{$eDate} 23:59:59' ";
		}		


		$r = $this->CI->db->query("select count(*) as cnt, c.web_browser from campclick_clicks c, campclick_links l where  $timestamp_sql AND c.io='{$io}' AND c.web_browser LIKE '%Internet Explorer%' AND c.link_id=l.link_id AND l.counter={$counter} order by cnt desc");
		$ie_results = $r->result_array();
		$browsers['IE'] = $ie_results[0]['cnt'];

		
		$r = $this->CI->db->query("select count(*) as cnt, c.web_browser from campclick_clicks c, campclick_links l where  $timestamp_sql AND c.io='{$io}' AND c.web_browser LIKE '%Chrome%' AND c.link_id=l.link_id AND l.counter={$counter} order by cnt desc");
		$chrome_results = $r->result_array();
		$browsers['Chrome'] = $chrome_results[0]['cnt'];
		
		$r = $this->CI->db->query("select count(*) as cnt, c.web_browser from campclick_clicks c, campclick_links l  where  $timestamp_sql AND c.io='{$io}' AND c.web_browser LIKE '%Firefox%' AND c.link_id=l.link_id AND l.counter={$counter} order by cnt desc");
		$firefox_results = $r->result_array();
		$browsers['Firefox'] = $firefox_results[0]['cnt'];
		
		
		$r = $this->CI->db->query("select count(*) as cnt, c.web_browser from campclick_clicks c, campclick_links l where  $timestamp_sql AND c.io='{$io}' AND c.web_browser LIKE '%Safari%' AND c.link_id=l.link_id AND l.counter={$counter} order by cnt desc");
		$sarafi_results = $r->result_array();
		$browsers['Safari'] = $sarafi_results[0]['cnt'];
		
		
		$r = $this->CI->db->query("select count(*) as cnt, c.web_browser from campclick_clicks c, campclick_links l where  $timestamp_sql AND c.io='{$io}' AND c.web_browser LIKE '%Opera%' AND c.link_id=l.link_id AND l.counter={$counter} order by cnt desc");
		$opera_results = $r->result_array();
		$browsers['Opera'] = $opera_results[0]['cnt'];
		
		
		$r = $this->CI->db->query("select count(*) as cnt, c.web_browser from campclick_clicks c, campclick_links l where  $timestamp_sql AND c.io='{$io}' AND c.link_id=l.link_id AND l.counter={$counter}");
		$all_results = $r->result_array();
		$browsers['Others'] = $all_results[0]['cnt']- $ie_results[0]['cnt'] - $chrome_results[0]['cnt'] - $firefox_results[0]['cnt'] - $sarafi_results[0]['cnt'] ;
		
		return $browsers;
		
 		//return implode(',',$clicks_of_hours);

	}
	
	public function get_counter_platform($io=NULL, $mode ="hour", $sDate="", $eDate="", $counter=NULL){
		$platform = array();
		switch($mode){
			case 'hour':
			case '':
			$date_range = date('Y-m-d h:i:s',strtotime("-1 day"));
			$timestamp_sql = " c.timestamp > '{$date_range}' ";
			break;
			case 'month':
			$date_range = date('Y-m-d h:i:s',strtotime("-30 day"));
			$timestamp_sql = " c.timestamp > '{$date_range}' ";
			break;
			case 'daterange':
			$timestamp_sql = " c.timestamp BETWEEN '{$sDate} 00:00:00' AND  '{$eDate} 23:59:59' ";
			break;
		}
		
	
		$r = $this->CI->db->query("select count(*) as cnt, c.web_browser from campclick_clicks c, campclick_links l where c.io='{$io}' AND c.link_id=l.link_id AND l.counter={$counter}");
		$all_results = $r->result_array();
		
		$r = $this->CI->db->query("SELECT count( * ) AS cnt, c.platform FROM campclick_clicks c, campclick_links l WHERE $timestamp_sql AND c.io='{$io}' AND c.platform LIKE '%Windows%' AND c.link_id=l.link_id AND l.counter={$counter} ORDER BY cnt DESC");
		$windows = $r->result_array();
		$platform['Windows'] =  $windows[0]['cnt'] ;
		
		$r = $this->CI->db->query("SELECT count( * ) AS cnt, c.platform FROM campclick_clicks c, campclick_links l WHERE  $timestamp_sql AND c.io='{$io}' AND c.platform LIKE '%Mac%' AND c.link_id=l.link_id AND l.counter={$counter} ORDER BY cnt DESC");
		$mac = $r->result_array();
		$platform['Mac'] =  $mac[0]['cnt'] ;


		$r = $this->CI->db->query("SELECT count( * ) AS cnt, c.platform FROM campclick_clicks c, campclick_links l WHERE $timestamp_sql AND c.io='{$io}' AND c.platform LIKE '%Solaris%' AND c.link_id=l.link_id AND l.counter={$counter} ORDER BY cnt DESC");
		$solaris = $r->result_array(); 
		$platform['Solaris'] =  $solaris[0]['cnt'] ;
		

		$r = $this->CI->db->query("SELECT count( * ) AS cnt, c.platform FROM campclick_clicks c, campclick_links l WHERE $timestamp_sql AND c.io='{$io}' AND c.platform LIKE '%FreeBSD%' AND c.link_id=l.link_id AND l.counter={$counter} ORDER BY cnt DESC");
		$freebsd = $r->result_array();
		$platform['FreeBSD'] =  $freebsd[0]['cnt'] ;
		
		$r = $this->CI->db->query("SELECT count( * ) AS cnt, c.platform FROM campclick_clicks c, campclick_links l WHERE  $timestamp_sql AND c.io='{$io}' AND c.platform LIKE '%Linux%' AND c.link_id=l.link_id AND l.counter={$counter} ORDER BY cnt DESC");
		$linux = $r->result_array();
		$platform['Linux'] = $linux[0]['cnt'] ;
		
		


		return $platform;
 		//return implode(',',$clicks_of_hours);

	}
	
	function get_all_data($io=NULL, $mode ="hour", $sDate="", $eDate="", $counter=NULL, $offset=0,$perpage=20,$total = false){
		switch($mode){
			case 'hour':
			case '':
			$date_range = date('Y-m-d h:i:s',strtotime("-1 day"));
			$timestamp_sql = " c.timestamp > '{$date_range}' ";
			$from = "FROM campclick_clicks c";
			break;
			case 'month':
			$date_range = date('Y-m-d h:i:s',strtotime("-30 day"));
			$timestamp_sql = " c.timestamp > '{$date_range}' ";
			$from = " FROM campclick_clicks c ";
			break;
			case 'daterange':
			$timestamp_sql = " c.timestamp BETWEEN '{$sDate} 00:00:00' AND  '{$eDate} 23:59:59' ";
			$from = " FROM campclick_clicks c ";
			break;
		}
			$counter_sql = ($counter!="")?" AND c.link_id=l.link_id AND l.counter={$counter}":'';
			$from .= ($counter!="")?', campclick_links l ':'';

		if(!$total){
            $sql = "SELECT c.ip_address,c.timestamp,c.web_browser, c.referrer, c.platform, c.is_fraud  $from WHERE  $timestamp_sql AND c.io='{$io}' $counter_sql LIMIT $offset, $perpage";
            //die($sql);
			$r = $this->CI->db->query($sql);
			$data = array();
			foreach($r->result_array() as $d)	{
				$url = parse_url($d['referrer']);
				if ($d['referrer'] != "")	{
					$d['referrer'] = $url['scheme'] . "://" . "ppc-host.com" . $url['path'] . "?" . $url['query'];
				}
				$data[] = $d;
			}
		}else{
            $sql = "SELECT count(*) as cnt $from WHERE  $timestamp_sql AND c.io='{$io}' $counter_sql";
			$r = $this->CI->db->query($sql);
			$data = $r->result_array();
		}
		
		if(!$total)
		return $data;
		else
		return $data[0]['cnt'];
		
	}

    function get_all_data_host($io=NULL, $mode ="hour", $sDate="", $eDate="", $counter=NULL, $offset=0,$perpage=20,$total = false){
        switch($mode){
            case 'hour':
            case '':
                $date_range = date('Y-m-d h:i:s',strtotime("-1 day"));
                $timestamp_sql = " c.timestamp > '{$date_range}' ";
                $from = "FROM campclick_clicks c";
                break;
            case 'month':
                $date_range = date('Y-m-d h:i:s',strtotime("-30 day"));
                $timestamp_sql = " c.timestamp > '{$date_range}' ";
                $from = " FROM campclick_clicks c ";
                break;
            case 'daterange':
                $timestamp_sql = " c.timestamp BETWEEN '{$sDate} 00:00:00' AND  '{$eDate} 23:59:59' ";
                $from = " FROM campclick_clicks c ";
                break;
        }
        $counter_sql = ($counter!="")?" AND c.link_id=l.link_id AND l.counter={$counter}":'';
        $from .= ($counter!="")?', campclick_links l ':'';

        if(!$total){
            $sql = "SELECT c.ip_address,c.timestamp,c.web_browser, c.referrer_host AS referrer, c.platform, c.is_fraud  $from WHERE  $timestamp_sql AND `referrer_host` <> '' AND c.io='{$io}' $counter_sql GROUP BY referrer_host LIMIT $offset, $perpage";
            //die($sql);
            $r = $this->CI->db->query($sql);
            $data = $r->result_array();
            foreach($r->result_array() as $d)	{
                $url = parse_url($d['referrer']);
                if ($d['referrer'] != "")	{
                    $d['referrer'] = $url['scheme'] . "://" . "ppc-host.com" . $url['path'] . "?" . $url['query'];
                }
                $data[] = $d;
            }
//			print_r($data);
        }else{
            $sql = "SELECT count(*) as cnt $from WHERE  $timestamp_sql AND `referrer_host` <> '' AND c.io='{$io}' $counter_sql GROUP BY referrer_host";
            $r = $this->CI->db->query($sql);
            $data = $r->result_array();
        }

        if(!$total)
            return $data;
        else
            return $data[0]['cnt'];

    }
	
    
/*	function get_mobile_devices($io){
		$devices = array();
		$r = $this->CI->db->query("select DISTINCT mobile_device from campclick_clicks where io={$io} AND is_mobile='Y'");
		$all_deveices = $r->result_array();		
		if(count($all_deveices)>0){
			foreach($all_deveices as $d){
					$r = $this->CI->db->query("select count(*) as cnt from campclick_clicks where io={$io} AND is_mobile='Y' AND mobile_device LIKE '%$d%'");
					$data = $r->result_array();	
			}
			
		}
	}*/
	public function __get($name)	{
		return $this->$name;
	}

	public function __set($name, $value)	{
		$this->$name = $value;
	}

	public function __isset($name)	{
		return isset($this->$name);
	}
	
	function IntervalDays($CheckIn,$CheckOut){
		$CheckInX = explode("-", $CheckIn);
		$CheckOutX =  explode("-", $CheckOut);
		$date1 =  mktime(0, 0, 0, $CheckInX[1],$CheckInX[2],$CheckInX[0]);
		$date2 =  mktime(0, 0, 0, $CheckOutX[1],$CheckOutX[2],$CheckOutX[0]);
		$interval =($date2 - $date1)/(3600*24);
		return  $interval ;
	}
	
	public function archive()	{
		if ($this->io == "")	{
			return false;
		}
		
		$this->CI->db->update("campclick_campaigns", array("is_active"=>"A"), array("io"=>$this->io));
		return true;
	}
	
	public function campaign_summary($userid = null, $stime = "", $etime = "")	{
		$extra_sql = ($userid!='')?' AND userid='.$userid:'';
		
		if ($stime != "" && $etime != "")	{
			$time_range = " AND (cl.timestamp BETWEEN '{$stime}' AND '{$etime}')";
			//$time_range = " AND (cc.create_date BETWEEN '{$stime}' AND '{$etime}')";
		} else {
			$stime = date("Y-m-d 00:00:00", strtotime("-7 days"));
			$etime = date("Y-m-d 23:59:59");
			$time_range = " AND (cl.timestamp BETWEEN '{$stime}' AND '{$etime}')";
			//$time_range = " AND (cc.create_date BETWEEN '{$stime}' AND '{$etime}')";
		}
		$sql = "SELECT cc.io, cc.name, cc.create_date, COUNT(*) AS total_clicks, COUNT(DISTINCT ip_address) AS unique_clicks, SUM(IF(cl.is_fraud='Y',1,0)) AS fraud_clicks FROM campclick_campaigns cc JOIN campclick_clicks cl ON cl.io=cc.io WHERE cc.is_active='Y' AND cc.io <> '10035' {$extra_sql} {$time_range} GROUP BY cc.io ORDER BY cc.create_date DESC";
		$r = $this->CI->db->query($sql);
		if ($r->num_rows() > 0)	{
            $results = $r->result_array();
            foreach($results as $key=>$value) {
                $uniqueHosts = 0;
                $results[$key]['unique_hosts'] = $uniqueHosts;
            }
			return $results;
		} else {
			return false;
		}
	}

    public function get_hosts($io = "")    {
        //$sql = "SELECT referrer FROM campclick_clicks WHERE referrer_host IS NULL OR referrer_host = '' LIMIT 1000";
		$sql = "SELECT referrer FROM campclick_clicks WHERE referrer <> '' AND (referrer_host IS NULL OR referrer_host = '') AND io='{$io}' LIMIT 1000";
        $r = $this->CI->db->query($sql);
        if ($r->num_rows() > 0)	{
            $cache = array();
            $results = $r->result_array();
            foreach($results as $referrers) {
                if(isset($cache[$referrers['referrer']])) {
                    $p['host'] = $cache[$referrers['referrer']];
                } else {
                    $p = parse_url($referrers['referrer']);
                    if(isset($p['host']) AND !empty($p['host'])) {
                        $cache[$referrers['referrer']] = $p['host'];
                    } else {
                        continue;
                    }
                }

                $this->CI->db->simple_query("UPDATE campclick_clicks SET referrer_host = '{$p['host']}' WHERE referrer = '{$referrers['referrer']}'");
            }
        }
        return true;
    }
	
	public function send_support_request()	{
		$this->CI->load->library('email');
		//$this->CI->email->from($this->email);
		$this->CI->email->from("jkorkin@safedatatech.onmicrosoft.com");
		$this->CI->email->to("jkorkin@safedatatech.onmicrosoft.com");
		$this->CI->email->subject("[CAMPCLICK] {$this->io} Inquiry");
		$this->CI->email->message($this->note);
		$this->CI->email->send();
	}
	
	public function get_ip_geolocate()	{
		$r = $this->CI->db->query("SELECT geo_lat AS lat, geo_lon AS `long`, ip_address AS id FROM campclick_clicks WHERE io='{$this->io}' AND is_geo='Y' ORDER BY timestamp DESC LIMIT 25000");
		if ($r->num_rows > 0)	{
			return $r->result_array();
		} else {
			return array();
		}
	}
	
	public function get_quick_stats_by_io()	{
		$r = $this->CI->db->query("SELECT COUNT(*) AS total_clicks, COUNT(DISTINCT ip_address) AS unique_clicks FROM campclick_campaigns cc JOIN campclick_clicks cl ON cl.io=cc.io WHERE cc.io='{$this->io}'");
		return $r->row_array();
	}
	
	public function schedule_summary($userid = null, $stime = "", $etime = "", $is_active = "N")	{
		$extra_sql = ($userid!='')?' AND userid='.$userid:'';
		
		if ($stime != "" && $etime != "")	{
			$time_range = " AND (campaign_start_datetime BETWEEN '{$stime}' AND '{$etime}')";
		} else {
			$stime = date("Y-m-d 00:00:00", strtotime("-14 days"));
			$etime = date("Y-m-d 23:59:59", strtotime("+14 days"));
			$time_range = " AND (campaign_start_datetime BETWEEN '{$stime}' AND '{$etime}')";
		}
		
		if ($is_active == "")	{
			$campaign_is_started = " AND campaign_is_started = 'N'";
		} else {
			$campaign_is_started = " AND campaign_is_started = '{$is_active}'";
		}
		
		$sql = "SELECT io, name, max_clicks, campaign_start_datetime, campaign_is_started FROM campclick_campaigns WHERE 1 {$campaign_is_started} {$extra_sql} {$time_range}";
		
		//print $sql;
		
		$r = $this->CI->db->query($sql);
		if ($r->num_rows() > 0)	{
			$result = array();
			$now = time();
			foreach($r->result_array() as $rr)	{
				$result[] = array(
						"io" => $rr['io'],
						"name" => $rr['name'],
						"max_clicks" => $rr['max_clicks'],
						"campaign_start_datetime" => $rr['campaign_start_datetime'],
						"campaign_is_started" => $rr['campaign_is_started']
				);
		
			}
			return $result;
		} else {
			return false;
		}
	}
	
	public function fulfillment_summary($userid = null, $stime = "", $etime = "", $get_bids = false)	{
		$extra_sql = ($userid!='')?' AND cc.userid='.$userid:'';
		
		if ($stime != "" && $etime != "")	{
			$time_range = " AND (cc.create_date BETWEEN '{$stime}' AND '{$etime}')";
		} else {
			$stime = date("Y-m-d 00:00:00", strtotime("-14 days"));
			$etime = date("Y-m-d H:m:s");
			$time_range = " AND (cc.campaign_start_datetime BETWEEN '{$stime}' AND '{$etime}')";
		}
		
		if ($get_bids)  {
		    //$sql = "SELECT cc.io, cc.name, cc.create_date, cc.max_clicks AS campaign_max_clicks, COUNT(*) AS total_clicks, (SELECT SUM(max_clicks) FROM campclick_links WHERE max_clicks <> 9999999 AND io=cc.io GROUP BY io) AS max_clicks, ads.daily_cap, ads.bid, cc.campaign_is_started, cc.fire_open_pixel, cc.impression_clicks, cc.max_clicks FROM campclick_campaigns cc JOIN campclick_clicks cl ON cl.io=cc.io LEFT JOIN ads ON ads.id=cc.local_ad_id WHERE cc.is_active='Y' AND cc.campaign_is_complete='N' AND cc.io <> '10035' {$extra_sql} {$time_range} GROUP BY cc.io ORDER BY cc.create_date DESC";
		    $sql = "SELECT cc.ppc_network_ad_id, cc.io, cc.name, cc.campaign_start_datetime, cc.max_clicks AS campaign_max_clicks, COUNT(*) AS total_clicks, ads.daily_cap, ads.bid, cc.campaign_is_started, cc.fire_open_pixel, cc.impression_clicks, cc.max_clicks, ads.ppc_network_ad_active, tpc.geotype, cc.cap_per_hour FROM campclick_campaigns cc JOIN take5_pending_campaigns tpc ON tpc.io=cc.io LEFT JOIN campclick_clicks cl ON cl.io=cc.io LEFT JOIN ads ON ads.id=cc.local_ad_id WHERE cc.is_active='Y' AND cc.campaign_is_complete='N' {$extra_sql} {$time_range} GROUP BY cc.io ORDER BY cc.create_date DESC";
		} else {
		    $sql = "SELECT cc.ppc_network_ad_id, cc.io, cc.name, cc.campaign_start_datetime, cc.max_clicks AS campaign_max_clicks, COUNT(*) AS total_clicks, cc.fire_open_pixel, cc.impression_clicks, cc.max_clicks FROM campclick_campaigns cc JOIN campclick_clicks cl ON cl.io=cc.io WHERE cc.is_active='Y' AND cc.campaign_is_started='Y' AND cc.campaign_is_complete='N' AND cc.io <> '10035' {$extra_sql} {$time_range} GROUP BY cc.io ORDER BY cc.create_date DESC";
		}
		
		//print $sql;
		
		$r = $this->CI->db->query($sql);
		if ($r->num_rows() > 0)	{
			$result = array();
			$now = time();
			foreach($r->result_array() as $rr)	{
			    if ($rr['fire_open_pixel'] == "Y")   {
			        $sql = "SELECT COUNT(*) AS cnt FROM campclick_impressions WHERE io='{$rr['io']}'";
			        $cRes = $this->CI->db->query($sql);
			        $cRes = $cRes->row_array();
			        //$rr['max_clicks'] = $rr['max_clicks'];
			        $rr['total_clicks'] = (int)$cRes['cnt'];
			        $rr['impression_clicks'] = (int)$cRes['cnt'];
			    } else {
			        $rr['max_clicks'] = ($rr['max_clicks'] > $rr['campaign_max_clicks']) ? $rr['max_clicks'] : $rr['campaign_max_clicks'];
			    }
			    

				if ($rr['total_clicks'] < ($rr['max_clicks'] * 0.20))	{
					$slow_performing = true;
					
					$create_date = strtotime($rr['campaign_start_datetime']);
					
					if (($now - $create_date) <= 86400)	{
						$slow_performing = "progress-danger";
					} elseif (($now - $create_date) >= 43200 && ($now - $create_date) <= 86400)	{
						$slow_performing = "progress-warning";
					} else {
						$slow_performing = "progress-info";
					}
									
				} else {
					$slow_performing = "progress-success";
				}
				$result[] = array(
					"io" => $rr['io'],
					"name" => $rr['name'],
					"create_date" => $rr['campaign_start_datetime'],
					"total_clicks" => $rr['total_clicks'],
					"max_clicks" => $rr['max_clicks'],
					"slow_performing" => $slow_performing,
					"time_delta" => ($now - $create_date),
				    "bid" => $rr['bid'],
				    "daily_cap" => $rr['daily_cap'],
				    "campaign_is_started" => $rr['campaign_is_started'],
				    "fire_open_pixel" => $rr['fire_open_pixel'],
				    "impression_clicks" => $rr['impression_clicks'],
				    "ppc_network_ad_active" => $rr['ppc_network_ad_active'],
				    "geotype" => $rr['geotype'],
				    "cap_per_hour" => $rr['cap_per_hour'],
				    "ppc_network_ad_id" => $rr['ppc_network_ad_id'],
				);
				
			}
			return $result;
		} else {
			return false;
		}
	}
	
	public function log_volume($traffic_type = "mobile")	{
	    $referrer = $this->CI->agent->referrer();
	    $p = parse_url($referrer);
	    $host = (isset($p['host'])) ? $p['host'] : '';
	
	    $insert = array(
	        "ip_address" => $this->CI->input->ip_address(),
	        "user_agent" => $this->CI->input->user_agent(),
	        "timestamp" => date("Y-m-d H:i:s"),
	        "referrer" => $referrer,
	        "referrer_host" => $host
	    );
	
	    $this->CI->db->insert("volume_{$traffic_type}", $insert);
	}
	
	public function io_tracking_report($io = "", $wildcard = "0")   {
	    if ($io == "")
	        return false;

	    $days_ago_90 = date("Y-m-d 00:00:00", strtotime("90 days ago"));
	    
	    // check to see if we have a valid IO
	    $this->io = $io;
	    $ioCheck = $this->get_campaign_id_from_io(true);
	    if (! $ioCheck > 0)  {
	        return false;
	    }

	    if ($wildcard != "0")   {
	        $r = $this->CI->db->query("SELECT cl.io, cl.dest_url, cl.counter, COUNT(*) AS click_count, c.campaign_start_datetime, c.name AS campaign_name FROM campclick_links cl JOIN campclick_campaigns c ON c.io=cl.io JOIN campclick_clicks cc ON cc.link_id=cl.link_id WHERE (c.campaign_start_datetime BETWEEN '{$days_ago_90}' AND NOW()) AND cl.io LIKE '{$io}%' GROUP BY cl.link_id");
	    } else {
	        $r = $this->CI->db->query("SELECT cl.io, cl.dest_url, cl.counter, COUNT(*) AS click_count, c.campaign_start_datetime, c.name AS campaign_name FROM campclick_links cl JOIN campclick_campaigns c ON c.io=cl.io JOIN campclick_clicks cc ON cc.link_id=cl.link_id WHERE (c.campaign_start_datetime BETWEEN '{$days_ago_90}' AND NOW()) AND cl.io = '{$io}' GROUP BY cl.link_id");
	    }

	    if ($r->num_rows() > 0)    {
	        return $r->result_array();
	    } else {
	        return array();
	    }
	}
	
	public function io_tracking_unique($io = "")   {
	    $r = $this->CI->db->query("SELECT COUNT(DISTINCT ip_address) AS unique_clickers FROM campclick_clicks WHERE io='{$io}'");

	    if ($r->num_rows() > 0)    {
	        $row = $r->row_array();
	        return $row['unique_clickers'];
	    } else {
	        return 0;
	    }
	}

	public function io_tracking_mobile($io = "", $min = 0.7, $max = 1.5)   {
	    $r = $this->CI->db->query("SELECT COUNT(*) AS mobile_clickers FROM campclick_clicks WHERE io='{$io}' AND is_mobile='Y'");

	    if ($r->num_rows() > 0)    {
	        $row = $r->row_array();
	        $randomMultiplier = mt_rand ($min*100, $max*100) / 100;

	        return (int)($row['mobile_clickers'] * $randomMultiplier);
	    } else {
	        return (int)(3 * $randomMultiplier);
	    }
	}
	
	public function clone_io($old_io = "", $new_io = "", $campaign_name = "") {
	    if ($old_io == "")
	        throw new exception("clone_io: old_io required");
	    
	    if ($new_io == "")
	        throw new exception("clone_io: new_io required");
	    
	    if ($campaign_name == "")
	        throw new exception("clone_io: campaign_name required");
	    
	    $campaign_result = $this->CI->db->query("SELECT * FROM campclick_campaigns WHERE io='{$old_io}' LIMIT 1");
	    if ($campaign_result->num_rows() > 0)  {
	        $campaign = $campaign_result->row_array();
	        
	        // modify the campaign structure for reinsertion
	        unset($campaign['id']);
	        unset($campaign['create_date']);
	        $campaign['io'] = $new_io;
	        $campaign['name'] = $campaign_name;
	        $campaign['is_active'] = "Y";
	        $campaign['campaign_is_started'] = "N";
	        $campaign['campaign_is_complete'] = "N";
	        
	        $id = $this->CI->db->insert("campclick_campaigns", $campaign);
	        
	        // get links from old campaign and reinsert as new campaign
	        $links_result = $this->CI->db->query("SELECT * FROM campclick_links WHERE io='{$old_io}'");
	        if ($links_result->num_rows() > 0) {
	            foreach($links_result->result_array() as $l)   {
	                
	                // modify link structure for reinsertion
	                unset($l['link_id']);
	                $l['io'] = $new_io;
	                $l['is_fulfilled'] = "N";
	                
	                $link_id = $this->CI->db->insert("campclick_links", $l);
	            }
	            
	            return true;
	        } else {
	            // remove the half-created cloned campaign
	            $this->CI->db->query("DELETE FROM campclick_campaigns WHERE id='{$id}'");
	            return false;
	        }
	        
	        if ($id > 0)   {
	            return true;
	        } else {
	            return false;
	        }
	        
	    } else {
	        return false;
	    }
	}
	
	public function rolling_count($io = "", $duration = 10) {
	    if ($io == "")
	        throw new exception("rolling_count: io required");
	    
	    if ((int)$duration < 10)
	        throw new exception("rolling_count: duration must be > 10");
	    
	    $sdate = date("Y-m-d H:i:s", strtotime("-{$duration} minutes"));
	    $rc = $this->CI->db->query("SELECT COUNT(*) AS cnt FROM campclick_clicks WHERE io='{$io}' AND timestamp BETWEEN '{$sdate}' AND NOW()");
	    $ri = $this->CI->db->query("SELECT COUNT(*) AS cnt FROM campclick_impressions WHERE io='{$io}' AND timestamp BETWEEN '{$sdate}' AND NOW()");

	    $i = 0;
	    if ($ri->num_rows() > 0)   {
	        $t = $ri->row_array();
	        $i = $t['cnt'];
	    }
	    
	    $c = 0;
	    if ($rc->num_rows() > 0)    {
	        $t = $rc->row_array();
	        $c = $t['cnt'];
	    } else {
	        $c = 0;
	    }
	    
	    return $i + $c;
	}

	public function all_links_fulfilled()  {
	    if ($this->io == "")
	        throw new exception("all_links_fulfilled: io required");
	    
	    $r = $this->CI->db->query("SELECT * FROM campclick_links WHERE io='{$this->io}'");

	    foreach($r->result_array() as $rr) {
	        //print_r($rr);
	        if ($rr['is_fulfilled'] == "N") {
	            return false;
	        }
	    }
	    
	    return true;
	}
	
	public function get_vertical_by_io()   {
	    if ($this->io == "")
	        throw new exception("io required");
	     
	    $r = $this->CI->db->query("SELECT vertical FROM take5_pending_campaigns WHERE io='{$this->io}' LIMIT 1");
	    if ($r->num_rows() > 0)    {
	        $v = $r->row_array();
	        return $v['vertical'];
	    } else {
	        return "GENERAL";
	    }
	}
	
}

?>
