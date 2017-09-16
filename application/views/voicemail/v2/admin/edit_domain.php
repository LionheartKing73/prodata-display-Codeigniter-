{include file="v2/sections/header.php"}

<div class="theme-report-row-wrap">
    <div class="theme-container container-fluid">    
        <div class="theme-report-campaign-list-row">
            <div class="row" >
                <div class="col-md-6">
                    <form role="form" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="url">Domain name:</label>
                            <input type="text" class="form-control" id="url" name="url" value="{$domain.name}">
                        </div>
                        {if $domain.logo}
                            <img src="/v2/images/domain_logos/{$domain.logo}" class="img_margins"/>
                        {/if}
                        <div class="form-group">
                            <label for="logo">Logo:</label>
                            <input type="file" id="logo" name="image_file">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-default">Edit Domain</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script src="/v2/js/jquery-2.0.3.min.js"></script> 
<!-- Include all compiled plugins (below), or include individual files as needed --> 
<script src="/v2/js/bootstrap.min.js"></script>
</body>
</html>