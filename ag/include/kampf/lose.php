<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

####################KAIOKEN-LOSE SPIELER 1

if($technick1['type'] == 'lose') {
$k_mp[0] = $k_mp[0] - $technick1['mp'];
if($k_mp[0] < 0) { 
$schaden_2 = 'Keinen Schaden';
$schaden_1 = 'Schw&auml;chen fehlgeschlagen';
$aktion_AV1[$x] = 'nein';
$k_mp[0] = 0; 
} else {
if($kampf_info['rasse'] != 'NPC') {			//// BEI NPC ATTACKEN GESPERRT


	$schaden_1 = 'Schw&auml;chen';
	$k_hp[0] = $k_hp[0] - (($k_hp1[1] / 100) * $technick1['hp']);
	if($k_hp[0]< 1) { $k_hp[0] = 0; }

	$k_starke[1] = $k_starke[1] - (($k_starke[1] / 100) * $technick1['starke']); 
	$k_speed[1] = $k_speed[1] - (($k_speed[1] / 100) * $technick1['speed']);
	$k_ver[1] = $k_ver[1] - (($k_ver[1] / 100) * $technick1['verteidigung']);
}
$aktion_AV1[$x] = 'nein';
}}
####################KAIOKEN-LOSE SPIELER 2

if($technick2['type'] == 'lose') {
$k_mp[1] = $k_mp[1] - $technick2['mp'];
if($k_mp[1] < 0) { 
$schaden_1 = 'Keinen Schaden';
$schaden_2 = 'Schw&auml;chen fehlgeschlagen';
$aktion_AV2[$x] = 'nein';
$k_mp[1] = 0; 
} else {
$k_starke[0] = $k_starke[0] - (($k_starke[0] / 100) * $technick2['starke']); 
$k_speed[0] = $k_speed[0] - (($k_speed[0] / 100) * $technick2['speed']);
$k_ver[0] = $k_ver[0] - (($k_ver[0] / 100) * $technick2['verteidigung']);

$k_hp[1] = $k_hp[1] - (($k_hp2[1] / 100) * $technick2['hp']);
if($k_hp[0]< 1) { $k_hp[0] = 0; }

$aktion_AV2[$x] = 'nein';
$schaden_2 = 'Schw&auml;chen';
}}

?>