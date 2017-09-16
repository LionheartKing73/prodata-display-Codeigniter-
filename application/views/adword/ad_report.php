{include file="adword/header.php"}

<div class="container">

    <div class="alert alert-error" id="err_bof" style="display:none;">
        <a class="close" data-dismiss="alert">X</a>
        <strong id="err_bof_message"></strong>
    </div>

    <div class="alert alert-success" id="success_bof" style="display:none;">
        <a class="close" data-dismiss="alert">X</a>
        <strong id="success_bof_message"></strong>
    </div>

    <!-- Example row of columns -->
    <div class="row">
        <div class="span12">
            <h2>Campaign List</h2>

            <ul class="nav nav-tabs" id="campaignTabs">
                <li class="active"><a href="#active" data-toggle="tab">Active</a></li>
                <li><a href="#completed" data-toggle="tab">Completed</a></li>
                <li><a href="#scheduled" data-toggle="tab">Scheduled</a></li>
                <li><a href="#disapproved" data-toggle="tab">Disapproved</a></li>
            </ul>

            <div class="tab-content">

                <div class="tab-pane active" id="active">
                    <table class="table table-bordered table-striped" id="mytable-inprogress">
                        <thead>
                        <tr>
                            <th>I/O #</th>
                            <th>Campaign Name</th>
                            <th>Current Clicks/Impressions</th>
                            <th>Max Clicks/Impressions</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        {for $k=0 to count($remarketings)-1}
                        {if $remarketings[$k]["status"] eq "active"}
                        <tr id="io-{$remarketings[$k]['id']}">
                            <td>{$remarketings[$k]["io"]}</td>
                            <td>{$remarketings[$k]["campaign"]}</td>
                            <td>{$remarketings[$k]["clicks"]}/{{$remarketings[$k]["max_clicks"]}}</td>
                            <td>{$remarketings[$k]["impressions"]}/{{$remarketings[$k]["max_impressions"]}}</td>
                            <td><a href="{$base_url}DisplayAdList/view?group_id={$remarketings[$k]['group_id']}"><i class="icon-eye-open"></i></a></td>
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
                            <th>Current Clicks/Impressions</th>
                            <th>Max Clicks/Impressions</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        {for $k=0 to count($remarketings)-1}
                        {if $remarketings[$k]["status"] eq "completed"}
                        <tr id="io-{$remarketings[$k]['id']}">
                            <td>{$remarketings[$k]["io"]}</td>
                            <td>{$remarketings[$k]["campaign"]}</td>
                            <td>{$remarketings[$k]["clicks"]}/{{$remarketings[$k]["max_clicks"]}}</td>
                            <td>{$remarketings[$k]["impressions"]}/{{$remarketings[$k]["max_impressions"]}}</td>
                            <td><a href="{$base_url}DisplayAdList/view?group_id={$remarketings[$k]['group_id']}"><i class="icon-eye-open"></i></a></td>
                        </tr>
                        {/if}
                        {/for}
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane" id="scheduled">
                    <table class="table table-bordered table-striped" id="mytable_scheduled">
                        <thead>
                        <tr>
                            <th>I/O #</th>
                            <th>Campaign Name</th>
                            <th>Current Clicks/Impressions</th>
                            <th>Max Clicks/Impressions</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        {for $k=0 to count($remarketings)-1}
                        {if $remarketings[$k]["status"] eq "scheduled"}
                        <tr id="io-{$remarketings[$k]['id']}">
                            <td>{$remarketings[$k]["io"]}</td>
                            <td>{$remarketings[$k]["campaign"]}</td>
                            <td>0/{{$remarketings[$k]["max_clicks"]}}</td>
                            <td>0/{{$remarketings[$k]["max_impressions"]}}</td>
                            <td><a href="{$base_url}DisplayAdList/view?group_id={$remarketings[$k]['group_id']}"><i class="icon-eye-open"></i></a></td>
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
                            <th>Current Clicks/Impressions</th>
                            <th>Max Clicks/Impressions</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        {for $k=0 to count($remarketings)-1}
                        {if $remarketings[$k]["status"] eq "disapproved"}
                        <tr id="io-{$remarketings[$k]['id']}">
                            <td>{$remarketings[$k]["io"]}</td>
                            <td>{$remarketings[$k]["campaign"]}</td>
                            <td>0/{{$remarketings[$k]["max_clicks"]}}</td>
                            <td>0/{{$remarketings[$k]["max_impressions"]}}</td>
                            <td><a href="{$base_url}DisplayAdList/view?group_id={$remarketings[$k]['group_id']}"><i class="icon-eye-open"></i></a></td>
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


    {include file="adword/footer.php"}
