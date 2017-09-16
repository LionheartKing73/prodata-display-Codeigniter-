$(document).ready(function(){
    
    $('.editable').editable({    
        source: [
              {value: "Y", text: 'YES'},
              {value: "N", text: 'NO'}
           ]
    });
    
    $('.user_type').editable({    
        source: [
              {value: "FLAT", text: 'FLAT'},
              {value: "PERCENTAGE", text: 'PERCENTAGE'}
           ],

        success: function(data) {

            var return_obj = JSON.parse(data);

            if (return_obj.status) {

                var min_budget = return_obj.min_budget;
                var user_id = return_obj.user_id;
                
                $(".min_budget[data-pk='"+user_id +"']").editable("setValue",min_budget)


            }

        }
    });
    
    $('.int_editable').editable({
        validate: function(value) {
            if(!$.isNumeric(value)) {
                return ' The field must be integer';
            }
        }
    });
    
    var domain_source = [{value: "", text: 'Empty'}];
    
    $.each(domain_arr, function( key, value ) {
        domain_source.push({value: value.id, text:value.domain})
    });

    var financial_manager_source = [{value: "", text: 'Empty'}];
    var account_ownership_source = [{value: "", text: 'Empty'}];

    $.each(users_arr, function( key, value ) { console.log(value);
        if(value.user_type == 'financial_manager') {
            financial_manager_source.push({value: value.id, text:value.username});
        }
        if(value.user_type == 'account_ownership') {
            account_ownership_source.push({value: value.id, text:value.username});
        }

    });
     
    $('.user_domain').editable({    
        source: domain_source,
        success: function(data){
            
            var return_obj = JSON.parse(data);

            if (return_obj.status){
                
                var domain_id = return_obj.domain_id;
                var user_id = return_obj.user_id;

                if (domain_id !== ''){

                    var result = $.grep(domain_arr, function(e){ return e.id == domain_id; });
                    $("td[user_id='"+user_id +"']").html("<img src='/v2/images/domain_logos/" + result[0].logo + "'/>");
                }
                else {
                    $("td[user_id='"+user_id +"']").html('');
                }
            }
            else {
                console.log(return_obj);
            }
        }
    });

    $('.assign_manager_to_user').editable({
        source: financial_manager_source,
    });

    $('.assign_ownership_to_user').editable({
        source: account_ownership_source,
    });

    $('.button_create_user').on('click', function(event) {
        event.preventDefault();
        var validate_create_viewer = false;
        var form = $(this).parent();
        if(form.find('.check_repeat_viewer_pass').val() !== form.find( "input[name='viewer_pass']" ).val()){
            alert("Password does not match");
            return false;
        } else if((form.find('.check_repeat_viewer_pass').val() !== '') && (form.find("input[name='viewer_email']").val() !== '') )
        {
            var validate_create_viewer = true;
        }

        if(validate_create_viewer == true) {

            $.ajax({
                url: "/v2/admin/create_user_by_type",
                type: "POST",
                dataType: "json",
                data:  form.serialize(),
                success: function(data)	{
                    if(data.success) {
                        alert(data.msg);
                        $(".create_"+form.find("input[type='hidden']").val()).slideToggle(); console.log(financial_manager_source);
                        if(form.find("input[type='hidden']").val() == 'financial_manager') {
                            financial_manager_source.push({ value: data.id, text: form.find("input[name='viewer_name']").val() });
                            $('.assign_manager_to_user').editable('option', {source: financial_manager_source});
                        } else {
                            account_ownership_source.push({value: data.id, text:value.username});
                            $('.assign_ownership_to_user').editable('option', {source: account_ownership_source});
                        }
                    }
                    else {
                        alert(data.msg);
                    }
                }
            });
        }
    });
    $('.btn_create_financial_manager').on('click', function() {
        $(".create_financial_manager").slideToggle();
    });
    $('.btn_create_account_ownership').on('click', function() {
        $(".create_account_ownership").slideToggle();
    });
});