<?php
/*
 * Created on 08.03.2009
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH. "/include/random.inc.php");

// + offset in Minuten
function insertUserTickerMessage($userid, $message, $offset = 0){
	if($offset == 0){
		$timestamp = 'now()';
	} else{
		$timestamp = '\''.date('Y-m-d H:i:s', time() + $offset * 60).'\'';
	}
	for($msgid = 0; $msgid < 100000; $msgid += mt_random_wrapper(1,1000)){
		$sql = 'INSERT INTO user_ticker(userid, date, msgid, text) values('.$userid.', '.$timestamp.', '.$msgid.', \''.$message.'\')';
		db_query($sql);
		if(db_affected_rows() == 1){
//			echo 'MYSQL success: '.$sql;
			return;
		}
	}
//	echo 'MYSQL-Error: '.$sql;
}

function insertClanTickerMessage($clanid, $message, $offset = 0){
	if($offset == 0){
		$timestamp = 'now()';
	} else{
		$timestamp = '\''.date('Y-m-d H:i:s', time() + $offset * 60).'\'';
	}
	for($msgid = 0; $msgid < 100000; $msgid += mt_random_wrapper(1,1000)){
		$sql = 'INSERT INTO clan_ticker(clanid, date, msgid, text) values('.$clanid.', now(), '.$msgid.', \''.$message.'\')';
		db_query($sql);
		if(db_affected_rows() == 1){
//			echo 'MYSQL success: '.$sql;
			return;
		}
	}
//	echo 'MYSQL-Error: '.$sql;
}

function displayMoney($value){
	return number_format($value,0,'','.').' &#165;';
}


?>