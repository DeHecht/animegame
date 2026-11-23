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
function displayAttackenMenu($action, $attackname, $attackid, $depth, $name, $starke, $verteidigung, $speed, $hp, $mp, $rassen, $level, $geld, $options, $type, $info, $req_atk, $req_lvl, $Frucht, $runden, $maxlvl, $request) {
	if($action === NULL){
		?>
<table>
	<tr>
		<th>Attacken</th>
	</tr>
	<tr>
		<td><a
			href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=attacken&action=create">Attacke
				erstellen</a>
		</td>
	</tr>
	<tr>
		<td><a
			href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=attacken&action=edit">Attacke
				editieren</a>
		</td>
	</tr>
	<tr>
		<td><a
			href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=attacken&action=delete">Attacke
				l&ouml;schen</a>
		</td>
	</tr>
	<tr>
		<th>Attackenset-Conditions</th>
	</tr>
	<tr>
		<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=attacken">Condition
				erstellen</a>
		</td>
	</tr>
	<tr>
		<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=attacken">Condition
				editieren</a>
		</td>
	</tr>
	<tr>
		<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=attacken">Condition
				l&ouml;schen</a>
		</td>
	</tr>
	<tr>
		<td><br>
		</td>
	</tr>
	<tr>
		<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a><br>
		</td>
	</tr>
</table>
		<?php
	} else if(($action == 'delete' || $action == 'edit') && $attackname === NULL && $attackid === NULL){
		searchfirstAttack($action);
	} else if(($action == 'delete' || $action == 'edit') && $attackid === NULL){
		searchsecondAttack($action, $attackname);
	} else if(($action == 'create' || $action == 'edit') && $depth === NULL){
		createOrEditAttack($action, $attackid);
	} else if($action == 'create' && $depth == 1){
		createAttack($attackname, $Frucht, $starke, $verteidigung, $speed, $hp, $mp, $rassen, $level, $geld, $options, $type, $info, $req_atk, $req_lvl, $runden, $maxlvl);
	} else if($action == 'delete' && $depth >= 1){
		deleteAttack($attackid, $depth);
	} else if($action == 'edit' && $depth == 1){
		editAttack($attackname, $attackid, $Frucht, $starke, $verteidigung, $speed, $hp, $mp, $rassen, $level, $geld, $options, $type, $info, $req_atk, $req_lvl, $runden, $maxlvl);
	}
}

function searchfirstAttack($action) {
	?>
<form action="" method="POST">
	<input name="choose" value="attacken" type="hidden"></input> <input
		name="action" value="<?php echo $action; ?>" type="hidden"></input>
	<table>
		<tr>
			<th colspan="2">Attacke suchen</th>
		</tr>
		<tr>
			<td><input name="attackname">
			</td>
			<td><input type="submit" name="suche">
			</td>
		</tr>
		<tr>
			<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a><br>
			</td>
		</tr>
	</table>
</form>
	<?php
}

function searchsecondAttack($action, $attackname) {
	$qry = db_query('Select name,id from attacken where name like \'%'.$attackname.'%\'');
	?>
<form action="" method="POST">
	<input name="choose" value="attacken" type="hidden"></input> <input
		name="action" value="<?php echo $action; ?>" type="hidden"></input>
		<?php if($action == 'delete'){ echo '<input name="depth" value="1" type="hidden"></input>'; }?>
	<table>
		<tr>
			<th colspan="2">Attacke ausw&auml;hlen</th>
		</tr>
		<tr>
			<td><select name="attackid">
			<?php
			while($row = mysqli_fetch_assoc($qry)){
				echo '<option value="'.$row['id'].'">'.$row['name'].'</option>\n';
			}
			?>
			</select>
			</td>
			<td><input type="submit" name="ausw&auml;hlen">
			</td>
		</tr>
		<tr>
			<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a><br>
			</td>
		</tr>
	</table>
</form>
			<?php
}

function createOrEditAttack($action, $attackid) {
	if($action == 'edit'){
		$row = mysqli_fetch_assoc(db_query('Select * from attacken where id ='.$attackid));
	}
	//Alle attacken auslesen
	$att_qry = db_query('SELECT id,name FROM attacken WHERE info not like \'%NPC%\' ORDER BY name');
	$available_attacks = array();
	while($attacks = mysqli_fetch_assoc($att_qry)) {
		$available_attacks[] = array($attacks['id'], $attacks['name']);
	}
	$used_attacks = explode(',', $row['req_atk']);

	?>
<form action="" method="POST">
	<input name="choose" value="attacken" type="hidden"></input> <input
		name="action" value="<?php echo $action; ?>" type="hidden"></input> <input
		name="attackid" value="<?php echo $attackid; ?>" type="hidden"></input>
	<input name="depth" value="1" type="hidden"></input>
	<table>
		<tr>
			<th colspan="2">Attacke</th>
		</tr>
		<tr>
			<td>ID:</td>
			<td><?php echo $attackid; ?>
			</td>
		</tr>
		<tr>
			<td>Name:</td>
			<td><input name='attackname'
				value='<?php echo $row['name']===NULL?'Attackenname':$row['name']; ?>'></input>
			</td>
		</tr>
		<tr>
			<td>St&auml;rke:</td>
			<td><input name='starke'
				value='<?php echo $row['starke']===NULL?0:$row['starke']; ?>'></input>
			</td>
		</tr>
		<tr>
			<td>Verteidigung:</td>
			<td><input name='verteidigung'
				value='<?php echo $row['verteidigung']===NULL?0:$row['verteidigung']; ?>'></input>
			</td>
		</tr>
		<tr>
			<td>Speed:</td>
			<td><input name='speed'
				value='<?php echo $row['speed']===NULL?0:$row['speed']; ?>'></input>
			</td>
		</tr>
		<tr>
			<td>HP:</td>
			<td><input name='hp'
				value='<?php echo $row['hp']===NULL?0:$row['hp']; ?>'></input>
			</td>
		</tr>
		<tr>
			<td>MP:</td>
			<td><input name='mp'
				value='<?php echo $row['mp']===NULL?0:$row['mp']; ?>'></input>
			</td>
		</tr>
		<tr>
			<td>Rassen:</td>
			<td><input name='rassen'
				value='<?php echo $row['rassen']===NULL?0:$row['rassen']; ?>'></input>
			</td>
		</tr>
		<tr>
			<td>Level:</td>
			<td><input name='level'
				value='<?php echo $row['level']===NULL?0:$row['level']; ?>'></input>
			</td>
		</tr>
		<tr>
			<td>Geld:</td>
			<td><input name='geld'
				value='<?php echo $row['geld']===NULL?0:$row['geld']; ?>'></input>
			</td>
		</tr>
		<tr>
			<td>Type:</td>
			<td><select name="type">
			<?php
			$qry = db_query('Select name FROM attackentyp;');
			while ($result = mysqli_fetch_assoc($qry)) {
				if($row['type'] == $result['name']){
					echo '<option value="'.$result['name'].'" selected="selected">'.$result['name'].'</option>';
				} else{
					echo '<option value="'.$result['name'].'">'.$result['name'].'</option>';
				}
			}
			?>
			</select>
			</td>
		</tr>
		<tr>
			<td>Options:</td>
			<td><input name='options'
				value='<?php echo $row['options']===NULL?'{}':$row['options']; ?>'></input>
			</td>
		</tr>
		<tr>
			<td>Info:</td>
			<td><input name='info'
				value='<?php echo $row['info']===NULL?'No Info available yet!':$row['info']; ?>'></input>
			</td>
		</tr>
		<tr>
			<td>Required Attacks:</td>
			<td><select name="req_atk[0]">
			<?php
			if($used_attacks[0] == 0){
				echo '<option value="0" selected="selected">-Keine-</option>';
			} else {
				echo '<option value="0">-Keine-</option>';
			}
			foreach ($available_attacks as $attack) {
				if($attack[0] == $used_attacks[0]){
					echo '<option value="'.$attack[0].'" selected="selected">'.$attack[1].'</option>';
				} else{
					echo '<option value="'.$attack[0].'">'.$attack[1].'</option>';
				}
			}
			?>
			</select>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><select name="req_atk[1]">
			<?php
			if($used_attacks[1] == 0){
				echo '<option value="0" selected="selected">-Keine-</option>';
			} else {
				echo '<option value="0">-Keine-</option>';
			}

			foreach ($available_attacks as $attack) {
				if($attack[0] == $used_attacks[1]){
					echo '<option value="'.$attack[0].'" selected="selected">'.$attack[1].'</option>';
				} else{
					echo '<option value="'.$attack[0].'">'.$attack[1].'</option>';
				}
			}
			?>
			</select>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><select name="req_atk[2]">
			<?php
			if($used_attacks[2] == 0){
				echo '<option value="0" selected="selected">-Keine-</option>';
			} else {
				echo '<option value="0">-Keine-</option>';
			}

			foreach ($available_attacks as $attack) {
				if($attack[0] == $used_attacks[2]){
					echo '<option value="'.$attack[0].'" selected="selected">'.$attack[1].'</option>';
				} else{
					echo '<option value="'.$attack[0].'">'.$attack[1].'</option>';
				}
			}
			?>
			</select>
			</td>
		</tr>
		<tr>
			<td>Required Level:</td>
			<td><input name='req_lvl'
				value='<?php echo $row['req_lvl']===NULL?0:$row['req_lvl']; ?>'></input>
			</td>
		</tr>
		<tr>
			<td>Frucht:</td>
			<td><select name="Frucht">
			<?php
			// Fuers dropdown
			$f_sql = 'SELECT id, item FROM wochen_markt WHERE type = \'Teufels Frucht\' order by item';
			$f_qry = db_query($f_sql);

			if($row['Frucht'] === NULL || $row['Frucht'] == 0){
				echo '<option value="0" selected="selected">-Keine-</option>';
			} else {
				echo '<option value="0">-Keine-</option>';
			}
			while($f_row = mysqli_fetch_assoc($f_qry)){
				if($f_row['id'] == $row['Frucht']){
					echo '<option value="'.$f_row['id'].'" selected="selected">'.$f_row['item'].'</option>';
				} else{
					echo '<option value="'.$f_row['id'].'">'.$f_row['item'].'</option>';
				}
			}

			?>
			</select>
			</td>
		</tr>
		<tr>
			<td>Runden:</td>
			<td><input name='runden'
				value='<?php echo $row['runden']===NULL?0:$row['runden']; ?>'></input>
			</td>
		</tr>
		<tr>
			<td>Maxlvl:</td>
			<td><input name='maxlvl'
				value='<?php echo $row['maxlvl']===NULL?0:$row['maxlvl']; ?>'></input>
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value='<?php echo $action; ?>'></input>
			</td>
		</tr>
		<tr>
			<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a><br>
			</td>
		</tr>
	</table>
</form>
			<?php
}

function createAttack($attackname, $Frucht, $starke, $verteidigung, $speed, $hp, $mp, $rassen, $level, $geld, $options, $type, $info, $req_atk, $req_lvl, $runden, $maxlvl) {
	json_decode($options, true);
	if(JSON_ERROR_NONE != json_last_error()) {
		echo "<br>";
		echo "Attacke konnte nicht erstellt werden, syntax f&ouml;r options ist nicht korrekt!";
		echo displayHistoryBackLink();
		return;
	}

	if($Frucht != 0){
		$sql = 'INSERT into attacken set name = \''.$attackname.'\', starke = '.$starke.', verteidigung = '.$verteidigung.', speed = '.$speed.', hp = '.$hp.', mp = '.$mp.', rassen = \''.$rassen.'\', level = '.$level.', geld = '.$geld.', type = \''.$type.'\', info = \''.$info.'\', req_atk = \''.$req_atk.'\', req_lvl = \''.$req_lvl.'\', Frucht = '.$Frucht.', runden = '.$runden.', maxlvl = '.$maxlvl.', options = \''.addslashes($options).'\'';
	} else {
		$sql = 'INSERT into attacken set name = \''.$attackname.'\', starke = '.$starke.', verteidigung = '.$verteidigung.', speed = '.$speed.', hp = '.$hp.', mp = '.$mp.', rassen = \''.$rassen.'\', level = '.$level.', geld = '.$geld.', type = \''.$type.'\', info = \''.$info.'\', req_atk = \''.$req_atk.'\', req_lvl = \''.$req_lvl.'\', runden = '.$runden.', maxlvl = '.$maxlvl.', options = \''.addslashes($options).'\'';
	}
	//echo $sql.'<br>';
	if(!db_query($sql)){
		echo "Fehler beim Erstellen der Attacke: &Uuml;berpr&uuml;fe deine Eingabe und melde erst DANACH dieses Statement einem Entwickler => $sql";
		return;
	}
	logaction('Attacke '.$attackname.' wurde erstellt!');
	?>
Attacke sollte nun erstellt sein!!
<br>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
<br>
	<?php

}

function deleteAttack($attackid, $depth) {
	if($depth == 4){
		$sql = 'Delete from attacken where id = '.$attackid;
		//			echo 'Debugnachricht: '.$sql.'<br>';
		db_query($sql);
		db_query('Delete from lernen where at_id = '.$attackid);
		logaction('Attacke mit id = '.$attackid.' wurde geloescht!');
			
	} else{
		for($i=0;$i<$depth;$i++){
			$wirklich .= 'wirklich ';
		}
		?>
Sind sie sich
		<?php echo $wirklich; ?>
sicher??
<a
	href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=attacken&action=delete&attackid=<?php echo $attackid; ?>&depth=<?php echo $depth+1; ?>">ja</a>
<br>
		<?php
	}
	?>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
<br>
	<?php
}

function editAttack($attackname, $attackid, $Frucht, $starke, $verteidigung, $speed, $hp, $mp, $rassen, $level, $geld, $options, $type, $info, $req_atk, $req_lvl, $runden, $maxlvl) {
	$row = mysqli_fetch_assoc(db_query('Select * from attacken where id ='.$attackid));
	$req_atk = implode(',', $req_atk);

	json_decode($options, true);
	if(JSON_ERROR_NONE != json_last_error()) {
		echo "<br>";
		echo "Attacke konnte nicht erstellt werden, syntax f&ouml;r options ist nicht korrekt!";
		echo "<br>";
		echo displayHistoryBackLink();
		return;
	}


	if($Frucht != 0){
		$sql = 'UPDATE attacken set name = \''.$attackname.'\', starke = '.$starke.', verteidigung = '.$verteidigung.', speed = '.$speed.', hp = '.$hp.', mp = '.$mp.', rassen = \''.$rassen.'\', level = '.$level.', geld = '.$geld.', type = \''.$type.'\', info = \''.$info.'\', req_atk = \''.$req_atk.'\', req_lvl = \''.$req_lvl.'\', Frucht = '.$Frucht.', runden = '.$runden.', maxlvl = '.$maxlvl.', options = \''.$options.'\' where id = '.$attackid;
	} else {
		$sql = 'UPDATE attacken set name = \''.$attackname.'\', starke = '.$starke.', verteidigung = '.$verteidigung.', speed = '.$speed.', hp = '.$hp.', mp = '.$mp.', rassen = \''.$rassen.'\', level = '.$level.', geld = '.$geld.', type = \''.$type.'\', info = \''.$info.'\', req_atk = \''.$req_atk.'\', req_lvl = \''.$req_lvl.'\', Frucht = NULL, runden = '.$runden.', maxlvl = '.$maxlvl.', options = \''.$options.'\' where id = '.$attackid;
	}
	if($row['name'] != $attackname){
		db_query('Update lernen set name = \''.$attackname.'\' WHERE at_id ='.$attackid);
	}
	//echo 'Debugnachricht: '.$sql.'<br>';
	if(!db_query($sql)){
		echo "Fehler beim Editieren der Attacke: &Uuml;berpr&uuml;fe deine Eingabe und melde erst DANACH dieses Statement einem Entwickler => $sql";
		return;
	}
	logaction('Attacke mit dem jetzigen Namen '.$attackname.' wurde editiert!');

	?>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=attacken">Zum
	Attackenmenü</a>
,
<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
<br>
	<?php
}

function displayAttackenTypen($request) {
	$colspan = 3;
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="GET">'."\n";
	echo "\t".'<input type="hidden" name="choose" value="attackentypen"></input>'."\n";
	echo "\t".'<input type="hidden" name="attid" value="'.$request['attid'].'"></input>'."\n";
	echo "\t".'<table width="80%" border="0">'."\n";
	switch ($request['action']) {
		case ACTION_EDIT:
			if($request['task'] == TASK_EDIT) {
				editAttackentyp($request['attid'], $request['data']);
				displayAttackenTypenInfo(ACTION_EDIT, TASK_EDIT, $request['attid']);
			} else {
				displayAttackenTypenInfo(ACTION_EDIT, TASK_EDIT, $request['attid']);
			}
			break;
		case ACTION_CREATE:
			echo "\t".'<input type="hidden" name="action" value="'.ACTION_CREATE.'"></input>'."\n";
			if($request['task'] == TASK_CREATE) {
				createAttackentyp($request['data']);
			} else {
				displayAttackenTypenInfo(ACTION_CREATE, TASK_CREATE, $request['attid']);
			}
			break;
		case 'delete':
			if($request['task'] == 'dodelete') {
				deleteAttackentyp($request['attid']);
			} else {
				displayDeleteAttackentyp($request['attid']);
			}
			break;
		default:
			displayAttackenTypenOverview($request['page']);
	}
	echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center"><a href="'.$_SERVER['PHP_SELF'].'">Zum Hauptmenu</a></td></tr>'."\n";
	echo "\t".'</table>'."\n";
	echo '</form>'."\n";
}

function displayAttackenTypenOverview($page) {
	$d_array = array('id', 'name', 'anzahl', 'farbe', 'kategorie');
	$desc_array = array('ID', 'Name', 'Anzahl', 'Farbe', 'Kategorie');

	$sql = 'SELECT '.join($d_array,',').' FROM attackentyp ORDER BY name LIMIT '.($page*20).', 20';
	$qry = db_query($sql);
	$colspan = count($d_array)+1;
	echo "\t".'<tr><td colspan="'.$colspan.'"><h1 style="text-align:center">Übersicht</h1></td></tr>'."\n";;
	echo "\t".'<tr>'."\n";
	for($i=0;$i<count($d_array);$i++){
		echo "\t\t".'<td>'.$desc_array[$i].'</td>'."\n";
	}
	echo "\t\t".'<td style="text-align:center">Bearbeiten</td>'."\n";
	echo "\t".'</tr>'."\n";

	echo "\t".'<tr><td colspan="'.$colspan.'"><hr></td></tr>'."\n";

	while($row = mysqli_fetch_assoc($qry)){
		echo "\t".'<tr>'."\n";
		for($i=0;$i<count($d_array);$i++){
			echo "\t\t".'<td>'.$row[$d_array[$i]].'</td>'."\n";
		}

		echo "\t\t".'<td style="text-align:center">'."\n";

		// Echo the delete and the edit button :)
		echo "\t\t\t".'<a href="'.$_SERVER['PHP_SELF'].'?choose=attackentypen&action=edit&attid='.$row[$d_array[0]].'">Edit</a>, '."\n";
		echo "\t\t\t".'<a href="'.$_SERVER['PHP_SELF'].'?choose=attackentypen&action=delete&attid='.$row[$d_array[0]].'">Delete</a>'."\n";

		echo "\t\t".'</td>'."\n";
		echo "\t".'</tr>'."\n";

	}
	$total = mysqli_num_rows(db_query('SELECT '.join($d_array,',').' FROM attackentyp'));
	$url = '<a href="'.$_SERVER['PHP_SELF'].'?choose=npc&page=###PAGE###">###LABEL###</a>';
	echo "\t".'<tr><td colspan="'.$colspan.'"><hr></td></tr>'."\n";
	echo "\t".'<tr><td colspan="'.$colspan.'">'.displayPagelinksNew(20, $total, $page, $url).'</td></tr>'."\n";
	echo "\t".'<tr><td colspan="'.$colspan.'"><hr></td></tr>'."\n";
	echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center"><a href="'.$_SERVER['PHP_SELF'].'?choose=attackentypen&action=create">Create a new Type</a></td></tr>'."\n";
}

function displayAttackenTypenInfo($action, $task, $attid) {
	//Form-Variablen
	echo "\t".'<input type="hidden" name="action" value="'.$action.'"></input>'."\n";
	echo "\t".'<input type="hidden" name="task" value="'.$task.'"></input>'."\n";
	//initialisierung der Variablen für Inhalt
	$d_array = array('id', 'name', 'anzahl', 'farbe', 'kategorie');
	$desc_array = array('ID', 'Name', 'Anzahl', 'Farbe', 'Kategorie');

	//initialisieren falls editiert wird (dort existieren schon Inhalte)
	if($task == TASK_EDIT) {
		$sql = 'SELECT * FROM attackentyp WHERE id = '.$attid.';';
		$qry = db_query($sql);
		$row = mysqli_fetch_assoc($qry);
	}

	$colspan = 2;

	if($task == TASK_EDIT) {
		echo "\t".'<tr><td colspan="'.$colspan.'"><h1 style="text-align:center">Attackentyp bearbeiten</h1></td></tr>'."\n";
	} else {
		echo "\t".'<tr><td colspan="'.$colspan.'"><h1 style="text-align:center">Attackentyp erstellen</h1></td></tr>'."\n";
	}

	echo "\t\t".'<tr><td>'.$desc_array[0].'</td><td style="text-align:center">'.$row[$d_array[0]].'</td></tr>'."\n";
	$j = 0;
	//allgemeine informationen
	for($i = 1;$i<count($d_array);$i++){
		echo "\t\t".'<tr><td>'.$desc_array[$i].'</td><td style="text-align:center"><input name="data['.$d_array[$i].']" value="'.debbcode($row[$d_array[$i]]).'"></input></td></tr>'."\n";
	}

	//menü
	echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center"><input type="submit" value="speichern"></td></tr>'."\n";
	echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center"><a href="'.$_SERVER['PHP_SELF'].'?choose=attackentypen">Attackentyp Übersicht</a></td></tr>'."\n";
}

function editAttackentyp($attid, $data) {
	$d_array = array('name', 'anzahl', 'farbe', 'kategorie');

	$sql = 'UPDATE attackentyp SET ';

	for($i=0;$i<count($d_array);$i++){
		$sql .= $d_array[$i].' = \''.encodeNoHTMLWithBB($data[$d_array[$i]]).'\'';
		if($i < count($d_array) - 1) {
			$sql .= ', ';
		}
	}

	$sql .= ' WHERE id=\''.$attid.'\'';
	echo $sql;
	$u = db_query($sql);
	$log = 0;

	if(db_affected_rows() == 0 || $u === FALSE) {
		echo "\t\t\t".'<tr><td colspan="2" style="color:red">Fehler aufgetreten!</td></tr>';
	} else{
		echo "\t\t\t".'<tr><td colspan="2">Änderungen wurden übernommen!</td></tr>';
		$log++;
	}

	if($log >= 1) {
		logaction('Ein Attackentyp wurde geändert (id = '.$attid.','.encodeNoHTMLWithBB(join($data,',')).')');
	}
}

function createAttackentyp($data) {
	if($data['name'] != null) {
		$sql = 'Insert into attackentyp (name, anzahl, farbe, kategorie) VALUES (';
		$sql .= '\''.$data['name'].'\',';
		if($data['anzahl'] == null) {
			$data['anzahl'] = 1;
		}
		$sql .= $data['anzahl'].',';
		if($data['farbe'] == null) {
			$data['farbe'] = 'white';
		}
		$sql .= '\''.$data['farbe'].'\',';
		if($data['kategorie'] == null) {
			$sql .= 'null';
		} else {
			$sql .= '\''.$data['kategorie'].'\'';
		}
		$sql .= ');';
		$identifier = db_query($sql);
		if($identifier == FALSE){
			echo "\t".'<tr><td colspan="2" style="text-align:center">Es gab Probleme beim erstellen des Attacktyps!</td></tr>'."\n";
		} else {
			echo "\t".'<tr><td colspan="2" style="text-align:center">Attackentyp '.$data['name'].' wurde erfolgreich erstellt!</td></tr>'."\n";
		}
	}
	echo "\t".'<tr><td colspan="2" style="text-align:center"><a href="'.$_SERVER['PHP_SELF'].'?choose=attackentypen">Attackentypen Übersicht</a></td></tr>'."\n";
}

function deleteAttackentyp($attid) {
	$colspan = 1;
	$sql = 'DELETE FROM attackentyp WHERE id = '.$attid.';';
	$qry = db_query($sql);
	if($qry === FALSE || db_affected_rows() <= 0){
		echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center;color:red">Der Attackentyp '.$row['name'].' konnte nicht gelöscht werden!</td></tr>'."\n";;
	} else {
		echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center">Der Attackentyp '.$row['name'].' wurde gelöscht!</td></tr>'."\n";;
	}
	echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center"><a href="'.$_SERVER['PHP_SELF'].'?choose=attackentypen">Attackentypen Übersicht</a></td></tr>'."\n";
}

function displayDeleteAttackentyp($attid) {
	$colspan = 1;

	$sql = 'SELECT * FROM attackentyp WHERE id = '.$attid;
	$qry = db_query($sql);
	$row = mysqli_fetch_assoc($qry);
	echo "\t".'<tr><td colspan="'.$colspan.'"><h1 style="text-align:center">Attackentyp löschen</h1></td></tr>'."\n";

	echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center">Soll der Attackentyp '.$row['name'].' wirklich gelöscht werden?</td></tr>'."\n";
	echo "\t".'<tr><td style="text-align:center"><a href="'.$_SERVER['PHP_SELF'].'?choose=attackentypen&action=delete&task=dodelete&attid='.$attid.'">ja</a>, <a href="'.$_SERVER['PHP_SELF'].'?choose=attackentypen&action=">nein</a></td></tr>'."\n";
}

?>
