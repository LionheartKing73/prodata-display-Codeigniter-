<?php
/**
 * CI library implementation of Google AdX API
 *
 * Using this library following actions can be implements:
 *
 * 1. Insert New Creative to Google AdX
 * 2. Check Approval/Disapproval status of creative with details reason
 * 3. Get Creatives listing ( w/ filtering and pagination )
 * 4. Retrieve An A/C account details by AdX Account ID
 *
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

define('GOOGLE_API_CLIEN_PATH', APPPATH . "third_party/GoogleApiClient/");
require_once GOOGLE_API_CLIEN_PATH . "autoload.php";

class Google_adx {

    protected $client;
    protected $adx_service;
    protected $adx_account_id;
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->adx_account_id = 212286445; // ProData Google AdX A/C ID

        // make Google Client instance
        $this->client = new Google_Client();
        $this->client->setApplicationName("Prodata_Google_Adx_Testing");
        $key_file_location = GOOGLE_API_CLIEN_PATH . "auth_config.json";
        $this->client->setAuthConfig($key_file_location);
        $this->client->addScope('https://www.googleapis.com/auth/adexchange.buyer');

        // AdX Buyer service
        $this->adx_service = new Google_Service_AdExchangeBuyer($this->client);

        // refresh access token and set to session
        if ( isset($_SESSION['GOOGLE_ADX_API_SERVICE_TOKEN']) ) {
            $this->client->setAccessToken($_SESSION['GOOGLE_ADX_API_SERVICE_TOKEN']);
        }

        if ( $this->client->isAccessTokenExpired() ) {
            $this->client->refreshTokenWithAssertion();
        }

        $_SESSION['GOOGLE_ADX_API_SERVICE_TOKEN'] = $this->client->getAccessToken();
    }

    /**
     * Return list of required parameters
     * to create a creative successfully
     *
     * @return array
     */
    protected function get_reqired_params() {
        return array(
            /*array('name' => 'account_id',
                   'display' => 'Account id',
                   'required' => true),*/
            array('name' => 'buyer_creative_id',
                   'display' => 'Buyer creative id',
                   'required' => true),
            array('name' => 'advertiser_name',
                   'display' => 'Advertiser name',
                   'required' => true),
            array('name' => 'html_snippet',
                   'display' => 'HTML Snippet',
                   'required' => true),
            array('name' => 'click_through_urls',
                   'display' => 'Click through URLs',
                   'required' => true),
            array('name' => 'width',
                   'display' => 'Width',
                   'required' => true),
            array('name' => 'height',
                   'display' => 'Height',
                   'required' => true)
        );
    }

    /**
     * Validate New Creative Parameters
     *
     * @param  array $params [description]
     * @return mixed
     */
    private function creative_insert_parameters_validation(array $params)
    {
        $required_params = $this->get_reqired_params();
        $required_keys = array_column($required_params, 'name');
        $param_keys = array_keys($params);
        $missing_params = array_diff($required_keys, $param_keys);
        if ( !empty($missing_params) ) {
            $err = 'Parameters: '. implode(',', $missing_params) .' are required';
            return [
                'status' => 'ERROR',
                'error' => $err
            ];
            exit;
        }

        return true;
    }

    /**
     * Get accounts list
     *
     * @return array
     */
    public function get_accounts_list()
    {
        $accounts = $this->adx_service->accounts->listAccounts();
        return (array)$accounts->toSimpleObject();
    }

    /**
     * Get AdX Account Detail by Account ID
     *
     * @param  integer $account_id
     * @return mixed
     */
    public function get_account($account_id = null)
    {
        empty($account_id) && $account_id = $this->adx_account_id;
        $account = (array)$this->adx_service->accounts->get($account_id)->toSimpleObject();
        return $account;
    }

    /**
     * Insert Creative to Google AdX pipeline to verification
     *
     * @param  array $params
     * @return void
     */
    public function insert_creative(array $params)
    {
        // If not account Id set in params, then
        // set ProData Account's ID by default
        if ( empty($params['account_id']) ) {
            $params['account_id'] = $this->adx_account_id;
        }

        // validate new creative parameters
        $validity = $this->creative_insert_parameters_validation($params);
        if ( $validity !== true ) return $validity;

        // set creative config
        $creative_service = new Google_Service_AdExchangeBuyer_Creative($this->client);
        $creative_service->accountId = $params['account_id'];
        $creative_service->buyerCreativeId = $params['buyer_creative_id'];
        $creative_service->advertiserName = $params['advertiser_name'];
        $creative_service->width = $params['width'];
        $creative_service->height = $params['height'];
        $creative_service->clickThroughUrl = $params['click_through_urls'];
        $creative_service->HTMLSnippet = $params['html_snippet'];

        // create creatives
        $creative_resource = null;
        try {
            $new_creative = $this->adx_service->creatives->insert($creative_service);
            $creative_resource = (array)$new_creative->toSimpleObject();
            $creative_resource['HTMLSnippet'] = htmlspecialchars($creative_resource['HTMLSnippet']);
        } catch(Google_Service_Exception $e) {
            return [
                'status' => 'ERROR',
                'errors' => $e->getErrors()
            ];
        }

        return [
            'status' => 'SUCCESS',
            'creative' => $creative_resource
        ];
    }

    public function get_creative($buyer_creative_id, $account_id = null)
    {
        empty($account_id) && $account_id = $this->adx_account_id;
        $creative_resource = null;
        try {
            $creative = $this->adx_service->creatives->get($account_id, $buyer_creative_id);
            $creative_resource = (array)$creative->toSimpleObject();
            $creative_resource['HTMLSnippet'] = htmlspecialchars($creative_resource['HTMLSnippet']);
        } catch (Google_Service_Exception $e) {
            return [
                'status' => 'ERROR',
                'errors' => $e->getErrors()
            ];
        }

        return [
            'status' => 'SUCCESS',
            'creative' => $creative_resource
        ];
    }

    /**
     * List All Creatives
     * By default, it's showing all creatives
     *
     * @param array $opt_params
     * @param array $creatives
     * @return void
     */
    public function get_creatives_list(array $opt_params = [], array $creatives = [])
    {
        $query_params = [
            'openAuctionStatusFilter' => 'disapproved',
            'maxResults' => 10
        ];
        $query_params = array_merge($query_params, $opt_params);

        // pull creatives
        $list_creatives = $this->adx_service->creatives->listCreatives($query_params);
        $items = $list_creatives->getItems();
        $next_page_token = $list_creatives->getNextPageToken();

        if ( !empty( $items ) ) {

            foreach ( $items as $item ) {
                $item = (array)$item->toSimpleObject();
                $item['HTMLSnippet'] = htmlspecialchars($item['HTMLSnippet']);
                $creatives[] = $item;

                /*$cc = [];

                $cc['account_id'] = $item->getAccountId();
                $cc['advertiser_name'] = $item->getAdvertiserName();
                $cc['advertiser_id'] = $item->getAdvertiserId();
                $cc['buyer_creative_id'] = $item->getBuyerCreativeId();
                $cc['api_upload_timestamp'] = $item->getApiUploadTimestamp();
                $cc['click_through_urls'] = $item->getClickThroughUrl();

                $cc['html_snippet'] = htmlspecialchars($item->getHtmlSnippet());
                $cc['attributes'] = $item->getAttribute();
                $cc['imp_url'] = $item->getImpressionTrackingUrl();
                $cc['deal_status'] = $item->getDealsStatus();
                $cc['open_auction_status'] = $item->getOpenAuctionStatus();
                $cc['width'] = $item->getWidth();
                $cc['height'] = $item->getHeight();

                $corrections = $item->getCorrections();

                $restrictions = $item->getServingRestrictions();
                $cc['restrictions'] = [];

                foreach ( $restrictions as $restriction ) {
                    $issue = [];
                    $issue['reason'] = $restriction->getReason();

                    // extract contexts
                    $contexts = $restriction->getContexts();
                    $issue['contexts'] = [];
                    foreach ( $contexts as $context ) {
                        $context_detail = [
                            'auctiion_type' => $context->getAuctionType(),
                            'context_type' => $context->getContextType(),
                            'platform' => $context->getPlatform()
                        ];
                        $issue['contexts'][] = $context_detail;
                    }

                    // extract disapproval reasons
                    $disapproval_reasons = $restriction->getDisapprovalReasons();
                    $issue['disapproval_reasons'] = [];
                    foreach ( $disapproval_reasons as $disapprove_reason ) {
                        $disapprove_detail = [
                            'details' => $disapprove_reason->getDetails(),
                            'reason' => $disapprove_reason->getReason()
                        ];
                        $issue['disapproval_reasons'][] = $disapprove_detail;
                    }

                    $cc['restrictions'][] = $issue;
                }

                $creatives[] = $cc;*/
            }
        }

        // check if next page available
        if ( !empty($next_page_token) ) {
            $this->get_creatives_list([
                'pageToken' => $next_page_token
            ], $creatives);
        }

        return $creatives;
    }

    public function set_budget($billing_id, $amount)
    {
        $account_id = $this->adx_account_id;

        $budget = new Google_Service_AdExchangeBuyer_Budget();
        $budget->accountId = $this->adx_account_id;
        $budget->billingId = $billing_id;
        $budget->budgetAmount = $amount;

        try {
            $res = $this->adx_service->budget->update($this->adx_account_id, $billing_id, $budget);
            return [
                'status' => 'SUCCESS',
                'budget_id' => $res->getId()
            ];
        } catch(Google_Service_Exception $e) {
            return [
                'status' => 'ERROR',
                'errors' => $e->getErrors()
            ];
        }

    }

    public function patch_pretargeting_config($config_id, array $params)
    {
        $config = new Google_Service_AdExchangeBuyer_PretargetingConfig();

        //$config->configName = 'AYTestConfig';
        $config->isActive = true; //!empty($params['is_active']) && $params['is_active'] === true ? true : false;
        $config->dimensions = $params['dims'];

        // TODO: for now we're not sending ZIP Geo Criteria as pretargeting to broaden the settings
        $config->geoCriteriaIds = $geoIds = $params['states_criteria_ids'];

        // Criteria Attribute
        /*$config->supportedCreativeAttributes = [
            2,  // CreativeType: Image/Rich Media
            3,  // VideoType: Adobe Flash FLV
            7,  // Tagging: IsTagged
            8,  // CookieTargeting: IsCookieTargeted
            9,  // UserInterestTargeting: IsUserInterestTargeted
            12, // ExpandingDirection: ExpandingNone
            13, // ExpandingDirection: ExpandingUp
            14, // ExpandingDirection: ExpandingDown
            15, // ExpandingDirection: ExpandingLeft
            16, // ExpandingDirection: ExpandingRight
            17, // ExpandingDirection: ExpandingUpLeft
            18, // ExpandingDirection: ExpandingUpRight
            19, // ExpandingDirection: ExpandingDownLeft
            20, // ExpandingDirection: ExpandingDownRight
            25, // ExpandingDirection: ExpandingUpOrDown
            26, // ExpandingDirection: ExpandingLeftOrRight
            27, // ExpandingDirection: ExpandingAnyDiagonal
            28, // ExpandingAction: RolloverToExpand
            //30, // InstreamVastVideoType: Vpaid
            32, // MraidType: MRAID
            44, // InstreamVastVideoType: Skippable Instream Video
            48, // RichMediaCapabilityType: RichMediaCapabilityNonSSL
            69, // InstreamVastVideoType: Non Skippable Instream Video
            //70, // NativeEligibility: Native Eligible
            71, // InstreamVastVideoType: Non Vpaid
            72, // NativeEligibility: Native Not Eligible
            73, // InterstitialSize: AnyInterstitial
            74, // InterstitialSize: NonInterstitial
        ];*/

        // TODO: For now inactive
        /*$config->geoCriteriaIds = $geoIds = array_merge(
            $params['states_criteria_ids'],
            $params['zips_criteria_ids']
        );*/

        /*$config->languages = array('en', 'es');
        $config->verticals = array(
            11, // Home & Garden
            16, // News
            29, // Real Estate
        );*/
        $res = false;
        try {
            $res = $this->adx_service->pretargetingConfig->patch($this->adx_account_id, $config_id, $config);
        } catch(Exception $e){}

        return $res;
    }

    public function get_pretargeting_configs_list()
    {
        $pretargeting_configs_list = $this->adx_service->pretargetingConfig->listPretargetingConfig($this->adx_account_id);
        $lists = $pretargeting_configs_list->getItems();

        foreach ( $lists as $config ) {
            $list = $config->toSimpleObject();
            debug($list, 0);
            $config_id = $list->configId;
            $config_name = $list->configName;
            $geo_criteria_ids = $list->geoCriteriaIds;
            $is_active = $list->isActive;
            $verticals = $list->verticals;
        }
    }

    public function create_pretargeting_config(array $pretargeting_configs)
    {
        if ( empty($pretargeting_configs)
            || empty($pretargeting_configs['dims'])
            || (empty($pretargeting_configs['states_criteria_ids'])
                && empty($pretargeting_configs['zips_criteria_ids'])) ) return false;

        $config = new Google_Service_AdExchangeBuyer_PretargetingConfig();

        $config->configName = 'AYTestConfig';
        $config->isActive = true;
        $config->creativeType = array(
            'PRETARGETING_CREATIVE_TYPE_HTML'
        );
        $config->dimensions = $pretargeting_configs['dims'];
        $config->geoCriteriaIds = array_merge(
            $pretargeting_configs['states_criteria_ids'],
            $pretargeting_configs['zips_criteria_ids']
        );

        $config->languages = array('en', 'es');
        $config->verticals = array(
            11, // Home & Garden
            16, // News
            29, // Real Estate
        );

        try {
            $pretargeting = $this->adx_service->pretargetingConfig->insert($this->adx_account_id, $config);
            $config_id = $pretargeting->getConfigId();
            $billing_id = $pretargeting->getBillingId();

            // set budget
            $budget_id = null;
            $res = $this->set_budget($billing_id, $amount = 100);
            if ( $res['status'] == 'SUCCESS' ) $budget_id = $res['budget_id'];

            return [
                'status' => 'SUCCESS',
                'config_id' => $config_id,
                'billing_id' => $billing_id,
                'budget_id' => $budget_id,
                'budget_update_status' => !empty($budget_id) ? 'SUCCESS' : 'FAILED'
            ];
        } catch(Google_Service_Exception $e) {
            return [
                'status' => 'ERROR',
                'errors' => $e->getErrors()
            ];
        }

        return $config_id;
    }

    public function delete_pretargeting_config($config_id)
    {
        $delete = $this->adx_service->pretargetingConfig->delete($this->adx_account_id, $config_id);
        return $delete->getStatusCode();
    }
}