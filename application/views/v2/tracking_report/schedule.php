{include file="v2/tracking_report/header.php"}

<page size="CUSTOM">

	<h1 align="center" class="pretty-font">ProData Media - Scheduled Campaigns<br/>( NON - AGENCY )</h1>

	<table class="pretty-font">
		<thead>
			<tr align="left" class="table-heading" bgcolor="#DDDDDD">
    			<th>IO#</th>
    			<th>Client</th>
    			<th>Campaign Name</th>
    			<th>Approved?</th>
    			<th>Date Start</th>
    			<th>Rep</th>
    			<th>Channels</th>
    			<th align="right">Budget</th>
			</tr>
		</thead>
		
		<tbody>
		{foreach from=$campaigns item=c}
			{if $zebra == "#FFFFFF"}
				{assign var="zebra" value="#EEEEEE"}
			{else}
				{assign var="zebra" value="#FFFFFF"}
			{/if}
			
			{if $c.status_deployed == "N" && $c.date_start < $last_update}
				{assign var="zebra" value="#FFbbba"}
			{/if}
			
			<tr class="tbody_style" valign="top" bgcolor="{$zebra}">
				<td><a href="/v2/trkreport/campaign/{$c.id}">{$c.io}</a></td>
				<td>{$c.company}<br/>{$c.first_name} {$c.last_name}</td>
				<td>{$c.name}</td>
				<td>
					<div class="tooltip"><img src="/v2/report-icons/NEW_STATUS_MONEY_IN_HOUSE.png" class="status-{if $c.status_money_in_house == "Y"}active{else}inactive{/if}" /><span class="tooltiptext">Money In House</span></div>
					<div class="tooltip"><img src="/v2/report-icons/NEW_STATUS_CREATIVE_APPROVED.png" class="status-{if $c.status_creative_approved == "Y"}active{else}inactive{/if}" /><span class="tooltiptext">Creative Approved</span></div>
					<div class="tooltip"><img src="/v2/report-icons/NEW_STATUS_CLIENT_APPROVED.png" class="status-{if $c.status_client_approved == "Y"}active{else}inactive{/if}" /><span class="tooltiptext">Client Approved</span></div>
					<div class="tooltip"><img src="/v2/report-icons/NEW_STATUS_DEPLOYED.png" class="status-{if $c.status_deployed == "Y"}active{else}inactive{/if}" /><span class="tooltiptext">Campaign Deployed</span></div>
				</td>
				<td>{$c.date_start}</td>
				<td>{$c.sales_fname} {$c.sales_lname}</td>
				<td>
					<div class="tooltip"><img src="/v2/report-icons/CHANNEL_DISPLAY.png" class="status-{if $c.channel_display == "Y"}active{else}inactive{/if}" /><span class="tooltiptext">Display Ads</span></div>
					<div class="tooltip"><img src="/v2/report-icons/CHANNEL_EMAIL.png" class="status-{if $c.channel_email == "Y"}active{else}inactive{/if}" /><span class="tooltiptext">Email</span></div>
					<div class="tooltip"><img src="/v2/report-icons/CHANNEL_RETARGET.png" class="status-{if $c.channel_retarget == "Y"}active{else}inactive{/if}" /><span class="tooltiptext">Retargeting</span></div>
					<div class="tooltip"><img src="/v2/report-icons/CHANNEL_SOCIAL.png" class="status-{if $c.channel_social == "Y"}active{else}inactive{/if}" /><span class="tooltiptext">Social</span></div>
				</td>
				<td align="right">$ {$c.budget_gross}</td>
			</tr>
		{/foreach}
		</tbody>
		
		<tfoot valign="bottom">
			<tr>
				<td colspan="7" align="center" class="small-pretty-font">
					<div class="legend-row">
    					<div class="legend-block"><img src="/v2/report-icons/NEW_STATUS_MONEY_IN_HOUSE.png" class="status-active" /><span>Money In House</span></div>
    					<div class="legend-block"><img src="/v2/report-icons/NEW_STATUS_CREATIVE_APPROVED.png" class="status-active" /><span>Creative Approved</span></div>
    					<div class="legend-block"><img src="/v2/report-icons/NEW_STATUS_CLIENT_APPROVED.png" class="status-active" /><span>Client Approved</span></div>
    					<div class="legend-block"><img src="/v2/report-icons/NEW_STATUS_DEPLOYED.png" class="status-active" /><span>Campaign Deployed</span></div>
    					<div class="legend-block"><img src="/v2/report-icons/CHANNEL_DISPLAY.png" class="status-active" /><span>Display Ads</span></div>
    					<div class="legend-block"><img src="/v2/report-icons/CHANNEL_EMAIL.png" class="status-active" /><span>Email</span></div>
    					<div class="legend-block"><img src="/v2/report-icons/CHANNEL_RETARGET.png" class="status-active" /><span>Retargeting</span></div>
    					<div class="legend-block"><img src="/v2/report-icons/CHANNEL_SOCIAL.png" class="status-active" /><span>Social</span></div>
					</div>
					<br/>
					Last Updated at {$last_update}
				</td>
			</tr>
		</tfoot>
	</table>
</page>

<script>
	setTimeout(function(){
		window.location.reload();
	}, 120000);
</script>

{include file="v2/tracking_report/footer.php"}