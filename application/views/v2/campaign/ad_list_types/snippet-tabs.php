<div class="col-xs-12">
    <button type="button" class="btn btn_iframe_code">Get Code</button>
    <div class="snipet_block" >
        <ul  class="nav nav-pills snipet_tabs">
            <li class="active">
                <a class="tab_link" href="#iframe_tab_{$ad.id}" data-toggle="tab">Iframe</a>
            </li>
            <li>
                <a class="tab_link" href="#js_tab_{$ad.id}" data-toggle="tab">Javascript</a>
            </li>
        </ul>
       <div class="tab-content clearfix">

          {if $ad.creative_type === 'VIDEO' || $ad.creative_type === 'VIDEO_YAHOO' || $ad.creative_type === 'VIDEO-CLICKS'}
             <div class="code_block tab-pane active" id="iframe_tab_{$ad.id}">
                &ltiframe src="{$domain_data.domain}/tracking/ad_video_view/{$ad.id}?redir=%%CLICK_URL_ESC_ESC%%&ord=%%CACHEBUSTER%%" scrolling='no' marginheight="0"
                marginwidth="0"
                frameborder="0"
                width="{$ad.creative_width}"
                height="{$ad.creative_height}" &gt
                &lt/iframe&gt
            </div>
            <div class="code_block tab-pane break-words" id="js_tab_{$ad.id}">
                {$url_encode = $ad.original_url|urlencode}
                {$url_d_encode = $url_encode|urlencode}
                &lt;script&gt;
                var tracking_ad_id = {$ad.id},
                video_url = "{$ad.video_url}",
                tracking_campaign_id = {$campaign.id},
                creative_width = {$ad.creative_width},
                creative_height = {$ad.creative_height},
                creative_src = "{$ad.creative_url|replace:"http://":"https://"}",
                destination_url = "{$ad.destination_url|replace:"http://":"https://"|replace:"/c2/":"/c3/"}?redir=%%CLICK_URL_ESC%%{$url_d_encode}"
                &lt;/script&gt;
                <br>
                &lt;script src="https://content.jwplatform.com/libraries/pFTCkdMP.js"&gt;&lt;/script&gt;
                &lt;script src="{$site_url|replace:"http://":"https://"}v2/js/add_impression_video.js?ord=%%CACHEBUSTER%%" &gt;
                &lt;/script&gt;
            </div>
          {else}
            <div class="code_block tab-pane active" id="iframe_tab_{$ad.id}">
                &ltiframe src="{$domain_data.domain}/tracking/ad_iframe_view/{$ad.id}?redir=%%CLICK_URL_ESC_ESC%%&ord=%%CACHEBUSTER%%" scrolling='no' marginheight="0"
                marginwidth="0"
                frameborder="0"
                width="{$ad.creative_width}"
                height="{$ad.creative_height}" &gt
                &lt/iframe&gt
            </div>
            <div class="code_block tab-pane break-words" id="js_tab_{$ad.id}">
                {$url_encode = $ad.original_url|urlencode}
                {$url_d_encode = $url_encode|urlencode}
                &lt;script&gt;
                var tracking_ad_id = {$ad.id},
                tracking_campaign_id = {$campaign.id},
                creative_width = {$ad.creative_width},
                creative_height = {$ad.creative_height},
                creative_src = "{$ad.creative_url|replace:"http://":"https://"}",
                destination_url = "{$ad.destination_url|replace:"http://":"https://"|replace:"/c2/":"/c3/"}?redir=%%CLICK_URL_ESC%%{$url_d_encode}"
                &lt;/script&gt;
                <br>
                &lt;script src="{$site_url|replace:"http://":"https://"}v2/js/add_impression.js?ord=%%CACHEBUSTER%%" &gt;
                &lt;/script&gt;
            </div>
            {/if}
        </div>
    </div>
</div>
