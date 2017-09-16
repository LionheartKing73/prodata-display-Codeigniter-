<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Lib for Find It quick network
 * 
 */

class Fiq {
    
    private $username = "safedata";
    private $password = "safedata123";
    
    public function __construct() {
        
    }
    
    public function create_ad($io = null, $ad = null, $category = null){
        
        if (!$io){
            throw new exception("io required");    
        }
            
        if (!$ad){
            throw new exception("ad required");
        }
        
        if (!$category){
            throw new exception("ad required");
        }
            
        $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/createAd";
        
        $poststring = "Ad[title]=" . urlencode($ad['title']) . "&Ad[description]=" . urlencode($ad['description_1'] . $ad['description_2']) . "&Ad[category]={$category}&Ad[campaign_name]=" . urlencode($ad['campaign_name']) . "&Ad[url]={$ad['destination_url']}&Ad[display_url]={$ad['display_url']}&Ad[targets][]=US&Keywords[ron_bid]={$ad['bid']}";

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

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode == 404)  {
            print "ERROR ENCOUNTERED!!";
            return false;
        }

        curl_close($ch);

        return $output;

    }
    
    public function get_all_ads($status = "active"){
        
        $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/getAds&status={$status}";

        $output = json_decode($this->sendRequest($url));
        return $output;
    }
    
    public function pause_ad($id = null){
        
        if (!$id){
            throw new exception("id required");
        }
            
        $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/pause&id={$id}";

        $output = json_decode($this->sendRequest($url));

        return $output;
    }
    
    public function set_cap($id = null, $cap_amount = 0) {
        
        if (!$id){
            throw new exception("set_cap: invalid ID");
        }
            
        if ($cap_amount <= 0){
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
    
    public function resume_ad($id = null)	{
        
        if (!$id){
            throw new exception("id required");
        }
            
        $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/resume&id={$id}";

        $output = json_decode($this->sendRequest($url));

        return $output;
    }
    
    public function set_schedule($id = null, $schedule = null) {
        
        if (!$id){
            throw new exception("set_schedule: invalid ID");
        }


        if (!$schedule)  {
            $schedule = "&schedule[0_8]=1&schedule[0_9]=1&schedule[0_10]=1&schedule[0_11]=1&schedule[0_12]=1&schedule[0_13]=1&schedule[0_14]=1&schedule[0_15]=1&schedule[0_16]=1&schedule[0_17]=1&schedule[0_18]=1&schedule[0_19]=1&schedule[0_20]=1&schedule[0_21]=1&schedule[0_22]=1&schedule[0_23]=1&";
            $schedule .= "&schedule[1_8]=1&schedule[1_9]=1&schedule[1_10]=1&schedule[1_11]=1&schedule[1_12]=1&schedule[1_13]=1&schedule[1_14]=1&schedule[1_15]=1&schedule[1_16]=1&schedule[1_17]=1&schedule[1_18]=1&schedule[1_19]=1&schedule[1_20]=1&schedule[1_21]=1&schedule[1_22]=1&schedule[1_23]=1&";
            $schedule .= "&schedule[2_8]=1&schedule[2_9]=1&schedule[2_10]=1&schedule[2_11]=1&schedule[2_12]=1&schedule[2_13]=1&schedule[2_14]=1&schedule[2_15]=1&schedule[2_16]=1&schedule[2_17]=1&schedule[2_18]=1&schedule[2_19]=1&schedule[2_20]=1&schedule[2_21]=1&schedule[2_22]=1&schedule[2_23]=1&";
            $schedule .= "&schedule[3_8]=1&schedule[3_9]=1&schedule[3_10]=1&schedule[3_11]=1&schedule[3_12]=1&schedule[3_13]=1&schedule[3_14]=1&schedule[3_15]=1&schedule[3_16]=1&schedule[3_17]=1&schedule[3_18]=1&schedule[3_19]=1&schedule[3_20]=1&schedule[3_21]=1&schedule[3_22]=1&schedule[3_23]=1&";
            $schedule .= "&schedule[4_8]=1&schedule[4_9]=1&schedule[4_10]=1&schedule[4_11]=1&schedule[4_12]=1&schedule[4_13]=1&schedule[4_14]=1&schedule[4_15]=1&schedule[4_16]=1&schedule[4_17]=1&schedule[4_18]=1&schedule[4_19]=1&schedule[4_20]=1&schedule[4_21]=1&schedule[4_22]=1&schedule[4_23]=1&";
            $schedule .= "&schedule[5_8]=1&schedule[5_9]=1&schedule[5_10]=1&schedule[5_11]=1&schedule[5_12]=1&schedule[5_13]=1&schedule[5_14]=1&schedule[5_15]=1&schedule[5_16]=1&schedule[5_17]=1&schedule[5_18]=1&schedule[5_19]=1&schedule[5_20]=1&schedule[5_21]=1&schedule[5_22]=1&schedule[5_23]=1&";
            $schedule .= "&schedule[6_8]=1&schedule[6_9]=1&schedule[6_10]=1&schedule[6_11]=1&schedule[6_12]=1&schedule[6_13]=1&schedule[6_14]=1&schedule[6_15]=1&schedule[6_16]=1&schedule[6_17]=1&schedule[6_18]=1&schedule[6_19]=1&schedule[6_20]=1&schedule[6_21]=1&schedule[6_22]=1&schedule[6_23]=1&";
        } 
        else {


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

        return $output;
    }
      
    public function set_bid($id = null, $bid = 0.0025)  {

        if(!$id){
            throw new exception("set_bid: Invalid Id");
        }
        
        if (!$bid > 0)  {
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
    
    public function set_target($id = NULL, $targets = array("US"))   {
        
        if (!$id){
            throw new exception("set_target: invalid ID");
        }
           
        if (empty($targets)){
            throw new exception("set_target: invalid target");
        }
            
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

    public function get_ad_reports() {
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

        //print_r($output);

        return $output;
    }

    public function get_ad_report($id = "") {
        if ($id == "")
            throw new exception ("get_ad_report: id required");

//        if ($sdate == "")
//            throw new exception ("get_ad_report: sdate required");
//
//        if ($edate == "")
//            $reqDate = $sdate;
//        else
//            $reqDate = "{$sdate}+-+{$edate}";

        $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/report&id={$id}";

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
    
    private function sendRequest($url = null){
        
        if (!$url){
            throw new exception("url required");
        }
            
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
}