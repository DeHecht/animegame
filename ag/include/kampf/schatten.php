<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

####################KAIOKEN SPIELER 1

if($technick1['type'] == 'schatten') {
	$k_mp[0] = $k_mp[0] - $technick1['mp'];
	$k_hp[0] = $k_hp[0] - $technick1['hp'];
	if($k_mp[0] < 0) {
		$schaden_2 = 'Keinen Schaden';
		$schaden_1 = 'Keine Kraft';
		$aktion_AV1[$x] = 'nein';
		$k_mp[0] = 0;
	} else {
		///////////////////die rechnung sozusagen.
		$play1_ausweich=$technick1['speed'];

	}}
	####################KAIOKEN SPIELER 2

	if($technick2['type'] == 'schatten') {
		$k_mp[1] = $k_mp[1] - $technick2['mp'];
		$k_hp[1] = $k_hp[1] - $technick2['hp'];
		if($k_mp[1] < 0) {
			$schaden_1 = 'Keinen Schaden';
			$schaden_2 = 'Keine Kraft';
			$aktion_AV2[$x] = 'nein';
			$k_mp[1] = 0;
		} else {

			$play2_ausweich=$technick2['speed'];

		}}

		?>