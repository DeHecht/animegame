/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
// Quelle: http://www.devblog.de
// "Shoutbox als AJAX-Anwendung"
// Code darf frei verwendet werden

// globale Instanz von XMLHttpRequest
var xmlHttp = false;
var char_id1 = 0;
var char_id2 = 0;
var warte_zeit = 0;

v=new Date();

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





 function char(char_id_1, char_id_2, wartezeit) {
 	char_id1 = char_id_1;
  char_id2 = char_id_2;
  warte_zeit = wartezeit;

      loadData();

  setInterval("loadData()",3000);
  setInterval("abholen()",1000);

 }


    function abholen() {

   	n=new Date();
		s=warte_zeit-Math.round((n.getTime()-v.getTime())/1000.);
		m=0;
		h=0;
		if(s<0){
			document.getElementById("timer").innerHTML="<a href='javascript:showfight1()'>Angucken</a>";
		} else {

			if(s>59){
				m=Math.floor(s/60);
				s=s-m*60
			}

			if(m>59){
				h=Math.floor(m/60);
				m=m-h*60
			}

			if(s<10){
				s='0'+s
			}

			if(m<10){
				m='0'+m
			}

			document.getElementById("timer").innerHTML="Zeit:"+h+':'+m+':'+s
    }
  }


function loadData()
{

 if (xmlHttp) {
    // xmlHttp.open('GET', 'kampf/chat.php?action=load&char_id1='+char_id1+'&char_id2='+char_id2, true);
    	xmlHttp.open('GET', 'kampf/chat.php?action=load&char_id1=4&char_id2=3', true);  
     xmlHttp.onreadystatechange = function () {
         if (xmlHttp.readyState == 4) {   ////4 f�r fertig
             document.getElementById("text_inhalt").innerHTML = xmlHttp.responseText;
         }
     };

     xmlHttp.send('char_id1'+char_id1+'&char_id2'+char_id2);
 }
}

function saveData()
{

if (xmlHttp) {
    xmlHttp.open('POST', 'kampf/chat.php?action=save');
    xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xmlHttp.send('text='+document.kampfchat.textfeld.value+'&charakter='+document.kampfchat.char.value+'&besitzer='+document.kampfchat.user.value);
}

// Message-Eingabefelder leeren und Focus setzen
document.kampfchat.textfeld.value = '';
document.kampfchat.textfeld.focus();
}
