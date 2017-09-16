<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Webservice_api {
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

	public function validate(array $post_data)
    {
    	$this->CI->load->model("V2_master_campaign_model");

        $required = array("so", "io", "campaign_name", "creative_html", "geo_type", "geo_data", "max_budget", "daily_budget", "timepart", "max_impressions");

        $error = array();

        // make sure required params are present
        foreach($required as $k) {
            if ($post_data[$k] == "") {
                $error[] = "{$k} required";
            }
        }

        // make sure we have a unique IO being passed
        $io_exists = $this->CI->V2_master_campaign_model->check_io($post_data['io']);
        if ($io_exists === true) {
            $error[] = "io exists, try another";
        }

        switch(strtoupper($post_data['geo_type'])) {
            case "COUNTRY":
            case "STATE":
            case "POSTALCODE":
                break;

            default:
                $error[] = "geo_type invalid type";
                break;
        }

        if ( count($error) > 0 ) {
            return array('result' => array("status" => "ERROR", "message" => $error, "timestamp" => date("Y-m-d H:i:s")), 'code' => 400);
        }

        return true;
    }
}