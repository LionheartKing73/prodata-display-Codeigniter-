<fieldset class="display_none">
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
                                    <option value="US">United States</option>
                                    <option value="CA">Canada</option>
                                    <option value="MX">Mexico</option>
                                    <option value="GB">United Kingdom</option>
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
                            <label class="yahoo_alert" style="display: none">For postal codes Yahoo support only Search chanel</label>
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
                                        <option value="Under 15k">Under 15k</option>
                                        <option value="15k-24.9k">15k-24.9k</option>
                                        <option value="25k-34.9k">25k-34.9k</option>
                                        <option value="35k-49.9k">35k-49.9k</option>
                                        <option value="50k-74.9k">50k-74.9k</option>
                                        <option value="75k-99.9k">75k-99.9k</option>
                                        <option value="100k-149.9k">100k-149.9k</option>
                                        <option value="150k-199.9k">150k-199.9k</option>
                                        <option value="Over 200k">Over 200k</option>
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
                                    <label class="theme-inline-label">Expanded Vertical Retargeting</label>
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
                                        <option value= "{$value}" class="network_{$io.network_id}">  {ucfirst($option)}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="theme-geoform-group theme-form-group">
                                    <label class="theme-inline-label">Daily User-Ad Frequency</label>
                                    <select name="retargeting_frequency" class="theme-geoform-control theme-form-control">
                                        <option value="1">1</option>
                                        <option value="5">5</option>
                                        <option value="10" selected>10</option>
                                        <option value="15">15</option>
                                        <option value="50">25</option>
                                        <option value="1">50</option>
                                        <option value="100">100</option>
                                        <option value="UNLIMITED">UNLIMITED</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RETARGETING based on Pre-selected Campaign IO -->
                    <div id="theme-io-based-retargeting-section" class="theme-geolocation-form-wrap theme-io-based-retargeting-section">

                        <div class="theme-geoform-group theme-tabbed-form-group them-form-group">
                            <h2>Retargeting Options</h2>
                        </div>
                        <div class="theme-bordered-legend theme-custom-field">
                            <div id="retargeting-campaign-group" class="theme-geoform-group theme-form-group theme-inline-group">
                                <input name="is_io_based_retargeting" type="checkbox" value="Y"
                                    class="theme-geoform-control theme-form-control" id="io-based-retargeting-option" />
                                <label for="io-based-retargeting-option" class="theme-inline-label theme-custom-label">Is IO Based Retargeting?</label>
                            </div>
                            <div id="theme-io-based-retargeting-group">
                                <div class="theme-geoform-group theme-form-group" id="the-basics">
                                    <label class="theme-inline-label">Linked Campaign(s)</label>
                                    <select id="io_based_retargeting_ios" name="io_based_retargeting_ios[]" multiple>
                                        <option value="" disabled="disabled" selected></option>
                                        {foreach from=$io_list item=io}
                                        {$value = $io["io"]}
                                        {$option = $io["io"]}
                                        <option value= "{$value}" class="network_{$io.network_id}">  {ucfirst($option)}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div> <!-- / RETARGETING based on Pre-selected Campaign IO End -->

                    <div id="expand_fb" class="theme-geolocation-form-wrap" style="display: none;">
                        <div class="theme-geoform-group theme-tabbed-form-group them-form-group">
                            <h2>Expand FB to Audience Network / Instagram</h2>
                        </div>
                        <div class="theme-bordered-legend theme-custom-field">
                            <div class="theme-geoform-group theme-form-group theme-inline-group">
                                <input name="is_audience_network" type="checkbox" value="Y" class="theme-geoform-control theme-form-control" id="expand_audience_network" />
                                <label for="expand_audience_network" class="theme-inline-label theme-custom-label">Expand To Audience network ?</label>
                            </div>
                            <div class="theme-geoform-group theme-form-group theme-inline-group">
                                <input name="is_instagram" type="checkbox" value="Y" class="theme-geoform-control theme-form-control" id="expand_instagram" />
                                <label for="expand_instagram" class="theme-inline-label theme-custom-label">Expand To Instagram ?</label>
                            </div>
                        </div>
                    </div>

            <div id="yahoo_call" class="theme-geolocation-form-wrap" style="display: none;">
                <div class="theme-geoform-group theme-tabbed-form-group them-form-group">
                    <h2>Call To Action Buttons</h2>
                </div>
                <div class="theme-geoform-group theme-form-group">
                    <select class="theme-form-control theme-control action_buttons">
                        <option value="" selected>Select Action</option>
                        <option value="Apply Now">Apply Now</option>
                        <option value="Bet Now">Bet Now</option>
                        <option value="Book Now">Book Now</option>
                        <option value="Buy Now">Buy Now</option>
                        <option value="Compare">Compare</option>
                        <option value="Contact Us">Contact Us</option>
                        <option value="Directions">Directions</option>
                        <option value="Donate Now">Donate Now</option>
                        <option value="Download">Download</option>
                        <option value="Enroll Now">Enroll Now</option>
                        <option value="Follow Now">Follow Now</option>
                        <option value="Get App">Get App</option>
                        <option value="Get Coupon">Get Coupon</option>
                        <option value="Get Now">Get Now</option>
                        <option value="Get Offer">Get Offer</option>
                        <option value="Get Quote">Get Quote</option>
                        <option value="Get Rates">Get Rates</option>
                        <option value="Get Sample">Get Sample</option>
                        <option value="Install">Install</option>
                        <option value="Join Now">Join Now</option>
                        <option value="Launch">Launch</option>
                        <option value="Learn More">Learn More</option>
                        <option value="Listen Now">Listen Now</option>
                        <option value="Play Now">Play Now</option>
                        <option value="Play Game">Play Game</option>
                        <option value="Read More">Read More</option>
                        <option value="Record Now">Record Now</option>
                        <option value="Register">Register</option>
                        <option value="Remind Me">Remind Me</option>
                        <option value="Save Now">Save Now</option>
                        <option value="Sell Now">Sell Now</option>
                        <option value="Shop Now">Shop Now</option>
                        <option value="Sign Up">Sign Up</option>
                        <option value="Try Now">Try Now</option>
                        <option value="Use App">Use App</option>
                        <option value="Vote Now">Vote Now</option>
                        <option value="Watch Now">Watch Now</option>
                        <option value="Watch More">Watch More</option>
                    </select>
                </div>
            </div>

                    {*if !empty($smarty.get.taskid) && $smarty.get.taskid == 'pp245'*}
                    <div class="retargeting-ip-form-wrap">
                        <div id="ip_targeting" class="theme-geoform-group theme-tabbed-form-group them-form-group">
                            <h2>IP Targeting</h2>
                            <span style="display: block; font-size: 14px; color: #2c3e50;">
                                Upload Text (.txt) File only, Max. 2mb size and one IP/CIDR per line
                            </span>
                        </div>
                        <div class="theme-geoform-group theme-form-group">
                            <input type="file" accept="text/plain"
                                id="retargeting_ips_file" style="visibility: hidden; border: none;">
                            <textarea name="ip_targeting_ips_json" id="ip_targeting_ips_json" style="display: none;"></textarea>
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
                    {*/if*}

                    <div class="theme-geolocation-form-wrap theme-mobile-carrer-row">
                        <div id="device_block" class="theme-geoform-group theme-tabbed-form-group them-form-group">
                            <h2>Mobile / Carrier Options</h2>
                        </div>
                        <div class="theme-geoform-group theme-form-group">
                            <select id="geo-device-type" name="device_type" class="theme-form-control theme-control">
                                <option value="desktop + mobile">Any Traffic Type</option>
                                <option value="desktop">Desktop only</option>
                                <option value="mobile">Mobile only</option>
                                <option net-type="airpush" value="Mobile">Mobile</option>
                                <option net-type="airpush" value="Tablet">Tablet</option>
                            </select>
                            <br/>
                            <select id="geo-carrier" name="carrier" class="theme-form-control theme-control" style="display:none;">
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

                    <div class="theme-geolocation-form-wrap theme-domain-exclusions-row">
                        <div id="" class="theme-geoform-group theme-tabbed-form-group them-form-group">
                            <h2>Domain Exclusions</h2>
                        </div>
                        <div class="theme-geoform-group theme-form-group">
                            <textarea type="text" id="domain_exclusions" name="domain_exclusions" value="" placeholder="Enter exclusion domains"  class="theme-geoform-control theme-form-control full_width valid" aria-invalid="false"></textarea>
                        </div>
                    </div>

                    <div id="ad_keyword_block" class="theme-geoform-group theme-form-group theme-inline-group theme-submit-group theme-align-center">
                        <button id="btn_add_ad" data-toggle="modal" data-target="#ad_modal" class="theme-create-add-btn theme-submit-control" >Add Keywords</button>
                        <div class="modal fade" id="ad_modal" role="dialog"  data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Add your Keywords</h4>
                                    </div>
                                    <div class="modal-body text-left">
                                        <div class="theme-ad-subrow text_ads">
                                            <div class="theme-create-ad-form-wrap">

                                                <div class="textarea_block theme-geoform-group theme-form-group theme-inline-group keywords_block">
                                                    <span class="error_block_keywords" style="font-size:16px; color: red; display: none"></span><br>
                                                    <span style="font-size:14px; color: black;">Enter keywords <span style="color: red;">one per line</span></span>
                                                    <textarea type="text" id="keyword_height" value="" placeholder="Enter keywords for your ad" data-type="keywords"  class="theme-geoform-control theme-form-control full_width"></textarea>
<!--                                                    <span style="font-size:12px; color: #999898;"> Character Left: <span maxlength="80" class="charecter_count">80</span> </span> <br>-->
<!--                                                    <span class="words" style="font-size:12px; color: #999898;"> Words count: <span class="words_count">0</span> </span>-->
                                                    <span class="" style="font-size:14px; color: red;"> Words count should be max 10 words per keyword </span> <br>
                                                    <span class="" style="font-size:14px; color: red;"> Characters count should be max 80 characters </span> <br>
                                                    <span class="keywords_block_msg" style="font-size:14px; color: red;"> For DISPLAY campaigns you can use only BROAD match type </span>
                                                </div>
                                                <div class="theme-geoform-group theme-form-group theme-submit-group theme-align-center">
                                                    <input type="button" value="Save" class="no-padding theme-create-add-btn theme-submit-control" id="add_new_keyword">
                                                    <div class="keyword_list_block" >
                                                    </div>
                                                </div>
                                                <div class="theme-geoform-group theme-form-group">
                                                    <div class="keyword_text">
<!--                                                        keyword : Broad match <br>-->
<!--                                                        +keyword : Broad match modifire <br>-->
<!--                                                        "keyword" : Phrase match <br>-->
<!--                                                        [keyword] : Exact match <br>-->
<!--                                                        -keyword : Negative match-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
<!--                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
                                </div>
                            </div>
                        </div>

                    </div>

                    <div id="audience_block" class="theme-geoform-group theme-form-group theme-submit-group theme-align-center">
                        <button id="btn_add_ad" data-toggle="modal" data-target="#audience_modal" class="theme-create-add-btn theme-submit-control" >Manage Audience</button>
                        <div class="modal fade" id="audience_modal" role="dialog"  data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog modal-lg">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Manage Social Audience</h4>
                                    </div>
                                    <div class="modal-body text-left" style="padding: 0">
                                        <div class="theme-ad-subrow text_ads">
                                            <div class="theme-create-ad-form-wrap col-md-12">
                                                <div class="theme-geoform-group col-md-8">
<!--                                                    <label class="theme-inline-label">Select Category</label>-->
                                                    <select id="google_category_type_select" class="theme-form-control" placeholder="Select Category" style="display: none">
                                                        <option value="affinity" data-type="google"> Affinity Audiences</option>
                                                        <option value="in_market" data-type="google"> In-Market Audiences</option>
                                                    </select>
                                                    <select id="fb_category_type_select" class="theme-form-control" data-placeholder="Select Category" style="display: none">
                                                        <option value="interests" data-type="fb"> Interest Audience</option>
                                                        <option value="behaviors" data-type="fb"> Behavior Audience</option>
                                                        <option value="demographics" data-type="fb"> Demographic Targeting</option>
                                                        <option value="jobs" data-type="fb"> Employment Targeting</option>
                                                        <option value="works" data-type="fb"> Corporate Employers</option>
                                                        <option value="schools" data-type="fb"> Education Audience</option>
                                                        <option value="majors" data-type="fb"> Educational Degrees</option>
                                                    </select>
                                                    <div class="categories_parent_block">
                                                        <div id="facebook_audiences" style="display: none">
                                                            <div class="theme-geoform-group interests">
                                                                <input type="text" id="interests_search" data-type="interests" class="theme-form-control search_input" placeholder="Serach Interest">
                                                                <div id="interests_select" class="categories_block">
                                                                    <ul>
                                                                        {foreach from=$interest_list key=k item=interest}
                                                                        <li>{$k}
                                                                            <ul>
                                                                                {foreach from=$interest key=key item=sub_cat}
                                                                                {if is_array($sub_cat)}
                                                                                <li>{$key}
                                                                                    <ul>
                                                                                        {foreach from=$sub_cat key=cat_key item=cat}
                                                                                        {if is_array($cat)}
                                                                                        <li>{$cat_key}
                                                                                            <ul>
                                                                                                {foreach from=$cat key=fin_cat_key item=final_cat}
                                                                                                {if is_array($final_cat)}
                                                                                                <li>{$fin_cat_key}
                                                                                                    <ul>
                                                                                                        {foreach from=$final_cat key=end_cat_key item=end_cat}
                                                                                                        <li value='{$end_cat}'>{$end_cat}</li>
                                                                                                        {/foreach}
                                                                                                    </ul>
                                                                                                </li>
                                                                                                {else}
                                                                                                <li value='{$final_cat}'>{$final_cat}</li>
                                                                                                {/if}
                                                                                                {/foreach}
                                                                                            </ul>
                                                                                        </li>
                                                                                        {else}
                                                                                        <li value='{$cat}'>{$cat}</li>
                                                                                        {/if}

                                                                                        {/foreach}
                                                                                    </ul>
                                                                                </li>
                                                                                {else}
                                                                                <li value='{$sub_cat}'>{$sub_cat}</li>
                                                                                {/if}
                                                                                {/foreach}
                                                                            </ul>
                                                                        </li>
                                                                        {/foreach}
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="theme-geoform-group behaviors" style="display: none">
                                                                <input type="text" id="behaviors_search" data-type="behaviors" class="theme-form-control search_input" placeholder="Serach Behavior">
                                                                <div id="behaviors_select" class="categories_block">
                                                                    <ul>
                                                                        {foreach from=$behavior_list key=k item=interest}
                                                                        <li>{$k}
                                                                            <ul>
                                                                                {foreach from=$interest key=key item=sub_cat}
                                                                                {if is_array($sub_cat)}
                                                                                <li>{$key}
                                                                                    <ul>
                                                                                        {foreach from=$sub_cat key=cat_key item=cat}
                                                                                        {if is_array($cat)}
                                                                                        <li>{$cat_key}
                                                                                            <ul>
                                                                                                {foreach from=$cat key=fin_cat_key item=final_cat}
                                                                                                {if is_array($final_cat)}
                                                                                                <li>{$fin_cat_key}
                                                                                                    <ul>
                                                                                                        {foreach from=$final_cat key=end_cat_key item=end_cat}
                                                                                                        <li value='{$end_cat}'>{$end_cat}</li>
                                                                                                        {/foreach}
                                                                                                    </ul>
                                                                                                </li>
                                                                                                {else}
                                                                                                <li value='{$final_cat}'>{$final_cat}</li>
                                                                                                {/if}
                                                                                                {/foreach}
                                                                                            </ul>
                                                                                        </li>
                                                                                        {else}
                                                                                        <li value='{$cat}'>{$cat}</li>
                                                                                        {/if}

                                                                                        {/foreach}
                                                                                    </ul>
                                                                                </li>
                                                                                {else}
                                                                                <li value='{$sub_cat}'>{$sub_cat}</li>
                                                                                {/if}
                                                                                {/foreach}
                                                                            </ul>
                                                                        </li>
                                                                        {/foreach}
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="theme-geoform-group demographics" style="display: none">
                                                                <input type="text" id="demographics_search" data-type="demographics" class="theme-form-control search_input" placeholder="Serach Demographic">
                                                                <div id="demographics_select" class="categories_block">
                                                                    <ul>
                                                                        {foreach from=$demographics_list key=k item=interest}
                                                                        <li>{$k}
                                                                            <ul>
                                                                                {foreach from=$interest key=key item=sub_cat}
                                                                                {if isset($sub_cat.name)}
                                                                                    {$value=['type'=>$k, 'name'=>$sub_cat.name, 'id'=>$sub_cat.id]}
                                                                                    <li data-value='{$value|@json_encode}'>{$sub_cat.name}</li>

                                                                                {else}
                                                                                    <li>{$key}
                                                                                        <ul>
                                                                                        {foreach from=$sub_cat item=cat}
                                                                                            {$value=['type'=>$k, 'name'=>$cat.name, 'id'=>$cat.id]}
                                                                                            <li data-value='{$value|@json_encode}'>{$cat.name}</li>
                                                                                        {/foreach}
                                                                                        </ul>
                                                                                    </li>
                                                                                {/if}
                                                                                {/foreach}
                                                                            </ul>
                                                                        </li>
                                                                        {/foreach}
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="theme-geoform-group schools" style="display: none">

                                                                <input type="text" id="schools_search" data-type="schools" class="theme-form-control search_input" placeholder="Serach Schools">
                                                                <div id="schools_select" class="categories_block">
                                                                </div>
                                                            </div>
                                                            <div class="theme-geoform-group majors" style="display: none">

                                                                <input type="text" id="majors_search" data-type="majors" class="theme-form-control search_input" placeholder="Serach Educational Degrees">
                                                                <div id="majors_select" class="categories_block">
                                                                </div>
                                                            </div>
                                                            <div class="theme-geoform-group works" style="display: none">

                                                                <input type="text" id="works_search" data-type="works" class="theme-form-control search_input" placeholder="Serach Works">
                                                                <div id="works_select" class="categories_block">
                                                                </div>
                                                            </div>
                                                            <div class="theme-geoform-group jobs" style="display: none">

                                                                <input type="text" id="jobs_search" data-type="jobs" class="theme-form-control search_input" placeholder="Serach Jobs">
                                                                <div id="jobs_select" class="categories_block">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="google_audiences" style="display: none">
                                                            <div class="theme-geoform-group affinity">
                                                                <input type="text" id="affinity_search" data-type="affinity" class="theme-form-control search_input" placeholder="Serach Affinity">
                                                                <div id="affinity_select" class="categories_block">
                                                                    <ul>
                                                                        {$category_name = $affinity_list[0].root_name}
                                                                        {$optgroup = true}
                                                                        {foreach from=$affinity_list key=k item=interest}

                                                                        {if $category_name != $interest.root_name}
                                                                        {if $has_optgroup}
                                                                    </ul>
                                                                    </li>
                                                                    {/if}
                                                                    {$optgroup = true}
                                                                    {$category_name = $interest.root_name}
                                                                    {/if}

                                                                    {if !empty($interest.down1_name)}
                                                                    {if $optgroup}
                                                                    <li label="{$interest.root_name}">{$interest.root_name}
                                                                        <ul>
                                                                            {$optgroup = false}
                                                                            {/if}
                                                                            {$has_optgroup = true}

                                                                            <li value="{$interest.down1_criterion_id}">{$interest.down1_name}</li>

                                                                            {if !empty($interest.down2_name)}

                                                                            <li value="{$interest.down2_criterion_id}" >{$interest.down1_name} -> {$interest.down2_name}</li>

                                                                            {/if}

                                                                            {else}

                                                                            <li value="{$interest.root_criterion_id}">{$interest.root_name}</li>
                                                                            {$has_optgroup = false}
                                                                            {/if}

                                                                            {/foreach}
                                                                            {if $has_optgroup}

                                                                            </li>
                                                                            {/if}
                                                                        </ul>
                                                                </div>
                                                            </div>
                                                            <div class="theme-geoform-group in_market" style="display: none">
                                                                <input type="text" id="in_market_search" data-type="in_market" class="theme-form-control search_input" placeholder="Serach In-Market">
                                                                <div id="in_market_select" class="categories_block">
                                                                    <ul>
                                                                        {foreach from=$in_market_list key=k item=interest}
                                                                        <li>{$k}
                                                                            <ul>
                                                                                {foreach from=$interest key=key item=sub_cat}
                                                                                {if is_array($sub_cat)}
                                                                                <li>{$key}
                                                                                    <ul>
                                                                                        {foreach from=$sub_cat key=cat_key item=cat}
                                                                                        {if is_array($cat)}
                                                                                        <li>{$cat_key}
                                                                                            <ul>
                                                                                                {foreach from=$cat key=fin_cat_key item=final_cat}
                                                                                                    {if is_array($final_cat)}
                                                                                                        <li>{$fin_cat_key}
                                                                                                            <ul>
                                                                                                                {foreach from=$final_cat key=end_cat_key item=end_cat}
                                                                                                                <li value='{$end_cat}'>{$end_cat_key}</li>
                                                                                                                {/foreach}
                                                                                                            </ul>
                                                                                                        </li>
                                                                                                    {else}
                                                                                                        <li value='{$final_cat}'>{$fin_cat_key}</li>
                                                                                                    {/if}
                                                                                                {/foreach}
                                                                                            </ul>
                                                                                        </li>
                                                                                        {else}
                                                                                        <li value='{$cat}'>{$cat_key}</li>
                                                                                        {/if}

                                                                                        {/foreach}
                                                                                    </ul>
                                                                                </li>
                                                                                {else}
                                                                                <li value='{$sub_cat}'>{$key}</li>
                                                                                {/if}
                                                                                {/foreach}
                                                                            </ul>
                                                                        </li>
                                                                        {/foreach}
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="yahoo_audiences" style="display: none">
                                                            <div class="theme-geoform-group yahoo_interest">
                                                                <input type="text" id="yahoo_interest_search" data-type="yahoo_interest" class="theme-form-control search_input" placeholder="Serach Interests">
                                                                <div id="yahoo_interests_select" class="categories_block">
                                                                    <ul>
                                                                        {$category_name = $yahoo_interest_list[0].root_name}
                                                                        {$optgroup = true}
                                                                        {foreach from=$yahoo_interest_list key=k item=interest}

                                                                        {if $category_name != $interest.root_name}
                                                                        {if $has_optgroup}
                                                                    </ul>
                                                                    </li>
                                                                    {/if}
                                                                    {$optgroup = true}
                                                                    {$category_name = $interest.root_name}
                                                                    {/if}

                                                                    {if !empty($interest.down1_name)}
                                                                    {if $optgroup}
                                                                    <li label="{$interest.root_name}">{$interest.root_name}
                                                                        <ul>
                                                                            {$optgroup = false}
                                                                            {/if}
                                                                            {$has_optgroup = true}

                                                                            <li value="{$interest.down1_criterion_id}">{$interest.down1_name}</li>

                                                                            {if !empty($interest.down2_name)}

                                                                            <li value="{$interest.down2_criterion_id}" >{$interest.down1_name} -> {$interest.down2_name}</li>

                                                                            {/if}

                                                                            {else}

                                                                            <li value="{$interest.root_criterion_id}">{$interest.root_name}</li>
                                                                            {$has_optgroup = false}
                                                                            {/if}

                                                                            {/foreach}
                                                                            {if $has_optgroup}

                                                                            </li>
                                                                            {/if}
                                                                        </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="is_custom_audience" id="is_custom_audience" value="0">
                                                        <input type="hidden" name="affinity" id="affinity_input" value="">
                                                        <input type="hidden" name="in_markets" id="in_market_input" value="">
                                                        <input type="hidden" name="interests" id="interests_input" value="">
                                                        <input type="hidden" name="behaviors" id="behaviors_input" value="">
                                                        <input type="hidden" name="demographics" id="demographics_input" value="">
                                                        <input type="hidden" name="schools" id="schools_input" value="">
                                                        <input type="hidden" name="jobs" id="jobs_input" value="">
                                                        <input type="hidden" name="majors" id="majors_input" value="">
                                                        <input type="hidden" name="works" id="works_input" value="">
                                                        <input type="hidden" name="yahoo_interests" id="yahoo_interests_input" value="">
                                                    </div>
                                                </div>
                                                <div id="results" class="theme-geoform-group theme-form-group col-md-4 result_block">
                                                </div>
                                                <div class="theme-align-center">
                                                    <button id="save_audience" class="theme-submit-control">Save Audience</button>
                                                    <button id="cancel_audience" class="theme-create-add-btn  theme-submit-control">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <!--                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    <div id="lookalike_audience_block" class="theme-geoform-group theme-form-group theme-submit-group theme-align-center">
                        <button data-toggle="modal" data-target="#lookalike_audience_modal" class="create_lookalike_btn theme-create-add-btn theme-submit-control">Lookalike Audience</button>
                        <div class="modal fade" id="lookalike_audience_modal" role="dialog"  data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog modal-lg">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Manage Lookalike Audience</h4>
                                    </div>
                                    <div class="modal-body text-left" style="padding: 0">
                                        <div class="theme-ad-subrow text_ads">
                                            <div class="theme-create-ad-form-wrap col-md-12">

                                                <div class="theme-geoform-group theme-tabbed-form-group them-form-group">
                                                    <h2>Lookalike Audience</h2>
                                                    <div class="theme-form-radio-group location_type">

                                                        <input name="audience_type" type="radio" value="new" class="theme-geofrom-control lookalike_radio theme-tabbed-form-control" id="new_audience" />
                                                        <label for="new_audience" class="theme-geoform-label theme-tabbed-form-label">New</label>

                                                        <input name="audience_type" type="radio" value="existing" class="theme-geofrom-control lookalike_radio theme-tabbed-form-control" id="existing_audience" />
                                                        <label for="existing_audience" class="theme-geoform-label theme-tabbed-form-label">Existing</label>

                                                        <div id="lookalike_new_block" style="display: none">
                                                            <input type="text" id="" name="lookalike_name" class="theme-form-control search_input" placeholder="lookalike Name">

                                                            <input name="lookalike_type" type="radio" value="page" class="theme-geofrom-control lookalike_radio theme-tabbed-form-control" id="page_type"  />
                                                            <label for="page_type" class="theme-geoform-label theme-tabbed-form-label">Page</label>

                                                            <input name="lookalike_type" type="radio" value="pixel" class="theme-geofrom-control lookalike_radio theme-tabbed-form-control" id="pixel_type"  />
                                                            <label for="pixel_type" class="theme-geoform-label theme-tabbed-form-label">Fb pixel</label>

                                                            <div id="lookalike_page_block" style="display: none">
                                                                <select name="lookalike_page_id" class="form-control" aria-required="true" aria-invalid="false">
                                                                    <option value="176293102495461">Select Facebook page</option>
                                                                    {foreach from=$fb_pages item=page}
                                                                    <option value="{$page.page_id}">{$page.page_name}</option>
                                                                    {/foreach}
                                                                </select>
                                                            </div>
                                                            <div id="lookalike_pixel_block" style="display: none"></div>
                                                        </div>
                                                        <div id="lookalike_existing_block" style="display: none">
                                                            <select name="lookalike_audiences[]" id="lookalike_audience_select" class="theme-form-control theme-multi-selectbox" placeholder="Select Lookalike audience" multiple>
                                                                {foreach from=$io_list item=io}
                                                                    {if $io.type != 'CUSTOM' && $io.type != 'EMAIL'}
                                                                        <option value="{$io.name}">{ucfirst($io.name)}</option>
                                                                    {/if}
                                                                {/foreach}
                                                            </select>
                                                        </div>
                                                        <input type="hidden" name="is_lookalike_audience" id="is_lookalike_audience" value="0">
                                                    </div>
                                                </div>
                                                <div class="theme-align-center">
                                                    <button id="save_lookalike_audience" class="theme-submit-control">Save Audience</button>
                                                    <button id="cancel_lookalike_audience" class="theme-create-add-btn  theme-submit-control">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <!--                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    <div id="email_audience_block" class="theme-geoform-group theme-form-group theme-submit-group theme-align-center">
                        <button data-toggle="modal" data-target="#email_audience_modal" class="create_email_btn theme-create-add-btn theme-submit-control">Custom Audience</button>
                        <div class="modal fade" id="email_audience_modal" role="dialog"  data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog modal-lg">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Manage Custom Audience</h4>
                                    </div>
                                    <div class="modal-body text-left" style="padding: 0">
                                        <div class="theme-ad-subrow text_ads">
                                            <div class="theme-create-ad-form-wrap col-md-12">
                                                <div class="theme-geoform-group theme-tabbed-form-group them-form-group">
                                                    <h2>Custom Audience</h2>
                                                    <input name="email_audience_type" type="radio" value="new" class="theme-geofrom-control lookalike_radio theme-tabbed-form-control" id="new_email_audience" />
                                                    <label for="new_email_audience" class="theme-geoform-label theme-tabbed-form-label">New</label>

                                                    <input name="email_audience_type" type="radio" value="existing" class="theme-geofrom-control lookalike_radio theme-tabbed-form-control" id="existing_email_audience" />
                                                    <label for="existing_email_audience" class="theme-geoform-label theme-tabbed-form-label">Existing</label>



                                                        <div id="email_new_block" style="display: none">
                                                            <input type="text" id="" name="custom_name" class="theme-form-control search_input" placeholder="Custom Name">
                                                            <div id="csv_uploader">
                                                                <p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
                                                            </div>
                                                            <input type="hidden" name="email_audience_file" id="email_audience_file" value="">
                                                        </div>

                                                    <div id="email_existing_block" style="display: none">
                                                        <select name="email_audiences[]" id="email_audience_select" class="theme-form-control theme-multi-selectbox" placeholder="Select Custom audience" multiple>
                                                            {foreach from=$io_list item=io}
                                                            {if $io.type == 'EMAIL'}
                                                            <option value="{$io.name}">{ucfirst($io.name)}</option>
                                                            {/if}
                                                            {/foreach}
                                                        </select>
                                                    </div>
                                                <input type="hidden" name="is_email_audience" id="is_email_audience" value="0">
                                                </div>
                                                <div class="theme-align-center">
                                                    <button id="save_email_audience" class="theme-submit-control">Save Audience</button>
                                                    <button id="cancel_email_audience" class="theme-create-add-btn  theme-submit-control">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <!--                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
                                    </div>
                                </div>

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
<style>
    .ui-autocomplete-category {
        font-weight: bold;
        padding: .2em .4em;
        margin: .8em 0 .2em;
        line-height: 1.5;
    }
</style>


