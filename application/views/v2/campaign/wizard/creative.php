<fieldset  class="display_none">
    <div class="theme-tab-content theme-report-tab-content">
        <div class="theme-textad-section">
            <h1>Create your text Ads</h1>
            <div class="theme-ad-banner-row theme-ad-creative-row">
                <div class="theme-display-table theme-no-gutter theme-no-gutter">
                    <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-two-col theme-table-top-cell">
                        <div class="theme-ad-subrow theme-ad-banner-subrow">
                            <h1 class="theme-banner-row-title"><span>Your Text Ad</span></h1>
                            <div id="examplte_show_div">
                                <div class="theme-ad-banner-content theme-display-table theme-no-gutter">
                                    <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                        <figure>
                                            <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                         </figure>
                                    </div>
                                    <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                        <div class="theme-ad-content">
                                            <h2><a href="" data-type="title">Title</a></h2>
                                            <p data-type="description">Description</p>
                                            <p class="theme-ad-url-line"><a href="" data-type="display_url">Display URL</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="theme-ad-subrow theme-ad-banner-subrow text_ads">
                            <h1 class="theme-banner-row-title"><span>Creative listing</span></h1>
                            <div class="theme-create-ad-form-wrap">
                                <div class="theme-geoform-group theme-form-group theme-inline-group">
                                    <label class="theme-inline-label theme-light-weight">Title:</label>
                                    <input name="title" maxlength="25"  type="text" value="" placeholder="Enter the title of your ad" class="theme-geoform-control theme-form-control autochange" data-type="title"/>
                                    <span style="font-size:12px; color: #999898; padding-left:143px;"> Character Left: <span class="charecter_count" maxlength="25">25</span> </span>
                                </div>
                                <div class="theme-geoform-group theme-form-group theme-inline-group">
                                    <label class="theme-inline-label theme-light-weight">Description 1</label>
                                    <textarea name="description_1" maxlength="25" placeholder="Enter the desc of your ad" class="theme-geoform-control theme-form-control autochange" data-type="description" id="desc_1"></textarea>
                                    <span style="font-size:12px; color: #999898; padding-left:143px;"> Character Left: <span class="charecter_count" maxlength="25">25</span> </span>
                                </div>
                                <div class="theme-geoform-group theme-form-group theme-inline-group">
                                    <label class="theme-inline-label theme-light-weight">Description 2</label>
                                    <textarea name="description_2" maxlength="25" placeholder="Enter the desc of your ad" class="theme-geoform-control theme-form-control autochange" data-type="description" id="desc_2"></textarea>
                                    <span style="font-size:12px; color: #999898; padding-left:143px;"> Character Left: <span class="charecter_count" maxlength="25">25</span> </span>
                                </div>
                                <div class="theme-geoform-group theme-form-group theme-inline-group">
                                    <label class="theme-inline-label theme-light-weight">Display Url:</label>
                                    <input name="display_url" type="url" value="" placeholder="Enter the display url of your ad" class="theme-geoform-control theme-form-control autochange" maxlength="128" data-type="display_url">/>
                                    <span style="font-size:12px; color: #999898; padding-left:143px;"> Character Left: <span class="charecter_count" maxlength="128">128</span> </span>
                                </div>
                                <div class="theme-geoform-group theme-form-group theme-inline-group">
                                    <label class="theme-inline-label theme-light-weight">Destination URL:</label>
                                    <input name="destination_url" type="url" value="" placeholder="Enter the url of your ad" class="theme-geoform-control theme-form-control" maxlength="2048" />
                                    <span style="font-size:12px; color: #999898; padding-left:143px;"> Character Left: <span class="charecter_count" maxlength="2048">2048</span> </span>
                                </div>
                                <div class="theme-geoform-group theme-form-group theme-inline-group">
                                    <div id="text_ad_upload"></div>
                                </div>
                                <div class="theme-geoform-group theme-form-group theme-submit-group theme-align-center">
                                    <input type="button" value="Create New Ad" class="theme-create-add-btn theme-submit-control" id="create_new_add">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-two-col theme-sidebar-ad-col theme-table-top-cell">
                        <div class="theme-ad-subrow theme-ad-banner-subrow scroll">
                            <h1 class="theme-banner-row-title"><span>text Ads creatives</span></h1>
                            <div class="theme-scrollable-ad-wrap theme-nicescroll-holder" id="text_ad_div">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="theme-geoform-group theme-form-group theme-submit-group theme-align-center">
                    <a href="javascript:" class="theme-btn theme-back-btn">Back</a>
                    <a href="javascript:" class="btn_next_step btn_continue btn_last_next" >CONTINUE</a>
                </div>
            </div>
        </div>
        <div class="form-for-email-pays-campaign" style="display: none">
            <div class="theme-ad-subrow theme-ad-banner-subrow">
                <!--<h1 class="theme-banner-row-title"><span>Creative listing</span></h1>-->
                <div class="theme-create-ad-form-wrap">
                    <div class="theme-geoform-group theme-form-group theme-inline-group large">
                        <div class="text_block" >
                            <p>Cut & Paste Your HTML Email Creative</p>
                            <textarea placeholder="" class="theme-geoform-control theme-form-control" id="message_result"></textarea>
                        </div>
                        <div class="tab-pane" id="heatmap_creative">
                            <iframe style="margin:0; padding:0; border:1px; width:99%; height:625px; position:relative; overflow-x:hidden; overflow-y:scroll;" id="heatmap_creative_iframe" class="hidden"></iframe>
                        </div>
                        <div class="theme-geoform-group theme-form-group theme-submit-group theme-align-right new-group">
                            <a id="show_hidden_section" href="#" class="theme-create-add-btn theme-submit-control">NEXT</a>
                        </div>
                    </div>
                    <div id="theme-campagin-hidden-section" class="theme-campagin-hidden-section">
                        <div class="theme-geoform-group theme-form-group theme-inline-group single-line-form">
                            <label class="theme-inline-label theme-light-weight single-line-form-lable">Total Records:</label>
                            <input name="total_records" type="text" value="" placeholder="" class="theme-geoform-control theme-form-control master-properties" id="total_records"/>
                        </div>
                        <div class="theme-geoform-group theme-form-group theme-inline-group single-line-form">
                            <label class="theme-inline-label theme-light-weight single-line-form-lable">% Opens:</label>
                            <input name="percentage_opens" type="text" value="10" placeholder="" class="theme-geoform-control theme-form-control master-properties" id="opens" />
                        </div>
                        <div class="theme-geoform-group theme-form-group theme-inline-group single-line-form">
                            <label class="theme-inline-label theme-light-weight single-line-form-lable">% Clicks:</label>
                            <input name="percentage_clicks" type="text" value="2" placeholder="" class="theme-geoform-control theme-form-control master-properties" id="clicks"/>
                        </div>
                        <div class="theme-geoform-group theme-form-group theme-inline-group single-line-form">
                            <label class="theme-inline-label theme-light-weight single-line-form-lable">% Bounce:</label>
                            <input name="percentage_bounce" type="text" value="0.01" placeholder="" class="theme-geoform-control theme-form-control master-properties" id="bounce"/>
                        </div>
                        <div class="theme-geoform-group theme-form-group theme-inline-group single-line-form">
                            <label class="theme-inline-label theme-light-weight single-line-form-lable">Total Clicks:</label>
                            <input name="total_clicks" type="text" value="" placeholder="" class="theme-geoform-control theme-form-control" id="total_clicks" />
                        </div>
                        <div class="theme-geoform-group theme-form-group theme-inline-group single-line-form">
                            <label class="theme-inline-label theme-light-weight single-line-form-lable">Total Opens:</label>
                            <input name="total_opens" type="text" value="" placeholder="" class="theme-geoform-control theme-form-control" id="total_opens" />
                        </div>
                        <div class="theme-geoform-group theme-form-group theme-inline-group single-line-form">
                            <label class="theme-inline-label theme-light-weight single-line-form-lable">Total Bounce:</label>
                            <input name="total_bounces" type="text" value="" placeholder="" class="theme-geoform-control theme-form-control" id="total_bounces"/>
                        </div>
                        <hr/>
                        <div class="alert-danger cs_alert hidden" id="cs_alert_hidden">Wrong value: insert value >= 100%.</div>
                        <div class="clearfix" ></div>
                        <div id="table_ppc_div">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th align="right">Totals: </th>
                                        <th><span class="user_clicks">0</span></th>
                                        <th><span class="user_percentage" id="user_percentage_set">0</span>%</th>
                                    </tr>
                                    <tr>
                                        <th>Destination URL</th>
                                        <th>Click Count</th>
                                        <th>%</th>
                                    </tr>
                                </thead>
                                <tbody id="heatmap_links"></tbody>
                                <tfooter>
                                    <tr>
                                        <th align="right">Totals: </th>
                                        <th><span class="user_clicks">0</span></th>
                                        <th><span class="user_percentage">0</span>%</th>
                                    </tr>
                                </tfooter>
                            </table>
                        </div>

                        <div class="theme-form-group theme-submit-group theme-align-center">
                            <a href="javascript:" class="btn_previous_step" >BACK</a>
                            <a href="javascript:" class="btn_next_step btn_continue btn_last_next" >CONTINUE</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="theme-imagead-section">
            <h1 class="create_your_video_ads">Create your Image ads</h1>
            <div class="theme-ad-banner-row theme-ad-creative-row">
                <div class="theme-display-table theme-no-gutter theme-no-gutter">
                    <div id="theme-file-uploader" class="theme-lg-6 theme-sm-6 theme-xs-12 theme-two-col theme-table-top-cell">
                    <h4 class="rich_media_step" style="display: none">Step 1:</h4>
                        <div id="image_upload_block" >
                            <div id="uploader">
                                <p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
                            </div>
                            <div class='facebook_allowed_text'>Ad images aren't allowed to include more than 20% text</div>
                            <button id="btn_facebook_ad" class="btn btn-success" style="display: none;">Create New Ad</button>
                            <div class='facebook_form' id='facebook_form' style="font-size: 16px;">

                                <label class="theme-inline-label add_margin">Form Type</label>
                                <select id="form_type" name="form[form_type]" class="form-control" aria-required="true" aria-invalid="false">
                                    <option value="new">New Form</option>
                                    <option value="existing">Existing Form</option>
                                </select>

                                <div id="existing_form_block" style="display: none;">
                                    <label class="theme-inline-label add_margin">Select Form</label>
                                    <select id="form_id" name="form[form_id]" class="form-control" aria-required="true" aria-invalid="false">
                                        <option value="">Select Facebook Form</option>
                                        {foreach from=$form_list item=form}
                                            <option data-form_json='{$form|@json_encode|replace:"'":"\'"}' value="{$form.id}">{$form.name}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div id="new_form_block">
                                    <label class="theme-inline-label add_margin">Lead Type</label>
                                    <select id="fb_form_lead_type" name="form[lead_type]" class="form-control" aria-required="true" aria-invalid="false">
                                        <option value="real_estate">Real Estate</option>
                                        <option value="automotive">Automotive</option>
                                        <option value="other">Other</option>
                                    </select>

                                    <label class="theme-inline-label add_margin">Form Name</label>
                                    <input class="form-control" type="text" name="form[name]" required="required" maxlength="45">
                                    <label class="theme-inline-label add_margin">Headline</label>
                                    <input class="form-control" type="text" name="form[headline]" required="required" maxlength="45">
                                    <label class="theme-inline-label add_margin">Lead Type</label>
                                    <select id="fb_form_context_type" name="form[context_type]" class="form-control" aria-required="true" aria-invalid="false">
                                        <option value="bullets">Bullets</option>
                                        <option value="paragraph">Paragraph</option>
                                    </select>
                                    <div id="bullets_block">
                                        <label class="theme-inline-label add_margin">Option 1</label>
                                        <input class="form-control" data-bullet="bullet_1" type="text" name="form[bullets][]" required="required" maxlength="80">
                                        <label class="theme-inline-label add_margin">Option 2</label>
                                        <input class="form-control" data-bullet="bullet_2" type="text" name="form[bullets][]" maxlength="80">
                                        <label class="theme-inline-label add_margin">Option 3</label>
                                        <input class="form-control" data-bullet="bullet_3" type="text" name="form[bullets][]" maxlength="80">
                                        <label class="theme-inline-label add_margin">Option 5</label>
                                        <input class="form-control" data-bullet="bullet_4" type="text" name="form[bullets][]" maxlength="80">
                                        <label class="theme-inline-label add_margin">Option 5</label>
                                        <input class="form-control" data-bullet="bullet_5" type="text" name="form[bullets][]" maxlength="80">
                                    </div>
                                    <div id="paragraph_block" style="display: none;">
                                        <label class="theme-inline-label add_margin">Form Description</label>
                                        <textarea class="form-control" name="form[paragraph]" required="required" ></textarea>
                                    </div>
                                    <label class="theme-inline-label add_margin">Button Text</label>
                                    <input class="form-control" type="text" name="form[button_text]" required="required" maxlength="30">

                                    <label class="theme-inline-label add_margin">Where to send lead info?</label>
                                    <select id="fb_form_export_type" name="form[export_type]" class="form-control" aria-required="true" aria-invalid="false">
                                        <option value="email_address">Email Address</option>
                                        <option value="export_as_csv">Export as CSV</option>
                                    </select>
                                    <div id="email_export_block">
                                        <label class="theme-inline-label add_margin">Email Address</label>
                                        <input class="form-control" type="text" name="form[email]" required="required">
                                        <label class="theme-inline-label add_margin">How to send lead info?</label>
                                        <select id="fb_form_email_type" name="form[email_type]" class="form-control" aria-required="true" aria-invalid="false">
                                            <option value="immediately">Send Immediately As Received</option>
                                            <option value="daily">Send Once Per Day</option>
                                        </select>
                                    </div>
                                    <div class="theme-form-group theme-geoform-group">
                                        <input name="form[is_phone_number]" type="checkbox" value="Y" class="theme-tabbed-form-control" id="is_phone_number" />
                                        <label class="theme-tabbed-form-label add_margin" for="is_phone_number"><b>Include Phone Number on Form</b></label>
                                    </div>
                                    <div id="form_image_uploader">
                                        <p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
                                    </div>
                                    <input type="hidden" name="is_fb_form" id="is_fb_form" value="0">
                                    <input type="hidden" name="form[image]" id="form_image" value="">

                                </div>
                                <div class="theme-align-center">
                                    <button id="save_fb_form" class="btn btn-success">Save Form</button>
                                </div>
                            </div>

                            <div class='facebook_page_like_page_id'>

                                <!--<label class="lbl_page_id">Page ID</label>-->
<!--                            <input class="form-control page_id aaaaaaaaaa" type="number" name="fb_page_id" required="required" minlength="5">-->
                                <input class="form-control " type="hidden" name="fb_page_like" value="fb_page_like" >

                                <select id="fb_page_select" name="fb_page_id" class="form-control" aria-required="true" aria-invalid="false">
                                    <option value="176293102495461">Select Facebook page</option>
                                {foreach from=$fb_pages item=page}
                                    <option value="{$page.page_id}">{$page.page_name}</option>
                                {/foreach}
<!--                                    <input class="form-control page_id" type="number" name="fb_page_id" required="required" minlength="5">-->
                                    <input class="form-control " type="hidden" name="fb_page_like" value="fb_page_like" >
                                </select>
                                <label class="lbl_page_id">Address</label>
                                <input class="form-control address" type="text" name="address" required="required" minlength="5">
                                <input type="hidden" value="" name="lat" id="hidden-lat">
                                <input type="hidden" value="" name="lng" id="hidden-lng">
                            </div>
                            <div id="upload-result" class="alert-message"></div>
                            <div id="info_block_for_carusel" class="info_block_for_carusel alert-message no_display">
                                <p class="instruction_p">Please organize your carousel ads by dragging and dropping. Top is the most left, bottom is most right for the carousel.</p>
                            </div>
                        </div>
                        <h4 class="rich_media_step" style="display: none">Step 2:</h4>
                        <div id="theme-from-rich-media" class="theme-geoform-group" style="display: none">
                            <label class="theme-inline-label add_margin">Enter Your Question</label>
                            <textarea class="form-control" name="question[]" id="rm_question"></textarea>
                            <label class="theme-inline-label add_margin">Choose Answer Input Type</label><br>
                            <input type="button" name="chek_type" value="Checkbox" class="btn btn_richmed" id="rich_media_checkbox">
                            <input type="button" name="radio_type" value="Radio" class="btn btn_richmed" id="rich_media_radio">
                            <input type="hidden" value="" id="inp_type">
                            <input type="button" value="+" class="btn btn_richmed" id="rich_media_checkbox_plus" style="display: none">
                            <input type="button" value="+" class="btn btn_richmed" id="rich_media_radio_plus" style="display: none">
                            <div id="rich_media_inp"></div>
                            <button id="save_rich_ad" class="btn btn-success">Save Form</button>
                        </div>
                        <div id="yahoo_carousek_block" class="yahoo_carousek_info" style="display: none;" >
                            <p class="instruction_yahoo_p">
                                In yahoo carousel ads min limit is 3 and max limit 5.
                            </p>
                        </div>
                        <div id="airpush_select_block" class="airpush_select_block no_display" >
                            <p class="instruction_p">For rich media ads, please paste in the provided Javascript and/or HTML for your associated ad.  We support JS and iFrame HTML5 based ads.<br>
                                If you have any questions, please contact support.
                            </p>
                            <button id="btn_airpush_ad" class="btn btn-success" >Create New Ad</button>
                            <div id="image_type_select" >
                                <select  class="form-control airpush_image_select "  >
                                    <option class="books_and_reference" >books_and_reference</option>
                                    <option class="business" >business</option>
                                    <option class="comics" >comics</option>
                                    <option class="communications" >communications</option>
                                    <option class="contests" >contests</option>
                                    <option class="education" >education</option>
                                    <option class="entertainment" >entertainment</option>
                                    <option class="finance" >finance</option>
                                    <option class="games" >games</option>
                                    <option class="health_and_fitness" >health_and_fitness</option>
                                    <option class="libraries_and_demo" >libraries_and_demo</option>
                                    <option class="lifestyle" >lifestyle</option>
                                    <option class="media_and_video" >media_and_video</option>
                                    <option class="medical" >medical</option>
                                    <option class="music_and_audio" >music_and_audio</option>
                                    <option class="news_and_magazine" >news_and_magazine</option>
                                    <option class="personalization" >personalization</option>
                                    <option class="photography" >photography</option>
                                    <option class="productivity" >productivity</option>
                                    <option class="ringtones" >ringtones</option>
                                    <option class="shopping" >shopping</option>
                                    <option class="social" >social</option>
                                    <option class="sports" >sports</option>
                                    <option class="tools" >tools</option>
                                    <option class="travel_and_icon" >travel_and_icon</option>
                                </select>
                            </div>
                        </div>
                        <div class="no_display" id="yahoo_app_url">
                            <input type="text" name="app_url" class="form-control theme-imagead-url-field app_url" placeholder="App URL - http://..." />
                        </div>
                    </div>
                    <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-two-col theme-sidebar-ad-col theme-table-top-cell">
                        <div class="theme-ad-subrow theme-ad-banner-subrow theme-imagead-banner-subrow">
                            <h1 class="theme-banner-row-title"><span>Uploaded Creative</span></h1>
                            <div id="ads_container" class="theme-scrollable-ad-wrap theme-nicescroll-holder scroll"></div>
                        </div>
                        <div id="form_preview_block" class="theme-ad-subrow theme-ad-banner-subrow theme-imagead-banner-subrow" style="display: none;">
                            <h1 class="theme-banner-row-title"><span>Form Preview</span></h1>
                            <div id="form_preview" class="theme-scrollable-ad-wrap theme-nicescroll-holder">
                                <div class="fixed-box">
                                    <div id="cover" style="width: 465px">
                                        <img id="cover_img" src="" width="100%" height="237" alt="">
                                    </div>
                                    <div id="smallPic">
                                        <img src="/v2/images/gray_square.jpg" width="90" height="90">
                                    </div>
                                    <p class="page_name_preview default-text text-center"></p>

                                    <h2 class="text-center form_heading_preview" style="text-align: center;">Heading comes here.</h2>
                                    <p class="text text-center paragraph_preview" style="display: none">

                                    </p>
                                    <ul class="list-unstyled text-center bullets_preview">
                                        <li class="bullet_1"></li>
                                        <li class="bullet_2"></li>
                                        <li class="bullet_3"></li>
                                        <li class="bullet_4"></li>
                                        <li class="bullet_5"></li>
                                    </ul>
                                    <a class="btn btn-primary button_preview" href="">Button</a>
                                </div>
                            </div>
                            <div id="existing_form_preview_div" style="display: none;" class="theme-scrollable-ad-wrap"></div>
                        </div>
                    </div>
                </div>
                <div class="theme-form-group theme-submit-group theme-align-center">
                    <a href="javascript:" class="btn_previous_step" >BACK</a>
                    <a href="javascript:" class="btn_next_step btn_continue btn_last_next" >CONTINUE</a>
                </div>
            </div>
        </div>
    </div>
</fieldset>
<div class="modal fade" id="image_show_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content text-center">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <img id="modal_img" src="" />
            </div>
        </div>
    </div>
</div>
