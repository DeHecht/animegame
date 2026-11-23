<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include "db.php";
include('path.inc.php');
include_once (ROOT_PATH.'/include/sqlwrapper.inc.php');

include_once(ROOT_PATH. "/include/random.inc.php");

$mak_time = time();
$z_time = time() - 86400;
$chars_k = db_query("SELECT charakter FROM kampf_list WHERE zeit < '$mak_time' and rasse != 'NPC'");
while ($row4 = mysqli_fetch_array($chars_k)) {
	db_query("UPDATE chars SET status='Frei' WHERE id='$row4[charakter]'");
}

db_query("DELETE FROM kampf_list WHERE zeit < '$mak_time' and rasse != 'NPC'");
//db_query("DELETE FROM user WHERE aktiv = 'nein' AND code_zeit < '$z_time'");
db_query("DELETE FROM top_kampf");

$platz1 = db_query("SELECT id, besitzer FROM chars WHERE level<'150' order by preis DESC LIMIT 3");
$x = 0;
$mac_geld = 3000;
while ($row = mysqli_fetch_array($platz1)) {
	$user = mysqli_fetch_array(db_query("SELECT geld FROM user WHERE id='$row[besitzer]' LIMIT 3"));
	$new_geld = $user[geld] + $mac_geld;
	db_query("UPDATE user SET geld='$new_geld' WHERE id='$row[besitzer]'");
	$mac_geld = $mac_geld -1000;
	$x++;
}

$row = "";
$platz1 = db_query("SELECT id, anzahl, preis FROM item");
$x = 0;
while ($row = mysqli_fetch_array($platz1)) {
	if ($row[preis] >= 200000) {
		$anzahl = 1;
	}
	if ($row[preis] < 200000) {
		$anzahl = 5;
	}
	if ($row[preis] < 100000) {
		$anzahl = 10;
	}
	if ($row[preis] < 50000) {
		$anzahl = 20;
	}
	if ($row[preis] < 30000) {
		$anzahl = 50;
	}
	if ($row[preis] < 20000) {
		$anzahl = 60;
	}
	if ($row[preis] < 15000) {
		$anzahl = 75;
	}
	if ($row[preis] < 10000) {
		$anzahl = 100;
	}
	if ($row[preis] < 7000) {
		$anzahl = 150;
	}
	if ($row[preis] < 5000) {
		$anzahl = 200;
	}
	if ($row[preis] < 2000) {
		$anzahl = 500;
	}
	if ($row[preis] < 1000) {
		$anzahl = 1000;
	}
	if ($row[preis] < 500) {
		$anzahl = 1500;
	}
	if ($row[preis] < 80) {
		$anzahl = 5000;
	}
	db_query("UPDATE item SET anzahl='$anzahl' WHERE id='$row[id]'");

	$x++;
}

$row = "";

db_query("DELETE FROM liga_kampf");
db_query("DELETE FROM wanted_kampf");
db_query("UPDATE chars SET status='Frei' WHERE status='Tod'");
db_query("UPDATE user SET pw_fight='4'");

//////////////////////////////////NPCS!!!!

$wahl = "starke+verteidigung+speed+ausdauer+glueck";
$row = mysqli_fetch_array(db_query("SELECT name, level, starke, speed, verteidigung, ausdauer, glueck, hp, mp FROM chars WHERE level<'150' AND rasse!='NPC' ORDER BY ($wahl) DESC LIMIT 1"));

$new_hp = explode(",", $row[hp]);
$new_mp = explode(",", $row[mp]);

$beste_mp = (($new_mp[1] / 100) * mt_random_wrapper(75, 130)) + 1000;
$beste_hp = (($new_hp[1] / 100) * mt_random_wrapper(80, 150)) + 3000;

$beste_starke = ($row[starke] / 100) * mt_random_wrapper(75, 130);
$beste_speed = ($row[speed] / 100) * mt_random_wrapper(75, 110);
$beste_verteidigung = ($row[verteidigung] / 100) * mt_random_wrapper(75, 110);
$beste_ausdauer = ($row[ausdauer] / 100) * mt_random_wrapper(75, 130);
$beste_glueck = ($row[glueck] / 100) * mt_random_wrapper(75, 130);

db_query("UPDATE chars SET hp='$beste_hp,$beste_hp', mp='$beste_mp,$beste_mp', starke='$beste_starke', speed='$beste_speed', verteidigung='$beste_verteidigung', ausdauer='$beste_ausdauer', glueck='$beste_glueck' WHERE id='$brolly' LIMIT 1");

//////////////////////////////////NPCS!!!!

$row = mysqli_fetch_array(db_query("SELECT name, level, starke, speed, verteidigung, ausdauer, glueck, hp, mp FROM chars WHERE level<'150' AND rasse!='NPC' ORDER BY starke DESC LIMIT 1"));

$new_hp = explode(",", $row[hp]);
$new_mp = explode(",", $row[mp]);

$beste_mp = (($new_mp[1] / 100) * mt_random_wrapper(100, 150)) + 1000;
$beste_hp = (($new_hp[1] / 100) * mt_random_wrapper(120, 220)) + 3000;

$beste_starke = ($row[starke] / 100) * mt_random_wrapper(60, 110);
$beste_speed = ($row[speed] / 100) * mt_random_wrapper(60, 100);
$beste_verteidigung = ($row[verteidigung] / 100) * mt_random_wrapper(100, 150);
$beste_ausdauer = ($row[ausdauer] / 100) * mt_random_wrapper(100, 200);
$beste_glueck = ($row[glueck] / 100) * mt_random_wrapper(80, 100);

db_query("UPDATE chars SET hp='$beste_hp,$beste_hp', mp='$beste_mp,$beste_mp', starke='$beste_starke', speed='$beste_speed', verteidigung='$beste_verteidigung', ausdauer='$beste_ausdauer', glueck='$beste_glueck' WHERE id='$bebi' LIMIT 1");

//////////////////NPC
$wahl = "starke+verteidigung+speed+ausdauer+glueck";
$row = mysqli_fetch_array(db_query("SELECT hp, mp, name, level, starke, speed, verteidigung, ausdauer, glueck FROM chars WHERE level<'150' AND rasse!='NPC' ORDER BY level DESC LIMIT 1"));

$new_hp = explode(",", $row[hp]);
$new_mp = explode(",", $row[mp]);

$beste_mp = (($new_mp[1] / 100) * mt_random_wrapper(150, 200)) + 1000;
$beste_hp = (($new_hp[1] / 100) * mt_random_wrapper(100, 200)) + 3000;

$beste_starke = ($row[starke] / 100) * mt_random_wrapper(75, 120);
$beste_speed = ($row[speed] / 100) * mt_random_wrapper(75, 120);
$beste_verteidigung = ($row[verteidigung] / 100) * mt_random_wrapper(80, 100);
$beste_ausdauer = ($row[ausdauer] / 100) * mt_random_wrapper(60, 120);
$beste_glueck = ($row[glueck] / 100) * mt_random_wrapper(60, 200);

db_query("UPDATE chars SET hp='$beste_hp,$beste_hp', mp='$beste_mp,$beste_mp', starke='$beste_starke', speed='$beste_speed', verteidigung='$beste_verteidigung', ausdauer='$beste_ausdauer', glueck='$beste_glueck' WHERE id='$flamingo' LIMIT 1");

//////////////////NPC
$row = mysqli_fetch_array(db_query("SELECT hp, mp, name, level, starke, speed, verteidigung, ausdauer, glueck FROM chars WHERE level<'150' AND rasse!='NPC' ORDER BY speed DESC LIMIT 1"));

$new_hp = explode(",", $row[hp]);
$new_mp = explode(",", $row[mp]);

$beste_mp = (($new_mp[1] / 100) * mt_random_wrapper(80, 110)) + 1000;
$beste_hp = (($new_hp[1] / 100) * mt_random_wrapper(80, 250)) + 3000;

$beste_starke = ($row[starke] / 100) * mt_random_wrapper(80, 120);
$beste_speed = ($row[speed] / 100) * mt_random_wrapper(80, 130);
$beste_verteidigung = ($row[verteidigung] / 100) * mt_random_wrapper(80, 120);
$beste_ausdauer = ($row[ausdauer] / 100) * mt_random_wrapper(80, 150);
$beste_glueck = ($row[glueck] / 100) * mt_random_wrapper(90, 100);

db_query("UPDATE chars SET hp='$beste_hp,$beste_hp', mp='$beste_mp,$beste_mp', starke='$beste_starke', speed='$beste_speed', verteidigung='$beste_verteidigung', ausdauer='$beste_ausdauer', glueck='$beste_glueck' WHERE id='$mihawk' LIMIT 1");
?>
