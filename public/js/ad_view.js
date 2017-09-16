$(document).ready(function () {
    $(".delete").click(function(){
        var conf=confirm("Are you sure you want to delete the ad with ID="+$(this).attr("data-value"));
        if(conf){
            return true;
        }

        return false;
    });

});