{include file="v2/sections/header.php"}
<link href="/v2/css/datetime-picker.css" rel="stylesheet" type="text/css"/>
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
                                <div class=" geoloc">
                                    <form id="theme-geo-form" action="">
                                        <div class="theme-geoform-group theme-tabbed-form-group them-form-group">
                                            <h4>Select Geo-Location Type</h4>
                                            <div class="location_type">
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
                                                <select id="geo-state" name="state[]" class="theme-form-control theme-multi-selectbox theme-control" multiple>
                                                    {if !empty($states)}
                                                    {foreach from=$states item=state}
                                                        {if $state.state|in_array:$campaign.state_array}
                                                            <option value= "{$state.state}" selected>{ucfirst($state.name)}</option>
                                                        {else}
                                                            <option value= "{$state.state}">{ucfirst($state.name)}</option>
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
                                                    <input name="zip" type="text" value="" placeholder="Enter your postal code" class="form-control" />
                                                </div>
                                                <div class="col-md-6">
                                                    <select id="geo-postal-radius" name="radius" class="form-control radius">
                                                        <option value="">Select Radius</option>
                                                        <option value="10">10 Miles</option>
                                                        <option value="15">15 Miles</option>
                                                        <option value="25">25 Miles</option>
                                                        <option value="50">50 Miles</option>
                                                        {if $campaign.campaign_type!="FB-PAGE-LIKE"}
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
                                                <input type="text" id="edit_start_date_datepicker" name="campaign_start_datetime"
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
                            </div>

                            <div class="col-md-6 col-sm-12 col-xs-12">
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
                                <div class="addBudget">

                                    <form id="theme-budget-form" class="theme-geoform-group">
                                        <div  class="form-group">
                                            <h4>Add Additional Budget?</h4>
                                            <p>By adding additional budget to Campaign Name (IO#: {$campaign.io}), you will receive additional views and clicks.</p>

                                            {if $user.is_billing_type == 'PERCENTAGE'}

                                                {if empty($campaign.max_budget)}

                                                    {if !empty($campaign.max_clicks)}

                                                        {$percent = $campaign.total_clicks_count/$campaign.max_clicks*100}
                                                        {$cost = $campaign.max_clicks*$user.display_click}

                                                        {if $percent<100}
                                                            <p>Total Budget Allocation: $ {$cost|string_format:"%.2f"}</p>
                                                            <p>Total Budget Left: $ {($cost*$percent/100)|string_format:"%.2f"}</p>
                                                        {else}
                                                            <p>Total Budget Allocation: $ {$cost|string_format:"%.2f"}</p>
                                                            <p>Total Budget Spent: $ {$cost|string_format:"%.2f"}</p>
                                                        {/if}
                                                    {else if !empty($campaign.max_impressions)}

                                                        {$percent = $campaign.total_impressions_count/$campaign.max_impressions*100}
                                                        {$cost = $campaign.max_impressions*$user.display_imp/1000}
                                                        {if $percent<100}
                                                            <p>Total Budget Allocation: $ {$cost|string_format:"%.2f"}</p>
                                                            <p>Total Budget Left: $ {($cost*$percent/100)|string_format:"%.2f"}</p>
                                                        {else}
                                                            <p>Total Budget Allocation: $ {$cost|string_format:"%.2f"}</p>
                                                            <p>Total Budget Spent: $ {$cost|string_format:"%.2f"}</p>
                                                        {/if}

                                                    {else}
                                                        <p>Total Budget Allocation: No budget Allocated</p>
                                                        <p>Total Budget Spent: $ {$campaign.cost|string_format:"%.2f"}</p>
                                                    {/if}
                                                {else}
                                                    {$cost = $campaign.percentage_max_budget - $campaign.cost}

                                                    {if ($cost>0)}
                                                        {$percent_cost = $cost*100/$campaign.percentage_max_budget}
                                                        <p>Total Budget Allocation: $ {$campaign.max_budget|string_format:"%.2f"}</p>
                                                        <p>Total Budget Left: $ {($campaign.max_budget*$percent_cost/100)|string_format:"%.2f"}</p>
                                                    {else}
                                                        <p>Total Budget Allocation: $ {$campaign.max_budget|string_format:"%.2f"}</p>
                                                        <p>Total Budget Spent: $ {$campaign.max_budget|string_format:"%.2f"}</p>
                                                    {/if}
                                                {/if}

                                            {else}

                                                {if !empty($campaign.max_budget)}
                                                    {$cost = $campaign.percentage_max_budget - $campaign.cost}
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
                                                    {$cost = $campaign.max_impressions*$user[$tier]}
                                                    {if $percent<100}
                                                        <p>Total Budget Allocation: $ {$cost|string_format:"%.2f"}</p>
                                                        <p>Total Budget Left: $ {($cost*$percent/100)|string_format:"%.2f"}</p>
                                                    {else}
                                                        <p>Total Budget Allocation: $ {$cost|string_format:"%.2f"}</p>
                                                        <p>Total Budget Spent: $ {$cost|string_format:"%.2f"}</p>
                                                    {/if}

                                                {else}
                                                    <p>Total Budget Allocation: No budget Allocated</p>
                                                    <p>Total Budget Spent: $ {$campaign.cost|string_format:"%.2f"}</p>
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
                                {if $campaign.campaign_type!="FB-PAGE-LIKE"}
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

                                                            </form>
                                                    </div>
                                                </div>

                                            <div class="modal-footer">
<!--                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
                                                <button type="button" class="btn btn-default" id="add_new_keyword">Add</button>
                                                <button type="submit"  class="btn btn-primary">Save</button>
                                                    <div class="keyword_list_block" >

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
                                                <div class="theme-geoform-group theme-form-group">
                                                    <div class="keyword_text">
                                                        <!--                                                                        keyword : Broad match <br>-->
                                                        <!--                                                                        +keyword : Broad match modifire <br>-->
                                                        <!--                                                                        "keyword" : Phrase match <br>-->
                                                        <!--                                                                        [keyword] : Exact match <br>-->
                                                        <!--                                                                        -keyword : Negative match-->
                                                    </div>
                                                </div>
                                                <input type="hidden" name="campaign_id" value="{$campaign.id}">
                                                    <!--                                                                    <input type="reset" value="Cancel" class="theme-cancel-btn theme-submit-control" />-->


                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                    {/if}
                                </div>
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
<script src="/v2/js/iscript.js"></script>
<script src="/v2/js/campaign-edit.js"></script>
<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script>
    $(document).ready(function() {
        var campaign_geotype = '{if !empty($campaign.geotype)}{$campaign.geotype}{/if}';
        var campaign_country = '{if !empty($campaign.country)}{$campaign.country}{/if}';
        var campaign_zip = '{if !empty($campaign.zip)}{$campaign.zip|replace:",":" "}{/if}';
        var campaign_radius = '{if !empty($campaign.radius)}{$campaign.radius}{/if}';
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
                    if ($(this).val() == campaign_country) {
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
        })

    })
</script>
</body>
</html>