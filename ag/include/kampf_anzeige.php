<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH.'//include/kampf_anzeige.inc.php');

?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="design/FightStyle.css">
	</head>
	<body>
		<table width="100%">
			<tr>
				<th class="top" colspan="3"><?php echo $char_1['name']; ?> Vs <?php echo $char_2['name']; ?></th>
			</tr>
			<tr>
				<th class="round" colspan="3">Start:</th>
			</tr>
			<tr>
				<td>
<?php
						displayCharTable($char_1['bild'], $char_1['name'], $starke1[0], $ver1[0], $speed1[0], $ausdauer1[0], $glueck1[0], $hp1[0], $a_hp1[1], $mp1[0], $a_mp1[1] );
?>
				</td>
				<td>&nbsp;</td>
				<td>
<?php
						displayCharTable($char_2['bild'], $char_2['name'], $starke2[0], $ver2[0], $speed2[0], $ausdauer2[0], $glueck2[0], $hp2[0], $a_hp2[1], $mp2[0], $a_mp2[1] );
?>
				</td>
			</tr>
<?php
$arenas_exps[0] += ($speed1[0] + $ausdauer1[0] + $starke1[0] + $ver1[0] + $speed2[0] + $ausdauer2[0] + $starke2[0] + $ver2[0]) / 10;
while ($runde < count($hp1)) {
?>

			<tr>
				<th class="round" colspan="3">Runde: <?php echo $runde; ?></th>
			</tr>
			<tr>
				<td>
<?php
				displayCharTable($char_1['bild'], $char_1['name'], $starke1[$runde], $ver1[$runde], $speed1[$runde], $ausdauer1[$runde], $glueck1[$runde], $hp1[$runde], $a_hp1[1], $mp1[$runde], $a_mp1[1] );
?>
				</td>
				<td width="400">
<?php
				displayAttacks($attacke1[$runde], $schaden1[$runde], $attacke2[$runde], $schaden2[$runde]);
?>
				</td>
				<td>
<?php
				displayCharTable($char_2['bild'], $char_2['name'], $starke2[$runde], $ver2[$runde], $speed2[$runde], $ausdauer2[$runde], $glueck2[$runde], $hp2[$runde], $a_hp2[1], $mp2[$runde], $a_mp2[1] );
?>
				</td>
			</tr>
<?php
	$runde++;
	if ($spleoic == 'turnier_kampf' OR $spleoic == 'wanted' OR $spleoic == 'liga' OR $spleoic == 'clan_fights') {
		if ($kampf1['win'] == $char_1['id']) {
			$char_s1 = 'Sieger';
			$char_s2 = 'Verlierer';
		} else {
			$char_s2 = 'Sieger';
			$char_s1 = 'Verlierer';
		}
	} else {
		if ($kampf1['win'] == $char_1['name']) {
			$char_s1 = 'Sieger';
			$char_s2 = 'Verlierer';
		} else {
			$char_s2 = 'Sieger';
			$char_s1 = 'Verlierer';
		}
	}
} // ENDE WHILE-SCHLEIFE

?>
			<tr>
				<td colspan="3">
					<table class="stylish" width="50%"">
						<tr>
							<td>
								<table width="100%">
									<tr>
										<td class="greenstats"><?php echo $char_s1; ?></td>
										<td class="bold"><?php echo $char_1['name']; ?></td>
										<td class="greenstats"><?php echo $char_s2; ?></td>
										<td class="bold"><?php echo $char_2['name']; ?></td>
									</tr>
									<?php
									if ($spleoic != 'clan_fights') {
									?>
									<tr>
										<td class="greenstats">Exp +</td>
										<td class="bold"><?php echo $kampf1['exp1']; ?></td>
										<td class="greenstats">Exp +</td>
										<td class="bold"><?php echo $kampf1['exp2']; ?></td>
									</tr>
									<tr>
										<td class="greenstats">Geld +</td>
										<td class="bold"><?php echo $kampf1['geld1']; ?></td>
										<td class="greenstats">Geld +</td>
										<td class="bold"><?php echo $kampf1['geld2']; ?></td>
									</tr>
									<tr>
										<td class="greenstats">Arena +</td>
										<td class="bold"><?php echo round($kampf1['arena_exp']*0.25); ?></td>
										<td class="greenstats">Arena +</td>
										<td class="bold"><?php echo $kampf1['arena_exp'] - round($kampf1['arena_exp']*0.25); ?></td>
									</tr>
									<?php
									}
									?>
								</table>
							</td>
						</tr>
					</table>
<?php
	if ($spleoic != 'turnier_kampf' AND $spleoic != 'wanted' AND $spleoic != 'liga' AND $spleoic != 'clan_fights') {
?>
					<table class="stylish" width="50%"">
						<tr>
							<td>
								<table width="100%">
									<tr>
										<td class="greenstats">Zuschauer</td>
										<td class="bold"><?php echo $kampf1['loge']+$kampf1['sitz']+$kampf1['steh']; ?></td>
									</tr>
									<tr>
										<td class="greenstats">Stehpl&auml;tze</td>
										<td class="bold"><?php echo $kampf1['steh']; ?></td>
									</tr>
									<tr>
										<td class="greenstats">Sitzpl&auml;tze</td>
										<td class="bold"><?php echo $kampf1['sitz']; ?></td>
									</tr>
									<tr>
										<td class="greenstats">Logenpl&auml;tze</td>
										<td class="bold"><?php echo $kampf1['loge']; ?></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
<?php
	}
?>
				</td>
			</tr>
		</table>
	</body>
</html>