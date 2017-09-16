$(document).ready(function(){

    now = new Date();
    future_date = new Date();
    future_date.setMonth(future_date.getMonth()+1);

    $("#remarketing_io").chosen({});

    /*ADD OPTIONS TO LAST FIELDSET*/
    $(".btn_last_next").on("click", function(){
        //$("#campagain_monitor span").text($('#example-advanced-form-p-0 .theme-tabbed-form-group input[type="radio"]:checked + label').html());
        //$("#campaign_info_io span").text($(".campaign_info .theme-geoform-group input[name=io]").val());
        $("#campaign_info_name span").text($(".campaign_info .theme-geoform-group input[name=name]").val());
        //$("#campaign_info_vertical span").text($(".campaign_info .theme-geoform-group select[name=vertical]").val());
        //$("#campaign_info_domain_name span").text($(".campaign_info .theme-geoform-group select[name=domain]").val());
        $("#campaign_info_start_date span").text(now.toISOString().slice(0,10));
        //if($(".campaign_info .theme-geoform-group input[name=budget]").val()){
        //    $("#campaign_info_daily_budget span").text($(".campaign_info .theme-geoform-group input[name=budget]").val());
        //    $("#campaign_info_daily_budget").show();
        //} else {
        //    $("#campaign_info_daily_budget").hide();
        //}


            $("#campaign_info_end_p").show();
            //if($(".campaign_info .theme-geoform-group #end_date_datepicker").val()){
                $("#campaign_info_end_date span").text(future_date.toISOString().slice(0,10));
                $("#campaign_info_end_date").show();
            //} else {
            //    $("#campaign_info_end_date").hide();
            //}

            if($(".campaign_info .theme-geoform-group #impressions_count_span").text()){
                $("#campaign_info_max_impressions span").text($(".campaign_info .theme-geoform-group #impressions_count_span").text());
                $("#campaign_info_max_impressions").show();
            } else {
                $("#campaign_info_max_impressions").hide();
            }

            if($(".campaign_info .theme-geoform-group #clicks_count_span").text()){
                $("#campaign_info_max_clicks span").text($(".campaign_info .theme-geoform-group #clicks_count_span").text());
                $("#campaign_info_max_clicks").show();
            } else {
                $("#campaign_info_max_clicks").hide();
            }

            $("#digital_rooftop").html('<p class="child_p">Postal-codes: ' + $('.campaign_info input[name=zip]').val() + '</p>');
            //$("#digital_rooftop").append('<p class="child_p">Radius: ' +$('.digitel_rooftop select[name=radius] option:selected').text()+ '</p>');


        var type = $('#example-advanced-form-p-0 .theme-tabbed-form-group input[type="radio"]:checked').val();
        $('#summary_campagain_type').text('Summary of '+$('#example-advanced-form-p-0 .theme-tabbed-form-group input[type="radio"]:checked + label').html()+' Campaign');
        $('#ads_finish_prev').html($('#professional_ad_div').html());

    });

    /*NEXT PREV BUTTONS IN WIZARD CONTENT*/
    $(".btn_continue").on("click", function(){
        $('.actions li a[href="#next"]').trigger( "click" );
    });
    $(".btn_previous_step").on("click", function(){
        $('.actions li a[href="#previous"]').trigger( "click" );
    });

    $(document).on( 'keypress', '[name="agent_phone"]', function(key){
        console.log(key.charCode);

        if(key.charCode==95 || key.charCode==46 || key.charCode==40 || key.charCode==41 || (key.charCode>=48 && key.charCode<=57) || key.charCode==45 || key.charCode==32){

            return true;
        }
        return false;
    });


    $("input[name='campaign_tier']").on( 'change', function(){

        var tier = $(this).val();
        $('#impressions_count_span').text(user['impressions_count_'+tier]);
        $('#clicks_count_span').text(user['clicks_count_'+tier]);

    });

    $(document).on( 'keypress', 'input[name="zip"]', function(key){
        if((key.charCode>=48 && key.charCode<=57) || (key.charCode==32 && $(this).val())){

            return true;
        }
        return false;
    });

    $(document).on('click','#finish_button',function(){
        $('[role="menu"] li:last').children().trigger('click');
    });

    var url_matching = function (url) {
        // check if url exist
        var existing_url = $('#ads_container').find('p.theme-ad-url-line:first').text();

        if(existing_url) {
            var existing_domain = extract_domain(existing_url);
            var domain = extract_domain(url);

            if(domain==existing_domain) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    var extract_domain = function (url) {
        var domain;
        //find & remove protocol (http, ftp, etc.) and get domain
        if (url.indexOf("://") > -1) {
            domain = url.split('/')[2];
        }
        else {
            domain = url.split('/')[0];
        }

        //find & remove port number
        domain = domain.split(':')[0];

        return domain;
    }

    $(document).on('submit','#example-advanced-form', function(){
        return false;
    });

    $("#profile_ad_upload").plupload({
        // General settings
        runtimes : 'html5,html4',
        url : '/v2/campaign/uploadFile',
        max_file_count: 1,

        chunk_size: '1mb',

        // Resize images on clientside if we can
        resize : {
            width : 90,
            height : 90,
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
        },

        init: {

            BeforeUpload: function (up, file) {
                up.settings.multipart_params = {type:'DISPLAY_PROFILE'};
            }
        }
    });

    var uploader = $("#profile_ad_upload").plupload('getUploader');

    uploader.bind('FileUploaded', function (upldr, file, object) {

        var response;
        try {
            response = eval(object.response);
        }
        catch (err) {
            response = eval('(' + object.response + ')');
        }

        if(response.status){
            
            $('.profile_pic').attr('src', response.file_dir);

            $("input[name='profile_image']").val(response.file_dir);
        }
        else {
            console.log(response);
        }
    });

    $("#background_ad_upload").plupload({
        // General settings
        runtimes : 'html5,html4',
        url : '/v2/campaign/uploadFile',
        max_file_count: 1,

        chunk_size: '1mb',

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
        },

        init: {

            BeforeUpload: function (up, file) {
                up.settings.multipart_params = {type:'DISPLAY'};
            }
        }
    });

    var uploader_bg = $("#background_ad_upload").plupload('getUploader');

    uploader_bg.bind('FileUploaded', function (upldr, file, object) {

        var response;
        try {
            response = eval(object.response);
        }
        catch (err) {
            response = eval('(' + object.response + ')');
        }

        if(response.status){

            $('#div_'+response.creative_width+' .dinamic_ad').css('background-image', "url("+response.file_dir+")");
            var selector_name = 'bg_'+ response.creative_width +'_'+response.creative_height;
            $("input[name="+selector_name+"]").val(response.file_dir);
        }
        else {
            console.log(response);
        }
    });

    $('.theme-form-control').on('keypress', function(){
        
        var change_type = ['agent_phone', 'agent_name', 'text_color', 'background_color', 'description'],
            type = $(this).data('type');

        if ($.inArray(type, change_type) >= 0){

            if (type === 'background_color') {
                $('.dinamic_ad').css("background-color", "#"+$(this).val());
            } else if (type === 'text_color') {
                $('.dinamic_ad').css("color", "#"+$(this).val());
            } else {
                $('.dinamic_ad [data-type="' + type + '"]').text($(this).val());
            }

        }
        var text_span = $(this).parent().find('.charecter_count');
        text_span.text(text_span.attr('maxlength')*1 - $(this).val().length);

    });

    $('.theme-form-control').on('change', function(){
        //console.log($(this).val());
        var change_type = ['agent_phone', 'font', 'agent_name', 'description', 'destination_url', 'text_color', 'background_color', 'agency_affiliation', 'agent_affiliation'],
            type = $(this).data('type');

        if ($.inArray(type, change_type) >= 0){
            
            if (type === 'background_color') {

                $('.dinamic_ad').css("background-color", "#"+$(this).val());
            } else if (type === 'text_color') {

                $('.dinamic_ad').css("color", "#"+$(this).val());
            } else if (type === 'font') {

                $('.dinamic_ad').css("font-family", $(this).val());
            } else if (type === 'font_size') {

                $('.dinamic_ad').css("font-size", $(this).val()+'px');
            } else if(type == 'agency_affiliation') {

                $('.dinamic_ad [data-type="' + type + '"] ').attr('src', 'v2/images/affiliation/'+$(this).val());
            } else if(type == 'agent_affiliation') {

                if($(this).is(":checked")) {
                    $('.dinamic_ad ul').append('<li class="affiliation_icon"><img src="v2/images/affiliation/'+$(this).val()+'" data-type="agent_affiliation" data-agent="'+$(this).data('agent')+'"></li>');
                } else {
                    $('.dinamic_ad [data-agent="' + $(this).data('agent') + '"] ').parent().remove();
                }
            } else if(type == 'destination_url' ) {

                $('.dinamic_ad [data-type="' + type + '"]').text(extract_domain($(this).val()));

            } else {

                $('.dinamic_ad [data-type="' + type + '"]').text($(this).val());
            }

            var text_span = $(this).parent().find('.charecter_count');
            text_span.text(text_span.attr('maxlength')*1 - $(this).val().length);
        }
    });

    $('.font_size_select').on('change', function(){

        var change_type = ['agent_phone', 'agent_name', 'description', 'destination_url'],
            type = $(this).data('type');

        if ($.inArray(type, change_type) >= 0){

            //if (type === 'background_color') {
            //
            //    $('.dinamic_ad').css("background-color", "#"+$(this).val());
            //} else if (type === 'text_color') {
            //
            //    $('.dinamic_ad').css("color", "#"+$(this).val());
            //} else if (type === 'font') {
            //
            //    $('.dinamic_ad').css("font-family", $(this).val());
            //} else if (type === 'font_size') {
            //
            //    $('.dinamic_ad').css("font-size", $(this).val()+'px');
            //} else if(type == 'agency_affiliation') {
            //
            //    $('.dinamic_ad [data-type="' + type + '"] ').attr('src', 'v2/images/affiliation/'+$(this).val());
            //} else if(type == 'agent_affiliation') {
            //
            //    if($(this).is(":checked")) {
            //        $('.dinamic_ad ul').append('<li class="affiliation_icon"><img src="v2/images/affiliation/'+$(this).val()+'" data-type="agent_affiliation" data-agent="'+$(this).data('agent')+'"></li>');
            //    } else {
            //        $('.dinamic_ad [data-agent="' + $(this).data('agent') + '"] ').parent().remove();
            //    }
            //} else if(type == 'destination_url' ) {
            //
            //    $('.dinamic_ad [data-type="' + type + '"]').text(extract_domain($(this).val()));

            //} else {

                $('.dinamic_ad [data-type="' + type + '"]').css("font-size", $(this).val()+'px');
            //}

            var text_span = $(this).parent().find('.charecter_count');
            text_span.text(text_span.attr('maxlength')*1 - $(this).val().length);
        }
    });
    $('.display_none').css('opacity',1);
});

function isUpperCase(str) {
    return str === str.toUpperCase();
}

function learnRegExp(s) {
    var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    return regexp.test(s);
}

var form = $("#example-advanced-form").show();

form.steps({
    headerTag: "h3",
    bodyTag: "fieldset",
    transitionEffect: "slideLeft",
    onStepChanging: function (event, currentIndex, newIndex)
    {   console.log(currentIndex,newIndex);

        // Allways allow previous action even if the current form is not valid!
        var type = $('#example-advanced-form-p-0 .theme-tabbed-form-group input[type="radio"]:checked').val();
        var campaignSubType = $('#campaign_type_placement .theme-tabbed-form-group input:checked').val();

        // Needed in some cases if the user went back (clean up)
        if (currentIndex < newIndex)
        {
            // To remove error styles
            form.find(".body:eq(" + newIndex + ") label.error").remove();
            form.find(".body:eq(" + newIndex + ") .error").removeClass("error");
        }
        else {
            return true;
        }


        if(newIndex == 2){


        }

        form.validate().settings.ignore = ":disabled,:hidden";
        return form.valid();
    },
    onStepChanged: function (event, currentIndex, priorIndex)
    {

        if (currentIndex < priorIndex){

            for (var i = 5; i > currentIndex; i--) {
                $('#example-advanced-form-t-' + i).parent().removeClass('done').addClass('disabled');
            }
            return true;
        }

    },
    onFinishing: function (event, currentIndex)
    {
        
        //hard coded for test;
        return true;
        form.validate().settings.ignore = ":disabled";
        return form.valid();
    },
    onFinished: function (event, currentIndex)
    {   //alert("Submitted!");

        var data = $("#example-advanced-form").serialize();
        show_loader($('#finish_button'));
        $.post('/v2/campaign/create_image', data, function(result){
            hide_loader();
            var data = JSON.parse(result);
            if(data.status == 'SUCCESS') {
                //alert(data.message);
                //window.location.href = "/v2/campaign/campaign_list/0/SCHEDULED";
                $('#modal_img1').attr('src', data.data[0]);
                $('#modal_img2').attr('src', data.data[1]);
                $('#modal_img3').attr('src', data.data[2]);
                $('#modal_img4').attr('src', data.data[3]);
                $('#image_show_modal').modal('show');
            } else {
                alert(data.message);
            }
        });
    }
}).validate({
    messages: {
        max_budget : {
            min: function(){
                if(true) {
                    $('#min_budget_div').show();
                    return "Enter greater than Min. BUDGET";
                } else {
                    return "";
                }
            }
        }

    },
    errorPlacement: function errorPlacement(error, element) { element.before(error); },
    rules: {
        
        'name': {
            required: true,
            maxlength : 32
        },
        //'agent_phone': {
        //    required: true,
        //    phoneUS: true
        //},

        'agent_name': {
            required: true,
            maxlength : 20
        },
        'agent_phone': {
            required: true,
            maxlength : 20
        },
        'description': {
            required: true,
            maxlength : 25
        },
        'agent_affiliation[]': {
            required: true
        },
        'agency_affiliation': {
            required: true
        },
        'destination_url': {
            required: true,
            url: true
        },

        //'campaign_start_datetime': {
        //    required: true
        //},
        'budget': {
            required: function(){
                return $('#example-advanced-form-p-0 .theme-tabbed-form-group input[type="radio"]:checked').val() !='EMAIL' ? true : false;
            },
            number : true,
            maxlength : 9
        },

        /*
        'percentage_opens' : {
            required: function() {
                return $('#example-advanced-form-p-0 .theme-tabbed-form-group input[type="radio"]:checked').val() =='EMAIL' ? true : false;
            }
        },
        'percentage_clicks' : {
            required: function() {
                return $('#example-advanced-form-p-0 .theme-tabbed-form-group input[type="radio"]:checked').val() =='EMAIL' ? true : false;
            }
        },
        */
        'max_impressions': {

            //required: function() {
            //    if(user.is_billing_type=='PERCENTAGE') {
            //        if ($(".enable-campaign-criteria input:checkbox").is(":checked") && $("#max_budget").val() == "" && $("#max_clicks").val() == "" && $("#end_date_datepicker").val() == "") {
            //            return true;
            //        } else {
            //            return false;
            //        }
            //    }
            //    //else {
            //    //    if ($("#max_clicks").val() == "") {
            //    //        return true;
            //    //    } else {
            //    //        return false;
            //    //    }
            //    //}
            //},

            require_from_group: function() {
                if(user.is_billing_type=='FLAT') {
                    return [1, ".flat_fields"];
                } else if($(".enable-campaign-criteria input:checkbox").is(":checked")) {
                    return [1, ".fillone"];
                } else {
                    return [0, ".fillone"];
                }
            },

            number : true,
            maxlength : 9
        },
        'max_clicks': {
            //required: function() {
            //    if(user.is_billing_type=='PERCENTAGE') {
            //        return false;
            //    } else {
            //        if ($("#max_impressions").val() == "") {
            //            return true;
            //        } else {
            //            return false;
            //        }
            //    }
            //},

            require_from_group: function() {
                if(user.is_billing_type=='FLAT') {
                    return [1, ".flat_fields"];
                } else if($(".enable-campaign-criteria input:checkbox").is(":checked")) {
                    return [1, ".fillone"];
                } else {
                    return [0, ".fillone"];
                }
            },
            number : true,
            maxlength : 9
        },
        'max_budget': {
            //require_from_group: function() {
            //    if($(".enable-campaign-criteria input:checkbox").is(":checked") && ($("#max_impressions").val()=="" && $('#example-advanced-form-p-0 .theme-tabbed-form-group input[type="radio"]:checked').val() !='EMAIL')) {
            //        return [1, ".fillone"];
            //    } else {
            //        return [0, ".fillone"];
            //    }
            //},
            //required: function() {
            //    if($(".enable-campaign-criteria input:checkbox").is(":checked") &&  $("#max_clicks").val()=="" && $("#end_date_datepicker").val()==""){
            //        return true;
            //    }
            //    else {
            //        return false;
            //    }
            //},
            require_from_group: function() {
                if(user.is_billing_type=='PERCENTAGE' && $(".enable-campaign-criteria input:checkbox").is(":checked")) {
                    return [1, ".fillone"];
                } else {
                    return [0, ".fillone"];
                }
            },
            number : true,
            maxlength : 9,
            min : function() {
                if( parseFloat(user.min_budget) < parseFloat($('input[name="budget"]').val()) ) {
                   return parseFloat($('input[name="budget"]').val());
                } else {
                    return parseFloat(user.min_budget);
                }
            }
        },
        //'campaign_end_datetime': {
        //    require_from_group: function() {
        //        if(user.is_billing_type=='PERCENTAGE' && $(".enable-campaign-criteria input:checkbox").is(":checked")) {
        //            return [1, ".fillone"];
        //        } else {
        //            return [0, ".fillone"];
        //        }
        //    },
        //    required: function() {
        //        if(user.is_billing_type=='FLAT') {
        //            return true;
        //        } else {
        //            return false;
        //        }
        //    }
        //},
        //'country': {
        //    required:  function() {
        //        if($("#country").is(":checked") || $("#state").is(":checked")){
        //            return true;
        //        } else {
        //            return false;
        //        }
        //    }
        //},
        //'state[]': {
        //    required:  function() {
        //        return $("#state").is(":checked");
        //    }
        //},
        'zip': {
            required:  true,
        },

        'remarketing_io[]': {
            required:  function() {
                return $("input#marketing-option.theme-geoform-control.theme-form-control").is(":checked");
            }
        }

    },

})