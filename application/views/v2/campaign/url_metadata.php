{include file="v2/sections/header.php"}
<style>


    button.btn.btn-default{
        margin-left: 5px !important;
    }
</style>
<div class="theme-report-row-wrap">
    <div class="theme-container container-fluid">
        <h1>Keyword Research Tool</h1>
        <h4></h4>
        <!--        <div class="theme-report-campaign-list-row">-->

        <div class="row">
            <div id="url_builder_block" class="col-md-6" style="margin-bottom: 15px;">
                <form id="url_metadata_form" action="javascript:void();">
                    <div class="input-group">

                        <input placeholder="Website URL *" required type="url" class="form-control" id="metadata_url" name="metadata_url">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit">Get Keywords</button>
                        </span>
                    </div>
                </form>
            </div>
            <div class="col-xs-12">
                <form id="check_metadata_form" action="javascript:void();">
                    <p id="generated_url"></p>
                    <span class="input-group-btn">
                        <button class="btn btn-default copy-btn" style="display: none; margin-left: 45%;" type="submit">Copy</button>
                    </span>
                </form>
                <p class="hidden" id="copyText"></p>
            </div>
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
<script src="/v2/js/jquery.density.js"></script>


<script>
    function copyToClipboard(element) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).text()).select();
        document.execCommand("copy");
        $temp.remove();
    }

    $(document).ready(function () {
        $("#url_metadata_form").submit(function () {
            $.ajax({
                url: "/v2/campaign/get_meta_data_by_url",
                type: "POST",
                dataType: "json",
                data: $('#url_metadata_form').serialize(),
                success: function (data) {
                    console.log(data.metadata);
                    if (data.status == 'SUCCESS') {
                        $("#generated_url").html('In the website <b>' + data.metadata.url + '</b><br><br>There are <b>' + data.metadata.word_count + '</b> words on this page<br><br>' + data.metadata.table_str + '<br>' + data.metadata.table_str2 + '<br>' + data.metadata.table_str3).slideDown();
                        setTimeout(function () {
                        }, 2000);
                        $(".copy-btn").css("display", "block");
                    }
                    else {
                        console.log(data)
//                    $('.pass_alert').text(data.msg);

                        setTimeout(function () {
                        }, 2000);
                    }
                }
            })
        });
        $("#check_metadata_form").submit(function () {
            var selectedCheckboxValue = "";
            $('input.forminput:checked').each(function() {

                selectedCheckboxValue += $(this).val() + "<\n>";

            });
            $("#copyText").html(selectedCheckboxValue);
            copyToClipboard('#copyText');
            //alert(selectedCheckboxValue);

        })
    });

</script>

</body>
</html>