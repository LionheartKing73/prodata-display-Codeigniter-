<?php

/**
 * @author eZanga.com, Inc.
 * @author Joe Rodichok
 * @copyright 2014
 * @updated 2015-01-31
 */


class Ezanga {

    private static $url = "https://adpad-api.ezanga.com";
    private static $header_map = array(
        200 => " - Success",
        300 => " - Authentication keys are missing",
        303 => " - Access denied to AdPad services",
        304 => " - Service not found ({srv})",
        401 => " - Failed authentication",
        500 => " - Internal server error",
        800 => " - AdPad exception, see \"msg\" for more details"
    );
    
    public static $raw_result = "";
    public static $errors = array();
    public static $feedback = array();
    
    public $api_key = "";
    public $username = "";
    
    private static function make_request($service = null, $subservice = null, $parameters = array()) {
        
        $parameters["apikey"] = self::$api_key;
        $parameters["username"] = self::$username;
        
        $api_url = self::$url . "/{$service}/{$subservice}/";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        $result = curl_exec($ch);      

        self::$feedback["curl"] = curl_getinfo($ch);
        curl_close($ch);
        
        if(self::$feedback["curl"]["http_code"] == 200) {
            
            self::$raw_result = $result;
            $result = json_decode($result);
            
            if($result->code == 200) {
                return($result);
            } elseif($result->code == 800 && $result->msg = "Record not found" && $subservice = "list") {
                // Add return of num rows for just this
                $result->totalrows = 0;
                $result->pagenum = 0;                
                $result->rows = array();
                return($result);
            } else {
                if(isset(self::$header_map[$result->code])) {
                    if($result->code == 304) { self::$header_map[$result->code] = str_replace("{srv}","/{$service}/{$subservice}/",self::$header_map[$result->code]); }
                    self::$errors[] = self::$header_map[$result->code];
                } else {
                    self::$errors[] = "Unknown error: $result->code";
                }
                return(false);   
            }
        } else {
            self::$errors[] = " - Request failed";
            return(false);
        }
    }
    

	public static function validate_and_request($service = null, $subservice = null, $parameters = array()) {
		if(empty(self::$api_key)) {
			self::$errors[] = "API Key is missing";
		} elseif(empty(self::$username)) {
			self::$errors[] = "Email missing";
		} elseif(empty($service) || is_null($service)) {
			self::$errors[] = "Service must be defined";
		} elseif (empty($subservice) || is_null($subservice)) {
			self::$errors[] = "Subservice must be defined!";
		} elseif(!is_array($parameters)) {
			self::$errors[] = "Parameters must be an array!";
		} elseif(count($parameters) == 0) {
			self::$errors[] = "Parameters must have a value!";
		}
		
		// If errors return false, if no errors make the request
		if(count(self::$errors == 0)) {
			return self::make_request($service,$subservice,$parameters);
		} else {
			return false;
		}
	}

}

?>
