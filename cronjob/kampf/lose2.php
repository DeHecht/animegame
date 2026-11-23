<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH. "/include/random.inc.php");

####################KAIOKEN-LOSE SPIELER 1

if($technick1['type'] == 'lose2') {
	$k_mp[0] = $k_mp[0] - $technick1['mp'];
	if($k_mp[0] < 0) {
		$schaden_1 = 'Schw&auml;chen fehlgeschlagen';
		$k_mp[0] = 0;
	} else {
		if($technick1['starke'] >= mt_random_wrapper(1,100)) {
			$schaden_1 = 'Schw&auml;chen';
			$k_mp[1] -= $k_mp2[1] * ($technick1['speed'] / 100);
			if($k_mp[1] < 0) {
				$k_mp[1] = 0;
			}
		} else {
			$schaden_1 = 'Schw&auml;chen fehlgeschlagen';
		}
	}
	$aktion_AV1[$x] = 'nein';
}
####################KAIOKEN-LOSE SPIELER 2

if($technick2['type'] == 'lose2') {
	$k_mp[1] = $k_mp[1] - $technick2['mp'];
	if($k_mp[1] < 0) {
		$schaden_2 = 'Schw&auml;chen fehlgeschlagen';
		$k_mp[1] = 0;
	} else {

		if($technick2['starke'] >= mt_random_wrapper(1,100)) {
			$schaden_2 = 'Schw&auml;chen';
			$k_mp[0] -= $k_mp1[1] * ($technick2['speed'] / 100);
			if($k_mp[0] < 0) {
				$k_mp[0] = 0;
			}
		} else {
			$schaden_2 = 'Schw&auml;chen fehlgeschlagen';

		}
	}
	$aktion_AV2[$x] = 'nein';
}
