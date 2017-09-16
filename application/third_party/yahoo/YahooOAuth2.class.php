<?php
/*
Example class to access Yahoo OAuth2 protected APIs, based on https://developer.yahoo.com/oauth2/guide/
Find documentation and support on Yahoo Developer Network: https://developer.yahoo.com/forums

*/

class YahooOAuth2 {

    const AUTHORIZATION_ENDPOINT  = 'https://api.login.yahoo.com/oauth2/request_auth';
    const TOKEN_ENDPOINT   = 'https://api.login.yahoo.com/oauth2/get_token';

    public function fetch($url,$postdata="",$auth="",$headers="", $resquest_type = null) {
        $curl = curl_init($url); 
        if($postdata) {
            if($resquest_type) {
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $resquest_type);
            } else {
                curl_setopt($curl, CURLOPT_POST, true);
            }
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        }
        else {
            curl_setopt($curl, CURLOPT_POST, false);
        }
        if($auth){
            curl_setopt($curl, CURLOPT_USERPWD, $auth);
        }
        if($headers){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec( $curl );
        if (empty($response)) {
            // some kind of an error happened
            die(curl_error($curl));
            curl_close($curl); // close cURL handler
        } else {
            $info = curl_getinfo($curl);
            curl_close($curl); // close cURL handler
            if($info['http_code'] != 200 && $info['http_code'] != 201 ) {
                echo "Received error: " . $info['http_code']. "\n";
                echo "Raw response:".$response."\n";
                //die();
            }
        }
        return $response;
    }
    public function getAuthorizationURL($client_id,$redirect_uri,$language="en-us") {
        $url = self::AUTHORIZATION_ENDPOINT;
        $authorization_url=$url.'?'.'client_id='.$client_id.'&redirect_uri='.$redirect_uri.'&language='.$language.'&response_type=code';
        return $authorization_url;
    }


    public function get_access_token($clientId, $clientSecret,$redirect_uri,$code) {
        $url=self::TOKEN_ENDPOINT;
        $postdata=array("redirect_uri"=>$redirect_uri,"code"=>$code,"grant_type"=>"authorization_code");
        $auth=$clientId . ":" . $clientSecret;
        $response=self::fetch($url,http_build_query($postdata),$auth);

        // Convert the result from JSON format to a PHP array
        $jsonResponse = json_decode( $response, true );
        return $jsonResponse;
    }

    public function refresh_access_token($clientId, $clientSecret,$redirect_uri,$refresh_token) {
        $url=self::TOKEN_ENDPOINT;
        $postdata=array("redirect_uri"=>$redirect_uri,"refresh_token"=>$refresh_token,"grant_type"=>"refresh_token");
        $auth=$clientId . ":" . $clientSecret;
        $response=self::fetch($url,http_build_query($postdata),$auth);

        // Convert the result from JSON format to a PHP array
        $jsonResponse = json_decode( $response, true );
        return $jsonResponse;
    }

}

?>
