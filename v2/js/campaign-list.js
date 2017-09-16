$(document).ready(function(){
    var campaign_status = $("select[name='campaign_status']").find(":selected").val();
    $('.theme-report-campaigne-row-title h1').html(campaign_status + ' CAMPAIGNS');

    var start_date = new Date();
    start_date.setDate(start_date.getDate() - 15)
    $("#start_date_datepicker1").datetimepicker({
        format: "Y/m/d H:i",
        //minDate: '-1970/01/8',
    });
    $("#end_date_datepicker1").datetimepicker({
        format: "Y/m/d H:i",
        //minDate: '-1970/01/8',
        //maxDate: '+1970/01/1',
    });

    $(document).on('click', 'ul.pagination a', function(event){
        event.preventDefault();

        var data = $("#theme-report-schedule-form").serialize();
        var url = $(this).attr('href');
        var page = url.substring( url.lastIndexOf('/') + 1 );
        if(!page) {
            page = 0;
        }
        show_loader();
        $.post('/v2/campaign/campaign_list/'+page, data, function(result){
            hide_loader();
            var data = JSON.parse(result);
            if(data.status == 'SUCCESS') {
                $('[data-toggle="popover"]').popover({
                    trigger: "hover",
                    html: true
                });
                $('#content_for_table').html(data.html);
            } else {
                alert(data.message);
            }
        });
    });

    $('#submit_form').click(function(event){
        event.preventDefault();

        var data = $("#theme-report-schedule-form").serialize();
        show_loader();
        $.post('/v2/campaign/campaign_list', data, function(result){
            hide_loader();
            var data = JSON.parse(result);
            if(data.status == 'SUCCESS') {
                var campaign_status = $('select[name="campaign_status"] option:selected').val();
                $('.theme-report-campaigne-row-title h1').html(campaign_status + ' CAMPAIGNS');

                // $("[name='campaign_status'] option").each(function(){
                //     $(this).removeAttr("selected");
                //     console.log("VAL - "+$(this).val());
                //     console.log("STATUS - " + campaign_status);
                //     if($(this).val()==campaign_status){
                //         $(this).attr("selected","selected");
                //     }
                // });

                $('#content_for_table').html(data.html);
                $('#theme-sortable-table').tablesorter();
                $('[data-toggle="popover"]').popover({
                    trigger: "hover",
                    html: true
                });
            } else {
                alert(data.message);
            }
        });
    });
    $(document).on('click', '.copy_campaign', function() {
        var check = confirm("Are you sure you want to copy this campaign?");
        if (check == true) {
            var id = $(this).parent().next().val();
            $.ajax({
                url: "/v2/campaign/copy_campaign/"+id,
                type: "POST",
                beforeSend: function(){
                    $('#loader_div').removeClass('hidden');
                }, 
                success: function( data ) {
                    if(data==false){
                        alert('The company you choose to copy is in the status of Scheduled');
                    }
                    else{
                        alert('Campaign is copied');
                        $('#loader_div').addClass('hidden');
                        window.location.href=data;
                    }                    
                }
            });
            
        }
    });
});

//$('#theme-report-schedule-form').validate({
//
//    rules: {
//
//        //'card_number': {
//        //    required: true,
//        //    number : true,
//        //    maxlength : 16,
//        //    minlength:16
//        //},
//        'keywords': {
//            require_from_group: [1, ".fillone"]
//        },
//        //'exp-month': {
//        //    required: true,
//        //    number : true
//        //},
//        //'exp-year': {
//        //    required: true,
//        //    number : true
//        //},
//        //'bill_name': {
//        //    required: true
//        //},
//        //'bill_address': {
//        //    required: true
//        //},
//        //'postal_code': {
//        //    required: true
//        //},
//        //'txt_agrement' : {
//        //    required: true
//        //}
//    }
//});