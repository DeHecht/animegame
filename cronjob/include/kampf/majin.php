<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

defineIfNotDefined('MAJIN_DIVIDENT', 10);


####################Majin SPIELER 1

if($technick1['type'] == "majin") {
	$k_mp[0] = $k_mp[0] - $technick1['mp'];
	if($k_mp[0] < 0) {
		$schaden_1 = "Majin Verwandlung fehlgeschlagen";
		$aktion_AV1[$x] = "nein";
		$k_mp[0] = 0;
	} else {
	        $options = json_decode($technick1['options'], true);
	
                // options is now an array with following input possible ...
                // 'attribute' => array('strength', 'defense', 'speed')

                // Place handling here depending on the options


		$k_starke[1] = $k_starke[1] * ($technick1['starke'] / MAJIN_DIVIDENT);
		$k_speed[1] = $k_speed[1] * ($technick1['speed'] / MAJIN_DIVIDENT);
		$k_ver[1] = $k_ver[1] * ($technick1['verteidigung'] / MAJIN_DIVIDENT);

		$alt_hp = $k_hp[1];

		$my_dmg = 0;

		foreach( $options['attribute'] as $attr ) {
			if($attr == 'strength') {
				$my_dmg += $k_starke[1] / 2;
			} else if($attr == 'defense') {
                                $my_dmg += $k_ver[1] / 2;
			} else if($attr == 'speed') {
                                $my_dmg += $k_speed[1] / 2;
			}
		}

		$k_hp[1] = round($k_hp[1] - $my_dmg);
		$schaden_1 = "$technick1[name]";
		$schaden_2 = $alt_hp - $k_hp[1];

		if($alt_hp < $k_hp[1]) { $k_hp[1] = $alt_hp; $schaden_2 = 0; }

		$aktion_AV1[$x] = "nein";
		$aktion_AV2[$x] = "nein";
		$aussetzten_runde = 1;
	}}

	####################Majin SPIELER 2

	if($technick2['type'] == "majin") {
		$k_mp[1] = $k_mp[1] - $technick2['mp'];
		if($k_mp[1] < 0) {
			$schaden_2 = "Majin Verwandlung fehlgeschlagen";
			$aktion_AV2[$x] = "nein";
			$k_mp[1] = 0;
		} else {
			$options = json_decode($technick2['options'], true);

			// options is now an array with following input possible ...
			// 'attribute' => array('strength', 'defense', 'speed')

			// Place handling here depending on the options

	                $k_starke[0] = $k_starke[0] * ($technick2['starke'] / MAJIN_DIVIDENT);
        	        $k_speed[0] = $k_speed[0] * ($technick2['speed'] / MAJIN_DIVIDENT);
                	$k_ver[0] = $k_ver[0] * ($technick2['verteidigung'] / MAJIN_DIVIDENT);

			$alt_hp = $k_hp[0];

	                $my_dmg = 0;
	
        	        foreach( $options['attribute'] as $attr ) {
                	        if($attr == 'strength') {
                        	        $my_dmg += $k_starke[0] / 2;
                        	} else if($attr == 'defense') {
                               		$my_dmg += $k_ver[0] / 2;
                        	} else if($attr == 'speed') {
                                	$my_dmg += $k_speed[0] / 2;
                        	}
                	}

			$k_hp[0] = round($k_hp[0] - $my_dmg);
			$schaden_2 = "$technick2[name]";
			$schaden_1 = $alt_hp - $k_hp[0];

			if($alt_hp < $k_hp[0]) { $k_hp[0] = $alt_hp; $schaden_1 = 0; }

			$aktion_AV1[$x] = "nein";
			$aktion_AV2[$x] = "nein";
			$aussetzten_runde = 1;
		}}

		?>
