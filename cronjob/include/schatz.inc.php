<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once(ROOT_PATH.'/include/config/db.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/exp.inc.php');
include_once(ROOT_PATH.'/include/sqlwrapper.inc.php');
include_once(ROOT_PATH.'/include/random.inc.php');

defineIfNotDefined('ATTACK_SET_SCHATZSUCHE', 2);


function interruptSchatzSuche($charid){
	db_query('Delete from quest_rounds where charid ='.$charid);
	db_query('Delete from quest_fights where charid ='.$charid);
	db_query('Delete from quests where charid ='.$charid);
	db_query('Update chars set status = \'Frei\' where id = '.$charid);
}

function getMonsterCount($ort){
	return mysqli_fetch_assoc(db_query('Select count(*) as arten, sum(anzahl) as anzahl from quest_monster_orte as qma inner join quest_monster as qm on qma.monsterid = qm.id where ortid = '.$ort));
}

// Die Monsterauswahl in eine Funktion gekapselt um es spaeter optimieren zu koennen!
function getMonster($char_level, $city_id, $min, $max){

	$bottom = round($char_level * 0.75); $top = round($char_level * 1.25); $min_l = min($top,$char_level+20);
	//	echo 'Char mit Level '.$char_level.' will auf Schatzsuche lower='.$bottom.', upper='.$top.'<br>';
	// Excel-Formel = RUNDEN(1/WURZEL(PI()*2) * EXP(-1/(($D141-$C141)*2,5) *POTENZ((H$1  -ABRUNDEN(($C141  +MIN($D141;  $E141))/2));2))*250)
	// SQL-Formel     ROUND( 1/SQRT(  PI()*2) * EXP(-1/(($top-$bottom)*2.5)*POW   ((level-   floor(($bottom+MIN($top ,levl+20))/2)),2) *250)
	$rechnung = 'ROUND(1/SQRT(PI()*2) * EXP(-1/(('.$top.'-'.$bottom.')*2.5)*POW((level-floor(('.$bottom.'+'.$min_l.')/2)),2))*250) as guete';
	$sql = 'select *, '.$rechnung.' from quest_monster as qm inner join quest_monster_orte as qmo on qm.id = qmo.monsterid where ortid = '.$city_id.' and level <= 20 + '.$char_level.' order by rand()';
	//	echo $sql.'<br>';
	$qry = db_query($sql); // Lade alle Monster
	//	while($monster[] = mysqli_fetch_assoc($qry)); // Lade alle monster in einen Array
	$gesamtguete = 0;
	while($row = mysqli_fetch_assoc($qry)){
		if($row['guete'] == 0){
			$row['guete'] += 1;
		}
		$gesamtguete+=$row['guete'];
		$monster[] = $row;
	}
	$count_monster = 0;
	for($i=0;$monster[$i];$i++){
		$count_monster+= $monster[$i]['anzahl'];
	}
	$max = min($count_monster,$max);
	if($max == 0){
		return NULL;
	}
	$sight = mt_random_wrapper($min,$max);
	$all_monster = array();
	for($i=0;$i<$sight;$i++){
		$index = mt_random_wrapper(0, $gesamtguete-1);
		for($t=0;$t<count($monster);$t++){
			if($monster[$t] !== NULL){
				$index -= $monster[$t]['guete'];
				if($index < 0){ // Das Monster wird ausgewaehlt
					$all_monster[] = $monster[$t];
					$monster[$t]['anzahl']--;
					if($monster[$t]['anzahl'] == 0){
						//Verringern der Gesamtguete
						$gesamtguete-= $monster[$t]['guete'];
						$monster[$t] = NULL;
					}
					break;
				}
			}
		}
	}
	// Jetzt nur nochmal mischen ^^
	shuffle($all_monster);
	$new_monster_array = array();
	foreach ($all_monster as $monster) {
		$new_monster_array[] = $monster;
	}
	// So jetzt aber!
	return $new_monster_array;
}

function monsterfight_neu($chara_1, $fight_monster, $std, $ort){

	if($chara_1['kampf_item'] != NULL && $chara_1['kampf_item'] != ',,,,'){
		//		echo 'Problematic Entry = '.$chara_1['kampf_item'].'<br>';
		$char1_buffs = mysqli_fetch_assoc(db_query('SELECT sum(i.hp) as hp, sum(i.mp) as mp, sum(i.starke) as starke, sum(i.verteidigung) as verteidigung, sum(i.speed) as speed FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE w.id IN (' . $chara_1['kampf_item'] . ')'));
	}

	// Retrieve the attack set for the treasure hunt!
	$chara_1['attacken'] = implode(',', getAttackSet($chara_1['id'], ATTACK_SET_SCHATZSUCHE ));

	$in_hp_max = explode(',', $chara_1['hp']);
	$in_mp_max = explode(',', $chara_1['mp']);
	$k_hp2[1] = $in_hp_max[1];
	$k_hp[0] = $in_hp_max[0];
	$k_mp[0] = $in_mp_max[0];
	$in_hp_max = $in_hp_max[1] + $char1_buffs['hp'];
	$in_mp_max = $in_mp_max[1] + $char1_buffs['mp'];

	$in_starke = $chara_1['starke'] + $char1_buffs['starke'];
	$in_speed = $chara_1['speed'] + $char1_buffs['speed'];
	$in_ver = $chara_1['verteidigung'] + $char1_buffs['verteidigung'];
	$in_glueck = $chara_1['glueck'];
	$k_attacke1 = explode(',', $chara_1['attacken']);

	$k_aufgabe_a_1 = $in_hp_max / 100;
	$k_aufgabe[0] = $k_aufgabe_a_1 * $chara_1['aufgeben'];
	// Ausdauer wird im Laufe des Kampfes nicht aufgefrischt!
	$k_ausdauer[0] = $chara_1['ausdauer'];
	$k_hp1[1] = $in_hp_max; // Fuer Heal wichtig!!
	$k_mp1[1] = $in_mp_max; // Fuer Heal wichtig!!
	// Hinzufuegen Quest
	$sql = 'Insert into quests(charid, dauer, cancel, std, ortid, st, sp, ver, gl, aus, hp, mp)' .
			' values('.$chara_1['id'].', ' .
			'TIMESTAMPADD(HOUR, '.$std.', now()), ' .
			'TIMESTAMPADD(MINUTE, 30, now()), ' .
	$std.', '.
	$ort.', '.
	$in_starke.', '.
	$in_speed.', '.
	$in_ver.', '.
	$in_glueck.', '.
	$chara_1['ausdauer'].', '.
	$in_hp_max.', '.
	$in_mp_max.')';
	//	echo 'Debug-Message: '.$sql.'<br>';
	db_query($sql);

	$Dead1 = 0;
	//	while ($chara_2 = mysqli_fetch_assoc($fight_monster)) { // Schleife in monsterfight.inc.php rein!!!
	for($i=0;$i<count($fight_monster) && $Dead1 == 0;$i++){
		$sql = 'Insert into quest_fights(charid, fightnr, monsterid)' .
				' values(' .
		$chara_1['id'].', ' .
		$i.', ' .
		$fight_monster[$i]['id'].')';
		//		echo $sql.'<br>';
		db_query($sql);
		// Werte uebernehmen
		$k_starke[0] = $in_starke;
		$k_speed[0] = $in_speed;
		$k_ver[0] = $in_ver;
		$k_glueck[0] = $in_glueck;
		if($i != 0){ // Die erste Runde keine Heilung!!
			$k_hp[0] = min($in_hp_max*0.2+$k_hp[0],$in_hp_max);
			$k_mp[0] = min($in_mp_max*0.4+$k_mp[0],$in_mp_max);
		}
		$chara_2 = $fight_monster[$i];
		if ($Dead1 != 1) {
			################## Kaempfer 2
			$k_starke[1] = $chara_2['starke'];
			$k_speed[1] = $chara_2['speed'];
			$k_ver[1] = $chara_2['verteidigung'];
			$k_hp[1] = $chara_2['hp'];
			$k_mp[1] = $chara_2['mp'];
			$k_ausdauer[1] = $chara_2['ausdauer'];
			$k_glueck[1] = $chara_2['glueck'];

			$uzze = 0;
			while ($uzze < 10) {
				$aktion_AV1[$uzze] = '';
				$aktion_AV2[$uzze] = '';
				$uzze++;
			}

			$ssj_tech_1 = '';
			$ssj_tech_2 = '';
			$play1_ausweich = '';
			$play2_ausweich = '';
			$k_exp[0] = '';
			$X23 = '';
			$X13 = '';
			$schaden_1 = '';
			$schaden_2 = '';

			$x = 0;
			$sql = 'Insert into quest_rounds(charid, fightnr, roundnr, c_st, c_sp, c_ver, c_hp, c_mp, c_aus, c_gl, m_st, m_sp, m_ver, m_hp, m_mp, m_aus, m_gl) values(' .
			$chara_1['id'].', '.$i . ','.$x.','.
			$k_starke[0].','.$k_speed[0].','.$k_ver[0].','.$k_hp[0].','.$k_mp[0].','.$k_ausdauer[0].','.$k_glueck[0].','.
			$k_starke[1].','.$k_speed[1].','.$k_ver[1].','.$k_hp[1].','.$k_mp[1].','.$k_ausdauer[1].','.$k_glueck[1].
					')';
			//			echo 'Debug Message: '.$sql.'<br>';
			db_query($sql);

			$statChange_Char1 = array();
			$statChange_Char2 = array();
			$tmp_hp = array();
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
			while ($k_hp[0] > $k_aufgabe[0] AND $k_hp[1] > 0 AND $x < 10) {

				$technick1 = mysqli_fetch_assoc(db_query("SELECT at_id, aktiv FROM lernen WHERE id='$k_attacke1[$x]' LIMIT 10"));
				$prozent_technik1 = (20 * $technick1['aktiv']) - 20;
				$technick1 = mysqli_fetch_assoc(db_query("SELECT * FROM attacken WHERE id='$technick1[at_id]' LIMIT 10"));

				include (ROOT_PATH . '/include/kampf/andere_technik.php');

				$technick1['speed'] = $technick1['speed'] + (($technick1['speed'] / 100) * $prozent_technik1);
				$technick1['verteidigung'] = $technick1['verteidigung'] + (($technick1['verteidigung'] / 100) * $prozent_technik1);
				$technick1['starke'] = $technick1['starke'] + (($technick1['starke'] / 100) * $prozent_technik1);

				$speeds_char1 = $k_speed[0] + $technick1['speed'];
				$speeds_char2 = $k_speed[1];

				$aussetzten_runde = ""; ////// MUSS SO BLEIBEN WICHTIG
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
				$x++;
				$sql = 'Insert into quest_rounds(charid, fightnr, roundnr, c_attack, c_dmg, m_attack, m_dmg, c_st, c_sp, c_ver, c_hp, c_mp, c_aus, c_gl, m_st, m_sp, m_ver, m_hp, m_mp, m_aus, m_gl) values(' .
				$chara_1['id'].', '.$i . ','.$x.',\''.$technick1['name'].'\',\''.$schaden_1.'\',\''.$technick2['name'].'\',\''.$schaden_2.'\','.
				$k_starke[0].','.$k_speed[0].','.$k_ver[0].','.$k_hp[0].','.$k_mp[0].','.$k_ausdauer[0].','.$k_glueck[0].','.
				$k_starke[1].','.$k_speed[1].','.$k_ver[1].','.$k_hp[1].','.$k_mp[1].','.$k_ausdauer[1].','.$k_glueck[1].
						')';
				//				echo $sql.'<br>';
				db_query($sql);
			}
			###########################EXP VERTEILUNG

			if ($k_hp[0] > $k_hp[1] && $k_hp[0] > $k_aufgabe[0]) {
				$sieger = $chara_1['id'];
				$verlierer = $chara_2['id'];
				// Hier ist die neue Expberechnung!!
				// Berechnen der MonsterPL (Alle Stats summiert)+HP/10
				//$exp = ($chara_2['starke']+$chara_2['speed']+$chara_2['verteidigung']+$chara_2['glueck']+$chara_2['ausdauer'])+$chara_2['hp']/10;
				//$exp = getPowerLevel($chara_1['id']) + ($chara_2['starke']+$chara_2['speed']+$chara_2['verteidigung']+$chara_2['glueck']+$chara_2['ausdauer']) + ($chara_2['hp']/10);
				$exp = getPowerLevelWithBuffs($chara_1['id']) + getMonsterPowerLevel($chara_2);
				$exp *= 9; // Mal Faktor
				//$exp *= 30; // Mal Faktor
				// Hier ist sie auch schon zu Ende
				//				$geld = ($exp / 15) * 2;
				$geld = 0;
			} else {
				//				echo 'Debug Message: Verloren Hp war '.$k_hp[0].'. Aufgabe war '.$k_aufgabe[0].'. HP des Gegners '.$k_hp[1].'<br>';
				$Dead1 = 1;
				$sieger = $chara_2['id'];
				$verlierer = $chara_1['id'];
				$exp = 0;
				$geld = 0;
			}
			//			With money on SS
			//			db_query('Update quest_fights set exp = '.$exp.', geld = '.$geld.', sieger = '.$sieger.' where charid = '.$chara_1['id'].' and fightnr = '.$i);
			//			No money
			db_query('Update quest_fights set exp = '.$exp.', geld = 0, sieger = '.$sieger.' where charid = '.$chara_1['id'].' and fightnr = '.$i);
		}
	}
	// Just wait (so users can not determinate the success of the SS by calculation time)
	#for(;$i<count($fight_monster);$i++){
	#	usleep(mt_random_wrapper(200000,500000)); // sleep 0.2-0.5 sec/monster
	#}
	
	####
}

// Returns itemrows;
function getSchatzItems($monster_count){
	$divisor = 2/(1+exp(-$monster_count/5+2)); // Je hoeher desto besser
	$items = array();
	$sql = 'SELECT * FROM sp_item where FLOOR(RAND()*wert/'.$divisor.') = 0';
	//	echo $sql.' ('.$monster_count.')<br>';
	$qry = db_query($sql);
	while($items[] = mysqli_fetch_assoc($qry));
	return $items;
}

function submitCharChanges($charid, $items, $exp, $geld, $ort, $hp, $mp, $attacken){
	if($ort == null){
		echo '<tr><td>Dein Char sieht ein blaues Portal und ist Neugierig. Er geht hindurch und findet sich aufeinmal in Gyasta wieder! Was für ein seltsames Ereignis!</td></tr>';
		$ort = 13;
	}
	$char = getChar($charid);
	$tmp = preg_split('#,#',$char['hp']);
	$hp[1] = $tmp[1];
	$tmp = preg_split('#,#',$char['mp']);
	$mp[1] = $tmp[1];
	$hp[0] = max($hp[0], 0);
	$mp[0] = max($mp[0], 0);
	//aktuelle HP/MP sollten die max. HP/MP nicht überschreiten
	if($hp[0] > $hp[1]) {
		$hp[0] = $hp[1];
	}

	if($mp[0] > $mp[1]) {
		$mp[0] = $mp[1];
	}
	// Erstmal aufräumen
	db_query('Delete from quests where charid = '.$charid);
	db_query('Delete from quest_fights where charid = '.$charid);
	db_query('Delete from quest_rounds where charid = '.$charid);

	// Das leveln :)
	// Erfahrung
	addExpToChar($charid, $exp);
	// Attacken
	addAttackUsageToChar($charid, $attacken);

	// Schaden und den neuen Ort eintragen (und den Status nicht vergessen :)
	$sql = 'Update chars set hp=\''.$hp[0].','.$hp[1].'\', mp=\''.$mp[0].','.$mp[1].'\', ort = '.$ort.', status = \'Frei\' where id = '.$charid;
	//	echo $sql.'<br>';
	db_query($sql);


	//	// Kohle gibts ja nichmehr :)
	//	$sql = 'Update user set geld = geld + '.$geld.' where id = '.$char['besitzer'];
	////	echo $sql.'<br>';
	//	db_query($sql);


	// Nun gehts zum auffinden der Items :)
	for($i=0;$items[$i];$i++){
		// Ermittle ob es eine Teufelsfrucht ist!
		$sql = 'Select name, type from sp_item where id = '.$items[$i]['id'];
		$row = mysqli_fetch_assoc(db_query($sql));
		if($row['type'] == 'Teufels Frucht'){
			$sql = 'Select id from wochen_markt where item = \''.$row['name'].'\'';
			$row2 = mysqli_fetch_assoc(db_query($sql));
			$sql = 'INSERT INTO wochen_ware(item, user) values('.$row2['id'].','.$char['besitzer'].')';
			db_query($sql);
		} else{
			$sql = 'Insert into sp_ware(item, user) values('.$items[$i]['id'].','.$char['besitzer'].')';
			//			echo $sql.'<br>';
			db_query($sql);
		}
	}
}

function getMonsterPowerLevel($monster, $buffer_enabled = true){
	if($monster == null){
		return null;
	}
	return $monster['starke']+$monster['verteidigung']+$monster['speed']+$monster['ausdauer']+$monster['glueck']+($monster['hp']/10);
}
?>
