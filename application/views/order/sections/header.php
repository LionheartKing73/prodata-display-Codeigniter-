<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Click Meter Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="{$base_url}static/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <link href="{$base_url}static/css/custom-theme/jquery-ui-1.8.20.custom.css" rel="stylesheet">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">Click Meter Management</a>
          {if $show_top_menu}
          <div class="nav-collapse">
            <ul class="nav">
            
           		{if $manage_users}<li class="active"><a href="{$base_url}auth/index">Manage Users</a></li>{/if}
              	<li class=""><a href="{$base_url}campclick/index">List Campaigns</a></li>
              	<li class=""><a href="{$base_url}order/index">New Order</a></li>
                <li><a href="{$base_url}auth/change_password">Change password</a></li>
                <li><a href="{$base_url}auth/logout">Logout</a></li>
            </ul>
            
          </div><!--/.nav-collapse -->
          {/if}
        </div>
      </div>
    </div>
    <!--  end header -->
