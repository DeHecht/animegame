/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *


Diese Datei soll alle eigenen Skripte umfassen, und das ausnahmslos.
Der Grund ist simpel. Es muss nur noch eine .js Seite im cache liegen und der Javascript
kann schneller starten.

Not yet finished!

*/

var busy; // Ein kleiner Timer

function setBusy(value){
	busy = value;
}
function getBusy(){
	return busy;
}


// Indexseite
function zeigeKampflisteAn(){
	var Info = window.open("kampf_list.php","","status=no,hotkeys=no,Height=600,Width=820,scrollbars=yes");
}

function opera_befreien () {
  document.links["befreien"].click();
}

// Schatzsuche

// Darstellen des Textes fuer den Ort
function showText(stadt,reisende,monster,kosten,info,x1,x2,xa1,xa2) 
{
	r_x1 = parseInt(x1);
	r_xa1 = parseInt(xa1);
	r_x2 = parseInt(x2);
	r_xa2 = parseInt(xa2);
	std = Math.round(Math.sqrt( (r_x1 - r_xa1) * (r_x1 - r_xa1) + (r_x2 - r_xa2) * (r_x2 - r_xa2))/35);
	document.getElementById('Textbox').style.visibility='visible';
 	document.getElementById('stadt').innerHTML = stadt;
	document.getElementById('reisende').innerHTML = 'Reisende:'+reisende;
	document.getElementById('monster').innerHTML = 'Monster:'+monster;
	document.getElementById('kosten').innerHTML = 'Kosten:'+kosten;
	document.getElementById('reisedauer').innerHTML = 'Std:'+std;
	document.getElementById('info').innerHTML = info;
	document.getElementById('input').innerHTML = '<input type="hidden" name="stadt_name" value="'+stadt+'"><input type=submit value="'+stadt+' Reise">';
}

// Abholen
function zeigeKampfberichtAn(site, abholstring){
	var Info = window.open("./"+site+".php?"+abholstring+"", "", "status=no,hotkeys=no,Height=600,Width=820,scrollbars=yes");
}

function inkrementiereFeld(feldid, value){
	document.getElementById(feldid).value = parseInt(document.getElementById(feldid).value) + value;
}

function dekrementiereFeld(feldid, value){
	document.getElementById(feldid).value -= value;
}

function startePeriodischesInkrementieren(feldid, value){
	
}