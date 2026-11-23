<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/pollfunctions.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$pollid = validateUnsignedInteger($_GET['pollid'], null);
$page = validateUnsignedInteger($_GET['page'], null);
$selected = validateUnsignedInteger($_GET['selected'], null);
$page = validateUnsignedInteger($_GET['page'], null);
//TODO
$comment = validateString($_GET['comment']);
// Unkritisch
$action = $_GET['action'];
$comment = $_GET['comment'];


function stelleUmfragenuebersichtDar($userid,$pageNumber){
	if($pageNumber == NULL || $pageNumber<0 || !is_numeric($pageNumber)){$pageNumber=0;}
	$entries = 10;
	$anzahl = getUmfragenCount();
	$results = getUmfragen($entries, $pageNumber);
	$url = '<a href="'.$_SERVER['PHP_SELF'].'?as=umfrage&page=###PAGE###">###LABEL###</a>';
	?>

		<form action="" method='GET'>
			<input type="hidden" name="as" value="umfrage"></input>
			<table width="100%">
				<tr>
					<th colspan="3">Umfragen</th>
				</tr>
				<tr>
					<th>Ersteller</th><th width="75%">Thema</th><th>Aktion</th>
	<?php
					for($i=0;$i<$entries && $results[$i]!=NULL;$i++){
						echo '<tr><td>'.$results[$i]['nickname'].'</td><td>'.$results[$i]['thema'].'</td><td><a href="'.$_SERVER['PHP_SELF'].'?as=umfrage&action=showPoll&pollid='.$results[$i]['pollid'].'">abstimmen</a></td></tr>';
					}
	?>
				<tr>
					<td colspan="3">
						<?php echo displayPagelinksNew($entries, $anzahl, $pageNumber, $url); ?>
					</td>
				</tr>
			</table>
		</form>
	<?php
}

function stelleUmfrageDar($userid, $pollid,$pageNumber){
	// Kommentare via Seiten blaettern
	if($pageNumber == NULL || $pageNumber<0 || !is_numeric($pageNumber)){$pageNumber=0;}
	$entries = 40;
	$anzahl = getCommentCount($pollid);
	$results = getComments($pollid, $entries, $pageNumber);
	$url = '<a href="'.$_SERVER['PHP_SELF'].'?as=umfrage&action=show&pollid='.$pollid.'&page=###PAGE###">###LABEL###</a>';

	// Hat man selbst schon gewaehlt??
	$choice = getStimme($pollid, $userid);
	$umfrage = getUmfrage($pollid);

	// Ist die Umfrage noch offen??
	$open = isOpen($pollid);


	if($choice || !$open){ // Ja, dann einfach nur Ergebnis anzeigen
		$ergebnisse = getPollResult($pollid);
		?>
		<table width="100%" border="0">
			<tr>
				<th colspan="3">
					<?php echo $umfrage['poll']['thema']; ?>
					<?php
						if(!$open){
							echo '(Umfrage beendet)';
						}
					?>
				</th>
			</tr>
			<tr>
				<td colspan="3"><?php echo $umfrage['poll']['text']; ?></td>
			</tr>
			<?php
				$max = 0;
				for($i = 0;$ergebnisse[$i] != null;$i++){
					$max += $ergebnisse[$i];
				}
				for($i = 0;$umfrage['options'][$i] != null;$i++){
					echo '<tr><td>'.$umfrage['options'][$i]['text'].'</td><td><img src="bilder/balken.php?max='.$max.'&act='.$ergebnisse[$i].'&width=350"></img></td><td>'.$ergebnisse[$i].'</td></tr>';
				}
			?>
			<tr>
				<th colspan="3">Kommentare</th>
			</tr>
			<?php
				$i = 0;
				while($results[$i] != null){
					echo '<tr><td>User hat f&uuml;r "'.$umfrage['options'][$results[$i]['polloptionid']]['text'].'" gestimmt</td><td colspan="2">'.$results[$i]['comment'].'</td></tr>';
					$i++;
				}
			?>
			<tr>
				<td>
					<?php echo displayPagelinksNew($entries, $anzahl, $pageNumber, $url); ?>
				</td>
			</tr>

		</table>
		<?php
	} else{ // Nein, dann eine Maske anzeigen, bei der man stimmen kann
		?>
		<form action="" method="get">
			<input type="hidden" name="as" value="umfrage"></input>
			<input type="hidden" name="action" value="vote"></input>
			<input type="hidden" name="pollid" value="<?php echo $pollid; ?>"></input>
			<table width="100%">
				<tr>
					<th colspan="2"><?php echo $umfrage['poll']['thema']; ?></th>
				</tr>
				<tr>
					<td colspan="2"><?php echo $umfrage['poll']['text']; ?></td>
				</tr>
				<?php
					$i = 0;
					while($umfrage['options'][$i] != null){
						echo '<tr><td>'.$umfrage['options'][$i]['text'].'</td><td><input id="input" type="radio" name="selected" value="'.$i.'"></td></tr>';
						$i++;
					}
				?>
				<tr>
					<th colspan="2">Kommentar</th>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<textarea id="input" cols="50" rows="10" name="comment"></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input id="input" type="submit" value="abstimmen!"></input>
					</td>
				</tr>
			</table>
		</form>
		<?php
	}
}

function trageStimmeEin($userid, $pollid, $selected,$kommentar){
	if(!isOpen($pollid)){
		echo '<table width="100%"><tr><td>Das Thema ist geschlossen</td></tr></table>';
		return;
	}
	$choice = getStimme($pollid, $userid);
	if($choice){
		echo '<table width="100%"><tr><td>Sie haben schon fuer dieses Thema gestimmt</td></tr></table>';
		return;
	}
	stimme($pollid, $selected, $userid, $kommentar);
	echo '<table width="100%"><tr><td><a href="'.$_SERVER['PHP_SELF'].'?as=umfrage>zur&uumlck</a></td></tr></table>';
}

if($action == 'showPoll'){
	stelleUmfrageDar($user_ida['id'], $pollid,$page);
} else if($action == 'vote'){
	trageStimmeEin($user_ida['id'], $pollid, $selected, $comment);
} else{
	stelleUmfragenuebersichtDar($user_ida['id'],$page);
}
?>