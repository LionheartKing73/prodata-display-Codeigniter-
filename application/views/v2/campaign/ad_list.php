{include file="v2/sections/header.php"}

<script>
    {if !empty($show_ad_id)}
    var show_ad_id = {$show_ad_id};
    {else}
    var show_ad_id = false;
    {/if}
</script>

<base href="{$base_url}">
<link href="{$base_url}/public/css/styles.css" rel="stylesheet" type="text/css"/>
<link href="{$base_url}/public/jquery-ui/jquery-ui.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="/v2/css/jquery.steps.css">
<link href="/v2/js/jquery-toggles-master/css/toggles.css" rel="stylesheet" type="text/css"/>
<link href="/v2/js/jquery-toggles-master/css/themes/toggles-light.css" rel="stylesheet" type="text/css"/>
{include file="v2/campaign/ad_list_types/"|cat:$campaign.campaign_type|cat:".php"}
{include file="v2/sections/scripts.php"}

<script src="{$base_url}/public/jquery-ui/jquery-ui.js" type="text/javascript"></script>
<link href="{$base_url}/public/plupload/css/jquery.ui.plupload.css" rel="stylesheet" type="text/css"/>
<script src="{$base_url}/public/plupload/plupload.full.min.js" type="text/javascript"></script>
<script src="{$base_url}/public/plupload/jquery.ui.plupload.js" type="text/javascript"></script>
<script src="/v2/js/jquery-toggles-master/toggles.min.js"></script>
<script src="/v2/js/ad-edit.js"></script>
<script src="/v2/js/ad_list/{$campaign.campaign_type}.js"></script>
{include file="v2/sections/footer.php"}


<script>
    $('.btn_iframe_code').click(function () {
        $(this).next().slideToggle('slow');
    })
</script>


</body>
</html>
