<?php
/*
 * Created on 31.05.2011
 *
 * @copyright (c) 2011 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

defineIfNotDefined('TURNIER_ANFAENGER_GEBUEHR', 10000);
defineIfNotDefined('TURNIER_WOCHEN_GEBUEHR', 50000);
defineIfNotDefined('TURNIER_WOCHENST_GEBUEHR', 50000);

include_once(ROOT_PATH.'/include/tournament.inc.php');

function persistTournamentChanges($request) {
	$data = getTournamentType($request['id']);

	$changes = array();
	foreach ($data as $key => $value) {
		if (isset($request[$key])) {
			if ($request[$key] == 'on') {
				$value = 1;
			} else {
				$value = $request[$key];
			}
		} else {
			$value = NULL;
		}
		
		if ($key == 'id') {
			continue;
		} else if ($value == "" || $value === NULL) {
			if (strpos($key, 'excl_') === false) {
				$changes[] = $key.' = NULL';	
			} else {
				$changes[] = $key.' = 0';	
			}
		} else if ($key == 'name' || $key == 'abbrevation' || $key == 'icon' || $key == 'fruit_type' || $key == 'icon') {
			$changes[] = $key.'=\''.$value.'\'';
		} else {
			$changes[] = $key.'='.$value;
		}
	}

	$sql = 'UPDATE tournament_types SET '.join(', ', $changes).' WHERE id = '.$request['id'];
	// echo $sql;
	db_query($sql);
	return db_affected_rows();
}

function displayTournamentForm($request, $data) {
	
	$form_data = array();
	foreach ($data as $key => $value) {
		$form_data[$key] = isset($request[$key]) ? $request[$key] : $value;
	}

?>
<form method="POST" action="">
	<table>
		<tr>
			<th colspan="2">Turnier editieren</th>
		</tr>
		<tr>
			<td colspan="2" align="center"><hr /></td>
		</tr>
		<tr>
			<td align="left">Name:</td>
			<td>
				<input type="text" name="name" value="<?php echo $form_data['name']; ?>" />
			</td>
		</tr>
		<tr>
			<td align="left">Abk&uuml;rzung:</td>
			<td>
				<input type="text" name="abbrevation" value="<?php echo $form_data['abbrevation']; ?>" />
			</td>
		</tr>
		<tr>
			<td align="left">Icon:</td>
			<td>
				<input type="text" name="icon" value="<?php echo $form_data['icon']; ?>" />
			</td>
		</tr>
		<tr>
			<td align="left">Teilnehmer:</td>
			<td>
				<select name="competitors">
<?php 
					for ($i = 2; $i <= 512; $i *= 2 ) {
						$selected = '';
						if ( $form_data['competitors'] == $i ) {
							$selected = ' selected';
						}
?>
						<option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>
<?php 
					}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Fusion ausschließen?</td>
			<td>
				<input type="checkbox" name="excl_fusions" <?php echo $form_data['excl_fusions']?'checked="checked"':''; ?> />
			</td>
		</tr>
		<tr>
			<td>Gewinner ausschließen?</td>
			<td>
				<input type="checkbox" name="excl_winners" <?php echo $form_data['excl_winners']?'checked="checked"':''; ?> />
			</td>
		</tr>
		<tr>
			<td>Spezialchars ausschließen?</td>
			<td>
				<input type="checkbox" name="excl_special_chars" <?php echo $form_data['excl_special_chars']?'checked="checked"':''; ?> />
			</td>
		</tr>
		<tr>
			<td>Maximale Teilnahme von Fusionen</td>
			<td>
				<select name="excl_fusion_particip_count">
<?php 
					for ($i = 0; $i < 20; ++$i ) {
						$selected = '';
						if ( $form_data['excl_fusion_particip_count'] == $i ) {
							$selected = ' selected';
						}
?>
						<option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>
<?php 
					}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Mindest Level</td>
			<td>
				<select name="min_level">
<?php 
				for ($i = 1; $i < CHAR_MAX_LEVEL; ++$i ) {
						$selected = '';
						if ( $form_data['min_level'] == $i ) {
							$selected = ' selected';
						}
?>
						<option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>
<?php 
					}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Maximales Level</td>
			<td>
				<select name="max_level">
					<option value="">-</option>
<?php 
					for ($i = 1; $i < CHAR_MAX_LEVEL; ++$i ) {
						$selected = '';
						if ( $form_data['max_level'] == $i ) {
							$selected = ' selected';
						}
?>
						<option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>
<?php 
					}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Reihenfolge jede Runde neu?</td>
			<td>
				<input type="checkbox" name="randomize" <?php echo $form_data['randomize']?'checked="checked"':''; ?> />
			</td>
		</tr>
		<tr>
			<td>Ausrüstung ignorieren?</td>
			<td>
				<input type="checkbox" name="without_equip" <?php echo $form_data['without_equip']?'checked="checked"':''; ?> />
			</td>
		</tr>
		<tr>
			<td>Frucht Droprate?</td>
			<td>
				<select id="input" name="fruit_chance">
<?php
				for ($i = 0; $i <= 100; ++$i) {
					$selected = '';
					if ( $form_data['fruit_chance'] == $i ) {
							$selected = ' selected';
					}
?>
					<option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?>%</option>
<?php
				}
?>
			</select>
			</td>
		</tr>
		<tr>
			<td>Fruchttyp</td>
			<td>
				<select id="input" name="fruit_type">
					<option value="">-</option>
<?php
					$types = array('Logia', 'Paramecia', 'Zoan', 'natur');
					foreach ($types as $type) {
						$selected = '';
						if ( $form_data['fruit_type'] == $type) {
							$selected = ' selected';
						}
						?>
						<option value="<?php echo $type; ?>" <?php echo $selected; ?>><?php echo $type; ?></option>
<?php
					}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Teilnameart</td>
			<td>
				<select id="input" name="gain">
<?php
					$types = array( TOURNAMENT_GAIN_ANMELDUNG => 'Anmeldung', TOURNAMENT_GAIN_PL => 'Powerlevel', TOURNAMENT_GAIN_LEVEL => 'Level' );
					foreach ($types as $key => $value) {
						$selected = '';
						if ( $form_data['gain'] == $key) {
							$selected = ' selected';
						}
						?>
						<option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
<?php
					}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Eventuelle Anmeldegebühr</td>
			<td>
				<input type="number" name="entrance_fee" value="<?php echo $form_data['entrance_fee']; ?>" />
			</td>
		</tr>
		<tr>
			<td>Wochentag</td>
			<td>
				<select id="input" name="day_of_week">
					<option value="">-</option>
<?php
				$translation = array('', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag');
				for ($i = 1; $i < count($translation); ++$i) {
					$selected = '';
					if ( $form_data['day_of_week'] == $i ) {
							$selected = ' selected';
					}
?>
					<option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $translation[$i]; ?></option>
<?php
				}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Tag des Monats</td>
			<td>
				<select id="input" name="day_of_month">
					<option value="">-</option>
<?php
				for ($i = 1; $i <= 28; ++$i) {
					$selected = '';
					if ( $form_data['day_of_month'] == $i ) {
							$selected = ' selected';
					}
?>
					<option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>
<?php
				}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Tageszeit</td>
			<td>
				<select id="input" name="hour_of_day">
<?php
				for ($i = 0; $i < 24; ++$i) {
					$selected = '';
					if ( $form_data['hour_of_day'] == $i ) {
							$selected = ' selected';
					}
?>
					<option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?> Uhr</option>
<?php
				}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center"><hr /></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><input type="submit" value="speichern" /></td>
		</tr>
		<tr>
			<td align="center" colspan="2"><a
				href="<?php echo $_SERVER['PHP_SELF'].'?choose=tournament'; ?>">Zur&uuml;ck</a>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="2"><a
				href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
			</td>
		</tr>
	</table>
</form>
<?php
}

function displayStartTournament($id) {
	runTournament($id);
}

function displayEditTournament($request) {
	$persisted = false;
	if(isset($request['name'])) {
		$persisted = persistTournamentChanges($request);
		if (!$persisted) {
			echo '<p>Speichern nicht erfolgreich.</p>';
		}
	}
	
	if ( !$persisted ) {
		displayTournamentForm($request, getTournamentType($request['id']));
	} else {
		echo 'Editieren erfolgreich. <a href="'.$_SERVER['PHP_SELF'].'?choose=tournament">Weiter</a>';
	}
}

function displayTournamentMenu($request) {
	switch ($request['action']) {
		case 'start':
			displayStartTournament($request['id']);
			break;
		case 'edit':
			displayEditTournament($request);
			break;
			
		default:
			?>
<table>
	<tr>
		<th colspan="3">Turniere</th>
	</tr>
<?php
$tournaments = getTournamentTypes();
foreach ($tournaments as $tournament) {
?>
	<tr>
		<td><?php echo $tournament['name']?>:</td>
<?php
		if (canStartTournament($tournament)) {
?>
			<td><a href="<?php $_SERVER['PHP_SELF'] ?>?choose=tournament&action=start&id=<?php echo $tournament['id']; ?>">starten</a></td>
<?php
		} else {
?>
			<td>starten</td>
<?php
}
?>
		</td>
<?php
		if (canEditTournament($tournament)) {
?>
			<td><a href="<?php $_SERVER['PHP_SELF'] ?>?choose=tournament&action=edit&id=<?php echo $tournament['id']; ?>">edit</a></td>
<?php
		} else {
?>
			<td>edit</td>
<?php
}
?>
	</tr>
<?php	
}
?>
	<tr>
		<td colspan="3">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
		</td>
	</tr>
</table>
	<?php
	}
}

?>
