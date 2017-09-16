<fieldset>
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
                        <input name="name" maxlength="16" type="text" value="" placeholder="Campaign Name" class="theme-geoform-control theme-form-control" />
                    </div>
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">Campaign Vertical :</label>
                        <select name="vertical" class="theme-geoform-control theme-form-control">
                            <option value="" disabled="disabled" selected>Campaign Vertical</option>
                            {foreach from=$vertical_list item=vertical}
                            {$value = $vertical["vertical"]}
                            {$option = $vertical["vertical"]}
                            <option value= "{$value}">  {ucfirst($option)}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">Domain Name :</label>
                        <select name="domain" class="theme-geoform-control theme-form-control">
                            <option value="report-site.com" selected>report-site.com</option>
                            {foreach from=$domain_list item=domain}
                            {$value = $domain["name"]}
                            <option value= "{$value}">{ucfirst($value)}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">Start Date :</label>
                        <input name="campaign_start_datetime" type="text" value="" placeholder="01-15-2015" id="start_date_datepicker" class="start_date_datepicker  theme-geoform-control theme-form-control  theme-date-picker " />
                    </div>
                    <div class="theme-geoform-group theme-form-group theme-inline-group" id="daily_budgets">
                        <label class="theme-inline-label theme-light-weight">Daily Budgets :</label>
                        <input name="budget" maxlength="9" type="text" value="" placeholder="Daily Budgets" class="theme-geoform-control theme-form-control" />
                    </div>
                </div>
                <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-form-half-col enable-campaign-criteria-col" id="open_pixel_div">
                    <div class="">
                        <label style="font-size: 20px; margin-bottom: 25px;" >Has Open Pixel?</label>
                        <div class="clearfix" ></div>
                        <div class="theme-tabbed-form-group pixel_radio">
                            <input name="fire_open_pixel" type="radio" value="Y" class="theme-tabbed-form-control open_pixel" id="has_open_pixel_yes"/>
                            <label class="theme-tabbed-form-label" for="has_open_pixel_yes">Yes</label>
                        </div> 
                        <div class="theme-tabbed-form-group pixel_radio">
                            <input name="fire_open_pixel" type="radio" value="N" class="theme-tabbed-form-control open_pixel" id="has_open_pixel_no"/>
                            <label class="theme-tabbed-form-label" for="has_open_pixel_no" checked>No</label>
                        </div>
                        <div class="clearfix"></div>
                        <div class="theme-tabbed-form-group hidden" id="open_pixel_layer">
                            <div>
                                <input type="text" placeholder="Enter Pixel Image HTML" name="open_pixel_src[]" class="input-xlarge open_pixel_src"><br>
                                <input type="text" placeholder="Enter Pixel Image HTML" name="open_pixel_src[]" class="input-xlarge open_pixel_src"><br>
                                <input type="text" placeholder="Enter Pixel Image HTML" name="open_pixel_src[]" class="input-xlarge open_pixel_src"><br>
                                <input type="text" placeholder="Enter Pixel Image HTML" name="open_pixel_src[]" class="input-xlarge open_pixel_src"><br>
                                <input type="text" placeholder="Enter Pixel Image HTML" name="open_pixel_src[]" class="input-xlarge open_pixel_src"><br>
                                <input type="text" placeholder="Enter Pixel Image HTML" name="open_pixel_src[]" class="input-xlarge open_pixel_src"><br>
                                <input type="text" placeholder="Enter Pixel Image HTML" name="open_pixel_src[]" class="input-xlarge open_pixel_src"><br>
                                <input type="text" placeholder="Enter Pixel Image HTML" name="open_pixel_src[]" class="input-xlarge open_pixel_src"><br>
                                <input type="text" placeholder="Enter Pixel Image HTML" name="open_pixel_src[]" class="input-xlarge open_pixel_src"><br>
                                <input type="text" placeholder="Enter Pixel Image HTML" name="open_pixel_src[]" class="input-xlarge open_pixel_src"><br>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-form-half-col enable-campaign-criteria-col">
                    <div class="theme-geoform-group theme-form-group theme-inline-group enable-campaign-criteria" id="enable_campaign_div">
                        <input name="more_options" type="checkbox" value="Y" class="theme-geoform-control theme-form-control" id="marketing-options">
                        <label for="marketing-options" class="theme-inline-label theme-custom-label theme-light-weight">Enable Campaign End Criteria? :</label>
                    </div>
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">Maximum Impressions :</label>
                        <input id="max_impressions" maxlength="9"  name="max_impressions" type="text" value="" placeholder="Maximum Impressions" class="theme-geoform-control theme-form-control" />
                    </div>
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">Maximum Budget :</label>
                        <input id="max_budget" name="max_budget" maxlength="9" type="text" value="" placeholder="Maximum Budget" class="theme-geoform-control theme-form-control" />
                    </div>
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">Maximum Clicks :</label>
                        <input id="max_clicks" name="max_clicks" maxlength="9" type="text" value="" placeholder="Maximum Clicks" class="theme-geoform-control theme-form-control" />
                    </div>
                    <div class="theme-geoform-group theme-form-group theme-inline-group">
                        <label class="theme-inline-label theme-light-weight">End Date :</label>
                        <input name="campaign_end_datetime" type="text" value="" placeholder="01-15-2015" id="end_date_datepicker" class="end_date_datepicker theme-geoform-control theme-form-control  theme-date-picker " />
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