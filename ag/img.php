<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/img.inc.php');
// Die möglichen Zeichen

// Einziger Übergabewert (und der ist auch noch kritisch!!)
$id = validateString($_GET['id'], null);

if (isset($user_ida['id'])) {
	displayImage($id, $user_ida);
}

?>
