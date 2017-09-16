<?php

##################################################################################
#                            Service Keywords
##################################################################################
#
# Author    : Asim Masood
# Created On: 2014-03-14
# Version   : 1.0
# CopyRight : Ezanga.com
#
# Details   : API LIB class to access Keywords Services
##################################################################################

class Keywords extends AdPad   {

    protected $request_params = array();



    /***************************************************************************
        @Description: Init Constructor.
        @params     : null
        @return     : null
    ***************************************************************************/
    public function __construct(){
        
        // call parent first
        parent::__construct();
        
        // request params for Keywords list service - check doc for details
        $this->request_params['list'] = array(
            'pagenum'           => 0,
            'pagesize'          =>10,
            'campaignid'        => '',
            'adgroupid'         => '',
            'filterflags'       => 'enabled',
            'qsearch'           => '',
        );


        // request params for  Keywords update service - check doc for details
        $this->request_params['update'] = array(
            'campaign_id' => '',
            'adgroupid'   => '',
            'media_type'  => '',
            'click_url'   => '',
            'matchtype'   => 'broad',
            'max_cpc'     => '',
            'termid'      => '',
       );

       
        // request params for Keywords create service - check doc for details
        $this->request_params['create'] = array(
            'campaign_id'   => '',
            'adgroupid'     => '',
            'max_cpc'       => '',
            'click_url'     => '',
            'keywordlist'   => '',
            'matchtype'     => 'broad',
            'batchid'       => '',
       );
    }


################################################################################
#   ADPAD SERVICE - keywords/list
################################################################################
    /***************************************************************************
        @Description: Get Keyword List.
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
#   ADPAD SERVICE - keywords/update
################################################################################
    /***************************************************************************
        @Description: Update Keywords.
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
#   ADPAD SERVICE - keywords/create
################################################################################
    /***************************************************************************
        @Description: Create Keywords.
        @params     : null
        @return     : null
    ***************************************************************************/
    public function exeCreate()   {
        $this->fetch();
    }



    /***************************************************************************
        @Description: Setter For Create Keywords.
        @params     : $param_name<string|array>, $param_value<string|interget>
        @return     : null
    ***************************************************************************/
    public function setCreate($param_name=null, $param_value=null) {
        $this->setParams($param_name, $param_value);
    }



    /***************************************************************************
        @Description: Getter For Create Keywords.
        @params     : $param_name<string>
        @return     : $param_value<string|interget>
    ***************************************************************************/
    public function getCreate($param_name=null) {
        return $this->getParams($param_name);
    }
################################################################################
}