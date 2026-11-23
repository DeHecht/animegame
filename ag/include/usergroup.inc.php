<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once (ROOT_PATH . '/include/defines.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/sqlwrapper.inc.php');
include_once(ROOT_PATH.'/include/user.inc.php');

//Konstanten
defineIfNotDefined('ADMIN', 1);
defineIfNotDefined('TESTER', 2);
defineIfNotDefined('ENTWICKLER', 3);
defineIfNotDefined('DESIGNER', 4);
defineIfNotDefined('WERBUNG_AN', 5);
defineIfNotDefined('INAKTIV_LOESCHSCHUTZ', 6);
defineIfNotDefined('AKTIV_LOESCHSCHUTZ', 7);
defineIfNotDefined('AKTIV_CHAT', 8);
defineIfNotDefined('MODERATOREN', 9);


function getGroupUsers($group) {
	$user_array = array();
	if (is_numeric($group)) {
		$sql = 'SELECT ugz.user_id FROM user_gruppe_zuordnung as ugz
                                           INNER JOIN user_gruppe as ug ON ugz.gruppen_id=ug.gruppen_id
                                           WHERE ug.gruppen_id = \''.$group.'\'';
		$qry = db_query($sql);
		while( $row = mysqli_fetch_assoc($qry) ) {
			$user_array[] = getUser($row['user_id']);
		}
	}

	return $user_array;
}

function getUserGroups($user) {
	$qry = null;
	$groups = array();

	//prüfen welcher wert für user steht (id oder name)
	if(is_numeric($user)) {
		$test = 'SELECT ug.gruppen_name, ug.gruppen_id FROM user_gruppe_zuordnung as ugz
							INNER JOIN user_gruppe as ug ON ugz.gruppen_id=ug.gruppen_id
							WHERE ugz.user_id = \''.$user.'\'';
		$qry = db_query($test);
	} else if (validateString($user) != null) {
		$test = 'SELECT ug.gruppen_name, ug.gruppen_id FROM user_gruppe_zuordnung as ugz
							INNER JOIN user_gruppe as ug ON ugz.gruppen_id=ug.gruppen_id
							INNER JOIN user as u ON u.id=ugz.user_id
							WHERE u.nickname = \''.$user.'\'';
		$qry = db_query($test);
	}

	$i = 0;
	if($qry) {
		while ($result = mysqli_fetch_assoc($qry)) {
			$groups[] = array("gruppen_id" => $result['gruppen_id'], "gruppen_name" => $result['gruppen_name']);
		}
	}

	return $groups;
}

function isUserInGroup(array $usergrouparray, $group) {
	if($group == null || count($usergrouparray) == 0 || $usergrouparray == null) {
		return false;
	}
	for($i = 0; $i < count($usergrouparray); $i++) {
		if($usergrouparray[$i]['gruppen_id'] == $group) {
			return true;
		}
	}
	return false;
}

function isUserInGroupSimple($user, $group) {
	$groupArray = getUserGroups($user);
	return isUserInGroup($groupArray, $group);
}

function removeUserFromGroup($user, $group) {
	if($user !== NULL) {
		if ($group !== NULL && $group >= 0) {
			$qry = 'DELETE FROM user_gruppe_zuordnung WHERE user_id in (SELECT id FROM user WHERE nickname = \''.$user.
			'\') AND gruppen_id = '.$group.';';
			db_query($qry);
			return true;
		}
	}
	return false;
}

function addUserToGroup($user, $group) {
	if($user !== NULL) {
		if ($group !== NULL && $group >= 0) {
			$qry = 'INSERT INTO user_gruppe_zuordnung(user_id, gruppen_id)
			VALUES((SELECT id FROM user WHERE nickname = \''.$user.'\'), '.$group.');';
			db_query($qry);
			return true;
		}
	}
	return false;
}
?>
