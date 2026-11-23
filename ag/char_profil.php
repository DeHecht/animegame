<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'//include/config.inc.php');
include_once(ROOT_PATH.'//include/designfunctions.inc.php');
include_once(ROOT_PATH.'//include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'//include/parse.inc.php');
include_once(ROOT_PATH.'//include/char_profil.inc.php');	//Allgemeine Funktionen für die Char darstellung
include_once(ROOT_PATH.'//include/defines.inc.php');
include_once(ROOT_PATH.'//include/attackenset.inc.php');

defineIfNotDefined('KAMPF_RUNDEN', 10);

/* Diese Datei ist etwas komisch, darum die Abweichende SQL-Injection methode */

$char_id = validateUnsignedInteger($_GET['char_id'], null);

function getExcludeIds($userId, $excludeCharId) {
	$ids = array();
	$qry = db_query('SELECT kampf_item from chars WHERE besitzer = '.$userId.' AND id != '.$excludeCharId);
	while ($row = mysqli_fetch_row($qry)) {
		foreach (explode(',', $row[0]) as $id) {
			if(is_numeric($id) && $id > 0) {
				$ids[] = $id;
			}
		}
	}
	return $ids;
}


function stelledar($char_id,$user_ida,$name) {
	//Alle Chardaten laden
	//TODO: chardaten gefällt mir absolut nicht! Das muss man anders regeln =/
	chardaten($char_id,$name,$char_1,$hp1,$mp1,$exp,$fehler);
	if ($fehler == NULL) {
		$buffedstats = getCharBuffs($char_1['kampf_item']);
		//Alle Attacken laden
		$learned_atts = getCharAttacks($char_id);

		//Attackensets laden
		$clanfight_attset = getAttackSet($char_id, CLANKAMPF);
		if(count($clanfight_attset) == 0) {
			createAttackSet($char_id, CLANKAMPF);
			for($i = 1; $i < KAMPF_RUNDEN+1;$i++) {
				$clanfight_attset[$i] = -1;
			}
		}
		$normfight_attset = getAttackSet($char_id, NORMALKAMPF);
		if(count($normfight_attset) == 0) {
			createAttackSet($char_id, NORMALKAMPF);
			for($i = 1; $i < KAMPF_RUNDEN+1;$i++) {
				$normfight_attset[$i] = -1;
			}
		}

		//TODO:prüfen ob man nicht auch den name aus $learned_atts holen kann
		$clanfight_attset_name = getAttackNames($clanfight_attset);
		$normfight_attset_name = getAttackNames($normfight_attset);

		//Rüstung laden
		$e_schuhe = db_query("SELECT i.name, w.id, i.level FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE (i.rassen_type=".$char_1['rasse_a']['type']." OR i.rassen_type is NULL) AND w.user='$user_ida[id]' AND w.ru_mal='1' AND i.s_type='Schuhe' AND i.level <='$char_1[level]' AND 0 = (SELECT count(c.id) from chars c WHERE c.besitzer = w.user AND c.id != '$char_1[id]' AND find_in_set(w.id, kampf_item)) group by i.name order by i.level desc");
		$e_schwert = db_query("SELECT i.name, w.id, i.level FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE (i.rassen_type=".$char_1['rasse_a']['type']." OR i.rassen_type is NULL) AND w.user='$user_ida[id]' AND w.ru_mal='1' AND i.s_type='Schwert' AND i.level <='$char_1[level]' AND 0 = (SELECT count(c.id) from chars c WHERE c.besitzer = w.user AND c.id != '$char_1[id]' AND find_in_set(w.id, kampf_item)) group by i.name order by i.level desc");
		$e_rustung = db_query("SELECT i.name, w.id, i.level FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE (i.rassen_type=".$char_1['rasse_a']['type']." OR i.rassen_type is NULL) AND w.user='$user_ida[id]' AND w.ru_mal='1' AND i.s_type='Ruestung' AND i.level <='$char_1[level]' AND 0 = (SELECT count(c.id) from chars c WHERE c.besitzer = w.user AND c.id != '$char_1[id]' AND find_in_set(w.id, kampf_item)) group by i.name order by i.level desc");
		$e_schild = db_query("SELECT i.name, w.id, i.level FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE (i.rassen_type=".$char_1['rasse_a']['type']." OR i.rassen_type is NULL) AND w.user='$user_ida[id]' AND w.ru_mal='1' AND i.s_type='Schild' AND i.level <='$char_1[level]' AND 0 = (SELECT count(c.id) from chars c WHERE c.besitzer = w.user AND c.id != '$char_1[id]' AND find_in_set(w.id, kampf_item)) group by i.name order by i.level desc");
		$e_helm = db_query("SELECT i.name, w.id, i.level FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE (i.rassen_type=".$char_1['rasse_a']['type']." OR i.rassen_type is NULL) AND w.user='$user_ida[id]' AND w.ru_mal='1' AND i.s_type='Helm' AND i.level <='$char_1[level]' AND 0 = (SELECT count(c.id) from chars c WHERE c.besitzer = w.user AND c.id != '$char_1[id]' AND find_in_set(w.id, kampf_item)) group by i.name order by i.level desc");
		$checked = explode(',',$char_1['kampf_item']);
		$excludeIds = getExcludeIds($user_ida['id'], $char_id);
		?>
<body>
	<form action="index.php?as=char_profil&char_id=<?php echo $char_id; ?>"
		method="post">
		<input type="hidden" name="as" value="char_profil"> <input
			type="hidden" name="char_id" value="<?php echo $char_id; ?>" /> <input
			type="hidden" name="submit" value="1" />

		<table border="0" cellpadding="0" cellspacing="0"
			style="border-collapse: collapse;" bordercolor="#111111" width="100%"
			height="25">
			<tr>
				<th width="590" height="25" colspan="2" align="center"><font
					color="#0000FF">Charakter&uuml;bersicht</font>
				</th>
			</tr>
			<tr>
				<td width="192" height="25"><b>Name</b>
				</td>
				<td width="392" height="25">&nbsp;<?php echo $char_1['name']; ?>
				</td>
			</tr>
			<tr>
				<td width="192" height="25"><b>Type</b>
				</td>
				<td width="392" height="25">&nbsp;<?php echo $char_1['type']; ?>
				</td>
			</tr>
			<tr>
				<td width="192" height="25"><b>Rasse</b>
				</td>
				<td width="392" height="25">&nbsp;<?php echo $char_1['rasse']; ?>
				</td>
			</tr>
			<?php
			if($char_1['fusion'] == 'ja') {
				?>
			<tr>
				<td width="192" height="25"><b>Fusion Rasse</b>
				</td>
				<td width="392" height="25">&nbsp;<?php echo $char_1['fusion_rasse']; ?>
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td width="192" height="25"><b>St&auml;rke</b>
				</td>
				<td width="392" height="25">&nbsp;<?php echo $buffedstats['starke']==0?$char_1['starke']:$char_1['starke'].' (+'.$buffedstats['starke'].' R&uuml;stungsbonus)'; ?>
				</td>
			</tr>
			<tr>
				<td width="192" height="25"><b>Verteidigung</b>
				</td>
				<td width="392" height="25">&nbsp;<?php echo $buffedstats['verteidigung']==0?$char_1['verteidigung']:$char_1['verteidigung'].' (+'.$buffedstats['verteidigung'].' R&uuml;stungsbonus)'; ?>
				</td>
			</tr>
			<tr>
				<td width="192" height="25"><b>Geschwindigkeit</b>
				</td>
				<td width="392" height="25">&nbsp;<?php echo $buffedstats['speed']==0?$char_1['speed']:$char_1['speed'].' (+'.$buffedstats['speed'].' R&uuml;stungsbonus)'; ?>
				</td>
			</tr>
			<tr>
				<td width="192" height="25"><b>Ausdauer</b>
				</td>
				<td width="392" height="25">&nbsp;<?php echo $buffedstats['ausdauer']==0?$char_1['ausdauer']:$char_1['ausdauer'].' (+'.$buffedstats['ausdauer'].' R&uuml;stungsbonus)'; ?>
				</td>
			</tr>
			<tr>
				<td width="192" height="25"><b>Gl&uuml;ck</b>
				</td>
				<td width="392" height="25">&nbsp;<?php echo $buffedstats['glueck']==0?$char_1['glueck']:$char_1['glueck'].' (+'.$buffedstats['glueck'].' R&uuml;stungsbonus)'; ?>
				</td>
			</tr>
			<tr>
				<td width="192" height="25"><b>HP</b>
				</td>
				<td width="392" height="25">&nbsp;<?php echo ($buffedstats['hp']==0)?($hp1[0].' / '.$hp1[1]):($hp1[0].' / '.$hp1[1].' (+'.$buffedstats['hp'].' R&uuml;stungsbonus)'); ?>
				</td>
			</tr>
			<tr>
				<td width="192" height="25"><b>MP</b>
				</td>
				<td width="392" height="25">&nbsp;<?php echo ($buffedstats['mp']==0)?($mp1[0].' / '.$mp1[1]):($mp1[0].' / '.$mp1[1].' (+'.$buffedstats['mp'].' R&uuml;stungsbonus)'); ?>
				</td>
			</tr>
			<tr>
				<td width="192" height="25"><b>Level</b>
				</td>
				<td width="392" height="25">&nbsp;<?php echo $char_1['level']; ?>
				</td>
			</tr>
			<tr>
				<td width="192" height="25"><b>Training Points</b>
				</td>
				<td width="392" height="25">&nbsp;<?php echo $char_1['training_points']; ?>
				</td>
			</tr>
			<tr>
				<td width="192" height="25"><b>Exp</b>
				</td>
				<td width="392" height="25">&nbsp;&nbsp;<?php echo ''.$exp[0].' / '.$exp[1].'' ?>
				</td>
			</tr>
			<tr>
				<td width="192" height="25"><b>Siege</b>
				</td>
				<td width="392" height="25">&nbsp;<?php echo $char_1['siege']; ?>
				</td>
			</tr>
			<tr>
				<td width="192" height="25"><b>Niederlage</b>
				</td>
				<td width="392" height="25">&nbsp;<?php echo $char_1['niederlagen']; ?>
				</td>
			</tr>
			<tr>
				<td width="192" height="25"><b>Liga Siege</b>
				</td>
				<td width="392" height="25">&nbsp;<?php echo $char_1['liga_siege']; ?>
				</td>
			</tr>
			<tr>
				<td width="192" height="25"><b>Liga Niederlage</b>
				</td>
				<td width="392" height="25">&nbsp;<?php echo $char_1['liga_niederlagen']; ?>
				</td>
			</tr>
			<tr>
				<td width="192" height="25"><b>Status</b>
				</td>
				<td width="392" height="25">&nbsp;<?php echo $char_1['status']; ?>
				</td>
			</tr>
			<tr>
				<td width="192" height="25"><b>Bild <?php echo ''.$pic_g31.' x '.$pic_g31.''; ?>
				</b>
				</td>
				<td width="392" height="25">&nbsp;<?php echo '<input name="bild11" id="input" size="40" value="'.$char_1['bild'].'">'; ?>
				</td>
			</tr>

			<!-- Übersicht über die Attackensets -->
			<tr>
				<th width="590" height="25" colspan="2" align="center"><font
					color="#0000FF">Kampfeinstellungen</font>
				</th>
			</tr>
			<tr>
				<th></th>
				<th width="584" height="25" align="center">Normale K&auml;mpfe</th>
				<th width="584" height="25" align="center">Schatzsuche</th>
			</tr>
			<?php
			//alle informationen zu den erlernten attacken auslesen
			for ($i = 1; $i < MAX_ROUNDS + 1; $i++) {
				?>
			<tr>
				<td width="10%" height="25"><b>Runde <?php echo $i; ?> </b></td>
				<td width="20%" height="25" align="center"><select id="input"
					name="<?php echo 'attset_nf['.$i.']'; ?>">
					<?php
					echo '<option value="'.$normfight_attset[$i].'">'.$normfight_attset_name[$normfight_attset[$i]].'</option>';
					foreach($learned_atts as $atts) {
						echo '<option value="'.$atts['id'].'">'.$atts['name'].'</option>';
					}
					?>
				</select>
				</td>
				<td width="39%" height="25" align="center"><select id="input"
					name="<?php echo 'attset_cf['.$i.']'; ?>">
					<?php
					echo '<option value="'.$clanfight_attset[$i].'">'.$clanfight_attset_name[$clanfight_attset[$i]].'</option>';
					foreach($learned_atts as $atts) {
						echo '<option value="'.$atts['id'].'">'.$atts['name'].'</option>';
					}
					?>
				</select>
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<th height="25" width="580" colspan="2"><font color="#0000FF">Ausrüstungs&uuml;bersicht</font>
				</th>
			</tr>
			<tr>
				<td height="25" width="192">&nbsp;<b>Helm:</b></td>
				<td height="25" width="392">&nbsp; <select id="input" name="helm_e">
						<option value="0">Nix</option>
						<?php
						while($helm_e_w = mysqli_fetch_array($e_helm)) {
							if(array_search($helm_e_w['id'], $excludeIds) !== FALSE) {
								continue;
							}
							if($checked[0] == $helm_e_w['id'] && !$checked_helm) { $checked_helm = 'selected'; }
							else { $checked_helm=''; }
							echo '<option value="'.$helm_e_w['id'].'" '.$checked_helm.'>'.$helm_e_w['name'].' (Level: '.$helm_e_w['level'].')</option>';
						}
						?>
				</select>
				</td>
			</tr>
			<tr>
				<td height="25" width="192">&nbsp;<b>R&uuml;stung:</b></td>
				<td height="25" width="392">&nbsp; <select id="input"
					name="rustung_e">
						<option value="0">Nix</option>
						<?php
						while($rustung_e_w = mysqli_fetch_array($e_rustung)) {
							if(array_search($rustung_e_w['id'], $excludeIds) !== FALSE) {
								continue;
							}
							if($checked[1] == $rustung_e_w['id'] && !$checked_rustung) { $checked_rustung = 'selected'; }
							else { $checked_rustung=''; }
							echo '<option value="'.$rustung_e_w['id'].'" '.$checked_rustung.'>'.$rustung_e_w['name'].' (Level: '.$rustung_e_w['level'].')</option>';
						}
						?>
				</select>
				</td>
			</tr>
			<tr>
				<td height="25" width="192">&nbsp;<b>Schild:</b></td>
				<td height="25" width="392">&nbsp; <select id="input"
					name="schild_e">
						<option value="0">Nix</option>
						<?php
						while($schild_e_w = mysqli_fetch_array($e_schild)) {
							if(array_search($schild_e_w['id'], $excludeIds) !== FALSE) {
								continue;
							}
							if($checked[2] == $schild_e_w['id'] && !$checked_schild) { $checked_schild = 'selected'; }
							else { $checked_schild=''; }
							echo '<option value="'.$schild_e_w['id'].'" '.$checked_schild.'>'.$schild_e_w['name'].' (Level: '.$schild_e_w['level'].')</option>';
						}
						?>
				</select>
				</td>
			</tr>
			<tr>
				<td height="25" width="192">&nbsp;<b>Schwert:</b></td>
				<td height="25" width="392">&nbsp; <select id="input"
					name="schwert_e">
						<option value="0">Nix</option>
						<?php
						while($schwert_e_w = mysqli_fetch_array($e_schwert)) {
							if(array_search($schwert_e_w['id'], $excludeIds) !== FALSE) {
								continue;
							}
							if($checked[3] == $schwert_e_w['id'] && !$checked_schwert) { $checked_schwert = 'selected'; }
							else { $checked_schwert=''; }
							echo '<option value="'.$schwert_e_w['id'].'" '.$checked_schwert.'>'.$schwert_e_w['name'].' (Level: '.$schwert_e_w['level'].')</option>';
						}
						?>
				</select>
				</td>
			</tr>
			<tr>
				<td height="25" width="192">&nbsp;<b>Schuhe:</b></td>
				<td height="25" width="392">&nbsp; <select id="input"
					name="schuhe_e">
						<option value="0">Nix</option>
						<?php
						while($schuhe_e_w = mysqli_fetch_array($e_schuhe)) {
							if(array_search($schuhe_e_w['id'], $excludeIds) !== FALSE) {
								continue;
							}
							if($checked[4] == $schuhe_e_w['id'] && !$checked_schuhe) { $checked_schuhe = 'selected'; }
							else { $checked_schuhe=''; }
							echo '<option value="'.$schuhe_e_w['id'].'" '.$checked_schuhe.'>'.$schuhe_e_w['name'].' (Level: '.$schuhe_e_w['level'].')</option>';
						}
						?>
				</select>
				</td>
			</tr>
			<tr>
				<td width="192" height="25"></td>
				<td width="392" height="25"><br /> <input name="submit2"
					type="submit" class="button" value="speichern" />
				</td>
			</tr>
		</table>
	</form>
	<?php
	} else {
		displayErrorMessage(NULL,$fehler, displayHistoryBackLink());
		exit;
	}
}

function eintragen($char_id,$name,$user_ida) {
	$char = getChar($char_id);
	if($char['besitzer'] != $user_ida['id']) {
		displayErrorMessage(NULL,'Dieser Charackter geh&ouml;rt nicht dir!', displayHistoryBackLink());
		exit;
	}

	if(!checkCharStatus($char_id, STATUS_FREI)) {
		displayErrorMessage(NULL,'Der Charakter ist zur Zeit nicht frei!', displayHistoryBackLink());
		exit;
	}

	//übermittelte Variablen auslesen und in Variable speichern
	$helm_e = validateUnsignedInteger($_POST['helm_e'], null);
	$rustung_e = validateUnsignedInteger($_POST['rustung_e'], null);
	$schild_e = validateUnsignedInteger($_POST['schild_e'], null);
	$schwert_e = validateUnsignedInteger($_POST['schwert_e'], null);
	$schuhe_e = validateUnsignedInteger($_POST['schuhe_e'], null);
	$bild11 = validateURL($_POST['bild11']);

	$attset_cf = array();
	$attset_nf = array();
	for ($i = 1; $i < MAX_ROUNDS + 1; $i++) {
		$attset_cf[$i] = validateUnsignedInteger($_POST['attset_cf'][$i], -1);
		$attset_nf[$i] = validateUnsignedInteger($_POST['attset_nf'][$i], -1);
	}

	//überprüfen, wie oft welche Attacke benutzt wird, sowie andere Abhängigkeiten
	$fehler[0] = validateAttackSet($attset_cf);
	$fehler[1] = validateAttackSet($attset_nf);

	//andere Sachen überprüfen
	if($bild11 != $_POST['bild11']){
		$fehler[2] = 'Die URL f&uuml;r das Bild ist ung&uuml;ltig!';
	}

	//Schauen ob ein Fehler vorhanden ist
	if ($fehler[0] == NULL && $fehler[1] == NULL && $fehler[2] == NULL) {
		//In DB einfügen (alter müll, vor allen was Attacken angeht!)
		$qry = 'UPDATE chars SET kampf_item=\''.$helm_e.','.$rustung_e.','.$schild_e.','.$schwert_e.','.$schuhe_e.'\', rustung =\''.$rustung.'\', '.
				'attacken=\''.implode(',', $attset_nf).'\', '.
				'attacken2=\''.implode(',', $attset_nf).'\', '.
				'bild=\''.$bild11.'\' WHERE id='.$char['id'].' LIMIT 1';
		db_query($qry);

		//neues updaten von Attacken
		updateAttackSet($char_id, CLANKAMPF, $attset_cf);
		updateAttackSet($char_id, NORMALKAMPF, $attset_nf);

		echo 'Profil erfolgreich ge&auml;ndert!!<br>';
		echo '<a href=index.php?as=char_profil&char_id='.$char_id.'><div align=center>weiter...</div></a>';
	} else {
			displayErrorMessage(NULL, $fehler[0].'<br>'.$fehler[1].'<br>'.$fehler[2], displayHistoryBackLink());
		return;
	}
}

if ($_POST['submit']==1) {
	eintragen($char_id,$name,$user_ida);
} elseif ($char_id) {
	stelledar($char_id,$user_ida,$name);
} else {
	echo 'Leider wurde die Seite nicht korrekt aufgerufen! Rufen Sie die Seite bitte korrekt auf';
}
?>
