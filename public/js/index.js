$(document).ready(function () {
    setTimeout('$(".session-block").hide();', 5000);

    $(".delete").click(function(){
        var conf=confirm("Are you sure you want to delete the IO with IO#="+$(this).attr("data-value"));
        if(conf){
           return true;
        }
        return false;
    });

    $("#max-clicks").keypress(function(key){
        if(key.charCode>=48 && key.charCode<=57){
            return true;
        }

        return false;
    });

    $("#max-impressions").keypress(function(key){
        if(key.charCode>=48 && key.charCode<=57){
            return true;
        }

        return false;
    });

    $("#max-spend").keypress(function(key){
        if(key.charCode>=48 && key.charCode<=57 ||key.charCode==46){
            return true;
        }

        return false;
    });
});