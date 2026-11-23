<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

####################KAMPF HPMP SPIELER 1
if($technick1['type'] == "hpmp") {

	$tmp_hp[0] = (($k_hp1[1] / 100) * $technick1['hp']);
	$tmp_hp[3] = true;
	$k_mp[0] = $k_mp[0] + (($k_mp1[1] / 100) * $technick1['mp']);

	if($k_mp1[1] < $k_mp[0]) {
		$k_mp[0] = $k_mp1[1];
	}

	$schaden_1 = "+ $technick1[hp]% HP/ $technick1[mp]% MP";
	$aktion_AV1[$x] = "nein";
}



####################KAMPF HPMP SPIELER 2

if($technick2['type'] == "hpmp") {

	$tmp_hp[1] = (($k_hp2[1] / 100) * $technick2['hp']);
	$tmp_hp[2] = true;
	$k_mp[1] = $k_mp[1] + (($k_mp2[1] / 100) * $technick2['mp']);

	if($k_mp2[1] < $k_mp[1]) {
		$k_mp[1] = $k_mp2[1];
	}

	$schaden_2 = "+ $technick2[hp]% HP/ $technick2[mp]% MP";
	$aktion_AV2[$x] = "nein";
}

?>
