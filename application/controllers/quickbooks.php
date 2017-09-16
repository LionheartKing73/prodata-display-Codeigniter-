<?php

/**
 * Example CodeIgniter QuickBooks Web Connector integration
 *
 * This file servers as a controller which servers up .QWC configuration files,
 * also also acts as the Web Connector SOAP endpoint. Download your .QWC file
 * by visiting:
 * 	http://path/to/ci/quickbooks/config
 *
 * The Web Connector will get pointed to this endpoint:
 * 	http://path/to/ci/quickbooks/qbwc
 *
 * This particular example adds dummy customers to QuickBooks, but you could
 * easily extend it to perform other operations on QuickBooks too. The final
 * piece of this is just throwing things into the queue to be processed - for
 * an example of that, see:
 * 	docs/example_web_connector_queueing.php
 *
 * @author Keith Palmer <keith@consolibyte.com>
 *
 * @package QuickBooks
 * @subpackage Documentation
 */

/**
 * Example CodeIgniter controller for QuickBooks Web Connector integrations
 */
class Quickbooks extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->helper("url");
		$this->load->helper('cookie');
		$this->load->library('user_agent');
		$this->load->database();

		// QuickBooks config
		$this->load->config('quickbooks');
	}

	/**
	 * SOAP endpoint for the Web Connector to connect to
	 */
	public function qbwc()
	{	$this->load->model('V2_log_model');
		$this->V2_log_model->create(22, 'quickbook is qbwc', 'xml start1');
		$user = $this->config->item('quickbooks_user');
		$pass = $this->config->item('quickbooks_pass');

		// Memory limit
		ini_set('memory_limit', $this->config->item('quickbooks_memorylimit'));

		// We need to make sure the correct timezone is set, or some PHP installations will complain
		if (function_exists('date_default_timezone_set'))
		{
			// * MAKE SURE YOU SET THIS TO THE CORRECT TIMEZONE! *
			// List of valid timezones is here: http://us3.php.net/manual/en/timezones.php
			date_default_timezone_set($this->config->item('quickbooks_tz'));
		}

		// Map QuickBooks actions to handler functions
		$map = array(
			QUICKBOOKS_ADD_CUSTOMER => array( array( $this, '_addCustomerRequest' ), array( $this, '_addCustomerResponse' ) ),
            QUICKBOOKS_MOD_CUSTOMER => array( array( $this, '_modCustomerRequest' ), array( $this, '_modCustomerResponse' ) ),
			QUICKBOOKS_ADD_INVOICE => array ( array( $this, '_addInvoice'), array($this, '_addInvoiceResponse')),
			QUICKBOOKS_QUERY_CUSTOMER => array ( array( $this, '_queryCustomer'), array($this, '_queryCustomerResponse')),
		);

		// Catch all errors that QuickBooks throws with this function
		$errmap = array(
			'*' => array( $this, '_catchallErrors' ),
		);

		// Call this method whenever the Web Connector connects
		$hooks = array(
			//QuickBooks_WebConnector_Handlers::HOOK_LOGINSUCCESS => array( array( $this, '_loginSuccess' ) ), 	// Run this function whenever a successful login occurs
		);

		// An array of callback options
		$callback_options = array();

		// Logging level
		$log_level = $this->config->item('quickbooks_loglevel');

		// What SOAP server you're using
		//$soapserver = QUICKBOOKS_SOAPSERVER_PHP;			// The PHP SOAP extension, see: www.php.net/soap
		$soapserver = QUICKBOOKS_SOAPSERVER_BUILTIN;		// A pure-PHP SOAP server (no PHP ext/soap extension required, also makes debugging easier)

		$soap_options = array(		// See http://www.php.net/soap
		);

		$handler_options = array(
			'deny_concurrent_logins' => false,
			'deny_reallyfast_logins' => false,
		);		// See the comments in the QuickBooks/Server/Handlers.php file

		$driver_options = array(		// See the comments in the QuickBooks/Driver/<YOUR DRIVER HERE>.php file ( i.e. 'Mysql.php', etc. )
			'max_log_history' => 32000,	// Limit the number of quickbooks_log entries to 1024
			'max_queue_history' => 1024, 	// Limit the number of *successfully processed* quickbooks_queue entries to 64
		);

		// Build the database connection string
		$dsn = 'mysql://' . $this->db->username . ':' . $this->db->password . '@' . $this->db->hostname . '/' . $this->db->database;

		// Check to make sure our database is set up
		if (!QuickBooks_Utilities::initialized($dsn))
		{
			// Initialize creates the neccessary database schema for queueing up requests and logging
			QuickBooks_Utilities::initialize($dsn);

			// This creates a username and password which is used by the Web Connector to authenticate
			QuickBooks_Utilities::createUser($dsn, $user, $pass);
		}

		// Set up our queue singleton
		QuickBooks_WebConnector_Queue_Singleton::initialize($dsn);

		// Create a new server and tell it to handle the requests
		// __construct($dsn_or_conn, $map, $errmap = array(), $hooks = array(), $log_level = QUICKBOOKS_LOG_NORMAL, $soap = QUICKBOOKS_SOAPSERVER_PHP, $wsdl = QUICKBOOKS_WSDL, $soap_options = array(), $handler_options = array(), $driver_options = array(), $callback_options = array()
		$Server = new QuickBooks_WebConnector_Server($dsn, $map, $errmap, $hooks, $log_level, $soapserver, QUICKBOOKS_WSDL, $soap_options, $handler_options, $driver_options, $callback_options);

		$response = $Server->handle(true, true);
        $this->V2_log_model->create(22, 'qwbc finish', 'xml start2');
	}

	public function _queryCustomer($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)  {
		return '<?xml version="1.0" ?><?qbxml version="7.0" ?><QBXML><QBXMLMsgsRq onError="stopOnError"><CustomerQueryRq><ActiveStatus>ActiveOnly</ActiveStatus></CustomerQueryRq></QBXMLMsgsRq></QBXML>';
	}

	public function _queryCustomerResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents) {
		// Do something here to record that the data was added to QuickBooks successfully
		$this->load->model('V2_log_model');
		$this->V2_log_model->create(1, $xml, 'xml3');
		mail("jkorkin@safedatatech.onmicrosoft.com", "quick books customer list", $xml);

		return true;
	}

	public function _addInvoice($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)  {

		$this->load->model('V2_log_model');

		// get the "IO" from the extra parameter.
		$io = $extra['io'];
		//$io = 'asdasdf';
		$quickbooks_list_id = $extra['quickbooks_list_id']; // 8000000D-1422041442 << Jason Korkin test user
		//$quickbooks_list_id = "8000003E-1453968740"; // only activate for TESTIGN!!

		$json = json_encode($extra);
		$this->V2_log_model->create($io, 'quickbook is starting  with '.$quickbooks_list_id.' extra'.$json, 'xml');
		// FROM INSIDE HERE, WE QUERY THE DB, GET THE INFO WE NEED TO BUILD THE INVOICE!
		// THEN RETURN THE XML.

		$sql = "SELECT * FROM v2_master_campaigns LEFT JOIN users ON v2_master_campaigns.userid = users.id WHERE BINARY io='{$io}' ";
		if(empty($extra["additional_budget"])) {
			$sql .= " AND (campaign_quickbooks_processed='N' OR campaign_quickbooks_processed='P')";
		}

		$r = $this->db->query($sql);

		if ($r->num_rows() > 0) {

            $campaign = $r->row_array();
            $budget_type = "";
            if ($extra['has_end_criteria'] == "Y") {

                if ($campaign['is_billing_type'] == 'FLAT') {

                    if (!empty($campaign['max_clicks'])) {
                        $tier_desc = array(
                            'tier_1' => 'Display Ad Services (CPC) Tier 1',
                            'tier_2' => 'Display Ad Services (CPC) Tier 2',
                            'tier_3' => 'Display Ad Services (CPC) Tier 3',
                        );
                        $tier_name = array(
                            'tier_1' => 'DISPLAY-CPC-TIER1',
                            'tier_2' => 'DISPLAY-CPC-TIER2',
                            'tier_3' => 'DISPLAY-CPC-TIER3',
                        );
                    } else {
                        $tier_desc = array(
                            'tier_1' => 'Display Ad Services Tier 1',
                            'tier_2' => 'Display Ad Services Tier 2',
                            'tier_3' => 'Display Ad Services Tier 3',
                        );
                        $tier_name = array(
                            'tier_1' => 'DISPLAY-TIER1',
                            'tier_2' => 'DISPLAY-TIER2',
                            'tier_3' => 'DISPLAY-TIER3',
                        );
                    }

//					if (!empty($campaign['max_impressions'])) {
//						$tier_desc = array(
//							'tier_1' => 'Display Ad Services Tier 1',
//							'tier_2' => 'Display Ad Services Tier 2',
//							'tier_3' => 'Display Ad Services Tier 3',
//						);
//						$tier_name = array(
//							'tier_1' => 'DISPLAY-TIER1',
//							'tier_2' => 'DISPLAY-TIER2',
//							'tier_3' => 'DISPLAY-TIER3',
//						);
//					} else {
//						$tier_desc = array(
//							'tier_1' => 'Display Ad Services (CPC) Tier 1',
//							'tier_2' => 'Display Ad Services (CPC) Tier 2',
//							'tier_3' => 'Display Ad Services (CPC) Tier 3',
//						);
//						$tier_name = array(
//							'tier_1' => 'DISPLAY-CPC-TIER1',
//							'tier_2' => 'DISPLAY-CPC-TIER2',
//							'tier_3' => 'DISPLAY-CPC-TIER3',
//						);
//					}

                    $ref = $tier_name[$campaign["campaign_tier"]];
                    $desc = $tier_desc[$campaign["campaign_tier"]];
                    $cost = $campaign["max_budget"];
                } else {
                    $ref = 'DISPLAY';
                    $desc = 'Display Ad Services';

                    if ($campaign["max_budget"]) {
                        $cost = $campaign["max_budget"];
                    } elseif ($campaign["max_clicks"]) {

                        $cost = $campaign["max_clicks"] * $campaign['display_click'];
                        $budget_type = 'Budget based on Clicks';

                    } elseif ($campaign["max_impressions"]) {

                        $cost = $campaign["max_impressions"] * $campaign['display_imp'] / 1000;
                        $budget_type = 'Budget based on Impressions';

                    } elseif ($campaign["campaign_end_datetime"]) {

                        $start_date = new DateTime($campaign['campaign_start_datetime']);
                        $end_date = new DateTime($campaign['campaign_end_datetime']);
                        $days_count = $end_date->diff($start_date)->days;
                        $cost = $campaign["budget"] * $days_count;

                    }
                }
				if($cost < $campaign["min_budget"]){
					$cost = $campaign["min_budget"];
				}
            } else {
                $ref = 'DISPLAY';
                $desc = 'Display Ad Services';

                $this->load->model("V2_master_campaign_model");
                $cost = $this->V2_master_campaign_model->get_campaign_cost($campaign['id'], $campaign['network_name'], date('Y-m-d'));

            }

			$this->V2_log_model->create($io, 'NO EERROR 7 '.$requestID.' quickbook is starting  with '.$quickbooks_list_id, 'xml');
			if(!empty($extra["additional_budget"])) {
				$cost = $extra["additional_budget"];
				if(!$budget_type){
					$budget_type = 'BUDGET';
				}
				$budget_type = 'ADDITIONAL '.$budget_type;
			}

			$this->V2_log_model->create($io, 'NO EERROR 2 quickbook is starting  with '.$quickbooks_list_id, 'xml');
			// make safe for XML
			$campaign['name'] = str_ireplace("&", "-", $campaign['name']);
			$campaign['name'] = str_ireplace("'", "", $campaign['name']);

			if ($campaign['userid'] == "5" || $campaign['userid'] == "8")    {
				$discount = '<InvoiceLineAdd>
                    			<ItemRef>
                    				<FullName>DISCOUNT</FullName>
                    			</ItemRef>
                    			<Desc>Discount 25% (Applied for Payments Within 45-Days)</Desc>
                    			<Rate>' . sprintf("%.2f", $cost * -0.25) . '</Rate>
                    		</InvoiceLineAdd>';
			} else {
				$discount = "";
			}
			//DISPLAY-TIER1 is the item ref.
//            "display ad services tier 1" is the description
			$xml = '<?xml version="1.0" encoding="utf-8"?>
            <?qbxml version="10.0"?>
            <QBXML>
                <QBXMLMsgsRq onError="continueOnError">
                    <InvoiceAddRq>
                    	<InvoiceAdd>
                    		<CustomerRef>
                    			<ListID>' . $quickbooks_list_id . '</ListID>
                    		</CustomerRef>
                    		<TxnDate>' . date("Y-m-d", strtotime($campaign['campaign_start_datetime'])) . '</TxnDate>
                    		<IsToBeEmailed>true</IsToBeEmailed>
                    		<InvoiceLineAdd>
                    			<ItemRef>
                    				<FullName>'. $ref .'</FullName>
                    			</ItemRef>
                    			<Desc>'. $desc .'; IO #: ' . $io . "\n" . $campaign['name'] . "\n" . $budget_type .'</Desc>
                    			<Quantity>1</Quantity>
                    			<Rate>' . sprintf("%.2f", $cost) . '</Rate>
                    		</InvoiceLineAdd>' . $discount . '
                    	</InvoiceAdd>
                    </InvoiceAddRq>
                </QBXMLMsgsRq>
            </QBXML>';

			$this->V2_log_model->create($campaign['id'], 'quickbook xml is '.$xml, 'xml');

			mail("jkorkin@safedatatech.onmicrosoft.com", "quick books xml", $xml);

			$this->V2_log_model->create(1, 'quickbook xml is '.$xml, 'xml');

			return trim($xml);

		} else {
			return true;
		}
	}

	public function _addInvoiceResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)  {

		$config['protocol'] = 'sendmail';
		$config['mailpath'] = '/usr/sbin/sendmail';
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = TRUE;
		$config['mailtype'] = 'html';
		$config['priority'] = 1;

		$this->load->library('email');
		$this->email->initialize($config);

        $this->load->model('V2_log_model');
        $this->V2_log_model->create($extra['io'], 'quickbook send response '.$xml, 'xml aaa');

		//update v2_master_campaigns with quickbooks_invoice_ref_id=INVOICE# and campaign_quickbooks_processed='Y'
		$invoice = json_decode(json_encode(simplexml_load_string($xml)));

		if (strtoupper($invoice->QBXMLMsgsRs->InvoiceAddRs->{"@attributes"}->statusMessage) == "STATUS OK")    {
			$this->db->query("UPDATE v2_master_campaigns SET campaign_quickbooks_processed='Y', quickbooks_invoice_ref_id='{$invoice->QBXMLMsgsRs->InvoiceAddRs->InvoiceRet->RefNumber}' WHERE BINARY io='{$extra['io']}' LIMIT 1");

			$this->V2_log_model->create($extra['io'], 'quickbook set quickbook to Y '.$xml, 'xml finish');
		} else {

			$this->V2_log_model->create($extra['io'], 'Error on Invoice Generation from Quickbooks Web Connector. Check out right away. IO #: ', 'xml finish');
			// email jason
			$this->email->from('noreply@report-site.com', 'Report-Site No Reply');
			$this->email->to('jkorkin@safedatatech.onmicrosoft.com');
			$this->email->subject('ProDataFeed - Report-Site: QB Error Invoice [' . $extra['io'] . ']');
			$this->email->message('Error on Invoice Generation from Quickbooks Web Connector. Check out right away. IO #: ' . $extra['io'] . "\n\n-----------------------------------\n\n" . $xml);
			$this->email->send();
		}

		return true;
	}

	public function _addAdditionalInvoice($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)  {

		$this->load->model('V2_log_model');

		// get the "IO" from the extra parameter.
		$io = $extra['io'];
		//$io = 'asdasdf';
		$campaign = $extra['campaign'];
		$quickbooks_list_id = $extra['quickbooks_list_id']; // 8000000D-1422041442 << Jason Korkin test user
		//$quickbooks_list_id = "8000000D-1422041442"; // only activate for TESTIGN!!
		$this->V2_log_model->create($campaign['id'], 'quickbook is starting  with '.$quickbooks_list_id, 'xml add');
		// FROM INSIDE HERE, WE QUERY THE DB, GET THE INFO WE NEED TO BUILD THE INVOICE!
		// THEN RETURN THE XML.

		$r = $this->db->query("SELECT * FROM v2_master_campaigns LEFT JOIN users ON v2_master_campaigns.userid = users.id WHERE BINARY io='{$io}' AND campaign_quickbooks_processed='N' LIMIT 1");
		if ($r->num_rows() > 0) {

			$campaign = $r->row_array();
			$budget_type = "";
			if ($extra['has_end_criteria'] == "Y") {

				if ($campaign['is_billing_type'] == 'FLAT') {

					if (!empty($campaign['max_clicks'])) {
						$tier_desc = array(
							'tier_1' => 'Display Ad Services (CPC) Tier 1',
							'tier_2' => 'Display Ad Services (CPC) Tier 2',
							'tier_3' => 'Display Ad Services (CPC) Tier 3',
						);
						$tier_name = array(
							'tier_1' => 'DISPLAY-CPC-TIER1',
							'tier_2' => 'DISPLAY-CPC-TIER2',
							'tier_3' => 'DISPLAY-CPC-TIER3',
						);
					} else {
						$tier_desc = array(
							'tier_1' => 'Display Ad Services Tier 1',
							'tier_2' => 'Display Ad Services Tier 2',
							'tier_3' => 'Display Ad Services Tier 3',
						);
						$tier_name = array(
							'tier_1' => 'DISPLAY-TIER1',
							'tier_2' => 'DISPLAY-TIER2',
							'tier_3' => 'DISPLAY-TIER3',
						);
					}

//					if (!empty($campaign['max_impressions'])) {
//						$tier_desc = array(
//							'tier_1' => 'Display Ad Services Tier 1',
//							'tier_2' => 'Display Ad Services Tier 2',
//							'tier_3' => 'Display Ad Services Tier 3',
//						);
//						$tier_name = array(
//							'tier_1' => 'DISPLAY-TIER1',
//							'tier_2' => 'DISPLAY-TIER2',
//							'tier_3' => 'DISPLAY-TIER3',
//						);
//					} else {
//						$tier_desc = array(
//							'tier_1' => 'Display Ad Services (CPC) Tier 1',
//							'tier_2' => 'Display Ad Services (CPC) Tier 2',
//							'tier_3' => 'Display Ad Services (CPC) Tier 3',
//						);
//						$tier_name = array(
//							'tier_1' => 'DISPLAY-CPC-TIER1',
//							'tier_2' => 'DISPLAY-CPC-TIER2',
//							'tier_3' => 'DISPLAY-CPC-TIER3',
//						);
//					}

					$ref = $tier_name[$campaign["campaign_tier"]];
					$desc = $tier_desc[$campaign["campaign_tier"]];
					$cost = $campaign["max_budget"];
				} else {
					$ref = 'DISPLAY';
					$desc = 'Display Ad Services';

					if ($campaign["max_budget"]) {
						$cost = $campaign["max_budget"];
					} elseif ($campaign["max_clicks"]) {

						$cost = $campaign["max_clicks"] * $campaign['display_click'];
						$budget_type = 'Budget based on Clicks';

					} elseif ($campaign["max_impressions"]) {

						$cost = $campaign["max_impressions"] * $campaign['display_imp'] / 1000;
						$budget_type = 'Budget based on Impressions';

					} elseif ($campaign["campaign_end_datetime"]) {

						$start_date = new DateTime($campaign['campaign_start_datetime']);
						$end_date = new DateTime($campaign['campaign_end_datetime']);
						$days_count = $end_date->diff($start_date)->days;
						$cost = $campaign["budget"] * $days_count;

					}
				}
				if($cost < $campaign["min_budget"]){
					$cost = $campaign["min_budget"];
				}
			} else {
				$ref = 'DISPLAY';
				$desc = 'Display Ad Services';

				$this->load->model("V2_master_campaign_model");
				$cost = $this->V2_master_campaign_model->get_campaign_cost($campaign['id'], $campaign['network_name'], date('Y-m-d'));

			}

			// make safe for XML
			$campaign['name'] = str_ireplace("&", "-", $campaign['name']);
			$campaign['name'] = str_ireplace("'", "", $campaign['name']);

			if ($campaign['userid'] == "5" || $campaign['userid'] == "8")    {
				$discount = '<InvoiceLineAdd>
                    			<ItemRef>
                    				<FullName>DISCOUNT</FullName>
                    			</ItemRef>
                    			<Desc>Discount 25% (Applied for Payments Within 45-Days)</Desc>
                    			<Rate>' . sprintf("%.2f", $cost * -0.25) . '</Rate>
                    		</InvoiceLineAdd>';
			} else {
				$discount = "";
			}
			//DISPLAY-TIER1 is the item ref.
//            "display ad services tier 1" is the description
			$xml = '<?xml version="1.0" encoding="utf-8"?>
            <?qbxml version="10.0"?>
            <QBXML>
                <QBXMLMsgsRq onError="continueOnError">
                    <InvoiceAddRq>
                    	<InvoiceAdd>
                    		<CustomerRef>
                    			<ListID>' . $quickbooks_list_id . '</ListID>
                    		</CustomerRef>
                    		<TxnDate>' . date("Y-m-d", strtotime($campaign['campaign_start_datetime'])) . '</TxnDate>
                    		<IsToBeEmailed>true</IsToBeEmailed>
                    		<InvoiceLineAdd>
                    			<ItemRef>
                    				<FullName>'. $ref .'</FullName>
                    			</ItemRef>
                    			<Desc>'. $desc .'; IO #: ' . $io . "\n" . $campaign['name'] . "\n" . $budget_type .'</Desc>
                    			<Quantity>1</Quantity>
                    			<Rate>' . sprintf("%.2f", $cost) . '</Rate>
                    		</InvoiceLineAdd>' . $discount . '
                    	</InvoiceAdd>
                    </InvoiceAddRq>
                </QBXMLMsgsRq>
            </QBXML>';

			$this->V2_log_model->create($campaign['id'], 'quickbook xml is '.$xml, 'xml');

			mail("jkorkin@safedatatech.onmicrosoft.com", "quick books xml", $xml);

			$this->V2_log_model->create(1, 'quickbook xml is '.$xml, 'xml');

			return trim($xml);

		} else {
			return true;
		}
	}


	public function _addAdditionalInvoiceResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)  {

		$config['protocol'] = 'sendmail';
		$config['mailpath'] = '/usr/sbin/sendmail';
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = TRUE;
		$config['mailtype'] = 'html';
		$config['priority'] = 1;

		$this->load->library('email');
		$this->email->initialize($config);

		$this->load->model('V2_log_model');
		$this->V2_log_model->create($extra['io'], 'quickbook send response '.$xml, 'xml aaa');

		//update v2_master_campaigns with quickbooks_invoice_ref_id=INVOICE# and campaign_quickbooks_processed='Y'
		$invoice = json_decode(json_encode(simplexml_load_string($xml)));

		if (strtoupper($invoice->QBXMLMsgsRs->InvoiceAddRs->{"@attributes"}->statusMessage) == "STATUS OK")    {
			//$this->db->query("UPDATE v2_master_campaigns SET campaign_quickbooks_processed='Y', quickbooks_invoice_ref_id='{$invoice->QBXMLMsgsRs->InvoiceAddRs->InvoiceRet->RefNumber}' WHERE io='{$extra['io']}' LIMIT 1");

			$this->V2_log_model->create($extra['io'], 'quickbook set quickbook to Y '.$xml, 'xml finish');
		} else {

			$this->V2_log_model->create($extra['io'], 'Error on Invoice Generation from Quickbooks Web Connector. Check out right away. IO #: ', 'xml finish');
			// email jason
			$this->email->from('noreply@report-site.com', 'Report-Site No Reply');
			$this->email->to('jkorkin@safedatatech.onmicrosoft.com');
			$this->email->subject('ProDataFeed - Report-Site: QB Error Invoice [' . $extra['io'] . ']');
			$this->email->message('Error on Invoice Generation from Quickbooks Web Connector. Check out right away. IO #: ' . $extra['io'] . "\n\n-----------------------------------\n\n" . $xml);
			$this->email->send();
		}

		return true;
	}

	/**
	 * Issue a request to QuickBooks to add a customer
	 */
	public function _addCustomerRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
	{

		$this->load->model('V2_log_model');

		$this->V2_log_model->create(22, 'quickbook is create customer' . $extra['user_id'], 'xml start2');

		$this->load->model('V2_users_model');

		$user = $this->V2_users_model->get_by_id($extra['user_id']);

		if($extra['update']=="N") {
		    //$user['company'] = str_ireplace("&", "-", $user['company']);
		    
			$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="10.0"?>
			<QBXML>
				<QBXMLMsgsRq onError="stopOnError">
					<CustomerAddRq requestID="' . $requestID . '">
						<CustomerAdd>
							<Name>' . $user['company'] . '</Name>
							<CompanyName>' . $user['company'] . '</CompanyName>
							<FirstName>' . $user['first_name'] . '</FirstName>
							<LastName>' . $user['last_name'] . '</LastName>
							<BillAddress>
								<Addr1>' . $user['company'] . '</Addr1>
								<Addr2>' . $user['address'] . '</Addr2>
								<City>' . $user['city'] . '</City>
								<State>' . $user['state'] . '</State>
								<PostalCode>' . $user['zip_code'] . '</PostalCode>
								<Country>United States</Country>
							</BillAddress>
							<Phone>' . $user['phone'] . '</Phone>
							<AltPhone></AltPhone>
							<Fax></Fax>
							<Email>' . $user['email'] . '</Email>
							<Contact>' . $user['first_name'] . ' ' . $user['last_name'] . '</Contact>
							<CreditCardInfo>
							  <CreditCardNumber>' . $user['card_number'] . '</CreditCardNumber>
							  <ExpirationMonth>' . $user['card_exp_month'] . '</ExpirationMonth>
							  <ExpirationYear>' . $user['card_exp_year'] . '</ExpirationYear>
							</CreditCardInfo>
						</CustomerAdd>
					</CustomerAddRq>
				</QBXMLMsgsRq>
			</QBXML>';

		} else {
			$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="10.0"?>
			<QBXML>
				<QBXMLMsgsRq>
					<CustomerModRq>
						<CustomerMod>
							<ListID>' . $user['quickbooks_list_id'] . '</ListID>
							<EditSequence>' . $user['edit_sequence'] . '</EditSequence>
							<Name>test name</Name>
						</CustomerMod>
					</CustomerModRq>
				</QBXMLMsgsRq>
			</QBXML>';

		}




//        <QBXML>
//			<QBXMLMsgsRq onError="stopOnError">
//				<CustomerAddRq requestID="' . $requestID . '">
//					<CustomerAdd>
//						<Name>Jason1 Korkin1</Name>
//						<CompanyName>ConsoliBYTE1, LLC</CompanyName>
//						<FirstName>Jason1</FirstName>
//						<LastName>Korkin1</LastName>
//						<BillAddress>
//							<Addr1>ConsoliBYTE1, LLC</Addr1>
//							<Addr2>134 Stonemill Road1</Addr2>
//							<City>Mansfield</City>
//							<State>CT</State>
//							<PostalCode>06268</PostalCode>
//							<Country>United States</Country>
//						</BillAddress>
//						<Phone>860-634-1602</Phone>
//						<AltPhone>860-429-0021</AltPhone>
//						<Fax>860-429-5183</Fax>
//						<Email>Keith@ConsoliBYTE1.com</Email>
//						<Contact>Keith Palmer1</Contact>
//                        <CreditCardInfo>
//                          <CreditCardNumber>4111111111111111</CreditCardNumber>
//                          <ExpirationMonth>12</ExpirationMonth>
//                          <ExpirationYear>2018</ExpirationYear>
//                        </CreditCardInfo>
//					</CustomerAdd>
//				</CustomerAddRq>
//			</QBXMLMsgsRq>
//		</QBXML>

        $this->V2_log_model->create(22, 'quickbook finish create customer'.$xml, 'xml start2');
		return $xml;
	}

	/**
	 * Handle a response from QuickBooks indicating a new customer has been added
	 */
	public function _addCustomerResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		// Do something here to record that the data was added to QuickBooks successfully
		//$extra['user_id'];
		$this->load->model('V2_log_model');
		$this->V2_log_model->create(33, 'starttttt' .$extra['user_id'].'  '.$xml, 'xml finish');
		$invoice = json_decode(json_encode(simplexml_load_string($xml)));

		if (strtoupper($invoice->QBXMLMsgsRs->CustomerAddRs->{"@attributes"}->statusMessage) == "STATUS OK")    {
			$this->db->query("UPDATE users SET quickbooks_list_id='{$invoice->QBXMLMsgsRs->CustomerAddRs->CustomerRet->ListID}', edit_sequence= {$invoice->QBXMLMsgsRs->CustomerAddRs->CustomerRet->EditSequence} WHERE id='{$extra['user_id']}' LIMIT 1");

			$this->V2_log_model->create($extra['user_id'], 'finishhh '.$xml, 'xml finish');
		} else {

			$this->V2_log_model->create($extra['user_id'], 'Error on Invoice Generation from Quickbooks Web Connector. Check out right away. IO #: ', 'xml finish');
			// email jason
			$this->email->from('noreply@report-site.com', 'Report-Site No Reply');
			$this->email->to('jkorkin@safedatatech.onmicrosoft.com');
			$this->email->subject('ProDataFeed - Report-Site: QB Error Invoice [' . $extra['user_id'] . ']');
			$this->email->message('Error on Invoice Generation from Quickbooks Web Connector. Check out right away. IO #: ' . $extra['user_id'] . "\n\n-----------------------------------\n\n" . $xml);
			$this->email->send();
		}
		return true;
	}



    public function _modCustomerRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
    {

        $this->load->model('V2_log_model');

        $this->V2_log_model->create(22, 'quickbook is create customer' . $extra['user_id'], 'xml mod');

        $this->load->model('V2_users_model');

        $user = $this->V2_users_model->get_by_id($extra['user_id']);


            $xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="10.0"?><QBXML><QBXMLMsgsRq><CustomerModRq><CustomerMod><ListID>8000003D-1453737868</ListID><EditSequence>"1453737868"</EditSequence><Name>test</Name></CustomerMod></CustomerModRq></QBXMLMsgsRq></QBXML>';

        $this->V2_log_model->create(22, 'quickbook finish create customer'.$xml, 'xml mod');
        return $xml;
    }

    /**
     * Handle a response from QuickBooks indicating a customer has been modifyed
     */
    public function _modCustomerResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
    {
        // Do something here to record that the data was added to QuickBooks successfully
        //$extra['user_id'];
        $this->load->model('V2_log_model');
        $this->V2_log_model->create(33, 'starttttt' .$extra['user_id'].'  '.$xml, 'xml mod');
        $invoice = json_decode(json_encode(simplexml_load_string($xml)));

        if (strtoupper($invoice->QBXMLMsgsRs->CustomerModRs->{"@attributes"}->statusMessage) == "STATUS OK")    {
            $this->db->query("UPDATE users SET quickbooks_list_id='{$invoice->QBXMLMsgsRs->CustomerAddRs->CustomerRet->ListID}', edit_sequence= {$invoice->QBXMLMsgsRs->CustomerAddRs->CustomerRet->EditSequence} WHERE id='{$extra['user_id']}' LIMIT 1");

            $this->V2_log_model->create($extra['user_id'], 'finishhh '.$xml, 'xml mod');
        } else {

            $this->V2_log_model->create($extra['user_id'], 'Error on Invoice Generation from Quickbooks Web Connector. Check out right away. IO #: ', 'xml mod');
            // email jason
            $this->email->from('noreply@report-site.com', 'Report-Site No Reply');
            $this->email->to('jkorkin@safedatatech.onmicrosoft.com');
            $this->email->subject('ProDataFeed - Report-Site: QB Error Invoice [' . $extra['user_id'] . ']');
            $this->email->message('Error on Invoice Generation from Quickbooks Web Connector. Check out right away. IO #: ' . $extra['user_id'] . "\n\n-----------------------------------\n\n" . $xml);
            $this->email->send();
        }
        return true;
    }

	/**
	 * Catch and handle errors from QuickBooks
	 */
	public function _catchallErrors($requestID, $user, $action, $ID, $extra, &$err, $xml, $errnum, $errmsg)
	{
	    mail("jkorkin@gmail.com", "ProData RTB QBWC Error", print_r($xml, true));
		return false;
	}

	/**
	 * Whenever the Web Connector connects, do something (e.g. queue some stuff up if you want to)
	 */
	public function _loginSuccess($requestID, $user, $hook, &$err, $hook_data, $callback_config)
	{
		return true;
	}

}