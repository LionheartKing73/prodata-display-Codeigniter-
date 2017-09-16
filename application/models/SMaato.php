<?php

class Ad_list_model extends CI_Model {

    function __construct() {
        $this->load->database();
        // Call the Model constructor
        parent::__construct();
    }
}