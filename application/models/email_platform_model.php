<?php 

require "/var/www/application/libraries/vendor/autoload.php";
use Mailgun\Mailgun;

class Email_platform_model extends CI_Model	{

	protected $CI;
	
	private $id;
	private $mg;
	private $mgb;
	
	private $domain = "prodataverify.com";
	private $campaign_name;
	private $io; // this is the unique ID of the order
	private $from_name;
	private $from_email;
	private $recipient;
	private $message_html;
	private $message_text;
	private $message_tracking = false; // true/false for message tracking (click rewriting)
	private $bounced = "N";
	private $optout = "N";
	private $bounce_code = "";
	private $complainer = "N";
	private $send_date;
	private $vertical;
	private $is_processed = "N";

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();
		
		$this->mg = new Mailgun("key-13ac797989a3af2992b65ab81012bd7b");
		$this->domain = "prodataverify.com";
		
		$this->mgb = $this->mg->MessageBuilder();
	}

	public function create_campaign($data = array())  {
	    if (empty($data))  {
	        return false;
	    }
	    
	    foreach($data as $k=>$v)   {
	        $this->{$k} = $v;
	    }
	    
	    $this->CI->db->insert("email_campaign", array(
	        "from_name" => $this->from_name,
	        "from_email" => $this->from_email,
	        "subject" => $this->subject,
	        "message_html" => $this->message_html,
	        "message_text" => $this->message_text,
	        "send_date" => $this->send_date,
	        "io" => $this->io,
	        "campaign_name" => $this->campaign_name,
	        "vertical" => $this->vertical,
	    ));
	    
	    $this->id = $this->CI->db->insert_id();
	    
	    return $this->id;
	}
	
	public function send()  {
	    if ($this->io == "")
	        throw new exception("io required");
	    
	    $r = $this->CI->db->query("SELECT * FROM email_campaign WHERE io='{$this->io}' LIMIT 1");
	    if ($r->num_rows() > 0)    {
	        $campaign = $r->row_array();
	    } else {
	        throw new exception("campaign does not exist");
	    }
	    
	    $r = $this->CI->db->query("SELECT email FROM email_recipients WHERE io='{$this->io}' AND is_processed='N' AND optout <> 'Y' AND bounced <> 'Y' AND complainer <> 'Y' ORDER BY RAND() LIMIT 1000");

	    $this->mgb->addCampaignId($this->io);
	    $this->mgb->addCustomHeader("IO-Number", $this->io);
	    $this->mgb->setFromAddress($campaign['from_email']);
	    $this->mgb->setSubject($campaign['subject']);
	    $this->mgb->setHtmlBody($campaign['message_html']);
	    $this->mgb->setOpenTracking($this->message_tracking);
	    $this->mgb->setDkim(true);

	    if ($r->num_rows() > 0)    {
	        foreach($r->result_array() as $e)  {
	            if ($e['email'] == "")
	                continue;

	            $this->mgb->addToRecipient($e['email']);

	            print $e['email'] . "\n";
	            
	            $this->CI->db->update("email_recipients", array("last_modified_date" => date("Y-m-d H:i:s"), "is_processed" => "Y"), array("email" => $e['email']));
	        }
	        
	        $result = $this->mg->post("{$this->domain}/messages", $this->mgb->getMessage(), $this->mgb->getFiles());
	        print_r($result);
	    }
	}
	
	public function validate() {
	    if ($this->io == "")
	        throw new exception("io required");
	    
	    $r = $this->CI->db->query("SELECT email FROM email_recipients WHERE io='{$this->io}' AND is_processed='N' AND optout <> 'Y' AND bounced <> 'Y' AND complainer <> 'Y' ORDER BY RAND() LIMIT 100");
	    
	    $mg = new Mailgun("pubkey-02e424a1159354af18f4fdeb009cd5b3");
	    
	    if ($r->num_rows() > 0) {
	        foreach($r->result_array() as $e)  {
	            if ($e['email'] == "")
	                continue;
	            
	            $result = $mg->get("address/validate", array("address" => $e['email']));
	            
	            print_r($result);
	        }
	    }
	}
	

	public function bounce()   {
	    $this->CI->db->update("email_recipients", array("bounced" => "Y", "bounce_code" => $this->bounce_code), array("email" => $this->recipient));
	}

	public function optout()   {
	    $this->CI->db->update("email_recipients", array("optout" => "Y"), array("email" => $this->recipient));
	}
	
	public function complaint()    {
	    $this->CI->db->update("email_recipients", array("complainer" => "Y"), array("email" => $this->recipient));
	}
	
	public function recipient_add($io = "", $email = "") {
	    $insert = array(
	        "io" => $io,
	        "email" => $email,
	        "create_date" => date("Y-m-d H:i:s"),
	        "last_modified_date" => date("Y-m-d H:i:s")
	    );
	    
	    $this->CI->db->insert("email_recipients", $insert);
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
