<?php include('common/header.php'); ?>
<div class="container">
<div class="row">
<div class="span12">
<div class="inner-container" style="margin:0 auto">


<?php if($auth_success): ?>
    <p class="alert alert-<?=$auth_success['type'] ?>" ><?=$auth_success['msg'] ?></p>
<?php endif ?>

<h1><?php echo lang('login_heading'); ?></h1>
<p><?php echo lang('login_subheading');?></p>

<div id="infoMessage"><?php echo $message ?></div>

<?php echo form_open("http://{$hostname}/auth/login");?>

  <p>
    <?php  lang('login_identity_label', 'indentity'); ?>
    <?php echo form_input($identity);?>
  </p>

  <p>
    <?php echo lang('login_password_label', 'password');?>
    <?php echo form_input($password);?>
  </p>

  <p>
    <?php echo lang('login_remember_label', 'remember');?>
    <?php echo form_checkbox('remember', '1', FALSE, 'id="remember"');?>
  </p>

  <p><?php echo form_submit('submit', lang('login_submit_btn'));?></p>

<?php echo form_close();?>

<p><a href="forgot_password"><?php echo lang('login_forgot_password');?></a> <a href="signup">Sign Up</a></p>

</div>
</div> <!-- END SPAN12 -->
</div> <!-- END row -->
</div> <!-- END container -->
<?php include('common/footer.php'); ?>
