<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/arena.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/exp.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$arena_name = validateName($_GET['arena_name']);
$steh = validateUnsignedInteger($_GET['steh'],null);
$sitz = validateUnsignedInteger($_GET['sitz'],null);
$loge = validateUnsignedInteger($_GET['loge'],null);
$luxus = validateUnsignedInteger($_GET['luxus'],null);
$hausmeister = validateUnsignedInteger($_GET['hausmeister'],null);
$charmax = validateUnsignedInteger($_GET['char_max'],null);

// Unkritisch
$user = $user_ida;
$action = $_GET['action'];
$confirm = $_GET['confirm'];

?>
<SCRIPT language="JavaScript">
	function showgamess(){
		var Info = window.open("kampf_list.php","","status=no,hotkeys=no,Height=600,Width=820,scrollbars=yes");
	}
</SCRIPT>
<?php

function displayArena($arena){
	$unterhalt = calculateUnterhaltsKosten($arena);
?>
<form action=<?php echo $_SERVER['PHP_SELF']; ?> method="get">
	<input type="hidden" name="as" value="arena">
	<input type="hidden" name="action" value="miete">
	<table cellpadding="0" cellspacing="0" width="100%" height="48">
		<tr>
			<th valign="top" colspan="2" height="23" align="center"><?php echo $arena['name']; ?></th>
		</tr>
		<tr>
			<th valign="top" width="117" align="center">Stehpl&auml;tze:</th>
			<td height="25" width="373" valign="top"><?php echo $arena['steh']; ?></td>
		</tr>
		<tr>
			<th valign="top" width="117" align="center">Sitzpl&auml;tze:</th>
			<td height="25" width="373" valign="top"><?php echo $arena['sitz']; ?></td>
		</tr>
		<tr>
			<th valign="top" width="117" align="center">Logenpl&auml;tze:</th>
			<td height="25" width="373" valign="top"><?php echo $arena['loge']; ?></td>
		</tr>
		<tr>
			<th valign="top" width="117" align="center">Arena Level:</th>
			<td height="25" width="373" valign="top"><?php echo $arena['level']; ?></td>
		</tr>
		<tr>
			<th valign="top" width="117" align="center">Umsatz heute:</th>
			<td height="25" width="373" valign="top"><?php echo displayMoney($arena['einnahmen']); ?></td>
		</tr>
		<tr>
			<th valign="top" width="117" align="center">Exp:</th>
			<td height="25" width="373" valign="top"><?php echo join(' / ',preg_split('#,#',$arena['exp'])); ?></td>
		</tr>
		<tr>
			<th valign="top" width="117" align="center">Zustand:</th>
			<?php
				if($arena['zustand'] != 1.0){
// javascript:if(confirm('Hecht-Sensei%20Wirklich%20l%C3%B6schen?')==true)%20window.location.href='index.php?as=char_index&charm=3&char_id=19260'
					$kosten = 12345;
					echo '<td height="25" width="373" valign="top"><a href="javascript:if(confirm(\'Arena wirklich f&uuml;r '.displayMoney(calculateRenovierungsKosten($arena)).' renovieren lassen?\')==true) window.location.href=\'index.php?as=arena&action=renovieren\'">'.(round($arena['zustand']*100,2)).'%</a></td>';
				} else{
					echo '<td height="25" width="373" valign="top">100%</td>';
				}
			?>
		</tr>
		<tr>
			<th valign="top" width="117" align="center">Upgrade</th>
			<?php
				if($unterhalt == 0){
					echo '			<td height="25" width="373" valign="top"><a href="index.php?as=arena&action=skill1">Skill Points</a> - <a href="index.php?as=arena&action=ausbau1">Arena Sizer</a></td>';
				} else{
					echo '			<td height="25" width="373" valign="top"><a href="index.php?as=arena&action=skill1">Skill Points</a> - Arena Sizer</td>';
				}
			?>
		</tr>
		<tr>
			<th valign="top" width="117" align="center">K&auml;mpfen</th>
			<td height="25" width="373" valign="top"><a href='javascript:showgamess()'>Kampf list</a></td>
		</tr>
		<tr>
			<td valign="top" width="117" align="center"></td>
			<?php
				if($unterhalt == 0){
					echo '<td height="25" width="373" valign="top"><input id="input" type="submit" value="Umsatzsteuer schon bezahlt!" disabled></td>';
				} else{
					echo '<td height="25" width="373" valign="top"><input id="input" type="submit" value="Umsatzsteuer bezahlen ('.displayMoney($unterhalt).')!" ></td>';
				}
			?>
		</tr>
	</table>
</form>
<?php
}

function displayAusbau($arena){
	$unterhalt = calculateUnterhaltsKosten($arena);
	if($unterhalt != 0){
		displayErrorMessage(NULL, 'Bezahle erstmal deine Schulden!', displayHistoryBackLink());
		return;
	}
?>
<form action=<?php echo $_SERVER['PHP_SELF']; ?> method="get">
	<input type="hidden" name="as" value="arena">
	<input type="hidden" name="action" value="ausbau2">
	<table cellpadding="0" cellspacing="0" width="100%" height="48">
		<tr>
			<th valign="top" colspan="2" height="23" align="center"><?php echo $arena['name']; ?></th>
		</tr>
		<tr>
			<th valign="top" width="117" align="center">Name:</th>
			<td height="25" width="373" valign="top"><input name="arena_name" class="input" value="<?php echo $arena['name']; ?>" /></td>
		</tr>
		<tr>
			<th valign="top" width="117" align="center">Stehpl&auml;tze:</th>
			<td height="25" width="373" valign="top"><input name="steh" class="input" size="4" value="<?php echo $arena['steh']; ?>" /> (<?php echo displayMoney(12);?> Kosten)</td>
		</tr>
		<tr>
			<th valign="top" width="117" align="center">Sitzpl&auml;tze:</th>
			<td height="25" width="373" valign="top"><input name="sitz" class="input" size="4" value="<?php echo $arena['sitz']; ?>" /> (<?php echo displayMoney(180);?> Kosten)</td>
		</tr>
		<tr>
			<th valign="top" width="117" align="center">Logenpl&auml;tze:</th>
			<td height="25" width="373" valign="top"><input name="loge" class="input" size="4" value="<?php echo $arena['loge']; ?>" /> (<?php echo displayMoney(21600);?> Kosten)</td>
		</tr>
		<tr>
			<td valign="top" width="117" align="center"></td>
			<td height="25" width="373" valign="top"><input id="input" type="submit" value="ausbauen/abreissen" name="submit"></td>
		</tr>
	</table>
</form>
<?php
}

function displayAusbau2($user, $arena, $arena_name, $steh, $sitz, $loge, $vermoegen, $confirm){
	$unterhalt = calculateUnterhaltsKosten($arena);
	if($unterhalt != 0){
		displayErrorMessage(NULL, 'Bezahle erstmal deine Schulden!', displayHistoryBackLink());
		return;
	}
	if($confirm == 1){
		$gesamtkosten = 0;
		if($arena['steh'] < $steh){
			$gesamtkosten += ($steh-$arena['steh'])*12;
		}
		if($arena['sitz'] < $sitz){
			$gesamtkosten += ($sitz-$arena['sitz'])*180;
		}
		if($arena['loge'] < $loge){
			$gesamtkosten += ($loge-$arena['loge'])*21600;
		}
		if($gesamtkosten > $vermoegen){
			displayErrorMessage(NULL, 'Nicht genug Geld f&uuml;r den Umbau!', displayHistoryBackLink());
		} else{
			$sql = 'UPDATE arena set name = \''.$arena_name.'\', steh = '.$steh.', sitz = '.$sitz.', loge = '.$loge.' WHERE id = '.$arena['id'];
//			echo $sql;
			db_query($sql);
			if(db_affected_rows() > 0){
				db_query('UPDATE user set geld = geld - '.$gesamtkosten.' where id = '.$user['id']);
				displayErrorMessage(NULL, 'Umbau durchgef&uuml;hrt!', '<a href="'.$_SERVER['PHP_SELF'].'?as=arena">weiter</a>');
			} else{
				displayErrorMessage(NULL, 'Umbau konnte nicht durchgef&uuml;hrt werden!', displayHistoryBackLink());
			}

		}
	} else{
		$changelog = array();
		$abbreissen = array();
		$aufbauen = array();
		$gesamtkosten = 0;
		if($arena['name'] != $arena_name){
			$arena_name = validateName($arena_name);
			$changelog[] = 'Den Namen wirklich zu "'.$arena_name.'" &auml;ndern?';
		}
		if($arena['steh'] > $steh){
			$abbreissen[] = ($arena['steh']-$steh).' Stehpl&auml;tze';
		} else if($arena['steh'] < $steh){
			$aufbauen[] = ($steh-$arena['steh']).' Stehpl&auml;tze';
			$gesamtkosten += ($steh-$arena['steh'])*12;
		}
		if($arena['sitz'] > $sitz){
			$abbreissen[] = ($arena['sitz']-$sitz).' Sitzpl&auml;tze';
		} else if($arena['sitz'] < $sitz){
			$aufbauen[] = ($sitz-$arena['sitz']).' Sitzpl&auml;tze';
			$gesamtkosten += ($sitz-$arena['sitz'])*180;
		}
		if($arena['loge'] > $loge){
			$abbreissen[] = ($arena['loge']-$loge).' Logenpl&auml;tze';
		} else if($arena['loge'] < $loge){
			$aufbauen[] = ($loge-$arena['loge']).' Logenpl&auml;tze';
			$gesamtkosten += ($loge-$arena['loge'])*21600;
		}

		if(count($abbreissen) != 0){
			$changelog[] = join(', ',$abbreissen).' wirklich abreissen?';
		}
		if($gesamtkosten > 0){
			$changelog[] = join(', ',$aufbauen).' wirklich f&uuml;r '.displayMoney($gesamtkosten).' ausbauen?';
		}

		if(count($changelog) != 0){
			echo join('<br>',$changelog).'<br>';
			echo '<a href="'.$_SERVER['PHP_SELF'].'?as=arena&action=ausbau2&confirm=1&arena_name='.$arena_name.'&steh='.$steh.'&sitz='.$sitz.'&loge='.$loge.'">ja</a> | '.displayHistoryBackLink();
		} else{
			echo displayErrorMessage(NULL,'Nix zu tun!!', displayHistoryBackLink());
		}
	}
}

function displaySkillen($user, $arena, $charmax, $hausmeister, $luxus){
if($charmax !== NULL || $hausmeister !== NULL || $luxus !== NULL){
	$cm = abs($user['char_max']-$charmax);
	if($user['char_max'] + $cm > 7){
		displayErrorMessage(NULL, 'Nicht mehr als 7 Chars m&ouml;glich!', displayHistoryBackLink());
		return;
	}
	$hm = abs($arena['hausmeister']-$hausmeister);
	if($arena['hausmeister'] + $hm > 10){
		displayErrorMessage(NULL, 'Nicht mehr als 10 Finanzberater m&ouml;glich!', displayHistoryBackLink());
		return;
	}
	$lu = abs($arena['luxus']-$luxus);
	if($arena['luxus'] + $lu > 10){
		displayErrorMessage(NULL, 'Nicht mehr als 10 Punkte bei Luxus m&ouml;glich!', displayHistoryBackLink());
		return;
	}

	//	echo $cm.' '.$hm.' '.$lu.'<br>';
	if($arena['skill'] < $cm + $hm + $lu){
		displayErrorMessage(NULL, 'Nicht genug Skill-Points!', displayHistoryBackLink());
	} else{
		$n_skill = $arena['skill'] - ($cm + $hm + $lu);
		$sql = 'Update arena SET skill = '.$n_skill.', hausmeister = '.($arena['hausmeister']+$hm).', luxus = '.($arena['luxus']+$lu).' WHERE id = '.$arena['id'];
		db_query($sql);
		if($cm != 0){
			db_query('Update user SET char_max = '.($user['char_max']+$cm).' WHERE id = '.$user['id']);
		}
		displayErrorMessage(NULL, 'Die Skill-Points wurden erfolgreich verteilt!', '<a href="'.$_SERVER['PHP_SELF'].'?as=arena&skill1">weiter</a>');
	}
	return;
}
?>
<form action=<?php echo $_SERVER['PHP_SELF']; ?> method="get">
	<input type="hidden" name="as" value="arena">
	<input type="hidden" name="action" value="skill1">
	<table cellpadding="0" cellspacing="0" width="100%" height="48">
		<tr>
			<th valign="top" colspan="4" height="23" align="center"><?php echo $arena['name']; ?></th>
		</tr>
		<tr>
			<th valign="top" width="100" align="center">Skillpoints:</th>
			<td height="25" valign="top"><?php echo $arena['skill']; ?></td>
			<td width="50"></td>
			<td></td>
		</tr>
		<tr>
			<th valign="top" align="center">Maximale Chars:</th>
			<td height="25" valign="top"><input name="char_max" class="input" size="1" value="<?php echo $user['char_max']; ?>" /></td>
			<td>Max: 7</td>
			<td>Mehr Chars</td>
		</tr>
		<tr>
			<th valign="top" align="center">Luxus:</th>
			<td height="25" valign="top"><input name="luxus" class="input" size="1" value="<?php echo $arena['luxus']; ?>" /></td>
			<td>Max: 10</td>
			<td>Mehr Zuschauer</td>
		</tr>
		<tr>
			<th valign="top" align="center">Finanzberater:</th>
			<td height="25" valign="top"><input name="hausmeister" class="input" size="1" value="<?php echo $arena['hausmeister']; ?>" /></td>
			<td>Max: 10</td>
			<td>Erhöht Steuerfreibetrag, Senkt Steuersatz</td>
		</tr>
		<tr>
			<td valign="top" align="center"></td>
			<td height="25" valign="top"><input id="input" type="submit" value="skillen" name="submit"></td>
			<td></td>
			<td></td>
		</tr>
	</table>
</form>
<?php
}



function renovate($user, $arena, $vermoegen){
	$costs = calculateRenovierungsKosten($arena);
	if($costs > $vermoegen){
		displayErrorMessage(NULL, 'Nicht genug Geld f&uuml;r die Renovierung!', displayHistoryBackLink());
	} else{
		db_query('Update user set geld = geld - '.$costs.' WHERE id = '.$user['id']);
		db_query('Update arena set zustand = 1 WHERE id = '.$arena['id']);
		displayErrorMessage(NULL, 'Renovierung durchgef&uuml;hrt!', displayHistoryBackLink());
	}
}


$arena = getArena($user_ida['id']);
$user_geld = getRelevantMoney($user_ida['id']);

$unterhalt = calculateUnterhaltsKosten($arena);

if($action == 'miete'){
	// Zahle Miete
	if($user_geld < $unterhalt){
		displayErrorMessage(NULL, 'Du hast nicht genug Geld um die Unterhaltskosten zu bezahlen!', displayHistoryBackLink());
	} else{
		db_query('Update user set geld = geld - '.$unterhalt.' WHERE id = '.$user_ida['id']);
		db_query('Update arena set mietlasten = 0, steuerlasten = 0 WHERE id = '.$arena['id']);
		$arena = mysqli_fetch_assoc(db_query('SELECT * FROM arena where besitzer = \''.$user_ida['id'].'\''));
		displayArena($arena);
	}
} else if($action == 'ausbau1'){
	displayAusbau($arena);
} else if($action == 'ausbau2'){
	displayAusbau2($user, $arena, $arena_name, $steh, $sitz, $loge, $user_geld, $confirm);
} else if($action == 'skill1'){
	displaySkillen($user, $arena, $charmax, $hausmeister, $luxus);
} else if($action == 'renovieren'){
	renovate($user, $arena, $user_geld);
}else{
	displayArena($arena);
}

