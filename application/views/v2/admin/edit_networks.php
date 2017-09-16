{include file="v2/sections/header.php"}
<div class="theme-report-row-wrap">
    <div class="theme-container container-fluid network_edit_container">
        <div class="row network_add_row" >
            <form id="add_network_form">
                <input type="hidden" value="{$network_user}" name="user_id" />
                <div class="col-sm-4" >
                    <select name="network" class="form-control" >
                        {foreach from=$networks item=network}
                        <option value="{$network.id}">{$network.name}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="col-sm-4" >
                    <select name="campaign_type" class="form-control" >
                        <option value="TEXTAD" >TEXTAD</option>
                        <option value="DISPLAY" >DISPLAY</option>
                        <option value="DISPLAY-RETARGET" >DISPLAY-RETARGET</option>
                        <option value="THIRD-PARTY-AD-TRACK" >THIRD-PARTY-AD-TRACK</option>
                        <option value="RICH_MEDIA_SURVEY" >RICH-MEDIA-SURVEY</option>
                        <option value="FB-MOBILE-NEWS-FEED" >FB-MOBILE-NEWS-FEED</option>
                        <option value="FB-DESKTOP-RIGHT-COLUMN" >FB-DESKTOP-RIGHT-COLUMN</option>
                        <option value="FB-DESKTOP-NEWS-FEED" >FB-DESKTOP-NEWS-FEED</option>
                        <option value="FB-PAGE-LIKE" >FB-PAGE-LIKE</option>
                        <option value="FB-VIDEO-VIEWS" >FB-VIDEO-VIEWS</option>
                        <option value="FB-VIDEO-CLICKS" >FB-VIDEO-CLICKS</option>
                        <option value="APPWALL" >APPWALL</option>
                        <option value="LANDING_PAGE" >LANDING_PAGE</option>
                        <option value="IN_APP" >IN_APP</option>
                        <option value="OVERLAY_AD" >OVERLAY_AD</option>
                        <option value="PUSH_CLICK_TO_CALL" >PUSH_CLICK_TO_CALL</option>
                        <option value="ABSTRACT_BANNER_LARGE" >ABSTRACT_BANNER_LARGE</option>
                        <option value="ABSTRACT_BANNER_SMALL" >ABSTRACT_BANNER_SMALL</option>
                        <option value="ABSTRACT_BANNER_LARGE_CC" >ABSTRACT_BANNER_LARGE_CC</option>
                        <option value="ABSTRACT_BANNER_LARGE_CM" >ABSTRACT_BANNER_LARGE_CM</option>
                        <option value="ABSTRACT_BANNER_SMALL_CM" >ABSTRACT_BANNER_SMALL_CM</option>
                        <option value="ABSTRACT_BANNER_SMALL_CC" >ABSTRACT_BANNER_SMALL_CC</option>
                        <option value="DISPLAY_YAHOO" >DISPLAY_YAHOO</option>
                        <option value="VIDEO_YAHOO" >VIDEO_YAHOO</option>
                        <option value="APP_INSTALL_YAHOO" >APP_INSTALL_YAHOO</option>
                        <option value="YAHOO_CAROUSEL"  >YAHOO_CAROUSEL</option>
                        <option value="FB-LOCAL-AWARENESS" >FB-LOCAL-AWARENESS</option>
                        <option value="REAL_ESTATE_PROFESSIONAL_CAMPAIGN" >REAL_ESTATE_PROFESSIONAL_CAMPAIGN</option>
                        <option value="RICH_MEDIA_INTERSTITIAL" >RICH_MEDIA_INTERSTITIAL</option>
                        <option value="FB-LOCAL-AWARENESS" >FB-LOCAL-AWARENESS</option>
                        <option value="FB-INSTAGRAM" >FB-INSTAGRAM</option>
                        <option value="FB-MOBILE-APP-INSTALLS" >FB-MOBILE-APP-INSTALLS</option>
                        <option value="FB-PROMOTE-EVENT" >FB-PROMOTE-EVENT</option>
                        <option value="FB-INSTAGRAM-VIDEO" >FB-INSTAGRAM-VIDEO</option>
                        <option value="FB-CAROUSEL-AD" >FB-CAROUSEL-AD</option>
                        <option value="FB-LEAD" >FB-LEAD</option>
                        <option value="DIALOG_CLICK_TO_CALL" >DIALOG_CLICK_TO_CALL</option>
                    </select>
                </div>
            </form>
            <div class="col-sm-4" >
                <button id="btn_add_network" class="btn btn-success" >Add</button>
            </div>
        </div>
        <div class="network_list" >
            {foreach from=$active_networks item=active_network}
            <div class="row network_row network_row_100" >
                <div class="col-sm-4 col-xs-4 network_info" >{$active_network.network_name}</div>
                <div class="col-sm-4 col-xs-6 network_info" >{$active_network.campaign_type}</div>
                <div class="col-sm-4 col-xs-2" >
                    <span net_id="{$active_network.id}" class="glyphicon glyphicon-remove remove_network" ></span>
                </div>
            </div>
            {/foreach}
        </div>
    </div>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="/v2/js/jquery-2.0.3.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/v2/js/bootstrap.min.js"></script>
<script src="/v2/js/admin/edit_network.js"></script>
