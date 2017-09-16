<fieldset class="display_none" style="overflow-y: scroll;">
    <div class="theme-tab-content theme-report-tab-content">
        <div class="theme-report-tabbed-form-wrap">
            <div class="theme-form-legend theme-display-table theme-no-gutter campaign_info">
                <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-form-half-col">
                    <div class="theme-geoform-group theme-form-group theme-inline-group" id="so_number_div">
                        <label class="theme-inline-label theme-light-weight">SO Number :</label>
                        <input id="so_number" name="so" maxlength="8" type="text" value="" placeholder="So Number" class="theme-geoform-control theme-form-control" />
                    </div>
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">IO # :</label>
                        <input id="io" name="io" maxlength="16" type="text" value="" placeholder="IO # : 1233444" class="theme-geoform-control theme-form-control" style="text-transform: uppercase" />
                    </div>
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">Campaign Name :</label>
                        <input name="name" maxlength="32" type="text" value="" placeholder="Campaign Name" class="theme-geoform-control theme-form-control" />
                    </div>
                    {if $user.is_admin}
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">Assign To User :</label>
                        <select name="assign_user" class="theme-geoform-control theme-form-control">
                            <option value="" disabled="disabled" selected>Select Customer</option>
                            {foreach from=$customers item=customer}
                            <option value= "{$customer.id}" {if $customer.id == $user.id}selected="selected"{/if}> {$customer.company}</option>
                            {/foreach}
                        </select>
                    </div>
                    {/if}

                    <div class="theme-geoform-group theme-form-group theme-inline-group clearfix">
                        <div id="iab_category_select" class="categories_block theme-form-control pull-right theme-inline-jstree-selection">
                            <ul>
                                {foreach from=$iab_categories item=category}
                                <li id="{$category.category_id}" data-catid="{$category.category_id}"
                                    data-parentcatid="{$category.parent_category_id}">{$category.name}
                                    {if !empty($category.children)}
                                    <ul>
                                        {foreach from=$category.children item=sub_cat}
                                        <li id="{$sub_cat.category_id}"
                                            data-catid="{$sub_cat.category_id}" data-parentcatid="{$sub_cat.parent_category_id}">{$sub_cat.name}</li>
                                        {/foreach}
                                    </ul>
                                    {/if}
                                </li>
                                {/foreach}
                            </ul>
                        </div>
                        <label class="theme-inline-label theme-light-weight">Campaign Vertical :</label>
                        <textarea name="vertical" class="vertical-cache-textarea"></textarea>
                    </div>

                    <!-- <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">Campaign Vertical :</label>
                        <select name="vertical" class="theme-geoform-control theme-form-control">
                            <option value="" disabled="disabled" selected>Campaign Vertical</option>
                            {foreach from=$vertical_list item=vertical}
                            {$value = $vertical["vertical"]}
                            {$option = $vertical["vertical"]}
                            <option {if $vertical["is_airpush"] == 1} network-type="airpush" {/if} value= "{$value}">  {ucfirst($option)}</option>
                            {/foreach}
                        </select>
                    </div> -->

                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">Domain Name :</label>
                        <select name="domain" class="theme-geoform-control theme-form-control">
                            {foreach from=$domain_list item=domain}
                            {if $domain.name=='prodataretargeting.com' || $domain.name=='reporting.prodata.media'}
                            <option value="{$domain.name}" selected>{$domain.name}</option>
                            {else}
                            <option value="{$domain.name}">{$domain.name}</option>
                            {/if}
                            {/foreach}
                        </select>
                    </div>
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">Start Date :</label>
                        <input name="campaign_start_datetime" type="text" value="" placeholder="01-15-2015" id="start_date_datepicker" class="start_date_datepicker  theme-geoform-control theme-form-control  theme-date-picker " />
                    </div>
                    {if $user.is_billing_type=='PERCENTAGE'}
                    <div class="theme-geoform-group theme-form-group theme-inline-group" id="daily_budgets">
                        <label class="theme-inline-label theme-light-weight">Daily Budget :</label>
                        <input name="budget" maxlength="9" type="text" value="" placeholder="Daily Budget" class="theme-geoform-control theme-form-control" />
                        <label id="min_daily_budget_error" for="min_daily_budget">Minimum Campaign Daily Budget: $<span class="min_daily_budget_error_span"></span></label>
                    </div>
                    {/if}
                </div>

                <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-form-half-col enable-campaign-criteria-col" id="min_budget_div">
                    <label id="min_budget" class="min-budget" style="font-size:16px;">Minimum Campaign Budget: $ <span>{$user.min_budget|string_format:"%.2f"}</span></label>
                </div>
                <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-form-half-col enable-campaign-criteria-col">
                    {if $user.is_billing_type=='PERCENTAGE'}
                    <div class="theme-geoform-group theme-form-group theme-inline-group enable-campaign-criteria" id="enable_campaign_div" >
                        <input name="more_options" type="checkbox" value="Y" class="theme-geoform-control theme-form-control" id="marketing-options">
                        <label for="marketing-options" class="theme-inline-label theme-custom-label theme-light-weight">Enable Campaign End Criteria?</label>
                    </div>
                    {else}
                    <div class="theme-geoform-group theme-form-group tiers_block">
                        <label> What traffic quality level do you desire?</label>
                        <div class="clearfix"></div>
                        <div class="theme-tabbed-form-group tier_radio">
                            <input name="campaign_tier" type="radio" value="tier_1" class="theme-tabbed-form-control" id="tier_1" />
                            <label class="theme-tabbed-form-label" for="tier_1">Tier 1</label>
                        </div>
                        <div class="theme-tabbed-form-group tier_radio">
                            <input name="campaign_tier" type="radio" value="tier_2" class="theme-tabbed-form-control" id="tier_2" />
                            <label class="theme-tabbed-form-label" for="tier_2">Tier 2</label>
                        </div>
                        <div class="theme-tabbed-form-group" style="display: inline-block">
                            <input name="campaign_tier" type="radio" value="tier_3" class="theme-tabbed-form-control" id="tier_3" />
                            <label class="theme-tabbed-form-label" for="tier_3">Tier 3</label>
                        </div>
                        <div class="theme-tabbed-form-group pull-right">
                            <input name="max_budget" type="hidden" value="" class="theme-tabbed-form-control" id="max_budget" />
                            <label>MAX Budget $:</label>
                            <span id="max_budget_span"></span>
                        </div>
                    </div>
                    {/if}
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">Maximum Impressions :</label>
                        <input id="max_impressions" maxlength="9"  name="max_impressions" type="text" value="" placeholder="Maximum Impressions" class="theme-geoform-control theme-form-control fillone flat_fields" />
                    </div>
                    {if $user.is_billing_type=='PERCENTAGE'}
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">Maximum Budget :</label>
                        <input id="max_budget" name="max_budget" maxlength="9" type="text" value="" placeholder="Maximum Budget" class="theme-geoform-control theme-form-control fillone" />
                    </div>
                    {/if}
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label id="max_clicks_label" class="theme-inline-label theme-light-weight">Maximum Clicks :</label>
                        <input id="max_clicks" name="max_clicks" maxlength="9" type="text" value="" placeholder="Maximum Clicks" class="theme-geoform-control theme-form-control fillone flat_fields" />
                    </div>
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">End Date :</label>
                        <input name="campaign_end_datetime" type="text" value="" placeholder="01-15-2015" id="end_date_datepicker" class="end_date_datepicker fillone theme-geoform-control theme-form-control  theme-date-picker" />
                    </div>
                </div>

		<!-- manage viewer start -->
            {if $user.is_guarantee == 'Y' && ($user.is_admin == '1' || $user.is_billing_type == 'FLAT')}
                <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-form-half-col enable-campaign-criteria-col" id='guarantee' style="display: none">
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <input type="checkbox" class="theme-geoform-control theme-form-control" id="show_guarantee">
                        <label for="show_guarantee" class="theme-inline-label theme-custom-label theme-light-weight">ProData {$user.is_guarantee_percentage} % Click Thru Guarantee</label>
                    </div>
                    <div id="guarantee_block_wizard" style="display: none">
                            <div class="theme-geoform-group theme-form-group" >
                                <div class="theme-geofrom-selectbox">
                                    <label class="guarantee_block_wizard_title" for="">Guaranteed click thru rate</label>
                                    <div  name="access_viewers[]" class="theme-form-control theme-multi-selectbox theme-control" id="guarantee">
                                    The ProData Click Thru Guarantee provides a minimum guaranteed click thru rate. ProData will continue to run the campaign display impressions, up to 2x the number of contracted impressions to satisfy the Guarantee.
                                    </div>
                                </div>
                            </div>
                        <input type="hidden" name="thru_guarantee" value="N" id="thru_guarantee">
                    </div>
                </div>
                {/if}
                {if $viewers}
                <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-form-half-col enable-campaign-criteria-col">
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <input type="checkbox" class="theme-geoform-control theme-form-control" id="show_viewers">
                        <label for="show_viewers" class="theme-inline-label theme-custom-label theme-light-weight">Enable Campaign Reporting User(s)?</label>
                    </div>
                    <div id="manage_access_block_wizard" style="display: none">
                            <div class="theme-geoform-group theme-form-group" >
                                <div class="theme-geofrom-selectbox">
                                    <label class="manage_access_block_wizard_title" for="">Manage access to the campaigns for viewer(s)</label>
                                    <select  name="access_viewers[]" class="theme-form-control theme-multi-selectbox theme-control" multiple="" id="multiple_select_manage_viewers">
                                        {foreach from=$viewers item=viewer}
                                        <option value= "{$viewer.id}">  {$viewer.username}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>

                    </div>
                </div>
                {/if}
                <!-- manage viewer end -->

                <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-form-half-col enable-campaign-criteria-col" id="open_pixel_div">
                    <div class="has-open-pixel">
                        <label class="label-has-open-pixel" >Has Open Pixel?</label>
                        <div class="clearfix" ></div>
                        <div class="theme-tabbed-form-group pixel_radio">
                            <input name="fire_open_pixel" type="radio" value="Y" class="theme-tabbed-form-control open_pixel" id="has_open_pixel_yes"/>
                            <label class="theme-tabbed-form-label" for="has_open_pixel_yes">Yes</label>
                        </div>
                        <div class="theme-tabbed-form-group pixel_radio">
                            <input name="fire_open_pixel" type="radio" value="N" class="theme-tabbed-form-control open_pixel" id="has_open_pixel_no" checked/>
                            <label class="theme-tabbed-form-label" for="has_open_pixel_no" >No</label>
                        </div>
                        <div class="clearfix"></div>
                        <div class="theme-tabbed-form-group hidden" id="open_pixel_layer">
                            <div>
                                <input type="text" placeholder="Enter Pixel Image HTML" name="open_pixel_src[]" class="input-xlarge open_pixel_src theme-geoform-control theme-form-control"><br>
                                <input type="text" placeholder="Enter Pixel Image HTML" name="open_pixel_src[]" class="input-xlarge open_pixel_src theme-geoform-control theme-form-control"><br>
                                <input type="text" placeholder="Enter Pixel Image HTML" name="open_pixel_src[]" class="input-xlarge open_pixel_src theme-geoform-control theme-form-control"><br>
                                <input type="text" placeholder="Enter Pixel Image HTML" name="open_pixel_src[]" class="input-xlarge open_pixel_src theme-geoform-control theme-form-control"><br>
                                <input type="text" placeholder="Enter Pixel Image HTML" name="open_pixel_src[]" class="input-xlarge open_pixel_src theme-geoform-control theme-form-control"><br>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="theme-form-group theme-submit-group theme-align-center">
                <a href="javascript:" class="btn_previous_step" >BACK</a>
                <a href="javascript:" class="btn_next_step btn_continue" >CONTINUE</a>
            </div>
        </div>
    </div>
</fieldset>
