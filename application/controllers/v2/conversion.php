<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author jkorkin
 * @cookieName ProDataMediaConversionTracker
 * @cookieValue GUID unique identifier hashed in database
 *
 */

class Conversion extends CI_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model("v2_conversion_model");
    }
    
    public function tracker()   {
        
        // 1- Detect our cookie
        $cookie = $this->input->cookie('ProDataMediaConversionTracker', true);
        
        // 2- Short circuit if cookie doesnt exist
        if ($cookie === false) {
            print json_encode(array("status" => "NO_COOKIE"));
            exit;
        }
        
        // 3- Lookup cookie in database and append data
        $this->v2_conversion_model->conversionValue = ($this->input->post("conversionValue") != "") ? $this->input->post("conversionValue") : "1.00";
        $this->v2_conversion_model->userAgent = $this->input->post("userAgent");
        $this->v2_conversion_model->pageUrl = $this->input->post("pageUrl");
        $this->v2_conversion_model->apiKey = $this->input->post("apiKey");
        $guidInfo = $this->v2_conversion_model->lookup_guid();
        
        // 4- If cookie is matched in database within last 90-days, log the conversion otherwise ignore
        if ($guidInfo === false) {
            print json_encode(array("status" => "NO_COOKIE"));
            exit;
        } else {
            // update the conversion table
            $this->v2_conversion_model->guid = $guidInfo['uuid'];
            $this->v2_conversion_model->store_conversion();
        }
        
        print json_encode(array("status" => "CONVERSION"));
        exit;
    }
}