<!--<div class="theme-report-row-wrap">-->
<section class="theme-container " xmlns="http://www.w3.org/1999/html">
    <div class="">
        <!--            <div class="theme-report-tabbed-section">-->
        <div class="row" >
            <div class="news-feed" >
                <h3 class="pull-left">{$campaign.name} ({$campaign.io})</h3>

                <div class="addButton pull-right" >
                    {if $editable}
                    <button data-toggle="modal" data-target="#addNewList" class="btn btn-success" >Add new AD</button>
                    {/if}
                    <a class="btn btn-success pull-right" href="/v2/campaign/reporting/{$campaign.id}" type="button"><img src="/v2/images/icons/report_icon.png"> View Report</a>


                    <div class="modal fade" id="addNewList" role="dialog">
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
                                    <h5>Ad images aren't allowed to include more than 20% text</h5>
                                    <div class="newAdd">
                                        <div id="uploaded_image_prew"></div>
                                        <div class="form-group">
                                            <label>Ad Title</label>
                                            <input id="fb_ad_title" maxlength="30" required="" class="form-control theme-geoform-control" type="text"name="title">
                                        </div>
                                        <div class="form-group">
                                            <label>Ad Description</label>
                                            <textarea id="fb_ad_body" maxlength="90" required="" class="form-control theme-geoform-control" name="fb_description"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <input type="url" name="destination_url" class="form-control theme-geoform-control" placeholder="Destination URL - http://..." id="modal_dest_url">
                                        </div>
                                        <input name="fb_page_id" id="fb_page_id" type="hidden" value="{$ads[$key]['fb_page_id']}" class="theme-form-control theme-geoform-control" />
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
                </div>
            </div>
        </div>

        <!--            </div>-->
        <div class="row" >
            <div class="campaign-info" >
                <h4>CAMPAIGN TYPE : <span>{$campaign.campaign_type}</span></h4>
                <h4 class="pull-left">START DATE : <span>{if !empty($campaign.campaign_start_datetime)}{$campaign.campaign_start_datetime|date_format:"%Y/%m/%d %H:%M"}{else}N/A{/if}</span></h4>
                <h4 class="endDateBlock">END DATE : <span>{if !empty($campaign.campaign_end_datetime)}{$campaign.campaign_end_datetime|date_format:"%Y/%m/%d %H:%M"}{else}N/A{/if}</span></h4>
                <hr>
                {if !$editable}
                <div class="theme-align-center"><h1 style="color:red;">You can't edit {$campaign.network_campaign_status} campaign</h1>
                </div>
                {/if}
                <!--                    <div class="newCampaign">-->

                {foreach from=$ads item=ad}
                {if $ad.approval_status!='DISAPPROVED'}
                <div class="newCampaign">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        {if $ad.creative_height > 155}
                        <img data-trigger="hover" data-toggle="popover"  data-content="<img src='{$ad.creative_url}' />" height="115" src="{$ad.creative_url}" />
                        {else}
                        {$margin = (155 - $ad.creative_height) / 2}
                        <img data-trigger="hover" data-toggle="popover"  data-content="<img src='{$ad.creative_url}' />" style="margin-top: {$margin}px" src="{$ad.creative_url}" />
                        {/if}
                    </div>
                    <div class="col-md-8 col-sm-6 col-xs-12" >
                        <div class="col-md-6">
                            <p class="link">{$ad.redirect_url}
                                {if $editable}
                                <a href="#" data-ad = '{$ad|@json_encode}' class="btn_edit_modal" ad_id = '{$ad.id}'>
                                    <!--                                    <img class="btn_edit" width="15" src="/v2/images/report-template/table-manage-edit-icon.png" />-->
                                    <i class="fa fa-edit"></i>
                                </a>
                                {/if}
                            </p>
                            <p><span class="txt_bold" >Total Clicks</span> : {if !$ad.clicks_count}N/A{else}{$ad.clicks_count}{/if}</p>
                            <p><span class="txt_bold" >Total Impressions</span> : {if !$ad.impressions_count}N/A{else}{$ad.impressions_count}{/if}</p>
                            {if !empty($campaign.total_clicks_count)}{$persent = $ad.clicks_count*100/$campaign.total_clicks_count}{/if}
                            <!--<p><span class="txt_bold" >Served</span> : {$persent|intval}</p>-->
                            <!--<div class="theme-report-progress progress"><div class="progress-bar theme-report-progress-bar progress-bar-blue" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: {$persent|intval}%;">{$persent|intval}%</div></div>-->

                        </div>
                        {if $ad.creative_is_active='Y' && $editable}
                        <div class="col-md-6">
                            <p><a class="toggle toggle-light" data-ad-id="{$ad.id}" data-toggle-on="{if $ad.creative_status=='ACTIVE' || $ad.creative_status=='ENABLED'}true{else}false{/if}" data-toggle-drag="{if $ad.creative_status=='ACTIVE'}true{else}false{/if}" data-toggle-click="{if $ad.creative_status=='ACTIVE'}true{else}true{/if}"></a></p>
                        </div>
                        {/if}
                    </div>
                    <!--                    <div >-->
                    <!--                        {if $ad.creative_is_active!='Y' || $editable}-->
                    <!--                        <span class="txt_bold" >AD status</span>-->
                    <!--                        <p><a class="toggle toggle-light" data-ad-id="{$ad.id}" data-toggle-on="{if $ad.creative_status=='ACTIVE' || $ad.creative_status=='ENABLED'}true{else}false{/if}" data-toggle-drag="{if $ad.creative_status=='ACTIVE'}true{else}false{/if}" data-toggle-click="{if $ad.creative_status=='ACTIVE'}true{else}true{/if}"></a></p>-->
                    <!--                        {/if}-->
                    <!--                    </div>-->

                </div>
                {else}
                <div class="newCampaign">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        {if $ad.creative_height > 155}
                        <img class="img-responsive fade in" data-trigger="hover" data-toggle="popover" data-content="<img src='{$ad.creative_url}' />" src="{$ad.creative_url}" />
                        {else}
                        {$margin = (155 - $ad.creative_height) / 2}
                        <img class="img-responsive fade in" data-trigger="hover" data-toggle="popover" data-content="<img src='{$ad.creative_url}' />" style="margin-top: {$margin}px" src="{$ad.creative_url}" />
                        {/if}
                    </div>
                    <div class="col-md-8 col-sm-6 col-xs-12" >
                        <p class="alert alert-danger ad_alert" >
                            The AD has been rejected by the network {$ad.disapproval_reasons}
                            <!--                                    <span class="glyphicon glyphicon-remove ad_notice_remove" ></span>-->
                        </p>

                        <div class="col-md-6">
                            <p class="link">{$ad.redirect_url}
                                {if $editable}
                                <a href="#" data-ad = '{$ad|@json_encode}' class="btn_edit_modal" ad_id = '{$ad.id}'>
                                    <!--                                            <img class="btn_edit" width="15" src="/v2/images/report-template/table-manage-edit-icon.png" />-->
                                    <i class="fa fa-edit"></i>
                                </a>
                                {/if}
                            </p>

                            <p><span class="txt_bold" >Total Clicks</span> : {if !$ad.clicks_count}N/A{else}{$ad.clicks_count}{/if}</p>
                            <p><span class="txt_bold" >Total Impressions</span> : {if !$ad.impressions_count}N/A{else}{$ad.impressions_count}{/if}</p>
                            {if !empty($campaign.total_clicks_count)}{$persent = $ad.clicks_count*100/$campaign.total_clicks_count}{/if}
                            <!--<p>% <span class="txt_bold" >Served</span> : {$persent|intval}</p>-->
                            <!--<div class="theme-report-progress progress"><div class="progress-bar theme-report-progress-bar progress-bar-blue" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: {$persent|intval}%;">{$persent|intval}%</div></div>-->
                        </div>
                    </div>
                </div>
                {/if}
                {/foreach}
            </div>
            <!--                    </div>-->

        </div>
    </div>
</section>
<!--</div>-->