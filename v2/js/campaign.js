var campaign = {

    options : {
        total_records : '',
        percentage_opens : '',
        percentage_clicks : '',
        percentage_bounce : '',
        total_clicks : '',
        total_opens : '',
        total_bounces : '',
        message_result : '',
        io : '',
        name : '',
        vendor : '',
        domain : '',
        campaign_start_datetime : '',
        geotype : '',
        country : '',
        state : '',
        radius : '',
        zip : '',
        special_instructions : '',
        fire_open_pixel : 'N',
        budget : '0.00',
        vertical : '',
        campaign_is_converted_to_live : '',
        campaign_is_approved : '',
        userid : '',
        cap_per_hour : '',
        campaign_quickbooks_processed_approved : '',
        quickbooks_invoice_ref_id : '',
        apply_discount : '',
        is_geo_expanded : 'N',
        last_geo_expanded_update : '',
        campaign_type : '',
        age : '',
        gender : '',
        platform : '',
        carrier : '',
        remarketing_io : '',
        is_remarketing_io : '',
        network_id : '',
        is_remarketing : '',
        network_campaign_id : '',
        network_campaign_status : '',
        network_name : '',
        ads : [],
    },

    ad : {
        title : '',
        description_1 : '',
        description_2 : '',
        creative_name : '',
        destination_url : '',
        display_url : '',
        creative_width : '',
        creative_url : '', // this is where the banner ad image exists
        creative_height : '',
        creative_status : '',
        create_date : '',
        creative_is_active : '',
        creative_type : '',
        approval_status : '',
        disapproval_reasons : '',
        network_group_id : '',
        network_campaign_id : '',
        network_creative_id : '',
        network_id : '',
        campaign_id : '',
        group_id : '',
    },

    setName : function(val) {
        if(!val) {
            return false;
        } else {
            if(val.length<=25){
                campaign.options.name = val;
                return true;
            } else {
                return false;
            }
        }
    },

    setTitle : function(val) {
        //if(!val) {
        //    $('#title').addClass("is-empty");
        //    return false;
        //} else {
        //    if(val.length<=30){
        //        $('#title').removeClass("is-empty");
        //        this.options.title = val;
        //        return true;
        //    } else {
        //        bootbox.error("????????? ????? ? ????????? 30 ????");
        //        return false;
        //    }
        //}
    },

    setDescription : function(val) {

        //if(!val) {
        //    $('#description').addClass("is-empty");
        //    return false;
        //} else {
        //    if(val.length<=90){
        //        $('#description').removeClass("is-empty");
        //        this.options.description = val;
        //        return true;
        //    } else {
        //        bootbox.error("????????? ????? ? ????????? 90 ????");
        //        return false;
        //    }
        //}
    },

    setStatusId : function($status_id) {
        this.options.status_id = $status_id;
    },

    
    setRegions : function() {
        //$('input:checkbox').filter(':checked').map(function() {
        //    if(campaign.options.regions.contains($(this).val()) == false){
        //        campaign.options.regions.push($(this).val());
        //    }
        //});
        //return true;
    },

    setOwnerEmail : function(val) {
        //email_re = /[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}/;
        //if(!val) {
        //    $('#email').addClass("is-empty");
        //    return false;
        //} else {
        //    if(email_re.test(val)){
        //        this.options.owner.email = val;
        //        $('#email').removeClass("is-empty");
        //        return true;
        //    } else {
        //        bootbox.error("??????? ??? ?????????? ???? ??. ?????");
        //        $('#email').addClass("is-empty");
        //        return false;
        //    }
        //}
    },

    getOptionsByJson : function() {
        return JSON.stringify(this.options);
    },

    
    save : function () {
        if(save_lock){
            return false;
        } else {
            save_lock = true;
        }
        $.ajax({
            url : '/v2/campaign/create_campaign',
            type : "POST",
            data : { 'data':campaign.getOptionsByJson() },
            success : function(response) {
                if(response.status == 1){
                    bootbox.success(response.message, "?????", function() {
                        $('#page5').hide();
                        $('#page6').show();
                    });
                    save_lock = false;
                    //popoxela petq
                }
                if(response.status == -1){
                    save_lock = false;
                    bootbox.error(response.message);
                }
            }
        });
    },

    resetOptions : function() {
        this.options.title = '';
        this.options.description = '';
        this.options.link = '';
        this.options.title_link = '';
        this.options.image_link = '';
        this.options.desc_link = '';
        this.options.percentage_id = 1;
        this.options.category_id = 1;
        this.options.attachment = {};
        $('.link-block #url').val('');
        $('#url-header').val('');
        $('#url-pic').val('');
        $('#url-desctiption').val('');
        $('.title p').html('');
        $('#title').val('');
        $('.description p').html('');
        $('#description').val('');
        //$('#image_edit .photo').css('background-image', 'none');
        $('#image_edit .photo').css('background', 'url("/static/img/adding-campaign/110x80.png") no-repeat scroll center center rgba(0, 0, 0, 0)');
        $('#image_edit .photo').html('<img src="/static/img/adding-campaign/video.png" class="play">');
        $('.upload').html('');
        $('#background_small').html('');
        $('#background_big').html('');
        $('#title').show();
        $('#description').show();
        $('.description').hide();
        $('.title').hide();
        $('.letters-count').show();
        limiter($('#title'),30);
        limiter($('#description'),90);
    },

    next: function(id, flag){

        newid= id+1;
        if(newid==2) {
            campaign.addPage(1);
        }
        if(newid == 3) {
            campaign.addCountry(); //regions checki hamar
            if( !campaign.options.position_id ) {
                bootbox.error("??????? ??? ?????? ??????? ????");
                return;
            }
        }
        if (newid == 4) {
            if(typeof flag =="undefined") {
                if(!campaign.options.start_date || !campaign.options.end_date) {
                    bootbox.error("??????? ??? ?????? ?????????");
                    return;
                }
            } else {
                if( !campaign.options.position_id ) {
                    bootbox.error("??????? ??? ?????? ??????? ????");
                    return;
                }
            }
            var position_id = parseInt(campaign.options.position_id);
            if(position_id >= 4 && position_id <= 10) {
                $('#image_edit').show();
                $('#default_edit').hide();
                $('#creating').hide();
                $('#background_edit').hide();
            } else {
                if(position_id == 1) {
                    $('#default_edit').show();
                    $('#image_edit').hide();
                    $('#creating').hide();
                    $('#background_edit').hide();
                } else {
                    if(position_id == 15) {
                        $('#background_edit').show();
                        $('#default_edit').hide();
                        $('#image_edit').hide();
                        $('#creating').hide();
                    } else {
                        $('#creating').show();
                        $('#default_edit').hide();
                        $('#image_edit').hide();
                        $('#background_edit').hide();
                        $('#change_button').removeClass('gray').addClass('green').html('???? ????');
                    }
                }
            }
            this.options.price = price;
            this.options.regions = checked_regions;
        };
        if(newid == 5) {
            if($.isEmptyObject(campaign.options.attachment)) {
                bootbox.error("??????? ??? ?????? ??????? ?????");
                return;
            } else {
                if(campaign.options.category_id==2){
                    if(!campaign.options.attachment.fallback_image_name){
                        bootbox.error("??????? ??? ?????? ??????? ????????? ?????");
                        return;
                    }
                }
            }



            if(!campaign.linksValidation(3)){
                return;
            };
        }
        if(newid==6) {
            if(!campaign.setOwnerName($('#name').val().trim()) || !campaign.setOwnerSname($('#sname').val().trim()) || !campaign.setOwnerEmail($('#email').val().trim()) || !campaign.setOwnerPhone($('#phone').val().trim())){
                return;
            } else {
                if(!campaign.options.owner.company_name) {
                    if(!campaign.setOwnerCompanyName($('#company').val())){
                        return;
                    }
                }
            }
            campaign.save();
            return;
        }
        $('#page'+id+'').hide();
        $('#page'+newid+'').show();
    },

    back: function($id) {
        $('#page'+$id+'').hide();
        newid= $id-1;
        $('#page'+newid+'').show();
    },

    getCategories: function() {
        $.ajax({
            url : '/staff/campaign/get-Categories-By-Position',
            type : "POST",
            data : { 'data':campaign.options.position_id },
            success : function(response) {
                if(response.status == 1){
                    $("#staff_add").html(response.content);
                    $('#next_hide1').hide();
                    $('#next_hide2').hide();
                    $('#next_hide3').hide();
                    $('#back_hide1').hide();
                    $('#back_hide2').hide();
                    $('#back_hide3').hide();
                }
            }
        });
    },

    changeLinks: function(clicked,Ltype) {
        $('.link-block:hidden').show();
        $(clicked).parent('.link-block').hide();
        link_type = Ltype;
    },
};
