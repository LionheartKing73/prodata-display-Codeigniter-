
var xhttp = new XMLHttpRequest();
xhttp.open("GET", "https://reporting.prodata.media" +
"/tracking/beacon/" + tracking_campaign_id + "/" + tracking_ad_id, true);
xhttp.send();



var img = document.createElement('img');
img.src = creative_src;
img.width = creative_width;
img.height = creative_height;
img.border = "0";
img.style.margin = "0";
img.style.padding = "0";
img.style.overflow = "hidden";

var a = document.createElement('a');
a.href = destination_url;
a.target = '_blank';
a.appendChild(img);

document.body.appendChild(a);
