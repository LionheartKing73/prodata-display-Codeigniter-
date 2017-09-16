<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/* Example code to access Gemini API: Fetch advertiser information, create a new campaign and read specific campaign data*/

require_once APPPATH.'third_party/yahoo/YahooOAuth2.class.php'; #Download here: https://github.com/saurabhsahni/php-yahoo-oauth2/
define("CONSUMER_KEY", "dj0yJmk9Z1BONmxiZjR5QTlzJmQ9WVdrOVdtTmhjWFJsTm1jbWNHbzlNQS0tJnM9Y29uc3VtZXJzZWNyZXQmeD0wZA--");
define("CONSUMER_SECRET", "590b4462737387b745894b3cf77a75ca5a651c93");

require_once APPPATH.'libraries/IniWriter.php';
/*Your Yahoo API consumer key & secret with access to Gemini data */
class Yahoo{

	private $redirect_uri = 'http://reporting.prodata.media/v2/yahoo_test/get_token'; //Or your other redirect URL - must match the callback domain
    private $targeting_url = 'https://api.gemini.yahoo.com/v2/rest/targetingattribute/';
    private $campaign_url = 'https://api.gemini.yahoo.com/v2/rest/campaign/';
    private $group_url = 'https://api.gemini.yahoo.com/v2/rest/adgroup/';
    private $ad_url = 'https://api.gemini.yahoo.com/v2/rest/ad/';
    private $keyword_url = 'https://api.gemini.yahoo.com/v2/rest/keyword/';
    private $report_url = 'https://api.gemini.yahoo.com/v2/rest/reports/custom?reportFormat=json';
    private $audience_url = 'https://api.gemini.yahoo.com/v2/rest/audience';
    private $tag_url = 'https://api.gemini.yahoo.com/v2/rest/tag';

	private $customer_secret = '590b4462737387b745894b3cf77a75ca5a651c93';
	private $customer_key = 'dj0yJmk9Z1BONmxiZjR5QTlzJmQ9WVdrOVdtTmhjWFJsTm1jbWNHbzlNQS0tJnM9Y29uc3VtZXJzZWNyZXQmeD0wZA--';

	private $gemini_api_endpoint = "https://api.admanager.yahoo.com/v2/rest";
	private $access_token;
	private $headers;

	private $oauth2client;

	public function __construct()
	{
		$auth = parse_ini_file(APPPATH . 'third_party/yahoo/auth.ini', true);
		$this->oauth2client = new YahooOAuth2();

		if(time() > ($auth["OAUTH2"]['refresh_time']-10)) {
			var_dump(7771);
			$writer = new IniWriter();
			$new_auth = $this->oauth2client->refresh_access_token($this->customer_key, $this->customer_secret, $this->redirect_uri, $auth["OAUTH2"]['refresh_token']);

			$auth["OAUTH2"]['refresh_time'] = time() + $new_auth['expires_in'];
			$auth["OAUTH2"]['expires_in'] = $new_auth['expires_in'];
			$auth["OAUTH2"]['refresh_token'] = $new_auth['refresh_token'];
			$auth["OAUTH2"]['access_token'] = $new_auth['access_token'];

			$result = $writer->writeToFile(APPPATH . 'third_party/yahoo/auth.ini', $auth);
		}

		$this->access_token = $auth["OAUTH2"]['access_token'];
		$this->headers = array(
			'Authorization: Bearer ' . $this->access_token,
			'Accept: application/json',
			'Content-Type: application/json'
		);
	}

	public function test()
	{

		//Fetch Advertiser Name and Advertiser ID
        echo '<pre>';
		$url = $this->gemini_api_endpoint . "/advertiser/";
        $url ='https://api.gemini.yahoo.com/v2/rest/dictionary/tracking_partners/';
        $url ='https://api.gemini.yahoo.com/v2/rest/dictionary/woeid/?type=DMA';
		$resp = $this->oauth2client->fetch($url, $postdata = "", $auth = "", $this->headers);
		$jsonResponse = json_decode($resp, true); var_dump($jsonResponse); exit;
		$advertiserName = $jsonResponse->response[0]->advertiserName;
		$advertiserId = $jsonResponse->response[0]->id;
		echo "Welcome " . $advertiserName . " with ID " . $advertiserId; exit;

		//Create a new campaign
		$url = $this->gemini_api_endpoint . "/campaign";
		$postdata = '{
		  "status":"PAUSED",
		  "campaignName":"NativeAdsCampaign",
		  "budget": 3000,
		  "budgetType": "LIFETIME",
		  "advertiserId": ' . $advertiserId . ',
		  "channel":"NATIVE"
		}';

		$resp = $this->oauth2client->fetch($url, $postdata = $postdata, $auth = "", $this->headers);
		$jsonResponse = json_decode($resp);

		$campaignID = $jsonResponse->response->id;
		$campaignName = $jsonResponse->response->campaignName;

		echo "\n<br>Created a new campaign with ID: " . $campaignID;

		//Read specific campaign data
		$url = $this->gemini_api_endpoint . "/campaign/" . $campaignID;
		$resp = $this->oauth2client->fetch($url, $postdata = "", $auth = "", $this->headers);
		$jsonResponse = json_decode($resp);
		echo "\n<br> Campaign object:<br>\n";
		print_r($jsonResponse->response);

	}

    public function get_group($group_id)
    {
        $response = array('message'=>'','result'=>'');

        $result = $this->oauth2client->fetch($this->group_url.$group_id, null, $auth = "", $this->headers);

        $result_array = json_decode($result, true);
        if($result_array['errors']) {
            $response['message'] = $result_array['errors'][0]['message'];
            return $response;
        }
        $response['result'] = $result_array['response'];
        return $response;
    }

	public function get_targeting()
	{

		//Fetch Advertiser Name and Advertiser ID
        echo '<pre>';

        $url ='https://api.gemini.yahoo.com/v2/rest/targetingattribute/?pi=9179500933&pt=ADGROUP';
		$resp = $this->oauth2client->fetch($url, $postdata = "", $auth = "", $this->headers);
		$jsonResponse = json_decode($resp, true); var_dump($jsonResponse); exit;
		$advertiserName = $jsonResponse->response[0]->advertiserName;
		$advertiserId = $jsonResponse->response[0]->id;
		echo "Welcome " . $advertiserName . " with ID " . $advertiserId; exit;

		//Create a new campaign
		$url = $this->gemini_api_endpoint . "/campaign";
		$postdata = '{
		  "status":"PAUSED",
		  "campaignName":"NativeAdsCampaign",
		  "budget": 3000,
		  "budgetType": "LIFETIME",
		  "advertiserId": ' . $advertiserId . ',
		  "channel":"NATIVE"
		}';

		$resp = $this->oauth2client->fetch($url, $postdata = $postdata, $auth = "", $this->headers);
		$jsonResponse = json_decode($resp);

		$campaignID = $jsonResponse->response->id;
		$campaignName = $jsonResponse->response->campaignName;

		echo "\n<br>Created a new campaign with ID: " . $campaignID;

		//Read specific campaign data
		$url = $this->gemini_api_endpoint . "/campaign/" . $campaignID;
		$resp = $this->oauth2client->fetch($url, $postdata = "", $auth = "", $this->headers);
		$jsonResponse = json_decode($resp);
		echo "\n<br> Campaign object:<br>\n";
		print_r($jsonResponse->response);

	}

	public function create_location_targeting($postdata)
	{
        $response = array('message'=>'','result'=>'');
		$result = $this->oauth2client->fetch($this->targeting_url, $postdata, $auth = "", $this->headers);

		$result_array = json_decode($result, true);
        if($result_array['errors']) {
            $response['message'] = $result_array['errors'];
            return $response;
        }
        $response['result'] = $result_array['response']['id'];
		return $response;
	}

	public function create_campaign($data)
	{
        $response = array('message'=>'','result'=>'');
		$result = $this->oauth2client->fetch($this->campaign_url, $data, $auth = "", $this->headers);

		$result_array = json_decode($result, true);
        if($result_array['errors']) {
            $response['message'] = $result_array['errors'][0]['message'];
            return $response;
        }
        $response['result'] = $result_array['response']['id'];
		return $response;
	}

	public function update_campaign($data)
	{
        $response = array('message'=>'','result'=>'');
		$result = $this->oauth2client->fetch($this->campaign_url, $data, $auth = "", $this->headers, $request_type = 'PUT');

		$result_array = json_decode($result, true);
        if($result_array['errors']) {
            $response['message'] = $result_array['errors'];
            return $response;
        }
        $response['result'] = $result_array['response']['id'];
		return $response;
	}

	public function create_group($data)
	{
        $response = array('message'=>'','result'=>'');
		$result = $this->oauth2client->fetch($this->group_url, $data, $auth = "", $this->headers);

		$result_array = json_decode($result, true);
        if($result_array['errors']) {
            $response['message'] = $result_array['errors'][0]['message'];
            return $response;
        }
        $response['result'] = $result_array['response']['id'];
		return $response;
	}

	public function update_group($data)
	{
        $response = array('message'=>'','result'=>'');
		$result = $this->oauth2client->fetch($this->group_url, $data, $auth = "", $this->headers, $request_type = 'PUT');

		$result_array = json_decode($result, true);
        if($result_array['errors']) {
            $response['message'] = $result_array['errors'];
            return $response;
        }
        $response['result'] = $result_array['response']['id'];
		return $response;
	}

    public function create_ad($data)
	{
        $response = array('message'=>'','result'=>'');
		$result = $this->oauth2client->fetch($this->ad_url, $data, $auth = "", $this->headers);

		$result_array = json_decode($result, true);
        if($result_array['errors']) {
            $response['message'] = $result_array['errors'];
            return $response;
        }
        $response['result'] = $result_array['response']['id'];
		return $response;
	}

	public function get_ads($data)
	{
        $response = array('message'=>'','result'=>'');
		$result = $this->oauth2client->fetch($this->ad_url.$data, $data=null, $auth = "", $this->headers);

		$result_array = json_decode($result, true);
        if($result_array['errors']) {
            $response['message'] = $result_array['errors'];
            return $response;
        }
        $response['result'] = $result_array['response'];
		return $response;
	}

	public function update_ad($data)
	{
        $response = array('message'=>'','result'=>'');
		$result = $this->oauth2client->fetch($this->ad_url, $data, $auth = "", $this->headers, $request_type = 'PUT');

		$result_array = json_decode($result, true);
        if($result_array['errors']) {
            $response['message'] = $result_array['errors'];
            return $response;
        }
        $response['result'] = $result_array['response']['id'];
		return $response;
	}

	public function create_targeting($data)
	{

		$result = $this->oauth2client->fetch($this->targeting_url, $data, $auth = "", $this->headers);

        $result_array = json_decode($result, true);

        if($result_array['errors']) {
            $response['message'] = $result_array['errors'];
            return $response;
        }

        $response['result'] = $result_array['response']['id'];
        return $response;

	}

	public function create_keyword($data)
	{

		$result = $this->oauth2client->fetch($this->keyword_url, $data, $auth = "", $this->headers);

        $result_array = json_decode($result, true);

        if($result_array['errors']) {
            $response['message'] = $result_array['errors'];
            return $response;
        }

        $response['result'] = $result_array['response']['id'];
        return $response;

	}

	public function remove_keyword($data)
	{

		$result = $this->oauth2client->fetch($this->keyword_url, $data, $auth = "", $this->headers, $request_type = 'DELETE');

        $result_array = json_decode($result, true);

        if($result_array['errors']) {
            $response['message'] = $result_array['errors'];
            return $response;
        }

        $response['result'] = $result_array['response'];
        return $response;

	}

	public function get_reporting($data)
	{
//        var_dump(11);
		$result = $this->oauth2client->fetch($this->report_url, $data, $auth = "", $this->headers);
        var_dump($result);
        $result_array = json_decode($result, true);

        if($result_array['errors']) {
            $response['message'] = $result_array['errors'];
            return $response;
        }

        $response['result'] = $result_array['response'];
        return $response;

	}
	public function get_job($link)
	{
//        var_dump(11);
		$result = $this->oauth2client->fetch($link, null, $auth = "", $this->headers);
        //var_dump($result); exit;
        $result_array = json_decode($result, true);

        if($result_array['errors']) {
            $response['message'] = $result_array['errors'];
            return $response;
        }

        $response['result'] = $result_array['response'];
        return $response;

	}


    public function create_audience($data)
    {
        $response = array('message'=>'','result'=>'');
        $result = $this->oauth2client->fetch($this->audience_url, $data, $auth = "", $this->headers);
        $result_array = json_decode($result, true);
        if($result_array['errors']) {
            $response['message'] = $result_array['errors'];
            return $response;
        }
        $response['result'] = $result_array['response']['id'];
        return $response;
    }
    public function get_audience($id)
    {
        $response = array('message'=>'','result'=>'');
        $result = $this->oauth2client->fetch($this->audience_url.'/'.$id, $data=null, $auth = "", $this->headers);
    var_dump($result);
        $result_array = json_decode($result, true); var_dump('<pre>',$result_array); exit;
        if($result_array['errors']) {
            $response['message'] = $result_array['errors'];
            return $response;
        }
        $response['result'] = $result_array['response']['id'];
        return $response;
    }
    public function get_tag($id)
    {
        $response = array('message'=>'','result'=>'');
        $result = $this->oauth2client->fetch($this->tag_url.'/'.$id.'?details=true', $data=null, $auth = "", $this->headers);

        $result_array = json_decode($result, true);
        if($result_array['errors']) {
            $response['message'] = $result_array['errors'];
            return $response;
        }
        $response['result'] = $result_array['response'];
        return $response;
    }

    public function create_tag($data)
    {
//        var_dump(11);
        $result = $this->oauth2client->fetch($this->tag_url, $data, $auth = "", $this->headers);
        $result_array = json_decode($result, true);

        if($result_array['errors']) {
            $response['message'] = $result_array['errors'];
            return $response;
        }

        $response['result'] = $result_array['response'];
        return $response;

    }
}
?>