<table class="table table-bordered table-striped" id="mytable">
	<thead class="dark_bg">
		<tr>
			<th>Campaign Name</th>
			<th>Client Name</th>
			<th>Invoiced Amount</th>
			<th>Campaign Spent Amount</th>
			<th>Net gain/loss</th>
			<th>Start Date</th>
		</tr>
	</thead>
	<tbody>
		{$total_cost = 0}
		{$total_max_budget = 0}
		{$total_max_clicks = 0}
		{foreach from=$campaigns item=c}
			<tr id="id_{$c.id}" class="io" data-id="{$c.id}" data-status="{$c.network_campaign_status}">
				<td>{$c.name}<br><small>{$c.io}</small></td>
				<td>{$c.username}</td>
				<td>{$c.max_budget|string_format:"%.2f"}</td>
				<td>{if !empty($c.cost)}{$c.cost}{else}-{/if} </td>
				<td>{$c.max_budget-$c.cost}</td>
				<td>{$c.campaign_start_datetime|date_format:"%Y/%m/%d"}</td>
			</tr>
			{$total_cost = $total_cost + $c.cost}
			{$total_max_budget = $total_max_budget + $c.max_budget}
		{/foreach}
	</tbody>
	<tfoot>
		<tr>
			<td>ALL</td>
			<td>-</td>
			<td>{$total_max_budget}</td>
			<td>{$total_cost}</td>
			<td>{$total_max_budget - $total_cost}</td>
			<td>-</td>
		</tr>
	</tfoot>
</table>