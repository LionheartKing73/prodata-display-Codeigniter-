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
        	<h2>LINK #: {$counter}</h2>
        	<br/>
        	<h6>Campaign Report</h6>
        	<br/>
        </div>
        <div class="span10">
        <div class="row">
        <div class="span7">
        <div class="btn-group" style="margin-bottom: 20px; margin-left: 10px">
        	<a class="btn {if $range == 'hour'}btn-inverse{/if}" href="{$base_url}campclick/moreinfo/{$io}/{$counter}/{$offset}/hour">24 Hours</a>
    	    <a class="btn {if $range == 'month'}btn-inverse{/if}" href="{$base_url}campclick/moreinfo/{$io}/{$counter}/{$offset}/month">Last 30 days</a>
 	    	<a id="dt-range-selector" class="btn {if $range == 'daterange'}btn-inverse{/if}">Date Range</a>
        </div>
        
        <div id="date-selection-form" style="display: none; margin:15px 0">
                	<form name="date-select" id="date-select" action="#" method="post" class="form-horizontal">
                		<input type="hidden" name="date_url" id="date_url" value="{$base_url}campclick/moreinfo/{$io}/{$counter}/{$offset}/daterange" />
                    	<input type="text" size="25" name="sDate" id="startDate" value="Start Date" onblur="if(this.value=='') this.value='Start Date'" onfocus="if(this.value=='Start Date') this.value= ''" />
                        <input type="text" size="25" name="eDate" id="endDate" value="End Date" onblur="if(this.value=='') this.value='End Date'" onfocus="if(this.value=='End Date') this.value= ''"  />
                        <input type="hidden" name="action_url" id="action_url" value="{$base_url}campclick/date_range_report/{$io}" />
                        <input type="button" name="btn" id="date-range-search" class="btn btn-info form-horizontal" value="Filter" />
                    </form>
         </div>
         </div>
       	 <div class="span3"><div class="btn-group" style="margin-bottom: 20px; margin-left: 10px">
                    <a class="btn " href="{$base_url}campclick/raw_data/{$io}/{$counter}">View Raw Data</a>
                    <a class="btn " href="{$base_url}campclick/export_raw_data/{$io}/{$counter}">Export Raw Data</a>
                </div></div>
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
        			<td>Mobile / Non-Mobile</td>
        			<td>{$report.mobile_results.mobile} / {$report.mobile_results.non_mobile}</td>
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
        			<th>Link Count</th>
        			
        		</tr>
        	</thead>
        	<tbody>
        		{foreach from=$report.group_count_results item=c}
	        		<tr>
	        			<td>http://{$domain_name}/c/{$io}/{$c.counter}</td>
	        			<td>{$c.dest_url}</td>
	        			<td>{$c.group_count}</td>
	        			
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
});
</script>

{include file="campclick/sections/footer.php"}
{include file="campclick/sections/chart-scripts.php"}