<?php

##################################################################################
#                            Service Campaigns
##################################################################################
#
# Author    : Asim Masood
# Created On: 2014-03-14
# Version   : 1.1
# CopyRight : Ezanga.com
#
# Details   : API LIB class to access Campaigns Services
# 0.1 [2014-03-31]
#           : [updated] now api_url will be fetch from method so that other child classes can override them
##################################################################################
define('ERROR_CONFIG_NOT_FOUND', 'System Configuration File Not Found.');

class AdPadException extends Exception {
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class AdPad {

    protected $api_key          = '';        # Client API KEY
    protected $api_username     = '';        # Client Username
    protected $api_url          = '';        # API URL
    
    protected $service          = '';        # Services [campaigns, adgroups, adlists, keywords]
    protected $sub_service      = '';        # Sub Services [list, create, update, ....]

    public $curl_info           = '';        # Curl Info for Debugning
    public $response            = array();   # Holding AdPad Response
    public $response_raw         = '';        # Unparsed Response
    public $error               = '';        # Holding Errors
    public $hasError            = '';        # If Has errors

    private $json_errors        = array();   # Hold json Errors
    /***************************************************************************
        @Description: Init Constructor.
        @params     : null
        @return     : null
    ***************************************************************************/
    public function __construct(){

        // Load Configuration
        if(!is_file("../config/adpad.conf")) { $this->raiseError(ERROR_CONFIG_NOT_FOUND); }

        $config = parse_ini_file("../config/adpad.conf");
        $this->api_key = $config['api_key'];
        $this->api_username = $config['api_username'];
        $this->api_url = rtrim($config['api_url'], '/');
        
        // json errors references
        $this->json_errors[JSON_ERROR_DEPTH] = ' - Maximum stack depth exceeded';
        $this->json_errors[JSON_ERROR_CTRL_CHAR] = ' - Underflow or the modes mismatch';
        $this->json_errors[JSON_ERROR_SYNTAX] = ' - Unexpected control character found';
        $this->json_errors[JSON_ERROR_UTF8] = ' - Syntax error, malformed JSON';
        $this->json_errors[JSON_ERROR_STATE_MISMATCH] = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
    }



    /***************************************************************************
        @Description: Execute the sub-services with backtrace
        @params     : $sub_service<string>
        @return     : $json<string>
    ***************************************************************************/
    protected function fetch($sub_service=null){
    
        $this->hasError = false;
        $this->error = '';
        
        // built service & sub-service uri
        $traces = debug_backtrace();    # always 1 subsscript to get caller fun
        $this->service = strtolower($traces[1]['class']);
        
        # skip three letters like getList to list, setUpdate to update
        $this->sub_service = substr(strtolower($traces[1]['function']), 3);
        $this->api_url .= $this->getApiUri();

        $this->request_params[$this->sub_service]['apikey'] = $this->api_key;
        $this->request_params[$this->sub_service]['username'] = $this->api_username;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request_params[$this->sub_service]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        $result = curl_exec($ch);
        
        // parse curl & hold errors
        $this->curl_info = curl_getinfo($ch);
        curl_close($ch);
        if($this->curl_info['http_code'] != 200) {
            $this->error = "Failed to connect to the site. Please check \$this->curl_info variable for more details.";
            $this->hasError = true;
            return;
        }

        // parse json & hold errors if any
        $this->response_raw = $result;
        $this->response = json_decode($result);
        if(isset($this->json_errors[json_last_error()])) {
            $this->error = "Json Error from AdPad [" . $this->json_errors[json_last_error()] . "]";
            $this->hasError = true;
        }
    }



    /***************************************************************************
        @Description: Set params for sub-service.
        @params     : $param_name=<mixed>, $param_value=<string|int>
        @return     : null
    ***************************************************************************/
    protected function setParams($param_name=null, $param_value=null){
    
        // built service & sub-service uri
        $traces = debug_backtrace();    # always 1 subsscript to get caller fun
        $service = strtolower($traces[1]['class']);
        
        # skip three letters like getList to list, setUpdate to update
        $sub_service = substr(strtolower($traces[1]['function']), 3);
        
        if(is_array($param_name)){
            foreach($param_name as $param => $value) {
                if(isset($this->request_params[$sub_service][$param])) {
                    $this->request_params[$sub_service][$param]=$value;
                }
            }

            return;
        }
        
        if(isset($this->request_params[$sub_service][$param_name])) {
            $this->request_params[$sub_service][$param_name]=$param_value;
        }
    }



    /***************************************************************************
        @Description: Get params from sub-service.
        @params     : $param_name=<string|int>
        @return     : $param_value=<string|int>
    ***************************************************************************/
    protected function getParams($param_name=null){
    
        // built service & sub-service uri
        $traces = debug_backtrace();    # always 1 subsscript to get caller fun
        $service = strtolower($traces[1]['class']);
        
        # skip three letters like getList to list, setUpdate to update
        $sub_service = substr(strtolower($traces[1]['function']), 3);
        
        if(isset($this->request_params[$sub_service][$param_name])) {
            return $this->request_params[$sub_service][$param_name];
        }
    }


    /***************************************************************************
        @Description: Get the api URL.
        @params     : null
        @return     : $api_url=<string>
    ***************************************************************************/
    protected function getApiUri() {
    
        if($this->service == 'reports') {
            $report_type = $this->request_params[$this->sub_service]['type'];
            return "/$this->service/$this->sub_service/$report_type/";
        }
        
        return "/$this->service/$this->sub_service/";
    }

    /***************************************************************************
        @Description: Raise Custom Error.
        @params     : $error_code<int>
        @return     : null
    ***************************************************************************/
    protected function raiseError($error_code=null){
        // make 1==0 to stop Exceptions
        if(1==1)    {
            throw new AdPadException($error_code);
        }
    }
}