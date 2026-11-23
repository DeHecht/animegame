<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/messagefunctions.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$char_id = validateUnsignedInteger($_GET['char_id'], null);
$speed = validateUnsignedInteger($_GET['speed'], null);
$staerke = validateUnsignedInteger($_GET['staerke'], null);
$verteidigung = validateUnsignedInteger($_GET['verteidigung'], null);
$glueck = validateUnsignedInteger($_GET['glueck'], null);
$ausdauer = validateUnsignedInteger($_GET['ausdauer'], null);
$hp = validateUnsignedInteger($_GET['hp'], null);
$mp = validateUnsignedInteger($_GET['mp'], null);

// Unkritisch
$charm = $_GET['charm'];


if (!$char_id) {
	include (ROOT_PATH . '/char_index.php');
	exit;
}

$user = mysqli_fetch_array(db_query("SELECT id, geld FROM user WHERE nickname='$name'"));

$char = getChar($char_id);

if ($speed < 0 || $staerke < 0 || $verteidigung < 0 || $glueck < 0 || $ausdauer < 0 || $hp < 0 || $mp < 0) {
	db_query("INSERT INTO cheater values('$user_ida[id]','Versuchte negative Werte in Levelup einzutragen!',now())");
	displayErrorMessage(NULL,'Nicht cheaten....', displayHistoryBackLink());
	exit;
}

if ($char['besitzer'] != $user['id'] && !$TETTETERTSTDSGFDHHRHGNJ) {
	displayErrorMessage(NULL,'Charakter geh&ouml;rt nicht dir!', displayHistoryBackLink());
	exit;
}

if ($speed + $staerke + $verteidigung + $glueck + $ausdauer + $hp + $mp > $char['lernpunkte']) {
	displayErrorMessage(NULL,'Du hast keine Lernpunkte mehr zur Verf&uuml;gung!', displayHistoryBackLink());
	exit;
}

$char_hp = explode(",", $char['hp']);
$char_mp = explode(",", $char['mp']);
if ($charm) {

	$new_st = $char['starke'] + $staerke;
	$new_ver = $char['verteidigung'] + $verteidigung;
	$new_speed = $char['speed'] + $speed;
	$new_ausdauer = $char['ausdauer'] + $ausdauer;
	$new_glueck = $char['glueck'] + $glueck;
	$new_hp = $char_hp[1] + $hp * 10;
	$new_mp = $char_mp[1] + $mp * 5;
	$new_lern = $char['lernpunkte'] - $speed - $staerke - $verteidigung - $glueck - $ausdauer - $hp - $mp;

	db_query("UPDATE chars SET lernpunkte='$new_lern', starke='$new_st', verteidigung='$new_ver', speed='$new_speed', ausdauer='$new_ausdauer',
	glueck='$new_glueck', hp='$char_hp[0],$new_hp', mp='$char_mp[0],$new_mp' WHERE id='$char_id'");

	$fehler_m = "
	St&auml;rke: old($char[starke]) new($new_st)<br>
	Verteidigung: old($char[verteidigung]) new($new_ver)<br>
	Geschwindigkeit: old($char[speed]) new($new_speed)<br>
	Ausdauer: old($char[ausdauer]) new($new_ausdauer)<br>
	Gl&uuml;ck: old($char[glueck]) new($new_glueck)<br>
	HP: old($char_hp[1]) new($new_hp)<br>
	MP: old($char_mp[1]) new($new_mp)<br>
	";

	insertUserTickerMessage($user_ida['id'], 'Es wurden Lernpunkte des Chars '.$char['name'].'(ID:'.$char['id'].') eingesetzt. Neue Werte sind: Str: '.$new_st.',Def: '.$new_ver.',Lck: '.$new_glueck.',Spd: '.$new_speed.', Aus: '.$new_ausdauer.', HP: '.$new_hp.', MP: '.$new_mp.', LP: '.$new_lern);
	displayErrorMessage(NULL,$fehler_m, '<a href=index.php?as=lernpunkte>weiter...</a>');
	exit;
}
?>
<html>
<head>
<script type="text/javascript">

<!--

function show_me () {
summe = (parseInt(document.forms[0].elements["staerke"].value) + parseInt(document.forms[0].elements["verteidigung"].value) + parseInt(document.forms[0].elements["speed"].value) + parseInt(document.forms[0].elements["ausdauer"].value) + parseInt(document.forms[0].elements["glueck"].value) + parseInt(document.forms[0].elements["hp"].value) + + parseInt(document.forms[0].elements["mp"].value));
alert (summe);
}


var aktiv;

function button_plus(feld,schritt)
{max = <?php echo $char[lernpunkte]; ?> - document.forms[0].elements["ges_lern"].value;
	if(document.forms[0].elements[feld].value<=max)
	{
		if( document.forms[0].elements["ges_lern"].value>0) {
			document.forms[0].elements["ges_lern"].value = document.forms[0].elements["ges_lern"].value - parseInt(schritt);
			nummer=parseInt(document.forms[0].elements[feld].value)+schritt;
			document.forms[0].elements[feld].value=nummer;
		}
	}
}

function button_minus(feld,min,schritt)
{
	if(document.forms[0].elements[feld].value>min)
	{
		document.forms[0].elements["ges_lern"].value = parseInt(document.forms[0].elements["ges_lern"].value)+parseInt(schritt);
		nummer=parseInt(document.forms[0].elements[feld].value)-schritt;
		document.forms[0].elements[feld].value=nummer;
	}
}

function real_start_plus(feld, schritt) {
	clearInterval(aktiv);
	aktiv = setInterval("button_plus('"+feld+"',"+schritt+")",100);

}

function real_start_minus(feld, min, schritt) {
	clearInterval(aktiv);
	aktiv = setInterval("button_minus('"+feld+"',"+min+","+schritt+")",100);
}

function start_plus(feld,schritt) {
	aktiv = setInterval("real_start_plus('"+feld+"',"+schritt+")", 250);
}

function start_minus(feld,min,schritt) {
	aktiv = setInterval("real_start_minus('"+feld+"',"+min+","+schritt+")", 250);
}

function stopp()
{
	clearInterval(aktiv);
	aktiv = null;
}
-->
</script>
</head>

<body>
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="as" value="lernpunkte">
	<input type="hidden" name="char_id" value="<?php echo $char_id; ?>">
	<input type="hidden" name="charm" value="1">
  <table width="100%" border="0">
    <tr>
      <td width="160">Restliche Lernpunkte</td>
      <td width="236"><input id="input" name="ges_lern" type="text" value="<?php echo $char['lernpunkte']; ?>" size="2" readonly></td>
    </tr>
    <tr>
      <td>St&auml;rke <?php echo $char['starke']; ?></td>
      <td><input id="input" name="staerke" type="text" value=0 size="2" readonly>
      <input id="input" type="button" name="+1" value=" + " onClick="button_plus('staerke', 1)" onMouseDown="start_plus('staerke',1);" onMouseUp="stopp();">
      <input id="input" type="button" name="-1" value="-" onClick="button_minus('staerke', 0, 1)" onMouseDown="start_minus('staerke',0,1);" onMouseUp="stopp();"></td>
    </tr>
    <tr>
      <td>Verteidigung <?php echo $char['verteidigung']; ?></td>
      <td><input id="input" name="verteidigung" type="text" value=0 size="2" readonly>
      <input id="input" type="button" name="+2" value=" + " onClick="button_plus('verteidigung', 1)" onMouseDown="start_plus('verteidigung',1);" onMouseUp="stopp();">
      <input id="input" type="button" name="-2" value="-" onClick="button_minus('verteidigung', 0, 1)" onMouseDown="start_minus('verteidigung',0,1);" onMouseUp="stopp();"></td>
    </tr>
    <tr>
      <td>Geschwindigkeit <?php echo $char['speed']; ?></td>
      <td><input id="input" name="speed" type="text" value=0 size="2" readonly>
      <input id="input" type="button" name="+3" value=" + " onClick="button_plus('speed', 1)" onMouseDown="start_plus('speed',1);" onMouseUp="stopp();">
      <input id="input" type="button" name="-3" value="-" onClick="button_minus('speed', 0, 1)" onMouseDown="start_minus('speed',0,1);" onMouseUp="stopp();"></td>
    </tr>
    <tr>
      <td>Ausdauer <?php echo $char['ausdauer']; ?></td>
      <td><input id="input" name="ausdauer" type="text" value=0 size="2" readonly>
      <input id="input" type="button" name="+4" value=" + " onClick="button_plus('ausdauer', 1)" onMouseDown="start_plus('ausdauer',1);" onMouseUp="stopp();">
      <input id="input" type="button" name="-4" value="-" onClick="button_minus('ausdauer', 0, 1)" onMouseDown="start_minus('ausdauer',0,1);" onMouseUp="stopp();"></td>
    </tr>
    <tr>
      <td>Gl&uuml;ck <?php echo $char['glueck']; ?></td>
      <td><input id="input" name="glueck" type="text" value=0 size="2" readonly>
      <input id="input" type="button" name="+5" value=" + " onClick="button_plus('glueck', 1)" onMouseDown="start_plus('glueck',1);" onMouseUp="stopp();">
      <input id="input" type="button" name="-5" value="-" onClick="button_minus('glueck', 0, 1)" onMouseDown="start_minus('glueck',0,1);" onMouseUp="stopp();"></td>
    </tr>
    <tr>
      <td>HP <?php echo $char_hp[1]; ?> ( 1 = 10)</td>
      <td><input id="input" name="hp" type="text" value=0 size="2" readonly>
      <input id="input" type="button" name="+6" value=" + " onClick="button_plus('hp', 1)" onMouseDown="start_plus('hp',1);" onMouseUp="stopp();">
      <input id="input" type="button" name="-6" value="-" onClick="button_minus('hp', 0, 1)" onMouseDown="start_minus('hp',0,1);" onMouseUp="stopp();"></td>
    </tr>
    <tr>
      <td>MP <?php echo $char_mp[1]; ?> (1 = 5)</td>
      <td><input id="input" name="mp" type="text" value=0 size="2" readonly>
      <input id="input" type="button" name="+7" value=" + " onClick="button_plus('mp', 1)" onMouseDown="start_plus('mp',1);" onMouseUp="stopp();">
      <input id="input" type="button" name="-7" value="-" onClick="button_minus('mp', 0, 1)" onMouseDown="start_minus('mp',0,1);" onMouseUp="stopp();"></td>
    </tr>
    <tr>
      <td></td>
      <td><input id="input" type=submit value="speichern"></td>
    </tr>
  </table>
</form>
</body>
</html>
