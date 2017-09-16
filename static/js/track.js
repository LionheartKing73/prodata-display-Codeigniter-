function setCookie(key, value)	{
	date = new Date();
	date.setDate(date.getDate() + TP_duration);
	document.cookie = escape(key) + '=' + escape(value) + ';expires=' + date;
}

function deleteCookie(key)	{
	date = new Date();
	date.setDate(date.getDate() - 1);
	document.cookie = escape(key) + '=;expires=' + date;
}

function getCookie(key)	{
	var cookies = document.cookie.split(";");
	var index;
	for(index = 0; index < cookies.length; index++)	{
		cookieEntry = cookies[index].split("=");
		if (key == cookieEntry[0])	{
			return cookieEntry[1];
		}
	}
	return null;
}

if (window['TP_campaignIO'] != undefined)	{
	var cookie = getCookie("trafficPingTracker");
	if (cookie == null && TP_conversionTracker == false)	{
		setCookie("trafficPingTracker", TP_campaignIO);
	} else {
		xmlhttp = new window.XMLHttpRequest();
		xmlhttp.onreadystatechange=function()	{
			if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
				// success for logging.
				console.log("conversion logged for " + TP_campaignIO + " at a value of " + TP_conversionValue);
			}
		}
		xmlhttp.open("GET","//t5camps.com/campclick/conversion/" + TP_campaignIO + "/" + TP_conversionValue, true);
		xmlhttp.send();
	}
} else {
	console.log("Invalid or unregistered campaignIO variable.");	
}