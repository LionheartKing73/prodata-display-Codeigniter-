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
        	<h2>New Mail Campaign via MailGun</h2>
        	
				<table class="table table-striped table-bordered" id="content_table">
        			<tr id="tr_io">
        				<td>I/O #*:</td>
        				<td><input type="text" name="io" id="io" value="{$campaign.io_number}" class="input-small required" maxlength="16" /><div id="duplicate_io_alert" class="alert alert-error" style="display:none;"><b>DUPLICATE IO#</b> Cannot be the same! Try appending an A, B, C, etc.</div><span class='pull-right'>IO must be unique for each campaign (append an 'A', 'B', etc if needed).<br/>MAX 16 Characters.</span></td>
        			</tr>
        		    <tr>
        				<td>Campaign Name*:</td>
        				<td><input type="text" name="campaign_name" id="campaign_name" value="{$campaign_name}" class="input-large required" /></td>
        			</tr>
        			<tr>
        			    <td>Campaign Vertical*:</td>
        			    <td>
        			         <select name="vertical" id="vertical" class="input-xlarge required">
        			             <option value="">Select Vertical</option>
        			             <option value="adult">Adult (18+ Mature Audience)</option>
        			             <option value="automotive">Automotive</option>
         			             <option value="autointender">Auto Intenders</option>
         			             <option value="autoowner">Auto Owners</option>
        			             <option value="beauty">Beauty</option>
        			             <option value="business">Business</option>
        			             <option value="bigbox">Big-Box Store</option>
         			             <option value="b2b">Business-to-Business</option>
         			             <option value="consumer">Consumer</option>
          			             <option value="coupons">Coupons</option>
        			             <option value="education">Education</option>
        			             <option value="entertainment">Entertainment</option>
        			             <option value="family">Family</option>
        			             <option value="finance">Finance</option>
        			             <option value="fitness">Fitness</option>
        			             <option value="food">Food</option>
        			             <option value="gender">Gender</option>
        			             <option value="health">Health</option>
        			             <option value="home">Home & Garden</option>
        			             <option value="law">Law</option>
        			             <option value="medical">Medical</option>
        			             <option value="music">Music</option>
        			             <option value="parents">Parents</option>
          			             <option value="pets">Pets</option>
        			             <option value="sports">Sports</option>
        			             <option value="toys">Toys</option>
          			             <option value="travel">Travel/Tourism</option>
          			             <option value="misc">Other</option>
     			             </select>
        			    </td>
        			</tr>
        			<tr>
        				<td>From Name:</td>
        				<td><input id="from_name" type="text" value="{$from_name}" class="form-control required" /></td>
        			</tr>
        			<tr>
        				<td>From Email:</td>
        				<td><input id="from_email" type="text" value="{$from_email}" class="form-control required" /></td>
        			</tr>
           			<tr>
        				<td>Subject:</td>
        				<td><input id="subject" type="text" value="{$subject}" class="form-control required" /></td>
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
        			     <td colspan="2">
        			         Creative (HTML)<br/>
                             <textarea name="message_html" id="message_html" style="width:95%; height:400px;" class="input-xlarge"></textarea>
    			         </td>
        			</tr>        			
        		</table>

                <span class="pull-right btn btn-danger" id="trigger-create-order">Next &gt;&gt;</span>
        	
        	
        </div>
      </div>
      
<script>
$(document).ready(function(){
	$("#trigger-create-order").click(function(){
		$.ajax({
			url: "/prodataverify/send/" + $("#io").val(),
			type: "POST",
			dataType: "json",
			data: {
				io: $("#io").val(),
				campaign_name: $("#campaign_name").val(),
				vertical: $("#vertical").val(),
				from_name: $("#from_name").val(),
				from_email: $("#from_email").val(),
				subject: $("#subject").val(),
				send_date: $("#campaign_start_datetime").val(),
				message_html: $("#message_html").val(),
			},
			success: function(msg){
				if (msg.status == "SUCCESS")	{
					alert("Message Scheduled!");
				}
			}
		});
	});
});
</script>
      
{include file="campclick/sections/footer.php"}
