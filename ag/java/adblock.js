// File: adblock.js
// Version: 1.2
// Update 9/1/04: fixed spelling errors in text
// Update 9/7/04: Added deny feature, allowing no access to users with AdBlock software.
// Distributed by: AdBlock.org
// This file is provided AS-IS and without warranty and is distributed under the GPL.
// This code may be freely used as long as it remains intact.

var chip = document.cookie;
var deny = 0;  //set this to 1 to deny access with no access.  
  // A denied user will go to page sorry.html.
  // if you enable this feature you must create a page called /sorry.html

function getCookie(name) { 
	var index = chip.indexOf(name + "=");
	if (index == -1) return null;
	index = chip.indexOf("=", index) + 1; // first character
	var endstr = chip.indexOf(";", index);
	if (endstr == -1) endstr = chip.length; // last character
	return unescape(chip.substring(index, endstr));
}

function hidediv() {
	if (document.getElementById) { 
		document.getElementById('warning').style.visibility = 'hidden';
		document.cookie = "warned=true; PATH=/";
	} 
}

function clear_cookie(){
	document.cookie =  "warned=; PATH=/";
}


if (document.getElementById) {
	if (!getCookie("warned")) {
		document.write("<div id=\"warning\" style=\"visibility:hidden; position:absolute; background: #000000; width:80%; left: 10%; top:50px; padding: 10px 10px 10px 10px; border: 1px solid black; \">");
		document.write("<p>Wichtig: <b>Es scheint, dass du eine Adblock Software nutzt.</b></p>");
		document.write("<p>Der Server dieses Spiels wird aus Werbung finanziert. ");
		document.write("Wenn du also Werbung blockst, f&uuml;hrt das evtl. dazu, dass die Kosten des Servers nicht gedeckt werden k&ouml;nnen. Die Spielleitung wird keine nervigen Werbungen einf&uuml;gen, wodurch der Einsatz eines Adblockers nicht gerechtfertigt ist.</p>");
		document.write("<p>Fuer mehr Informationen, <a href=\"http://www.adblock.org\" style=\"background: #0000FF;\">visit AdBlock.org</a>.</p>");
		document.write("<p><div align=center><a href=\"javascript:hidediv()\" style=\"background: #0000FF;\">Hier klicken um Info zu verbergen</a>.<br>");
		document.write("Du wirst diese Nachricht in dieser Sitzung nicht mehr empfangen (Cookies muessen aktiviert sein.)</p></div></div>");
	}
}

function checkVisible(variable){
	if(variable == 1){
		document.getElementById('warning').style.visibility = 'visible';
	}
}
/*
if (document.images) {
	var currImg;
	var ImgFound = 0;
	var LinkFound = 0;
	//alert("checking images..");
	for (var d=0; d < document.images.length; ++d) {
		currImg = document.images[d];
		if (currImg.src.indexOf("banner_ad") > -1) {
			ImgFound = 1;
		}
	}
	
	if (!ImgFound || AdBlockTest) {
		if(deny) {	 
			location = "/sorry.html";
			window.location(location);		
		} else {
			document.getElementById('warning').style.visibility = 'visible';
		}
	}

}*/
