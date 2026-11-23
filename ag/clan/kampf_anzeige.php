<html>
<head>
<link rel="stylesheet" type="text/css" href="../design/style3.css">
</head>
<body bgcolor="#008080">
<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once('../path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/clan.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$challenge_id = validateInteger($_GET['challenge'], null);

// Unkritisch
$action = $_GET['action'];

function getEventChars($challenge_id, $event_id) {
	$sql = 'SELECT * FROM clan_challenge_participants ccp INNER JOIN event_chars ec ON ccp.char_id=ec.event_char_id
			INNER JOIN event_fights ef ON ccp.slot=ef.event_fight_id 
			WHERE ccp.clan_challenge_id = '.$challenge_id.' AND ec.event_id= '.$event_id.' AND ef.event_id= '.$event_id.' ORDER BY slot;';
	$qry = db_query($sql);
	$chars = array();
	while($row = mysqli_fetch_array($qry)) {
		$chars[] = $row;
	}
	return $chars;
}

function getChallengeClans($challenge_id) {
	$sql = 'SELECT ccc.id, c.clanname, ccc.elo_change, ccc.exp_change, c.id as clan_id, ccc.points FROM clan_challenge_clans ccc INNER JOIN clan c ON ccc.clan_id=c.id WHERE clan_challenge_id='.$challenge_id.';';

	$qry = db_query($sql);
	$clans = array();
	while($row = mysqli_fetch_array($qry)) {
		$clans[$row['id']] = array('name' => $row['clanname'], 'id' => $row['clan_id'], 'elo' => $row['elo_change'], 'xp' => $row['exp_change'], 'points' => $row['points']);
	}
	return $clans;
}

function validatePicture($picture) {
	if($picture[0] == 'd') {
		return '../'.$picture;
	} else {
		return $picture;
	}
}

function displayChar($char) {
	if(strtotime($char['starting']) < time()) {
		if(strtotime($char['visible']) < time()) {
		echo '<div style="position:relative; top:0px; left:0px;width:75;">';
		if($char['event_char_id'] != $char['winner']) {
			echo '<img src="'.validatePicture($char['char_bild']).'" style="position:absolute; top:0px; left:0px; z-index:1; width:75px;height:75px;border:0px">';
			echo '<img src="../design/bilder/avatare/Lose.gif" style="position:relative; top:0px; left:0px; z-index:2;width:75px;height:75px;border:0px">';
		} else {
			echo '<img src="'.validatePicture($char['char_bild']).'" style="position:relative; top:0px; left:0px; z-index:2;width:75px;height:75px;border:0px">';
		}
		echo '</div>';
		} else {
			echo '<div style="position:relative; top:0px; left:0px;width:75;">';
			echo '<img src="'.validatePicture($char['char_bild']).'" style="position:relative; top:0px; left:0px; z-index:2;width:75px;height:75px;border:0px">';
			echo '</div>';
		}
		echo $char['char_name'];
	} else {
		echo '<div style="position:relative; top:0px; left:0px;width:75;">';
		echo '<img src="../design/bilder/avatare/Char.gif" width="75" height="75" border="0">';
		echo '</div>';
		echo '???';
	}
}

function displayHeader($top, $bottom, $challenge, $clans) {
	$border = 0;
	$challenge_id = $challenge['clan_challenge_id'];
	$event_id = $challenge['event_id'];
	$chars = getEventChars($challenge_id, $event_id);
	$number_chars = count($chars);
	$left = $clans[0]['id'];
	$right = $clans[1]['id'];
	
	echo '<table cellpadding="0" cellspacing="0" width="913" height="0" border="'.$border.'">';
	echo '<tr><th valign="top" height="32" colspan="9" align="center">Clankampf</th></tr>';

	echo '<tr>';
	//erste spalte
	echo '<th width="45%" height="32" valign="middle" align="center">'.$clans[0]['name'].'</th>';
	//zweite spalte
	echo '<td width="5%" valign="middle" align="center" rowspan="3"><b>VS.</b></td>';
	//dritte spalte
	echo '<th width="45%" height="32" valign="middle" align="center">'.$clans[1]['name'].'</th>';
	echo '</tr>';

	//obere Teil
	echo '<tr height = "150">';
	//Darstellen der Bilder auf der linken Seite
	echo '<td><table width="100%" height="100%" cellpadding="0" cellspacing="0" border="'.$border.'"><tr>';
	$width = 100 / $top;
	$max = 0;
	for ($i = 0; $max != $top && $i <= $number_chars; $i++) {
		if($chars[$i]['clan_id'] == $left) {
			echo '<td width="'.$width.'%" valign="middle" align="center">';
			displayChar($chars[$i]);
			echo '</td>';
			$max++;
		}
	}
	echo '</tr></table></td>';
	//Darstellen der Bilder auf der rechten Seite
	echo '<td><table width="100%" height="100%" cellpadding="0" cellspacing="0" border="'.$border.'"><tr>';
	$max = 0;
	$i = 0;
	for ($i = 0; $max != $top && $i <= $number_chars; $i++) {
		if($chars[$i]['clan_id'] == $right) {
			echo '<td width="'.$width.'%" valign="middle" align="center">';
			displayChar($chars[$i]);
			echo '</td>';
			$max++;
		}
	}
	echo '</tr></table></td>';
	echo '</tr>';

	//untere Teil
	echo '<tr height = "150">';
	//Darstellen der Bilder auf der linken Seite
	echo '<td><table width="100%" height="100%" cellpadding="0" cellspacing="0" border="'.$border.'"><tr>';
	$showed = $top;
	$max = 0;
	$i = 0;
	$width = 100 / $bottom;
	for ($i = 0; $max != ($bottom+$showed) && $i <= $number_chars; $i++) {
		if($chars[$i]['clan_id'] == $left) {
			if($max >= $showed) {
				echo '<td width="'.$width.'%" valign="middle" align="center">';
				displayChar($chars[$i]);
				echo '</td>';
			}
			$max++;
		}
	}
	echo '</tr></table></td>';
	//Darstellen der Bilder auf der rechten Seite
	echo '<td><table width="100%" height="100%" cellpadding="0" cellspacing="0" border="'.$border.'"><tr>';
	$max = 0;
	$i = 0;
	for ($i = 0; $max != ($bottom+$showed) && $i <= $number_chars; $i++) {
		if($chars[$i]['clan_id'] == $right) {
			if($max >= $showed) {
				echo '<td width="'.$width.'%" valign="middle" align="center">';
				displayChar($chars[$i]);
				echo '</td>';
			}
			$max++;
		}
	}
	echo '</tr></table></td>';
	echo '</tr>';
	echo '</table>';
}

function getColor($row) {
	if($row % 2 == 0) {
		$color = '#018B8B';
		//$color = '#FFFFFF';
	} else {
		$color = '#029393';
		//$color = '#FF00FF';
	}
	return $color;
}

function displayBody($challenge, $clans, $user) {
	$event_id = $challenge['event_id'];
	$fight_ids = getEventFightIds($event_id);
	$row = 0;
	//checken in welchem clan nun der User sitzt, für die Anzeige von EXP, ELO usw.
	if($user['clan'] == $clans[0]['id']) {
		$i = 0;
	} else {
		$i = 1;
	}

	echo '<table cellpadding="0" cellspacing="0" width="913" height="83" border="0">';
	foreach($fight_ids as $fight_id) {
		$event_fight = getEventFight($event_id, $fight_id);
		$round_data = getEventFightRoundData($event_id, $event_fight['event_fight_id'], 0); // get participants of fight
		$host_char_id = NULL;
		$guest_char_id = NULL;
		foreach($round_data as $key => $value) {
			if($event_fight['host'] == $key) {
				$host_char_id = $key;
			} else {
				$guest_char_id = $key;
			}
		}

		// Key now lets read out the event_chars ...

		$host_char = getEventChar($event_id, $host_char_id);
		$guest_char = getEventChar($event_id, $guest_char_id);

		// Check if the fight is already "visible"
		if(strtotime($event_fight['visible']) < time()) {
			echo '<tr><td width="45%" height="15" valign="middle" bgcolor="'.getColor($row).'" align="right">';
			echo '<a href="../event_kampf_anzeige.php?event_id='.$event_id.'&event_fight_id='.$event_fight['event_fight_id'].'">';
			echo $host_char['char_name'].'</a>';
			echo '</td>';
			echo '<td width="5%" valign="middle" bgcolor="'.getColor($row).'" align="center">vs</td>';
			echo '<td width="45%" valign="middle" bgcolor="'.getColor($row).'" align="left">';
			echo '<a href="../event_kampf_anzeige.php?event_id='.$event_id.'&event_fight_id='.$event_fight['event_fight_id'].'">';
			echo $guest_char['char_name'].'</a>';
			echo '</td></tr>';
			$row++;
		} else if(strtotime($event_fight['starting']) < time()) {
			//has it started?
			echo '<tr><td width="45%" height="15" valign="middle" bgcolor="'.getColor($row).'" align="right">';
			echo $host_char['char_name'].'</td>';
			echo '<td width="5%" valign="middle" bgcolor="'.getColor($row).'" align="center">vs</td>';
			echo '<td width="45%" valign="middle" bgcolor="'.getColor($row).'" align="left">';
			echo $guest_char['char_name'].'</td></tr>';
		}
	}

	if(strtotime($challenge['enddate']) < time() ) {
		//sieger rausfinden
		if($clans[0]['points'] > $clans[1]['points']) {
			$winner = $clans[0]['name'];
		} else {
			$winner = $clans[1]['name'];
		}

		echo '<tr><td height="15" bgcolor="'.getColor($row++).'" colspan="3"> </td></tr>';
		echo '<tr><td width="45%" height="15" valign="middle" align="right" bgcolor="'.getColor($row).'">Gewinner: </td>';
		echo '<td width="5%" valign="middle" align="left" bgcolor="'.getColor($row).'"></td>';
		echo '<td width="45%" valign="middle" align="left" bgcolor="'.getColor($row++).'">'.$winner.'</td></tr>';
		echo '<tr><td width="45%" height="15" valign="middle" align="right" bgcolor="'.getColor($row).'">Clan-EXP:</td>';
		echo '<td width="5%" valign="middle" align="left" bgcolor="'.getColor($row).'"></td>';
		echo '<td width="45%" valign="middle" align="left" bgcolor="'.getColor($row++).'"> '.$clans[$i]['xp'].'</td></tr>';
		echo '<tr><td width="45%" height="15" valign="middle" align="right" bgcolor="'.getColor($row).'">ELO-Punkte: </td>';
		echo '<td width="5%" valign="middle" align="left" bgcolor="'.getColor($row).'"> </td>';
		echo '<td width="45%" valign="middle" align="left" bgcolor="'.getColor($row).'"> '.$clans[$i]['elo'].'</td></tr>';
		echo '</table>';
	}
}

function displayOverview($challenge_id, $user) {
	$user_clan = getClan($user['clan']);
	$challenge = getChallenge($challenge_id);
	$clan_ids = getParticipatingClanIDs($challenge_id);
	$clans = getChallengeClans($challenge_id);

	if(!in_array($user['clan'] , $clan_ids)) {
		displayErrorMessage(NULL, 'Wollte da jemand schummeln? ;)', displayHistoryBackLink());
		return;
	}
	if($challenge['calculated'] == FALSE) {
		displayErrorMessage(NULL, 'Der Clankampf ist noch nicht soweit!!', displayHistoryBackLink());
		return;
	}

	$event_id = $challenge['event_id'];
	$event = getEvent($event_id);
	if($event['event_type'] != EVENT_CLAN_FIGHT) {
		displayErrorMessage(NULL, 'Das Event is garkein Clan-Kampf!!', displayHistoryBackLink());
		return;
	}

	switch ($challenge['anzahl_chars']) {
		case 3:
			//not implemented yet!!
			break;
		case 4:
			//not implemented yet!!
			break;
		case 5:
			displayHeader(2, 3, $challenge, $clans);
			break;
	}
	displayBody($challenge, $clans, $user);
}

switch ($action) {
	case 'showsomething':

		break;
	default:
		displayOverview($challenge_id, $user_ida);
		break;
}
?>
</body>
</html>
