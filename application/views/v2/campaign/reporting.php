{include file="v2/sections/header.php"}
<div class="theme-report-row-wrap">
    <div class="theme-container">
        <div class="theme-report-charts-wrap theme-report-charts-row">
            <div class="row theme-row clearfix">
            <div class="col-md-7 col-sm-12 col-xs-12">
<!--                <div class="chart-block col-md-7 col-sm-12 col-xs-12 theme-chart-col  theme-piechart-col theme-lg-8 theme-sm-7 theme-xs-12"></div>-->
                <div class="chart-block">
                    {if $user_type != 'viewer'}

                    <div class="campaign-progress">
<div class="pdf_row">
									        <h4 class="span12">Tracking Report</h4>
									        {if ( strtotime('now') > strtotime('+1 day', strtotime($campaign.campaign_start_datetime)) && ( empty($campaign.campaign_end_datetime) || ( !empty($campaign.campaign_end_datetime) && strtotime($campaign.campaign_end_datetime) > strtotime('+1 day', strtotime($campaign.campaign_start_datetime) ) ) ) )}
									            <a href="{base_url()}v2/campaign/generate_report/{$campaign.id}/24" >24 Hours &nbsp;|&nbsp;</a>
									        {/if}
									        {if ( strtotime('now') > strtotime('+2 days', strtotime($campaign.campaign_start_datetime)) && ( empty($campaign.campaign_end_datetime) || ( !empty($campaign.campaign_end_datetime) && strtotime($campaign.campaign_end_datetime) > strtotime('+2 days', strtotime($campaign.campaign_start_datetime) ) ) ) )}
									            <a href="{base_url()}v2/campaign/generate_report/{$campaign.id}/48" >48 Hours &nbsp;|&nbsp;</a>
									        {/if}
									        {if ( strtotime('now') > strtotime('+4 days', strtotime($campaign.campaign_start_datetime)) && ( empty($campaign.campaign_end_datetime) || ( !empty($campaign.campaign_end_datetime) && strtotime($campaign.campaign_end_datetime) > strtotime('+4 days', strtotime($campaign.campaign_start_datetime) ) ) ) )}
									            <a href="{base_url()}v2/campaign/generate_report/{$campaign.id}/96" >96 Hours &nbsp;|&nbsp;</a>
									        {/if}
									        <a href="{base_url()}v2/campaign/generate_report/{$campaign.id}/all" >All Time</a>
								</div>
                            <h4>Campaign Progress</h4>
                            <div id="theme-piechart-holder" class="cm_progress pull-left" style=""></div>
                            <div class="pull-left progress_left_block">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <h5 class="theme-budget-label">Campaign Progress</h5>
                                    {if $campaign.is_thru_guarantee == 'Y' && $campaign.campaign_status != 'COMPLETED'}
                                        {$percent = 100*($campaign.total_impressions_count/$campaign.max_impressions)*1/2}
                                      <h1 id="campaign_total_budget" class="theme-budget-value">{$percent|string_format:"%.2f"}%</h1>
                                    {else}
                                        <h1 id="campaign_total_budget" class="theme-budget-value">
                                            {*
                                            {if $user.is_billing_type == 'PERCENTAGE'}

                                                    {if empty($campaign.max_budget)}

                                                        {if !empty($campaign.max_clicks)}

                                                            {$percent = $campaign.total_clicks_count/$campaign.max_clicks*100}
                                                            {$cost = $campaign.max_clicks*$user.display_click}

                                                            {if $percent<100}
                                                                {$cost|string_format:"%.2f"}
                                                                {$budget_left = ($cost*$percent/100)|string_format:"%.2f"}
                                                            {else}
                                                                {$cost|string_format:"%.2f"}
                                                                {$budget_left = 0.00}
                                                            {/if}
                                                        {else if !empty($campaign.max_impressions)}

                                                            {$percent = $campaign.total_impressions_count/$campaign.max_impressions*100}
                                                            {$cost = $campaign.max_impressions*$user.display_imp}
                                                            {if $percent<100}
                                                                {$cost|string_format:"%.2f"}
                                                                {$budget_left = ($cost*$percent/100)|string_format:"%.2f"}
                                                            {else}
                                                                {$cost|string_format:"%.2f"}
                                                                {$budget_left = ''}
                                                            {/if}

                                                        {else}
                                                            {$campaign.cost|string_format:"%.2f"}
                                                            {$budget_left = 0.00}
                                                        {/if}
                                                    {else}
                                                        {$cost = $campaign.percentage_max_budget - $campaign.cost}

                                                        {if ($cost>0)}
                                                            {$percent_cost = $cost*100/$campaign.percentage_max_budget}
                                                            {$campaign.max_budget|string_format:"%.2f"}
                                                            {$budget_left = ($campaign.max_budget*$percent_cost/100)|string_format:"%.2f"}
                                                        {else}
                                                            {$campaign.max_budget|string_format:"%.2f"}
                                                            {$budget_left = 0.00}
                                                        {/if}
                                                    {/if}

                                            {else}
                                                    {if !empty($campaign.max_budget)}
                                                        {$cost = $campaign.percentage_max_budget - $campaign.cost}

                                                        {if ($cost>0) && $campaign.campaign_status!='COMPLETED'}
                                                            {$percent_cost = $cost*100/$campaign.percentage_max_budget}
                                                            {$campaign.max_budget|string_format:"%.2f"}
                                                            {$budget_left = ($campaign.max_budget*$percent_cost/100)|string_format:"%.2f"}
                                                        {else}
                                                            {$campaign.max_budget|string_format:"%.2f"}
                                                            {$budget_left = 0.00}
                                                        {/if}
                                                    {else if !empty($campaign.max_clicks)}
                                                        {$tier = 'display_click_'|cat:$campaign.campaign_tier}
                                                        {$percent = $campaign.total_clicks_count/$campaign.max_clicks*100}
                                                        {$cost = $campaign.max_clicks*$user[$tier]}
                                                        {if $percent<100}
                                                            {$cost|string_format:"%.2f"}
                                                            {$budget_left = ($cost*$percent/100)|string_format:"%.2f"}
                                                        {else}
                                                            {$cost|string_format:"%.2f"}
                                                            {$budget_left = 0.00}
                                                        {/if}
                                                    {else if !empty($campaign.max_impressions)}

                                                        {$tier = 'display_imp_'|cat:$campaign.campaign_tier}
                                                        {$percent = $campaign.total_impressions_count/$campaign.max_impressions*100}
                                                        {$cost = $campaign.max_impressions*$user[$tier]}
                                                        {if $percent<100}
                                                            {$cost|string_format:"%.2f"}
                                                            {$budget_left = ($cost*$percent/100)|string_format:"%.2f"}
                                                        {else}
                                                            {$cost|string_format:"%.2f"}
                                                            {$budget_left = 0.00}
                                                        {/if}

                                                    {else}
                                                        {$campaign.cost|string_format:"%.2f"}
                                                        {$budget_left = ''}
                                                    {/if}

                                                {/if}
                                            *}
        {if $campaign.campaign_status == 'COMPLETED'}
            {$percent = 100}
            {$percent|string_format:"%.2f"}%
        {else}
            {$count = 0}
            {if !empty($campaign.max_budget)}
                {$count = $count + 1}
                {$cost = $campaign.percentage_max_budget - $campaign.cost}
                {if ($cost>0)}
                    {$percent_cost = $campaign.cost*100/$campaign.percentage_max_budget}
                {else}
                    {$percent_cost = 100}
                {/if}
            {/if}
            {if !empty($campaign.max_clicks)}
                {$percent_clicks = 100*$campaign.total_clicks_count/$campaign.max_clicks}
                {if $percent_clicks >= 100}
                    {$percent_clicks = 100}
                {/if}
                {$count = $count +1}
            {else}
                {$percent_clicks = 0}
            {/if}
            {if !empty($campaign.max_impressions)}
                {$percent_impressions = 100*$campaign.total_impressions_count/$campaign.max_impressions}
                {if $percent_impressions >= 100}
                    {$percent_impressions = 100}
                {/if}
                {$count = $count + 1}
            {else}
                {$percent_impressions = 0}
            {/if}
            {*
            {if !empty($campaign.date_diff) && $campaign.percent_diff < $campaign.date_diff}
                {$percent_date = 100*$campaign.percent_diff/$campaign.date_diff}

                {if $percent_date >= 100}
                    {$percent_date = 100}
                {/if}
                {$count = $count + 1}
            {else}
                {$percent_date = 0}
            {/if}
            *}
            {$percent = ($percent_cost + $percent_clicks + $percent_impressions)/$count}
            {$percent|string_format:"%.2f"}%
        {/if}



                                        </h1>
    {/if}
                                        <h5 class="theme-budget-label">Progress Remaining</h5>
                                        <h1 id="total_budget_spent" class="theme-budget-value">
                                            {100-$percent|string_format:"%.2f"}%
                                        </h1>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <p>
                                            Campaign progress is subject to multiple variables include traffic availability, budget, bid rate and more.
                                        </p>
                                        {if !isset($pdf)}
        <!--                                <a href="#" class="theme-btn theme-report-add-btn" id='view_campaign'>View Campaign Info</a>-->
                                        <button class="btn btn-success" id='view_campaign' type="button">
                                            Reset & View All Ads
                                        </button>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>

                     {/if}
                    <div class="campaignProg">
                        <form class="form-inline">
                            <div class="form-group">
                                <label class="range_picke_head">Select Date</label>
                                <input type="text" id="reportrange" class="form-control">
                            </div>
                        </form>
                        <div id="theme-area-chart-holder" class="theme-area-chart-holder"></div>
                        <!--<i class="glyphicon glyphicon-calendar fa fa-calendar icon_calendar"></i>-->
                    </div>
                    <hr>
<!--                    <div class="theme-chart-row theme-report-subrow theme-report-areachart-row">-->
<!--                        <div id="theme-area-chart-holder" class="theme-area-chart-holder"></div>-->
<!--                    </div>-->
                    {if $campaign.campaign_type != 'RICH_MEDIA_INTERSTITIAL' && $campaign.campaign_type != 'FB-PAGE-LIKE' && $campaign.campaign_type != 'FB-VIDEO-VIEWS'}
<!--                    <div class="line"></div>-->
                    <div class="web-browser">
                        <h2>Web Browsers</h2>
                        <div id="chart_browsers" class="theme-area-chart-holder"></div>
                    </div>
                    <hr>

                    <div class="mobile-devices">
                        <h2>Mobile Devices</h2>
                        <div id="mobile_device" class="theme-area-chart-holder"></div>
                    </div>
                    <hr>

                    <div class="operating-systems">
                        <h2>Operating Systems</h2>
                        <div id="platforms" class="theme-area-chart-holder"></div>
                    </div>
                    <hr>
                    {/if}
                    {if $campaign.network_id!=4}

                    <div class="users-gender">
                        <h2>Users Gender</h2>
                        <div id="users_gender" class="theme-area-chart-holder"></div>
                    </div>
                    <hr>

                    <div class="users-age">
                        <h2>Users Age</h2>
                        <div id="users_age" class="theme-area-chart-holder"></div>
                    </div>
                    <hr>
                    {if $campaign.campaign_type == 'FB-VIDEO-VIEWS' || $campaign.campaign_type == 'VIDEO_YAHOO' || $campaign.campaign_type == 'FB-VIDEO-CLICKS'}
                    <div class="theme-chart-row theme-report-subrow theme-report-areachart-row title-chart">
                        <h2>Watched Video Actions</h2>
                        <div id="video_views" class="theme-area-chart-holder"></div>
                        <div style="text-align: center;font-size: 13px;font-weight: bold;">Viewed Duration</div>
                    </div>
                    {/if}
                    {/if}
                    {if !empty($places)}
                    <div class="report_places_bloc">
                        <h2>Placements</h2>
                        <table class="table table-bordered" >
                            <thead>
                            <tr>
                                <th>Placement</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach from=$places item=place}
                            <tr>
                                <td>{$place.placement}</td>
                            </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                    {/if}
                </div>
            </div>
<!--                <div class="theme-chart-col theme-reportpage-sidebar-col theme-lg-4 theme-sm-5 theme-xs-12">-->
                <div class="col-md-5 col-sm-12 col-xs-12">
                    <div class="right-sidebar">
<!--                        <aside id="" class="theme-sidebar-widget theme-date-time-widget">-->
<!--                            <div class="campaign_details_block" >-->
                        <small>Campaign details</small>
<!--                        {if !isset($pdf)}-->
<!---->
<!--                        <a class="btn btn-success pull-right" target="_blank" href="/v2/campaign/pdf_download/{$campaign.id}">Download Pdf</a>-->
<!--                        {/if}-->
<!--                            </div>-->
                        <div class="details-block">
                        <p>{$campaign.name} (IO : {$campaign.io})</p>
                        <p><span  class="bold-p">Campaign Status:</span> {$campaign.campaign_status}</p>

                            <div class="dateTime">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-sm-6 col-xs-6 col-exs-12 text-right text-xs-center" >
                                                <h6>Date Start:</h6>
                                            </div>
                                            <div class="col-sm-6 col-xs-6 col-exs-12 text-xs-center" style="padding: 0;">
                                                <span>
                                                    {if !empty($campaign.campaign_start_datetime)}
                                                    {$campaign.campaign_start_datetime|date_format:"%Y-%m-%d %H:%M"}
                                                    {/if}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6 col-xs-6 col-exs-12 text-right text-xs-center" >
                                                <h6>Date End:</h6>
                                            </div>
                                            <div class="col-sm-6 col-xs-6 col-exs-12 text-xs-center" style="padding: 0;">
                                                <span>
                                                    {if !empty($campaign.campaign_start_datetime)}
                                                    {$campaign.campaign_end_datetime|date_format:"%Y-%m-%d %H:%M"}
                                                    {else}
                                                    Not Set
                                                    {/if}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6 col-xs-6 col-exs-12 text-right text-xs-center" >
                                                <h6>Total Clicks:</h6>
                                            </div>
                                            <div class="col-sm-6 col-xs-6 col-exs-12 text-xs-center" style="padding: 0">
                                                <span>{if $click_count}{$click_count}{else}0{/if}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-7 col-sm-6 col-xs-6 col-exs-12 text-right text-xs-center" >
                                                <h6>Remaining Days:</h6>
                                            </div>
                                            <div class="col-md-5 col-sm-6 col-xs-6 col-exs-12 text-xs-center" style="padding: 0;">
                                                <span>{$campaign.rem_days}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-7 col-sm-6 col-xs-6 col-exs-12 text-right text-xs-center" >
                                                <h6>Total Days:</h6>
                                            </div>
                                            <div class="col-md-5 col-sm-6 col-xs-6 col-exs-12 text-xs-center" >
                                                <span>{if $campaign.total_days}{$campaign.total_days}{else}0{/if}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-7 col-sm-6 col-xs-6 col-exs-12 text-right text-xs-center" >
                                                <h6>Total Views:</h6>
                                            </div>
                                            <div class="col-md-5 col-sm-6 col-xs-6 col-exs-12 text-xs-center" >
                                                <span>{if $impression_count}{$impression_count}{else}0{/if}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

<!--                            <ul class="theme-widget-list theme-datetime-list theme-display-table">-->
<!--                                <li class="theme-table-row theme-datetime-item">-->
<!--                                    <span class="theme-datetime-data theme-table-middle-cell">-->
<!--                                        <label>-->
<!--                                            <strong>Date Start :</strong>-->
<!--                                            &nbsp;{if !empty($campaign.campaign_start_datetime)}{$campaign.campaign_start_datetime|date_format:"%Y-%m-%d %H:%M"}{/if}-->
<!--                                        </label>-->
<!--                                    </span>-->
<!--                                    <span class="theme-datetime-data theme-table-middle-cell">-->
<!--                                        <label><strong>Remaining Days :</strong>&nbsp;{$campaign.rem_days}</label>-->
<!--                                    </span>-->
<!--                                </li>-->
<!--                                <li class="theme-table-row theme-datetime-item">-->
<!--                                    <span class="theme-datetime-data theme-table-middle-cell">-->
<!--                                        <label><strong>Date End :</strong>&nbsp;{if !empty($campaign.campaign_start_datetime)}{$campaign.campaign_end_datetime|date_format:"%Y-%m-%d %H:%M"}{/if}</label>-->
<!--                                    </span>-->
<!--                                    <span class="theme-datetime-data theme-table-middle-cell">-->
<!--                                        <label><strong>Total Days :</strong>&nbsp;{$campaign.total_days}</label>-->
<!--                                    </span>-->
<!--                                </li>-->
<!--                                <li class="theme-table-row theme-datetime-item">-->
<!--                                    <span class="theme-datetime-data theme-table-middle-cell">-->
<!--                                        <label><strong class="fb_page_like">Total Clicks :</strong>&nbsp; {$click_count}</label>-->
<!--                                    </span>-->
<!--                                    <span class="theme-datetime-data theme-table-middle-cell">-->
<!--                                     <label><strong>Total Views :</strong>&nbsp;{$impression_count}</label>-->
<!--                                    </span>-->
<!--                                </li>-->
<!--                            </ul>-->
<!--                        </div>-->
<!--                        </div>-->
<!--                        </aside>-->


<!--                            <aside  class="theme-sidebar-widget theme-date-time-widget theme-pos-rel">-->
                        <div id="ads_div_with_scroll" class="addBanner">
                            <div class="scroll theme-total-click-list-wrap theme-nicescroll-holder">
<!--                                <ul class="theme-total-click-list theme-banner-list">-->
                                    {if $campaign.campaign_type == 'TEXTAD'}
                                        {foreach from=$ads item=ad}
                                        {if !isset($pdf)}
                                        <a class="edit_campaign" {if !isset($pdf)} href="/v2/campaign/ad_list/{$campaign.id}/{$ad.id}" {/if} >
                                            <img alt="" src="/v2/images/report-template/table-manage-edit-icon.png" class="img-responsive">
                                        </a>
                                        {/if}
<!--                                        <li class="theme-total-click-item theme-ad-banner-item theme-pos-rel report_text_ad">-->

                                            <a href="#" class='ad_id_list' data-id='{$ad.id}'>
                                                <figure class="theme-hover-image">
                                                    {if $ad.creative_url}
                                                    {*{if $ad.creative_width > 155}
                                                    <img src="{$ad.creative_url}" alt="" class="theme-normal-image img-responsive 111" />
                                                    {else}
                                                    {$margin = (155 - $ad.creative_height) / 2}
                                                    <img src="{$ad.creative_url}" alt="" class="theme-normal-image img-responsive 222 " />
                                                    {/if}
                                                    *}


                                                    {else}
                                                    <img src='/v2/images/report-template/no-ad-logo-thumb.png' alt="" class="theme-normal-image img-responsive 333" />
                                                    {/if}
                                                    <h4>{$ad.title}</h4>
                                                    <h6>{$ad.description_1} {$ad.description_2}</h6>
                                                    <h6>{$ad.display_url}</h6>
                                                    <!--<a href="{$ad.destination_url}" target="_blank"></a>-->
                                                </figure>
                                            </a>
<!--                                        </li>-->
                                        {/foreach}
                                    {else if $campaign.campaign_type == 'PUSH_CLICK_TO_CALL'}

                                        {foreach from=$ads item=ad}
                                            {if !isset($pdf)}
                                            <a class="edit_campaign" {if !isset($pdf)} href="/v2/campaign/ad_list/{$campaign.id}/{$ad.id}" {/if} >
                                            <img alt="" src="/v2/images/report-template/table-manage-edit-icon.png img-responsive 444">
                                            </a>
                                            {/if}
                                            <li class="theme-total-click-item theme-ad-banner-item theme-pos-rel report_text_ad">

                                                <a href="#" class='ad_id_list' data-id='{$ad.id}'>
                                                    <figure class="theme-hover-image">
                                                        <h4>{$ad.airpush_image_type}</h4>
                                                        <h4>{$ad.title}</h4>
                                                        <h6>{$ad.description_1} {$ad.description_2}</h6>
                                                        <h6>{$ad.destination}</h6>
                                                    </figure>
                                                </a>
                                            </li>
                                        {/foreach}
                                    {else if $campaign.campaign_type == 'RICH_MEDIA_INTERSTITIAL'}
                                        {foreach from=$ads item=ad}
                                            {if !isset($pdf)}
                                                <a class="edit_campaign" {if !isset($pdf)} href="/v2/campaign/ad_list/{$campaign.id}/{$ad.id}" {/if} >
                                                <img alt="" src="/v2/images/report-template/table-manage-edit-icon.png">
                                            </a>
                                            {/if}
                                            <li class="theme-total-click-item theme-ad-banner-item theme-pos-rel">
                                                <textarea disabled="disabled" class="form-control script" style="width: 100% !important;" />{$ad.script}</textarea>
                                            </li>
                                        {/foreach}
                                    {else if $campaign.campaign_type == 'RICH_MEDIA_SURVEY'}
                                
                    {foreach from=$ads item=ad}
                    <div id="adContainer" style="width:300px;margin:10px auto;padding:0px;background-color:#ffffff;">
                    <div id="resized" style="width:298px;height:248px;margin:auto;position:relative;top:0px;left:0px;background-color:#ffffff;border-style:solid;border-width:1px;border-color:rgb(238,50,36);">
                     <img src="/{$ad.creative_url}" class="hover_image rich_logo" style="position:relative;top:0px;left:0px; margin: 0px;">
                    <div style="position:absolute;top:1%;right:1%;background-color:rgb(238,50,36);width:20px;height:20px;">
                    <div style="text-align:center;vertical-align:middle;font-family: Arial, Helvetica, sans-serif;">X</div>
                    
                    </div>
                    <h5 class="h5_question" style="width:280px; margin-left: 15px">{$ad.rm_question}</h5>
                            {if $ad.rm_answere1 != null}
                            <div class="input_banner" style="width:280px ; margin : 0 auto">
                            <label>
                            <input type="checkbox" name="rm_input" value="{$ad.rm_answere1}" id="inp1" style="display:inline ; margin: 5px 0 0 5px"> {$ad.rm_answere1}
                            </label>
                            </div>
                            {/if}
                            {if $ad.rm_answere2 != null}
                            <div class="input_banner" style="width:280px ; margin : 0 auto">
                            <label>
                            <input type="checkbox" name="rm_input" value="{$ad.rm_answere2}" id="inp1" style="display:inline ; margin: 5px 0 0 5px"> {$ad.rm_answere2}
                            </label>
                            </div>
                            {/if}
                            {if $ad.rm_answere3 != null}
                             <div class="input_banner" style="width:280px ; margin : 0 auto">
                            <label>
                            <input type="checkbox" name="rm_input" value="{$ad.rm_answere3}" id="inp1" style="display:inline ; margin: 5px 0 0 5px"> {$ad.rm_answere3}
                            </label>
                            </div>
                            {/if}
                            {if $ad.rm_answere4 != null}
                            <div class="input_banner" style="width:280px ; margin : 0 auto">
                            <label>
                            <input type="checkbox" name="rm_input" value="{$ad.rm_answere4}" id="inp1" style="display:inline ; margin: 5px 0 0 5px"> {$ad.rm_answere4}
                            </label>
                            </div>
                            {/if}
                            {if $ad.rm_answere5 != null}
                             <div class="input_banner" style="width:280px ; margin : 0 auto">
                            <label>
                            <input type="checkbox" name="rm_input" value="{$ad.rm_answere5}" id="inp1" style="display:inline ; margin: 5px 0 0 5px"> {$ad.rm_answere5}
                            </label>
                            </div>
                            {/if}
                            <input type="hidden" name="client_address" id="client_address" value="{get_client_ip()}">
                            <input type="hidden" name="client_network" id="client_network" value="7"> 
                            <input type="hidden" name="client_network" id="compaign_id" value="{$campaign.id}"> 

                    <input type="button" value="Send" id="send_question_form" style="position:absolute;bottom:1%;right:1%;background-color:rgb(238,50,36);width:60px;height:20px;text-align:center;font-family: Arial, Helvetica, sans-serif;">
                    
                    </div>
                    </div>
                    </div>

                                      
                                        {/foreach}
                                    {else}

                                        {foreach from=$ads item=ad}
<!--                                        <li class="theme-total-click-item theme-ad-banner-item theme-pos-rel">-->
                                            <!--<span class="theme-list-remove-icon closer"></span>-->
                                            {if !isset($pdf)}
                                            <h6>
                                                {if $campaign.campaign_type == 'FB-VIDEO-VIEWS' || $campaign.campaign_type == 'FB-VIDEO-CLICKS' || $campaign.campaign_type == 'VIDEO_YAHOO'}
                                                Ad : Video {$ad.video_duration}
                                                {else}
                                                Ad : {$ad.creative_width} X {$ad.creative_height} banner
                                                {/if}
                                                <a class="edit-ad fa fa-edit" {if !isset($pdf)} href="/v2/campaign/ad_list/{$campaign.id}/{$ad.id}" {/if} ></a>
                                            </h6>
                                            {/if}
                                            <a href="#" class='ad_id_list' data-id='{$ad.id}'>
<!--                                                <figure class="theme-hover-image">-->
                                                    {if $campaign.campaign_type == 'FB-VIDEO-VIEWS' || $campaign.campaign_type == 'FB-VIDEO-CLICKS'  || $campaign.campaign_type == 'VIDEO_YAHOO'}
                                                    <video width="320" height="260" controls class="theme-imagead-subrow-bottom"><source src="{$ad.video_url}"type="video/mp4">Your browser does not support the video tag.</video>
                                                    {else}
                                                    <img src="{$ad.creative_url}" alt="" class="theme-normal-image img-responsive 555" />
                                                    {/if}

                                                    <!--                                                <div class="theme-hidden-image-wrap">
                                                                                                        <img src="/v2/images/report-template/mobile-banner-design.jpg" alt="" class="theme-original-image" />
                                                                                                    </div>-->
<!--                                                </figure>-->
                                            </a>
<!--                                        </li>-->
                                        {/foreach}
                                    {/if}
<!--                                </ul>-->
                            </div>
                        </div>
<!--sidebar ends-->

                    </div>
                </div>
            </div>
        </div>
</div>
        {if $campaign.campaign_type != 'FB-VIDEO-VIEWS' && $campaign.campaign_type != 'FB-PAGE-LIKE'}
        <div class="theme-report-charts-wrap theme-report-charts-row">
                <div class="campaign-map">
                    <div class="col-md-8">
                        <div id="map-canvas" class="span12" style="height:500px;"></div>
                            <!--<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3104.8188822484144!2d-77.06616249999999!3d38.9052569!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89b7b648df5e6ba1%3A0x1d0fae5121cdb7af!2sGeorgetown+Cupcake!5e0!3m2!1sen!2snp!4v1443110463349" width="100%" height="" frameborder="0" style="border:0" allowfullscreen></iframe>-->
                    </div>
                        <div class="col-md-4 theme-chart-col theme-reportpage-sidebar-col theme-lg-4 theme-sm-5 theme-xs-12">
                        <div class="theme-reportpage-sidebar-content theme-borderless-sidebar">
                            {if $campaign.geotype == 'postalcode'}
                                <aside id="" class="theme-sidebar-widget theme-map-data-widget theme-geomap-top-widget">
                                    <h3>Target Digital Rooftop</h3>
                                    <h2>{$campaign.radius} Miles</h2>
                                </aside>
                            {/if}
                            <aside id="theme-geo-state-widget" class="theme-sidebar-widget theme-map-data-widget theme-pos-rel">
                                <div class="theme-total-click-list-wrap theme-nicescroll-holder">
                                    <ul class="theme-location-geo-list">
                                        <li class="theme-geo-header-item theme-display-table">
                                            <span class="theme-geo-header border-width">GEO LOCATION</span>
                                            <span class="theme-geo-header border-right  b-width5">Geo Type</span>
                                            <span class="theme-geo-header border-width3 fb_page_like_geo_loc">Clicks</span>
                                        </li>
                                        {foreach from = $geotype_array item=v}
                                            <li class="theme-display-table theme-geo-list-item theme-pos-rel">

                                                {if $campaign.geotype == 'postalcode'}
                                                    <span class="theme-geo-data b_width">{$v.postal_code}</span>
                                                    <span class="theme-geo-data b_right border-width2" style="font-size: 10px;">{$campaign.geotype}</span>
                                                    <span class="theme-geo-data click_count_js" data-postal_code="{$v.postal_code}">{$v.clicks_count}</span>
                                                {else}
                                                    <span class="theme-geo-data b_width">{$v}</span>
                                                    <span class="theme-geo-data b_right border-width2">{$campaign.geotype}</span>
                                                    <span class="theme-geo-data click_count_js" data-postal_code="{$v}"  {if $campaign.geotype == 'state'} data-state = "{$v}" {/if}>
                                                    {if $campaign.geotype == 'country'}
                                                        {$click_count}
                                                    {/if}
                                                    </span>
                                                {/if}
                                            </li>
                                        {/foreach}
                                    </ul>
                                </div>
                            </aside>
    <!--                        <aside id="" class="theme-sidebar-widget theme-geo-state-footer-info">
                                <p class="theme-geo-state-note">A Location can we nationwide, a state, particular zip code(s),or a physical address. You may add up to 2000 locations per campaign</p>
                            </aside>-->
                        </div>
                    </div>
                </div>
        </div>
        {/if}
    </div>
</div>
{include file="v2/sections/scripts.php"}
<!-- Include Required Prerequisites -->
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<!-- Include Date Range Picker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />

{if $campaign.geotype == 'state' && $campaign.country == "US"}
<!--    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAzr2EBOXUKnm_jVnk0OJI7xSosDVG8KKPE1-m51RBrvYughuyMxQ-i1QfUnH94QxWIa6N4U6MouMmBA"-->
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=AIzaSyApBHZJa7hhU-wub80ZGY0mcIJ1LdYGYFo"
            type="text/javascript"></script>
    <script src="/v2/js/epoly2.js"></script>
    {else}
<!--     <script src="https://maps.google.com/maps/api/js?key=ABQIAAAAzr2EBOXUKnm_jVnk0OJI7xSosDVG8KKPE1-m51RBrvYughuyMxQ-i1QfUnH94QxWIa6N4U6MouMmBA"></script>-->
<script src="https://maps.google.com/maps/api/js?key=AIzaSyApBHZJa7hhU-wub80ZGY0mcIJ1LdYGYFo"></script>

{/if}

<script src="/v2/js/amcharts/amcharts.js"></script>
<script src="/v2/js/amcharts/serial.js"></script>
<script src="/v2/js/amcharts/themes/light.js"></script>
<script src="/v2/js/amcharts/pie.js"></script>
{$campaign.demographics = NULL}
{$campaign.majors = NULL}
{$campaign.jobs = NULL}
{$campaign.educations = NULL}
{$campaign.works = NULL}
{$campaign.keywords = NULL}
<script>

    var js_data = {
        campaign_id: {$campaign.id},
        start_date: '{$start_data}',
        date_now: '{$date_now}',
        //budget_left: {$campaign.cost * 100/$campaign.max_budget},
       // budget: {($campaign.max_budget - $campaign.cost) * 100/$campaign.max_budget},
        js_date_now: '{$js_date_now}',
        js_start_data: '{$js_start_data}'
    };
    campaign = JSON.parse('{$campaign|@json_encode|replace:"'":"\'"|replace:'"':'\"'}');
    ads = JSON.parse('{$ads|@json_encode|replace:"'":"\'"|replace:'"':'\"'}');
    //clicks_state = JSON.parse('{$clicks_state|@json_encode}'),
    //geo_data = JSON.parse('{$geo_data|@json_encode}');

</script>

<script src="/v2/js/reporting.js"></script>
<script src="/v2/js/amcharts/plugins/responsive/responsive.min.js"></script>
{include file="v2/sections/footer.php"}
</body>
</html>
