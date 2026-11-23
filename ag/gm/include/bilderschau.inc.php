<?php 
/*
 * Created on 31.05.2011
 *
 * @copyright (c) 2011 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
?>

<?php /**
 * TODO: 2-3 Ansichten
 * Sortiert nach erstelldatum, zufällig
 * Erstelldatum: User, Char
 * Zufällig: User, Char
 * Sortiert nach User 20 User pro Seite (Alphabetisch! mit Buchstabenauswahl ;))
 */

function zeigeBilder($begin, $bildurl, $sure, $view, $letter, $date_from, $date_until){
	echo '<table>';
	$colspan = '';
	if($view === NULL){ 
		echo '<tr><th'.$colspan.'>Auf welche Art und Weise m&ouml;chtest du dir die Avatare anzeigen lassen?</th></tr>';
	} else if($view == 'ByDate'){
		$colspan = ' colspan=9';
		echo '<tr><th'.$colspan.'>Avatar nach Datum</th></tr>';
		if($date_from == NULL){
			$date_from = 'CURRENT_DATE';
		} else{
			$date_from = '\''.$date_from.'\'';
		}
		if($date_until == NULL){
			$date_until = 'CURRENT_TIMESTAMP';
		} else{
			$date_until = '\''.$date_until.'\'';
		}

		echo '<tr><form action="'.$_SERVER['PHP_SELF'].'" method="GET"><input type="hidden" name="choose" value="bilder" /><input type="hidden" name="view" value="ByDate" /><td>Von:<input name="date_from"></td><td>Bis: <input name="date_until"></td><td><input type="submit" name="aktualisieren"></td></form></tr>';		
		$test = 'SELECT name, nickname, bild FROM chars inner join user on chars.besitzer = user.id where bild != \'design/bilder/avatare/noavart.gif\' and bild != \'\' and bild_edit is not null and bild_edit between '.$date_from.' and '.$date_until.' group by besitzer, bild order by nickname';
//		echo $test.'<br>';
		$qry = db_query($test);
		$actual = '';
		$i = 0;
		while($result = mysqli_fetch_assoc($qry)){
			if($actual == '' || $result['nickname'] != $actual){
				// Fuelle die Leeren Felder aus
				for(;$i<8;$i++){
					echo '<td></td>';
				}
				echo '</tr>';
				$actual = $result['nickname'];
				echo '<tr><td>'.$result['nickname'].'</td>';
			}
			echo '<td><a href="gm_seite.php?choose=bilder&view=SinglePic&bildurl='.urlencode($result['bild']).'">'.$result['name'].'<br><img src="'.$result['bild'].'" width="75" height="75"></a></td>';
			$i++;
		}
		echo '</tr>';
		
	} else if($view == 'Random'){
		$colspan = ' colspan=5';
		$sql = 'SELECT name, nickname, bild FROM chars inner join user on chars.besitzer = user.id where besitzer > 2 and bild != \'design/bilder/avatare/noavart.gif\' and bild != \'\' group by bild order by rand() limit 20';
		$qry = db_query($sql);
		echo '<tr><th'.$colspan.'>Avatar Zufall</th></tr>';
		for($i=0;$i<4;$i++){
			echo '<tr>';
			for($t=0;$t<5;$t++){
				$tmp = mysqli_fetch_assoc($qry);
				if($tmp){
					echo '<td><a href="gm_seite.php?choose=bilder&view=SinglePic&bildurl='.urlencode($tmp['bild']).'">'.$tmp['name'].'<br><img src="'.$tmp['bild'].'" width="75" height="75"></a></td>';					
				} else {
					echo '<td>&nbsp;</td>';
				}
			}
			echo '</tr>';
		}
		echo '<tr><td'.$colspan.'><a href="'.$_SERVER['PHP_SELF'].'?choose=bilder&view=Random">weiter</a></td></tr>';
	} else if($view == 'ByUser'){
		if($begin == NULL){$begin = 0;}
		// Die Bilder werden so dargestellt, dass man meherere auf einer seite sehen kann.
		// Dabei werden die Bilder jedoch gebündelt, dass keine doppelten urls angezeigt werden

		$colspan = ' colspan=9';
		echo '<tr><th'.$colspan.'>Sortiert nach Nutzernamen</th></tr>';
		echo '<tr><td'.$colspan.'><table width="100%"><tr>';
		echo '<td><a href="'.$_SERVER['PHP_SELF'].'?choose=bilder&view=ByUser">Alle</a></td>';
		echo '<td><a href="'.$_SERVER['PHP_SELF'].'?choose=bilder&view=ByUser&letter=Numeric">0-9</a></td>';
		for($i=ord('A');$i<=ord('Z');$i++){
			if($i == ord($letter)){
				echo '<td>'.$letter.'</td>';
			} else{
				echo '<td><a href="'.$_SERVER['PHP_SELF'].'?choose=bilder&view=ByUser&letter='.chr($i).'">'.chr($i).'</a></td>';
			}
		}
		echo '</tr></table></td></tr>';

		if($letter == 'Numeric'){
			$auser = db_query('SELECT distinct(user.nickname) FROM chars inner join user on chars.besitzer = user.id where SUBSTRING(nickname,1,1) < \'a\' and bild != \'design/bilder/avatare/noavart.gif\' and bild != \'\' group by user.id order by nickname ASC LIMIT '.($begin*10).', 10');
			$total = mysqli_fetch_assoc(db_query('SELECT count(distinct(user.id)) as anzahl FROM chars inner join user on chars.besitzer = user.id  where SUBSTRING(nickname,1,1) < \'a\' and bild != \'design/bilder/avatare/noavart.gif\' and bild != \'\''));
		} else if($letter == null){
			$auser = db_query('SELECT * FROM chars inner join user on chars.besitzer = user.id where bild != \'design/bilder/avatare/noavart.gif\' and bild != \'\' group by user.id order by nickname ASC LIMIT '.($begin*10).', 10');
			$total = mysqli_fetch_assoc(db_query('SELECT count(distinct(user.id)) as anzahl FROM chars inner join user on chars.besitzer = user.id where bild != \'design/bilder/avatare/noavart.gif\' and bild != \'\''));
		} else{
			$auser = db_query('SELECT * FROM chars inner join user on chars.besitzer = user.id where nickname like \''.$letter.'%\' and bild != \'design/bilder/avatare/noavart.gif\' and bild != \'\' group by user.id order by nickname ASC LIMIT '.($begin*10).', 10');	
			$total = mysqli_fetch_assoc(db_query('SELECT count(distinct(user.id)) as anzahl FROM chars inner join user on chars.besitzer = user.id where nickname like \''.$letter.'%\' and bild != \'design/bilder/avatare/noavart.gif\' and bild != \'\''));
		}
		$total = $total['anzahl'];
		$users = array();
		while($row = mysqli_fetch_assoc($auser)){
//			echo 'User: '.$row['id'].'<br>';
			$users[] = $row['id'];
		}

		echo '<td'.$colspan.'><table>'.displayPagelinksNew(10, $total, $begin, '<a href="'.$_SERVER['PHP_SELF'].'?choose=bilder&view=ByUser&letter='.$letter.'&begin=###PAGE###">###LABEL###</a>').'</td>';
		$test = 'SELECT name, nickname, bild FROM chars inner join user on chars.besitzer = user.id where besitzer IN ('.join(',', $users).') and bild != \'design/bilder/avatare/noavart.gif\' and bild != \'\' group by besitzer, bild order by nickname';
		$qry = db_query($test);
		$actual = '';
		$i = 0;
		while($result = mysqli_fetch_assoc($qry)){
			if($actual == '' || $result['nickname'] != $actual){
				// Fuelle die Leeren Felder aus
				for(;$i<8;$i++){
					echo '<td></td>';
				}
				echo '</tr>';
				$actual = $result['nickname'];
				echo '<tr><td>'.$result['nickname'].'</td>';
			}
			echo '<td><a href="gm_seite.php?choose=bilder&view=SinglePic&bildurl='.urlencode($result['bild']).'">'.$result['name'].'<br><img src="'.$result['bild'].'" width="75" height="75"></a></td>';
			$i++;
		}
		echo '</tr>';
		
	} else if($view == 'SinglePic'){
		if($bildurl !== NULL && $sure == 'yes'){
			echo '<tr><td>Bild wurde zur&uuml;ckgesetzt</td>';
			echo '<td><a href="javascript:history.go(-2);">Zur&uuml;ck</a></td></tr>';
			db_query('Update chars SET bild=\'\' where bild = \''.urldecode($bildurl).'\' ');
		} else if($bildurl !== NULL){
			$colspan = ' colspan=2';
			$qry = db_query('SELECT name, nickname, bild FROM chars inner join user on chars.besitzer = user.id where bild=\''.urldecode($bildurl).'\'');
			echo '<tr><td'.$colspan.' align="center">Wollen sie dieses Bild wirklich l&ouml;schen?</td></tr>';
			echo '<tr><td'.$colspan.' align="center"><img src="'.$bildurl.'" width="75" height="75"></td></tr>';
			echo '<tr><td>Betroffener User</td><td>Betroffener Char</td></tr>';
			while($row = mysqli_fetch_assoc($qry)) {
				echo '<tr><td>'.$row['nickname'].'</td><td>'.$row['name'].'</td></tr>';
			}			
			echo '<tr><td><a href="gm_seite.php?choose=bilder&view=SinglePic&bildurl='.urlencode($bildurl).'&sure=yes">ja</a></td><td><a href="javascript:history.back();">nein</a></td></tr>';
			return;
		}
	}
	
	echo '<tr><td'.$colspan.'><a href="'.$_SERVER['PHP_SELF'].'?choose=bilder&view=ByDate&date_from='.$date_from.'&date_until='.$date_until.'">Nach URL-Aktualisierungsdatum darstellen</a></td></tr>';
	echo '<tr><td'.$colspan.'><a href="'.$_SERVER['PHP_SELF'].'?choose=bilder&view=ByUser&letter='.$letter.'">Sortiert nach Nutzername darstellen</a></td></tr>';
	echo '<tr><td'.$colspan.'><a href="'.$_SERVER['PHP_SELF'].'?choose=bilder&view=Random">Zufallsanzeige</a></td></tr>';
	echo '<tr><td'.$colspan.'><a href="'.$_SERVER['PHP_SELF'].'">Zurueck zum Hauptmenu</a></td></tr>';
	echo '</table>';
}
?>