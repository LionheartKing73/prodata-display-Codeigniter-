<?php 

class Take5_Campaign_Pending_Model extends CI_Model	{

	protected $CI;
	
	// campaign
	private $id;
	private $total_records;
	private $percentage_opens;
	private $precentage_clicks;
	private $precentage_bounce;
	private $total_clicks;
	private $total_opens;
	private $total_bounces;
	private $message_result;
	private $io;
	private $create_name;
	private $vendor;
	private $domain;
	private $campaign_start_datetime;
	private $geotype;
	private $country;
	private $state = array();
	private $radius;
	private $zip;
	private $links = array();
	private $special_instructions;
	private $fire_open_pixel = "N";
	private $open_pixel = array();
	private $budget = 0.00;
	private $vertical;
	private $campaign_is_converted_to_live;
	private $ad_id;
	private $userid;
	private $record_created;
	private $email_seeds = "";
	private $is_geo_expanded = "N";
	
	// Take5 Pricing for CPC
	private $cpc_national = 0.021; // National USA PPC cost
	private $cpc_geo = 0.031; // GEO specific PPC cost
	private $MINIMUM_ORDER_AMOUNT = 150.00; // this is the minimum order amount we will accept from Take5.

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		
		$this->CI->load->model("Take5_Pending_Campaign_Links_Model");
		$this->CI->load->model("Take5_Pending_Campaign_Openpixel_Model");
		$this->CI->load->model("Campclick_model");
		$this->CI->load->model("Ad_model");
		$this->CI->load->model("Zip_model");
		$this->CI->load->model("Take5_Clicktrack365_Model");
		$this->CI->load->model("Email_Seeds_Model");
		
		$config['protocol'] = 'sendmail';
		$config['mailpath'] = '/usr/sbin/sendmail';
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = TRUE;
		$config['mailtype'] = 'html';
		$config['priority'] = 1;
		 
		$this->CI->load->library('email');
		$this->CI->email->initialize($config);
	}
	
	public function process_raw_post($data = array())    {
	    foreach($data as $k=>$v)   {
	        $this->{$k} = $data[$k];
	    }
	    
	    // filter out the non-alpha numeric stuff (spaces too)
	    $this->io = preg_replace("/[^A-Za-z0-9 ]/", '', $this->io);
	    
	    // check to make sure that the IO doesnt already exist
        $this->CI->Campclick_model->io = $this->io;
        $io = $this->CI->Campclick_model->get_campaign_id_from_io();
        $io2 = $this->get_campaign_id_from_io();
        $io = $io + $io2;
        if ($io > 0)    {
            return false;
        }
	    
        if ($this->state != "") {
    	    $states = "";
    	    // normalize the geo data
    	    foreach($this->state as $s)   {
    	        $states .= $s . ",";
    	    }
        }
        
        if ($this->zip != "") {
            $this->zip = preg_replace("/,/", " ", $this->zip); // remove the commas
            $this->zip = preg_replace("/\s+/", " ", $this->zip); // remove the multiple spaces
    	    $zip = implode(",", explode(" ", $this->zip)); // implode to something usable
        }
        
	    /**
	     * Budget Calculation
	     * 
	     */
	    if (strtoupper($this->fire_open_pixel) == "Y") {
	        $max_clicks = $this->total_opens;
	    } else {
	        $max_clicks = $this->total_clicks;
	    }
	    
	    if ($this->budget > 0) {
	        $budget = $this->budget;
	    } else {
	        if ($this->geotype == "country")   {
	            $budget = ($this->cpc_national * $max_clicks);
	        } else {
	            $budget = ($this->cpc_geo * $max_clicks);
	        }
	        // round up to nearest "5" for accounting purposes.
	        $budget = round(($budget+5/2)/5)*5;
	        $budget = ($budget > $this->MINIMUM_ORDER_AMOUNT) ? $budget : $this->MINIMUM_ORDER_AMOUNT;
	    }

	    $insert = array(
	        "total_records" => $this->total_records,
	        "percentage_opens" => $this->percentage_opens,
	        "percentage_clicks" => $this->percentage_clicks,
	        "percentage_bounce" => $this->percentage_bounce,
	        "total_clicks" => $this->total_clicks,
	        "total_opens" => $this->total_opens,
	        "total_bounces"=> $this->total_bounces,
	        "message_result" => $this->message_result,
	        "io" => $this->io,
	        "create_name" => $this->create_name,
	        "vendor" => $this->vendor,
	        "domain" => $this->domain,
	        "campaign_start_datetime" => date("Y-m-d H:i:00", strtotime($this->campaign_start_datetime)),
	        "geotype" => $this->geotype,
	        "special_instructions" => $this->special_instructions,
	        "fire_open_pixel" => $this->fire_open_pixel,
	        "budget" => $budget,
	        "campaign_is_approved" => "N",
	        "cap_per_hour" => ceil($max_clicks * 0.15),
	        "vertical" => $this->vertical,
	        "userid" => $this->userid,
	        "record_created" => date("Y-m-d H:i:s"),
	        
	        // geo specific info
	        "country" => $this->country,
	        "radius" => $this->radius,
	        "state" => $states,
	        "zip" => $zip,
	    );
	    
	    $this->CI->db->insert("take5_pending_campaigns", $insert);
	    $id = $this->CI->db->insert_id();
	    
	    if ($id > 0)  {
	        $this->id = $id;
	        
	        // create links
	        $this->CI->Take5_Pending_Campaign_Links_Model->pending_campaign_id = $this->id;
	        foreach($this->links as $l)    {
	            /*
	            //
	            // Take the destination_url and then append tracking_io to query string
	            // We will use this tracking_io with Google Analytics.
	            //
	            $url = parse_url($l['link']);
	            if ($url['query'] != "")   {
	                $l['link'] . "&tracking_io=" . $this->io;
	            } else {
	                $l['link'] . "?tracking_io=" . $this->io;
	            }
	            */
	            
	            $this->CI->Take5_Pending_Campaign_Links_Model->destination_url = $l['link'];
	            $this->CI->Take5_Pending_Campaign_Links_Model->original_url = $l['link'];
	            $this->CI->Take5_Pending_Campaign_Links_Model->click_count = (int)$l['count'];
	            $this->CI->Take5_Pending_Campaign_Links_Model->create();
	        }
	        
	        // create pixels
	        $this->CI->Take5_Pending_Campaign_Openpixel_Model->pending_campaign_id = $this->id;
	        foreach($this->open_pixel as $p)   {
	            $this->CI->Take5_Pending_Campaign_Openpixel_Model->pixel_url = $p;
	            $this->CI->Take5_Pending_Campaign_Openpixel_Model->create();
	        }
	        
	        // create email seeds (if any exist)
	        if ($this->email_seeds != "")  {
	            $this->CI->Email_Seeds_Model->io = $this->io;
	            $seeds = preg_split("/\r\n|\n|\r/", $this->email_seeds);

	            if (! empty($seeds))   {
	                foreach($seeds as $s)  {
	                    $this->CI->Email_Seeds_Model->email = $s;
	                    $this->CI->Email_Seeds_Model->create();
	                }
	            }
	        }
	        
	        return $this->id;
	        
	    } else {
	        return false;
	    }
	}
	
	public function get_list($campaign_is_approved = "N", $campaign_is_convert_to_live = "N") {
	    if ($this->userid != "")   {
	        $r = $this->CI->db->query("SELECT id, io, create_name, budget, geotype, campaign_start_datetime, (IF(fire_open_pixel = 'Y', total_opens, total_clicks)) AS max_clicks FROM take5_pending_campaigns WHERE campaign_is_approved='{$campaign_is_approved}' AND campaign_is_converted_to_live='{$campaign_is_convert_to_live}' AND userid='{$this->userid}' ORDER BY campaign_start_datetime DESC");
	    } else {
	        $r = $this->CI->db->query("SELECT id, io, create_name, budget, geotype, campaign_start_datetime, (IF(fire_open_pixel = 'Y', total_opens, total_clicks)) AS max_clicks FROM take5_pending_campaigns WHERE campaign_is_approved='{$campaign_is_approved}' AND campaign_is_converted_to_live='{$campaign_is_convert_to_live}' ORDER BY campaign_start_datetime DESC");
	    }
	    
	    if ($r->num_rows() > 0)    {
	        return $r->result_array();
	    } else {
	        return array();
	    }
	}
	
	public function get_campaign_by_id() {
	    $r = $this->CI->db->query("SELECT * FROM take5_pending_campaigns WHERE id='{$this->id}'");
	    $c = $r->row_array();

	    $l = $this->CI->db->query("SELECT * FROM take5_pending_campaign_links WHERE pending_campaign_id='{$this->id}'");
	    $l = $l->result_array();
	    
	    $p = $this->CI->db->query("SELECT * FROM take5_pending_campaign_openpixel WHERE pending_campaign_id='{$this->id}'");
	    $p = $p->result_Array();
	    
	    $c['open_pixel'] = $p;
	    $c['links'] = $l;
	    
	    return $c;
	}
	
	
	public function remove_pending_campaign()  {
	    $this->CI->db->query("DELETE FROM take5_pending_campaigns WHERE id='{$this->id}'");
	    $this->CI->Take5_Pending_Campaign_Openpixel_Model->pending_campaign_id = $this->id;
	    $this->CI->Take5_Pending_Campaign_Openpixel_Model->remove_campaign();
	    
	    $this->CI->Take5_Pending_Campaign_Links_Model->pending_campaign_id = $this->id;
	    $this->CI->Take5_Pending_Campaign_Links_Model->remove_campaign();
	}
	
	public function convert_order()    {
	    $pendingCampaign = $this->CI->db->query("SELECT * FROM take5_pending_campaigns WHERE id='{$this->id}'");
	    if ($pendingCampaign->num_rows() > 0)  {
	        $c = $pendingCampaign->row_array();
	        
	        /**
	         * 1. DONE! Add order to campclick_campaigns (incl. open pixels - thats NEW)
	         * 2. DONE! Add links to campclick_links
	         * 3. Add campaign to CDQR API (LATER)
	         * 4. Add links to CDRQ API (LATER)
	         * 5. DONE! Flag pending order as converted to live order
	         */
	        
	        $this->CI->Campclick_model->io = $c['io'];
	        $this->CI->Campclick_model->name = $c['create_name'];
	        $this->CI->Campclick_model->message = $c['message_result'];
	        $this->CI->Campclick_model->conversion_tracking = "N";
	        $this->CI->Campclick_model->is_geo = ($c['geotype'] == "country") ? "N" : "Y";
	        $this->CI->Campclick_model->vendor_id = $c['vendor'];
	        $this->CI->Campclick_model->campaign_start_datetime = $c['campaign_start_datetime'];
	        $this->CI->Campclick_model->is_traffic_shape = "N";
	        $this->CI->Campclick_model->is_active = "Y";
	        $this->CI->Campclick_model->userid = $c['userid'];
	        $this->CI->Campclick_model->ppc_network = "FIQ";
	        $this->CI->Campclick_model->cap_per_hour = $c['cap_per_hour'];
	        $this->CI->Campclick_model->max_clicks = ($c['fire_open_pixel'] == "Y") ? $c['total_opens'] : $c['total_clicks'];
	        $this->CI->Campclick_model->opens = $c['total_opens'];
	        $this->CI->Campclick_model->fire_open_pixel = $c['fire_open_pixel'];
	        $this->CI->Campclick_model->impression_clicks = $c['total_clicks'];
	        
	        $id = $this->CI->Campclick_model->create();
	        
	        // campaign was created successfully, now copy the links
	        if ($id > 0) {

	            // If the user is "take5" user, then we need to pass the request over to Clicktrack365 and properly set the links as well.
	            $clicktrack365_process_links = false;
	            if ($c['userid'] == "5") {
	                $this->CI->Take5_Clicktrack365_Model->campaignName = $c['io'] . " - " . $c['create_name'];
	                $this->CI->Take5_Clicktrack365_Model->campaignDesc = $c['io'] . " - " . $c['create_name'] . " - Clicks: " . $this->CI->Campclick_model->max_clicks . " (ProDataFeed.com Auto)";
	                $this->CI->Take5_Clicktrack365_Model->date_start = $c['campaign_start_datetime'];
	                $this->CI->Take5_Clicktrack365_Model->date_end = date("Y-m-d H:i:s", strtotime("+14 days"));
	                $this->CI->Take5_Clicktrack365_Model->max_clicks = $this->CI->Campclick_model->max_clicks;
	                $return = $this->CI->Take5_Clicktrack365_Model->create_campaign();
	                
	                if ($return !== false)  {
	                    $clicktrack365_process_links = $return;
	                }
	            }
	            
	            $this->CI->Domains_model->id = (int)$c['domain'];
	            $domain = $this->CI->Domains_model->get_domain();
	            
	            $this->CI->Take5_Pending_Campaign_Links_Model->pending_campaign_id = $c['id'];
	            $links = $this->CI->Take5_Pending_Campaign_Links_Model->get_links_by_pending_id();
	            
	            // hard coded to TURN OFF the API temporarily until Fred Fixes!
	            $clicktrack365_process_links = false; // coded to save entered in campaigns from CDQR and bypass Fred's bug in the api
	            
	            $counter = 0;
	            foreach($links as $l)  {
	                if ($clicktrack365_process_links !== false)    {
	                    $this->CI->Take5_Clicktrack365_Model->campaign_id = $clicktrack365_process_links;
	                    $this->CI->Take5_Clicktrack365_Model->target_url = $l['destination_url'];
	                    $this->CI->Take5_Clicktrack365_Model->link_max_clicks = $l['click_count'];
	                    $url = $this->CI->Take5_Clicktrack365_Model->create_link();
	                    
	                    if ($url !== false)    {
	                        $link = "http://{$domain['name']}/c/{$c['io']}/{$counter}";
	                        $this->CI->Campclick_model->create_links($url, $c['io'], $counter, $l['click_count']);
	                        $counter++;
	                    }
	                } else {
	                    $l['click_count'] = ($l['click_count'] > 0) ? $l['click_count'] : 99999; // fix bug where users are entering in "0" expecting it to run forever
	                    $link = "http://{$domain['name']}/c/{$c['io']}/{$counter}";
	                    $this->CI->Campclick_model->create_links($l['destination_url'], $c['io'], $counter, $l['click_count']);
	                    $counter++;
	                }
	            }
	            
	            // set the pending order to show as an converted-to-live status
	            $this->CI->db->query("UPDATE take5_pending_campaigns SET campaign_is_approved='Y',campaign_is_converted_to_live='Y' WHERE id='{$c['id']}'");
	        }

	        $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
	        $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
	        
	        // send to notification email address
	        $r = $this->CI->db->query("SELECT notifcation_email FROM users WHERE id='{$c['userid']}' LIMIT 1");
	        if ($r->num_rows() > 0)    {
	            $email = $r->row_array();
	            $this->CI->email->cc($email['notifcation_email']);
	        }

	        $this->CI->email->subject("Report-Site: [{$c['io']}] New Campaign Approval");
	        $total_clicks = ($c['fire_open_pixel'] == "Y") ? $c['total_opens'] : $c['total_clicks'];
	        $this->CI->email->message("Campaign IO: {$c['io']}<br/>\nCampaign Name: {$c['create_name']}<br/>\nBudget: \${$c['budget']}<br/>Campaign Start: {$c['campaign_start_datetime']}<br/>Impressions? {$c['fire_open_pixel']}<br/>\nTotal Clicks: " . $total_clicks);
	        $this->CI->email->send();
	        
	    }
	}
	
	public function get_campaign_id_from_io($pending = true)  {
	    if ($pending === true) {
	        $r = $this->CI->db->query("SELECT id FROM take5_pending_campaigns WHERE io='{$this->io}'");
	    } else {
	        $r = $this->CI->db->query("SELECT id FROM campclick_campaigns WHERE io='{$this->io}'");
	    }
	    if ($r->num_rows() > 0)    {
	        $r = $r->row_array();
	        return $r['id'];
	    } else {
	        return 0;
	    }
	}
	
	public function make_campaign_live($io = "")   {
	    $date_before = date("Y-m-d H:i:00", strtotime("-7 day"));
	    
	    // automated queue runner vs. manual activation by io#.
	    if ($io == "") {
	        print "No IO Passed\n";
	        $sql = "SELECT tpc.io FROM take5_pending_campaigns tpc JOIN campclick_campaigns cc ON cc.io=tpc.io WHERE tpc.io <> '13772A' AND cc.ppc_network_ad_id IS NULL AND (tpc.vendor='2' OR tpc.vendor='5' OR tpc.vendor='7') AND tpc.campaign_is_approved='Y' AND tpc.campaign_is_converted_to_live='Y' AND tpc.campaign_start_datetime >= '{$date_before}' AND tpc.campaign_start_datetime <= NOW() GROUP BY tpc.io ORDER BY tpc.campaign_start_datetime";
            $camps_to_go_live = $this->CI->db->query($sql);
	    } else {
	        print "IO: {$io}\n";
            $camps_to_go_live = $this->CI->db->query("SELECT take5_pending_campaigns.io FROM take5_pending_campaigns JOIN campclick_campaigns cc ON cc.io=take5_pending_campaigns.io WHERE take5_pending_campaigns.io='{$io}' AND cc.ppc_network_ad_id IS NULL AND vendor='2' AND campaign_is_approved='Y' AND campaign_is_converted_to_live='Y' AND cc.campaign_start_datetime <= NOW()");
	    }
	    
	    print_r($camps_to_go_live->result_array());
	    
	    if ($camps_to_go_live->num_rows() > 0) {
                
	        foreach($camps_to_go_live->result_array() as $c)   {
                    
	            $this->CI->Campclick_model->io = $c['io'];
	            $campaign = $this->CI->Campclick_model->get_campaign_by_io();
	            if ($campaign['ppc_network_ad_id'] != "")  {
	                print "{$io} Already Activated Live\n";
	                continue;
	            }
	            
	            $this->io = $c['io'];
	            $campaignID = $this->get_campaign_id_from_io();
	            
	            // create the auto-generated advertisement
	            $this->CI->Take5_Pending_Campaign_Links_Model->pending_campaign_id = $campaignID;
	            $link = $this->CI->Take5_Pending_Campaign_Links_Model->get_primary_campaign_link();
	            
                    
                    
	            print_r($link);
	            print "AutoGen Campaign Ad";
	            $ad = $this->CI->Ad_model->auto_generate_ad_content($link['destination_url']);
	            print "Done.";
	            print_r($ad);
	            
	            // create the advertisement on the proper ad network
	            $this->id = $campaignID;
	            $campaign = $this->get_campaign_by_id();
	            
	            // HARD CODED NETWORK!! 
	            $network = "FIQ"; // HARD CODED (FOR NOW)

	            $clicks_per_day = ceil((($campaign['fire_open_pixel'] == "Y") ? $campaign['total_opens'] : $campaign['total_clicks']) / 2.85);
	            $initial_bid = (strtoupper($campaign['geotype']) == "COUNTRY") ? 0.0018 : 0.0028;
	            $spend_per_day = sprintf("%.2f", $clicks_per_day * $initial_bid); // default bid, including overage
	            $spend_per_day = ($spend_per_day > 4) ? $spend_per_day : 4.00; // minimum spend per day is $4.00

	            $this->CI->Ad_model->title = $ad['title'];
	            $this->CI->Ad_model->description = $ad['description'];
	            $this->CI->Ad_model->category = 104; // hard coded to RUN OF NETWORK
	            $this->CI->Ad_model->campaign_name = $campaign['io'] . " - " . $campaign['create_name'] . " (AUTO)";
	            
	            if ($campaign['fire_open_pixel'] == "Y")   {
	                $this->CI->Ad_model->destination_url = "http://report-site.com/i/" . $campaign['io'];
	            } else {
	                $this->CI->Ad_model->destination_url = "http://report-site.com/r/" . $campaign['io'];
	            }
	             
	            $this->CI->Ad_model->display_url = $ad['display_url'];
	            $this->CI->Ad_model->bid = $initial_bid;
	            $this->CI->Ad_model->daily_cap = $spend_per_day;
	            $id = $this->CI->Ad_model->create();
	            
	            switch(strtoupper($network))   {
	                case "EZANGA":
	                    if ($id > 0)   {
	                        $ezID = $this->CI->Ezanga_model->create_ad($campaign['io'], $id);

	                        // links the created "ad" to the campaign for future reference/usage.
	                        $this->CI->db->update("campclick_campaigns", array("local_ad_id" => $id), array("io" => $campaign['io']));
	                         
	                        if ($ezID === false) {
	                            $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
	                            $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
	                            $this->CI->email->subject('Report-Site: EZANGA Campaign Creation Error');
	                            $this->CI->email->message("** EZANGA CAMPAIGN CREATE ERROR **<br/><br/>Campaign IO: {$c['io']}<br/>\nCampaign Name: {$campaign['create_name']}<br/>Date: " . date("Y-m-d H:i:s"));
	                            $this->CI->email->send();
	                        
	                            $this->CI->Ad_model->id = $id;
	                            $this->CI->Ad_model->remove();
	                            return false;
	                        }
	                         
	                        print_r($ezID);
	                         
	                        // update the campaign with ad ID
	                        $activate_campaign = false;
	                        if (isset($ezID)) {
	                            // pause the ad while we "operate" on it - we dont need it to go live with incorrect info
	                            $this->CI->Ezanga_model->pause_ad($ezID);
	                             
	                            $this->CI->db->query("UPDATE campclick_campaigns SET ppc_network_ad_id='{$ezID}' WHERE io='{$campaign['io']}' LIMIT 1");
	                        
	                            // set target, budget, schedule and cap
	                            $total_clicks = ceil((($campaign['fire_open_pixel'] == "Y") ? $campaign['total_opens'] : $campaign['total_clicks']));
	                            $set_schedule_result = $this->CI->Ezanga_model->set_schedule($ezID); // randomize schedule

	                            $set_cap_result = $this->CI->Ezanga_model->set_cap($ezID, $spend_per_day);
// 	                            $set_bid_result = $this->CI->Finditquick_model->set_bid($ezID, $initial_bid);
	                             
	                            switch(strtoupper($campaign['geotype']))   {
	                                default:
	                                case "COUNTRY":
	                                    $set_target_result = $this->CI->Ezanga_model->set_target($ezID, $campaign['country'], "COUNTRY");
	                                    $activate_campaign = true;
	                                    break;
	                                     
	                                case "STATE":
	                                    $set_target_result = $this->CI->Ezanga_model->set_target($ezID, $campaign['state'], "STATE");
	                                    $activate_campaign = true;
	                                    break;
	                                     
	                                case "POSTALCODE":
	                                    $resultGeo = array();
	                                    $source_locations = array();
	                                    $ziplist = explode(",", $campaign['zip']);
	                                     
	                                    foreach($ziplist as $zip) {
	                                        if ($zip == "" || $zip == "undefined")
	                                            continue;
	                        
	                                        // hack for a STUPID excel copy-paste mistake that everyone makes
	                                        if (strlen($zip) == 4) {
	                                            $zip = "0" . $zip;
	                                        }
	                                         
	                                        // open the radius up a bit
	                                        if ($campaign['radius'] < 25)  {
	                                            $radius = $campaign['radius'] * 3;
	                                        } else {
	                                            $radius = $campaign['radius'] * 1.25; // open the radius
	                                        }
	                                        $zipLocations = $this->CI->Zip_model->find_locations($zip, $radius, true);
	                                        if (! empty($zipLocations))   {
	                                            foreach($zipLocations as $r)   {
	                                                $resultGeo[] = $r;
	                                            }
	                                        }
	                                    }
	                                     
	                                    if (! empty($resultGeo))   {
	                                        $resultGeo = array_unique($resultGeo); // remove duplicate entries from array

	                                        if (count($resultGeo) > 10000)  {
	                                            $newResultGeo = array();
	                                            $random_entries = array_rand($resultGeo, 10000);
	                                            foreach($random_entries as $e) {
	                                                $newResultGeo[] = $resultGeo[$e];
	                                            }
	                                            $resultGeo = $newResultGeo;
	                                        }
	                                         
	                                        print_r($resultGeo);
	                                        $set_target_result = $this->CI->Ezanga_model->set_target($ezID, $resultGeo);
	                                        $activate_campaign = true;
	                                    }
	                                    break;
	                            }
	                        
	                            // resume ad
	                            if ($activate_campaign === true)   {
	                                $this->CI->Ezanga_model->resume_ad($ezID);
	                        
	                                $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
	                                $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
	                                $this->CI->email->subject("Report-Site: EZANGA New Campaign Creation [{$c['io']}]");
	                                $this->CI->email->message("** EZANGA NEW CAMPAIGN CREATION **<br/><br/>Campaign IO: {$c['io']}<br/>\nCampaign Name: {$campaign['create_name']}<br/>Date: " . date("Y-m-d H:i:s"));
	                                $this->CI->email->send();
	                        
	                            } else {
	                                $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
	                                $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
	                                $this->CI->email->subject("Report-Site: EZANGA GEO Campaign Creation Error [{$c['io']}]");
	                                $this->CI->email->message("** EZANGA GEO CAMPAIGN CREATE ERROR **<br/><br/>Campaign IO: {$c['io']}<br/>\nCampaign Name: {$campaign['create_name']}<br/>Date: " . date("Y-m-d H:i:s"));
	                                $this->CI->email->send();
	                            }
	                        }
	                    }
	                    break;
	                    
	                default:
	                case "FIQ":
	                    /*
	                    $clicks_per_day = ceil((($campaign['fire_open_pixel'] == "Y") ? $campaign['total_opens'] : $campaign['total_clicks']) / 2.85);
	                    $initial_bid = (strtoupper($campaign['geotype']) == "COUNTRY") ? 0.0019 : 0.0026;
	                    $spend_per_day = sprintf("%.2f", $clicks_per_day * $initial_bid); // default bid, including overage
	                    $spend_per_day = ($spend_per_day > 4) ? $spend_per_day : 4.00; // minimum spend per day is $4.00

	                    // normalize the display ad url
	                    //$tmp_url = parse_url($ad['display_url']);
	                    //$ad['display_url'] = sprintf("%s://%s%s", $tmp_url['scheme'], $tmp_url['host']);

	                    $this->CI->Ad_model->title = $ad['title'];
	                    $this->CI->Ad_model->description = $ad['description'];
	                    $this->CI->Ad_model->category = 104; // hard coded to RUN OF NETWORK
	                    $this->CI->Ad_model->campaign_name = $campaign['io'] . " - " . $campaign['create_name'] . " (AUTO)";

	                    if ($campaign['fire_open_pixel'] == "Y")   {
	                        $this->CI->Ad_model->destination_url = "http://report-site.com/i/" . $campaign['io'];
	                    } else {
	                        $this->CI->Ad_model->destination_url = "http://report-site.com/r/" . $campaign['io'];
	                    }
	                    
	                    $this->CI->Ad_model->display_url = $ad['display_url'];
	                    $this->CI->Ad_model->bid = $initial_bid;
	                    $this->CI->Ad_model->daily_cap = $spend_per_day;
	                    $id = $this->CI->Ad_model->create();
	                    
	                    //print_r("Campaign ID: {$id}");
	                    */
	                    if ($id > 0)   {
	                        // create the ad @ FIQ
	                        $fiqID = $this->CI->Finditquick_model->create_ad($campaign['io'], $id);
	                        
	                        print_r($fiqID);
	                        
	                        // links the created "ad" to the campaign for future reference/usage.
	                        $this->CI->db->update("campclick_campaigns", array("local_ad_id" => $id), array("io" => $campaign['io']));
	                        
	                        if ($fiqID === false) {
	                            $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
	                            $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
	                            $this->CI->email->subject('Report-Site: FIQ Campaign Creation Error');
	                            $this->CI->email->message("** FIND IT QUICK CAMPAIGN CREATE ERROR **<br/><br/>Campaign IO: {$c['io']}<br/>\nCampaign Name: {$campaign['create_name']}<br/>Date: " . date("Y-m-d H:i:s"));
	                            $this->CI->email->send();
	                             
	                            $this->CI->Ad_model->id = $id;
	                            $this->CI->Ad_model->remove();
	                            return false;
	                        } else {
	                            // decode the json returned by the FIQ create ad
	                            print_r($fiqID);
	                            $fiqID = json_decode($fiqID);
	                            $fiqID = $fiqID[0]; // patch to fix broken JSON on 1/27/2015
	                        }
	                        
	                        print_r($fiqID);
	                        
	                        // update the campaign with ad ID
	                        $activate_campaign = false;
	                        if (isset($fiqID->Ad->id)) {
	                            // pause the ad while we "operate" on it - we dont need it to go live with incorrect info
	                            $this->CI->Finditquick_model->pause_ad($fiqID->Ad->id);
	                            
	                            $this->CI->db->query("UPDATE campclick_campaigns SET ppc_network_ad_id='{$fiqID->Ad->id}' WHERE io='{$campaign['io']}' LIMIT 1");
	                             
	                            // set target, budget, schedule and cap
	                            $total_clicks = ceil((($campaign['fire_open_pixel'] == "Y") ? $campaign['total_opens'] : $campaign['total_clicks']));
	                            if ($total_clicks > 40000)  {
	                                $set_schedule_result = $this->CI->Finditquick_model->set_schedule($fiqID->Ad->id); // use default (all hours of the day)
	                            } else {
	                                // this is a truely randomized schedule, its horrible though.
    	                            $schedule = "&schedule[0_8]=" . mt_rand(0,1) . "&schedule[0_9]=" . mt_rand(0,1) . "&schedule[0_10]=" . mt_rand(0,1) . "&schedule[0_11]=" . mt_rand(0,1) . "&schedule[0_12]=" . mt_rand(0,1) . "&schedule[0_13]=" . mt_rand(0,1) . "&schedule[0_14]=" . mt_rand(0,1) . "&schedule[0_15]=" . mt_rand(0,1) . "&schedule[0_16]=" . mt_rand(0,1) . "&schedule[0_17]=" . mt_rand(0,1) . "&schedule[0_18]=" . mt_rand(0,1) . "&schedule[0_19]=" . mt_rand(0,1) . "&schedule[0_20]=" . mt_rand(0,1) . "&schedule[0_21]=" . mt_rand(0,1) . "&schedule[0_22]=" . mt_rand(0,1) . "&schedule[0_23]=" . mt_rand(0,1) . "&";
    	                            $schedule .= "&schedule[1_8]=" . mt_rand(0,1) . "&schedule[1_9]=" . mt_rand(0,1) . "&schedule[1_10]=" . mt_rand(0,1) . "&schedule[1_11]=" . mt_rand(0,1) . "&schedule[1_12]=" . mt_rand(0,1) . "&schedule[1_13]=" . mt_rand(0,1) . "&schedule[1_14]=" . mt_rand(0,1) . "&schedule[1_15]=" . mt_rand(0,1) . "&schedule[1_16]=" . mt_rand(0,1) . "&schedule[1_17]=" . mt_rand(0,1) . "&schedule[1_18]=" . mt_rand(0,1) . "&schedule[1_19]=" . mt_rand(0,1) . "&schedule[1_20]=" . mt_rand(0,1) . "&schedule[1_21]=" . mt_rand(0,1) . "&schedule[1_22]=" . mt_rand(0,1) . "&schedule[1_23]=" . mt_rand(0,1) . "&";
    	                            $schedule .= "&schedule[2_8]=" . mt_rand(0,1) . "&schedule[2_9]=" . mt_rand(0,1) . "&schedule[2_10]=" . mt_rand(0,1) . "&schedule[2_11]=" . mt_rand(0,1) . "&schedule[2_12]=" . mt_rand(0,1) . "&schedule[2_13]=" . mt_rand(0,1) . "&schedule[2_14]=" . mt_rand(0,1) . "&schedule[2_15]=" . mt_rand(0,1) . "&schedule[2_16]=" . mt_rand(0,1) . "&schedule[2_17]=" . mt_rand(0,1) . "&schedule[2_18]=" . mt_rand(0,1) . "&schedule[2_19]=" . mt_rand(0,1) . "&schedule[2_20]=" . mt_rand(0,1) . "&schedule[2_21]=" . mt_rand(0,1) . "&schedule[2_22]=" . mt_rand(0,1) . "&schedule[2_23]=" . mt_rand(0,1) . "&";
    	                            $schedule .= "&schedule[3_8]=" . mt_rand(0,1) . "&schedule[3_9]=" . mt_rand(0,1) . "&schedule[3_10]=" . mt_rand(0,1) . "&schedule[3_11]=" . mt_rand(0,1) . "&schedule[3_12]=" . mt_rand(0,1) . "&schedule[3_13]=" . mt_rand(0,1) . "&schedule[3_14]=" . mt_rand(0,1) . "&schedule[3_15]=" . mt_rand(0,1) . "&schedule[3_16]=" . mt_rand(0,1) . "&schedule[3_17]=" . mt_rand(0,1) . "&schedule[3_18]=" . mt_rand(0,1) . "&schedule[3_19]=" . mt_rand(0,1) . "&schedule[3_20]=" . mt_rand(0,1) . "&schedule[3_21]=" . mt_rand(0,1) . "&schedule[3_22]=" . mt_rand(0,1) . "&schedule[3_23]=" . mt_rand(0,1) . "&";
    	                            $schedule .= "&schedule[4_8]=" . mt_rand(0,1) . "&schedule[4_9]=" . mt_rand(0,1) . "&schedule[4_10]=" . mt_rand(0,1) . "&schedule[4_11]=" . mt_rand(0,1) . "&schedule[4_12]=" . mt_rand(0,1) . "&schedule[4_13]=" . mt_rand(0,1) . "&schedule[4_14]=" . mt_rand(0,1) . "&schedule[4_15]=" . mt_rand(0,1) . "&schedule[4_16]=" . mt_rand(0,1) . "&schedule[4_17]=" . mt_rand(0,1) . "&schedule[4_18]=" . mt_rand(0,1) . "&schedule[4_19]=" . mt_rand(0,1) . "&schedule[4_20]=" . mt_rand(0,1) . "&schedule[4_21]=" . mt_rand(0,1) . "&schedule[4_22]=" . mt_rand(0,1) . "&schedule[4_23]=" . mt_rand(0,1) . "&";
    	                            $schedule .= "&schedule[5_8]=" . mt_rand(0,1) . "&schedule[5_9]=" . mt_rand(0,1) . "&schedule[5_10]=" . mt_rand(0,1) . "&schedule[5_11]=" . mt_rand(0,1) . "&schedule[5_12]=" . mt_rand(0,1) . "&schedule[5_13]=" . mt_rand(0,1) . "&schedule[5_14]=" . mt_rand(0,1) . "&schedule[5_15]=" . mt_rand(0,1) . "&schedule[5_16]=" . mt_rand(0,1) . "&schedule[5_17]=" . mt_rand(0,1) . "&schedule[5_18]=" . mt_rand(0,1) . "&schedule[5_19]=" . mt_rand(0,1) . "&schedule[5_20]=" . mt_rand(0,1) . "&schedule[5_21]=" . mt_rand(0,1) . "&schedule[5_22]=" . mt_rand(0,1) . "&schedule[5_23]=" . mt_rand(0,1) . "&";
    	                            $schedule .= "&schedule[6_8]=" . mt_rand(0,1) . "&schedule[6_9]=" . mt_rand(0,1) . "&schedule[6_10]=" . mt_rand(0,1) . "&schedule[6_11]=" . mt_rand(0,1) . "&schedule[6_12]=" . mt_rand(0,1) . "&schedule[6_13]=" . mt_rand(0,1) . "&schedule[6_14]=" . mt_rand(0,1) . "&schedule[6_15]=" . mt_rand(0,1) . "&schedule[6_16]=" . mt_rand(0,1) . "&schedule[6_17]=" . mt_rand(0,1) . "&schedule[6_18]=" . mt_rand(0,1) . "&schedule[6_19]=" . mt_rand(0,1) . "&schedule[6_20]=" . mt_rand(0,1) . "&schedule[6_21]=" . mt_rand(0,1) . "&schedule[6_22]=" . mt_rand(0,1) . "&schedule[6_23]=" . mt_rand(0,1) . "&";
    	                            
	                                $set_schedule_result = $this->CI->Finditquick_model->set_schedule($fiqID->Ad->id, true); // randomize schedule
	                                print_r($set_schedule_result);
	                            }
	                            
	                            $set_cap_result = $this->CI->Finditquick_model->set_cap($fiqID->Ad->id, $spend_per_day);
	                            $set_bid_result = $this->CI->Finditquick_model->set_bid($fiqID->Ad->id, $initial_bid);
	                            
	                            // fix our little issue where people are submitting blank geotype orders.
	                            if ((string)$campaign['geotype'] == "")    {
	                                if ($campaign['zip'] != "" && (strlen($campaign['state']) >= 2) && $campaign['country'] == "")    {
	                                    $campaign['geotype'] = "POSTALCODE";
	                                } elseif ($campaign['state'] != "" && $campaign['zip'] == "")  {
	                                    $campaign['geotype'] = "STATE";
	                                } elseif ($campaign['country'] != "" && $campaign['state'] == "" && $campaign['zip'] == "") {
	                                    $campaign['geotype'] = "COUNTRY";
	                                } else {
	                                    $campaign['geotype'] = ""; 
	                                }
	                            }
	                            // end for our fix
	                            
	                            switch(strtoupper($campaign['geotype']))   {
	                                default:
	                                case "COUNTRY":
	                                    $set_target_result = $this->CI->Finditquick_model->set_target($fiqID->Ad->id, $campaign['country']);
	                                    $activate_campaign = true;
	                                    break;
	                                    
	                                case "STATE":
	                                    $state = array();
	                                    foreach(explode(",", $campaign['state']) as $s)  {
	                                        if ($s == "")
	                                            continue;
	                                        
	                                        $state[] = "{$campaign['country']}/{$s}";
	                                    }
	                                    $set_target_result = $this->CI->Finditquick_model->set_target($fiqID->Ad->id, $state);
	                                    $activate_campaign = true;
	                                    break;
	                                    
	                                case "POSTALCODE":
	                                    $resultGeo = array();
	                                    $source_locations = array();
	                                    $ziplist = explode(",", $campaign['zip']);
	                                    
	                                    foreach($ziplist as $zip) {
	                                        if ($zip == "" || $zip == "undefined")
	                                            continue;

	                                        // hack for a STUPID excel copy-paste mistake that everyone makes
	                                        if (strlen($zip) == 4) {
	                                            $zip = "0" . $zip;
	                                        }
	                                        
	                                        // open the radius up a bit
	                                        if ($campaign['radius'] < 25)  {
	                                            $radius = $campaign['radius'] * 3;
	                                        } else {
	                                            $radius = $campaign['radius'] * 1.75; // open the radius (was 1.5 before 5/21/2015)
	                                        }
	                                        $zipLocations = $this->CI->Zip_model->find_locations($zip, $radius);
	                                        if (! empty($zipLocations))   {
	                                            foreach($zipLocations as $r)   {
	                                                $resultGeo[] = $r['final_tgt'];
	                                            }
	                                        }
	                                    }
	                                    
	                                    if (! empty($resultGeo))   {
	                                        $resultGeo = array_unique($resultGeo); // remove duplicate entries from array
	                                        
	                                        // this is a stop-gap for the issues we're having with set_target @ FIQ. We're reducing the qty of entries down to a MAX of 2500 random selections for now.
	                                        if (count($resultGeo) > 2500)  {
	                                            $newResultGeo = array();
	                                            $random_entries = array_rand($resultGeo, 2500);
	                                            foreach($random_entries as $e) {
	                                                $newResultGeo[] = $resultGeo[$e];
	                                            }
	                                            $resultGeo = $newResultGeo;
	                                        }
	                                        
	                                        print_r($resultGeo);
    	                                    $set_target_result = $this->CI->Finditquick_model->set_target($fiqID->Ad->id, $resultGeo);
    	                                    $activate_campaign = true;
	                                    }
                                        break;
	                            }

	                            // resume ad
	                            if ($activate_campaign === true)   {
	                                $this->CI->Finditquick_model->resume_ad($fiqID->Ad->id);

	                                $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
	                                $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
	                                $this->CI->email->subject("Report-Site: FIQ New Campaign Creation [{$c['io']}]");
	                                $this->CI->email->message("** FIND IT QUICK NEW CAMPAIGN CREATION **<br/><br/>Campaign IO: {$c['io']}<br/>\nCampaign Name: {$campaign['create_name']}<br/>Date: " . date("Y-m-d H:i:s"));
	                                $this->CI->email->send();
	                                 
	                            } else {
	                                $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
	                                $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
	                                $this->CI->email->subject("Report-Site: FIQ GEO Campaign Creation Error [{$c['io']}]");
	                                $this->CI->email->message("** FIND IT QUICK GEO CAMPAIGN CREATE ERROR **<br/><br/>Campaign IO: {$c['io']}<br/>\nCampaign Name: {$campaign['create_name']}<br/>Date: " . date("Y-m-d H:i:s"));
	                                $this->CI->email->send();
	                            }
                            }
	                    }
	                    break;
	                    
	                case "FACEBOOK":
	                    break;
	                    
	                case "GOOGLE":
	                    break;
	            }
	        }
	    }
	}
	
	public function set_is_geo_expanded()  {
	    $this->CI->db->update("take5_pending_campaigns", array("is_geo_expanded" => $this->is_geo_expanded, "last_geo_expanded_update" => date("Y-m-d H:i:s")), array("io" => $this->io));
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
	
}

?>
