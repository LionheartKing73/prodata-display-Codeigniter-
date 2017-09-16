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
        	<h2>HTML Creative Link Processor &nbsp;&nbsp;<small>Bulk processing of links from HTML creative</small></h2>
	       	<h6>Please paste the source code for the HTML email message below.  When complete click the "Continue" button.  Email content will be updated with rewriten links.</h6>
        	<br/>
	        <form class="well" name="create_form" id="create_form">
        		<table class="table table-striped table-bordered" id="content_table">
        			<tr>
        				<td>Campaign Name*:</td>
        				<td><input type="text" name="create_name" id="create_name" value="{$campaign.name}" class="input-large required" /></td>
        			</tr>
        			<tr id="tr_io">
        				<td>I/O #*:</td>
        				<td><input type="text" name="io" id="io" value="{$campaign.io_number}" class="input-small required" onBlur="checkIO();" maxlength="16" /><div id="duplicate_io_alert" class="alert alert-error" style="display:none;"><b>DUPLICATE IO#</b> Cannot be the same! Try appending an A, B, C, etc.</div><span class='pull-right'>IO must be unique for each campaign (append an 'A', 'B', etc if needed).<br/>MAX 16 Characters.</span></td>
        			</tr>
        			<tr>
        				<td>Default URL*:</td>
        				<td><input type="text" name="default_url" id="default_url" value="" class="input-large required" /><span class='pull-right'>This is the default URL for call to action.</span></td>
        			</tr>
        			<tr>
        				<td>Vendor?</td>
        				<td>
        					<select name="vendor" id="vendor">
        						{foreach from=$vendor item=v}
        							<option value='{$v.id}'>{$v.name}</option>
        						{/foreach}
        					</select>
        				</td>
        			</tr>
        			<tr>
        				<td>Is Geo Targetted?</td>
        				<td>
        					<select name="is_geo" id="is_geo">
        						<option value="N" SELECTED>No</option>
        						<option value="Y">Yes</option>
        					</select>
        				</td>
        			</tr>
        			<tr>
        				<td>Domain Name?</td>
        				<td>
        					<select name="domain" id="domain">
        					{foreach from=$domain item=d}
        						<option value='{$d.id}'>{$d.name}</option>
        					{/foreach}
        					</select>
        				</td>
        			</tr>
        			<tr>
        				<td>Requested Campaign<Br/>Start Date*:</td>
        				<td>
							<div class="input-group">
								<input id="campaign_start_datetime" type="text" value="{$next_date}" class="form-control required">
							    <span class="input-group-btn">
							    	<button id="image_button" class="btn btn-default" type="button"><span class="icon-calendar"></span></button>
								</span>
							</div><!-- /input-group -->
        				</td>
        			</tr>
        			<tr>
        				<td>Required Clicks*</td>
        				<td><input type="text" name="max_clicks" id="max_clicks" value="0" class="input-large required" /> <span class="pull-right">Minimum 500 clicks</span></td>
        			</tr>
        			<tr>
        				<td>Opens*</td>
        				<td><input type="text" name="opens" id="opens" value="0" class="input-large required" /> <span class="pull-right">Used merely for tracking calculations; actual opens should use an open pixel in creative.</span></td>
        			</tr>
        			<tr>
        			     <td>Traffic Shape?</td>
        			     <td>
        			         <select name="is_traffic_shape" id="is_traffic_shape">
        			             <option value="N">No</option>
        			             <option value="Y">Yes</option>
        			         </select>
        			         <span class='pull-right'>Applies a normalized distribution w/ preloaded 10% clicks on first sample</span>
        			     </td>
        			</tr>
        			<tr>
        				<td>Message Content*:<br/><small>(Paste HTML Creative)</small></td>
        				<td><textarea name="message" id="message" rows="25" cols="60" class="span9 required"></textarea></td>
        			</tr>
        			<tr>
        				<td colspan="2">
        					<span class="btn btn-success pull-right generate_content" >Continue</span>
        				</td>
        			</tr>
        		</table>
        		
        		<table class="table table-striped table-bordered" id="content_results" style="display:none;">
        			<tr>
        				<td>Campaign Name:</td>
        				<td id="name_result"></td>
        			</tr>
        			<tr>
        				<td>Generic URL:</td>
        				<td id="url_result"></td>
        			</tr>
        			<tr>
        				<td>I/O #:</td>
        				<td id="io_result"></td>
        			</tr>
        			<tr>
        				<td>Message Content:</td>
        				<td><textarea name="message" id="message_result" rows="30" cols="60" class="span7">{$campaign.message}</textarea></td>
        			</tr>
        		</table>
	        </form>
        </div>
      </div>

<script>
	$(document).ready(function(){
		$(".campaign-create").addClass("active");

		$("#io").keypress(function(key){
			if (key.charCode == 32)	{
				$(this).val($(this).val() + "-");
				return false;
			}

			if (key.charCode == 0)	{
				return true;
			}

			if((key.charCode < 48 || key.charCode > 57) && (key.charCode < 97 || key.charCode > 122) && (key.charCode < 65 || key.charCode > 90) && (key.charCode != 45)) return false;
		});

		// setup date stuff
		var myDate = new Date();
		
		$('#campaign_start_datetime').datetimepicker({
			  format:'Y-m-d H:i',
			  lang:'en',
			  minDate: 0, // we cannot go back in time
			  minTime: 0,
			  step: 15, // 15 minute increments
			  mask: true
		});
		
		$('#image_button').click(function(){
			  $('#campaign_start_datetime').datetimepicker('show'); //support hide,show and destroy command
		});
		
	});
</script>

<script>
	function checkIO()	{
		$.ajax({
			url: "/campclick/check_io/" + $("#io").val(),
			dataType: "json",
			success: function(msg)	{
				if (msg.status == "ERROR")	{
					$(".generate_content").addClass("disabled").attr("disabled", "disabled");
					$("#tr_io").addClass("error");
					$("#duplicate_io_alert").show();
				} else {
					$(".generate_content").removeClass("disabled").removeAttr("disabled");
					$("#tr_io").removeClass("error");
					$("#duplicate_io_alert").hide();
				}
			}
		});
	}
</script>

<script>
	$(".generate_content").click(function(){
		var validate = $("#create_form").validate().form();
		
		if (validate)	{
			$.ajax({
				url: "/campclick/generate_code",
				type: "POST",
				dataType: "json",
				data: { opens: $("#opens").val(), is_traffic_shape: $("#is_traffic_shape").val(), max_clicks: $("#max_clicks").val(), campaign_start_datetime: $("#campaign_start_datetime").val(), vendor: $("#vendor").val(), name: $("#create_name").val(), io: $("#io").val(), message: $("#message").val(), default_url: $("#default_url").val(), domain: $("#domain").val(), is_geo: $("#is_geo").val() },
				success: function(msg)	{
					if (msg.status == "SUCCESS")	{
						$("#content_table").hide();
						$("#message_result").val(msg.message);
						$("#io_result").html($("#io").val());
						$("#url_result").html(msg.url);
						$("#name_result").html($("#create_name").val());
						$("#content_results").show();
					}
				}
			});
		}
	});
</script>

{include file="campclick/sections/footer.php"}
