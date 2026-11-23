<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');
include_once(ROOT_PATH.'/include/user.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/abholcodes.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/semaphore.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/img.inc.php');
include_once(ROOT_PATH.'/include/exp.inc.php');

// DEFINES FOR auto filling the forms!
define(AUTO_FIGHT_TIME, 'auto_fight_time');
define(AUTO_FIGHT_NAME, 'auto_fight_name');
define(AUTO_FIGHT_NONAME, 'auto_fight_noname');
define(AUTO_FIGHT_CREATE, 'auto_fight_create');
define(AUTO_FIGHT_LEVEL, 'auto_fight_level');

define(CREATE_FIGHT_TIME, 'create_fight_time');
define(CREATE_FIGHT_NAME, 'create_fight_name');
define(CREATE_FIGHT_LEVEL, 'create_fight_level');


// GET-Section
// Kritisch (SQL-Injections)
$lvl = validateUnsignedInteger($_GET['lvl'], null);
$pagenum = validateUnsignedInteger($_GET['pagenum'], null);
if(is_array($_GET['char_id'])){
  $char_id = validateUnsignedIntegerArray($_GET['char_id'], null);
} else {
  $char_id = validateUnsignedInteger($_GET['char_id'], null);
}
$kampf_id = validateUnsignedInteger($_GET['kampf_id'], null);
$kampf_zeit = validateUnsignedInteger($_GET['kampf_zeit'], null);
$kampf_name = validateName($_REQUEST['kampf_name']);
$portal = validateString($_GET['portal']);
$kampf_pw = validateString($_GET['kampf_pw']);

// Wird später geprüft
$phrase = $_GET['phrase'];
$imgid = $_GET['imgid'];


// Unkritisch
$charm = $_REQUEST['charm'];
$noname_autofight = $_GET['noname_autofight'];
$create_autofight = $_GET['create_autofight'];

$auth = true;


?>
<html>
	<head>
		<title>AnimeGame.eu</title>
		<meta http-equiv="Content-Language" content="de">
		<meta http-equiv="Content-Type" content="text/html;">
		<link href="design/style.css" rel="STYLESHEET" type="text/css">
	</head>
	<body>
<?php

function starteFight($chara_1, $chara_2, $kampf_info){
	include 'kampf2.php'; // Muss einfach noch umgeschrieben werden!! (Später!!)
}


function erstelleEinenFight($charid, &$user, $kampf_name, $kampf_pw, $kampf_zeit, $lvl){
// Ermittle ob der User eine Funktionierende Arena besitzt!
	$qry = db_query('Select * from arena where besitzer = '.$user['id']);
	if(mysqli_num_rows($qry) == 0){
		db_query('INSERT arena(besitzer, exp) values (\''.$user_ida['id'].'\', \'0,'.calculateRequiredExpArena(1).'\')');
		$arena = db_query('Select * from arena where besitzer = '.$user['id']);
	}
	$arena = mysqli_fetch_assoc($qry);
	if($arena['mietlasten'] > 6){
		displayErrorMessage('Kampf erstellen nicht M&ouml;glich!', 'Ihre Arena ist geschlossen', displayHistoryBackLink());
		return;
	} else if($arena['zustand'] <= 0.5){
		displayErrorMessage('Kampf erstellen nicht M&ouml;glich!', 'Ihre Arena ist in einem mieserablen Zustand', displayHistoryBackLink());
		return;
	}


	$char_a = getChar($charid);
	if(!isUserOwnerOf($user['id'],$charid)){
		return $char_a['name'].': Der Char geh&ouml;rt dir nicht.';
	}
	if($char_a['status'] != 'Frei') {
		return $char_a['name'].': Der Char ist nicht frei!';
	}
	if(($kampf_pw != '' && !is_null($kampf_pw)) && $user['pw_fight'] < 1) {
		return $char_a['name'].': Sie d&uuml;rfen nur 4 Passwortk&auml;mpfe pro Tag machen!';
	}

	$char_hp = explode(',', $char_a['hp']);

	if($char_hp['0'] < 50) {
		return $char_a['name'].': Die HP des Chars ist unter 50.';
	}

	if($char_hp[0] <= ($char_hp[1] / 100) * $char_a['aufgeben']) {
		return $char_a['name'].': Die HP des Chars ist unter seiner Aufgabegrenze.';
	}

	$w_zeit = time() + 43200;
	db_query('UPDATE chars SET status=\'Kampf Erstellt\' WHERE id='.$charid);
	if($lvl != 0){
		$sql = 'INSERT INTO kampf_list(lvlmin, lvlmax, kampfname, charakter, passwort, besitzer, zeit, zeit_rec, ip) values ('.($char_a['level']-$lvl).', '.($char_a['level']+$lvl).', \''.$kampf_name.'\', '.$charid.', \''.$kampf_pw.'\', \''.$user['nickname'].'\', '.$w_zeit.', '.$kampf_zeit.', \''.$_SERVER['REMOTE_ADDR'].'\')';
	} else{
		$sql = 'INSERT INTO kampf_list(lvlmin, lvlmax, kampfname, charakter, passwort, besitzer, zeit, zeit_rec, ip) values (0, 0, \''.$kampf_name.'\', '.$charid.', \''.$kampf_pw.'\', \''.$user['nickname'].'\', '.$w_zeit.', '.$kampf_zeit.', \''.$_SERVER['REMOTE_ADDR'].'\')';
	}
//	echo $sql.'<br';

	db_query($sql);
	if($kampf_pw != '' && !is_null($kampf_pw)){
		db_query('UPDATE user SET pw_fight=pw_fight-1 WHERE id='.$user['id']);
		$user['pw_fight']--;
	}

	return $char_a['name'].': Kampf wurde erfolgreich erstellt!';
}

function nehmeKampfAn($user, $charid, $kampf_id, $kampf_pw){
	if(!is_numeric($kampf_id)){
		displayErrorMessage(NULL,'Kampfid ist keine Zahl', displayHistoryBackLink());
		return;
	}

	$char_a = getChar($charid);
	if(!isUserOwnerOf($user['id'],$charid)){
		return $char_a['name'].': Der Char geh&ouml;rt dir nicht.';
	}
	if($char_a['status'] != 'Frei') {
		return $char_a['name'].': Der Char ist nicht frei!';
	}
	$char_hp = explode(',', $char_a['hp']);

	if($char_hp['0'] < 50) {
		return $char_a['name'].': Die HP des Chars ist unter 50.';
	}

	if($char_hp[0] <= ($char_hp[1] / 100) * $char_a['aufgeben']) {
		return $char_a['name'].': Die HP des Chars ist unter seiner Aufgabegrenze.';
	}

	// Reserviere den User!!
	$ressource1 = 'User:'.$user['id'];
	// Reserviere den Fight!!
	$ressource2 = 'Kampf:'.$kampf_id;
	if(!semaphoreUP($ressource1)){ // TRUE, diese Anfrage darf bearbeitet werden
		displayErrorMessage(NULL,'Verarbeitung gerade in Gange, bitte warten...', displayHistoryBackLink());
		return;
	}
	if(!semaphoreUP($ressource2)){ // TRUE, diese Anfrage darf bearbeitet werden
		displayErrorMessage(NULL,'Kampf existiert nicht mehr!', displayHistoryBackLink());
		semaphoreDown($ressource1);
		return;
	}
	$sql = 'SELECT * FROM kampf_list where id = '.$kampf_id.' AND ('.$char_a['level'].' between lvlmin and lvlmax OR lvlmax = 0) AND ip != \''.$_SERVER['REMOTE_ADDR'].'\' AND (passwort = \'\' OR passwort = \''.$kampf_pw.'\')';
//	echo $sql.'<br>';
	$fight_row = mysqli_fetch_assoc(db_query($sql));

	if(!$fight_row){
		$fight_row = mysqli_fetch_assoc(db_query('SELECT * FROM kampf_list where id = '.$kampf_id));
		if(!$fight_row){
			displayErrorMessage(NULL,'Kampf existiert nicht mehr!', displayHistoryBackLink());
		} else if($_SERVER['REMOTE_ADDR'] == $fight_row['ip']){
			displayErrorMessage(NULL,'Kampf kann nicht angenommen werden, da die IP gleich ist!', displayHistoryBackLink());
		} else if($fight_row['lvlmax'] != 0 && $fight_row['lvlmin'] > $char_a['level']){
			displayErrorMessage(NULL,'Level zu niedrig!', displayHistoryBackLink());
		} else if($fight_row['lvlmax'] != 0 && $fight_row['lvlmax'] < $char_a['level']){
			displayErrorMessage(NULL,'Level zu hoch!', displayHistoryBackLink());
		} else if($fight_row['passwort'] != $kampf_pw){
			displayErrorMessage(NULL,'Passwort falsch!', displayHistoryBackLink());
		} else{
			displayErrorMessage(NULL,'Kampf konnte nicht angenommen werden, warum auch immer!', displayHistoryBackLink());
		}
		semaphoreDown($ressource2);
		semaphoreDown($ressource1);
		return;
	}

	$char_b = getChar($fight_row['charakter']);
	if(isUserOwnerOf($user['id'], $fight_row['charakter'])){
		displayErrorMessage(NULL,'Annehmen von eigenen Fights ist nicht möglich!', displayHistoryBackLink());
		semaphoreDown($ressource2);
		semaphoreDown($ressource1);
		return;
	}

	createAbholcode($char_a['id']);
	createAbholcode($char_b['id']);

	// Ein Kampf kostet 1 Aktionspunkt!
	hasDoneAction($user, 1);

	// So nun kann der Fight in aller Ruhe starten!
	starteFight($char_a, $char_b, $fight_row);

	displayErrorMessage(NULL,'Kampf angenommen...', '');

	semaphoreDown($ressource2);
	semaphoreDown($ressource1);
}

function nehmeAutofightAn($user, $charid, $kampf_name, $kampf_zeit, $lvl){

	$char_a = getChar($charid);
	if(!isUserOwnerOf($user['id'],$charid)){
		return $char_a['name'].': Der Char geh&ouml;rt dir nicht.';
	}
	if($char_a['status'] != 'Frei') {
		return $char_a['name'].': Der Char ist nicht frei!';
	}
	$char_hp = explode(',', $char_a['hp']);

	if($char_hp['0'] < 50) {
		return $char_a['name'].': Die HP des Chars ist unter 50.';
	}

	if($char_hp[0] <= ($char_hp[1] / 100) * $char_a['aufgeben']) {
		return $char_a['name'].': Die HP des Chars ist unter seiner Aufgabegrenze.';
	}
	// Char ist ok! Suche Fight ^^
	if($kampf_name !== '' && !is_null($kampf_name)){
		$whereclause = ' kampfname like \''.$kampf_name.'\' AND ';
	}
	if($lvl > 0 && $lvl < 10){
		$sql = 'SELECT kl.id FROM kampf_list kl inner join chars c on kl.charakter = c.id WHERE c.level between '.($char_a['level'] - $lvl).' and '.($char_a['level'] + $lvl).' AND '.$whereclause.' kl.besitzer != \''.$user['nickname'].'\' AND ('.$char_a['level'].' between kl.lvlmin and kl.lvlmax OR kl.lvlmax = 0) AND kl.IP != \''.$_SERVER['REMOTE_ADDR'].'\' AND kl.rasse != \'NPC\' AND kl.zeit_rec = '.$kampf_zeit.' AND passwort = \'\' ORDER BY id ASC';
	} else {
		$sql = 'SELECT id FROM kampf_list WHERE '.$whereclause.' besitzer != \''.$user['nickname'].'\' AND ('.$char_a['level'].' between lvlmin and lvlmax OR lvlmax = 0) AND IP != \''.$_SERVER['REMOTE_ADDR'].'\' AND rasse != \'NPC\' AND zeit_rec = '.$kampf_zeit.' AND passwort = \'\' ORDER BY id ASC';
	}

	// Nur mal die ID-Liste ausgeben
//	echo $sql.'<br>';
	$qry = db_query($sql);
	$fight_row = NULL;
	while($row = mysqli_fetch_assoc($qry)){
		$ressource = 'Kampf:'.$row['id'];
		// Wichtig, die Semaphore!!
		if(semaphoreUP($ressource)){ // TRUE, diese Anfrage darf bearbeitet werden
			$fight_row = mysqli_fetch_assoc(db_query('SELECT * FROM kampf_list where id = '.$row['id']));
			if($fight_row){
				break;
			}
		}
		semaphoreDown($ressource); // kein Break?? Dann gebe Ressource frei!!
	}
	if(!$fight_row){
		return $char_a['name'].': Es konnte kein Fight gefunden werden!';
	}
	//  Nur noch Informationen ueber den anderen Char holen!!
	$chara_2 = getChar($fight_row['charakter']);

	createAbholcode($char_a['id']);
	createAbholcode($chara_2['id']);

	// Pro Char kostet das annehmen 1 Aktionspunkt
	hasDoneAction($user,1);

	// So nun kann der Fight in aller Ruhe starten!
	starteFight($char_a, $chara_2, $fight_row);

	semaphoreDown($ressource);
	return NULL;
}

function autokampf($user, $kampf_name, $char_id, $kampf_zeit, $lvl, $acceptWithoutName, $createAfterAccept){
	if(!is_numeric($lvl) || !is_numeric($kampf_zeit) || $kampf_zeit < 0 || c > 4){
		displayErrorMessage(NULL,'Fehlerhafte Eingabe!',displayHistoryBackLink());
		return;
	}

	$kampf_zeit *= 300;
	// User darf erstmal nix mehr machen!
	$ressource = 'User:'.$user['id'];
	// Wichtig, die Semaphore!!
	if(!semaphoreUP($ressource)){ // TRUE, diese Anfrage darf bearbeitet werden
		displayErrorMessage(NULL,'Verarbeitung gerade in Gange, bitte warten...', displayHistoryBackLink());
		return;
	}

	if(is_array($char_id)){
		for($i=0;$i<count($char_id);$i++){
			if(is_numeric($char_id[$i])){
				$txt = nehmeAutofightAn($user, $char_id[$i], $kampf_name, $kampf_zeit, $lvl);
				if($txt != NULL && $acceptWithoutName == 1) {
					$txt = nehmeAutofightAn($user, $char_id[$i], '', $kampf_zeit, $lvl);
				}
				if($txt != NULL && $createAfterAccept == 1) {
					// does always return text!!
					$txt = erstelleEinenFight($char_id[$i], $user, $kampf_name, '', $kampf_zeit, $lvl);
				}

				if($txt == NULL) {
					$char = getChar($char_id[$i]);
					$txt = $char['name']. ': Kampf wurde erfolgreich angenommen!';
				}

				$tmp .= $txt ."<br>\n";
			}
		}
		setUserMetaDataEntry($user['id'], AUTO_FIGHT_NAME, $kampf_name);
		setUserMetaDataEntry($user['id'], AUTO_FIGHT_TIME, $kampf_zeit/300);
		setUserMetaDataEntry($user['id'], AUTO_FIGHT_LEVEL, $lvl);
		setUserMetaDataEntry($user['id'], AUTO_FIGHT_CREATE, $createAfterAccept==1?1:0);
		setUserMetaDataEntry($user['id'], AUTO_FIGHT_NONAME, $acceptWithoutName==1?1:0);
	} else {
		$tmp = 'Kein Char ausgew&auml;hlt!';
	}

	displayErrorMessage(NULL,$tmp, '');

	semaphoreDown($ressource);
}
function erstelleFights($user, $kampf_name, $kampf_pw, $char_id, $kampf_zeit, $lvl){

	if(!is_numeric($lvl) || !is_numeric($kampf_zeit) || $kampf_zeit < 0 || c > 4){
		displayErrorMessage(NULL,'Fehlerhafte Eingabe!',displayHistoryBackLink());
		return;
	}
	$kampf_zeit *= 300;

	if($kampf_name === NULL || $kampf_name == '') { // Kampfname ist fuer alle gleich einzugeben!
		displayErrorMessage(NULL,'Geben sie bitte den Kampfnamen ein.',displayHistoryBackLink());
		return;
	}

	// User darf erstmal nix mehr machen!
	$ressource = 'User:'.$user['id'];
	// Wichtig, die Semaphore!!
	if(!semaphoreUP($ressource)){ // TRUE, diese Anfrage darf bearbeitet werden
		displayErrorMessage(NULL,'Verarbeitung gerade in Gange, bitte warten...', displayHistoryBackLink());
		return;
	}

	if(is_array($char_id)){
		for($i=0;$i<count($char_id);$i++){
			if(is_numeric($char_id[$i])){
				$tmp .= erstelleEinenFight($char_id[$i], $user, $kampf_name, $kampf_pw, $kampf_zeit, $lvl)."<br>\n";
			}
		setUserMetaDataEntry($user['id'], CREATE_FIGHT_NAME, $kampf_name);
		setUserMetaDataEntry($user['id'], CREATE_FIGHT_TIME, $kampf_zeit/300);
		setUserMetaDataEntry($user['id'], CREATE_FIGHT_LEVEL, $lvl);
		}
		// Pro erstellter Fight kostet es einen Aktionspunkt!
		hasDoneAction($user,count($char_id));
	} else {
		$tmp = 'Kein Char ausgew&auml;hlt!';
	}

	displayErrorMessage(NULL,$tmp, '');

	semaphoreDown($ressource);
}

function displayAutoFights($user, $portal, $pagenum){
	$meta_data = getUserMetaData($user['id']);
	?>
	<form action="kampf_list.php" method="GET">
		<input type="hidden" name="charm" value="3">
		<input type="hidden" name="portal" value="<?php echo $portal; ?>">
		<input type="hidden" name="pagenum" value="<?php echo $pagenum; ?>">
		<table id="content" width="100%">
			<tr>
				<th id="content" align="center" colspan="2">Autofight!</th>
			</tr>
			<tr>
				<th id="content" align="center">Kampfname</th>
				<td><input id="input" name="kampf_name"/ value="<?php echo $meta_data[AUTO_FIGHT_NAME]; ?>"></td>
			</tr>
			<tr>
				<th id="content" align="center">Charakter</th>
				<td id="content">
<?php
		$chars = getCharsOfUser($user['id']);
		foreach($chars as $row){
			if($row['status'] != 'Frei'){
				$disabled = ' disabled="1" ';
			} else{
				$disabled = '';
			}
			echo '<input id="input" type="checkbox" name="char_id[]" value="'.$row['id'].'"'.$disabled.' checked="1">'.$row['name'].'</input><br>'."\n";
		}
?>
				</td>
			</tr>
			<tr>
				<th id="content" align="center">Kampf Zeit</th>
				<td>
					<select id="input" name="kampf_zeit">
						<option value="1" <?php echo $meta_data[AUTO_FIGHT_TIME]==1?'selected="selected"':''; ?>>5min</option>
						<option value="4"  <?php echo $meta_data[AUTO_FIGHT_TIME]==4?'selected="selected"':''; ?>>20min</option>
						<option value="12" <?php echo $meta_data[AUTO_FIGHT_TIME]==12?'selected="selected"':''; ?>>60min</option>
					</select>
				</td>
			</tr>
			<tr>
				<th id="content" align="center">Level Begrenzung</th>
				<td>
					<select id="input" name="lvl">
						<option value="0">alle</option>
						<?php
							// Dynamisches einstellen der Stufen
							for($i=1;$i<10;$i++){
								if($meta_data[AUTO_FIGHT_LEVEL] == $i) {
									$select = 'selected="selected"';
								} else {
									$select = '';
								}

								echo '<option value="'.$i.'" '.$select.'>'.$i.'</option>';
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th id="content" align="center">Falls kein Kampf gefunden wird:</th>
				<td id="content" >
					<input type="checkbox" name="noname_autofight" <?php echo $meta_data[AUTO_FIGHT_NONAME]==1?'checked="checked"':''; ?> value="1"/>Autofight mit und ohne Namensbegrenzung<br>
					<input type="checkbox" name="create_autofight" <?php echo $meta_data[AUTO_FIGHT_CREATE]==1?'checked="checked"':''; ?> value="1"/>Kampf erstellen
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input id="input" type="submit" value="Autofight!">
				</td>
			</tr>
		</table>
	</form>
	<?php
}

function displayKampfErstellen($user, $portal, $pagenum){
	$meta_data = getUserMetaData($user['id']);
	// Ermittle ob der User eine Funktionierende Arena besitzt!
	$qry = db_query('Select * from arena where besitzer = '.$user['id']);
	$arena = mysqli_fetch_assoc($qry);
	if(mysqli_num_rows($qry) == 0){
		db_query('INSERT arena(besitzer, exp) values (\''.$user['id'].'\', \'0,'.calculateRequiredExpArena(1).'\')');
		$arena = mysqli_fetch_assoc(db_query('Select * from arena where besitzer = '.$user['id']));
	}
	if($arena['mietlasten'] > 7){
		displayErrorMessage('Kampf erstellen nicht M&ouml;glich!', 'Ihre Arena ist geschlossen', displayHistoryBackLink());
		return;
	} else if($arena['zustand'] <= 0.5){
		displayErrorMessage('Kampf erstellen nicht M&ouml;glich!', 'Ihre Arena ist in einem mieserablen Zustand', displayHistoryBackLink());
		return;
	}
	?>
	<form action="kampf_list.php" method="GET">
		<input type="hidden" name="charm" value="2">
		<input type="hidden" name="portal" value="<?php echo $portal; ?>">
		<input type="hidden" name="pagenum" value="<?php echo $pagenum; ?>">
		<table id="content" width="100%">
			<tr>
				<th id="content" align="center" colspan="2">Kampf Erstellen</th>
			</tr>
			<tr>
				<th id="content" align="center">Kampfname</th>
				<td id="content" ><input id="input" name="kampf_name" value="<?php echo $meta_data[CREATE_FIGHT_NAME]; ?>"></td>
			</tr>
			<tr>
				<th id="content" align="center">Charakter</th>
				<td id="content">
<?php
		$chars = getCharsOfUser($user['id']);
		foreach($chars as $row){
			if($row['status'] != 'Frei'){
				$disabled = ' disabled="1" ';
			} else{
				$disabled = '';
			}
			echo '<input id="input" type="checkbox" name="char_id[]" value="'.$row['id'].'"'.$disabled.' checked="1">'.$row['name'].'</input><br>'."\n";
		}
?>
				</td>
			</tr>
			<tr>
				<th id="content" align="center">Kampf Zeit</th>
				<td>
					<select id="input" name="kampf_zeit">
						<option value="1" <?php echo $meta_data[CREATE_FIGHT_TIME]==1?'selected="selected"':''; ?>>5min</option>
						<option value="4" <?php echo $meta_data[CREATE_FIGHT_TIME]==4?'selected="selected"':''; ?>>20min</option>
						<option value="12" <?php echo $meta_data[CREATE_FIGHT_TIME]==12?'selected="selected"':''; ?>>60min</option>
					</select>
				</td>
			</tr>
			<tr>
				<th id="content" align="center">Level Begrenzung</th>
				<td id="content" >
					<select id="input" name="lvl">
						<option value="0">alle</option>
						<?php
							// Dynamisches einstellen der Stufen
							for($i=1;$i<10;$i++){
								if($meta_data[CREATE_FIGHT_LEVEL] == $i) {
									$select = 'selected="selected"';
								} else {
									$select = '';
								}
								echo '<option value="'.$i.'" '.$select.'>'.$i.'</option>';
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th id="content" align="center">Passwort</th>
				<td id="content">
					<input id="input" name="kampf_pw">
				</td>
			</tr>
			<tr>
				<td id="content">&nbsp;</td>
				<td id="content">
					<input id="input" type="submit" value="Kampf Erstellen">
				</td>
			</tr>
		</table>
	</form>
	<?php
}

function display($user, $portal, $pagenum){
	if(!is_numeric($pagenum)){
		$pagenum = 0;
	}
	?>
		<table id="content" width="100%" border="2">
			<tr id="content">
				<td id="content" align="center" colspan="2">
					<a id="popup" href='kampf_list.php?charm=5' title='Automatische Zuweisung f&uuml;r deine Chars.'>Auto Kampf</a>
					/
					<a id="popup" href='kampf_list.php?portal=NPC' title='Das Portal in dem Noobs gegen NPCs K&auml;mpfen k&ouml;nnen'>NPC Kampf</a>
				</td>
				<td id="content" align="center" colspan="2">
					<a id="popup" href='kampf_list.php?portal=LVL' title='Alle begrenzten und passwortgeschützten Fights.'>Begrenzt</a>
					/
					<a id="popup" href='kampf_list.php' title='Hier stehen alle freien Fights aufgelistet.'>Free 4 All</a>
				</td>
				<td id="content" align="center" colspan="2">
					<a id="popup" href="kampf_list.php?charm=1">Kampf Erstellen</a>
				</td>
	        </tr>
	        <tr id="content">
	          <td id="content" align="center">Gegner</td>
	          <td id="content" align="center">Info</td>
	          <td id="content" align="center">Kampf Zeit</td>
	          <td id="content" align="center">Arena</td>
	          <td id="content" align="center">Passwort</td>
	          <td id="content" align="center">K&auml;mpfen</td>
	        </tr>
	<?php

	if($portal == 'NPC') {
		$kampf_l = db_query('SELECT n.mal, k.rasse, k.id, k.passwort, k.kampfname, k.lvlmin, k.lvlmax, k.zeit_rec, k.besitzer, k.charakter ,a.name as a_name, a.level as a_level, a.luxus, a.loge, a.steh, a.sitz FROM kampf_list k LEFT JOIN chars c ON(c.id=k.charakter) LEFT JOIN npc_item n ON(n.charakter=c.id) inner join arena a ON a.besitzer = c.besitzer WHERE k.rasse=\'NPC\' ORDER BY c.level');
		$total = mysqli_fetch_row(db_query('SELECT count(*) FROM kampf_list WHERE rasse=\'NPC\''));
		$total = $total[0];
		$entriesPerPage = $total;
	} else if($portal == 'LVL'){
		$entriesPerPage = 10;
		$kampf_l = db_query('SELECT k.id, k.passwort, k.kampfname, k.lvlmin, k.lvlmax, k.zeit_rec, k.besitzer, k.charakter, a.name as a_name, a.level as a_level, a.luxus, a.loge, a.steh, a.sitz FROM kampf_list k LEFT JOIN chars c ON(c.id=k.charakter) inner join arena a ON a.besitzer = c.besitzer WHERE k.rasse=\'ALL\' and k.lvlmax != 0 or passwort != \'\' ORDER BY k.id ASC LIMIT '.$pagenum*$entriesPerPage.','.$entriesPerPage);
		$total = mysqli_fetch_row(db_query('SELECT count(*) FROM kampf_list WHERE rasse=\'ALL\' and lvlmax != 0 or passwort != \'\''));
		$total = $total[0];
	} else{
		$entriesPerPage = 10;
		$kampf_l = db_query('SELECT k.id, k.passwort, k.kampfname, k.lvlmin, k.lvlmax, k.zeit_rec, k.besitzer, k.charakter, a.name as a_name, a.level as a_level, a.luxus, a.loge, a.steh, a.sitz FROM kampf_list k LEFT JOIN chars c ON(c.id=k.charakter) inner join arena a ON a.besitzer = c.besitzer WHERE k.rasse=\'ALL\' and k.lvlmax = 0 and passwort = \'\' ORDER BY k.id ASC LIMIT '.$pagenum*$entriesPerPage.','.$entriesPerPage);
		$total = mysqli_fetch_row(db_query('SELECT count(*) FROM kampf_list WHERE rasse=\'ALL\' and lvlmax = 0 and passwort = \'\''));
		$total = $total[0];
	}

	while($row = mysqli_fetch_array($kampf_l)) {
		$char = getChar($row['charakter']);
		if($row['passwort']){
			$pw_exe = 'pass1';
		} else {
			$pw_exe = 'pass2';
		}
		$ca_pl = ($char['starke'] + $char['verteidigung'] + $char['speed'] + $char['ausdauer'] + $char['glueck']);
		$ca_pls = 'PL';

		$char_min = $row['lvlmin'];
		$char_max = $row['lvlmax'];
		if($char_min < 1) {
			$char_min = 0;
		}
		?>
			<tr>
				<td id="content" align="center">
		          	<br>
					<?php echo $row['kampfname']; ?><br>
					<img border="0" src="<?php echo $char['bild']; ?>" width="75" height="75"><br>
					<?php echo $char['name']; ?>
				</td>
				<td id="content" align="center">
					<?php
						if($char_max == 0){
							$lvlbegr = '';
						}else{
							$lvlbegr = '<br>LVL Begrenzung: '.$char_min.' - '.$char_max;
						}
						echo 'Level: '.$char['level'].'<br>'.$ca_pls.': '.$ca_pl.$lvlbegr;
					?>
				</td>
				<td id="content" align="center">
					<?php echo $row['zeit_rec']/60; ?>Min
				</td>
				<td id="content" align="center">
					<?php
					 echo '<strong>'.$row['a_name'].'</strong> ('.$row['besitzer'].')<br>';
					 echo 'Stehplätze: '.$row['steh'].'<br>';
					 echo 'Sitzplätze: '.$row['sitz'].'<br>';
					 echo 'Logenplätze: '.$row['loge'].'<br>';
					 echo 'Luxus: '.$row['luxus'].'<br>';
					 ?>
				</td>
				<td id="content" align="center">
					<?php
		if($row['rasse'] == 'NPC') {
			$anzahl_min = mysqli_fetch_row(db_query('SELECT count(id) FROM npc_ware WHERE charakter=\''.$row['charakter'].'\' AND user='.$user['id'].' LIMIT 50'));
			$anzahl_min = $anzahl_min[0];
			echo 'geschafft ('.$anzahl_min.'/'.$row['mal'].')<br>';
		} else {
		?>
			<img border="0" src="design/bilder/buttons/<?php echo $pw_exe.'.gif'; ?>" width="60" height="16">
			<?php
		}
		?>
				</td>
				<td id="content" align="center">

<?php
		if($char['besitzer'] == $user['id']) {
?>
					<a id="popup" href="kampf_list.php?charm=7&kampf_id=<?php echo $row['id']; ?>&portal=<?php echo $portal; ?>">zur&uuml;ckziehen</a>
<?php
		} else {
?>
					<a id="popup" href="kampf_list.php?charm=4&kampf_id=<?php echo $row['id']; ?>&portal=<?php echo $portal; ?>">annehmen</a>
<?php
		}

?>

				</td>
			</tr>
		<?php
	}
	?>
		<tr>
			<td colspan="6">
				<?php echo displayPagelinksNew($entriesPerPage, $total, $pagenum, '<a href="kampf_list.php?pagenum=###PAGE###&portal='.$portal.'">###LABEL###</a>'); ?>
			</td>
		</tr>
	</table>
<?php
}


function displayKampfAnnehmen($user, $kampf_id, $portal, $pagenum){
	?>
	<form action="kampf_list.php" method="GET">
		<input type="hidden" name="portal" value="<?php echo $portal; ?>">
		<input type="hidden" name="pagenum" value="<?php echo $pagenum; ?>">
		<input type="hidden" name="charm" value="6">
		<table width="100%" id="content">
			<input type="hidden" name="kampf_id" value="<?php echo $kampf_id; ?>">
			<tr>
				<th id="content" align="center" colspan="2">Kampf annehmen</th>
			</tr>
			<tr>
				<th id="content" align="center">Charakter</th>
				<td id="content">
					<select id="input" name="char_id">
<?php
		$chars = getCharsOfUser($user['id']);
		foreach($chars as $row){
			echo '<option value="'.$row['id'].'">'.$row['name'].' | '.$row['status'].'</option>'."\n";
		}
?>
					</select>
				</td>
			</tr>
			<tr>
				<th id="content" align="center">Passwort</th>
				<td id="content">
					<input id="input" name="kampf_pw">
				</td>
			</tr>
			<tr>
				<td id="content">&nbsp;</td>
				<td id="content">
					<input id="input" type="submit" value="annehmen">
				</td>
			</tr>
		</table>
	</form>
	<?php
}


function zieheKampfZurueck($user, $kampf_id){
	// User darf erstmal nix mehr machen!
	if(!is_numeric($kampf_id)){
		return;
	}

	$kampf_a = mysqli_fetch_assoc(db_query('SELECT * FROM kampf_list where id = '.$kampf_id));
	if(!$kampf_a){
		displayErrorMessage(NULL,'Kampf ist nicht mehr vorhanden! ('.$kampf_a['charakter'].')', displayHistoryBackLink());
		return;
	}
	$char_id = $kampf_a['charakter'];

	$char_a = getChar($char_id);
	if(!isUserOwnerOf($user['id'], $char_id)){
		displayErrorMessage(NULL,'Der Char gehoert nicht dir!!', displayHistoryBackLink());
		return;
	}

	// Reserviere den User!!
	$ressource1 = 'User:'.$user['id'];
	// Reserviere den Fight!!
	$ressource2 = 'Kampf:'.$kampf_id;


	if(!semaphoreUP($ressource1)){
		displayErrorMessage(NULL,'Verarbeitung gerade in Gange, bitte warten...', displayHistoryBackLink());
		return;
	}

	if(!semaphoreUP($ressource2)){
		semaphoreDown($ressource1);
		return;
	}

	db_query('DELETE FROM kampf_list WHERE id = '.$kampf_id);
	db_query('UPDATE chars SET status = \'Frei\' WHERE id ='.$char_id);
	if($kampf_a['kampf_pw'] !== '' || $kampf_a['kampf_pw'] !== NULL){
		db_query('UPDATE user SET pw_fight = pw_fight + 1 WHERE id ='.$user['id']);
	}
	semaphoreDown($ressource2);
	semaphoreDown($ressource1);

	displayErrorMessage(NULL,'Der Kampf wurde zur&uuml;ckgezogen!', '');
}

if(hasToAuthenticate($user_ida)){
	if(checkImageCode($imgid, $phrase, $user_ida) ){
		resetUserCounter($user_ida);
	} else{
		$id = generateImageID();
		echo '<form action="kampf_list.php" method="GET">';
		while (list($key, $val) = each($_GET)) {
		    echo '<input type="hidden" name="'.$key.'" value="'.$val.'" />';
		}
		echo '<table width="100%" class="content"><tr><th class="content">Bitte Code eingeben</th></tr><tr><td align="center"><img src="img.php?id='.$id.'"></td></tr><tr><td align="center"><input class="input" name="phrase"/><input name="imgid" value="'.$id.'" type="hidden"/></td></tr><tr><td align="center"><input id="input" type="submit" value="eingeben"></td></tr></table></form>';
		$auth = false;
	}
}
if($auth == false){
	// Do nothing anymore
} else if($charm == 1){
	displayKampfErstellen($user_ida, $portal, $pagenum);
	display($user_ida, $portal, $pagenum);
} else if($charm == 2){
// function charm2($user, $kampf_name, $kampf_pw, $char_id, $kampf_zeit, $lvl){
	erstelleFights($user_ida, $kampf_name, $kampf_pw, $char_id, $kampf_zeit, $lvl);
	display($user_ida, $portal, $pagenum);
} else if($charm == 3){
	autokampf($user_ida, $kampf_name, $char_id, $kampf_zeit, $lvl, $noname_autofight, $create_autofight);
	display($user_ida, $portal, $pagenum);
} else if($charm == 4){
	displayKampfAnnehmen($user_ida, $kampf_id,$portal, $pagenum);
	display($user_ida, $portal, $pagenum);
} else if($charm == 5){
	displayAutoFights($user_ida, $portal, $pagenum);
	display($user_ida, $portal, $pagenum);
} else if($charm == 6){
	nehmeKampfAn($user_ida, $char_id, $kampf_id, $kampf_pw);
	display($user_ida, $portal, $pagenum);
} else if($charm == 7){
	zieheKampfZurueck($user_ida, $kampf_id);
	display($user_ida, $portal, $pagenum);
} else{
	display($user_ida, $portal, $pagenum);
}
?>
</body>
</html>
