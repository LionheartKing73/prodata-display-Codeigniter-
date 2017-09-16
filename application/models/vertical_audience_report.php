<?php
class Vertical_audience_report extends CI_Model {
    function __construct()
    {
        $this->load->database(); 
        // Call the Model constructor
        parent::__construct();
    }

    public function insert(array $data){
        $this->db->insert('vertical_audience_report', $data);
    }
}
?>
