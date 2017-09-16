<?php defined('BASEPATH') or exit('No direct script access allowed');
// SELECT gip.*, l.* FROM geo_ip_block gip JOIN geo_ip_location l ON l.geoname_id = gip.geoname_id WHERE network_last_integer >= INET_ATON('96.56.56.86') LIMIT 1
class Geolookup extends CI_Controller {
    
    private $ip_address;
    protected $CI;
    
    public function __construct() {
        $this->CI = get_instance();
        $this->CI->load->database();
    }
    
    public function lookup() {
        
        $r = $this->CI->db->query("SELECT gip.*, l.* FROM geo_ip_block gip JOIN geo_ip_location l ON l.geoname_id = gip.geoname_id WHERE network_last_integer >= INET_ATON('{$this->ip_address}') LIMIT 1");
        
        if ($r->num_rows() > 0) {
            $result = $r->row_array();

            if ($result['subdivision_1_iso_code'] != "") {
                return array("country" => $result['country_iso_code'], "state" => $result['subdivision_1_iso_code'], "city" => $result['city_name'], "postal_code" => $result['postal_code'], 'lat' => $result['latitude'], 'lng' => $result['longitude']);
            }
        }
        
        // no match
        return false;
    }    
    
    public function __get($name) {
        return $this->$name;
    }
    
    public function __set($name, $value) {
        $this->$name = $value;
    }
    
}
?>