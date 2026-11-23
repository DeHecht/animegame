<?php
/*
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */


include_once('path.inc.php'); // get the path ;)
include_once (ROOT_PATH . '/include/config.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$kampf_id = validateUnsignedInteger($_GET['kampf_id'], null);
$kampf_name = validateString($_GET['kampf_name']);

//Unkritisch
$search = $_GET['search'];



?>
<body bgcolor="#202020">
<?php

if ($search) {
	$kampf_id2 = mysqli_fetch_array(db_query("SELECT k.id FROM chars c LEFT JOIN wanted_kampf k ON(k.char1=c.id OR k.char2=c.id) WHERE c.name='$kampf_name' LIMIT 1"));
?>

<SCRIPT language="JavaScript">
window.open(<?php echo '"wanted_kampf_anzeige.php?kampf_id=' .$kampf_id2['id']. '"'; ?>,"","status=no,hotkeys=no,Height=600,Width=820,scrollbars=yes");
</SCRIPT>
<?php

}

if ($kampf_id) {

	echo "<p align=center>$back<br>&nbsp;<br>";

	$kampf1 = mysqli_fetch_array(db_query("SELECT * FROM wanted_kampf WHERE id='$kampf_id' LIMIT 1"));
	$char_1 = getChar($kampf1['char1']);
	$char_2 = getChar($kampf1['char2']);

	$spleoic = "wanted";
	$starke1 = explode(",", $kampf1['starke1']);
	$starke2 = explode(",", $kampf1['starke2']);
	$ver1 = explode(",", $kampf1['ver1']);
	$ver2 = explode(",", $kampf1['ver2']);
	$speed1 = explode(",", $kampf1['speed1']);
	$speed2 = explode(",", $kampf1['speed2']);
	$ausdauer1 = explode(",", $kampf1['ausdauer1']);
	$ausdauer2 = explode(",", $kampf1['ausdauer2']);
	$glueck1 = explode(',', $kampf1['glueck1']);
	$glueck2 = explode(',', $kampf1['glueck2']);

	$schaden1 = explode(",", $kampf1['schaden1']);
	$schaden2 = explode(",", $kampf1['schaden2']);
	$attacke1 = explode(",", $kampf1['attacke1']);
	$attacke2 = explode(",", $kampf1['attacke2']);

	$hp1 = explode(",", $kampf1['hp1']);
	$hp2 = explode(",", $kampf1['hp2']);
	$mp1 = explode(",", $kampf1['mp1']);
	$mp2 = explode(",", $kampf1['mp2']);

	$a_hp1 = explode(",", $char_1['hp']);
	$a_hp2 = explode(",", $char_2['hp']);
	$a_mp1 = explode(",", $char_1['mp']);
	$a_mp2 = explode(",", $char_2['mp']);
	$runde = 1;

	// das muss noch umgestellt werden!!
	include "include/kampf_anzeige.php";

} else {
	$kampf_search = db_query('SELECT c1.name AS c1_name, c2.name AS c2_name, k.id FROM wanted_kampf k LEFT JOIN chars c1 ON(c1.id=k.char1) LEFT JOIN chars c2 ON(c2.id=k.char2) LEFT JOIN user u ON(u.id=c1.besitzer OR u.id=c2.besitzer) WHERE u.id=' . $user_ida[id]);
?>
<SCRIPT language="JavaScript">
	function wanted_anzeige(id){
		var Info = window.open("wanted_kampf_anzeige.php?kampf_id="+id,"","status=no,hotkeys=no,Height=600,Width=820,scrollbars=yes");
	}
</SCRIPT>

<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" width="100%" height="88">
	<tr>
		<th width="484" height="40" colspan="4" align="center">Meine Wantedkämpfe</th>
	</tr>
<?php

	$kampfs = 1;
	while ($row = mysqli_fetch_array($kampf_search)) {
?>
   <tr>
      <th width="102" height="23" align="center"><?php echo $row['c1_name']; ?></th>
      <th width="19" height="23">vs.</th>
      <th width="298" height="23"><?php echo $row['c2_name']; ?></th>
      <th width="279" height="22"><a href='javascript:wanted_anzeige(<?php echo $row['id']; ?>)'>Anzeigen</a></td>
    </tr>

<?php

		$kampfs++;
	}
?>
	<tr>
		<th width="135" height="20" align="center">Wantedkampf suche</th>
		<td width="343" height="20" colspan="3">
		<form action="index.php" method="GET">
			<input type="hidden" name="as" value="wanted_kampf_anzeige" />
			<input type="hidden" name="search" value="1" />
			<input id="input" name="kampf_name">
			<input id="input" type=submit value="nach Namen suchen.">
		</td>
	</tr>
  </table>
<?php

}
?>
