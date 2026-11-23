<?php
/*
 *
 * @copyright (c) 2011 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once (ROOT_PATH. '/include/config/db.inc.php');
include_once (ROOT_PATH.'/include/sqlwrapper.inc.php');


$GLOBALS['definesInitialized'] = FALSE;
$GLOBALS['definesEditable'] = array();

function initializeDefines() {
	$sql = 'SELECT * from defines';
	$qry = db_query($sql);
	while($row = mysqli_fetch_assoc($qry)) {
		if(!defined($row['key'])) {
			define($row['key'], $row['value']);
			$GLOBALS['definesEditable'][$row['key']] = $row['editable'];
		}
	}
	$GLOBALS['definesInitialized'] = TRUE;
}


function defineIfNotDefined($key, $value, $editable = TRUE) {
	if(!$GLOBALS['definesInitialized']) {
		initializeDefines();
	}

	if(!defined($key)) {
		// we seem that we need to insert this key - value pair!
		$sql = 'INSERT INTO defines(`key`, `value`, `editable`) values(\''.$key.'\', \''.$value.'\', \''.$editable.'\')';
		db_query($sql);
		define($key, $value);
	} else if($GLOBALS['definesEditable'][$key] != $editable){
		// if the editableness of this value has been changed, adjust the database ;)
		$sql = 'UPDATE defines SET editable = ' . ($editable?'1':'0') . ' WHERE `key` = \'' .$key.'\'';
//		echo $sql.'<br>';
		db_query($sql);
	}
}


function getDefines($prefix = NULL) {
	if(!$GLOBALS['definesInitialized']) {
		initializeDefines();
	}
	
	$appendix = NULL;
	// readout the database (where editable true)
	if($prefix === NULL) {
		$appendix = '';
	} else {
		$appendix = 'AND key like \''.$prefix.'%\'';
	}
	
	$sql = 'SELECT * FROM defines WHERE editable = 1 ' . $appendix;
	$qry = db_query($sql);
	
	if(!$qry) {
		return 'Fehler in SQL-Anweisung';
	}
	
	$result = array();
	while($row = mysqli_fetch_assoc($qry)) {
		$result[] = $row;
	}
	
	return $result;
}

function setDefine($key, $value) {
	if(!$GLOBALS['definesInitialized']) {
		initializeDefines();
	}
	// set where editable true
	$sql = 'UPDATE defines SET `value` = \''.$value.'\' WHERE `key` = \''.$key.'\' AND editable = TRUE';
	$qry = silent_query($sql);
	if(!$qry || db_affected_rows() == 0) {
		return 'Konnte den define ' . $key . ' nicht auf ' . $value . ' setzen';
	}
	return NULL;
}


?>
