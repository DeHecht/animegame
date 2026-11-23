<?php
/*
 * Created on 28.08.2017
 *
 * @copyright (c) 2011 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

function displayPresent($request) { 
	$tables = getItemsMap();
	if (isset($request['persist'])) {
		$item = lookupItem($tables, $request['item']);
		$user = mysqli_fetch_assoc(db_query('SELECT id, nickname FROM user where id = '.$request['user']));
		if ($item && $user ) {
			logaction('Hat das item '.$item['name'].' von Tabelle '.$item['table_name'].' an User '.$user['nickname'].' verschenkt');
			if (createItemForUser($item, $user['id'])) {
				echo '<p>Das item '.$item['name'].' von Tabelle '.$item['table_name'].' an User '.$user['nickname'].' erfolgreich verschenkt!</p>';
			} else {
				echo '<p>FEHLER!! Das item '.$item['name'].' von Tabelle '.$item['table_name'].' an User '.$user['nickname'].' wurde nicht verschenkt!</p>';
				
			}
		}
	}

?>
<form method="POST" action="">
	<input name="persist" value="true" type="hidden" />
	<table>
		<tr>
			<th colspan="2">Verschenken von Items</th>
		</tr>
		<tr>
			<th>Item</th>
			<td>
				<select name="item">
<?php
	foreach ($tables as $table) {
		foreach ($table as $item) {
			echo '<option value="'.item2Value($item).'">'.item2Name($item).'</option>'."\n";
		}
	}
?>
				</select>
			</td>
		</tr>
		<tr>
			<th>User</th>
			<td>
				<select name="user">
<?php
		$qry = db_query('SELECT id, nickname FROM `user` ORDER BY nickname');
		while ($row = mysqli_fetch_assoc($qry)) {
			echo '<option value="'.$row['id'].'">'.$row['nickname'].'</option>'."\n";
		}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center"><input type="submit" value="verschenken" /></td>
		</tr>
		<tr>
			<td colspan="2">Bedenke, dass das Verschenken aufgezeichnet wird, also nicht cheaten!!</td>
		</tr>
		<tr>
			<td colspan="2"><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
			</td>
		</tr>
</form>
<?php

}

