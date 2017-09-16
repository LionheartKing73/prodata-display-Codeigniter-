<fieldset class="display_none">
    <div class="theme-tab-content theme-report-tab-content">
        <div class="theme-report-tabbed-form-wrap">
            <div class="theme-form-legend theme-display-table theme-no-gutter campaign_info">
                <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-form-half-col">
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">Campaign Name :</label>
                        <input name="name" maxlength="32" type="text" value="" placeholder="Campaign Name" class="theme-geoform-control theme-form-control" />
                    </div>
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">ZIP code list :</label>
                        <input name="zip" type="text" value="" placeholder="Enter your postal code" class="theme-geoform-control theme-form-control" />
                        <span style="margin-left: 200px; font-size: 12px; color: #909090;">( Enter one or more postal codes, space separated )</span>
                    </div>
                </div>
                <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-form-half-col enable-campaign-criteria-col">

                    <div class="theme-geoform-group theme-form-group">
                        <label>Select the amount of traffic you desire?</label>
                        <div class="clearfix"></div>
                        <div class="theme-tabbed-form-group tier_radio">
                            <input name="campaign_tier" type="radio" value="tier_1" class="theme-tabbed-form-control" checked id="tier_1" />
                            <label class="theme-tabbed-form-label" for="tier_1">Plan 1 - $100/mo</label>
                        </div>
                        <div class="theme-tabbed-form-group tier_radio">
                            <input name="campaign_tier" type="radio" value="tier_2" class="theme-tabbed-form-control" id="tier_2" />
                            <label class="theme-tabbed-form-label" for="tier_2">Plan 2 - $250/mo</label>
                        </div>
                        <div class="theme-tabbed-form-group" style="display: inline-block">
                            <input name="campaign_tier" type="radio" value="tier_3" class="theme-tabbed-form-control" id="tier_3" />
                            <label class="theme-tabbed-form-label" for="tier_3">Plan 3 - $500/mo</label>
                        </div>
                    </div>
                    <div class="theme-geoform-group theme-form-group click_count_div">
<!--                        <input name="max_budget" type="hidden" value="" class="theme-tabbed-form-control" id="max_budget">-->
                        <label>MAX Clicks:</label>
                        <span id="clicks_count_span">{$user.clicks_count_tier_1}</span>
                    </div>
                    <div class="theme-geoform-group theme-form-group">
                        <!--                        <input name="max_budget" type="hidden" value="" class="theme-tabbed-form-control" id="max_budget">-->
                        <label>MAX Impressions:</label>
                        <span id="impressions_count_span">{$user.impressions_count_tier_1}</span>
                    </div>
                </div>
            </div>
            <div class="theme-form-group theme-submit-group theme-align-center">
                <a href="javascript:" class="btn_next_step btn_continue" >CONTINUE</a>
            </div>
        </div>
    </div>
</fieldset>
