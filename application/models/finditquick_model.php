<?php 

class Finditquick_model extends CI_Model	{

    private $username = "safedata";
    private $password = "safedata123";

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

            $this->CI->load->library("Clickcap");
            
    }
    
    public function create_ad($io = "", $ad_id = 0){
        if ($io == "")
            throw new exception("io required");

        if ($ad_id == 0)
            throw new exception("ad required");

        $this->CI->Ad_model->id = $ad_id;
        $ad = $this->CI->Ad_model->get_by_id();

        $category = $this->CI->Ad_model->lookup_category($ad['category']);

        $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/createAd";

        $poststring = "Ad[title]=" . urlencode($ad['title']) . "&Ad[description]=" . urlencode($ad['description']) . "&Ad[category]={$category}&Ad[campaign_name]=" . urlencode($ad['campaign_name']) . "&Ad[url]={$ad['destination_url']}&Ad[display_url]={$ad['display_url']}&Ad[targets][]=US&Keywords[ron_bid]={$ad['bid']}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERAGENT, "api");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $poststring);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $output = curl_exec($ch);

        //print_r($output);
        //print_r(curl_getinfo($ch));

        // error encountered!
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode == 404)  {
            print "ERROR ENCOUNTERED!!";
            return false;
        }

        curl_close($ch);

        return $output;

    }
	
    /**
     * List All Ads from FIQ
     * @param string $status (active, paused)
     */
    public function get_all_ads($status = "active")	{
            $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/getAds&status={$status}";

            $output = json_decode($this->sendRequest($url));

            return $output;
    }
	
    public function pause_ad($id = "")	{
            if ($id == "")
                    throw new exception("id required");

            $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/pause&id={$id}";

            $output = json_decode($this->sendRequest($url));

            return $output;
    }
	
    public function resume_ad($id = "")	{
        if ($id == "")
            throw new exception("id required");

        $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/resume&id={$id}";

        $output = json_decode($this->sendRequest($url));

        return $output;
    }
	
    public function mark_ads_completed()	{
        
        $ads = $this->get_all_ads();

        $this->CI->load->library('email');

        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';
        $config['priority'] = 1;

    $this->CI->email->initialize($config);

    $msg = "";
            foreach($ads as $a)	{

                $all_links_fulfilled = false;
                if (stristr($a->Ad->url, "/r/")) {
                    list($junk, $io) = explode("/r/", $a->Ad->url, 2);
                } elseif (stristr($a->Ad->url, "/i/"))    {
                    list($junk, $io) = explode("/i/", $a->Ad->url, 2);
                } else {
                    continue;
                }

                    if ($io == "DR2245")
                            continue;

                    $this->CI->Campclick_model->io = $io;
                    $id = $this->CI->Campclick_model->get_campaign_id_from_io();
                    $this->CI->Campclick_model->id = $id;
                    $campaign = $this->CI->Campclick_model->get_campaign_by_io();

                    $click_count = $this->CI->Campclick_model->get_campaign_clicks();
                    $impression_count = $this->CI->Campclick_model->get_campaign_impressions();

                    if ($campaign['fire_open_pixel'] == "Y")    {
                        $count = (int)$impression_count;
                    } else {
                        $count = (int)$click_count;
                    }

                    $all_links_fulfilled = false;
                    $all_links_fulfilled = $this->CI->Campclick_model->all_links_fulfilled();

                    //if (($count >= $campaign['max_clicks'] && $count > 0 && $campaign['max_clicks'] > 0) or ($all_links_fulfilled === true))	{
                    if (($count >= $campaign['max_clicks'] && $count > 0 && $campaign['max_clicks'] > 0) || ($all_links_fulfilled === true))	{
                        //print "GOT HERE = {$io} ({$count} of {$campaign['max_clicks']}) - Fulfilled? " . (($all_links_fulfilled === true) ? "TRUE" : "FALSE") . "\n";
                            $status = $this->pause_ad($a->Ad->id);

                            if ($status->paused != 1)	{
                                    $msg = "UNABLE TO PAUSE CAMPAIGN: {$io}<br/>";
                                    $msg .= "IO: {$io}, AD_ID: {$a->Ad->id}<br/>";
                                    $msg .= "Camp Name: {$a->Ad->campaign_name}<br/>";

                                    $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
                                    $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
                                    $this->CI->email->subject("Report-Site: Pause Error {$io}");
                                    $this->CI->email->message($msg);
                                    $this->CI->email->send();
                            } else {
                                    $msg = "IO: ({$io}) {$a->Ad->campaign_name}<br/>";
                                    $msg .= "Total Clicks: {$count}<br/>";
                                    $msg .= "Ordered Clicks: {$campaign['max_clicks']}<br/>";
                                    $msg .= "Start Date: {$campaign['campaign_start_datetime']}<br/>";

                                    $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
                                    $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
                                    $this->CI->email->subject("Report-Site: Campaign Complete {$io}");
                                    $this->CI->email->message($msg);
                                    $this->CI->email->send();

                                    $this->CI->Campclick_model->campaign_complete();
                            }
                    }
            }

            // Mark the manual ads completed (e.g. bundle associatiates, manual process ads, and Other-vendor not using API-model ads)
            $this->CI->Campclick_model->mark_ads_completed();
    }

    public function mark_ads_processing()	{
            $scheduled = $this->Campclick_model->get_campaign_list(null, "Y", "AND campaign_is_started='N' AND campaign_is_complete='N'");

            foreach($scheduled as $s)	{
                    $this->Campclick_model->io = $s['io'];
                    $id = $this->Campclick_model->get_campaign_id_from_io();
                    $this->Campclick_model->id = $id;
                    $campaign = $this->Campclick_model->get_campaign();
                    $count = $this->Campclick_model->get_campaign_clicks();

                    // mark the campaign as processing
                    if ($count > 25 && date("Y-m-d H:i:s") >= $campaign['campaign_start_datetime'])	{
                            $this->Campclick_model->campaign_start();
                    }
            }
    }
	
    public function set_bid($id = "", $bid = 0.0025)  {
        if (! $bid > 0)  {
            throw new exception("set_bid: Invalid Bid");
        }

        $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/setBid&id={$id}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERAGENT, "api");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array("bid" => $bid));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $output = curl_exec($ch);
        curl_close($ch);
    }
	
    public function get_ad($id = "")   {
        if ($id == "")
            throw new exception("id required");

        $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/getAd&id={$id}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERAGENT, "api");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $output = curl_exec($ch);

        return $output;
    }
	
    public function set_target($id = "", $targets = array("US"))   {
        if ($id == "")
            throw new exception("set_target: invalid ID");

        if (empty($targets))
            throw new exception("set_target: invalid target");

        $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/setTargeting&id={$id}";

        $poststring = "";
        foreach($targets as $t)    {
            $poststring .= "targets[]={$t}&";
        }

        $poststring = trim($poststring, "&");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERAGENT, "api");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $poststring);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $output = curl_exec($ch);

        curl_close($ch);

        //mail("jkorkin@safedatatech.onmicrosoft.com", "Set Target", print_r($output, true));

        return $output;
    }
	
    public function set_cap($id = null, $cap_amount = 0) {
        
        if (!$id){
            throw new exception("set_cap: invalid ID");
        }
            
        if (! $cap_amount > 0){
            throw new exception("set_cap: invalid cap_amount");
        }
            

        $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/setCap&id={$id}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERAGENT, "api");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array("cap" => sprintf("%.2f", $cap_amount)));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);


        $output = curl_exec($ch);
        curl_close($ch);

        //mail("jkorkin@safedatatech.onmicrosoft.com", "Set Cap", print_r($output, true));

        return $output;

    }
	
    public function set_schedule($id = "", $schedule = "") {
    
        if (!$id){
            throw new exception("set_schedule: invalid ID");
        }
            

       // print "---- SCHEDULE PASSED ---\n" . $schedule . "\n---- SCHEDULE PASSED ----\n\n\n";

        if ($schedule == "")  {
            $schedule = "&schedule[0_8]=1&schedule[0_9]=1&schedule[0_10]=1&schedule[0_11]=1&schedule[0_12]=1&schedule[0_13]=1&schedule[0_14]=1&schedule[0_15]=1&schedule[0_16]=1&schedule[0_17]=1&schedule[0_18]=1&schedule[0_19]=1&schedule[0_20]=1&schedule[0_21]=1&schedule[0_22]=1&schedule[0_23]=1&";
            $schedule .= "&schedule[1_8]=1&schedule[1_9]=1&schedule[1_10]=1&schedule[1_11]=1&schedule[1_12]=1&schedule[1_13]=1&schedule[1_14]=1&schedule[1_15]=1&schedule[1_16]=1&schedule[1_17]=1&schedule[1_18]=1&schedule[1_19]=1&schedule[1_20]=1&schedule[1_21]=1&schedule[1_22]=1&schedule[1_23]=1&";
            $schedule .= "&schedule[2_8]=1&schedule[2_9]=1&schedule[2_10]=1&schedule[2_11]=1&schedule[2_12]=1&schedule[2_13]=1&schedule[2_14]=1&schedule[2_15]=1&schedule[2_16]=1&schedule[2_17]=1&schedule[2_18]=1&schedule[2_19]=1&schedule[2_20]=1&schedule[2_21]=1&schedule[2_22]=1&schedule[2_23]=1&";
            $schedule .= "&schedule[3_8]=1&schedule[3_9]=1&schedule[3_10]=1&schedule[3_11]=1&schedule[3_12]=1&schedule[3_13]=1&schedule[3_14]=1&schedule[3_15]=1&schedule[3_16]=1&schedule[3_17]=1&schedule[3_18]=1&schedule[3_19]=1&schedule[3_20]=1&schedule[3_21]=1&schedule[3_22]=1&schedule[3_23]=1&";
            $schedule .= "&schedule[4_8]=1&schedule[4_9]=1&schedule[4_10]=1&schedule[4_11]=1&schedule[4_12]=1&schedule[4_13]=1&schedule[4_14]=1&schedule[4_15]=1&schedule[4_16]=1&schedule[4_17]=1&schedule[4_18]=1&schedule[4_19]=1&schedule[4_20]=1&schedule[4_21]=1&schedule[4_22]=1&schedule[4_23]=1&";
            $schedule .= "&schedule[5_8]=1&schedule[5_9]=1&schedule[5_10]=1&schedule[5_11]=1&schedule[5_12]=1&schedule[5_13]=1&schedule[5_14]=1&schedule[5_15]=1&schedule[5_16]=1&schedule[5_17]=1&schedule[5_18]=1&schedule[5_19]=1&schedule[5_20]=1&schedule[5_21]=1&schedule[5_22]=1&schedule[5_23]=1&";
            $schedule .= "&schedule[6_8]=1&schedule[6_9]=1&schedule[6_10]=1&schedule[6_11]=1&schedule[6_12]=1&schedule[6_13]=1&schedule[6_14]=1&schedule[6_15]=1&schedule[6_16]=1&schedule[6_17]=1&schedule[6_18]=1&schedule[6_19]=1&schedule[6_20]=1&schedule[6_21]=1&schedule[6_22]=1&schedule[6_23]=1&";
        } else {
            /*
            $sched_array = array();
            $schedule = "&schedule[0_8]=0&schedule[0_9]=1&schedule[0_10]=0&schedule[0_11]=1&schedule[0_12]=0&schedule[0_13]=1&schedule[0_14]=0&schedule[0_15]=1&schedule[0_16]=0&schedule[0_17]=1&schedule[0_18]=0&schedule[0_19]=1&schedule[0_20]=0&schedule[0_21]=1&schedule[0_22]=0&schedule[0_23]=1&";
            $schedule .= "&schedule[1_8]=1&schedule[1_9]=0&schedule[1_10]=1&schedule[1_11]=0&schedule[1_12]=1&schedule[1_13]=0&schedule[1_14]=1&schedule[1_15]=0&schedule[1_16]=1&schedule[1_17]=0&schedule[1_18]=1&schedule[1_19]=0&schedule[1_20]=1&schedule[1_21]=0&schedule[1_22]=1&schedule[1_23]=1&";
            $schedule .= "&schedule[2_8]=0&schedule[2_9]=1&schedule[2_10]=0&schedule[2_11]=1&schedule[2_12]=0&schedule[2_13]=1&schedule[2_14]=0&schedule[2_15]=1&schedule[2_16]=0&schedule[2_17]=1&schedule[2_18]=0&schedule[2_19]=1&schedule[2_20]=0&schedule[2_21]=1&schedule[2_22]=0&schedule[2_23]=1&";
            $schedule .= "&schedule[3_8]=1&schedule[3_9]=0&schedule[3_10]=1&schedule[3_11]=0&schedule[3_12]=1&schedule[3_13]=0&schedule[3_14]=1&schedule[3_15]=0&schedule[3_16]=1&schedule[3_17]=0&schedule[3_18]=1&schedule[3_19]=0&schedule[3_20]=1&schedule[3_21]=0&schedule[3_22]=1&schedule[3_23]=1&";
            $schedule .= "&schedule[4_8]=0&schedule[4_9]=1&schedule[4_10]=0&schedule[4_11]=1&schedule[4_12]=0&schedule[4_13]=1&schedule[4_14]=0&schedule[4_15]=1&schedule[4_16]=0&schedule[4_17]=1&schedule[4_18]=0&schedule[4_19]=1&schedule[4_20]=0&schedule[4_21]=1&schedule[4_22]=0&schedule[4_23]=1&";
            $schedule .= "&schedule[5_8]=1&schedule[5_9]=0&schedule[5_10]=1&schedule[5_11]=0&schedule[5_12]=1&schedule[5_13]=0&schedule[5_14]=1&schedule[5_15]=0&schedule[5_16]=1&schedule[5_17]=0&schedule[5_18]=1&schedule[5_19]=0&schedule[5_20]=1&schedule[5_21]=0&schedule[5_22]=1&schedule[5_23]=1&";
            $schedule .= "&schedule[6_8]=0&schedule[6_9]=1&schedule[6_10]=0&schedule[6_11]=1&schedule[6_12]=0&schedule[6_13]=1&schedule[6_14]=0&schedule[6_15]=1&schedule[6_16]=0&schedule[6_17]=1&schedule[6_18]=0&schedule[6_19]=1&schedule[6_20]=0&schedule[6_21]=1&schedule[6_22]=0&schedule[6_23]=1&";
            $sched_array[] = $schedule; 

            $schedule = "&schedule[0_8]=1&schedule[0_9]=0&schedule[0_10]=1&schedule[0_11]=0&schedule[0_12]=1&schedule[0_13]=0&schedule[0_14]=1&schedule[0_15]=0&schedule[0_16]=1&schedule[0_17]=0&schedule[0_18]=1&schedule[0_19]=0&schedule[0_20]=1&schedule[0_21]=0&schedule[0_22]=1&schedule[0_23]=1&";
            $schedule .= "&schedule[1_8]=0&schedule[1_9]=1&schedule[1_10]=0&schedule[1_11]=1&schedule[1_12]=0&schedule[1_13]=1&schedule[1_14]=0&schedule[1_15]=1&schedule[1_16]=0&schedule[1_17]=1&schedule[1_18]=0&schedule[1_19]=1&schedule[1_20]=0&schedule[1_21]=1&schedule[1_22]=0&schedule[1_23]=1&";
            $schedule .= "&schedule[2_8]=1&schedule[2_9]=0&schedule[2_10]=1&schedule[2_11]=0&schedule[2_12]=1&schedule[2_13]=0&schedule[2_14]=1&schedule[2_15]=0&schedule[2_16]=1&schedule[2_17]=0&schedule[2_18]=1&schedule[2_19]=0&schedule[2_20]=1&schedule[2_21]=0&schedule[2_22]=1&schedule[2_23]=1&";
            $schedule .= "&schedule[3_8]=0&schedule[3_9]=1&schedule[3_10]=0&schedule[3_11]=1&schedule[3_12]=0&schedule[3_13]=1&schedule[3_14]=0&schedule[3_15]=1&schedule[3_16]=0&schedule[3_17]=1&schedule[3_18]=0&schedule[3_19]=1&schedule[3_20]=0&schedule[3_21]=1&schedule[3_22]=0&schedule[3_23]=1&";
            $schedule .= "&schedule[4_8]=1&schedule[4_9]=0&schedule[4_10]=1&schedule[4_11]=0&schedule[4_12]=1&schedule[4_13]=0&schedule[4_14]=1&schedule[4_15]=0&schedule[4_16]=1&schedule[4_17]=0&schedule[4_18]=1&schedule[4_19]=0&schedule[4_20]=1&schedule[4_21]=0&schedule[4_22]=1&schedule[4_23]=1&";
            $schedule .= "&schedule[5_8]=0&schedule[5_9]=1&schedule[5_10]=0&schedule[5_11]=1&schedule[5_12]=0&schedule[5_13]=1&schedule[5_14]=0&schedule[5_15]=1&schedule[5_16]=0&schedule[5_17]=1&schedule[5_18]=0&schedule[5_19]=1&schedule[5_20]=0&schedule[5_21]=1&schedule[5_22]=0&schedule[5_23]=1&";
            $schedule .= "&schedule[6_8]=1&schedule[6_9]=0&schedule[6_10]=1&schedule[6_11]=0&schedule[6_12]=1&schedule[6_13]=0&schedule[6_14]=1&schedule[6_15]=0&schedule[6_16]=1&schedule[6_17]=0&schedule[6_18]=1&schedule[6_19]=0&schedule[6_20]=1&schedule[6_21]=0&schedule[6_22]=1&schedule[6_23]=1&";
            $sched_array[] = $schedule;
            */

            $schedule = "&schedule[0_8]=1&schedule[0_9]=0&schedule[0_10]=1&schedule[0_11]=1&schedule[0_12]=0&schedule[0_13]=1&schedule[0_14]=0&schedule[0_15]=1&schedule[0_16]=1&schedule[0_17]=0&schedule[0_18]=0&schedule[0_19]=1&schedule[0_20]=1&schedule[0_21]=1&schedule[0_22]=0&schedule[0_23]=1&";
            $schedule .= "&schedule[1_8]=1&schedule[1_9]=0&schedule[1_10]=1&schedule[1_11]=1&schedule[1_12]=0&schedule[1_13]=1&schedule[1_14]=0&schedule[1_15]=1&schedule[1_16]=1&schedule[1_17]=0&schedule[1_18]=0&schedule[1_19]=1&schedule[1_20]=1&schedule[1_21]=1&schedule[1_22]=1&schedule[1_23]=1&";
            $schedule .= "&schedule[2_8]=1&schedule[2_9]=0&schedule[2_10]=1&schedule[2_11]=1&schedule[2_12]=0&schedule[2_13]=1&schedule[2_14]=0&schedule[2_15]=1&schedule[2_16]=1&schedule[2_17]=0&schedule[2_18]=0&schedule[2_19]=1&schedule[2_20]=1&schedule[2_21]=1&schedule[2_22]=0&schedule[2_23]=1&";
            $schedule .= "&schedule[3_8]=1&schedule[3_9]=0&schedule[3_10]=1&schedule[3_11]=1&schedule[3_12]=0&schedule[3_13]=1&schedule[3_14]=0&schedule[3_15]=1&schedule[3_16]=1&schedule[3_17]=0&schedule[3_18]=0&schedule[3_19]=1&schedule[3_20]=1&schedule[3_21]=1&schedule[3_22]=1&schedule[3_23]=1&";
            $schedule .= "&schedule[4_8]=1&schedule[4_9]=0&schedule[4_10]=1&schedule[4_11]=1&schedule[4_12]=0&schedule[4_13]=1&schedule[4_14]=0&schedule[4_15]=1&schedule[4_16]=1&schedule[4_17]=0&schedule[4_18]=0&schedule[4_19]=1&schedule[4_20]=1&schedule[4_21]=1&schedule[4_22]=0&schedule[4_23]=1&";
            $schedule .= "&schedule[5_8]=1&schedule[5_9]=0&schedule[5_10]=1&schedule[5_11]=1&schedule[5_12]=0&schedule[5_13]=1&schedule[5_14]=0&schedule[5_15]=1&schedule[5_16]=1&schedule[5_17]=0&schedule[5_18]=0&schedule[5_19]=1&schedule[5_20]=1&schedule[5_21]=1&schedule[5_22]=1&schedule[5_23]=1&";
            $schedule .= "&schedule[6_8]=1&schedule[6_9]=0&schedule[6_10]=1&schedule[6_11]=1&schedule[6_12]=0&schedule[6_13]=1&schedule[6_14]=0&schedule[6_15]=1&schedule[6_16]=1&schedule[6_17]=0&schedule[6_18]=0&schedule[6_19]=1&schedule[6_20]=1&schedule[6_21]=1&schedule[6_22]=0&schedule[6_23]=1&";
            $sched_array[] = $schedule;


            $schedule = "&schedule[0_8]=1&schedule[0_9]=1&schedule[0_10]=0&schedule[0_11]=0&schedule[0_12]=1&schedule[0_13]=0&schedule[0_14]=1&schedule[0_15]=0&schedule[0_16]=1&schedule[0_17]=1&schedule[0_18]=0&schedule[0_19]=1&schedule[0_20]=1&schedule[0_21]=0&schedule[0_22]=1&schedule[0_23]=1&";
            $schedule .= "&schedule[1_8]=1&schedule[1_9]=1&schedule[1_10]=0&schedule[1_11]=0&schedule[1_12]=1&schedule[1_13]=0&schedule[1_14]=1&schedule[1_15]=0&schedule[1_16]=1&schedule[1_17]=1&schedule[1_18]=0&schedule[1_19]=1&schedule[1_20]=1&schedule[1_21]=0&schedule[1_22]=1&schedule[1_23]=1&";
            $schedule .= "&schedule[2_8]=1&schedule[2_9]=1&schedule[2_10]=0&schedule[2_11]=0&schedule[2_12]=1&schedule[2_13]=0&schedule[2_14]=1&schedule[2_15]=0&schedule[2_16]=1&schedule[2_17]=1&schedule[2_18]=0&schedule[2_19]=1&schedule[2_20]=1&schedule[2_21]=0&schedule[2_22]=1&schedule[2_23]=1&";
            $schedule .= "&schedule[3_8]=1&schedule[3_9]=1&schedule[3_10]=0&schedule[3_11]=0&schedule[3_12]=1&schedule[3_13]=0&schedule[3_14]=1&schedule[3_15]=0&schedule[3_16]=1&schedule[3_17]=1&schedule[3_18]=0&schedule[3_19]=1&schedule[3_20]=1&schedule[3_21]=0&schedule[3_22]=1&schedule[3_23]=1&";
            $schedule .= "&schedule[4_8]=1&schedule[4_9]=1&schedule[4_10]=0&schedule[4_11]=0&schedule[4_12]=1&schedule[4_13]=0&schedule[4_14]=1&schedule[4_15]=0&schedule[4_16]=1&schedule[4_17]=1&schedule[4_18]=0&schedule[4_19]=1&schedule[4_20]=1&schedule[4_21]=0&schedule[4_22]=1&schedule[4_23]=1&";
            $schedule .= "&schedule[5_8]=1&schedule[5_9]=1&schedule[5_10]=0&schedule[5_11]=0&schedule[5_12]=1&schedule[5_13]=0&schedule[5_14]=1&schedule[5_15]=0&schedule[5_16]=1&schedule[5_17]=1&schedule[5_18]=0&schedule[5_19]=1&schedule[5_20]=1&schedule[5_21]=0&schedule[5_22]=1&schedule[5_23]=1&";
            $schedule .= "&schedule[6_8]=1&schedule[6_9]=1&schedule[6_10]=0&schedule[6_11]=0&schedule[6_12]=1&schedule[6_13]=0&schedule[6_14]=1&schedule[6_15]=0&schedule[6_16]=1&schedule[6_17]=1&schedule[6_18]=0&schedule[6_19]=1&schedule[6_20]=1&schedule[6_21]=0&schedule[6_22]=1&schedule[6_23]=1&";
            $sched_array[] = $schedule;

            $schedule = $sched_array[array_rand($sched_array, 1)];
        }

        $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/setSchedule&id={$id}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERAGENT, "api");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $schedule);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $output = curl_exec($ch);
        curl_close($ch);
        //print "\n\n---- SCHEDULE USED ---\n" . $schedule . "\n---- SCHEDULE USED ----\n\n\n";

        return $output;
    }
	
    public function get_ad_reports()    {
        $date = date("Y-m-d");
        $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/reportDetails&group=campaign&date={$date}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERAGENT, "api");
        curl_setopt($ch, CURLOPT_POST, true);

        $output = curl_exec($ch);

        print_r($output);

        return $output;
    }

    public function get_ad_report($id = "", $sdate = "", $edate = "")    {
        if ($id == "")
            throw new exception ("get_ad_report: id required");

        if ($sdate == "")
            throw new exception ("get_ad_report: sdate required");

        if ($edate == "")
            $reqDate = $sdate;
        else
            $reqDate = "{$sdate}+-+{$edate}";

        $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/report&date={$reqDate}&id={$id}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERAGENT, "api");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $output = curl_exec($ch);

        return $output;
    }
		
    private function sendRequest($url = ""){
        
        if ($url == "")
                throw new exception("url required");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERAGENT, "api");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }
	
    public function real_time_bid_adjustment()  {
        
        $this->CI->Log_model->purge_old_entries();
        
        $processing = $this->CI->Campclick_model->get_campaign_list(null, "Y", "AND campaign_is_complete='N' AND ppc_network='FIQ' AND ppc_network_ad_id <> '' AND io IN ('13630', '13827', '13834', '13765', '13840', '13863', '13808', '13835', '13862', '13841')");

        foreach($processing as $p)  {
            print "Processing IO: {$p['io']}\n";
            
            $bid_rate = 0.0000;

            $this->CI->Campclick_model->io = $p['io'];
	        $campaign = $this->CI->Campclick_model->get_campaign_by_io();

	        $current_click_count = $this->CI->Campclick_model->get_click_count();
	        $current_hourly_count = $this->CI->Campclick_model->get_click_count_for_hour();

	        $this->CI->Ad_model->id = $campaign['local_ad_id'];
	        $ad = $this->CI->Ad_model->get_by_id();
	        
	        // if we dont have a logged bid rate, skip it and move on. Its likely a manually adjusted campaign.
	        if (! $ad['bid'] > 0)
	            continue;
	        
	        // skip if we're within the first hour
	        if ((time() - strtotime($campaign['campaign_start_datetime']) < 3600) && ($current_hourly_count['clicks'] < $campaign['cap_per_hour']))
	            continue;
	        
	        //$fiqAd = $this->get_ad($campaign['ppc_network_ad_id']);
	        $this->CI->clickcap->io = $campaign['io'];
	        $campaignActiveStatus = $this->CI->clickcap->get_campaign_status();
	        if ($campaignActiveStatus == "")   {
	            $this->CI->clickcap->set_campaign_status("ACTIVE");
	        }

	        $originalCampaignActiveStatus = $this->CI->clickcap->get_campaign_status();
	        
//	        print "{$p['io']} AD {$campaign['local_ad_id']} BID RATE: {$ad['bid']}\n";
	        $bid_rate = $ad['bid']; // current bid rate, we adjust this
	        $old_bid_rate = $ad['bid']; // old bid rate, we dont adjust this
	        	        
	        if ($current_hourly_count['clicks'] >= $campaign['cap_per_hour'] && $current_click_count <= $campaign['max_clicks'])   {
	            if ($this->CI->clickcap->get_campaign_status() == "ACTIVE")    {
	                $this->CI->Log_model->io = $p['io'];
	                $this->CI->Log_model->action = "CAMPAIGN_PAUSE";
	                $this->CI->Log_model->note = "Traffic Exceeded for {$p['io']}; for hour ({$current_hourly_count['hour']}:00); {$current_hourly_count['clicks']} of {$campaign['cap_per_hour']}";
	                $this->CI->Log_model->create();
	                
	                $bid_rate = $bid_rate - 0.0001;
	                 
	                $this->CI->Log_model->io = $p['io'];
	                $this->CI->Log_model->action = "CAMPAIGN_BID_ADJUSTMENT";
	                $this->CI->Log_model->note = "PPC Bid Changed for {$p['io']} from: {$old_bid_rate} to:" . $bid_rate;
	                $this->CI->Log_model->create();
	                 
	                print $this->CI->Log_model->note . "\n";
	                 
	                $response = $this->set_bid($campaign['ppc_network_ad_id'], $bid_rate); // decrease the bid
	                $response = $this->pause_ad($campaign['ppc_network_ad_id']); // pause the campaign
	                 
	                $this->CI->clickcap->set_campaign_status("INACTIVE");
	            }
	        } else {
	            $this->CI->Log_model->io = $p['io'];
	            $this->CI->Log_model->action = "CAMPAIGN_RESUME";
	            $this->CI->Log_model->note = "Traffic Under for {$p['io']}; {$current_hourly_count['clicks']} of {$campaign['cap_per_hour']}";
	            $this->CI->Log_model->create();

	            // determine if we're coming in "hot" or "cold" on the campaign pacing speed
	            $speed = ($current_hourly_count['clicks'] / $campaign['cap_per_hour']);
	            if ($speed > 1)    {
	                // too hot (fast)
	                $bid_rate = $bid_rate - 0.0001;

	                $this->CI->Log_model->io = $p['io'];
	                $this->CI->Log_model->action = "CAMPAIGN_BID_ADJUSTMENT";
	                $this->CI->Log_model->note = "PPC Bid Changed for {$p['io']} from: {$old_bid_rate} to:" . $bid_rate;
	                $this->CI->Log_model->create();

	                print $this->CI->Log_model->note . "\n";

	                $response = $this->set_bid($campaign['ppc_network_ad_id'], $bid_rate); // decrease the bid
	            } else {
	                // too cold (slow) - we will only adjust this *IF* we are under 50% cap_per_hour AND 45-min into the hour
	                if (($current_hourly_count['clicks']/2) < ($campaign['cap_per_hour']/2) && (date("m") > 45))   {

	                    $bid_rate = $bid_rate + 0.0001;
	                    
	                    $this->CI->Log_model->io = $p['io'];
	                    $this->CI->Log_model->action = "CAMPAIGN_BID_ADJUSTMENT";
	                    $this->CI->Log_model->note = "PPC Bid Changed for {$p['io']} from: {$old_bid_rate} to:" . $bid_rate;
	                    $this->CI->Log_model->create();
	                    
	                    print $this->CI->Log_model->note . "\n";
	                    
	                    $response = $this->set_bid($campaign['ppc_network_ad_id'], $bid_rate); // decrease the bid
	                }
	            }
	            
	            if ($originalCampaignActiveStatus == "INACTIVE")   {
	                // after all of the changes, resume the ad
	                $response = $this->resume_ad($campaign['ppc_network_ad_id']); // resume the campaign
	            }
	            
	            $this->CI->clickcap->set_campaign_status("ACTIVE");
	        }
	        
	        // update the database with the "new" bid rate & daily cap
	        if ($bid_rate > 0 && $bid_rate != $old_bid_rate) {
	            $this->CI->Ad_model->bid = $bid_rate;
	            $this->CI->Ad_model->id = $ad['id'];
	            $clicks_per_day = ceil((($campaign['fire_open_pixel'] == "Y") ? $campaign['impression_clicks'] : $campaign['max_clicks']) / 4);
	            $spend_per_day = sprintf("%.2f", $clicks_per_day * $bid_rate);
	            $spend_per_day = ($spend_per_day > 4) ? $spend_per_day : 4.00;

	            $this->CI->Ad_model->daily_cap = $spend_per_day;
	            $this->CI->Ad_model->set_bid();
	            $this->CI->Ad_model->set_daily_cap();
	            
	            // update FIQ with new daily cap
	            $this->set_cap($campaign['ppc_network_ad_id'], $spend_per_day);
	        }
	        
	        if ($bid_rate > 0.0037)    {
	            $config['protocol'] = 'sendmail';
	            $config['mailpath'] = '/usr/sbin/sendmail';
	            $config['charset'] = 'utf-8';
	            $config['wordwrap'] = TRUE;
	            $config['mailtype'] = 'html';
	            $config['priority'] = 1;

	            $this->CI->load->library('email');
	            $this->CI->email->initialize($config);

	            $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
	            $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
	            $this->CI->email->subject('Report-Site: RTB Bid Alert (FIQ)');
	            $this->CI->email->message("** RTB BID ALERT **<br/><br/>Campaign IO: {$p['io']}<br/>\nCampaign Name: {$campaign['create_name']}<br/>Bid Amount: {$bid_rate}<br/>Date: " . date("Y-m-d H:i:s"));
	            $this->CI->email->send();
	        }
        }
    }

}