var add_form = {creative_type : 'TEXTAD'};

$(document).ready(function(){
    

    
    
    $('.theme-form-control').on('keypress', function(){

        var change_type = ['title', 'description', 'display_url'],
            type = $(this).data('type');

        if ($.inArray(type, change_type) >= 0){

            var new_text = (type === 'description') ? $('#desc_1').val() + ' ' +  $('#desc_2').val() : $(this).val();
            $('#examplte_show_div [data-type="' + type + '"]').text(new_text);
        }

        var text_span = $(this).parent().find('.charecter_count');
        text_span.text(text_span.attr('maxlength')*1 - $(this).val().length);

    });

    $('.theme-form-control').on('change', function(){

        var change_type = ['title', 'description', 'display_url'],
            type = $(this).data('type');

        if ($.inArray(type, change_type) >= 0){

            var new_text = (type === 'description') ? $('#desc_1').val() + ' ' +  $('#desc_2').val() : $(this).val();
            $('#examplte_show_div [data-type="' + type + '"]').text(new_text);
        }

        var text_span = $(this).parent().find('.charecter_count');
        text_span.text(text_span.attr('maxlength')*1 - $(this).val().length);

    });
    
    $('#btn_add_ad').on('click', function(){
        uploader.splice();
        
        $('#ad_modal input').not('input[name=campaign_id]').val('');
        $('#create_new_add').val('Create New Ad');
        $('#ad_modal .modal-title').text('Create your text Ad');
        $('#create_new_add').text('Create New Ad');
        $('#ad_modal textarea').val('');
    });
    
    $('#create_new_add').on('click', function(){
        
        var valid_ad = true;
        var button_name = $(this).val();
        $(this).closest('.theme-create-ad-form-wrap').find('.theme-geoform-control').each(function(key, value){
                    
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
                }
            }
            add_form[$(this).attr('name')] = input_value;
            
        });
        
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

            uploader.splice();
        }
        else {
            return false;
        }

    });
    
    $("#text_ad_upload").plupload({
        // General settings
        runtimes : 'html5,html4',
        url : '/v2/campaign/uploadFile',
        max_file_count: 1,

        chunk_size: '1mb',

        // Resize images on clientside if we can
        resize : {
            width : 125,
            height : 125,
            quality : 90,
            crop: true // crop to exact dimensions
        },

        filters : {
            // Maximum file size
            max_file_size : '1000mb',
            // Specify what files to browse for
            mime_types: [
                {title : "Image files", extensions : "jpeg,jpg,png"}
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

    var uploader = $("#text_ad_upload").plupload('getUploader');

    uploader.bind('FileUploaded', function (upldr, file, object) {

        var response;
        try {
            response = eval(object.response);
        }
        catch (err) {
            response = eval('(' + object.response + ')');
        }

        if(response.status){
            add_form['creative_url'] = response.file_dir;
        }
        else {
            console.log(response);
        }
    });
    
    $('.btn_edit_modal').on('click', function(){
        
        var ad_data = $(this).data('ad');
        
        $('#ad_modal .modal-title').text('Edit your text Ad');
        $("#ad_modal input[name=title]").val(ad_data.title);
        $("#ad_modal input[name=keywords]").val(ad_data.keywords);
        $("#ad_modal textarea[name=description_1]").val(ad_data.description_1);
        $("#ad_modal textarea[name=description_2]").val(ad_data.description_2);
        $("#ad_modal input[name=display_url]").val(ad_data.display_url);
        $("#ad_modal input[name=destination_url]").val(ad_data.redirect_url);
        $("#ad_modal input[name=ad_id]").val(ad_data.id);
        $("#ad_modal input[name=ad_link_id]").val(ad_data.ad_link_id);

        $('#ad_modal .modal-title').text('Edit your text Ad');
        $('#create_new_add').val('Edit Ad');
        uploader.splice();
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