<?php

##################################################################################
#                            Service Campaigns
##################################################################################
#
# Author    : Asim Masood
# Created On: 2014-03-14
# Version   : 1.2
# Revision  : Change Log
# 0.2 [2014-03-26]
#           : [updated] Wizard, has changed the param list names
#           : [added] Geo Targeting.
#
# 0.1 [2014-03-20]
#           : [added] Timetargeting, Trafficfilter, Uafilter
#           : [updated] Removes typos from comments and reshape them
#
# CopyRight : Ezanga.com
#
# Details   : API LIB class to access Campaigns Services
##################################################################################

class Campaigns extends AdPad   {

    protected $request_params = array();



    /***************************************************************************
        @brief : Init Constructor.
        @params     : null
        @return     : null
    ***************************************************************************/
    public function __construct(){
        
        // call parent first
        parent::__construct();
        
        // request params for Campaigns list service - check doc for details
        $this->request_params['list'] = array(
            'pagenum'           => 0,
            'pagesize'          => 10,
            'filterflags'       => 'enabled',
            'media_type'        => 'ppc',
            'datefrom'          => '',
            'dateto'            => '',
            'qsearch'           => '',
            'sortby'            => '',
            'sortbydirection'   => '',
        );


        // request params for Campaigns update service - check doc for details
        $this->request_params['update'] = array(
            'customer_status'   => '',
            'name'              => '',
            'description'       => '',
            'user_refno'        => '',
            'daily_budget'      => '',
            'max_cpc'           => '',
            'campaignid'        => '',
            'start_timestamp'   => '',
            'stop_timestamp'    => '',
       );

       
        // request params for Campaigns wizard service - check doc for details
        $this->request_params['wizard'] = array(
            'cp_start_timestamp'    => '',
            'cp_stop_timestamp'    => '',
            'cp_name'               => '',
            'cp_customer_status'    => '',
            'cp_daily_budget'       => '',
            'cp_max_cpc'            => '',
            'media_type'            => '',
            'geocc'                 => 'us,ca',
            'geostates'             => '',
            'geocities'             => '',
            'geozipcodes'           => '',
            'geodmacodes'           => '',
            'ca_geostates'          => '',
            'ca_geocities'          => '',
            'ca_geozipcodes'        => '',
            'au_geocities'          => '',
            'au_geostates'          => '',
            'adgroup_name'          => 'Ad Group 1',
            'adgroup_adult_flag'    => 0,
            'adv_keywords'          => '',
            'adv_title'             => '',
            'adv_description'       => '',
            'adv_visible_url'       => '',
            'adv_click_url'         => '',
            'phn_name'              => '',
            'phn_address'           => '',
            'phn_city'              => '',
            'phn_state'             => '',
            'phn_zip'               => '',
            'phn_phone_number'      => ''
       );
       
       // request to update time targeting
        $this->request_params['timetargeting'] = array(
            'campaignid'      => '',
            'start_timestamp' => @date('Y-m-d', time()),
            'stop_timestamp'  => @date('Y-m-d', time()) . ' 23:59:59',
            'scheduling01'    =>'sun[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]mon[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]tue[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]wed[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]thu[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]fri[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]sat[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]',
       );


       // request to update traffic filter
        $this->request_params['trafficfilter'] = array(
            'campaignid'        => '',
            'whitelist'         => '',  # tab delimited
            'blacklist'         => '',  # tab delimited
            'applyacfilter'     => '1', # default to check
            'applycpfilter'     => '',  # default to uncheck
       );

       // request to update ua filter
        $this->request_params['uafilter'] = array(
            'campaignid'        => '',
            'whitelist'         => '',  # tab delimited
            'blacklist'         => '',  # tab delimited
            'applyacfilter'     => '1', # default to check
            'applycpfilter'     => '',  # default to uncheck
       );
       
       // request to update ua filter
        $this->request_params['geotargeting'] = array(
            'campaignid'        => '',
            'geocc'             => 'us,ca', # Geo country names
            'geostates'         => '',      # Geo state names
            'geocities'         => '',      # Geo city names
            'geozipcodes'       => '',      # Geo zipcodes
            'geodmacodes'       => '',      # Geo dma codes
            'ca_geostates'      => '',      # Geo Canada states
            'ca_geocities'      => '',      # Geo Canada cities
            'ca_geozipcodes'    => '',      # Geo Canada zipcodes
            'au_geocities'      => '',      # Geo Austrailian Cities
            'au_geostates'      => '',      # Geo Austrailian States
            
       );
       
       
    }


################################################################################
#   ADPAD SERVICE - campaigns/list
################################################################################
    /***************************************************************************
        @Description: Get Campaign List.
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
#   ADPAD SERVICE - campaigns/update
################################################################################
    /***************************************************************************
        @Description: Update a Campaign.
        @params     : null
        @return     : null
    ***************************************************************************/
    public function exeUpdate()   {
        $this->fetch();
    }



    /***************************************************************************
        @brief : Setter For Update.
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
#   ADPAD SERVICE - campaigns/wizard
################################################################################
    /***************************************************************************
        @Description: Create a new campaign with Wizard
        @params     : null
        @return     : null
    ***************************************************************************/
    public function exeWizard()   {
        $this->fetch();
    }



    /***************************************************************************
        @Description: Setter For Wizard.
        @params     : $param_name<string|array>, $param_value<string|interget>
        @return     : null
    ***************************************************************************/
    public function setWizard($param_name=null, $param_value=null) {
        $this->setParams($param_name, $param_value);
    }



    /***************************************************************************
        @Description: Getter For Wizard.
        @params     : $param_name<string>
        @return     : $param_value<string|interget>
    ***************************************************************************/
    public function getWizard($param_name=null) {
        return $this->getParams($param_name);
    }
################################################################################



################################################################################
#   ADPAD SERVICE - campaigns/timetargeting
################################################################################
    /***************************************************************************
        @Description: Update Time Targeting
        @params     : null
        @return     : null
    ***************************************************************************/
    public function exeTimetargeting()   {
        $this->fetch();
    }

    /***************************************************************************
        @Description: Update Geo Targeting
        @params     : null
        @return     : null
    ***************************************************************************/
    public function exeGeoTargeting()   {
        $this->fetch();
    }


    /***************************************************************************
        @Description: Setter For Time Targeting.
        @params     : $param_name<string|array>, $param_value<string|interget>
        @return     : null
    ***************************************************************************/
    public function setTimetargeting($param_name=null, $param_value=null) {
        $this->setParams($param_name, $param_value);
    }



    /***************************************************************************
        @Description: Getter For Time Targeting.
        @params     : $param_name<string>
        @return     : $param_value<string|interget>
    ***************************************************************************/
    public function getTimetargeting($param_name=null) {
        return $this->getParams($param_name);
    }
################################################################################



################################################################################
#   ADPAD SERVICE - campaigns/trafficfilter
################################################################################
    /***************************************************************************
        @Description: Update Traffic Filter
        @params     : null
        @return     : null
    ***************************************************************************/
    public function exeTrafficfilter()   {
        $this->fetch();
    }



    /***************************************************************************
        @Description: Setter For Trafficfilter.
        @params     : $param_name<string|array>, $param_value<string|interget>
        @return     : null
    ***************************************************************************/
    public function setTrafficfilter($param_name=null, $param_value=null) {
        $this->setParams($param_name, $param_value);
    }
    
    /***************************************************************************
        @Description: Setter For Geo Targeting.
        @params     : $param_name<string|array>, $param_value<string|interget>
        @return     : null
    ***************************************************************************/
    public function setGeoTargeting($param_name=null, $param_value=null) {
        $this->setParams($param_name, $param_value);
    }



    /***************************************************************************
        @Description: Getter For Trafficfilter.
        @params     : $param_name<string>
        @return     : $param_value<string|interget>
    ***************************************************************************/
    public function getTrafficfilter($param_name=null) {
        return $this->getParams($param_name);
    }
################################################################################



################################################################################
#   ADPAD SERVICE - campaigns/uafilter
################################################################################
    /***************************************************************************
        @Description: Update UA Filter
        @params     : null
        @return     : null
    ***************************************************************************/
    public function exeUafilter()   {
        $this->fetch();
    }
    
    /***************************************************************************
        @Description: Update GEO Targetting
        @params     : null
        @return     : null
    ***************************************************************************/
    public function exeGeofilter()   {
        $this->fetch();
    }


    /***************************************************************************
        @Description: Setter For Uafilter.
        @params     : $param_name<string|array>, $param_value<string|interget>
        @return     : null
    ***************************************************************************/
    public function setUafilter($param_name=null, $param_value=null) {
        $this->setParams($param_name, $param_value);
    }


    /***************************************************************************
        @Description: Getter For GeoTargeting.
        @params     : $param_name<string>
        @return     : $param_value<string|interget>
    ***************************************************************************/
    public function getGeoTargeting($param_name=null) {
        return $this->getParams($param_name);
    }
################################################################################
}