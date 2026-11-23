<?php
/*
 * Created on 07.03.2009
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

################### Spieler 1

if ($technick1['type'] == 'copy') {
	$k_mp[0] = $k_mp[0] - $technick1['mp'];
	if ($k_mp[0] >= 0) {
		$schaden_1 = 'Power Kopieren!';
                // addiere str des gegners %ual zur eigenen hinzu
		$k_starke[0] += $k_starke[1] * $technick1['starke'] / 100;
		// addiere nun spd des gegners %ual zur eigenen hinzu
		$k_speed[0] += $k_speed[1] * $technick1['speed'] / 100;
		// letztendlich tue das gleiche fuer die def
		$k_ver[0] += $k_ver[1] * $technick1['verteidigung'] / 100;
	} else {
		$schaden_1 = 'Kopieren Fehlgeschlagen';
		$k_mp[0] = 0;
	}

	$aktion_AV1[$x] = 'nein';
}

################### Spieler 2

if ($technick2['type'] == 'copy') {
	$k_mp[1] = $k_mp[1] - $technick2['mp'];

	if ($k_mp[1] >= 0) {
                // addiere str des gegners %ual zur eigenen hinzu
		$k_starke[1] += $k_starke[0] * $technick2['starke'] / 100;
		// addiere nun spd des gegners %ual zur eigenen hinzu
		$k_speed[1] += $k_speed[0] * $technick2['speed'] / 100;
		// letztendlich tue das gleiche fuer die def
		$k_ver[1] += $k_ver[0] * $technick2['verteidigung'] / 100;
		$schaden_2 = 'Power Kopieren!';
	} else {
		$schaden_2 = 'Kopieren Fehlgeschlagen';
		$k_mp[1] = 0;
	}

	$aktion_AV2[$x] = 'nein';
}
