{include file="v2/sections/header.php"}
<div class="theme-report-row-wrap" id="wrap">
    <div class="theme-container container-fluid mobile-container" id="content">
        <div class="pull-right" >
            <button id="btn_add_network" type="submit" form="add_domain_form" class="btn btn-success" >Add</button>
        </div>
        <div class="row network_add_row" >

            <form id="add_domain_form" class="mult-net">

                <div class="col-sm-6 col-xs-12">
                    <div class="col-sm-12 domain_inputs" >
                        <label>Domain</label>
                        <input type="text" maxlength="100" value="" name="domain[domain]" class="form-control" placeholder="Domain"/>
                    </div>
                    <div class="col-sm-12 domain_inputs" >
                        <label>Company name</label>
                        <input type="text" maxlength="50" value="" name="domain[company_name]" class="form-control" placeholder="Company name"/>
                    </div>
                    <div class="col-sm-12 domain_inputs" >
                        <label>Company email</label>
                        <input type="email" maxlength="50" value="" name="domain[company_email]" class="form-control" placeholder="Company email"/>
                    </div>
                        <div class="col-sm-12 domain_inputs" >
                            <label>Upload your logo</label>
                            <div id="uploader">
                                <p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
                            </div>
                        </div>
                    <div class="col-sm-12 domain_inputs" >
                        <input id="uploaded_logo" type="hidden" value="" name="domain[logo]"/>
                    </div>
                </div>

                <div class="col-sm-6 col-xs-12">
                    <div class="col-sm-12 domain_inputs" >
                        <label>Background color</label>
                        <input type="text" maxlength="20" name="domain[background_color]" class="form-control jscolor" value="#084D8E" placeholder="Background color"/>
                    </div>
                    <div class="col-sm-12 domain_inputs" >
                        <label>Footer color</label>
                        <input type="text" maxlength="20" value="#7FBE27" name="domain[footer_color]" class="form-control jscolor" placeholder="Footer color"/>
                    </div>
                    <div class="col-sm-12 domain_inputs" >
                        <label>Active Button color</label>
                        <input type="text" maxlength="20" value="#5cb85c" name="domain[active_button_color]" class="form-control jscolor" placeholder="Active Button color"/>
                    </div>
                    <div class="col-sm-12 domain_inputs" >
                        <label>Passive Button color</label>
                        <input type="text" maxlength="20" value="#d0d0d0" name="domain[passive_button_color]" class="form-control jscolor" placeholder="Passive Button color"/>
                    </div>
                    <div class="col-sm-12 domain_inputs" >
                        <label>Content Background color</label>
                        <input type="text" maxlength="20" value="#F6F6F6" name="domain[content_background_color]" class="form-control jscolor" placeholder="Content Background color"/>
                    </div>
                    <div class="col-sm-12 domain_inputs" >
                        <label>Conttent Text color</label>
                        <input type="text" maxlength="20" value="#2c3e50" name="domain[content_text_color]" class="form-control jscolor" placeholder="Conttent Text color"/>
                    </div>
                    <div class="col-sm-12 domain_inputs" >
                        <label>Blocks Header Text color</label>
                        <input type="text" maxlength="20" value="#ffffff" name="domain[block_header_text_color]" class="form-control jscolor" placeholder="Blocks Header Text color"/>
                    </div>
                    <div class="col-sm-12 domain_inputs" >
                        <label>Blocks Header Icon color</label>
                        <input type="text" maxlength="20" value="#ffa500" name="domain[block_header_icon_color]" class="form-control jscolor" placeholder="Blocks Header Icon color"/>
                    </div>
                    <div class="col-sm-12 domain_inputs" >
                        <label>Blocks Header Background color</label>
                        <input type="text" maxlength="20" value="#d3d3d3" name="domain[block_header_background_color]" class="form-control jscolor" placeholder="Blocks Header Background color"/>
                    </div>
                    <div class="col-sm-12 domain_inputs" >
                        <label>Blocks Content Text color</label>
                        <input type="text" maxlength="20" value="#ffa500" name="domain[block_content_text_color]" class="form-control jscolor" placeholder="Blocks Content Text color"/>
                    </div>
                </div>
<!--                <div class="col-sm-4" >-->
<!--                    <label></label>-->
<!--                    <input type="text" maxlength="20" value="" name="" class="form-control" placeholder=""/>-->
<!--                </div>-->

            </form>
        </div>

        <div class="admin_users_table_block table-responsive">
            <table id="theme-sortable-table" class="table theme-report-table">
                <thead>
                <tr class="theme-table-row theme-table-header theme-report-table-row e-mult-net">
                    <th class="theme-table-middle-cell theme-report-header-data">Domain</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Company name</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Company email</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Background color</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Footer color</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Active Button color</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Passive Button color</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Content Background color</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Content Text color</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Blocks Header Text color</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Blocks Header Icon color</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Blocks Header Background color</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Blocks Content Text color</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Logo</th>
                    <th class="theme-table-middle-cell theme-report-header-data">Actions</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$domains item=domain}
                <tr class="theme-table-row theme-table-data-row theme-report-table-row">
                    <td class="theme-table-middle-cell theme-report-table-data">{$domain.domain}</td>
                    <td class="theme-table-middle-cell theme-report-table-data">{$domain.company_name}</td>
                    <td class="theme-table-middle-cell theme-report-table-data">{$domain.company_email}</td>
                    <td class="theme-table-middle-cell theme-report-table-data">{$domain.background_color}</td>
                    <td class="theme-table-middle-cell theme-report-table-data">{$domain.footer_color}</td>
                    <td class="theme-table-middle-cell theme-report-table-data">{$domain.active_button_color}</td>
                    <td class="theme-table-middle-cell theme-report-table-data">{$domain.passive_button_color}</td>
                    <td class="theme-table-middle-cell theme-report-table-data">{$domain.content_background_color}</td>
                    <td class="theme-table-middle-cell theme-report-table-data">{$domain.content_text_color}</td>
                    <td class="theme-table-middle-cell theme-report-table-data">{$domain.block_header_text_color}</td>
                    <td class="theme-table-middle-cell theme-report-table-data">{$domain.block_header_icon_color}</td>
                    <td class="theme-table-middle-cell theme-report-table-data">{$domain.block_header_background_color}</td>
                    <td class="theme-table-middle-cell theme-report-table-data">{$domain.block_content_text_color}</td>
                    <td class="theme-table-middle-cell theme-report-table-data"><img class="img-responsive" src="/v2/images/domain_logos/{$domain.logo}"></td>
                    <td class="theme-table-middle-cell theme-report-table-data">
                        <span data-id="{$domain.id}" class="glyphicon glyphicon-remove remove_domain" ></span>
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
<script src="/v2/js/jquery.validate.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/v2/js/bootstrap.min.js"></script>
<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<link href="{$base_url}/public/plupload/css/jquery.ui.plupload.css" rel="stylesheet" type="text/css"/>
<link href="{$base_url}/public/jquery-ui/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

<script src="{$base_url}/public/jquery-ui/jquery-ui.js" type="text/javascript"></script>
<script src="{$base_url}/public/plupload/plupload.full.min.js" type="text/javascript"></script>
<script src="{$base_url}/public/plupload/jquery.ui.plupload.js" type="text/javascript"></script>
<script src="/v2/js/jscolor.min.js"></script>

<script src="/v2/js/admin/manage_domains.js"></script>
