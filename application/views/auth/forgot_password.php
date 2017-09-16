<?php include('common/header.php'); ?>
<div class="container">
<div class="row">
<div class="span12">
<div class="inner-container" style="margin:0 auto">
<h1><?php echo lang('forgot_password_heading');?></h1>
<p><?php echo sprintf(lang('forgot_password_subheading'), $identity_label);?></p>

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("auth/forgot_password");?>

      <p>
      	<label for="email"><?php echo sprintf(lang('forgot_password_email_label'), $identity_label);?></label> <br />
      	<?php echo form_input($email);?>
      </p>

      <p><?php echo form_submit('submit', lang('forgot_password_submit_btn'));?></p>

<?php echo form_close();?>
</div>
</div> <!-- END SPAN12 -->
</div> <!-- END row -->
</div> <!-- END container -->
<?php include('common/footer.php'); ?>