{include file="v2/sections/header.php"}
    <div class="theme-container container-fluid">

	    <div class="alert alert-error" id="err_bof" style="display:none;">
	    	<a class="close" data-dismiss="alert">X</a>
	    	<strong id="err_bof_message"></strong>
	    </div>

	    <div class="alert alert-success" id="success_bof" style="display:none;">
	    	<a class="close" data-dismiss="alert">X</a>
	    	<strong id="success_bof_message"></strong>
	    </div>

      <!-- Example row of columns -->
      <div class="theme-report-campaign-list-row">
        <div class="span12">
        	<h3>Campaigns Accounting Report
				<small class='pull-right'>
				</small>
			</h3>
			<form class="pull-right" style="margin-bottom: 5px" method="post">
				<select name="status" id="status" class="input-medium" onchange="this.form.submit()">
					<option value="ALL" {if $status == 'ALL'} selected {/if}>ALL</option>
					<option value="ACTIVE" {if $status == 'ACTIVE'} selected {/if}>Active</option>
					<option value="PAUSED" {if $status == 'PAUSED'} selected {/if}>Paused</option>
					<option value="SCHEDULED" {if $status == 'SCHEDULED'} selected {/if}>Scheduled</option>
				</select>
			</form>
        	<h5 style="display: inline-block;">{$status} Campaign Count: {$campaigns|count}</h5>

        	<table class="table table-bordered table-striped" id="mytable">
				<thead class="dark_bg">
					<tr>
						<th>I/O #</th>
						<th>Campaign Name</th>
						<th>Date Created</th>
						<th>Date Completed</th>
						<th>Current/Max Clicks</th>
						<th>Current/Max Impressions</th>
						<th>Max Budget</th>
						<th>Cost</th>
						<th>Network name</th>
						<th>Profit</th>
					</tr>
				</thead>
				<tbody>
					{$total_cost = 0}
					{$total_max_budget = 0}
					{$total_max_clicks = 0}
					{$total_max_impressions = 0}
					{$limit_max_clicks = 0}
					{$limit_max_impressions = 0}
					{foreach from=$campaigns item=c}
						<tr id="id_{$c.id}" class="io" data-id="{$c.id}" data-status="{$c.network_campaign_status}">
							<td>{$c.io}</td>
							<td>{$c.name}</td>
							<td>{$c.campaign_start_datetime|date_format:"%Y/%m/%d"}</td>
							<td>{$c.campaign_end_datetime|date_format:"%Y/%m/%d"}</td>
							<td>{if !empty($c.total_clicks_count)} {$c.total_clicks_count}{else}-{/if} / {if !empty($c.max_clicks)} {$c.max_clicks} {else}-{/if} </td>
							<td>{if !empty($c.total_impressions_count)} {$c.total_impressions_count}{else}-{/if} / {if !empty($c.max_impressions)} {$c.max_impressions} {else}-{/if}</td>
							<td>{if !empty($c.max_budget)}{$c.max_budget}{else}-{/if} </td>
							<td>{if !empty($c.cost)}{$c.cost}{else}-{/if} </td>
							<td>{$c.network_name}</td>
							<td>{$c.max_budget-$c.cost}</td>
						</tr>
						{$total_cost = $total_cost + $c.cost}
						{$total_max_impressions = $total_max_impressions + $c.total_impressions_count}
						{$total_max_clicks = $total_max_clicks + $c.total_clicks_count}
					    {$limit_max_impressions = $limit_max_impressions + $c.max_impressions}
						{$limit_max_clicks = $limit_max_clicks + $c.max_clicks}
						{$total_max_budget = $total_max_budget + $c.max_budget}
					{/foreach}
				</tbody>
				<tfoot>
					<tr>
						<td>ALL</td>
						<td>ALL</td>
						<td>-</td>
						<td>-</td>
						<td>{$total_max_clicks} / {$limit_max_clicks}</td>
						<td>{$total_max_impressions} / {$limit_max_impressions}</td>
						<td>{$total_max_budget}</td>
						<td>{$total_cost}</td>
						<td>-</td>
						<td>{$total_max_budget-$total_cost}</td>
					</tr>
				</tfoot>
			</table>
        </div>
      </div>
	</div>

</section>
</main>
	<script src="/v2/js/jquery-2.0.3.min.js"></script>
	<script src="/v2/js/bootstrap.min.js"></script>
	<script src="/v2/js/jquery.tablesorter.min.js"></script>
	<script>
		{literal}
      	$(document).ready(function() {

				$("#mytable").tablesorter({
					sortList: [[3, 1]]
				});

//				$(".bid").on("change", function () {
//					var cnfrm = confirm("Are you sure you want to CHANGE the BID?");
//					var currentRow = $(this).closest("tr");
//					var id = $(this).data("id");
//
//					if (cnfrm) {
//						$.ajax({
//							url: "/v2/campaign/edit_bid/" + id + "/" + $("#bid_" + id).val(),
//							dataType: "json",
//							success: function (msg) {
//								if (msg.status == "SUCCESS") {
//									$("#bid_" + id).val(msg.bid);
//									currentRow.animateHighlight("#F5A9A9", 1000);
//								} else {
//									alert("Error encountered: " + msg.message);
//								}
//							}
//						});
//					}
//					return false;
//				});
			});
		{/literal}
	</script>

</body>
</html>
