<?php
/*
 *
 * @copyright (c) 2017 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 */

include_once(ROOT_PATH.'/include/sqlwrapper.inc.php');
include_once(ROOT_PATH.'/include/defines.inc.php');

define ( TOURNAMENT_GAIN_ANMELDUNG, 0 );
define ( TOURNAMENT_GAIN_PL, 1 );
define ( TOURNAMENT_GAIN_LEVEL, 2 );

defineIfNotDefined( TOURNAMENT_FIGHT_DURATION, 5);
defineIfNotDefined( ATTACK_SET_TOURNAMENT, 1);


function getTournamentTypes() {
	$qry = db_query ( 'SELECT * from tournament_types' );
	$types = array ();
	while ( $row = mysqli_fetch_assoc ( $qry ) ) {
		$types [$row ['id']] = $row;
	}
	return $types;
}

/**
 * 
 * @param int $id the id of the tournament_type
 * @return array
 */
function getTournamentType($id) {
	$qry = db_query ( 'SELECT * from tournament_types WHERE id = ' . $id );
	return mysqli_fetch_assoc ( $qry );
}

/**
 * Calling this method will run all scheduled tournaments for this hour.
 */
function runScheduledTournaments() {
	
	$hour_of_day = date('G');
	$day_of_week = date('N');
	$day_of_month = date('j');
	
	$sql = 'SELECT id FROM tournament_types WHERE (day_of_week = '.$day_of_week.' OR day_of_week is NULL) AND (day_of_month = '.$day_of_month.' or day_of_month is NULL) AND hour_of_day = '.$hour_of_day.' AND id NOT IN (SELECT type from tournament where ausgewertet = FALSE)';
	// echo $sql;
	
	$qry = db_query($sql);
	
	while( $row = mysqli_fetch_assoc($qry) ) {
		runTournament( $row['id'] );
	}
}

function getTournamentCharExclusionSQL($tournament_type, $prefix=NULL) {
	$exclusions = array();
	
	if ($prefix === NULL) {
		$prefix = '';
	} else {
		$prefix = $prefix.'.';
	}
	
	$name = $tournament_type['name'];
	
	$fusion = !$tournament_type['excl_fusions']; // 1 fuer erlaubt
	$special = !$tournament_type['excl_special_chars'];	// Spezialchars 1 für erlaubt
	
	$minlevel = $tournament_type['min_level'];	// selbsterklärend
	$maxlevel = $tournament_type['max_level'];	// selbsterklärend
	$exclude_winners = $tournament_type['excl_winners']; // gibt an ob gewinner vorheriger turniere des typs ausgeschlossen werden
	$exclude_fusi_count = $tournament_type['excl_fusion_particip_count'];
	
	if($fusion != 1){
		$exclusions[] = $prefix.'fusion = \'nein\'';
	} else if(is_numeric($exclude_fusi_count) && $exclude_fusi_count > 0) {
		$subselect = 'SELECT ec.char_id from event_chars ec inner join tournament t on ec.event_id = t.event_id WHERE t.type = '.$tournament_type['id'].' GROUP by ec.char_id HAVING count(*) >= '.$exclude_fusi_count;
		$exclusions[] = '( '.$prefix.'fusion = \'nein\' OR '.$prefix.'id NOT IN ( '.$subselect.' ) )';
	}
	
	// Es sollen keine NPC mitmachen :) und nicht die Spezial Wanted NPCs
	
	$exclude_race_ids = array();
	
	$npc_race = getRaceTypeName('NPC');
	$races = getRacesByType($npc_race); // remove all NPC
	foreach($races as $race) {
		$exclude_race_ids[] = '\''.$race['id'].'\'';
	}
	
	if($special != 1){
		$races = getSpecialRaceIds();
		$npc_race = getRaceTypeName('NPC'); // do not remove npc (there is a separate setting for this)
		
		foreach($races as $race) {
			if($race['type'] != $npc_race['id']) {
				$exclude_race_ids[] = '\''.$race['id'].'\'';
			}
		}
	}
	
	if(count($exclude_race_ids)) {
		$exclusions[] = $prefix.'rasse NOT IN('.implode(',',$exclude_race_ids).') ';
	}
	
	if(is_numeric($minlevel)){
		$exclusions[] = $prefix.'level >= '.$minlevel;
	}
	
	if(is_numeric($maxlevel)){
		$exclusions[] = $prefix.'level <= '.$maxlevel;
	}
	
	if ($exclude_winners == 1) {
		$exclusions[] = $prefix.'id NOT IN( SELECT charid FROM highscore WHERE art = "'.$name.'" AND charid IS NOT NULL) ';
	}

	if ( count($exclusions ) > 0 ) {
		return ' '.join(' AND ', $exclusions).' ';
	}
	return '1';
	
}

function retrieveParticipants($tournament_type) {
	$filter_sql = getTournamentCharExclusionSQL($tournament_type, 'c');

	$name = $tournament_type['name'];
	
	$anzahl = $tournament_type['competitors']; // Anzahl (benötigt)
	$gain = $tournament_type['gain'];			// ('Anmeldung', 'PL', 'Level')
	$itemless = $_GET['without_equip']; // gibt an ob das turnier die items ingorieren soll (standard sind items aktiv)
	
	$sql = 'SELECT count(*) as anzahl from chars c where '.$filter_sql;
	
	// echo $sql.'<br>';
	$qry = db_query($sql);
	$row = mysqli_fetch_assoc($qry);
	
	if($row['anzahl'] < $anzahl){
		echo $name.' konnte nicht mit '.$anzahl.' Chars gestartet werden, da es nur '.$row['anzahl'].' von '.$anzahl.' nötigen Chars gibt!<br>';
		$anzahl = pow(2, floor(log($row['anzahl'], 2)));
		echo $name.' wird nun mit '.$anzahl.' Chars gestartet!<br>';
	}
	
	// Erstma nur die id auslesen (danach wird eh nochmal gemischt ;))
	$cid = array();
	if($gain == TOURNAMENT_GAIN_ANMELDUNG){
		$sql = 'SELECT c.id FROM tournament_registration tr inner join chars as c ON tr.charakter = c.id WHERE tr.type='.$tournament_type['id'].' AND '.$filter_sql;
		// echo '<p>'.$sql.'</p>';
		$qry = db_query($sql);
		while (($row = mysqli_fetch_assoc($qry)) && count($cid) < $anzahl){
			$cid[] = $row['id'];
		}
		$sql = 'DELETE FROM tournament_registration WHERE type = '.$tournament_type['id'];
		//	echo $sql .'<br>';
		db_query($sql); // Lösche die Anmeldungsliste
	
		if ( count($cid) == 0 ) {
			$sql = 'SELECT id FROM chars c where '.$filter_sql.' ORDER BY RAND()';
		} else {
			$sql = 'SELECT id FROM chars c where '.$filter_sql.' AND id NOT IN ('.join(',', $cid).') ORDER BY RAND()';
		}	
		// In case the tournament is not full
	} else if($gain == TOURNAMENT_GAIN_PL){
		// Die Buffs werden nicht zur PL gezählt ;)
		$sql = 'SELECT c.id FROM chars c where '.$filter_sql.' ORDER BY starke+verteidigung+speed+ausdauer+glueck desc';
	} else if($gain == TOURNAMENT_GAIN_LEVEL){
		$sql = 'SELECT c.id FROM chars c where '.$filter_sql.' ORDER BY level desc';
	} else{
		echo 'Parameter gain war weder Anmeldung, PL noch Level<br>';
		return array();
	}

	$qry = db_query($sql);
	
	while( ($row = mysqli_fetch_assoc($qry)) && count($cid) < $anzahl) {
		$cid[] = $row['id'];
	}
	
	// echo $sql.'<br>';
	$char_array = array();
	foreach ($cid as $char_id) {
		// echo $row['id'].'<br>';
		$char_data = NULL;
		if ($itemless == 1) {
			$char_data = getChar($char_id); // ohne Equip für Turniere!!
		} else {
			$char_data = getCharWithBuffs($char_id); // Equip für Turniere!!
		}
		$hp = explode(',', $char_data['hp']);
		$hp_max = $hp[1];
		$mp = explode(',', $char_data['mp']);
		$mp_max = $mp[1];
		$char_data['hp'] = join(',', array($hp_max, $hp_max));
		$char_data['mp'] = join(',', array($mp_max, $mp_max));
		$char_array[] = $char_data;
	}
	
	return $char_array;
}

function canEditTournament($tournament_type) {
	$sql = 'SELECT count(id) FROM tournament WHERE ausgewertet = FALSE AND type = '.$tournament_type['id'];
	$count = mysqli_fetch_row(db_query($sql))[0];
	return $count == 0;
}

function canStartTournament($tournament_type) {
	return canEditTournament($tournament_type);
}

/**
 * Calling this method will run the given tournament
 * @param mixed $tournament_type integer => id, string => name, array fetched from getTournamentType(s)
 */
function runTournament($tournament_type) {
	if (is_numeric($tournament_type)) {
		$tournament_type = getTournamentType($tournament_type);
	} else if (is_string($tournament_type)) {
		$tournament_type= mysqli_fetch_assoc( db_query( 'SELECT * from tournament_types WHERE name = \'' . $tournament_type.'\'' ));
	}
	
	if (!canStartTournament($tournament_type)) {
		echo 'Unable to start Tournament as tournament of the same type is already running!';
		return;
	}
	
	$participants = retrieveParticipants($tournament_type);
	if ($participants !== NULL) {
		$tournament = createTournament($tournament_type);
		calculateTournament($tournament, $participants);
	}
}

function createTournament($tournament_type) {
	$event_id= createEvent(EVENT_TURNIER);
	db_query('INSERT INTO tournament(type, event_id) values('.$tournament_type['id'].','.$event_id.')');
	return mysqli_fetch_assoc(db_query( 'SELECT * FROM tournament WHERE event_id = ' . $event_id));
}

function calculateTournament($tournament, $participants) {
	shuffle($participants);
	$tournament_type = getTournamentType($tournament['type']);
	
	$event_id = $tournament['event_id'];
	
	foreach ($participants as $participant) {
		addParticipant($event_id, $participant);
	}
	
	// Hier Gruppenphase berechnen und die Chars für die Endrunde in $chars speichern
	$start_time = time();
	$counter = 1;
	$round = 1;
	$duration = TOURNAMENT_FIGHT_DURATION * 60;
	
	$char_array = $participants;
	
	// Beginn der Endrunde
	while(count($char_array) > 1){ // Solange bis nur ein Char übrig bleibt
		$n_chars = array();
		for($i=0;$i<count($char_array);$i+=2){
			$fight_start_time = $start_time + ($counter-1) * $duration;
			$fight_end_time = $fight_start_time + $duration;
			$winner = calculateEventFight($event_id, $char_array[$i], $char_array[$i+1], ATTACK_SET_TOURNAMENT, $fight_start_time, $fight_end_time, 1, 0, array('round' => $round));
			if ($winner['id'] == $char_array[$i]['id']) {
				$n_chars[] = $char_array[$i];
			} else {
				$n_chars[] = $char_array[$i+1];
			}
			//		echo 'Berechne fight zwischen '.$char_array[$i].'und '.$char_array[$i+1].'('.$i.'/'.count($char_array).')<br>';
			$counter++;
		}
		//	echo 'nxt round<br>';
		$round++;
		if($tournament_type['randomize'] == 1) {
			shuffle($n_chars);
		}
		$char_array = $n_chars;
	}
	
	$end_time = $start_time + $counter * $duration;
	$end_time_str = date("Y-m-d H:i:s",$end_time);
	db_query('UPDATE event_chars SET block_begin = \''.$end_time_str.'\', block_end = \''.$end_time_str.'\' WHERE event_id = '.$event_id);
	
	// Seems that we have a winner :D
	$fruit_item_str = getFruitItem($tournament_type['fruit_type'], $tournament_type['fruit_chance']);

	if ($fruit_item_str !== NULL) {
		// add item in event_char_metadata
		db_query('INSERT INTO event_char_metadata(event_id, event_char_id, `key`, `value`) values('.$event_id.','.$char_array[0]['id'].',\''.KEY_ITM_CHAR1.'\',\''.$fruit_item_str.'\')');
	}
	
}

function getFruitItem( $fruit_type, $drop_chance ) {
        $item_hash = NULL;
        if ( $drop_chance == 0 ) {
                return NULL;
        } else if($drop_chance >= 100 || $drop_chance >= mt_random_wrapper(1, 100)) {
                if ($fruit_type == 'natur') {
                        $sql = 'SELECT id FROM wochen_markt WHERE art=\''.$fruit_type.'\' order by rand() LIMIT 1';
                } else {
                        $sql = 'SELECT id FROM wochen_markt WHERE kategorie=\''.$fruit_type.'\' order by rand() LIMIT 1';
                }

                $qry = db_query($sql);
                $id = mysqli_fetch_row($qry)[0];
         
                $item_hash = item2Value(array('id' => $id, 'table_name' => 'wochen_markt'));
        }
        return $item_hash;
}


