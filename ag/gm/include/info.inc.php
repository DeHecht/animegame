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
function displayFaq($action, $faqentry,$parent,$header,$body, $child){
	$themes = getTableOfContents('<option value="###ID###">','</option>');
	if($action == 'confirm'){
		$header = urldecode($header);
		$body = urldecode($body);
		updateEntry($faqentry,$parent,$header,$body);
		logaction('Faqeintrag '.$faqentry.' angepasst!');
		echo '&Auml;nderungen gespeichert!';
		displayFaq('edit', $faqentry,$parent,$header,$body,null);
	}	else if($action == 'insert'){
		$header = urldecode($header);
		$body = urldecode($body);
		insertFaqEntry($parent, $header, $body);
		logaction('Faqeintrag bei '.$parent.' hinzugefuegt!');
		echo 'Datensatz eingef&uuml;gt!';
		displayFaq(null, null,null,null,null,null);
	} else if($action == 'moveup'){
		moveUp($child);
		logaction('Faqeintrag '.$child.' verschoben!');
		displayFaq('edit', $faqentry,null,null,null,null);
	} else if($action == 'movedown'){
		moveDown($child);
		logaction('Faqeintrag '.$child.' verschoben!');
		displayFaq('edit', $faqentry,null,null,null,null);
	} else if($action == 'create'){
	?>
		<form action="" method="POST">
			<input name="choose" value="faq" type="hidden"></input>
			<input name="action" value="insert" type="hidden"></input>
			<table>
				<tr>
					<th colspan="2">FAQ Bearbeitung</th>
				</tr>
				<tr>
					<td>
						Vaterknoten
					</td>
					<td>
						<select name="parent">
							<option value="0"></option>
							<?php echo $themes; ?>						
						</select>
					</td>
				</tr>
				<tr>
					<td>
						Titel
					</td>
					<td>
						<input name="header" value="" size="50"></input>
					</td>
				</tr>
				<tr>
					<td>
						Text
					</td>
					<td>
						<textarea name="body" cols="50" rows="10"></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" value="einf&uuml;gen"></input>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=faq">Zur &Uuml;bersicht</a><br>
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a><br>
					</td>
				</tr>
			</table>
		</form>
	<?php
	}	else if($action == 'edit'){
		$entry = getEntry($faqentry);
	?>
		<form action="" method="POST">
			<input name="choose" value="faq" type="hidden"></input>
			<input name="action" value="confirm" type="hidden"></input>	
			<input name="faqentry" value="<?php echo $faqentry; ?>" type="hidden"></input>
			<table>
				<tr>
					<th colspan="2">FAQ Bearbeitung</th>
				</tr>
				<tr>
					<td>
						Vaterknoten
					</td>
					<td>
						<select name="parent">
							<option value="">Keine &Auml;nderung</option>
							<option value="0"></option>
							<?php echo $themes; ?>						
						</select>
					</td>
				</tr>
				<tr>
					<td>
						Titel
					</td>
					<td>
						<input name="header" value="<?php echo debbcode($entry['header']); ?>" size="50"></input>
					</td>
				</tr>
				<tr>
					<td>
						Text
					</td>
					<td>
						<textarea name="body" cols="50" rows="10"><?php echo debbcode($entry['body']); ?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" value="speichern"></input>
					</td>
				</tr>
				<tr>
					<td>
<?php
						$results = getChildren($faqentry);
						for($i=0;$i<count($results);$i++){
							$urlhoch = $_SERVER['PHP_SELF'].'?choose=faq&faqentry='.$faqentry.'&action=moveup&child='.$results[$i]['id'];
							$urldown = $_SERVER['PHP_SELF'].'?choose=faq&faqentry='.$faqentry.'&action=movedown&child='.$results[$i]['id'];
							echo $results[$i]['header'].' <a href="'.$urlhoch.'">hoch</a> <a href="'.$urldown.'">down</a><br>';
						}
?>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=faq">Zur &Uuml;bersicht</a><br>
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a><br>
					</td>
				</tr>
			</table>
		</form>
	<?php
	} else{
	?>
		<form action="" method="POST">
			<input name="choose" value="faq" type="hidden"></input>
			<input name="action" value="edit" type="hidden"></input>
			<table>
				<tr>
					<th>FAQ Bearbeitung</th>
				</tr>
				<tr>
					<td>
						<select name="faqentry">
							<option value="0">Vaterknoten</option>
							<?php echo $themes; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<input type="submit" value="bearbeiten"></input>
					</td>
				</tr>
				<tr>
					<td>
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=faq&action=create">Neuen Eintrag erstellen</a><br>
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a><br>
					</td>
				</tr>
			</table>
		</form>
	<?php
	}
}

function displayNews($action, $username, $newsid, $header, $value){
	if($action == 'create' && $header !== NULL){
		if($header !== null && $value !== null){
			$header = addslashes($header);
			$value = urldecode($value);
			$value = encodeNoHTMLWithBB($value);
			db_query('INSERT INTO ff11_news(name, betreff, text, zeit, datum)' .
						' values(\''.$username.'\',\''.$header.'\',\''.$value.'\',CURRENT_TIME, CURRENT_DATE)');
			if(db_affected_rows() > 0){
				logaction('News '.$header.' wurde erstellt!');
				echo 'news erfolgreich erstellt!<br>';
			} else {
				echo 'news erstellen fehlgeschlagen!<br>';
			}
		}
	} else if($action == 'edit' && $header !== NULL){
		$header = addslashes($header);
		$value = urldecode($value);
		$value = encodeNoHTMLWithBB($value);
		$sql = 'UPDATE ff11_news set betreff = \''.$header.'\', text = \''.$value.'\' where id ='.$newsid;
		echo $sql.'<br>';
		db_query($sql);
		if(db_affected_rows() > 0){
			logaction('News '.$header.' wurde editiert!');
			echo 'news erfolgreich editiert!<br>';
		} else {
			echo 'news editieren fehlgeschlagen!<br>';
		}
	}
	if($action == 'create' || ($action == 'edit' && $newsid !== NULL)){
		if($action == 'create'){
			$topic = 'Neuen Newseintrag erstellen';
			$buttonval = 'erstellen';
		} else{
			$topic = 'Newseintrag editieren';
			$row = mysqli_fetch_assoc(db_query('Select * from ff11_news where id = '.$newsid));
			$header = debbcode($row['betreff']);
			$value = debbcode($row['text']);
			$buttonval = 'editieren';
		}
		?>
		<form action="" method="POST">
			<input name="choose" value="news" type="hidden"></input>
			<input name="action" value="<?php echo $action; ?>" type="hidden"></input>
			<input name="newsid" value="<?php echo $newsid; ?>" type="hidden"></input>
			<table>
				<tr>
					<th colspan="2"><?php echo $topic; ?></th>
				</tr>
				<tr>
					<td>
						&Uuml;berschrift:
					</td>
				</tr>
				<tr>
					<td>
						<input name="header" value="<?php echo $header; ?>"></input>
					</td>
				</tr>
				<tr>
					<td>
						Text:
					</td>
				</tr>
				<tr>
					<td>
					  <textarea name="value" rows="10" cols="50"><?php echo $value; ?></textarea>
					</td>
				</tr>
				<tr>
					<td>
						<input type="submit" value="<?php echo $buttonval; ?>"></input>
					</td>
				</tr>
				<tr>
					<td align="center">
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
					</td>
				</tr>
			</table>
		</form>
		<?php
	} else if($action == 'edit'){
		$qry = db_query('Select * from ff11_news order by id desc');
		?>
			<table>
				<tr>
					<th>News bearbeiten (Auswahl)</th>
				</tr>
				<?php
					while($row = mysqli_fetch_assoc($qry)){
					?>
				<tr>
					<td>
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=news&action=edit&newsid=<?php echo $row['id']; ?>"><?php echo $row['betreff']; ?></a>
					</td>
				</tr>
					<?php
					}
				?>
				<tr>
					<td align="center">
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=news">Zum Newsmenu</a>
					</td>
				</tr>
				<tr>
					<td align="center">
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
					</td>
				</tr>
			</table>	
		<?php
	} else{
		?>
			<table>
				<tr>
					<th>News bearbeiten</th>
				</tr>
				<tr>
					<td>
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=news&action=create">Newseintrag erstellen</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=news&action=edit">Newseintrag editieren</a>
					</td>
				</tr>
				<tr>
					<td align="center">
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
					</td>
				</tr>
			</table>
		<?php
	}
}

function displayPolls($action,$openumfrage,$closeumfrage,$thema,$optionen,$text,$pollid){
	if($action == NULL){
?>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
			<input type="hidden" name="choose" value="poll"></input>
			<input type="hidden" name="action" value="display"></input>
			<table>
				<tr>
					<td>
						Offene Umfragen
					</td>
					<td>
						<select name="openumfrage">
							<option value="-1">---Bitte Ausw&auml;hlen---</option>
							<?php
								$qry = db_query('SELECT * from poll WHERE TIMESTAMPDIFF(Minute, close, now()) <= 0 || close is null');
								while($result = mysqli_fetch_assoc($qry)){
									echo '<option value="'.$result['pollid'].'">'.$result['thema'].'</option>';
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						Abgeschlossene Umfragen
					</td>
					<td>
						<select name="closeumfrage">
							<option value="-1">---Bitte Ausw&auml;hlen---</option>
							<?php
								$qry = db_query('SELECT * from poll WHERE TIMESTAMPDIFF(Minute, close, now()) > 0');
								while($result = mysqli_fetch_assoc($qry)){
									echo '<option value="'.$result['pollid'].'">'.$result['thema'].'</option>';
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" value="&Ouml;ffne Umfrage">
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=poll&action=create">Erstelle neue Umfrage</a>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>							
					</td>
				</tr>
			</table>
		</form>
	<?php
	} else if($action == 'display'){
		if(is_numeric($openumfrage) && $openumfrage != -1){
			$pollid = $openumfrage;
		}
		if(is_numeric($closeumfrage) && $closeumfrage != -1){
			$pollid = $closeumfrage;
		}
		if(is_numeric($pollid) && $pollid >= 0){
			$result = mysqli_fetch_assoc(db_query('SELECT * FROM poll WHERE pollid='.$pollid));
			$qry = db_query('SELECT * FROM poll_options WHERE pollid='.$pollid);
			while($opts = mysqli_fetch_assoc($qry)){
				$options[$opts['polloptionid']] = $opts['text'];
			}
			?>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
				<input type="hidden" name="choose" value="poll"></input>
				<input type="hidden" name="action" value="update"></input>
				<input type="hidden" name="pollid" value="<?php echo $result['pollid']; ?>"></input>
				<table>
					<tr>
						<td>Thema</td>
						<td><input name="thema" value="<?php echo $result['thema']; ?>"></input></td>
					</tr>
					<?php
						for($i=0;$i<5;$i++){
					?>
						<tr>
							<td>Option <?php echo $i+1; ?></td>
							<td><input name="optionen[<?php echo $i; ?>]" value="<?php echo $options[$i]; ?>"></input></td>
						</tr>
					<?php
						}
					?>
						<tr>
							<td colspan="2">
								<textarea name="text" cols="50" rows="10"><?php echo $result['text']; ?></textarea>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="submit" value="&Auml;nderungen &uuml;bernehmen!"></input>
							</td>
						</tr>
						<tr>
							<td>
								<a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=bann">Zur &Uuml;bersicht</a>
								<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
							</td>
						</tr>
				</table>
			</form>
		<?php
		}
	} else if($action == 'create'){
		?>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
				<input type="hidden" name="choose" value="poll"></input>
				<input type="hidden" name="action" value="save"></input>
				<table>
					<tr>
						<td>Thema</td>
						<td><input name="thema"></input></td>
					</tr>
					<?php
						for($i=0;$i<5;$i++){
					?>
						<tr>
							<td>Option <?php echo $i+1; ?></td>
							<td><input name="optionen[<?php echo $i; ?>]"></input></td>
						</tr>
					<?php
						}
					?>
						<tr>
							<td colspan="2">
								<textarea name="text" cols="50" rows="10">Hier ein Kommentar!</textarea>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="submit" value="Umfrage erstellen!"></input>
							</td>
						</tr>
						<tr>
							<td>
								<a href="<?php echo $_SERVER['PHP_SELF']; ?>?choose=bann">Zur &Uuml;bersicht</a>
								<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
							</td>
						</tr>
				</table>
			</form>
		<?php
	} else if($action == 'save'){
		db_query('INSERT INTO poll(ersteller, thema, open, close, text) values('.$_SESSION['user'].',\''.$thema.'\',now(),NULL,\''.$text.'\')');
		$pollid = mysqli_fetch_assoc(db_query('SELECT pollid FROM poll WHERE text=\''.$text.'\''));
		for($i=0;$i<5;$i++){
			if($optionen[$i] != null){
				db_query('INSERT INTO poll_options(pollid, polloptionid, text) values('.$pollid['pollid'].','.$i.',\''.$optionen[$i].'\')');
			}
		}
		displayPolls('display',NULL, NULL,NULL,NULL,NULL,$pollid['pollid']);
	} else if($action == 'update'){
		db_query('UPDATE poll set thema =\''.$thema.'\', text =\''.$text.'\' WHERE pollid ='.$pollid);
		for($i=0;$i<5;$i++){
			if($optionen[$i] != null){
				$sql = 'UPDATE poll_options SET text =\''.$optionen[$i].'\' WHERE pollid='.$pollid.' AND polloptionid ='.$i;
				db_query($sql);
				echo $sql.'<br>';
			}
		}
		displayPolls('display',NULL, NULL,NULL,NULL,NULL,$pollid);
	} else{
		echo 'BUG!! Mal wieder!!';
	}
}
?>