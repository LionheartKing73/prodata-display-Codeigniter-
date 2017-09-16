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
                                        <h4 class="modal-title">Create your text Ad</h4>
                                    </div>
                                    <div class="modal-body text-left">
                                        <div class="theme-ad-subrow theme-ad-banner-subrow text_ads">
                                            <div class="theme-create-ad-form-wrap">
                                                <input name="campaign_id" type="hidden" value="{$campaign.id}" class="theme-form-control theme-geoform-control" />
                                                <input name="ad_id" type="hidden" value="" class="theme-form-control theme-geoform-control" />
                                                <input name="ad_link_id" type="hidden" value="" class="theme-form-control theme-geoform-control" />
                                                <div class="row" >
                                                    <div class="col-sm-12" >
                                                        <div class="textarea_block theme-geoform-group theme-form-group theme-inline-group">
                                                            <label class="theme-inline-label theme-light-weight">Title:</label>
                                                            <input name="title" maxlength="25"  type="text" value="" placeholder="Enter the title of your ad" id="title_input" class="theme-geoform-control theme-form-control" />
                                                            <span style="font-size:12px; color: #999898;"> Character Left: <span class="charecter_count" maxlength="25">25</span> </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" >
                                                    <div class="col-sm-6" >
                                                        <div class=" textarea_block theme-geoform-group theme-form-group theme-inline-group">
                                                            <label class="theme-inline-label theme-light-weight">Description 1</label>
                                                            <textarea name="description_1" maxlength="35" placeholder="Enter the desc of your ad" class="theme-geoform-control theme-form-control full_width"></textarea>
                                                            <span style="font-size:12px; color: #999898;"> Character Left: <span class="charecter_count" maxlength="35">35</span> </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6" >
                                                        <div class=" textarea_block theme-geoform-group theme-form-group theme-inline-group">
                                                            <label class="theme-inline-label theme-light-weight">Description 2</label>
                                                            <textarea name="description_2" maxlength="35" placeholder="Enter the desc of your ad" class="theme-geoform-control theme-form-control full_width"></textarea>
                                                            <span style="font-size:12px; color: #999898;"> Character Left: <span class="charecter_count" maxlength="35">35</span> </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" >
                                                    <div class="col-sm-6" >
                                                        <div class="textarea_block theme-geoform-group theme-form-group theme-inline-group">
                                                            <label class="theme-inline-label theme-light-weight">Display Url:</label>
                                                            <input name="display_url" type="text" value="" placeholder="Enter the display url of your ad" id="modal_input1" class="theme-geoform-control theme-form-control" maxlength="25" />
                                                            <span style="font-size:12px; color: #999898;"> Character Left: <span class="charecter_count" maxlength="25">25</span> </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6" >
                                                        <div class="textarea_block theme-geoform-group theme-form-group theme-inline-group">
                                                            <label class="theme-inline-label theme-light-weight">URl:</label>
                                                            <input name="destination_url" type="url" value="" placeholder="Enter the url of your ad" id="modal_input"  class="theme-geoform-control theme-form-control" maxlength="35" />
                                                            <span style="font-size:12px; color: #999898;"> Character Left: <span class="charecter_count" maxlength="35">35</span> </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="theme-geoform-group theme-form-group">
                                                    <div id="text_ad_upload" class="text_ad_upload_label"></div>
                                                </div>
                                                <div class="theme-geoform-group theme-form-group theme-submit-group theme-align-center">
                                                    <input type="button" value="Create New Ad" class="theme-create-add-btn theme-submit-control" id="create_new_add">
                                                </div>
                                            </div>
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
                            <img src="/v2/images/report-template/table-manage-edit-icon.png" width="15"/>
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
                            <div class="theme-ad-subrow theme-ad-banner-subrow">
                                <div class="examplte_show_div">
                                    <div class="theme-ad-banner-content theme-display-table theme-no-gutter">
                                        <div class="theme-xs-12 theme-ad-logo-col theme-table-middle-cell text_ad_block">
                                            <span class="text_ad_img" >
                                                <a href="{$ad.redirect_url}"><img alt="" src="{if $ad.creative_url}{$ad.creative_url}{else}/v2/images/report-template/no-ad-logo-thumb.png{/if}"></a>
                                            </span>
                                        </div>
                                        <div class="theme-xs-12 theme-sm-7 theme-ad-desc-col theme-table-middle-cell">
                                            <div class="theme-ad-content">
                                                <h2><a href="">{$ad.title}</a></h2>
                                                <p>{$ad.description_1} {$ad.description_2}</p>
                                                <p class="theme-ad-url-line"> <a href="{$ad.redirect_url}">{$ad.redirect_url}</a></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>  
                        </td>
                        <td class="td_success td40" >
                            <p>{$ad.redirect_url}
                                {if $editable}
                                <a href="#" data-ad = '{$ad|@json_encode}' class="btn_edit_modal" ad_id = '{$ad.id}'>
                                    <img class="btn_edit" width="27" src="/v2/images/icons/btn_edit.png" />
                                </a>
                                {/if}
                            </p>
                            <p><span class="txt_bold" >Total Clicks</span> :{$ad.clicks_count} </p>
                            <p><span class="txt_bold" >Total Impressions</span> : {$ad.impressions_count}</p>
                            {if !empty($campaign.total_clicks_count)}{$persent = $ad.clicks_count*100/$campaign.total_clicks_count}{/if}
                            <!--<p><span class="txt_bold" >Served</span> :{$persent|intval}</p>-->
<!--                            <div class="theme-report-progress progress">
                                <div class="progress-bar theme-report-progress-bar progress-bar-blue" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: {$persent|intval}%" >
                                    {$persent|intval}%
                                </div>
                            </div>-->
                        </td>
                        <td class="td_success td20" >
                            {if $ad.creative_is_active=='Y' && $editable}
                            <span class="txt_bold" >AD status</span>
                            <p><a class="toggle toggle-light" data-ad-id="{$ad.id}" data-campaign-id="{$campaign.id}" data-toggle-on="{if $ad.creative_status=='ACTIVE' || $ad.creative_status=='ENABLED'}true{else}false{/if}" data-toggle-drag="{if $ad.creative_status=='ACTIVE'}true{else}false{/if}" data-toggle-click="{if $ad.creative_status=='ACTIVE'}true{else}true{/if}"></a></p>
                            {/if}
                        </td>
                    </tr> 
                {else}
                    <tr>
                        <td class="td_error td40" >
                            <div class="theme-ad-subrow theme-ad-banner-subrow">
                                <div class="examplte_show_div">
                                    <div class="theme-ad-banner-content theme-display-table theme-no-gutter">
                                        <div class="theme-xs-12 theme-ad-logo-col theme-table-middle-cell text_ad_block">
                                            <span class="text_ad_img" >
                                                <a href="{$ad.redirect_url}">
                                                    <img alt="" src="{if $ad.creative_url}{$ad.creative_url}{else}/v2/images/report-template/no-ad-logo-thumb.png{/if}">
                                                </a>
                                            </span>
                                        </div>
                                        <div class="theme-xs-12 theme-sm-7 theme-ad-desc-col theme-table-middle-cell">
                                            <div class="theme-ad-content">
                                                <h2><a href="">{$ad.title}</a></h2>
                                                <p>{$ad.description_1} {$ad.description_2}</p>
                                                <p class="theme-ad-url-line"> <a href="{$ad.redirect_url}">{$ad.redirect_url}</a></p>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>  
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
                            <!--<p><span class="txt_bold" >Served</span> : {$persent|intval}</p>-->
<!--                            <div class="theme-report-progress progress" style="width:440px">
                                <div class="progress-bar theme-report-progress-bar progress-bar-blue" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: {$persent|intval}%;">
                                    {$persent|intval}%
                                </div>
                            </div>-->
                        </td>
                    </tr> 
                {/if}
                {/foreach}
            </table>
        </div>
    </div>
</div>