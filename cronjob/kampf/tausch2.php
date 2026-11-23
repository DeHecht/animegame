<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH. "/include/random.inc.php");

################### Spieler 1

if($technick1[type] == "tausch2") {
$k_mp[0] = $k_mp[0] - $technick1[mp];
if($k_mp[0] >= 0) {

$prozent_technik1 = 0;
$technick1 = mysqli_fetch_array(db_query("SELECT * FROM attacken WHERE id='$technick2[id]' LIMIT 10"));


$technick1[speed]=$technick1[speed]+(($technick1[speed]/100)*$prozent_technik1);
$technick1[verteidigung]=$technick1[verteidigung]+(($technick1[verteidigung]/100)*$prozent_technik1);
$technick1[starke]=$technick1[starke]+(($technick1[starke]/100)*$prozent_technik1);
$speeds_char1 = ($k_speed[0] + $technick1[speed]);
$speeds_char1_4 = ($k_speed[0] + $technick1[speed]) / 1.5;
if($speeds_char1_4 < 1) { $speeds_char1_4 = 1; } 
$speed_char1 = mt_random_wrapper($speeds_char1_4,$speeds_char1);


} else {
$k_mp[0] = $k_mp[0] - $technick1[mp];
$schaden_1 = "Techniktausch Fehlgeschlagen";
$aktion_AV1[$x] = "nein";
}

}

################### Spieler 2

if($technick2[type] == "tausch2") {
$k_mp[1] = $k_mp[1] - $technick2[mp];
if($k_mp[1] >= 0) {

$prozent_technik2 = 0;
$technick2 = mysqli_fetch_array(db_query("SELECT * FROM attacken WHERE id='$technick1[id]' LIMIT 10"));

$technick2[speed]=$technick2[speed]+(($technick2[speed]/100)*$prozent_technik2);
$technick2[verteidigung]=$technick2[verteidigung]+(($technick2[verteidigung]/100)*$prozent_technik2);
$technick2[starke]=$technick2[starke]+(($technick2[starke]/100)*$prozent_technik2);
$speeds_char2 =  ($k_speed[1] + $technick2[speed]);
$speeds_char2_4 =  ($k_speed[1] + $technick2[speed]) / 1.5;
if($speeds_char2_4 < 1) { $speeds_char2_4 = 1; } 
$speed_char2 = mt_random_wrapper($speeds_char2_4,$speeds_char2);


} else {
$k_mp[0] = $k_mp[0] - $technick1[mp];
$schaden_2 = "Techniktausch Fehlgeschlagen";
$aktion_AV2[$x] = "nein";
}

}
