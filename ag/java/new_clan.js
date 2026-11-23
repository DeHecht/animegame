/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
<!--
var new_preis = 0;
var news_preis = 0;

var aktiv;

function nix()
{
}





function button_plus(feld,schritt)
{
	new_wert = parseInt(document.getElementById(feld).innerHTML)+schritt;
	new_tag = parseInt(document.forms[0].elements['tag'].value)+1;
	new_tick = parseInt(document.forms[0].elements['uhr1'].value) + 1;
	if(new_wert > 23) {
				if(new_tag<21) {
					document.forms[0].elements['tag'].value = new_tag;
					new_wert=0;
				}
	}

if(new_tag<21) {
	document.getElementById(feld).innerHTML = new_wert;
	document.forms[0].elements['uhr1'].value = new_tick;
}
		<!--	document.getElementById(feld).innerHTML = ''; -->
}

function button_minus(feld,schritt,min)
{

	new_wert = parseInt(document.getElementById(feld).innerHTML)-schritt;
	new_tick = parseInt(document.forms[0].elements['uhr1'].value) - 1;


	new_tag = parseInt(document.forms[0].elements['tag'].value);

	if(new_wert < 0 && new_tag != 0) {
		document.forms[0].elements['tag'].value = new_tag-1;
		new_wert=16;
	}

		if(min>new_wert && new_tag == 0) {
		return;
		}

if(new_tag == 0 && new_wert<0) {

} else {
	document.getElementById(feld).innerHTML = new_wert;
	document.forms[0].elements['uhr1'].value = new_tick;
}
		<!--	document.getElementById(feld).innerHTML = ''; -->

}

-->
