<?php
/*
 * Created on 14.08.2007
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
/******************************************************************************
* Dieser Skript erzeugt die Eingabemaske für die Auktion,
* es nutzt hierbei die in auktion_functions.inc.php zur verfügung gestellten Funktionen
*******************************************************************************
* Autor: Pascal Proksch
* Erstellungsdatum: 14.08.2007
* Zuletzt verändert: 15.08.2007
*******************************************************************************/

include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/auktion_functions.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$auktionsid = validateUnsignedInteger($_GET['auktionsid'],null);
$gebot = validateUnsignedInteger($_GET['gebot'],null);
$anzahl = validateUnsignedInteger($_GET['anzahl'], null);
$start = validateUnsignedInteger($_GET['start'], null);
$entries = validateUnsignedInteger($_GET['entries'], null);
$page = validateUnsignedInteger($_GET['page'], null);
$maxpreis = validateUnsignedInteger($_GET['maxpreis'], null);
$itemname = validateString($_GET['itemname']);

// Unkritisch
$pay = $_GET['pay'];
$action = $_GET['action'];

// Wird selbst geprueft!!
$items = $_GET['items'];
$order = $_GET['order'];
$dir = $_GET['dir'];
$kategorie = $_GET['kategorie'];


function stelleBietenMaskeDar($userid, $auktionsid, $gebot, $anzahl){
	$auktionsdaten = getEntryInformation($auktionsid);
	if ( $auktionsdaten['type'] == 1 ) {
		stelleBietenMaskeDarNormal($userid, $auktionsdaten, $gebot);
	} else if ($auktionsdaten['type'] == 2) {
		stelleBietenMaskeDarInverse($userid, $auktionsdaten, $gebot, $anzahl);
	} else {
		displayErrorMessage(NULL, 'Auktionstype nicht unterst&uuml;zt!!',displayHistoryBackLink());
	}
}

function stelleBietenMaskeDarNormal($userid,$auktionsdaten,$gebot) {
	if ($auktionsdaten === NULL) {
		displayErrorMessage(NULL, 'Auktion ist nicht mehr verf&uuml;gbar!!',displayHistoryBackLink());
		return;
	}
	
	if($gebot != NULL && $gebot != ''){
		$errorMsg = biete($userid,$auktionsdaten['auktionsid'],$gebot);
		if($errorMsg == ''){
			echo 'Es wurde erfolgreich f&uuml;r das Item geboten!<br><br>';
		} else{
			echo 'Es ist beim Bieten ein Fehler aufgetreten: '.$errorMsg.'!<br><br>';
		}
	}
	?>
	<form action="" method=GET>
		<input type="hidden" name="as" value="auktion"></input>
		<input type="hidden" name="action" value="1"></input>
		<input type='hidden' name="auktionsid" value='<?php echo $auktionsdaten['auktionsid']; ?>'></input>
		<table border="0" width="332" height="187" align="center">
			<tr>
				<th height="25" width="332" colspan="2" align="center">Item Auktion</th>
			</tr>
			<tr>
				<td height="25" width="138" align="left"><b>Name</b></td>
				<td height="25" width="178" align="left"><?php echo displayItemLink($auktionsdaten['tablename'], $auktionsdaten['itemid'], $auktionsdaten['itemname']); ?></td>
			</tr>
			<tr>
				<td height="25" width="138" align="left"><b>H&ouml;chstbietender?</b></td>
				<td height="25" width="178" align="left"> <?php echo displayUserLink($auktionsdaten['bieter'],$auktionsdaten['bietername']); ?> </td>
			</tr>
			<tr>
				<td height="25" width="138" align="left"><b>Aktuelles Gebot</b></td>
				<td height="25" width="178" align="left"><?php echo displayMoney($auktionsdaten['aktuellesgebot']); ?></td>
			</tr>
			<tr>
				<td height="25" width="138" align="left"><b>Startgebot</b></td>
				<td height="25" width="178" align="left"><?php echo displayMoney($auktionsdaten['startgebot']); ?></td>
			</tr>
			<tr>
				<td height="25" width="138" align="left"><b>Anzahl</b></td>
				<td height="25" width="178" align="left"><?php echo displayCount($auktionsdaten['anzahl']); ?></td>
			</tr>
			<?php
			if($auktionsdaten['anbieter'] != 1){
			?>
			<tr>
				<td height="25" width="138" align="left"><b>Durchschnittlicher Kaufpreis</b></td>
				<td height="25" width="178" align="left"><?php echo displayMoney(getAveragePrice($auktionsdaten['itemid'], $auktionsdaten['tablename'], $auktionsdaten['anzahl'])); ?></td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td height="25" width="138" align="left"><b>Bieten</b></td>
				<td height="25" width="178" align="left"><input id="input" type="int" name="gebot" value="<?php echo aktuellesMindestgebot($auktionsdaten['auktionsid']); ?>" size="7"></input></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td height="25" width="178" align="left"><input id="input" type="submit" value="bieten"></input></td>
			</tr>
			<tr>
				<td height="25" colspan="2">INFO: Es muss immer mindestens 2% mehr Geboten werden, als das akutelle Gebot.<br><br>Es ist verboten weit &uuml;ber dem Wert zu ersteigern! Dies wird als Cheatingverdacht behandelt und auch bestraft! Bsp. HP Trank II f&uuml;r 20.000</td>
			</tr>
		</table>
	</form>
<?php
}

function stelleBietenMaskeDarInverse($userid,$auktionsdaten,$gebot,$anzahl){
	if ($auktionsdaten === NULL) {
		displayErrorMessage(NULL, 'Auktion ist nicht mehr verf&uuml;gbar!!',displayHistoryBackLink());
		return;
	}
	
	if($gebot != NULL && $gebot != ''){
		$errorMsg = biete($userid,$auktionsdaten['auktionsid'],$gebot, $anzahl);
		if($errorMsg == ''){
			echo 'Es wurde erfolgreich f&uuml;r das Item geboten!<br><br>';
		} else{
			echo 'Es ist beim Bieten ein Fehler aufgetreten: '.$errorMsg.'!<br><br>';
		}
	}

	$listing = array();
	$qry = db_query('select range_from, range_to from inverse_auktion_gebote WHERE bieter = '.$userid.' and auktionsid = '.$auktionsdaten['auktionsid'].' order by range_from asc');
	while ($row = mysqli_fetch_assoc($qry)) {
		if ($row['range_from'] == $row['range_to']) {
			$listing[] = displayMoney($row['range_from']);
		} else {
			$listing[] = displayMoney($row['range_from']).'-'.displayMoney($row['range_to']);
		}
	}
	?>
	<script type="text/javascript">
	<!--
		function raise() {
			var start_gebot = <?php echo $auktionsdaten['startgebot']?>;
			if ( document.forms[0].elements["gebot"].value < start_gebot ) {
				document.forms[0].elements["gebot"].value = start_gebot;
			}
			if ( document.forms[0].elements["anzahl"].value < start_gebot ) {
				document.forms[0].elements["anzahl"].value = start_gebot;
			}
			if ( document.forms[0].elements["gebot"].value > document.forms[0].elements["anzahl"].value ) {
				document.forms[0].elements["anzahl"].value = document.forms[0].elements["gebot"].value;
			}
			var fBase  = start_gebot * 0.05;
			var fFrom = parseInt(document.forms[0].elements["gebot"].value);
			var fTo = parseInt(document.forms[0].elements["anzahl"].value);
			var blubb = fBase * (fTo - fFrom + 1); 
			document.forms[0].elements["output"].value = blubb;
		}
	//-->
	</script>
	
	<form action="" method=GET>
		<input type="hidden" name="as" value="auktion"></input>
		<input type="hidden" name="action" value="1"></input>
		<input type='hidden' name="auktionsid" value='<?php echo $auktionsdaten['auktionsid']; ?>'></input>
		<table border="0" width="400" height="187" align="center">
			<tr>
				<th height="25" width="332" colspan="2" align="center">Item Auktion</th>
			</tr>
			<tr>
				<td height="25" width="138" align="left"><b>Name</b></td>
				<td height="25" width="178" align="left"><?php echo displayItemLink($auktionsdaten['tablename'], $auktionsdaten['itemid'], $auktionsdaten['itemname']); ?></td>
			</tr>
			<tr>
				<td height="25" width="138" align="left"><b>Eigenes Gebot</b></td>
				<td height="25" width="178" align="left"><?php echo join(',', $listing); ?></td>
			</tr>
			<tr>
				<td height="25" width="138" align="left"><b>Startgebot</b></td>
				<td height="25" width="178" align="left"><?php echo displayMoney($auktionsdaten['startgebot']); ?></td>
			</tr>
			<tr>
				<td height="25" width="138" align="left"><b>Gebot</b></td>
				<td height="25" width="200" align="left">
				<input id="input1" class="input" type="number" name="gebot" style="width: 70px;" onchange="raise()" value="<?php echo $auktionsdaten['startgebot']; ?>" /> 
				- 
				<input id="input2" class="input" type="number" name="anzahl" style="width: 70px;" onchange="raise()" value="<?php echo $auktionsdaten['startgebot']; ?>"/>
				</td>
			</tr>
			<tr>
				<td height="25" width="138" align="left"><b>Gebühr</b></td>
				<td height="25" width="200" align="left">
					<input id="output" class="input" type="number" name="fee" disabled="disabled" style="width: 150px;" value="<?php echo round($auktionsdaten['startgebot'] * 0.05, 0); ?>"/>
				</td>
			</tr>
				
			<tr>
				<td>&nbsp;</td>
				<td height="25" width="178" align="left"><input id="input" type="submit" value="bieten"></input></td>
			</tr>
			<tr>
				<td height="25" colspan="2">INFO: Das kleinste einmalig abgegebene Gebot bekommt den Zuschlag. F&uuml;r jedes abgegebene Gebot muss eine Geb&uuml;hr bezahlt werden. Wird der Zuschlag erhalten wird auch noch der Kaufpreis f&auml;llig!</td>
			</tr>
		</table>
	</form>
<?php
}


function stelleAuktionErstellenMaskeDar($userid,$id_db_mix, $anzahl, $startgebot){
	// Maske in <html>
	if($id_db_mix != '' && !is_null($id_db_mix)){
		$errorMessage = insertItem($userid,$id_db_mix, $anzahl, $startgebot);
		if($errorMessage == '' OR $errorMessage == NULL){
			echo '<br>Das Item konnte erfolgreich eingef&uuml;gt werden.<br>';
			$id_db_mix = '';
			$anzahl = '1';
			$startgebot = '10';
		}
		else{
			echo '<br>Das Item konnte nicht eingef&uuml;gt werden: '.$errorMessage.'<br>';
		}
	}
	else{
			$anzahl = '1';
			$startgebot = '10';
	}
	?>
	<form action="" method=GET>
		<input type="hidden" name="as" value="auktion"></input>
		<input type="hidden" name="action" value="2"></input>
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<tr>
				<th height="20" valign="center" width="557" colspan="2" align="center">Item versteigern!</th>
			</tr>
			<tr>
				<td height="25" width="50%" valign="center" align="center">Item</td>
				<td height="25" width="50%" valign="center" align="right">
					<select id="input" name="items">
<?php
					// Nun erstmal die Optionen empfangen
					$options = getItemOptions($userid,$id_db_mix);
					$index = 0;
					while($options[$index] != NULL){
						echo "\t\t\t\t\t".$options[$index++]."\n";
					}
?>					</select>
				</td>
			</tr>
			<tr>
				<td height="25" width="50%" valign="center" align="center">Mindestgebot</td>
				<td height="25" width="50%" valign="center" align="right"> <input id="input" type="int" name="start" value="<?php echo $startgebot; ?>" size="5"></input></td>
			</tr>
			<tr>
				<td height="25" width="50%" valign="center" align="center">Anzahl</td>
				<td height="25" width="50%" valign="center" align="right"> <input id="input" type="int" name="anzahl" value="<?php echo $anzahl; ?>" size="5"></input></td>
			</tr>
			<tr>
				<td height="25" width="50%">&nbsp;</td>
				<td height="25" width="50%" valign="center" align="right"><input id="input" type=submit value="Verkaufen"></input></td>
			</tr>
			<tr>
				<td height="25" colspan="2">Da dieser Service nicht gratis ist, werden als Bearbeitungsgeb&uuml;hr 1% des Versteigerungspreises enthalten.</td>
			</tr>
		</table>
	</form>
	<?php
}

function stelleAuktionsuebersichtDar($userid,$kategorie,$itemnamepart,$entries,$pageNumber,$maxpreis,$order,$dir){
	if($pageNumber == NULL || $pageNumber<0 || !is_numeric($pageNumber)){$pageNumber=0;}
	if($entries == NULL || $entries == 0 || !is_numeric($entries)){ $entries = 20;}

	if(!($kategorie == 'ware' || $kategorie == 'sp_ware' || $kategorie == 'wochen_ware')){$kategorie = 'all';}
	if(!($order == 'itemname' || $order == 'aktuellesgebot' || $order ==  'anbietername' || $order == 'bietername' || $order == 'anzahl' || $order == 'deadline')){
		$order = 'deadline';
	}
	if($dir != 'DESC' || $dir == NULL){
		$dir = 'ASC';
	}
//	// Wichtig fuer die SQL-Abfrage -> Muss nicht mehr
//	$itemnamepart = preg_replace('#\'#','',$itemnamepart);

	$orderstring = '<a href="index.php?as=auktion&page='.$pageNumber.'&entries='.$entries.'&maxpreis='.$maxpreis.'&kategorie='.$kategorie.'&itemname='.$itemnamepart.'&###ORDER###">###LABEL###</a>';
	?>
	<form action="" method='GET'>
		<input type="hidden" name="as" value="auktion"></input>
		<input type="hidden" name="order" value="<?php echo $order; ?>"></input>
		<input type="hidden" name="dir" value="<?php echo $dir; ?>"></input>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="30" valign="top" width="410" align="center"><b>Auktions-Markt</b></td>
			<td height="30" valign="top" width="136" align="center"><b><a href="index.php?as=auktion&action=2">Item Online Stellen</a></b></td>
		</tr>
		<tr>
			<td align="center">Suchfeld einschr&auml;nken</td>
			<td align="right">
				<select id="input" id="input" name='kategorie'>
<?php
			$index = 0;
			$options = getKategorieOptions($kategorie);
			while($options[$index] != NULL){
				echo "\t\t\t\t".$options[$index++]."\n";
			}
?>			</select>
			</td>
		</tr>
		<tr>
			<td align="center">Auktionen pro Seite</td>
			<td align="right"><input id="input" type="int" name="entries" size="5" value="<?php echo $entries;?>"></input></td>
		</tr>
		<tr>
			<td align="center">Maximalgebot</td>
			<td align="right"><input id="input" type="int" name="maxpreis" size="5" value="<?php echo $maxpreis;?>"></input></td>
		</tr>
		<tr>
			<td align="center">Itemname</td>
			<td align="right"><input id="input" name="itemname" size="5" value="<?php echo $itemnamepart;?>"></input></td>
		</tr>
		<tr>
			<td height="25" width="131">&nbsp;</td>
			<td height="25" width="426" valign="center" align="right"><input id="input" type=submit value="Suchanfrage absenden"></input></form></td>
		</tr>
		<tr>
			<td height="25" width="546" colspan="2">
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<th height="31" valign="top" align="center">
<?php
							if($order == 'itemname' && $dir == 'ASC'){
								echo preg_replace(array('/###ORDER###/','/###LABEL###/'), array('order=itemname&dir=DESC','Item'),$orderstring);
							}
							else{
								echo preg_replace(array('/###ORDER###/','/###LABEL###/'), array('order=itemname&dir=ASC','Item'),$orderstring);
							}
?>
						</th>
						<th height="31" valign="top" align="center">
<?php
							if($order == 'type' && $dir == 'ASC'){
								echo preg_replace(array('/###ORDER###/','/###LABEL###/'), array('order=type&dir=DESC','Typ'),$orderstring);
							} else{
								echo preg_replace(array('/###ORDER###/','/###LABEL###/'), array('order=type&dir=ASC','Typ'),$orderstring);
							}

?>
						</th>
						<th height="31" valign="top" align="center">
<?php
							if($order == 'aktuellesgebot' && $dir == 'ASC'){
								echo preg_replace(array('/###ORDER###/','/###LABEL###/'), array('order=aktuellesgebot&dir=DESC','Gebot'),$orderstring);
							} else{
								echo preg_replace(array('/###ORDER###/','/###LABEL###/'), array('order=aktuellesgebot&dir=ASC','Gebot'),$orderstring);
							}

?>
						</th>
						<th height="31" valign="top" align="center">
<?php					if($order == 'anbietername' && $dir == 'ASC'){
								echo preg_replace(array('/###ORDER###/','/###LABEL###/'), array('order=anbietername&dir=DESC','Anbieter'),$orderstring);
							} else{
								echo preg_replace(array('/###ORDER###/','/###LABEL###/'), array('order=anbietername&dir=ASC','Anbieter'),$orderstring);
							}
?>
						</th>
						<th height="31" valign="top" align="center">
<?php					if($order == 'bietername' && $dir == 'ASC'){
									echo preg_replace(array('/###ORDER###/','/###LABEL###/'), array('order=bietername&dir=DESC','Bieter'),$orderstring);
							} else{
									echo preg_replace(array('/###ORDER###/','/###LABEL###/'), array('order=bietername&dir=ASC','Bieter'),$orderstring);
							}
?>
						</th>
						<th height="31" valign="top" align="center">
<?php					if($order == 'anzahl' && $dir == 'ASC'){
									echo preg_replace(array('/###ORDER###/','/###LABEL###/'), array('order=anzahl&dir=DESC','Anzahl'),$orderstring);
							} else{
									echo preg_replace(array('/###ORDER###/','/###LABEL###/'), array('order=anzahl&dir=ASC','Anzahl'),$orderstring);
							}
?>
						</th>
						<th height="31" valign="top" align="center">
<?php					if($order == 'deadline' && $dir == 'ASC'){
									echo preg_replace(array('/###ORDER###/','/###LABEL###/'), array('order=deadline&dir=DESC','Deadline'),$orderstring);
							} else{
									echo preg_replace(array('/###ORDER###/','/###LABEL###/'), array('order=deadline&dir=ASC','Deadline'),$orderstring);
							}
?>
						</th>
						<th height="31" valign="top" align="center">
							Bieten
						</th>
					</tr>
<?php
				$aktionentries = getAuktionEntries($kategorie,$itemnamepart,$entries,$pageNumber,$maxpreis,$order,$dir);
				$index = 0;
				while($aktionentries[$index] != NULL){
					echo "\t\t\t\t".'<tr>'."\n";
					echo "\t\t\t\t\t".'<td height="31" valign="top" align="center">'.displayItemLink($aktionentries[$index]['tablename'], $aktionentries[$index]['itemid'], $aktionentries[$index]['itemname']).'</td>'."\n";
					echo "\t\t\t\t\t".'<td height="31" valign="top" align="center">'.($aktionentries[$index]['type']==1?'Normal':'Inverse').'</td>'."\n";
					if($aktionentries[$index]['aktuellesgebot'] != NULL){
						echo "\t\t\t\t\t".'<td height="31" valign="top" align="center">'.displayMoney($aktionentries[$index]['aktuellesgebot']).'</td>'."\n";
					} else{
						echo "\t\t\t\t\t".'<td height="31" valign="top" align="center">'.displayMoney($aktionentries[$index]['startgebot']).'</td>'."\n";
					}
					echo "\t\t\t\t\t".'<td height="31" valign="top" align="center">'.displayUserLink($aktionentries[$index]['anbieter'],$aktionentries[$index]['anbietername']).'</td>'."\n";
					echo "\t\t\t\t\t".'<td height="31" valign="top" align="center">'.displayUserLink($aktionentries[$index]['bieter'],$aktionentries[$index]['bietername']).'</td>'."\n";
					echo "\t\t\t\t\t".'<td height="31" valign="top" align="center">'.displayCount($aktionentries[$index]['anzahl']).'</td>'."\n";
					echo "\t\t\t\t\t".'<td height="31" valign="top" align="center">'.$aktionentries[$index]['deadline'].'</td>'."\n";
					if($userid == $aktionentries[$index]['anbieter']){
						echo "\t\t\t\t\t".'<td height="31" valign="top" align="center"><a href="index.php?as=auktion&action=3&auktionsid='.$aktionentries[$index]['auktionsid'].'">zur&uuml;ckziehen</a></td>'."\n";
					} else{
						echo "\t\t\t\t\t".'<td height="31" valign="top" align="center"><a href="index.php?as=auktion&action=1&auktionsid='.$aktionentries[$index]['auktionsid'].'">mitbieten</a></td>'."\n";
					}
					echo "\t\t\t\t".'</tr>'."\n";
					$index++;
				}
?>
				</table>
			</td>
		</tr>
		<tr>
			<td align="center" width="100%" colspan="2">
<?php
						if($entries > 0){
							$anzahl = ceil(getAuktionTotalCount($kategorie,$itemnamepart,$maxpreis) / $entries);
							$pagestring = '<a href="index.php?as=auktion&page=###PAGE###&entries='.$entries.'&maxpreis='.$maxpreis.'&kategorie='.$kategorie.'&itemname='.$itemnamepart.'&order='.$order.'&dir='.$dir.'">###LABEL###</a>';
							echo displayPagelinks($pageNumber,$anzahl,$pagestring);
						}
?>
			</td>
		</tr>
	</table>
	<input type='hidden' name='page' value='0'></input>
</form>
<?php
}

function zieheAuktionZurueck($userid,$auktionsid,$pay){
	$auktionsdaten = getEntryInformation($auktionsid);
	if ($auktionsdaten === NULL) {
		displayErrorMessage(NULL, 'Auktion ist nicht mehr verf&uuml;gbar!!',displayHistoryBackLink());	
		return;
	}


	if($pay == NULL || $pay == ''){
		// Nix eigentlich!!
		$pay = $auktionsdaten['aktuellesgebot']!=NULL?'YES':'NO';
	} else{
		$errMessage = zurueckziehen($userid, $auktionsid, $pay);
		if($errMessage == ''){
			echo 'Zur&uuml;ckziehen erfolgreich!';
			return;
		}
		else{
			echo 'Fehler beim Zur&uuml;ckziehen aufgetreten: '.$errMessage;
		}
	}
?>
	<form action="" method=GET>
		<input type="hidden" name="as" value="auktion"></input>
		<input type="hidden" name="action" value="3"></input>
		<input type="hidden" name="auktionsid" value='<?php echo $auktionsdaten['auktionsid']; ?>'></input>
		<input type="hidden" name="pay" value="<?php echo $pay; ?>"></input>
		<table border="0" width="332" height="187" align="center">
			<tr>
				<th height="25" width="332" colspan="2" align="center">Item zur&uuml;ckziehen</th>
			</tr>
			<tr>
				<td height="25" width="138" align="left"><b>Name</b></td>
				<td height="25" width="178" align="left"><?php echo displayItemLink($auktionsdaten['tablename'], $auktionsdaten['itemid'], $auktionsdaten['itemname']); ?></td>
			</tr>
			<tr>
				<td height="25" width="138" align="left"><b>H&ouml;chstbietender?</b></td>
				<td height="25" width="178" align="left"> <?php echo displayUserLink($auktionsdaten['bieter'],$auktionsdaten['bietername']);; ?> </td>
			</tr>
			<tr>
				<td height="25" width="138" align="left"><b>Aktuelles Gebot</b></td>
				<td height="25" width="178" align="left"><?php echo displayMoney($auktionsdaten['aktuellesgebot']); ?></td>
			</tr>
			<tr>
				<td height="25" width="138" align="left"><b>Startgebot</b></td>
				<td height="25" width="178" align="left"><?php echo displayMoney($auktionsdaten['startgebot']); ?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td height="25" width="178" align="left"><input id="input" type="submit" value="zur&uuml;ckziehen"></input></td>
			</tr>
			<tr>
				<td height="25" colspan="2">
					Auktionen, bei denen noch niemand geboten hat, k&ouml;nnen ohne zus&auml;tzliche Kosten zur&uuml;ckgezogen werden. Auktionen mit aktuellem Gebot kosten beim Zur&uuml;ckziehen 10% des aktuellen Gebots!
				</td>
			</tr>
		</table>
	</form>
<?php

}

if(isset($user_ida['id'])){
	// Abfrage der uebergebenen Aktionen
	if($action == 1){ // Bieten für eine Auktion
		stelleBietenMaskeDar($user_ida['id'],$auktionsid, $gebot, $anzahl);
	} else if($action == 2){ // Erstellen einer Auktion
		stelleAuktionErstellenMaskeDar($user_ida['id'],$items,$anzahl,$start);
	} elseif($action == 3){ // Zurueckziehen einer Auktion
		zieheAuktionZurueck($user_ida['id'],$auktionsid,$pay);
	} else{ // Ansonsten Standardfenster
		stelleAuktionsuebersichtDar($user_ida['id'],$kategorie,$itemname,$entries,$page,$maxpreis,$order,$dir);
	}
}

?>
