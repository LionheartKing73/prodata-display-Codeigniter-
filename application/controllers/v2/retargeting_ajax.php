<?php
class Retargeting_ajax extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *, User_id, Campaign_id');


        $this->load->helper("url");
        $this->load->helper('cookie');

        $this->load->model("Userlist_io_model");

    }

    public function index()
    {
        $headers = getallheaders();
        $user_id = $headers['User_id'];
        $origin = $headers['Origin'];

        $origin = trim($origin, '/');
        $urlParts = parse_url($origin);

        $origin_domain = preg_replace('/^www\./', '', $urlParts['host']);

//        echo '<pre>'; print_r($origin_domain); die;

        if ($headers['Campaign_id']) {
            $campaign_id = $headers['Campaign_id'];
        }

        if ($user_id) {
            $snippets = $this->Userlist_io_model->get_snippet_code($user_id, $campaign_id, $origin_domain);
        }

//        echo '<pre>'; print_r($snippets); die;
        foreach ($snippets as $snippet)
        {
            echo $snippet['sniped_code'];
        }
    }

}