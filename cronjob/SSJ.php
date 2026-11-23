<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
####################SSJ SPIELER 1

if($technick1[type] == SSJ) {
$k_mp[0] = $k_mp[0] - $technick1[mp];
if($k_mp[0] < 0) { 
$schaden_1 = "SSJ Verwandlung fehlgeschlagen";
$aktion_AV1[$x] = "nein";
$k_mp[0] = 0; 
} else {

if(!$ssj_tech_1) {

$k_starke[0] = $k_starke[0] * (($technick1[starke]+1) / 2); 
$k_speed[0] = $k_speed[0] * (($technick1[speed]+1) / 2);
$k_ver[0] = $k_ver[0] * (($technick1[verteidigung]+1) / 2);

$schaden_1 = "$technick1[name]!!!";
$aktion_AV1[$x] = "nein";
$ssj_tech_1=$technick1[level];
}

}}


####################SSJ SPIELER 2

if($technick2[type] == SSJ) {
$k_mp[1] = $k_mp[1] - $technick2[mp];
if($k_mp[1] < 0) { 
$schaden_2 = "SSJ Verwandlung fehlgeschlagen";
$aktion_AV2[$x] = "nein";
$k_mp[1] = 0; 
} else {

if(!$ssj_tech_2) {
$k_starke[1] = $k_starke[1] * (($technick2[starke]+1) / 2); 
$k_speed[1] = $k_speed[1] * (($technick2[speed]+1) / 2);
$k_ver[1] = $k_ver[1] * (($technick2[verteidigung]+1) / 2);

$schaden_2 = "$technick2[name]!!!";

$aktion_AV2[$x] = "nein";
$ssj_tech_2=$technick2[level];
}}}
?>