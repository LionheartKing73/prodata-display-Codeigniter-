var add_form = {
    creative_type : 'VIDEO_YAHOO',
    //campaign_id: $('#campaign_id').val()
};

$(document).ready(function(){

    $('#btn_add_ad').on('click', function(){
        uploader.splice();

        $('#ad_modal input').not('input[name=campaign_id]').val('');
        $('#create_new_add').val('Create New Ad');
        $('#ad_modal .modal-title').text('Create your text Ad');
        $('#create_new_add').text('Create New Ad');
        $('#ad_modal textarea').val('');
        $('#uploaded_image_prew').html('');
        add_form['creative_url'] = '';
        add_form['creative_width'] = '';
        add_form['creative_height'] = '';

    });

    $('#create_new_add').on('click', function(){

        var valid_ad = true;
        var button_name = $(this).val();
        if(button_name == 'Create New Ad') {
            if (!add_form.creative_url) {
                alert('please upload image');
                return false;
            }
        }

        var fbAdTitle = $('#fb_ad_title');
        var fbAdBody = $('#fb_ad_body');
        if(fbAdTitle.val() == '' || fbAdTitle.val().length > 30 || fbAdTitle.val().length < 3) {

            fbAdTitle.addClass('error');
            alert('Enter correct title(Max 30, Min 3 characters)');
            return false;

        }else{
            fbAdTitle.removeClass('error');
        }
        if(fbAdBody.val() == '' || fbAdBody.val() > 90 || fbAdBody.val() < 3) {

            fbAdBody.addClass('error');
            alert('Enter correct description(Max 90, Min 3 characters)');
            return false;
        }else{
            fbAdTitle.removeClass('error');
        }
        function isUpperCase(str) {
            return str === str.toUpperCase();
        }
        if( isUpperCase(fbAdBody.val())){
            alert("Your description can't be in all capital letters.");
            return false;
        }





        $('.theme-create-ad-form-wrap').find('.theme-geoform-control').each(function(key, value){

            var input_value = ($(this).attr('name') === 'keywords' && !$(this).val()) ? 'RON' : $(this).val();

            if (!input_value && $(this.name)){
                if(button_name == 'Create New Ad') {
                    if ($(this).attr('name') != 'ad_id' && $(this).attr('name') != 'ad_link_id') {
                        valid_ad = false;
                        $(this).addClass('error');
                    }
                }
                else {
                    valid_ad = false;
                    $(this).addClass('error');
                }
            }
            else {
                if ($(this).attr('type') === 'url' && !learnRegExp(input_value)){
                    valid_ad = false;
                    $(this).addClass('error');
                    alert('The destination URL is ivalid.');
                }
            }
            add_form[$(this).attr('name')] = input_value;

        });
        //console.log(add_form, valid_ad); return false;
        //var input = $('#modal_dest_url');
        //
        //add_form['url'] = input.val();
        //
        //if (!learnRegExp(add_form.url)){
        //    input.addClass('error');
        //    return false;
        //}
        //else {
        //    add_form['destination_url'] = input.val();
        //}

        if (valid_ad){
            var url = "/v2/campaign/";
            if(button_name == "Create New Ad") {
                url +="create_ad";
            } else {
                url +="edit_ad";
            }
            $.ajax({
                url: url,
                type: "POST",
                dataType: "json",
                beforeSend: function(){
                    show_loader($('#create_new_add'));
                },
                data: {
                    ad: JSON.stringify(add_form)
                },
                success: function(msg)  {
                    hide_loader();
                    //var result = JSON.parse(msg);
                    console.log(msg);
                    if (msg.status == "SUCCESS")    {
                        alert(msg.message);
                        window.location.href = "/v2/campaign/ad_list/"+add_form.campaign_id;
                    } else {
                        alert(msg.message);
                    }
                },
                error: function(error){
                    hide_loader();
                    alert("You have some error");
                    console.log("error = ", error.responseText);
                }
            });

        }
        else {
            return false;
        }

    });


    $("#theme-file-uploader").plupload({
        // General settings
        runtimes : 'html5,html4',
        url : '/v2/campaign/uploadFile',
        max_file_count: 1,

        chunk_size: '1mb',

        // Resize images on clientside if we can
        resize : {
            quality : 90,
            crop: true // crop to exact dimensions
        },

        filters : {
            // Maximum file size
            max_file_size : '1000mb',
            // Specify what files to browse for
            mime_types: [
                {title : "Video files", extensions : "mov,mp4" }
            ]
        },

        init: {

            BeforeUpload: function (up, file) {

                campaignSubType = 'VIDEO_YAHOO';
                // creative_type = 'VIDEO';
                up.settings.multipart_params = {campaignSubType: campaignSubType };

            }
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

    var uploader = $("#theme-file-uploader").plupload('getUploader');

    uploader.bind('FileUploaded', function (upldr, file, object) {

        var response;
        try {
            response = eval(object.response);
        }
        catch (err) {
            response = eval('(' + object.response + ')');
        }

        if(response.status){
            //uploader.splice();
            add_form['video_url'] = response.file_dir;
            add_form['creative_width'] = response.creative_width;
            add_form['creative_height'] = response.creative_height;


            var padding = 0;
            if(response.creative_height < 110){
                padding = (110-response.creative_height)/2 + "px";
            }
            $("#uploaded_image_prew").html('<figure class="height220" style="padding-top:' + padding + '; margin-bottom:15px" ><video width="450" height="320" controls class="theme-imagead-subrow-bottom"><source src="' + response.file_dir +'" type="video/mp4">Your browser does not support the video tag.</video></figure>');
        }
        else {
            alert(response.message);
            add_form['creative_url'] = '';
            add_form['creative_width'] = '';
            add_form['creative_height'] = '';
        }
    });

    $('.btn_edit_modal').on('click', function(){

        var ad_data = $(this).data('ad'); console.log(ad_data.redirect_url);

        $('#ad_modal .modal-title').text('Edit your Display Ad');

        $("#ad_modal input[name=destination_url]").val(ad_data.redirect_url);
        $("#ad_modal input[name=ad_id]").val(ad_data.id);
        $("#ad_modal input[name=ad_link_id]").val(ad_data.ad_link_id);
        $("#ad_modal input[name=title]").val(ad_data.title);
        $("#ad_modal textarea[name=description_1]").val(ad_data.description_1);

        add_form['creative_url'] = '';
        add_form['creative_width'] = '';
        add_form['creative_height'] = '';

        var padding = 0;
        if(ad_data.creative_height < 110){
            padding = (110-ad_data.creative_height)/2 + "px";
        }

        $("#uploaded_image_prew").html('<figure class="height220" style="padding-top:' + padding + '; margin-bottom:15px" ><video width="450" height="320" controls class="theme-imagead-subrow-bottom"><source src="' + ad_data.video_url +'" type="video/mp4">Your browser does not support the video tag.</video></figure>');

        $('#create_new_add').val('Edit Ad');
        uploader.splice();

        if ($(this).attr('type') === 'url' && !learnRegExp(input_value)){
            valid_ad = false;
            $(this).addClass('error');

        }
        $('#ad_modal').modal('show');
        return false;
    });


    if (show_ad_id){
        $( "a[ad_id='" + show_ad_id + "']" ).click();
    }

});

function learnRegExp(s) {
    var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    return regexp.test(s);
}
