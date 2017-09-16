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
		
		login : function()	{
			$("#username").removeClass("error");
			$("#password").removeClass("error");
			$("#err_login").hide();
			
			var errorCnt = 0;
			
			if ($("#username").val() == "")	{
				$("#username").addClass("error");
				errorCnt++;
			}
			
			if ($("#password").val() == ""){
				$("#password").addClass("error");
				errorCnt++;
			}
			
			if (errorCnt == 0){
				$.ajax({
					url: "/auth/login",
					type: "POST",
					dataType: "json",
					data: { username: $("#username").val(), password: $("#password").val() },
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							// redirect
							document.location.href="/welcome";
						} else if (msg.status == "NO_LOGIN")	{
							// invalid login
							$("#username").addClass("error");
							$("#password").addClass("error");
							$("#err_login").show();
						} else {
							// error
							$("#err_login").show();
						}
					}
				});
			}
		},
		
		logout : function() {
			$.ajax({
				url: "/auth/logout",
				type: "POST",
				dataType: "json",
				success: function(msg)	{
					if (msg.status == "SUCCESS")	{
						document.location.href="/welcome";
					} else {
						alert("Error on logout");
					}
				}
			});
		},
	
		change_password : function()	{
			$("#success_password").hide();
			$("#err_password").hide();
			
			if ($("#password_new").val() == $("#password_new2").val())	{
				$.ajax({
					url: "/profile/changepassword",
					type: "POST",
					dataType: "json",
					data: { password: $("#password").val(), new_password: $("#new_password").val(), new_password2: $("#new_password2").val() },
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							$("#password").val("");
							$("#new_password").val();
							$("#new_password2").val();
							$("#success_password").html(msg.message).show();
						} else {
							$("#err_password").html(msg.message).show();
						}
					}
				});
			} else {
				$("err_password").html("New passwords do not match.").show();
			}
		},
				
		resetPassword: function()	{
			$("#forget_password_error").hide();
			$("#forget_password_success").hide();

			$.ajax({
				url: "/auth/resetpassword",
				type: "POST",
				dataType: "json",
				data: { email: $("#forget_password_email").val() },
				success: function(msg)	{
					if (msg.status == "SUCCESS")	{
						$("#forget_password_success").show();
					} else {
						$("#forget_password_error").show();
					}
				}
			});
		},
		
		reset_password_change : function()	{
			$("#success_password").hide();
			$("#err_password").hide();
			
			if ($("#password_new").val() == $("#password_new2").val())	{
				$.ajax({
					url: "/auth/changepassword_reset",
					type: "POST",
					dataType: "json",
					data: { new_password: $("#new_password").val(), new_password2: $("#new_password2").val(), password_reset_key: $("#password_reset_key").val() },
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							$("#new_password").val();
							$("#new_password2").val();
							$("#password_reset_key").val();
							$("#success_password").html(msg.message).show();
						} else {
							$("#err_password").show();
						}
					}
				});
			} else {
				$("err_password").html("New passwords do not match.").show();
			}
		},
		
		bof_cancel: function()	{
			var cnfrm = confirm("Are you sure you wish to cancel this order request?");
			if (cnfrm)	{
				document.location.href="/welcome";
			} else {
				return false;
			}
		},

		bof_order_create: function()	{
			var validator = $("#bof_create_form").validate().form();
			if (validator)	{
				$.ajax({
					url: "/communication/bof_create",
					type: "POST",
					dataType: "json",
					data: { bof_create_max_recipients: $("#bof_create_max_recipients").val(), bof_create_risk_level: $("#bof_risk_level").val(), bof_create_content_html: $("#bof_create_content_html").val(), bof_create_name: $("#bof_create_name").val(), bof_create_agency: $("#bof_create_agency").val(), bof_create_list: $("#bof_create_list").val(), bof_create_io: $("#bof_create_io").val(), bof_create_test_date: $("#bof_create_test_date").val(), bof_create_deploy_date: $("#bof_create_deploy_date").val(), bof_create_subject: $("#bof_create_subject").val(), bof_create_from_name: $("#bof_create_from_name").val(), bof_create_from_email: $("#bof_create_from_email").val(), bof_create_seed: $("#bof_create_seed").val(), bof_create_instructions: $("#bof_create_instructions").val() },
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							document.location.href="/communication/queue/U";
						}
					}
				});
			} else {
				return false;
			}
		},
		
		bof_order_update: function()	{
			var validator = $("#bof_create_form").validate().form();
			if (validator)	{
				$.ajax({
					url: "/communication/bof_update",
					type: "POST",
					dataType: "json",
					data: { bof_create_max_recipients: $("#bof_create_max_recipients").val(), bof_create_risk_level: $("#bof_risk_level").val(), bof_create_id: $("#bof_create_id").val(), bof_create_content_html: $("#bof_create_content_html").val(), bof_create_name: $("#bof_create_name").val(), bof_create_agency: $("#bof_create_agency").val(), bof_create_list: $("#bof_create_list").val(), bof_create_io: $("#bof_create_io").val(), bof_create_test_date: $("#bof_create_test_date").val(), bof_create_deploy_date: $("#bof_create_deploy_date").val(), bof_create_subject: $("#bof_create_subject").val(), bof_create_from_name: $("#bof_create_from_name").val(), bof_create_from_email: $("#bof_create_from_email").val(), bof_create_seed: $("#bof_create_seed").val(), bof_create_instructions: $("#bof_create_instructions").val() },
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							document.location.href="/communication/queue/Y";
						}
					}
				});
			} else {
				return false;
			}
		},
		
		agency_load: function(id)	{
			$("#modal_agency_id").val(id);
			$.ajax({
				url: "/corporate/agency/" + id,
				dataType: "json",
				success: function(msg)	{
					if (msg.status == "SUCCESS")	{
						$("#modal_agency_name").val(msg.message.name);
						$("#modal_agency_is_active").val(msg.message.is_active);
					}
				}
			});
		},
		
		agency_save: function()	{
			var validator = $("#modal_agency_form").validate().form();
			
			if (validator)	{
				$.ajax({
					url: "/corporate/agency_save",
					type: "POST",
					dataType: "json",
					data: { "id": $("#modal_agency_id").val(), "name": $("#modal_agency_name").val(), "is_active": $("#modal_agency_is_active").val() },
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							document.location.reload();
						}
					}
				});
			}
		},
		
		agency_remove: function(id)	{
			$.ajax({
				url: "/corporate/agency_remove/" + id,
				dataType: "json",
				success: function(msg)	{
					if (msg.status == "SUCCESS")	{
						document.location.reload();
					}
				}
			});
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
		
		import_data: function()	{
			$("#data_import_success").hide();
			var validator = $("#data_import_form").validate().form();
			
			if (validator)	{
				$.ajax({
					url: "/lists/import_data",
					type: "POST",
					dataType: "json",
					data: { 'name': $("#data_import_name").val(), 'url': $("#data_import_url").val() },
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							// success
							$("#data_import_name").val("");
							$("#data_import_url").val("");
							$("#data_import_success_message").html("List Added; Please Allow Up To 30-Minutes For Processing To Begin.");
							$("#data_import_success").show();
						} else {
							// error
							alert("Unable to create list.  Try again later.");
						}
					}
				});
			} else {
				return false;
			}
		},
		
		import_remove: function(id)	{
			var cnfrm = confirm("Are you sure you want to remove this list and associated data?");
			
			if (cnfrm)	{
				$.ajax({
					url: "/lists/import_remove/" + id,
					dataType: "json",
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							document.location.reload();
						} else {
							alert("Unable to remove list. May be in use.");
						}
					}
				});
			}
		},
		
		campaign_remove: function(id)	{
			var cnfrm = confirm("Are you sure you want to remove this campaign?");
			
			if (cnfrm)	{
				$.ajax({
					url: "/communication/campaign_remove/" + id,
					dataType: "json",
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							document.location.reload();
						} else {
							alert("Unable to remove campaign. May be in use.");
						}
					}
				});
			}
		},
		
		campaign_seedtest: function(id)	{
			var cnfrm = confirm("Are you sure you want to send email to test seeds?");
			
			if (cnfrm)	{
				$.ajax({
					url: "/communication/campaign_seedtest/" + id,
					dataType: "json",
					success: function(msg)	{
						alert(msg.message);
					}
				});
			}
		},
		
		zipSearch: function()	{
			$("#resultTR").hide();
			$("#listData").html("");
			$("#spinnerTR").show();
			
			$.ajax({
				url: "/lists/search",
				type: "POST",
				dataType: "json",
				data: { search_list: $("#search_list").val(), search_vertical: $("#search_vertical").val(), search_range: $("#search_range").val(), search_zip: $("#search_zip").val(), search_max_count: $("#search_max_count").val() },
				success: function(msg)	{
					if (msg.status == "SUCCESS")	{
						$("#listData").html("");
						
						$("#listType").val(msg.list_type);
						
						$.each(msg.data, function()	{
							//alert(this[0].postalcode)
							$("#listData").append("<tr><td><input type='checkbox' class='recorddata' name='recorddata[]' value='" + this[0].postalcode + "'> " + this[0].cnt + "</td><td>" + this[0].name + "</td></tr>");
						});

						$("#spinnerTR").hide();
						$("#resultTR").show();
					} else {
						$("#listData").html("<tr><td colspan='3'>- No Matching Data -</td></tr>").show();
					}
				}
			});
		},
		
		help_create: function()	{
			var validate = $("#corp_help_create_form").validate().form();
			
			if (validate)	{
				$.ajax({
					url: "/help/create",
					type: "POST",
					dataType: "json",
					data: { subject: $("#corp_help_create_subject").val(), content: $("#corp_help_create_content").val() },
					success: function(msg){
						if (msg.status == "SUCCESS")	{
							document.location.reload();
						}
					}
				});
			}
		},
		
		help_topic_remove: function(id)	{
			if (id > 0)	{
				$.ajax({
					url: "/help/remove/" + id,
					dataType: "json",
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							document.location.reload();
						}
					}
				});
			}	
		},
		
		help_topic_load: function(id)	{
			$.ajax({
				url: "/help/help_topic_load/" + id,
				dataType: "json",
				success: function(msg)	{
					if (msg.status == "SUCCESS")	{
						$("#corp_help_update_subject").html(msg.message.help_topic_heading);
						$("#corp_help_update_content").html(msg.message.help_topic_content);
						$("#corp_help_update_id").html(msg.message.id);
					}
				}
			});
		},
		
		help_update: function()	{
			var validate = $("#corp_help_update_form").validate().form();
			
			if (validate)	{
				$.ajax({
					url: "/help/update",
					type: "POST",
					dataType: "json",
					data: { id: $("#corp_help_update_id").val(), subject: $("#corp_help_update_subject").val(), content: $("#corp_help_update_content").val() },
					success: function(msg){
						if (msg.status == "SUCCESS")	{
							document.location.reload();
						}
					}
				});
			}			
		},
		
		user_create: function()	{
			var validate = $("#user_create_form").validate().form();
			
			if (validate)	{
				is_reports = ($("#user_acl_reports").is(':checked')) ? "Y" : "N";
				is_campaigns = ($("#user_acl_campaigns").is(':checked')) ? "Y" : "N";				
				is_admin = ($("#user_acl_admin").is(':checked')) ? "Y" : "N";

				$.ajax({
					url: "/corporate/user_create",
					type: "POST",
					dataType: "json",
					data: { name: $("#user_name").val(), username: $("#user_email").val(), password: $("#user_password").val(), 'is_reports': is_reports, 'is_campaigns': is_campaigns, 'is_admin': is_admin, is_active: "Y" },
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							document.location.reload();
						}
					}
				});
			}
		},
		
		user_remove: function(id)	{
			$.ajax({
				url: "/corporate/user_remove/" + id,
				dataType: "json",
				success: function(msg)	{
					if (msg.status == "SUCCESS")	{
						document.location.reload();
					}
				}
			});
		},
		
		campaign_list_create: function()	{
			$.ajax({
				url: "/lists/campaign_list_create",
				type: "POST",
				dataType: "json",
				data: { list_name: $("#search_dataset").val(), list_type: $("#listType").val(), sublists: $(".recorddata:checked").map(function(i,n){ return $(n).val(); }).get() },
				success: function(msg)	{
					if (msg.status == "SUCCESS")	{
						//document.location.href='/lists/view/C';
					}
				}
			});
		},
		
		campaign_approve: function(id)	{
			var cnfrm = confirm("Are you sure you want to approve this campaign for mailing?\nDoing so will schedule for the deploy date and record this action.");
			if (cnfrm)	{
				$.ajax({
					url: "/communication/approve_campaign/" + id,
					dataType: "json",
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							document.location.href="/communication/queue/Y";
						}
					}
				});
			} else	{
				return false;
			}
		},
		
		campaign_disapprove: function(id)	{
			var cnfrm = confirm("Are you sure you want to disapprove this campaign for mailing?\nDoing so will NOT send this campaign.");
			if (cnfrm)	{
				$.ajax({
					url: "/communication/disapprove_campaign/" + id,
					dataType: "json",
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							document.location.href="/communication/queue/N";
						}
					}
				});
			} else	{
				return false;
			}
		},
		
		
		bof_reload_content: function(id)	{
			var cnfrm = confirm("Are you sure you want to reload the content of this message from source URL?");
			if (cnfrm)	{
				$.ajax({
					url: "/communication/bof_reload_content/" + id,
					dataType: "json",
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							alert("Updated content");
						}
					}
				});
			}
		},
		
		smtpserver_create: function()	{
			var validator = $("#smtpserver_create_form").validate().form();
			
			if (validator)	{
				$.ajax({
					url: "/corporate/smtpserver_create",
					type: "POST",
					dataType: "json",
					data: { hostname: $("#corp_smtpserver_create_hostname").val(), risk: $("#corp_smtpserver_create_risk").val(), maxdaily: $("#corp_smtpserver_create_maxdaily").val() },
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							document.location.reload();
						} else {
							$("#corp_smtpserver_create_error_message").html(msg.message);
							$("#corp_smtpserver_create_error").show();
						}
					}
				});
			}
		},
		
		smtpserver_inactive: function(id)	{
			$.ajax({
				url: "/corporate/smtpserver_inactive/" + id,
				dataType: "json",
				success: function(msg)	{
					if (msg.status == "SUCCESS")	{
						document.location.reload();
					}
				}
			});
		}
		
	};

	$.massmail = function( method ) {
		 if ( methods[method] ) {
			 return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		 } else if ( typeof method === 'object' || ! method ) {
			 return methods.init.apply( this, arguments );
		 } else {
			 $.error( 'Method ' +  method + ' does not exist on massmail plugin');
		 }
	};
})( jQuery );

$.massmail();

function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }

    var pre = document.createElement('pre');
    pre.innerHTML = out;
    document.body.appendChild(pre)
}
