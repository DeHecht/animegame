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
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');

//nur zum test
// GET-Section
// Kritisch (SQL-Injections)
$spleoic = validateString($_GET['spleoic']);
$kampf_id = validateUnsignedInteger($_GET['kampf_id'], null);

?>
<html>
	<body bgcolor="#202020">
<?php

if(!$spleoic) { $spleoic = "top_kampf";
} else {
echo '<p align=center><a href="javascript:window.close();">Schlie&szlig;en</a><br>&nbsp;<br>';
$kampf1 = mysqli_fetch_array(db_query("SELECT * FROM $spleoic WHERE id='$kampf_id' LIMIT 1"));
/*if(($kampf_id * 300) + $kampf1[dauer] > time()) {
echo "Fehler Sie k&ouml;nnen diesen Kampf nicht anschauen bevor er vorbei ist";
exit;
}*/
}

$kampf1 = mysqli_fetch_array(db_query("SELECT * FROM $spleoic WHERE id='$kampf_id' LIMIT 1"));
$char_1 = getChar2($kampf1['char1']);
$char_2 = getChar2($kampf1['char2']);

if($kampf1['dauer'] > time()) {
	displayErrorMessage(NULL,'Der Kampf ist noch nicht vorbei!', displayHistoryBackLink());
	exit;
}


$starke1 = explode(",", $kampf1['starke1']);
$starke2 = explode(",", $kampf1['starke2']);
$ver1 = explode(",", $kampf1['ver1']);
$ver2 = explode(",", $kampf1['ver2']);
$speed1 = explode(",", $kampf1['speed1']);
$speed2 = explode(",", $kampf1['speed2']);
$ausdauer1 = explode(",", $kampf1['ausdauer1']);
$ausdauer2 = explode(",", $kampf1['ausdauer2']);

$schaden1 = explode(",", $kampf1['schaden1']);
$schaden2 = explode(",", $kampf1['schaden2']);
$attacke1 = explode(",", $kampf1['attacke1']);
$attacke2 = explode(",", $kampf1['attacke2']);

$hp1 = explode(",", $kampf1['hp1']);
$hp2 = explode(",", $kampf1['hp2']);
$mp1 = explode(",", $kampf1['mp1']);
$mp2 = explode(",", $kampf1['mp2']);

$a_hp1 = explode(",", $char_1['hp']);
$a_hp2 = explode(",", $char_2['hp']);
$a_mp1 = explode(",", $char_1['mp']);
$a_mp2 = explode(",", $char_2['mp']);
$runde = 1;

include(ROOT_PATH.'//include/kampf_anzeige.php');
