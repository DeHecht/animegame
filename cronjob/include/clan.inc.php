<?php
/*
 *
 * @copyright (c) 2011 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once (ROOT_PATH . '/include/defines.inc.php');
include_once (ROOT_PATH . '/include/clan_fights.inc.php');
include_once (ROOT_PATH . '/include/exp.inc.php');
include_once (ROOT_PATH . '/include/messagefunctions.inc.php');
include_once (ROOT_PATH . '/include/designfunctions.inc.php');

defineIfNotDefined('CLAN_LVL_NEWSLETTER', 1);
defineIfNotDefined('CLAN_LVL_CHAT', 2);
defineIfNotDefined('CLAN_LVL_BACKGROUND', 3);

defineIfNotDefined('MAX_CHARS_CLANSIGN', 8);
$GLOBALS['clan_buffered_instances'] = array ();


function getClan($clanid, $buffer = TRUE) {
	if(!is_numeric($clanid) || $clanid === NULL) { return NULL; }
	if($buffer && isset($GLOBALS['clan_buffered_instances'][$clanid])) {
		return $GLOBALS['clan_buffered_instances'][$clanid]; // okay, we already buffered the clan :)
	}

	$clan = mysqli_fetch_assoc(db_query('SELECT * FROM clan WHERE id = '. $clanid));

	// now calculate the member, max_exp fields :D

	if($clan) {
		$GLOBALS['clan_buffered_instances'][$clanid] = $clan;
		return $clan;
	} else {
		return NULL;
	}
}


function getClanMembersCount($clanid) {
	if(!is_numeric($clanid)) { return NULL; }
	$row = mysqli_fetch_assoc(db_query('SELECT count(id) as amount FROM user WHERE clan = '. $clanid));
	return $row['amount'];
}

function getClanMemberBonusByLevel($level) {
	switch($level) {
		case 1:
		case 2:
		case 3:
			return 0;
		case 4:
		case 5:
			return 1;
		case 6:
		case 7:
			return 2;
		case 8:
		case 9:
			return 3;
		case 10:
		case 11:
		case 12:
		case 13:
		case 14:
		case 15:
		case 16:
		case 17:
		default:
			return 4;
	}
}


function getMoneyBonusByLevel($level) {
	switch($level) {
		case 12:
		case 14:
		case 16:
		case 17:
			return 100000;
		default:
			return 0;
	}
}



function getMaximumMembers($clanid) {
	// okay, this is a more tricky calculation as this requires the information (are leader and co_leader the same person??)
	// the second parameter is the clan level. Each 2 levels (where the maximum clan level is about 20) one more member can
	// be invited :)

	$clan = getClan($clanid);
	if($clan === NULL){ return 0; }
	$base = 8;
	$members_by_level = getClanMemberBonusByLevel($clan['level']);
	$malus = 0;
	if($clan['co_leader'] == NULL || $clan['co_leader'] == $clan['leader']) {
		$malus = 1;
	}
	return $base + $members_by_level - $malus;
}

function getRequiredClanExp($level) {
	//return pow(2, $level) * 10;
	$level = $level + 1;
	// 12,5*(neuesLevel)^2-22,5*(neues Level)+10
	// 10-22,5*(neues Level)+12,5*(neuesLevel)^2
	$value = 12.5 * pow($level, 2) - 22.5 * $level + 5;
	$value = beautifyNumericValue($value, 10);
	return $value;
}

function revokeInvitation($user, $userid, $clan = NULL){
	if($user['clan'] === NULL) {
		return 'Du bist in keinem Clan!';
	}

	if($clan === NULL) {
		$qry = db_query('Select * from clan where id = ' . $user['clan']);
		$clan = mysqli_fetch_assoc($qry);
	}

	if($clan['leader'] != $user['id'] && $clan['co_leader'] != $user['id']){
		return 'Nur der Leader, bzw. der Co-Leader kann Einladungen zur&uuml;ckziehen!!';
	}

	$sql = 'DELETE FROM user_clan_invitations WHERE clanid = '.$clan['id'].' AND userid = '.$userid;
	//	echo $sql . '<br>';
	db_query($sql);
	if(db_affected_rows() > 0){
		return NULL;
	} else{
		return 'Einladung konnte nicht zur&uuml;ckgezogen werden!';
	}
}

/**
 *
 * Enter description here ...
 * @param array $user the user that wants to invite
 * @param int $userid the id of the user that should be invited
 * @param array $clan the optional clan array (if available)
 */
function inviteUser(array $user, $userid, array $clan = NULL){
	if($user['clan'] === NULL) {
		return 'Du bist in keinem Clan!';
	}

	if($clan === NULL) {
		$qry = db_query('Select * from clan where id = ' . $user['clan']);
		$clan = mysqli_fetch_assoc($qry);
	}

	if($clan['leader'] != $user['id'] && $clan['co_leader'] != $user['id']){
		return 'Nur der Leader, bzw. der Co-Leader kann neue Member einladen!';
	}

	$row = mysqli_fetch_assoc(db_query('SELECT * FROM user where id = \''. $userid.'\''));
	if(!row) {
		return 'Fehler! Nutzer exisitiert nicht!!';
	}
	if($row['clan'] == $user['clan']){
		return 'Fehler! Nutzer ist schon in deinem Clan!';
	}

	$sql = 'INSERT INTO user_clan_invitations(userid, clanid, valid) values('.$userid.','.$user['clan'].',TIMESTAMPADD(DAY, 5, now()))';
	//		echo $sql.'<br>';
	db_query($sql);
	if(db_affected_rows() <= 0){ // Fehler?
		$sql = 'UPDATE user_clan_invitations SET valid = TIMESTAMPADD(DAY, 5, now()) where userid = '.$userid.' and clanid = '.$user['clan'];
		//			echo $sql.'<br>';
		db_query($sql);
		if(db_affected_rows() <= 0){ // Fehler?
			return 'Fehler! Konnte Nutzer nicht einladen!';
		} else{
			sendMessage($user['nickname'], $userid, 'Einladung in Clan', 'Die Einladung des Clans '.$clan['clanname'].' wurde aufgefrischt! Klicke auf "Clan beitreten" um die Einladung anzunehmen!');
			return NULL;
		}
	} else{
		sendMessage($user['nickname'], $userid, 'Einladung in Clan', 'Du wurdest eingeladen dich dem Clan '.$clan['clanname'].' anzuschliessen! Klicke auf "Clan beitreten" um die Einladung anzunehmen!');
		return NULL;
	}
}

function fireMember($user, $member){
	$clan = getClan($user['clan']);
	if($member != $clan['leader'] && $member != $clan['co_leader'] && ($user['id'] == $clan['leader'] || $user['id'] == $clan['co_leader'] || $member == $user['id'])){
		$sql = 'Update user set clan = NULL where id = '.$member.' AND clan = '.$user['clan'];
		//		echo $sql.'<br>';
		db_query($sql);
		if(db_affected_rows() > 0){ // Soll nur ausgeführt werden, wenn member wirklich gekickt wurde!!
			$sql = 'Update chars set clan_train = NULL where besitzer = '.$member;
			db_query($sql);
			if($user['id'] != $member) {
				sendMessage($user['nickname'], $member, 'Clan rauswurf!', 'Du wurdest von '.$user['nickname'].' aus deinem Clan geworfen. Du wirst dir wohl einen neuen suchen m&uuml;ssen!');
			} else if($clan['exp'] == 0 && $clan['level'] == 1 && getClanMembersCount($user['clan']) == 0) {
				db_query('DELETE FROM clan WHERE id =' .$user['clan']);
			}

			// delete the participation in open clan fights! Thats tricky because the user itself is not stored in the database!!
			db_query('DELETE FROM clan_challenge_whitelist WHERE clan_id = ' .$clan['id'] . ' AND user_id = ' .$user['id']);
			db_query('DELETE FROM clan_challenge_participants WHERE clan_challenge_id in (SELECT clan_challenge_id FROM clan_challenges where calculated = FALSE) and char_id IN (Select id FROM chars where besitzer = '.$user['id'].')');
		}
	} else{
		echo displayErrorMessage(NULL, 'Du kannst den Member nicht feuern!', displayHistoryBackLink());
	}
}

/**
 *
 * Return all Clans with their id and name.
 *
 */
function getAllClans() {
	$qry = db_query('SELECT id,clanname FROM clan;');
	while ($result = mysqli_fetch_assoc($qry)) {
		$clans[] = $result;
	}
	return $clans;
}

function joinClan($user, $clanid, $check_invitation = true){
	// last chance to lock the clan if in the old situation it was having to few members ;)
	isClanLocked($clanid); // we only require the calculation not the result!

	$sql = 'SELECT * FROM user_clan_invitations WHERE userid = '.$user['id'].' and clanid = '.$clanid.' and valid > now();';
	$clan_user = mysqli_fetch_assoc(db_query($sql));

	if($clan_user || !$check_invitation){
		$clan = getClan($clanid);
		$members = getClanMembersCount($clanid);
		$members_max = getMaximumMembers($clan['id']);

		if($members >= $members_max){
			displayErrorMessage(NULL,'Konnte die Einladung des Clans nicht annehmen (Clan voll)!',displayHistoryBackLink());
			return;
		} else{
			$sql = 'UPDATE user SET clan = '.$clanid.' WHERE id = '.$user['id'];
			//			echo $sql.'<br>';
			db_query($sql);

			// unset the leader if the user is moving from one clan to another
			db_query('UPDATE clan SET leader = NULL WHERE leader = ' .$user['id'] . ' and id != ' .$clanid);
			db_query('UPDATE clan SET co_leader = NULL WHERE co_leader = ' .$user['id'] . ' and id != ' .$clanid);
			db_query('DELETE FROM clan_challenge_whitelist WHERE clan_id = ' .$clanid . ' AND user_id = ' .$user['id']);
			db_query('DELETE FROM clan_challenge_participants where char_id IN (SELECT id from chars where besitzer = '.$user['id'].') and clan_challenge_id IN (SELECT clan_challenge_id from clan_challenges WHERE calculated = 0)');

			if($check_invitation) {
				displayErrorMessage('Aufgenommen','Du wurdest erfolgreich in den Clan aufgenommen!','<a href="index.php?as=clan/clan_info">weiter</a>');
			}
		}
	} else{
		displayErrorMessage(NULL,'Konnte die Einladung des Clans nicht annehmen!',displayHistoryBackLink());
		return;
	}
	db_query('DELETE FROM user_clan_invitations WHERE userid = '.$user['id'].' and clanid = '.$clanid);
}

function denyClan($user, $clanid){
	db_query('DELETE FROM user_clan_invitations WHERE userid = '.$user['id'].' and clanid = '.$clanid);
	if(db_affected_rows() > 0){
		displayErrorMessage('Abgelehnt','Du hast die Einladung erfolgreich abgelehnt!','<a href="index.php?as=clan/add">weiter</a>');
	} else{
		displayErrorMessage(NULL,'Einladung des Clans konnte nicht abgelehnt werden!',displayHistoryBackLink());
	}
}

function deleteClan($userid) {
	$user = getUser($userid);
	$clanid = $user['clan'];
	$clan = getClan($clanid);

	if($userid != $clan['leader']) {
		return 'Du kannst den Clan nicht aufl&ouml;sen!';
	}

	// Okay notify the members :)

	$qry = db_query('SELECT id FROM user WHERE clan = '.$user['clan']);
	while($member = mysqli_fetch_assoc($qry)) {
		sendMessage($user['nickname'], $member['id'], 'Clan wurde gelöscht!', 'Dein Clan wurde von '.$user['nickname'].' gel&ouml;scht. Du wirst dir wohl einen neuen suchen m&uuml;ssen!');
	}

	// Check if this clan is level 0 and has 0 exp ...
	if($clan['exp'] == 0 && $clan['level'] == 1) {
		// really do delete the clan !!
		db_query('DELETE FROM clan where id = '.$user['clan']);
	} else {
		// cleanup the clan!
		$sql1 = 'UPDATE user SET clan = NULL WHERE clan = ' . $clanid;
		$sql2 = 'UPDATE clan SET leader = NULL, co_leader = NULL WHERE id = ' .$clanid;
		$sql3 = 'DELETE FROM clan_challenge_whitelist WHERE clan_id = ' .$clanid; // cleanup the whitelist

//		echo $sql1 . '<br>';
//		echo $sql2 . '<br>';

		db_query($sql1);
		db_query($sql2);
	}
	return NULL;

}

function conquerClan($clanid, $userid) {
	// It is possible to enter a clan that is empty :)
	$user = getUser($userid);
	$result = mysqli_fetch_row(db_query('SELECT count(*) FROM user WHERE clan = ' . $clanid));
	if($result[0] != 0) {
		return 'Der Clan ('.$clanid.') ist nicht leer!';
	}

	$sql = 'UPDATE clan SET leader = ' . $userid . ' WHERE leader is NULL AND id = '. $clanid;
	db_query($sql);

	$done = db_affected_rows() > 0;
	if(!$done) {
		return 'Der Clan ist schon &uuml;bernommen worden!';
	}

	// Cool we got em !! Now move to the new clan :)
	joinClan($user, $clanid, false);
	return NULL;
}

function setMeAsLeader($user){
	db_query('UPDATE clan set leader = '.$user['id'].' WHERE id = '.$user['clan'].' and leader IS null AND co_leader IS null');
	return db_affected_rows() > 0;

}

function isClanNewsletterAvailable($clanid) {
	$clan = getClan($clanid);
	return $clan['level'] >= CLAN_LVL_NEWSLETTER;
}

function isClanChatAvailable($clanid) {
	$clan = getClan($clanid);
	return $clan['level'] >= CLAN_LVL_CHAT;
}
function isClanBackgroundAvailable($clanid) {
	$clan = getClan($clanid);
	return $clan['level'] >= CLAN_LVL_BACKGROUND;
}

function sendClanNewsletter($user, $text){
	$clan = getClan($user['clan']);
	if(!isClanNewsletterAvailable($clan['id'])) {
		displayErrorMessage(NULL,'Dein Clan hat noch nicht das ausreichende Level um Clannachrichten zu senden!', '<a href="index.php?as=clan/clan_info">weiter...</a>');
		return;
	}
	$errors = FALSE;
	$sql = 'SELECT id FROM user WHERE clan='.$user['clan'];
	$clans = db_query($sql);
	while($row = mysqli_fetch_assoc($clans)) {
		$errors |= !sendMessage($user['nickname'], $row['id'], $clan['clanname'].' Nachricht', $text);
	}
	if($errors) {
		displayErrorMessage(NULL,'Beim Versenden sind Fehler aufgetreten', '<a href="index.php?as=clan/clan_info">weiter...</a>');
	} else {
		displayErrorMessage(NULL,'Nachricht erfolgreich verschickt', '<a href="index.php?as=clan/clan_info">weiter...</a>');
	}
}



function addClanExp($clan_id, $exp) {
	$clan = getClan($clan_id, false);
	if($clan['level'] == 18)
		return;
	$n_exp = $clan['exp'] + $exp;
	$n_level = $clan['level'];
	$req_exp = getRequiredClanExp($clan['level']);
	if($req_exp <= $n_exp) {
		$n_exp = 0;
		$n_level++;
	}
	db_query('UPDATE clan SET exp = ' .$n_exp. ', level = ' .$n_level . ' WHERE id = ' .$clan_id . ' AND exp = ' .$clan['exp'] . ' AND level = ' .$clan['level']);
	if(db_affected_rows() == 0) {
		echo 'Fehler beim zuweisen der Clanexp, dadurch sind dem Clan mit der ID ' . $clan['level'] . ' ' .$exp . ' exp fl&omul;ten gegangen!! Neue exp w&auml;ren ' . $n_exp . ' bei level ' .$n_level;
	} else {
		$money = getMoneyBonusByLevel($n_level);
		if($money > 0) {
			// nice we can give the clan some money :D
			insertClanTickerMessage($clan_id, 'Durch Erreichen von Level ' .$n_level . ' habt ihr ' . displayMoney($money) . ' bekommen!!');
			db_query('UPDATE clan SET geld = geld + ' . $money . ' WHERE id = ' .$clan_id );
		}
	}
}

?>
