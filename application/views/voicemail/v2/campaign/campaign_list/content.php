<div class="theme-pagination-wrap">
    {if !empty($links)}{$links}{/if}
</div>

<div class="theme-report-table-wrap theme-report-table-skin">
    {if empty($campaigns)}
        <div class="alert">Campaigns not found</div>
    {else}
    <table id="theme-sortable-table" class="theme-display-table theme-report-table none_background">
        <thead class="dark_bg">
            <tr class="theme-table-row theme-table-header theme-report-table-row">
                <th class="theme-table-middle-cell theme-report-header-data theme-report-table-id">IO # </th>
                <th class="theme-table-middle-cell theme-report-header-data theme-report-table-name">CAMPAIGN NAME</th>
<!--                <th class="theme-table-middle-cell theme-report-header-data theme-report-table-status">CAMPAIGN STATUS </th>-->
                <th class="theme-table-middle-cell theme-report-header-data theme-report-table-type">CAMPAIGN TYPE</th>
                <th class="theme-table-middle-cell theme-report-header-data theme-report-table-startdate">STARTED DATE</th>
                <th class="theme-table-middle-cell theme-report-header-data theme-report-table-enddate">PROGRESS</th>
                <th class="theme-table-middle-cell theme-report-header-data theme-report-table-manage">MANAGE</th>
            </tr>
        </thead>

        <tbody>
        {foreach from=$campaigns item=campaign}
        {if $campaign.campaign_status=='DISAPPROVED' || $campaign.disapproved_ads_count!=0}
            {if $campaign.campaign_status=='DISAPPROVED'}
                <tr data-placement="top" data-trigger="hover" data-toggle="popover" data-content="<p>{$campaign.disapproval_reasons}</p>" class="theme-table-row theme-table-data-row theme-report-table-row hilight">
            {else}
                <tr data-placement="top" data-trigger="hover" data-toggle="popover" data-content="<p>{$campaign.disapproved_ads_count} ADS are disapproved</p>" class="theme-table-row theme-table-data-row theme-report-table-row hilight_yellow">
            {/if}
        {else if $campaign.unchecked_ads_count!=0}
            <tr data-placement="top" data-trigger="hover" data-toggle="popover" data-content="<p>{$campaign.unchecked_ads_count} ADS ARE UNDER REVIEW PLEASE WAIT.</p>" class="theme-table-row theme-table-data-row theme-report-table-row hilight_gray">
        {else}
            <tr class="theme-table-row theme-table-data-row theme-report-table-row">
        {/if}
                <td class="theme-table-middle-cell theme-report-table-data">{$campaign.io}</td>
                <td class="theme-table-middle-cell theme-report-table-data">{$campaign.name}</td>
<!--                <td class="theme-table-middle-cell theme-report-table-data">{$campaign.campaign_status}</td>-->
                <td class="theme-table-middle-cell theme-report-table-data">{$campaign.campaign_type}</td>
                <td class="theme-table-middle-cell theme-report-table-data">{if !empty($campaign.campaign_start_datetime)}{$campaign.campaign_start_datetime|date_format:"%Y/%m/%d %H:%M"}{/if}</td>
                <td class="theme-table-middle-cell theme-report-table-data">
                    <div class="theme-report-progress progress">
                    {if $campaign.campaign_type == 'EMAIL'}
                        {$percent = (($campaign.total_report.clicks_count+$campaign.total_report.impressions_count)/$campaign.total_clicks)*100}
                        <div class="progress-bar theme-report-progress-bar progress-bar-blue click" role="progressbar" style="width:{$percent}%;">{$percent|string_format:"%.2f"}%</div>
                    {else}
                        {if $user.is_billing_type == 'PERCENTAGE'}

                            {if empty($campaign.max_budget)}
                                {if !empty($campaign.max_clicks)}
                                    {$percent = 100*$campaign.total_clicks_count/$campaign.max_clicks}
                                    {if $percent >= 100}
                                        {$percent = 100}
                                    {/if}
                                    <div class="progress-bar theme-report-progress-bar progress-bar-blue click" role="progressbar" style="width:{$percent}%;">{$percent|string_format:"%.2f"}%</div>
                                {else if !empty($campaign.max_impressions)}
                                    {$percent = 100*$campaign.total_impressions_count/$campaign.max_impressions}
                                    {if $percent >= 100}
                                        {$percent = 100}
                                    {/if}
                                    <div class="progress-bar theme-report-progress-bar progress-bar-blue impr" role="progressbar" style="width:{$percent}%;">{$percent|string_format:"%.2f"}%</div>
                                {else if !empty($campaign.date_diff) && $campaign.persent_diff < $campaign.date_diff}
                                    {$percent = 100*$campaign.persent_diff/$campaign.date_diff}
                                    {if $percent >= 100}
                                        {$percent = 100}
                                    {/if}
                                    <div class="progress-bar theme-report-progress-bar progress-bar-blue date" role="progressbar" style="width:{$percent}%;">{$percent|string_format:"%.2f"}%</div>
                                {/if}
                            {else}
                                {$cost = $campaign.percentage_max_budget - $campaign.cost}

                                {if ($cost>0)}
                                    {$percent_cost = $campaign.cost*100/$campaign.percentage_max_budget}
                                    <div class="progress-bar theme-report-progress-bar progress-bar-blue budg" role="progressbar" style="width:{$percent_cost}%;">{$percent_cost|string_format:"%.2f"}%</div>
                                {else}
                                    <div class="progress-bar theme-report-progress-bar progress-bar-blue budg" role="progressbar" style="width:100%;">100%</div>
                                {/if}
                            {/if}

                        {else}

                            {if !empty($campaign.max_budget)}
                                {$cost = $campaign.percentage_max_budget - $campaign.cost}

                                {if ($cost>0) && $campaign.campaign_status!='COMPLETED'}
                                    {$percent_cost = $campaign.cost*100/$campaign.percentage_max_budget}
                                    <div class="progress-bar theme-report-progress-bar progress-bar-blue budg" role="progressbar" style="width:{$percent_cost}%;">{$percent_cost|string_format:"%.2f"}%</div>
                                {else}
                                    <div class="progress-bar theme-report-progress-bar progress-bar-blue budg" role="progressbar" style="width:100%;">100%</div>
                                {/if}
                            {else if !empty($campaign.max_clicks)}
                                {$percent = 100*$campaign.total_clicks_count/$campaign.max_clicks}
                                {if $percent >= 100}
                                    {$percent = 100}
                                {/if}
                                <div class="progress-bar theme-report-progress-bar progress-bar-blue click" role="progressbar" style="width:{$percent}%;">{$percent|string_format:"%.2f"}%</div>
                            {else if !empty($campaign.max_impressions)}
                                {$percent = 100*$campaign.total_impressions_count/$campaign.max_impressions}
                                {if $percent >= 100}
                                    {$percent = 100}
                                {/if}
                                <div class="progress-bar theme-report-progress-bar progress-bar-blue impr" role="progressbar" style="width:{$percent}%;">{$percent|string_format:"%.2f"}%</div>
                            {else if !empty($campaign.date_diff) && $campaign.persent_diff < $campaign.date_diff}
                                {$percent = 100*$campaign.persent_diff/$campaign.date_diff}
                                {if $percent >= 100}
                                    {$percent = 100}
                                {/if}
                                <div class="progress-bar theme-report-progress-bar progress-bar-blue date" role="progressbar" style="width:{$percent}%;">{$percent|string_format:"%.2f"}%</div>
                            {/if}

                        {/if}
                    {/if}
                    </div>
                </td>
                <td class="theme-table-middle-cell theme-report-table-data">
                    <div class="theme-report-table-edit">
                        {if $user_type != 'viewer' && $campaign.campaign_type != 'EMAIL' && $user.edit_campaign != 'N'}
                        <a href="/v2/campaign/edit_campaign/{$campaign.id}" class="theme-report-table-edit-pencil">
                            <img src="/v2/images/report-template/table-manage-edit-icon.png" alt="" />
                        </a>
                        {/if}

                        <a href="/v2/campaign/{if $campaign.campaign_type == 'EMAIL'}email_{/if}reporting/{$campaign.id}" class="theme-report-table-edit-bar">
                            <img src="/v2/images/report-template/table-manage-bar-icon.png" alt="" />
                        </a>
                        {if $user_type != 'viewer' && $campaign.campaign_type != 'EMAIL'}
                        <a href="/v2/campaign/ad_list/{$campaign.id}" class="theme-report-table-edit-bar" style="margin-left: 10px">
                            ADS
                        </a>
                        {/if}
                    </div>
                </td>
            </tr>
        {/foreach}
        </tbody>

    </table>
    {/if}
</div>

<div class="theme-pagination-wrap">
    {if !empty($links)}{$links}{/if}
</div>