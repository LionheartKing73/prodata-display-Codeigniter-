{include file="adword/header.php"}

<div class="container">
    <!-- Example row of columns -->
    <div class="row">
        <div class="span12">
            <h2>Display Ad Campaigns &nbsp;&nbsp;<small>Review &amp; Approve Campaigns</small> <span class='pull-right btn btn-success' style='margin-top:10px;' onClick='document.location.href="/adword"'>Create New Campaign</span></h2>


            <div class="session-block">
                {if $this->session->flashdata('success')}
                <div class="alert alert-success text-center"> {$this->session->flashdata("success")} </div>
                {elseif $this->session->flashdata('error')}
                <div class="alert alert-danger text-center"> {$this->session->flashdata("error")} </div>
                {/if}
            </div>



            <ul class="nav nav-tabs" id="campaignTabs">
                <li class="{if $this->session->flashdata('scheduled_active') neq '1'}active{/if}"><a href="#active" data-toggle="tab">Active</a></li>
                <li><a href="#completed" data-toggle="tab">Completed</a></li>
                <li class="{if $this->session->flashdata('scheduled_active') eq '1'}active{/if}"><a href="#scheduled" data-toggle="tab">Scheduled</a></li>
                <li><a href="#disapproved" data-toggle="tab">Disapproved</a></li>
            </ul>

            <div class="tab-content">

                <div class="tab-pane {if $this->session->flashdata('scheduled_active') neq '1'}active{/if}" id="active">
                    <table class="table table-bordered table-striped" id="mytable-inprogress">
                        <thead>
                        <tr>
                            <th>I/O #</th>
                            <th>Campaign Name</th>
                            <th>Clicks</th>
                            <th>Impressions</th>
                            <th>Budget</th>
                            <th>End Criteria</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        {for $k=0 to count($group_lists)-1}
                        {if $group_lists[$k]["status"] eq "active"}
                        <tr id="io-{$group_lists[$k]['id']}">
                            <td>{$group_lists[$k]["io"]}</td>
                            <td>{$group_lists[$k]["campaign"]} {if $group_lists[$k]["remarketing"]}<i class="icon-check"></i>{/if}</td>
                            <td>{$group_lists[$k]["clicks"]} / {$group_lists[$k]["max_clicks"]}</td>
                            <td>{$group_lists[$k]["impressions"]} / {$group_lists[$k]["max_impressions"]}</td>
                            <td>${number_format($group_lists[$k]["cost"]/1000000, 2, ".", " ")} / ${number_format($group_lists[$k]["max_spend"]/1000000, 2,  ".", " ")}</td>
                            <td>{if $group_lists[$k]["max_clicks"] neq 0}Clicks <br />{/if} {if $group_lists[$k]["max_impressions"] neq 0}Impressions <br />{/if} {if $group_lists[$k]["max_spend"] neq 0.00}Budget <br />{/if}
                                {if $group_lists[$k]["end_date"] neq "0000-00-00 00:00:00"}{$group_lists[$k]["end_date"]}{/if}
                            </td>
                            <td><a href="{$base_url}displayAdList/view?group_id={$group_lists[$k]['id']}"><i class="icon-eye-open"></i></a>
                                <a href="{$base_url}displayAdList/edit_group?id={$group_lists[$k]['id']}"><i class="icon-edit"></i></a>
                                <a href="{$base_url}displayAdList/delete_group?id={$group_lists[$k]['id']}" class="delete" data-value="{$group_lists[$k]['io']}"><i class="icon-trash"></i></a>
                            </td>
                        </tr>
                        {/if}
                        {/for}
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane" id="completed">
                    <table class="table table-bordered table-striped" id="mytable_completed">
                        <thead>
                        <tr>
                            <th>I/O #</th>
                            <th>Campaign Name</th>
                            <th>Clicks</th>
                            <th>Impressions</th>
                            <th>Budget</th>
                            <th>End Criteria</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        {for $k=0 to count($group_lists)-1}
                        {if $group_lists[$k]["status"] eq "completed"}
                        <tr id="io-{$group_lists[$k]['id']}">
                            <td>{$group_lists[$k]["io"]}</td>
                            <td>{$group_lists[$k]["campaign"]} {if $group_lists[$k]["remarketing"]}<i class="icon-check"></i>{/if}</td>
                            <td>{$group_lists[$k]["clicks"]} / {$group_lists[$k]["max_clicks"]}</td>
                            <td>{$group_lists[$k]["impressions"]} / {$group_lists[$k]["max_impressions"]}</td>
                            <td>${number_format($group_lists[$k]["cost"]/1000000, 2, ".", " ")} / ${number_format($group_lists[$k]["max_spend"]/1000000, 2,  ".", " ")}</td>
                            <td>{if $group_lists[$k]["max_clicks"] neq 0}Clicks <br />{/if} {if $group_lists[$k]["max_impressions"] neq 0}Impressions <br />{/if} {if $group_lists[$k]["max_spend"] neq 0.00}Budget <br />{/if}
                                {if $group_lists[$k]["end_date"] neq "0000-00-00 00:00:00"}{$group_lists[$k]["end_date"]}{/if}
                            </td>
                            <td><a href="{$base_url}displayAdList/view?group_id={$group_lists[$k]['id']}"><i class="icon-eye-open"></i></a>
                                <a href="{$base_url}displayAdList/edit_group?id={$group_lists[$k]['id']}"><i class="icon-edit"></i></a>
                                <a href="{$base_url}displayAdList/delete_group?id={$group_lists[$k]['id']}" class="delete" data-value="{$group_lists[$k]['io']}"><i class="icon-trash"></i></a>
                            </td>
                        </tr>
                        {/if}
                        {/for}
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane {if $this->session->flashdata('scheduled_active') eq '1'}active{/if}" id="scheduled">
                    <table class="table table-bordered table-striped" id="mytable_scheduled">
                        <thead>
                        <tr>
                            <th>I/O #</th>
                            <th>Campaign Name</th>
                            <th>Clicks</th>
                            <th>Impressions</th>
                            <th>Budget</th>
                            <th>End Criteria</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        {for $k=0 to count($group_lists)-1}
                        {if $group_lists[$k]["status"] eq "scheduled"}
                        <tr id="io-{$group_lists[$k]['id']}">
                            <td>{$group_lists[$k]["io"]}</td>
                            <td>{$group_lists[$k]["campaign"]} {if $group_lists[$k]["remarketing"]}<i class="icon-check"></i>{/if}</td>
                            <td>{$group_lists[$k]["clicks"]} / {$group_lists[$k]["max_clicks"]}</td>
                            <td>{$group_lists[$k]["impressions"]} / {$group_lists[$k]["max_impressions"]}</td>
                            <td>${number_format($group_lists[$k]["cost"]/1000000, 2, ".", " ")} / ${number_format($group_lists[$k]["max_spend"]/1000000, 2,  ".", " ")}</td>
                            <td>{if $group_lists[$k]["max_clicks"] neq 0}Clicks <br />{/if} {if $group_lists[$k]["max_impressions"] neq 0}Impressions <br />{/if} {if $group_lists[$k]["max_spend"] neq 0.00}Budget <br />{/if}
                                {if $group_lists[$k]["end_date"] neq "0000-00-00 00:00:00"}{$group_lists[$k]["end_date"]}{/if}
                            </td>
                            <td><a href="{$base_url}displayAdList/view?group_id={$group_lists[$k]['id']}"><i class="icon-eye-open"></i></a>
                                <a href="{$base_url}displayAdList/edit_group?id={$group_lists[$k]['id']}"><i class="icon-edit"></i></a>
                                <a href="{$base_url}displayAdList/delete_group?id={$group_lists[$k]['id']}" class="delete" data-value="{$group_lists[$k]['io']}"><i class="icon-trash"></i></a>

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
                            <th>I/O #</th>
                            <th>Campaign Name</th>
                            <th>Budget</th>
                            <th>Disapproval Reason</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        {for $k=0 to count($disapproved_groups)-1}
                            {if $k!=count($disapproved_groups)-1 && $disapproved_groups[$k]["id"]!=$disapproved_groups[$k+1]["id"]}
                        <tr id="io-{$disapproved_groups[$k]['id']}">
                            <td>{$disapproved_groups[$k]["io"]}</td>
                            <td>{$disapproved_groups[$k]["campaign"]} {if $disapproved_groups[$k]["remarketing"]}<i class="icon-check"></i>{/if}</td>
                            <td>${number_format($disapproved_groups[$k]["cost"]/1000000, 2, ".", " ")} / ${number_format($group_lists[$k]["max_spend"]/1000000, 2, ".", " ")}</td>
                            <td>{$disapproved_groups[$k]["disapproval_reasons"]}</td>
                            <td>
                                <a href="{$base_url}displayAdList/view?group_id={$disapproved_groups[$k]['id']}"><i class="icon-eye-open"></i></a>
                                <a href="{$base_url}displayAdList/edit_group?id={$group_lists[$k]['id']}"><i class="icon-edit"></i></a>
                                <a href="{$base_url}displayAdList/delete_group?id={$group_lists[$k]['id']}" class="delete" data-value="{$group_lists[$k]['io']}"><i class="icon-trash"></i></a>
                            </td>
                        </tr>
                        {/if}
                        {if $k==count($disapproved_groups)-1}
                        <tr id="io-{$disapproved_groups[$k]['id']}">
                            <td>{$disapproved_groups[$k]["io"]}</td>
                            <td>{$disapproved_groups[$k]["campaign"]} {if $disapproved_groups[$k]["remarketing"]}<i class="icon-check"></i>{/if}</td>
                            <td>${number_format($disapproved_groups[$k]["cost"]/1000000, 2, ".", " ")} / ${number_format($disapproved_groups[$k]["max_spend"]/1000000, 2, ".", " ")}</td>
                            <td>{$disapproved_groups[$k]["disapproval_reasons"]}</td>
                            <td>

                                <a href="{$base_url}displayAdList/view?group_id={$disapproved_groups[$k]['id']}"><i class="icon-eye-open"></i></a>
                                <a href="{$base_url}displayAdList/edit_group?id={$disapproved_groups[$k]['id']}"><i class="icon-edit"></i></a>
                                <a href="{$base_url}displayAdList/delete_group?id={$disapproved_groups[$k]['id']}" class="delete" data-value="{$disapproved_groups[$k]['io']}"><i class="icon-trash"></i></a>
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
    <script>

    </script>

    <script src="/public/js/index.js" type="text/javascript"></script>
    <script>
       $(document).ready(function(){
           setTimeout('$(".session-block").hide();', 5000);

       });

        </script>
    {include file="adword/footer.php"}
