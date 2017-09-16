var xhttp = new XMLHttpRequest();
xhttp.open("GET", "https://reporting.prodata.media" +
"/tracking/beacon/" + tracking_campaign_id + "/" + tracking_ad_id, true);
xhttp.send();

jwplayer.key="31g7gB9+3Ml/qV1DA9i/BtaSAATOAWIHjbVibA==";

var div = document.createElement('div');
div.id = "media_video";
div.style = "display: inline-block";

var a = document.createElement('a');
a.href = destination_url;
a.width = creative_width;
a.height = creative_height;
a.target = '_blank';
a.style.display="inline-block"
a.appendChild(div);

document.body.appendChild(a);

 jwplayer('media_video').setup({
	 file: video_url,
	 image: creative_src,
	 width: creative_width,
	 height: creative_height,
	 primary: "html5",
	 advertising: { 
	  client: "vast",
	}
});


