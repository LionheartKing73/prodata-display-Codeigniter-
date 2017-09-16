{include file="v2/sections/header.php"}

<div class="theme-report-row-wrap">
    <div class="theme-container container-fluid">
        <div class="theme-report-campaign-list-row admin_users_table_block">
            {if $users}
                <table id="theme-sortable-table" class="theme-display-table theme-report-table none_background admin_users_table">
                    <thead>
                        <tr class="theme-table-row theme-table-header theme-report-table-row">
                            <th class="theme-table-middle-cell theme-report-header-data">Email</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Email Reporting</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Can Create Campaign</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Can Edit Campaign</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Google Add</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Airpush Add</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Facebook Add</th>
<!--                            <th class="theme-table-middle-cell theme-report-header-data">D+retarget Add</th>-->
                            <th class="theme-table-middle-cell theme-report-header-data">Billing Type</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Imp tier 1</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Imp tier 2</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Imp tier 3</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Click tier 1</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Click tier 2</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Click tier 3</th>
                            <th class="theme-table-middle-cell theme-report-header-data">White Label</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Display Imp</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Display Click</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Min Budget</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Logo</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Edit Networks</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Edit Multiple Networks</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$users item=user}
                            <tr class="theme-table-row theme-table-data-row theme-report-table-row">
                                <td class="theme-table-middle-cell theme-report-table-data">{$user.email}</td>
                                <td class="theme-table-middle-cell theme-report-table-data ">
                                    <a href="#" class="editable" data-type="select" data-pk="{$user.id}" data-url="updateUser" data-title="Email Reporting" data-value="{$user.is_email}" data-name="is_email">
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data ">
                                    <a href="#" class="editable" data-type="select" data-pk="{$user.id}" data-url="updateUser" data-title="Can Create Campaign" data-value="{$user.create_campaign}" data-name="create_campaign">
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="editable" data-type="select" data-pk="{$user.id}" data-url="updateUser" data-title="Can Edit Campaign" data-value="{$user.edit_campaign}" data-name="edit_campaign">
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="editable" data-type="select" data-pk="{$user.id}" data-url="updateUser" data-title="Google Add" data-value="{$user.is_google}" data-name="is_google">
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="editable" data-type="select" data-pk="{$user.id}" data-url="updateUser" data-title="Airpush Add" data-value="{$user.is_airpush}" data-name="is_airpush">
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="editable" data-type="select" data-pk="{$user.id}" data-url="updateUser" data-title="Facebook Add" data-value="{$user.is_facebook}" data-name="is_facebook">
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="user_type" data-type="select" data-pk="{$user.id}" data-url="updateUser" data-title="User Type" data-value="{$user.is_billing_type}" data-name="is_billing_type">
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="int_editable" data-type="text" data-pk="{$user.id}" data-url="updateUser" data-title="Imp Tier 1" data-name="display_imp_tier_1">
                                        {$user.display_imp_tier_1}                                
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="int_editable" data-type="text" data-pk="{$user.id}" data-url="updateUser" data-title="Imp Tier 2" data-name="display_imp_tier_2">
                                        {$user.display_imp_tier_2}
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="int_editable" data-type="text" data-pk="{$user.id}" data-url="updateUser" data-title="Imp Tier 3" data-name="display_imp_tier_3">
                                        {$user.display_imp_tier_3}
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="int_editable" data-type="text" data-pk="{$user.id}" data-url="updateUser" data-title="Click Tier 1" data-name="display_click_tier_1">
                                        {$user.display_click_tier_1}
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="int_editable" data-type="text" data-pk="{$user.id}" data-url="updateUser" data-title="Click Tier 2" data-name="display_click_tier_2">
                                        {$user.display_click_tier_2}
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="int_editable" data-type="text" data-pk="{$user.id}" data-url="updateUser" data-title="Click Tier 3" data-name="display_click_tier_3">
                                        {$user.display_click_tier_3}
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="user_domain" data-type="select" data-pk="{$user.id}" data-url="updateUser" data-title="White Label" data-value="{$user.domain_id}" data-name="domain_id">
                                    </a>
                                </td>

                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="int_editable" data-type="text" data-pk="{$user.id}" data-url="updateUser" data-title="Display Imp"  data-name="display_imp">
                                        {$user.display_imp}
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="int_editable" data-type="text"  data-pk="{$user.id}" data-url="updateUser" data-title="Display Click"  data-name="display_click">
                                        {$user.display_click}
                                    </a>
                                </td>

                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="editable min_budget" data-type="text"  data-pk="{$user.id}" data-url="updateUser" data-title="Min Budget"  data-name="min_budget">
                                        {$user.min_budget}
                                    </a>
                                </td>

                                <td class="theme-table-middle-cell theme-report-table-data" user_id = {$user.id}>
                                    {if $user.domain_id}
                                        <img src='/v2/images/domain_logos/{$user.logo}'/>
                                    {/if}
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data" >
                                    <a href="/v2/admin/edit_networks/{$user.id}" >Edit Networks</a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data" >
                                    <a href="/v2/admin/edit_multiple_networks/{$user.id}" >Edit Multiple Networks</a>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            {else}
            {/if}
        </div>
    </div>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script src="/v2/js/jquery-2.0.3.min.js"></script> 
<!-- Include all compiled plugins (below), or include individual files as needed --> 
<script src="/v2/js/bootstrap.min.js"></script>
<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script>
    var domain_arr = JSON.parse('{$domains|@json_encode}');
</script>
<script src="/v2/js/admin/users.js"></script>
</body>
</html>