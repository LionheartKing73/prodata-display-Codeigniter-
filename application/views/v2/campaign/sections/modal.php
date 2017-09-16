<!-- Clone Modal -->
<div id="clone-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
    <h3 id="myModalLabel">Clone Campaign - <span id="clone-io">-</span></h3>
  </div>
  <div class="modal-body">
    <p>
        <div id="duplicate_io_alert" class="alert alert-error" style="display:none;"><b>DUPLICATE IO#</b> Cannot be the same! Try appending an A, B, C, etc.</div>
        <div id="missing_io_name_alert" class="alert alert-error" style="display:none;"><b>Campaign Name Required</b> A campaign name is required (make it simple & informative)</div>
        <div id="missing_io_alert" class="alert alert-error" style="display:none;"><b>New IO Value Required</b> The IO number is required.</div>

        You are about to CLONE (copy) an existing campaign including links, opens, clicks, etc.  These can be edited after the cloning process, if needed.
        <br/><br/>
        Please enter the new IO number and campaign name below:
        <br/><br/>
        <input type="text" class="input-medium" name="clone-io-new" id="clone-io-new" value="" placeholder="New Unique IO" /> <br/>
        <input type="text" class="input-medium" name="clone-io-name" id="clone-io-name" value="" placeholder="New Campaign Name" />
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button class="btn btn-primary clone-io-save" id="clone-io-save">Save changes</button>
  </div>
</div>

<!-- Create New Link -->
<div id="myModal_createLink" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
		<h3 id="myModalLabel">Create New Link for IO: {$io}</h3>
	</div>
	<div class="modal-body">
		<div id="myModal_createLink_error" class="alert alert-error" style="display:none;"></div>
		<div id="myModal_createLink_success" class="alert alert-success" style="display:none;"></div>

		<table class="table table-bordered table-striped" id="tbl_create">
			<tr>
				<td>Destination URL:</td>
				<td><input type="text" class="input-xlarge" name="create_dest_url" id="create_dest_url" placeholder="http://"></td>
			</tr>
			<tr>
				<td>Maximum Clicks:</td>
				<td><input type="text" class="input-medium" name="create_max_clicks" id="create_max_clicks" placeholder="9999999" value="9999999"></td>
			</tr>
		</table>
	</div>
	<input type="hidden" name="create_io" id="create_io" value="{$io}" />
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		<button class="btn btn-primary create-link" id="modal-create-link-btn">Create Link</button>
	</div>
</div>


<!-- Edit Link -->
<div id="myModal_editLink" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalEditLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
		<h3 id="myModalEditLabel">Edit Link for IO: {$io}</h3>
	</div>
	<div class="modal-body">
		<div id="myModal_createLink_error" class="alert alert-error" style="display:none;"></div>
		<div id="myModal_createLink_success" class="alert alert-success" style="display:none;"></div>

		<table class="table table-bordered table-striped" id="tbl_create">
			<tr>
				<td>Destination URL:</td>
				<td><input type="text" class="input-xlarge" name="edit_dest_url" id="edit_dest_url" placeholder="http://"></td>
			</tr>
			<tr>
				<td>Maximum Clicks:</td>
				<td><input type="text" class="input-medium" name="edit_max_clicks" id="edit_max_clicks" placeholder="9999999" value="9999999"></td>
			</tr>
			<tr>
			    <td>Fulfilled?</td>
			    <td><select name="edit_fulfilled" id="edit_fulfilled" class="input-medium"><option value="N">No</option><option value="Y">Yes</option></select>
			</tr>
		</table>
	</div>
	   <input type="hidden" name="edit_link_id" id="edit_link_id" value="" />
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		<button class="btn btn-primary update-link" id="modal-update-link-btn">Update Link</button>
	</div>
</div>



<!--  View Message Content -->
<div id="myModal_messageContent" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
		<h3 id="myModalLabel">Message Content for IO: {$io}</h3>
	</div>
	<div class="modal-body">
		<div class="alert alert-error" style="display:none;"></div>
		<div class="alert alert-success" style="display:none;"></div>

		<textarea name="message" id="message_result" rows="15" cols="60" style="width:98%;"></textarea>

	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
	</div>
</div>

