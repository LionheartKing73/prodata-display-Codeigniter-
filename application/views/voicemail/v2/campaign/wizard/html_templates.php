<script type="text/template" id="ad_form_template">
    <div class="theme-scrollable-ad-row">
        <span class="theme-list-remove-icon closer"></span>
        <div class="theme-ad-banner-content theme-display-table theme-no-gutter">
            <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                <figure>
                    <% var src = (data.creative_url) ? data.creative_url : '/v2/images/report-template/no-ad-logo-thumb.png'; %>
                    <a href=""><img src="<%= src %>" alt="" /></a>
                </figure>
            </div>
            <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                <div class="theme-ad-content">
                    <h2><a href="<%= data.destination_url %>" target="_blank" data-type="title"><%= data.title %></a></h2>
                    <p data-type="description"><%= data.description_1 %><%= data.description_2 %></p>
                    <p class="theme-ad-url-line"> <a href="<%= data.destination_url %>" target="_blank" data-type="display_url"><%= data.display_url %></a></p>
                </div>
            </div>
        </div>
        <div class="theme-ad-action-btn-group">
            <a id="edit-theme-ad" href="#" class="edit-theme-ad">Edit</a>
            <a id="url-theme-ad" href="<%= data.destination_url %>" class="url-theme-ad" target="_blank">View Url</a>
        </div>
        <% var ad_value = JSON.stringify(data); %>

<!--        <input name="ads[]" type="hidden" value='<%= ad_value %>' class="hidden_inputs"/>-->
        <input name="ads[]" type="hidden" value='wtf' class="hidden_inputs"/>
    </div>
</script>

<script type="text/template" id="html_pars_template">
    <%
        if (data){
    %>
            <table class="custom-itable">
                <thead>
                    <tr>
                        <td>Totals:</td>
                        <td class="user_clicks">0</td>
                        <td id="table_total_pers">0%</td>
                    </tr>
                    <tr>
                        <td><h5><strong>Destination URL</strong></h5></td>
                        <td><h5><strong>Click Count</strong></h5></td>
                        <td><h5>%</h5></td>
                    </tr>
                </thead>
                <tbody>
                            
    <%
            _.each(data, function(num){ 
    %>
                <tr style="background-color:#f8f8f8;" class="">
                    <% var val_href = num.getAttribute("href");%>
                    <td class="href_td"><%= val_href %></td>
                    <td><input type="text" value="0" placeholder="" class="theme-geoform-control theme-form-control click-count" /></td>
                    <td>
                        <input type="text" value="0" placeholder="" style="padding:5px 15px; border:2px solid #dbdada; border-radius:3px; font-size:14px;" class="percentage" />
                        %
                    </td>
                </tr>            
    <%  
            });
    %>      
                <% var ppc_links_value = JSON.stringify(data); %>
                <input name="ppc_links" value="<%= ppc_links_value %>" type="hidden"/>
                </tbody>
            </table>
    <%
        } 
    %>
</script>
<script type="text/template" id="last_page_tr">
    <%
        _.each(data, function(num){ 
    %>
            <tr style="background-color:#f8f8f8;" class="">
                <td class="href_td"><%= num.destination_url %></td>
                <td><%= num.max_clicks %></td>
                <td><%= num.perc %></td>
            </tr>            
    <%  
        });
    %>   
</script>

