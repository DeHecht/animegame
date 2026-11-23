<?php
/*
 *
 * @copyright (c) 2011 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once(ROOT_PATH.'/include/user.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/clan.inc.php');
include_once(ROOT_PATH.'/include/clan_fights.inc.php');

function doTheClanfightTest($acceptTestCase, $amount) {

db_query('UPDATE user SET clan = null'); // clean all clans


$ids = getClanFightCapableClanIds();
$row = mysqli_fetch_row(db_query('SELECT COUNT(*) FROM user WHERE 0 < (SELECT COUNT(*) FROM chars WHERE besitzer = user.id)'));

if(count($ids) < $row[0] / MIN_MEMBERS) {
	//echo 'We may optimize the user('.$row[0].')/clan('.count($ids).') ratio<br>';

	db_query('UPDATE user SET clan = null'); // clean all clans
	db_query('UPDATE clan SET leader = null, co_leader = null');
	$sql = 'SELECT id FROM clan ORDER BY ID ASC';
	$qry = db_query($sql);
	while( $row = mysqli_fetch_assoc($qry)) {
		$sql = 'UPDATE user SET clan = ' .$row['id'] . ' WHERE clan is null AND 0 < (SELECT COUNT(*) FROM chars WHERE besitzer = user.id) LIMIT 5';
		//echo $sql . '<br>';
		db_query($sql);

		if(db_affected_rows() == 0) {
			break;
		}

	}

	$qry = db_query('SELECT clan, min(id) as id FROM user WHERE clan is not null GROUP BY clan');
	while($row = mysqli_fetch_assoc($qry)) {
		$sql = 'UPDATE clan SET leader = '.$row['id'].' WHERE id = '.$row['clan'];
		//echo $sql . '<br>';
		db_query($sql);
	}

}


db_query('DELETE FROM clan_challenge_requests');
db_query('DELETE FROM clan_challenges');
db_query('DELETE FROM events');
db_query('DELETE FROM clan_locked');




$ids = getClanFightCapableClanIds();
if(count($ids) < 2) {
	echo "zu wenig Clans die Clanfights machen können " . count($ids);
	die();
}

for($i=0;$i<count($ids) - 1;$i+=2) {
	calculateClanFight(getClan($ids[$i]), getClan($ids[$i+1]), $acceptTestCase);
}

#$clan1 = getClan($ids[0]);
#$clan2 = getClan($ids[1]);

function calculateClanFight($clan1, $clan2, $acceptTestCase) {
#	echo $clan1['id'] . ' ' . $clan2['id'] . ' ' . ($acceptTestCase?'TRUE':'FALSE') . '<br>';
$leaderClan1 = getUser($clan1['leader']);
$leaderClan2 = getUser($clan2['leader']);

$result = challengeClan($leaderClan1, $clan2['id'], $amount);
if($result !== NULL) {
	echo $result.'<br>';
}

if(getOpenChallengeRequestCount($clan1['id']) != 1) {
	echo 'Fehler: Es sollte mindestens ein Fight offen sein!!<br>';
}

if(getOpenChallengeRequestCount($clan2['id']) != 1) {
	echo 'Fehler: Es sollte mindestens ein Fight offen sein!!<br>';
}

if(count(getOpenChallengeRequests($clan1['id'])) != 1) {
	echo 'Fehler: Es sollte mindestens ein Fight offen sein!!<br>';
}

if(count(getOpenChallengeRequests($clan2['id'])) != 1) {
	echo 'Fehler: Es sollte mindestens ein Fight offen sein!!<br>';
}

$challengeArray = getOpenChallengeRequests($clan2['id']);

if($acceptTestCase) {

	$result = acceptChallengeRequest($challengeArray[0]['clan_requester'], $leaderClan2);

	if($result !== NULL) {
		echo $result.'<br>';
	}

	if(getOpenChallengeRequestCount($clan1['id']) > 0) {
		echo 'Fehler in getOpenChallengeRequestCount: Es sollte kein Fight-Request offen sein!!<br>';
	}

	if(getOpenChallengeRequestCount($clan2['id'])  > 0) {
		echo 'Fehler in getOpenChallengeRequestCount: Es sollte kein Fight-Request offen sein!!<br>';
	}

	if(count(getOpenChallengeRequests($clan1['id']))  > 0) {
		echo 'Fehler in getOpenChallengeRequests ('.$clan1['id'].'): Es sollte kein Fight-Request offen sein!!<br>';
	}

	if(count(getOpenChallengeRequests($clan2['id']))  > 0) {
		echo 'Fehler in getOpenChallengeRequests ('.$clan2['id'].'): Es sollte kein Fight-Request offen sein!!<br>';
	}


	if(count(getOpenChallengeIDs($clan1['id'])) != 1) {
		echo 'Fehler: Es sollte mindestens ein Fight offen sein!!<br>';
	}

	if(count(getOpenChallengeIDs($clan2['id'])) != 1) {
		echo 'Fehler: Es sollte mindestens ein Fight offen sein!!<br>';
	}

	$challengeIDsArray = getOpenChallengeIDs($clan1['id']);
	$clan_challenge_id = $challengeIDsArray[0];
	if(count(getParticipatingClanIDs($clan_challenge_id)) != 2) {
		echo 'Fehler in getParticipatingClanIDs: Es sollten 2 Clans teilnehmen!<br>';
	}

	// Okay now let the chars participate

	$chars1 = getCharsOfUser($leaderClan1['id']);
	$chars2 = getCharsOfUser($leaderClan2['id']);

	$found = -1;

        for($i=0;$i<count($chars1);$i++) {
                if($chars1[$i]['status'] == 'Frei') {
                        $found = $i;
                        break;
                }
        }
        if($found == -1) {
                echo 'Konnte keinen freien Char fuer User ' .$leaderClan1 . ' finden<br>';
        }


	$result = joinChallenge($clan_challenge_id, $leaderClan1, $chars1[$found]['id'], 1);
	if($result !== NULL) {
		echo $result .'<br>';
	}

	$found = -1;

	for($i=0;$i<count($chars2);$i++) {
		if($chars2[$i]['status'] == 'Frei') {
			$found = $i;
			break;
		}
	}
	if($found == -1) {
		echo 'Konnte keinen freien Char fuer User ' .$leaderClan2 . ' finden<br>';
	}

	$result  = joinChallenge($clan_challenge_id, $leaderClan2, $chars2[$found]['id'], 1);
	if($result !== NULL) {
		echo $result .'<br>';
	}



} else {
	$result = denyChallengeRequest($challengeArray[0]['clan_requester'], $leaderClan2);

	if($result !== NULL) {
		echo $result .'<br>';
	}

	if(getOpenChallengeRequestCount($clan1['id']) > 0) {
		echo 'Fehler in getOpenChallengeRequestCount: Es sollte kein Fight-Request offen sein!!<br>';
	}

	if(getOpenChallengeRequestCount($clan2['id'])  > 0) {
		echo 'Fehler in getOpenChallengeRequestCount: Es sollte kein Fight-Request offen sein!!<br>';
	}

	if(count(getOpenChallengeRequests($clan1['id']))  > 0) {
		echo 'Fehler in getOpenChallengeRequests ('.$clan1['id'].'): Es sollte kein Fight-Request offen sein!!<br>';
	}

	if(count(getOpenChallengeRequests($clan2['id']))  > 0) {
		echo 'Fehler in getOpenChallengeRequests ('.$clan2['id'].'): Es sollte kein Fight-Request offen sein!!<br>';
	}

}
}

}


?>
