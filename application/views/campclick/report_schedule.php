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
        	<h2>Campaign Schedule</h2>
        	<div class="well">
        		<select name="vendor" id="vendor">
        			<option value="">Select A Vendor</option>
        			<option value="ANY">All Vendors</option>
        			{foreach from=$vendors item=v}
        				<option value="{$v.id}">{$v.name}</option>
        			{/foreach}
        		</select>
        		
        		<select name="campaign_is_started">
        			<option value="">Campaign Is...</option>
        			<option value="N">In Active</option>
        			<option value="Y">Active</option>
        		</select>
        	</div>

        	<br/>
        	
        	<table class="table table-bordered table-striped" id="mytable">
        	<thead>
        		<tr>
        			<th>I/O #</th>
        			<th>Campaign Name</th>
        			<th>Is Active?</th>
        			<th>Start Date</th>
        			<th>&nbsp;</th>
        		</tr>
        	</thead>
        	<tbody>
        		{foreach from=$campaigns item=c}
	        		<tr id="io_{$c.io}">
	        			<td>{$c.io}</td>
	        			<td>{$c.name}</td>
	        			<td>{$c.campaign_is_started}</td>
	        			<td>{$c.campaign_start_datetime|date_format:"%Y-%m-%d %I:%M"}</td>
	        			<td><span class="pull-right"><a href="{$base_url}campclick/report/{$c.io}"><i class="icon-eye-open"></i></a></span></td>
	        		</tr>
        		{/foreach}
        	</tbody>
        	</table>
        </div>
      </div>

      <script>
      	$(document).ready(function(){
			$(".campaign-summary").addClass("active");

			$("#mytable").tablesorter({
				sortList: [[3,1]]
			});
       	});
      </script>

{include file="campclick/sections/footer.php"}
