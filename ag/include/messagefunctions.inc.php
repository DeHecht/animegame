<?php
/*
 * Created on 20.08.2007
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

/******************************************************************************
 * Dieser Skript enthält Funktionen für das versenden von Nachrichten wichtig ist
 * Dabei werden die Info's ueber einen bbCode umgesetzt
 *******************************************************************************
 * Autor: Pascal Proksch
 * Erstellungsdatum: 20.08.2007
 * Zuletzt verändert: 03.10.2007
 *******************************************************************************/

include_once(ROOT_PATH.'/include/config/db.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/bann.inc.php');
include_once(ROOT_PATH.'/include/sqlwrapper.inc.php');
include_once(ROOT_PATH.'/include/random.inc.php');
include_once(ROOT_PATH.'/include/user.inc.php');

function sendMessage($von, $besitzer, $betreff, $text){
	//	$betreff = $betreff;

	if (is_numeric($von)) {
		$von = getUser($von)['nickname'];
	}

	$text = urldecode($text);
	$text = encodeNoHTMLWithBB($text);
	$betreff = urldecode($betreff);
	$betreff = encodeNoHTMLWithBB($betreff);

	$sql = 'INSERT INTO nachricht(von, besitzer, betreff, text) VALUES (\''.$von.'\', '.$besitzer.', \''.$betreff.'\', \''.$text.'\')';
	//	echo $sql.'<br>';
	db_query($sql);
	return db_affected_rows() != 0;
}

function colorize($text){
	$sum = md5($text);
	return '<span style="color:#'.substr($sum,0,2).substr($sum,2,2).substr($sum,4,2).'">'.$text.'</span>';
}



// Der Text MUSS schon vorher überprüft werden!
function sendChatMessage($user,$text, $usergroups){
	//		$text = urldecode($text);
	//		echo $text.'<br>';
	// So nun erstmal sehen ob der user eine spamstrafe besitzt!!
	if(isSpammer($user['id'])){
		return;
	}

	$text = encodeNoHTMLWithBB($text);

	// Nun pruefen, ob der User ein spammer ist
	becomeSpammer($user['id'],$user['nickname'],$text);

	if(strpos($user['nickname'], 'anon_') === 0) {
		$nick = $user['nickname'];
	} else {
		$nick = displayUserNameByID($user['id']);
	}
	if($text != '' && !is_null($text)){
		db_query('INSERT ag_chat SET nachricht=\''.$text.'\', user=\''.$nick.'\'');
	}
}

function getChatMessages($userid, $messageid = 0, $asc = true, $limit = 25){
	if(!is_numeric($messageid)){
		$messageid = 0;
	}
	$row = mysqli_fetch_row(db_query('Select max(id), count(id) from ag_chat'));
	$maxid = $row[0];
	$count = $row[1];
	if($maxid === null) {
		$maxid = 0;
	}
	if($count > $limit) {
		$minid = $maxid - $limit;
	} else {
		$minid = $maxid - $count;	
	}

	if($messageid > $minid){
		$minid = $messageid;
	}
	

	$result = db_query('SELECT *, Minute(zeit) as m, Hour(zeit) as h FROM ag_chat WHERE id > '.$minid.' ORDER BY ID ASC LIMIT '.$limit);
	while ($row = mysqli_fetch_assoc($result)) {
		$h = $row['h'];
		if($h < 10){ $h = '0'.$h;}
		$m = $row['m'];
		if($m < 10){ $m = '0'.$m;}
		if(!$asc) {
			$resultstring = '<div>'.$h.':'.$m.' '.$row['user'].': '.$row['nachricht'].'</div>'.$resultstring;
		} else {
			$resultstring .= '<div>'.$h.':'.$m.' '.$row['user'].': '.$row['nachricht'].'</div>';
		}
	}
	if(isSpammer($userid)){
		if(!$asc) {
			$resultstring = '<div style="color:red">SPAM-BLOCKIERUNG!!</div>'.$resultstring;
		} else {
			$resultstring.= '<div style="color:red">SPAM-BLOCKIERUNG!!</div>';
		}
	}
	return $maxid.';'.$resultstring;
}

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

//$test = "Dies ist eine [b]beliebige[/b] Nachricht mit einem auktionslink [a]testlink.html[/a]";
//echo decodeMessage($test).'<br>';

//$test = '<a href="test.html">Test</a>';
//echo encodeMessage($test).'<br>';


function sendInviteToClanMessage($leaderid, $userid) {

}

?>
