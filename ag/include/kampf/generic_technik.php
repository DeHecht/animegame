<?php
/*
 * Created on 07.03.2009
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

if (!function_exists('getTechniqueAttributeName')) {
	function getTechniqueAttributeName($attribute) {
		switch ($attribute) {
			case 's_hp_cur':
			case 'e_hp_cur':
			case 's_hp_max':
			case 'e_hp_max':
				return 'hp';
			case 's_mp_cur':
			case 'e_mp_cur':
			case 's_mp_max':
			case 'e_mp_max':
				return 'mp';
			case 's_str':
			case 'e_str':
				return 'starke';
			case 's_def':
			case 'e_def':
				return 'verteidigung';
			case 's_spd':
			case 'e_spd':
				return 'speed';
			default:
				return '';
		}
	}
}

################### Spieler 1

if ($technick1['type'] == 'generic') {
	$k_mp[0] = $k_mp[0] - $technick1['mp'];
	if ($k_mp[0] >= 0) {
		$options = json_decode($technick1['options'], true);
		$statChange_Char1[] = $options;

		foreach ($statChange_Char1 as &$attacke) {
			foreach ($attacke as &$stat) {
				if(is_array($stat)) {
					$stat['maxdauer'] = $stat['dauer'];
					$stat['wert'] = $technick1[getTechniqueAttributeName($stat['attribut'])];
				}
			}
		}

	} else {
		$schaden_1 = 'Technik Fehlgeschlagen';
		$k_mp[0] = 0;
	}

	$aktion_AV1[$x] = 'nein';
}

################### Spieler 2

if ($technick2['type'] == 'generic') {
	$k_mp[1] = $k_mp[1] - $technick2['mp'];

	if ($k_mp[1] >= 0) {
		$options = json_decode($technick2['options'], true);
		$statChange_Char2[] = $options;

		foreach ($statChange_Char2 as &$attacke) {
			foreach ($attacke as &$stat) {
				if(is_array($stat)) {
					$stat['maxdauer'] = $stat['dauer'];
					$stat['wert'] = $technick2[getTechniqueAttributeName($stat['attribut'])];
				}
			}
		}

	} else {
		$schaden_2 = 'Technik Fehlgeschlagen';
		$k_mp[1] = 0;
	}

	$aktion_AV2[$x] = 'nein';
}
