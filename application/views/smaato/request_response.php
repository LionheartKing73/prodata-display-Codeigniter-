{
    "id": "<?php echo $response_data['id']; ?>",
    "seatbid": [
        {
            "bid": [
                {
                    "id": "<?php echo  $response_data['imp'][0]['id']; ?>",
                    "impid": "<?php echo  $response_data['imp'][0]['id']; ?>",
                    "price": 0.5,
                    "adm":"<?php echo $ad_type; ?>",
                    "nurl": "http://reporting.prodata.media/bidder/win/<?php echo $bid_id; ?>/${AUCTION_PRICE}"
                }
            ],
            "seat": "2"
        }
    ],
    "cur": "USD"
}
