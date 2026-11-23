<?php
/*
 * Created on 08.05.2008
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */


include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');
include_once(ROOT_PATH.'/include/rassen.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/exp.inc.php');



function erstelleUser(){
	// TODO: Imlementierung

}

function hasUserFreeCharSlot($user, $special = false) {
	$chars = getCharsOfUser($user['id']);
	$special_chars = 0;
	$normal_chars = 0;
	foreach($chars as $char){
		if($char['rasse_a']['special']) {
			$special_chars++;
		} else {
			$normal_chars++;
		}
	}
	
	if(!$special || $special_chars > 0) {
		if($special_chars > 0)
			$special_chars--;

		$chars_avail = $normal_chars + $special_chars;
		$slots_avail = $user['char_max'];
		return $chars_avail < $slots_avail;
	} else {
		// special char but no special char yet available!
		return true;
	}
	
	return false;
}

// Muss aus einem sicheren Kontext gestartet werden
function erstelleChar($user, $rassen_id, $newname = '', $picture = '', $lvl = 1, $lp = 0, $hp = 0, $mp = 0, $str = 0, $def = 0, $spd = 0, $end = 0, $lck = 0, $chartype = 'Dragonball'){
	// If-Anweisungen muessen noch durch Datenbankabfragen ersetzt werden
	$trainingspoints = 0;

	if($newname == ''){
		displayErrorMessage(NULL,'Name ist nicht erlaubt!!',displayHistoryBackLink());
		return false;
	}

	$race = getRaceById($rassen_id);
	if($race == NULL) {
		displayErrorMessage(NULL,'Rasse nicht vorhanden!!',displayHistoryBackLink());
		return false;
	}

	// Durch $race können wir nun die chars generisch erstellen :) ... Bye bye multi-ifs :D

	$HP = $race['hp'] + $hp*10;
	$MP = $race['mp'] + $mp*5;
	$Starke = $race['str'] + $str;
	$Verteidigung = $race['def'] + $def;
	$Geschwindigkeit = $race['spd'] + $spd;
	$Gluck = $race['lck'] + $lck;
	$Ausdauer = $race['stm'] + $end;
	$type = ''; // Not required anymore!
	$c_type = '';
	$special_char = $race['special'];
	$trainingspoints += $lp*2;
	$LP = ($lvl - 1) * 10;

	if($race['name'] != 'NPC') {
		//Ueberprüfe ob ein Slot frei ist
		$check = hasUserFreeCharSlot($user, $special_char);
		if(!$check){
			displayErrorMessage(NULL,'Alle Slots sind schon belegt!!',displayHistoryBackLink());
			return false;
		}
	}
	// Slotüberprüfung beendet!
	$sql = 'Insert into chars(name, starke, verteidigung, speed, glueck, ausdauer, hp, mp, level, rasse, besitzer, training_points, lernpunkte, bild, exp) ' .
				'values(\''.$newname.'\',' .
						' '.$Starke.',' .
						' '.$Verteidigung.',' .
						' '.$Geschwindigkeit.',' .
						' '.$Gluck.',' .
						' '.$Ausdauer.',' .
						' \''.$HP.','.$HP.'\',' .
						' \''.$MP.','.$MP.'\',' .
						' '.$lvl.',' .
						' \''.$rassen_id.'\',' .
						' '.$user['id'].',' .
						' '.$trainingspoints.',' .
						' '.$LP.',' .
						'\''.$picture.'\', '.
						'\'0,'.calculateRequiredExpChars($lvl).'\'' .
						')';
	//echo $sql.'<br>';
	$identifier = silent_query($sql);
	if($identifier == FALSE){
		if(mysqli_fetch_assoc(db_query('Select id from chars where name = \''.$newname.'\''))){
			displayErrorMessage(NULL,'Name schon vorhanden!!',displayHistoryBackLink());
		} else{
			displayErrorMessage(NULL,'Erstellen fehlgeschlagen!!',displayHistoryBackLink());
		}
		return false; // Hat nich geklappt
	}

	if($race['name'] != 'NPC') {
		$charsw_id = mysqli_fetch_assoc(db_query('SELECT id FROM chars WHERE name=\''.$newname.'\''));

		db_query('INSERT lernen SET at_id=1, aktiv=1, besitzer='.$charsw_id['id'].', name=\'Schlag\', dauer=0');
		db_query('INSERT lernen SET at_id=2, aktiv=1, besitzer='.$charsw_id['id'].', name=\'Kick\', dauer=0');
		db_query('INSERT lernen SET at_id=3, aktiv=1, besitzer='.$charsw_id['id'].', name=\'Block\', dauer=0');
	}
	return true; // Hat geklappt
}

?>
