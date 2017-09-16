<?php 

class Zip_model extends CI_Model	{

	private $collection = "zip_mapping";
	protected $CI;

	private $zip;
	private $zipType;
	private $primary_city;
	private $acceptable_cities;
	private $state;
	private $county;
	private $area_codes;
	private $latitude;
	private $longitude;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		
	}

    public function load_zip($file_location = "/tmp/zips.txt")	{
    	$file = file_get_contents($file_location);
    	$lines = explode("\n", $file);
    	
    	$this->CI->db->query("DELETE FROM {$this->collection}");
   
    	foreach($lines as $l)	{
    		$arry = str_getcsv($l);
    		
    		$insert = array(
    			"zip" => $arry[0],
    			"zipType" => $arry[1],
    			"primary_city" => $arry[2],
    			"acceptable_cities" => $arry[3],
    			"state" => $arry[5],
    			"county" => $arry[6],
    			"area_codes" => $arry[8],
    			"latitude" => $arry[9],
    			"longitude" => $arry[10]
    		);
    		
    		$this->CI->db->insert($this->collection, $insert);
    	}
    }
  	
    public function load_geo($file_location = "/tmp/geos.csv")	{
    	$file = file_get_contents($file_location);
    	$lines = explode("\n", $file);
    	 
    	$this->CI->db->query("DELETE FROM geo_data");
    	 
    	foreach($lines as $l)	{
    		$arry = explode("\t", $l);

    		$insert = array(
    			"country" => $arry[0],
    			"state" => $arry[1],
    			"city" => $arry[2]
    		);

    		$this->CI->db->insert("geo_data", $insert);
    	}
    }
    
    public function match_zip_to_geo($zip = "", $radius = 0)	{
    	$zip = $this->CI->db->query("SELECT * FROM zip_mapping WHERE zip='{$zip}' LIMIT 1");
    	$z = $zip->row_array();
    	
    	$geo = $this->CI->db->query("SELECT * FROM geo_data WHERE state='{$z['state']}' AND city='{$z['primary_city']}' LIMIT 1");
    	
    	if ($geo->num_rows() > 0)  {
    	    $geo = $geo->row_array();
    	    $geo['latitude'] = $z['latitude'];
    	    $geo['longitude'] = $z['longitude'];
    	    $geo['radius'] = $radius;
    	    $geo['final_tgt'] = $geo['country'] . "/" . $geo['state'] . "/" . $geo['city'];
    	     
    	    return $geo;
    	} else {
    	    return array();
    	}
    }

	public function match_all_zip_to_geo($zip = "", $radius = 0)	{
		$trimed_zip = trim($zip,',');
		
		// remove non-numeric characters
		$trimed_zip = preg_replace("/[^0-9,]/", "", $trimed_zip);
		
		// remove double commas
		$trimed_zip = preg_replace("/,+/", ",", $trimed_zip);
		
		$zips = $this->CI->db->query("SELECT * FROM zip_mapping WHERE zip IN ({$trimed_zip})")->result_array();
		$source_locations = array();

		foreach($zips as $zip) {
			$geo = $this->CI->db->query("SELECT * FROM geo_data WHERE state='{$zip['state']}' AND city='{$zip['primary_city']}' LIMIT 1");

			if ($geo->num_rows() > 0) {
				$geo = $geo->row_array();
				$geo['latitude'] = $zip['latitude'];
				$geo['longitude'] = $zip['longitude'];
				$geo['radius'] = $radius;
				$geo['final_tgt'] = $geo['country'] . "/" . $geo['state'] . "/" . $geo['city'];

				$source_locations[] = $geo;
			}
		}

		return $source_locations;
	}

    public function find_locations($zip = "", $distance = 30, $zipOnly = false)	{
    	$zip = $this->CI->db->query("SELECT * FROM zip_mapping WHERE zip='{$zip}' LIMIT 1");
    	if ($zip->num_rows() > 0)  {
    	    $z = $zip->row_array();
    	     
    	    $sql = "SELECT zip, (3959 * acos ( cos ( radians({$z['latitude']}) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians({$z['longitude']}) ) + sin ( radians({$z['latitude']}) ) * sin( radians( latitude ) ) ) ) AS distance FROM zip_mapping HAVING distance < {$distance} ORDER BY distance LIMIT 5000";
    	     
    	    if ($z['latitude'] != "" && $z['longitude'] != "") {
    	        $result = $this->CI->db->query($sql);
    	        
    	        $finalArry = array();
    	        foreach($result->result_array() as $r)	{
    	            if ($zipOnly === false) {
        	            $geo = $this->match_zip_to_geo($r['zip']);
        	            	
        	            if (! empty($geo))
        	                $finalArry[$geo['country'] . "_" . $geo['state'] . "_" . $geo['city']] = $geo;
    	            } else {
    	                $finalArry[] = $r['zip'];
    	            }
    	        }
    	        	
    	        return $finalArry;
    	    }
    	}
    	
    	return array();
    }
    
    public function match_city_to_zip($city = "", $state = "") {
        $r = $this->CI->db->query("SELECT zip FROM zip_mapping WHERE primary_city='{$city}' AND state='{$state}' GROUP BY zip");
        if ($r->num_rows() > 0) { 
            foreach($r->result_array() as $rr)  {
                $zip[] = $rr['zip'];
            }
            return $zip;
        } else {
            return array();
        }
    }
    
    public function get_campaign_by_geo($zip_list = array())  {
        $zip_list = implode("|", $zip_list);
        $r = $this->CI->db->query("SELECT tpc.io FROM take5_pending_campaigns tpc JOIN campclick_campaigns cc ON cc.io=tpc.io WHERE cc.campaign_is_started='Y' AND cc.campaign_is_complete='N' AND cc.is_geo='Y' AND tpc.geotype='postalcode' AND tpc.zip REGEXP '{$zip_list}'");
         
        if ($r->num_rows() > 0)    {
            return $r->result_array();
        } else {
            return array();
        }
    }
    
    public function get_campaign_by_state($state_list = "")  {
        $r = $this->CI->db->query("SELECT tpc.io FROM take5_pending_campaigns tpc JOIN campclick_campaigns cc ON cc.io=tpc.io WHERE cc.campaign_is_started='Y' AND cc.campaign_is_complete='N' AND cc.is_geo='Y' AND tpc.geotype='state' AND tpc.state REGEXP '{$state_list}'");
         
        if ($r->num_rows() > 0)    {
            return $r->result_array();
        } else {
            return array();
        }
    }
    
    
    public function get_campaign_zipcode($ppc_local_ad_id)   {
        $r = $this->CI->db->query("SELECT tpc.zip FROM take5_pending_campaigns tpc JOIN campclick_campaigns cc ON cc.io=tpc.io WHERE tpc.geotype='postalcode' AND cc.ppc_network_ad_id='{$ppc_local_ad_id}' LIMIT 1");
        
        if ($r->num_rows() > 0) {
            $zip = $r->row_array();
            return implode(" ", explode(",", $zip['zip']));
        } else {
            return array();
        }
    }

    public function get_campaign_radius($ppc_local_ad_id)   {
        $r = $this->CI->db->query("SELECT tpc.radius FROM take5_pending_campaigns tpc JOIN campclick_campaigns cc ON cc.io=tpc.io WHERE tpc.geotype='postalcode' AND cc.ppc_network_ad_id='{$ppc_local_ad_id}' LIMIT 1");
    
        if ($r->num_rows() > 0) {
            $zip = $r->row_array();
            return $zip['radius'];
        } else {
            return "10";
        }
    }
    
    public function set_campaign_radius($radius, $io)   {
        $this->CI->db->update("take5_pending_campaigns", array("radius" => $radius), array("io" => $io));
    }
    
    public function set_campaign_ziplist($ziplist, $io) {
        $this->CI->db->update("take5_pending_campaigns", array("zip" => $ziplist), array("io" => $io));
    }
    
	public function __get($name)	{
		return $this->$name;
	}

	public function __set($name, $value)	{
		$this->$name = $value;
	}

	public function __isset($name)	{
		return isset($this->$name);
	}

	public function get_kordinate_by_zip($zip){

		$results = $this->CI->db->select('latitude, longitude')
		->from($this->collection)
		->where_in('zip', explode("," , $zip))
		->get()->result_array();

//		foreach ($results as $key => $result){
//
//			$results[$key]['radius'] = $radius;
//			$results[$key]['distance_unit'] = 'mile';
//
//		}
		return $results;



	}

}



?>
