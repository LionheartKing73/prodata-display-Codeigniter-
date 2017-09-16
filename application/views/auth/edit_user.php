<?php //include('common/header_loggedin.php'); ?>
<!--<h1>--><?php //echo lang('create_user_heading');?><!--</h1>-->
<!--<p>--><?php //echo lang('create_user_subheading');?><!--</p>-->
<?php include('common/header.php'); ?>

<div class="reg-container container">
    <div class="row">
        <div class="">
            <div class="inner-container" style="margin:0 auto">
                <?php if($message): ?>
                    <p class="alert ?>" ><?= $message ?></p>
                <?php endif ?>
                <div class="logo">
                    <img src="../../v2/images/login-icons/logo-main.png" class="img-responsive">

                </div>
                <div class="content sign-upContent">
                    <div class="padding col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h3 class="text-uppercase text-center">Create User</h3>
                            </div>
                            <div class="panel-footer">
<!--                                <div id="infoMessage">--><?php //echo $message ?><!--</div>-->
                                <?php echo form_open("http://{$hostname}/auth/edit_user/$user->id"); ?>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <p>
                                            <!--                                        <label for="first_name">First Name:</label>-->
                                            <input required type="text" class="form-control" value="<?= $user->first_name ?>" name="first_name" id="inputFName" placeholder="First Name:">
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <p>
                                            <!--                                        <label for="last_name">Last Name:</label>-->
                                            <input required type="text" value="<?= $user->last_name ?>" name="last_name" class="form-control" id="inputLName" placeholder="Last Name:">
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <p>
                                            <!--                                        <label for="company">Company:</label>-->
                                            <input required type="text" value="<?= $user->company ?>" name="company" class="form-control" id="company" placeholder="Company:">
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <p>
                                            <!--                                        <label for="address">Address:</label>-->
                                            <input required type="text" value="<?= $user->address ?>" name="address" class="form-control" id="address" placeholder="Address:">
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <p>
                                            <!--                                            <label for="city">City:</label>-->
                                            <input required type="text" value="<?= $user->city ?>" name="city" class="form-control" id="city" placeholder="City:">
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <p>
                                            <!--                                        <label for="state">State:</label>-->
                                            <input required type="text" value="<?= $user->state ?>" name="state" class="form-control" id="state" placeholder="State:">
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <p>
                                            <!--                                        <label for="zip">Zip:</label>-->
                                            <input required type="text" value="<?= $user->zip_code ?>" name="zip" class="form-control" id="zip" placeholder="Zip:">
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <p>
                                            <!--                                        <label for="phone">Phone:</label>-->
                                            <input required type="text" value="<?= $user->phone ?>" name="phone" class="form-control" id="phone" placeholder="Phone:">
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <p>
                                            <!--                                        <label for="password">Email:</label>-->
                                            <input required type="email" value="<?= $user->email ?>" name="email" class="form-control" id="email" placeholder="Email:">
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <p>
                                            <!--                                        <label for="password">Password:</label>-->
                                            <input required type="password" value="<?= $user->password ?>" name="password" class="form-control" id="password" placeholder="Password:">
                                        </p
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <!--                                    <label for="card">Card Number:</label>-->
                                    <input required type="text" value="<?= $user->card_number ?>" name="card" class="form-control" id="cardNumber" placeholder="Card Number:">
                                    <p class="alert alert-danger no_display card_notice" >enter valid card</p>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <!--                                    <label for="exp_month">Expiration Month:</label>-->
                                    <select name="exp_month" class="form-control year" >
                                        <option>Expiration Month:</option>
                                        <option value='1' <?php if ($user->card_exp_month == '1') { echo 'selected'; } ?> >Jan.</option>
                                        <option value='2' <?php if ($user->card_exp_month == '2') { echo 'selected'; } ?> >Feb.</option>
                                        <option value='3' <?php if ($user->card_exp_month == '3') { echo 'selected'; } ?> >Mar.</option>
                                        <option value='4' <?php if ($user->card_exp_month == '4') { echo 'selected'; } ?> >Apr.</option>
                                        <option value='5' <?php if ($user->card_exp_month == '5') { echo 'selected'; } ?> >May</option>
                                        <option value='6' <?php if ($user->card_exp_month == '6') { echo 'selected'; } ?> >June</option>
                                        <option value='7' <?php if ($user->card_exp_month == '7') { echo 'selected'; } ?> >July</option>
                                        <option value='8' <?php if ($user->card_exp_month == '8') { echo 'selected'; } ?> >Aug.</option>
                                        <option value='9' <?php if ($user->card_exp_month == '9') { echo 'selected'; } ?> >Sept.</option>
                                        <option value='10' <?php if ($user->card_exp_month == '10') { echo 'selected'; } ?> >Oct.</option>
                                        <option value='11' <?php if ($user->card_exp_month == '11') { echo 'selected'; } ?> >Nov.</option>
                                        <option value='12' <?php if ($user->card_exp_month == '12') { echo 'selected'; } ?>>Dec.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <!--                                    <label for="exp_year">Expiration Year:</label>-->
                                    <select name="exp_year" class="form-control year" >
                                        <option>Expiration Year:</option>
                                        <?php for($i = date('Y'); $i <= date('Y') + 15; $i++ ):   ?>
                                            <?php echo $i; ?>
                                            <option value='<?=$i ?>' <?php if($i == $user->card_exp_year) echo 'selected' ?>> <?=$i ?> </option>
                                        <?php endfor ?>
                                    </select>
                                </div>
                            </div>

<!--                            <div class="col-md-6 col-sm-6 col-xs-12">-->
<!--                                <div class="padding-margin-none form-group">-->
<!--                                    <input id="send-email" type="checkbox" value="1" name="send-email" class="form-control" checked>-->
<!--                                    <label for="send-email">Send email notification </label>-->
<!--                                </div>-->
<!--                                <div class="form-group">-->
<!--                                    <input id="create-quickbooks-account" type="checkbox" value="1" name="create-quickbooks-account" class="form-control" checked>-->
<!--                                    <label for="create-quickbooks-account">Create account on Quickbooks</label>-->
<!--                                </div>-->
<!--                            </div>-->
                            <!--                    <p>--><?php //echo form_submit('submit', 'Create'); ?><!--</p>-->
                            <div class="col-md-12">
                                <button type="submit" name="create" class="btn btn-default btn-lg text-uppercase">Update </button>

                                <?php echo form_hidden('id', $user->id);?>
                                <?php echo form_hidden($csrf); ?>

                                <?php echo form_close(); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- END SPAN12 -->
</div> <!-- END row -->
</div> <!-- END container -->
<?php include('common/footer.php'); ?>
<style>
    .no_display {display: none;}
    .card_notice {width: 206px;}
</style>

<script>

    var cardValidate = false;
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


    $('input[name="card"]').keyup( function() {

        var card = checkLuhn($(this).val());

        if(card) {
            $('.card_notice').hide();
            cardValidate = true;
        }
        else {
            $('.card_notice').show();
            cardValidate = false;
        }

    });

    $('input[name="submit"]').on('click', function() {

        if(!cardValidate) {
            return false;
        }

    });

</script>
