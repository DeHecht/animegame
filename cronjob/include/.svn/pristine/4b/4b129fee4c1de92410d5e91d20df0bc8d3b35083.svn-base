<?php
/*
 * Created on 20.01.2010
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once(ROOT_PATH.'/include/char.inc.php');

/**
 *
 * @param unknown_type $title
 * @param unknown_type $charid
 * @param unknown_type $userid
 * @return boolean if the insert was successful
 */
function insertIntoHallOfFame($title, $charid, $userid){
	$row = mysqli_fetch_assoc(db_query('Select max(id) as round from online'));
	$char = getChar($charid);
	$user = mysqli_fetch_assoc(db_query('Select * from user where id = '.$userid));

	if(!$row || is_null($char) || is_null($user)){
		return false;
	}

	return insertIntoHallOfFameFull($row['round'], $title, $charid, $char['name'], $userid, $user['nickname'], 'now()');
}

/**
 *
 * @param unknown_type $round
 * @param unknown_type $title
 * @param unknown_type $charid
 * @param unknown_type $charname
 * @param unknown_type $userid
 * @param unknown_type $username
 * @param unknown_type $date (Whatch out date is not escaped!!)
 * @return unknown_type
 */
function insertIntoHallOfFameFull($round = '', $title = '', $charid = '', $charname = '', $userid = '', $username = '', $date = '', $tournament_id = ''){
	// These Entries may not be ''
	if(!is_numeric($round) || $charname == '' || $username == '' || $date == '' || $title == ''){
		echo '('.$round.') ('.$charname.') ('.$username.')'.') ('.$date.')'.') ('.$title.')'.'<br>';
		return false;
	}
	$columns = 'art, runde, datum, charname, username';
	$values  = '\''.$title.'\','.$round.','.$date.',\''.$charname.'\',\''.$username.'\'';

	if(is_numeric($charid)){
		$columns .= ',charid';
		$values .= ','.$charid;
	}
	if(is_numeric($userid)){
		$columns .= ',userid';
		$values .= ','.$userid;
	}
	if(is_numeric($tournament_id)) {
		$columns .= ',tournament_id';
		$values .= ','.$tournament_id;
	}
	
	$sql = 'INSERT INTO highscore('.$columns.') values('.$values.')';
	echo $sql.'<br>';
	silent_query($sql);
	return db_affected_rows() > 0;
}

function getHallOfFameEntryCount($title, $userid) {
	$row = mysqli_fetch_assoc(db_query('Select max(id) as round from online'));
	return getHallOfFameEntryCountFull($row['round'],$title, $userid);
}

function getHallOfFameEntryCountFull($round = '', $title = '', $userid = '') {
	if(!is_numeric($round) && !is_numeric($userid)) {
		return 0;
	}
	$sql = 'SELECT COUNT(*) FROM highscore WHERE art=\''.$title.'\' AND runde='.$round.' AND userid='.$userid;
//	echo $sql .'<br>';
	$row = mysqli_fetch_row(db_query($sql));
	return $row[0];
}

?>
