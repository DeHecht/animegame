/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
<!--

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
	document.getElementById('kosten').innerHTML = 'Kosten:'+(kosten+Math.round(Math.log(std*0.1+1.25)*kosten,0));
	document.getElementById('reisedauer').innerHTML = 'Std:'+std;
	document.getElementById('info').innerHTML = info;
	document.getElementById('input').innerHTML = '<input type="hidden" name="stadt_name" value="'+stadt+'"><input type=submit value="'+stadt+' Reise">';
}

-->