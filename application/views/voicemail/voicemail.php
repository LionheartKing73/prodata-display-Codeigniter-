<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Village of Franklin Park - Voicemail Messages</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Jason Korkin, Safe Data Technologies, 1-877-502-6245">
	<meta http-equiv="refresh" content="600">

    <link href="/static/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

 	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
  </head>

  <body>
  	<div class="navbar">
  		<div class="navbar-inner">
  			<a class="brand" href="#"><img src="http://www.villageoffranklinpark.com/cms/images/logo.png"></a>
  			<h1 class="pull-right" style="padding-top:20px">Voicemail Messages</h1>
  		</div>
  	</div>
    <div class="container">
      
      <div class="row">
      		{if isset($voicemail)}
        	{foreach from=$voicemail item=v}
        		{if $v.new_msg >= 5}
        			{assign color "important"}
        		{/if}
        		{if $v.new_msg >= 2 && $v.new_msg < 5}
        			{assign color "warning"}
        		{/if}
        		{if $v.new_msg >= 0 && $v.new_msg < 2}
        			{assign color "info"}
        		{/if}

	        	<div class="well span2 row">
	        		<span class='alert alert-{$color}'>{$v.extension}</span>
	        		<span class="badge badge-{$color} pull-right"><i class='icon-envelope icon-white'></i> {$v.new_msg}</span>
	        		<br/><br/>
	        		<h6>Last Message Received: {$v.updated_date}</h6>
	        	</div>
			{foreachelse}
				<div><h4 style="text-align:center">No Voicemail Extensions</h4></div>
        	{/foreach}
        	{else}
        		<div><h4 style="text-align:center">Database Access Failure</h4></div>
        	{/if}
         </div>
      </div>
	
  	  <div class="navbar navbar-fixed-bottom">
      	<footer>
        	<p align="center">Copyright &copy; 2013, <a href="http://www.safedatatech.com">Safe Data Technologies LLC</a>.</p>
      	</footer>
	  </div>
	
    </div> <!-- /container -->

    <script src="/static/js/bootstrap.js"></script>
  </body>
</html>