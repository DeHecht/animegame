/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
<!--

var aktiv;

function button_plus(feld,max,schritt)
{
	if(document.forms[0].elements[feld].value<max)
	{
		nummer=parseInt(document.forms[0].elements[feld].value)+schritt;
		document.forms[0].elements[feld].value=nummer;
	}
}

function button_minus(feld,min,schritt)
{
	if(document.forms[0].elements[feld].value>min)
	{
		nummer=parseInt(document.forms[0].elements[feld].value)-schritt;
		document.forms[0].elements[feld].value=nummer;
	}
}

function start_plus(feld,max,schritt)
{
	aktiv = setInterval("button_plus('"+feld+"',"+max+","+schritt+")",50);
}

function start_minus(feld,min,schritt)
{
	aktiv = setInterval("button_minus('"+feld+"',"+min+","+schritt+")",50);
}

function stopp()
{
	clearInterval(aktiv);
}
-->
