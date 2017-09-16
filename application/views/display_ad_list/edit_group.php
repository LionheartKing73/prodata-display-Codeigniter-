{include file="adword/header.php"}   

    <base href="{$base_url}">
    <link rel="stylesheet" type="text/css" href="public/bootstrap/css/bootstrap.min.css">
    <link href="../public/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
    <link href="../public/css/styles.css" rel="stylesheet" type="text/css"/>
    <link href="../public/jquery-ui/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <link href="../public/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>

    <script src="../public/js/jquery-1.11.2.min.js" type="text/javascript"></script>

    <script src="../public/jquery-ui/jquery-ui.js" type="text/javascript"></script>

    
    <link href="../public/plupload/css/jquery.ui.plupload.css" rel="stylesheet" type="text/css"/>
    <script src="../public/plupload/plupload.full.min.js" type="text/javascript"></script>
    <script src="../public/plupload/jquery.ui.plupload.js" type="text/javascript"></script>
    <script src="../public/plupload/plupload.js" type="text/javascript"></script>
    <script src="../public/chosen/chosen.jquery.min.js" type="text/javascript"></script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.2.1/jquery.form-validator.min.js"></script>
    <link href="../public/chosen/chosen.css" rel="stylesheet" type="text/css"/>



<div class="row">
    <div class="col-sm-2"></div>
    <div class="col-sm-4 col-xs-12">
        <h3 class="text-left">Edit: {$data["io"]} {$data["campaign"]}</h3><h5>
            </div>
    <div class="col-sm-4 col-xs-12">
             <div class="text-right header-align" ><a href="displayAdList">Back to Reporting</a></div>
        </div>

    </div>
</div>


<!-- BEGIN FORM-->
<form action="" method="Post" id="ad_wizard" class="form-horizontal" name="ad_form" >
    <div class="form-body">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-sm-8 col-xs-12">
                <div class="session-block">
                   {if $this->session->flashdata('success')}
                         <div class="alert alert-success text-center"> {$this->session->flashdata("success")} </div>
                         {elseif $this->session->flashdata('error')}
                            <div class="alert alert-danger text-center"> {$this->session->flashdata("error")} </div>                         
                    {/if}
                    </div>
                <div class="col-sm-12 server-validate" >
                    {if validation_errors()}
                    Sorry there are some errors. Try again. <br />
                    {validation_errors()}
                    <br /><br />
                    {/if}
                </div>
                <div class="io-exists alert alert-danger text-center"></div>
                <div class="form-group">
                    <div class="col-sm-4">
                        <label class="control-label ">IO# :<span class="required">
                                * </span>
                        </label>
                    </div>
                    <div class="col-sm-8 col-xs-12">                       
                         <input id="io" name="io" value="{$data['io']}" type="text" class="form-control" data-validation="required" data-validation-error-msg="This field is required"
                             data-value="{$data['io']}" readonly/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-4">
                        <label class="control-label">Campaign Name :</label>
                    </div>
                    <div class="col-sm-8 col-xs-12">
                        <input name="campaign" value="{$data['campaign']}" type="text" class="form-control"/>
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-sm-4">
                        <label class="control-label">Requested Campaign Start Date :<span class="required">
                                * </span>
                        </label>
                    </div>
                    <div class="col-sm-8 col-xs-12">
                        <input name="date" type="text" value="{$data['date']}" class="form-control" data-validation="required" data-validation-error-msg="This field is required"
                            {if $data["status"] eq "active"}
                                 readonly="readonly"
                            {else}
                                id="datepicker"
                            {/if}
                            />
                    </div>
                    </div>

                <div class="form-group">
                    <div class="col-sm-4">
                        <label class="control-label">Select Ad Status : </label>
                    </div>
                    <div class="col-sm-8 col-xs-12">
                        <select class="form-control" name="status" id="status">
                            <option value="ENABLED" selected >ACTIVE</option>
                            <option value="PAUSED" >INACTIVE</option>
                        </select>
                    </div>
                </div>

                    <div class="form-group">
                        <div class="col-sm-4">
                            <label class="control-label">Campaign Vertical :</label>
                        </div>
                        <div class="col-sm-8 col-xs-12">
                            <select class="form-control" id="vertical" name="audience_id">
                                <option value="" disabled="disabled" selected>- Select Vertical -</option>
                                {foreach from=$vertical_list item=user}
                                {$value = $user["remarketing_list_id"]}
                                {$option = $user["vertical"]}
                                <option value= "{$value}">  {ucfirst($option)}</option>
                                {/foreach}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-4">
                        <label class="control-label">Remarketing Campaign :</label>
                    </div>
                    <div class="col-sm-8 col-xs-12">
                        <select class="form-control" name="remarketing" id="remarketing_campaign">
                            <option value="0" selected>No</option>
                            <option value="1" >Yes</option>
                        </select>
                    </div>
                </div>

                <div class="io_section">
                <div class="form-group">
                    <div class="col-sm-4">
                        <label class="control-label"> Linked IO :</label>
                    </div>
                    <div class="col-sm-8 col-xs-12">
                        <select class="form-control" name="linked_io[]" id="linked-io" multiple="multiple" data-placeholder="Select Linked IO">
                            {foreach from=$io_list item=user}
                            {$value = $user["remarketing_list_id"]}
                            {$option = $user["io"]}
                            <option value= "{$value}" {if $user['selected']} selected {/if}> {$option}</option>

                            {/foreach}
                        </select>
                    </div>

                </div>
                <div class="form-group">
                    <div class="col-sm-4">
                        <label class="control-label">Expanded Vertical Targeting? </label>
                    </div>
                    <div class="col-sm-8 col-xs-12">
                        <select class="form-control" name="expanded_remarketing" id="expanded_remarketing">
                            <option value="0" selected>No</option>
                            <option value="1" >Yes</option>
                        </select>
                    </div>
                </div>
                    </div>

                <div class="form-group">
                    <div class="col-sm-4">
                        <label class="control-label">Enable Ad End Criteria :</label>
                    </div>
                    <div class="col-sm-8 col-xs-12">
                        <input type="checkbox" name="show_details" id="show_details" class="show-details"/>
                    </div>
                </div>


                <div class="details-block">
                    <div class="form-group">
                        <div class="col-sm-4">
                            <label class="control-label">Maximum Clicks :</label>
                        </div>

                        <div class="col-sm-8 col-xs-12">
                            <input id="max-clicks" name="max_clicks" value="{if $data['max_clicks'] neq 0}{$data['max_clicks']}{/if}" type="text" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4">
                            <label class="control-label">Maximum Impressions :</label>
                        </div>
                        <div class="col-sm-8 col-xs-12">
                            <input id="max-impressions" name="max_impressions" value="{if $data['max_impressions'] neq 0}{$data['max_impressions']}{/if}" type="text" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4">
                            <label class="control-label">Maximum Spend :</label>
                        </div>
                        <div class="col-sm-8 col-xs-12">
                            <input id="max-spend" name="max_spend" value="{if number_format($data['max_spend']/1000000, 2, ".", " ") neq 0.00}{number_format($data['max_spend']/1000000, 2, ".", " ")}{/if}" type="text" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4">
                            <label class="control-label">End Date :</label>
                        </div>
                        <div class="col-sm-8 col-xs-12">
                            <input id="end-date" name="end_date" value="{if $data['end_date'] neq '0000-00-00 00:00:00'}{$data['end_date']}{/if}" type="text" class="form-control"/>
                        </div>
                    </div>
                </div>


                <input id="active_state" name="active_state" type="hidden" class="form-control" />
                <input name="id" value="{$data['id']}" type="hidden" class="form-control"/>
                <input name="linked_io_data"  id="linked_io_data" type="hidden" class="form-control"/>
                <input type="hidden"
                       data_vertical="{foreach from=$remarketings item=remarketing}{if $remarketing['audience_type'] eq 'vertical'}{$remarketing['audience_id']}{/if}{/foreach}" id="audience-data" />
                <div class="form-actions">
                    <div class="col-sm-offset-5 col-sm-7">
                        <button type="submit" class="btn green btn-success" id="submit">Save Changes</button>
                        <button type="button" class="btn green btn-danger" id="cancel">Cancel</button>
                    </div>
                </div>
            </div>
            <div class="col-md-3"></div>
            <div>
            </div>
</div>
</form>
<!-- END FORM-->


<script src="../public/js/ext.js" type="text/javascript"></script>

<script>
    $(document).ready(function () {
        var current_date=new Date();
        $.validate({
            form: '#ad_wizard'
        });

        $("#status").val("{$data["group_status"]}").attr("selected", true);
        var vertical=$("#audience-data").attr("data_vertical");
        var is_remarketing="{$is_remarketing}";




        if(vertical){
            $("#vertical").val(vertical).attr("selected", true);
            if(is_remarketing=="Y") {
                $("#remarketing_campaign").val(1).attr("selected", true);
            }
        }
        $("#linked-io").chosen({});


        if($("#remarketing_campaign").val()==1){
            $(".io_section").show();
        }else{
            $(".io_section").hide();
        }


        $("#remarketing_campaign").change(function(){
            if($("#remarketing_campaign").val()==1){
                $(".io_section").show();
            }else{
                $(".io_section").hide();
            }
        });



        $("#datepicker").datetimepicker({ minDate: 0 });
        $("#end-date").datetimepicker({  minDate: 0 });


        $("#submit").click(function(){
            var res=[];
            var a=$(".search-choice span");
            a.each(function(index){
                res.push(($(this).text()).trim());
            });

            res=JSON.stringify(res);

            $("#linked_io_data").val(res);

            if( new Date($("#datepicker").val()) <= current_date){
                $("#active_state").val("true");
            }else{
                $("#active_state").val("false");
            }
        });

        {foreach from=$io_list item=user}
        {if $user['selected']} $("#expanded_remarketing").val(1).attr("selected", true); {/if}
            {/foreach}


        $("#cancel").click(function(){
            document.location.href="displayAdList/index";
        });




    });
</script>


{include file="adword/footer.php"}