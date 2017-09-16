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
        	<h2>Campaign List</h2>

        	<ul class="nav nav-tabs" id="campaignTabs">
        		<li class="active"><a href="#in-progress" data-toggle="tab">In-Progress</a></li>
        		<li><a href="#completed" data-toggle="tab">Completed</a></li>
        		<li><a href="#scheduled" data-toggle="tab">Scheduled</a></li>
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
			        			<td>{$c.name} {if $c.fire_open_pixel == "Y"}<i class='icon-star-empty'></i>{/if} {if $c.is_geo_expanded == "Y"}<i class='icon-map-marker'></i>{/if}</td>
			        			<td>http://{$domain_name}/r/{$c.io}</td>
			        			<td>{$c.create_date|date_format:"%Y-%m-%d"}</td>
			        			<td><a href="{$base_url}campclick/report/{$c.io}"><i class="icon-eye-open"></i></a> &nbsp;|&nbsp; <a href="{$base_url}campclick/map/{$c.io}"><i class="icon-globe"></i></a> &nbsp;|&nbsp; <a href='#' class='archive-campaign' data-io='{$c.io}'><i class='icon-lock'></i></a> &nbsp;|&nbsp; <a href='#' class='clone-campaign' data-io='{$c.io}' data-name='{$c.name}'><i class='icon-random'></i></a></td>
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
			        			<td><a href="{$base_url}campclick/report/{$c.io}"><i class="icon-eye-open"></i></a> &nbsp;|&nbsp; <a href="{$base_url}campclick/map/{$c.io}"><i class="icon-globe"></i></a> &nbsp;|&nbsp; <a href='#' class='archive-campaign' data-io='{$c.io}'><i class='icon-lock'></i></a> &nbsp;|&nbsp; <a href='#' class='clone-campaign' data-io='{$c.io}' data-name='{$c.name}'><i class='icon-random'></i></a></td>
			        		</tr>
		        		{/foreach}
		        	</tbody>
		        	</table>
        		</div>

        		<div class="tab-pane" id="scheduled">
        			<table class="table table-bordered table-striped" id="mytable-scheduled">
		        	<thead>
		        		<tr>
		        			<th>I/O #</th>
		        			<th>Campaign Name</th>
		        			<th>Random URL</th>
		        			<th>Scheduled Date</th>
		        			<th>Ordered Clicks</th>
		        			<th>&nbsp;</th>
		        		</tr>
		        	</thead>
		        	<tbody>
		        		{foreach from=$campaigns.scheduled item=c}
			        		<tr id="io_{$c.io}">
			        			<td>{$c.io}</td>
			        			<td>{$c.name}</td>
			        			<td>http://{$domain_name}/r/{$c.io}</td>
			        			<td>{$c.campaign_start_datetime|date_format:"%Y-%m-%d @ ~%H:%M"}</td>
			        			<td>{$c.max_clicks}</td>
			        			<td><a href="{$base_url}campclick/report/{$c.io}"><i class="icon-eye-open"></i></a> &nbsp;|&nbsp; <a href="{$base_url}campclick/map/{$c.io}"><i class="icon-globe"></i></a> &nbsp;|&nbsp; <a href='#' class='archive-campaign' data-io='{$c.io}'><i class='icon-lock'></i></a> &nbsp;|&nbsp; <a href='#' class='clone-campaign' data-io='{$c.io}' data-name='{$c.name}'><i class='icon-random'></i></a></td>
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

			$(".clone-campaign").click(function(){
				var io = $(this).data("io");
				var name = $(this).data("name");


				$("#clone-io").html(io);

				// preload with old values to edit
				$("#clone-io-name").val(name);
				$("#clone-io-new").val(io);

				$('#clone-modal').modal({
					backdrop: true,
					keyboard: false,
					show: true
				});
			});

			$("#clone-io-save").click(function(){
				var is_ok = false;
				var old_io = $("#clone-io").html();

				if ($("#clone-io-name").val() == "")    {
					$("#missing_io_name_alert").show();
				} else {
					is_ok = true;
				}

				if ($("#clone-io-new").val() == "")    {
					$("#missing_io_alert").show();
				} else {
					is_ok = true;
				}

				if (is_ok === true)  {
					is_ok = false; // reset for next set of tests.

					$.ajax({
						url: "/campclick/check_io/" + $("#clone-io-new").val(),
						dataType: "json",
						success: function(msg)	{
							if (msg.status == "ERROR")	{
								$("#clone-io-new").addClass("error");
								$("#duplicate_io_alert").show();
								is_ok = false;
							} else {
								$("#duplicate_io_alert").hide();

								if(confirm("Are you sure you wish to clone this IO?")) {
									$.ajax({
										url: "/campclick/clone_io",
										type: "POST",
										dataType: "json",
										data: {
											'old_io': old_io,
											'new_io': $("#clone-io-new").val(),
											'campaign_name': $("#clone-io-name").val()
										},
										success: function(msg)  {
											if (msg.status == "SUCCESS")   {
												//document.location.href='/campclick/report/' + $("#clone-io-new").val();
												document.location.reload();
											} else {
												alert("Error processing request.");
											}
										}
									});
								}
							}
						}
					});
				}
			});

			$("#clone-io-new").keypress(function(key){
				if (key.charCode == 32)	{
					$(this).val($(this).val() + "-");
					return false;
				}

				if (key.charCode == 0)	{
					return true;
				}

				if((key.charCode < 48 || key.charCode > 57) && (key.charCode < 97 || key.charCode > 122) && (key.charCode < 65 || key.charCode > 90) && (key.charCode != 45)) return false;
			});


			$(".campaign-list").addClass("active");

			$("#mytable").tablesorter();
       	});
      </script>


{include file="campclick/sections/modal.php"}
{include file="campclick/sections/footer.php"}
