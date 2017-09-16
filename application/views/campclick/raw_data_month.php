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
          <div class="row" >
           <div class="span9"> &nbsp;
           </div>
            <div class="span3" style="text-align: right">    
                
                
           </div>
        </div>
      <div class="row">
        <div class="span2">
        	<h2>LINK #: {$counter}</h2>
        	<br/>
        	<h6>Campaign Report</h6>
        	<br/>
        </div>
        <div class="span10">
 		<div class="row">
        	<div class="span7"><div class="btn-group" style="margin-bottom: 20px; margin-left: 10px">
                    <a class="btn " href="{$base_url}campclick/export_raw_data_month/{$io}/{$counter}">Export Raw Data</a>                     
                    
<!--                    <a class="btn" href="{$base_url}campclick/moreinfo_last_month/{$io}/{$counter}">Last 30 days</a>
                    <a  id="dt-range-selector" class="btn">Date Range</a>-->
                </div></div>
            <div class="span3">
            <div class="btn-group" style="margin-bottom: 20px; margin-left: 10px">
                    <a class="btn " href="{$base_url}campclick/raw_data/{$io}/{$counter}">24 Hours</a>
                    <a class="btn btn-inverse" href="{$base_url}campclick/raw_data_month/{$io}/{$counter}">Last 30 days</a>
                    <a  id="dt-range-selector" class="btn">Date Range</a>
       </div>
       <div id="date-selection-form" style="display: none; margin:15px 0">
                	<form name="date-select" id="date-select" action="{$base_url}campclick/moreinfo_last_month/{$io}/{$counter}" method="post">
                    	 <input type="text" size="25" name="sDate" id="startDate" value="Start Date" onblur="if(this.value=='') this.value='Start Date'" onfocus="if(this.value=='Start Date') this.value= ''" />
                        <input type="text" size="25" name="eDate" id="endDate" value="End Date" onblur="if(this.value=='') this.value='End Date'" onfocus="if(this.value=='End Date') this.value= ''"  />
                        <input type="hidden" name="action_url" id="action_url" value="{$base_url}campclick/raw_data_date_range/{$io}/{$counter}" />
                        <input type="submit" name="submit" value="Filter" />
                        </form>
                </div></div>
        </div>
                                  
        	<table id="all-data" class="table table-bordered table-striped">
        	<thead>
        		<tr>
        			<th>IP Address</th>
        			<th>Date/Time</th>
                    <th>Referer</th>
                    <th>Browser</th>
                    <th>Platform</th>
        		</tr>
        	</thead>
        	<tbody>
        		{foreach from=$all_data key=bn item=b}
	        		<tr>
	        			<td>{$b.ip_address}</td>
	        			<td>{$b.timestamp}</td>
                        <td style=" word-wrap: break-word "><span style=" display:block; max-width:300px;word-wrap: break-word ">{* {$b.referrer} *}-</span></td>
                        <td>{if $b.web_browser|contains:"Internet" > 0}<img src="{$base_url}/static/img/browser_ie.png" />{/if}
 							{if $b.web_browser|contains:"Chrome" > 0}<img src="{$base_url}/static/img/browser_chrome.png" />{/if}
                        {if $b.web_browser|contains:"Opera" > 0}<img src="{$base_url}/static/img/browser_opera.png" />{/if}
                        {if $b.web_browser|contains:"Safari" > 0}<img src="{$base_url}/static/img/browser_safari.png" />{/if}
                        {if $b.web_browser|contains:"Firefox" > 0}<img src="{$base_url}/static/img/browser_firefox.png" />{/if}
                      </td>
                        <td>{if $b.platform|contains:"Windows" > 0}<img src="{$base_url}/static/img/platform_windows.png" />{/if} 							
                        {if $b.platform|contains:"Linux" > 0}<img src="{$base_url}/static/img/platform_linux.png" />{/if}
                        {if $b.platform|contains:"FreeBSD" > 0}<img src="{$base_url}/static/img/platform_windows.png" />{/if}
                        {if $b.platform|contains:"Solaris" > 0}<img src="{$base_url}/static/img/platform_windows.png" />{/if}
                        {if $b.platform|contains:"Mac" > 0}<img src="{$base_url}/static/img/platform_apple.png" />{/if}
                      </td>                        
	        		</tr>
        		{/foreach}

        	</tbody>
        	</table> 
			{$pagination_link}             
        </div>
      </div>

{include file="campclick/sections/footer-rawdata.php"}
