var network = '';
$(function () {
    var campaignType, device, campaignSubType;
    var fileMaxSize = '1000mb';
    var f= $("#uploader").plupload({
        // General settings
        runtimes : 'html5,html4',
        url : '/v2/campaign/uploadFile',



        // User can upload no more then 20 files in one go (sets multiple_queues to false)
        max_file_count: 20,

        chunk_size: '1mb',


        // Resize images on clientside if we can
        resize : {
            //width : 200,
            // height : 200,
            quality : 90,
            crop: true // crop to exact dimensions
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

        filters : {
            // Maximum file size
            max_file_size : fileMaxSize,
            // Specify what files to browse for
            mime_types: [
                {title : "Image files", extensions : "jpeg,jpg,png,gif"},
                {title : "Video files", extensions : "mov,mp4" }

            ]
        },

        init: {

            BeforeUpload: function (up, file) {
                device = $( "#geo-device-type option:selected" ).val();
                campaignType = $('.theme-report-tabbed-form-wrap input:checked').val();
                campaignSubType = $('#campaign_type_placement .theme-tabbed-form-group input:checked').val();
                // if(campaignSubType=='FB-CAROUSEL-AD') { console.log(774);
                //     var elements = $('#ads_container').find('input[name="ads[]"]');
                //     if(elements.length==2){
                //         alert('Max limit for carousel ads is 10');
                //         return false;
                //     }
                // }
                up.settings.multipart_params = {type:campaignType, platform: device, campaignSubType: campaignSubType };
                console.log(campaignSubType,campaignType);
            }
        }

    });



    count = 0;
    var uploader = $("#uploader").plupload('getUploader');

    uploader.bind('FileUploaded', function (upldr, file, object) {

        //$("#upload-result").text("");
        var myData;

        try {
            myData = eval(object.response);
        } catch (err) {
            myData = eval('(' + object.response + ')');
        }

        if(myData.status == false){
             $("div[title='" + myData.title + "']").parent().remove();
             
            $("#upload-result").append("<div>Your "+ myData.title+ " image's size doesn't correspond to any of the " + campaignType + ' ' + campaignSubType + " required sizes. Try again!</div>").show();
            setTimeout(function() { $("#upload-result").hide() }, 6000);
        }
        else if (campaignType == 'AIRPUSH' && file.origSize/1000 > 40) {
            $("div[title='" + myData.title + "']").parent().remove();
        
            $("#upload-result").append("<div>Image size must be not bigger than 40kb</div>").show();
            setTimeout(function() { $("#upload-result").hide() }, 6000)
        
        } else if (campaignType == 'DISPLAY' && file.origSize/1000 > 150) {
            $("div[title='" + myData.title + "']").parent().remove();

            $("#upload-result").append("<div>Image size must be not bigger than 150kb</div>").show();
            setTimeout(function() { $("#upload-result").hide() }, 6000);

        }
        else
        {
          var padding = 0;
          if(myData.creative_height < 110){
              padding = (110-myData.creative_height)/2 + "px";
          }

            var fbTextAreas = '';
            if(campaignType == 'FACEBOOK') {

                fbTextAreas =
                    '<label class="lbl_fb_title" >Ad Title</label><input maxlength="30"  class="form-control fb_ad_title" type="text" name="ad_title" />' +
                    '<label class="lbl_fb_desc">Ad Description</label><textarea maxlength="90"  class="form-control fb_ad_body" name="ad_body" ></textarea>';

                if(campaignSubType == 'FB-DESKTOP-NEWS-FEED') {
                    fbTextAreas +='<label class="lbl_fb_link_description" >Link description Title</label><input type="text" name="link_description"  maxlength="120" class="form-control link_description" placeholder="destination url description" />';
                }
            }

            var yahooTextAreas = '';
            if(campaignType == 'YAHOO') {
                yahooTextAreas =
                    '<label class="lbl_fb_title" >Ad Title</label><input maxlength="50"  class="form-control fb_ad_title" type="text" name="ad_title" />' +
                    '<label class="lbl_fb_desc">Ad Description</label><textarea maxlength="160"  class="form-control fb_ad_body" name="ad_body" ></textarea>' +
                    fbTextAreas;
                if(campaignSubType == 'APP_INSTALL_YAHOO'){
                    yahooTextAreas += '<label class="">Tumblr post URL</label><input type="text" name="tumblr_post_url" class="form-control theme-imagead-url-field tumblr_post_url" placeholder="Tumblr post URL" />';

                }
            }

            var tracking_url = '';
            //console.log(campaignType,user);
            if(campaignType == 'DISPLAY' && user.is_tracking_url=='Y') {
                tracking_url = '<input type="text" name="tracking_url[]" class="theme-form-control theme-imagead-url-field " placeholder="Tracking URL - http://..." />'+
                                '<p>A tracking URL must redirect to the Destination URL as entered for the ad.</p>';
            }

            // if(campaignType == 'DISPLAY') {
            //     if(myData.creative_width == 1200) {
            //         network = 'yahoo';
            //     } else {
            //         network = 'google';
            //     }
            // }


            var destination_url = '<input type="text" name="dest_url[]" class="theme-form-control theme-imagead-url-field " placeholder="Destination URL - http://..." />' ;

            if(campaignSubType == 'FB-PAGE-LIKE' || campaignSubType == 'FB-VIDEO-VIEWS'){
                var destination_url = '<input type="hidden" name="dest_url[]" class="theme-form-control theme-imagead-url-field " placeholder="Destination URL - http://..." />' ;

            }

            if(campaignSubType == 'FB-MOBILE-APP-INSTALLS'){
                var destination_url = '<input type="text" name="app_url[]" class="theme-form-control theme-imagead-url-field app_url" placeholder="App URL - http://..." />'
                                       +'<input type="text" name="app_id[]" class="theme-form-control theme-imagead-url-field app_id" placeholder="App ID" />';

            }

            if(campaignSubType == 'APP_INSTALL_YAHOO'){
                var destination_url = '';

            }

            if(campaignSubType == 'FB-VIDEO-VIEWS' || campaignSubType == 'FB-VIDEO-CLICKS' || campaignSubType == 'FB-LOCAL-AWARENESS' || campaignSubType == 'FB-MOBILE-APP-INSTALLS') {
                var call_to_action_select = '<select name="call_to_action" class="form-control call_to_action">'
                    +'<option value="BOOK_TRAVEL">BOOK TRAVEL</option>'
                    +'<option value="DOWNLOAD">DOWNLOAD</option>'
                    +'<option value="GET_DIRECTIONS">GET DIRECTIONS</option>'
                    +'<option value="INSTALL_APP">INSTALL APP</option>'
                    +'<option value="LEARN_MORE">LEARN MORE</option>'
                    +'<option value="OPEN_LINK">OPEN LINK</option>'
                    +'<option value="SIGN_UP">SIGN UP</option>'
                    +'<option value="WATCH_MORE">WATCH MORE</option>'
                    +'</select>';

                var ext = myData.extension

                var elem = '<h4 class="h4_margin">Ad : ' +myData.creative_width +' X ' +myData.creative_height +' banner</h4>'
                    + '<span class="theme-list-remove-icon closer ads_remove"></span>'
                    + '<figure class="height220" style="padding-top:' + padding + '" >'
                    + '<img class="hover_image" src="/' + myData.file_dir +'" alt="" />'
                    + '</figure>';

                if (ext == 'mov' || ext == 'mp4') {
                    elem = '<h4 class="h4_margin">VIDEO AD</h4>'
                        + '<span class="theme-list-remove-icon closer ads_remove"></span>'
                        + '<video width="450" height="320" controls class="theme-imagead-subrow-bottom">'
                        + '<source src="/' + myData.file_dir +'" type="video/mp4">'
                        + 'Your browser does not support the video tag.'
                        + '</video>';
                }


                var html = '<div class="theme-scrollable-ad-row">'

                    + elem
                    +'<div class="theme-imagead-subrow-bottom">';

                if(campaignSubType != 'FB-LOCAL-AWARENESS' && campaignSubType != 'FB-MOBILE-APP-INSTALLS') {
                    html += call_to_action_select;
                }

                html += fbTextAreas
                    + '<div class="theme-adbanner-form-group">'
                    + '<input type="hidden" name="ads[]" class="ads_info" creative_width ="' +myData.creative_width +'" creative_height ="' +myData.creative_height +'">'
                    + destination_url
                    + '<div class="theme-btn theme-submit-control theme-ad-save">save</div>'
                    + '</div></div></div>';
            } else {
                var elem = '<figure class="height220" style="padding-top:' + padding + '" ><img class="hover_image" src="/' + myData.file_dir +'" alt="" /></figure>';
                if(campaignSubType == 'VIDEO_YAHOO'){
                    var ext = myData.extension;
                    if (ext == 'mov' || ext == 'mp4') {
                        elem = '<h4 class="h4_margin">VIDEO AD</h4>'
                            + '<span class="theme-list-remove-icon closer ads_remove"></span>'
                            + '<video width="450" height="320" controls class="theme-imagead-subrow-bottom">'
                            + '<source src="/' + myData.file_dir +'" type="video/mp4">'
                            + 'Your browser does not support the video tag.'
                            + '</video>';
                    }

                }
                if (campaignSubType == 'RICH_MEDIA_SURVEY'){
                    if(mraid.getState() == "loading"){
                        mraid.addEventListener('ready', showRichMedia(myData));                 
                    }
                    else{
                        showRichMedia();
                    }
                }
                else{
                    var html = '<div class="theme-scrollable-ad-row">'
                        + '<h4 class="h4_margin">Ad : ' +myData.creative_width +' X ' +myData.creative_height +' banner</h4>'
                        + '<span class="theme-list-remove-icon closer ads_remove"></span>'
                        + elem
                        +'<div class="theme-imagead-subrow-bottom">' + fbTextAreas + yahooTextAreas
                        + '<div class="theme-adbanner-form-group">'
                        + '<input type="hidden" name="ads[]" class="ads_info" creative_width ="' +myData.creative_width +'" creative_height ="' +myData.creative_height +'">'
                        + tracking_url
                        + destination_url
                        + '<div id="theme-ad-save-btn" class="rich-ad-save theme-submit-control theme-ad-save">save</div>'
                        + '</div></div></div>';
                }
            }

            $("#ads_container").append(html).attr("style", "display: block !important");


            
            $("body").on('click', '.hover_image', function(){
                
                var src = $(this).attr('src');
                $('#modal_img').attr('src', src);
                $('#image_show_modal').modal('show');
                
            });
            
            
           
//            $('.hover_image').last().popover({
//                html: true,
//                trigger: 'hover',
//                placement : 'top',
//                content: function () {
//                  return '<img class="hover_img" src="'+$(this).attr('src') + '" />';
//                }
//            });
            
            var count=parseInt($("#img_count").val());
            count+=1;
            $("#img_count").val(count);   
        }
    });


    uploader.bind('FilesRemoved', function(up, files) {
        // Called when files are removed from queue
        $("#upload-result").text("");
        var fileName=files[0]["name"];

        $.ajax({
            url: '/v2/html/deleteUploadedFiles',
            type: 'POST',
            data: {fname: fileName}
        });


        var count=parseInt($("#img_count").val());
        count-=1;
        $("#img_count").val(count);

    });

    uploader.bind('FilesAdded', function(up, files){ console.log(666);
        campaignSubType = $('#campaign_type_placement .theme-tabbed-form-group input:checked').val();
        if(campaignSubType=='FB-CAROUSEL-AD') { console.log(774);
            var elements = $('#ads_container').find('input[name="ads[]"]');
            if(elements.length==10){
                alert('Max limit for carousel ads is 10');
                uploader.removeFile(files[0]);
            }
        }
        $("#upload-result").text("");
    });
    // Handle the case when form was submitted before uploading has finished
    $('#form').submit(function(e) {

        // When all files are uploaded submit form
        $('#uploader').on('complete', function() {
            $('#form')[0].submit();
        });

        $('#uploader').plupload('start');

        return false; // Keep the form from submitting
    });

    function showRichMedia(myData){

          var html = '<div class="theme-scrollable-ad-row">'
                    + '<h4 class="h4_margin">Ad : 300 X 250 banner</h4>'
                    + '<div id="adContainer" style="width:300px;margin:10px auto;padding:0px;background-color:#ffffff;">'
                    + '<div id="resized" style="width:298px;height:248px;margin:auto;position:relative;top:0px;left:0px;background-color:#ffffff;border-style:solid;border-width:1px;border-color:rgb(238,50,36);">'
                    + '<img class="hover_image rich_logo" style="position:relative;top:0px;left:0px;" src="/' + myData.file_dir +'"/>'
                    + '<div style="position:absolute;top:1%;right:1%;background-color:rgb(238,50,36);width:20px;height:20px;">'
                    + '<div style="text-align:center;vertical-align:middle;font-family: Arial, Helvetica, sans-serif;">X</div>'
                    + '</div></div></div>'
                    + '<input type="hidden" name="compaignid" id="compaignid" >'
                    + '<input type="hidden" name="ads[]" class="ads_info" creative_width ="300" creative_height ="250">'
                    + '<div id="theme-ad-save" class="theme-btn theme-submit-control theme-ad-save" disabled = "disabled">save</div>'
                    + '</div>';
                    $('#uploader_browse').hide();

         $("#ads_container").append(html).attr("style", "display: block !important");
    }
});
