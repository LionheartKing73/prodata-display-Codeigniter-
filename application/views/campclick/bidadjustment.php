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
        	<h2>Campaign Bid Adjustment <small class='pull-right'><i class="icon-ok"></i> indicates campaign started.<br/><i class='icon-star-empty'></i> indicates open pixel campaign<br/>S=State, Z=Zip, C=Country</small></h2>
        	<h6>Active Campaign Count: {$campaigns|count}</h6>
        	<table class="table table-bordered table-striped" id="mytable">
        	<thead>
        		<tr>
        			<th>I/O #</th>
        			<th>Campaign Name</th>
        			<th>% Complete</th>
        			<th>Date Created</th>
        			<th>ClkCap</th>
        			<th>Bid</th>
        			<th>Daily Cap</th>
        		</tr>
        	</thead>
        	<tbody>
        		{foreach from=$campaigns item=c}
	        		<tr id="io_{$c.io}" class="io" data-io="{$c.io}">
	        			<td>{$c.io}<br>{if $c.campaign_is_started == "Y"}<i class="icon-ok"></i>{/if} {if $c.fire_open_pixel == "Y"}<i class='icon-star-empty'></i>{/if}</td>
	        			<td>
	        			    {$c.name} ({if $c.geotype == "country"}C{elseif $c.geotype == "postalcode"}Z{elseif $c.geotype == "state"}S{/if})
	        			    <br/>
	        			    <small><strong>10-Min Cnt:</strong> <span id='10min_cnt_{$c.io}'>0</span></small>
	        			    |
            			    <small><strong>30-Min Cnt:</strong> <span id='30min_cnt_{$c.io}'>0</span></small>
	        			    |
            			    <small><strong>60-Min Cnt:</strong> <span id='60min_cnt_{$c.io}'>0</span></small>
	        			    |
            			    <small><strong>6-Hr Cnt:</strong> <span id='360min_cnt_{$c.io}'>0</span></small>
            			    
	        			    <br/>
	        			    <span class='btn btn-success btn-mini resume-btn' id="resume_{$c.io}" data-io="{$c.io}" {if $c.ppc_network_ad_active == "Y"}style="display:none;"{/if}>Resume</span>
	        			    <span class='btn btn-danger btn-mini pause-btn' id="pause_{$c.io}" data-io="{$c.io}" {if $c.ppc_network_ad_active == "N"}style="display:none;"{/if}>Pause</span>
	        			    <span class='btn btn-success btn-mini complete-btn' id="complete_{$c.io}" data-io="{$c.io}">Complete Campaign</span>
	        			    <span class='btn btn-info btn-mini set-geo-btn' id="set_geo_{$c.io}" data-io="{$c.io}">Set Geo</span>
	        			    <span class='btn btn-info btn-mini clear-time-schedule-btn' id="set_timeschedule_{$c.io}" data-io="{$c.io}" data-ppc-id="{$c.ppc_network_ad_id}">Clear Time Schedule</span>
	        			    
	        			</td>
	        			<td>
	        				<div class="progress progress-striped {$c.slow_performing}">
	        				    {if $c.fire_open_pixel == "N"}
	        					      <div class="bar" style="width:{({$c.total_clicks/$c.max_clicks}*100)}%;">{({$c.total_clicks/$c.max_clicks}*100)|string_format:"%.2f"}%</div>
	        					{else}
	        					      <div class="bar" style="width:{({$c.impression_clicks/$c.max_clicks}*100)}%;">{({$c.impression_clicks/$c.max_clicks}*100)|string_format:"%.2f"}%</div>
	        					{/if}
	        				</div>
	        			</td>
	        			<td>{$c.create_date|date_format:"%Y-%m-%d"}</td>
	        			<td><input type="text" class="input-mini clkcap" id="clkcap_{$c.io}" data-io="{$c.io}" value="{$c.cap_per_hour}" /><br/>{$c.max_clicks}</td>
	        			<td>
	        			    <input type='text' class='input-mini' id='bid_{$c.io}' data-io='{$c.io}' class='bid' value='{$c.bid}'><br/>
	        			    <a href='#' class='bid-up' data-io='{$c.io}' title='Bid UP'><i class='icon-arrow-up'></i></a> &nbsp;|&nbsp; <a href='#' class='bid-down' data-io='{$c.io}' title='Bid DOWN'><i class='icon-arrow-down'></i></a>
	        			</td>
	        			<td>
	        			    <input type='text' class='input-mini' id='daily_cap_{$c.io}' data-io='{$c.io}' class='daily_cap' value='{$c.daily_cap}'><br/><small id="curspend_{$c.io}" data-io="{$c.io}"></small><br/>
	        			    <!-- <span class="btn btn-mini btn-info cap-recalc" data-io="{$c.io}">Rclc</span> |  --><span class="btn btn-mini btn-success cap-save" data-io="{$c.io}">Save</span>
	        			</td>
	        		</tr>
        		{/foreach}
        	</tbody>
        	</table>
        </div>
      </div>

      <script>
      	$(document).ready(function(){
      		{literal}
      		var notLocked = true;
      		$.fn.animateHighlight = function(highlightColor, duration) {
      		    var highlightBg = highlightColor || "#FFFF9C";
      		    var animateMs = duration || 1500;
      		    var originalBg = this.css("backgroundColor");
      		    if (notLocked) {
      		        notLocked = false;
      		        this.stop().css("background-color", highlightBg)
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
				var io = $(this).data("io");
				var currentRow = $(this).closest("tr");

				if (cnfrm)  {
					$.ajax({
						url: "/campclick/clickcap/" + io + "/" + $("#clkcap_" + io).val(),
						dataType: "json",
						success: function(msg){
						    if (msg.status == "SUCCESS")  {
							    $("#clkcap_" + io).val(msg.click_cap);
							    currentRow.animateHighlight("#F5A9A9", 1000);
						    } else {
						    	alert("Error encountered: " + msg.message);
						    }
						}
					});
				}
			});

			$(".set-geo-btn").click(function(){
				document.location.href="/campclick/geolocation/" + $(this).data("io");
			});

			$(".clear-time-schedule-btn").click(function(){
				var ppc_id = $(this).data("ppc-id");
				
				$.ajax({
					url: "/campclick/set_schedule/" + ppc_id,
					dataType: "json",
					success: function(msg){
						alert("Schedule Cleared");
					}
				});
			});
			
			$(".bid-up").click(function(){
				var cnfrm = confirm("Are you sure you want to INCREASE the BID?");
				var currentRow = $(this).closest("tr");
				var io = $(this).data("io");

				if (cnfrm)  {
					$.ajax({
					    url: "/campclick/bid_up/" + io,
					    dataType: "json",
					    success: function(msg) {
						    if (msg.status == "SUCCESS")  {
							    $("#bid_" + io).val(msg.bid);
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
				var io = $(this).data("io");
				var currentRow = $(this).closest("tr");

				if (cnfrm)  {
					$.ajax({
					    url: "/campclick/bid_down/" + $(this).data("io"),
					    dataType: "json",
					    success: function(msg) {
						    if (msg.status == "SUCCESS")  {
							    $("#bid_" + io).val(msg.bid);
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
			var io = $(this).data("io");
			var currentRow = $(this).closest("tr");

			if (cnfrm)  {
				$.ajax({
				    url: "/campclick/cap_save",
				    type: "POST",
				    dataType: "json",
				    data: { 
				        'io': io, 
				        'cap': $("#daily_cap_" + io).val()
			        },
				    success: function(msg) {
					    if (msg.status == "SUCCESS")  {
						    $("#daily_cap_" + io).val(msg.cap);
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
           	var io = $(this).data("io");

           	var cnfrm = confirm("Are you sure you want to PAUSE this campaign?");

           	if (cnfrm)  {
               	$.ajax({
                   	url: "/campclick/campaign_pause/" + io,
                   	dataType: "json",
                   	success: function(msg)  {
                       	if (msg.status == "SUCCESS")    {
                           	$("#pause_" + io).hide();
                           	$("#resume_" + io).show();
                       	} else {
                           	alert("Error encountered: " + msg.message);
                       	}
                   	}
               	});
           	}
       	});

       	$(".resume-btn").click(function(){
           	var io = $(this).data("io");

           	var cnfrm = confirm("Are you sure you want to RESUME this campaign?");

           	if (cnfrm)  {
               	$.ajax({
                   	url: "/campclick/campaign_resume/" + io,
                   	dataType: "json",
                   	success: function(msg)  {
                       	if (msg.status == "SUCCESS")    {
                           	$("#resume_" + io).hide();
                           	$("#pause_" + io).show();
                       	} else {
                       	    alert("Error encountered: " + msg.message);
                       	}
                    }
               	});
           	}
       	});

       	$(".complete-btn").click(function(){
           	var io = $(this).data("io");

           	var cnfrm = confirm("Are you sure you want to COMPLETE & STOP this campaign?");
			var currentRow = $(this).closest("tr");
           	
           	if (cnfrm)  {
               	$.ajax({
                   	url: "/campclick/campaign_complete/" + io,
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
                var io = $(this).data("io");

                $.ajax({
                    url: "/campclick/rolling_count/" + io + "/" + duration,
                    dataType: "json",
                    success: function(msg)  {
                        if (msg.status == "SUCCESS")    {
                            $("#" + duration + "min_cnt_" + io).html(msg.count);
                            $("#" + duration + "min_cnt_" + io).animateHighlight("#F5A9A9", 1000);
                        }
                    }
                });
            });
        };

        var get_current_spend = function() {
        	$(".io").each(function(){
                var io = $(this).data("io");

                $.ajax({
                    url: "/campclick/check_cap/" + io,
                    dataType: "json",
                    success: function(msg)  {
                        if (msg.status == "SUCCESS")    {
                            $("#curspend_" + io).html(msg.report.total);
                            $("#curspend_" + io).animateHighlight("#F5A9A9", 1000);
                        }
                    }
                });
            });
        };

        /* run these on page load since these take longer to get data for*/
        //get_rolling_count(30);
        //get_rolling_count(60);
        //get_current_spend();
      </script>

{include file="campclick/sections/footer.php"}
