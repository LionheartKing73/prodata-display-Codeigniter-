<?php

class V2_multiple_campaign_model extends CI_Model	{

	protected $CI;
	protected $collection = 'v2_multiple_campaigns';
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
		//$this->CI->load->model("Take5_Pending_Campaign_Openpixel_Model");
		//$this->CI->load->model("Campclick_model");
		$this->CI->load->model("V2_ad_model");
		$this->CI->load->model("Zip_model");
		$this->CI->load->model("Email_Seeds_Model");
		$this->CI->load->model("V2_network_model");

		$config['protocol'] = 'sendmail';
		$config['mailpath'] = '/usr/sbin/sendmail';
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = TRUE;
		$config['mailtype'] = 'html';
		$config['priority'] = 1;

		$this->CI->load->library('email');
		$this->CI->email->initialize($config);
	}

	public function update_by_campaign_id($campaign_id, $data) {
		$result = $this->CI->db->where("campaign_id", $campaign_id)->update($this->collection, $data);
        return $result;
	}

    public function update_budget_by_campaign_id($campaign_id, $budget) {
        $result = $this->CI->db->where("campaign_id", $campaign_id)->set('budget','budget+'.$budget, FALSE)->update($this->collection);
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
					$insert['radius'] = '';
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
				$insert['radius'] = '';
            } else {
                if ($data['zip'] != "" && !empty($data['radius'])) {
                    $data['zip'] = preg_replace("/,/", " ", $data['zip']); // remove the commas
                    $data['zip'] = preg_replace("/\s+/", " ", $data['zip']); // remove the multiple spaces
                    $zip = implode(",", explode(" ", $data['zip'])); // implode to something usable
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
		$insert['network_id'] = 1;
		$insert['network_name'] = 'GOOGLE';
        $insert['campaign_id'] = $data['id'];
		$insert['bid'] = $data['bid'];
//        if(!empty($data['campaign_end_datetime'])){
//            $insert['campaign_end_datetime'] = $data['campaign_end_datetime'];
//        } else {
//            $message[] = 'Campaign_end_date is empty';
//        }
//
//        if(!empty($data['max_clicks'])){
//            $insert['max_clicks'] = $data['max_clicks'];
//        } elseif(empty($data['max_impressions'])) {
//            $message[] = 'Max_clicks is empty';
//        }
//
//        if(!empty($data['max_impressions'])){
//            $insert['max_impressions'] = $data['max_impressions'];
//        } elseif(empty($data['max_clicks'])) {
//            $message[] = 'Max_impressions is empty';
//        }

        if(!empty($data['max_budget'])){
            $insert['max_budget'] = $data['max_budget']*2/100;
        } else {
            //$message[] = 'Max_budget is empty';
        }

        if(!empty($data['budget'])){
            $insert['budget'] = $data['budget'];
        } else {
            $message[] = 'Budget is empty';
        }

		return array('messages' => $message, 'valide_campaign' => $insert);
	}

	public function create($data = array())    {
        $result = $this->validate($data);
        if($result['message']){
            return false;
        }

		$this->CI->db->insert($this->collection, $result['valide_campaign']);
		$id = $this->CI->db->insert_id();

		if ($id > 0)  {
			$this->id = $id;
			return $this->id;
		} else {
			return false;
		}
	}

	public function get_campaign_id_by_io($io)  {

		$result = $this->CI->db->select('id')->where('io','{$io}')->get($this->collection);
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

	public function get_list_count($params, $user_id) {

        $sql = 'SELECT count(*) as count FROM '.$this->collection.' WHERE userid = '.$user_id.' ';

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
            } else {
                $sql .= ' AND '.$name.' = "'.$value.'" ';
            }
        }
        $result = $this->CI->db->query($sql);
        return (int) $result->result_array()[0]['count'];
	}

	public function get_list($params, $user_id, $limit=0, $offset) {
		$sql = 'SELECT '.$this->collection.'.*, COUNT(v2_ads.campaign_id) as disapproved_ads_count, DATEDIFF(`campaign_end_datetime`,`campaign_start_datetime`) AS `date_diff`, DATEDIFF(NOW(),`campaign_start_datetime`) AS `persent_diff` FROM '.$this->collection.'';

		$sql .=' LEFT JOIN v2_ads ON '.$this->collection.'.id = v2_ads.campaign_id AND v2_ads.approval_status = "DISAPPROVED" ';
		$sql .=' WHERE userid = '.$user_id.' ';

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
			} else {
				$sql .= ' AND '.$name.' = "'.$value.'" ';
			}
		}
		$sql .= 'GROUP BY '.$this->collection.'.id ORDER BY campaign_start_datetime DESC LIMIT '.$limit.','.$offset.'';

		//var_dump($sql); exit;
		//$query = $this->CI->db->select('*, DATEDIFF(`campaign_end_datetime`,`campaign_start_datetime`) AS `date_diff`, DATEDIFF(NOW(),`campaign_start_datetime`) AS `persent_diff`, NOW() AS `now`', FALSE)->where('userid', $user_id);

		// remove empty values from params array
//		if($params) {
//			$params = array_filter($params); //var_dump($params);
//			if ($params) {
//				if(!empty($params['campaign_start_datetime'])){
//					$query->where('date(campaign_start_datetime)', $params['campaign_start_datetime']);
//					unset($params['campaign_start_datetime']);
//				}
//				if(!empty($params['campaign_end_datetime'])){
//					$query->where('date(campaign_end_datetime)', $params['campaign_end_datetime']);
//					unset($params['campaign_end_datetime']);
//				}
//				if($params) {
//
//					if($params['name']){
//						$where = ' (name = '.$params['name'].' OR io = '.$params['name'].' ) ';
//						unset($params['name']);
//						if($params){
//							$query->where($params);
//						}
//						$query->where($where, TRUE);
//					} else {
//						$query->where($params);
//					}
//				}
//			}
//		}

		$result = $this->CI->db->query($sql);
		//$result = $query->order_by('id')->get( $this->collection, $offset, $limit );
		//var_dump($query); exit;
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
        
    public function get_by_campaign_id($user_id = null, $campaign_id) {
        
        $query = $this->CI->db->where('campaign_id', $campaign_id);
              
        if ($user_id){
            $query->where('userid', $user_id);
        }
        
        return $query->get($this->collection)->row_array();
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
			->join('v2_groups','v2_groups.campaign_id = '.$this->collection.'.id', 'left')
			->where(''.$this->collection.'.userid',$user_id)
			->where(''.$this->collection.'.id',$id)
			->limit(1)
			->get();
		return $result->row_array();

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


            return $result->row_array();

    }

	public function get_all_with_clicks_by_id($user_id, $id) {
		$result = $this->CI->db->select($this->collection.'.*, count(v2_campclick_clicks.id) as total_clicks_count ')
			                  	->from($this->collection)
							   	->join('v2_campclick_clicks','v2_campclick_clicks.campaign_id = '.$this->collection.'.id', 'left')
								->where(''.$this->collection.'.userid',$user_id)
								->where(''.$this->collection.'.id',$id)
								->limit(1)
								->get();
		return $result->row_array();

	}

	public function fulfillment_summary($userid = null, $stime = "", $etime = "", $status) {

		$query = $this->CI->db->select($this->collection.'.*,
			DATEDIFF(`'.$this->collection.'`.`campaign_end_datetime`, `'.$this->collection.'`.`campaign_start_datetime`) AS `date_diff`, DATEDIFF(NOW(),`'.$this->collection.'`.`campaign_start_datetime`) AS `persent_diff`,
		 	count(v2_campclick_clicks.id) as total_clicks_count,
		 	 (SELECT SUM(v2_campclick_impressions.impressions_count) FROM v2_campclick_impressions
		 	  WHERE v2_campclick_impressions.campaign_id=`'.$this->collection.'`.id ) AS total_impressions_count', false)
			->from($this->collection)
			->join('v2_campclick_clicks','v2_campclick_clicks.campaign_id = '.$this->collection.'.id', 'left')
			->where($this->collection.'.network_campaign_status', $status);
        if($userid) {
            $query->where($this->collection.'.userid',$userid);
        }
        if ($stime != "" && $etime != "")	{
            $query->where($this->collection.".create_date BETWEEN '{$stime}' AND '{$etime}'");
        } else {
            $stime = date("Y-m-d 00:00:00", strtotime("-14 days"));
            $etime = date("Y-m-d H:m:s");
            $query->where($this->collection.".campaign_start_datetime BETWEEN '{$stime}' AND '{$etime}'");
        }
		$query->group_by($this->collection.'.id');
		$result = $query->get();
		$campaigns = $result->result_array();
        $now = time();
		return $campaigns;
	}

	public function make_campaign_live($id = "")   {
            
            $date_before = date("Y-m-d H:i:00", strtotime("-7 day")); //var_dump($date_before); exit;
            //$date_before = '2015-08-04';
            // automated queue runner vs. manual activation by io#.
            if ($id == "") {
                    //print "No IO Passed\n";
                    $sql = "SELECT * FROM v2_master_campaigns WHERE  campaign_is_approved='Y' AND campaign_is_converted_to_live='N' AND date(campaign_start_datetime) >= '{$date_before}' GROUP BY io ORDER BY campaign_start_datetime";
                    //var_dump($sql); exit;
                    $camps_to_go_live = $this->CI->db->query($sql);

            } else {
                    print "IO: {$id}\n";
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
        $result = $this->CI->db->where('network_campaign_status', 'ACTIVE')
            ->where($this->collection.'.max_impressions IS NULL')
            ->where($this->collection.'.max_clicks IS NULL')
            ->where($this->collection.'.max_budget IS NULL')
            ->where('campaign_end_datetime <=', $now)
            ->get($this->collection);
        return $result->result_array();
    }

    public function get_active_campaigns_by_network_id($network_id){
		return $result = $this->CI->db->where('network_campaign_status', 'ACTIVE')->where('network_id', $network_id)->get($this->collection)->result_array();
    }

    public function get_active_campaigns_network_names(){
		$result = $this->CI->db->select($this->collection.'.network_name')
                        ->from($this->collection)
                        ->where('network_campaign_status', 'ACTIVE')
                        ->group_by($this->collection.'.network_name')
                        ->get();
        return $result->result_array();
    }

	public function get_active_campaigns_impressions_count(){
        $result = $this->CI->db->select($this->collection.'.*, SUM(v2_campclick_impressions.id) as total_impressions_count ')
            ->from($this->collection)
            ->join('v2_campclick_impressions','v2_campclick_impressions.campaign_id = '.$this->collection.'.id', 'left')
            ->where($this->collection.'.max_impressions IS NOT NULL')
            ->where($this->collection.'.max_budget IS NULL')
            ->where($this->collection.'.network_campaign_status','ACTIVE')
            ->group_by($this->collection.'.id')
            ->get();
        return $result->result_array();
	}

	public function get_active_campaigns_cost(){
		$result = $this->CI->db->select($this->collection.'.*, MAX(v2_campaign_costs.cost) as total_cost ')
			->from($this->collection)
			->join('v2_campaign_costs','v2_campaign_costs.campaign_id = '.$this->collection.'.id', 'left')
			->where($this->collection.'.max_budget IS NOT NULL')
			->where($this->collection.'.network_campaign_status','ACTIVE')
			->group_by($this->collection.'.id')
			->get();
		return $result->result_array();
	}
        
    public function get_campaign_cost($id, $type){
        
        if ($type == 'FIQ'){
            $result = $this->CI->db->query("
            SELECT SUM(max_cost) as cost
            FROM (SELECT MAX(cost) AS max_cost 
            FROM `v2_campaign_costs` 
                WHERE `v2_campaign_costs`.campaign_id = $id 
                GROUP BY YEAR(date_updated), MONTH(date_updated), DAY(date_updated)) cost_table
            ");
        }
        else {
            
        }
        
        return $result->row_array()['cost'];

    }

}

?>
