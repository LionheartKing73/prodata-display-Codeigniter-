{include file="adword/header.php"}
{include file="display_ad_list/edit_ad_links.php"}

<h3>Create New Ad</h3>

<!-- BEGIN FORM-->
<form action="" method="Post" id="ad_wizard" class="form-horizontal" name="ad_form" >
    <div class="form-body">
        <input type="hidden" name="group_id" value="{$data}">
        <div class="row">
            <div class="col-sm-2"></div>
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
                <div class="io-exists alert alert-danger text-center"></div>
                   <div class="form-group">
                                        <div class="col-sm-4">
                                            <label class="control-label">Select Ad Status : </label>
                                        </div>
                                        <div class="col-sm-8 col-xs-12">
                                        <select class="form-control" name="status" id="ad_status">
                                            <option value="0" disabled="disabled" selected>- Select Ad Status -</option>
                                            <option value="ENABLED" >ENABLED</option>
                                            <option value="PAUSED" >PAUSED</option>
                                        </select>
                                    </div>
                                </div>
                                    <div class="form-group">
                                        <div class="col-sm-4">
                                            <label class="control-label">Destination URL : </label>
                                        </div>
                                    <div class="col-sm-8 col-xs-12">
                                        <input id="display_url" name="destination_url" type="text" class="form-control" data-validation="required" data-validation-error-msg="This field is required"/>
                                    </div>
                                </div>

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

                <div class="col-sm-offset-4 col-sm-6 col-xs-offset-0 col-xs-12">
                    <div id="upload-result" class="alert-message"></div>
                </div>
                <div class="form-actions">
                    <div class="col-sm-offset-5 col-sm-7">
                        <button type="submit" class="btn green btn-success" id="submit">Save</button>
                        <a class="btn green btn-danger" href="displayAdList/view?group_id={$data}">Cancel</a>
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
{literal}
<script>
    $(document).ready(function () {
        $.validate({
            form: '#ad_wizard'
        });

        a=0;

        $("#submit").click(function(){
            if(a==0){
                $("#upload-result").text("This field is required");
                return false;
            }else if(a>1){
                $("#upload-result").text("You can upload only one image.");
                return false;
            }else{
                return true;
            }
        });
    });
</script>
{/literal}

{include file="adword/footer.php"}