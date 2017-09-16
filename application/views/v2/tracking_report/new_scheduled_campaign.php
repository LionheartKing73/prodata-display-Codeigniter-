{include file="v2/tracking_report/header.php"}

<link href="/v2/css/datetime-picker.css" rel="stylesheet" type="text/css"/>

<link href="http://reporting.prodata.media/public/plupload/css/jquery.ui.plupload.css" rel="stylesheet" type="text/css"/>

<page size="CUSTOM_LONG_A4">
	<form action="#" method="post" name="campaign_form" id="campaign_form">
	<input type="hidden" name="campaign_id" id="campaign_id" value="{$campaign_id}" />

	<h1 align="center" class="pretty-font">ProData Media - Create New Campaign</h1>

	<table class="pretty-font">
	
		<tr>
			<td>Select Client:</td>
			<td>
				<select name="client_id" id="client_id" class="required">
					<option value="">Select Client</option>
					{foreach from=$clients item=c}
						<option value="{$c.id}" {if $c.id == $campaign.client_id}SELECTED{/if}>{$c.company}</option>
					{/foreach}
				</select> | <span class='btn-small' id="btn_new_client">New Client</span>
			</td>
		</tr>
		
		<tr>
			<td>Select Sales Rep:</td>
			<td>
				<select name="sales_rep_id" id="sales_rep_id" class="required">
					<option value="">Select Sales Rep</option>
					{foreach from=$salesreps item=s}
						<option value="{$s.id}" {if $s.id == $campaign.sales_rep_id}SELECTED{/if}>{$s.lname}, {$s.fname}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		
		<tr>
			<td>Insertion Order ID:</td>
			<td>
				<input type="text" name="io" id="io" class="required" value="{$campaign.io}" />
			</td>
		</tr>
	
		<tr>
			<td>Campaign Name:</td>
			<td>
				<input type="text" name="campaign_name" id="campaign_name" style="width:100%" class="required" value="{$campaign.name}" />
			</td>
		</tr>
	
		<tr>
			<td>Campaign Dates:</td>
			<td>
				<input type="text" class="date_picker" name="date_start" id="date_start" class="required" value="{$campaign.date_start}" />
				-to-
				<input type="text" class="date_picker" name="date_end" id="date_end" class="required" value="{$campaign.date_end}" />
			</td>
		</tr>
		
		{if $campaign_id > 0}
		<tr valign="top">
			<td>Campaign Assets:</td>
			<td>
				<div id="uploader" class="dropzone">
					<p>Your browser doesnt have flash, silverlight or html5 support</p>
				</div>
			</td>
		</tr>
		{if $files}
		<tr valign="top">
			<td>Campaign Files:</td>
			<td>
				<table>
					<tr bgcolor="#DDDDDD" style="font-size:10px;">
						<td>Name</td>
						<td>Size</td>
						<td>Datetime</td>
						<td></td>

					</tr>
					{foreach from=$files item=c}
						<tr style="font-size:10px;">
							<td><a href='/uploads/trkreports/{$c.name}' target='_blank'>{$c.original_name}</a></td>
							<td>{$c.size}</td>
							<td>{$c.datetime}</td>
							<td><button class="remove-trk-file btn-danger">X</button></td>
						</tr>
					{/foreach}
				</table>
			</td>
		</tr>
		{/if}
		{/if}
		<tr valign="top">
			<td>Geographic Targeting Information:</td>
			<td>
				<textarea id="geo_targeting" name="geo_targeting" style="width:100%;height:60px;" class="required">{$campaign.geo_targeting}</textarea>
				<br/>
				<select name="radius" id="radius">
					<option value="">Select Radius (Zip/DMA Only)</option>
					{foreach from=$radius item=r}
						<option value="{$r}" {if $r == $campaign.radius}SELECTED{/if}>{$r} Miles</option>
					{/foreach}
				</select>
			</td>
		</tr>
		
		<tr valign="top">
			<td>Demographic Targeting Information:</td>
			<td>
				<textarea id="demo_targeting" name="demo_targeting" style="width:100%;height:60px;" class="required">{$campaign.demo_targeting}</textarea>
			</td>
		</tr>
		
		<tr valign="top">
			<td>Channels?</td>
			<td>
				<div class="legend-row">
					<div class="legend-block">
						<input type="checkbox" id="chk_channel_email" {if $campaign.channel_email == "Y"}CHECKED{/if} />
						<label for="chk_channel_email"><img src="/v2/report-icons/CHANNEL_EMAIL.png" class="status-active" /><br/><span>Email</span></label>
					</div>
					<div class="legend-block">
						<input type="checkbox" id="chk_channel_display" {if $campaign.channel_display == "Y"}CHECKED{/if} />
						<label for="chk_channel_display"><img src="/v2/report-icons/CHANNEL_DISPLAY.png" class="status-active" /><br/><span>Display</span></label>
					</div>
					<div class="legend-block">
    					<input type="checkbox" id="chk_channel_retarget" {if $campaign.channel_retarget == "Y"}CHECKED{/if} />
						<label for="chk_channel_retarget"><img src="/v2/report-icons/CHANNEL_RETARGET.png" class="status-active" /><br/><span>Retargeting</span></label>
					</div>
					<div class="legend-block">
						<input type="checkbox" id="chk_channel_social" {if $campaign.channel_sociall == "Y"}CHECKED{/if} />
						<label for="chk_channel_social"><img src="/v2/report-icons/CHANNEL_SOCIAL.png" class="status-active" /><br/><span>Social</span></label>
					</div>
				</div>
			</td>
		</tr>
		
		<tr valign="top">
			<td>Campaign Specifics:</td>
			<td>
				<div class="row" id="campaign_email_from">
					<div class="column-quarter">From Name: </div>
					<div class="column-3quarter"><input type="text" name="email_from_name" id="email_from_name" style="width:100%;" value="{$campaign.email_from_name}" /></div>
				</div>
				
				<div class="row" id="campaign_email_subject">
					<div class="column-quarter">Subject: </div>
					<div class="column-3quarter"><input type="text" name="email_subject" id="email_subject" style="width:100%;" value="{$campaign.email_subject}" /></div>
				</div>
				
				<div class="row" id="campaign_email_listcount">
					<div class="column-quarter">List Count: </div>
					<div class="column-3quarter"><input type="text" name="email_count" id="email_count" value="{$campaign.email_count}" /></div>
				</div>
				
				<div class="row" id="campaign_email_click_open">
					<div class="column-quarter">Click &amp; Open %: </div>
					<div class="column-3quarter"><input type="text" name="email_click" id="email_click" placeholder="Click%" value="{$campaign.email_click}" /> <input type="text" name="email_open" id="email_open" placeholder="Open%" value="{$campaign.email_open}" /></div>
				</div>
				
				<div class="row" id="campaign_email_divider"><hr /></div>
				
				
				<div class="row" id="campaign_display_impressions">
					<div class="column-quarter">Impressions: </div>
					<div class="column-3quarter"><input type="text" name="display_impressions" id="display_impressions" placeholder="Req. Imp. Count" value="{$campaign.display_impressions}" /></div>
				</div>
				
				<div class="row" id="campaign_display_clicks">
					<div class="column-quarter">Click: </div>
					<div class="column-3quarter"><input type="text" name="display_clicks" id="display_clicks" placeholder="Req. Click %" value="{$campaign.display_clicks}" /></div>
				</div>
				
				<div class="row" id="campaign_display_divider"><hr /></div>
			</td>
		</tr>
		
		<tr>
			<td>Status?</td>
			<td>
				<div class="legend-row">
    				<div class="legend-block">
    					<input type="checkbox" id="chk_status_mih" {if $campaign.status_money_in_house == "Y"}CHECKED{/if} />
						<label for="chk_status_mih"><img src="/v2/report-icons/NEW_STATUS_MONEY_IN_HOUSE.png" class="status-active" /><br/><span>Money In House</span></label>
					</div>
    				<div class="legend-block">
    					<input type="checkbox" id="chk_status_creative_approved" {if $campaign.status_creative_approved == "Y"}CHECKED{/if} />
						<label for="chk_status_creative_approved"><img src="/v2/report-icons/NEW_STATUS_CREATIVE_APPROVED.png" class="status-active" /><br/><span>Creative Approved</span></label>
					</div>
    				<div class="legend-block">
    					<input type="checkbox" id="chk_status_client_approved" {if $campaign.status_client_approved == "Y"}CHECKED{/if} />
						<label for="chk_status_client_approved"><img src="/v2/report-icons/NEW_STATUS_CLIENT_APPROVED.png" class="status-active" /><br/><span>Client Approved</span></label>
					</div>
    				<div class="legend-block">
    					<input type="checkbox" id="chk_status_deployed" {if $campaign.status_deployed == "Y"}CHECKED{/if} />
						<label for="chk_status_deployed"><img src="/v2/report-icons/NEW_STATUS_DEPLOYED.png" class="status-active" /><br/><span>Campaign Deployed</span></label>
					</div>
				</div>
			</td>
		</tr>
		
		<tr valign="top">
			<td>Notes:</td>
			<td>
				<textarea id="notes" name="notes" style="width:100%;height:60px;">{$campaign.notes}</textarea>
			</td>
		</tr>
		
		<tr valign="top">
			<td>Gross Budget:</td>
			<td><input type="text" name="budget_gross" id="budget_gross" value="{$campaign.budget_gross}" class="required" /></td>
		</tr>
		<tr valign="top">
			<td>Ad Spend Budget:</td>
			<td><input type="text" name="budget_adspend" id="budget_adspend" value="{$campaign.budget_adspend}" class="required" /> <span id="adspend_percentage">0</span>%</td>
		</tr>
		
	</table>

	<div class="btn center" style="width:100px;text-align:center;" id="btn_save">Save</div>
	</form>
</page>

<div id="modal_new_client" class="small-pretty-font" style="display:none;">
	<form action="#" method="#" name="new_client_form" id="new_client_form">
	<div class="row">
		<h1>Create New Client</h1>
	</div>
	<div class="row">
		<div class="column-quarter">Company Name</div>
		<div class="column-3quarters"><input type="text" name="client_company" id="client_company" class="required" /></div>
	</div>
		<div class="row">
		<div class="column-quarter">First Name</div>
		<div class="column-3quarters"><input type="text" name="client_fname" id="client_fname" class="required" /></div>
	</div>
		<div class="row">
		<div class="column-quarter">Last Name</div>
		<div class="column-3quarters"><input type="text" name="client_lname" id="client_lname" class="required" /></div>
	</div>
	<div class="row">
		<div class="column-quarter">Email</div>
		<div class="column-3quarters"><input type="text" name="client_email" id="client_email" class="required" /></div>
	</div>
	<div class="row">
		<div class="column-quarter">Phone</div>
		<div class="column-3quarters"><input type="text" name="client_phone" id="client_phone" class="required" /></div>
	</div>
	<div class="row">
		<div class="column-quarter">Address</div>
		<div class="column-3quarters"><input type="text" name="client_address" id="client_address" class="required" /></div>
	</div>
	<div class="row">
		<div class="column-quarter">City</div>
		<div class="column-3quarters"><input type="text" name="client_city" id="client_city" class="required" /></div>
	</div>
	<div class="row">
		<div class="column-quarter">State</div>
		<div class="column-3quarters">
			<select name="client_state" id="client_state" class="required" >
    			<option value="">Select State</option>
    			{foreach from=$states item=s}
    				<option value="{$s.state}">{$s.name}</option>
    			{/foreach}
			</select>
		</div>
	</div>
	<div class="row">
		<div class="column-quarter">Postal Code</div>
		<div class="column-3quarters"><input type="text" name="client_zip" id="client_zip" class="required" /></div>
	</div>
	
	<div class="row">
		<br/><br/>
		<span class="btn center" id="btn_client_save">Save</span>
	</div>
	</form>
</div>

<script src="/v2/js/jquery-2.0.3.min.js"></script>
<script src="/v2/js/datetime-picker.jquery.js"></script>
<script src="/public/plupload/plupload.full.min.js"></script>
<script src="/public/plupload/jquery.plupload.queue.js"></script>
<link type="text/css" rel="stylesheet" href="http://www.plupload.com/plupload//js/jquery.plupload.queue/css/jquery.plupload.queue.css" media="screen" />
<script src="/v2/js/jquery.validate.min.js"></script>
<link type="text/css" rel="stylesheet" href="/v2/css/jquery.modal.css" media="screen" />
<script src="/v2/js/jquery.modal.js"></script>

<script>
{literal}
$(document).ready(function(){
	$(".date_picker").datetimepicker({
		format: "Y/m/d H:i",
		//minDate: '-1970/01/8',
		//maxDate: '+1970/01/1',
	});

	$("#btn_new_client").click(function(){
		$("#modal_new_client").modal();
	});

	$("#btn_client_save").click(function(){
		var validation = $("#new_client_form").validate().form();

		if (validation) {
			
    		$.ajax({
    			url: "/v2/trkreport/client_create",
    			type: "post",
    			dataType: "json",
    			data: {
    				company: $("#client_company").val(),
    				first_name: $("#client_fname").val(),
    				last_name: $("#client_lname").val(),
    				email: $("#client_email").val(),
    				phone: $("#client_phone").val(),
    				address: $("#client_address").val(),
    				city: $("#client_city").val(),
    				state: $("#client_state").val(),
    				zip: $("#client_zip").val(),
    			},
    			success: function(msg){
    				if (msg.status == "SUCCESS") {
    					$("#client_id").append("<option value='" + msg.client_id + "' selected>" + $("#client_company").val() + "</option>");
    					$.modal.close();
    				} else {
    					alert("Error on creation. Try again.");
    				}
    			}
    		});
		}
	});
	
	$("#btn_save").click(function(){
		var validation = $("#campaign_form").validate().form();
		
		if (validation) {
			
    		$.ajax({
    			url: "/v2/trkreport/campaign_save/" + $("#campaign_id").val(),
    			type: "post",
    			dataType: "json",
    			data: {
    				campaign_id: $("#campaign_id").val(),
    				client_id: $("#client_id").val(),
    				io: $("#io").val(),
    				campaign_name: $("#campaign_name").val(),
    				date_start: $("#date_start").val(),
    				date_end: $("#date_end").val(),
    				geo_targeting: $("#geo_targeting").val(),
    				radius: $("#radius").val(),
    				demo_targeting: $("#demo_targeting").val(),
    				channel_email: $("#chk_channel_email").is(":checked") ? "Y" : "N",
    				channel_display: $("#chk_channel_display").is(":checked") ? "Y" : "N",
    				channel_retarget: $("#chk_channel_retarget").is(":checked") ? "Y" : "N",
    				channel_social: $("#chk_channel_social").is(":checked") ? "Y" : "N",
    				status_money_in_house: $("#chk_status_mih").is(":checked") ? "Y" : "N",
    				status_creative_approved: $("#chk_status_creative_approved").is(":checked") ? "Y" : "N",
    				status_client_approved: $("#chk_status_client_approved").is(":checked") ? "Y" : "N",
    				status_deployed: $("#chk_status_deployed").is(":checked") ? "Y" : "N",
    				notes: $("#notes").val(),
    				budget_gross: $("#budget_gross").val(),
    				budget_adspend: $("#budget_adspend").val(),
    				email_from_name: $("#email_from_name").val(),
    				email_subject: $("#email_subject").val(),
    				email_count: $("#email_count").val(),
    				email_click: $("#email_click").val(),
    				email_open: $("#email_open").val(),
    				display_impressions: $("#display_impressions").val(),
    				display_clicks: $("#display_clicks").val(),
					sales_rep_id: $("#sales_rep_id").val(),
    			},
    			success: function(msg){
        			if (msg.status == "SUCCESS") {
            			document.location.href="/v2/trkreport/campaign/" + msg.campaign_id;
        			} else {
            			alert("Sorry! An error occurred. Please try again.");
        			}
    			}
    		});
		}
	});
	

	$("#uploader").pluploadQueue({
		url: "/v2/trkreport/file_upload/" + $("#campaign_id").val(),
		dragdrop: true,
		filters: {
			mime_types: [
		          {title: "Image files", extensions: "jpg,gif,png,jpeg"},
		          {title: "Email files", extensions: "html,htm"},
		          {title: "Data files", extensions: "csv,xls,xlsx"},
		          {title: "Zip files", extensions: "zip,tar,tgz"},
		          {title: "Video files", extensions: "avi,mov,mp4,wmv"},
  			],
  			max_file_size: '500mb'
  		},
  		rename: true
	});
	var uploader = $('#uploader').pluploadQueue();

	uploader.bind('FileUploaded', function (upldr, file, object) {

		var response;
		try {
			response = eval(object.response);
		}
		catch (err) {
			response = eval('(' + object.response + ')');
		}

//		if(response.status){
//
//			$('#examplte_show_div img').attr('src', response.file_dir);
//
//			ad_image = response.file_dir;
//		}
//		else {
			console.log(response);
//		}
	});
});
{/literal}
</script>

{include file="v2/tracking_report/footer.php"}