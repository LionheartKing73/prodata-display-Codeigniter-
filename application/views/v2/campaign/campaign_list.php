{include file="v2/sections/header.php"}
<link href="/v2/css/datetime-picker.css" rel="stylesheet" type="text/css"/>
<div class="theme-report-campaigne-row-wrap">
    <div class="theme-container r-container">

        <div class="theme-report-campaigne-schedule-row">

            <div class="theme-report-campaigne-row-title">
                <div class="row" >
                    <div class="col-sm-6 col-xs-12 active-campaign"><h1></h1></div>
                    <div class="col-sm-6 col-xs-12 createNewCampaign text-right" >
<!--                        <div class="form-group theme-submit-group">-->
<!--                            <a class="theme-submit-control"> Create new campaign</a>-->
<!--                        </div>-->
                        {if $user_type != 'viewer' && $user.create_campaign != 'N'}
                        <a class="btn btn-success btn-create-campaign" href="/v2/campaign/new_campaign">
                            <img src="/v2/images/icons/report_icon.png" />
                            Create new campaign
                        </a>
                        {/if}
                    </div>
                </div>
            </div>
            <div class="theme-report-campaigne-form-wrap">
                <form id="theme-report-schedule-form" class="theme-report-schedule-form">
                    <div class="theme-display-table theme-form-row">
                        <div class="col-md-4 form-group theme-form-group theme-table-middle-cell">
                            <label for="search-keyword" class="theme-control-label">Search Criteria</label>
                            <input type="text" name="name" value="{if !empty($params.name)}{$params.name}{/if}" placeholder="I/O or Name" class="fillone form-control theme-form-control" id="search-keyword" />
                        </div>
                        <div class="col-md-4 form-group theme-form-group theme-table-middle-cell">
                            <label for="start-dae" class="theme-control-label">Start Date</label>
                            <input type="text" id="start_date_datepicker1" name="campaign_start_datetime" value="{if !empty($params.campaign_start_datetime)}{$params.campaign_start_datetime|date_format:"%Y/%m/%d %H:%M"}{/if}" placeholder="2014/20/12" class=" fillone form-control theme-date-picker theme-form-control" id="start-date" />
                        </div>
                        <div class="col-md-4 form-group theme-form-group theme-table-middle-cell">
                            <label for="end-date" class="theme-control-label">End Date</label>
                            <input type="text" id="end_date_datepicker1"  name="campaign_end_datetime"  value="{if !empty($params.campaign_end_datetime)}{$params.campaign_end_datetime|date_format:"%Y/%m/%d 23:59"}{/if}" placeholder="2014/20/18" class="fillone form-control theme-date-picker theme-form-control" id="end" />
                        </div>
                        <div class="col-md-4 form-group theme-form-group theme-table-middle-cell">
                            <label for="search-keyword" class="theme-control-label">Campaign Type</label>
                            <select name="campaign_type" placeholder="Campaign Type" class="fillone form-control theme-form-control" >
                                <option value="">ALL</option>
                                {if $user['is_email'] == "Y" || $user['is_admin']}
                                <option value="NO_EMAIL" >ALL - No EMAIL</option>
                                <option value="EMAIL" >Email PPC</option>
                                {/if}
                                <option value="TEXTAD" >TEXTAD</option>
                                <option value="DISPLAY" >DISPLAY</option>
                                <option value="DISPLAY-RETARGET" >DISPLAY-RETARGET</option>
                                <option value="THIRD-PARTY/AD-TRACK" >THIRD-PARTY/AD-TRACK</option>
                                <option value="RICH-MEDIA-SURVEY" >RICH-MEDIA-SURVEY</option>
                                <option value="FB-MOBILE-NEWS-FEED" >FB-MOBILE-NEWS-FEED</option>
                                <option value="FB-DESKTOP-RIGHT-COLUMN" >FB-DESKTOP-RIGHT-COLUMN</option>
                                <option value="FB-DESKTOP-NEWS-FEED" >FB-DESKTOP-NEWS-FEED</option>
                                <option value="FB-PAGE-LIKE" >FB-PAGE-LIKE</option>
                                <option value="FB-VIDEO-VIEWS" >FB-VIDEO-VIEWS</option>
                                <option value="FB-VIDEO-CLICKS" >FB-VIDEO-CLICKS</option>
                                <option value="FB-LOCAL-AWARENESS" >FB-LOCAL-AWARENESS</option>
                                <option value="FB-PROMOTE-EVENT" >FB-PROMOTE-EVENT</option>
                                <option value="FB-MOBILE-APP-INSTALLS" >FB-MOBILE-APP-INSTALLS</option>
                                <option value="IN_APP" >IN_APP</option>
                                <option value="OVERLAY_AD" >OVERLAY_AD</option>
                                <option value="PUSH_CLICK_TO_CALL" >PUSH_CLICK_TO_CALL</option>
                                <option value="RICH_MEDIA_INTERSTITIAL" >RICH_MEDIA_INTERSTITIAL</option>
                                <option value="DIALOG_CLICK_TO_CALL" >DIALOG_CLICK_TO_CALL</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group theme-form-group theme-table-middle-cell">
                            <label for="search-" class="theme-control-label">Campaign Status</label>
                            <select name="campaign_status" placeholder="Campaign Status" class="fillone form-control theme-form-control" >
                                <option value="ACTIVE">Active</option>
                                <option value="SCHEDULED">Scheduled</option>
                                <option value="PAUSED">Paused</option>
                                <option value="REMOVED">Removed</option>
                                <option value="DISAPPROVED">Disapproved</option>
                                <option value="COMPLETED">Completed</option>
                            </select>
                        </div>

                    </div>

                    <div class="form-group theme-submit-group">
                        <input id="submit_form" type="button" value="Update" class="theme-submit-control campaign-list-update" />
                    </div>

                </form>
            </div>
            <div id="content_for_table">
                {include file="v2/campaign/campaign_list/content.php"}
            </div>
        </div>

    </div>
</div>

            <!-- #Theme Report Page Header -->

        </section>
    </main>

    <!-- #Theme Report Page Structure -->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/v2/js/jquery-2.0.3.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/v2/js/bootstrap.min.js"></script>
    <!-- Include all the complipled plugins (below) need to creat charts/pie/maps, or include individual files as needed -->
<!--    <script src="/v2/js/bootstrap-datepicker.js"></script>-->
    <script src="/v2/js/jquery.tablesorter.min.js"></script>
    <!-- ikentoo custom script -->
    <script src="/v2/js/iscript.js"></script>
    <script src="/v2/js/highcharts.js"></script>
    <script src="/v2/js/jquery.validate.min.js"></script>
<!--    <script src="http://cdn.jsdelivr.net/jquery.validation/1.14.0/additional-methods.min.js"></script>-->
    <script src="/v2/js/loader.js"></script>
    <script src="/v2/js/campaign-list.js"></script>
    <script src="/v2/js/datetime-picker.jquery.js"></script>
    <script>
        var campaign_type = '{if !empty($params.campaign_type)}{$params.campaign_type}{/if}';
        if(campaign_type) {
            $("[name='campaign_type'] option").each(function(){
                if($(this).val()==campaign_type){
                    $(this).attr("selected","selected");
                }
            });
        }
        var campaign_status =  '{if !empty($params.campaign_status)}{$params.campaign_status}{/if}';
//        var campaign_status = $('select[name="campaign_status"] option:selected').val();
        if(campaign_status) {
            console.log(campaign_status);
            $("[name='campaign_status'] option").each(function(){
                if($(this).val()==campaign_status){
                    $(this).attr("selected","selected");
                }
            });
        }
        $('[data-toggle="popover"]').popover({
            trigger: "hover",
            html: true
        });
    </script>
{include file="v2/sections/footer.php"}
</body>
</html>
