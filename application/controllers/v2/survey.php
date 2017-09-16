<?php
class Survey extends CI_Controller
{
    
    private $viewArray = array();
    
    public function __construct()   {
        parent::__construct();

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *, User_id, Campaign_id');

        $this->load->helper("url");
        $this->load->helper('cookie');
        $this->load->model("V2_rich_media_survey_model");
        $this->load->model("V2_ad_model");
        $this->load->library('parser');
    }

    public function save() {
        if ($this->input->post()) {
            // this is a post
            
            $this->V2_rich_media_survey_model->campaign_id = $this->input->post("campaign_id");
            $this->V2_rich_media_survey_model->ip_address = $this->input->ip_address();
            $this->V2_rich_media_survey_model->answer = $this->input->post("selected_answer");
            $this->V2_rich_media_survey_model->set_survey_results();
            
            $data = $this->V2_rich_media_survey_model->get_survey_results();

            print json_encode(array("status" => "SUCCESS", "data" => $data['answer_result'], "total_count" => $data['total_count'], "destination_url" => $data['destination_url']));
        } else {
            // this is something else and we're rejceting it.
            print json_encode(array("status" => "ERROR", "message" => "POST required"));
        }
        
        exit;
    }
    
    public function index() {
        print "INVALID ACCESS";
        exit;
    }
    
    public function loader($id = 0)  {
        if (! $id > 0) {
            print "INVALID AD";
        }

        $ad = $this->V2_ad_model->get_by_id($id);

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
        
        print $this->parser->parse("v2/ads/rich_media_survey.php", $this->viewArray);
        exit;
    }
}