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
    <div class="container">
    <div class="alert alert-error" id="err_bof" style="display:none;">
        <a class="close" data-dismiss="alert">X</a>
        <strong id="err_bof_message"></strong>
    </div>

    <div class="alert alert-success" id="success_bof" style="display:none;">
        <a class="close" data-dismiss="alert">X</a>
        <strong id="success_bof_message"></strong>
    </div>

    <!--  START SUMMARY HEADER -->
    <div class="email_row">
        <h2 class="span12">{$campaign.name} ({$io})</h2>
    </div>

    <div class="email_row">
        <h4 class="span12">Tracking Report</h4>
        {if ( strtotime('now') > strtotime('+1 day', strtotime($campaign.campaign_start_datetime)) && ( empty($campaign.campaign_end_datetime) || ( !empty($campaign.campaign_end_datetime) && strtotime($campaign.campaign_end_datetime) > strtotime('+1 day', strtotime($campaign.campaign_start_datetime) ) ) ) )}
            <a href="{base_url()}v2/campaign/download_pdf?file_name={$campaign.name|replace:'/':''} {$campaign.io|replace:'/':''} for 24H.pdf" >24 Hours &nbsp;|&nbsp;</a>
        {/if}
        {if ( strtotime('now') > strtotime('+2 days', strtotime($campaign.campaign_start_datetime)) && ( empty($campaign.campaign_end_datetime) || ( !empty($campaign.campaign_end_datetime) && strtotime($campaign.campaign_end_datetime) > strtotime('+2 days', strtotime($campaign.campaign_start_datetime) ) ) ) )}
            <a href="{base_url()}v2/campaign/download_pdf?file_name={$campaign.name|replace:'/':''} {$campaign.io|replace:'/':''} for 48H.pdf" >48 Hours &nbsp;|&nbsp;</a>
        {/if}
        {if ( strtotime('now') > strtotime('+4 days', strtotime($campaign.campaign_start_datetime)) && ( empty($campaign.campaign_end_datetime) || ( !empty($campaign.campaign_end_datetime) && strtotime($campaign.campaign_end_datetime) > strtotime('+4 days', strtotime($campaign.campaign_start_datetime) ) ) ) )}
            <a href="{base_url()}v2/campaign/download_pdf?file_name={$campaign.name|replace:'/':''} {$campaign.io|replace:'/':''} for 96H.pdf" >96 Hours &nbsp;|&nbsp;</a>
        {/if}
        <a href="{base_url()}v2/campaign/download_pdf_for_all_time/{$campaign.id}" >All Time</a>
    </div>

    <div class="email_row">
        <div class="span2" style="margin-right: 42px; float: left">
            <h6>Estimated Quantity:</h6>
        </div>
        <div class="span2">
            <h6>{$campaign.total_clicks}</h6>
        </div>
        <div class="span8">
            <h6 class="pull-right">Start Date: {$campaign.campaign_start_datetime}</h6>
        </div>
    </div>
    <div class="email_row">
        <div class="span2" style="margin-right: 67px; float: left">
            <h6>Total records:</h6>
        </div>
        <div class="span2">
            <h6>{$campaign.total_records}</h6>
        </div>
        <div class="span8">
            <h6 class="pull-right">Campaign Status: {$campaign.campaign_status}</h6>
        </div>
    </div>
    <div class="email_row">
        <div class="span2" style="margin-right: 60px; float: left">
            <h6>Total Bounces:</h6>
        </div>
        <div class="span2">
            <h6>{$campaign.total_bounces}</h6>
        </div>
        <div class="span8">

            <div class="pull-right" style="display: inline-block; width: 140px">
                <h6 class="pull-right">Campaign Progress:</h6>
                <h6 class="progress progress-striped"  style="display: inline-block; width: 140px; margin-top: 10px;">
                    <div class="bar progress-bar-success" style="width: {(($total_report.clicks_count+$total_report.impressions_count)/$campaign.total_clicks)*100}%; height: 100%"></div>
                </h6>
                <span style="color:#339bb9;">Mobile ({$total_report.mobile_clicks_count})</span><br/>
                <span style="color:#62c462;">Desktop ({$additional_report.non_mobile_clicks_count})</span><br/>
            </div>
        </div>
    </div>
    <div class="email_row">
<!--        <div class="span2" style="margin-right: 45px; float: left">-->
<!--            <h6>Campaign Status:</h6>-->
<!--        </div>-->
<!--        <div class="span2"  style="width: 140px; display: inline-block">-->
<!--            <h6>{$campaign.campaign_status}</h6>-->
<!--        </div>-->
<!--        <div class="span8">-->
<!--            {* <h6 class="pull-right">Campaign Type: PPC {if $extended_campaign.is_email_campaign == "Y"} &amp; Email{/if}</h6> *}-->
<!--        </div>-->
    </div>

    <div class="email_row">
<!--        <div class="span2" style="margin-right: 30px; float: left">-->
<!--            <h6>Campaign Progress:</h6>-->
<!--        </div>-->
<!--        <div class="span2" style="display: inline-block; width: 140px">-->
<!--            <h6 class="progress progress-striped"  style="display: inline-block; width: 140px">-->
<!--                <div class="bar progress-bar-success" style="width: {(($total_report.clicks_count+$total_report.impressions_count)/$campaign.total_clicks)*100}%; height: 100%"></div>-->
<!--            </h6>-->
<!--            <span style="color:#339bb9;">Mobile ({$total_report.mobile_clicks_count})</span><br/>-->
<!--            <span style="color:#62c462;">Desktop ({$total_report.clicks_count})</span><br/>-->
<!--            {if $extended_campaign.fire_open_pixel == "Y"}<span style="color:#fbb450;">Impressions: {$report.impressions_total}</span>{/if}-->
<!--        </div>-->
<!---->
<!--        <div class="span8">-->
<!--<!--            <h6><a href="/geo/geoquery/{$campaign.io}/{if $extended_campaign.fire_open_pixel == 'Y'}true{else}false{/if}/true" class="pull-right btn">Download IP Logs</a></h6>-->
<!--        </div>-->
    </div>
    <!--  END SUMMARY HEADER -->

<!--    <div class="email_row span12">-->
<!--        <hr/>-->
<!--    </div>-->
        </div>

    <!--  START PAGE CONTENT  -->

    <div class="email_row span12">
        <div class="btn-group">
            <a class="btn {if $range == 'hour'}btn-inverse{/if}" href="{$base_url}v2/campaign/email_reporting/{$campaign.id}/{$offset}/hour">24 Hours</a>
            <a class="btn {if $range == 'month'}btn-inverse{/if}" href="{$base_url}v2/campaign/email_reporting/{$campaign.id}/{$offset}/month">Last 30 days</a>
            <a id="dt-range-selector" class="btn {if $range == 'daterange'}btn-inverse{/if}">Date Range</a>
        </div>
        <div id="date-selection-form" class="pull-right" style="display: none;">
            <form name="date-select" id="date-select" action="#" method="post" class="form-horizontal">
                <input type="hidden" name="date_url" id="date_url" value="{$base_url}v2/campaign/email_reporting/{$campaign.id}/{$offset}/daterange" />
                <input type="text" class="form-control" size="25" name="sDate" id="startDate" value="Start Date" onblur="if(this.value=='') this.value='Start Date'" onfocus="if(this.value=='Start Date') this.value= ''" />
                <input type="text" class="form-control" size="25" name="eDate" id="endDate" value="End Date" onblur="if(this.value=='') this.value='End Date'" onfocus="if(this.value=='End Date') this.value= ''"  />
                <input type="hidden" name="action_url" id="action_url" value="{$base_url}campclick/date_range_report/{$io}" />
                <input type="button" name="btn" id="date-range-search" class="btn btn-info form-horizontal" value="Filter" />
            </form>
        </div>
    </div>

    <div class="email_row span12">
        <hr/>
    </div>

    <div class="email_row">
        <div class="col-md-4 col-sm-3 span4">
            {if $campaign.id==1791}
            <img src="http://45.33.7.188:3000/screenshots/RBEMNYC1.png" />
            {else}
                <img src="http://45.33.7.188:3000/screenshots/{$io}.png" />
            {/if}
        </div>
        <div class="col-md-8 col-sm-9 span8" >
            <div id="container-linechat" style="height: 700px; width: 100%"></div>

            <br/>

            <table class="table table-bordered table-striped span8" id="mytable_links">
                <thead class="dark_bg">
                <tr>
                    <th>Destination Link</th>
                    <th style="padding-right: 30px">Clicks</th>
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

            <br/>

            {if $additional_report.mobile_devices|@count gt 0}
            <hr />
            <h2 class="span8">Mobile Devices</h2>
            <table class="table table-bordered table-striped table-responsive span8" id="mytable_mobile">
                <thead class="dark_bg">
                <tr>
                    <th>Mobile Devices</th>
                    <th>Click Count</th>
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
                <tr>
                    <td colspan="2"><div id="container-devices" style="height: 500px; width:100%;"></div></td>
                </tr>
                </tfoot>
            </table>
            {/if}

            {if $additional_report.platform_results|@count gt 0}
            <hr />
            <h2 class="span8">Operating Systems</h2>
            <table class="table table-bordered table-striped span8" id="mytable_os">
                <thead class="dark_bg">
                <tr>
                    <th>Platform</th>
                    <th>Click Count</th>
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
                    <td colspan="2"><div id="container-platform" style="height: 500px; width:100%;"></div></td>
                </tr>
                </tfoot>
            </table>
            {/if}

            {if $additional_report.browsers_shares|@count gt 0}
            <hr />
            <h2 class="span8">Web Browsers</h2>
            <table class="table table-bordered table-striped span8" id="mytable_browser">
                <thead class="dark_bg">
                <tr>
                    <th>Browser</th>
                    <th>Click Count</th>
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
                    <td colspan="2"><div id="container" style="height: 500px; width:100%;"></div></td>
                </tr>
                </tfoot>
            </table>
            {/if}
        </div>
    </div>
</div>

<script src="/v2/js/jquery-2.0.3.min.js"></script>
<script src="/v2/js/bootstrap.min.js"></script>
<script src="/v2/js/jquery.tablesorter.min.js"></script>
<!--<script src="/static/js/campclick.js"></script>-->

<script src="/static/js/jquery.validate.min.js"></script>
<script src="/static/js/additional-methods.min.js"></script>
<script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<script>
    $(document).ready(function(){
        $("#mytable_links").tablesorter();
        $("#mytable_os").tablesorter();
        $("#mytable_browser").tablesorter();
        $("#mytable_mobile").tablesorter();
        $("#date-range-search").click(function(){
            document.location.href = $("#date_url").val() + "/" + $("#startDate").val() + "/" + $("#endDate").val();
        });
        $(".show-modal").click(function(){
            $(".alert-error").hide();
            $(".alert-success").hide();
            if ($(this).data("modaltype") == "create_link")	{
                $("#myModal_createLink").modal();
            } else if ($(this).data("modaltype") == "message_content")	{
                $("#myModal_messageContent").modal();
                $.ajax({
                    url: "/campclick/get_message",
                    type: "POST",
                    dataType: "json",
                    data: { io: $(this).data("io") },
                    success: function(msg)	{
                        $("#message_result").val(msg.campaign.message);
                    }
                });
            } else {
                // do nothing
                alert("no match found for modal click");
            }
        });
        $(".edit-link").click(function(){
            var io = $(this).data("io");
            var link_id = $(this).data("linkid");

            $.ajax({
                url: "/campclick/get_link",
                type: "POST",
                dataType: "json",
                data: {
                    'io': io,
                    'link_id': link_id
                },
                success: function(msg)   {
                    if (msg.status == "SUCCESS")    {
                        $("#edit_dest_url").val(msg.link.dest_url);
                        $("#edit_max_clicks").val(msg.link.max_clicks);
                        $("#edit_link_id").val(link_id);
                        $("#edit_fulfilled").val(msg.link.is_fulfilled);
                        $("#myModal_editLink").modal();
                    }
                }
            });
        });
        $(".update-link").click(function(){
            $.ajax({
                url: "/campclick/update_link",
                type: "POST",
                dataType: "json",
                data: {
                    dest_url: $("#edit_dest_url").val(),
                    max_clicks: $("#edit_max_clicks").val(),
                    link_id: $("#edit_link_id").val(),
                    fulfilled: $("#edit_fulfilled").val(),
                    io: $("#edit_link_io").val(),
                },
                success: function(msg)   {
                    if (msg.status == "SUCCESS")    {
                        document.location.reload();
                    } else {
                        $("#myModal_editLink_error").html("Current quantity of clicks: " + msg.cur_max_clicks + " of maximum clicks: " + msg.max_clicks);
                        $("#myModal_editLink_error").show();
                    }
                }
            });
        });


        $(".create-link").click(function(){
            $.ajax({
                url: "/campclick/update_link/create",
                type: "POST",
                dataType: "json",
                data: {
                    io: $("#create_io").val(),
                    dest_url: $("#create_dest_url").val(),
                    max_clicks: $("#create_max_clicks").val()
                },
                success: function(msg)   {
                    if (msg.status == "SUCCESS")    {
                        document.location.reload();
                    }
                }
            });
        });

    });
</script>

{include file="campclick/sections/modal.php"}
{include file="v2/sections/footer.php"}
{include file="campclick/sections/chart-scripts.php"}
