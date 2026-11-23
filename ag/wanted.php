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
include_once(ROOT_PATH.'/include/char.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$char_id = validateUnsignedInteger($_GET['char_id'], null);

//Unkritisch
$charm = $_GET['charm'];



function displayTodesliste($char_id){
	$char_in = getChar($char_id);
	echo '<p align=center><b>Todes List von '.$char_in['name'].':</b><br>&nbsp;<br>';

	$list_a = explode(',', $char_in['dead_list']);
	$u = 0;

	while($u < count($list_a)){
		echo $list_a[$u].'<br>';
		$u++;
	}
}

function displayWantedTop10(){
?>
	<table cellpadding="0" cellspacing="0" width="100%" height="25">
		<!-- MSTableType="layout" -->
		<tr>
			<th valign="top" height="25" colspan="4" align="center">TOP 10 WANTED</th>
		</tr>
		<tr>
			<th height="25" width="117" valign="top" align="center">Charakter</th>
			<th height="25" width="147" valign="top" align="center">Trainer</th>
			<th height="25" width="85" valign="top" align="center">Preis</th>
			<th height="25" width="88" valign="top" align="center">Todes List</th>
		</tr>
<?php
$char_z = db_query('SELECT name, bild, besitzer, preis, id FROM chars order by preis DESC LIMIT 10');
while($row = mysqli_fetch_array($char_z)) {
	$trainer = mysqli_fetch_array(db_query('SELECT nickname, id FROM user WHERE id=\''.$row['besitzer'].'\' LIMIT 10'));
?>
		<tr>
			<td height="30" width="117" valign="top" align="center"><a href="index.php?as=info&charm=1&char_id=<?php echo $row['id']; ?>"> <?php echo $row['name']; ?><br><img border="0" src="<?php echo $row['bild']; ?>" width="75" height="75"></a></td>
			<td height="30" width="147" valign="middle" align="center"><a href="index.php?as=info&user_id=<?php echo $trainer['id']; ?>"><?php echo $trainer['nickname']; ?></a></td>
			<td height="30" width="85" valign="middle" align="center"><?php echo $row['preis']; ?></td>
			<td height="30" width="88" valign="middle" align="center"><a href="index.php?as=wanted&charm=1&char_id=<?php echo $row['id']; ?>">Todes List</a></td>
		</tr>
<?php
}
?>
	</table>
<?php
}

if($charm == 1){
	displayTodesliste($char_id);
} else{
	displayWantedTop10();
}

?>