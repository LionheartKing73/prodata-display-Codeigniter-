<!--<div class="theme-report-row-wrap">-->
{$smarty.current_dir assign=dir}

<section class="theme-container container-fluid">
    <div class="">
        <!--            <div class="theme-report-tabbed-section">-->
        <div class="row" >
            <div class="news-feed" >
                <h3 class="pull-left" >{$campaign.name} ({$campaign.io})</h3>
                {$key = $ads|@count -1}

                <div class="addButton pull-right" >

                    {if $editable}
                    <button data-toggle="modal" data-target="#ad_modal" class="btn btn-success" id="btn_add_ad">Add new AD</button>
                    {/if}

                    <a class="btn btn-success pull-right" href="/v2/campaign/reporting/{$campaign.id}">
                        <img src="/v2/images/icons/report_icon.png" />
                        VIEW REPORT
                    </a>
                </div>

                <!--                    <div class="col-sm-3 text-right" >-->

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
                                <!--                                        <div class="row">-->
                                <!--                                            <div class="col-xs-12">-->

                                <div class="newAdd theme-create-ad-form-wrap">
                                    <div id="uploaded_image_prew"></div>
                                    <div class="form-group">
                                        <input type="url" name="destination_url" class="form-control theme-geoform-control" placeholder="Destination URL - http://..." id="modal_dest_url">
                                    </div>
                                    {if $user.is_tracking_url}
                                    <div class="form-group">
                                        <input type="url" name="tracking_url" class="form-control theme-geoform-control" placeholder="Tracking URL - http://..." id="modal_tracking_url">
                                    </div>
                                    {/if}

                                    <input name="campaign_id" type="hidden" value="{$campaign.id}" class="theme-form-control theme-geoform-control" />
                                    <input name="ad_id" type="hidden" value="" class="theme-form-control theme-geoform-control" />
                                    <input name="ad_link_id" type="hidden" value="" class="theme-form-control theme-geoform-control" />
                                </div>
                            </div>
                            <!--                                        </div>-->
                            <!--                                    </div>-->
                            <div class="modal-footer">
                                <input type="button" value="Create New Ad" class="btn btn-primary text-uppercase" id="create_new_add">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--                    </div>-->

            </div>
        </div>

    </div>

    <div class="row" >
        <div class="campaign-info" >
            <h4>CAMPAIGN TYPE : <span>{$campaign.campaign_type}</span></h4>
            <h4 class="pull-left">START DATE : <span>{if !$campaign.campaign_start_datetime}N/A{else}{$campaign.campaign_start_datetime|date_format:"%Y/%m/%d %H:%M"}{/if}</span></h4>
            <h4 class="endDateBlock">END DATE : <span>{if !$campaign.campaign_end_datetime}N/A{else}{$campaign.campaign_end_datetime|date_format:"%Y/%m/%d %H:%M"}{/if}</span></h4>
            <hr>
            {if !$editable}
            <div class="theme-align-center"><h1 style="color:red;">You can't edit {$campaign.network_campaign_status} campaign</h1>
            </div>
            {/if}
            <!--                <table class="table table-responsive ad_table" cellpadding="10">-->
            {foreach from=$ads item=ad}
            {if $ad.approval_status != 'DISAPPROVED'}
            <div class="newCampaign">
                <div class="col-md-4 col-sm-6 col-xs-12">
                    {if $ad.creative_height > 155}
                    <img data-trigger="hover" data-toggle="popover" class="img-responsive"  data-content="<img src='{$ad.creative_url}' />" height="115" src="{$ad.creative_url}" />
                    {else}
                    {$margin = (155 - $ad.creative_height) / 2}
                    <img data-trigger="hover" data-toggle="popover" class="img-responsive" data-content="<img src='{$ad.creative_url}' />" style="margin-top: {$margin}px" src="{$ad.creative_url}" />
                    {/if}
                </div>
                <div class="col-md-7 col-sm-6 col-xs-9" >
                    {if $editable}
                    <a href="#" data-ad = '{$ad|@json_encode}' class="btn_edit_modal" ad_id = '{$ad.id}' >
                        <!--                                    <img class="btn_edit" width="15" src="/v2/images/report-template/table-manage-edit-icon.png" />-->
                        <i class="fa fa-edit"></i>
                    </a>
                    {/if}
                    {if $ad.creative_is_active=='Y' && $editable}
                    <div class="col-md-3 col-xs-3">
                        <p><a class="toggle toggle-light" data-ad-id="{$ad.id}" data-toggle-on="{if $ad.creative_status=='ACTIVE' || $ad.creative_status=='ENABLED'}true{else}false{/if}" data-toggle-drag="{if $ad.creative_status=='ACTIVE'}true{else}false{/if}" data-toggle-click="{if $ad.creative_status=='ACTIVE'}true{else}true{/if}"></a></p>
                    </div>
                    {/if}
                    <div class="col-md-12 col-xs-12">
                        <p class="link">{$ad.redirect_url}</p>
                        <p><span class="txt_bold" >Total Clicks</span> : {if !$ad.clicks_count}0{else}{$ad.clicks_count}{/if}</p>
                        <p><span class="txt_bold" >Total Impressions</span> : {if !$ad.impressions_count}0{else}{$ad.impressions_count}{/if}</p>
                        {if !empty($campaign.total_clicks_count)}{$persent = $ad.clicks_count*100/$campaign.total_clicks_count}{/if}
                        {if $user.is_admin}
                        <p><span class="txt_bold" >Click Url</span> : {$ad.destination_url}</p>
                        <p><span class="txt_bold" >Beacon Url</span> : {base_url()}tracking/beacon/{$ad.campaign_id}/{$ad.id}</p>
                        {/if}
                        <!--<p><span class="txt_bold" >Served</span> : {$persent|intval}</p>-->
                        <!--<div class="theme-report-progress progress"><div class="progress-bar theme-report-progress-bar progress-bar-blue" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: {$persent|intval}%;">{$persent|intval}%</div></div>-->
                    </div>
                    {include file='v2/campaign/ad_list_types/snippet-tabs.php'}

                </div>

            </div>
            {else}
            <div class="newCampaign">
                <div class="col-md-4 col-sm-6 col-xs-12">
                    {if $ad.creative_height > 155}
                    <img id="popover" src="{$ad.creative_url}"  data-trigger="hover" data-content="<img src='{$ad.creative_url}' />" class="img-responsive" data-toggle="popover">
                    <!--                    <img data-trigger="hover" data-toggle="popover"  data-content="<img src='{$ad.creative_url}' />" height="115" src="{$ad.creative_url}" />-->
                    {else}
                    {$margin = (155 - $ad.creative_height) / 2}
                    <img id="popover" src="{$ad.creative_url}"  data-trigger="hover" class="img-responsive" data-toggle="popover">
                    {/if}

                </div>
                <div class="col-md-7 col-sm-6 col-xs-9" >
                    {if $editable}
                    <a href="#" data-ad = '{$ad|@json_encode}' class="btn_edit_modal" ad_id = '{$ad.id}'>
                        <!--                                    <img class="btn_edit" width="15" src="/v2/images/report-template/table-manage-edit-icon.png" />-->
                        <i class="fa fa-edit"></i>
                    </a>
                    {/if}
                    <p class="alert alert-danger ad_alert" >
                        The AD has been rejected by the network {$ad.disapproval_reasons}
                        <!--                            <span class="glyphicon glyphicon-remove ad_notice_remove" ></span>-->
                    </p>

                    <p class="link">{$ad.redirect_url}</p>
                    <p><span class="txt_bold" >Total Clicks</span> : {if !$ad.clicks_count}0{else}{$ad.clicks_count}{/if}</p>
                    <p><span class="txt_bold" >Total Impressions</span> : {if !$ad.impressions_count}0{else}{$ad.impressions_count}{/if}</p>
                    {if !empty($campaign.total_clicks_count)}{$persent = $ad.clicks_count*100/$campaign.total_clicks_count}{/if}
                    {if $user.is_admin}
                    <p><span class="txt_bold" >Click Url</span> : {$ad.destination_url}</p>
                    <p><span class="txt_bold" >Beacon Url</span> : {base_url()}tracking/beacon/{$ad.campaign_id}/{$ad.id}</p>
                    {/if}
                        <!--<p><span class="txt_bold" >Served</span> : {$persent|intval}</p>-->
                        <!--<div class="theme-report-progress progress"><div class="progress-bar theme-report-progress-bar progress-bar-blue" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: {$persent|intval}%;">{$persent|intval}%</div></div>-->
                    
                </div>
                {include file='v2/campaign/ad_list_types/snippet-tabs.php'}
            </div>
            {/if}
            {/foreach}
            <!--            </table>-->
            <!--        </div>-->
        </div>
    </div>
</section>
<!--</div>-->
