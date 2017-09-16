{include file="v2/sections/header.php"}

<div class="theme-report-row-wrap" id="wrap">
    <div class="theme-container mobile-container container-fluid" id="r-content">
        <div class=" ">
            <a href="/auth/create_user" class="btn btn-primary">Create User</a>
            <button id="create_financial_manager" class="btn btn-primary btn_create_financial_manager">Create Financial Manager</button>
            <button id="create_account_ownership" class="btn btn-primary btn_create_account_ownership">Create Account ownership</button>
            <section class="container-fluid section-viewer">
                <div class="container">
                    <div class="row">
<!--                        <div class="createviewer-info">-->
<!--                            <button class="btn btn-primary btn_create_financial_manager">Create Financial Manager</button>-->
<!--                        </div>-->
                        <div class="createviewer-info-form create_financial_manager">
                            <form method="post">
                                <div class="row">
                                    <div class="col-md-3  col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label>First Name</label>
                                            <input name="viewer_name" type="text" class="form-control" id="vfname" placeholder="Viewer Name">
                                        </div>
                                    </div>
                                    <div class="col-md-3  col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label>Viewer Email</label>
                                            <input type="email" name="viewer_email" class="form-control viewer_email_input" id="vemail" placeholder="Viewer Email">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input name="viewer_pass" type="password" class="form-control viewer_pass_input" id="vpass" placeholder="Password">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label>Repeat New Password</label>
                                            <input name="repeat_viewer_pass" type="password" class="form-control viewer_pass_input check_repeat_viewer_pass" id="vnpass" placeholder="Repeat New Password">
                                        </div>
                                    </div>
                                    <input type="hidden" value="financial_manager" name="type">
                                </div>
                                <button class="btn btn-success button_create_user" name="create_viewer" />Create Financial Manager</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
            <section class="container-fluid section-viewer">
                <div class="">
                    <div class="row">
                        <!--                        <div class="createviewer-info">-->
                        <!--                            <button class="btn btn-primary btn_create_financial_manager">Create Financial Manager</button>-->
                        <!--                        </div>-->
                        <div class="createviewer-info-form create_account_ownership">
                            <form method="post">
                                <div class="row">
                                    <div class="col-md-3  col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label>First Name</label>
                                            <input name="viewer_name" type="text" class="form-control" id="vfname" placeholder="Viewer Name">
                                        </div>
                                    </div>
                                    <div class="col-md-3  col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label>Viewer Email</label>
                                            <input type="email" name="viewer_email" class="form-control viewer_email_input" id="vemail" placeholder="Viewer Email">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input name="viewer_pass" type="password" class="form-control viewer_pass_input" id="vpass" placeholder="Password">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label>Repeat New Password</label>
                                            <input name="repeat_viewer_pass" type="password" class="form-control viewer_pass_input check_repeat_viewer_pass" id="vnpass" placeholder="Repeat New Password">
                                        </div>
                                    </div>
                                    <input type="hidden" value="account_ownership" name="type">
                                </div>
                                <button class="btn btn-success button_create_user" name="create_viewer" />Create Account Ownership</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
            {if $users}
            <div class=" table-responsive">
                <table id="theme-sortable-table" class="table theme-display-table theme-report-table none_background admin_users_table">
                    <thead>
                        <tr class="theme-table-row theme-table-header theme-report-table-row user-man">
                            <th class="theme-table-middle-cell theme-report-header-data">Email</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Email Reporting</th>
                            <th class="theme-table-middle-cell theme-report-header-data">User email ability</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Can Create Campaign</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Can Edit Campaign</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Google Add</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Airpush Add</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Facebook Add</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Quickbook Invoicing</th>
                            <th class="theme-table-middle-cell theme-report-header-data">On/Off Billing</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Ad Track</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Guarantee</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Guarantee Percentage</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Guarantee Upcharge</th>
                            <th class="theme-table-middle-cell theme-report-header-data">White Label</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Billing Type</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Imp tier 1</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Imp tier 2</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Imp tier 3</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Click tier 1</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Click tier 2</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Click tier 3</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Display Imp</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Display Click</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Min Budget</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Budget Percent</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Can Extend Campaigns</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Logo</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Edit Networks</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Edit Multiple Networks</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Edit Users</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Assign Financial Manager</th>
                            <th class="theme-table-middle-cell theme-report-header-data">Assign Account Ownership</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$users item=user}
                            {if $user.user_type == 'customer'}
                            <tr class="theme-table-row theme-table-data-row theme-report-table-row user-man">
                                <td class="theme-table-middle-cell theme-report-table-data">{$user.email}</td>
                                <td class="theme-table-middle-cell theme-report-table-data ">
                                    <a href="#" class="editable u-empty-editable" data-type="select" data-pk="{$user.id}" data-url="updateUser" data-title="Email Reporting" data-value="{$user.is_email}" data-name="is_email">
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data ">
                                    <a href="#" class="editable camp_edit" data-type="select" data-pk="{$user.id}" data-url="updateUser" data-title="Can Get Email" data-value="{$user.user_email_ability}" data-name="user_email_ability">
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data ">
                                    <a href="#" class="editable camp_edit" data-type="select" data-pk="{$user.id}" data-url="updateUser" data-title="Can Create Campaign" data-value="{$user.create_campaign}" data-name="create_campaign">
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
                                    <a href="#" class="editable" data-type="select" data-pk="{$user.id}" data-url="updateUser" data-title="Quickbook Invoicing" data-value="{$user.is_qb_invoicing}" data-name="is_qb_invoicing">
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="editable" data-type="select" data-pk="{$user.id}" data-url="updateUser" data-title="Turn On/Off billing" data-value="{$user.is_billing}" data-name="is_billing">
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="editable" data-type="select" data-pk="{$user.id}" data-url="updateUser" data-title="Guarantee" data-value="{$user.is_adtrack}" data-name="is_adtrack">
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="editable" data-type="select" data-pk="{$user.id}" data-url="updateUser" data-title="User Type" data-value="{$user.is_guarantee}" data-name="is_guarantee">
                                    </a>
                                </td>
                                  <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="int_editable" data-type="text" data-pk="{$user.id}" data-url="updateUser" data-title="Guarantee Percentage" data-name="is_guarantee_percentage">
                                        {$user.is_guarantee_percentage}
                                    </a> <spam style="color:#084d8d;">%</spam>
                                </td>
                                 <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="int_editable" data-type="text" data-pk="{$user.id}" data-url="updateUser" data-title="Guarantee upcharge" data-name="is_guarantee_upcharge">
                                        {$user.is_guarantee_upcharge}
                                    </a> <spam style="color:#084d8d;">%</spam>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="editable" data-type="text" data-pk="{$user.id}" data-url="updateUser" data-title="White Label" data-value="{$user.is_branding}" data-name="is_branding">
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
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="editable budget_percentage" data-type="text"  data-pk="{$user.id}" data-url="updateUser" data-title="Percentage Budget"  data-name="budget_percentage">
                                        {$user.budget_percentage}
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    <a href="#" class="editable" data-type="select"  data-pk="{$user.id}" data-url="updateUser" data-title="Can Extend Campaigns" data-value="{$user.can_extend_campaigns}"  data-name="can_extend_campaigns">
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data" user_id = {$user.id}>
                                    {if $user.domain_id}
                                        <img src='/v2/images/domain_logos/{$user.logo}' class="img-responsive"/>
                                    {/if}
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data" >
                                    <a href="/v2/admin/edit_networks/{$user.id}" class="editNet">Edit Networks</a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data" >
                                    <a href="/v2/admin/edit_multiple_networks/{$user.id}" class="editNet">Edit Multiple Networks</a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data" >
                                    <a href="/auth/edit_user/{$user.id}" class="editNet">Edit User</a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data" >
                                    <a href="#" class="assign_manager_to_user" data-type="select" data-pk="{$user.id}" data-url="updateUser" data-title="Financial Managers" data-value="{$user.financial_manager_id}" data-name="financial_manager_id">
                                    </a>
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data" >
                                    <a href="#" class="assign_ownership_to_user" data-type="select" data-pk="{$user.id}" data-url="updateUser" data-title="Accounting Ownerships" data-value="{$user.accounting_ownership_id}" data-name="accounting_ownership_id">
                                    </a>
                                </td>
                            </tr>
                            {/if}
                        {/foreach}
                    </tbody>
                </table>
            </div>
            {else}
            {/if}
        </div>
    </div>
</div>
{include file="v2/sections/footer.php"}
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="/v2/js/jquery-2.0.3.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/v2/js/bootstrap.min.js"></script>
<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script>
    var domain_arr = JSON.parse('{$domains|@json_encode}');
    var users_arr = JSON.parse('{$users|@json_encode|replace:"'":"\'"}');
</script>
<script src="/v2/js/admin/users.js"></script>
</body>
</html>
