<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AirpushCrons extends CI_Controller
{

    private $viewArray = array();
    public $campaign_name = "testing";
    private $admin="harutyun.sardaryan.bw@gmail.com";


    function __construct()
    {
        parent::__construct();

        //load our new Adwords library
        $this->load->library('parser');
        $this->load->library('MY_Parser');
        $this->load->library('ion_auth');

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library('email');

        $this->load->library('Airpush');
        $this->load->model('Ad_list_model');

        $this->load->model('Criterion_list_model');
        $this->load->model('Group_list_model');
        $this->load->model('Userlist_vertical_model');
        $this->load->model('Userlist_io_model');
        $this->load->model('V2_network_country_criterion_model');
        $this->load->model('V2_network_state_criterion_model');
        $this->load->model('Group_report_model');
        $this->load->model('Ion_auth_model');
        $this->load->model('Ad_report_model');
        $this->load->model('Airpush_model');

        $this->load->database();
        $this->uploadedPath = $this->config->base_url() . 'uploads/';
        $this->viewArray['current_url'] = current_url();
        $this->viewArray['base_url'] = base_url();
        $this->viewArray['site_url'] = site_url();

        $this->viewArray['manage_users'] = false;
        $this->viewArray['show_top_menu'] = true;
        $this->viewArray['take5_user'] = false;
        $this->viewArray["domain"] = "report-site.com";
        $this->viewArray["domain_name"] = "report-site.com";

        $this->no_fraud = array(
            "66.0.218.10",
            "76.110.227.216",
            "50.198.249.13",
            "76.110.217.139"
        );
        $this->viewArray['logo'] = '';

    }

    public function rest(){

        $this->Airpush_model->rest();
    }

    public function impr(){

        $this->Airpush_model->get_ads_impressions();
    }

    public function cost(){

        $this->Airpush_model->get_campaigns_cost();
    }

    public function campaign(){
//        $zip_array = explode(",", '00501,00544,06390,06404,06440,06468,06470,06482,06484,06491,06601,06602,06604,06605,06606,06607,06608,06610,06611,06612,06614,06615,06699,06784,06801,06804,06807,06810,06811,06812,06813,06814,06816,06817,06820,06824,06825,06828,06829,06830,06831,06836,06838,06840,06850,06851,06852,06853,06854,06855,06856,06858,06860,06870,06875,06876,06877,06878,06879,06880,06881,06883,06888,06889,06890,06896,06897,06901,06902,06903,06904,06905,06906,06907,06911,06912,06913,06920,06921,06922,06926,06927,07001,07002,07003,07004,07005,07006,07007,07008,07009,07010,07011,07012,07013,07014,07015,07016,07017,07018,07019,07020,07021,07022,07023,07024,07026,07027,07028,07029,07030,07031,07032,07033,07034,07035,07036,07039,07040,07041,07042,07043,07044,07045,07046,07047,07050,07051,07052,07054,07055,07057,07058,07059,07060,07061,07062,07063,07064,07065,07066,07067,07068,07069,07070,07071,07072,07073,07074,07075,07076,07077,07078,07079,07080,07081,07082,07083,07086,07087,07088,07090,07091,07092,07093,07094,07095,07096,07097,07099,07101,07102,07103,07104,07105,07106,07107,07108,07109,07110,07111,07112,07114,07175,07184,07188,07189,07191,07192,07193,07195,07199,07201,07202,07203,07204,07205,07206,07207,07208,07302,07303,07304,07305,07306,07307,07308,07310,07311,07395,07399,07401,07403,07405,07407,07410,07416,07417,07418,07419,07420,07421,07422,07423,07424,07428,07430,07432,07435,07436,07438,07439,07440,07442,07444,07446,07450,07451,07452,07456,07457,07458,07460,07461,07462,07463,07465,07470,07474,07480,07481,07495,07501,07502,07503,07504,07505,07506,07507,07508,07509,07510,07511,07512,07513,07514,07522,07524,07533,07538,07543,07544,07601,07602,07603,07604,07605,07606,07607,07608,07620,07621,07624,07626,07627,07628,07630,07631,07632,07640,07641,07642,07643,07644,07645,07646,07647,07648,07649,07650,07652,07653,07656,07657,07660,07661,07662,07663,07666,07670,07675,07676,07677,07701,07702,07703,07704,07710,07711,07712,07715,07716,07717,07718,07719,07720,07721,07722,07723,07724,07726,07727,07728,07730,07731,07732,07733,07734,07735,07737,07738,07739,07740,07746,07747,07748,07750,07751,07752,07753,07754,07755,07756,07757,07758,07760,07762,07763,07764,07765,07801,07802,07803,07806,07820,07821,07822,07823,07825,07826,07827,07828,07830,07831,07832,07833,07834,07836,07837,07838,07839,07840,07842,07843,07844,07845,07846,07847,07848,07849,07850,07851,07852,07853,07855,07856,07857,07860,07863,07865,07866,07869,07870,07871,07874,07875,07876,07877,07878,07879,07880,07882,07885,07890,07901,07902,07920,07921,07922,07924,07926,07927,07928,07930,07931,07932,07933,07934,07935,07936,07938,07939,07940,07945,07946,07950,07960,07961,07962,07963,07970,07974,07976,07977,07978,07979,07980,07981,08005,08006,08008,08050,08087,08092,08501,08502,08504,08510,08512,08514,08526,08527,08528,08530,08533,08535,08536,08551,08553,08555,08556,08557,08558,08559,08701,08720,08721,08722,08723,08724,08730,08731,08732,08733,08734,08735,08736,08738,08739,08740,08741,08742,08750,08751,08752,08753,08754,08755,08756,08757,08758,08759,08801,08802,08803,08804,08805,08807,08808,08809,08810,08812,08816,08817,08818,08820,08821,08822,08823,08824,08825,08826,08827,08828,08829,08830,08831,08832,08833,08834,08835,08836,08837,08840,08844,08846,08848,08850,08852,08853,08854,08855,08857,08858,08859,08861,08862,08863,08865,08867,08868,08869,08870,08871,08872,08873,08875,08876,08879,08880,08882,08884,08885,08886,08887,08888,08889,08890,08899,08901,08902,08903,08904,08906,08933,10001,10002,10003,10004,10005,10006,10007,10008,10009,10010,10011,10012,10013,10014,10016,10017,10018,10019,10020,10021,10022,10023,10024,10025,10026,10027,10028,10029,10030,10031,10032,10033,10034,10035,10036,10037,10038,10039,10040,10041,10043,10044,10045,10055,10065,10069,10075,10080,10081,10087,10101,10102,10103,10104,10105,10106,10107,10108,10109,10110,10111,10112,10113,10114,10115,10116,10117,10118,10119,10120,10121,10122,10123,10124,10125,10126,10128,10129,10130,10131,10132,10133,10138,10150,10151,10152,10153,10154,10155,10156,10157,10158,10159,10160,10162,10163,10164,10165,10166,10167,10168,10169,10170,10171,10172,10173,10174,10175,10176,10177,10178,10179,10185,10199,10213,10242,10249,10256,10259,10260,10261,10265,10268,10269,10270,10271,10272,10273,10274,10275,10276,10277,10278,10279,10280,10281,10282,10285,10286,10292,10301,10302,10303,10304,10305,10306,10307,10308,10309,10310,10311,10312,10313,10314,10451,10452,10453,10454,10455,10456,10457,10458,10459,10460,10461,10462,10463,10464,10465,10466,10467,10468,10469,10470,10471,10472,10473,10474,10475,10501,10502,10503,10504,10505,10506,10507,10509,10510,10511,10512,10514,10516,10517,10518,10519,10520,10521,10522,10523,10524,10526,10527,10528,10530,10532,10533,10535,10536,10537,10538,10540,10541,10542,10543,10545,10546,10547,10548,10549,10550,10551,10552,10553,10560,10562,10566,10567,10570,10573,10576,10577,10578,10579,10580,10583,10587,10588,10589,10590,10591,10594,10595,10596,10597,10598,10601,10602,10603,10604,10605,10606,10607,10610,10701,10702,10703,10704,10705,10706,10707,10708,10709,10710,10801,10802,10803,10804,10805,10901,10910,10911,10912,10913,10914,10915,10916,10917,10918,10919,10920,10921,10922,10923,10924,10925,10926,10927,10928,10930,10931,10932,10933,10940,10941,10949,10950,10952,10953,10954,10956,10958,10959,10960,10962,10963,10964,10965,10968,10969,10970,10973,10974,10975,10976,10977,10979,10980,10981,10982,10983,10984,10985,10986,10987,10988,10989,10990,10992,10993,10994,10996,10997,10998,11001,11002,11003,11004,11005,11010,11020,11021,11022,11023,11024,11030,11040,11042,11050,11051,11052,11053,11054,11055,11096,11101,11102,11103,11104,11105,11106,11109,11201,11202,11203,11204,11205,11206,11207,11208,11209,11210,11211,11212,11213,11214,11215,11216,11217,11218,11219,11220,11221,11222,11223,11224,11225,11226,11228,11229,11230,11231,11232,11233,11234,11235,11236,11237,11238,11239,11241,11242,11243,11245,11247,11249,11251,11252,11256,11351,11352,11354,11355,11356,11357,11358,11359,11360,11361,11362,11363,11364,11365,11366,11367,11368,11369,11370,11371,11372,11373,11374,11375,11377,11378,11379,11380,11381,11385,11386,11405,11411,11412,11413,11414,11415,11416,11417,11418,11419,11420,11421,11422,11423,11424,11425,11426,11427,11428,11429,11430,11431,11432,11433,11434,11435,11436,11439,11451,11499,11501,11507,11509,11510,11514,11516,11518,11520,11530,11531,11542,11545,11547,11548,11549,11550,11551,11552,11553,11554,11555,11556,11557,11558,11559,11560,11561,11563,11565,11566,11568,11569,11570,11571,11572,11575,11576,11577,11579,11580,11581,11582,11590,11596,11598,11599,11690,11691,11692,11693,11694,11695,11697,11701,11702,11703,11704,11705,11706,11707,11709,11710,11713,11714,11715,11716,11717,11718,11719,11720,11721,11722,11724,11725,11726,11727,11729,11730,11731,11732,11733,11735,11738,11739,11740,11741,11742,11743,11746,11747,11749,11751,11752,11753,11754,11755,11756,11757,11758,11762,11763,11764,11765,11766,11767,11768,11769,11770,11771,11772,11773,11775,11776,11777,11778,11779,11780,11782,11783,11784,11786,11787,11788,11789,11790,11791,11792,11793,11794,11795,11796,11797,11798,11801,11802,11803,11804,11854,11901,11930,11931,11932,11933,11934,11935,11937,11939,11940,11941,11942,11944,11946,11947,11948,11949,11950,11951,11952,11953,11954,11955,11956,11957,11958,11959,11960,11961,11962,11963,11964,11965,11967,11968,11969,11970,11971,11972,11973,11975,11976,11977,11978,11980,12401,12402,12404,12409,12410,12411,12412,12416,12417,12419,12420,12428,12429,12432,12433,12435,12440,12441,12443,12446,12448,12449,12453,12456,12457,12458,12461,12464,12465,12466,12471,12472,12475,12477,12480,12481,12483,12484,12486,12487,12489,12490,12491,12493,12494,12495,12498,12501,12504,12506,12507,12508,12510,12511,12512,12514,12515,12518,12520,12522,12524,12525,12527,12528,12531,12533,12537,12538,12540,12542,12543,12545,12546,12547,12548,12549,12550,12551,12552,12553,12555,12561,12563,12564,12566,12567,12568,12569,12570,12571,12572,12574,12575,12577,12578,12580,12581,12582,12583,12584,12585,12586,12588,12589,12590,12592,12594,12601,12602,12603,12604,12701,12719,12720,12721,12722,12723,12724,12725,12726,12729,12732,12733,12734,12736,12737,12738,12740,12741,12742,12743,12745,12746,12747,12748,12749,12750,12751,12752,12754,12758,12759,12762,12763,12764,12765,12766,12767,12768,12769,12770,12771,12775,12776,12777,12778,12779,12780,12781,12783,12784,12785,12786,12787,12788,12789,12790,12791,12792,18324,18328,18336,18337,18340,18371,18425,18426,18428,18435,18451,18457,18458,18464,07502,07511,07512,08701,20794,06901,06902,06903,06904,06905,06906,06907,06911,06912,06913,06920,06921,06922,06926,06927,19801,19802,19803,19804,19805,19806,19807,19808,19809,19810,19850,19880,19884,19885,19886,19890,19891,19893,19894,19897,19898,19899,19019,19092,19093,19101,19102,19103,19104,19105,19106,19107,19108,19109,19110,19111,19112,19113,19114,19115,19116,19118,19119,19120,19121,19122,19123,19124,19125,19126,19127,19128,19129,19130,19131,19132,19133,19134,19135,19136,19137,19138,19139,19140,19141,19142,19143,19144,19145,19146,19147,19148,19149,19150,19151,19152,19153,19154,19155,19160,19162,19170,19171,19172,19173,19175,19176,19177,19178,19181,19182,19184,19185,19187,19188,19191,19192,19193,19194,19195,19196,19197,19244,19255'); //var_dump($zip_array);
//        if(count($zip_array)>500) {
//            shuffle($zip_array);
//            $zip_array = array_slice($zip_array, 0, 499);
//        }
//        $zip = implode(",", $zip_array);
//        var_dump($zip); exit;
        $this->Airpush_model->create();
    }

    public function text_ad(){

        $this->Airpush_model->create_ads(1980419,array('id'=>82));
    }

    public function country(){

        $result = $this->airpush->get_country();
        $countrys = json_decode($result);
        $countrys = json_decode($countrys, true);

        var_dump(count($countrys));
        foreach($countrys as $country=>$code) {

            $c = $this->V2_network_country_criterion_model->get_country($country,1); //var_dump($c); exit;
            if($c) {  //var_dump($c); exit;
                $data['country_name'] = $country;
                $data['country_code'] = $c[0]['country_code'];
                $data['network_id'] = 4;
                $data['criterion_id'] = $code;
                $this->V2_network_country_criterion_model->create($data);
            }
        }
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

    public function get_token()
    {
        // code M91b784f0-efb4-2b82-0da1-bed8da14aae4
        // red_url http://reporting.prodata.media/v2/bingCrons/getToken
        // client id 000000004417AE58
        // secet f6A3bgcI77xO3ID4iO58sYIWsbdB7llp
        //$refresh_tocken = $this->input->get('refresh_tocken');
        var_dump($this->input->get()); exit;
        $accessTokenExchangeUrl = "https://login.live.com/oauth20_token.srf";
        $accessTokenExchangeParams = array(
            'client_id' => $this->client_id,
            //'client_secret' => $this->client_secret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_tocken,
            //'code' => $code,
            'redirect_uri' => 'http://reporting.prodata.media/v2/bingCrons/get_token'
        );
        $json = $this->post_data('https://login.live.com/oauth20_token.srf',$accessTokenExchangeParams);
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




