	<script src="https://code.highcharts.com/highcharts.js"></script>

	<script type="text/javascript">
//    var j = $.noConflict();
    $(function () {
           $('#container-linechat').highcharts({
                chart: {
                type: 'line',
                margin: [ 50, 50, 100, 80]
                },
				credits: {
					enabled: false
				},
                title: {
                    text: '{$rep.title}',
                    x: -20 //center
                },
                subtitle: {
                    text: '',
                    x: -20
                },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enable: true	
                        }
                    }
                },
                xAxis: {
                    title: {
                        text: '{$rep.title}'
                    },
                    categories: [{$rep.date}],
					labels: {
                    rotation: -45,
                    align: 'right',
                    style: {
                        fontSize: '13px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                }
                },
                yAxis: {
                    title: {
                        text: 'Number of Clicks'
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                },
                tooltip: {
                    valueSuffix: ''
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'top',
                    x: -10,
                    y: 100,
                    borderWidth: 0
                },
                series: [
                {
                    name: 'Total Clicks',
                    data: [{$rep.click_data}]
                },
                {
                    name: 'Unique Clicks',
                    data: [{$rep.unique_clicks_data}]
                },
                {
                    name: 'Impressions (Views)',
                    data: [{$rep.impressions_data}]
                },
                {
                    name: 'Mobile Clicks',
                    data: [{$rep.mobile_data}]
                }
                ]
            });
        });
	
    $(function () {
            $('#container').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false
                },
				credits: {
					enabled: false
				},
                title: {
                    text: 'User Agents'
                },
                tooltip: {
                    {literal}  pointFormat: '{series.name}: <b>{point.percentage}%</b> ',{/literal}
                    percentageDecimals: 1,
    /*				formatter: function() {
                                return '<b>'+this.point.name+'</b> Total clicks: '+this.point.y;
                            }*/
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            color: '#000000',
                            connectorColor: '#000000',
                            formatter: function() {
                                return '<b>'+this.point.name+'</b> Total clicks: '+this.point.y;
                            }
                        }
                    }
                },
                series: [{
                    type: 'pie',
                    name: 'Browser share',
                    data: [
                        ['Firefox',   {$browsers.Firefox}],
                        ['IE',       {$browsers.IE}],
                        {
                            name: 'Chrome',
                            y: {$browsers.Chrome},
                            sliced: true,
                            selected: true
                        },
                        ['Safari',    {$browsers.Safari}],
                        ['Opera',     {$browsers.Opera}],
                        ['Others',   {$browsers.Others}]
                    ]
                }]
            });
        });
    {if $platforms|@count gt 0}
    $(function () {
            $('#container-platform').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false
                },
				credits: {
					enabled: false
				},
                title: {
                    text: 'Operating System'
                },
                tooltip: {
                  {literal}  pointFormat: '{series.name}: <b>{point.percentage}%</b> ',{/literal}
                    percentageDecimals: 1,
                    valueDecimals: 0
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            color: '#000000',
                            connectorColor: '#000000',
                            formatter: function() {
                                return '<b>'+this.point.name+'</b> Total clicks: '+this.point.y;
                            }
                        }
                    }
                },
                series: [{
                    type: 'pie',
                    name: 'Platform Usage',
                    data: [
//                        ['Windows',   {$platforms.Windows}],
//                        ['Linux',       {$platforms.Linux}],
//                        {
//                            name: 'Mac',
//                            y: 0,
//                            sliced: true,
//                            selected: true
//                        },
//                        ['Solaris',    {$platforms.Solaris}],
//                        ['FreeBSD',     {$platforms.FreeBSD}],
                        {foreach from=$platforms item=p}
                            ['{$p.platform}',{$p.cnt}],
                        {/foreach}
                    ]
                }]
            });
        });
   {/if}
   {if $mobile_devices|@count gt 0}
    $(function () {
            $('#container-devices').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false
                },
				credits: {
					enabled: false
				},
                title: {
                    text: 'Mobile Usage'
                },
                tooltip: {
                  {literal}  pointFormat: '{series.name}: <b>{point.percentage}%</b> ',{/literal}
                    percentageDecimals: 1,
                    valueDecimals: 0
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            color: '#000000',
                            connectorColor: '#000000',
                            formatter: function() {
                                return '<b>'+this.point.name+'</b> Total clicks: '+this.point.y;
                            }
                        }
                    }
                },
                series: [{
                    type: 'pie',
                    name: 'Mobile Usage',
                    data: [
                        {foreach from=$mobile_devices item=m}
                        ['{$m.mobile_device}',{$m.cnt}],
                        {/foreach}
                    ]
                }]
            });
        });
   {/if}
    
    </script>
    <script type="text/javascript">
	$(function () {
	  $('#dt-range-selector').click(function(){
											//$('#dt-range-selector').addClass('btn-inverse');
											    $('.btn').removeClass('btn-inverse');
												$('#dt-range-selector').toggleClass('btn-inverse');
												$('#date-selection-form').toggle('slow');
											 });
	  
	  $('#date-select').submit(function(){
										var st_date = $('#startDate').val();
										var ed_date = $('#endDate').val();
										var action_url = $('#action_url').val();
										window.location = action_url+'/'+st_date+'/'+ed_date;
										return false;
										});
	});
	</script>
     <script>
	$(function() {
		$("#startDate").datepicker({ dateFormat: "yy-mm-dd" });
		$("#endDate").datepicker({ dateFormat: "yy-mm-dd" });
	});
	</script>
