<?php
/*
 *
 * @copyright (c) 2012 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 * @author hecht
 */

include_once(ROOT_PATH.'/include/sqlwrapper.inc.php');
include_once(ROOT_PATH.'/include/user.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/messagefunctions.inc.php');



/**
 * creates a chat room for a conversation!
 * @param array $clan_ids the ids of clans that are allowed to read and write in this chatroom
 * @param string $foreign_identifier something like "cf_12345" or "clan_1234"
 * return identifier
 */
function createChatRoom(array $clan_ids, $foreign_identifier) {
	db_query('INSERT INTO clan_chatrooms(foreign_id) values(\''.$foreign_identifier.'\')');
	if(db_affected_rows() == 0) {
		return 'Konnte den Clanchatroom nicht erstellen, da er schon exisitert!';
	}
	$chat_id = getClanChatId($foreign_identifier);
	foreach ($clan_ids as $clan_id) {
		db_query('INSERT INTO clan_chatroom_clans(clan_chat_id, clan_id) values(\''.$chat_id.'\', \''.$clan_id.'\')');
	}
	return $chat_id;
}

/**
 * this method returns the clanChatId using the foreign identifier!!
 * @param string $foreign_identifier
 */
function getClanChatId($foreign_identifier) {
	$row = mysqli_fetch_row(db_query('SELECT clan_chat_id FROM clan_chatrooms WHERE foreign_id = \''.$foreign_identifier.'\''));
	return $row[0];
}

function checkAccessRights($user_id, $clan_chat_id) {
	if($user_id == NULL) {
		return false;
	}
	$qry = silent_query('SELECT clan_chat_id FROM clan_chatroom_clans cc INNER JOIN user u ON cc.clan_id = u.clan WHERE cc.clan_chat_id = ' . $clan_chat_id . ' and u.id = ' .$user_id);
	if(!$qry) {
		return false;
	}
	return mysqli_num_rows($qry);
}

function sendClanChatMessage($user_id, $clan_chat_id, $message) {
	$message = addslashes($message);
	if(checkAccessRights($user_id, $clan_chat_id)) {
		$user = getUser($user_id);
		while(true) {
			$qry = db_query('SELECT IFNULL(max(msg_id),0) + 1 FROM clan_chatroom_messages WHERE clan_chat_id = ' .$clan_chat_id);
			if(!qry) {
				return 'COULD NOT EXECUTE A QUERY!!!';
			}
			$row = mysqli_fetch_row($qry);
			$res = db_query('INSERT INTO clan_chatroom_messages(clan_chat_id, user_id, message, msg_id) values('.$clan_chat_id.', '.$user_id.', \''.$message.'\', '. $row[0] .')');
			if(db_affected_rows() > 0) {
				break;
			}
		}
	}
	return NULL;
}

function getClanChatMessages($requester, $clan_chat_id, $count, $asc, $msg_id = NULL) {
	if(!is_numeric($count)) {
		$count = 10;
	}


	if(checkAccessRights($requester, $clan_chat_id)) {
		$result['chat_rows'] = array();
		$sql = 'SELECT * FROM clan_chatroom_messages WHERE clan_chat_id = ' . $clan_chat_id;
		if(is_numeric($msg_id)) {
			$sql .= ' AND msg_id > ' .$msg_id;
		} else {
			$max_id_row = mysqli_fetch_row(db_query('Select max(msg_id) from clan_chatroom_messages WHERE clan_chat_id = ' . $clan_chat_id));
			$sql .= ' AND msg_id > ' . ($max_id_row[0] - $count);
		}
		$sql .= ' ORDER BY msg_id ASC LIMIT ' .$count;
		$qry = db_query($sql);
		while($row = mysqli_fetch_assoc($qry)) {
			$result['max_id'] = max($result['max_id'], $row['msg_id']);
			$message = stripslashes(encodeNoHTMLWithBB(stripslashes($row['message'])));
			$result['chat_rows'][] = formatTimestampShortYear($row['zeit']) . ' ' . generateUserNameByID($row['user_id']) . ': ' . $message;
		}

		if(!$asc) {
			$copy = array();
			for ($i=0; $i<count($result['chat_rows']); $i++) {
				$copy[$i] = $result['chat_rows'][count($result['chat_rows']) - ($i + 1)];
			}
			$result['chat_rows'] = $copy;
		}

		return $result;
	}
	return NULL;
}
