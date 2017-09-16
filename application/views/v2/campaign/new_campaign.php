{include file="v2/sections/header.php"}
<base href="{$base_url}">
<link href="{$base_url}/public/css/styles.css" rel="stylesheet" type="text/css"/>
<link href="{$base_url}/public/jquery-ui/jquery-ui.css" rel="stylesheet" type="text/css"/>
<link href="/v2/css/jquery.steps.css" rel="stylesheet" type="text/css">
<link href="/v2/js/chosen/chosen.min.css" rel="stylesheet" type="text/css"/>
<link href="/v2/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<link href="/v2/css/ion.rangeSlider.skinModern.css" rel="stylesheet" type="text/css"/>
<link href="/v2/css/ion.rangeSlider.css" rel="stylesheet" type="text/css"/>
<link href="/v2/css/jstree/jstree.min.css" rel="stylesheet" type="text/css"/>

<div class="theme-report-row-wrap">
    <div class="theme-container">
        <div class="theme-report-campaign-list-row">
            <div class="theme-report-tabbed-section">
                <nav class="theme-reoprt-tabbed-nav" role="navigation">
                    <form id="example-advanced-form" action="/v2/html/creat_campaign" >


                        <h3>Campaign Type</h3>
                        {include file="v2/campaign/wizard/campaign_type.php"}

                        <h3 class="display_none">Campaign Information</h3>
                        {include file="v2/campaign/wizard/campaign_information.php"}

                        <h3 class="display_none">Day-Time Parting</h3>
                        {include file="v2/campaign/wizard/time_parting.php"}

                        <h3 class="display_none">Digital Rooftop</h3>
                        {include file="v2/campaign/wizard/digital_rooftop.php"}

                        <h3 class="display_none">Creative</h3>
                        {include file="v2/campaign/wizard/creative.php" links=$fb_pages}

                        <h3 class="display_none">Review</h3>
                        {include file="v2/campaign/wizard/review.php"}
                    </form>
                </nav>
            </div>
        </div>
    </div>
</div>

{include file="v2/sections/scripts.php"}
<script src="{$base_url}public/jquery-ui/jquery-ui.js" type="text/javascript"></script>
<link href="{$base_url}public/plupload/css/jquery.ui.plupload.css" rel="stylesheet" type="text/css"/>
<script src="{$base_url}public/plupload/plupload.full.min.js" type="text/javascript"></script>
<script src="{$base_url}public/plupload/jquery.ui.plupload.js" type="text/javascript"></script>
<script src="{$base_url}public/plupload/plupload_wizard.js" type="text/javascript"></script>
<script src="{$base_url}public/plupload/mraid.js" type="text/javascript"></script>
<script src="/static/js/heatmap.min.js"></script>
<script src="/v2/js/datetime-picker.jquery.js"></script>
<script src="/v2/js/jquery.steps.min.js"></script>
<script src="/v2/js/jquery.validate.min.js"></script>
<script src="/v2/js/additional-methods.min.js"></script>
<script src="/v2/js/underscore.js"></script>
<script src="/v2/js/chosen/chosen.jquery.js"></script>
<script src="/v2/js/iscript.js"></script>
<script src="/v2/js/ion.rangeSlider.min.js"></script>
<script src="/v2/js/jstree.min.js"></script>

{include file="v2/campaign/wizard/html_templates.php"}
<script>
    var availableSo = JSON.parse('{$so_numbers|@json_encode}');
    //console.log(availableSo);
</script>
<script src="/v2/js/wizard.js"></script>

{include file="v2/sections/footer.php"}
</body>
</html>