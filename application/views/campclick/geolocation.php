{include file="campclick/sections/header.php"}

<script>
	var MY_SELECTED_IO = "{$io}";
	var MY_SELECTED_ADID = "{$adid}";
</script>

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
        				    <input type="text" name="zipcode" id="zipcode" value="" class="input-xxlarge required" placeholder="Enter Zip - Space separated for multi" />
        				     
        				     &nbsp;&nbsp;
        				     
        				    <select name="radius" id="radius" class="input-medium">
        				        <option value="">Select Radius</option>
        				        <option value="10">10-Miles</option>
        				        <option value="15">15-Miles</option>
        				        <option value="25">25-Miles</option>
        				        <option value="50">50-Miles</option>
        				        <option value="75">75-Miles</option>
        				        <option value="100">100-Miles</option>
        				        <option value="150">150-Miles</option>
        				        <option value="200">200-Miles</option>
        				        <option value="250">250-Miles</option>
        				        <option value="350">350-Miles</option>
        				        <option value="500">500-Miles</option>
        				        <option value="1000">1000-Miles</option>
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
        
            <div class="well span12 form-horizontal" id="apply_location_to_ad">
                <h6>Select the "Ad" in which to apply the above geo-radius search.  This will OVERWRITE all existing geo-targeting for campaign.</h6>
                <br/>
			    <select name="ad" id="ad" class="input-xlarge">
			    {foreach from=$ads item=a}
			        <option value="{$a.id}">{$a.name}</option>
			    {/foreach}
			    </select>
			    
			    &nbsp;&nbsp;
			    
			    <span class="btn btn-success" id="ad_target_btn">Apply Changes</span>
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

</script>
<script>

	$(document).ready(function(){

		$("#ad").on("change", function(){
			  $.ajax({
				   url: "/campclick/geolocation_ad/" + $(this).val(),
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
			    url: "/campclick/geolocation_ajax",
			    type: "POST",
			    dataType: "json",
			    data: {
				    zip: $("#zipcode").val(),
				    radius: $("#radius").val()
			    },
			    success: function(msg)   {
				    if (Object.size(msg.locations) > 0)   {
				    	// populate the data
				    	target_data = msg.locations;
				    	
					    // initialize the map
					    google.maps.event.addDomListener(window, 'load', initialize(msg));

				    } else {
					    alert("Sorry, no matching locations");
				    }
			    }
			});
		});

		$("#ad_target_btn").click(function(){
			var final_targets = [];
			
			$.each(target_data, function(index, value){
				var target = target_data[index];
				if (target.final_tgt != "//")   {
					final_targets.push(target.final_tgt);
				}
    		});

			$.ajax({
				url: "/campclick/set_target",
				type: "POST",
				dataType: "json",
				data: {
					id: $("#ad").val(),
					targets: final_targets,
					io: MY_SELECTED_IO,
					zip: $("#zipcode").val(),
					radius: $("#radius").val(),
				},
				success: function(msg)  {
					alert("Target has been set!");
				}
			});
		});

		if (MY_SELECTED_ADID != "") {
			$("#ad").val(MY_SELECTED_ADID).change();
		}
	});

	Object.size = function(obj) {
	    var size = 0, key;
	    for (key in obj) {
	        if (obj.hasOwnProperty(key)) size++;
	    }
	    return size;
	};
</script>

{include file="campclick/sections/footer.php"}
