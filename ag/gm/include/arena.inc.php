<?php

include_once(ROOT_PATH.'/include/defines.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');

defineIfNotDefined('TAX_NOOB_MIN_TAX', 0.5, true);
defineIfNotDefined('TAX_NOOB_MAX_LEVEL', 10, true);
defineIfNotDefined('TAX_START_TAX', 0.85, true);
defineIfNotDefined('TAX_REDUCTION', 0.05, true);
defineIfNotDefined('TAX_FREE_START', 250000, true);
defineIfNotDefined('TAX_FREE_X', 50000, true);
defineIfNotDefined('TAX_FREE_Y', 17500, true);

function displayPercent($value) {
	return ($value * 100).'%';
}

function displayArena($request) {
	if(isset($request['TAX_NOOB_MIN_TAX'])) {
		$keys = array('TAX_NOOB_MIN_TAX', 'TAX_NOOB_MAX_LEVEL', 'TAX_START_TAX', 'TAX_REDUCTION',
				'TAX_FREE_START', 'TAX_FREE_X', 'TAX_FREE_Y');
		foreach($keys as $key) {
			setDefine($key, $request[$key]);
		}
		echo '<a href="">weiter</a>';
		return;
	}
	?>
		<form action="" method="POST">
			<input name="choose" value="arena" type="hidden"></input>
			<table width="100%" border="1">
				<tr>
					<th colspan="4">
						Arena Einstellungsübersicht
					</th>
				</tr>
				<tr>
					<th align="center" colspan="4">Freibetrag</th>
				</tr>
				<tr>
					<td width="200px">Startwert</td>
					<td width="100px"><?php echo displayMoney(TAX_FREE_START); ?></td>
					<td width="100px"><input name="TAX_FREE_START" value="<?php echo TAX_FREE_START; ?>" /></td>
					<td rowspan="3">
					<?php
						for($i=0;$i<=10;$i++) {
							echo $i.' Finanzberater: '.displayMoney(TAX_FREE_START + TAX_FREE_X * $i + TAX_FREE_Y * $i*$i).'<br>';
						}
					?>
					</td>
				</tr>
				<tr>
					<td>X pro Berater</td>
					<td><?php echo displayCount(TAX_FREE_X); ?></td>
					<td width="100px"><input name="TAX_FREE_X" value="<?php echo TAX_FREE_X; ?>" /></td>
				</tr>
				<tr>
					<td>Y pro Berater</td>
					<td><?php echo displayCount(TAX_FREE_Y); ?></td>
					<td width="100px"><input name="TAX_FREE_Y" value="<?php echo TAX_FREE_Y; ?>" /></td>
				</tr>
				<tr>
					<th align="center" colspan="4">Steuersatz</th>
				</tr>
				<tr>
					<td>Startwert</td>
					<td><?php echo displayPercent(TAX_START_TAX); ?></td>
					<td width="100px"><input name="TAX_START_TAX" value="<?php echo TAX_START_TAX; ?>" /></td>
					<td rowspan="3">
					<?php
						for($i=0;$i<=10;$i++) {
							echo $i.' Finanzberater: '.displayPercent(TAX_START_TAX - TAX_REDUCTION * $i).'<br>';
						}
					?>
					</td>
				<tr>
				</tr>
					<td>Rabatt pro Berater</td>
					<td><?php echo displayPercent(TAX_REDUCTION); ?></td>
					<td width="100px"><input name="TAX_REDUCTION" value="<?php echo TAX_REDUCTION; ?>" /></td>
				</tr>
				<tr>
				
					<th align="center" colspan="4">Anfängerhilfe</th>
				</tr>
				<tr>
					<td>Mindest Steuer</td>
					<td><?php echo displayPercent(TAX_NOOB_MIN_TAX); ?></td>
					<td width="100px"><input name="TAX_NOOB_MIN_TAX" value="<?php echo TAX_NOOB_MIN_TAX; ?>" /></td>
					<td rowspan="2">
					<?php
						for($i=1;$i<=TAX_NOOB_MAX_LEVEL;$i++) {
							echo $i.' Arena-Level: '.displayPercent(max(TAX_NOOB_MIN_TAX, min(1, $i / TAX_NOOB_MAX_LEVEL))).'<br>';
						}
					?>
					</td>
				</tr>
				<tr>
					<td>Maximales Arenalevel</td>
					<td><?php echo TAX_NOOB_MAX_LEVEL; ?></td>
					<td width="100px"><input name="TAX_NOOB_MAX_LEVEL" value="<?php echo TAX_NOOB_MAX_LEVEL; ?>" /></td>
				</tr>
				<tr>
					<th colspan="4">Beispiele</th>
				</tr>
				<tr>
					<td align="center" colspan="4"><input type="submit" value="&Auml;nderungen &uuml;bernehmen" /></td>
				</tr>
				<tr>
					<td colspan="4">
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
					</td>
				</tr>
			</table>
		</form>
<?php 
}
