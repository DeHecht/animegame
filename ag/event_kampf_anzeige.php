<?php
/*
 *
 * @copyright (c) 2011 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="design/FightStyle.css">
	</head>
	<body>
		<table width="100%">
			<tr><td align="center" height="20px"><a href="javascript:(window.history.back())">Zur&uuml;ck</a></td></tr>
		</table>
<?php

include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config/db.inc.php');
include_once(ROOT_PATH.'/include/kampf_anzeige.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');

$event_id = validateUnsignedInteger($_GET['event_id'], null);
$event_fight_id = validateUnsignedInteger($_GET['event_fight_id'], null);

if($event_id !== NULL && $event_fight_id !== NULL) {
	displayEventFight($event_id, $event_fight_id);
} else {
	echo 'Man kann die Seite so nicht aufrufen!!<br>';
}
?>
	</body>
</html>
