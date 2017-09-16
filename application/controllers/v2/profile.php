<?php defined('BASEPATH') OR exit('No direct script access allowed');

session_start();
class Profile extends CI_Controller
{
    public $viewArray = array();
    private $userid;
    protected $CI;
    protected $multiple_budget = 2;
    protected $campaign_budget = 98;
    private $view_file = null;
    private $parent_customer;
    private $user_type;

    public function __construct()	{

        parent::__construct();

        $this->load->helper("url");
        $this->load->helper('cookie');
        $this->load->library('user_agent');
        $this->load->library('pagination');
        $this->load->library('form_validation');

        $this->load->model('Monitor_model');
        $this->load->model("V2_domains_model");
        $this->load->model("Vendor_model");
        $this->load->model("Country_model");
        $this->load->model("V2_master_campaign_model");
        $this->load->model("V2_ad_model");
        $this->load->model('V2_users_model');
        $this->load->model("Zip_model");
        $this->load->model("Report_model");
        $this->load->model("Log_model");
        $this->load->model("Finditquick_model");
        $this->load->model("Facebook_model");

        $this->load->library("parser");
        $this->load->library("session");
        $this->load->library('ion_auth');

        //is checking accessable domains
        $this->load->library("domain");
        $this->domain->filterDom();
        $this->viewArray['domain_data'] = $this->session->userdata['assets'];

        if ($this->ion_auth->logged_in()) {
            $user = $this->ion_auth->user()->row_array();
        }

        //$this->require_auth();

        $this->userid = $user['id'];
        $this->viewArray['user'] = $user;
        $this->user_type = $user['user_type'];
        $this->viewArray['user_type'] = $this->user_type;

        if (!empty($user['is_admin'])){
            $this->viewArray['is_admin'] = true;
        }

        if($user['domain_id']) {
            $domain = $this->V2_domains_model->get_domain($user['domain_id']);
            if($domain['domain'] == $_SERVER["HTTP_HOST"]) {
                $this->viewArray['domain_data'] = $domain;
            }
        }

        $this->viewArray['current_url'] = current_url();
        $this->viewArray['base_url'] = base_url();
        $this->viewArray['site_url'] = site_url();

        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';
        $config['priority'] = 1;

        $this->load->library('email');
        $this->load->library('facebook');

        $this->email->initialize($config);
        $this->view_file = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/' .$this->uri->segment(3);
    }

    public function fb_login_callback() {
        $user_id = $this->session->userdata['user_id'];
        $this->facebook->login_callback($user_id);
    }

    public function assign_user_to_page() {
        if ($this->input->is_ajax_request()) {
            $pages = $this->facebook_model->get_fb_pages($this->session->userdata['user_id']);

            $this->facebook->assign_user_to_page($pages);
        } else {
            redirect(base_url());
        }
    }



    public function index() {
        if($this->session->userdata['user_id']) {
            if($this->user_type == 'viewer') {
                redirect(base_url());
            }

            $user_id = $this->session->userdata['user_id'];

            if($this->input->post('info_update')) {

                $userInfo = $this->V2_users_model->check_email($this->input->post('email'), $this->userid);

                if(!$userInfo) {
                    $userInfoArray['first_name'] = htmlspecialchars($this->input->post('first_name'));
                    $userInfoArray['last_name'] = htmlspecialchars($this->input->post('last_name'));
                    $userInfoArray['email'] = htmlspecialchars($this->input->post('email'));
                    $userInfoArray['company'] = htmlspecialchars($this->input->post('company'));
                    $userInfoArray['address'] = htmlspecialchars($this->input->post('address'));
                    $userInfoArray['city'] = htmlspecialchars($this->input->post('city'));
                    $userInfoArray['state'] = htmlspecialchars($this->input->post('state'));
                    $userInfoArray['zip_code'] = htmlspecialchars($this->input->post('zip_code'));

                    $this->V2_users_model->update($user_id, $userInfoArray);

                    // send rquest to quickbooks for update customer info user
                    $this->load->model('billing_model');
                    $this->billing_model->createCustomer($user_id, "Y");

                }

                redirect('v2/profile/index');

            }

            if($this->input->post('edit_card')) {

                $validCard = $this->checkLuhn($this->input->post('card_number'));

                if($validCard) {

                    $userInfoArray['card_number'] = htmlspecialchars($this->input->post('card_number'));
                    $userInfoArray['card_cvv'] = htmlspecialchars($this->input->post('card_cvv'));
                    $userInfoArray['card_exp_year'] = htmlspecialchars($this->input->post('exp_year'));
                    $userInfoArray['card_exp_month'] = htmlspecialchars($this->input->post('exp_month'));

                    $this->V2_users_model->update($user_id, $userInfoArray);

                    // send rquest to quickbooks for update customer info user
                    $this->load->model('billing_model');
                    $this->billing_model->createCustomer($user_id, "Y");

                }

                redirect('v2/profile/index');

            }

	        $this->viewArray['user'] = $this->V2_users_model->get_by_id($user_id);
            $this->viewArray['viewers'] = $this->V2_users_model->get_all_viewers_by_userid($this->viewArray['user']['id']);


            $this->viewArray['campaigns_information']= $this->V2_master_campaign_model->get_by_user_id($this->viewArray['user']['id']);

            $this->viewArray['campaigns'] = $this->V2_master_campaign_model->get_active_by_user_id($user_id);

            $this->load->model("V2_viewer_campaign_model");

            $this->viewArray['access_campaigns'] = $this->V2_viewer_campaign_model->get_campaigns_by_id($this->viewArray['user']['id']);

            $this->viewArray['months'] = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];


            try {
                $helper = $this->facebook->fb->getRedirectLoginHelper();
            } catch (Exception $e) {
                echo $e->getMessage();
            }


            $permissions = ["pages_show_list", "ads_management","manage_pages"];

            $this->viewArray['loginUrl'] = $helper->getLoginUrl('http://reporting.prodata.media/v2/profile/fb_login_callback', $permissions);

            if ($this->facebook_model->is_linked_to_facebook($user_id)) {
                $this->viewArray['linkedToFacebook'] = true;
            } else {
                $this->viewArray['linkedToFacebook'] = false;
            }

            $this->viewArray['fbUnlinkUrl'] = '/v2/profile/unlinkFromFB';

//            echo '<pre>'; print_r($this->viewArray['user']['card_number']); die;

            $number = $this->viewArray['user']['card_number'];
            $maskingCharacter = 'X';

            $this->viewArray['user']['card_number'] = str_repeat($maskingCharacter, strlen($number) - 4) . substr($number, -4);

            $this->parser->parse('v2/profile/index', $this->viewArray);

        }
        else {
            redirect('/auth/login/index');
        }


    }

    public function reset_password() {

        if($this->input->is_ajax_request()) {

            $olPassword = $this->input->post('old_pass');
            $newPassword = $this->input->post('new_pass');
            $email = $this->input->post('email');

            $this->load->model("Ion_auth_model");

            $changePass = $this->Ion_auth_model->change_password($email, $olPassword, $newPassword);

            if($changePass) {

                echo json_encode(['success' => true, 'msg' => 'Password changed successfully' ]);die;
            }
            else {
                echo json_encode(['success' => false, 'msg' => "Password don't changed" ]);die;
            }

        }

    }

    public function create_viewer(){
        //create viewer start
        if($this->input->is_ajax_request()) {

            $viewer_email = $this->input->post('viewer_email');
            $viewer_password = $this->input->post('viewer_pass');
            $viewer_name = $this->input->post('viewer_name');
            $customer_id = $this->userid;
            //    $user_type = $this->input->post('address');
            $userData = [
                'email' => $viewer_email,
                'password' => $viewer_password,
                'parent_customer' => $customer_id,
                'username'     => $viewer_name,
                'user_type' => 'viewer',
                'active'    => 1
            ];
            $this->load->model("Ion_auth_model");

            $userInfo = $this->Ion_auth_model->register_user($viewer_name, $viewer_password, $viewer_email, $userData);

            if($userInfo) {

                echo json_encode(['success' => true, 'msg' => 'Viewer created successfully' ]);die;
            }
            else {
                echo json_encode(['success' => false, 'msg' => "Viewer creation duplicate email" ]);die;
            }


        }
        //create viewer end
    }

    public function link_to_email(){

        if($this->input->is_ajax_request()) {

            $is_email = $this->input->post('is_email');
            $user_id = $this->userid;

            $user_data = [
                'is_email' => $is_email
            ];

            $user_info = $this->V2_users_model->update($user_id, $user_data);

            if($user_info) {

                echo json_encode(['success' => true, 'msg' => 'Email campaigns linked successfully']);die;
            }
            else {

                echo json_encode(['success' => false, 'msg' => "VError oqured please try again"]);die;
            }

        }
    }

    public function add_viewer_access_to_campaign(){
        if ($this->input->is_ajax_request()) {

            $this->load->model("V2_viewer_campaign_model");

            $this->load->model('V2_map_users_network_model');
            $viewer_id = $this->input->post('viewer');
            $campaign_id = $this->input->post('campaign');


            $access_viewer_exist = $this->V2_viewer_campaign_model->check_access_viewer_exist($viewer_id,$campaign_id,$this->userid);
            //$access_viewer_exist = FALSE ;
            if(!$access_viewer_exist) {
                $new_viewer_access_campaign = $this->V2_viewer_campaign_model->insert_viewer($viewer_id,$campaign_id, $this->userid);

                echo json_encode(['success' => true, 'viewer_id' => $viewer_id ]);die;
             }
            else {

                echo json_encode(['success' => false, 'msg' => 'Network already exist for this user' ]);die;
            }

        }
    }

    public function delete_viewer_access_to_campaign() {

        if($this->input->is_ajax_request()) {

            $this->load->model("V2_viewer_campaign_model");
            $viewer_id = $this->input->post('viewer_id');

            if((int)$viewer_id) {

                $this->V2_viewer_campaign_model->delete_access_viewer($viewer_id);
                echo json_encode(['success' => true ]);die;

            }
        }
    }

    public function check_email() {

        if($this->input->is_ajax_request()) {

            $email = $this->input->post('email');
            $userInfo = $this->V2_users_model->check_email($email, $this->userid);

            if($userInfo) {

                echo json_encode(['success' => false, 'msg' => 'Email exist']);die;
            }
            else {

                echo json_encode(['success' => true, 'msg' => 'Email don"t exist']);die;
            }

        }

    }

    public function checkLuhn($card)  {
        $sum = 0;
        $numdigits = strlen($card);
        $parity = $numdigits % 2;
        for($i=0; $i < $numdigits; $i++) {
        $digit = $card[$i];
            if($i % 2 == $parity) $digit *= 2;
            if($digit > 9) $digit -= 9;
            $sum += $digit;
        }
        return ($sum % 10) == 0;
    }

    public function isLinkedToFB() {
        if($this->input->is_ajax_request()) {
            if ($this->facebook_model->is_linked_to_facebook($this->userid)) {
                print json_encode(array("status" => "SUCCESS", "message" => $this->facebook_model->is_linked_to_facebook($this->userid)));
            } else {
                print json_encode(array("status" => "ERROR", "message" => false));
            }
        } else {
            redirect(base_url());
        }
    }


    public function unlinkFromFB() {
        try {
            $helper = $this->facebook->fb->getRedirectLoginHelper();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $user_id = $this->session->userdata['user_id'];

        $accessToken = $this->facebook_model->get_access_token_from_db($user_id);

        if (!$accessToken) {
            redirect(base_url());
        }

        try {
            $logoutUrl = $helper->getLogoutUrl($accessToken, 'http://reporting.prodata.media/v2/profile/index');
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        if($this->facebook_model->delete_linked_data($user_id)) {
            redirect($logoutUrl);
        } else {
            redirect(base_url());
        }
    }

    public function check_access()
    {
        if ($this->input->is_ajax_request())
        {
            //$page = $this->input->post('page_id');

            //$this->session->userdata['user_id'];
            $token = $this->facebook_model->get_access_token_from_db($this->userid);
            $token = 'CAAXy9TeICeEBAFaOLdNXk0rXJHnonnwGn49RU2u76gSqm3J1G24ZBqMhU0HPLQ45OkJCaOX0tTfPUgTmg6epG6ALrdkYxSIFLQ8VWyvRmKZBioxPzWbgeiQJALoZCGXZArWZBRV9YVKtkr1JJ5Vl2SE1KgS3dCHsX3AxP7e0Nwh8AQ3LrA9on';
            $page_id = $this->input->post('page_id');

            $url = "https://graph.facebook.com/v2.6/1094106567329800/pages?access_token=$token";
            $output = file_get_contents($url); //var_dump($output); exit;
            $result = json_decode($output, true);

            foreach ($result['data'] as $page) {
                if ($page['id'] == $page_id) {
                    $message = $page;
                }
            }

            if (in_array('ADVERTISER', $message['permitted_roles'])) {

                $message['isAdvertiser'] = true;
            } else {
                $message['isAdvertiser'] = false;
            }

            if ($result['data']) {
                print json_encode(array("status" => "SUCCESS", "message" => $message));
            } else {
                print json_encode(array("status" => "ERROR"));
            }
        }

    }

}
