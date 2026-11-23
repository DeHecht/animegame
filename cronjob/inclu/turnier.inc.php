<?php
/*
 *
 * @copyright (c) 2011 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once 'exp.inc.php';
include_once 'char.inc.php';
include_once ('messagefunctions.inc.php');

/**
 * This function is ment to replace the copy&paste calculation that was previously when processing the result of a tournament.
 * @param string $name the name of the tournament (gross, klein, etc.)
 * @param int $additionalExpPerRound the amount of experience a char should get when reaching to the next level
 * @param int $additionalMoneyPerRound the amount of experience a char should get when reaching to the next level
 */
function generateTournamentResults($name, $additionalExpPerRound, $additionalMoneyPerRound) {
	// Generates a Ranking of the Chars like that based on the round, so [0] array( char1), [1] array(char2), [2] array(char3, char4) and so on!
	// the date containing is the CharId, the experience and money is transfered automatically ;)

	$charArray = array();

	$charids = array();

	// First calculate the money and exp the char gets just for doing good at fights ;)
	$qry = silent_query('select char1, char2, exp1, exp2, geld1, geld2, round from turnier_kampf where art = \''.$name.'\' order by round asc');

	while($fight = mysqli_fetch_assoc($qry)) {
		if(!isset($charArray[$fight['char1']])) {
			$charArray[$fight['char1']] = array('exp' => 0, 'money' => 0);
			$charids[$fight['char1']] = 1;
		}
		$charArray[$fight['char1']]['exp'] += $fight['exp1'];
		$charArray[$fight['char1']]['money'] += $fight['geld1'];

		if(!isset($charArray[$fight['char2']])) {
			$charArray[$fight['char2']] = array('exp' => 0, 'money' => 0);
			$charids[$fight['char2']] = 1;
		}
		$charArray[$fight['char2']]['exp'] += $fight['exp2'];
		$charArray[$fight['char2']]['money'] += $fight['geld2'];
	}

	$returnArray = array();

	// Now calculate the money and exp the char gets just for proceeding in the tournament (in this case ... order the chars right away ;))
	$qry = silent_query('Select count(win) as anzahl, win from turnier_kampf where art = \''.$name.'\' group by win order by count(win) desc');
	$maxRank = -1;
	while ($participant = mysqli_fetch_assoc($qry)) {
		$charids[$participant['win']] = 0; // okay we already proceeded this one :)
		// okay, first add experience the the $charArray !!
		$charArray[$participant['win']]['exp'] += $additionalExpPerRound * $participant['anzahl'];
		$charArray[$participant['win']]['money'] += $additionalMoneyPerRound * $participant['anzahl'];
		if($maxRank == -1) {
			$maxRank = $participant['anzahl'];
		}
		$returnArray[$maxRank - $participant['anzahl']][] = $participant['win'];
		// add all the experience collected in this tournament at once ;)
		addExpToChar($participant['win'], $charArray[$participant['win']]['exp']);
		// the char is now already buffered, so no problem with that ;)
		$char = getChar($participant['win']);
		$sql = 'UPDATE user set geld = geld + ' . $charArray[$participant['win']]['money'] . ' WHERE id = ' . $char['besitzer'];
		echo $sql . ' <br>';
		silent_query($sql);
	}

	foreach ($charids as $key => $value) {
		if($value === 1) { // we did not have processed this id when processing the winners -> it is a ultimate loser!
			$returnArray[$maxRank][] = $key;

			addExpToChar($key, $charArray[$key]['exp']);
			// the char is now already buffered, so no problem with that ;)
			$char = getChar($key);
			$sql = 'UPDATE user set geld = geld + ' . $charArray[$key]['money'] . ' WHERE id = ' . $char['besitzer'];
			echo $sql . ' <br>';
			silent_query($sql);

		}
	}

	echo 'The results!<br>';
	print_r($returnArray);

	return $returnArray; // return the array for further investigations like giving out a nature fruit ;), preventing from deletion etc.
}

/**
 * Generates the Rank of a char using the depth of the charid in the ranking array. E.g. 0 is for 1., 1 is for 2., 2 is for 3.-4., ...
 * @param int $depth the depth
 */
function generateRanglistString($depth) {
	if(!is_numeric($depth) || $depth < 0) {
		return 'NaN.';
	}
	if($depth <= 1) {
		return ($depth + 1).'.'; // 1. and 2.
	} else {
		return (pow(2, $depth - 1) + 1) . '. - '.(pow(2, $depth)).'.';
	}
}

function distributeShopPoints(array $prices, array $winners, $reason) {
	$n = min(count($prices), count($winners));
	for($i = 0; $i<$n; $i++) {
		$points = $prices[$i];
		foreach($winners[$i] as $winner) {
//			echo $winner . ' <br>';
			$char = getChar($winner); // buffered so will not cause in a sql statement all the time!
			$userid = $char['besitzer'];
			$sql = 'UPDATE user set pkt = pkt + '.$points.' WHERE id = '. $userid;
			echo $sql . ' <br>';
			silent_query($sql);
			insertUserTickerMessage($userid, $points .' IP durch den '.generateRanglistString($i).' Platz im '.$reason.' erhalten!');
		}
	}
}

function distributeUserPrices(array $prices, array $winners, $reason) {
        $n = min(count($prices), count($winners));
        for($i = 0; $i<$n; $i++) {
                $points = $prices[$i];
                foreach($winners[$i] as $winner) {
//                      echo $winner . ' <br>';
                        $char = getChar($winner); // buffered so will not cause in a sql statement all the time!
                        $userid = $char['besitzer'];
                        $sql = 'UPDATE user set geld = geld + '.$points.' WHERE id = '. $userid;
                        echo $sql . ' <br>';
                        silent_query($sql);
                        insertUserTickerMessage($userid, $points .' Preisgeld durch den '.generateRanglistString($i).' Platz im '.$reason.' erhalten!');
                }
        }
}


function distributeClanPrices(array $prices, array $results, $reason) {
	$n = min(count($prices), count($results));

	for($i = 0; $i<$n; $i++) {
		$points = $prices[$i];
		foreach($results[$i] as $winner) {
			$char = getChar($winner); // buffered so will not cause in a sql statement all the time!
			$userid = $char['besitzer'];
			$data = mysqli_fetch_assoc(silent_query('Select clan from user where id = ' .$userid ));
			if(is_numeric($data['clan'])) {
				$sql = 'UPDATE clan SET geld = geld + ' . $points . ' WHERE id = ' . $data['clan'];
				echo $sql . ' <br>';
				silent_query($sql);
				insertClanTickerMessage($row['clan'], 'Das Preisgeld von '.$points.' f&uuml;r den '.generateRanglistString($i).' Platz im '.$reason.' erhalten!');
			}
		}
	}
}



?>
