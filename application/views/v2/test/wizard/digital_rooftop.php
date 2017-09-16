<fieldset>
    <div class="theme-tab-content theme-report-tab-content digitel_rooftop">
        <div class="theme-gelocation-from-row">
            <div class="theme-display-table theme-no-gutter">
                <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-two-col theme-table-top-cell">
                    <div class="theme-geolocation-form-wrap">
                        <div class="theme-geoform-group theme-tabbed-form-group them-form-group">
                            <h2>Select Geo-Location Type</h2>
                            <div class="theme-form-radio-group location_type">
                                <input name="geotype" type="radio" value="country" class="theme-geofrom-control theme-tabbed-form-control geo-country-radio" id="country" checked />
                                <label for="country" class="theme-geoform-label theme-tabbed-form-label">Country (Nationwide)</label>
                                <input name="geotype" type="radio" value="state" class="theme-geofrom-control theme-tabbed-form-control geo-state-radio" id="state" />
                                <label for="state" class="theme-geoform-label theme-tabbed-form-label">State</label>
                                <input name="geotype" type="radio" value="postalcode" class="theme-geofrom-control theme-tabbed-form-control geo-postal-radio" id="postal-code" />
                                <label for="postal-code" class="theme-geoform-label theme-tabbed-form-label">Postal Code</label>
                            </div>
                        </div>
                        <div id="geo-country" class="theme-geoform-group theme-form-group">
                            <div class="theme-geofrom-selectbox">
                                <label for="">Country (Nationwide)</label>
                                <select id="geo-country" name="country" class="theme-form-control theme-control">
                                    <option value="">Select Country</option>
                                    <option value="CA">Canada</option>
                                    <option value="MX">Mexico</option>
                                    <option value="GB">United Kingdom</option>
                                    <option value="US">United States</option>
                                </select>
                            </div>
                        </div>
                        <div id="geo-state" class="theme-geoform-group theme-form-group">
                            <div class="theme-geofrom-selectbox">
                                <label for="">State</label>
                                <select id="geo-state" name="state[]" class="theme-form-control theme-multi-selectbox theme-control" multiple>

                                </select>
                            </div>
                        </div>

                        <div id="geo-postal" class="theme-geoform-group theme-form-group geo-postal">
                            <div class="theme-geofrom-selectbox">
                                <p><label for="">Postal Code <span style="font-size: 12px; color: #909090;">( Enter one or more postal codes, space separated )</span></label></p>
                                <div class="theme-inlineform-group zip_code">
                                    <input name="zip" type="text" value="" placeholder="Enter your postal code" class="theme-form-control theme-geoform-control" />
                                </div>
                                <div class="theme-inlineform-group">
                                    <select id="geo-postal-radius" name="radius" class="theme-form-control">
                                        <option value="">Select Radius</option>
                                        <option value="10">10 Miles</option>
                                        <option value="15">15 Miles</option>
                                        <option value="25">25 Miles</option>
                                        <option value="50">50 Miles</option>
                                        <option value="75">75 Miles</option>
                                        <option value="100">100 Miles</option>
                                        <option value="125">125 Miles</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="theme-report-socialsignal-wrap">
                            <div class="theme-geoform-group theme-form-group">
                                <h2>Audience Options</h2>
                                <div class="theme-geofrom-selectbox">
                                    <!--  <label for="">Select Gender</label> -->
                                    <select id="geo-gender" name="gender" class="theme-form-control theme-control">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                    <br/>
                                    <select id="geo-income-level" name="income_level" class="theme-form-control theme-control">
                                        <option value="">Select Income Level</option>
                                        <option value="2000">2000</option>
                                        <option value="3000">3000</option>
                                        <option value="4000">4000</option>
                                    </select>
                                    <br/>
                                    <select id="geo-chil-parent" name="parent" class="theme-form-control theme-control">
                                        <option value="">Select Children Present</option>
                                        <option value="Y">Yes</option>
                                        <option value="N">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-two-col theme-table-top-cell">
                    <div id="theme-retargetting-section" class="theme-geolocation-form-wrap theme-retargetting-section">

                        <div class="theme-geoform-group theme-tabbed-form-group them-form-group">
                            <h2>Remarketing Options</h2>
                        </div>
                        <div class="theme-bordered-legend theme-custom-field">
                            <div id="remarketing-campaign-group" class="theme-geoform-group theme-form-group theme-inline-group">
                                <input name="is_remarketing" type="checkbox" value="Y" class="theme-geoform-control theme-form-control" id="marketing-option" />
                                <label for="marketing-option" class="theme-inline-label theme-custom-label">Is Remarketing Campaign?</label>
                            </div>
                            <div id="theme-retargetting-group">
                                <div class="theme-geoform-group theme-form-group">
                                    <label class="theme-inline-label">Expanded Vertical Retargting</label>
                                    <select id="retargetting" name="is_remarketing_io" class="theme-form-control">
                                        <option value="Y">Yes</option>
                                        <option value="N">No</option>
                                    </select>
                                </div>
                                <div class="theme-geoform-group theme-form-group" id="the-basics">
                                    <label class="theme-inline-label">Linked Campaign(s)</label>
                                    <select id="remarketing_io" name="remarketing_io[]" multiple>
                                        <option value="" disabled="disabled" selected></option>
                                        {foreach from=$io_list item=io}
                                        {$value = $io["io"]}
                                        {$option = $io["io"]}
                                        <option value= "{$value}">  {ucfirst($option)}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="theme-geolocation-form-wrap theme-mobile-carrer-row">
                        <div class="theme-geoform-group theme-tabbed-form-group them-form-group">
                            <h2>Mobile / Carrier Options</h2>
                        </div>
                        <div class="theme-geoform-group theme-form-group">
                            <select id="geo-device-type" name="device_type" class="theme-form-control theme-control">
                                <option value="">Any Traffic Type</option>
                                <option value="desktop">Desktop only</option>
                                <option value="mobile">Mobile only</option>
                            </select>
                            <br/>
                            <select id="geo-carrier" name="carrier" class="theme-form-control theme-control">
                                <option value="">Any Carrier</option>
                            </select>
<!--                            <br/>-->
<!--                            <select id="geo-preferred-mobile" name="preferred_mobile" class="theme-form-control theme-control">-->
<!--                                <option value="">Any Property</option>-->
<!--                                <option value="mobile_friendly">Mobile Friendly</option>-->
<!--                                <option value="desktop_friendly">Desktop Friendly</option>-->
<!--                                <option value="in_app">In App</option>-->
<!--                            </select>-->
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
