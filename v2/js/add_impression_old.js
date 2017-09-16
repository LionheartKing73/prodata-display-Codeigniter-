
// var xhttp = new XMLHttpRequest();
// xhttp.open("GET", "/tracking/beacon/" + tracking_campaign_id + "/" + tracking_ad_id, true);
// xhttp.send();



var ftClick = "";
var ftExpTrack_1927494 = "";
var ftX = "";
var ftY = "";
var ftZ = "";
var ftOBA = 1;
var ftContent = "";
var ftCustom = "";
var ft336x280_OOBclickTrack = "";
var ftRandom = Math.random()*1000000;
var ftBuildTag1 = "<scr";
var ftBuildTag2 = "</";
var ftClick_1927494 = ftClick;
if(typeof(ft_referrer)=="undefined"){
    var ft_referrer=(function(){
        var r="";
        if(window==top){
            r=window.location.href;
        }
        else{
            try{
                r=window.parent.location.href;
            }catch(e){

            }
            r=(r)?r:document.referrer;
        }
        while(encodeURIComponent(r).length>1000){
            r=r.substring(0,r.length-1);
        }return r;
    }
    ());
}
var ftDomain = (window==top)?"":(function(){
    var d=document.referrer,h=(d)?d.match("(?::q/q/)+([qw-]+(q.[qw-]+)+)(q/)?".replace(/q/g,decodeURIComponent("%"+"5C")))[1]:"";
    return (h&&h!=location.host)?"&ft_ifb=1&ft_domain="+encodeURIComponent(h):"";
}
());
var ftTag = ftBuildTag1 + 'ipt language="javascript1.1" type="text/javascript" ';
ftTag += 'src="http://servedby.flashtalking.com/imp/8/63854;1927494;201;js;Take5Solutions;Take5ContextualBannersIAD336x280/?ftx='+ftX+'&fty='+ftY+'&ftadz='+ftZ+'&ftscw='+ftContent+'&ft_custom='+ftCustom+'&ftOBA='+ftOBA+ftDomain+'&ft_referrer='+encodeURIComponent(ft_referrer)+'&cachebuster='+ftRandom+'" id="ftscript_336x280" name="ftscript_336x280"';
ftTag += '>' + ftBuildTag2 + 'script>';
document.write(ftTag);
