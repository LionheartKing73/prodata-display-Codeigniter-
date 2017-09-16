$(document).ready(function() {

    //Add network for user
    $('#btn_add_network').on('click', function() {

        var network = $( "select[name='network'] option:selected" ).text();
        var campaignType = $( "select[name='campaign_type'] option:selected" ).text();


        $.ajax({
            url: "/v2/admin/add_user_network",
            type: "POST",
            dataType: "json",
            data: $('#add_network_form').serialize(),
            success: function(data)  {

                if(data.success) {
                    $('.network_list').append('<div class="row network_row" >' +
                        '<div class="col-sm-4 network_info" >' + network + '</div>' +
                        '<div class="col-sm-4 network_info" >' + campaignType + '</div>' +
                        '<div class="col-sm-4" >' +
                        '<span net_id="' + data.networkId + '" class="glyphicon glyphicon-remove remove_network" ></span>' +
                        '</div></div>');
                }
                else {
                    alert(data.msg);
                }

            }
        });

    });

    //Delete network for user
    $('.network_list').on('click', '.remove_network', function() {

        var This = $(this);
        var netId = This.attr('net_id');

        $.ajax({
            url: "/v2/admin/delete_user_network",
            type: "POST",
            dataType: "json",
            data: { net_id: netId },
            success: function(data)  {

                if(data.success) {

                    This.closest('.network_row').remove();
                }
                else {
                    alert('something went wrong');
                }


            }
        });

    })


});