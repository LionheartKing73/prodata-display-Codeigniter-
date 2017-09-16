<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."third_party/predis/autoload.php";

class Clickcap	{

	private $client;
	private $io;
	private $link_id;
	private $redis_host = "45.33.7.188";

	public function __construct()	{
	    try {
	        $this->client = new Predis\Client(array(
            	"host" => "127.0.0.1", //$this->redis_host,
				"port" => 6379,
				"database" => 7
			));
			//$this->client->select(7);
	    } catch (Exception $e) {
	        print "Could not load Predis";
	        print $e->getMessage();
	    }
	}

	/**
	 * Get Ads Geo mapping
	 *
	 * @param  integer $cursor
	 * @return array
	 */
	public function get_ads_geo_map($cursor = 0, $match="", $count = 500)
	{
		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379,
				"database" => 9
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}

		return $this->client->zscan("activeAdsGeoMap", $cursor, array('match' => "{$match}*", 'count' => $count));
	}

	/**
	 * Remove Ads Geo Mapping by keys
	 *
	 * @param  array  $keys [description]
	 * @return void
	 */
	public function remove_ads_geo_maps_by_key(array $keys)
	{
		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379,
				"database" => 9
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}

		$this->client->zrem("activeAdsGeoMap", $keys);
	}

	/**
	 * Delete Any item from Redis based on
	 * provided DB and Key
	 *
	 * @param  integer $db
	 * @param  integer $key
	 * @return void
	 */
	public function del_item($db, $key)
	{
		if ( empty($db) || empty($key) ) return;

		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379,
				"database" => $db
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}

		$this->client->del($key);
	}

	public function cleanup_prodata_id_retargeting_data()
	{
		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}

		$this->client->select(12);
		$this->client->flushdb();
	}

	public function cleanup_retargeting_ip_data()
	{
		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}

		$this->client->select(10);
		$this->client->del("campaignRetargetingIPs");
	}

	public function load_prodata_id_retargeting_data($prodata_id, array $campaign_ids)
	{
		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379,
				"database" => 12
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}

		if ( !empty($campaign_ids) && is_array($campaign_ids) ) {
			$this->client->sadd($prodata_id, $campaign_ids);
		}
	}

	public function keep_cron_exec_time_track($cron_name, $data, $db = 9)
	{
		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379,
				"database" => $db
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}

		if ( is_array($data) ) {
			$this->client->hmset($cron_name, $data);
		} else {
			$this->client->set($cron_name, $data);
		}
	}

	public function delete_all_active_ads_geomap()  {
		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379,
				"database" => 9
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}
		//$this->client->select(9);
		return $this->client->flushdb();
	}

	public function get_all_zips_within_ad_radius($zip, $radius)
	{
		if ( empty($zip) || empty($radius) ) return [];

		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379,
				"database" => 8
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}

		$zips = [];
		try {
			$zips = $this->client->georadiusbymember('UsZipToCoordMap', $zip, $radius, 'mi');
			$zips = array_unique($zips);
		} catch(Exception $e) {}

		return $zips;
	}

	public function store_active_country_level_ad($ad_key, $ad_data)
	{
		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379,
				"database" => 9
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}
		//$this->client->select(9);
		return $this->client->sadd($ad_key, $ad_data);
	}

	public function store_active_ad_geomap(array $geo_data)
	{
		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379,
				"database" => 9
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}

		//$this->client->select(9);
		return $this->client->geoadd(
            'activeAdsGeoMap',
            $geo_data
        );
	}

	public function get_geopos_by_geokey($geo_key)
	{
		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379,
				"database" => 3
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}
		//$this->client->select(3);
        $geopos = $this->client->geopos('US', $geo_key);
        return $geopos;
	}

	public function get_zips_by_state($state_iso_code)
	{
		$state_iso_code = strtoupper($state_iso_code);
		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379,
				"database" => 8
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}
		//$this->client->select(8);
		return $this->client->smembers($state_iso_code);
	}

	public function get_state_by_zip($zip)
	{
		if ( empty($zip) ) null;

		$zip = (string)$zip;
		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379,
				"database" => 8
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}
		//$this->client->select(8);
		$state_iso_codes = $this->client->keys('*');
		$match_state = '';
		$state_iso_codes = array_diff($state_iso_codes, ['UsZipToCoordMap']);
		$zips = [];
		foreach ( $state_iso_codes as $state_iso_code) {
			if ( $is_match = $this->client->sismember($state_iso_code, $zip) ) {
				$match_state = $state_iso_code;
				$zips = $this->client->smembers($state_iso_code);
				break;
			}
		}

		return ['state' => $match_state, 'all_zips' => $zips];
	}

	public function get_clicks()	{
		return $this->client->get($this->io . "_hourly_cap_count");
	}

	public function updateClicks()	{
	    $exists = $this->client->exists($this->io . "_hourly_cap_count");

		if ($exists === true)	{
			$value = $this->client->get($this->io . "_hourly_cap_count");
			$this->client->incrby($this->io . "_hourly_cap_count", 1);

			return $value+1;
		} else {
			$this->client->set($this->io . "_hourly_cap_count", 1);
			$this->client->expire($this->io . "_hourly_cap_count", 3600);

			return 1;
		}
	}

	public function linkClicks()   {
	    $exists = $this->client->exists($this->link_id . "_link_count");

	    if ($exists === true)  {
	        $value = $this->client->get($this->link_id . "_link_count");
	        $this->client->incrby($this->link_id . "_link_count", 1);

	        return $value+1;
	    } else {
	        $this->client->set($this->link_id . "_link_count", 1);
	        $this->client->expire($this->link_id . "_link_count", 2592000); // set the cap at 30-days

	        return 1;
	    }
	}

	public function linkImpressions()   {
	    $exists = $this->client->exists($this->link_id . "_impression_count");

	    if ($exists === true)  {
	        $value = $this->client->get($this->link_id . "_impression_count");
	        $this->client->incrby($this->link_id . "_impression_count", 1);

	        return $value+1;
	    } else {
	        $this->client->set($this->link_id . "_impression_count", 1);
	        $this->client->expire($this->link_id . "_impression_count", 2592000); // set the cap at 30-days

	        return 1;
	    }
	}

	public function set_campaign_status($status = "")  {
	    $this->client->set($this->io . "_campaign_status", $status);
	}

	public function set_ads($ads)  {
		foreach($ads as $ad){
			$this->client->hmset($ad['id'],$ad);
		}
	}

	public function set_ad($ad)  {
	    try {
	        $this->client = new Predis\Client(array(
	            "host" => $this->redis_host,
	            "port" => 6379,
	            "database" => 7
	        ));
	        //$this->client->select(7);
	    } catch (Exception $e) {
	        print "Could not load Predis";
	        print $e->getMessage();
	    }

		$this->client->hmset($ad['id'], $ad);
	}

	public function set_campaign($campaign)  {
	    try {
	        $this->client = new Predis\Client(array(
	            "host" => $this->redis_host,
	            "port" => 6379,
	            //"database" => 5
	        ));
	    } catch (Exception $e) {
	        print "Could not load Predis";
	        print $e->getMessage();
	    }

	    $this->client->select(5);
		$this->client->hmset($campaign['id'],$campaign);

		// NOTE:
        //  Here we're saving retargeting IPs data as JSON string with campaign hash
        //  Also we're saving individual IP as `zadd` to Redis DB of campaign
        //  `zadd` will help to search by `zrangebyscore` in future if we need
        //
        //  Redis Key is: campaignRetargetingIPs
        //
        if ( !empty($campaign['retargeting_ips']) ) {
        	$retargeting_ips = json_decode($campaign['retargeting_ips'], true);
        	if ( !empty($retargeting_ips) ) {
        		foreach ( $retargeting_ips as $ip ) {
	        		$data = [$campaign['id'] . ':' . $ip['start_ip_long'] => $ip['start_ip_long']];

	                // For single IP, start and end IP is same
	                // For CIDR, start and end IP is different and need to save End IP also
	                if ( $ip['start_ip_long'] != $ip['end_ip_long'] ) {
	                	$data[$campaign['id'] . ':' . $ip['end_ip_long']] = $ip['end_ip_long'];
	                }

	                $this->client->select(10);
					$this->client->zadd('campaignRetargetingIPs', $data);
	        	}
        	}
        }
	}

	public function campaign_exists($campaign)  {
	    try {
	        $this->client = new Predis\Client(array(
	            "host" => $this->redis_host,
	            "port" => 6379
	        ));
	    } catch (Exception $e) {
	        print "Could not load Predis";
	        print $e->getMessage();
	    }

	    $this->client->select(5);
	    $r = $this->client->hmget((string)$campaign, array("id"));

	    return $r;
	}

	public function set_domain($key,$domain)  {

		try {
	        $this->client = new Predis\Client(array(
	            "host" => $this->redis_host,
	            "port" => 6379,
	            "database" => 4
	        ));
	    } catch (Exception $e) {
	        print "Could not load Predis";
	        print $e->getMessage();
	    }

		$this->client->set($key,$domain);
	}

	public function set_ip($key,$ip)  {
	    try {
	        $this->client = new Predis\Client(array(
	            "host" => $this->redis_host,
	            "port" => 6379,
	            "database" => 2
	        ));
	    } catch (Exception $e) {
	        print "Could not load Predis";
	        print $e->getMessage();
	    }

		$this->client->set($key,$ip);
	}

	public function get_ads($key)  {
		return $this->client->hgetall($key);
	}

	public function get_domains()  {

		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379,
				"database" => 4
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}
		$keys = $this->client->keys('*');
		//var_dump($keys); exit;
		$domains = [];
		foreach ($keys as $key) {
			$domains[] = $this->client->get($key);
		}
		return $domains;
	}

	public function get_ips()  {

		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379,
				"database" => 2
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}
		$keys = $this->client->keys('*');
		//var_dump($keys); exit;
		$ips = [];
		foreach ($keys as $key) {
			$ips[] = $this->client->get($key);
		}
		return $ips;
	}

	public function delete_all_active_ads()  {
		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379,
				"database" => 7
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}
		return $this->client->flushdb();
	}

	public function delete_all_domains()  {
		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379,
				"database" => 4
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}
		return $this->client->flushdb();
	}

	public function delete_all_ips()  {
		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379,
				"database" => 2
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}
		return $this->client->flushdb();
	}

	public function delete_all_active_campaigns()  {
		try {
			$this->client = new Predis\Client(array(
				"host" => $this->redis_host,
				"port" => 6379,
				//"database" => 5
			));
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}

		// Flush all campaigns
		$this->client->select(5);
		$this->client->flushdb();

		// Flash all Campaign Retargeting IPs
		$this->client->select(10);
		$this->client->flushdb();
	}

	public function delete_ads($keys)  {
		return $this->client->del($keys);
	}

	public function get_keys($key)  {
		return $this->client->keys($key);
	}

	public function get_campaign_status()  {
	    return $this->client->get($this->io . "_campaign_status");
	}

	public function get_link_clicks()	{
	    return $this->client->get($this->link_id . "_link_count");
	}

	public function get_link_impressions()	{
	    return $this->client->get($this->link_id . "_impression_count");
	}

	public function __get($name)	{
		return $this->$name;
	}

	public function __set($name, $value)	{
		$this->$name = $value;
	}

	public function add_domain($item) {

		try {
			$this->client = new Predis\Client(array(
				"host" => "127.0.0.1",
				"port" => 6379,
				"database" => 10
			));
			//$this->client->select(7);
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}

		$this->client->set($item, $item);
	}

	public function add_ip($item) {

		try {
			$this->client = new Predis\Client(array(
				"host" => "127.0.0.1",
				"port" => 6379,
				"database" => 11
			));
			//$this->client->select(7);
		} catch (Exception $e) {
			print "Could not load Predis";
			print $e->getMessage();
		}

		$this->client->set($item, $item);
	}

	public function clear_daily_spend()  {
	    try {
	        $this->client = new Predis\Client(array(
	            "host" => "127.0.0.1",
	            "port" => 6379,
	            "database" => 14
	        ));
	    } catch (Exception $e) {
	        print "Could not load Predis";
	        print $e->getMessage();
	    }

	    $this->client->select(14);
	    $this->client->flushdb();
	}


	public function update_campaign_spend($incr_amount = 0.00, $campaign_id = 0)  {
	    if (! $incr_amount> 0)
	        throw new exception("amount required");

        if (! $campaign_id > 0)
            throw new exception("campaign_id required");

	    try {
	        $this->client = new Predis\Client(array(
	            "host" => "127.0.0.1",
	            "port" => 6379,
	            "database" => 14
	        ));
	    } catch (Exception $e) {
	        print "Could not load Predis";
	        print $e->getMessage();
	    }

	    $this->client->select(14);

	    if ($this->client->exists("CAMPAIGN_{$campaign_id}")) {
	        $this->client->incrbyfloat("CAMPAIGN_{$campaign_id}", $incr_amount);
	    } else {
	        $this->client->set("CAMPAIGN_{$campaign_id}", "0.000001");
	    }
	}


	public function reset_daily_spend($key = "", $value = 0.00)  {
        try {
            $this->client = new Predis\Client(array(
                "host" => "127.0.0.1",
                "port" => 6379,
                "database" => 14
            ));
        } catch (Exception $e) {
            print "Could not load Predis";
            print $e->getMessage();
        }

        $this->client->select(14);
        $this->client->set($key, $value);
	}

	public function get_campaign_spend($campaign_id = 0)   {
	    try {
	        $this->client = new Predis\Client(array(
	            "host" => "127.0.0.1",
	            "port" => 6379,
	            "database" => 14
	        ));
	    } catch (Exception $e) {
	        print "Could not load Predis";
	        print $e->getMessage();
	    }

	    if (! $campaign_id > 0)
	        throw new Exception("campaign_id required");

        $this->client->select(14);
        $spend = $this->client->get("CAMPAIGN_{$campaign_id}");

        return $spend;
	}

	public function increase_impression_counter($key, $field, $incr = 1)
	{
 		try {
	        $this->client = new Predis\Client(array(
	            "host" => $this->redis_host,
	            "port" => 6379,
	            "database" => 13
	        ));
	    } catch (Exception $e) {
	        print "Could not load Predis";
	        print $e->getMessage();
	    }

        $this->client->select(13);
        $amount = $this->client->zincrby($key, $incr, $field);

        return $amount;
	}

	public function reset_daily_impression_count()  {
	    try {
	        $this->client = new Predis\Client(array(
	            "host" => $this->redis_host,
	            "port" => 6379,
	            "database" => 13
	        ));
	    } catch (Exception $e) {
	        print "Could not load Predis";
	        print $e->getMessage();
	    }

	    $this->client->select(13);
	    $res = $this->client->zremrangebyrank('creativesImpCounter', 0, -1);
	    return $res;
	}
}

?>
