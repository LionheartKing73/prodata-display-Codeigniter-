<script>
    var user = {$user|@json_encode};
</script>
<div id="fb_alert" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Link your account to Facebook</h4>
            </div>
            <div class="modal-body">
                <p>
                    Many Facebook campaigns require a Facebook Page to be used for linking the campaign and page for best practices.
                </p>
                <p>
                    If you are unable to do so, ProData will utilize our own Facebook page for your campaign.
                </p>
                <p>
                    Please go to your profile page and link your account to Facebook.
                </p>
                <div class="modal-footer">
                    <a href="/v2/profile/index" class="btn btn-primary">LINK MY ACCOUNT</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">SKIP</button>
                </div>
            </div>
        </div>
    </div>
</div>
<fieldset>
    <h1>Select Your Campaign Type</h1>
    <div class="theme-report-tabbed-form-wrap theme-row ">
        <div id="network_types_list" class="theme-lg-6 theme-sm-6 theme-md-6 theme-xs-12 campaign_type_float_left">
            <!--        <div class="theme-tabbed-form-group">
                        <input name="campaign_type" type="radio" value="EMAIL" class="theme-tabbed-form-control email-pays-campaign-radio check_type" checked id="email-pays" />
                        <label class="theme-tabbed-form-label" for="email-pays">Email to Pay-Per-Click Campaign</label>
                    </div>-->
            {if $user['is_google'] == "Y"}
            <div class="theme-tabbed-form-group">
                <input  type_class="GOOGLE" name="campaign_network" type="radio" value="DISPLAY" class="theme-tabbed-form-control display-ads-radio check_type" id="display-ads" />
                <label class="theme-tabbed-form-label" for="display-ads">Display Ads</label>
            </div>
            {/if}
            {if $user['is_fiq'] == "Y"}
            <div class="theme-tabbed-form-group">
                <input type_class="FIQ" name="campaign_network" type="radio" value="DISPLAY-RETARGET" class="theme-tabbed-form-control check_type" id="remarketing" />
                <label class="theme-tabbed-form-label" for="remarketing">Text Ads</label>
            </div>
            {/if}
            <!--
            <div class="theme-tabbed-form-group">
                <input name="campaign_type" type="radio" value="TEXTAD" class="theme-tabbed-form-control" id="link-ads" />
                <label class="theme-tabbed-form-label" for="link-ads">Text Link Ads (SEO)</label>
            </div>
            -->
            {if $user['is_bing'] == "Y" || $user['is_fiq'] == "Y" || $user['is_google'] == "Y"}
            <div class="theme-tabbed-form-group">
                <input type_class="TEXTAD" name="campaign_network" type="radio" value="TEXTAD" class="theme-tabbed-form-control check_type" id="text-ads" />
                <label class="theme-tabbed-form-label" for="text-ads">Text Link Ads (SEO)</label>
            </div>
            {/if}
            {if $user['is_facebook'] == "Y"}
            <div class="theme-tabbed-form-group">
                <input type_class="FACEBOOK" name="campaign_network" type="radio" value="FACEBOOK" class="theme-tabbed-form-control check_type" id="facebook" />
                <label class="theme-tabbed-form-label" for="facebook">Facebook Ad Exchange</label>
            </div>
            {/if}
            {if $user['is_airpush'] == "Y"}
            <div class="theme-tabbed-form-group">
                <input type_class="AIRPUSH" name="campaign_network" type="radio" value="AIRPUSH" class="theme-tabbed-form-control check_type" id="airpush" />
                <label class="theme-tabbed-form-label" for="airpush">Mobile Exclusive</label>
            </div>
            {/if}
            {if $user['is_yahoo'] == "Y"}
            <div class="theme-tabbed-form-group">
                <input type_class="YAHOO" name="campaign_network" type="radio" value="YAHOO" class="theme-tabbed-form-control check_type" id="yahoo" />
                <label class="theme-tabbed-form-label" for="yahoo">Yahoo Gemini</label>
            </div>
            {/if}
        </div>
        <!-- block campaign type placement -->
        <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-md-6"  id="campaign_type_placement">
            {foreach from=$user_networks  key=k item=network_type}
                {if $network_type.campaign_type!='REAL_ESTATE_PROFESSIONAL_CAMPAIGN' && $campaign_type_names[$network_type.campaign_type] != ''}
                    <div class="theme-tabbed-form-group {if $network_type.campaign_type != 'TEXTAD'} {$network_type.network_name} {/if}  {$network_type.campaign_type}">
                        <input name="campaign_type" type="radio" value="{$network_type.campaign_type}"  class="{if $network_type.campaign_type == 'DISPLAY-RETARGET'} marketing-ads-radio {/if}theme-tabbed-form-control display-ads-radio" id="network_type_{$network_type.id}" />
                        <label class="theme-tabbed-form-label" for="network_type_{$network_type.id}" >{$campaign_type_names[$network_type.campaign_type]}</label>
                    </div>
                {/if}
            {/foreach}
        </div>
        <!-- block campaign type placement end-->
        <div class="clearfix" ></div>
        <div class="theme-tabbed-form-group theme-tabbed-form-submit-group">
            <a href="javascript:" class="btn_next_step btn_continue" >CONTINUE</a>
        </div>
    </div>
</fieldset>
