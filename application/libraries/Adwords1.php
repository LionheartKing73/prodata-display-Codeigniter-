<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
define('SRC_PATH', APPPATH.'third_party/Adwords/src/');
define('LIB_PATH', 'Google/Api/Ads/AdWords/Lib');
define('UTIL_PATH', 'Google/Api/Ads/Common/Util');
define('AW_UTIL_PATH', 'Google/Api/Ads/AdWords/Util');
define('DFP_PATH', 'Google/Api/Ads/Dfp/Lib');
define('DFP_UTIL_PATH', 'Google/Api/Ads/Dfp/Util');
define('THIRD_PARTY', APPPATH.'third_party/');

define('MAIN_LIB', 'Google/Api/Ads/AdWords/v201509');

define('ADWORDS_VERSION', 'v201509');

// Configure include path
ini_set('include_path', implode(array(
        ini_get('include_path'), PATH_SEPARATOR, SRC_PATH))
);

// Include the AdWordsUser file
require_once SRC_PATH.LIB_PATH. '/AdWordsUser.php';
require_once SRC_PATH.MAIN_LIB. '/AdwordsUserListService.php';
require_once SRC_PATH.LIB_PATH. '/AdWordsSoapClient.php';
require_once SRC_PATH.UTIL_PATH. '/MediaUtils.php';
require_once SRC_PATH.DFP_PATH. '/DfpUser.php';
require_once THIRD_PARTY . 'imageResizer/classSimpleImage.php';


class Adwords extends AdWordsUser {
    public function __construct() {
        parent::__construct();
    }

    /**
     * Runs the example.
     * @param AdWordsUser $user the user to run the example with
     * @param io insertion order id ( io is conversionType and userList name )
     */
    public function addAudience(AdWordsUser $user, $io) {
        // Get the services, which loads the required classes.
        $userListService = $user->GetService('AdwordsUserListService', ADWORDS_VERSION);
        $conversionTrackerService =
            $user->GetService('ConversionTrackerService', ADWORDS_VERSION);

        // Create conversion type (tag).
        $conversionType = new UserListConversionType();
        $conversionType->name = $io;

        // Create remarketing user list.
        $userList = new BasicUserList();
        $userList->name = $io;
        $userList->conversionTypes = array($conversionType);

        // Set additional settings (optional).
        $userList->description = "(AUTO) ProDataFeed";
        $userList->status = 'OPEN';
        $userList->membershipLifeSpan = 365;

        // Create operation.
        $operation = new UserListOperation();
        $operation->operand = $userList;
        $operation->operator = 'ADD';

        $operations = array($operation);

        // Make the mutate request.
        $result = $userListService->mutate($operations);
        $userList = $result->value[0];

        // Wait a moment before retrieving the conversion snippet.
        sleep(1);

        // Create the selector.
        $selector = new Selector();
        $selector->fields = array('Id');
        $selector->predicates[] =
            new Predicate('Id', 'IN', array($userList->conversionTypes[0]->id));

        // Make the get request.
        $page = $conversionTrackerService->get($selector);

        $conversionTracker = $page->entries[0];

        // return result.
        return array('userList' => $userList, 'code' => $conversionTracker);
    }

    //gets the total number of users into all of the audiences of our campaign
    public function getNumberOfUsersIntoAudiences(AdWordsUser $user) {

        // Get the services, which loads the required classes.
        $userListService = $user->GetService('AdwordsUserListService', ADWORDS_VERSION);

        $conversionTrackerService =
            $user->GetService('ConversionTrackerService', ADWORDS_VERSION);


        // Create remarketing user list.
        $userList = new BasicUserList();

        //Create selector object which will be used during sending the get request
        //to the server
        $selector = new Selector();


        //selector's fields property should contain a list of fields which we want to get from the server
        //  $selector->fields = array('Size', 'SizeRange', 'PromotedImpressions');
        $selector->fields = array('Size', 'SizeRange');

        //sends the get request
        $result = $userListService->get($selector);



        return $result;
    }
    /**
     * Gets an OAuth2 credential.
     * @param string $user the user that contains the client ID and secret
     * @return array the user's OAuth 2 credentials
     */
    //http://reporting.prodata.media/index.php?/adword/refreshToken?test=1
    //http://report-site.com/index.php/adword/refreshToken
    public function GetOAuth2Credential($user, $authCode) {
        //$redirectUri = base_url().'adword/refreshToken?test=1';
        $redirectUri = 'http://reporting.prodata.media/index.php/adword/refreshToken';

        $offline = TRUE;
        // Get the authorization URL for the OAuth2 token.
        // No redirect URL is being used since this is an installed application. A web
        // application would pass in a redirect URL back to the application,
        // ensuring it's one that has been configured in the API console.
        // Passing true for the second parameter ($offline) will provide us a refresh
        // token which can used be refresh the access token when it expires.
        $OAuth2Handler = $user->GetOAuth2Handler();
        $authorizationUrl = $OAuth2Handler->GetAuthorizationUrl(
            $user->GetOAuth2Info(), $redirectUri, $offline);

        // In a web application you would redirect the user to the authorization URL
        // and after approving the token they would be redirected back to the
        // redirect URL, with the URL parameter "code" added. For desktop
        // or server applications, spawn a browser to the URL and then have the user
        // enter the authorization code that is displayed.
        if(!$authCode){
            header("Location: ".$authorizationUrl); exit;
        }
        // Get the access token using the authorization code. Ensure you use the same
        // redirect URL used when requesting authorization.


        $user->SetOAuth2Info(
            $OAuth2Handler->GetAccessToken(
                $user->GetOAuth2Info(), $authCode, $redirectUri));


        // The access token expires but the refresh token obtained for offline use
        // doesn't, and should be stored for later use.
        return $user->GetOAuth2Info();
    }

    //returns the list of all campaigns contained into the account
    public function getCampaigns(AdWordsUser $user) {
        // Get the service, which loads the required classes.
        $campaignService = $user->GetService('CampaignService', ADWORDS_VERSION);

        // Create selector.
        $selector = new Selector();
        $selector->fields = array('Id', 'Name');
        $selector->ordering[] = new OrderBy('Name', 'ASCENDING');

        // Create paging controls.
        $selector->paging = new Paging(0, AdWordsConstants::RECOMMENDED_PAGE_SIZE);

        $compain_list=array();

        do {
            // Make the get request.
            $page = $campaignService->get($selector);

            // Display results.
            if (isset($page->entries)) {
                foreach ($page->entries as $campaign) {
                    $compain_list[$campaign->name]=$campaign->id;
                }
            } else {
                print "No campaigns were found.\n";
            }

            // Advance the paging index.
            $selector->paging->startIndex += AdWordsConstants::RECOMMENDED_PAGE_SIZE;
        } while ($page->totalNumEntries > $selector->paging->startIndex);

        return $compain_list;
    }

    public function createCampaign(AdWordsUser $user, $campaign_array) {
        // Get the BudgetService, which loads the required classes.

        $budgetService = $user->GetService('BudgetService', ADWORDS_VERSION);
        //  var_dump(222); exit;
        // Create the shared budget (required).
        $money = $campaign_array['budget']*1000000;
        $budget = new Budget();
        $budget->name = 'Budget_'.uniqid();
        $budget->period = 'DAILY';
        $budget->amount = new Money($money);
        $budget->deliveryMethod = 'STANDARD';

        $operations = array();

        // Create operation.
        $operation = new BudgetOperation();
        $operation->operand = $budget;
        $operation->operator = 'ADD';
        $operations[] = $operation;

        // Make the mutate request.
        try {
            $result = $budgetService->mutate($operations);
        } catch(Exception $e){
            var_dump($e->getMessage()); exit;
        }

        $budget = $result->value[0];

        // Get the CampaignService, which loads the required classes.
        $campaignService = $user->GetService('CampaignService', ADWORDS_VERSION);

        $operations = array();
        // Create campaign.

        //$campaignType = $campaign['campaign_type'];
        //var_dump(888); exit;
        $campaign = new Campaign(); //var_dump($campiagn); exit;
        $campaign->name = $campaign_array['io'].' #' . uniqid();
        $campaign->advertisingChannelType = 'DISPLAY'; // set comaign type SEARCH
        if($campaign_array['preferred_mobile'] && $campaign_array['device_type']=="mobile"){

            $campaign->advertisingChannelSubType = 'DISPLAY_MOBILE_APP';
        }


        // Set shared budget (required).
        $campaign->budget = new Budget();
        $campaign->budget->budgetId = $budget->budgetId;

        // Set bidding strategy (required).
        $biddingStrategyConfiguration = new BiddingStrategyConfiguration();
        $biddingStrategyConfiguration->biddingStrategyType = 'MANUAL_CPC';

        // You can optionally provide a bidding scheme in place of the type.
        $biddingScheme = new ManualCpcBiddingScheme();
        $biddingScheme->enhancedCpcEnabled = false;
        $biddingStrategyConfiguration->biddingScheme = $biddingScheme;

        $campaign->biddingStrategyConfiguration = $biddingStrategyConfiguration;

        // Set network targeting (optional).
        // now working with display only network by default
        $networkSetting = new NetworkSetting();
        $networkSetting->targetGoogleSearch = false;
        $networkSetting->targetSearchNetwork = false;
        $networkSetting->targetContentNetwork = true;
        $campaign->networkSetting = $networkSetting;

        // Set additional settings (optional).
        $startDate = date('Ymd', strtotime($campaign_array['campaign_start_datetime'])); //var_dump($startDate,strtotime($campaign_array['campaign_start_datetime'])); exit;
        //date('Ymd', $campaign['end_date']);

        $campaign->status = 'PAUSED';
        $campaign->startDate = $startDate;
        //$campaign->endDate = date('Ymd', strtotime('+1 month'));
        $campaign->adServingOptimizationStatus = 'ROTATE';

        // Set frequency cap (optional).
        $frequencyCap = new FrequencyCap();
        $frequencyCap->impressions = $campaign_array['total_opens'];
        $frequencyCap->timeUnit = 'DAY';
        $frequencyCap->level = 'ADGROUP';
        $campaign->frequencyCap = $frequencyCap;

        // Set advanced location targeting settings (optional).
        $geoTargetTypeSetting = new GeoTargetTypeSetting();
        $geoTargetTypeSetting->positiveGeoTargetType = 'DONT_CARE';
        $geoTargetTypeSetting->negativeGeoTargetType = 'DONT_CARE';
        $campaign->settings[] = $geoTargetTypeSetting;

        // Create operation.
        $operation = new CampaignOperation();
        $operation->operand = $campaign;
        $operation->operator = 'ADD';
        $operations[] = $operation;

        // Make the mutate request.
        try{
            $result = $campaignService->mutate($operations); //var_dump($result); exit;
        }catch(Exception $e) {
            echo $e->getMessage(); exit;
        }


        // Display results.
        foreach ($result->value as $campaign) {
            printf("Campaign with name '%s' and ID '%s' was added.\n", $campaign->name,
                $campaign->id);
        }

        // }catch(Exception $e) {
        //   echo $e->getMessage();
        // }

        //$campaignId = $result->value[0]->id;
        return $result->value[0];
    }

    function updateCampaignStatus(AdWordsUser $user, $campaignId, $status) {
        // Get the service, which loads the required classes.
        try {
            $campaignService = $user->GetService('CampaignService', ADWORDS_VERSION);

            // Create campaign using an existing ID.
            $campaign = new Campaign();
            $campaign->id = $campaignId;
            $campaign->status = $status;

            // Create operation.
            $operation = new CampaignOperation();
            $operation->operand = $campaign;
            $operation->operator = 'SET';

            $operations = array($operation);

            // Make the mutate request.
            $result = $campaignService->mutate($operations);
        } catch(Exception $e) {
            var_dump($e->getMessage()); exit;
        }
        // Display result.
        $campaign = $result->value[0];
        printf("Campaign with ID '%s' was '%s'.\n", $campaign->id, $status);
    }

    public function createLocationCriteria(AdWordsUser $user, $campaign_id, $criteria_id) {
        $campaignCriterionService = $user->GetService('CampaignCriterionService', ADWORDS_VERSION);

        // Create locations. The IDs can be found in the documentation or retrieved
        // with the LocationCriterionService.
        $location = new Location();
        $location->id = $criteria_id;
        $campaignCriteria = new CampaignCriterion($campaign_id, null, $location);

        // Create operations.
        $operations = array();
        $operations[] = new CampaignCriterionOperation($campaignCriteria, 'ADD');

        $result = $campaignCriterionService->mutate($operations);

        foreach ($result->value as $campaignCriterion) {
            printf("Campaign targeting criterion with ID '%s' and type '%s' was "
                . "added.\n", $campaignCriterion->criterion->id,
                $campaignCriterion->criterion->CriterionType);
        }
    }

    public function createProximityCriteria(AdWordsUser $user, $campaign_id, $postalCode, $radius)
    {
        try {
            $geoLocationService = $user->GetService('GeoLocationService', ADWORDS_VERSION);
            // Lookup the geo point
            $address = new Address();
            $address->postalCode = $postalCode;

            // Create geo location selector.
            $selector = new GeoLocationSelector();
            $selector->addresses = array($address);

            //Get geo location.
            $geoLocationResult = $geoLocationService->get($selector);
            $geoPoint = $geoLocationResult[0]->geoPoint;

            $campaignCriterionService = $user->GetService('CampaignCriterionService', ADWORDS_VERSION);

            $proximity = new Proximity();
            $proximity->geoPoint = $geoPoint;
            $proximity->radiusDistanceUnits = 'MILES';
            $proximity->radiusInUnits = $radius;

            $campaignCriteria = new CampaignCriterion($campaign_id, null, $proximity);

            // Create operations.
            $operations = array();
            $operations[] = new CampaignCriterionOperation($campaignCriteria, 'ADD');

            $result = $campaignCriterionService->mutate($operations);
        } catch(Exception $e) {
            var_dump($e->getMessage()); exit;
        }
        foreach ($result->value as $campaignCriterion) {
            printf("Campaign targeting criterion with ID '%s' and type '%s' was "
                . "added.\n", $campaignCriterion->criterion->id,
                $campaignCriterion->criterion->CriterionType);
        }
        return $result->value[0]->criterion->id;

    }

    public function createCarrierCriteria(AdWordsUser $user, $campaign_id, $criteria_id) {

        $campaignCriterionService = $user->GetService('CampaignCriterionService', ADWORDS_VERSION);

        // Create locations. The IDs can be found in the documentation or retrieved
        // with the LocationCriterionService.
        $carrier = new Carrier();
        $carrier->id = $criteria_id;
        $campaignCriteria = new CampaignCriterion($campaign_id, null, $carrier);

        // Create operations.
        $operations = array();
        $operations[] = new CampaignCriterionOperation($campaignCriteria, 'ADD');

        $result = $campaignCriterionService->mutate($operations);

        foreach ($result->value as $campaignCriterion) {
            printf("Campaign targeting criterion with ID '%s' and type '%s' was "
                . "added.\n", $campaignCriterion->criterion->id,
                $campaignCriterion->criterion->CriterionType);
        }
    }

    public function createLanguageCriteria(AdWordsUser $user, $campaign_id, $criteria_id) {

        $campaignCriterionService = $user->GetService('CampaignCriterionService', ADWORDS_VERSION);

        // Create locations. The IDs can be found in the documentation or retrieved
        // with the LocationCriterionService.
        $language = new Language();
        $language->id = $criteria_id;
        $campaignCriteria = new CampaignCriterion($campaign_id, null, $language);

        // Create operations.
        $operations = array();
        $operations[] = new CampaignCriterionOperation($campaignCriteria, 'ADD');

        $result = $campaignCriterionService->mutate($operations);

        foreach ($result->value as $campaignCriterion) {
            printf("Campaign targeting criterion with ID '%s' and type '%s' was "
                . "added.\n", $campaignCriterion->criterion->id,
                $campaignCriterion->criterion->CriterionType);
        }
    }

    public function createPlatformCriteria(AdWordsUser $user, $campaign_id, $criteria_id) {
        try { //var_dump(8777); exit;
            $campaignCriterionService = $user->GetService('CampaignCriterionService', ADWORDS_VERSION);

            // Create locations. The IDs can be found in the documentation or retrieved
            // with the LocationCriterionService.
            $platform = new Platform();
            //$platform->name = 'mobile';
            $platform->id = $criteria_id;
            $campaignCriteria = new CampaignCriterion($campaign_id, null, $platform);
            //var_dump($campaignCriteria);
            // Create operations.
            $operations = array();
            $operations[] = new CampaignCriterionOperation($campaignCriteria, 'REMOVE');

            $result = $campaignCriterionService->mutate($operations);
        } catch(Exception $e){
            var_dump($e->getMessage()); exit;
        }
        foreach ($result->value as $campaignCriterion) {
            printf("Campaign targeting criterion with ID '%s' and type '%s' was "
                . "added.\n", $campaignCriterion->criterion->id,
                $campaignCriterion->criterion->CriterionType);
        }
        //   $campaignCriterionService =
        //     $user->GetService('CampaignCriterionService', ADWORDS_VERSION);

        // // Create selector.
        // $selector = new Selector();
        // $selector->fields = array('Id', 'CriteriaType');

        // // Create predicates.
        // $selector->predicates[] =
        //     new Predicate('CampaignId', 'IN', array($campaign_id));
        // $selector->predicates[] = new Predicate('CriteriaType', 'IN',
        //     array('LANGUAGE', 'LOCATION', 'AGE_RANGE', 'CARRIER',
        //         'OPERATING_SYSTEM_VERSION', 'GENDER', 'PROXIMITY', 'PLATFORM'));

        // // Create paging controls.
        // $selector->paging = new Paging(0, AdWordsConstants::RECOMMENDED_PAGE_SIZE);

        // do {
        //   // Make the get request.
        //   $page = $campaignCriterionService->get($selector);

        //   // Display results.
        //   if (isset($page->entries)) {
        //     foreach ($page->entries as $campaignCriterion) {
        //       printf("Campaign targeting criterion with ID '%s' and type '%s' was "
        //           . "found.\n", $campaignCriterion->criterion->id,
        //           $campaignCriterion->criterion->CriterionType);
        //     }
        //   } else {
        //     print "No campaign targeting criteria were found.\n";
        //   }

        //   // Advance the paging index.
        //   $selector->paging->startIndex += AdWordsConstants::RECOMMENDED_PAGE_SIZE;
        // } while ($page->totalNumEntries > $selector->paging->startIndex);
    }


    public function createOsVersionCriteria(AdWordsUser $user, $campaign_id, $criteria_id) {

        $campaignCriterionService = $user->GetService('CampaignCriterionService', ADWORDS_VERSION);

        // Create locations. The IDs can be found in the documentation or retrieved
        // with the LocationCriterionService.
        $operatingSystemVersion = new OperatingSystemVersion();
        $operatingSystemVersion->id = $criteria_id;
        $campaignCriteria = new CampaignCriterion($campaign_id, null, $operatingSystemVersion);

        // Create operations.
        $operations = array();
        $operations[] = new CampaignCriterionOperation($campaignCriteria, 'ADD');

        $result = $campaignCriterionService->mutate($operations);

        foreach ($result->value as $campaignCriterion) {
            printf("Campaign targeting criterion with ID '%s' and type '%s' was "
                . "added.\n", $campaignCriterion->criterion->id,
                $campaignCriterion->criterion->CriterionType);
        }
    }

    //add the group into the campaign with id defined by the $campaignId
    public function createGroup(AdWordsUser $user, $campaignId, $groupName, $status='ENABLED') {
        // Get the service, which loads the required classes.
        $adGroupService = $user->GetService('AdGroupService', ADWORDS_VERSION);

        $operations = array();

        // Create ad group.
        $adGroup = new AdGroup();
        $adGroup->campaignId = $campaignId;
        $adGroup->name = $groupName . uniqid();

        //    // Set bids (required).
        //    $bid = new CpcBid();
        //    $bid->bid =  new Money(1000000);
        //    $biddingStrategyConfiguration = new BiddingStrategyConfiguration();
        //    $biddingStrategyConfiguration->bids[] = $bid;
        //    $adGroup->biddingStrategyConfiguration = $biddingStrategyConfiguration;

        // Set additional settings (optional).
        $adGroup->status = $status;

        // Targeting restriction settings - these settings only affect serving
        // for the Display Network.
        $targetingSetting = new TargetingSetting();
        // Restricting to serve ads that match your ad group placements.
        // This is equivalent to choosing "Target and bid" in the UI.
        $targetingSetting->details[] =
            new TargetingSettingDetail('PLACEMENT', FALSE);
        // Using your ad group verticals only for bidding. This is equivalent
        // to choosing "Bid only" in the UI.
        $targetingSetting->details[] =
            new TargetingSettingDetail('VERTICAL', TRUE);
        $adGroup->settings[] = $targetingSetting;

        // Create operation.
        $operation = new AdGroupOperation();
        $operation->operand = $adGroup;
        $operation->operator = 'ADD';
        $operations[] = $operation;


        // Make the mutate request.
        $result = $adGroupService->mutate($operations);

        // Display result.
        foreach ($result->value as $group_array) {
            printf("group with name '%s' and ID '%s' was added.\n", $group_array->name,
                $group_array->id);
        }
        $group = $result->value[0];

        return $group;
    }

    //removes the group from the campaign with id defined by the $campaignId
    public function removeGroup(AdWordsUser $user, $groupId) {
        // Get the service, which loads the required classes.
        $adGroupService = $user->GetService('AdGroupService', ADWORDS_VERSION);

        // Create ad group with REMOVED status.
        $adGroup = new AdGroup();
        $adGroup->id = $groupId;
        $adGroup->status = 'REMOVED';

        // Create operations.
        $operation = new AdGroupOperation();
        $operation->operand = $adGroup;
        $operation->operator = 'SET';

        $operations = array($operation);

        // Make the mutate request.
        $result = $adGroupService->mutate($operations);

        // Display result.
        $adGroup = $result->value[0];
    }

    //returns the list of groups contained into the campaign in the form of the associative array in the form of group_name=>group_id
    public function getGroups(AdWordsUser $user, $campaignId) {
        // Get the service, which loads the required classes.
        $adGroupService = $user->GetService('AdGroupService', ADWORDS_VERSION);

        // Create selector.
        $selector = new Selector();
        $selector->fields = array('Id', 'Name');
        $selector->ordering[] = new OrderBy('Name', 'ASCENDING');

        // Create predicates.
        $selector->predicates[] =
            new Predicate('CampaignId', 'IN', array($campaignId));

        // Create paging controls.
        $selector->paging = new Paging(0, AdWordsConstants::RECOMMENDED_PAGE_SIZE);

        $group_list=array();

        do {
            // Make the get request.
            $page = $adGroupService->get($selector);


            // Display results.
            if (isset($page->entries)) {
                foreach ($page->entries as $adGroup) {
                    $group_list[$adGroup->name]=$adGroup->id;
                }
            } else {
                print "No ad groups were found.\n";
            }

            // Advance the paging index.
            $selector->paging->startIndex += AdWordsConstants::RECOMMENDED_PAGE_SIZE;
        } while ($page->totalNumEntries > $selector->paging->startIndex);

        return $group_list;
    }

    //ads an image ad into the group defined by the argument $adGroupId
    public function createImageAd(AdWordsUser $user, $adGroupId, $ad) {
        // Get the service, which loads the required classes.
        try{
            $adGroupAdService = $user->GetService('AdGroupAdService', ADWORDS_VERSION);

            $operations = array();

            // Create image ad.
            $imageAd = new ImageAd();
            $imageAd->name = $ad['creative_name'];
            $imageAd->displayUrl = $ad['display_url'];
            if(isset($ad['destination_url'])){
                $imageAd->finalUrls = $ad['destination_url'];
            } else {
                $imageAd->finalUrls = $ad['display_url'];
            }

            // Create image.
            $image = new Image();
            $image->data = MediaUtils::GetBase64Data($ad['creative_url']);
            $imageAd->image = $image;
            //var_dump(444); exit;
            // Create ad group ad.
            $adGroupAd = new AdGroupAd();
            $adGroupAd->adGroupId = $adGroupId;
            $adGroupAd->ad = $imageAd;

            // Set additional settings (optional).
            $adGroupAd->status = $ad['creative_status'];

            $operation = new AdGroupAdOperation();
            $operation->operand = $adGroupAd;
            $operation->operator = 'ADD';
            $operations[] = $operation;

            // Make the mutate request.

            $result = $adGroupAdService->mutate($operations);
        } catch(Exception $e) {
            var_dump($e->getMessage()); exit;
        }

        // Display results.
        printf("ADD with ID '%s' was added.\n", $result->value[0]->ad->id);

        return $result->value[0];
    }

//    public function createImageAdWithDisplayUrl(AdWordsUser $user, $adGroupId, $adImage, $status, $destination_url, $displayUrl) {
//        // Get the service, which loads the required classes.
//        $adGroupAdService = $user->GetService('AdGroupAdService', ADWORDS_VERSION);
//
//        $operations = array();
//
//        // Create image ad.
//        $imageAd = new ImageAd();
//        $imageAd->name = 'Template image ad #' . time();
//        $imageAd->displayUrl = $displayUrl;
//        $imageAd->finalUrls=$destination_url;
//        // Create image.
//        $image = new Image();
//        $image->data = MediaUtils::GetBase64Data($adImage);
//        $imageAd->image = $image;
//
//        // Create ad group ad.
//        $adGroupAd = new AdGroupAd();
//        $adGroupAd->adGroupId = $adGroupId;
//        $adGroupAd->ad = $imageAd;
//
//        // Set additional settings (optional).
//        $adGroupAd->status = $status;
//
//        $operation = new AdGroupAdOperation();
//        $operation->operand = $adGroupAd;
//        $operation->operator = 'ADD';
//        $operations[] = $operation;
//
//        // Make the mutate request.
//
//        $result = $adGroupAdService->mutate($operations);
//
//        $ad=array();
//        // Display results.
//
//        foreach ($result->value as $adGroupAd) {
//            $ad["ad_id"]=$adGroupAd->ad->id;
//            $ad["display_url"]=$adGroupAd->ad->displayUrl;
//            $ad["ad_status"]=$adGroupAd->status;
//            $ad["approval_status"]=$adGroupAd->approvalStatus;
//            $ad["disapproval_reasons"]=$adGroupAd->disapprovalReasons;
//            $ad["adword_group_id"]=$adGroupAd->adGroupId;
//            $ad["ad_name"]=$imageAd->name ;
//        }
//
//        return $ad;
//    }

    // add demographics targeting into the group defined by the $adGroupId
    public function createGenderCriteria(AdWordsUser $user, $adGroupId, $genderId) {
        // Get the service, which loads the required classes.
        try{
            $adGroupCriterionService = $user->GetService('AdGroupCriterionService', ADWORDS_VERSION);

            // Create biddable ad group criterion for gender
            $gender = new Gender();
            // Criterion Id for male. The IDs can be found here
            // https://developers.google.com/adwords/api/docs/appendix/genders
            $gender->id = $genderId;

            $genderBiddableAdGroupCriterion = new BiddableAdGroupCriterion();
            $genderBiddableAdGroupCriterion->adGroupId = $adGroupId;
            $genderBiddableAdGroupCriterion->criterion = $gender;


            $operations = array();

            // Create operations.
            $genderBiddableAdGroupCriterionOperation = new AdGroupCriterionOperation();
            $genderBiddableAdGroupCriterionOperation->operand = $genderBiddableAdGroupCriterion;
            $genderBiddableAdGroupCriterionOperation->operator = 'ADD';
            $operations[] = $genderBiddableAdGroupCriterionOperation;

            $result = $adGroupCriterionService->mutate($operations);
        } catch(Exception $e){
            var_dump($e->getMessage()); exit;
        }
        foreach ($result->value as $campaignCriterion) {
            printf("Campaign targeting criterion with ID '%s' and type '%s' was "
                . "added.\n", $campaignCriterion->criterion->id,
                $campaignCriterion->criterion->CriterionType);
        }

        $demographics=array();
        // Display results.
        // foreach ($result->value as $adGroupCriterion) {
        //   $demographics["id"]=$adGroupCriterion->criterion->id;
        // }

        // $criterion["group_id"]=$adGroupId;
        // $criterion["user_list_id"]=$userListId;

        return $result->value[0];
    }


    public function createAgeCriteria(AdWordsUser $user, $adGroupId, $ageId) {
        // Get the service, which loads the required classes.
        $adGroupCriterionService = $user->GetService('AdGroupCriterionService', ADWORDS_VERSION);

        // Create biddable ad group criterion for gender
        $age = new Age();
        // Criterion Id for male. The IDs can be found here
        // https://developers.google.com/adwords/api/docs/appendix/genders
        $age->id = 10;

        $ageBiddableAdGroupCriterion = new BiddableAdGroupCriterion();
        $ageBiddableAdGroupCriterion->adGroupId = $adGroupId;
        $ageBiddableAdGroupCriterion->criterion = $age;


        $operations = array();

        // Create operations.
        $ageBiddableAdGroupCriterionOperation = new AdGroupCriterionOperation();
        $ageBiddableAdGroupCriterionOperation->operand = $ageBiddableAdGroupCriterion;
        $ageBiddableAdGroupCriterionOperation->operator = 'ADD';
        $operations[] = $ageBiddableAdGroupCriterionOperation;

        $result = $adGroupCriterionService->mutate($operations);

        $demographics=array();
        // Display results.
        // foreach ($result->value as $adGroupCriterion) {
        //   $demographics["id"]=$adGroupCriterion->criterion->id;
        // }

        // $criterion["group_id"]=$adGroupId;
        // $criterion["user_list_id"]=$userListId;

        return $result->value;
    }

    // add demographics targeting into the group defined by the $adGroupId
    public function createDemographicsTargeting(AdWordsUser $user, $adGroupId) {
        // Get the service, which loads the required classes.
        $adGroupCriterionService = $user->GetService('AdGroupCriterionService', ADWORDS_VERSION);

        // Create biddable ad group criterion for gender
        $genderTargetMale = new Gender();
        // Criterion Id for male. The IDs can be found here
        // https://developers.google.com/adwords/api/docs/appendix/genders
        $genderTargetMale->id = 10;

        $genderTargetFemale = new Gender();
        // Criterion Id for male. The IDs can be found here
        // https://developers.google.com/adwords/api/docs/appendix/genders
        $genderTargetFemale->id = 11;

        $genderBiddableAdGroupCriterionFemale = new BiddableAdGroupCriterion();
        $genderBiddableAdGroupCriterionFemale->adGroupId = $adGroupId;
        $genderBiddableAdGroupCriterionFemale->criterion = $genderTargetFemale;

        $genderBiddableAdGroupCriterionMale = new BiddableAdGroupCriterion();
        $genderBiddableAdGroupCriterionMale->adGroupId = $adGroupId;
        $genderBiddableAdGroupCriterionMale->criterion = $genderTargetMale;


        $operations = array();

        // Create operations.
        $femaleGenderBiddableAdGroupCriterionOperation = new AdGroupCriterionOperation();
        $femaleGenderBiddableAdGroupCriterionOperation->operand = $genderBiddableAdGroupCriterionFemale;
        $femaleGenderBiddableAdGroupCriterionOperation->operator = 'ADD';
        $operations[] = $femaleGenderBiddableAdGroupCriterionOperation;

        $maleGenderBiddableAdGroupCriterionOperation = new AdGroupCriterionOperation();
        $maleGenderBiddableAdGroupCriterionOperation->operand = $genderBiddableAdGroupCriterionMale;
        $maleGenderBiddableAdGroupCriterionOperation->operator = 'ADD';
        $operations[] = $maleGenderBiddableAdGroupCriterionOperation;

        $result = $adGroupCriterionService->mutate($operations);


        $demographics=array();
        // Display results.
        // foreach ($result->value as $adGroupCriterion) {
        //   $demographics["id"]=$adGroupCriterion->criterion->id;
        // }

        // $criterion["group_id"]=$adGroupId;
        // $criterion["user_list_id"]=$userListId;

        return $result->value;
    }

    //adds the audience defined by the $userListId into the group defined by the $adGroupId
    public function createCriterion(AdWordsUser $user, $adGroupId, $userListId) {
        // Get the service, which loads the required classes.
        try {
            $adGroupCriterionService = $user->GetService('AdGroupCriterionService', ADWORDS_VERSION);

            $operations = array();

            $criterion = new CriterionUserList();
            //$criterion->id= 2;
            $criterion->userListId = $userListId;
            $criterion->userListMembershipStatus='OPEN';
            $criterion->userListName="Criterion_Harut";

            // Create biddable ad group criterion.
            $adGroupCriterion = new BiddableAdGroupCriterion();
            $adGroupCriterion->adGroupId = $adGroupId;
            $adGroupCriterion->criterion = $criterion;

            // Set additional settings (optional).
            $adGroupCriterion->userStatus = 'PAUSED';
            //   $adGroupCriterion->destinationUrl = 'http://www.example.com/mars';

            // Set bids (optional).
            /*  $bid = new CpcBid();
              $bid->bid =  new Money($cpc);
              /*
              $cpm_bid=new CpmBid();
              $cpm_bid->bid=new Money($cpm);
               *
               */
            //    $biddingStrategyConfiguration = new BiddingStrategyConfiguration();
            //    $biddingStrategyConfiguration->biddingStrategyType = "TARGET_CPA";
            //    $biddingStrategyConfiguration->bids[] = $bid;
            //   $biddingStrategyConfiguration->bids[] = $cpm_bid;
            //    $adGroupCriterion->biddingStrategyConfiguration = $biddingStrategyConfiguration;

            $adGroupCriteria[] = $adGroupCriterion;

            // Create operation.
            $operation = new AdGroupCriterionOperation();
            $operation->operand = $adGroupCriterion;
            $operation->operator = 'ADD';
            $operations[] = $operation;

            // Make the mutate request.
            $result = $adGroupCriterionService->mutate($operations);
        } catch(Exception $e) {
            var_dump($e->getMessage()); exit;
        }
        $criterion=array();
        // Display results.
        foreach ($result->value as $adGroupCriterion) {
            $criterion["id"]=$adGroupCriterion->criterion->id;
            printf("group targeting criterion with ID '%s' and type '%s' was "
                . "added.\n", $adGroupCriterion->criterion->id,
                $adGroupCriterion->criterion->CriterionType);

        }

        $criterion["group_id"]=$adGroupId;
        $criterion["user_list_id"]=$userListId;

        return $result->value[0]->criterion->id;
    }

    //removes the criterion (object which connects the audience with the ad group) from the group defined by the argument $adGroupId
    public function removeCriterion(AdWordsUser $user, $adGroupId, $criterionId) {
        // Get the service, which loads the required classes.
        $adGroupCriterionService =
            $user->GetService('AdGroupCriterionService', ADWORDS_VERSION);

        // Create criterion using an existing ID. Use the base class Criterion
        // instead of Keyword to avoid having to set keyword-specific fields.
        $criterion = new Criterion();
        $criterion->id = $criterionId;
        //$criterion->userListId = 75657505;

        // Create ad group criterion.
        $adGroupCriterion = new AdGroupCriterion();
        $adGroupCriterion->adGroupId = $adGroupId;
        $adGroupCriterion->criterion = new Criterion($criterionId);

        // Create operation.
        $operation = new AdGroupCriterionOperation();
        $operation->operand = $adGroupCriterion;
        $operation->operator = 'REMOVE';

        $operations = array($operation);

        // Make the mutate request.
        $result = $adGroupCriterionService->mutate($operations);

        // Display result.
        $adGroupCriterion = $result->value[0];

    }
    //returns the performance list of ad on xml format, which contains the id, number of clicks during the last hour
    // and the number of impressions during the last hour
    public function getAdPerformanceReport(AdWordsUser $user, $filePath, $reportFormat) {
        // Prepare a date range for the last week. Instead you can use 'LAST_7_DAYS'.
        $dateRange = sprintf('%d,%d',
            date('Ymd', strtotime('-1 month')), date('Ymd', strtotime('now')));

        // Create report query.
        $reportQuery = 'SELECT Id, Clicks, Impressions, Cost FROM AD_PERFORMANCE_REPORT '
            . 'WHERE Status IN [ENABLED, PAUSED] DURING ' . $dateRange;

        // Set additional options.
        $options = array('version' => ADWORDS_VERSION);

        // Download report.
        $result=ReportUtils::DownloadReportWithAwql($reportQuery, $filePath, $user, $reportFormat, $options);

        return $result;
    }

    //http://report-site.com
    //returns the performance list of ad on xml format, which contains the id, number of clicks during the last hour
    // and the number of impressions during the last hour
    public function DownloadAdGroupPerformanceReport(AdWordsUser $user, $filePath, $reportFormat, $month_amount) {
        // Prepare a date range for the last week. Instead you can use 'LAST_7_DAYS'.
        $dateRange = sprintf('%d,%d',
            date('Ymd', strtotime('-1 hours')), date('Ymd', strtotime('now')));

        // Create report query.
        $reportQuery = 'SELECT AdGroupId, Clicks, Impressions, TotalCost FROM ADGROUP_PERFORMANCE_REPORT '
            . 'WHERE AdGroupStatus IN [ENABLED, PAUSED] DURING ' . $dateRange;

        // Set additional options.
        $options = array('version' => ADWORDS_VERSION);

        // Download report.
        $result=ReportUtils::DownloadReportWithAwql($reportQuery, $filePath, $user, $reportFormat, $options);

        return $result;
    }

    //removes the specified ad from the account
    //returns the Id of deleted ad
    public function removeImageAds(AdWordsUser $user, $adGroupId, $adImageId, $adName) {
        // Get the service, which loads the required classes.
        $adGroupAdService = $user->GetService('AdGroupAdService', ADWORDS_VERSION);

        $operations = array();

        // Create image ad.
        $imageAd = new ImageAd();
        $imageAd->id = $adImageId;
        $imageAd->name = $adName;


        // Create ad group ad.
        $adGroupAd = new AdGroupAd();
        $adGroupAd->adGroupId = $adGroupId;
        $adGroupAd->ad = $imageAd;

        $operation = new AdGroupAdOperation();
        $operation->operand = $adGroupAd;
        $operation->operator = 'REMOVE';
        $operations[] = $operation;

        // Make the mutate request.
        $result = $adGroupAdService->mutate($operations);

        return $adImageId;
    }

    //changes the ad status to the one defined by the $adStatus argument
    public function updateAdStatus(AdWordsUser $user,$adGroupId, $adName, $adId, $adStatus) {
        // Get the service, which loads the required classes.
        $adGroupAdService = $user->GetService('AdGroupAdService', ADWORDS_VERSION);

        $operations = array();

        // Create image ad.
        $imageAd = new ImageAd();
        $imageAd->id=$adId;
        $imageAd->name = $adName;

        // Create ad group ad.
        $adGroupAd = new AdGroupAd();
        $adGroupAd->adGroupId = $adGroupId;
        $adGroupAd->ad = $imageAd;

        // Set additional settings (optional).
        $adGroupAd->status = $adStatus;

        $operation = new AdGroupAdOperation();
        $operation->operand = $adGroupAd;
        $operation->operator = 'SET';
        $operations[] = $operation;

        // Make the mutate request.
        $result = $adGroupAdService->mutate($operations);

        return $result;
    }

    public function updateGroupStatus(AdWordsUser $user, $adGroupId, $status){
        $adGroupService = $user->GetService('AdGroupService', ADWORDS_VERSION);

        // Create ad group using an existing ID.
        $adGroup = new AdGroup();
        $adGroup->id = $adGroupId;
        $adGroup->status=$status;
        // Update the bid.
        //        $bid = new CpmBid();
        //        $bid->bid =  new Money(0.75 * AdWordsConstants::MICROS_PER_DOLLAR);
        //        $biddingStrategyConfiguration = new BiddingStrategyConfiguration();
        //        $biddingStrategyConfiguration->bids[] = $bid;
        //        $adGroup->biddingStrategyConfiguration = $biddingStrategyConfiguration;

        // Create operation.
        $operation = new AdGroupOperation();
        $operation->operand = $adGroup;
        $operation->operator = 'SET';

        $operations = array($operation);

        // Make the mutate request.
        $result = $adGroupService->mutate($operations);

        // Display result.
        $adGroup = $result->value[0];

    }

    //creates a text ad into the group with id defined by $adGroupId
    public function createTextAds(AdWordsUser $user, $adGroupId, $ad) {
        // Get the service, which loads the required classes.
        try{
            $adGroupAdService = $user->GetService('AdGroupAdService', ADWORDS_VERSION);

            $numAds = 1;
            $operations = array();
            for ($i = 0; $i < $numAds; $i++) {
                // Create text ad.
                $textAd = new TextAd();
                $textAd->headline = $ad['title'];
                $textAd->description1 = $ad['description_1'];
                $textAd->description2 = $ad['description_2'];
                $textAd->displayUrl = $ad['display_url'];
                if(isset($ad['destination_url'])) {
                    $textAd->finalUrls = array($ad['destination_url']);
                } else {
                    $textAd->finalUrls = array($ad['display_url']);
                }
                // Create ad group ad.
                $adGroupAd = new AdGroupAd();
                $adGroupAd->adGroupId = $adGroupId;
                $adGroupAd->ad = $textAd;

                // Set additional settings (optional).
                $adGroupAd->status = $ad['creative_status'];

                // Create operation.
                $operation = new AdGroupAdOperation();
                $operation->operand = $adGroupAd;
                $operation->operator = 'ADD';
                $operations[] = $operation;
            }

            // Make the mutate request.
            $result = $adGroupAdService->mutate($operations);
        } catch(Exception $e){
            var_dump($e->getMessage()); exit;
        }
        return $result->value[0];

        // $text_ad=array();
        // $text_ad["adGroupId"]=$result->value[0]->adGroupId;
        // $text_ad["headline"]=$result->value[0]->ad->headline;
        // $text_ad["description1"]=$result->value[0]->ad->description1;
        // $text_ad["description2"]=$result->value[0]->ad->description2;
        // $text_ad["id"]=$result->value[0]->ad->id;
        // $text_ad["status"]=$result->value[0]->status;
        // $text_ad["approval_status"]=$result->value[0]->approvalStatus;
        // $text_ad["disapproval_reasons"]=$result->value[0]->disapprovalReasons;

        // return $text_ad;
    }

    public function removeTextAd(AdWordsUser $user, $adGroupId, $textAdId, $headline) {
        // Get the service, which loads the required classes.
        $adGroupAdService = $user->GetService('AdGroupAdService', ADWORDS_VERSION);

        $operations = array();

        // Create image ad.
        $textAd = new TextAd();
        $textAd->id = $textAdId;
        $textAd->headline = $headline;

        // Create ad group ad.
        $adGroupAd = new AdGroupAd();
        $adGroupAd->adGroupId = $adGroupId;
        $adGroupAd->ad =  $textAd;

        $operation = new AdGroupAdOperation();
        $operation->operand = $adGroupAd;
        $operation->operator = 'REMOVE';
        $operations[] = $operation;

        // Make the mutate request.
        $result = $adGroupAdService->mutate($operations);

        return $textAdId;
    }

    //changes the ad status to the one defined by the $adStatus argument
    public function UpdateTextAdStatus(AdWordsUser $user, $adGroupId, $adId, $adStatus, $headline) {
        // Get the service, which loads the required classes.
        $adGroupAdService = $user->GetService('AdGroupAdService', ADWORDS_VERSION);

        $operations = array();

        // Create image ad.
        $imageAd = new TextAd();
        $imageAd->id=$adId;
        $imageAd->headline = $headline;

        // Create ad group ad.
        $adGroupAd = new AdGroupAd();
        $adGroupAd->adGroupId = $adGroupId;
        $adGroupAd->ad = $imageAd;

        // Set additional settings (optional).
        $adGroupAd->status = $adStatus;

        $operation = new AdGroupAdOperation();
        $operation->operand = $adGroupAd;
        $operation->operator = 'SET';
        $operations[] = $operation;

        // Make the mutate request.
        $result = $adGroupAdService->mutate($operations);
    }

    //returns the performance list of ad on xml format, which contains the id, number of clicks during the last hour
    // and the number of impressions during the last hour
    public function getAdApprovalReport(AdWordsUser $user, $filePath, $reportFormat) {
        // Prepare a date range for the last week. Instead you can use 'LAST_7_DAYS'.
        $dateRange = sprintf('%d,%d',
            date('Ymd', strtotime('-1 month')), date('Ymd', strtotime('now')));

        // Create report query.
        $reportQuery = 'SELECT Id, AdGroupAdDisapprovalReasons, CreativeApprovalStatus FROM AD_PERFORMANCE_REPORT '
            . 'WHERE Status IN [ENABLED, PAUSED] DURING ' . $dateRange;

        // Set additional options.
        $options = array('version' => ADWORDS_VERSION);

        // Download report.
        $result=ReportUtils::DownloadReportWithAwql($reportQuery, $filePath, $user, $reportFormat, $options);

        return $result;
    }

    public function getLocationReport(AdWordsUser $user, $filePath, $reportFormat) {
        // Prepare a date range for the last week. Instead you can use 'LAST_7_DAYS'.
        $dateRange = sprintf('%d,%d', date('Ymd', strtotime('-1 month')), date('Ymd', strtotime('now')));

        // Create report query.
        $reportQuery = 'SELECT CountryCriteriaId, Clicks FROM GEO_PERFORMANCE_REPORT WHERE CountryCriteriaId = "2840" ';
        //WHERE CountryCriteriaId = 2840
        // Set additional options.
        $options = array('version' => ADWORDS_VERSION);

        // Download report.
        try{
            $result=ReportUtils::DownloadReportWithAwql($reportQuery, $filePath, $user, $reportFormat, $options);
        } catch (Exception $e) {
            printf("An error has occurred: %s\n", $e->getMessage());
        }
        return $result;
    }

    //returns the performance list of ad on xml format, which contains the id, number of clicks during the last hour
    // and the number of impressions during the last hour
    public function getAudiencePerformanceReport(AdWordsUser $user, $filePath, $reportFormat) {


        // Prepare a date range for the last week. Instead you can use 'LAST_7_DAYS'.
        $dateRange = sprintf('%d,%d',
            date('Ymd', strtotime('-1 month')), date('Ymd', strtotime('now')));

        // Create report query.
        //  $reportQuery = "SELECT Clicks FROM USER_AD_DISTANCE_REPORT WHERE DistanceBucket='DISTANCE_BUCKET_WITHIN_40MILES'";
        $reportQuery = "SELECT CountryCriteriaId, Impressions, CityCriteriaId, Clicks, AdGroupName, AdGroupId, RegionCriteriaId FROM GEO_PERFORMANCE_REPORT WHERE AdGroupId=20592775705";
        //  $reportQuery = "SELECT Id, Clicks, AdGroupId FROM AUDIENCE_PERFORMANCE_REPORT WHERE Id=141996322825";


        // Set additional options.
        $options = array('version' => ADWORDS_VERSION);

        // Download report.
        $result=ReportUtils::DownloadReportWithAwql($reportQuery, $filePath, $user,
            $reportFormat, $options);


        $result=simplexml_load_string($result);

        echo "<pre />";
        var_dump($result); exit;
        return $result;
    }
}


