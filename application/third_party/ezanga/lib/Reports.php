<?php
include_once('AdPad.php');
##################################################################################
#                            Service Reports
##################################################################################
#
# Author    : Asim Masood
# Created On: 2014-03-31
# Version   : 1.0
# CopyRight : Ezanga.com
#
# Details   : API LIB class to access Report Services
##################################################################################

class Reports extends AdPad   {

    protected $request_params = array();

    /***************************************************************************
        @Description: Init Constructor.
        @params     : null
        @return     : null
    ***************************************************************************/
    public function __construct(){
        
        // call parent first
        parent::__construct();
        
        // request params for Report list service - check doc for details
        $this->request_params['account'] = array(
            'type'                      => '',
            'datefrom'                  => '',
            'dateto'                    => '',
            'media_type'                => '',
            'customer_status'           => '',

            'minhits'                   => 0,  // exclusive to keywords click or calls

            'maxrecords'                => 0,  // exclusive to geo
            'region'                    => '', // exclusive to geo: country, us_states, us_cities
            
            'maxrecords'                => 0, // exclusive to source
            'minhits'                   => 0, // exclusive to source
            
        );
        
        $this->request_params['campaign'] = array(
            'type'                      => '',
            'datefrom'                  => '',
            'dateto'                    => '',
            'media_type'                => '',
            'cplist'                    => '',

            'minhits'                   => '', // exclusive for keywords
            'customer_status'           => '', // exclusive for keywords
            
            'maxrecords'                => '', // exclusive for geo
            'region'                    => '', // exclusive to geo: country, us_states, us_cities
            
            'maxrecords'                => '', // exclsuive for src
            'minhits'                   => '', // exclsuive for src
            
            
            
            
        );
        
    }


################################################################################
#   ADPAD SERVICE - reports/account/<type>
################################################################################
    /***************************************************************************
        @Description: Get Account reports List.
        @params     : null
        @return     : null
    ***************************************************************************/
    public function exeAccount()   {
        $this->fetch();
    }



    /***************************************************************************
        @Description: Setter For Account Report List.
        @params     : $param_name<string|array>, $param_value<string|interget>
        @return     : null
    ***************************************************************************/
    public function setAccount($param_name=null, $param_value=null) {
        $this->setParams($param_name, $param_value);
    }



    /***************************************************************************
        @Description: Getter For Account Report List.
        @params     : $param_name<string>
        @return     : $param_value<string|interget>
    ***************************************************************************/
    public function getAccount($param_name=null) {
        return $this->getParams($param_name);
    }
    
################################################################################



################################################################################
#   ADPAD SERVICE - reports/campaign/<type>
################################################################################
    /***************************************************************************
        @Description: Get Account reports List.
        @params     : null
        @return     : null
    ***************************************************************************/
    public function exeCampaign()   {
        $this->fetch();
    }



    /***************************************************************************
        @Description: Setter For Account Report List.
        @params     : $param_name<string|array>, $param_value<string|interget>
        @return     : null
    ***************************************************************************/
    public function setCampaign($param_name=null, $param_value=null) {
        $this->setParams($param_name, $param_value);
    }



    /***************************************************************************
        @Description: Getter For Account Report List.
        @params     : $param_name<string>
        @return     : $param_value<string|interget>
    ***************************************************************************/
    public function getCampaign($param_name=null) {
        return $this->getParams($param_name);
    }
    
################################################################################
}


/* $report = new Reports();
$report->setAccount('type', 'transactions');
$report->exeAccount();

print_r($report->response_raw);
print "\n"; */
