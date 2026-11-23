<?php
/*
 *
 * @copyright (c) 2011 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once('path.inc.php'); // get the path ;)
include_once (ROOT_PATH . '/include/config.inc.php');
include_once (ROOT_PATH . '/include/char.inc.php');
include_once(ROOT_PATH.'/include/kampf_anzeige.inc.php');
include_once (ROOT_PATH . '/include/event.inc.php');
include_once (ROOT_PATH . '/include/parse.inc.php');

function fetchProcessing($user, $char, $type) {
	$char_id = $char['id'];
	$event_ids = getEventIdsToFetch($char_id, $type);
	if(count($event_ids) == 0) {
		return 'Es gibt nix zum Abholen!<br>';
	}
	foreach ($event_ids as $event_id) {
		$fight_ids = abholenChar($user, $event_id, $char_id);
		if(!is_array($fight_ids)) {
			echo $fight_ids . '<br>';
			continue;
		}
		foreach ($fight_ids as $event_fight_id) {
			displayEventFight($event_id, $event_fight_id);
		}
		
		// only continue for clan-fights
		if ( $type != EVENT_CLAN_FIGHT ) {
			break;
		}
	}
	return NULL;
}

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="design/FightStyle.css">
</head>
<body>
<?php
$char_id = validateUnsignedInteger($_GET['char_id'], null);
$type = string2EventType(validateStringCritical($_GET['type'], null));
$char = getChar($char_id);

if($char['besitzer'] != $user_ida['id']) {
	echo 'Du kannst den char nicht abholen, da er dir nicht geh&ouml;rt!';
} else {
	$res = fetchProcessing($user_ida, $char, $type);
	if($res != NULL) {
		echo $res;
	}
}

//function abholenChar(array $user, $event_id, $char_id) {
//function getEventIdsToFetch($charid) {


?>
</body>
</html>
