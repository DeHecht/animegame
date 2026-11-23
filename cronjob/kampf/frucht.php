<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

####################KAIOKEN SPIELER 1

if($technick1[type] == "frucht") {
$frucht_stats1 = mysqli_fetch_array(db_query("SELECT starke, ver, speed, ausdauer, glueck FROM wochen_markt WHERE item='$chara_1[frucht]' LIMIT 1"));
$k_mp[0] = $k_mp[0] - $technick1[mp];
$k_hp[0] = $k_hp[0] - $technick1[hp];
if($k_mp[0] < 0) { 
$schaden_1 = "Technik fehlgeschlagen";
$aktion_AV1[$x] = "nein";
$k_mp[0] = 0; 
} else {

$schaden_1 = "Zauber Frucht";
$k_ausdauer[0] = $k_ausdauer[0] + $frucht_stats1[ausdauer];
$k_starke[0] = $k_starke[0] + ($frucht_stats1[starke]*$technick1[starke]);
$k_speed[0] = $k_speed[0] + ($frucht_stats1[speed]*$technick1[speed]);
$k_ver[0] = $k_ver[0] + ($frucht_stats1[ver]*$technick1[verteidigung]);
$k_glueck[0] = $k_glueck[0] + $frucht_stats1[glueck];
$aktion_AV1[$x] = "nein";
}}
####################KAIOKEN SPIELER 2

if($technick2[type] == "frucht") {
$frucht_stats2 = mysqli_fetch_array(db_query("SELECT starke, ver, speed, ausdauer, glueck FROM wochen_markt WHERE item='$chara_2[frucht]' LIMIT 1"));
$k_mp[1] = $k_mp[1] - $technick2[mp];
$k_hp[1] = $k_hp[1] - $technick2[hp];
if($k_mp[1] < 0) { 
$schaden_2 = "Technik fehlgeschlagen";
$aktion_AV2[$x] = "nein";
$k_mp[1] = 0; 
} else {

$schaden_2 = "Zauber Frucht";
$k_ausdauer[1] = $k_ausdauer[1] + $frucht_stats2[ausdauer];
$k_starke[1] = $k_starke[1] + ($frucht_stats2[starke]*$technick2[starke]);
$k_speed[1] = $k_speed[1] + ($frucht_stats2[speed]*$technick2[speed]);
$k_ver[1] = $k_ver[1] + ($frucht_stats2[ver]*$technick2[verteidigung]);
$k_glueck[1] = $k_glueck[1] + $frucht_stats2[glueck];
$aktion_AV2[$x] = "nein";
}}
