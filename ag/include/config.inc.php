<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

/*
 * Hier muss echt mal aufgeräumt werden
 * ne Menge altlasten hier
 */

// Immer den ganzen Pfad angeben, dann hat man es einfacher ^^
include_once (ROOT_PATH . '/include/config/db.inc.php');
include_once (ROOT_PATH . '/include/config/server.inc.php');
include_once (ROOT_PATH . '/include/config/settings.inc.php');
include_once (ROOT_PATH . '/include/sqlwrapper.inc.php');
include_once (ROOT_PATH . '/include/speed_config.inc.php');
include_once (ROOT_PATH . '/include/online.inc.php');
include_once (ROOT_PATH . '/include/parse.inc.php');
include_once (ROOT_PATH . '/include/user.inc.php');
include_once (ROOT_PATH . '/include/usergroup.inc.php');

// Referer statistics (no use for this in the moment)
//$rs = strpos($_SERVER['HTTP_REFERER'],'//');
//if($rs !== FALSE){
//	$rs += 2;
//	$ls = strpos($_SERVER['HTTP_REFERER'],'/', $rs);
//	if($ls !== FALSE){
//		$referer = substr($_SERVER['HTTP_REFERER'],$rs, $ls-$rs);
//	} else{
//		$referer = substr($_SERVER['HTTP_REFERER'],$rs);
//	}
//}
//
//db_query('Update referer set anzahl = anzahl + 1 where url = \''.$referer.'\' and datum = CURRENT_DATE');
//if(db_affected_rows() == 0){
//	db_query('Insert into referer(url, anzahl, datum) values(\''.$referer.'\', 1, CURRENT_DATE)');
//}

$name = validateName($_COOKIE['name']);
$passwort = validateString($_COOKIE['passwort']);
$usergroups = getUserGroups($name);

if(!isUserInGroup($usergroups, ADMIN) && time() < mktime(18, 0, 0, 1, 6, 2012)) {
	$passwort = null;
}

if(!checkCookiePassword($name, $passwort)){
	$POLOE = 1;
	$user_ida = array();
} else {
	$user_ida = mysqli_fetch_assoc(db_query("SELECT * FROM user WHERE nickname='$name' LIMIT 1"));
	db_query('UPDATE user SET ip=\'' . $_SERVER['REMOTE_ADDR'] . '\', online_zeit=now() WHERE id='.$user_ida['id']);
	if ($online_rekord_1_0['anzahl'] < $online_r) {
		db_query('UPDATE online SET anzahl='.$online_r.', datum=now() WHERE id=1');
	}
}

$banned_accs = mysqli_num_rows(db_query("SELECT id FROM user WHERE ip!='' AND ip='$user_ida[ip]' AND id!='$user_ida[id]' LIMIT 2"));

if ($user_ida['id'] != null) {
	$as = 'profil';
}

$pic_g31 = 75; ///////////Bild Groesse

$GLOBALS['user_array'] = $user_ida;

