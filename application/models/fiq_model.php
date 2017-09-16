<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Fiq_model
 *
 */

class Fiq_model extends CI_Model {
    
    protected $CI;
    protected $network_id = 2;
    public function __construct()	{
        
        parent::__construct();
        
        $this->CI =& get_instance();
        $this->CI->load->database();
        
        $this->CI->load->library("Fiq");      
    }
    
    public function create($campaign){

        if (!$campaign){
            throw new exception("campaign required");
        }
        
        if ($campaign['total_records']){
            $this->create_ppc_ad($campaign);
        }
        else {
            $this->create_text_ad($campaign);
        }
        
    }
    
    private function create_text_ad($campaign){
                
        $this->CI->load->model('V2_ad_model');
        $this->CI->load->model('Zip_model');
        $this->CI->load->model('V2_master_campaign_model');
        $this->CI->load->model('V2_campaign_network_criteria_rel_model');
        
        $ads = $this->CI->V2_ad_model->get_ads_by_campaign_id($campaign['id']);
        
        // fix our little issue where people are submitting blank geotype orders.
        if ((string)$campaign['geotype'] == "")    {

            if ($campaign['zip'] != "" && (strlen($campaign['state']) >= 2) && $campaign['country'] == "")    {
                $campaign['geotype'] = "POSTALCODE";
            } 
            elseif ($campaign['state'] != "" && $campaign['zip'] == "")  {
                $campaign['geotype'] = "STATE";
            } 
            elseif ($campaign['country'] != "" && $campaign['state'] == "" && $campaign['zip'] == "") {
                $campaign['geotype'] = "COUNTRY";
            } 
            else {
                $campaign['geotype'] = ""; 
            }
        }
        
        $this->CI->V2_master_campaign_model->update($campaign['id'], [
            'campaign_is_converted_to_live' => 'Y',
            'campaign_status' => 'ACTIVE',
            'network_campaign_status' => 'ACTIVE'
        ]);
        
        $error_count = 0;

        foreach ($ads as $ad){
            
            /*
             * Hard code for test;
             */
            $ad['daily_cap'] = 1;
            $ad['bid'] = 0.0001;
            
            
            $fiq_id = $this->fiq->create_ad($campaign['io'], $ad, $campaign['vertical']);
                        
            if ($fiq_id === false) {
                
                $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
                $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
                $this->CI->email->subject('Report-Site: FIQ Campaign Creation Error');
                $this->CI->email->message("** FIND IT QUICK CAMPAIGN CREATE ERROR **<br/><br/>Campaign IO: {$campaign['io']}<br/>\nCampaign Name: {$campaign['name']}<br/>Date: " . date("Y-m-d H:i:s"));
                $this->CI->email->send();

            } 
            else {
                
                $fiq_id = json_decode($fiq_id, true);
                
                if (isset($fiq_id['errors'])){
                     
                    $error_data_for_update["approval_status"] = 'DISAPPROVED';
                    $error_data_for_update["disapproval_reasons"] = json_encode($fiq_id['errors']);
                    $this->CI->V2_ad_model->update($ad['id'], $error_data_for_update);
                    $error_count++;
                }
                else {
                    $fiq_id = $fiq_id[0];
                }
            }
                        
            $activate_campaign = false;
            
            if (isset($fiq_id['Ad']['id'])) {

                // pause the ad while we "operate" on it - we dont need it to go live with incorrect info
                $this->fiq->pause_ad($fiq_id['Ad']['id']);         
                //update ad in our db, set network id 
                $this->V2_ad_model->update($ad['id'], ['network_creative_id' => $fiq_id['Ad']['id'], 'creative_is_active' => 'Y']);

                // set target, budget, schedule and cap
                $this->fiq->set_schedule($fiq_id['Ad']['id']); // use default (all hours of the day)
                
                $this->fiq->set_cap($fiq_id['Ad']['id'], $ad['daily_cap']);
                $this->fiq->set_bid($fiq_id['Ad']['id'], $ad['bid']);
                switch(strtoupper($campaign['geotype'])){
                    default:
                    case "COUNTRY":
                        $set_target_result = $this->fiq->set_target($fiq_id['Ad']['id'], [$campaign['country']]);
                        $criteria_value = $campaign['country'];
                        $activate_campaign = true;
                        break;

                    case "STATE":
                        $state = array();
                        foreach(explode(",", $campaign['state']) as $s)  {
                            if ($s == "")
                                continue;

                            $state[] = "{$campaign['country']}/{$s}";
                        }
                        $set_target_result = $this->fiq->set_target($fiq_id['Ad']['id'], $state);
                        $criteria_value = $campaign['state'];
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
                        $set_target_result = $this->fiq->set_target($fiq_id['Ad']['id'], $resultGeo);
                        $criteria_value = $campaign['zip'];
                        $activate_campaign = true;
                        }
                    break;
                }

                // resume ad
                if ($activate_campaign === true)   {
                    $this->fiq->resume_ad($fiq_id['Ad']['id']);

                    $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
                    $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
                    $this->CI->email->subject("Report-Site: FIQ New Campaign Creation [{$campaign['io']}]");
                    $this->CI->email->message("** FIND IT QUICK NEW CAMPAIGN CREATION **<br/><br/>Campaign IO: {$campaign['io']}<br/>\nCampaign Name: {$campaign['create_name']}<br/>Date: " . date("Y-m-d H:i:s"));
                    $this->CI->email->send();

                } 
                else {
                    // need to make ad creative status paused

                    $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
                    $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
                    $this->CI->email->subject("Report-Site: FIQ GEO Campaign Creation Error [{$campaign['io']}]");
                    $this->CI->email->message("** FIND IT QUICK GEO CAMPAIGN CREATE ERROR **<br/><br/>Campaign IO: {$campaign['io']}<br/>\nCampaign Name: {$campaign['create_name']}<br/>Date: " . date("Y-m-d H:i:s"));
                    $this->CI->email->send();
                }

                $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $criteria_value, $campaign['geotype']);
            }
        }

        if ($error_count == count($ads)){

            $error_data['disapproval_reasons'] = "All campaign ads are disapproved";
            $error_data['campaign_status'] = 'DISAPPROVED';
            $this->CI->V2_master_campaign_model->update($campaign['id'], $error_data);

            return 'rejected';
        }
        else {
            return 'approved';
        }
        
    }
    
    private function create_ppc_ad($campaign){
        echo "<pre>";
        print_r($campaign);die;
        /*
         echo "<pre>";
        print_r($campaign);die;

        
        
        $this->CI->load->model('V2_ads_link_model');
        $this->CI->load->model('V2_master_campaign_model');
        $this->CI->load->model('Zip_model');
        $this->CI->load->model('v2_campaign_network_criteria_rel_model');
        
        echo "<pre>";
        print_r($campaign);die;
        
        
        
        $this->CI->V2_ads_link_model->pending_campaign_id = $campaign['id'];
               
        $ads = [];
        
        
        $clicks_per_day = ceil((($campaign['fire_open_pixel'] == "Y") ? $campaign['total_opens'] : $campaign['total_clicks']) / 2.85);          
        $spend_per_day = sprintf("%.2f", $clicks_per_day * $initial_bid); // default bid, including overage
        $spend_per_day = ($spend_per_day > 4) ? $spend_per_day : 4.00; // minimum spend per day is $4.00
        
        if ($campaign['total_records']){
            
            $spend_per_day = $campaign['budget'];
            
            $ad =  $this->CI->V2_ad_model->auto_generate_ad_content($link['destination_url']);
            $link = $this->CI->V2_ads_link_model->get_primary_campaign_link();
            
            $data = [
                'title' => $ad['title'],
                'description_1' => substr($ad['description'], 0, 35),
                'description_2' => substr($ad['description'], 35, 70),
                'display_url' => $ad['display_url'],
                'creative_name' => $campaign['io'] . " - " . $campaign['name'] . " (AUTO)",
                'destination_url' => ($campaign['fire_open_pixel'] == "Y") ? "http://{$campaign['domain']}/c2/{$campaign['id']}/{$link['id']}": "http://{$campaign['domain']}/c2/{$campaign['id']}/{$link['id']}",
                'bid' => $initial_bid,
                'daily_cap' => $spend_per_day,
                'network_id' => $campaign['network_id'],
                'campaign_id' => $campaign['id'],
                'creative_type' => 'TEXTAD',
            ];
                
            //Validate ad data
            $insert_data = $this->CI->V2_ad_model->validate($data);
            //if not valid return false
            if ($insert_data['messages']){
                echo $messages[0];die;
            }
            
            $id = $this->CI->V2_ad_model->create($insert_data['valide_ad']);
            
            //Add ad in our db
            if(!$id){
                echo "Something went wrong"; die();
            }
            
            $ad['id'] = $id;      
            $ads[] = $ad;
        }
        else {
            
        }
          


        
        foreach ($ads as $ad){
        
            $ad['approval_status'] = "APPROVED";
            $ad['creative_status'] = "ACTIVE";
            
            // create the ad @ FIQ
            $fiq_id = $this->fiq->create_ad($campaign['io'], $insert_data['valide_ad'], $campaign['vertical']);

            //$id = 36;
            //$fiq_id = '[{"Ad":{ "status":"active", "id":"94614310460165", "title":"Tampa Real Estate - 2704 ", "description":"Tampa real estate,FL real estate,2704 West Bay Avenue, Tamp Click Now!", "url":"http:\/\/report-site.com\/i\/fg","display_url":"http:\/\/minderbyrd.smithandassociates.com","category": "","type": "Run Of Network","campaign_name":"","targets":["US"]},"Keywords": { "ron_bid": "0.0010"}}]';
            //Update Campaign
            

            if ($fiq_id === false) {

                $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
                $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
                $this->CI->email->subject('Report-Site: FIQ Campaign Creation Error');
                $this->CI->email->message("** FIND IT QUICK CAMPAIGN CREATE ERROR **<br/><br/>Campaign IO: {$campaign['io']}<br/>\nCampaign Name: {$campaign['name']}<br/>Date: " . date("Y-m-d H:i:s"));
                $this->CI->email->send();
                
                if ($campaign['total_records']){
                    $this->CI->V2_ad_model->id = $id;
                    $this->CI->V2_ad_model->remove();
                }
                return false;
            } 
            else {
                // decode the json returned by the FIQ create ad
                $fiq_id = json_decode($fiq_id, true)[0]; 
            }

            //print_r($fiq_id);
            // update the campaign with ad ID
            $activate_campaign = false;

            if (isset($fiq_id['Ad']['id'])) {

                // pause the ad while we "operate" on it - we dont need it to go live with incorrect info
                $this->fiq->pause_ad($fiq_id['Ad']['id']);         
                //update ad in our db, set network id 
                $this->V2_ad_model->update($id, ['network_creative_id' => $fiq_id['Ad']['id']]);

                // set target, budget, schedule and cap
                $total_clicks = ceil((($campaign['fire_open_pixel'] == "Y") ? $campaign['total_opens'] : $campaign['total_clicks']));

                if ($total_clicks > 40000)  {
                    $set_schedule_result = $this->fiq->set_schedule($fiq_id['Ad']['id']); // use default (all hours of the day)
                } 
                else {
                    // this is a truely randomized schedule, its horrible though.
                    $schedule = "&schedule[0_8]=" . mt_rand(0,1) . "&schedule[0_9]=" . mt_rand(0,1) . "&schedule[0_10]=" . mt_rand(0,1) . "&schedule[0_11]=" . mt_rand(0,1) . "&schedule[0_12]=" . mt_rand(0,1) . "&schedule[0_13]=" . mt_rand(0,1) . "&schedule[0_14]=" . mt_rand(0,1) . "&schedule[0_15]=" . mt_rand(0,1) . "&schedule[0_16]=" . mt_rand(0,1) . "&schedule[0_17]=" . mt_rand(0,1) . "&schedule[0_18]=" . mt_rand(0,1) . "&schedule[0_19]=" . mt_rand(0,1) . "&schedule[0_20]=" . mt_rand(0,1) . "&schedule[0_21]=" . mt_rand(0,1) . "&schedule[0_22]=" . mt_rand(0,1) . "&schedule[0_23]=" . mt_rand(0,1) . "&";
                    $schedule .= "&schedule[1_8]=" . mt_rand(0,1) . "&schedule[1_9]=" . mt_rand(0,1) . "&schedule[1_10]=" . mt_rand(0,1) . "&schedule[1_11]=" . mt_rand(0,1) . "&schedule[1_12]=" . mt_rand(0,1) . "&schedule[1_13]=" . mt_rand(0,1) . "&schedule[1_14]=" . mt_rand(0,1) . "&schedule[1_15]=" . mt_rand(0,1) . "&schedule[1_16]=" . mt_rand(0,1) . "&schedule[1_17]=" . mt_rand(0,1) . "&schedule[1_18]=" . mt_rand(0,1) . "&schedule[1_19]=" . mt_rand(0,1) . "&schedule[1_20]=" . mt_rand(0,1) . "&schedule[1_21]=" . mt_rand(0,1) . "&schedule[1_22]=" . mt_rand(0,1) . "&schedule[1_23]=" . mt_rand(0,1) . "&";
                    $schedule .= "&schedule[2_8]=" . mt_rand(0,1) . "&schedule[2_9]=" . mt_rand(0,1) . "&schedule[2_10]=" . mt_rand(0,1) . "&schedule[2_11]=" . mt_rand(0,1) . "&schedule[2_12]=" . mt_rand(0,1) . "&schedule[2_13]=" . mt_rand(0,1) . "&schedule[2_14]=" . mt_rand(0,1) . "&schedule[2_15]=" . mt_rand(0,1) . "&schedule[2_16]=" . mt_rand(0,1) . "&schedule[2_17]=" . mt_rand(0,1) . "&schedule[2_18]=" . mt_rand(0,1) . "&schedule[2_19]=" . mt_rand(0,1) . "&schedule[2_20]=" . mt_rand(0,1) . "&schedule[2_21]=" . mt_rand(0,1) . "&schedule[2_22]=" . mt_rand(0,1) . "&schedule[2_23]=" . mt_rand(0,1) . "&";
                    $schedule .= "&schedule[3_8]=" . mt_rand(0,1) . "&schedule[3_9]=" . mt_rand(0,1) . "&schedule[3_10]=" . mt_rand(0,1) . "&schedule[3_11]=" . mt_rand(0,1) . "&schedule[3_12]=" . mt_rand(0,1) . "&schedule[3_13]=" . mt_rand(0,1) . "&schedule[3_14]=" . mt_rand(0,1) . "&schedule[3_15]=" . mt_rand(0,1) . "&schedule[3_16]=" . mt_rand(0,1) . "&schedule[3_17]=" . mt_rand(0,1) . "&schedule[3_18]=" . mt_rand(0,1) . "&schedule[3_19]=" . mt_rand(0,1) . "&schedule[3_20]=" . mt_rand(0,1) . "&schedule[3_21]=" . mt_rand(0,1) . "&schedule[3_22]=" . mt_rand(0,1) . "&schedule[3_23]=" . mt_rand(0,1) . "&";
                    $schedule .= "&schedule[4_8]=" . mt_rand(0,1) . "&schedule[4_9]=" . mt_rand(0,1) . "&schedule[4_10]=" . mt_rand(0,1) . "&schedule[4_11]=" . mt_rand(0,1) . "&schedule[4_12]=" . mt_rand(0,1) . "&schedule[4_13]=" . mt_rand(0,1) . "&schedule[4_14]=" . mt_rand(0,1) . "&schedule[4_15]=" . mt_rand(0,1) . "&schedule[4_16]=" . mt_rand(0,1) . "&schedule[4_17]=" . mt_rand(0,1) . "&schedule[4_18]=" . mt_rand(0,1) . "&schedule[4_19]=" . mt_rand(0,1) . "&schedule[4_20]=" . mt_rand(0,1) . "&schedule[4_21]=" . mt_rand(0,1) . "&schedule[4_22]=" . mt_rand(0,1) . "&schedule[4_23]=" . mt_rand(0,1) . "&";
                    $schedule .= "&schedule[5_8]=" . mt_rand(0,1) . "&schedule[5_9]=" . mt_rand(0,1) . "&schedule[5_10]=" . mt_rand(0,1) . "&schedule[5_11]=" . mt_rand(0,1) . "&schedule[5_12]=" . mt_rand(0,1) . "&schedule[5_13]=" . mt_rand(0,1) . "&schedule[5_14]=" . mt_rand(0,1) . "&schedule[5_15]=" . mt_rand(0,1) . "&schedule[5_16]=" . mt_rand(0,1) . "&schedule[5_17]=" . mt_rand(0,1) . "&schedule[5_18]=" . mt_rand(0,1) . "&schedule[5_19]=" . mt_rand(0,1) . "&schedule[5_20]=" . mt_rand(0,1) . "&schedule[5_21]=" . mt_rand(0,1) . "&schedule[5_22]=" . mt_rand(0,1) . "&schedule[5_23]=" . mt_rand(0,1) . "&";
                    $schedule .= "&schedule[6_8]=" . mt_rand(0,1) . "&schedule[6_9]=" . mt_rand(0,1) . "&schedule[6_10]=" . mt_rand(0,1) . "&schedule[6_11]=" . mt_rand(0,1) . "&schedule[6_12]=" . mt_rand(0,1) . "&schedule[6_13]=" . mt_rand(0,1) . "&schedule[6_14]=" . mt_rand(0,1) . "&schedule[6_15]=" . mt_rand(0,1) . "&schedule[6_16]=" . mt_rand(0,1) . "&schedule[6_17]=" . mt_rand(0,1) . "&schedule[6_18]=" . mt_rand(0,1) . "&schedule[6_19]=" . mt_rand(0,1) . "&schedule[6_20]=" . mt_rand(0,1) . "&schedule[6_21]=" . mt_rand(0,1) . "&schedule[6_22]=" . mt_rand(0,1) . "&schedule[6_23]=" . mt_rand(0,1) . "&";

                    $set_schedule_result = $this->fiq->set_schedule($fiq_id['Ad']['id'], true); // randomize schedule

                }

                $set_cap_result = $this->fiq->set_cap($fiq_id['Ad']['id'], $spend_per_day);
                $set_bid_result = $this->fiq->set_bid($fiq_id['Ad']['id'], $initial_bid);

                // fix our little issue where people are submitting blank geotype orders.
                if ((string)$campaign['geotype'] == "")    {

                    if ($campaign['zip'] != "" && (strlen($campaign['state']) >= 2) && $campaign['country'] == "")    {
                        $campaign['geotype'] = "POSTALCODE";
                    } 
                    elseif ($campaign['state'] != "" && $campaign['zip'] == "")  {
                        $campaign['geotype'] = "STATE";
                    } 
                    elseif ($campaign['country'] != "" && $campaign['state'] == "" && $campaign['zip'] == "") {
                        $campaign['geotype'] = "COUNTRY";
                    } 
                    else {
                        $campaign['geotype'] = ""; 
                    }
                }

                switch(strtoupper($campaign['geotype'])){
                    default:
                    case "COUNTRY":
                        $set_target_result = $this->fiq->set_target($fiq_id['Ad']['id'], [$campaign['country']]);
                        $criteria_value = $campaign['country'];
                        $activate_campaign = true;
                        break;

                    case "STATE":
                        $state = array();
                        foreach(explode(",", $campaign['state']) as $s)  {
                            if ($s == "")
                                continue;

                            $state[] = "{$campaign['country']}/{$s}";
                        }
                        $set_target_result = $this->fiq->set_target($fiq_id['Ad']['id'], $state);
                        $criteria_value = $state;
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
                        $set_target_result = $this->fiq->set_target($fiq_id['Ad']['id'], $resultGeo);
                        $criteria_value = $resultGeo;
                        $activate_campaign = true;
                        }
                    break;
                }

                // resume ad
                if ($activate_campaign === true)   {
                    $this->fiq->resume_ad($fiq_id['Ad']['id']);

                    $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
                    $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
                    $this->CI->email->subject("Report-Site: FIQ New Campaign Creation [{$campaign['io']}]");
                    $this->CI->email->message("** FIND IT QUICK NEW CAMPAIGN CREATION **<br/><br/>Campaign IO: {$campaign['io']}<br/>\nCampaign Name: {$campaign['create_name']}<br/>Date: " . date("Y-m-d H:i:s"));
                    $this->CI->email->send();

                } 
                else {
                    // need to make ad creative status paused

                    $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
                    $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
                    $this->CI->email->subject("Report-Site: FIQ GEO Campaign Creation Error [{$campaign['io']}]");
                    $this->CI->email->message("** FIND IT QUICK GEO CAMPAIGN CREATE ERROR **<br/><br/>Campaign IO: {$campaign['io']}<br/>\nCampaign Name: {$campaign['create_name']}<br/>Date: " . date("Y-m-d H:i:s"));
                    $this->CI->email->send();
                }

                $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $criteria_value, $campaign['geotype']);
            }
        }        
         * 
         */    
    }
    
    public function update ($campaign = null, $type = null){
        
        $this->load->model('V2_ad_model');
        
        $ads = $this->V2_ad_model->get_ads_by_campaign_id($campaign['id']);
            
        foreach ($ads as $ad){
        
            switch ($type){
                case 'location':

                    if ((string)$campaign['geotype'] == "")    {

                        if ($campaign['zip'] != "" && (strlen($campaign['state']) >= 2) && $campaign['country'] == "")    {
                            $campaign['geotype'] = "POSTALCODE";
                        } 
                        elseif ($campaign['state'] != "" && $campaign['zip'] == "")  {
                            $campaign['geotype'] = "STATE";
                        } 
                        elseif ($campaign['country'] != "" && $campaign['state'] == "" && $campaign['zip'] == "") {
                            $campaign['geotype'] = "COUNTRY";
                        } 
                        else {
                            $campaign['geotype'] = ""; 
                        }
                    }
                    
                    $initial_bid = 0.0028;
                    //jason code need to change for v2
                    switch(strtoupper($campaign['geotype'])){
                        default:
                        case "COUNTRY":

                            $set_target_result = $this->fiq->set_target($ad['network_creative_id'], [$campaign['country']]);
                            $criteria_value = $campaign['country'];
                            $activate_campaign = true;
                            break;

                        case "STATE":
                            $state = array();
                            foreach(explode(",", $campaign['state']) as $s)  {
                                if ($s == "")
                                    continue;

                                $state[] = "{$campaign['country']}/{$s}";
                            }
                            $set_target_result = $this->fiq->set_target($ad['network_creative_id'], $state);
                            $criteria_value = $state;
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
                            $set_target_result = $this->fiq->set_target($ad['network_creative_id'], $resultGeo);
                            $criteria_value = $resultGeo;
                            $activate_campaign = true;
                            }
                        break;
                    }
                    
                    //hard code for test
                    
                    $initial_bid = 0.0001;
                    
                    $set_bid_result = $this->fiq->set_bid($ad['network_creative_id'], $initial_bid);
                    
                    break;

                case 'budget':
                    /*
                    $clicks_per_day = ceil((($campaign['fire_open_pixel'] == "Y") ? $campaign['total_opens'] : $campaign['total_clicks']) / 2.85);
                    $initial_bid = (strtoupper($campaign['geotype']) == "COUNTRY") ? 0.0018 : 0.0028;
                    $spend_per_day = sprintf("%.2f", $clicks_per_day * $initial_bid); // default bid, including overage
                    $spend_per_day = ($spend_per_day > 4) ? $spend_per_day : 4.00; // minimum spend per day is $4.00

                    /*
                     * Hard code for test;
                     */
                    
                    
                    
                    $initial_bid = 0.0001;
                    $spend_per_day = 1;

                    $this->fiq->set_cap($ad['network_creative_id'], $spend_per_day);
                    $this->fiq->set_bid($ad['network_creative_id'], $initial_bid);
                    
                    break;

            }
        
        }
                
    }
    
    public function update_ad_status( $ad = null){  
        
        if ($ad['creative_status'] == "PAUSED"){
            $this->fiq->pause_ad($ad['network_creative_id']); 
        }
        else {
            $this->fiq->resume_ad($ad['network_creative_id']);
        }    
    }
    
    public function update_ad($campaign, $ad_id) {
                        
        $this->load->model('V2_ad_model');
        
        $ad = $this->CI->V2_ad_model->get_by_id($ad_id);
        
        $this->fiq->pause_ad($ad['network_creative_id']);
        
        $network_id = $this->create_ad_in_fiq($campaign, $ad);
        
        $this->V2_ad_model->update($ad['id'], ['network_creative_id' => $network_id, 'creative_is_active' => 'Y']);
        
    }
    
    public function delete (){
        
    }
    
    public function pause_campaign($campaign){
    
        if (!$campaign){
            throw new exception("campaign required");
        }
        
        $this->CI->load->model('V2_ad_model');
        
        $ads = $this->CI->V2_ad_model->get_ads_by_campaign_id($campaign['id']);
        
        foreach ($ads as $ad){
            
            $this->fiq->pause_ad($ad['network_creative_id']);
        }
               
    }

    public function update_campaign_status($campaign) {

        $this->CI->load->model('V2_ad_model');
        
        $ads = $this->CI->V2_ad_model->get_ads_by_campaign_id($campaign['id']); 
                
        foreach ($ads as $ad){
            
            if ($campaign['network_campaign_status'] == "PAUSED"){
                $this->fiq->pause_ad($ad['network_creative_id']); 
            }
            else {
                $this->fiq->resume_ad($ad['network_creative_id']);
            } 
        }
        
    }

    public function update_cap_per_hour($campaign) {


    }

    public function update_schedule($ad) {
        $this->fiq->set_schedule($ad['network_creative_id']); 
    }

    public function update_bid($campaign) {
        
        $this->CI->load->model('V2_ad_model');
        
        $ads = $this->CI->V2_ad_model->get_ads_by_campaign_id($campaign['id']);
        
        foreach ($ads as $ad){
            
            $this->fiq->set_bid($ad['network_creative_id'], $ad['bid']);
        }
        
    }

    public function update_daily_cap($ad, $network_name) {


    }

    public function get_ad_report($ad, $network_name, $sdate = "", $edate = "") {


        if ($sdate == "")
            throw new exception ("get_ad_report: sdate required");

        if ($edate == "")
            $reqDate = $sdate;
        else
            $reqDate = "{$sdate}+-+{$edate}";

       // $url = "https://www.findit-quick.com/customer/index.php?r=advertiser/api/report&date={$reqDate}&id={$id}";

    }

    public function get_ads_impressions() {

        $this->CI->load->model('V2_ad_model');

        $ads = $this->CI->V2_ad_model->get_active_campaigns_ads_by_network_id($this->network_id);
        
        // real ad id 96883926033042
        $this->CI->load->model('V2_campclick_impression_model');
        foreach ($ads as $ad){

            $report = $this->fiq->get_ad_report($ad['network_creative_id']);
            if ($report) {
                $report = json_decode($report, true);
                $report = $report[date("Y-m-d")];
                
                if($report["impressions"]){
                    $data_for_update = array();
                    $data_for_update['ad_id'] = $ad['id'];
                    $data_for_update['network_id'] = $this->network_id;
                    $data_for_update['campaign_id'] = $ad['campaign_id'];
                    $data_for_update['impressions_count'] = (int)$report["impressions"] - (int)$ad['impressions_count'];

                    $this->CI->V2_campclick_impression_model->create($data_for_update);
                }
            }
        }

    }
    
    public function get_campaigns_cost() {

        $this->CI->load->model('V2_ad_model');

        $ads = $this->CI->V2_ad_model->get_active_campaigns_ads_by_network_id($this->network_id);
        // real ad id 96883926033042
        if(!$ads) {
            return false;
        }
        //adds the ad performance report into the database
        $this->CI->load->model('V2_campaign_cost_model');
        $campaign_id = $ads[0]['campaign_id'];
        $cost = 0;
        foreach ($ads as $key => $ad){

            $report = $this->fiq->get_ad_report($ad['network_creative_id']);
            if ($report) {
                $report = json_decode($report, true);
                $report = $report[date("Y-m-d")];
                
                if($ad['campaign_id'] == $campaign_id) {
                    // last campaign cost calculated but didn't save in this loop
                    $cost = $cost + $report['total'];
                } else {
                    // save campaign total cost in db
                    $data_for_update = array();
                    $data_for_update['network_id'] = $this->network_id;
                    $data_for_update['campaign_id'] = $campaign_id;
                    $data_for_update['cost'] = $cost;

                    if ($cost){
                        $this->CI->V2_campaign_cost_model->create($data_for_update);
                    }
                    // reset cost and change campaign_id
                    $cost = $report['total'];
                    $campaign_id = $ad['campaign_id'];
                }
            }
        }
        // save last campaign cost in db
        $data_for_update = array();
        $data_for_update['network_id'] = $this->network_id;
        $data_for_update['campaign_id'] = $campaign_id;
        $data_for_update['cost'] = $cost;
        
        if ($cost){
            $this->CI->V2_campaign_cost_model->create($data_for_update);
        }
    }
    
    public function create_ad($campaign, $ad_id){
        
        $ad = $this->CI->V2_ad_model->get_by_id($ad_id);
        $this->create_ad_in_fiq($campaign, $ad);

        $this->update_bid($campaign);

    }
    
    public function create_ad_in_fiq($campaign, $ad){
        
        $this->CI->load->model('V2_ad_model');
        $this->CI->load->model('Zip_model');
        $this->CI->load->model('V2_master_campaign_model');
        $this->CI->load->model('V2_campaign_network_criteria_rel_model');
             
        // fix our little issue where people are submitting blank geotype orders.
        if ((string)$campaign['geotype'] == "")    {

            if ($campaign['zip'] != "" && (strlen($campaign['state']) >= 2) && $campaign['country'] == "")    {
                $campaign['geotype'] = "POSTALCODE";
            } 
            elseif ($campaign['state'] != "" && $campaign['zip'] == "")  {
                $campaign['geotype'] = "STATE";
            } 
            elseif ($campaign['country'] != "" && $campaign['state'] == "" && $campaign['zip'] == "") {
                $campaign['geotype'] = "COUNTRY";
            } 
            else {
                $campaign['geotype'] = ""; 
            }
        }
        
        $ad['daily_cap'] = 1;
        $ad['bid'] = 0.0001;

        $fiq_id = $this->fiq->create_ad($campaign['io'], $ad, $campaign['vertical']);
        
        $error = false;
        
        if ($fiq_id === false) {

            $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
            $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
            $this->CI->email->subject('Report-Site: FIQ Campaign Creation Error');
            $this->CI->email->message("** FIND IT QUICK CAMPAIGN CREATE ERROR **<br/><br/>Campaign IO: {$campaign['io']}<br/>\nCampaign Name: {$campaign['name']}<br/>Date: " . date("Y-m-d H:i:s"));
            $this->CI->email->send();

        } 
        else {

            $fiq_id = json_decode($fiq_id, true);

            if (isset($fiq_id['errors'])){

                $error_data_for_update["approval_status"] = 'DISAPPROVED';
                $error_data_for_update["disapproval_reasons"] = json_encode($fiq_id['errors']);
                $this->CI->V2_ad_model->update($ad['id'], $error_data_for_update);
                $error = true;
            }
            else {
                $fiq_id = $fiq_id[0];
            }
            
            $activate_campaign = false;
            
            if (isset($fiq_id['Ad']['id'])) {

                // pause the ad while we "operate" on it - we dont need it to go live with incorrect info
                $this->fiq->pause_ad($fiq_id['Ad']['id']);         
                //update ad in our db, set network id 
                $this->V2_ad_model->update($ad['id'], ['network_creative_id' => $fiq_id['Ad']['id'], 'creative_is_active' => 'Y']);

                // set target, budget, schedule and cap
                $this->fiq->set_schedule($fiq_id['Ad']['id']); // use default (all hours of the day)
                
                $this->fiq->set_cap($fiq_id['Ad']['id'], $ad['daily_cap']);
                $this->fiq->set_bid($fiq_id['Ad']['id'], $ad['bid']);
                switch(strtoupper($campaign['geotype'])){
                    default:
                    case "COUNTRY":
                        $set_target_result = $this->fiq->set_target($fiq_id['Ad']['id'], [$campaign['country']]);
                        $criteria_value = $campaign['country'];
                        $activate_campaign = true;
                        break;

                    case "STATE":
                        $state = array();
                        
                        foreach(explode(",", $campaign['state']) as $s)  {
                            if ($s == "")
                                continue;

                            $state[] = "{$campaign['country']}/{$s}";
                        }
                        $set_target_result = $this->fiq->set_target($fiq_id['Ad']['id'], $state);
                        $criteria_value = $campaign['state'];
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
                        $set_target_result = $this->fiq->set_target($fiq_id['Ad']['id'], $resultGeo);
                        $criteria_value = $campaign['zip'];
                        $activate_campaign = true;
                        }
                    break;
                }

                // resume ad
                if ($activate_campaign === true)   {
                    
                    $this->fiq->resume_ad($fiq_id['Ad']['id']);

                    $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
                    $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
                    $this->CI->email->subject("Report-Site: FIQ New Campaign Creation [{$campaign['io']}]");
                    $this->CI->email->message("** FIND IT QUICK NEW CAMPAIGN CREATION **<br/><br/>Campaign IO: {$campaign['io']}<br/>\nCampaign Name: {$campaign['create_name']}<br/>Date: " . date("Y-m-d H:i:s"));
                    $this->CI->email->send();


                } 
                else {
                    // need to make ad creative status paused

                    $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
                    $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
                    $this->CI->email->subject("Report-Site: FIQ GEO Campaign Creation Error [{$campaign['io']}]");
                    $this->CI->email->message("** FIND IT QUICK GEO CAMPAIGN CREATE ERROR **<br/><br/>Campaign IO: {$campaign['io']}<br/>\nCampaign Name: {$campaign['create_name']}<br/>Date: " . date("Y-m-d H:i:s"));
                    $this->CI->email->send();
                }
                
                $this->CI->V2_campaign_network_criteria_rel_model->insert_by_type($campaign, $criteria_value, $campaign['geotype']);

                return $fiq_id['Ad']['id'];
            }
            
        }
              
    }

}