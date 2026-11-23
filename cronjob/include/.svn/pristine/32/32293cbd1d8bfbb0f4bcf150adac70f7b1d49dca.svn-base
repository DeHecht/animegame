/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */


<!--

var aktiv;
var anzahl;

if(!anzahl) { anzahl = 0; }
function arenaname() 
{

document.forms[0].elements['submit'].disabled=false;
}

function button_plus(feld,max,schritt){
	document.forms[0].elements['submit'].disabled=false;
	if(document.forms[0].elements[feld].value<max){
		nummer=parseInt(document.forms[0].elements[feld].value)+1;
		document.forms[0].elements[feld].value=nummer;

		gold=parseInt(document.forms[0].elements["geld"].value)-(schritt*nummer);
		document.forms[0].elements["geld"].value=gold;

		if(document.forms[0].elements['geld'].value<=0) {
			document.forms[0].elements['submit'].disabled=true;
		}
	}
}

function button_minus(feld,min,schritt){
	document.forms[0].elements['submit'].disabled=false;
	if(document.forms[0].elements[feld].value>min){
		nummer=parseInt(document.forms[0].elements[feld].value)-1;
		document.forms[0].elements[feld].value=nummer;

		gold=parseInt(document.forms[0].elements["geld"].value)+(schritt*nummer);
		document.forms[0].elements["geld"].value=gold;
		anzahl--;

		if(document.forms[0].elements['geld'].value<=0) {
			document.forms[0].elements['submit'].disabled=true;
		}
	}
}



function start_plus(feld,max,schritt)
{
	aktiv = setInterval("button_plus('"+feld+"',"+max+","+schritt+")",50);
}

function start_minus(feld,min,schritt)
{
	alert('Minus');
	aktiv = setInterval("button_minus('"+feld+"',"+min+","+schritt+")",50);
}

function stopp()
{
	clearInterval(aktiv);
}
-->
