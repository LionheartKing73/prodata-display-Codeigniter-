<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
define('SRC_PATH', APPPATH.'third_party/Adwords/src/');
define('LIB_PATH', 'Google/Api/Ads/AdWords/Lib');
define('UTIL_PATH', 'Google/Api/Ads/Common/Util');
define('AW_UTIL_PATH', 'Google/Api/Ads/AdWords/Util');
define('DFP_PATH', 'Google/Api/Ads/Dfp/Lib');
define('DFP_UTIL_PATH', 'Google/Api/Ads/Dfp/Util');
define('THIRD_PARTY', APPPATH.'third_party/');

define('MAIN_LIB', 'Google/Api/Ads/AdWords/v201702');

define('ADWORDS_VERSION', 'v201702');

// Configure include path
ini_set('include_path', implode(array(
        ini_get('include_path'), PATH_SEPARATOR, SRC_PATH))
);

// Include the AdWordsUser file
require_once SRC_PATH.LIB_PATH. '/AdWordsUser.php';
require_once SRC_PATH.MAIN_LIB. '/AdwordsUserListService.php';
require_once SRC_PATH.LIB_PATH. '/AdWordsSoapClient.php';
require_once SRC_PATH.UTIL_PATH. '/MediaUtils.php';
require_once SRC_PATH.AW_UTIL_PATH. '/ReportUtils.php';
require_once SRC_PATH.UTIL_PATH. '/ErrorUtils.php';
require_once SRC_PATH.DFP_PATH. '/DfpUser.php';
//require_once THIRD_PARTY . 'imageResizer/classSimpleImage.php';


class Adwords extends AdWordsUser {
    public function __construct() {
        parent::__construct();
    }

    private $error_messages = array(
                'INVALID_PROXIMITY_ADDRESS' => 'One or more postal codes entered are invalid. Please review and correct.',
                'CANNOT_SET_DATE_TO_PAST' => 'The campaign start date must be in the future and cannot be in the past.',
                'TOO_LOW' => 'Minimum value error.',
                'TOO_BIG' => 'Maximum value error.',
                'RATE_EXCEEDED' => 'We currently are experiencing an outage connecting to the network API. We will automatically retry in 60-minutes to resubmit the campaign changes.',
                '' => 'Undefined error occured',
            );
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
        try {
            $response = array('message'=>'','result'=>'');
            $budgetService = $user->GetService('BudgetService', ADWORDS_VERSION);
            //  var_dump(222); exit;
            // Create the shared budget (required).
            $money = $campaign_array['budget']*1000000;
            $budget = new Budget();
            $budget->name = 'Budget_'.uniqid();
            $budget->period = 'DAILY';
            $budget->isExplicitlyShared = false;
            $budget->amount = new Money($money);
            $budget->deliveryMethod = 'STANDARD';

            $operations = array();

            // Create operation.
            $operation = new BudgetOperation();
            $operation->operand = $budget;
            $operation->operator = 'ADD';
            $operations[] = $operation;

            // Make the mutate request.

            $result = $budgetService->mutate($operations);


            $budget = $result->value[0];
            // Get the CampaignService, which loads the required classes.
            $campaignService = $user->GetService('CampaignService', ADWORDS_VERSION);

            $operations = array();
            // Create campaign.

            //$campaignType = $campaign['campaign_type'];
            //var_dump(888); exit;
            $campaign = new Campaign(); //var_dump($campiagn); exit;
            $campaign->name = $campaign_array['io'].' '.$campaign_array['name'].' '.$campaign_array['id'];
            $campaign->advertisingChannelType = 'DISPLAY'; // set comaign type SEARCH
            if($campaign_array['preferred_mobile'] && $campaign_array['device_type']=="mobile"){

                $campaign->advertisingChannelSubType = 'DISPLAY_MOBILE_APP';
            }


            // Set shared budget (required).
            $campaign->budget = new Budget();
            $campaign->budget->budgetId = $budget->budgetId;

            // Set bidding strategy (required).
            $biddingStrategyConfiguration = new BiddingStrategyConfiguration();

            if($campaign_array['max_clicks']) {
                $biddingStrategyConfiguration->biddingStrategyType = 'MANUAL_CPC';
                $biddingScheme = new ManualCpcBiddingScheme();
                $biddingScheme->enhancedCpcEnabled = false;
            }
            else {
                $biddingStrategyConfiguration->biddingStrategyType = 'MANUAL_CPM';
                $biddingScheme = new ManualCpmBiddingScheme();
            }


            // You can optionally provide a bidding scheme in place of the type
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
            //$frequencyCap->impressions = $campaign_array['total_opens'];
            $frequencyCap->impressions = $campaign_array['retargeting_frequency']; // // PP-243
            $frequencyCap->timeUnit = 'DAY';
            $frequencyCap->level = 'ADGROUP';
            $campaign->frequencyCap = $frequencyCap;

            // Set advanced location targeting settings (optional).
            $geoTargetTypeSetting = new GeoTargetTypeSetting();
            $geoTargetTypeSetting->positiveGeoTargetType = 'LOCATION_OF_PRESENCE';
            $geoTargetTypeSetting->negativeGeoTargetType = 'LOCATION_OF_PRESENCE';
            $campaign->settings[] = $geoTargetTypeSetting;

            // Create operation.
            $operation = new CampaignOperation();
            $operation->operand = $campaign;
            $operation->operator = 'ADD';
            $operations[] = $operation;

            // Make the mutate request.

            $result = $campaignService->mutate($operations);

        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    var_dump($campaign_array['budget'],777,$error);
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        var_dump($error);
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0];
        // Display results.
        foreach ($result->value as $campaign) {
            printf("Campaign with name '%s' and ID '%s' was added.\n", $campaign->name,
                $campaign->id);
        }
        return $response;
    }

    function updateCampaignStatus(AdWordsUser $user, $campaignId, $status) {
        // Get the service, which loads the required classes.
        try {
            $response = array('message'=>'','result'=>'');
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
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0];
        // Display result.
//        $campaign = $result->value[0];
//        printf("Campaign with ID '%s' was '%s'.\n", $campaign->id, $status);
        return $response;
    }

    function updateCampaignEndDate(AdWordsUser $user, $campaignId, $endDate) {
        // Get the service, which loads the required classes.
        try {
            $response = array('message'=>'','result'=>'');
            $campaignService = $user->GetService('CampaignService', ADWORDS_VERSION);

            // Create campaign using an existing ID.
            $campaign = new Campaign();
            $campaign->id = $campaignId;
            $endDate = date('Ymd', strtotime($endDate));
            $campaign->endDate = $endDate;

            // Create operation.
            $operation = new CampaignOperation();
            $operation->operand = $campaign;
            $operation->operator = 'SET';

            $operations = array($operation);

            // Make the mutate request.
            $result = $campaignService->mutate($operations);
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0];
        // Display result.
        //$campaign = $result->value[0];
        //printf("Campaign with ID '%s' was .\n", $campaign);
        return $response;
    }

    function updateCampaignBudget(AdWordsUser $user, $campaignId, $newBudget) {
        // Get the service, which loads the required classes.

            $campaignService = $user->GetService('CampaignService', ADWORDS_VERSION);
            $budgetService = $user->GetService('BudgetService', ADWORDS_VERSION);
            // Create campaign using an existing ID.
//            $campaign = new Campaign();
//            $campaign->id = $campaignId;

            $selector = new Selector();
            $selector->fields = array('BudgetId', 'CampaignId');

            // Create predicates.
            $selector->predicates[] =
                new Predicate('CampaignId', 'EQUALS', $campaignId);
            // Create paging controls.
            $selector->paging = new Paging(0, AdWordsConstants::RECOMMENDED_PAGE_SIZE);

            do {
                // Make the get request.
                $page = $campaignService->get($selector);
                //var_dump($page); exit;
                // Display results.
                if (isset($page->entries)) {
                    $budget = $page->entries[0]->budget;
                    $money = $newBudget*1000000;

                    $budget->amount = new Money($money);

                    $operations = array();

                    // Create operation.
                    $operation = new BudgetOperation();
                    $operation->operand = $budget;
                    $operation->operator = 'SET';
                    $operations[] = $operation;

                    // Make the mutate request.
                    try {
                        $response = array('message'=>'','result'=>'');
                        $result = $budgetService->mutate($operations);

                    } catch(SoapFault $fault) {

                        $errors = ErrorUtils::GetApiErrors($fault);
                        if (sizeof($errors) == 0) {
                            // Not an API error, so throw fault.
                            return $fault->getMessage();
                        } else {
                            $text = '';
                            foreach ($errors as $key => $error) {
                                //detect error type
                                if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                                    // make error message readable text
                                    $text .= ++$key.') '.$error->externalPolicyName.' ';
                                    $text .= $error->key->violatingText.' ';
                                    $text .= $error->externalPolicyDescription.' ';
                                } else {
                                    $text .= ++$key.') '.$error->errorString.' ';
                                }
                            }
                            if($text) {
                                // return error message
                                $response['message'] = $text;
                                return $response;
                            }
                        }
                    }
                    $response['result'] = $result->value[0];
                } else {
                    print "No campaign targeting criteria were found.\n";
                }

                // Advance the paging index.
                $selector->paging->startIndex += AdWordsConstants::RECOMMENDED_PAGE_SIZE;
            } while ($page->totalNumEntries > $selector->paging->startIndex);
        return $response;
    }

    public function createLocationCriteria(AdWordsUser $user, $campaign_id, $criteria_id) {
        try {
            $response = array('message'=>'','result'=>'');
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
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0];
        foreach ($result->value as $campaignCriterion) {
            printf("Campaign targeting criterion with ID '%s' and type '%s' was "
                . "added.\n", $campaignCriterion->criterion->id,
                $campaignCriterion->criterion->CriterionType);
        }
        return $response;
    }

    public function createLocationCriteria1(AdWordsUser $user, $campaign_id, $criteria_array) {
        try {
            $campaignCriterionService = $user->GetService('CampaignCriterionService', ADWORDS_VERSION);
            $operations = array();
            foreach($criteria_array as $criteria_id) {
                // Create locations. The IDs can be found in the documentation or retrieved
                // with the LocationCriterionService.
                $location = new Location();
                $location->id = $criteria_id;
                $campaignCriteria = new CampaignCriterion($campaign_id, null, $location);

                // Create operations.
                $operations[] = new CampaignCriterionOperation($campaignCriteria, 'ADD');
            }
            $result = $campaignCriterionService->mutate($operations);
        } catch(Exception $e) {
            var_dump($e->getMessage()); exit;
        }
        foreach ($result->value as $campaignCriterion) {
//            printf("Campaign targeting criterion with ID '%s' and type '%s' was "
//                . "added.\n", $campaignCriterion->criterion->id,
//                $campaignCriterion->criterion->CriterionType);
        }
    }

    public function createProximityCriteria(AdWordsUser $user, $campaign_id, $postalCode, $radius) {
        try {
            $response = array('message'=>'', 'result'=>'');
            $campaignCriterionService = $user->GetService('CampaignCriterionService', ADWORDS_VERSION);

            $address = new Address();
            $address->postalCode = $postalCode;
            $address->countryCode = 'US';

            $proximity = new Proximity();
            $proximity->address = $address;
            $proximity->radiusDistanceUnits = 'MILES';
            $proximity->radiusInUnits = $radius;
            $campaignCriteria = new CampaignCriterion($campaign_id, null, $proximity);

            // Create operations.
            $operations = array();
            $operations[] = new CampaignCriterionOperation($campaignCriteria, 'ADD');

            $result = $campaignCriterionService->mutate($operations);
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);

            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {

                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        if(!empty($this->error_messages[$error->reason])) {
                            $error_text = $this->error_messages[$error->reason];
                        } else {
                            $error_tex = $error->reason;
                        }
                        $text .= ++$key.') '.$error_text;
                    }
                }

                if($text) {
                    // return error message
                    $response['message'] = $text.' '.$postalCode; //var_dump($postalCode);
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0]->criterion->id;
//        foreach ($result->value as $campaignCriterion) {
//            printf("Campaign targeting criterion with ID '%s' and type '%s' was "
//                . "added.\n", $campaignCriterion->criterion->id,
//                $campaignCriterion->criterion->CriterionType);
//        }
        return $response;

    }

    public function createProximityCriteria1(AdWordsUser $user, $campaign_id, $postalCodeArray, $radius) {
        try {
            $campaignCriterionService = $user->GetService('CampaignCriterionService', ADWORDS_VERSION);
            $operations = array();
            foreach($postalCodeArray as $postalCode) {
                $address = new Address();
                $address->postalCode = $postalCode;
                $address->countryCode = 'US';
                // Create geo location selector.
                // $selector = new GeoLocationSelector();
                // $selector->addresses = array($address);
                //var_dump($selector);
                //Get geo location.
                // $geoLocationResult = $geoLocationService->get($selector);
                // $geoPoint = $geoLocationResult[0]->geoPoint;
                //var_dump($address);

                $proximity = new Proximity();
                $proximity->address = $address;
                $proximity->radiusDistanceUnits = 'MILES';
                $proximity->radiusInUnits = $radius;
                $campaignCriteria = new CampaignCriterion($campaign_id, null, $proximity);

                // Create operations.
                $operations[] = new CampaignCriterionOperation($campaignCriteria, 'ADD');
            }
            $result = $campaignCriterionService->mutate($operations);
        } catch(Exception $e) {
            var_dump($e->getMessage()); exit;
        }
        $criteriaArray = array();
        foreach ($result->value as $campaignCriterion) {
//            printf("Campaign targeting criterion with ID '%s' and type '%s' was "
//                . "added.\n", $campaignCriterion->criterion->id,
//                $campaignCriterion->criterion->CriterionType);
            $criteriaArray[] = $campaignCriterion->criterion->id;
        }
        return $criteriaArray;

    }

    public function createCarrierCriteria(AdWordsUser $user, $campaign_id, $criteria_id) {
        try {
            $response = array('message'=>'', 'result'=>'');
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
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0]->criterion->id;
            foreach ($result->value as $campaignCriterion) {
                printf("Campaign targeting criterion with ID '%s' and type '%s' was "
                    . "added.\n", $campaignCriterion->criterion->id,
                    $campaignCriterion->criterion->CriterionType);
            }
        return $response;
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
    public function createGroup(AdWordsUser $user, $campaignId, $groupName, $maxClick, $campaign_bid, $status='ENABLED') {

        // Get the service, which loads the required classes.
        try {
            $response = array('message'=>'','result'=>'');
            $adGroupService = $user->GetService('AdGroupService', ADWORDS_VERSION);

            $operations = array();

            // Create ad group.
            $adGroup = new AdGroup();
            $adGroup->campaignId = $campaignId;
            $adGroup->name = $groupName;

            // Set bids (required).
            if($maxClick) {
                $bid = new CpcBid();
            }
            else {
                $bid = new CpmBid();
            }
            // set max bid for CPC 0.12$
            $newBid = 1000000*$campaign_bid;
            $bid->bid =  new Money($newBid);
            $biddingStrategyConfiguration = new BiddingStrategyConfiguration();
            $biddingStrategyConfiguration->bids[] = $bid;
            $adGroup->biddingStrategyConfiguration = $biddingStrategyConfiguration;

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
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0];
        // Display result.
//        foreach ($result->value as $group_array) {
//            printf("group with name '%s' and ID '%s' was added.\n", $group_array->name,
//                $group_array->id);
//        }
        return $response;
    }

    public function updateGroupBid(AdWordsUser $user, $groupId, $newBid, $isCpc) {
        // Get the service, which loads the required classes.
        try {
            $response = array('message'=>'','result'=>'');
            $adGroupService = $user->GetService('AdGroupService', ADWORDS_VERSION);

            $operations = array();

            // Create ad group.
            $adGroup = new AdGroup();
            $adGroup->id = $groupId;

            // Set bids (required).
            if($isCpc) {
                $bid = new CpcBid();
            }
            else {
                $bid = new CpmBid();
            }
            // set max bid for CPC 0.12$
            $newBid = 1000000*$newBid;
            $bid->bid =  new Money($newBid);
            $biddingStrategyConfiguration = new BiddingStrategyConfiguration();
            $biddingStrategyConfiguration->bids[] = $bid;
            $adGroup->biddingStrategyConfiguration = $biddingStrategyConfiguration;

            // Create operation.
            $operation = new AdGroupOperation();
            $operation->operand = $adGroup;
            $operation->operator = 'SET';
            $operations[] = $operation;


            // Make the mutate request.
            $result = $adGroupService->mutate($operations);
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0];
        // Display result.
//        foreach ($result->value as $group_array) {
//            printf("group with name '%s' and ID '%s' was added.\n", $group_array->name,
//                $group_array->id);
//        }
        return $response;
    }

    //removes the group from the campaign with id defined by the $campaignId
    public function removeGroup(AdWordsUser $user, $groupId) {
        // Get the service, which loads the required classes.
        try {
            $response = array('message'=>'', 'result'=>'');
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
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0];

        return $response;
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
            $response = array('message'=>'','result'=>'');
            $adGroupAdService = $user->GetService('AdGroupAdService', ADWORDS_VERSION);

            $operations = array();

            // Create image ad.
            $imageAd = new ImageAd();
            $imageAd->name = $ad['creative_name'];

            $url_array = parse_url($ad['original_url']);
//            $url = $url_array['scheme'].'://'.$url_array['host'];

            $imageAd->finalUrls = $ad['original_url'];

            if(isset($ad['display_url'])) {
                $imageAd->displayUrl = $ad['display_url'];
            } else {
                $imageAd->displayUrl = $url_array['host'];
            }
            if($ad['tracking_url']) {
                //$url_1 = urlencode($ad['original_url']);
                //$url_2 = $ad['tracking_url'].'?url='.$url_1;
                //$url_2 = urlencode($url_2);
                $imageAd->trackingUrlTemplate = $ad['destination_url'].'/?url='.$ad['tracking_url'];
            } else {
                $imageAd->trackingUrlTemplate = $ad['destination_url'];
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
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0];
        return $response;

        // Display results.
        //printf("ADD with ID '%s' was added.\n", $result->value[0]->ad->id);

    }

    // add demographics targeting into the group defined by the $adGroupId
    public function createGenderCriteria(AdWordsUser $user, $adGroupId, $genderId) {
        // Get the service, which loads the required classes.
        try{
            $response = array('message'=>'','result'=>'');
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
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0];
        foreach ($result->value as $campaignCriterion) {
            printf("Campaign targeting criterion with ID '%s' and type '%s' was "
                . "added.\n", $campaignCriterion->criterion->id,
                $campaignCriterion->criterion->CriterionType);
        }

        return $response;
    }

    public function createDomainExclusionsCriteria(AdWordsUser $user, $campaignId, $domain) {
        // $genderId ????

        // Get the service, which loads the required classes.
        try{
            $response = array('message'=>'','result'=>'');
            $campaignCriterionService = $user->GetService('CampaignCriterionService', ADWORDS_VERSION);

            // Create biddable ad group criterion for gender
            $placement = new Placement();
            // Criterion Id for male. The IDs can be found here
            // https://developers.google.com/adwords/api/docs/appendix/genders
            $placement->url = $domain;

            $domainExclusionsNegativeCriterion = new NegativeCampaignCriterion();
            $domainExclusionsNegativeCriterion->campaignId = $campaignId;
            $domainExclusionsNegativeCriterion->criterion = $placement;


            $operations = array();

            // Create operations.

            $domainExclusionsNegativeCriterionOperation = new CampaignCriterionOperation();
            $domainExclusionsNegativeCriterionOperation->operand = $domainExclusionsNegativeCriterion;
            $domainExclusionsNegativeCriterionOperation->operator = 'ADD';
            $operations[] = $domainExclusionsNegativeCriterionOperation;

            $result = $campaignCriterionService->mutate($operations);
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0]->criterion->id;
        foreach ($result->value as $campaignCriterion) {
            printf("Campaign targeting criterion with ID '%s' and type '%s' was "
                . "added.\n", $campaignCriterion->criterion->id,
                $campaignCriterion->criterion->CriterionType);
        }

        return $response;
    }

    public function createInterestCriteria(AdWordsUser $user, $adGroupId, $interestId) {
        // Get the service, which loads the required classes.
        try{
            $response = array('message'=>'','result'=>'');
            $adGroupCriterionService = $user->GetService('AdGroupCriterionService', ADWORDS_VERSION);

            // Create biddable ad group criterion for gender
            $interest = new CriterionUserInterest();
            // Criterion Id for male. The IDs can be found here
            // https://developers.google.com/adwords/api/docs/appendix/genders
            $interest->userInterestId = $interestId;

            $interestBiddableAdGroupCriterion = new BiddableAdGroupCriterion();
            $interestBiddableAdGroupCriterion->adGroupId = $adGroupId;
            $interestBiddableAdGroupCriterion->criterion = $interest;


            $operations = array();

            // Create operations.
            $interestBiddableAdGroupCriterionOperation = new AdGroupCriterionOperation();
            $interestBiddableAdGroupCriterionOperation->operand = $interestBiddableAdGroupCriterion;
            $interestBiddableAdGroupCriterionOperation->operator = 'ADD';
            $operations[] = $interestBiddableAdGroupCriterionOperation;

            $result = $adGroupCriterionService->mutate($operations);
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0]->criterion->id;
        foreach ($result->value as $campaignCriterion) {
            printf("Campaign targeting criterion with ID '%s' and type '%s' was "
                . "added.\n", $campaignCriterion->criterion->id,
                $campaignCriterion->criterion->CriterionType);
        }

        return $response;
    }

    public function createInMarketCriteria(AdWordsUser $user, $adGroupId, $inMarketId) {
        // Get the service, which loads the required classes.
        try{
            $response = array('message'=>'','result'=>'');
            $adGroupCriterionService = $user->GetService('AdGroupCriterionService', ADWORDS_VERSION);

            // Create biddable ad group criterion for im market
            $interest = new CriterionUserInterest();

            $interest->userInterestId = $inMarketId;

            $inMarketBiddableAdGroupCriterion = new BiddableAdGroupCriterion();
            $inMarketBiddableAdGroupCriterion->adGroupId = $adGroupId;
            $inMarketBiddableAdGroupCriterion->criterion = $interest;


            $operations = array();

            // Create operations.
            $inMarketBiddableAdGroupCriterionOperation = new AdGroupCriterionOperation();
            $inMarketBiddableAdGroupCriterionOperation->operand = $inMarketBiddableAdGroupCriterion;
            $inMarketBiddableAdGroupCriterionOperation->operator = 'ADD';
            $operations[] = $inMarketBiddableAdGroupCriterionOperation;

            $result = $adGroupCriterionService->mutate($operations);
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0]->criterion->id;
        foreach ($result->value as $campaignCriterion) {
            printf("Campaign targeting criterion with ID '%s' and type '%s' was "
                . "added.\n", $campaignCriterion->criterion->id,
                $campaignCriterion->criterion->CriterionType);
        }

        return $response;
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
        try {
            $response = array('message'=>'','result'=>'');
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
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $array[0] = $result->value[0]->criterion->id;
        $array[1] = $result->value[1]->criterion->id;
        $response['result'] = $array;

        return $response;

    }

    public function createKeyword(AdWordsUser $user, $adGroupId, $keyword_text) {
        // Get the service, which loads the required classes.
        try {
            $response = array('message'=>'','result'=>'');
            $adGroupCriterionService = $user->GetService('AdGroupCriterionService', ADWORDS_VERSION);

            $operations = array();

            // Create keyword criterion.
            $keyword = new Keyword();
            $keyword->text = $keyword_text;
            $keyword->matchType = 'BROAD';

            // Create biddable ad group criterion.
            $adGroupCriterion = new BiddableAdGroupCriterion();
            $adGroupCriterion->adGroupId = $adGroupId;
            $adGroupCriterion->criterion = $keyword;

            $adGroupCriteria[] = $adGroupCriterion;

            // Create operation.
            $operation = new AdGroupCriterionOperation();
            $operation->operand = $adGroupCriterion;
            $operation->operator = 'ADD';
            $operations[] = $operation;

            // Make the mutate request.
            $result = $adGroupCriterionService->mutate($operations);
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0]->criterion->id;
        return $response;
        // Display results.
       // foreach ($result->value as $adGroupCriterion) {
//            printf("Keyword with text '%s', match type '%s', and ID '%s' was added.\n",
//                $adGroupCriterion->criterion->text,
//                $adGroupCriterion->criterion->matchType,
//                $adGroupCriterion->criterion->id);
      //  }
       // return $result->value[0]->criterion->id;
    }

    public function createKeywords(AdWordsUser $user, $adGroupId, $keyword_array) {
        // Get the service, which loads the required classes.
        try {
            $operations = array();
            $response = array('message' => '', 'result' => '');
            foreach($keyword_array as $keyword_text) {

                $adGroupCriterionService = $user->GetService('AdGroupCriterionService', ADWORDS_VERSION);

                // Create keyword criterion.
                $keyword = new Keyword();
                $keyword->text = $keyword_text;
                $keyword->matchType = 'BROAD';

                // Create biddable ad group criterion.
                $adGroupCriterion = new BiddableAdGroupCriterion();
                $adGroupCriterion->adGroupId = $adGroupId;
                $adGroupCriterion->criterion = $keyword;

                $adGroupCriteria[] = $adGroupCriterion;

                // Create operation.
                $operation = new AdGroupCriterionOperation();
                $operation->operand = $adGroupCriterion;
                $operation->operator = 'ADD';
                $operations[] = $operation;
            }
            // Make the mutate request.
            $result = $adGroupCriterionService->mutate($operations);
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }

        // Display results.
        $result_array = array();
         foreach ($result->value as $adGroupCriterion) {
//            printf("Keyword with text '%s', match type '%s', and ID '%s' was added.\n",
//                $adGroupCriterion->criterion->text,
//                $adGroupCriterion->criterion->matchType,
//                $adGroupCriterion->criterion->id);
             $result_array[] = $adGroupCriterion->criterion->id;
          }
        $response['result'] = $result_array;
        return $response;
    }

    //adds the audience defined by the $userListId into the group defined by the $adGroupId
    public function createCriterion(AdWordsUser $user, $adGroupId, $userListId) {
        // Get the service, which loads the required classes.
        try { //var_dump('crit');
            $response = array('message'=>'','result'=>'');
            $adGroupCriterionService = $user->GetService('AdGroupCriterionService', ADWORDS_VERSION);

            $operations = array();

            $criterion = new CriterionUserList();
            $criterion->userListId = $userListId;
            $criterion->userListMembershipStatus='OPEN';
            $criterion->userListName="Criterion_Harut";

            // Create biddable ad group criterion.
            $adGroupCriterion = new BiddableAdGroupCriterion();
            $adGroupCriterion->adGroupId = $adGroupId;
            $adGroupCriterion->criterion = $criterion;

            // Set additional settings (optional).
            $adGroupCriterion->userStatus = 'ENABLED';

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
            //var_dump('crit1');
            $adGroupCriteria[] = $adGroupCriterion;

            // Create operation.
            $operation = new AdGroupCriterionOperation();
            $operation->operand = $adGroupCriterion;
            $operation->operator = 'ADD';
            $operations[] = $operation;

            // Make the mutate request.
            //var_dump('crit2');
            $result = $adGroupCriterionService->mutate($operations);// var_dump('crit3');
        } catch(SoapFault $fault) {
            var_dump(3333,$fault);
            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                $response['message'] =  $fault->getMessage();
                return $response;
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }

        //var_dump($result, $result->value[0]->criterion->id);
        $response['result'] = $result->value[0]->criterion->id;

        // Display results.
//        foreach ($result->value as $adGroupCriterion) {
//            $criterion["id"]=$adGroupCriterion->criterion->id;
//            printf("group targeting criterion with ID '%s' and type '%s' was "
//                . "added.\n", $adGroupCriterion->criterion->id,
//                $adGroupCriterion->criterion->CriterionType);
//        }
        return $response;
    }

    //removes the criterion (object which connects the audience with the ad group) from the group defined by the argument $adGroupId
    public function removeCriterion(AdWordsUser $user, $adGroupId, $criterionId) {
        // Get the service, which loads the required classes.
        try {
            $response = array('message'=>'','result'=>'');
            $adGroupCriterionService = $user->GetService('AdGroupCriterionService', ADWORDS_VERSION);

            // Create criterion using an existing ID. Use the base class Criterion
            // instead of Keyword to avoid having to set keyword-specific fields.
            $criterion = new Criterion();
            $criterion->id = $criterionId;

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
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0];
        return $response;
    }

    public function removeCampaignCriteria(AdWordsUser $user, $campaignId, $criteriaArray) {
        // Get the service, which loads the required classes.
        try {
            $response = array('message'=>'','result'=>'');
            $campaignCriterionService = $user->GetService('CampaignCriterionService', ADWORDS_VERSION);
            $operations = array();
            foreach ($criteriaArray as $criterionId) {
                // Create criterion using an existing ID. Use the base class Criterion
                // instead of Keyword to avoid having to set keyword-specific fields.
                $criterion = new Criterion();
                $criterion->id = $criterionId;

                // Create ad group criterion.
                $campaignCriterion = new CampaignCriterion($campaignId, null, $criterion);
                // Create operation.
//            $operation = new CampaignCriterionOperation();
//            $operation->operand = $campaignCriterion;
//            $operation->operator = 'REMOVE';
                $operation = new CampaignCriterionOperation($campaignCriterion, 'REMOVE');
                $operations[] = $operation;
            }
            // Make the mutate request.
            $result = $campaignCriterionService->mutate($operations);
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0];

        // Display result.
//        foreach ($result->value as $campaignCriterion) {
//            printf("Campaign targeting criterion with ID '%s' and type '%s' was "
//                . "Removed.\n", $campaignCriterion->criterion->id,
//                $campaignCriterion->criterion->CriterionType);
//        }
        return $response;
    }

    function getCampaignTargetingCriteria(AdWordsUser $user, $campaignId) {
        // Get the service, which loads the required classes.
        $campaignCriterionService =
            $user->GetService('CampaignCriterionService', ADWORDS_VERSION);

        // Create selector.
        $selector = new Selector();
        $selector->fields = array('Id', 'CriteriaType');

        // Create predicates.
        $selector->predicates[] =
            new Predicate('CampaignId', 'IN', array($campaignId));
        $selector->predicates[] = new Predicate('CriteriaType', 'IN',
            array('LANGUAGE', 'LOCATION', 'AGE_RANGE', 'CARRIER',
                'OPERATING_SYSTEM_VERSION', 'GENDER', 'PROXIMITY', 'PLATFORM'));

        // Create paging controls.
        $selector->paging = new Paging(0, AdWordsConstants::RECOMMENDED_PAGE_SIZE);

        do {
            // Make the get request.
            $page = $campaignCriterionService->get($selector);

            // Display results.
            if (isset($page->entries)) {
                foreach ($page->entries as $campaignCriterion) {
                    printf("Campaign targeting criterion with ID '%s' and type '%s' was "
                        . "found.\n", $campaignCriterion->criterion->id,
                        $campaignCriterion->criterion->CriterionType);
                }
            } else {
                print "No campaign targeting criteria were found.\n";
            }

            // Advance the paging index.
            $selector->paging->startIndex += AdWordsConstants::RECOMMENDED_PAGE_SIZE;
        } while ($page->totalNumEntries > $selector->paging->startIndex);
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
        $reportQuery = 'SELECT AdGroupId, Clicks, Impressions, Cost FROM ADGROUP_PERFORMANCE_REPORT '
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
//    public function updateAdStatus(AdWordsUser $user,$adGroupId, $adName, $adId, $adStatus) {
//        // Get the service, which loads the required classes.
//        $adGroupAdService = $user->GetService('AdGroupAdService', ADWORDS_VERSION);
//
//        $operations = array();
//
//        // Create image ad.
//        $imageAd = new ImageAd();
//        $imageAd->id=$adId;
//        $imageAd->name = $adName;
//
//        // Create ad group ad.
//        $adGroupAd = new AdGroupAd();
//        $adGroupAd->adGroupId = $adGroupId;
//        $adGroupAd->ad = $imageAd;
//
//        // Set additional settings (optional).
//        $adGroupAd->status = $adStatus;
//
//        $operation = new AdGroupAdOperation();
//        $operation->operand = $adGroupAd;
//        $operation->operator = 'SET';
//        $operations[] = $operation;
//
//        // Make the mutate request.
//        $result = $adGroupAdService->mutate($operations);
//
//        return $result;
//    }

    public function updateGroupStatus(AdWordsUser $user, $adGroupId, $status){
        try{
            $response = array('message'=>'','result'=>'');
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
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0];
        return $response;
    }

    //creates a text ad into the group with id defined by $adGroupId
    public function createTextAds(AdWordsUser $user, $adGroupId, $ad) {
        // Get the service, which loads the required classes.
        try{
            $response = array('message'=>'','result'=>'');
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

//                if(isset($ad['original_url'])) {
//                    $url_array = parse_url($ad['original_url']);
//                    $url = $url_array['scheme'].'://'.$url_array['host'];
                    $textAd->finalUrls = $ad['original_url'];
//                } else {
//                    $textAd->finalUrls = $ad['display_url'];
//                }
                $textAd->trackingUrlTemplate = $ad['destination_url'];
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
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0];
        return $response;

    }

    function HandlePolicyViolationErrorExample(AdWordsUser $user, $adGroupId) { //var_dump(77); exit;
        // Get the service, which loads the required classes.
        $adGroupAdService = $user->GetService('AdGroupAdService', ADWORDS_VERSION);

        // Get validateOnly version of the AdGroupAdService.
        $adGroupAdValidationService =
            $user->GetService('AdGroupAdService', ADWORDS_VERSION, null, null, true);

        // Create text ad that violates an exemptable policy. This ad will only
        // trigger an error in the production environment.
        $textAd = new TextAd();
        $textAd->headline = 'Mars Cruise !!!';
        $textAd->description1 = 'Visit the Red Planet in .';
        $textAd->description2 = 'Low-gravity fun for everyone!';
        $textAd->displayUrl = 'www.exampllle.com';
        $textAd->finalUrls = array('http://www.example.com/');

        // Create ad group ad.
        $adGroupAd = new AdGroupAd();
        $adGroupAd->adGroupId = $adGroupId;
        $adGroupAd->ad = $textAd;

        // Create operation.
        $operation = new AdGroupAdOperation();
        $operation->operand = $adGroupAd;
        $operation->operator = 'ADD';

        $operations = array($operation);

        try {
            // Make the mutate request.
            $result = $adGroupAdValidationService->mutate($operations);
        } catch (SoapFault $fault) {
            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                throw $fault;
            }
            $operationIndicesToRemove = array();
            foreach ($errors as $error) {
                if ($error->ApiErrorType == 'PolicyViolationError') {
                    $operationIndex = ErrorUtils::GetSourceOperationIndex($error);
                    $operation = $operations[$operationIndex];
                    printf("Ad with headline '%s' violated %s policy '%s'.\n",
                        $operation->operand->ad->headline,
                        $error->isExemptable ? 'exemptable' : 'non-exemptable',
                        $error->externalPolicyName);
                    if ($error->isExemptable) {
                        // Add exemption request to the operation.
                        printf("Adding exemption request for policy name '%s' on text "
                            ."'%s'.\n", $error->key->policyName, $error->key->violatingText);
                        $operation->exemptionRequests[] = new ExemptionRequest($error->key);
                    } else {
                        // Remove non-exemptable operation.
                        print "Removing the operation from the request.\n";
                        $operationIndicesToRemove[] = $operationIndex;
                    }
                } else {
                    // Non-policy error returned, throw fault.
                    throw $fault;
                }
            }
            $operationIndicesToRemove = array_unique($operationIndicesToRemove);
            rsort($operationIndicesToRemove, SORT_NUMERIC);
            foreach ($operationIndicesToRemove as $operationIndex) {
                unset($operations[$operationIndex]);
            }
        }

        if (sizeof($operations) > 0) {
            // Retry the mutate request.
            $result = $adGroupAdService->mutate($operations);

            // Display results.
            foreach ($result->value as $adGroupAd) {
                printf("Text ad with headline '%s' and ID '%s' was added.\n",
                    $adGroupAd->ad->headline, $adGroupAd->ad->id);
            }
        } else {
            print "All the operations were invalid with non-exemptable errors.\n";
        }
    }

//    public function removeTextAd(AdWordsUser $user, $adGroupId, $textAdId, $headline) {
//        // Get the service, which loads the required classes.
//        $adGroupAdService = $user->GetService('AdGroupAdService', ADWORDS_VERSION);
//
//        $operations = array();
//
//        // Create image ad.
//        $textAd = new TextAd();
//        $textAd->id = $textAdId;
//        $textAd->headline = $headline;
//
//        // Create ad group ad.
//        $adGroupAd = new AdGroupAd();
//        $adGroupAd->adGroupId = $adGroupId;
//        $adGroupAd->ad =  $textAd;
//
//        $operation = new AdGroupAdOperation();
//        $operation->operand = $adGroupAd;
//        $operation->operator = 'REMOVE';
//        $operations[] = $operation;
//
//        // Make the mutate request.
//        $result = $adGroupAdService->mutate($operations);
//
//        return $textAdId;
//    }

    function updateAdStatus(AdWordsUser $user, $adGroupId, $adId, $adStatus) {
        // Get the service, which loads the required classes.
        try{
            $response = array('message'=>'','result'=>'');
            $adGroupAdService = $user->GetService('AdGroupAdService', ADWORDS_VERSION);

            // Create ad using an existing ID. Use the base class Ad instead of TextAd to
            // avoid having to set ad-specific fields.
            $ad = new Ad();
            $ad->id = $adId;

            // Create ad group ad.
            $adGroupAd = new AdGroupAd();
            $adGroupAd->adGroupId = $adGroupId;
            $adGroupAd->ad = $ad;

            // Update the status.
            $adGroupAd->status = $adStatus;

            // Create operation.
            $operation = new AdGroupAdOperation();
            $operation->operand = $adGroupAd;
            $operation->operator = 'SET';

            $operations = array($operation);

            // Make the mutate request.
            $result = $adGroupAdService->mutate($operations);
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
            return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $result->value[0];
        // Display result.
        //        printf("Ad of type '%s' with ID '%s' has updated status '%s'.\n",
        //     $adGroupAd->ad->AdType, $adGroupAd->ad->id, $adGroupAd->status);
        return $response;
    }

    //changes the ad status to the one defined by the $adStatus argument
//    public function UpdateTextAdStatus(AdWordsUser $user, $adGroupId, $adId, $adStatus, $headline) {
//        // Get the service, which loads the required classes.
//        $adGroupAdService = $user->GetService('AdGroupAdService', ADWORDS_VERSION);
//
//        $operations = array();
//
//        // Create image ad.
//        $imageAd = new TextAd();
//        $imageAd->id=$adId;
//        $imageAd->headline = $headline;
//
//        // Create ad group ad.
//        $adGroupAd = new AdGroupAd();
//        $adGroupAd->adGroupId = $adGroupId;
//        $adGroupAd->ad = $imageAd;
//
//        // Set additional settings (optional).
//        $adGroupAd->status = $adStatus;
//
//        $operation = new AdGroupAdOperation();
//        $operation->operand = $adGroupAd;
//        $operation->operator = 'SET';
//        $operations[] = $operation;
//
//        // Make the mutate request.
//        $result = $adGroupAdService->mutate($operations);
//    }

    /**
     * Runs the example.
     * @param AdWordsUser $user the user to run the example with
     * @param string $adGroupId the parent ad group id of the ads to retrieve
     */
    function getAllDisapprovedAds(AdWordsUser $user, $campaignIds) {
        // Get the service, which loads the required classes.
        try {
            $response = array('message'=>'','result'=>'');
            $adGroupAdService = $user->GetService('AdGroupAdService', ADWORDS_VERSION);

            // Create selector.
            $selector = new Selector();
            $selector->fields = array('Id', 'AdGroupAdDisapprovalReasons', 'AdGroupCreativeApprovalStatus');
            $selector->ordering = array(new OrderBy('Id', 'ASCENDING'));

            // Create predicates.
            $selector->predicates[] = new Predicate('CampaignId', 'IN', $campaignIds);
            //$selector->predicates[] = new Predicate('AdGroupCreativeApprovalStatus', 'EQUALS', array('DISAPPROVED'));

            // Create paging controls.
            $selector->paging = new Paging(0, AdWordsConstants::RECOMMENDED_PAGE_SIZE);
            $ads = array();
            do {
                // Make the get request.
                $page = $adGroupAdService->get($selector);

                // Display results.
                if (isset($page->entries)) {
                    foreach ($page->entries as $adGroupAd) {
                        $ad = array();
                        printf("Ad with ID '%.0f', and type '%s' was disapproved for the "
                            . "following reasons:\n", $adGroupAd->ad->id,
                            $adGroupAd->ad->AdType);
                            $ad['network_creative_id'] = $adGroupAd->ad->id;
                        if (!empty($adGroupAd->disapprovalReasons)) {
                            $reason_array = array();
                            foreach ($adGroupAd->disapprovalReasons as $reason) {
                                printf("\t'%s'\n", $reason);
                                $reason_array[] = $reason;
                            }
                            $ad['reasons'] = $reason_array;
                        }
                        if (!empty($adGroupAd->approvalStatus)) {
                            $ad['approval_status'] = $adGroupAd->approvalStatus;
                            printf("\t'%s'\n", $adGroupAd->approvalStatus);
                        }
                        $ads[] = $ad;
                    }
                } else {
                    print "No disapproved ads were found.\n";
                }

                // Advance the paging index.
                $selector->paging->startIndex += AdWordsConstants::RECOMMENDED_PAGE_SIZE;
            } while ($page->totalNumEntries > $selector->paging->startIndex);
        } catch(SoapFault $fault) {

            $errors = ErrorUtils::GetApiErrors($fault);
            if (sizeof($errors) == 0) {
                // Not an API error, so throw fault.
                return $fault->getMessage();
            } else {
                $text = '';
                foreach ($errors as $key => $error) {
                    //detect error type
                    if ($error->ApiErrorType == 'PolicyViolationError' || $error->ApiErrorType == 'CriterionPolicyError') {
                        // make error message readable text
                        $text .= ++$key.') '.$error->externalPolicyName.' ';
                        $text .= $error->key->violatingText.' ';
                        $text .= $error->externalPolicyDescription.' ';
                    } else {
                        $text .= ++$key.') '.$error->errorString.' ';
                    }
                }
                if($text) {
                    // return error message
                    $response['message'] = $text;
                    return $response;
                }
            }
        }
        $response['result'] = $ads;
        return $response;
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
        $reportQuery = 'SELECT Clicks, Impressions, CampaignId, ImpressionAssistedConversions FROM AD_PERFORMANCE_REPORT WHERE CampaignId = "281979385" ';
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

    public function getAdsImpressionsByActiveCampaigns(AdWordsUser $user, $filePath, $reportFormat) {
        // Optional: Set clientCustomerId to get reports of your child accounts
        // $user->SetClientCustomerId('INSERT_CLIENT_CUSTOMER_ID_HERE');

        // Prepare a date range for the last week. Instead you can use 'LAST_7_DAYS'.
        //$dateRange = sprintf('%d,%d',date('Ymd', strtotime('-7 day')), date('Ymd', strtotime('-1 day')));
        //$dateRange = sprintf('%d,%d',date('Ymd', strtotime('-1 day')), date('Ymd', strtotime('now')));
        $response = array('message'=>'','result'=>'');
        // Create report query.
        $reportQuery = 'SELECT Id, Impressions FROM AD_PERFORMANCE_REPORT WHERE CampaignStatus = "ENABLED"';

        // Set additional options.
        $options = array('version' => ADWORDS_VERSION);

        // Download report.
        try {
            $result = ReportUtils::DownloadReportWithAwql($reportQuery, $filePath = null, $user, $reportFormat = 'XML', $options);
        } catch(Exception $e) {
            var_dump($e->getMessage()); exit;
        }
        $result=simplexml_load_string($result);
        //echo '<pre>';
        //print_r($result1); //exit;
        $response['result'] = $result->table->row;
        return $response;
    }

    function getActiveCampaignsCosts(AdWordsUser $user, $filePath, $reportFormat) {
        // Optional: Set clientCustomerId to get reports of your child accounts
        // $user->SetClientCustomerId('INSERT_CLIENT_CUSTOMER_ID_HERE');

        // Prepare a date range for the last week. Instead you can use 'LAST_7_DAYS'.
        //$dateRange = sprintf('%d,%d',date('Ymd', strtotime('-7 day')), date('Ymd', strtotime('-1 day')));
        $dateRange = sprintf('%d,%d',date('Ymd', strtotime('-1 day')), date('Ymd', strtotime('now')));

        // Create report query.
        //$reportQuery = 'SELECT Id, Impressions FROM AD_PERFORMANCE_REPORT WHERE CampaignStatus = "ENABLED" DURING ' . $dateRange;
        $reportQuery = 'SELECT CampaignId, Cost FROM CAMPAIGN_PERFORMANCE_REPORT WHERE CampaignStatus = "ENABLED" ';

        // Set additional options.
        $options = array('version' => ADWORDS_VERSION);

        // Download report.
        try {
            $result = ReportUtils::DownloadReportWithAwql($reportQuery, $filePath = null, $user, $reportFormat = 'XML', $options);
        } catch(Exception $e) {
            var_dump($e->getMessage()); exit;
        }
        $result=simplexml_load_string($result);
        $response['result'] = $result->table->row;
        return $response;
    }

    public function getActiveCampaignsAgeReport(AdWordsUser $user, $filePath, $reportFormat) {

        $response = array('message'=>'','result'=>'');
        // Create report query.
        $reportQuery = 'SELECT Id, Impressions, Criteria, Clicks, AdGroupId, AdGroupName, CampaignId FROM AGE_RANGE_PERFORMANCE_REPORT
                        WHERE CampaignStatus = "ENABLED"';
        // Set additional options.
        $options = array('version' => ADWORDS_VERSION);

        // Download report.
        try {
            $result = ReportUtils::DownloadReportWithAwql($reportQuery, $filePath = null, $user, $reportFormat = 'XML', $options);
        } catch(Exception $e) {
            var_dump($e->getMessage()); exit;
        }
        $result=simplexml_load_string($result);
        //echo '<pre>';
        //print_r($result->table); exit;
        $response['result'] = $result->table->row;
        return $response;
    }

    public function getActiveCampaignsGenderReport(AdWordsUser $user, $filePath, $reportFormat) {

        $response = array('message'=>'','result'=>'');
        // Create report query.

        $reportQuery = 'SELECT Id, Impressions, Criteria, Clicks, AdGroupId, AdGroupName, CampaignId FROM GENDER_PERFORMANCE_REPORT
                        WHERE CampaignStatus = "ENABLED"';

        // Set additional options.
        $options = array('version' => ADWORDS_VERSION);

        // Download report.
        try {
            $result = ReportUtils::DownloadReportWithAwql($reportQuery, $filePath = null, $user, $reportFormat = 'XML', $options);
        } catch(Exception $e) {
            var_dump($e->getMessage()); exit;
        }
        $result=simplexml_load_string($result);
//        echo '<pre>';
//        print_r($result->table); exit;
        $response['result'] = $result->table->row;
        return $response;
    }

    public function getActiveCampaignsPlacementReport(AdWordsUser $user, $filePath, $reportFormat) {

        $response = array('message'=>'','result'=>'');
        // Create report query.

        $reportQuery = 'SELECT Id, Impressions, Clicks,Cost, AdGroupId, AdGroupName, CampaignId, CustomerDescriptiveName, DisplayName FROM PLACEMENT_PERFORMANCE_REPORT
                        WHERE CampaignStatus = "ENABLED"';

        // Set additional options.
        $options = array('version' => ADWORDS_VERSION);

        // Download report.
        try {
            $result = ReportUtils::DownloadReportWithAwql($reportQuery, $filePath = null, $user, $reportFormat = 'XML', $options);
        } catch(Exception $e) {
            var_dump($e->getMessage()); exit;
        }
        $result=simplexml_load_string($result); //var_dump($result);

        $response['result'] = $result->table->row;
        return $response;
    }
}
