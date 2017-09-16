var ProDataConversion = function(apiKey, conversionValue) {
	this.server = {};
	this.params = {};
	this.apiKey = apiKey;
	this.conversionValue = conversionValue;

	this.init = function(apiKey) {
		if (typeof XMLHttpRequest != 'undefined') {
			this.server = new window.XMLHttpRequest();
			
			this.server.onreadystatechange=function() {
				if (this.server.readyState == 4 && this.server.status == 200) {
					console.log("www.ProData.Media - Conversion - Ready");
				}
			}

			this.params = {
				'userAgent' : navigator.userAgent,
				'pageUrl' : window.location.href,
				'cookieEnabled' : navigator.cookieEnabled,
				'apiKey' : this.apiKey,
				'conversionValue' : this.conversionValue,
			};

			this.server.open("POST", "//reporting.prodata.media/v2/conversion/tracker", true);
			this.server.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			console.log("www.ProData.Media - Conversion - Submit");
			return true;
		}
	};

	this.send = function() {
		if (this.init()) {
			this.server.send("param=" + JSON.stringify(this.params));
		}
	};
	
	if (this.apiKey == undefined) {
		console.log("www.ProData.Media - API Key Required");
	} else {
		this.send();
	}
};