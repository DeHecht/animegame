<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once(ROOT_PATH.'/include/random.inc.php');

if (!function_exists('getKTChance')) {
	
	if(!function_exists('calculateChance')) {
		
		function calculateChance($char1_luck, $char2_luck) {
			$max = max($char1_luck, $char2_luck);
			$min = min($char1_luck, $char2_luck);
			if($max == 0) {
				$max = 1;
			}
			return (($max - $min) / $max) * 50;
		}
	}
	
	function getKTChance($ktuser_luck, $ktvictim_luck) {
		//bei gleichem glück ist die chance 50%
		$chance = 50;
		if($ktuser_luck >= $ktvictim_luck) {
			//hat der angreifer mehr glück wird der errechnete wert auf die 50% addiert
			$chance += calculateChance($ktuser_luck, $ktvictim_luck); 
		} else {
			//hat der verteidiger mehr glück wird der errechnete wert auf die 50% subtrahiert
			$chance -= calculateChance($ktuser_luck, $ktvictim_luck);
		}
		return $chance;
	}	
}

################### Spieler 1
$char1_luck = mt_random_wrapper(0, $k_glueck[0]);
$char2_luck = mt_random_wrapper(0, $k_glueck[1]);
$chance = round(getKTChance($char1_luck, $char2_luck));
$random = mt_random_wrapper(0,100);
	
if ($technick1['type'] == 'tausch') {
	if ($k_mp[0] >= $technick1['mp']) {
		if($random <= $chance) { //Trefferchance abhängig vom ausgewürfeltem Glück
			if ($kampf_info['rasse'] != 'NPC') { ///BEI NPCS WIRD KOERPER TAUSCH GEBLOCKT
				$k_starke_alt[0] = $k_starke[0];
				$k_speed_alt[0] = $k_speed[0];
				$k_ver_alt[0] = $k_ver[0];
				$k_ausdauer_alt[0] = $k_ausdauer[0];
				$k_hp_alt[0] = $k_hp[0];

				$k_starke_alt[1] = $k_starke[1];
				$k_speed_alt[1] = $k_speed[1];
				$k_ver_alt[1] = $k_ver[1];
				$k_ausdauer_alt[1] = $k_ausdauer[1];
				$k_hp_alt[1] = $k_hp[1];

				$k_starke[0] = $k_starke_alt[1];
				$k_speed[0] = $k_speed_alt[1];
				$k_ver[0] = $k_ver_alt[1];
				$k_ausdauer[0] = $k_ausdauer_alt[1];
				$k_hp[0] = $k_hp_alt[1];

				$k_starke[1] = $k_starke_alt[0];
				$k_speed[1] = $k_speed_alt[0];
				$k_ver[1] = $k_ver_alt[0];
				$k_ausdauer[1] = $k_ausdauer_alt[0];
				$k_hp[1] = $k_hp_alt[0];

				$k_mp[0] = $k_mp[0] - $technick1['mp'];
				$schaden_1 = 'Chance: '.$chance.'%<br>K&ouml;rpertausch';
			}
		} else {
			$k_mp[0] = $k_mp[0] - $technick1['mp'];			
			$schaden_1 = 'Chance: '.$chance.'%<br>K&ouml;rpertausch fehlgeschlagen';
		}		
		if ($k_mp[0] < 0) {
				$k_mp[0] = 0;
		}
	}
	else {
		$schaden_1 = 'K&ouml;rpertausch fehlgeschlagen';
		$k_mp[0] = 0;
	}
	$aktion_AV1[$x] = 'nein';
}

################### Spieler 2
$char1_luck = mt_random_wrapper(0, $k_glueck[0]);
$char2_luck = mt_random_wrapper(0, $k_glueck[1]);
$chance = round(getKTChance($char2_luck, $char1_luck));
$random = mt_random_wrapper(0,100);

if ($technick2['type'] == 'tausch') {
	if ($k_mp[1] >= $technick2['mp']) {
		if($random <= $chance) { //Trefferchance abhängig vom ausgewürfeltem Glück
			if ($kampf_info['rasse'] != 'NPC') {
			$k_starke_alt[0] = $k_starke[0];
			$k_speed_alt[0] = $k_speed[0];
			$k_ver_alt[0] = $k_ver[0];
			$k_ausdauer_alt[0] = $k_ausdauer[0];
			$k_hp_alt[0] = $k_hp[0];

			$k_starke_alt[1] = $k_starke[1];
			$k_speed_alt[1] = $k_speed[1];
			$k_ver_alt[1] = $k_ver[1];
			$k_ausdauer_alt[1] = $k_ausdauer[1];
			$k_hp_alt[1] = $k_hp[1];

			$k_starke[0] = $k_starke_alt[1];
			$k_speed[0] = $k_speed_alt[1];
			$k_ver[0] = $k_ver_alt[1];
			$k_ausdauer[0] = $k_ausdauer_alt[1];
			$k_hp[0] = $k_hp_alt[1];

			$k_starke[1] = $k_starke_alt[0];
			$k_speed[1] = $k_speed_alt[0];
			$k_ver[1] = $k_ver_alt[0];
			$k_ausdauer[1] = $k_ausdauer_alt[0];
			$k_hp[1] = $k_hp_alt[0];

			$k_mp[1] = $k_mp[1] - $technick2['mp'];
			$schaden_2 = 'Chance: '.$chance.'%<br>K&ouml;rpertausch';
			}
		} else {
			$k_mp[1] = $k_mp[1] - $technick2['mp'];			
			$schaden_2 = 'Chance: '.$chance.'%<br>K&ouml;rpertausch fehlgeschlagen';
		}
		if ($k_mp[1] < 0) {
			$k_mp[1] = 0;
		}
	}
	else {
		$schaden_2 = 'K&ouml;rpertausch fehlgeschlagen';
		$k_mp[1] = 0;
	}
	$aktion_AV2[$x] = 'nein';
	
}

?>