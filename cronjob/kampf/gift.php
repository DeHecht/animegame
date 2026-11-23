<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
####################GIFT SPIELER 1

if($runden_gif1) {
	if($runden_gif1 > $x) {
		$technick1_old = mysqli_fetch_array(db_query("SELECT * FROM attacken WHERE id='$attacken_list[$runden_gif_technik1]' LIMIT 1"));

		$k_starke[1] = $k_starke[1] - $technick1_old[starke];
		$k_speed[1] = $k_speed[1] - $technick1_old[speed];
		$k_ver[1] = $k_ver[1] - $technick1_old[verteidigung];
		$k_hp[1] = $k_hp[1] - $technick1_old[hp];

  } else {
     $runden_gif1 = "";
     $runden_gif_technik1 = "";
  }

}


if($technick1[type] == "gift") {

$k_mp[0] = $k_mp[0] - $technick1[mp];
if($k_mp[0] < 0) {
$schaden_2 = "Keinen Schaden";
$schaden_1 = "Sch&auml;digen fehlgeschlagen";
$aktion_AV1[$x] = "nein";
$k_mp[0] = 0;
} else {

$schaden_1 = "Sch&auml;digen";

$runden_gif1 = $x+$technick1[runden];
$runden_gif_technik1 = $x;

$k_starke[1] = $k_starke[1] - $technick1[starke];
$k_speed[1] = $k_speed[1] - $technick1[speed];
$k_ver[1] = $k_ver[1] - $technick1[verteidigung];
$k_hp[1] = $k_hp[1] - $technick1[hp];

$aktion_AV1[$x] = "nein";
}}

####################GIFT SPIELER 2

if($runden_gif2) {
	if($runden_gif2 > $x) {
		$technick2_old = mysqli_fetch_array(db_query("SELECT * FROM attacken WHERE id='$attacken_list[$runden_gif_technik2]' LIMIT 1"));

		$k_starke[0] = $k_starke[0] - $technick2_old[starke];
		$k_speed[0] = $k_speed[0] - $technick2_old[speed];
		$k_ver[0] = $k_ver[0] - $technick2_old[verteidigung];
		$k_hp[0] = $k_hp[0] - $technick2_old[hp];
  } else {
     $runden_gif2 = "";
     $runden_gif_technik2 = "";
  }

}


if($technick2[type] == "gift") {

$k_mp[1] = $k_mp[1] - $technick2[mp];

if($k_mp[1] < 0) {
$schaden_1 = "Keinen Schaden";
$schaden_2 = "Sch&auml;digen fehlgeschlagen";
$aktion_AV2[$x] = "nein";
$k_mp[1] = 0;
} else {


$schaden_2 = "Sch&auml;digen";

$runden_gif2 = $x+$technick2[runden];
$runden_gif_technik2 = $x;

$k_starke[0] = $k_starke[0] - $technick2[starke];
$k_speed[0] = $k_speed[0] - $technick2[speed];
$k_ver[0] = $k_ver[0] - $technick2[verteidigung];
$k_hp[0] = $k_hp[0] - $technick2[hp];

$aktion_AV2[$x] = "nein";
}}








