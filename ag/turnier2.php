<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

$nodisturb = 'YES';

include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');


// Um Copy&PasteFehler zu umgehen!
function displaySomething($event_id, $id, $new_max_turnier, $char_nic1, $char_nic2, $colspan = 1) {
// echo '<a href="event_kampf_anzeige.php?event_id='.$event_id.'&event_fight_id='.$id[$z].'"><b>'.$char_nick1[$z].'</b> vs. '.$char_nick2[$z].' -> Runde: '.$round[$z].'</a>';
	?>
		<td width="430" height="150" valign="middle" align="center" colspan="<?php echo $colspan; ?>">
			<a href="event_kampf_anzeige.php?event_id=<?php echo $event_id; ?>&event_fight_id=<?php echo $id[$new_max_turnier]; ?>"><?php echo $char_nic1[$new_max_turnier]; ?></a>
		</td>
		<td width="430"  valign="middle" align="center" colspan="<?php echo $colspan; ?>">
			<a href="event_kampf_anzeige.php?event_id=<?php echo $event_id; ?>&event_fight_id=<?php echo $id[$new_max_turnier]; ?>"><?php echo $char_nic2[$new_max_turnier]; ?></a>
		</td>
	<?php
}

// GET-Section
// Kritisch (SQL-Injections)
$type_id = validateUnsignedInteger($_GET['type_id']);
$id = validateUnsignedInteger($_GET['id']);

// Unkritisch
$charm = $_GET['charm'];

$turnier_daten = NULL;

if (is_numeric($id)) {
	$turnier_daten = mysqli_fetch_assoc(db_query('SELECT * from tournament WHERE id = "'.$id.'"'));
} else {
	$turnier_daten = mysqli_fetch_assoc(db_query('SELECT * from tournament WHERE type = "'.$type_id.'" ORDER BY datum DESC LIMIT 1'));
}

if(!$turnier_daten) {
	displayErrorMessage(NULL,'Turnier fand noch nicht statt!',$back);
}
else if($charm == 1) {
	$event_id = $turnier_daten['event_id'];
	$event_fight_ids = getEventFightIds($event_id);
	$max_turnier = count($event_fight_ids);

	echo '<link rel="stylesheet" type="text/css" href="design/style3.css">';
	$turnier_date = $turnier_daten['datum'];

	$u=0;

	$eventChars = array();
	$char_nick1 = array();
	$char_nick2 = array();
	$char_nic1 = array();
	$char_nic2 = array();
	$char1 = array();
	$char2 = array();
	$win = array();
	$round = array();
	$dauer = array();
	
	foreach ($event_fight_ids as $event_fight_id) {
		$event_fight = getEventFight($event_id, $event_fight_id);

		$host_id = $event_fight['host'];
		$qry = db_query('SELECT event_char_id FROM event_fight_rounds where event_id = '.$event_id.' and event_fight_id = '.$event_fight_id.' and round = 0');

		$char_info1 = NULL;
		$char_info2 = NULL;

		while( $row = mysqli_fetch_row($qry) ) {
			$char_data = $eventChars[$row[0]];
			if ($char_data === NULL) {
				$char_data = getEventChar($event_id, $row[0]);
				$eventChars[$row[0]] = $char_data;
			}
			if ($row[0] == $host_id) {
				$char_info1 = $char_data;
			} else {
				$char_info2 = $char_data;
			}
		}

	        $dauer[] = $event_fight['passed'];

		if ($event_fight['passed'] == 1) {
			$char_nic1[]='<img src="'.$char_info1['char_bild'].'" width="75" height="75" border="0"><br>'.$char_info1['char_name'];
			$char_nic2[]='<img src="'.$char_info2['char_bild'].'" width="75" height="75" border="0"><br>'.$char_info2['char_name'];
		} else {
			$char_nic1[]='<img src="design/bilder/avatare/Char.gif" width="75" height="75" border="0"><br>???';
			$char_nic2[]='<img src="design/bilder/avatare/Char.gif" width="75" height="75" border="0"><br>???';
		}
		$char_nick1[]=$char_info1['char_name'];
		$char_nick2[]=$char_info2['char_name'];
		$char1[]=$char_info1['event_char_id'];
		$char2[]=$char_info2['event_char_id'];

		$win[]=$event_fight['winner'];
		$round[]=getEventFightMetaData($event_id, $event_fight_id)['round'];
		$u++;
		
	}

	$maxs = $max_turnier-1;
	$win_char = $eventChars[$win[$maxs]];
?>

<html>

<body bgcolor="#008080">

<table cellpadding="0" cellspacing="0" width="913" height="0">
	<!-- MSTableType="layout" -->
	<tr>
		<th valign="top" height="32" colspan="8" align="center">Das Turnier am <?php echo $turnier_date; ?></th>
	</tr>

<?php

if( $dauer[count($dauer) - 1] == 1) {
	?>
	<tr>
		<td width="900" height="150" colspan="8" valign="middle" align="center">
			<img src="<?php echo $win_char['char_bild']; ?>" width="75" height="75">
			<br><?php echo $win_char['char_name']; ?>
		</td>
	</tr>
	<?php
} else {
	?>
	<tr>
		<td width="900" height="150" colspan="8" valign="middle" align="center">
			<img src="design/bilder/avatare/Char.gif" width="75" height="75">
		</td>
	</tr>
	<?php
}
	// Keine Ahnung was da angezeigt werden soll
	echo '<tr>';
	$new_max_turnier = $max_turnier-1;
	displaySomething($event_id, $event_fight_ids, $new_max_turnier, $char_nic1, $char_nic2, 4);
	echo '</tr><tr>';
	$new_max_turnier = $max_turnier-3;
	displaySomething($event_id, $event_fight_ids, $new_max_turnier, $char_nic1, $char_nic2, 2);
	$new_max_turnier = $max_turnier-2;
	displaySomething($event_id, $event_fight_ids, $new_max_turnier, $char_nic1, $char_nic2, 2);
	echo '</tr><tr>';
	$new_max_turnier = $max_turnier-7;
	displaySomething($event_id, $event_fight_ids, $new_max_turnier, $char_nic1, $char_nic2);
	$new_max_turnier = $max_turnier-6;
	displaySomething($event_id, $event_fight_ids, $new_max_turnier, $char_nic1, $char_nic2);
	$new_max_turnier = $max_turnier-5;
	displaySomething($event_id, $event_fight_ids, $new_max_turnier, $char_nic1, $char_nic2);
	$new_max_turnier = $max_turnier-4;
	displaySomething($event_id, $event_fight_ids, $new_max_turnier, $char_nic1, $char_nic2);
	echo '</tr>';
?>
</table>
<table cellpadding="0" cellspacing="0" width="913" height="83">
	<!-- MSTableType="layout" -->
	<tr>
		<th valign="middle" height="21" align="center">Vorrunde</th>
	</tr>

<?php
$x=1;
$z= 0;
$k8=0;
$o=0;
while($x < count($char_nick1) - 6) {
	if($o == 0) {
		$mul = '#018B8B';
		$o++;
	} else {
		$mul = '#029393';
		$o--;
	}
	echo '<tr><td height="15" valign="middle" bgcolor="'.$mul.'" align="center">';

	if($dauer[$x - 1] == 1) {
		if($char1[$z] == $win[$z]) {
			echo '<a href="event_kampf_anzeige.php?event_id='.$event_id.'&event_fight_id='.$event_fight_ids[$z].'"><b>'.$char_nick1[$z].'</b> vs. '.$char_nick2[$z].' -> Runde: '.$round[$z].'</a>';
		} else {
			echo '<a href="event_kampf_anzeige.php?event_id='.$event_id.'&event_fight_id='.$event_fight_ids[$z].'">'.$char_nick1[$z].' vs. <b>'.$char_nick2[$z].'</b> -> Runde: '.$round[$z].'</a>';
		}
	} else {
		if($k8 == 0) {
			echo '<a href="event_kampf_anzeige.php?event_id='.$event_id.'&event_fight_id='.$event_fight_ids[$z].'">'.$char_nick1[$z].' vs. '.$char_nick2[$z].' -> Runde: '.$round[$z].'</a>';
			
			$k8++;
		}
	}
	$z++;
	$x++;
	echo '</td></tr>';
}
?>

</table>

</body>

</html>
<?php
}
