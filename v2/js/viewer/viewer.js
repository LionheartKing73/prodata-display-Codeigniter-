$(document).ready(function() {

    //Add network for user
    $('#btn_add_viewer').on('click', function() {

        var campaign = $( "select[name='campaign'] option:selected" ).text();
        var viewer = $( "select[name='viewer'] option:selected" ).text();


        $.ajax({
            url: "/v2/profile/add_viewer_access_to_campaign",
            type: "POST",
            dataType: "json",
            data: $('#manage_viewer_form').serialize(),
            success: function(data)  {

                if(data.success) {
                    $('.campaign_list').append('<div class="row network_row" >' +
                        '<div class="col-sm-4 network_info" >' + campaign + '</div>' +
                        '<div class="col-sm-4 network_info" >' + viewer + '</div>' +
                        '<div class="col-sm-4" >' +
                        '<span viewer_id="' + data.viewer_id + '" class="glyphicon glyphicon-remove remove_network" ></span>' +
                        '</div></div>');
                }
                else {
                    alert(data.msg);
                }

            }
        });

    });

    //Delete network for user
    $('.remove_network').on('click', function() {

        var This = $(this);
        var viewer_id = This.attr('viewer_id');

        $.ajax({
            url: "/v2/profile/delete_viewer_access_to_campaign",
            type: "POST",
            dataType: "json",
            data: { viewer_id: viewer_id },
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