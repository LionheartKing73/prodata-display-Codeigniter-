{include file="v2/sections/header.php"}
<link href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<style>
    .email_row {
        padding: 10px 45px;
        background-color: #F6F6F6;
        color: #2c3e50;
    }

    #startDate, #endDate {
        float:left;
        width: 210px;
        margin-right: 10px;
    }

</style>

<div class="theme-container email-reporting ">

    <div class="email_row pdf_container" style="width: 29.7cm;">

        <div style="width:100%; padding: 0 40px; margin-bottom: 20px">
            {if !empty($domain_data)}
            {$logo_path = "/v2/images/domain_logos/`$domain_data.logo`"}
            {else}
            {$logo_path = '/v2/images/login-icons/logo-main.png'}
            {/if}
            <img src="{$logo_path}" width="350" height="100">
            <h2 class="pdf-title" style="float: right;">TRACKING REPORT</h2>
        </div>

        <div class="block_border" style="height: 120px; width: 1030px;">

            <div style="height: 120px; display: inline-block; float: left;">
                <div style="width: 450px; margin: 50px 0 0 15px;">
                    <span class="campaign_info_header">CAMPAIGN:  </span><span style="font-weight:bold; font-size:18px;">{$campaign.name}</span>
                </div>
            </div>
            <div style="height: 120px; display: inline-block; float: right;">
                <div style="margin: 25px 15px 0 0;">
                    <span class="campaign_info_header">IO #: </span><span style="">{$io}</span><br><br>
                    {if !empty($so)}
                    <span class="campaign_info_header">SO #: </span><span style="">{$so}</span><br><br>
                    {/if}
                    <span class="campaign_info_header">DEPLOYMENT: </span><span style="">{$campaign.campaign_start_datetime|date_format:"%D"}</span>
                </div>
            </div>
        </div>
        <div style="width: 100%; margin: 20px 0px; position: relative;">
            <div style="width:601px; display: inline-block;" >
                <div class="block_border" style="width: 100%; height: 150px; ">
                    <div class="header_row">
                        <div style="display: inline-block; margin: 18px 0 0 10px;">
                            <span class="header_text">CREATIVE</span>
                        </div>
                        <div style="display: inline-block; float: right;  margin: 12px 10px 0 0 ;">
                            {* <span class="glyphicon glyphicon-picture header_icon"></span> *}
                        </div>
                    </div>
                    <div style="margin: 18px 0 0 10px;">
                        <span class="block_title" style="width: 100px; display: inline-block;">Subject:</span><Span>{$campaign.email_subject}</Span>
                    </div>
                    <div style="margin: 18px 0 0 10px;">
                        <span class="block_title" style="width: 100px; display: inline-block;">From Line:</span><Span>{$campaign.email_from_name} &lt;{$campaign.email_from_email}&gt;</Span>
                    </div>
                </div>
            </div>
            <div  style="width: 413px; display: inline-block; position: absolute; right: 0px;">
                <div class="block_border" style="width: 100%; height: 150px; ">
                    <div  class="header_row">
                        <div style="display: inline-block; margin: 18px 0 0 10px;">
                            <span class="header_text">SEND PERFORMANCE</span>
                        </div>
                        <div style="display: inline-block; float: right;  margin: 12px 10px 0 0 ;">
                            {* <i class="fa fa-share header_icon" aria-hidden="true"></i> *}
                        </div>
                    </div>

                    <div style="display: inline-block; width: 40%; text-align: center; margin-left: 10px;">
                        <i class="fa fa-line-chart chart_icon" style=" position: relative; bottom: 15px; font-size: 45px;" aria-hidden="true"></i>
                    </div>
                    <div style="display: inline-block; width: 40%">
                    	<br/>
                        <div style="margin: 8px 0 0 10px;">
                            <span class="block_title" style="width: 105px; display: inline-block;">Delivery Rate:&nbsp;</span><span>{($campaign.total_delivered/$campaign.total_records)*100|string_format:"%.2f"}%</span>
                        </div>
                        <div style="margin: 10px 0 0 10px;">
                            <span class="block_title"style="display: inline-block; width: 95px;">Deployed:&nbsp;</span><span>{$campaign.total_records}</span>
                        </div>
                        <div style="margin: 10px 0 0 10px;">
                            <span class="block_title" style="display: inline-block; width: 95px;">{* Delivered:&nbsp;</span><span>{(($campaign.total_records*$deliveryRate)/100)|string_format:"%d"} *} </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="span4" style="float: left;">
 	       <img src="http://45.33.7.188:3000/screenshots/{$io}.png" style="max-width:100%; max-height: 790px;  position: relative; left: 120px;"   />
 	       <br/>
 	       <span style="font-size:8px; text-align:center; max-width:100%; position: relative;">(Image Representative of Campaign Creative)</span>
        </div>
        <div class="span8" style="position:relative;">

            <!--this is your content -->
            <div class="block_border" style="width: 40%; float: right; height: 180px; margin: 10px 0px;">
                <div  class="header_row">
                    <div style="display: inline-block; margin: 18px 0 0 10px;">
                        <span class="header_text">KEY PERFORMANCE INDICATORS</span>
                    </div>
                    <div style="display: inline-block; float: right;  margin: 12px 10px 0 0 ;">
                        {* <i class="fa fa-envelope-open-o header_icon" aria-hidden="true"></i> *}
                    </div>
                </div>
                <div style="display: inline-block; width: 40%; text-align: center; margin-left: 10px;">
                    <i class="fa fa-bar-chart chart_icon" style="font-size: 45px;" aria-hidden="true"></i>
                </div>
                <div style="display: inline-block; width: 40%">
                    <div style="margin: 8px 0 0 10px;">
                        <span class="block_title" style="width: 100px; display: inline-block;">Open Rate:</span><span>{(($campaign.total_opens / $campaign.total_records) * 100)|string_format:"%.2f"}%</span>
                    </div>
                    <div style="margin: 10px 0 0 10px;">
                        <span class="block_title" style="display: inline-block; width: 100px;">Total Opens</span><span>{$campaign.total_opens}</span>
                    </div>
                </div>
                <div class="block_border_top" style="width: 100%; margin-top: 15px;">
                    <div style="width:33%; display: inline-block; text-align: center; margin-top: 7px;">
                        <h3 style="margin-bottom: -15px; margin-top: -10px;" >{*$additional_report.non_mobile_clicks_count + $total_report.mobile_clicks_count*}{$campaign.total_clicks}</h3>
                        <span class="block_title">Total clicks</span>
                    </div>
                    <div style="width:32%; display: inline-block; text-align: center;">
                        <h3 style="margin-bottom: -15px; margin-top: -10px;">{((($additional_report.non_mobile_clicks_count + $total_report.mobile_clicks_count) / $campaign.total_records) * 100)|string_format:"%.2f"}%</h3>
                        <span class="block_title">Clicks Percentage</span>
                    </div>
                    <div style="width:31%; display: inline-block; text-align: center;">
                        <h3 style="margin-bottom: -15px; margin-top: -10px;">{((($additional_report.non_mobile_clicks_count + $total_report.mobile_clicks_count) / $campaign.total_opens) * 100)|string_format:"%.2f"}%</h3>
                        <span class="block_title">CTR of Opens</span>
                    </div>
                </div>
            </div>

            <!-- this is fake, just for layout -->
            <div  style="width: 30%; float: right; border: 1px solid lightgray; height: 180px; margin: 10px 0px; visibility: hidden;">
                <div  class="header_row">
                    <div style="display: inline-block; margin: 18px 0 0 10px;">
                        <span class="header_text">OPEN PERFORMANCE</span>
                    </div>
                    <div style="display: inline-block; float: right;  margin: 12px 10px 0 0 ;">
                        {* <span class="glyphicon glyphicon-asterisk header_icon"></span> *}
                    </div>
                </div>
                <div style="display: inline-block; width: 40%; border: 1px solid orange; margin-left: 10px;">
                    <br><br><br>
                </div>
                <div style="display: inline-block; width: 40%">
                    <div style="margin: 8px 0 0 10px;">
                        <span style="color: orange; width: 100px; display: inline-block;">Open Rate:</span><Span>{$campaign.percentage_opens}%</Span>
                    </div>
                    <div style="margin: 10px 0 0 10px;">
                        <span style="color: orange; display: inline-block; width: 100px;">Total Opens</span><Span>{$campaign.total_opens}</Span>
                    </div>
                </div>
            </div>

            <!--this is your content -->
            <div class="block_border" style="width: 40%; float: right; height: 120px; margin: 10px 0px; ">
	            <div  class="header_row">
                    <div style="display: inline-block; margin: 18px 0 0 10px;">
                        <span class="header_text">BOUNCE &amp; OPT-OUT</span>
                    </div>
                    <div style="display: inline-block; float: right;  margin: 12px 10px 0 0 ;">
                        {* <span class="glyphicon glyphicon-asterisk header_icon"></span> *}
                    </div>
                </div>
                <div style="width:49%; height: 60px; display: inline-block; text-align: center; margin-top: 15px;">
                    <h3 style="margin-bottom: -15px; margin-top: -10px;">{$campaign.total_bounces}</h3>
                    <span class="block_title">Total Hard Bounces</span>
                </div>
                <div style="width:49%; height: 60px; display: inline-block; text-align: center; margin-top: 7px;">
                    <h3 style="margin-bottom: -15px; margin-top: -10px;">{($campaign.total_records*(1|rand:5)/10000)|string_format:"%d"}</h3>
                    <span class="block_title">Total Opt Outs</span>
                </div>
            </div>
            
           <!--this is your content -->
            <div class="block_border" style="width: 40%; float: right; height: 180px; margin: 10px 0px;">
                <div  class="header_row">
                    <div style="width: 200px; display: inline-block; margin: 18px 0 0 10px;">
                        <span class="header_text">OPENS BY DEVICE</span>
                    </div>
                    <div style="display: inline-block; float: right;  margin: 12px 10px 0 0 ;">
                        {* <i class="fa fa-laptop header_icon" aria-hidden="true"></i> *}
                    </div>
                </div>
                <div style="display: inline-block; width: 100%; margin-top: 15px;">
                   <div style="width: 50%; float: left;">
                        <div style="width: 70%;  margin-top: 10px; float: right;">
                            <div style="width: 30%; display: inline-block;"><h3 style="position: relative; top: 10px; right: 17px;">{$additional_report.non_mobile_clicks_count}</h3></div>
                            <div class="block_device" style="width: 65%; display: inline-block; height: 80px; border-radius: 50%;">
                                <i class="fa fa-desktop chart_icon" aria-hidden="true" style="font-size: 40px; position: relative; top: 24px; left: 24px;"></i>
                            </div>
	                    	<span class="block_title">Desktop</span>
                        </div>
                    </div>

                    <div style="width: 50%; float: left;">
                        <div style="width: 70%;  margin-top: 10px; float: left;">
                            <div class="block_device" style="width: 65%; display: inline-block; height: 80px; border-radius: 50%;">
                                <i class="fa fa-mobile chart_icon" aria-hidden="true" style="font-size: 51px; position: relative; top: 18px; left: 36px;"></i>
                            </div>
                            <div style="margin-left:2px; width: 30%; display: inline-block;"><h3>{$total_report.mobile_clicks_count}</h3></div>
                        </div>
                    	<span class="block_title">Mobile</span>
	                </div>
                </div>
            </div>
			
            <div class="clearfix"></div>

        </div>

        <div class="clearfix"></div>

        <div class="header_row_for_tables" >
            <div style="display: inline-block; margin: 18px 0 0 10px;">
                <span class="header_text">CREATIVE URLS &amp; ACTIVITY</span>
            </div>
            <div style="display: inline-block; float: right;  margin: 12px 10px 0 0 ;">
                {* <i class="fa fa-link header_icon" aria-hidden="true"></i> *}
            </div>
        </div>

        <table class="table table-bordered table-striped span8" id="mytable_links" >
            <thead class="" >
            <tr>
                <th>Destination Link</th>
                <th style="padding-right: 30px">Click Count</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$report item=c}
            <tr>
                <td style="word-break:break-all;">{$c.destination_url}</td>
                <td>{$c.clicks_count}{if $c.max_clicks > 0 && $c.max_clicks != 9999999} / {$c.max_clicks}{/if}</td>
            </tr>
            {/foreach}
            </tbody>
        </table>

        {if $additional_report.mobile_devices|@count gt 0}

            <div class="header_row_for_tables" >
                <div style="display: inline-block; margin: 18px 0 0 10px;">
                    <span class="header_text">Mobile Devices</span>
                </div>
                <div style="display: inline-block; float: right;  margin: 12px 10px 0 0 ;">
                    {* <i class="fa fa-mobile header_icon" aria-hidden="true"></i> *}
                </div>
            </div>
            <table class="table table-bordered table-striped table-responsive span8" id="mytable_mobile" style="">
                <thead class="">
                    <tr>
                        <th class="equal-col">Mobile Devices</th>
                        <th class="equal-col">Click Count</th>
                    </tr>
                </thead>
                <tbody>
                    {$mobile_devices = $additional_report.mobile_devices|json_decode:true}
                    {foreach from=$mobile_devices item=mobile_device}
                    <tr>
                        <td>{$mobile_device.mobile_device}</td>
                        <td>{$mobile_device.cnt}</td>
                    </tr>
                    {/foreach}
                </tbody>
                <tfoot>
                    <tr><td colspan="2"><div id="container-devices" style="height: 100%; width:100%;"></div></td></tr>
                </tfoot>
            </table>

        {/if}

        {if $additional_report.platform_results|@count gt 0}

            <div class="header_row_for_tables" >
                <div style="display: inline-block; margin: 18px 0 0 10px;">
                    <span class="header_text">Operating Systems</span>
                </div>
                <div style="display: inline-block; float: right;  margin: 12px 10px 0 0 ;">
                    {* <i class="fa fa-windows header_icon" aria-hidden="true"></i> *}
                </div>
            </div>
            <table class="table table-bordered table-striped span8" id="mytable_os" style="">
            <thead class="">
            <tr>
                <th class="equal-col">Platform</th>
                <th  class="equal-col">Click Count</th>
            </tr>
            </thead>
            <tbody>
            {$platforms = $additional_report.platform_results|json_decode:true}
            {foreach from=$platforms item=platform}
            <tr>
                <td>{$platform.platform}</td>
                <td>{$platform.cnt}</td>
            </tr>
            {/foreach}
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2"><div id="container-platform" style="height: 100%; width:100%; "></div></td>
            </tr>
            </tfoot>
             </table>

        {/if}

        {if $additional_report.browsers_shares|@count gt 0}

            <div class="header_row_for_tables" >
                <div style="display: inline-block; margin: 18px 0 0 10px;">
                    <span class="header_text">Web Browsers</span>
                </div>
                <div style="display: inline-block; float: right;  margin: 12px 10px 0 0 ;">
                    {* <i class="fa fa-edge header_icon" aria-hidden="true"></i> *}
                </div>
            </div>
            <table class="table table-bordered table-striped span8" id="mytable_browser" style="">
            <thead class="">
            <tr>
                <th  class="equal-col">Browser</th>
                <th  class="equal-col">Click Count</th>
            </tr>
            </thead>
            <tbody>
            {$browsers = $additional_report.browsers_shares|json_decode:true}
            {foreach from=$browsers key=bn item=b}
            <tr>
                <td>{$bn}</td>
                <td>{$b}</td>
            </tr>
            {/foreach}
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2"><div id="container" style="height: 100%; width:100%;"></div></td>
            </tr>
            </tfoot>
        </table>

        {/if}
    </div>

</div>

<script src="/v2/js/jquery-2.0.3.min.js"></script>
<script src="/v2/js/bootstrap.min.js"></script>
<script src="/v2/js/jquery.tablesorter.min.js"></script>
<!--<script src="/static/js/campclick.js"></script>-->

<script src="/static/js/jquery.validate.min.js"></script>
<script src="/static/js/additional-methods.min.js"></script>
<script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<style>
    tr {
        page-break-inside: avoid;
        /*display: table-row;*/
    }
    thead {
        display: table-header-group;
    }
    tfoot {
        display: table-row-group;
    }
    footer,header {
        display: none;
    }
    footer h5, footer ul {
        display:none;
    }
    .equal-col {
        width: 50%;
    }
    .header_row_for_tables {
        width: 100%;
        height: 50px;
        background-color: lightgrey;
        margin:20px 0px;
        border: 1px solid lightgrey;
    }
    .header_row {
        width: 100%;
        height: 50px;
        background-color: lightgrey;
        border-bottom: 1px solid lightgrey;
    }
    .header_text{
        color: white;
        font-weight: bold;
        font-size: 18px;
    }
    .header_icon{
        font-size: 26px;
        color: orange;
    }
    .chart_icon{
        color: orange;
    }
    .campaign_info_header{
        font-size: 18px;
        color: orange;
    }
    .pdf-title {
        font-size: 28px;
        color: gray;
        font-weight: bold;
        font-style: italic;
        margin-top: 40px;
    }
    .block_title {
        color: orange;
    }
    .block_border {
        border: 1px solid lightgray;
    }
    .block_border_top {
        border-top: 1px solid lightgrey;
    }
    .block_device{
        border: 1px solid lightgrey;
        background-color: lightgrey;
    }

</style>

{include file="v2/sections/footer.php"}



<script src="http://code.highcharts.com/highcharts.js"></script>

<script type="text/javascript">
    //    var j = $.noConflict();
//    $(function () {
//        $('#container-linechat').highcharts({
//            chart: {
//                type: 'line',
//                margin: [ 50, 50, 100, 80]
//            },
//            credits: {
//                enabled: false
//            },
//            title: {
//                text: '{$rep.title}',
//                x: -20 //center
//            },
//            subtitle: {
//                text: '',
//                x: -20
//            },
//            plotOptions: {
//                line: {
//                    dataLabels: {
//                        enable: true
//                    }
//                }
//            },
//            xAxis: {
//                title: {
//                    text: '{$rep.title}'
//                },
//                categories: [{$rep.date}],
//        labels: {
//            rotation: -45,
//                align: 'right',
//                style: {
//                fontSize: '13px',
//                    fontFamily: 'Verdana, sans-serif'
//            }
//        }
//    },
//        yAxis: {
//            title: {
//                text: 'Number of Clicks'
//            },
//            plotLines: [{
//                value: 0,
//                width: 1,
//                color: '#808080'
//            }]
//        },
//        tooltip: {
//            valueSuffix: ''
//        },
//        legend: {
//            layout: 'vertical',
//                align: 'right',
//                verticalAlign: 'top',
//                x: -10,
//                y: 100,
//                borderWidth: 0
//        },
//        series: [
//            {
//                name: 'Total Clicks',
//                data: [{$rep.click_data}]
//    },
//        {
//            name: 'Unique Clicks',
//                data: [{$rep.unique_clicks_data}]
//        },
//        {
//            name: 'Impressions (Views)',
//                data: [{$rep.impressions_data}]
//        },
//        {
//            name: 'Mobile Clicks',
//                data: [{$rep.mobile_data}]
//        }
//        ]
//    });
//    });

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
            animation: false,
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
            animation: false,
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
            animation: false,
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

