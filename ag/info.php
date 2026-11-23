<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once('path.inc.php'); // get the path ;)
include_once (ROOT_PATH . '/include/config.inc.php');
include_once (ROOT_PATH . '/include/designfunctions.inc.php');
include_once (ROOT_PATH . '/include/char.inc.php');
include_once (ROOT_PATH . '/include/clan.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');



// GET-Section
// Kritisch (SQL-Injections)
$char_id = validateUnsignedInteger($_GET['char_id'], null);
$item_id = validateUnsignedInteger($_GET['item_id'], null);
$user_id = validateUnsignedInteger($_GET['user_id'], null);
$userage = validateName($_GET['userage']);
$group = validateName($_GET['group']);

// Unkritisch
$charm = $_GET['charm'];


function isNickChanged($user){
	$row = mysqli_fetch_assoc(db_query('Select count(*) as anzahl from user_rename where userid = '.$user['id']));
	return $row['anzahl'] != 0;
}


// Etwas anderer Aufbau
function display1($char_id){
	if(!is_numeric($char_id)){
		return;
	}
	$char_a = getChar($char_id);

?>
<table cellpadding="0" cellspacing="0" width="100%" height="100">
	<tr>
		<th valign="middle" align="center"><?php echo $char_a['name']; ?></th>
		<td height="25"></td>
	</tr>
	<tr>
		<td valign="middle" align="center">
			<img border="1" src="<?php echo $char_a['bild']; ?>" width="75" height="75">
		</td>
		<th height="90" valign="middle" align="center">
			<a href="index.php?as=wanted&charm=1&char_id=<?php echo $char_id; ?>">Dead List</a>
		</th>
	</tr>
	<tr>
		<th valign="center"  align="center">Siege</th>
		<td height="25">&nbsp;<?php echo $char_a['siege']; ?></td>
	</tr>
	<tr>
		<th valign="center" align="center">Liga Siege</th>
		<td height="25">&nbsp;<?php echo $char_a['liga_siege']; ?></td>
	</tr>
	<tr>
		<th width="142" valign="center" align="center">Niederlagen</td>
		<td height="25" width="429">&nbsp;<?php echo $char_a['niederlagen']; ?></td>
	</tr>
	<tr>
		<th width="142" valign="center" align="center">Liga Niederlagen</th>
		<td height="25" width="429">&nbsp;<?php echo $char_a['liga_niederlagen']; ?></td>
	</tr>
	<tr>
		<th width="142" valign="center" align="center">Rasse</th>
		<td height="25" width="429">&nbsp;<?php echo $char_a['rasse']; ?></td>
	</tr>
<?php
	if($char_a['fusion'] == "ja") {
?>
    <tr>
      <th width="192" height="25" align="center">Fusion Rasse</th>
      <td width="392" height="25">&nbsp;<?php echo $char_a['fusion_rasse']; ?></td>
    </tr>

<?php
	}
?>
<?php
	// Erstmal deaktivieren
	if($char_a['type'] == "Onepiece" && $char_a['frucht'] != NULL && false) {
?>
    <tr>
      <th width="192" height="25" align="center">Teufels Frucht</th>
      <td width="392" height="25">&nbsp;<?php echo $char_a['frucht']; ?></td>
    </tr>
<?php
	}
?>
	<tr>
		<th width="142" valign="center" align="center">Level</th>
		<td height="25" width="429">&nbsp;<?php echo $char_a['level']; ?></td>
	</tr>
</table>
<?php

}


function display2($user_id, $userage, $user_ida){
	if($userage != NULL){
		$user_a = mysqli_fetch_assoc(db_query('SELECT * FROM user WHERE nickname=\''.$userage.'\''));
	} else{
		$user_a = mysqli_fetch_assoc(db_query('SELECT * FROM user WHERE id='.$user_id));
	}
	if(!is_numeric($user_id)) {
		$user_id = $user_a['id'];
	}

?>
<table cellpadding="0" cellspacing="0" width="100%" height="51">

	<tr>
		<td valign="top" colspan="2" height="32">
		<p align="center"><b><?php echo "$user_a[nickname]s Info"; ?> </b></td>
	</tr>
	<tr>
		<td valign="top" height="25" width="195" align="center"><b>Homepage</b></td>
		<td valign="top" height="25" width="191">
		&nbsp;<?php echo $user_a['homepage']; ?></td>
	</tr>
<tr>
      <th valign="top" height="25" width="195" align="center">Clan</th>
            <td valign="top" height="25" width="191"><?php
            	if($user_a['clan'] == NULL){
	            	echo 'Keiner';
		} else{
			$clan = mysqli_fetch_assoc(db_query('SELECT * FROM clan WHERE id = '.$user_a['clan']));
	            	 echo displayClanLink ($clan['id'], $clan['clanname']) ;
		}

		// Now could it be that the user is in an other clan and we may invite him ;), check if current user is a leader!
		$qry = db_query('Select id from clan where leader = ' . $user_ida['id'] . ' or co_leader = ' . $user_ida['id']);
		$clan = mysqli_fetch_assoc($qry);
		// is there data available and if yes does the other user have the same clan?
		if($clan && $clan['id'] !== $user_a['clan']) {
			// check if we already invited him. Then renew invitation ->
			$qry = db_query('Select count(*) as anzahl from user_clan_invitations where clanid = '.$clan['id'].' AND userid = ' . $user_id);
			$row = mysqli_fetch_assoc($qry);

			if ($row['anzahl'] > 0) {
				$action = 'Einladung erneuern';
			} else {
				$action = 'einladen';
			}
			// no ?? Then display the option to invite this user ;)
			echo ' <a href="index.php?as=info&charm=3&user_id='.$user_id.'">('.$action.')</a>';
		}
            	 ?></td>
	</tr>
	<tr>
		<td valign="top" height="25" width="195" align="center"><b>Geschlecht</b></td>
		<td valign="top" height="25" width="191">
		&nbsp;<?php echo $user_a['geschlecht']; ?></td>
	</tr>
<?php
	if(isNickChanged($user_a)){
		$nick_history = array();
		$nick_qry = db_query('Select * from user_rename where userid = '.$user_a['id'].' ORDER BY datum asc');
		while($row = mysqli_fetch_assoc($nick_qry)){
			$nick_history[] = $row['datum'].': Von "'.$row['pre_name'].'" zu "'.$row['post_name'].'"';
		}
		?>
	<tr>
		<td valign="top" height="25" width="195" align="center"><b>Nick-History</b></td>
		<td valign="top" height="25" width="191"><?php echo join('<br>',$nick_history) ?></td>
	</tr>
		<?php
	}
?>
<?php
$chars = getCharsOfUser($user_a['id']);
$x=1;
foreach($chars as $row){
?>
		<tr>
		<td valign="top" height="25" width="195" align="center"><b><?php echo "Charakter $x"; ?></b></td>
		<td valign="top" height="25" width="191">
		&nbsp;<?php echo displayCharLink($row['id'], $row['name']); ?></td>
	</tr>
<?php
$x++;
}
?>
	<tr><td colspan="2" align="center"><a href="index.php?as=nachricht&charm=1&besitzer=<?php echo $user_a['nickname']; ?>">Nachricht schreiben</a></td></tr>
</table>
<?php
}


function display3($tablename, $itemid){
	if(!is_numeric($itemid)){
		return;
	}
	$tabletogo = '';
	if($tablename == 'ware'){
		$tabletogo = 'item';
	} else if($tablename == 'sp_ware'){
		$tabletogo = 'sp_item';
	} else if($tablename == 'wochen_ware'){
		$tabletogo = 'wochen_markt';
	} else{
		return;
	}

$item_a = mysqli_fetch_assoc(db_query('SELECT * FROM '.$tabletogo.' WHERE id='.$itemid));
?>
<table cellpadding="0" cellspacing="0" width="100%" height="51">
	<tr>
		<th valign="top" height="32" align="center"><?php echo $item_a['name'].$item_a['item']; ?></th>
	</tr>
	<tr>
		<td valign="top" height="25" width="191"> <?php echo $item_a['info']; ?></td>
	</tr>
	<tr>
		<th valign="top" height="32" align="center"><?php echo displayAuktionsLink($tablename,$item_a['name'],$item_a['item'],NULL,NULL,NULL); ?></th>
	</tr>
	<tr>
		<td></td>
	</tr>
</table>
<?php
}


if($charm == 1) {
	display1($char_id);
} else if($charm == 2){
	display3($group,$item_id);
} elseif ($charm == 3) {
	$errorMsg = inviteUser($user_ida, $user_id);
	if($errorMsg !== NULL) {
		echo displayErrorMessage(NULL, $errorMsg, displayHistoryBackLink());
		$displayMore = false;
	} else {
		displayErrorMessage('','Du hast die Einladung erfolgreich verschickt!', '<a href="index.php?as=info&user_id='.$user_id.'">weiter</a>');
		$displayMore = false;
	}
} else if($user_id != NULL || $userage != NULL){
	display2($user_id, $userage, $user_ida);
}

?>
