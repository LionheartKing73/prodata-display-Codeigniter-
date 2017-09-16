{include file="v2/sections/header.php"}

<div class="theme-report-row-wrap" id="wrap">
    <div class="theme-container mobile-container container-fluid"  id="content">
        <div class="r-container admin_users_table_block table-responsive">

            <table id="theme-sortable-table" class="table theme-report-table none_background ">
                <thead>
                <tr class="theme-table-row theme-table-header theme-report-table-row e-mult-net">
                    <th class="theme-table-middle-cell theme-report-header-data">Name</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Status Active</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Has Group</th>
                    <th class="theme-table-middle-cell theme-report-header-data">CPC Bid</th>
                    <th class="theme-table-middle-cell theme-report-header-data">CPM Bid</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$networks item=network}
                <tr class="theme-table-row theme-table-data-row theme-report-table-row">
                    <td class="theme-table-middle-cell theme-report-table-data">{$network.name}</td>
                    <td class="theme-table-middle-cell theme-report-table-data for_status" >
                        {if $network.is_active == 'Y'}
                            YES
                        {else}
                            NO
                        {/if}
                    </td>
                    <td class="theme-table-middle-cell theme-report-table-data for_status">
                        {if $network.has_group == 'Y'}
                        YES
                        {else}
                        NO
                        {/if}
                    </td>
                    <td class="theme-table-middle-cell theme-report-table-data">
                        <a href="#" class="int_editable editable editable-click" data-type="text" data-pk="{$network.id}" data-url="update_bid" data-title="Bid" data-name="bid" data-value="{$network.bid}" ></a>
                    </td>
                    <td class="theme-table-middle-cell theme-report-table-data">
                        <a href="#" class="int_editable editable editable-click" data-type="text" data-pk="{$network.id}" data-url="update_cpm_bid" data-title="CPM Bid" data-name="cpm_bid" data-value="{$network.cpm_bid}" ></a>
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
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/v2/js/bootstrap.min.js"></script>
<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script>
    var domain_arr = JSON.parse('{$domains|@json_encode}');
</script>
<script src="/v2/js/admin/network_management.js"></script>
</body>
</html>