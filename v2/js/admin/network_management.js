$(document).ready(function(){



    //var for_status_item = $('.for_status').each(function() {
    //    $( this).text();
    //    console.log($( this).text());
    //});
    ////console.log(for_status_item);return false;
    //if(for_status_item == 'Y'){
    //    console.log(333);
    //    $('.for_status').text('NO');
    //} else if ($('.for_status').text() == 'Y'){
    //    $('.for_status').text('YES');
    //}

    //$('.for_status').valeditable({
    //    source: [
    //          {value: "Y", text: 'YES'},
    //          {value: "N", text: 'NO'}
    //       ]
    //});
    


    
    $('.int_editable').editable({
        validate: function(value) {
            if(!$.isNumeric(value)) {
                return ' The field must be number';
            }
        }
    });



});