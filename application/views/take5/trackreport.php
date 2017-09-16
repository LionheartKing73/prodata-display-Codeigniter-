{include file="campclick/sections/header.php"}

    <div class="container">

	    <div class="alert alert-error" id="err_bof" style="display:none;">
	    	<a class="close" data-dismiss="alert">X</a>
	    	<strong id="err_bof_message"></strong>
	    </div>

	    <div class="alert alert-success" id="success_bof" style="display:none;">
	    	<a class="close" data-dismiss="alert">X</a>
	    	<strong id="success_bof_message"></strong>
	    </div>

      <!-- Example row of columns -->
      <div class="row">
        <div class="span12">
        	<h2>Campaign Tracking Report</h2>
        	
        	<ul class="nav nav-tabs" id="campaignTabs">
        		<li class="active"><a href="#in-progress" data-toggle="tab">In-Progress</a></li>
        		<li><a href="#completed" data-toggle="tab">Completed</a></li>
        	</ul>
        	
        	<div class="tab-content">
        	
        		<div class="tab-pane active" id="in-progress">
        			<table class="table table-bordered table-striped" id="mytable-inprogress">
		        	<thead>
		        		<tr>
		        			<th>I/O #</th>
		        			<th>Campaign Name</th>
		        			<th>Random URL</th>
		        			<th>Date Created</th>
		        			<th>&nbsp;</th>
		        		</tr>
		        	</thead>
		        	<tbody>
		        		{foreach from=$campaigns.inprogress item=c}
			        		<tr id="io_{$c.io}">
			        			<td>{$c.io}</td>
			        			<td>{$c.name}</td>
			        			<td>http://{$domain_name}/r/{$c.io}</td>
			        			<td>{$c.create_date|date_format:"%Y-%m-%d"}</td>
			        			<td><a href='{$base_url}take5/trackingreport/{$c.io}'><i class='icon-download-alt'></i></a> {* &nbsp;|&nbsp; <a href="{$base_url}campclick/report/{$c.io}"><i class="icon-eye-open"></i></a> &nbsp;|&nbsp; <a href='#' class='archive-campaign' data-io='{$c.io}'><i class='icon-lock'></i></a> *}</td>
			        		</tr>
		        		{/foreach}
		        	</tbody>
		        	</table>        			
        		</div>
        		
        		<div class="tab-pane" id="completed">
        			<table class="table table-bordered table-striped" id="mytable-completed">
		        	<thead>
		        		<tr>
		        			<th>I/O #</th>
		        			<th>Campaign Name</th>
		        			<th>Random URL</th>
		        			<th>Date Created</th>
		        			<th>&nbsp;</th>
		        		</tr>
		        	</thead>
		        	<tbody>
		        		{foreach from=$campaigns.completed item=c}
			        		<tr id="io_{$c.io}">
			        			<td>{$c.io}</td>
			        			<td>{$c.name}</td>
			        			<td>http://{$domain_name}/r/{$c.io}</td>
			        			<td>{$c.create_date|date_format:"%Y-%m-%d"}</td>
			        			<td><a href='{$base_url}take5/trackingreport/{$c.io}'><i class='icon-download-alt'></i></a> {* &nbsp;|&nbsp; <a href="{$base_url}campclick/report/{$c.io}"><i class="icon-eye-open"></i></a> &nbsp;|&nbsp; <a href='#' class='archive-campaign' data-io='{$c.io}'><i class='icon-lock'></i></a> *}</td>
			        		</tr>
		        		{/foreach}
		        	</tbody>
		        	</table>
        		</div>
        	
        	</div>
        </div>
      </div>
      
      <script>
      	$(document).ready(function(){
			$(".archive-campaign").click(function(){
				var io = $(this).data("io");
				if (confirm("Are you sure you want to move this campaign to the archive?"))	{
					$.ajax({
						url: "/campclick/archive/" + io,
						dataType: "json",
						success: function(msg)	{
							if (msg.status == "SUCCESS")	{
								$("#io_" + io).hide();
							}
						}
					});
				}
			});

			$(".campaign-list").addClass("active");

			$("#mytable").tablesorter();
       	});
      </script>

{include file="campclick/sections/footer.php"}
