{include file="v2/sections/header.php"}
<base href="{$base_url}">
<link href="{$base_url}/public/css/styles.css" rel="stylesheet" type="text/css"/>
<link href="{$base_url}/public/jquery-ui/jquery-ui.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="/v2/css/jquery.steps.css">
<link href="/v2/js/chosen/chosen.min.css" rel="stylesheet" type="text/css"/>
<link href="/v2/css/datetime-picker.css" rel="stylesheet" type="text/css"/>

<div class="theme-report-row-wrap">
    <div class="theme-container container-fluid">
        <div class="theme-report-campaign-list-row">
            <div class="theme-report-tabbed-section">
                <nav class="theme-reoprt-tabbed-nav" role="navigation">
                    <form id="example-advanced-form" action="/v2/html/creat_campaign" >
                        <h3>Campaign Type</h3>
                        {include file="v2/test/wizard/campaign_type.php"}

                        <h3>Campaign Information</h3>
                        {include file="v2/test/wizard/campaign_information.php"}

                        <h3>Digital Rooftop</h3>
                        {include file="v2/test/wizard/digital_rooftop.php"}

                        <h3>Creative</h3>
                        {include file="v2/test/wizard/creative.php"}
                        
                        <h3>Review</h3>
                        {include file="v2/test/wizard/review.php"}
                    </form>
                </nav>
            </div>
        </div>
    </div>
</div>
{include file="v2/sections/scripts.php"}
<script src="{$base_url}/public/jquery-ui/jquery-ui.js" type="text/javascript"></script>
<link href="{$base_url}/public/plupload/css/jquery.ui.plupload.css" rel="stylesheet" type="text/css"/>
<script src="{$base_url}/public/plupload/plupload.full.min.js" type="text/javascript"></script>
<script src="{$base_url}/public/plupload/jquery.ui.plupload.js" type="text/javascript"></script>
<script src="{$base_url}/public/plupload/plupload_wizard.js" type="text/javascript"></script>
<script src="/static/js/heatmap.min.js"></script>
<script src="/v2/js/datetime-picker.jquery.js"></script>
<script src="/v2/js/jquery.steps.min.js"></script>
<script src="/v2/js/jquery.validate.min.js"></script>
<script src="/v2/js/underscore.js"></script>
<script src="/v2/js/chosen/chosen.jquery.js"></script>
{include file="v2/test/wizard/html_templates.php"}
{literal}
<script src="/v2/js/wizard.js"></script>

{/literal}
</body>
</html>