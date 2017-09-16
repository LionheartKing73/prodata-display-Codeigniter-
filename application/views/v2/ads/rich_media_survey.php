<script src="http://reporting.prodata.media/v2/js/jquery-2.0.3.min.js"></script>

<style>
#ProDataMedia_adContainer {
	font-family: "Trebuchet MS", "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", Tahoma, sans-serif;
	font-size: 12px;
}

.ProDataMedia_Btn {
    background-color: #4CAF50; /* Green */
    border: none;
    color: white;
    padding: 10px 24px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
	border-radius: 8px;
	-webkit-transition-duration: 0.4s; /* Safari */
    transition-duration: 0.4s;
	box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
}

.ProDataMedia_Btn:hover {
    background-color: #FFFFFF;
	color: #4CAF50;
	box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24), 0 17px 50px 0 rgba(0,0,0,0.19);
}

.ProDataMedia_Btn:active {
  background-color: #3e8e41;
  box-shadow: 0 5px #666;
  transform: translateY(4px);
}

.ProDataMedia_BotLft {
	position:absolute;
	left:3;
	bottom:0;
}

.ProDataMedia_TopRght {
	position:absolute;
	top:1%;
	right:7%;
	width:20px;
	height:20px;
}

.clear {
	clear: both;
}

.ProData_Answer {
	display: block;
	width: 175px;
}

.ProDataMedia_Percent_Border {
	height: 15px;
	position: relative;
	background: #555;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	padding: 2px;
	box-shadow: inset 0 -1px 1px rgba(255,255,255,0.3);
	width: 125px;
	display: inline-block;
	white-space: nowrap;
	float:right;
}

.ProDataMedia_Percent_Border > span {
	display: block;
	height: 100%;
    border-top-right-radius: 3px;
    border-bottom-right-radius: 3px;
    border-top-left-radius: 3px;
    border-bottom-left-radius: 3px;
    background-color: rgb(43,194,83);
    background-image: linear-gradient(
        center bottom,
        rgb(43,194,83) 37%,
        rgb(84,240,84) 69%
    );
    box-shadow: 
        inset 0 2px 9px  rgba(255,255,255,0.3),
        inset 0 -2px 6px rgba(0,0,0,0.4);
    position: relative;
    overflow: hidden;
	text-align: center;
}

</style>

<div id="ProDataMedia_adContainer" style="width:300px; height:250px; margin: 10px auto; padding:0px; background-color:#ffffff;">
	<div style="width:298px; height:248px; margin:auto; position:relative; top:0px; left:0px; background-color:#ffffff; border-style:solid; border-width:1px; border-color:rgb(169,169,169);">
		<img style="position:relative; width:298px; height:248px;" src="http://reporting.prodata.media/{$ad.creative_url}">
		
		<input type="hidden" name="ProDataMedia_campaign_id" id="ProDataMedia_campaign_id" value="{$ad.campaign_id}" />
		
		<div id="ProDataMedia_Survey" style="position:absolute; top:25%; left:10;">
    		<div style="width:280px; margin-left:8%; margin-bottom: 0px; font-weight:900;">{$ad.question}</div>
    		<br/>
    		
    		{foreach from=$ad.answer key=k item=a}
    			{if $a != ""}
		        	<div style="width:280px ; margin : 0 auto"><label><input type="radio" name="ProDataMedia_radio" value="{$k}" id="ProDataMedia_{$k}" style="display:inline ; margin: 5px 0 0 5px" class='ProDataMedia_SaveBtn'> {$a}</label></div>
		        	<br/>
	        	{/if}
    		{/foreach}
        	
        	<div class="">
        		<div id="ProDataMedia_Content" style="position:absolute; left:2px; font-family: Arial, Helvetica, sans-serif; font-size:10px; width:60%;  color:rgb(129,129,129);">ProData Media asks for your participation in this brief one question survey. Results will be displayed after saving.</div>
	        	<div style="position:absolute; right:10;" class="ProDataMedia_Btn ProDataMedia_SaveBtn" >Save</div>
        	</div>
		</div>
		
		<!-- show this only after survey saved. -->
		<div id="ProDataMedia_Results" style="position:absolute; top:25%; left:10; display:none;">
			<div style="width:280px; margin-left:8%; margin-bottom: 0px; font-weight:900;">{$ad.question}</div>
    		<br/>
    		<div id="ProDataMedia_Answers"></div>
    		<br/>
    		<div class="ProDataMedia_Btn" id="ProDataMedia_LearnMore_Btn">Learn More!</div>
		</div> 
		
		<div class="ProDataMedia_TopRght">
			<div style="text-align:center;vertical-align:middle;font-family: Arial, Helvetica, sans-serif; font-size:8px"><a href="http://prodata.media/opt-out.html?utm_source=ProDataNative&utm_medium=RICH_MEDIA_SURVEY&content=OPT_OUT&utm_campaign=PRODATA_ORIGIN" target="_blank" style="text-decoration:none; color:rgb(169,169,169);">AdChoices</a></div>
		</div>
		
		<div class="ProDataMedia_BotLft">
			<div style="text-align:center;vertical-align:middle;font-family: Arial, Helvetica, sans-serif; font-size:8px"><a href="http://prodata.media/privacy-policy.html?utm_source=ProDataNative&utm_medium=RICH_MEDIA_SURVEY&content=OPT_OUT&utm_campaign=PRODATA_ORIGIN" target="_blank" style="text-decoration:none; color:rgb(169,169,169);">How we use this data?</a></div>
		</div>
	</div>
	
</div>

<iframe src="http://trkpixel.com/pixelproxy/{$ad.campaign_id}" width=0 height=0 style="position:absolute; visibility:hidden;"></iframe>


<script>
$(document).ready(function(){
	$(".ProDataMedia_SaveBtn").click(function(){
		$.ajax({
			url: "http://reporting.prodata.media/v2/survey/save",
			type: "POST",
			dataType: "json",
			data: {
				campaign_id: $("#ProDataMedia_campaign_id").val(),
				selected_answer: $("input[name='ProDataMedia_radio']:checked").val(),
			},
			success: function(msg){
				$("#ProDataMedia_Survey").hide();

				$("#ProDataMedia_LearnMore_Btn").on('click', function(){
					document.location.href = msg.destination_url;
				});
				
				$.each(msg.data, function(idx, obj){
					if (obj.ad_answer) {

						var width_percent = Math.ceil((obj.count / msg.total_count) * 100);
						var snippet = "<div style='width:280px ; margin: 0 auto;'><label class='ProDataMedia_Answer'>" + obj.ad_answer + "</label><div class='ProDataMedia_Percent_Border'><span class='ProDataMedia_Percent' style='width:" + width_percent + "%;'>" + width_percent + "%</span></div></div><br/>";
						$("#ProDataMedia_Answers").append(snippet);
					}
				});

				$("#ProDataMedia_Results").show();
			}
		});
	});
});
</script>