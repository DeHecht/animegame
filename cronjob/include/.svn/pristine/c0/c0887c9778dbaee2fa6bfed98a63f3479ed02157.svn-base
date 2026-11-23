<?php
/*
 * Created on 07.03.2009
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

################### Spieler 1
if ($technick1['type'] == 'reborn') {
	$k_mp[0] = $k_mp[0] - $technick1['mp'];
	if ($k_mp[0] >= 0) {
		$reborn_hp[0] = ($k_hp1[1] / 100) * $technick1['hp'];
		$schaden_1 = 'Reborn!';
	} else {
		$schaden_1 = 'Technik fehlgeschlagen';
		$k_mp[0] = 0;
	}

	$aktion_AV1[$x] = 'nein';
}

################### Spieler 2

if ($technick2['type'] == 'reborn') {
	$k_mp[1] = $k_mp[1] - $technick2['mp'];
	if ($k_mp[1] >= 0) {
		$reborn_hp[1] = ($k_hp2[1] / 100) * $technick2['hp'];
		$schaden_2 = 'Reborn!';
	} else {
		$schaden_2 = 'Technik fehlgeschlagen';
		$k_mp[1] = 0;
	}

	$aktion_AV2[$x] = 'nein';
}

