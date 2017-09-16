<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class bidder extends CI_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->library('parser');
        $this->load->helper('url');
    }
    
    public function request(){
        
        //var_dump(444); exit;
        if($_GET["testbid"] == "nobid"){
            $this->output->set_status_header('204', 'text');
        }
                
        $post_data = file_get_contents("php://input");
        
        $response_data = json_decode($post_data, true);
        
        $ad_type = explode('/' ,$response_data['imp'][0]['banner']['mimes'][0])[0];
        
        //$ads = $this->lib($response_data);
        $this->db->insert('smaato_test', ['data' => $post_data]);
        $bid_id = $this->db->insert_id();
        
        $ads = [
            [
                'id' => 1,
                'campaign_id' => 2,
                'type' => $ad_type,
                'w' => 150,
                'h' => 30,
                'click_url' => 'http://mysite.com/landingpages/mypage/',
                'img_url' => 'dada',
                'text' => 'Text', 
            ],
            [
                'id' => 2,
                'campaign_id' => 2,
                'type' => $ad_type,
                'w' => 150,
                'h' => 30,
                'click_url' => 'http://mysite.com/landingpages/mypage/',
                'img_url' => 'dada',
                'text' => 'Text', 
            ]
        ];
        
        if ($ads){
            $this->return_response($ads, $response_data, $bid_id);
        }
        else {
            $this->output->set_status_header('204', 'text');
        }

              
        switch($ad_type) {
            
            case 'image': 
                $ad_type = "<ad modelVersion='0.9'><imageAd><clickUrl>http://mysite.com/landingpages/mypage/</clickUrl><imgUrl>http://149.154.70.183/test.png</imgUrl><width>" . $response_data['imp'][0]['banner']['w'] . "</width><height>" .  $response_data['imp'][0]['banner']['h'] . "</height> <toolTip>This is a tooltip text</toolTip><additionalText>Additional text to be displayed</additionalText></imageAd></ad>";
                break;
            case 'application':
                $ad_type = "<ad modelVersion='0.9'><richmediaAd><content></content><width>" . $response_data['imp'][0]['banner']['w'] . "</width><height>" .  $response_data['imp'][0]['banner']['h'] . "</height></richmediaAd>></ad>";
                break;
            case 'text':
                $ad_type = "<ad modelVersion='0.9'><textAd><clickText>Text</clickText><clickUrl>http://mysite.com/landingpages/mypage/</clickUrl><toolTip>This is a tooltip text</toolTip><additionalText>Additional text to be displayed</additionalText></textAd></ad>";
                break;
        }

        $this->db->insert('smaato_test', ['data' => $post_data]);
        $bid_id = $this->db->insert_id();
        $this->load->view('smaato/request_response', ['response_data' => $response_data, 'ad_type' => $ad_type, 'bid_id' => $bid_id]);
    }
    
    public function win($id, $win_price) {
        
        $this->db->where('id', $id);
        $this->db->update('smaato_test', ['win_price' => $win_price, 'is_win' => 1]);   
    }
    
    public function result(){
        
        $count_bid = $this->db->from('smaato_test')->count_all_results();
        
        $result_array = [
            'bids' => $count_bid,
            'no_bids' => 0,
            'won_auctions' => $this->db->from('smaato_test')->where('is_win', 1)->count_all_results(),
            'sum_all_bid_price' => $count_bid * 45,
            'sum_all_won_price' =>$this->db->where('is_win', 1)->select_sum('win_price')->get('smaato_test')->result_array()[0]['win_price']
        ];
        
        echo "<pre>";
        
        print_r($result_array);die;
                
    }
    
    /*
     * generate and return reposne to smaato
     * $ads our db ads, $request_data data from smaato,  $bid_id our db request id
     */
    
    public function return_response($ads, $request_data, $bid_id){
        
        $seat_bid = [];
        
        foreach ($ads as $ad) {
            
            switch($ad['type']) {

                case 'image': 
                    $ad_type = "<ad modelVersion='0.9'><imageAd><clickUrl>http://mysite.com/landingpages/mypage/</clickUrl><imgUrl>http://149.154.70.183/test.png</imgUrl><width>" . $request_data['imp'][0]['banner']['w'] . "</width><height>" .  $request_data['imp'][0]['banner']['h'] . "</height> <toolTip>This is a tooltip text</toolTip><additionalText>Additional text to be displayed</additionalText></imageAd></ad>";
                    break;
                case 'application':
                    $ad_type = "<ad modelVersion='0.9'><richmediaAd><content></content><width>" . $request_data['imp'][0]['banner']['w'] . "</width><height>" .  $request_data['imp'][0]['banner']['h'] . "</height></richmediaAd>></ad>";
                    break;
                case 'text':
                    $ad_type = "<ad modelVersion='0.9'><textAd><clickText>Text</clickText><clickUrl>" .$ad['click_url'] . "</clickUrl><toolTip>This is a tooltip text</toolTip><additionalText>Additional text to be displayed</additionalText></textAd><beacons><beacon>" . base_url() . 'v2/smaato/beacon/' .  $bid_id . '/' . $ad['id'] . '/' . $ad['campaign_id'] ."</beacon></beacons></ad>";
                    break;
            }
            
            $seat_bid[] = [
                'bid' => [
                    [
                        'id' => $ad['id'],
                        'impid' => 15,
                        'price' => 45,
                        'adm' => $ad_type,
                        'nurl' => base_url() . 'v2/bidder/win/' . $bid_id . '/${AUCTION_PRICE}'
                    ],
                ]
            ]; 
        }
        
        $response_array = [
            'id' => $request_data['id'],
            'seatbid' => $seat_bid
        ];
        
        echo json_encode($response_array, true); die;
                 
    }
    
    /*
     * check iurl working
     */
    
    
    public function iurl($id, $win_price){
        
        $this->db->where('id', $id);
        $this->db->update('smaato_test', ['win_price' => $win_price, 'iurl' => 1]);     
    }
    
    /*
     * url for save beacon da from smaato 
     */
    
    
    public function beacon($bid_id, $ad_id, $campaign_id){
        
        /*
         * Beacon call to get ad impresion count
         * 
         */
        $this->db->insert('smaato_test', ['data' => json_encode($_REQUEST)]);
    }
    
}