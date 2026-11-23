<?php

/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

/**
 * This script checks for completed tournaments to add the winner in the "hall-of-fame"
 */

include('db.php');
include('path.inc.php');
include_once (ROOT_PATH.'/include/sqlwrapper.inc.php');
include_once (ROOT_PATH.'/include/event.inc.php');
include_once (ROOT_PATH.'/include/halloffame.inc.php');
include_once (ROOT_PATH.'/include/tournament.inc.php');

// First run schedule tournaments
runScheduledTournaments();

$tournaments = array();
$tournament_event_ids = array();

$qry = db_query('SELECT * FROM tournament WHERE ausgewertet = FALSE');

while ($row = mysqli_fetch_assoc($qry)) {
	$tournament_event_ids[] = $row['event_id']; 
	$tournaments[$row['event_id']] = $row;
}

if (count($tournaments) > 0) {
	$sql = 'select event_id, MAX(visible) < now() as done, MAX(event_fight_id) as final_fight_id FROM event_fights WHERE event_id IN ('.join(',', $tournament_event_ids).') GROUP BY event_id';
	$qry = db_query($sql);

	$tournaments_done = array();

	while ($row = mysqli_fetch_assoc($qry)) {
		if ($row['done']) {
			$event_id = $row['event_id'];
			$tournament = $tournaments[$event_id];
			$tournament_type = getTournamentType($tournament['type']);
			// determine the winner of the tournament ...
			$final_fight = getEventFight($event_id, $row['final_fight_id']);
			$winner_char = getEventChar($event_id, $final_fight['winner']);
			$row = mysqli_fetch_assoc(db_query('Select max(id) as round from online'));

			insertIntoHallOfFameFull($row['round'], $tournament_type['name'], $winner_char['char_id'], $winner_char['char_name'], $winner_char['user_id'], $winner_char['user_name'], '\''.$tournament['datum'].'\'', $tournament['id']);
			$tournaments_done[] = $tournament['id'];
		}
	}

	if (count($tournaments_done) > 0) {
		db_query('UPDATE tournament SET ausgewertet = TRUE WHERE id IN ('.join(',', $tournaments_done).')');
	}
}
