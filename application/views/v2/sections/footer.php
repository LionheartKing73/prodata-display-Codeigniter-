<div class="theme-container padding_0">
    <footer>
        <div class="container">
            <div class="col-md-6 col-sm-6 col-xs-12 padding_0">
                <h5>Copyright &copy; 2017 ProDataFeed LLC. All Rights Reserved</h5>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12 padding_0">
                <ul >
                    <li><a href="http://www.prodatafeed.com/privacy-policy.html" target="_blank">Privacy Policy</a></li>
                    <li class="border-right"></li>
                    <li><a href="http://www.prodatafeed.com/terms-conditon.html" target="_blank">Terms and Conditions</a></li>
                    <li class="border-right"></li>
                    <li><a href="http://www.prodatafeed.com/contactus.php" target="_blank">Support</a></li>
                </ul>
            </div>
        </div>
    </footer>
</div>
<!-- Google Code for Prodata Display Conversion Page -->
<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = 956401398;
    var google_conversion_language = "en";
    var google_conversion_format = "3";
    var google_conversion_color = "ffffff";
    var google_conversion_label = "-58YCJLqzWwQ9o2GyAM";
    var google_remarketing_only = false;
    /* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
    <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/956401398/?label=-58YCJLqzWwQ9o2GyAM&amp;guid=ON&amp;script=0"/>
    </div>
</noscript>
{if !empty($domain_data)}
<style>
    footer {
        background-color: {$domain_data.footer_color};
    }

    body {
        background: {$domain_data.background_color};
    }
    .pdf_container {
        background-color: {$domain_data.content_background_color};
        color: {$domain_data.content_text_color};
    }
    .header_row_for_tables {
        background-color: {$domain_data.block_header_background_color};
        border: 1px solid {$domain_data.block_header_background_color};
    }
    .header_row {
        background-color: {$domain_data.block_header_background_color};
        border-bottom: 1px solid {$domain_data.block_header_background_color};
    }
    .header_text{
        color: {$domain_data.block_header_text_color};
    }
    .header_icon{
        color: {$domain_data.block_header_icon_color};
    }
    .chart_icon{
        color: {$domain_data.block_header_icon_color};
    }
    .campaign_info_header{
        color: {$domain_data.block_content_text_color};
    }
    .block_title{
        color: {$domain_data.block_content_text_color};
    }
    .block_border {
        border: 1px solid {$domain_data.block_header_background_color};
    }
    .block_border_top {
        border-top: 1px solid {$domain_data.block_header_background_color};
    }
    .block_device{
        border: 1px solid {$domain_data.block_header_background_color};
        background-color: {$domain_data.block_header_background_color};
    }
    table tr th, table tr td{
        border:1px solid {$domain_data.block_header_background_color} !important;
    }
    .theme-submit-group .theme-submit-control, .theme-tabbed-form-submit, .theme-back-btn, .theme-ad-save.theme-submit-control {
        background: {$domain_data.active_button_color};
        border: 2px solid {$domain_data.active_button_color} !important;
    }

    button.btn.btn-success.pull-right, a.btn.btn-success.btn-create-campaign {
        background-color: {$domain_data.active_button_color} !important;
    }
    .btn_next_step {
        background: {$domain_data.active_button_color};
        border-color: {$domain_data.active_button_color};
        border: 1px solid {$domain_data.active_button_color};
    }
    .btn_previous_step {
        background: {$domain_data.passive_button_color};
        border-color: {$domain_data.passive_button_color};
        border: 1px solid {$domain_data.passive_button_color};
    }
    .theme-submit-group .theme-submit-control:hover {
        color:{$domain_data.active_button_color};
    }
    .btn-create-campaign {
        border-color: {$domain_data.active_button_color};
    }
    .btn_next_step:hover {
        color: {$domain_data.active_button_color};
    }
    .btn_previous_step:hover {
        color: {$domain_data.passive_button_color};
    }
    .theme-submit-group .theme-create-add-btn:hover {
        color: {$domain_data.active_button_color};
    }
    .user-info button.btn-info, .card-info button.btn-info, .pass-info button.btn-info, .createviewer-info button.btn-info, .fb-button a.btn-info, .manageAccess-info button.btn-info, .generateSnippet-info button.btn-info{
        background-color: {$domain_data.active_button_color};
        border-color: {$domain_data.active_button_color};
    }

    .campaign-progress .btn-success {
        background-color: {$domain_data.active_button_color};
        border-color: {$domain_data.active_button_color};
    }

    .campaign-progress .btn-success:hover, .campaign-progress .btn-success:active, .campaign-progress .btn-success:focus {
        background-color: {$domain_data.active_button_color};
        border-color: {$domain_data.active_button_color};
    }

    .geoloc-button button.btn-info, .addBudget-button button.btn-info, .edit-date-button button.btn-info, .start-date-button button.btn-info {
        background: {$domain_data.active_button_color};
        border-color: {$domain_data.active_button_color};
    }

    .geoloc-button .btn-default, .addBudget-button button.btn-default, .edit-date-button button.btn-default, .start-date-button button.btn-default {
        background: {$domain_data.passive_button_color};
        border-color: {$domain_data.passive_button_color};
    }

    .news-feed .addButton .btn-success {
        background-color: {$domain_data.active_button_color}  !important;
        border-color: {$domain_data.active_button_color};
    }

    .modal-footer .btn-primary {
        background-color: {$domain_data.active_button_color};
        border-color: {$domain_data.active_button_color};
    }

    .addKeyword button.btn-info {
        background-color: {$domain_data.active_button_color};
        border-color: {$domain_data.active_button_color};
    }

    .modal-footer .btn-default {
        background-color: {$domain_data.passive_button_color};
        border-color: {$domain_data.passive_button_color};
    }
</style>
{/if}