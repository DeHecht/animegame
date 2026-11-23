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
include_once(ROOT_PATH.'/include/designfunctions.inc.php');

$page = validateUnsignedInteger($_GET['page'], null);

$row = mysqli_fetch_assoc(db_query('Select max(runde) as maximum from highscore'));
$maxpages = $row['maximum'];

$url = '<a href="index.php?as=halloffame&page=###PAGE###">###LABEL###</a>';

if(!is_numeric($page) || $page < 1){
	$page = 0;
} else if($page > $maxpages){
	$page = $maxpages;
}

?>

<script>
	function kampf(id) {
		window.open("./turnier2.php?charm=1&id="+id,"","status=no,hotkeys=no,height=600,width=929,scrollbar=yes");
	}
</script>

<?php

$qry = db_query('Select * from highscore where runde = '.($maxpages-$page).' order by datum');

echo '<table border="0" cellpadding="0" cellspacing="0" width="100%" height="69">'."\n";
echo '<tr>'."\n";
echo '	<th class="content" colspan="4">Halle der Helden aus Runde '.($maxpages - $page).'</th>'."\n";
echo '</tr>'."\n";

$name_translation = array(
		'klein' => 'Anf&auml;ngerturnier',
		'gross' => 'Weltturnier',
		'grossst' => 'Weltturnier Non-Fusion',
		'wochen' => 'Wochenturnier',
		'wochenst' => 'Wochenturnier Non-Fusion'
);

$eventhighscore = array();
$events = array();

// Now generate the strings
while($row = mysqli_fetch_assoc($qry)){
	$art = $row['art'];
	if ($name_translation[$art] !== NULL) {
		$art = $name_translation[$art];
	}
	
	$events[$art] += 1;
	
	$eventhighscore[$art] .=  '<tr>'."\n";
	$eventhighscore[$art] .=  '	<td class="content" height="25" width="100" align="center">'.$row['datum'].'</td>'."\n";
	if(!is_null($row['charid'])){
		$eventhighscore[$art] .=  '	<td class="content" height="25" width="100" align="center">'.displayCharLink($row['charid'], $row['charname']).'</td>'."\n";
	} else {
		$eventhighscore[$art] .=  '	<td class="content" height="25" width="100" align="center">'.$row['charname'].'</td>'."\n";
	}
	if(!is_null($row['userid'])){
		$eventhighscore[$art] .=  '	<td class="content" height="25" width="100" align="center">'.displayUserLink($row['userid'], $row['username']).'</td>'."\n";
	} else{
		$eventhighscore[$art] .=  '	<td class="content" height="25" width="100" align="center">'.$row['username'].'</td>'."\n";
	}
	if(!is_null($row['tournament_id'])) {
		$eventhighscore[$art] .=  '	<td class="content" height="25" width="100" align="center"><a href="javascript:kampf('.$row['tournament_id'].');">Turnier anzeigen</a></td>'."\n";
	}
	$eventhighscore[$art] .=  '</tr>'."\n";
}

asort($events);

foreach ($events as $key => $value) {
	echo '<tr>'."\n";
	echo '	<th class="content" height="39" colspan="4">'.$key.'</th>'."\n";
	echo '</tr>'."\n";
	echo '<tr>'."\n";
	echo '	<th class="content" height="22" width="100" align="center">Datum</th>'."\n";
	echo '	<th class="content" height="22" width="100" align="center">Sieger</th>'."\n";
	echo '	<th class="content" height="22" width="100" align="center">Trainer</th>'."\n";
	echo '</tr>'."\n";
	
	echo $eventhighscore[$key];
	
}

echo '<tr>'."\n";
echo '	<td align="center" valign="bottom" height="22" width="100%" colspan="4">'."\n";
echo '		<table width="100%">'."\n";
echo displayPagelinks($page, $maxpages, $url);
echo '		</table>'."\n";
echo '	</td>'."\n";
echo '</tr>'."\n";

echo '</table>'."\n";
