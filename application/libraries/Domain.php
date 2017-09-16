<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Domain {

    private $default = 'http://reporting.prodata.media';
    // private $default = 'http://prodata.loc';
    private $assets = [
        'logo' => 'logo-main.png',
        'domain' => '',
        'company_name' => 'ProData Media',
        'company_email' => '',
        'background_color' => '#084D8E',
        'footer_color' => '#7FBE27',
        'active_button_color' => '#5CB85C',
        'passive_button_color' => '#D0D0D0',
        'content_background_color' => '#F6F6F6',
        'content_text_color' => '#2C3E50',
        'block_header_text_color' => '#FFFFFF',
        'block_header_icon_color' => '#FFA500',
        'block_header_background_color' => '#D3D3D3',
        'block_content_text_color' => '#FFA500',
    ];

    private function current() {
        $proto = explode('/', $_SERVER['SERVER_PROTOCOL']);
        return strtolower($proto[0]).'://'.$_SERVER['HTTP_HOST'];
    }

    function __construct() {

        $this->CI =& get_instance();
        $this->CI->load->model('V2_domains_model');
        $this->CI->load->library('ion_auth');
        if ($this->CI->ion_auth->logged_in() && $this->current() != $this->default) {
            $this->user = $this->CI->ion_auth->user()->row_array();
            if($this->user['is_branding'] != 'Y') {
                $this->CI->ion_auth->logout();
                redirect($this->default);
            }
        }
        $this->CI->load->library('session');

    }

    private function makeDefault($arg = null) {
        if($arg) {
            $this->assets = array_merge($this->assets, $arg);
        }
        $this->assets['company_name'] = $this->user['company'];
        $this->assets['domain'] = $this->default;
        $this->CI->session->set_userdata('assets', $this->assets);
    }

    private function check($doms) {
        if($this->current() == $this->default) {
            $this->makeDefault();
        } else {
            $doms = $this->CI->V2_domains_model->get_domain_list();
            foreach ($doms as $key => $value) {
                $val = str_replace(['/', 'http:', 'https:'],'',$value['domain']);
                $base = str_replace(['/', 'http:', 'https:'],'',$this->current());
                if ($val == $base) {
                    $this->CI->session->set_userdata('assets',$value);
                }
            }
            if(!isset($this->CI->session->userdata['assets']) || $this->CI->session->userdata['assets']['domain'] != $this->current()) {
               $this->makeDefault();
               redirect($this->default);
           }
        }
    }

    public function filterDom() {
        if(!isset($this->CI->session->userdata['assets']['domain']) || $this->CI->session->userdata['assets']['domain'] != $this->current()) {
            $this->check();
        }
    }

}
