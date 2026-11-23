<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once('path.inc.php'); // get the path ;)
include_once (ROOT_PATH . '/include/config.inc.php');
include_once (ROOT_PATH . '/include/char.inc.php');
include_once (ROOT_PATH . '/include/designfunctions.inc.php');
include_once (ROOT_PATH . '/include/abholcodes.inc.php');
include_once (ROOT_PATH . '/include/fehlerausgabe.inc.php');
include_once (ROOT_PATH . '/include/semaphore.inc.php');
include_once (ROOT_PATH . '/include/parse.inc.php');
include_once (ROOT_PATH . '/include/exp.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$char_id = validateUnsignedInteger($_GET['char_id'], null);
$code = validateString($_GET['code']);

// Unkritisch
$name = $user_ida['nickname'];
$user = $user_ida;

$my_charakter = getChar($char_id);

if (!isUserOwnerOf($user['id'], $char_id)) {
	displayErrorMessage(NULL, 'Das ist nicht dein Charakter!', displayHistoryBackLink());
	exit;
}

$kampf1 = mysqli_fetch_array(db_query("SELECT * FROM kampf WHERE db_satz='$char_id' order by id DESC LIMIT 1"));
$char_1 = getChar($kampf1['char1']);
$char_2 = getChar($kampf1['char2']);
$arena = mysqli_fetch_array(db_query("SELECT * FROM arena WHERE id='$kampf1[arena_name]' LIMIT 1"));

$ressource = 'Kampf:' . $kampf1['id'];
if (!semaphoreUP($ressource)) {
	displayErrorMessage(NULL, 'Der Kampf wird gerade abgeholt!', displayHistoryBackLink());
	exit;
}

if ($kampf1['dauer'] > time()) {
	displayErrorMessage(NULL, 'Der Kampf ist noch nicht vorbei!', displayHistoryBackLink());
	semaphoreDown($ressource);
	exit;
}

if (!checkAbholcode($char_id, $code)) {
	displayErrorMessage(NULL, 'Der Abholcode ist falsch!', displayHistoryBackLink());
	semaphoreDown($ressource);
	exit;
}

db_query('DELETE FROM kampf WHERE db_satz='.$char_id);
if (db_affected_rows() == 0) {
	displayErrorMessage(NULL, 'Der Kampf wurde schon abgeholt!', displayHistoryBackLink());
	semaphoreDown($ressource);
	exit;
}

if ($kampf1['art2'] == 'NPC') { // No Arena-Exp on NPC-Fights
	$kampf1['arena_exp'] = 0;
}


$arenas_exps = explode(',', $arena['exp']);
$starke1 = explode(',', $kampf1['starke1']);
$starke2 = explode(',', $kampf1['starke2']);
$ver1 = explode(',', $kampf1['ver1']);
$ver2 = explode(',', $kampf1['ver2']);
$speed1 = explode(',', $kampf1['speed1']);
$speed2 = explode(',', $kampf1['speed2']);
$ausdauer1 = explode(',', $kampf1['ausdauer1']);
$ausdauer2 = explode(',', $kampf1['ausdauer2']);
$glueck1 = explode(',', $kampf1['glueck1']);
$glueck2 = explode(',', $kampf1['glueck2']);


$schaden1 = explode(',', $kampf1['schaden1']);
$schaden2 = explode(',', $kampf1['schaden2']);
$attacke1 = explode(',', $kampf1['attacke1']);
$attacke2 = explode(',', $kampf1['attacke2']);

$hp1 = explode(',', $kampf1['hp1']);
$hp2 = explode(',', $kampf1['hp2']);
$mp1 = explode(',', $kampf1['mp1']);
$mp2 = explode(',', $kampf1['mp2']);

$a_hp1 = explode(',', $char_1['hp']);
$a_hp2 = explode(',', $char_2['hp']);
$a_mp1 = explode(',', $char_1['mp']);
$a_mp2 = explode(',', $char_2['mp']);
$runde = 1;

$a_mp1[1] = round($a_mp1[1]);
$a_mp2[1] = round($a_mp2[1]);
$a_hp1[1] = round($a_hp1[1]);
$a_hp2[1] = round($a_hp2[1]);

$a_mp1[0] = round($a_mp1[0]);
$a_mp2[0] = round($a_mp2[0]);
$a_hp1[0] = round($a_hp1[0]);
$a_hp2[0] = round($a_hp2[0]);

include_once (ROOT_PATH . '/include/kampf_anzeige.php');

$runde = $runde -1;
if ($kampf1['id']) {
	$char_infos = getChar($char_id);

	$alt_exp = explode(',', $char_infos['exp']);

	if ($kampf1['char1'] == $char_id) {
		$new_exp = $kampf1['exp1'];
		$new_geld = $user['geld'] + $kampf1['geld1'];
		$new_geld_offset = $kampf1['geld1'];
		$attacken = $attacke1;
		$new_hp = $hp1[$runde];
		$new_mp = $mp1[$runde];
		if ($new_hp < 1) {
			$new_hp = 1;
		}
		if ($new_mp < 1) {
			$new_mp = 1;
		}
		if ($new_hp > $a_hp1[1]) {
			$new_hp = $a_hp1[1];
		}
		if ($new_mp > $a_mp1[1]) {
			$new_mp = $a_mp1[1];
		}
		db_query("UPDATE chars SET status='Frei', mp='$new_mp,$a_mp1[1]', hp='$new_hp,$a_hp1[1]' WHERE id='$char_id' LIMIT 1");
	} else {
		$new_exp = $kampf1['exp2'];
		$new_geld = $user['geld'] + $kampf1['geld2'];
		$new_geld_offset = $kampf1['geld2'];
		$attacken = $attacke2;
		$new_hp = $hp2[$runde];
		$new_mp = $mp2[$runde];
		if ($new_hp < 1) {
			$new_hp = 1;
		}
		if ($new_mp < 1) {
			$new_mp = 1;
		}
		if ($new_hp > $a_hp2[1]) {
			$new_hp = $a_hp2[1];
		}
		if ($new_mp > $a_mp2[1]) {
			$new_mp = $a_mp2[1];
		}

		db_query("UPDATE chars SET status='Frei', mp='$new_mp,$a_mp2[1]', hp='$new_hp,$a_hp2[1]' WHERE id='$char_id' LIMIT 1");

	}

	###################################################CHARAKTER WIN

	if ($kampf1['win'] == $my_charakter['name']) {
		$new_siege = $my_charakter['siege'] + 1;

		////////NPC item verteilung
		if ($kampf1['art2'] == "NPC") {
			$item = mysqli_fetch_array(db_query('SELECT item, mal, datensatz, wieviel, feld FROM npc_item WHERE charakter='.$kampf1['char2'].';'));

			$schon_bekommen = mysqli_fetch_assoc(db_query("SELECT Count(*) as anzahl FROM npc_ware WHERE charakter='$kampf1[char2]' AND user='$user_ida[id]'"));

			if ($schon_bekommen['anzahl'] >= $item['mal']) {
				/////item schon bekommen
				//wtf?! :D wasn das für ne if-anweisung :D :D :D
			} else {

				if ($item['datensatz'] == "ware") {
					$satz = "item_id";
					$new_item = "item";
				}
				elseif ($item['datensatz'] == "chars") {
					$punkte_block = 1;
					//muss gemacht werden weil bei hp/mp ein String steht und kein int
					if($item['feld'] == 'hp' || $item['feld'] == 'mp') {
						$chars = getCharsOfUser($user_ida['id']);
						foreach ($chars as $tmp_char) {
							$values = explode(',', $tmp_char[$item['feld']]);
							db_query('UPDATE '.$item['datensatz'].' SET '.$item['feld'].'=\''.$values[0].','.($values[1]+$item['wieviel']).'\' WHERE besitzer='.$user_ida['id'].' AND id='.$tmp_char['id']);	
						}
					} else {
						db_query("UPDATE $item[datensatz] SET $item[feld]=$item[feld]+'$item[wieviel]' WHERE besitzer='$user_ida[id]' LIMIT 10");
					}
					db_query("INSERT npc_ware SET user='$user_ida[id]', charakter='$kampf1[char2]'");
				} else {
					$satz = "item";
					$new_item = "sp_item";
				}

				//////wieviel items er bekommt.
				if ($punkte_block) {

				} else {
					$ut = 0;
					while ($ut < $item['wieviel']) {
						db_query("INSERT $item[datensatz] SET $satz='$item[item]', user='$user_ida[id]'");

						if (!$ut) {
							$itemname = $item['item'];
						} else {
							$itemname .= ',' . $item['item'];
						}
						$ut++;
					}

					db_query("INSERT npc_ware SET user='$user_ida[id]', charakter='$kampf1[char2]'");
				}
			}
		}
		///ENDE

		db_query("UPDATE chars SET siege='$new_siege' WHERE id='$char_id'");
	} else {

		###################################################CHARAKTER LOSE
		$new_niederlagen = $my_charakter['niederlagen'] + 1;

		if ($kampf1[art] == "Liga Kampf") {

			$new_liga_niederlagen = $my_charakter['liga_niederlagen'] + 1;

		} else {
			$new_liga_niederlagen = $my_charakter['liga_niederlagen'];
		}

		if ($kampf1['art2'] == "Todes Kampf") {
			print " <script language='JavaScript'>
					<!--
						alert('$my_charakter[name] Ist in diesen Kampf gestorben und K&auml;mpft bleibt bis 0Uhr in Reich der Toten');
					//-->
				</script>";
			$new_preis = 0;
			db_query("UPDATE chars SET status='Tod' WHERE id='$char_id'");

		} else {

			$new_dead_list = "$my_charakter[dead_list]";

			$new_preis = $my_charakter['preis'];

		}

		db_query("UPDATE chars SET niederlagen='$new_niederlagen', liga_niederlagen='$new_liga_niederlagen', preis='$new_preis', dead_list='$new_dead_list' WHERE id='$char_id'");

	}


	if($user['id'] == $char_2['besitzer']){
		//		$arenas_new = round($arenas_exps[0] + (50* ($char_1['level']+$char_2['level'] )));
		// Leveln der Arena :) (Um Rundungsfehler zu vermeiden diese umstaendliche Rechnung)
		addExpToArena($char_2['besitzer'], $kampf1['arena_exp']-round($kampf1['arena_exp']*0.25));
		//		Umsatz wird schon vorher ermittelt :)
		//		db_query('UPDATE arena SET einnahmen = einnahmen + '.$kampf1['arena_geld'].' WHERE besitzer='.$char_2['besitzer']);
		//		include_once (ROOT_PATH . '/include/arena_exp.php');
	} else{
		addExpToArena($char_1['besitzer'], round($kampf1['arena_exp']*0.25));
	}


	$row = mysqli_fetch_assoc(db_query('Select mietlasten FROM arena WHERE besitzer='.$user['id']));

	if($row['mietlasten'] > 2){
		// Steuerschulden
		$tilgung = ceil($new_geld_offset * 0.75);
		$restgeld = $new_geld_offset - $tilgung;
		db_query('UPDATE user SET geld=geld+'.$restgeld.' WHERE id= '.$user['id']);
		echo '<script type="text/javascript">alert("Da du 3 Tage hintereinander nicht deine Umsatzsteuerverbindlichkeit beglichen hast wird ab jetzt automatisch von jedem Kampf ein Teil zur Tilgung verwendet. Daher fliessen '.$tilgung.' zum Gl&auml;biger");</script>';
		db_query('UPDATE arena SET steuerlasten = steuerlasten - '.$tilgung. ' WHERE besitzer ='.$user['id']);
	} else{
		db_query('UPDATE user SET geld=' . $new_geld . ' WHERE id=' . $user['id'] . ' LIMIT 1');
	}
	//	db_query("DELETE FROM kampf_nachricht WHERE charakter='$char_id'");

	//	include_once (ROOT_PATH . '/include/exp.php');

	// Hinzufuegen der Exp und der Attacken :)
	addExpToChar($char_id, $new_exp); // That was easy :)

	// Nun die Attacken ins richtige Format setzen :)
	// Momentan ist es so (1D Array)
	// Schlag,Schlag,Kick,Schlag,Block,Schlag
	// Gewollt ist ein 2D Array
	// [0] => (['a_name'] => 'Schlag', ['anzahl'] => 4), [1] => (['a_name'] => 'Kick', ['anzahl'] => 1), [2] => (['a_name'] => 'Block', ['anzahl'] => 1)
	$l_attacken = array();
	$l_attacken = array();
	for($i=0;$i<count($attacken);$i++){
		$found = false;
		for($t=0;$t<count($l_attacken);$t++){
			if($attacken[$i] == $l_attacken[$t]['a_name']){
				$l_attacken[$t]['anzahl'] = $l_attacken[$t]['anzahl'] + 1;
				$found = true;
				break;
			}
		}
		if(!$found){
			$l_attacken[] = array('a_name' => $attacken[$i], 'anzahl' => 1);
		}
	}

	// Rufe nun die Funktion zum req'en der attacken auf :)
	addAttackUsageToChar($char_id, $l_attacken);

	if ($itemname) {
		$item_names = db_query("SELECT name FROM $new_item WHERE id IN(" . $itemname . ")");
		$x2 = 0;

		while ($new_row = @ mysqli_fetch_array($item_names)) {
			print " <script language='JavaScript'>
						<!--
						alert('$new_row[name] $item[wieviel]x erhalten');
						//-->
						</script>";
			$x2++;
		}

	}
	elseif ($punkte_block) {
		print " <script language='JavaScript'>
					<!--
					alert('$item[wieviel] $item[feld] erhalten');
					//-->
					</script>";
	}
}
semaphoreDown($ressource);
?>