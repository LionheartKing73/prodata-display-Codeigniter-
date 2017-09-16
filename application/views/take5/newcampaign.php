{include file="campclick/sections/header.php"}

<script src="/static/js/heatmap.min.js"></script>

<style>
    .click_border { border: 5px solid #64FE2E !important; }
</style>

    <div class="container">

      <!-- Example row of columns -->
      <div class="row">
        <div class="span12">
            <h2>New Campaign Wizard &nbsp;&nbsp;<small>Create new Pay-Per-Click Campaign.</small></h2>
            <hr>
        </div>

        <ul class="nav nav-tabs" id="myTab">
            <li><a href="#message" data-toggle="tab">Creative <span class="badge badge-important" id="badge-creative">0</span></a></li>
            <li><a href="#heatmap" data-toggle="tab">Heatmap <span class="badge badge-important" id="badge-heatmap">0</span></a></li>
            <li><a href="#createorder" data-toggle="tab">Create Order <span class="badge badge-important" id="badge-create-order">0</span></a></li>
            <li><a href="#geolocation" data-toggle="tab">Geo-Location <span class="badge badge-important" id="badge-geo-location">0</span></a></li>
            {if $is_take5_user != "Y"}
                <li><a href="#emailseeds" data-toggle="tab">Email Seeds <span class="badge badge-important" id="badge-email-seeds">0</span></a></li>
            {/if}
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="message">
                <textarea name="message" id="message_result" rows="30" cols="60" class="span12">{$campaign.message}</textarea>
                <br/>
                Paste your HTML content above, then click "Next" button.
                <span class="pull-right btn btn-success" id="trigger-message">Next &gt;&gt;</span>
            </div>

            <div class="tab-pane span12" id="heatmap">
                <div class="tab-pane" id="heatmap_creative">
                    <iframe style="margin:0; padding:0; border:1; width:99%; height:625px; position:relative; overflow-x:hidden; overflow-y:scroll;" id="heatmap_creative_iframe"></iframe>
                </div>
                <br/>
                <div id="link_div" style="display:none;">
                    <hr/>

                    <div class="row">
                        <span class="span2">
                            Total Records: <input type="text" class="input-small master-properties required" id="total_records" name="total_records" placeholder="" />
                        </span>

                        <span class="span1">
                            % Opens: <input type="text" class="input-mini master-properties required" id="percentage_opens" name="percentage_opens" placeholder="" value="10"/>
                        </span>

                        <span class="span1">
                            % Clicks: <input type="text" class="input-mini master-properties required" id="percentage_clicks" name="percentage_clicks" placeholder="" value="2"/>
                        </span>

                        <span class="span2">
                            % Bounce:<br/><input type="text" class="input-mini master-properties required" id="percentage_bounce" name="percentage_bounce" placeholder="" value="0.01"/>
                        </span>

                        <span class="span2">
                            Total Clicks:
                            <input type="text" class="input-small total-click-update required" id="total_clicks" name="total_clicks" placeholder="" value="0"/>
                        </span>

                        <span class="span2">
                            Total Opens:
                            <input type="text" class="input-small required" id="total_opens" name="total_opens" placeholder="" value="0"/>
                        </span>

                        <span class="span2">
                            Total Bounce:
                            <input type="text" class="input-small required" id="total_bounces" name="total_bounces" placeholder="" value="0"/>
                        </span>

                    </div>

                    <hr/>

                    <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th align="right">Totals: </th>
                        <th><span class="user_clicks">0</span></th>
                        <th><span class="user_percentage" id="user_percentage_set">0</span>%</th>
                    </tr>
                    <tr>
                        <th>Destination URL</th>
                            <th>Click Count</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody id="heatmap_links"></tbody>
                    <tfooter>
                        <tr>
                            <th align="right">Totals: </th>
                            <th><span class="user_clicks">0</span></th>
                            <th><span class="user_percentage">0</span>%</th>
                        </tr>
                    </tfooter>
                    </table>

                    <div>
                        <span class="pull-left btn btn-info" id="trigger-csv-export">Export CSV</span>
                        <span class="pull-right btn btn-success" id="trigger-create-order">Next &gt;&gt;</span>
                    </div>
                </div>
            </div>

            <div class="tab-pane span12" id="createorder">
        		<table class="table table-striped table-bordered" id="content_table">
        			<tr id="tr_io">
        				<td>I/O #*:</td>
        				<td><input type="text" name="io" id="io" value="{$campaign.io_number}" class="input-small required" maxlength="16" /><div id="duplicate_io_alert" class="alert alert-error" style="display:none;"><b>DUPLICATE IO#</b> Cannot be the same! Try appending an A, B, C, etc.</div><span class='pull-right'>IO must be unique for each campaign (append an 'A', 'B', etc if needed).<br/>MAX 16 Characters.</span></td>
        			</tr>
        		    <tr>
        				<td>Campaign Name*:</td>
        				<td><input type="text" name="create_name" id="create_name" value="{$campaign.name}" class="input-large required" /></td>
        			</tr>
        			<tr>
        			    <td>Campaign Vertical*:</td>
        			    <td>
        			         <select name="vertical" id="vertical" class="input-xlarge required">
        			             <option value="">Select Vertical</option>
        			             <option value="adult">Adult (18+ Mature Audience)</option>
        			             <option value="automotive">Automotive</option>
         			             <option value="autointender">Auto Intenders</option>
         			             <option value="autoowner">Auto Owners</option>
        			             <option value="beauty">Beauty</option>
        			             <option value="business">Business</option>
        			             <option value="bigbox">Big-Box Store</option>
         			             <option value="b2b">Business-to-Business</option>
         			             <option value="consumer">Consumer</option>
          			             <option value="coupons">Coupons</option>
        			             <option value="education">Education</option>
        			             <option value="entertainment">Entertainment</option>
        			             <option value="family">Family</option>
        			             <option value="finance">Finance</option>
        			             <option value="fitness">Fitness</option>
        			             <option value="food">Food</option>
        			             <option value="gender">Gender</option>
        			             <option value="health">Health</option>
        			             <option value="home">Home & Garden</option>
        			             <option value="law">Law</option>
        			             <option value="medical">Medical</option>
        			             <option value="music">Music</option>
        			             <option value="parents">Parents</option>
          			             <option value="pets">Pets</option>
        			             <option value="sports">Sports</option>
        			             <option value="toys">Toys</option>
          			             <option value="travel">Travel/Tourism</option>
          			             <option value="misc">Other</option>
     			             </select>
        			    </td>
        			</tr>
        			<tr>
        				<td>Vendor?</td>
        				<td>
        					<select name="vendor" id="vendor">
        						{foreach from=$vendor item=v}
        							<option value='{$v.id}'>{$v.name}</option>
        						{/foreach}
        					</select>
        				</td>
        			</tr>
        			<tr>
        				<td>Domain Name?</td>
        				<td>
        					<select name="domain" id="domain">
        					{foreach from=$domain item=d}
        						<option value='{$d.id}'>{$d.name}</option>
        					{/foreach}
        					</select>
        				</td>
        			</tr>
        			<tr>
        				<td>Requested Campaign<Br/>Start Date*:</td>
        				<td>
							<div class="input-group">
								<input id="campaign_start_datetime" type="text" value="{$next_date}" class="form-control required">
							    <span class="input-group-btn">
							    	<button id="image_button" class="btn btn-default" type="button"><span class="icon-calendar"></span></button>
								</span>
							</div><!-- /input-group -->
        				</td>
        			</tr>
        			<tr>
        			     <td>Has Open Pixel?</td>
        			     <td>
                            <label class="radio inline">
                                <input type="radio" name="open_pixel" value="Y" /> Yes
                            </label>

                            <label class="radio inline">
                                <input type="radio" name="open_pixel" value="N" checked="checked" /> No
                            </label>

                            <div id="open_pixel_layer" style="display:none;">
                                <input type="text" class="input-xlarge open_pixel_src" name="open_pixel_src[1]" placeholder="Enter Pixel Image HTML" /><br/>
                                <input type="text" class="input-xlarge open_pixel_src" name="open_pixel_src[2]" placeholder="Enter Pixel Image HTML" /><br/>
                                <input type="text" class="input-xlarge open_pixel_src" name="open_pixel_src[3]" placeholder="Enter Pixel Image HTML" /><br/>
                                <input type="text" class="input-xlarge open_pixel_src" name="open_pixel_src[4]" placeholder="Enter Pixel Image HTML" /><br/>
                                <input type="text" class="input-xlarge open_pixel_src" name="open_pixel_src[5]" placeholder="Enter Pixel Image HTML" /><br/>
                                <input type="text" class="input-xlarge open_pixel_src" name="open_pixel_src[6]" placeholder="Enter Pixel Image HTML" /><br/>
                                <input type="text" class="input-xlarge open_pixel_src" name="open_pixel_src[7]" placeholder="Enter Pixel Image HTML" /><br/>
                                <input type="text" class="input-xlarge open_pixel_src" name="open_pixel_src[8]" placeholder="Enter Pixel Image HTML" /><br/>
                                <input type="text" class="input-xlarge open_pixel_src" name="open_pixel_src[9]" placeholder="Enter Pixel Image HTML" /><br/>
                                <input type="text" class="input-xlarge open_pixel_src" name="open_pixel_src[10]" placeholder="Enter Pixel Image HTML" /><br/>
                            </div>
        			     </td>
        			</tr>

        			{if $is_take5_user != "Y"}
        			<tr>
        			     <td>Budget:</td>
        			     <td><input type="text" class="input-medium" name="budget" id="budget" value="0.00"></td>
        			</tr>
        			{/if}

        			<tr>
        			     <td colspan="2">
        			         Special Instructions<br/>
                             <textarea name="special_instructions" id="special_instructions" style="width:95%; height:100px;" class="input-xlarge"></textarea>
    			         </td>
        			</tr>
        		</table>

                <span class="pull-right btn btn-danger" id="trigger-create-order2">Next &gt;&gt;</span>

            </div>

            <div class="tab-pane span12" id="geolocation">
                <h4>Select Geo-Location Type</h4>

                <label class="radio inline">
                    <input type="radio" name="geotype" value="country" /> Country (Nationwide)
                </label>

                <label class="radio inline">
                    <input type="radio" name="geotype" value="state" /> State
                </label>

                <label class="radio inline">
                    <input type="radio" name="geotype" value="postalcode" /> Postal Code
                </label>

                <hr />

                <div class="accordion" id="geo-accordion">
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#geo-accordion" href="#geo-nationwide" id="geo-link-country">Country (Nationwide)</a>
                        </div>
                        <div id="geo-nationwide" class="accordion-body collapse">
                            <select name="geo-input-country" id="geo-input-country" size="1">
                                <option value=''>Select Country</option>
                                <option value='US'>United States</option>
                                <option value='CA'>Canada</option>
                                <option value='UK'>United Kingdom</option>
                            </select>
                        </div>
                    </div>

                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#geo-accordion" href="#geo-state" id="geo-link-state">State</a>
                        </div>
                        <div id="geo-state" class="accordion-body collapse">
                            <select name="geo-input-state" id="geo-input-state" size="5" multiple="multiple">
                                <option value=''>Select State</option>
                            </select>
                        </div>
                    </div>

                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#geo-accordion" href="#geo-postal-code" id="geo-link-postalcode">Postal Code</a>
                        </div>
                        <div id="geo-postal-code" class="accordion-body collapse">
                            <input type="text" class="input-xlarge" placeholder="Enter SPACE separated list of Zip Codes (MAX 25)" id="geo-input-postalcode" />
                            <select id="geo-input-postalcode-radius">
                                <option value=''>Select Radius</option>
                               {* <option value='1'>1 Mile</option>
                                <option value='3'>3 Miles</option>
                                <option value='5'>5 Miles</option> *}
                                <option value='10'>10 Miles</option>
                                <option value='15'>15 Miles</option>
                                <option value='25'>25 Miles</option>
                                <option value='50'>50 Miles</option>
                                <option value='75'>75 Miles</option>
                                <option value='100'>100 Miles</option>
                                <option value='125'>125 Miles</option>
                            </select>
                        </div>
                    </div>
                </div>

                <h6 class="pull-right">
                    Verify your setup is correct and then click button.
                </h6>
                <br/><br/>
                {if $is_take5_user != "Y"}
                    <span class="pull-right btn btn-success" id="trigger-create-order4">Next &gt;&gt;</span>
                {else}
                    <span class="pull-right btn btn-danger" id="trigger-create-order3">Save Pending Order &gt;&gt;</span>
                {/if}
            </div>

            <div class="tab-pane span12" id="emailseeds">
                <h4>Email Seeds (Optional)</h4>
                <h6>Please enter one email address per line below in the text box.  Email seeds will be automatically sent the creative when the campaign is approved and goes "live."</h6>

                <div>
                    <textarea class="span12" name="emailseeds_data" rows="15" cols="60" id="emailseeds_data"></textarea>
                </div>

                <h6 class="pull-right">
                    Verify your setup is correct and then click button.
                </h6>
                <br/><br/>
                <span class="pull-right btn btn-danger" id="trigger-create-order3">Save Pending Order &gt;&gt;</span>
            </div>
        </div>
      </div>
    </div>

    <script src="/static/js/clickmap_iframe.js"></script>

{include file="campclick/sections/footer.php"}
