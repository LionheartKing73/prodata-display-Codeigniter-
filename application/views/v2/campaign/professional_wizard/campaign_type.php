<script>

    var user = {$user|@json_encode};

</script>
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
            {if $user['is_bing'] == "Y"}
            <div class="theme-tabbed-form-group">
                <input type_class="BING" name="campaign_network" type="radio" value="TEXTAD" class="theme-tabbed-form-control check_type" id="text-ads" />
                <label class="theme-tabbed-form-label" for="text-ads">Bing Ads</label>
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



        </div>

        <!-- block campaign type placement -->

        <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-md-6"  id="campaign_type_placement">
            {foreach from=$user_networks  key=k item=network_type}

            <div class="theme-tabbed-form-group {$network_type.network_name}">
                <input name="campaign_type" type="radio" value="{$network_type.campaign_type}"  class="{if $network_type.campaign_type == 'DISPLAY-RETARGET'} marketing-ads-radio {/if}theme-tabbed-form-control display-ads-radio" id="network_type_{$network_type.id}" />
                <label class="theme-tabbed-form-label" for="network_type_{$network_type.id}" >{$campaign_type_names[$network_type.campaign_type]}</label>
            </div>
            {/foreach}


        </div>
        <!-- block campaign type placement end-->

        <div class="clearfix" ></div>
        <div class="theme-tabbed-form-group theme-tabbed-form-submit-group">
            <a href="javascript:" class="btn_next_step btn_continue" >CONTINUE</a>
        </div>


    </div>


</fieldset>


