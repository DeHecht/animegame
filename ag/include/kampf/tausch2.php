<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

################### Spieler 1
include_once(ROOT_PATH.'/include/random.inc.php');

if ($technick1['type'] == 'tausch2') {
	$k_mp[0] = $k_mp[0] - $technick1['mp'];
	if ($k_mp[0] >= 0) {
		$prozent_technik1 = 0;
		if ($technick2['type'] == 'SSJ' && $ssj_tech_1) {
			// Gegner setzt SSJ ein ... der aktuelle char hat aber schon eine SSJ eingesetzt!
			$schaden_1 = 'Doppelte SSJ (+10%)';
			$k_starke[0] = round($k_starke[0] * 1.1);
		        $k_speed[0] = round($k_speed[0] * 1.1);
	            	$k_ver[0] = round($k_ver[0] * 1.1);
			$aktion_AV1[$x] = 'nein';
		} else {
			$technick1 = mysqli_fetch_array(db_query("SELECT * FROM attacken WHERE id='$technick2[id]' LIMIT 10"));
			$technick1['speed'] = $technick1['speed'] + (($technick1['speed'] / 100) * $prozent_technik1);
			$technick1['verteidigung'] = $technick1['verteidigung'] + (($technick1['verteidigung'] / 100) * $prozent_technik1);
			$technick1['starke'] = $technick1['starke'] + (($technick1['starke'] / 100) * $prozent_technik1);
			$speeds_char1 = ($k_speed[0] + $technick1['speed']);
			$speeds_char1_4 = ($k_speed[0] + $technick1['speed']) / 1.5;
			if ($speeds_char1_4 < 1) {
				$speeds_char1_4 = 1;
			}
			$speeds_char1 = mt_random_wrapper($speeds_char1_4, $speeds_char1);
		}

	} else {
		$k_mp[0] = 0;
		$schaden_1 = 'Techniktausch Fehlgeschlagen';
		$aktion_AV1[$x] = 'nein';
	}

}

################### Spieler 2

if ($technick2['type'] == 'tausch2') {
	$k_mp[1] = $k_mp[1] - $technick2['mp'];
	if ($k_mp[1] >= 0) {
		$prozent_technik2 = 0;
		if ($technick1['type'] == 'SSJ' && $ssj_tech_2) {
			// Gegner setzt SSJ ein ... der aktuelle char hat aber schon eine SSJ eingesetzt!
			$schaden_2 = 'Doppelte SSJ (+10%)';
			$k_starke[1] = round($k_starke[1] * 1.1);
		        $k_speed[1] = round($k_speed[1] * 1.1);
	            	$k_ver[1] = round($k_ver[1] * 1.1);
			$aktion_AV2[$x] = 'nein';
		} else {
			$technick2 = mysqli_fetch_array(db_query("SELECT * FROM attacken WHERE id='$technick1[id]' LIMIT 10"));

			$technick2['speed'] = $technick2['speed'] + (($technick2['speed'] / 100) * $prozent_technik2);
			$technick2['verteidigung'] = $technick2['verteidigung'] + (($technick2['verteidigung'] / 100) * $prozent_technik2);
			$technick2['starke'] = $technick2['starke'] + (($technick2['starke'] / 100) * $prozent_technik2);
			$speeds_char2 = ($k_speed[1] + $technick2['speed']);
			$speeds_char2_4 = ($k_speed[1] + $technick2['speed']) / 1.5;
			if ($speeds_char2_4 < 1) {
				$speeds_char2_4 = 1;
			}
			$speeds_char2 = mt_random_wrapper($speeds_char2_4, $speeds_char2);
		}
	} else {
		$k_mp[1] = 0;
		$schaden_2 = 'Techniktausch Fehlgeschlagen';
		$aktion_AV2[$x] = 'nein';
	}

}
