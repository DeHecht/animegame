<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once (ROOT_PATH . '/include/parse.inc.php');
include_once (ROOT_PATH . '/include/messagefunctions.inc.php');
// GET-Section
// Kritisch (SQL-Injections)
$text = validateString($_GET['text']);
$username = validateName($_GET['username'], NULL);

// Unkritisch
$action = $_GET['action'];

function handleInvitation($user, $username, $clan){
	if($clan['leader'] != $user['id'] && $clan['co_leader'] != $user['id']){
		displayErrorMessage(NULL, 'Nur der Leader, bzw. der Co-Leader kann neue Member einladen!');
		return false;
	}

	if($user['clan'] == NULL){
		displayErrorMessage(NULL, 'Fehler! Nutzer existiert nicht!');
		return false;
	}
	$row = mysqli_fetch_assoc(db_query('SELECT * FROM user where nickname = \''. $username.'\''));
	$userid = $row['id'];
	if($row['clan'] == $user['clan']){
		displayErrorMessage(NULL, 'Fehler! Nutzer ist schon in deinem Clan!', displayHistoryBackLink());
		return false;
	}

	if($row){
		$sql = 'INSERT INTO user_clan_invitations(userid, clanid, valid) values('.$userid.','.$user['clan'].',TIMESTAMPADD(DAY, 5, now()))';
//		echo $sql.'<br>';
		db_query($sql);
		if(db_affected_rows() <= 0){ // Fehler?
			$sql = 'UPDATE user_clan_invitations SET valid = TIMESTAMPADD(DAY, 5, now()) where userid = '.$userid.' and clanid = '.$user['clan'];
//			echo $sql.'<br>';
			db_query($sql);
			if(db_affected_rows() <= 0){ // Fehler?
				displayErrorMessage(NULL, 'Fehler! Konnte Nutzer nicht einladen!', displayHistoryBackLink());
				return false;
			} else{
				displayErrorMessage('','Nutzereinladung aufgefrischt!', '<a href="index.php?as=clan/post">weiter</a>');
				sendMessage($user['id'], $userid, 'Einladung in Clan', 'Die Einladung des Clans '.$clan['clanname'].' wurde aufgefrischt! Klicke auf "Clan beitreten" um die Einladung anzunehmen!');
			}
		} else{
			displayErrorMessage('','Nutzer erfolgreich eingeladen!', '<a href="index.php?as=clan/post">weiter</a>');
			sendMessage($user['id'], $userid, 'Einladung in Clan', 'Du wurdest eingeladen dich dem Clan '.$clan['clanname'].' anzuschliessen! Klicke auf "Clan beitreten" um die Einladung anzunehmen!');
		}
	}
	return true;
}

function sendClanNewsletter($user, $clan, $text){
	if($clan == NULL){
		return;
	}
	$sql = 'SELECT id FROM user WHERE clan='.$clan['id'];
	$clans = db_query($sql);
	while($row = mysqli_fetch_assoc($clans)) {
		sendMessage($user['id'], $row['id'], $clan['clanname'].' Nachricht', $text);
	}
	displayErrorMessage(NULL,'Nachricht erfolgreich verschickt', '<a href="index.php?as=clan/post">weiter...</a>');
}

function revertInvitation($user, $clan, $userid){
	if($clan['leader'] != $user['id'] && $clan['co_leader'] != $user['id']){
		displayErrorMessage(NULL, 'Nur der Leader, bzw. der Co-Leader kann Einladungen zur&uuml;ckziehen!!', displayHistoryBackLink());
		return false;
	}
	db_query('DELETE FROM user_clan_invitations WHERE clan = '.$clan['id'].' AND userid = '.$userid);
	if(db_affected_rows() > 0){
		displayErrorMessage('Zur&uuml;ckgezogen','Du hast die Einladung erfolgreich zur&uuml;ckgezogen!','<a href="index.php?as=clan/post">weiter</a>');
	} else{
		displayErrorMessage(NULL,'Einladung konnte nicht zur&uuml;ckgezogen werden!',displayHistoryBackLink());
		return false;
	}
	return true;
}


$clan = mysqli_fetch_assoc(db_query('SELECT * from clan where id = '.$user_ida['clan']));

if($action == 'newsletter') {
	sendClanNewsletter($user_ida, $clan, $text);
} else if($action == 'invite'){
	handleInvitation($user_ida, $username, $clan);
} else if($action == 'reject'){
	revertInvitation($user_ida, $username, $clan);
} else {
	?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
	<input type="hidden" name="as" value="clan/post">
	<input type="hidden" name="action" value="invite">
	<table cellpadding="0" cellspacing="0" width="100%" height="69">
		<tr>
			<th height="18" width="603" class="content">
				Nutzer in Clan einladen!
			</th>
		</tr>
		<tr>
			<td height="25" align="center">&nbsp;<input class="input" name="username"/></td>
		</tr>
		<tr>
			<td height="25" valign="top" colspan="2" align="center">&nbsp;<input class="input" type="submit" value="abschicken"></td>
		</tr>
	</table>
</form>

	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<th height="30" width="585" colspan="5" class="content">Versendete Claneinladungen</th>
		</tr>
		<tr>
			<th height="30" class="content">Username</th>
			<th height="30" colspan="2" class="content">Deadline</th>
			<th height="30" colspan="2" class="content">Aktion</th>
<?php
	$sql = 'SELECT * FROM user_clan_invitations WHERE clanid = '.$user_ida['clan'].' and valid > now();';
	$qry = db_query($sql);
	while($row = mysqli_fetch_assoc($qry)){
?>
		<tr>
			<td height="30" align="center" class="content"><?php echo displayUserLinkById($row['userid']); ?></td>
			<td height="30" align="center" colspan="2" class="content"><?php echo $row['valid']; ?></td>
			<td height="30" align="center" colspan="2" class="content"><a href="index.php?as=clan/post&action=reject&userid=<?php echo $row['userid']; ?>">zur&uuml;ckziehen</a></td>
		</tr>
<?php
	}
?>
</table>
<?php
}
?>