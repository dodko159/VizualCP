
function showMobileAlert(){
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
		
		document.getElementById("under_door").classList.add("mobile_under_door");
		
        //if(window.innerHeight > window.innerWidth){
            var d = new Date();
            var alertedDay = localStorage.getItem('alerted-day') || '';
            if (alertedDay != d.getDate()) {

                document.getElementById("mobile-alert-window").classList.add("mobile");
                    
             localStorage.setItem('alerted-day',d.getDate());
            }
        //}
    }
	
	$("#mobile-alert-window").click(function(){
		$(this).removeClass("mobile");
	});
}

function supportAPNG(){
    if(BrowserDetect.browser == "Chrome" && BrowserDetect.version > 58){
        return true;
    }else if(BrowserDetect.browser == "Firefox" && BrowserDetect.version > 2){
        return true;
    }else if(BrowserDetect.browser == "Safari" && BrowserDetect.version >= 8){
        return true;
    }
    else if(BrowserDetect.browser == "Opera" && BrowserDetect.version >= 46){
        return true;
    }else if(BrowserDetect.browser == "Chrome" && BrowserDetect.OS == "Android" && BrowserDetect.version > 67){
        return true;
    }else if(BrowserDetect.browser == "Firefox" && BrowserDetect.OS == "Android" && BrowserDetect.version > 60){
        return true;
    }
    return false;
}

var BrowserDetect = {
	init: function () {
		this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
		this.version = this.searchVersion(navigator.userAgent)
			|| this.searchVersion(navigator.appVersion)
			|| "an unknown version";
		this.OS = this.searchString(this.dataOS) || "an unknown OS";
		this.bit = this.searchString(this.dataBit) || " x32"; 
		this.OsVersion = this.searchString(this.dataOsVersion) || "an unknown OS version";
	},
	searchString: function (data) {
		for (var i=0;i<data.length;i++)	{
			var dataString = data[i].string;
			var dataProp = data[i].prop;
			this.versionSearchString = data[i].versionSearch || data[i].identity;
			if (dataString) {
				if (dataString.indexOf(data[i].subString) != -1)
					return data[i].identity;
			}
			else if (dataProp)
				return data[i].identity;
		}
	},
	searchVersion: function (dataString) {
		var index = dataString.indexOf(this.versionSearchString);
		if (index == -1) return;
		return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
	},
	dataBrowser: [
		{
			string: navigator.userAgent,
			subString: "Chrome",
			identity: "Chrome"
		},
		{ 	string: navigator.userAgent,
			subString: "OmniWeb",
			versionSearch: "OmniWeb/",
			identity: "OmniWeb"
		},
		{
			string: navigator.vendor,
			subString: "Apple",
			identity: "Safari",
			versionSearch: "Version"
		},
		{
			prop: window.opera,
			identity: "Opera",
			versionSearch: "Version"
		},
		{
			string: navigator.vendor,
			subString: "iCab",
			identity: "iCab"
		},
		{
			string: navigator.vendor,
			subString: "KDE",
			identity: "Konqueror"
		},
		{
			string: navigator.userAgent,
			subString: "Firefox",
			identity: "Firefox"
		},
		{
			string: navigator.vendor,
			subString: "Camino",
			identity: "Camino"
		},
		{		// for newer Netscapes (6+)
			string: navigator.userAgent,
			subString: "Netscape",
			identity: "Netscape"
		},
		{
			string: navigator.userAgent,
			subString: "MSIE",
			identity: "Explorer",
			versionSearch: "MSIE"
		},
		{
			string: navigator.userAgent,
			subString: "Gecko",
			identity: "Mozilla",
			versionSearch: "rv"
		},
		{ 		// for older Netscapes (4-)
			string: navigator.userAgent,
			subString: "Mozilla",
			identity: "Netscape",
			versionSearch: "Mozilla"
		}
	],
	dataOS : [
		{
			string: navigator.platform,
			subString: "Win",
			identity: "Windows"
		},
		{
			string: navigator.platform,
			subString: "Mac",
			identity: "Mac"
		},
		{
			   string: navigator.userAgent,
			   subString: "iPhone",
			   identity: "iPhone/iPod"
	    },
		{
			string: navigator.platform,
			subString: "Linux",
			identity: "Linux"
		}
	],
	dataBit : [
		{
			string: navigator.userAgent,
			subString: "Win64",
			identity: "x64"
		},
		{
			string: navigator.userAgent,
			subString: "WOW64",
			identity: "x64"
		}
	],
	dataOsVersion : [
		{
			string: navigator.userAgent,
			subString: "NT 5.1",
			identity: "XP"
		},
		{
			string: navigator.userAgent,
			subString: "NT 6.0",
			identity: "Vista"
		},
		{
			string: navigator.userAgent,
			subString: "NT 6.1",
			identity: "7"
		},
		{
			string: navigator.userAgent,
			subString: "NT 6.2",
			identity: "8"
		}
	]
};
BrowserDetect.init();