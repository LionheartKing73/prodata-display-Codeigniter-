{include file="v2/sections/header.php"}

<div class="theme-report-row-wrap">
    <div class="theme-container container-fluid">
        <h1>URL Builder</h1>
        <h4>Create custom campaign tracking parameters for your advertisements.</h4>
<!--        <div class="theme-report-campaign-list-row">-->

            <div class="row" >
                <div id="url_builder_block" class="col-md-6">
                    <form id="url_builder_form" >
                        <div class="form-group">
                            <label for="url">Website URL *</label>
                            <input required type="url" class="form-control" id="website_url" name="url">
                            <span class="info_row" >(e.g. http://reporting.prodata.media/test.html)</span>
                        </div>
                        <div class="form-group">
                            <label for="url">Campaign Source *</label>
                            <input required type="text" class="form-control" id="campaign_source" name="utm_source">
                            <span class="info_row" >(referrer: prodata, pdm, etc)</span>
                        </div>
                        <div class="form-group">
                            <label for="url">Campaign Medium *</label>
                            <input required type="text" class="form-control" id="campaign_medium" name="utm_medium">
                            <span class="info_row" >(marketing medium: cpc, banner, email)</span>
                        </div>
<!--                        <div class="form-group">-->
<!--                            <label for="url">Campaign Term</label>-->
<!--                            <input type="text" class="form-control" id="campaign_term" name="term">-->
<!--                            <span class="info_row" >(identify the paid keywords)</span>-->
<!--                        </div>-->
                        <div class="form-group">
                            <label for="url">Campaign Content</label>
                            <input type="text" class="form-control" id="campaign_content" name="content">
                            <span class="info_row" >(use to differentiate ads)</span>
                        </div>
                        <div class="form-group">
                            <label for="url">Campaign Name *</label>
                            <input required type="text" class="form-control" id="campaign_name" name="utm_campaign">
                            <span class="info_row" >((product, promo code, or slogan)</span>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-default">Generate Url</button>
                        </div>
                    </form>

                </div>
                <div class="col-xs-12" >
                    <p id="generated_url" ></p>
                </div>
            </div>
            <div class="row">
                <table class="info_table">
                    <h3>More information and examples for each parameter</h3>
                    <tbody>
                    <tr>
                        <td>
                            <p>Campaign Source (utm_source)</p>
                        </td>
                        <td>Required. Use <strong>utm_source</strong> to identify a search engine, newsletter name, or other source.<br>
                            <em>Example</em>: utm_source=google</td>
                    </tr>
                    <tr>
                        <td>
                            <p>Campaign Medium (utm_medium)</p>
                        </td>
                        <td>Required. Use <strong>utm_medium</strong> to identify a medium such as email or cost-per- click.<br>
                            <em>Example</em>: utm_medium=cpc</td>
                    </tr>
                    <tr>
                        <td>
                            <p>Campaign Term (utm_term)</p>
                        </td>
                        <td>Used for paid search. Use <strong>utm_term</strong> to note the keywords for this ad.<br>
                            <em>Example</em>: utm_term=running+shoes</td>
                    </tr>
                    <tr>
                        <td>
                            <p>Campaign Content (utm_content)</p>
                        </td>
                        <td>Used for A/B testing and content-targeted ads. Use <strong>utm_content</strong> to differentiate ads or links that point to the same URL.<br>
                            <em>Examples</em>: <code>utm_content=logolink <em>or</em> utm_content=textlink</code> </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Campaign Name (utm_campaign)</p>
                        </td>
                        <td>Required. Used for keyword analysis. Use <strong>utm_campaign </strong>to identify a specific product promotion or strategic campaign.<br>
                            <em>Example</em>: <code>utm_campaign=spring_sale</code></td>
                    </tr>
                    </tbody>
                </table>
            </div>
<!--        </div>-->
    </div>
</div>
{include file="v2/sections/footer.php"}
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script src="/v2/js/jquery-2.0.3.min.js"></script>
<script src="/v2/js/jquery.validate.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed --> 
<script src="/v2/js/bootstrap.min.js"></script>


<script>
    $("#url_builder_form").validate({
        submitHandler: function(form) {

            var newUrl = $('#website_url').val();

            if(newUrl.indexOf("?") < 0) {
                newUrl += '?';
            }
            else {
                newUrl += '&';
            }

            $("#url_builder_form input").each(function () {

                if($(this).attr('name') != 'url' && $(this).val() ) {

                    var inputContent = $(this).val().replace(/ /g, "%20");
                    newUrl += $(this).attr('name') + '=' + inputContent;
                    if($(this).attr('name') != 'utm_campaign') {
                        newUrl += '&';
                    }
                }
            });
            $("#generated_url").html(newUrl).slideDown();
            console.log($(this).attr("action"));
        }
    });
</script>

</body>
</html>