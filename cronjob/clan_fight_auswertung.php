<?php

include('db.php');
include('path.inc.php');
include_once (ROOT_PATH.'/include/sqlwrapper.inc.php');
include_once(ROOT_PATH.'/include/clan.inc.php');
include_once(ROOT_PATH.'/include/elo.inc.php');
include_once(ROOT_PATH.'/include/semaphore.inc.php');

function getClanRanking() {
	$qry = db_query('SELECT id, elo from clan ORDER by elo desc LIMIT 10');
	$clan_ids = array();
	while($res = mysqli_fetch_row($qry))
		$clan_ids[] = $res;

	return $clan_ids;
}

function distributeClanPrices(array $prices, array $results) {
	$n = min(count($prices), count($results));
	echo "Resultate: ";
	print_r($results);
	for($i = 0; $i<$n; $i++) {
		$points = $prices[$i];
		$winner = $results[$i][0]; // second index is the elo value!
		$clan = getClan($winner); // buffered so will not cause in a sql statement all the time!
		$sql = 'UPDATE clan SET elo = 1000, geld = geld + ' . $points . ' WHERE id = ' . $clan['id'];
		db_query($sql);
		insertClanTickerMessage($clan['id'], 'Das Preisgeld von '.$points.' f&uuml;r den '.$i.' Platz in der Clanliga erhalten!');
	}
	db_query('UPDATE clan SET elo = 1000');
}

$time = date("Hid");
if($time == 1) {
	// okay it is 0:00	in the morning at the first day of the month!
	$results = getClanRanking();

	$sql2 = 'Select avg(level) as dlevel from chars order by level desc limit 100';
	$row = mysqli_fetch_assoc(db_query($sql2));
	$dlevel = ceil($row['dlevel']);
	$summe = 20000000/(1+exp(5-$dlevel/14));

	for($i = 1; $i<=count($results) ; $i++) {
		$cprices[] = ceil($summe/pow(2, $i));
	}
	distributeClanPrices($cprices, $results);
}


$ressource = 'CLAN_AUSWERTUNG';


if(!semaphoreUP($ressource)) {
	echo 'konnte den cronjob nicht starten weil dieser schon in Gange ist!';
} else {
	$ids = getAwatingChallengeIDs();
	foreach($ids as $id) {
//		echo 'Werte Clanfight mit ID ' . $id . ' aus<br>'.PHP_EOL;
		$challenge = getChallenge($id);
		$clan_ids = getParticipatingClanIDs($id);

		$clan1 = getClan($clan_ids[0]);
		$clan2 = getClan($clan_ids[1]);

		$c_clan_data1 = getParticipatingClanData($id, $clan_ids[0]);
		$c_clan_data2 = getParticipatingClanData($id, $clan_ids[1]);

		if($c_clan_data1['points'] > $c_clan_data2['points']) {
			$winner = $clan1['id'];
		} else {
			$winner = $clan2['id'];
		}

		$expClan1 = calculateClanExperience($clan1, $clan2, $winner);
		$expClan2 = calculateClanExperience($clan2, $clan1, $winner);

		$newEloClan1 = calculateElo($c_clan_data1['elo'], $c_clan_data2['elo'], $c_clan_data1['points'] - $c_clan_data2['points'], $challenge['anzahl_chars']);
		$newEloClan2 = calculateElo($c_clan_data2['elo'], $c_clan_data1['elo'], $c_clan_data2['points'] - $c_clan_data1['points'], $challenge['anzahl_chars']);

		$eloClan1 = ($newEloClan1 - $c_clan_data1['elo']);
		$eloClan2 = ($newEloClan2 - $c_clan_data2['elo']);

		if($_clan_data1['elo_change'] == null) {
			$sql = 'UPDATE clan_challenge_clans SET elo_change = ' .$eloClan1 . ', exp_change = ' . $expClan1 . ' WHERE clan_challenge_id = ' . $id . ' AND clan_id = ' . $clan1['id'];
//			echo $sql . '<br>';
			db_query($sql);
			addClanExp($clan1['id'], $expClan1);
			$appendum = null;
			if($winner == $clan1['id']) {
				$appendum = ', siege = siege + 1';
			} else {
				$appendum = ', niederlagen = niederlagen + 1';
			}

			db_query('UPDATE clan SET elo = elo + ' .$eloClan1 . $appendum . ' WHERE id = ' . $clan1['id']);
		}

		if($_clan_data2['elo_change'] == null) {
			$sql = 'UPDATE clan_challenge_clans SET elo_change = ' .$eloClan2 . ', exp_change = ' . $expClan2 . ' WHERE clan_challenge_id = ' . $id . ' AND clan_id = ' . $clan2['id'];
//			echo $sql . '<br>';
			db_query($sql);
			addClanExp($clan2['id'], $expClan2);

			$appendum = null;
			if($winner == $clan2['id']) {
				$appendum = ', siege = siege + 1';
			} else {
				$appendum = ', niederlagen = niederlagen + 1';
			}

			db_query('UPDATE clan SET elo = elo + ' .$eloClan2 . $appendum . ' WHERE id = ' . $clan2['id']);
		}
	}
	semaphoreDown($ressource);
}


