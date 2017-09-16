<?php

class V2_ad_model extends CI_Model	{

	protected $CI;
	private $collection = 'v2_ads';

    private $id;
	private $title;
	private $description_1;
	private $description_2;
	private $creative_name;
	private $destination_url;
	private $display_url;
	private $creative_width;
    private $creative_url; // this is where the banner ad image exists
	private $creative_height;
	private $creative_status;
	private $create_date;
	private $creative_is_active;
	private $creative_type;
	private $approval_status;
	private $disapproval_reasons;
	private $network_group_id;
	private $network_campaign_id;
	private $network_creative_id;
	private $network_id;
	private $campaign_id;
	private $group_id;

	public function __construct()	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->database();

		//$this->CI->load->model("Monitor_model");
	}

    public function create($data, $destination_url = null) {
            // rewrite dest_url to our reporting site
        if(!is_null($destination_url)){
            $data['destination_url'] = $destination_url;
        }

        $this->CI->db->insert($this->collection, $data);
        $this->id = $this->CI->db->insert_id();

        return $this->id;
    }

    public function validate($data, $for_edit = false) {

        $message=[];
        $ad_types = array('TEXTAD','DISPLAY','DISPLAY_FACEBOOK','DISPLAY_YAHOO', 'VIDEO', 'FB-CAROUSEL-AD', 'VIDEO_YAHOO', 'VIDEO-CLICKS', 'DISPLAY_AIRPUSH', 'RICH_MEDIA', 'EVENT', 'APP_INSTALL','YAHOO_CAROUSEL', 'RICH_MEDIA_SURVEY');

        if (!empty($data['destination_url'])) {

            if(!$for_edit) {
                $insert["destination_url"] = $data['destination_url'];
            }
            $insert["original_url"] = $data['destination_url'];
        } elseif (!empty($data['destination_url']) && $data['creative_url'] != 'DISPLAY_FACEBOOK' && $data['creative_url'] != 'VIDEO') {
            $message[] = 'AD destination_url is empty';
        }

        if (!empty($data['creative_type']) && in_array($data['creative_type'], $ad_types)) {
            $insert["creative_type"] = $data['creative_type'];

        } else if (!$for_edit) {
            $message[] = 'AD type is empty or not matching';
        }

        if (!empty($data['network_id'])) {
            $insert["network_id"] = $data['network_id'];
        } else if (!$for_edit) {
            $message[] = 'AD network_id is empty';
        }

        if (!empty($data['campaign_id']) && !$for_edit) {
            $insert["campaign_id"] = $data['campaign_id'];
        } else if (!$for_edit) {
            $message[] = 'AD campaign_id is empty';
        }

        if (!empty($data['tracking_url'])) {
            $insert["tracking_url"] = $data['tracking_url'];
        }

        if (!empty($data['video_duration'])) {
            $insert["video_duration"] = $data['video_duration'];
        }

        if(!empty($data['address'])) {
            $insert['address'] = $data['address'];
        }

        if(!empty($data['action_buttons'])){
                $insert["action_buttons"] = $data['action_buttons'];
            }

        if(!empty($data['lat'])) {
            $insert['lat'] = $data['lat'];
        }

        if(!empty($data['lng'])) {
            $insert['lng'] = $data['lng'];
        }

        if(!empty($data['creative_status'])){
            $insert["creative_status"] = $data['creative_status'];
        } else {
            //$message[] = 'AD creative_status is empty';
        }

        if(!empty($data['approval_status'])){
            $insert["approval_status"] = $data['approval_status'];
        } else {
            //$message[] = 'AD approval_status is empty';
        }

        if(!empty($data['creative_name'])){
            $insert["creative_name"] = $data['creative_name'];
        } elseif(!$for_edit) {
            $message[] = 'AD name is empty';
        }

        if(isset($data['group_id'])){
            $insert["group_id"] = $data['group_id'];
        }

        if(isset($data['network_campaign_id'])){
            $insert["network_campaign_id"] = $data['network_campaign_id'];
        }

        if(isset($data['network_group_id'])){
            $insert["network_group_id"] = $data['network_group_id'];
        }

        $insert["keywords"] = 'RON';

        if(count($message)){
            return array('messages' => $message, 'valide_ad' => $insert);
        }

        if($data['creative_type']=="TEXTAD"){
            if(strlen($data['title']) > 0 && strlen($data['title']) <= 25){
                    $insert["title"] = $data['title'];
            } else {
                    $message[] = 'AD title must be less then 25 characters';
            }
            if(strlen($data['description_1']) > 0 && strlen($data['description_1']) <= 35){
                    $insert["description_1"] = $data['description_1'];
            } else {
                    $message[] = 'AD description must be less then 35 characters';
            }
            if(strlen($data['description_2']) > 0 && strlen($data['description_2']) <= 35){
                    $insert["description_2"] = $data['description_2'];
            } else {
                    $message[] = 'AD description must be less then 35 characters';
            }
            if(!empty($data['display_url'])){
                $insert["display_url"] = $data['display_url'];
                //$insert["display_url"] = 'reporting.prodata.media';
            } else {
                    $message[] = 'AD display_url is empty';
            }
            if(!empty($data['destination_url'])){
                if(!$for_edit) {
                    $insert["destination_url"] = $data['destination_url'];
                }
                $insert["original_url"] = $data['destination_url'];
            } else {
                    $message[] = 'AD destination_url is empty aaa';
            }
            if(!empty($data['creative_url'])){
                $insert["creative_url"] = $data['creative_url'];
            }

            if(!empty($data['daily_cap'])){
                $insert["daily_cap"] = $data['daily_cap'];
            }
            if(!empty($data['bid'])){
                $insert["bid"] = $data['bid'];
            }
        }
        if($data['creative_type']=="DISPLAY_FACEBOOK"){

            if(strlen($data['title']) > 0 && strlen($data['title']) <= 30){
                $insert["title"] = $data['title'];
            } else {
                //$message[] = 'AD title must be less then 25 characters';
            }
            if(strlen($data['description_1']) > 0 && strlen($data['description_1']) <= 70){
                $insert["description_1"] = $data['description_1'];
            } else {
                //$message[] = 'AD description must be less then 35 characters';
            }

            if(isset($data['fb_description'])){
                $insert["fb_description"] = $data['fb_description'];
            }

            if(isset($data['link_description'])){
                $insert["link_description"] = $data['link_description'];
            }

            if(isset($data['destination'])){
                $insert["destination"] = $data['destination'];
            }

            if(!empty($data['creative_url'])){
                $insert["creative_url"] = $data['creative_url'];
            }
            else if(isset($data['creative_url']) && empty($data['creative_url'])) {
                if(!$for_edit) {
                    $message[] = 'AD creative_url is empty';
                }
            }

            if(!$data['fb_page_like']) {

                if (!empty($data['destination_url'])) {
                    if (!$for_edit) {
                        $insert["destination_url"] = $data['destination_url'];


                    }

                    $url_array = parse_url($data['destination_url']);
                    //var_dump($url_array);
                    $url = $url_array['scheme'] . '://' . $url_array['host'];
                    $insert["display_url"] = $url_array['host'];
                    $insert["original_url"] = $data['destination_url'];
                    //var_dump($insert); //exit;
                } else {
                    $message[] = 'AD destination_url is empty ddd';
                }
            }

            if(!empty($data['creative_width'])){
                $insert["creative_width"] = $data['creative_width'];
            }
            else if(isset($data['creative_width']) && empty($data['creative_width'])) {
                if(!$for_edit || !empty($data['creative_width'])) {
                    $message[] = 'AD creative_width is empty';
                }
            }
            if(!empty($data['creative_height'])){
                $insert["creative_height"] = $data['creative_height'];
            }
            else if(isset($data['creative_height']) && empty($data['creative_height'])) {
                if(!$for_edit || !empty($data['creative_height'])) {
                    $message[] = 'AD creative_height is empty';
                }
            }

            if(!empty($data['airpush_internal_image'])){
                $insert["airpush_image_type"] = $data['airpush_internal_image'];
            }
            else if(isset($data['airpush_internal_image']) && empty($data['airpush_internal_image'])) {
                if(!$for_edit || !empty($data['airpush_internal_image'])) {
                    $message[] = 'AD airpush_internal_image is empty';
                }
            }

            if(!empty($data['fb_page_id'])){
                $insert["fb_page_id"] = $data['fb_page_id'];
            }
            else if(isset($data['fb_page_id']) && empty($data['fb_page_id'])) {
                if(!$for_edit || !empty($data['fb_page_id'])) {
                    //$message[] = 'Facebook page id is empty';
                }
            }

            if(!empty($data['call_to_action'])){

                $insert["call_to_action"] = $data['call_to_action'];
            } else {
//                if(!$for_edit) {
//                    $message[] = 'AD video_url is empty';
//                }
            }

        }
        else if($data['creative_type']=="FB-CAROUSEL-AD"){

            if(strlen($data['title']) > 0 && strlen($data['title']) <= 30){
                $insert["title"] = $data['title'];
            } else {
                //$message[] = 'AD title must be less then 25 characters';
            }
            if(strlen($data['description_1']) > 0 && strlen($data['description_1']) <= 70){
                $insert["description_1"] = $data['description_1'];
            } else {
                //$message[] = 'AD description must be less then 35 characters';
            }

            if(isset($data['fb_description'])){
                $insert["fb_description"] = $data['fb_description'];
            }

            if(isset($data['link_description'])){
                $insert["link_description"] = $data['link_description'];
            }

            if(isset($data['destination'])){
                $insert["destination"] = $data['destination'];
            }

            if(!empty($data['creative_url'])){
                $insert["creative_url"] = $data['creative_url'];
            }
            else if(isset($data['creative_url']) && empty($data['creative_url'])) {
                if(!$for_edit) {
                    $message[] = 'AD creative_url is empty';
                }
            }
            if (!empty($data['destination_url'])) {
                if (!$for_edit) {
                    $insert["destination_url"] = $data['destination_url'];
                }
                $url_array = parse_url($data['destination_url']);
                $url = $url_array['scheme'] . '://' . $url_array['host'];
                $insert["display_url"] = $url_array['host'];
                $insert["original_url"] = $data['destination_url'];
            } else {
                $message[] = 'AD destination_url is empty ddd';
            }

            if(!empty($data['creative_width'])){
                $insert["creative_width"] = $data['creative_width'];
            }
            else if(isset($data['creative_width']) && empty($data['creative_width'])) {
                if(!$for_edit || !empty($data['creative_width'])) {
                    $message[] = 'AD creative_width is empty';
                }
            }
            if(!empty($data['creative_height'])){
                $insert["creative_height"] = $data['creative_height'];
            }
            else if(isset($data['creative_height']) && empty($data['creative_height'])) {
                if(!$for_edit || !empty($data['creative_height'])) {
                    $message[] = 'AD creative_height is empty';
                }
            }

            if(!empty($data['airpush_internal_image'])){
                $insert["airpush_image_type"] = $data['airpush_internal_image'];
            }
            else if(isset($data['airpush_internal_image']) && empty($data['airpush_internal_image'])) {
                if(!$for_edit || !empty($data['airpush_internal_image'])) {
                    $message[] = 'AD airpush_internal_image is empty';
                }
            }

            if(!empty($data['fb_page_id'])){
                $insert["fb_page_id"] = $data['fb_page_id'];
            }
            else if(isset($data['fb_page_id']) && empty($data['fb_page_id'])) {
                if(!$for_edit || !empty($data['fb_page_id'])) {
                    //$message[] = 'Facebook page id is empty';
                }
            }

            if(!empty($data['call_to_action'])){

                $insert["call_to_action"] = $data['call_to_action'];
            } else {
//                if(!$for_edit) {
//                    $message[] = 'AD video_url is empty';
//                }
            }

        }
        else if($data['creative_type']=="VIDEO" || $data['creative_type']=="VIDEO-CLICKS") {

	        if(isset($data['fb_description'])){
                $insert["fb_description"] = $data['fb_description'];
            }

            if(strlen($data['title']) > 0 && strlen($data['title']) <= 30){
                $insert["title"] = $data['title'];
            } else {
                //$message[] = 'AD title must be less then 25 characters';
            }

            if(!empty($data['creative_url'])){

                    $insert["creative_url"] = $data['creative_url'];
            } else {
                if(!$for_edit) {
                    $message[] = 'AD creative_url is empty';
                }
            }

            if(!empty($data['video_url'])){

                    $insert["video_url"] = $data['video_url'];
            } else {
                if(!$for_edit) {
                    $message[] = 'AD video_url is empty';
                }
            }

            if(!empty($data['call_to_action'])){

                $insert["call_to_action"] = $data['call_to_action'];
            } else {
//                if(!$for_edit) {
//                    $message[] = 'AD video_url is empty';
//                }
            }

            if(!empty($data['destination_url'])){
                if(!$for_edit) {
                    $insert["destination_url"] = $data['destination_url'];


                }
                $url_array = parse_url($data['destination_url']);
                $url = $url_array['scheme'].'://'.$url_array['host'];
                $insert["display_url"] = $url_array['host'];
                $insert["original_url"] = $data['destination_url'];
            } else {
                //$message[] = 'AD destination_url is empty';

            }

            if(!empty($data['fb_page_id']) && $data['creative_type']!="VIDEO-CLICKS"){
                $insert["fb_page_id"] = $data['fb_page_id'];
            }
            else if(isset($data['fb_page_id']) && empty($data['fb_page_id'])) {
                if(!$for_edit || !empty($data['fb_page_id'])) {
                    $message[] = 'Facebook page id is empty';
                }
            }

            if(!empty($data['creative_width'])){
                    $insert["creative_width"] = $data['creative_width'];
            } else {
//                if(!$for_edit || !empty($data['creative_url'])) {
//                    $message[] = 'AD creative_width is empty';
//                }
            }
            if(!empty($data['creative_height'])){
                    $insert["creative_height"] = $data['creative_height'];
            } else {
//                if(!$for_edit || !empty($data['creative_url'])) {
//                    $message[] = 'AD creative_height is empty';
//                }
            }
        }
        else if($data['creative_type']=="EVENT") {

            if(!empty($data['event_url'])){

                $insert["event_url"] = $data['event_url'];
            } else {
                if(!$for_edit) {
                    $message[] = 'AD event url is empty';
                }
            }

            if(!empty($data['fb_page_id'])){
                $insert["fb_page_id"] = $data['fb_page_id'];
            }
            else if(isset($data['fb_page_id']) && empty($data['fb_page_id'])) {
                if(!$for_edit || !empty($data['fb_page_id'])) {
                    $message[] = 'Facebook page id is empty';
                }
            }
        }
        else if($data['creative_type']!="DISPLAY_AIRPUSH" && $data['creative_type']!="DISPLAY_YAHOO" && $data['creative_type']!="FB-CAROUSEL-AD" && $data['creative_type']!="VIDEO_YAHOO" && $data['creative_type']!="TEXTAD" && $data['creative_type']!="RICH_MEDIA" && $data['creative_type']!="APP_INSTALL") {
            if(!empty($data['title']) && strlen($data['title']) > 0 && strlen($data['title']) <= 25){
                $insert["title"] = $data['title'];
            } else {
                //$message[] = 'AD title must be less then 25 characters';
            }
            if(isset($data['description_1']) && !empty($data['description_1']) && strlen($data['description_1']) > 0 && strlen($data['description_1']) <= 35){
                $insert["description_1"] = $data['description_1'];
            } else {
                //$message[] = 'AD description must be less then 35 characters';
            }

	        if(isset($data['fb_description'])){
                $insert["fb_description"] = $data['fb_description'];
            }

            if(!empty($data['creative_url'])){

                    $insert["creative_url"] = $data['creative_url'];
            } else {
                if(!$for_edit) {
                    $message[] = 'AD creative_url is empty';
                }
            }

            if(isset($data['destination_url']) && !empty($data['destination_url'])){
                if(!$for_edit) {
                    $insert["destination_url"] = $data['destination_url'];
                }
                $url_array = parse_url($data['destination_url']);
                $url = $url_array['scheme'].'://'.$url_array['host'];
                $insert["display_url"] = $url_array['host'];
                $insert["original_url"] = $data['destination_url'];
            } else {
                //alert($data['creative_type']);
                if ($data['creative_type'] != 'RICH_MEDIA_SURVEY') {
                   $message[] = 'AD destination_url is empty eee';
                }

            }

            if(!empty($data['creative_width'])){
                    $insert["creative_width"] = $data['creative_width'];
            } else {
                if(!$for_edit || !empty($data['creative_url'])) {
                    $message[] = 'AD creative_width is empty';
                }
            }
            if(!empty($data['creative_height'])){
                    $insert["creative_height"] = $data['creative_height'];
            } else {
                if(!$for_edit || !empty($data['creative_url'])) {
                    $message[] = 'AD creative_height is empty';
                }
            }
        }

        if($data['creative_type']=="DISPLAY_AIRPUSH") {


            if(!empty($data['creative_url'])){

                $insert["creative_url"] = $data['creative_url'];
            } else {
                if(!$for_edit && !isset($data['airpush_internal_image'])) {
                    //$message[] = 'AD creative_url is empty';
                }
            }

            if(strlen($data['title']) > 0 && strlen($data['title']) <= 25){
                $insert["title"] = $data['title'];
            } else {
                //$message[] = 'AD title must be less then 25 characters';
            }
            if(strlen($data['description_1']) > 0 && strlen($data['description_1']) <= 40){
                $insert["description_1"] = $data['description_1'];
            } else {
                //$message[] = 'AD description must be less then 35 characters';
            }

            if(isset($data['destination'])){
                $insert["destination"] = $data['destination'];
            }

            if(!empty($data['destination_url'])){
                if(!$for_edit) {
                    $insert["destination_url"] = $data['destination_url'];
                }
                $url_array = parse_url($data['destination_url']);
                $url = $url_array['scheme'].'://'.$url_array['host'];
                $insert["display_url"] = $url_array['host'];
                $insert["original_url"] = $data['destination_url'];
            } else {
                $message[] = 'AD destination_url is empty bbb';

            }

            if(!empty($data['airpush_internal_image'])){
                $insert["airpush_image_type"] = $data['airpush_internal_image'];
            }
            else if(isset($data['airpush_internal_image']) && empty($data['airpush_internal_image'])) {
                if(!$for_edit || !empty($data['airpush_internal_image'])) {
                    $message[] = 'AD airpush_internal_image is empty';
                }
            }

            if(!empty($data['creative_width'])){
                $insert["creative_width"] = $data['creative_width'];
            } else if(!isset($data['airpush_internal_image'])) {
                if(!$for_edit || !empty($data['creative_url'])) {
                    //$message[] = 'AD creative_width is empty';
                }
            }
            if(!empty($data['creative_height'])){
                $insert["creative_height"] = $data['creative_height'];
            } else if(!isset($data['airpush_internal_image'])) {
                if(!$for_edit || !empty($data['creative_url'])) {
                    //$message[] = 'AD creative_height is empty';
                }
            }
        }

        if($data['creative_type']=="RICH_MEDIA") {


//            if(!empty($data['creative_url'])){
//
//                $insert["creative_url"] = $data['creative_url'];
//            } else {
//                if(!$for_edit && !isset($data['airpush_internal_image'])) {
//                    //$message[] = 'AD creative_url is empty';
//                }
//            }
            //var_dump($data); exit;
            if(strlen($data['script']) > 0 && strlen($data['script']) <= 3000){
                $insert["script"] = $data['script'];
            } else {
                $message[] = 'AD script must be less then 3000 characters';
            }

            if(isset($data['destination'])){
                $insert["destination"] = $data['destination'];
            }

            if(!empty($data['destination_url'])){
                if(!$for_edit) {
                    $insert["destination_url"] = $data['destination_url'];
                }
                $url_array = parse_url($data['destination_url']);
                $url = $url_array['scheme'].'://'.$url_array['host'];
                $insert["display_url"] = $url_array['host'];
                $insert["original_url"] = $data['destination_url'];
            } else {
                $message[] = 'AD destination_url is empty bbb';

            }

//            if(!empty($data['airpush_internal_image'])){
//                $insert["airpush_image_type"] = $data['airpush_internal_image'];
//            }
//            else if(isset($data['airpush_internal_image']) && empty($data['airpush_internal_image'])) {
//                if(!$for_edit || !empty($data['airpush_internal_image'])) {
//                    $message[] = 'AD airpush_internal_image is empty';
//                }
//            }

//            if(!empty($data['creative_width'])){
//                $insert["creative_width"] = $data['creative_width'];
//            } else if(!isset($data['airpush_internal_image'])) {
//                if(!$for_edit || !empty($data['creative_url'])) {
//                    //$message[] = 'AD creative_width is empty';
//                }
//            }
//            if(!empty($data['creative_height'])){
//                $insert["creative_height"] = $data['creative_height'];
//            } else if(!isset($data['airpush_internal_image'])) {
//                if(!$for_edit || !empty($data['creative_url'])) {
//                    //$message[] = 'AD creative_height is empty';
//                }
//            }
        }

        if($data['creative_type']=="RICH_MEDIA_SURVEY") {

            if(isset($data['rm_question']) && strlen($data['rm_question']) > 0){
                $insert["rm_question"] = $data['rm_question'];
            } else {
                $message[] = 'Question must not be empty';
            }
            if (isset($data['rm_container'])) {
                $insert["rm_container"] = $data['rm_container'];
            }
            for($i=0;$i<count($data['rm_answer']);$i++ )
            {
                $insert["rm_answere".($i+1)] = $data['rm_answer'][$i];
            }

        }

        if($data['creative_type']=="DISPLAY_YAHOO"){

            if(strlen($data['title']) > 0 && strlen($data['title']) <= 50){
                $insert["title"] = $data['title'];
            } else {
                //$message[] = 'AD title must be less then 25 characters';
            }
            if(strlen($data['description_1']) > 0 && strlen($data['description_1']) <= 160){
                $insert["description_1"] = $data['description_1'];
            } else {
                //$message[] = 'AD description must be less then 35 characters';
            }

            if(!empty($data['creative_url'])){
                $insert["creative_url"] = $data['creative_url'];
            }
            else if(isset($data['creative_url']) && empty($data['creative_url'])) {
                if(!$for_edit) {
                    $message[] = 'AD creative_url is empty';
                }
            }

            if(!empty($data['creative_width'])){
                $insert["creative_width"] = $data['creative_width'];
            }
            else if(isset($data['creative_width']) && empty($data['creative_width'])) {
                if(!$for_edit || !empty($data['creative_width'])) {
                    $message[] = 'AD creative_width is empty';
                }
            }
            if(!empty($data['creative_height'])){
                $insert["creative_height"] = $data['creative_height'];
            }
            else if(isset($data['creative_height']) && empty($data['creative_height'])) {
                if(!$for_edit || !empty($data['creative_height'])) {
                    $message[] = 'AD creative_height is empty';
                }
            }

            if(!empty($data['destination_url'])){
                if(!$for_edit) {
                    $insert["destination_url"] = $data['destination_url'];
                }
                $url_array = parse_url($data['destination_url']);
                $url = $url_array['scheme'].'://'.$url_array['host'];
                $insert["display_url"] = $url_array['host'];
                $insert["original_url"] = $data['destination_url'];
            } else {
                $message[] = 'AD destination_url is empty eee';

            }

            if(!empty($data['call_to_action'])){

                $insert["call_to_action"] = $data['call_to_action'];
            }
        }


        if($data['creative_type']=="APP_INSTALL"){

            if(strlen($data['title']) > 0 && strlen($data['title']) <= 50){
                $insert["title"] = $data['title'];
            } else {
                //$message[] = 'AD title must be less then 25 characters';
            }
            if(strlen($data['description_1']) > 0 && strlen($data['description_1']) <= 160){
                $insert["description_1"] = $data['description_1'];
            } else {
                //$message[] = 'AD description must be less then 35 characters';
            }

            if(!empty($data['creative_url'])){
                $insert["creative_url"] = $data['creative_url'];
            }
            else if(isset($data['creative_url']) && empty($data['creative_url'])) {
                if(!$for_edit) {
                    $message[] = 'AD creative_url is empty';
                }
            }

            if(!empty($data['creative_width'])){
                $insert["creative_width"] = $data['creative_width'];
            }
            else if(isset($data['creative_width']) && empty($data['creative_width'])) {
                if(!$for_edit || !empty($data['creative_width'])) {
                    $message[] = 'AD creative_width is empty';
                }
            }
            if(!empty($data['creative_height'])){
                $insert["creative_height"] = $data['creative_height'];
            }
            else if(isset($data['creative_height']) && empty($data['creative_height'])) {
                if(!$for_edit || !empty($data['creative_height'])) {
                    $message[] = 'AD creative_height is empty';
                }
            }

//            if(!empty($data['destination_url'])){
//                if(!$for_edit) {
//                    $insert["destination_url"] = $data['destination_url'];
//                }
//                $url_array = parse_url($data['destination_url']);
//                $url = $url_array['scheme'].'://'.$url_array['host'];
//                $insert["display_url"] = $url_array['host'];
//                $insert["original_url"] = $data['destination_url'];
//            } else {
//                $message[] = 'AD destination_url is empty eee';
//
//            }

            if(!empty($data['call_to_action'])){

                $insert["call_to_action"] = $data['call_to_action'];
            }
            if(!empty($data['tumblr_post_url'])){

                $insert["tumblr_post_url"] = $data['tumblr_post_url'];
            }
        }
        if( $data['creative_type']=="YAHOO_CAROUSEL"){

            if(!empty($data['square_creative_url'])){
                $insert["square_creative_url"] = $data['square_creative_url'];
            }

        }

        if($data['creative_type']=="VIDEO_YAHOO"){

            if(strlen($data['title']) > 0 && strlen($data['title']) <= 50){
                $insert["title"] = $data['title'];
            } else {
                //$message[] = 'AD title must be less then 25 characters';
            }
            if(strlen($data['description_1']) > 0 && strlen($data['description_1']) <= 160){
                $insert["description_1"] = $data['description_1'];
            } else {
                //$message[] = 'AD description must be less then 35 characters';
            }

            if(!empty($data['creative_url'])){
                $insert["creative_url"] = $data['creative_url'];
            }
            else if(isset($data['creative_url']) && empty($data['creative_url'])) {
                if(!$for_edit) {
                    $message[] = 'AD creative_url is empty';
                }
            }
            if(!empty($data['square_creative_url'])){
                $insert["square_creative_url"] = $data['square_creative_url'];
            }
            else if(isset($data['square_creative_url']) && empty($data['square_creative_url'])) {
                if(!$for_edit) {
                    $message[] = 'AD square_creative_url is empty';
                }
            }

            if(!empty($data['creative_width'])){
                $insert["creative_width"] = $data['creative_width'];
            }
            else if(isset($data['creative_width']) && empty($data['creative_width'])) {
                if(!$for_edit || !empty($data['creative_width'])) {
                    $message[] = 'AD creative_width is empty';
                }
            }

            if(!empty($data['video_url'])){

                $insert["video_url"] = $data['video_url'];
            } else {
                if(!$for_edit) {
                    $message[] = 'AD video_url is empty';
                }
            }

            if(!empty($data['creative_height'])){
                $insert["creative_height"] = $data['creative_height'];
            }
            else if(isset($data['creative_height']) && empty($data['creative_height'])) {
                if(!$for_edit || !empty($data['creative_height'])) {
                    $message[] = 'AD creative_height is empty';
                }
            }

            if(!empty($data['destination_url'])){
                if(!$for_edit) {
                    $insert["destination_url"] = $data['destination_url'];
                }
                $url_array = parse_url($data['destination_url']);
                $url = $url_array['scheme'].'://'.$url_array['host'];
                $insert["display_url"] = $url_array['host'];
                $insert["original_url"] = $data['destination_url'];
            } else {
                $message[] = 'AD destination_url is empty eee';

            }

            if(!empty($data['call_to_action'])){

                $insert["call_to_action"] = $data['call_to_action'];
            }
        }
        return array('messages' => $message, 'valide_ad' => $insert);
    }
//	public function update()   {
//	    $update = array(
//	        "title" => $this->title,
//	        "description" => $this->description,
//	        "category" => $this->category,
//	        "campaign_name" => $this->campaign_name,
//	        "destination_url" => $this->destination_url,
//	        "display_url" => $this->display_url,
//	        "target_radius" => $this->target_radius,
//	        "creative_url" => $this->creative_url,
//	        "bid" => $this->bid,
//	        "daily_cap" => $this->daily_cap,
//	        "ppc_network_ad_active" => "Y",
//	    );
//	    $this->CI->db->update($this->collection, $update, array("id" => $this->id));
//	}

	public function update($id, $data){
		return $this->CI->db->where("id", $id)->update($this->collection, $data);
	}

    public function update_by_network_creative_id($id, $data){
        return $this->CI->db->where("network_creative_id", $id)->update($this->collection, $data);
    }

	public function get_ads_by_campaign_id($campaign_id){
		$result=$this->CI->db->get_where($this->collection, ["campaign_id"=>$campaign_id]);

		return $result->num_rows() ? $result->result_array() : [];
	}

    public function get_active_campaigns_ads_by_network_id($network_id){


        /**
         * if network is fiq we need
         */

        if ($network_id != 2 ){

            $result = $this->CI->db->select([$this->collection.'.*','SUM(v2_campclick_impressions.impressions_count) AS impressions_count','v2_master_campaigns.campaign_type AS campaign_type'])
                ->from($this->collection)
                ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id')
                ->join('v2_campclick_impressions','v2_campclick_impressions.ad_id = '.$this->collection.'.id AND `v2_campclick_impressions`.`is_openrtb`=0', 'left')
                //->where(["v2_campclick_impressions.is_openrtb"=>0])
                //->where(["v2_master_campaigns.network_campaign_status"=>'ACTIVE'])
                ->where(["v2_master_campaigns.campaign_status"=>'ACTIVE'])
                ->where(["v2_master_campaigns.network_id"=>$network_id])
                ->group_by($this->collection.'.id')
                ->order_by($this->collection.'.campaign_id')
                ->get();

        }
        else {

            $date_now = date('Y-m-d');

            $result = $this->CI->db->select([$this->collection.'.*','SUM(v2_campclick_impressions.impressions_count) AS impressions_count'])
                ->from($this->collection)
                ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id')
                ->join('v2_campclick_impressions','v2_campclick_impressions.ad_id = '.$this->collection. ".id AND DATE_FORMAT($date_now, '%j') = DATE_FORMAT(v2_campclick_impressions.timestamp, '%j') ", 'left')
                //->where(["v2_master_campaigns.network_campaign_status"=>'ACTIVE'])
                ->where(["v2_master_campaigns.campaign_status"=>'ACTIVE'])
                ->where([$this->collection.".creative_status"=>'ACTIVE'])
                ->where(["v2_master_campaigns.network_id"=>$network_id])
                ->group_by($this->collection.'.id')
                ->order_by($this->collection.'.campaign_id')
                ->get();
        }

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function get_active_campaigns_ads_by_network_id_and_campaign_type($network_id, $campaign_type){


        /**
         * if network is fiq we need
         */

        if ($network_id != 2 ){

            $result = $this->CI->db->select([$this->collection.'.*','SUM(v2_campclick_impressions.impressions_count) AS impressions_count','v2_master_campaigns.campaign_type AS campaign_type','v2_master_campaigns.form_id AS form_id', 'v2_master_campaigns.name AS campaign_name', 'v2_master_campaigns.io AS io'])
                ->from($this->collection)
                ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id')
                ->join('v2_campclick_impressions','v2_campclick_impressions.ad_id = '.$this->collection. '.id AND `v2_campclick_impressions`.`is_openrtb`=0', 'left')
//                ->where(["v2_campclick_impressions.is_openrtb"=>0])
                ->where(["v2_master_campaigns.network_campaign_status"=>'ACTIVE'])
                ->where(["v2_master_campaigns.network_id"=>$network_id])
                ->where(["v2_master_campaigns.campaign_type"=>$campaign_type])
                ->group_by($this->collection.'.id')
                ->order_by($this->collection.'.campaign_id')
                ->get();

        }
        else {

            $date_now = date('Y-m-d');

            $result = $this->CI->db->select([$this->collection.'.*','SUM(v2_campclick_impressions.impressions_count) AS impressions_count'])
                ->from($this->collection)
                ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id')
                ->join('v2_campclick_impressions','v2_campclick_impressions.ad_id = '.$this->collection. ".id AND DATE_FORMAT($date_now, '%j') = DATE_FORMAT(v2_campclick_impressions.timestamp, '%j') ", 'left')
                ->where(["v2_master_campaigns.network_campaign_status"=>'ACTIVE'])
                ->where([$this->collection.".creative_status"=>'ACTIVE'])
                ->where(["v2_master_campaigns.network_id"=>$network_id])
                ->group_by($this->collection.'.id')
                ->order_by($this->collection.'.campaign_id')
                ->get();
        }

        return $result->num_rows() ? $result->result_array() : [];
    }


    public function get_active_campaigns_ads_by_network_id_likes($network_id){


        /**
         * if network is fiq we need
         */

        if ($network_id != 2 ){

            $result = $this->CI->db->select([$this->collection.'.*','SUM(v2_campclick_likes.likes_count) AS likes_count'])
                ->from($this->collection)
                ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id')
                ->join('v2_campclick_likes','v2_campclick_likes.ad_id = '.$this->collection. ".id", 'left')
                ->where(["v2_master_campaigns.network_campaign_status"=>'ACTIVE'])
                ->where(["v2_master_campaigns.network_id"=>$network_id])
                ->where(["v2_master_campaigns.campaign_type"=>'FB-VIDEO-VIEWS'])
                ->or_where(["v2_master_campaigns.campaign_type"=>'FB-PAGE-LIKE'])
                ->group_by($this->collection.'.id')
                ->order_by($this->collection.'.campaign_id')
                ->get();


        }
        else {

            $date_now = date('Y-m-d');

            $result = $this->CI->db->select([$this->collection.'.*','SUM(v2_campclick_impressions.impressions_count) AS impressions_count'])
                ->from($this->collection)
                ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id')
                ->join('v2_campclick_impressions','v2_campclick_impressions.ad_id = '.$this->collection. ".id AND DATE_FORMAT($date_now, '%j') = DATE_FORMAT(v2_campclick_impressions.timestamp, '%j') ", 'left')
                ->where(["v2_master_campaigns.network_campaign_status"=>'ACTIVE'])
                ->where([$this->collection.".creative_status"=>'ACTIVE'])
                ->where(["v2_master_campaigns.network_id"=>$network_id])
                ->group_by($this->collection.'.id')
                ->order_by($this->collection.'.campaign_id')
                ->get();
        }

        return $result->num_rows() ? $result->result_array() : [];
    }

    public function get_active_campaigns_ads($campaign_id = null){

        $sql = "
                SELECT v2_ads.id, v2_ads.creative_width, v2_ads.creative_height, v2_ads.creative_url, v2_ads.creative_type, v2_ads.destination_url, v2_ads.display_url AS domain_name, v2mc.state, v2mc.country, v2mc.zip, v2mc.io, v2mc.id AS campaign_id, v2mc.radius, v2mc.budget AS daily_budget, v2mc.campaign_status, v2_ads.tracking_pixel
                FROM v2_ads
                JOIN v2_master_campaigns v2mc ON v2mc.id=v2_ads.campaign_id";

        // $where = "
        //         WHERE (v2mc.network_campaign_status='ACTIVE' AND v2mc.campaign_type <> 'EMAIL' AND v2mc.campaign_type NOT LIKE 'FB-%')";

        // Changing `network_campaign_status` to `campaign_status`
        $where = "
                WHERE (v2mc.campaign_status='ACTIVE' AND v2mc.campaign_type <> 'EMAIL' AND v2mc.campaign_type NOT LIKE 'FB-%')";

        if ( !empty($campaign_id) && is_numeric($campaign_id) ) {
            // $where = "
            //     WHERE (v2_ads.campaign_id = ? AND v2mc.network_campaign_status='ACTIVE' AND v2mc.campaign_type <> 'EMAIL' AND v2mc.campaign_type NOT LIKE 'FB-%')";

            // Changing `network_campaign_status` to `campaign_status`
            $where = "
                WHERE (v2_ads.campaign_id = ? AND v2mc.campaign_status='ACTIVE' AND v2mc.campaign_type <> 'EMAIL' AND v2mc.campaign_type NOT LIKE 'FB-%')";
        }

        $sql .= "
                {$where}
                ORDER BY v2_ads.campaign_id";

        $result = $this->CI->db->query($sql, array($campaign_id));

        if ($result->num_rows() > 0) {
            $this->CI->load->model('V2_campaign_cost_model');
            $this->CI->load->model("V2_time_parting_model");

            $currentTime = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));

            $ads = array();
            foreach($result->result_array() as $ad) {
                //print_r($ad);
                $daily_cost = $this->CI->V2_campaign_cost_model->get_daily_cost_by_campaign_id($ad['campaign_id']);

                $timePart = $this->CI->V2_time_parting_model->get_by_campaign_id_dow($ad['campaign_id'], strtolower(date("l")));
                $startTime = DateTime::createFromFormat('H:i a', $timePart['start_time']);
                $endTime = DateTime::createFromFormat('H:i a', $timePart['end_time']);

                if ($currentTime > $startTime && $currentTime < $endTime) {
                    //if ($daily_cost < $ad['daily_budget']) {
                        $ad['daily_cost'] = $daily_cost;

                        // if its marked as ACTIVE -or- DISAPPROVED (its active yet Google flagged us), add it
                        if ($ad['campaign_status'] == "ACTIVE" || $ad['campaign_status'] == "DISAPPROVED") {
                            $ads[] = $ad;
                        }
                   // }
                }
            }

            //print_r($ads);
            return $ads;
        } else {
            return array();
        }
    }


    public function get_active_campaigns_ads_for_adx_submit(){

        $this->CI->load->model('v2_ads_disapproval_model');
        $already_submitted_creative_ids = $this->CI->v2_ads_disapproval_model->get_already_submitted_ad_ids();

        if ( empty($already_submitted_creative_ids) ) {
            $result = $this->CI->db->query("
                SELECT v2_ads.id, v2_ads.creative_width, v2_ads.creative_height, v2_ads.creative_url, v2_ads.creative_type, v2_ads.destination_url, v2_ads.display_url AS domain_name, v2mc.state, v2mc.country, v2mc.zip, v2mc.io, v2mc.id AS campaign_id, v2mc.radius, v2mc.budget AS daily_budget, v2mc.campaign_status, v2_ads.tracking_pixel,
                    v2mc.campaign_type AS campaign_type, v2mc.network_name, v2mc.io AS campaign_io
                FROM v2_ads
                JOIN v2_master_campaigns v2mc ON v2mc.id=v2_ads.campaign_id
                WHERE (v2mc.network_campaign_status='ACTIVE' AND v2mc.campaign_type IN ('DISPLAY', 'DISPLAY-RETARGET') AND v2mc.campaign_type <> 'EMAIL' AND v2mc.campaign_type NOT LIKE 'FB-%')
                ORDER BY v2_ads.campaign_id
            ");
        } else {
            $ad_ids_imploded = implode(',', $already_submitted_creative_ids);
            $result = $this->CI->db->query("
                SELECT v2_ads.id, v2_ads.creative_width, v2_ads.creative_height, v2_ads.creative_url, v2_ads.creative_type, v2_ads.destination_url, v2_ads.display_url AS domain_name, v2mc.state, v2mc.country, v2mc.zip, v2mc.io, v2mc.id AS campaign_id, v2mc.radius, v2mc.budget AS daily_budget, v2mc.campaign_status, v2_ads.tracking_pixel,
                    v2mc.campaign_type AS campaign_type, v2mc.network_name, v2mc.io AS campaign_io
                FROM v2_ads
                JOIN v2_master_campaigns v2mc ON v2mc.id=v2_ads.campaign_id
                WHERE (v2mc.network_campaign_status='ACTIVE' AND v2mc.campaign_type IN ('DISPLAY', 'DISPLAY-RETARGET') AND v2_ads.id NOT IN ({$ad_ids_imploded}) AND v2mc.campaign_type <> 'EMAIL' AND v2mc.campaign_type NOT LIKE 'FB-%')
                ORDER BY v2_ads.campaign_id
            ");
        }

        if ($result->num_rows() > 0) {
            $this->CI->load->model('V2_campaign_cost_model');
            $this->CI->load->model("V2_time_parting_model");

            $currentTime = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));

            $ads = array();
            foreach($result->result_array() as $ad) {
                //print_r($ad);
                $daily_cost = $this->CI->V2_campaign_cost_model->get_daily_cost_by_campaign_id($ad['campaign_id']);

                $timePart = $this->CI->V2_time_parting_model->get_by_campaign_id_dow($ad['campaign_id'], strtolower(date("l")));
                $startTime = DateTime::createFromFormat('H:i a', $timePart['start_time']);
                $endTime = DateTime::createFromFormat('H:i a', $timePart['end_time']);

                if ($currentTime > $startTime && $currentTime < $endTime) {
                    if ($daily_cost < $ad['daily_budget']) {
                        $ad['daily_cost'] = $daily_cost;

                        // if its marked as ACTIVE -or- DISAPPROVED (its active yet Google flagged us), add it
                        if ($ad['campaign_status'] == "ACTIVE" || $ad['campaign_status'] == "DISAPPROVED") {
                            $ads[] = $ad;
                        }
                    }
                }
            }

            //print_r($ads);
            return $ads;
        } else {
            return array();
        }
    }


    public function get_by_id($id)  {
        return $this->CI->db->get_where($this->collection, ["id"=>$id])->row_array();
    }

	public function auto_generate_ad_content($url = "") {
	    if ($url == "")
	        throw new exception("url required");

	    $old_url = $url;
	    if (strpos($url, "cdqr") !== false)    {
			$url = $this->CI->Monitor_model->retrieve_remote_url($url);
		}

	    if ($url == "")
	        $url = $old_url;

	    // get page title
	    //$urlContents = file_get_contents($url);

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0");
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
	    $urlContents = curl_exec($ch);
	    curl_close($ch);

	    /*
	    preg_match("/<title>(.*)<\/title>/i", $urlContents, $matches);
	    $title = $matches[1];
        */

	    $doc = new DOMDocument();
	    @$doc->loadHTML($urlContents);
	    $nodes = $doc->getElementsByTagName('title');
	    $title = trim($nodes->item(0)->nodeValue);

	    $meta = get_meta_tags($url);
	    $parse = parse_url($url);
	    //print_r($meta);

        $title = ($title != "") ? substr($title, 0, 25) : "Special Offer for You";
	    $description = ($meta['description'] != "") ? (substr($meta['description'],0,59) . " Click Now!") : "To learn more about this special offer, click now!";

	    if ($parse['host'] == "")  {
	        $parse['host'] = "www.specialdiscounts.com";
	    }

	    $ad = array(
	        "display_url" => "http://" . $parse['host'],
	        "title" => $title,
	        "description" => $description
	    );

	    return $ad;
	}

	public function remove()   {
	    $this->CI->db->query("DELETE FROM ads WHERE id='{$this->id}'");
	}

	public function __get($name)	{
		return $this->$name;
	}

	public function __set($name, $value)	{
		$this->$name = $value;
	}

	public function __isset($name)	{
		return isset($this->$name);
	}

    public function get_by_campaign_id($campaign_id){

        return $this->CI->db->get_where($this->collection, ['campaign_id' => $campaign_id])->result_array();
    }

    public function get_with_clicks_by_campaign_id($campaign_id){

        $result = $this->CI->db->select(
                $this->collection.'.*, '
                . 'count(v2_campclick_clicks.id) as clicks_count, '
                . 'v2_ads_links.destination_url as redirect_url,  '
                . 'v2_ads_links.id as ad_link_id, '
                . '(SELECT SUM(v2_campclick_impressions.`impressions_count`) from v2_campclick_impressions WHERE v2_campclick_impressions.ad_id = v2_ads.id) as impressions_count'
                )
            ->from($this->collection)
            ->join('v2_campclick_clicks','v2_campclick_clicks.ad_id = '.$this->collection.'.id', 'left')
            //->join('','v2_campclick_impressions.ad_id = '.$this->collection.'.id', 'left')
            ->join('v2_ads_links','v2_ads_links.ad_id = '.$this->collection.'.id')
            ->where(''.$this->collection.'.campaign_id',$campaign_id)
            ->group_by('v2_ads.id')
            ->order_by('v2_ads.create_date', 'DESC')
            ->get();
        return $result->result_array();
    }

    public function get_with_likes_by_campaign_id($campaign_id){

        $result = $this->CI->db->select(
            $this->collection.'.*, '
            . 'SUM(v2_campclick_likes.likes_count) as clicks_count, '
            . 'v2_ads_links.destination_url as redirect_url,  '
            . 'v2_ads_links.id as ad_link_id, '
            . '(SELECT SUM(v2_campclick_impressions.`impressions_count`) from v2_campclick_impressions WHERE v2_campclick_impressions.ad_id = v2_ads.id) as impressions_count'
        )
            ->from($this->collection)
            ->join('v2_campclick_likes','v2_campclick_likes.ad_id = '.$this->collection.'.id', 'left')
            //->join('','v2_campclick_impressions.ad_id = '.$this->collection.'.id', 'left')
            ->join('v2_ads_links','v2_ads_links.ad_id = '.$this->collection.'.id')
            ->where(''.$this->collection.'.campaign_id',$campaign_id)
            ->group_by('v2_ads.id')
            ->order_by('v2_ads.create_date', 'DESC')
            ->get();
        return $result->result_array();
    }

    public function get_by_approval_status($status){

        return $this->CI->db->select($this->collection.'.*, v2_master_campaigns.network_name')
            ->from($this->collection)
            ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id')
            ->where(''.$this->collection.'.approval_status',$status)
            ->where(''.$this->collection.'.creative_is_active',"Y")
            ->get()->result_array();
    }

    public function get_network_names_by_approval_status($status){

        return $this->CI->db->select('v2_master_campaigns.network_name')
            ->from($this->collection)
            ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id')
            ->where(''.$this->collection.'.approval_status',$status)
            ->where(''.$this->collection.'.creative_is_active',"Y")
            ->group_by('v2_master_campaigns.network_name')
            ->get()->result_array();
    }

    public function get_by_approval_status_and_network_id($status, $network_id){

        return $this->CI->db
            ->where('approval_status',$status)
            ->where('creative_is_active',"Y")
            ->where('network_id',$network_id)
            ->get($this->collection)->result_array();
    }

    public function get_campaign_id_by_ad_approval_status_and_network_id($status, $network_id){

        return $this->CI->db->select($this->collection.'.network_campaign_id')
            ->from($this->collection)
            ->where('approval_status',$status)
            ->where('creative_is_active',"Y")
            ->where('network_id',$network_id)
            ->group_by('network_campaign_id')
            ->get()->result_array();
    }

    public function get_ads_with_campaign_by_approval_status_and_network_id($status, $network_id){

        return $this->CI->db->select($this->collection.'.*, v2_master_campaigns.name AS campaign_name, v2_master_campaigns.io AS campaign_io, v2_master_campaigns.campaign_type AS campaign_type, users.email AS email, v2_master_campaigns.userid as userid')
            ->from($this->collection)
            ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id')
            ->join('users', 'users.id = v2_master_campaigns.userid','left')
            ->where($this->collection.'.approval_status',$status)
            ->where($this->collection.'.creative_is_active',"Y")
            ->where($this->collection.'.network_id',$network_id)
            //->group_by($this->collection.'.network_campaign_id')
            ->get()->result_array();
    }

    public function get_with_network_name_by_id($id){

        return $this->CI->db->select($this->collection.'.*, v2_master_campaigns.network_name')
            ->from($this->collection)
            ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id')
            ->where(''.$this->collection.'.id',$id)
            ->get()->row_array();
    }

    public function update_all_by_campaign_id($campaign_id, $data){
            return $this->CI->db->where("campaign_id", $campaign_id)->update($this->collection, $data);
    }

    /**
     * This Method is being used by adx controller's resubmit_creatives() method
     */
    public function get_all_ads_with_campaign_info($status = null, $network_id = 1){

        $sql_inst = $this->CI->db->select([
                $this->collection . '.id',
                $this->collection . '.creative_width',
                $this->collection . '.creative_height',
                $this->collection . '.destination_url',
                'v2_master_campaigns.io AS campaign_io',
                'v2_master_campaigns.campaign_type AS campaign_type',
                'v2_master_campaigns.network_name'
            ])
            ->from($this->collection)
            ->join('v2_master_campaigns','v2_master_campaigns.id = '.$this->collection.'.campaign_id')
            ->where($this->collection.'.creative_is_active', 'Y')
            ->where($this->collection.'.network_id', 1) // for GOOGLE ads only
            ->where_in('v2_master_campaigns.campaign_type', ['DISPLAY', 'DISPLAY-RETARGET']);

        if ( !empty($status) ) {
            $sql_inst->where($this->collection.'.approval_status', $status);
        }

        return $sql_inst->get()->result_array();
    }
    public function copy_new_ad($new_ads){
        $new_ads['id']=null;
        $this->CI->db->insert($this->collection, $new_ads);
        $this->id = $this->CI->db->insert_id();
        return $this->id;
    }
    public function update_destination_url_by_campaign_id($ad_id, $dest_url){
        $data = array(
            'destination_url' => $dest_url
        );
        return $this->CI->db->where("id", $ad_id)->update($this->collection, $data);
    }
}

?>
