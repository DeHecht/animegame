<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH.'/include/char.inc.php');

function calculateArenaData($owner_id, $char_1, $char_2, $factor){
//	echo 'Berechne die Arena-Daten mit Faktor '.$factor;
	$lf = 0.7;				// Luxus Exponent
	$kf_1 = 1/3; 		// Kampf Exponent a)
	$kf_2 = $kf_1 * $kf_1;	// Kampf Exponent b)

	// default arena!
	if($owner_id == NULL) {
		$arena['luxus'] = 2;
		$arena['level'] = 2;
		$arena['steh'] = 1000000;
		$arena['sitz'] = 1000000;
		$arena['loge'] = 1000000;
		$arena['zustand'] = 1;
	} else  {
		$arena = mysqli_fetch_assoc(db_query('SELECT * FROM arena WHERE besitzer = '.$owner_id));
	}

	$wert_char1 = max(0.75,min(1.25,$char_1['siege']/($char_1['niederlagen'] + 0.01))) * getPowerLevelWithBuffs($char_1['id']);
	$wert_char2 = max(0.75,min(1.25,$char_2['siege']/($char_2['niederlagen'] + 0.01))) * getPowerLevelWithBuffs($char_2['id']);
	// Neue Kalkulation :)
	$kampf_wert = (pow($wert_char1,$kf_1)+pow($wert_char1,$kf_2))*(pow($wert_char2,$kf_1)+pow($wert_char2,$kf_2));
	// Neue Kalkulation :)
	$arena_wert = pow($arena['luxus'], $lf)+log($arena['level'],4)+0.5;

	$zuschauer = $kampf_wert*$arena_wert*$factor;
	$z_loge = floor(0.01*$zuschauer);
	$z_sitz = round(0.36*$zuschauer);
	$z_steh = round($zuschauer - $z_loge - $z_sitz); // Damit auch ja keiner zu kurz kommt :D
	$result['steh'] = $z_steh>$arena['steh']?$arena['steh']:$z_steh;
	$result['sitz'] = $z_sitz>$arena['sitz']?$arena['sitz']:$z_sitz;
	$result['loge'] = $z_loge>$arena['loge']?$arena['loge']:$z_loge;
	$result['steh'] = round($result['steh']*$arena['zustand']);
	$result['sitz'] = round($result['sitz']*$arena['zustand']);
	$result['loge'] = round($result['loge']*$arena['zustand']);
	$result['geld'] = $result['steh'] * 10 + $result['sitz'] * 60 + $result['loge'] * 3000;
	$result['id'] = $arena['id'];
	return $result;
}

function calculateUnterhaltsKosten($arena){
	return $arena['steuerlasten'];
}

function calculateRenovierungsKosten($arena){
	return (1-$arena['zustand'])*($arena['steh']*10+$arena['sitz']*150+$arena['loge']*18000);
}

function getArena($user_id) {
	$sql_select = 'SELECT * FROM arena where besitzer = \''.$user_id.'\'';
	$qry = db_query($sql_select);
	if(mysqli_num_rows($qry) == 0) {
			$sql = 'INSERT arena(besitzer, exp) values (\''.$user_id.'\', \'0,'.calculateRequiredExpArena(1).'\')';
			db_query($sql);
			$qry = db_query($sql_select);
	}
	$arena = mysqli_fetch_assoc($qry);
	return $arena;
}


?>