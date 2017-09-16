{include file="v2/sections/header.php"}
<div class="theme-report-row-wrap">
    <div class="theme-container">
<!--        <div class="theme-report-charts-wrap theme-report-charts-row">-->
            <div class="theme-report-charts-row">
            <div class="row theme-row clearfix">
            <div class="col-md-7 col-sm-12 col-xs-12">
<!--                <div class="chart-block col-md-7 col-sm-12 col-xs-12 theme-chart-col  theme-piechart-col theme-lg-8 theme-sm-7 theme-xs-12"></div>-->
                <div class="chart-block">
                    {if $user_type != 'viewer'}

                    <div class="campaign-progress">
                            <h4>Campaign Progress</h4>
                            <div id="theme-piechart-holder" class="cm_progress col-md-4 col-sm-4 col-xs-12" style="padding: 0"></div>
                            <div class="col-md-3 col-sm-4 col-xs-12">
                                <h5 class="theme-budget-label">Campaign Progress</h5>
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




                                    {if $user.is_billing_type == 'PERCENTAGE'}

                                    {if empty($campaign.max_budget)}
                                        {if !empty($campaign.max_clicks)}
                                            {$percent = 100*$campaign.total_clicks_count/$campaign.max_clicks}
                                            {if $percent >= 100}
                                                {$percent = 100}
                                            {/if}
                                            {$percent|string_format:"%.2f"}%
                                        {else if !empty($campaign.max_impressions)}
                                            {$percent = 100*$campaign.total_impressions_count/$campaign.max_impressions}
                                            {if $percent >= 100}
                                                {$percent = 100}
                                            {/if}
                                            {$percent|string_format:"%.2f"}%
                                        {else if !empty($campaign.date_diff) && $campaign.persent_diff < $campaign.date_diff}
                                            {$percent = 100*$campaign.persent_diff/$campaign.date_diff}
                                            {if $percent >= 100}
                                                {$percent = 100}
                                            {/if}
                                            {$percent|string_format:"%.2f"}%
                                        {/if}
                                    {else}
                                        {$cost = $campaign.percentage_max_budget - $campaign.cost}

                                        {if ($cost>0)}
                                            {$percent_cost = $campaign.cost*100/$campaign.percentage_max_budget}
                                            {$percent_cost|string_format:"%.2f"}%
                                        {else}
                                            100%
                                        {/if}
                                    {/if}
                                {else}

                                    {if !empty($campaign.max_budget)}
                                        {$cost = $campaign.percentage_max_budget - $campaign.cost}

                                        {if ($cost>0) && $campaign.campaign_status!='COMPLETED'}
                                            {$percent_cost = $campaign.cost*100/$campaign.percentage_max_budget}
                                            {$percent_cost|string_format:"%.2f"}%
                                        {else}
                                            100%
                                        {/if}
                                    {else if !empty($campaign.max_clicks)}
                                        {$percent = 100*$campaign.total_clicks_count/$campaign.max_clicks}
                                        {if $percent >= 100}
                                            {$percent = 100}
                                        {/if}
                                        {$percent|string_format:"%.2f"}%
                                    {else if !empty($campaign.max_impressions)}
                                        {$percent = 100*$campaign.total_impressions_count/$campaign.max_impressions}
                                        {if $percent >= 100}
                                            {$percent = 100}
                                        {/if}
                                        {$percent|string_format:"%.2f"}%
                                    {else if !empty($campaign.date_diff) && $campaign.persent_diff < $campaign.date_diff}
                                        {$percent = 100*$campaign.persent_diff/$campaign.date_diff}
                                        {if $percent >= 100}
                                            {$percent = 100}
                                        {/if}
                                            {$percent|string_format:"%.2f"}%
                                    {/if}
                                {/if}
                                </h1>
                                <h5 class="theme-budget-label">Progress Left</h5>
                                <h1 id="total_budget_spent" class="theme-budget-value">
                                    {100-$percent|string_format:"%.2f"}%
                                </h1>
                            </div>
                            <div class="col-md-5 col-sm-4 col-xs-12">
                                <p>Your budget indicates the total and remaining
                                    for the campaign.
                                </p>
                                {if !isset($pdf)}
<!--                                <a href="#" class="theme-btn theme-report-add-btn" id='view_campaign'>View Campaign Info</a>-->
                                <button class="btn btn-success" id='view_campaign' type="button">
                                    View Campaign info
                                </button>
                                {/if}
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
                    {if $campaign.campaign_type == 'FB-VIDEO-VIEWS'}
                    <div class="theme-chart-row theme-report-subrow theme-report-areachart-row title-chart">
                        <h3>Watched Video Actions</h3>
                        <div id="video_views" class="theme-area-chart-holder"></div>
                        <div style="text-align: center;font-size: 13px;font-weight: bold;">Viewed Duration</div>
                    </div>
                    {/if}
                    {/if}
                    {if !empty($places)}
                    <div class="report_places_bloc">
                        <h3>Placements</h3>
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
                        {if !isset($pdf)}
<!--                                <a target="_blank" href="/v2/campaign/pdf_download/{$campaign.id}" class="btn btn-success btn_pdf_download">Download Pdf</a>-->
                        <a class="btn btn-success pull-right" target="_blank" href="/v2/campaign/pdf_download/{$campaign.id}">Download Pdf</a>
                        {/if}
<!--                            </div>-->
                        <div class="details-block">
                        <p>{$campaign.name} (IO : {$campaign.io})</p>
                        <p>Campaign Status: {$campaign.campaign_status}</p>

                            <div class="dateTime">
                                <div class="col-md-6">
                                    <h6>Date Start: <span>{if !empty($campaign.campaign_start_datetime)}{$campaign.campaign_start_datetime|date_format:"%Y-%m-%d %H:%M"}{/if}</span></h6>
                                    <h6>Date End: <span>{if !empty($campaign.campaign_start_datetime)}{$campaign.campaign_end_datetime|date_format:"%Y-%m-%d %H:%M"}{/if}</span></h6>
                                    <h6>Total Clicks: <span>{$click_count}</span></h6>
                                </div>
                                <div class="col-md-6">
                                    <h6>Remaining Days : <span>{$campaign.rem_days}</span></h6>
                                    <h6>Total Days: <span>{$campaign.total_days}</span></h6>
                                    <h6>Total Views: <span>{$impression_count}</span></h6>
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
                                    {else}

                                        {foreach from=$ads item=ad}
<!--                                        <li class="theme-total-click-item theme-ad-banner-item theme-pos-rel">-->
                                            <!--<span class="theme-list-remove-icon closer"></span>-->
                                            {if !isset($pdf)}
                                            <h6>
                                                {if $campaign.campaign_type == 'FB-VIDEO-VIEWS' || $campaign.campaign_type == 'FB-VIDEO-CLICKS'}
                                                Ad : Video {$ad.video_duration}
                                                {else}
                                                Ad : {$ad.creative_width} X {$ad.creative_height} banner
                                                {/if}
                                                <a class="edit-ad fa fa-edit" {if !isset($pdf)} href="/v2/campaign/ad_list/{$campaign.id}/{$ad.id}" {/if} ></a>
                                            </h6>
                                            {/if}
                                            <a href="#" class='ad_id_list' data-id='{$ad.id}'>
<!--                                                <figure class="theme-hover-image">-->
                                                    {if $campaign.campaign_type == 'FB-VIDEO-VIEWS' || $campaign.campaign_type == 'FB-VIDEO-CLICKS'}
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
<!--        <div class="theme-report-charts-wrap theme-report-charts-row">-->
            <div class="theme-report-charts-row">
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
                                                <span class="theme-geo-data b_width">{$v}</span>
                                                <span class="theme-geo-data b_right border-width2">{$campaign.geotype}</span>
                                                <span class="theme-geo-data click_count_js" data-postal_code="{$v}"  {if $campaign.geotype == 'state'} data-state = "{$v}" {/if}  ></span>
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
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAzr2EBOXUKnm_jVnk0OJI7xSosDVG8KKPE1-m51RBrvYughuyMxQ-i1QfUnH94QxWIa6N4U6MouMmBA"
            type="text/javascript"></script>
    <script src="/v2/js/epoly2.js"></script>
    {else}
     <script src="https://maps.google.com/maps/api/js"></script>
{/if}

<script src="/v2/js/amcharts/amcharts.js"></script>
<script src="/v2/js/amcharts/serial.js"></script>
<script src="/v2/js/amcharts/themes/light.js"></script>
<script src="/v2/js/amcharts/pie.js"></script>

<script>
    
    var js_data = {
        campaign_id: {$campaign.id},
        start_date: '{$start_data}',
        date_now: '{$date_now}',
        //budget_left: {$campaign.cost * 100/$campaign.max_budget},
       // budget: {($campaign.max_budget - $campaign.cost) * 100/$campaign.max_budget},
        js_date_now: '{$js_date_now}',
        js_start_data: '{$js_start_data}'
    },

    campaign = JSON.parse('{$campaign|@json_encode|replace:"'":"\'"}'),
    ads = JSON.parse('{$ads|@json_encode|replace:"'":"\'"}');

    //clicks_state = JSON.parse('{$clicks_state|@json_encode}'),
    //geo_data = JSON.parse('{$geo_data|@json_encode}');

</script>

<script src="/v2/js/reporting.js"></script>
<script src="/v2/js/amcharts/plugins/responsive/responsive.min.js"></script>
</body>
</html>
