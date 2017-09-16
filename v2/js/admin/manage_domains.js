$(document).ready(function() {

    $('#add_domain_form').validate({

        rules: {
            'domain[domain]': {
                required:  true,
                url: true,
            },

            'domain[company_name]': {
                required:  true
            },

            'domain[company_email]': {
                required:  true,
                email:  true
            },

            'domain[logo]': {
                required:  true
            },

            'domain[background_color]': {
                required:  true
            },

            'domain[footer_color]': {
                required:  true
            },

            'domain[active_button_color]': {
                required:  true
            },

            'domain[passive_button_color]': {
                required:  true
            },

            'domain[block_header_background_color]': {
                required:  true
            },

            'domain[block_header_icon_color]': {
                required:  true
            },

            'domain[block_header_text_color]': {
                required:  true
            },

            'domain[content_background_color]': {
                required:  true
            },

            'domain[content_text_color]': {
                required:  true
            },

            'domain[block_content_text_color]': {
                required:  true
            },

            // 'general_network_id': {
            //     required:  true
            // },
            //
            // 'percent_of_budget': {
            //     number : true,
            //     maxlength : 2,
            //     required:  true
            // }
        },

        submitHandler: function() {
            var data = $("#add_domain_form").serialize();

            $.post('/v2/admin/add_domain', data, function(result){

                var data = JSON.parse(result);
                if(data.status) {
                    location.reload();
                }
                else {
                    alert(data.msg);
                }
            });
        }
    });

    //Delete network for user
    $('#theme-sortable-table').on('click', '.remove_domain', function() {

        var This = $(this);
        var netId = This.attr('data-id');

        $.ajax({
            url: "/v2/admin/delete_domain",
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

    $("#uploader").plupload({
        // General settings
        runtimes : 'html5,html4',
        url : '/v2/admin/upload_logo',
        max_file_count: 1,

        chunk_size: '1mb',

        // Resize images on clientside if we can
        resize : {
            width : 167,
            height : 53,
            quality : 90,
            crop: true // crop to exact dimensions
        },

        filters : {
            // Maximum file size
            max_file_size : '1000mb',
            // Specify what files to browse for
            mime_types: [
                {title : "Image files", extensions : "jpeg,jpg,png,gif"}
            ]
        },

        // Rename files by clicking on their titles
        rename: true,

        // Sort files
        sortable: true,

        // Enable ability to drag'n'drop files onto the widget (currently only HTML5 supports that)
        dragdrop: true,

        // Views to activate
        views: {
            list: true,
            thumbs: true, // Show thumbs
            active: 'thumbs'
        }
    });

    var uploader = $("#uploader").plupload('getUploader');

    uploader.bind('FileUploaded', function (upldr, file, object) {

        var response;
        try {
            response = eval(object.response);
        }
        catch (err) {
            response = eval('(' + object.response + ')');
        }

        if(response.status){
            $('#uploaded_logo').val(response.file_name);
        }
        else {
            alert(response.message);
        }
    });



});