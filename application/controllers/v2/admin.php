<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller{

    private $view_file = null;
    public $viewArray = [];
    private $user;

    function __construct() {

        parent::__construct();

        $this->load->helper(["url","form"]);
        $this->load->library('ion_auth');
        $this->load->library("parser");
        $this->load->library('session');

        //is checking accessable domains
        $this->load->library("domain");
        $this->domain->filterDom();

        if ($this->ion_auth->logged_in()) {
            $this->user = $this->ion_auth->user()->row_array();
        }

        if(!$this->user || !$this->user['is_admin']){
            return redirect(base_url());
        }

        $this->viewArray['is_admin'] = true;
        $this->viewArray['domain_data'] = $this->session->userdata['assets'];
        $this->viewArray['assets'] = $this->session->userdata('assets');
        $this->view_file = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/' .$this->uri->segment(3);

    }

    public function users () {
        $this->load->model('V2_users_model');
        $this->load->model('V2_domains_model');
        $this->viewArray['users'] = $this->V2_users_model->get_all_users_with_domain();
        $this->viewArray['domains'] = $this->V2_domains_model->get_domain_list();
        $this->parser->parse($this->view_file, $this->viewArray);
    }

    public function domains(){
        $this->load->model('V2_domains_model');
        $this->viewArray['domains'] = $this->V2_domains_model->get_domain_list();
        $this->parser->parse($this->view_file, $this->viewArray);
    }

    public function add_domain(){
        // check is request ajax
        $this->load->model('V2_domains_model');

        if($this->input->post('domain')){
            $domain = $this->input->post('domain');

            $domain['background_color'] = '#'.$domain['background_color'];
            $domain['footer_color'] = '#'.$domain['footer_color'];
            $domain['active_button_color'] = '#'.$domain['active_button_color'];
            $domain['passive_button_color'] = '#'.$domain['passive_button_color'];
            $domain['block_header_background_color'] = '#'.$domain['block_header_background_color'];
            $domain['block_header_text_color'] = '#'.$domain['block_header_text_color'];
            $domain['block_header_icon_color'] = '#'.$domain['block_header_icon_color'];
            $domain['content_background_color'] = '#'.$domain['content_background_color'];
            $domain['content_text_color'] = '#'.$domain['content_text_color'];
            $domain['block_content_text_color'] = '#'.$domain['block_content_text_color'];

            $insert = $this->V2_domains_model->create($domain);

            if($insert) {
                echo json_encode(['status' => true]);die;
            } else {
                echo json_encode(['status' => false, 'msg' => 'Something went wrong. Please try again.']);die;
            }

        } else {
            echo json_encode(['status' => false, 'msg' => 'Empty post data. Please try again.']);die;
        }

    }

    public function edit_domain($id = null){

        if(!$id || !is_numeric($id)){
            return redirect('v2/admin/domains');
        }

        $this->load->model('V2_domains_model');

        $domain = $this->V2_domains_model->get_domain($id);

        if (!$domain){
            return redirect('v2/admin/domains');
        }

        if($this->input->post()){

            $update_array = ['name' => $this->input->post('url')];

            if ($_FILES['image_file']['name']){

                $new_name = md5(microtime()) . '.' . pathinfo($_FILES['image_file']['name'])['extension'];

                if (move_uploaded_file($_FILES['image_file']['tmp_name'], 'v2/images/domain_logos/' . $new_name)){

                    $update_array['logo'] = $new_name;

                    $file_path = 'v2/images/domain_logos/' . $domain['logo'];
                    if (file_exists($file_path)){
                        unlink($file_path);
                    }
                }

            }

            $this->V2_domains_model->update($id, $update_array);

            return redirect('v2/admin/domains');
        }

        $this->viewArray['domain'] = $domain;
        $this->parser->parse($this->view_file, $this->viewArray);
    }

    public function updateUser(){

        if ($this->input->is_ajax_request()) {

            $name_array = [
                'is_textads',
                'is_email',
                'is_display',
                'is_airpush',
                'is_google',
                'is_facebook',
                'is_branding',
                'is_adtrack',
                'is_displayretarget',
                'is_billing_type',
                'domain_id',
                'display_imp_tier_1',
                'display_imp_tier_2',
                'display_imp_tier_3',
                'display_click_tier_1',
                'display_click_tier_2',
                'display_click_tier_3',
                'display_imp',
                'display_click',
                'min_budget',
                'create_campaign',
                'edit_campaign',
                'is_qb_invoicing',
                'budget_percentage',
                'can_extend_campaigns',
                'financial_manager_id',
                'is_billing',
                'user_email_ability',
                'is_guarantee',
                'is_guarantee_percentage',
                'is_guarantee_upcharge',
                'accounting_ownership_id'
            ];

            if (!in_array($this->input->post('name'), $name_array)){
                echo json_encode(['status' => false, 'message' => 'Something went wrong']);die;
            }

            if (!$this->input->post('pk') || !is_numeric($this->input->post('pk'))){
                echo json_encode(['status' => false, 'message' => 'Something went wrong']);die;
            }

            $this->load->model('V2_users_model');

            $minBudget = 100;
            if($this->input->post('name') == 'is_billing_type') {

                $updatedUserInfo = $this->V2_users_model->get_by_id($this->input->post('pk'));
                $minBudget = $updatedUserInfo['min_budget'];

                if($this->input->post('value') == 'FLAT' && $minBudget <= 100 ) {

                    $minBudget = 150;

                }
                else if($this->input->post('value') == 'FLAT' && $minBudget > 100 ) {

                    $minBudget = 150 + ($minBudget - 100);

                }
                else if($this->input->post('value') == 'PERCENTAGE' && $minBudget > 150 ) {

                    $minBudget = 100 + ($minBudget - 150);

                }
                else if($this->input->post('value') == 'PERCENTAGE' && $minBudget <= 150 ) {

                    $minBudget = 100;

                }


                $this->V2_users_model->update($this->input->post('pk'),[
                    'min_budget' => $minBudget
                ]);


            }


            $this->V2_users_model->update($this->input->post('pk'),[
                $this->input->post('name') => $this->input->post('value')
            ]);


            if ($this->input->post('name') == 'domain_id'){
                echo json_encode(
                        ['status' => true, 'domain_id' => $this->input->post('value'), 'user_id' => $this->input->post('pk')]
                    );
                die;
            }
            else if ($this->input->post('name') == 'is_billing_type') {

                echo json_encode(
                    ['status' => true, 'min_budget' => $minBudget, 'user_id' => $this->input->post('pk')]
                );
                die;

            }
            else {
                echo json_encode(['status' => true]);die;
            }

        }
        else {
            return redirect(base_url());
        }

    }

    public function edit_multiple_networks() {

        $this->load->model('V2_map_users_network_model');
        $this->load->model('V2_network_model');
        $this->load->model('V2_users_multiple_networks_model');
        $this->load->model('V2_users_model');

        $userId = $this->input->get('user_id');
        $activeNetworks = $this->V2_map_users_network_model->get_networks($userId);
        $multipleNetworks = $this->V2_users_multiple_networks_model->get_networks_by_user_id($userId);
        $networks = $this->V2_network_model->get_all_networks();
        $this->viewArray['multiple_networks'] = $multipleNetworks;
        $this->viewArray['active_networks'] = $activeNetworks;
        $this->viewArray['networks'] = $networks;
        $this->viewArray['network_user'] = $userId;
        $this->viewArray['users'] = $this->V2_users_model->get_all_users();

        $this->parser->parse($this->view_file, $this->viewArray);

    }

    public function manage_domains() {


        $this->load->model('V2_users_model');
        $this->load->model('V2_domains_model');

        $domains = $this->V2_domains_model->get_domain_list();

        // $this->viewArray['users'] = $this->V2_users_model->get_all_users_with_domain();
        $this->viewArray['domains'] = $domains;
        // $this->viewArray['branding'] = $this->user['is_branding'];
        // echo "<pre>";
        // print_r($this->viewArray);
        // die;

        $this->parser->parse($this->view_file, $this->viewArray);
    }

    public function delete_domain() {

        if($this->input->is_ajax_request()) {

            $this->load->model('V2_domains_model');
            $id = $this->input->post('id');

            if((int)$id) {
                $domain = $this->V2_domains_model->get_domain($id);

                if (!$domain){
                    echo json_encode(['success' => false]);die;
                }

                $file_path = 'v2/images/domain_logos/' . $domain['logo'];

                if (file_exists($file_path)){
                    unlink($file_path);
                }

                $this->V2_domains_model->delete($id);
                echo json_encode(['success' => true ]);die;

            }

        } else {
            redirect(base_url());
        }

    }

    public function edit_networks($userId) {

        $this->load->model('V2_map_users_network_model');
        $this->load->model('V2_network_model');

        $activeNetworks = $this->V2_map_users_network_model->get_networks($userId);
        $networks = $this->V2_network_model->get_all_networks();
        $this->viewArray['active_networks'] = $activeNetworks;
        $this->viewArray['networks'] = $networks;
        $this->viewArray['network_user'] = $userId;

        $this->parser->parse($this->view_file, $this->viewArray);

    }


    public function network_management()
    {

        $this->load->model('V2_network_model');
        $networks = $this->V2_network_model->get_all_networks();
        $this->viewArray['networks'] = $networks;

        $this->parser->parse($this->view_file, $this->viewArray);
    }

    public function update_bid()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post('name')) {
                $id = $this->input->post('pk');
                $arr_update = array('bid' => $this->input->post('value') );

                if($id && $arr_update) {
                    $this->load->model('V2_network_model');
                    $this->V2_network_model->update_bid($id, $arr_update);
                    echo json_encode(['status' => true, 'message' => 'Bid updated', 'bid' => $this->input->post('value')]);
                } else {
                    echo json_encode(['status' => true, 'message' => 'Something went wrong', 'bid' => $this->input->post('value')]);
                }

            }

        }
    }

    public function update_cpm_bid()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post('name')) {
                $id = $this->input->post('pk');
                $arr_update = array('cpm_bid' => $this->input->post('value') );

                if($id && $arr_update) {
                    $this->load->model('V2_network_model');
                    $this->V2_network_model->update_bid($id, $arr_update);
                    echo json_encode(['status' => true, 'message' => 'CPM Bid updated', 'bid' => $this->input->post('value')]);
                } else {
                    echo json_encode(['status' => true, 'message' => 'Something went wrong', 'bid' => $this->input->post('value')]);
                }

            }

        }
    }

    public function update_percentage_budget()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post('name')) {
                $id = $this->input->post('pk');
                $arr_update = array('percent_of_budget' => $this->input->post('value') );

                if($id && $arr_update) {
                    $this->load->model('V2_users_multiple_networks_model');
                    $this->V2_users_multiple_networks_model->update($id, $arr_update);
                    echo json_encode(['status' => true, 'message' => 'Percent of budget updated', 'percent_of_budget' => $this->input->post('value')]);
                    exit;
                } else {
                    echo json_encode(['status' => true, 'message' => 'Something went wrong', 'percent_of_budget' => $this->input->post('value')]);
                    exit;
                }

            }

        }

    }


    public function add_user_network() {

        if ($this->input->is_ajax_request()) {

            $this->load->model('V2_map_users_network_model');
            $userId = $this->input->post('user_id');
            $network_id = $this->input->post('network');
            $campaign_type = $this->input->post('campaign_type');

            $userNetworkArray = [

                'user_id' => $userId,
                'network_id' => $network_id,
                'campaign_type' => $campaign_type

            ];

            $networkExist = $this->V2_map_users_network_model->check_network_exist($userId, $network_id, $campaign_type);

            if(!$networkExist) {
                $userNewNetwork = $this->V2_map_users_network_model->create($userNetworkArray);

                echo json_encode(['success' => true, 'networkId' => $userNewNetwork ]);die;
            }
            else {

                echo json_encode(['success' => false, 'msg' => 'Network already exist for this user' ]);die;
            }



        }

    }

    public function add_user_multiple_network() {

        if ($this->input->is_ajax_request()) {

            //$this->load->model('V2_map_users_network_model');
            $this->load->model('V2_users_multiple_networks_model');
            $user_id = $this->input->post('user_id');
            $percent_of_budget = $this->input->post('percent_of_budget');
            $general_network_id = $this->input->post('general_network_id');
            $multiple_networks_ids = $this->input->post('multiple_networks_ids');

            foreach($multiple_networks_ids as $multiple_network_id) {

                $userNetworkArray = [

                    'user_id' => $user_id,
                    'general_network_id' => $general_network_id,
                    'multiple_network_id' => $multiple_network_id,
                    'percent_of_budget' => $percent_of_budget,
                    'is_active' => 1

                ];

                $networkExist = $this->V2_users_multiple_networks_model->check_network_exist($user_id, $general_network_id, $multiple_network_id);

                if (!$networkExist) {
                    $userNewNetwork = $this->V2_users_multiple_networks_model->create($userNetworkArray);

                } else {

                    echo json_encode(['success' => false, 'msg' => 'Network already exist for this user']);
                    die;
                }

            }

            echo json_encode(['success' => true]);
            die;

        }

    }

    public function delete_user_network() {

        if($this->input->is_ajax_request()) {

            $this->load->model('V2_map_users_network_model');
            $netId = $this->input->post('net_id');

            if((int)$netId) {

                $this->V2_map_users_network_model->delete_network($netId);
                echo json_encode(['success' => true ]);die;

            }

        }

    }

    public function delete_multiple_network() {

        if($this->input->is_ajax_request()) {

            $this->load->model('V2_users_multiple_networks_model');
            $id = $this->input->post('id');

            if((int)$id) {

                $this->V2_users_multiple_networks_model->delete($id);
                echo json_encode(['success' => true ]);die;

            }

        }

    }

    public function network_managment() {

        $this->load->model('V2_users_model');
        $this->load->model('V2_domains_model');
        $this->viewArray['users'] = $this->V2_users_model->get_all_users_with_domain();
        $this->viewArray['domains'] = $this->V2_domains_model->get_domain_list();

        $this->parser->parse($this->view_file, $this->viewArray);
    }

    public function upload_logo()
    {
        $targetDir = 'v2/images/domain_logos/';


        $name = $_POST["name"];

        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds
        //
        // Create target dir
        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
            @chmod($targetDir, 0777);
        }

        // Chunking might be enabled
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

        $fileNameArray = pathinfo($this->input->post('name'));
        $fileExtension = $fileNameArray['extension'];

        if($chunks){

            $fileName = $fileNameArray['filename'];
            if ($chunk == $chunks - 1) {
                $fileFinalName = md5(microtime());
                $fileFinalName = $fileFinalName . "." . $fileExtension;
                $fileFinalPath = $targetDir . DS . $fileFinalName;
            }
        } else {
            $fileName = md5(microtime());
            $fileName = $fileName . "." . $fileExtension;
        }

        $filePath = $targetDir . DS . $fileName;

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
        //var_dump($chunks, $chunk);
        // Check if file has been uploaded
        if (!$chunks || $chunk == $chunks - 1) {
            // Strip the temp .part suffix off
            rename("{$filePath}.part", $fileFinalPath);
            $filePath = $fileFinalPath;
        }

        @chmod($filePath, 0777);

        echo json_encode(['jsonrpc' => '2.0', 'title' => $name,  'file_name' => $fileFinalName, 'status' => true, 'file_dir' => $filePath]);

    }

    public function create_user_by_type(){
        //create viewer start
        if($this->input->is_ajax_request()) {

            $viewer_email = $this->input->post('viewer_email');
            $viewer_password = $this->input->post('viewer_pass');
            $viewer_name = $this->input->post('viewer_name');
            $type = $this->input->post('type');
            //    $user_type = $this->input->post('address');
            $userData = [
                'email' => $viewer_email,
                'password' => $viewer_password,
                'username'     => $viewer_name,
                'user_type' => $type,
                'active'    => 1
            ];
            $this->load->model("Ion_auth_model");

            $userInfo = $this->Ion_auth_model->register_user($viewer_name, $viewer_password, $viewer_email, $userData);

            if($userInfo) {

                echo json_encode(['success' => true, 'msg' => 'Viewer created successfully', 'id'=> $userInfo['user_id'] ]);die;
            }
            else {
                echo json_encode(['success' => false, 'msg' => "Viewer creation duplicate email" ]);die;
            }


        }
        //create viewer end
    }

}
