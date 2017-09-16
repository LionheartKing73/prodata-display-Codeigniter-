{include file="v2/sections/header.php"}

<div class="theme-report-row-wrap">
    <div class="theme-container container-fluid">
        <div class="theme-report-campaign-list-row admin_users_table_block">

            <table id="theme-sortable-table" class="theme-display-table theme-report-table none_background ">
                <thead>
                <tr class="theme-table-row theme-table-header theme-report-table-row">
                    <th class="theme-table-middle-cell theme-report-header-data">Name</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Status Active</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Has Group</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Bid</th>
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
                        <a href="#" class="int_editable editable editable-click" data-type="text" data-pk="{$network.id}" data-url="update_bid" data-title="Bid" data-name="bid" data-value="{$network.bid}" >

                        </a>
                    </td>

                </tr>
                {/foreach}
                </tbody>
            </table>

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
<script src="/v2/js/admin/network_management.js"></script>
</body>
</html>