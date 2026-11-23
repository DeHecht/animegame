<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once('path.inc.php'); // get the path ;)
include_once (ROOT_PATH . '/include/config/speed_config.inc.php');
include_once (ROOT_PATH . '/include/config/db.inc.php');
include_once(ROOT_PATH . '/include/designfunctions.inc.php');
include_once(ROOT_PATH . '/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH . '/include/parse.inc.php');
include_once(ROOT_PATH . '/include/user.inc.php');

$nick_name = validateName($_POST['nick_name']);
$pw_1 = validateString($_POST['pw_1']);

$ip_con = mysqli_fetch_array(db_query("SELECT nickname FROM user WHERE ip='$REMOTE_ADDR' AND nickname !='$nick_name' LIMIT 1"));

if (!checkLoginPassword($nick_name, $pw_1)) {
	displayErrorMessage(NULL,'Username oder Passwort falsch!', displayHistoryBackLink());
	exit;
}

// MumbleAddon!!
if(isset($GLOBALS['mumble_db_name'])){
	include_once(ROOT_PATH . '/include/mumble.inc.php');
	insertUser($_POST['nick_name'], $_POST['pw_1']);
}

setCookies($nick_name, $pw_1);
header ("Location: index.php");
?>