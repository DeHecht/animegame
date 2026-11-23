<?php
/*
 *
 * @copyright (c) 2011 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once (ROOT_PATH . '/include/arena.inc.php');
include_once (ROOT_PATH . '/include/sqlwrapper.inc.php');

/**
 *
 * Converts the learn-ids of a char to the attack ids (that can be used to determine the technique)
 * @param array $learnids
 * @param int $char_id
 */
function convertLearnIDToAttackID (array $learnids, $char_id) {
	$result = array();

	$sql = 'SELECT id, at_id FROM lernen WHERE id IN ('.implode(',', $learnids).') AND besitzer = ' . $char_id;
	//		echo $sql .'<br>';
	$qry = silent_query($sql);
	if (!$qry) {
		return array();
	}
	while($row = mysqli_fetch_assoc($qry)) {
		for($i=0;$i<count($learnids);$i++) {
			if($learnids[$i] == $row['id']) {
				$result[$i] = $row['at_id'];
			}
		}
	}
	return $result;
}


/**
 *
 * Lets char1 fight against char2 (the attacks that are in the entry "attacken" will be used, no conditions!)
 * @param array $char1 the char-array for the first char that should be used (like it is)
 * @param array $char2 the char-array for the second char that should be used (like it is)
 * @return an array with the contents of the several rounds + metadata
 */
function calculateFight(array $char1, array $char2) {

	$chara_1 = $char1;
	$chara_2 = $char2;

	################## Init Kaempfer 1

	//aktuelle hp [0] max hp [1]
	$k_hp1 = explode(',', $chara_1['hp']);
	$k_mp1 = explode(',', $chara_1['mp']);

	$k_starke[0] = $chara_1['starke'];
	$k_speed[0] = $chara_1['speed'];
	$k_ver[0] = $chara_1['verteidigung'];
	$k_ausdauer[0] = $chara_1['ausdauer'];
	$k_glueck[0] = $chara_1['glueck'];

	// readout the attacks of fighter one

	$k_attacke1 = convertLearnIDToAttackID(explode(',', $chara_1['attacken']), $chara_1['id']);

	//aktuelle hp Spieler 1[0] Spieler 2[1]
	$k_hp[0] = round($k_hp1[0]);
	$k_mp[0] = round($k_mp1[0]);

	$k_aufgabe_a_1 = $k_hp1[1] / 100;
	$k_aufgabe[0] = $k_aufgabe_a_1 * $chara_1['aufgeben'];

	################## Init Kaempfer 2

	//aktuelle hp [0] max hp [1]
	$k_hp2 = explode(',', $chara_2['hp']);
	$k_mp2 = explode(',', $chara_2['mp']);

	$k_starke[1] = $chara_2['starke'];
	$k_speed[1] = $chara_2['speed'];
	$k_ver[1] = $chara_2['verteidigung'];
	$k_ausdauer[1] = $chara_2['ausdauer'];
	$k_glueck[1] = $chara_2['glueck'];

	$k_attacke2 = convertLearnIDToAttackID(explode(',', $chara_2['attacken']), $chara_2['id']);

	//aktuelle hp Spieler 1[0] Spieler 2[1]
	$k_hp[1] = round($k_hp2[0]);
	$k_mp[1] = round($k_mp2[0]);

	$k_aufgabe_a_2 = $k_hp2[1] / 100;
	$k_aufgabe[1] = $k_aufgabe_a_2 * $chara_2['aufgeben'];


	$runden_type1 = 0;
	$runden_type2 = 0;
	$x = 0;

	$runden_summon1 = '';
	$runden_summon2 = '';
	$runden_gif1 = 0;
	$runden_gif_technik1 = '';
	$runden_gif2 = 0;
	$runden_gif_technik2 = '';
	$statChange_Char1 = array();
	$statChange_Char2 = array();
	$tmp_hp = array();

	// now that we know what attacks our chars are capable of readout the attack information!
	$kombined = array_merge($k_attacke1, $k_attacke2);
	if(count($kombined) > 0) {
		$sql = 'SELECT * FROM attacken WHERE id IN(' .implode(',', $kombined) . ')';
		//		echo $sql .'<br>';
		$qry = db_query($sql);

		$attack_data = array();
		while($row = mysqli_fetch_assoc($qry)) {
			$attack_data[$row['id']] = $row;
		}
	}

	$metaData = array();
	$metaData['host'] = $char1['id'];
	## Start the fight ;)

	// The "rundenArray" has a specific structure. For each round played another entry is placed.
	// An entry contains { char1_array*, char2_array*, atk_char1, atk_char2, dmg_char1, dmg_char2 }
	// the char arrays contain the stats of the chars at the end of the round !

	$chara_1['hp'] = $k_hp[0];
	$chara_1['hp_max'] = $k_hp1[1];
	$chara_1['mp'] = $k_mp[0];
	$chara_1['mp_max'] = $k_mp1[1];
	$chara_2['hp'] = $k_hp[1];
	$chara_2['hp_max'] = $k_hp2[1];
	$chara_2['mp'] = $k_mp[1];
	$chara_2['mp_max'] = $k_mp2[1];

	$runde['char1_array'] = $chara_1;
	$runde['char2_array'] = $chara_2;
	$rundenArray = array($runde);

	//globals erstellen für den generic-typ
	$GLOBALS['kampf']['k_starke'] = &$k_starke;
	$GLOBALS['kampf']['k_speed'] = &$k_speed;
	$GLOBALS['kampf']['k_ver'] = &$k_ver;
	$GLOBALS['kampf']['k_hp'] = &$k_hp;
	$GLOBALS['kampf']['k_hp1'] = &$k_hp1;
	$GLOBALS['kampf']['k_hp2'] = &$k_hp2;
	$GLOBALS['kampf']['k_mp'] = &$k_mp;
	$GLOBALS['kampf']['k_mp1'] = &$k_mp1;
	$GLOBALS['kampf']['k_mp2'] = &$k_mp2;
	$GLOBALS['kampf']['tmp_hp'] = &$tmp_hp;
	$GLOBALS['kampf']['schaden1'] = &$schaden_1;
	$GLOBALS['kampf']['schaden2'] = &$schaden_2;
	$GLOBALS['kampf']['aussetzen'] = &$aussetzten_runde;

	while ($k_hp[0] > $k_aufgabe[0] AND $k_hp[1] > $k_aufgabe[1] AND $x < 10) {

		$runde = array();
		$runde['char1_array'] = $char1;
		$runde['char2_array'] = $char2;

		$technick1 = $attack_data[$k_attacke1[$x]];
		$technick2 = $attack_data[$k_attacke2[$x]];

		///////////////////Hier laedt er den RELOAD der technik wenn die zusammen setzung nicht stimmen sollte
		include (ROOT_PATH . '/include/kampf/andere_technik.php'); /////////////////// Wenn man ne andere technik sich aussuchen will...

		$speeds_char1 = ($k_speed[0] + $technick1['speed']);
		$speeds_char2 = ($k_speed[1] + $technick2['speed']);

		////// MUSS SO BLEIBEN WICHTIG
		$aussetzten_runde = '';
		//Diese Variablen speichern den Wert um wieviel HP geheilt wird damit wir falls nötig diesen durch Healkonter
		//in Schaden umwandeln können
		$tmp_hp[0] = 0;
		$tmp_hp[1] = 0;
		//Diese Werte sind wichtig, damit wir wissen ob eine Heal-Attacke eingesetzt wurde und wir die HP falls sie das
		//Maximum überschreiten zurücksetzen müssen
		$tmp_hp[2] = false;
		$tmp_hp[3] = false;
		##################################MP, SSJ Verwandlungen, HP, Koerper Tausch Technicken
		include (ROOT_PATH . '/include/kampf/majin.php');

		if (!$aussetzten_runde) { /////DAS FUER MAJIN ATTACKEN
			include (ROOT_PATH . '/include/kampf/atk_wert.php');
			include (ROOT_PATH . '/include/kampf/tausch2.php'); //////////////////Muss ganz oben sein wegen technik Tauschen
			include (ROOT_PATH . '/include/kampf/gift.php');
			include (ROOT_PATH . '/include/kampf/frucht.php');
			include (ROOT_PATH . '/include/kampf/runde.php');
			include (ROOT_PATH . '/include/kampf/mp.php');
			include (ROOT_PATH . '/include/kampf/hp.php');
			include (ROOT_PATH . '/include/kampf/hp2.php');
			include (ROOT_PATH . '/include/kampf/SSJ.php');
			include (ROOT_PATH . '/include/kampf/kaioken.php');
			include (ROOT_PATH . '/include/kampf/kaioken2.php');
			include (ROOT_PATH . '/include/kampf/copy.php');
			include (ROOT_PATH . '/include/kampf/lose.php');
			include (ROOT_PATH . '/include/kampf/ausdauer.php');
			include (ROOT_PATH . '/include/kampf/mpv.php');
			include (ROOT_PATH . '/include/kampf/hpmp.php');
			include (ROOT_PATH . '/include/kampf/generic_technik.php');
			include (ROOT_PATH . '/include/kampf/lose2.php');
		}

		include (ROOT_PATH . '/include/kampf/generic_rechnung.php');
		include (ROOT_PATH . '/include/kampf/atk_wert.php');

		if (!$aussetzten_runde) { /////DAS FUER MAJIN ATTACKEN
			//Als letztes um alle HP-Attacken zu kontern (in $tmp_hp gespeichert)
			include (ROOT_PATH . '/include/kampf/konter_heal.php');
			//Nach Konterheal, damit überstehende HP bleibt
			include (ROOT_PATH . '/include/kampf/tausch.php');
			include (ROOT_PATH . '/include/kampf/reborn.php');
		}
		include (ROOT_PATH . '/include/kampf/heilung.php');

		#####################################KAMPF SYSTEM
		include (ROOT_PATH . '/include/kampf/kampf_rechnung.php');

		// stupid, i know .... but what else should i do if i don't want to rewrite it all -.-
		// maybe you should have set a reference to the variable? :P (radi)

		$chara_1['starke'] = $k_starke[0];
		$chara_1['speed'] = $k_speed[0];
		$chara_1['verteidigung'] = $k_ver[0];
		$chara_1['ausdauer'] = $k_ausdauer[0];
		$chara_1['glueck'] = $k_glueck[0];

		$chara_1['hp'] = $k_hp[0];
		$chara_1['hp_max'] = $k_hp1[1];
		$chara_1['mp'] = $k_mp[0];
		$chara_1['mp_max'] = $k_mp1[1];

		$chara_2['starke'] = $k_starke[1];
		$chara_2['speed'] = $k_speed[1];
		$chara_2['verteidigung'] = $k_ver[1];
		$chara_2['ausdauer'] = $k_ausdauer[1];
		$chara_2['glueck'] = $k_glueck[1];

		$chara_2['hp'] = $k_hp[1];
		$chara_2['hp_max'] = $k_hp2[1];
		$chara_2['mp'] = $k_mp[1];
		$chara_2['mp_max'] = $k_mp2[1];

		$runde['char1_array'] = $chara_1;
		$runde['char2_array'] = $chara_2;

		$runde['atk_char1'] = $k_attacke1[$x];
		$runde['atk_char2'] = $k_attacke2[$x];
		$runde['dmg_char1'] = $schaden_1;
		$runde['dmg_char2'] = $schaden_2;

		$x++;
		$rundenArray[] = $runde;
	}

	if(($k_hp[0] > $k_aufgabe[0] && $k_hp[1] > $k_aufgabe[1]) || ($k_hp[0] <= $k_aufgabe[0] && $k_hp[1] <= $k_aufgabe[1])) {
		// Both are above or both are below their limit to surrender?
		if($k_hp[0] >= $k_hp[1]) { // If even the creator wins ;)
			$metaData['winner'] = $chara_1['id'];
		} else {
			$metaData['winner'] = $chara_2['id'];
		}
	} else if($k_hp[0] <= $k_aufgabe[0]) { // char1 is about to surrender
		$metaData['winner'] = $chara_2['id'];
	} else {
		$metaData['winner'] = $chara_1['id'];
	}

	$metaData['rounds'] = $rundenArray;
	$metaData['data'] = array();
	return $metaData;
}


function calculateExperience(array $char1, array $char2, $winner, $factor) {
	$exp[$char1['id']] = 0;
	$exp[$char2['id']] = 0;
	if($winner == $char1['id']) {
		$exp[$char1['id']] = (getPowerLevelWithBuffs($char1['id']) + getPowerLevelWithBuffs($char2['id'])) * 2.2 * $factor;
		$exp[$char2['id']] = (getPowerLevelWithBuffs($char1['id']) + getPowerLevelWithBuffs($char2['id'])) * $factor;
	} else {
		$exp[$char1['id']] = (getPowerLevelWithBuffs($char1['id']) + getPowerLevelWithBuffs($char2['id'])) * $factor;
		$exp[$char2['id']] = (getPowerLevelWithBuffs($char1['id']) + getPowerLevelWithBuffs($char2['id'])) * 2.2 * $factor;
	}
	$exp[$char1['id']] = round($exp[$char1['id']]);
	$exp[$char2['id']] = round($exp[$char2['id']]);
	return $exp;
}



?>
