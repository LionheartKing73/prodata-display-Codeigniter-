{include file="v2/sections/header.php"}
<section class="theme-container r-container" id="wrap">
    <div class="alert alert-error" id="err_bof" style="display:none;">
        <a class="close" data-dismiss="alert">X</a>
        <strong id="err_bof_message"></strong>
    </div>
    <div class="alert alert-success" id="success_bof" style="display:none;">
        <a class="close" data-dismiss="alert">X</a>
        <strong id="success_bof_message"></strong>
    </div>
    <!-- Example row of columns -->
    <div class="theme-report-campaign-list-row mobile-container" id="r-content">
        <div class="span12">
            <div style="width: 38%; display: inline-block;">
                <h3>Omni Channel Campaign Report</h3>
                <br>
                <br>
                <button class="btn btn-primary pdf_export" >Export PDF</button>
            </div>
            <div style="width: 58%;  display: inline-block">
                <form class="form-inline" style="width: 58%;  display: inline-block; margin-right: 10%;">
                    <div class="form-group" style="width: 100%;">
                        <label class="range_picke_head">Date Range</label>
                        <input type="text" id="reportrange" class="form-control">
                    </div>
                </form>
                <form class="pull-right bid-camp-status" method="post" id="form_reporting" style="width: 28%;  display: inline-block">
                    <select name="so" id="status" class="form-control input-medium" onchange="this.form.submit()">
                        <option value="" {if empty($so)} selected="selected" {/if}>Select SO number </option>
                        {foreach from=$so_numbers item=item}
                            <option value="{$item.so}" {if !empty($so) && ($so == $item.so)} selected="selected" {/if}> {$item.so}</option>
                        {/foreach}
                    </select>
                    <input type="hidden" id="form_start_date" name="start_date" value="{$js_start_data}">
                    <input type="hidden" id="form_end_date" name="end_date" value="{$js_end_data}">
                </form>
            </div>
            <div class="campaignProg">
                <div id="theme-area-chart-holder" class="theme-area-chart-holder">
                    <div id="curtain"><i class="fa fa-spinner fa-spin" style="font-size:75px;
                    color:black;top:100px; left:-45px;"></i></div>            
                </div>
                <!-- <i class="glyphicon glyphicon-calendar fa fa-calendar icon_calendar"></i> -->
            </div>
            <hr>

            <div class="table-responsive">
                <table class="table tracking_report_table table-bordered table-striped" id="mytable">
                    <thead class="dark_bg">
                    <tr>
                        <th width="30%">Chanel Name</th>
                        <th width="35%">Current Actions</th>
                        <th width="35%">Current Impressions</th>
                    </tr>
                    </thead>
                    <tbody>
                    
                    {$total_cost = 0}
                    {$total_max_budget = 0}
                    {$total_max_clicks = 0}
                    {$total_max_impressions = 0}
                    {$limit_max_clicks = 0}
                    {$limit_max_impressions = 0}

                    {foreach from=$data key=k item=c}
                    {*
                    {$count = 0}
                    {if !empty($c.max_budget)}
                    {$count = $count + 1}
                    {$cost = $c.percentage_max_budget - $c.cost}
                    {if ($cost>0)}
                    {$percent_cost = $c.cost*100/$c.percentage_max_budget}
                    {else}
                    {$percent_cost = 100}
                    {/if}
                    {/if}
                    {if !empty($c.max_clicks)}
                    {$percent_clicks = 100*$c.total_clicks_count/$c.max_clicks}
                    {if $percent_clicks >= 100}
                    {$percent_clicks = 100}
                    {/if}
                    {$count = $count +1}
                    {else}
                    {$percent_clicks = 0}
                    {/if}
                    {if !empty($c.max_impressions)}
                    {$percent_impressions = 100*$c.total_impressions_count/$c.max_impressions}
                    {if $percent_impressions >= 100}
                    {$percent_impressions = 100}
                    {/if}
                    {$count = $count + 1}
                    {else}
                    {$percent_impressions = 0}
                    {/if}


                    {if !empty($c.date_diff) && $c.percent_diff <= $c.date_diff}
                    {$percent_date = 100*$c.percent_diff/$c.date_diff}

                    {if $percent_date >= 100}
                    {$percent_date = 100}
                    {/if}
                    {$count = $count + 1}
                    {else}
                    {$percent_date = 0}
                    {/if}
                    {$percent = ($percent_cost + $percent_clicks + $percent_impressions + $percent_date)/$count}
                    {$class=''}
                    {if $percent_date>=50 && $percent_date<80}
                    {if $percent<50}
                    {$class='yellow_row'}
                    {/if}
                    {else if $percent_date>=80}
                    {if $percent<80}
                    {$class='red_row'}
                    {/if}
                    {/if} *}
                    <tr id="id_{$c.id}" class="io {$c.date_diff} {$c.percent_diff}" data-id="{$c.id}" data-status="{$c.network_campaign_status}">
                        <td width="30%">{$k}</td>
                        <td width="35%" id="click_{$k}">{if !empty($c.clicks_count)} {$c.clicks_count}{else}-{/if}  </td>
                        <td width="35%" id="impressions_{$k}" >{if !empty($c.impressions_count)} {$c.impressions_count}{else}-{/if} </td>
                    </tr>
                    {/foreach}
                    </tbody>
             </table>
            </div>
            <hr>
            <div>
                <div class="users-gender" style="display: inline-block; width: 48%; margin-right: 2%;">
                    <h2 class="text-center" style="margin-bottom: 15px;">Impressions</h2>
                    <div id="total_impressions" class="theme-area-chart-holder" style="height: 380px;"></div>
                </div>
                <div class="users-gender" style="display: inline-block; width: 48%;">
                    <h2 class="text-center" style="margin-bottom: 15px;">Clicks</h2>
                    <div id="total_clicks" class="theme-area-chart-holder" style="height: 380px;"></div>
                </div>
            </div>
            <div>
                <div class="users-gender" style="display: inline-block; width: 49%; margin-right: 1%;">
                    <h2  class="text-center">Users Gender</h2>
                    <div id="users_gender" class="theme-area-chart-holder" style="height: 380px;"></div>
                </div>

                <div class="users-gender" style="display: inline-block; width: 48%; ">
                    <h2 class="text-center" style="margin-bottom: 15px;">Users Age</h2>
                    <div id="age_chart" class="theme-area-chart-holder" style="height: 390px;"></div>
                </div>
            </div>
            <div class="users-gender" style="display: inline-block; width: 70%; margin-left: 180px">
                <h2 class="text-center" style="margin-bottom: 15px;">Placement</h2>
                <div id="placement_chart" class="theme-area-chart-holder" style="height: 480px;"></div>
            </div>
        </div>
        <div class="table-responsive">
                <table class="table tracking_report_table table-bordered table-striped" id="table_all">
                    <thead class ="bg-primary" style="background-color:#333333;color:#fff;">
                    <tr>
                        <th width="25%">IO</th>
                        <th width="25%">Name</th>
                        <th width="25%">Type</th>
                        <th width="25%">Campaign Status</th>
                    </tr>
                    </thead>
                <tbody>
                </tbody>
             </table>
             <div class="theme-pagination-wrap" style= "display: none;">
                {if !empty($links)}{$links}{/if}
            </div>
        </div>
    </div>
</section>
<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/serial.js"></script>
<script src="https://www.amcharts.com/lib/3/pie.js"></script>
<script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
{include file="v2/sections/footer.php"}
{include file="v2/sections/scripts.php"}
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
</section>
</main>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<!-- Include Date Range Picker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<script src="/v2/js/amcharts/amcharts.js"></script>
<script src="/v2/js/amcharts/serial.js"></script>
<script src="/v2/js/amcharts/themes/light.js"></script>
<script src="/v2/js/amcharts/pie.js"></script>
<script>

    var js_data = {
        start_date: '{$start_date}',
        date_now: '{$date_now}',
        js_date_now: '{$js_date_now}',
        js_start_data: '{$js_start_data}',
        so: '{$so}'
    };
    var pie_data = JSON.parse('{$donats_chart_data|@json_encode}');


</script>

<script src="/v2/js/combine_reporting.js"></script>
<script src="/v2/js/amcharts/plugins/responsive/responsive.min.js"></script>

    <script src="/v2/js/bootstrap.min.js"></script>
    <script src="/v2/js/jquery.tablesorter.min.js"></script>
    <script src="/v2/js/tableExport.js"></script>
    <script src="/v2/js/jquery.base64.js"></script>
    <script>
        {literal}
        $(document).ready(function() {
            $("#mytable").tablesorter({
            });
            $("#csv_b").click(function () {
                $('#mytable').tableExport({type:'csv',escape:'false',tableName:'test'});
            });
        });
        {/literal}
    </script>
</body>
</html>
