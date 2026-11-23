<?php
/*
 *
 * @copyright (c) 2011 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once (ROOT_PATH . '/include/defines.inc.php');
include_once (ROOT_PATH . '/include/clan.inc.php');
include_once (ROOT_PATH . '/include/messagefunctions.inc.php');
include_once (ROOT_PATH . '/include/attackenset.inc.php');
include_once (ROOT_PATH . '/include/arena.inc.php');
include_once (ROOT_PATH . '/include/kampf_wrapper.inc.php');
include_once (ROOT_PATH . '/include/semaphore.inc.php');
include_once (ROOT_PATH . '/include/event.inc.php');


defineIfNotDefined('MIN_MEMBERS', 5);
defineIfNotDefined('MAX_OPEN_REQUESTS', 3);
defineIfNotDefined('MAX_LEADER_ASSIGNED_CHARS', 1);
defineIfNotDefined('CLANFIGHTS_MAX_FIGHTS_PER_24H', 6);
defineIfNotDefined('CLANFIGHTS_RECHALLENGE_DELAY', 23); // in hours
defineIfNotDefined('DAVY_BACK_FIGHT', 1);
defineIfNotDefined('SURVIVAL', 2);
defineIfNotDefined('DURATION_DAVY_BACK_FIGHT', 15);
defineIfNotDefined('DURATION_SURVIVAL_FIGHT', 15);
defineIfNotDefined('MAX_COUNT_PARALLEL_CFS', 1);


defineIfNotDefined('ATTACK_SET_DAVY_BACK_FIGHT', 1);
defineIfNotDefined('ATTACK_SET_SURVIVAL', 1);

defineIfNotDefined('CLAN_EXP_FAKTOR_G', 1);


$GLOBALS['clan_challenge_buffered_instances'] = array ();


// Methods required within the request/accept phase

/**
 *
 * Challenge an other clan (Davy Back Fight)
 * @param array $user the user array
 * @param int $clanid the id of the clan to challenge
 * @param int $validTime the amount of hours that the challenge is valid
 */
function challengeClan(array $user, $clanid, $anzahl = MIN_MEMBERS, $type = DAVY_BACK_FIGHT, $validTime = 24) {
	$clan = getClan($user['clan']);
	$oclan = getClan($clanid);

	$ressource_clan1 = 'Clan:' . $user['clan'];
	$ressource_clan2 = 'Clan:' . $clanid;

	if(!$clan || !$oclan) {
		return 'Entweder bist du nicht in einem Clan oder der andere Clan existiert nicht mehr!!';
	}

	if(!isAuthorizedClanfightCoordinator($user['clan'], $user['id'])){
		return 'Nur der Leader, Co-Leader oder freigeschaltete Mitglieder können andere Clans herausfordern!!';
	}

	if($clan['id'] == $oclan['id']) {
		return 'Du kannst deinen eigenen Clan nicht herausfordern!!';
	}

	if(isClanLocked($user['clan']) || isClanLocked($clanid)) {
		// okay we cannot accept the challenge so make it inactive!
		return 'Einer der Clans erfüllt die Anforderungen an Clanfights nichtmehr (wahrscheinlich zu wenig Members)!';
	}

	if(!semaphoreUP($ressource_clan1)) {
		return 'Fehler beim Verarbeiten der Anfrage für  \'' . $clan['clanname'] . '\' versuche es bitte nochmal! ';
	}

	if(!semaphoreUP($ressource_clan2)) {
		semaphoreDown($ressource_clan1); // free the previously reserved semaphore!
		return 'Fehler beim Verarbeiten der Anfrage für  \'' . $oclan['clanname'] . '\' versuche es bitte nochmal! ';
	}

	// okay we have both semaphores :)

	$myOpenChallengeIDs = getOpenChallengeIDs($user['clan']);

	$clan_challenges_my_clan = getOpenChallengeRequestCount($user['clan']) + count($myOpenChallengeIDs);
	$clan_challenges_oth_clan = getOpenChallengeRequestCount($clanid) + count(getOpenChallengeIDs($clanid));

	if($clan_challenges_my_clan >= MAX_OPEN_REQUESTS) {
		semaphoreDown($ressource_clan1); // free the previously reserved semaphore!
		semaphoreDown($ressource_clan2); // free the previously reserved semaphore!
		return 'Dein Clan hat schon die maximale Anzahl von Herausforderungen offen!';
	}

	if($clan_challenges_oth_clan >= MAX_OPEN_REQUESTS) {
		semaphoreDown($ressource_clan1); // free the previously reserved semaphore!
		semaphoreDown($ressource_clan2); // free the previously reserved semaphore!
		return 'Der Clan "'.$oclan['clanname'].'" hat schon die maximale Anzahl von Herausforderungen offen!';
	}

	// check if we have a open challenge with this clan ...
	foreach ($myOpenChallengeIDs as $clan_challenge_id) {
		$challenge_clans = getParticipatingClanIDs($clan_challenge_id);
		if(in_array($user['clan'],$challenge_clans) && in_array($clanid,$challenge_clans)) {
			semaphoreDown($ressource_clan1); // free the previously reserved semaphore!
			semaphoreDown($ressource_clan2); // free the previously reserved semaphore!
			return 'Der Clan kann derzeit nicht herausgefordert werden, da noch ein offener Clan-Fight mit diesem Clan besteht!';
		}
	}


	// check if both clans have already been challengeing each other since 24h?
	$sql = 'SELECT count(*) as anzahl FROM clan_challenge_requests WHERE clan_requester IN ('.$user['clan'].', '.$clanid.') AND clan_challenged IN ('.$user['clan'].', '.$clanid.') AND  challenged_time > TIMESTAMPADD(hour, -'.CLANFIGHTS_RECHALLENGE_DELAY.', now())';
	$qry = db_query($sql);

	$row = mysqli_fetch_assoc($qry);
	if($row['anzahl'] > 0) {
		semaphoreDown($ressource_clan1); // free the previously reserved semaphore!
		semaphoreDown($ressource_clan2); // free the previously reserved semaphore!
		return 'Konnte den anderen Clan nicht herausfordern, da dieser Clan schoneinmal in den letzten '.CLANFIGHTS_RECHALLENGE_DELAY.' Stunden herausgefordert wurde!';
	}

	// it should not be possible to challenge a clan that already has an open match that is not yet calculated! ( not completely possible in sql)
	$sql = 'select count(k.anzahl) as anzahl from (SELECT count(*) as anzahl FROM clan_challenges c inner join clan_challenge_clans ccc on c.clan_challenge_id = ccc.clan_challenge_id where (calculated = 1 or enddate > now()) and clan_id IN ('.$user['clan'].', '.$clanid.') group by c.clan_challenge_id) k where k.anzahl = 0';
	//echo $sql .'<br>';
	$row = mysqli_fetch_assoc(db_query($sql));
	if($row['anzahl'] > 0) {
		semaphoreDown($ressource_clan1); // free the previously reserved semaphore!
		semaphoreDown($ressource_clan2); // free the previously reserved semaphore!
		return 'Du kannst einen Clan nicht herausfordern, wenn derzeit gerade ein Clanfight zwischen euch läuft oder offen ist!';
	}

	// now calculate how many open fights we currently had or have!
	$sql = 'select count(*) as anzahl from (SELECT count(*) as anz FROM clan_challenges c inner join clan_challenge_clans ccc on c.clan_challenge_id = ccc.clan_challenge_id where calculated = 0 or TIMESTAMPADD(day, -1, now()) < enddate and clan_id IN ('.$user['clan'].', '.$clanid.') group by clan_id) k where k.anz >= ' . CLANFIGHTS_MAX_FIGHTS_PER_24H;
	//echo $sql .'<br>';
	$row = mysqli_fetch_assoc(db_query($sql));

	if($row['anzahl'] > 0) {
		semaphoreDown($ressource_clan1); // free the previously reserved semaphore!
		semaphoreDown($ressource_clan2); // free the previously reserved semaphore!
		return 'Einer der beiden Clans hat das Limit von ' . CLANFIGHTS_MAX_FIGHTS_PER_24H . ' offenen und abgeschlossenen Clanfights innerhalb von 24 erreicht. Daher kann die Herausforderung nicht versandt werden!';
	}


	// Fordere heraus!
	db_query('INSERT INTO clan_challenge_requests(clan_requester, clan_challenged, type, challenged_time, clan_challenge_creator, anzahl_chars, active) VALUES('.$user['clan'].', '.$clanid.','.$type.' , TIMESTAMPADD(HOUR, CURRENT_TIMESTAMP, 4), '.$user['id'].', '.$anzahl.', true)');

	if(db_affected_rows() > 0) {
		if(is_numeric($oclan['leader'])) {
			sendMessage($user['nickname'], $oclan['leader'], 'Clan-Fight Herausforderung', 'Der Clan  '.$clan['clanname'].' hat euch zu einem Clan-Fight herausgefordert! Über "Mein Clan" könnt ihr die Herausforderung annehmen!');
		}
		if(is_numeric($oclan['co_leader']) && $oclan['leader'] != $oclan['co_leader']) {
			sendMessage($user['nickname'], $oclan['co_leader'], 'Clan-Fight Herausforderung', 'Der Clan  '.$clan['clanname'].' hat euch zu einem Clan-Fight herausgefordert! Über "Mein Clan" könnt ihr die Herausforderung annehmen!');
		}
	} else {
		semaphoreDown($ressource_clan1); // free the previously reserved semaphore!
		semaphoreDown($ressource_clan2); // free the previously reserved semaphore!
		return 'Konnte den anderen Clan nicht herausfordern!!';
	}

	semaphoreDown($ressource_clan1); // free the previously reserved semaphore!
	semaphoreDown($ressource_clan2); // free the previously reserved semaphore!
	return NULL;
}

/**
 * With this function the actual user accepts the challenge request from another clan!
 * @param int $challenged_clan the clan that has challenged us!
 * @param array $user
 */
function acceptChallengeRequest($challenged_clan, array $user) {
	$clan = getClan($user['clan']);

	if(!$clan) {
		return 'Du bist in keinem Clan!!';
	}

	if(!isAuthorizedClanfightCoordinator($user['clan'], $user['id'])){
		return 'Nur der Leader, Co-Leader oder freigeschaltete Mitglieder können Herausforderungen anderer Clans annehmen!!';
	}

	$sql = 'SELECT * FROM clan_challenge_requests WHERE clan_challenged = ' .$user['clan'] . ' AND clan_requester = ' .$challenged_clan . ' AND active = TRUE AND challenged_time > TIMESTAMPADD(day, -1, now())';
	$clan_challenge = mysqli_fetch_assoc(db_query($sql));

	if(!$clan_challenge) {
		return 'Konnte die Herausforderung nicht annehmen, da diese Herausforderung nicht exisitiert!';
	}

	if($user['clan'] != $clan_challenge['clan_challenged']) {
		return 'Du kannst die Herausforderung nicht annehmen, da du nicht im herausgeforderten Clan bist!';
	} else if ($clan_challenge['active'] != 1) {
		return 'Du kannst die Herausforderung nicht annehmen, da diese schon angenommen wurde!';
	}

	if(isClanLocked($clan_challenge['clan_challenged']) || isClanLocked($clan_challenge['clan_requester'])) {
		// okay we cannot accept the challenge so make it inactive!
		db_query('UPDATE clan_challenge_requests SET active = FALSE WHERE clan_challenged = ' .$clan_challenge['clan_challenged']. ' AND clan_requester = ' .$clan_challenge['clan_requester']. ' AND challenged_time = "' .$clan_challenge['challenged_time']. '"');
		return 'Einer der Clans erfüllt die Anforderungen an Clanfights nichtmehr (wahrscheinlich zu wenig Members)!';
	}

	// now calculate how many open fights we currently had or have! // FIXME: Good idea to pach this in a semaphore?
	$sql = 'select count(*) as anzahl from (SELECT count(*) as anz FROM clan_challenges c inner join clan_challenge_clans ccc on c.clan_challenge_id = ccc.clan_challenge_id where calculated = 0 or TIMESTAMPADD(day, -1, now()) < enddate and clan_id IN ('.$clan_challenge['clan_challenged'].', '.$clan_challenge['clan_requester'].') group by clan_id) k where k.anz >= ' . CLANFIGHTS_MAX_FIGHTS_PER_24H;
	//	echo $sql . '<br>';
	$row = mysqli_fetch_assoc(db_query($sql));

	if($row['anzahl'] > 0) {
		return 'Einer der beiden Clans hat das Limit von ' . CLANFIGHTS_MAX_FIGHTS_PER_24H . ' offenen und abgeschlossenen Clanfights innerhalb von 24 erreicht. Daher kann die Herausforderung nicht angenommen werden!';
	}

	$oclan = getClan($clan_challenge['clan_requester']);

	db_query('UPDATE clan_challenge_requests SET active = false WHERE clan_challenged = ' .$user['clan'] . ' AND clan_requester = ' .$challenged_clan . ' AND active = TRUE AND challenged_time > TIMESTAMPADD(day, -1, now())');

	if(db_affected_rows() > 0) {
		// okay and for failsaveness just deactivate old requests
		db_query('UPDATE clan_challenge_requests SET active = false WHERE clan_challenged = ' .$user['clan'] . ' AND clan_requester = ' .$challenged_clan. ' AND active = TRUE');

		if(is_numeric($oclan['leader'])) {
			sendMessage($user['nickname'], $oclan['leader'], 'Clan-Fight', 'Der Clan  '.$clan['clanname'].' hat eure Herausforderungen zu einem Clan-Fight angenommen! Über "Mein Clan" könnt ihr nun Chars anmelden!');
		}

		if(is_numeric($oclan['co_leader']) && $oclan['leader'] != $oclan['co_leader']) {
			sendMessage($user['nickname'], $oclan['co_leader'], 'Clan-Fight', 'Der Clan  '.$clan['clanname'].' hat eure Herausforderungen zu einem Clan-Fight angenommen! Über "Mein Clan" könnt ihr nun Chars anmelden!');
		}

		if(is_numeric($clan_challenge['clan_challenge_creator']) && $clan_challenge['clan_challenge_creator'] != $oclan['leader'] && $clan_challenge['clan_challenge_creator'] != $oclan['co_leader']) {
			sendMessage($user['nickname'], $clan_challenge['clan_challenge_creator'], 'Clan-Fight', 'Der Clan  '.$clan['clanname'].' hat eure Herausforderungen zu einem Clan-Fight angenommen! Über "Mein Clan" könnt ihr nun Chars anmelden!');
		}

		$finished = FALSE;
		$id = -1;
		// get id
		while(!$finished) {
			$row = mysqli_fetch_row(db_query('select IFNULL(max(clan_challenge_id),0) + 1 from clan_challenges'));
			db_query('INSERT INTO clan_challenges(clan_challenge_id, type, anzahl_chars) values('.$row[0].', '.$clan_challenge['type'].', '.$clan_challenge['anzahl_chars'].')');
			$finished = db_affected_rows() > 0;
			$id = $row[0];
		}

		// insert the participants :)
		$sql = 'INSERT INTO clan_challenge_clans(clan_challenge_id, id, clan_id, clan_coordinator, elo) values('.$id.', 0, '.$clan['id'].', '.$user['id'].', ' .$clan['elo'].')';
		db_query($sql);
		$sql = 'INSERT INTO clan_challenge_clans(clan_challenge_id, id, clan_id, clan_coordinator, elo) values('.$id.', 1, '.$oclan['id'].', ' .$clan_challenge['clan_challenge_creator'].', ' .$clan['elo'].')';
		db_query($sql);

		// we have everything we need for this challenge to be opened :)

	} else {
		return 'Du kannst die Herausforderungen nicht annehmen. Wurde die Herausforderung schon angenommen?';
	}

	return NULL;
}

function cancelChallengeRequest($challenged_clan, array $user) {
	$clan = getClan($user['clan']);
	$oclan = getClan($challenged_clan);

	if(!$clan) {
		return 'Du bist in keinem Clan!!';
	}

	if(!isAuthorizedClanfightCoordinator($user['clan'], $user['id'])){
		return 'Nur der Leader, Co-Leader oder freigeschaltete Mitglieder können Herausforderungen anderer Clans ablehnen!!';
	}

	$sql = 'SELECT * FROM clan_challenge_requests WHERE clan_challenged = ' .$challenged_clan . ' AND clan_requester = ' .$user['clan'] . ' AND active = TRUE AND challenged_time > TIMESTAMPADD(day, -1, now())';
	//	echo $sql .'<br>';
	$clan_challenge = mysqli_fetch_assoc(db_query($sql));

	if(!$clan_challenge) {
		return 'Konnte die Herausforderung nicht zur&uuml;ckziehen, da diese Herausforderung nicht exisitiert!';
	}

	// TODO: Check if this conditions are redundant
	if($user['clan'] != $clan_challenge['clan_requester']) {
		return 'Du kannst die Herausforderung nicht ablehnen, da du nicht im herausfordernden Clan bist!';
	} else if ($clan_challenge['active'] != 1) {
		return 'Du kannst die Herausforderung nicht ablehnen, da diese schon angenommen wurde!';
	}

	// okay seems to be okay
	db_query('UPDATE clan_challenge_requests SET active = false WHERE clan_challenged = ' .$challenged_clan . ' AND clan_requester = ' .$user['clan'] . ' AND active = TRUE');

	if(is_numeric($oclan['leader'])) {
		sendMessage($user['nickname'], $oclan['leader'], 'Clan-Fight', 'Der Clan  '.$clan['clanname'].' hat die Herausforderungen zu einem Clan-Fight zurückgezogen! Über "Mein Clan" kann eine neue Herausforderungen erstellt werden!');
	}

	if(is_numeric($oclan['co_leader']) && $oclan['leader'] != $oclan['co_leader']) {
		sendMessage($user['nickname'], $oclan['co_leader'], 'Clan-Fight', 'Der Clan  '.$clan['clanname'].' hat die Herausforderungen zu einem Clan-Fight zurückgezogen! Über "Mein Clan" kann eine neue Herausforderungen erstellt werden!');
	}

	return NULL;
}

/**
 * With this function the actual user from the challenged clan denies the challenge request!
 * @param int $challenged_clan
 * @param array $user
 */
function denyChallengeRequest($challenge_requester, array $user) {
	$clan = getClan($user['clan']);
	$oclan = getClan($challenge_requester);
	if(!$clan) {
		return 'Du bist in keinem Clan!!';
	}

	if(!isAuthorizedClanfightCoordinator($user['clan'], $user['id'])){
		return 'Nur der Leader, Co-Leader oder freigeschaltete Mitglieder können Herausforderungen anderer Clans ablehnen!!';
	}

	$sql = 'SELECT * FROM clan_challenge_requests WHERE clan_challenged = ' .$user['clan'] . ' AND clan_requester = ' .$challenge_requester . ' AND active = TRUE AND challenged_time > TIMESTAMPADD(day, -1, now())';
	//	echo $sql . '<br>';
	$clan_challenge = mysqli_fetch_assoc(db_query($sql));

	if(!$clan_challenge) {
		return 'Konnte die Herausforderung nicht ablehnen, da diese Herausforderung nicht exisitiert!';
	}

	// TODO: Check if this conditions are redundant
	if($user['clan'] != $clan_challenge['clan_challenged']) {
		return 'Du kannst die Herausforderung nicht ablehnen, da du nicht im herausgeforderten Clan bist!';
	} else if ($clan_challenge['active'] != 1) {
		return 'Du kannst die Herausforderung nicht ablehnen, da diese schon angenommen wurde!';
	}

	// okay seems to be okay
	db_query('UPDATE clan_challenge_requests SET active = false WHERE clan_challenged = ' .$user['clan'] . ' AND clan_requester = ' .$challenge_requester. ' AND active = TRUE');

	if(is_numeric($oclan['leader'])) {
		sendMessage($user['nickname'], $oclan['leader'], 'Clan-Fight', 'Der Clan  '.$clan['clanname'].' hat eure Herausforderungen zu einem Clan-Fight abgelehnt! Über "Mein Clan" könnt neue Herausforderungen erstellen!');
	}

	if(is_numeric($oclan['co_leader']) && $oclan['leader'] != $oclan['co_leader']) {
		sendMessage($user['nickname'], $oclan['co_leader'], 'Clan-Fight', 'Der Clan  '.$clan['clanname'].' hat eure Herausforderungen zu einem Clan-Fight abgelehnt! Über "Mein Clan" könnt neue Herausforderungen erstellen!');
	}

	if(is_numeric($clan_challenge['clan_challenge_creator']) && $clan_challenge['clan_challenge_creator'] != $oclan['leader'] && $clan_challenge['clan_challenge_creator'] != $oclan['co_leader']) {
		sendMessage($user['nickname'], $clan_challenge['clan_challenge_creator'], 'Clan-Fight', 'Der Clan  '.$clan['clanname'].' hat eure Herausforderungen zu einem Clan-Fight abgelehnt! Über "Mein Clan" könnt neue Herausforderungen erstellen!');
	}


	return NULL;
}

/**
 * Reads out the given date from the table "clan_challenge"
 * @param int $challenge_id
 * @param bool $buffer
 */
function getChallenge($challenge_id, $buffer = TRUE) {
	if($buffer && isset($GLOBALS['clan_challenge_buffered_instances'][$challenge_id])) {
		return $GLOBALS['clan_challenge_buffered_instances'][$challenge_id]; // okay, we already buffered the clan :)
	}

	$qry = db_query('SELECT * from clan_challenges WHERE clan_challenge_id = ' .$challenge_id);
	$GLOBALS['clan_challenge_buffered_instances'][$challenge_id] = mysqli_fetch_assoc($qry);
	return $GLOBALS['clan_challenge_buffered_instances'][$challenge_id]; // okay, we already buffered the clan :)
}


function getResult($challenge_id) {
	// clan_challenge_participants
}

function getOpenChallengeRequests($clanid) {
	$array = array();
	if(isClanLocked($clanid)) {
		return $array;
	}

	$sql = 'SELECT * from clan_challenge_requests WHERE challenged_time > TIMESTAMPADD(day, -1, now()) and active = TRUE AND (clan_requester = '.$clanid.' OR clan_challenged = '.$clanid.')';
	//	echo $sql . '<br>';
	$qry = db_query($sql);
	while($row = mysqli_fetch_assoc($qry)) {
		$array[] = $row;
	}
	return $array;
}

function getOpenChallengeRequestCount($clanid) {
	if(isClanLocked($clanid)) {
		return 0;
	}
	$sql = 'SELECT count(*) from clan_challenge_requests where challenged_time > TIMESTAMPADD(day, -1, now()) and active = TRUE AND (clan_requester = '.$clanid.' OR clan_challenged = '.$clanid.')';
	//	echo $sql . '<br>';
	$row = mysqli_fetch_row(db_query($sql));
	return $row[0];
}

// this method returns the challenge ids for open fights
function getOpenChallengeIDs($clanid) {
	// before doing anything check if we are locked
	$array = array();
	if(isClanLocked($clanid)) {
		return $array;
	}

	$sql = 'SELECT cc.clan_challenge_id FROM clan_challenges AS cc inner join clan_challenge_clans AS ccp ON cc.clan_challenge_id = ccp.clan_challenge_id WHERE clan_id = ' . $clanid . ' AND calculated = FALSE';
	$qry = db_query($sql);
	while($row = mysqli_fetch_row($qry)) {
		$array[] = $row[0];
	}
	// return the ids
	return $array;
}

function getActiveChallengeIDs($clanid) {
	// before doing anything check if we are locked
	$array = array();
	if(isClanLocked($clanid)) {
		return $array;
	}

	$sql = 'SELECT cc.clan_challenge_id FROM clan_challenges AS cc inner join clan_challenge_clans AS ccp ON cc.clan_challenge_id = ccp.clan_challenge_id WHERE clan_id = ' . $clanid . ' AND calculated = TRUE AND enddate > now() ORDER BY cc.date DESC ';
	$qry = db_query($sql);
	while($row = mysqli_fetch_row($qry)) {
		$array[] = $row[0];
	}
	// return the ids
	return $array;
}

/**
 * Returns the ChallengeIDs that are finished but not yet processed by the cronjob!!
 * @param int $clanid
 * @return array an array containing the ids of the clan_challenges
 */
function getAwatingChallengeIDs() {
	// before doing anything check if we are locked
	$array = array();
	$sql = 'SELECT distinct(cc.clan_challenge_id) FROM clan_challenges AS cc inner join clan_challenge_clans AS ccp ON cc.clan_challenge_id = ccp.clan_challenge_id WHERE elo_change is null AND calculated = TRUE AND enddate < now()';
	//	echo $sql . '<br>';
	$qry = db_query($sql);
	while($row = mysqli_fetch_row($qry)) {
		$array[] = $row[0];
	}
	// return the ids
	return $array;
}


// returns the last challenge ids (beginning with the latest to the first)
function getHistoricalChallengeIDs($clanid, $limit = NULL) {
	$addition = '';
	if($limit !== null && is_numeric($limit)) {
		$addition = ' LIMIT ' .$limit;
	}
	$sql = 'SELECT cc.clan_challenge_id FROM clan_challenges AS cc inner join clan_challenge_clans AS ccp ON cc.clan_challenge_id = ccp.clan_challenge_id WHERE clan_id = ' . $clanid . ' AND calculated = TRUE AND enddate < now() ORDER BY cc.date DESC ' . $addition;
	$qry = db_query($sql);
	$array = array();
	while($row = mysqli_fetch_row($qry)) {
		$array[] = $row[0];
	}
	// return the ids
	return $array;
}

function getParticipatingClanIDs($clan_challenge_id) {
	$array = array();
	$sql = 'SELECT clan_id FROM clan_challenge_clans AS ccp WHERE clan_challenge_id = ' . $clan_challenge_id . ' ORDER BY id';
	$qry = db_query($sql);
	while($row = mysqli_fetch_row($qry)) {
		$array[] = $row[0];
	}
	// return the ids
	return $array;
}

function getParticipatingClanData($clan_challenge_id, $clan_id) {
	$sql = 'SELECT * FROM clan_challenge_clans AS ccp WHERE clan_challenge_id = ' . $clan_challenge_id . ' AND clan_id = ' .$clan_id;
	$qry = silent_query($sql);
	if(!$qry) {
		echo $sql .'<br>';
	}
	return mysqli_fetch_assoc($qry);
}

function isCharBusyWithClanfighting($char_id) {
	$sql = 'SELECT count(*) as anzahl FROM clan_challenge_participants ccp INNER JOIN clan_challenges cc on ccp.clan_challenge_id = cc.clan_challenge_id WHERE cc.calculated = FALSE AND char_id = ' .$char_id;
	$row = mysqli_fetch_row(db_query($sql));
	return $row[0] > 0;
}

function getParticipatingCharIDs($clan_challenge_id, $clan_id) {
	$array = array();
	$sql = 'SELECT char_id, slot FROM clan_challenge_participants WHERE clan_challenge_id = ' . $clan_challenge_id . ' AND clan_id = ' .$clan_id;
	$qry = db_query($sql);
	while($row = mysqli_fetch_assoc($qry)) {
		$array[$row['slot']] = $row['char_id'];
	}
	// return the ids
	return $array;
}

function getParticipatingChar($clan_challenge_id, $clan_id, $char_id) {
	$qry = db_query('SELECT * FROM clan_challenge_participants WHERE clan_challenge_id = ' . $clan_challenge_id . ' AND clan_id = ' .$clan_id . ' AND char_id = ' . $char_id);
	$row = mysqli_fetch_assoc($qry);
	return $row;
}


function getClanLockedTime($clan_id) {
	if(!isClanLocked($clan_id)) {
		return 0;
	}
	$sql = 'SELECT TIMESTAMPDIFF(SECOND, NOW(), locked_until) FROM clan_locked WHERE clanid = ' . $clan_id;
	$row = mysqli_fetch_row(db_query($sql));
	if($row)
	return $row[0];
	return 0;
}

// Checks if the clan is locked, will lock it if required!
function isClanLocked($clanid) {
	if(!is_numeric($clanid)) {
		return true;
	}
	// we really want to know if we are locked at the moment. So readout everything at once ;), right join so we always have a row :D
	$sql = 'SELECT *, locked_until > now() as `lock`, (Select count(id) from user where clan = c2.id) as members FROM clan_locked right join clan c2 on c2.id = clan_locked.clanid where c2.id = ' . $clanid;
	//	echo $sql.'<br>';
	$lock = mysqli_fetch_assoc(db_query($sql));
	if($lock['lock'] == 0) { // don't matter if it is 0 or NULL

		// we do not have data in the clan locked table so we are not locked :D
		// or
		// we do have data but it seems we are not locked anymore ;)

		// are we still having enough members??
		if($lock['members'] >= MIN_MEMBERS) {
			if($lock['lock'] === 0) { // If data is available in 'clan_locked' remove it
				$sql = 'DELETE FROM clan_locked WHERE clanid = ' .$clanid .' AND locked_until < now()';
				db_query($sql);
			}

			// key so every thing seems to be fine :)
			return false; // the only case we may return false!
		} else if($lock['lock'] === NULL){ // we need to insert a row
			// INSERT
			$sql = 'INSERT INTO clan_locked(clanid, locked_until) values('.$clanid.', TIMESTAMPADD(HOUR, 24, CURRENT_TIMESTAMP))';
			//			echo $sql.'<br>';
			$res = db_query($sql);
			if(!$res)
			echo $sql . '<br>';
			db_query('UPDATE clan_challenge_requests SET active = FALSE WHERE (clan_challenged = '.$clanid.' OR clan_requester = '.$clanid.')');
			db_query('DELETE FROM clan_challenges WHERE calculated = FALSE AND clan_challenge_id IN (SELECT clan_challenge_id from clan_challenge_clans WHERE clan_id = '.$clanid.')');
		} else if($lock['members'] < MIN_MEMBERS){ // we have to update the actual row
			// UPDATE
			$sql = 'UPDATE clan_locked set locked_until = TIMESTAMPADD(HOUR, 24, CURRENT_TIMESTAMP) WHERE clanid = ' .$clanid;
			//			echo $sql.'<br>';
			$res = db_query($sql);
			if(!$res)
			echo $sql . '<br>';
		}
	} else if($lock['members'] < MIN_MEMBERS) {
		$sql = 'UPDATE clan_locked set locked_until = TIMESTAMPADD(HOUR, 24, CURRENT_TIMESTAMP) WHERE clanid = ' .$clanid;
		//                      echo $sql.'<br>';
		$res = db_query($sql);
		if(!$res)
		echo $sql . '<br>';
	}
	return true;
}


/**
 * This function lets a user take part in the clan fight! The clan fight will be calculated if all chars have been registered.
 * A challenge can only be joined if there is no other char of the same user joined. If the given slot is free and the challangeid is valid.
 * @param int $clan_challenge_id the id of the challenge
 * @param array $user the user array
 * @param int $charid the id of the char that should join
 */
function joinChallenge($clan_challenge_id, array $user, $charid, $slot ) {
	$clan = getClan($user['clan']);
	$char = getChar($charid);

	$forced = false;

	if(!$clan) {
		return 'Du bist in keinem Clan!!';
	}

	if($char['besitzer'] != $user['id']) {
		// the current user is NOT the owner of the char --> check if it is a coordinator
		if(isAuthorizedClanfightCoordinator($clan['id'], $user['id'], $clan_challenge_id)) {
			if(isUserOnline($char['besitzer'])) {
				return 'Du kannst den Char nicht an dem Kampf teilnehmen lassen, da der Besitzer online ist!';
			}
			// okay we may assign an additional char ;) // check if we have reached the limit is later!!
			$forced = true;
			$requestor = $user;
			$user = getUser($char['besitzer']);
		} else {
			return 'Du kannst den Char nicht an dem Kampf teilnehmen lassen, da er dir nicht gehört!';
		}
	}

	if($char['status'] != 'Frei') {
		if($forced && ($char['status'] == 'Kampf' || $char['status'] == 'Schatz Suche' || $char['status'] == 'Clanfight')) { // foreign assignment may also possible if the status is in "fetching" state
			// okay now check if the fetch-time is reached!!
			if($char['status'] == 'Schatz Suche') {
				$row = mysqli_fetch_row(db_query('SELECT Timestampdiff(Second,now(),dauer) as dauer from quests where charid = '.$charid));
			} else if($char['status'] == 'Clanfight') {
				if(isCharBusyWithClanfighting($charid)) {
					$row[0] = '???'; // we do not know when the fight is starting!
				} else if(getOngoingEventCount($charid, EVENT_CLAN_FIGHT) >= MAX_COUNT_PARALLEL_CFS) {
					$row[0] = 'MAX';
				} else {
					$row[0] = getEventStatusBlocked($charid);
				}
			} else {
				$row = mysqli_fetch_row(db_query('SELECT dauer - ' . time() . ' FROM kampf WHERE db_satz='.$charid ));
			}
			if(!is_numeric($row[0]) || $row[0] > 0) {
				return 'Du kannst den Char \''.$char['name'].'\' nicht an dem Kampf teilnehmen lassen, da er im Moment noch f&uuml;r ' . $row[0] . ' Sekunden mit ' . $char['status']. ' besch&auml;ftigt ist!';
			}
		} else {
			// char is not free but is he already participating?
			$participant = getParticipatingChar($clan_challenge_id, $clan['id'], $charid);
			if(!$participant || !$participant['forced']) {
				return 'Du kannst den Char nicht an dem Kampf teilnehmen lassen, da er nicht frei ist!';
			}
			db_query('UPDATE clan_challenge_participants SET forced = FALSE WHERE clan_challenge_id = ' . $clan_challenge_id . ' AND clan_id = '.$clan['id'].' AND char_id = ' . $charid);
			return NULL;
		}
	}

	$clan_challenge = getChallenge($clan_challenge_id);

	if($slot < 1 || $slot > $clan_challenge['anzahl_chars']) {
		return 'Der Slot ' . $slot . ' ist f&uuml;r diesen Kampf nicht verf&uuml;gbar!!';
	}

	$clan_ids = getParticipatingClanIDs($clan_challenge['clan_challenge_id']);

	if(array_search($user['clan'], $clan_ids) === FALSE) {
		// the clan does not match a participant!!
		return 'Du bist in keinem Clan der an dieser Challenge teilnimmt!';
	}

	// block the user actions so we can ensure he won't register multiple of its chars here!
	$ressource = 'User:'.$user['id'];
	if(!semaphoreUP($ressource)) {
		return 'Du kannst den Char nicht an dem Kampf teilnehmen lassen, da du scheinbar gerade auch mit was anderem beschäftigt bist, versuche es bitte nochmal!';
	}

	foreach ($clan_ids as $clan_id) {
		if(isClanLocked($clan_id)) {
			semaphoreDown($ressource);
			return 'Einer der Clans erfüllt die Anforderungen an Clanfights nichtmehr (wahrscheinlich zu wenig Members)!';
		}
	}

	// okay does this user already has a char in the fight??
	$qry = db_query('SELECT count(*) as anzahl FROM clan_challenge_participants ccp inner join chars c on ccp.char_id = c.id WHERE besitzer = ' .$user['id'] . ' AND clan_challenge_id = ' . $clan_challenge_id);
	$row = mysqli_fetch_assoc($qry);

	if($row['anzahl'] != 0) {
		semaphoreDown($ressource);
		return 'Konnte den Char nicht hinzufügen. Da du schon einen Char angemeldet hast!';
	}

	if($forced && !isForceForCharAcknowledged($char['id'])) {

	}

	$ressource2 = 'Clan:'.$clan['id'];
	if($forced && !semaphoreUP($ressource2)) {
		semaphoreDown($ressource);
		return 'Du kannst den Char nicht zuweisen, da exakt im gleichen Moment jemand aus deinem Clan auch einen Char zuweist!';
	}

	// okay and now check if we reached the maximum amount of leader assigned chars!!
	if($forced && getCountLeaderAssignedChars($clan['id'], $clan_challenge_id) >= MAX_LEADER_ASSIGNED_CHARS) {
		// shit!!
		semaphoreDown($ressource);
		semaphoreDown($ressource2);
		return 'Du kannst den Char nicht zuweisen, da die maximale Anzahl der erlaubten Zuweisungen ('.MAX_LEADER_ASSIGNED_CHARS.') schon erreicht ist!';
	}
	$sql = 'INSERT INTO clan_challenge_participants(clan_challenge_id, clan_id, char_id, slot, forced) values('.$clan_challenge_id.', '.$user['clan'].', '.$charid.', '.$slot.', ' .($forced?'TRUE':'FALSE') .')';
	$res = silent_query($sql);
	if(db_affected_rows() == 0) {
		if(!$forced) {
			// maybe the leader has already assigned the char and we now give the official okay for that!
			db_query('UPDATE clan_challenge_participants SET forced = FALSE, slot = '.$slot.' WHERE clan_challenge_id = '.$clan_challenge_id.' AND clan_id = '.$user['clan'].' AND char_id = '.$charid);
		}

		if(db_affected_rows() == 0) {
			semaphoreDown($ressource);
			if($forced) {
				semaphoreDown($ressource2);
			}
			return 'Konnte den Char nicht hinzufügen. Slot mittlerweile schon belegt?';
		}
	}

	// okay, now try to update the challenge to close the challenge and to start the calculating (as long we can do it ;))
	$sql = 'update clan_challenges AS cc SET calculated = TRUE WHERE '.
				'(SELECT count(*) FROM clan_challenge_participants AS ccp ' .
					'WHERE ccp.clan_challenge_id = cc.clan_challenge_id AND clan_id = '.$clan_ids[0].
				') = ' . $clan_challenge['anzahl_chars'] .
				' AND ' .
				'(SELECT count(*) FROM clan_challenge_participants AS ccp ' .
					'WHERE ccp.clan_challenge_id = cc.clan_challenge_id AND clan_id = '.$clan_ids[1].
				') = ' . $clan_challenge['anzahl_chars'];

	db_query($sql);
	$rows = db_affected_rows();
	semaphoreDown($ressource);
	if($forced) {
		semaphoreDown($ressource2);
	}

	if($rows  > 0) {
		// okay we now have a complete competition and the challenge is closed :)
		return calculateChallenge($clan_challenge_id);
	}

	if($forced) {
		sendMessage($requestor['nickname'], $char['besitzer'], 'Clanfightteilnahme', 'Dein Char ' . $char['name'] . ' wurde zu einem Clanfight hinzugezogen, du kannst ihn im Clanfight Men&uuml; abmelden oder dein OK geben, damit evtl. weitere Chars von offline Usern hinzugef&uuml;gt werden k&ouml;nnen!');
	}

	return NULL;
}

/**
 *
 * Lets the user to remove his char from the challenge
 * @param integer $clan_challenge_id
 * @param array $user
 * @param integer $charid
 */
function leaveChallenge($clan_challenge_id, array $user, $charid) {
	$clan = getClan($user['clan']);
	$char = getChar($charid);

	if(!$clan) {
		return 'Du bist in keinem Clan!!';
	}

	$forced = false;

	if($char['besitzer'] != $user['id']) {
		if(!isAuthorizedClanfightCoordinator($clan['id'], $user['id'], $clan_challenge_id)) {
			$forced = true;
			// authorized coordinators may remove a char!! But this one is not authorized
			return 'Du kannst den Char nicht vom Kampf zurückziehen, da er dir nicht gehört!';
		}
	}

	// Okay, the rest we can check with the delete statement much easier!
	// basically the check consists of a) is the challenge not yet calculated b) is char active in the challenge?
	$sql = 'DELETE FROM clan_challenge_participants WHERE (SELECT calculated FROM clan_challenges AS cc WHERE cc.clan_challenge_id = clan_challenge_participants.clan_challenge_id) = FALSE AND char_id = ' . $charid;
	db_query($sql);

	if(db_affected_rows() == 0) {
		return 'Der Char kann nicht aus dem Kampf zurückgezogen werden.';
	}

	if($forced) {
		sendMessage($user['nickname'], $char['besitzer'], 'Kick aus Clanfight', 'Dein Char ' . $char['name'] . ' wurde aus einem Clanfight gekickt!');
	}

	return NULL;
}

function cancelChallenge($clan_challenge_id, array $user) {
	$clan = getClan($user['clan']);
	$clan_ids = getParticipatingClanIDs($clan_challenge_id);

	if(!$clan) {
		return 'Du bist in keinem Clan!!';
	}

	if(!in_array($clan['id'], $clan_ids)) {
		return 'Dein Clan nimmt überhaupt nicht an diesem Clan-Fight teil!';
	}

	if(!isAuthorizedClanfightCoordinator($clan['id'], $user['id'], $clan_challenge_id)) {
		return 'Du hast nicht die Rechte diesen Clan-Fight zur&uuml;ck zu ziehen!';
	}

	// calculate who to notify!!
	$user_to_notify = array();
	foreach ($clan_ids as $clan_id) {
		$clan_tmp = getClan($clan_id);
		if(is_numeric($clan_tmp['leader']) && !in_array($clan_tmp['leader'], $user_to_notify)) {
			$user_to_notify[] = $clan_tmp['leader'];
		} else if(is_numeric($clan_tmp['co_leader']) && !in_array($clan_tmp['co_leader'], $user_to_notify)) {
			$user_to_notify[] = $clan_tmp['co_leader'];
		}
		$char_ids = getParticipatingCharIDs($clan_challenge_id, $clan_id);
		foreach($char_ids as $char_id) {
			$char_tmp = getChar($char_id);
			if(is_numeric($char_tmp['besitzer']) && !in_array($char_tmp['besitzer'], $user_to_notify)) {
				$user_to_notify[] = $char_tmp['besitzer'];
			}
		}
	}

	// kay, finish him !!!
	$sql = 'DELETE FROM clan_challenges WHERE clan_challenge_id = ' .$clan_challenge_id . ' AND calculated = FALSE';
	//	echo $sql . '<br>';
	$res = db_query($sql);
	if(!$res)
	return 'Beim zur&uuml;ckziehen des Clan-Fights ist ein technischer Fehler aufgetreten!';

	foreach ($user_to_notify as $userid) {
		sendMessage($user['nickname'], $userid, 'Clan-Fight', 'Der Clan  '.$clan['clanname'].' hat den Clan-Fight zurückgezogen!');
	}

	return NULL;
}


/**
 * This function does the calculation of the clan fight.
 * @param int $clan_challenge_id the id of the challenge
 */
function calculateChallenge($clan_challenge_id) {
	$challenge = getChallenge($clan_challenge_id);

	$start_time = time();

	// okay the calculated flag is already set so readout all the required data for further processing!
	$clan_ids = getParticipatingClanIDs($clan_challenge_id);
	$clan_chars = array();

	$event_id = createEvent(EVENT_CLAN_FIGHT);

	foreach ($clan_ids as $clan_id) {
		// get the actual elo !!
		$clan_char_ids = getParticipatingCharIDs($clan_challenge_id, $clan_id);

		$sql = 'update clan_challenge_clans ccc inner join clan c on c.id = clan_id SET ccc.elo = c.elo WHERE clan_id IN (' . implode(', ', $clan_ids) .')';
		$res = db_query($sql);
		if(!$res)
		echo 'Could not execute QUERY : ' .$sql .'<br>';

		foreach ($clan_char_ids as $slot => $char_id) {
			addParticipant($event_id, getCharWithBuffs($char_id));
			$clan_chars[$clan_id][$slot] = getCharWithBuffs($char_id);
		}
	}

	$result = NULL;
	// okay now determine the type of fight
	switch($challenge['type']) {
		case DAVY_BACK_FIGHT:
			$result = calculateDavyBackClanFight($clan_challenge_id, $event_id, $clan_ids, $clan_chars, $start_time);
			if(!is_numeric($result))
			return $result;
			break;
		case SURVIVAL:
			$result = calculateSurvivalClanFight($clan_challenge_id, $event_id, $clan_ids, $clan_chars, $start_time);
			if(!is_numeric($result))
			return $result;
			break;
		default:
			// cannot create anything -.-
			return 'Der Clanfight Typ "'.$challenge['type'].'" wird nicht unterstützt, bitte melde dich beim Administrator!' ;
	}

	$sql = 'UPDATE clan_challenges set event_id = '.$event_id.', enddate = \''.date("Y-m-d H:i:s",$result).'\' WHERE clan_challenge_id = ' .$clan_challenge_id;
	$res = db_query($sql);
	if(!$res) {
		echo $sql.'<br>';
		return 'Konnte Datenbank-Anfrage nicht durchf&uuml;hren ... breche Skript ab!';
	}


	return NULL;
}

function calculateDavyBackClanFight($clan_challenge_id, $event_id, array $clan_ids, array $clan_char_array, $start_time) {
	$duration = DURATION_DAVY_BACK_FIGHT * 60;
	// okay, davy back fights are 1 vs. 1 and the winner is the one that has won the most fights.
	if(count($clan_char_array) != 2) {
		return 'Davy Back Fights können nur zwischen 2 Clans ausgetragen werden und nicht von ' . count($clan_char_array);
	}
	if(count($clan_char_array[$clan_ids[0]]) != count($clan_char_array[$clan_ids[1]])) {
		return 'Davy Back Fights können nur zwischen Clans mit der selben Anzahl von Chars ausgetragen werden!';
	}
	$count_chars = count($clan_char_array[$clan_ids[0]]);
	// okay ... now do the calculation!
	for($runde = 1; $runde<=$count_chars;$runde++) {
		$char_clan_1 = $clan_char_array[$clan_ids[0]][$runde];
		$char_clan_2 = $clan_char_array[$clan_ids[1]][$runde];

		// we do not need to know right now who won the battle! This is required in the post processing (cronjob)!
		$winner = calculateEventFight($event_id, $char_clan_1, $char_clan_2, ATTACK_SET_DAVY_BACK_FIGHT, $start_time + ($runde-1) * $duration, $start_time + $runde * $duration, 0.01, 0.01);
		if($winner['id'] == $char_clan_1['id']){
			$sql = 'UPDATE clan_challenge_clans SET points = points + 1 WHERE clan_challenge_id = ' . $clan_challenge_id . ' AND clan_id = ' .$clan_ids[0];
			// echo $sql . '<br>';
			db_query($sql);
		} else {
			$sql = 'UPDATE clan_challenge_clans SET points = points + 1 WHERE clan_challenge_id = ' . $clan_challenge_id . ' AND clan_id = ' .$clan_ids[1];
			// echo $sql . '<br>';
			db_query($sql);
		}
	}
	
	$end_time = $start_time + $count_chars* $duration;
	db_query('UPDATE event_chars SET block_end = \''.date("Y-m-d H:i:s",$end_time).'\' WHERE event_id = '.$event_id);
	
	return $start_time + ($runde-1) * $duration;
}

function calculateSurvivalClanFight($clan_challenge_id, $event_id, array $clan_ids, array $clan_char_array, $start_time) {
	$duration = DURATION_SURVIVAL_FIGHT * 60;
	// okay, survival fights are 1 vs. 1 and the winner is the one that has the last man standing.
	if(count($clan_char_array) != 2) {
		return 'Survival Fights können nur zwischen 2 Clans ausgetragen werden und nicht von ' . count($clan_char_array);
	}
	if(count($clan_char_array[$clan_ids[0]]) != count($clan_char_array[$clan_ids[1]])) {
		return 'Survival Fights können nur zwischen Clans mit der selben Anzahl von Chars ausgetragen werden!';
	}

	$counter_clan_1 = 1;
	$counter_clan_2 = 1;
	$char_clan_1 = $clan_char_array[$clan_ids[0]][$counter_clan_1];
	$char_clan_2 = $clan_char_array[$clan_ids[1]][$counter_clan_2];

	$runde = 0;
	// As long as char 1 and char 2 are set
	while($char_clan_1 != NULL && $char_clan_2 != NULL) {
		$runde++;
		$sieger = calculateEventFight($event_id, $char_clan_1, $char_clan_2, ATTACK_SET_SURVIVAL, $start_time + ($runde-1) * $duration, $start_time + $runde * $duration, 0.01, 0.01);
		if($sieger['id'] != $char_clan_1['id']) {
			// the loser is of clan 1
			$char_clan_1 = $clan_char_array[$clan_ids[0]][++$counter_clan_1];
			$char_clan_2 = $sieger; // the new array contains the adjusted health parameter
			db_query('UPDATE clan_challenge_clans SET points = points + 1 WHERE clan_challenge_id = ' . $clan_challenge_id . ' AND clan_id = ' .$clan_ids[0]);
		} else {
			// the loser is of clan 2
			$char_clan_2 = $clan_char_array[$clan_ids[1]][++$counter_clan_2];
			$char_clan_1 = $sieger; // the new array contains the adjusted health parameter
			db_query('UPDATE clan_challenge_clans SET points = points + 1 WHERE clan_challenge_id = ' . $clan_challenge_id . ' AND clan_id = ' .$clan_ids[1]);
		}

	}
	$end_time = $start_time + $runde * $duration;
	db_query('UPDATE event_chars SET block_end = \''.date("Y-m-d H:i:s",$end_time).'\' WHERE event_id = '.$event_id);
	return $char_clan_1 != null?1:-1;
}


function getClanFightCapableClanIds() {
	$clan_info = getAllClans();
	$ids = array();
	foreach ($clan_info as $clan) {
		if(!isClanLocked($clan['id'])) {
			$ids[] = $clan['id'];
		}
	}
	return $ids;
}


function updateAuthorizedClanfightCoordinators($clan_id, array $user_ids) {
	$ids = getAuthorizedClanfightCoordinatorIDs($clan_id);

	$add = array_diff($user_ids, $ids);
	$remove = array_diff($ids, $user_ids);

	if (count($remove) > 0) {
		$sql = 'DELETE FROM `clan_challenge_whitelist` WHERE `user_id` IN (' . implode(',', $remove) .') AND `clan_id` = ' . $clan_id;
		//	echo $sql . '<br>';
		db_query($sql);
	}

	foreach($add as $user_id) {
		$sql = 'INSERT INTO `clan_challenge_whitelist`(`user_id`, `clan_id`) values('.$user_id.', '.$clan_id.')';
		//		echo $sql . '<br>';
		db_query($sql);
	}
}

function getAuthorizedClanfightCoordinatorIDs($clan_id) {
	$sql = 'SELECT user_id as id FROM `clan_challenge_whitelist` WHERE `clan_id` = '.$clan_id;
	$qry = db_query($sql);
	$return = array();
	while($row = mysqli_fetch_assoc($qry)) {
		$return[] = $row['id'];
	}
	return $return;
}

/**
 * Checks if the user is authorized to coordinate clanfights (if a id is given, it checks this for a given clan_fight)
 *
 * @param int $clan_id the clan that is in scope
 * @param int $user_id the user that is in scope
 * @param int $clan_fight_id the optional clan_fights that is in scope
 */
function isAuthorizedClanfightCoordinator($clan_id, $user_id, $clan_fight_id = NULL) {
	// do we require this information for a specific clan_fight???
	if($clan_fight_id !== NULL) {
		$sql = 'SELECT COUNT(*) FROM `clan_challenge_clans` WHERE `clan_coordinator` = ' .$user_id. ' AND `clan_id` = '.$clan_id . ' AND `clan_challenge_id` = ' .$clan_fight_id;
		//		echo $sql . '<br>';
		$row = mysqli_fetch_row(db_query($sql));
		if($row[0] <= 0) {
			// okay, maybe its a leader ;)
			$clan = getClan($clan_id);
			if($clan['leader'] == $user_id || $clan['co_leader'] == $user_id) {
				// the leader or the co_leader is always authorized!
				return true;
			}
			// not authorized!
			return false;
		}
		// the coordinator is always authorized!
		return true;

	} else {
		// it is a more general thing ;)
		$sql = 'SELECT COUNT(*) FROM `clan_challenge_whitelist` WHERE `user_id` = ' .$user_id. ' AND `clan_id` = '.$clan_id;
		$row = mysqli_fetch_row(db_query($sql));
		if($row[0] <= 0) {
			// okay, maybe its a leader ;)
			$clan = getClan($clan_id);
			if($clan['leader'] == $user_id || $clan['co_leader'] == $user_id) {
				// the leader or the co_leader is always authorized!
				//echo 'true<br>';
				return true;
			}
			// not authorized!
			return false;
		}
	}
	return true;
}

/**
 * Calculates the clan exp for clan1
 * @param array $clan1 the clan for whom the experience is calculated
 * @param array $clan2 the other clan that was participating
 * @param int $winner the id of the clan that has won
 */
function calculateClanExperience(array $clan1, array $clan2, $winner) {
	$m = 1+pow(10, ($clan2['level'] - $clan1['level'])/20);
	$g = CLAN_EXP_FAKTOR_G;
	if($clan1['id'] == $winner) {
		$exp = (($clan1['level'] + $clan2['level'])/2)*$m*$g;
	} else {
		$exp = (($clan1['level'] + $clan2['level'])/3)*$m*$g;
	}
	return round($exp);
}

/**
 * Returns how many chars were assigned by the leader for the given fight
 * @param int $clan_id
 * @param int $clan_fight_id
 */
function getCountLeaderAssignedChars($clan_id, $clan_fight_id) {
	$qry = db_query('SELECT COUNT(*) FROM `clan_challenge_participants` WHERE `clan_challenge_id` = ' .$clan_fight_id . ' AND `clan_id` = '.$clan_id .' AND `forced` = TRUE');
	$row = mysqli_fetch_row($qry);
	return $row[0];
}


/**
 * This function checks if there is a char-rule that allows forcing the char for leader assignment!
 * @param int $char_id
 */
function isForceForCharAcknowledged($char_id) {
	return true;
}

?>
