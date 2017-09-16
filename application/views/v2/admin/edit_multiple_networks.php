{include file="v2/sections/header.php"}
<div class="theme-report-row-wrap" id="wrap">
    <div class="theme-container container-fluid mobile-container" id="content">
        <div class="pull-right" >
            <button id="btn_add_network" type="submit" form="add_network_form" class="btn btn-success" >Add</button>
        </div>
        <div class="row network_add_row" >

            <form id="add_network_form" class="mult-net">
                <input type="hidden" value="{$network_user}" name="user_id" />
                <div class="col-sm-4" >
                    <label>Budget</label>
                    <input type="text" maxlength="20" value="" name="percent_of_budget" class="form-control" placeholder="PERCENTAGE BUDGET"/>
                </div>
                <div class="col-sm-4" >
                    <label>Default Network</label>
                    <select name="general_network_id" class="form-control general-net" >
                        {foreach from=$networks item=network}
                        <option value="{$network.id}">{$network.name}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="col-sm-4" >
                    <label>Additional Networks</label>
                    <select name="multiple_networks_ids[]" class="form-control theme-multi-selectbox theme-control general-net">
                        {foreach from=$networks item=network}
                        <option value="{$network.id}">{$network.name}</option>
                        {/foreach}
                    </select>
                </div>
            </form>
        </div>
        <div class="col-sm-9" >
            <ol class="instruction">
                <li><span class="list1"></span>Percentage Budget is between 1 and 100 (e.g. $10 of $100 budget) for that particular network split</li>
                <li><span class="list2"></span>Select the “Default Network” for campaigns</li>
                <li><span class="list3"></span>Select the “Additional Network” for campaigns</li>
            </ol>
        </div>
        <div class="col-sm-4">
            <form class="" style="margin-bottom: 20px" method="get" action="/v2/admin/edit_multiple_networks">
                <label>Select USER</label>
                <select name="user_id" id="user_id" class="form-control general-net" onchange="this.form.submit()">
                    {foreach from=$users item=user}
                        {if $user.user_type=='customer'}
                            <option value="{$user.id}" {if $network_user == $user.id} selected {/if}>{$user.username}</option>
                        {/if}
                    {/foreach}
                </select>
            </form>
        </div>
        <div class="admin_users_table_block table-responsive">
            <table id="theme-sortable-table" class="table theme-report-table  ">
                <thead>
                <tr class="theme-table-row theme-table-header theme-report-table-row e-mult-net">
                    <th class="theme-table-middle-cell theme-report-header-data">Id</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Status Active</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Default Network</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Additional Network</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Percentage budget</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Actions</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$multiple_networks item=network}
                <tr class="theme-table-row theme-table-data-row theme-report-table-row">
                    <td class="theme-table-middle-cell theme-report-table-data">{$network.id}</td>
                    <td class="theme-table-middle-cell theme-report-table-data for_status" >
                        {if $network.is_active == 1}
                            YES
                        {else}
                            NO
                        {/if}
                    </td>
                    <td class="theme-table-middle-cell theme-report-table-data for_status">
                        {$networks[($network.general_network_id-1)]['name']}
                    </td>
                    <td class="theme-table-middle-cell theme-report-table-data for_status">
                        {$networks[($network.multiple_network_id-1)]['name']}
                    </td>

                    <td class="theme-table-middle-cell theme-report-table-data">
                        <a href="#" class="int_editable editable editable-click" data-type="text" data-pk="{$network.id}" data-url="/v2/admin/update_percentage_budget" data-title="percentage_budget" data-name="percentage_budget" data-value="{$network.percent_of_budget}" >

                        </a>
                    </td>
                    <td class="theme-table-middle-cell theme-report-table-data">
                        <span data-id="{$network.id}" class="glyphicon glyphicon-remove remove_network" ></span>
                    </td>

                </tr>
                {/foreach}
                </tbody>
            </table>

        </div>
    </div>
</div>
{include file="v2/sections/footer.php"}

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="/v2/js/jquery-2.0.3.min.js"></script>
<script src="/v2/js/jquery.validate.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/v2/js/bootstrap.min.js"></script>
<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script src="/v2/js/admin/edit_multiple_network.js"></script>
