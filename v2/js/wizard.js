var ad_image;
$(document).ready(function(){

    $( "#so_number" ).autocomplete({
        source: availableSo
    });

    var heatmapInstance = null;

    // this is for rules in jquery, assign campaign to user must be available and required only for admins
    user.is_admin = parseInt(user.is_admin);

    $("#remarketing_io").chosen({width: "100%"});
    $("#io_based_retargeting_ios").chosen({width: "100%"});

    $(".hour_slider").ionRangeSlider({

        type: "double",
        min: '12:00 AM',
        max: '11:59 PM',
        values: [
            '12:00 AM', '01:00 AM', '02:00 AM','03:00 AM', '04:00 AM', '05:00 AM', '06:00 AM', '07:00 AM','08:00 AM', '09:00 AM', '10:00 AM', '11:00 AM',
            '12:00 PM', '01:00 PM', '02:00 PM','03:00 PM', '04:00 PM', '05:00 PM', '06:00 PM', '07:00 PM','08:00 PM', '09:00 PM', '10:00 PM', '11:00 PM', '11:59 PM'
        ],
        drag_interval: true,
        grid:true,
        grid_snap: true,
        disable: true
    });

    $('.morning').on('click', function(){
        var parent = $(this).closest('tr');
        var slider = parent.find('.hour_slider');
        parent.find('.btn-success').removeClass('btn-success').addClass('btn-default');
        $(this).removeClass('btn-default').addClass('btn-success');
        slider.data("ionRangeSlider").update({
            from: 0,
            to: 12,
            disable: true
        });
    });
    $('.mid_day').on('click', function(){
        var parent = $(this).closest('tr');
        var slider = parent.find('.hour_slider');
        parent.find('.btn-success').removeClass('btn-success').addClass('btn-default');
        $(this).removeClass('btn-default').addClass('btn-success');
        slider.data("ionRangeSlider").update({
            from: 9,
            to: 17,
            disable: true
        });
    });
    $('.evening').on('click', function(){
        var parent = $(this).closest('tr');
        var slider = parent.find('.hour_slider');
        parent.find('.btn-success').removeClass('btn-success').addClass('btn-default');
        $(this).removeClass('btn-default').addClass('btn-success');
        slider.data("ionRangeSlider").update({
            from: 17,
            to: 24,
            disable: true
        });
    });
    $('.custom').on('click', function(){
        var parent = $(this).closest('tr');
        var slider = parent.find('.hour_slider');
        parent.find('.btn-success').removeClass('btn-success').addClass('btn-default');
        $(this).removeClass('btn-default').addClass('btn-success');
        slider.data("ionRangeSlider").update({
            disable: false,
        });
    });
    $('.clear').on('click', function(){
        $('.all_day').trigger('click');
    });

    $('.all_day').on('click', function(){
        var parent = $(this).closest('tr');
        var slider = parent.find('.hour_slider');
        parent.find('.btn-success').removeClass('btn-success').addClass('btn-default');
        $(this).removeClass('btn-default').addClass('btn-success');
        slider.data("ionRangeSlider").update({
            from: 0,
            to: 24,
            disable: true
        });
    });

    uncheckCategory = function(id, selector) {
        var ref = $(selector).jstree(true), sel = ref.is_selected(id);
        if(!sel) { return false; }
        ref.uncheck_node(id);
    };


    addSearchableItem = function (item, type) {
        var val, v = [];
        var new_item = "<div data-type='"+type+"' class='right_panel_item' data-value='"+JSON.stringify($(item).data('value'))+"'><span>"+$(item).text()+"</span><i onclick='removeSearchableItem(this, \""+type+"\")' class='glyphicon glyphicon-remove'></i></div>";
        $(item).remove();
        $('#results').append(new_item);
        // loop and push values to array then add that array to hidden input value

        $('#results div[data-type='+type+']').each(function(){
            val = $(this).data('value');
            if(val){
                v.push(val);
            }
        });

        $('#'+type+'_input').val(JSON.stringify(v));
    }

    removeSearchableItem = function (item, type) {
        var val, v = [];
        $(item).parent().remove();
        // loop and push values to array then add that array to hidden input value

        $('#results div[data-type='+type+']').each(function(){
            val = $(this).data('value');
            if(val){
                v.push(JSON.stringify(val));
            }
        });
        $('#'+type+'_input').val(JSON.stringify(v));
    };
    var timeout = false;
    var to = false;
    var search_types = {
        'schools':'adeducationschool',
        'jobs':'adworkposition',
        'majors':'adeducationmajor',
        'works':'adworkemployer',
    };

    $('.categories_parent_block').on('keyup', '#demographics_search, #behaviors_search, #interests_search, #in_market_search, #affinity_search', function() {

        var v = $(this).val();
        var type = $(this).data('type');

        if(to) {
            clearTimeout(to);
        };
        to = setTimeout(function () {
            $('#'+type+'_select').jstree(true).search(v);
        }, 250);

    });

    $('#facebook_audiences').on('keyup', '#schools_search, #majors_search, #jobs_search, #works_search', function() {

        if(timeout) {
            clearTimeout(timeout);
        };
        var v = $(this).val();
        var type = $(this).data('type');
        if(v.length <= 1) {
            return false;
        }

        timeout = setTimeout(function () {
            $("#"+type+"_select").html('LOADING...');
            $.ajax({
                url: "/v2/campaign/get_demographics_by_type",
                type: "POST",
                dataType: "json",
                data: {value:v, type:search_types[type]},
                success: function( data ) {
                    var html = '';
                    $.map( data.data, function( item ) {
                        html += "<div class='searchable_item' onclick='addSearchableItem(this,\""+type+"\")' data-value='"+JSON.stringify(item) +"'>" + item.name + "</div>";
                    });
                    if(!html){
                        html = 'No Results Found'
                    }
                    $("#"+type+"_select").html(html);

                }
            });
        }, 500);
    });

    $('#affinity_select').jstree({
        "core" : {
            'expand_selected_onload':true,
            "themes" : {
                "variant" : "large",
                "stripes" : false,
                "icons" : false,
            }
        },
        "plugins" : [ "wholerow", "checkbox", "search", "contextmenu", "types"]
    });
    $('#affinity_select').on('changed.jstree', function (e, data) {
        var i, j, val, html="", r = [], v = [];
        data.instance.open_all(data.node.id);
        for(i = 0, j = data.selected.length; i < j; i++) {
            r.push(data.instance.get_node(data.selected[i]).text);
            html += '<div data-type="affinity" class="right_panel_item"><span>'+data.instance.get_node(data.selected[i]).text+'</span><i onclick="uncheckCategory( '+data.instance.get_node(data.selected[i]).id+',\'#affinity_select\')" class="glyphicon glyphicon-remove"></i></div>';
            val = $('#'+data.instance.get_node(data.selected[i]).id).val();
            if(val){
                v.push(val);
            }
        }

        $('#affinity_input').val(v);
        $('#results div').not('[data-type=in_market]').remove();
        $('#results').append(html);
    });

    $('#in_market_select').jstree({
        "core" : {
            "themes" : {
                "variant" : "large",
                "stripes" : false,
                "icons" : false,
            }
        },
        "plugins" : [ "wholerow", "checkbox","search"]
    });
    $('#in_market_select').on('changed.jstree', function (e, data) {
        var i, j, val, html="", v = [];
        data.instance.open_all(data.node.id);
        for(i = 0, j = data.selected.length; i < j; i++) {
            html += '<div data-type="in_market" class="right_panel_item"><span>'+data.instance.get_node(data.selected[i]).text+'</span><i onclick="uncheckCategory( '+data.instance.get_node(data.selected[i]).id+',\'#in_market_select\')" class="glyphicon glyphicon-remove"></i></div>';
            val = $('#'+data.instance.get_node(data.selected[i]).id).val();
            if(val){
                v.push(val);
            }
        }

        $('#in_market_input').val(v);
        $('#results div').not('[data-type=affinity]').remove();
        $('#results').append(html);
    });

    $('#interests_select').jstree({
        "core" : {
            "themes" : {
                "variant" : "large",
                "stripes" : false,
                "icons" : false,
            }
        },
        "plugins" : [ "wholerow", "checkbox","search"]
    });
    $('#interests_select').on('changed.jstree', function (e, data) {
        var i, j, val, html="", v = [];
        data.instance.open_all(data.node.id);
        for(i = 0, j = data.selected.length; i < j; i++) {

            html += '<div data-type="interest" class="right_panel_item"><span>'+data.instance.get_node(data.selected[i]).text+'</span><i onclick="uncheckCategory( '+data.instance.get_node(data.selected[i]).id+',\'#interests_select\')" class="glyphicon glyphicon-remove"></i></div>';
            val = data.instance.get_node(data.selected[i]).text;
            if(val){
                v.push(val.trim());
            }
        }

        $('#interests_input').val(v);
        $('#results div[data-type=interest]').remove();
        $('#results').append(html);
    });

    $('#yahoo_interests_select').jstree({
        "core" : {
            "themes" : {
                "variant" : "large",
                "stripes" : false,
                "icons" : false,
            }
        },
        "plugins" : [ "wholerow", "checkbox","search"]
    });
    $('#yahoo_interests_select').on('changed.jstree', function (e, data) {
        var i, j, val, html="", v = [];
        data.instance.open_all(data.node.id);
        for(i = 0, j = data.selected.length; i < j; i++) {

            html += '<div data-type="yahoo_interest" class="right_panel_item"><span>'+data.instance.get_node(data.selected[i]).text+'</span><i onclick="uncheckCategory( '+data.instance.get_node(data.selected[i]).id+',\'#yahoo_interests_select\')" class="glyphicon glyphicon-remove"></i></div>';
            val = $('#'+data.instance.get_node(data.selected[i]).id).val();
            if(val){
                v.push(val);
            }
        }

        $('#yahoo_interests_input').val(v);
        $('#results div[data-type=yahoo_interest]').remove();
        $('#results').append(html);
    });

    $('#behaviors_select').jstree({
        "core" : {
            "themes" : {
                "variant" : "large",
                "stripes" : false,
                "icons" : false,
            }
        },
        "plugins" : [ "wholerow", "checkbox","search"]
    });
    $('#behaviors_select').on('changed.jstree', function (e, data) {
        var i, j, val, html="", v = [];
        data.instance.open_all(data.node.id);
        for(i = 0, j = data.selected.length; i < j; i++) {

            html += '<div data-type="behavior" class="right_panel_item"><span>'+data.instance.get_node(data.selected[i]).text+'</span><i onclick="uncheckCategory( '+data.instance.get_node(data.selected[i]).id+',\'#behaviors_select\')" class="glyphicon glyphicon-remove"></i></div>';
            val = data.instance.get_node(data.selected[i]).text;
            if(val){
                v.push(val.trim());
            }
        }

        $('#behaviors_input').val(v);
        $('#results div[data-type=behavior]').remove();
        $('#results').append(html);
    });

    $('#demographics_select').jstree({
        "core" : {
            "themes" : {
                "variant" : "large",
                "stripes" : false,
                "icons" : false,
            }
        },
        "plugins" : [ "wholerow", "checkbox","search"]
    });
    $('#demographics_select').on('changed.jstree', function (e, data) {
        var i, j, val, html="", v = [];
        data.instance.open_all(data.node.id);
        for(i = 0, j = data.selected.length; i < j; i++) {
            html += '<div data-type="demographics" class="right_panel_item"><span>'+data.instance.get_node(data.selected[i]).text+'</span><i onclick="uncheckCategory( '+data.instance.get_node(data.selected[i]).id+',\'#demographics_select\')" class="glyphicon glyphicon-remove"></i></div>';
            val = $('#'+data.instance.get_node(data.selected[i]).id).data('value');
            if(val){
                v.push(val);
            }
        }

        $('#demographics_input').val(JSON.stringify(v));
        $('#results div[data-type=demographics]').remove();
        $('#results').append(html);
    });

    $('#google_category_type_select').change(function () {
        $('#google_audiences>div').not('.'+$(this).val()).hide();
        $('.'+$(this).val()).show();
    });

    $('#fb_category_type_select').change(function () {
        $('#facebook_audiences>div').not('.'+$(this).val()).hide();
       $('.'+$(this).val()).show();
    });

    $('#save_audience').on('click', function() {
        var campaignType = $('.theme-report-tabbed-form-wrap input:checked').val();
        if (campaignType == 'FACEBOOK') {
            if (!$("#demographics_input").val() && !$("#interests_input").val() && !$("#behaviors_input").val() && !$("#schools_input").val() && !$("#majors_input").val() && !$("#works_input").val() && !$("#jobs_input").val()) {

                alert('Please select at liast one option');
                return false;
            }
        } else {

            if (!$("#affinity_input").val() && !$("#in_market_input").val() && !$("#yahoo_interests_input").val()) {

                alert('Please select at liast one option');
                return false;
            }
        }
        $("#is_custom_audience").val(1);
        $('#audience_modal').modal('hide');
    });
    $('#cancel_audience').on('click', function() {
        $("#is_custom_audience").val(0);
        $('#audience_modal').modal('hide');
    });

    $('#save_lookalike_audience').on('click', function() {
        var audience_type = $('input[name="audience_type"]:checked').val();
        var lookalike_type = $('input[name="lookalike_type"]:checked').val();
        console.log(audience_type, lookalike_type)
        if (audience_type == 'new') {
            if (!$('input[name="lookalike_name"]').val()) {
                alert('Audience name is empty');
                return false;
            }
            if(lookalike_type == 'page') {
                if(!$('select[name="lookalike_page_id"]').val()) {
                    alert('Please select page');
                    return false;
                }
            } else {

            }
        } else {
            if(!$('select[name="lookalike_audiences[]"]').val()) {
                alert('Please select at list one audience');
                return false;
            }
        }
        $("#is_lookalike_audience").val(1);
        $('#lookalike_audience_modal').modal('hide');
    });


    $('#cancel_lookalike_audience').on('click', function() {
        $("#is_lookalike_audience").val(0);
        $('#lookalike_audience_modal').modal('hide');
    });

    $('#save_email_audience').on('click', function() {

        var audience_type = $('input[name="email_audience_type"]:checked').val();

        if (audience_type == 'new') {
            if (!$('input[name="custom_name"]').val()) {
                alert('Audience name is empty');
                return false;
            }

            if(!$('#email_audience_file').val()) {
                alert('Please upload email list');
                return false;
            }
        } else {
            if(!$('select[name="email_audiences[]"]').val()) {
                alert('Please select at list one audience');
                return false;
            }
        }


        $("#is_email_audience").val(1);
        $('#email_audience_modal').modal('hide');
    });


    $('#cancel_email_audience').on('click', function() {
        $("#is_email_audience").val(0);
        $('#email_audience_modal').modal('hide');
    });

    $('#save_fb_form').on('click', function() {
        if($('#form_type').val()=='new') {
            if (!$('#form_image').val()) {
                alert('Please upload image for FORM');
                return false;
            }
        } else {
            if (!$('#form_id').val()) {
                alert('Please select FORM');
                return false;
            }
        }
        $("#is_fb_form").val(1);
        alert('The Form saved successfully');
        $(this).hide();
        $('#facebook_form').hide();
    });

    // show viewers section after checkbox check
    $('#show_viewers').on('change', function() {
        if($(this).is(':checked')) {
            $('#manage_access_block_wizard').show();
        } else {
            $('#manage_access_block_wizard').hide();
        }
    });

   $('#show_guarantee').on('change', function() {
        if($(this).is(':checked')) {
            $('#guarantee_block_wizard').show();
            $('#thru_guarantee').val('Y');
        } else {
            $('#guarantee_block_wizard').hide();
            $('#thru_guarantee').val('N');
        }
    });

    $("#facebook").on('change', function() {
        if($(this).is(":checked")) {
            $.ajax({
                url: "/v2/profile/isLinkedToFB",
                type: "POST",
                dataType: "json",
                success: function(msg)  {
                    if (!msg.message) {
                        // msg.message variable is an id of linked facebook profile

                        $("#fb_alert").modal()

                    }
                }
            });
        }
    });

    /*ADD OPTIONS TO LAST FIELDSET*/
    $(".btn_last_next").on("click", function()
    {

        //$("#campagain_monitor span").text($('#example-advanced-form-p-0 .theme-tabbed-form-group input[type="radio"]:checked + label').html());
        $("#campaign_info_io span").text($(".campaign_info .theme-geoform-group input[name=io]").val());
        $("#campaign_info_name span").text($(".campaign_info .theme-geoform-group input[name=name]").val());
        $("#campaign_info_vertical span").text($(".campaign_info .theme-geoform-group select[name=vertical]").val());

        // pp-242
        var iab_categories = $('textarea[name="vertical"]').val(),
            campaign_vertical_review = $("#campaign_info_vertical span"),
            iab_categories_str = '';
        if ( iab_categories && iab_categories.length  ) {
            iab_categories = JSON.parse(iab_categories);
            iab_categories_str = _.pluck(iab_categories, 'vertical').join(', ');
            campaign_vertical_review.html(iab_categories_str);
        }
        // pp-242

        //$("#campaign_info_domain_name span").text($(".campaign_info .theme-geoform-group select[name=domain]").val());
        $("#campaign_info_start_date span").text($(".campaign_info .theme-geoform-group #start_date_datepicker").val());
        if($(".campaign_info .theme-geoform-group input[name=budget]").val()){
            $("#campaign_info_daily_budget span").text($(".campaign_info .theme-geoform-group input[name=budget]").val());
            $("#campaign_info_daily_budget").show();
        } else {
            $("#campaign_info_daily_budget").hide();
        }

        if($(".enable-campaign-criteria input:checkbox").is(":checked") || user.is_billing_type=='FLAT'){
            $("#campaign_info_end_p").show();
            if($(".campaign_info .theme-geoform-group #end_date_datepicker").val()){
                $("#campaign_info_end_date span").text($(".campaign_info .theme-geoform-group #end_date_datepicker").val());
                $("#campaign_info_end_date").show();
            } else {
                $("#campaign_info_end_date").hide();
            }

            if($(".campaign_info .theme-geoform-group #max_impressions").val()){
                $("#campaign_info_max_impressions span").text($(".campaign_info .theme-geoform-group #max_impressions").val());
                $("#campaign_info_max_impressions").show();
            } else {
                $("#campaign_info_max_impressions").hide();
            }

            if($(".campaign_info .theme-geoform-group #max_clicks").val()){
                $("#campaign_info_max_clicks span").text($(".campaign_info .theme-geoform-group #max_clicks").val());
                $("#campaign_info_max_clicks").show();
            } else {
                $("#campaign_info_max_clicks").hide();
            }

            if($(".campaign_info .theme-geoform-group #max_budget").val()){
                $("#campaign_info_max_budget span").text($(".campaign_info .theme-geoform-group #max_budget").val());
                $("#campaign_info_max_budget").show();
            } else {
                $("#campaign_info_max_budget").hide();
            }
        } else {
            $("#campaign_info_max_budget").hide();
            $("#campaign_info_max_clicks").hide();
            $("#campaign_info_max_impressions").hide();
            $("#campaign_info_end_date").hide();
            $("#campaign_info_end_p").hide();
        }

        if($("#country").is(":checked")){
            $("#digital_rooftop").html('<p class="child_p">Country: ' + $('.digitel_rooftop select[name=country] option:selected').text() + '</p>');
        }
        else if($("#postal-code").is(":checked")) {

            $("#digital_rooftop").html('<p class="child_p">Postal-codes: ' + $('.digitel_rooftop input[name=zip]').val() + '</p>');
            $("#digital_rooftop").append('<p class="child_p">Radius: ' +$('.digitel_rooftop select[name=radius] option:selected').text()+ '</p>');
        }
        else {
            //$("#digital_rooftop").html($('.digitel_rooftop select[name=state] option:selected').text());
            $("#digital_rooftop").html('<p class="child_p">States: ' +  $('.digitel_rooftop select[name="state[]"] option:selected').text() + '</p>');
        }

        if($('.digitel_rooftop select[name=gender] option:selected').val()) {
            $("#gender span").text($('.digitel_rooftop select[name=gender] option:selected').text());
            $("#gender").show();
        } else {
            $("#gender").hide();
        }
        if($('.digitel_rooftop select[name=income_level] option:selected').val()){
            $("#inc_level span").text($('.digitel_rooftop select[name=income_level] option:selected').text());
            $("#inc_level").show();
        } else {
            $("#inc_level").hide();
        }
        if($('.digitel_rooftop select[name=parent] option:selected').val()){
            $("#parent span").text($('.digitel_rooftop select[name=parent] option:selected').text());
            $("#parent").show();
        } else {
            $("#parent").hide();
        }

        $("#device span").text($('.digitel_rooftop select[name=device_type] option:selected').text());
        $("#carrier span").text($('.digitel_rooftop select[name=carrier] option:selected').text());
        $("#prefered span").text($('.digitel_rooftop select[name=preferred_mobile] option:selected').text());

        var type = $('#example-advanced-form-p-0 .theme-tabbed-form-group input[type="radio"]:checked').val();
        $('#summary_campagain_type').text('Summary of '+$('#example-advanced-form-p-0 .theme-tabbed-form-group input[type="radio"]:checked + label').html()+' Campaign');

        if (type == 'TEXTAD'){
            $('#ppc_review_div').addClass('hidden');
            $('#ads_finish_prev').html($('#text_ad_div').html());
        }
        else if (type !== "EMAIL"){
            $('#ads_finish_prev').html($('#ads_container').html());
            $('#ppc_review_div').addClass('hidden');
        }
        else {

            $('#total_records_show span').text($('#total_records').val());
            $('#opens_show span').text($('#opens').val());
            $('#clicks_show span').text($('#clicks').val());

            $('#ppc_review_div').removeClass('hidden');
            $('#campaign_info_daily_budget').hide();
        }

        $('#ads_finish_prev .hidden_inputs').remove();
        $('#ads_finish_prev .edit-theme-ad').remove();
        $('#ads_finish_prev .theme-list-remove-icon').remove();

    });

    var date = new Date;
    date.setDate(date.getDate() + 7);

    /*NEXT PREV BUTTONS IN WIZARD CONTENT*/
    $(".btn_continue").on("click", function(){
        $('.actions li a[href="#next"]').trigger( "click" );
    });

    $(".btn_previous_step").on("click", function(){
        $('.actions li a[href="#previous"]').trigger( "click" );
    });

    $('body').on('click', '.theme-list-remove-icon', function(){
        $(this).parent().remove();
    });

    /*REMOVE ADS FROM SIDEBAR*/
    $(document).on("click",".ads_remove", function(){
        $(this).parent(".theme-scrollable-ad-row").remove();
    });

    $(document).on( 'keypress', '[name="io"]', function(key){
        if((key.charCode>=65 && key.charCode<=90) || (key.charCode>=97 && key.charCode<=122) || (key.charCode>=48 && key.charCode<=57) || key.charCode==45 || key.charCode==32){
            if(key.charCode==32){
                key.preventDefault();
                $(this).val($(this).val()+"-");
            }
            return true;
        }
        return false;
    });

    $(document).on( 'keypress', '#max_impressions, #max_clicks, [name="destination"]', function(key){
        if( key.charCode>=48 && key.charCode<=57 ){
            return true;
        }
        return false;
    });

    $('#max_clicks').on( 'change', function(){
        var campaignType = $('.theme-report-tabbed-form-wrap input:checked').val();
        if(user.is_billing_type=='FLAT'){
           var tier = $("input[name='campaign_tier']:checked").val();
           if(!$(this).val()){
               $('#max_impressions').trigger('change');
               return false;
           }

           var max_budget = parseFloat(user['display_click_'+tier])*parseInt($(this).val());
           if(user.is_guarantee == 'Y'  && campaignType == "DISPLAY"){
                max_budget = max_budget + max_budget * parseInt(user.is_guarantee_upcharge) / 100;
           }
           max_budget = max_budget.toFixed(2);
           $("input[name='max_budget']").val(max_budget);
           $('#max_budget_span').html(max_budget);
           if(max_budget<parseFloat(user.min_budget)) {
               $('#min_budget_div').show();
           }
        }
    });

    $('#max_impressions').on( 'change', function(){
        var campaignType = $('.theme-report-tabbed-form-wrap input:checked').val();
        if(user.is_billing_type=='FLAT' && !$('#max_clicks').val()){
           var tier = $("input[name='campaign_tier']:checked").val();
           if(!$(this).val()){
               $('#max_clicks').trigger('change');
               return false;
           }

           var max_budget = parseFloat(user['display_imp_'+tier])*parseInt($(this).val())/1000;
            if(user.is_guarantee == 'Y' && campaignType == "DISPLAY"){
                max_budget = max_budget + max_budget * parseInt(user.is_guarantee_upcharge) / 100;
           }
           max_budget = max_budget.toFixed(2);
           $("input[name='max_budget']").val(max_budget);
           $('#max_budget_span').html(max_budget);
           if(max_budget<parseFloat(user.min_budget)) {
               $('#min_budget_div').show();
           }
        }
    });

    $("input[name='campaign_tier']").on( 'change', function(){
        if(user.is_billing_type=='FLAT'){
            var tier = $(this).val();
            if($('#max_clicks').val()){
               $('#max_clicks').trigger('change');
            } else if($('#max_impressions').val()){
               $('#max_impressions').trigger('change');
            }

        }
    });

    $('.theme-geoform-group').on( 'keypress', 'input[name="budget"], #max_budget', function(key){
        if($(this).val() && key.charCode == 46 && $(this).val().indexOf('.') == -1 ){ //console.log($('[name="budget"]').val());
            return true;
        }

        if( key.charCode>=48 && key.charCode<=57 && (($(this).val().length - $(this).val().indexOf('.')) < 3 || $(this).val().indexOf('.') == -1)){
            return true;
        }
        return false;
    });

    $(document).on('change', '#geo-device-type', function(){
        if($(this).val()=='mobile' || $(this).val()=='' ) {
            $("#geo-carrier").show();
        } else {
            $("#geo-carrier").hide();
        }
    });

    $(document).on( 'keypress', 'input[name="zip"]', function(key){
        if((key.charCode>=48 && key.charCode<=57) || (key.charCode==32 && $(this).val())){

            return true;
        }
        return false;
    });

    $(document).on('click', '#state', function(){
        $('select#geo-country').trigger('change');
    });

    $(document).on('change','select#geo-country',function(){

        if($("#state").is(":checked") && ($(this).val()=='US' || $(this).val()=='CA')){
            //show_loader();
            $.post('/v2/campaign/get_states_by_country', { country : $(this).val() }, function(result){
              //  hide_loader();
                var data = JSON.parse(result);
                if(data.status == 'SUCCESS') {
                    var html = '';
                    $.each(data.states, function(key, value){
                        html +='<option value="'+value.state+'">'+value.name+'</option>';
                    });
                    $('select#geo-state').html(html);
                } else {

                }
            });
        }
        $('select#geo-state').html('<option value="" disabled>You can select states only for USA and CANADA</option>');
        $.post('/v2/campaign/get_carriers_by_country', { country : $(this).val() }, function(result){
            var data = JSON.parse(result);
            if(data.status == 'SUCCESS') {
                var html = '<option value="">Any carrier</option>';
                $.each(data.carriers, function(key, value){
                    html +='<option value="'+value.carrier+'">'+value.carrier+'</option>';
                });
                $('select#geo-carrier').html(html);
            } else {
                //alert(data.message);
            }
        });

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

    $(document).on('click','.theme-ad-save',function(){

        var closest_div = $(this).closest('.theme-scrollable-ad-row');
        var campaignType = $('.theme-report-tabbed-form-wrap input:checked').val();
        var campaignSubType = $('#campaign_type_placement .theme-tabbed-form-group input:checked').val();

        if((campaignType == 'FACEBOOK' && campaignSubType != 'FB-PROMOTE-EVENT') || (campaignType == 'AIRPUSH' && campaignSubType == 'PUSH_CLICK_TO_CALL') || (campaignType == 'YAHOO')) {

            var adTitle = $(this).closest('.theme-imagead-subrow-bottom').children('.fb_ad_title');
            var adBody = $(this).closest('.theme-imagead-subrow-bottom').children('.fb_ad_body');
            var linkDescription = $(this).closest('.theme-imagead-subrow-bottom').children('.link_description');
            var adPage = $(this).closest('.theme-imagead-subrow-bottom').children('.page_id');
            var title_valid = 30;
            var desc_valid = 90;

            if(campaignSubType == 'DISPLAY_YAHOO') {
                var title_valid = 50;
                var desc_valid = 160;
            }

            if(campaignSubType == 'PUSH_CLICK_TO_CALL') {
                var title_valid = 25;
                var desc_valid = 40;
            }

            if(adTitle.val() == '' || adTitle.val().length > title_valid || adTitle.val().length < 3) {

                adTitle.addClass('error');
                alert('Enter correct title(Max '+title_valid+', Min 3 characters)');
                return false;

            }
            if(adBody.val() == '' || adBody.val() > desc_valid || adBody.val() < 3) {

                adBody.addClass('error');
                alert('Enter correct description(Max '+desc_valid+', Min 3 characters)');
                return false;
            }
            if((adBody.val() !== '') && is_upper_case(adBody.val())){
                alert("Your description can't be in all capital letters.");
                return false;
            }
            if(adPage.val() == '') {
                alert('Enter page id');
                return false;
            }

        }
        var tracking_url = '';
        if(campaignSubType == 'PUSH_CLICK_TO_CALL' || campaignSubType == 'DIALOG_CLICK_TO_CALL' || campaignSubType == 'RICH_MEDIA_INTERSTITIAL') {
            var dest_url = 'http://reporting.prodata.media';

        } else if((campaignSubType == 'FB-PAGE-LIKE') || (campaignSubType == 'FB-VIDEO-VIEWS') || (campaignSubType == 'FB-PROMOTE-EVENT') || (campaignSubType == 'FB-MOBILE-APP-INSTALLS') || (campaignSubType == 'APP_INSTALL_YAHOO') && campaignSubType != 'RICH_MEDIA_SURVEY'){
            var dest_url = '';
        } else if(campaignType=='DISPLAY' && user.is_tracking_url=='Y' && campaignSubType != 'THIRD-PARTY-AD-TRACK'){

            var dest_url = $(this).prev().val();
            var valid = /^(ftp|http|https):\/\/[^ "]+$/.test(dest_url);
            var matching = url_matching(dest_url);
            if(!valid || !matching){
                $(this).prev().addClass('error');
                if(!valid) {
                    alert('The destination URL is invalid.');
                } else {
                    alert('The destination URL must be all within the same domain name. D1');
                }

                return false;
            }

            tracking_url = $(this).prev().prev().prev().val();
            if(tracking_url) {
                var valid_tracking = /^(ftp|http|https):\/\/[^ "]+$/.test(tracking_url);
                //var matching_tracking = url_matching(tracking_url);
                if(!valid_tracking){
                    $(this).prev().prev().prev().addClass('error');
                    alert('The Tracking URL is invalid.');
                    return false;
                }
            }

        } else if(campaignSubType != 'RICH_MEDIA_SURVEY') {
            var dest_url = $(this).prev().val();
            var valid = /^(ftp|http|https):\/\/[^ "]+$/.test(dest_url);

            //var matching = url_matching(dest_url);

            //if(!valid || !matching){
            if(!valid){
                $(this).prev().addClass('error');
                if(!valid) {
                    alert('The destination URL is invalid.');
                } else {
                    alert('The destination URL must be all within the same domain name. D2');
                }

                return false;
            }
        }

        var ad = {
            creative_type:"DISPLAY",
            destination_url:dest_url,
            creative_width: closest_div.find('.ads_info').attr('creative_width'),
            creative_height: closest_div.find('.ads_info').attr('creative_height')
        };

        if(tracking_url) {
            ad.tracking_url = tracking_url;
            $(this).prev().prev().prev().remove();
            $(this).prev().prev().remove();
            $(this).prev().prev().before('<p>' + tracking_url + '</p>');
        } else if(campaignType=='DISPLAY' && user.is_tracking_url=='Y'){
            $(this).prev().prev().prev().remove();
            $(this).prev().prev().remove();
            $(this).prev().prev().before('<p></p>');
        }

        if(campaignSubType == 'PUSH_CLICK_TO_CALL' || campaignSubType == 'LANDING_PAGE') {
            ad.airpush_internal_image = $(this).closest('.theme-imagead-subrow-bottom').find('.airpush_image_select option:selected').html();
            $(this).closest('.theme-imagead-subrow-bottom').children('.airpush_image_select').attr('disabled', 'disabled');
        }
        else if(campaignSubType == 'FB-MOBILE-NEWS-FEED' || campaignSubType == 'FB-DESKTOP-NEWS-FEED' || campaignSubType == 'FB-CAROUSEL-AD' || campaignSubType == 'FB-PAGE-LIKE') {
            ad.fb_page_id = adPage.val();
            ad.creative_url = closest_div.children('figure').children('img').attr('src').slice(1);
            adPage.prev('.lbl_page_id').after('<p>' + adPage.val() + '</p>');
            adPage.remove();
        } else if(campaignSubType == 'FB-VIDEO-VIEWS') {
            ad.fb_page_id = adPage.val();
            adPage.prev('.lbl_page_id').after('<p>' + adPage.val() + '</p>');
            adPage.remove();
            ad.creative_url = closest_div.children('video').children('source').attr('src').slice(1);
        } else if(campaignSubType == 'FB-VIDEO-CLICKS' || campaignSubType == 'VIDEO_YAHOO') {
            ad.creative_url = closest_div.children('video').children('source').attr('src').slice(1);
        } else if(campaignSubType == 'FB-LOCAL-AWARENESS'  && closest_div.closest('.theme-scrollable-ad-row').children('video').length !=  0) {
            ad.creative_url = closest_div.children('video').children('source').attr('src').slice(1);
        } else if(campaignSubType == 'DIALOG_CLICK_TO_CALL' || campaignSubType == 'RICH_MEDIA_INTERSTITIAL' || campaignSubType == 'FB-PROMOTE-EVENT'){

        } else if(campaignSubType == 'RICH_MEDIA_SURVEY'){
             ad.creative_type = 'RICH_MEDIA_SURVEY';
             ad.creative_url  = closest_div.children('div').children('div').children('img').attr('src').slice(1);
             ad.rm_question = closest_div.children('div').children('div').children('h5').text();
             ad.rm_container = closest_div.html();
             //alert(ad.rm_question);
             var size = closest_div.children('div').children('div').children('.input_banner').size();
              ad.rm_answer = [];
             for(var i=0; i < size; i++){
                var child_labels = closest_div.children('div').children('div').children('.input_banner').children();
                //alert(child_labels[i].innerText);
                if(child_labels[i] != undefined){
                  ad.rm_answer[i] = child_labels[i].innerText;
                }

             }
        } else {
           ad.creative_url = closest_div.children('figure').children('img').attr('src').slice(1);
        }

        var keywordArray = new Array();
        $('.keywords_block').find('.theme-form-control').each(function(key, value){

            if($(this).attr('name') == 'keywords') {
                if($(this).val()) {
                    keywordArray.push($(this).val());
                }
            }
        });
        if(keywordArray.length == 0) {
            keywordArray.push('RON');
        }
        ad.keywords = keywordArray;

        if(campaignType == 'FACEBOOK') {

            if(campaignSubType == 'FB-PROMOTE-EVENT') {
                ad.creative_type = 'EVENT';
                var adPage = $(this).closest('.theme-imagead-subrow-bottom').children('.page_id');
                ad.fb_page_id = adPage.val();
                var event_url = $(this).closest('.theme-imagead-subrow-bottom').children('.event_url');
                event_url.after('<p>' + event_url.val() + '</p>');

                if(event_url.val() == '') {
                    alert('Enter event url');
                    return false;
                }
                ad.event_url = event_url.val();
                event_url.remove();
            } else if(campaignSubType == 'FB-MOBILE-APP-INSTALLS') {

                ad.creative_type = 'APP_INSTALL';
                ad.title = adTitle.val();
                ad.fb_description = adBody.val();
                var app_url = $(this).closest('.theme-imagead-subrow-bottom').find('.app_url');
                var app_id = $(this).closest('.theme-imagead-subrow-bottom').find('.app_id');
                var hidden_input = $(this).closest('.theme-imagead-subrow-bottom').find('.ads_info');
                ad.app_url = app_url.val();
                ad.app_id = app_id.val();
                ad.call_to_action = 'INSTALL_APP';

                $(this).closest('.theme-adbanner-form-group').prepend('<p>' + app_url.val() + '</p>');
                $(this).closest('.theme-adbanner-form-group').prepend('<p>' + app_id.val() + '</p>');

                app_id.remove();
                app_url.remove();

            } else if(campaignSubType == 'FB-DESKTOP-NEWS-FEED') {
                ad.creative_type = 'DISPLAY_FACEBOOK';
                ad.title = adTitle.val();
                ad.fb_description = adBody.val();
                ad.link_description = linkDescription.val();
            } else if(campaignSubType == 'FB-CAROUSEL-AD') {
                ad.creative_type = 'FB-CAROUSEL-AD';
                ad.title = adTitle.val();
                ad.fb_description = adBody.val();
            } else {
                ad.creative_type = 'DISPLAY_FACEBOOK';
                ad.title = adTitle.val();
                ad.fb_description = adBody.val();
            }

            if(campaignSubType == 'FB-VIDEO-VIEWS' || campaignSubType == 'FB-VIDEO-CLICKS') {
                ad.creative_type = 'VIDEO';
                ad.call_to_action = $(this).closest('.theme-imagead-subrow-bottom').find('.call_to_action option:selected').val();
                $(this).closest('.theme-imagead-subrow-bottom').children('.call_to_action').attr('disabled', 'disabled');
            }

            if(campaignSubType == 'FB-LOCAL-AWARENESS') {
                ad.creative_type = 'VIDEO';
                ad.call_to_action = 'GET_DIRECTIONS';

                if (closest_div.children('video').length ==  0) {
                    ad.creative_type = 'DISPLAY_FACEBOOK';
                }
            }

        }

        if(campaignType == 'AIRPUSH') {
            ad.creative_type = 'DISPLAY_AIRPUSH';
            if(campaignSubType == 'PUSH_CLICK_TO_CALL'){
                ad.title = adTitle.val();
                ad.description_1 = adBody.val();
                ad.destination = $(this).prev().val();
                $(this).prev().prev().before('<p>' + ad.destination + '</p>');
            }

            if(campaignSubType == 'DIALOG_CLICK_TO_CALL'){

                var adTitle = $(this).closest('.theme-imagead-subrow-bottom').children('.fb_ad_title');
                if(adTitle.val() == '' || adTitle.val().length > 25 || adTitle.val().length < 3) {

                    adTitle.addClass('error');
                    alert('Enter correct title(Max 25, Min 3 characters)');
                    return false;

                }

                ad.title = adTitle.val();
                ad.destination = $(this).prev().val();
                $(this).prev().prev().before('<p>' + ad.destination + '</p>');
                adTitle.prev('.lbl_fb_title').after('<p>' + adTitle.val() + '</p>');
                adTitle.remove();
            }

            if(campaignSubType == 'RICH_MEDIA_INTERSTITIAL') {
                ad.creative_type = 'RICH_MEDIA';
                var script = $(this).closest('.theme-imagead-subrow-bottom').children('.script');
                ad.script = script.val(); console.log(script.val());
                //script.prev('.lbl_script').after('<p>"' + script.val() + '"</p>');
                script.attr('disabled',true);

            }

        }

        if(campaignType == 'YAHOO') {
            if(campaignSubType == 'VIDEO_YAHOO') {
                ad.creative_type = 'VIDEO_YAHOO';
            } else if(campaignSubType == 'APP_INSTALL_YAHOO') {
                ad.creative_type = 'APP_INSTALL';
                var tumblr_post_url = $(this).closest('.theme-imagead-subrow-bottom').children('.tumblr_post_url');
                ad.tumblr_post_url = tumblr_post_url.val();
                tumblr_post_url.after('<p>' + tumblr_post_url.val() + '</p>');
                tumblr_post_url.remove();
            }else if(campaignSubType == 'YAHOO_CAROUSEL'){
                ad.creative_type = 'YAHOO_CAROUSEL';
            } else {
                ad.creative_type = 'DISPLAY_YAHOO';
            }
            console.log(ad);
            ad.title = adTitle.val();               
            ad.description_1 = adBody.val();
            ad.action_buttons = $(".action_buttons").val();
        }

        var ads = JSON.stringify(ad);
        if(campaignSubType != 'FB-PROMOTE-EVENT' && campaignSubType != 'FB-MOBILE-APP-INSTALLS' && campaignSubType != 'APP_INSTALL_YAHOO' && campaignSubType != 'RICH_MEDIA_SURVEY') {
            $(this).prev().prev().addClass('hidden_inputs').val(ads);
            $(this).prev().remove();
        } else {
            $(this).prev().addClass('hidden_inputs').val(ads);
        }
        $(this).remove();

        if((campaignType == 'FACEBOOK' && campaignSubType != 'FB-PROMOTE-EVENT') || (campaignType == 'AIRPUSH' && campaignSubType == 'PUSH_CLICK_TO_CALL') || (campaignType == 'YAHOO')) {

            adTitle.prev('.lbl_fb_title').after('<p>' + adTitle.val() + '</p>');
            adBody.prev('.lbl_fb_desc').after('<p>' + adBody.val() + '</p>');
            linkDescription.prev('.lbl_fb_link_description').after('<p>' + linkDescription.val() + '</p>');

            adTitle.remove();
            adBody.remove();
            linkDescription.remove();

        }

        if(!(campaignSubType == 'PUSH_CLICK_TO_CALL' || campaignSubType == 'DIALOG_CLICK_TO_CALL') && campaignSubType != 'RICH_MEDIA_SURVEY') {
            closest_div.append('<p class="theme-ad-url-line ">' +
                '<a class="editable_url" target="_blank" href="'+ dest_url +'">'+ dest_url +'</a></p>');

        } else if(campaignSubType == 'RICH_MEDIA_SURVEY'){
 
            $("#curtain").find("i.fa-spinner").show();
            closest_div.append('<p class="theme-ad-url-line" id="valid_true" style="color:#398439">Saved</p>');
        }

        //cleare keywords inputs
        $('.keywords_block').find('.theme-form-control').val('');
        $('.keywords_block').find('.charecter_count').text('80');
        $('.keywords_block').find('.words_count').text('0')

    });

    $(document).on('click', '.editable_url', function () {
        $(this).closest('.theme-scrollable-ad-row').find('.theme-adbanner-form-group').append(
            '<input type="text" name="dest_url[]" class="theme-form-control theme-imagead-url-field" placeholder="Destination URL - http://..." value="'+ $(this).text() +'"/>'
            + '<div id="ad-save-btn" class="theme-btn theme-submit-control theme-ad-save">save</div>'
        );
        $(this).parent().remove();
        return false;
    });

    $(document).on('submit','#example-advanced-form', function(){
        return false;
    });

    $(document).on('click', '.edit-theme-ad', function () {

        var edit_ad_div = $(this).closest('.theme-scrollable-ad-row');
        var ad_data = JSON.parse(edit_ad_div.find('input').val());

        $('.theme-scrollable-ad-row').removeAttr('edit');
        edit_ad_div.attr('edit', true);
        $('.theme-create-ad-form-wrap input[name="title"]').val(ad_data.title);
        $('.theme-create-ad-form-wrap input[name="display_url"]').val(ad_data.display_url);
        $('.theme-create-ad-form-wrap input[name="destination_url"]').val(ad_data.destination_url);
        $('.theme-create-ad-form-wrap textarea[name="description_1"]').val(ad_data.description_1);
        $('.theme-create-ad-form-wrap textarea[name="description_2"]').val(ad_data.description_2);
        $('.theme-create-ad-form-wrap textarea[name="keywords"]').val((ad_data.keywords !== 'RON') ? ad_data.keywords : '');
        $('.theme-create-ad-form-wrap #create_new_add').val('Update Text Ad');

        if ($('#cancel_edit_ad').length == 0){
            $('.theme-create-ad-form-wrap #create_new_add').after('<input type="button" value="Cancel" class="theme-create-add-btn theme-submit-control" id="cancel_edit_ad">');
        }

        $('.theme-create-ad-form-wrap .charecter_count').each(function(key, value){
            if ($(this).attr('maxlength') !== undefined){
                $(this).text($(this).attr('maxlength') * 1 - $(this).closest('.theme-form-group').find('.theme-form-control').val().length);
            }
        });

        ad_image = ad_data.creative_url;
        //uploader.addFile('file', ad_image);

        return false;
    });

    valide_io = true;

    $('#io').focusout(function(){
        var element = $(this);
        //show_loader();
        $.post('/v2/campaign/check_io', { io: $(this).val() } , function(result){
            //hide_loader();
            var data = JSON.parse(result);
            if(data.status == 'SUCCESS') {
                if(element.hasClass('error')){
                    element.removeClass('error');
                }
                valide_io = true;
                return true;
            } else {
                element.addClass('error');
                var error_lable = $('#io-error');
                if(error_lable.length){
                    error_lable.html('This IO# already exists; please use a different value.').css('display','inline-block');
                } else {
                    element.before("<label id='io-error' class='error' for='io' style='width:380px;'>This IO# already exists; please use a different value.</label>")
                }
                valide_io = false;
            }
        });
    });

    $(document).on('click', '#cancel_edit_ad', function() {

        $('.theme-create-ad-form-wrap #create_new_add').val('Create New Ad');
        var closest_div = $(this).closest('.theme-create-ad-form-wrap');
        closest_div.find('.theme-form-control').val('');
        $(this).remove();
        closest_div.find('.charecter_count').each(function(key, value){
            $(this).text($(this).attr('maxlength'));
        });

        ad_image = false;
    });

    $("#text_ad_upload").plupload({
        // General settings
        runtimes : 'html5,html4',
        url : '/v2/campaign/uploadFile/text_ad',
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

            $('#examplte_show_div img').attr('src', response.file_dir);

            ad_image = response.file_dir;
        }
        else {
            console.log(response);
        }
    });

    $("#csv_uploader").plupload({
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
                {title : "txt files", extensions : "txt,csv"}
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

    var csv_uploader = $("#csv_uploader").plupload('getUploader');

    csv_uploader.bind('FileUploaded', function (upldr, file, object) {
        //console.log(object);
        var response;
        try {
            response = eval(object.response);
        }
        catch (err) {
            response = eval('(' + object.response + ')');
        }

        console.log(response.file_dir);

        if(response.status){
            console.log(response.file_dir);
            $('#email_audience_file').val(response.file_dir);
        }
        else {
            alert(response.message);
        }
    });

    $("#form_image_uploader").plupload({
        // General settings
        runtimes : 'html5,html4',
        url : '/v2/campaign/uploadFile',
        max_file_count: 1,

        chunk_size: '1mb',

        // Resize images on clientside if we can
        resize : {
            quality : 90,
        },

        filters : {
            // Maximum file size
            max_file_size : '10mb',
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

    var form_image_uploader = $("#form_image_uploader").plupload('getUploader');

    form_image_uploader.bind('FilesAdded', function(up, files) {
        console.log(files);

        var reader = new FileReader();
        reader.readAsDataURL(files[0].getNative());
        reader.onload = (function(e) {
            var image = new Image();
            image.src = e.target.result;
            //console.log(image);
            image.onload = function() {
                if(this.width != 1200 && this.height != 628){

                    alert('Image should has 1200x628 dimensions');
                    form_image_uploader.removeFile(files[0]);

                }else{
                    return true;
                }
            }
        });
    });

    form_image_uploader.bind('FileUploaded', function (upldr, file, object) {
        //console.log(object);
        var response;
        try {
            response = eval(object.response);
        }
        catch (err) {
            response = eval('(' + object.response + ')');
        }

        console.log(response.file_dir);

        if(response.status){
            console.log(response.file_dir);
            $('#cover_img').attr('src',response.file_dir);
            $('#form_image').val(response.file_dir);
        }
        else {
            alert(response.message);
        }
    });

    $('#create_new_add').on('click', function(){

        var valid_ad = true;
        var add_form = {creative_type : 'TEXTAD'};
        var keywordArray = new Array();
        $(this).closest('.theme-create-ad-form-wrap').find('.theme-form-control').each(function(key, value){

            if($(this).attr('name') == 'keywords') {
                if($(this).val()) {
                    keywordArray.push($(this).val());
                }
            } else {
                var input_value = $(this).val();

                if (!input_value && $(this.name)) {

                    valid_ad = false;
                    $(this).addClass('error');
                }
                else {
                    if ($(this).attr('type') === 'url' && !check_url(input_value)) {
                        valid_ad = false;
                        $(this).addClass('error');
                    }
                }
                add_form[$(this).attr('name')] = input_value;
            }
        });
        if(keywordArray.length == 0) {
            keywordArray.push('RON');
        }
        add_form['keywords'] = keywordArray;

        if (valid_ad){

            if ($(this).val() === 'Update Text Ad'){

                $('#text_ad_div .theme-scrollable-ad-row[edit="true"]').remove();
                $('#cancel_edit_ad').remove();
                $(this).val('Create New Ad');
            }

            $(this).closest('.theme-create-ad-form-wrap').find('.theme-form-control').val('');
            $(this).closest('.theme-create-ad-form-wrap').find('.charecter_count').text('80');
            $(this).closest('.theme-create-ad-form-wrap').find('.words_count').text('0');

            if (ad_image){
                add_form['creative_url'] = ad_image;
            }

            ad_image = false;

            var template = _.template($("#ad_form_template").html())({data: add_form});
            uploader.splice();
        }
        else {
            return false;
        }

        $('#text_ad_div').append(template);
        $('#examplte_show_div').html(template);
        $('#examplte_show_div .theme-list-remove-icon').remove();
        $('#examplte_show_div .theme-ad-action-btn-group').remove();
        $('#examplte_show_div .hidden_inputs').remove();
        return false;
    });

    $('.theme-form-control').on('keypress', function(){

        var change_type = ['title', 'description', 'display_url', ],
            type = $(this).data('type');

        if ($.inArray(type, change_type) >= 0){

            var new_text = (type === 'description') ? $('#desc_1').val() + ' ' +  $('#desc_2').val() : $(this).val();
            $('#examplte_show_div [data-type="' + type + '"]').text(new_text);

            var text_span = $(this).parent().find('.charecter_count');
            text_span.text(text_span.attr('maxlength')*1 - $(this).val().length);
        }
    });

    $(document).on('click', '#add_new_keyword', function(){
       // console.log(this);
        var keyword = $('#keyword_height').val();
        var dublicate = false;
        if(keyword) {
            var lines = keyword.split('\n');
            var word_count = 0;
            var word_error_count = 0;
            var characters_error_count = 0;
            var msg = '';
            if ($(this).val() == 'Edit') {

                word_count = keyword.trim().replace('/\s+/gi', ' ').split(' ').length;
                if (word_count > 10) {
                    alert('Words count in keyword more then 10 words. Please change your keyword');
                    return false;
                }
                if(keyword.length > 80){
                    alert('Characters count in keyword more then 80 characters. Please change your keyword');
                    return false;
                }

                var keyword_valid = keyword.replace(/"/g,'&quot;'); console.log(keyword_valid);

                $('.editable_keyword').children('p').children('span').text(keyword);
                $('.editable_keyword').children('input').val(keyword_valid);
                $('.editable_keyword').css('background','none');
                $('.editable_keyword').removeClass('editable_keyword');
                $('.error_block_keywords').hide();

                $('#add_new_keyword').val('Save');

            } else {
                $.each(lines, function(key, value) {
                    word_count = value.trim().replace('/\s+/gi', ' ').split(' ').length;
                    if (word_count > 10) {
                        word_error_count++;
                        console.log(word_count, 'change');
                        var element = '<div class="add-keyword" style="background: #f74441;"><p><span>' + value + '</span>' +
                            '<button type="button" class="close remove_keyword"><span class="glyphicon glyphicon-trash trash_keyword"></button><button type="button" class="edit_keyword theme-report-table-edit-pencil" ><img src="/v2/images/report-template/table-manage-edit-icon.png" alt=""></button></p><input type="hidden" name="keywords[]" value=""></div>';
                        $('.keyword_list_block').prepend(element);
                        return
                    } else {
                        // console.log('mtav');
                    }

                    if(value.length>80){
                        characters_error_count++;
                        var element = '<div class="add-keyword" style="background: #ff8684;"><p><span>' + value + '</span>' +
                            '<button type="button" class="close remove_keyword"><span class="glyphicon glyphicon-trash trash_keyword"></button><button type="button" class="edit_keyword theme-report-table-edit-pencil" ><img src="/v2/images/report-template/table-manage-edit-icon.png" alt=""></button></p><input type="hidden" name="keywords[]" value=""></div>';
                        $('.keyword_list_block').prepend(element);
                        return
                    }
                    var keyword_valid = value.replace(/"/g,'&quot;'); console.log(keyword_valid);
                    var element = '<div class="add-keyword"><p><span>' + value + '</span>' +
                        '<button type="button" class="close remove_keyword"><span class="glyphicon glyphicon-trash trash_keyword"></button><button type="button" class="edit_keyword theme-report-table-edit-pencil" ><img src="/v2/images/report-template/table-manage-edit-icon.png" alt=""></button></p><input type="hidden" name="keywords[]" value="' + keyword_valid + '"></div>';
                    $('.keyword_list_block').prepend(element);


                });

                if(characters_error_count || word_error_count) {

                    if(characters_error_count) {
                        msg += ' You have '+characters_error_count+' keywords with invalid characters count. ';
                    }
                    if(word_error_count) {
                        msg += ' You have '+word_error_count+' keywords with invalid words count. ';
                    }

                    msg += " Please edit and fix invalid keywords. If you don't fix, invalid keywords will be ignored. ";

                    $('.error_block_keywords').text(msg);
                    $('.error_block_keywords').show();
                }
            }

            // reset keyword textarea
            $('#keyword_height').val('');
        }

    });

    $(document).on('click', '.remove_keyword', function(){

        if (confirm("Are you sure you want to delete this keyword?")) {
            $(this).parent().parent().remove();
        }
    });

    $(document).on('click', '.edit_keyword', function(){
        var keyword = $(this).parent().children('p span').text();
        $(this).parent().parent().addClass('editable_keyword');
        $('#keyword_height').val(keyword);
        $('#add_new_keyword').val('Edit');

    });

    $('.theme-form-control').on('change', function(){

        var change_type = ['title', 'description', 'display_url'],
            type = $(this).data('type');

        if ($.inArray(type, change_type) >= 0){

            var new_text = (type === 'description') ? $('#desc_1').val() + ' ' +  $('#desc_2').val() : $(this).val();
            $('#examplte_show_div [data-type="' + type + '"]').text(new_text);

            var text_span = $(this).parent().find('.charecter_count');
            text_span.text(text_span.attr('maxlength')*1 - $(this).val().length);
        }
    });

    $('.open_pixel').on('click', function(){

        if ($(this).val() == 'Y'){
            $('#open_pixel_layer').removeClass('hidden');
        }
        else {
            $('#open_pixel_layer').addClass('hidden');
        }
    });

    $(document).on('click', '#btn_airpush_ad', function() {

        var type = $('#example-advanced-form-p-0 .theme-tabbed-form-group input[type="radio"]:checked').val();
        var campaignSubType = $('#campaign_type_placement .theme-tabbed-form-group input:checked').val();

        var ad_html = '<div class="theme-scrollable-ad-row">'
            + '<span class="theme-list-remove-icon closer ads_remove"></span>'
            +'<div class="theme-imagead-subrow-bottom">';

        if(campaignSubType != 'DIALOG_CLICK_TO_CALL' && campaignSubType != 'RICH_MEDIA_INTERSTITIAL') {
            ad_html += $('#image_type_select').html();
        }
        if(campaignSubType != 'RICH_MEDIA_INTERSTITIAL') {

            ad_html += '<label class="lbl_fb_title" >Ad Title</label><input maxlength="25"  class="form-control fb_ad_title" type="text" name="ad_title" />';
        } else {
            ad_html += '<label class="lbl_script" >HTML/JS script</label><textarea maxlength="3000"  class="form-control script" name="script" /></textarea>';

        }
        if(campaignSubType != 'DIALOG_CLICK_TO_CALL' && campaignSubType != 'RICH_MEDIA_INTERSTITIAL') {

            ad_html += '<label class="lbl_fb_desc">Ad Description</label><textarea maxlength="40"  class="form-control fb_ad_body" name="ad_body" ></textarea>';
        }

        ad_html += '<div class="theme-adbanner-form-group">'
            + '<input type="hidden" name="ads[]" class="ads_info" >';

        if(campaignSubType == 'PUSH_CLICK_TO_CALL' || campaignSubType == 'DIALOG_CLICK_TO_CALL') {

            ad_html += '<input type="text" name="destination" maxlength="11" class="theme-form-control theme-imagead-url-field" placeholder="1XXXXXXXXXX" />';
        }  else if(campaignSubType = 'RICH_MEDIA_INTERSTITIAL') {

            ad_html += '<input type="text" name="dest_url[]" style="display: none !important;" class="theme-form-control theme-imagead-url-field" placeholder="Destination URL - http://..." />';
        } else {

            ad_html += '<input type="text" name="dest_url[]" class="theme-form-control theme-imagead-url-field" placeholder="Destination URL - http://..." />';
        }


        ad_html +='<div class="theme-btn theme-submit-control theme-ad-save">save</div>'
            +'</div></div></div>';
            alert(ad_html);die();
        $("#ads_container").append(ad_html).show();

    });

    $(document).on('click', '#btn_facebook_ad', function() {

        var type = $('#example-advanced-form-p-0 .theme-tabbed-form-group input[type="radio"]:checked').val();
        var campaignSubType = $('#campaign_type_placement .theme-tabbed-form-group input:checked').val();

        var ad_html = '<div class="theme-scrollable-ad-row">'
            + '<span class="theme-list-remove-icon closer ads_remove"></span>'
            +'<div class="theme-imagead-subrow-bottom">';

        ad_html += '<label class="lbl_fb_title" >Event url</label><input maxlength="100"  class="form-control fb_ad_title event_url" type="text" name="event_url" /><input type="hidden" name="ads[]" class="ads_info" >';

        ad_html +='<div class="theme-btn theme-submit-control theme-ad-save">save</div>'
            +'</div></div></div>';

        $("#ads_container").append(ad_html).show();

    });

 

    $( ".check_type" ).change(function() {

        if ($(this).is(':checked')) {

            $('#campaign_type_placement').find('.theme-tabbed-form-group').hide();
            $('#campaign_type_placement').find('.' + $(this).attr('type_class')).show();
            $('#campaign_type_placement').find('.' + $(this).attr('type_class')).first().children('input').prop( "checked", true );
        }

    });


    $('#network_types_list .theme-tabbed-form-group:first-child input').prop('checked', true);
    var typeClass = $('#network_types_list .theme-tabbed-form-group:first-child input').attr('type_class');
    $('#campaign_type_placement').find('.' + typeClass).show();
    $('#campaign_type_placement').find('.' + typeClass).first().children('input').prop('checked', true);


    $('.display_none').css('opacity',1);

    $('input[name="email_audience_type"]').change(function(){
        var value = $(this).val(); console.log(value);
        if(value=='new') {
            $('#email_new_block').show();
            $('#email_existing_block').hide();
        } else {
            $('#email_existing_block').show();
            $('#email_new_block').hide();
        }
    });

    $('#fb_form_context_type').change(function(){
        var value = $(this).val(); console.log(value);
        if(value=='bullets') {
            $('#bullets_block').show();
            $('.bullets_preview').show();
            $('#paragraph_block').hide();
            $('.paragraph_preview').hide();
        } else {
            $('#paragraph_block').show();
            $('.paragraph_preview').show();
            $('#bullets_block').hide();
            $('.bullets_preview').hide();
        }
    });
    $('#fb_form_export_type').change(function(){
        var value = $(this).val(); console.log(value);
        if(value=='email_address') {
            $('#email_export_block').show();
        } else {
            $('#email_export_block').hide();
        }
    });
    $('#form_type').change(function(){
        var value = $(this).val(); console.log(value);
        if(value=='new') {
            $('#form_preview').show();
            $('#new_form_block').show();
            $('#existing_form_block').hide();
            $('#existing_form_preview_div').hide();
        } else {
            $('#existing_form_block').show();
            $('#existing_form_preview_div').show();
            $('#new_form_block').hide();
            $('#form_preview').hide();
        }
    });
    $('input[name="audience_type"]').change(function(){
        var value = $(this).val(); console.log(value);
        if(value=='new') {
            $('#lookalike_new_block').show();
            $('#lookalike_existing_block').hide();
        } else {
            $('#lookalike_existing_block').show();
            $('#lookalike_new_block').hide();
        }
    });

    $('input[name="lookalike_type"]').change(function(){
        var value = $(this).val(); console.log(value);
        if(value=='page') {
            $('#lookalike_page_block').show();
            $('#lookalike_pixel_block').hide();
        } else {
            $('#lookalike_pixel_block').show();
            $('#lookalike_page_block').hide();
        }
    });
});

function is_upper_case(str) {
    return str === str.toUpperCase();
}

function check_url(s) {
    var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    return regexp.test(s);
}

var form = $("#example-advanced-form").show();

form.steps({
    headerTag: "h3",
    bodyTag: "fieldset",
    transitionEffect: "slideLeft",
    onStepChanging: function (event, currentIndex, newIndex)
    {
       // console.log(newIndex, currentIndex);
        // Allways allow previous action even if the current form is not valid!
        var type = $('#example-advanced-form-p-0 .theme-tabbed-form-group input[type="radio"]:checked').val();
        var campaignSubType = $('#campaign_type_placement .theme-tabbed-form-group input:checked').val();
        // Forbid next action on "Warning" step if the user is to young
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

        if(newIndex == 1){
            $("#max_clicks").val("");
            $("#max_impressions").val("");
            $("#max_budget").val("");
            $("#max_budget_span").html("");
            var assign_user_select = $('select[name="assign_user"]'),
                remarketing_io_select = $('#remarketing_io'),
                remarketing_io_options = $('#remarketing_io option'),
                io_based_retargeting_ios_select = $('#io_based_retargeting_ios'),
                io_based_retargeting_ios_options = $('#io_based_retargeting_ios option');

                assign_user_select.change(
                    function () {
                        $.ajax({
                            method: "GET",
                            url: "/v2/campaign/get_userlist_from_io",
                            dataType: "json",
                            data: { user_id: $(this).val() }
                        }).done(function( response ) {
                                remarketing_io_select.empty();
                                io_based_retargeting_ios_select.empty();
                                $.map( response.data, function( item ) {
                                    remarketing_io_select.append("<option value='"+item.io+"' class='network_"+item.network_id+"'>" + item.io + "</option>")
                                    io_based_retargeting_ios_select.append("<option value='"+item.io+"' class='network_"+item.network_id+"'>" + item.io + "</option>")
                                });
                                remarketing_io_select.trigger("chosen:updated");
                                io_based_retargeting_ios_select.trigger("chosen:updated");

                            });
                    }
                )
            if(type == 'DISPLAY'){
                $("#guarantee").show();
            }else{
                 $("#guarantee").hide();
            }
            if (type == 'EMAIL'){
                $('#enable_campaign_div').show();
                if(campaignSubType =='FB-PAGE-LIKE'){
                    $("#max_clicks_label").empty();
                    $("#max_clicks_label").html('Maximum Likes :');
                    $("#max_clicks").attr('placeholder','Maximum Likes');
                }
                $('#daily_budgets').hide();
                $('#has_open_pixel_div').show();
                $('#open_pixel_div').show();
            }
            else{

                $('#daily_budgets').show();
                $('#enable_campaign_div').show();

                if(campaignSubType =='FB-PAGE-LIKE' || campaignSubType == 'FB-LEAD') {
                    if (campaignSubType == 'FB-PAGE-LIKE') {
                        $("#max_clicks_label").empty();
                        $("#max_clicks_label").html('Maximum Likes :');
                        $("#max_clicks").attr('placeholder', 'Maximum Likes');
                    }

                    if (campaignSubType == 'FB-LEAD') {
                        $("#max_clicks_label").empty();
                        $("#max_clicks_label").html('Maximum Leads :');
                        $("#max_clicks").attr('placeholder', 'Maximum Leads');
                    }
                } else {
                    $("#max_clicks_label").empty();
                    $("#max_clicks_label").html('Maximum Clicks :');
                    $("#max_clicks").attr('placeholder', 'Maximum Clicks');
                }

                $('#max_impressions').show();
                $('#open_pixel_div').hide();
            }


            (function() {
                var selected = {};
                $('#multiple_select_manage_viewers').click(function(e) {
                    var $this = $(this),
                        options = this.options,
                        option,
                        value,
                        n;

                    // Find out what option was just added
                    value = $this.val();

                    // Re-apply the selections
                    for (n = 0; n < options.length; ++n) {
                        option = options[n];
                        if (option.value == value) {
                            // The one being updated
                            selected[value] = !selected[value];
                        }

                        // One of the others
                        option.selected = !!selected[option.value];
                    }

                    return false;
                });
            })();

            if(type=='FACEBOOK') {
                $('.campaign_info .theme-geoform-group').eq(4).hide()
                if(user.is_billing_type == 'FLAT') {
                    $('#tier_3').trigger('click');
                    $('.tiers_block').hide();
                }
            } else {
                $('.campaign_info .theme-geoform-group').eq(4).show()
                if(user.is_billing_type == 'FLAT') {
                    $('#tier_1').trigger('click');
                    $('.tiers_block').show();
                }
            }
        }
        if(newIndex == 2){
            // var date = $('#start_date_datepicker').val();
            // $('.add_width .time-left-block').each(function(index){
            //     $(this).text(moment(date).add('days', index).format('dddd'));
            // })
        }
        if(newIndex == 3){

            console.log(type, campaignSubType);
            if(!valide_io) {
                return false;
            }

            /**
             * IP Retargeting will be ENABLE for DISPLAY / DISPLAY-TARGET
             * campaign Type for now
             */
            if ( campaignSubType == "DISPLAY" || campaignSubType == "DISPLAY-RETARGET" ) {
                console.log('RETAGETING_IP_SHOW');
                $('.retargeting-ip-form-wrap').show(0);
            } else {
                console.log('RETAGETING_IP_HIDE');
                $('.retargeting-ip-form-wrap').hide(0);
            }

            if(type == "EMAIL") {
                $('.theme-imagead-section').hide();
                $('.theme-textad-section').hide();
                $('.form-for-email-pays-campaign').show();
            }
            else if(campaignSubType == 'TEXTAD') {
                $('.theme-imagead-section').hide();
                $('.theme-textad-section').show();
                $('.form-for-email-pays-campaign').hide();
            }
            else {
                $('.theme-imagead-section').show();
                $('.theme-textad-section').hide();
                $('.form-for-email-pays-campaign').hide();
            }

            if($('#facebook').is(':checked')) {

                if (campaignSubType == "FB-PROMOTE-EVENT") {
                    $('#uploader').hide();
                    $('.facebook_allowed_text').hide();
                    $('#btn_facebook_ad').show();
                    var ad_html = '<div class="theme-scrollable-ad-row">'
                        + '<span class="theme-list-remove-icon closer ads_remove"></span>'
                        +'<div class="theme-imagead-subrow-bottom">';

                    ad_html += '<label class="lbl_fb_title" >Event url</label><input maxlength="100"  class="form-control fb_ad_title event_url" type="text" name="event_url" /><input type="hidden" name="ads[]" class="ads_info" >';

                    ad_html +='<div id="ad-save" class="theme-btn theme-submit-control theme-ad-save">save</div>'
                        +'</div></div></div>';

                    $("#ads_container").append(ad_html).show();
                }

                if ($('input[value="FB-LOCAL-AWARENESS"]').is(':checked')) {
                    $("#country, label[for='country'], #geo-country, #state, label[for='state']").hide();
                    $('#expand_audience_network').show();
                    $(".address, .lbl_page_id").show();
                } else {
                    $("#country, label[for='country'], #geo-country, #state, label[for='state']").show();
                    $('#expand_audience_network').hide();
                    $(".address, .lbl_page_id").hide();
                }

                if (campaignSubType != "FB-PROMOTE-EVENT") {
                    $('.facebook_allowed_text').show();
                }

                if (campaignSubType != "FB-DESKTOP-RIGHT-COLUMN") {
                    $('#expand_instagram').hide();
                }

                if (campaignSubType != "FB-PROMOTE-EVENT") {
                    $('#btn_facebook_ad').hide();
                }

                if (campaignSubType == "FB-CAROUSEL-AD") {
                    $( "#ads_container").sortable({revert: true});
                    $('#info_block_for_carusel').show();


                } else {
                    if($("#ads_container").sortable("instance")) {
                        $("#ads_container").sortable('destroy');
                    }
                    $('#info_block_for_carusel').hide();
                }
                var daily_budget = $("input[name='budget']");

                $('#geo-postal-radius option:nth-last-child(-n+3)').hide();
                //$('.theme-mobile-carrer-row').hide();
                $('#ad_keyword_block #btn_add_ad').hide();


                if ( (daily_budget.val() !== '') && (daily_budget.val() < 5 ) ) {
                    $('#min_daily_budget_error .min_daily_budget_error_span').html('5.00');
                    $('#min_daily_budget_error').show();
                    return false;
                }
                else{
                    $('#min_daily_budget_error').hide();
                }

                if(campaignSubType == 'FB-DESKTOP-NEWS-FEED' || campaignSubType == 'FB-CAROUSEL-AD' || campaignSubType == 'FB-DESKTOP-RIGHT-COLUMN' || campaignSubType == 'FB-MOBILE-NEWS-FEED') {
                    $('#expand_instagram').show();
                    $('#expand_fb').show();
                }

                if(campaignSubType == 'FB-DESKTOP-NEWS-FEED' || campaignSubType == 'FB-CAROUSEL-AD' || campaignSubType == 'FB-LOCAL-AWARENESS' || campaignSubType == 'FB-MOBILE-NEWS-FEED') {
                    $('#expand_audience_network').show();
                    $('#expand_fb').show();
                }

                $('#lookalike_audience_block').show();
                $('#email_audience_block').show();

            }
            else {
                $('#lookalike_audience_block').hide();
                $('#email_audience_block').hide();
                $('#geo-postal-radius option:nth-last-child(-n+3)').show();
                $('.theme-mobile-carrer-row').show();
                $('#expand_fb').hide();
                $('#info_block_for_carusel').hide();
                if($("#ads_container").sortable("instance")) {
                    $("#ads_container").sortable('destroy');
                }
                if(campaignSubType == 'THIRD-PARTY-AD-TRACK') {
                    $('#ad_keyword_block #btn_add_ad').hide(0);
                    $("#audience_block").hide(0);
                } else {
                    $('#ad_keyword_block #btn_add_ad').show(0);
                }
            }

            if($('#airpush').is(':checked')) {
                var daily_budget = $("input[name='budget']");
                $('#postal-code + label').hide();
                $('#geo-device-type option').hide();
                $('#geo-device-type option[net-type = "airpush"]').show();
                $('#geo-device-type option[value = "Mobile"]').attr('selected', true);
                $('#ad_keyword_block').hide();
                $('#device_block h2').text('Select device');

                if ( (daily_budget.val() !== '') && (daily_budget.val() < 10 ) ) {
                    $('#min_daily_budget_error .min_daily_budget_error_span').html('10.00');
                    $('#min_daily_budget_error').show();
                    return false;
                }
                else{
                    $('#min_daily_budget_error').hide();
                }

                if( campaignSubType == 'LANDING_PAGE' || campaignSubType == 'PUSH_CLICK_TO_CALL' || campaignSubType == 'RICH_MEDIA_INTERSTITIAL' || campaignSubType == 'DIALOG_CLICK_TO_CALL') {
                    $('#image_upload_block').hide();
                    $('#airpush_select_block').show();

                    var ad_html = '<div class="theme-scrollable-ad-row">'
                        + '<span class="theme-list-remove-icon closer ads_remove"></span>'
                        +'<div class="theme-imagead-subrow-bottom">';
                    if(campaignSubType != 'DIALOG_CLICK_TO_CALL' && campaignSubType != 'RICH_MEDIA_INTERSTITIAL') {
                        ad_html += $('#image_type_select').html();
                    }
                    if(campaignSubType != 'RICH_MEDIA_INTERSTITIAL') {

                        ad_html += '<label class="lbl_fb_title" >Ad Title</label><input maxlength="25"  class="form-control fb_ad_title" type="text" name="ad_title" />';
                    } else {
                        ad_html += '<label class="lbl_script" >HTML/JS script</label><textarea maxlength="3000"  class="form-control script" type="text" name="script" /></textarea>';

                    }
                    if(campaignSubType != 'DIALOG_CLICK_TO_CALL' && campaignSubType != 'RICH_MEDIA_INTERSTITIAL') {

                        ad_html += '<label class="lbl_fb_desc">Ad Description</label><textarea maxlength="40"  class="form-control fb_ad_body" name="ad_body" ></textarea>';
                    }

                    ad_html += '<div class="theme-adbanner-form-group">'
                        + '<input type="hidden" name="ads[]" class="ads_info" >';

                    if(campaignSubType == 'PUSH_CLICK_TO_CALL' || campaignSubType == 'DIALOG_CLICK_TO_CALL') {

                        ad_html += '<input type="text" name="destination" maxlength="11" class="theme-form-control theme-imagead-url-field" placeholder="1XXXXXXXXXX" />';
                    }  else if(campaignSubType = 'RICH_MEDIA_INTERSTITIAL') {

                        ad_html += '<input type="text" name="dest_url[]" style="display: none !important;" class="theme-form-control theme-imagead-url-field" placeholder="Destination URL - http://..." />';
                    } else {

                        ad_html += '<input type="text" name="dest_url[]" class="theme-form-control theme-imagead-url-field" placeholder="Destination URL - http://..." />';
                    }
                    ad_html +='<div class="theme-btn theme-submit-control theme-ad-save">save</div>'
                        +'</div></div></div>';

                    $("#ads_container").append(ad_html).show();

                }
                else {
                    $('#airpush_select_block').hide();
                    $('#image_upload_block').show();
                }

            }
            else {
                $('#geo-device-type option:first-child').attr('selected', true);
                $('#geo-device-type option').show();
                $('#geo-device-type option[net-type = "airpush"]').hide();
                $('#postal-code + label').show();
                $('#ad_keyword_block').show();
                $('#device_block h2').text('Mobile / Carrier Options');
                $('#airpush_select_block').hide();
                $('#image_upload_block').show();
            }

            $('#yahoo_app_url').hide();
            $('.yahoo_alert').hide();
            $('.yahoo_carousek_info').hide();
            $("#yahoo_call").hide();
            $('#info_block_for_carusel').hide();
            if(type == 'YAHOO') {
                $("#yahoo_call").show();
                if (campaignSubType == 'APP_INSTALL_YAHOO') {
                    $('#yahoo_app_url').show();
                }
                if (campaignSubType == 'YAHOO_CAROUSEL') {
                    $( "#ads_container").sortable({revert: true});
                    $('#info_block_for_carusel').show();
                    $('.yahoo_carousek_info').show();
                }
                $('.yahoo_alert').show();
            }

            if($('.marketing-ads-radio').is(':checked') || (type=='FACEBOOK' && campaignSubType !='FB-LEAD')) {
                $("#theme-retargetting-section").show();

                    var hide_class_name;
                    var show_class_name;
                    if (type == 'DISPLAY') {
                        hide_class_name = 'network_5';
                        show_class_name = 'network_1';
                    } else if (type == 'FACEBOOK') {
                        hide_class_name = 'network_1';
                        show_class_name = 'network_5';
                    }

                    $('#remarketing_io option.' + show_class_name).attr('disabled', false);
                    $('#remarketing_io option.' + hide_class_name).attr('disabled', true);
                    $("#remarketing_io").trigger('chosen:updated');

                    $('#io_based_retargeting_ios option.' + show_class_name).attr('disabled', false);
                    $('#io_based_retargeting_ios option.' + hide_class_name).attr('disabled', true);
                    $("#io_based_retargeting_ios").trigger('chosen:updated');

            }
            else {
                $("#theme-retargetting-section").hide();
            }


            if(type=='FACEBOOK' || type == 'DISPLAY' || type == 'YAHOO') {
                if(type == 'DISPLAY' && campaignSubType != 'THIRD-PARTY-AD-TRACK') {
                    $("#audience_block").show(0);
                }
                if(type=='FACEBOOK') {
                    $("#facebook_audiences").show();
                    $("#fb_category_type_select").show();
                    $("#google_audiences").hide();
                    $("#google_category_type_select").hide();

                    if($('#affinity_input').val() || $('#in_market_input').val() || $('#yahoo_interests_input').val()){
                        $('#affinity_input').val('');
                        $('#in_market_input').val('');
                        $('#yahoo_interests_input').val('');
                        $('#results').html();
                    }
                } else {
                    if(type == 'DISPLAY') {
                        $("#google_audiences").show();
                        $("#google_category_type_select").show();
                        $("#yahoo_audiences").hide();
                        $('#yahoo_interests_input').val('');
                    } else {
                        $("#yahoo_audiences").show();
                        $("#google_audiences").hide();
                        $("#google_category_type_select").hide();
                        $('#affinity_input').val('');
                    }

                    $("#fb_category_type_select").hide();
                    $("#facebook_audiences").hide();

                    if ($("#demographics_input").val() || $("#interests_input").val() || $("#behaviors_input").val() || $("#schools_input").val() || $("#majors_input").val() || $("#works_input").val() || $("#jobs_input").val()) {

                        $("#demographics_input").val('');
                        $("#interests_input").val('');
                        $("#behaviors_input").val('');
                        $("#schools_input").val('');
                        $("#majors_input").val('');
                        $("#works_input").val('');
                        $("#jobs_input").val('');
                        $('#results').html();
                    }
                }
            } else {
                $("#audience_block").hide();
            }

            if(type == 'DISPLAY') {

                $('.theme-domain-exclusions-row').show();
                $('.keywords_block_msg').show();
            } else {
                $('.keywords_block_msg').hide();
                $('.theme-domain-exclusions-row').hide();
            }
        }
        if(newIndex == 4){

            if($('#facebook').is(':checked')) {
                if(type == 'FACEBOOK' && !$("#interests_input").val()) {
                    alert('please select audience');
                    return false;
                }

                if (($('input[value="FB-PAGE-LIKE"]').is(':checked')) || ($('input[value="FB-DESKTOP-NEWS-FEED"]').is(':checked')) || ($('input[value="FB-CAROUSEL-AD"]').is(':checked')) || ($('input[value="FB-VIDEO-VIEWS"]').is(':checked')) || ($('input[value="FB-LOCAL-AWARENESS"]').is(':checked')) || ($('input[value="FB-PROMOTE-EVENT"]').is(':checked'))) {

                    if ($('#expand_instagram').is(':checked')) {
                        $('.facebook_page_like_page_id').hide();
                    } else {
                        $('.facebook_page_like_page_id').show();
                    }
                } else {
                    $('.facebook_page_like_page_id').hide();
                }
                if ($('input[value="FB-LEAD"]').is(':checked')) {
                    $('#facebook_form').show();
                    $('#form_preview_block').show();
                    $('.facebook_page_like_page_id').show();
                    $('.create_your_video_ads').html('CREATE YOUR LEAD FORM AND ADS');
                } else {
                    $('.create_your_video_ads').html('CREATE YOUR IMAGE ADS');
                    $('#facebook_form').hide();
                    $('#form_preview_block').hide();
                }

            } else {
                $('.facebook_page_like_page_id').hide();
                $('#form_preview_block').hide();
                $('#facebook_form').hide();
                $('.create_your_video_ads').html('CREATE YOUR IMAGE ADS');
            }
          if($('input[value="FB-VIDEO-VIEWS"]').is(':checked') || $('input[value="FB-VIDEO-CLICKS"]').is(':checked') || $('input[value="VIDEO_YAHOO"]').is(':checked')){
                $('.create_your_video_ads').html('CREATE YOUR VIDEO ADS');
                $('.facebook_page_like_page_id').css('padding-top','15px');
            }
            if(campaignSubType == 'RICH_MEDIA_SURVEY'){
                $('.rich_media_step').show();
                $("#theme-from-rich-media").show();
                $('.create_your_video_ads').html('CREATE YOUR RICH MEDIA SURVEY');
                $("#theme-from-rich-media").css({'font-size':'15px'});
                $('.btn_richmed').css({'display':'inline'});
                $('#rich_media_radio_plus').hide();
                $('#rich_media_checkbox_plus').hide();
                 var type;
                $('#rich_media_checkbox').click(function(){
                    $(this).css({'background-color':'#4abc40'});
                    $('#rich_media_radio').css({'background-color':'#ccc'});
                    $('#rich_media_checkbox_plus').show();
                    $('#rich_media_radio_plus').hide();
                    $('#rich_media_inp').html(
                        '<input type="text" class="theme-geoform-control theme-form-control valid" placeholder="Answere 1" style="width: 93%">'
                        +'<input type="text" class="theme-geoform-control theme-form-control valid" placeholder="Answere 2" style="width: 93%">'
                    );
                    $('#inp_type').val('checkbox');
                }); 
                $('#rich_media_radio').click(function(){
                    $(this).css({'background-color':'#4abc40'});
                    $('#rich_media_checkbox').css({'background-color':'#ccc'});
                    $('#rich_media_checkbox_plus').hide();
                    $('#rich_media_radio_plus').show();
                    $('#rich_media_inp').html(
                        '<input type="text" class="theme-geoform-control theme-form-control valid" placeholder="Answere 1" style="width: 93%">'
                        +'<input type="text" class="theme-geoform-control theme-form-control valid" placeholder="Answere 2" style="width: 93%">'
                    );
                    $('#inp_type').val('radio');
                });
                var i=2;
                $('#rich_media_checkbox_plus').click(function(){
                    j=2;
                    i++;
                    if(i<=5){
                        $('#rich_media_inp').append(
                            '<input type="text" name="answers[]" class="theme-geoform-control theme-form-control valid" placeholder="Answere '+i+'" style="width: 93%">'
                        );
                    }
                });
                var j=2;
                $('#rich_media_radio_plus').click(function(){
                    i=2;
                    j++;
                    if(j<=5){
                        $('#rich_media_inp').append(
                            '<input type="text" name="answers[]" class="theme-geoform-control theme-form-control valid" placeholder="Answere '+j+'" style="width: 93%">'
                        );
                    }
                });
                $('#save_rich_ad').click(function(){
                    if(!$("div").is('#resized')){
                        alert('Please Upload Logo (Step 1)');
                        return false;
                    }
                    if($("#resized").find('h5').length>0){
                        alert('Please Upload Logo (Step 1)');
                        $('#rm_question').val('')
                        for (var i = 1; i <= $('#rich_media_inp').children().length; i++) {
                        $('#rich_media_inp :nth-child('+i+')').val(''); 
                        return false;               
                    }
                    }
                    $('.theme-table-top-cell #ads_container').attr("style", "display: block !important");
                    var rmQuestion=$('#rm_question').val().trim();
                    if (rmQuestion == '') {
                        alert('Field Question is required.');
                        return false;
                    }
                    for (var i = 1; i <= $('#rich_media_inp').children().length; i++) {
                        if ($('#rich_media_inp :nth-child('+i+')').val().trim() == '') {
                            alert('Answer Field is required.');
                            return false;
                        }
                    }
                    $('#resized').append('<h5 class="h5_question" style="width:280px; margin-left: 15px">'+rmQuestion+'</h5>'
                        +'<input type="button" value="Send" style="position:absolute;bottom:1%;right:1%;background-color:rgb(238,50,36);width:60px;height:20px;text-align:center;font-family: Arial, Helvetica, sans-serif;")>');
                    for (var i = 1; i <= $('#rich_media_inp').children().length; i++) {
                        $('#resized').append('<div class="input_banner" style="width:280px ; margin : 0 auto">'
                            +'<label>'
                            +'<input type="'+$('#inp_type').val()+'" name="rm_input" value="a'+i+'" id="inp'+i+'" style="display:inline ; margin: 5px 0 0 5px"> '+$('#rich_media_inp :nth-child('+i+')').val().trim()
                            +'</label></div>');
                    }
                    $('#rm_question').val('');
                    for (var i = 1; i <= $('#rich_media_inp').children().length; i++) {
                        $('#rich_media_inp :nth-child('+i+')').val('');                
                    }
                    $(this).prop('disabled', true);
                });
               
            }else{
                 $("#theme-from-rich-media").hide();
                 $('.rich_media_step').hide();
            }
        }
        if(currentIndex == 4){
            console.log($('#adContainer').html());
            var valide = false;
            var oneAd = false;
            if (type == 'DISPLAY' || type == 'FACEBOOK' || type == 'AIRPUSH' || type == 'YAHOO' ){
            if(campaignSubType == 'YAHOO_CAROUSEL'){
                if($(".theme-scrollable-ad-row").length>=5 && $(".theme-scrollable-ad-row").length<=7){
                            valide = true;
                        }
                        else{
                            valide = false;
                            alert("Please ad number beetwen 3 to 5");
                        }
                        return valide;
                }
                if((campaignSubType == 'FB-PAGE-LIKE' || campaignSubType == 'FB-CAROUSEL-AD' || campaignSubType == 'FB-MOBILE-NEWS-FEED' || campaignSubType == 'FB-VIDEO-VIEWS' || campaignSubType == 'FB-LOCAL-AWARENESS') && ($("#fb_page_select option:selected").val().length < 5)){
                    alert('Select Facebook Page');
                    return false;
                } else if (campaignSubType == 'FB-LOCAL-AWARENESS') {
                    $("#hidden-lat").val('');
                    $("#hidden-lng").val('');
                    var address = $(".address").val();
                    var campaignSubType = $('#campaign_type_placement .theme-tabbed-form-group input:checked').val();
                    var page_id = $("#fb_page_select option:selected").val();

                    var latlng = false;
                    var isAdvertiser = false;
                    var access_status = false;

                    $.ajax({
                        url: "/v2/campaign/getLatLng",
                        type: "POST",
                        dataType: "json",
                        data: {
                            address: address,
                        },
                        success: function(msg) {
                            if (msg.status == "SUCCESS") {
                                var lat = msg.message.lat;
                                var lng = msg.message.lng;
                                $("#hidden-lat").val(lat);
                                $("#hidden-lng").val(lng);
                                latlng = true;
                                console.log(lat + ' ' + lng);
                            }
                        },
                        async: false
                    });

                    $.ajax({
                        url: "/v2/profile/check_access",
                        type: "POST",
                        dataType: "json",
                        data: {
                            page_id: page_id,
                        },
                        success: function(msg) {
                            if(msg.message.isAdvertiser) {
                                console.log(msg.message.access_status)
                                isAdvertiser = true;
                                if(msg.message.access_status == "CONFIRMED") {
                                    access_status = true;
                                }
                            }
                        },
                        async: false
                    });

                    $.ajax({
                        url: "/v2/profile/assign_user_to_page",
                        type: "POST",
                        dataType: "json",
                        success: function(msg) {
                            if(msg.status == "SUCCESS") {
                                console.log(msg);
                            }
                        },
                        async: false
                    });

                    if (!latlng) {
                        alert('Enter correct address');
                        return false;
                    }

                    if (!isAdvertiser || !access_status) {
                        if(!isAdvertiser) {
                            alert('You are not advertiser');
                            return false;
                        } else {
                            alert('You are not CONFIRMED yet our request from your fb page.');
                            return false;
                        }
                    }
                }

                if(campaignSubType == 'FB-MOBILE-NEWS-FEED'){

                    var page_id = $("#fb_page_select option:selected").val();
                    $.ajax({
                        url: "/v2/profile/check_access",
                        type: "POST",
                        dataType: "json",
                        data: {
                            page_id: page_id,
                        },
                        success: function(msg) {
                            if(msg.message.isAdvertiser) {
                                console.log(msg.message.access_status)
                                isAdvertiser = true;
                                if(msg.message.access_status == "CONFIRMED") {
                                    access_status = true;
                                }
                            }
                        },
                        async: false
                    });

                    if(!isAdvertiser) {
                        alert('You are not advertiser');
                        return false;
                    }

                }
                console.log(campaignSubType);
                if(campaignSubType == 'FB-LEAD'){

                    console.log('mtav')
                    if($("#is_fb_form").val() == 0) {
                        alert('Please save Form');
                        return false;
                    }

                }
                if (campaignSubType == 'RICH_MEDIA_SURVEY') {
                    var check_valid = $('#ads_container').find('#valid_true');
                    if(check_valid.length!=0){
                        valide = true;
                    }
                }
                var elements = $('#ads_container').find('input[name="ads[]"]');
                if(elements.length!=0 && campaignSubType != 'RICH_MEDIA_SURVEY'){
                    elements.each(function(key, value){
                        if($(value).val()!=''){
                            valide = true;
                            oneAd = true;
                        }
                        else {
                            valide = false;
                            return false;
                        }
                    });

                    if(oneAd && !valide) {

                        if(confirm("You have not saved ads, do you want to continue without saving?")) {
                            valide = true;
                            $('#ads_container').find('.theme-ad-save').each(function(){

                                $(this).closest('.theme-scrollable-ad-row').remove();

                            });
                            $('#campaign_review').find('.theme-ad-save').each(function(){

                                $(this).closest('.theme-scrollable-ad-row').remove();

                            });

                        }
                        else {
                            valide = false;
                        }

                    }
                }

                if (valide){
                    return valide;
                }
                else {
                    alert('Please submit save button');
                    return valide;
                }


            }
            else if(type !== "EMAIL") {
                var elements = $('#text_ad_div').find('input[name="ads[]"]');
                if(elements.length!=0){
                    elements.each(function(key, value){
                        if($(value).val()!=''){
                            valide = true;
                        }
                    });
                }
                return valide;
            }
        }
        form.validate().settings.ignore = ":disabled,:hidden";
        return form.valid();
    },
    onStepChanged: function (event, currentIndex, priorIndex)
    {

        if (currentIndex < priorIndex){

            if (currentIndex == 0 )
            {
                $('#ads_container').html('');


                $('#marketing-options, #io-based-retargeting-option')
                    .prop('checked', false)
                    .trigger("change");
            }
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
    {
        // need to enable select boxes to send theirs values using form submit
        $('.hour_slider').removeAttr('disabled');
        var data = $("#example-advanced-form").serialize();
       
        show_loader($('#finish_button'));
        
        $.post('/v2/campaign/create_campaign', data, function(result){
            hide_loader();
 
            var data = JSON.parse(result);
            
            if(data.status == 'SUCCESS') {
               // alert(data.message);
                window.location.href = "/v2/campaign/campaign_list/0/SCHEDULED";
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

        'campaign_type': {
            required: true
        },

        'assign_user' : {
            required: function () {
                return user.is_admin == true;
            }
        },

        'io': {
            required: true,
//            alphanumeric: true,
            maxlength : 16
        },

        'so': {
            required: true,
//            alphanumeric: true,
            maxlength : 8
        },

        'name': {
            required: true,
            maxlength : 32
        },

        'vertical': {
            required: true
        },

        'domain': {
            required: true
        },

        'campaign_start_datetime': {
            required: true
        },
        'budget': {
            required: function(){
                return $('#example-advanced-form-p-0 .theme-tabbed-form-group input[type="radio"]:checked').val() !='EMAIL' ? true : false;
            },
            number : true,
            maxlength : 9
        },
        'open_pixel_src[]': {
            require_from_group: function() {
                if($(".pixel_radio input:radio:checked").val() == 'Y' && $('.theme-tabbed-form-group input[type="radio"]:checked').val() == 'EMAIL') {
                    return [1, ".open_pixel_src"];
                } else {
                    return [0, ".open_pixel_src"];
                }
            }
        },

        'max_impressions': {

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
        'campaign_end_datetime': {
            require_from_group: function() {
                if(user.is_billing_type=='PERCENTAGE' && $(".enable-campaign-criteria input:checkbox").is(":checked")) {
                    return [1, ".fillone"];
                } else {
                    return [0, ".fillone"];
                }
            },
            required: function() {
                if(user.is_billing_type=='FLAT') {
                    return true;
                } else {
                    return false;
                }
            }
        },
        'country': {
            required:  function() {
                if($("#country").is(":checked") || $("#state").is(":checked")){
                    return true;
                } else {
                    return false;
                }
            }
        },
        'state[]': {
            required:  function() {
                return $("#state").is(":checked");
            }
        },
        'zip': {
            required:  function() {
                return $("#postal-code").is(":checked");
            }
        },
        'radius': {
            required:  function() {
                return $("#postal-code").is(":checked");
            },
            number : true
        },
        'remarketing_io[]': {
            required:  function() {
                return $("input#marketing-option.theme-geoform-control.theme-form-control").is(":checked");
            }
        },
         'io_based_retargeting_ios[]': {
            required:  function() {
                return $("input#io-based-retargeting-option.theme-geoform-control.theme-form-control").is(":checked");
            }
        }
    },

});

window.fbAsyncInit = function() {
    FB.init({
        appId      : '1674509896124897',
        xfbml      : true,
        version    : 'v2.8'
    });
    FB.AppEvents.logPageView();
};

(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

$('#fb-form-builder').click(function() {
    FB.ui({
            method: 'lead_gen',
            page_id: 221313168255572,
            ad_account_id: 36029346,
}, handleFormResponse);
});
function handleFormResponse(payload) {
    if(payload.status == 'success'){
        $('#fb_form_id').val(payload.formID);
    } else {

    }
    console.log("You just created a new form named " + formName + "!");
    console.log(payload);
}
  
// setTimeout(function(){$('.theme-imagead-section').show();}, 1000);

$('input[name="form[headline]"]').on('keyup', function(){
    $('.form_heading_preview').text($(this).val());
});

$('input[name="form[button_text]"]').on('keyup', function(){
    $('.button_preview').text($(this).val());
});

$('textarea[name="form[paragraph]"]').on('keyup', function(){
    $('.paragraph_preview').text($(this).val());
});

$('input[name="form[bullets][]"]').on('keyup', function(){
    var class_name = $(this).data('bullet');
    $('.bullets_preview li.'+class_name).text($(this).val());
});

$('select[name="form[form_id]"]').on('change', function(){
    console.log($(this));
    var form_json = $( "#form_id option:selected").data('form_json'); console.log(form_json);
    var template = _.template($("#form_preview_template").html())({data: form_json});
    $('#existing_form_preview_div').html(template);
});

$("#fb_page_select").on('change', function(){

    var campaignSubType = $('#campaign_type_placement .theme-tabbed-form-group input:checked').val();
    if(campaignSubType == 'FB-LEAD' && $('#form_type').val()=='new'){
        $('.page_name_preview').text($("#fb_page_select option:selected").text());
    }
});


$(function() {

    /**
     * CAMPAIGN CATEGORY SELECTION
     *
     * "Campaign Vertical" Selection for IAB Categories/Sub-categories
     * in "Campaign Confirmation" Tab during New Campaign Create Process
     */
    $('#iab_category_select').jstree({
        "core" : {
            "themes" : {
                "variant" : "large",
                "stripes" : false,
                "icons" : false,
            }
        },
        "plugins" : [ "wholerow", "checkbox","search"]
    });

    $('#iab_category_select').on('changed.jstree', function (e, data) {
        var i, j, v = [];
        data.instance.open_all(data.node.id);

        for(i = 0, j = data.selected.length; i < j; i++) {
            var node = data.instance.get_node(data.selected[i]),
                data_attr = node.data,
                text = data.instance.get_node(data.selected[i]).text.trim();

            data_attr.vertical = text;
            if ( data_attr.parentcatid ) v.push(data_attr);
        }

        $('textarea[name="vertical"]').val( v.length ? JSON.stringify(v) : '' );

    }).on('select_node.jstree', function(e, data) {
        var node = data.node,
            selected = data.selected;

        var selected_category = [];
        if ( data.selected.length ) {
            selected_category = data.selected.filter(function(cat_id) {
                return cat_id.match(/^IAB\d+$/) != null;
            });
            if ( selected_category.length > 1 ) {
                data.instance.deselect_node(selected_category.splice(selected_category.indexOf(node.id), 1));
            }
        }
    });
    // CAMPAIGN CATEGORY SELECTION END

    /**
     * IP Targeting File validation by AJAX
     * on Retargeting IPs Text File selection
     */
    var retargeting_ips_file = $('#retargeting_ips_file'),
        invalid_ips_display = $('.invalid-ips-display'),
        uploader_img = $('img.file_uploading'),
        file_name_display = $('.ip_targeting_file_name');

    function readSingleFile(e) {
        var file = e.target.files[0];

        if ( !file ) {
            file_name_display.text('No File Chosen.');
            return false;
        }

        var name = file.name,
            type = file.type,
            size = file.size;

        if ( type != 'text/plain' || size > 2 * 1024 * 1024 ) {
            alert('File MUST be text (.txt) file and Max. 2mb');
            return false;
        }

        file_name_display.text(name);

        if ( window.FileReader ) {
            var r = new FileReader();

            r.onload = function(e) {
                var contents = e.target.result;

                $.post('/v2/campaign/validate_retargeting_ip_file', {
                    data: contents
                }, function(res) {
                    if ( !res.all_valid ) {
                        var invalids = '';
                        res.invalids.forEach(function(data) {
                            if ( !data.is_valid ) {
                                invalids += '<h5>' + data.ip + '</<h5>'
                            }
                        });
                        invalid_ips_display.find('div.ips').html(invalids);
                        invalid_ips_display.show(0);

                        // reset input when have error
                        retargeting_ips_file.val('').change();
                    } else {
                        $('#ip_targeting_ips_json').val(JSON.stringify(res.ip_addresses));
                        invalid_ips_display.find('div.ips').html('');
                        invalid_ips_display.hide(0);
                    }
                },'json').always(function() {
                    uploader_img.hide(0);
                });
            };

            r.onprgress = function() {
                uploader_img.show(0);
            };

            r.readAsText(file);
        }
    }

    retargeting_ips_file.on('change', readSingleFile);

    $('#ip_targeting_upload_btn').on('click', function() {
        retargeting_ips_file.trigger('click');
    });
});
