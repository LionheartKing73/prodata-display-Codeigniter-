<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public $logo;
	protected $CCEmail = 'jkorkin@gmail.com';
	public $user;
	//protected $CCEmail = 'harutyun.sardaryan.bw@gmail.com';


	function __construct()
	{

		parent::__construct();

        // $this->load->model('Domains_model');
        // $this->logo = $this->Domains_model->get_domain_logo_by_name($_SERVER['HTTP_HOST']);

		$this->load->library('ion_auth');
		$this->load->library('form_validation');
		$this->load->helper('url');
		$this->load->helper('cookie');
		$this->load->library("session");
		$this->load->library("parser");
		$this->load->library('email');
		$this->load->library('Send_email');
		$this->load->model('v2_users_model');
        $this->logo = $this->session->userdata['assets']['logo'];
		// Load MongoDB library instead of native db driver if required
		//$this->config->item('use_mongodb', 'ion_auth') ?
		//$this->load->library('mongo_db') :
		$this->load->database();
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		$this->lang->load('auth');
		$this->load->helper(array('language', 'url', 'email'));

		$this->data['current_url'] = current_url();
		$this->data['base_url'] = base_url();
		$this->data['site_url'] = site_url();

        //is checking accessable domains
		$this->load->library("domain");
		$this->domain->filterDom();

		$url = parse_url(current_url());
		$this->data['hostname'] = $_SERVER['HTTP_HOST'];
		$this->data['logo'] = $this->ion_auth->get_logo_by_domain($_SERVER['HTTP_HOST']);

		$user_id = $this->session->userdata('user_id');
		$this->user = $this->v2_users_model->get_by_id($user_id);
	}

	//redirect if needed, otherwise display the user list
	function index()	{
            if (!$this->ion_auth->logged_in())
            {
                    //redirect them to the login page
                    //redirect('auth/login', 'refresh');
                    redirect("https://{$this->data['hostname']}/auth/login", 'refresh');
            }
            elseif (!$this->ion_auth->is_admin())
            {
                    //redirect them to the home page because they must be an administrator to view this
                    //redirect('/auth', 'refresh');
                $user = $this->ion_auth->user()->row_array();
                if($user['user_type'] == 'financial_manager') {
                    redirect("https://{$this->data['hostname']}/v2/campaign/financial_report", 'refresh');
                } else {
                    redirect("https://{$this->data['hostname']}/v2/campaign/campaign_list", 'refresh');
                }
            }
		$this->session->set_userdata("count", $this->session->userdata("count") + 1);

		if ($this->session->userdata("count") > 10)	{
			//redirect("/auth/logout");
			redirect("https://{$this->data['hostname']}/auth/logout");
			exit;
		}

		if (!$this->ion_auth->logged_in())
		{
			//redirect them to the login page
			//redirect('auth/login', 'refresh');
			redirect("https://{$this->data['hostname']}/auth/login", 'refresh');
		}
		elseif (!$this->ion_auth->is_admin())
		{
			//redirect them to the home page because they must be an administrator to view this
			//redirect('/auth', 'refresh');
			redirect("https://{$this->data['hostname']}/auth", 'refresh');
		}
		else
		{
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			//list the users
			$this->data['users'] = $this->ion_auth->users()->result();
			foreach ($this->data['users'] as $k => $user)
			{
				$this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
			}

			$this->_render_page('auth/index', $this->data);
		}
	}

	//log the user in
	function login($id = null, $code= null)
	{

		if($this->ion_auth->logged_in()) {
			redirect(base_url());
		}

		if($id && $code) {

			if((int)$id) {

				$this->load->model('V2_users_model');
				$user = $this->V2_users_model->get_by_id($id);

				if(($user['activation_code'] == $code) && ($user['active'] == 0) ) {

					$user['active'] = 1;
					$this->V2_users_model->update($id, $user);
					$this->data['auth_success'] = ['type' => 'success', 'msg' => 'Your account activated successfully' ];

				}
				else {
					$this->data['auth_success'] = ['type' => 'danger', 'msg' => 'Something went wrong' ];
				}

			}

		}

		$this->data['title'] = "Login";
        $this->data['logo'] = '/v2/images/domain_logos/' . $this->logo;

		//validate form input
		$this->form_validation->set_rules('identity', 'Identity', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == true)
		{
			//check to see if the user is logging in
			//check for "remember me"
			$remember = (bool) $this->input->post('remember');

			if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
			{
				//if the login is successful
				//redirect them back to the home page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				if ($this->ion_auth->is_admin())
				{
					//redirect them to the home page because they must be an administrator to view this
					//redirect('/auth', 'refresh');
					redirect("https://{$this->data['hostname']}/auth", 'refresh');

				} else {
    				//redirect('/campclick', 'refresh');
                    $user = $this->ion_auth->user()->row_array();
                    if($user['user_type'] == 'financial_manager') {
                        redirect("https://{$this->data['hostname']}/v2/campaign/financial_report", 'refresh');
                    } else {
                        redirect("https://{$this->data['hostname']}/v2/campaign/campaign_list", 'refresh');
                    }
				}
			}
			else
			{
				//if the login was un-successful
				//redirect them back to the login page
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				//redirect('auth/login', 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
				redirect("https://{$this->data['hostname']}/auth/login", 'refresh');
			}
		}
		else
		{
			$this->load->model('v2_domains_model');

			$domainData = $this->v2_domains_model->getDataByName(substr(base_url(), 0, -1));
			if($domainData) {
				$this->data['domain_logo'] = $domainData['logo'];
				$this->data['active_button_color'] = $domainData['active_button_color'];
			}

			//the user is not logging in so display the login page
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['identity'] = array('name' => 'identity',
				'id' => 'identity',
				'type' => 'text',
				'value' => $this->form_validation->set_value('identity'),
			);
			$this->data['password'] = array('name' => 'password',
				'id' => 'password',
				'type' => 'password',
			);
			$this->_render_page('auth/login', $this->data);
		}
	}

	//log the user out
	function logout()
	{
		$this->data['title'] = "Logout";

		//log the user out
		$logout = $this->ion_auth->logout();

		//redirect them to the login page
		$this->session->set_flashdata('message', $this->ion_auth->messages());
        $this->domain->filterDom();
		//redirect('auth/login', 'refresh');
		redirect("https://{$this->data['hostname']}/auth/login", 'refresh');
	}

	//change password
	function change_password()
	{
		if (!$this->ion_auth->logged_in())
		{
			//redirect them to the login page
			redirect('auth/login', 'refresh');
		}

		$this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
		$this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
		$this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');

		if (!$this->ion_auth->logged_in())
		{
			//redirect('auth/login', 'refresh');
			redirect("https://{$this->data['hostname']}/auth/login", 'refresh');
		}

		$user = $this->ion_auth->user()->row();

		if ($this->form_validation->run() == false)
		{
			//display the form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
			$this->data['old_password'] = array(
				'name' => 'old',
				'id'   => 'old',
				'type' => 'password',
			);
			$this->data['new_password'] = array(
				'name' => 'new',
				'id'   => 'new',
				'type' => 'password',
				'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
			);
			$this->data['new_password_confirm'] = array(
				'name' => 'new_confirm',
				'id'   => 'new_confirm',
				'type' => 'password',
				'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
			);
			$this->data['user_id'] = array(
				'name'  => 'user_id',
				'id'    => 'user_id',
				'type'  => 'hidden',
				'value' => $user->id,
			);

			//render
			$this->_render_page('auth/change_password', $this->data);
		}
		else
		{
			$identity = $this->session->userdata($this->config->item('identity', 'ion_auth'));

			$change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

			if ($change)
			{
				//if the password was successfully changed
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				$this->logout();
			}
			else
			{
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				//redirect('auth/change_password', 'refresh');
				redirect("https://{$this->data['hostname']}/auth/change_password", 'refresh');
			}
		}
	}

	//forgot password
	function forgot_password()
	{
		$this->form_validation->set_rules('email', $this->lang->line('forgot_password_validation_email_label'), 'required');
		if ($this->form_validation->run() == false)
		{
			//setup the input
			$this->data['email'] = array('name' => 'email',
				'id' => 'email',
			);

			if ( $this->config->item('identity', 'ion_auth') == 'username' ){
				$this->data['identity_label'] = $this->lang->line('forgot_password_username_identity_label');
			}
			else
			{
				$this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
			}

			//set any errors and display the form
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			$this->_render_page('auth/forgot_password', $this->data);
		}
		else
		{
			// get identity for that email
			$config_tables = $this->config->item('tables', 'ion_auth');
			$identity = $this->db->where('email', $this->input->post('email'))->limit('1')->get($config_tables['users'])->row();

			//run the forgotten password method to email an activation code to the user
			$forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

			if ($forgotten)
			{
				//if there were no errors
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				//redirect("auth/login", 'refresh'); //we should display a confirmation page here instead of the login page
				redirect("https://{$this->data['hostname']}/auth/login", 'refresh');
			}
			else
			{
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				//redirect("auth/forgot_password", 'refresh');
				redirect("https://{$this->data['hostname']}/auth/forgot_password", 'refresh');
			}
		}
	}

	//reset password - final step for forgotten password
	public function reset_password($code = NULL)
	{
		if (!$code)
		{
			show_404();
		}

		$user = $this->ion_auth->forgotten_password_check($code);

		if ($user)
		{
			//if the code is valid then display the password reset form

			$this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
			$this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

			if ($this->form_validation->run() == false)
			{
				//display the form

				//set the flash data error message if there is one
				$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

				$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
				$this->data['new_password'] = array(
					'name' => 'new',
					'id'   => 'new',
				'type' => 'password',
					'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
				);
				$this->data['new_password_confirm'] = array(
					'name' => 'new_confirm',
					'id'   => 'new_confirm',
					'type' => 'password',
					'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
				);
				$this->data['user_id'] = array(
					'name'  => 'user_id',
					'id'    => 'user_id',
					'type'  => 'hidden',
					'value' => $user->id,
				);
				$this->data['csrf'] = $this->_get_csrf_nonce();
				$this->data['code'] = $code;

				//render
				$this->_render_page('auth/reset_password', $this->data);
			}
			else
			{
				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id'))
				{

					//something fishy might be up
					$this->ion_auth->clear_forgotten_password_code($code);

					show_error($this->lang->line('error_csrf'));

				}
				else
				{
					// finally change the password
					$identity = $user->{$this->config->item('identity', 'ion_auth')};

					$change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

					if ($change)
					{
						//if the password was successfully changed
						$this->session->set_flashdata('message', $this->ion_auth->messages());
						$this->logout();
					}
					else
					{
						$this->session->set_flashdata('message', $this->ion_auth->errors());
						//redirect('auth/reset_password/' . $code, 'refresh');
						redirect("https://{$this->data['hostname']}/auth/reset_password/{$code}", 'refresh');
					}
				}
			}
		}
		else
		{
			//if the code is invalid then send them back to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			//redirect("auth/forgot_password", 'refresh');
			redirect("https://{$this->data['hostname']}/auth/forgot_password", 'refresh');
		}
	}


	//activate the user
	function activate($id, $code=false)
	{
		if ($code !== false)
		{
			$activation = $this->ion_auth->activate($id, $code);
		}
		else if ($this->ion_auth->is_admin())
		{
			$activation = $this->ion_auth->activate($id);
		}

		if ($activation)
		{
			//redirect them to the auth page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			//redirect("auth", 'refresh');
			redirect("https://{$this->data['hostname']}/auth/", 'refresh');
		}
		else
		{
			//redirect them to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			//redirect("auth/forgot_password", 'refresh');
			redirect("https://{$this->data['hostname']}/auth/forgot_password", 'refresh');
		}
	}

	//deactivate the user
	function deactivate($id = NULL)
	{
		if (!$this->ion_auth->logged_in() AND !$this->ion_auth->is_admin())
		{
			//redirect them to the login page
			//redirect('auth/login', 'refresh');
			redirect("https://{$this->data['hostname']}/auth/login", 'refresh');
		}
		$id = $this->config->item('use_mongodb', 'ion_auth') ? (string) $id : (int) $id;

		$this->load->library('form_validation');
		$this->form_validation->set_rules('confirm', $this->lang->line('deactivate_validation_confirm_label'), 'required');
		$this->form_validation->set_rules('id', $this->lang->line('deactivate_validation_user_id_label'), 'required|alpha_numeric');

		if ($this->form_validation->run() == FALSE)
		{
			// insert csrf check
			$this->data['csrf'] = $this->_get_csrf_nonce();
			$this->data['user'] = $this->ion_auth->user($id)->row();

			$this->_render_page('auth/deactivate_user', $this->data);
		}
		else
		{
			// do we really want to deactivate?
			if ($this->input->post('confirm') == 'yes')
			{
				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
				{
					show_error($this->lang->line('error_csrf'));
				}

				// do we have the right userlevel?
				if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin())
				{
					$this->ion_auth->deactivate($id);
				}
			}

			//redirect them back to the auth page
			//redirect('auth', 'refresh');
			redirect("https://{$this->data['hostname']}/auth", 'refresh');
		}
	}

	//create a new user
//	function create_user()
//	{
//		if (!$this->ion_auth->logged_in() AND !$this->ion_auth->is_admin())
//		{
//			//redirect them to the login page
//			//redirect('auth/login', 'refresh');
//			redirect("https://{$this->data['hostname']}/auth/login", 'refresh');
//		}
//
//		$this->data['title'] = "Create User";
//
//		//validate form input
//		$this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required|xss_clean');
//		$this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required|xss_clean');
//		$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email');
//		$this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'required|xss_clean');
//		$this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'required|xss_clean');
//		$this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
//		$this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');
//
//		if ($this->form_validation->run() == true)
//		{
//			$username = strtolower($this->input->post('first_name')) . ' ' . strtolower($this->input->post('last_name'));
//			$email    = $this->input->post('email');
//			$password = $this->input->post('password');
//
//			$additional_data = array(
//				'first_name' => $this->input->post('first_name'),
//				'last_name'  => $this->input->post('last_name'),
//				'company'    => $this->input->post('company'),
//				'phone'      => $this->input->post('phone'),
//			);
//		}
//		if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data))
//		{
//			//check to see if we are creating the user
//			//redirect them back to the admin page
//			$this->session->set_flashdata('message', $this->ion_auth->messages());
//			//redirect("auth", 'refresh');
//			redirect("http://{$this->data['hostname']}/auth", 'refresh');
//		}
//		else
//		{
//			//display the create user form
//			//set the flash data error message if there is one
//			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
//
//			$this->data['first_name'] = array(
//				'name'  => 'first_name',
//				'id'    => 'first_name',
//				'type'  => 'text',
//				'value' => $this->form_validation->set_value('first_name'),
//			);
//			$this->data['last_name'] = array(
//				'name'  => 'last_name',
//				'id'    => 'last_name',
//				'type'  => 'text',
//				'value' => $this->form_validation->set_value('last_name'),
//			);
//			$this->data['email'] = array(
//				'name'  => 'email',
//				'id'    => 'email',
//				'type'  => 'text',
//				'value' => $this->form_validation->set_value('email'),
//			);
//			$this->data['company'] = array(
//				'name'  => 'company',
//				'id'    => 'company',
//				'type'  => 'text',
//				'value' => $this->form_validation->set_value('company'),
//			);
//			$this->data['phone'] = array(
//				'name'  => 'phone',
//				'id'    => 'phone',
//				'type'  => 'text',
//				'value' => $this->form_validation->set_value('phone'),
//			);
//			$this->data['password'] = array(
//				'name'  => 'password',
//				'id'    => 'password',
//				'type'  => 'password',
//				'value' => $this->form_validation->set_value('password'),
//			);
//			$this->data['password_confirm'] = array(
//				'name'  => 'password_confirm',
//				'id'    => 'password_confirm',
//				'type'  => 'password',
//				'value' => $this->form_validation->set_value('password_confirm'),
//			);
//
//			$this->_render_page('auth/create_user', $this->data);
//		}
//	}

	function create_user() {


		if (!$this->ion_auth->logged_in() || $this->user['is_admin'] != '1')
		{
			//redirect them to the login page
			//redirect('auth/login', 'refresh');
			redirect("https://{$this->data['hostname']}/auth/login", 'refresh');
		}

		if($this->input->post()) {

			$validCard = $this->checkLuhn($this->input->post('card'));

			if($validCard) {

				$this->load->model('Ion_auth_model');
				$this->load->model('V2_map_users_network_model');

				$firstName = $this->input->post('first_name');
				$lastName = $this->input->post('last_name');
				$company = $this->input->post('company');
				$address = $this->input->post('address');
				$city = $this->input->post('city');
				$state = $this->input->post('state');
				$zip = $this->input->post('zip');
				$phone = $this->input->post('phone');
				$email = $this->input->post('email');
				$password = $this->input->post('password');
				$card = $this->input->post('card');
//				$cvv = $this->input->post('cvv');
				$exp_month = $this->input->post('exp_month');
				$exp_year = $this->input->post('exp_year');
				if($this->input->post('quickbooks_invoicing')) {
					$is_qb_invoicing = 'Y';
				} else {
					$is_qb_invoicing = 'N';
				}

				$userData = [
				    'ip_address' => $_SERVER['REMOTE_ADDR'],
					'first_name' => $firstName,
					'last_name' => $lastName,
					'company' => $company,
					'address' => $address,
					'city' => $city,
					'state' => $state,
					'zip_code' =>$zip,
					'phone' => $phone,
					'email' => $email,
					'password' => $password,
					'card_number' => $card,
					'card_exp_year' => $exp_year,
					'card_exp_month' => $exp_month,
					'budget_percentage' => 80,
					'display_click' => 0.15,
					'display_imp' => 1.5,
					'display_click_tier_3' => 1,
					'display_click_tier_2' => 0.25,
					'display_click_tier_1' => 0.12,
					'display_imp_tier_3' => 2.4,
					'display_imp_tier_2' => 1.83,
					'display_imp_tier_1' => 1.35,
					'is_textads' => 'N',
					'is_display' => 'Y',
                    'create_campaign' => 'Y',
                    'edit_campaign' => 'Y',
					'is_displayretarget' => 'Y',
					'user_type'         => 'customer',
					'parent_customer'   => 0,
					'is_qb_invoicing' => $is_qb_invoicing
				];

				$userInfo = $this->Ion_auth_model->register($email, $password, $email, $userData);

				if($userInfo) {
					$createdUserId = $userInfo['user_id'];

					$userNetworkArray = ['user_id' => $createdUserId, 'network_id' => 1];

					if($userData['is_textads'] == 'Y') {
						$userNetworkArray['campaign_type'] = 'TEXTAD';
						$this->V2_map_users_network_model->create($userNetworkArray);
					}

					if($userData['is_display'] == 'Y') {
						$userNetworkArray['campaign_type'] = 'DISPLAY';
						$this->V2_map_users_network_model->create($userNetworkArray);
					}

					if($userData['is_displayretarget'] == 'Y') {
						$userNetworkArray['campaign_type'] = 'DISPLAY-RETARGET';
						$this->V2_map_users_network_model->create($userNetworkArray);
					}


					// send rquest to quickbooks for creating customer for new user

					$this->load->model('billing_model');
					if ($this->input->post('create_quickbooks_account')) {
						$this->billing_model->createCustomer($userInfo['user_id']);
					}

					$userInfoId = $userInfo['user_id'];
//					$userInfoCode = $userInfo['activation_code'];


					$subject = 'Welcome to ProData';
					//$link = urlencode();
//					$data = array(
//						'link' => $this->config->base_url().'auth/login/'.$userInfoId.'/'.$userInfoCode
//
//					);
					//echo $data['link'];exit;

					if ($this->input->post('send-email')) {
						$data = [];
						$message = $this->parser->parse("v2/email/welcome.tpl", $data, true);

						$this->send_email->send_welcome_message($email, $subject, $message);
					}


					//var_dump($message);echo 333333; exit;

					//$message = "ooooooooooooooooooooooooo";
					//	send_email($email, $subject, $message);


					$this->data['auth_success'] = [
						'type' => 'success',
						'msg' => 'You have created user successfully.'
					];

				}
				else {
					$this->data['auth_success'] = ['type' => 'danger', 'msg' => 'User with this email already exist' ];
				}

			}
			else {
				$this->data['auth_success'] = ['type' => 'danger', 'msg' => 'Invalid card number' ];
			}

		}

		$this->_render_page('auth/create_user', $this->data);
	}

	//edit a user
	function edit_user($id)
	{

		if (!$this->ion_auth->logged_in() || $this->user['is_admin'] != '1')
		{
			//redirect them to the login page
			//redirect('auth/login', 'refresh');
			redirect("https://{$this->data['hostname']}/auth/login", 'refresh');
		}

		$this->data['title'] = "Edit User";

//		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())
//		{
//			//redirect('auth', 'refresh');
//			redirect("http://{$this->data['hostname']}/auth", 'refresh');
//		}

		$user = $this->ion_auth->user($id)->row();
		$groups=$this->ion_auth->groups()->result_array();
		$currentGroups = $this->ion_auth->get_users_groups($id)->result();

		//validate form input
		$this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'required|xss_clean');
		$this->form_validation->set_rules('company', $this->lang->line('edit_user_validation_company_label'), 'required|xss_clean');
		$this->form_validation->set_rules('groups', $this->lang->line('edit_user_validation_groups_label'), 'xss_clean');

		if (isset($_POST) && !empty($_POST))
		{
			// do we have a valid request?
			if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
			{
				show_error($this->lang->line('error_csrf'));
			}

			$validCard = $this->checkLuhn($this->input->post('card'));

//			$data = array(
//				'first_name' => $this->input->post('first_name'),
//				'last_name'  => $this->input->post('last_name'),
//				'company'    => $this->input->post('company'),
//				'phone'      => $this->input->post('phone'),
//			);

			if($validCard) {

				$this->load->model('Ion_auth_model');
				$this->load->model('V2_map_users_network_model');

				$firstName = $this->input->post('first_name');
				$lastName = $this->input->post('last_name');
				$company = $this->input->post('company');
				$address = $this->input->post('address');
				$city = $this->input->post('city');
				$state = $this->input->post('state');
				$zip = $this->input->post('zip');
				$phone = $this->input->post('phone');
				$email = $this->input->post('email');
				$password = $this->input->post('password');
				$card = $this->input->post('card');
//				$cvv = $this->input->post('cvv');
				$exp_month = $this->input->post('exp_month');
				$exp_year = $this->input->post('exp_year');


				$data = [
					'first_name' => $firstName,
					'last_name' => $lastName,
					'company' => $company,
					'address' => $address,
					'city' => $city,
					'state' => $state,
					'zip_code' => $zip,
					'phone' => $phone,
					'email' => $email,
					'password' => $password,
					'card_number' => $card,
					'card_exp_year' => $exp_year,
					'card_exp_month' => $exp_month,
					'budget_percentage' => 80,
					'display_click' => 0.15,
					'display_imp' => 1.5,
					'display_click_tier_3' => 1,
					'display_click_tier_2' => 0.25,
					'display_click_tier_1' => 0.12,
					'display_imp_tier_3' => 2.4,
					'display_imp_tier_2' => 1.83,
					'display_imp_tier_1' => 1.35,
					'is_textads' => 'N',
					'is_display' => 'Y',
					'is_displayretarget' => 'Y',
					'user_type' => 'customer',
					'parent_customer' => 0

				];

				//Update the groups user belongs to
//			$groupData = $this->input->post('groups');
//
//			if (isset($groupData) && !empty($groupData)) {
//
//				$this->ion_auth->remove_from_group('', $id);
//
//				foreach ($groupData as $grp) {
//					$this->ion_auth->add_to_group($grp, $id);
//				}
//
//			}

				//update the password if it was posted
				if ($this->input->post('password')) {
					$this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth'));
//				$this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');

					$data['password'] = $this->input->post('password');
				}

				if ($this->form_validation->run() === TRUE) {
					$this->ion_auth->update($user->id, $data);

					//check to see if we are creating the user
					//redirect them back to the admin page
					$this->session->set_flashdata('message', "User Saved");
					//redirect("auth", 'refresh');
//				redirect("https://{$this->data['hostname']}/auth", 'refresh');
					redirect($_SERVER['REQUEST_URI'], 'refresh');
				}
			}
		}

		//display the edit user form
		$this->data['csrf'] = $this->_get_csrf_nonce();

		//set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

		//pass the user to the view
		$this->data['user'] = $user;
		$this->data['groups'] = $groups;
		$this->data['currentGroups'] = $currentGroups;

		$this->data['first_name'] = array(
			'name'  => 'first_name',
			'id'    => 'first_name',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('first_name', $user->first_name),
		);
		$this->data['last_name'] = array(
			'name'  => 'last_name',
			'id'    => 'last_name',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('last_name', $user->last_name),
		);
		$this->data['company'] = array(
			'name'  => 'company',
			'id'    => 'company',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('company', $user->company),
		);
		$this->data['phone'] = array(
			'name'  => 'phone',
			'id'    => 'phone',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('phone', $user->phone),
		);
		$this->data['password'] = array(
			'name' => 'password',
			'id'   => 'password',
			'type' => 'password'
		);
		$this->data['password_confirm'] = array(
			'name' => 'password_confirm',
			'id'   => 'password_confirm',
			'type' => 'password'
		);

//		echo '<pre>';
//		print_r($this->data['user']);
//		die;

		$this->_render_page('auth/edit_user', $this->data);
	}

	// create a new group
	function create_group()
	{
		if (!$this->ion_auth->logged_in() AND !$this->ion_auth->is_admin())
		{
			//redirect them to the login page
			//redirect('auth/login', 'refresh');
			redirect("https://{$this->data['hostname']}/auth/login", 'refresh');
		}
		$this->data['title'] = $this->lang->line('create_group_title');

		//validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'required|alpha_dash|xss_clean');
		$this->form_validation->set_rules('description', $this->lang->line('create_group_validation_desc_label'), 'xss_clean');

		if ($this->form_validation->run() == TRUE)
		{
			$new_group_id = $this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('description'));
			if($new_group_id)
			{
				// check to see if we are creating the group
				// redirect them back to the admin page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				//redirect("auth", 'refresh');
				redirect("https://{$this->data['hostname']}/auth", 'refresh');
			}
		}
		else
		{
			//display the create group form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

			$this->data['group_name'] = array(
				'name'  => 'group_name',
				'id'    => 'group_name',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('group_name'),
				);
			$this->data['description'] = array(
				'name'  => 'description',
				'id'    => 'description',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('description'),
			);

			$this->_render_page('auth/create_group', $this->data);
		}
	}

	//edit a group
	function edit_group($id)
	{
		if (!$this->ion_auth->logged_in() AND !$this->ion_auth->is_admin())
		{
			//redirect them to the login page
			//redirect('auth/login', 'refresh');
			redirect("https://{$this->data['hostname']}/auth/login", 'refresh');
		}
		// bail if no group id given
		if(!$id || empty($id))
		{
			//redirect('auth', 'refresh');
			redirect("https://{$this->data['hostname']}/auth", 'refresh');
		}

		$this->data['title'] = $this->lang->line('edit_group_title');

		$group = $this->ion_auth->group($id)->row();

		//validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('edit_group_validation_name_label'), 'required|alpha_dash|xss_clean');
		$this->form_validation->set_rules('group_description', $this->lang->line('edit_group_validation_desc_label'), 'xss_clean');

		if (isset($_POST) && !empty($_POST))
		{
			if ($this->form_validation->run() === TRUE)
			{
				$group_update = $this->ion_auth->update_group($id, $_POST['group_name'], $_POST['group_description']);

				if($group_update)
				{
					$this->session->set_flashdata('message', $this->lang->line('edit_group_saved'));
				}
				else
				{
					$this->session->set_flashdata('message', $this->ion_auth->errors());
				}
				//redirect("auth", 'refresh');
				redirect("https://{$this->data['hostname']}/auth", 'refresh');
			}
		}

                //set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

		//pass the user to the view
		$this->data['group'] = $group;

		$this->data['group_name'] = array(
			'name'  => 'group_name',
			'id'    => 'group_name',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('group_name', $group->name),
		);
		$this->data['group_description'] = array(
			'name'  => 'group_description',
			'id'    => 'group_description',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('group_description', $group->description),
		);

		$this->_render_page('auth/edit_group', $this->data);
	}


	function _get_csrf_nonce()
	{
		$this->load->helper('string');
		$key   = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return array($key => $value);
	}

	function _valid_csrf_nonce()
	{
		if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
			$this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue'))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function _render_page($view, $data=null, $render=false)
	{

		$this->viewdata = (empty($data)) ? $this->data: $data;

		$view_html = $this->load->view($view, $this->viewdata, $render);

		if (!$render) return $view_html;
	}

    public function signup(){

		if($this->input->post()) {

			$validCard = $this->checkLuhn($this->input->post('card'));

			if($validCard) {

				$this->load->model('Ion_auth_model');
				$this->load->model('V2_map_users_network_model');

				$firstName = $this->input->post('first_name');
				$lastName = $this->input->post('last_name');
				$company = $this->input->post('company');
				$address = $this->input->post('address');
				$city = $this->input->post('city');
				$state = $this->input->post('state');
				$zip = $this->input->post('zip');
				$phone = $this->input->post('phone');
				$email = $this->input->post('email');
				$password = $this->input->post('password');
				$card = $this->input->post('card');
//				$cvv = $this->input->post('cvv');
				$exp_month = $this->input->post('exp_month');
				$exp_year = $this->input->post('exp_year');


				$userData = [
					'first_name' => $firstName,
					'last_name' => $lastName,
					'company' => $company,
					'address' => $address,
					'city' => $city,
					'state' => $state,
					'zip_code' =>$zip,
					'phone' => $phone,
					'email' => $email,
					'password' => $password,
					'card_number' => $card,
					'card_exp_year' => $exp_year,
					'card_exp_month' => $exp_month,
					'budget_percentage' => 80,
					'display_click' => 0.15,
					'display_imp' => 1.5,
					'display_click_tier_3' => 1,
					'display_click_tier_2' => 0.25,
					'display_click_tier_1' => 0.12,
					'display_imp_tier_3' => 2.4,
					'display_imp_tier_2' => 1.83,
					'display_imp_tier_1' => 1.35,
					'is_airpush' => 'Y',
					'is_fiq' => 'Y',
					'is_google' => 'Y',
					'is_facebook' => 'Y',
					'create_campaign' => 'Y',
					'edit_campaign' => 'Y',
					'user_type'         => 'customer',
					'parent_customer'   => 0

				];

				$userInfo = $this->Ion_auth_model->register_user($email, $password, $email, $userData);

				if($userInfo) {

					$createdUserId = $userInfo['user_id'];

					$userNetworkArray = ['user_id' => $createdUserId];

					if($userData['is_airpush'] == 'Y') {
						$campaignTypes = array(
							'IN_APP',
							'OVERLAY_AD',
							'PUSH_CLICK_TO_CALL',
							'DIALOG_CLICK_TO_CALL',
							'RICH_MEDIA_INTERSTITIAL',
						);
						foreach($campaignTypes as $type){
							$userNetworkArray['campaign_type'] = $type;
							$userNetworkArray['network_id'] = 4;
							$this->V2_map_users_network_model->create($userNetworkArray);
						}

					}

					if($userData['is_google'] == 'Y') {
						$campaignTypes = array(
							'DISPLAY',
							'TEXTAD',
							'DISPLAY-RETARGET',
						);
						foreach($campaignTypes as $type){
							$userNetworkArray['campaign_type'] = $type;
							$userNetworkArray['network_id'] = 1;
							$this->V2_map_users_network_model->create($userNetworkArray);
						}
					}

					if($userData['is_fiq'] == 'Y') {
						$campaignTypes = array(
							'TEXTAD',
						);
						foreach($campaignTypes as $type){
							$userNetworkArray['campaign_type'] = $type;
							$userNetworkArray['network_id'] = 2;
							$this->V2_map_users_network_model->create($userNetworkArray);
						}
					}

					if($userData['is_facebook'] == 'Y') {
						$campaignTypes = array(
							'FB-MOBILE-NEWS-FEED',
							'FB-DESKTOP-RIGHT-COLUMN',
							'FB-DESKTOP-NEWS-FEED',
							'FB-PAGE-LIKE',
							'FB-VIDEO-VIEWS',
							'FB-VIDEO-CLICKS',
							'FB-LOCAL-AWARENESS',
							'FB-PROMOTE-EVENT',
							'FB-MOBILE-APP-INSTALLS',
						);
						foreach($campaignTypes as $type){
							$userNetworkArray['campaign_type'] = $type;
							$userNetworkArray['network_id'] = 5;
							$this->V2_map_users_network_model->create($userNetworkArray);
						}
					}


					// send rquest to quickbooks for creating customer for new user

					$this->load->model('billing_model');
					$this->billing_model->createCustomer($userInfo['user_id']);

					$userInfoId = $userInfo['user_id'];
					$userInfoCode = $userInfo['activation_code'];


					$subject = 'Account Activation';
					//$link = urlencode();
					$data = array(
						'link' => $this->config->base_url().'auth/login/'.$userInfoId.'/'.$userInfoCode

					);
					//echo $data['link'];exit;

						$message = $this->parser->parse("v2/email/account_activation.tpl", $data, true);



					//var_dump($message);echo 333333; exit;

					//$message = "ooooooooooooooooooooooooo";
				//	send_email($email, $subject, $message);

					$this->accaunt_activation_email($email, $subject, $message);





					$this->data['auth_success'] = [
						'type' => 'success',
						'msg' => 'Registration completed successfully, Please check your email to activate Your account.'
					];

				}
				else {
					$this->data['auth_success'] = ['type' => 'danger', 'msg' => 'User with this email already exist' ];

				}

			}
			else {
				$this->data['auth_success'] = ['type' => 'danger', 'msg' => 'Invalid card number' ];
			}

		}

		//var_dump(444444);exit;
        $this->_render_page('auth/signup', $this->data);
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

	public function accaunt_activation_email($email, $subject, $message){


		$config['protocol'] = 'sendmail';
		$config['mailpath'] = '/usr/sbin/sendmail';
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = TRUE;
		$config['mailtype'] = "html";
		$config['priority'] = 1;

		$this->email->initialize($config);


		$this->email->from('noreply@report-site.com', 'Report-Site No Reply');
		$this->email->to($email);

		$this->email->cc($this->CCEmail);
		$this->email->subject($subject);


		$this->email->message($message);

		$this->email->send();
	}
}
