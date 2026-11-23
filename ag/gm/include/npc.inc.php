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
include_once(ROOT_PATH.'/include/attackenset.inc.php');
include_once(ROOT_PATH.'/include/erstellfunctions.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/sqlwrapper.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');


define('ACTION_EDIT', 'edit');
define('ACTION_CREATE', 'create');
define('TASK_EDIT', 'dochange');
define('TASK_CREATE', 'docreate');
defineIfNotDefined('NPC_USER_NAME', 'animegame-npc');

function getDropItemsAsOptions(){
	$result = NULL;
	$index = 0;
	// Die etwas abgewandelten SQL-Querys aus dem auktion_functions.php
	$query = 'SELECT id, name, \'ware\' as tablename FROM item i WHERE s_type = \'Trank\'
					union
			  SELECT id, name, \'sp_ware\' as tablename FROM sp_item i
					union
			  SELECT id, item AS name, \'wochen_ware\' as tablename FROM wochen_markt w;';

	$qry = db_query($query);
	// Damit waeren alle noetigen Datenbankaufrufe erledigt!
	while($row = mysqli_fetch_assoc($qry)){
		$result[$index++] = '<option value=\''.$row['id'].','.$row['tablename'].'\'>'.$row['name'].'</option>';
	}

	$result[$index++] = '<option value=\'lernpunkte,chars\'>Lernpunkte</option>';
	$result[$index++] = '<option value=\'training_points,chars\'>Trainingspunkte</option>';
	$result[$index++] = '<option value=\'starke,chars\'>St&auml;rke</option>';
	$result[$index++] = '<option value=\'speed,chars\'>Geschwindigkeit</option>';
	$result[$index++] = '<option value=\'verteidigung,chars\'>Verteidigung</option>';
	$result[$index++] = '<option value=\'ausdauer,chars\'>Ausdauer</option>';
	$result[$index++] = '<option value=\'glueck,chars\'>Gl&uuml;ck</option>';
	$result[$index++] = '<option value=\'hp,chars\'>HP</option>';
	$result[$index++] = '<option value=\'mp,chars\'>MP</option>';

	return $result;
}

function istrinstr($source, $insert, $search) {
	$pos = strpos($source, $search);
	$newstr = substr($source, 0, $pos).$insert.substr($source, $pos);
	return $newstr;
}

function getNPCUser() {
	$qry = db_query('SELECT * FROM user WHERE nickname = "'.NPC_USER_NAME.'";');
	return mysqli_fetch_assoc($qry);
}

function getNPCAttacks() {
	$qry = db_query('SELECT name,id FROM attacken a WHERE info like "%NPC%";');
	$set = array();

	while ($result = mysqli_fetch_assoc($qry)) {
		$set[$result['id']] = $result['name'];
	}

	return $set;
}

function learnNewAttacks($charid, $att_ids) {
	//auslesen der gelernten Attacken
	$sql = 'SELECT at_id, id FROM lernen WHERE besitzer = '.$charid.' AND at_id in ('.implode(',', $att_ids).');';
	$qry = db_query($sql);

	$learned_atts = array();
	while($result = mysqli_fetch_assoc($qry)) {
		$learned_atts[$result['at_id']] = $result['id'];
	}
	
	//Namen der Attacken auslesen
	$sql = 'SELECT id,name FROM attacken WHERE id in ('.implode(',', $att_ids).');';
	$qry = db_query($sql);

	$att_names = array();
	while($result = mysqli_fetch_assoc($qry)) {
		$att_names[$result['id']] = $result['name'];
	}
	
	$tmp_ids = array();
	foreach ($att_ids as $att) {
		if($learned_atts[$att] == NULL && $tmp_ids[$att] == NULL) { 
			$tmp_ids[$att] = 1;
			/*
			 * Keine Gute Lösung, es wird zwar von der db verhindert, dass doppelte Einträge gespeichert werden
			 * aber ungebrauchte Attacken werden so auch nicht gelöscht (höchstens der NPC wird gelöscht)
			 */
			db_query('INSERT INTO lernen(name,at_id,besitzer,aktiv) VALUES(\''.$att_names[$att].'\','.$att.','.$charid.',1);');
		}
	}
}

function getNPCAttackSet($charid, $att_ids) {
	$sql = 'SELECT at_id, id FROM lernen WHERE besitzer = '.$charid.' AND at_id in ('.implode(',', $att_ids).');';
	$qry = db_query($sql);

	$learned_atts = array();
	while($result = mysqli_fetch_assoc($qry)) {
		$learned_atts[$result['at_id']] = $result['id'];
	}

	$attset = array();
	$i = 1;
	foreach ($att_ids as $att) {
		if($learned_atts[$att] != NULL) {
			$attset[$i] = $learned_atts[$att];
		} else {
			$attset[$i] = $att;
		}
		$i++;
	}
	return $attset;
}

function buildAttackSet($charid, $att_ids) {
	learnNewAttacks($charid, $att_ids);
	return getNPCAttackSet($charid, $att_ids);
}

function displayNPCInfo($action, $task, $charid) {
	//Form-Variablen
	echo "\t".'<input type="hidden" name="action" value="'.$action.'"></input>'."\n";
	echo "\t".'<input type="hidden" name="task" value="'.$task.'"></input>'."\n";
	
	$npc_race = getRaceTypeName('NPC'); // do not remove npc (there is a separate setting for this)
	$races = getRacesByType($npc_race); // remove all NPC
	$race_ids = array();
	$race_names = array();
	foreach($races as $race) {
		$race_ids[] = $race['id'];
		$race_names[] = $race['name'];
	}
	
	//initialisierung der Variablen für Inhalt
	$d_select_array = array($race_ids, array(300, 1200, 3600));
	$d_array = array('charakter', 'name', 'rasse', 'hp', 'mp', 'starke', 'verteidigung', 'speed',
		'ausdauer', 'glueck', 'zeit_rec', 'level', 'lvlmin', 'lvlmax', 'bild');
	$desc_select_array = array($race_names, array('5min', '20min', '60min'));
	$desc_array = array('ID', 'Name', 'Type', 'HP', 'MP', 'Stärke', 'Verteidigung', 'Geschwindigkeit',
			'Ausdauer', 'Glück', 'Kampfdauer in Sekunden', 'Level', 'Min-Level', 'Max-Level', 'Bild');
	$attset = array(-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1);
	$attset_names = array(' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ');
	$drop_items = getDropItemsAsOptions();

	//initialisieren falls editiert wird (dort existieren schon Inhalte)
	if($task == TASK_EDIT) {
		$attset = getAttackSet($charid, NORMALKAMPF);
		$attset_names = getAttackNames($attset);

		$sql = 'SELECT kl.*, ni.* FROM chars c inner join npc_item ni on ni.charakter = c.id inner join kampf_list kl on ni.charakter = kl.charakter WHERE c.id ='.$charid;
		//$sql = 'SELECT * FROM chars c inner join kampf_list kl on c.id = kl.charakter WHERE c.id ='.$charid;
		//echo $sql."<br>";
		$qry = db_query($sql);
		$row = mysqli_fetch_assoc($qry);
		// Fetch the char array by getChar method (for backwards compat) with no caching enabled!
		$char_data = getChar($charid, false);
                foreach( $char_data as $key => $value ) {
			if($key != 'item') {
				$row[$key] = $value;
			}
		}
	}

	$attacks = getNPCAttacks();
	$colspan = 2;

	if($task == TASK_EDIT) {
		echo "\t".'<tr><td colspan="'.$colspan.'"><h1 style="text-align:center">NPC bearbeiten</h1></td></tr>'."\n";
	} else {
		echo "\t".'<tr><td colspan="'.$colspan.'"><h1 style="text-align:center">NPC erstellen</h1></td></tr>'."\n";
	}

	echo "\t\t".'<tr><td>'.$desc_array[0].'</td><td style="text-align:center">'.$row[$d_array[0]].'</td></tr>'."\n";
	$j = 0;
	//allgemeine informationen
	for($i = 1;$i<count($d_array);$i++){
		if($i == 2 || $i == 10) {
			echo "\t\t".'<tr><td>'.$desc_array[$i].'</td><td style="text-align:center"><select id="input" name="data['.$d_array[$i].']">';
			for($k = 0; $k < count($d_select_array[$j]); $k++) {
				echo "<option value=\"".$d_select_array[$j][$k]."\" ";
				if($d_select_array[$j][$k] == debbcode($row[$d_array[$i]])) {
					echo " selected";
				}
				echo ">".$desc_select_array[$j][$k]."</option>";
			}
			echo '</select></td></tr>'."\n";
			$j++;
		} else {
			echo "\t\t".'<tr><td>'.$desc_array[$i].'</td><td style="text-align:center"><input name="data['.$d_array[$i].']" value="'.debbcode($row[$d_array[$i]]).'"></input></td></tr>'."\n";
		}
	}

	//dropitems
	echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center">Drop</td></tr>'."\n";
	echo "\t\t".'<tr><td>Item</td><td style="text-align:center"><select id="input" name="data[drop]">';
	//zusammenbauen des suchparameters
	if($row['datensatz'] == 'chars') {
		$selected = '\''.$row['feld'].','.$row['datensatz'].'\'';
	} else {
		$selected = '\''.$row['item'].','.$row['datensatz'].'\'';
	}
	foreach($drop_items as $drop) {
		if(strpos($drop, $selected) == true) {
			echo istrinstr($drop, ' selected', '>')."&nbsp;\n";
		} else {
			echo $drop."\n";
		}
	}
	echo '</select></td></tr>'."\n";
	echo "\t\t".'<tr><td>Anzahl</td><td style="text-align:center"><input name="data[drop_anzahl]" value="'.debbcode($row['wieviel']).'"></input></td></tr>'."\n";
	echo "\t\t".'<tr><td>maximale Wiederholungen</td><td style="text-align:center"><input name="data[drop_max]" value="'.debbcode($row['mal']).'"></input></td></tr>'."\n";
	echo "\t\t".'<tr><td>Preis</td><td style="text-align:center"><input name="data[preis]" value="'.debbcode($row['preis']).'"></input></td></tr>'."\n";
	//attacken informationen
	echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center">Attacken bearbeiten</td></tr>'."\n";
	for($i = 1;$i<11;$i++){
		echo "\t\t".'<tr><td>Runde '.$i.'</td><td style="text-align:center"><select id="input" name="att_ids['.$i.']">';
		echo "<option value=\"".$attset[$i]."\" selected> ".$attset_names[$attset[$i]]."</option>";
		foreach($attacks as $key => $value) {
			echo "<option value=\"".$key."\"> ".$value."</option>";
		}
		echo '</select></td></tr>'."\n";
	}
	//menü
	echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center"><input type="submit" value="speichern"></td></tr>'."\n";
	echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center"><a href="'.$_SERVER['PHP_SELF'].'?choose=npc">NPC Übersicht</a></td></tr>'."\n";
}

function editNPC($charid, $data, $att_ids) {
	$attset = buildAttackSet($charid, $att_ids);
	$error = validateAttackSet($attset);

	$d_array = array('name', 'rasse', 'hp', 'mp', 'starke', 'verteidigung', 'speed',
					'ausdauer', 'glueck', 'level', 'bild');

	$sql = 'UPDATE chars c INNER JOIN kampf_list kl ON c.id=kl.charakter INNER JOIN npc_item n ON c.id=n.charakter SET c.';
	$sql .= $d_array[0].' = \''.encodeNoHTMLWithBB($data[$d_array[0]]).'\'';

	for($i=1;$i<count($d_array);$i++){
		$sql .= ', c.'.$d_array[$i].' = \''.encodeNoHTMLWithBB($data[$d_array[$i]]).'\'';
	}

	$d_array = array('zeit_rec', 'lvlmin', 'lvlmax');
	for($i=0;$i<count($d_array);$i++){
		$sql .= ', kl.'. $d_array[$i].' = \''.encodeNoHTMLWithBB($data[$d_array[$i]]).'\'';
	}

	$info = explode(',', encodeNoHTMLWithBB($data['drop']));
	if($info[1] == 'chars') {
		$sql .= ', n.feld = \''.$info[0].'\'';
	} else {
		$sql .= ', n.item = \''.$info[0].'\'';
	}

	$sql .= ', n.datensatz = \''.$info[1].'\'';
	$sql .= ', n.wieviel = \''.encodeNoHTMLWithBB($data['drop_anzahl']).'\'';
	$sql .= ', n.mal = \''.encodeNoHTMLWithBB($data['drop_max']).'\'';
	$sql .= ', n.preis = \''.encodeNoHTMLWithBB($data['preis']).'\'';

	$sql .= ' WHERE c.id=\''.$charid.'\'';
	//echo $sql."<br>";
	$u = db_query($sql);
	$log = 0;

	if(db_affected_rows() == 0 || $u === FALSE) {
		echo "\t\t\t".'<tr><td colspan="2" style="color:red">Stats wurden nicht geändert oder es gab Fehler!</td></tr>';
	} else{
		echo "\t\t\t".'<tr><td colspan="2">Änderungen wurden übernommen!</td></tr>';
		$log++;
	}

	if($error == NULL) {
		updateAttackSet($charid, NORMALKAMPF, $attset);
		//alter müll
		$qry = 'UPDATE chars SET attacken=\''.implode(',', $attset).'\', '.
				'attacken2=\''.implode(',', $attset).'\' WHERE id='.$charid.' LIMIT 1';
		db_query($qry);
		$log++;
		echo "\t\t\t".'<tr><td colspan="2">Attacken übernommen!</td></tr>';
	} else {
		echo "\t\t\t".'<tr><td colspan="2" style="color:red">'.$error.'</td></tr>';
	}

	if($log >= 1) {
		logaction('Ein NPC wurde geändert (id = '.$charid.','.encodeNoHTMLWithBB(join($data,',')).')');
	}
}

function createNPC($data, $att_ids) {
	foreach ($data as $key => $value) {
		if($value == NULL) {
			echo 'fehler dude! bei '.$key;
		} else {
			$value = encodeNoHTMLWithBB($value);
		}
	}

	//Char erstellen, wichtig für die restlichen Sachen
	$success = erstelleChar(getNPCUser(), $data['rasse'], $data['name'], $data['bild'], $data['level'], 0, $data['hp'], $data['mp'], $data['starke'], $data['verteidigung'], $data['speed'],
	$data['ausdauer'], $data['glueck'], $data['rasse']);

	if($success) {
		//Char ID holen
		$qry = db_query('SELECT id FROM chars c WHERE name = "'.$data['name'].'";');
		$result = mysqli_fetch_assoc($qry);
		$char_id = $result['id'];

		//zur Kampfliste hinzufügen :)
		$sql = 'INSERT INTO kampf_list(kampfname,charakter,rasse,besitzer,zeit,zeit_rec,lvlmax,ip,lvlmin) VALUES("NPC Kampf",'.$char_id.',"NPC","'.NPC_USER_NAME.'",0,'.
		$data['zeit_rec'].','.$data['lvlmax'].',0,'.$data['lvlmin'].');';
		db_query($sql);

		//drops hinzufügen :D
		$sql = 'INSERT INTO npc_item(charakter,item,feld,mal,datensatz,wieviel,preis) VALUES('.$char_id;
		$info = explode(',', $data['drop']);
		if($info[1] == 'chars') {
			$sql .= ',0,\''.$info[0].'\'';
		} else {
			$sql .= ','.$info[0].',\'\'';
		}
		$sql .= ','.$data['drop_max'].',\''.$info[1].'\','.$data['drop_anzahl'].','.$data['preis'].');';
		echo $sql;
		db_query($sql);

		//Attacksets soweit einbinden
		createAttackSet($char_id, NORMALKAMPF);
		$attset = buildAttackSet($char_id, $att_ids);
		$error = validateAttackSet($attset);

		echo "\t".'<tr><td colspan="2" style="text-align:center">NPC '.$data['name'].' wurde erfolgreich erstellt!</td></tr>'."\n";
		if($error != null) {
			echo "\t".'<tr><td colspan="2" style="text-align:center">Es gab Probleme beim erstellen des Attacksets!</td></tr>'."\n";
			echo "\t".'<tr><td colspan="2" style="text-align:center">'.$error.'</td></tr>'."\n";
		} else {
			updateAttackSet($char_id, NORMALKAMPF, $attset);
			//alter müll
			$qry = 'UPDATE chars SET attacken=\''.implode(',', $attset).'\', '.
				'attacken2=\''.implode(',', $attset).'\' WHERE id='.$char_id.' LIMIT 1';
			db_query($qry);
		}
	}
	echo "\t".'<tr><td colspan="2" style="text-align:center"><a href="'.$_SERVER['PHP_SELF'].'?choose=npc">NPC Übersicht</a></td></tr>'."\n";
}

function displayDeleteNPC($charid) {
	$colspan = 1;

	$row = getChar($charid, false);
	echo "\t".'<tr><td colspan="'.$colspan.'"><h1 style="text-align:center">NPC löschen</h1></td></tr>'."\n";

	echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center">Soll der NPC '.$row['name'].' wirklich gelöscht werden</td></tr>'."\n";
	echo "\t".'<tr><td style="text-align:center"><a href="'.$_SERVER['PHP_SELF'].'?choose=npc&action=delete&task=dodelete&charid='.$charid.'">ja</a>, <a href="'.$_SERVER['PHP_SELF'].'?choose=npc&action=">nein</a></td></tr>'."\n";
}

function deleteNPC($charid) {
	$colspan = 1;
	$sql = 'DELETE FROM chars WHERE id = '.$charid.';';
	$qry = db_query($sql);
	if($qry === FALSE || db_affected_rows() <= 0){
		echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center;color:red">Der NPC '.$row['name'].' konnte nicht gelöscht werden!</td></tr>'."\n";;
	} else {
		echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center">Der NPC '.$row['name'].' wurde gelöscht!</td></tr>'."\n";;
	}
	echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center"><a href="'.$_SERVER['PHP_SELF'].'?choose=npc">NPC Übersicht</a></td></tr>'."\n";
}

function displayNPCOverview($page) {
	$d_array = array('id', 'name', 'level');
	$desc_array = array('id', 'Name', 'Level');

	$npc_race = getRaceTypeName('NPC'); // do not remove npc (there is a separate setting for this)
	$races = getRacesByType($npc_race); // remove all NPC
	foreach($races as $race) {
		$include_race_ids[] = '\''.$race['id'].'\'';
	}
	
	$sql = 'SELECT '.join($d_array,',').' from chars where rasse IN ('.implode(',', $include_race_ids).') ORDER BY level, Name LIMIT '.($page*20).', 20';
	$qry = db_query($sql);
	$colspan = count($d_array);
	echo "\t".'<tr><td colspan="'.$colspan.'"><h1 style="text-align:center">Übersicht</h1></td></tr>'."\n";;
	echo "\t".'<tr>'."\n";
	for($i=1;$i<count($d_array);$i++){
		echo "\t\t".'<td>'.$desc_array[$i].'</td>'."\n";
	}
	echo "\t\t".'<td style="text-align:center">Bearbeiten</td>'."\n";
	echo "\t".'</tr>'."\n";

	echo "\t".'<tr><td colspan="'.$colspan.'"><hr></td></tr>'."\n";

	while($row = mysqli_fetch_assoc($qry)){
		echo "\t".'<tr>'."\n";
		for($i=1;$i<count($d_array);$i++){
			echo "\t\t".'<td>'.$row[$d_array[$i]].'</td>'."\n";
		}

		echo "\t\t".'<td style="text-align:center">'."\n";

		// Echo the delete and the edit button :)
		echo "\t\t\t".'<a href="'.$_SERVER['PHP_SELF'].'?choose=npc&action=edit&charid='.$row[$d_array[0]].'">Edit</a>, '."\n";
		echo "\t\t\t".'<a href="'.$_SERVER['PHP_SELF'].'?choose=npc&action=delete&charid='.$row[$d_array[0]].'">Delete</a>'."\n";

		echo "\t\t".'</td>'."\n";
		echo "\t".'</tr>'."\n";

	}
	$total = mysqli_num_rows(db_query('SELECT '.join($d_array,',').' from chars where rasse IN ('.implode(',', $include_race_ids).')'));
	$url = '<a href="'.$_SERVER['PHP_SELF'].'?choose=npc&page=###PAGE###">###LABEL###</a>';
	echo "\t".'<tr><td colspan="'.$colspan.'"><hr></td></tr>'."\n";
	echo "\t".'<tr><td colspan="'.$colspan.'">'.displayPagelinksNew(20, $total, $page, $url).'</td></tr>'."\n";
	echo "\t".'<tr><td colspan="'.$colspan.'"><hr></td></tr>'."\n";
	echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center"><a href="'.$_SERVER['PHP_SELF'].'?choose=npc&action=create">Create a new NPC</a></td></tr>'."\n";
}

function displayNPC($action, $task, $charid, $table, $page, $data, $att_ids){
	$colspan = 3;
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">'."\n";
	echo "\t".'<input type="hidden" name="choose" value="npc"></input>'."\n";
	echo "\t".'<input type="hidden" name="charid" value="'.$charid.'"></input>'."\n";
	echo "\t".'<table width="80%" border="0">'."\n";
	switch ($action) {
		case ACTION_EDIT:
			echo "\t".'<input type="hidden" name="action" value="'.ACTION_EDIT.'"></input>'."\n";
			if($task == TASK_EDIT) {
				editNPC($charid, $data, $att_ids);
				displayNPCInfo(ACTION_EDIT, TASK_EDIT, $charid);
			} else {
				displayNPCInfo(ACTION_EDIT, TASK_EDIT, $charid);
			}
			break;
		case ACTION_CREATE:
			echo "\t".'<input type="hidden" name="action" value="'.ACTION_CREATE.'"></input>'."\n";
			if($task == TASK_CREATE) {
				createNPC($data, $att_ids);
			} else {
				displayNPCInfo(ACTION_CREATE, TASK_CREATE, $charid);
			}
			break;
		case 'delete':
			if($task == 'dodelete') {
				deleteNPC($charid);
			} else {
				displayDeleteNPC($charid);
			}
			break;
		default:
			displayNPCOverview($page);
	}
	echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center"><a href="'.$_SERVER['PHP_SELF'].'">Zum Hauptmenu</a></td></tr>'."\n";
	echo "\t".'</table>'."\n";
	echo '</form>'."\n";
}
?>
