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
include_once(ROOT_PATH.'//include/char.inc.php');

$char_id = validateUnsignedInteger($_REQUEST['char_id'], null);
$char = getChar($char_id);
?>

<table>
	<tr>
		<th colspan="2" height="50"><?php echo $char['name']; ?></th>
	</tr>
	<tr>
		<th colspan="2" height="50">Kampfitems</th>
	</tr>
	<tr>
		<th align="left">Helm</th>
		<td>
			<select><option>Nichts</option></select>
		</td>
	</tr>
	<tr>
		<th align="left">Rüstung</th>
		<td>
			<select><option>Nichts</option></select>
		</td>
	</tr>
	<tr>
		<th align="left">Schild</th>
		<td>
			<select><option>Nichts</option></select>
		</td>
	</tr>
	<tr>
		<th align="left">Schwert</th>
		<td>
			<select><option>Nichts</option></select>
		</td>
	</tr>
	<tr>
		<th align="left">Schuhe</th>
		<td>
			<select><option>Nichts</option></select>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<hr id="hrc">
		</td>
	</tr>
	<tr>
		<th colspan="2" height="50">Kampfeinstellungen</th>
	</tr>
	<tr>
		<th align="left">Arenakämpfe</th>
		<td>
			<select><option>A</option><option>B</option><option>C</option></select>
		</td>
	</tr>
	<tr>
		<th align="left">Schatzsuche</th>
		<td>
			<select><option>A</option><option>B</option><option>C</option></select>
		</td>
	</tr>
	<tr>
		<th align="left">Liga</th>
		<td>
			<select><option>A</option><option>B</option><option>C</option></select>
		</td>
	</tr>
	<tr>
		<th align="left">Turniere</th>
		<td>
			<select><option>A</option><option>B</option><option>C</option></select>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<a href="index.php?as=char_fight_settings">Kampfeinstellungen editieren</a>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<hr id="hrc">
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<input type="submit" value="Einstellungen Speichern" />
		</td>
	</tr>
</table>