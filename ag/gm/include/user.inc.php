<?php
/*
 * Created on 31.05.2011
 *
 * @copyright (c) 2011 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
?>

<?php

function getOtherUserGroups($user) {
	$qry = null;
	$groups = array();

	//prüfen welcher wert für user steht (id oder name)
	if(is_numeric($user)) {
		$test = 'SELECT gruppen_id, gruppen_name FROM user_gruppe WHERE gruppen_id not in
				(SELECT gruppen_id FROM user_gruppe_zuordnung WHERE user_id = \''.$user.'\')';
		$qry = db_query($test);
	} else if (validateString($user) != null) {
		$test = 'SELECT gruppen_id, gruppen_name FROM user_gruppe WHERE gruppen_id not in
				(SELECT ugz.gruppen_id FROM user u INNER JOIN user_gruppe_zuordnung ugz ON
				 u.id=ugz.user_id WHERE nickname = \''.$user.'\')';
		$qry = db_query($test);
	}

	$i = 0;
	while ($result = mysqli_fetch_assoc($qry)) {
		$groups[$i] = array("gruppen_id" => $result['gruppen_id'], "gruppen_name" => $result['gruppen_name']);
		$i++;
	}
	return $groups;
}

//TODO: action loggen!
function displayUserGroups($action, $name, $ugname, $delete, $add) {
	if($action === 'search') {
		$username = validateName($name);
		if($username !== NULL) {
			if ($delete !== NULL && $delete >= 0) {
				logaction('User '.$username.' aus der Gruppe '.$delete.' gelöscht');
				removeUserFromGroup($username, $delete);
			} else if($add !== NULL && $add >= 0) {
				logaction('User '.$username.' in die Gruppe '.$add.' hinzugefügt');
				addUserToGroup($username, $add);
			}

			$usergroups = getUserGroups($username);
			$notusergroups = getOtherUserGroups($username);

			?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
	<input name="choose" value="usergroups" type="hidden"></input> <input
		name="action" value="search" type="hidden"></input> <input
		name="uname" value="<?php echo $username; ?>" type="hidden"></input>
	<table width="80%">
		<tr>
			<th align="center" colspan="3">User <?php echo $username;?></th>
		</tr>
		<tr>
			<td align="center"><select id="input" name="delete">
					<option value="-1">Gruppe ausw&auml;hlen</option>
					<?php
					for($i = 0; $i < count($usergroups); $i++) {
						echo '<option value="'.$usergroups[$i]['gruppen_id'].'">'.$usergroups[$i]['gruppen_name'].'</option>';
					}
					?>
			</select> <input type="submit" value="löschen" />
		
		</tr>
		<tr>
			<td align="center"><select id="input" name="add">
					<option value="-1">Gruppe ausw&auml;hlen</option>
					<?php
					for($i = 0; $i < count($notusergroups); $i++) {
						echo '<option value="'.$notusergroups[$i]['gruppen_id'].'">'.$notusergroups[$i]['gruppen_name'].'</option>';
					}
					?>
			</select> <input type="submit" value="hinzufügen" />
		
		</tr>
		<tr>
			<td colspan="3">
				<table border="1" width="100%">
					<tr>
						<th>Usergruppen</th>
						<?php
						for($i = 0; $i < count($usergroups); $i++) {
							echo '<tr><td align="center">'.$usergroups[$i]['gruppen_name'].'</td>';
						}
						?>
				
				</table>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="3"><a
				href="<?php echo $_SERVER['PHP_SELF'].'?choose=usergroups'; ?>">Zur&uuml;ck</a>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="3"><a
				href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
			</td>
		</tr>
	</table>
</form>
						<?php		} else {
							echo 'Ungültiger Username!<br><a href='.$_SERVER['PHP_SELF'].'?choose=usergroups>Zur&uuml;ck</a>';
						}
	} else if($action === 'show') {
		$username = validateName($ugname);
		$sqlqry = 'SELECT u.nickname FROM user_gruppe_zuordnung ugz
					INNER JOIN user_gruppe ug ON ug.gruppen_id=ugz.gruppen_id
					INNER JOIN user u ON ugz.user_id=u.id WHERE ug.gruppen_name = \''.$ugname.'\' ORDER BY u.nickname;';
		$usergroups_qry = db_query($sqlqry);
		?>
<table width="80%">
	<tr>
		<th align="center" colspan="3">Usergruppe <?php echo $ugname;?></th>
	</tr>
	<tr>
		<td colspan="3">
			<table border="1" width="100%">
			<?php
			echo '<tr>';
			$x = 0;
			while($row = mysqli_fetch_assoc($usergroups_qry)){
				echo '<td align="center" width = "25%">'.$row['nickname'].'</td>';
				$x++;
				if($x % 4 == 0) {
					echo '</tr><tr>';
					$x = 0;
				}
			}
			echo '</tr>'
			?>

			</table>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="3"><a
			href="<?php echo $_SERVER['PHP_SELF'].'?choose=usergroups'; ?>">Zur&uuml;ck</a>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="3"><a
			href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
		</td>
	</tr>
</table>
			<?php
	} else {
		?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
	<input name="choose" value="usergroups" type="hidden"></input> <input
		name="action" value="search" type="hidden"></input>
	<table width="80%">
		<tr>
			<th align="center" colspan="3">User suchen</th>
		</tr>
		<tr>
			<td align="center">Username: <input name="uname" value="" /> <input
				type="submit" value="search" />
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<table border="1" width="100%">
					<tr>
						<th>Usergruppe</th>
						<th>Beschreibung</th>
						<?php
						$usergroups_qry = db_query('SELECT gruppen_name, gruppen_beschreibung FROM user_gruppe ug;');
						while($row = mysqli_fetch_assoc($usergroups_qry)){
							echo '<tr><td align="center"><a href="'
							.$_SERVER['PHP_SELF'].'?choose=usergroups&action=show&ugname='.$row['gruppen_name'].'">'
							.$row['gruppen_name'].'</a></td><td>'.$row['gruppen_beschreibung'].'</td></tr>';
						}
						?>
				
				</table>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="3"><a
				href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
			</td>
		</tr>
	</table>
</form>
						<?php
	}
}?>

<?php
function displayCheater($action, $from, $until, $user1, $user2, $tradeid, $verify){
	if($action === null){
		?>
<form action="" method="POST">
	<input name="choose" value="cheat" type="hidden"></input>
	<table>
		<tr>
			<th align="center" colspan="2">Multiuser und Cheater</th>
		</tr>
		<tr>
			<td align="center" colspan="2"><a
				href="<?php echo $_SERVER['PHP_SELF'].'?choose=cheat&action=access'; ?>">Zugriffe</a>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="2"><a
				href="<?php echo $_SERVER['PHP_SELF'].'?choose=cheat&action=trades'; ?>">Denkwürdige
					Trades</a></td>
		</tr>
		<tr>
			<td align="center" colspan="2"><a
				href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
			</td>
		</tr>
	</table>
</form>
		<?php
	} else if($action == 'access'){
		$minmax = mysqli_fetch_assoc(db_query('SELECT DATE(min(occured)) as min, DATE(max(occured)) as max FROM multiuser'));
		$low = $from===null?$minmax['min']:$from;
		$high = $until===null?$minmax['max']:$until;
		?>
<form action="" method="POST">
	<input name="choose" value="cheat" type="hidden" />
	<table>
		<tr>
			<th align="center" colspan="3">Zugriffe</th>
		</tr>
		<tr>
			<td align="center">Von: <input name="from"
				value="<?php echo $low; ?>" />
			</td>
			<td align="center">Bis: <input name="until"
				value="<?php echo $high; ?>" />
			</td>
			<td align="center"><input type="submit" value="aktualisieren" />
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<table border="1">
					<tr>
						<th>User1</th>
						<th>User2</th>
						<th>Anzahl</th>
						<th>Action</th>
						<?php
						$cheater_qry = db_query('Select u1.nickname as u1n, u1.id as u1i, u2.nickname u2n, u2.id u2i, count(occured) as anzahl from multiuser left join user as u1 on multiuser.user1 = u1.id left join user as u2 on multiuser.user2 = u2.id where occured >= \''.$low.' 00:00:00\' and occured <= \''.$high.' 23:59:59\' group by u1i, u2i order by anzahl');
						while($row = mysqli_fetch_assoc($cheater_qry)){
							echo '<tr><td>'.$row['u1n'].'</td><td>'.$row['u2n'].'</td><td>'.$row['anzahl'].'</td><td><a href="'.$_SERVER['PHP_SELF'].'?choose=cheat&action=show&from='.$low.'&until='.$high.'&user1='.$row['u1i'].'&user2='.$row['u2i'].'">Nachweis</a> | <a href="'.$_SERVER['PHP_SELF'].'?choose=cheat&action=vwarn&user1='.$row['u1i'].'&user2='.$row['u2i'].'">Verwarnen</a> | <a href="'.$_SERVER['PHP_SELF'].'?choose=cheat&action=ban&user1='.$row['u1i'].'&user2='.$row['u2i'].'">Bannen</a></td></tr>';
						}
						?>
				
				</table>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="3"><a
				href="<?php echo $_SERVER['PHP_SELF'].'?choose=cheat'; ?>">Multiuser
					und Cheatermen&uuml;</a>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="3"><a
				href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
			</td>
		</tr>
	</table>
</form>

						<?php

	} else if($action == 'trades'){
		// Hier ist wichtig, zu hohe Trades und Trades in den Clans
		$minmax = mysqli_fetch_assoc(db_query('SELECT DATE(min(deadline)) as min, DATE(max(deadline)) as max FROM auktion_transaktionen'));
		$low = $from===null?$minmax['min']:$from;
		$high = $until===null?$minmax['max']:$until;
		?>
<form action="" method="POST">
	<input name="choose" value="cheat" type="hidden" />
	<table>
		<tr>
			<th align="center" colspan="3">Trades die durch den Auto-Check
				gefallen sind</th>
		</tr>
		<tr>
			<td align="center">Von: <input name="from"
				value="<?php echo $low; ?>" />
			</td>
			<td align="center">Bis: <input name="until"
				value="<?php echo $high; ?>" />
			</td>
			<td align="center"><input type="submit" value="aktualisieren" />
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<table border="1" width="100%">
					<tr>
						<th>Verk&auml;ufer</th>
						<th>K&auml;ufer</th>
						<th>Item</th>
						<th>Betrag</th>
						<th>Anzahl</th>
						<th>Datum</th>
						<th>Auto-Check</th>
						<th>Action</th>
						<?php
						$sql = 'SELECT at.transaktionsid, u1.nickname as u1n, u1.id as u1i, u2.nickname u2n, u2.id u2i, at.anzahl, betrag, deadline, cheatingverdacht, it.name as i_name, si.name as s_name, wm.item as w_name, itemid, tablename FROM auktion_transaktionen at left join sp_item si on tablename = \'sp_ware\' and itemid = si.id left join item as it on tablename = \'ware\' and itemid = it.id left join wochen_markt as wm on tablename = \'wochen_ware\' and itemid = wm.id left join user as u1 on at.anbieter = u1.id left join user as u2 on at.bieter = u2.id where cheatingverdacht = 1 and deadline >= \''.$low.' 00:00:00\' and deadline <= \''.$high.' 23:59:59\'';
						$cheater_qry = db_query($sql);
						while($row = mysqli_fetch_assoc($cheater_qry)){
							$name = $row['i_name']!=null?$row['i_name']:($row['w_name']!=null?$row['w_name']:$row['s_name']);
							$cheatv = $row['cheatingverdacht']==0?'OK':'WARNUNG';
							echo '<tr><td align="center">'.$row['u1n'].'</td><td align="center">'.$row['u2n'].'</td><td align="center">'.$name.'</td><td align="center">'.$row['betrag'].'</td><td align="center">'.$row['anzahl'].'</td><td align="center">'.$row['deadline'].'</td><td align="center">'.$cheatv.'</td><td><a href="'.$_SERVER['PHP_SELF'].'?choose=cheat&action=show&from='.$low.'&until='.$high.'&user1='.$row['u1i'].'&user2='.$row['u2i'].'">Nachweis</a>  | <a href="'.$_SERVER['PHP_SELF'].'?choose=cheat&action=undo&tradeid='.$row['transaktionsid'].'">R&uuml;ckg&auml;ngig</a>  | <a href="'.$_SERVER['PHP_SELF'].'?choose=cheat&action=markok&tradeid='.$row['transaktionsid'].'">OK</a> | <a href="'.$_SERVER['PHP_SELF'].'?choose=cheat&action=vwarn&user1='.$row['u1i'].'&user2='.$row['u2i'].'">Verwarnen</a> | <a href="'.$_SERVER['PHP_SELF'].'?choose=cheat&action=ban&user1='.$row['u1i'].'&user2='.$row['u2i'].'">Bannen</a></td></tr>';
						}
						?>
				
				</table>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="3"><a
				href="<?php echo $_SERVER['PHP_SELF'].'?choose=cheat'; ?>">Multiuser
					und Cheatermen&uuml;</a>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="3"><a
				href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
			</td>
		</tr>
	</table>
</form>

						<?php
	} else if($action == 'show'){
		// Zeigt die Verbindungen zwischen 2 Usern auf
		$minmax = mysqli_fetch_assoc(db_query('SELECT DATE(min(occured)) as min, DATE(max(occured)) as max FROM multiuser'));
		$low = $from===null?$minmax['min']:$from;
		$high = $until===null?$minmax['max']:$until;

		?>
<form action="" method="POST">
	<input name="choose" value="cheat" type="hidden" />
	<table>
		<tr>
			<th align="center" colspan="3">Zugriffe</th>
		</tr>
		<tr>
			<td align="center">Von: <input name="from"
				value="<?php echo $low; ?>" />
			</td>
			<td align="center">Bis: <input name="until"
				value="<?php echo $high; ?>" />
			</td>
			<td align="center"><input type="submit" value="aktualisieren" />
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<table border="1" width="100%">
					<tr>
						<th>User1</th>
						<th>User2</th>
						<th>Datum</th>
						<th>Art</th>
						<th>Action</th>
						<?php
						$sql = 'Select u1.nickname as u1n, u1.id as u1i, u2.nickname u2n, u2.id u2i, occured, type from multiuser left join user as u1 on multiuser.user1 = u1.id left join user as u2 on multiuser.user2 = u2.id where user1 = '.$user1.' and user2 = '.$user2.' and occured >= \''.$$low.' 0:00:00\' and occured <= \''.$high.' 23:59:59\' order by occured desc';
						$cheater_qry = db_query($sql);
						while($row = mysqli_fetch_assoc($cheater_qry)){
							echo '<tr><td align="center">'.$row['u1n'].'</td><td align="center">'.$row['u2n'].'</td><td align="center">'.$row['occured'].'</td><td align="center">'.$row['type'].'</td><td align="center"><a href="'.$_SERVER['PHP_SELF'].'?choose=cheat&action=vwarn&user1='.$row['u1i'].'&user2='.$row['u2i'].'">Verwarnen</a> | <a href="'.$_SERVER['PHP_SELF'].'?choose=cheat&action=ban&user1='.$row['u1i'].'&user2='.$row['u2i'].'">Bannen</a></td></tr>';
						}
						?>
				
				</table>
			</td>
		</tr>
		<tr>
			<th align="center" colspan="3">Trades</th>
		</tr>
		<tr>
			<td colspan="3">
				<table border="1" width="100%">
					<tr>
						<th>Verk&auml;ufer</th>
						<th>K&auml;ufer</th>
						<th>Item</th>
						<th>Betrag</th>
						<th>Anzahl</th>
						<th>Datum</th>
						<th>Auto-Check</th>
						<th>Action</th>
						<?php
						$sql = 'SELECT u1.nickname as u1n, u1.id as u1i, u2.nickname u2n, u2.id u2i, at.anzahl, betrag, deadline, cheatingverdacht, it.name as i_name, si.name as s_name, wm.item as w_name, itemid, tablename FROM auktion_transaktionen at left join sp_item si on tablename = \'sp_ware\' and itemid = si.id left join item as it on tablename = \'ware\' and itemid = it.id left join wochen_markt as wm on tablename = \'wochen_ware\' and itemid = wm.id left join user as u1 on at.anbieter = u1.id left join user as u2 on at.bieter = u2.id where bieter IN ('.$user1.','.$user2.') and anbieter IN('.$user1.','.$user2.') and deadline >= \''.$low.' 00:00:00\' and deadline <= \''.$high.' 23:59:59\'';
						//							echo $sql.'<br>';
						$cheater_qry = db_query($sql);
						while($row = mysqli_fetch_assoc($cheater_qry)){
							$name = $row['i_name']!=null?$row['i_name']:($row['w_name']!=null?$row['w_name']:$row['s_name']);
							$cheatv = $row['cheatingverdacht']==0?'OK':'WARNUNG';
							echo '<tr><td align="center">'.$row['u1n'].'</td><td align="center">'.$row['u2n'].'</td><td align="center">'.$name.'</td><td align="center">'.$row['betrag'].'</td><td align="center">'.$row['anzahl'].'</td><td align="center">'.$row['deadline'].'</td><td align="center">'.$cheatv.'</td><td></td></tr>';
						}
						?>
				
				</table>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="3"><?php echo displayHistoryBackLink(); ?>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="3"><a
				href="<?php echo $_SERVER['PHP_SELF'].'?choose=cheat'; ?>">Multiuser
					und Cheatermen&uuml;</a>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="3"><a
				href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
			</td>
		</tr>
	</table>
</form>
						<?php
	} else if($action == 'vwarn'){
		// Spricht eine Verwarnung zwischen 2 Usern aus
		if($verify == 1){
			$qry = db_query('select id, nickname from user where id in ('.$user1.', '.$user2.')');
			while($row = mysqli_fetch_assoc($qry)){
				$users[] = $row['nickname'];
			}
			displayErrorMessage('Verwarnung ausgesprochen', 'Die User '.join(' und ',$users).' sind nun verwarnt!', '<a href="'.$_SERVER['PHP_SELF'].'?choose=cheat">weiter</a>');
			db_query('INSERT INTO nachricht(betreff, von, datum, text, besitzer) values(\'Verwarnung\', \'AG-Team\', now(), \'Es kam zwischen den Accounts '.join(' und ',$users).' zu &Uuml;berschneidungen, wir bitten euch dies zu unterlassen, da wir uns sonst gezwungen sehen diese Aktivit&auml;ten zu bestrafen!\', '.$user1.')');
			db_query('INSERT INTO nachricht(betreff, von, datum, text, besitzer) values(\'Verwarnung\', \'AG-Team\', now(), \'Es kam zwischen den Accounts '.join(' und ',$users).' zu &Uuml;berschneidungen, wir bitten euch dies zu unterlassen, da wir uns sonst gezwungen sehen diese Aktivit&auml;ten zu bestrafen!\', '.$user2.')');
			logaction('Die User '.join(' und ',$users).' wurden verwarnt!');
		} else{
			$qry = db_query('select id, nickname from user where id in ('.$user1.', '.$user2.')');
			while($row = mysqli_fetch_assoc($qry)){
				$users[] = $row['nickname'];
			}
			displayErrorMessage('Verwarnen', 'Sollen die User '.join(' und ',$users).' wirklich verwarnt werden?', '<a href="'.$_SERVER['PHP_SELF'].'?choose=cheat&action=vwarn&user1='.$user1.'&user2='.$user2.'&verify=1">weiter</a> | '.displayHistoryBackLink());
		}
	} else if($action == 'ban'){
		// Bannt zwei Accounts
		if($verify == 1){
			$qry = db_query('select id, nickname from user where id in ('.$user1.', '.$user2.')');
			while($row = mysqli_fetch_assoc($qry)){
				$users[] = $row['nickname'];
			}
			displayErrorMessage('Bann vollzogen!', 'Die User '.join(' und ',$users).' sind nun verwarnt!', '<a href="'.$_SERVER['PHP_SELF'].'?choose=cheat">weiter</a>');
			db_query('INSERT INTO nachricht(betreff, von, datum, text, besitzer) values(\'Bann\', \'AG-Team\', now(), \'Es kam zwischen den Accounts '.join(' und ',$users).' zu &Uuml;berschneidungen. Deswegen sahen wir uns gezwungen diese Aktivit&auml;ten zu bestrafen!\', '.$user1.')');
			db_query('INSERT INTO nachricht(betreff, von, datum, text, besitzer) values(\'Bann\', \'AG-Team\', now(), \'Es kam zwischen den Accounts '.join(' und ',$users).' zu &Uuml;berschneidungen. Deswegen sahen wir uns gezwungen diese Aktivit&auml;ten zu bestrafen!\', '.$user2.')');
			db_query('Update user set bann = \'ja\' where id IN('.$user1.', '.$user2.')');
			logaction('Die User '.join(' und ',$users).' wurden gebannt!');
		} else{
			$qry = db_query('select id, nickname from user where id in ('.$user1.', '.$user2.')');
			while($row = mysqli_fetch_assoc($qry)){
				$users[] = $row['nickname'];
			}
			displayErrorMessage('Bannen', 'Sollen die User '.join(' und ',$users).' wirklich gebannt werden?', '<a href="'.$_SERVER['PHP_SELF'].'?choose=cheat&action=ban&user1='.$user1.'&user2='.$user2.'&verify=1">weiter</a> | '.displayHistoryBackLink());
		}
	} else if($action == 'undo'){
		// Macht eine Auktion rückgängig
		displayErrorMessage(NULL, 'not yet implemented', displayHistoryBackLink());
	} else if($action == 'markok'){
		if($verify == 1){
			$trade = mysqli_fetch_assoc(db_query('SELECT u1.nickname as u1n, u1.id as u1i, u2.nickname u2n, u2.id u2i, at.anzahl, betrag, deadline, cheatingverdacht, it.name as i_name, si.name as s_name, wm.item as w_name, itemid, tablename FROM auktion_transaktionen at left join sp_item si on tablename = \'sp_ware\' and itemid = si.id left join item as it on tablename = \'ware\' and itemid = it.id left join wochen_markt as wm on tablename = \'wochen_ware\' and itemid = wm.id left join user as u1 on at.anbieter = u1.id left join user as u2 on at.bieter = u2.id where transaktionsid = '.$tradeid));
			displayErrorMessage('Trade validiert!', 'Der Handel zwischen '.$trade['u1n'].' und '.$trade['u2n'].' ist nun als \'Ok\' markiert!', '<a href="'.$_SERVER['PHP_SELF'].'?choose=cheat">weiter</a>');
			logaction('Der Handel zwischen '.$trade['u1n'].' und '.$trade['u2n'].' wurde als \'Ok\' markiert (Tradeid = '.$tradeid.')!');
			db_query('Update auktion_transaktionen set cheatingverdacht = 0 where transaktionsid = '.$tradeid);
		} else{
			$trade = mysqli_fetch_assoc(db_query('SELECT u1.nickname as u1n, u1.id as u1i, u2.nickname u2n, u2.id u2i, at.anzahl, betrag, deadline, cheatingverdacht, it.name as i_name, si.name as s_name, wm.item as w_name, itemid, tablename FROM auktion_transaktionen at left join sp_item si on tablename = \'sp_ware\' and itemid = si.id left join item as it on tablename = \'ware\' and itemid = it.id left join wochen_markt as wm on tablename = \'wochen_ware\' and itemid = wm.id left join user as u1 on at.anbieter = u1.id left join user as u2 on at.bieter = u2.id where transaktionsid = '.$tradeid));
			displayErrorMessage('Trade validieren!', 'Sollen der Handel zwischen '.$trade['u1n'].' und '.$trade['u2n'].' als \'Ok\' markiert werden?', '<a href="'.$_SERVER['PHP_SELF'].'?choose=cheat&action=markok&tradeid='.$tradeid.'&verify=1">weiter</a> | '.displayHistoryBackLink());
		}
	}
	// TODO: Action um IP-Sharing zu erlauben! -> tabelle (duerfen keine miesen trades und fights mehr machen)
	// TODO: Erlaubte IP-Sharer muessen markiert werden
	// TODO: Verwarnte und Gebannte User muessen markiert werden!
}

function displayBannOptions($action, $username,$opfer,$gamebann,$shoutbann,$forenbann,$anonid){

	if($action == '' or $action == NULL){
		?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
	<input type="hidden" name="choose" value="bann"></input> <input
		type="hidden" name="action" value="search"></input>
	<table>
		<tr>
			<th colspan="2">User auswählen</th>
		</tr>
		<tr>
			<td>Username</td>
			<td><input name="username"></input>
			</td>
		</tr>
		<tr>
			<td>Anonid (nur die Zahlen!)</td>
			<td><input name="anonid"></input>
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="suchen">
			</td>
		</tr>
		<tr>
			<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=bann">Zur
					&Uuml;bersicht</a><br> <a
				href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
			</td>
		</tr>
	</table>
</form>
		<?php
	} else if(is_numeric($anonid)){
		db_query('UPDATE anon_chatter set muted = 1 where anon_id = '.$anonid);
		?>
<table>
	<tr>
		<td>anon_<?php echo $anonid; ?> wurde im Chat gebannt!</td>
	</tr>
	<tr>
		<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=bann">Zur
				&Uuml;bersicht</a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum
				Hauptmenu</a>
		</td>
	</tr>
</table>
		<?php
	} else if($action == "search"){
		// Auslagern ;D
		$qry = db_query('Select nickname, id from user where nickname like \''.$username.'%\'');
		?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
	<input type="hidden" name="choose" value="bann"></input> <input
		type="hidden" name="action" value="finish_him"></input>
	<table>
		<tr>
			<th colspan="2">Userstatus bearbeiten</th>
		</tr>
		<tr>
			<td>Username</td>
			<td><select name="opfer">
			<?php
			while($result = mysqli_fetch_assoc($qry)){
				echo '<option value="'.$result['id'].'">'.$result['nickname'].'</option>';
			}
			?>
			</select>
			</td>
		</tr>
		<tr>
			<td>Account</td>
			<td><input type="radio" name="gamebann" value="1">Account sperren<br>
				<input type="radio" name="gamebann" value="2">Account entsperren<br>
			</td>
		</tr>
		<tr>
			<td>Shoutbox</td>
			<td><input type="radio" name="shoutbann" value="1">Shoutbox mute<br>
				<input type="radio" name="shoutbann" value="2">Shoutbox demute<br>
			</td>
		</tr>
		<tr>
			<td>Forum</td>
			<td><input type="radio" name="forenbann" value="1"
				disabled="disabled">Forenaccout sperren<br> <input type="radio"
				name="forenbann" value="2" disabled="disabled">Forenaccount
				entsperren<br>
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit"
				value="Banns zuweisen/l&ouml;schen">
			</td>
		</tr>
		<tr>
			<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=bann">Zur
					&Uuml;bersicht</a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum
					Hauptmenu</a>
			</td>
		</tr>
	</table>
</form>

			<?php
	}	else if($action == "finish_him"){
		if($gamebann == 1){
			logaction('Nutzer mit der id '.$opfer.' wurde gebannt.');
			bannAccount($opfer);
		} else if($gamebann == 2){
			logaction('Nutzer mit der id '.$opfer.' wurde entbannt.');
			unbannAccount($opfer);
		}
		if($shoutbann == 1){
			logaction('Nutzer mit der id '.$opfer.' wurde stumm geschaltet.');
			setSpamblock($opfer);
		} else if($shoutbann == 2){
			logaction('Stummschatung des Nutzers mit der id '.$opfer.' wurde abgebrochen.');
			releaseSpamblock($opfer);
		}
		?>
<table>
	<tr>
		<td>&Auml;nderungen &uuml;bernommen!</td>
	</tr>
	<tr>
		<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=bann">Zur
				&Uuml;bersicht</a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum
				Hauptmenu</a>
		</td>
	</tr>
</table>
		<?php
	}
}
?>