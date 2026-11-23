<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once('path.inc.php'); // get the path ;)
include_once (ROOT_PATH . '/include/designfunctions.inc.php');
include_once (ROOT_PATH . '/include/fehlerausgabe.inc.php');
include_once (ROOT_PATH . '/include/arena.inc.php');
include_once (ROOT_PATH . '/include/char.inc.php');
include_once(ROOT_PATH.'/include/random.inc.php');

$k_exp[0] = "";
$X23 = "";
$X13 = "";

if (!$chara_1['id'] OR !$chara_2['id']) {
	echo "Unbekannter B.U.G";
	exit;
}
################## Kaempfer 1
$char1_quis_item = explode(",", $chara_1['kampf_item']);
$char1_item_helm = mysqli_fetch_array(db_query("SELECT i.hp, i.mp, i.starke, i.verteidigung, i.speed FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE w.id='$char1_quis_item[0]' LIMIT 1"));
$char1_item_rustung = mysqli_fetch_array(db_query("SELECT i.hp, i.mp, i.starke, i.verteidigung, i.speed FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE w.id='$char1_quis_item[1]' LIMIT 1"));
$char1_item_schild = mysqli_fetch_array(db_query("SELECT i.hp, i.mp, i.starke, i.verteidigung, i.speed FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE w.id='$char1_quis_item[2]' LIMIT 1"));
$char1_item_schwert = mysqli_fetch_array(db_query("SELECT i.hp, i.mp, i.starke, i.verteidigung, i.speed FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE w.id='$char1_quis_item[3]' LIMIT 1"));
$char1_item_schuhe = mysqli_fetch_array(db_query("SELECT i.hp, i.mp, i.starke, i.verteidigung, i.speed FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE w.id='$char1_quis_item[4]' LIMIT 1"));

$k_hp1 = explode(",", $chara_1['hp']);
$k_mp1 = explode(",", $chara_1['mp']);

$k_starke[0] = $chara_1['starke'] + $char1_item_helm['starke'] + $char1_item_rustung['starke'] + $char1_item_schild['starke'] + $char1_item_schwert['starke'] + $char1_item_schuhe['starke'];
$k_speed[0] = $chara_1['speed'] + $char1_item_helm['speed'] + $char1_item_rustung['speed'] + $char1_item_schild['speed'] + $char1_item_schwert['speed'] + $char1_item_schuhe['speed'];
$k_ver[0] = $chara_1['verteidigung'] + $char1_item_helm['verteidigung'] + $char1_item_rustung['verteidigung'] + $char1_item_schild['verteidigung'] + $char1_item_schwert['verteidigung'] + $char1_item_schuhe['verteidigung'];
$k_hp1[0] += $char1_item_helm['hp'] + $char1_item_rustung['hp'] + $char1_item_schild['hp'] + $char1_item_schwert['hp'] + $char1_item_schuhe['hp'];
$k_hp1[1] += $char1_item_helm['hp'] + $char1_item_rustung['hp'] + $char1_item_schild['hp'] + $char1_item_schwert['hp'] + $char1_item_schuhe['hp'];
$k_mp1[0] += $char1_item_helm['mp'] + $char1_item_rustung['mp'] + $char1_item_schild['mp'] + $char1_item_schwert['mp'] + $char1_item_schuhe['mp'];
$k_mp1[1] += $char1_item_helm['mp'] + $char1_item_rustung['mp'] + $char1_item_schild['mp'] + $char1_item_schwert['mp'] + $char1_item_schuhe['mp'];

$k_ausdauer[0] = $chara_1['ausdauer'];
$k_glueck[0] = $chara_1['glueck'];

$k_attacke1 = explode(",", $chara_1['attacken']);
$k_select1_1 = explode(",", $chara_1['auswahl1']);
$k_select1_2 = explode(",", $chara_1['auswahl2']);
$k_select1_3 = explode(",", $chara_1['auswahl3']);
$k_attacke1_2 = explode(",", $chara_1['attacken2']);

$k_hp[0] = round($k_hp1[0]);
$k_mp[0] = round($k_mp1[0]);

$k_aufgabe_a_1 = $k_hp1[1] / 100;
$k_aufgabe[0] = $k_aufgabe_a_1 * $chara_1['aufgeben'];

################## Kaempfer 2
$char2_quis_item = explode(",", $chara_2['kampf_item']);
$char2_item_helm = mysqli_fetch_array(db_query("SELECT i.hp, i.mp, i.starke, i.verteidigung, i.speed FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE w.id='$char2_quis_item[0]' LIMIT 1"));
$char2_item_rustung = mysqli_fetch_array(db_query("SELECT i.hp, i.mp, i.starke, i.verteidigung, i.speed FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE w.id='$char2_quis_item[1]' LIMIT 1"));
$char2_item_schild = mysqli_fetch_array(db_query("SELECT i.hp, i.mp, i.starke, i.verteidigung, i.speed FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE w.id='$char2_quis_item[2]' LIMIT 1"));
$char2_item_schwert = mysqli_fetch_array(db_query("SELECT i.hp, i.mp, i.starke, i.verteidigung, i.speed FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE w.id='$char2_quis_item[3]' LIMIT 1"));
$char2_item_schuhe = mysqli_fetch_array(db_query("SELECT i.hp, i.mp, i.starke, i.verteidigung, i.speed FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE w.id='$char2_quis_item[4]' LIMIT 1"));

$k_hp2 = explode(",", $chara_2['hp']);
$k_mp2 = explode(",", $chara_2['mp']);

$k_starke[1] = $chara_2['starke'] + $char2_item_helm['starke'] + $char2_item_rustung['starke'] + $char2_item_schild['starke'] + $char2_item_schwert['starke'] + $char2_item_schuhe['starke'];
$k_speed[1] = $chara_2['speed'] + $char2_item_helm['speed'] + $char2_item_rustung['speed'] + $char2_item_schild['speed'] + $char2_item_schwert['speed'] + $char2_item_schuhe['speed'];
$k_ver[1] = $chara_2['verteidigung'] + $char2_item_helm['verteidigung'] + $char2_item_rustung['verteidigung'] + $char2_item_schild['verteidigung'] + $char2_item_schwert['verteidigung'] + $char2_item_schuhe['verteidigung'];
$k_hp2[0] += $char2_item_helm['hp'] + $char2_item_rustung['hp'] + $char2_item_schild['hp'] + $char2_item_schwert['hp'] + $char2_item_schuhe['hp'];
$k_hp2[1] += $char2_item_helm['hp'] + $char2_item_rustung['hp'] + $char2_item_schild['hp'] + $char2_item_schwert['hp'] + $char2_item_schuhe['hp'];
$k_mp2[0] += $char2_item_helm['mp'] + $char2_item_rustung['mp'] + $char2_item_schild['mp'] + $char2_item_schwert['mp'] + $char2_item_schuhe['mp'];
$k_mp2[1] += $char2_item_helm['mp'] + $char2_item_rustung['mp'] + $char2_item_schild['mp'] + $char2_item_schwert['mp'] + $char2_item_schuhe['mp'];

$k_ausdauer[1] = $chara_2['ausdauer'];
$k_glueck[1] = $chara_2['glueck'];

$k_attacke2 = explode(",", $chara_2['attacken']);
$k_select2_1 = explode(",", $chara_2['auswahl1']);
$k_select2_2 = explode(",", $chara_2['auswahl2']);
$k_select2_3 = explode(",", $chara_2['auswahl3']);
$k_attacke2_2 = explode(",", $chara_2['attacken2']);

$k_hp[1] = round($k_hp2[0]);
$k_mp[1] = round($k_mp2[0]);

$k_aufgabe_a_2 = $k_hp2[1] / 100;
$k_aufgabe[1] = $k_aufgabe_a_2 * $chara_2['aufgeben'];

$runden_type1 = 0;
$runden_type2 = 0;
$x = 0;

$runden_summon1 = "";
$runden_summon2 = "";
$runden_gif1 = 0;
$runden_gif_technik1 = "";
$runden_gif2 = 0;
$runden_gif_technik2 = "";

if ($k_hp[0] > $k_aufgabe[0] AND $k_hp[1] > $k_aufgabe[1] AND $x < 10) {
	$sql = "INSERT kampf SET hp1='$k_hp[0]', hp2='$k_hp[1]', mp1='$k_mp[0]', mp2='$k_mp[1]', schaden1='$schaden_1', schaden2='$schaden_2', attacke1='$technick1[name]', attacke2='$technick2[name]', char1='$chara_1[id]', char2='$chara_2[id]', starke1='$k_starke[0]', starke2='$k_starke[1]', ver1='$k_ver[0]', ver2='$k_ver[1]', speed1='$k_speed[0]', speed2='$k_speed[1]', ausdauer1='$k_ausdauer[0]', ausdauer2='$k_ausdauer[1]', glueck1='$k_glueck[0]', glueck2='$k_glueck[1]'";
	db_query($sql);
	//	echo $sql . '<br>';
	if (db_affected_rows() == 0) {
		echo '"' . $sql . '" konnte nicht ausgef&uuml;rt werden!<br>';
	}
} else {
	displayErrorMessage(NULL, 'Unbekannter B.U.G!', displayHistoryBackLink());
	exit;
}

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

while ($k_hp[0] > $k_aufgabe[0] AND $k_hp[1] > $k_aufgabe[1] AND $x < 10) {

	$technick1 = mysqli_fetch_array(db_query("SELECT at_id, aktiv FROM lernen WHERE id='$k_attacke1[$x]' LIMIT 10"));
	$prozent_technik1 = (20 * $technick1['aktiv']) - 20;
	$technick1 = mysqli_fetch_array(db_query("SELECT * FROM attacken WHERE id='$technick1[at_id]' LIMIT 10"));

	$technick2 = mysqli_fetch_array(db_query("SELECT at_id, aktiv FROM lernen WHERE id='$k_attacke2[$x]' LIMIT 10"));
	$prozent_technik2 = (20 * $technick2['aktiv']) - 20;
	$technick2 = mysqli_fetch_array(db_query("SELECT * FROM attacken WHERE id='$technick2[at_id]' LIMIT 10"));

	///////////////////Hier laedt er den RELOAD der technik wenn die zusammen setzung nicht stimmen sollte
	include (ROOT_PATH . '/include/kampf/andere_technik.php'); /////////////////// Wenn man ne andere technik sich aussuchen will...

	$technick1['speed'] = $technick1['speed'] + (($technick1['speed'] / 100) * $prozent_technik1);
	$technick2['speed'] = $technick2['speed'] + (($technick2['speed'] / 100) * $prozent_technik2);

	$technick1['verteidigung'] = $technick1['verteidigung'] + (($technick1['verteidigung'] / 100) * $prozent_technik1);
	$technick2['verteidigung'] = $technick2['verteidigung'] + (($technick2['verteidigung'] / 100) * $prozent_technik2);

	$technick1['starke'] = $technick1['starke'] + (($technick1['starke'] / 100) * $prozent_technik1);
	$technick2['starke'] = $technick2['starke'] + (($technick2['starke'] / 100) * $prozent_technik2);

	$speeds_char1 = ($k_speed[0] + $technick1['speed']);
	$speeds_char2 = ($k_speed[1] + $technick2['speed']);

	// Speedbug
	//	$speeds_char1_4 = ($k_speed[0] + $technick1['speed']) / 1.5;
	//	$speeds_char2_4 = ($k_speed[1] + $technick2['speed']) / 1.5;
	//
	//	if ($speeds_char1_4 < 1) {
	//		$speeds_char1_4 = 1;
	//	}
	//	if ($speeds_char2_4 < 1) {
	//		$speeds_char2_4 = 1;
	//	}
	//
	//	$speed_char1 = mt_random_wrapper($speeds_char1_4, $speeds_char1);
	//	$speed_char2 = mt_random_wrapper($speeds_char2_4, $speeds_char2);

	////// MUSS SO BLEIBEN WICHTIG
	$aussetzten_runde = "";
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

	$db_query = mysqli_fetch_array(db_query("SELECT * FROM kampf WHERE (char1='$chara_1[id]' OR char1='$chara_2[id]') AND dauer='0' order by id DESC LIMIT 1"));

	db_query("UPDATE kampf SET hp1='$db_query[hp1],$k_hp7[0]', hp2='$db_query[hp2],$k_hp7[1]', mp1='$db_query[mp1],$k_mp7[0]', mp2='$db_query[mp2],$k_mp7[1]', schaden1='$db_query[schaden1],$schaden_1', schaden2='$db_query[schaden2],$schaden_2', attacke1='$db_query[attacke1],$technick1[name]', attacke2='$db_query[attacke2],$technick2[name]', starke1='$db_query[starke1],$k_starke[0]', starke2='$db_query[starke2],$k_starke[1]', ver1='$db_query[ver1],$k_ver[0]', ver2='$db_query[ver2],$k_ver[1]', speed1='$db_query[speed1],$k_speed[0]', speed2='$db_query[speed2],$k_speed[1]', ausdauer1='$db_query[ausdauer1],$k_ausdauer[0]', ausdauer2='$db_query[ausdauer2],$k_ausdauer[1]', glueck1='$db_query[glueck1],$k_glueck[0]', glueck2='$db_query[glueck2],$k_glueck[1]' WHERE id='$db_query[id]'");

	$x++;
}
###########################EXP VERTEILUNG

if ($k_hp[0] <= 0 AND $k_hp[1] <= 0) {

	if ($k_hp[0] > $k_hp[1]) {
		$k_hp[0] = $k_hp1[1];
	} else {
		$k_hp[1] = $k_hp2[1];
	}
}

$s = 0;
while ($s < 2) {

	if ($s == 1) {
		$s_k_hp1 = (100 / $k_hp1[1]);
		$s_k_hp2 = (100 / $k_hp2[1]);
		if ($k_hp[0] > $k_hp[1]) {
			$X13 = HALLO;
		} else {
			$X23 = HALLO;
		}
	}
	if ($kampf_info['zeit_rec'] == "300") {
		$exp001 = 0.5;

	}
	if ($kampf_info['zeit_rec'] == "1200") {
		$exp001 = 3;

	}
	if ($kampf_info['zeit_rec'] == "3600") {
		$exp001 = 9;

	}
	// Geldfaktor (nach Zeit)
	if ($kampf_info['zeit_rec'] <= 3600) {
		$geld_factor = pow($kampf_info['zeit_rec'], 3 / 4) / pow(1200, 3 / 4);
	} else {
		$geld_factor = 0.01; // Cheat!!
	}

	if ($k_hp[1] < $k_aufgabe[1] OR $X13 == "HALLO") {

		$k_exp[0] = (getPowerLevelWithBuffs($chara_1['id']) + getPowerLevelWithBuffs($chara_2['id'])) * 2.2 * $exp001;

		//		$k_geld[0] = ((($chara_1['starke'] + $chara_1['verteidigung'] + $chara_1['speed'] + $chara_1['ausdauer'] + $chara_1['glueck'] + $chara_2['starke'] + $chara_2['verteidigung'] + $chara_2['speed'] + $chara_2['ausdauer'] + $chara_2['glueck']) / 2)) * $exp001;

		$k_exp[1] = (getPowerLevelWithBuffs($chara_1['id']) + getPowerLevelWithBuffs($chara_2['id'])) * $exp001;

		//		$k_geld[1] = ((((($chara_1['starke'] + $chara_1['verteidigung'] + $chara_1['speed'] + $chara_1['ausdauer'] + $chara_1['glueck'] + $chara_2['starke'] + $chara_2['verteidigung'] + $chara_2['speed'] + $chara_2['ausdauer'] + $chara_2['glueck']) / 2)) / 2)) * $exp001;

		$sieger = $chara_1['name'];
		$verlierer = $chara_2['name'];

		$nw_preis = $chara_2['preis'];
	}

	if ($k_hp[0] < $k_aufgabe[0] OR $X23 == "HALLO") {

		$sieger = $chara_2['name'];
		$verlierer = $chara_1['name'];

		$nw_preis = $chara_1['preis'];

		$k_exp[1] = (getPowerLevelWithBuffs($chara_1['id']) + getPowerLevelWithBuffs($chara_2['id'])) * 2.2 * $exp001;

		//		$k_geld[1] = ((($chara_1['starke'] + $chara_1['verteidigung'] + $chara_1['speed'] + $chara_1['ausdauer'] + $chara_1['glueck'] + $chara_2['starke'] + $chara_2['verteidigung'] + $chara_2['speed'] + $chara_2['ausdauer'] + $chara_2['glueck']) / 2)) * $exp001;

		$k_exp[0] = (getPowerLevelWithBuffs($chara_1['id']) + getPowerLevelWithBuffs($chara_2['id'])) * $exp001;

		//		$k_geld[0] = ((((($chara_1['starke'] + $chara_1['verteidigung'] + $chara_1['speed'] + $chara_1['ausdauer'] + $chara_1['glueck'] + $chara_2['starke'] + $chara_2['verteidigung'] + $chara_2['speed'] + $chara_2['ausdauer'] + $chara_2['glueck']) / 2)) / 2)) * $exp001;

	}

	if ($k_exp[0]) {
		$s = 2;
	} else {
		$s++;
	}

}

$arena_data = calculateArenaData($chara_2['besitzer'], getChar($chara_1['id']), getChar($chara_2['id']), $geld_factor);

if ($sieger == $chara_1['name']) {
	// Auswaertschar
	$k_geld[0] = round(0.6 * $arena_data['geld']);
	// Heimchar
	$k_geld[1] = $arena_data['geld'] - $k_geld[0];


} else {
	// Heimchar
	$k_geld[1] = round(0.7 * $arena_data['geld']);
	// Auswaertschar
	$k_geld[0] = $arena_data['geld'] - $k_geld[1];
}

// Umsatzsteuer (auf alles, dafür is ja der Freibetrag höher) :)
db_query('Update arena set einnahmen = einnahmen + '.$k_geld[0].' WHERE besitzer = '.$chara_1['besitzer']);
db_query('Update arena set einnahmen = einnahmen + '.$k_geld[1].' WHERE besitzer = '.$chara_2['besitzer']);

/*
 // Das ist der Teil, der an das Heim-Team geht und versteuert werden muss! 10%
 $arena_geld = round($arena_data['geld']*0.1);
 */
$arena_geld = 0;

$dauer_Z = time() + ($kampf_info['zeit_rec']);

$arena_exp_new = round(50 * ($chara_1['level'] + $chara_2['level']) * ($exp001 / 3));

db_query("UPDATE kampf SET preis='$nw_preis', art='$kampf_info[art]', art2='$kampf_info[rasse]', exp1='$k_exp[0]', arena_name = '$arena_data[id]', win='$sieger', lose='$verlierer', exp2='$k_exp[1]', geld1='$k_geld[0]', geld2='$k_geld[1]', loge='$arena[loge]' , sitz='$arena[sitz]', steh='$arena[steh]', db_satz='$chara_1[id]', dauer='$dauer_Z', arena_exp=$arena_exp_new, loge = $arena_data[loge], sitz = $arena_data[sitz], steh = $arena_data[steh] , arena_geld = $arena_geld WHERE id='$db_query[id]'");

$db_query2 = mysqli_fetch_array(db_query("SELECT * FROM kampf WHERE char1='$chara_1[id]' order by id DESC LIMIT 1"));

db_query("UPDATE chars SET status='Kampf' WHERE id='$chara_1[id]'");

/////wenn npcs kaempfen
if ($kampf_info['rasse'] != "NPC") {

	$sql = "INSERT kampf SET preis='$nw_preis', art='$kampf_info[art]', art2='$kampf_info[rasse]', hp1='$db_query2[hp1]', hp2='$db_query2[hp2]', mp1='$db_query2[mp1]', mp2='$db_query2[mp2]', attacke1='$db_query2[attacke1]', attacke2='$db_query2[attacke2]', schaden1='$db_query2[schaden1]', schaden2='$db_query2[schaden2]', char1='$db_query2[char1]', char2='$db_query2[char2]', starke1='$db_query2[starke1]', starke2='$db_query2[starke2]', ver1='$db_query2[ver1]', ver2='$db_query2[ver2]', speed1='$db_query2[speed1]',speed2='$db_query2[speed2]', ausdauer1='$db_query2[ausdauer1]', ausdauer2='$db_query2[ausdauer2]', glueck1='$db_query2[glueck1]', glueck2='$db_query2[glueck2]', exp1='$db_query2[exp1]', exp2='$db_query2[exp2]', geld1='$db_query2[geld1]', geld2='$db_query2[geld2]', loge='$db_query2[loge]', sitz=$db_query2[sitz], steh=$db_query2[steh], win='$db_query2[win]', lose='$db_query2[lose]', dauer='$dauer_Z', db_satz ='$chara_2[id]', arena_name='$db_query2[arena_name]', arena_exp=$arena_exp_new, arena_geld='$db_query2[arena_geld]'";
	db_query($sql);
	//	echo $sql . '<br>';
	if (db_affected_rows() == 0) {
		echo '"' . $sql . '" konnte nicht ausgef&uuml;rt werden!<br>';
	}

	db_query("UPDATE chars SET status='Kampf' WHERE id='$chara_2[id]'");
	db_query("DELETE FROM kampf_list WHERE id='$kampf_info[id]'");

	db_query("INSERT top_kampf SET preis='$nw_preis', art='$kampf_info[art]', art2='$kampf_info[rasse]', hp1='$db_query2[hp1]', hp2='$db_query2[hp2]', mp1='$db_query2[mp1]', mp2='$db_query2[mp2]', attacke1='$db_query2[attacke1]', attacke2='$db_query2[attacke2]', schaden1='$db_query2[schaden1]', schaden2='$db_query2[schaden2]', char1='$db_query2[char1]', char2='$db_query2[char2]', starke1='$db_query2[starke1]', starke2='$db_query2[starke2]', ver1='$db_query2[ver1]', ver2='$db_query2[ver2]', speed1='$db_query2[speed1]',speed2='$db_query2[speed2]', ausdauer1='$db_query2[ausdauer1]', ausdauer2='$db_query2[ausdauer2]', glueck1='$db_query2[glueck1]', glueck2='$db_query2[glueck2]', exp1='$db_query2[exp1]', exp2='$db_query2[exp2]', geld1='$db_query2[geld1]', geld2='$db_query2[geld2]', loge='$db_query2[loge]', sitz=$db_query2[sitz], steh=$db_query2[steh], win='$db_query2[win]', lose='$db_query2[lose]', arena_name='$db_query2[arena_name]', arena_exp='$db_query2[arena_exp]', dauer='$dauer_Z', db_satz ='$chara_2[id]'");
}
?>