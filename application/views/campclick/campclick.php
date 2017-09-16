<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campclick extends CI_Controller	{
	public $viewArray = array();
	
    public function __construct()	{
		parent::__construct();

		$this->load->helper("url");
		$this->load->helper('cookie');
		
		$this->load->library("parser");
		$this->load->library("session");
		//$this->load->model("Users_model");
		$this->load->model("Campclick_model");
		$this->viewArray['current_url'] = current_url();
		$this->viewArray['base_url'] = base_url();
		$this->viewArray['site_url'] = site_url();
		/*
		if ((int)$this->session->userdata("id") > 0)	{
			$this->Users_model->id = (int)$this->session->userdata("id");
			$u = $this->Users_model->get_user_by_id(true);
		}

		if ($this->session->userdata("logged_in") === true)	{
			$this->viewArray['logged_in'] = true;
		} else {
			redirect("/auth/logout");
			exit;
		}

		$this->viewArray['user'] = (array)$u;
		*/
		
    }
    
    public function update_geo_tracking($max = 60)	{
    	$this->Campclick_model->geo_track_update($max);
    }
    
    public function update_fulfilled_status($io = "")	{
    	if ($io == "")	{
    		print "IO Required";
    		exit;
    	}
    	$this->Campclick_model->update_fulfilled_status($io);
    }
    
    public function random($io = "")	{
    	$this->Campclick_model->io = $io;
    	$link = $this->Campclick_model->select_random_link();
    	$this->redirect($io, $link['counter'], $link['link_id']);
    }

    public function report($io = "",$offset=0)	{
    	$this->viewArray['io'] = $io;
		
		$this->load->library('pagination');		
		$config['base_url'] = base_url()."campclick/report/$io";
		$config['num_links'] = 4;
		$config['uri_segment'] = 4;
		$config['per_page'] = 20;
		$config['total_rows'] = $this->Campclick_model->get_all_data($io, 'hour', '', '', '', '', '', true);
		$this->pagination->initialize($config); 	
		$this->viewArray['pagination_link'] = $this->pagination->create_links();	
		
		
    	$this->viewArray['report'] = $this->Campclick_model->report_by_io($io, 'hour');
		$this->viewArray['all_data'] = $this->Campclick_model->get_all_data($io, 'hour','','','',$offset,$config['per_page'], false);
		$this->Campclick_model->get_click_count_by_hour('2013-03-05');
		$this->viewArray['moreinfo_url'] = current_url();
    	
		$this->parser->parse('campclick/report.php', $this->viewArray);
    }
	
	
    public function last_month_report($io = "",$offset=0)	{
    	$this->viewArray['io'] = $io;
		echo $io;
		exit;
		$this->load->library('pagination');		
		$config['base_url'] = base_url()."campclick/last_month_report/$io";
		$config['num_links'] = 4;
		$config['uri_segment'] = 4;
		$config['per_page'] = 20;
		$config['total_rows'] = $this->Campclick_model->get_all_data($io, 'month', '', '', '', '', '', true);
		$this->pagination->initialize($config); 	
		$this->viewArray['pagination_link'] = $this->pagination->create_links();		
		
    	$this->viewArray['report'] = $this->Campclick_model->report_by_io($io,'month');
		$this->viewArray['all_data'] = $this->Campclick_model->get_all_data($io, 'month', '', '', '', $offset, $config['per_page'], false);	
		
		
		$this->Campclick_model->get_click_count_by_hour('2013-03-05');
    	
		$this->parser->parse('campclick/last_month_report.php', $this->viewArray);
    }


    public function date_range_report($io = "", $start_date = "", $end_date ="",$offset=0)	{
		$this->viewArray['sDate'] = $start_date;
		$this->viewArray['eDate'] = $end_date;	
    	$this->viewArray['io'] = $io;
		
		$this->load->library('pagination');		
		$config['base_url'] = base_url()."campclick/date_range_report/$io/$start_date/$end_date";
		$config['num_links'] = 4;
		$config['uri_segment'] = 6;
		$config['per_page'] = 20;
		$config['total_rows'] = $this->Campclick_model->get_all_data($io, 'daterange', $start_date, $end_date, '', '', '', true);
		$this->pagination->initialize($config); 	
		$this->viewArray['pagination_link'] = $this->pagination->create_links();
		
		
    	$this->viewArray['report'] = $this->Campclick_model->report_by_io($io, 'daterange', $start_date, $end_date);
		$this->viewArray['all_data'] = $this->Campclick_model->get_all_data($io, 'daterange',  $start_date, $end_date, '', $offset, $config['per_page'], false);
		$this->Campclick_model->get_click_count_by_hour('2013-03-05');
    	
		$this->parser->parse('campclick/date_range_report.php', $this->viewArray);
    }

    public function data($type = "unique", $io = 0, $counter = 0, $sdate = "", $edate = "")	{
    	$sdate = ($sdate == "") ? date("Y-m-d", strtotime("-14 days")) : $sdate;
    	$edate = ($edate == "") ? date("Y-m-d") : $edate;
    	
    	header("Content-type: application/json");
    	/*header("Content-type: text/csv");
    	print "date,unique_clickers\n";
    	foreach($this->Campclick_model->report_by_io_counter($io, $counter) as $l)	{
    		print $l['date'] . "," . $l['unique_clickers'] . "\n";
    	}*/
    	print json_encode($this->Campclick_model->report_by_io_counter($io, $counter, $sdate, $edate));
    }

    public function moreinfo($io = "", $counter = 0, $offset=0)	{
    	$this->viewArray['io'] = $io;
    	$this->viewArray['counter'] = $counter;
		$this->load->library('pagination');		
		$config['base_url'] = base_url()."campclick/moreinfo/$io/$counter";
		$config['num_links'] = 4;
		$config['uri_segment'] = 5;
		$config['per_page'] = 20;
		$config['total_rows'] = $this->Campclick_model->get_all_data($io, 'hour', '', '', $counter, '', '', true);
		$this->pagination->initialize($config); 	
		$this->viewArray['pagination_link'] = $this->pagination->create_links();			
		
    	$this->viewArray['report'] = $this->Campclick_model->report_by_io_counter($io, 'hour','','',$counter);
    	$this->viewArray['all_data'] = $this->Campclick_model->get_all_data($io, 'hour','','','',$offset,$config['per_page'], false);
		$this->parser->parse('campclick/moreinfo.php', $this->viewArray);
    }
	
	
    public function moreinfo_last_month($io = "", $counter = 0, $offset=0)	{
    	$this->viewArray['io'] = $io;
    	$this->viewArray['counter'] = $counter;
		
		
		
		$this->load->library('pagination');		
		$config['base_url'] = base_url()."campclick/moreinfo_last_month/$io/$counter";
		$config['num_links'] = 4;
		$config['uri_segment'] = 5;
		$config['per_page'] = 20;
		$config['total_rows'] = $this->Campclick_model->get_all_data($io, 'month', '', '', $counter, '', '', true);
		$this->pagination->initialize($config); 	
		$this->viewArray['pagination_link'] = $this->pagination->create_links();		
		
		
		
    	$this->viewArray['report'] = $this->Campclick_model->report_by_io_counter($io, 'month', '', '', $counter);
    	$this->viewArray['all_data'] = $this->Campclick_model->get_all_data($io, 'month', '', '', $counter,$offset,$config['per_page'], false);    	
		$this->parser->parse('campclick/moreinfo_last_month.php', $this->viewArray);
    }
	
    public function more_info_date_range($io = "", $counter = 0,$sDate="", $eDate="",$offset=0)	{
    	$this->viewArray['io'] = $io;
    	$this->viewArray['counter'] = $counter;
		
		$this->load->library('pagination');		
		$config['base_url'] = base_url()."campclick/more_info_date_range/$io/$counter/$sDate/$eDate";
		$config['num_links'] = 4;
		$config['uri_segment'] = 7;
		$config['per_page'] = 20;
		$config['total_rows'] = $this->Campclick_model->get_all_data($io, 'daterange', $sDate, $eDate, $counter, '', '', true);
		$this->pagination->initialize($config); 	
		$this->viewArray['pagination_link'] = $this->pagination->create_links();			
		
    	$this->viewArray['report'] = $this->Campclick_model->report_by_io_counter($io, 'daterange', $sDate, $eDate, $counter,$offset,$config['per_page']);
    	$this->viewArray['all_data'] = $this->Campclick_model->get_all_data($io, 'daterange', $sDate, $eDate, $counter,$offset,$config['per_page'], false);     	
		$this->parser->parse('campclick/moreinfo_date_range.php', $this->viewArray);
    }	
    public function campcreate()	{
    	$this->parser->parse('campclick/message.php', $this->viewArray);
    }
    
    public function index()	{
    	$this->viewArray['campaigns'] = $this->Campclick_model->get_campaign_list();
    	$this->parser->parse('campclick/camplist.php', $this->viewArray);
    }
    
    public function redirect($io = "", $counter = 0, $is_random = false)	{

    	// rewrite the IO stuff as needed.
    	switch($io)	{
    		case "9391A":
    			$io = "9391AA";
    			break;
    		
    		case "9391B":
    			$io = "9391BB";
    			break;
    			
    		case "9391C":
    			$io = "9391CC";
    			break;
    			
    		case "9391D":
    			$io = "9391DD";
    			break;
    			
    		default:
    			$io = $io;
    			break;
    	}
    	
    /*
    // redirect some external traffic from 9444 to this campaign (9391, 9444)
    if ($io == "9444")	{
    	$camplist = array('9444', '9391');
		$randomSelectedIO = array_rand($camplist);
		$io = $camplist[$randomSelectedIO];
		header("Referer: http://t5camps.com/r/{$io}");
		header("Location: http://t5camps.com/r/{$io}");
		exit;
    }
    
    // redirect all external traffic from 9412 to this campaign (9391)
      if ($io == "9412")	{
    	$io = "9391";
    }
    */

    // randomly select one of four "sub" campaigns from 9391.
    if ($io == "9391DD" || $io == "9391AA")	{
    //if (strpos($io, "9391") !== false)	{
    	//$camplist = array('9391DD', '9391CC', '9391BB', '9391AA');
		$camplist = array('9391DD', '9391AA', '-1', '-1', '-1', '-1');
    	$randomSelectedIO = array_rand($camplist);
		$io = $camplist[$randomSelectedIO];
		if ($io == "-1")	{
			// push some traffic over to Paul's FB page for an experiment. (that should be 60% of DD and AA traffic)
			redirect("https://www.facebook.com/BocaRatonPromotions");
			exit;
		}
    }
    
    if ($io == "9391BB")	{
    //if (strpos($io, "9391") !== false)	{
    	//$camplist = array('9391DD', '9391CC', '9391BB', '9391AA');
		$camplist = array('9391BB', '-1', '-1', '-1', '-1');
    	$randomSelectedIO = array_rand($camplist);
		$io = $camplist[$randomSelectedIO];
		if ($io == "-1")	{
			// push some traffic over to Paul's FB page for an experiment. (that should be 60% of DD and AA traffic)
			redirect("https://www.facebook.com/BocaRatonPromotions");
			exit;
		}
    }
    
    if ($io == "9391CC")	{
    //if (strpos($io, "9391") !== false)	{
    	//$camplist = array('9391DD', '9391CC', '9391BB', '9391AA');
		$camplist = array('9391BB', '-1', '-1', '-1', '-1');
    	$randomSelectedIO = array_rand($camplist);
		$io = $camplist[$randomSelectedIO];
		if ($io == "-1")	{
			// push some traffic over to Paul's FB page for an experiment. (that should be 60% of DD and AA traffic)
			redirect("https://www.facebook.com/BocaRatonPromotions");
			exit;
		}
    }
    
    /*    	
    	if ($io == "2072" && $counter == "0")	{
    		redirect("http://t5camps.com/r/9412");
    		exit;
    	}
    	if ($io == "2073" && $counter == "0")	{
    		redirect("http://t5camps.com/r/9037");
    		exit;
    	}
    	*/
    	
    	$link = $this->Campclick_model->get_link($io, $counter);
    	$this->Campclick_model->log_click($io, $link['link_id']);
    	
    	// update fulfillment of the order -- MOVED TO A CRON SCRIPT NOW!
    	//$this->Campclick_model->update_fulfilled_status($io);
    	
    	if ($is_random !== false)	{
    		//$this->Campclick_model->update_fulfilled_count($io, $is_random['link_id']);
    	}
    	
    	$cookie = array(
    		"name" => "trafficPingTracker",
    		"value" => $io,
    		"expire" => 30*86400,
    		"domain" => ".t5camps.com",
    		"path" => "/",
    	);

    	set_cookie($cookie);
		header("Referer: http://t5camps.com/c/{$io}/0");
//		header("Location: {$link['dest_url']}");
		print "<html><head><meta http-equiv='refresh' content=\"0;URL='{$link['dest_url']}'\"></head></html>";
    	exit;
    }

    public function generate_code()	{
    	$message = $this->input->post("message");
    	$io = $this->input->post("io");
    	$name = $this->input->post("name");

		$this->Campclick_model->io = $io;
		$this->Campclick_model->name = $name;
		$this->Campclick_model->message = $message;
		$campId = $this->Campclick_model->create();

    	//
		// Parse out the links, replace with our click tracking code.
		//
		$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
		if (preg_match_all("/$regexp/siU", $message, $matches, PREG_SET_ORDER)) {
			$counter = 1;
			foreach($matches as $match) { 
				// $match[2] = link address 
				// $match[3] = link text

				// create the click/dest links
				$this->Campclick_model->create_links($match[2], $io, $counter);

				$link = "http://t5camps.com/c/{$io}/{$counter}";
				$message = str_ireplace($match[2], $link, $message);
				$counter++;
			}
		}
		
		// create the "Default" dest link
		$this->Campclick_model->create_links($this->input->post("default_url"), $io, 0);
		
		print json_encode(array(
			"status" => "SUCCESS",
			"message" => $message,
			"url" => "http://t5camps.com/c/{$io}/0"
		));
    }
    
    public function conversion($io = 0, $conv_value = 1)	{
    	$this->Campclick_model->io = $io;
    	$this->Campclick_model->conversion_value = sprintf("%.2f", $conv_value);
    	$this->Campclick_model->log_conversion($io, 0, $conv_value);
    	
    	print json_encode(array(
    		"status" => "SUCCESS"
    	));
    }
    
    public function loadlinks($io = "")	{
    	if ($io == "")	{
    		print "IO required";
    		exit;
    	}
    	
    	$file = file_get_contents("/var/www/{$io}-LINKS.txt");
    	$lines = explode("\n", $file);
    	
    	$cnt=1;
    	$total_clicks = 0;
    	foreach($lines as $l)	{
    		list($url, $percentage, $clicks) = explode("\t", $l);

    		$this->Campclick_model->create_links($url, $io, $cnt, $clicks);
    		$total_clicks += $clicks;
    		$cnt++;
    	}
    	
    	print "Total Expected Clicks: {$total_clicks}<br/>";
    	print "Total Links Created: {$cnt}";
    }
}
