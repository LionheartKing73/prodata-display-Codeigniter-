<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

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
use FacebookAds\Object\Values\CampaignObjectiveValues;;
// use FacebookAds\Object\Values\AdObjectives;
// use FacebookAds\Object\TargetingSpecs;
// use FacebookAds\Object\Fields\TargetingSpecsFields;
use FacebookAds\Object\Targeting;
use FacebookAds\Object\Fields\TargetingFields;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Values\BillingEvents;
use FacebookAds\Object\Values\OptimizationGoals;
use FacebookAds\Object\AdImage;
use FacebookAds\Object\Fields\AdImageFields;
use FacebookAds\Object\AdCreative;
use FacebookAds\Object\LeadgenForm;
use FacebookAds\Object\ObjectStory\LinkData;
use FacebookAds\Object\Fields\ObjectStory\LinkDataFields;
use FacebookAds\Object\ObjectStorySpec;
use FacebookAds\Object\Fields\AdCreativeObjectStorySpecFields;
use FacebookAds\Object\Fields\AdCreativeFields;
use FacebookAds\Object\Ad;
use FacebookAds\Object\Fields\AdFields;
use FacebookAds\Object\Values\AdSetBillingEventValues;
use FacebookAds\Object\Values\AdSetOptimizationGoalValues;
use FacebookAds\Object\Values\AdCreativeCallToActionTypeValues;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Values\PageTypes;
use FacebookAds\Object\Values\InsightsPresets;
use FacebookAds\Object\Values\InsightsLevels;
use Facebook\FacebookRequest;
use Facebook\Helpers\FacebookRedirectLoginHelper;
use FacebookAds\Object\CustomAudience;
use FacebookAds\Object\Fields\CustomAudienceFields;
use FacebookAds\Object\Values\CustomAudienceSubtypes;
use FacebookAds\Object\Values\CustomAudienceTypes;
use FacebookAds\Object\Fields\CustomAudienceMultikeySchemaFields;
use FacebookAds\Object\CustomAudienceMultikey;
use FacebookAds\Object\Fields\InsightsFields;

use FacebookAds\Object\Fields\AdCreativeLinkDataFields;
//use FacebookAds\Object\Fields\AdCreativeObjectStorySpecFields;
use FacebookAds\Object\Fields\AdCreativeLinkDataChildAttachmentFields;
use FacebookAds\Object\AdCreativeLinkDataChildAttachment;
use FacebookAds\Object\AdCreativeLinkData;
use FacebookAds\Object\AdCreativeObjectStorySpec;

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

include APPPATH.'libraries/IniWriter.php';


class FacebookAd
{

    private $fbAppId = '1674509896124897';
    private $fbAppSecret = '3840e5e1633fd3f78a4c3042af87729b';
    private $adAccountDataId = '';
    private $backUrl = 'http://reporting.prodata.media/v2/fb_test/test';
    private $apiVersion = 'v2.8';
   // private $userAccessToken = 'CAAXy9TeICeEBADKmeXlSnevg1CUuyOqPyZBL0IHurjCLdfXmWYLqZBp2ioByGL7BKeC3tbMP5apoqiSaDZCMouTN3ZAg0ZB7DrwDkHuaU3ECZC8TC0aIiXs6Pn6cIAnBKI91G0dXmbXtJ8M3SDPRYJPG8kYHGuHVeYgV7FtezPwR18009VwAOZCyaiWnwqq3ZAcQ6ZAABvf5g1wZDZD';
    private $imgHash = '';
    private $targeting = '';
    private $amount = 100;
    private $pixel_id = '700571266746502';
    private $fb_page_id = '840687409373475';

    public function __construct()
    {
        session_start();

        $fb = new Facebook([
            'app_id' => $this->fbAppId,
            'app_secret' => $this->fbAppSecret,
        ]);

        $this->set_access_taken();

    }

    /*
    * Refresh access token by using expired access token and store in db as well change expiration time too
    *
    * @params integer $socialAccountId tassleem user social account id
    * @params string $oldAccessToken expired access token
    * @return string new access token
    */
    public function get_access_token()
    {
        $auth = parse_ini_file(APPPATH . 'third_party/facebook/auth.ini', true);
        $this->userAccessToken = $auth["OAUTH2"]['access_token'];
        //var_dump($this->userAccessToken); exit;
        $codeUrl = FacebookClient::BASE_GRAPH_URL.'/oauth/client_code?access_token='.$this->userAccessToken.'&client_id='.$this->fbAppId.'&client_secret='.$this->fbAppSecret.'&redirect_uri='.$this->backUrl;
        //var_dump($codeUrl); //exit;
        $response = file_get_contents($codeUrl);

        $codeObj = json_decode($response); //var_dump($response); exit;
        $code = $codeObj->code; //var_dump($code); exit;
        $accessUrl = "https://graph.facebook.com/$this->apiVersion/oauth/access_token?client_id=$this->fbAppId&redirect_uri=$this->backUrl&client_secret=$this->fbAppSecret&code=$code";

        $result = json_decode(file_get_contents($accessUrl));

        return $result;

    }

    public function set_access_taken()
    {
        $auth = parse_ini_file(APPPATH . 'third_party/facebook/auth.ini', true);

        if(time() > ($auth["OAUTH2"]['refresh_time']-10)) {;
            $token = $this->get_access_token();
            if(!$token->access_token){ return;}
            $writer = new IniWriter();
            $auth["OAUTH2"]['access_token'] = $token->access_token;
            $auth["OAUTH2"]['refresh_time'] = time() + 5018394;

            $result = $writer->writeToFile(APPPATH . 'third_party/facebook/auth.ini', $auth);
        }


        $this->userAccessToken = $auth["OAUTH2"]['access_token'];

        //var_dump($this->userAccessToken); exit;
        Api::init($this->fbAppId, $this->fbAppSecret, $this->userAccessToken);

        $api = Api::instance();
        $me = new AdUser('me');


        $adAccount = $me->getAdAccounts()->current();
        $adAccountData = $adAccount->getData();
        $this->adAccountDataId = $adAccountData['id'];

    }

    public function create_campaign($campaign_array)
    {
        $campaign = new Campaign(null, $this->adAccountDataId);
        if($campaign_array['campaign_type'] == 'FB-PAGE-LIKE'){

            $campaign->setData(array(
                CampaignFields::NAME => $campaign_array['io'] . ' ' . $campaign_array['name'] . ' ' . $campaign_array['id'],
                CampaignFields::OBJECTIVE => CampaignObjectiveValues::PAGE_LIKES,
            ));

        } else if($campaign_array['campaign_type'] == 'FB-VIDEO-VIEWS') {
            $campaign->setData(array(
                CampaignFields::NAME => $campaign_array['io'] . ' ' . $campaign_array['name'] . ' ' . $campaign_array['id'],
                CampaignFields::OBJECTIVE => CampaignObjectiveValues::VIDEO_VIEWS,
            ));

        } else if($campaign_array['campaign_type'] == 'FB-LOCAL-AWARENESS') {
            $campaign->setData(array(
                CampaignFields::NAME => $campaign_array['io'] . ' ' . $campaign_array['name'] . ' ' . $campaign_array['id'],
                CampaignFields::OBJECTIVE => CampaignObjectiveValues::LOCAL_AWARENESS,
            ));
        } else if($campaign_array['campaign_type'] == 'FB-PROMOTE-EVENT') {
            $campaign->setData(array(
                CampaignFields::NAME => $campaign_array['io'] . ' ' . $campaign_array['name'] . ' ' . $campaign_array['id'],
                CampaignFields::OBJECTIVE => CampaignObjectiveValues::EVENT_RESPONSES,
            ));
        } else if($campaign_array['campaign_type'] == 'FB-LEAD') {
            $campaign->setData(array(
                CampaignFields::NAME => $campaign_array['io'] . ' ' . $campaign_array['name'] . ' ' . $campaign_array['id'],
                CampaignFields::OBJECTIVE => CampaignObjectiveValues::LEAD_GENERATION,
                //CampaignFields::BUYING_TYPE => 'AUCTION',
            ));
        } else {
            $campaign->setData(array(
                CampaignFields::NAME => $campaign_array['io'] . ' ' . $campaign_array['name'] . ' ' . $campaign_array['id'],
                CampaignFields::OBJECTIVE => CampaignObjectiveValues::LINK_CLICKS,
                //check for type
            ));
        }

        $response = array('message' => '', 'result' => '');
        try {
            $campaign->create(array(
                Campaign::STATUS_PARAM_NAME => Campaign::STATUS_PAUSED,
            ));
        } catch (Exception $e) {
            $response['message'] = $e->getErrorUserMessage();
            return $response;
        }

        $response['result'] = $campaign->getData()['id'];

        return $response;

    }

    public function array_for_interests($campaign_array)
    {
        $campaign_array = explode('-', $campaign_array['vertical']);
        // $array_for_interests = array('adult' => 6003342807051, 'autointender' => 6003169889717, 'automotive' => 6003304550260, 'autoowner' => 6003169889717,
        //     'b2b' => 6003089503592, 'beauty' => 6002867432822, 'business' => 6003402305839, 'consumer' => 6003174913249, 'education' => 6003327060545,
        //     'entertainment' => 6003349442621, 'family' => 6003476182657, 'finance' => 6003130044117, 'fitness' => 6003067198997, 'food' => 6003266061909,
        //     'gender' => 6002893875879, 'GENERAL' => 6003114849468, 'health' => 6003464109203, 'home' => 6003418314031, 'law' => 6003703762913,
        //     'medical' => 6003472127663, 'misc' => 6003194386247, 'music' => 6003020834693, 'parents' => 6003516132642, 'pets' => 6004037726009,
        //     'sports' => 6003269553527, 'travel' => 6004160395895);

            $interests_names = array(
                'IAB1'  => array('id' => 6003349442621, 'name' => 'entertainment'),
                'IAB2'  => array('id' => 6003304550260, 'name' => 'automotive'),
                'IAB3'  => array('id' => 6003402305839, 'name' => 'business'),
                'IAB4'  => array('id' => 6003270811593, 'name' => 'higher education'),//careers
                'IAB5'  => array('id' => 6003327060545, 'name' => 'education'),
                'IAB6'  => array('id' => 6003476182657, 'name' => 'family'),
                'IAB7'  => array('id' => 6003067198997, 'name' => 'fitness'),
                'IAB8'  => array('id' => 6003266061909, 'name' => 'food'),
                'IAB9'  => array('id' => 6012547807252, 'name' => 'hobbies'),
                'IAB10' => array('id' => 6003418314031, 'name' => 'home'),
                'IAB11' => array('id' => 6003703762913, 'name' => 'law'),
                'IAB12' => array('id' => 6004043913548, 'name' => 'news'),
                'IAB13' => array('id' => 6003130044117, 'name' => 'finance'),
                'IAB14' => array('id' => 6003389760112, 'name' => 'social media marketing'),//society
                'IAB15' => array('id' => 6002866718622, 'name' => 'science'),
                'IAB16' => array('id' => 6004037726009, 'name' => 'pets'),
                'IAB17' => array('id' => 6003269553527, 'name' => 'sports'),
                'IAB18' => array('id' => 6002867432822, 'name' => 'beauty'),
                'IAB19' => array('id' => 6003985771306, 'name' => 'technology'),
                'IAB20' => array('id' => 6004160395895, 'name' => 'travel'),
                'IAB21' => array('id' => 6003578086487, 'name' => 'realestate'),
                'IAB22' => array('id' => 6003167425934, 'name' => 'shopping'),
                'IAB23' => array('id' => 6003395353671, 'name' => 'religion'),
                'IAB24' => array('id' => 6002991736368, 'name' => 'Reading'),//uncategorized
                'IAB25' => array('id' => 6003342807051,'adult'),
                'IAB26' => array('id' => 6003268182136,'tv reality shows'),//Illegal Content
            );
        $interest_id_name = array($interests_names[$campaign_array[0]]);
        return $interest_id_name;
    }

    public function get_interests()
    {
        $result = TargetingSearch::search(
            TargetingSearchTypes::TARGETING_CATEGORY,
            'interests');
        $interests = $result->getResponse()->getBody();
        $interests = json_decode($interests, true);
        return $interests['data'];
    }

    public function get_behaviors()
    {
        $result = TargetingSearch::search(
            TargetingSearchTypes::TARGETING_CATEGORY,
            'behaviors');
        $behaviors = $result->getResponse()->getBody();
        $behaviors = json_decode($behaviors, true);
        return $behaviors['data'];
    }

    public function get_demographics_by_type($value, $type)
    {
        $result = TargetingSearch::search(
            $type,
            null,
            $value);
        $data = $result->getResponse()->getBody();
        //$behaviors = json_decode($behaviors, true);
        return $data;
    }

    public function get_demographics()
    {
        $result = TargetingSearch::search(
            TargetingSearchTypes::TARGETING_CATEGORY,
            'demographics');
        $demographics = $result->getResponse()->getBody();
        $demographics = json_decode($demographics, true);
        return $demographics['data'];
    }

    public function create_adSet($campaign_id, $campaign_array, $locations, $gender_id, $ads, $io_audiences=null)
    {
        $start_time = (new \DateTime($campaign_array['campaign_start_datetime']))->format(DateTime::ISO8601);
	    $adset = new AdSet(null, $this->adAccountDataId);

        if($campaign_array['campaign_type'] == 'FB-PAGE-LIKE'){

            $adset->setData(array(
                AdSetFields::NAME => $campaign_array['name'] . ' adSET',
                AdSetFields::DAILY_BUDGET => $campaign_array['budget'] * $this->amount,
                AdSetFields::START_TIME => $start_time,
                AdSetFields::CAMPAIGN_ID => $campaign_id,
                AdSetFields::IS_AUTOBID => true,
		        AdSetFields::PROMOTED_OBJECT => array('page_id' => $ads[0]['fb_page_id']),
                //  AdSetFields::BID_AMOUNT => $campaign_array['bid'] * $this->amount,
                AdSetFields::BILLING_EVENT => BillingEvents::PAGE_LIKES,
                AdSetFields::OPTIMIZATION_GOAL=> OptimizationGoals::PAGE_LIKES
            ));

        } else if($campaign_array['campaign_type'] == 'FB-LEAD'){

            //var_dump($campaign_array, $ads[0]['fb_page_id']);
            $adset->setData(array(
                AdSetFields::NAME => $campaign_array['name'] . ' adSET',
                AdSetFields::DAILY_BUDGET => $campaign_array['budget'] * $this->amount,
                AdSetFields::START_TIME => $start_time,
                AdSetFields::CAMPAIGN_ID => $campaign_id,
                AdSetFields::IS_AUTOBID => true,
		        AdSetFields::PROMOTED_OBJECT => array('page_id' => $ads[0]['fb_page_id']),
                //  AdSetFields::BID_AMOUNT => $campaign_array['bid'] * $this->amount,
                AdSetFields::BILLING_EVENT => AdSetBillingEventValues::IMPRESSIONS,
                //AdSetFields::BILLING_EVENT => 'LEAD_GENERATION',
                AdSetFields::OPTIMIZATION_GOAL=> AdSetOptimizationGoalValues::LEAD_GENERATION
            ));
            //var_dump($adset);
        }
        else if($campaign_array['campaign_type'] == 'FB-VIDEO-VIEWS'){

            $adset->setData(array(
                AdSetFields::NAME => $campaign_array['name'] . ' adSET',
                AdSetFields::DAILY_BUDGET => $campaign_array['budget'] * $this->amount,
                AdSetFields::START_TIME => $start_time,
                AdSetFields::CAMPAIGN_ID => $campaign_id,
                AdSetFields::IS_AUTOBID => true,
                AdSetFields::PROMOTED_OBJECT => array(
                    'page_id' => $ads[0]['fb_page_id'],
                ),
                //  AdSetFields::BID_AMOUNT => $campaign_array['bid'] * $this->amount,
                AdSetFields::BILLING_EVENT => BillingEvents::VIDEO_VIEWS,
                AdSetFields::OPTIMIZATION_GOAL=> OptimizationGoals::VIDEO_VIEWS
                //check for type
            ));

        }
        else if($campaign_array['campaign_type'] == 'FB-VIDEO-CLICKS'){

            $adset->setData(array(
                AdSetFields::NAME => $campaign_array['name'] . ' adSET',
                AdSetFields::DAILY_BUDGET => $campaign_array['budget'] * $this->amount,
                AdSetFields::START_TIME => $start_time,
                AdSetFields::CAMPAIGN_ID => $campaign_id,
                AdSetFields::IS_AUTOBID => true,
                AdSetFields::PROMOTED_OBJECT => array('page_id' => $ads[0]['fb_page_id']),
                AdSetFields::BILLING_EVENT => AdSetBillingEventValues::CLICKS,
                AdSetFields::OPTIMIZATION_GOAL=> OptimizationGoals::CLICKS,
            ));
            //var_dump($adset);
        }
        else if($campaign_array['campaign_type'] == 'FB-LOCAL-AWARENESS') {
            $adset->setData(array(
                AdSetFields::NAME => $campaign_array['name'] . ' adSET',
                AdSetFields::DAILY_BUDGET => $campaign_array['budget'] * $this->amount,
                AdSetFields::START_TIME => $start_time,
                AdSetFields::CAMPAIGN_ID => $campaign_id,
                AdSetFields::IS_AUTOBID => true,

                AdSetFields::PROMOTED_OBJECT => array(
                    'page_id' => $ads[0]['fb_page_id'],

                ),
                //  AdSetFields::BID_AMOUNT => $campaign_array['bid'] * $this->amount,
                AdSetFields::BILLING_EVENT => BillingEvents::IMPRESSIONS,
                AdSetFields::OPTIMIZATION_GOAL=> OptimizationGoals::REACH

            ));
        }
        else if($campaign_array['campaign_type'] == 'FB-PROMOTE-EVENT') {
            $adset->setData(array(
                AdSetFields::NAME => $campaign_array['name'] . ' adSET',
                AdSetFields::DAILY_BUDGET => $campaign_array['budget'] * $this->amount,
                AdSetFields::START_TIME => $start_time,
                AdSetFields::CAMPAIGN_ID => $campaign_id,
                AdSetFields::IS_AUTOBID => true,
                //  AdSetFields::BID_AMOUNT => $campaign_array['bid'] * $this->amount,
                AdSetFields::BILLING_EVENT => BillingEvents::IMPRESSIONS,
                AdSetFields::OPTIMIZATION_GOAL=> OptimizationGoals::EVENT_RESPONSES
                //check for type
            ));
        }
        else if($campaign_array['campaign_type'] == 'FB-MOBILE-APP-INSTALLS' || $campaign_array['campaign_type'] == 'FB-DESKTOP-RIGHT-COLUMN' || $campaign_array['campaign_type'] == 'FB-DESKTOP-NEWS-FEED') {

            $dataArray = array(
                AdSetFields::NAME => $campaign_array['name'] . ' adSET',
                AdSetFields::DAILY_BUDGET => $campaign_array['budget'] * $this->amount,
                AdSetFields::START_TIME => $start_time,
                AdSetFields::CAMPAIGN_ID => $campaign_id,
                AdSetFields::IS_AUTOBID => true,
                //  AdSetFields::BID_AMOUNT => $campaign_array['bid'] * $this->amount,
                //check for type
            );

            if($campaign_array['max_clicks']) {
                $dataArray[AdSetFields::BILLING_EVENT] = BillingEvents::LINK_CLICKS;
                $dataArray[AdSetFields::OPTIMIZATION_GOAL] = OptimizationGoals::LINK_CLICKS;
            }
            else {
                $dataArray[AdSetFields::BILLING_EVENT] = BillingEvents::IMPRESSIONS;
                $dataArray[AdSetFields::OPTIMIZATION_GOAL] = OptimizationGoals::IMPRESSIONS;
            }

            $adset->setData($dataArray);

        }
        else if($campaign_array['campaign_type'] == 'FB-INSTAGRAM') {
            $adset->setData(array(
                AdSetFields::NAME => $campaign_array['name'] . ' adSET',
                AdSetFields::DAILY_BUDGET => $campaign_array['budget'] * $this->amount,
                AdSetFields::START_TIME => $start_time,
                AdSetFields::PROMOTED_OBJECT => array(
                    'page_id' => $ads[0]['fb_page_id'],
                ),
                AdSetFields::CAMPAIGN_ID => $campaign_id,
                AdSetFields::IS_AUTOBID => true,
                //  AdSetFields::BID_AMOUNT => $campaign_array['bid'] * $this->amount,
                AdSetFields::BILLING_EVENT => BillingEvents::LINK_CLICKS,
                AdSetFields::OPTIMIZATION_GOAL=> OptimizationGoals::LINK_CLICKS
                //check for type
            ));
        }
        else {

            $dataArray = array(
                AdSetFields::NAME => $campaign_array['name'] . ' adSET',
                AdSetFields::DAILY_BUDGET => $campaign_array['budget'] * $this->amount,
                AdSetFields::START_TIME => $start_time,

                AdSetFields::CAMPAIGN_ID => $campaign_id,
                AdSetFields::IS_AUTOBID => true,
                //  AdSetFields::BID_AMOUNT => $campaign_array['bid'] * $this->amount,
            );
            if($campaign_array['max_clicks']) {
                $dataArray[AdSetFields::BILLING_EVENT] = BillingEvents::LINK_CLICKS;
                $dataArray[AdSetFields::OPTIMIZATION_GOAL] = OptimizationGoals::LINK_CLICKS;
            }
            else {
                $dataArray[AdSetFields::BILLING_EVENT] = BillingEvents::IMPRESSIONS;
                $dataArray[AdSetFields::OPTIMIZATION_GOAL] = OptimizationGoals::IMPRESSIONS;
            }

            if($campaign_array['campaign_type'] == 'FB-CAROUSEL-AD' && $campaign_array['is_instagram'] == 'Y') { var_dump(11111,  $ads[0]['fb_page_id']);
                $dataArray[AdSetFields::PROMOTED_OBJECT] = array(
                    'page_id' => $ads[0]['fb_page_id'],
                    //'page_id' => '176293102495461',
                );
            }
            // var_dump($dataArray);
            $adset->setData($dataArray);
                //check for type
        }

        $placement = $this->get_placement($campaign_array['campaign_type'], $campaign_array['is_audience_network'], $campaign_array['is_instagram']);
        // $interest_id_name = $this->array_for_interests($campaign_array);
        $interest_id_name = array();
        if($campaign_array['interests']) {
            $interests = $this->get_interests();
            $interests_array = explode(',', $campaign_array['interests']);

            foreach($interests as $interest) {
                $interest_sort[$interest['name']] = $interest;
            }

            foreach($interests_array as $interest) {
                $array['name'] = $interest;
                $array['id'] = $interest_sort[$interest]['id'];
                if (!empty($array['id']) && !empty($array['name'])) {
                    $interest_id_name[] = $array;
                }
            }

        }
        if($campaign_array['behaviors']) {
            $behaviors = $this->get_behaviors();
            $behaviors_array = explode(',', $campaign_array['behaviors']);

            foreach($behaviors as $behavior) {
                $behaviors_sort[$behavior['name']] = $behavior;
            }

            foreach($behaviors_array as $behavior) {
                $array['name'] = $behavior;
                $array['id'] = $behaviors_sort[$behavior]['id'];
                if (!empty($array['id']) && !empty($array['name'])) {
                    $behaviors_id_name[] = $array;
                }
            }

            //var_dump('behavi', $behaviors_array, $behaviors_sort, $behaviors_id_name);
        }

        if($campaign_array['demographics']) {

            $demographics_array = json_decode($campaign_array['demographics'], true);

            foreach($demographics_array as $demographic_json) {
                if(!is_array($demographic_json)){
                    $demographic = json_decode($demographic_json, true);
                } else {
                    $demographic = $demographic_json;
                }
                $array['name'] = $demographic['name'];
                $array['id'] = $demographic['id'];
                if (!empty($array['id']) && !empty($array['name'])) {
                    $demographics_id_name_by_type[$demographic['type']][] = $array;
                }
            }

        }

        if($campaign_array['campaign_type'] == 'FB-PAGE-LIKE' || $campaign_array['campaign_type'] == 'FB-VIDEO-VIEWS'){
            $params = array(
                TargetingFields::GEO_LOCATIONS => $locations,
                TargetingFields::INTERESTS => $interest_id_name,
                TargetingFields::EXCLUDED_CONNECTIONS => array('page_id' => $ads[0]['fb_page_id']),
                TargetingFields::PUBLISHER_PLATFORMS => $placement['platform'],
                TargetingFields::DEVICE_PLATFORMS => $placement['device'],
                TargetingFields::FACEBOOK_POSITIONS => $placement['position'],
            );
        }
        else if($campaign_array['campaign_type'] == 'FB-INSTAGRAM') {
            $params = array(
                TargetingFields::GEO_LOCATIONS => $locations,
                TargetingFields::PUBLISHER_PLATFORMS => $placement['platform'],
                TargetingFields::DEVICE_PLATFORMS => $placement['device'],
//                TargetingFields::FACEBOOK_POSITIONS => $placement['position'],
                TargetingFields::INTERESTS => $interest_id_name,
            );
        }
        else if($campaign_array['campaign_type'] == 'FB-DESKTOP-RIGHT-COLUMN' || $campaign_array['campaign_type'] == 'FB-DESKTOP-NEWS-FEED' || $campaign_array['campaign_type'] == 'FB-MOBILE-NEWS-FEED'){

            $placement['platform'] = array(
                'facebook',
                'instagram',
                'audience_network'
            );
            $placement['device'] = array(
                'mobile',
                'desktop'
            );
            $placement['position'] = array(
                'feed',
                'right_hand_column'
            );

            $params = array(
                TargetingFields::GEO_LOCATIONS => $locations,
                TargetingFields::INTERESTS => $interest_id_name,
                TargetingFields::PUBLISHER_PLATFORMS => $placement['platform'],
                TargetingFields::DEVICE_PLATFORMS => $placement['device'],
                TargetingFields::FACEBOOK_POSITIONS => $placement['position'],
            );
        }
        else {
            $params = array(
                TargetingFields::GEO_LOCATIONS => $locations,
                TargetingFields::PUBLISHER_PLATFORMS => $placement['platform'],
                TargetingFields::DEVICE_PLATFORMS => $placement['device'],
                TargetingFields::FACEBOOK_POSITIONS => $placement['position'],
                TargetingFields::INTERESTS => $interest_id_name,
            );
        }

        if ($gender_id) {
            $params[TargetingFields::GENDERS] = array($gender_id);
        }

        if ($campaign_array['demographics']) {

            foreach ($demographics_id_name_by_type as $type=>$value){
                $params[$type] = $value;
            }
        }


        if ($campaign_array['educations']) {

            $educations_array = json_decode($campaign_array['educations'],true);
            foreach ($educations_array as $education_json){
                if(!is_array($education_json)) {
                    $education = json_decode($education_json, true);
                } else {
                    $education = $education_json;
                }
                $array['name'] = $education['name'];
                $array['id'] = $education['id'];
                if (!empty($array['id']) && !empty($array['name'])) {
                    $educations_id_name[] = $array;
                }
            }

            $params[TargetingFields::EDUCATION_SCHOOLS] = $educations_id_name;
        }

        if ($campaign_array['works']) {
            $works_array = json_decode($campaign_array['works'], true);
            foreach ($works_array as $work_json){
                if(!is_array($work_json)) {
                    $work = json_decode($work_json, true);
                } else {
                    $work = $work_json;
                }
                $array['name'] = $work['name'];
                $array['id'] = $work['id'];
                if (!empty($array['id']) && !empty($array['name'])) {
                    $works_id_name[] = $array;
                }
            }

            $params[TargetingFields::WORK_EMPLOYERS] = $works_id_name;
        }

        if ($campaign_array['jobs']) {
            $jobs_array = json_decode($campaign_array['jobs'], true);
            foreach ($jobs_array as $job_json){
                if(!is_array($job_json)) {
                    $job = json_decode($job_json, true);
                } else {
                    $job = $job_json;
                }
                $array['name'] = $job['name'];
                $array['id'] = $job['id'];
                if (!empty($array['id']) && !empty($array['name'])) {
                    $jobs_id_name[] = $array;
                }
            }

            $params[TargetingFields::WORK_POSITIONS] = $jobs_id_name;
        }

        if ($campaign_array['majors']) {
            $majors_array = json_decode($campaign_array['majors'], true);
            foreach ($majors_array as $major_json){
                if(!is_array($major_json)) {
                    $major = json_decode($major_json, true);
                } else {
                    $major = $major_json;
                }
                $array['name'] = $major['name'];
                $array['id'] = $major['id'];
                if (!empty($array['id']) && !empty($array['name'])) {
                    $majors_id_name[] = $array;
                }
            }

            $params[TargetingFields::EDUCATION_MAJORS] = $majors_id_name;
        }

        if ($campaign_array['behaviors']) {
            $params[TargetingFields::BEHAVIORS] = $behaviors_id_name;
        }

        if ($io_audiences) {
            $params[TargetingFields::CUSTOM_AUDIENCES] = $io_audiences;
        }

        $adset->setData(array(
            AdSetFields::TARGETING => (new Targeting())->setData($params),
        ));
        $response = array('message' => '', 'result' => '');

        try {
            $adset->create(array(AdSet::STATUS_PARAM_NAME => AdSet::STATUS_ACTIVE));

        } catch (Exception $e) {
            //var_dump($e); // exit;
            $response['message'] = $e->getErrorUserMessage();
            var_dump($e->getErrorUserMessage());
            return $response;
        }
        $response['result'] = $adset->getData()['id'];
        return $response;
    }

    //create audience for adset
    public function create_form($form_data)
    {

        $fb = new Facebook([
            'app_id' => $this->fbAppId,
            'app_secret' => $this->fbAppSecret,
            'default_graph_version' => $this->apiVersion,
            'default_access_token' => $this->userAccessToken,

        ]);

        $form['name'] = $form_data['name'];
        $form['locale'] = 'EN_US';
        $form['follow_up_action_url'] = "http://reporting.prodata.media";
        $questions = ['{"type": "EMAIL"}','{"type": "FULL_NAME"}'];
        if($form_data['is_phone_number'] == 'Y') {
            $questions[] = '{"type": "PHONE"}';
        }
        $form['questions'] = $questions;
        $form['privacy_policy']['url'] = "http://reporting.prodata.media";
        $form['privacy_policy']['link_text'] = "Prodata Privacy Policy";
        $form['context_card']['title'] = $form_data['headline'];
        if($form_data['bullets']) {
            $form['context_card']['style'] = 'LIST_STYLE';
            $form['context_card']['content'] = json_decode($form_data['bullets'], true);
        } else {
            $form['context_card']['style'] = 'PARAGRAPH_STYLE';
            $form['context_card']['content'] = [$form_data['paragraph']];
        }

        $form['context_card']['button_text'] = $form_data['button_text'];

//        $form['name'] = $form_data['name'];
//        $form['locale'] = 'EN_US';
//        $form['follow_up_action_url'] = "http://reporting.prodata.media";
//        $form['questions[0]'] = '{"type": "FULL_NAME"}';
//        $form['questions[1]'] = '{"type": "EMAIL"}';
//        $form['privacy_policy[url]'] = "http://reporting.prodata.media";
//        $form['privacy_policy[link_text]'] = "Prodata Privacy Policy";
//        $form['context_card[title]'] = $form_data['headline'];
//        if($form_data['bullets']) {
//            $form['context_card[style]'] = 'LIST_STYLE';
//            $form['context_card[content][]'] = 'this is bullet';
//        } else {
//            $form['context_card']['style'] = 'PARAGRAPH_STYLE';
//            $form['context_card']['content'] = [$form_data['paragraph']];
//        }
//
//        $form['context_card[button_text]'] = $form_data['button_text'];

//        $form['access_token'] = $this->userAccessToken;
//        try {
//            $ch = curl_init();
//            $videoUrl = FacebookClient::BASE_GRAPH_URL  . '/'.$form_data["page_id"].'/leadgen_forms';
//
//            $file = new \CurlFile('uploads/permanent/a89bc4ae7a1077724a562ccdcf7a5d9e.png', 'image/png', 'cover_photo');
//            var_dump($file, $videoUrl, $form, http_build_query($form));
//
//            $form['cover_photo'] = $file;
//
//            $headers = array("Content-Type:multipart/form-data");
//
//            $options = array(
//                CURLOPT_URL => $videoUrl,
//                //CURLOPT_URL => 'http://reporting.prodata.media/v2/fb_test/test_form_post',
//                CURLOPT_HEADER => false,
//                CURLOPT_HTTPHEADER => $headers,
//                CURLOPT_RETURNTRANSFER => true,
//                CURLOPT_POST => true,
//                CURLOPT_POSTFIELDS => $form,
//                CURLOPT_VERBOSE => true
//            );
//
//            curl_setopt_array($ch, $options);
//
//            $response = curl_exec($ch);
//            var_dump($response); exit;
//            if (FALSE === $response) {
//                $curlErr = curl_error($ch);
//                $curlErrNum = curl_errno($ch);
//
//                curl_close($ch); echo 666;
//                throw new Exception($curlErr, $curlErrNum);
//            }
//
//            curl_close($ch);
//
//        } catch (Exception $e) { var_dump($e, 77); exit;
//            $codeObj = json_decode($e);
//            echo '<pre>';
//            print_r($codeObj);echo 'codeObj'; exit;
//
//        }







//        $file1 = new \CurlFile($form_data['image'], 'image/jpg', 'cover_photo');

        //$file = $fb->fileToUpload('uploads/permanent/c641d7d0a1b30a7c49edff7d2f3cebca.jpg');
//        $file = $fb->fileToUpload('uploads/tmp/50337421d0941232b52fd228d1739300.png');
        //$file = $fb->fileToUpload('http://reporting.prodata.media/uploads/tmp//7d23c0db5cb4620480933042fa595bf2.jpg');
        //$file = $fb->fileToUpload('uploads/tmp/\e4174cc191118d507d451f6447bdaba1.txt');
        //$form['cover_photo'] = '5455http://reporting.prodata.media/uploads/tmp//7d23c0db5cb4620480933042fa595bf2.jpg';
//        $form['cover_photo'] = $file;
//        $form['cover_photooo'] = 1222;
//        $form['cover_phoeetooo'] = 1222;
//        $form['files'] = $file1;
        //var_dump($file->getMimetype()); exit;
        var_dump('form', $form);
//        $form['name'] = 'lead for';
//        $form['locale'] = 'EN_US';
//        $form['follow_up_action_url'] = 'http://example.com';
//        $form['questions'] = ['{"type": "EMAIL"}', '{"type": "CUSTOM", "label": "Select your favorite car"}'];
//        $form['privacy_policy']['url'] = "http://www.xyzabc.com";
//        $form['privacy_policy']['link_text'] = "Jasper Market Privacy Policy";
//        $form['custom_disclaimer']['title'] = "Jasper Market Privacy Policy";
//        $form['custom_disclaimer']['body']['text'] = "Jasper Market Privacy Policy";
//        $form['custom_disclaimer']['body']['url_entities'] = ['{"offset": 3, "length": 6, "url": "http://example.com"}'];
//        $form['custom_disclaimer']['checkboxes'] = ['{"is_required": false, "is_checked_by_default": false, "text": "Allow Jasper Market to contact you via phone", "key": "checkbox_1"}'];
//        $form['context_card']['title'] = "Sign up to win 20 dollars";
//        $form['context_card']['style'] = 'LIST_STYLE';
//        $form['context_card']['content'] = ["Easy sign-up flow", "Submit your info to have a chance to win"];
//        $form['context_card']['button_text'] = "Get Started";



        //var_dump($file); exit;
        //$form_data['page_id'] = '176293102495461';
        //$request = $fb->request('POST','/176293102495461/leadgen_legal_content', $form);
        //$me = new AdUser('me'); var_dump($me);
        $request = $fb->request('POST','/'.$form_data['page_id'].'/leadgen_forms', $form); //var_dump($request);

//        legal_cont_id = 1645345155762639
        //var_dump($request); exit;
        $response = array('message' => '', 'result' => '');

        try {
            $request = $fb->getClient()->sendRequest($request);
        } catch (Exception $e) {
            //var_dump(777, $e); //exit;
            $response['message'] = $e->getMessage();
            var_dump($response);
            return $response;
        }
        //var_dump($response); exit;
        $response_json = $request->getBody();

        $array = json_decode($response_json, true);

        $response['result'] = $array['id'];
        //var_dump($array); exit;
        return $response;
//
//        $context_card['title'] = "Sign up to win 20 dollars";
//        $context_card['style'] = 'LIST_STYLE';
//        $context_card['content'] = ["Easy sign-up flow", "Submit your info to have a chance to win"];
//        $context_card['button_text'] = "Get Started";
//        //$context_card['title']
//
//        $card_request = $fb->request('POST','/me/leadgen_context_cards', $context_card);
//        try {
//            $response = $fb->getClient()->sendRequest($card_request);
//        } catch (Exception $e) {
//            var_dump(777, $e); exit;
//        }

//        $response_json = $response->getBody();
//
//        $array = json_decode($response_json, true);
//        var_dump($array); exit;

    }
    //create audience for adset
    public function create_audience($campaign_io)
    {
        $pixel = new AdsPixel($this->pixel_id, $this->adAccountDataId);


        $data = $pixel->read(array(AdsPixelsFields::CODE))->getData();

        $custom_audience = new CustomAudience(null, $this->adAccountDataId);

        $custom_audience->setData(
            array(
                CustomAudienceFields::PIXEL_ID => $this->pixel_id,
                CustomAudienceFields::RETENTION_DAYS => 150,
                CustomAudienceFields::NAME => $campaign_io,
                CustomAudienceFields::SUBTYPE => CustomAudienceSubtypes::WEBSITE,
                CustomAudienceFields::RULE => array('url' => array('i_contains' => '')),
                CustomAudienceFields::PREFILL => true,

            )
        );

        $custom_audience->create();

        $audience_id = $custom_audience->getData()['id'];

        return array('id' => $audience_id, 'code' => $data['code']);

    }

    //create audience for adset
    public function create_lookalike_audience($lookalike_data)
    {
        $pixel = new AdsPixel($this->pixel_id, $this->adAccountDataId);

        $data = $pixel->read(array(AdsPixelsFields::CODE))->getData();

        $lookalike = new CustomAudience(null, $this->adAccountDataId);
        $lookalike_fields = array(
            CustomAudienceFields::NAME => $lookalike_data['name'],
            CustomAudienceFields::SUBTYPE => CustomAudienceSubtypes::LOOKALIKE
        );

        if($lookalike_data['type'] == 'similarity') {
            $lookalike_spec = array(
                'type' => 'similarity',
                'country' => 'US',
            );
            $lookalike_fields[CustomAudienceFields::ORIGIN_AUDIENCE_ID] = '';

        } else if($lookalike_data['type'] == 'page') {
            $lookalike_spec = array(
                'ratio' => 0.01,
                'country' => 'US',
                'page_id' => $lookalike_data['page_id'],
                'conversion_type' => 'page_like',
              );
        } else if($lookalike_data['type'] == 'pixel') {
            array(
                'pixel_ids' => $lookalike_data['pixel'],
                'ratio' => 0.01,
                'conversion_type' => 'offsite',
                'country' => 'US',
              );
        }
//        else if($lookalike_data['type'] = 'campaign_conversions') {
//            $lookalike_spec = array(
//                'origin_ids' => $lookalike_data['type'],
//                'starting_ratio' => 0.03,
//                'ratio' => 0.05,
//                'conversion_type' => 'campaign_conversions',
//                'country' => 'US',
//            );
//        }
        echo '<pre>';
        var_dump($lookalike_data, $lookalike_spec);
        $lookalike_fields[CustomAudienceFields::LOOKALIKE_SPEC] = $lookalike_spec;

        $lookalike->setData($lookalike_fields);

        //var_dump($lookalike);
        try {
            $lookalike->create(); var_dump(555);
        } catch (Exception $e) {

            print_r($e); // exit;
            $response['message'] = $e->getErrorUserMessage();
            return $response;
        }

        var_dump($lookalike->getData()['id']); //exit;
        $audience_id = $lookalike->getData()['id'];

        return array('id' => $audience_id, 'code' => $data['code']);

    }

    public function create_custom_audience($email_data)
    {
        $pixel = new AdsPixel($this->pixel_id, $this->adAccountDataId);

        $data = $pixel->read(array(AdsPixelsFields::CODE))->getData();

        $custom_audience = new CustomAudience(null, $this->adAccountDataId);

        echo '<pre>';

        $custom_audience->setData(
            array(
                CustomAudienceFields::NAME => $email_data['name'],
                CustomAudienceFields::SUBTYPE => CustomAudienceSubtypes::CUSTOM,
            )
        );

        try {
            $custom_audience->create();
        } catch (Exception $e) {
            var_dump($e); exit;
        }

        $emails = file($email_data['file'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        //$emails = file('uploads/tmp/16e9e2b6d8c8a6bf5f7a74c4851cb042.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($emails as $email) {
            $new_email_array[] = trim($email);
        }

        $custom_audience->addUsers($new_email_array, CustomAudienceTypes::EMAIL);

        $audience_id = $custom_audience->getData()['id'];
        //var_dump($audience_id); exit;
//        $users = array(
//            array('fname', 'lname', 'someone@example.com'),
//            array('fnamenew', 'lnamenew', 'someone_new@example.com'),
//        );
//
//        $schema = array(
//            CustomAudienceMultikeySchemaFields::FIRST_NAME,
//            CustomAudienceMultikeySchemaFields::LAST_NAME,
//            CustomAudienceMultikeySchemaFields::EMAIL,
//        );
//
//        $audience = new CustomAudienceMultiKey($audience_id);
//
//        try {
//            $audience->addUsers($users, $schema);
//        } catch (Exception $e) {
//            var_dump($e); exit;
//        }
        //var_dump($custom_audience);
        echo 'email created';
        return array('id' => $audience_id, 'code' => $data['code']);

    }

    public function add_audience_to_group($adset_id, $audience_id, $campaign_array, $edit_locations, $gender_id)
    {
        $adset = new AdSet($adset_id, $this->adAccountDataId);

        $response = array('message' => '', 'result' => '');

        $interest_id_name = $this->array_for_interests($campaign_array);

        $placement = $this->get_placement($campaign_array['campaign_type'], $campaign_array['is_audience_network'], $campaign_array['is_instagram']);

        $params = array(
            TargetingFields::CUSTOM_AUDIENCES => array(array('id' => $audience_id)),
            TargetingFields::GEO_LOCATIONS => $edit_locations,
            TargetingFields::INTERESTS => $interest_id_name,
            // TargetingFields::PAGE_TYPES => $placement,
            TargetingFields::PUBLISHER_PLATFORMS => $placement['platform'],
            TargetingFields::DEVICE_PLATFORMS => $placement['device'],
            TargetingFields::FACEBOOK_POSITIONS => $placement['position'],
        );

        if ($gender_id) {
            $params[TargetingFields::GENDERS] = array($gender_id);
        }
        $adset->setData(array(
                AdSetFields::TARGETING => (new Targeting())->setData($params),
            )
        );
        try {
            $adset->update();
        } catch (Exception $e) {
            $response['message'] = $e->getErrorUserMessage();
            return $response;
        }
    }

    public function create_creative($adArray,$campaign_type = null)
    {
        if(($campaign_type == 'FB-DESKTOP-RIGHT-COLUMN') || ($campaign_type == 'FB-DESKTOP-NEWS-FEED') || ($campaign_type == 'FB-INSTAGRAM') || ($campaign_type == 'FB-MOBILE-NEWS-FEED') || ($campaign_type == 'FB-PAGE-LIKE') )
        {

            $delete = "http://" . $_SERVER["HTTP_HOST"]. '/';
            $creative_url=str_replace($delete,'',$adArray['creative_url']);

            $image = new AdImage(null, $this->adAccountDataId);
            $image->{AdImageFields::FILENAME} = $creative_url;
            try {
              $image->create();
            } catch (Exception $e) { var_dump($e);// exit;
              $response = array('message' => '', 'result' => '');
              $response['message'] = $e->getErrorUserMessage();
              return $response;
            }

            $image_hash = $image->{AdImageFields::HASH}.PHP_EOL;

            $link_data = new AdCreativeLinkData();

            $link_data->setData(array(
            AdCreativeLinkDataFields::MESSAGE => $adArray['fb_description'],
            AdCreativeLinkDataFields::DESCRIPTION => $adArray['fb_description'],
            AdCreativeLinkDataFields::LINK => $adArray['destination_url'],

            AdCreativeLinkDataFields::CAPTION => $adArray['display_url'],
            AdCreativeLinkDataFields::IMAGE_HASH => $image_hash,
//            AdCreativeLinkDataFields::PICTURE => $adArray['creative_url'],
            //AdCreativeLinkDataFields::PICTURE => 'http://reporting.prodata.media/uploads/permanent/88c1a957c6e11981fd04e19378333a8c.png',
            AdCreativeLinkDataFields::NAME => $adArray['title'],

            ));

            $object_story_spec = new AdCreativeObjectStorySpec();

            if($campaign_type != 'FB-PAGE-LIKE'){
                $object_story_spec->setData(array(
                    AdCreativeObjectStorySpecFields::PAGE_ID => '176293102495461',
                    AdCreativeObjectStorySpecFields::INSTAGRAM_ACTOR_ID => '1031554676971310',
                    AdCreativeObjectStorySpecFields::LINK_DATA => $link_data,
                ));
            } else {
                $object_story_spec->setData(array(
                    AdCreativeObjectStorySpecFields::PAGE_ID => $adArray['fb_page_id'],
                    AdCreativeObjectStorySpecFields::LINK_DATA => $link_data,
                ));
            }

            $adArray['destination_url'] = null;

        }
        else if($campaign_type == 'FB-VIDEO-VIEWS') {

          $video_id = $this->upload_video_and_get_video_id($adArray['video_url']);

            $video_data = new VideoData();
            $video_data->setData(array(
                VideoDataFields::DESCRIPTION => $adArray['fb_description'],
                VideoDataFields::IMAGE_URL => $adArray['creative_url'],
                VideoDataFields::VIDEO_ID => $video_id,
                VideoDataFields::TITLE => $adArray['title'],
                //VideoDataFields::CALL_TO_ACTION => array(
                  //  'type' => CallToActionTypes::,
        //            'value' => array(
        //                'page' => (int)$adArray['fb_page_id'],
        //            ),
                //),
            ));

              $object_story_spec = new AdCreativeObjectStorySpec();
              $object_story_spec->setData(array(
                  AdCreativeObjectStorySpecFields::PAGE_ID => (int)$adArray['fb_page_id'],
                  AdCreativeObjectStorySpecFields::VIDEO_DATA => $video_data
              ));

          //$adArray['destination_url'] = null;

        }
        else if($campaign_type == 'FB-PROMOTE-EVENT') {

          $link_data = new AdCreativeLinkData();

          $link_data->setData(array(
              AdCreativeLinkDataFields::LINK => $adArray['event_url'],
          ));

          $object_story_spec = new AdCreativeObjectStorySpec();
          $object_story_spec->setData(array(
              AdCreativeObjectStorySpecFields::PAGE_ID => '221313168255572',
              AdCreativeObjectStorySpecFields::LINK_DATA => $link_data,
          ));

        }
        else if($campaign_type == 'FB-VIDEO-CLICKS') {

          $video_id = $this->upload_video_and_get_video_id($adArray['video_url']);

          $video_data = new VideoData();
          $video_data->setData(array(
              VideoDataFields::DESCRIPTION => $adArray['fb_description'],
              VideoDataFields::IMAGE_URL => $adArray['creative_url'],
              VideoDataFields::VIDEO_ID => $video_id,
              VideoDataFields::TITLE => $adArray['title'],
              VideoDataFields::CALL_TO_ACTION => array(
                  'type' => $adArray['call_to_action'],
                  'value' => array(
                      'link' => $adArray['destination_url'],
                ),
              ),
          ));


          $object_story_spec = new AdCreativeObjectStorySpec();
          $object_story_spec->setData(array(
              AdCreativeObjectStorySpecFields::PAGE_ID => $adArray['fb_page_id'],
              AdCreativeObjectStorySpecFields::VIDEO_DATA => $video_data
          ));

          //$adArray['destination_url'] = null;

        }
        else if($campaign_type == 'FB-CAROUSEL-AD') {
            foreach ($adArray as $ad) {

//                $product1 = (new AdCreativeLinkDataChildAttachment())->setData(array(
//                    AdCreativeLinkDataChildAttachmentFields::LINK => 'https://www.link.com/product1',
//                    AdCreativeLinkDataChildAttachmentFields::NAME => 'Product 1',
//                    AdCreativeLinkDataChildAttachmentFields::DESCRIPTION => '$8.99',
//                    AdCreativeLinkDataChildAttachmentFields::IMAGE_HASH => '<IMAGE_HASH>',
//                    AdCreativeLinkDataChildAttachmentFields::VIDEO_ID => '<VIDEO_ID>',
//                ));

                $image_creative = (new AdCreativeLinkDataChildAttachment())->setData(array(
                    AdCreativeLinkDataChildAttachmentFields::LINK => $ad['destination_url'],
                    AdCreativeLinkDataChildAttachmentFields::NAME => $ad['title'],
                    AdCreativeLinkDataChildAttachmentFields::DESCRIPTION => $ad['fb_description'],
                    //AdCreativeLinkDataChildAttachmentFields::IMAGE_HASH => $image_hash,
                    //AdCreativeLinkDataChildAttachmentFields::PICTURE => 'http://reporting.prodata.media/uploads/permanent/88c1a957c6e11981fd04e19378333a8c.png',
                    AdCreativeLinkDataChildAttachmentFields::PICTURE => $ad['creative_url'],
                ));
                $images[] = $image_creative;
            }

            $link_data = new AdCreativeLinkData();

            $link_data->setData(array(
                AdCreativeLinkDataFields::LINK => $adArray[0]['destination_url'],
                AdCreativeLinkDataFields::CAPTION => $adArray[0]['display_url'],
                AdCreativeLinkDataFields::CHILD_ATTACHMENTS => $images,
                AdCreativeLinkDataFields::MULTI_SHARE_END_CARD => false,
            ));
            //var_dump('fb_page', $adArray[0]['fb_page_id']);
            $object_story_spec = new AdCreativeObjectStorySpec();
            $object_story_spec->setData(array(
                AdCreativeObjectStorySpecFields::PAGE_ID => $adArray[0]['fb_page_id'],
                //AdCreativeObjectStorySpecFields::INSTAGRAM_ACTOR_ID => 1031554676971310,
                //AdCreativeObjectStorySpecFields::PAGE_ID => '176293102495461',
                AdCreativeObjectStorySpecFields::INSTAGRAM_ACTOR_ID => '1031554676971310',
                AdCreativeObjectStorySpecFields::LINK_DATA => $link_data,
            ));
            //var_dump($object_story_spec);
        }
        else if($campaign_type == 'FB-LEAD') {

            $link_data = new AdCreativeLinkData();

            $link_data->setData(array(

                AdCreativeLinkDataFields::MESSAGE => $adArray['title'],
                AdCreativeLinkDataFields::LINK => $adArray['destination_url'],
                AdCreativeLinkDataFields::NAME => $adArray['title'],
                AdCreativeLinkDataFields::DESCRIPTION => $adArray['fb_description'],
                AdCreativeLinkDataFields::CAPTION => $adArray['display_url'],
                AdCreativeLinkDataFields::PICTURE => $adArray['creative_url'],
                //AdCreativeLinkDataFields::PICTURE => 'http://reporting.prodata.media/uploads/permanent/88c1a957c6e11981fd04e19378333a8c.png',
                AdCreativeLinkDataFields::CALL_TO_ACTION => array(
                        'type' => AdCreativeCallToActionTypeValues::SIGN_UP,
                        'value' => array(
                                'lead_gen_form_id' => $adArray['form_id'],
                        )
                ),
            ));
            var_dump($link_data, 'ppp', $adArray['fb_page_id'], $adArray['form_id']);
            $object_story_spec = new AdCreativeObjectStorySpec();
            $object_story_spec->setData(array(
                AdCreativeObjectStorySpecFields::PAGE_ID => $adArray['fb_page_id'],
                AdCreativeObjectStorySpecFields::LINK_DATA => $link_data,
            ));

        }
        else if($campaign_type == 'FB-LOCAL-AWARENESS') {

            if($adArray['creative_type'] == 'VIDEO') {
              $video_id = $this->upload_video_and_get_video_id($adArray['fb_page_id']);

              $video_data = new VideoData();
              $video_data->setData(array(
                  VideoDataFields::DESCRIPTION => $adArray['fb_description'],
                  VideoDataFields::IMAGE_URL => $adArray['creative_url'],
                  VideoDataFields::VIDEO_ID => $video_id,
                  VideoDataFields::TITLE => $adArray['title'],
                  VideoDataFields::CALL_TO_ACTION => array(
                      'type' => $adArray['call_to_action'],
                      'value' => array(
            //                               'link' => 'fbgeo://'. $locations["custom_locations"][0]['latitude'].', '. $locations["custom_locations"][0]['longitude'].', "' .$adArray['fb_description'].'"',
                          'link' => 'fbgeo://'. $adArray['lat'].', '. $adArray['lng'].', "' .$adArray['address'].'"',
                      ),
                  ),
              ));
            //                  var_dump('fbgeo://'. $locations["custom_locations"][0]['latitude'].', '. $locations["custom_locations"][0]['longitude'].', "' .$adArray['fb_description'].'"');
              $object_story_spec = new AdCreativeObjectStorySpec();
              $object_story_spec->setData(array(
                  AdCreativeObjectStorySpecFields::PAGE_ID => $adArray['fb_page_id'],
                  AdCreativeObjectStorySpecFields::VIDEO_DATA => $video_data
              ));
            } else {
                $delete = "http://" . $_SERVER["HTTP_HOST"]. '/';
                $creative_url=str_replace($delete,'',$adArray['creative_url']);

                $image = new AdImage(null, $this->adAccountDataId);
                $image->{AdImageFields::FILENAME} = $creative_url;
                try {
                  $image->create();
                } catch (Exception $e) {
                  $response = array('message' => '', 'result' => '');
                  $response['message'] = $e->getErrorUserMessage();
                  return $response;
                }

                $image_hash = $image->{AdImageFields::HASH}.PHP_EOL;

                $link_data = new AdCreativeLinkData();

                $link_data->setData(array(
                  AdCreativeLinkDataFields::MESSAGE => $adArray['title'],
                  AdCreativeLinkDataFields::LINK => $adArray['destination_url'],
                  AdCreativeLinkDataFields::NAME => $adArray['display_url'],
                  AdCreativeLinkDataFields::CAPTION => $adArray['fb_description'],
                  AdCreativeLinkDataFields::IMAGE_HASH => $image_hash,
                  AdCreativeLinkDataFields::CALL_TO_ACTION => array(
                      'type' => $adArray['call_to_action'],
                      'value' => array(
                //                              'link' => 'fbgeo://'. $locations["custom_locations"][0]['latitude'].', '. $locations["custom_locations"][0]['longitude'].', "' .$adArray['fb_description'].'"',
                          'link' => 'fbgeo://'. $adArray['lat'].', '. $adArray['lng'].', "' .$adArray['address'].'"',
                      ),
                  ),

                ));
                //var_dump('fbgeo://'. $locations["custom_locations"][0]['latitude'].', '. $locations["custom_locations"][0]['longitude'].', "' .$adArray['fb_description'].'"');
                $object_story_spec = new AdCreativeObjectStorySpec();
                $object_story_spec->setData(array(
                  //AdCreativeObjectStorySpecFields::PAGE_ID => (int)$adArray['fb_page_id'], // 840687409373475,
                  AdCreativeObjectStorySpecFields::PAGE_ID => $adArray['fb_page_id'],
                  AdCreativeObjectStorySpecFields::LINK_DATA => $link_data,
                ));

            }

        }

        $creative = new AdCreative(null, $this->adAccountDataId);

        if($campaign_type == 'FB-PAGE-LIKE') {
            $creative->setData(array(
                AdCreativeFields::TITLE => $adArray['title'],
                AdCreativeFields::BODY => $adArray['fb_description'],
                AdCreativeFields::OBJECT_URL => $adArray['destination_url'],

               // AdCreativeFields::OBJECT_STORY_SPEC => $object_story_spec,
                AdCreativeFields::OBJECT_ID => (int)$adArray['fb_page_id'],
                AdCreativeFields::IMAGE_URL => $adArray['creative_url'],

            ));

        }
        else if ($campaign_type == 'FB-VIDEO-VIEWS' || $campaign_type == 'FB-VIDEO-CLICKS' || $campaign_type == 'FB-LOCAL-AWARENESS' || $campaign_type == 'FB-CAROUSEL-AD') {

            $creative->setData(array(
                AdCreativeFields::NAME => $adArray['title'],
                AdCreativeFields::OBJECT_STORY_SPEC => $object_story_spec,
            ));

        }
        else if($campaign_type == 'FB-PROMOTE-EVENT') {

            $creative->setData(array(
                AdCreativeFields::OBJECT_TYPE => 'EVENT',
                AdCreativeFields::OBJECT_STORY_SPEC => $object_story_spec,
            ));

        }
        else if($campaign_type == 'FB-LEAD') {

            $creative->setData(array(
                AdCreativeFields::OBJECT_STORY_SPEC => $object_story_spec,
            ));

        }
        else {

            $creative->setData(array(
                AdCreativeFields::TITLE => $adArray['title'],
                AdCreativeFields::BODY => $adArray['fb_description'],
                AdCreativeFields::OBJECT_URL => $adArray['destination_url'],
                AdCreativeFields::OBJECT_STORY_SPEC => $object_story_spec,
                AdCreativeFields::IMAGE_URL => $adArray['creative_url'],
            ));

        }
        $response = array('message' => '', 'result' => '');

        try {
            $creative->create();
        } catch (Exception $e) {
            echo '<pre>';
            echo 'exeption_1';
            print_r($e) ;echo 'createive';
            $response['message'] = $e->getErrorUserMessage();
            return $response;
        }

        $response['result'] = $creative->getData()['id'];
        //var_dump($response['result']);
        return $response;

    }

    public function createAd($adSetId, $creativeId, $adName)
    {
        //var_dump($adSetId, $creativeId, $adName);echo 3333333333;
        $data = array(
            AdFields::NAME => $adName,
            AdFields::ADSET_ID => $adSetId,
            AdFields::CREATIVE => array(
                'creative_id' => $creativeId,
            ),
        );

        $ad = new Ad(null, $this->adAccountDataId);
        $ad->setData($data);

        $response = array('message' => '', 'result' => '');
        try {
            $ad->create(array(
                Ad::STATUS_PARAM_NAME => Ad::STATUS_ACTIVE,
            ));
        } catch (Exception $e) {
            // var_dump($e);
            $response['message'] = $e->getErrorUserMessage();
            return $response;
        }

        $response['result'] = $ad->getData()['id'];

        return $response;

    }

    public function get_placement($device_type, $is_audience_network, $is_instagram)
    {

        if ($device_type == 'FB-MOBILE-NEWS-FEED') {
            $placement['platform'] = array(
                'facebook'
            );
            $placement['device'] = array(
                'mobile'
            );
            $placement['position'] = array(
                'feed'
            );
//            if($is_instagram == 'Y') {
//                $placement['platform'][] = 'instagram';
//            }
            if($is_audience_network == 'Y') {
                $placement['platform'][] = 'audience_network';
            }
        } else if ($device_type == 'FB-DESKTOP-RIGHT-COLUMN' || $device_type == 'FB-VIDEO-CLICKS') {
            $placement['platform'] = array(
                'facebook'
            );
            $placement['device'] = array(
                'desktop'
            );
            $placement['position'] = array(
                'right_hand_column'
            );
//            if($is_instagram == 'Y') {
//                $placement['platform'][] = 'instagram';
//            }
        } else if ($device_type == 'FB-DESKTOP-NEWS-FEED' || $device_type == 'FB-LOCAL-AWARENESS') {
            $placement['platform'] = array(
                'facebook'
            );
            $placement['device'] = array(
                'desktop'
            );
            $placement['position'] = array(
                'feed'
            );
//            if($is_instagram == 'Y') {
//                $placement['platform'][] = 'instagram';
//            }
            if($is_audience_network == 'Y') {
                $placement['platform'][] = 'audience_network';
            }
        } else if ($device_type == 'FB-PAGE-LIKE' || $device_type == 'FB-VIDEO-VIEWS') {

            $placement['platform'] = array(
                'facebook'
            );
            $placement['device'] = array(
                'mobile',
                'desktop'
            );
            $placement['position'] = array(
                'feed',
                'right_hand_column'
            );
        } else if ($device_type == 'FB-INSTAGRAM') {

            $placement['platform'] = array(
                'instagram'
            );
            $placement['device'] = array(
                'mobile'
            );
        } else if($device_type == 'FB-CAROUSEL-AD'){
            $placement['platform'] = array(
                'facebook',
            );
            $placement['device'] = array(
                'mobile',
                'desktop'
            );
            $placement['position'] = array(
                'feed',
                'right_hand_column'
            );

            if($is_audience_network == 'Y') {
                $placement['platform'][] = 'audience_network';
            }

            if($is_instagram == 'Y') {
                $placement['platform'][] = 'instagram';
            }
        } else if($device_type == 'FB-LEAD'){
            $placement['platform'] = array(
                'facebook'
            );
            $placement['device'] = array(
                'mobile',
                'desktop'
            );
            $placement['position'] = array(
                'feed',
                'right_hand_column'
            );
        }

        return $placement;

    }


    public function upload_video_and_get_video_id($video_url) {

        try {
            $delete = "http://" . $_SERVER["HTTP_HOST"]. '/';
            $video_url=str_replace($delete,'',$video_url);
          //  $filename = 'uploads/tmp/SampleVideo_1080x720_5mb.mp4';
            $filename = $video_url;
            $filesize = filesize($filename);
            $ch = curl_init();
            $videoUrl = FacebookClient::BASE_GRAPH_VIDEO_URL . '/' . $this->apiVersion . '/'.$this->adAccountDataId.'/advideos';

            $data = array (
                'access_token' => $this->userAccessToken,
                'upload_phase' => 'start',
                'file_size' => $filesize
            );

            $params = '';
            foreach($data as $key=>$value)
                $params .= $key.'='.$value.'&';

            $params = trim($params, '&');

            $options = array(
                CURLOPT_URL => $videoUrl,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_FOLLOWLOCATION => 1,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_POST => TRUE,
                CURLOPT_POSTFIELDS => $params);

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

        } catch (Exception $e) {
            var_dump($e->getMessage());
        }

        $codeObj = json_decode($response);

        $upload_session_id = $codeObj->upload_session_id;
        $video_id = $codeObj->video_id;
        $start_offset = $codeObj->start_offset;
        $end_offset = $codeObj->end_offset;

        while ($start_offset < $end_offset) {

            $complete = 'uploads/tmp/chunk.mp4';

            $com = fopen($complete, "ab");

            $chunk_size = $end_offset - $start_offset;

            $in = fopen($filename, "rb");

            if ($in) {

                fseek($in, $start_offset);
                $buff = fread($in, $chunk_size);
                fwrite($com, $buff);

            }

            fclose($in);
            fclose($com);
            @chmod($complete, 0777);
            $file = realpath($complete);

            try {
                $ch = curl_init();
                $videoUrl = FacebookClient::BASE_GRAPH_VIDEO_URL . '/' . $this->apiVersion . '/' . $this->adAccountDataId . '/advideos';

                $file1 = new \CurlFile($file, 'video/mp4', 'video_file_chunk');
                $data = array (
                    'access_token' => $this->userAccessToken,
                    'upload_phase' => 'transfer',
                    'upload_session_id' => $upload_session_id,
                    'start_offset'      => $start_offset,
                    'video_file_chunk'  => $file1
                );

                $headers = array("Content-Type:multipart/form-data");

                $options = array(
                    CURLOPT_URL => $videoUrl,
                    CURLOPT_HEADER => false,
                    CURLOPT_HTTPHEADER => $headers,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $data,
                    CURLOPT_VERBOSE => true
                );

                curl_setopt_array($ch, $options);

                $response = curl_exec($ch);

                if (FALSE === $response) {
                    $curlErr = curl_error($ch);
                    $curlErrNum = curl_errno($ch);

                    curl_close($ch);
                    throw new Exception($curlErr, $curlErrNum);
                }

                curl_close($ch);

            } catch (Exception $e) {
                $codeObj = json_decode($e);
                echo '<pre>';
                print_r($codeObj);echo 'codeObj';

            }

            $codeObj = json_decode($response);

            unlink($complete);
            $start_offset = $codeObj->start_offset;
            $end_offset = $codeObj->end_offset;

        }

        if ($start_offset == $end_offset) {
            try {
                $ch = curl_init();
                $videoUrl = FacebookClient::BASE_GRAPH_VIDEO_URL . '/' . $this->apiVersion . '/' . $this->adAccountDataId . '/advideos';

                $data = array (
                    'access_token' => $this->userAccessToken,
                    'upload_phase' => 'finish',
                    'upload_session_id' => $upload_session_id,
                    'title'      => 'full test video type'

                );

                $options = array(
                    CURLOPT_URL => $videoUrl,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_FOLLOWLOCATION => 1,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_POST => TRUE,
                    CURLOPT_POSTFIELDS => $data);

                curl_setopt_array($ch, $options);

                $response = curl_exec($ch);

                if (FALSE === $response) {
                    $curlErr = curl_error($ch);
                    $curlErrNum = curl_errno($ch);

                    curl_close($ch);
                    throw new Exception($curlErr, $curlErrNum);
                }

                curl_close($ch);


            } catch (Exception $e) {
                print_r($e);

            }

        }
        sleep(35);
        return $video_id;

    }

    public function getAdsImpressionsByActiveCampaigns($ads)
    {

        $fb = new Facebook([
            'app_id' => $this->fbAppId,
            'app_secret' => $this->fbAppSecret,
            'default_graph_version' => $this->apiVersion,
            'default_access_token' => $this->userAccessToken,

        ]);



        $array_data = array();

        foreach ($ads as $key => $value) {

            //$value['network_creative_id'] = 6038140627808 ;

            if((int)$value['network_creative_id'] && $value['network_creative_id']) { var_dump((int)$value['network_creative_id']);
                $request = $fb->request('GET',
                    '/' . $value['network_creative_id'] . '/insights');


                $response = $fb->getClient()->sendRequest($request);


                $response_json = $response->getBody();

                $array = json_decode($response_json, true);

                $index = (string)$value['network_creative_id'];
                $array_data[$index] = $array['data'][0]['impressions'];
            }
        }

//        echo '<pre>';
//        print_r($array_data);echo 474747;

        $response = array('message' => '', 'result' => '');

        $response['result'] = $array_data;

        return $response;

    }


    public function getAdsLikesByActiveCampaigns($ads)
    {

        $fb = new Facebook([
            'app_id' => $this->fbAppId,
            'app_secret' => $this->fbAppSecret,
            'default_graph_version' => $this->apiVersion,
            'default_access_token' => $this->userAccessToken,

        ]);



        $array_data = array();

        foreach ($ads as $key => $value) {

            //$value['network_creative_id'] = 6038140627808 ;
            if($value['network_creative_id']) {
                $request = $fb->request('GET',
                    '/' . $value['network_creative_id'] . '/insights');
            }

            $response = $fb->getClient()->sendRequest($request);


            $response_json = $response->getBody();

            $array = json_decode($response_json, true);


            $index = (string)$value['network_creative_id'];

            $array_actions = $array['data'][0]['actions'];
            for($i = 0; $i <= count($array_actions); $i++){
                if($array_actions[$i]['action_type'] == 'page_like' && $array_actions[$i]['value']) {
                    $array_data[$index] = $array_actions[$i]['value'];
                }

            }


        }

//        echo '<pre>';
//        print_r($array_data);echo 474747;

        $response = array('message' => '', 'result' => '');

        $response['result'] = $array_data;

        return $response;

    }


    public function getCampaignVideoWatchMetricReport($campaign)
    {

        $fb = new Facebook([
            'app_id' => $this->fbAppId,
            'app_secret' => $this->fbAppSecret,
            'default_graph_version' => $this->apiVersion,
            'default_access_token' => $this->userAccessToken,

        ]);



            //$value['network_creative_id'] = 6038140627808 ;
            if($campaign['network_campaign_id']) {
                $request = $fb->request('GET',
                    '/' . $campaign['network_campaign_id'] . '/insights');
            }



            $response = $fb->getClient()->sendRequest($request);


            $response_json = $response->getBody();

            $array = json_decode($response_json, true);




//        echo '<pre>';
//        print_r($array_data);echo 474747;


        $response = array('message' => '', 'result' => '');

        $response['result'] = $array['data'];


        return $response;



    }
    public function getCampaignVideoWatchMetricReportWithFields($campaign)
    {

    	$fb = new Facebook([
    			'app_id' => $this->fbAppId,
    			'app_secret' => $this->fbAppSecret,
    			'default_graph_version' => $this->apiVersion,
    			'default_access_token' => $this->userAccessToken,

    	]);



    	//$value['network_creative_id'] = 6038140627808 ;
    	if($campaign['network_campaign_id']) {
    		$request = $fb->request('GET',
    				'/' . $campaign['network_campaign_id'] . '/insights?fields=video_10_sec_watched_actions,video_15_sec_watched_actions,video_30_sec_watched_actions,video_p25_watched_actions,video_p50_watched_actions,video_p75_watched_actions,video_p95_watched_actions');
    	}



    	$response = $fb->getClient()->sendRequest($request);


    	$response_json = $response->getBody();

    	$array = json_decode($response_json, true);




    	//        echo '<pre>';
    	        //print_r($this->userAccessToken);

    	$response = array('message' => '', 'result' => '');

    	$response['result'] = $array['data'];


    	return $response;



    }

    public function getAdsVideoViewsByActiveCampaigns($ads){
        $fb = new Facebook([
            'app_id' => $this->fbAppId,
            'app_secret' => $this->fbAppSecret,
            'default_graph_version' => $this->apiVersion,
            'default_access_token' => $this->userAccessToken,

        ]);



        $array_data = array();

        foreach ($ads as $key => $value) {
            if($value['creative_type'] == 'VIDEO' && (int)$value['network_creative_id']) {
                //$value['network_creative_id'] = 6038140627808 ;
                //if ($value['network_creative_id']) {
                    $request = $fb->request('GET',
                        '/' . $value['network_creative_id'] . '/insights');
                //}

                $response = $fb->getClient()->sendRequest($request);


                $response_json = $response->getBody();

                $array = json_decode($response_json, true);


                $index = (string)$value['network_creative_id'];

                $array_actions = $array['data'][0]['actions'];
                for ($i = 0; $i <= count($array_actions); $i++) {
                    if ($array_actions[$i]['action_type'] == 'video_view' && $array_actions[$i]['value']) {
                        $array_data[$index] = $array_actions[$i]['value'];

                    }
                }
            }
        }



        $response = array('message' => '', 'result' => '');

        $response['result'] = $array_data;

        return $response;
    }


    public function getAllDisapprovedAds($ads)
    {

        if (!empty($ads)) {

            foreach ($ads as $key => $value) {
                if(!$value['network_creative_id']) {
                    continue;
                }
                $index = (string)$value['network_creative_id'];

                $ads = new Ad($value['network_creative_id']);

                $ads->read(array(
                    AdFields::EFFECTIVE_STATUS,
                ));
                $effective_status = $ads->effective_status;
                if ($effective_status == 'DISAPPROVED') {
                    $ads->read(array(

                        AdFields::AD_REVIEW_FEEDBACK,
                    ));
                    $ad_review_feedbacks = $ads->ad_review_feedback;
                    foreach ($ad_review_feedbacks as $ad_review_feedback) {
                        $ad_review_feedbacks = $ad_review_feedback;
                        if (is_array($ad_review_feedbacks) && (count($ad_review_feedbacks) > 0)) {
                            foreach ($ad_review_feedbacks as $ad_review_feedback) {
                                $ad_review_feedbacks = $ad_review_feedback;
                                if (is_array($ad_review_feedbacks) && (count($ad_review_feedbacks) > 1)) {
                                    foreach ($ad_review_feedbacks as $ad_review_feedback) {
                                        $ad_review_feedbacks = $ad_review_feedback;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $ad_review_feedbacks = '';
                }

                $array_status[$index] = array('approval_status' => $ads->effective_status, 'reasons' => $ad_review_feedbacks);//$ads->effective_status;

            }
            $response = array('message' => '', 'result' => '');
            $response['result'] = $array_status;

            return $response;

        }

    }

    public function getCampaignAgeReport($campaign)
    {
        if($campaign['network_campaign_id']) {
            $campaign = new Campaign($campaign['network_campaign_id']);
        }
        $params = array(
            'breakdowns' => 'age',
        );

        $fields = array(
            InsightsFields::IMPRESSIONS,
            InsightsFields::INLINE_LINK_CLICKS,
            InsightsFields::CAMPAIGN_ID,
            InsightsFields::ACTIONS,

        );
try {


    $insights = $campaign->getInsights($fields, $params);
    $response_json = $insights->getResponse()->getBody();

} catch (Exception $e) {
    echo '<pre>';
    print_r($e);
}
        $array = json_decode($response_json, true);


        $response = array('message' => '', 'result' => '');
        $response['result'] = $array['data'];

        return $response;

    }

    public function getCampaignGenderReport($campaign)
    {

        $campaign = new Campaign($campaign['network_campaign_id']);

        $params = array(
            'breakdowns' => 'gender',
        );

        $fields = array(
            InsightsFields::IMPRESSIONS,
            InsightsFields::INLINE_LINK_CLICKS,
            InsightsFields::CAMPAIGN_ID,
            InsightsFields::ACTIONS,

        );

        $insights = $campaign->getInsights($fields, $params);
        $response_json = $insights->getResponse()->getBody();
        $array = json_decode($response_json, true);

        $response = array('message' => '', 'result' => '');
        $response['result'] = $array['data'];

        return $response;


    }

    public function get_campaigns_placements_report($campaign)
    {

        if ($campaign['campaign_type'] == 'FB-PAGE-LIKE') {
        $campaign = new Campaign($campaign['network_campaign_id']);

        $params = array(
            'breakdowns' => 'placement',
//            'sort' => array(
//                'actions' => 'likes_descending'
//            )
        );
    } else {

        $campaign = new Campaign($campaign['network_campaign_id']);
        $params = array(
            'breakdowns' => 'placement',
            'sort' => 'inline_link_clicks_descending'


        );
    }


        $fields = array(
            InsightsFields::IMPRESSIONS,
            InsightsFields::INLINE_LINK_CLICKS,
            InsightsFields::CAMPAIGN_ID,
            InsightsFields::COST_PER_INLINE_LINK_CLICK,
            InsightsFields::ACTIONS,
        );

         $insights = $campaign->getInsights($fields, $params);

        $response_json = $insights->getResponse()->getBody();
        $array = json_decode($response_json, true);

        $response = array('message' => '', 'result' => '');
        $response['result'] = $array['data'];


        return $response;

    }

    public function get_campaigns_cost($campaign)
    {

        $campaign = new Campaign($campaign['network_campaign_id']);

        $params = array();

        $fields = array(
            InsightsFields::CAMPAIGN_ID,
            InsightsFields::SPEND,
        );

        $insights = $campaign->getInsights($fields, $params);
        $response_json = $insights->getResponse()->getBody();
        $array = json_decode($response_json, true);

        $response = array('message' => '', 'result' => '');
        $response['result'] = $array['data'][0];

        return $response;
    }


    public function updateCampaignStatus($campaign_id, $status)
    {

        $campaign = new Campaign($campaign_id);
        $response = array('message' => '', 'result' => '');

        if ($status == 'ACTIVE' || $status == 'ENABLED') {

            $updateArray = [Campaign::STATUS_PARAM_NAME => Campaign::STATUS_ACTIVE];
        } else {

            $updateArray = [Campaign::STATUS_PARAM_NAME => Campaign::STATUS_PAUSED];
        }

        try {
            $campaign->update($updateArray);
        } catch (Exception $e) {
            $response['message'] = $e->getErrorUserMessage();
            return $response;
        }

        $response['result'] = $campaign->getData()['id'];

        return $response;


    }


//    public function update_campaign_status($campaign)
//    {
//        $response = array('message'=>'','result'=>'');
//        try {
//            if ($campaign['network_campaign_status'] == 'ACTIVE') {
//
//                $updateArray = [Campaign::STATUS_PARAM_NAME => Campaign::STATUS_ACTIVE];
//            } else {
//
//                $updateArray = [Campaign::STATUS_PARAM_NAME => Campaign::STATUS_PAUSED];
//            }
//
//            $campaign = new Campaign($campaign['network_campaign_id']);
//            $campaign->update($updateArray);
//        } catch (Exception $e) {
//            $response['message'] = $e->getErrorUserMessage();
//            return $response;
//        }
//        $response['result'] = $campaign->getData()['id'];
//
//        return $response;
//    }


    public function update_ad_status($ad)
    {

        $ads = new Ad($ad["network_creative_id"]);
        $ads->read(array(
            AdFields::EFFECTIVE_STATUS,
            // AdFields::AD_REVIEW_FEEDBACK,
        ));

        $effective_status = $ads->effective_status;
        $response = array('message' => '', 'result' => '');
        if ($effective_status != 'PENDING_REVIEW') {

            //var_dump($effective_status);exit;
            try {
                if ($ad['creative_status'] == 'ACTIVE' || $ad['creative_status'] == 'ENABLED') {

                    $updateArray = [Ad::STATUS_PARAM_NAME => Ad::STATUS_ACTIVE];
                } else {

                    $updateArray = [Ad::STATUS_PARAM_NAME => Ad::STATUS_PAUSED];
                }

                $ad = new Ad($ad['network_creative_id']);
                $ad->update($updateArray);
            } catch (Exception $e) {
                $response['message'] = $e->getErrorUserMessage();
                return $response;
            }
            $response['result'] = $ad->getData()['id'];

            return $response;

        } else if ($ad['creative_status'] == "PAUSED") {
            try {
                $ad = new Ad($ad['network_creative_id']);
                $ad->delete();
            } catch (Exception $e) {
                $response['message'] = $e->getErrorUserMessage();
                return $response;
            }


        }
    }



        public function update_bid($network_group_id, $bid)
    {
        $response = array('message' => '', 'result' => '');
        try {
            $adset = new AdSet($network_group_id, $this->adAccountDataId);
            $adset->setData(array(
                AdSetFields::BID_AMOUNT => $bid * $this->amount,
            ));

            $adset->update();
        } catch (Exception $e) {
            $response['message'] = $e->getErrorUserMessage();
            return $response;
        }
        $response['result'] = $adset->getData()['id'];

        return $response;
    }

    public function update_budget($network_group_id, $budget)
    {
        $response = array('message' => '', 'result' => '');
        try {
            $adset = new AdSet($network_group_id, $this->adAccountDataId);
            $adset->setData(array(
                AdSetFields::DAILY_BUDGET => $budget * $this->amount,
            ));

            $adset->update();
        } catch (Exception $e) {
            $response['message'] = $e->getErrorUserMessage();
            return $response;
        }
        $response['result'] = $adset->getData()['id'];

        return $response;
    }


    public function edit_location($edit_locations, $adset_id, $campaign_array, $gender_id , $audience_id, $ads = null)
    {

        $adset = new AdSet($adset_id);
        $response = array('message' => '', 'result' => '');

        $interest_id_name = $this->array_for_interests($campaign_array);

        $placement = $this->get_placement($campaign_array['campaign_type'], $campaign_array['is_audience_network'], $campaign_array['is_instagram']);
       // $placement = $this->get_placement($campaign_array['device_type']);
        if($campaign_array['campaign_type'] == 'FB-PAGE-LIKE'){
            $params = array(
                TargetingFields::GEO_LOCATIONS => $edit_locations,
                TargetingFields::INTERESTS => $interest_id_name,
                TargetingFields::PUBLISHER_PLATFORMS => $placement['platform'],
                TargetingFields::DEVICE_PLATFORMS => $placement['device'],
                TargetingFields::FACEBOOK_POSITIONS => $placement['position'],
                TargetingFields::EXCLUDED_CONNECTIONS => array(
                    'page_id' => $ads[0]['fb_page_id'],
                ),
            );
        } else {
            $params = array(
                TargetingFields::GEO_LOCATIONS => $edit_locations,
                TargetingFields::INTERESTS => $interest_id_name,
                TargetingFields::PUBLISHER_PLATFORMS => $placement['platform'],
                TargetingFields::DEVICE_PLATFORMS => $placement['device'],
                TargetingFields::FACEBOOK_POSITIONS => $placement['position'],

            );
        }
        if ($gender_id) {
            $params[TargetingFields::GENDERS] = array($gender_id);
        }
//        if ($audience_id) {
//            $params[TargetingSpecsFields::CUSTOM_AUDIENCES] = array(array('id' => $audience_id));
//        }
        $adset->setData(array(
            AdSetFields::TARGETING => (new Targeting())->setData($params),
        ));
        // $adset->targeting = ((new TargetingSpecs())->setData($params));

        try {

            $adset->update();

        } catch (Exception $e) {
            $response['message'] = $e->getErrorUserMessage();
            return $response;
        }
        $response['result'] = $adset->getData()['id'];

        return $response;

    }

    public function get_form($form_data)
    {
        $response = array('message' => '', 'result' => '');
        try {
            $form = new LeadgenForm($form_data['form_network_id']);
            $result = $form->read();
            echo '<pre>';
//            var_dump($result->getData()["leadgen_export_csv_url"]); exit;
            $leads = $form->getLeads();
            //https://www.facebook.com/ads/lead_gen/export_csv/?id=1785832328322502&type=form&source_type=graph_api
            //$ad = new Ad('6052919911208');
//            $ad = new Ad('6053154428608');
//            $leads = $ad->getLeads();
            //var_dump($leads->getLastResponse()->getBody()); exit;
            $leads_array = json_decode($leads->getLastResponse()->getBody(), true);
            $response['result'] = $leads_array;
            return $response;
            var_dump($leads_array); exit;
        } catch (Exception $e) {
            $response['message'] = $e->getErrorUserMessage();
            return $response;
        }
        $response['result'] = $adset->getData()['id'];

        return $response;
    }

    public function get_leads($form_id)
    {
        $response = array('message' => '', 'result' => '');
        try {
            $form = new LeadgenForm($form_id);
            $result = $form->read();
            echo '<pre>';
//            var_dump($result->getData()["leadgen_export_csv_url"]); exit;
            $leads = $form->getLeads();
            //https://www.facebook.com/ads/lead_gen/export_csv/?id=1785832328322502&type=form&source_type=graph_api
            //$ad = new Ad('6052919911208');
            $ad = new Ad('6053154428608');
            $leads = $ad->getLeads();
            //var_dump($leads); exit;
            $leads_array = json_decode($leads->getLastResponse()->getBody(), true);
            $response['result'] = $leads_array['data']; //var_dump($leads_array); exit;
            return $response;
           // var_dump($leads_array); exit;
        } catch (Exception $e) {
            $response['message'] = $e->getErrorUserMessage();
            return $response;
        }
        $response['result'] = $adset->getData()['id'];

        return $response;
    }

    public function get_leads_by_ad_id($ad_id)
    {
        $response = array('message' => '', 'result' => '');
        try {

            //https://www.facebook.com/ads/lead_gen/export_csv/?id=1785832328322502&type=form&source_type=graph_api
            //$ad = new Ad('6052919911208');
            //$ad = new Ad('6053154428608');
            //var_dump($ad_id); exit;
            $ad = new Ad($ad_id);
            $leads = $ad->getLeads();
            //var_dump($leads); exit;
            $leads_array = json_decode($leads->getLastResponse()->getBody(), true);
            $response['result'] = $leads_array['data']; //var_dump($leads_array); exit;
            return $response;
           // var_dump($leads_array); exit;
        } catch (Exception $e) {
            $response['message'] = $e->getErrorUserMessage();
            return $response;
        }
        $response['result'] = $adset->getData()['id'];

        return $response;
    }
}
