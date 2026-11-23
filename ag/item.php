<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once (ROOT_PATH . '/include/char.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/messagefunctions.inc.php');
include_once(ROOT_PATH.'/include/auktion_functions.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$item_id1 = validateUnsignedInteger($_GET['item_id1'], null);
$char_id2 = validateUnsignedInteger($_GET['char_id2'], null);
$item_id = validateUnsignedInteger($_GET['item_id'], null);
$sp_item = validateUnsignedInteger($_GET['sp_item'], null);
$tausch_item = validateString($_GET['item_id']);
$tausch_anzahl = validateUnsignedInteger($_GET['anzahl'], null);
$tausch_geld= validateUnsignedInteger($_GET['geld'], null);
$tausch_user  = validateName($_GET['user']);

// Unkritisch
$charm = $_GET['charm'];
$ak_no = $_GET['ak_no'];

?>

<script type="text/javascript">
<!--
function disablebutton()
{
if(document.forms[0].elements['char_id2'].value!=-1)
document.forms[0].elements['submit'].disabled=false;
else document.forms[0].elements['submit'].disabled=true;

}
-->
</script>

<?php
// Die etwas abgewandelten Methode aus dem auktion_functions.php
function getTradeItems($userid){
	$result = NULL;
	$index = 0;

	$arenalvl = mysqli_fetch_array(db_query("SELECT level FROM arena WHERE besitzer = ".$userid.";"));
	if (!$arenalvl) {
		$arenalvl['level'] = 0;
	}
	// Die etwas abgewandelten SQL-Querys aus dem auktion_functions.php
	$query = 'SELECT i.id, i.name, count(i.id) AS anzahl, tausch_anzahl, tausch_lvl, \'ware\' as tablename FROM ware w INNER JOIN item i ON(i.id=w.item_id) WHERE w.user = '.$userid.' AND s_type = \'Trank\' AND tausch_lvl <= '.$arenalvl['level'].' GROUP BY i.id
					union
					SELECT i.id, i.name,count(i.id) AS anzahl, tausch_anzahl, tausch_lvl, \'sp_ware\' as tablename FROM sp_ware w INNER JOIN sp_item i ON(i.id=w.item) WHERE w.user = '.$userid.' AND tausch_lvl <= '.$arenalvl['level'].' GROUP BY i.id
					union
					SELECT i.id, i.item AS name, count(i.id) AS anzahl, tausch_anzahl, tausch_lvl, \'wochen_ware\' as tablename FROM wochen_ware w INNER JOIN wochen_markt i ON(i.id=w.item) WHERE w.user = '.$userid.' AND tausch_lvl <= '.$arenalvl['level'].' GROUP BY (i.id)';

	$qry = db_query($query);
	// Damit waeren alle noetigen Datenbankaufrufe erledigt!
	while($row = mysqli_fetch_assoc($qry)){
		$result[$index++] = '<option value=\''.$row['id'].','.$row['tablename'].'\'>'.$row['name'].' | Anzahl:'.$row['anzahl'].' (max: '.$row['tausch_anzahl'].')</option>';
	}
	return $result;
}

function getTradeConditions($table, $item_id) {
	$row = null;
	switch ($table) {
		case 'ware':
			$row = mysqli_fetch_array(db_query("SELECT tausch_lvl, tausch_anzahl, name FROM item WHERE id = $item_id;"));
			break;
		case 'wochen_ware':
			$row = mysqli_fetch_array(db_query("SELECT tausch_lvl, tausch_anzahl, item FROM wochen_markt WHERE id = $item_id;"));
			break;
		case 'sp_ware':
			$row = mysqli_fetch_array(db_query("SELECT tausch_lvl, tausch_anzahl, name FROM sp_item WHERE id = $item_id;"));
			break;
	}
	return $row;
}

function getUserTransaction($user_id) {
	$row = mysqli_fetch_array(db_query('SELECT zeit, NOW() as zeit2 FROM transaktionen WHERE verkaeufer = '.$user_id.' ORDER BY zeit DESC LIMIT 1;'));
	$day_old = $row['zeit'];
	if($day_old != null) {
		$day_old = substr($day_old, 8, 2);
	} else {
		return false;
	}
	$day_now = $row['zeit2'];
	if($day_now != null) {
		$day_now = substr($day_now, 8, 2);
	} else {
		return false;
	}

	if($day_now == $day_old) {
		return true;
	} else {
		return false;
	}
}

function setUserTransaction($user_id, $trade_user_id, $item, $anzahl=1, $betrag=0) {
	$qry = 'INSERT INTO transaktionen(kaeufer, verkaeufer, item, anzahl, betrag, zeit) VALUES ('.$trade_user_id.','.$user_id.',\''.$item.'\','.$anzahl.','.$betrag.',NOW())';
	db_query($qry);
}

$user = $user_ida;

if ($charm == 1) {
	$item_info1 = mysqli_fetch_array(db_query("SELECT item_id, id, user FROM ware WHERE id='$item_id1' LIMIT 1"));
	$item_info3 = mysqli_fetch_array(db_query("SELECT preis, anzahl, hp, mp, starke, verteidigung, speed, s_type, rassen_type FROM item WHERE id='$item_info1[item_id]' LIMIT 1"));
	$char_id1 = getChar($char_id2);

	if ($item_info1['user'] != $user['id']) {
		displayErrorMessage(NULL,'Dieses Item geh&ouml;rt nicht dir', displayHistoryBackLink());
		exit;
	}

	if ($char_id1['besitzer'] != $user['id']) {
		displayErrorMessage(NULL,'Dieser Charakter geh&ouml;rt nicht dir!', displayHistoryBackLink());
		exit;
	}
	if ($item_info3['rassen_type'] != $char_id1['rasse_a']['type'] AND $item_info3['rassen_type'] != NULL)  {
		displayErrorMessage(NULL,'Dein Charakter kann dieses Item nicht benutzen!', displayHistoryBackLink());
		exit;
	}
	if ($item_info3['s_type'] != "Trank") {
		displayErrorMessage(NULL,'Fehler dieses Item ist kein Trank!', displayHistoryBackLink());
		exit;
	}

	$hp1 = explode(",", $char_id1['hp']);
	$mp1 = explode(",", $char_id1['mp']);

	$st1 = explode(",", $item_info3['starke']);

	$hp2 = explode(",", $item_info3['hp']);
	$mp2 = explode(",", $item_info3['mp']);

	$ver1 = explode(",", $item_info3['verteidigung']);
	$speed1 = explode(",", $item_info3['speed']);
	$new_hp2 = $hp1[1] + $hp2[1];
	$new_mp2 = $mp1[1] + $mp2[1];
	$new_hp = $hp1[0] + $hp2[0];
	$new_mp = $mp1[0] + $mp2[0];
	$new_starke = $st1[0] + $char_id1['starke'];
	$new_ver = $ver1[0] + $char_id1['verteidigung'];
	$new_speed = $speed1[0] + $char_id1['speed'];

	if ($new_hp > $hp1[1]) {
		$new_hp = $hp1[1];
	}
	if ($new_mp > $mp1[1]) {
		$new_mp = $mp1[1];
	}

	db_query("UPDATE chars SET starke='$new_starke', verteidigung='$new_ver', speed='$new_speed', hp='$new_hp,$new_hp2', mp='$new_mp,$new_mp2' WHERE id='$char_id2' LIMIT 1");
	db_query("DELETE FROM ware WHERE id='$item_info1[id]' LIMIT 1");

	displayErrorMessage(NULL,'Trank erfolgreich Benutzt', '<a href="index.php?as=item">weiter...</a>');
	exit;
}

if ($charm == 2) {
	if (!$ak_no) {
		displayErrorMessage(NULL,'Wollen sie das item wirklich Verkaufen? PS: Sie bekommen nur 50%', displayHistoryBackLink().' | <a href="index.php?as=item&charm=2&item_id='.$item_id.'&ak_no=1">Ja</a>');
		exit;
	}

	$item_info = mysqli_fetch_array(db_query("SELECT item_id, id, user, ru_mal FROM ware WHERE id='$item_id' LIMIT 1"));
	$item_info2 = mysqli_fetch_array(db_query("SELECT preis, anzahl FROM item WHERE id='$item_info[item_id]' LIMIT 1"));

	if ($item_info['user'] != $user_ida['id']) {
		displayErrorMessage(NULL,'Dieses Item geh&ouml;rt nicht dir', displayHistoryBackLink());
		exit;
	}
	if ($item_info['ru_mal'] == 0) {
		$new_geld = $user['geld'] + ($item_info2['preis'] / 2);
	} else {
		$new_geld = $user['geld'] + (($item_info2['preis'] / 2) * $item_info['ru_mal']);
	}
	$new_zahl = $item_info2['anzahl'] + 1;

	$sql = "UPDATE user SET geld='$new_geld' WHERE id='$user[id]'";
	//	echo $sql.'<br>';
	db_query($sql);
	db_query("UPDATE item SET anzahl='$new_zahl' WHERE id='$item_info[item_id]'");
	db_query("DELETE FROM ware WHERE id='$item_info[id]'");
	displayErrorMessage(NULL,'Item erfolgreich verkauft', '<a href="index.php?as=item">weiter...</a>');
	exit;
}

if($charm == 3) {
	if(!getUserTransaction($user['id'])) {
		if($tausch_user == null) {
			displayErrorMessage(NULL,'Kein Namen für den User angegeben!', displayHistoryBackLink());
			exit;
		} else {
			$row = mysqli_fetch_array(db_query("SELECT id FROM user WHERE nickname='$tausch_user' LIMIT 1"));
			$tausch_user_id = $row['id'];
			if($tausch_user_id == null) {
				displayErrorMessage(NULL,'Unbekannter User!', displayHistoryBackLink());
				exit;
			}
			if($tausch_user_id == $user['id']) {
				displayErrorMessage(NULL,'Warum an sich selbst was schenken?', displayHistoryBackLink());
				exit;
			}
		}

		$tausch_item = explode(",", $tausch_item);
		if($tausch_geld != null xor $tausch_item[0] >= 0) {
			if($tausch_geld != null) {
				$row = mysqli_fetch_array(db_query("SELECT level FROM arena WHERE besitzer = ".$user['id'].";"));
				$arenalvl = $row['level'];
				$tausch_geld = round($tausch_geld);

				if($tausch_geld <= ($arenalvl * 10000) && $tausch_geld > 0) {
					if($tausch_geld <= getRelevantMoney($user['id'])) {
						$qry = 'UPDATE user SET geld = geld - '.$tausch_geld.' WHERE id = '.$user['id'].';';
						db_query($qry);
						$qry = 'UPDATE user SET geld = geld + '.$tausch_geld.' WHERE id = '.$tausch_user_id.';';
						db_query($qry);
						sendMessage($user['nickname'], $tausch_user_id, 'Geschenk', 'Der Spieler '.$user['nickname'].' hat dir '.$tausch_geld.' geschenkt!');
						setUserTransaction($user['id'], $tausch_user_id, 'Geld', 0, $tausch_geld);

						displayErrorMessage(NULL, 'Die Summe von '.$tausch_geld.'¥ wurde erfolgreich dem Spieler '.$tausch_user.' geschenkt!', '<a href="index.php?as=item">weiter...</a>');
						exit;
					} else {
						displayErrorMessage(NULL,'Du besitzt garnicht soviel Geld!', displayHistoryBackLink());
						exit;
					}
				} else {
					displayErrorMessage(NULL,'Die Höhe des Geldbetrags darf nicht verschenkt werden! (Max. Arenalevel * 10000)', displayHistoryBackLink());
					exit;
				}
			}

			if($tausch_item[0] >= 0) {
				if($tausch_anzahl != null) {
					$tausch_anzahl = round($tausch_anzahl);
					$row = getTradeConditions($tausch_item[1], $tausch_item[0]);
					if($tausch_anzahl <= $row['tausch_anzahl'] && $tausch_anzahl > 0) {
						if($tausch_item[1] == 'ware') {
							$qry = 'UPDATE ware SET user = '.$tausch_user_id.' WHERE item_id = '.$tausch_item[0].' AND user = '.$user['id'].' LIMIT '.$tausch_anzahl.';';
							db_query($qry);
						} else {
							$qry = 'UPDATE '.$tausch_item[1].' SET user = '.$tausch_user_id.' WHERE item = '.$tausch_item[0].' AND user = '.$user['id'].' LIMIT '.$tausch_anzahl.';';
							db_query($qry);
						}
						sendMessage($user['nickname'], $tausch_user_id, 'Geschenk', 'Der Spieler '.$user['nickname'].' hat dir das Item '.$row['name'].$row['item'].' geschenkt!');
						setUserTransaction($user['id'], $tausch_user_id, $row['item'].$row['name'], $tausch_anzahl);

						displayErrorMessage(NULL, 'Item erfolgreich verschenkt!', '<a href="index.php?as=item">weiter...</a>');
						exit;
					} else {
						displayErrorMessage(NULL,'Keine gültige Eingabe bei der Anzahl!', displayHistoryBackLink());
						exit;
					}
				} else {
					displayErrorMessage(NULL,'Keine gültige Eingabe bei der Anzahl!', displayHistoryBackLink());
					exit;
				}
			}

		} else {
			displayErrorMessage(NULL,'Es kann entweder nur Geld oder ein Item verschenkt werden.', displayHistoryBackLink());
			exit;
		}
	} else {
		displayErrorMessage(NULL,'Limit für Verschenken schon erreicht!', displayHistoryBackLink());
		exit;
	}
}

if ($charm == 6) {
	$item_info = mysqli_fetch_array(db_query("SELECT w.user, i.item, i.starke, i.ver, i.speed, i.ausdauer, i.hp, i.mp, i.glueck  FROM wochen_ware w LEFT JOIN wochen_markt i ON(i.id=w.item) WHERE w.id='$sp_item' LIMIT 1"));
	$char_id1 = getChar($char_id2);

	if ($item_info['user'] != $user['id']) {
		displayErrorMessage(NULL,'Dieses Item geh&ouml;rt nicht dir', displayHistoryBackLink());
		exit;
	}

	if ($char_id1['besitzer'] != $user['id']) {
		displayErrorMessage(NULL,'Dieser Charakter geh&ouml;rt nicht dir!', displayHistoryBackLink());
		exit;
	}

	if ($char_id1['status'] != 'Frei') {
		displayErrorMessage(NULL,'Dieser Charakter ist nicht frei!', displayHistoryBackLink());
		exit;
	}

	if ($char_id1['type'] != 'Onepiece') {
		displayErrorMessage(NULL,'Dieser Charakter ist kein One Piece char!', displayHistoryBackLink());
		exit;
	}
	
	if ($char_id1['frucht'] != NULL) {
		displayErrorMessage(NULL,'Dieser Charakter hat schon eine Teufelsfrucht gegessen!', displayHistoryBackLink());
		exit;
	}	

	$hp1 = explode(",", $char_id1['hp']);
	$mp1 = explode(",", $char_id1['mp']);

	$new_hp2 = $hp1[1] + $item_info['hp'];
	$new_mp2 = $mp1[1] + $item_info['mp'];

	$new_starke = $item_info['starke'] + $char_id1['starke'];
	$new_ver = $item_info['ver'] + $char_id1['verteidigung'];
	$new_speed = $item_info['speed'] + $char_id1['speed'];
	$new_ausdauer = $item_info['ausdauer'] + $char_id1['ausdauer'];
	$new_glueck = $item_info['glueck'] + $char_id1['glueck'];

	db_query("UPDATE chars SET frucht='$item_info[item]', ausdauer='$new_ausdauer', glueck='$new_glueck', starke='$new_starke', verteidigung='$new_ver', speed='$new_speed', hp='$hp1[0],$new_hp2', mp='$mp1[0],$new_mp2' WHERE id='$char_id2' LIMIT 1");
	db_query("DELETE FROM wochen_ware WHERE id='$sp_item' LIMIT 1");

	displayErrorMessage(NULL,$item_info['item'].' erfolgreich benutzt', '<a href="index.php?as=item">weiter...</a>');
	exit;
}

$chars = getCharsOfUser($user_ida['id']);
$sql = "SELECT item_id, count(item_id) as anzahl, user, id, ru_mal FROM ware WHERE user='$user[id]' group by item_id";
//echo $sql;
$item2 = db_query($sql);
$item1 = db_query("SELECT item_id, id, ru_mal FROM ware WHERE user='$user[id]' group by item_id");
?>

<div align="center">
	<center>
		<table border="0" cellpadding="0" cellspacing="0"
			style="border-collapse: collapse" bordercolor="#111111" width="100%"
			height="1">
			<tr>
				<th align="center">Item nutzen</th>
			</tr>
			<tr>
				<td width="488" height="50" align="center">
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
						<input type="hidden" name="as" value="item"> <input type="hidden"
							name="charm" value="1"> <select id="input" name="char_id2"
							onchange="disablebutton()">
							<option value="-1">Charakter ausw&auml;hlen</option>
							<?php
							foreach($chars as $row){
								$hp1 = explode(",", $row['hp']);
								$mp1 = explode(",", $row['mp']);
								echo '<option value="'.$row['id'].'">'.$row['name'].' (HP: '.$hp1[0].' / '.$hp1[1].') (MP: '.$mp1[0].' / '.$mp1[1].') (Type: '.$row['type'].')</option>';
							}
							?>
						</select> <br> <br> <select id="input" name="item_id1">
						<?php
						while ($row = mysqli_fetch_array($item2)) {
							$item_name = mysqli_fetch_assoc(db_query('SELECT hp, mp, name, s_type, id FROM item WHERE id='.$row['item_id']));
							if ($item_name['s_type'] == "Trank") {
								echo '<option value="'.$row['id'].'">'.$item_name['name'].' ('.$row['anzahl'].' mal)';
							}
						}
						?>
						</select> <br> <br> <input id="input" type=submit disabled
						<?php echo $disabled; ?> name='submit' value='Benutzen'>
					</form>
				</td>
			</tr>
			<tr>
				<td><hr id="hrc"></td>
			</tr>
			<tr>
				<th>Spezialitems nutzen</th>
			</tr>
			<tr>
				<td width="488" height="50" align="center"><?php
				$sp_items = array(1,2,11,404,415);
				$sql = 'SELECT si.name, si.id, count(sw.item) as anzahl FROM sp_item si LEFT JOIN (Select item from sp_ware where user = '.$user_ida['id'].') sw ON sw.item = si.id WHERE si.id IN ('.join(',',$sp_items).') GROUP by si.id';
				//				echo $sql.'<br>';
				$qry = db_query($sql);
				while($row = mysqli_fetch_assoc($qry)){
					// 7 DBs, 1 Potara und 1 Bohne gibts derzeit
					if($row['id'] == 11 && $row['anzahl'] >= 7){
						echo '<a href="index.php?as=dragonballs">'.$row['name'].' einsetzen ('.$row['anzahl'].')</a><br>';
					} else if(($row['id'] == 1 || $row['id'] == 2) && $row['anzahl'] > 0){
						// Potaras und Bohnen
						echo '<a href="index.php?as=sp_item&item_id='.$row['id'].'">'.$row['name'].' einsetzen ('.$row['anzahl'].')</a><br>';
					} else if($row['id'] == 404 || $row['id'] == 415){
						if($row['anzahl'] > 0){
							echo '<a href="index.php?as=sp_item&item_id='.$row['id'].'">'.$row['name'].' öffnen ('.$row['anzahl'].')</a><br>';
						}
					} else{
						echo $row['name'].' ('.$row['anzahl'].')<br>'."\n";
					}
				}
				?>
				</td>
			</tr>
			<tr>
				<td><hr id="hrc"></td>
			</tr>
			<tr>
				<th>Teufelsfrucht benutzen</th>
			</tr>
			<tr>
				<td width="491" height="25" align="center">
					<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
						<input type="hidden" name="as" value="item"> <input type="hidden"
							name="charm" value="6"> <select id="input" name="char_id2">
							<option value="">Charakter ausw&auml;hlen</option>
							<?php
							// Speziell!!
							#$char4 = db_query('SELECT id, name FROM chars WHERE besitzer='.$user_ida['id'].' AND type=\'Onepiece\' AND frucht is NULL');
							$all_chars = getCharsOfUser($user_ida['id']);
							foreach($all_chars as $row4) {
								if($row4['type'] == 'Onepiece' && $row4['frucht'] == NULL) {
									echo '<option value="'.$row4['id'].'">'.$row4['name'].'</option>';
								}
							}
							?>
						</select> <br> <br> <select id="input" name="sp_item">
						<?php
						$sp_item = db_query("SELECT w.id, i.item FROM wochen_ware w LEFT JOIN wochen_markt i ON(w.item=i.id) WHERE w.user='$user_ida[id]'");
						while ($row3 = mysqli_fetch_array($sp_item)) {
							echo "<option value='$row3[id]'>$row3[item]";
						}
						?>
						</select> <br> <br> <input id="input" type=submit
							value="Teufels Frucht Benutzen">
					</form>
				</td>
			</tr>
			<tr>
				<td><hr id="hrc"></td>
			</tr>
			<tr>
				<th align="center">Item verkaufen</th>
			</tr>
			<tr>
				<td width="491" height="50" align="center">
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
						<input type="hidden" name="as" value="item"> <input type="hidden"
							name="charm" value="2"> <select id="input" name=item_id>
							<option value="0">Verkaufen!</option>
							<?php
							while ($row = mysqli_fetch_assoc($item1)) {
								$item_name = mysqli_fetch_assoc(db_query('SELECT * FROM item WHERE id='.$row['item_id']));
								if($item_name['s_type'] != 'Trank'){
									echo '<option value="'.$row['id'].'">'.$item_name['name'].' (Typ: '.$item_name['s_type'].', Level: '.$item_name['level'].')</option>';
								} else{
									echo '<option value="'.$row['id'].'">'.$item_name['name'].'</option>';
								}
							}
							?>
						</select> <br> <br> <input id="input" type="submit"
							value="Verkaufen">
					</form>
				</td>
			</tr>
			<tr>
				<td><hr id="hrc"></td>
			</tr>
			<tr>
				<th align="center">Item verschenken</th>
			</tr>
			<tr>
				<td width="491" align="center">
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
						<input type="hidden" name="as" value="item"> <input type="hidden"
							name="charm" value="3">
						<table border="0" width="70%" align="center">
							<tr>
								<td align="center">Item:</td>
								<td align="left"><select id="input" name=item_id>
										<option value="-1">Auswahl...</option>
										<?php
										$user_items = getTradeItems($user['id']);
										for($i = 0; $i < count($user_items); $i++) {
											echo $user_items[$i];
										}
										?>
								</select>
								</td>
							</tr>
							<tr>
								<td align="center">Anzahl:</td>
								<td align="left"><input class="input" name="anzahl" value="">
								</td>
							</tr>
							<tr>
								<td align="center">Geld:</td>
								<td align="left"><input class="input" name="geld" value=""></td>
							</tr>
							<tr>
								<td align="center">User:</td>
								<td align="left"><input class="input" name="user" value=""></td>
							</tr>
							<tr>
								<td colspan="2" align="center"><input id="input" type="submit"
									value="Verschenken">
								</td>
							</tr>
							<tr>
								<td><?php //print_r($user);?></td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
		</table>
	</center>
</div>
