{include file="adword/header.php"}
{include file="display_ad_list/view_links.php"}
<div class="container">
    <!-- Example row of columns -->
    <div class="row">
        <div class="span12 margin-bottom-40">
            <h2 class="margin-left-20">{$io[0]["io"]} - {$io[0]["campaign"]}</h2>

            <div class="session-block">
                {if $this->session->flashdata('success')}
                <div class="alert alert-success text-center"> {$this->session->flashdata("success")} </div>
                {elseif $this->session->flashdata('error')}
                <div class="alert alert-danger text-center"> {$this->session->flashdata("error")} </div>
                {/if}
            </div>

<h5>
            <table class="table table-margins">
                <tr>
                    <td> Campaign Started: </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;{if $io[0]["status"] eq "scheduled"} {date("Y-m-d", strtotime($io[0]["date"]))} {else}{$io[0]["date_created"]}{/if}&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td> End Criteria: </td>
                    {if $io[0]["max_clicks"] neq 0}<td>&nbsp;&nbsp;&nbsp;&nbsp;Clicks</td>{/if} {if $io[0]["max_impressions"] neq 0}<td>&nbsp;&nbsp;&nbsp;&nbsp;Impressions</td>{/if}
                    {if $io[0]["max_spend"] neq 0}<td>&nbsp;&nbsp;&nbsp;&nbsp;Budget</td>{/if} {if $io[0]["end_date"] neq 0}<td>&nbsp;&nbsp;&nbsp;&nbsp;{$io[0]["end_date"]}</td>{/if}
                </tr>
                <tr>
                    <td>Campaign Totals:</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;Clicks: {$io[0]["clicks"]} / {$io[0]["max_clicks"]}</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;Impressions: {$io[0]["impressions"]} /  {$io[0]["max_impressions"]}</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;Budget: ${number_format($io[0]["spend"]/1000000,  2, ".", " ")} / ${number_format($io[0]["max_spend"]/1000000,  2, ".", " ")}</td>
                </tr>
                </table>
</h5>


                <div class="">
                    <div class="btn-group" style="margin-bottom: 20px">
                        <a class="btn {if $range == 'hour'}btn-inverse{/if}" href="{$base_url}displayAdList/view?group_id={$io[0]['id']}&range=hour">24 Hours</a>
                        <a class="btn {if $range == 'month'}btn-inverse{/if}" href="{$base_url}displayAdList/view?group_id={$io[0]['id']}&range=month">Last 30 days</a>
                        <a id="dt-range-selector" class="btn {if $range == 'range'}btn-inverse{/if}">Date Range</a>
                    </div>

                    <div id="date-selection-form" style="display: none; margin:15px 0">
                            <input type="text" size="25" name="sDate" id="startDate" placeholder="Start Date" onblur="if(this.value=='') this.value='Start Date'" onfocus="if(this.value=='Start Date') this.value= ''" />
                            <input type="text" size="25" name="eDate" id="endDate" placeholder="End Date" onblur="if(this.value=='') this.value='End Date'" onfocus="if(this.value=='End Date') this.value= ''"  />
                            <a class="btn btn-info " id="date-range-search" href="{$base_url}displayAdList/view?group_id={$io[0]['id']}&range=range">Filter</a>
                    </div>

                    <h2>Click Graph</h2>
                    <table class="table table-bordered table-striped">
                        <tbody>

                        <tr>
                            <td colspan="2"><div id="container-linechat" style="height: 500px; width: 100%"></div></td>
                        </tr>

                        <tr>
                            <td>Total Clicks / Total Impressions / Total Budget</td>
                            <td>{$report["total_clicks"]} / {$report["total_impressions"]} / ${number_format($report["total_cost"]/1000000, 2, ".", " ")}</td>
                        </tr>
                        </tbody>
                    </table>

<br /><br />



            <a class="btn" id="create" href="{$base_url}displayAdList/create_new_ad?id={$io[0]['id']}">Create New Ad</a>

            <ul class="nav nav-tabs" id="campaignTabs">
                <li class="active"><a href="#approved" data-toggle="tab">Approved</a></li>
                <li><a href="#disapproved" data-toggle="tab">Disapproved</a></li>
            </ul>

            <div class="tab-content">

                <div class="tab-pane active" id="approved">
                    <table class="table table-bordered table-striped" id="mytable-approved">
                        <thead>
                        <tr>
                            <th class="width-13">Ad Image</th>
                            <th class="width-19">Display URL</th>
                            <th class="width-19">Destination URL</th>
                            <th class="width-7">Clicks</th>
                            <th class="width-11">Impressions</th>
                            <th class="width-13">Budget</th>
                            <th class="width-18">Status</th>
                            <th class="width-33">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        {for $k=0 to count($ad_lists)-1}
                        {if $ad_lists[$k]["approval_status"] neq "DISAPPROVED"}
                        <tr id="{$ad_lists[$k]['id']}">
                            <td class="img-td">

                               <img  data-toggle="popover" rel="popover"
                                        src="{$base_url}uploads/permanent/{$ad_lists[$k]['img_name']}" alt="Ad Image" class="photo"/>

                                <div style="display: none" class="popper-content popover-div">
                                   <img src="{$base_url}uploads/permanent/{$ad_lists[$k]['img_name']}"
                                              alt="Ad Image"  />
                                </div>
                            </td>
                            <td>{$ad_lists[$k]["display_url"]}</td>
                            <td>{$ad_lists[$k]["destination_url"]}</td>
                            <td>{if isset($ad_lists[$k]["clicks"])}{$ad_lists[$k]["clicks"]}{else}0{/if}</td>
                            <td>{if isset($ad_lists[$k]["impressions"])}{$ad_lists[$k]["impressions"]}{else}0{/if}</td>
                            <td>${if isset($ad_lists[$k]["cost"])}{number_format($ad_lists[$k]["cost"], 2, ".", " ")}{else}0.00{/if}</td>
                            <td>{$ad_lists[$k]["ad_status"]}</td>
                            <td >
<!--                                <a href="{$base_url}displayAdList/edit_ad?id={$ad_lists[$k]['id']}"><i class="icon-edit"></i></a>-->
                                <a href="{$base_url}displayAdList/delete_ad?id={$ad_lists[$k]['id']}&status={$io[0]['status']}" class="delete" ><i class="icon-trash"></i></a>
                            </td>
                        </tr>
                        {/if}
                        {/for}
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane" id="disapproved">
                    <table class="table table-bordered table-striped" id="mytable_disapproved">
                        <thead>
                        <tr>
                            <th class="width-12">Ad Image</th>
                            <th class="width-17">Display URL</th>
                            <th class="width-17">Destination URL</th>
                            <th class="width-7">Clicks</th>
                            <th class="width-11">Impressions</th>
                            <th class="width-12">Budget</th>
                            <th class="width-8">Status</th>
                            <th class="width-12">Disapproval Reasons</th>
                            <th class="width-33">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        {for $k=0 to count($ad_lists)-1}
                        {if $ad_lists[$k]["approval_status"] eq "DISAPPROVED"}
                        <tr id="{$ad_lists[$k]['id']}">
                            <td class="img-td">
                                <img  data-toggle="popover" rel="popover"
                                      src="{$base_url}uploads/disapproved/{$ad_lists[$k]['img_name']}" alt="Ad Image" class="photo"/>

                                <div style="display: none" class="popper-content">
                                    <div><img src="{$base_url}uploads/disapproved/{$ad_lists[$k]['img_name']}"
                                              alt="Ad Image" /></div>
                                </div>
                            </td>
                            <td>{$ad_lists[$k]["display_url"]}</td>
                            <td>{$ad_lists[$k]["destination_url"]}</td>
                            <td>{if isset($ad_lists[$k]["clicks"])}{$ad_lists[$k]["clicks"]}{else}0{/if}</td>
                            <td>{if isset($ad_lists[$k]["impressions"])}{$ad_lists[$k]["impressions"]}{else}0{/if}</td>
                            <td>${if isset($ad_lists[$k]["cost"])}{number_format($ad_lists[$k]["cost"], 2, ".", " ")}{else}0.00{/if}</td>
                            <td>{$ad_lists[$k]["ad_status"]}</td>
                            <td>{$ad_lists[$k]["disapproval_reasons"]}</td>
                            <td>
<!--                                <a href="{$base_url}displayAdList/edit_ad?id={$ad_lists[$k]['id']}"><i class="icon-edit"></i></a>-->
                                <a href="{$base_url}displayAdList/delete_ad?id={$ad_lists[$k]['id']}&status={$io[0]['status']}" class="delete"><i class="icon-trash"></i></a>
                            </td>
                        </tr>
                        {/if}
                        {/for}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



    <script src="/public/js/ad_view.js" type="text/javascript"></script>
        <script src="https://code.highcharts.com/highcharts.js"></script>

    <script>
        $(document).ready(function(){
            setTimeout('$(".session-block").hide();', 5000);

            $('[data-toggle="popover"]').popover({
                trigger: 'hover',
                placement: "right",
                container: 'body',
                html: true,
                content: function () {
                    return $(this).next('.popper-content').html();
                }
            });


            $("#startDate").datepicker({ dateFormat: "yy-mm-dd" });
            $("#endDate").datepicker({ dateFormat: "yy-mm-dd" });

            $("#dt-range-selector").click(function(){
                $("#date-selection-form").toggle();
            });

            $("#date-range-search").click(function(){
                if($("#startDate").val()  && $("#endDate").val()){
                    var href=$("#date-range-search").attr("href");
                    href+="&start="+$("#startDate").val()+"&end="+$("#endDate").val();
                    $("#date-range-search").attr("href", href);
                    return true;
                }

                return false;
            });


            var a=function () {
                $('#container-linechat').highcharts({
                    chart: {
                        type: 'line',
                        margin: [ 50, 50, 100, 80]
                    },
                    credits: {
                        enabled: false
                    },
                    title: {

                        text: '{$report["title"]}',
                        x: -20 //center
                    },
                    subtitle: {
                        text: '',
                        x: -20
                    },
                    plotOptions: {
                        line: {
                            dataLabels: {
                                enable: true
                            }
                        }
                    },
                    xAxis: {
                        title: {
                            text: "{$report['title']}"

                        },
                        categories: [{$report['x']}],
                        labels: {
                            rotation: -45,
                            align: 'right',
                            style: {
                                fontSize: '13px',
                                fontFamily: 'Verdana, sans-serif'
                            }
                        }
                    },
                    yAxis: {
                        title: {
                            text: 'Number of Clicks'
                        },
                        plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }]
                    },
                    tooltip: {
                        valueSuffix: ''
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'top',
                        x: -10,
                        y: 100,
                        borderWidth: 0
                    },
                    series: [{
                        name: 'Total Clicks',
                        data: [{$report['y']['clicks']}]

                    }, {
                        name: 'Total Impressions',
                        data: [{$report['y']['impressions']}]

                    },
                        {
                            name: 'Total Budget',

                            data: [{$report['y']['cost']}]
                        }
                    ]
                });
            };


            a();
        });



</script>



    {include file="adword/footer.php"}
