$(document).ready(function () {

     setTimeout('$(".session-block").hide();', 5000);
     

    $("body").on("change", "#io-number", function () {
        $(".io-exists").removeClass("alert-danger");
        $(".io-exists").hide();

        $.ajax({
            url: "/adword/checkIO",
            type: "POST",           
            data: {io: $(this).val()},
            success: function (res) {
                var data = JSON.parse(res);
                if(data.exists){
                    $(".io-exists").addClass("alert-danger");
                    $(".io-exists").toggle();
                    $(".io-exists").text("Sorry. The IO# already exists. Try again!");
                }
            }
        });
    });

    $("body").on("change", "#remarketing_campaign", function(){
        if($(this).val()==1){
            $(".remarketing-block").show();
        }else{
            $(".remarketing-block").hide();
        }
    });
    
    $("body").on("change", ".show-details", function(){
        var a=$(".show-details:checked");
 
        if(a.length>0){
            $(".details-block").show();
        }else{
            $(".details-block").hide();
        }
    });


    
     $("body").on("change", "#datepicker", function(){
                var start= $("#datepicker").datepicker("getDate");
                start.setDate(start.getDate() + 1);

         if(new Date($("#end-date").val()) < start){
             $("#end-date").val("");
         }
                $("#end-date").datetimepicker("destroy");
                $("#end-date").datetimepicker({  minDate: start });
       });
       
       $(window).on('beforeunload ',function() {
          $.ajax({
            url: "/adword/onUnload",            
            type: "POST",           
            data: {data: 1},
            success: function (res) {   
                       
            }
        });       
    });

    $("#io-number").keypress(function(key){
        if((key.charCode>=65 && key.charCode<=90) || (key.charCode>=97 && key.charCode<=122) || (key.charCode>=48 && key.charCode<=57) || key.charCode==45 || key.charCode==32){
            if(key.charCode==32){
                key.preventDefault();
               $(this).val($(this).val()+"-");
            }
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

    if($("#max-clicks").val() || $("#max-impressions").val() || $("#max_spend").val() || $("#end-date").val()){
        $("#show_details").attr("checked", true);
        $(".details-block").show();

    }

});


   