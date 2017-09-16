$(document).ready(function(){

    $('#download_leads').click(function(){
        $.ajax({
            url: "/v2/campaign/download_leads",
            type: "POST",
            dataType: "json",

            data: {
                start_date: lead_start_date,
                end_date: lead_end_date,
                ad_id: ad_id,
                campaign_id : js_data.campaign_id,
            },

            success: function(result)  {
                if(result.status == 'success') {
                    $('#iframe_for_download').attr('src', result.url);
                } else {
                    alert(result.msg);
                }

            }
        });
    });

    $('.btn_open_modal').on('click', function(){

        var lead_data = $(this).data('lead'); console.log(lead_data);

        $("#lead_modal .lead_email span").text(lead_data.email);
        $("#lead_modal .lead_full_name span").text(lead_data.full_name);
        $("#lead_modal .lead_phone_number span").text(lead_data.phone_number);
        $("#lead_modal .lead_created_date span").text(lead_data.created_date);


        $('#lead_modal').modal('show');
        return false;
    });

    if (campaign.campaign_type == 'FB-PAGE-LIKE'){
    $('.fb_page_like').text('Total Likes :');
    $('.fb_page_like_geo_loc').text('Likes');

        var check_if_fb_page_like = "Likes:";

    } else if (campaign.campaign_type == 'FB-VIDEO-VIEWS'){
        $('.fb_page_like').text('Total Video Views :');
        $('.fb_page_like_geo_loc').text('Video Views');

        var check_if_fb_page_like = "Video Views:";
    }
    else {
        var check_if_fb_page_like = "Clicks:" ;
    }

    if(campaign.geotype == 'country' || (campaign.geotype == 'state' && campaign.country !== 'US')){
        if(campaign.campaign_type != 'FB-VIDEO-VIEWS') {
            initialize_country_map(campaign.country);
        }
    }
        //console.log(js_data);
    var ad_id,
        start_date = js_data.js_start_data,
        end_date = js_data.js_date_now;
        lead_start_date = js_data.js_start_data,
        lead_end_date = js_data.js_date_now;
    console.log(start_date, end_date);
    make_all_charts(start_date, end_date);
     
    highChartsFire();
    
    $('.ad_id_list').on('click', function(){
        
        $('.ad_id_list').removeClass('active_green_ad');
        $(this).addClass('active_green_ad');
        
        ad_id = $(this).data('id');
        make_all_charts(start_date, end_date, ad_id);
        return false;
    });
    
    $('#view_campaign').on('click', function(){
        $('.ad_id_list').parent().removeClass('active_ad');
        ad_id = 0;
        make_all_charts(start_date, end_date, ad_id);
        return false;
    });
    
    $(function () {
        
        $('#reportrange').daterangepicker({
            "startDate": js_data.start_date,
            "endDate": js_data.date_now,
            ranges: {
                'Hourly': [moment(), moment()],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, 
        function (start, end) {
            start_date = start.format('YYYY-MM-DD');
            end_date = end.format('YYYY-MM-DD'); console.log(start_date, end_date);
            make_all_charts(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'), ad_id);
        });

        $('#lead_date_range').daterangepicker({
            "startDate": js_data.start_date,
            "endDate": js_data.date_now,
            ranges: {
                'Hourly': [moment(), moment()],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        },
        function (start, end) {
            lead_start_date = start.format('YYYY-MM-DD');
            lead_end_date = end.format('YYYY-MM-DD'); console.log(lead_start_date, lead_end_date);
        });
    });



    var div_width = $('.theme-banner-list .theme-ad-banner-item:nth-child(1)').width();

    $.each(ads, function( index, value ){

        if(value.creative_width > div_width) {
            $('.theme-banner-list .theme-ad-banner-item:nth-child(' + index + 1 + ')').find('.theme-normal-image').css('width', '100%');
        }
    });
    
});

/**
 * 
 * @param date start_date
 * @param date end_date
 * @param int ad_id
 * make pie chart and line chart
 */

var make_all_charts = function(start_date, end_date, ad_id){
    
    $.ajax({
        url: "/v2/campaign/chart_data",
        type: "POST",
        beforeSend: function(){
            //show_loader($('#view_campaign'));
        },
        dataType: "json",
        data: {
            start_date: start_date,
            end_date: end_date,
            ad_id: ad_id,
            campaign_id : js_data.campaign_id,
        },

        success: function(msg)  {
            console.log(start_date, end_date);
            hide_loader();
            highAreaChartsFire('theme-area-chart-holder', msg.click_data, (start_date != undefined &&  start_date == end_date) ? false : true);
            // makeChartPie('chart_browsers', msg.pie_chart.browsers_data, 'Web Browsers');
            // makeChartPie('mobile_device', msg.pie_chart.mobile_device, 'Mobile Devices');
            // makeChartPie('platforms', msg.pie_chart.platforms, 'Operating Systems');
            
            // var gender_array = [
            //     {data_filed: 'Male', click_count: msg.demograpics_data.male},
            //     {data_filed: 'Female', click_count: msg.demograpics_data.female},
            //     {data_filed: 'Unknown Gender', click_count: msg.demograpics_data.unknown_gender}
            // ];
            //
            // makeChartPie('users_gender', gender_array, 'Users Gender');

            // var clickCount = msg.demograpics_data['18_24'] * 1 + msg.demograpics_data['25_34'] * 1 +
            //     msg.demograpics_data['35_44'] * 1 + msg.demograpics_data['45_54'] * 1 + msg.demograpics_data['55_64'] * 1 + msg.demograpics_data['64+'] * 1 + msg.demograpics_data.unknown_age * 1;
            //
            // var firstAgeClick = ((msg.demograpics_data['18_24'] * 100) / clickCount).toFixed(1);
            // var secondAgeClick = ((msg.demograpics_data['25_34'] * 100) / clickCount).toFixed(1);
            // var thirdAgeClick = ((msg.demograpics_data['35_44'] * 100) / clickCount).toFixed(1);
            // var fourthAgeClick = ((msg.demograpics_data['45_54'] * 100) / clickCount).toFixed(1);
            // var fiveAgeClick = ((msg.demograpics_data['55_64'] * 100) / clickCount).toFixed(1);
            // var sixAgeClick = ((msg.demograpics_data['64+'] * 100) / clickCount).toFixed(1);
            // var unknownAgeClick = ((msg.demograpics_data.unknown_age * 100) / clickCount).toFixed(1);
            //
            //
            // var age_array = [
            //     {age: '18-24', clicks: firstAgeClick},
            //     {age: '25-34', clicks: secondAgeClick},
            //     {age: '35-44', clicks: thirdAgeClick},
            //     {age: '45-54', clicks: fourthAgeClick},
            //     {age: '55-64', clicks: fiveAgeClick},
            //     {age: '64+', clicks: sixAgeClick},
            //     {age: 'Unknown Age', clicks: unknownAgeClick},
            // ];
            //
            // makeChartBar('users_age', age_array);


            //msg.video_data['10_sec'] = 3;
            //msg.video_data['25_p']  = 5;
            //msg.video_data['50_p'] = 7;
            //msg.video_data['75_p'] = 2;
            //msg.video_data['95_p'] = 15;
            if (campaign.campaign_type == 'FB-VIDEO-VIEWS' || campaign.campaign_type == 'VIDEO_YAHOO'){

                var watchCount = msg.video_data['10_sec'] * 1 + msg.video_data['25_p'] * 1 +
                    msg.video_data['50_p'] * 1 + msg.video_data['75_p'] * 1 + msg.video_data['95_p'] * 1;

                var first_video_watch = ((msg.video_data['10_sec'] * 100) / watchCount).toFixed(1);
                var second_video_watch = ((msg.video_data['25_p'] * 100) / watchCount).toFixed(1);
                var third_video_watch = ((msg.video_data['50_p'] * 100) / watchCount).toFixed(1);
                var fourth_video_watch = ((msg.video_data['75_p'] * 100) / watchCount).toFixed(1);
                var five_video_watch = ((msg.video_data['95_p'] * 100) / watchCount).toFixed(1);
                //console.log(first_video_watch);
                //console.log(second_video_watch);
                //console.log(third_video_watch);
                //console.log(fourth_video_watch);
                //console.log(five_video_watch);




                var video_watched_array = [
                    {video: 'Under 10 Sec', watch: first_video_watch, "color":"#5CB85C"},
                    {video: '25%', watch: second_video_watch, "color":"#5CB85C"},
                    {video: '50%', watch: third_video_watch, "color":"#5CB85C"},
                    {video: '75%', watch: fourth_video_watch, "color":"#5CB85C"},
                    {video: '95%', watch: five_video_watch, "color":"#5CB85C"},


                ];

                makeChartBarVideo('video_views', video_watched_array);

            }

            
            if(campaign.geotype == 'state' && campaign.country == 'US'){
                initialize_state_map(msg.clicks_state);
            }

            if(campaign.geotype == 'postalcode'){
                initialize_geo_map(msg.geo_data);
            }
        }
    });
};

/**
 * 
 * @param element div
 * @param object chartData
 * make line chart
 */


if (campaign.campaign_type == 'FB-PAGE-LIKE'){


    var check_if_fb_page_like = "Likes: ";

}
else if (campaign.campaign_type == 'FB-VIDEO-VIEWS'){


    var check_if_fb_page_like = "Video Views: ";

} else {
    var check_if_fb_page_like = "Clicks: " ;
}

var highAreaChartsFire = function (div, chartData, format_dates) {
    console.log(chartData, format_dates);
    var chart = AmCharts.makeChart(div, {
        "responsive": {
            "enabled": true
        },
        "type": "serial",
        "theme": "light",
        "marginTop": 7,
        "dataProvider": chartData,
        "dataDateFormat":"YYYY-MM-DD",
        "valueAxes": [{
                "axisAlpha": 0.2,
                "dashLength": 1,
                "position": "left"
            }],
        "mouseWheelZoomEnabled": true,
        "graphs": [{
                "id": "g1",
                "balloonText": "[[category]]<br/><b><span style='font-size:14px;'>" + check_if_fb_page_like + "[[value]]</span></b>",
                "bullet": "round",
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "hideBulletsCount": 50,
                "title": "red line",
                "valueField": "click_count",
                "useLineColorForBulletBorder": true
            },
            {
                "id": "a1",
                "balloonText": "[[category]]<br/><b><span style='font-size:14px;'>Impressions: [[value]]</span></b>",
                "bullet": "round",
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "hideBulletsCount": 50,
                "title": "red line",
                "valueField": "impression_count",
                "useLineColorForBulletBorder": true
            }
        ],
        "chartCursor": {
        },
        "categoryField": "date",
        "categoryAxis": {
            "parseDates": format_dates,
            "axisColor": "#DADADA",
            "dashLength": 1,
            "minorGridEnabled": true
        }
    });

    chart.addListener("rendered", zoomChart);
    zoomChart();

// this method is called when chart is first inited as we listen for "rendered" event
    function zoomChart() {
        // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
        chart.zoomToIndexes(chartData.length - 40, chartData.length - 1);
    }
};

var highChartsFire = function () {
    var campaign_total_budget = $('#campaign_total_budget').text();
    campaign_total_budget = campaign_total_budget.slice(0, -1);
    var total_budget_spent = $('#total_budget_spent').text();
    total_budget_spent = total_budget_spent.slice(0, -1);
    total_budget_spent = parseFloat(total_budget_spent);
    campaign_total_budget = parseFloat(campaign_total_budget);

    if(!total_budget_spent) {
        total_budget_spent = 0;
    }
    //console.log(total_budget_spent, campaign_total_budget);
    //if (campaign.max_budget) {
        //if(campaign.percentage_max_budget) {
        //   var cost = campaign.percentage_max_budget - campaign.cost;
        //   var percent_cost = cost * 100 / campaign.percentage_max_budget;
        //   if(cost*1>0){
        //       var budget_left = percent_cost;
        //        //console.log(budget_left, percent_cost);
        //   } else {
        //       var budget_left = 0;
        //   }
        //} else {
        //    var budget_left = (campaign.max_budget - campaign.cost)*100/campaign.max_budget
        //}

        //var budget_left =  100*total_budget_spent/campaign_total_budget;
        //
        //var budget_spent = 100 - budget_left;
        //
        //var data = [
        //    {
        //        name: "Budget Spent",
        //        y: budget_spent
        //    },
        //    {
        //        name: "Budget Left",
        //        y: budget_left
        //    }
        //];

        var data = [
            {
                name: "Progress Spent",
                y: campaign_total_budget
            },
            {
                name: "Progress Left",
                y: total_budget_spent
            }
        ];
    //}
    //else if (campaign.campaign_end_datetime){
    //    var data = [
    //        {
    //            name: "Budget Spent",
    //            y: campaign.cost*100/(campaign.budget * campaign.total_days)
    //        },
    //        {
    //            name: "Budget Left",
    //            y: 100 - campaign.cost*100/(campaign.budget * campaign.total_days)
    //        }
    //    ];
    //}
    
    // Create the chart
    $('#theme-piechart-holder').highcharts({
        chart: {
            renderTo: 'container',
            alignTicks: false,
            backgroundColor: '#fafafa',
            type: 'pie',
            margin: [0, 0, 0, 0],
            spacingTop: 0,
            spacingBottom: 0,
            spacingLeft: 0,
            spacingRight: 0
        },
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        credits: {
            enabled: false
        },
        plotOptions: {
            pie: {
                size:'100%',
                center: [80, 90],
                dataLabels: {
                    enabled: false
                },
                shadow: false,
            },
            series: {
                dataLabels: {
                    enabled: false,
                    format: '{point.name}: {point.y:.1f}%'
                },
                 states: {
                  hover: {
                    enabled: false,
                    halo: {
                      size: 0
                    }
                  }
                }
            }
        },
        exporting: {
            buttons: {
                contextButtons: {
                    enabled: false,
                    menuItems: null
                }
            },
            enabled: false
        }, 
        tooltip: {
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
        },
        series: [{
            name: "Brands",
            colorByPoint: true,
            data: data
        }]
    });
};
/**
 * 
 * @param element div
 * @param object chartData
 * @param string title
 * make pie chart 
 */

var makeChartPie = function(div, chartData) {
    
    if (chartData.length == 0){
        chartData = [{click_count : 1, data_filed: 'Unknown'}];
    }
    
    var chart = AmCharts.makeChart(div, {
        "type": "pie",
        "startDuration": 0,
        "theme": "light",
        "addClassNames": true,
        "legend": {
            "position": "right",
            "autoMargins": false
        },
        "innerRadius": "30%",
        "defs": {
            "filter": [{
                "id": "shadow", 
                "width": "200%",
                "height": "200%",
                "feOffset": {
                    "result": "offOut",
                    "in": "SourceAlpha", 
                    "dx": 0,
                    "dy": 0
                },
                "feGaussianBlur": {
                    "result": "blurOut",
                    "in": "offOut",
                    "stdDeviation": 5
                },
                "feBlend": {
                    "in": "SourceGraphic",
                    "in2": "blurOut",
                    "mode": "normal"
                }
            }]
        },
        "dataProvider":chartData,
        "valueField": "click_count",
        "titleField": "data_filed"
    });

    chart.addListener("init", handleInit);

    chart.addListener("rollOverSlice", function(e) {
        handleRollOver(e);
    });

    function handleInit(){
        chart.legend.addListener("rollOverItem", handleRollOver);
    }

    function handleRollOver(e){

        if (e.dataItem.wedge){
            var wedge = e.dataItem.wedge.node;
            wedge.parentNode.appendChild(wedge); 
        } 
    }
};

var makeChartBar = function (div, chartData) {

    var chart = AmCharts.makeChart(div, {
        "type": "serial",
        "theme": "light",
        "dataProvider": chartData,
        "valueAxes": [{
                "gridColor": "#FFFFFF",
                "gridAlpha": 0.2,
                "dashLength": 0
            }],
        "gridAboveGraphs": true,
        "startDuration": 1,
        "graphs": [{
                "balloonText": "[[category]]: <b>[[value]]%</b>",
                "fillAlphas": 0.6,
                "lineAlpha": 0.2,
                "type": "column",
                "valueField": "clicks"
            }],
        "chartCursor": {
            "categoryBalloonEnabled": false,
            "cursorAlpha": 0,
            "zoomable": false
        },
        "categoryField": "age",
        "categoryAxis": {
            "gridPosition": "start",
            "gridAlpha": 0,
            "tickPosition": "start",
            "tickLength": 20
        }

    });

};

var makeChartBarVideo = function (div, chartData) {


    var chart = AmCharts.makeChart(div, {

        "type": "serial",
        "theme": "light",
        "dataProvider": chartData,
        "valueAxes": [{
            "gridColor": "#FFFFFF",
            "gridAlpha": 0.2,
            "dashLength": 0,

        }],
        "gridAboveGraphs": true,
        "startDuration": 1,
        "graphs": [{
            "balloonText": "[[category]]: <b>[[value]]%</b>",
            "fillAlphas": 0.6,
            "lineAlpha": 0.2,
            "type": "column",
            "valueField": "watch",
            "colorField": "color"

        }],
        "chartCursor": {
            "categoryBalloonEnabled": false,
            "cursorAlpha": 0,
            "zoomable": false
        },
        "categoryField": "video",
        "categoryAxis": {
            "gridPosition": "start",
            "gridAlpha": 0,
            "tickPosition": "start",
            "tickLength": 20
        }

    });

};

var initialize_geo_map = function (msg){
    
    var latlngbounds = new google.maps.LatLngBounds();
    
    var mapOptions = {
          zoom: 5,
          center: new google.maps.LatLng(37.09024, -95.712891),
          mapTypeId: google.maps.MapTypeId.TERRAIN
    };

    var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
        
    for(var city in msg.source_location)    {
        
        //$('#theme-geo-state-widget').find('.click_count_js:eq(' + city +')').text(msg.source_location[city].click_count);
                
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
        
        
        
        var latLng = new google.maps.LatLng(msg.source_location[city].latitude, msg.source_location[city].longitude);
                
        latlngbounds.extend(latLng);
        
        var markerCenter = new google.maps.Marker({
            position: latLng,
            title: "Location",
            map: map,
            draggable: false,
            i:city
        });
        
        markerCenter = new google.maps.Marker({map: map, position: latLng, clickable: true});
        
        markerCenter.set("id", city);
        
        var info = null;
        
        info = new google.maps.InfoWindow();
        
        //console.log(msg.source_location[i].click_count);
        
        
        
        google.maps.event.addListener(markerCenter, 'click', function() {
            
            var i = this.get('id');
            
            var contentString = '<p>State: ' + msg.source_location[i].state + '</p>' + 
                '<p>City: ' + msg.source_location[i].city + '</p>' +
                '<p>Click Count: ' + msg.source_location[i].click_count + '</p>';
            
            info.setContent(contentString);
            info.open(map, this);
        });

        circle.bindTo('center', markerCenter, 'position');
    }
    
    map.setCenter(latlngbounds.getCenter());
};

var initialize_country_map = function(country){
    
    var map = new google.maps.Map(document.getElementById('map-canvas'), {
        center: new google.maps.LatLng(37.0625,-95.677068),
        zoom: 2,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    
    var geocoder = new google.maps.Geocoder();
    
    /*
    
    geocoder.geocode( {'address' : 'Canada'}, function(results, status) {
        console.log('----');
        console.log(results);
        console.log('---');
        if (status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
        }
    });
    */

    var world_geometry = new google.maps.FusionTablesLayer({
        query: {
            select: 'geometry',
            from: '1N2LBk4JHwWpOY4d9fobIn27lfnZ5MDy-NoqqRpk',
            where: "ISO_2DIGIT IN ('" + country + "')"
        },
        map: map,
        suppressInfoWindows: true
    });
};

var initialize_state_map = function(clicks_state){

    var polys = [];
    var labels = [];

    // Display the map, with some controls and set the initial location 
    var map = new GMap2(document.getElementById("map-canvas"));
    map.addControl(new GLargeMapControl());
    map.addControl(new GMapTypeControl());
    map.setCenter(new GLatLng(42.16, -100.72), 4);


    $('.click_count_js[data-state]').each(function() {

        var thisState = $(this).attr('data-state');
        $(this).text(clicks_state[thisState]);

    });


    GEvent.addListener(map, "click", function (overlay, point) {

        if (!overlay) {

            for (var i = 0; i < polys.length; i++) {
                if (polys[i].Contains(point)) {

                    var clicks_count = (clicks_state[labels[i].short_name] === undefined) ? 0 : clicks_state[labels[i].short_name];

                    map.openInfoWindowHtml(point, "<p>State: " + labels[i].label + "</p>Clicks: " + clicks_count);
                    i = 999; // Jump out of loop
                }
            }
        } 
    });


    // Read the data from states.xml
    var request = GXmlHttp.create();
    request.open("GET", "/v2/files/states.xml", true);
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            var xmlDoc = GXml.parse(request.responseText);
            // ========= Now process the polylines ===========
            var states = xmlDoc.documentElement.getElementsByTagName("state");


            // read each line
            for (var a = 0; a < states.length; a++) {

                var short_name = states[a].getAttribute("short_name");

                if (campaign.state.search(short_name) > -1 || short_name in clicks_state){

                    var label = states[a].getAttribute("name");

                    var colour = campaign.state.search(short_name) > -1 ? states[a].getAttribute("colour") : '#EADBC8';

                    // read each point on that line
                    var points = states[a].getElementsByTagName("point");
                    var pts = [];
                    for (var i = 0; i < points.length; i++) {
                        pts[i] = new GLatLng(parseFloat(points[i].getAttribute("lat")),
                                parseFloat(points[i].getAttribute("lng")));
                    }
                    var poly = new GPolygon(pts, "#000000", 1, 1, colour, 0.5, {clickable: false});

                    //google.maps.event.addListener(poly, 'click', showInfo);

                    polys.push(poly);
                    labels.push({label: label, short_name: short_name});
                    map.addOverlay(poly);

                }
            }

            // ================================================
        }
    };
        
    request.send(null);
};
