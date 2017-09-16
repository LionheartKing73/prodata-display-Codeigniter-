<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'third_party/PHP-IPAddress/vendor/autoload.php';

use Leth\IPAddress\IP,
	Leth\IPAddress\IPv4,
	Leth\IPAddress\IPv6;

class Ip_address {

	public function set_network_address($address)
    {
    	$network = false;
    	try {
    		$network = IP\NetworkAddress::factory($address);
    	} catch(Exception $e) {
    		die($e->getMessage());
    	}
        return $network;
    }

    public function is_valid_ip($address)
    {
        $is_valid = true;
        try {
            $network = IP\NetworkAddress::factory($address);
        } catch(Exception $e) {
            $is_valid = false;
        }
        return $is_valid;
    }

    public function get_start_ip($address)
    {
    	$network = $this->set_network_address($address);
        return $network->get_network_start();
    }

    public function get_end_ip($address)
    {
    	$network = $this->set_network_address($address);
        return $network->get_network_end();
    }

    public function get_ip_count($address)
    {
    	$network = $this->set_network_address($address);
        return $network->count();
    }

    public function get_all_ips($address)
    {
    	$addresses = [];
    	$network = $this->set_network_address($address);
    	foreach ( $network as $k => $ip ) {
			$ip_str = (string)$ip;
			$addresses[] = ['ip_addr' => $ip_str, 'ip_long' => ip2long($ip_str)];
		}
    	return $addresses;
    }

	public function get_ip_range($address)
	{
		set_time_limit(-1);
		$range = [];

		$start_ip = (string)$this->get_start_ip($address);
		$end_ip = (string)$this->get_end_ip($address);
		$start_ip_long = ip2long($start_ip);
		$end_ip_long = ip2long($end_ip);

		$range = [
            'user_input' => $address,
			'start_ip' => $start_ip,
			'end_ip' => $end_ip,
			'start_ip_long' => $start_ip_long,
			'end_ip_long' => $end_ip_long,
			'ip_count' => $this->get_ip_count($address),
		];

		return $range;
	}
}