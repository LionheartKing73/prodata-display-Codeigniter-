<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>ProDataFeed - Advertiser Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="/static/bootstrap/css/bootstrap.css" rel="stylesheet">
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

    <link href="/static/css/custom-theme/jquery-ui-1.8.20.custom.css" rel="stylesheet">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
 	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
 	

<!--

    remove
<script src="/static/js/jquery.jstree.js"></script>

-->
 	<script src="/static/js/jquery.tablesorter.js"></script>
 	
 	<link href="/static/css/datetime-picker.css" rel="stylesheet">
 	<script src="/static/js/datetime-picker.jquery.js"></script>

  </head>

  <body>

	<style>
	th.header	{
		background-image: url(/static/img/small.gif);
		cursor: pointer;
		font-weight: bold;
		background-repeat: no-repeat;
		background-position: center left;
		padding-left: 20px;
		border-right: 1px solid #dad9c7;
	}
	
	th.headerSortUp { 
	    background-image: url(/static/img/asc.gif); 
	    background-color: #dddddd; 
	} 
	
	th.headerSortDown { 
	    background-image: url(/static/img/desc.gif); 
	    background-color: #dddddd; 
	}
	</style>

{*	{include file="campclick/sections/navtree.php"} *}
<!--REMOVE
    {if ! $take5_user}
        {include file="rtb/advertisement.php"}
    {/if}
  -->  

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#"><img src="{if $logo == ""}http://prodatafeed.com/images/logo.png{else}{$logo}{/if}" style="height:36px" /></a>
          {if $show_top_menu}
          <div class="nav-collapse">
            <ul class="nav">
           		<li class="campaign dropdown">
           			<a class="dropdown-toggle" data-toggle="dropdown" href="#">Campaigns <b class="caret"></b></a>
           			<ul class="dropdown-menu">
           				{* <li><a href="{$base_url}campclick/bof">Create Traffic Order</a></li> *}
           				{* <li><a href="{$base_url}campclick/campcreate">HTML Creative Link Processor</a></li> *}
           				<li><a href="{$base_url}take5/newcampaign">Create New Campaign</a></li>
           				
           				{if ! $take5_user}
               				<li><a href="{$base_url}adword/index">Create Display Ad Campaign</a></li>
           				{/if}
           				<li><a href="{$base_url}take5/queue">Approval Queue</a></li>
		              	<li class="campaign-list"><a href="{$base_url}campclick/index">List Campaigns</a></li>
           			</ul>
           		</li>
           		<li class="reports dropdown">
           			<a class="dropdown-toggle" data-toggle="dropdown" href="#">Reporting <b class="caret"></b></a>
           			<ul class="dropdown-menu">
		              	<li class="campaign-summary"><a href="{$base_url}campclick/summary">Campaign Summary Report</a></li>
		              	<li class="campaign-fulfillment"><a href="{$base_url}campclick/fulfillment">Fulfillment Report</a></li>
		              	<li class="campaign-fulfillment"><a href="{$base_url}campclick/schedule">Scheduled Campaigns</a></li>
		              	<li class="campaign-fulfillment"><a href="{$base_url}displayAdList">Display Ad Reporting</a></li>
		              	{if $take5_user}
		              	   <li class="campaign-tracking"><a href="{$base_url}take5/tracking">Tracking Reports</a></li>
		              	{/if}
	              	</ul>
           		</li>
           		<li class="admin dropdown">
           			<a class="dropdown-toggle" data-toggle="dropdown" href="#">Admin Tools <b class="caret"></b></a>
           			<ul class="dropdown-menu">
           				<li class="admin-domains"><a href="{$base_url}campclick/domains">Domain Management</a></li>
           				<li class="admin-vendors"><a href="{$base_url}campclick/vendors">Vendor Management</a></li>
		           		{if $manage_users}<li class="manage-users"><a href="{$base_url}auth/index">Manage Users</a></li>{/if} 				
           			</ul>
           		</li>
                <li class="change-password"><a href="{$base_url}auth/change_password">Change password</a></li>
                <li class="logout"><a href="{$base_url}auth/logout">Logout</a></li>
            </ul>
            
          </div><!--/.nav-collapse -->
          {/if}
        </div>
      </div>
    </div>
    <!--  end header -->
