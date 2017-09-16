$(document).ready(function(){
    $('[data-toggle="popover"]').popover({
        trigger: "hover",
        html: true
    });

    $('.toggle').toggles();
    // Getting notified of changes, and the new state:
    $('.toggle').on('toggle', function(e, active) {
        console.log($(this).attr('data-ad-id'));
        var data = {
            'ad_id':$(this).attr('data-ad-id'),
            'campaign_id':$(this).attr('data-campaign-id'),
        }
        if (active) {
            data.status = 'ACTIVE';
        } else {
            data.status = 'PAUSED';
        }
        show_loader();
        $.post('/v2/campaign/edit_ad_status', data, function(result){
            hide_loader();
            var data = JSON.parse(result);
            if(data.status == 'SUCCESS') {
                alert(data.message);
            } else {
                alert(data.message);
            }
        });
    });
    // ajax call for edit destination url
    //var data = $("#theme-link-form").serialize();
    //$.post('/v2/campaign/edit_link', data, function(result){
    //    var data = JSON.parse(result);
    //    if(data.status == 'SUCCESS') {
    //        alert(data.message);
    //    } else {
    //        alert(data.message);
    //    }
    //});
});