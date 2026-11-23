<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

################### Spieler 1

if ($technick1[type] == copy) {
	$k_mp[0] = $k_mp[0] - $technick1[mp];
	if ($k_mp[0] >= 0) {

		if ($technick1[starke] == 1) { /////Staerke wird kopiert
			$k_starke[0] = $k_starke[0] + $k_starke[1];
		}
		if ($technick1[speed] == 1) { /////Staerke wird kopiert
			$k_speed[0] = $k_speed[0] + $k_speed[1];
		}
		if ($technick1[verteidigung] == 1) { /////Staerke wird kopiert
			$k_ver[0] = $k_ver[0] + $k_ver[1];
		}
		$schaden_1 = "Power Kopiern!";
	} else {
		$schaden_1 = "Kopiern Fehlgeschlagen";
		$k_mp[0] = 0;
	}

	$aktion_AV1[$x] = "nein";
}

################### Spieler 2

if ($technick2[type] == copy) {
	$k_mp[1] = $k_mp[1] - $technick2[mp];

	if ($k_mp[1] >= 0) {

		if ($technick2[starke] == 1) { /////Staerke wird kopiert
			$k_starke[1] = $k_starke[0] + $k_starke[1];
		}
		if ($technick2[speed] == 1) { /////Staerke wird kopiert
			$k_speed[1] = $k_speed[0] + $k_speed[1];
		}
		if ($technick2[verteidigung] == 1) { /////Staerke wird kopiert
			$k_ver[1] = $k_ver[0] + $k_ver[1];
		}
		$schaden_2 = "Power Kopiern!";
	} else {
		$schaden_2 = "Kopiern Fehlgeschlagen";
		$k_mp[1] = 0;
	}

	$aktion_AV2[$x] = "nein";
}