<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class DisplayAdList extends CI_Controller
{

    private $viewArray = array();

    function __construct()
    {
        parent::__construct();

        //load our new Adwords library
        $this->load->library('parser');
        $this->load->library('MY_Parser');

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
        $this->load->model('Ad_report_model');
        $this->load->model('Group_report_model');

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

        if (!isset($this->session->userdata["user_id"])) {
            redirect("auth/login");
        }

    }

    /*
     * @description provides a form for creating display ads and adding them as a remarketing
     *
     */
    public function index()
    {
        $user_id = $this->session->userdata["user_id"];
        $disapproved_groups = $this->Group_list_model->select_all_disapproved_groups($user_id);
        $group_lists = $this->Group_list_model->select_all_existing_groups($user_id);

        foreach ($group_lists as $key => $group_list) {
            $report = $this->Group_report_model->last_record_for_group($group_list["group_id"]);
            if (isset($report["id"])) {
                $group_lists[$key]["clicks"] = $report["clicks"];
                $group_lists[$key]["impressions"] = $report["impressions"];
                $group_lists[$key]["cost"] = $report["cost"];
            } else {
                $group_lists[$key]["clicks"] = 0;
                $group_lists[$key]["impressions"] = 0;
                $group_lists[$key]["cost"] = 0.00;
            }

            $remarketing = $this->Criterion_list_model->get_remarketing_by_group_id($group_list["id"]);

            foreach($remarketing as $rem) {
                if ($rem["audience_type"] == "vertical") {
                    if ($rem["is_remarketing"] == "Y") {
                        $group_lists[$key]["remarketing"] = true;
                    } else {
                        $group_lists[$key]["remarketing"] = false;
                    }
                }
            }

        }


        foreach ($disapproved_groups as $key => $group_list) {

            $report = $this->Group_report_model->last_record_for_group($group_list["group_id"])[0];
            if (isset($report["id"])) {
                $disapproved_groups[$key]["clicks"] = $report["clicks"];
                $disapproved_groups[$key]["impressions"] = $report["impressions"];
                $disapproved_groups[$key]["cost"] = $report["cost"];
            } else {
                $disapproved_groups[$key]["clicks"] = 0;
                $disapproved_groups[$key]["impressions"] = 0;
                $disapproved_groups[$key]["cost"] = 0.00;
            }


            $remarketing = $this->Criterion_list_model->get_remarketing_by_group_id($group_list["id"]);
            if ($remarketing != []) {
                $disapproved_groups[$key]["remarketing"] = true;
            } else {
                $disapproved_groups[$key]["remarketing"] = false;
            }
        }


        $this->viewArray["group_lists"] = $group_lists;
        $this->viewArray["disapproved_groups"] = $disapproved_groups;

        $this->parser->parse('display_ad_list/index', $this->viewArray);

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


    public function view($group_id = "", $range = "hour", $start = "", $end = "")
    {
        $data = $this->input->get();


        if (isset($data["group_id"])) {
            $group_id = $data["group_id"];
        }
        if (isset($data["range"])) {
            $range = $data["range"];
        }
        if (isset($data["start"])) {
            $start = $data["start"];
        }
        if (isset($data["end"])) {
            $end = $data["end"];
        }

        $io = $this->Group_list_model->get_io_by_id($group_id);
        $ad_lists = $this->Ad_list_model->get_existing_ad_by_group_id($group_id);


        $report = [];
        $report["x"] = [];
        $report["y"]["clicks"] = [];
        $report["y"]["impressions"] = [];
        $report["y"]["cost"] = [];
        if ($range == "hour") {
            $report["title"] = "Last 24-Hours";
            for ($i = 0; $i < 24; $i++) {
                $report["x"][] = $i;
                $date = date("Y-m-d", strtotime(" -$i hour"));
                $time = date("H", strtotime(" -$i hour"));

                $res = $this->Group_report_model->report_for_day($io[0]["group_id"], $date, $time);
                if ($res != []) {
                    $report["y"]["clicks"][] = $res[0]["clicks"];
                    $report["y"]["impressions"][] = $res[0]["impressions"];
                    $report["y"]["cost"][] = $res[0]["cost"] / 1000000;
                } else {
                    $report["y"]["clicks"][] = 0;
                    $report["y"]["impressions"][] = 0;
                    $report["y"]["cost"][] = 0.00;
                }
            }
        } else if ($range == "month") {
            $report["title"] = "Last 30-Days";

            for ($i = 0; $i < 30; $i++) {
                $report["x"][] = '"' . date("M-j", strtotime(" -$i month")) . '"';
                $date = date("Y-m-d", strtotime(" -$i month"));

                $res = $this->Group_report_model->report_for_month($io[0]["group_id"], $date);
                if ($res != []) {
                    $report["y"]["clicks"][] = $res[0]["clicks"];
                    $report["y"]["impressions"][] = $res[0]["impressions"];
                    $report["y"]["cost"][] = $res[0]["cost"] / 1000000;
                } else {
                    $report["y"]["clicks"][] = 0;
                    $report["y"]["impressions"][] = 0;
                    $report["y"]["cost"][] = 0.00;
                }
            }


        } else if ($range == "range") {
            $report["title"] = "$start to $end";

            $day_number = (strtotime($end) - strtotime($start)) / 24 / 3600;

            for ($i = 0; $i < $day_number; $i++) {
                $report["x"][] = '"' . date("M-j Y", strtotime(" -$i day")) . '"';
                $date = date("Y-m-d", strtotime(" -$i day"));

                $res = $this->Group_report_model->report_for_month($io[0]["group_id"], $date);
                if ($res != []) {
                    $report["y"]["clicks"][] = $res[0]["clicks"];
                    $report["y"]["impressions"][] = $res[0]["impressions"];
                    $report["y"]["cost"][] = $res[0]["cost"] / 1000000;
                } else {
                    $report["y"]["clicks"][] = 0;
                    $report["y"]["impressions"][] = 0;
                    $report["y"]["cost"][] = 0.00;
                }
            }
        }

        $report["total_clicks"] = array_sum($report["y"]["clicks"]);
        $report["total_impressions"] = array_sum($report["y"]["impressions"]);
        $report["total_cost"] = array_sum($report["y"]["cost"]);

        $report["y"]["clicks"] = implode(",", $report["y"]["clicks"]);
        $report["y"]["impressions"] = implode(",", $report["y"]["impressions"]);
        $report["y"]["cost"] = implode(",", $report["y"]["cost"]);
        $report["x"] = implode(",", $report["x"]);


        foreach ($ad_lists as $key => $ad_list) {
            $ad_report = $this->Ad_report_model->last_record_for_ad($ad_list["ad_id"]);

            if (isset($ad_report[0]["clicks"])) {
                $ad_lists[$key]["clicks"] = $ad_report[0]["clicks"];
                $ad_lists[$key]["impressions"] = $ad_report[0]["impressions"];
                $ad_lists[$key]["cost"] = $ad_report[0]["cost"];
            } else {
                $ad_lists[$key]["clicks"] = 0;
                $ad_lists[$key]["impressions"] = 0;
                $ad_lists[$key]["cost"] = 0.00;
            }
        }


        $reports = $this->Group_report_model->last_record_for_group($io[0]["group_id"]);
        if (isset($reports[0]["clicks"])) {
            $io[0]["clicks"] = $reports[0]["clicks"];
            $io[0]["impressions"] = $reports[0]["impressions"];
            $io[0]["spend"] = $reports[0]["cost"];
        } else {
            $io[0]["clicks"] = 0;
            $io[0]["impressions"] = 0;
            $io[0]["spend"] = 0.00;
        }

        $this->viewArray["ad_lists"] = $ad_lists;
        $this->viewArray["range"] = $range;
        $this->viewArray["io"] = $io;
        $this->viewArray["report"] = $report;


        $this->parser->parse('display_ad_list/view', $this->viewArray);
    }

    public function edit_ad()
    {
        $this->form_validation->set_rules('destination_url', 'Destination URL', 'required');

        if ($this->form_validation->run() == FALSE) {
            $id = $this->input->get()["id"];
            $data = $this->Ad_list_model->select_by_pk($id);
            $this->viewArray["data"] = $data;
            $this->viewArray["files_uploaded"] = null;

            $this->parser->parse('display_ad_list/edit_ad', $this->viewArray);
        } else {
            $data = $this->input->post();
            $result = [];

            $files = $this->setUploadedTmpFiles($data);
            $prev_data = $this->Ad_list_model->select_by_pk_joined_with_groups($data["id"])[0];

            if ($files == []) {
                $files = $prev_data["img_name"];
            } else {
                $files = $files[0];
            }
            $result["img_name"] = $files;
            $image_url = base_url() . "uploads/permanent/$files";

            if ($prev_data["status"] == "active") {
                try {
                    $res = $this->addImageAd($prev_data["adword_group_id"], $image_url, $data["status"], $data["destination_url"]);
                    $this->removeAd($prev_data["adword_group_id"], $prev_data["ad_id"], $prev_data["ad_name"]);
                } catch (Exception $e) {
                    $session_data = "Some of your inserted data aren't correct. Try again!";
                    $this->session->set_flashdata('error', $session_data);
                    redirect("displayAdList/edit_ad?id=" . $data["id"]);
                }

                $result = array_merge($result, $res);
            } else {
                $result = array_merge($result, ["ad_status" => $data["status"], "destination_url" => $data["destination_url"]]);
            }

            $this->Ad_list_model->update($data["id"], $result);

            $session_data = "Your ad has been updated successfully!";
            $this->session->set_flashdata('success', $session_data);
            redirect("displayAdList/view?group_id=" . $prev_data["id"]);
        }
    }

    public function delete_ad()
    {
        $data = $this->input->get();
        $prev_data = $this->Ad_list_model->select_by_pk($data["id"]);

        if ($data["status"] != "scheduled") {
            try {
                $this->removeAd($prev_data[0]["adword_group_id"], $prev_data[0]["ad_id"], $prev_data[0]["ad_name"]);
            } catch (Exception $e) {
                $session_data = "Some errors has been occured. Try again!";
                $this->session->set_flashdata('error', $session_data);
                redirect("displayAdList/view?group_id=" . $prev_data[0]["group_id"]);
            }
        }

        $this->Ad_list_model->update($data["id"], ["ad_status" => "REMOVED"]);

        $session_data = "Your ad has been removed successfully!";
        $this->session->set_flashdata('success', $session_data);
        redirect("displayAdList/view?group_id=" . $prev_data[0]["group_id"]);
    }

    public function create_new_ad()
    {
        $this->form_validation->set_rules('destination_url', 'Destination URL', 'required');

        if ($this->form_validation->run() == FALSE) {
            $id = $this->input->get()["id"];

            $this->viewArray["data"] = $id;
            $this->viewArray["files_uploaded"] = null;

            $this->parser->parse('display_ad_list/create_ad', $this->viewArray);
        } else {
            $data = $this->input->post();

            $result = [];
            $files = $this->setUploadedTmpFiles($data);

            if ($files == []) {
                $session_data = "Sorry. No ad has been created as you haven't selected the Image for the ad. Try again!";
                $this->session->set_flashdata('error', $session_data);
                redirect("displayAdList/create_new_ad?id=" . $data["group_id"]);
            } else {
                $files = $files[0];
            }



            $result["img_name"] = $files;
            $result["group_id"] = $data["group_id"];
            $image_url = base_url() . "uploads/permanent/$files";

            $group_data = $this->Group_list_model->get_io_by_id($data["group_id"]);


            if($group_data[0]["status"]!="scheduled") {
                try {
                    $res = $this->addImageAd($group_data[0]["group_id"], $image_url, $data["status"], $data["destination_url"]);
                } catch (Exception $e) {
                    $session_data = "Some of your inserted data aren't correct. Try again!";
                    $this->session->set_flashdata('error', $session_data);
                    redirect("displayAdList/create_new_ad?id=" . $data["group_id"]);
                }
            }else{
                $res=[];
                $result["ad_status"] = $data["status"];
                $result["destination_url"] = $data["destination_url"];
            }

            $result = array_merge($result, $res);

            $this->Ad_list_model->createAd($result);

            $session_data = "Your ad has been created successfully!";
            $this->session->set_flashdata('success', $session_data);
            redirect("displayAdList/view?group_id=" . $data["group_id"]);
        }
    }


    //remove the specified ad both from the account and from the database
    public function removeAd($groupId = "20164832065", $adId = "77869602265", $adName = "Template image ad #1439795499")
    {
        $user = new Adwords();
        $user->RemoveImageAds($user, $groupId, $adId, $adName);
    }

    /*
    *  @description adds an image ad into the group
    *  @param $group_id is the id of the group where adds the ad
    *   @param image_url defines the display ad's image
    *   @param $displayUrl defines both the display URL and the ad's URL
    *   @param status defines the $status of the ad and must have one of the values ENABLED, PAUSED, DISABLED
    */

    public function addImageAd($group_id = 19717806265, $image_url = 'https://farm4.staticflickr.com/3318/3613298151_0514b1316f.jpg', $status = "PAUSED",
                               $destination_url = "http://www.prodatafeed.com/")
    {

        $user = new Adwords();
        $result = $user->AddImageAds($user, $group_id, $image_url, $status, $destination_url);
        $result["destination_url"] = $destination_url;

        return $result;
    }

    /*
    * @description gets the list of the uploaded files came by the Post requeas as an array
    * @param postData is the data came by the Post request
    * @return array
    *
    */

    private function setUploadedTmpFiles($postData)
    {
        $arrUploadedFiles = [];

        if (!empty($postData) && $postData['uploader_count'] > 0) {
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
                        redirect("displayAdList/index");
                    }
                }
            }
        }
        return $arrUploadedFiles;
    }

    public function remove_ad_group($group_id)
    {
        $user = new Adwords();

        $user->RemoveAdGroups($user, $group_id);
    }

    public function delete_group()
    {
        $id = $this->input->get()["id"];

        $data = $this->Group_list_model->get_io_by_id($id);
        $group_data = $this->Group_list_model->get_io_by_id($id)[0];
        if ($group_data["status"] != "scheduled") {
            try {
                $this->remove_ad_group($group_data["group_id"]);
            } catch (Exception $e) {
                $session_data = "Sorry. There are some errors. Try again!";
                $this->session->set_flashdata('error', $session_data);
                redirect("displayAdList/index");
            }
        }

        $this->Group_list_model->update($id, ["group_status" => "REMOVED"]);

        $session_data = "Your IO with IO#=" . $data[0]["io"] . " has been removed successfully!";
        $this->session->set_flashdata('success', $session_data);
        redirect("displayAdList/index");
    }

    public function edit_group()
    {
        $this->form_validation->set_rules('io', 'IO', 'required');
        $this->form_validation->set_rules('date', 'Start Date', 'required');

        if ($this->form_validation->run() == FALSE) {
            $id = $this->input->get()["id"];
            $remarketings = $this->Criterion_list_model->get_remarketing_by_group_id($id);
            $data = $this->Group_list_model->get_io_by_id($id)[0];

            $data["date"] = date("m/d/Y H:i:s", strtotime($data["date"]));

            if ($data["end_date"] != "0000-00-00 00:00:00") {
                $data["end_date"] = date("m/d/Y H:i:s", strtotime($data["end_date"]));
            }

            $three_month_ago = date("Y-m-d", strtotime("-3 month"));

            $io_list = $this->Criterion_list_model->getIoForThreeMonths($three_month_ago);
            $vertical_list = $this->Userlist_vertical_model->get_all_users_from_vertical();


            foreach ($io_list as $key => $el) {
                $io_list[$key]["selected"] = false;
                foreach ($remarketings as $remark) {
                    if ($el["remarketing_list_id"] == $remark["audience_id"]) {
                        $io_list[$key]["selected"] = true;
                    }
                }
            }


            foreach ($remarketings as $remarketing) {
                if ($remarketing["audience_type"] == "vertical") {
                    $is_remarketing = $remarketing["is_remarketing"];
                }
            }
            $this->viewArray["files_uploaded"] = null;
            $this->viewArray["is_remarketing"] = $is_remarketing;
            $this->viewArray["remarketings"] = $remarketings;
            $this->viewArray["io_list"] = $io_list;
            $this->viewArray["vertical_list"] = $vertical_list;
            $this->viewArray["data"] = $data;
            $this->viewArray["date_time"] = date("D M j Y G:i:s");


            $this->parser->parse('display_ad_list/edit_group', $this->viewArray);
        } else {
            try {
                $data = $this->input->post();

                if (!$data["end_date"]) {
                    $data["end_date"] = "0000-00-00 00:00:00";
                } else {
                    $data["end_date"] = date("Y-m-d H:i:s", strtotime($data["end_date"]));
                }

                $data["date"] = date("Y-m-d H:i:s", strtotime($data["date"]));

                $io = [];


                $prev_data = $this->Group_list_model->get_io_by_id($data["id"])[0]; // get group by id
                $rem_data = $this->Criterion_list_model->get_remarketing_by_group_id($data["id"]); // get old remarketing list
                 
                // if(count($rem_data)==1 && $rem_data[0]['is_remarketing'] == 'N' && $data['remarketing']==1){ //remove demographic target
                //     $is_demographic_targeting = true;

                // } elseif((count($rem_data)>1 || (count($rem_data)==1 && $rem_data[0]['is_remarketing'] == 'Y')) && $data['remarketing']==0) {//create demographic
                //     $is_demographic_targeting = false;
                // }

                if(count($rem_data)==1 && $rem_data[0]['is_remarketing'] == 'N'){ //remove demographic target
                    if($data['remarketing']==1 || ($data['expanded_remarketing']==1 && count( $data['linked_io'] ) !=0 ) ){
                        $this->removeDemograpihics($prev_data["group_id"]);
                    }

                } else {
                    //create demographic
                    if($data['remarketing']==0){
                        $this->addDemograpihics($prev_data["group_id"]);
                    }
                }

                if ($prev_data["io"] != $data["io"]) {
                    $session_data = "Sorry. No changes are realized as the IO# has been changed.Try again!";
                    $this->session->set_flashdata('error', $session_data);
                    redirect("displayAdList/edit_group?id=" . $data["id"]);
                } else {
                    foreach ($rem_data as $key => $rem) {
                        if ($rem["audience_type"] == "vertical") {
                            $vertical = $rem_data[$key];
                        } else if ($rem["audience_type"] == "io") {
                            $io[] = $rem_data[$key];
                        }
                    }


                    if ($data["audience_id"] == $vertical["audience_id"]) { // 
                        if ($data["remarketing"] == 0 && $vertical["is_remarketing"] == "Y") {
                            if ($prev_data["status"] != "scheduled") {
                                try {
                                    $this->removeRemarketing($prev_data["group_id"], $vertical["criterion_id"]);
                                } catch (Exception $e) {
                                    $session_data = "Sorry. There are some errors into your remarketing data. Try again!";
                                    $this->session->set_flashdata('error', $session_data);
                                    redirect("displayAdList/edit_group?id=" . $data["id"]);
                                }
                            }

                            try {
                                $this->Criterion_list_model->update($vertical["id"], ["criterion_id" => null, "group_id" => null, "is_remarketing" => "N"]);
                            } catch (Exception $e) {
                                $session_data = "Sorry. There are some errors into your remarketing data. Try again!";
                                $this->session->set_flashdata('error', $session_data);
                                redirect("displayAdList/edit_group?id=" . $data["id"]);
                            }
                        } else if ($data["remarketing"] == 1 && $vertical["is_remarketing"] == "N") {
                            if ($prev_data["status"] != "scheduled") {
                                try {
                                    $criterion_data = $this->addRemarketing(["group_id" => $prev_data["group_id"], "audience_id" => $data["audience_id"]]);
                                } catch (Exception $e) {
                                    $session_data = "Sorry. There are some errors into your remarketing data. Try again!";
                                    $this->session->set_flashdata('error', $session_data);
                                    redirect("displayAdList/edit_group?id=" . $data["id"]);
                                }
                            }
                            $criterion_data["is_remarketing"] = "Y";
                            $this->Criterion_list_model->update($vertical["id"], $criterion_data);
                        }
                    } else if ($data["audience_id"] != $vertical["audience_id"]) {
                        if ($vertical["is_remarketing"] == "Y") {
                            if ($prev_data["status"] != "scheduled") {
                                try {
                                    $this->removeRemarketing($prev_data["group_id"], $vertical["criterion_id"]);
                                } catch (Exception $e) {
                                    $session_data = "Sorry. There are some errors into your remarketing data. Try again!";
                                    $this->session->set_flashdata('error', $session_data);
                                    redirect("displayAdList/edit_group?id=" . $data["id"]);
                                }
                            }
                            try {
                                $this->Criterion_list_model->update($vertical["id"], ["criterion_id" => null, "group_id" => null]);
                            } catch (Exception $e) {
                                $session_data = "Sorry. There are some errors into your remarketing data. Try again!";
                                $this->session->set_flashdata('error', $session_data);
                                redirect("displayAdList/edit_group?id=" . $data["id"]);
                            }
                        }
                        if ($data["remarketing"] == 0) {
                            $this->Criterion_list_model->update($vertical["id"], ["is_remarketing" => "N"]);
                        } else if ($data["remarketing"] == 1) {
                            if ($prev_data["status"] != "scheduled") {
                                try {
                                    $criterion_data = $this->addRemarketing(["group_id" => $prev_data["group_id"], "audience_id" => $data["audience_id"]]);
                                } catch (Exception $e) {
                                    $session_data = "Sorry. There are some errors into your remarketing data. Try again!";
                                    $this->session->set_flashdata('error', $session_data);
                                    redirect("displayAdList/edit_group?id=" . $data["id"]);
                                }
                            }
                            $criterion_data["is_remarketing"] = "Y";
                            $this->Criterion_list_model->update($vertical["id"], $criterion_data);
                        }
                    }


                    $criterion_data = [];

                    if (!($data["audience_id"] && $data["remarketing"]) || (($data["audience_id"] && $data["remarketing"]) && !($data["linked_io"] && $data["expanded_remarketing"]))) {
                        if ($io) {
                            foreach ($io as $rem) {
                                if ($prev_data["status"] != "scheduled") {
                                    try {
                                        $this->removeRemarketing($rem["group_id"], $rem["criterion_id"]);
                                    } catch (Exception $e) {
                                        $session_data = "Sorry. There are some errors into your remarketing data. Try again!";
                                        $this->session->set_flashdata('error', $session_data);
                                        redirect("displayAdList/edit_group?id=" . $data["id"]);
                                    }
                                }
                                $this->Criterion_list_model->removeCriterionById($rem["id"]);
                            }
                        }
                    } else if ((($data["audience_id"] && $data["remarketing"]) && ($data["linked_io"] && $data["expanded_remarketing"]))) {

                        foreach ($data["linked_io"] as $linked_io) {
                            $res = 0;
                            foreach ($io as $rem) {
                                if ($rem["audience_id"] == $linked_io) {
                                    $res = 1;
                                }
                            }
                            if (!$res) {
                                if ($prev_data["status"] != "scheduled") {
                                    try {
                                        $criterion_data = $this->addRemarketing(["group_id" => $prev_data["group_id"], "audience_id" => $linked_io]);
                                    } catch (Exception $e) {
                                        $session_data = "Sorry. There are some errors into your remarketing data. Try again!";
                                        $this->session->set_flashdata('error', $session_data);
                                        redirect("displayAdList/edit_group?id=" . $data["id"]);
                                    }
                                }
                                $criterion_data["audience_type"] = "io";
                                $criterion_data["date_created"] = date("Y-m-d");
                                $criterion_data["group_id_in_db"] = $prev_data["id"];
                                $criterion_data["audience_id"] = $linked_io;
                                $this->Criterion_list_model->createCriterion($criterion_data);
                            }
                        }
                        foreach ($io as $rem) {
                            $res = 0;
                            foreach ($data["linked_io"] as $linked_io) {
                                if ($rem["audience_id"] == $linked_io) {
                                    $res = 1;
                                }
                            }
                            if (!$res) {
                                if ($prev_data["status"] != "scheduled") {
                                    try {
                                        $this->removeRemarketing($rem["group_id"], $rem["criterion_id"]);
                                    } catch (Exception $e) {
                                        $session_data = "Sorry. There are some errors into your remarketing data. Try again!";
                                        $this->session->set_flashdata('error', $session_data);
                                        redirect("displayAdList/edit_group?id=" . $data["id"]);
                                    }
                                }
                                $this->Criterion_list_model->removeCriterionById($rem["id"]);
                            }
                        }
                    }

                    if ($prev_data["group_status"] != $data["status"] && $prev_data["status"] != "scheduled") {
                        $this->updateGroupStatus($prev_data["group_id"], $data["status"]);
                    }

                    $this->Group_list_model->update($prev_data["id"], ["campaign" => $data["campaign"], "date" => $data["date"], "max_clicks" => $data["max_clicks"],
                        "max_impressions" => $data["max_impressions"], "max_spend" => $data["max_spend"] * 1000000, "end_date" => $data["end_date"], "group_status" => $data["status"]]);

                    $session_data = "Your IO with the IO#=" . $prev_data["io"] . " has been updated successfully!";
                    $this->session->set_flashdata('success', $session_data);
                    redirect("displayAdList/index");
                }
            } catch (Exception $e) {
                $session_data = "Sorry. There are some errors into your remarketing data. Try again!";
                $this->session->set_flashdata('error', $session_data);
                redirect("displayAdList/edit_group?id=" . $data["id"]);
            }
        }
    }
    //gets ads's performance report (id, clicks, impressions, budget) for each ad into the account
    //and stores them into the database
    public function getAdPerformance()
    {
        $user = new Adwords();
        //gets ad performance report
        $ad_report = $user->DownloadAdPerformanceReport($user, null, "XML");

        //converts the report from XML into string
        $ad_report = simplexml_load_string($ad_report);

        $ad_performance = [];

        //adds the ad performance report into the database
        foreach ($ad_report->table->row as $report) {
            $adId = (array)$report["adID"];
            $adId = $adId[0];
            $clicks = (array)$report["clicks"];
            $clicks = $clicks[0];
            $impressions = (array)$report["impressions"];
            $impressions = $impressions[0];

            $cost = (array)$report["cost"];
            $cost = $cost[0];

            $ad_performance[] = ["adId" => $adId, "clicks" => $clicks, "impressions" => $impressions, "cost" => $cost];

        }
        return $ad_performance;
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

    /*
     *  @description removes the criterion
     * @param $group_id is the group id which was added as a remarketing
     * @param $criterion_id is the remarketing id
     */

    public function removeRemarketing($group_id = 20173115065, $criterion_id = 146280073225)
    {
        $user = new Adwords();
        $user->RemoveCriterion($user, $group_id, $criterion_id);
    }

    public function addDemograpihics($group_id)
    {   
        $user = new Adwords();
        $demographics = $user->AddDemographicsTargeting($user, $group_id); 
        return $demographics;
    }

    public function removeDemograpihics($group_id)
    {
        $user = new Adwords();
        $user->RemoveCriterion($user, $group_id, 10);
        $user->RemoveCriterion($user, $group_id, 11);
        return $group_id;
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

        $name = $_POST["name"];


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
  * @description creates all the remarketing stored for today
  */

    public function createScheduledCampaign()
    {
        $today = date("Y-m-d H:i:s");
        $campaigns = $this->Group_list_model->get_scheduled_campaigns($today);

        foreach ($campaigns as $campaign) {
            try {
                $this->updateGroupStatus($campaign["group_id"], $campaign["group_status"]);
            } catch (Exception $e) {
                echo "Sorry. API connection errors has been occured. Try again!";
            }
            try {
                $this->Group_list_model->update($campaign["id"], ["status" => "active"]);
                echo "The scheduled IO with IO#=" . $campaign["io"] . " has been activated <br />";
            } catch (Exception $e) {
                echo "Some errors has been occured during activating the IO with IO#=" . $campaign["io"] . "<br />";
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
        } catch (Exception $e) {
            echo "Sorry. API connection errors has been occured. Try again!";
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
                        } catch (Exception $e) {
                            echo "Sorry. API connection errors has been occured. Try again!";
                        }

                        try {
                            $this->Group_list_model->update($ad_group["id"], array("status" => "completed", "clicks" => $performance["clicks"],
                                "impressions" => $performance["impressions"], "spend" => $performance["cost"]));
                        } catch (Exception $e) {
                            echo "Sorry. Some of your data aren't correct. Try again!";
                        }

                        echo "Campaign with IO#=" . $ad_group["io"] . " has been completed <br />";
                    }
                }
            }
        }
    }

    public function updateGroupStatus($group_id, $status)
    {
        $user = new Adwords();
        $user->UpdateGroupStatus($user, $group_id, $status);
    }

}