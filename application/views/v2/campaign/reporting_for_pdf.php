{include file="v2/sections/header.php"}
<link href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
<link rel="stylesheet" href="/v2/css/elusive-icons.min.css">
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

	 .keeptogether {
		page-break-inside:avoid !important;
	}
.dont-break-out {

  /* These are technically the same, but use both */
  overflow-wrap: break-word;
  word-wrap: break-word;

  -ms-word-break: break-all;
  /* This is the dangerous one in WebKit, as it breaks things wherever */
  word-break: break-all;
  /* Instead use this non-standard one: */
  word-break: break-word;

  /* Adds a hyphen where the word breaks, if supported (No Blink) */
  -ms-hyphens: auto;
  -moz-hyphens: auto;
  -webkit-hyphens: auto;
  hyphens: auto;

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

        <div class="block_border" style="height: 100px; width: 1030px;">

            <div style="height: 120px; display: inline-block; float: left;">
                <div style="width: 450px; margin: 20px 0 0 15px;">
                    <span class="campaign_info_header">CAMPAIGN: </span><span style="font-weight:bold; font-size:18px;">{$campaign.name}</span>
                    {if !empty($campaign.so)}
	                    <br/><br/><span class="campaign_info_header">SO #: </span><span style="">{$campaign.so}</span>
                    {/if}
                    
                </div>
            </div>
            <div style="height: 120px; display: inline-block; float: right;">
                <div style="margin: 20px 15px 0 0;">
                    <span class="campaign_info_header">IO #: </span><span style="">{$campaign.io}</span><br><br>
                    <span class="campaign_info_header">DEPLOYMENT: </span><span style="">{$start_date|date_format:"%D"} - {$end_date|date_format:"%D"}</span>
                </div>
            </div>
        </div>


        <div class="span8"  class="keeptogether">

        	{* BLOCKED OUT AREA *}
        	<br/><br/>
        	<div style="width:550px; display: inline-block;" >
                <div class="block_border" style="width: 100%; height: 175px; ">
                    <div class="header_row">
                        <div style="display: inline-block; margin: 18px 0 0 10px;">
                            <span class="header_text">CAMPAIGN SUMMARY</span>
                        </div>
                        <div style="display: inline-block; float: right;  margin: 12px 10px 0 0 ;">
                        </div>
                    </div>
                    <div style="margin: 18px 0 0 10px;">
                        <span class="block_title" style="width: 100px; display: inline-block;">Clicks:</span><span>{$campaign.total_clicks_count}</span>
                    </div>
                    <div style="margin: 18px 0 0 10px;">
                        <span class="block_title" style="width: 100px; display: inline-block;">Impressions:</span><span>{$campaign.total_impressions_count}</span>
                    </div>
                    <div style="margin: 18px 0 0 10px;">
                        <span class="block_title" style="width: 100px; display: inline-block;">CTR:</span><span>{(($campaign.total_clicks_count / $campaign.total_impressions_count)*100)|sprintf:"%.2f"}%</span>
                    </div>
                    <br/><br/>
                </div>
            </div>

           <div id="ads_div_with_scroll" class="addBanner" style="float:left;">
                            <div class="theme-total-click-list-wrap theme-nicescroll-holder" style="overflow-y:auto;height:auto;float: left;">
                                    {if $campaign.campaign_type == 'TEXTAD'}
                                        {foreach from=$ads item=ad}
                                           <div style="float:left;" >
                                             <div class="col-md-4 col-sm-6 col-xs-3">
							                    <h2><a href="">{$ad.title}</a></h2>
                                                <p>{$ad.description_1} {$ad.description_2}</p>							 
							                </div>
							                <div class="col-md-8 col-sm-6 col-xs-9" >
							                    <div class="col-md-12" style="padding-top: 23px;">
							                          <p><span class="txt_bold" >Total Clicks</span> :{$ad.clicks_count} </p>
							                          <p><span class="txt_bold" >Total Impressions</span> : {$ad.impressions_count}</p>
							                          {if !empty($campaign.total_clicks_count)}{$persent = $ad.clicks_count*100/$campaign.total_clicks_count}{/if}
							                          </div>
							                </div>
              							   </div>
                                        {/foreach}
                                    {else if $campaign.campaign_type == 'PUSH_CLICK_TO_CALL'}

                                        {foreach from=$ads item=ad}
                                             <div style="float:left;" >
                                             <div class="col-md-4 col-sm-6 col-xs-3">
							                    <h2><a href="">{$ad.title}</a></h2>
					                            <p>{$ad.description_1} {$ad.description_2}</p>
					                            <p>{$ad.airpush_image_type}</p>
							 
							                </div>
							                <div class="col-md-8 col-sm-6 col-xs-9" >
							                    <div class="col-md-12" style="padding-top: 23px;">
						                        <p><span class="txt_bold" >Total Clicks</span> : {$ad.clicks_count}</p>
						                        <p><span class="txt_bold" >Total Impressions</span> : {$ad.impressions_count}</p>
						                        {if !empty($campaign.total_clicks_count)}{$persent = $ad.clicks_count*100/$campaign.total_clicks_count}{/if}						                        
							                    </div>
							              
							                </div>
              							   </div>
                                        {/foreach}
                                    {else if $campaign.campaign_type == 'RICH_MEDIA_INTERSTITIAL'}
                                        {foreach from=$ads item=ad}
                                        
                                           <div style="float:left;" >
                                             <div class="col-md-4 col-sm-6 col-xs-3">
							                     <textarea disabled="disabled" class="form-control script" style="width: 100% !important;" />{$ad.script}</textarea>
							 
							                </div>
							                <div class="col-md-8 col-sm-6 col-xs-9" >
							                    
							                    <div class="col-md-12" style="padding-top: 23px;">
							                        <p><span class="txt_bold" >Total Clicks</span> : {if !$ad.clicks_count}0{else}{$ad.clicks_count}{/if}</p>
							                        <p><span class="txt_bold" >Total Impressions</span> : {if !$ad.impressions_count}0{else}{$ad.impressions_count}{/if}</p>

							                    </div>
							              
							                </div>
              							   </div>
              							   
                                           
                                        {/foreach}
                                    {else}

                                        {foreach from=$ads item=ad}
<!--                                        <li class="theme-total-click-item theme-ad-banner-item theme-pos-rel">-->
                                            <!--<span class="theme-list-remove-icon closer"></span>-->
                             
                                            
                                            <div style="float:left;" >
                                             <div class="col-md-4 col-sm-6 col-xs-3" >
                                                {if !isset($pdf)}
	                                            <h6>
	                                                {if $campaign.campaign_type == 'FB-VIDEO-VIEWS' || $campaign.campaign_type == 'FB-VIDEO-CLICKS' || $campaign.campaign_type == 'VIDEO_YAHOO'}
	                                                Ad : Video {$ad.video_duration}
	                                                {else}
	                                                Ad : {$ad.creative_width} X {$ad.creative_height} banner
	                                                {/if}
	                                                
	                                            </h6>
	                                            {/if}
							                    {if $ad.creative_height > 155}
							                    <img id="popover" src="{$ad.creative_url}"  data-trigger="hover" data-content="<img src='{$ad.creative_url}' />" class="img-responsive ad-list-image" data-toggle="popover">
							                    <!--                    <img data-trigger="hover" data-toggle="popover"  data-content="<img src='{$ad.creative_url}' />" height="115" src="{$ad.creative_url}" />-->
							                    {else}
							                    {$margin = (155 - $ad.creative_height) / 2}
							                    <img id="popover" src="{$ad.creative_url}"  data-trigger="hover" class="img-responsive ad-list-image" data-toggle="popover">
							                    {/if}
							
							                </div>
							                <div class="col-md-8 col-sm-6 col-xs-9" >
							                    <div style="padding-top: 23px;">
							                        <p><span class="txt_bold" >Total Clicks</span> : {if !$ad.clicks_count}0{else}{$ad.clicks_count}{/if}</p>
							                        <p><span class="txt_bold" >Total Impressions</span> : {if !$ad.impressions_count}0{else}{$ad.impressions_count}{/if}</p>
							                        <p><span class="txt_bold" >Destination Url</span> : <a href="{$ad.redirect_url}">{$ad.redirect_url}</a></p>
							                    </div>
							                </div>
              							   </div>
<!--                                        </li>-->
                                        {/foreach}
                                    {/if}
<!--                                </ul>-->
                            </div>
                        </div>


         <div class="clearfix"></div>
        </div>
        
		<div class="keeptogether">
                <div id="container-linechat" style="height: 700px; width: 100%"></div>
		</div>
        
        	
        {if $additional_report.mobile_device|@count gt 0}
		<div class="keeptogether">
            <div class="header_row_for_tables" >
                <div style="display: inline-block; margin: 18px 0 0 10px;">
                    <span class="header_text">Mobile Devices</span>
                </div>
                <div style="display: inline-block; float: right;  margin: 12px 10px 0 0 ;">
                    <i class="el el-laptop header_icon_pdf"></i>
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
                     
                    {foreach from=$additional_report.mobile_device item=mobile_device}
                    <tr>
                        <td>{$mobile_device.data_filed}</td>
                        <td>{$mobile_device.click_count}</td>
                    </tr>
                    {/foreach}
                </tbody>
                <tfoot>
                    <tr><td colspan="2"><div id="container-devices" style="height: 100%; width:100%;"></div></td></tr>
                </tfoot>
            </table>
		</div>
        {/if}

        {if $additional_report.platforms|@count gt 0}
		<div class="keeptogether">
            <div class="header_row_for_tables" >
                <div style="display: inline-block; margin: 18px 0 0 10px;">
                    <span class="header_text">Operating Systems</span>
                </div>
                <div style="display: inline-block; float: right;  margin: 12px 10px 0 0 ;">
                   <i class="el el-th-large header_icon_pdf"></i>
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
            
            {foreach from=$additional_report.platforms item=platform}
            <tr>
                <td>{$platform.data_filed}</td>
                <td>{$platform.click_count}</td>
            </tr>
            {/foreach}
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2"><div id="container-platform" style="height: 100%; width:100%; "></div></td>
            </tr>
            </tfoot>
             </table>
		</div>
        {/if}

        {if $additional_report.browsers_data|@count gt 0}
		<div class="keeptogether">
            <div class="header_row_for_tables" >
                <div style="display: inline-block; margin: 18px 0 0 10px;">
                    <span class="header_text">Web Browsers</span>
                </div>
                <div style="display: inline-block; float: right;  margin: 12px 10px 0 0 ;">
                    <i class="el el-website header_icon_pdf"></i>
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
            
            {foreach from=$additional_report.browsers_data key=bn item=b}
            <tr>
                <td>{$b.data_filed}</td>
                <td>{$b.click_count}</td>
            </tr>
            {/foreach}
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2"><div id="container" style="height: 100%; width:100%;"></div></td>
            </tr>
            </tfoot>
        </table>
		</div>
        {/if}
        
         {if $demograpics_data|@count gt 0}
		<div class="keeptogether">
            <div class="header_row_for_tables" >
                <div style="display: inline-block; margin: 18px 0 0 10px;">
                    <span class="header_text">User Gender</span>
                </div>
                <div style="display: inline-block; float: right;  margin: 12px 10px 0 0 ;">
                    <i class="fa fa-venus-mars header_icon_pdf" aria-hidden="true"></i>
                </div>
            </div>
            <table class="table table-bordered table-striped span8" id="mytable_browser" style="">
            <thead class="">
            <tr>
                <th  class="equal-col">Gender</th>
                <th  class="equal-col">Count</th>
            </tr>
            </thead>
            <tbody>
            
            
            <tr>
                <td>Male</td>
                <td>{$demograpics_data.male}</td>
            </tr>
            <tr>
                <td>Female</td>
                <td>{$demograpics_data.female}</td>
            </tr>
            <tr>
                <td>Unknown Gender</td>
                <td>{$demograpics_data.unknown_gender}</td>
            </tr>
            
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2"><div id="container-usergender" style="height: 100%; width:100%;"></div></td>
            </tr>
            </tfoot>
        </table>
		</div>
        {/if}
        
        {if $places|@count gt 0}
        <div class="keeptogether">
        <div class="header_row_for_tables" >
            <div style="display: inline-block; margin: 18px 0 0 10px;">
                <span class="header_text">Placements</span>
            </div>
            <div style="display: inline-block; float: right;  margin: 12px 10px 0 0 ;">
                <i class="el el-link header_icon_pdf"></i>
            </div>
        </div>

        <table class="table table-bordered table-striped span8" id="mytable_links" >
            <thead class="" >
            <tr>
                <th>Placements</th>
                <th>Impressions</th>
            </tr>
            </thead>
            <tbody>
             {$total = 0}
             {foreach from=$places item=c}
             {$total = $total + $c.impressions}
             {/foreach}
            {foreach from=$places item=c}
            <tr>
                <td style="word-break:break-all;">{$c.placement}</td>
                <td>{(($c.impressions*100)/$total)|string_format:"%.2f"}%</td>
            </tr>
            {/foreach}
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2"><div id="container-placement" style="height: 100%; width:100%;"></div></td>
            </tr>
            </tfoot>
        </table>
        </div>
		{/if}
		
        {if $demograpics_data|@count gt 0}
		<div class="keeptogether">
            <div class="header_row_for_tables" >
                <div style="display: inline-block; margin: 18px 0 0 10px;">
                    <span class="header_text">Users Age</span>
                </div>
                <div style="display: inline-block; float: right;  margin: 12px 10px 0 0 ;">
                  <i class="el el-group header_icon_pdf"></i>
                </div>
            </div>
            <table class="table table-bordered table-striped span8" id="mytable_browser" style="">
            <thead class="">
            <tr>
                <th  class="equal-col">Range</th>
                <th  class="equal-col">Count</th>
            </tr>
            </thead>
            <tbody>
            
            
            <tr>
                <td>18 - 24</td>
                <td>{$demograpics_data.18_24}</td>
            </tr>
            <tr>
                <td>25 - 34</td>
                <td>{$demograpics_data.25_34}</td>
            </tr>
            <tr>
                <td>35 - 44</td>
                <td>{$demograpics_data.35_44}</td>
            </tr>
            <tr>
                <td>45 - 54</td>
                <td>{$demograpics_data.45_54}</td>
            </tr>
            <tr>
                <td>55 - 64</td>
                <td>{$demograpics_data.55_64}</td>
            </tr>
            <tr>
                <td>64+</td>
                <td>{$demograpics_data['64+']}</td>
            </tr>
             <tr>
                <td>Unknown Age</td>
                <td>{$demograpics_data.unknown_age}</td>
            </tr>
            
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2"><div id="container-usersage" style="height: 100%; width:100%;"></div></td>
            </tr>
            </tfoot>
        </table>
		</div>
        {/if}
        
          {if $video_data|@count gt 0}
		<div class="keeptogether">
            <div class="header_row_for_tables" >
                <div style="display: inline-block; margin: 18px 0 0 10px;">
                    <span class="header_text">Watched Video Actions</span>
                </div>
                <div style="display: inline-block; float: right;  margin: 12px 10px 0 0 ;">
                    <i class="fa fa-user-circle-o header_icon_pdf" aria-hidden="true"></i>
                </div>
            </div>
            <table class="table table-bordered table-striped span8" id="mytable_browser" style="">
            <thead class="">
            <tr>
                <th  class="equal-col">Video Duration</th>
                <th  class="equal-col">Count</th>
            </tr>
            </thead>
            <tbody>
            
            
            <tr>
                <td>Under 10 sec</td>
                <td>{$video_data.10_sec}</td>
            </tr>
            <tr>
                <td>25%</td>
                <td>{$video_data.25_p}</td>
            </tr>
            <tr>
                <td>50%</td>
                <td>{$video_data.50_p}</td>
            </tr>
            <tr>
                <td>75%</td>
                <td>{$video_data.75_p}</td>
            </tr>
            <tr>
                <td>95%</td>
                <td>{$video_data.95_p}</td>
            </tr>            
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2"><div id="container-watchedvideo" style="height: 100%; width:100%;"></div></td>
            </tr>
            </tfoot>
        </table>
		</div>
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
    .header_icon_pdf{
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
	{if $click_data1|@count gt 0 || $click_data2|@count gt 0}
	$(function () {		
		{if $campaign.campaign_type == "FB-VIDEO-CLICKS"}
		var labelgraph = "Impressions";
		{else}
		var labelgraph = "Views";
		{/if}
	    $('#container-linechat').highcharts({
	   	    chart: {
	   	        type: 'spline'
	   	    },
	   	    title: {
	   	        text: 'Clicks & Impressions'
	   	    },
	   	    subtitle: {
	   	        text: ''
	   	    },
	   	    xAxis: {
	   	        type: 'datetime',
	   	        dateTimeLabelFormats: { // don't display the dummy year
					day: '%b %e',
	   	        	month: '%b %e',
	   	            year: '%b'
	   	        },
	   	        title: {
	   	            text: 'Date'
	   	        }
	   	    },
	   	    yAxis: {
	   	        title: {
	   	            text: ''
	   	        },
	   	        min: 0
	   	    },
	   	    plotOptions: {
	   	        spline: {
	   	            marker: {
	   	                enabled: true
	   	            }
	   	        }
	   	    },
	   	 series: [
	   	     	 {literal}{{/literal}name:"Clicks",data:{$click_data1|@json_encode}{literal}}{/literal},
		    	{literal}{{/literal}name:labelgraph,data:{$click_data2|@json_encode}{literal}}{/literal}    		
	   	         ]
	   	});
		
	 });
	{/if}
    {if $additional_report.browsers_data|@count gt 0}
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
                text: 'Web Browsers'
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
            name: 'Web Browsers',
            data: [
                   {foreach from=$additional_report.browsers_data item=p}
                   ['{$p.data_filed}',{$p.click_count}],
               {/foreach}
        ]
    }]
    });
    });
    {/if}
    
    {if $additional_report.platforms|@count gt 0}
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
            name: 'Operating System',
            data: [

                {foreach from=$additional_report.platforms item=p}
            ['{$p.data_filed}',{$p.click_count}],
        {/foreach}
        ]
    }]
    });
    });
    {/if}

    {if $additional_report.mobile_device|@count gt 0}
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
                    {foreach from=$additional_report.mobile_device item=m}
                ['{$m.data_filed}',{$m.click_count}],
            {/foreach}
            ]
        }]
        });
        });
     {/if}


         {if $demograpics_data|@count gt 0}
         $(function () {
             $('#container-usergender').highcharts({
                 chart: {
                     plotBackgroundColor: null,
                     plotBorderWidth: null,
                     plotShadow: false
                 },
                 credits: {
                     enabled: false
                 },
                 title: {
                     text: 'User Gender'
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
                 name: 'User Gender',
                 data: [
                        ['Male',{$demograpics_data.male}],
                        ['Female',{$demograpics_data.female}],
                        ['Unknown Gender',{$demograpics_data.unknown_gender}]
                     
             ]
         }]
         });
         });
   		 {/if}


   			{if $places|@count gt 0}
            $(function () {
                $('#container-placement').highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false
                    },
                    credits: {
                        enabled: false
                    },
                    title: {
                        text: 'Placements'
                    },
                    tooltip: {
                {literal}  pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b> ',{/literal}
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
                                return '<b>'+this.point.name+'</b> Impressions: '+this.point.y;
                            }
                        }
                    }
                },
                series: [{
                    type: 'pie',
                    animation: false,
                    name: 'Impressionsr',
                    data: [
                            {foreach from=$places item=m}
                			['{$m.placement}',{$m.impressions}],
            				{/foreach}
                        
                		  ]
            }]
            });
            });
         {/if}
             
    	  {if $demograpics_data|@count gt 0}
    	  $(function () {
    		  {$totalDuration = $demograpics_data.18_24 + $demograpics_data.25_34 +$demograpics_data.35_44 +$demograpics_data.45_54 +$demograpics_data.55_64}

    		  $("#container-usersage").highcharts({
    			    chart: {
    			        type: 'column'
    			    },
    			    title: {
    			        text: 'Users Age'
    			    },
    			    xAxis: {
    			        categories: [
    			            '18-24',
    			            '25-34',
    			            '35-44',
    			            '45-54',
    			            '55-64',
    			            '64+',
    			            'Unknown Age'
    			        ],
    			        crosshair: true
    			    },
    			    yAxis: {
    			        min: 0
    			    },
    			    legend: {
    			        enabled: false
    			    },
    			    tooltip: {
    		             {literal}  pointFormat: '<b>{point.y:.1f}%</b> ',{/literal}
    		             
    			    },
    			    plotOptions: {
    			        column: {
    			            pointPadding: 0.2,
    			            borderWidth: 0
    			        }
    			    },
    			    series: [{
    			      name: "Users Age",
    			      data: [{($demograpics_data.18_24 * 100) / $totalDuration}, {($demograpics_data.25_34 * 100) / $totalDuration}, {($demograpics_data.35_44 * 100) / $totalDuration}, {($demograpics_data.45_54 * 100 )/ $totalDuration}, {($demograpics_data.55_64 * 100) / $totalDuration}, {($demograpics_data["64+"] * 100) / $totalDuration}, {($demograpics_data.unknown_age * 100) / $totalDuration}],
    			      dataLabels: {
  			            enabled: true,
  			            rotation: -90,
  			            color: '#FFFFFF',
  			            align: 'right',
  			            {literal}format: '{point.y:.1f} %', {/literal}
  			            y: 10, // 10 pixels down from the top
  			            style: {
  			                fontSize: '13px',
  			                fontFamily: 'Verdana, sans-serif'
  			            }
  			        	}

    			    }]
    			});
    	
    	  });
    	  {/if}

    		  {if $video_data|@count gt 0}

    		  {if $campaign.campaign_type eq 'FB-VIDEO-VIEWS' or $campaign.campaign_type eq 'VIDEO_YAHOO' or $campaign.campaign_type eq 'FB-VIDEO-CLICKS'}

    			
                  //makeChartBarVideo('video_views', video_watched_array);
                var watchCount = {$video_data.10_sec} * 1 + {$video_data.25_p} * 1 + {$video_data.50_p} * 1 + {$video_data.75_p} * 1 + {$video_data.95_p} * 1;
                //console.log(watchCount);
                
                var first_video_watch = parseInt((({$video_data.10_sec} * 100) / watchCount).toFixed(1));
                var second_video_watch = parseInt((({$video_data.25_p} * 100) / watchCount).toFixed(1));
                var third_video_watch = parseInt((({$video_data.50_p} * 100) / watchCount).toFixed(1));
                var fourth_video_watch = parseInt((({$video_data.75_p} * 100) / watchCount).toFixed(1));
                var five_video_watch = parseInt((({$video_data.95_p} * 100) / watchCount).toFixed(1));
              
                var video_watched_array = [
                    
                    [ 'Under 10 Sec',  first_video_watch],
                    [ '25%',  second_video_watch],
                    [ '50%',  third_video_watch],
                    [ '75%',  fourth_video_watch],
                    [ '95%',  five_video_watch]


                ]; 

               $("#container-watchedvideo").highcharts({
			    chart: {
			        type: 'column'
			    },
			    title: {
			        text: 'Watched Video'
			    },
			    
			    xAxis: {
			        type: 'category',
			        labels: {
			            rotation: -45,
			            style: {
			                fontSize: '13px',
			                fontFamily: 'Verdana, sans-serif'
			            }
			        },
			        title: {
			            text: 'View duration'
			        }
			    },
			    yAxis: {
			        min: 0
			       
			    },
			    legend: {
			        enabled: false
			    },
			    tooltip: {
			    	 {literal}  pointFormat: '<b>{point.y:.1f} %</b>' {/literal}
			    },
			    series: [{
			        name: 'Population',
			        data: video_watched_array,
			        dataLabels: {
			            enabled: true,
			            rotation: -90,
			            color: '#FFFFFF',
			            align: 'right',
			            {literal}format: '{point.y:.1f} %', {/literal}
			            y: 10, // 10 pixels down from the top
			            style: {
			                fontSize: '13px',
			                fontFamily: 'Verdana, sans-serif'
			            }
			        }
			    }]
			});


              {/if}
    		  {/if}
</script>

