<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH.'//include/config.inc.php');
include_once (ROOT_PATH . '//include/parse.inc.php');
include_once (ROOT_PATH . '//include/designfunctions.inc.php');
// GET-Section
// Kritisch (SQL-Injections)

//Unkritisch
$char_name = $_GET['char_name'];
$wahl = $_GET['wahl'];

if ($wahl == 'geld') {
	$wahl_x = 'Geld';
	$wahl_y = 'Elo';
} elseif ($wahl == 'siege' OR $wahl == 'niederlagen') {
	$wahl_x= 'Siege';
	$wahl_y= 'Niederlagen';
	$wahl_z = 'Elo';
} elseif ($wahl == 'elo'){
	$wahl_x= 'Siege';
	$wahl_y= 'Niederlagen';
	$wahl_z = 'Elo';
} else {
	$wahl = 'elo';
	$wahl_x= 'Siege';
	$wahl_y= 'Niederlagen';
	$wahl_z = 'Elo';
}

$sql = 'SELECT i.level, i.siege, i.leader, i.niederlagen, i.geld, i.clanname, i.id, u.nickname, (Select count(*) from user u1 where clan = i.id) as member, (Select 10 - count(*) from user u2 where clan = i.id and ((i.leader is null || i.leader != u2.id) and (i.co_leader is null ||i.co_leader != u2.id))) as freeslots, (select sum(spende) as summe from user_clan where clanid = i.id) as summe, elo FROM clan i LEFT JOIN user u ON(i.leader=u.id) ORDER BY i.'.$wahl.' DESC LIMIT 50';
//echo $sql . '<br>';
$char_s = db_query($sql);
?>

  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%"  height="1">
	<!-- MSTableType="layout" -->
	<tr>
		<td valign="middle" colspan="3">
		<p align="center">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get"><br>
			<input type="hidden" name="as" value="clan/c_ranglist" />
			<input type="hidden" name="char_name" value="<?php echo $char_name; ?>" />
			<select id="input" name="wahl">
				<?php
				$choices = array('elo', 'siege', 'niederlagen', 'geld');
				$choice_labels = array('ELO-Wertung', 'Siege', 'Niederlagen', 'Geld');
				for($i = 0; $i< count($choices); $i++) {
					$selected = '';
					if($wahl == $choices[$i]) {
						$selected = ' selected="selected"';
					}
						echo '<option value="'.$choices[$i].'"'.$selected.'>'.$choice_labels[$i].'</option>'. PHP_EOL;
				}
				?>
			</select>&nbsp;&nbsp;&nbsp;
			<input id="input" type=submit value="ordnen" />
		</form>
</td>
		<td valign="middle" colspan="3" height="1">
		<p align="center"><b>
</b></td>
	</tr>
	<tr>
		<th valign="middle" height="25" align="center">#</th>
		<th valign="middle" height="25" align="center" width="20%">Name</th>
		<th valign="middle" height="25" align="center" width="20%">Leader</th>
		<th valign="middle" height="25" align="center">Level</th>
		<th valign="middle" height="25" align="center">Member</th>
		<th valign="middle" height="25" align="center">Slots</th>
		<?php
		if ($wahl != 'geld') {
			echo '<th valign="middle" height="25">'.$wahl_x.'</th>';
			echo '<th valign="middle" height="25">'.$wahl_y.'</th>';
			echo '<th valign="middle" height="25">'.$wahl_z.'</th>';
		} else {
			echo '<th valign="middle" height="25">'.$wahl_x.' Aktuell</th>';
			echo '<th validn="middle" height="25">'.$wahl_x.' &#8721;</th>';
			echo '<th valign="middle" height="25">'.$wahl_z.'</th>';
		}
		?>
	</tr>
<?php
$x=1;
while($row = mysqli_fetch_assoc($char_s)) {
	echo '<tr>'."\n";
	echo '<th valign="middle" height="25" bgcolor="'.$cols[$x].'" align="center">'.$x.'</th>'."\n";
	echo '<th valign="middle" height="25" bgcolor="'.$cols[$x].'" align="center"><a href="index.php?as=clan/clan_info&clan_id='.$row['id'].'">'.$row['clanname'].'</a></th>'."\n";
	echo '<th valign="middle" height="25" bgcolor="'.$cols[$x].'" align="center">'.displayUserLink($row['leader'], $row['nickname']).'</th>'."\n";
	echo '<th valign="middle" height="25" bgcolor="'.$cols[$x].'" align="center">'.$row['level'].'</th>'."\n";
	echo '<th valign="middle" height="25" bgcolor="'.$cols[$x].'" align="center">'.$row['member'].'</th>'."\n";
	echo '<th valign="middle" height="25" bgcolor="'.$cols[$x].'" align="center">'.(getMaximumMembers($row['id']) - $row['member']).'</th>'."\n";

	if($wahl == "geld"){
		echo '<th valign="middle" height="25" bgcolor="'.$cols[$x].'" align="center">'.displayMoney($row[$wahl]).'</th>';
		echo '<th valign="middle" height="25" bgcolor="'.$cols[$x].'" align="center">'.displayMoney($row['summe']).'</th>';
	} else {
		echo '<th valign="middle" height="25" bgcolor="'.$cols[$x].'" align="center">'.displayCount($row[strtolower($wahl_x)]).'</th>';
		echo '<th valign="middle" height="25" bgcolor="'.$cols[$x].'" align="center">'.displayCount($row[strtolower($wahl_y)]).'</th>';
		echo '<th valign="middle" height="25" bgcolor="'.$cols[$x].'" align="center">'.displayCount($row[strtolower($wahl_z)]).'</th>';
	}
	echo '</tr>'."\n";

?>

<?php
$x++;
}
?>
</table>

