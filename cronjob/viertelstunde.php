<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
// Laden der db-Verbindung
ignore_user_abort(true);

include "db.php";
include_once("path.inc.php");
include_once (ROOT_PATH.'/include/sqlwrapper.inc.php');
include_once(ROOT_PATH. "/include/defines.inc.php");
include_once(ROOT_PATH. "/include/random.inc.php");
include_once(ROOT_PATH. "/include/items.inc.php");
include_once(ROOT_PATH. "/include/messagefunctions.inc.php");

// Da es wichtig ist, dass die Querys korrekt abgehandelt werden wurde folgender Code eingefuehrt

function database_query($sql){
	$tmp = db_query($sql);
	if(!$tmp){
		echo 'SQL-Anweisung '.$sql.' konnte nicht ausgefuehrt werden!!<br>';
	}
	return $tmp;
}

function bearbeiteAbgelaufeneInverseAuktion($auktion) {
	$item = array('table_name' => $auktion['tablename'], 'id' => $auktion['itemid']);

	$min_max = mysqli_fetch_row(db_query('SELECT min(range_from), max(range_to), sum((range_to - range_from + 1) * 500) from inverse_auktion_gebote WHERE auktionsid = '.$auktion['auktionsid']));
	echo '<p>Min: '.$min_max[0].', Max: '.$min_max[1].', Total: '.$min_max[2].'</p>';

	$winner = NULL;
	$cost = NULL;
	if ($min_max[0] != NULL) {
		for ($i = $min_max[0]; $i <= $min_max[1]; ++$i) {
			$row = mysqli_fetch_row(db_query('SELECT distinct(bieter), count(*) from inverse_auktion_gebote WHERE auktionsid = '.$auktion['auktionsid'].' AND '.$i.' between range_from and range_to'));
			if ( $row[1] == 1 ) {
				// we have a winner!!!
				echo '<p>Spieler '.$row[0].' hat die inverse auktion mit dem betrag '.$i.' gewonnen!</p>';
				$winner = $row[0];
				$cost = $i;
				break;
			}
		}
	}

	$message_suffix = '"'.$auktion['itemname'].'" ('.$auktion['anzahl'].'x): Auktion von '.$auktion['anbietername'].' mit Deadline '.$auktion['deadline'].'.';
	$lose_message = NULL;
	if ($cost === NULL) {
		$lose_message = 'Leider gab ein kein kleinstes einmaliges Gebot. Das item wurde deshalb nicht versteigert!';
	} else {
		$lose_message = 'Leider hat jemand anderes das kleinste einmalige Gebot abgegeben. Das item wurde f&uuml;r '.displayMoney($cost).' versteigert';
	}
	$qry = NULL;
	if ($winner !== NULL) {
		$qry = db_query('SELECT bieter FROM inverse_auktion_gebote WHERE auktionsid = '.$auktion['auktionsid'].' AND bieter != '.$winner.' GROUP BY bieter');
	} else {
		$qry = db_query('SELECT bieter FROM inverse_auktion_gebote WHERE auktionsid = '.$auktion['auktionsid'].' GROUP BY bieter');

	}
	while ($row = mysqli_fetch_row($qry)) {
		if ($winner != $row[0]) {
			sendMessage('Auktionsmarkt', $row[0], 'Inverse Versteigerung verloren!', $lose_message.' '.$message_suffix);	
		}
	}

        db_query('DELETE FROM auktion WHERE auktionsid = '.$auktion['auktionsid']);

	if ($winner !== NULL) {
		createItemForUser($item, $winner, $auktion['anzahl']);
		$msg = 'Herzlichen Gl&uuml;ckwunsch, du hast die inverse Versteigerung (f&uuml;r '.displayMoney($cost).') gewonnen!';
		sendMessage('Auktionsmarkt', $winner, 'Inverse Versteigerung gewonnen!', $msg.' '.$message_suffix);
		sendMessage('Auktionsmarkt', $auktion['anbieter'], 'Item versteigert!', 'Item in inverser Auktion f&uuml;r '.displayMoney($cost).' versteigert! '.$message_suffix);
		db_query('UPDATE user SET geld = geld - '.$cost.' WHERE id = '.$winner);
	} else {
		sendMessage('Auktionsmarkt', $auktion['anbieter'], 'Item nicht versteigert!', 'Item in inverser Auktion wurde nicht versteigert! '.$message_suffix);
		createItemForUser($item, $auktion['anbieter'], $auktion['anzahl']);
	}
}

// Handelt eine einzelne Auktion ab
// @Return nichts
function bearbeiteAbgelaufeneAuktion($auktionsid){
//	echo '<br>bearbeite '.$auktionsid.'</br>';

	$qry = database_query('SELECT u.nickname AS anbietername, u2.nickname AS bietername, a.*, TIMESTAMPDIFF(Minute,now(),deadline) AS zeitdifferenz FROM auktion  AS a INNER JOIN user as u ON a.anbieter = u.id LEFT JOIN user as u2 ON a.bieter = u2.id WHERE auktionsid = '.$auktionsid);

	if(mysqli_num_rows($qry) == 0){ return NULL; }
	$result = mysqli_fetch_assoc($qry);

	if ($result['type'] == 2) {
		bearbeiteAbgelaufeneInverseAuktion($result);
		return;
	}

	$itemreceiver = '';
	
	// Nun muss geprüft werden, ob überhaupt jemand geboten hat!
	if($result['bieter'] == NULL){ // Wenn nicht, dann geht das item an den anbieter
		$itemreceiver = $result['anbieter'];
	}
	else{
		// Ansonsten geht das Item an den Bietenden
		$itemreceiver = $result['bieter'];
		// Aber erst muss das Geld überwiesen werden
		$erhalten = ceil($result['aktuellesgebot'] * 0.99);
		$gebuehr = $result['aktuellesgebot'] - $erhalten;
		
		database_query('UPDATE user SET geld = geld - '.$result['aktuellesgebot'].' WHERE id = '.$result['bieter']);
		database_query('UPDATE user SET geld = geld + '.$erhalten.' WHERE id = '.$result['anbieter']);

		if($result[cheatingverdacht] == 0){ // Wenn cheatingverdacht nicht besteht, dann pruefe ob Verdacht besteht!!
			$avgprice = getAveragePrice($result[itemid], $result[tablename], $result[anzahl]);
			if($avgprice != 0 && $avgprice * 1.2 < $result['aktuellesgebot'] && $result['anbieter'] != 1){
				$result[cheatingverdacht] = 1;
			}
		}

		//Transaktion verbuchen
		$test = 'INSERT INTO auktion_transaktionen (anbieter, bieter, itemid, tablename, anzahl, betrag, deadline,cheatingverdacht) VALUES('.$result['anbieter'].','.$result['bieter'].','.$result['itemid'].',\''.$result['tablename'].'\','.$result['anzahl'].','.$result['aktuellesgebot'].',\''.$result['deadline'].'\','.$result['cheatingverdacht'].')';
		database_query($test);
		//echo '<br><br>'.$test.'<br><br>';
	}
	$anzahl = $result['anzahl'];
	$item_row_name = 'item';
	if($result['tablename'] == 'ware'){$item_row_name = 'item_id';}

        #database_query('DELETE FROM auktion WHERE auktionsid = '.$auktionsid);

	while($anzahl-- > 0){

		if($anzahl > 1000 && $anzahl % 1000 == 0) {
			echo date("H:i:s").' Anzahl maxi Auktion '.$anzahl."\n";
		}
		silent_query('INSERT INTO '.$result['tablename'].' ('.$item_row_name.', user) VALUES('.$result['itemid'].','.$itemreceiver.')');
	}
	
	database_query('DELETE FROM auktion WHERE auktionsid = '.$auktionsid);
	
	if($result['bieter'] == NULL){
		database_query('INSERT INTO nachricht(von, besitzer, betreff, text) VALUES (\'Auktionsmarkt\', '.$result['anbieter'].', \'Item nicht verkauft\', \' Tut mir leid, doch Sie sind das Item '.$result['itemname'].' mit dem Startgebot '.$result['startgebot'].' nicht losgeworden. Deadline war '.$result['deadline'].'\')');
	}	else{
		// Nun noch die Gluecklichen benachrichtigen
		database_query('INSERT INTO nachricht(von, besitzer, betreff, text) VALUES (\'Auktionsmarkt\', '.$result['anbieter'].', \'Item erfolgreich verkauft\', \' Herzlichen Gl&uuml;ckwunsch, Sie sind das Item '.$result['itemname'].' f&uuml;r ganze '.$result['aktuellesgebot'].' an '.$result['bietername'].' losgeworden. Anzahl war '.$result['anzahl'].'. Nach abzug der Geb&uuml;hr von '.$gebuehr.' (ca. 1%) bleiben noch '.$erhalten.' &uuml;ber. Deadline war '.$result['deadline'].'\')');
		database_query('INSERT INTO nachricht(von, besitzer, betreff, text) VALUES (\'Auktionsmarkt\', '.$result['bieter'].', \'Item erfolgreich ersteigert\', \' Herzlichen Gl&uuml;ckwunsch, Sie haben das Item '.$result['itemname'].' f&uuml;r ganze '.$result['aktuellesgebot'].' von '.$result['anbietername'].' ersteigert. Anzahl war '.$result['anzahl'].', Deadline war '.$result['deadline'].'\')');
	}

	
}

//Sucht nach abgelaufenen Auktionen und handelt diese ab
// @Return nichts
function bearbeiteAlleAbgelaufenenAuktionen(){
	$qry = database_query('SELECT auktionsid from auktion where TIMESTAMPDIFF(Minute,now(),deadline) < 0');
	// Gehe alle Auktionen durch und bearbeite diese
	$index = 0;
	while($result = mysqli_fetch_assoc($qry)){
		bearbeiteAbgelaufeneAuktion($result['auktionsid']);
		$index++;
	}
	return $index;
}

function insertAuktionen(){
	if(Date("D") == 'Sun'){
		defineIfNotDefined('GEHEIM_START_GEBOT', 1000000);
		
		// Definiert wieviele Auktion insgesamt minimal geplant werden
		defineIfNotDefined('GEHEIM_ANZAHL_AUKTIONEN_MIN', 1);
		// Definiert wieviele Auktion insgesamt maximal geplant werden
		defineIfNotDefined('GEHEIM_ANZAHL_AUKTIONEN_MAX', 2);
		// Definiert wieviele Items gleichzeitig pro Auktion auslaufen
		defineIfNotDefined('GEHEIM_ANZAHL_AUKTIONS_ITEMS', 4);
		// Definiert wie hoch die Chance ist eine Niete zu bekommen. 0.5 = 50%
		defineIfNotDefined('GEHEIM_CHANCE_NIETE', 0.5);
		
		$anzahl_auktionen = mt_random_wrapper(GEHEIM_ANZAHL_AUKTIONEN_MIN,GEHEIM_ANZAHL_AUKTIONEN_MAX); // anzahl der auktionen die gleichzeitig starten (ein oder zwei)
		$anzahl_auktions_items = GEHEIM_ANZAHL_AUKTIONS_ITEMS;
		$chance_niete = GEHEIM_CHANCE_NIETE * 100;
		$startgebot = GEHEIM_START_GEBOT;
		
		// Jeden Sonntag wird dieser Skript ausgefuehrt!
		// erstmal checken obs schon Auktionen für kommende Woche gibt
		$number = mysqli_num_rows(database_query('SELECT * FROM auktion WHERE anbieter = 1 and TIMESTAMPDIFF(Second,startdate,now()) < 0'));
//		echo $number.'= Number of effected rows';
		if($number == 0){
			// So, also es sind noch keine Auktionen vorhanden!!
			// Nun 6 Auktionen erstellen 3x2 Auktionen (update 2x2)
			$date = mysqli_fetch_assoc(database_query('Select current_date AS date'));
			$qry = database_query('SELECT id FROM `wochen_markt` WHERE aktiv = \'ja\'');
			$fruchtcount = 0;
			$frucht = array();
			while($result = mysqli_fetch_assoc($qry)){
				$frucht[$fruchtcount++] = $result[id];
			}
			
			// Ermittle die "Niete" (das wertloseste Item auf der Schatzsuche)
			$qry = database_query('Select id from `sp_item` where wert = (Select min(wert) from `sp_item`)');
			$nieten_id = mysqli_fetch_assoc($qry);
			$nieten_id = $nieten_id['id'];
			
			for($i = 0; $i<$anzahl_auktionen; $i++) {
				// Termin festlegen
				// Start Sonntag->0,Montag->1,Dienstag->2, Mittwoch->3, Donnerstag->4
				$off = mt_random_wrapper(0,4);
				$start = 'TIMESTAMPADD(Day,'.$off.',\''.$date[date].' 20:00:00\')';
				$deadline = 'TIMESTAMPADD(Day,'.($off+3).',\''.$date[date].' 20:00:00\')';

				for($t = 0; $t<$anzahl_auktions_items; $t++) {
					if(mt_random_wrapper(1,100) <= $chance_niete) {
						$item_table = 'sp_ware';
						$item_id = $nieten_id;
					} else {
						$item_table = 'wochen_ware';
						$item_id = $frucht[mt_random_wrapper(0,$fruchtcount-1)];
					}
					$test = 'INSERT INTO auktion (itemid, type, itemname, tablename, anbieter, anzahl, deadline, startgebot, startdate) VALUES('.$item_id.',2,\'Geheim!\',\''.$item_table.'\',1,1,'.$deadline.','.$startgebot.','.$start.')';
					// echo $test."<br>\n";
					database_query($test);
				}
			}
		} else if($number > GEHEIM_ANZAHL_AUKTIONEN_MAX * GEHEIM_ANZAHL_AUKTIONS_ITEMS){
			// echo 'Es gab zuviele Geheimauktionen ('.$number.') entferne die überschüssigen!';
			database_query('DELETE FROM auktion WHERE anbieter = 1 LIMIT '.($number-GEHEIM_ANZAHL_AUKTIONEN_MAX * GEHEIM_ANZAHL_AUKTIONS_ITEMS));
		}
	}
}

function detectMultiAccounts(){
	$sql = 'Select u1.id as uid1, u2.id as uid2, u1.nickname as n1, u2.nickname as n2 , u1.ip as ip1, u2.ip as ip2, u1.online_zeit as o1, u2.online_zeit as o2 from user as u1 inner join user as u2 on u1.ip = u2.ip and u1.id < u2.id and u1.ip != \'\' and abs(TIMESTAMPDIFF(HOUR, u1.online_zeit, u2.online_zeit)) < 24 and (abs(TIMESTAMPDIFF(HOUR, now(), u1.online_zeit)) < 1 || abs(TIMESTAMPDIFF(HOUR, now(), u2.online_zeit)) < 1)';
	$qry = db_query($sql);
	while($tmp = mysqli_fetch_assoc($qry)){
		$otime = min($tmp['o2'], $tmp['o1']);
		$sql = 'INSERT INTO multiuser(user1, user2, occured, type) values(\'' . $tmp['uid1'] . '\', \''.$tmp['uid2'].'\',\''.$otime.'\', \'IP_'.$tmp['ip1'].'\')';
		silent_query($sql);
	}
}

// sollte irgendwann mit der auktion_funktions verlinkt werden
function getAveragePrice($itemid, $tablename, $anzahl){
	$sql = 'SELECT avg(betrag) / avg(anzahl) AS average FROM auktion_transaktionen WHERE anbieter != 1 and itemid = '.$itemid.' and tablename = \''.$tablename.'\' and cheatingverdacht = 0';
	$qry = database_query($sql);
	$row = mysqli_fetch_assoc($qry);
	return round($row['average']*$anzahl);
}

$array = array();

if(!isset($argv)) {
	// invocation by server
	$arrayCpy = array();
} else {
	// invocation by cronjob
	exec('pgrep '.$argv[0], $array);

	$arrayCpy = array();
	foreach($array as $tmp) {
		if($tmp != getmypid()) {
			$arrayCpy[] = $tmp;
		}
	}
}

// check if this cronjob is already busy!!
if(count($arrayCpy)<=1) {
	// Starten der Auktionsverteilung
	$i = bearbeiteAlleAbgelaufenenAuktionen();
	//$sql = 'INSERT INTO nachricht(von, besitzer, betreff, text) VALUES (\'GM-INFO\', 3735, \'Cronjob lief!\', \' Der Vierteilstunde-Cronjob wurde ausgefuehrt! Es wurden '.$i.' Anfragen bearbeitet!\')';
	//database_query($sql);
	insertAuktionen();
	detectMultiAccounts();
}

