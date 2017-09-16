<?php

##################################################################################
#                            Service AdListings
##################################################################################
#
# Author    : Asim Masood
# Created On: 2014-03-14
# Version   : 1.1
# CopyRight : Ezanga.com
#
# Details   : API LIB class to access AdListings Services for Calls
# 0.1 [2014-03-25] = After QA done by Ejaz
#           : [added] Put two params for sortby and sortorder for list sub-service.

##################################################################################

class AdListingCalls extends AdPad   {

    protected $request_params = array();



    /***************************************************************************
        @Description: Init Constructor.
        @params     : null
        @return     : null
    ***************************************************************************/
    public function __construct(){
        
        // call parent first
        parent::__construct();
        
        // request params for Calls AdListings Call list service - check doc for details
        $this->request_params['list'] = array(
            'pagenum'           => 0,
            'pagesize'          => 10,
            'adgroupid'         => '',
            'filterflags'       => 'enabled',
            'qsearch'           => '',
            'sortdatafield'     => 'advid',
            'sortorder'         => 'asc',
        );


        // request params for Calls AdListings update service - check doc for details
        $this->request_params['update'] = array(
            'advid'             => '',
            'campaign_id'       => '',
            'adgroupid'         => '',
            'phn_name'         => '',
            'phn_address'      => '',
            'phn_city'         => '',
            'phn_state'        => '',
            'phn_zip'          => '',
            'phn_phone_number' => '',
            'customer_status'  => '',
       );

        // request params for Calls AdListings create service - check doc for details
        $this->request_params['create'] = array(
            'campaign_id'      => '',
            'adgroupid'        => '',
            'phn_name'         => '',
            'phn_address'      => '',
            'phn_city'         => '',
            'phn_state'        => '',
            'phn_zip'          => '',
            'phn_phone_number' => '',
            'customer_status'  => '',
       );
    }


################################################################################
#   ADPAD SERVICE - adlistingcalls/list
################################################################################
    /***************************************************************************
        @Description: Get Calls AdListing List.
        @params     : null
        @return     : null
    ***************************************************************************/
    public function exeList()   {
        $this->fetch();
    }



    /***************************************************************************
        @Description: Setter For List.
        @params     : $param_name<string|array>, $param_value<string|interget>
        @return     : null
    ***************************************************************************/
    public function setList($param_name=null, $param_value=null) {
        $this->setParams($param_name, $param_value);
    }



    /***************************************************************************
        @Description: Getter For List.
        @params     : $param_name<string>
        @return     : $param_value<string|interget>
    ***************************************************************************/
    public function getList($param_name=null) {
        return $this->getParams($param_name);
    }
################################################################################



################################################################################
#   ADPAD SERVICE - adlistingcalls/update
################################################################################
    /***************************************************************************
        @Description: Update Calls AdListing.
        @params     : null
        @return     : null
    ***************************************************************************/
    public function exeUpdate()   {
        $this->fetch();
    }



    /***************************************************************************
        @Description: Setter For Update.
        @params     : $param_name<string|array>, $param_value<string|interget>
        @return     : null
    ***************************************************************************/
    public function setUpdate($param_name=null, $param_value=null) {
        $this->setParams($param_name, $param_value);
    }



    /***************************************************************************
        @Description: Getter For Update.
        @params     : $param_name<string>
        @return     : $param_value<string|interget>
    ***************************************************************************/
    public function getUpdate($param_name=null) {
        return $this->getParams($param_name);
    }
################################################################################



################################################################################
#   ADPAD SERVICE - adlistingcalls/create
################################################################################
    /***************************************************************************
        @Description: Create Calls Adlisting.
        @params     : null
        @return     : null
    ***************************************************************************/
    public function exeCreate()   {
        $this->fetch();
    }



    /***************************************************************************
        @Description: Setter For Create.
        @params     : $param_name<string|array>, $param_value<string|interget>
        @return     : null
    ***************************************************************************/
    public function setCreate($param_name=null, $param_value=null) {
        $this->setParams($param_name, $param_value);
    }



    /***************************************************************************
        @Description: Getter For Create.
        @params     : $param_name<string>
        @return     : $param_value<string|interget>
    ***************************************************************************/
    public function getCreate($param_name=null) {
        return $this->getParams($param_name);
    }
################################################################################
}