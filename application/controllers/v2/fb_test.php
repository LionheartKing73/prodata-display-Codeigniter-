<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'/third_party/autoload.php';

use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use FacebookAds\Api;
use FacebookAds\Object\AdUser;
use FacebookAds\Object\TargetingSearch;
use FacebookAds\Object\Search\TargetingSearchTypes;
use FacebookAds\Object\Campaign;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Object\Values\AdObjectives;
use FacebookAds\Object\TargetingSpecs;
use FacebookAds\Object\Fields\TargetingSpecsFields;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Values\BillingEvents;
use FacebookAds\Object\Values\OptimizationGoals;
use FacebookAds\Object\AdImage;
use FacebookAds\Object\Fields\AdImageFields;
use FacebookAds\Object\AdCreative;
use FacebookAds\Object\ObjectStory\LinkData;
use FacebookAds\Object\Fields\ObjectStory\LinkDataFields;
use FacebookAds\Object\ObjectStorySpec;
use FacebookAds\Object\Fields\ObjectStorySpecFields;
use FacebookAds\Object\Fields\AdCreativeFields;
use FacebookAds\Object\Ad;
use FacebookAds\Object\Fields\AdFields;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Values\PageTypes;
use FacebookAds\Object\Values\InsightsPresets;
use FacebookAds\Object\Values\InsightsLevels;
use Facebook\FacebookRequest;
use Facebook\Helpers\FacebookRedirectLoginHelper;
use FacebookAds\Object\CustomAudience;
use FacebookAds\Object\Fields\CustomAudienceFields;
use FacebookAds\Object\Values\CustomAudienceSubtypes;
use FacebookAds\Object\Fields\InsightsFields;

use FacebookAds\Object\AdsPixel;
use FacebookAds\Object\Fields\AdsPixelsFields;

use FacebookAds\Object\AdAccountGroup;
use FacebookAds\Object\Fields\AdAccountGroupFields;

use Facebook\Authentication\AccessToken;

use FacebookAds\Object\ObjectStory\VideoData;
use FacebookAds\Object\Fields\ObjectStory\VideoDataFields;
use FacebookAds\Object\AdVideo;
use FacebookAds\Object\Fields\AdVideoFields;
use FacebookAds\Object\Values\CallToActionTypes;
use Facebook\FileUpload\FacebookTransferChunk;
use Facebook\FacebookClient;

class Fb_test extends CI_Controller	{

    private $fbAppId = '1674509896124897';
    private $fbAppSecret = '3840e5e1633fd3f78a4c3042af87729b';
    private $adAccountDataId = 'act_36029346';
    private $businessId = '1094106567329800';
    private $backUrl = 'http://reporting.prodata.media/v2/fb_test/test';
    private $apiVersion = 'v2.5';
    private $userAccessToken = 'CAAXy9TeICeEBADKmeXlSnevg1CUuyOqPyZBL0IHurjCLdfXmWYLqZBp2ioByGL7BKeC3tbMP5apoqiSaDZCMouTN3ZAg0ZB7DrwDkHuaU3ECZC8TC0aIiXs6Pn6cIAnBKI91G0dXmbXtJ8M3SDPRYJPG8kYHGuHVeYgV7FtezPwR18009VwAOZCyaiWnwqq3ZAcQ6ZAABvf5g1wZDZD';


    public function __construct()	{

        parent::__construct();


        $this->load->helper("url");
        $this->load->helper('cookie');
        $this->load->library('user_agent');
        $this->load->library('pagination');
        $this->load->library('form_validation');

        $this->load->model('Monitor_model');
        $this->load->model("Domains_model");
        $this->load->model("Vendor_model");
        $this->load->model("Country_model");
        $this->load->model("V2_master_campaign_model");
        $this->load->model("V2_ad_model");

        $this->load->model("Zip_model");
        $this->load->model("Report_model");
        $this->load->model("Log_model");
        $this->load->model("Finditquick_model");


        $this->load->library("parser");
        $this->load->library("session");
        $this->load->library('ion_auth');




        if ($this->ion_auth->logged_in()) {
            $user = $this->ion_auth->user()->row_array();
        }


        $this->userid = $user['id'];

        $this->viewArray['user'] = $user;
        $this->user = $user;

        if (!empty($user['is_admin'])){
            $this->viewArray['is_admin'] = true;
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
        $this->email->initialize($config);
        $this->view_file = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/' .$this->uri->segment(3);
    }


    function test() {

//        $this->load->model('Common_model');
//        $this->Common_model->test_emails();
//        exit;
//
//
//
//
//        $this->load->model('Facebook_model');
//        $this->Facebook_model->test_video();
//        exit;
//        $filename = 'uploads/tmp/SampleVideo_1080x720_5mb.mp4' ;
//        $filesize = filesize($filename);
//        $ch = curl_init();
//        $url = 'https://graph-video.facebook.com/'.$this->apiVersion.'/'.$this->adAccountDataId.'/advideos?upload_phase=start&file_size='.$filesize;
//        $query = 'upload_session_id';
//
//
//        $options = array(
//            CURLOPT_URL => $url,
//            CURLOPT_SSL_VERIFYHOST => 0,
//            CURLOPT_SSL_VERIFYPEER => 0,
//            CURLOPT_FOLLOWLOCATION => 1,
//            CURLOPT_RETURNTRANSFER => TRUE,
//            CURLOPT_POST => TRUE,
//            CURLOPT_POSTFIELDS => $query);
//
//        curl_setopt_array($ch, $options);
//
//        $response = curl_exec($ch);
//
//        if(FALSE === $response)
//        {
//            $curlErr = curl_error($ch);
//            $curlErrNum = curl_errno($ch);
//
//            curl_close($ch);
//            throw new Exception($curlErr, $curlErrNum);
//        }
//
//        curl_close($ch);
//
//        print_r($response);exit;
//
////        if($this->input->get('code')) {
////
////            $code = $this->input->get('code');
////
////            $token = $this->Facebook_model->get_access_token($code);
////
////            $this->Facebook_model->set_token($token);
////            var_dump($token);exit;
////
////        }
//
//        $firstUrl = $this->Facebook_model->get_first_url();
//        print_r(5555555555);
//
//        //redirect($firstUrl);
        exit;

    }

    public function test_array(){
        echo '<pre>';
        $emails = file('uploads/tmp/16e9e2b6d8c8a6bf5f7a74c4851cb042.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($emails as $email) {
            $new_email_array[] = trim($email);
        }
        var_dump($new_email_array);
    }

    public function test_form(){
        echo '<pre>';
        $this->load->library("facebookAd");
        $this->load->model("V2_fb_form_model");
        $form = $this->V2_fb_form_model->get_by_id(5);//var_dump($form); //exit;
        $audience = $this->facebookad->create_form($form);
    }

    public function get_form(){
        echo '<pre>';
        $this->load->library("facebookAd");
        $this->load->model("V2_fb_form_model");
        $form = $this->V2_fb_form_model->get_by_id(4);
        $form['form_network_id'] = '1785832328322502';
        $form['form_network_id'] = '167457853727233';
        $audience = $this->facebookad->get_form($form);
    }

    public function get_impressions(){
        echo '<pre>';
        $this->load->library("facebookAd");
        $ads = [['network_creative_id'=>6053141865608]];
        $audience = $this->facebookad->getAdsImpressionsByActiveCampaigns($ads);
    }
    public function get_leads_by_ad_id(){
        echo '<pre>';
        $this->load->library("facebookAd");
        $ads = [['network_creative_id'=>6053141865608]];
        $audience = $this->facebookad->get_leads_by_ad_id($ads);
    }
    public function get_leads(){
        echo '<pre>';
        //$this->load->library("facebookad");
        $this->load->model('Facebook_model');
        $this->Facebook_model->get_campaigns_leads();
    }

    public function lookalike(){
        $this->load->model("Userlist_io_model");
        $lookalike = $this->Userlist_io_model->get_lookalike_userlist_by_campaign_id(1638);
        $this->load->library("facebookad");
        $audience = $this->facebookad->create_lookalike_audience($lookalike);
        $update_data = array('remarketing_list_id'=>$audience['id'], 'sniped_code'=>htmlspecialchars($audience['code']));
        $this->Userlist_io_model->update($lookalike['id'], $update_data);
//        $this->load->model('Facebook_model');
//        $campaign['io'] = 'test';
//        $this->Facebook_model->create_lookalike_audience($campaign, $type='similarity');
    }

    public function create_custom_audience(){
//        $this->load->model("Userlist_io_model");
//        $lookalike = $this->Userlist_io_model->get_lookalike_userlist_by_campaign_id(1638);
        $this->load->library("facebookad");
        $audience = $this->facebookad->create_custom_audience($lookalike = '');
//        $update_data = array('remarketing_list_id'=>$audience['id'], 'sniped_code'=>htmlspecialchars($audience['code']));
//        $this->Userlist_io_model->update($lookalike['id'], $update_data);
//        $this->load->model('Facebook_model');
//        $campaign['io'] = 'test';
//        $this->Facebook_model->create_lookalike_audience($campaign, $type='similarity');
    }

    public function test_video(){

        $this->load->model('V2_log_model');
//echo 'start';
        var_dump($_REQUEST, $_POST, $_GET, $_FILES, $_SERVER);

        $post = serialize($_REQUEST);

        $this->V2_log_model->create(1, $post, 'post'); echo 'tr';
        if($this->input->post()) {
            echo 'post1';



        }

        return true;
    }

    function tesddddt() {
		die(555);
		mail('hovhannes.zhamharyan.bw@gmail.com', 'My Subject', 'lorem ipsum lore ipsum');die;


        $this->load->model('Facebook_model');

        if($this->input->get('code')) {

            $code = $this->input->get('code');

            $token = $this->Facebook_model->get_access_token($code);
            $this->Facebook_model->set_token($token);

            $imgUrl = 'C:\xampp\htdocs\adword\uploads\tmp\0b3977a848e528cc7b86c8c4941f3684.jpg';
            $campaign = '';
            $adSetId = '';
            $creativeId = '';

            $campaignId = $this->Facebook_model->create_campaign($campaign);


            $this->Facebook_model->create_targeting();

            $adSetId = $this->Facebook_model->create_adSet($campaignId);
//
//            echo '<br>';
//            echo 'camp-'.$campaignId;
//            echo '<br>';
//            echo 'adsetId-'.$adSetId.'<br>';


            $adId = $this->Facebook_model->create_ad($adSetId);
            var_dump($adId);die;



        }

        $firstUrl = $this->Facebook_model->get_first_url();
        redirect($firstUrl);

    }

    function create_ad() {

        $this->load->model('Facebook_model');
        $test = $this->Facebook_model->get_created_data();
        exit;
        $campaign = '';

        $campaignId = $this->Facebook_model->create_campaign($campaign);

        $this->Facebook_model->create_targeting();

        $adSetId = $this->Facebook_model->create_adSet($campaignId);

        $adId = $this->Facebook_model->create_ad($adSetId);
        var_dump($adId);die;

    }

    public function check_emails(){

        $this->load->model('Common_model');

        $this->Common_model->test_emails();

    }

    public function claim_page()
    {
//        curl \
//        -F "page_id=<PAGE_ID>" \
//        -F "access_type=OWNER" \
//        -F "access_token=<ACCESS_TOKEN>" \
//        "https://graph.facebook.com/<API_VERSION>/<BUSINESS_ID>/pages"

        $data = "page_id=1027880993955022&access_type=OWNER&access_token=$this->userAccessToken";

        $url = "https://graph.facebook.com/v2.6/$this->businessId/pages";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);


//        try{
//            $result = curl_exec($ch);
//        } catch (Exception $e) {
//            echo 'Caught exception: ',  $e->getMessage(), "\n";
//        }
        $result = curl_exec($ch);
        $json = json_decode($result, true);
        var_dump($json);
        curl_close($ch);
    }

    public function page_access()
    {
//        curl \
//        -F "page_id=<PAGE_ID>" \
//        -F "access_type=AGENCY" \
//        -F "permitted_roles=['ADVERTISER','INSIGHTS_ANALYST']" \
//        "https://graph.facebook.com/<API_VERSION>/<BUSINESS_ID>/pages?access_token=<ACCESS_TOKEN>" \

        $data = "page_id=1027880993955022&access_type=AGENCY&permitted_roles=['ADVERTISER']";
        $url = "https://graph.facebook.com/v2.6/$this->businessId/pages?access_token=$this->userAccessToken";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $result = curl_exec($ch);
        $json = json_decode($result, true);
        var_dump($json);
        curl_close($ch);

    }
}
