<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trkreport extends CI_Controller{
    private $viewArray = array();
    private $user_id;
    
    function __construct(){
        parent::__construct();
        $this->load->library('parser');
        
        $this->load->model("Trkreport_model");
        $this->load->model("Country_model");
//        $this->load->model("Trkreport_files_model");
        
        $this->user_id = $this->session->userdata["user_id"];
    }

    public function index() {
        $campaigns = $this->Trkreport_model->get_schedule();
        
        $this->viewArray['campaigns'] = $campaigns;
        $this->viewArray['last_update'] = date("Y-m-d H:i:s");
        
        $this->parser->parse("v2/tracking_report/schedule.php", $this->viewArray);
    }
    
    
    public function campaign($id = 0)   {
        $this->load->model("Trkreport_files_model");
        if ($id > 0) {
            $this->Trkreport_model->id = (int)$id;
            $this->viewArray['campaign_id'] = (int)$id;
            $this->viewArray['campaign'] = $this->Trkreport_model->get_campaign();
        }
        
        $this->viewArray['radius'] = array(10, 15, 25, 35, 50, 75, 100, 125, 150, 200, 250);
        $this->viewArray['states'] = $this->Country_model->get_states_by_country("US");
        $this->viewArray['clients'] = $this->Trkreport_model->get_clients();
        $this->viewArray['files'] = $this->Trkreport_files_model->get_trkreport_files($id);
        $this->viewArray['salesreps'] = $this->Trkreport_model->get_sales_reps();
        
        $this->parser->parse("v2/tracking_report/new_scheduled_campaign.php", $this->viewArray);
    }
    
    public function client_create() {
        $this->Trkreport_model->company = $this->input->post("company");
        $this->Trkreport_model->first_name = $this->input->post("first_name");
        $this->Trkreport_model->last_name = $this->input->post("last_name");
        $this->Trkreport_model->email = $this->input->post("email");
        $this->Trkreport_model->phone = $this->input->post("phone");
        $this->Trkreport_model->address = $this->input->post("address");
        $this->Trkreport_model->city = $this->input->post("city");
        $this->Trkreport_model->state = $this->input->post("state");
        $this->Trkreport_model->zip = $this->input->post("zip");
        
        $status = $this->Trkreport_model->client_create();
        
        if ($status !== false) {
            print json_encode(array("status" => "SUCCESS", "client_id" => $status));
        } else {
            print json_encode(array("status" => "ERROR", "message" => "Client was not created"));
        }
        
        exit;
    }
    
    public function campaign_save() {
        $this->Trkreport_model->client_id = $this->input->post("client_id");
        $this->Trkreport_model->io = $this->input->post("io");
        $this->Trkreport_model->name = $this->input->post("campaign_name");
        $this->Trkreport_model->date_start = $this->input->post("date_start");
        $this->Trkreport_model->date_end = $this->input->post("date_end");
        $this->Trkreport_model->geo_targeting = $this->input->post("geo_targeting");
        $this->Trkreport_model->radius = $this->input->post("radius");
        $this->Trkreport_model->demo_targeting = $this->input->post("demo_targeting");
        $this->Trkreport_model->channel_email = $this->input->post("channel_email");
        $this->Trkreport_model->channel_display = $this->input->post("channel_display");
        $this->Trkreport_model->channel_retarget = $this->input->post("channel_retarget");
        $this->Trkreport_model->channel_social = $this->input->post("channel_social");
        $this->Trkreport_model->status_money_in_house = $this->input->post("status_money_in_house");
        $this->Trkreport_model->status_creative_approved = $this->input->post("status_creative_approved");
        $this->Trkreport_model->status_client_approved = $this->input->post("status_client_approved");
        $this->Trkreport_model->status_deployed = $this->input->post("status_deployed");
        $this->Trkreport_model->notes = $this->input->post("notes");
        $this->Trkreport_model->budget_gross = $this->input->post("budget_gross");
        $this->Trkreport_model->budget_adspend = $this->input->post("budget_adspend");
        $this->Trkreport_model->email_from_name = $this->input->post("email_from_name");
        $this->Trkreport_model->email_subject = $this->input->post("email_subject");
        $this->Trkreport_model->email_count = $this->input->post("email_count");
        $this->Trkreport_model->email_click = $this->input->post("email_click");
        $this->Trkreport_model->email_open = $this->input->post("email_open");
        $this->Trkreport_model->display_impressions = $this->input->post("display_impressions");
        $this->Trkreport_model->display_clicks = $this->input->post("display_clicks");
        $this->Trkreport_model->sales_rep_id = $this->input->post("sales_rep_id");
        
        if ($this->input->post("campaign_id") != "") {
            // this is an update
            $this->Trkreport_model->id = $this->input->post("campaign_id");
            $status = $this->Trkreport_model->campaign_update();
        } else {
            // this is new
            $status = $this->Trkreport_model->campaign_create();
        }
        
        print json_encode(array("status" => "SUCCESS", "campaign_id" => $status));
        
        exit;
    }

    public function file_upload($id=null)
    {
        $this->load->model("Trkreport_files_model");

        $targetDir = 'uploads/trkreports';
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds
        // Create target dir
        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
            @chmod($targetDir, 0777);
        }
        // Get a file name
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
        }



        $fileNameArray = pathinfo($this->input->post('name')); //var_dump($_FILES); exit;
        //var_dump($fileNameArray);die;
        $fileExtension = $fileNameArray['extension'];
        $fileName = $id.'_'.date("Ymd").'_'.md5(microtime());
        $fileFinalName = $fileName . "." . $fileExtension;
//        $fileFinalPath = $targetDir . DS . $fileFinalName;

        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileFinalName;
        // Chunking might be enabled
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
        // Remove old temp files
        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
            }
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
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
        // Return Success JSON-RPC response

        $this->Trkreport_files_model->campaign_id = $id;
        $this->Trkreport_files_model->name = $fileFinalName;
        $this->Trkreport_files_model->original_name = $this->input->post('name');
        $this->Trkreport_files_model->size = $_FILES['file']['size'];
        $this->Trkreport_files_model->datetime = date("Y-m-d H:i:s");
        $this->Trkreport_files_model->create();

        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }
}
