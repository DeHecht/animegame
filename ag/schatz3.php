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
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/abholcodes.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/semaphore.inc.php');
include_once(ROOT_PATH.'/include/kampf_anzeige.inc.php');
include_once(ROOT_PATH.'/include/schatz.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$char_id = validateUnsignedInteger($_GET['char_id'], null);
$ressource = 'User:'.$user_ida['id'];

// Unkritisch
$code = $_GET['code'];
$abort = $_GET['abort'];
$true = $_GET['true'];

if(!is_numeric($char_id)){
	displayErrorMessage(NULL,'Fehler aufgetreten ... (ID des Chars keine Zahl!)', displayHistoryBackLink());
	exit;
}
if(!semaphoreUP($ressource)){ // TRUE, diese Anfrage darf bearbeitet werden
	displayErrorMessage(NULL,'Verarbeitung gerade in Gange, bitte warten...', displayHistoryBackLink());
	exit;
}

$char = getChar($char_id);

if($char['status'] != 'Schatz Suche') {
	displayErrorMessage(NULL,'Der Charakter ist nicht auf Schatzsuche', displayHistoryBackLink());
	semaphoreDown($ressource);
	exit;
}
if(!isUserOwnerOf($user_ida['id'],$char_id)) {
	displayErrorMessage(NULL,'Der Char ist nicht deiner!', displayHistoryBackLink());
	semaphoreDown($ressource);
	exit;
}

if(!checkAbholcode($char_id, $code)){
	displayErrorMessage(NULL,'Der Abholcode ist falsch!', displayHistoryBackLink());
	semaphoreDown($ressource);
	exit;
}

if($abort == 'cancel'){
	$kampf_a = mysqli_fetch_array(db_query('select Timestampdiff(Second,now(),dauer) as dauer, Timestampdiff(Second,now(),cancel) as cancel from quests where charid = '.$char_id));
	$zeit_in_sek = $kampf_a['dauer'];
	$cancel_zeit_in_sek = $kampf_a['cancel'];

	if ($cancel_zeit_in_sek <= 0) {
		if ($true == 1) {
	  	interruptSchatzSuche($char_id);
	  	displayErrorMessage(NULL,'Die Schatzsuche wurde abgebrochen!', displayIndexBackLink());
		} else {
			echo '<p>Schatz Suche wirklich abbrechen?</p>';
			echo '<p><a href="./schatz3.php?char_id='.$char_id.'&code='.$code.'&abort=cancel&true=1">Ja</a> | <a href="./index.php?as=abholen&ab=Schatz Suche&char_id='.$char_id.'">Nein</a></p>';
		}
	} else {
		displayErrorMessage(NULL,'Die Schatzsuche kann noch nicht  abgebrochen werden!', displayIndexBackLink());
	}
} else{
	if(($text = displaySchatzSuche($char)) !== null){
		displayErrorMessage(NULL,$text, displayHistoryBackLink());
	}
}


semaphoreDown($ressource); // Gebe die Useraktionen wieder frei :)

?>
