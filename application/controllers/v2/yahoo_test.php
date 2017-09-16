<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'third_party/ffmpeg-php-master/FFmpegAutoloader.php';
class yahoo_test extends CI_Controller
{

    private $viewArray = array();
    public $campaign_name = "testing";
    private $admin = "harutyun.sardaryan.bw@gmail.com";


    function __construct()
    {
        parent::__construct();

        //load our new Adwords library
        //$this->load->library('parser');
        //$this->load->library('MY_Parser');
        //$this->load->library('ion_auth');

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library('email');
        $this->load->model('yahoo_model');
        $this->load->model('V2_network_country_criterion_model');
        $this->load->model('V2_network_state_criterion_model');
        //$this->load->library('yahoo');

        //$this->uploadedPath = $this->config->base_url() . 'uploads/';
        //$this->viewArray['current_url'] = current_url();
        $this->viewArray['base_url'] = base_url();
        $this->viewArray['site_url'] = site_url();

    }

    public function get_image(){
//        var_dump(ceil(42.1), floor (42.9),round(1.754, 2)); exit;
//        echo '<A HREF="%%CLICK_URL_ESC_ESC%%https://ad.doubleclick.net/jump/N5700.1056085.3PCERTTESTING.COMS/B6823230.5;sz=300x250;ord=%%CACHEBUSTER%%?" target=”_blank”>
//            <IMG SRC="https://ad.doubleclick.net/ad/N5700.1056085.3PCERTTESTING.COMS/B6823230.5;sz=300x250;ord=%%CACHEBUSTER%%?" BORDER=0 WIDTH=300 HEIGHT=250 ALT="Advertisement"></A>'; exit;
        $url = 'uploads/permanent/0ca1c03d251e1bcdcf70c3e5cb36ffd9.mp4'; //var_dump(555);
        $movie = new FFmpegMovie($url);
        $height = $movie->getFrameHeight();
        $width = $movie->getFrameWidth();
        //var_dump($width, $height, $width/$height); exit;
        if($width>=1200 && $height>=627) {
            if(round($width/$height, 2) <= 1.95 && round($width/$height, 2) >= 1.90) {

            } else if(round($width/$height, 2) < 1.90) {
                $new_height = round($width/1.9);
                $height_crop_pixels = $height-$new_height;
                $crop_from_top = floor($height_crop_pixels/2);
                $crop_from_bottom = ceil($height_crop_pixels/2);
                $frame = $movie->getFrame(3);

                $frame->crop($crop_from_top, $crop_from_bottom);
                $image = $frame->toGDImage();
//                header('Content-Type: image/jpeg');
//                imagejpeg($image, NULL); exit;
            }
        }
        $width_crop_pixels = $width-$height;
        $crop_from_left = floor($width_crop_pixels/2);
        $crop_from_right = ceil($width_crop_pixels/2);
        $frame = $movie->getFrame(3);
        $frame->crop(null, null, $crop_from_left, $crop_from_right);
//        $frame = $movie->getFrame(3);
//        $frame->crop(100,100,100,100);
        //var_dump($frame->getWidth()); exit;
        $image = $frame->toGDImage();
        header('Content-Type: image/jpeg');
        imagejpeg($image, NULL); exit;
        echo '<img src="'.$image.'">';
        var_dump('<pre>',$movie->getPixelFormat());
    }

    public function create_ad() {

        // select from db all ads by campaign_id
//        $ad = $this->CI->V2_ad_model->get_by_id($ad_id);
//        if(!$ad){
//            $response['message'] = 'No find ad by id '.$ad_id;
//            return $response;
//        }
        //add ads into adwords
//        if($ad['creative_type'] == $this->text_ad_type){
//            $response = $this->yahoo->create_text_ad($ad);
//        } else {
            $ad_array['campaignId'] = 352576060;
            $ad_array['adGroupId'] = 9068387579;
            $ad_array["advertiserId"] = 1493170;

        $ad_array['adName'] =  'first 2';
        $ad_array['title'] = 'title for you';
        $ad_array['title2'] = '';
        $ad_array['sponsoredBy'] = 'harut';
        $ad_array['description'] = 'description 1';
        $ad_array['displayUrl'] = 'www.palmbeachweightloss.com';
        $ad_array['landingUrl'] = 'http://www.palmbeachweightloss.com';
        $ad_array['status'] = 'ACTIVE';
//        $ad_array['imageUrlHQ'] = 'http://reporting.prodata.media/uploads/permanent/1b0de8cb35defd189898e2b08de0e3e6.png';
        $ad_array['imageUrlHQ'] = 'http://www.prodataretargeting.com/uploads/permanent/925d912952cb8301910de8b40a2c7c39.jpg';
        $ad_array['imageUrlHQ'] = 'https://i.warosu.org/data/tg/img/0352/55/1412276410342.png';
        $ad_array['imageUrl'] = 'https://s.yimg.com/av/moneyball/ads/1476976941438-5373.jpg';
        $ad_array['videoPrimaryUrl'] = 'http://reporting.prodata.media/uploads/permanent/0ca1c03d251e1bcdcf70c3e5cb36ffd9.mp4';

//            $ad_array['adName'] =  $ad['name'];
//            $ad_array['title'] = $ad['title'];
//            $ad_array['title2'] = '';
//            $ad_array['sponsoredBy'] = $this->advertiser_name;
//            $ad_array['description'] = $ad['description_1'];
//            $ad_array['displayUrl'] = $ad['display_url'];
//            $ad_array['landingUrl'] = $ad['original_url'];
//            $ad_array['status'] = 'ACTIVE';
//            $ad_array['imageUrlHQ'] = $ad['creative_url'];
            //$ad_array['imageUrlHQ'] = $ad['creative_url'];

            $ad_json = json_encode($ad_array);

            $response = $this->yahoo->create_ad($ad_json); var_dump($response); exit;
    }
    //}

    public function hi() {
        $this->yahoo->test();
    }
    public function get_group($group_id) {
        $result = $this->yahoo->get_group($group_id);
        echo '<pre>';
        var_dump($result); exit;
    }

    public function get_targeting() {
        $this->yahoo->get_targeting();
    }

    public function get_audience() {
        $this->yahoo->get_audience(50086437);
    }

    public function get_ads() {

        $this->yahoo_model->check_ads_approved_status();
    }
    public function update_status() {
        $this->load->model("V2_master_campaign_model");
        $campaign =  $this->V2_master_campaign_model->get_by_id(null,1462); //var_dump($campaign); exit;
        $this->yahoo_model->update_campaign_status($campaign);
    }
    public function get_ads_impressions() {

        $this->yahoo_model->get_ads_impressions1();
    }
    public function get_cost() {

        $this->yahoo_model->get_campaigns_cost();
    }
    public function create_audience() {

        $this->yahoo_model->create_audience(['io'=>'withtag', 'name'=>'second', 'id'=>'1', 'userid'=>'1']);
    }
    public function get_demographics_report() {
        $this->yahoo_model->get_demographics_report();
    }
    public function get_jobs() {

        $this->yahoo_model->get_job();
    }

    public function country(){

        $result = $this->yahoo->get_country();

        $countrys = json_decode($result, true);
        var_dump(count($countrys['response']));

        foreach($countrys['response'] as $country) {

            $c = $this->V2_network_country_criterion_model->get_country($country['name'],1); //var_dump($c); exit;
            if($c) {  //var_dump($c); exit;
                $data['country_name'] = $country['name'];
                $data['country_code'] = $c[0]['country_code'];
                $data['network_id'] = 6;
                $data['criterion_id'] = $country['woeid'];
                $this->V2_network_country_criterion_model->create($data);
            }
        }
        exit;
        //$this->V2_network_country_criterion_model->create($data);
    }

    public function carrier(){



        $countrys = $this->V2_network_country_criterion_model->get_all_country_by_network_id(4);
        //var_dump($countrys); exit;
        foreach($countrys as $country) {
            if($country['criterion_id']==35 || $country['criterion_id']==1) {
                $result = $this->airpush->get_carrier($country['criterion_id']);
                $carriers = json_decode($result);
                $carriers = json_decode($carriers, true);
                $carriers = reset($carriers);
                foreach ($carriers as $code=>$state) {
                    //var_dump($code, $state); exit;

                    $data['carrier'] = $state;
                    $data['country_code'] = $country['country_code'];
//                        $data['state_code'] = $s[0]['state_code'];
                    $data['network_id'] = 4;
//                        $data['type'] = $s[0]['type'];
                    $data['criterion_id'] = $code;

                    $this->db->insert('v2_network_carrier_criterion', $data);

                }
            }
        }
    }

    public function state(){
        var_dump(json_decode($this->yahoo->get_states(23424977), true)); exit;
        $countrys = $this->V2_network_country_criterion_model->get_all_country_by_network_id(4);
        //var_dump($countrys); exit;
        foreach($countrys as $country) {
            if($country['criterion_id']==35 || $country['criterion_id']==1) {
                $result = $this->airpush->get_states($country['criterion_id']);
                $states = json_decode($result);
                $states = json_decode($states, true); //var_dump(); exit;
                $states = reset($states);
                foreach ($states as $code=>$state) {
                    //var_dump($code, $state); exit;
                    $s = $this->V2_network_state_criterion_model->get_state($state,1);
                    if ($s) {  //var_dump($c); exit;
                        $data['state_name'] = $state;
                        $data['country_code'] = $country['country_code'];
                        $data['state_code'] = $s[0]['state_code'];
                        $data['network_id'] = 4;
                        $data['type'] = $s[0]['type'];
                        $data['canonical_name'] = $s[0]['canonical_name'];
                        $data['criterion_id'] = $code;
                        $this->V2_network_state_criterion_model->create($data);
                    }
                }
            }

        }
        //$this->V2_network_country_criterion_model->create($data);
    }

    public function get_token()
    {
        // code M91b784f0-efb4-2b82-0da1-bed8da14aae4
        // red_url http://reporting.prodata.media/v2/bingCrons/getToken
        // client id 000000004417AE58
        // secet f6A3bgcI77xO3ID4iO58sYIWsbdB7llp
        //$refresh_tocken = $this->input->get('refresh_tocken');
        var_dump($this->input->get()); exit;
//        $accessTokenExchangeUrl = "https://login.live.com/oauth20_token.srf";
//        $accessTokenExchangeParams = array(
//            'client_id' => $this->client_id,
//            //'client_secret' => $this->client_secret,
//            'grant_type' => 'refresh_token',
//            'refresh_token' => $refresh_tocken,
//            //'code' => $code,
//            'redirect_uri' => 'http://reporting.prodata.media/v2/bingCrons/get_token'
//        );
//        $json = $this->post_data('https://login.live.com/oauth20_token.srf',$accessTokenExchangeParams);
//        $responseArray = json_decode($json, TRUE);
//        var_dump($responseArray); exit;

    }
public function rest() {

    $jsonDataEncoded = '{
    "campaign_info": {
       "name": "xyz_0025",
       "dailyBudget": 100,
       "type": "text",
       "startDate": "2016-06-15 10:30",
       "endDate": " ",
       "category": "Automotive",
       "platformType": "Android"
   }
 }';


    //API Url
    $url = 'http://openapi.airpush.com/createCampaign?apikey=eb90bf1b21ebb284931bee05&campaigns='.$jsonDataEncoded;

    //Initiate cURL.
//    $ch = curl_init($url);

    //The JSON data.
//    $jsonData = array(
//    'username' => 'MyUsername',
//    'password' => 'MyPassword'
//    );

    //'http://openapi.airpush.com/createCampaign?apikey=eb90bf1b21ebb284931bee05&campaigns={%22campaignId%22:%22115%22,%22bid%22:%225%22,%22dailyBudget%22:%2220%22,%22startDate%22:%222013-08-20%2015:45%22,%22endDate%22:%222013-08-25%2010:15%22,%22osVersion%22:%221.6%22,%22dayOfWeek%22:%22Mon,Tue,Wed%22,%22scheduleTime%22:{%22Mon%22:%223-4,5-9%22,%22Tue%22:%221-4,6-12%22,%22Wed%22:%220-3,4-10,19-24%22},%22countryId%22:%2292,2,9,13%22,%22stateId%22:{%225%22:%224,5,6,11%22,%229%22:%227,8,9%22,%2292%22:%221,2,3%22},%22domain_name%22:%22www.yourdomain.com%22,%22manufacturerId%22:%2213,12%22,%22deviceId%22:{%2212%22:%224,5%22,%2213%22:%221,2,3%22},%22carrierId%22:{%225%22:%2214,15%22,%229%22:%2216,17%22,%2292%22:%2212,13%22}}'
    //{"campaignId":"115","bid":"5","dailyBudget":"20","startDate":"2016-08-20%2015:45","endDate":"2016-08-25%2010:15","osVersion":"1.6","dayOfWeek":"Mon,Tue,Wed","scheduleTime":{"Mon":"3-4,5-9","Tue":"1-4,6-12","Wed":"0-3,4-10,19-24"},"countryId":"92,2,9,13","stateId":{"5":"4,5,6,11","9":"7,8,9","92":"1,2,3"},"domain_name":"www.yourdomain.com","manufacturerId":"13,12","deviceId":{"12":"4,5","13":"1,2,3"},"carrierId":{"5":"14,15","9":"16,17","92":"12,13"}}
    //Encode the array into JSON.
    //$jsonDataEncoded1 = json_encode($jsonData);
    //var_dump($jsonDataEncoded1);
//    $jsonDataEncoded = '{
//   "campaignId":"115",
//   "bid":"5",
//   "dailyBudget":"20",
//   "startDate":"2013-08-20 15:45",
//   "endDate":"2013-08-25 10:15",
//   "osVersion":"1.6",
//   "dayOfWeek":"Mon,Tue,Wed",
//   "scheduleTime":{
//           "Mon":"3-4,5-9",
//           "Tue":"1-4,6-12",
//           "Wed":"0-3,4-10,19-24"
//       },
//   "countryId":"92,2,9,13",
//   "stateId":{
//       "5":"4,5,6,11",
//       "9":"7,8,9",
//       "92":"1,2,3"
//   },
//   "domain_name":"www.yourdomain.com",
//   "manufacturerId":"13,12",
//   "deviceId":{
//       "12":"4,5",
//       "13":"1,2,3"
//   },
//   "carrierId":{
//       "5":"14,15",
//       "9":"16,17",
//       "92":"12,13"
//}}';

    //var_dump($jsonDataEncoded);

    //Tell cURL that we want to send a POST request.
    //curl_setopt($ch, CURLOPT_POST, 1);

    //Attach our encoded JSON string to the POST fields.
    //curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);

    //Set the content type to application/json
    //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    //var_dump($url);
//    var_dump(file_get_contens($url)); exit;

//    curl_setopt($ch, CURLOPT_URL, $url);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, CURLOPT_HEADER, 1);

    //Execute the request
//    $result = curl_exec($ch);
//    var_dump($result);
    var_dump($url);
    // Get cURL resource
    $curl = curl_init();
// Set some options - we are passing in a useragent too here
//    curl_setopt_array($curl, array(
//        CURLOPT_RETURNTRANSFER => 1,
//        CURLOPT_URL => $url,
//        //CURLOPT_USERAGENT => 'Codular Sample cURL Request'
//    ));
    $url = 'http://openapi.airpush.com/createCampaign?apikey=eb90bf1b21ebb284931bee05&campaigns={%20%22campaign_info%22:%20{%20%22name%22:%20%22xyz_0025%22,%20%22dailyBudget%22:%20100,%20%22type%22:%20%22in_app%22,%20%22startDate%22:%20%222016-06-15%2010:30%22,%20%22endDate%22:%20%22%20%22,%20%22category%22:%20%22Automotive%22,%20%22platformType%22:%20%22Android%22%20}%20}';
    curl_setopt($curl, CURLOPT_URL, $url);

    //return the transfer as a string
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
// Send the request & save response to $resp
    $resp = curl_exec($curl); var_dump($resp);
// Close request to clear up some resources
    curl_close($curl);


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
        $user = new Bing();
        $result = $user->create_campaign(1);
        //$result["destination_url"] = $destination_url;

        return $result;
    }

    public function testing()
    {   //var_dump(777); exit;
        $user = new Bing();
        $result = $user->test();
        //$result["destination_url"] = $destination_url;

        return $result;
    }

    public function getToken()
    {
       // code M91b784f0-efb4-2b82-0da1-bed8da14aae4
    // red_url http://reporting.prodata.media/v2/bingCrons/getToken
        // client id 000000004417AE58
        // secet f6A3bgcI77xO3ID4iO58sYIWsbdB7llp
        $code = $this->input->get('code');
        var_dump($this->input->get()); exit;
        $accessTokenExchangeUrl = "https://login.live.com/oauth20_token.srf";
        $accessTokenExchangeParams = array(
            'client_id' => '000000004417AE58',
            'client_secret' => 'f6A3bgcI77xO3ID4iO58sYIWsbdB7llp',
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => 'http://reporting.prodata.media/v2/bingCrons/getToken'
        );
        $json = $this->postData('https://login.live.com/oauth20_token.srf',$accessTokenExchangeParams);
        $responseArray = json_decode($json, TRUE);
        var_dump($responseArray); exit;

    }

    public function postData($url, $postData) {
        $ch = curl_init();

        $query = "";

        while(list($key, $val) = each($postData))
        {
            if(strlen($query) > 0)
            {
                $query = $query . '&';
            }

            $query = $query . $key . '=' . $val;
        }

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $query);

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);

        if(FALSE === $response)
        {
            $curlErr = curl_error($ch);
            $curlErrNum = curl_errno($ch);

            curl_close($ch);
            throw new Exception($curlErr, $curlErrNum);
        }

        curl_close($ch);

        return $response;
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
        $result = $user->createLocationCriteria($user, 325093345, 21137);
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




