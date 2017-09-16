{include file="v2/campaign/sections/header.php"}

<style>
html { height: 100% }
body { height: 100% }
#map-canvas { height: 30% }

#map-canvas img {
  max-width: none;
}

#map-canvas label { 
  width: auto; display:inline; 
}
</style>
<script src="https://maps.google.com/maps/api/js?sensor=false"></script>

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
        	<h2>Geo-Location Tool &nbsp;&nbsp;<small>Radius search of matching geo-locations by zipcode</small></h2>
        	<br/>
	        <form class="form-horizontal" name="create_form" id="create_form">
        		<table class="table table-striped table-bordered" id="content_table">
        			<tr>
        				<td>
        				    <input type="text" name="zipcode" id="zipcode" value="{$selected_campaign.zip}" class="input-xxlarge required" placeholder="Enter Zip - Space separated for multi" />
        				     
        				     &nbsp;&nbsp;
        				     
        				    <select name="radius" id="radius" class="input-medium">
        				        <option value="">Select Radius</option>
        				        <option value="10" {if $selected_campaign.radius == 10} selected {/if}>10-Miles</option>
        				        <option value="15" {if $selected_campaign.radius == 15} selected {/if}>15-Miles</option>
        				        <option value="25" {if $selected_campaign.radius == 25} selected {/if}>25-Miles</option>
        				        <option value="50" {if $selected_campaign.radius == 50} selected {/if}>50-Miles</option>
        				        <option value="75" {if $selected_campaign.radius == 75} selected {/if}>75-Miles</option>
        				        <option value="100" {if $selected_campaign.radius == 100} selected {/if}>100-Miles</option>
        				        <option value="125" {if $selected_campaign.radius == 125} selected {/if}>125-Miles</option>
        				     </select>
        				     
        				     &nbsp;&nbsp;
        				     
        				     <span class="btn btn-success" id="btnSubmit">Search Geo-Radius</span>
        				</td>
        			</tr>
        		</table>
	        </form>
	        <hr>
        </div>
    <!--  google maps stuff -->
            <div id="map-canvas" class="span12" style="height:500px;"></div>
            <hr>
        </div>
            <div id="locations"></div>
        
            <br clear="all"/>
        
            <div class="well form-horizontal" id="apply_location_to_ad">
                <h6>Select the "Ad" in which to apply the above geo-radius search.  This will OVERWRITE all existing geo-targeting for campaign.</h6>
                <br/>
			    <select name="ad" id="campaign" class="input-xlarge">
			    {foreach from=$campaigns item=campaign}
                    {if $selected_campaign.id == $campaign.id}
			            <option value="{$campaign.id}" selected>{$campaign.name}</option>
                    {else}
                        <option value="{$campaign.id}">{$campaign.name}</option>
                    {/if}
			    {/foreach}
			    </select>
			    
			    &nbsp;&nbsp;
			    
			    <span class="btn btn-success" id="campaign_target_btn">Apply Changes</span>
            </div>

<script>
    var target_data;

    function initialize(msg) {
        var mapOptions = {
              zoom: 4,
              center: new google.maps.LatLng(37.09024, -95.712891),
              mapTypeId: google.maps.MapTypeId.TERRAIN
        };

        var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

        $("#locations").empty();

        for(var city in msg.locations)    {
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(msg.locations[city].latitude, msg.locations[city].longitude),
                setMap: map,
                title: city.city,
                visible: true,
            });

            $("#locations").append("<span class='label' style='padding:10px; margin:5px;'>" + msg.locations[city].city + ", " + msg.locations[city].state + "</span>");
        }

        for(var city in msg.source_location)    {
            var circle = new google.maps.Circle({
                map: map,
                clickable: false,
                //radius: msg.source_location[city].radius * 2400,
                radius: (1609.34 * msg.source_location[city].radius),
                fillColor: '#ff0000',
                fillOpacity: 0.6,
                strokeColor: '#ff0000',
                strokeOpacity: .4,
                strokeWeight: .8
            });

            var markerCenter = new google.maps.Marker({
                position: new google.maps.LatLng(msg.source_location[city].latitude, msg.source_location[city].longitude),
                title: "Location",
                map: map,
                draggable: false
            });
            circle.bindTo('center', markerCenter, 'position');
        }
    }

	$(document).ready(function(){

		$("#campaign").on("change", function(){
			  $.ajax({
				   url: "/v2/campaign/geolocation_ad/" + $(this).val(),
				   dataType: "json",
				   success: function(msg)   {
					   if (msg.status == "SUCCESS")    {
						   $("#zipcode").val(msg.zip);
						   $("#radius").val(msg.radius);
					   }
				   }
			  });
		});
			
		$("#btnSubmit").click(function(){
			$.ajax({
			    url: "/v2/campaign/geolocation_ajax",
			    type: "POST",
			    dataType: "json",
			    data: {
				    zip: $("#zipcode").val(),
				    radius: $("#radius").val()
			    },
			    success: function(msg)   {
				    if (Object.size(msg.locations) > 0)   {
				    	// populate the data
				    	target_data = msg.locations;;
					    // initialize the map
					    google.maps.event.addDomListener(window, 'load', initialize(msg));

				    } else {
					    alert("Sorry, no matching locations");
				    }
			    }
			});
		});

		$("#campaign_target_btn").click(function(){
			var final_targets = [];
			
			$.each(target_data, function(index, value){
				var target = target_data[index];
				if (target.final_tgt != "//")   {
					final_targets.push(target.final_tgt);
				}
    		});

			$.ajax({
				url: "/v2/campaign/edit_location",
				type: "POST",
				dataType: "json",
				data: {
					targets: final_targets,
					campaign_id: MY_SELECTED_ID,
					zip: $("#zipcode").val(),
					radius: $("#radius").val(),
                    geotype: 'postalcode'
				},
				success: function(msg)  {
                    if(msg.status=='SUCCESS') {
                        alert(msg.message);
                    } else {
                        alert(msg.message);
                    }
				}
			});
		});

        $("#btnSubmit").click()

	});

	Object.size = function(obj) {
	    var size = 0, key;
	    for (key in obj) {
	        if (obj.hasOwnProperty(key)) size++;
	    }
	    return size;
	};
</script>

{include file="v2/campaign/sections/footer.php"}
