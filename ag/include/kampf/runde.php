<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

if($technick1['type'] == 'runde') {

	$k_mp[0] = $k_mp[0] - $technick1['mp'];

	if($k_mp[0] < 0) {
		$schaden_1 = $technick1['name'] . ' fehlgeschlagen';
		$aktion_AV1[$x] = 'nein';
		$k_mp[0] = 0;
		// set the tech stats to 0
		$technick1['starke'] = 0;
		$technick1['speed'] = 0;
		$technick1['verteidigung'] = 0;
		$play1_ausweich = 0;
	} else {

		if($runden_type1 < $technick1['hp']-1) {			/////////HP wert sagt an wieviel runden die technik dauert
				$schaden_1 = 'Energie Sammeln';
				$play1_ausweich = 0;
				$aktion_AV1[$x] = 'nein';
				$runden_type1++;
		} else {
				$aktion_AV1[$x] = '';
				$runden_type1=0;
		}
	}

}

if($technick2['type'] == 'runde') {
	$k_mp[1] = $k_mp[1] - $technick2['mp'];

	if($k_mp[1] < 0) {
		$schaden_2 = $technick2['name'] . ' fehlgeschlagen';
		$aktion_AV2[$x] = 'nein';
		$k_mp[1] = 0;
		// set the tech stats to 0
		$technick2['starke'] = 0;
		$technick2['speed'] = 0;
		$technick2['verteidigung'] = 0;
		$play2_ausweich = 0;
} else {

	if($runden_type2 < $technick2['hp']-1) {			/////////HP wert sagt an wieviel runden die technik dauert
			$schaden_2 = 'Energie Sammeln';
			$play2_ausweich = 0;
			$aktion_AV2[$x] = 'nein';
			$runden_type2++;
		} else {
			$aktion_AV2[$x] = '';
			$runden_type2=0;
		}
	}
}
?>
