<?php

function getDefaultData() {
	$array = array();
	$array['race_type']=array(
	array(
		'name'	=> 'Onepiece',
		'id'	=> 1,
		'gm_only' => false,
	),
	array(
		'name'	=> 'Dragonball',
		'id'	=> 2,
		'gm_only' => false,
	),
	array(
		'name'	=> 'NPC',
		'id'	=> 3,
		'gm_only' => true,
	),
	);

	// Dieses Array ist zum simulieren der Datenbank da!
	$array['race']=array(

	array(
		'name'		=> 'Mensch',
		'type'		=> 2,
		'id'		=> 1,
		'hp'		=> 150,
		'mp'		=> 30,
		'str'		=> 15,
		'def'		=> 9,
		'spd'		=> 13,
		'lck'		=> 4,
		'stm'		=> 8,
		'special' 	=> false
	),
	array(
		'name'		=> 'Saiyajin',
		'type'		=> 2,
		'id'		=> 2,
		'hp'		=> 120,
		'mp'		=> 30,
		'str'		=> 18,
		'def'		=> 10,
		'spd'		=> 9,
		'lck'		=> 5,
		'stm'		=> 10,
		'special' 	=> false
	),
	array(
		'name'		=> 'Dämon',
		'type'		=> 2,
		'id'		=> 3,
		'hp'		=> 160,
		'mp'		=> 50,
		'str'		=> 8,
		'def'		=> 9,
		'spd'		=> 18,
		'lck'		=> 4,
		'stm'		=> 5,
		'special' 	=> false
	),
	array(
		'name'		=> 'Mutant',
		'type'		=> 2,
		'id'		=> 4,
		'hp'		=> 180,
		'mp'		=> 15,
		'str'		=> 13,
		'def'		=> 9,
		'spd'		=> 18,
		'lck'		=> 4,
		'stm'		=> 5,
		'special' 	=> false
	),
	array(
		'name'		=> 'Cyborg',
		'type'		=> 2,
		'id'		=> 5,
		'hp'		=> 250,
		'mp'		=> 20,
		'str'		=> 11,
		'def'		=> 30,
		'spd'		=> 0,
		'lck'		=> 0,
		'stm'		=> 0,
		'special' 	=> false
	),
	array(
		'name'		=> 'Namekianer',
		'type'		=> 2,
		'id'		=> 6,
		'hp'		=> 100,
		'mp'		=> 10,
		'str'		=> 18,
		'def'		=> 10,
		'spd'		=> 14,
		'lck'		=> 6,
		'stm'		=> 10,
		'special' 	=> false
	),
	array(
		'name'		=> 'Tsufurujin',
		'type'		=> 2,
		'id'		=> 13,
		'hp'		=> 50,
		'mp'		=> 120,
		'str'		=> 0,
		'def'		=> 0,
		'spd'		=> 11,
		'lck'		=> 30,
		'stm'		=> 0,
		'special'	=> false,
	),

	// Start 'One Piece'
	array(
		'name'		=> 'Pirat',
		'type'		=> 1,
		'id'		=> 7,
		'hp'		=> 100,
		'mp'		=> 25,
		'str'		=> 20,
		'def'		=> 10,
		'spd'		=> 15,
		'lck'		=> 3,
		'stm'		=> 7,
		'special' 	=> false
	),
	// In-Between Special chars
	array(
		'name'		=> 'Shichibukai',
		'type'		=> 1,
		'id'		=> 8,
		'hp'		=> 1000,
		'mp'		=> 150,
		'str'		=> 100,
		'def'		=> 100,
		'spd'		=> 100,
		'lck'		=> 100,
		'stm'		=> 100,
		'tp'		=> 500,
		'special' 	=> true,
		'item'		=> 15
	),
	array(
		'name'		=> 'Kaioshin',
		'type'		=> 2,
		'id'		=> 9,
		'hp'		=> 1000,
		'mp'		=> 150,
		'str'		=> 100,
		'def'		=> 100,
		'spd'		=> 100,
		'lck'		=> 100,
		'stm'		=> 100,
		'tp'		=> 500,
		'special' 	=> true,
		'item'		=> 3
	),
	// Continue 'One Piece'
	array(
		'name'		=> 'Schwertkämpfer',
		'type'		=> 1,
		'id'		=> 10,
		'hp'		=> 100,
		'mp'		=> 25,
		'str'		=> 25,
		'def'		=> 10,
		'spd'		=> 10,
		'lck'		=> 2,
		'stm'		=> 8,
		'special' 	=> false
	),
	array(
		'name'		=> 'Grandline Maschine',
		'type'		=> 1,
		'id'		=> 11,
		'hp'		=> 400,
		'mp'		=> 5,
		'str'		=> 5,
		'def'		=> 5,
		'spd'		=> 5,
		'lck'		=> 0,
		'stm'		=> 10,
		'special' 	=> false
	),
	// NPC chars
	array(
		'name'		=> 'NPC',
		'type'		=> 3,
		'id'		=> 12,
		'hp'		=> 0,
		'mp'		=> 0,
		'str'		=> 0,
		'def'		=> 0,
		'spd'		=> 0,
		'lck'		=> 0,
		'stm'		=> 0,
		'special'	=> true
	),
	array(
		'name'		=> 'MONSTER',
		'type'		=> 3,
		'id'		=> 14,
		'hp'		=> 0,
		'mp'		=> 0,
		'str'		=> 0,
		'def'		=> 0,
		'spd'		=> 0,
		'lck'		=> 0,
		'stm'		=> 0,
		'special'	=> true
	),
	);
	return $array;
}

function createLink($name, $values, $confirmation = NULL) {
	if ( $confirmation !== NULL ) {
		$link = '<a href="javascript:if(prompt(\'Best&auml;tige mit der Eingabe von \\\''.$confirmation.'\\\'\')==\''.$confirmation.'\') window.location.href=\'?choose=rassen';
	} else {
		$link = '<a href="?choose=rassen';
	}
        foreach ($values as $key => $value) {
		$link .= '&'.$key.'='.$value;
	}


	if ( $confirmation !== NULL ) {
		$link .= '\'">'.$name.'</a>';
	} else {
		$link .= '">'.$name.'</a>';
	}

	return $link;
}

function displayRassen($request) {
	$showOverview = true;
	if($request['action'] == 'setup') {
		$showOverview = displayRassenSetup( $request );
	} else if ($request['action'] == 'edit') {
		$showOverview = displayRassenEdit( $request );
	} else if ($request['action'] == 'delete') {
		$showOverview = displayRassenDelete( $request );
	} else if ($request['action'] == 'edit_type') {
		$showOverview = displayRassenTypeEdit( $request );
	} else if ($request['action'] == 'delete_type') {
		$showOverview = displayRassenTypeDelete( $request );
	} else if ($request['action'] == 'create') {
		$showOverview = displayRassenCreate( $request );
	} else if ($request['action'] == 'create_type') {
		$showOverview = displayRassenTypeCreate( $request );
	}
?>
			<table>
<?php
	$num = mysqli_num_rows(db_query('SELECT * FROM rassen_type'));
	if($num == 0) {
		echo '<tr><th colspan="4">'.createLink('setup rassen database', array('action' => 'setup')).'</a></th></tr>';
	} else if ( $showOverview ) {
		$race_types = getRaceTypes();
?>
				<tr>
					<th colspan="4">
						Rassen-Typ &Uuml;bersicht
					</th>
				</tr>
				<tr>
					<th align="center">Name</th>
					<th align="center">GM-Only</th>
					<th align="center" colspan="2">Aktion</th>
				</tr>
<?php
		foreach ($race_types as $type) {
			$editLink = createLink('Edit', array('action' => 'edit_type', 'id' => $type['id']));
			$deleteLink = createLink('Delete', array('action' => 'delete_type', 'id' => $type['id']), $type['name']);
			echo '<tr><td>'.$type['name'].'</td><td>'.($type['gm_only']?'Ja':'Nein').'</td><td>'.$editLink.'</td><td>'.$deleteLink.'</td></tr>'."\n";
		}
?>
				<tr><td colspan="4" align="center"><?php echo createLink('Neuer Rassentyp', array('action' => 'create_type')); ?></td></tr>
	
				<tr><th colspan="4"><hr/></th></tr>
				<tr>
					<th colspan="4">
						Rassen &Uuml;bersicht
					</th>
				</tr>
				<tr>
					<th align="center">Name</th>
					<th align="center">Type</th>
					<th align="center" colspan="2">Aktion</th>
				</tr>
<?php
		foreach ($race_types as $type) {
			$races = getRacesByType($type);
			foreach ($races as $race) {
				$editLink = createLink('Edit', array('action' => 'edit', 'id' => $race['id']));
				$deleteLink = createLink('Delete', array('action' => 'delete', 'id' => $race['id']), $race['name']);
				echo '<tr><td>'.$race['name'].'</td><td>'.$type['name'].'</td><td>'.$editLink.'</td><td>'.$deleteLink.'</td></tr>'."\n";
			}
		}
?>
				<tr><td colspan="4" align="center"><?php echo createLink('Neue Rasse', array('action' => 'create')); ?></td></tr>
                                <tr><th colspan="4"><hr/></th></tr>
<?php
	} else {
?>
				<tr>
					<td>
						<?php echo createLink('Zur &Uuml;bersicht', array())."\n"; ?>
					</td>
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
}

function displayRassenSetup( $request ) {
	$array = getDefaultData();
	foreach($array['race_type'] as $race_type) {
		db_query('INSERT INTO rassen_type(id, name, gm_only) values('.$race_type['id'].', \''.$race_type['name'].'\', '.($race_type['gm_only']?'1':'0').')');
	}
	foreach($array['race'] as $r) {
		db_query('INSERT INTO rassen(name, type, id, hp, mp, str, def, spd, lck, stm, special) '.
				'values(\''.$r['name'].'\', '.$r['type'].','.$r['id'].', '.$r['hp'].','.$r['mp'].','.$r['str'].','.$r['def'].','.$r['spd'].','.$r['lck'].','.$r['stm'].','.($r['special']?'1':'0').')');
	}
	return true;
}


function createRassenForm( $data = NULL ) {
	$action = ($data === NULL) ? 'create' : 'edit';
	$items = array();
	$qry = db_query('SELECT id, name FROM sp_item ORDER BY name');
	while ($row = mysqli_fetch_assoc($qry)) {
		$items[] = array('name' => $row['name'], 'id' => $row['id']);
	}
?>
	<form action="" method="POST">
		<input name="persist" value="true" type="hidden" />
<?php
	if ($data !== NULL) {
?>
		<input name="id" value="<?php echo $data['id']; ?>" type="hidden" />
<?php
	}
?>

		<table>
			<tr><th colspan="2">Rasse</th></tr>
			<tr><th>Name</th><td><input name="name" value="<?php echo $data['name']; ?>" /></td></tr>
			<tr><th>Typ</th><td>
				<select name="type">
<?php
				$race_types = getRaceTypes();
				foreach ($race_types as $race_type) {
					$selected = ($race_type['id'] == $data['type']);
?>
					<option value="<?php echo $race_type['id']; ?>" <?php echo $selected?'selected':''; ?>><?php echo $race_type['name']; ?></option>
<?php
				}
?>
				</select>
			<tr><th>HP</th><td><input name="hp" value="<?php echo $data['hp']; ?>" /></td></tr>
			<tr><th>MP</th><td><input name="mp" value="<?php echo $data['mp']; ?>" /></td></tr>
			<tr><th>St&auml;rke</th><td><input name="str" value="<?php echo $data['str']; ?>" /></td></tr>
			<tr><th>Verteidigung</th><td><input name="def" value="<?php echo $data['def']; ?>" /></td></tr>
			<tr><th>Geschwindigkeit</th><td><input name="spd" value="<?php echo $data['spd']; ?>" /></td></tr>
			<tr><th>Gl&uuml;ck</th><td><input name="lck" value="<?php echo $data['lck']; ?>" /></td></tr>
			<tr><th>Ausdauer</th><td><input name="stm" value="<?php echo $data['stm']; ?>" /></td></tr>
			<tr><th>Special</th><td><input name="special" <?php echo $data['special']?'checked':''; ?> type="checkbox" /></td></tr>
			<tr>
				<th>Item (nur Special)</th>
				<td>
					<select name="item">
						<option value="">- Keins -</option>
<?php
						foreach ($items as $item) {
							$selected = $item['id'] == $data['item'] ? 'selected':'';
							echo '<option value="'.$item['id'].'" '.$selected.'>'.$item['name'].'</option>'."\n";
						}
?>
					</select>
				</td>
			</tr>
			<tr><td colspan="2" align="center"><input type="submit" value="<?php echo $action; ?>" /></td></tr>
		</table>
	</form>
<?php
}

function displayRassenCreate( $request ) {
	if ( $request['persist'] ) {
		$special = ($request['special']=='on') ? 'true' : 'false';
		$item = ($request['item'] != NULL) ? $request['item'] : 'NULL';
		$id = mysqli_fetch_row(db_query('SELECT max(id) + 1 FROM rassen'));
		$id = $id[0];

		$data = array('"'.$request['name'].'"', $request['type'], $request['hp'], $request['mp'], $request['str'], $request['def'], $request['spd'], $request['lck'], $request['stm'], $special, $item);
		db_query('INSERT INTO rassen(id, name, type, hp, mp, str, def, spd, lck, stm, special, item) values('.$id.', '.join(',', $data).')');
		return true;
	} else {
		createRassenForm();
		return false;
	}
}

function displayRassenEdit( $request ) {
	if ( $request['persist'] ) {
		$special = ($request['special']=='on') ? 'true' : 'false';
		$item = ($request['item'] != NULL) ? $request['item'] : 'NULL';
		$data = array(
				'name' => '"'.$request['name'].'"',
				'type' => $request['type'],
				'hp' => $request['hp'],
				'mp' => $request['mp'],
				'str' => $request['str'],
				'def' => $request['def'],
				'spd' => $request['spd'],
				'lck' => $request['lck'],
				'stm' => $request['stm'],
				'special' => $special,
				'item' => $item
		);

		$data_entries = array();
		foreach ($data as $key => $value) {
			$data_entries[] = $key.'='.$value;
		}

		db_query('UPDATE rassen SET '.join(', ', $data_entries).' WHERE id = '.$request['id']);
		return true;
	} else {
		createRassenForm(getRaceById($request['id']));
		return false;
	}
}

function displayRassenDelete( $request ) {
	db_query('DELETE FROM rassen WHERE id = '.$request['id']);
	return true;
}

function createRassenTypeForm( $data = NULL ) {
	$action = ($data === NULL) ? 'create' : 'edit';
?>
	<form action="" method="POST">
		<input name="persist" value="true" type="hidden" />
<?php
	if ($data !== NULL) {
?>
		<input name="id" value="<?php echo $data['id']; ?>" type="hidden" />
<?php
	}
?>

		<table>
			<tr><th colspan="2">Rassen-typ</th></tr>
			<tr><th>Name</th><td><input name="name" value="<?php echo $data['name']; ?>" /></td></tr>
			<tr><th>GM-Only</th><td><input name="gm_only" type="checkbox" <?php echo $data['gm_only']?'checked':''; ?> /></td></tr>
			<tr><td colspan="2" align="center"><input type="submit" value="<?php echo $action; ?>" /></td></tr>
		</table>
	</form>
<?php
}

function displayRassenTypeCreate( $request ) {
	if ( $request['persist'] ) {
		$gm_only = ($request['gm_only']=='on') ? 'true' : 'false';
		$id = mysqli_fetch_row(db_query('SELECT max(id) + 1 FROM rassen_type'));
		$id = $id[0];

		db_query('INSERT INTO rassen_type(id, name, gm_only) values('.$id.', "'.$request['name'].'", '.$gm_only.')');
		return true;
	} else {
		createRassenTypeForm();
		return false;
	}
}

function displayRassenTypeEdit( $request ) {
	if ( $request['persist'] ) {
		$gm_only = ($request['gm_only']=='on') ? 'true' : 'false';
		db_query('UPDATE rassen_type SET name = "'.$request['name'].'", gm_only = '.$gm_only.' WHERE id = '.$request['id']);
		return true;
	} else {
		createRassenTypeForm(getRaceTypeById($request['id']));
		return false;
	}
}

function displayRassenTypeDelete( $request ) {
	db_query('DELETE FROM rassen_type WHERE id = '.$request['id']);
	return true;
}

