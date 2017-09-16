<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * 
 * EXAMPLE OF HOW REST WORKS:
 * 
 *  index.php/rtb_api/tracking/id/1/format/json
 *             /           /     \         \
 *     controller     resource   param    output format
 *
 *
 *  Supported Output format: xml, json, csv, html, php, serialize
 *
 *
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Rtb_api extends REST_Controller    {
	function __construct()
    {
        // Construct our parent class
        parent::__construct();

        $this->load->model("Take5_Campaign_Pending_Model");
        $this->load->model("Take5_Pending_Campaign_Links_Model");
        $this->load->model("Take5_Pending_Campaign_Openpixel_Model");
        
        // Configure limits on our controller methods. Ensure
        // you have created the 'limits' table and enabled 'limits'
        // within application/config/rest.php
        $this->methods['campaigns_get']['limit'] = 1000; //500 requests per hour per user/key
        $this->methods['campaign_post']['limit'] = 99999200; //200 requests per hour per user/key
    }
    
    /*
     * POST campaign
     * io - UNIQUE, required
     * geotype - required, values: COUNTRY (US/CA), STATE, POSTALCODE
     * country - (required: COUNTRY) - US | CA
     * state - (required: STATE) - space separated list of 2 char state codes
     * zip - (required: ZIPCODE) - space separated list of 5 char zip codes (MAX: 5000)
     * radius - (required: ZIPCODE) - INT in MILES for radius
     * fire_open_pixel - required: Y|N (default to 'N')
     * total_opens - required: INT
     * total_clicks - required: INT
     * total_records - required: INT
     * campaign_name - requried: STRING - human campaign name
     * campaign_start_datetime - required: DATE-STRING (YYYY-MM-DD HH:MM:SS)
     * campaign_is_approved - required: DEFAULT to 'N', or pass 'Y'
     * vertical - required: ENUM
     * links - required: ARRAY
     *      destination_url - string
     *      original_url - string
     *      click_count (0 = unlimited clicks, OR INT)
     *      (Example: link[0..n]['destination_url'])
     * open_pixel - (required: fire_open_pixel = Y) - ARRAY
     *      pixel_url - (img string for open pixel to fire)
     * 
     */
    function campaign_post() {
        $required = array("io", "geotype", "vertical", "fire_open_pixel", "total_opens", "total_clicks", "total_records", "campaign_name", "campaign_start_datetime", "campaign_is_approved");
        $error = array();
        
        foreach($required as $r)    {
            if ($this->input->post($r) == "")   {
                $error[] = $r . " required";
            }
        }
        
        // filter out the non-alpha numeric stuff (spaces too)
        $io = preg_replace("/[^A-Za-z0-9 ]/", '', $this->input->post("io"));
        
        // check to make sure that the IO doesnt already exist
        $this->Take5_Campaign_Pending_Model->io = $io;
        $io2 = $this->Take5_Campaign_Pending_Model->get_campaign_id_from_io(false);
        $io3 = $this->Take5_Campaign_Pending_Model->get_campaign_id_from_io(true);
        $io3 = $io2 + $io3;
        if ($io3 > 0)    {
            $error[] = "Duplicate IO";
        }
        
        if (! empty($error))    {
            // invalid / missing properties
            $this->response(array("status" => "ERROR", "message" => $error), 404);
        } else {
            // check geo & required properties
            $zip = $states = $country = ""; // setup for blanks
            
            switch(strtoupper($this->input->post("geotype")))   {
                case "COUNTRY":
                    if (! ($this->input->post("country") == "US" || $this->input->post("country") == "CA")) {
                        $this->response(array("status" => "ERROR", "message" => "geotype: country; invalid country code: US|CA"));
                    } else {
                        $country = $this->input->post("country");
                    }
                    break;
                    
                case "STATE":
                    if (! (count(explode(" ", $this->input->post("state"))) > 0 && $this->input->post("state") != "") || $this->input->post("country") == "")   {
                        $this->response(array("status" => "ERROR", "message" => "geotype: state; at least one US/CA state code required"));
                    } else {
                        $country = $this->input->post("country");
                        $states = "";
                        $stateList = explode(" ", $this->input->post("state"));
                        foreach($stateList as $s)   {
                            $states .= $s . ",";
                        }
                    }
                    break;
                    
                case "POSTALCODE":
                    if (! (count(explode(" ", $this->input->post("zip"))) > 0 && $this->input->post("zip") != "") || $this->input->post("country") == "" || (int)$this->input->post("radius") < 10) {
                        $this->response(array("status" => "ERROR", "message" => "geotype: postalcode; at least one postalcode (zip) required and country. Radius must be >= 10"));
                    } else {
                        $zip_tmp = $this->input->post("zip");
                        $zip_tmp = preg_replace("/,/", " ", $zip_tmp); // remove the commas
                        $zip_tmp = preg_replace("/\s+/", " ", $zip_tmp); // remove the multiple spaces
                        $zip = implode(",", explode(" ", $zip_tmp)); // implode to something usable
                        $country = $this->input->post("country");
                        $radius = (int)$this->input->post("radius");
                    }
                    break;
                    
                default:
                        $country = "US";
                    break;
            }
            
            if (strtoupper($this->input->post("fire_open_pixel") == "Y")) {
                $max_clicks = $this->input->post("total_opens");
            } else {
                $max_clicks = $this->input->post("total_clicks");
            }

            //print_r("MAX CLICKS: {$max_clicks}");
            
            if (strtoupper($this->input->post("geotype")) == "COUNTRY")   {
                $budget = ($this->Take5_Campaign_Pending_Model->cpc_national * $max_clicks);
            } else {
                $budget = ($this->Take5_Campaign_Pending_Model->cpc_geo * $max_clicks);
            }
            // round up to nearest "5" for accounting purposes.
            $budget = round(($budget+5/2)/5)*5;
            $budget = ($budget > $this->Take5_Campaign_Pending_Model->MINIMUM_ORDER_AMOUNT) ? $budget : $this->Take5_Campaign_Pending_Model->MINIMUM_ORDER_AMOUNT;
            
            //print_r("BUDGET: {$budget}");

            $insert = array(
                "total_records" => $this->input->post("total_records"),
                //"percentage_opens" => $this->percentage_opens,
                //"percentage_clicks" => $this->percentage_clicks,
                //"percentage_bounce" => $this->percentage_bounce,
                "total_clicks" => (int)$this->input->post("total_clicks"),
                "total_opens" => (int)$this->input->post("total_opens"),
                //"total_bounces"=> $this->total_bounces,
                //"message_result" => $this->message_result,
                "io" => $io,
                "create_name" => $this->input->post("campaign_name"),
                "vendor" => "2",
                "domain" => "1",
                "campaign_start_datetime" => date("Y-m-d H:i:00", strtotime(($this->input->post("campaign_start_datetime") != "") ? $this->input->post("campaign_start_datetime") : date("Y-m-d H:i:s"))),
                "geotype" => ($this->input->post("geotype") == "") ? "country" : $this->input->post("geotype"),
                "special_instructions" => "API Created via RTB_API",
                "fire_open_pixel" => $this->input->post("fire_open_pixel"),
                "budget" => $budget,
                "campaign_is_approved" => ($this->input->post("campaign_is_approved") != "") ? $this->input->post("campaign_is_approved") : "N",
                "cap_per_hour" => ceil($max_clicks * 0.15),
                "vertical" => $this->input->post("vertical"),
                "userid" => "5",
                "record_created" => date("Y-m-d H:i:s"),
                 
                // geo specific info
                "country" => $country,
                "radius" => ($radius > 10) ? $radius : 10,
                "state" => $states,
                "zip" => $zip,
            );

            //print_r($insert);

            // dont like doing the direct db stuff, but its needed here
            $this->db->insert("take5_pending_campaigns", $insert);
            $id = $this->db->insert_id();
            
            //print_r("CAMPAIGN ID#: {$id}");
            

            if ($id > 0)    {
                // create links
                
                if (! count($this->input->post("links")) > 0)   {
                    $this->db->delete("take5_pending_campaigns", array("id" => $id)); // remove the pending IO since its not valid as there were no included links.
                    $this->response(array("status" => "ERROR", "message" => "Links Required"), 404);
                } else {
                    $this->Take5_Pending_Campaign_Links_Model->pending_campaign_id = (int)$id;

                    foreach($this->input->post("links") as $l)  {
                        $this->Take5_Pending_Campaign_Links_Model->destination_url = $l['link'];
                        $this->Take5_Pending_Campaign_Links_Model->original_url = $l['link'];
                        $this->Take5_Pending_Campaign_Links_Model->click_count = (int)$l['count'];
                        $link_id = $this->Take5_Pending_Campaign_Links_Model->create();
                    }
                }
                
                if ($this->input->post("fire_open_pixel") == "Y")   {

                    // create pixels
                    $this->Take5_Pending_Campaign_Openpixel_Model->pending_campaign_id = (int)$id;
                    foreach($this->input->post("open_pixel") as $p)   {
                        print_r($p);
                        $this->Take5_Pending_Campaign_Openpixel_Model->pixel_url = $p['pixel_url'];
                        $this->Take5_Pending_Campaign_Openpixel_Model->create();
                    }
                }

                if ($this->input->post("campaign_is_approved") == "Y")  {
                    print "campaign is approved";
                    
                    $this->Take5_Pending_Campaign_Model->id = $id;
                    $this->Take5_Pending_Campaign_Model->convert_order();
                }
                
                print json_encode(array("status" => "SUCCESS", "message" => "Campaign Created", "budget" => sprintf("%.2f", $budget)));
                
//                $this->response(array("status" => "STATUS", "message" => "Campaign Created", "budget" => sprintf("%.2f", $budget)));
            }
        }
    }
    
    function campaigns_get()    {
        // update user in system
        if (! $this->get("io")) {
            $this->response(array("status" => "ERROR"), 404);
        } else {
            $get = $this->get();
            $io = $get['io'];

            $this->response(array("status" => "SUCCESS", "report" => $report, "timestamp" => date("Y-m-d H:i:s")), 200);
        }
    }


}