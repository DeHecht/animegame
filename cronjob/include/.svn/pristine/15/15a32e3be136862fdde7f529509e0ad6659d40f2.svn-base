<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once(ROOT_PATH.'/include/char.inc.php');

defineIfNotDefined('STATUS_FREI', 'Frei');

//Dadurch kriege ich detailierte infos, sollte ein mysql query mal inkorrekt sein!
function safe_query($query="") {
	if(empty($query)) return false;
	if(DEBUG == "OFF") $result = db_query($query) or die('Query failed!');
	else {
		$result = db_query($query) or die('Query failed: '
												.'<li>errorno='.mysqli_errno($GLOBALS['main_db'])
												.'<li>error='.mysqli_error($GLOBALS['main_db'])
												.'<li>query='.$query);
	}
	return $result;
}


//komplette row des chars auslesen und in einem array speichern
function chardaten($char_id,$name,&$char_1,&$hp1,&$mp1,&$exp,&$fehler) {

	$char_1 = getChar($char_id);
	$hp1 = explode(',', $char_1['hp']);
	$mp1 = explode(',', $char_1['mp']);
	$exp = explode(',', $char_1['exp']);
	$user = mysqli_fetch_array(db_query("SELECT id FROM user WHERE nickname='$name' LIMIT 1"));
	if($char_1['besitzer'] != $user[0]) {
		$fehler= 'Dieser Char geh&ouml;rt nicht dir!';
	}
	return array($char_1,$hp1,$mp1,$exp,$fehler);
}

/**
 * 
 * Überprüft den Status des Charakters
 * 
 * @param Int $char_id: Die ID des zu überprüfenden Charakters
 * @param String $status: Der Status auf den geprüft wird 
 */
function checkCharStatus($char_id, $status) {
	$char = getChar($char_id, false);
	return $char['status'] == $status;
}

?>