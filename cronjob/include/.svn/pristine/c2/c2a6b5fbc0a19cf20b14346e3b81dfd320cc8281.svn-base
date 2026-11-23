<?php
/*
 * Created on 25.03.2009
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

function checkCookies($nickname, $yps) {
	$cookiearray = preg_split('#,#', $yps);
	if (md5($cookiearray[0]) == $cookiearray[1]) { // Einfach um manipulation zu verringern
		$nick_name2 = $cookiearray[0];
	}

	if (isset ($nick_name2) && strtolower($nick_name2) != strtolower($nickname)) {
		// Ha!!! Da ist doch was faul!!
		// sortiere die namen
		$m_user_qry = db_query('SELECT * FROM user where nickname IN (\'' . $nickname . '\',\'' . $nick_name2 . '\') order by id');
		$muser = array ();
		while ($row = mysqli_fetch_assoc($m_user_qry)) {
			$muser[] = $row;
		}
		if (count($muser) != 2) {
			return false;
		}
		// Neue Tabelle
		$sql = 'INSERT INTO multiuser(user1, user2, occured, type) values(\'' . $muser[0]['id'] . '\', \'' . $muser[1]['id'] . '\',CURRENT_TIMESTAMP, \'Cookie\')';
		db_query($sql);
	}
}

function detectNonCodeEnterers(){
	$qry = db_query('Select * from bot_images WHERE TIMESTAMPDIFF(MINUTE, created, now()) > 10');
	db_query('DELETE from bot_images WHERE TIMESTAMPDIFF(MINUTE, created, now()) > 10 ');
	while($row = mysqli_fetch_assoc($qry)){
		$sql = 'Insert into bot_image_failures(userid, inserted, valid, used) values('.$row['userid'].', \'!NOTHING!\', \''.$row['compressed_phrase'].'\', \''.$row['created'].'\')';
//		echo $sql.'<br>';
		silent_query($sql);
	}
}

?>
