{include file="v2/sections/header.php"}
<base href="{$base_url}">
<link href="{$base_url}/public/css/styles.css" rel="stylesheet" type="text/css"/>
<link href="{$base_url}/public/jquery-ui/jquery-ui.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="/v2/css/jquery.steps.css">
<link href="/v2/js/chosen/chosen.min.css" rel="stylesheet" type="text/css"/>
<link href="/v2/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>

<div class="theme-report-row-wrap" style="padding: 0;">
    <div class="theme-container container-fluid" style="padding: 0;">
        <div class="theme-report-campaign-list-row">
            <div class="theme-report-tabbed-section">
                <nav class="theme-reoprt-tabbed-nav" role="navigation">
                    <form id="example-advanced-form" >

                        <h3 class="display_none">Campaign Information</h3>
                        {include file="v2/campaign/professional_wizard/campaign_information.php"}

                        <h3 class="display_none">Creative</h3>
                        {include file="v2/campaign/professional_wizard/creative.php"}
                        
                        <h3 class="display_none">Review</h3>
                        {include file="v2/campaign/professional_wizard/review.php"}

                    </form>
                </nav>
            </div>
        </div>
    </div>
</div>
{include file="v2/sections/scripts.php"}
<script> var user = {$user|@json_encode}; </script>
<script src="{$base_url}public/jquery-ui/jquery-ui.js" type="text/javascript"></script>
<link href="{$base_url}public/plupload/css/jquery.ui.plupload.css" rel="stylesheet" type="text/css"/>
<script src="{$base_url}public/plupload/plupload.full.min.js" type="text/javascript"></script>
<script src="{$base_url}public/plupload/jquery.ui.plupload.js" type="text/javascript"></script>
<script src="{$base_url}public/plupload/plupload_wizard.js" type="text/javascript"></script>
<script src="/static/js/heatmap.min.js"></script>
<script src="/v2/js/datetime-picker.jquery.js"></script>
<script src="/v2/js/jquery.steps.min.js"></script>
<script src="/v2/js/jquery.validate.min.js"></script>
<script src="/v2/js/additional-methods.min.js"></script>
<script src="/v2/js/underscore.js"></script>
<script src="/v2/js/jscolor.min.js"></script>
<script src="/v2/js/chosen/chosen.jquery.js"></script>
{include file="v2/campaign/professional_wizard/html_templates.php"}
<script src="/v2/js/professional_wizard.js"></script>
</body>
</html>