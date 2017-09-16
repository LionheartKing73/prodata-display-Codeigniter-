<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Lib for Bing network
 * 
 */
require_once  APPPATH . '/third_party/bingads/v10/CampaignManagementClasses.php';
require_once  APPPATH . '/third_party/bingads/ClientProxy.php';

include APPPATH.'libraries/IniWriter.php';
//include 'third_party\bingads\ClientProxy.php';


// Specify the BingAds\CampaignManagement objects that will be used.
use BingAds\v10\CampaignManagement\AddCampaignsRequest;
use BingAds\v10\CampaignManagement\DeleteCampaignsRequest;
use BingAds\v10\CampaignManagement\AddAdGroupsRequest;
use BingAds\v10\CampaignManagement\AddTargetsToLibraryRequest;
use BingAds\v10\CampaignManagement\GetTargetsByIdsRequest;
use BingAds\v10\CampaignManagement\UpdateTargetsInLibraryRequest;
use BingAds\v10\CampaignManagement\GetTargetsInfoFromLibraryRequest;
use BingAds\v10\CampaignManagement\DeleteTargetFromAdGroupRequest;
use BingAds\v10\CampaignManagement\DeleteTargetFromCampaignRequest;
use BingAds\v10\CampaignManagement\DeleteTargetsFromLibraryRequest;
use BingAds\v10\CampaignManagement\SetTargetToAdGroupRequest;
use BingAds\v10\CampaignManagement\SetTargetToCampaignRequest;
use BingAds\v10\CampaignManagement\Campaign;
use BingAds\v10\CampaignManagement\AdGroup;
use BingAds\v10\CampaignManagement\AdGroupStatus;
use BingAds\v10\CampaignManagement\Target;
use BingAds\v10\CampaignManagement\AgeTarget;
use BingAds\v10\CampaignManagement\AgeTargetBid;
use BingAds\v10\CampaignManagement\DayTimeTarget;
use BingAds\v10\CampaignManagement\DayTimeTargetBid;
use BingAds\v10\CampaignManagement\DeviceOSTarget;
use BingAds\v10\CampaignManagement\DeviceOSTargetBid;
use BingAds\v10\CampaignManagement\GenderTarget;
use BingAds\v10\CampaignManagement\GenderTargetBid;
use BingAds\v10\CampaignManagement\LocationTarget;
use BingAds\v10\CampaignManagement\LocationTargetBid;
use BingAds\v10\CampaignManagement\CityTarget;
use BingAds\v10\CampaignManagement\CityTargetBid;
use BingAds\v10\CampaignManagement\CountryTarget;
use BingAds\v10\CampaignManagement\CountryTargetBid;
use BingAds\v10\CampaignManagement\MetroAreaTarget;
use BingAds\v10\CampaignManagement\MetroAreaTargetBid;
use BingAds\v10\CampaignManagement\PostalCodeTarget;
use BingAds\v10\CampaignManagement\PostalCodeTargetBid;
use BingAds\v10\CampaignManagement\RadiusTarget;
use BingAds\v10\CampaignManagement\RadiusTargetBid;
use BingAds\v10\CampaignManagement\StateTarget;
use BingAds\v10\CampaignManagement\StateTargetBid;
use BingAds\v10\CampaignManagement\TargetInfo;
use BingAds\v10\CampaignManagement\Bid;
use BingAds\v10\CampaignManagement\BudgetLimitType;
use BingAds\v10\CampaignManagement\AdDistribution;
use BingAds\v10\CampaignManagement\BiddingModel;
use BingAds\v10\CampaignManagement\PricingModel;
use BingAds\v10\CampaignManagement\Date;
use BingAds\v10\CampaignManagement\Day;
use BingAds\v10\CampaignManagement\Minute;
use BingAds\v10\CampaignManagement\HourRange;
use BingAds\v10\CampaignManagement\AgeRange;
use BingAds\v10\CampaignManagement\GenderType;
use BingAds\v10\CampaignManagement\DistanceUnit;
use BingAds\v10\CampaignManagement\IntentOption;

use BingAds\v10\CampaignManagement\AddKeywordsRequest;
use BingAds\v10\CampaignManagement\AddAdsRequest;
use BingAds\v10\CampaignManagement\Keyword;
use BingAds\v10\CampaignManagement\Ad;
use BingAds\v10\CampaignManagement\TextAd;
use BingAds\v10\CampaignManagement\MatchType;
use BingAds\v10\CampaignManagement\CustomParameters;
use BingAds\v10\CampaignManagement\CustomParameter;

use BingAds\Proxy\ClientProxy;

class Bing {
    // code M91b784f0-efb4-2b82-0da1-bed8da14aae4
    // red_url http://reporting.prodata.media/v2/bingCrons/getToken
    private $client_id = "000000004417AE58";
    private $client_secret = "f6A3bgcI77xO3ID4iO58sYIWsbdB7llp";
    private $authentication_token = 'EwBoAnhlBAAUxT83/QvqiAZEx5SuwyhZqHzk21oAAVJQBRow%2bmMjk%2bUuuyELyLVLeLGRnU60AzAPjGwtbofhyDREYxjBWIA3z1efW4AYz3NdPJFQ4BPMtmYIsJr0iRyL4O%2bb4N9VdbHO3pvL5yB4QEZvQdl0hY0T20BZ9KJOxa%2bQrszPfSuCu6oYwk/KHBLwLPprvGjzhp8tDvyTPP/AhJx7SiyFvji0V9ceNex9kNxa/veys9w%2bAusjQCI%2b7lJbQC5UCcgQ5kvemzTp0TnU1W1fsLpFyFEfraMnJrS1BbnrMc3zeg2SvggLWZDQdHKrLTlgphVhVkQyoMg3KMmiKJWppcojBkrID3s9mo0gp3TC6%2bRmqMDqDN67N4tzinsDZgAACAYGWGy1fpMmOAFtVOfhIgr6T7GQnLM7oHVpgpox6ifJRaRj7dZxGoYtG2dUwahTKQLm1Xf2EdGrqNYm%2bVGEb5VF3g2dytflm3obH4JntwgKLV3K3unwbiuP4FGweB1h%2bUA2yqXp5xiNn8XaOGr4%2b0Yv39xEg6LhWdfxU7foEn7ODQdiWU%2bOpSEIM5Y8VLieu7yva1AnH9LMVNSRd1x3rfhnd2xpUfT/%2bqiW%2bXgmoyIPSyxAAAurRj6QPKS6p42TTBzRHMJrOIU0%2b8PKtskuth23wdbsScHcl6OfjrAwp/HuwaFyZRzysM5J5OAcMka4jnMKGfASmaBoU2omjnO5jdDqh312rUsWNZAKWipPWP4/XJz96E8kZfaKBd51Y8zZ9aC9JAfMAoEVLaYSabuNwPuZoL4tejiV4M8IBDlpSvDoKcdZAQ%3d%3d';
    private $user_name = "jason@prodatafeed.com";
    private $password = "ProDataFeed2015";
    private $developer_token = "0208M80IDR111936";
    private $customer_id = "21122488";
    private $account_id = "50023555";
    private $proxy;
    private $wsdl = "https://campaign.api.bingads.microsoft.com/Api/Advertiser/CampaignManagement/V10/CampaignManagementService.svc?singleWsdl";

    public function __construct() {
        
        ini_set("soap.wsdl_cache_enabled", "0");
        ini_set("soap.wsdl_cache_ttl", "0");
        $auth = parse_ini_file(APPPATH . 'third_party/bingads/auth.ini', true);


        if(time() > ($auth["OAUTH2"]['refresh_time']-10)) {
            var_dump(777);
            $writer = new IniWriter();
            $new_auth = $this->get_token($auth["OAUTH2"]['refresh_token']);
            $auth["OAUTH2"]['refresh_time'] = time() + $new_auth['expires_in'];
            $auth["OAUTH2"]['expires_in'] = $new_auth['expires_in'];
            $auth["OAUTH2"]['refresh_token'] = $new_auth['refresh_token'];
            $auth["OAUTH2"]['access_token'] = $new_auth['access_token'];

            $result = $writer->writeToFile(APPPATH . 'third_party/bingads/auth.ini', $auth);
        }

        $this->authentication_token = $auth["OAUTH2"]['access_token'];

        $this->proxy = ClientProxy::ConstructWithAccountAndCustomerId(
            $this->wsdl,
            $this->user_name,
            $this->password,
            $this->developer_token,
            $this->account_id,
            $this->customer_id,
            $this->authentication_token
        );
        
    }

    public function get_token($refresh_tocken)
    {

        $accessTokenExchangeUrl = "https://login.live.com/oauth20_token.srf";
        $accessTokenExchangeParams = array(
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_tocken,
            'redirect_uri' => 'http://reporting.prodata.media/v2/bingCrons/get_token'
        );

        $json = $this->post_data($accessTokenExchangeUrl,$accessTokenExchangeParams);
        $responseArray = json_decode($json, TRUE);

        return $responseArray;

    }
    public function post_data($url, $postData) {
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

    public function create_campaign($campaign){


        $campaigns = array();

        $new_campaign = new Campaign();
//        var_dump($new_campaign); exit;
        //print_r($new_campaign);
        $new_campaign->Name = "Winter Clothing 1" . $_SERVER['REQUEST_TIME'];
        $new_campaign->BudgetType = BudgetLimitType::DailyBudgetStandard;
        $new_campaign->DailyBudget = 1;
        $new_campaign->TimeZone = "PacificTimeUSCanadaTijuana";
        $new_campaign->Description = "Winter clothing line.";
        $new_campaign->DaylightSaving = true;
        // Used with FinalUrls shown in the sitelinks that we will add below.
        //$new_campaign->TrackingUrlTemplate = "http://tracker.example.com/?season={_season}&promocode={_promocode}&u={lpurl}";
        $campaigns[] = $new_campaign;
        
        $request = new AddCampaignsRequest();
        $request->AccountId = $this->account_id;
        $request->Campaigns = $campaigns;
        
        try{
            $campaignIds =  $this->proxy->GetService()->AddCampaigns($request);
        }
        catch (SoapFault $e)
        {
            // Output the last request/response.

            print "\nLast SOAP request/response:\n";

            print $this->proxy->GetWsdl() . "\n";
            print $this->proxy->GetService()->__getLastRequest()."\n";
            print $this->proxy->GetService()->__getLastResponse()."\n";
//
//            print $agencyProxy->GetWsdl() . "\n";
//            print $agencyProxy->GetService()->__getLastRequest()."\n";
//            print $agencyProxy->GetService()->__getLastResponse()."\n";


            // Customer Management service operations can throw AdApiFaultDetail.
            if (isset($e->detail->AdApiFaultDetail))
            {
                // Log this fault.

                print "The operation failed with the following faults:\n";

                $errors = is_array($e->detail->AdApiFaultDetail->Errors->AdApiError)
                    ? $e->detail->AdApiFaultDetail->Errors->AdApiError
                    : array('AdApiError' => $e->detail->AdApiFaultDetail->Errors->AdApiError);

                // If the AdApiError array is not null, the following are examples of error codes that may be found.
                foreach ($errors as $error)
                {
                    print "AdApiError\n";
                    printf("Code: %d\nError Code: %s\nMessage: %s\n", $error->Code, $error->ErrorCode, $error->Message);

                    switch ($error->Code)
                    {
                        case 105:  // InvalidCredentials
                            break;
                        default:
                            print "Please see MSDN documentation for more details about the error code output above.\n";
                            break;
                    }
                }
            }

            // Customer Management service operations can throw ApiFault.
            elseif (isset($e->detail->ApiFault))
            {
                // Log this fault.

                print "The operation failed with the following faults:\n";

                // If the OperationError array is not null, the following are examples of error codes that may be found.
                if (!empty($e->detail->ApiFault->OperationErrors))
                {
                    $errors = is_array($e->detail->ApiFault->OperationErrors->OperationError)
                        ? $e->detail->ApiFault->OperationErrors->OperationError
                        : array('OperationError' => $e->detail->ApiFault->OperationErrors->OperationError);

                    foreach ($errors as $error)
                    {
                        print "OperationError\n";
                        printf("Code: %d\nMessage: %s\n", $error->Code, $error->Message);

                        switch ($error->Code)
                        {
                            case 106:   // UserIsNotAuthorized
                                break;
                            default:
                                print "Please see MSDN documentation for more details about the error code output above.\n";
                                break;
                        }
                    }
                }
            }
        }
        catch (Exception $e)
        {
            if ($e->getPrevious())
            {
                ; // Ignore fault exceptions that we already caught.
            }
            else
            {
                print $e->getCode()." ".$e->getMessage()."\n\n";
                print $e->getTraceAsString()."\n\n";
            }
        }
        print_r($campaignIds);die;
    }

public function test()
{
    try {
        //$proxy = ClientProxy::ConstructWithAccountAndCustomerId($wsdl, $UserName, $Password, $DeveloperToken, $AccountId, $CustomerId, null);
        $proxy = $this->proxy;
        // Specify one or more campaigns.
        $UserName = $this->user_name;
        $Password = $this->password;
        $DeveloperToken = $this->developer_token;
        $CustomerId = $this->customer_id;
        $AccountId = $this->account_id;

        $campaigns = array();

        $campaign = new Campaign();
        $campaign->Name = "test" . $_SERVER['REQUEST_TIME'];
        $campaign->Description = "Winter clothing line.";
        $campaign->BudgetType = BudgetLimitType::MonthlyBudgetSpendUntilDepleted;
        $campaign->MonthlyBudget = 10.00;
        $campaign->TimeZone = "PacificTimeUSCanadaTijuana";
        $campaign->DaylightSaving = true;

        $campaigns[] = $campaign;

        // Specify one or more ad groups.

        $adGroups = array();

        $endDate = new Date();
        $endDate->Day = 31;
        $endDate->Month = 12;
        $endDate->Year = 2016;

        $adGroup = new AdGroup();
        $adGroup->Name = "Women's Heated Ski Glove Sale";
        $adGroup->AdDistribution = AdDistribution::Search;
        $adGroup->BiddingModel = BiddingModel::Keyword;
        $adGroup->PricingModel = PricingModel::Cpc;
        $adGroup->StartDate = null;
        $adGroup->EndDate = $endDate;
        $adGroup->SearchBid = new Bid();
        $adGroup->SearchBid->Amount = 0.10;
        $adGroup->Language = "English";
        $adGroup->Status = AdGroupStatus::Paused;

        $adGroups[] = $adGroup;
        echo '<pre>';
        // Add the campaign, ad group
        var_dump(555);
        $campaignIds = $this->AddCampaigns($proxy, $AccountId, $campaigns);
        var_dump(666);
        $adGroupIds = $this->AddAdGroups($proxy, $campaignIds[0], $adGroups);
        var_dump(777);
        // Print the new assigned campaign and ad group identifiers

        $this->PrintCampaignIdentifiers($campaignIds);
        $this->PrintAdGroupIdentifiers($adGroupIds);







        // In this example only the second keyword should succeed. The Text of the first keyword exceeds the limit,
        // and the third keyword is a duplicate of the second keyword.

        $keywords = array();

        $keyword = new Keyword();
        $keyword->Bid = new Bid();
        $keyword->Bid->Amount = 0.47;
        $keyword->Param2 = "10% Off";
        $keyword->MatchType = MatchType::Broad;
        $keyword->Text = "Brand-A Gloves Brand-A Gloves Brand-A Gloves Brand-A Gloves Brand-A Gloves " .
            "Brand-A Gloves Brand-A Gloves Brand-A Gloves Brand-A Gloves Brand-A Gloves " .
            "Brand-A Gloves Brand-A Gloves Brand-A Gloves Brand-A Gloves Brand-A Gloves";
        $keywords[] = $keyword;

        $keyword = new Keyword();
        $keyword->Bid = new Bid();
        $keyword->Bid->Amount = 0.47;
        $keyword->Param2 = "10% Off";
        $keyword->MatchType = MatchType::Phrase;
        $keyword->Text = "Brand-A Gloves";
        $keywords[] = $keyword;

        $keyword = new Keyword();
        $keyword->Bid = new Bid();
        $keyword->Bid->Amount = 0.47;
        $keyword->Param2 = "10% Off";
        $keyword->MatchType = MatchType::Phrase;
        $keyword->Text = "Brand-A Gloves";
        $keywords[] = $keyword;

        // In this example only the first 3 ads should succeed.
        // The Title of the fourth ad is empty and not valid,
        // and the fifth ad is a duplicate of the second ad.

        $ads = array();

        for ($i = 0; $i < 5; $i++)
        {
            $textAd = new TextAd();
            $textAd->Text = "Huge Savings on women's shoes.";
            $textAd->DisplayUrl = "Contoso.com";

            // Destination URLs are deprecated and will be sunset in March 2016.
            // If you are currently using the Destination URL, you must upgrade to Final URLs.
            // Here is an example of a DestinationUrl you might have used previously.
            // $textAd->DestinationUrl = "http://www.contoso.com/womenshoesale/?season=spring&promocode=PROMO123";

            // To migrate from DestinationUrl to FinalUrls for existing ads, you can set DestinationUrl
            // to an empty string when updating the ad. If you are removing DestinationUrl,
            // then FinalUrls is required.
            // $textAd->DestinationUrl = "";

            // With FinalUrls you can separate the tracking template, custom parameters, and
            // landing page URLs.

            $textAd->FinalUrls = array();
            $textAd->FinalUrls[] = "http://www.contoso.com/womenshoesale";

            // Final Mobile URLs can also be used if you want to direct the user to a different page
            // for mobile devices.
            $textAd->FinalMobileUrls = array();
            $textAd->FinalMobileUrls[] = "http://mobile.contoso.com/womenshoesale";

            // You could use a tracking template which would override the campaign level
            // tracking template. Tracking templates defined for lower level entities
            // override those set for higher level entities.
            // In this example we are using the campaign level tracking template.
            $textAd->TrackingUrlTemplate = null;

            // Set custom parameters that are specific to this ad,
            // and can be used by the ad, ad group, campaign, or account level tracking template.
            // In this example we are using the campaign level tracking template.
//            $textAd->UrlCustomParameters = new CustomParameters();
//            $textAd->UrlCustomParameters->Parameters = array();
//            $customParameter1 = new CustomParameter();
//            $customParameter1->Key = "promoCode";
//            $customParameter1->Value = "PROMO" . ($i+1);
//            $textAd->UrlCustomParameters->Parameters[] = $customParameter1;
//            $customParameter2 = new CustomParameter();
//            $customParameter2->Key = "season";
//            $customParameter2->Value = "summer";
//            $textAd->UrlCustomParameters->Parameters[] = $customParameter2;

            $ads[] = new SoapVar($textAd, SOAP_ENC_OBJECT, 'TextAd', $proxy->GetNamespace());
        }

        $ads[0]->enc_value->Title = "Women's Shoe Sale";
        $ads[1]->enc_value->Title = "Women's Super Shoe Sale";
        $ads[2]->enc_value->Title = "Women's Red Shoe Sale";
        $ads[3]->enc_value->Title = "";
        $ads[4]->enc_value->Title = "Women's Super Shoe Sale";

        // Add the campaign, ad group, keywords, and ads

//        $campaignIds = AddCampaigns($proxy, $AccountId, $campaigns);
//        $adGroupIds = AddAdGroups($proxy, $campaignIds[0], $adGroups);

        $addKeywordsResponse = $this->AddKeywords($proxy, $adGroupIds[0], $keywords);
        $keywordIds = $addKeywordsResponse->KeywordIds->long;
        $keywordErrors = $addKeywordsResponse->PartialErrors->BatchError;

        $addAdsResponse = $this->AddAds($proxy, $adGroupIds[0], $ads);
        $adIds = $addAdsResponse->AdIds->long;
        $adErrors = $addAdsResponse->PartialErrors->BatchError;

        // Print the new assigned campaign and ad group identifiers

//        PrintCampaignIdentifiers($campaignIds);
//        PrintAdGroupIdentifiers($adGroupIds);

        // Print the new assigned keyword and ad identifiers, as well as any partial errors

        $this->PrintKeywordResults($keywords, $keywordIds, $keywordErrors);
        $this->PrintAdResults($ads, $adIds, $adErrors);





        // Bing Ads API Version 9 supports both Target and Target objects. You should use Target.
        // This sample compares Target and Target, and demonstrates the impact of updating the
        // DayTimeTarget, IntentOption, and RadiusTarget nested in a Target object.

        $campaignTarget = new Target();
        $campaignTarget->Name = "My Campaign Target";

//        $campaignDayTimeTarget = new DayTimeTarget();
//        $campaignDayTimeTargetBid = new DayTimeTargetBid();
//        $campaignDayTimeTargetBid->BidAdjustment = 10;
//        $campaignDayTimeTargetBid->Day = Day::Monday;
//        $campaignDayTimeTargetBid->FromHour = 1;
//        $campaignDayTimeTargetBid->ToHour = 12;
//        $campaignDayTimeTargetBid->FromMinute = Minute::Zero;
//        $campaignDayTimeTargetBid->ToMinute = Minute::FortyFive;
//        $campaignDayTimeTarget->Bids = array($campaignDayTimeTargetBid);
//        $campaignTarget->DayTime = $campaignDayTimeTarget;

        $campaignDeviceOSTarget = new DeviceOSTarget();
        $campaignDeviceOSTargetBid = new DeviceOSTargetBid();
        $campaignDeviceOSTargetBid->BidAdjustment = 10;
        $campaignDeviceOSTargetBid->DeviceName = "Tablets";
        $campaignDeviceOSTarget->Bids = array($campaignDeviceOSTargetBid);
        $campaignTarget->DeviceOS = $campaignDeviceOSTarget;

        $campaignLocationTarget = new LocationTarget();
        $campaignLocationTarget->IntentOption = IntentOption::PeopleSearchingForOrViewingPages;

        $campaignRadiusTarget = new RadiusTarget();
        $campaignRadiusTargetBid = new RadiusTargetBid();
        $campaignRadiusTargetBid->BidAdjustment = 50;
        $campaignRadiusTargetBid->LatitudeDegrees = 47.755367;
        $campaignRadiusTargetBid->LongitudeDegrees = -122.091827;
        $campaignRadiusTargetBid->Radius = 5;
        $campaignRadiusTargetBid->RadiusUnit = DistanceUnit::Miles;
        $campaignRadiusTargetBid->IsExcluded = false;
        $campaignRadiusTarget->Bids = array($campaignRadiusTargetBid);
        $campaignLocationTarget->RadiusTarget = $campaignRadiusTarget;
        $campaignTarget->Location = $campaignLocationTarget;

        $adGroupTarget = new Target();
        $adGroupTarget->Name = "My Ad Group Target";

        $adGroupDayTimeTarget = new DayTimeTarget();
        $adGroupDayTimeTargetBid = new DayTimeTargetBid();
        $adGroupDayTimeTargetBid->BidAdjustment = 10;
        $adGroupDayTimeTargetBid->Day = Day::Friday;
        $adGroupDayTimeTargetBid->FromHour = 1;
        $adGroupDayTimeTargetBid->ToHour = 12;
        $adGroupDayTimeTargetBid->FromMinute = Minute::Zero;
        $adGroupDayTimeTargetBid->ToMinute = Minute::FortyFive;
        $adGroupDayTimeTarget->Bids = array($adGroupDayTimeTargetBid);
        $adGroupTarget->DayTime = $adGroupDayTimeTarget;

        // Each customer has a target library that can be used to set up targeting for any campaign
        // or ad group within the specified customer.

        // Add a target to the library and associate it with the campaign.
        $campaignTargetId = $this->AddTargetsToLibrary($proxy, array($campaignTarget))->long[0];
        printf("Added Target Id: %d\n\n", $campaignTargetId);
        $this->SetTargetToCampaign($proxy, $campaignIds[0], $campaignTargetId);
        printf("Associated CampaignId %s with TargetId %s.\n\n", $campaignIds[0], $campaignTargetId);

        // Get and print the Target with the GetTargetsByIds operation
        print "Get Campaign Target: \n\n";
        $targets = $this->GetTargetsByIds($proxy, array($campaignTargetId));
        $this->PrintTarget($targets->Target[0]);

        // Add a target to the library and associate it with the ad group.
        $adGroupTargetId = $this->AddTargetsToLibrary($proxy, array($adGroupTarget))->long[0];
        printf("Added Target Id: %s\n\n", $adGroupTargetId);
        $this->SetTargetToAdGroup($proxy, $adGroupIds[0], $adGroupTargetId);
        printf("Associated AdGroupId %s with TargetId %s.\n\n", $adGroupIds[0], $adGroupTargetId);

        // Get and print the Target with the GetTargetsByIds operation
        print "Get AdGroup Target: \n\n";
        $targets = $this->GetTargetsByIds($proxy, array($adGroupTargetId));
        $this->PrintTarget($targets->Target[0]);

        // Update the ad group's Target object with additional target types.
        // Existing target types such as DayTime must be specified
        // or they will not be included in the updated target.

        $target = new Target();
        $target->Id = $adGroupTargetId;
        $target->Name = "My Target";

        $ageTarget = new AgeTarget();
        $ageTargetBid = new AgeTargetBid();
        $ageTargetBid->BidAdjustment = 10;
        $ageTargetBid->Age = AgeRange::EighteenToTwentyFive;
        $ageTarget->Bids = array($ageTargetBid);
        $target->Age = $ageTarget;

        $dayTimeTarget = new DayTimeTarget();
        $dayTimeTargetBid = new DayTimeTargetBid();
        $dayTimeTargetBid->BidAdjustment = 10;
        $dayTimeTargetBid->Day = Day::Friday;
        $dayTimeTargetBid->FromHour = 1;
        $dayTimeTargetBid->ToHour = 12;
        $dayTimeTargetBid->FromMinute = Minute::Zero;
        $dayTimeTargetBid->ToMinute = Minute::FortyFive;
        $dayTimeTarget->Bids = array($dayTimeTargetBid);
        $target->DayTime = $dayTimeTarget;

        $deviceOSTarget = new DeviceOSTarget();
        $deviceOSTargetBid = new DeviceOSTargetBid();
        $deviceOSTargetBid->BidAdjustment = 10;
        $deviceOSTargetBid->DeviceName = "Tablets";
        $deviceOSTarget->Bids = array($deviceOSTargetBid);
        $target->DeviceOS = $deviceOSTarget;

        $genderTarget = new GenderTarget();
        $genderTargetBid = new GenderTargetBid();
        $genderTargetBid->BidAdjustment = 10;
        $genderTargetBid->Gender = GenderType::Female;
        $genderTarget->Bids = array($genderTargetBid);
        $target->Gender = $genderTarget;

        $locationTarget = new LocationTarget();
        $locationTarget->IntentOption = IntentOption::PeopleSearchingForOrViewingPages;

        $countryTarget = new CountryTarget();
        $countryTargetBid = new CountryTargetBid();
        $countryTargetBid->BidAdjustment = 10;
        $countryTargetBid->CountryAndRegion = "US";
        $countryTargetBid->IsExcluded = false;
        $countryTarget->Bids = array($countryTargetBid);
        $locationTarget->CountryTarget = $countryTarget;

        $postalCodeTarget = new PostalCodeTarget();
        $postalCodeTargetBid = new PostalCodeTargetBid();
        $postalCodeTargetBid->BidAdjustment = 10;
        $postalCodeTargetBid->PostalCode = "98052, WA US";
        $postalCodeTargetBid->IsExcluded = false;
        $postalCodeTarget->Bids = array($postalCodeTargetBid);
        $locationTarget->PostalCodeTarget = $postalCodeTarget;

        $radiusTarget = new RadiusTarget();
        $radiusTargetBid = new RadiusTargetBid();
        $radiusTargetBid->BidAdjustment = 50;
        $radiusTargetBid->LatitudeDegrees = 47.755367;
        $radiusTargetBid->LongitudeDegrees = -122.091827;
        $radiusTargetBid->Radius = 11;
        $radiusTargetBid->RadiusUnit = DistanceUnit::Miles;
        $radiusTargetBid->IsExcluded = false;
        $radiusTarget->Bids = array($radiusTargetBid);
        $locationTarget->RadiusTarget = $radiusTarget;
        $target->Location = $locationTarget;

        // Update the Target object associated with the ad group.
        $this->UpdateTargetsInLibrary($proxy, array($target));
        print "Updated the ad group level target as a Target object.\n\n";

        // Get and print the Target with the GetTargetsByIds operation
        print "Get Campaign Target: \n\n";
        $targets = $this->GetTargetsByIds($proxy, array($campaignTargetId));
        $this->PrintTarget($targets->Target[0]);

        // Get and print the Target with the GetTargetsByIds operation
        print "Get AdGroup Target: \n\n";
        $targets = $this->GetTargetsByIds($proxy, array($adGroupTargetId));
        $this->PrintTarget($targets->Target[0]);

        // Get all new and existing targets in the customer library, whether or not they are
        // associated with campaigns or ad groups.

        $allTargetsInfo = $this->GetTargetsInfoFromLibrary($proxy);
        print "All target identifiers and names from the customer library: \n\n";
        $this->PrintTargetsInfo($allTargetsInfo->TargetInfo);

        // Delete the campaign, ad group, and targets that were previously added.
        // DeleteCampaigns would remove the campaign and ad group, as well as the association
        // between ad groups and campaigns. To explicitly delete the association between an entity
        // and the target, use DeleteTargetFromCampaign and DeleteTargetFromAdGroup respectively.

//        $this->DeleteTargetFromCampaign($proxy, $campaignIds[0]);
//        $this->DeleteTargetFromAdGroup($proxy, $adGroupIds[0]);

//        $this->DeleteCampaigns($proxy, $AccountId, array($campaignIds[0]));
//        printf("Deleted CampaignId %s\n\n", $campaignIds[0]);

        // DeleteCampaigns deletes the association between the campaign and target, but does not
        // delete the target from the customer library.
        // Call the DeleteTargetsFromLibrary operation for each target that you want to delete.
        // You must specify an array with exactly one item.

//        $this->DeleteTargetsFromLibrary($proxy, array($campaignTargetId));
//        printf("Deleted TargetId %s\n\n", $campaignTargetId);
//
//        $this->DeleteTargetsFromLibrary($proxy, array($adGroupTargetId));
//        printf("Deleted TargetId %s\n\n", $adGroupTargetId);
    } catch (SoapFault $e) {
        // Output the last request/response.

        print "\nLast SOAP request/response:\n";
        print $proxy->GetWsdl() . "\n";
        print $proxy->GetService()->__getLastRequest() . "\n";
        print $proxy->GetService()->__getLastResponse() . "\n";

        // Campaign Management service operations can throw AdApiFaultDetail.
        if (isset($e->detail->AdApiFaultDetail)) {
            // Log this fault.

            print "The operation failed with the following faults:\n";

            $errors = is_array($e->detail->AdApiFaultDetail->Errors->AdApiError)
                ? $e->detail->AdApiFaultDetail->Errors->AdApiError
                : array('AdApiError' => $e->detail->AdApiFaultDetail->Errors->AdApiError);

            // If the AdApiError array is not null, the following are examples of error codes that may be found.
            foreach ($errors as $error) {
                print "AdApiError\n";
                printf("Code: %d\nError Code: %s\nMessage: %s\n", $error->Code, $error->ErrorCode, $error->Message);

                switch ($error->Code) {
                    default:
                        print "Please see MSDN documentation for more details about the error code output above.\n";
                        break;
                }
            }
        } // Campaign Management service operations can throw ApiFaultDetail.
        elseif (isset($e->detail->EditorialApiFaultDetail)) {
            // Log this fault.

            print "The operation failed with the following faults:\n";

            // If the BatchError array is not null, the following are examples of error codes that may be found.
            if (!empty($e->detail->EditorialApiFaultDetail->BatchErrors)) {
                $errors = is_array($e->detail->EditorialApiFaultDetail->BatchErrors->BatchError)
                    ? $e->detail->EditorialApiFaultDetail->BatchErrors->BatchError
                    : array('BatchError' => $e->detail->EditorialApiFaultDetail->BatchErrors->BatchError);

                foreach ($errors as $error) {
                    printf("BatchError at Index: %d\n", $error->Index);
                    printf("Code: %d\nError Code: %s\nMessage: %s\n", $error->Code, $error->ErrorCode, $error->Message);

                    switch ($error->Code) {
                        default:
                            print "Please see MSDN documentation for more details about the error code output above.\n";
                            break;
                    }
                }
            }

            // If the EditorialError array is not null, the following are examples of error codes that may be found.
            if (!empty($e->detail->EditorialApiFaultDetail->EditorialErrors)) {
                $errors = is_array($e->detail->EditorialApiFaultDetail->EditorialErrors->EditorialError)
                    ? $e->detail->EditorialApiFaultDetail->EditorialErrors->EditorialError
                    : array('BatchError' => $e->detail->EditorialApiFaultDetail->EditorialErrors->EditorialError);

                foreach ($errors as $error) {
                    printf("EditorialError at Index: %d\n", $error->Index);
                    printf("Code: %d\nError Code: %s\nMessage: %s\n", $error->Code, $error->ErrorCode, $error->Message);
                    printf("Appealable: %s\nDisapproved Text: %s\nCountry: %s\n", $error->Appealable, $error->DisapprovedText, $error->PublisherCountry);

                    switch ($error->Code) {
                        default:
                            print "Please see MSDN documentation for more details about the error code output above.\n";
                            break;
                    }
                }
            }

            // If the OperationError array is not null, the following are examples of error codes that may be found.
            if (!empty($e->detail->EditorialApiFaultDetail->OperationErrors)) {
                $errors = is_array($e->detail->EditorialApiFaultDetail->OperationErrors->OperationError)
                    ? $e->detail->EditorialApiFaultDetail->OperationErrors->OperationError
                    : array('OperationError' => $e->detail->EditorialApiFaultDetail->OperationErrors->OperationError);

                foreach ($errors as $error) {
                    print "OperationError\n";
                    printf("Code: %d\nError Code: %s\nMessage: %s\n", $error->Code, $error->ErrorCode, $error->Message);

                    switch ($error->Code) {
                        default:
                            print "Please see MSDN documentation for more details about the error code output above.\n";
                            break;
                    }
                }
            }
        }
    } catch (Exception $e) {
        if ($e->getPrevious()) {
            ; // Ignore fault exceptions that we already caught.
        } else {
            print $e->getCode() . " " . $e->getMessage() . "\n\n";
            print $e->getTraceAsString() . "\n\n";
        }
    }
}
// Adds one or more campaigns to the specified account.

    function AddCampaigns($proxy, $accountId, $campaigns)
    {
        $request = new AddCampaignsRequest();
        $request->AccountId = $accountId;
        $request->Campaigns = $campaigns;

        return $proxy->GetService()->AddCampaigns($request)->CampaignIds->long;
    }

// Deletes one or more campaigns from the specified account.

    function DeleteCampaigns($proxy, $accountId, $campaignIds)
    {
        $request = new DeleteCampaignsRequest();
        $request->AccountId = $accountId;
        $request->CampaignIds = $campaignIds;

        $proxy->GetService()->DeleteCampaigns($request);
    }

// Adds one or more ad groups to the specified campaign.

    function AddAdGroups($proxy, $campaignId, $adGroups)
    {
        $request = new AddAdGroupsRequest();
        $request->CampaignId = $campaignId;
        $request->AdGroups = $adGroups;

        return $proxy->GetService()->AddAdGroups($request)->AdGroupIds->long;
    }

// Prints the campaign identifiers for each campaign added.

    function PrintCampaignIdentifiers($campaignIds)
    {
        if (count((array)$campaignIds) == 0) {
            return;
        }

        foreach ($campaignIds as $id) {
            printf("Campaign successfully added and assigned CampaignId %d\n\n", $id);
        }
    }


// Prints the ad groupd identifiers for each ad group added.

    function PrintAdGroupIdentifiers($adGroupIds)
    {
        if (count((array)$adGroupIds) == 0) {
            return;
        }

        foreach ($adGroupIds as $id) {
            printf("AdGroup successfully added and assigned AdGroupId %d\n\n", $id);
        }
    }

// Gets all target info from the customer library.

    function GetTargetsInfoFromLibrary($proxy)
    {
        $request = new GetTargetsInfoFromLibraryRequest();

        return $proxy->GetService()->GetTargetsInfoFromLibrary($request)->TargetsInfo;
    }

// Gets the list of Target objects given the specified target identifiers.

    function GetTargetsByIds($proxy, $targetIds)
    {
        $request = new GetTargetsByIdsRequest();
        $request->TargetIds = $targetIds;

        return $proxy->GetService()->GetTargetsByIds($request)->Targets;
    }

// Adds the specified Target object to the customer library.
// The operation requires exactly one Target in a list.

    function AddTargetsToLibrary($proxy, $targets)
    {
        $request = new AddTargetsToLibraryRequest();
        $request->Targets = $targets;

        return $proxy->GetService()->AddTargetsToLibrary($request)->TargetIds;
    }

// Updates the specified Target object within the customer library.
// The operation requires exactly one Target in a list.

    function UpdateTargetsInLibrary($proxy, $targets)
    {
        $request = new UpdateTargetsInLibraryRequest();
        $request->Targets = $targets;

        $proxy->GetService()->UpdateTargetsInLibrary($request);
    }

// Deletes the specified target from the customer library.
// The operation requires exactly one identifier in a list.

    function DeleteTargetsFromLibrary($proxy, $targetIds)
    {
        $request = new DeleteTargetsFromLibraryRequest();
        $request->TargetIds = $targetIds;

        $proxy->GetService()->DeleteTargetsFromLibrary($request);
    }

// Removes the target association from the specified campaign.
// Does not delete the target or the campaign.

    function DeleteTargetFromCampaign($proxy, $campaignId)
    {
        $request = new DeleteTargetFromCampaignRequest();
        $request->CampaignId = $campaignId;

        $proxy->GetService()->DeleteTargetFromCampaign($request);
    }

// Removes the target association from the specified ad group.
// Does not delete the target or the ad group.

    function DeleteTargetFromAdGroup($proxy, $adGroupId)
    {
        $request = new DeleteTargetFromAdGroupRequest();
        $request->AdGroupId = $adGroupId;

        $proxy->GetService()->DeleteTargetFromAdGroup($request);
    }

// Associates the specified campaign and target.

    function SetTargetToCampaign($proxy, $campaignId, $targetId)
    {
        $request = new SetTargetToCampaignRequest();
        $request->CampaignId = $campaignId;
        $request->TargetId = $targetId;

        $proxy->GetService()->SetTargetToCampaign($request);
    }

// Associates the specified ad group and target.

    function SetTargetToAdGroup($proxy, $adGroupId, $targetId)
    {
        $request = new SetTargetToAdGroupRequest();
        $request->AdGroupId = $adGroupId;
        $request->TargetId = $targetId;

        $proxy->GetService()->SetTargetToAdGroup($request);
    }

// Prints the info for each target.

    function PrintTargetsInfo($targetsInfo)
    {
        if (count((array)$targetsInfo) == 0) {
            return;
        }

        foreach ($targetsInfo as $info) {
            printf("Target Id: %d\n", $info->Id);
            printf("Target Name: %s\n\n", $info->Name);
        }
    }

// Prints the specified Target object.

    function PrintTarget($target)
    {
        if (count((array)$target) == 0) {
            return;
        }

        printf("Target Id: %d\n", $target->Id);
        printf("Target Name: %s\n\n", $target->Name);

        if (!is_null($target->Age)) {
            print "AgeTarget:\n";
            foreach ($target->Age->Bids->AgeTargetBid as $bid) {
                printf("\tBidAdjustment: %d\n", $bid->BidAdjustment);
                printf("\tAge: %s\n\n", $bid->Age);
            }
        }
        if (!is_null($target->DayTime)) {
            print "DayTimeTarget:\n";
            foreach ($target->DayTime->Bids->DayTimeTargetBid as $bid) {
                printf("\tBidAdjustment: %d\n", $bid->BidAdjustment);
                printf("\tDay: %s\n", $bid->Day);
                printf("\tFromHour: %s\n", $bid->FromHour);
                printf("\tToHour: %s\n", $bid->ToHour);
                printf("\tFromMinute: %s\n", $bid->FromMinute);
                printf("\tToMinute: %s\n\n", $bid->ToMinute);
            }
        }
        if (!is_null($target->DeviceOS)) {
            print "DeviceOSTarget:\n";
            foreach ($target->DeviceOS->Bids->DeviceOSTargetBid as $bid) {
                printf("\tBidAdjustment: %d\n", $bid->BidAdjustment);
                printf("\tDeviceName: %s\n", $bid->DeviceName);
                print "\n";
            }
        }
        if (!is_null($target->Gender)) {
            print "GenderTarget:\n";
            foreach ($target->Gender->Bids->GenderTargetBid as $bid) {
                printf("\tBidAdjustment: %d\n", $bid->BidAdjustment);
                printf("\tGender: %s\n\n", $bid->Gender);
            }
        }
        if (!is_null($target->Location)) {
            print "LocationTarget:\n";
            printf("\tIntentOption: %s\n\n", $target->Location->IntentOption);
            if (!is_null($target->Location->CityTarget)) {
                print "\tCityTarget:\n";
                foreach ($target->Location->CityTarget->Bids->CityTargetBid as $bid) {
                    printf("\t\tBidAdjustment: %s\n", $bid->BidAdjustment);
                    printf("\t\tCity: %s\n\n", $bid->City);
                }
            }
            if (!is_null($target->Location->CountryTarget)) {
                print "\tCountryTarget:\n";
                foreach ($target->Location->CountryTarget->Bids->CountryTargetBid as $bid) {
                    printf("\t\tBidAdjustment: %d\n", $bid->BidAdjustment);
                    printf("\t\tCountryAndRegion: %s\n", $bid->CountryAndRegion);
                    printf("\t\tIsExcluded: %s\n\n", $bid->IsExcluded ? "True" : "False");
                }
            }
            if (!is_null($target->Location->MetroAreaTarget)) {
                print "\tMetroAreaTarget:\n";
                foreach ($target->Location->MetroAreaTarget->Bids->MetroAreaTargetBid as $bid) {
                    printf("\t\tBidAdjustment: %d\n", $bid->BidAdjustment);
                    printf("\t\tMetroArea: %s\n", $bid->MetroArea);
                    printf("\t\tIsExcluded: %s\n\n", $bid->IsExcluded ? "True" : "False");
                }
            }
            if (!is_null($target->Location->PostalCodeTarget)) {
                print "\tPostalCodeTarget:\n";
                foreach ($target->Location->PostalCodeTarget->Bids->PostalCodeTargetBid as $bid) {
                    printf("\t\tBidAdjustment: %d\n", $bid->BidAdjustment);
                    printf("\t\tPostalCode: %s\n", $bid->PostalCode);
                    printf("\t\tIsExcluded: %s\n\n", $bid->IsExcluded ? "True" : "False");
                }
            }
            if (!is_null($target->Location->RadiusTarget)) {
                print "\tRadiusTarget:\n";
                foreach ($target->Location->RadiusTarget->Bids->RadiusTargetBid as $bid) {
                    printf("\t\tBidAdjustment: %d\n", $bid->BidAdjustment);
                    printf("\t\tLatitudeDegrees: %f\n", $bid->LatitudeDegrees);
                    printf("\t\tLongitudeDegrees: %f\n", $bid->LongitudeDegrees);
                    printf("\t\tRadius: %s %s\n\n", $bid->Radius, $bid->RadiusUnit);
                }
            }
            if (!is_null($target->Location->StateTarget)) {
                print "\tStateTarget:\n";
                foreach ($target->Location->StateTarget->Bids->StateTargetBid as $bid) {
                    printf("\t\tBidAdjustment: %d\n", $bid->BidAdjustment);
                    printf("\t\tState: %s\n", $bid->State);
                    printf("\t\tIsExcluded: %s\n\n", $bid->IsExcluded ? "True" : "False");
                }
            }
        }

        print "\n";
    }

    function AddKeywords($proxy, $adGroupId, $keywords)
    {
        // Set the request information.

        $request = new AddKeywordsRequest();
        $request->AdGroupId = $adGroupId;
        $request->Keywords = $keywords;

        return $proxy->GetService()->AddKeywords($request);
    }

// Adds one or more ads to the specified ad group.

    function AddAds($proxy, $adGroupId, $ads)
    {
        // Set the request information.

        $request = new AddAdsRequest();
        $request->AdGroupId = $adGroupId;
        $request->Ads = $ads;

        return $proxy->GetService()->AddAds($request);
    }

    // Prints the keyword identifiers, as well as any partial errors

    function PrintKeywordResults($keywords, $keywordIds, $partialErrors)
    {
        if(empty($keywordIds))
        {
            return;
        }

        // Print the identifier of each successfully added keyword.

        for ($index = 0; $index < count($keywords); $index++ )
        {
            // The array of keyword identifiers equals the size of the attempted keywords. If the element
            // is not empty, the keyword at that index was added successfully and has a keyword identifer.

            if (!empty($keywordIds[$index]))
            {
                printf("Keyword[%d] (Text:%s) successfully added and assigned KeywordId %s\n",
                    $index,
                    $keywords[$index]->Text,
                    $keywordIds[$index] );
            }
        }

        // Print the error details for any keyword not successfully added.
        // Note also that multiple error reasons may exist for the same attempted keyword.

        foreach ($partialErrors as $error)
        {
            // The index of the partial errors is equal to the index of the list
            // specified in the call to AddKeywords.

            printf("\nKeyword[%d] (Text:%s) not added due to the following error:\n", $error->Index, $keywords[$error->Index]->Text);

            printf("\tIndex: %d\n", $error->Index);
            printf("\tCode: %d\n", $error->Code);
            printf("\tErrorCode: %s\n", $error->ErrorCode);
            printf("\tMessage: %s\n", $error->Message);

            // In the case of an EditorialError, more details are available

            if ($error->Type == "EditorialError" && $error->ErrorCode == "CampaignServiceEditorialValidationError")
            {
                printf("\tDisapprovedText: %s\n", $error->DisapprovedText);
                printf("\tLocation: %s\n", $error->Location);
                printf("\tPublisherCountry: %s\n", $error->PublisherCountry);
                printf("\tReasonCode: %d\n", $error->ReasonCode);
            }
        }

        print "\n";
    }


// Prints the ad identifiers, as well as any partial errors

    function PrintAdResults($ads, $adIds, $partialErrors)
    {
        if(empty($adIds))
        {
            return;
        }

        $attributeValues = array();

        // Print the identifier of each successfully added ad.

        for ($index = 0; $index < count($ads); $index++ )
        {
            // Determine the type of ad. Prepare the corresponding attribute value to be printed,
            // both for successful new ads and partial errors.

            if($ads[$index]->enc_stype === "TextAd")
            {
                $attributeValues[] = "Title:" . $ads[$index]->enc_value->Title;
            }
            else if($ads[$index]->enc_stype === "ProductAd")
            {
                $attributeValues[] = "PromotionalText:" . $ads[$index]->enc_value->PromotionalText;
            }
            else
            {
                $attributeValues[] = "Unknown Ad Type";
            }

            // The array of ad identifiers equals the size of the attempted ads. If the element
            // is not empty, the ad at that index was added successfully and has an ad identifer.

            if (!empty($adIds[$index]))
            {
                printf("Ad[%d] (%s) successfully added and assigned AdId %s\n",
                    $index,
                    $attributeValues[$index],
                    $adIds[$index] );

                print "DestinationUrl: " . $ads[$index]->enc_value->DestinationUrl . "\n";
                print("FinalMobileUrls: \n");
                foreach ($ads[$index]->enc_value->FinalMobileUrls as $finalMobileUrl)
                {
                    print("\t" . $finalMobileUrl . "\n");
                }
                print("FinalUrls: \n");
                foreach ($ads[$index]->enc_value->FinalUrls as $finalUrl)
                {
                    print("\t" . $finalUrl . "\n");
                }
                print("TrackingUrlTemplate: " . $ads[$index]->enc_value->TrackingUrlTemplate . "\n");
                print("UrlCustomParameters: \n");
                if ($ads[$index]->enc_value->UrlCustomParameters != null && $ads[$index]->enc_value->UrlCustomParameters->Parameters != null)
                {
                    foreach ($ads[$index]->enc_value->UrlCustomParameters->Parameters as $customParameter)
                    {
                        print("\tKey: " . $customParameter->Key . "\n");
                        print("\tValue: " . $customParameter->Value . "\n");
                    }
                }
                print "\n";
            }
        }


        // Print the error details for any ad not successfully added.
        // Note also that multiple error reasons may exist for the same attempted ad.

        foreach ($partialErrors as $error)
        {
            // The index of the partial errors is equal to the index of the list
            // specified in the call to AddAds.

            printf("\nAd[%d] (%s) not added due to the following error:\n", $error->Index, $attributeValues[$error->Index]);

            printf("\tIndex: %d\n", $error->Index);
            printf("\tCode: %d\n", $error->Code);
            printf("\tErrorCode: %s\n", $error->ErrorCode);
            printf("\tMessage: %s\n", $error->Message);

            // In the case of an EditorialError, more details are available

            if ($error->Type == "EditorialError" && $error->ErrorCode == "CampaignServiceEditorialValidationError")
            {
                printf("\tDisapprovedText: %s\n", $error->DisapprovedText);
                printf("\tLocation: %s\n", $error->Location);
                printf("\tPublisherCountry: %s\n", $error->PublisherCountry);
                printf("\tReasonCode: %d\n", $error->ReasonCode);
            }
        }

        print "\n";
    }
}
