var add_form = {
    creative_type : 'RICH_MEDIA',
    //campaign_id: $('#campaign_id').val()
};

$(document).ready(function(){

    $('#btn_add_ad').on('click', function(){
       // uploader.splice();

        $('#addNewList input').not('input[name=campaign_id]').val('');
        $('#create_new_add').val('Create New Ad');
        $('#addNewList .modal-title').text('Create your text Ad');
        $('#create_new_add').text('Create New Ad');
        $('#addNewList textarea').val('');
        //$('#uploaded_image_prew').html('');
        //add_form['creative_url'] = '';
        //add_form['creative_width'] = '';
        //add_form['creative_height'] = '';
    });

    $('#create_new_add').on('click', function(){

        var valid_ad = true;
        var button_name = $(this).val();
        if(button_name == 'Create New Ad') {
            //if (!add_form.creative_url){
            //    alert('please upload image');
            //    return false;
            //}
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

    $('.btn_edit_modal').on('click', function(event){
        event.preventDefault()
        var ad_id = $(this).data('ad_id'); //console.log(ad_data.redirect_url);
        var script = $(this).closest('td').prev().find('.theme-ad-content .script').val();
        $('#addNewList .modal-title').text('Edit your Ad');

        $("#addNewList input[name=ad_id]").val(ad_id);
        $("#addNewList textarea[name=script]").val(script);

        $('#create_new_add').val('Edit Ad');
        //uploader.splice();
        $('#addNewList').modal('show');
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