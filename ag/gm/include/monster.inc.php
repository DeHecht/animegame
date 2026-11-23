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
function displayQuestNPC($action, $name, $starke, $speed, $verteidigung, $ausdauer, $hp, $mp, $level, $orte,$id, $anzahl, $confirm, $glueck){
	if($action != 'delete' && $action != 'change' && $action != 'create'){
		$sql = 'SELECT id , name FROM `quest_monster` order by name asc';
		$result = db_query($sql);
		?>
			<table>
				<tr>
					<th colspan="2">
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=qnpc&action=create">Neues Monster erstellen!!</a>
					</th>
				</tr>
				<tr>
					<th align="center">Name</th>
					<th align="center" colspan="2">Aktion</th>
				</tr>
				<?php
				while($res = mysqli_fetch_assoc($result)){
				?>
				<tr>
					<td><?php echo $res['name']; ?></td>
					<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=qnpc&action=change&id=<?php echo $res['id']; ?>">&auml;ndern</a></td>
					<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=qnpc&action=delete&id=<?php echo $res['id']; ?>">l&ouml;schen</a></td>
				</tr>
				<?php
				}
				?>
				<tr>
					<td>
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
					</td>
				</tr>
			</table>
		<?php
	} else if($action == 'delete'){
			if($confirm == null){
				echo 'Das Monster mit der ID '.$id.' soll geloescht werden<br>';
				echo '<a href="'.$_SERVER['PHP_SELF'].'?choose=qnpc&action=delete&id='.$id.'&confirm=yes">L&ouml;schen!!</a><br>';
			} else{
				$sql = 'DELETE FROM `quest_monster` where id = '.$id;
				$result = db_query($sql);
				logaction('Das Monster mit der ID '.$id.' wurde geloescht!!');
				echo 'Das Monster mit der ID '.$id.' wurde gel&ouml;scht<br>';
			}
		?>
		<a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=qnpc">Zur &Uuml;bersicht</a><br>
		<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a><br>
		<?php
	} else if($action == 'change'){
		
		if($name != null){
			$sql = 'Update `quest_monster` set name = \''.$name.'\', starke = '.$starke.', speed =  '.$speed.', verteidigung = '.$verteidigung.', ausdauer = '.$ausdauer.', hp = '.$hp.', mp = '.$mp.', anzahl = \''.$anzahl.'\', `level` =  '.$level.', glueck = '.$glueck.' where id = '.$id;

			db_query('Delete from quest_monster_orte where monsterid = '.$id);
			for($i=0;$i<count($orte);$i++){
				db_query('Insert into quest_monster_orte(ortid, monsterid) values('.$orte[$i].', '.$id.')');
			}

			logaction('Das Monster mit dem Namen '.$name.' wurde angepasst!!');
			$query = db_query($sql);
		}
		// Erstmal die Orte aufzaehlen, in der das Monster zu finden ist
		$sql = 'Select ortid from quest_monster_orte where monsterid = '.$id;
		$qry = db_query($sql);
		while($row = mysqli_fetch_assoc($qry)){
			$monster_orte[$row['ortid']] = $row;
		}
		
		// Jetzt die Orte auslesen die zu Bereisen sind
		$sql = 'Select * from quest_ort order by stadt';
		$qry = db_query($sql);
		while($row = mysqli_fetch_assoc($qry)){
			$orte_array[] = $row;
		}


		$sql = 'SELECT * FROM `quest_monster` where id = '.$id;
		$result = db_query($sql);
		$res = mysqli_fetch_assoc($result);
		?>
		<form action="" method="POST">
			<input name="choose" value="qnpc" type="hidden"></input>
			<input name="action" value="change" type="hidden"></input>
			<input name="id" value="<?php echo $id ?>" type="hidden"></input>
			<table>
				<tr>
					<th>Name:</th>
					<td><input name="npc_name" value="<?php echo $res['name']; ?>"></input></td>
				</tr>
				<tr>
					<th>Starke:</th>
					<td><input name="starke" value="<?php echo $res['starke']; ?>"></input></td>
				</tr>
				<tr>
					<th>Speed:</th>
					<td><input name="speed" value="<?php echo $res['speed']; ?>"></input></td>
				</tr>
				<tr>
					<th>Verteidigung:</th>
					<td><input name="verteidigung" value="<?php echo $res['verteidigung']; ?>"></input></td>
				</tr>
				<tr>
					<th>Ausdauer:</th>
					<td><input name="ausdauer" value="<?php echo $res['ausdauer']; ?>"></input></td>
				</tr>
				<tr>
					<th>Gl&uuml;ck:</th>
					<td><input name="glueck" value="<?php echo $res['glueck']; ?>"></input></td>
				</tr>
				<tr>
					<th>HP:</th>
					<td><input name="hp" value="<?php echo $res['hp']; ?>"></input></td>
				</tr>
				<tr>
					<th>MP:</th>
					<td><input name="mp" value="<?php echo $res['mp']; ?>"></input></td>
				</tr>
				<tr>
					<th>Anzahl:</th>
					<td><input name="anzahl" value="<?php echo $res['anzahl']; ?>"></input></td>
				</tr>
				<tr>
					<th>Exp:</th>
<?php
				$exp = ($res['starke']+$res['speed']+$res['verteidigung']+$res['glueck']+$res['ausdauer'])+$res['hp']/10;
				$exp *= 30; // Mal Faktor
?>

					<td><input name="exp" disabled="disabled" value="<?php echo $exp; ?>"></input></td>
				</tr>
				<tr>
					<th>Level:</th>
					<td><input name="level" value="<?php echo $res['level']; ?>"></input></td>
				</tr>
				<tr>
					<th>Orte:</th>
					<td><?php
						// Ein Checkboxenfeld, womit die Orte ausgewaehlt werden koennen
						for($i=0;$i<count($orte_array);$i++){
							if($monster_orte[$orte_array[$i]['id']] != null){
								echo $orte_array[$i]['stadt'].': <input type="checkbox" class="input" name="orte[]" value="'.$orte_array[$i]['id'].'" checked="checked" /><br>';
							} else {
								echo $orte_array[$i]['stadt'].': <input type="checkbox" class="input" name="orte[]" value="'.$orte_array[$i]['id'].'" /><br>';
							}
						}
					
					?></td>
				</tr>
				<tr>
					<td>
						<input type="submit" value="update"></input>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=qnpc">Zur &Uuml;bersicht</a><br>
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a><br>
					</td>
				</tr>
			</table>
		</form>
		<?php
	} else if($action == 'create'){
		if($name != null){
			$sql = 'Insert into `quest_monster`(name, starke, speed, verteidigung, ausdauer, hp, mp, anzahl, `level`) values(\''.$name.'\', '.$starke.', '.$speed.', '.$verteidigung.', '.$ausdauer.', '.$hp.', '.$mp.', \''.$anzahl.'\', '.$level.')';
			$query = db_query($sql);
			$id = mysqli_fetch_assoc(db_query('Select id from quest_monster where name = \''.$name.'\''));
			$monsterid = $id['id'];
			for($i=0;$i<count($orte);$i++){
				db_query('Insert into quest_monster_orte(ortid, monsterid) values('.$orte[$i].', '.$monsterid.')');
			}
			logaction('Das Monster mit dem Namen '.$name.' wurde erstellt!!');
			echo "Monster erstellt<br>";
			?>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=qnpc">Zur &Uuml;bersicht</a><br>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a><br>
			<?php
			return;
		}
		// Jetzt die Orte auslesen die zu Bereisen sind
		$sql = 'Select * from quest_ort order by stadt';
		$qry = db_query($sql);
		while($row = mysqli_fetch_assoc($qry)){
			$orte_array[] = $row;
		}
		
		?>
		<form action="" method="POST">
			<input name="choose" value="qnpc" type="hidden"></input>
			<input name="action" value="create" type="hidden"></input>
			<input name="id" value="<?php echo $id ?>" type="hidden"></input>
			<table>
				<tr>
					<th>Name:</th>
					<td><input name="npc_name" value=""></input></td>
				</tr>
				<tr>
					<th>Starke:</th>
					<td><input name="starke" value=""></input></td>
				</tr>
				<tr>
					<th>Speed:</th>
					<td><input name="speed" value=""></input></td>
				</tr>
				<tr>
					<th>Verteidigung:</th>
					<td><input name="verteidigung" value=""></input></td>
				</tr>
				<tr>
					<th>Ausdauer:</th>
					<td><input name="ausdauer" value=""></input></td>
				</tr>
				<tr>
					<th>HP:</th>
					<td><input name="hp" value=""></input></td>
				</tr>
				<tr>
					<th>MP:</th>
					<td><input name="mp" value=""></input></td>
				</tr>
				<tr>
					<th>Anzahl:</th>
					<td><input name="anzahl" value=""></input></td>
				</tr>
				<tr>
					<th>Level:</th>
					<td><input name="level" value=""></input></td>
				</tr>
				<tr>
					<th>Orte:</th>
					<td><?php
						// Ein Checkboxenfeld, womit die Orte ausgewaehlt werden koennen
						for($i=0;$i<count($orte_array);$i++){
							echo $orte_array[$i]['stadt'].': <input type="checkbox" class="input" name="orte[]" value="'.$orte_array[$i]['id'].'" /><br>';
						}
					
					?></td>
				</tr>
				<tr>
					<td>
						<input type="submit" value="insert"></input>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=qnpc">Zur &Uuml;bersicht</a><br>
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a><br>
					</td>
				</tr>
			</table>
		</form>
		<?php
	}
}
?>