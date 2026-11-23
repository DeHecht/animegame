<?php
/*
 * Created on 11.08.2008
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

# Um die Datenbank nutzen zu koennen muss eine db.inc.php erstellt werden, bitte diese nicht in die Versionskontrolle einfügen!!

#####Online Bitte für localhost eintragen!!
$db_host = "Server";
$db_user ="username";
$db_passwort ="password";
$db_name = "databasename";

$main_db_name = "otherdatabasename";
$GLOBALS['mumble_db_name'] = "anotheradatabasename";

if(!isset($conn)){
	$GLOBALS['main_db'] = new mysqli($db_host, $db_user, $db_passwort, $db_name);
}
?>