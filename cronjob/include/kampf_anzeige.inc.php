<?php
/*
 * Created on 12.07.2008
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH.'/include/attacke.inc.php');
include_once(ROOT_PATH.'/include/random.inc.php');


function displayCharTable($charbild, $charname, $starke, $ver, $speed, $ausdauer, $glueck = -1, $hp_akt, $hp_max, $mp_akt, $mp_max ){
?>
<table class="stylish">
	<tr>
		<td><img src="<?php echo $charbild; ?>" width="75" height="75"></td>
		<td class="stats"><span class="stats">Name:</span> <span
			class="statvals"><?php echo $charname; ?></span> <br> <span
			class="stats">HP:</span> <span class="statvals"><?php echo $hp_akt.' / '.$hp_max; ?></span>
			<br> <span class="stats">MP:</span> <span class="statvals"><?php echo $mp_akt.' / '.$mp_max; ?></span>
			<br> <span class="stats">St&auml;rke:</span> <span class="statvals"><?php echo round($starke); ?></span>
			<br> <span class="stats">Verteidigung:</span> <span class="statvals"><?php echo round($ver); ?></span>
			<br> <span class="stats">Geschwindigkeit:</span> <span
			class="statvals"><?php echo round($speed); ?></span> <br> <span
			class="stats">Gl&uuml;ck</span> <span class="statvals"><?php echo round($glueck); ?></span>
			<br> <span class="stats">Ausdauer:</span> <span class="statvals"><?php echo round($ausdauer); ?></span>
		</td>
	</tr>
</table>
<?php
}

function displayAttacks($attacke1, $schaden1, $attacke2, $schaden2){
?>
<table border="0" width="100%">
	<tr>
		<td width="50%"><span class="stats">Technik:</span> <span
			class="statvals"><?php
					if(is_numeric($attacke1) || $attacke1 === NULL) {
						$atk = getAttack($attacke1);
						echo $atk['name'];
					} else {
						echo $attacke1;
					}
					?></span> <br>
		<br> <span class="stats">Schaden:</span> <span class="statvals"><?php echo $schaden1; ?></span>
		</td>
		<td width="50%"><span class="stats">Technik:</span> <span
			class="statvals"><?php
					if(is_numeric($attacke2)|| $attacke2 === NULL) {
						$atk = getAttack($attacke2);
						echo $atk['name'];
					} else {
						echo $attacke2;
					}
				?></span> <br>
		<br> <span class="stats">Schaden:</span> <span class="statvals"><?php echo $schaden2; ?></span>
		</td>
	</tr>
</table>
<?php
}


function displayFightNotAvailable() {
echo '<tr><td colspan="3"><table class="stylish" width="50%""><tr><td><table width="100%">';


echo '<tr>' .
	'<th class="greenstats" colspan="4">Der Fight existiert nicht oder hat noch nicht angefangen!</th>'.
	'</tr>';

echo '</table></td></tr>';
echo '</table>';
echo '</td></tr>';

}

function displayFightIsOngoing($char1_name, $char2_name) {
echo '<tr><td colspan="3"><table class="stylish" width="50%""><tr><td><table width="100%">';


echo '<tr>' .
	'<th class="greenstats" colspan="4">Der Fight läuft noch!</th>'.
	'</tr>';


echo '<tr>' .
	'<td class="greenstats">'.$char1_status.'</td>'.
	'<td class="bold">???</td>'.
	'<td class="greenstats">'.$char2_status.'</td>'.
	'<td class="bold">???</td>' .
	'</tr>';


echo '</table></td></tr>';
echo '</table>';
echo '</td></tr>';

}

function displaySummary($char1_name, $char2_name, $sieger, $exp_char1, $exp_char2, $money_char1, $money_char2, $arena_exp1 = NULL, $arena_exp2 = NULL, $loge = NULL, $sitz = NULL, $steh = NULL){

echo '<tr><td colspan="3"><table class="stylish" width="50%""><tr><td><table width="100%">';

if($sieger == $char1_name){
	$char1_status = 'Sieger';
	$char2_status = 'Verlierer';
} else{
	$char1_status = 'Verlierer';
	$char2_status = 'Sieger';
}
echo '<tr>' .
	'<td class="greenstats">'.$char1_status.'</td>'.
	'<td class="bold">'.$char1_name.'</td>'.
	'<td class="greenstats">'.$char2_status.'</td>'.
	'<td class="bold">'.$char2_name.'</td>' .
	'</tr>';

	if($exp_char1 !== NULL || $exp_char2 !== NULL) {

echo '<tr>' .
	'<td class="greenstats">Exp +</td>' .
	'<td class="bold">'.$exp_char1.'</td>' .
	'<td class="greenstats">Exp +</td>' .
	'<td class="bold">'.$exp_char2.'</td>'.
	'</tr>';

	}

	if($money_char1 !== NULL || $money_char2 !== NULL) {

echo '<tr>'.
	'<td class="greenstats">Geld +</td>'.
	'<td class="bold">'.$money_char1.'</td>'.
	'<td class="greenstats">Geld +</td>'.
	'<td class="bold">'.$money_char2.'</td>' .
	'</tr>';
	}
	if($arena_exp1 !== NULL || $arena_exp2 !== NULL) {
		echo '<tr>'.
			'<td class="greenstats">Arena Exp +</td>'.
			'<td class="bold">'.$arena_exp1.'</td>'.
			'<td class="greenstats">Arena Exp +</td>'.
			'<td class="bold">'.$arena_exp2.'</td>' .
			'</tr>';
	}

	echo '</table></td></tr>';
echo '</table>';

	if($loge !== NULL || $steh !== NULL || $sitz !== NULL) {
		?>
<table class="stylish" width="50%"">
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td class="greenstats">Zuschauer</td>
					<td class="bold"><?php echo $loge+$steh+$sitz; ?></td>
				</tr>
				<tr>
					<td class="greenstats">Stehpl&auml;tze</td>
					<td class="bold"><?php echo $steh; ?></td>
				</tr>
				<tr>
					<td class="greenstats">Sitzpl&auml;tze</td>
					<td class="bold"><?php echo $sitz; ?></td>
				</tr>
				<tr>
					<td class="greenstats">Logenpl&auml;tze</td>
					<td class="bold"><?php echo $loge; ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php
		 echo '</td></tr>';
	}


}

function displayFinalResult($items, $exp, $money, $final_result){
echo '<tr><td colspan="3"><table class="stylish" width="50%"><tr><td>' .
		'<table width="100%">';
echo '<tr><th class="greenstats" colspan="2">Zusammenfassung</th></tr>';
if($final_result == 'lost') {
	echo '<tr><td class="greenstats" colspan="2">Ihr m&uuml;sst euch selbst eingestehen, dass diese Monster zu stark waren. Der Zweifel der euch plagt verringert eure Erfahrung und Diebe den Betrag in eurem Geldbeutel.</td></tr>';
}

echo '<tr>' .
		'<td class="greenstats" align="center">Exp +</td>' .
		'<td class="bold">'.$exp.'</td>'.
	'</tr>';
echo '<tr>' .
		'<td class="greenstats" align="center">Geld +</td>' .
		'<td class="bold">'.$money.'</td>'.
	'</tr>';
echo '<tr>' .
		'<td class="greenstats" align="center">Items</td>' .
		'<td class="bold">';
	// Letze Eintrag von Items ist false!!
	if(!$items[0]){
		echo '&nbsp;';
	} else{
		for($i=0;$items[$i];$i++){
			echo $items[$i]['name'].'<br>';
		}
	}
echo'</td></tr>';
echo '</table>' .
		'</td></tr></table></td></tr>';
}

function displaySchatzSuche($char){
	include_once(ROOT_PATH.'/include/schatz.inc.php');
	// Erstmal die Schatzsuche-stammdaten laden!
	$sql = 'Select * from quests where charid ='.$char['id'].' and dauer < now()';
	$quest = mysqli_fetch_assoc(db_query($sql));
//	echo $sql.'<br>';
	if(!$quest){
		return 'Schatzsuche noch nicht beendet!!';
	}

	// Uberschreibe die Char-Daten mit den Daten aus der Datenbank
	$char['starke'] = $quest['st'];
	$char['verteidigung'] = $quest['ver'];
	$char['speed'] = $quest['sp'];
	$char['glueck'] = $quest['gl'];
	$char['ausdauer'] = $quest['aus'];
	$char['hp_max'] = $quest['hp'];
	$char['mp_max'] = $quest['mp'];
	
	$std = $quest['std'];

	// Erstmal die Seite starten
	echo '<html><head><link rel="stylesheet" type="text/css" href="design/FightStyle.css"></head><body><table width="100%">';
	// Einfach mal ne dicke Ueberschrift
	$ort = mysqli_fetch_assoc(db_query('select * from quest_ort where id = '.$quest['ortid']));
	echo '<tr><th class="top" colspan="3">Schatzsuche in '.$ort['stadt'].'</th></tr>';

	// Hole die Ergebnisse die auch in die Datenbank eingetragen werden muessen!
	$row = mysqli_fetch_assoc(db_query('Select sum(exp) as exp, sum(geld) as geld, count(*) as anzahl from quest_fights where charid = '.$char['id']));

	// Ermittle den Endzustand des Chars
	$end_qf = mysqli_fetch_assoc(db_query('select max(fightnr) as mf from quest_fights where charid = '.$char['id']));
	$end_qr = mysqli_fetch_assoc(db_query('select max(roundnr) as mr from quest_rounds where fightnr = '.$end_qf['mf'].' and charid = '.$char['id']));
	$last_round = mysqli_fetch_assoc(db_query('Select * from quest_rounds where roundnr = '.$end_qr['mr'].' and fightnr = '.$end_qf['mf'].' and charid = '.$char['id']));
	$last_fight = mysqli_fetch_assoc(db_query('Select * from quest_fights where fightnr = '.$end_qf['mf'].' and charid = '.$char['id']));

	// Ermittle die Reqs des Chars!
	$sql = 'SELECT c_attack as a_name, count(*) as anzahl FROM quest_rounds q where roundnr > 0 and charid = '.$char['id'].' group by c_attack';
//	echo $sql.'<br>';
	$reqs = db_query($sql);

	while($row_r = mysqli_fetch_assoc($reqs)){
		$c_attacken[] = $row_r;
//		print_r($c_attacken);
//		echo '<br>';
	}

	if($last_fight['sieger'] == $char['id']){
		$final_result = 'won';
		// Nur wer gewinnt bekommt was!
		$items = getSchatzItems($row['anzahl']);
		
		for($i=0;$items[$i];$i++){
			$res = db_query('Update quest_item_stats set anzahl = anzahl + 1 WHERE monster = '.$row['anzahl'].' and id = '.$items[$i]['id']);
			if(db_affected_rows() == 0){
				// entry not yet present				
				db_query('INSERT INTO quest_item_stats (monster, id, anzahl) values('.$row['anzahl'].', '.$items[$i]['id'].', 1)');
			}
		}
	} else{
		$final_result = 'lost';
		$lose_rate = mt_random_wrapper(25,60)/100;
		$row['exp'] = round($row['exp'] * $lose_rate);
		$row['geld'] = round ( $row ['geld'] * $lose_rate );
		for($i = 0;$i< count($row_r); $i++){
			$c_attacken[$i]['anzahl'] = floor($c_attacken[$i]['anzahl'] * $lose_rate);
		}
	}

	$res = db_query('Update quest_stats set anzahl = anzahl + 1 WHERE zeit = '.$std.' AND monster = '.$row['anzahl'].' AND status = '.($final_result=='won'?1:0));
	if(db_affected_rows() == 0){
		db_query('INSERT INTO quest_stats (monster, zeit, anzahl, status) values('.$row['anzahl'].', '.$std.', 1, '.($final_result=='won'?1:0).')');
	}

	// Zeige als erstes die Zusammenfassung an!
	displayFinalResult($items, $row['exp'], $row['geld'], $final_result);


	// Lade nun die Fights runter
	$sql = 'Select * from quest_fights where charid = '.$char['id'].' ORDER BY fightnr ASC';
	$qry = db_query($sql);
	while($fight = mysqli_fetch_assoc($qry)){
		displaySchatzSucheFight($quest, $char, $fight);
	}

	submitCharChanges($char['id'], $items, $row['exp'], $row['geld'], $quest['ortid'], array($last_round['c_hp']) , array($last_round['c_mp']), $c_attacken);
	echo '</table></body></html>';
	return null;
}

function displaySchatzSucheFight($quest, $char, $fight){
	// Lade das Monster aus der Datenbank
	$sql = 'select * from quest_monster where id = '.$fight['monsterid'];
	$monster = mysqli_fetch_assoc(db_query($sql));

	if($monster['bild'] == null || $monster['bild'] == ''){
		$monster['bild'] = 'design/bilder/avatare/noavart.gif';
	}

	// Platziere den Table-Header
	echo '<tr><th class="top" colspan="3">'.$char['name'].' Vs '.$monster['name'].'</th></tr>';
	$sql = 'Select * from quest_rounds where charid = '.$char['id']. ' and fightnr = '.$fight['fightnr'];
//	echo $sql.'<br>';
	$qry = db_query($sql);
	while($round = mysqli_fetch_assoc($qry)){
		displaySchatzSucheFightRound($char, $monster, $fight, $round);
	}
	displaySummary($char['name'], $monster['name'], $fight['sieger']==$char['id']?$char['name']:$monster['name'], $fight['exp'], 0, $fight['geld'], 0);
	// Und dann halt immer noch etwas platz nach unten lassen

	echo '<tr height="5px"><td>&nbsp;</td></tr>';
}

function displaySchatzSucheFightRound($char, $monster, $fight, $round){
	// Rundennummer:
	if($round['roundnr'] == 0){
		$runde = 'Start:';
	} else{
		$runde = 'Runde: '.$round['roundnr'];
	}
	echo '<tr><th class="round" colspan="3">'.$runde.'</th></tr>';

	// Bild + Avantarinfo | Attacken + Schaden | Bild + Avantarinfo
	// In einer Reihe allerdings
	echo '<tr><td>';
	displayCharTable($char['bild'], $char['name'], $round['c_st'], $round['c_ver'], $round['c_sp'], $round['c_aus'], $round['c_gl'], $round['c_hp'], $char['hp_max'], $round['c_mp'], $char['mp_max'] );
	echo '</td><td>';
	if($round['roundnr'] != 0){
		displayAttacks($round['c_attack'], $round['c_dmg'], $round['m_attack'], $round['m_dmg']);
	} else{
		echo '&nbsp;';
	}
	echo '</td><td>';
	displayCharTable($monster['bild'], $monster['name'], $round['m_st'], $round['m_ver'], $round['m_sp'], $round['m_aus'], $round['m_gl'], $round['m_hp'], $monster['hp'], $round['m_mp'], $monster['mp'] );
	echo '</td></tr>';
}


function displayEventFight($event_id, $event_fight_id) {
	include_once(ROOT_PATH.'/include/event.inc.php'); // for the defines :)

	// Erstmal die Seite starten
	echo '<table width="100%">';
	// :D
	// first readout the required event data
	$sql = 'SELECT *, visible <= now() as passed FROM event_fights WHERE event_id = ' . $event_id . ' AND event_fight_id = ' . $event_fight_id . ' AND `starting` <= now()';
	//echo $sql . '<br>';
	$event_fight_data = mysqli_fetch_assoc(db_query($sql));
	if(!$event_fight_data){
		displayFightNotAvailable();
		echo '</table></body></html>';
		return;
	}
	// Okay and now get the data of the chars ;)
	$sql = 'SELECT * FROM event_chars e where event_id = '.$event_id.' and event_char_id IN (SELECT event_char_id from event_fight_rounds where event_id = '.$event_id.' and event_fight_id = '.$event_fight_id.' and `round` = 0)';
//	echo $sql . '<br>';
	$qry = db_query($sql);
	while($row = mysqli_fetch_assoc($qry)) {
		if($row['char_bild'] == null || $row['char_bild'] == ''){
			$row['char_bild'] = 'design/bilder/avatare/noavart.gif';
		}
		if($event_fight_data['host'] == $row['event_char_id']) {
			$char1 = $row;

		} else {
			$char2 = $row;
		}
	}

		// Platziere den Table-Header
	echo '<tr><th class="top" colspan="3">'.$char1['char_name'].' Vs '.$char2['char_name'].'</th></tr>';
	// now get the round data :D
	$sql = 'SELECT * from event_fight_rounds WHERE event_id = ' .$event_id . ' AND event_fight_id = ' .$event_fight_id . ' and visible <= now()';
	$qry = db_query($sql);
	
	$char1_rounds = array();
	$char2_rounds = array();
	
	while($row = mysqli_fetch_assoc($qry)) {
		if($event_fight_data['host'] == $row['event_char_id']) {
			$char1_rounds[$row['round']] = $row;
		} else {
			$char2_rounds[$row['round']] = $row;
		}
	}

	for($i=0;$i<count($char1_rounds);$i++) {
		displayEventFightRound($char1, $char2, $char1_rounds[$i], $char2_rounds[$i], $i);
	}

	// readout the meta-data of this fight :D
	$sql = 'SELECT * FROM event_fight_metadata WHERE event_id = ' .$event_id . ' AND event_fight_id = ' .$event_fight_id;
	$qry = db_query($sql);
	$event_fight_metadata = array();
	while($row = mysqli_fetch_assoc($qry)) {
		$event_fight_metadata[$row['key']] = $row['value'];
	}

	if($event_fight_data['passed'] == 1) {
		displaySummary($char1['char_name'], $char2['char_name'], $event_fight_data['winner']==$char1['event_char_id']?$char1['char_name']:$char2['char_name'], $event_fight_metadata[KEY_EXP_CHAR1], $event_fight_metadata[KEY_EXP_CHAR2], $event_fight_metadata[KEY_GELD_CHAR1], $event_fight_metadata[KEY_GELD_CHAR2], $event_fight_metadata[KEY_AXP_CHAR1], $event_fight_metadata[KEY_AXP_CHAR2], $event_fight_metadata[KEY_LOGE], $event_fight_metadata[KEY_SITZ], $event_fight_metadata[KEY_STEH]);
	} else {
		displayFightIsOngoing($char1['char_name'], $char2['char_name']);
	}

	// Und dann halt immer noch etwas platz nach unten lassen

	echo '<tr height="5px"><td>&nbsp;</td></tr>';

	echo '</table>';
	return null;
}

function displayEventFightRound(array $char1_data, array $char2_data, array $char1_round_data, array $char2_round_data, $roundnr) {
	// xD
	if($roundnr == 0){
		$runde = 'Start:';
	} else{
		$runde = 'Runde: '.$roundnr;
	}
	echo '<tr><th class="round" colspan="3">'.$runde.'</th></tr>';

	$c1_str = $char1_round_data['strength'];
	$c1_def = $char1_round_data['defense'];
	$c1_spd = $char1_round_data['speed'];
	$c1_stm = $char1_round_data['stamina'];
	$c1_lck = $char1_round_data['luck'];
	$c1_hp = $char1_round_data['hp'];
	$c1_hpMax = $char1_data['hp'];
	$c1_mp = $char1_round_data['mp'];
	$c1_mpMax = $char1_data['mp'];

	$c2_str = $char2_round_data['strength'];
	$c2_def = $char2_round_data['defense'];
	$c2_spd = $char2_round_data['speed'];
	$c2_stm = $char2_round_data['stamina'];
	$c2_lck = $char2_round_data['luck'];
	$c2_hp = $char2_round_data['hp'];
	$c2_hpMax = $char2_data['hp'];
	$c2_mp = $char2_round_data['mp'];
	$c2_mpMax = $char2_data['mp'];

	// Bild + Avantarinfo | Attacken + Schaden | Bild + Avantarinfo
	// In einer Reihe allerdings
	echo '<tr><td>';
	displayCharTable($char1_data['char_bild'], $char1_data['char_name'], $c1_str, $c1_def, $c1_spd, $c1_stm, $c1_lck, $c1_hp, $c1_hpMax, $c1_mp, $c1_mpMax );
	echo '</td><td>';
	if($roundnr != 0){
		displayAttacks($char1_round_data['attack'], $char1_round_data['damage'], $char2_round_data['attack'], $char2_round_data['damage']);
	} else{
		echo '&nbsp;';
	}
	echo '</td><td>';

	displayCharTable($char2_data['char_bild'], $char2_data['char_name'], $c2_str, $c2_def, $c2_spd, $c2_stm, $c2_lck, $c2_hp, $c2_hpMax, $c2_mp, $c2_mpMax );
	echo '</td></tr>';

}
?>
