<?php
/*
 * Created on 28.08.2017
 *
 * @copyright (c) 2011 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

// 1 => Text (will be rendered on display)

function displayTutorial($request) { 
	if (isset($request['data'])) {
		$content = encodeNoHTMLNoBB($request['data']);
		db_query('UPDATE tutorial SET content = "'.$content.'" WHERE id = 1');
		if (db_affected_rows() == 0) {
			silent_query('INSERT INTO tutorial(id, content) value(1, "'.$content.'")');
		}
	}
	$content = mysqli_fetch_row(db_query('SELECT content FROM tutorial WHERE id = 1'))[0];
?>

<form method="POST" action="">
	<p><textarea name="data" style="width: 90%;height: 80%"><?php echo $content; ?></textarea></p>

	<p><input type="submit" value="&auml;ndern" /></p>
	<p><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a></p>

</form>

<?php
}

