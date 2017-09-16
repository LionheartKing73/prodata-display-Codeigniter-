<?php include('header.php'); ?>

<div class="container">
<div class="row">
<div class="span2"><h1><?php echo "Menu";?></h1>
<?php  if($this->ion_auth->is_admin()){ ?>
    <ul class="nav nav-tabs nav-stacked">
   <li><a href="<?php echo $base_url ?>auth">All Users</a></li>
<!--   <li><a href="<?php echo $base_url ?>groups">All Users Groups</a></li>-->
   <li><a href="<?php echo $base_url ?>auth/create_user">Create New User</a></li>
   <li><a href="<?php echo $base_url ?>auth/create_group">Create New User Group</a></li>
    </ul>
    
  <br/><br/>
 <?php } ?> 
      <ul class="nav nav-tabs nav-stacked">
   <li><a href="<?php echo $base_url ?>auth/change_password">Change password</a></li>
   <li><a href="<?php echo $base_url ?>auth/logout">Logout</a></li>
    </ul>  
</div>
<div class="span10">