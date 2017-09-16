<?php
class V2_campclick_conversion_model extends CI_Model {

    protected $CI;
    private $collection = 'v2_campclick_conversion';

    private $id;
    private $campaign_id; // ref: v2_master_campaigns.id
    private $cookie_id;
    private $conversion_value; // default: 1.00

    function __construct() {
        parent::__construct();
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    function test()
    {
        debug(generate_uuid());
    }

}
?>