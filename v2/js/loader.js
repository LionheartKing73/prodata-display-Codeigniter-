var glob_element;

var show_loader = function (element) {
    if (element !== undefined) {
        glob_element = element
        element.attr('disabled', true);
    }

    $('#loader_div').removeClass('hidden');
};

var hide_loader = function () {
    
    if (glob_element !== undefined) {
        glob_element.removeAttr('disabled');
    }
    $('#loader_div').addClass('hidden');
};