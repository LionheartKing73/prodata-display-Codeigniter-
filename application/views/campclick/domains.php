{include file="campclick/sections/header.php"}

    <div class="container">

	    <div class="alert alert-error" id="err_bof" style="display:none;">
	    	<a class="close" data-dismiss="alert">X</a>
	    	<strong id="err_bof_message"></strong>
	    </div>

	    <div class="alert alert-success" id="success_bof" style="display:none;">
	    	<a class="close" data-dismiss="alert">X</a>
	    	<strong id="success_bof_message"></strong>
	    </div>

      <!-- Example row of columns -->
      <div class="row">
        <div class="span12">
        	<h2>Domain Name Manager &nbsp;&nbsp;<small>Manage domains available for HTML Message processing</small></h2>
        	<br/>
	        <form class="" name="create_form" id="create_form">
        		<table class="table table-striped table-bordered" id="content_table">
        			<tr>
        				<td>Domain Name*:</td>
        				<td><input type="text" name="domain" id="domain" value="" class="input-large required" /></td>
        			</tr>
        			<tr>
        				<td colspan="2">
        					<span><small>For the domain to "work", please point domain to IP: {$ipaddress}</small></span>
        					<span class="btn btn-success pull-right domain_create">Continue</span>
        				</td>
        			</tr>
        		</table>
	        </form>
	        <hr>
	        
	        <table class="table table-striped table-bordered">
	        <thead>
	        	<tr>
	        		<th>Domain Name</th>
	        		<th>Owner</th>
	        		<th>Manage</th>
	        	</tr>
	        </thead>
	        <tbody>
	        	{foreach from=$domains item=d}
	        		<tr>
	        			<td>{$d.name}</td>
	        			<td>{$d.user_id}</td>
	        			<td>
	        				{if $d.name != "report-site.com"}<a href="#" class='pull-right domain_delete' data-domain='{$d.id}'><i class="icon-trash"></i></a>{else}-{/if}
	        			</td>
	        		</tr>
	        	{/foreach}
	        </tbody>
	        </table>
        </div>
      </div>

<script>
	$(document).ready(function(){
		$(".admin-domains").addClass("active");

		$(".domain_create").click(function(){
			var verify = $("#create_form").validate().form();

			if (verify)	{
				$.ajax({
					url: "/campclick/domain_create",
					type: "POST",
					dataType: "json",
					data: { name: $("#domain").val() },
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							document.location.reload(true);
						}
					}
				});
			}
		});
		
		$(".domain_delete").click(function(){
			var cnfrm = confirm("Are you sure? This cannot be undone!");

			if (cnfrm)	{
				$.ajax({
					url: "/campclick/domain_delete",
					type: "POST",
					dataType: "json",
					data: { id: $(this).data("domain") },
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							document.location.reload(true);
						}
					}
				});
			}
		});
	});
</script>

{include file="campclick/sections/footer.php"}
