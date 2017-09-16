<?php 

class Trafficshape_model extends CI_Model	{

	protected $CI;
	
	private $io;
	private $campaign_duration_days = 4;
	private $standard_deviation = 1;
	private $sample_points_per_day = 24; // every 60 minutes
	private $campaign_max_clicks;
	private $start_click_offset = 0;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		
		$this->CI->load->Model("Ppcnetworks_model");
		$this->CI->load->Model("Log_model");
		
		$this->standard_deviation = $this->campaign_duration_days;
	}
	
	public function create_db_table()  {
	    $sql = "CREATE TABLE traffic_shape_master (";
	    $sql .= "id INT NOT NULL auto_increment,";
	    $sql .= "io VARCHAR(16) NOT NULL,";
	    $sql .= "campaign_duration_days INT NOT NULL DEFAULT 7,";
	    $sql .= "sample_points_per_day INT NOT NULL DEFAULT 24,";
	    $sql .= "current_sample_point INT NOT NULL DEFAULT 1,"; // corresponds to the current sample point
	    $sql .= "current_sample_time TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',"; // corresponds to the current MAXIMUM sample time
	    $sql .= "total_clicks INT NOT NULL DEFAULT 0,"; // this is the total clicks, regardless of whether they passed the normalization curve application (we'll use this for bid control later on)
	    $sql .= "progress_clicks INT NOT NULL DEFAULT 0,"; // this is the progress clicks, regardless of whether they passed the normalization curve application (we'll use this for bid control later on)
	    	  
	    for($x=1; $x<=7; $x++)    {
	        for($y=1; $y<=24; $y++)    {
    	       $sql .= "d{$x}_ts{$y}_cur_clicks INT DEFAULT 0,"; // current clicks for current time slot
	           $sql .= "d{$x}_ts{$y}_max_clicks INT DEFAULT 0,"; // max clicks for current time slot
	           $sql .= "d{$x}_ts{$y}_sum_clicks INT DEFAULT 0,"; // how many clicks we *should* be at for entire campaign
	        }
	    }
	    
	    $sql = rtrim($sql, ","); // remove the "extra" comma from the loop above
	    $sql .= ", primary key (id));";
	    $this->CI->db->query($sql);
	    $this->CI->db->query("ALTER TABLE traffic_shape_master ADD INDEX io (io)");
	}
	
	/**
	 * process_click($io) - processes the click against the normal distribution curve; returns boolean for whether to allow the click to go through or not
	 * @param string $io
	 * @return boolean
	 */
	public function process_click($io = "")    {
	    if ($io == "")
	        return false;
	    
	    $this->io = $io;
	    $timeslot = $this->get_timeslot();
	    
	    if ($timeslot === false)   {
	        return false;
	    } else {
	        if ((int)$timeslot['current_clicks'] < (int)$timeslot['max_clicks'] && (int)$timeslot['progress_clicks'] < (int)$timeslot['current_clicks']+1) {
	            $where = array("io" => $this->io);
	            $update = array(
	                "progress_clicks" => (int)$timeslot['progress_clicks']+1,
	                "d{$timeslot['how_far_in_days']}_ts{$timeslot['how_far_in_segments']}_cur_clicks" => (int)$timeslot['current_clicks']+1
	            );
	            
	            $this->CI->db->update("traffic_shape_master", $update, $where);
	            return true;
	        } else {
	            // update only the total clicks to reflect we registered a click outside of the distribution curve
	            $this->CI->db->update("traffic_shape_master", array("progress_clicks" => (int)$timeslot['progress_clicks']+1), array("io" => $this->io));
	            return false;
	        }
	    }
	}
	
	public function get_timeslot() {
	    if ($this->io == "")
	        throw new Exception("IO Required");
	    
	    $r = $this->CI->db->query("SELECT campaign_start_datetime FROM campclick_campaigns WHERE io='{$this->io}'");
	    if ($r->num_rows() > 0)    {
	        $io = $r->row_array();

	        $timeCampaignStartDate = strtotime($io['campaign_start_datetime']);
	        $timeNow = time();
	        
	        $howFarInSegments = (int)(($timeNow - $timeCampaignStartDate) / (60 * 30));
	        $howFarInDays = (int)($howFarInSegments / $this->sample_points_per_day) + 1;
	        
	        // VERY HACKY - BEGIN
	        if ($howFarInSegments > 24)    {
	            $howFarInSegments = ($howFarInDays * $this->sample_points_per_day) - $howFarInSegments;
	        }

	        if ($howFarInSegments == 0)
	            $howFarInSegments = 1;
	        // VERY HACKY - END
	        
	        $r = $this->CI->db->query("SELECT io, progress_clicks, d{$howFarInDays}_ts{$howFarInSegments}_cur_clicks AS current_clicks, d{$howFarInDays}_ts{$howFarInSegments}_max_clicks AS max_clicks, d{$howFarInDays}_ts{$howFarInSegments}_sum_clicks AS sum_clicks, total_clicks FROM traffic_shape_master WHERE io='{$this->io}'");
	        if ($r->num_rows() > 0)    {
	            $ts = $r->row_array();
	            
	            $ts['how_far_in_days'] = $howFarInDays;
	            $ts['how_far_in_segments'] = $howFarInSegments;
	            
	            return $ts;
	        } else {
	            return false;
	        }
	    } else {
	        return false;
	    }
	}
	
	public function create()   {
	    if ($this->io == "")
	        throw new Exception("io required");
	    
	    if (! $this->campaign_duration_days > 0)
	        throw new Exception("campaign_duration_days must be > 0");
	    
	    if (! $this->campaign_max_clicks > 0)
	        throw new Exception("campaign_max_clicks must be > 0");
	    
	    $campaign_days = mt_rand(($this->campaign_duration_days + 0.25)*1000, ($this->campaign_duration_days + 0.75)*1000)/1000;
	    $standard_deviation = mt_rand(($this->standard_deviation + 0.25)*1000, ($this->standard_deviation + 0.75)*1000)/1000;
	    
	    if ($this->start_click_offset > 0) {
	        $curve = $this->calculateNormalDistributionOffset($this->start_click_offset, $campaign_days, $this->sample_points_per_day, $this->campaign_max_click, $standard_deviation);
	    } else {
	        $curve = $this->calculateNormalDistribution($campaign_days, $this->sample_points_per_day, $this->campaign_max_clicks, $standard_deviation);
	    }
	    
	    $r = $this->CI->db->query("SELECT campaign_start_datetime FROM campclick_campaigns WHERE io='{$this->io}'");
	    $campaign = $r->row_array();
	    
	    $insert = array(
	        "io" => $this->io,
	        "campaign_duration_days" => $this->campaign_duration_days,
	        "sample_points_per_day" => $this->sample_points_per_day,
	        "current_sample_point" => 1,
	        "current_sample_time" => date("Y-m-d H:i:00", strtotime("{$campaign['campaign_start_datetime']} +60 minutes")),
	        "total_clicks" => $this->campaign_max_clicks,
	        "standard_deviation" => $standard_deviation
	    );
	    
	    $current_sum_of_clicks = 0;
	    $samplePoint = 1;
	    $day = 1;
	    foreach($curve as $c)  {
	        if ($samplePoint == 25)    {
	            $samplePoint = 1;
	            $day++;
	        }
	        
	        $insert["d{$day}_ts{$samplePoint}_max_clicks"] = ceil($c);
	        
	        $current_sum_of_clicks += $insert["d{$day}_ts{$samplePoint}_max_clicks"];
	        
	        $insert["d{$day}_ts{$samplePoint}_sum_clicks"] = $current_sum_of_clicks;	      
	        $samplePoint++;
	    }
	    
	    // make 100% sure we get all of the contracted for clicks!
	    if ($this->campaign_max_clicks > $current_sum_of_clicks)   {
	        $diff = ($this->campaign_max_clicks - $current_sum_of_clicks);
	        $insert["d1_ts1_max_clicks"] = $diff + $insert['ds1_ts1_max_clicks'];
	    }
	    
	    $id = $this->CI->db->insert("traffic_shape_master", $insert);
	    
	    if ($id > 0)   {
	        $this->CI->Ppcnetworks_model->io = $this->io;
	        $this->CI->Ppcnetworks_model->ppc_network_id = "";
	        $this->CI->Ppcnetworks_model->ppc_network = $campaign['ppc_network'];
	        $this->CI->Ppcnetworks_model->status = "P";
	        $this->CI->Ppcnetworks_model->bid_rate = 0.0010; // $1.00 CPM (we will adjust later)
	        $id = $this->CI->Ppcnetworks_model->create();
	        
	        print_r($curve);
	        
	        return $curve;
	    } else {
	        return false;
	    }
	}
	
	public function calculateNormalDistribution($campaignDays, $samplePointsPerDay, $totalClicks, $standardDeviation)  {
	    $xAxis = $campaignDays * $samplePointsPerDay; //total periods
	    $standardDeviation *= $samplePointsPerDay; //convert deviation to right format
	    $mean = ($xAxis / 2) - 1; //mean - half of total length of campaign - 1 period
	
	    $dev = 1 / ($standardDeviation * sqrt(2 * M_PI));
	    $result = array();
	
	    for ($i = 0; $i < $xAxis; $i++) {
	        $current = $i - $mean;
	        $power = ($current * $current) / (2 * $standardDeviation * $standardDeviation);
	        $result[$i] = (double)($totalClicks * $dev * pow(M_E, -$power));
	    }
	
	    return $result;
	}
	
	/**
	 * Calculates the normal distribution with an initial offset (e.g. X-clicks within the first sample period)
	 * @param INT $firstValue
	 * @param INT $campaignDays
	 * @param INT $samplePointsPerDay
	 * @param INT $totalClicks
	 * @param FLOAT $standardDeviation
	 * @return ARRAY <number, multitype:>
	 */
	public function calculateNormalDistributionOffset($firstValue, $campaignDays, $samplePointsPerDay, $totalClicks, $standardDeviation)    {
	    $firstValue = (int)$firstValue;
	    $xAxis = $campaignDays * $samplePointsPerDay; //total periods
	    $standardDeviation *= $samplePointsPerDay; //convert deviation to right format
	    $fullAxis = $xAxis * 2; //to move graph to the right - make the multiplier greater, but it'll work slower
	    $mean = ($fullAxis / 2) - 1; //mean - half of total length of campaign - 1 period
	
	    $dev = 1 / ($standardDeviation * sqrt(2 * M_PI));
	    $result = array();
	
	    for ($i = 0; $i < $fullAxis; $i++) {
	        $current = $i - $mean;
	        $power = ($current * $current) / (2 * $standardDeviation * $standardDeviation);
	        $result[$i] = $dev * pow(M_E, -$power);
	    }
	
	    $multiplier = 0;
	    $key = 0;
	
	    while ($key < $mean) {
	        $sum = 0;
	        $slice = $xAxis + $key;
	
	        for ($i = $key; $i < $slice; $i++) {
	            $sum += $result[$i];
	        }
	
	        $multiplier = $totalClicks / $sum;
	        $value = $multiplier * $result[$key];
	
	        if ((int)$value >= $firstValue) {
	            break;
	        }
	
	        $key++;
	    }
	
	    $result = array_slice($result, $key, $xAxis);
	
	    foreach ($result as $key => $value) {
	        $result[$key] = $value * $multiplier;
	    }
	
	    return $result;
	}
	
	public function detect_traffic_and_adjust_bid()    {
	    $processing = $this->CI->Campclick_model->get_campaign_list(null, "Y", "AND campaign_is_started='Y' AND campaign_is_complete='N'");

	    foreach($processing as $p)  {
	        $bid_rate = 0.0000;
	        
	        // skip the campaigns which are NOT traffic shaped
	        if ($p['is_traffic_shape'] == "N")
	            continue;
	        
	        $this->CI->Ppcnetworks_model->io = $p['io'];
	        $map = $this->CI->Ppcnetworks_model->get_by_io();

	        // get the timeslot w/ counts for campaign
	        $this->io = $p['io'];
	        $ts = $this->get_timeslot();
	        
	        $this->CI->Campclick_model->io = $this->io;
	        $current_click_count = $this->CI->Campclick_model->get_click_count();
	        
	        $this->CI->Campclick_model->id = $this->CI->Campclick_model->get_campaign_id_from_io();
	        $campaign = $this->CI->Campclick_model->get_campaign();
	        
	        if ($ts['current_clicks'] >= $ts['max_clicks'] && $ts['sum_clicks'] <= $current_click_count)    {
	            // pause the campaign on the PPC network(s)
	            $this->CI->Log_model->io = $p['io'];
	            $this->CI->Log_model->action = "CAMPAIGN_PAUSE";
	            $this->CI->Log_model->note = "Traffic Exceeded for Segment; {$ts['current_clicks']} of {$ts['max_clicks']}";
	            $this->CI->Log_model->create();
	            
	            switch(strtoupper($map['ppc_network']))    {
	                case "FINDITQUICK":
	                case "FIQ":
	                    $response = $this->CI->Finditquick_model->pause_ad($map['ppc_network_id']);
	                    break;
	                    
	                case "EXOCLICK":
	                    break;
	                    
	                case "FACEBOOK":
	                    break;
	            }
	        } else {
	            // unpause the campaign on the PPC network(s)
	            $this->CI->Log_model->io = $p['io'];
	            $this->CI->Log_model->action = "CAMPAIGN_RESUME";
	            $this->CI->Log_model->note = "Traffic Under for Segment; {$ts['current_clicks']} of {$ts['max_clicks']}";
	            $this->CI->Log_model->create();
	            
	            // lets look at campaign activity thus far -- is it fast or slow.
	            $speed = ($ts['current_clicks'] / $ts['max_clicks']);
	            if ($speed > 1)    {
	                // running too fast
	                $bid_rate = $map['bid_rate'] - 0.0001;
	                
	                $this->CI->Log_model->io = $p['io'];
	                $this->CI->Log_model->action = "CAMPAIGN_BID_ADJUSTMENT";
	                $this->CI->Log_model->note = "PPC Bid Changed to:" . $bid_rate;
	                $this->CI->Log_model->create();
	                
	                $this->CI->Ppcnetworks_model->bid_rate = $bid_rate;
	                $this->CI->Ppcnetworks_model->io = $p['io'];
    	            $this->CI->Ppcnetworks_model->ad_id = $map['ad_id'];
	                $this->CI->Ppcnetworks_model->set_bid();
	                
	                $response = $this->CI->Finditquick_model->set_bid($map['ppc_network_id'], $bid_rate); // decrease the bid
	            } else {
	                // running too slow
	                $speed = $speed * 100;
	                if ($speed < 50)   {
	                    // increase bid
	                    $bid_rate = $map['bid_rate'] + 0.0001;
    	                $this->CI->Log_model->io = $p['io'];
    	                $this->CI->Log_model->action = "CAMPAIGN_BID_ADJUSTMENT";
    	                $this->CI->Log_model->note = "PPC Bid Changed to:" . $bid_rate;
    	                $this->CI->Log_model->create();
    	                
    	                $this->CI->Ppcnetworks_model->bid_rate = $bid_rate;
    	                $this->CI->Ppcnetworks_model->io = $p['io'];
    	                $this->CI->Ppcnetworks_model->ad_id = $map['ad_id'];
    	                $this->CI->Ppcnetworks_model->set_bid();
        	                
    	                $response = $this->CI->Finditquick_model->set_bid($map['ppc_network_id'], $bid_rate); // decrease the bid
	                }
	            }
	            
	            switch(strtoupper($map['ppc_network']))    {
	                case "FINDITQUICK":
	                case "FIQ":
	                    $response = $this->CI->Finditquick_model->resume_ad($map['ppc_network_id']);
	                    break;

                    case "EXOCLICK":
                        break;
                         
                    case "FACEBOOK":
                        break;
	                         
	            }
	        }
	    }
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
	
}

?>