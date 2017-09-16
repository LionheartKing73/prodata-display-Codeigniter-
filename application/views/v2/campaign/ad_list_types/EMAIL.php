<div class="theme-report-row-wrap">
    <div class="theme-container container-fluid">
        <div class="theme-report-campaign-list-row">
            <div class="theme-report-tabbed-section">
                <div class="row" >
                    <div class="col-sm-6" >
                        <h1 class="campaign_name" >AUDI SELL DOWN</h1>
                    </div>
                    <div class="col-sm-3" >
                        <button class="btn btn-success pull-right" >
                            <img src="/v2/images/icons/report_icon.png" />
                            VIEW REPORT
                        </button>
                    </div>
                </div>
            </div>
            <div class="row" >
                <div class="col-sm-6 campaign_info_block" >
                    <p><span class="txt_bold" >CAMPAIGN TYPE</span> : {$campaign.campaign_type}</p>
                    <p><span class="txt_bold" >START DATE</span> : {if !empty($campaign.campaign_start_datetime)}{$campaign.campaign_start_datetime|date_format:"%Y/%m/%d %H:%M"}{/if}  <span class="txt_bold ad_end_date" >END DATE</span> : {if !empty($campaign.campaign_end_datetime)}{$campaign.campaign_end_datetime|date_format:"%Y/%m/%d %H:%M"}{/if}</p>
                </div>
            </div>
            {if !empty($ads)}
            <table class="table table-responsive ad_table" cellpadding="10">
                {foreach from=$ads item=ad}
                    <tr>
                        <td class="td_success td40" >
                            <div class="theme-ad-subrow theme-ad-banner-subrow">
                                <div id="examplte_show_div">
                                    <div class="theme-ad-banner-content theme-display-table theme-no-gutter">
                                        <div class="theme-xs-12 theme-ad-logo-col theme-table-middle-cell text_ad_block">
                                            <span class="text_ad_img" >
                                                <a href=""><img alt="" src="/v2/images/report-template/no-ad-logo-thumb.png"></a>
                                            </span>
                                        </div>
                                        <div class="theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                            <div class="theme-ad-content">
                                                <h2><a href="">{$ad.title}</a></h2>
                                                <p>{$ad.description_1} {$ad.description_2}</p>
                                                <p class="theme-ad-url-line"> <a href="">{$ad.display_url}</a></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>  
                        </td>
                        <td class="td_success td40" >
                            <p><img class="btn_edit" width="27" src="/v2/images/icons/btn_edit.png" /> </p>
                            <p><span class="txt_bold" >Total Clicks</span> :{$ad.clicks_count} </p>
                            {if !empty($campaign.total_clicks_count)}{$persent = $ad.clicks_count*100/$campaign.total_clicks_count}{/if}
                            <p><span class="txt_bold" >Served</span> :{$persent|intval}</p>
                            <div class="theme-report-progress progress"><div class="progress-bar theme-report-progress-bar progress-bar-blue" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:{$persent|intval}%">{$persent|intval}%</div></div>
                            {include file='v2/campaign/ad_list_types/snippet-tabs.php'}
                        </td>
                        <td class="td_success td20" >
                            <span class="txt_bold" >AD status</span>
                            <p><a class="toggle toggle-light" data-ad-id="{$ad.id}" data-toggle-on="{if $ad.creative_status=='ACTIVE' || $ad.creative_status=='ENABLED'}true{else}false{/if}" data-toggle-drag="{if $ad.creative_status=='ACTIVE'}true{else}false{/if}" data-toggle-click="{if $ad.creative_status=='ACTIVE'}true{else}true{/if}"></a></p>
                        </td>
                    </tr> 
                {/foreach}
            </table>
            {else}
                <div> For EMAIL campaigns the system generated ads automatically </div>
            {/if}
        </div>
    </div>
</div>