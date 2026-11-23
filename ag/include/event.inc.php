<?php
/*
 *
 * @copyright (c) 2011 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH.'/include/user.inc.php');
include_once (ROOT_PATH . '/include/defines.inc.php');
include_once (ROOT_PATH . '/include/items.inc.php');

defineIfNotDefined('EVENT_TEST', 0);
defineIfNotDefined('EVENT_SCHATZ_SUCHE', 1);
defineIfNotDefined('EVENT_TURNIER', 2);
defineIfNotDefined('EVENT_KAMPF', 3);
defineIfNotDefined('EVENT_LIGA', 4);
defineIfNotDefined('EVENT_WANTED', 5);
defineIfNotDefined('EVENT_CLAN_FIGHT', 6);

defineIfNotDefined('KEY_LOGE', 'LOGE');
defineIfNotDefined('KEY_SITZ', 'SITZ');
defineIfNotDefined('KEY_STEH', 'STEH');
defineIfNotDefined('KEY_EXP_CHAR1', 'EXP_CHAR1');
defineIfNotDefined('KEY_EXP_CHAR2', 'EXP_CHAR2');
defineIfNotDefined('KEY_GELD_CHAR1', 'GELD_CHAR1');
defineIfNotDefined('KEY_GELD_CHAR2', 'GELD_CHAR2');
defineIfNotDefined('KEY_AXP_CHAR1', 'AXP_CHAR1');
defineIfNotDefined('KEY_AXP_CHAR2', 'AXP_CHAR2');
defineIfNotDefined('KEY_ITM_CHAR1', 'ITM_CHAR1');


/**
 * Creates an event for a given type and returns the id of this event!
 * @param string $type
 * @return int the id of the event
 */
function createEvent($type) {
	while(!$finished) {
		$sql = 'SELECT IFNULL(max(event_id),0) + 1 FROM events';
		$row = mysqli_fetch_row(db_query($sql));
		db_query('INSERT INTO events(event_id, event_type) values('.$row[0].', \''.$type.'\')');
		$finished = db_affected_rows() > 0;
		$id = $row[0];
	}

	return $id;
}
/**
 * Adds a participant to a given event
 * @param int event_id
 * @param array $char
 * @return int the id of the participant (usually the charid), NULL on failure!
 */
function addParticipant($event_id, array $char) {
	// Okay now add the char ;)
	$user = getUser($char['besitzer']);
	$hp = explode(',', $char['hp']);
	$mp = explode(',', $char['mp']);

	$sql = 'INSERT INTO event_chars(event_id, event_char_id, char_id, char_name, char_bild, user_id, user_name, hp, mp, strength, speed, defense, luck, stamina, block_begin, block_end, abgeholt)';
	$sql .= ' value('. $event_id . ', '.$char['id'] .', '.$char['id'] .', \''.$char['name'].'\', \''.$char['bild'].'\', '.$char['besitzer'].', \''.$user['nickname'].'\', '.$hp[1].', '.$mp[1].', '.$char['starke'].', '.$char['speed'].', '.$char['verteidigung'].', '.$char['glueck'].', '.$char['ausdauer'].', now(), TIMESTAMPADD(Minute, 1, now()), FALSE)';

//	echo $sql . '<br>';

	db_query($sql);
	if(db_affected_rows() == 0)
		return NULL;
	return $char['id'];
}


function calculateEventFight($event_id, array $char1, array $char2, $attack_set, $startTimestamp, $endTimestamp, $expFactor, $arenaFactor, array $metaData = array()) {
	$char1['attacken'] = implode(',', getAttackSet($char1['id'], $attack_set));
	$char2['attacken'] = implode(',', getAttackSet($char2['id'], $attack_set));
	$combinedArray = calculateFight($char1, $char2);

	$exp = calculateExperience($char1, $char2, $combinedArray['winner'], $expFactor);

	$combinedArray['data'][KEY_EXP_CHAR1] = $exp[$char1['id']];
	$combinedArray['data'][KEY_EXP_CHAR2] = $exp[$char2['id']];

	$arena = calculateArenaData(null, $char1, $char2, $arenaFactor);

	$combinedArray['data'][KEY_LOGE] = $arena['loge'];
	$combinedArray['data'][KEY_SITZ] = $arena['sitz'];
	$combinedArray['data'][KEY_STEH] = $arena['steh'];

	if($combinedArray['winner'] == $char1['id']) {
		$combinedArray['data'][KEY_GELD_CHAR1] = $arena['geld'] * 0.7;
		$combinedArray['data'][KEY_GELD_CHAR2] = $arena['geld'] * 0.3;
	} else {
		$combinedArray['data'][KEY_GELD_CHAR2] = $arena['geld'] * 0.7;
		$combinedArray['data'][KEY_GELD_CHAR1] = $arena['geld'] * 0.3;
	}

	foreach ($metaData as $key => $value) {
		$combinedArray['data'][$key] = $value;
	}

	// first persist the fight and then check who was the winner and adjust the char array for him ;)
	persistFight($event_id, $combinedArray, $startTimestamp, $endTimestamp);

	// now get the winners array
	$rounds = $combinedArray['rounds'];
	$lastRound = $rounds[count($rounds) - 1];

	$newChar = null;
	if($lastRound['char1_array']['id'] == $combinedArray['winner']){
		$newChar = $lastRound['char1_array'];
	} else {
		$newChar = $lastRound['char2_array'];
	}
	$newChar['mp'] = $newChar['mp']. ','.$newChar['mp_max'];
	$newChar['hp'] = $newChar['hp']. ','.$newChar['hp_max'];
	return $newChar;
}

/**
 * Persists a fight of one event.
 * @param int $event_id
 * @param array combined array that is calculated in the kampf_wrapper
 * @param int $startTimestamp unix timestamp for the start of the battle
 * @param int $endTimestamp unix timestamp for the end of the battle
 * @return int the id of the fight
 */
function persistFight($event_id, array $combinedArray, $startTimestamp, $endTimestamp) {
	// persist a fight of two chars (calculated by the wrapper) ^^"
	//echo 'The fight was about ' . count($combinedArray) . ' rounds <br>';

	$roundsArray = $combinedArray['rounds'];
	$winnerId = $combinedArray['winner'];
	$hostId = $combinedArray['host'];
	$data = $combinedArray['data'];


	// first get a fight id ;).
	$finished = FALSE;
	while(!$finished) {
		$sql = 'SELECT IFNULL(max(event_fight_id),0) + 1 FROM event_fights WHERE event_id = ' .$event_id;
		$row = mysqli_fetch_row(db_query($sql));
		$sql = 'INSERT INTO event_fights(event_id, event_fight_id, host, winner, `starting`, visible) values('.$event_id.', \''.$row[0].'\', '. $hostId . ', ' . $winnerId .', \''.date("Y-m-d H:i:s",$startTimestamp).'\', \''.date("Y-m-d H:i:s",$endTimestamp).'\')';
		if(db_query($sql) === FALSE) {
			echo $sql . '<br>';
			break;
		}
		$finished = db_affected_rows() > 0;
		$event_fight_id = $row[0];
	}

	// now we have a $event_fight_id ;) lets insert the other stuff ;)

	for($round = 0; $round < count($roundsArray) ; $round++) {
		// persist this round ^^"

		// first persist char1
		$chara_1 = $roundsArray[$round]['char1_array'];

		$visible_ts_value = $startTimestamp + (($endTimestamp - $startTimestamp) / 10) * $round;
		$visible_ts = date("Y-m-d H:i:s", $visible_ts_value);

		$sql = 'INSERT INTO event_fight_rounds(event_id, event_fight_id, round, event_char_id, hp, mp, strength, speed, defense, luck, stamina, attack, damage, visible)';
		$sql .= ' values('.$event_id.','.$event_fight_id.','.$round.',' .$chara_1['id'].',\''. $chara_1['hp'].'\', \''. $chara_1['mp'].'\', '. $chara_1['starke'].', '. $chara_1['speed'].', '. $chara_1['verteidigung'].', '. $chara_1['glueck'].', '. $chara_1['ausdauer'].', '. ($roundsArray[$round]['atk_char1']===NULL?'NULL': '\''.$roundsArray[$round]['atk_char1'].'\'' ).', '. ($roundsArray[$round]['dmg_char1']===NULL?'NULL': '\''.$roundsArray[$round]['dmg_char1'].'\'' ).', \''.$visible_ts.'\')';


		$res = db_query($sql);
		if(!$res)
			echo $sql .'<br>';
		// now persist char2
		$chara_2 = $roundsArray[$round]['char2_array'];

		$sql = 'INSERT INTO event_fight_rounds(event_id, event_fight_id, round, event_char_id, hp, mp, strength, speed, defense, luck, stamina, attack, damage, visible)';
		$sql .= ' values('.$event_id.','.$event_fight_id.','.$round.',' .$chara_2['id'].',\''. $chara_2['hp'].'\', \''. $chara_2['mp'].'\', '. $chara_2['starke'].', '. $chara_2['speed'].', '. $chara_2['verteidigung'].', '. $chara_2['glueck'].', '. $chara_2['ausdauer'].', '. ($roundsArray[$round]['atk_char2']===NULL?'NULL': '\''.$roundsArray[$round]['atk_char2'].'\'' ).', '. ($roundsArray[$round]['dmg_char2']===NULL?'NULL': '\''.$roundsArray[$round]['dmg_char2'].'\'' ).', \''.$visible_ts.'\')';

		$res = db_query($sql);
		if(!$res)
			echo $sql .'<br>';
		}

	// Okay now do we have any additional data left?
	foreach ($data as $key => $value) {
		$sql = 'INSERT INTO event_fight_metadata(event_id, event_fight_id, `key`, `value`) values('.$event_id.','.$event_fight_id.',\''.$key.'\',\''.$value.'\')';
//		echo $sql . ' <br>';
		db_query($sql);
	}
}

function getEventIdsToFetch($char_id, $type = NULL) {
	$sql_appendix = ($type === NULL) ? '' : ' inner join events e on ec.event_id = e.event_id AND event_type = '.$type;
	
	$sql = 'SELECT ec.event_id FROM event_chars ec '.$sql_appendix.' WHERE block_begin <= now() AND (block_end < now() AND abgeholt = FALSE) AND char_id = ' .$char_id . ' ORDER BY ec.event_id ASC';
	$qry = db_query($sql);
	$result = array();
	
	while ($row = mysqli_fetch_row($qry)) {
		$result[] = $row[0];
	}
	return $result;
}

function getEventStatus($charid) {
	$event_ids = getEventIdsToFetch($charid);
	if (count($event_ids) > 0) {
		$event = getEvent($event_ids[0]);
		switch ($event['event_type']) {
			case EVENT_TEST:
				return 'Test';
			case EVENT_SCHATZ_SUCHE:
				return 'Schatz Suche';
			case EVENT_TURNIER:
				return 'Turnier';
			case EVENT_KAMPF:
				return 'Kampf';
			case EVENT_LIGA:
				return 'Liga';
			case EVENT_WANTED:
				return 'Wanted';
			case EVENT_CLAN_FIGHT:
				return 'Clanfight';
		}
	}
	return NULL;
}

function string2EventType($text) {
	switch($text) {
		case 'Schatz Suche':
			return EVENT_SCHATZ_SUCHE;
		case 'Turnier':
			return EVENT_TURNIER;
		case 'Kampf':
			return EVENT_KAMPF;
		case 'Liga':
			return EVENT_LIGA;
		case 'Wanted':
			return EVENT_WANTED;
		case 'Clanfight':
			return EVENT_CLAN_FIGHT;
		default:
			return NULL;
	}
}

function getOngoingEventCount($charid, $type = NULL) {
	$event_ids = getEventIdsToFetch($charid, $type);
	return count($event_ids);
}

function isEventToFetchAwaiting($charid) {
	return getOngoingEventCount($charid) > 0;
}

function getEventStatusBlocked($charid) {
	$event_ids = getEventIdsToFetch($charid);
	if (count($event_ids) > 0) {
		$sql = 'SELECT Timestampdiff(Second,now(),MAX(block_end)) FROM event_chars WHERE event_id = ' . $event_ids[0];
		$row = mysqli_fetch_row(db_query($sql));
		return $row[0];
	}
	return 0;
}

function abholenChar(array $user, $event_id, $char_id) {
	// okay, first checking the basic things
	$char = getChar($char_id);
	if($char['besitzer'] != $user['id']) {
		return 'Du kannst den Char nicht abholen weil er dir nicht gehört!!';
	}

	$ressource = 'User:'.$user['id'];
	if(!semaphoreUP($ressource)){ // TRUE, diese Anfrage darf bearbeitet werden
		return 'Verarbeitung gerade in Gange, bitte warten...';
	}

	$event_char = getEventChar($event_id, $char_id);
	if($event_char === NULL) {
		semaphoreDown($ressource); // free the previously reserved semaphore!
		return 'Der Char hat an diesem Event garnicht teil genommen!';
	}

	if($event_char['abgeholt'] == TRUE) {
		semaphoreDown($ressource); // free the previously reserved semaphore!
		return 'Der Char wurde schon abgeholt!';
	}

	// now get the ids of fights that the char has at the event!
	$fight_ids = getEventFightIdsByChar($event_id, $char_id);
	if(count($fight_ids) <= 0) {
		semaphoreDown($ressource); // free the previously reserved semaphore!
		return 'Der Char hat in diesem Event keinen Fight bestritten!';
	}

	
	$char_meta = getEventCharMetaData($event_id, $event_char['event_char_id']);
	
	$char_exp = 0;
	$char_aexp = 0;
	$char_geld = 0;
	
	if (isset($char_meta[KEY_EXP_CHAR1])) {
		$char_exp += $char_meta[KEY_EXP_CHAR1];
	}

	if (isset($char_meta[KEY_AXP_CHAR1])) {
		$char_aexp += $char_meta[KEY_AXP_CHAR1];
	}

	if (isset($char_meta[KEY_GELD_CHAR1])) {
		$char_geld += $char_meta[KEY_GELD_CHAR1];
	}
	
	if (isset($char_meta[KEY_ITM_CHAR1])) {
		$items = getItemsMap();
		$item = lookupItem($items, $char_meta[KEY_ITM_CHAR1]);
		createItemForUser($item, $event_char['user_id']);
		echo '<script language="JavaScript">alert(\''.$char['name'].' hat das Item '.$item['name'].' gewonnen!\');</script>';
	}
	
	// Okay and now calculate those fights?
	// Get the "chars" data :)
	foreach ($fight_ids as $fight_id) {
		$fight = getEventFight($event_id, $fight_id);
		$fight_meta = getEventFightMetaData($event_id, $fight_id);
		if($fight['host'] == $char_id) {
			$char_exp += $fight_meta[KEY_EXP_CHAR1];
			$char_aexp += $fight_meta[KEY_AXP_CHAR1];
			$char_geld += $fight_meta[KEY_GELD_CHAR1];
		} else {
			$char_exp += $fight_meta[KEY_EXP_CHAR2];
			$char_aexp += $fight_meta[KEY_AXP_CHAR2];
			$char_geld += $fight_meta[KEY_GELD_CHAR2];
		}
	}
	// add the experience to the char
	addExpToChar($char_id, $char_exp);
	// add the experience to the arena
	if($char_aexp > 0) {
		addExpToArena($char['besitzer'], $char_aexp);
	}
	addMoneyToUser($char['besitzer'], $char_geld);

	// Okay and now we want to learn the attacks :)
	$sql = 'SELECT attack, count(attack) as anzahl FROM event_fight_rounds e where event_id = ' . $event_id . ' AND event_char_id = ' . $char_id . ' group by attack';
	$qry = db_query($sql);
	$attacken_usage = array();

	while($row = mysqli_fetch_assoc($qry)) {
		$attacken_usage[] = array('id' => $row['attack'], 'anzahl' => $row['anzahl']);
	}

	addAttackUsageToChar($char_id, $attacken_usage);

	$sql = 'UPDATE event_chars SET abgeholt = TRUE WHERE char_id = ' . $char_id . ' AND event_id = ' . $event_id;
	$qry = db_query($sql);

	semaphoreDown($ressource); // free the previously reserved semaphore!
	return $fight_ids; // Return the fight ids that the char has participated in!
}


function getEvent($event_id) {
	$result = mysqli_fetch_assoc(db_query('SELECT * FROM events WHERE event_id = ' . $event_id));
	if($result)
		return $result;
	return NULL;
}

function getEventChar($event_id, $event_char_id) {
	$result = mysqli_fetch_assoc(db_query('SELECT * FROM event_chars WHERE event_id = ' . $event_id . ' AND event_char_id = ' . $event_char_id));
	if($result)
		return $result;
	return NULL;

}

/**
 * @return array with fight value (+ a passed column to indicate the fight is over)
 */
function getEventFight($event_id, $event_fight_id) {
	$result = mysqli_fetch_assoc(db_query('SELECT *, visible <= now() as passed FROM event_fights WHERE event_id = ' . $event_id. ' AND event_fight_id = ' .$event_fight_id ));
	if($result)
		return $result;
	return NULL;
}

function getEventFightMetaData($event_id, $event_fight_id) {
	$qry = db_query('SELECT * FROM event_fight_metadata WHERE event_id = ' . $event_id. ' AND event_fight_id = ' .$event_fight_id);
	$result = array();
	while($row = mysqli_fetch_assoc($qry)) {
		$result[$row['key']]= $row['value'];
	}
	return $result;
}

function getEventCharMetaData($event_id, $event_char_id) {
	$qry = db_query('SELECT * FROM event_char_metadata WHERE event_id = ' . $event_id. ' AND event_char_id = ' .$event_char_id);
	$result = array();
	while($row = mysqli_fetch_assoc($qry)) {
		$result[$row['key']]= $row['value'];
	}
	return $result;
}

function getEventFightIds($event_id) {
	$qry = db_query('SELECT event_fight_id FROM event_fights WHERE event_id = ' . $event_id . ' ORDER by event_fight_id ASC');
	$result = array();
	while($row = mysqli_fetch_assoc($qry)) {
		$result[]= $row['event_fight_id'];
	}
	return $result;
}

// this is more tricky XD
function getEventFightIdsByChar($event_id, $event_char_id) {
	$sql = 'SELECT event_fight_id FROM event_fight_rounds where event_id = ' . $event_id . ' AND event_char_id = ' . $event_char_id . ' AND `round` = 0 ORDER BY event_fight_id ASC';
	$qry = db_query($sql);
	while($row = mysqli_fetch_assoc($qry)) {
		$result[]= $row['event_fight_id'];
	}
	return $result;
}

function getEventFightRoundData($event_id, $event_fight_id, $round) {
	$sql = 'SELECT * FROM event_fight_rounds WHERE event_id = ' . $event_id . ' AND event_fight_id = ' . $event_fight_id . ' AND `round` = ' .$round;

	$qry = db_query($sql);
	while($row = mysqli_fetch_assoc($qry)) {
		$result[$row['event_char_id']] = $row;
	}
	return $result;
}



?>
