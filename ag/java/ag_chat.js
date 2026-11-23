/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
// Quelle: http://www.devblog.de
// "Shoutbox als AJAX-Anwendung"
// Code darf frei verwendet werden

// globale Instanz von XMLHttpRequest
var xmlHttp = false;

// XMLHttpRequest-Instanz erstellen
// ... f�r Internet Explorer

try {
    xmlHttp  = new ActiveXObject("Msxml2.XMLHTTP");
} catch(e) {
    try {
        xmlHttp  = new ActiveXObject("Microsoft.XMLHTTP");
    } catch(e) {
        xmlHttp  = false;
    }
}
// ... f�r Mozilla, Opera und Safari
if (!xmlHttp  && typeof XMLHttpRequest != 'undefined') {
    xmlHttp = new XMLHttpRequest();
}



  loadData();
  setInterval("loadData()",6500);



function loadData()
{

 if (xmlHttp) {
     xmlHttp.open('GET', 'info/agchat.php?action=load', true);
     xmlHttp.onreadystatechange = function () {
         if (xmlHttp.readyState == 4) {   ////4 f�r fertig
             document.getElementById("text_inhalt").innerHTML = xmlHttp.responseText;
         }
     };

     xmlHttp.send(null);
 }
}

function saveData()
{

if (xmlHttp) {
	var url = 'text='+document.kampfchat2.textfeld.value.replace(/&/g, '[AMP]').replace(/\+/g, '[ADD]').replace(/\\/g, '\\\\');
    xmlHttp.open('POST', 'info/agchat.php?action=save');
    xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xmlHttp.send(url);
}

// Message-Eingabefelder leeren und Focus setzen
document.kampfchat2.textfeld.value = '';
document.kampfchat2.textfeld.focus();
}