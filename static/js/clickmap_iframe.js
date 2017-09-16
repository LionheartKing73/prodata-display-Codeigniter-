$(document).ready(function(){
	var heatmapInstance = null;
	
	// default the first tab
	$('#myTab a:first').tab('show');
	$("#geo-link-country").click(function(e){ e.preventDefault(); e.removeAttr('href'); });
	$("#geo-link-state").click(function(e){ e.preventDefault(); e.removeAttr('href'); });
	$("#geo-link-postalcode").click(function(e){ e.preventDefault(); e.removeAttr('href'); });
	
	$(document).on('focusin', '.click-count', function(){
	    var link_id = $(this).data("id");
	    $("#heatmap_creative_iframe").contents().find(".click_border").removeClass("click_border");
	    $("#heatmap_creative_iframe").contents().find("#hm_link_" + link_id).addClass("click_border");
    });
	
    $(document).on('change', '.click-count', function(){
        var link_id = $(this).data("id");
        var position = $("#heatmap_creative_iframe").contents().find("#hm_link_" + link_id).offset();
        var click_value = $(this).val();

        var bottom = position.top + $("#heatmap_creative_iframe").contents().find("#hm_link_" + link_id).outerHeight();
        var right = position.left + $("#heatmap_creative_iframe").contents().find("#hm_link_" + link_id).outerWidth();

        var mean_x = ((right - position.left) / 2) + position.left;
        var mean_y = position.top - ((bottom - position.top) / 2);
        
        heatmapInstance.addData({
            x: mean_x,
            y: mean_y,
            value: click_value
        });

        heatmapInstance.repaint();
        
        update_percentages();
    });
    
    $(document).on('change', '.percentage', function(){
        var link_id = $(this).data("id");
        var position = $("#heatmap_creative_iframe").contents().find("#hm_link_" + link_id).offset();
        var click_value = $(this).val();

        var bottom = position.top + $("#heatmap_creative_iframe").contents().find("#hm_link_" + link_id).outerHeight();
        var right = position.left + $("#heatmap_creative_iframe").contents().find("#hm_link_" + link_id).outerWidth();

        var mean_x = ((right - position.left) / 2) + position.left;
        var mean_y = position.top - ((bottom - position.top) / 2);
        
        heatmapInstance.addData({
            x: mean_x,
            y: mean_y,
            value: click_value
        });

        heatmapInstance.repaint();
        
        update_clicks();
    });
    
    $(document).on('change', '.total-click-update', function(){
    	var user_clicks = $(this).val();
    	$(".user_clicks").html(parseInt(user_clicks));
    	console.log("user-clicks: " + $(".user_clicks").html());
    });

    
    $(document).on('change', '.master-properties', function(){
        
    	var data = {
            "total_records" : parseInt($("#total_records").val()),
            "percentage_opens" : parseInt($("#percentage_opens").val()),
            "percentage_clicks" : parseFloat($("#percentage_clicks").val()),
            "percentage_bounce" : parseFloat($("#percentage_bounce").val()),
            "adjusted_opens" : 0,
            "adjusted_clicks" : 0,
            "adjusted_bounce" : 0,
            "opens" : 0,
            "clicks" : 0,
            "bounce" : 0
    	};
    	
    	//calculated the adjusted values by randomized value +/- 3 points
    	var randomOpen = randomIntFromMinMaxInterval(0, 3)/10;
    	var randomClicks = randomIntFromMinMaxInterval(0, 3)/10;
    	
    	data.adjusted_opens = data.percentage_opens + randomOpen + 0.25;
    	data.adjusted_clicks = data.percentage_clicks + randomClicks + 0.15;
    	data.adjusted_bounce = parseFloat(randomIntFromMinMaxInterval(1, (data.percentage_bounce*100)+3)/100) + 0.15;

    	//Click-Through Rate (CTR) = # of click-throughs / # of messages delivered
    	//Adjusted Click Through Rate = # of click-throughs / # of messages opened
    	
    	// calculate clicks, bounce & opens
    	//data.clicks = parseInt((data.adjusted_clicks/100) * data.opens);
    	data.opens = parseInt(data.total_records * (data.adjusted_opens/100));
    	data.clicks = parseInt((data.adjusted_clicks/100) * data.total_records);
    	data.bounce = parseInt((data.adjusted_bounce/100) * data.total_records);
    	
    	localStorage.setItem("master_properties", JSON.stringify(data));
    	
    	$(".user_clicks").html(data.clicks);
    	$("#total_clicks").val(data.clicks);
    	$("#total_opens").val(data.opens);
    	$("#total_bounces").val(data.bounce);
    });
    
    $("#trigger-create-order").click(function(){
    	var total_records = parseInt($("#total_records").val());
    	var percentage_opens = parseFloat($("#percentage_opens").val());
    	var percentage_clicks = parseFloat($("#percentage_clicks").val());
    	var percentage_bounce = parseFloat($("#percentage_bounce").val());
    	var user_entered_percentage = parseInt($("#user_percentage_set").html());
		
    	if (total_records > 0 && percentage_opens > 0 && percentage_clicks > 0 && percentage_bounce > 0 && user_entered_percentage >= 100)	{
            $("#max_clicks").val($("#total_clicks").val());
            $("#badge-heatmap").html("0");
            $('#myTab a[href="#createorder"]').tab('show');
    	} 
        else {
            var errCount = 0;
            if (! total_records > 0) errCount++;
            if (! percentage_opens > 0) errCount++;
            if (! percentage_clicks > 0) errCount++;
            if (! percentage_bounce > 0) errCount++;
            if (user_entered_percentage < 100) errCount++;

            $("#badge-heatmap").html(errCount);
            window.scrollTo(0,0); // scroll to top
    	}
    });
    
    $("#trigger-create-order2").click(function(){
    	var errCount = 0;
    	
    	if ($("#io").val() == "") errCount++;
    	if ($("#create_name").val() == "") errCount++;
    	if ($("#vendor").val() == "") errCount++;
    	if ($("#domain").val() == "") errCount++;
    	if ($("#campaign_start_datetime").val() == "") errCount++;
    	if ($("#vertical").val() == "") errCount++;
    	
    	if (errCount > 0){
    		$("#badge-create-order").html(errCount);
    		window.scrollTo(0,0); // scroll to top
    	} else {
    		$("#badge-create-order").html("0");
        	$('#myTab a[href="#geolocation"]').tab('show');
    	}
    });
    
    $("input[name='geotype']").on('change', (function() {
    	var geotype = this.value;
    	
    	if (geotype == "country")	{
    		$("#geo-nationwide").collapse("show");
    		$("#geo-state").collapse("hide");
    		$("#geo-postal-code").collapse("hide");
    	} else if (geotype == "state")	{
    		$("#geo-nationwide").collapse("show");
    		$("#geo-state").collapse("show");
    		$("#geo-postal-code").collapse("hide");
    	} else if (geotype == "postalcode")	{
    		$("#geo-nationwide").collapse("hide");
    		$("#geo-state").collapse("hide");
    		$("#geo-postal-code").collapse("show");
    	} else {
    		alert("Invalid selection.");
    	}
    }));
    
    $("#trigger-message").click(function(){
    	if ($("#message_result").val() == "")	{
    		window.scrollTo(0,0); // scroll to top
    		$("#badge-creative").html("1");
    		$("#message").addClass("error");
    	} 
        else {
    		$("#message").removeClass("error");
    		$("#badge-creative").html("0");
    		
            $.ajax({
                url: "/campclick/clickmap_ajax",
                type: "POST",
                dataType: "json",
                data: {
                    message: $("#message_result").val()
                },
                success: function(msg)  {
                    if (msg.status == "SUCCESS")    {
                    	$("#heatmap_links").empty();
                    	$.each(msg.links, function(i, item){
                        	var tr = "<tr><td style='max-width:300px; word-wrap:break-word;' class='link_href' data-id='" + item.link_id + "'>" + item.href + "</td><td><input type='text' class='input-xxsmall click-count' id='click_count_" + item.link_id + "' value='0' data-id='" + item.link_id + "' data-link='" + item.href + "' /></td><td><input type='text' class='input-mini percentage' id='percentage_" + item.link_id + "' data-id='" + item.link_id + "' data-link='" + item.href + "' value='0'>%</td></tr>";
                        	$("#heatmap_links").append(tr);
                        });

                        $("#heatmap_creative_iframe").contents().find("body").html(msg.content);
                    	$('#myTab a[href="#heatmap"]').tab('show');
                    	$("#link_div").show();

                        var iframe = document.getElementById('heatmap_creative_iframe');
                        var innerDoc = iframe.contentDocument || iframe.contentWindow.document;

                        if (heatmapInstance == null)  {
                            var config = {
                      			  container: innerDoc.getElementById('prodatafeed_hm_master_id'),
                         		  radius: 150,
                            };
                            
                        	heatmapInstance = h337.create(config);
                        }
                    }
                }
            });
    	}
    });

    var update_percentages = function(){
    	var master_properties = JSON.parse(localStorage.getItem("master_properties"));
    	
    	var total_clicks = master_properties.clicks;
    	var user_clicks = 0;
    	var user_percentage = 0;
    	
    	$(".click-count").each(function(i, obj){
    		var user_value = parseInt($(this).val());
    		var link_id = $(this).data("id");
    		
    		var percentage = parseInt((user_value / total_clicks) * 100);
    		
    		//$("#percentage_" + link_id).html(percentage);
    		$("#percentage_" + link_id).val(percentage);
    		
    		user_clicks += user_value;
    		user_percentage += percentage;
    	});
    	
    	$(".user_clicks").html(user_clicks);
    	$(".user_percentage").html(user_percentage);
    };
    
    var update_clicks = function()	{
    	var master_properties = JSON.parse(localStorage.getItem("master_properties"));
    	
    	var total_clicks = master_properties.clicks;
    	var user_clicks = 0;
    	var user_percentage = 0;
    	
    	$(".percentage").each(function(i, obj){
    		var link_id = $(this).data("id");
    		var user_value = parseInt($(this).val());
    		
    		//var click_count = parseInt((user_value / total_clicks) * 100);
    		var click_count = parseInt((user_value / 100) * total_clicks);
    		
    		$("#click_count_" + link_id).val(click_count);
    		
    		user_clicks += click_count;
    		user_percentage += user_value;
    	});
    	
    	$(".user_clicks").html(user_clicks);
    	$(".user_percentage").html(user_percentage);   	
    };
    
    $("#trigger-csv-export").click(function(){
    	export_links();
    });
    
    var export_links = function(){
    	var items = new Array();
    	
    	$(".link_href").each(function(i, obj){
    		var href = $(this).html();
    		var link_id = $(this).data("id");
    		var click_count = $("#click_count_" + link_id).val();
    		
    		items.push({
    			link: href,
    			count: click_count
    		});
    	});
    	
    	var a = document.createElement('a');
    	a.href= "data:attachment/csv," + encodeURIComponent(convert_to_csv(items));
    	a.target = "_blank";
    	a.download = "HeatMapClicks.csv";
    	document.body.appendChild(a);
    	a.click();
    };
    
    var convert_to_csv = function(objArray) {
        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        var str = '';

        for (var i = 0; i < array.length; i++) {
            var line = '';
            for (var index in array[i]) {
                if (line != '') line += ','

                line += array[i][index];
            }

            str += line + '\r\n';
        }

        return str;
    };
    
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
	
	$("#io").on('blur', function(){
		checkIO();
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
	
	var checkIO = function()	{
		$.ajax({
			url: "/take5/check_io/" + $("#io").val(),
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
	
	var randomIntFromMinMaxInterval = function(min, max){
		return Math.floor(Math.random()*(max-min+1)+min);
	};
	
	$("#trigger-create-order3").click(function(){
		var errCount = 0;
		
		var geo_type = $("input[name='geotype']:checked").val();
		if (geo_type == "") errCount++;
		
		if (geo_type == "country")	{
			if ($("#geo-input-country").val() == "") errCount++;
		} else if (geo_type == "state")	{
			if ($("#geo-input-country").val() == "") errCount++;
			if ($("#geo-input-state").val() == "") errCount++;
		} else if (geo_type == "postalcode")	{
			if ($("#geo-input-postalcode-radius").val() == "") errCount++;
			if ($("#geo-input-postalcode").val() == "") errCount++;
		} else {
			// caught above
		}
		
		if (errCount > 0)	{
    		$("#badge-geo-location").html(errCount);
    		window.scrollTo(0,0); // scroll to top
		} else {
			// process the order
	    	var items = new Array();
	    	
	    	$(".link_href").each(function(i, obj){
	    		var href = $(this).html();
	    		var link_id = $(this).data("id");
	    		var click_count = $("#click_count_" + link_id).val();
	    		
	    		items.push({
	    			link: href,
	    			count: click_count
	    		});
	    	});
	    	
	    	var pixels = new Array();
	    	$(".open_pixel_src").each(function(i, obj){
	    		pixels.push($(this).val());
	    	});
			
			$.ajax({
				url: "/take5/process_order_request",
				dataType: "json",
				type: "POST",
				data: {
					total_records: $("#total_records").val(),
					percentage_opens: $("#percentage_opens").val(),
					percentage_clicks: $("#percentage_clicks").val(),
					percentage_bounce: $("#percentage_bounce").val(),
					total_clicks: parseInt($("#total_clicks").val()),
					total_opens: parseInt($("#total_opens").val()),
					total_bounces: parseInt($("#total_bounces").val()),
					message_result: $("#message_result").val(),
					io: $("#io").val(),
					create_name: $("#create_name").val(),
					vendor: $("#vendor").val(),
					domain: $("#domain").val(),
					campaign_start_datetime: $("#campaign_start_datetime").val(),
					geotype: $("input[name='geotype']:checked").val(),
					country: $("#geo-input-country").val(),
					state: $("#geo-input-state").val(),
					radius: $("#geo-input-postalcode-radius").val(),
					zip: $("#geo-input-postalcode").val(),
					links: items,
					special_instructions: $("#special_instructions").val(),
					fire_open_pixel: $("input[name='open_pixel']:checked").val(),
					open_pixel: pixels,
					vertical: $("#vertical").val(),
					budget: $("#budget").val(),
					email_seeds: $("#emailseeds_data").val()
				},
				success: function(msg)	{
					if (msg.status == "SUCCESS")	{
						alert("Campaign Queued; ID: " + msg.campaign_id);
						document.location.href="/take5/queue";
					}
				}
			});
		}
	});
	
	$("#geo-input-country").change(function(){
		$.ajax({
			url: "/take5/get_states_list/" + $("#geo-input-country").val(),
			dataType: "json",
			success: function(msg)	{
				if (msg.status == "SUCCESS")	{

					$("#geo-input-state").empty();
					$.each(msg.states, function(i, item) {
						$("#geo-input-state").append("<option value='" + item.state + "'>" + item.name + "</option>");
					});
				}
			}
		});
	});
	
	$("input[name='open_pixel']").click(function() {
		if ($("input[name='open_pixel']:checked").val() == "Y"){
			$("#open_pixel_layer").show();
		} else {
			$("#open_pixel_layer").hide();
		}
	});
	
	 $("#trigger-create-order4").click(function(){
	    	var errCount = 0;
	    	
	    	var geo_type = $("input[name='geotype']:checked").val();
			if (geo_type == "") errCount++;
			
			if (geo_type == "country")	{
				if ($("#geo-input-country").val() == "") errCount++;
			} else if (geo_type == "state")	{
				if ($("#geo-input-country").val() == "") errCount++;
				if ($("#geo-input-state").val() == "") errCount++;
			} else if (geo_type == "postalcode")	{
				if ($("#geo-input-postalcode-radius").val() == "") errCount++;
				if ($("#geo-input-postalcode").val() == "") errCount++;
			} else {
				// caught above
			}
	    	
	    	if (errCount > 0){
	    		$("#badge-geo-location").html(errCount);
	    		window.scrollTo(0,0); // scroll to top
	    	} else {
	    		$("#badge-create-order").html("0");
	        	$('#myTab a[href="#emailseeds"]').tab('show');
	    	}
	    });
	
});