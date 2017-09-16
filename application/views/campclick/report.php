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
          <div class="row" >
           <div class="span9"> &nbsp;
           </div>
            <div class="span3" style="text-align: right">    
                
                
           </div>
        </div>
      <div class="row">
        <div class="span2">
        	<h2>I/O #: {$io}</h2>
        	<br/>
        	<h5>Campaign Report</h5>
        	<h6>Ordered Qty: {$campaign.max_clicks}</h6>
        	<h6><a href='#myModal' class='show-modal' data-io="{$io}" data-modaltype="create_link">Create New Link</a></h6>
        	<h6><a href='#myModal' class='show-modal' data-io="{$io}" data-modaltype="message_content">Message Content</a></h6>
        	<br/>
        </div>
        <div class="span10">
        <div class="btn-group" style="margin-bottom: 20px; margin-left: 10px">
        	<a class="btn {if $range == 'hour'}btn-inverse{/if}" href="{$base_url}campclick/report/{$io}/{$offset}/hour">24 Hours</a>
            <a class="btn {if $range == 'month'}btn-inverse{/if}" href="{$base_url}campclick/report/{$io}/{$offset}/month">Last 30 days</a>
            <a id="dt-range-selector" class="btn {if $range == 'daterange'}btn-inverse{/if}">Date Range</a>
        </div>
        <div class="pull-right">
        	<a class="btn" href="{$base_url}/campclick/export_raw_data/{$io}">Export Raw Data</a>
        </div>
        
        <div id="date-selection-form" style="display: none; margin:15px 0">
                	<form name="date-select" id="date-select" action="#" method="post" class="form-horizontal">
                		<input type="hidden" name="date_url" id="date_url" value="{$base_url}campclick/report/{$io}/{$offset}/daterange" />
                    	<input type="text" size="25" name="sDate" id="startDate" value="Start Date" onblur="if(this.value=='') this.value='Start Date'" onfocus="if(this.value=='Start Date') this.value= ''" />
                        <input type="text" size="25" name="eDate" id="endDate" value="End Date" onblur="if(this.value=='') this.value='End Date'" onfocus="if(this.value=='End Date') this.value= ''"  />
                        <input type="hidden" name="action_url" id="action_url" value="{$base_url}campclick/date_range_report/{$io}" />
                        <input type="button" name="btn" id="date-range-search" class="btn btn-info form-horizontal" value="Filter" />
                    </form>
                </div>
       
			<h2>Click Graph</h2>
        	<table class="table table-bordered table-striped">
        	<tbody>
        		<tr>
        			<td>Unique Visitors (by IP):</td>
        			<td>{$report.unique_clickers}</td>
        		</tr>
        		<tr>
        			<td colspan="2"><div id="container-linechat" style="height: 500px; width: 100%"></div></td>
        		</tr>                
                
        		<tr>
        			<td>Mobile / Non-Mobile / Impressions</td>
        			<td>{$report.mobile_results.mobile} / {$report.mobile_results.non_mobile} / {$report.impressions_total}</td>
        		</tr>
        	</tbody>
        	</table>

			<hr />

			<h2>Campaign Links</h2>
        	<table class="table table-bordered table-striped" id="mytable_links">
        	<thead>
        		<tr>
        			<th>Short Link</th>
        			<th>Link URL</th>
        			<th>Clicks</th>
        			<th>-</th>
        		</tr>
        	</thead>
        	<tbody>
        		{foreach from=$report.group_count_results item=c}
	        		<tr>
	        			<td width="12%">
	        				<a href='http://{$domain_name}/c/{$io}/{$c.counter}' target="_blank">{$io}/{$c.counter}</a>
	        				<br/>
	        				<a href='#' style="text-align: center" class='edit-link' data-io='{$io}' data-linkid='{$c.link_id}'><i class="icon-edit"></i></a>
	        			</td>
	        			<td>{$c.dest_url}</td>
	        			<td>{$c.group_count}{if $c.max_clicks > 0 && $c.max_clicks != 9999999} / {$c.max_clicks}{/if}</td>
	        			<td><a href="{$base_url}campclick/moreinfo/{$io}/{$c.counter}"><i class="icon-eye-open"></i></a></td>
	        		</tr>
        		{/foreach}
        	</tbody>
        	</table>
        	
			{if $report.mobile_devices|@count gt 0}
			<hr />
			<h2>Mobile Devices</h2>
        	<table class="table table-bordered table-striped" id="mytable_mobile">
        	<thead>
        		<tr>
        			<th>Mobile Devices</th>
        			<th>Click Count</th>
        		</tr>
        	</thead>
        	<tbody>
        		{foreach from=$report.mobile_devices item=m}
	        		<tr>
	        			<td>{$m.mobile_device}</td>
	        			<td>{$m.cnt}</td>
	        		</tr>
        		{/foreach}
        	</tbody>
        	<tfoot>
        		<tr>
        			<td colspan="2"><div id="container-devices" style="height: 500px; min-width: 500px"></div></td>
        		</tr>                 
        	</tfoot>
        	</table>
			{/if}

			{if $report.platform|@count gt 0}
			<hr />
			<h2>Operating Systems</h2>
        	<table class="table table-bordered table-striped" id="mytable_os">
        	<thead>
        		<tr>
        			<th>Platform</th>
        			<th>Click Count</th>
        		</tr>
        	</thead>
        	<tbody>
        		{foreach from=$report.platform key=platformname item=p}
	        		<tr>
	        			<td>{$platformname}</td>
	        			<td>{$p}</td>
	        		</tr>
        		{/foreach}
        	</tbody>
        	<tfoot>
        		<tr>
        			<td colspan="2"><div id="container-platform" style="height: 500px; min-width: 500px"></div></td>
        		</tr>                
        	</tfoot>
        	</table>
        	{/if}

			{if $report.browsers_shares|@count gt 0}
			<hr />
			<h2>Web Browsers</h2>
        	<table class="table table-bordered table-striped" id="mytable_browser">
        	<thead>
        		<tr>
        			<th>Browser</th>
        			<th>Click Count</th>
        		</tr>
        	</thead>
        	<tbody>
        		{foreach from=$report.browsers_shares key=bn item=b}
	        		<tr>
	        			<td>{$bn}</td>
	        			<td>{$b}</td>
	        		</tr>
        		{/foreach}
        	</tbody>
        	<tfoot>
        		<tr>
	        		<td colspan="2"><div id="container" style="height: 500px; min-width: 500px"></div></td>
	        	</tr>
        	</tfoot>
        	</table>
            {/if}
            
        </div>
      </div>



<script>
$(document).ready(function(){
	$("#mytable_links").tablesorter();
	$("#mytable_os").tablesorter();
	$("#mytable_browser").tablesorter();
	$("#mytable_mobile").tablesorter();

	$("#date-range-search").click(function(){
		document.location.href = $("#date_url").val() + "/" + $("#startDate").val() + "/" + $("#endDate").val();
	});

	$(".show-modal").click(function(){
		$(".alert-error").hide();
		$(".alert-success").hide();

		if ($(this).data("modaltype") == "create_link")	{
			$("#myModal_createLink").modal();
		} else if ($(this).data("modaltype") == "message_content")	{
			$("#myModal_messageContent").modal();
			$.ajax({
				url: "/campclick/get_message",
				type: "POST",
				dataType: "json",
				data: { io: $(this).data("io") },
				success: function(msg)	{
					$("#message_result").val(msg.campaign.message);
				}
			});
		} else {
			// do nothing
			alert("no match found for modal click");
		}
	});

	$(".edit-link").click(function(){
		var io = $(this).data("io");
		var link_id = $(this).data("linkid");
		
		$.ajax({
			url: "/campclick/get_link",
			type: "POST",
			dataType: "json",
			data: {
				'io': io,
				'link_id': link_id
			},
			success: function(msg)   {
				if (msg.status == "SUCCESS")    {
					$("#edit_dest_url").val(msg.link.dest_url);
					$("#edit_max_clicks").val(msg.link.max_clicks);
					$("#edit_link_id").val(link_id);
					$("#edit_fulfilled").val(msg.link.is_fulfilled);
					$("#myModal_editLink").modal();
				}
			}
		});
	});

	$(".update-link").click(function(){
	    $.ajax({
		    url: "/campclick/update_link",
		    type: "POST",
		    dataType: "json",
		    data: {
			    dest_url: $("#edit_dest_url").val(),
			    max_clicks: $("#edit_max_clicks").val(),
			    link_id: $("#edit_link_id").val(),
			    fulfilled: $("#edit_fulfilled").val(),
			},
			success: function(msg)   {
				if (msg.status == "SUCCESS")    {
					document.location.reload();
				}
			}
		});
	});

	$(".create-link").click(function(){
	    $.ajax({
		    url: "/campclick/update_link/create",
		    type: "POST",
		    dataType: "json",
		    data: {
			    io: $("#create_io").val(),
			    dest_url: $("#create_dest_url").val(),
			    max_clicks: $("#create_max_clicks").val()
			},
			success: function(msg)   {
				if (msg.status == "SUCCESS")    {
					document.location.reload();
				}
			}
		});
	});
	
});
</script>

{include file="campclick/sections/modal.php"}
{include file="campclick/sections/footer.php"}
{include file="campclick/sections/chart-scripts.php"}
