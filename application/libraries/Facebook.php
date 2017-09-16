<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if ( !isset($_SESSION) ) session_start();
ini_set('display_errors', 'on');
// Autoload the required files
require_once( APPPATH . 'third_party/facebook/php-sdk-v4/src/Facebook/autoload.php' );

//echo  APPPATH . 'third_party/facebook/php-sdk-v4/src/Facebook/autoload.php';


// Make sure to load the Facebook SDK for PHP via composer or manually

use Facebook\FacebookSessionPersistentDataHandler;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
// add other classes you plan to use, e.g.:
// use Facebook\FacebookRequest;
// use Facebook\GraphUser;
// use Facebook\FacebookRequestException;


class Facebook
{
    public $fb;
    public $config = [
        'app_id' => '1674509896124897',
        'app_secret' => '3840e5e1633fd3f78a4c3042af87729b',
        'default_graph_version' => 'v2.6',
    ];
    public $businessId = '1094106567329800';
    public $accessToken1;


    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('facebook_model');
        $this->accessToken1 = $this->set_access_token();
        $this->fb = new Facebook\Facebook($this->config);

    }

    public function login_callback($userId) {

        if (!session_id()) {
            session_start();
        }
        $helper = $this->fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if($accessToken) {

            $fbUser = $this->fb->get('/me', $accessToken);
            $fbUserBody = $fbUser->getBody();
            $fbUserData = json_decode($fbUserBody);
            $fbUserId = $fbUserData->id;

            $pages = $this->fb->get('/me/accounts', $accessToken);
            $pagesBody = $pages->getBody();
            $pagesDecodedBody = json_decode($pagesBody);
            $pagesData = $pagesDecodedBody->data;


//            $url_perms = "https://graph.facebook.com/v2.6/1094106567329800/userpermissions?access_token=$accessToken";
////            var_dump($fbUserId, $userId); exit;
//
//            //var_dump($accessToken); die;
//            $postDataPerms['email'] = 'harutyun.sardaryan.bw@gmail.com';
////            $postDataPerms['email'] = 'businessfbuser@gmail.com';
//            $postDataPerms['role'] = 'EMPLOYEE';

            //echo '<pre>'; print_r($postDataPerms); die;
//            $s = curl_init();
//            curl_setopt($s, CURLOPT_URL, $url_perms);
//            curl_setopt($s, CURLOPT_POSTFIELDS, $postDataPerms);
//            $output = curl_exec($s);
//            echo '<pre>';var_dump($output);
//            curl_close($s);


            if ($pagesData) {
                //$accessToken1='CAAXy9TeICeEBAFaOLdNXk0rXJHnonnwGn49RU2u76gSqm3J1G24ZBqMhU0HPLQ45OkJCaOX0tTfPUgTmg6epG6ALrdkYxSIFLQ8VWyvRmKZBioxPzWbgeiQJALoZCGXZArWZBRV9YVKtkr1JJ5Vl2SE1KgS3dCHsX3AxP7e0Nwh8AQ3LrA9on';

                $url_pages = "https://graph.facebook.com/v2.6/1094106567329800/pages?access_token=$this->accessToken1";
                $roles = '["ADVERTISER","INSIGHTS_ANALYST"]';

                $postDataPages['access_type'] = 'AGENCY';
                $postDataPages['permitted_roles'] = $roles;

                $data['user_id'] = $userId;

                if($this->CI->facebook_model->is_page_exists($userId)) {
                    $exists = true;
                } else {
                    $exists = false;
                }

                foreach ($pagesData as $pages) {
                    $postDataPages['page_id'] = $pages->id;

                    $data['page_id'] = $pages->id;
                    $data['page_name'] = $pages->name;

                    if (!$exists) {
                        $this->CI->facebook_model->save_fb_pages($data);
                    } else {
                        $this->CI->facebook_model->delete_linked_data($userId);
                        $this->CI->facebook_model->save_fb_pages($data);
                    }

                    //echo '<pre>';

                    $s = curl_init();
                    curl_setopt($s, CURLOPT_URL, $url_pages);
                    curl_setopt($s, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($s, CURLOPT_POSTFIELDS, $postDataPages);
                    $output = curl_exec($s);
                    $result = json_decode($output, true);
                    //var_dump($result, $data);
                    if(array_key_exists('error', $result)) {
                        echo '<p>'.$result["error"]["message"].'</p>';
                    }
                    curl_close($s);

//                    curl \
//                    -F "business=<business_id>" \
//                    -F "user=<USER_ID>" \
//                    -F "role=ADVERTISER" \
//                    -F "access_token=<ACCESS_TOKEN>" \
//                    "https://graph.facebook.com/<API_VERSION>/<PAGE_ID>/userpermissions"



                }
            }

            $isFbUserExists = $this->CI->facebook_model->is_user_exists($fbUserId);

            if(!$isFbUserExists) {
                if ($this->CI->facebook_model->save_fb_user($userId, $fbUserId, $accessToken)) {
                    redirect('/v2/profile/index');
                } else {
                    die('Something went wrong.');
                }
            }

        } else {
            die('no token');
        }

    }

    public function assign_user_to_page($pages) {
        //$accessToken1 = 'CAAXy9TeICeEBAFaOLdNXk0rXJHnonnwGn49RU2u76gSqm3J1G24ZBqMhU0HPLQ45OkJCaOX0tTfPUgTmg6epG6ALrdkYxSIFLQ8VWyvRmKZBioxPzWbgeiQJALoZCGXZArWZBRV9YVKtkr1JJ5Vl2SE1KgS3dCHsX3AxP7e0Nwh8AQ3LrA9on';

        $postDataAssign['business'] = $this->businessId;
        $postDataAssign['user'] = '145218842530167';
        $postDataAssign['role'] = 'ADVERTISER';

        foreach ($pages as $page)
        {
            $url_assign = "https://graph.facebook.com/v2.6/".$page['page_id']."/userpermissions?access_token=$this->accessToken1";
            $s2 = curl_init();
            curl_setopt($s2, CURLOPT_URL, $url_assign);
            curl_setopt($s2, CURLOPT_POSTFIELDS, $postDataAssign);
            $output2 = curl_exec($s2);
            curl_close($s2);
        }

        print json_encode(array("status" => "SUCCESS"));
    }


    public function set_access_token() {
        $parse = parse_ini_file(APPPATH . 'third_party/facebook/auth.ini', true);
        $accessToken = $parse["OAUTH2"]["access_token"];

        return $accessToken;
    }



}