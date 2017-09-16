<?php include('common/header-login.php'); ?>
<div class="container vertical-center">
    <div class="form-wrapper col-md-6 col-md-offset-3">
        <div class="logo-main">
            <?php if(!empty($domain_logo)): ?>
                <img src="/v2/images/domain_logos/<?=$domain_logo ?>">
            <?php else: ?>
                <img src="/v2/images/login-icons/logo-main.png">
            <?php endif; ?>
        </div>
        <div <?php if($active_button_color){ ?> style="background: <?=$active_button_color ?>" <?php } ?> class="form-header">
            Log in to your account
        </div>
        <form class='login-form' action="<?php echo base_url(); ?>auth/login" method="post">
            <div class="input-group form-field">
                <span <?php if($active_button_color){ ?> style="background-color: <?=$active_button_color ?>" <?php } ?> class="usr-span"></span>
                <input type="text" name="identity" value="" id="identity"                                         placeholder="Username or Email" class="usr">
            </div>
            <div class="input-group form-field">
                <span <?php if($active_button_color){ ?> style="background-color: <?=$active_button_color ?>" <?php } ?> class="pwd-span"></span>
                <input type="password" name="password" value="" id="password"                                         placeholder="Password" class="pwd">
            </div>
            <div>
                <input <?php if($active_button_color){ ?> style="background-color: <?=$active_button_color ?>" <?php } ?> type="submit" value="LOG IN" class="login-btn">
            </div>
            <div>
                <input <?php if($active_button_color){ ?> style="border-color: <?=$active_button_color ?>" <?php } ?> type="checkbox" name="remember" value="1" id="remember"                                         >
                <label>Remember Me</label>
                <a href="forgot_password">Forgot Password</a>
            </div>
        </form>
    </div>
</div>

<div class='container col-md-6' style='text-align:center;'>
	<A href='http://www.prodata.media'>ProData Media</a> - &copy; 2017, ProDataFeed LLC. All Rights Reserved. <a href='tel://18557767020'>1-855-776-7020</a>
</div>


<?php if($active_button_color) ?>
<style>
</style>

</body>
</html>

