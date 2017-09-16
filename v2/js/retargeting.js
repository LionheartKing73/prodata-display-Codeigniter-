var ProDataRetargeting = function(apiKey, campaignSO) {
	this.server = {};
	this.params = {};
	this.apiKey = apiKey;
	this.campaignSO = campaignSO;

	this.init = function(apiKey) {
		if (typeof XMLHttpRequest != 'undefined') {
			this.server = new window.XMLHttpRequest();

			this.server.onreadystatechange=function() {
				if (this.server.readyState == 4 && this.server.status == 200) {
					console.log("www.ProData.Media - Retargeting - Ready");
				}
			}

			this.server.open("GET", "//reporting.prodata.media/tracking/retarget/" + campaignSO, true);
			console.log("www.ProData.Media - Retargeting - Submit");
			return true;
		}
	};

	this.send = function() {
		if (this.init()) {
			this.server.send();
		}
	};

	if (this.apiKey == undefined) {
		console.log("www.ProData.Media - API Key Required");
	} else {
		this.send();
	}
};