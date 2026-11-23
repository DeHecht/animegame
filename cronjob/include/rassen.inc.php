<?php
/*
 * Created on 02.05.2014
 *
 * @copyright (c) 2014 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */


#include_once(ROOT_PATH.'/include/event.inc.php');

// Dieses Array ist dafuer da, dass nicht unnötig viele SQL-Abfragen gemacht werden!
// Fehlgriff im Design, aber solange wir nichts Klassenbasiert machen, gehts nicht anders
$GLOBALS['char_buffered_races'] = array ();
$GLOBALS['char_buffered_race_types'] = array(); 


function getRassen() {
	return getRaces();
}

/**
 * Diese Funktion gibt ein array mit allen rassen aus 'id' => array('name', 'type', ...);
 */
function getRaces() {
	if(count($GLOBALS['char_buffered_races']) == 0) {
		$qry = db_query('select * from rassen');
		// FIXME: This is a simulated SQL Request!
		while($race = mysqli_fetch_assoc($qry)) {
			$GLOBALS['char_buffered_races'][$race['id']] = $race;
		}
	}
	return $GLOBALS['char_buffered_races'];
}

function getRassenTypeMapping() {
	return getRaceTypes();
}

function getRaceTypes(){
	if(count($GLOBALS['char_buffered_race_types']) == 0) {
		$qry = db_query('select * from rassen_type');
		while($racetype = mysqli_fetch_assoc($qry)) {
			$GLOBALS['char_buffered_race_types'][$racetype['id']] = $racetype;
		}
	}
	return $GLOBALS['char_buffered_race_types'];
}

function getRaceById($id) {
	$races = getRassen();
	return $races[$id];
}

function getRaceByName($name) {
	// TODO: Improve performance for this
	$races = getRaces();
	foreach($races as $race) {
		if($race['name'] == $name) {
			return $race;
		}
	}
	return NULL;
}

function getRacesByType($type) {
	$races = getRaces();
	$result = array();
	foreach($races as $race) {
		if($race['type'] == $type['id']) {
			$result[] = $race;
		}
	}
	return $result;
}

function getRaceTypeById($id) {
	$types = getRaceTypes();
	return $types[$id];
}

function getRaceTypeName($name) {
	$types = getRaceTypes();
	foreach($types as $type) {
		if($type['name'] == $name) {
			return $type;
		}
	}
	return NULL;
}

function getSpecialRaceIds() {
	$races = getRaces();
	$result = array();
	foreach($races as $race) {
		if($race['special']) {
			$result[] = $race;
		}
	}
	return $result;
}

#print_r(getRassen());

#print_r(getRaceById(1));

#print_r(getRaceByName('Dämon'));

