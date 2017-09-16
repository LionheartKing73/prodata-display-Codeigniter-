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
        <div class="span3">
        	<h2>Purchase Traffic</h2>
        	<br/>
        	<h6>Please complete the order form below to make your traffic purchase.</h6>
        	<br/>
        </div>
        <form action="#" method="POST" id="frm_traffic" name="frm_traffic">
        <div class="span6">
        	<div class="">
        		<h3>Your Information</h3>
	        	<table class="table table-bordered table-striped">
	        	<tr>
	        		<td class="span2">Your Name*</td>
	        		<td><input type="text" class="input-large required" id="fullname" name="fullname" value="" placeholder="Full Name" /></td>
	        	</tr>
	        	
	        	<tr>
	        		<td>Company</td>
	        		<td><input type="text" class="input-large" id="company" name="company" value="" placeholder="Company Name" /></td>
	        	</tr>
	        	<tr>
	        		<td>Email*</td>
	        		<td><input type="text" class="input-large required" id="email" name="email" value="" placeholder="Email Address" /></td>
	        	</tr>
	        	<tr>
	        		<td>Phone*</td>
	        		<td><input type="text" class="input-large required" id="phone" name="phone" value="" placeholder="Phone Number" /></td>
	        	</tr>
	        	</table>
        	</div>
        	
        	<hr>
        	
        	<div class="">
        	<h3>Traffic Selection</h3>
        	<table class="table table-bordered table-striped">
        	<tr>
        		<td class="span2">Website URL*</td>
        		<td>
        			<input type="text" name="traffic_url" id="traffic_url" class="input-xlarge required" value="" placeholder="http://" />
        			<br/>
        			We do not provide "adult site" related traffic.
        		</td>
        	</tr>
        	
        	<tr>
        		<td>Campaign Duration*</td>
        		<td>
        			<select name="traffic_duration" id="traffic_duration" class="input-large required">
        				<option value="">Select Duration</option>
        				<option value="1-Day">1-Day</option>
        				<option value="7-Days">7-Days</option>
        				<option value="14-Days">14-Days</option>
        				<option value="21-Days">21-Days</option>
        				<option value="30-Days">30-Days</option>
        				<option value="45-Days">45-Days</option>
        				<option value="60-Days">60-Days</option>
        				<option value="90-Days">90-Days</option>
        				<option value="120-Days">120-Days</option>
        			</select>
        			<br/>
        			Ex: 10000 visitors over 7 days is 1430 visitors per day
        		</td>
        	</tr>
        	
        	<tr>
        		<td>Traffic Quantity*</td>
        		<td>
        			<input type="text" name="traffic_qty" id="traffic_qty" onBlur="updateQuantity();" class="input-large required numbers-only" value="10000" placeholder="Value in 1000's" />
        			<br/>Traffic in increments of 1000 only
        		</td>
        	</tr>
        	
        	<tr>
        		<td>Targeted Country*</td>
        		<td>
        			<select name="traffic_country" id="traffic_country" class="input-large required">
        			<option value="">Select Country</option>
        			<option value="US">United States</option>
        			<option value="CA">Canada</option>
        			<option value="UK">United Kingdom</option>
        			<option value="WorldWide">WorldWide</option>
        			<option value="">---</option>
        			{foreach from=$country item=c}
        				<option value="{$c.name}">{$c.name}</option>
        			{/foreach}
        			</select>
        		</td>
        	</tr>
        	
        	<tr>
        		<td>Traffic Category*</td>
        		<td>
        			<select name="traffic_category" id="traffic_category" onChange="updatePricing();">
        				<option value="">Select Category</option>
        				<option value=''>---</option>
        				<option value='Run of Network'>Run of Network</option>
        				<option value=''>---</option>
						<option value='AAA Top Sites'>AAA Top Sites</option>
						<option value='Automotive'>Automotive</option>
						<option value='Beauty'>Beauty</option>
						<option value='Business'>Business</option>
						<option value='Coupons'>Coupons</option>
						<option value='Education'>Education</option>
						<option value='Electronics'>Electronics</option>
						<option value='Electronics::Computers'>Electronics::Computers</option>
						<option value='Employment'>Employment</option>
						<option value='Entertainment'>Entertainment</option>
						<option value='Family'>Family</option>
						<option value='Family::Kids'>Family::Kids</option>
						<option value='Finance'>Finance</option>
						<option value='Fitness'>Fitness</option>
						<option value='Food'>Food</option>
						<option value='Games'>Games</option>
						<option value='Health'>Health</option>
						<option value='Health::Weight Loss'>Health::Weight Loss</option>
						<option value='Hobbies'>Hobbies</option>
						<option value='Home'>Home</option>
						<option value='Home::Gardening'>Home::Gardening</option>
						<option value='Home::Improvement'>Home::Improvement</option>
						<option value='Insurance'>Insurance</option>
						<option value='Internet'>Internet</option>
						<option value='Law'>Law</option>
						<option value='Media'>Media</option>
						<option value='Medical'>Medical</option>
						<option value='Mobile'>Mobile</option>
						<option value='Music'>Music</option>
						<option value='New Age'>New Age</option>
						<option value='Other'>Other</option>
						<option value='Pets'>Pets</option>
						<option value='Recreation'>Recreation</option>
						<option value='Recreation::Outdoors'>Recreation::Outdoors</option>
						<option value='Relationships'>Relationships</option>
						<option value='Scrapbooking'>Scrapbooking</option>
						<option value='Shopping'>Shopping</option>
						<option value='Shopping::Accessories'>Shopping::Accessories</option>
						<option value='Shopping::Auctions'>Shopping::Auctions</option>
						<option value='Shopping::Clothing'>Shopping::Clothing</option>
						<option value='Sports'>Sports</option>
						<option value='Sports::Football'>Sports::Football</option>
						<option value='Sports::Golf'>Sports::Golf</option>
						<option value='Toys'>Toys</option>
						<option value='Travel'>Travel</option>
        			</select>
        		</td>
        	</tr>
        	<tr id="traffic_keywords_tr">
        		<td>Keywords*</td>
        		<td>
        			<textarea id="traffic_keywords" name="traffic_keywords" cols="40" rows="5" class="input-xlarge required"></textarea>
        			<br/>
        			Keywords are used to further optimize your traffic.  One per line.  Negative keywords, include a "-" before the term.
        		</td>
        	</tr>
        	</table>
        	</div>
        	
        	<div class="well">
        		<h4>Your payment is secured and collected by PayPal.  Your payment will be invoiced by TrafficPing LLC. <a href="/order/tos">Terms of Service</a></h4>
        		<br/>
        		<center><span class="btn btn-success btn-large">Order Now</span></center>
        	</div>
        </div>
        </form>
        
        <div class="span3">
        	<table class="table table-bordered table-striped">
        		<tr>
        			<td>Amount of Traffic: </td>
        			<td id="order_traffic_qty">0</td>
        		</tr>
        		<tr>
        			<td>Cost / CPM:</td>
        			<td id="order_traffic_cpm">0.00</td>
        		</tr>
        		<tr>
        			<td><b>Order Total:</b></td>
        			<td id="order_total">0.00</td>
        	</table>
        </div>
      </div>
      
<script>
function updateTotal()	{
	$("#order_traffic_qty").val($("#traffic_qty").val());
	$("#order_total").val($("#order_traffic_qty").val() * 22);
}

function updatePricing()	{
	if ($("#traffic_category").val() == "Run of Network")	{
		$("#traffic_keywords_tr").hide();
	} else {
		$("#traffic_keywords_tr").show();
	}
	updateTotal();
}

function updateQuantity()	{
	$("#traffic_qty").val(Math.round($("#traffic_qty").val() / 1000) * 1000);
	updateTotal();
}

</script>

{include file="order/sections/footer.php"}