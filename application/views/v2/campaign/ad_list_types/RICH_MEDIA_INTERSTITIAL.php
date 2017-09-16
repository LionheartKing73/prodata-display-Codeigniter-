<div class="theme-report-row-wrap">
    <div class="theme-container container-fluid">
        <div class="theme-report-campaign-list-row">
            <div class="theme-report-tabbed-section">
                <div class="row" >
                    <div class="col-sm-6" >
                        <h1 class="campaign_name" >{$campaign.name} ({$campaign.io})</h1>
                    </div>
                    <div class="col-sm-3 text-right" >
                        {if $editable}
                        <button id="btn_add_ad" data-toggle="modal" data-target="#ad_modal" class="btn btn-success" id="btn_add_ad">Add new AD</button>
                        {/if}
                        <div class="modal fade" id="ad_modal" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Create your ads</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div style="width:100%;margin-top:15px" class="theme-create-ad-form-wrap theme-scrollable-ad-row">
                                                    <div id="uploaded_image_prew"></div>
                                                    <label class="lbl_script" style="text-align: left; width: 100%;">HTML/JS script</label>
                                                    <textarea maxlength="3000"  class="form-control theme-geoform-control script" type="text" name="script" /></textarea>
                                                    <input name="campaign_id" type="hidden" value="{$campaign.id}" class="theme-form-control theme-geoform-control" />
                                                    <input name="ad_id" type="hidden" value="" class="theme-form-control theme-geoform-control" />
<!--                                                    <input name="ad_link_id" type="hidden" value="" class="theme-form-control theme-geoform-control" />-->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="theme-geoform-group theme-form-group theme-submit-group theme-align-center">
                                            <input type="button" value="Create New Ad" class="no-padding theme-create-add-btn theme-submit-control" id="create_new_add">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3" >
                        <a class="btn btn-success pull-right" href="/v2/campaign/reporting/{$campaign.id}">
                            <img src="/v2/images/icons/report_icon.png" />
                            VIEW REPORT
                        </a>
                    </div>
                </div>

            </div>
            <div class="row" >
                <div class="col-sm-6 campaign_info_block" >
                    <p><span class="txt_bold" >CAMPAIGN TYPE</span> : {$campaign.campaign_type}</p>
                    <p><span class="txt_bold" >START DATE</span> : {if !empty($campaign.campaign_start_datetime)}{$campaign.campaign_start_datetime|date_format:"%Y/%m/%d %H:%M"}{/if}  <span class="txt_bold ad_end_date" >END DATE</span> : {if !empty($campaign.campaign_end_datetime)}{$campaign.campaign_end_datetime|date_format:"%Y/%m/%d %H:%M"}{/if}</p>
                </div>
            </div>
            {if !$editable}
            <div class="theme-align-center"><h1 style="color:red;">You can't edit {$campaign.network_campaign_status} campaign</h1>
            </div>
            {/if}
            <table class="table table-responsive ad_table" cellpadding="10">
                {foreach from=$ads item=ad}
                {if $ad.approval_status!='DISAPPROVED'}
                <tr>
                    <td class="td_success td40" >
                        <div class="theme-ad-content">
                            <textarea maxlength="3000" disabled="disabled" class="form-control script" style="width: 500px !important;" />{$ad.script}</textarea>
                        </div>
                    </td>
                    <td class="td_success td40" >
                        <p>
                            {if $editable}
                            <a href="#"  class="btn_edit_modal" data-ad_id = '{$ad.id}'>
                                <img class="btn_edit" width="15" src="/v2/images/report-template/table-manage-edit-icon.png" />
                            </a>
                            {/if}
                        </p>
                        <p><span class="txt_bold" >Total Clicks</span> : {$ad.clicks_count}</p>
                        <p><span class="txt_bold" >Total Impressions</span> : {$ad.impressions_count}</p>
                        {if !empty($campaign.total_clicks_count)}{$persent = $ad.clicks_count*100/$campaign.total_clicks_count}{/if}
                        <!--<p><span class="txt_bold" >Served</span> : {$persent|intval}</p>-->
                        <!--<div class="theme-report-progress progress"><div class="progress-bar theme-report-progress-bar progress-bar-blue" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: {$persent|intval}%;">{$persent|intval}%</div></div>-->
                        <div class="col-xs-12">
                            <button type="button" class="btn btn_iframe_code">Get Code</button>
                            <div class="snipet_block" >
                                <ul  class="nav nav-pills snipet_tabs">
                                    <li class="active">
                                        <a class="tab_link" href="#iframe_tab_{$ad.id}" data-toggle="tab">Iframe</a>
                                    </li>
                                    <li>
                                        <a class="tab_link" href="#js_tab_{$ad.id}" data-toggle="tab">Javascript</a>
                                    </li>
                                </ul>

                                <div class="tab-content clearfix">
                                    <div class="code_block tab-pane active" id="iframe_tab_{$ad.id}">
                                        &ltiframe src="{$site_url}v2/tracking/ad_iframe_view/{$ad.id}" scrolling='no' marginheight="0"
                                        marginwidth="0" 
                                        frameborder="0" 
                                        width="{$ad.creative_width}"
                                        height="{$ad.creative_height}" &gt
                                        &lt/iframe&gt
                                    </div>
                                    <div class="code_block tab-pane" id="js_tab_{$ad.id}">
                                        &ltscript&gt
                                        var tracking_ad_id = {$ad.id};
                                        var tracking_campaign_id = {$campaign.id};
                                        &lt/script&gt
                                        <br>
                                        &ltscript src="{$site_url}v2/js/add_impression.js &gt
                                        &lt/script&gt
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>

                    <td class="td_success td20" >
                        {if $ad.creative_is_active=='Y' && $editable}
                        <span class="txt_bold" >AD status</span>
                        <p><a class="toggle toggle-light" data-ad-id="{$ad.id}" data-toggle-on="{if $ad.creative_status=='ACTIVE' || $ad.creative_status=='ENABLED'}true{else}false{/if}" data-toggle-drag="{if $ad.creative_status=='ACTIVE'}true{else}false{/if}" data-toggle-click="{if $ad.creative_status=='ACTIVE'}true{else}true{/if}"></a></p>
                        {/if}
                    </td>

                </tr>
                {else}
                <tr>
                    <td class="td_error td40" >
                        <div class="theme-ad-content">
                            <textarea maxlength="3000" disabled="disabled" class="form-control script" style="width: 500px !important;" />{$ad.script}</textarea>
                        </div>
                    </td>
                    <td class="td_error" colspan="2" >
                        <p class="alert alert-danger ad_alert" >
                            The AD has been rejected by the network {$ad.disapproval_reasons}
                            <span class="glyphicon glyphicon-remove ad_notice_remove" ></span>
                        </p>
                        <p>
                            {if $editable}
                            <a href="#" class="btn_edit_modal" data-ad_id = '{$ad.id}'>
                                <img class="btn_edit" width="15" src="/v2/images/report-template/table-manage-edit-icon.png" />
                            </a>
                            {/if}
                        </p>
                        <p><span class="txt_bold" >Total Clicks</span> : {$ad.clicks_count}</p>
                        <p><span class="txt_bold" >Total Impressions</span> : {$ad.impressions_count}</p>
                        {if !empty($campaign.total_clicks_count)}{$persent = $ad.clicks_count*100/$campaign.total_clicks_count}{/if}
                        <!--<p>% <span class="txt_bold" >Served</span> : {$persent|intval}</p>-->
                        <!--<div class="theme-report-progress progress"><div class="progress-bar theme-report-progress-bar progress-bar-blue" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: {$persent|intval}%;">{$persent|intval}%</div></div>-->
                        {include file='snippet_tabs.php'}
                    </td>
                </tr>
                {/if}
                {/foreach}
            </table>
        </div>
    </div>
</div>
