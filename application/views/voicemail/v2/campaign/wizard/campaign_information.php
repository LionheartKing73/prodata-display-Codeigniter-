<fieldset class="display_none">
    <div class="theme-tab-content theme-report-tab-content">
        <div class="theme-report-tabbed-form-wrap">
            <div class="theme-form-legend theme-display-table theme-no-gutter campaign_info">
                <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-form-half-col">
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">IO # :</label>
                        <input id="io" name="io" maxlength="16" type="text" value="" placeholder="IO # : 1233444" class="theme-geoform-control theme-form-control" style="text-transform: uppercase" />
                    </div>
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">Campaign Name :</label>
                        <input name="name" maxlength="32" type="text" value="" placeholder="Campaign Name" class="theme-geoform-control theme-form-control" />
                    </div>
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">Campaign Vertical :</label>
                        <select name="vertical" class="theme-geoform-control theme-form-control">
                            <option value="" disabled="disabled" selected>Campaign Vertical</option>
                            {foreach from=$vertical_list item=vertical}
                            {$value = $vertical["vertical"]}
                            {$option = $vertical["vertical"]}
                            <option {if $vertical["is_airpush"] == 1} network-type="airpush" {/if} value= "{$value}">  {ucfirst($option)}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">Domain Name :</label>
                        <select name="domain" class="theme-geoform-control theme-form-control">
                            {foreach from=$domain_list item=domain}
                            {if $domain.name=='prodataretargeting.com' || $domain.name =='reporting.prodata.media'}
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
                    <label id="min_budget" style="font-size:16px;">Minimum Campaign Budget: $ <span style="color:red;">{$user.min_budget|string_format:"%.2f"}</span></label>
                </div>
                <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-form-half-col enable-campaign-criteria-col">
                    {if $user.is_billing_type=='PERCENTAGE'}
                    <div class="theme-geoform-group theme-form-group theme-inline-group enable-campaign-criteria" id="enable_campaign_div" >
                        <input name="more_options" type="checkbox" value="Y" class="theme-geoform-control theme-form-control" id="marketing-options">
                        <label for="marketing-options" class="theme-inline-label theme-custom-label theme-light-weight">Enable Campaign End Criteria?</label>
                    </div>
                    {else}
                    <div class="theme-geoform-group theme-form-group">
                        <label> What traffic quality level do you desire?</label>
                        <div class="clearfix"></div>
                        <div class="theme-tabbed-form-group tier_radio">
                            <input name="campaign_tier" type="radio" value="tier_1" class="theme-tabbed-form-control" checked id="tier_1" />
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
                        <label class="theme-inline-label theme-light-weight">Maximum Clicks :</label>
                        <input id="max_clicks" name="max_clicks" maxlength="9" type="text" value="" placeholder="Maximum Clicks" class="theme-geoform-control theme-form-control fillone flat_fields" />
                    </div>
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">End Date :</label>
                        <input name="campaign_end_datetime" type="text" value="" placeholder="01-15-2015" id="end_date_datepicker" class="end_date_datepicker fillone theme-geoform-control theme-form-control  theme-date-picker" />
                    </div>
                </div>

		<!-- manage viewer start -->
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
                    <div class="">
                        <label style="font-size: 20px; margin-bottom: 25px;" >Has Open Pixel?</label>
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
