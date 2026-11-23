<?php
/*
 * Created on 28.08.2017
 *
 * @copyright (c) 2011 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

// 1 => Text (will be rendered on display)

function displayDefines($request) { 
	$defines = getDefines();

	if (isset($request['persist'])) {
		foreach ($defines as $define) {
			if ($request[$define['key']] != $define['value']) {
				setDefine($define['key'], $request[$define['key']]);
			}
		}
		$defines = getDefines();
	}
?>
<form method="POST" action="">
	<input name="persist" value="true" type="hidden" />
<table>
<?php
foreach ($defines as $define) {
?>
<tr>
	<th align="left"><?php echo $define['key']; ?></th>
	<td><input name="<?php echo $define['key']; ?>" value="<?php echo $define['value']; ?>" /></td>
</tr>
<?php
}
?>
</tr><td colspan="2"><input type="submit" value="&auml;ndern" /></td></tr>
<tr>
	<td colspan="3"><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
	</td>
</tr>
</table>
</form>
<?php
}
