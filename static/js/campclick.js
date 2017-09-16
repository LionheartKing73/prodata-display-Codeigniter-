/*
 * MassMailing JS Controller Script
 * Author: Jason Korkin <jkorkin@safedatatech.com>
 * Last modified: 2012-07-16
 * 
 */
(function ($)	{
	var methods = {
		init : function(options)	{
			// init as needed
		},

		test_temp: function()	{
			alert("test");
		},
		
		urltest: function()	{
			$("#testblock").html("");
			
			$.ajax({
				url: "/lists/urltest",
				type: "POST",
				dataType: "json",
				data: { url: $("#data_import_url").val() },
				success: function(msg)	{
					if (msg.status == "SUCCESS")	{
						if (msg.message === false)	{
							// this is a hard failure
							$("#testblock").html("URL Does Not Exist.");
						} else if (msg.message == "HTTP/1.1 404 Not Found"){
							// this is a 404 error
							$("#testblock").html("URL 404 Error");
						} else {
							// success
							$("#testblock").html("URL Test Succeeded");
						}
					}
				}
			});
		},
		
		generate_content: function()	{
			var validate = $("#create_form").validate().form();
			
			if (validate)	{
				$.ajax({
					url: "/campclick/generate_code",
					type: "POST",
					dataType: "json",
					data: { name: $("#create_name").val(), io: $("#io").val(), message: $("#message").val(), default_url: $("#default_url").val() },
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							$("#content_table").hide();
							$("#message_result").val(msg.message);
							$("#io_result").html($("#io").val());
							$("#url_result").html(msg.url);
							$("#name_result").html($("#create_name").val());
							$("#content_results").show();
						}
					}
					
				});
			}
		}
	};

	$.campclick = function( method ) {
		 if ( methods[method] ) {
			 return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		 } else if ( typeof method === 'object' || ! method ) {
			 return methods.init.apply( this, arguments );
		 } else {
			 $.error( 'Method ' +  method + ' does not exist on campclick plugin');
		 }
	};
})( jQuery );

$.campclick();

function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }

    var pre = document.createElement('pre');
    pre.innerHTML = out;
    document.body.appendChild(pre)
}
