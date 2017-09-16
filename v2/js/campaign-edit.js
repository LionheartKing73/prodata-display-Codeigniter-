$("#geo_country_select").change(function(){
    var campaign_state = $('#camp_state').val();
    var camp_states = [];
    camp_states = explode(',', campaign_state);
    
    if($("#state").is(":checked") && ($(this).val()=='US' || $(this).val()=='CA')){
        show_loader();
        $.post('/v2/test/get_states_by_country', { country : $(this).val() }, function(result){
            hide_loader();
            var data = JSON.parse(result);
            if(data.status == 'SUCCESS') {
                var html = '';
                $.each(data.states, function(key, value){
                    html +='<option value="'+value.state+'">'+value.name+'</option>';
                });
                $('select#geo-state').html(html);
            } else {

            }
            $('#geo-state option').each(function () {
                if ($(this).val() == campaign_state) {
                    $(this).attr("selected", "selected");
                }
            });
        });
    } //else {
    $('select#geo-state').html('<option value="" disabled>You can select states only for USA and CANADA</option>');

//                if (!$("#country").is(":checked")) {
//
//                    $('#geo_country_select').prop('selectedIndex', 0);
//                }
    //}
})

$("#edit_end_date_datepicker").datetimepicker({
    format: "Y/m/d H:i",
    minDate: '-1969/12/25',
});

$("#edit_start_date_datepicker").datetimepicker({
    format: "Y/m/d H:i",
    minDate: '-1970/01/1',
});

$('.theme-geoform-group').on( 'keypress', 'input[name="budget"], input[name="max_clicks"]', function(key){
    //console.log($('[name="budget"]'));
    if($(this).val() && key.charCode == 46 && $(this).val().indexOf('.') == -1 ){ //console.log($('[name="budget"]').val());
        return true;
    }

    if( key.charCode>=48 && key.charCode<=57 && (($(this).val().length - $(this).val().indexOf('.')) < 3 || $(this).val().indexOf('.') == -1)){
        return true;
//                if(($(this).val().length - $(this).val().indexOf('.')) == 3){
//                    $(this).val($(this).val().slice(0,-1));
//                }
    }
    return false;
});

$(document).on('click', '#add_new_keyword', function(){
    console.log(this);
    var keyword = $('#keyword_height').val();
    var dublicate = false;

    if(keyword) {

        $('.add-keyword').each(function(key, value){

            if($(value).children('p').children('span').text()==keyword && !$(value).hasClass('editable_keyword')) {
                dublicate = true;
            }
        });

        if(dublicate) {
            alert('Dublicate keyword');
            return false;
        }

        if ($(this).val() == 'Edit') {

            $('.editable_keyword').children('p').children('span').text(keyword);
            $('.editable_keyword').children('input').val(keyword);
            $('.editable_keyword').removeClass('editable_keyword');
            $('#add_new_keyword').val('Save');

        } else {
            console.log(777);
            var element = '<div class="add-keyword"><p><span>' + keyword + '</span>' +
                '<button type="button" class="close remove_keyword"><span class="glyphicon glyphicon-trash trash_keyword"></span></button><button type="button" class="edit_keyword theme-report-table-edit-pencil" ><img src="/v2/images/report-template/table-manage-edit-icon.png" alt=""></button></p><input type="hidden" name="keywords[]" value="' + keyword + '"></div>';
            console.log(element, $('.keyword_list_block'));
            $('.keyword_list_block').prepend(element);
        }
        // reset keyword textarea
        $('#keyword_height').val('');
        $('.keywords_block .charecter_count').text('80');
        $('.keywords_block .words_count').text('0');
    }

});

$(document).on('click', '.remove_keyword', function(){
    //console.log(this);

    if (confirm("Are you sure you want to delete this keyword?")) {
        $(this).parent().parent().remove();
    }
});

$(document).on('click', '.edit_keyword', function(){;
    var keyword = $(this).parent().children('p span').text();
    $(this).parent().parent().addClass('editable_keyword');
    $('#keyword_height').val(keyword);
    $('#keyword_height').trigger('keypress');
    $('#add_new_keyword').val('Edit');

});

$('#keyword_height').on('keypress', function(){

    //var change_type = ['keywords'],
    //    type = $(this).data('type');

    //if ($.inArray(type, change_type) >= 0){
    //
    //    var new_text = (type === 'description') ? $('#desc_1').val() + ' ' +  $('#desc_2').val() : $(this).val();
    //    $('#examplte_show_div [data-type="' + type + '"]').text(new_text);
    //}

    var text_span = $(this).parent().find('.charecter_count');
    text_span.text(text_span.attr('maxlength')*1 - $(this).val().length);

    var word_count = $(this).val().trim().replace('/\s+/gi', ' ').split(' ').length;
    if(word_count > 10) {
        $(this).addClass('error_keyword');
        $(this).parent().find('.words_count').html(word_count).addClass('error');
        $(this).parent().find('.words').addClass('error');
        //$('.modal-dialog .modal-footer').hide();
        $('#add_new_keyword').prop("disabled",true);
    } else {
        $(this).removeClass('error_keyword');
        $(this).parent().find('.words_count').html(word_count).removeClass('error');
        $(this).parent().find('.words').removeClass('error');
        //$('.modal-dialog .modal-footer').show();
        $('#add_new_keyword').prop("disabled",false);
    }

});


$('#theme-geo-form').validate({

    rules: {
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
    },

    submitHandler: function() {
        var data = $("#theme-geo-form").serialize();
        show_loader();
        $.post('/v2/campaign/edit_location', data, function(result){
            hide_loader();
            var data = JSON.parse(result);
            if(data.status == 'SUCCESS') {
                alert(data.message);
            } else {
                alert(data.message);
            }
        });
    }
});

$('#theme-link-form').validate({
    rules: {
        'destination_url': {
            url: true,
            required: true
        },

        'max_clicks': {
            required:  true,
            number: true
        },
    },
    submitHandler: function() {
        var data = $("#theme-link-form").serialize();
        show_loader();
        $.post('/v2/campaign/edit_link', data, function(result){
            hide_loader();
            var data = JSON.parse(result);
            if(data.status == 'SUCCESS') {
                alert(data.message);
            } else {
                alert(data.message);
            }
        });
    }
});

$('#theme-budget-form').validate({

    rules: {
        'budget': {
            required: true,
            number : true,
            maxlength : 9
        },
    },

    submitHandler: function() {
        var data = $("#theme-budget-form").serialize();
        show_loader();
        $.post('/v2/campaign/edit_budget', data, function(result){
            hide_loader();
            var data = JSON.parse(result);
            if(data.status == 'SUCCESS') {
                alert(data.message);
            } else {
                alert(data.message);
            }
        });
    },
});

$('#theme-end-form').validate({

    rules: {
        'campaign_end_datetime': {
            required: true,
        },
    },

    submitHandler: function() {
        var data = $("#theme-end-form").serialize();
        show_loader();
        $.post('/v2/campaign/edit_end_date', data, function(result){
            hide_loader();
            var data = JSON.parse(result);
            if(data.status == 'SUCCESS') {
                alert(data.message);
            } else {
                alert(data.message);
            }
        });
    }
});

$('#theme-start-form').validate({

    rules: {
        'campaign_start_datetime': {
            required: true,
        },
    },

    submitHandler: function() {
        var data = $("#theme-start-form").serialize();
        show_loader();
        $.post('/v2/campaign/edit_start_date', data, function(result){
            hide_loader();
            var data = JSON.parse(result);
            if(data.status == 'SUCCESS') {
                alert(data.message);
            } else {
                alert(data.message);
            }
        });
    }
});

$('#theme-keywords-form').validate({

    //rules: {
    //    'campaign_end_datetime': {
    //        required: true,
    //    },
    //},

    submitHandler: function() {
        var data = $("#theme-keywords-form").serialize();
        show_loader();
        $.post('/v2/campaign/edit_keywords', data, function(result){
            hide_loader();
            var data = JSON.parse(result);
            if(data.status == 'SUCCESS') {
                alert(data.message);
            } else {
                alert(data.message);
            }
        });
    }
});


$('#theme-fb-form-form').validate({

    rules: {
        'email': {
            required: true,
            email:true,
        },

        'email_type': {
            required: true,
        },

        'export_type': {
            required: true,
        },
    },

    submitHandler: function() {
        var data = $("#theme-fb-form-form").serialize();
        show_loader();
        $.post('/v2/campaign/edit_fb_form', data, function(result){
            hide_loader();
            var data = JSON.parse(result);
            if(data.status == 'SUCCESS') {
                alert(data.message);
            } else {
                alert(data.message);
            }
        });
    }
});


// IO BASED RETARGETING FORM SAVING
$('#theme-io-based-retargeting-form').validate({

    rules: {
        'io_based_retargeting_ios': {
            required: true,
        },
    },

    submitHandler: function() {
        var data = $("#theme-io-based-retargeting-form").serialize();
        show_loader();
        $.post('/v2/campaign/edit_io_based_retargeting_io', data, function(result){
            hide_loader();
            var data = JSON.parse(result);
            if(data.status == 'SUCCESS') {
                alert(data.message);
            } else {
                alert(data.message);
            }
        });
    }
});

$(function() {

     /**
     * EDIT CATEGORY
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
            },
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
    }).on('ready.jstree', function(event, data) {
        if ( iab_categories_preselected.length ) {
            data.instance.select_node($.map(iab_categories_preselected, function(d) {
                return d.iab_category_id;
            }));
        }
    });

    $('#theme-iab-category-form').submit(function(e) {
        e.preventDefault();
        var input_field = $(this).find('textarea[name="vertical"]'),
            input_data = input_field.val();

        if ( !input_data || !input_data.length ) {
            alert('No Vertical to Save.');
            return false;
        }

        var campaign_categories = JSON.parse(input_data),
            campaign_id = $(this).find('input[name="campaign_id"]').val(),
            data = {
                verticals: campaign_categories,
                campaign_id: campaign_id
            };

        if ( !campaign_categories || !campaign_categories.length ) {
            alert('Nothing to save.');
            return false;
        }

        show_loader();
        $.post('/v2/campaign/edit_campaign_categories', data, function(data){
            hide_loader();
            if(data.status == 'SUCCESS') {
                alert(data.message);
            } else {
                alert(data.message);
            }
        }, 'json').always(function() {
            input_field.val('');
        });
        return false;
    });

    // Reset verticals on CANCEL
    $('#theme-iab-category-form button[type="reset"]').on('click', function() {
        var jstree = $.jstree.reference('#iab_category_select');
        jstree.deselect_all(true);

        if ( iab_categories_preselected.length ) {
            jstree.select_node($.map(iab_categories_preselected, function(d) {
                return d.iab_category_id;
            }));
        }
    });
    // EDIT CATEGORY END


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

    $('#theme-ip-retargeting-form').submit(function(e) {
        e.preventDefault();
        var input_field = $(this).find('textarea[name="ip_targeting_ips_json"]'),
            input_data = input_field.val();

        if ( !input_data || !input_data.length ) {
            alert('No Retargeting IP to Save.');
            return false;
        }

        var ip_targeting_ips_json = JSON.parse(input_data),
            campaign_id = $(this).find('input[name="campaign_id"]').val(),
            data = {
                ip_targeting_ips_json: ip_targeting_ips_json,
                campaign_id: campaign_id
            };

        if ( !ip_targeting_ips_json || !ip_targeting_ips_json.length ) {
            alert('Nothing to save.');
            return false;
        }

        show_loader();
        $.post('/v2/campaign/edit_campaign_retargeting_ips', data, function(data){
            hide_loader();
            if(data.status == 'SUCCESS') {
                alert(data.message);
            } else {
                alert(data.message);
            }
        }, 'json').always(function() {
            retargeting_ips_file.val('').change();
            input_field.val('');
        });
        return false;
    });

    $('#theme-ip-retargeting-form button[type="reset"]').on('click', function() {
        retargeting_ips_file.val('').change();
    });
    // IP Targeting End

    $('#theme-io-based-retargeting-form button[type="reset"]').on('click', function() {
        var io_based_retargeting_ios_select = $('#io_based_retargeting_ios');
        io_based_retargeting_ios_select.val(selected_campaign_io_for_retargeting).change().trigger("chosen:updated");
    });
});