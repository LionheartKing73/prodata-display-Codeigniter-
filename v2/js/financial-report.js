$(document).ready(function(){

    var start_date = new Date();
    start_date.setDate(start_date.getDate() - 15)
    $("#start_date_datepicker1").datetimepicker({
        format: "Y/m/d",
        // onShow:function( ct ){ console.log($('#end_date_datepicker1').val());
        //     this.setOptions({
        //         maxDate:$('#end_date_datepicker1').val()?$('#end_date_datepicker1').val():false
        //     })
        // },
        // onChangeDateTime: function(currentDateTime){  console.log($('#end_date_datepicker1').val());
        //     $("#end_date_datepicker1").datetimepicker('setOptions', {
        //         minDate:$('#start_date_datepicker1').val()?$('#start_date_datepicker1').val():false,
        //     })
        // },
        // timepicker:false
    });
    $("#end_date_datepicker1").datetimepicker({
        format: "Y/m/d",
        // onShow:function( ct ){ console.log($('#start_date_datepicker1').val());
        //     this.setOptions({
        //         minDate:$('#start_date_datepicker1').val()?$('#start_date_datepicker1').val():false
        //     })
        // },
        // timepicker:false
    });

    $('#submit_form').click(function(event){
        event.preventDefault();

        var data = $("#theme-report-schedule-form").serialize();
        show_loader();
        $.post('/v2/campaign/financial_report', data, function(result){
            hide_loader();
            var data = JSON.parse(result);
            if(data.status == 'SUCCESS') {
                $('.table-responsive').html(data.html);
                $('#mytable').tablesorter();
            } else {
                alert(data.message);
            }
        });
    });
});
