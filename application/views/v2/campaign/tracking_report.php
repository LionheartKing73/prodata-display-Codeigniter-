{include file="v2/sections/header.php"}
<section class="theme-container r-container" id="wrap">
	<div class="alert alert-error" id="err_bof" style="display:none;">
		<a class="close" data-dismiss="alert">X</a>
		<strong id="err_bof_message"></strong>
	</div>
	<div class="alert alert-success" id="success_bof" style="display:none;">
		<a class="close" data-dismiss="alert">X</a>
		<strong id="success_bof_message"></strong>
	</div>
	<!-- Example row of columns -->
	<div class="theme-report-campaign-list-row mobile-container" id="r-content">
		<div class="span12">
			<h3>Tracking Report</h3>
			<h4>{$smarty.now|date_format:"%Y-%m-%d %H:%M"}</h4>
			<h5 style="display: inline-block;">{$status} Campaign Count: {$campaigns|count}</h5>
			<div class="span4 pull-right"><a id="csv_b" href="#" class="btn btn-primary pull-rigth">Export</a></div>
			<div class="table-responsive">
				<table class="table tracking_report_table table-bordered table-striped" id="mytable">
					<thead class="dark_bg">
						<tr>
							<th width="10%">Campaign Name<br><small> I/O #</small></th>
							<th width="6%">Start date</th>
							<th width="6%">End Date</th>
							<th width="7%">Ad Type</th>
							<th width="40%">% Complete</th>
							<th width="5%">Current/Max Clicks</th>
							<th width="5%">Current/Max Impressions</th>
							<th width="5%">Spent Budget</th>
							<th width="5%">Max Budget</th>
							<th width="3%">% Budget</th>
							<th width="2%">Run Type</th>
							<th width="2%">Last 6 hours</th>
							<th width="2%">Last 12 hours</th>
							<th width="2%">Last 24 hours</th>
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
						{$c.cost = $c.cost + $rtb_cost}
						{$count = 0}
						{if !empty($c.max_budget)}
						{$count = $count + 1}
						{$cost = $c.percentage_max_budget - $c.cost}
						{if ($cost>0)}
						{$percent_cost = $c.cost*100/$c.percentage_max_budget}
						{else}
						{$percent_cost = 100}
						{/if}
						{/if}
						{if !empty($c.max_clicks)}
						{$percent_clicks = 100*$c.total_clicks_count/$c.max_clicks}
						{if $percent_clicks >= 100}
						{$percent_clicks = 100}
						{/if}
						{$count = $count +1}
						{else}
						{$percent_clicks = 0}
						{/if}
						{if !empty($c.max_impressions)}
						{$percent_impressions = 100*$c.total_impressions_count/$c.max_impressions}
						{if $percent_impressions >= 100}
						{$percent_impressions = 100}
						{/if}
						{$count = $count + 1}
						{else}
						{$percent_impressions = 0}
						{/if}

						{*
						{if !empty($c.date_diff) && $c.percent_diff <= $c.date_diff}
						{$percent_date = 100*$c.percent_diff/$c.date_diff}

						{if $percent_date >= 100}
						{$percent_date = 100}
						{/if}
						{$count = $count + 1}
						{else}
						{$percent_date = 0}
						{/if}
						{$percent = ($percent_cost + $percent_clicks + $percent_impressions + $percent_date)/$count}
						{$class=''}
						{if $percent_date>=50 && $percent_date<80}
						{if $percent<50}
						{$class='yellow_row'}
						{/if}
						{else if $percent_date>=80}
						{if $percent<80}
						{$class='red_row'}
						{/if}
						{/if} *}
							<tr id="id_{$c.id}" class="io {$c.date_diff} {$c.percent_diff}" data-id="{$c.id}" data-status="{$c.network_campaign_status}">
								<td width="10%"> {$c.name}<br><small>{$c.io}</small></td>
								<td width="6%">{$c.campaign_start_datetime|date_format:"%Y/%m/%d"}</td>
								<td width="6%">{$c.campaign_end_datetime|date_format:"%Y/%m/%d"}</td>
								<td width="7%">{$c.campaign_type}</td>
								<td width="40%">
									<div class="progress progress-striped {$c.slow_performing}">
										<div class="progress-bar theme-report-progress-bar progress-bar-blue click" role="progressbar" style="width:{$percent}%;">{$percent|string_format:"%.2f"}%</div>
									</div>
								</td>
								<td width="5%">{if !empty($c.total_clicks_count)} {$c.total_clicks_count}{else}-{/if} / {if !empty($c.max_clicks)} {$c.max_clicks} {else}-{/if} </td>
								<td width="5%">
								 {if $c.is_thru_guarantee=="Y" && $c.total_impressions_count>=$c.max_impressions}
								   {$c.max_impressions}
								   {else}
								   {if !empty($c.total_impressions_count)}{$c.total_impressions_count}{else}-{/if}{/if} / {if !empty($c.max_impressions)} {$c.max_impressions} {else}-{/if}
								</td>
								<td width="5%">{if !empty($c.cost)}{$c.cost}{else}-{/if} </td>
								<td width="5%" 93>{if !empty($c.max_budget)}{$c.max_budget}{else}-{/if} </td>
								<td width="3%">{if !empty($c.max_budget)}{($c.cost*100/$c.max_budget)|string_format:"%.2f"}{else}-{/if}</td>
								<td width="2%">{if !empty($c.max_clicks)}CPC{else}CPM{/if} </td>
								<td width="2%">{$c.six}</td>
								<td width="2%">{$c.twelve}</td>
								<td width="2%">{$c.twentyfour}</td>
							</tr>
							{$total_cost = $total_cost + $c.cost}
							{$total_max_impressions = $total_max_impressions + $c.total_impressions_count}
							{$total_max_clicks = $total_max_clicks + $c.total_clicks_count}
							{$limit_max_impressions = $limit_max_impressions + $c.max_impressions}
							{$limit_max_clicks = $limit_max_clicks + $c.max_clicks}
							{$total_max_budget = $total_max_budget + $c.max_budget}
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
  	</div>
</section>
{include file="v2/sections/footer.php"}
</section>
</main>
	<script src="/v2/js/jquery-2.0.3.min.js"></script>
	<script src="/v2/js/bootstrap.min.js"></script>
	<script src="/v2/js/jquery.tablesorter.min.js"></script>
	<script src="/v2/js/tableExport.js"></script>
	<script src="/v2/js/jquery.base64.js"></script>
	<script>
		{literal}
      	$(document).ready(function() {
			setTimeout( function() {
				location.reload()
			}, 300000);
			$("#mytable").tablesorter({
				//sortList: [[3, 1]]
			});
			$("#csv_b").click(function () {
				$('#mytable').tableExport({type:'csv',escape:'false',tableName:'test'});
			});
		});
		{/literal}
	</script>
</body>
</html>
