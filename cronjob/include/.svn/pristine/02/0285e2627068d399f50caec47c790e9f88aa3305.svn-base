/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

<!--

var aktiv;
var string;
function button_plus(feld,max,schritt,dex)
{
document.forms[0].elements['submit'].disabled=false;


	if(document.forms[0].elements[feld].value<max && document.forms[0].elements['skill'].value>0)
	{
		nummer=parseInt(document.forms[0].elements[feld].value)+1;
		document.forms[0].elements[feld].value=nummer;

		skills=parseInt(document.forms[0].elements['skill'].value)-1;
		document.forms[0].elements['skill'].value=skills;
	}
	

if(dex == 3 && document.forms[0].elements['skill'].value>0 && document.forms[0].elements[feld].value!='Ja') {
document.forms[0].elements[feld].value="Ja";

		skills=parseInt(document.forms[0].elements['skill'].value)-1;
		document.forms[0].elements['skill'].value=skills;
}

		if(dex == 2 && document.forms[0].elements[feld].value<50 && document.forms[0].elements['skill'].value>0) {


		skills=parseInt(document.forms[0].elements['skill'].value)-1;
		document.forms[0].elements['skill'].value=skills;
		

		nummer = parseInt(document.forms[0].elements[feld].value)+10;
		document.forms[0].elements[feld].value = nummer;


		}



}
function button_minus(feld,min,schritt,dex)
{
document.forms[0].elements['submit'].disabled=false;



	if(document.forms[0].elements[feld].value>min && dex != 2)
	{
		nummer=parseInt(document.forms[0].elements[feld].value)-1;
		document.forms[0].elements[feld].value=nummer;

		nummers=parseInt(document.forms[0].elements['skill'].value)+1;
		document.forms[0].elements['skill'].value=nummers;
	}
if(dex == 3 && document.forms[0].elements[feld].value=='Ja' && min != 1) {
document.forms[0].elements[feld].value='Nein';

		skills=parseInt(document.forms[0].elements['skill'].value)+1;
		document.forms[0].elements['skill'].value=skills;
}



		if(dex == 2 && document.forms[0].elements[feld].value>min) {


		skills=parseInt(document.forms[0].elements['skill'].value)+1;
		document.forms[0].elements['skill'].value=skills;
		

		nummer = parseInt(document.forms[0].elements[feld].value)-10;
		document.forms[0].elements[feld].value = nummer;


		}



}



function start_plus(feld,max,schritt,dex)
{
	aktiv = setInterval("button_plus('"+feld+"',"+max+","+schritt+", "+dex+")",50);
}

function start_minus(feld,min,schritt,dex)
{
	aktiv = setInterval("button_minus('"+feld+"',"+min+","+schritt+", "+dex+")",50);
}

function stopp()
{
	clearInterval(aktiv);
}
-->
