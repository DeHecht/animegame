<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
if($technick1['type'] == "runde") {
	
	$k_mp[0] = $k_mp[0] - $technick1['mp'];
		
	if($k_mp[0] < 0) { 
		$schaden_1 = "$technick1[name] fehlgeschlagen";
		$aktion_AV1[$x] = "nein";
		$k_mp[0] = 0; 
	} else {

		if($runden_type1 < $technick1['hp']) {			/////////HP wert sagt an wieviel runden die technik dauert
				$schaden_1 = "Energie Sammeln";
				$speeds_char1 = 10;
				$aktion_AV1[$x] = "nein";
				$runden_type1++;
		} else {
				echo "MEIN SPEED: $speeds_char1";
				$runden_type1=0;
		}
	}
}

if($technick2['type'] == "runde") {
$k_mp[1] = $k_mp[1] - $technick2['mp'];
if($k_mp[1] < 0) { 
$schaden_2 = "$technick2[name] fehlgeschlagen";
$aktion_AV2[$x] = "nein";
$k_mp[1] = 0; 
} else {

	if($runden_type2 < $technick2['hp']) {			/////////HP wert sagt an wieviel runden die technik dauert
			$schaden_2 = "Energie Sammeln";
			$speed_char2 = 10;
			$aktion_AV2[$x] = "nein";
			$runden_type2++;
		} else {
				echo "MEIN SPEED2: $speed_char2";
			$runden_type2=0;
		}
	}
}
?>