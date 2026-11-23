<?php
/*
 * Created on 07.08.2008
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
/*
 *
 * Alle Hilfsfunktionen fuer Chars sollen hier rein!
 *
 * Die Funktionen der char_profil wären sehr geeignet hierfür
 * 
 */

/*
 * Char Status
 */

// Dieses Array ist dafuer da, dass nicht unnötig viele SQL-Abfragen gemacht werden!
// Fehlgriff im Design, aber solange wir nichts Klassenbasiert machen, gehts nicht anders
$GLOBALS['char_buffered_statuses'] = array ();

/* Diese Funktion gibt den Status des Chars wieder, kann ohne probleme mehrfach aufgerufen 
 * werden da die Anfragen gepuffert werden!
 * returns Status des Charakters mit der id = $charid
 */
function getStatus($charid, $buffer_enabled = true) {
	// Prioritaet, erst Turnier, dann Schatzsuche, dann Kampf
	if ($GLOBALS['char_buffered_statuses'][$charid] != null && $buffer_enabled) {
		return $GLOBALS['char_buffered_statuses'][$charid];
	}

	$sql = 'SELECT count(*) as anzahl FROM turnier_kampf t where art != \'gross\' and (win = ' . $charid . ' or lose = ' . $charid . ') and dauer > now()';
	$row = mysqli_fetch_assoc(db_query($sql));
	if ($row['anzahl'] > 0) {
		$GLOBALS['char_buffered_statuses'][$charid] = 'Turnier';
		return 'Turnier';
	}
	$sql = 'SELECT count(*) as anzahl from quests where charid = ' . $charid;
	$row = mysqli_fetch_assoc(db_query($sql));
	if ($row['anzahl'] > 0) {
		$GLOBALS['char_buffered_statuses'][$charid] = 'Schatz Suche';
		return 'Schatz Suche';
	}
	$sql = 'SELECT count(*) as anzahl from kampf where db_satz = ' . $charid;
	$row = mysqli_fetch_assoc(db_query($sql));
	if ($row['anzahl'] > 0) {
		$GLOBALS['char_buffered_statuses'][$charid] = 'Kampf';
		return 'Kampf';
	}
	
	$sql = 'SELECT count(*) as anzahl FROM kampf_list where charakter = ' . $charid;
	$row = mysqli_fetch_assoc(db_query($sql));
	if ($row['anzahl'] > 0) {
		$GLOBALS['char_buffered_statuses'][$charid] = 'Kampf erstellt';
		return 'Kampf erstellt';
	}
	$sql = 'SELECT count(*) as anzahl FROM turnier1 where charakter = ' . $charid;
	$row = mysqli_fetch_assoc(db_query($sql));
	if ($row['anzahl'] > 0) {
		$GLOBALS['char_buffered_statuses'][$charid] = 'Angemeldet';
		return 'Angemeldet';
	}
	$GLOBALS['char_buffered_statuses'][$charid] = 'Frei';
	return 'Frei';
}

/*
 * Char generell!
 */

// Initialisierung des Puffers
$GLOBALS['char_buffered_instances'] = array ();
$GLOBALS['chars_read_out_user'] = array ();

// Uebergabe der Charid, und optional ob gepuffert werden darf
// returns: Char als Array
function getChar($charid, $buffer_enabled = true) {
	// Fehlerkontrolle
	if(!is_numeric($charid)){
		return null;
	}
// 	Wenn Char nicht im Puffer ist, oder nicht gepuffert werden soll
	if ($GLOBALS['char_buffered_instances'][$charid] == null || !$buffer_enabled) {
//		Lese das Charfeld aus der Datenbank aus
		$char = mysqli_fetch_assoc(db_query('Select * from chars where id = '.$charid));
		if(!$char){return null;}
//		Speichere in $char['stauts'] den aktuellen Status des Chars (buffer-Strategie ist hierbei die selbe)
		$char['status'] = getStatus($char['id'],$buffer_enabled);
		$char['bild'] = getPicture($char);
		$GLOBALS['char_buffered_instances'][$charid] = &$char; // Speichern des Chars in den Puffer
	}
// 	Gebe Char zurueck
	return $GLOBALS['char_buffered_instances'][$charid];
}

function getChar2($charid) {
	// Fehlerkontrolle
	if(!is_numeric($charid)){
		return null;
	} else {
		$char = mysqli_fetch_assoc(db_query('Select * from chars where id = '.$charid));
		if(!$char){return null;}
//		Speichere in $char['stauts'] den aktuellen Status des Chars (buffer-Strategie ist hierbei die selbe)
		$char['status'] = getStatus($char['id'],$buffer_enabled);
		$char['bild'] = getPicture($char);
	}
	return $char;
}

function getCharWithBuffs($charid, $buffer_enabled = true){
	$char = getChar($charid, $buffer_enabled);
	if($char['kampf_item'] != NULL && $char['kampf_item'] != ',,,,'){
		$char_buffs = mysqli_fetch_assoc(db_query('SELECT sum(i.hp) as hp, sum(i.mp) as mp, sum(i.starke) as starke, sum(i.verteidigung) as verteidigung, sum(i.speed) as speed FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE w.id IN (' . $char['kampf_item'] . ')'));
	}
	$char['starke'] += $char_buffs['starke'];
	$char['verteidigung'] += $char_buffs['verteidigung'];
	$char['speed'] += $char_buffs['speed'];
	$char['ausdauer'] += $char_buffs['ausdauer'];
	$char['glueck'] += $char_buffs['glueck'];
	return $char;
}

// Uebergabe der Userid (Nachteil ist, dass Puffer von getChar mit $charid nicht moeglich ist, weil information fehlt, ob alle Chars im Buffer sind


/* 
 * Liefert die Url zum Avatarbild (Falls Char keinen Avatar hat gibt es die
 * noavatar.gif zurueck!)
 * returns Url of Avatar-Picture
 * */
function getPicture($char){
	// Sichergehen, dass Char geladen wurde
	if($char['bild'] == null || $char['bild'] == ''){
		return 'design/bilder/avatare/noavart.gif';
	}
	return $char['bild'];
}


// returns Array von Char-Arrays
function getCharsOfUser($userid, $buffer_enabled = true) {
	// Fehlerkontrolle
	if(!is_numeric($userid)){
		return null;
	}

	if ($GLOBALS['chars_read_out_user'][$userid] == null || !$buffer_enabled) {
		$GLOBALS['chars_read_out_user'][$userid] = array();
		$qry = db_query('Select * from chars where besitzer = '.$userid.' order by id');
		while($char = mysqli_fetch_assoc($qry)){
//			Speichere in $char['stauts'] den aktuellen Status des Chars (buffer-Strategie ist hierbei die selbe)
			$char['status'] = getStatus($char['id'],$buffer_enabled);
			$char['bild'] = getPicture($char);
//			Speichere den Char in das Globale-Pufferfeld
			$GLOBALS['char_buffered_instances'][$char['id']] = $char;
// 			Gebe dem User eine Referenz auf die globale chars-Variable
			$GLOBALS['chars_read_out_user'][$userid][] = &$GLOBALS['char_buffered_instances'][$char['id']];
		}
	}
	return $GLOBALS['chars_read_out_user'][$userid]; // Gebe das globale Userfeld zurück.
}

function deleteChar($userid, $charid){
	$char = getChar($charid);
	if($char['besitzer'] != $userid){
		return 'Char gehoert nicht dir!';
	} else if($char['status'] != 'Frei'){
		return 'Char ist nicht Frei!';
	}
	db_query('Delete from lernen where besitzer = '.$charid);
	db_query('Delete from chars where id = '.$charid);
	return true;
}


/* 
 * Funktionen die durch Puffern profitieren würden oder um einfach Code einzusparen und um flexibel zu bleiben
 * 
 */

// Ermittelt den Besitzer
// Returns true = Ja, False = Nein, NULL = Error
function isUserOwnerOf($userid, $charid, $buffer_enabled = true){
	// Fehlerkontrolle
	if(!is_numeric($userid) || !is_numeric($charid)){
		return null;
	}
	$char = getChar($charid, $buffer_enabled);
	if($char == null){
		return null;
	}
	return $char['besitzer'] == $userid;
}

// Funktion um das Powerlevel zu Berechnen (Kann ja durchaus sein, dass sich mal was dran ändert!)
// Returns Powerlevel als Integer, NULL = Error
function getPowerLevel($charid, $buffer_enabled = true){
	$char = getChar($charid, $buffer_enabled);
	if($char == null){
		return null;
	}
	return $char['starke']+$char['verteidigung']+$char['speed']+$char['ausdauer']+$char['glueck'];
}

function getPowerLevelWithBuffs($charid, $buffer_enabled = true){
	$char = getChar($charid, $buffer_enabled);
	if($char == null){
		return null;
	}
	if($char['kampf_item'] != NULL && $char['kampf_item'] != ',,,,'){
		$char_buffs = mysqli_fetch_assoc(db_query('SELECT sum(i.hp) as hp, sum(i.mp) as mp, sum(i.starke) as starke, sum(i.verteidigung) as verteidigung, sum(i.speed) as speed FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE w.id IN (' . $char['kampf_item'] . ')'));
	}
	return $char_buffs['starke']+$char_buffs['verteidigung']+$char_buffs['speed']+$char_buffs['ausdauer']+$char_buffs['glueck']+$char['starke']+$char['verteidigung']+$char['speed']+$char['ausdauer']+$char['glueck'];
}

/*
 * Auslesen der Attacken, die der Char beherrscht
 */
function getCharAttacks($charid){
	
}

// Auslesen der Kampfeinstellungen
function getChoosenAttacks($charid){
	
}


function getCharRaceId($charid){
	$char = getChar($charid);
	$rassen_werte = array(
		'Mensch'			=> 1,	///0
		'Saiyajin'			=> 2,	///1
		'Dämon'	  			=> 3,	///2
		'Mutant'			=> 4,	///3
		'Cyborg'			=> 5,	///4
		'Namekianer'		=> 6,	///5
		'Pirat'				=> 7,	///6
		'Shichibukai'		=> 8,	///7
		'Kaioshin'			=> 9,	///8
		'Schwertkämpfer'	=> 10,///9
		'Grandline Maschine'=> 11,///10
		'01'				=> 12,///11
		'Leaf Dorf'			=> 13,///12
		'Sand Dorf'			=> 14,///13
		'Sound Dorf'		=> 15///14
	);
	return $rassen_werte[$char['rasse']];
}

function getFusionRaceId($charid){
	$char = getChar($charid);
	$rassen_werte = array(
		'Mensch'			=> 1,	///0
		'Saiyajin'			=> 2,	///1
		'Dämon'	  			=> 3,	///2
		'Mutant'			=> 4,	///3
		'Cyborg'			=> 5,	///4
		'Namekianer'		=> 6,	///5
		'Pirat'				=> 7,	///6
		'Shichibukai'		=> 8,	///7
		'Kaioshin'			=> 9,	///8
		'Schwertkämpfer'	=> 10,///9
		'Grandline Maschine'=> 11,///10
		'01'				=> 12,///11
		'Leaf Dorf'			=> 13,///12
		'Sand Dorf'			=> 14,///13
		'Sound Dorf'		=> 15///14
	);
	return $rassen_werte[$char['fusion_rasse']];
	
}

?>