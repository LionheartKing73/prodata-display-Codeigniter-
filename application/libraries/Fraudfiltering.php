<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."third_party/predis/autoload.php";

class Fraudfiltering	{

	private $client;
	private $ipaddress;
	private $io;
	private $counter;
	private $referral;

	public function __construct()	{
	}

	public function getIP()	{
		$this->client = new Predis\Client();

		return $this->client->get($this->ipaddress . $this->io);
	}

	/**
	*
	* checkFraud - determines whether we have click fraud or not taking place.
	* @returns TRUE = FRAUD, FALSE = NOT_FRAUD
	*
	*/
	public function checkFraud()	{
	    return false;
	    
		$this->client = new Predis\Client();

		$this->counter = 0;

		if ($this->ipaddress == "50.198.249.13")	{
			return false;
		}

		if ($this->client->exists($this->ipaddress . "-" . $this->counter . "-" . $this->io))	{
			$value = $this->client->get($this->ipaddress . "-" . $this->counter . "-" . $this->io);
			$this->client->incrby($this->ipaddress . "-" . $this->counter . "-" . $this->io, 1);

			//$rand_click_acceptance = mt_rand(5,10);
			$rand_click_acceptance = 1;

			//if matches for revisitors.com - fraud traffic
			if (strpos($this->referral, "revisitor") !== false)	{
				return true;
			}

			if ($value > $rand_click_acceptance)	{
				//this is fraud!!
				return true;
			} else {
				return false;
			}
		} else {
			$this->client->set($this->ipaddress . "-" . $this->counter . "-" . $this->io, 1);
			$this->client->expire($this->ipaddress . "-" . $this->counter . "-" . $this->io, 86400);
			return false;
		}
	}

	public function __get($name)	{
		return $this->$name;
	}

	public function __set($name, $value)	{
		$this->$name = $value;
	} 
}

?>