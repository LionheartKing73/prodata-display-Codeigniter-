<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Adword extends CI_Controller
{

    private $viewArray = array();
    public $campaign_name = "testing";

    function __construct()
    {
                
        parent::__construct();

        //load our new Adwords library
        $this->load->library('parser');
        $this->load->library('MY_Parser');
        $this->load->library('ion_auth');

		
        $this->load->helper(array('form', 'url'));
        return redirect('v2/campaign/campaign_list');
        $this->load->library('form_validation');
        $this->load->library('session');

        $this->load->library('Adwords');
        $this->load->model('Ad_list_model');

        $this->load->model('Criterion_list_model');
        $this->load->model('Group_list_model');
        $this->load->model('Userlist_vertical_model');
        $this->load->model('Userlist_io_model');
        $this->load->model('Group_report_model');
        $this->load->model('Ion_auth_model');
        $this->load->model('Ad_report_model');

        $this->uploadedPath = $this->config->base_url() . 'uploads/';
        $this->viewArray['current_url'] = current_url();
        $this->viewArray['base_url'] = base_url();
        $this->viewArray['site_url'] = site_url();

        $this->viewArray['manage_users'] = false;
        $this->viewArray['show_top_menu'] = true;
        $this->viewArray['take5_user'] = false;
        $this->viewArray["domain"] = "report-site.com";
        $this->viewArray["domain_name"] = "report-site.com";

        $this->no_fraud = array(
            "66.0.218.10",
            "76.110.227.216",
            "50.198.249.13",
            "76.110.217.139"
        );
        $this->viewArray['logo'] = '';

        if(!isset($this->session->userdata["user_id"])) {
            redirect("auth/login");
        }
    }


    /*
     * @description provides a form for creating display ads and adding them as a remarketing
     *
     */

    public function index()
    {
            //adopt the form to the db and add validation rules
            $this->form_validation->set_rules('io', 'IO', 'required');
            $this->form_validation->set_rules('audience_id', 'Campaign Vertical', 'required');
            $this->form_validation->set_rules('start_date', 'Start Date', 'required');
            $this->form_validation->set_rules('destination_url', 'Destination URL', 'required');


            if ($this->form_validation->run() == FALSE) {
                $three_month_ago=date("Y-m-d", strtotime("-3 month"));

                $this->viewArray['ioList'] =$this->Criterion_list_model->getIoForThreeMonths($three_month_ago);
                $this->viewArray['userList'] = $this->Userlist_vertical_model->get_all_users_from_vertical();
                $this->viewArray["files_uploaded"] = null;
                $this->viewArray["date_time"] =date("D M j Y G:i:s");

                $this->parser->parse('adword/index', $this->viewArray);
            } else {
                $data = $this->input->post();
                $res = $this->Group_list_model->wheatherIOExists($data["io"]);

                if (!$res) {
					try{
                    $files = $this->setUploadedTmpFiles($data);
                    if (count($files) > 0) {
                        $date = $data["start_date"];
                        $date = date("Y-m-d H:i:s", strtotime($date));

                        if($data["end_date"] != ""){
                            $data["end_date"] = date("Y-m-d H:i:s", strtotime($data["end_date"]));
                        }else{
                            $data["end_date"]="0000-00-00 00:00:00";
                        }

                        $today = date("Y-m-d H:i:s");


                        if (!strtotime($data["start_date"])  || (!$date["end_date"]  && !strtotime($date["end_date"]))) {
                            $session_data = "Sorry. Some of your data aren't correct. Try again!";
                            $this->session->set_flashdata('error', $session_data);
                            redirect("adword/index");
                        }


                        $created_ads = 0;
                        $session_data = "";
                        $targetDir = 'uploads/permanent/';

                        $audience_id = $data["audience_id"];
                        $status = $data["status"];
                        $displayUrl = $data["destination_url"];

                        $group_status="PAUSED";
                        $ad_status = "scheduled";


                        $group_form_data = ['date' => $date, "max_clicks" => $data["max_clicks"], "max_impressions" => $data["max_impressions"], "max_spend" => $data["max_spend"] * 1000000,
                            "end_date" => $data["end_date"], "date_created" => date("Y-m-d"), "status" => $ad_status, 'io' => $data["io"], 'campaign' => $data["campaign"], "group_status" => $status,
                            "user_id"=>$this->session->userdata["user_id"]];

                            // create ad group

                        $group_id_in_db=$this->Group_list_model->createGroup($group_form_data);

                        //adds display ad data into db
                        foreach ($files as $file) {							
                            $ad_data=["group_id"=>$group_id_in_db,"img_name"=>$file, "ad_status"=>$data["status"], "destination_url"=>$data["destination_url"]];
                            $this->Ad_list_model->createAd($ad_data);

                            ++$created_ads;
							
                        }

                        $criterion_data = ["group_id_in_db"=>$group_id_in_db, "date_created"=>  date("Y-m-d")];

                        //adds remarketing data into the db
                        if ($audience_id && $data["remarketing"]) {

                            $criterion_data['audience_id']=$data["audience_id"];
                            $criterion_data['audience_type']="vertical";
                            $criterion_data['is_remarketing']="Y";
                            $this->Criterion_list_model->createCriterion($criterion_data);

                        }else if($audience_id){

                            $criterion_data['audience_id']=$data["audience_id"];
                            $criterion_data['audience_type']="vertical";
                            $criterion_data['is_remarketing']="N";
                            $this->Criterion_list_model->createCriterion($criterion_data);

                        }

                        if ($data["remarketing"] && $data["expanded_remarketing"] && isset($data["linked_io"])) {

                            foreach($data["linked_io"] as $io) {
                                $criterion_data['audience_id'] = $io;
                                $criterion_data['audience_type'] = "io";
                                $criterion_data['is_remarketing']="Y";
                                $this->Criterion_list_model->createCriterion($criterion_data);
                            }
                        }

                    } else {
                        $session_data = "No Ad or Remarketing has been created as no ad was added by you. Try again.";
                        $this->session->set_flashdata('error', $session_data);
                        redirect("adword/index");
                    }
					$session_data = "Your display campaign has been created.";

                    $this->session->set_flashdata('success', $session_data);
                    $this->session->set_flashdata('scheduled_active', 1);
                    redirect("displayAdList");
					}catch(Exception $e){
						 $session_data = "No Ad or Remarketing has been created as no ad was added by you. Try again.";
                        $this->session->set_flashdata('error', $session_data);
                        redirect("adword/index");
					}
                } else {
                    $session_data = "The campaign with the IO#=" . $data["io"] . " already exists. Try again with another IO#!";
                    $this->session->set_flashdata('error', $session_data);
                    redirect("adword/index");
                }
            }
    }

    //Inserts the total number of each audience of the Vertical campaign
    // into the database table vertical_audience_report
    //for the current date
    public function insertNumOfUsersVertical()
    {
        $user = new Adwords();
        
        $results = $user->getNumberOfUsersIntoAudiences($user);

        $vartical_list_from_db = $this->getVerticalListFromDb();
        $date = date("Y.m.d", strtotime("now"));
        
        //var_dump($results);die;
        $this->load->model('Vertical_audience_report');
        foreach ($results->entries as $result) {
            $audience_id = $result->id;
            if (in_array($audience_id, $vartical_list_from_db, true)) {
                $vertical = $result->name;
                $count = $result->size;

                $data = array("vertical" => $vertical, "date" => $date, "count" => $count, "list_id" => $audience_id);
                $this->Vertical_audience_report->insert($data);
            }
        }
    }
    //Updates the total number of each audience of the Io campaign
    // into Io's database table userlist_io
    //each time, when executes
    private function insertNumOfUsersIo()
    {
        $user = new Adwords();
        $results = $user->getNumberOfUsersIntoAudiences($user);

        $io_list_from_db = $this->getIoListFromDb();
        $date = date("Y.m.d", strtotime("now"));

        foreach ($results->entries as $result) {
            $audience_id = $result->id;
            if (in_array($audience_id, $io_list_from_db, true)) {
                $count = $result->size;
                $this->UserlistIoModel->update_by_attr("remarketing_list_id", $audience_id, array("count" => $count));
            }
        }
    }

    private function getVerticalListFromDb()
    {
        $list = $this->Userlist_vertical_model->select_all_realketing();

        $vartical_list_from_db = array();
        foreach ($list->result() as $vertical_list) {
            $vartical_list_from_db[] = $vertical_list->remarketing_list_id;
        }

        return $vartical_list_from_db;
    }

    private function getIoListFromDb()
    {
        $list = $this->UserlistIoModel->select_all_io();

        $io_list_from_db = array();
        foreach ($list->result() as $io_list) {
            $io_list_from_db[] = $io_list->remarketing_list_id;
        }

        return $io_list_from_db;
    }

    /*
     *  @description adds an image ad into the group
     *  @param $group_id is the id of the group where adds the ad
     *   @param image_url defines the display ad's image
     *   @param $displayUrl defines both the display URL and the ad's URL
     *   @param status defines the $status of the ad and must have one of the values ENABLED, PAUSED, DISABLED
     */

    private function addImageAd($group_id, $image_url, $status, $destination_url)
    {
        $user = new Adwords();
        $result = $user->AddImageAds($user, $group_id, $image_url, $status, $destination_url);
        $result["destination_url"] = $destination_url;


        return $result;
    }

    /*
     *    @description adds remarketing list to the audience
     *    @param audience_id is the id of the audience
     *    @param group_id is the id of the group
     *
     */
    public function addRemarketing(array $data)
    {
        $user = new Adwords();
        $criterion = $user->AddCriterion($user, $data["group_id"], $data["audience_id"]);
        $data["criterion_id"] = $criterion["id"];

        return $data;
    }


    //remove the specified ad both from the account and from the database
    private function removeAd($groupId, $adId, $adName)
    {
        $user = new Adwords();
        $adId = $user->RemoveImageAds($user, $groupId, $adId, $adName);

        $this->Ad_list_model->removeAd($adId);
    }

    /*
     *  @description removes the criterion
     * @param $group_id is the group id which was added as a remarketing
     * @param $criterion_id is the remarketing id
     */

    public function removeRemarketing($group_id, $criterion_id)
    {
        $user = new Adwords();
        $user->RemoveCriterion($user, $group_id, $criterion_id);



        $this->Criterion_list_model->update_criterion_status_by_criterion_id($criterion_id, "completed");
        $this->Ad_list_model->update_ad_status_by_group_id($group_id, "completed");
    }

    /*
     *    @description creates new ad group
     *   @param campaign_name defines the campaign where will be created the ad group
     *  @return created group id
     */

    public function createGroup($groupName = "ad_group_", $status="PAUSED")
    {
        $campaign_name = $this->campaign_name;
        $user = new Adwords();

        $results = $user->GetCampaigns($user);
        $group = $user->AddAdGroups($user, $results[$campaign_name], $groupName, $status);
        $group_id = ($this->getGroupList($campaign_name)[$group->name]);

        return ["group_id" => $group_id, "campaign_id" => $group->campaignId, "group_name" => $group->name];
    }

    /*
     * @description gets the list of the ad groups defined into the campaign 
     * @param campaign_name defines the name of the current campaign
     * @return array
     */

    private function getGroupList($campaign_name = "testing")
    {
        $user = new Adwords();
        $results = $user->GetCampaigns($user);
        $group_list = $user->GetAdGroups($user, $results[$campaign_name]);

        return $group_list;
    }

    /*
     * @description gets the list of the uploaded files came by the Post requeas as an array
     * @param postData is the data came by the Post request
     * @return array
     *
     */

    private function setUploadedTmpFiles($postData)
    {
        if (!empty($postData) && $postData['uploader_count'] > 0) {
            $arrUploadedFiles = array();
            $file_types = ["jpeg", "jpg", "png"];
            $targetDir = 'uploads/tmp/';
            for ($i = 0; $i < $postData['uploader_count']; $i++) {
                if ($postData['uploader_' . $i . '_status'] == 'done') {
                    $fileName = $postData['uploader_' . $i . '_name'];
                    if (in_array(pathinfo($fileName)['extension'], $file_types)) {
                        if (file_exists($targetDir . $fileName)) {
                            $newName = md5(microtime()) . "." . pathinfo($fileName)['extension'];
                            rename($targetDir . $fileName, "uploads/permanent/$newName");
                            $arrUploadedFiles[] = $newName;
                        }
                    } else {
                        $session_data = "There are some not allowed changes are done into the code. Try again!";
                        $this->session->set_flashdata('error', $session_data);
                        redirect("adword/index");
                    }
                }
            }
        }

        return $arrUploadedFiles;
    }


    /*
     *
     * @description This function gets the files came by the Ajax Post request and saves them into the targetDir directory. Calls by ajax.
     * @return ajax response
     * 
     */

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
            if (isset($_REQUEST["name"])) {
                $fileName = $_REQUEST["name"];
            } elseif (!empty($_FILES)) {
                $fileName = $_FILES["file"]["name"];
            } else {
                $fileName = uniqid("file_");
            }


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

            if ($this->checkSize($targetDir)) {
                // Return Success JSON-RPC response
//                die('{"jsonrpc" : "2.0", "result" : ""}');
                print ('{"jsonrpc" : "2.0", "title" :' . "'$name'" . ', "status" : "true"}');
            } else {

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

        $img = $targetDir . $_FILES["file"]["name"];
        $image = new SimpleImage();
        $image->load($img);

        $height = $image->getHeight();
        $width = $image->getWidth();

        if (in_array([$width, $height], $requiredSize)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * @description This function is used to get refresh token
     */

    public function refreshToken()
    {
            $user = new Adwords();
            $user->LogAll();

            $authCode = '';

            $code = $this->input->get('code');


            if (isset($code))
                $authCode = $code;

            // Get the OAuth2 credential.
            $oauth2Info = $user->GetOAuth2Credential($user, $authCode);


            echo "Your Refresh Token is: " . $oauth2Info['refresh_token'] . ". Please add this code in your auth.ini file";
    }


    public function checkIO()
    {
            if ($this->input->is_ajax_request()) {
                $data = $this->input->post();
                $res = $this->Group_list_model->wheatherIOExists($data["io"]);
                echo json_encode(["exists" => $res]);
                die();
            }
    }


    //gets ads's performance report (id, clicks, impressions, budget) for each ad into the account
    //and stores them into the database
    public function getAdPerformance(){
        $user=new Adwords();
        //gets ad performance report
        $ad_report=$user->DownloadAdPerformanceReport($user,null, "XML");

        //converts the report from XML into string
        $ad_report=simplexml_load_string($ad_report);

        $ad_performance=[];

        //adds the ad performance report into the database
        foreach ($ad_report->table->row as $report){
            $adId=(array)$report["adID"];
            $adId=$adId[0];
            $clicks=(array)$report["clicks"];
            $clicks=$clicks[0];
            $impressions=(array)$report["impressions"];
            $impressions=$impressions[0];

            $cost=(array)$report["cost"];
            $cost=$cost[0];

            $ad_performance[]=["adId"=>$adId, "clicks"=>$clicks, "impressions"=>$impressions, "cost"=>$cost];

        }
        return $ad_performance;
    }


    public function onUnload()
    {

    }



    public function getAdGroupReport()
    {
            $user = new Adwords();
            //gets ad performance report
            $res = $this->Group_list_model->select_oldest_campaign();

            $month_number = $res[0]["months"] + 1;

            $report = $user->DownloadAdGroupPerformanceReport($user, null, "XML", $month_number);
            //converts the report from XML into string
            $report = simplexml_load_string($report);
            $performance = [];

            //adds the ad performance report into the database
            foreach ($report->table->row as $report) {
                $id = (array)$report["adGroupID"];
                $id = $id[0];
                $clicks = (array)$report["clicks"];
                $clicks = $clicks[0];
                $impressions = (array)$report["impressions"];
                $impressions = $impressions[0];
                $cost = (array)$report["totalCost"];
                $cost = $cost[0];

                $performance[] = ["id" => $id, "clicks" => $clicks, "impressions" => $impressions, "cost" => $cost];
            }

            return $performance;
    }


    /*
     * @description function removes the ad group from the google ads account
     * @param $group_id is the id of the group into the adwords account
     */
    private function remove_ad_group($group_id)
    {
        $user = new Adwords();

        $user->RemoveAdGroups($user, $group_id);
    }


    public function io($io)
    {
        $io = $this->getIoName($io);
        // Get user list via io
        $arrList = $this->Userlist_io_model->get_userlist_from_io($io);

        // If io doesn't exist, create new list
        if (empty($arrList)) {
            $arrList = $this->createIoList($io);
        }

        $arrList = isset($arrList[0]) ? $arrList[0] : array();
    }

    /*
    * @description This function is used to create new vertical user list if it isn't exist
    * @param $vertical vertical id
   */
    public function vertical($vertical)
    {
        $vertical = $this->getVerticalName($vertical);
        // Get user list via vertical
        $arrList = $this->Userlist_vertical_model->get_userlist_from_vertical($vertical);

        // If vertical doesn't exist, create new list
        if (empty($arrList)) {
            $arrList = $this->createVerticalList($vertical);
        }

        $arrList = isset($arrList[0]) ? $arrList[0] : array();
    }

    /*
        * @description Create new Io list
        * @param $io insertion order id
       */
    protected function createIoList($io)
    {
        $arrList = array();
        $user = new Adwords();
        $pAudience = $user->AddAudience($user, $io);

        // Create new user list in our db
        $bIoCreated = $this->Userlist_io_model->create_userlist_io($io, $pAudience['userList']->id, htmlspecialchars($pAudience['code']->snippet), date("Y-m-d"));

        if ($bIoCreated) {
            $arrList = $this->Userlist_io_model->get_userlist_from_io($io);
        }

        return $arrList;
    }

    /*
     * @description Create new Vertical list
     * @param $vertical vertical id
    */
    protected function createVerticalList($vertical)
    {
        $arrList = array();
        $user = new Adwords();
        $pAudience = $user->AddAudience($user, $vertical);

        // Create new user list in our db
        $bIoCreated = $this->Userlist_vertical_model->create_userlist_vertical($vertical, $pAudience['userList']->id, htmlspecialchars($pAudience['code']->snippet));
        if ($bIoCreated) {
            $arrList = $this->Userlist_vertical_model->get_userlist_from_vertical($vertical);
        }
        return $arrList;
    }

    private function getIoName($io)
    {
        return "Campaign_IO_" . $io;
    }

    private function getVerticalName($vertical)
    {
        return "Campaign_VERTICAL_" . $vertical;
    }

    /*
     * Cron Job Actions
     *
     * */


    /*
     * description gets active groups's group report and stores it into the database
     * */
    public function getGroupReport()
    {
        try {
            $reports = $this->getAdGroupReport();
        }catch (Exception $e) {
            echo  "Sorry. API connection errors has been occured. Try again!";
        }
            foreach ($reports as $report) {
                $data = ["group_id" => $report["id"], "clicks" => $report["clicks"], "impressions" => $report["impressions"], "cost" => $report["cost"], "date_created" => date("Y-m-d H:i:s")];
                $this->Group_report_model->create_report($data);
            }
    }

    /*
   * description gets active groups's group report and stores it into the database
   * */
    public function getAdReport()
    {
        try {
            $reports = $this->getAdPerformance();
        }catch (Exception $e) {
            echo  "Sorry. API connection errors has been occured. Try again!";
        }
        foreach ($reports as $report) {
            $data = ["ad_id" => $report["adId"], "clicks" => $report["clicks"], "impressions" => $report["impressions"], "cost" => $report["cost"], "date_created" => date("Y-m-d H:i:s")];
            $this->Ad_report_model->create_report($data);
        }
    }


    //gets ads's performance report (id, clicks, impressions) for each ad into the account
    //and stores them into the database
    public function getAdApprovalStatus(){
        $user=new Adwords();
        //gets ad performance report
        $ad_report=$user->DownloadAdApprovalReport($user,null, "XML");

        //converts the report from XML into string
        $ad_report=simplexml_load_string($ad_report);
        $ad_performance=[];



        //adds the ad performance report into the database
        foreach ($ad_report->table->row as $report){

            $adId=(array)$report["adID"];
            $adId=$adId[0];
            $disapprovalReasons=(array)$report["disapprovalReasons"];
            $disapprovalReasons=$disapprovalReasons[0];
            $adApprovalStatus=(array)$report["adApprovalStatus"];
            $adApprovalStatus=$adApprovalStatus[0];

            $ad_performance[]=["ad_id"=>$adId, "approval_status"=>$adApprovalStatus, "disapproval_reasons"=>$disapprovalReasons];

        }


        return $ad_performance;
    }

    /*
     * description gets the active ads's approval status and stores into db, removing ad's image into corresponding folder
     * */
    public function updateAdsApprovalStatus()
    {
        $ads = $this->Ad_list_model->select_all_active_ads();
        try {
            $ad_perfromances = $this->getAdApprovalStatus();
        }catch (Exception $e) {
            echo  "Sorry. API connection errors has been occured. Try again!";
        }

        $permanent_dir = "uploads/permanent/";
        $disapp_dir = "uploads/disapproved/";

        foreach ($ads as $ad) {
            foreach($ad_perfromances as $ad_perfromance){
                $ad_perfromance["approval_status"]=strtoupper($ad_perfromance["approval_status"]);


                if($ad["ad_id"]==$ad_perfromance["ad_id"] ){
                    if($ad_perfromance["approval_status"]=="DISAPPROVED"){
                        echo "You display ad with Id=" . $ad["id"] . " has been disapproved <br />";
                    }
                    if($ad["approval_status"] != $ad_perfromance["approval_status"] || $ad["disapproval_reasons"] != $ad_perfromance["disapproval_reasons"]){
                        if ($ad["approval_status"] != "DISAPPROVED" && $ad_perfromance["approval_status"]== "DISAPPROVED") {
                            if (file_exists($permanent_dir . $ad["img_name"])) {
                                rename($permanent_dir . $ad["img_name"], $disapp_dir . $ad["img_name"]);
                            }
                        } else if ($ad["approval_status"] == "DISAPPROVED" && $ad_perfromance["approval_status"] != "DISAPPROVED"){
                            if (file_exists($disapp_dir . $ad["img_name"])) {
                                rename($disapp_dir . $ad["img_name"], $permanent_dir . $ad["img_name"]);
                            }
                            echo "You display ad with Id=" . $ad["id"] . " has been approved";
                        }
                        try {
                            $this->Ad_list_model->update($ad["id"], ["approval_status" => $ad_perfromance["approval_status"], "disapproval_reasons" => $ad_perfromance["disapproval_reasons"]]);
                        }catch (Exception $e) {
                            echo  "Sorry. Database error. Try again!";
                        }
                    }
                }
            }
        }
    }


    /*
     * description deletes all the images from the tmp folder
     * */
    public function emptyTmpFolder()
    {
            $res = array_map('unlink', glob("uploads/tmp/*"));
            echo "<pre />";
            var_dump($res);
    }


    /*
    * @description creates all the remarketing stored for today
    */

    public function createScheduledCampaign()
    {
            $today = date("Y-m-d H:i:s");
            $campaigns = $this->Group_list_model->get_scheduled_campaigns($today);

            foreach ($campaigns as $campaign) {
                try {
                $this->updateGroupStatus($campaign["group_id"], $campaign["group_status"]);
                }catch (Exception $e) {
                    echo  "Sorry. API connection errors has been occured. Try again!";
                }
                try{
                    $this->Group_list_model->update($campaign["id"], ["status" => "active"]);
                    echo "The scheduled IO with IO#=".$campaign["io"]." has been activated <br />";
                }catch (Exception $e) {
                    echo "Some errors has been occured during activating the IO with IO#=".$campaign["io"]."<br />";
                }
            }
    }


    /*
    * @description creates all the remarketing stored for today
    */

    public function completeEndedCampaigns()
    {
            $today = date("Y-m-d");
            $ad_groups = $this->Group_list_model->select_all_active_campaigns();

        try {
            $reports = $this->getAdGroupReport();
        }catch (Exception $e) {
            echo  "Sorry. API connection errors has been occured. Try again!";
        }

            foreach ($ad_groups as $ad_group) {
                if ($ad_group["max_impressions"] != 0 || $ad_group["max_clicks"] != 0 || $ad_group["max_spend"] != 0 || $ad_group["end_date"] != "0000-00-00 00:00:00") {
                    foreach ($reports as $performance) {
                        if ($performance["id"] == $ad_group["group_id"] &&
                            (($ad_group["max_clicks"] != 0 && $performance["clicks"] >= $ad_group["max_clicks"]) ||
                                ($ad_group["max_impressions"] != 0 && $performance["impressions"] >= $ad_group["max_impressions"])
                                || ($ad_group["max_spend"] != 0 && $performance["cost"] >= $ad_group["max_spend"]) ||
                                ($ad_group["end_date"] != "0000-00-00 00:00:00" && $ad_group["end_date"] <= $today))
                        ) {
                            try {
                                $this->updateGroupStatus($ad_group["group_id"], "PAUSED");
                            }catch (Exception $e) {
                                echo  "Sorry. API connection errors has been occured. Try again!";
                            }

                            try {
                                $this->Group_list_model->update($ad_group["id"], array("status" => "completed", "clicks" => $performance["clicks"],
                                    "impressions" => $performance["impressions"], "spend" => $performance["cost"]));
                            }catch (Exception $e) {
                                echo  "Sorry. Some of your data aren't correct. Try again!";
                            }

                            echo "Campaign with IO#=" . $ad_group["io"] . " has been completed <br />";
                        }
                    }
                }
            }
    }

    public function updateGroupStatus($group_id, $status){
        $user=new Adwords();
        $user->UpdateGroupStatus($user, $group_id, $status);
    }


    // public function aaa($group_id="20532862465"){
    //     $user=new Adwords();
    //     $user->AddDemographicsTargeting($user, $group_id);
    // }

}




