<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once (ROOT_PATH . '/include/designfunctions.inc.php');
include_once (ROOT_PATH . '/include/fehlerausgabe.inc.php');
include_once (ROOT_PATH . '/include/parse.inc.php');
include_once (ROOT_PATH . '/include/char.inc.php');

$char_id = validateUnsignedInteger($_GET['char_id'], null);
$attack_id = validateUnsignedInteger($_REQUEST['attack_id'], null);

if ($char_id == null) {
	include (ROOT_PATH . '/char_index.php');
	exit;
} else {
	if (!isUserOwnerOf($user_ida['id'],$char_id)) {
		displayErrorMessage(NULL, 'Dieser Charakter geh&ouml;rt nicht dir.', displayHistoryBackLink());
		exit;
	}
	if ($attack_id != null) {
		$test = 'INSERT INTO lernen(name, at_id,besitzer,aktiv) VALUES'.
			'((SELECT name FROM attacken WHERE id ='.$attack_id.'), '.$attack_id.', '.$char_id.', 1);';
		$qry = db_query($test);
	}
	$attacks_learned = getAttacksforChar($char_id, 0);
	$attacks_unlearned = getAttacksforChar($char_id, 1);
	$testuser = isUserInGroup($usergroups, TESTER);

	?>
<form
	action=<?php echo $_SERVER['PHP_SELF'].'?as=info/lern&char_id='.$char_id; ?>
	method="POST">
	<table border="0" width="500" height="83">
		<tr>
			<td height="50" width="480" colspan="2">
				<p align="center">
					<b>Technik System</b>

			</td>
		</tr>
		<tr>
			<td width="100%">
				<table border="0" width="100%">
					<tr>
						<th width="30%">Attacke</th>
						<th width="10%">Level</th>
						<th width="10%">eingesetzt</th>
						<th width="10%">Anzahl</th>
						<th width="40%">benötigte Attacken</th>
						<?php if($testuser) {
							echo '<th widht="5%">lernen?</th>';
						}
						?>
					</tr>
					<?php
					foreach ($attacks_learned as $item) {
						?>
					<tr>
						<th align="left"><?php echo generateAttackLinkByID($item['id']); ?></th>
						<td align="center">-</td>
						<td align="center"><?php echo $item['benutzt'].'x'; ?>
						</td>
						<td align="center">-</td>
						<td>gelernt!</td>

					</tr>
					<?php
					}
					foreach ($attacks_unlearned as $item) {
						?>
					<tr>
						<th align="left"><?php echo generateAttackLinkByID($item['id']); ?></th>
						<td align="center"><?php if($item['levelups']) {
							echo $item['levelups'];
						} else
						echo '-';
						?>
						</td>
						<td align="center">0x</td>
						<td align="center"><?php if(isset($item['req'])) {
							foreach($item['req'] as $requirenment) {
								echo $requirenment['reggs'].'x<br>';
							}
						} else
						echo '-';
						?>
						</td>
						<td><?php if(isset($item['req'])) {
							foreach($item['req'] as $requirenment) {
								echo generateAttackLinkByID($requirenment['id']) .'<br>';
							}
						} else
						echo ' ';
						if($testuser) {
							echo '<td align="center">';
							echo '<input type="radio" value="'.$item['id'].'" name="attack_id">';
							echo '</td>';
						}
						?>
						</td>
					</tr>
					<?php };?>
				</table>
			</td>
		</tr>
		<?php
		if($testuser) {
			echo '<tr>';
			echo '<td><hr id="hrc"></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td align="center"><input id="input" size="15" type="submit" value="lernen"></td>';
			echo '</tr>';
		}
		?>
	</table>
</form>
		<?php }?>