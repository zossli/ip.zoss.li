<HTML>

<HEAD>
<TITLE>ip.zoss.li - IP Infos</TITLE>
<meta name="description=" content="&Uuml;berpr&uuml;fe deine public und local used IP Adresse. Zudem siehst du hilfreiche Informationen aus der RIPE DB.">
<link rel="icon" href="data:;base64,iVBORw0KGgo=">
</HEAD>
<BODY BGCOLOR="#ffffff" TEXT="#000000" LINK="#0000a0" VLINK="#0000a0" ALINK="#0000a0">

<?php

function getInfo($array){
	foreach($array as $ripe)
	{
			foreach($ripe as $ripeval)
			{
				
				if(isset($ripeval->link))
				{	echo("<br />".$ripeval->name.": ".$ripeval->value);					
				
					$moreDetails = json_decode(file_get_contents($ripeval->link->href .".json"));
					foreach($moreDetails->objects->object[0]->attributes as $ripetwo)
					{
						foreach($ripetwo as $ripevaltwo)
						{
							switch($ripevaltwo->name)
							{
							case "org-name":
							case "address":
							case "phone":
							case "fax-no":
							case "person":
							case "created":
							case "last-modified":
							case "mnt-by":
							case "descr":
								echo("<br /><span style='margin-left:25pt'>".$ripevaltwo->name.": ".$ripevaltwo->value. "</span>");				
							break;
							}
						}
						echo "<br />";
					}
				}
				else
				{
					echo("<br />".$ripeval->name.": ".$ripeval->value);
				}
			}
	echo "<br />";
	}

}

    echo("Current IP:".$_SERVER["REMOTE_ADDR"]."<br />");
	echo("used LAN IP:<span id=\"list\"></span><br /><br />");

	$ip = $_SERVER['REMOTE_ADDR'];
	$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
	echo("resolved Name: ".$details->hostname."<br />");
	echo("retrieved City: ".$details->city."<br />");
	echo("retrieved Region: ".$details->region."<br />");
	echo("retrieved Country: ".$details->country."<br /><br />");
$details = json_decode(file_get_contents("http://rest.db.ripe.net/search.json?query-string={$ip}&flags=no-filtering"));
			echo("RIPE.net:");
	
	getInfo($details->objects->object[0]->attributes);
		

?>
<br /><br /><br />

    <script>

var RTCPeerConnection =  window.webkitRTCPeerConnection || window.mozRTCPeerConnection;

if (RTCPeerConnection) (function () {
    var rtc = new RTCPeerConnection({iceServers:[]});
    if (1 || window.mozRTCPeerConnection) {      
        rtc.createDataChannel('', {reliable:false});
    };
    
    rtc.onicecandidate = function (evt) {
        if (evt.candidate) grepSDP("a="+evt.candidate.candidate);
    };
    rtc.createOffer(function (offerDesc) {
        grepSDP(offerDesc.sdp);
        rtc.setLocalDescription(offerDesc);
    }, function (e) { console.warn("offer failed", e); });
    
    
    var addrs = Object.create(null);
    addrs["0.0.0.0"] = false;
    function updateDisplay(newAddr) {
        if (newAddr in addrs) return;
        else addrs[newAddr] = true;
        var displayAddrs = Object.keys(addrs).filter(function (k) { return addrs[k]; });
        document.getElementById('list').textContent = displayAddrs.join(" or perhaps ") || "n/a";
    }
    
    function grepSDP(sdp) {
        var hosts = [];
        sdp.split('\r\n').forEach(function (line) { // c.f. http://tools.ietf.org/html/rfc4566#page-39
            if (~line.indexOf("a=candidate")) {     // http://tools.ietf.org/html/rfc4566#section-5.13
                var parts = line.split(' '),        // http://tools.ietf.org/html/rfc5245#section-15.1
                    addr = parts[4],
                    type = parts[7];
                if (type === 'host') updateDisplay(addr);
            } else if (~line.indexOf("c=")) {       // http://tools.ietf.org/html/rfc4566#section-5.7
                var parts = line.split(' '),
                    addr = parts[2];
                updateDisplay(addr);
            }
        });
    }
})(); else {
    document.getElementById('list').innerHTML = "nicht verf&uuml;gbar. - Verwende Chrome oder Firefox";
}
	</script>

</BODY>

</HTML>
