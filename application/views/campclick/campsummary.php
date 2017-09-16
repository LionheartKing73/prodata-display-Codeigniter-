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
        	<h2>Campaign Summary</h2>
        	<table class="table table-bordered table-striped" id="mytable">
        	<thead>
        		<tr>
        			<th>I/O #</th>
        			<th>Campaign Name</th>
        			<th>Total / Unique / Fraud</th>
        			<th>Date Created</th>
        			<th>&nbsp;</th>
        		</tr>
        	</thead>
        	<tbody>
        		{foreach from=$campaigns item=c}
	        		<tr id="io_{$c.io}">
	        			<td>{$c.io}</td>
	        			<td>{$c.name}</td>
	        			<td>{$c.total_clicks} / {$c.unique_clicks} / {$c.fraud_clicks}</td>
	        			<td>{$c.create_date|date_format:"%Y-%m-%d"}</td>
	        			<td><span class="pull-right"><a href="{$base_url}campclick/report/{$c.io}"><i class="icon-eye-open"></i></a> &nbsp;|&nbsp; <a href="{$base_url}campclick/map/{$c.io}"><i class="icon-globe"></i></a> &nbsp;|&nbsp; <a href='#myModal' data-io='{$c.io}' class='show-modal'><i class='icon-envelope'></i></a></span></td>
	        		</tr>
        		{/foreach}
        	</tbody>
        	</table>
        </div>
      </div>

	<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
			<h3 id="myModalLabel">Campaign IO#: <span id='modal_io'>-</span></h3>
			<input type="hidden" name="frm_io" id="frm_io" value="" />
			<div class="alert alert-error" style="display:none;">Support / Inquiry notes are required.</div>
			<div class="alert alert-success" style="display:none;">Support has been contacted.  Please allow up to 1-business day for response.</div>
		</div>
		<div class="modal-body">
			<h6>Note any questions, concerns or traffic requirements in notes below.</h6>
			<textarea class="input-xlarge required" style="width:95%;" id="frm_notes"></textarea>
		</div>
		<div class="modal-footer">
			<h6>By submitting this form, you will be sending a message to support.</h6>
			<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
			<button class="btn btn-primary save" id="modal-send-btn">Send Request</button>
		</div>
	</div>

      <script>
      	$(document).ready(function(){
			$(".campaign-summary").addClass("active");

			$("#mytable").tablesorter();

			$(".show-modal").click(function(){
				$(".alert-error").hide();
				$(".alert-success").hide();
				$("#modal-send-btn").show();

				var io = $(this).data("io");
				$("#frm_io").val(io);

				$("#modal_io").html(io);
				$("#myModal").modal();
			});

			$(".save").click(function(){
				$(".alert-error").hide();
				$(".alert-success").hide();
				$("#modal-send-btn").show();

				if ($("#frm_io").val() == "" || $("#frm_notes").val() == "")	{
					$(".alert-error").show();
				}

				$.ajax({
					url: "/campclick/support_request",
					dataType: "json",
					type: "POST",
					data: { "io": $("#frm_io").val(), "notes": $("#frm_notes").val() },
					success: function(msg)	{
						if (msg.status == "SUCCESS")	{
							$(".alert-success").show();
							$("#modal-send-btn").hide();
						} else {
							$(".alert-error").show();
						}
					}
				});
			});
       	});
      </script>

{include file="campclick/sections/footer.php"}
