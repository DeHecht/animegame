<?php
/*
 * Created on 01.09.2016
 *
 * @copyright (c) 2016 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH.'/include/items.inc.php');

function getSetDefinitionFromRequest($request) {
	$items = getItemsMap();

	$craft_item = lookupItem($items, $request['crafted']);
	$deps = array();
	for ($i=0; $i<5;$i++) {
		$dep_item = lookupItem($items, $request['ingredient_'.$i]);
		if ( $dep_item !== NULL ) {
			$deps[]=$dep_item;
		}
	}
	$set['item'] = $craft_item;
	$set['deps'] = $deps;
	if ( isset($request['topic']) ) {
		$set['topic_id'] = $request['topic'] == 0 ? NULL : $request['topic'];
	}
	return $set;
}


function displayRezepte($request) {
	switch ( $request['action'] ) {
		case 'create':
			displayRezepteCreateMenu($request);
			break;
		case 'edit':
			displayRezepteEditMenu($request);
			break;
		case 'delete':
			displayRezepteDeleteMenu($request);
			break;
		case 'create_topic':
			displayRezepteTopicCreateMenu($request);
			break;
		case 'edit_topic':
			displayRezepteTopicEditMenu($request);
			break;
		case 'delete_topic':
			displayRezepteTopicDeleteMenu($request);
			break;
		case 'up_topic':
			displayRezepteTopicUpMenu($request);
			break;
		case 'down_topic':
			displayRezepteTopicDownMenu($request);
			break;
		default:
			displayRezepteMainMenu($request);
			break;
	}
}

function displayRezepteMainMenu($request) {
?>
<table>
        <tr>
                <th colspan="3">Rezepte</th>
        </tr>

<?php
	$sets = get_set_definitions();
	$prev_topic = '#INVALID_TOPIC#';
	foreach ($sets as $set) {
		$dep_item_names = array();
		foreach( $set['deps'] as $dep_item )  {
			$dep_item_names[]=$dep_item['name'];
		}
		if ($set['topic'] != $prev_topic) {
			$prev_topic = $set['topic'];
			$print_topic = ($set['topic'] === NULL) ? '- Kein Topic -' : $set['topic'];
?>
        <tr>
                <th colspan="3"><hr></th>
        </tr>
	<tr>
		<th colspan="3"><?php echo $print_topic; ?></th>
	</tr>
	<tr>
		<th>Item</th><th width="50%">Zutaten</th><th>Action</th>
	</tr>
<?php
		}
		echo '<tr><td>'.$set['item']['name'].'</td><td align="center">'.join(',',$dep_item_names).'</td><td align="center"><a href="?choose=rezept&action=edit&set='.$set['id'].'">Edit</a> | <a href="?choose=rezept&action=delete&set='.$set['id'].'">Delete</a></td></tr>';
	}
?>
	<tr><td colspan="3" align="center"><a href="?choose=rezept&action=create">Neues Rezept erstellen</a></td></tr>
	<tr><th colspan="3"><hr /></th></tr>
        <tr>
                <th colspan="3">Rezept Topics</th>
        </tr>
<?php
	$set_topics = getItemTopicsMap();
	foreach ($set_topics as $set_topic) {
		$id = $set_topic['id'];
?>
	<tr>
		<td><?php echo $set_topic['name']; ?></td>
		<td colspan="2" align="right"><a href="?choose=rezept&action=edit_topic&id=<?php echo $id; ?>">Edit</a> | <a href="?choose=rezept&action=up_topic&id=<?php echo $id; ?>">Up</a> | <a href="?choose=rezept&action=down_topic&id=<?php echo $id; ?>">Down</a> | <a href="?choose=rezept&action=delete_topic&id=<?php echo $id; ?>">Delete</a></td>
	</tr>
<?php
	}
?>
        <tr><td colspan="3" align="center"><a href="?choose=rezept&action=create_topic">Neues Rezept Topic erstellen</a></td></tr>

        <tr>
                <td colspan="3"><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
                </td>
        </tr>
</table>

<?php
}

function createRezeptEntry($set) {
	$qry = db_query('Select max(set_id) FROM sp_sets');
	$new_id = mysqli_fetch_row($qry)[0];
	do {
		$new_id++;
		db_query('INSERT sp_sets(set_id, table_name, item_id) values('.$new_id.',\''.$set['item']['table_name'].'\','.$set['item']['id'].')');
	} while(db_affected_rows() == 0);	
	return $new_id;
}

function updateRezept($base, $edit) {
	if ($base['id'] === NULL) {
		$base['id'] = createRezeptEntry($edit);
	} else {
		$topic_id = ($edit['topic_id'] == NULL) ? 'NULL' : $edit['topic_id'];
		db_query('UPDATE sp_sets SET table_name=\''.$edit['item']['table_name'].'\', item_id='.$edit['item']['id'].', set_topic_id='.$topic_id.' WHERE set_id='.$base['id']);
	}
	if( $base['deps'] === NULL ) {
		$base['deps'] = array();
	}

	$to_add = array();
	// first add the items that are in edit, but not in base	
	foreach ($edit['deps'] as $new_dep) {
		$add = true;
		foreach ($base['deps'] as $old_dep) {
			if (itemEquals($new_dep, $old_dep)) {
				$add = false;
				break;
			}
		}
		if($add) {
			$to_add[]=$new_dep;
		}
	}
	$to_remove = array();
	foreach ($base['deps'] as $old_dep) {
		$remove = true;
		foreach ($edit['deps'] as $new_dep) {
			if (itemEquals($old_dep, $new_dep)) {
				$remove=false;
				break;
			}
		}
		if($remove) {
			$to_remove[]=$old_dep;
		}
	}

	foreach ($to_add as $item) {
		db_query('INSERT sp_set_ingredient(set_id, table_name, item_id) values('.$base['id'].', \''.$item['table_name'].'\', '.$item['id'].')');
	}
	foreach ($to_remove as $item) {
		db_query('DELETE FROM sp_set_ingredient WHERE set_id = '.$base['id'].' and table_name = \''.$item['table_name'].'\' and item_id = '.$item['id']);
	}
	return getSetDefinition($base['id']);
}


function displayRezepteEditMenu($request) {
        $set = getSetDefinition($request['set']);
        if ($set === NULL) {
                displayErrorMessage("Rezept konnte nicht bearbeitet werden", "Set existiert nicht!", displayHistoryBackLink());
		return;
        }

	if ( isset($request['crafted']) ) {
		$set_changes = getSetDefinitionFromRequest($request);
		updateRezept($set, $set_changes);
		$set = getSetDefinition($request['set']);
	}
	displayForm($request, $set);
}
	
function displayRezepteCreateMenu($request) {
	if ( isset($request['crafted']) ) {
		$set_changes = getSetDefinitionFromRequest($request);
		$set = updateRezept(array(), $set_changes);
		$request['set'] = $set['id'];
		$request['action'] = 'edit';
		displayForm($request, $set);
		return;
	}
	displayForm($request, array());
}

function displayForm($request, $set) {

	$itemsMap = getItemsMap();
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <input name="choose" value="<?php echo $request['choose']; ?>" type="hidden" />
        <input name="action" value="<?php echo $request['action']; ?>" type="hidden" />
	<input name="set" value="<?php echo $set['id']; ?>" type="hidden" />

<table>
        <tr>
                <th colspan="2">Rezept <?php echo $set['id']; ?></th>
        </tr>
	<tr>
		<td>Topic</td>
		<td>
			<select name="topic">
				<option value="0">- Kein Topic -</option>
<?php
				$topics = getItemTopicsMap();
				foreach ($topics as $topic) {
					$selected = ($set['topic'] == $topic['name'])?'selected':'';
?>
					<option value="<?php echo $topic['id']; ?>" <?php echo $selected; ?>><?php echo $topic['name']; ?></option>
<?php
				}
?>

		</td>
	</tr>
	<tr>
		<td>Item</td>
		<td>
			<select name="crafted"><?php
			foreach ($itemsMap as $table) {
				foreach ($table as $item) {
					$selected = '';
					if($set['item']['table_name'] == $item['table_name'] && $set['item']['id'] == $item['id']) {
						$selected = 'selected="selected"';
					}
					
					echo '<option value="'.item2Value($item).'" '.$selected.'>'.item2Name($item).'</option>';
				}
			}
			?>
			</select>
		</td>
	</tr>
	<?php
		for ($i=0;$i<5;$i++){
	?>
        <tr>
                <td>Zutat <?php echo $i+1; ?>:</td>
                <td>
<?php
			$dep_item = $set['deps'][$i];
			echo '<select name="ingredient_'.$i.'">';
			echo '<option value="">None</option>';
                        foreach ($itemsMap as $table) {
                                foreach ($table as $item) {
                                        $selected = '';
                                        if($dep_item['table_name'] == $item['table_name'] && $dep_item['id'] == $item['id']) {
                                                $selected = 'selected="selected"';
                                        }
                                        
                                        echo '<option value="'.item2Value($item).'" '.$selected.'>'.item2Name($item).'</option>';
                                }
                        }
                        ?>
                        </select>

		</td>
	<?php
		}
	?>
	<tr>
		<td colspan="2" align="center"><input type="submit" value="&Auml;ndern" /></td>
  	</tr>
        <tr>
                <td colspan="2"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=rezept">Zum Rezeptemenu</a>
                </td>
        </tr>
        <tr>
                <td colspan="2"><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
                </td>
        </tr>
</table>
</form>
<?php
}


function displayRezepteDeleteMenu($request) {
	$set = getSetDefinition($request['set']);
	if ($set === NULL) {
		displayErrorMessage("Rezept konnte nicht gel&ouml;scht werden", "Set existiert nicht!", displayHistoryBackLink());
		return;
	}
	db_query('DELETE FROM sp_sets WHERE set_id = '.$set['id']);
	db_query('DELETE FROM sp_set_ingredient WHERE set_id = '.$set['id']);
	displayRezepteMainMenu(array());	
}

function displayRezepteTopicCreateMenu($request) {
	if(isset($request['persist'])) {
		$id = mysqli_fetch_row(db_query('SELECT max(id) +1 from sp_set_topics'))[0];
		if ($id === NULL) {
			$id = 1;
		}
		db_query('INSERT INTO sp_set_topics(id, name) values('.$id.', "'.$request['name'].'")');
		displayRezepteMainMenu(array());
	} else {
?>
<form action="" method="POST">
        <input name="persist" value="true" type="hidden" />
<table>
	<tr>
		<th colspan="2">Neues Rezept Topic erstellen</th>
	</tr>
	<tr>
		<th>Name:</th>
		<td><input name="name" value="" /></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" value="create" /></td>
	</tr>
        <tr>
                <td colspan="2"><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
                </td>
        </tr>
</table>
</form>

<?php
	}
}

function displayRezepteTopicDeleteMenu($request) {
	db_query('DELETE FROM sp_set_topics WHERE id = '. $request['id']);
        displayRezepteMainMenu(array());
}

function displayRezepteTopicEditMenu($request) {
	if(isset($request['persist'])) {
		db_query('UPDATE sp_set_topics SET name = "'.$request['name'].'" WHERE id = '.$request['id']);
		displayRezepteMainMenu(array());
	} else {
		$topic = getItemTopicsMap()[$request['id']];
?>
<form action="" method="POST">
        <input name="persist" value="true" type="hidden" />
	<input name="id" value="<?php echo $topic['id']; ?>" type="hidden" />
<table>
	<tr>
		<th colspan="2">Topic editieren</th>
	</tr>
	<tr>
		<th>Name:</th>
		<td><input name="name" value="<?php echo $topic['name']; ?>" /></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" value="edit" /></td>
	</tr>
        <tr>
                <td colspan="2"><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
                </td>
        </tr>
</table>
</form>

<?php
	}

}


function swapRezepteTopicOrder( $lhs_id, $rhs_id ) {
	if ( $lhs_id !== NULL && $rhs_id !== NULL ) {
		db_query('UPDATE sp_set_topics SET id = 0 WHERE id = '.$lhs_id );
		db_query('UPDATE sp_set_topics SET id = '.$lhs_id.' WHERE id = '.$rhs_id );
		db_query('UPDATE sp_set_topics SET id = '.$rhs_id.' WHERE id = 0' );
	}
}

function displayRezepteTopicUpMenu($request) {
	$topic_id = $request['id'];
	$other = mysqli_fetch_row(db_query('SELECT max(id) from sp_set_topics WHERE id < '.$topic_id));
	if ( $other ) {
		swapRezepteTopicOrder($topic_id, $other[0]);
	}
	displayRezepteMainMenu(array());
}

function displayRezepteTopicDownMenu($request) {
	$topic_id = $request['id'];
	$other = mysqli_fetch_row(db_query('SELECT min(id) from sp_set_topics WHERE id > '.$topic_id));
	if ( $other ) {
		swapRezepteTopicOrder($topic_id, $other[0]);
	}
	displayRezepteMainMenu(array());
}



