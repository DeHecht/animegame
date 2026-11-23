<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include "db.php";
include('path.inc.php');
include_once (ROOT_PATH.'/include/sqlwrapper.inc.php');
include_once ('inclu/messagefunctions.inc.php');
include_once ('inclu/halloffame.inc.php');

$platz1 = db_query("SELECT id, besitzer, name FROM chars order by liga_punkte DESC LIMIT 3");
db_query("UPDATE chars SET liga_siege='0', liga_niederlagen='0', liga_punkte='0'");
$x=0;
$mac_geld = 50000;
while($row = mysqli_fetch_array($platz1)) {
	$user = mysqli_fetch_array(db_query("SELECT geld, nickname FROM user WHERE id='$row[besitzer]' LIMIT 3"));
	$new_geld = $user[geld] + $mac_geld;
	db_query("UPDATE user SET geld='$new_geld' WHERE id='$row[besitzer]'");
	$mac_geld = $mac_geld - 20000;
	if($x == 0) {
		$platz_a = $row[name];
		$platz_a_id = $row['id'];
		$platz_b = $user[nickname];
		$platz_b_id = $row['besitzer'];
	}
	$x++;
}
$all_user = db_query("SELECT mail FROM user");

//while($rowa = mysqli_fetch_array($all_user)) {
//@mail($rowa[mail],"LIGA Sieger","Sieger: $platz_a \n Trainer: $platz_b", "From: "."Vegeta"."\nReply-To: "."vegeta@animegame.de"."\nX-Mailer: PHP/" . phpversion());
//}
$ligas = mysqli_fetch_array(db_query("SELECT liga FROM online WHERE id='1'"));
db_query("UPDATE online SET liga='$ligas[liga],$platz_a,$platz_b' WHERE id='1'");
// insertIntoHallOfFame($title, $charid, $userid);
insertIntoHallOfFame('Liga', $platz_a_id, $platz_b_id);
