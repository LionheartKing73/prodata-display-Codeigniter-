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

<h3>New Display Ad Wizard</h3>

<!-- BEGIN FORM-->
<form action="" method="Post" id="ad_wizard" class="form-horizontal" name="ad_form" >
    <div class="form-body">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-sm-7 col-xs-12">
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
                <div class="io-exists alert text-center"></div>
                <div class="form-group">
                    <div class="col-sm-4">
                        <label class="control-label ">IO# :<span class="required">
                                * </span>
                        </label>
                    </div>
                    <div class="col-sm-8 col-xs-12">
                        <input id="io-number" name="io" type="text" class="form-control" data-validation="required" data-validation-error-msg="This field is required" style="text-transform: uppercase" max="16"/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-4">
                        <label class="control-label">Campaign Name :</label>
                    </div>
                    <div class="col-sm-8 col-xs-12">
                        <input name="campaign" type="text" class="form-control"/>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-4">
                        <label class="control-label">Campaign Vertical :<span class="required">*</span></label>
                    </div>
                    <div class="col-sm-8 col-xs-12">
                        <select class="form-control" name="audience_id" data-validation="required" data-validation-error-msg="This field is required">
                            <option value="" disabled="disabled" selected>- Select Vertical -</option>
                            {foreach from=$userList item=user}
                            {$value = $user["remarketing_list_id"]}
                            {$option = $user["vertical"]}
                            <option value= "{$value}">  {ucfirst($option)}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-4">
                        <label class="control-label">Requested Campaign Start Date :<span class="required">
                                * </span>
                        </label>
                    </div>
                    <div class="col-sm-8 col-xs-12">
                        <input name="start_date" type="text" class="form-control" id="datepicker" data-validation="required" data-validation-error-msg="This field is required" />
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
                <div class="remarketing-block">
                    <div class="form-group">
                        <div class="col-sm-4">
                            <label class="control-label"> Linked IO :</label>
                        </div>
                        <div class="col-sm-8 col-xs-12">
                            <select class="form-control" name="linked_io[]" id="linked-io" multiple="multiple" data-placeholder="Select Linked IO">
                                {foreach from=$ioList item=user}
                                {$value = $user["remarketing_list_id"]}
                                {$option = $user["io"]}
                                <option value= "{$value}">  {$option}</option>
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
                        <label class="control-label">Select Ad Status : </label>
                    </div>
                    <div class="col-sm-8 col-xs-12">
                        <select class="form-control" name="status">
                            <option value="ENABLED" selected >ACTIVE</option>
                            <option value="PAUSED" >INACTIVE</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-4">
                        <label class="control-label">Destination URL :<span class="required">*</span></label>
                    </div>
                    <div class="col-sm-8 col-xs-12">
                        <input id="display_url" name="destination_url" type="text" class="form-control" data-validation="required" data-validation-error-msg="This field is required"/>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-4">
                        <label class="control-label">Enable Ad End Criteria :</label>
                    </div>
                    <div class="col-sm-8 col-xs-12">
                        <input type="checkbox" name="show_details" class="show-details" />
                    </div>
                </div>


                <div class="details-block">
                    <div class="form-group">
                        <div class="col-sm-4">
                            <label class="control-label">Maximum Clicks :</label>
                        </div>

                        <div class="col-sm-8 col-xs-12">
                            <input id="max-clicks" name="max_clicks" type="text" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4">
                            <label class="control-label">Maximum Impressions :</label>
                        </div>
                        <div class="col-sm-8 col-xs-12">
                            <input id="max-impressions" name="max_impressions" type="text" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4">
                            <label class="control-label">Maximum Spend :</label>
                        </div>
                        <div class="col-sm-8 col-xs-12">
                            <input id="max-spend" name="max_spend" type="text" class="form-control"  />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4">
                            <label class="control-label">End Date :</label>
                        </div>
                        <div class="col-sm-8 col-xs-12">
                            <input id="end-date" name="end_date" type="text" class="form-control" />
                        </div>
                    </div>
                </div>


                <input id="linked_io_data" name="linked_io_data" type="hidden" class="form-control" />
                <input id="active_state" name="active_state" type="hidden" class="form-control" />
                <input id="img_count" type="hidden" value="0" class="form-control" />
                <!--Begin Upload Creatives -->
                <div class="col-sm-offset-4 col-sm-8 col-xs-12">
                    <div id="uploader">
                        <p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
                    </div>
                    {if $files_uploaded}
                    {foreach from=$files_uploaded item=file}
                    <div class="existing-img" title="{$file}">
                        <i class="fa fa-trash-o remove-img"></i>
                        <span>"{$file}"</span>
                    </div>
                    {/foreach}
                    {/if}
                </div>

                <div class="col-sm-offset-4 col-sm-8 col-xs-offset-0 col-xs-12">
                    <div id="upload-result" class="alert-message"></div>
                </div>
                <div class="form-actions">
                    <div class="offset5 col-sm-7">
                        <button type="submit" class="btn green" id="submit">Continue>></button>
                    </div>
                </div>
            </div>
            <div class="col-md-3"></div>
            <div>
            </div>
        </div>
</form>
<!-- END FORM-->


<script src="/public/js/ext.js" type="text/javascript"></script>

<script>
    $(document).ready(function () {
        var current_date=new Date();

        $.validate({
            form: '#ad_wizard'
        });

        $("#datepicker").datetimepicker({ minDate: 0 });
        $("#end-date").datetimepicker({  minDate: 0 });

        $("#linked-io").chosen({});
        $(".remarketing-block").hide();


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

            if($("#img_count").val()==0 || $("#uploader_count").val()==0){
                $("#upload-result").text("This field is required");

                return false;
            }
            return true;

        });


    });


</script>


{include file="adword/footer.php"}