<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campclick extends CI_Controller	{
    public $viewArray = array();
    private $re_injector_url = "http://report-site.com/campclick/reinjector";

    public function __construct()	{
        parent::__construct();

        $this->load->helper("url");
        return redirect('v2/campaign/campaign_list');
        $this->load->helper('cookie');
        $this->load->library('user_agent');

        $this->load->library("parser");
        $this->load->library("session");
        $this->load->library('ion_auth');
        $this->load->model("Campclick_model");
        $this->load->model("Domains_model");
        $this->load->model("Vendor_model");
        $this->load->model("Zip_model");
        $this->load->model("Trafficshape_model");
        $this->load->model("Finditquick_model");
        $this->load->model("Take5_Pending_Campaign_Openpixel_Model");
        $this->load->model("Log_model");
        $this->load->model("Ad_model");

        // this is for retargeting
        $this->load->library('Adwords');
        $this->load->model('Userlist_io_model');
        $this->load->model('Userlist_vertical_model');

        $this->viewArray['current_url'] = current_url();
        $this->viewArray['base_url'] = base_url();
        $this->viewArray['site_url'] = site_url();

        $this->viewArray['manage_users'] = false;
        $this->viewArray['show_top_menu'] = true;
        $this->viewArray['take5_user'] = false;

        $this->no_fraud = array(
            "66.0.218.10",
            "76.110.227.216",
            "50.198.249.13",
            "76.110.217.139"
        );

        if ($this->ion_auth->logged_in()) {
            $user = $this->ion_auth->user()->row();

            if ($user->is_take5 == "Y")
                $this->viewArray['take5_user'] = true;

            $this->viewArray['domain'] = $user->domain_name;
            $this->viewArray['logo'] = $user->logo;
        }
    }

    public function register()	{
        $this->ion_auth->register("admin@admin.com", "asdasdasd", "admin@admin.com");
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

    public function update_geo_tracking($max = 60, $io = "")	{
        $this->Campclick_model->geo_track_update($max, $io);
    }

    public function update_fulfilled_status()	{
        $this->Campclick_model->update_fulfilled_status();
    }

    public function impression($io = "", $bypass = "")    {
        if ($io == "14716-E7")  {
            redirect("/campclick/verizoninjector/14716-E7/1796");
            exit;
        }

        $this->Campclick_model->io = $io;

        $this->Campclick_model->update_click_cap();
        $count = $this->Campclick_model->get_current_click_cap();

        $campaign = $this->Campclick_model->get_campaign_by_io();

        if ($campaign['fire_open_pixel'] == "Y" && $campaign['impression_clicks'] > 0 && ($count <= $campaign['cap_per_hour'] || $campaign['cap_per_hour'] == "0"))    {
            $pixels = $this->Take5_Pending_Campaign_Openpixel_Model->get_pixels($io);

            if (!empty($pixels))    {
                $random_open_pixel = array();
                foreach($pixels as $p)  {
                    $random_open_pixel[] = array("id" => $p['id'], "src" => $p['pixel_url']);
                }

                $weighted_percentage = ceil(($campaign['impression_clicks']/$campaign['opens']) * 100);

                $random_link = array();
                for($x=1; $x<=100; $x++)    {
                    if ($x <= $weighted_percentage) {
                        $random_link[] = "http://report-site.com/r/{$io}";
                    } else {
                        $random_link[] = "http://facebook.com/cheapdeals4me";
                        //$random_link[] = "http://report-site.com/campclick/reinjector";
                    }
                }

                if (! empty($random_link))  {
                    $fallover_redirect = $random_link[array_rand($random_link, 1)];
                    $fallover_openpixel = $random_open_pixel[array_rand($random_open_pixel, 1)];

                    // for those who insist in just adding a URL for the open pixel... grrr...
                    if (! preg_match('/(<img[^>]+>)/i', $fallover_openpixel['src']))  {
                        $fallover_openpixel['src'] = "<img src='" . $fallover_openpixel['src'] . "' />";
                    }

                    // log the impression
                    $this->Campclick_model->log_impression($io, $fallover_openpixel['id']);
                }
            } else {
                $fallover_openpixel['src'] = "<img src='http://report-site.com/noimage.png' />";
                $fallover_redirect = "http://report-site.com/r/{$io}";
            }

            $cookie = array(
                "name" => "trafficPingTracker",
                "value" => $io,
                "expire" => 1825*86400,
                "domain" => ".report-site.com",
                "path" => "/",
            );

            if ($bypass != "")  {
                $fallover_redirect = "http://report-site.com/r/{$io}";
            }

            // adwords user mapping code
            $this->Campclick_model->io = $io;
            $vertical = $this->Campclick_model->get_vertical_by_io();
            $arrIoList = $this->Userlist_io_model->get_userlist_from_io($io);

            // if io list doesnt exist, create a new one
            if(empty($arrIoList)){
                $arrIoList = $this->createIoList($io);
            }
            $arrIoList = isset($arrIoList[0])?$arrIoList[0]:array();
            $arrVerticalList = $this->Userlist_vertical_model->get_userlist_from_vertical($vertical);

            // if vertical list doesnt exist, create a new one
            if(empty($arrVerticalList)){
                $arrVerticalList = $this->createVerticalList($vertical);
            }

            $arrVerticalList = isset($arrVerticalList[0])?$arrVerticalList[0]:array();
            $verticalScriptTag = html_entity_decode($arrVerticalList['sniped_code']);
            $ioScriptTag = html_entity_decode($arrIoList['sniped_code']);
            // end adwords user mapping code

            set_cookie($cookie);
            header("Referer: http://report-site.com/i/{$io}");
            print "<html><head><meta http-equiv='refresh' content=\"1;URL='{$fallover_redirect}'\"></head><body>{$fallover_openpixel['src']}{$ioScriptTag}{$verticalScriptTag}</body></html>";
            exit;
        }

        //redirect("http://report-site.com/campclick/reinjector");
        redirect("http://facebook.com/cheapdeals4me");
    }

    public function random($io = "", $crap = "")	{
        $this->Campclick_model->io = $io;

        //redirect("http://report-site.com/campaigns/landing/index3.php?utm_campaign=prodata-test-2015-06-10&utm_medium=ppc&utm_source=prodatafeed");

        $link = $this->Campclick_model->select_random_link_improved();
//	    $link = $this->Campclick_model->select_random_link();

        if ($link !== false)	{
            $this->Campclick_model->update_click_cap();
            $count = $this->Campclick_model->get_current_click_cap();
            $campaign = $this->Campclick_model->get_campaign_by_io();

            // THIS WILL SET CAMPAIGN CAPS PER HOUR IF WE ENABLE IT
            if ($campaign['cap_per_hour'] == "0")   {
                $this->redirect($io, $link['counter'], $link['link_id'], $crap);
                exit;
            } else {
                if ($count <= $campaign['cap_per_hour'])  {
                    $this->redirect($io, $link['counter'], $link['link_id'], $crap);
                    exit;
                }
            }
        }

        if ($crap == "")
            print "blah";
        //$this->reinjector(); // if the campaign exceeds its cap per hour, reinject back into the system as a new campaign
        //redirect("http://report-site.com/campclick/verizoninjector");
        else
            redirect("http://www.facebook.com/cheapdeals4me");
        exit;
    }
    

    public function shape($io = "", $is_geo = "N", $crap = "")	{
        $shapeResult = false;
        $this->Trafficshape_model->io = $io;
        $shapeResult = $this->Trafficshape_model->process_click($io);

        if ($shapeResult !== false) {
            $this->Campclick_model->io = $io;
            $link = $this->Campclick_model->select_random_link_improved();

            if ($link !== false)	{
                $this->redirect($io, $link['counter'], $link['link_id'], $crap);
                exit;
            }
        }

        // bleed off campaign clicks to other campaigns
        if ($is_geo == "N") {
            //redirect("http://report-site.com/campclick/reinjector");
            redirect("http://www.facebook.com/cheapdeals4me");
        } else {
            redirect("http://www.facebook.com/cheapdeals4me");
        }
        exit;
    }

    public function summary($stime = "", $etime = "")	{
        $this->require_auth();

        $user = $this->ion_auth->user()->row();
        if($this->ion_auth->is_admin())
            $userid = '';
        else
            $userid = $user->id;

        $this->session->set_userdata("domain_name", $user->domain_name);

        $this->viewArray['campaigns'] = $this->Campclick_model->campaign_summary($userid, $stime, $etime);
        //print_r($this->viewArray['campaigns']); exit;
        $this->viewArray['domain_name'] = $this->session->userdata("domain_name");

        $this->parser->parse("campclick/campsummary.php", $this->viewArray);
    }

    public function get_hosts($io = "")
    {
        $this->Campclick_model->get_hosts($io);
        die('done');
    }

    public function report($io = "", $offset = 0, $range = "hour", $start_date = "", $end_date = "")	{
        $this->require_auth();

        $this->viewArray['io'] = $io;

        $this->load->library('pagination');
        $config['base_url'] = base_url()."campclick/report/$io";
        $config['num_links'] = 4;
        $config['uri_segment'] = 4;
        $config['per_page'] = 20;
        $config['total_rows'] = $this->Campclick_model->get_all_data($io, $range, '', '', '', '', '', true);
        $this->pagination->initialize($config);
        $this->viewArray['pagination_link'] = $this->pagination->create_links();
        $this->viewArray['domain_name'] = $this->session->userdata("domain_name");
        $this->viewArray['range'] = $range;
        $this->viewArray['start_date'] = $start_date;
        $this->viewArray['end_date'] = $end_date;
        $this->viewArray['offset'] = (int)$offset;

        $this->viewArray['report'] = $this->Campclick_model->report_by_io($io, $range, $start_date, $end_date);
        $this->viewArray['all_data'] = $this->Campclick_model->get_all_data($io, $range, $start_date, $end_date,'',$offset,$config['per_page'], false);
        //$this->Campclick_model->get_click_count_by_hour('2013-03-05');
        $this->viewArray['moreinfo_url'] = current_url();

        $this->Campclick_model->io = $io;
        $id = $this->Campclick_model->get_campaign_id_from_io();
        $this->Campclick_model->id = $id;
        $this->viewArray['campaign'] = $this->Campclick_model->get_campaign();

        $this->parser->parse('campclick/report.php', $this->viewArray);
    }

    /*
    public function date_range_report($io = "", $start_date = "", $end_date ="",$offset=0)	{
    	$this->require_auth();
    	
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
		$this->viewArray['domain_name'] = $this->session->userdata("domain_name");
		
		
    	$this->viewArray['report'] = $this->Campclick_model->report_by_io($io, 'daterange', $start_date, $end_date);
		$this->viewArray['all_data'] = $this->Campclick_model->get_all_data($io, 'daterange',  $start_date, $end_date, '', $offset, $config['per_page'], false);
		$this->Campclick_model->get_click_count_by_hour('2013-03-05');
    	
		$this->parser->parse('campclick/date_range_report.php', $this->viewArray);
    }
    */

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

    public function moreinfo($io = "", $counter = 0, $offset = 0, $range = "hour", $start_date = "", $end_date = "")	{
        $this->require_auth();

        $this->viewArray['io'] = $io;
        $this->viewArray['counter'] = $counter;
        $this->load->library('pagination');
        $config['base_url'] = base_url()."campclick/moreinfo/$io/$counter";
        $config['num_links'] = 4;
        $config['uri_segment'] = 5;
        $config['per_page'] = 20;
        $config['total_rows'] = $this->Campclick_model->get_all_data($io, $range, $start_date, $end_date, $counter, '', '', true);
        $this->pagination->initialize($config);
        $this->viewArray['pagination_link'] = $this->pagination->create_links();
        $this->viewArray['domain_name'] = $this->session->userdata("domain_name");
        $this->viewArray['sDate'] = $start_date;
        $this->viewArray['eDate'] = $end_date;
        $this->viewArray['offset'] = (int)$offset;
        $this->viewArray['range'] = $range;

        $this->viewArray['report'] = $this->Campclick_model->report_by_io_counter($io, $range, $start_date, $end_date, $counter);
        $this->viewArray['all_data'] = $this->Campclick_model->get_all_data($io, $range, $start_date, $end_date, '', $offset, $config['per_page'], false);
        $this->parser->parse('campclick/moreinfo.php', $this->viewArray);
    }

    public function raw_data($io = "", $counter = 0, $offset=0, $range = "hour", $start_date = "", $end_date = "")	{
        $this->require_auth();

        $this->viewArray['io'] = $io;
        $this->viewArray['counter'] = (int)$counter;
        $this->viewArray['offset'] = (int)$offset;
        $this->viewArray['range'] = $range;

        $this->load->library('pagination');
        $config['base_url'] = base_url()."campclick/raw_data/$io/$counter";
        $config['num_links'] = 4;
        $config['uri_segment'] = 5;
        $config['per_page'] = 20;
        $config['total_rows'] = $this->Campclick_model->get_all_data($io, $range,'', '', $counter, '', '', true);
        $this->pagination->initialize($config);
        $this->viewArray['pagination_link'] = $this->pagination->create_links();
        $this->viewArray['all_data'] = $this->Campclick_model->get_all_data($io, $range, $start_date, $end_date, $counter, $offset, $config['per_page'], false);
        $this->viewArray['domain_name'] = $this->session->userdata("domain_name");

        $this->viewArray['sDate'] = $start_date;
        $this->viewArray['eDate'] = $end_date;
        $this->viewArray['offset'] = (int)$offset;
        $this->viewArray['range'] = $range;

        $this->parser->parse('campclick/raw_data.php', $this->viewArray);

    }

    function raw_data2_date_range($io = "", $counter = 0,$sDate="", $eDate="",$offset=0){
        $this->require_auth();

        $this->viewArray['sDate'] = $sDate;
        $this->viewArray['eDate'] = $eDate;
        $this->viewArray['io'] = $io;
        $this->viewArray['counter'] = $counter;

        $this->load->library('pagination');
        $config['base_url'] = base_url()."campclick/raw_data2_date_range/$io/$counter/$sDate/$eDate";
        $config['num_links'] = 4;
        $config['uri_segment'] = 7;
        $config['per_page'] = 20;
        $config['total_rows'] = $this->Campclick_model->get_all_data_host($io, 'daterange', $sDate, $eDate, $counter, '', '', true);
        //die($config['total_rows']);
        $this->pagination->initialize($config);
        $this->viewArray['pagination_link'] = $this->pagination->create_links();
        $this->viewArray['all_data'] = $this->Campclick_model->get_all_data_host($io, 'daterange', $sDate, $eDate, $counter,$offset,$config['per_page'], false);
        $this->viewArray['domain_name'] = $this->session->userdata("domain_name");
        $this->parser->parse('campclick/raw_data2_date_range.php', $this->viewArray);
    }

    function export_raw_data($io = "", $counter = 0){
        $this->require_auth();

        $this->viewArray['io'] = $io;
        $this->viewArray['counter'] = $counter;
        $date_range = date('Y-m-d h:i:s',strtotime("-1 day"));
        $timestamp_sql = " c.timestamp > '{$date_range}' ";
        $from = " FROM campclick_clicks c ";

        $counter_sql = ($counter!="")?" AND c.link_id=l.link_id AND l.counter={$counter}":'';
        $from .= ($counter!="")?', campclick_links l ':'';

        $r = $this->db->query("SELECT c.ip_address,c.timestamp,c.web_browser,c.platform $from WHERE  $timestamp_sql AND c.io='{$io}' $counter_sql");

        $all_data = $r->result_array();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');

        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');

        // output the column headings
        fputcsv($output, array('IP Address', 'Date-Time', 'Browser', 'Platform'));


        if(count($all_data>0)){

            foreach($all_data as $row){
                fputcsv($output, str_replace('.bidvalidation.com','.ppc-host.com',$row));
            }

        }
    }

    function export_raw_data_month($io = "", $counter = 0,$offset=0){
        $this->require_auth();

        $this->viewArray['io'] = $io;
        $this->viewArray['counter'] = $counter;

        $date_range = date('Y-m-d h:i:s',strtotime("-30 day"));
        $timestamp_sql = " c.timestamp > '{$date_range}' ";
        $from = " FROM campclick_clicks c ";
        $counter_sql = ($counter!="")?" AND c.link_id=l.link_id AND l.counter={$counter}":'';
        $from .= ($counter!="")?', campclick_links l ':'';

        $r = $this->db->query("SELECT c.ip_address,c.timestamp, c.web_browser, c.platform  $from WHERE  $timestamp_sql AND c.io='{$io}' $counter_sql");

        $all_data = $r->result_array();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');

        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');

        // output the column headings
        fputcsv($output, array('IP Address', 'Date-Time', 'Browser', 'Platform'));
        if(count($all_data>0)){

            foreach($all_data as $row){
                fputcsv($output, str_replace('.bidvalidation.com','.ppc-host.com',$row));
            }

        }
    }

    function export_raw_data_date_range($io = "", $counter = 0,$sDate="", $eDate="",$offset=0){
        $this->require_auth();

        $this->viewArray['sDate'] = $sDate;
        $this->viewArray['eDate'] = $eDate;
        $this->viewArray['io'] = $io;
        $this->viewArray['counter'] = $counter;

        $this->viewArray['io'] = $io;
        $this->viewArray['counter'] = $counter;
        $timestamp_sql = " c.timestamp BETWEEN '{$sDate} 00:00:00' AND  '{$eDate} 23:59:59' ";
        $from = " FROM campclick_clicks c ";

        $counter_sql = ($counter!="")?" AND c.link_id=l.link_id AND l.counter={$counter}":'';
        $from .= ($counter!="")?', campclick_links l ':'';


        $r = $this->db->query("SELECT c.ip_address,c.timestamp, c.web_browser, c.platform  $from WHERE  $timestamp_sql AND c.io='{$io}' $counter_sql");

        $all_data = $r->result_array();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');

        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');

        // output the column headings
        fputcsv($output, array('IP Address', 'Date-Time', 'Browser', 'Platform'));
        if(count($all_data>0)){

            foreach($all_data as $row){
                fputcsv($output, str_replace('.bidvalidation.com','.ppc-host.com',$row));
            }

        }
    }

    public function campcreate()	{
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

        $this->parser->parse('campclick/message.php', $this->viewArray);
    }

    public function index()	{
        $this->require_auth();

        $user = $this->ion_auth->user()->row();
        if($this->ion_auth->is_admin())
            $userid = '';
        else
            $userid = $user->id;

        $this->session->set_userdata("domain_name", $user->domain_name);
        $this->viewArray['domain_name'] = $this->session->userdata("domain_name");

        $this->parser->parse('campclick/camplist.php', $this->viewArray);
    }

    public function archive($io = "")	{
        $this->Campclick_model->io = $io;
        $retval = $this->Campclick_model->archive();
        if ($retval === true)	{
            print json_encode(array("status"=>"SUCCESS"));
        } else {
            print json_encode(array("status" => "ERROR"));
        }
        exit;
    }

    public function check_io($io = "")	{
        $this->Campclick_model->io = $io;
        $io = $this->Campclick_model->get_campaign_id_from_io();
        if ($io > 0)	{
            print json_encode(array("status"=>"ERROR")); // io exists
        } else {
            print json_encode(array("status"=>"SUCCESS")); // io doesnt exist
        }
        exit;
    }

    public function redirect($io = "", $counter = 0, $is_random = false, $misc = "")	{
        if ($io == "14716-E7")  {
            redirect("/campclick/verizoninjector");
            exit;
        }

        $this->load->library("fraudfiltering");
        $this->fraudfiltering->ipaddress = $this->input->ip_address();
        $this->fraudfiltering->io = $io;
        $this->fraudfiltering->counter = $counter;
        $this->fraudfiltering->referral = $this->agent->referrer();
        $is_fraud = $this->fraudfiltering->checkFraud();

        if ($is_fraud === true && !in_array($this->input->ip_address(), $this->no_fraud))	{
            $link = $this->Campclick_model->get_link($io, $counter);
            $this->Campclick_model->log_click($io, $link['link_id'], true);
            print "Fraud Detected; {$this->input->ip_address()}";
            exit;
        }

        $link = $this->Campclick_model->get_link($io, $counter);
        $this->Campclick_model->log_click($io, $link['link_id']);

        $cookie = array(
            "name" => "trafficPingTracker",
            "value" => $io,
            "expire" => 1825*86400,
            "domain" => ".report-site.com",
            "path" => "/",
        );

        // adwords user mapping code
        $this->Campclick_model->io = $io;
        $vertical = $this->Campclick_model->get_vertical_by_io();
        $arrIoList = $this->Userlist_io_model->get_userlist_from_io($io);

        // if io list doesnt exist, create a new one
        if(empty($arrIoList)){
            $arrIoList = $this->createIoList($io);
        }
        $arrIoList = isset($arrIoList[0])?$arrIoList[0]:array();
        $arrVerticalList = $this->Userlist_vertical_model->get_userlist_from_vertical($vertical);

        // if vertical list doesnt exist, create a new one
        if(empty($arrVerticalList)){
            $arrVerticalList = $this->createVerticalList($vertical);
        }

        $arrVerticalList = isset($arrVerticalList[0])?$arrVerticalList[0]:array();
        $verticalScriptTag = html_entity_decode($arrVerticalList['sniped_code']);
        $ioScriptTag = html_entity_decode($arrIoList['sniped_code']);
        // end adwords user mapping code

        set_cookie($cookie);
        header("Referer: http://report-site.com/c/{$io}/0");
        //$open_pixel = "http://ad.doubleclick.net/ad/N7072.287297TAKE5SOLUTIONS.COM/B8080257.108952051;sz=1x1;ord=[timestamp]?";
        $open_pixel = "";

        if ($_SERVER['REMOTE_ADDR'] == "76.110.217.139" || $_SERVER['REMOTE_ADDR'] == "")       {
            /*
            $link['dest_url'] = "";
            $redirectTime = 100;
            print_r($io);
            print_r($vertical);
            */
            $redirectTime = 0;
        } else {
            $redirectTime = 1;
        }

        $javascriptGA = "
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-54038238-1', 'auto');    
  ga('send', 'pageview');               

</script>
";
        print "<html><head><meta http-equiv='refresh' content=\"{$redirectTime};URL='{$link['dest_url']}'\"></head><body>{$open_pixel}{$ioScriptTag}{$verticalScriptTag}{$javascriptGA}</body></html>";
        exit;
    }

    public function fix_database()	{
        $r = $this->db->query("SELECT * FROM campclick_campaigns");
        foreach($r->result_array() as $c)	{
            $this->Domains_model->id = (int)$c['domain'];
            $domain = $this->Domains_model->get_domain();
            $io = $c['io'];
            $message = $c['message'];

            //
            // Parse out the links, replace with our click tracking code.
            //
            $counter = 0;
            $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
            if (preg_match_all("/$regexp/siU", $message, $matches, PREG_SET_ORDER)) {
//				$counter = 0;
                foreach($matches as $match) {
                    // $match[2] = link address
                    // $match[3] = link text

                    // create the click/dest links
                    $this->Campclick_model->create_links($match[2], $io, $counter);

                    print "{$io}\n\t{$match[2]}-{$counter}\n";

                    $link = "http://{$domain['name']}/c/{$io}/{$counter}";
                    $message = str_ireplace($match[2], $link, $message);
                    $counter++;
                }
            }

            //$regexp = "<area\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
            //$regexp = "<area\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/area>";
            $regexp = "<area\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>";
            if (preg_match_all("/$regexp/siU", $message, $matches, PREG_SET_ORDER)) {
                foreach($matches as $match) {
                    // $match[2] = link address
                    // $match[3] = link text

                    // create the click/dest links
                    $this->Campclick_model->create_links($match[2], $io, $counter);

                    print "{$io}\n\t{$match[2]}-{$counter}\n";

                    $link = "http://{$domain['name']}/c/{$io}/{$counter}";
                    $message = str_ireplace($match[2], $link, $message);
                    $counter++;
                }
            }
        }
    }

    public function generate_code()	{
        $user = $this->ion_auth->user()->row();

        if ($this->ion_auth->is_admin())	{
            $user_id = "5"; // was previously ""
        } else {
            $user_id = $user->id;
        }

        $message = $this->input->post("message");
        $io = $this->input->post("io");
        $name = $this->input->post("name");
        $domain = $this->input->post("domain");
        $is_geo = $this->input->post("is_geo");
        $vendor_id = $this->input->post("vendor");

        $this->Campclick_model->io = $io;
        $this->Campclick_model->name = $name;
        $this->Campclick_model->message = $message;
        $this->Campclick_model->domain = $domain;
        $this->Campclick_model->is_geo = $is_geo;
        $this->Campclick_model->vendor_id = $vendor_id;
        $this->Campclick_model->userid = $user_id;
        $this->Campclick_model->max_clicks = (int)$this->input->post("max_clicks");
        $this->Campclick_model->campaign_start_datetime = date("Y-m-d H:i:s", strtotime($this->input->post("campaign_start_datetime")));
        $this->Campclick_model->is_traffic_shape = $this->input->post("is_traffic_shape");
        $this->Campclick_model->ppc_network = "FIQ"; // HARD CODED - FOR NOW
        $this->Campclick_model->opens = (int)$this->input->post("opens");
        $this->Campclick_model->impression_clicks = 0;
        $this->Campclick_model->fire_open_pixel = "N";
        $campId = $this->Campclick_model->create();

        /*
         * This is our traffic shaping algorithm.
         * Its not pretty, but it works for tracking the campaign in duration days
         *
         */
        if ($this->Campclick_model->is_traffic_shape == "Y")  {
            $this->Trafficshape_model->io = $io;
            $this->Trafficshape_model->campaign_duration_days = 4;
            $this->Trafficshape_model->campaign_max_clicks = $this->Campclick_model->max_clicks;
            $this->Trafficshape_model->standard_deviation = 0.25; // change this to fluctuate as needed
            //$this->Trafficshape_model->start_click_offset = ($this->Campclick_model->max_clicks * 0.10); // pre-load 10% of the clicks on the first sample point
            $this->Trafficshape_model->create();
        }

        $this->Domains_model->id = (int)$domain;
        $domain = $this->Domains_model->get_domain();

        $parsedLinks = $this->parse_html_for_links($message);

        $postProcess = array();

        $counter = 1;
        $finalMessage = $message; // copy message to final message so we keep original around, just in case
        foreach($parsedLinks as $l)	{

            // skip if we have a blank url
            if ($l['href'] == "")
                continue;

            // redirect link
            $link = "http://{$domain['name']}/c/{$io}/{$counter}";
            $this->Campclick_model->create_links($l['href'], $io, $counter);

            $urlPath = parse_url($l['href'], PHP_URL_PATH);
            if ($urlPath == "/")	{
                $postProcess[] = array(
                    "href" => $l['href'],
                    "redir" => $link
                );
            } else {
                // update the message
                $finalMessage = str_replace($l['href'], $link, $finalMessage);
            }

            $counter++;
        }

        if (! empty($postProcess))	{
            foreach($postProcess as $l)	{
                $finalMessage = str_replace($l['href'], $l['redir'], $finalMessage);
            }
        }

        // create the "Default" dest link
        $this->Campclick_model->create_links($this->input->post("default_url"), $io, 0);

        print json_encode(array(
            "status" => "SUCCESS",
            "message" => $finalMessage,
            "orig_message" => $message,
            "url" => "http://{$domain['name']}/c/{$io}/0"
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

        $this->Campclick_model->io = $io;
        if ($this->Campclick_model->get_campaign() === false)	{
            // campaign doesnt exist, we need to create it
            $this->Campclick_model->name = $io;
            $this->Campclick_model->io = $io;
            $this->Campclick_model->message  = "";
            $this->Campclick_model->conversion_tracking = "N";
            $this->Campclick_model->create();
        }

        $file = file_get_contents("/var/www/LINKS/{$io}-LINKS.txt");
        $lines = explode("\n", $file);

        $cnt=1;
        $total_clicks = 0;
        foreach($lines as $l)	{
            list($url, $clicks) = explode("\t", $l);

            if ($url != "")	{
                $this->Campclick_model->create_links($url, $io, $cnt, $clicks);
                $total_clicks += $clicks;
                $cnt++;
            }
        }

        print "Total Expected Clicks: {$total_clicks}<br/>";
        print "Total Links Created: {$cnt}";
    }

    public function support_request()	{
        $user = $this->ion_auth->user()->row();
        $this->Campclick_model->io = $this->input->post("io");
        $this->Campclick_model->note = $this->input->post("notes");
        $this->Campclick_model->email = $user->email;
        $this->Campclick_model->send_support_request();
        print json_encode(array("status"=>"SUCCESS"));
    }

    public function map($io = "")	{
        $this->Campclick_model->io = $io;
        $stats = $this->Campclick_model->get_quick_stats_by_io();
        $this->viewArray['io'] = $io;
        $this->viewArray['total_clicks'] = $stats['total_clicks'];
        $this->viewArray['unique_clicks'] = $stats['unique_clicks'];
        $this->parser->parse("campclick/maps.php", $this->viewArray);
    }

    public function map_ajax()	{
        $this->Campclick_model->io = $this->input->post("io");
        print json_encode(array("status"=>"SUCCESS", "data"=>$this->Campclick_model->get_ip_geolocate()));
    }

    public function domains()	{
        $this->require_auth();

        $user = $this->ion_auth->user()->row();
        if($this->ion_auth->is_admin())
            $userid = '5';
        else
            $userid = $user->id;

        $this->viewArray['domains'] = $this->Domains_model->get_domain_list($userid);
        $this->viewArray['ipaddress'] = "65.60.43.112"; // hard coded ip address
        $this->viewArray['domain_name'] = $this->session->userdata("domain_name");

        $this->parser->parse('campclick/domains.php', $this->viewArray);
    }

    public function domain_delete()	{
        $this->Domains_model->id = (int)$this->input->post("id");
        $this->Domains_model->delete();
        print json_encode(array("status"=>"SUCCESS"));
    }

    public function domain_create()	{
        $this->require_auth();

        $this->Domains_model->name = $this->input->post("name");
        $this->Domains_model->is_active = "Y";

        $user = $this->ion_auth->user()->row();

        if($this->ion_auth->is_admin())
            $this->Domains_model->user_id = 5;
        else
            $this->Domains_model->user_id = $user->id;

        $id = $this->Domains_model->create();
        print json_encode(array("status"=>"SUCCESS", "domain_id"=>$id));
    }

    public function navtree()	{
        $this->load->model("Organization_model");
        $this->require_auth();

        $user = $this->ion_auth->user()->row();

        $data = array();

        if($this->ion_auth->is_admin())
            $user_id = 5;
        else
            $user_id = $user->id;

        $this->Organization_model->user_id = $user_id;
        $nodes = $this->Organization_model->get_nodes();

        foreach($nodes as $n)	{
            $thisNode = array(
                "data" => $n['name'],
                "attr" => array("id" => $n['id']),
                "state" => "open",
            );

            $this->Organization_model->node_id = (int)$n['id'];
            $leafs = $this->Organization_model->get_leafs();
            foreach($leafs as $l)	{
                $thisNode['children'] = array(
                    "data" => $l['name'],
                    "attr" => array("id" => $l['id']),
                    "state" => "open"
                );
            }

            $data[] = $thisNode;
        }

        print json_encode($data);
    }

    public function vendors()	{
        $this->require_auth();

        $user = $this->ion_auth->user()->row();
        if($this->ion_auth->is_admin())
            $userid = '5';
        else
            $userid = $user->id;

        $this->viewArray['vendors'] = $this->Vendor_model->get_vendor_list($userid);
        $this->viewArray['ipaddress'] = "65.60.43.112"; // hard coded ip address
        $this->viewArray['domain_name'] = $this->session->userdata("domain_name");

        $this->parser->parse('campclick/vendors.php', $this->viewArray);
    }

    public function vendor_delete()	{
        $this->Vendor_model->id = (int)$this->input->post("id");
        $this->Vendor_model->delete();
        print json_encode(array("status"=>"SUCCESS"));
    }

    public function vendor_create()	{
        $this->require_auth();

        $this->Vendor_model->name = $this->input->post("name");
        $this->Vendor_model->email = $this->input->post("email");
        $this->Vendor_model->is_active = "Y";

        $user = $this->ion_auth->user()->row();

        if($this->ion_auth->is_admin())
            $this->Vendor_model->user_id = 5;
        else
            $this->Vendor_model->user_id = $user->id;

        $id = $this->Vendor_model->create();
        print json_encode(array("status"=>"SUCCESS", "domain_id"=>$id));
    }

    public function fulfillment($stime = "", $etime = "")	{
        $this->require_auth();

        $user = $this->ion_auth->user()->row();
        if($this->ion_auth->is_admin())
            $userid = '5';
        else
            $userid = $user->id;

        $this->session->set_userdata("domain_name", $user->domain_name);

        $this->viewArray['campaigns'] = $this->Campclick_model->fulfillment_summary($userid, $stime, $etime);
        //print_r($this->viewArray['campaigns']); exit;
        $this->viewArray['domain_name'] = $this->session->userdata("domain_name");

        $this->parser->parse("campclick/fulfillment.php", $this->viewArray);
    }

    public function bidadjust($stime = "", $etime = "")	{
        $this->require_auth();

        $user = $this->ion_auth->user()->row();
        if($this->ion_auth->is_admin())
            $userid = '5';
        else
            $userid = $user->id;

        $this->session->set_userdata("domain_name", $user->domain_name);

        $this->viewArray['campaigns'] = $this->Campclick_model->fulfillment_summary($userid, $stime, $etime, true);
        $this->viewArray['domain_name'] = $this->session->userdata("domain_name");

        //print_r($this->viewArray['campaigns']);

        $this->parser->parse("campclick/bidadjustment.php", $this->viewArray);
    }

    public function schedule($is_active = "N", $stime = "", $etime = "")	{
        $this->require_auth();

        $user = $this->ion_auth->user()->row();
        if($this->ion_auth->is_admin())
            $userid = '5';
        else
            $userid = $user->id;

        $this->session->set_userdata("domain_name", $user->domain_name);

        $this->viewArray['campaigns'] = $this->Campclick_model->schedule_summary($userid, $stime, $etime, $is_active);
        //print_r($this->viewArray['campaigns']); exit;
        $this->viewArray['domain_name'] = $this->session->userdata("domain_name");

        $this->parser->parse("campclick/report_schedule.php", $this->viewArray);
    }

    public function test()	{
        $file = file_get_contents("/tmp/12393-clicks.txt");

        $lines = explode("\n", $file);

        foreach($lines as $l)	{
            $cols = explode("\t", $l);

            $link_id = rand(42560, 42587);
            $io_list = array('12393BRAD', '12393BRIAN', '12393WENDY', '12393MARK', '12393KYLE', '12393RICK', '12393ROB', '12393JERRY', '12393MARK', '12393RICK');
            $io = array_rand($io_list, 1);

            $insert = array(
                "ip_address" => $cols[0],
                "user_agent" => $cols[1],
                "timestamp" => $cols[2],
                "is_mobile" => "N",
                "web_browser" => $cols[4],
                "mobile_device" => $cols[5],
                "platform" => $cols[6],
                "referrer" => "",
                "link_id" => $link_id,
                "io" => $io_list[$io],
                "is_geo" => "N",
                "is_fraud" => "N",
                "geo_lat" => 0,
                "geo_lon" => 0,
            );

            print_r($insert);

            $this->db->insert("campclick_clicks", $insert);
            print mysql_error();
        }
    }

    public function parse_html_for_links($html_string = "")	{
        //$html_string = file_get_contents("http://www.jimshorkey.com");

        libxml_use_internal_errors(true);

        $doc = new DOMDocument();
        $doc->loadHTML($html_string);

        $links = array();

        $linksAREA = $this->dom_parser($doc->getElementsByTagName("area"));
        $linksA = $this->dom_parser($doc->getElementsByTagName("a"));

        return array_merge($linksA, $linksAREA);
    }

    private function dom_parser($arr = "")	{
        $links = array();

        foreach($arr as $item) {
            $href =  $item->getAttribute("href");
            $text = trim(preg_replace("/[\r\n]+/", " ", $item->nodeValue));

            // ignore in-page markers
            if ($href == "#")
                continue;

            //
            // THIS IS BROKEN! DO NOT USE AS IT WONT PASS THRU URLS WITH & or ? IN THEM!
            //	if (! preg_match("%^((https?://)|(www\.))([a-z0-9-].?)+(:[0-9]+)?(/.*)?$%i", $href))
            //		continue;
            //

            $links[] = array(
                'href' => $href,
                'text' => $text
            );
        }

        return $links;
    }

    public function conversioncpa()	{
        $insert = array(
            "io" => $this->input->post("io"),
            "data" => serialize($this->input->post())
        );

        $this->db->insert("conversions", $insert);

        print json_encode(array("status" => "SUCCESS"));
    }

    public function get_message()	{
        $this->Campclick_model->io = $this->input->post("io");
        $id = $this->Campclick_model->get_campaign_id_from_io();
        $this->Campclick_model->id = $id;

        print json_encode(array("status"=>"SUCCESS", "campaign" => $this->Campclick_model->get_campaign()));
    }

    public function geolocation($myio = "")  {
        //$this->load->model("api_model");
        //$ads = $this->api_model->get_all_ads();

        $selectedAdID = "";

        $ads = $this->Finditquick_model->get_all_ads();

        $adList = array();
        foreach($ads as $a)    {
            list($io, $therest) = explode("-", $a->Ad->campaign_name);
            $adList[] = array("id" => $a->Ad->id, "name" => $a->Ad->campaign_name);

            if (trim($io) == trim($myio))
                $selectedAdID = $a->Ad->id;
        }

        $newSort = array();
        foreach($adList as $key => $row)   {
            $newSort[$key] = $row['name'];
        }
        array_multisort($newSort, SORT_DESC, $adList);

        $this->viewArray['ads'] = $adList;
        $this->viewArray['io'] = $myio;
        $this->viewArray['adid'] = $selectedAdID;

        $this->parser->parse("campclick/geolocation.php", $this->viewArray);
    }

    public function geolocation_ajax() {
        $zipcodes = array_map('trim',explode(" ",$this->input->post("zip")));

        //$zipcodes = explode(" ", $this->input->post("zip"));

        $resultGeo = array();
        $source_locations = array();

        foreach($zipcodes as $zip) {
            if ($zip == "" || $zip == "undefined")
                continue;

            $r = $this->Zip_model->find_locations($zip, $this->input->post("radius"));
            $resultGeo = array_merge($resultGeo, $r);

            $r = $this->Zip_model->match_zip_to_geo($zip, $this->input->post("radius"));
            $source_locations[] = $r;
        }

        print json_encode(array("status" => "SUCCESS", "locations" => $resultGeo, "source_location" => $source_locations));
    }

    public function geolocation_ad($io = "")    {
        $zipcode = $this->Zip_model->get_campaign_zipcode($io);
        $radius = $this->Zip_model->get_campaign_radius($io);
        print json_encode(array("status" => "SUCCESS", "zip" => $zipcode, "radius" => $radius));
    }

    public function clickmap_iframe()  {
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

        $this->parser->parse("campclick/clickmap_iframe.php", $this->viewArray);
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

    public function set_schedule($id = "") {
        $this->Finditquick_model->set_schedule($id, ""); // we keep the schedule blank as it will give us 8AM-Midnight clicks
        print json_encode(array("status" => "SUCCESS", "message" => "Schedule Cleared"));
    }

    public function set_bid($id = "", $bump = 1, $bump_amount = 0.0001)  {
        //$this->load->model("api_model");
        //$ads = $this->api_model->get_ad($id);

        $ads = $this->Finditquick_model->get_ad($id);
        $ron_bid = $ads[0]->Keywords->ron_bid;

        if (! $ron_bid > 0)  {
            print json_encode(array("status" => "ERROR", "message" => "Invalid AD ID"));
            exit;
        }

        $new_bid = $ron_bid + ($bump * $bump_amount);

        $this->Finditquick_model->set_bid($id, $new_bid);

        print json_encode(array("status" => "SUCCESS", "message" => "Bid Updated to $ {$new_bid}"));
    }

    public function set_cap($id = "", $cap_amount = 8.00)    {
        //$this->load->model("api_model");
        //$this->api_model->set_cap($id, $cap_amount);

        $this->Finditquick_model->set_cap($id, $cap_amount);
        print json_encode(array("status" => "SUCCESS", "message" => "Cap Updated to $ {$cap_amount}"));
    }

    public function set_target()   {
        //$this->load->model("api_model");
        //$this->api_model->set_target($this->input->post("id"), $this->input->post("targets"));

        $io = $this->input->post("io");

        $this->Log_model->io = $io;
        $this->Log_model->action = "CAMPAIGN_GEO_ADJUSTMENT";
        $this->Log_model->note = "GEO Adjustment for {$io}. Radius: {$this->input->post('radius')}mi. Zip List: {$this->input->post('zip')}";
        $this->Log_model->create();

        // update the campaign "template" for use later on.
        $this->Zip_model->set_campaign_radius($this->input->post("radius"), $io);
        $this->Zip_model->set_campaign_ziplist($this->input->post("zip"), $io);

        $this->Finditquick_model->set_target($this->input->post("id"), $this->input->post("targets"));
        print json_encode(array("status" => "SUCCESS", "message" => "Targets Updated"));
    }

    public function reinjector() {
        $this->load->library("Mobile_Detect");
        $detect = new Mobile_Detect();

        // is this traffic mobile?? if so, redirect it to mobile consumer
        if ($detect->isMobile())   {
            $this->mobile(); // send over to mobile method for consumption
            exit;
        } else {
            // lets see what geo this is from
            $geo = file_get_contents("http://api.hostip.info/get_json.php?ip={$this->input->ip_address()}&position=true");
            $geo = json_decode($geo);

            // unknown location- we'll just throw this into a nationwide campaign
            if (strtoupper($geo->country_code) == "XX" || strtoupper($geo->country_code) == "US")    {
                $inprogress = $this->Campclick_model->get_campaign_list(null, "Y", "AND campaign_is_started='Y' AND campaign_is_complete='N' AND is_geo='N'");

                if (! empty($inprogress))  {
                    $selected_element = array_rand($inprogress, 1);

                    $io = $inprogress[$selected_element];

                    $this->Campclick_model->io = $io['io'];
                    $linkInfo = $this->Campclick_model->select_random_link_improved();
                    $link = $this->Campclick_model->get_link($io['io'], $linkInfo['counter']);

                    if (stripos($link['dest_url'], "mailto") === false && stripos($link['dest_url'], "facebook") === false && stripos($link['dest_url'], "twitter") === false && $link['link_id'] !== "")  {
                        //redirect("http://report-site.com/r/{$io['io']}/1");
                        exit;
                    }
                }
            }
        }

        redirect("http://facebook.com/cheapdeals4me");
        exit;
    }

    public function mobile($redirect = "")   {
        $this->load->library('Mobile_Detect');
        $detect = new Mobile_Detect();

        if ($detect->isMobile())   {
            //redirect("http://www.safedatatech.com?utm_campaign=mobile-test&utm_medium=email&utm_source=report-site");
            //exit;

            // Lets try to figure out the GEO of the mobile click
            $geo = file_get_contents("http://api.hostip.info/get_json.php?ip={$this->input->ip_address()}&position=true");
            $geo = json_decode($geo);

            if ($geo->country_code == "US")    {
                list($city, $state) = explode(",", $geo->city);
                $state = trim($state);

                if ($state != "")  {
                    $match_zips = $this->Zip_model->match_city_to_zip($city, $state);

                    if (! empty($match_zips))   {
                        // zip specific match for campaign
                        $inprogress = $this->Zip_model->get_campaign_by_geo($match_zips);
                        log_message('error', "ZIP 2 CAMPAIGN MATCH: " . $geo->city);
                    }
                }
            } else {
                // this is NOT a US based mobile click (best we can tell) - do something with it
            }

            if (empty($inprogress))  {
                // country specific matching
                $inprogress = $this->Campclick_model->get_campaign_list(null, "Y", "AND campaign_is_started='Y' AND campaign_is_complete='N' AND is_geo='N'");
            }

            /**
             * If the mobile redirect matches below, then do a quick redirect to the proper place.
             */
            /*
            if ($redirect != "")   {
                switch($redirect)  {
                    case "1":
                        redirect("http://report-site.com/i/13827");
                        break;
                    case "2":
                        redirect("http://report-site.com/i/13827");
                        break;

                    case "3":
                        redirect("http://report-site.com/i/13872/true");
                        break;

                    case "4":
                        redirect("http://report-site.com/r/12703");
                        break;
                }
            }*/

            if (! empty($inprogress))  {
                $selected_element = array_rand($inprogress, 1);

                $io = $inprogress[$selected_element];

                $this->Campclick_model->io = $io['io'];
                $cap = $this->Campclick_model->get_current_click_cap();
                $campaign = $this->Campclick_model->get_campaign_by_io();


                /**
                 * we went over cap, redirect!
                 */
                if ($cap > $campaign['cap_per_hour'])  {
                    redirect("http://facebook.com/cheapdeals4me");
                    exit;
                }

                $linkInfo = $this->Campclick_model->select_random_link_improved();
                $link = $this->Campclick_model->get_link($io['io'], $linkInfo['counter']);

                if ($link === false)   {
                    redirect("http://www.report-site.com/campaigns/landing/index.php?NOMATCH=1");
                    exit;
                }

                if (stripos($link['dest_url'], "mailto") === false && stripos($link['dest_url'], "facebook") === false && stripos($link['dest_url'], "twitter") === false && $link['link_id'] !== "")  {
                    $this->Campclick_model->log_volume("mobile"); // log the ip -- we're going to track the volume of how fast this traffic is coming thru

                    //redirect("http://report-site.com/r/{$io['io']}");
                    header("Referer: http://report-site.com/r/{$io}");
                    print "<html><head><meta http-equiv='refresh' content=\"1;URL='http://report-site.com/r/{$io['io']}'\"></head><body>Redirecting...</body></html>";
                    exit;
                } else {
                    redirect("http://www.report-site.com/campaigns/landing/index.php?NOSOC=1");
                    exit;
                }
            }
        } else {
            redirect("http://www.report-site.com/campaigns/landing/index.php?NOMOB=1");
            exit;
        }
    }

    public function clone_io() {
        $success = $this->Campclick_model->clone_io($this->input->post("old_io"), $this->input->post("new_io"), $this->input->post("campaign_name"));

        if ($success === true) {
            print json_encode(array("status"=>"SUCCESS"));
        } else {
            print json_encode(array("status"=>"ERROR"));
        }
    }

    public function get_link() {
        $link = $this->Campclick_model->get_link_by_id($this->input->post("link_id"));

        print json_encode(array("status" => "SUCCESS", "link" => $link));
    }

    public function update_link($is_create = "")  {

        if ($is_create == "") {
            $this->Campclick_model->update_link($this->input->post("link_id"), $this->input->post("dest_url"), $this->input->post("max_clicks"), $this->input->post("fulfilled"));
        } else {
            $this->Campclick_model->create_new_link($this->input->post("dest_url"), $this->input->post("io"), $this->input->post("max_clicks"));
        }
        print json_encode(array("status" => "SUCCESS"));
    }

    public function bid_up($io = "")  {
        $this->Campclick_model->io = $io;
        $campaign = $this->Campclick_model->get_campaign_by_io();

        switch(strtoupper($campaign["ppc_network"]))   {
            case "FIQ":
                $ads = json_decode($this->Finditquick_model->get_ad($campaign['ppc_network_ad_id']));
                $ron_bid = $ads[0]->Keywords->ron_bid;
                $new_bid = $ron_bid + 0.0001;
                $this->Finditquick_model->set_bid($campaign['ppc_network_ad_id'], $new_bid);
                break;

            case "EZANGA":
                break;
        }

        if (! $ron_bid > 0)  {
            print json_encode(array("status" => "ERROR", "message" => "Invalid AD ID"));
            exit;
        }

        $this->Log_model->io = $io;
        $this->Log_model->action = "CAMPAIGN_BID_ADJUSTMENT";
        $this->Log_model->note = "PPC Bid Changed for {$io} from: {$ron_bid} to:" . $new_bid;
        $this->Log_model->create();

        $this->Ad_model->id = $campaign['local_ad_id'];
        $ad = $this->Ad_model->get_by_id();

        $this->Ad_model->bid = $new_bid;
        $this->Ad_model->id = $ad['id'];
        $this->Ad_model->set_bid();

        print json_encode(array("status" => "SUCCESS", "message" => "Bid Updated to $ {$new_bid}", "bid" => $new_bid));
    }

    public function bid_down($io = "")  {
        $this->Campclick_model->io = $io;
        $campaign = $this->Campclick_model->get_campaign_by_io();

        switch(strtoupper($campaign["ppc_network"]))   {
            case "FIQ":
                $ads = json_decode($this->Finditquick_model->get_ad($campaign['ppc_network_ad_id']));
                $ron_bid = $ads[0]->Keywords->ron_bid;
                $new_bid = $ron_bid - 0.0001;
                $this->Finditquick_model->set_bid($campaign['ppc_network_ad_id'], $new_bid);
                break;

            case "EZANGA":
                break;
        }

        if (! $ron_bid > 0)  {
            print json_encode(array("status" => "ERROR", "message" => "Invalid AD ID"));
            exit;
        }

        $this->Log_model->io = $io;
        $this->Log_model->action = "CAMPAIGN_BID_ADJUSTMENT";
        $this->Log_model->note = "PPC Bid Changed for {$io} from: {$ron_bid} to:" . $new_bid;
        $this->Log_model->create();

        $this->Ad_model->id = $campaign['local_ad_id'];
        $ad = $this->Ad_model->get_by_id();

        $this->Ad_model->bid = $new_bid;
        $this->Ad_model->id = $ad['id'];
        $this->Ad_model->set_bid();

        print json_encode(array("status" => "SUCCESS", "message" => "Bid Updated to $ {$new_bid}", "bid" => $new_bid));
    }

    public function cap_save() {
        $this->Campclick_model->io = $this->input->post("io");
        $campaign = $this->Campclick_model->get_campaign_by_io();

        switch(strtoupper($campaign['ppc_network']))   {
            case "FIQ":
                $this->Finditquick_model->set_cap($campaign['ppc_network_ad_id'], sprintf("%.2f", $this->input->post("cap")));
                $this->Finditquick_model->pause_ad($campaign['ppc_network_ad_id']);
                $this->Finditquick_model->resume_ad($campaign['ppc_network_ad_id']);
                break;

            case "EZANGA":
                break;
        }

        if ($campaign['ppc_network_ad_id'] == "")  {
            print json_encode(array("status" => "ERROR", "message" => "Legacy campaign or PPC Network ID not stored." ));
            exit;
        }

        $this->Ad_model->id = $campaign['local_ad_id'];
        $ad = $this->Ad_model->get_by_id();

        $this->Ad_model->daily_cap = $this->input->post("cap");
        $this->Ad_model->set_daily_cap();

        $this->Log_model->io = $io;
        $this->Log_model->action = "CAMPAIGN_DAILYCAP_ADJUSTMENT";
        $this->Log_model->note = "Daily Cap Changed for {$io} from: {$ad['daily_cap']} to:" . sprintf("%.2f", $this->input->post("cap"));
        $this->Log_model->create();

        print json_encode(array("status" => "SUCCESS", "message" => "OK", "cap" => sprintf("%.2f", $this->input->post("cap"))));
    }

    public function rolling_count($io = "", $duration = 10)    {
        $count = $this->Campclick_model->rolling_count($io, $duration);

        $this->Log_model->io = $io;
        $this->Log_model->action = "ROLLING_COUNT";
        $this->Log_model->note = "Rolling count {$io} for last {$duration}: {$count}";
        $this->Log_model->create();

        print json_encode(array("status" => "SUCCESS", "count" => $count));
    }

    public function campaign_pause($io = "")   {
        $this->Campclick_model->io = $io;
        $campaign = $this->Campclick_model->get_campaign_by_io();

        switch(strtoupper($campaign['ppc_network']))   {
            case "FIQ":
                $this->Finditquick_model->pause_ad($campaign['ppc_network_ad_id']);
                break;

            case "EZANGA":
                break;
        }

        if ($campaign['ppc_network_ad_id'] == "")  {
            print json_encode(array("status" => "ERROR", "message" => "Legacy campaign or PPC Network ID not stored." ));
            exit;
        }

        $this->Ad_model->id = $campaign['local_ad_id'];
        $ad = $this->Ad_model->get_by_id();

        $this->Ad_model->ppc_network_ad_active = "N";
        $this->Ad_model->set_ppc_network_ad_active();

        $this->Log_model->io = $io;
        $this->Log_model->action = "CAMPAIGN_PAUSE";
        $this->Log_model->note = "Daily Cap Changed for {$io} from: {$ad['daily_cap']} to:" . sprintf("%.2f", $this->input->post("cap"));
        $this->Log_model->create();

        print json_encode(array("status" => "SUCCESS", "message" => "OK" ));
    }

    public function campaign_resume($io = "")   {
        $this->Campclick_model->io = $io;
        $campaign = $this->Campclick_model->get_campaign_by_io();

        switch(strtoupper($campaign['ppc_network']))   {
            case "FIQ":
                $this->Finditquick_model->resume_ad($campaign['ppc_network_ad_id']);
                break;

            case "EZANGA":
                break;
        }

        if ($campaign['ppc_network_ad_id'] == "")  {
            print json_encode(array("status" => "ERROR", "message" => "Legacy campaign or PPC Network ID not stored." ));
            exit;
        }

        $this->Ad_model->id = $campaign['local_ad_id'];
        $ad = $this->Ad_model->get_by_id();

        $this->Ad_model->ppc_network_ad_active = "Y";
        $this->Ad_model->set_ppc_network_ad_active();

        $this->Log_model->io = $io;
        $this->Log_model->action = "CAMPAIGN_PAUSE";
        $this->Log_model->note = "Daily Cap Changed for {$io} from: {$ad['daily_cap']} to:" . sprintf("%.2f", $this->input->post("cap"));
        $this->Log_model->create();

        print json_encode(array("status" => "SUCCESS", "message" => "OK" ));
    }

    public function check_cap($io = "")    {
        $this->Campclick_model->io = $io;
        $campaign = $this->Campclick_model->get_campaign_by_io();

        if ($campaign['ppc_network_ad_id'] == "")  {
            print json_encode(array("status" => "ERROR", "message" => "Legacy campaign or PPC Network ID not stored." ));
            exit;
        }

        switch(strtoupper($campaign['ppc_network']))   {
            case "FIQ":
                $report = $this->Finditquick_model->get_ad_report($campaign['ppc_network_ad_id'], date("Y-m-d"));
                break;

            case "EZANGA":
                break;
        }

        if ($report != "") {
            $report = json_decode($report);
            $report = $report->{date("Y-m-d")};
        }

        print json_encode(array("status"=>"SUCCESS", "report" => $report));
    }

    public function clickcap($io = "", $click_cap = 100)   {
        $this->Campclick_model->io = $io;
        $campaign = $this->Campclick_model->get_campaign_by_io();

        $this->Campclick_model->cap_per_hour = (int)$click_cap;
        $this->Campclick_model->set_click_cap();

        $this->Log_model->io = $io;
        $this->Log_model->action = "CAMPAIGN_CLICKCAP";
        $this->Log_model->note = "Hourly Click Cap Changed for {$io} to: {$click_cap}";
        $this->Log_model->create();

        print json_encode(array("status" => "SUCCESS", "message" => "OK", "click_cap" => $click_cap ));
    }

    public function campaign_complete($io = "")   {
        $this->Campclick_model->io = $io;
        $campaign = $this->Campclick_model->get_campaign_by_io();

        // mark campaign as being completed.
        $this->Campclick_model->campaign_complete();

        // pause the ad on the PPC network
        switch(strtoupper($campaign['ppc_network']))   {
            case "FIQ":
                $this->Finditquick_model->pause_ad($campaign['ppc_network_ad_id']);
                break;

            case "EZANGA":
                break;
        }

        if ($campaign['ppc_network_ad_id'] == "")  {
            print json_encode(array("status" => "ERROR", "message" => "Legacy campaign or PPC Network ID not stored." ));
            exit;
        }

        $this->Ad_model->id = $campaign['local_ad_id'];
        $ad = $this->Ad_model->get_by_id();

        $this->Ad_model->ppc_network_ad_active = "N";
        $this->Ad_model->set_ppc_network_ad_active();


        $this->Log_model->io = $io;
        $this->Log_model->action = "CAMPAIGN_COMPLETE";
        $this->Log_model->note = "Campaign Marked Completed for IO: {$io}";
        $this->Log_model->create();

        print json_encode(array("status" => "SUCCESS", "message" => "OK" ));
    }

    public function verizoninjector($io = "", $pixelId = "")	{
        /*redirect("http://www.pathtoyourdegree.com");
        exit;*/

        /*
        $randCamp = array("VERIZON-MAR-E7", "VERIZON-MAR-E8");
        $io = $randCamp[array_rand($randCamp, 1)];

        if ($io == "VERIZON-MAR-E8")   {
            $pixelId = "1402";
        } else {
            $pixelId = "1403";
        }
        */

        $this->Campclick_model->io = $io;

        $this->Campclick_model->update_click_cap();
        $count = $this->Campclick_model->get_current_click_cap();

        $campaign = $this->Campclick_model->get_campaign_by_io();

        $weighted_percentage = ceil(($campaign['impression_clicks']/$campaign['opens']) * 100);

        $counter = mt_rand(0, $this->Campclick_model->get_link_count());
        //$link_counters = array("8", "9", "10", "11", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40", "41", "42", "43", "44", "45", "46", "47", "48", "49", "50", "51", "52", "53", "54", "55", "56", "57", "58", "59", "60", "61", "62", "63", "64", "65", "66", "67", "68", "69", "70", "71", "72", "73", "74", "75");
        //$counter = $link_counters[array_rand(array("8", "9", "10", "11", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40", "41", "42", "43", "44", "45", "46", "47", "48", "49", "50", "51", "52", "53", "54", "55", "56", "57", "58", "59", "60", "61", "62", "63", "64", "65", "66", "67", "68", "69", "70", "71", "72", "73", "74", "75"), 1)];

        $random_link = array();
        for($x=1; $x<=100; $x++)    { // this was 100
            if ($x <= $weighted_percentage) {
                // open+click link
                //$random_link[] = "?visitor=1";
                $random_link[] = "1";
            } else {
                // open-only link
                //$random_link[] = "?visitor=0&redirurl=" . urlencode("http://report-site.com/campclick/verizon");
                $random_link[] = "0";
            }
        }

        if ($pixelId == "")    {
            $pixels = $this->Take5_Pending_Campaign_Openpixel_Model->get_pixels($io);
            $pixelId = (int)$pixels[0]['id'];
        }

        if (! empty($random_link))  {
            $fallover_redirect = $random_link[array_rand($random_link, 1)];

            if ($fallover_redirect == "0") {
                //open-only
                $link = $this->Campclick_model->get_link($io, $counter);
                $this->Campclick_model->log_impression($io, $pixelId);

                $fallover_redirect = $link['dest_url'] . "&visitor=0&date=" . date("Y-m-d");
            } else {
                //open+click
                // log the impression
                $link = $this->Campclick_model->get_link($io, $counter);
                $this->Campclick_model->log_click($io, $link['link_id']);
                $this->Campclick_model->log_impression($io, $pixelId);

                $fallover_redirect = $link['dest_url'] . "&visitor=1&redirurl=" . urlencode("http://report-site.com/campclick/verizon") . "&date=" . date("Y-m-d");
            }
        }

        $cookie = array(
            "name" => "trafficPingTracker",
            "value" => $io,
            "expire" => 1825*86400,
            "domain" => ".report-site.com",
            "path" => "/",
        );

        set_cookie($cookie);
        header("Referer: http://report-site.com/i/{$io}");

        print "<html><head><meta http-equiv='refresh' content=\"1;URL='{$fallover_redirect}'\"></head><body>Redirecting...</body></html>";
        exit;
    }

    public function verizon()  {
        // Lets try to figure out the GEO of the click
        $geo = file_get_contents("http://api.hostip.info/get_json.php?ip={$this->input->ip_address()}&position=true");
        $geo = json_decode($geo);

        if ($geo->country_code == "US")    {
            list($city, $state) = explode(",", $geo->city);
            $state = trim($state);

            if ($state != "")  {
                $match_zips = $this->Zip_model->match_city_to_zip($city, $state);

                if (! empty($match_zips))   {
                    // zip specific match for campaign
                    $inprogress = $this->Zip_model->get_campaign_by_geo($match_zips);
                    log_message('error', "ZIP CAMPAIGN MATCH: " . $geo->city);
                } else {
                    // state match for campaign
                    $inprogress = $this->Zip_model->get_campaign_by_state($state);
                    log_message('error', "STATE CAMPAIGN MATCH: " . $state);
                }
            }
        } else {
            // throw away the click, its junk
            redirect("http://www.facebook.com/cheapdeals4me");
        }

        if (empty($inprogress))  {
            // country specific matching
            $inprogress = $this->Campclick_model->get_campaign_list(null, "Y", "AND campaign_is_started='Y' AND campaign_is_complete='N' AND is_geo='N'");
        }

        if (! empty($inprogress))  {
            $selected_element = array_rand($inprogress, 1);

            $io = $inprogress[$selected_element];

            $this->Campclick_model->io = $io['io'];
            $cap = $this->Campclick_model->get_current_click_cap();
            $campaign = $this->Campclick_model->get_campaign_by_io();

            /**
             * we went over cap, redirect!
             */
            if ($cap > $campaign['cap_per_hour'])  {
                redirect("http://facebook.com/cheapdeals4me");
                exit;
            }

            $linkInfo = $this->Campclick_model->select_random_link_improved();
            $link = $this->Campclick_model->get_link($io['io'], $linkInfo['counter']);

            if ($link === false)   {
                redirect("http://www.report-site.com/campaigns/landing/index.php?NOMATCH=1");
                exit;
            }

            if (stripos($link['dest_url'], "mailto") === false && stripos($link['dest_url'], "facebook") === false && stripos($link['dest_url'], "twitter") === false && $link['link_id'] !== "")  {
                //$this->Campclick_model->log_volume("mobile"); // log the ip -- we're going to track the volume of how fast this traffic is coming thru
                redirect("http://report-site.com/r/{$io['io']}");
            } else {
                redirect("http://www.report-site.com/campaigns/landing/index.php?NOSOC=1");
                exit;
            }
        }

    }

    /*
     * @description Create new Io list
     * @param $io insertion order id
     */
    protected function createIoList($io){
        $arrList = array();

        try {
            $user = new Adwords();
            $pAudience = $user->AddAudience($user, $io);

            // Create new user list in our db
            $bIoCreated = $this->Userlist_io_model->create_userlist_io($io, $pAudience['userList']->id, htmlspecialchars($pAudience['code']->snippet));

            if($bIoCreated){
                $arrList = $this->Userlist_io_model->get_userlist_from_io($io);
            }
        } catch (Exception $e) {
            $arrList = array();
        }

        return $arrList;
    }

    /*
     * @description Create new Vertical list
     * @param $vertical vertical id
     */
    protected function createVerticalList($vertical){
        $arrList = array();

        try {
            $user = new Adwords();
            $pAudience = $user->AddAudience($user, $vertical);

            print_r($pAudience);

            // Create new user list in our db
            $bIoCreated = $this->Userlist_vertical_model->create_userlist_vertical($vertical, $pAudience['userList']->id, htmlspecialchars($pAudience['code']->snippet));
            if($bIoCreated){
                $arrList = $this->Userlist_vertical_model->get_userlist_from_vertical($vertical);
            }
        } catch (Exception $e) {
            $arrList = array();
        }

        return $arrList;
    }

}
