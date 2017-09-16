<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Take5 extends CI_Controller	{
	public $viewArray = array();
	private $userid;
	
    public function __construct()	{
		parent::__construct();

		$this->load->helper("url");
		$this->load->helper('cookie');
		$this->load->library('user_agent');
		
		$this->load->model("Campclick_model");
		$this->load->model('Monitor_model');
		$this->load->model("Domains_model");
		$this->load->model("Vendor_model");
		$this->load->model("Country_model");
		$this->load->model("Take5_Campaign_Pending_Model");
		$this->load->model("Take5_Pending_Campaign_Links_Model");
		$this->load->model("Ad_Model");
		$this->load->model("Zip_model");
		$this->load->model("Report_model");
		$this->load->model("Log_model");
		$this->load->model("Finditquick_model");
		
		$this->load->library("parser");
		$this->load->library("session");
		$this->load->library('ion_auth');
		
		if ($this->ion_auth->logged_in()) {
		    $user = $this->ion_auth->user()->row();
		
	    if ($user->is_take5 == "Y")
	        $this->viewArray['take5_user'] = true;
		}
		
		$this->userid = $user->id;
		
		$this->session->set_userdata("domain_name", $user->domain_name);
		$this->viewArray['domain_name'] = $this->session->userdata("domain_name");
		
		$this->viewArray['current_url'] = current_url();
		$this->viewArray['base_url'] = base_url();
		$this->viewArray['site_url'] = site_url();
		
		$config['protocol'] = 'sendmail';
		$config['mailpath'] = '/usr/sbin/sendmail';
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = TRUE;
		$config['mailtype'] = 'html';
		$config['priority'] = 1;
		
		$this->load->library('email');
		$this->email->initialize($config);

    }
    
    public function index() {
        $this->parser->parse('take5/trackreport.php', $this->viewArray);
    }
    
    public function tracking()	{
    	$this->require_auth();
    	
    	$user = $this->ion_auth->user()->row();
		if($this->ion_auth->is_admin())
    		$userid = '';
		else
	        $userid = $user->id;
		
		$date_offset = date("Y-m-d 00:00:00", strtotime("15 days ago"));
		
		$this->session->set_userdata("domain_name", $user->domain_name);
		$this->viewArray['domain_name'] = $this->session->userdata("domain_name");
    	$this->viewArray['campaigns'] = array(
    		"inprogress" => $this->Campclick_model->get_campaign_list($userid, "Y", "campaign_is_started='Y' AND campaign_is_complete='N'"),
    		"completed" => $this->Campclick_model->get_campaign_list($userid, "Y", "campaign_is_complete='Y' AND campaign_start_datetime >= '{$date_offset}'"),
    	);

    	$this->parser->parse('take5/trackreport.php', $this->viewArray);
    }

    public function require_auth()	{
        if (!$this->ion_auth->logged_in())	{
            redirect('auth/login', 'refresh');
        } else {
            if($this->ion_auth->is_admin())
                $this->viewArray['manage_users'] = true;
            else
                $this->viewArray['manage_users'] = false;
    
            $this->viewArray['show_top_menu'] = true;
        }
    }
    
    public function trackingreport($io = "")    {
        if ($io == "")
            return false;
        
        $data = $this->Campclick_model->io_tracking_report($io);

        $date = date("Y-m-d");
        
        header("Content-Type: application/csv");
        header("Content-Disposition: attachment; filename=ProDataFeed-IO-{$io}-Track-{$date}.csv");
        header('Pragma: no-cache');

        $fp = fopen('php://output', 'w');
        
        print "IO,ReportSite_URL,Destination_URL,RealURL,CampaignStartDate,ClickCount,UniqueCnt,MobileCnt\n";
        foreach($data as $d)    {
            $myUrl = $this->Monitor_model->retrieve_remote_url($d['dest_url']);
            
            $line = array(
                "IO" => $d['io'],
                "Reportsite_URL" => "http://report-site.com/c/{$d['io']}/{$d['counter']}",
                "Destination_URL" => $d['dest_url'],
                "RealURL" => $myUrl[0],
                "CampaignStartDate" => $d['campaign_start_datetime'],
                "ClickCount" => $d['click_count'],
                "UniqueCnt" => (int)$this->Campclick_model->io_tracking_unique($d['io']),
                "MobileCnt" => (int)$this->Campclick_model->io_tracking_mobile($d['io']),
            );
            
            fputcsv($fp, $line);
        }
        
        fclose($fp);
    }
    
    public function newcampaign()  {
        $this->require_auth();
        $user = $this->ion_auth->user()->row();
    
        if ($this->ion_auth->is_admin())	{
            $user_id = "5"; // was previously ""
        } else {
            $user_id = $user->id;
        }
        
        $this->Domains_model->user_id = $user_id;
        $this->viewArray['domain'] = $this->Domains_model->get_domain_list($user_id);
        $this->viewArray['vendor'] = $this->Vendor_model->get_vendor_list($user_id);
        $this->viewArray['user_id'] = $user_id;
        $this->viewArray['is_take5_user'] = $user->is_take5;

        $this->parser->parse("take5/newcampaign.php", $this->viewArray);
    }
    
    public function clickmap_ajax()    {
        libxml_use_internal_errors(true);
         
        $cnt = 0;
        $parsedLinks = array();
         
        $xml = new DOMDocument();
         
        $xml->loadHTML($this->input->post("message"));
    
        $head = $xml->getElementsByTagName("head")->item(0);
        $documentHead = $this->DOMInnerHTML($head);
        $documentHead = "<head>" . $documentHead . "</head>";
         
        $body = $xml->getElementsByTagName('body')->item(0);
        $documentBody = $this->DOMInnerHTML($body);
        $documentBody = str_ireplace("<body>", "", $documentBody);
        $documentBody = str_ireplace("</body>", "", $documentBody);
    
        $bodyAttr = "";
        if ($body->hasAttributes()) {
            foreach($body->attributes as $attr) {
                $bodyAttr .= "{$attr->nodeName}='{$attr->nodeValue}' ";
            }
        }
    
        $documentBody = "<style>.click_border { outline: thick solid #64FE2E !important }</style><body {$bodyAttr}><div id='prodatafeed_hm_master_id'>" . $documentBody . "</div></body>";
    
        $documentFinal = "<html><head>" . $documentHead . "</head>" . $documentBody . "</html>";
    
        // lets reset and start over
        $xml->loadHTML($documentFinal);
    
        //$body->innerHTML = $documentBody;
    
        // create the click_border
        //$styleDom = $xml->createElement("style", ".click_border { outline: thick solid #64FE2E !important }");
         
        foreach($xml->getElementsByTagName('a') as $link)  {
            $oldLink = $link->getAttribute("href");
            $link->setAttribute('data-id', $cnt);
            $link->setAttribute('id', "hm_link_" . $cnt);
             
            $parsedLinks[] = array('href' => $link->getAttribute('href'), 'text' => $text, "link_id" => $cnt);
            $cnt++;
        }
         
        //$xml->appendChild($styleDom);
        $message = $xml->saveHtml();
         
        print json_encode(array("status" => "SUCCESS", "content" => $message, "links" => $parsedLinks));
    }
    
    private function DOMInnerHTML(DOMNode $element)    {
        $innerHTML = "";
        $children  = $element->childNodes;
         
        foreach($children as $child)   {
            $innerHTML .= $element->ownerDocument->saveHTML($child);
        }

        return $innerHTML;
    }

    public function get_states_list($country = "")    {
        $this->Country_model->country = $country;
        print json_encode(array("status"=>"SUCCESS", "states" => $this->Country_model->get_states()));
    }
    
    public function process_order_request() {
        $this->Take5_Campaign_Pending_Model->userid = $this->userid;
        $id = $this->Take5_Campaign_Pending_Model->process_raw_post($_POST);

        if ($id > 0)    {
            print json_encode(array("status" => "SUCCESS", "campaign_id" => $id));
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Error on campaign creation"));
        }
        exit;
    }
    
    public function queue($campaign_is_approved = "N", $campaign_is_convert_to_live = "N") {
        $this->require_auth();
        $user = $this->ion_auth->user()->row();
        
        if ($this->ion_auth->is_admin())	{
            $user_id = "5"; // was previously ""
        } else {
            $user_id = $user->id;
        }
        
        $this->Domains_model->user_id = $user_id;
        $this->Take5_Campaign_Pending_Model->userid = $user_id;
        $this->viewArray['campaigns']['pending'] = $this->Take5_Campaign_Pending_Model->get_list("N", "N");
        $this->viewArray['campaigns']['approved'] = $this->Take5_Campaign_Pending_Model->get_list("Y", "Y");
        
        $this->parser->parse("take5/queue.php", $this->viewArray);
    }
    
    public function remove_order_request($id = "")  {
        $this->Take5_Campaign_Pending_Model->id = (int)$id;
        $this->Take5_Campaign_Pending_Model->remove_pending_campaign();
        
        print json_encode(array("status" => "SUCCESS"));
    }
    
    public function accept_order_request($id = "")  {
        $this->Take5_Campaign_Pending_Model->id = (int)$id;
        $this->Take5_Campaign_Pending_Model->userid = (int)$this->userid;
        $this->Take5_Campaign_Pending_Model->convert_order();
        
        print json_encode(array("status" => "SUCCESS"));
    }
    
    public function check_io($io = "")	{
        $this->Campclick_model->io = $io;
        $io = $this->Campclick_model->get_campaign_id_from_io();
        
        $this->Take5_Campaign_Pending_Model->io = $io;
        $io2 = $this->Take5_Campaign_Pending_Model->get_campaign_id_from_io();
        
        // sum these together - if they are > 0, then one exists
        $io = $io + $io2;
        
        if ($io > 0)	{
            print json_encode(array("status"=>"ERROR")); // io exists
        } else {
            print json_encode(array("status"=>"SUCCESS")); // io doesnt exist
        }
        exit;
    }
    
    public function get_pending_campaign($id = "")  {
        $this->Take5_Campaign_Pending_Model->id = $id;
        $r = $this->Take5_Campaign_Pending_Model->get_campaign_by_id();
 
        print json_encode(array("status"=>"SUCCESS", "data"=>$r));
    }
    
    public function adtest()    {
        /*
        $this->Take5_Pending_Campaign_Links_Model->pending_campaign_id = 17;
        $url = $this->Take5_Pending_Campaign_Links_Model->get_primary_campaign_link();

        //$ad = $this->Ad_Model->auto_generate_ad_content($url['destination_url']);
        $ad = $this->Ad_Model->auto_generate_ad_content("http://www.buydig.com");
        
        print_r($ad);
        */
        
        $this->Take5_Campaign_Pending_Model->make_campaign_live();
    }
    
    public function createVerizon($iofile = "", $file = "") {
        if ($file == "" || $iofile == "")
            die("FILE REQUIRED");
        
        $data = file_get_contents("/var/www/{$file}");
        $lines = explode("\n", $data);
        
        $iodata = file_get_contents("/var/www/{$iofile}");
        $iolines = explode("\n", $iodata);
        
        $iodata = array();
        foreach($iolines as $l)   {
            if ($l == "")
                continue;
            
            list($io, $total_records, $click) = explode("\t", $l);

            if ($io == "")
                continue;
            
            $open = 10; // 10% hard coded
            
            $day = substr($io, -1);
            
            $iodata[$io] = array(
                "total_records" => $total_records,
                "click_count" => ceil($total_records * ($click / 100)),
                "open_count" => ceil($total_records * ($open / 100)),
                "start_date" => date("Y-m-d 00:00:00", strtotime("+{$day}"))
            );
        }
        
        print_r($iodata);
        
        $links = array();
        foreach($lines as $l)   {
            if ($l == "")
                continue;
            
            list($url, $io) = explode("\t", $l);
            
            if ($io == "")
                continue;
            
            $links[$io][] = array(
                "destination_url" => $url,
                "original_url" => $url,
                "click_count" => 0,
            );
        }
        
        $ch = curl_init('http://www.report-site.com/rtb_api/campaign/format/json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-ProDataFeed-Auth: accf71e711cedbd30e5accd0633d8b44'
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        
        foreach($links as $k => $v) {
            //print $k . "==" . print_r($v,true) . "\n";

            if ($k == "" || $k == "IO")
                continue;
            
            $day = substr($k, -2);
            
            $postFields = array(
                "io" => $k,
                "geotype" => "COUNTRY",
                "country" => "US",
                "fire_open_pixel" => "N",
                "total_opens" => $iodata[$k]['open_count'],
                "total_clicks" => $iodata[$k]['click_count'],
                "total_records" => $iodata[$k]['total_records'],
                "campaign_name" => "Verizon {$k} Day {$day}",
                "campaign_start_datetime" => $iodata[$k]['start_date'],
                "campaign_is_approved" => "N",
                "vertical" => "CONSUMER",
            );
            
            $linkStr = "";
            $cnt = 0;
            foreach($v as $vv)  {
                $linkStr .= "&links[{$cnt}][link]=" . urlencode($vv['destination_url']);
                $linkStr .= "&links[{$cnt}][count]=" . $vv['click_count'];
                $cnt++;
            }
            
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields) . "&" . $linkStr);
            $result = curl_exec($ch);
            
//            print_r($postFields);
            print_r($result);
        }
        
        curl_close($ch);
    }
    
    public function expand_geo_target() {
        $inprogress = $this->Campclick_model->get_campaign_list(null, "Y", "AND campaign_is_started='Y' AND campaign_is_complete='N'");
        
        foreach($inprogress as $io) {
            if ($io['is_geo'] == "Y")   {
                
                $this->Take5_Campaign_Pending_Model->io = $io['io'];
                $pending_id = $this->Take5_Campaign_Pending_Model->get_campaign_id_from_io(true);
                $this->Take5_Campaign_Pending_Model->id = $pending_id;
                $pending_io = $this->Take5_Campaign_Pending_Model->get_campaign_by_id();
                
                // skip if our geo-type is NOT postalcode (zip)
                if (strtoupper($pending_io['geotype']) != "POSTALCODE")
                    continue;

                $this->Campclick_model->io = $pending_io['io'];
                if ($pending_io['fire_open_pixel'] == "Y")  {
                    $current_clicks = $this->Campclick_model->get_campaign_impressions();
                } else {
                    $current_clicks = $this->Campclick_model->get_campaign_clicks();
                }

                $date_start_int = strtotime($pending_io['campaign_start_datetime']);
                $date_current_int = time();
                
                $num_hours = round(($date_current_int - $date_start_int)/3600, 0, PHP_ROUND_HALF_DOWN);
                $new_radius = $pending_io['radius'];

                // skip if this was adjusted within the last 12hrs. OR if under 48 hrs
                if ((time() - strtotime($pending_io['last_geo_expanded_update'])) < 43200 || $num_hours < 48)    {
                    continue;
                }
                
                if ($num_hours < 48)    {
                    // do nothing
                } elseif ($num_hours > 48 && $num_hours < 60 && $current_clicks < round($io['max_clicks'] * 0.66))   {
                    // bump to radius+50
                    $new_radius += 50;
                } elseif ($num_hours > 60 && $num_hours < 120 && $current_clicks < round($io['max_clicks'] * 0.90))  {
                    // bump to radius+75
                    $new_radius += 75;
                } elseif ($num_hours > 120 && $current_clicks < round($io['max_clicks'] * 0.90)) {
                    // bump to radius * 2
                    if ($new_radius < 250)
                        $new_radius = ($new_radius * 2);
                } else {
                    // do nothing, this is a catch all for unmatching issues
                }
                
                if ($new_radius != $pending_io['radius'])   {
                    $this->Log_model->io = $io['io'];
                    $this->Log_model->action = "CAMPAIGN_GEO_ADJUSTMENT";
                    $this->Log_model->note = "GEO Adjustment for {$io['io']}. Radius: {$new_radius}mi. Zip List: {$pending_io['zip']}";
                    $this->Log_model->create();
                    
                    $this->Zip_model->set_campaign_radius($new_radius, $io['io']);
                    $this->Zip_model->set_campaign_ziplist($pending_io['zip'], $io['io']);
                    
                    $resultGeo = array();
                    $source_locations = array();
                    $ziplist = explode(",", $pending_io['zip']);
                     
                    foreach($ziplist as $zip) {
                        if ($zip == "" || $zip == "undefined")
                            continue;
                    
                        // hack for a STUPID excel copy-paste mistake that everyone makes
                        if (strlen($zip) == 4) {
                            $zip = "0" . $zip;
                        }
                         
                        $zipLocations = $this->Zip_model->find_locations($zip, $new_radius);
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
                         
                        // bad AD ID HERE >>> FIX IT!
                        $set_target_result = $this->Finditquick_model->set_target($io['ppc_network_ad_id'], $resultGeo);
                        $this->Take5_Campaign_Pending_Model->io = $pending_io['io'];
                        $this->Take5_Campaign_Pending_Model->is_geo_expanded = "Y";
                        $this->Take5_Campaign_Pending_Model->set_is_geo_expanded();
                        
                        
                        $this->email->from('noreply@report-site.com', 'Report-Site No Reply');
                        $this->email->to('jkorkin@safedatatech.onmicrosoft.com');
                        //$this->email->cc('fulfillment@take5s.com', 'orderCR@take5s.com');
                        $this->email->subject("Report-Site: {$io['io']} GEO Expanded");
                        $this->email->message("GEO expanded to {$new_radius}mi. for IO: {$io['io']}.");
                        $this->email->send();
                        
                        print "{$io['io']} - {$num_hours} - {$pending_io['radius']} - {$new_radius}\n";
                    }
                    
                }
            }
        }
    }
    
}

?>