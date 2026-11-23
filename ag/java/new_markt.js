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

	function info(id){
		var Info = window.open("markt.php?info="+id,"","status=no,hotkeys=no,Height=250,Width=250,scrollbars=no");
	}


function button_plus(feld,max,schritt,preis)
{

		if(document.forms[0].elements[feld].value<=max)
		{
			nummer=parseInt(document.forms[0].elements[feld].value)+schritt;
			document.forms[0].elements[feld].value=nummer;
			change1(preis);
		}
}

function button_minus(feld,min,schritt, preis)
{
		if(document.forms[0].elements[feld].value>min)
		{
			nummer=parseInt(document.forms[0].elements[feld].value)-schritt;
			document.forms[0].elements[feld].value=nummer;
			change2(preis);
		}
}

function nachrechnen() 
{
	zaehler = 1;
	news_preis = 0;
	for (i = 0; i < document.forms[0].elements.length; i++)
	{
		if (document.forms[0].elements[i].type == 'text') 
		{
			var preis = document.forms[0].elements['anzahl_'+zaehler].value*document.forms[0].elements['preis_'+zaehler].value;
			news_preis += Number(preis);
			zaehler++;
		}
	}
	document.getElementById('preis').innerHTML = 'Preis: '+news_preis;
}

function preis() {
	document.getElementById('preis').innerHTML = 'Preis: '+new_preis;
}

function change1(preis) {
	news_preis = news_preis + preis;
	document.getElementById('preis').innerHTML = 'Preis: '+news_preis;
}

function change2(preis) {
	news_preis = news_preis - preis;
	document.getElementById('preis').innerHTML = 'Preis: '+news_preis;
}
-->
