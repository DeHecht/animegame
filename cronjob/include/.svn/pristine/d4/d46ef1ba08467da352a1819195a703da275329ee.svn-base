<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */


####################KAMPF HP SPIELER 1
if ($technick1['type'] == "hp") {
	$k_mp[0] = $k_mp[0] - $technick1['mp'];
	if ($k_mp[0] < 0) {
		$k_mp[0] = 0;
		$schaden_1 = 'Keine MP';
		$aktion_AV1[$x] = 'nein';
	} else {
		$tmp_hp[0] = (($k_hp1[1] / 100) * $technick1['hp']);
		$tmp_hp[3] = true;
		$schaden_1 = '+' . $technick1['hp'] .'% HP';
		$aktion_AV1[$x] = 'nein';
	}
}

####################KAMPF HP SPIELER 2
if ($technick2['type'] == 'hp') {
	$k_mp[1] = $k_mp[1] - $technick2['mp'];
	if ($k_mp[1] < 0) {
		$k_mp[1] = 0;
		$schaden_2 = 'Keine MP';
		$aktion_AV2[$x] = 'nein';
	} else {
		$tmp_hp[1] = (($k_hp2[1] / 100) * $technick2['hp']);
		$tmp_hp[2] = true;
		$schaden_2 = '+' . $technick2['hp'] .'% HP';
		$aktion_AV2[$x] = 'nein';
	}
}
?>