<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH. "/include/random.inc.php");

if (!function_exists('getKTChance')) {
	
	function getKTChance($ktuser_luck, $ktvictim_luck) {
	$chance = 50;
	$ktvictim_luck= mt_random_wrapper(0, $ktvictim_luck);	
	$ktuser_luck = mt_random_wrapper(0, $ktuser_luck);
	if($ktvictim_luck < ($ktuser_luck*0.25)) {
		$chance = 75;
	}
	if($ktvictim_luck > ($ktuser_luck*1.75)) {
		$chance = 25;
	}
	return $chance;
	}
	
}

################### Spieler 1
$chance = getKTChance($k_glueck[0], $k_glueck[1]);
$random = mt_random_wrapper(0,100);
	
if ($technick1['type'] == tausch) {
	if ($k_mp[0] >= $technick1['mp']) {
		if($random <= $chance) { //Trefferchance abhängig vom ausgewürfeltem Glück
			if ($kampf_info['rasse'] != "NPC") { ///BEI NPCS WIRD KOERPER TAUSCH GEBLOCKT
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
				$schaden_1 = "K&ouml;rpertausch";
			}
		} else {
			$k_mp[0] = $k_mp[0] - $technick1['mp'];
			if ($k_mp[0] < 0) {
				$k_mp[0] = 0;
			}
			$schaden_1 = "K&ouml;rpertausch fehlgeschlagen";
		}
	}
	else {
		$schaden_1 = "K&ouml;rpertausch fehlgeschlagen";
	}
	$aktion_AV1[$x] = "nein";
}

################### Spieler 2
$chance = getKTChance($k_glueck[1], $k_glueck[0]);
$random = mt_random_wrapper(0,100);

if ($technick2['type'] == tausch) {
	if ($k_mp[1] >= $technick2['mp']) {
		if($random <= $chance) { //Trefferchance abhängig vom ausgewürfeltem Glück

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
			$schaden_2 = "K&ouml;rpertausch";
		} else {
			$k_mp[1] = $k_mp[1] - $technick2['mp'];
			if ($k_mp[1] < 0) {
				$k_mp[1] = 0;
			}
			$schaden_1 = "K&ouml;rpertausch fehlgeschlagen";
		}
	}
	else {
		$schaden_2 = "K&ouml;rpertausch fehlgeschlagen";
	}
	$aktion_AV2[$x] = "nein";
	
}

