<?php

require_once APPPATH."third_party/predis/autoload.php";


class RTB_Ads_Model extends CI_Model      {

    protected $CI;

    private $ad_type; // RICH_MEDIA, DISPLAY_AD, VIDEO, SOCIAL
    private $size = array(
        "width" => 0,
        "height" => 0
    );
    private $geo = array(
        "country" => "",
        "state" => "",
        "zip_code" => ""
    );
    private $category;
    private $referral_url;
    private $demographic = array(
        "year_of_birth" => "",
        "gender" => "",
        "unique_user_id" => "",
        "device_user_id" => ""
    );

    public function __construct() {
        parent::__construct();
        $this->CI =& get_instance();
        $this->CI->load->database();

    }

    public function search_matching_ads()  {
        try {
            $client = new Predis\Client(
                array(
                    "scheme" => "tcp",
                    "host" => "127.0.0.1",
//                    "host" => "192.168.204.239",
                    "port" => "6379",
                    "database" => 7
                )
            );

        } catch(Predis\Connection\ConnectionException $e) {
            return false;
        }

        // query redis for:
        /*
         * ad_type
         * size.width AND size.height
         * geo.country AND geo.state
         *
         * if we have MATCHING on these from the set variables, then return the ad(s) via HGETALL.
         *
         */
        $keys = $client->keys('*');
        $matching_ads = [];
        foreach ($keys as $key) {
            $ad = $client->hgetall($key);

            if($ad['country'] != $this->geo['country'] || $ad['state'] != $this->geo['state'] || $ad['creative_type'] != $this->category || $ad['creative_width'] != $this->size['width'] || $ad['creative_height'] != $this->size['height']){
                continue;
            }
            $matching_ads[] = $ad;
        }

        return $matching_ads;

    }

    public function __get($name) {
        return $this->$name;
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __isset($name) {
        return isset($this->$name);
    }

}

?>