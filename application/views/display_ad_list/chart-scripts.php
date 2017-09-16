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
                    text: '{$report.hourly_total_clicks.category_title}',
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
                        text: '{$report.hourly_total_clicks.category_title}'
                    },
                    categories: [{$report.hourly_total_clicks.categories}],
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
    series: [{
        name: 'Total Clicks',
        data: [{$report.hourly_total_clicks.data}]
    }, {
        name: 'Unique Clicks',
            data: [{$report.hourly_unqiue_total_clicks.data}]
    },
    {
        name: 'Impressions (Views)',
            data: [{$report.hourly_impressions.data}]
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
            ['Firefox',   {$report.browsers_shares.Firefox}],
        ['IE',       {$report.browsers_shares.IE}],
    {
        name: 'Chrome',
            y: {$report.browsers_shares.Chrome},
        sliced: true,
            selected: true
    },
    ['Safari',    {$report.browsers_shares.Safari}],
    ['Opera',     {$report.browsers_shares.Opera}],
    ['Others',   {$report.browsers_shares.Others}]
    ]
    }]
    });
    });

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
            ['Windows',   {$report.platform.Windows}],
        ['Linux',       {$report.platform.Linux}],
    {
        name: 'Mac',
            y: {$report.platform.Mac},
        sliced: true,
            selected: true
    },
    ['Solaris',    {$report.platform.Solaris}],
    ['FreeBSD',     {$report.platform.FreeBSD}],
    ]
    }]
    });
    });

    {if $report.mobile_devices|@count gt 0}
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
            {foreach from=$report.mobile_devices item=m}
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
