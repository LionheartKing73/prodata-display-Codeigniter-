{include file="v2/sections/header.php"}
<section class="theme-report-row-wrap theme-container container-height r-container" id="wrap">
        <div class="theme-report-campaign-list-row mobile-container" id="content">
            <a href="/v2/admin/add_domain" class="btn btn-success pull-right btn_margin tbl-margin-bottom">Add New Domain</a>
            {if $domains}
                <table id="theme-sortable-table" class="theme-display-table theme-report-table none_background">
                    <thead>
                        <tr class="theme-table-row td-font-size theme-table-header theme-report-table-row">
                            <th class="theme-table-middle-cell th-bg theme-report-header-data">Name</th>
                            <th class="theme-table-middle-cell th-bg theme-report-header-data">Logo</th>
                            <th class="theme-table-middle-cell th-bg theme-report-header-data">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$domains item=domain}
                            <tr class="theme-table-row td-font-size theme-table-data-row theme-report-table-row">
                                <td class="theme-table-middle-cell td-bg theme-report-table-data">{$domain.name}</td>
                                <td class="theme-table-middle-cell theme-report-table-data">
                                    {if $domain.logo}
                                    <img src="/v2/images/domain_logos/{$domain.logo}" class="img-responsive"/>
                                    {/if}
                                </td>
                                <td class="theme-table-middle-cell theme-report-table-data td-float">
                                    <a href="/v2/admin/edit_domain/{$domain.id}" class="btn btn-info">
                                        Edit
                                    </a>
                                    <a href="/v2/admin/delete_domain/{$domain.id}" class="btn btn-danger">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            {else}
            {/if}
    </div>
</section>
{include file="v2/sections/footer.php"}
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script src="/v2/js/jquery-2.0.3.min.js"></script> 
<!-- Include all compiled plugins (below), or include individual files as needed --> 
<script src="/v2/js/bootstrap.min.js"></script>

</body>
</html>