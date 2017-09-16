<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 *
 * EXAMPLE OF HOW REST WORKS:
 *
 *  index.php/webservice/tracking/id/1/format/json
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

class Webservice extends REST_Controller    {

	function __construct()
    {
        // Construct our parent class
        parent::__construct();

        $this->CI =& get_instance();

        $this->CI->load->model("V2_master_campaign_model");
        $this->load->library("Webservice_api");

        // Configure limits on our controller methods. Ensure
        // you have created the 'limits' table and enabled 'limits'
        // within application/config/rest.php
        $this->methods['campaigns_get']['limit'] = 1000; //500 requests per hour per user/key
        $this->methods['campaign_post']['limit'] = 200; //200 requests per hour per user/key
    }


    public function pixel_media_post()
    {
        $post_data = $this->post();

        // validate
        $validation_res = $this->webservice_api->validate($post_data);
        if ( $validate_res !== true ) {
            return $this->response($validation_res['result'], $validation_res['code']);
        }

        // we should now have clean data. lets move forward.


    }
}