{include file="v2/sections/header.php"}

<script>
    var iab_categories_preselected = {$campaign_associated_iab_categories|json_encode};
</script>
<link href="/v2/css/datetime-picker.css" rel="stylesheet" type="text/css"/>
<link href="/v2/js/chosen/chosen.min.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="/v2/css/jquery.steps.css">
    <div class="theme-report-campaigne-row-wrap">
        <div class="theme-container">

            <div class="theme-report-campaigne-schedule-row container-fluid">

                <div class="theme-report-campaigne-row-title news-feed">

                    <h3 class="campaign_name " id="name" data-onblur="ignore" data-toggle="manual" data-type="text" data-pk="{$campaign.id}" data-url="/v2/campaign/edit_campaign_name" data-title="Enter campaign name">{$campaign.name}
                        <a href="#" class="edit">
                            <i class="fa fa-edit"></i>
                        </a>
                    </h3>
                     
                    {if !$editable}<div class="theme-align-center"><h1 style="color:red;">You can't edit PAUSED campaign</h1></div>{/if}
                    {if $campaign.campaign_status=="DISAPPROVED"}<div class="theme-align-center"><h1 style="color:red;">Your campaign DISAPPROVED with this reason {$campaign.disapproval_reasons}</h1></div>{/if}
                </div>
              
                <div class="theme-form-group-wrap content">

                    <div class="theme-gelocation-from-row">
                        <div class="theme-display-table theme-no-gutter">
                            <div class="col-md-6 col-sm-12 col-xs-12">

                                <div class="iab_categories">
                                    <form id="theme-iab-category-form" action="">
                                        <div class="theme-geoform-group theme-tabbed-form-group them-form-group">
                                            <h4>Edit Campaign Vertical</h4>
                                            <div class="theme-geoform-group theme-form-group theme-inline-group clearfix">
                                                <div id="iab_category_select" class="categories_block theme-form-control pull-right theme-inline-jstree-selection">
                                                    <ul>
                                                        {foreach from=$iab_categories item=category}
                                                        <li id="{$category.category_id}" data-catid="{$category.category_id}"
                                                            data-parentcatid="{$category.parent_category_id}">{$category.name}
                                                            {if !empty($category.children)}
                                                            <ul>
                                                                {foreach from=$category.children item=sub_cat}
                                                                <li id="{$sub_cat.category_id}" data-catid="{$sub_cat.category_id}"
                                                                    data-parentcatid="{$sub_cat.parent_category_id}">{$sub_cat.name}</li>
                                                                {/foreach}
                                                            </ul>
                                                            {/if}
                                                        </li>
                                                        {/foreach}
                                                    </ul>
                                                </div>
                                                <textarea name="vertical" class="vertical-cache-textarea"></textarea>
                                            </div>
                                        </div>
                                        <input type="hidden" name="campaign_id" value="{$campaign.id}">

                                        <div class="geoloc-button">
                                            <button type="reset" class="btn btn-default">Cancel</button>
                                            <button type="submit" class="btn btn-info">Save</button>
                                        </div>
                                    </form>
                                </div>

                                {*if !empty($smarty.get.taskid) && $smarty.get.taskid == 'pp245'*}
                                {if !empty($campaign.campaign_type) && $campaign.campaign_type == 'DISPLAY'}
                                <div class="ip_retargeting">
                                    <form id="theme-ip-retargeting-form" action="">
                                        <div class="theme-geoform-group theme-tabbed-form-group them-form-group">
                                            <h4>IP Retargeting</h4>
                                            <span style="display: block; font-size: 14px; color: #2c3e50;">
                                                Upload Text (.txt) File only, Max. 2mb size and one IP/CIDR per line
                                            </span>
                                            <div class="theme-geoform-group theme-form-group theme-inline-group clearfix">
                                                <!-- <div id="ip_targeting" class="theme-geoform-group theme-tabbed-form-group them-form-group">
                                                    <h2>IP Targeting</h2>

                                                </div> -->
                                                <input type="file" accept="text/plain"
                                                        id="retargeting_ips_file" style="visibility: hidden; border: none;">
                                                <textarea name="ip_targeting_ips_json" id="ip_targeting_ips_json" class="vertical-cache-textarea"></textarea>
                                                <div>
                                                    <button class="theme-create-add-btn theme-submit-control"
                                                        type="button" id="ip_targeting_upload_btn">Upload File&nbsp;</button>
                                                    <img class="file_uploading" src="/static/img/ajax-loader.gif"
                                                        alt="loading..." width="20" height="20" style="display: none; margin-left: 10px;">
                                                </div>
                                                <p class="ip_targeting_file_name">No File Chosen.</p>
                                                <div class="alert alert-danger invalid-ips-display" style="display: none; margin-top: 10px;">
                                                    <h4>Invalid IP(s)<hr></h4>
                                                    <div class="ips"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="campaign_id" value="{$campaign.id}">

                                        <div class="geoloc-button">
                                            <button type="reset" class="btn btn-default">Cancel</button>
                                            <button type="submit" class="btn btn-info">Save</button>
                                        </div>
                                    </form>
                                </div>
                                {*/if*}
                                {/if}

                                <div class=" geoloc">
                                    <form id="theme-geo-form" action="">
                                        <div class="theme-geoform-group theme-tabbed-form-group them-form-group">
                                            <h4>Select Geo-Location Type</h4>
                                            <div class="location_type">
                                                <input type="hidden" value="{$campaign.geotype}" id="camp_geotype">
                                                <input name="geotype" type="radio" value="country" class="theme-geofrom-control theme-tabbed-form-control geo-country-radio" id="country" checked />
                                                <label for="country" class="theme-geoform-label theme-tabbed-form-label">Country (Nationwide)</label>
                                                <input name="geotype" type="radio" value="state" class="theme-geofrom-control theme-tabbed-form-control geo-state-radio" id="state" />
                                                <label for="state" class="theme-geoform-label theme-tabbed-form-label">State</label>
                                                <input name="geotype" type="radio" value="postalcode" class="theme-geofrom-control theme-tabbed-form-control geo-postal-radio" id="postal-code" />
                                                <label for="postal-code" class="theme-geoform-label theme-tabbed-form-label">Postal Code</label>
                                            </div>
                                            <hr>
                                        </div>
                                        <div id="geo-country" class=" theme-geoform-group theme-form-group">
                                            <div class=" countryLoc" id="theme-geofrom-country-selectbox">
                                                <label for="">Country (Nationwide)</label>
                                                <input type="hidden" value="{$campaign.country}" id="camp_country">
                                                <select id="geo_country_select" name="country" class="form-control country">
                                                    <option value="">Select Country</option>
                                                    <option value="CA">Canada</option>
                                                    <option value="MX">Mexico</option>
                                                    <option value="GB">United Kingdom</option>
                                                    <option value="US">United States</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="geo-state" class=" theme-geoform-group theme-form-group">
                                            <div class="state">
                                                <label for="">State</label>
                                                <input type="hidden" value="{$campaign.state}" id="camp_state">
                                                <select id="geo-state" name="state[]" class="theme-form-control theme-multi-selectbox theme-control" multiple>
                                                    {if !empty($states)}
                                                    {foreach from=$states item=state}
                                                        {if $state.state|in_array:$campaign.state_array}
                                                            <option value= "{$state.state}" selected>{ucfirst($state.name)}</option>
                                                        {else}
                                                            <option value= "{$state.state}" >{ucfirst($state.name)}</option>
                                                        {/if}
                                                    {/foreach}
                                                    {/if}
                                                </select>
                                            </div>
                                        </div>
                                        <div id="geo-postal" class="theme-geoform-group theme-form-group geo-postal">
                                            <div class="postal-code">
                                                <label for="">Postal Code <span style="font-size: 12px; color: #909090;">( Enter one or more postal codes, space separated )</span></label>
                                                <div class="col-md-6  zip_code">
                                                    <input type="hidden" value="{$campaign.zip}" id="camp_zip">
                                                    <input name="zip" type="text" value="" placeholder="Enter your postal code" class="form-control" />
                                                </div>id="select-checker"
                                                <div class="col-md-6">
                                                    <input type="hidden" value="{$campaign.radius}" id="camp_radius">
                                                    <select id="geo-postal-radius" name="radius" class="form-control radius">
                                                        <option value="">Select Radius</option>
                                                        <option value="10">10 Miles</option>
                                                        <option value="15">15 Miles</option>
                                                        <option value="25">25 Miles</option>
                                                        <option value="50">50 Miles</option>
                                                        {if $campaign.network_id != 5}
                                                        <option value="75">75 Miles</option>
                                                        <option value="100">100 Miles</option>
                                                        <option value="125">125 Miles</option>
                                                        {/if}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="campaign_id" value="{$campaign.id}">
                                        {if $editable}
<!--                                        <div class="theme-geoform-group theme-form-group theme-submit-group theme-align-center">-->
<!--                                            <input type="reset" value="Cancel" class="theme-cancel-btn theme-submit-control" />-->
<!--                                            <input type="submit" id="save_location_button" value="Save Location" class="theme-cancel-btn theme-submit-control" />-->
<!--                                        </div>-->
                                        <div class="geoloc-button">
                                            <button type="reset" class="btn btn-default">Cancel</button>
                                            <button type="submit" class="btn btn-info">Save Location</button>
                                        </div>
                                        {/if}
                                    </form>
                                </div>
                                {if $campaign.campaign_type!="EMAIL"}
                                <div class="edit-date">
                                    <form class=" form-horizontal" id="theme-end-form">
                                        <h4>Edit campaign end date</h4>
                                        <div class="endDate">
                                            <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                <label class="theme-inline-label">End date</label>
                                                <input type="text" id="edit_end_date_datepicker" name="campaign_end_datetime" value="{if !empty($campaign.campaign_end_datetime)}{$campaign.campaign_end_datetime|date_format:'%Y/%m/%d %H:%M'}{/if}" placeholder="2014/20/18" class="fillone form-control theme-date-picker">
                                            </div>
                                        </div>
                                        <input type="hidden" name="campaign_id" value="{$campaign.id}">
                                        {if $editable}
<!--                                        <div class="theme-geoform-group theme-form-group theme-submit-group theme-align-center">-->
<!--                                            <input type="reset" value="Cancel" class="theme-cancel-btn theme-submit-control" />-->
<!--                                            <input type="submit" value="Save" class="theme-cancel-btn theme-submit-control" />-->
<!--                                        </div>-->
                                        <div class="geoloc-button">
                                            <button type="reset" class="btn btn-default">Cancel</button>
                                            <button type="submit" class="btn btn-info">Save</button>
                                        </div>
                                        {/if}
                                    </form>
                                </div>
                                <div class="start-date">
                                    <form id="theme-start-form">
                                        <h4>Edit campaign Start date</h4>
                                        <div class="startDate">
                                            <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                <label class="theme-inline-label">Start date</label>
                                                <input type="text" {if $campaign.campaign_status == 'ACTIVE'} disabled {/if} id="edit_start_date_datepicker" name="campaign_start_datetime"
                                                       value="{if !empty($campaign.campaign_start_datetime)}{$campaign.campaign_start_datetime|date_format:'%Y/%m/%d %H:%M'}{/if}" placeholder="2014/20/18" class="fillone form-control theme-date-picker">
                                            </div>
                                        </div>
                                        <input type="hidden" name="campaign_id" value="{$campaign.id}">
                                        {if $editable}
<!--                                        <div class="theme-geoform-group theme-form-group theme-submit-group theme-align-center">-->
<!--                                            <input type="reset" value="Cancel" class="theme-cancel-btn theme-submit-control" />-->
<!--                                            <input type="submit" value="Save" class="theme-cancel-btn theme-submit-control" />-->
<!--                                        </div>-->
                                        <div class="geoloc-button">
                                            <button type="reset" class="btn btn-default">Cancel</button>
                                            <button type="submit" class="btn btn-info">Save</button>
                                        </div>
                                        {/if}
                                    </form>

                                </div>
                                {/if}

                                <!-- IO BASED RETARGETING -->
                                {if !empty($campaign.campaign_type) && ($campaign.campaign_type == 'DISPLAY-RETARGET' || $campaign.campaign_type == 'DISPLAY')}
                                <input type="hidden" id="user_id_to_pick_io" value="{$campaign.user_id_to_pick_io}" readonly>
                                <div class="io-based-retargeting">
                                    <form id="theme-io-based-retargeting-form">
                                        <h4>Campaign IO Based Retargeting Setting</h4>
                                        <div class="retargetingIO" style="overflow: visible !important;">
                                            <select id="io_based_retargeting_ios" name="io_based_retargeting_ios[]" multiple>
                                                <option value="" disabled="disabled" selected></option>
                                                {foreach from=$io_list item=io}
                                                {$value = $io["io"]}
                                                {$option = $io["io"]}
                                                {if $io.network_id == 1}
                                                <option value= "{$value}" class="network_{$io.network_id}">  {ucfirst($option)}</option>
                                                {/if}
                                                {/foreach}
                                            </select>
                                        </div>
                                        <input type="hidden" name="campaign_id" value="{$campaign.id}">
                                        {if $editable}
                                        <div class="geoloc-button">
                                            <button type="reset" class="btn btn-default">Cancel</button>
                                            <button type="submit" class="btn btn-info">Save</button>
                                        </div>
                                        {/if}
                                    </form>
                                </div><!-- //IO BASED RETARGETING END -->
                                {/if}

                            </div>

                            <div class="col-md-6 col-sm-12 col-xs-12" {if !empty($user.is_billing) && $user.is_billing == 'N'}style="margin-top:-40px;"{/if}>
                                {if $campaign.campaign_type=="EMAIL"}
                                <div class="theme-geolocation-form-wrap">
                                    <form id="theme-link-form">
                                        <div class="theme-geoform-group theme-tabbed-form-group them-form-group">
                                            <h2>Edit Link for IO: {$campaign.io}</h2>
                                        </div>

                                        <div class="theme-bordered-legend">
                                            <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                <label class="theme-inline-label">Destination Url</label>
                                                <input name="destination_url" type="text" value="{$link.destination_url}" placeholder="http://www.xyx.com/?lfsf=xYcSCFSDF" class="theme-geoform-control theme-form-control" />
                                            </div>
                                            <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                <label class="theme-inline-label">Maximum Clicks</label>
                                                <input name="max_clicks" type="text" maxlength="9" value="{$link.max_clicks}" placeholder="61" class="theme-geoform-control theme-form-control" />
                                            </div>
                                            <div class="theme-geoform-group theme-form-group theme-align-center">
                                                <label class="theme-inline-label">Fulfilled : <span>{if $link.is_fulfilled=='Y'}YES{else}NO{/if}</span> </label>
                                            </div>
                                            <input type="hidden" name="link_id" value="{$link.id}">
                                            {if $editable}
                                            <div class="theme-geoform-group  theme-form-group theme-submit-group theme-align-center">
                                                <input type="reset" value="Cancel" class="theme-cancel-btn theme-submit-control" />
                                                <input type="submit" value="Save" class="theme-cancel-btn theme-submit-control" />
                                            </div>

                                            {/if}
                                        </div>

                                    </form>
                                </div>
                                {/if}
                                {if !empty($user.is_billing) && $user.is_billing == 'Y'}
                                <div class="addBudget">
                                    <form id="theme-budget-form" class="theme-geoform-group">
                                        <div  class="form-group">
                                            <h4>Add Additional Budget?</h4>
                                            <p>By adding additional budget to {$campaign.name} ({$campaign.io}), you will receive additional views and clicks.</p>
                                            {$all_cost = $campaign.cost + $campclick}

                                            {if $user.is_billing_type == 'PERCENTAGE'}
                                                {if !empty($campaign.max_budget) && $campaign.max_budget > 0}
                                                    {$cost = $campaign.percentage_max_budget - $all_cost}

                                                    {if ($cost>0)}
                                                        {$percent_cost = $cost*100/$campaign.percentage_max_budget}
                                                        <p>Total Budget Allocation: $ {$campaign.max_budget|string_format:"%.2f"}</p>
                                                        <p>Total Budget Left: $ {($campaign.max_budget*$percent_cost/100)|string_format:"%.2f"}</p>
                                                    {else}
                                                        <p>Total Budget Allocation: $ {$campaign.max_budget|string_format:"%.2f"}</p>
                                                        <p>Total Budget Spent: $ {$campaign.max_budget|string_format:"%.2f"}</p>
                                                    {/if}
                                                {else}
                                                    {if $campaign.is_thru_guarantee == 'Y'}
                                                            <p>Total Budget Allocation: $ {$campaign.max_budget|string_format:"%.2f"}</p>
                                                            <p>Total Budget Spent: $ {$cost_left|string_format:"%.2f"}</p>
                                                    {/if}
                                                    {if !empty($campaign.max_clicks) && $campaign.max_clicks > 0}

                                                        {$percent = $campaign.total_clicks_count/$campaign.max_clicks*100}
                                                        {$cost = $campaign.max_clicks*$user.display_click}

                                                        {if $percent<100}
                                                            <p>Total Budget Allocation: $ {$cost|string_format:"%.2f"}</p>
                                                            <p>Total Budget Left: $ {($cost*$percent/100)|string_format:"%.2f"}</p>
                                                        {else}
                                                            <p>Total Budget Allocation: $ {$cost|string_format:"%.2f"}</p>
                                                            <p>Total Budget Spent: $ {$cost|string_format:"%.2f"}</p>
                                                        {/if}
                                                    {/if}
                                                    {if !empty($campaign.max_impressions) && $campaign.max_impressions > 0}
                                                        {$percent = $campaign.total_impressions_count/$campaign.max_impressions*100}
                                                        {$cost = $campaign.max_impressions*$user.display_imp/1000}
                                                        {if $percent<100}
                                                            <p>Total Budget Allocation: $ {$cost|string_format:"%.2f"}</p>
                                                            <p>Total Budget Left: $ {($cost*$percent/100)|string_format:"%.2f"}</p>
                                                        {else}
                                                            <p>Total Budget Allocation: $ {$cost|string_format:"%.2f"}</p>
                                                            <p>Total Budget Spent: $ {$cost|string_format:"%.2f"}</p>
                                                        {/if}
                                                    {/if}

                                                {/if}
                                            {else}
                                                {if !empty($campaign.max_budget)}
                                                    {$cost = $campaign.percentage_max_budget - $all_cost}
                                                    {if ($cost>0)}
                                                        {$percent_cost = $cost*100/$campaign.percentage_max_budget}
                                                        <p>Total Budget Allocation: $ {$campaign.max_budget|string_format:"%.2f"}</p>
                                                        <p>Total Budget Left: $ {($campaign.max_budget*$percent_cost/100)|string_format:"%.2f"}</p>
                                                    {else}
                                                        <p>Total Budget Allocation: $ {$campaign.max_budget|string_format:"%.2f"}</p>
                                                        <p>Total Budget Spent: $ {$campaign.max_budget|string_format:"%.2f"}</p>
                                                    {/if}
                                                {else if !empty($campaign.max_clicks)}
                                                    {$tier = 'display_click_'|cat:$campaign.campaign_tier}
                                                    {$percent = $campaign.total_clicks_count/$campaign.max_clicks*100}
                                                    {$cost = $campaign.max_clicks*$user[$tier]}
                                                    {if $percent<100}
                                                        <p>Total Budget Allocation: $ {$cost|string_format:"%.2f"}</p>
                                                        <p>Total Budget Left: $ {($cost*$percent/100)|string_format:"%.2f"}</p>
                                                    {else}
                                                        <p>Total Budget Allocation: $ {$cost|string_format:"%.2f"}</p>
                                                        <p>Total Budget Spent: $ {$cost|string_format:"%.2f"}</p>
                                                    {/if}
                                                {else if !empty($campaign.max_impressions)}

                                                    {$tier = 'display_imp_'|cat:$campaign.campaign_tier}
                                                    {$percent = $campaign.total_impressions_count/$campaign.max_impressions*100}
                                                    {$cost = $campaign.max_impressions*$user[$tier]/1000}
                                                    {if $percent<100}
                                                        <p>Total Budget Allocation: $ {$cost|string_format:"%.2f"}</p>
                                                        <p>Total Budget Left: $ {($cost*$percent/100)|string_format:"%.2f"}</p>
                                                    {else}
                                                        <p>Total Budget Allocation: $ {$cost|string_format:"%.2f"}</p>
                                                        <p>Total Budget Spent: $ {$cost|string_format:"%.2f"}</p>
                                                    {/if}
                                                {else if $campaign.is_thru_guarantee == 'Y'}
                                                    {$cost = $campaign.percentage_max_budget - $all_cost}
                                                    {if ($cost>0)}
                                                        {$percent_cost = $cost*100/$campaign.percentage_max_budget}
                                                        <p>Total Budget Allocation: $ {$campaign.max_budget|string_format:"%.2f"}</p>
                                                        <p>Total Budget Left: $ {($campaign.max_budget*$percent_cost/100)|string_format:"%.2f"}</p>
                                                    {else}
                                                        <p>Total Budget Allocation: $ {$campaign.max_budget|string_format:"%.2f"}</p>
                                                        <p>Total Budget Spent: $ {$campaign.max_budget|string_format:"%.2f"}</p>
                                                    {/if}
                                                {else}
                                                    <p>Total Budget Allocation: No budget Allocated</p>
                                                    <p>Total Budget Spent: $ {$all_cost|string_format:"%.2f"}</p>
                                                {/if}
                                            {/if}
                                        </div>
                                        <div class="theme-geoform-group them-form-group">
                                            <h6 class="budget-note">How much additional budget would you like to allocate?</h6>
                                        </div>
                                        <div class="theme-geo-narrow-group theme-geoform-group form-group theme-inline-group budget">
                                            <label class="theme-inline-label">$</label>
                                            <input name="budget" type="text" value="" placeholder="$500" maxlength="9" class=" form-control" />
                                        </div>
                                        <input type="hidden" name="campaign_id" value="{$campaign.id}">
                                        {if $editable || $campaign.campaign_status == 'COMPLETED'}

<!--                                        <div class="theme-geoform-group theme-form-group theme-submit-group">-->
<!--                                            <input type="reset" value="Cancel" class="theme-cancel-btn theme-submit-control" />-->
<!--                                            <input type="submit" value="Save" class="theme-cancel-btn theme-submit-control" />-->
<!--                                        </div>-->
                                        <div class="geoloc-button">
                                            <button type="reset" class="btn btn-default">Cancel</button>
                                            <button type="submit" class="btn btn-info">Save</button>
                                        </div>
                                        {/if}
                                    </form>
                                </div>
                                {/if}

                                {if $campaign.network_id != 5}
                                <div class="theme-geoform-group theme-form-group theme-inline-group theme-submit-group theme-align-center">
                                    <div class="addKeyword">
                                        <button id="btn_add_ad" data-toggle="modal" data-target="#ad_modal" class="btn btn-info" >Add Keywords</button>
                                    </div>
                                    <div class="modal fade" id="ad_modal" role="dialog"  data-keyboard="false" data-backdrop="static">
                                        <div class="modal-dialog">
                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title text-left" id="myModalLabel">Add your Keywords</h4>
                                                </div>
                                                <div class="modal-body ">
                                                    <div class="theme-ad-subrow">
                                                        <form id="theme-keywords-form">
                                                                <div class="textarea_block form-group keywords_block text-left">
                                                                    <textarea type="text" id="keyword_height" maxlength="80" value="" placeholder="Enter keywords for your ad" data-type="keywords"  class="form-control full_width"></textarea>
                                                                    <span style="font-size:12px; color: #999898;"> Character Left: <span maxlength="80" class="charecter_count">80</span> </span> <br>
                                                                    <span class="words" style="font-size:12px; color: #999898;"> Words count: <span class="words_count">0</span> </span>
                                                                </div>
                                                                <div class="keyword_list_block" >
                                                                    <input type="hidden" name="campaign_id" value="{$campaign.id}">
                                                                    {if $campaign.keywords[0] != 'RON'}
                                                                    {foreach from=$campaign.keywords item=keyword}
                                                                    <div class="add-keyword">
                                                                        <p>
                                                                            <span>{$keyword}</span>
                                                                            <button type="button" class="close remove_keyword"><span class="glyphicon glyphicon-trash trash_keyword" ></span></button>
                                                                            <button type="button" class="edit_keyword theme-report-table-edit-pencil" >
                                                                                <img src="/v2/images/report-template/table-manage-edit-icon.png" alt="">
                                                                            </button>
                                                                        </p>
                                                                        <input type="hidden" name="keywords[]" value="{$keyword}">
                                                                    </div>
                                                                    {/foreach}
                                                                    {/if}

                                                                </div>
                                                            </form>
                                                    </div>
                                                </div>

                                            <div class="modal-footer">
<!--                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
                                                <button type="button" class="btn btn-default" id="add_new_keyword">Add</button>
                                                <button type="submit" form="theme-keywords-form" class="btn btn-primary">Save</button>

                                                <div class="theme-geoform-group theme-form-group">
                                                    <div class="keyword_text">
                                                        <!--                                                                        keyword : Broad match <br>-->
                                                        <!--                                                                        +keyword : Broad match modifire <br>-->
                                                        <!--                                                                        "keyword" : Phrase match <br>-->
                                                        <!--                                                                        [keyword] : Exact match <br>-->
                                                        <!--                                                                        -keyword : Negative match-->
                                                    </div>
                                                </div>

                                                    <!--                                                                    <input type="reset" value="Cancel" class="theme-cancel-btn theme-submit-control" />-->


                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {/if}
                                <div id="snippet" class="snippet-background display-block">
                                    <p>
                                        Please add the below Javascript snippet to your web page(s), directly before the closing &lt;/body&gt; tag.
                                    </p>
                                    &lt;script&gt; var prodata_user_id = {$user.id}, var prodata_campaign_id = {$campaign.id};&lt;/script&gt;<br>
                                    &lt;script src="//reporting.prodata.media/v2/js/retargeting.js"&gt;&lt;/script&gt;
                                </div>
                                {if $campaign.campaign_type == "FB-LEAD"}
                                    <div class="fb_form_block">
                                        <form class="form-horizontal" id="theme-fb-form-form">
                                            <h4>Edit Leads Delivery Options</h4>
                                            <div class="edit-form">
                                                <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                    <label class="theme-inline-label">Where to send lead info?</label>
                                                    <select id="fb_form_export_type" name="export_type" class="form-control">
                                                        <option value="email_address" {if $form.export_type == 'email_address'}selected{/if}>Email Address</option>
                                                        <option value="export_as_csv" {if $form.export_type == 'export_as_csv'}selected{/if}>Export as CSV</option>
                                                    </select>
                                                    <div id="email_export_block">
    <!--                                                    <div class="theme-geoform-group theme-form-group theme-inline-group">-->

                                                            <label class="theme-inline-label add_margin">Email Address</label>
                                                            <input class="form-control add_margin" type="text" name="email" value="{$form.email}">

                                                            <label class="theme-inline-label add_margin">How to send lead info?</label>
                                                            <select id="fb_form_email_type" name="email_type" class="form-control add_margin">
                                                                <option value="immediately" {if $form.email_type == 'immediately'}selected{/if}>Send Immediately As Received</option>
                                                                <option value="daily" {if $form.email_type == 'daily'}selected{/if}>Send Once Per Day</option>
                                                            </select>
    <!--                                                    </div>-->
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="campaign_id" value="{$campaign.id}">
                                            <input type="hidden" name="form_id" value="{$form.id}">
                                            {if $editable}
                                            <div class="geoloc-button">
                                                <button type="reset" class="btn btn-default">Cancel</button>
                                                <button type="submit" class="btn btn-info">Save</button>
                                            </div>
                                            {/if}
                                        </form>
                                    </div>
                                {/if}
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- #Theme Report Page Header -->

    </section>
</main>

<script>var selected_campaign_io_for_retargeting = {$campaign.retargeting_ios_array|json_encode};</script>
<!-- #Theme Report Page Structure -->
<script src="/v2/js/loader.js"></script>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="/v2/js/jquery-2.0.3.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/v2/js/bootstrap.min.js"></script>
<!-- Include all the complipled plugins (below) need to creat charts/pie/maps, or include individual files as needed -->
<script src="/v2/js/jquery.validate.min.js"></script>
<script src="/v2/js/datetime-picker.jquery.js"></script>
<!-- Include all the complipled plugins (below) need to creat charts/pie/maps, or include individual files as needed -->
<!--<script src="js/bootstrap-datepicker.js"></script>-->
<!-- ikentoo custom script -->
<script src="/v2/js/chosen/chosen.jquery.js"></script>
<script src="/v2/js/iscript.js"></script>
<script src="/v2/js/campaign-edit.js"></script>
<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<link href="/v2/css/jstree/jstree.min.css" rel="stylesheet" type="text/css"/>
<script src="/v2/js/jstree.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
{literal}
<script>
    $(document).ready(function() {
        $('#fb_form_export_type').change(function(){
            var value = $(this).val(); console.log(value);
            if(value=='email_address') {
                $('#email_export_block').show();
            } else {
                $('#email_export_block').hide();
            }
        });

        $('#fb_form_export_type').trigger('change');

       
        
        var campaign_geotype = $('#camp_geotype').val();
        var campaign_zip = $('#camp_zip').val();
        var campaign_radius = $('#camp_radius').val();
        var campaign_state = $('#camp_state').val();

        if (campaign_geotype) {

            if(campaign_geotype == "postalcode") {
                $("[name='zip']").val(campaign_zip);
                $("#geo-postal-radius option").each(function () {
                    if ($(this).val() == campaign_radius) {
                        $(this).attr("selected", "selected");
                    }
                });
                $("#postal-code").trigger('click');

            } else {

                if(campaign_geotype == "state") {
                   $("#state").trigger('click');
                }
                $("#geo_country_select option").each(function () {
                    if ($(this).val() == $('#camp_country').val()) {
                        $(this).attr("selected", "selected");
                    }
                });
            }
        }

        $(document).on('click', '#state', function(){
            $('select#geo_country_select').trigger('change');
        });

        $.fn.editable.defaults.mode = 'inline';
        $('.edit').click(function(e){
            e.stopPropagation();
            $('#name').editable('toggle');
            $('.edit').hide();
        });

        $(document).on('click', '.editable-cancel, .editable-submit', function(){
            $('.edit').show();
        });

        // IO Based Retargeting Select box
        var io_based_retargeting_ios_select = $('#io_based_retargeting_ios'),
            io_based_retargeting_ios_options = $('#io_based_retargeting_ios option');
        io_based_retargeting_ios_select.chosen({width: '100%'});
        $.ajax({
            method: "GET",
            url: "/v2/campaign/get_userlist_from_io",
            dataType: "json",
            data: { user_id: $('#user_id_to_pick_io').val() }
        }).done(function( response ) {
            io_based_retargeting_ios_select.empty();
            $.map( response.data, function( item ) {
                var is_disabled = item.network_id == 1 ? '' : 'disabled';
                var is_selected = '';
                if ( selected_campaign_io_for_retargeting.length && selected_campaign_io_for_retargeting.indexOf(item.io.trim()) != -1 ) {
                    is_selected = 'selected';
                }
                io_based_retargeting_ios_select.append("<option value='"+item.io+"' class='network_"+item.network_id+"' "+ is_disabled +" "+ is_selected +">" + item.io + "</option>")
            });
            io_based_retargeting_ios_select.trigger("chosen:updated");
        });
    });
</script>
{/literal}
{include file="v2/sections/footer.php"}
</body>
</html>
