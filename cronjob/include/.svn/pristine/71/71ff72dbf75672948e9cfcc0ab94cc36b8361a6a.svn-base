<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once (ROOT_PATH.'/include/defines.inc.php');
include_once (ROOT_PATH.'/include/sqlwrapper.inc.php');


//Set-Arten
defineIfNotDefined('NORMALKAMPF', 1);
defineIfNotDefined('CLANKAMPF', 2);

//Anzahl der Runden pro Kampf
defineIfNotDefined('KAMPF_RUNDEN', 10);

$GLOBALS['char_buffered_attackset'] = array();

function createAttackSet($char_id, $type) {
	if(!is_numeric($char_id) || !is_numeric($type)) {
		return false;
	}

	for($i = 1; $i < KAMPF_RUNDEN + 1; $i++) {
		$values .= '('.$char_id.',-1,'.$type.','.$i.')';
		if($i < 10) {
			$values .= ',';
		}
	}

	$qry = 'INSERT INTO attackenset VALUES '.$values.';';
	return db_query($qry);
}

function deleteAttackSet($char_id, $type) {
	if(!is_numeric($char_id) || !is_numeric($type)) {
		return false;
	}

	$qry = 'DELETE FROM attackenset WHERE char_id = '.$char_id.' AND type = '.$type.';';
	return db_query($qry);
}

function getAttackSet($char_id, $type, $buffer_enabled = true) {
	if(!is_numeric($char_id) || !is_numeric($type)) {
		return false;
	}
	if (isset($GLOBALS['char_buffered_attackset'][$char_id]) && isset($GLOBALS['char_buffered_attackset'][$char_id][$type]) && $buffer_enabled) {
		return $GLOBALS['char_buffered_attackset'][$char_id][$type];
	}
	$qry = db_query('SELECT attack_id, round FROM attackenset WHERE char_id = '.$char_id.' AND type = '.$type);

	$set = array();
	while ($result = mysqli_fetch_assoc($qry)) {
		$set[$result['round']] = $result['attack_id'];
	}
	$GLOBALS['char_buffered_attackset'][$char_id][$type] = $set;
	return $set;
}

function updateAttackSet($char_id, $type, $newSet) {
	if(!is_numeric($char_id) || !is_numeric($type) || !is_array($newSet) || count($newSet) == 0) {
		return false;
	}
	$oldset = getAttackSet($char_id, $type, false);

	for ($i = 1; $i < KAMPF_RUNDEN + 1; $i++) {
		if($oldset[$i] != $newSet[$i]) {
			$qry = 'UPDATE attackenset SET attack_id = '.$newSet[$i].' WHERE char_id = '.$char_id.' AND type = '.$type.' AND round = '.$i.';';
			db_query($qry);
		}
	}
	unset($GLOBALS['char_buffered_attackset'][$char_id][$type]);
	return true;
}

function getAttackNames($attackSet) {
	if(!is_array($attackSet) || count($attackSet) == 0) {
		return array();
	}

	$qry = db_query('SELECT l.id,a.name FROM attacken a INNER JOIN lernen l ON a.id=l.at_id WHERE l.id in ('.implode(',', $attackSet).');');

	while ($result = mysqli_fetch_assoc($qry)) {
		$set[$result['id']] = $result['name'];
	}

	return $set;
}

function getAttackTypes($attackSet) {
	if(!is_array($attackSet) || count($attackSet) == 0) {
		return array();
	}

	$qry = db_query('SELECT l.id,a.type,a.options FROM attacken a INNER JOIN lernen l ON a.id=l.at_id WHERE l.id in ('.implode(',', $attackSet).');');
	while ($result = mysqli_fetch_assoc($qry)) {
		if($result['type'] == 'generic') {
			$options = json_decode($result['options'], true);
			$set[$result['id']] = $options['type'];
		} else {
			$set[$result['id']] = $result['type'];
		}
	}
	return $set;
}

function validateAttackSet($attackSet) {
	if(!is_array($attackSet) || count($attackSet) == 0) {
		return 'Invalides Attackenset!';
	}

	$types = getAttackTypes($attackSet);
	//attackentypen im set zählen
	$data = array();
	for($i = 1; $i < 11; $i++) {
		if(is_string($types[$attackSet[$i]])) {
			$data[$types[$attackSet[$i]]] += 1;
		}
	}

	//Alle Attackentypen auslesen
	$qry = db_query('SELECT name, anzahl, kategorie FROM attackentyp;');
	$types = array();
	while ($row = mysqli_fetch_assoc($qry)) {
		$types[$row['name']] = array('anzahl' => $row['anzahl'], 'kategorie' => $row['kategorie']);
	}

	//hier passieren mehrere sachen, erst wird geschaut ob die anzahl die max. anzahl übersteigt
	//danach wird die kategorie ausgewertet indem geschaut wird ob in dem kategorie array mehr als 1 eintrag sind
	$kategorie = array();
	foreach ($data as $row => $key) {
		if($types[$row]['anzahl'] < $key) {
			return 'Fehler die Technik vom type '.$row.' darf nur '.$types[$row]['anzahl'].' mal benutzt werden.';
		} else {
			if(is_string($types[$row]['kategorie']) && strlen($types[$row]['kategorie']) > 0) {
				if($kategorie[$types[$row]['kategorie']] == null) {
					$kategorie[$types[$row]['kategorie']] = array($row);
				} else {
					$kategorie[$types[$row]['kategorie']][] = $row;
				}
				if(count($kategorie[$types[$row]['kategorie']])> 1) {
					return 'Fehler sie d&uuml;rfen Techniken vom Type '.$kategorie[$types[$row]['kategorie']][0].' und '.$kategorie[$types[$row]['kategorie']][1].' nicht zusammen nutzen.';
				}
			}
		}
	}
	
	return NULL;
}
?>
