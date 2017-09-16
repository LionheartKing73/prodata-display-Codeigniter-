{include file="v2/sections/header.php"}
<style>
	.table > tbody > tr > td {
		/*box-sizing: border-box !important;*/
		/*padding: 0 !important;*/
		/*text-align: center;*/
		font-size: 12px;

	}
</style>
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
			<h3>Email Campaigns Tracking Report
				<small class='pull-right'>
				</small>
			</h3>
			<!--			{var_dump($campaigns)}-->

			<h5 style="display: inline-block;">{$status} Campaign Count: {$campaigns|count}</h5>
			<div class="span4 pull-right">
				<a id="csv_b" href="#" class="btn btn-primary pull-rigth">Export</a>
			</div>
			<div class="table-responsive ">
				<table class="table table-bordered table-striped" id="mytable">
					<thead class="dark_bg">
					<tr>
						<th width="21%">Campaign Name<br>
							<small> I/O #</small>
						</th>
						<th width="14%">Start date</th>
						<th width="25%">% Complete</th>
						<th width="10%">Current/Max Clicks</th>
						<th width="10%">Last 6 hours</th>
						<th width="10%">Last 12 hours</th>
						<th width="10%">Last 24 hours</th>
					</tr>
					</thead>
					<tbody>
					{$total_max_clicks = 0}
					{$limit_max_clicks = 0}

					{foreach from=$campaigns item=c}

					{$count = 0}

					{if !empty($c.total_clicks)}
					{$percent_clicks = 100*($c.total_report.mobile_clicks_count+$c.additional_report.non_mobile_clicks_count)/$c.total_clicks}
					{if $percent_clicks >= 100}
					{$percent_clicks = 100}
					{/if}
					{$count = $count +1}
					{else}
					{$percent_clicks = 0}
					{/if}
					
					{*					
					{if $c.percent_diff <= 6}
					{$percent_date = 100*$c.percent_diff/6}

					{if $percent_date >= 100}
					{$percent_date = 100}
					{/if}
					{$count = $count + 1}
					{else}
					{$percent_date = 0}
					{/if}
					{$percent = ($percent_clicks + $percent_date)/$count}
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

					<tr id="id_{$c.id}" class="io" data-id="{$c.id}" data-status="{$c.network_campaign_status}">
						<td width="21%"> {$c.name}<br>
							<small>{$c.io}</small>
						</td>
						<td width="14%">{$c.campaign_start_datetime|date_format:"%Y/%m/%d"}</td>
						<td width="25%">
							<h6 class="progress progress-striped" style="display: inline-block; width: 140px; margin-top: 10px;">
								<div class="progress-bar theme-report-progress-bar progress-bar-blue click" style="width: {(($c.total_report.clicks_count+$c.total_report.impressions_count)/$c.total_clicks)*100}%; height: 100%">{((($c.total_report.clicks_count+$c.total_report.impressions_count)/$c.total_clicks)*100)|string_format:"%.2f"}%</div>
							</h6>
						</td>
						<td width="10%">{if !empty($c.total_report.mobile_clicks_count+$c.additional_report.non_mobile_clicks_count)} {$c.total_report.mobile_clicks_count+$c.additional_report.non_mobile_clicks_count}{else}-{/if} / {if !empty($c.total_clicks)} {$c.total_clicks} {else}-{/if} </td>
						<td width="10%">{if !empty($c.six)}{$c.six}{else}-{/if}</td>
						<td width="10%">{if !empty($c.twelve)}{$c.twelve}{else}-{/if}</td>
						<td width="10%">{if !empty($c.twentyfour)}{$c.twentyfour}{else}-{/if}</td>
						<!--							<td>{if !empty($c.max_budget)}{$c.max_budget}{else}-{/if} </td>-->
						<!--							<td>{if !empty($c.cost)}{$c.cost}{else}-{/if} </td>-->
						<!--							<td>{$c.network_name}</td>-->
						<!--							<td>{$c.max_budget-$c.cost}</td>-->
					</tr>
					{$total_max_clicks = $total_max_clicks + $c.total_report.mobile_clicks_count+$c.additional_report.non_mobile_clicks_count}
					{$limit_max_clicks = $limit_max_clicks + $c.total_clicks}
					{/foreach}
					</tbody>
					<!--				<tfoot>-->
					<!--					<tr>-->
					<!--						<td>ALL</td>-->
					<!--						<td>ALL</td>-->
					<!--						<td>-</td>-->
					<!--						<td>-</td>-->
					<!--						<td>{$total_max_clicks} / {$limit_max_clicks}</td>-->
					<!--						<td>{$total_max_impressions} / {$limit_max_impressions}</td>-->
					<!--						<td>{$total_max_budget}</td>-->
					<!--						<td>{$total_cost}</td>-->
					<!--						<td>-</td>-->
					<!--						<td>{$total_max_budget-$total_cost}</td>-->
					<!--					</tr>-->
					<!--				</tfoot>-->
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
	$(document).ready(function () {
		setTimeout(function () {
			location.reload()
		}, 300000);
		$("#mytable").tablesorter({
			//sortList: [[3, 1]]
		});
		$("#csv_b").click(function () {
			$('#mytable').tableExport({type: 'csv', escape: 'false'});
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
