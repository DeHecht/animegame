<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');

$daten_ende = mysqli_fetch_array(db_query("SELECT * FROM kampf_new WHERE char1='$char_id' OR char2='$char_id' LIMIT 1"));

$daten_satz = mysqli_fetch_array(db_query("SELECT id FROM kampf WHERE db_satz='$char_id' LIMIT 1"));

if($daten_satz[id]) {
	displayErrorMessage(NULL,'Sie k&ouml;nnen ihren Charakter nun abholen!', displayHistoryBackLink());
	exit;
}

$chara_1x = mysqli_fetch_array(db_query("SELECT verteidigung, speed, ausdauer, glueck, starke FROM chars WHERE id='$daten_ende[char1]' LIMIT 1"));
$chara_2x = mysqli_fetch_array(db_query("SELECT verteidigung, speed, ausdauer, glueck, starke FROM chars WHERE id='$daten_ende[char2]' LIMIT 1"));


//##################################### EXP!!!!!!!!!


if($daten_ende[lose1] != $daten_ende[char1]) {
//////////Sieger ist  Char 1 (heisst doppelte Exp fuer ihn)
$new_exp1 = round(((($chara_1x[starke] + $chara_1x[verteidigung] + $chara_1x[speed] + $chara_1x[ausdauer] + $chara_1x[glueck] + $chara_2x[starke] + $chara_2x[verteidigung] + $chara_2x[speed] + $chara_2x[ausdauer] + $chara_2x[glueck])  * 2) ) * 2);
$new_geld1 = ((($chara_1x[starke] + $chara_1x[verteidigung] + $chara_1x[speed] + $chara_1x[ausdauer] + $chara_1x[glueck] + $chara_2x[starke] + $chara_2x[verteidigung] + $chara_2x[speed] + $chara_2x[ausdauer] + $chara_2x[glueck]) / 2) ) * 2;
$new_geld1 = round($new_geld1);

$char_win = $daten_ende[char1];
$char_lose = $daten_ende[char2];

//////////Verlierer ist  Char 2
$new_exp2 = round(((($chara_1x[starke] + $chara_1x[verteidigung] + $chara_1x[speed] + $chara_1x[ausdauer] + $chara_1x[glueck] + $chara_2x[starke] + $chara_2x[verteidigung] + $chara_2x[speed] + $chara_2x[ausdauer] + $chara_2x[glueck])  * 2) ));
$new_geld2 = ((($chara_1x[starke] + $chara_1x[verteidigung] + $chara_1x[speed] + $chara_1x[ausdauer] + $chara_1x[glueck] + $chara_2x[starke] + $chara_2x[verteidigung] + $chara_2x[speed] + $chara_2x[ausdauer] + $chara_2x[glueck]) / 2) );
$new_geld2 = round($new_geld2);
} else {
//////////Sieger ist  Char 2 (heisst doppelte Exp fuer ihn)
$new_exp2 = round(((($chara_1x[starke] + $chara_1x[verteidigung] + $chara_1x[speed] + $chara_1x[ausdauer] + $chara_1x[glueck] + $chara_2x[starke] + $chara_2x[verteidigung] + $chara_2x[speed] + $chara_2x[ausdauer] + $chara_2x[glueck])  * 2) ) * 2);
$new_geld2 = ((($chara_1x[starke] + $chara_1x[verteidigung] + $chara_1x[speed] + $chara_1x[ausdauer] + $chara_1x[glueck] + $chara_2x[starke] + $chara_2x[verteidigung] + $chara_2x[speed] + $chara_2x[ausdauer] + $chara_2x[glueck]) / 2) ) * 2;
$new_geld2 = round($new_geld2);
$char_win = $daten_ende[char2];
$char_lose = $daten_ende[char1];

//////////Verlierer ist  Char 2
$new_exp1 = round(((($chara_1x[starke] + $chara_1x[verteidigung] + $chara_1x[speed] + $chara_1x[ausdauer] + $chara_1x[glueck] + $chara_2x[starke] + $chara_2x[verteidigung] + $chara_2x[speed] + $chara_2x[ausdauer] + $chara_2x[glueck])  * 2) ));
$new_geld1 = ((($chara_1x[starke] + $chara_1x[verteidigung] + $chara_1x[speed] + $chara_1x[ausdauer] + $chara_1x[glueck] + $chara_2x[starke] + $chara_2x[verteidigung] + $chara_2x[speed] + $chara_2x[ausdauer] + $chara_2x[glueck]) / 2) );
$new_geld1 = round($new_geld1);
}
include(ROOT_PATH.'/include/arena.php');


//######################EXP ENDE
$ze = 0;
$new_atks1 = explode(",", $daten_ende[atk1]);
$new_atks2 = explode(",", $daten_ende[atk2]);

while($ze < count($new_atks1)) {
$atks = mysqli_fetch_array(db_query("SELECT name FROM lernen WHERE id='$new_atks1[$ze]' LIMIT 1"));
$new_atku .=$atks[name];
if($ze<count($new_atks1)) {
$new_atku .=",";
}
$ze++;
}

$ze = 0;
while($ze < count($new_atks2)) {
$atks = mysqli_fetch_array(db_query("SELECT name FROM lernen WHERE id='$new_atks2[$ze]' LIMIT 1"));
$new_atku .=$atks[name];
if($ze<count($new_atks2)) {
$new_atku .=",";
}
$ze++;
}

db_query("INSERT kampf SET hp1='$daten_ende[hp1]', hp2='$daten_ende[hp2]', mp1='$daten_ende[mp1]', mp2='$daten_ende[mp2]',
attacke1='$daten_ende[atk1]', attacke2='$daten_ende[atk2]', schaden1='$daten_ende[schaden1]', schaden2='$daten_ende[schaden2]', char1='$daten_ende[char1]', char2='$daten_ende[char2]', starke1='$daten_ende[starke1]', starke2='$daten_ende[starke2]', ver1='$daten_ende[ver1]', ver2='$daten_ende[ver2]', speed1='$daten_ende[speed1]',speed2='$daten_ende[speed2]', ausdauer1='$daten_ende[ausdauer1]', ausdauer2='$daten_ende[ausdauer2]'
,exp1='$new_exp1', exp2='$new_exp2', geld1='$new_geld1', geld2='$new_geld2', arena_geld='$arena_einkommen', besucher='$arena_besucher', win='$char_win', lose='$char_lose', arena_name='$arena[name]', dauer='0', db_satz ='$daten_ende[char1]'");

db_query("INSERT kampf SET hp1='$daten_ende[hp1]', hp2='$daten_ende[hp2]', mp1='$daten_ende[mp1]', mp2='$daten_ende[mp2]',
attacke1='$daten_ende[atk1]', attacke2='$daten_ende[atk2]', schaden1='$daten_ende[schaden1]', schaden2='$daten_ende[schaden2]', char1='$daten_ende[char1]', char2='$daten_ende[char2]', starke1='$daten_ende[starke1]', starke2='$daten_ende[starke2]', ver1='$daten_ende[ver1]', ver2='$daten_ende[ver2]', speed1='$daten_ende[speed1]',speed2='$daten_ende[speed2]', ausdauer1='$daten_ende[ausdauer1]', ausdauer2='$daten_ende[ausdauer2]'
,exp1='$new_exp1', exp2='$new_exp2', geld1='$new_geld1', geld2='$new_geld2', arena_geld='$arena_einkommen', besucher='$arena_besucher', win='$char_win', lose='$char_lose', arena_name='$arena[name]', dauer='0', db_satz ='$daten_ende[char2]'");

db_query("DELETE FROM kampf_new1 WHERE char1='$daten_ende[char1]' AND char2='$daten_ende[char2]' LIMIT 1");
db_query("DELETE FROM kampf_new WHERE char1='$daten_ende[char1]' AND char2='$daten_ende[char2]' LIMIT 1");

db_query("UPDATE chars SET status='Kampf' WHERE id='$daten_ende[char1]' OR id='$daten_ende[char2]' LIMIT 2");

if($daten_satz['id']) {
	displayErrorMessage(NULL,'Sie k&ouml;nnen ihren Charakter nun abholen!', displayHistoryBackLink());
	exit;
}
