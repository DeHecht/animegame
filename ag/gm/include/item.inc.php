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
function displayItems($action, $task, $itemid, $itemtable, $searchstring, $s_itemtable,$page, $data){
	if(!is_numeric($page) || $page < 0){ $page = 0; }
	
	$error = false;
	$colspan = 1;
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">'."\n";
	echo "\t".'<input type="hidden" name="choose" value="items"></input>'."\n";
	echo "\t\t".'<table width="80%">'."\n";
	if($action == 'edit'){
		// Change or delte
		echo "\t".'<input type="hidden" name="action" value="edit"></input>'."\n";
		echo "\t".'<input type="hidden" name="searchstring" value="'.$searchstring.'"></input>'."\n";
		if(is_null($itemid) || is_null($itemtable) ){
			echo "\t".'<input type="hidden" name="page" value="0"></input>'."\n";
			// Display overview
			if($s_itemtable == 'item'){
				$sql = 'SELECT i.id, i.name, \'item\' as tablename FROM item i';
			} else if($s_itemtable == 'sp_item'){
				$sql = 'SELECT i.id, i.name, \'sp_item\' as tablename FROM sp_item i';
			} else if($s_itemtable == 'wochen_markt'){
				$sql = 'SELECT i.id, i.item AS name, \'wochen_markt\' as tablename FROM wochen_markt i';
			} else{
				$sql = 'SELECT i.id, i.name, \'item\' as tablename FROM item i'
			        . ' union'
			        . ' SELECT i.id, i.name, \'sp_item\' as tablename FROM sp_item i'
			        . ' union'
			        . ' SELECT i.id, i.item AS name, \'wochen_markt\' as tablename FROM wochen_markt i';
			}
			$qry = db_query($sql.' LIMIT '.($page*20).', 20');
		//	echo $sql.' LIMIT '.($page*30).', 30';
			$anzahlSeiten = ceil(mysqli_num_rows(db_query($sql))/20);
			$colspan = 3;
			echo "\t\t\t".'<tr><td colspan="'.$colspan.'"><h1 style="text-align:center">Bearbeiten</h1></td></tr>'."\n";;
			$o_array = array('-Alle-', 'Markt Items', 'Schatzsuche Items', 'Auktionsitems');
			$o_tblarray = array('', 'item', 'sp_item', 'wochen_markt');
			echo "\t\t\t".'<tr><td colspan="'.$colspan.'">Suche eingrenzen:<select name="s_itemtable">';
			for($i=0;$i<count($o_tblarray);$i++){
				if($o_tblarray[$i] == $s_itemtable){
					$selected = ' selected="selected"';
				} else{
					$selected = '';
				}
				echo '<option value="'.$o_tblarray[$i].'"'.$selected.'>'.$o_array[$i].'</option>';
			}			
			echo "\t\t\t".' <input type="submit" value="filtern"></td></tr>';
			echo "\t\t\t".'<tr><td colspan="'.$colspan.'"><hr></td></tr>'."\n";
			
			
			while($row = mysqli_fetch_assoc($qry)){
				echo "\t\t\t".'<tr><td>'.$row['name'].'</td><td width="10"><a href="'.$_SERVER['PHP_SELF'].'?choose=items&action=edit&task=change&itemid='.$row['id'].'&itemtable='.$row['tablename'].'&searchstring='.$searchstring.'&s_itemtable='.$s_itemtable.'&page='.$page.'">Edit</a></td>';
				echo '<td width="10"><a href="'.$_SERVER['PHP_SELF'].'?choose=items&action=edit&task=delete&itemid='.$row['id'].'&itemtable='.$row['tablename'].'&searchstring='.$searchstring.'&s_itemtable='.$s_itemtable.'&page='.$page.'">Delete</a></td></tr>'."\n";
			}
			echo "\t\t\t".'<tr><td colspan="'.$colspan.'"><hr></td></tr>'."\n";
			
			echo "\t\t\t".'<tr><td colspan="'.$colspan.'">'.displayPagelinks($page, $anzahlSeiten , '<a href="'.$_SERVER['PHP_SELF'].'?choose=items&action=edit&itemtable='.$itemtable.'&searchstring='.$searchstring.'&s_itemtable='.$s_itemtable.'&page=###PAGE###">###LABEL###</a>').'</td></tr>';
		} else if($task == 'change'){
			$d_array = array();
			echo "\t".'<input type="hidden" name="page" value="'.$page.'"></input>'."\n";
			echo "\t".'<input type="hidden" name="task" value="dochange"></input>'."\n";
			echo "\t".'<input type="hidden" name="itemid" value="'.$itemid.'"></input>'."\n";
			echo "\t".'<input type="hidden" name="itemtable" value="'.$itemtable.'"></input>'."\n";
			$colspan = 2;
			echo "\t\t\t".'<tr><td colspan="'.$colspan.'"><h1 style="text-align:center">Editieren</h1></td></tr>'."\n";
			$d_array = NULL;
			$desc_array = NULL;
			$value_type_map = array();
			if($itemtable == 'item'){
				$d_array = array('id','name','hp','mp','starke','verteidigung','speed','s_type','preis','anzahl','rassen_type','level','tausch_lvl','tausch_anzahl','info');
				$desc_array = array('ID', 'Name', 'HP', 'MP', 'Bonus Stärke', 'Bonus Verteidigung', 'Bonus Geschwindigkeit', 'Art (Item, Helm, ..)', 'Preis im Markt', 'Anzahl', 'Rassen', 'Level', 'Tauschlevel', 'Tauschanzahl', 'Infotext');
				$race_types = getRaceTypes();
				$race_types[] = array('id' => NULL, 'name' => 'ALL');
				$value_type_map = array('rassen_type' => array( 'index' => 'id', 'name' => 'name', 'data' => $race_types ));
			} else if($itemtable == 'sp_item'){
				$d_array = array('id','name','preis','wert','type','slot','tausch_lvl','tausch_anzahl','info');	
				$desc_array = array('ID', 'Name', 'Preis', 'Häufigkeit (je höher desto seltener)', '"Teufels Frucht" oder nicht', 'Slot (immer auf 0 setzen)', 'Tauschlevel', 'Tauschanzahl', 'Infotext');
			} else if($itemtable == 'wochen_markt'){
				$d_array = array('id','item','starke','ver','speed','ausdauer','hp','mp','glueck','type','preis','anzahl','verteilung','aktiv','art','kategorie','tausch_lvl','tausch_anzahl','info');
				$desc_array = array('ID', 'Name', 'Bonus Stärke', 'Bonus Verteidigung', 'Bonus Geschwindigkeit', 'Bonus Ausdauer', 'Bonus HP', 'Bonus MP', 'Bonus Glück', 'Typ ("Teufels Frucht")', 'Preis', 'Anzahl', 'Verteilung (0=Nicht wünschbar, Summe Verteilung sollte 100 ergeben!!)', 'Kann bei Geheimauktion auftreten', 'Art (normal, natur)', 'Kategorie (Logia, Zoan, ...)', 'Tauschlevel', 'Tauschanzahl', 'Infotext' );
			} else{
				// Something went completely wrong!!
				$error = true;
			}
			if(!$error){
				$sql = 'SELECT '.join(',', $d_array).' FROM '.$itemtable.' where id = '.$itemid;
				$qry = db_query($sql);
				$row = mysqli_fetch_assoc($qry);
				echo "\t\t".'<tr><td>'.$desc_array[0].'</td><td style="text-align:center">'.$row[$d_array[0]].'</td></tr>'."\n";
				for($i = 1;$i<count($d_array)-1;$i++){
					if (isset($value_type_map[$d_array[$i]])) {
						$entry = $value_type_map[$d_array[$i]];
						echo "\t\t".'<tr><td>'.$desc_array[$i].'</td><td style="text-align:center">'."\n";
						echo "\t\t\t".'<select name="data['.$d_array[$i].']">'."\n";
						foreach ($entry['data'] as $entry_data) {
							$selected = '';
							if ($row[$d_array[$i]] == $entry_data[$entry['index']] ) {
								$selected = 'selected="selected"';
							}
							echo "\t\t\t\t".'<option value="'.$entry_data[$entry['index']].'" '.$selected.'>'.$entry_data[$entry['name']].'</option>'."\n";
						}
						echo "\t\t\t".'</select>'."\n";
						echo "\t\t".'</td></tr>'."\n";
					} else {
						echo "\t\t".'<tr><td>'.$desc_array[$i].'</td><td style="text-align:center"><input name="data['.$d_array[$i].']" value="'.debbcode($row[$d_array[$i]]).'"></input></td></tr>'."\n";
					}
				}
								
				// Infotext
				echo "\t\t\t".'<tr><td>'.$desc_array[$i].'</td><td style="text-align:center"><textarea name="data['.$d_array[$i].']" cols="30" rows="5">'.debbcode($row[$d_array[$i]]).'</textarea></td></tr>'."\n";
				echo "\t\t\t".'<td colspan="'.$colspan.'" style="text-align:center"><input type="submit" value="ändern"></td></tr>'."\n";
				echo "\t\t\t".'<tr><td align="center" colspan="'.$colspan.'"><a href="'.$_SERVER['PHP_SELF'].'?choose=items&action=edit&s_itemtable='.$s_itemtable.'&searchstring='.$searchstring.'&page='.$page.'">Zurück zur Übersicht</a></td></tr>'."\n";
			}
		} else if($task == 'delete'){
			echo "\t".'<input type="hidden" name="page" value="'.$page.'"></input>'."\n";
			$colspan = 1;
			echo "\t\t\t".'<tr><td colspan="'.$colspan.'"><h1 style="text-align:center">Löschen</h1></td></tr>'."\n";

			if($itemtable == 'item'){
				$sql = 'SELECT i.id, i.name, \'item\' as tablename FROM item i WHERE id ='.$itemid;
			} else if($itemtable == 'sp_item'){
				$sql = 'SELECT i.id, i.name, \'sp_item\' as tablename FROM sp_item i WHERE id ='.$itemid;
			}else if($itemtable == 'wochen_markt'){
				$sql = 'SELECT i.id, i.item AS name, \'wochen_markt\' as tablename FROM wochen_markt i WHERE id ='.$itemid;
			} else{
				// Something went completely wrong!!
				$error = true;
			}
			if(!is_numeric($itemid)){
				$error = true;
			}
			
			
			if(!$error){
				$row = mysqli_fetch_assoc(db_query($sql));
				echo "\t\t\t".'<tr><td colspan="'.$colspan.'" style="text-align:center">Soll Item "'.$row['name'].'" wirklich gelöscht werden?</td></tr>'."\n";
				echo "\t\t\t".'<tr><td style="text-align:center"><a href="'.$_SERVER['PHP_SELF'].'?choose=items&action=edit&task=dodelete&itemid='.$itemid.'&itemtable='.$itemtable.'&s_itemtable='.$s_itemtable.'&searchstring='.$searchstring.'&page='.$page.'">ja</a>, <a href="'.$_SERVER['PHP_SELF'].'?choose=items&action=edit&s_itemtable='.$s_itemtable.'&searchstring='.$searchstring.'&page='.$page.'">nein</a>, <a href="'.$_SERVER['PHP_SELF'].'?choose=items&action=edit&task=delete&itemid='.$itemid.'&itemtable='.$itemtable.'&s_itemtable='.$s_itemtable.'&searchstring='.$searchstring.'&page='.$page.'">vielleicht</a></td></tr>'."\n";	
			}
		} else if($task == 'dochange'){
			// d_array[0] muss name sein!!
			if($itemtable == 'item'){
				$d_array = array('name','hp','mp','starke','verteidigung','speed','s_type','preis','anzahl','rassen_type','level','tausch_lvl','tausch_anzahl','info');
			} else if($itemtable == 'sp_item'){
				$d_array = array('name','preis','wert','type','slot','tausch_lvl','tausch_anzahl','info');				
			} else if($itemtable == 'wochen_markt'){
				$d_array = array('item','starke','ver','speed','ausdauer','hp','mp','glueck','type','preis','anzahl','verteilung','aktiv','art','kategorie','tausch_lvl','tausch_anzahl','info');
			} else{
				// Something went completely wrong!!
				$error = true;
			}
			
			if(!$error){
				$sql = 'UPDATE '.$itemtable.' SET ';
				$row = mysqli_fetch_assoc(db_query('Select '.$d_array[0].' from '.$itemtable.' WHERE id = '.$itemid));
				
				$sql .= $d_array[0].' = \''.encodeNoHTMLWithBB($data[$d_array[0]]).'\'';
				
				for($i=1;$i<count($d_array);$i++){
					if ($data[$d_array[$i]] == "") {
						$sql .= ', '. $d_array[$i].' = NULL';
					} else {
						$sql .= ', '. $d_array[$i].' = \''.encodeNoHTMLWithBB($data[$d_array[$i]]).'\'';
					}
				}
				
				$sql .= ' WHERE id=\''.$itemid.'\'';
				// echo $sql.'<br>';
				$u = db_query($sql);
				if(db_affected_rows() == 0 || $u === FALSE){
					echo "\t\t\t".'<tr><td colspan="2" style="color:red">Fehler! Änderungen konnten nicht übernommen werden!</td></tr>';
				} else{
					logaction('Ein Item wurde geändert (@'.$itemtable.', id = '.$itemid.','.encodeNoHTMLWithBB(join($data,',')).')');
					echo "\t\t\t".'<tr><td colspan="2">Änderungen wurden übernommen!</td></tr>';
					db_query('UPDATE chars set frucht = \''.encodeNoHTMLWithBB($data[$d_array[0]]).'\' where frucht = \''.$row[$d_array[0]].'\'');
					if($itemtable == 'wochen_markt'){
						$sql = 'Update sp_item set name = \''.encodeNoHTMLWithBB($data[$d_array[0]]).'\' where name = \''.$row[$d_array[0]].'\'';
				echo $sql.'<br>';
						db_query($sql);
					} else if($itemtable == 'sp_item'){
						$sql = 'Update wochen_markt set item = \''.encodeNoHTMLWithBB($data[$d_array[0]]).'\' where item = \''.$row[$d_array[0]].'\'';
				echo $sql.'<br>';
						db_query($sql);
					}
				}
			}
			
			displayItems($action, 'change', $itemid, $itemtable, $searchstring, $s_itemtable,$page, $data);
			return;
		} else if($task == 'dodelete'){
			if($itemtable == 'item'){
				$sql = 'SELECT i.id, i.name, \'item\' as tablename FROM item i WHERE id ='.$itemid;
				$d_sql = 'DELETE FROM item WHERE id ='.$itemid;
				$d_sql_2 = 'DELETE FROM ware WHERE item_id ='.$itemid;
			} else if($itemtable == 'sp_item'){
				$sql = 'SELECT i.id, i.name, \'sp_item\' as tablename FROM sp_item i WHERE id ='.$itemid;
				$d_sql = 'DELETE FROM sp_item WHERE id ='.$itemid;
				$d_sql_2 = 'DELETE FROM sp_ware WHERE item ='.$itemid;
			}else if($itemtable == 'wochen_markt'){
				$sql = 'SELECT i.id, i.item AS name, \'wochen_markt\' as tablename FROM wochen_markt i WHERE id ='.$itemid;
				$d_sql = 'DELETE FROM wochen_markt WHERE id ='.$itemid;
				$d_sql_2 = 'DELETE FROM wochen_ware WHERE item ='.$itemid;
			} else{
				// Something went completely wrong!!
				$error = true;
			}
			if(!$error){
				$row = mysqli_fetch_assoc(db_query($sql));
				
				if(db_query($d_sql) === FALSE || db_affected_rows() == 0){
					echo "\t\t\t".'<tr><td colspan="2">Item "'.$row['name'].'" konnte nicht gelöscht werden!</td></tr>';
				} else if(db_query($d_sql_2) === FALSE){
					logaction('Ein Item wurde gelöscht (@'.$itemtable.', id = '.$itemid.','.$row['name'].')');
					echo "\t\t\t".'<tr><td colspan="2">Item "'.$row['name'].'" wurde zwar gelöscht, bei der Warentabelle ist jedoch ein Fehler aufgetreten!</td></tr>';
				} else{
					logaction('Ein Item wurde gelöscht (@'.$itemtable.', id = '.$itemid.','.$row['name'].')');
					echo "\t\t\t".'<tr><td colspan="2">Item "'.$row['name'].'" wurde erfolgreich gelöscht!</td></tr>';
				}
				
				displayItems($action, null, null, null, $searchstring, $s_itemtable,$page,$data);
				return;
			}
		}
			echo "\t\t\t".'<tr><td align="center" colspan="'.$colspan.'"><a href="'.$_SERVER['PHP_SELF'].'?choose=items">Zum Itemsmenü</a></td></tr>'."\n";
	} else if($action == 'create'){
		echo "\t".'<input type="hidden" name="action" value="create"></input>'."\n";
		echo "\t".'<input type="hidden" name="itemtable" value="'.$itemtable.'"></input>'."\n";
		

		if($task == 'docreate'){
			// Do the insert and open the edit-page :)
			if($itemtable == 'item'){
				$d_array = array('name','hp','mp','starke','verteidigung','speed','s_type','preis','anzahl','rassen_type','level','info');
			} else if($itemtable == 'sp_item'){
				$d_array = array('name','preis','wert','type','slot','info');				
			} else if($itemtable == 'wochen_markt'){
				$d_array = array('item','starke','ver','speed','ausdauer','hp','mp','glueck','type','preis','anzahl','verteilung','aktiv','art','kategorie','info');
			} else{
				// Something went completely wrong!!
				$error = true;
			}
			
			if(!$error){
				// We create an itemtable-wide unique id!!
				$row = mysqli_fetch_assoc(db_query('select max(max_id) + 1 as id from (select max(s.id) as max_id from sp_item s union select max(i.id)  as max_id from item i union select max(w.id)  as max_id from wochen_markt w) gno'));
//				$row = mysqli_fetch_assoc(db_query('SELECT max(id) +1 as id FROM '.$itemtable));
				$itemid = $row['id'];
				
				$sql = 'INSERT INTO '.$itemtable.' (id, '.join($d_array,', ').') values('.$itemid;
				
				for($i=0;$i<count($d_array);$i++){
					if ($data[$d_array[$i]] == "") {
						$sql .= ', '. $d_array[$i].' = NULL';
					} else {
						$sql .= ', \''.encodeNoHTMLWithBB($data[$d_array[$i]]).'\'';
					}
				}

				$sql .= ')';
				
//				echo $sql.'<br>';
				$u = db_query($sql);
				if(db_affected_rows() == 0 || $u === FALSE){
					echo "\t\t\t".'<tr><td colspan="2" style="color:red">Fehler! Konnte Item nicht erstellen!</td></tr>';
					displayItems($action, null, null, $itemtable, $searchstring, $s_itemtable,$page, $data);
				} else{
					logaction('Ein Item wurde erstellt (@'.$itemtable.', id = '.$itemid.','.encodeNoHTMLWithBB(join($data,',')).')');
					echo "\t\t\t".'<tr><td colspan="2">Item wurde erstellt!</td></tr>';
					displayItems('edit', 'change', $itemid, $itemtable, $searchstring, $s_itemtable, $page, $data);
				}
				return;
			}
		} else {
			$colspan = 2;
			$d_array = array();
			$desc_array = array();
			$value_type_map = array();
			echo "\t\t\t".'<tr><td colspan="'.$colspan.'"><h1 style="text-align:center">Erstellen</h1></td></tr>'."\n";
			if($itemtable == 'item'){
				$d_array = array('id','name','hp','mp','starke','verteidigung','speed','s_type','preis','anzahl','rassen_type','level','info');
				$desc_array = array('ID', 'Name', 'HP', 'MP', 'Bonus Stärke', 'Bonus Verteidigung', 'Bonus Geschwindigkeit', 'Art (Item, Helm, ..)', 'Preis im Markt', 'Anzahl', 'Rassen', 'Level', 'Infotext');
				$race_types = getRaceTypes();
				$race_types[] = array('id' => NULL, 'name' => 'ALL');
				$value_type_map = array('rassen_type' => array( 'index' => 'id', 'name' => 'name', 'data' => $race_types ));

			} else if($itemtable == 'sp_item'){
				$d_array = array('id','name','preis','wert','type','slot','info');				
				$desc_array = array('ID', 'Name', 'Preis', 'Häufigkeit (je höher desto seltener)', '"Teufels Frucht" oder nicht', 'Slot (immer auf 0 setzen)', 'Infotext');
			} else if($itemtable == 'wochen_markt'){
				$d_array = array('id','item','starke','ver','speed','ausdauer','hp','mp','glueck','type','preis','anzahl','verteilung','aktiv','art','kategorie','info');
				$desc_array = array('ID', 'Name', 'Bonus Stärke', 'Bonus Verteidigung', 'Bonus Geschwindigkeit', 'Bonus Ausdauer', 'Bonus HP', 'Bonus MP', 'Bonus Glück', 'Typ ("Teufels Frucht")', 'Preis', 'Anzahl', 'Verteilung (0=Nicht wünschbar, Summe Verteilung sollte 100 ergeben!!)', 'Kann bei Geheimauktion auftreten', 'Art (normal, natur)', 'Kategorie (Logia, Zoan, ...)','Infotext' );
			} else{
				// Something went completely wrong!!
				$o_array = array('Markt Items', 'Schatzsuche Items', 'Auktionsitems');
				$o_tblarray = array('item', 'sp_item', 'wochen_markt');
				echo "\t\t\t".'<tr><td colspan="'.$colspan.'" style="text-align:center"><select name="itemtable">';
				for($i=0;$i<count($o_tblarray);$i++){
					if($o_tblarray[$i] == $s_itemtable){
						$selected = ' selected="selected"';
					} else{
						$selected = '';
					}
					echo '<option value="'.$o_tblarray[$i].'"'.$selected.'>'.$o_array[$i].'</option>';
				}			
				echo "\t\t\t".' <input type="submit" value="auswählen"></td></tr>';
				$error = true;
			}
			if(!$error){
				// Erstelle eine Maske (daran denken, dass die eingaben die schon gemacht wurden übernommen werden)!
				echo "\t".'<input type="hidden" name="task" value="docreate"></input>'."\n";
				echo "\t\t".'<tr><td>'.$desc_array[0].'</td><td style="text-align:center">'.$data[$d_array[0]].'</td></tr>'."\n";
				for($i = 1;$i<count($d_array)-1;$i++){
					if (isset($value_type_map[$d_array[$i]])) {
						$entry = $value_type_map[$d_array[$i]];
						echo "\t\t".'<tr><td>'.$desc_array[$i].'</td><td style="text-align:center">'."\n";
						echo "\t\t\t".'<select name="data['.$d_array[$i].']">'."\n";
						foreach ($entry['data'] as $entry_data) {
							$selected = '';
							if ($row[$d_array[$i]] == $entry_data[$entry['index']] ) {
								$selected = 'selected="selected"';
							}
							echo "\t\t\t\t".'<option value="'.$entry_data[$entry['index']].'" '.$selected.'>'.$entry_data[$entry['name']].'</option>'."\n";
						}
						echo "\t\t\t".'</select>'."\n";
						echo "\t\t".'</td></tr>'."\n";
					} else {
						echo "\t\t".'<tr><td>'.$desc_array[$i].'</td><td style="text-align:center"><input name="data['.$d_array[$i].']" value="'.debbcode($data[$d_array[$i]]).'"></input></td></tr>'."\n";
					}
				}
								
				// Infotext
				echo "\t\t\t".'<tr><td>'.$desc_array[$i].'</td><td style="text-align:center"><textarea name="data['.$d_array[$i].']" cols="30" rows="5">'.debbcode($data[$d_array[$i]]).'</textarea></td></tr>'."\n";
				echo "\t\t\t".'<td colspan="'.$colspan.'" style="text-align:center"><input type="submit" value="erstellen"></td></tr>'."\n";
			}
		}
		
		// Create
		echo "\t\t\t".'<tr><td align="center" colspan="'.$colspan.'"><a href="'.$_SERVER['PHP_SELF'].'?choose=items">Zum Itemsmenü</a></td></tr>'."\n";
	} else if($action == 'overview'){
			echo "\t".'<input type="hidden" name="action" value="overview"></input>'."\n";

			if($itemtable == 'sp_item'){
				$d_array = array('id','name','preis','wert','type','slot','info');				
				$desc_array = array('ID', 'Name', 'Preis', 'Häufigkeit', 'Typ', 'Slot', 'Infotext');
			} else if($itemtable == 'wochen_markt'){
				$d_array = array('id','item','starke','ver','speed','ausdauer','hp','mp','glueck','type','preis','anzahl','verteilung','aktiv','art','kategorie','info');
				$desc_array = array('ID', 'Name', 'Bonus Stärke', 'Bonus Verteidigung', 'Bonus Geschwindigkeit', 'Bonus Ausdauer', 'Bonus HP', 'Bonus MP', 'Bonus Glück', 'Typ ("Teufels Frucht")', 'Preis', 'Anzahl', 'Verteilung (0=Nicht wünschbar, Summe Verteilung sollte 100 ergeben!!)', 'Kann bei Geheimauktion auftreten', 'Art (normal, natur)', 'Kategorie (Logia, Zoan, ...)','Infotext' );
			} else {
				$itemtable = 'item';
				$d_array = array('id','name','hp','mp','starke','verteidigung','speed','s_type','preis','anzahl','rassen_type','level','info');
				$desc_array = array('ID', 'Name', 'HP', 'MP', 'Stärke', 'Verteidigung', 'Geschwindigkeit', 'Art', 'Preis', 'Anzahl', 'Rassen', 'Level', 'Infotext');
			}
	
			$colspan = count($d_array);
			
			echo "\t\t\t".'<tr><td colspan="'.$colspan.'"><h1 style="text-align:center">Übersicht</h1></td></tr>'."\n";
			$o_array = array('Markt Items', 'Schatzsuche Items', 'Auktionsitems');
			$o_tblarray = array('item', 'sp_item', 'wochen_markt');
			echo "\t\t\t".'<tr><td colspan="'.$colspan.'"><select name="itemtable">';
			for($i=0;$i<count($o_tblarray);$i++){
				if($o_tblarray[$i] == $s_itemtable){
					$selected = ' selected="selected"';
				} else{
					$selected = '';
				}
				echo '<option value="'.$o_tblarray[$i].'"'.$selected.'>'.$o_array[$i].'</option>';
			}			
			echo "\t\t\t".' <input type="submit" value="vergleichen"></td></tr>';
			echo "\t\t\t".'<tr><td colspan="'.$colspan.'"><hr></td></tr>'."\n";
			
			// We do not want to display the ID and the Info
			echo "\t\t\t".'<tr>';
			for($i=1;$i<count($desc_array)-1;$i++){
				echo '<td>'.$desc_array[$i].'</td>';
			}
			echo '<td>Aktion</td>';
			echo '</tr>';
			
			
			$sql = 'SELECT '.join($d_array,',').' FROM '.$itemtable;
			$qry = db_query($sql);
			while($row = mysqli_fetch_assoc($qry)){
				echo "\t\t\t".'<tr>';
				for($i=1;$i<count($d_array)-1;$i++){
					echo '<td>'.$row[$d_array[$i]].'</td>';
				}
				echo '<td>';
				echo '<a href="'.$_SERVER['PHP_SELF'].'?choose=items&action=edit&task=change&itemid='.$row['id'].'&itemtable='.$itemtable.'">Edit</a>, ';
				echo '<a href="'.$_SERVER['PHP_SELF'].'?choose=items&action=delete&task=change&itemid='.$row['id'].'&itemtable='.$itemtable.'">Delete</a>';
				echo '</td>';
				echo '</tr>';
			}
			echo "\t\t\t".'<tr><td align="center" colspan="'.$colspan.'"><a href="'.$_SERVER['PHP_SELF'].'?choose=items">Zum Itemsmenü</a></td></tr>'."\n";
	} else {
		echo "\t\t\t".'<tr><td align="center" colspan="'.$colspan.'"><a href="'.$_SERVER['PHP_SELF'].'?choose=items&action=overview">Übersicht</a></td></tr>'."\n";
		echo "\t\t\t".'<tr><td align="center" colspan="'.$colspan.'"><a href="'.$_SERVER['PHP_SELF'].'?choose=items&action=edit">Bearbeiten</a></td></tr>'."\n";
		echo "\t\t\t".'<tr><td align="center" colspan="'.$colspan.'"><a href="'.$_SERVER['PHP_SELF'].'?choose=items&action=create">Erstellen</a></td></tr>'."\n";
	}
	echo "\t\t\t".'<tr><td align="center" colspan="'.$colspan.'"><a href="'.$_SERVER['PHP_SELF'].'">Zum Hauptmenu</a></td></tr>'."\n";
	echo "\t\t".'</table>'."\n";
	echo '</form>'."\n";

}
?>
