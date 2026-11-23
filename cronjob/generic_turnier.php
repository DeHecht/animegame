<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
/************************************************************************
 * Dieses Tunier soll die verschiedenen Turnierberechnungsseiten
 * ersetzen. Durch Übergabe der Parameter beim starten des Cronjobs
 * lässt sich das Turnier konfigurieren.
 * @author Hecht
 *
 * TODO: Gruppenphase implementieren
 * TODO: Parameter (bis auf Name) sollten aus Datenbank geladen werden
 * 		 damit die GM direkt einfluss darauf nehmen können
 * TODO: Die Funktion turnierfight nutzt die komplexe Datei
 * 		 "turnier_kampf.php". Es wäre sinnvoll dies zu ersetzen.
 * TODO: Die Datenbankstruktur ist mehr als grauenvoll. Umsetzung wie
 * 		 bei der Schatzsuche gewünscht ^^
 ************************************************************************/


// Put args into get

// Neccessary for cronjob execution
if ($argv) {
	foreach ($argv as $k=>$v){
		if ($k==0) continue;
		$it = explode("=",$argv[$k]);
//		echo $it[0].'='.$it[1].'('.$argv[$k].')'."\n";
		if (isset($it[1])) $_GET[$it[0]] = $it[1];
	}
}



//set_time_limit(0);
//include "db.php";
//include "inclu/char.inc.php";
include('db.php');
include('path.inc.php');
include_once (ROOT_PATH.'/include/sqlwrapper.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');
include_once(ROOT_PATH.'/include/rassen.inc.php');
include_once(ROOT_PATH.'/include/defines.inc.php');
include_once(ROOT_PATH.'/include/event.inc.php');
include_once(ROOT_PATH.'/include/items.inc.php');

defineIfNotDefined('ATTACK_SET_TOURNAMENT', 1);
defineIfNotDefined('TOURNAMENT_FIGHT_DURATION', 5);


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


$name = $_GET['name'];		// Name des Turniers (benötigt)
$anzahl = $_GET['anzahl'];	// Anzahl (benötigt)

if(is_null($name)){
	echo 'Kein Name für Turnier angegeben!<br>';
	exit;
}

// Auswahlkriterien
$fusion = $_GET['fusion'];		// 1 für erlaubt
$special = $_GET['special'];	// Spezialchars 1 für erlaubt
$minlevel = $_GET['minlevel'];	// selbsterklärend
$maxlevel = $_GET['maxlevel'];	// selbsterklärend
$gain = $_GET['gain'];			// ('Anmeldung', 'PL', 'Level')
$randomize = $_GET['randomize']; // 1 für nach jeder Runde die reihenfolge setzen
$itemless = $_GET['itemless']; // gibt an ob das turnier die items ingorieren soll (standard sind items aktiv)
$fruit_chance = $_GET['fruit_chance']; // gibt die prozentuale chance auf eine frucht an
$fruit_type = isset($_GET['fruit_type']) ? $_GET['fruit_type'] : 'natur'; // gibt den typ der frucht an
$exclude_winners = isset($_GET['exclude_winners']) && $_GET['exclude_winners']; // gibt an ob gewinner vorheriger turniere des typs ausgeschlossen werden
$exclude_fusi_count = $_GET['exclude_fusi_count'];

$npc = $_GET['npc'];

// Turniereigenschaften
$gruppenphase = $_GET['gruppenphase'];	// Flag für Gruppenphase (1 für aktiv)
$grp_size = $_GET['grp_size'];			// Anzahl der Teilnehmer in einer Gruppe
$grp_proceed = $_GET['grp_proceed'];	// Anzahl der Teilnehmer die Gruppenphase verlassen (1 nur Sieger, 2 Sieger und Zweiter ..)

// Auswerten der Eingaben
if($gruppenphase == 1){
	if(is_null($grp_size)){ // Wenn es nicht gesetzt ist
		$grp_size = 4; // Default: 4 in einer Gruppe
	}
	if(is_null($grp_proceed)){ // Wenn es nicht gesetzt ist
		$grp_proceed = 1; // Default: Nur Sieger kommt weiter
	}
	// Berechne ob die Angegebene Anzahl passt (Anzahl muss durch grp_size teilbar und Ergebnis eine 2er Potenz sein)
	$count_groups = round($anzahl / $grp_size);
	$loga = log($count_groups * $grp_proceed, 2) ;
	if($anzahl % $grp_size != 0 || $loga != round($loga)){
		echo 'Parameter der Gruppenphasen sind ungültig!<br>';
	}

	echo 'Gruppenphasen werden leider noch nicht unterstützt<br>';
	exit;
} else{
	$loga = log($anzahl, 2) ;
	if($loga != round($loga)){
		echo 'Die Anzahl der Teilnehmer ist ungültig (Muss eine 2er Potenz sein)!<br>';
		exit;
	}
}

$auswahl_kriteria = '1';

if($fusion != 1){
	$auswahl_kriteria .= ' AND fusion = \'nein\' ';
} else if(is_numeric($exclude_fusi_count) && $exclude_fusi_count > 0) {
	$subselect = 'SELECT ec.char_id from event_chars ec inner join turniere t on ec.event_id = t.event_id WHERE art = \''.$name.'\' GROUP by ec.char_id HAVING count(*) >= '.$exclude_fusi_count;
	$auswahl_kriteria .= ' AND (fusion = \'nein\' OR id NOT IN ( '.$subselect.' ) )';
}

// Es sollen keine NPC mitmachen :) und nicht die Spezial Wanted NPCs

$exclude_race_ids = array();

if($special != 1){
	$races = getSpecialRaceIds();
	$npc_race = getRaceTypeName('NPC'); // do not remove npc (there is a separate setting for this)
	
	foreach($races as $race) {
		if($race['type'] != $npc_race['id']) {
			$exclude_race_ids[] = '\''.$race['id'].'\'';
		}
	}
}

if($npc != 1) {
	$npc_race = getRaceTypeName('NPC'); // do not remove npc (there is a separate setting for this)
	$races = getRacesByType($npc_race); // remove all NPC
	foreach($races as $race) {
		$exclude_race_ids[] = '\''.$race['id'].'\'';
	}
}

if(count($exclude_race_ids)) {
	$auswahl_kriteria .= ' AND rasse NOT IN('.implode(',',$exclude_race_ids).') ';
}


if(is_numeric($minlevel)){
	$auswahl_kriteria .= ' AND level >= '.$minlevel.' ';
}

if(is_numeric($maxlevel)){
	$auswahl_kriteria .= ' AND level <= '.$maxlevel.' ';
}

if ($exclude_winners == 1) {
	$auswahl_kriteria .= ' AND id NOT IN( SELECT charid FROM highscore WHERE art = "'.$name.'") ';
}


// Erstma checken ob es überhaupt genug chars gibt ;)

$sql = 'SELECT count(*) as anzahl from chars where '.$auswahl_kriteria;
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
if($gain == 'Anmeldung'){
	$sql = 'SELECT charakter as id FROM turnier1 t WHERE art = \''.$name.'\'';
	$qry = db_query($sql);
	while($row = mysqli_fetch_assoc($qry)){
		$cid[] = $row['id'];
	}
	$sql = 'DELETE FROM turnier1 WHERE art = \''.$name.'\'';
//	echo $sql .'<br>';
	db_query($sql); // Lösche die Anmeldungsliste
	if(count($cid) > 0){
		$sql = 'SELECT id FROM chars where '.$auswahl_kriteria.' and id NOT IN('.join(',',$cid).') ORDER BY RAND()';
	} else {
		// Turnier is voll!
		$sql = 'SELECT id FROM chars where '.$auswahl_kriteria.' ORDER BY RAND()';
	}
} else if($gain == 'PL'){
	// Die Buffs werden nicht zur PL gezählt ;)
	$sql = 'SELECT id FROM chars where '.$auswahl_kriteria.' ORDER BY starke+verteidigung+speed+ausdauer+glueck desc';
} else if($gain == 'Level'){
	$sql = 'SELECT id FROM chars where '.$auswahl_kriteria.' ORDER BY level desc';
} else{
	echo 'Parameter gain war weder Anmeldung, PL noch Level<br>';
	exit;
}

// echo $sql.'<br>';
$qry = db_query($sql);
while(($row = mysqli_fetch_assoc($qry)) && count($cid) < $anzahl){
//	echo $row['id'].'<br>';
	$cid[] = $row['id'];
}

//print_r($cid);

$event_id = createEvent(EVENT_TURNIER);
db_query('INSERT into turniere(art, event_id) values("'.$name.'", '.$event_id.')');

$sql = 'SELECT id FROM chars WHERE id IN('.join(', ',$cid).') ORDER BY RAND()'; // Mischen der Teilnehmer
//echo $sql.'<br>';
$qry = db_query($sql);
$char_array = array();
while($row = mysqli_fetch_assoc($qry)){
	$char_data = NULL;
	if($itemless == 1) {
		$char_data = getChar($row['id']); // ohne Equip für Turniere!!
	} else {
		$char_data = getCharWithBuffs($row['id']); // Equip für Turniere!!
	}
	$max_hp = explode(',', $char_data['hp'])[1];
	$char_data['hp'] = join(',', array($max_hp, $max_hp));
	$max_mp = explode(',', $char_data['mp'])[1];
	$char_data['mp'] = join(',', array($max_mp, $max_mp));
	
	addParticipant($event_id, $char_data);

	$char_array[] = $char_data;
}
// So alle chars sind jetzt in den Arrays
$round = 1;

//print_r($chars);
//echo '<br>';

// Hier Gruppenphase berechnen und die Chars für die Endrunde in $chars speichern
$start_time = time();
$counter = 1;
$duration = TOURNAMENT_FIGHT_DURATION * 60;

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
	if($randomize == 1) {
		shuffle($n_chars);
	}
	$char_array = $n_chars;
}

$end_time = $start_time + $counter * $duration;
$end_time_str = date("Y-m-d H:i:s",$end_time);
db_query('UPDATE event_chars SET block_begin = \''.$end_time_str.'\', block_end = \''.$end_time_str.'\' WHERE event_id = '.$event_id);

// Seems that we have a winner :D
$fruit_item_str = getFruitItem($fruit_type, $fruit_chance);
if ($fruit_item_str !== NULL) {
	// add item in event_char_metadata
	db_query('INSERT INTO event_char_metadata(event_id, event_char_id, `key`, `value`) values('.$event_id.','.$char_array[0]['id'].',\''.KEY_ITM_CHAR1.'\',\''.$fruit_item_str.'\')');
}

