{include file="v2/sections/header.php"}
<link href="/v2/css/datetime-picker.css" rel="stylesheet" type="text/css"/>
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
			<h3>Financial Report
				<small class='pull-right'>
				</small>
			</h3>
<!--			<h5 style="display: inline-block;">{$status} Campaign Count: {$campaigns|count}</h5>-->
            <div class="theme-report-campaigne-form-wrap">
                <form id="theme-report-schedule-form" class="theme-report-schedule-form">
                    <div class="theme-display-table theme-form-row">
                        <div class="col-md-4 form-group theme-form-group theme-table-middle-cell">
                            <label for="start-dae" class="theme-control-label">Start Date</label>
                            <input type="text" id="start_date_datepicker1" name="campaign_start_datetime" value="{if !empty($params.campaign_start_datetime)}{$params.campaign_start_datetime|date_format:"%Y/%m/%d %H:%M"}{/if}" placeholder="2014/20/12" class=" fillone form-control theme-date-picker theme-form-control" id="start-date" />
                        </div>
                        <div class="col-md-4 form-group theme-form-group theme-table-middle-cell">
                            <label for="end-date" class="theme-control-label">End Date</label>
                            <input type="text" id="end_date_datepicker1"  name="campaign_end_datetime"  value="{if !empty($params.campaign_end_datetime)}{$params.campaign_end_datetime|date_format:"%Y/%m/%d 23:59"}{/if}" placeholder="2014/20/18" class="fillone form-control theme-date-picker theme-form-control" id="end" />
                        </div>
                        <div class="col-md-4 form-group theme-form-group theme-table-middle-cell">
                            <label for="search-" class="theme-control-label">Users</label>
                            <select name="user" class="fillone form-control theme-form-control" >
                                <option value="ALL" selected>All Users</option>
                                {foreach from=$users item=user}
                                    <option value="{$user.id}">{$user.username}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group theme-submit-group">
                        <input id="submit_form" type="button" value="Update" class="theme-submit-control campaign-list-update" />
                    </div>
                </form>
            </div>
			<div class="table-responsive ">
				{include file="v2/campaign/financial_report_content.php"}
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
    <script src="/v2/js/datetime-picker.jquery.js"></script>
	<script src="/v2/js/loader.js"></script>
	<script src="/v2/js/financial-report.js"></script>

	<script>
		{literal}
      	$(document).ready(function() {
				$("#mytable").tablesorter({});
			});
		{/literal}
	</script>

</body>
</html>
