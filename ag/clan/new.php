<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once (ROOT_PATH . '/include/config.inc.php');
include_once (ROOT_PATH . '/include/designfunctions.inc.php');
include_once (ROOT_PATH . '/include/fehlerausgabe.inc.php');
include_once (ROOT_PATH . '/include/parse.inc.php');
include_once (ROOT_PATH . '/include/clan.inc.php');
include_once (ROOT_PATH . '/include/user.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$clan_name = validateName($_GET['clan_name']);
$clan_for = validateString($_GET['clan_for']);
$clan_back = validateString($_GET['clan_back']);
$clan_info = validateString($_GET['clan_info']);

// Unkritisch
$user = $user_ida;
$charm = $_GET['charm'];

//Defines
defineIfNotDefined("CLAN_CREATION_FEE", 20000);

if ($charm == 1) {
	$error = null;

	if (!$clan_for AND !$clan_back) {
		$error .= 'Sie m&uuml;ssen ein Clanzeichen haben.<br>';
	}

	if(strlen($clan_for.$clan_back) > MAX_CHARS_CLANSIGN) {
		$error .= 'Clanzeichen sind ungültig: Summe aus beiden Teilen darf nicht größer als '.MAX_CHARS_CLANSIGN.' Zeichen sein!.<br>';
	}

	if (!$clan_name) {
		$error .= 'Sie m&uuml;ssen einen Clannamen haben.<br>';
	}

	if ($user['clan'] != 0) {
		$error .= 'Sie haben schon einen Clan.<br>';
	}

	$user_geld = getRelevantMoney($user['id']);
	if ($user_geld < CLAN_CREATION_FEE) {
		$error .= 'Sie haben nicht genug Geld um einen Clan gr&uuml;nden zu k&ouml;nnen.<br>';
	}

	$sql = 'SELECT id, clanname FROM clan WHERE clanname = \'' .$clan_name. '\'';
	//	echo $sql.'<br>';
	$dup = mysqli_fetch_assoc(db_query($sql));

	if($dup) {
		// we have a clan that has the same name!!
		$error .= 'Es gibt schon einen Clan mit dem Namen '.$clan_name.'.';
	}

	if($error == null) {
		$sql = 'INSERT into clan(clanname,clanz_pre,clanz_suff,info, leader) values(\''.$clan_name.'\', \''.$clan_for.'\', \''.$clan_back.'\', \''.$clan_info.'\', '.$user_ida['id'].')';
		//	echo $sql.'<br>';
		db_query($sql);
		if(db_affected_rows() > 0){ // Erstellen des Clans geglueckt
			$c_id = mysqli_fetch_assoc(db_query('Select * from clan where clanname = \''.$clan_name.'\''));
			db_query('UPDATE user SET clan='.$c_id['id'].', geld = geld - '.CLAN_CREATION_FEE.' WHERE id='.$user_ida['id']);
			displayErrorMessage(NULL, 'Clan erfolgreich Erstellt.', '<a href="index.php?as=clan/clan_info">weiter...</a>');
		} else{
			$sql = 'Select * from clan where clanname = \''.$clan_name.'\'';
			$c_id = mysqli_fetch_assoc(db_query($sql));
			if($c_id){
				$error = 'Clanname besteht schon!';
			} else{
				$error = 'Insert failed!';
			}
			displayErrorMessage(NULL, 'Clan erstellen fehlgeschlagen! ('.$error.')', displayHistoryBackLink());
		}
	} else {
		displayErrorMessage(NULL, $error , displayHistoryBackLink());
	}
} else {
	?>
<form action="index.php" method="GET">
	<input type="hidden" name="as" value="clan/new" /> <input type="hidden"
		name="charm" value="1" />

	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<th height="25" valign="top" width="557" colspan="2" align="center">Clan
				gr&uuml;nden</th>
		</tr>
		<tr>
			<th height="25" width="162" valign="top" align="center">Gründungsgebühr</th>
			<td height="25" width="395" valign="top">&nbsp;<?php echo CLAN_CREATION_FEE;?>
			</td>
		</tr>
		<tr>
			<th height="25" width="162" valign="top" align="center">Clanname</th>
			<td height="25" width="395" valign="top">&nbsp;<input
				name="clan_name"></td>
		</tr>
		<tr>
			<th height="25" width="162" valign="top" align="center">Clanzeichen
				vorn</th>
			<td height="25" width="395" valign="top">&nbsp;<input name="clan_for"
				size="5" maxlength="10"></td>
		</tr>
		<tr>
			<th height="25" width="162" valign="top" align="center">Clanzeichen
				hinten</th>
			<td height="25" width="395" valign="top">&nbsp;<input
				name="clan_back" size="5" maxlength="10"></td>
		</tr>
		<tr>
			<th height="25" width="162" valign="top" align="center">Clan Info</th>
			<td height="25" width="395" valign="top">&nbsp;<textarea rows="5"
					cols="30" name="clan_info"></textarea></td>
		</tr>
		<tr>
			<td height="25" width="162" align="center" colspan="2"><input
				type="submit" value="Clan gr&uuml;nden"></td>
		</tr>
	</table>
</form>
	<?php
}
?>