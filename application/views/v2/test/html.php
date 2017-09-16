{include file="v2/sections/header.php"}
<base href="{$base_url}">
<link href="{$base_url}/public/css/styles.css" rel="stylesheet" type="text/css"/>
<link href="{$base_url}/public/jquery-ui/jquery-ui.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="/v2/css/jquery.steps.css">
<link href="/v2/js/chosen/chosen.min.css" rel="stylesheet" type="text/css"/>
<link href="/v2/css/datetime-picker.css" rel="stylesheet" type="text/css"/>
<link href="/v2/js/jquery-toggles-master/css/toggles.css" rel="stylesheet" type="text/css"/>
<link href="/v2/js/jquery-toggles-master/css/themes/toggles-light.css" rel="stylesheet" type="text/css"/>


<div class="theme-report-row-wrap">
    <div class="theme-container container-fluid">
        <div class="theme-report-campaign-list-row">
            <div class="theme-report-tabbed-section">
                <div class="row" >
                    <div class="col-sm-6" >
                        <h1 class="campaign_name" >AUDI SELL DOWN</h1>
                    </div>
                    <div class="col-sm-6" >
                        <button class="btn btn-success pull-right" >
                            <img src="/v2/images/icons/report_icon.png" />
                            VIEW REPORT
                        </button>
                    </div>
                </div>
                <div class="row" >
                    <div class="col-sm-6 campaign_info_block" >
                        <p><span class="txt_bold" >CAMPAIGN TYPE</span> : Display Targeting</p>
                        <p><span class="txt_bold" >START DATE</span> : 11-08-2015 08:10  <span class="txt_bold ad_end_date" >END DATE</span> : 11-08-2015</p>
                    </div>
                </div>
                
                <table class="table table-responsive ad_table" cellpadding="10" >
                    {foreach from=$ads item=v}
                    <tr>
                        <td class="td_success td40" >
                            <img src="/v2/images/ad_image.png" />
                        </td>
                        <td class="td_success td40" >
                            <p>www.apple.com/apple-event/september-2015 <img class="btn_edit" width="27" src="/v2/images/icons/btn_edit.png" /> </p>
                            <p><span class="txt_bold" >Total Clicks</span> : 5782</p>
                            <p>% <span class="txt_bold" >Served</span> : 73% </p>
                            <div class="theme-report-progress progress"><div class="progress-bar theme-report-progress-bar progress-bar-blue" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%;">50%</div></div>
                        </td>
                        <td class="td_success td20" >
                            <span class="txt_bold" >Ad is Active</span>
                            <p><a class="toggle toggle-light"></a></p>
                        </td>
                    </tr>
                    {/foreach}
                    {*
                    <tr>
                        <td class="td_success td40" >
                            <img src="/v2/images/ad_image.png" />
                        </td>
                        <td class="td_success td40" >
                            <p>www.apple.com/apple-event/september-2015 <img class="btn_edit" width="27" src="/v2/images/icons/btn_edit.png" /> </p>
                            <p><span class="txt_bold" >Total Clicks </span>: 5782</p>
                            <p>% <span class="txt_bold" >Served</span> : 73% </p>
                            <div class="theme-report-progress progress"><div class="progress-bar theme-report-progress-bar progress-bar-blue" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%;">50%</div></div>
                        </td>
                        <td class="td_success td20" >
                            <span class="txt_bold" >Ad is Active</span>
                            <p><a class="toggle toggle-light"></a></p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_error td40" >
                            <img src="/v2/images/ad_image.png" />
                        </td>
                        <td class="td_error" colspan="2" >
                            <p class="alert alert-danger ad_alert" >
                                The AD has been rejected by the network Click here to view the reason
                                <span class="glyphicon glyphicon-remove ad_notice_remove" ></span>
                            </p>
                            <p>www.apple.com/apple-event/september-2015 <img class="btn_edit" width="27" src="/v2/images/icons/btn_edit.png" /> </p>
                            <p><span class="txt_bold" >Total Clicks</span> : 5782</p>
                            <p>% <span class="txt_bold" >Served</span> : 73% </p>
                            <div class="theme-report-progress progress"><div class="progress-bar theme-report-progress-bar progress-bar-blue" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%;">50%</div></div>
                        </td>
                    </tr>
                    *}
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
{include file="v2/sections/scripts.php"}

<script src="/static/js/heatmap.min.js"></script>
<script src="/v2/js/datetime-picker.jquery.js"></script>
<script src="/v2/js/jquery.steps.min.js"></script>
<script src="/v2/js/jquery.validate.min.js"></script>
<script src="/v2/js/underscore.js"></script>
<script src="/v2/js/chosen/chosen.jquery.js"></script>
<script src="/v2/js/jquery-toggles-master/toggles.min.js"></script>
{include file="v2/test/wizard/html_templates.php"}
{literal}
<script>
    $(document).ready(function(){
        $('.toggle').toggles({clicker:$('.clickme')});
    });
</script>
{/literal}
</body>
</html>
