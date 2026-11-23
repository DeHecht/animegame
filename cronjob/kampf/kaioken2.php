<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
####################KAIOKEN SPIELER 1

if($technick1[type] == "kaioken2") {

$k_mp[0] = ($k_mp1[1] / 100) * (100-$technick1[mp]);
$k_hp[0] = ($k_hp1[1] / 100) * (100-$technick1[hp]);

$schaden_1 = "Super Kaioken";
$k_starke[0] = $k_starke[0] + (($k_starke[0] / 100) * $technick1[starke]); 
$k_speed[0] = $k_speed[0] + (($k_speed[0] / 100) * $technick1[speed]); 
$k_ver[0] = $k_ver[0] + (($k_ver[0] / 100) * $technick1[verteidigung]); 
$aktion_AV1[$x] = "nein";
}
####################KAIOKEN SPIELER 2

if($technick2[type] == "kaioken2") {

$k_mp[1] = ($k_mp2[1] / 100) * (100-$technick2[mp]);
$k_hp[1] = ($k_hp2[1] / 100) * (100-$technick2[hp]);

$schaden_2 = "Super Kaioken";
$k_starke[1] = $k_starke[1] + (($k_starke[1] / 100) * $technick2[starke]); 
$k_speed[1] = $k_speed[1] + (($k_speed[1] / 100) * $technick2[speed]); 
$k_ver[1] = $k_ver[1] + (($k_ver[1] / 100) * $technick2[verteidigung]); 
$aktion_AV2[$x] = "nein";
}

