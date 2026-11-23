<?php
/*
 * Created on 14.08.2007
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
/******************************************************************************
* Dieser Skript enthält Funktionen für das Implementieren eines Auktionshauses 
* Ein Skript kann diese Funktionen verwenden, um Informationen darzustellen
*******************************************************************************
* Autor: Pascal Proksch
* Erstellungsdatum: 14.08.2007
* Zuletzt verändert: 28.09.2007
*******************************************************************************/

include_once "parse.inc.php";

function stimme($pollid, $polloptionid, $userid, $kommentar){
	if(mysqli_num_rows(db_query('SELECT * FROM poll_votes WHERE pollid = '.$pollid.' and userid = '.$userid)) == 0){
		// NEU einfuegen!
		$sql = 'INSERT INTO poll_votes(pollid, polloptionid, userid, comment) values ('.$pollid.','.$polloptionid.','.$userid.',\''.$kommentar.'\')';
		db_query($sql);
		//echo $sql.'<br>';
	} else{
		db_query('UPDATE poll_votes SET polloptionid='.$polloptionid.', comment = \''.encodeNoHTMLNoBB($kommentar).'\' WHERE pollid='.$pollid.' and userid = '.$userid);		
	}
}

// Liefert den Datensatz der Stimme (Nr und eigener Kommentar)
function getStimme($pollid, $userid){
	$qry = db_query('SELECT * FROM poll_votes WHERE pollid = '.$pollid.' and userid = '.$userid);
	$result = mysqli_fetch_assoc($qry);
	return $result;
}

function getUmfrage($pollid){
	$umfrage = array();
	$qry = db_query('SELECT p.thema, p.pollid, p.text, u.nickname FROM poll as p inner join user as u on p.ersteller = u.id WHERE pollid='.$pollid);
	$umfrage['poll'] = mysqli_fetch_assoc($qry);
	$qry = db_query('SELECT * FROM poll_options WHERE pollid ='.$pollid);
	while($result = mysqli_fetch_assoc($qry)){
		$umfrage['options'][$result['polloptionid']] = $result;
	}
	return $umfrage;
}

function getUmfragen($entries, $page){
	$returnArray = array();
	$qry = db_query('SELECT p.thema, p.pollid, u.nickname FROM poll as p inner join user as u on p.ersteller = u.id LIMIT '.$entries*$page.','.$entries);
	$i=0;
	while($result=mysqli_fetch_assoc($qry)){
		$returnArray[$i++] = $result;
	}
	return $returnArray;
}

function getUmfragenCount(){
	$temp = mysqli_fetch_assoc(db_query('SELECT count(*) as anzahl FROM poll'));
	return $temp['anzahl'];
}

function getComments($pollid, $entries, $page){
	$returnArray = array();
	$sql = 'SELECT polloptionid, comment FROM poll_votes where comment != \'\' and pollid= '.$pollid.' LIMIT '.$entries*$page.','.$entries;
	$qry = db_query($sql);
//	echo '<br>'.$sql.'<br>';
	for($i=0;($result = mysqli_fetch_assoc($qry)) != null && $i<$entries;$i++){
		$returnArray[$i] = $result;
	}
	return $returnArray;
}

function getCommentCount($pollid){
	$temp = mysqli_fetch_assoc(db_query('Select count(*) as anzahl from poll_votes where comment != \'\' and pollid= '.$pollid));
	return $temp['anzahl'];
}

function getPollResult($pollid){	
	$returnArray = array();
	$sql = 'SELECT text, pv.polloptionid, count(pv.polloptionid) as anzahl FROM poll_votes as pv inner join poll_options as po on pv.polloptionid = po.polloptionid and pv.pollid = po.pollid where pv.pollid = '.$pollid.' group by polloptionid';
	$qry = db_query($sql);
//	echo '<br>'.$sql.'<br>';
	while($result = mysqli_fetch_assoc($qry)){
		$returnArray[$result['polloptionid']] = $result['anzahl'];
	}
	return $returnArray;
}

function isOpen($pollid){
	$sql = 'SELECT count(*) as anzahl from poll where pollid ='.$pollid.' AND (close is null OR close > now())';

	$qry = db_query($sql);
	$row = mysqli_fetch_assoc($qry);
	if($row['anzahl'] > 0){
		return true;
	}
	return false;
}
?>