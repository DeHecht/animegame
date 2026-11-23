<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

# Um die Datenbank nutzen zu koennen muss eine db.php erstellt werden, bitte diese nicht in die Versionskontrolle einfügen!!

#####Online Bitte für localhost eintragen!!
$db_host = "Server";
$db_user ="username";
$db_passwort ="password";
$db_name = "databasename";

$conn = new mysqli($db_host, $db_user, $db_passwort, $db_name);
$GLOBALS['main_db'] = $conn;

//////////////////Animegame NPC
$bebi = "2";
$mihawk = "4";
$brolly = "1";
$flamingo = "3";

?>

