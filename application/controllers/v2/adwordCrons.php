<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AdwordCrons extends CI_Controller
{

    private $viewArray = array();
    public $campaign_name = "testing";
    private $admin="harutyun.sardaryan.bw@gmail.com";


    function __construct()
    {
        parent::__construct();

        //load our new Adwords library
        $this->load->library('parser');
        $this->load->library('MY_Parser');
        $this->load->library('ion_auth');

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library('email');

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

    }


    private function send_message($to, $data){
        $message = $this->parser->parse("messages/disapproved_message.tpl", $data, true);

        $subject="DISAPPROVED AD";

        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = "html";
        $config['priority'] = 1;

        $this->email->initialize($config);
        $this->email->from($this->admin, "Jason Korkin");
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);


        $res=$this->email->send();
        echo $this->email->print_debugger();exit;

        return $res;
    }

    public function test(){
        try {
            $res=$this->send_message("harutyun.sardaryan.bw@gmail.com", ["id" => 12565555, "io" => "sdudjjkj"]);
            var_dump($res);
        }catch(Exception $e){
            var_dump("error");
        }
    }
    public function domains(){
        try {
            $this->load->model('google_model_site');
            $campaign = ['id'=>'710333414', 'network_id'=>1];
            $result = $this->google_model_site->create_domain_exclusions_targeting($campaign);
            var_dump($result); exit;
        }catch(Exception $e){
            var_dump("error");
        }
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

    public function addComp()
    {   //var_dump(777); exit;
        $user = new Adwords();
        $result = $user->AddCampaigns($user);
        //$result["destination_url"] = $destination_url;


        return $result;
    }

    public function addAudi()
    {   //var_dump(777); exit;
        $this->load->model("Userlist_io_model");
        $user = new Adwords();
        $audience = $user->addAudience($user, '22805'); //var_dump(777);
        $this->Userlist_io_model->create_userlist_io('22805', 103, $audience['userList']->id, htmlspecialchars($audience['code']->snippet));
        var_dump($audience); exit;
        //$result["destination_url"] = $destination_url;


        return $result;
    }
    public function report()
    {   //var_dump(777); exit;
        $user = new Adwords();
        $result = $user->downloadCriteriaReportWithAwql($user, null, 'XML');
        //$result["destination_url"] = $destination_url;


        return $result;
    }

    public function addCriterias()
    {   //var_dump(777); exit;
        $user = new Adwords();
        $result = $user->createProximityCriteria($user, 385183825, 'aaaa', 10);
        //$result["destination_url"] = $destination_url;
        return $result;
    }

    public function getReport()
    {   //var_dump(777); exit;
        $user = new Adwords();
        $result = $user->getActiveCampaignsPlacementReport($user, null, 'XML');
        $report = simplexml_load_string($result);
        var_dump($report); exit;
        //$result["destination_url"] = $destination_url;
        return $result;
    }

    public function getLoc()
    {   //var_dump(777); exit;
        $cost=(array)'aaaa';
        //$cost=$cost[0];
        var_dump($cost); exit;
        $user = new Adwords();
        $result = $user->getLocationReport($user, null, 'XML');
        $report = simplexml_load_string($result);
        echo '<pre>';
        foreach ($report->table->row as $row) {
            var_dump($row);
        }
        //$result["destination_url"] = $destination_url;
        return $result;
    }

    public function get_ads_report() {   //var_dump(777); exit;
        $user = new Adwords();
        $result = $user->getAllDisapprovedAds($user, array(334178785));
        exit;
    }

    public function demograp() {   //var_dump(777); exit;
        $user = new Adwords();
        $result = $user->createDemographicsTargeting($user, 22119016345);
        var_dump($result); exit;
    }

    public function bid() {   //var_dump(777); exit;
        $user = new Adwords();
        $result = $user->updateGroupBid($user, 22113449425, 0.13);
        var_dump($result); exit;
    }

    public function add_key() {   //var_dump(777); exit;
        $user = new Adwords();
        $result = $user->getAllDisapprovedAds($user, array(334178785));
        exit;
    }

    public function error() { //var_dump(888); exit;
        $user = new Adwords();
        $result = $user->HandlePolicyViolationErrorExample($user, 21418190545); exit;
    }

    /*
     *    @description adds remarketing list to the audience
     *    @param audience_id is the id of the audience
     *    @param group_id is the id of the group
     *
     */
    private function addRemarketing(array $data)
    {
        $user = new Adwords();
        $criterion = $user->AddCriterion($user, $data["group_id"], $data["audience_id"]);
        $data["criterion_id"] = $criterion["id"];

        return $data;
    }

    private function addDemograpihics(array $data)
    {
        $user = new Adwords();
        $demographics = $user->AddDemographicsTargeting($user, $data["group_id"]);
        //$data["criterion_id"] = $criterion["id"];
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

    private function removeRemarketing($group_id=20435619385, $criterion_id=151475753065)
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

    private function createGroup($groupName = "ad_group_", $status="PAUSED")
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

    public function getGroupList($campaign_name = "21117-Sorrel Spa")
    {
        $user = new Adwords();
        $results = $user->GetCampaigns($user);
        $group_list = $user->GetAdGroups($user, $results[$campaign_name]);
        var_dump($group_list); exit;
        return $group_list;
    }


    //gets ads's performance report (id, clicks, impressions, budget) for each ad into the account
    //and stores them into the database
    private function getAdPerformance(){
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



    private function getAdGroupReport()
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


    //gets ads's performance report (id, clicks, impressions) for each ad into the account
    //and stores them into the database
    private function getAdApprovalStatus(){
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

    private function updateGroupStatus($group_id, $status){
        $user=new Adwords();
        $user->UpdateGroupStatus($user, $group_id, $status);
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
            echo  "Sorry. API connection errors has been occurred. Try again!";
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
            echo  "Sorry. API connection errors has been occurred. Try again!";
        }
        foreach ($reports as $report) {
            $data = ["ad_id" => $report["adId"], "clicks" => $report["clicks"], "impressions" => $report["impressions"], "cost" => $report["cost"], "date_created" => date("Y-m-d H:i:s")];
            $this->Ad_report_model->create_report($data);
        }
    }




    /*
     * description gets the active ads approval status and stores into db, removing ad's image into corresponding folder
     * */
    public function updateAdsApprovalStatus()
    {
        $ads = $this->Ad_list_model->select_all_active_ads();
        try {
            $ad_perfromances = $this->getAdApprovalStatus();
        }catch (Exception $e) {
            echo  "Sorry. API connection errors has been occurred. Try again!";
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

    public function completeEndedCampaigns()
    {
            $today = date("Y-m-d");
            $ad_groups = $this->Group_list_model->select_all_active_campaigns();

        try {
            $reports = $this->getAdGroupReport();
        }catch (Exception $e) {
            echo  "Sorry. API connection errors has been occurred. Try again!";
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
                                echo  "Sorry. API connection errors has been occurred. Try again!";
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


    public function createScheduledCampaign() {

        $today = date("Y-m-d H:i:s");

        $campaigns=$this->Group_list_model->get_scheduled_campaigns($today);




        foreach($campaigns as $campaign){
            $remarketings=$this->Criterion_list_model->get_remarketing_by_group_id($campaign["id"]);
            if(count($remarketings)==1 && $remarketings[0]['is_remarketing'] == 'N'){
                $is_demographic_targeting = true;
            } else {
                $is_demographic_targeting = false;
            }
// var_dump($is_demographic_targeting); exit;


            $group_data= $this->createGroup($campaign["io"]."_", $campaign["group_status"]);
            $group_data["status"]="active";
            $group_data["date_created"]=date("Y-m-d");
            $targetDir = 'uploads/permanent/';

            $this->Group_list_model->update($campaign["id"], $group_data);

            echo "The caplaign with IO#=".$campaign["io"]. " has been created successfully <br />";

            $ads=$this->Ad_list_model->get_ad_by_group_id($campaign["id"]);

            foreach($ads as $ad){
                $image_url = base_url() . $targetDir . $ad["img_name"];

                $result = $this->addImageAd($group_data["group_id"], $image_url, $ad["ad_status"], $ad["destination_url"]);

                $this->Ad_list_model->update($ad["id"], $result);

                echo "The Display Ad with id=".$ad["id"]. " has been created successfully <br />";
            }


            if($is_demographic_targeting) {
                $demographic_targeting = $this->addDemograpihics(["group_id" => $group_data["group_id"]]);
            } else {
                foreach($remarketings as $remarketing){
                    if($remarketing["is_remarketing"]=="Y") {
                        $criterion = $this->addRemarketing(["group_id" => $group_data["group_id"], "audience_id" => $remarketing["audience_id"]]);
                        $this->Criterion_list_model->update($remarketing["id"], ["criterion_id" => $criterion["criterion_id"], "group_id" => $group_data["group_id"]]);

                        echo "The Remarketing campaign with id=" . $remarketing["id"] . " has been created successfully <br />";
                    }
                }
            }

        }
    }

}




