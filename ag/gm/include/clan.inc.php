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
defineIfNotDefined("CLAN_CREATION_FEE", 20000);

function displayClanItems() {
	$raume_qry = db_query('SELECT * FROM clan_item order by tp');
	?>
<table>
	<tr>
		<th align="center" colspan="12"><h1>Clanr&auml;ume bearbeiten</h1></th>
	</tr>
	<tr>
		<td>Name</td>
		<td>St&auml;rke</td>
		<td>Verteidigung</td>
		<td>Gl&uuml;ck</td>
		<td>Ausdauer</td>
		<td>Geschwindigkeit</td>
		<td>Kaufpreis</td>
		<td>Miete</td>
		<td>Nutzungsgeb&uuml;hr</td>
		<td>TP</td>
		<td>Level</td>
		<td>bearbeiten</td>
	</tr>
	<tr>
		<td colspan=12><hr></td>
	</tr>
	<?php
	while($res = mysqli_fetch_assoc($raume_qry)){
		echo '<tr><td>'.$res['name'].'</td><td>'.$res['staerke'].'</td><td>'.$res['verteidigung'].'</td><td>'.$res['glueck'].'</td><td>'.$res['ausdauer'].'</td><td>'.$res['geschwindigkeit'].'</td><td>'.$res['preis'].'</td><td>'.$res['miete'].'</td><td>'.$res['nutzung'].'</td><td>'.$res['tp'].'</td><td>'.$res['req_level'].'</td><td><a href="'.$_SERVER['PHP_SELF'].'?choose=clan&action=editrooms&raumid='.$res['id'].'">edit</a> | <a href="'.$_SERVER['PHP_SELF'].'?choose=clan&action=deleterooms&raumid='.$res['id'].'">delete</a></td></tr>';
	}
	?>
	<tr>
		<td colspan=12><hr></td>
	</tr>
	<tr>
		<td align="center" colspan="12"><a
			href="<?php echo $_SERVER['PHP_SELF'].'?choose=clan&action=createrooms'; ?>">Raum
				erstellen</a>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="12"><a
			href="<?php echo $_SERVER['PHP_SELF'].'?choose=clan'; ?>">Zum
				Clanmenu</a>
		</td>
	</tr>
</table>
	<?php
}

function displayEditOrCreateClanRoom($action, $raumid, $raumname, $staerke, $verteidigung, $glueck, $ausdauer, $geschwindigkeit, $info, $level) {
	if($raumname !== NULL){ // Sprich es wurde was geaendert!
		// Berechne den TP Verbrauch
		$b38 = 1.3; // Parameter aus Exceltabelle
		$c38 = 0.3;
		$g3 = ($staerke!=0?1:0) + ($verteidigung!=0?1:0) + ($glueck!=0?1:0) + ($ausdauer!=0?1:0) + ($geschwindigkeit!=0?1:0);
		$summe_werte = ($staerke+$verteidigung+$glueck+$ausdauer+$geschwindigkeit);
		$tp = round($summe_werte * (pow($b38,$g3)-$c38));
		// Berechne Kaufpreis
		$b33 = 1.1; // Parameter aus Exceltabelle
		$c33 = 3000;
		$kaufpreis = round(pow($b33,$summe_werte) * $c33);
		// Berechne Miete
		$b42=0.5; $c42=2; $d42=4; $e42=2; $f42=1; $g42=5; $h42=6; $i42=1.9; $j42=100;
		$gewichtete_summe= $ausdauer*$b42+$staerke*$c42+$verteidigung*$d42+$geschwindigkeit*$e42+$glueck*$f42;
		$miete = floor(pow($i42,log($gewichtete_summe,$h42)*$g42)*$j42/7); // Durch sieben (weil 7 Chars pro User)
		// Berechne Nutzungsgebuehr
		$b46 = 100; $c46 = 0.69; $d46 = -1762;
		$nutz = floor(($miete*7*0.7)/log($gewichtete_summe, $b46)*$c46+$d46);

		// Und jetzt poliere die Preise optisch etwas auf
		$stepkp = round(pow(10,floor(max(log10($kaufpreis),2)))/4);
		$stepmp = round(pow(10,floor(max(log10($miete),2)))/4);
		$stepnp = round(pow(10,floor(max(log10($nutz),2)))/4);

		$kaufpreis = round($kaufpreis/$stepkp)*$stepkp;
		$miete = ceil($miete/$stepmp)*$stepmp;
		$nutz = ceil($nutz/$stepnp)*$stepnp;
		// So und nun kann weiter gemacht werden!
		if($action == 'editrooms'){
			logaction('Der Raum '.$raumname.' wurde editiert!');
			$sql ='Update clan_item set name = \''.$raumname.'\', staerke = '.$staerke.', verteidigung = '.$verteidigung.', ausdauer = '.$ausdauer.', geschwindigkeit = '.$geschwindigkeit.', glueck = '.$glueck.', preis = '.$kaufpreis.', miete = '.$miete.', nutzung = '.$nutz.', tp = '.$tp.', info = \''.$info.'\', req_level = '.$level.' where id = '.$raumid.'';
			$res = db_query($sql);
			if(!$res)
			echo $sql . '<br>';
		} else if($action == 'createrooms'){
			logaction('Der Raum '.$raumname.' wurde erstellt!');
			$sql = 'INSERT INTO clan_item(name, staerke, verteidigung, ausdauer, geschwindigkeit, glueck, preis, miete, nutzung, tp, info, req_level) values(\''.$raumname.'\', '.$staerke.', '.$verteidigung.', '.$ausdauer.', '.$geschwindigkeit.', '.$glueck.', '.$kaufpreis.', '.$miete.', '.$nutz.', '.$tp.', \''.$info.'\', '.$level.')';
			$res = db_query($sql);
			if(!$res)
			echo $sql . '<br>';
		}
	}
	if($action == 'editrooms'){
		$raum = mysqli_fetch_assoc(db_query('SELECT * FROM clan_item WHERE id = '.$raumid));
		$aLabel = 'editieren';
	} else{
		$aLabel = 'erstellen';
	}
	?>
<form action="" method="POST">
	<input name="choose" value="clan" type="hidden"></input> <input
		name="action" value="<?php echo $action; ?>" type="hidden"></input> <input
		name="raumid" value="<?php echo $raumid; ?>" type="hidden"></input>
	<table>
		<tr>
			<th align="center" colspan="2">Clanr&auml;ume <?php echo $aLabel; ?>
			</th>
		</tr>
		<tr>
			<td>Name</td>
			<td><input type="text" class="text" name="raumname"
				value="<?php echo $raum['name']; ?>" /></td>
		</tr>
		<tr>
			<td>St&auml;rke</td>
			<td><input type="text" class="text" name="staerke"
				value="<?php echo $raum['staerke']; ?>" /></td>
		</tr>
		<tr>
			<td>Verteidigung</td>
			<td><input type="text" class="text" name="verteidigung"
				value="<?php echo $raum['verteidigung']; ?>" /></td>
		</tr>
		<tr>
			<td>Gl&uuml;ck</td>
			<td><input type="text" class="text" name="glueck"
				value="<?php echo $raum['glueck']; ?>" /></td>
		</tr>
		<tr>
			<td>Ausdauer</td>
			<td><input type="text" class="text" name="ausdauer"
				value="<?php echo $raum['ausdauer']; ?>" /></td>
		</tr>
		<tr>
			<td>Geschwindigkeit</td>
			<td><input type="text" class="text" name="geschwindigkeit"
				value="<?php echo $raum['geschwindigkeit']; ?>" /></td>
		</tr>
		<tr>
			<td>Level</td>
			<td><input type="text" class="text" name="req_level"
				value="<?php echo $raum['req_level']; ?>" /></td>
		</tr>
		<tr>
			<td>Kaufpreis</td>
			<td><?php echo $raum['preis']; ?></td>
		</tr>
		<tr>
			<td>Miete</td>
			<td><?php echo $raum['miete']; ?></td>
		</tr>
		<tr>
			<td>Nutzungsgeb&uuml;hr</td>
			<td><?php echo $raum['nutzung']; ?></td>
		</tr>
		<tr>
			<td>Trainingspunkte</td>
			<td><?php echo $raum['tp']; ?></td>
		</tr>
		<tr>
			<td>Info</td>
			<?php
			echo '<td><textarea name="info" rows="10" cols="50">'.$raum['info'].'</textarea></td>';
			?>
		</tr>
		<tr>
			<td align="center" colspan="2"><input type="submit" /></td>
		</tr>
		<tr>
			<td align="center" colspan="2"><a
				href="<?php echo $_SERVER['PHP_SELF'].'?choose=clan&action=rooms'; ?>">Zur
					&Uuml;bersicht</a>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="2"><a
				href="<?php echo $_SERVER['PHP_SELF'].'?choose=clan'; ?>">Zum
					Clanmenu</a>
			</td>
		</tr>
	</table>
</form>
			<?php
}

function displayDeleteClanRoom($raumname, $raumid) {
	?>
<table>
<?php
if($raumname !== NULL){
	db_query('DELETE FROM clan_item WHERE id = '.$raumid);
	db_query('DELETE FROM clan_ware where item_id ='.$raumid);
	?>
	<tr>
		<td align="center" colspan="2">Der Raum "<?php echo $raumname; ?>"
			wurde gel&ouml;scht</td>
	</tr>
	<?php
} else{
	$res = mysqli_fetch_assoc(db_query('SELECT * FROM clan_item WHERE id = '.$raumid));
	?>
	<tr>
		<td align="center" colspan="2">Den Raum "<?php echo $res['name']; ?>"
			wirklich l&ouml;schen? <a
			href="<?php echo $_SERVER['PHP_SELF'].'?choose=clan&action=deleterooms&raumid='.$raumid.'&raumname='.$res['name']; ?>">L&ouml;schen
				best&auml;tigen!</a>
		</td>
	</tr>
	<?php
}
?>
	<tr>
		<td align="center" colspan="2"><a
			href="<?php echo $_SERVER['PHP_SELF'].'?choose=clan&action=rooms'; ?>">Zur
				&Uuml;bersicht</a>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="2"><a
			href="<?php echo $_SERVER['PHP_SELF'].'?choose=clan'; ?>">Zum
				Clanmenu</a>
		</td>
	</tr>
</table>
<?php
}

function displayClanFightOptions($data) {
	$colspan = 2;
	$desc_array = array('minimale Anzahl an Clanmitgliedern um kämpfen zu können', 'maximale Anzahl an Herausforderungen',
	'maximale Anzahl von Offlinecharakteren die angemeldet werden dürfen', 'maximalen Clankämpfe pro Tag', 'Zeit zwischen dem Herausfordern des selben Clans in Stunden', 'Kampfdauer pro Einzelkampf in Davy Back',
	 'Kampfdauer pro Einzelkampf in Survival', 'Modifikator G für die Clan-Exp Verteilungsformel', 'Anzahl CFs die ein Char nacheinander haben kann.');
	$define_array = array('MIN_MEMBERS', 'MAX_OPEN_REQUESTS', 'MAX_LEADER_ASSIGNED_CHARS', 'CLANFIGHTS_MAX_FIGHTS_PER_24H', 'CLANFIGHTS_RECHALLENGE_DELAY', 'DURATION_DAVY_BACK_FIGHT',
	 'DURATION_SURVIVAL_FIGHT',	'CLAN_EXP_FAKTOR_G', 'MAX_COUNT_PARALLEL_CFS');
	$value_array = array(MIN_MEMBERS, MAX_OPEN_REQUESTS, MAX_LEADER_ASSIGNED_CHARS, CLANFIGHTS_MAX_FIGHTS_PER_24H, CLANFIGHTS_RECHALLENGE_DELAY, DURATION_DAVY_BACK_FIGHT, DURATION_SURVIVAL_FIGHT,
	CLAN_EXP_FAKTOR_G, MAX_COUNT_PARALLEL_CFS);
	$change = false;

	//data überprüfen
	$i = 0;
	$log = null;
	if($data != null) {
		foreach ($data as $key => $value) {
			if($value != null && is_numeric($value) && $value != $value_array[$i]) {
				setDefine($key, $value);
				$log .= $key.'('.$value_array[$i].' auf '.$value.') ';
				$change = true;
			}
			$i++;
		}
		if($log != null) {
			logaction('Die Clankampf-Optionen wurden geändert: '.$log);
		}
	}

	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">'."\n";
	echo "\t".'<input type="hidden" name="choose" value="clan"></input>'."\n";
	echo "\t".'<input type="hidden" name="action" value="clanfights"></input>'."\n";
	echo "\t".'<table width="50%" border="0">'."\n";
	echo "\t".'<tr><th colspan="'.$colspan.'" align="center"><h1>Clankampf-Optionen</h1></th></tr>'."\n";
	
	if($change) {
		echo "\t\t".'<tr><td>'.$desc_array[$i].'</td><td style="text-align:center"></td>&Auml;nderungen &uuml;bernommen!</tr>'."\n";
	}
	
	for($i = 0;$i<count($value_array);$i++){
		echo "\t\t".'<tr><td>'.$desc_array[$i].'</td><td style="text-align:center"><input name="data['.$define_array[$i].']" value="'.
		(($data[$define_array[$i]] != null && is_numeric($data[$define_array[$i]]))?$data[$define_array[$i]]:$value_array[$i]).'"></input></td></tr>'."\n";
	}

	echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center"><input type="submit" value="speichern"></td></tr>'."\n";
	echo "\t".'<tr><td colspan="'.$colspan.'" style="text-align:center"><a href="'.$_SERVER['PHP_SELF'].'?choose=clan">Zum Clanmenu</a></td></tr>'."\n";
	echo "\t".'</table>'."\n";
	echo '</form>'."\n";
}

function displayGeneralOptions($ccfee) {
	$change = false;
	if($ccfee != null && $ccfee != CLAN_CREATION_FEE) {
		if(is_numeric($ccfee)) {
			setDefine('CLAN_CREATION_FEE', $ccfee);
			logaction('Clangebühr wurde von '.CLAN_CREATION_FEE.' auf '.$ccfee.' geändert.');
		}
	}
	?>
<form method="POST">
	<input type="hidden" name="choose" value="clan"> <input type="hidden"
		name="action" value="general">
	<table width="50%">
		<tr>
			<th colspan="2" align="center"><h1>Clan-Optionen</h1></th>
		</tr>
		<?php if($change === TRUE) {?>
		<tr>
			<td colspan="2" align="center">&Auml;nderungen &uuml;bernommen!</td>
		</tr>
		<?php }?>
		<tr>
			<td align="left">Gr&uuml;ndungsgeb&uuml;hr</td>
			<td align="center"><input type="text" name="ccfee"
				value=<?php echo ($ccfee == null)?CLAN_CREATION_FEE:$ccfee;?>></td>
		</tr>
		<tr>
			<td align="center" colspan="2"><input type="submit"
				value="&auml;ndern"></td>
		</tr>
		<tr>
			<td align="center" colspan="2"><a
				href="<?php echo $_SERVER['PHP_SELF'].'?choose=clan'; ?>">Zum
					Clanmenu</a>
			</td>
		</tr>
	</table>
</form>
		<?php
}

function displayClanOptions() {
	?>
<table>
	<tr>
		<td><a
			href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=clan&action=general">Clan-Optionen
				bearbeiten</a>
		</td>
	</tr>
	<tr>
		<td><a
			href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=clan&action=clanfights">Clankampf-Optionen
				bearbeiten</a>
		</td>
	</tr>
	<tr>
		<td><a
			href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=clan&action=rooms">Clanr&auml;ume
				bearbeiten</a>
		</td>
	</tr>
	<tr>
		<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a><br>
		</td>
	</tr>
</table>
	<?php
}

function displayClanMenu($action, $raumid, $raumname, $staerke, $verteidigung, $glueck, $ausdauer, $geschwindigkeit, $info, $level, $ccfee, $data) {
	switch ($action) {
		case 'editrooms':
			displayEditOrCreateClanRoom($action, $raumid, $raumname, $staerke, $verteidigung, $glueck, $ausdauer, $geschwindigkeit, $info, $level);
			break;
		case 'createrooms':
			displayEditOrCreateClanRoom($action, $raumid, $raumname, $staerke, $verteidigung, $glueck, $ausdauer, $geschwindigkeit, $info, $level);
			break;
		case 'deleterooms':
			displayDeleteClanRoom($raumname, $raumid);
			break;
		case 'rooms':
			displayClanItems();
			break;
		case 'general':
			displayGeneralOptions($ccfee);
			break;
		case 'clanfights':
			displayClanFightOptions($data);
			break;
		default:
			displayClanOptions();
			break;
	}
}

?>
