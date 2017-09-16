<style>
#ad_content {
  position: absolute;
  right: 0;
  top: 150px;
  width: 250px;
  padding: 0 30px 50px 15px;
  font-size: small;
  color: #FFFFFF;
}
 
.ad_sidebox {
    background: #B4B4B4 no-repeat left bottom;	
    -moz-border-radius: 4px 0px 0px 4px;
    -webkit-border-radius: 4px 0px 0px 4px;
    border-radius: 4px 0px 0px 4px;
	-webkit-box-shadow: 0 2px 2px 0 #C2C2C2;
    box-shadow: 0 4px 4px 0 #C2C2C2;
}

.ad_sidebox ul li {
	list-style-type: none;
	font-size:14px;
	line-height: 30px;
}
</style>

<script>
window.onscroll = function() {

	if( window.XMLHttpRequest ) {
		if (document.documentElement.scrollTop > 221 || self.pageYOffset > 221) {
			$('#ad_content').style.position = 'fixed';
			$('#ad_content').style.top = '0';
		} else if (document.documentElement.scrollTop < 221 || self.pageYOffset < 221) {
			$('#ad_content').style.position = 'absolute';
			$('#ad_content').style.top = '221px';
		}
	}
}
</script>

<div id="ad_content" class="ad_sidebox" style="position: absolute; top: 221px;">
    <h4>Introducing...</h4>
    <ul>
        <li>New Campaign Management</li>
        <li>Open Pixel Tracking &amp; Retargeting</li>
        <li>Retargeting Display Ads</li>
        <li>17+ Networks Supported</li>
    </ul>
</div>