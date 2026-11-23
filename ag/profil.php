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
include_once(ROOT_PATH.'/include/user.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$icq = validateString($_GET['icq']);
$new_pw = validateString($_GET['new_pw']);
$new_nick = validateName($_GET['new_nick']);
// wird später benötigt
$new_nick2 = validateName($_GET['new_nick2']);
$homepage = validateString($_GET['homepage']);
$chat = validateString($_GET['chat']);
$ads = validateString($_GET['ads']);
$acc_delete = validateString($_GET['acc_delete']);
$alt_pw = validateString($_GET['alt_pw']);
$hide_avatars = validateInteger($_GET['hide_avatars']);

// Unkritisch
$charm = $_GET['charm'];

if($_GET['new_nick'] == ''){
	$new_nick = '';
}

if((!is_null($_GET['new_nick']) && is_null($new_nick)) || ($new_nick != $_GET['new_nick'])){
	displayErrorMessage('Name ung&uuml;tig!', 'Der Name ist leider ung&uuml;ltig!', displayHistoryBackLink());
	exit;
}

function isNickChanged($user){
	$sql = 'Select count(*) as anzahl from user_rename where userid = '.$user['id'].' and wunsch = 0 and datum >= TIMESTAMPADD(MONTH, -6, now())';
	$row = mysqli_fetch_assoc(db_query($sql));
	return $row['anzahl'] != 0;
}


function changeProfil($user, $usergroups, $new_pw, $new_nick, $new_nick2, $alt_pw, $homepage, $icq, $chat, $ads, $acc_delete, $hide_avatars){
	if(!is_numeric($chat)){
		displayErrorMessage(NULL,'Chat-Feld inkorrekt ausgefuellt!','');
		return;
	}
	if(!is_numeric($ads)){
		displayErrorMessage(NULL,'Werbung-Feld inkorrekt ausgefuellt!','');
		return;
	}
	if($new_nick == $new_nick2 && $new_nick !== ''){
		$raw_nick = $new_nick;
		if(!isNickChanged($user) && $new_nick != $user['nickname'] && $raw_nick == $new_nick) {
			$sql = 'Update user set nickname = \''.$new_nick.'\' where id = '.$user['id'];
			db_query($sql);
			if(db_affected_rows() > 0){
				$sql = 'Insert into user_rename(pre_name, post_name, datum, userid) values(\''.$user['nickname'].'\', \''.$new_nick.'\', now(), '.$user['id'].')';
				//					echo $sql;
				db_query($sql);
				displayErrorMessage('Nickname erfolgreich ge&auml;ndert','Ein neuer Login wird jedoch nun ben&ouml;tigt.','');
			} else{
				displayErrorMessage('Fehler','Nickname konnte nicht ge&auml;ndert werden.','');
			}
		} else if($new_nick != $raw_nick){
			displayErrorMessage('Neuer Nickname ung&uuml;ltig!','Es sind Sonderzeichen erlaubt!','');
		}
	} else if($new_nick !== ''){
		displayErrorMessage('Neuer Nickname ung&uuml;ltig!','Es sind Sonderzeichen erlaubt!','');
	}

	if($new_pw) {
		if($new_pw == "") {
			displayErrorMessage(NULL,'Neues Passwort nicht angegeben!','');
			return;
		}
		$alt_pw = encryptPassword($alt_pw);
		if($alt_pw != $user['passwort']) {
			displayErrorMessage(NULL,'Passwort falsch!','');
			return;
		}
		setPassword($user['nickname'], $new_pw);
	}

	if($chat == 1) {
		if(!isUserInGroup($usergroups, AKTIV_CHAT)) {
			addUserToGroup($user['nickname'], AKTIV_CHAT);
		}
	} else {
		if(isUserInGroup($usergroups, AKTIV_CHAT)) {
			removeUserFromGroup($user['nickname'], AKTIV_CHAT);
		}
	}

	if($ads == 1) {
		if(!isUserInGroup($usergroups, WERBUNG_AN)) {
			addUserToGroup($user['nickname'], WERBUNG_AN);
		}
	} else {
		if(isUserInGroup($usergroups, WERBUNG_AN)) {
			removeUserFromGroup($user['nickname'], WERBUNG_AN);
		}
	}

	if($acc_delete == 1) {
		if(!isUserInGroup($usergroups, AKTIV_LOESCHSCHUTZ)) {
			addUserToGroup($user['nickname'], AKTIV_LOESCHSCHUTZ);
			removeUserFromGroup($user['nickname'], INAKTIV_LOESCHSCHUTZ);
		}
	} else if($acc_delete == 0) {
		if(isUserInGroup($usergroups, AKTIV_LOESCHSCHUTZ)) {
			removeUserFromGroup($user['nickname'], AKTIV_LOESCHSCHUTZ);
			addUserToGroup($user['nickname'], INAKTIV_LOESCHSCHUTZ);
		}
	}
	
	setUserMetaDataEntry($user['id'], 'hide_avatars', $hide_avatars);

	db_query('UPDATE user SET homepage=\''.$homepage.'\', icq=\''.$icq.'\' WHERE id=\''.$user['id'].'\'');
	displayErrorMessage('Profil erfolgreich ge&auml;ndert','Sollte das Passwort ge&auml;ndert worden sein, ist ein erneuter Login erforderlich.','');
	return mysqli_fetch_assoc(db_query('Select * from user where id = '.$user['id']));

}

function display($user, $usergroups){
	if($user['post']){
		$post_check = 'checked';
	}
	if($user['schnelllink'] == 2){
		$char_schnell = 'selected';
	}
	?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
	<input type="hidden" name="as" value="profil"> <input type="hidden"
		name="charm" value="1"> <input type="hidden" name="acc_delete"
		value="-1">
	<table cellpadding="0" cellspacing="0" width="100%" height="51">
		<tr>
			<th colspan="2" align="center">Ihr Profil</th>
		</tr>
		<tr>
			<th align="center">Nickname</th>
			<td><?php echo $user['nickname']; ?></td>
		</tr>
		<?php
		if(!isNickChanged($user)){
			?>
		<tr>
			<th align="center">Neuer Nickname</th>
			<td><input class="input" name="new_nick" value="" /></td>
		</tr>
		<tr>
			<th align="center">Neuer Nickname</th>
			<td><input class="input" name="new_nick2" value="" /></td>
		</tr>
		<?php
		}
		?>
		<tr>
			<th align="center">Altes Passwort</th>
			<td><input id="input" name="alt_pw" type="password" size="35">
			</td>
		</tr>
		<tr>
			<th align="center">Neues Passwort</th>
			<td><input id="input" name="new_pw" type="password" size="35">
			</td>
		</tr>
		<tr>
			<th align="center">Homepage</th>
			<td><input id="input" name="homepage" size="35"
				value="<?php echo $user['homepage']; ?>">
			</td>
		</tr>
		<tr>
			<th align="center">ICQ:</th>
			<td><input id="input" name="icq" size="35"
				value="<?php echo $user['icq']; ?>">
			</td>
		</tr>
		<tr>
			<th align="center">Werbegrad:</th>
			<td><?php
			if(!isUserInGroup($usergroups, WERBUNG_AN)) {
				$che1 = 'checked="checked"';
				$che2 = '';
			} else{
				$che1 = '';
				$che2 = 'checked="checked"';
			}
			?> moderat: <input type="radio" name="ads" value="0" <?php echo $che1; ?> /> <?php
			if(!(getLayerAdvertisement() == '' || getLayerAdvertisement() == null)){
			echo 'hardcore: <input type="radio" name="ads" value="1"'.$che2.'/>';
			}
			?>
			</td>
		</tr>
		<tr>
			<th align="center">Chat:</th>
			<td><?php
			if(isUserInGroup($usergroups, AKTIV_CHAT)) {
				$che1 = 'checked="checked"';
				$che2 = '';
			} else{
				$che1 = '';
				$che2 = 'checked="checked"';
			}
			?> an: <input type="radio" name="chat" value="1" <?php echo $che1; ?> />
				aus: <input type="radio" name="chat" value="0" <?php echo $che2; ?> />
			</td>
		</tr>
		<tr>
			<th align="center">Avatare anzeigen:</th>
			<td><?php
			$data = getUserMetaData($user['id']);
			if($data['hide_avatars'] == 1) {
				$che1 = '';
				$che2 = 'checked="checked"';
			} else{
				$che1 = 'checked="checked"';
				$che2 = '';
			}
			?> an: <input type="radio" name="hide_avatars" value="0" <?php echo $che1; ?> />
				aus: <input type="radio" name="hide_avatars" value="1" <?php echo $che2; ?> />
			</td>
		</tr>
		<tr>
			<th align="center">Account L&ouml;schschutz</th>
			<td><?php
			$disabled = '';
			$che1 = '';
			$che2 = '';

			if(isUserInGroup($usergroups, INAKTIV_LOESCHSCHUTZ)){
				$che1 = '';
				$che2 = 'checked="checked"';
			} else if (isUserInGroup($usergroups, AKTIV_LOESCHSCHUTZ)){
				$che1 = 'checked="checked"';
				$che2 = '';
			} else {
				$disabled = 'disabled';
			}
			?> an: <input type="radio" name="acc_delete" value="1"
			<?php echo $che1.' '.$disabled; ?> /> aus: <input type="radio"
				name="acc_delete" value="0" <?php echo $che2.' '.$disabled; ?> />
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input id="input" size="15" type="submit" value="&auml;ndern">
			</td>
		</tr>

	</table>
</form>
			<?php
}

if(isset($user_ida['id'])) {
	if($charm == '1'){
		$user_ida = changeProfil($user_ida, $usergroups, $new_pw, $new_nick, $new_nick2, $alt_pw, $homepage, $icq, $chat, $ads, $acc_delete, $hide_avatars);
	}
	$usergroups = getUserGroups($user_ida['nickname']);
	display($user_ida, $usergroups);
}

?>
