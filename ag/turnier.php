<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');
include_once(ROOT_PATH.'/include/halloffame.inc.php');
include_once(ROOT_PATH.'/include/auktion_functions.inc.php');
include_once(ROOT_PATH.'/include/tournament.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$char_id = validateUnsignedInteger($_GET['char_id'], null);
$type_id = validateUnsignedInteger($_GET['type_id']);

// Unkritisch
$charm = $_GET['charm'];

function anmelden($user, $charid, $type_id){
	$tournament_type = getTournamentType($type_id);
	$filter_sql = getTournamentCharExclusionSQL($tournament_type, 'c');
	$anzahl = $tournament_type['competitors'];
	
	if ($tournament_type['gain'] != TOURNAMENT_GAIN_ANMELDUNG) {
		displayErrorMessage(NULL,'Es ist ein Fehler beim Anmelden aufgetreten!!','<a href="index.php">weiter...</a>');
	}
	
	if(!is_numeric($charid)){ // Ist Charid wirklich eine Zahl????
		displayErrorMessage(NULL,'Charid ist nicht numerisch!!','<a href="index.php">weiter...</a>');
		return;
	}
	// Es wird angenommen, dass $user schon sicher ist, weil das in der config.inc.php ausgelesen wird
	
	$fee = $tournament_type['entrance_fee'];

	if(getUserAvailableMoney($user['id']) < $fee) {
		displayErrorMessage(NULL,'Nicht genug Geld um am Turnier teilzunehmen!','<a href="index.php">weiter...</a>');
		return;
	}

	$sear_a = mysqli_num_rows(db_query('SELECT tr.id FROM tournament_registration tr inner join chars as c ON tr.charakter = c.id WHERE tr.type='.$type_id.' AND '.$filter_sql));
	if($sear_a >= $anzahl) {
		displayErrorMessage(NULL,'Turnier voll! Es k&uuml;nnen maximal nur '.$anzahl.' Chars am Turnier teilnehmen','<a href="index.php?as=turnier&type_id='.$type_id.'">weiter...</a>');
		return;
	}
	
	// sehr speziell und deswegen nicht getChar();
	$PRUEF = mysqli_num_rows(db_query('SELECT id FROM chars c WHERE id=\''.$charid.'\' AND '.$filter_sql.' AND besitzer=\''.$user['id'].'\' LIMIT 1'));
	if(!$PRUEF) {
		displayErrorMessage(NULL,'Charakter erf&uuml;llt die Bestimungen nicht!','<a href="index.php?as=turnier&type_id='.$type_id.'">weiter...</a>');
		return;
	}

	$sear = mysqli_fetch_assoc(db_query('SELECT id FROM tournament_registration WHERE besitzer=\''.$user['id'].'\' and type = \''.$type_id.'\' LIMIT 1'));
	if($sear['id']) {
		displayErrorMessage(NULL,'Es darf nur einer deiner Chars am Turnier teilnehmen','<a href="index.php?as=turnier&type_id='.$type_id.'">weiter...</a>');
		return;
	}

	$sql = 'INSERT tournament_registration SET charakter='.$charid.', besitzer='.$user['id'].', type = '.$type_id;
//	echo $sql;
	db_query($sql);
	db_query('UPDATE chars SET status=\'Turnier\' WHERE id='.$charid);
	db_query('UPDATE user SET geld= '.($user['geld']-$fee).' WHERE id='.$user['id']);

	displayErrorMessage('&Auml;nderungen &uuml;bernommen','Charakter erfolgreich beim Turnier angemeldet','<a href="index.php?as=turnier&type_id='.$type_id.'">weiter...</a>');
	return;
}

function abmelden($user_ida, $char_id, $type_id){
	db_query('DELETE FROM tournament_registration WHERE charakter=\''.$char_id.'\' AND besitzer=\''.$user_ida['id'].'\'');
	db_query('UPDATE chars SET status=\'Frei\' WHERE id=\''.$char_id.'\' AND besitzer=\''.$user_ida['id'].'\' AND status=\'Turnier\' LIMIT 1');
	displayErrorMessage('&Auml;nderungen &uuml;bernommen','Charakter erfolgreich beim Turnier abgemeldet','<a href="index.php?as=turnier&type_id='.$type_id.'">weiter...</a>');
	return;
}


function displayDefault($user, $type_id){
	$tournament_type = getTournamentType($type_id);
	$filter_sql = getTournamentCharExclusionSQL($tournament_type);
	
	// sehr speziell und deswegen nicht getChar();
	$sql = 'SELECT id, name FROM chars WHERE besitzer='.$user['id'].' AND '.$filter_sql;
	$char = db_query($sql);
	if(!$char)
		echo $sql;

?>
<script type="text/javascript">
<!--
function disablebutton()
{
if(document.forms[0].elements['char_id'].value!=-1) document.forms[0].elements['submit'].disabled=false;
else document.forms[0].elements['submit'].disabled=true;
}
-->
</script>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
	<input type="hidden" name="as" value="turnier">
	<input type="hidden" name="charm" value="1">
	<input type="hidden" name="type_id" value="<?php echo $type_id; ?>">
<table cellpadding="0" cellspacing="0" width="100%" height="25">
	<!-- MSTableType="layout" -->
	<tr>
		<th colspan="2" align="center"><?php echo $tournament_type['name']; ?> Anmeldung</th>
	</tr>
	<tr>
		<th align="center">Anmeldegeb&uuml;hr</th>
		<td valign="top">
			<?php echo $tournament_type['entrance_fee'];?>
		</td>
	</tr>
	<tr>
		<th align="center">Charakter</th>
		<td valign="top">
			<select id="input" name="char_id" onchange="disablebutton()">
				<option value="-1">K&auml;mpfer w&auml;hlen</option>
<?php
while($row = mysqli_fetch_assoc($char)) {
	$row['status'] = getStatus($row['id']);
	if($row['status'] != 'Frei') { $row['id'] = -1;  }
	echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
}

?>
</select>
</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><br><input id="input" name="submit" disabled="disabled" type="submit" value="Am Turnier anmelden"></td>
	</tr>
</table><hr id="hrc">
<?php
$char_ag = db_query('SELECT c.id as charid, c.name, t.charakter, u.nickname, u.id as userid FROM tournament_registration t LEFT JOIN chars c ON(t.charakter=c.id) LEFT JOIN user u ON(t.besitzer=u.id) WHERE t.type='.$type_id);
?>
<table cellpadding="0" cellspacing="0" width="388" height="0">
	<!-- MSTableType="layout" -->
	<tr>
		<td valign="top" colspan="3" height="25">
		<p align="center"><b>Turnier Member</b></td>
	</tr>
	<tr>
		<th valign="middle" align="center">Charakter</th>
		<th valign="middle" align="center">Trainer</th>
		<th valign="middle" align="center">Aktion</td>
	</tr>
<?php
while($row2 = mysqli_fetch_assoc($char_ag)) {
	if($row2['userid'] == $user['id']) {
		$alo = '<a href="index.php?as=turnier&charm=2&char_id='.$row2['charakter'].'&type_id='.$type_id.'">Abmelden</a>';
	} else {
		$alo = '&nbsp;';
	}
?>
	<tr>
		<td align="left"><?php echo displayCharLink($row2['charid'],$row2['name']); ?></td>
		<td align="left"><?php echo displayUserLink($row2['userid'],$row2['nickname']); ?></td>
		<td align="center"><?php echo $alo; ?></td>
	</tr>
<?php
}
?>
</table>
<?php
}

if($charm == 1){
	anmelden($user_ida,$char_id,$type_id);
} else if($charm == 2){
	abmelden($user_ida, $char_id,$type_id);
} else{
	displayDefault($user_ida, $type_id);
}



?>
