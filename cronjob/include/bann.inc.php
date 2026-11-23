<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
function isSpammer($id){
	$result = mysqli_fetch_assoc(db_query('SELECT TIMESTAMPDIFF(Second,max(bann_until),now()) as anzahl from spammer WHERE userid='.$id.' group by userid'));
	if($result['anzahl'] < 0){
		return true;
	} else{
		return false;
	}
}

function becomeSpammer($id,$nick,$text){
	// Es gibt 2 indikatoren ein Spammer zu sein
	// Man schreibt min 10 posts innerhalb von 20 Sekunden
	$result1 = mysqli_num_rows(db_query('SELECT * FROM ag_chat WHERE user=\''.$nick.'\' and TIMESTAMPDIFF(Second,zeit,now()) <= 20'));
	// Man schreibt min 30 Posts innerhalb einer Minute
	$result2 = mysqli_num_rows(db_query('SELECT * FROM ag_chat WHERE user=\''.$nick.'\' and TIMESTAMPDIFF(Second,zeit,now()) <= 60'));
	// Innerhalb einer halben Minute 4 mal den selben Post!!
	$result3 = mysqli_num_rows(db_query('SELECT * FROM ag_chat WHERE user=\''.$nick.'\' AND TIMESTAMPDIFF(Second,zeit,now()) <= 30 AND nachricht = \''.$text.'\''));
	

	// Und nun noch ein paar Aergernisse
//	$a = stripos($text, 'frei') !== FALSE || stripos($text, 'free') !== FALSE || stripos($text, 'offen') !== FALSE || stripos($text, 'open') !== FALSE;
//	$b = stripos($text, 'kämpfe') !== FALSE || stripos($text, 'fights') !== FALSE || stripos($text, 'f.') !== FALSE;
//	$c = stripos($text, 'spam') !== FALSE;
//	if(($a && $b && count(preg_split('# #', $text)) <= 4 ) || ($c && count(preg_split('# #', $text)) <= 1)){
//		setSpamblock($id);
//	}


	if($result1 >= 10 || $result2 >= 30 || $result3 >=4){
//		 Anzahl eintraege in spamliste
		setSpamblock($id);
	}
}

function setSpamblock($id){
		$anzahl = mysqli_num_rows(db_query('SELECT * FROM spammer WHERE userid='.$id.' AND TIMESTAMPDIFF(Day,bann_until,now()) <=14'));
		$zeit = pow(5,($anzahl+1));
		db_query('INSERT INTO spammer(userid, bann_until) values('.$id.',TIMESTAMPADD(Minute,'.$zeit.',now()))');	
}

function releaseSpamblock($id){
	db_query('DELETE from spammer where userid='.$id.' and TIMESTAMPDIFF(Second,bann_until,now()) < 0');
}

function bannAccount($id){
	db_query('UPDATE user SET bann = \'ja\' WHERE id='.$id);
}

function unbannAccount($id){
	db_query('UPDATE user SET bann = \'nein\' WHERE id='.$id);
}

?>
