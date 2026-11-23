<?php
/*
 * Created on 14.08.2007
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
/******************************************************************************
* Dieser Skript enthält Funktionen für das Implementieren eines Auktionshauses
* Ein Skript kann diese Funktionen verwenden, um Informationen darzustellen
*******************************************************************************
* Autor: Pascal Proksch
* Erstellungsdatum: 14.08.2007
* Zuletzt verändert: 15.10.2007

* changelog:
* 15.10. Fehler in der Bietefunktion. if($userid == $anbieter) war immer ungueltig!!
*******************************************************************************/

include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/messagefunctions.inc.php');
include_once(ROOT_PATH.'/include/semaphore.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/defines.inc.php');
include_once(ROOT_PATH.'/include/user.inc.php');

defineIfNotDefined('AUCTION_FEE_FACTOR', 0.05);

// Funktion um das aktuelle Mindestgebot einer Auktion zu ermitteln!
// Returns Mindestgebot
// @Return Integer
function aktuellesMindestgebot($auktionsid){
	$qry = db_query('SELECT aktuellesgebot, startgebot FROM auktion WHERE auktionsid = '.$auktionsid);
	if(mysqli_num_rows($qry) != 1){ // Auktion nicht vorhanden??
		return -1;
	}
	$result = mysqli_fetch_assoc($qry); // Hole dir den einen Datensatz
// Aktuelles Gebot vorhanden??
	if($result['aktuellesgebot']!=NULL){
		$minBetrag = $result['aktuellesgebot']*1.02; // Mindestens 2% mehr muss geboten werden
		return ceil($minBetrag-$result['aktuellesgebot']<10?$result['aktuellesgebot']+10:$minBetrag); // Mindestens aber 10 Gold mehr
	} else{ // Nein?? Dann den startBetrag
		return ceil($result['startgebot']);
	}
}


/**
 * 
 * siehe user.inc.php getRelevantMoney()
 * @param int $userid
 * @deprecated
 */
function getUserAvailableMoney($userid) {
	return getRelevantMoney($userid);
}


// Funktion zum bieten für eine Auktion
// Returns '' wenn alles ok, ansonsten eine Fehlerausgabe
// @Return String
function biete($userid, $auktionsid, $gebot, $anzahl = 0){
	// Keine Kommagebote!! Immer Abrunden!!
	if(!is_numeric($gebot) || floor($gebot) <= 0){
		return 'Gebot ung&uuml;ltig!';
	}
	$gebot = floor($gebot);
	$auktionsdaten = getEntryInformation($auktionsid);

	if($userid == $anbieter){
		return 'Sie k&ouml;nnen nicht auf eigene Items bieten';
	}

	$zeitdifferenz = $auktionsdaten['zeitdifferenz'];
	// Nun erstmal pruefen ob noch geboten werden kann
	if($zeitdifferenz < 0){
		return 'Deadline abgelaufen!';
	}

	$type = $auktionsdaten['type'];
	if ($type == 1) {
		return biete_normal($userid, $auktionsdaten, $gebot);
	} else if ($type == 2) {
		return biete_inverse($userid, $auktionsdaten, $gebot, $anzahl);
	}
	return 'Nicht unterst&uuml;tzer Aktionstyp!';
}

function biete_normal($userid, $auktionsdaten, $gebot) {
	$auktionsid = $auktionsdaten['auktionsid'];
	$itemname = $auktionsdaten['itemname'];
	$aktuellesgebot = $auktionsdaten['aktuellesgebot'];
	$startgebot = $auktionsdaten['startgebot'];
	$deadline = $auktionsdaten['deadline'];
	$zeitdifferenz = $auktionsdaten['zeitdifferenz'];
	$bieter = $auktionsdaten['bieter'];
	$anbieter = $auktionsdaten['anbieter'];
	
	// Was ist das aktuelle startgebot?? Rufe die Funktion auf!
	$klGebot = aktuellesMindestgebot($auktionsid);

	if($klGebot == -1){
		return 'Fehler beim Bieten aufgetreten';
	}

	if($gebot < $klGebot){
		return 'Es muss mindestens '.$klGebot.' geboten werden';
	}


	//Nun muss noch geprueft werden ob der User genug Geld hat und auch mit allen Geboten gesamt nicht in den Minusbereich kommt
	$user_a = mysqli_fetch_assoc(db_query('SELECT nickname, geld FROM user WHERE id = '.$userid));
	
	$usermoney = getRelevantMoney($userid);
	
	if($usermoney < $gebot){
		return 'Nicht genug Geld!';
	}

	//Wenn 10 minuten vor Deadline-ende geboten wird wird die deadline um 10 Minuten verschoben
	if($zeitdifferenz < 10){
		db_query('UPDATE auktion SET bieter='.$userid.', aktuellesgebot='.$gebot.', deadline=TIMESTAMPADD(Minute,10,TIMESTAMPADD(Second,-second(now()),now())) WHERE auktionsid='.$auktionsid);
		// Speichern der neuen deadline in die deadlinevariable
		$res_deadline = mysqli_fetch_assoc(db_query('SELECT deadline FROM auktion WHERE auktionsid = '.$auktionsid));
		$deadline = $res_deadline['deadline'];
	} else {
		// Ansonsten nur die Grundwerte
		db_query('UPDATE auktion SET bieter='.$userid.', aktuellesgebot='.$gebot.' WHERE auktionsid='.$auktionsid);
	}
	if ( $bieter !== NULL && trim($bieter) != '' ) {
		// Nun noch den aktuellen bieter eine Nachricht senden
		sendMessage('Auktionsmarkt', $bieter, '&Uuml;berboten worden', 'Sie sind von '.$user_a['nickname'].' &uuml;berboten worden, er bietet '.$gebot.' f&uuml;r das Item '.debbcode($itemname).'. Deadline: '.$deadline.' Link: [url='.$GLOBALS['server_url_long'].ROOT_URL.'/index.php?as=auktion&action=1&auktionsid='.$auktionsid.']Auktion[/url]');
	}

//	db_query($new_query);
//		echo '<br><br>'.$new_query.'<br><br>';
	return ''; // Leerer String, also kein Fehler!

}

function biete_inverse($userid, $auktionsdaten, $gebot, $gebot2) {
	$auktionsid = $auktionsdaten['auktionsid'];
	$itemname = $auktionsdaten['itemname'];
	$aktuellesgebot = $auktionsdaten['aktuellesgebot'];
	$startgebot = $auktionsdaten['startgebot'];
	$deadline = $auktionsdaten['deadline'];
	$zeitdifferenz = $auktionsdaten['zeitdifferenz'];
	$bieter = $auktionsdaten['bieter'];
	$anbieter = $auktionsdaten['anbieter'];

	if(!is_numeric($gebot2) || floor($gebot2) < $gebot){
		return 'Gebot ung&uuml;ltig!';
	}

	if($gebot < $startgebot){
		return 'Es muss mindestens '.$startgebot.' geboten werden';
	}

	$fee = ($gebot2 - $gebot + 1) * $startgebot * AUCTION_FEE_FACTOR;

	//Nun muss noch geprueft werden ob der User genug Geld hat und auch mit allen Geboten gesamt nicht in den Minusbereich kommt
	$usermoney = getRelevantMoney($userid);
	$required_money = $gebot2 + $fee;

	if($usermoney < $required_money){
		return 'Nicht genug Geld!';
	}

	db_query('UPDATE user SET geld = geld - '.$fee.' WHERE geld >= '.$required_money.' AND id = '.$userid);

	if (db_affected_rows() == 0) {
		return 'Fehler beim Abbuchen der Geb&uuml;hr!';
	}
	$sql = 'INSERT INTO inverse_auktion_gebote(auktionsid, bieter, range_from, range_to) values('.$auktionsid.','.$userid.','.$gebot.','.$gebot2.')';
	db_query($sql);
	return ''; // Leerer String, also kein Fehler!
}

// Funktion erstellt die Optionen die Auswahl
// Returns items mit Anzahl die der User verkaufen kann
// @Return String-Array
function getItemOptions($userid,$preselected){
	$result = NULL;
	$index = 0;
	// Die etwas abgewandelten SQL-Querys aus dem Onlinemarkt (MEGA-QUERY)
	$query = 'SELECT i.id, i.name, count(i.id) AS anzahl, \'ware\' as tablename FROM ware w INNER JOIN item i ON(i.id=w.item_id) WHERE w.user = '.$userid.' AND s_type = \'Trank\' GROUP BY i.id
					union
					SELECT i.id, i.name,count(i.id) AS anzahl, \'sp_ware\' as tablename FROM sp_ware w INNER JOIN sp_item i ON(i.id=w.item) WHERE w.user = '.$userid.' GROUP BY i.id
					union
					SELECT i.id, i.item AS name, count(i.id) AS anzahl, \'wochen_ware\' as tablename FROM wochen_ware w INNER JOIN wochen_markt i ON(i.id=w.item) WHERE w.user = '.$userid.' GROUP BY (i.id)';

	$qry = db_query($query);
	// Damit waeren alle noetigen Datenbankaufrufe erledigt!
	while($row = mysqli_fetch_assoc($qry)){
		if($row['id'] == $preselected){
			$result[$index++] = '<option value=\''.$row['id'].','.$row['tablename'].'\' selected>'.$row['name'].' | Anzahl:'.$row['anzahl'].'</option>';
		}
		else{
			$result[$index++] = '<option value=\''.$row['id'].','.$row['tablename'].'\'>'.$row['name'].' | Anzahl:'.$row['anzahl'].'</option>';
		}
	}
	return $result;
}

//Liefert Zusatzinformationen zu einer Waren-Datenbank
// Returns Array mit DatenbankName, ItemDatenbankName, itemFeldName
// @Return Array
function getDatabaseAdditions($db){
	$returnValue = Array(3);
	$returnValue[0] = $db;
	if($db == 'ware'){$returnValue[1] = 'item';$returnValue[2]='item_id';$returnValue[3]='name';}
	else if($db == 'sp_ware'){$returnValue[1] = 'sp_item';$returnValue[2]='item';$returnValue[3]='name';}
	else if($db == 'wochen_ware'){$returnValue[1] = 'wochen_markt';$returnValue[2]='item';$returnValue[3]='item';}
	return $returnValue;
}

// Diese Funktion stellt ein item in den Basar
// Returns '' wenn alles ok, ansonsten eine Fehlerausgabe
// @Return String
function insertItem($userid,$id_db_mix, $anzahl, $startgebot){
	if(!is_numeric($startgebot) || floor($startgebot) <=0){
		return 'Es muss ein g&uuml;ltiges Gebot abgegeben werden.';
	}
	$startgebot = floor($startgebot);
	if(!is_numeric($anzahl) || floor($anzahl) <= 0){
		return 'Es muss eine g&uuml;ltige Anzahl angegeben werden.';
	}
	$anzahl = floor($anzahl);

	if($anzahl > 1000) {
		return 'Es k&ouml;nnen nicht mehr als 1000 items gleichzeitig vertickt werden.';
	}

	preg_replace('#,#','',$id_db_mix); // Entferne die Gefaehrlichen \' !!!

	$mix_explode = explode(',',$id_db_mix);
	$id = $mix_explode[0]; // ID des items
	$db = getDatabaseAdditions($mix_explode[1]); // Datenbank die das Item speichert, mit samt den Zusatzinformationen abholen

	// Nun die Anzahl vergleichen
	$test = 'SELECT count(i.id) AS anzahl FROM '.$db[0].' as w INNER JOIN '.$db[1].' as i ON(i.id=w.'.$db[2].') where w.user= '.$userid.' AND i.id = '.$id;
	$result = mysqli_fetch_assoc(db_query($test));
//		echo $test;
	if($result['anzahl'] < $anzahl){
		return 'Nicht genug Items (Vorhanden:'.$result['anzahl'].', Eingetragen:'.$anzahl.')';
	}
	$test = 'SELECT '.$db[3].' AS name FROM '.$db[1].' WHERE id ='.$id;
	$itemname = mysqli_fetch_assoc(db_query($test));
//	echo $test;
	// Nun werden dem User die Items abgezogen und in die auktionstabelle eingefuegt.
	$qry = db_query('DELETE FROM '.$db[0].' WHERE '.$db[2].'='.$id.' AND user='.$userid.' LIMIT '.$anzahl);
	$anzahl2 = db_affected_rows();
	if($anzahl2 == 0){
		return 'Cheater!!';
	} else if($anzahl != $anzahl2){
		db_query('INSERT INTO auktion (itemid, itemname, tablename, anbieter, anzahl, deadline, startgebot, startdate) VALUES('.$id.',\''.$itemname['name'].'\',\''.$db[0].'\','.$userid.','.$anzahl2.',TIMESTAMPADD(Day,3,TIMESTAMPADD(Second,-second(now()),now())),'.$startgebot.',now())');
	} else{
		db_query('INSERT INTO auktion (itemid, itemname, tablename, anbieter, anzahl, deadline, startgebot, startdate) VALUES('.$id.',\''.$itemname['name'].'\',\''.$db[0].'\','.$userid.','.$anzahl.',TIMESTAMPADD(Day,3,TIMESTAMPADD(Second,-second(now()),now())),'.$startgebot.',now())');
	}
	return '';
}


// Diese Funktion erstellt die Auktionstabelle und die Transaktionentabelle
function createTables(){
	// Erstellen der Auktionstabelle
	db_query('DROP TABLE auktion');
	db_query('
		CREATE TABLE `auktion` (
			`auktionsid` int(10) unsigned NOT NULL auto_increment,
			`anbieter` int(10) unsigned NOT NULL,
			`bieter` int(10) unsigned default NULL,
			`itemid` int(10) unsigned NOT NULL,
			`itemname` varchar(20) collate utf8_unicode_ci NOT NULL,
			`tablename` varchar(15) collate utf8_unicode_ci NOT NULL,
			`anzahl` int(10) unsigned NOT NULL,
			`startgebot` decimal(10,0) unsigned NOT NULL,
			`aktuellesgebot` decimal(10,0) unsigned default NULL,
			`deadline` timestamp NULL default NULL,
			`startdate` timestamp NULL default NULL,
			`cheatingverdacht` int(10) unsigned NOT NULL,
			PRIMARY KEY  (`auktionsid`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0 ;
	');

	db_query('DROP TABLE auktion_transaktionen');
	db_query('
		CREATE TABLE `auktion_transaktionen` (
			`transaktionsid` int(10) unsigned NOT NULL auto_increment,
			`anbieter` int(10) unsigned NOT NULL,
			`bieter` int(10) unsigned NOT NULL,
			`itemid` int(10) unsigned NOT NULL,
			`tablename` varchar(15) collate utf8_unicode_ci NOT NULL,
			`anzahl` int(10) unsigned NOT NULL,
			`betrag` decimal(10,0) unsigned NOT NULL,
			`deadline` timestamp NULL default NULL,
			`cheatingverdacht` int(10) unsigned NOT NULL,
			PRIMARY KEY  (`transaktionsid`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0 ;
	');
}

function getAuktionTotalCount($kategorie,$itemnamepart,$maxPrice){
	// vorbereiten der uebergabevariablen zu sicherheitszwecken

	$priceDelimiter = '';
	$kategorieDelimitier ='';
	$stringDelimiter = '';

	if($maxPrice > 0){
		$priceDelimiter = ' AND startgebot <= '.$maxPrice.' AND (aktuellesgebot IS NULL OR aktuellesgebot <= '.$maxPrice.') ';
	}
	if($kategorie == 'wochen_ware' OR $kategorie == 'ware' OR $kategorie == 'sp_ware'){
		$kategorieDelimitier = ' AND tablename = \''.$kategorie.'\' ';
	}
	if($itemnamepart != NULL && $itemnamepart != ''){
		$stringDelimiter = ' AND itemname LIKE \'%'.$itemnamepart.'%\' ';
	}
	$test = 'SELECT count(auktionsid) as anzahl FROM auktion WHERE 1 '.$kategorieDelimitier.$stringDelimiter.$priceDelimiter.' AND TIMESTAMPDIFF(Minute,now(),deadline) >= 0';
	$qry = db_query($test);
	if(mysqli_num_rows($qry) == 0){
		return 0;
	}
	$result = mysqli_fetch_assoc($qry);
	return $result['anzahl'];
}

function getAuktionEntries($kategorie,$itemnamepart,$entriesPerSite,$pageNumber,$maxPrice,$order,$dir){
	// vorbereiten der uebergabevariablen zu sicherheitszwecken
	$priceDelimiter = '';
	$kategorieDelimitier ='';
	$stringDelimiter = '';
	$interval = ' LIMIT '.($entriesPerSite*$pageNumber).','.$entriesPerSite.' ';
	$returnArray = NULL;

	if($maxPrice > 0){
		$priceDelimiter = ' AND startgebot <= '.$maxPrice.' AND (aktuellesgebot IS NULL OR aktuellesgebot <= '.$maxPrice.') ';
	}
	if($kategorie == 'wochen_ware' OR $kategorie == 'ware' OR $kategorie == 'sp_ware'){
		$kategorieDelimitier = ' AND tablename = \''.$kategorie.'\' and itemname != \'Geheim!\'';
	}
	if($itemnamepart != NULL && $itemnamepart != ''){
		$stringDelimiter = ' AND itemname LIKE \'%'.$itemnamepart.'%\' ';
	}
	$index = 0;
	$test = 'SELECT u.nickname AS anbietername, u2.nickname AS bietername, a.* FROM auktion AS a INNER JOIN user as u ON a.anbieter = u.id LEFT JOIN user as u2 ON a.bieter = u2.id WHERE 1 '.$kategorieDelimitier.$stringDelimiter.$priceDelimiter.' AND TIMESTAMPDIFF(Minute,now(),deadline) >= 0 ORDER BY '.$order.' '.$dir.' '.$interval;
	$qry = db_query($test);
	//echo '<br>'.$test.'<br>';
	while($result = mysqli_fetch_assoc($qry)){
		$returnArray[$index++] = $result;
	}
	return $returnArray;
}

function getEntryInformation($auktionsid){
	if(!is_numeric($auktionsid)){return;}
	$qry = db_query('SELECT u.nickname AS anbietername, u2.nickname AS bietername, a.*, TIMESTAMPDIFF(Minute,now(),deadline) AS zeitdifferenz FROM auktion  AS a INNER JOIN user as u ON a.anbieter = u.id LEFT JOIN user as u2 ON a.bieter = u2.id WHERE auktionsid = '.$auktionsid);
	if(mysqli_num_rows($qry) == 0){ return NULL; }
	return mysqli_fetch_assoc($qry);
}

function getKategorieOptions($preselect){
	$result = NULL;
	$index = 0;
	$converter['ware'] = 'Tr&auml;nke';
	$converter['sp_ware'] = 'Items';
	$converter['wochen_ware'] = 'Teufelsfr&uuml;chte';

	$total = 0;
	$select = false;
	$qry = db_query('SELECT tablename,count(tablename) as anzahl FROM auktion WHERE TIMESTAMPDIFF(Minute,now(),deadline) >= 0 AND itemname != \'Geheim!\' GROUP BY tablename');
	while($row = mysqli_fetch_assoc($qry)){
		if($preselect == $row['tablename']){
			$result[$index++] = '<option value=\''.$row['tablename'].'\' selected>'.$converter[$row['tablename']].' | Anzahl:'.$row['anzahl'].'</option>';
			$select = true;
		} else{
			$result[$index++] = '<option value=\''.$row['tablename'].'\'>'.$converter[$row['tablename']].' | Anzahl:'.$row['anzahl'].'</option>';
		}
		$total += $row['anzahl'];
	}
	if(!$select){
		$result[$index++] = '<option value=\'all\' selected>Alles | Anzahl:'.$total.'</option>';
	} else{
		$result[$index++] = '<option value=\'all\'>Alles | Anzahl:'.$total.'</option>';
	}
	return $result;
}

function zurueckziehen($userid, $auktionsid, $pay){
	// So das eigentliche abziehen
	$auktionsdaten = getEntryInformation($auktionsid);
	if($userid != $auktionsdaten['anbieter']){
		return 'Sie sind garnicht der Anbieter dieser Auktion!';
	}
	if($pay == 'YES' && $auktionsdaten['aktuellesgebot'] == NULL){
		return 'Es wurde mit&uuml;bergeben, dass geboten wurde, es ist aber nicht geboten worden!';
	}
	else if($pay == 'NO' && $auktionsdaten['aktuellesgebot'] != NULL){
		return 'Es wurde zwischenzeitlich geboten! Zur&uuml;ckziehen nur mit Geb&uuml;hr m&ouml;glich!';
	}
	$gebuehr = floor($auktionsdaten['aktuellesgebot'] * 0.1);
	db_query('UPDATE user SET geld=geld-'.$gebuehr.' WHERE id='.$userid);

	db_query('DELETE FROM auktion WHERE auktionsid='.$auktionsid);
	$anzahl2 = db_affected_rows();
	if($anzahl2 == 0){
		return 'CHEATER!!';
	}
	$anzahl = $auktionsdaten['anzahl'];
	$item_row_name = 'item';
	if($auktionsdaten['tablename'] == 'ware'){$item_row_name = 'item_id';}
	while($anzahl-- > 0){
		$sql = 'INSERT INTO '.$auktionsdaten['tablename'].' ('.$item_row_name.', user) VALUES('.$auktionsdaten['itemid'].','.$userid.')';
		db_query($sql);
//			echo '<br>'.$sql.'<br>';
	}
	$sql = 'INSERT INTO nachricht(von, besitzer, betreff, text) VALUES (\'Auktionsmarkt\', '.$auktionsdaten['anbieter'].', \'Item zur&uuml;ckgezogen!\', \' Sie haben das Item '.$auktionsdaten['itemname'].' f&uuml;r eine Geb&uuml;hr von '.$gebuehr.' zur&uuml;ckgezogen!\')';
	db_query($sql);
//		echo '<br>'.$sql.'<br>';
	if($auktionsdaten['bieter'] != NULL){
		$sql = 'INSERT INTO nachricht(von, besitzer, betreff, text) VALUES (\'Auktionsmarkt\', '.$auktionsdaten['bieter'].', \'Item zur&uuml;ckgezogen!\', \' Das Item '.$auktionsdaten['itemname'].' wurde von '.$auktionsdaten['anbietername'].' zur&uuml;ckgezogen!\')';
		db_query($sql);
//			echo '<br>'.$sql.'<br>';
	}
}

function getAveragePrice($itemid, $tablename, $anzahl){
	$sql = 'SELECT avg(betrag) / avg(anzahl) AS average FROM auktion_transaktionen WHERE anbieter != 1 and itemid = '.$itemid.' and tablename = \''.$tablename.'\' and cheatingverdacht = 0 and TIMESTAMPADD(DAY,30,deadline) > CURRENT_TIMESTAMP';
	$qry = db_query($sql);
	$row = mysqli_fetch_assoc($qry);
	return round($row['average']*$anzahl);
}



?>
