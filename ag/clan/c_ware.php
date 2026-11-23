<?php
/*
 * Created on 07.03.2009
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');
include_once (ROOT_PATH . '/include/parse.inc.php');
include_once (ROOT_PATH . '/include/sqlwrapper.inc.php');
// GET-Section
// Kritisch (SQL-Injections)
$c_ware = validateUnsignedInteger($_GET['c_ware'], null);

//Unkritisch
$charm = $_GET['charm'];

if($charm == 1) {
	$chars = getCharsOfUser($user_ida['id']);

	foreach($chars as $char) {
		$c_ware = validateUnsignedInteger($_GET['c_ware_'.$char['id']], null);
		db_query('UPDATE chars SET clan_train='.$c_ware.' WHERE id='.$char['id'].' AND besitzer='.$user_ida['id']);
	}
}


?>
<html>


<body>
<form action="index.php" method="GET">
	<input type="hidden" name="as" value="clan/c_ware" />
	<input type="hidden" name="charm" value="1" />

<table cellpadding="0" cellspacing="0" width="100%" height="127">
	<!-- MSTableType="layout" -->
	<tr>
		<td valign="top" height="31" colspan="2">
		<p align="center"><b>Clan Items</b></td>
	</tr>
<?php 
	$chars = getCharsOfUser($user_ida['id'], false);
	foreach($chars as $char) {
		$clan_items = db_query("SELECT ci.name, ci.type, cw.id, 1/(1+exp(3-$char[level]/12)) * nutzung as nutzkosten FROM clan_ware cw LEFT JOIN clan_item ci ON(cw.item_id=ci.id) WHERE cw.clan='$user_ida[clan]'");
		
		?>
			<tr>
				<td height="25" width="260" valign="middle" align="center">
				Raum f&uuml;r <?php echo $char['name'] ; ?>
				</td>
				<td height="25" width="273" valign="middle">
				<select id="input" name="c_ware_<?php echo $char['id']; ?>">
					<option value="0">W&auml;hle Kampf Raum</option>
					<?php
					while($row = mysqli_fetch_array($clan_items)) {
						$save="";
						if($row['id'] == $char['clan_train']) {
							$save = 'Selected';
						}
						if($row['type'] == 'Raum') {
							echo "<option value='$row[id]' $save>$row[name] (".displayMoney($row['nutzkosten']).") </option>";
						}
					}
					?>
				</select>
				</td>
			</tr>
		<?php
	}
?>
	<tr>
		<td height="19" width="260" valign="top">&nbsp;</td>
		<td height="19" width="273">&nbsp;<input id="input" type=submit value="Speichern"></form></td>
	</tr>
</table>
</form>
</body>

</html>
