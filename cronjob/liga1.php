<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include('db.php');
include('path.inc.php');
include_once (ROOT_PATH.'/include/sqlwrapper.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');
include_once(ROOT_PATH. "/include/random.inc.php");

set_time_limit(0);
function rand_array($array)
{
	$maxarray=count($array)-1;
	mt_srand((double)microtime()*1000000);
	for($i=$maxarray; $i>=0; $i--)
	{
		if($i>0) $zufall=mt_random_wrapper(0,$i);
		else $zufall=0;
		$temp=$array[$i];
		$array[$i]=$array[$zufall];
		$array[$zufall]=$temp;
		$newarray[]=$array[$i];
		array_pop($array);
	}
	return $newarray;
}

include "db.php";

$npc_race = getRaceTypeName('NPC'); // do not remove npc (there is a separate setting for this)
$races = getRacesByType($npc_race); // remove all NPC
foreach($races as $race) {
	$exclude_race_ids[] = '\''.$race['id'].'\'';
}

$max_charakter = mysqli_num_rows(db_query('SELECT id FROM chars WHERE rasse NOT IN ('.implode(',', $exclude_race_ids).') LIMIT 250'));

if($max_charakter<250) {
	echo "Not enough chars ($max_charakter)";
exit;
}

$charakter = db_query('SELECT name, id FROM chars WHERE rasse NOT IN('.implode(',', $exclude_race_ids).') ORDER BY level DESC LIMIT '.$max_charakter);



while($row=mysqli_fetch_array($charakter))
{
	$test2[]=$row['id'];
}


		$test2 = rand_array($test2);
		$test2 = array_unique($test2);

while(count($test2)<$max_charakter);

$test3=$test2;
reset($test3);
while(current($test3)!=end($test2))
{
	if(empty($list)) $list=current($test3);
	else $list.=','.next($test3);
}


$result=db_query("SELECT id FROM chars WHERE id IN(".$list.") LIMIT 250");
while($row=mysqli_fetch_array($result)) {
	// Nutze getChar um kompatibel mit Aenderungen der Datenstruktur zu bleiben!
	$charas[$row['id']]=getChar($row['id']);
}


$a8=0;
$b8=1;

$round = 1;
while($a8 < $max_charakter)
{
	$chara_1 = $charas[$test2[$a8]];
	$chara_2 = $charas[$test2[$b8]];
	include "liga_kampf.php";
	$a8+=2;
	$b8+=2;
}




?>
