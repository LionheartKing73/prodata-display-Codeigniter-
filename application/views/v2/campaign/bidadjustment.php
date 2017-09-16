{include file="v2/sections/header.php"}
<link href="/v2/css/datetime-picker.css" rel="stylesheet" type="text/css"/>
    <div class="theme-container mobile-container "  id="wrap">

	    <div class="alert alert-error" id="err_bof" style="display:none;">
	    	<a class="close" data-dismiss="alert">X</a>
	    	<strong id="err_bof_message"></strong>
	    </div>

	    <div class="alert alert-success" id="success_bof" style="display:none;">
	    	<a class="close" data-dismiss="alert">X</a>
	    	<strong id="success_bof_message"></strong>
	    </div>

      <!-- Example row of columns -->
      <div id="r-content">
        <div class="span12">
        	<h3>Campaign Bid Adjustment
				<small class='pull-right'>
					<i class="icon-ok"></i> indicates campaign started.<br/>
					<i class='icon-star-empty'></i> indicates open pixel campaign<br/>
					S=State, Z=Zip, C=Country
				</small>
			</h3>
			<form class="pull-right bid-camp-status" method="post">
				<select name="status" id="status" class="input-medium" onchange="this.form.submit()">
					<option value="ACTIVE" {if $status == 'ACTIVE'} selected {/if}>Active</option>
					<option value="PAUSED" {if $status == 'PAUSED'} selected {/if}>Paused</option>
					<option value="SCHEDULED" {if $status == 'SCHEDULED'} selected {/if}>Scheduled</option>
				</select>
			</form>
        	<h5 style="display: inline-block;">{$status} Campaign Count: {$campaigns|count}</h5>
			<div class="table-responsive">
        	<table class="table table-bordered table-striped" id="mytable" style="font-size: 12px;">
        	<thead class="dark_bg">
        		<tr class="tbl-padding">
        			<th>Campaign Name</th>
        			<th class="cmp-width">% Complete</th>
        			<th>Date Created / End date</th>
        			<th>Max Clicks</th>
        			<th>Max Impressions</th>
        			<th>Bid</th>
        			<th>Daily Cap</th>
                    <th>Max Budget</th>
                    <th>REAL Budget</th>
                    <th>Cost</th>
                    <th>Invoiced Amount</th>
        		</tr>
        	</thead>
        	<tbody>
        		{foreach from=$campaigns item=c}
	        		<tr id="id_{$c.id}" class="io" data-id="{$c.id}" data-status="{$c.network_campaign_status}">
	        			<td>
	        				{$c.io}<br/>
	        			    {$c.name} ({if $c.geotype == "country"}C{elseif $c.geotype == "postalcode"}Z{elseif $c.geotype == "state"}S{/if})
                            <a target="_blank" href="/v2/campaign/reporting/{$c.id}" class=''><i class="fa fa-edit"></i></a>
	        			    <br/>
	        			    <small><strong>10-Min Cnt:</strong> <span id='10min_cnt_{$c.id}'>0</span></small>
	        			    |
            			    <small><strong>30-Min Cnt:</strong> <span id='30min_cnt_{$c.id}'>0</span></small>
	        			    |
            			    <small><strong>60-Min Cnt:</strong> <span id='60min_cnt_{$c.id}'>0</span></small>
	        			    |
            			    <small><strong>6-Hr Cnt:</strong> <span id='360min_cnt_{$c.id}'>0</span></small>

	        			    <br/>
	        			    <span class='btn btn-success btn-xs resume-btn' id="resume_{$c.id}" data-id="{$c.id}" {if $c.campaign_status == "ACTIVE"}style="display:none;"{/if}>Resume</span>
	        			    <span class='btn btn-warning btn-xs pause-btn' id="pause_{$c.id}" data-id="{$c.id}" {if $c.campaign_status == "PAUSED"}style="display:none;"{/if}>Pause</span>
	        			    <span class='btn btn-danger btn-xs complete-btn' id="complete_{$c.id}" data-id="{$c.id}" {if $c.campaign_status == "PAUSED"}style="display:none;"{/if}>Complete</span>
							{if $c.geotype=='postalcode'}
								<span class='btn btn-info btn-xs set-geo-btn' id="set_geo_{$c.id}" data-id="{$c.id}" {if $c.network_campaign_status == "PAUSED"}style="display:none;"{/if}>Set Geo</span>
							{/if}
							{if $c.network_name=='FIQ'}
	        			    	<span class='btn btn-info btn-xs clear-time-schedule-btn' id="set_timeschedule_{$c.id}" data-id="{$c.id}" {if $c.network_campaign_status == "PAUSED"}style="display:none;"{/if}>Clear Time Schedule</span>
	        			    {/if}
	        			</td>
	        			<td>
	        				<div class="progress progress-striped {$c.slow_performing}">
							{if $c.is_thru_guarantee == 'Y'}
                                {$percent = 100*($c.total_impressions_count/$c.max_impressions)*1/2}
                            {else}
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
								{if !empty($c.date_diff) && $c.percent_diff < $c.date_diff}
								{$percent_date = 100*$c.percent_diff/$c.date_diff}

								{if $percent_date >= 100}
								{$percent_date = 100}
								{/if}
								{$count = $count + 1}
								{else}
								{$percent_date = 0}
								{/if} *}
								{$percent = ($percent_cost + $percent_clicks + $percent_impressions)/$count}
							{/if}
								<div class="progress-bar theme-report-progress-bar progress-bar-blue click" role="progressbar" style="width:{$percent}%;">{$percent|string_format:"%.2f"}%</div>
	        				</div>
							<h4>{$percent|string_format:"%.2f"}%</h4>
	        			</td>
	        			<td><p style="margin-bottom: -20px;padding-left:2px;">{$c.campaign_start_datetime|date_format:"%Y/%m/%d"}</p>
							<input type='text' class='input-small dailyCrap form-control end_date_picker' id='end_date_{$c.id}' data-id='{$c.id}' value='{if !empty($c.campaign_end_datetime)}{$c.campaign_end_datetime|date_format:"%Y/%m/%d"}{/if}'>
							<small id="curspend_{$c.id}" data-id="{$c.id}"></small>
							<span class="btn btn-xs btn-success end_date_save" data-id="{$c.id}">Save</span>
	        			</td>
                        <td>
                            <span class="label label-default">Clicks Count: {$c.total_clicks_count}</span>
							<input type='text' class='input-small dailyCrap form-control' id='max_clicks_{$c.id}' data-id='{$c.id}' class='max_clicks' value='{if !empty($c.max_clicks)}{$c.max_clicks}{/if}'>
							<small id="curspend_{$c.id}" data-id="{$c.id}"></small>
							<span class="btn btn-xs btn-success max_clicks_save" data-id="{$c.id}">Save</span>
						</td>
						<td>
							<input type='text' class='input-xs dailyCrap form-control' id='max_impressions_{$c.id}' data-id='{$c.id}' class='max_impressions' value='{if !empty($c.max_impressions)}{$c.max_impressions}{/if}'>
							<small id="curspend_{$c.id}" data-id="{$c.id}"></small>
							<span class="btn btn-xs btn-success max_impressions_save" data-id="{$c.id}">Save</span>
						</td>
	        			<td>
                            {if $c.network_campaign_status != "PAUSED"}
								<input type='text' class='input-mini bid bid_camp form-control' id='bid_{$c.id}' data-id='{$c.id}' value='{$c.bid}'><br/>
							{/if}
						</td>
	        			<td>
                            <p style="margin-bottom: -20px;padding-left:2px;">{$c.daily_cost|string_format:"%.2f"}</p>
                            {if $c.network_campaign_status != "PAUSED"}
                            {if $c.network_name != "FIQ"}
	        			        <input type='text' class='input-mini dailyCrap camp_daily_budget form-control' id='daily_cap_{$c.id}' data-id='{$c.id}' class='daily_cap' value='{$c.budget}'>
							{else}
								<input type='text' class='input-mini dailyCrap camp_daily_budget form-control' id='daily_cap_{$c.id}' data-id='{$c.id}' class='daily_cap' value='{$c.daily_cap}'>
							{/if}
								<small id="curspend_{$c.id}" data-id="{$c.id}"></small>
	        			        <span class="btn btn-mini btn-success cap-save" data-id="{$c.id}">Save</span>
	        			    {/if}
                        </td>
						<td>
							{if $c.network_campaign_status != "PAUSED"}
							<input type='text' class='input-mini dailyCrap form-control' id='max_budget_{$c.id}' data-id='{$c.id}' class='max_budget' value='{$c.max_budget}'>
							<small id="budget_curspend_{$c.id}" data-id="{$c.id}"></small>
							<span class="btn btn-mini btn-success max_budget_save" data-id="{$c.id}">Save</span>
							{/if}
						</td>
                        <td>
                            {$c.percentage_max_budget|string_format:"%.2f"}
                        </td>
						<td>
							{$c.cost|string_format:"%.2f"}
						</td>
						<td>
							{$c.max_budget|string_format:"%.2f"}
						</td>
	        		</tr>
        		{/foreach}
        	</tbody>
        	</table>
			</div>
        </div>
      </div>
	</div>
		</section>
	</main>
		<script src="/v2/js/jquery-2.0.3.min.js"></script>
		<script src="/v2/js/bootstrap.min.js"></script>
		<script src="/v2/js/jquery.tablesorter.min.js"></script>
		<script src="/v2/js/datetime-picker.jquery.js"></script>
      <script>
      	$(document).ready(function(){

			$(".end_date_picker").datetimepicker({
				format: "Y/m/d H:i",
				//minDate: '-1970/01/8',
				//maxDate: '+1970/01/1',
			});

      		{literal}
      		var notLocked = true;
      		$.fn.animateHighlight = function(highlightColor, duration) {
      		    var highlightBg = highlightColor || "#FFFF9C";
      		    var animateMs = duration || 1500;
      		    var originalBg = this.css("backgroundColor");
				console.log(highlightBg,animateMs,originalBg);
      		    if (notLocked) {
      		        notLocked = false;
      		        this.stop().css("backgroundColor", highlightBg)
      		            .animate({backgroundColor: originalBg}, animateMs);
      		        setTimeout( function() { notLocked = true; }, animateMs);
      		    }
      		};
      		{/literal}

			$(".campaign-summary").addClass("active");

			$("#mytable").tablesorter({
				sortList: [[3,1]]
			});

			$(".clkcap").on("change", function(){
				var cnfrm = confirm("Are you sure you want to CHANGE the HOURLY CLICK CAP?");
				var id = $(this).data("id");
				var currentRow = $(this).closest("tr");

				if (cnfrm)  {
					$.ajax({
						url: "/v2/campaign/clickcap/" + id + "/" + $("#clkcap_" + id).val(),
						dataType: "json",
						success: function(msg){
						    if (msg.status == "SUCCESS")  {
							    $("#clkcap_" + id).val(msg.click_cap);
							    currentRow.animateHighlight("#F5A9A9", 1000);
						    } else {
						    	alert("Error encountered: " + msg.message);
						    }
						}
					});
				}
			});

			$(".set-geo-btn").click(function(){
				document.location.href="/v2/campaign/geolocation/" + $(this).data("id");
			});

			$(".clear-time-schedule-btn").click(function(){
				var id = $(this).data("id");

				$.ajax({
					url: "/v2/campaign/set_schedule/" + id,
					dataType: "json",
					success: function(msg){
						if (msg.status == "SUCCESS")  {
							alert(msg.message);
						} else {
							alert("Error encountered: " + msg.message);
						}
					}
				});
			});

			$(".bid").on("change", function(){
				var cnfrm = confirm("Are you sure you want to CHANGE the BID?");
				var currentRow = $(this).closest("tr");
				var id = $(this).data("id");

				if (cnfrm)  {
					$.ajax({
						url: "/v2/campaign/edit_bid/" + id+ "/" + $("#bid_" + id).val(),
						dataType: "json",
						success: function(msg) {
							if (msg.status == "SUCCESS")  {
								$("#bid_" + id).val(msg.bid);
								currentRow.animateHighlight("#F5A9A9", 1000);
							} else {
								alert("Error encountered: " + msg.message);
							}
						}
					});
				}
				return false;
			});

			$(".bid-up").click(function(){
				var cnfrm = confirm("Are you sure you want to INCREASE the BID?");
				var currentRow = $(this).closest("tr");
				var id = $(this).data("id");

				if (cnfrm)  {
					$.ajax({
					    url: "/v2/campaign/bid_up/" + id,
					    dataType: "json",
					    success: function(msg) {
						    if (msg.status == "SUCCESS")  {
							    $("#bid_" + id).val(msg.bid);
							    currentRow.animateHighlight("#F5A9A9", 1000);
						    } else {
						    	alert("Error encountered: " + msg.message);
						    }
					    }
					});
				}
				return false;
			});

			$(".bid-down").click(function(){
				var cnfrm = confirm("Are you sure you want to DECREASE the BID?");
				var id = $(this).data("id");
				var currentRow = $(this).closest("tr");

				if (cnfrm)  {
					$.ajax({
					    url: "/v2/campaign/bid_down/" + $(this).data("id"),
					    dataType: "json",
					    success: function(msg) {
						    if (msg.status == "SUCCESS")  {
							    $("#bid_" + id).val(msg.bid);
							    currentRow.animateHighlight("#F5A9A9", 1000);
						    } else {
						    	alert("Error encountered: " + msg.message);
						    }
					    }
					});
				}
				return false;
			});

       	});

       	$(".cap-save").click(function(){
			var cnfrm = confirm("Are you sure you want to CHANGE the DAILY CAP?");
			var id = $(this).data("id");
			var currentRow = $(this).closest("tr");

			if (cnfrm)  {
				$.ajax({
				    url: "/v2/campaign/cap_save",
				    type: "POST",
				    dataType: "json",
				    data: {
				        'id': id,
				        'cap': $("#daily_cap_" + id).val()
			        },
				    success: function(msg) {
					    if (msg.status == "SUCCESS")  {
						    $("#daily_cap_" + id).val(msg.cap);
						    currentRow.animateHighlight("#F5A9A9", 1000);
					    } else {
						    alert("Error encountered: " + msg.message);
					    }
				    }
				});
			}
			return false;
        });

        $(".max_budget_save").click(function(){
			var cnfrm = confirm("Are you sure you want to CHANGE the MAX BUDGET?");
			var id = $(this).data("id");
			var currentRow = $(this).closest("tr");

			if (cnfrm)  {
				$.ajax({
				    url: "/v2/campaign/edit_max_budget",
				    type: "POST",
				    dataType: "json",
				    data: {
				        'id': id,
				        'max_budget': $("#max_budget_" + id).val()
			        },
				    success: function(msg) {
					    if (msg.status == "SUCCESS")  {
						    $("#max_budget_" + id).val(msg.max_budget);
						    currentRow.animateHighlight("#F5A9A9", 1000);
					    } else {
						    alert("Error encountered: " + msg.message);
					    }
				    }
				});
			}
			return false;
        });

		$(".max_clicks_save").click(function(){
			var cnfrm = confirm("Are you sure you want to CHANGE the MAX CLICKS COUNT?");
			var id = $(this).data("id");
			var currentRow = $(this).closest("tr");

			if (cnfrm)  {
				$.ajax({
				    url: "/v2/campaign/edit_max_clicks",
				    type: "POST",
				    dataType: "json",
				    data: {
				        'id': id,
				        'max_clicks': $("#max_clicks_" + id).val()
			        },
				    success: function(msg) {
					    if (msg.status == "SUCCESS")  {
						    $("#max_clicks_" + id).val(msg.max_clicks);
						    currentRow.animateHighlight("#F5A9A9", 1000);
					    } else {
						    alert("Error encountered: " + msg.message);
					    }
				    }
				});
			}
			return false;
        });

		$(".end_date_save").click(function(){
			var cnfrm = confirm("Are you sure you want to CHANGE the END DATE?");
			var id = $(this).data("id");
			var currentRow = $(this).closest("tr");

			if (cnfrm)  {
				$.ajax({
				    url: "/v2/campaign/update_end_date",
				    type: "POST",
				    dataType: "json",
				    data: {
				        'id': id,
				        'end_date': $("#end_date_" + id).val()
			        },
				    success: function(msg) {
					    if (msg.status == "SUCCESS")  {
						    currentRow.animateHighlight("#F5A9A9", 1000);
					    } else {
						    alert("Error encountered: " + msg.message);
					    }
				    }
				});
			}
			return false;
        });

		$(".max_impressions_save").click(function(){
			var cnfrm = confirm("Are you sure you want to CHANGE the MAX IMPRESSIONS COUNT?");
			var id = $(this).data("id");
			var currentRow = $(this).closest("tr");

			if (cnfrm)  {
				$.ajax({
				    url: "/v2/campaign/edit_max_impressions",
				    type: "POST",
				    dataType: "json",
				    data: {
				        'id': id,
				        'max_impressions': $("#max_impressions_" + id).val()
			        },
				    success: function(msg) {
					    if (msg.status == "SUCCESS")  {
						    $("#max_impressions_" + id).val(msg.max_impressions);
						    currentRow.animateHighlight("#F5A9A9", 1000);
					    } else {
						    alert("Error encountered: " + msg.message);
					    }
				    }
				});
			}
			return false;
        });

       	$(".pause-btn").click(function(){
           	var id = $(this).data("id");

           	var cnfrm = confirm("Are you sure you want to PAUSE this campaign?");

           	if (cnfrm)  {
               	$.ajax({
                   	url: "/v2/campaign/edit_campaign_status/" + id +"/PAUSED",
                   	dataType: "json",
                   	success: function(msg)  {
                       	if (msg.status == "SUCCESS")    {
							$("#resume_" + id).show();
							$("#pause_" + id).hide();
							$("#set_timeschedule_" + id).hide();
							$("#set_geo_" + id).hide();
							$("#complete_" + id).hide();
                       	} else {
                           	alert("Error encountered: " + msg.message);
                       	}
                   	}
               	});
           	}
       	});

       	$(".resume-btn").click(function(){
           	var id = $(this).data("id");

           	var cnfrm = confirm("Are you sure you want to RESUME this campaign?");

           	if (cnfrm)  {
               	$.ajax({
                   	url: "/v2/campaign/edit_campaign_status/" + id + "/ACTIVE",
                   	dataType: "json",
                   	success: function(msg)  {
                       	if (msg.status == "SUCCESS")    {
                           	$("#resume_" + id).hide();
                           	$("#pause_" + id).show();
                           	$("#set_timeschedule_" + id).show();
                           	$("#set_geo_" + id).show();
                           	$("#complete_" + id).show();
                       	} else {
                       	    alert("Error encountered: " + msg.message);
                       	}
                    }
               	});
           	}
       	});

       	$(".complete-btn").click(function(){
           	var id = $(this).data("id");

           	var cnfrm = confirm("Are you sure you want to COMPLETE & STOP this campaign?");
			var currentRow = $(this).closest("tr");

           	if (cnfrm)  {
               	$.ajax({
                   	url: "/v2/campaign/make_campaign_completed/" + id,
                   	dataType: "json",
                   	success: function(msg)  {
                       	if (msg.status == "SUCCESS")    {
                           	currentRow.fadeOut("1500"); // make this row disappear
                       	} else {
                       	    alert("Error encountered: " + msg.message);
                       	}
                    }
               	});
           	}
       	});


        setTimeout(function(){
            get_rolling_count(10);
        }, 10000);

        setTimeout(function(){
            get_rolling_count(30);
        }, 30000);

        setTimeout(function(){
        	get_rolling_count(60);
        }, 60000);

        setTimeout(function(){
        	get_rolling_count(360);
        }, 60000);

        setTimeout(function(){
            get_current_spend();
        }, 240000);

        var get_rolling_count = function(duration)  {
            $(".io").each(function(){
                var id = $(this).data("id");
				var status = $(this).data("status");
				if(status == "ACTIVE") {
					$.ajax({
						url: "/v2/campaign/rolling_count/" + id + "/" + duration,
						dataType: "json",
						success: function (msg) {
							if (msg.status == "SUCCESS") {
								$("#" + duration + "min_cnt_" + id).html(msg.count);
								$("#" + duration + "min_cnt_" + id).animateHighlight("#F5A9A9", 1000);
							}
						}
					});
				}
            });
        };

        var get_current_spend = function() {
        	$(".io").each(function(){
                var id = $(this).data("id");
				var status = $(this).data("status");
				if(status == "ACTIVE") {
					$.ajax({
						url: "/v2/campaign/check_cap/" + id,
						dataType: "json",
						success: function (msg) {
							if (msg.status == "SUCCESS") {
								$("#curspend_" + id).html(msg.report.total);
								$("#curspend_" + id).animateHighlight("#F5A9A9", 1000);
							}
						}
					});
				}
            });
        };

        /* run these on page load since these take longer to get data for*/
        //get_rolling_count(30);
        //get_rolling_count(60);
        //get_current_spend();
      </script>

{include file="v2/sections/footer.php"}
