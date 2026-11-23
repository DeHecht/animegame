<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/rassen.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$wahl = validateStringCritical($_GET['wahl']);
$char_name = validateName($_GET['char_name']);

$char_vorhanden = mysqli_num_rows(db_query('SELECT id FROM chars WHERE name like \''.$char_name.'%\' LIMIT 1'));

function displayHighscore($wahl, $char_name, $user, $char_vorhanden){

	if($wahl === null || $wahl=='powerlevel') {
		$wahl_x = 'Powerlevel';
		$wahl='powerlevel';
	} else if($wahl == 'level') {
		$wahl_x = 'Level';
	} else if($wahl == 'siege') {
		$wahl_x = 'Siege'; }
	else if($wahl == 'niederlagen') {
		$wahl_x = 'Niederlagen'; }
	else if($wahl == 'preis') {
		$wahl_x = 'Preis';
//	} else if($wahl != 'geld'){
//		return;
	} else{
		return;
	}

	if($wahl == 'geld') {
		$choice_s = db_query('SELECT geld, id FROM user where id >= 2 ORDER BY geld DESC LIMIT 50');
	} else {
		$npc_race = getRaceTypeName('NPC'); // do not remove npc (there is a separate setting for this)
		$races = getRacesByType($npc_race); // remove all NPC
		foreach($races as $race) {
			$exclude_race_ids[] = '\''.$race['id'].'\'';
		}

		$sql = 'SELECT c.name, c.level, c.besitzer, c.starke, c.preis, c.speed, c.glueck, c.bild, c.verteidigung, c.ausdauer, c.siege, c.niederlagen, c.id as charid, starke+verteidigung+speed+ausdauer+glueck as powerlevel FROM chars c ' .
				'WHERE rasse NOT IN('.implode(',',$exclude_race_ids).')  ORDER BY ('.$wahl.') DESC LIMIT 50';

//		Uncomment for hp/mp in PL
//		$sql = 'SELECT cl.clanzeichen, u.nickname, u.id as userid, c.name, c.level, c.besitzer, c.starke, c.preis, c.speed, c.glueck, c.bild, c.verteidigung, c.ausdauer, c.siege, c.niederlagen, c.id as charid, starke+verteidigung+speed+ausdauer+glueck+substring_index(hp,\',\',-1)/10+substring_index(mp,\',\',-1)/5 as powerlevel' .
//				' FROM chars as c INNER JOIN user as u on c.besitzer = u.id' .
//				' LEFT JOIN clan as cl on u.clan = cl.id ' .
//				'WHERE rasse != \'NPC\' ORDER BY ('.$wahl.') DESC LIMIT 50';
//		echo $sql.'<br>';
		$choice_s = db_query($sql);
	}
	?>

	<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%"  height="1">
		<!-- MSTableType="layout" -->
		<tr>
			<td valign="middle" colspan="3" align="center">
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
					<input type="hidden" name="as" value="ranglist">
					<input type="hidden" name="char_name" value="<?php echo $char_name; ?>"></input>
					<input type="hidden" name="as" value="ranglist"></input>
					<select id="input" name="wahl">
	<?php
		$choices = array('Powerlevel','Siege','Niederlagen','Preis','Level');
		for($i=0;$i<count($choices);$i++){
			if(strtolower($choices[$i]) == $wahl){
				$selected = ' selected';
			} else{
				$selected = '';
			}
			echo '			<option value="'.strtolower($choices[$i]).'"'.$selected.'>Nach '.$choices[$i].'</option>'."\n";
		}
	?>
					</select>
					<input id="input" type=submit value="ordnen">
				</form>
			</td>
			<th valign="middle" colspan="3" height="44" align="center">
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
					<input type="hidden" name="as" value="ranglist">
					<input type="hidden" name="wahl" value="<?php echo $wahl; ?>"></input>
					<input type="hidden" name="as" value="ranglist"></input>
					Charakter Suchen:
					<input id="input" name="char_name" value="<?php echo $char_name; ?>">
					<input id="input" type=submit value="suchen">
				</form>
			</th>
		</tr>
	<?php
	if($wahl == 'geld') {
	?>
		<tr>
			<th valign="middle" height="25" align="center">Platz</th>
			<th valign="middle" height="25" align="center">User</th>
			<th valign="middle" height="25" align="center">Geld</th>
		</tr>
	<?php
	} else {
	?>
		<tr>
			<th valign="middle" height="25" align="center">Platz</th>
			<th valign="middle" height="25" align="center">Charakter</th>
			<th valign="middle" height="25" align="center">Trainer</th>
			<th valign="middle" height="25" align="center"><?php echo $wahl_x; ?></th>
			<th valign="middle" height="25" align="center">Siege / Niederlagen</th>
			<th valign="middle" height="25" align="center">Preis</th>
		</tr>
	<?php
	}
	$x=1;
	$found = 0;
	while($row = mysqli_fetch_assoc($choice_s)) {
		if($wahl != "geld") {
			if($row['besitzer'] == $user['id']) {
				$cols[$x] = 'bgcolor="#666666"';
			}
			if($row['name'] == $char_name) {
				$cols[$x] = 'bgcolor="#666666"';
				$found = 1;
			}

			$row['bild'] = getPicture($row);

			$row['wahl'] = $row[$wahl];

			$clan_logo = explode(',',$row['clanzeichen']);
		}
		if($wahl == 'geld') {
		?>
			<tr>
				<th <?php echo $cols[$x];?> align="center"><?php echo $x; ?></th>
				<th <?php echo $cols[$x];?> align="center"><?php echo generateUserLinkByID($row['id']); ?></th>
				<th <?php echo $cols[$x];?> align="center">???</th>
			</tr>


		<?php
		} else {
		?>
			<tr>
				<th <?php echo $cols[$x];?> align="center"><?php echo $x; ?></th>
				<th <?php echo $cols[$x];?> align="center"><?php echo displayCharLink($row['charid'],$row['name'],$row['bild']); ?></th>
				<th <?php echo $cols[$x];?> align="center"><?php echo generateUserLinkByID($row['besitzer']); ?></th>
				<th <?php echo $cols[$x];?> align="center"><?php echo $row['wahl']; ?></th>
				<th <?php echo $cols[$x];?> align="center"><?php echo $row['siege'].' / '.$row['niederlagen']; ?></th>
				<th <?php echo $cols[$x];?> align="center"><?php echo $row['preis']; ?></th>
			</tr>
		<?php
		}
		$x++;
	}
	// Wird nach einem Char gesucht oder ist noch kein Char des Users gefunden worden, dann
	// zeige dessen Position an
	if($char_name && $found < 1 && $wahl != 'geld' && !empty($char_vorhanden)) {
		$char_x_name = mysqli_fetch_assoc(db_query('SELECT id, name, besitzer, level, starke, preis, speed, bild, verteidigung, ausdauer, siege, niederlagen, id, starke+verteidigung+speed+ausdauer+glueck as powerlevel FROM chars WHERE name like \''.$char_name.'%\' LIMIT 1'));
		$nick_name2 = mysqli_fetch_assoc(db_query("SELECT nickname FROM user WHERE id='".$char_x_name['besitzer']."' LIMIT 1"));
		$cols[$x] = '666666';
		$u=0;
		if($wahl_x == 'Powerlevel') {
			$z = mysqli_fetch_assoc(db_query('SELECT COUNT(id) as anzahl FROM chars WHERE '.$char_x_name[$wahl].' < starke+verteidigung+speed+ausdauer+glueck'));
			$z = $z['anzahl'];
		} else {
			$z = mysqli_fetch_assoc(db_query('SELECT COUNT(id) as anzahl FROM chars WHERE '.$char_x_name[$wahl].' < '.$wahl));
			$z = $z['anzahl'];
		}

	?>
		<tr>
			<th bgcolor="<?php echo $cols[$x];?>" align="center"><?php echo $z; ?></th>
			<th bgcolor="<?php echo $cols[$x];?>" align="center"><?php echo displayCharLink($char_x_name['id'],$char_x_name['name'],$char_x_name['bild']); ?></th>
			<th bgcolor="<?php echo $cols[$x];?>" align="center"><?php echo generateUserLinkByID($char_x_name['besitzer']); ?></th>
			<th bgcolor="<?php echo $cols[$x];?>" align="center"><?php echo $char_x_name[$wahl]; ?></th>
			<th bgcolor="<?php echo $cols[$x];?>" align="center"><?php echo $char_x_name['siege'].' / '.$char_x_name['niederlagen']; ?></th>
			<th bgcolor="<?php echo $cols[$x];?>" align="center"><?php echo $char_x_name['preis']; ?></th>
		</tr>

	<?php
	}
	?>
	</table>
	<?php
}

displayHighscore($wahl, $char_name, $user_ida, $char_vorhanden);

?>
