<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/erstellfunctions.inc.php');
include_once(ROOT_PATH.'/include/semaphore.inc.php');
include_once(ROOT_PATH.'/include/random.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
// Nothing
// Unkritisch
$charm = $_GET['charm'];
$wunsch = $_GET['wunsch'];
$h_name = validateName($_GET['h_name']);


// Nicht viel aber eine große Auswirkung
function wuenscheGeld($user){
	db_query('UPDATE user SET geld=geld+10000000 WHERE id='.$user['id']);
	return true;
}

function wuenschePotaras($user){
	db_query('INSERT sp_ware SET item=1, user='.$user['id']);
	db_query('INSERT sp_ware SET item=1, user='.$user['id']);
	return true;
}

function wuenscheTeufelsfrucht($user){
	$teufelsseele = mysqli_fetch_assoc(db_query('SELECT count(id) as anzahl FROM sp_ware WHERE item=33 AND user='.$user['id']));

	if($teufelsseele['anzahl'] <= 0) {
		displayErrorMessage(NULL,'Du besitzt das benötigte Item nicht!!',displayHistoryBackLink());
		return false;
	}
	db_query('DELETE FROM sp_ware WHERE item=33 AND user='.$user['id'].' LIMIT 1');

	$sum = mysqli_fetch_assoc(db_query('SELECT sum(verteilung) as gesamt FROM wochen_markt WHERE art=\'natur\''));
	if($sum['gesamt'] != 100){
		displayErrorMessage(NULL,'Die Wahrscheinlichkeit eine Frucht zu bekommen entspricht nicht 100%, Melde dich bei einem der GM!!',displayHistoryBackLink());
		return false;
	}
	$natur_frucht = db_query('SELECT item, id,verteilung FROM wochen_markt WHERE art=\'natur\' order by id');		///wieviel TF gibt es?.
	$value = mt_random_wrapper(1,100);


	$peak = 0;
	while($row = mysqli_fetch_assoc($natur_frucht)){
		$peak += $row['verteilung'];
		if($value <= $peak){
			break;
		}
	}
	displayErrorMessage(NULL,'Du hast eine '.$row['item'].' bekommen!!','');
	db_query('INSERT wochen_ware SET item='.$row['id'].', user='.$user['id']);
	return true;
}

function wuenscheSpecialChar($user, $specialcharid, $newname = ''){
	$specialRaceIds = getSpecialRaceIds();
	$race = NULL;
	foreach ($specialRaceIds as $specialRace) {
		if ($specialRace['item'] !== NULL && $specialRace['id'] == $specialcharid) {
			$race = $specialRace;
			break;
		}
	}
	
	if ($race == NULL) {
		displayErrorMessage(NULL,'Diese Rasse kann nicht gew&uuml;nscht werden!!',displayHistoryBackLink());
		return false;
	}

	if($newname == ''){
		displayErrorMessage(NULL,'Der Name ist ungueltig!!',displayHistoryBackLink());
		return false;
	}

	// Hat der User überhaupt das Item ???
	$qry = db_query('SELECT count(*) AS anzahl FROM sp_ware WHERE item='.$race['item'].' AND user='.$user['id']);
	$row = mysqli_fetch_assoc($qry);
	if($row['anzahl'] < 1){
		// Er hat das Item garnicht!!
		displayErrorMessage(NULL,'Du kannst dir einen '.$specialcharname.' nur mit dem richtigen Item wünschen!!',displayHistoryBackLink());
		return false;
	}

	$rasse = $race;
	
	$chars = getCharsOfUser($user['id']);
	foreach($chars as $char) {
		if($char['rasse_a']['id'] == $rasse['id']) {
			displayErrorMessage(NULL,'Der '.$specialcharname.'-Slot ist schon durch '.$char['name'].' belegt!!',displayHistoryBackLink());
			return false;
		}
	}

	// Kleine Funktion grosse Wirkung
	if(!erstelleChar($user, $rasse['id'], $newname)){
		return false;
	}

	// Slotüberprüfung beendet!
	db_query('DELETE FROM sp_ware WHERE item='.$race['item'].' AND user='.$user['id'].' LIMIT 1');

	return true;
}


function wuenscheUserRename($user, $newname){
	if($newname == null || strlen(trim($newname)) < 3) {
		return false;
	}
	$sql = 'Insert into user_rename(pre_name, post_name, datum, userid, wunsch) values(\''.$user['nickname'].'\', \''.$newname.'\', now(), '.$user['id'].', 1)';
	db_query($sql);
	db_query('Update user set nickname = \''.$newname.'\' WHERE id = '.$user['id']);
	return true;
}

function worker($charm,$wunsch,$user,$newname){
	$ressource = 'User:'.$user['id'];
	// Wichtig, die Semaphore!!
	if(!semaphoreUP($ressource)){ // TRUE, diese Anfrage darf bearbeitet werden
		displayErrorMessage(NULL,'Verarbeitung gerade in Gange, bitte warten...', displayHistoryBackLink());
		return;
	}

	$dragonballs = mysqli_fetch_assoc(db_query('SELECT count(id) as anzahl FROM sp_ware WHERE user='.$user['id'].' AND item=11'));
	if($dragonballs['anzahl'] < 7) {
		displayErrorMessage(NULL,'Du hast keine 7 Dragonballs!!',displayHistoryBackLink());
		semaphoreDown($ressource);
		return;
	}

	$sql = 'DELETE FROM sp_ware WHERE item=11 AND user='.$user['id'].' LIMIT 7';
	db_query($sql);

	$result = true;
	if($charm == 1) {
		if($wunsch == 1) {
			$result = wuenscheGeld($user);
		} else if($wunsch == 2) {
			$result = wuenschePotaras($user);
		} else if($wunsch == 5) {
			$result = wuenscheTeufelsfrucht($user);
		} else if($wunsch == 6) {
			$result = wuenscheUserRename($user,$newname);
		} else if($wunsch > 10) {
			$result = wuenscheSpecialChar($user,$wunsch - 10,$newname);
		}
	}
	if($result){
		displayErrorMessage('&Auml;nderungen &uuml;bernommen!','Dragonballs erfolgreich eingesetzt!!','<a href="index.php?as=dragonballs">weiter</a>');
	} else{
		db_query('INSERT sp_ware SET item=11, user='.$user['id']); // 1
		db_query('INSERT sp_ware SET item=11, user='.$user['id']); // 2
		db_query('INSERT sp_ware SET item=11, user='.$user['id']); // 3
		db_query('INSERT sp_ware SET item=11, user='.$user['id']); // 4
		db_query('INSERT sp_ware SET item=11, user='.$user['id']); // 5
		db_query('INSERT sp_ware SET item=11, user='.$user['id']); // 6
		db_query('INSERT sp_ware SET item=11, user='.$user['id']); // 7
	}
	semaphoreDown($ressource);
	return;
}


function displayWuensche($user){
$items = db_query('SELECT distinct(i1.id) FROM sp_ware i INNER JOIN sp_item i1 ON(i.item=i1.id) WHERE i.user='.$user['id']);

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
<input type="hidden" name="as" value="dragonballs">
<input type="hidden" name="charm" value="1">
<table cellpadding="0" cellspacing="0" width="100%">
	<!-- MSTableType="layout" -->
	<tr>
		<th align="center">W&uuml;nsch dir etwas</th>
	</tr>
	<tr>
		<td align="center">
<?php
$specialRaceIds = getSpecialRaceIds();
$races = array();
foreach ($specialRaceIds as $race) {
	if ($race['item'] !== NULL) {
		$races[] = $race;
	}
}

?>
			<select id="input" name="wunsch">
				<option value="1">10.000.000 &#165;</option>
				<option value="2">2x Potara Ohrringe</option>
				<option value="6">Nutzernamen &auml;ndern</option>
<?php


while($row = mysqli_fetch_assoc($items)) {
	foreach ($races as $race) {
		if ($row['id'] == $race['item']) {
			echo '<option value="'.($race['id']+10).'">'.$race['name'].' befreien</option>'."\n";
		}
	}

	if($row['id'] == 33) {
?>
					<option value="5">Naturfrucht</option>
<?php
	}
}
?>
			</select>
		</td>
	</tr>
		<th align="center">
			<br>Name nur bei Erschaffung von Specialchars oder &Auml;nderung des Nutzernamens
		</th>
	</tr>
	<tr>
		<td align="center">
			<input id="input" name="h_name" value="" size="20"><br>&nbsp;<br>
			<input id="input" type=submit value="Shenlong erscheine und erf&uuml;lle mir meinen Wunsch!"></td>
		</td>
	</tr>
</table>
<?php
}

if($charm !== NULL && $wunsch !== NULL){
	worker($charm, $wunsch,$user_ida,$h_name);
} else{
	displayWuensche($user_ida);
}
?>
