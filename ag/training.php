<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once('path.inc.php'); // get the path ;)
include_once (ROOT_PATH . '/include/config.inc.php');
include_once (ROOT_PATH . '/include/char.inc.php');
include_once (ROOT_PATH . '/include/fehlerausgabe.inc.php');
include_once (ROOT_PATH . '/include/parse.inc.php');
include_once (ROOT_PATH . '/include/designfunctions.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$char_id = validateUnsignedInteger($_GET['char_id'], null);
$train = validateStringArray($_GET['train']);

// Unkritisch
$charm = $_GET['charm'];

// Das $train-array muss schon vorher ueberprueft werden!!
function updateChar($user, $char_id, $train) {
	$char_1 = getChar($char_id);
	if ($char_1['besitzer'] != $user['id']) {
		displayErrorMessage(NULL, 'Charakter geh&ouml;rt nicht dir!!', displayHistoryBackLink());
		return;
	} else
		if (!is_numeric($char_id)) {
			displayErrorMessage(NULL, 'Charakter-ID ist keine Zahl!!', displayHistoryBackLink());
			return;
		}
	$sql = 'UPDATE chars SET training=\'' . join($train, ',') . '\' WHERE id=' . $char_id . ' LIMIT 1';
	//	echo $sql.'<br>';
	db_query($sql);
	displayTraining($user, $char_id); // Ueberspringe die Besitzerueberpruefung
}

function displayTraining($user, $char_id) {
	$char_1 = getChar($char_id, false);
	if ($char_1['besitzer'] != $user['id']) {
		displayErrorMessage(NULL, 'Charakter geh&ouml;rt nicht dir!!', displayHistoryBackLink());
		return;
	} else
		if (!is_numeric($char_id)) {
			displayErrorMessage(NULL, 'Charakter-ID ist keine Zahl!!', displayHistoryBackLink());
			return;
		}
	$x = 0;
?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
		<input type="hidden" name="as" value="training">
		<input type="hidden" name="charm" value="1">
		<input type="hidden" name="char_id" value="<?php echo $char_id; ?>">
		<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%" height="25">
			<tr>
				<th colspan="2" align="center">
					Charaktertraining
				</th>
			</tr>
<?php


	$ui = 0;
	$lo = 0;
	$trains = explode(',', $char_1['training']);
	while ($ui < 12) {
		$lo1 = $lo +2;
?>
			<tr>
				<th align="center">
					<?php echo $lo.'-'.$lo1.' Uhr'; ?>
				</th>
				<td>
<?php


		echo '<select id="input" name="train[]">';
		if ($trains[$ui] == 'ausruhen') {
			$auswah0[$ui] = 'selected';
		}
		elseif ($trains[$ui] == 'starke') {
			$auswah1[$ui] = 'selected';
		}
		elseif ($trains[$ui] == 'verteidigung') {
			$auswah2[$ui] = 'selected';
		}
		elseif ($trains[$ui] == 'speed') {
			$auswah3[$ui] = 'selected';
		}
		elseif ($trains[$ui] == 'hp') {
			$auswah4[$ui] = 'selected';
		}
		elseif ($trains[$ui] == 'mp') {
			$auswah5[$ui] = 'selected';
		}
		elseif ($trains[$ui] == 'ausdauer') {
			$auswah6[$ui] = 'selected';
		}
		elseif ($trains[$ui] == 'glueck') {
			$auswah7[$ui] = 'selected';
		}
		echo '<option value="ausruhen" ' . $auswah0[$ui] . '>Ausruhen</option>' .
		'<option value="starke" ' . $auswah1[$ui] . '>St&auml;rke</option>' .
		'<option value="verteidigung" ' . $auswah2[$ui] . '>Verteidigung</option>' .
		'<option value="speed" ' . $auswah3[$ui] . '>Geschwindigkeit</option>' .
		'<option value="hp" ' . $auswah4[$ui] . '>HP</option>' .
		'<option value="mp" ' . $auswah5[$ui] . '>MP</option>' .
		'<option value="ausdauer" ' . $auswah6[$ui] . '>Ausdauer</option>' .
		'<option value="glueck" ' . $auswah7[$ui] . '>Gl&uuml;ck</option>';
		$ui++;
		$lo = $lo +2;
?>
					</select>
				</td>
			</tr>
	<?php


	}
?>
			<tr>
				<td colspan="2" align="center">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td>
					&nbsp;
				</td>
				<td>
					<input id="input" type=submit value='speichern'>
				</td>
			</tr>
		</table>
<?php


}

if ($char_id == null) {
	include "char_index.php"; // Der Charindex muss ne Funktion werden!!!
} else
	if ($charm == 1) {
		updateChar($user_ida, $char_id, $train);
	} else {
		displayTraining($user_ida, $char_id);
	}
?>