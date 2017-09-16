{include file="v2/sections/header.php"}

<section class="container-fluid section-profile">
    <div class="container">
        <div class="row">
            <div class="user-info">
                <h3>User Info</h3>
                <button id="btn_user_info_edit" class="btn btn-info">Edit</button>
            </div>
            <div class="user-info-form">
                <form method="post">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="edit">
                                <h5>First Name</h5>
                                <p>{$user.first_name}</p>
                            </div>
                            <div class="form-group">
                                <label>First Name</label>
                                <input name="first_name" type="text" class="form-control" id="FName"  value="{$user.first_name}">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="edit">
                                <h5>Last Name</h5>
                                <p>{$user.last_name}</p>
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input name="last_name" type="text" class="form-control" id="LName" value="{$user.last_name}">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="edit">
                                <h5>Email</h5>
                                <p>{$user.email}</p>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input name="email"  type="email" class="form-control" id="email" value="{$user.email}">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="edit">
                                <h5>Company</h5>
                                <p>{$user.company}</p>
                            </div>
                            <div class="form-group">
                                <label>Company</label>
                                <input name="company"  type="text" class="form-control" id="us-company" value="{$user.company}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="edit">
                                <h5>Address</h5>
                                <p>{$user.address}</p>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <input name="address" type="text" class="form-control" id="address" value="{$user.address}">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="edit">
                                <h5>City</h5>
                                <p>{$user.city}</p>
                            </div>
                            <div class="form-group">
                                <label>City</label>
                                <input name="city" type="text" class="form-control" id="city" value="{$user.city}">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="edit">
                                <h5>State</h5>
                                <p>{$user.state}</p>
                            </div>
                            <div class="form-group">
                                <label>State</label>
                                <input name="state" type="text" class="form-control" id="state" value="{$user.state}">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="edit">
                                <h5>Zip Code</h5>
                                <p>{$user.zip_code}</p>
                            </div>
                            <div class="form-group">
                                <label>Zip Code</label>
                                <input name="zip_code" type="text" class="form-control" id="zip-code" value="{$user.zip_code}">
                            </div>
                        </div>
                    </div>
                    <input type="submit" value="UPDATE" name="info_update" id="btn_info_update" class="btn btn-success btn-lg">
<!--                    <input id="btn_info_update" name="info_update" type="submit"  class="btn btn-success" value="Update" >-->
                </form>
            </div>
        </div>
    </div>
</section>

<section class="container-fluid section-profile">
    <div class="container">
        <div class="row">
            <div class="card-info">
                <h3>Card Info</h3>
                <button class="btn btn-info btn_edit_card">Edit</button>
            </div>
            <div class="card-info-form">
                <form method="post">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="edit">
                                <h5>Card Number</h5>
                                <p>{$user.card_number} </p>
                            </div>
                            <div class="form-group">
                                <label>Card Number</label>
                                <input name="card_number" type="text" class="form-control" id="cardNum" value="{$user.card_number}">
                            </div>
                        </div>
                        <!--<div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="edit">
                                <h5>CVV</h5>
                                <p>{$user.card_cvv}</p>
                            </div>
                            <div class="form-group">
                                <label>CVV</label>
                                <input type="text" class="form-control" id="cvv" value="{$user.card_cvv}">
                            </div>
                        </div>-->
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="edit">
                                <h5>Expiration Date</h5>
                                <p>{$months[$user.card_exp_month - 1]} / {$user.card_exp_year}</p>
                            </div>
                            <div class="form-group">
                                <label>Exp. Year</label>
                                <select class="form-control expyear" name="exp_year">
                                    {for $i = date('Y') to date('Y')+15}
                                    <option {if $user.card_exp_year == $i} selected {/if} value='{$i}'>{$i}</option>
                                    {/for}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">

                            <div class="form-group">
                                <label>Exp.Month</label>
                                <select class="form-control expmonth" name="exp_month">
                                    <option value="1" {if $user.card_exp_month == 1} selected {/if} >01</option>
                                    <option value="2" {if $user.card_exp_month == 2} selected {/if} >02</option>
                                    <option value="3" {if $user.card_exp_month == 3} selected {/if} >03</option>
                                    <option value="4" {if $user.card_exp_month == 4} selected {/if} >04</option>
                                    <option value="5" {if $user.card_exp_month == 5} selected {/if} >05</option>
                                    <option value="6" {if $user.card_exp_month == 6} selected {/if} >06</option>
                                    <option value="7" {if $user.card_exp_month == 7} selected {/if} >07</option>
                                    <option value="8" {if $user.card_exp_month == 8} selected {/if} >08</option>
                                    <option value="9" {if $user.card_exp_month == 9} selected {/if} >09</option>
                                    <option value="10" {if $user.card_exp_month == 10} selected {/if} >10</option>
                                    <option value="11" {if $user.card_exp_month == 11} selected {/if} >11</option>
                                    <option value="12" {if $user.card_exp_month == 12} selected {/if} >12</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <input id="btn_edit_card" name="edit_card" type="submit" class="btn btn-success btn-lg" value="UPDATE">
                </form>
            </div>
        </div>
    </div>
</section>

<section class="container-fluid section-password">
    <div class="container">
        <div class="row">
            <div class="pass-info">
                <h3>Password</h3>
                <button id="btn_reset_pass" class="btn btn-info">Reset</button>
            </div>
            <hr class="border-bottom">
            <div class="pass-info-form">
                <form method="post" id="reset_pass_form">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label>Old Password</label>
                                <input name="old_pass" type="password" class="form-control pass_input" id="oldpass" placeholder="Old Password ">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="new_pass" class="form-control pass_input new_pass" id="npass" placeholder="New Password">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label>Repeat New Password</label>
                                <input type="password" name="repeat_new_pass" class="form-control pass_input repeat_new_pass" id="rnpass" placeholder="Repeat New Password">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <input id="btn_update_pass" type="submit" class="btn btn-success btn-lg" value="UPDATE">
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>


<section class=" container-fluid fb-button">
    <div class="container">
        <div class="row">
    <div class="manageAccess-info">
        <h3>Manage Facebook Connection</h3>
    </div>
    <br><br><br>
    {if !$linkedToFacebook}
    <div class="col-sm-6" >
        <a class="btn btn-info text-uppercase" style="margin
        -bottom: 15px" href="{$loginUrl}">Link Account to Facebook</a>
    </div>
    {else}
    <div class="col-sm-6" >
        <!--        <button class="btn btn-info text-uppercase" type="button" disabled="disabled">You have already linked your account to Facebook</button>-->
        <a class="btn btn-info text-uppercase" style="margin-bottom: 15px" href="{$fbUnlinkUrl}">Unlink Facebook Account</a>
    </div>
    {/if}
    <hr class="border-bottom">
            </div>
        </div>
</section>

<section class="container-fluid section-viewer">
    <div class="container">
        <div class="row">
            <div class="createviewer-info">
                <h3>Create Viewer</h3>
                <button class="btn btn-info btn_create_viewer">Create</button>
            </div>
            <hr class="border-bottom">
            <div class="createviewer-info-form">
                <form method="post">
                    <div class="row">
                        <div class="col-md-3  col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label>First Name</label>
                                <input name="viewer_name" type="text" class="form-control" id="vfname" placeholder="Viewer Name">
                            </div>
                        </div>
                        <div class="col-md-3  col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label>Viewer Email</label>
                                <input type="email" name="viewer_email" class="form-control viewer_email_input" id="vemail" placeholder="Viewer Email">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label>Password</label>
                                <input name="viewer_pass" type="password" class="form-control viewer_pass_input" id="vpass" placeholder="Password">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label>Repeat New Password</label>
                                <input name="repeat_viewer_pass" type="password" class="form-control viewer_pass_input check_repeat_viewer_pass" id="vnpass" placeholder="Repeat New Password">
                            </div>
                        </div>
                    </div>
                    <button id="button_create_viewer" class="btn btn-success" name="create_viewer" />Create</button>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="container-fluid section-manage">
    <div class="container">
        <div class="row">
            <div class="manageAccess-info">
                <h3>Manage access to the campaigns for viewer(s)</h3>
                <button class="btn btn-info btn_manage_viewer">Manage</button>
            </div>
            <hr class="border-bottom">
            <div class="manageAccess-info-form">
                <form method="post">
                    <div class="row mWidth">
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <div class="form-group">
                                <select name="campaign" class="form-control mtest" >
                                    {foreach from=$campaigns_information item=campaign_information}
                                    <option value="{$campaign_information.id}" >{$campaign_information.name}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4  col-sm-4 col-xs-12coloffset1">
                            <div class="form-group">
                                <select name="viewer" class="form-control mem">
                                    {foreach from=$viewers item=viewer}
                                    <option value="{$viewer.id}">{$viewer.username}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2  col-sm-2 col-xs-12">
                            <button id="btn_add_viewer" class="btn btn-success btn-lg">Add</button>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-md-9">
                            {foreach from=$access_campaigns item=access_campaign}
                            <div class="row network_row" >
                                <div class="col-md-5 col-sm-4 col-xs-4 network_info" >{$access_campaign.name}</div>
                                <div class="col-md-5 col-sm-4 col-xs-4 network_info" >{$access_campaign.username}</div>
                                <div class="col-md-2 col-sm-4 col-xs-4 text-right" >
                                    <span viewer_id="{$access_campaign.id}" class="glyphicon glyphicon-remove remove_network" ></span>
                                </div>
                            </div>
                            {/foreach}

<!--                            <div class="row network_row">-->
<!--                                <div class="col-md-5 col-sm-4 col-xs-4 network_info"><p>6034967969</p></div>-->
<!--                                <div class="col-md-5 col-sm-4 col-xs-4 network_info" id="networkemail"><p>hov@email.com</p></div>-->
<!--                                <div class="col-md-2 col-sm-4 col-xs-4 text-right">-->
<!--                                    <span class="fa fa-times remove_network"></span>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="row network_row">-->
<!--                                <div class="col-md-5 col-sm-4 col-xs-4 network_info"><p>6034967969</p></div>-->
<!--                                <div class="col-md-5 col-sm-4 col-xs-4 network_info" id="networkemail"><p>hov@email.com</p></div>-->
<!--                                <div class="col-md-2 col-sm-4 col-xs-4 text-right">-->
<!--                                    <span class="fa fa-times remove_network"></span>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="row network_row">-->
<!--                                <div class="col-md-5 col-sm-4 col-xs-4 network_info"><p>6034967969</p></div>-->
<!--                                <div class="col-md-5 col-sm-4 col-xs-4 network_info" id="networkemail"><p>hov@email.com</p></div>-->
<!--                                <div class="col-md-2 col-sm-4 col-xs-4 text-right">-->
<!--                                    <span class="fa fa-times remove_network"></span>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="row network_row">-->
<!--                                <div class="col-md-5 col-sm-4 col-xs-4 network_info"><p>6034967969</p></div>-->
<!--                                <div class="col-md-5 col-sm-4 col-xs-4 network_info" id="networkemail"><p>hov@email.com</p></div>-->
<!--                                <div class="col-md-2 col-sm-4 col-xs-4 text-right">-->
<!--                                    <span class="fa fa-times remove_network"></span>-->
<!--                                </div>-->
<!--                            </div>-->
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="container-fluid section-manage section-snippet">
    <div class="container">
        <div class="row">
            <div class="snippet-info">
                <h3>Campaign Retargeting & Tracking</h3>
            </div>
        </div>
        <div class="col-md-3">
            <select  id="campaigns-snippets" name="campaigns_snippets" class="campaigns-snippets" aria-required="true" aria-invalid="false" multiple>
                <option value="" selected="selected">All Campaigns</option>
                {foreach from=$campaigns item=campaign}
                    <option value="{$campaign['id']}">{$campaign['name']}</option>
                {/foreach}


            </select>
        </div>
        <div class="row generateSnippet-info">
            <button class="btn btn-info btn-generate-snippet">Generate Snippet</button>
        </div>

        <div id="snippet" class="snippet-background">
            <p>
                Please add the below Javascript snippet to your web page(s), directly before the closing &lt;/body&gt; tag.
            </p>
            &lt;script&gt; var prodata_user_id = {$user.id}<span class="campaign-id"></span>&lt;/script&gt;<br>
            &lt;script src="//reporting.prodata.media/v2/js/retargeting.js"&gt;&lt;/script&gt;
        </div>
    </div>
</section>

<!--<div class="theme-report-campaigne-row-wrap">-->
<!--    <div class="theme-container container-fluid">-->
<!--        <div class="theme-report-campaigne-schedule-row">-->



<!---->
<!--            <div class="section_head row manage_viewer_head" >-->
<!--                <div class="col-sm-6" >-->
<!--                    <h3>Manage access to the campaigns for viewer(s)</h3>-->
<!---->
<!---->
<!--                </div>-->
<!--                <div class="col-sm-6" >-->
<!--                    <button class="btn btn-info btn_manage_viewer" >Manage</button>-->
<!--                </div>-->
<!--            </div>-->
<!---->
<!--            <div class="manage_viewer_body" >-->
<!---->
<!--                    <div class="theme-report-row-wrap">-->
<!--                        <div class="theme-container container-fluid network_edit_container">-->
<!--                            <div class="row network_add_row" >-->
<!--                                <form id="manage_viewer_form">-->
<!---->
<!--                                    <div class="col-sm-4" >-->
<!--                                        <select name="campaign" class="form-control" >-->
<!--                                            {foreach from=$campaigns_information item=campaign_information}-->
<!--                                            <option value="{$campaign_information.id}" >{$campaign_information.name}</option>-->
<!--                                            {/foreach}-->
<!--                                        </select>-->
<!--                                    </div>-->
<!--                                    <div class="col-sm-4" >-->
<!--                                        <select name="viewer" class="form-control" >-->
<!--                                            {foreach from=$viewers item=viewer}-->
<!--                                            <option value="{$viewer.id}">{$viewer.username}</option>-->
<!--                                            {/foreach}-->
<!--                                        </select>-->
<!--                                    </div>-->
<!--                                </form>-->
<!--                                <div class="col-sm-4" >-->
<!--                                    <button id="btn_add_viewer" class="btn btn-success" >Add</button>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="campaign_list" >-->
<!--                                {foreach from=$access_campaigns item=access_campaign}-->
<!--                                <div class="row network_row" >-->
<!--                                    <div class="col-sm-4 network_info" >{$access_campaign.name}</div>-->
<!--                                    <div class="col-sm-4 network_info" >{$access_campaign.username}</div>-->
<!--                                    <div class="col-sm-4" >-->
<!--                                        <span viewer_id="{$access_campaign.id}" class="glyphicon glyphicon-remove remove_network" ></span>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                {/foreach}-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!---->
<!---->
<!--            </div>-->
            <!-- manage viewer end -->

<!--            {if !$linkedToFacebook}-->
<!--                <div class="col-sm-6" >-->
<!--                    <a class="btn btn-info" href="{$loginUrl}">Link Account to Facebook</a>-->
<!--                </div>-->
<!--                {else}-->
<!--                <div class="col-sm-6" >-->
<!--                    <button class="btn btn-info" type="button" disabled="disabled">You have already linked your account to Facebook</button>-->
<!--                    <a class="btn btn-info" href="{$fbUnlinkUrl}">Unlink Facebook Account</a>-->
<!--                </div>-->
<!--            {/if}-->
        </div>

    </div>
</div>

<script src="/v2/js/jquery-2.0.3.min.js"></script>
<!--Include all compiled plugins (below), or include individual files as needed-->
<script src="/v2/js/bootstrap.min.js"></script>

<script src="/v2/js/viewer/viewer.js"></script>
<script>
    var updateInfo = true;
    var updateCard = true;

    function checkLuhn(input) {
            var sum = 0;
            var numdigits = input.length;
            var parity = numdigits % 2;
            for(var i=0; i < numdigits; i++) {
                var digit = parseInt(input.charAt(i))
                if(i % 2 == parity) digit *= 2;
                if(digit > 9) digit -= 9;
                sum += digit;
            }
            return (sum % 10) == 0;
        }


    //Create Viewer
    $('.btn_create_viewer').on('click', function() {

         $(".createviewer-info-form").slideToggle();
//        $(".create_viewer_body").slideToggle();

    });

    $('#button_create_viewer').on('click', function(event) {
        var validate_create_viewer = false;
        if($('.check_repeat_viewer_pass').val() !== $( "input[name='viewer_pass']" ).val()){
            alert("Password does not match");
        }else if(($('.check_repeat_viewer_pass').val() !== '') && ($("input[name='viewer_email']").val() !== '') )
        {
            var validate_create_viewer = true;
            event.preventDefault();
        }



        if(validate_create_viewer == true) {

            $.ajax({
                url: "/v2/profile/create_viewer",
                type: "POST",
                dataType: "json",
                data:  $('#create_viewer_form').serialize(),
                success: function(data)	{

                    if(data.success) {
                        console.log(data)
                        $('.pass_alert_success').text(data.msg);
                        $('.pass_alert').hide();
                        $('.pass_alert_success').show();
                        $('.viewer_pass_input').val('');
                        $('.viewer_email').val('');
                        setTimeout(function() {

                            $('.pass_alert').hide();
                            $('.pass_alert_success').hide();
                            $(".create_viewer_body").hide();

                        }, 2000);


                    }
                    else {
                        console.log(data)
                        $('.pass_alert').text(data.msg);
                        $('.pass_alert_success').hide();
                        $('.pass_alert').show();
                        $('.viewer_pass_input').val('');


                        setTimeout(function() {

                            $('.pass_alert').hide();
                            $('.pass_alert_success').hide();

                        }, 2000);


                    }


                }
            });

        }
    });

	 //manage Viewer
    $('.btn_manage_viewer').on('click', function() {

//         $(".manage_viewer_body").slideToggle();
        $(".manageAccess-info-form").slideToggle();

    });


    //check valid card
    $('input[name="card_number"]').keyup(function() {

            var card = checkLuhn($(this).val());

            if(card) {
                $('.card_notice').hide();
                updateCard = true;
            }
            else {
                $('.card_notice').show();
                updateCard = false;
            }

        });


    //Update user info
    $('#btn_user_info_edit').on('click', function() {
        $(".user-info-form form .edit").slideToggle();
//        $("#btn_info_update").css('display', 'block');
        $(".user-info-form .form-group").slideToggle();
        $(".user-info-form #btn_info_update").slideToggle({
            step: function() {
                if ($(this).css('display') != 'block') {
                    $(this).css('display', 'block');
                }
            },
        });
    });

    //Open new pass block
    $('#btn_reset_pass').on('click', function() {
        $('.pass-info-form').slideToggle();
    });

    //Update Password
    $('#btn_update_pass').on('click', function(e) {

        e.preventDefault();
        var validate = true;
        $('.pass_input').each(function() {

            if($(this).val().length == 0) {
                validate = false;
                $('.pass_alert').text('Enter all Required fields');
                $('.pass_alert').show();
            }
            else if($(this).val().length < 4) {
                validate = false;
                $('.pass_alert').text('Password must be min 4 characters');
                $('.pass_alert').show();


            }
            else if( $('.new_pass').val() != $('.new_pass_repeat').val() ) {
                validate = false;
                $('.pass_alert').text("Passwords don't match");
                $('.pass_alert').show();
            }
            else {
                $('.pass_alert').hide();
            }

        });

        if(validate) {

            $.ajax({
                url: "/v2/profile/reset_password",
                type: "POST",
                dataType: "json",
                data:  $('#reset_pass_form').serialize(),
                success: function(data)	{

                    if(data.success) {

                        $('.pass_alert_success').text(data.msg);
                        $('.pass_alert').hide();
                        $('.pass_alert_success').show();
                        $('.pass_input').val('');

                        setTimeout(function() {

                            $('.pass_alert').hide();
                            $('.pass_alert_success').hide();

                        }, 1000);


                    }
                    else {

                        $('.pass_alert').text(data.msg);
                        $('.pass_alert_success').hide();
                        $('.pass_alert').show();
                        $('.pass_input').val('');

                        setTimeout(function() {

                            $('.pass_alert').hide();
                            $('.pass_alert_success').hide();

                        }, 1000);


                    }


                }
            });

        }

    });

    //Update Card
    $('.btn_edit_card').on('click', function() {

        $(".card-info-form form .edit").slideToggle();
        $(".card-info-form .form-group").slideToggle();
        $(".card-info-form #btn_edit_card").slideToggle({
            step: function() {
                if ($(this).css('display') != 'block') {
                    $(this).css('display', 'block');
                }
            },
        });


//        $(".user_card_info").slideToggle();
//        $(".user_card_edit").slideToggle();



    });

    //Check email
    $('input[name="email"]').on('blur', function() {

        var email = $(this).val();

        $.ajax({
            url: "/v2/profile/check_email",
            type: "POST",
            dataType: "json",
            data:  { email: email },
            success: function(data)	{

                if(!data.success) {

                    $('.email_notice').text(data.msg).show();
                    updateInfo = false;

                }
                else {

                    updateInfo = true;
                    $('.email_notice').hide();

                }


            }
        });


    });

    //Update user info click
    $('#btn_info_update').on('click', function() {

        if(!updateInfo) {
            return false;
        }

    });

    //Update Card click
    $('#btn_edit_card').on('click', function() {

        if(!updateCard) {
            return false;
        }

    });



    // Generate snippet

    $("#campaigns-snippets").change(function() {
        if ($(this).val() != "") {
            $(".campaign-id").html(", prodata_campaign_id = [" + $(this).val() + "];");
        } else {
            $(".campaign-id").html("");
        }

    });

    $(".btn-generate-snippet").click(function () {
        $(".snippet-background").slideDown();
        $('body,html').animate({ scrollTop: $('body').height()+200 }, 500);


    })

</script>


<!--            <div class="section_head row" >-->
<!--                <div class="col-sm-6" >-->
<!--                    <h3>User Info</h3>-->
<!--                </div>-->
<!--                <div class="col-sm-6" >-->
<!--                    <button id="btn_user_info_edit" class="btn btn-info" >Edit</button>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="user_info_block" >-->
<!--                <div class="user_info_text" >-->
<!--                    <label>First Name</label>-->
<!--                    <p>{$user.first_name} </p>-->
<!--                    <label>Last Name</label>-->
<!--                    <p>{$user.last_name}</p>-->
<!--                    <label>Email</label>-->
<!--                    <p>{$user.email}</p>-->
<!--                    <label>Company</label>-->
<!--                    <p>{$user.company}</p>-->
<!--                    <label>Address</label>-->
<!--                    <p>{$user.address}</p>-->
<!--                    <label>City</label>-->
<!--                    <p>{$user.city}</p>-->
<!--                    <label>State</label>-->
<!--                    <p>{$user.state}</p>-->
<!--                    <label>Zip Code</label>-->
<!--                    <p>{$user.zip_code}</p>-->
<!--                </div>-->
<!--                <div class="user_info_form no_display" >-->
<!--                    <form method="post" >-->
<!--                        <label>First Name</label>-->
<!--                        <input name="first_name" required class="form-control" type="text" value="{$user.first_name}" />-->
<!--                        <label>Last Name</label>-->
<!--                        <input name="last_name" required class="form-control" type="text" value="{$user.last_name}" />-->
<!--                        <label>Email</label>-->
<!--                        <input name="email" required class="form-control" type="email" value="{$user.email}" />-->
<!--                        <p class="alert alert-danger no_display email_notice" ></p>-->
<!--                        <label>Company</label>-->
<!--                        <input name="company" required class="form-control" type="text" value="{$user.company}" />-->
<!--                        <label>Address</label>-->
<!--                        <input name="address" required class="form-control" type="text" value="{$user.address}" />-->
<!--                        <label>City</label>-->
<!--                        <input name="city" required class="form-control" type="text" value="{$user.city}" />-->
<!--                        <label>State</label>-->
<!--                        <input name="state" required class="form-control" type="text" value="{$user.state}" />-->
<!--                        <label>Zip Code</label>-->
<!--                        <input name="zip_code" required class="form-control" type="text" value="{$user.zip_code}" />-->
<!--                        <input id="btn_info_update" name="info_update" type="submit"  class="btn btn-success" value="Update" >-->
<!--                    </form>-->
<!--                </div>-->
<!--            </div>-->





<!--            <div class="section_head row" >-->
<!--                <div class="col-sm-6" >-->
<!--                    <h3>Card Info</h3>-->
<!--                </div>-->
<!--                <div class="col-sm-6" >-->
<!--                    <button class="btn btn-info btn_edit_card" >Edit</button>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="card_info_block" >-->
<!--                <div class="user_card_info" >-->
<!--                    <label>Card Number</label>-->
<!--                    <p>{$user.card_number} </p>-->
<!--                    <label>CVV</label>-->
<!--                    <p>{$user.card_cvv}</p>-->
<!--                    <label>Exp. Year</label>-->
<!--                    <p>{$user.card_exp_year}</p>-->
<!--                    <label>Exp. Month</label>-->
<!--                    <p>{$months[$user.card_exp_month - 1]}</p>-->
<!--                </div>-->
<!--                <div class="user_card_edit no_display" >-->
<!--                    <form method="post" >-->
<!--                        <label>Card Number</label>-->
<!--                        <input class="form-control" type="text" value="{$user.card_number}" name="card_number" />-->
<!--                        <p class="alert alert-danger no_display card_notice" >enter valid card</p>-->
<!--                        <label>CVV</label>-->
<!--                        <input class="form-control" type="text" value="{$user.card_cvv}" name="card_cvv" />-->
<!--                        <label>Exp. Year</label>-->
<!--                        <select class="form-control" name="exp_year" >-->
<!--                            {for $i = date('Y') to date('Y')+15}-->
<!--                            <option {if $user.card_exp_year == $i} selected {/if} value='{$i}'>{$i}</option>-->
<!--                            {/for}-->
<!--                        </select>-->
<!--                        <label>Exp. Month</label>-->
<!--                        <select name="exp_month" class="form-control" >-->
<!--                            <option value="1" {if $user.card_exp_month == 1} selected {/if} >Jan</option>-->
<!--                            <option value="2" {if $user.card_exp_month == 2} selected {/if} >Feb</option>-->
<!--                            <option value="3" {if $user.card_exp_month == 3} selected {/if} >Mar</option>-->
<!--                            <option value="4" {if $user.card_exp_month == 4} selected {/if} >Apr</option>-->
<!--                            <option value="5" {if $user.card_exp_month == 5} selected {/if} >May</option>-->
<!--                            <option value="6" {if $user.card_exp_month == 6} selected {/if} >June</option>-->
<!--                            <option value="7" {if $user.card_exp_month == 7} selected {/if} >July</option>-->
<!--                            <option value="8" {if $user.card_exp_month == 8} selected {/if} >Aug</option>-->
<!--                            <option value="9" {if $user.card_exp_month == 9} selected {/if} >Sept</option>-->
<!--                            <option value="10" {if $user.card_exp_month == 10} selected {/if} >Oct</option>-->
<!--                            <option value="11" {if $user.card_exp_month == 11} selected {/if} >Nov</option>-->
<!--                            <option value="12" {if $user.card_exp_month == 12} selected {/if} >Dec</option>-->
<!--                        </select>-->
<!--                        <input id="btn_edit_card" type="submit" name="edit_card" class="btn btn-success" value="Update" >-->
<!--                    </form>-->
<!--                </div>-->
<!---->
<!--            </div>-->

<!--            <div class="section_head row" >-->
<!--                <div class="col-sm-6" >-->
<!--                    <h3>Password</h3>-->
<!--                </div>-->
<!--                <div class="col-sm-6" >-->
<!--                    <button id="btn_reset_pass" class="btn btn-info" >Reset</button>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="reset_pass_block no_display" >-->
<!--                <form method="post" id="reset_pass_form" >-->
<!--                    <input  type="hidden" name="email" value="{$user.email}" />-->
<!--                    <input type="password" class="form-control pass_input" name="old_pass" placeholder="Old Password" />-->
<!--                    <input type="password" class="form-control pass_input new_pass" name="new_pass" placeholder="New Password" />-->
<!--                    <input type="password" class="form-control pass_input new_pass_repeat" name="repeat_new_pass" placeholder="Repeat New Password" />-->
<!--                    <button id="btn_update_pass" class="btn btn-success" >Update</button>-->
<!--                </form>-->
<!--                <div class="alert alert-danger no_display pass_alert" ></div>-->
<!--                <div class="alert alert-success no_display pass_alert_success" ></div>-->
<!--            </div>-->
<!-- create viewer start -->
<!--            <div class="section_head row create_viewer_head" >-->
<!--                <div class="col-sm-6" >-->
<!--                    <h3>Create Viewer</h3>-->
<!--                </div>-->
<!--                <div class="col-sm-6" >-->
<!--                    <button class="btn btn-info btn_create_viewer" >Create</button>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="section_head create_viewer_body" >-->
<!--                <form method="post" id="create_viewer_form" >-->
<!--                    <input type="text" class="form-control" name="viewer_name" placeholder="Viewer Name" required="required"/>-->
<!--                    <input type="email" class="form-control viewer_email_input" name="viewer_email" placeholder="Viewer Email" required="required"/>-->
<!--                    <input type="password" class="form-control viewer_pass_input" name="viewer_pass" placeholder="Viewer Password" required="required" minlength="6"  maxlength="20"/>-->
<!--                    <input type="password" class="form-control viewer_pass_input check_repeat_viewer_pass" name="repeat_viewer_pass" placeholder="Repeat Viewer Password" required="required" minlength="6" maxlength="20" />-->
<!--                    <button id="button_create_viewer" class="btn btn-success" name="create_viewer" />Create</button>-->
<!--                </form>-->
<!---->
<!--                <div class="alert alert-danger no_display pass_alert" ></div>-->
<!--                <div class="alert alert-success no_display pass_alert_success" ></div>-->
<!--            </div>-->
<!-- create viewer end -->



<!-- manage viewer start -->

{include file="v2/sections/footer.php"}
</body>
</html>
