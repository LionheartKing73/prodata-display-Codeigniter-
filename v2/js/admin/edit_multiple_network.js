$(document).ready(function() {

    $('#add_network_form').validate({

        rules: {
            'multiple_networks_ids[]': {
                required:  true
            },

            'general_network_id': {
                required:  true
            },

            'percent_of_budget': {
                number : true,
                maxlength : 2,
                required:  true
            }
        },

        submitHandler: function() {
            var data = $("#add_network_form").serialize();
            //show_loader();
            $.post('/v2/admin/add_user_multiple_network', data, function(result){
                //hide_loader();
                var data = JSON.parse(result);
                if(data.success) {
                    location.reload();
                }
                else {
                    alert(data.msg);
                }
            });
        }
    });

    $('select[name="general_network_id"]').on('change', function() {
        $('select[name="multiple_networks_ids[]"] option').attr("disabled", "disabled");
        if ($(this).val() == 2 || $(this).val() == 4){
            $('select[name="multiple_networks_ids[]"] option[value="1"]').removeAttr("disabled");
        }
    });

    $('select[name="general_network_id"]').trigger('change');

    //Delete network for user
    $('#theme-sortable-table').on('click', '.remove_network', function() {

        var This = $(this);
        var netId = This.attr('data-id');

        $.ajax({
            url: "/v2/admin/delete_multiple_network",
            type: "POST",
            dataType: "json",
            data: { id: netId },
            success: function(data)  {

                if(data.success) {

                    This.closest('.theme-table-row').remove();
                }
                else {
                    alert('something went wrong');
                }


            }
        });

    });

    $('.int_editable').editable({
        validate: function(value) {
            if(!$.isNumeric(value)) {
                return ' The field must be integer';
            }
        }
    });


});