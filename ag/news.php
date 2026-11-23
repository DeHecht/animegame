<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once('path.inc.php'); // get the path ;)
include_once (ROOT_PATH . '/include/config.inc.php');
include_once (ROOT_PATH . '/include/designfunctions.inc.php');
include_once (ROOT_PATH . '/include/parse.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$komment = validateString($_GET['komment']);
$news_id = validateUnsignedInteger($_GET['id'], null);
$pagenum = validateUnsignedInteger($_GET['pagenum'], null);

// Unkritisch
$charm = $_GET['charm'];
$username = $user_ida['nickname'];


function insertComment($username, $komment, $news_id) {
	$sql = 'INSERT ff11_komments SET user=\'' . $username . '\', text=\'' . encodeNoHTMLWithBB($komment) . '\', ip=\'' . $_SERVER['REMOTE_ADDR'] . '\', datum=CURRENT_DATE, zeit=CURRENT_TIME, news_id=' . $news_id;
	db_query($sql);
	//	echo $sql;
}

function showNewsKomments($userid, $news_id) {
	$nachricht = mysqli_fetch_assoc(db_query('SELECT * FROM ff11_news WHERE id=' . $news_id));
?>
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="35" valign="top" align="center"></td>
		</tr>

		<tr>
			<td id="content" height="57" valign="top"><b><img src="pictures/news.jpg"> Von:</b> <?php echo $nachricht['name'] ?><br><br><b>Datum:</b> <?php echo $nachricht['datum']; ?> um <?php echo $nachricht['zeit']; ?> Uhr<br><b>Betreff:</b> <?php echo $nachricht['betreff']; ?></td>
		</tr>
		<tr>
			<td height="1"></td>
		</tr>
		<tr>
			<td id="content" height="50" valign="top"><br><?php echo $nachricht['text']; ?><br></td>
		</tr>
		<tr>
			<td height="1"><br></td>
		</tr>

<?php

	$kommens = db_query('SELECT * FROM ff11_komments WHERE news_id=' . $news_id . ' order by id ASC');

	while ($row = mysqli_fetch_assoc($kommens)) {
?>
		<tr>
			<td id="content" height="15" valign="top"><b>Name:</b> <?php echo $row['user'].' schrieb am '.$row['datum'].' um '.$row['zeit'].' Uhr'; ?> <img src=pictures/komment.jpg></td>
		</tr>
		<tr>
			<td height="1" ></td>
		</tr>
		<tr>
			<td id="content" height="50" valign="top"><br><?php echo $row['text']; ?><br></td>
		</tr>
		<tr>
			<td height="1" ><br></td>
		</tr>
<?php

	}
	if ($userid != NULL) {
?>
		<tr>
			<td  height="50" align="center">
				<br>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
					<input type="hidden" name="as" value="news">
					<input type="hidden" name="id" value="<?php echo $news_id; ?>">
					<input type="hidden" name="charm" value="1">
					<textarea id="input"  name="komment" cols="65" rows="5"></textarea>
					<br><br>
					<input id="input" type="submit" value="Senden">
				</form>
			</td>
		</tr>
<?php

	}
?>
</table>
<?php

}
function displayNews($pagenum) {
?>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td id="content" align="center">&nbsp;</td>
	</tr>
<?php

	if (!is_numeric($pagenum) || $pagenum < 0) {
		$pagenum = 0;
	}

	$nachrichten = db_query('SELECT * FROM ff11_news order by id DESC LIMIT ' . ($pagenum*5) . ', 5');

	while ($row = mysqli_fetch_assoc($nachrichten)) {
		$komments = mysqli_fetch_assoc(db_query('SELECT count(id) as anzahl FROM ff11_komments WHERE news_id=' . $row['id']));
		$komments = $komments['anzahl'];
?>
	<tr>
		<td id="content" height="30" valign="top"><img src="pictures/news1.jpg"> <b>Von:</b> <?php echo $row['name']; ?> <b>am</b> <?php echo $row['datum']; ?> <b>um</b> <?php echo $row['zeit']; ?>Uhr<br><b>Betreff:</b> <?php echo $row['betreff']; ?></td>
	</tr>
	<tr>
		<td height="1"><br></td>
	</tr>
	<tr>
		<td id="content" height="50" valign="top"><br><?php echo $row['text'];  ?>
		<p><a href="index.php?as=news&id=<?php echo $row['id']; ?>" id="content">antworten:</a> (<?php echo $komments; ?>)</td>
	</tr>
	<tr>
		<td height="1"><br></td>
	</tr>


<?php

	}

	$total = mysqli_fetch_assoc(db_query('SELECT count(*) as anzahl FROM ff11_news'));
	$total = $total['anzahl'];
	$url = '<a href="index.php?as=news&pagenum=###PAGE###">###LABEL###</a>';
?>
	<tr>
		<td id="content" height="15"><?php echo displayPagelinksNew(5, $total, $pagenum, $url); ?></td>
	</tr>
</table>
<?php

}
// so nun die aufrufenden Funktionen noch einbinden!
if ($charm == 1 && $user_ida['nickname'] != NULL) {
	insertComment($username, $komment, $news_id);
	showNewsKomments($user_ida['id'], $news_id);
} else
	if ($news_id != NULL) {
		showNewsKomments($user_ida['id'], $news_id);
	} else {
		displayNews($pagenum);
	}
?>
