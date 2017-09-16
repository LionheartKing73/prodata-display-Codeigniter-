<?php 

class Monitor_model extends CI_Model	{

	protected $CI;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		
	}
	
	public function delete_old_data($hours_ago = 12)	{
	    $old_time = date("Y-m-d H:00:00", strtotime("-{$hours_ago} hour"));
	    $this->CI->db->query("DELETE FROM volume_mobile WHERE timestamp < '{$old_time}'");
	}
	
	public function get_count()	{
	    $low_date = date("Y-m-d H:00:00", strtotime("-1 hour"));
	    $high_date = date("Y-m-d H:59.59", strtotime("-1 hour"));

	    $sql = "SELECT COUNT(*) AS cnt FROM volume_mobile WHERE timestamp BETWEEN '{$low_date}' AND '{$high_date}'";
	    
		$r = $this->CI->db->query($sql);
		if ($r->num_rows() > 0)	{
		    $data = $r->row_array();
		    return (int)$data['cnt'];
		} else {
			return 0;
		}
	}
	
	public function test_url() {
	    $sql = "SELECT * FROM campclick_campaigns WHERE is_active='Y' AND campaign_is_started='Y' AND campaign_is_complete='N'";
	    $r = $this->CI->db->query($sql);
	    
	    if ($r->num_rows() > 0)    {
	        $message = "";
	        $checked_link_count = 0;
	        foreach($r->result_array() as $rr) {
	            $sql = "SELECT * FROM campclick_links WHERE io='{$rr['io']}'";
	            $rl = $this->CI->db->query($sql);
	            
	            if ($rl->num_rows() > 0)   {
	                foreach($rl->result_array() as $l)    {
	                    // test the link
	                    
	                    if ($l['is_fulfilled'] == "N") {
	                        $ch = curl_init();
	                        curl_setopt($ch, CURLOPT_URL, $l['dest_url']);
	                        curl_setopt($ch, CURLOPT_USERAGENT, "REPORT-SITE URL CHECK");
	                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	                        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	                         
	                        $response = curl_exec($ch);
	                         
	                        $info = curl_getinfo($ch);
	                        $dom = new DOMDocument();
	                        $dom->loadHTML($response);
	                        $xpath = new DOMXpath($dom);
	                        
	                        $meta_redirect = $xpath->query("//meta[@http-equiv='refresh']");
	                        
	                        foreach($meta_redirect as $node)   {
	                            list($timeout, $url) = explode(";", $node->getAttribute('content'));
	                            list($junk, $url) = explode("=", $url, 2);
	                            $url = str_replace("'", "", $url);
	                            
	                            if ($this->is_url_exist($url) !== true)    {
	                                $message .= "{$rr['io']}\t{$l['dest_url']}\t{$url}\n";
	                            }
	                            
	                            $checked_link_count++;
	                        }
	                    }
	                }
	            }
	        }
	        
	        if ($message != "")    {
	            $message = "Total Checked Links: {$checked_link_count}\n\n" . $message;
	            $filename = "URLTesting-" . date("Y-m-d-H");
	            file_put_contents("/tmp/{$filename}.csv", $message);
	             
	            $config['protocol'] = 'sendmail';
	            $config['mailpath'] = '/usr/sbin/sendmail';
	            $config['charset'] = 'utf-8';
	            $config['wordwrap'] = TRUE;
	            $config['mailtype'] = 'html';
	            $config['priority'] = 1;
	             
	            $this->CI->load->library('email');
	            $this->CI->email->initialize($config);
	             
	            $this->CI->email->from('noreply@report-site.com', 'Report-Site No Reply');
	            $this->CI->email->to('jkorkin@safedatatech.onmicrosoft.com');
	            $this->CI->email->cc('fulfillment@take5s.com');
	            $this->CI->email->subject('Report-Site: Link Error Report');
	            $this->CI->email->message('Attached is the morning/evening of link errors from Report-Site.com.  This is a CSV DELIMITED file; open in Excel.');
	            $this->CI->email->attach("/tmp/{$filename}.csv");
	            $this->CI->email->send();
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
	
	private function is_url_exist($url)    {
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_exec($ch);
        
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($code == 200)   {
            return true;
        } else {
            return false;
        }
	}
	
	/*
	public function retrieve_remote_url2($url)  {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_USERAGENT, "REPORT-SITE URL CHECK");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	    
	    $response = curl_exec($ch);
	    
	    $info = curl_getinfo($ch);
	    $dom = new DOMDocument();
	    $dom->loadHTML($response);
	    $xpath = new DOMXpath($dom);
	     
	    $meta_redirect = $xpath->query("//meta[@http-equiv='refresh']");

	    $returnUrl = array();
	    foreach($meta_redirect as $node)   {
	        list($timeout, $url) = explode(";", $node->getAttribute('content'));
	        list($junk, $url) = explode("=", $url, 2);
	        $url = str_replace("'", "", $url);

	        $returnUrl[] = $url;
	    }
	    
	    return $returnUrl;
	}
	*/
	
	public function retrieve_remote_url($url) {
	    $cnt = 0;
	    
	    $pos = strpos($url, "cdqr.us");
	    if ($pos === false) {
	        return $url;
	    } else {
	        $count = 0;
	        while(1)   {
	            $count++;
	            
	            if ($count == 20)  {
	                return "";
	            }
	            
	            if ($url == "")
	                return $url;
	            
	            if (strpos($url, "cdqr.us") === false)   {
	                return $url;
	            } else {
	                $url = $this->recursive_retrieve_remote_url($url);
	            }
	        }
	    }
	    
	}
	
	private function recursive_retrieve_remote_url($url)    {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_USERAGENT, "REPORT-SITE URL CHECK");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	     
	    $response = curl_exec($ch);
	     
	    $info = curl_getinfo($ch);
	    $dom = new DOMDocument();
	    $dom->loadHTML($response);
	    $xpath = new DOMXpath($dom);
	    
	    $meta_redirect = $xpath->query("//meta[@http-equiv='refresh']");
	    
	    $returnUrl = array();
	    foreach($meta_redirect as $node)   {
	        list($timeout, $url) = explode(";", $node->getAttribute('content'));
	        list($junk, $url) = explode("=", $url, 2);
	        $url = str_replace("'", "", $url);
	    
	        $returnUrl[] = $url;
	    }
	     
	    return $url;
	}
}

?>
