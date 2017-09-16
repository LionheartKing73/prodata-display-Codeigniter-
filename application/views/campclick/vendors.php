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
        	<h2>Vendor Manager &nbsp;&nbsp;<small>Manage vendors available for campaign assignment</small></h2>
        	<br/>
	        <form class="" name="create_form" id="create_form">
        		<table class="table table-striped table-bordered" id="content_table">
        			<tr>
        				<td>Vendor Name*:</td>
        				<td><input type="text" name="name" id="name" value="" class="input-large required" /></td>
        			</tr>
        			<tr>
        				<td>Vendor Email*:</td>
        				<td><input type="text" name="email" id="email" value="" class="input-large required" /></td>
        			</tr>
        			<tr>
        				<td colspan="2">
        					<span class="btn btn-success pull-right vendor_create">Continue</span>
        				</td>
        			</tr>
        		</table>
	        </form>
	        <hr>
	        
	        <table class="table table-striped table-bordered">
	        <thead>
	        	<tr>
	        		<th>Vendor Name</th>
	        		<th>Vendor Email</th>
	        		<th>Owner</th>
	        		<th>Manage</th>
	        	</tr>
	        </thead>
	        <tbody>
	        	{foreach from=$vendors item=d}
	        		<tr>
	        			<td>{$d.name}</td>
	        			<td>{$d.email}</td>
	        			<td>{$d.user_id}</td>
	        			<td>
	        				<a href="#" class='pull-right vendor_delete' data-id='{$d.id}'><i class="icon-trash"></i></a>
	        			</td>
	        		</tr>
	        	{/foreach}
	        </tbody>
	        </table>
        </div>
      </div>

<script>
	$(document).ready(function(){
		$(".admin-vendors").addClass("active");

		$(".vendor_create").click(function(){
			var verify = $("#create_form").validate().form();

			if (verify)	{
				$.ajax({
					url: "/campclick/vendor_create",
					type: "POST",
					dataType: "json",
					data: { name: $("#name").val(), email: $("#email").val() },
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							document.location.reload(true);
						}
					}
				});
			}
		});
		
		$(".vendor_delete").click(function(){
			var cnfrm = confirm("Are you sure? This cannot be undone!");

			if (cnfrm)	{
				$.ajax({
					url: "/campclick/vendor_delete",
					type: "POST",
					dataType: "json",
					data: { id: $(this).data("id") },
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
