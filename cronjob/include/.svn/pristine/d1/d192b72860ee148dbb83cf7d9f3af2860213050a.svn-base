<?php
/*
 * Created on 07.08.2008
 *
 * @copyright (c) 2009 animegame.eu
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

// required for status calculation!
include_once(ROOT_PATH.'/include/event.inc.php');
include_once(ROOT_PATH.'/include/clan_fights.inc.php');
include_once(ROOT_PATH.'/include/rassen.inc.php');

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
	$sql = 'SELECT count(*) as anzahl FROM tournament_registration where charakter = ' . $charid;
	$row = mysqli_fetch_assoc(db_query($sql));
	if ($row['anzahl'] > 0) {
		$GLOBALS['char_buffered_statuses'][$charid] = 'Angemeldet';
		return 'Angemeldet';
	}

	if(isCharBusyWithClanfighting($charid)) {
		$GLOBALS['char_buffered_statuses'][$charid] = 'Clanfight';
		return 'Clanfight';
	}

	$event_status = getEventStatus($charid);
	if($event_status !== NULL) {
		$GLOBALS['char_buffered_statuses'][$charid] = $event_status;
		return $event_status;
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
		$char = getChar2($charid);
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
		// Check if it is new style!
		if(is_numeric($char['rasse']) && $char['rasse'] != 0) {
			$rasse = getRaceById($char['rasse']);
			$char['rasse']=$rasse['name'];
			$type=getRaceTypeById($rasse['type']);
			$char['type']=$type['name'];
			if($rasse['special']) {
				$char['char_type'] = $rasse['name'];
			}
			$char['rasse_a'] = $rasse;
		} else if($char['rasse'] != '0'){
			// it is old style!
			$rasse = getRaceByName($char['rasse']);
			if($rasse != NULL) {
				$char['rasse_a'] = $rasse;
				// update the old styled rasse
				db_query('update chars set rasse = \''.$rasse['id'].'\', type = NULL where id = '.$charid);
			}
		}
		
		if(is_numeric($char['fusion_rasse']) && $char['fusion_rasse'] != 0) {
			$rasse = getRaceById($char['fusion_rasse']);
			$char['fusion_rasse'] = $rasse['name'];
		} else if($char['fusion_rasse'] != '0') {
			$rasse = getRaceByName($char['fusion_rasse']);
			if($rasse != NULL) {
				$char['fusion_rasse_a'] = $rasse;
				// update the old styled fusion_rasse
				db_query('update chars set fusion_rasse = \''.$rasse['id'].'\' where id = '.$charid);
			}
		}
	}
	return $char;
}

function getCharWithBuffs($charid, $buffer_enabled = true){
	$char = getChar($charid, $buffer_enabled);
	$char_buffs = getCharBuffs($char['kampf_item']);
	$valueshp = explode(',', $char['hp']);
	$char['hp'] = ($valueshp[0] + $char_buffs['hp']). ','.($valueshp[1] + $char_buffs['hp']);
	$valuesmp = explode(',', $char['mp']);
	$char['mp'] = ($valuesmp[0] + $char_buffs['mp']). ','.($valuesmp[1] + $char_buffs['mp']);
	$char['starke'] += $char_buffs['starke'];
	$char['verteidigung'] += $char_buffs['verteidigung'];
	$char['speed'] += $char_buffs['speed'];
	$char['ausdauer'] += $char_buffs['ausdauer'];
	$char['glueck'] += $char_buffs['glueck'];
	return $char;
}

function getCharBuffs($items) {
	if($items != NULL && $items != ',,,,'){
		$char_buffs = mysqli_fetch_assoc(db_query('SELECT sum(i.hp) as hp, sum(i.mp) as mp, sum(i.starke) as starke, sum(i.verteidigung) as verteidigung, sum(i.speed) as speed FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE w.id IN ('.$items.')'));
	}
	return $char_buffs;
}
// Uebergabe der Userid (Nachteil ist, dass Puffer von getChar mit $charid nicht moeglich ist, weil information fehlt, ob alle Chars im Buffer sind


/*
 * Liefert die Url zum Avatarbild (Falls Char keinen Avatar hat gibt es die
 * noavatar.gif zurueck!)
 * returns Url of Avatar-Picture
 * */
function getPicture($char) {
	$hide = false;
	if(isset($GLOBALS['user_array']['id'])) {
		$data = getUserMetaData($GLOBALS['user_array']['id']);
		$hide = $data['hide_avatars'] == 1;
	}
	// Sichergehen, dass Char geladen wurde
	if($char['bild'] == null || $char['bild'] == '' || $hide) {
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
		$qry = db_query('Select id from chars where besitzer = '.$userid.' order by id');
		while($char = mysqli_fetch_assoc($qry)){
			$GLOBALS['chars_read_out_user'][$userid][] = &getChar($char['id'], $buffer_enabled);
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
	//	Uncomment for hp/mp in PL
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
	//	Uncomment for hp/mp in PL
	return $char_buffs['starke']+$char_buffs['verteidigung']+$char_buffs['speed']+$char_buffs['ausdauer']+$char_buffs['glueck']+$char['starke']+$char['verteidigung']+$char['speed']+$char['ausdauer']+$char['glueck'];
}

/*
 * Auslesen der Attacken, die der Char beherrscht
 */
function getCharAttacks($charid){
	$attacks = array();
	$qry = db_query("SELECT name, id, at_id FROM lernen WHERE besitzer='$charid'	AND aktiv > '0' order by id");
	while($row = mysqli_fetch_array($qry)) {
		$attacks[] = array('name'=>$row['name'], 'id'=>$row['id'], 'at_id'=>$row['at_id']);
	}
	return $attacks;
}

// Auslesen der Kampfeinstellungen
function getChoosenAttacks($charid){

}


function getCharRaceId($charid){
	$char = getChar($charid);
	return $char['rasse_a']['id'];
}

function getFusionRaceId($charid){
	$char = getChar($charid);
	return $char['fusion_rasse_a']['id'];
}

/**
 * This function returns an overview about the
 * @param $charid the id of the character
 * @param $mode 0 = show all available attacks, 1 = show all unavailable attacks, 2 = show both!
 */
function getAttacksforChar($charid, $mode = 0) {
	$char = getChar($charid);
	$char_race = getCharRaceId($charid);
	// first read out basic information about our attacks


	//	$sql = 'select a.name, a.id, a.level, if(find_in_set(\'0\', req_atk) = 0, a.req_atk, substr(a.req_atk, 1, locate(\',0\', a.req_atk) - 1)) as req_atk , geld, ifnull((select l.benutzt from lernen l where l.besitzer = '.$char['id'].' and at_id = a.id),0) as benutzt, a.id not in (select l.at_id from lernen l where l.besitzer = '.$char['id'].') as unknown from attacken a where find_in_set('.$char_race.', a.rassen) order by level';

	$sql_append = '';

	if($char['frucht'] !== NULL) {
		$sql_append1 = ' OR (frucht = (Select id from wochen_markt where item = \''.$char['frucht'].'\')) ';
		$sql_append2 = ' AND (frucht <> (Select id from wochen_markt where item = \''.$char['frucht'].'\')) ';
	}

	// First sql to include all currently learnable attacks
	$sql = 'SELECT a.name, a.id, a.level, a.geld, if(find_in_set(\'0\', req_atk) = 0, a.req_atk, substr(a.req_atk, 1, locate(\',0\', a.req_atk) - 1)) as req_atk, ifnull(l.benutzt, 0) as benutzt, if(l.benutzt is null,1,0) as unknown from attacken a left join lernen l on a.id = l.at_id AND l.besitzer = '.$char['id']. ' where find_in_set('.$char_race.', a.rassen) <> 0 or l.benutzt is not null '.$sql_append1;
	// Second sql to include all currently not learnable attacks (due to an other fruit or fusion race)
	$sql2 = 'SELECT a.name, a.id, a.level, a.geld, if(find_in_set(\'0\', req_atk) = 0, a.req_atk, substr(a.req_atk, 1, locate(\',0\', a.req_atk) - 1)) as req_atk, ifnull(l.benutzt, 0) as benutzt, if(l.benutzt is null,1,0) as unknown from lernen l inner join attacken a on l.at_id = a.id where besitzer = '.$char['id'].' and find_in_set('.$char_race.', a.rassen) = 0 '.$sql_append2;

	// make a union out of both (TODO: maybe an outer join would result into the same result)
	$sql = '('.$sql.') union distinct ('.$sql2.') order by level';

	$qry = db_query($sql);
	if($qry === FALSE) // print sql on error
	echo '<br>'.$sql.'<br>';

	// The first index is the id of the attack!
	// then there is the basic information like
	//
	// entries {id, name, levelups, benutzt, unknown}
	// ==> id, name, required level, times that it has been used , if its still unkown (bool)
	// entry {req} -> array {id, name, reggs}
	// ==> [0] => first requirenment -> id, name, amount level ups required, times to be used
	// ==> [n] => nth requirenment -> ...

	$overview = array();

	$attacke = array();
	$requires = array();

	while($row = mysqli_fetch_assoc($qry)) {
		$attacke[$row['id']] = $row;

		if($row['unknown'] == 0 && $mode == 1) {
			continue;
		} else if($row['unknown'] == 1 && $mode == 0) {
			continue;
		}

		$overview[$row['id']] = array('id' => $row['id'], 'name' => $row['name'], 'levelups' => ($row['level'] - $char['level']), 'benutzt' => $row['benutzt'], 'unknown' => $row['unknown'] == 1);
		// as it is straight forward (on level) we already can calculate the dependencies

		if($overview[$row['id']]['levelups'] < 0) {
			$overview[$row['id']]['levelups'] = 0;
		}

		// echo $attacke[$row['id']]['name'] . ' = ' . $attacke[$row['id']]['req_atk'] . '<br>';

		if($attacke[$row['id']]['req_atk'] == 0) {
			// nothing :D
			$attacke[$row['id']]['req'] = array(0);
		} else if(strpos($attacke[$row['id']]['req_atk'], ',') === FALSE) {
			//			echo 'One thing to do<br>';
			// only one attack required :) => use reference

			$other_attack = &$attacke[$attacke[$row['id']]['req_atk']];
			// => this is the id of the required attack, so easy going :)
			$reggs = ($attacke[$row['id']]['geld'] - $other_attack['benutzt']);
			if($reggs < 0) {
				$reggs = 0;
			}
			$overview[$row['id']]['req'][] = array('id' => $other_attack['id'], 'name' => $other_attack['name'], 'reggs' => $reggs);
		} else {
			//			echo 'More things to do<br>';
			// multiple attacks required :(
			$attack_ids = explode(',',$attacke[$row['id']]['req_atk']);
			for($i=0;$i<count($attack_ids);$i++) {
				$other_attack = &$attacke[$attack_ids[$i]];
				// => this is the id of the required attack, so easy going :)
				$reggs = ($attacke[$row['id']]['geld'] - $other_attack['benutzt']);
				if($reggs < 0) {
					$reggs = 0;
				}
				$overview[$row['id']]['req'][] = array('id' => $other_attack['id'], 'name' => $other_attack['name'], 'reggs' => $reggs);
			}
		}
	}

	return $overview;
}

?>
