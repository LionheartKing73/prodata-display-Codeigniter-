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
        	<h2>New Campaign Queue &nbsp;&nbsp;<small>Review &amp; Approve Campaigns</small> <span class='pull-right btn btn-success' style='margin-top:10px;' onClick='document.location.href="/take5/newcampaign"'>Create Campaign</span></h2>
        	
        	<ul class="nav nav-tabs" id="campaignTabs">
        		<li class="active"><a href="#in-progress" data-toggle="tab">Pending Approval</a></li>
        		<li class=""><a href="#approved" data-toggle="tab">Approved Campaigns</a></li>
        		</ul>
        	
        	<div class="tab-content">
        	    <div class="tab-pane" id="approved">
        	        <table class="table table-bordered table-striped" id="mytable-approved">
		        	<thead>
		        		<tr>
		        			<th>I/O #</th>
		        			<th>Campaign Name</th>
		        			<th>Date Scheduled</th>
		        			<th>Ordered Clicks</th>
		        			<th>Budget</th>
		        		</tr>
		        	</thead>
		        	<tbody>
		        		{foreach from=$campaigns.approved item=c}
			        		<tr id="io_{$c.io}">
			        			<td>{$c.io}</td>
			        			<td>{$c.create_name}</td>
			        			<td>{$c.campaign_start_datetime}</td>
			        			<td>{$c.max_clicks}</td>
			        			<td>$ {$c.budget}</td>
			        		</tr>
		        		{/foreach}
		        	</tbody>
		        	</table>        			
        	       
        	    </div>
        	    
        		<div class="tab-pane active" id="in-progress">
        			<table class="table table-bordered table-striped" id="mytable-inprogress">
		        	<thead>
		        		<tr>
		        			<th>I/O #</th>
		        			<th>Campaign Name</th>
		        			<th>Date Scheduled</th>
		        			<th>Ordered Clicks</th>
		        			<th>Budget</th>
		        			<th>&nbsp;</th>
		        		</tr>
		        	</thead>
		        	<tbody>
		        		{foreach from=$campaigns.pending item=c}
			        		<tr id="io_{$c.io}">
			        			<td>{$c.io}</td>
			        			<td>{$c.create_name}</td>
			        			<td>{$c.campaign_start_datetime}</td>
			        			<td>{$c.max_clicks}</td>
			        			<td>$ {$c.budget}</td>
			        			<td><a href='#' class='pending-info' data-id='{$c.id}'><i class='icon-info-sign'></i></a> &nbsp;|&nbsp; <a href='#' class='pending-accept' data-id='{$c.id}'><i class='icon-ok-circle'></i></a> &nbsp;|&nbsp; <a href='#' class='pending-remove' data-id='{$c.id}'><i class='icon-ban-circle'></i></a></td>
			        		</tr>
			        		<tr id="io_moreinfo_{$c.id}" style="display:none;">
			        		   <td colspan="6">
			        		       <!--  put order details here  -->
			        		       <table style="width:99%;">
			        		           <tr>
			        		               <td width="20%"><strong>Total Records</strong><br/><span id="total_records_{$c.id}">-</span></td>
			        		               <td width="20%"><strong>Total Opens</strong><br/><span id="total_opens_{$c.id}">-</span></td>
			        		               <td width="20%"><strong>Total Bounces</strong><br/><span id="total_bounces_{$c.id}">-</span></td>
			        		               <td width="20%"><strong>Vertical</strong><br/><span id="vertical_{$c.id}">-</span></td>
	        		                   </tr>
			        		           <tr>
			        		               <td><strong>Campaign Link(s)</strong></td>
		        		                   <td colspan="3" id="links_{$c.id}">-</td>
		        		               </tr>
	        		                   <tr>
			        		               <td><strong>Geo-Targeting</strong></td>
			        		               <td colspan="3" id="geo_{$c.id}" style="word-wrap: break-word;">-</td>
	        		                   </tr>
			        		           <tr>
			        		               <td><strong>Open Pixel(s)</strong></td>
		        		                   <td colspan="3" id="open_pixel_{$c.id}">-</td>
		        		               </tr>
			        		           <tr>
			        		               <td><strong>Special Instructions</strong></td>
			        		               <td colspan="3" id="special_instructions_{$c.id}" class="edit">-</td>
		        		               </tr>
		        		               
			        		       </table>
			        		   </td>
			        		</tr>
		        		{/foreach}
		        	</tbody>
		        	</table>        			
        		</div>
        	</div>
        </div>
      </div>
      
<script src="/static/js/jquery.editable.js"></script>
<script>
$(document).ready(function(){
    $(".pending-accept").click(function(){
        if (confirm("Are you sure you wish to place this campaign LIVE?"))  {
            $.ajax({
                url: "/take5/accept_order_request/" + $(this).data("id"),
                dataType: "json",
                success: function(msg)  {
                    document.location.reload(true);
                }
            });
        }
    });

    $(".pending-remove").click(function(){
        if (confirm("Are you sure you wish to REMOVE this campaign?"))  {
            $.ajax({
                url: "/take5/remove_order_request/" + $(this).data("id"),
                dataType: "json",
                success: function(msg)  {
                    document.location.reload(true);
                }
            });
        }
    });

    $(".pending-info").click(function(){
        var id = $(this).data("id");
        
        $.ajax({
            url: "/take5/get_pending_campaign/" + $(this).data("id"),
            dataType: "json",
            success: function(msg)  {
                if (msg.status == "SUCCESS")    {
                    $("#total_records_" + id).html(msg.data.total_records);
                    $("#total_opens_" + id).html(msg.data.total_opens);
                    $("#total_bounces_" + id).html(msg.data.total_bounces);
                    $("#vertical_" + id).html(msg.data.vertical);
                    
                    $("#special_instructions_" + id).html(msg.data.special_instructions);

                    if (msg.data.geotype == "postalcode")  {
                        $("#geo_" + id).html("Postal Code: " + msg.data.zip + " (" + msg.data.radius + " mi)");
                    } else if (msg.data.geotype == "country")  {
                        $("#geo_" + id).html("Country: " + msg.data.country);
                    } else if (msg.data.geotype == "state")    {
                        $("#geo_" + id).html("State: " + msg.data.state);
                    } else {
                        $("#geo_" + id).html("Invalid Geo Targeting Set");
                    }

                    var links = "<div style='font-size:8px;'>";
                    $.each(msg.data.links, function(i, obj){
                        links += obj.original_url + " (" + obj.click_count + ")<br/>"; 
                    });
                    links += "</div>";
                    $("#links_" + id).html(links);

                    var pixel = "<div style='font-size:8px;'>";
                    if (msg.data.fire_open_pixel == "Y")    {
                        pixel += "Fire Open Pixel: YES<br/><br/>";
                        $.each(msg.data.open_pixel, function(i, obj){
                            if (obj.pixel_url != "") {
                                pixel += obj.pixel_url + "<br/>";
                            }
                        });
                    } else {
                        pixel += "Fire Open Pixel: NO";
                    }
                    pixel += "</div>";
                    $("#open_pixel_" + id).html(pixel);
                    
                    $("#io_moreinfo_" + id).toggle();
                }
            }
        });
    });
});
</script>
      
{include file="campclick/sections/footer.php"}
