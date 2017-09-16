<?php 

class Billing_model extends CI_Model	{

	protected $CI;
	private $_dsn;
	
	private $io;
	private $customerName;
	private $memo;
	
	private $itemName;
	private $price;
	private $quantity = 1;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		
		$this->CI->load->config('quickbooks');

		$this->dsn('mysql://' . $this->CI->db->username . ':' . $this->CI->db->password . '@' . $this->CI->db->hostname . '/' . $this->CI->db->database);
	}
	
	public function createCustomer($user_id, $update="N")   {
	    $queue = new QuickBooks_WebConnector_Queue($this->_dsn);
	    $response = $queue->enqueue(QUICKBOOKS_ADD_CUSTOMER, null, 1000, array("user_id" => $user_id, "update" => $update));
	}
	
	/**
	 * Set the DSN connection string for the queue class
	 */
	public function dsn($dsn) {
	    $this->_dsn = $dsn;
	}

	/**
	 * @param string $is_end_criteria
     */
	public function build_invoice_queue($has_end_criteria = 'Y')  {
	    $config['protocol'] = 'sendmail';
	    $config['mailpath'] = '/usr/sbin/sendmail';
	    $config['charset'] = 'utf-8';
	    $config['wordwrap'] = TRUE;
	    $config['mailtype'] = 'html';
	    $config['priority'] = 1;
	    
	    $this->CI->load->library('email');
	    $this->CI->email->initialize($config);

		$this->CI->load->model('V2_log_model');
		$this->CI->V2_log_model->create(1, 'quickbook invoice creating', 'invoice');

		if($has_end_criteria == 'Y') {
			$sql = "SELECT u.quickbooks_list_id, tpc.io, u.id AS userid FROM v2_master_campaigns as tpc
					JOIN users u ON u.id=tpc.userid
					WHERE tpc.campaign_is_converted_to_live='Y'
					AND tpc.campaign_is_approved='Y'
					AND tpc.campaign_quickbooks_processed='N'
					AND u.is_qb_invoicing='Y'
					AND (tpc.max_budget IS NOT NULL
						OR tpc.max_clicks IS NOT NULL
						OR tpc.max_impressions IS NOT NULL
						OR tpc.campaign_end_datetime IS NOT NULL)
					";
		} else {
            $sql = "SELECT u.quickbooks_list_id, tpc.io, u.id AS userid FROM v2_master_campaigns as tpc
					JOIN users u ON u.id=tpc.userid
					WHERE tpc.campaign_is_converted_to_live='Y'
					AND tpc.campaign_is_approved='Y'
					AND u.is_qb_invoicing='Y'
					AND (tpc.max_budget IS NULL
						OR tpc.max_clicks IS NULL
						OR tpc.max_impressions IS NULL
						OR tpc.campaign_end_datetime IS NULL)
					";
        }

	    $r = $this->CI->db->query($sql);
//	    var_dump($r->result_array());
		if ($r->num_rows() > 0)    {
			foreach($r->result_array() as $i)  {
				if ($i['quickbooks_list_id'] == "")    {
					// email jason so we know there is an issue!!
					$this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
					$this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
					$this->CI->email->subject('ProDataFeed - Report-Site: RS Error Invoice [' . $i['io'] . ']');
					$this->CI->email->message("User/Invoice does not have a quickbooks list id assigned.  User ID: {$i['userid']}");
					$this->CI->email->send();
				} else {
					$queue = new QuickBooks_WebConnector_Queue($this->_dsn);
					$response = $queue->enqueue(QUICKBOOKS_ADD_INVOICE, null, 1000, array("io" => $i['io'], "quickbooks_list_id" => $i['quickbooks_list_id'], "has_end_criteria" =>$has_end_criteria));

					if($response) {
						$test = $this->CI->db->query("UPDATE v2_master_campaigns SET campaign_quickbooks_processed='P' WHERE BINARY io='{$i['io']}'");

					}
				}

				//print_r($i);
			}
	    }
	}

	public function build_additional_invoice_queue($campaign)  {
		$config['protocol'] = 'sendmail';
		$config['mailpath'] = '/usr/sbin/sendmail';
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = TRUE;
		$config['mailtype'] = 'html';
		$config['priority'] = 1;

		$this->CI->load->library('email');
		$this->CI->email->initialize($config);

		$this->CI->load->model('V2_log_model');
		$this->CI->V2_log_model->create(1, 'quickbook invoice creating', 'invoice');


		if ($campaign['quickbooks_list_id'] == "")    {
			// email jason so we know there is an issue!!
			$this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
			$this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
			$this->CI->email->subject('ProDataFeed - Report-Site: RS Error Invoice [' . $campaign['io'] . ']');
			$this->CI->email->message("User/Invoice does not have a quickbooks list id assigned.  User ID: {$campaign['userid']}");
			$this->CI->email->send();
		} else {
			$queue = new QuickBooks_WebConnector_Queue($this->_dsn);
			$response = $queue->enqueue(QUICKBOOKS_ADD_INVOICE, null, 1000, array("io" => $campaign['io'], "quickbooks_list_id" => $campaign['quickbooks_list_id'], "has_end_criteria" =>'Y', "additional_budget" => $campaign['additional_budget']));
		}

	}
	
	public function createInvoice($io = "")    {
	    $queue = new QuickBooks_WebConnector_Queue($this->_dsn);
	    $response = $queue->enqueue(QUICKBOOKS_ADD_INVOICE, null, 1000, $io);
	    
	    print_r($response);
	}
	
	public function queryCustomer()    {
	    $queue = new QuickBooks_WebConnector_Queue($this->_dsn);
	    $response = $queue->enqueue(QUICKBOOKS_QUERY_CUSTOMER, uniqid(), 1000, uniqid());

		mail("jkorkin@safedatatech.onmicrosoft.com", "QB Query Customer", print_r($response, true));
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