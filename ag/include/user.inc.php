<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH . '/include/config/server.inc.php');
include_once(ROOT_PATH.'/include/cheater.inc.php');

$GLOBALS['user_buffered_instances'] = array ();


function getUser($userid, $buffer_enabled = true) {
	// Fehlerkontrolle
	if(!is_numeric($userid)){
		return null;
	}
	// 	Wenn Char nicht im Puffer ist, oder nicht gepuffert werden soll
	if ($GLOBALS['user_buffered_instances'][$userid] == null || !$buffer_enabled) {
		$user = mysqli_fetch_assoc(db_query('SELECT *, (online_zeit between TIMESTAMPADD(Minute, -15, now()) and now()) as online FROM user WHERE id = '.$userid));
		$GLOBALS['user_buffered_instances'][$userid] = $user;
	}
	return $GLOBALS['user_buffered_instances'][$userid];
}

function isUserOnline($userid) {
	$user = getUser($userid);
	return $user['online'];
}

/**
 *
 * Returns the user of the Character. (attributes of the user-table should be added here if needed)
 * @param unknown_type $charid
 * @param unknown_type $buffer_enabled
 */
function getOwnerOfChar($charid, $buffer_enabled = true) {
	// Fehlerkontrolle
	if(!is_numeric($charid)){
		return null;
	}

	$user = mysqli_fetch_assoc(db_query('SELECT u.id FROM user u INNER JOIN chars ch ON ch.besitzer=u.id WHERE ch.id = '.$charid));
	return getUser($user['id']);
}

function addMoneyToUser($userid, $amount) {
	db_query('update user set geld = geld + ' . $amount . ' WHERE id = ' .$userid);
}

/**
 * Gibt den wirklichen Geldbetrag des Users zurück (Abzug von Auktions-Aktionen)
 * @param user id
 */
function getRelevantMoney($userid) {
	$user = getUser($userid);
	$auktionensumme = mysqli_fetch_assoc(db_query('SELECT SUM(aktuellesgebot) as summe FROM auktion WHERE bieter = ' . $user['id'] . ' GROUP BY bieter'));
	$auktionensumme = $auktionensumme['summe'];

	$qry = db_query('SELECT max(range_to) FROM inverse_auktion_gebote WHERE bieter = '.$userid.' group by auktionsid');
	
	while( $inverse_summe = mysqli_fetch_row($qry) ) {
		$auktionensumme += $inverse_summe[0];
	}	
	
	if ($auktionensumme == 0) {
		$money = $user['geld'];
	} else {
		$money = $user['geld'] - $auktionensumme;
	}
	return $money;
}

function checkSessionPasswort($userid, $password){
	$sql = 'SELECT passwort from user where id = \''.$userid.'\'';
	$row = mysqli_fetch_assoc(db_query($sql));
	if($row['passwort'] != null){
		//		echo $password.' == '.$row['passwort'];
		return $password == $row['passwort'];
	} else{
		return false;
	}
}


function checkCookiePassword($username, $password){
	$sql = 'SELECT passwort from user where nickname = \''.$username.'\'';
	$row = mysqli_fetch_assoc(db_query($sql));
	if($row['passwort'] != null){
		return $password == $row['passwort'];
	} else{
		return false;
	}
}

function checkLoginPassword($username, $password){
	// We should now use the BCRYPT algo to store passwords
	$pw = encryptPassword($password);
	
	$sql = 'SELECT SHA1(AES_ENCRYPT(\''.$password.'\',\''.$GLOBALS['PW_AES_KEY'].'\')) as encrypt_password, passwort from user where nickname = \''.$username.'\'';
	//	echo $sql.'<br>';
	$row = mysqli_fetch_assoc(db_query($sql));
	if($row){
		if($row['passwort'] == $pw) {
			return true; // already bcrypt based!
		}
		if($row['encrypt_password'] == $row['passwort'] || md5($password) == $row['passwort']){
			setPassword($username, $password);
			return true;
		}
	}
	return false;
}

function setCookies($nick_name, $password){
	$pw = encryptPassword($password);
	checkCookies($nick_name, $_COOKIE['yps']);
	setcookie('name',$nick_name,time()+864000);
	setcookie('passwort',$pw,time()+864000);
	setcookie('yps',$nick_name.','.md5($nick_name),time()+864000);
}

function setPassword($username, $password){
	$pw = encryptPassword($password);
	$sql = 'UPDATE user set passwort = \''.$pw.'\' where nickname = \''.$username.'\'';
	//	echo $sql.'<br>';
	db_query($sql);
}

function encryptPassword($password){
	$pw = password_hash($password, PASSWORD_BCRYPT, array('salt' => $GLOBALS['PW_AES_KEY']));
	return $pw;
}

function getUserMetaData($userid) {
	$sql = 'SELECT * FROM user_meta_data WHERE user_id = ' . $userid;
	$qry = db_query($sql);

	$result = array();
	while ($row = mysqli_fetch_assoc($qry)) {
		$result[$row['key']] = $row['value'];
	}
	return $result;
}

function setUserMetaDataEntry($userid, $key, $value) {
	$sql = 'UPDATE user_meta_data SET `value` = \''.$value.'\' WHERE user_id = \''.$userid.'\' and `key` = \''.$key.'\'';
	$qry = db_query($sql);
	if(!qry)
		return;

	if(db_affected_rows() > 0)
		return;
	

	$sql = 'SELECT `value` FROM user_meta_data WHERE user_id = \''.$userid.'\' and `key` = \''.$key.'\'';
	if ( mysqli_num_rows(db_query($sql)) == 0)
	{
		$sql = 'INSERT INTO user_meta_data(user_id, `key`, `value`) values (\''.$userid.'\',\''.$key.'\',\''.$value.'\')';
		$qry = db_query($sql);
	}
}


?>
