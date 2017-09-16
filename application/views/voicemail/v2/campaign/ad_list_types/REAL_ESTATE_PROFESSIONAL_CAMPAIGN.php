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
                            <button id="btn_add_ad" data-toggle="modal" data-target="#ad_modal" class="btn btn-success" >Add new AD</button>
                        {/if}
                        <div class="modal fade" id="ad_modal" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Create your Image ads</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="theme-display-table theme-no-gutter theme-no-gutter">
                                            <div id="theme-file-uploader" class="theme-lg-6 theme-sm-6 theme-xs-12 theme-two-col theme-table-top-cell">
                                                <div>
                                                    <div id="uploader">
                                                        <p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
                                                    </div>
                                                    <div id="upload-result" class="alert-message"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div style="width:100%;margin-top:15px" class="theme-create-ad-form-wrap theme-scrollable-ad-row">
                                                    <div id="uploaded_image_prew"></div>
                                                    <input type="url" name="destination_url" class="form-control theme-geoform-control" placeholder="Destination URL - http://..." id="modal_dest_url">
                                                    <input type="url" name="tracking_url" class="form-control theme-geoform-control" placeholder="Tracking URL - http://..." id="modal_tracking_url">
                                                    <input name="campaign_id" type="hidden" value="{$campaign.id}" class="theme-form-control theme-geoform-control" />
                                                    <input name="ad_id" type="hidden" value="" class="theme-form-control theme-geoform-control" />
                                                    <input name="ad_link_id" type="hidden" value="" class="theme-form-control theme-geoform-control" />
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
                <tr {if $ad.approval_status =="UNCHECKED"} class="hilight_gray" {/if}>
                    <td class="td_success td40" >
                        {if $ad.creative_height > 155}
                        <img data-trigger="hover" data-toggle="popover"  data-content="<img src='{$ad.creative_url}' />" height="115" src="{$ad.creative_url}" />
                        {else}
                        {$margin = (155 - $ad.creative_height) / 2}
                        <img data-trigger="hover" data-toggle="popover"  data-content="<img src='{$ad.creative_url}' />" style="margin-top: {$margin}px" src="{$ad.creative_url}" />
                        {/if}
                    </td>
                    <td class="td_success td40" >
                        <p>{$ad.redirect_url}
                            {if $editable}
                            <a href="#" data-ad = '{$ad|@json_encode}' class="btn_edit_modal" ad_id = '{$ad.id}'>
                                <img class="btn_edit" width="15" src="/v2/images/report-template/table-manage-edit-icon.png" />
                            </a>
                            {/if}
                        </p>
                        <p><span class="txt_bold" >Total Clicks</span> : {$ad.clicks_count}</p>
                        <p><span class="txt_bold" >Total Impressions</span> : {$ad.impressions_count}</p>
                        {if !empty($campaign.total_clicks_count)}{$persent = $ad.clicks_count*100/$campaign.total_clicks_count}{/if}
                        <!--<p><span class="txt_bold" >Served</span> : {$persent|intval}</p>-->
                        <!--<div class="theme-report-progress progress"><div class="progress-bar theme-report-progress-bar progress-bar-blue" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: {$persent|intval}%;">{$persent|intval}%</div></div>-->
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
                        {if $ad.creative_height > 155}
                        <img data-trigger="hover" data-toggle="popover"  data-content="<img src='{$ad.creative_url}' />" height="115" src="{$ad.creative_url}" />
                        {else}
                        {$margin = (155 - $ad.creative_height) / 2}
                        <img data-trigger="hover" data-toggle="popover"  data-content="<img src='{$ad.creative_url}' />" style="margin-top: {$margin}px" src="{$ad.creative_url}" />
                        {/if}
                    </td>
                    <td class="td_error" colspan="2" >
                        <p class="alert alert-danger ad_alert" >
                            The AD has been rejected by the network {$ad.disapproval_reasons}
                            <span class="glyphicon glyphicon-remove ad_notice_remove" ></span>
                        </p>
                        <p>{$ad.redirect_url}
                            {if $editable}
                            <a href="#" data-ad = '{$ad|@json_encode}' class="btn_edit_modal" ad_id = '{$ad.id}'>
                                <img class="btn_edit" width="15" src="/v2/images/report-template/table-manage-edit-icon.png" />
                            </a>
                            {/if}
                        </p>
                        <p><span class="txt_bold" >Total Clicks</span> : {$ad.clicks_count}</p>
                        <p><span class="txt_bold" >Total Impressions</span> : {$ad.impressions_count}</p>
                        {if !empty($campaign.total_clicks_count)}{$persent = $ad.clicks_count*100/$campaign.total_clicks_count}{/if}
                        <!--<p>% <span class="txt_bold" >Served</span> : {$persent|intval}</p>-->
                        <!--<div class="theme-report-progress progress"><div class="progress-bar theme-report-progress-bar progress-bar-blue" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: {$persent|intval}%;">{$persent|intval}%</div></div>-->
                    </td>
                </tr>
                {/if}
                {/foreach}
            </table>
        </div>
    </div>
</div>