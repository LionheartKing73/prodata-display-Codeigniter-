{include file="campclick/sections/header1.php"}


<div class="alert alert-success" role="alert"> Your remarketing was created successfully</div>
You can see your remarketings by visiting
 {foreach from=$audience item=element}
                        <p><a href="https://adwords.google.com/cm/CampaignMgmt?authuser=0&__u=7285608738&__c=7043040345&syncServiceIdentity=true#ul_re.{$element}.ul_re">Remarketing Link</a></p>
                        {/foreach}

{include file="campclick/sections/footer.php"}