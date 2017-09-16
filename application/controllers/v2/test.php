<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//require_once APPPATH.'third_party/getid3/getid3.php';
//require_once APPPATH.'third_party/getid3/getid3.php';
//require_once APPPATH.'third_party/ffmpeg-php-master/FFmpegAutoloader.php';
class test extends CI_Controller{
    private $viewArray = array();
    private $view_file = null;
    private $user_id;
    function __construct(){
        parent::__construct();
        $this->load->library('parser');
        
        $this->load->helper('url');
        $this->load->library('Adwords');
        $this->load->model('Userlist_io_model');
        $this->load->model('Userlist_vertical_model');
        $this->load->model('Country_model');
        $this->load->model('Domains_model');
        $this->load->model('V2_campclick_impression_model');
        $this->load->model('V2_master_campaign_model');
        $this->load->model('V2_carriers_list_model');
		$this->load->helper(array('language', 'url', 'email'));
        $this->viewArray['base_url'] = base_url();
        $this->view_file = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/' .$this->uri->segment(3);
        $this->user_id = $this->session->userdata["user_id"];
    }
        
    public function create_campaign() {
        $this->parser->parse($this->view_file, $this->viewArray);
        
        //die('create_campaign');die;
    }

    public function create_pdf() {
        $this->load->library('Wkhtmltopdf');
        $path = '/var/www/html/v2/pdf/file';
        //$this->wkhtmltopdf->__set('url', $url.$campaign['id']);
        $this->wkhtmltopdf->__set('mode', 'MODE_SAVE');
        $this->wkhtmltopdf->__set('path', $path.' for 24H.pdf');
        $this->wkhtmltopdf->__set('title', 'ProData Media Campaign Report');
        $this->wkhtmltopdf->__set('url', 'http://reporting.prodata.media/v2/campaign/email_reporting_for_pdf/1958');

        $this->wkhtmltopdf->downloadPDF(); exit;
    }

    public function redirect() {
        header("Location: http://vk.com/"); exit;
        //die('create_campaign');die;
    }

    public function test_memory() { var_dump(555);
        //ini_set('memory_limit', '-1');
        phpinfo(); exit;
        echo '<pre>'; var_dump($this->V2_campclick_impression_model->is_ad_exists(2020));
    }

    public function rand($groups, $ad_id, $campaign_id) {
//        $tot = 100;
//        $groups = 5;
        //var_dump($ad_id, $campaign_id); exit;
        echo '<pre>';
        $campaign = $this->V2_master_campaign_model->get_by_id(null, $campaign_id);
        $impression = $this->V2_campclick_impression_model->get_greate_then_1_impression_count_openrtb($ad_id);
        $tot = $impression["impressions_count"];
        //var_dump($campaign, $impression); exit;
        //var_dump($tot, $campaign_id); exit;
        $numbers = array();
        $max = ceil($tot/$groups);
        $min = ceil($max/1.1);
        for($i = 1; $i < $groups; $i++) {
            $num = rand($min, $max);
            $tot -= $num;
            $numbers[] = $num;
        }
        $numbers[] = $tot;
        var_dump($numbers, array_sum($numbers));  //exit;
        if(array_sum($numbers) == $impression["impressions_count"]){
            foreach ($numbers as $key=>$number){
                if($key == 0) {
                    $this->V2_campclick_impression_model->update($impression['id'], ['impressions_count'=>$number]);
                } else {
                    if($key==1){
                        $date = $campaign['campaign_start_datetime'];
                    } else {
                        $days = $key - 1;
                        if ($days == 1) {
                            $date = date("Y-m-d H:i:s", strtotime($campaign['campaign_start_datetime'] . " +$days day"));
                        } else {
                            $date = date("Y-m-d H:i:s", strtotime($campaign['campaign_start_datetime'] . " +$days days"));
                        }
                    }

                    $insert = array(
                        'ip_address' => $impression['ip_address'],
                        'user_agent' => $impression['user_agent'],
                        'referrer' => $impression['referrer'],
                        'ad_id' => $ad_id,
                        'campaign_id' => $campaign_id,
                        'is_openrtb' => 1,
                        'impressions_count' => $number,
                        'timestamp' => $date
                    );

                    $this->V2_campclick_impression_model->log_impressions_insert(1, $insert);
                }
            }
        } else {
            echo 'chexav';
        }

    }
    public function redirect1() {
        $snip = "<html><head><meta http-equiv='refresh' content=\"{$redirectTime};URL='{$link['destination_url']}'\">{$javascriptGA}</head><body>{$ioScriptTag}{$verticalScriptTag}</body></html>";
        //die('create_campaign');die;
        print $snip;
        exit;
    }
    public function get_ref() {
        $this->load->model('V2_log_model');
        $data = $this->V2_log_model->get_by_campaign_id(31893);
        echo '<pre>';
        foreach($data as $key=>$xml) { //var_dump($xml); exit;
            $invoice = json_decode(json_encode(simplexml_load_string(substr($xml['note'], 29))));
            //$key = $key+1;
            echo ++$key.' : '.$invoice->QBXMLMsgsRs->InvoiceAddRs->InvoiceRet->RefNumber.'<br>';
        }
        exit;
    }
    public function api() {
        $ch = curl_init();
        $test = urlencode('fulfillment@take5mg.com'); //var_dump($test); exit;
        $url = 'http://report-site.com/report_api/iobyemail';
        $data = array (
            'email' => 'fulfillment@take5mg.com',
        );
        $headers = array("X-ProDataFeed-Auth: accf71e711cedbd30e5accd0633d8b44");
        $options = array(
            CURLOPT_URL => $url,
            //CURLOPT_HEADER => true,
            //CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $data
        );
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        if (FALSE === $response) {
            $curlErr = curl_error($ch);
            $curlErrNum = curl_errno($ch);
            curl_close($ch);
            throw new Exception($curlErr, $curlErrNum);
        }
        echo '<pre>';
        $info = curl_getinfo($ch);
        curl_close($ch);
        $test = json_decode(substr($response,0,-38), true);
        var_dump(substr($response,0,-38));
        var_dump($test); exit;
    }
    public function vid() { var_dump(strtotime(date("Y-m-d H:i:s")));
        if(strtotime('2016-05-10 12:08:00')>strtotime(date("Y-m-d H:i:s"))) {
            echo 'true';
        } else {
            echo 'false';
        }
        exit;
        $filePath = 'uploads/tmp/b4a360388a955a63ad022b85ba6b6c34.mov';
        //var_dump(file_exists($filePath));
        //$getID3 = new getID3;
        //$file = $getID3->analyze($filePath);
        try {
            $movie = new FFmpegMovie($filePath);
        } catch(Exception $e) {
            var_dump($e->getMessage()); exit;
        }
        $frame = $movie->getFrame(2); //var_dump($frame);
        //$image1 = $frame->getImage(); var_dump(imagecreatefromstring($image1, 'uploads/tmp/lol.jpg'));
        $image = $frame->toGDImage(); var_dump(is_resource($image),get_resource_type($image),imagejpeg($image,'uploads/tmp/lol2.jpg'));
        //echo '<img src="'.$image.'.jpg">';
        echo '<pre>';
        var_dump($frame); exit;
    }
    
    public function wizard(){
        $this->viewArray['vertical_list'] = $this->Userlist_vertical_model->get_all_users_from_vertical();
        $this->viewArray["domain_list"] = $this->Domains_model->get_all_by_user_id($this->user_id); //var_dump($this->viewArray["domain_list"]); exit;
//        $io = $this->Userlist_io_model->get_all_users_from_io();
//        foreach($io as $item) {
//            $io_array[] = $item['io'];
//        }
        $this->viewArray['io_list'] = $this->Userlist_io_model->get_all_users_from_io();
        $this->viewArray["files_uploaded"] = null;
        //$this->viewArray["files_uploaded"] = null;
        $this->parser->parse($this->view_file, $this->viewArray);
    }
    public function get_io(){
        $io = $this->Userlist_io_model->get_all_users_from_io();
        foreach($io as $item) {
            $io_array[] = $item['io'];
        }
        print json_encode(array("status" => "SUCCESS", "options" => $io_array));
        exit;
    }
    /*
     *
     * @description This function gets the files came by the Ajax Post request and saves them into the targetDir directory. Calls by ajax.
     * @return ajax response
     * 
     */
    public function get_states_by_country(){
        $country = $this->input->post('country');
        $states = [];
        if($country == "US" || $country == "CA"){
            $states = $this->Country_model->get_states_by_country($country);
        }
        if (count($states)) {
            print json_encode(array("status" => "SUCCESS", "states" => $states));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "NO states for this country"));
            exit;
        }
    }
    public function get_carriers_by_country(){
        $country = $this->input->post('country');
        $carriers = [];
        //if($country == "US" || $country == "CA"){
            $carriers = $this->V2_carriers_list_model->get_carriers_list_by_country($country, 1); //1 is id for google network
        //}
        if (count($carriers)) {
            print json_encode(array("status" => "SUCCESS", "carriers" => $carriers));
            exit;
        } else {
            print json_encode(array("status" => "ERROR", "message" => "NO carriers for this country"));
            exit;
        }
    }
    public function check_io(){
        $io = $this->input->post('io');
        //$this->load()->model('V2_master_campaign');
        $io_exist = $this->V2_master_campaign_model->check_io($io);
        if ($io_exist) {
            print json_encode(array("status" => "ERROR"));
            exit;
        } else {
            print json_encode(array("status" => "SUCCESS"));
            exit;
        }
    }
    public function uploadFile()
    {
            $targetDir = 'uploads/tmp/';
        $name=$_POST["name"];
            $cleanupTargetDir = true; // Remove old files
            $maxFileAge = 5 * 3600; // Temp file age in seconds
            //
            // Create target dir
            if (!file_exists($targetDir)) {
                @mkdir($targetDir);
                @chmod($targetDir, 0777);
            }
            // Get a file name
//            if (isset($_REQUEST["name"])) {
//                $fileName = $_REQUEST["name"];
//            } elseif (!empty($_FILES)) {
//                $fileName = $_FILES["file"]["name"];
//            } else {
//                $fileName = uniqid("file_");
//            }
            
            $fileNameArray = pathinfo($this->input->post('name'));
            $fileExtension =  $fileNameArray['extension'];
            $fileName = md5(microtime());
            $fileName = $fileName.".".$fileExtension; 
            
            $filePath = $targetDir . DS . $fileName;
            // Chunking might be enabled
            $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
            $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
            
            // Remove old temp files
            if ($cleanupTargetDir) {
                if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                    die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
                }
                while (($file = readdir($dir)) !== false) {
                    $tmpfilePath = $targetDir . DS . $file;
                    // If temp file is current file proceed to the next
                    if ($tmpfilePath == "{$filePath}.part") {
                        continue;
                    }
                    // Remove temp file if it is older than the max age and is not the current file
                    if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
                        @unlink($tmpfilePath);
                    }
                }
                closedir($dir);
            }
            // Open temp file
            if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            }
            if (!empty($_FILES)) {
                if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                    die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
                }
                // Read binary input stream and append it to temp file
                if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                    die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                }
            } else {
                if (!$in = @fopen("php://input", "rb")) {
                    die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                }
            }
            while ($buff = fread($in, 4096)) {
                fwrite($out, $buff);
            }
            @fclose($out);
            @fclose($in);
            // Check if file has been uploaded
            if (!$chunks || $chunk == $chunks - 1) {
                // Strip the temp .part suffix off
                rename("{$filePath}.part", $filePath);
            }
            @chmod($filePath, 0777);
            
            $size = $this->checkSize($filePath);
            
            if ($size) {
                echo json_encode(['jsonrpc' => '2.0', 'title' => $name, 'status' => true, 'file_dir' => $filePath, 'creative_width' => $size['0'], 'creative_height' => $size['1']]); die;
            } 
            else {
                unlink($targetDir . $_FILES["file"]["name"]);
                $errorMessage = "The $name image\'s size doesn\'t correspond to Google Adword\'s required sizes.";
                print ('{"jsonrpc" : "2.0", "title" :' . "'$name'" . ', "status" : "false"}');
            }
    }
    /**
     * @description this function deletes Uploaded Files. Calls by Ajax
     * @return false on error
     */
    public function deleteUploadedFiles()
    {
            if ($this->input->is_ajax_request()) {
                $data = $this->input->post();
                $targetDir = 'uploads/tmp/';
                $fname = $targetDir . $data["fname"];
                if (file_exists($fname)) {
                    unlink($fname);
                } else {
                    echo 'Files deleted!';
                    die();
                }
            }
            return false;
    }
    /*
     *  @description this function checks wheather the image corresponds to the size or not
     *  @param targetDir is the image directory where is stored the image came by the porst
     *  @param size is the id of the size
     *  @returns true if the image's size corresponds to the size, otherwise false
     *
     */
    private function checkSize($targetDir)
    {
        $requiredSize = [[728, 90], [468, 60], [234, 60], [125, 125], [120, 600], [160, 600], [180, 150], [120, 240], [200, 200], [250, 250],
            [300, 250], [336, 280], [300, 600], [300, 1050], [320, 50], [970, 90], [970, 250]];
                
        $image = new SimpleImage();
        $image->load($targetDir);
        
        $height = $image->getHeight();
        $width = $image->getWidth();
        
        if (in_array([$width, $height], $requiredSize)) {
            return [$width, $height];
        } else {
            return false;
        }
    }
    /*
     * @description This function is used to get refresh token
     */
    
    public function clickmap_ajax()    {
        //libxml_use_internal_errors(true);
        
        $cnt = 0;
        $parsedLinks = array();
        $xml = new DOMDocument();
die('dada');
        $xml->loadHTML($this->input->post("message"));
        $head = $xml->getElementsByTagName("head")->item(0);
        $documentHead = $this->DOMInnerHTML($head);
        $documentHead = "<head>" . $documentHead . "</head>";
        $body = $xml->getElementsByTagName('body')->item(0);
        $documentBody = $this->DOMInnerHTML($body);
        $documentBody = str_ireplace("<body>", "", $documentBody);
        $documentBody = str_ireplace("</body>", "", $documentBody);
        $bodyAttr = "";
        if ($body->hasAttributes()) {
            foreach($body->attributes as $attr) {
                $bodyAttr .= "{$attr->nodeName}='{$attr->nodeValue}' ";
            }
        }
        $documentBody = "<style>.click_border { outline: thick solid #64FE2E !important }</style><body {$bodyAttr}><div id='prodatafeed_hm_master_id'>" . $documentBody . "</div></body>";
        $documentFinal = "<html><head>" . $documentHead . "</head>" . $documentBody . "</html>";
        // lets reset and start over
        $xml->loadHTML($documentFinal);
        //$body->innerHTML = $documentBody;
        // create the click_border
        //$styleDom = $xml->createElement("style", ".click_border { outline: thick solid #64FE2E !important }");
        foreach($xml->getElementsByTagName('a') as $link)  {
            $oldLink = $link->getAttribute("href");
            $link->setAttribute('data-id', $cnt);
            $link->setAttribute('id', "hm_link_" . $cnt);
            $parsedLinks[] = array('href' => $link->getAttribute('href'), 'text' => $text, "link_id" => $cnt);
            $cnt++;
        }
        //$xml->appendChild($styleDom);
        $message = $xml->saveHtml();
        print json_encode(array("status" => "SUCCESS", "content" => $message, "links" => $parsedLinks));
    }
    
    public function html($campaign_id = 2){
        
        if (!$campaign_id || !is_numeric($campaign_id)){
            redirect(base_url());
        }
        
        $this->load->model('V2_ad_model');
        
        $ads = $this->V2_ad_model->get_by_campaign_id($campaign_id);
        
        $this->viewArray['ads'] = $ads;
        
        $this->parser->parse($this->view_file, $this->viewArray);
    }
	
	public function email_test() {
		
		
        send_email('hovhannes.zhamharyan.bw@gmail.com');die(55555);
		
		mail('hovhannes.zhamharyan.bw@gmail.com', 'My Subject', 'lorem ipsum lore ipsum');die;
		
		$this->load->library('email');
		$this->email->from('test@yandex.ru', 'Hovo');
		$this->email->to('Hovhannes.Zhamharyan.bw@gmail.com'); 
		$this->email->cc('Hovhannes.Zhamharyan.bw@gmail.com'); 
		$this->email->bcc('them@their-example.com'); 
		$this->email->subject('ccccccccc');
		$this->email->message('Testing the email class.');	
		$this->email->send();
		echo $this->email->print_debugger();
	}
	
	public function real_test(){
		$this->load->library('Send_email');
		$this->send_email->send_rejected('harutyun.sardaryan.bw@gmail.com', 22222222, 'my campaign');
	}
	
	public function how_many_run_hours_today($c = 2451, $dow = "monday") {
	    $this->load->model("V2_time_parting_model");
	    $this->load->model("V2_campaign_cost_model");
	    
	    $v = $this->V2_time_parting_model->how_many_run_hours_today($c, $dow);
	    print_r($v);
	    
	    $cc = $this->V2_campaign_cost_model->get_hourly_spend_by_campaign($c);
	    print_r($cc);
	    
	    $s = $this->V2_campaign_cost_model->get_hourly_spend_by_campaign($c);
	    print_r($s);
	}
	
	public function mobfox_report()    {
	    $this->load->library("Mobfox");
	    
	    $r = $this->mobfox->report();
	    
	    print_r($r);
	}
	
	public function test_redis_campaign_exists()   {
	    $this->load->library("Clickcap");
	    
	    $r = $this->clickcap->campaign_exists("2697");
	    
	    print_r($r);
	}
	
	public function rmad() {
	    $this->load->model("V2_ad_model");
	    
	    $ad = $this->V2_ad_model->get_by_id(2895);
	    
	    $this->viewArray['ad'] = array(
	        "campaign_id" => $ad['id'],
	        "creative_url" => $ad['creative_url'],
	        "question" => $ad['rm_question'],
	        "answer" => array(
	            "rm_answere1" => $ad['rm_answere1'],
	            "rm_answere2" => $ad['rm_answere2'],
	            "rm_answere3" => $ad['rm_answere3'],
	            "rm_answere4" => $ad['rm_answere4'],
	            "rm_answere5" => $ad['rm_answere5']
	        )
	    );
	    
	    //print_r($this->viewArray['ad']);
	    
	    $this->parser->parse("v2/ads/rich_media_survey.php", $this->viewArray);
	}
	
	public function rmad2()    {
	    $this->load->model("V2_rich_media_survey_model");
	    
	    $this->V2_rich_media_survey_model->campaign_id = 2895;
	    $this->V2_rich_media_survey_model->get_survey_results();
	}
}
