<?php 
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
?>

<script language="JavaScript" type="text/javascript">

<!--
var arena_geld;
function arena()
{

platz=parseInt(document.forms[0].elements['platz'].value)*500;
ring=parseInt(document.forms[0].elements['ring'].value);
var x=0;
var ring_preis=0;

arena_geld = ((platz/500) / 2) + (ring * 3) + (1 * 5) - (10 / 5);
arena_geld /= 4;


while(x < ring) {
x++;
ring_preis += x * 100;
}

document.forms[0].elements['preis'].value=platz+ring_preis;
document.forms[0].elements['besucher'].value=arena_geld;
}
-->
</script>

<form action="pl.php" method="GET">
<input name="platz" value="0" maxlength="5" onKeyup="arena();" size=7> Pl&auml;tze<br>
<input name="ring" value="0" maxlength="5" onKeyup="arena();" size=7> Ring<br>
<input name="preis" value="0" onKeyup="arena();" readonly size=7> Preis<br>
<input name="besucher" value="0" onKeyup="arena();" readonly size=7> Besucher<br>
</form>


