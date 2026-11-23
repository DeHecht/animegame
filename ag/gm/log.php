<?php
/*
 * Created on 20.08.2007
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
/******************************************************************************
* Dieser Skript enthaelt Funktionen fuer das versenden von Nachrichten wichtig ist
* Dabei werden die Info's ueber einen bbCode umgesetzt
*******************************************************************************
* Autor: Pascal Proksch
* Erstellungsdatum: 20.08.2007
* Zuletzt veraendert: 03.10.2007
*******************************************************************************/

include_once('../path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config/db.inc.php');
include_once(ROOT_PATH.'/include/sqlwrapper.inc.php');

function getChatMessages(){
	$result = db_query('SELECT *, Minute(zeit) as m, Hour(zeit) as h, DAY(zeit) as d , MONTH(zeit) as mon FROM ag_chat ORDER BY ID desc');
	$resultstring = '';
	
	while ($row = mysqli_fetch_array($result)) {
		$h = $row['h'];
		if($h < 10){ $h = "0".$h;}
		$m = $row['m'];
		if($m < 10){ $m = "0".$m;}
		$resultstring = $row['d'].'.'.$row['mon'].' '.$h.':'.$m.' '.$row['user'].': '.$row['nachricht'].'<br>'.$resultstring;
	}
	return $resultstring;
}

echo getChatMessages();

?>

