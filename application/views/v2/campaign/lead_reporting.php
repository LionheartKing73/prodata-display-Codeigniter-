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
                            <h4>Campaign Progress</h4>
                            <div id="theme-piechart-holder" class="cm_progress pull-left" style=""></div>
                            <div class="pull-left progress_left_block">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
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
                    <div class="report_places_bloc">
                        <h2>Leads</h2>
                        <table class="table table-bordered" >
                            <thead>
                            <tr>
                                <th>Lead</th>
                                <th>View</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach from=$leads key=key item=lead}
                            <tr>
                                <td>Lead-{$key+1}</td>
                                <td>
                                    <a href="#" data-lead = '{$lead|@json_encode}' class="btn_open_modal" data-lead_id = '{$lead.id}' >
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                    <div class="modal fade" id="lead_modal" role="dialog">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">View Lead Info</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="newAdd theme-create-ad-form-wrap">
                                        <p class="lead_email"> <b> Email : </b> <span></span></p>
                                        <p class="lead_full_name"> <b> Full name : </b> <span></span></p>
                                        <p class="lead_phone_number"> <b> Phone Number : </b> <span></span></p>
                                        <p class="lead_created_date"> <b> Created Date : </b> <span></span></p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="report_places_bloc">
                        <iframe id="iframe_for_download" style="display:none;"></iframe>
                        <div class="form-group">
                            <label class="range_picke_head">Select Date</label>
                            <input type="text" id="lead_date_range" class="form-control">
                        </div>
                        <h2>Export Leads</h2>
                        <button id="download_leads" class="btn btn-success add_margin">Download Leads as CSV</button>
                    </div>
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
                        <div id="ads_div_with_scroll" class="addBanner">
                            <div class="scroll theme-total-click-list-wrap theme-nicescroll-holder">

                                    {if $campaign.campaign_type == 'TEXTAD'}
                                        {foreach from=$ads item=ad}
                                        {if !isset($pdf)}
                                        <a class="edit_campaign" {if !isset($pdf)} href="/v2/campaign/ad_list/{$campaign.id}/{$ad.id}" {/if} >
                                            <img alt="" src="/v2/images/report-template/table-manage-edit-icon.png" class="img-responsive">
                                        </a>
                                        {/if}


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
                                                </figure>
                                            </a>

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

                                                    {if $campaign.campaign_type == 'FB-VIDEO-VIEWS' || $campaign.campaign_type == 'FB-VIDEO-CLICKS'  || $campaign.campaign_type == 'VIDEO_YAHOO'}
                                                    <video width="320" height="260" controls class="theme-imagead-subrow-bottom"><source src="{$ad.video_url}"type="video/mp4">Your browser does not support the video tag.</video>
                                                    {else}
                                                    <img src="{$ad.creative_url}" alt="" class="theme-normal-image img-responsive 555" />
                                                    {/if}


                                            </a>
                                        {/foreach}
                                    {/if}

                            </div>
                        </div>
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

<script src="/v2/js/lead_reporting.js"></script>
<script src="/v2/js/amcharts/plugins/responsive/responsive.min.js"></script>
{include file="v2/sections/footer.php"}
</body>
</html>
