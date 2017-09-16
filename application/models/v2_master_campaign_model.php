<?php
class V2_master_campaign_model extends CI_Model	{
	protected $CI;
	protected $collection = 'v2_master_campaigns';
	// campaign
	private $id;
	private $total_records;
	private $percentage_opens;
	private $percentage_clicks;
	private $percentage_bounce;
	private $total_clicks;
	private $total_opens;
	private $total_bounces;
	private $message_result;
	private $io;
	private $name;
	private $vendor;
	private $domain;
	private $campaign_start_datetime;
	private $geotype;
	private $country;
	private $state = array();
	private $radius;
	private $zip;
	private $special_instructions;
	private $fire_open_pixel = "N";
	private $budget = 0.00;
	private $vertical;
	private $campaign_is_converted_to_live;
	private $campaign_is_approved;
	private $userid;
	private $cap_per_hour;
	private $campaign_quickbooks_processed_approved;
	private $quickbooks_invoice_ref_id;
	private $apply_discount;
	private $is_geo_expanded = "N";
	private $last_geo_expanded_update;
	private $campaign_type;
	private $age;
	private $gender;
	private $platform;
	private $carrier;
	private $remarketing_io;
	private $is_remarketing_io;
	private $network_id;
	private $is_remarketing;
	private $network_campaign_id;
	private $network_campaign_status;
	private $network_name;
	private $group_id;
	// Take5 Pricing for CPC
	private $cpc_national = 0.021; // National USA PPC cost
	private $cpc_geo = 0.031; // GEO specific PPC cost
	private $MINIMUM_ORDER_AMOUNT = 150.00; // this is the minimum order amount we will accept from Take5.
	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->model("V2_ads_link_model");
		$this->CI->load->model("V2_ad_model");
		$this->CI->load->model("Zip_model");
		$this->CI->load->model("Email_Seeds_Model");
		$this->CI->load->model("V2_network_model");
		$this->CI->load->model("v2_campaign_category_model");
		$this->CI->load->model("v2_retargeting_ip_model");
		$config['protocol'] = 'sendmail';
		$config['mailpath'] = '/usr/sbin/sendmail';
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = TRUE;
		$config['mailtype'] = 'html';
		$config['priority'] = 1;
		$this->CI->load->library('email');
		$this->CI->email->initialize($config);
	}
	public function update($id, $data) {
		if ( empty($data) ) return false;
		$result = $this->CI->db->where("id", $id)->update($this->collection, $data);
        return $result;
	}
    public function update_budget($id, $budget) {
        $result = $this->CI->db->where("id", $id)->set('budget','budget+'.$budget, FALSE)->update($this->collection);
        return $result;
    }
    public function validate_location($data = array()) {
        $message = [];
		$insert = [];
        if (!empty($data['geotype'])) {
            $insert['geotype'] = $data['geotype'];
            if ($data['geotype'] == 'country') {
                if (!empty($data['country']) && strlen($data['country']) == 2) {
                	$insert['country'] = $data['country'];
					$insert['state'] = '';
					$insert['zip'] = '';
					$insert['radius'] = 0;
                } else {
                    $message[] = "Country is empty";
                }
            } elseif ($data['geotype'] == 'state') {
                $state_count = count($data['state']);
                if ($state_count) {
                    $states = "";
                    // normalize the geo data
                    foreach ($data['state'] as $key => $s) {
                        if ($key != $state_count - 1) {
                            $states .= $s . ",";
                        } else {
                            $states .= $s;
                        }
                    }
                    $insert['state'] = $states;
                    if (!empty($data['country']) && strlen($data['country']) == 2) {
                        $insert['country'] = $data['country'];
                    } else {
                        $message[] = "Country is empty";
                    }
                    //$insert['country'] = $data['country'];
                } else {
                    $message[] = "States is empty";
                }
				$insert['zip'] = '';
				$insert['radius'] = 0;
            } else {
                if ($data['zip'] != "" && !empty($data['radius'])) {
                    $data['zip'] = preg_replace("/,/", " ", $data['zip']); // remove the commas
                    $data['zip'] = preg_replace("/\s+/", " ", $data['zip']); // remove the multiple spaces
					$zip_array = explode(" ", $data['zip']);
					if(count($zip_array)>500) {
						shuffle($zip_array);
						$zip_array = array_slice($zip_array, 0, 499);
					}
                    $zip = implode(",", $zip_array); // implode to something usable
                    $insert['zip'] = $zip;
                    $insert['radius'] = $data['radius'];
					$insert['country'] = '';
					$insert['state'] = '';
                } else {
                    $message[] = "Zip or radius is empty";
                }
            }
        } else {
            $message[] = "Geotype is empty";
        }
        return array('messages' => $message, 'valide_geo' => $insert);
    }
	public function validate($data = array()) {
		$message = [];
		$result = $this->validate_location($data);
		$message = $result['messages'];
		$insert = $result['valide_geo'];
		if(!empty($data['domain'])){
			$insert['domain'] = $data['domain'];
		} else {
			$message[] = 'Domain is empty';
		}
		if(!empty($data['domain_exclusions'])){
			$insert['domain_exclusions'] = $data['domain_exclusions'];
		}
		if($data['campaign_network'] != "FACEBOOK") {
			if(!empty($data['vertical'])){
				$insert['vertical'] = $data['vertical'];
			} else {
				$message[] = 'Vertical is empty';
			}
		}
		if(!empty($data['name'])){
			$insert['name'] = $data['name'];
		} else {
			$message[] = 'Name is empty';
		}
		if(!empty($data['io'])){
			$insert['io'] = $data['io'];
		} elseif($data['campaign_type'] != "REAL_ESTATE_PROFESSIONAL_CAMPAIGN") {
			$message[] = 'IO is empty';
		}
		if(!empty($data['so'])){
			$insert['so'] = $data['so'];
		} else {
			$message[] = 'SO is empty';
		}
		if(!empty($data['thru_guarantee'])){
			$insert['is_thru_guarantee'] = $data['thru_guarantee'];
		}
		if(!empty($data['budget'])){
			$insert['budget'] = $data['budget'];
		} elseif($data['campaign_type']!="EMAIL") {
			$message[] = 'budget is empty';
		}
		if(!empty($data['userid'])){
			$insert['userid'] = $data['userid'];
		} else {
			$message[] = 'userid is empty';
		}
		if(!empty($data['campaign_type'])){
			$insert['campaign_type'] = $data['campaign_type'];
		} else {
			$message[] = 'campaign_type is empty';
		}
		if(!empty($data['network_id'])){
			$insert['network_id'] = $data['network_id'];
		} else {
			$message[] = 'network_id is empty';
		}
		if(!empty($data['network_name'])){
			$insert['network_name'] = $data['network_name'];
		} else {
			$message[] = 'network_name is empty';
		}
		if(!empty($data['campaign_start_datetime'])){
			$insert['campaign_start_datetime'] = $data['campaign_start_datetime'];
		} else {
			$message[] = 'Campaign_start_date is empty';
		}
		if(!empty($data['keywords'])){
            $filtered_keyword = array_filter($data['keywords']);
		    if($filtered_keyword) {
                $insert["keywords"] = implode(',', $filtered_keyword);
                //$insert["keywords"] = $data['keywords'];
            }
		} else {
			$insert["keywords"] = 'RON';
			//$message[] = 'AD keywords is empty';
		}
		if ($data['is_custom_audience']) {
			if (!empty($data['interests'])) {
				$insert["interests"] = $data['interests'];
			} else {
				//$message[] = 'Interests is empty';
			}
			if (!empty($data['behaviors'])) {
				$insert["behaviors"] = $data['behaviors'];
			} else {
				//$message[] = 'behaviors is empty';
			}
			if (!empty($data['majors'])) {
				$insert["majors"] = $data['majors'];
			} else {
				//$message[] = 'behaviors is empty';
			}
			if (!empty($data['jobs'])) {
				$insert["jobs"] = $data['jobs'];
			} else {
				//$message[] = 'behaviors is empty';
			}
			if (!empty($data['works'])) {
				$insert["works"] = $data['works'];
			} else {
				//$message[] = 'behaviors is empty';
			}
			if (!empty($data['schools'])) {
				$insert["educations"] = $data['schools'];
			} else {
				//$message[] = 'behaviors is empty';
			}
			if (!empty($data['demographics'])) {
				$insert["demographics"] = $data['demographics'];
			} else {
				//$message[] = 'behaviors is empty';
			}
			if (!empty($data['affinity']) && $data['campaign_type'] == 'DISPLAY') {
				$insert["interests"] = $data['affinity'];
			} else {
				//$message[] = 'behaviors is empty';
			}
			if (!empty($data['in_markets']) && $data['campaign_type'] == 'DISPLAY') {
				$insert["behaviors"] = $data['in_markets'];
			} else {
				//$message[] = 'behaviors is empty';
			}
			if (!empty($data['yahoo_interests']) && $data['campaign_type'] == 'DISPLAY_YAHOO') {
				$insert["interests"] = $data['yahoo_interests'];
			} else {
				//$message[] = 'behaviors is empty';
			}
		}
		if ($data['is_lookalike_audience']) {
            $insert['is_lookalike_audience'] = 'Y';
			if ($data['audience_type'] == 'existing') {
                $lookalike_count = count($data['lookalike_audiences']);
                if ($lookalike_count) {
                    $lookalike = "";
                    // normalize the geo data
                    foreach ($data['lookalike_audiences'] as $key => $s) {
                        if ($key != $lookalike_count - 1) {
                            $lookalike .= $s . ",";
                        } else {
                            $lookalike .= $s;
                        }
                    }
                    $insert['lookalike_audiences'] = $lookalike;
                }
            }
		}
		if ($data['is_email_audience']) {
            $insert['is_email_audience'] = 'Y';
			if ($data['email_audience_type'] == 'existing') {
                $email_count = count($data['email_audiences']);
                if ($email_count) {
                    $email = "";
                    // normalize the geo data
                    foreach ($data['email_audiences'] as $key => $s) {
                        if ($key != $email_count - 1) {
                            $email .= $s . ",";
                        } else {
                            $email .= $s;
                        }
                    }
                    $insert['email_audiences'] = $email;
                }
            }
		}
		if ($data['is_fb_form']) {
			if($data['form']["form_type"]=='existing') {
				$insert['form_id'] = $data['form']["form_id"];
			}
		}
		if(!empty($data['more_options'])){
			if( $data['more_options']=="Y") {
				if (!empty($data['campaign_end_datetime'])) {
					$insert['campaign_end_datetime'] = $data['campaign_end_datetime'];
				} else {
					//$message[] = 'Campaign_end_date is empty';
				}
				if (!empty($data['max_clicks'])) {
					$insert['max_clicks'] = $data['max_clicks'];
				} elseif (empty($data['max_impressions'])) {
					//$message[] = 'Max_clicks is empty';
				}
				if (!empty($data['max_impressions'])) {
					$insert['max_impressions'] = $data['max_impressions'];
				} elseif (empty($data['max_clicks'])) {
					//$message[] = 'Max_impressions is empty';
				}
				if (!empty($data['max_budget'])) {
					$insert['max_budget'] = $data['max_budget'];
					if (!empty($data['percentage_max_budget'])) {
						$insert['percentage_max_budget'] = $data['percentage_max_budget'];
					} else {
						$message[] = 'Percentage Max budget is empty';
					}
				} else {
					//$message[] = 'Max_budget is empty';
				}
			}
            if (!empty($data['percentage_budget'])) {
                $insert['percentage_budget'] = $data['percentage_budget'];
            } else {
                $message[] = 'Percentage budget is empty';
            }
		} elseif( !empty( $data['campaign_tier']) ) {
			$insert['campaign_tier'] = $data['campaign_tier'];
			if (!empty($data['campaign_end_datetime'])) {
				$insert['campaign_end_datetime'] = $data['campaign_end_datetime'];
			} else {
				//$message[] = 'Campaign_end_date is empty';
			}
			if (!empty($data['max_clicks'])) {
				$insert['max_clicks'] = $data['max_clicks'];
			} elseif (empty($data['max_impressions'])) {
				$message[] = 'Max_clicks is empty';
			}
			if (!empty($data['max_impressions'])) {
				$insert['max_impressions'] = $data['max_impressions'];
			} elseif (empty($data['max_clicks'])) {
				$message[] = 'Max_impressions is empty';
			}
			if (!empty($data['max_budget'])) {
				$insert['max_budget'] = $data['max_budget'];
				if (!empty($data['percentage_max_budget'])) {
					$insert['percentage_max_budget'] = $data['percentage_max_budget'];
				} else {
					$message[] = 'Percentage Max budget is empty';
				}
			} else {
				$message[] = 'Max_budget is empty';
			}
			if (!empty($data['percentage_budget'])) {
				$insert['percentage_budget'] = $data['percentage_budget'];
			} else {
				$message[] = 'Percentage budget is empty';
			}
		} else {
            //$message[] = 'Campaign tier is empty';
        }
		// combine this 3 if statemants
		if(!empty($data['is_remarketing'])){ // default N or esim inch
			if(!empty($data['is_remarketing_io'])){
				$insert['is_remarketing_io'] = $data['is_remarketing_io'];
			} else {
				$message[] = 'Is_remarketing_io is empty';
			}
			$io_count = count($data['remarketing_io']);
			if($io_count){ // implode foreache
				$io_list = "";
				// normalize the geo data
				foreach($data['remarketing_io'] as $key=>$io) {
					if($key != $io_count-1){
						$io_list .= $io . ",";
					} else {
						$io_list .= $io;
					}
				}
				$insert['remarketing_io'] = $io_list;
			} else {
				$message[] = 'Remarketing_io is empty';
			}
			$insert['is_remarketing'] = $data['is_remarketing'];
		} else {
			$insert['is_remarketing'] = 'N';
			//$message[] = 'Is_remarketing is empty';
		}

		// IO Based Retargeting Setting
		if ( !empty($data['is_io_based_retargeting'])
			&& !empty($data['io_based_retargeting_ios']) )
		{
			$insert['is_io_based_retargeting'] = $data['is_io_based_retargeting'];
			$io_list = implode(',', array_map('trim', $data['io_based_retargeting_ios']));
			$insert['retargeting_io'] = $io_list;
		} else {
			$insert['is_io_based_retargeting'] = 'N';
		}

		if(!empty($data['gender'])){
			$insert['gender'] = $data['gender'];
		} else {
			//$message[] = 'Gender is empty';
		}
		if(!empty($data['app_url'])){
			$insert['app_url'] = $data['app_url'];
		} else {
			//$message[] = 'Gender is empty';
		}
		if(!empty($data['income_level'])){
			$insert['income_level'] = $data['income_level'];
		} else {
			//$message[] = 'income_level is empty';
		}
		if(!empty($data['parent'])){
			$insert['parent'] = $data['parent'];
		} else {
			//$message[] = 'Parent is empty';
		}
		if(!empty($data['device_type'])){
			$insert['device_type'] = $data['device_type'];
		} else {
			//$message[] = 'device_type is empty';
		}
		if(!empty($data['carrier'])){
			$insert['carrier'] = $data['carrier'];
		} else {
			//$message[] = 'carrier is empty';
		}
		if(!empty($data['is_audience_network'])){
			$insert['is_audience_network'] = $data['is_audience_network'];
		} else {
			//$message[] = 'carrier is empty';
		}
		if(!empty($data['is_instagram'])){
			$insert['is_instagram'] = $data['is_instagram'];
		} else {
			//$message[] = 'carrier is empty';
		}
		if(!empty($data['preferred_mobile'])){
			$insert['preferred_mobile'] = $data['preferred_mobile'];
		} else {
			//$message[] = 'preferred_mobile is empty';
		}
		if($data['campaign_type'] == "EMAIL") {
			if (!empty($data['total_records'])) {
				$insert['total_records'] = $data['total_records'];
			} else {
				$message[] = 'total_records is empty';
			}
			if (!empty($data['percentage_opens'])) {
				$insert['percentage_opens'] = $data['percentage_opens'];
			} else {
				$message[] = 'percentage_opens is empty';
			}
			if (!empty($data['percentage_clicks'])) {
				$insert['percentage_clicks'] = $data['percentage_clicks'];
			} else {
				$message[] = 'percentage_clicks is empty';
			}
			if (!empty($data['percentage_bounce'])) {
				$insert['percentage_bounce'] = $data['percentage_bounce'];
			} else {
				$message[] = 'percentage_bounce is empty';
			}
			if (!empty($data['total_clicks'])) {
				$insert['total_clicks'] = $data['total_clicks'];
			} else {
				$message[] = 'total_clicks is empty';
			}
			if (!empty($data['total_opens'])) {
				$insert['total_opens'] = $data['total_opens'];
			} else {
				$message[] = 'total_opens is empty';
			}
			if (!empty($data['total_bounces'])) {
				$insert['total_bounces'] = $data['total_bounces'];
			} elseif($data['total_bounces']!=0) {
				$message[] = 'total_bounces is empty';
			}
			if (!empty($data['fire_open_pixel'])) {
				$insert['fire_open_pixel'] = $data['fire_open_pixel'];
			} else {
				$message[] = 'has_open_pixel is empty';
			}
			if(!empty($data['message_result'])){
				$insert['message_result'] = $data['message_result'];
			} else {
				$message[] = 'message_result is empty';
			}
		}
//
//		if(!empty($data[''])){
//			$insert[''] = $data[''];
//		} else {
//			$message[] = ' is empty';
//		}
		//var_dump($insert); exit;
		return array('messages' => $message, 'valide_campaign' => $insert);
	}
	public function create($data = array())    {
		// store verticals
		$campaign_vertical = $data['vertical'];
		$verticals = json_decode($campaign_vertical, true);
		// store retargeting IPs
		$retargeting_ips = [];
		if ( !empty($data['ip_targeting_ips_json']) ) {
			$retargeting_ips = $data['ip_targeting_ips_json'];
			unset($data['ip_targeting_ips_json']);
		}
		// If vertical is JSON string,
		// then set one vertical as v2_master_campaign.vertical value
		if ( is_array($verticals) ) {
			$first_vertical = trim($verticals[0]['catid']);
			$data['vertical'] = $first_vertical;
		}
		$this->CI->db->insert($this->collection, $data);
		$id = $this->CI->db->insert_id();
		if ($id > 0)  {
			$this->id = $id;
			// save verticals associations
			if ( is_array($verticals) ) {
				$this->CI->v2_campaign_category_model->batch_insert($id, $verticals);
			}
			// save retargeting IPs
			if ( !empty($retargeting_ips) && is_array($retargeting_ips) ) {
				$this->CI->v2_retargeting_ip_model->batch_insert($id, $retargeting_ips);
			}
			return $this->id;
		} else {
			return false;
		}
	}
	public function get_campaign_id_by_io($io)  {
		$result = $this->CI->db->select('id')->where('io', trim($io))->get($this->collection);
		if ($result->num_rows() > 0)    {
			$result = $result->row_array();
			return $result['id'];
		} else {
			return 0;
		}
	}
	public function check_io($io)  {
		$result = $this->CI->db->select('id')->where('io',$io)->get($this->collection);
		if ($result->num_rows() > 0)    {
			return true;
		} else {
			return false;
		}
	}
	public function generate_destination_url($campaign_id, $domain, $type) {
		if($type == 'EMAIL') {
			$destination_url = "http://".$domain."/r2/".$campaign_id;
		} else {
			$destination_url = "http://".$domain."/c2/".$campaign_id;
		}
		return $destination_url;
	}
	public function get_list_count($params, $user_id, $campaigns_id = null, $is_admin = null) {
       // $sql = 'SELECT count(*) as count FROM '.$this->collection.' WHERE userid = '.$user_id.' ';
		if($campaigns_id) {
			$sql = 'SELECT count(*) as count FROM '.$this->collection.' WHERE id IN ('.$campaigns_id.') AND userid = '.$user_id.' ';
		}
		else {
			if($is_admin) {
				$sql = 'SELECT count(*) as count FROM '.$this->collection.' WHERE userid IS NOT NULL ';
			} else {
				$sql = 'SELECT count(*) as count FROM '.$this->collection.' WHERE userid = '.$user_id.' ';
			}
		}
        foreach ($params as $name=>$value) {
            if($name == 'name') {
                $sql .= ' AND ('.$name.' = "'.$value.'" OR io = "'.$value.'") ';
            } elseif($name == 'campaign_end_datetime' || $name == 'campaign_start_datetime') {
                $new_date = date("Y-m-d", strtotime($value));
				if($name == 'campaign_end_datetime') {
					$sql .= ' AND date(campaign_start_datetime) <= "'.$new_date.'" ';
				} else {
					$sql .= ' AND date(' . $name . ') >= "' . $new_date . '" ';
				}
            } else if($name == 'campaign_type' && $value == 'NO_EMAIL') {
                $sql .= ' AND '.$name.' != "EMAIL" ';
            } else {
                $sql .= ' AND '.$name.' = "'.$value.'" ';
            }
        }
        $result = $this->CI->db->query($sql);
        return (int) $result->result_array()[0]['count'];
	}
	public function get_list($params, $user_id, $limit=0, $offset, $campaigns_id = null, $is_admin = null) {

		$sql = 'SELECT '.$this->collection.'.*, DATEDIFF(`'.$this->collection.'`.`campaign_end_datetime`, `'.$this->collection.'`.`campaign_start_datetime`) AS `date_diff`, DATEDIFF(NOW(),`'.$this->collection.'`.`campaign_start_datetime`) AS `percent_diff`,
				(SELECT COUNT(v2_ads.id) FROM v2_ads WHERE '.$this->collection.'.id = v2_ads.campaign_id
				AND v2_ads.approval_status = "DISAPPROVED") AS `disapproved_ads_count`,
				(SELECT COUNT(v2_ads.id) FROM v2_ads WHERE '.$this->collection.'.id = v2_ads.campaign_id
				AND v2_ads.approval_status = "UNCHECKED") AS `unchecked_ads_count`,
				DATEDIFF(`campaign_end_datetime`,`campaign_start_datetime`) AS `date_diff`,
				DATEDIFF(NOW(),`campaign_start_datetime`) AS `persent_diff`
				FROM '.$this->collection.' ';
		if(!empty($params['name'])) {
			$sql .=' JOIN users ON '.$this->collection.'.userid = users.id ';
		}
		if($campaigns_id) {
			$sql .=' WHERE userid = '.$user_id.' AND '.$this->collection.'.id IN ('.$campaigns_id.')';
		} else {
			if(!$is_admin) {
				$sql .=' WHERE userid = '.$user_id.' ';
			} else {
				$sql .=' WHERE userid IS NOT NULL ';
			}
		}
		foreach ($params as $name=>$value) {
			if($name == 'name') {
				$sql .= ' AND ('.$name.' like "%'.$value.'%" OR io like "%'.$value.'%" OR email like "%'.$value.'%") ';
			} elseif($name == 'campaign_end_datetime' || $name == 'campaign_start_datetime') {
				$new_date = date("Y-m-d", strtotime($value));
				if($name == 'campaign_end_datetime') {
					$sql .= ' AND date(campaign_start_datetime) <= "'.$new_date.'" ';
				} else {
					$sql .= ' AND date(' . $name . ') >= "' . $new_date . '" ';
				}
			} else if($name == 'campaign_type' && $value == 'NO_EMAIL') {
                $sql .= ' AND '.$name.' != "EMAIL" ';
            } else {
				$sql .= ' AND '.$name.' = "'.$value.'" ';
			}
		}

		$sql .= ' ORDER BY campaign_start_datetime DESC LIMIT '.$limit.','.$offset.'';

		$result = $this->CI->db->query($sql);
		if ($result->num_rows() > 0)    {
			return  $result->result_array();
		} else {
			return array();
		}
	}
    /**
     *
     * @param type $user_id
     * @param type $id
     * @return type array $campaign by id
     * if user_id find by user_id and id else by id
     */
    public function get_by_id($user_id = null, $id) {
        $query = $this->CI->db->where('id', $id);
        if ($user_id){
            $query->where('userid', $user_id);
        }
        return $query->get($this->collection)->row_array();
    }
    public function get_by_id_and_status($user_id = null, $id, $status) {
        $query = $this->CI->db->where('id', $id)->where('campaign_status', $status);
        if ($user_id){
            $query->where('userid', $user_id);
        }
        return $query->get($this->collection)->row_array();
    }
	public function get_by_user_id($user_id) {
		$query = $this->CI->db->where('userid', $user_id);
		return $query->get($this->collection)->result_array();
	}
	public function get_so_numbers($user_id,$start_date = '',$end_date  = '') {
		$query = $this->CI->db->select('DISTINCT('.$this->collection.'.so)')
			->from($this->collection);
		if ($user_id){
			$query->where('userid', $user_id);
		}
		if($start_date && $end_date){
			$query->where('date(`create_date`) >=',$start_date);
			$query->where('date(`create_date`) <=',$end_date);
		}
		if($start_date && !$end_date){
			$query->where('date(`create_date`)', $start_date);
		}
		$query->where('so IS NOT NULL');
			//->group_by('so');
		return $query->get()->result_array();
	}

	public function get_so($so_number,$start_date  = '',$end_date  = '',$limit,$offset) {

	$query = $this->CI->db->select($this->collection.'.* ,v2_campclick_clicks.timestamp as hour')
			->from($this->collection)
			->join('v2_campclick_clicks','v2_campclick_clicks.campaign_id = '.$this->collection.'.id')
			->where('campaign_type !=', 'EMAIL')
			->where('campaign_status =', 'ACTIVE');
		if ($so_number){
			$query->where('so', $so_number);
		}
		if($start_date && $end_date){
			$query->where('v2_campclick_clicks.timestamp >=',$start_date);
			$query->where('v2_campclick_clicks.timestamp <=',$end_date);
		}
		if($start_date && !$end_date){
			$query->where('v2_campclick_clicks.timestamp >', $start_date);
		}
		$query->where('so IS NOT NULL');

		if($limit){
			$query->limit($limit);
		}
		if($offset){
			$query->Offset($offset);
		}
		$query->group_by($this->collection.'.io');
		return $query->get()->result_array();
	}

	public function get_active_by_user_id($user_id) {
		$where = "(`network_name` = 'GOOGLE' OR `network_name` = 'FACEBOOK')";
		$this->CI->db->where('userid', $user_id);
		$this->CI->db->where('campaign_status', 'ACTIVE');
		$query = $this->CI->db->where($where);
		return $query->get($this->collection)->result_array();
	}
	public function get_by_geotype_and_status($user_id = null, $geotype, $status) {
		$query = $this->CI->db->where('geotype', $geotype);
		$query->where('network_campaign_status', $status);
		if ($user_id){
			$query->where('userid', $user_id);
		}
        $query->order_by('create_date', 'DESC');
		return $query->get($this->collection)->result_array();
	}
	public function get_all_with_network_by_id($user_id, $id) {
		$result = $this->CI->db->select($this->collection.'.*, v2_groups.network_group_id')
			->from($this->collection)
			->join('v2_groups','v2_groups.campaign_id = '.$this->collection.'.id', 'left');
		if ($user_id){
			$result->where(''.$this->collection.'.userid',$user_id);
		}
		$result->where(''.$this->collection.'.id',$id)
			->limit(1);
		return $result->get()->row_array();
	}
    public function get_all_with_cost_by_id($user_id, $id) {
        $sql = "SELECT *,
            (SELECT SUM(max_cost)
            FROM (SELECT MAX(cost) AS max_cost
                FROM `v2_campaign_costs`
                    WHERE `v2_campaign_costs`.campaign_id = $id
                    GROUP BY
                    YEAR(date_updated),
                    MONTH(date_updated),
                    DAY(date_updated))
                cost_table) AS cost
            FROM v2_master_campaigns WHERE v2_master_campaigns.id = $id AND v2_master_campaigns.userid = $user_id";
        $result = $this->CI->db->query($sql)->row_array();
//        $result = $this->CI->db->query($this->collection.'.*, v2_campaign_costs.cosat')
//                ->from($this->collection)
//                ->join('v2_campaign_costs','v2_campaign_costs.campaign_id = '.$this->collection.'.id', 'left')
//                ->where(''.$this->collection.'.userid',$user_id)
//                ->where(''.$this->collection.'.id',)
//                ->order_by('v2_campaign_costs.date_updated', 'DESC')
//                ->get();
            return $result;
    }
	public function get_all_with_clicks_by_id($user_id, $id) {
		$query = $this->CI->db->select($this->collection.'.*, count(v2_campclick_clicks.id) as total_clicks_count ')
		               	->from($this->collection)
					   	->join('v2_campclick_clicks','v2_campclick_clicks.campaign_id = '.$this->collection.'.id', 'left');
		if($user_id) {
			$query->where($this->collection.'.userid',$user_id);
		}
		$query->where(''.$this->collection.'.id',$id)
				->limit(1);
		$result = $query->get();
		return $result->row_array();
	}
	public function get_all_with_clicks_and_impressions_by_id($user_id, $id) {
		$query = $this->CI->db->select($this->collection.'.*, DATEDIFF(`'.$this->collection.'`.`campaign_end_datetime`, `'.$this->collection.'`.`campaign_start_datetime`) AS `date_diff`, DATEDIFF(NOW(),`'.$this->collection.'`.`campaign_start_datetime`) AS `percent_diff`, count(v2_campclick_clicks.id) as total_clicks_count,
		(SELECT SUM(v2_campclick_impressions.impressions_count) FROM v2_campclick_impressions
		 	  WHERE v2_campclick_impressions.campaign_id=`'.$this->collection.'`.id ) AS total_impressions_count', false)
			->from($this->collection)
			->join('v2_campclick_clicks','v2_campclick_clicks.campaign_id = '.$this->collection.'.id', 'left');
		if($user_id) {
			$query->where($this->collection.'.userid',$user_id);
		}
		$query->where($this->collection.'.id',$id)->limit(1);
		$result = $query->get();
		return $result->row_array();
	}

	public function get_all_with_clicks_and_impressions_by_id_with_range_date($user_id, $id,$date_start,$date_end) {
		$date_start = date("Y-m-d 00:00:00",strtotime($date_start));
		$date_end = date("Y-m-d 23:59:59",strtotime($date_end));
		$query = $this->CI->db->select($this->collection.'.*, DATEDIFF(`'.$this->collection.'`.`campaign_end_datetime`, `'.$this->collection.'`.`campaign_start_datetime`) AS `date_diff`, DATEDIFF(NOW(),`'.$this->collection.'`.`campaign_start_datetime`) AS `percent_diff`, count(v2_campclick_clicks.id) as total_clicks_count,
		(SELECT SUM(v2_campclick_impressions.impressions_count) FROM v2_campclick_impressions
		 	  WHERE v2_campclick_impressions.campaign_id=`'.$this->collection.'`.id AND v2_campclick_impressions.timestamp >= "'.$date_start.'" AND v2_campclick_impressions.timestamp <= "'.$date_end.'") AS total_impressions_count', false)
			 	  ->from($this->collection)
			 	  ->join('v2_campclick_clicks','v2_campclick_clicks.campaign_id = '.$this->collection.'.id', 'left');

			 	  if($user_id) {
			 	  	$query->where($this->collection.'.userid',$user_id);
			 	  }

			 	  $query->where($this->collection.'.id',$id);
			 	  $query->where('v2_campclick_clicks.timestamp >=',$date_start);
			 	  $query->where('v2_campclick_clicks.timestamp <=',$date_end)->limit(1);
				//   die;
			 	  $result = $query->get();
			 	  //$query->group_by($this->collection.'.id');
			 	  //echo "<pre>";print_r($this->CI->db);die;
			 	  return $result->row_array();

	}


	public function get_active_campaigns_all_data($user_id=null) {
		$query = $this->CI->db->select($this->collection.'.is_thru_guarantee,'.$this->collection.'.io, '.$this->collection.'.id, DATEDIFF(`'.$this->collection.'`.`campaign_end_datetime`, `'.$this->collection.'`.`campaign_start_datetime`) AS `date_diff`, DATEDIFF("'.date("Y-m-d H:i:s").'",`'.$this->collection.'`.`campaign_start_datetime`) AS `percent_diff`,'.$this->collection.'.name, '.$this->collection.'.campaign_type, '.$this->collection.'.max_budget, '.$this->collection.'.max_impressions, '.$this->collection.'.max_clicks, `'.$this->collection.'`.`campaign_end_datetime`, `'.$this->collection.'`.`campaign_start_datetime`, count(v2_campclick_clicks.id) as total_clicks_count,
		(SELECT SUM(v2_campclick_impressions.impressions_count) FROM v2_campclick_impressions
		 	  WHERE v2_campclick_impressions.campaign_id=`'.$this->collection.'`.id ) AS total_impressions_count,
			(SELECT MAX(v2_campaign_costs.cost) FROM `v2_campaign_costs` WHERE '.$this->collection.'.id = v2_campaign_costs.campaign_id) as cost', false)
			->from($this->collection)
			->join('v2_campclick_clicks','v2_campclick_clicks.campaign_id = '.$this->collection.'.id', 'left')
			->where($this->collection.'.network_campaign_status','ACTIVE')
			->where($this->collection.'.campaign_type !=','EMAIL');
		if($user_id) {
			$query->where($this->collection.'.userid',$user_id);
		}
		$query->group_by($this->collection.'.id');
		$query->order_by($this->collection.'.campaign_start_datetime ASC');
		$result = $query->get();
		//var_dump('<pre>',$result->result_array()); exit;
		return $result->result_array();
	}
	public function check_campaign_type_by_campaign_id($campaign_id){
		$query = $this->CI->db->where('id', $campaign_id);
		return $query->get($this->collection)->result_array();
	}
	public function get_all_with_likes_and_impressions_by_id($user_id, $id) {
		$query = $this->CI->db->select($this->collection.'.*, SUM(v2_campclick_likes.likes_count) as total_clicks_count,
		(SELECT SUM(v2_campclick_impressions.impressions_count) FROM v2_campclick_impressions
		 	  WHERE v2_campclick_impressions.campaign_id=`'.$this->collection.'`.id ) AS total_impressions_count', false)
			->from($this->collection)
			->join('v2_campclick_likes','v2_campclick_likes.campaign_id = '.$this->collection.'.id', 'left');
		if($user_id) {
			$query->where($this->collection.'.userid',$user_id);
		}
		$query->where($this->collection.'.id',$id)
			->limit(1);
		$result = $query->get();
		return $result->row_array();
	}


	public function get_all_with_likes_and_impressions_by_id_with_range_date($user_id, $id, $date_start, $date_end) {

		$query = $this->CI->db->select($this->collection.'.*, SUM(v2_campclick_likes.likes_count) as total_clicks_count,
		(SELECT SUM(v2_campclick_impressions.impressions_count) FROM v2_campclick_impressions
		 	  WHERE v2_campclick_impressions.campaign_id=`'.$this->collection.'`.id AND v2_campclick_impressions.timestamp >= "'.$date_start.'" AND v2_campclick_impressions.timestamp <= "'.$date_end.'") AS total_impressions_count', false)
			 	  ->from($this->collection)
			 	  ->join('v2_campclick_likes','v2_campclick_likes.campaign_id = '.$this->collection.'.id', 'left');

			 	  if($user_id) {
			 	  	$query->where($this->collection.'.userid',$user_id);
			 	  }

			 	  $query->where($this->collection.'.id',$id);
			 	  $query->where('v2_campclick_likes.timestamp >=',$date_start);
			 	  $query->where('v2_campclick_likes.timestamp <=',$date_end)->limit(1);
			 	  $result = $query->get();
			 	  return $result->row_array();

	}


	public function fulfillment_summary($userid = null, $stime = "", $etime = "", $status) {
		$query = $this->CI->db->select($this->collection.'.*, DATEDIFF(`'.$this->collection.'`.`campaign_end_datetime`, `'.$this->collection.'`.`campaign_start_datetime`) AS `date_diff`, DATEDIFF(NOW(),`'.$this->collection.'`.`campaign_start_datetime`) AS `percent_diff`',false)
			->from($this->collection);
		if($status!='ALL') {
			//$query->where($this->collection.'.network_campaign_status', $status);
			$query->where($this->collection.'.campaign_status', $status);
		}
        if($userid) {
            $query->where($this->collection.'.userid',$userid);
        }
        if ($stime != "" && $etime != "")	{
            $query->where($this->collection.".create_date BETWEEN '{$stime}' AND '{$etime}'");
        } else {
            $stime = date("Y-m-d 00:00:00", strtotime("-75 days"));
            $etime = date("Y-m-d H:i:s", strtotime("+1 day"));
            $query->where($this->collection.".campaign_start_datetime BETWEEN '{$stime}' AND '{$etime}'");
        }
		$query->where($this->collection.'.campaign_type !=','EMAIL');
		$query->group_by($this->collection.'.id');
		$result = $query->get();
		$campaigns = $result->result_array();
		return $campaigns;
	}

	public function financial_report($userid = null, $stime, $etime, $customer_id = null) {
        //var_dump($userid, $customer_id); exit;
		$query = $this->CI->db->select($this->collection.'.*, users.username as username, MAX(v2_campaign_costs.cost) as cost', false)
			->from($this->collection);
        if($userid) {
            $query->join('users',''.$this->collection.'.userid = users.id AND users.financial_manager_id = '.$userid.'');
        } else {
            $query->join('users',''.$this->collection.'.userid = users.id');
        }
        $query->join('v2_campaign_costs','v2_campaign_costs.campaign_id = '.$this->collection.'.id');
        if($customer_id && $customer_id!='ALL') {
			$query->where($this->collection.'.userid', $customer_id);
		}
        if ($stime && $etime)	{
            $query->where($this->collection.".campaign_start_datetime BETWEEN '{$stime}' AND '{$etime}'");
        }
		$query->where($this->collection.'.campaign_type !=','EMAIL');
		$query->group_by($this->collection.'.id');
		$result = $query->get();
		$campaigns = $result->result_array();
		return $campaigns;
	}

    public function make_campaign_live($id = null)   {
        $date_before = date("Y-m-d H:i:00", strtotime("-7 day")); //var_dump($date_before); exit;
        //$date_before = '2015-08-04';
        // automated queue runner vs. manual activation by io#.
        if (!$id) {
                //print "No IO Passed\n";
                $sql = "SELECT v2_master_campaigns.*, users.email AS email, users.is_billing_type AS billing_type FROM v2_master_campaigns LEFT JOIN users ON v2_master_campaigns.userid = users.id WHERE  campaign_is_approved='Y' AND campaign_is_converted_to_live='N' AND campaign_type != 'EMAIL' AND campaign_status <> 'DISAPPROVED' AND date(campaign_start_datetime) >= '{$date_before}' GROUP BY io,id,email,billing_type ORDER BY campaign_start_datetime";
                //var_dump($sql); exit;
                $camps_to_go_live = $this->CI->db->query($sql);
        } else {
                print "ID: {$id}\n";
                $sql = "SELECT v2_master_campaigns.*, users.email AS email, users.is_billing_type AS billing_type FROM v2_master_campaigns LEFT JOIN users ON v2_master_campaigns.userid = users.id WHERE  campaign_is_approved='Y' AND campaign_is_converted_to_live='N' AND campaign_type != 'EMAIL' AND campaign_status <> 'DISAPPROVED' AND date(campaign_start_datetime) >= '{$date_before}' AND v2_master_campaigns.copied = 'Y' GROUP BY io,id,email,billing_type ORDER BY campaign_start_datetime";
                $camps_to_go_live = $this->CI->db->query($sql);
                //$camps_to_go_live = $this->CI->db->query("SELECT take5_pending_campaigns.io FROM take5_pending_campaigns JOIN campclick_campaigns cc ON cc.io=take5_pending_campaigns.io WHERE take5_pending_campaigns.io='{$io}' AND cc.ppc_network_ad_id IS NULL AND vendor='2' AND campaign_is_approved='Y' AND campaign_is_converted_to_live='Y' AND cc.campaign_start_datetime <= NOW()");
        }
        if ($camps_to_go_live->num_rows() > 0) {
            return $camps_to_go_live->result_array();
        }
        return false;
    }

	public function set_is_geo_expanded()  {
		$this->CI->db->update($this->collection, array("is_geo_expanded" => $this->is_geo_expanded, "last_geo_expanded_update" => date("Y-m-d H:i:s")), array("io" => $this->io));
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

    public function get_active_campaigns(){
		return $result = $this->CI->db->where('network_campaign_status', 'ACTIVE')->get($this->collection)->result_array();
    }

    public function get_active_campaigns_by_time_range(){
    	$res = array();
	    $query1 = $this->CI->db->select('v2_master_campaigns.campaign_type as type, v2_master_campaigns.name, count(*) as CurrentClicks, Sum(impressions_count) as CurrentImpressions')
	        ->from('v2_email_campaign_reporting')
	        ->join('v2_master_campaigns', $this->collection.'.id = v2_email_campaign_reporting.campaign_id');

	        $query1->where('date_created >=', date('Y-m-d H:i:s', mktime() - 86400))->where('date_created <=', 'date()');
	        $query1->where($this->collection.'.campaign_status =', 'ACTIVE');
	        $query1->group_by('v2_master_campaigns.name');
	        $result1 = $query1->get()->result_array();


	    $query1_1 = $this->CI->db->select('count(*) as total_clicks, Sum(impressions_count) as total_impressions')
	        ->from('v2_email_campaign_reporting')
	        ->join('v2_master_campaigns', $this->collection.'.id = v2_email_campaign_reporting.campaign_id');
	        $query1_1->where($this->collection.'.campaign_status =', 'ACTIVE');
	        $query1_1->group_by('v2_master_campaigns.name');
	        $result1_1 = $query1_1->get()->result_array();


        $query2 = $this->CI->db->select('v2_master_campaigns.campaign_type as type, v2_master_campaigns.name, count(v2_campclick_clicks.id) as CurrentClicks')
	        ->from('v2_master_campaigns')
	        ->join('v2_campclick_clicks', $this->collection.'.id = v2_campclick_clicks.campaign_id');

	        $query2->where('timestamp >=', date('Y-m-d H:i:s', mktime() - 86400))->where('timestamp <=', 'date()');
	        $query2->where($this->collection.'.campaign_status =', 'ACTIVE');
	        $query2->group_by('v2_master_campaigns.id');
	        $result2 = $query2->get()->result_array();

	    $query2_1 = $this->CI->db->select('count(v2_campclick_clicks.id) as total_clicks')
	        ->from($this->collection)
	        ->join('v2_campclick_clicks', $this->collection.'.id = v2_campclick_clicks.campaign_id');

	        $query2_1->where($this->collection.'.campaign_status =', 'ACTIVE');
	        $query2_1->where($this->collection.'.campaign_type !=','EMAIL');
	        $query2_1->group_by( $this->collection.'.id');
	        $result2_1 = $query2_1->get()->result_array();

	    $query3 = $this->CI->db->select('count(v2_campclick_impressions.impressions_count) as CurrentImpressions, v2_master_campaigns.max_impressions')
	        ->from('v2_master_campaigns')
	        ->join('v2_campclick_impressions', $this->collection.'.id = v2_campclick_impressions.campaign_id');

	        $query3->where('timestamp >=', date('Y-m-d H:i:s', mktime() - 86400))->where('timestamp <=', 'date()');
	        $query3->where($this->collection.'.campaign_status =', 'ACTIVE');
	        $query3->group_by('v2_master_campaigns.id');
	        $result3 = $query3->get()->result_array();

	    $query3_1 = $this->CI->db->select('SUM(v2_campclick_impressions.impressions_count) as total_impressions')
	        ->from('v2_master_campaigns')
	        ->join('v2_campclick_impressions', $this->collection.'.id = v2_campclick_impressions.campaign_id');

	        $query3_1->where($this->collection.'.campaign_status =', 'ACTIVE');
	        $query3_1->group_by('v2_master_campaigns.id');
	        $result3_1 = $query3_1->get()->result_array();

	    $query4 = $this->CI->db->select('MAX(v2_campaign_costs.cost) as total_cost')
            ->from('v2_master_campaigns')
            ->join('v2_campaign_costs', $this->collection.'.id = v2_campaign_costs.campaign_id');

            $query4->where('date_updated >=', date('Y-m-d H:i:s', mktime() - 86400))->where('date_updated <=', 'date()');
            $query4->where($this->collection.'.campaign_status','ACTIVE');
            $query4->where($this->collection.'.campaign_type !=','EMAIL');
            $query4->group_by('v2_master_campaigns.id');
           	$result4 = $query4->get()->result_array();

	        for ($i = 0; $i < count($result2); $i++) {
				$result2[$i]['CurrentImpressions'] = $result3[$i]['CurrentImpressions'];
				$result2[$i]['total_clicks'] = $result2_1[$i]['total_clicks'];
				$result2[$i]['total_impressions'] = $result3_1[$i]['total_impressions'];
				$result2[$i]['max_impressions'] = $result3[$i]['max_impressions'];
				$result2[$i]['total_cost'] = $result4[$i]['total_cost'];
			}
			for ($i=0; $i < count($result1); $i++) {
				$result1[$i]['total_clicks'] = $result1_1[$i]['total_clicks'];
				$result1[$i]['total_impressions'] = $result1_1[$i]['total_impressions'];
				$result1[$i]['max_impressions'] = '-';
				$result1[$i]['total_cost'] = '-';
			}

	        $res['totalall'] = $result2;
	        $res['emails'] = $result1;

        return $res;

    }

	public function campaign_is_active($id){
		$result = $this->CI->db->select('network_campaign_status')
			->where('network_campaign_status', 'ACTIVE')
			->where('campaign_is_converted_to_live', 'Y')
			->where('id', $id)
			->get($this->collection)
			->row_array();
		if($result){
			return true;
		}
		return false;
    }

    public function get_ended_active_campaigns(){
        $now = date('Y-m-d H:i');
//        $result = $this->CI->db->where('network_campaign_status', 'ACTIVE')
//            ->where($this->collection.'.max_impressions IS NULL')
//            ->where($this->collection.'.max_clicks IS NULL')
//            ->where($this->collection.'.max_budget IS NULL')
//            ->where('campaign_end_datetime <=', $now)
//            ->get($this->collection);
//        return $result->result_array();
		$result = $this->CI->db->select($this->collection.'.*,users.email as email, users.can_extend_campaigns as can_extend_campaigns')
			->from($this->collection)
			->join('users','users.id = '.$this->collection.'.userid', 'left')
			->where('network_campaign_status', 'ACTIVE')
			->where('campaign_type !=', 'EMAIL')
			//->where($this->collection.'.max_impressions IS NULL')
			//->where($this->collection.'.max_clicks IS NULL')
			//->where($this->collection.'.max_budget IS NULL')
			->where('campaign_end_datetime <=', $now)
			->get();
		return $result->result_array();
    }

	public function get_active_campaigns_by_type_and_form_email_type($type, $email_type){
		$result = $this->CI->db->select($this->collection.'.*, v2_fb_forms.email AS email')
			->from($this->collection)
			->join('v2_fb_forms','v2_fb_forms.id = '.$this->collection.'.form_id AND v2_fb_forms.email_type = "'.$email_type.'"')
			->where('network_campaign_status', 'ACTIVE')
			->where('campaign_type', $type)
			->get();
		return $result->result_array();
    }

    public function get_active_campaigns_by_network_id($network_id){
		return $result = $this->CI->db->where('network_campaign_status', 'ACTIVE')->where('network_id', $network_id)->order_by('campaign_start_datetime', 'ASC')->get($this->collection)->result_array();
    }

    public function get_active_campaigns_by_network_id_and_type($network_id, $type){
		return $result = $this->CI->db->where('network_campaign_status', 'ACTIVE')->where('network_id', $network_id)->where('campaign_type', $type)->order_by('campaign_start_datetime', 'ASC')->get($this->collection)->result_array();
    }

    public function get_active_video_campaigns_by_network_id($network_id){
		return $result = $this->CI->db->where('network_campaign_status', 'ACTIVE')->where('network_id', $network_id)->where('campaign_type', 'VIDEO_YAHOO')->order_by('campaign_start_datetime', 'ASC')->get($this->collection)->result_array();
    }

	public function get_active_campaigns_id_by_network_id($network_id){
		return $result = $this->CI->db->select($this->collection.'.id')->where('network_campaign_status', 'ACTIVE')->where('network_id', $network_id)->get($this->collection)->result_array();
    }

    public function get_active_campaigns_network_names(){
		$result = $this->CI->db->select($this->collection.'.network_name')
                        ->from($this->collection)
                        ->where('network_campaign_status', 'ACTIVE')
                        ->where('campaign_type !=', 'EMAIL')
                        ->group_by($this->collection.'.network_name')
                        ->get();
        return $result->result_array();
    }

    public function get_active_video_campaigns_network_names(){
		$result = $this->CI->db->select($this->collection.'.network_name')
                        ->from($this->collection)
                        ->where('network_campaign_status', 'ACTIVE')
                        ->where('campaign_type', 'VIDEO_YAHOO')
                        ->where('campaign_type !=', 'EMAIL')
                        ->group_by($this->collection.'.network_name')
                        ->get();
        return $result->result_array();
    }

	public function get_active_campaigns_network_names_by_ad_status($status){
		$result = $this->CI->db->select($this->collection.'.network_name')
                        ->from($this->collection)
						->join('v2_ads','v2_ads.campaign_id = '.$this->collection.'.id AND v2_ads.approval_status = "'.$status.'" AND v2_ads.creative_is_active = "Y"')
                        ->where($this->collection.'.network_campaign_status', 'ACTIVE')
                        ->where($this->collection.'.campaign_type !=', 'EMAIL')
                        ->group_by($this->collection.'.network_name')
                        ->get();
        return $result->result_array();
    }

	public function get_active_campaigns_impressions_cost($date=null){
        $query = $this->CI->db->select($this->collection.'.max_budget, '.$this->collection.'.id, '.$this->collection.'.network_id, SUM(v2_campclick_impressions.win_price) as total_impressions_cost, COUNT(v2_campclick_impressions.win_price) AS total_impressions_count, budget AS daily_budget, name, campaign_type, io, users.email, campaign_status, network_campaign_status, network_campaign_id, v2_master_campaigns.is_multiple, v2_master_campaigns.userid, (SELECT MAX(v2_campaign_costs.cost) FROM `v2_campaign_costs` WHERE '.$this->collection.'.id = v2_campaign_costs.campaign_id AND `type` = "network") as total_cost')
            ->from($this->collection)
            ->join('v2_campclick_impressions','v2_campclick_impressions.campaign_id = '.$this->collection.'.id', 'left')
            ->join('users', 'users.id=v2_master_campaigns.userid')
            //->where($this->collection.'.network_campaign_status','ACTIVE');
            ->where($this->collection.'.campaign_status','ACTIVE');
            if($date){
                $query->where('v2_campclick_impressions.timestamp >=',$date);
            }
            //->where('v2_campclick_impressions.win_price >',0)
            $query->where($this->collection.'.campaign_type !=','EMAIL')
            ->group_by($this->collection.'.id');
            $result = $query->get();
            //echo $this->CI->db->last_query();die;

        // get network cost

        // get rtb cost
        if ($result->num_rows() > 0) {
            $campaigns = array();

            foreach($result->result_array() as $c) {
                $r = $this->CI->db->query("SELECT SUM(win_price) AS total_rtb_cost FROM v2_campclick_impressions WHERE campaign_id='{$c['id']}'");
                if ($r->num_rows() > 0) {
                    $rtb_result = $r->row_array();
                } else {
                    $rtb_result = array("total_rtb_cost" => 0.00);
                }

                $r = $this->CI->db->query("SELECT MAX(cost) AS total_network_cost FROM v2_campaign_costs WHERE campaign_id='{$c['id']}' AND type='network'");
                if ($r->num_rows() > 0) {
                    $network_result = $r->row_array();
                } else {
                    $network_result = array("total_network_cost" => 0.00);
                }

                $c['total_cost'] = sprintf("%.6f", $network_result['total_network_cost']) + $rtb_result['total_rtb_cost'];
                $c['total_rtb_cost'] = $rtb_result['total_rtb_cost'];
                $c['total_network_cost'] = sprintf("%.6f", $network_result['total_network_cost']);

                $campaigns[] = $c;
            }
        }

        //return $result->result_array();
        return $campaigns;
	}

	public function get_active_campaigns_impressions_count(){
        $result = $this->CI->db->select($this->collection.'.*, SUM(v2_campclick_impressions.impressions_count) as total_impressions_count, users.email as email, users.is_guarantee_percentage as is_guarantee_percentage,users.is_admin as admin, users.is_billing_type as billing_type, (SELECT COUNT(v2_campclick_clicks.id) FROM v2_campclick_clicks WHERE v2_campclick_clicks.campaign_id = '.$this->collection.'.id) AS `total_clicks_count`')
            ->from($this->collection)
            ->join('v2_campclick_impressions','v2_campclick_impressions.campaign_id = '.$this->collection.'.id', 'left')
			->join('users','users.id = '.$this->collection.'.userid', 'left')
            ->where($this->collection.'.max_impressions IS NOT NULL')
            //->where($this->collection.'.max_budget IS NULL')
			//->where($this->collection.'.max_clicks IS NULL')
            ->where($this->collection.'.network_campaign_status','ACTIVE')
            ->where($this->collection.'.campaign_type !=','EMAIL')
            ->group_by($this->collection.'.id')
            ->get();
        return $result->result_array();
	}

	public function get_active_campaigns_demographics_count($network_id){
        $result = $this->CI->db->select($this->collection.'.*,
        	SUM(v2_demographics_reporting.male) as male_count,
        	SUM(v2_demographics_reporting.female) as female_count,
        	SUM(v2_demographics_reporting.18_24) as 18_24_count,
        	SUM(v2_demographics_reporting.25_34) as 25_34_count,
        	SUM(v2_demographics_reporting.35_44) as 35_44_count,
        	SUM(v2_demographics_reporting.45_54) as 45_54_count,
        	SUM(v2_demographics_reporting.55_64) as 55_64_count,
        	SUM(v2_demographics_reporting.64) as 64_count,
         	SUM(v2_demographics_reporting.unknown_age) as unknown_age_count,
         	SUM(v2_demographics_reporting.unknown_gender) as unknown_gender_count,
         	SUM(v2_demographics_reporting.unknown_device) as unknown_device_count,
         	SUM(v2_demographics_reporting.smartphone) as smartphone_count,
         	SUM(v2_demographics_reporting.desktop) as desktop_count,
         	SUM(v2_demographics_reporting.tablet) as tablet_count
        	 ')
            ->from($this->collection)
            ->join('v2_demographics_reporting','v2_demographics_reporting.campaign_id = '.$this->collection.'.id AND v2_demographics_reporting.type = "CLICK" ', 'left')
			->join('v2_video_watch','v2_video_watch.campaign_id = '.$this->collection.'.id AND v2_video_watch.type = "watch" ', 'left')
			->where($this->collection.'.network_campaign_status','ACTIVE')
            ->where($this->collection.'.network_id',$network_id)
            ->group_by($this->collection.'.id')
            ->get();

            $campaign = $result->result_array();
            foreach ($campaign as &$res){
            	$video_views = $this->CI->db->select('
		        SUM(v2_video_watch.10_sec) as 10_sec_count,
		        SUM(v2_video_watch.15_sec) as 15_sec_count,
		        SUM(v2_video_watch.30_sec) as 30_sec_count,
		        SUM(v2_video_watch.25_p) as 25_p_count,
		        SUM(v2_video_watch.50_p) as 50_p_count,
		        SUM(v2_video_watch.75_p) as 75_p_count,
		        SUM(v2_video_watch.95_p) as 95_p_count
		        ')
            	->from('v2_video_watch')
            	->where('v2_video_watch.campaign_id',$res['id'])
            	->get()->result_array();

            	$res['10_sec_count'] = $video_views[0]['10_sec_count'];
            	$res['15_sec_count'] = $video_views[0]['15_sec_count'];
            	$res['30_sec_count'] = $video_views[0]['30_sec_count'];
            	$res['25_p_count'] = $video_views[0]['25_p_count'];
            	$res['50_p_count'] = $video_views[0]['50_p_count'];
            	$res['75_p_count'] = $video_views[0]['75_p_count'];
            	$res['95_p_count'] = $video_views[0]['95_p_count'];

            }

            return $campaign;
	}

	public function get_active_campaigns_video_count($network_id){
        $result = $this->CI->db->select($this->collection.'.*,
         	SUM(v2_video_watch.10_sec) as 10_sec_count,
         	SUM(v2_video_watch.25_p) as 25_p_count,
         	SUM(v2_video_watch.50_p) as 50_p_count,
         	SUM(v2_video_watch.75_p) as 75_p_count,
         	SUM(v2_video_watch.95_p) as 95_p_count
        	 ')
            ->from($this->collection)
			->join('v2_video_watch','v2_video_watch.campaign_id = '.$this->collection.'.id AND v2_video_watch.type = "watch" ', 'left')
			->where($this->collection.'.network_campaign_status','ACTIVE')
			->where($this->collection.'.campaign_type','VIDEO_YAHOO')
            ->where($this->collection.'.network_id',$network_id)
            ->group_by($this->collection.'.id')
            ->get();
        return $result->result_array();
	}

	public function get_active_campaigns_cost(){
            $result = $this->CI->db->select($this->collection.'.*, MAX(v2_campaign_costs.cost) as total_cost, users.email as email, users.is_billing as is_billing ')
                    ->from($this->collection)
                    ->join('v2_campaign_costs','v2_campaign_costs.campaign_id = '.$this->collection.'.id', 'left')
					->join('users','users.id = '.$this->collection.'.userid', 'left')
                    ->where($this->collection.'.max_budget IS NOT NULL')
                    ->where($this->collection.'.network_campaign_status','ACTIVE')
                    ->where($this->collection.'.campaign_type !=','EMAIL')
                    ->group_by($this->collection.'.id')
                    ->get();
            return $result->result_array();
	}

    public function get_campaign_cost($id, $type, $date=null){
        if ($type == 'FIQ'){
			$sql = "
            SELECT SUM(max_cost) as cost
            FROM (SELECT MAX(cost) AS max_cost
            FROM `v2_campaign_costs`
                WHERE `v2_campaign_costs`.campaign_id = $id AND `v2_campaign_costs`.type <> 'RTB'";
			if($date) {
				$sql .= " AND date(`v2_campaign_costs`.date_updated) = '$date' ";
			}
			$sql .= "
                GROUP BY YEAR(date_updated), MONTH(date_updated), DAY(date_updated)) cost_table
            ";
            $result = $this->CI->db->query($sql);
        }
        else {
			$sql = "
            SELECT MAX(cost) as cost
            FROM `v2_campaign_costs`
                WHERE `v2_campaign_costs`.campaign_id = $id
                AND `v2_campaign_costs`.type <> 'RTB'
            ";
			if($date) {
				$sql .= " AND date(`v2_campaign_costs`.date_updated) = '$date' ";
			}
            $result = $this->CI->db->query($sql);
        }
        return $result->row_array()['cost'];
    }

	public function get_future_campaigns(){
		$now = date("Y-m-d H:i:s");
		$result = $this->CI->db
			->where(['network_campaign_status'=>'PAUSED', 'campaign_start_datetime < ' => $now, 'campaign_status'=>'SCHEDULED', 'campaign_type !='=>'EMAIL'])
			->get($this->collection);
		return $result->result_array();
	}

	public function get_campaigns_with_time_parting(){
		$day_of_week = lcfirst(date("l"));
		$result = $this->CI->db->select($this->collection.'.id, '.$this->collection.'.time_parting_status, '.$this->collection.'.network_name, '.$this->collection.'.campaign_status, '.$this->collection.'.network_campaign_status, '.$this->collection.'.network_campaign_id, v2_time_parting.start_time, v2_time_parting.end_time')
			->from($this->collection)
			->where([$this->collection.'.campaign_status !='=>'SCHEDULED',$this->collection.'.campaign_status !='=>'DISAPPROVED', $this->collection.'.campaign_status !='=>'COMPLETED', $this->collection.'.campaign_type !='=>'EMAIL'])
			->join('v2_time_parting','v2_time_parting.campaign_id = '.$this->collection.'.id AND v2_time_parting.day_of_week = "'.$day_of_week.'" AND (v2_time_parting.start_time != "12:00 AM" OR v2_time_parting.end_time != "11:59 PM")' )
			->get();
		return $result->result_array();
	}

	public function get_email_campaigns($user_id=null){
		$query = $this->CI->db->select($this->collection.'.*')
			->from($this->collection)
			->where('campaign_type', 'EMAIL');
		if($user_id) {
			$query->where('userid', $user_id);
		}
		$result = $query->get();
		return $result->result_array();
	}

	public function get_email_campaigns_by_so($user_id=null,$start_date,$end_date = null,$so = null,$limit,$offset){

		$query = $this->CI->db->select($this->collection.'.* ,v2_email_campaign_reporting.date_created as hour')
			->from($this->collection)
			->join('v2_email_campaign_reporting','v2_email_campaign_reporting.campaign_id = '.$this->collection.'.id')
			->where('campaign_type  =', 'EMAIL');
		if ($so_number){
			$query->where('so', $so_number);
		}

		if($start_date == $end_date){
			$query->where('v2_email_campaign_reporting.date_created >', $start_date);
		}else{
			$query->where('v2_email_campaign_reporting.date_created >=',$start_date);
			$query->where('v2_email_campaign_reporting.date_created <=',$end_date);
		}

		if($so){
			$query->where('so', $so);
		}

		if($limit){
			$query->limit($limit);
		}
		if($offset){
			$query->Offset($offset);
		}
		$query->group_by($this->collection.'.io');
		return $query->get()->result_array();

	}

	public function get_active_email_campaigns($user_id=null){
		$query = $this->CI->db->select($this->collection.'.*')
			->from($this->collection)
			->where('campaign_type', 'EMAIL')
			->where('network_campaign_status', 'ACTIVE');
		if($user_id) {
			$query->where('userid', $user_id);
		}
		$result = $query->get();
		return $result->result_array();
	}

	public function get_email_campaigns_with_clicks_and_impressions($user_id=null){
		$query = $this->CI->db->select($this->collection.'.*')
			->from($this->collection)
			->where('network_campaign_status', 'ACTIVE')
			->where('campaign_type', 'EMAIL');
		if($user_id) {
			$query->where('userid', $user_id);
		}
		$query->order_by('id', 'ASC');
		$result = $query->get();
		return $result->result_array();
	}

	public function get_active_campaigns_likes_count_with_impressions() {
		$query = $this->CI->db->select($this->collection.'.*, SUM(v2_campclick_likes.likes_count) as total_likes_count,
		(SELECT SUM(v2_campclick_impressions.impressions_count) FROM v2_campclick_impressions
		 	  WHERE v2_campclick_impressions.campaign_id=`'.$this->collection.'`.id ) AS total_impressions_count', false)
			->where($this->collection.'.max_clicks IS NOT NULL')
			->where($this->collection.'.campaign_type','FB-PAGE-LIKE')
			->from($this->collection)
			->join('v2_campclick_likes','v2_campclick_likes.campaign_id = '.$this->collection.'.id', 'left');
		$query->where($this->collection.'.network_campaign_status','ACTIVE');
        $query->group_by($this->collection.'.id');
		$result = $query->get();
		return $result->result_array();
	}

	public function get_active_campaigns_leads_count_with_impressions() {
		$query = $this->CI->db->select($this->collection.'.*, COUNT(v2_fb_leads.id) as total_leads_count,
		(SELECT SUM(v2_campclick_impressions.impressions_count) FROM v2_campclick_impressions
		 	  WHERE v2_campclick_impressions.campaign_id=`'.$this->collection.'`.id ) AS total_impressions_count', false)
			->where($this->collection.'.max_clicks IS NOT NULL')
			->where($this->collection.'.campaign_type','FB-LEAD')
			->from($this->collection)
			->join('v2_fb_leads','v2_fb_leads.campaign_id = '.$this->collection.'.id', 'left');
		$query->where($this->collection.'.network_campaign_status','ACTIVE');
        $query->group_by($this->collection.'.id');
		$result = $query->get();
		return $result->result_array();
	}

    public function get_active_campaigns_for_redis_with_cost($campaign_id = null) {
        $result = $this->CI->db->select($this->collection.'.id, '.$this->collection.'.io, '.$this->collection.'.name, '.$this->collection.'.bid, '.$this->collection.'.budget, '.$this->collection.'.max_budget, MAX(v2_campaign_costs.cost) as spend_all_time,'.$this->collection.'.retargeting_frequency,'.$this->collection.'.campaign_type,'.$this->collection.'.retargeting_io')
            ->from($this->collection)
            ->join('v2_campaign_costs','v2_campaign_costs.campaign_id = '.$this->collection.'.id', 'left')
            //->where($this->collection.'.network_campaign_status','ACTIVE')
            ->where($this->collection.'.campaign_status','ACTIVE') // Changing to campaign_status as JASON said so
            ->where($this->collection.'.campaign_type !=','EMAIL');

        if ( !empty($campaign_id) && is_numeric($campaign_id) ) {
        	$result = $result->where($this->collection.'.id', $campaign_id);
        }

        $result = $result->group_by($this->collection.'.id')
            	->get();
        return $result->result_array();
    }

	public function get_all_campaigns() {
		$r = $this->CI->db->query("SELECT * FROM $this->collection");
		if ($r->num_rows() > 0) {
			$campaign = $r->result_array();
		} else {
			$campaign = array();
		}
		return $campaign;
	}

    public function get_max_impressions_count($so){
        $query = $this->CI->db->select('SUM('.$this->collection.'.max_impressions) as max_impressions')
            ->from($this->collection);
		if($so) {
			$query->join('v2_campclick_impressions', 'v2_campclick_impressions.campaign_id = ' . $this->collection . '.id')
				->where('v2_master_campaigns.so', $so);
		}
		$result = $query->get();
        return $result->row_array();
    }
    public function copy_new_campaign($new_camp){
    	$new_camp['id']=null;
    	$this->CI->db->insert($this->collection, $new_camp);
        $this->id = $this->CI->db->insert_id();
        return $this->id;
    }
}
?>