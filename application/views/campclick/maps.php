{include file="campclick/sections/header.php"}
<script src="/static/js/markerclusterer.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>

    <div class="container">


      <div class="row">
        <div class="span2">
        	<h2>IO #: {$io}</h2>
        	<br/>
        	<h6>Campaign Geo Reporting</h6>
        	<span>Processed Clicks: <span id="click_count">-</span></span><br/>
        	<span>Unique Clicks: {$unique_clicks}</span><br/>
        	<span>Total Clicks: {$total_clicks}</span>
        	<br/>
        </div>
        <div class="span10">
        	<div id="pleasewait" style="text-align:center;"><h4>** PLEASE WAIT - LOADING GEO **<BR/>Can Take Up to 3 Minutes</h4></div>
        	<input type="hidden" name="io" id="io" value="{$io}" />
        	<input type="hidden" name="data" id="data" value="" />
   			<div id="map" style="height:480px;" class="span10"></div>
        </div>
      </div>

{literal}
<script>

function initialize() {
	$(document).ready(function(){
		$.ajax({
			url: "/campclick/map_ajax",
			type: "POST",
			dataType: "json",
			data: { io: $("#io").val() },
			success: function(msg)	{
				if (msg.status == "SUCCESS")	{
					$("#click_count").html(msg.data.length); // update the click count
					
					var center = new google.maps.LatLng(41.850033, -87.6500523);

					var map = new google.maps.Map(document.getElementById('map'), {
					    zoom: 3,
					    center: center,
					    mapTypeId: google.maps.MapTypeId.ROADMAP
					});

					var markers = [];

					for (var i = 0; i < msg.data.length; i++) {
					    var dataPhoto = msg.data[i];
    
					    var latLng = new google.maps.LatLng(dataPhoto.lat,
					        dataPhoto.long);
					    var marker = new google.maps.Marker({
					        position: latLng
					    });
					    
					    markers.push(marker);
					}

					var markerCluster = new MarkerClusterer(map, markers);
					$("#pleasewait").hide();
				}
			}
		});
	});
}

$(document).ready(function(){
	initialize();
});
</script>
{/literal}

{include file="campclick/sections/footer.php"}