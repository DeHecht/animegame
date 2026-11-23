<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH.'/include/defines.inc.php');
include_once(ROOT_PATH.'/include/clan.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');
include_once(ROOT_PATH.'/include/user.inc.php');

defineIfNotDefined('DENY_CHALLENGE', 1);
defineIfNotDefined('WITHDRAW_CHALLENGE', 2);

//Get-Section
if(isset($_GET['action'])) {
	$action = $_GET['action'];
} else {
	$action = NULL;
}
$challangedclan_id = validateUnsignedInteger($_GET['challangedclan_id'], null);
$inquirychallenge_clanid = validateUnsignedInteger($_GET['clanid'], null);
$clan_challange_id = validateUnsignedInteger($_GET['challenge'], null);
$char_id = validateUnsignedInteger($_GET['char'], null);
$type = validateUnsignedInteger($_GET['type'], null);

//unkritisch
if($action == null) {
	$action = $_POST['action'];
}

if(isset($_POST['other_submit'])) {
	$slot = validateUnsignedInteger($_POST['oslot'], null);
	$char_id = validateUnsignedInteger($_POST['o_char'], null);
} else {
	if($char_id == null) {
		$char_id = validateUnsignedInteger($_POST['char'], null);
	}
	$slot = validateUnsignedInteger($_POST['slot'], null);
}

if($clan_challange_id == null) {
	$clan_challange_id = validateUnsignedInteger($_POST['challenge'], null);
}

function displayChallengeClan($user, $clan_id) {
	if($clan_id == -1 || $clan_id == NULL) {
		displayErrorMessage(NULL,'Es wurde kein Clan ausgewählt!', displayHistoryBackLink());
		return;
	}

	if($user['clan'] == $clan_id) {
		displayErrorMessage(NULL,'Man kann sich nicht selbst herausfordern!', displayHistoryBackLink());
		return;
	}
	//TODO: es sollte hier unbedingt ne Auswahl geben wieviel Leute nun kämpfen dürfen :)
	$result = challengeClan($user, $clan_id, MIN_MEMBERS);
	if ($result != NULL) {
		displayErrorMessage(NULL, $result, displayHistoryBackLink());
		return;
	} else {
		echo 'Clan wurde erfolgreich herausgefordert.<br><br>';
		echo '<a href="index.php?as=clan/kampf_info">weiter</a>';
	}
}

function displayChallengeAccepted($user, $clan_id) {
	if(!is_numeric($clan_id)) {
		displayErrorMessage(NULL, 'Wollte da jemand schummeln? ;)', displayHistoryBackLink());
		return;
	}
	acceptChallengeRequest($clan_id, $user);
	echo 'Herausforderung wurde angenommen!<br><br>';
	echo '<a href="index.php?as=clan/kampf_info">weiter</a>';
}

function displayDenyChallenge($clan_id, $type) {
	if(!is_numeric($clan_id)) {
		displayErrorMessage(NULL, 'Wollte da jemand schummeln? ;)', displayHistoryBackLink());
		return;
	}
	?>
<table>
	<tr>
	<?php if($type == DENY_CHALLENGE) {?>
		<th colspan="2">Herausforderung wirklich ablehnen?</th>
		<?php } else {?>
		<th colspan="2">Herausforderung wirklich zur&uuml;ckziehen?</th>
		<?php }?>
	</tr>
	<tr>
		<td align="center"><?php echo '<a href="index.php?as=clan/kampf_info&action=denychallenge&clanid='.$clan_id.'&type='.$type.'">ja</a>';?>
		</td>
		<td align="center"><?php echo '<a href="index.php?as=clan/kampf_info">nein</a>';?>
		</td>
	</tr>
</table>
		<?php
}

function denyChallenge($type, $clan_id, $user) {
	if($type == DENY_CHALLENGE) {
		$success = denyChallengeRequest($clan_id, $user);
	} elseif($type == WITHDRAW_CHALLENGE) {
		$success = cancelChallengeRequest($clan_id, $user);
	}

	if($success == null) {
		displayClanFightMenu($user);
	} else {
		displayErrorMessage(NULL, $success, displayHistoryBackLink());
		return;
	}
}

function displayShowChallenge($clan_challenge_id, $user) {
	$user_clan = getClan($user['clan']);
	$open_challenge_ids = getOpenChallengeIDs($user_clan['id']);

	if(!in_array($clan_challenge_id, $open_challenge_ids)) {
		displayClanFightMenu($user);
		return;
	}

	//initialisieren von wichtigen variablen
	$participating_clan_ids = getParticipatingClanIDs($clan_challenge_id);
	if($participating_clan_ids[0] != $user_clan['id']) {
		$other_clan = getClan($participating_clan_ids[0]);
	} else {
		$other_clan = getClan($participating_clan_ids[1]);
	}
	$clan_challenge = getChallenge($clan_challenge_id);
	$anzahl = $clan_challenge['anzahl_chars'];
	$user_clan_chars = getParticipatingCharIDs($clan_challenge_id, $user_clan['id']);
	$other_clan_chars = getParticipatingCharIDs($clan_challenge_id, $other_clan['id']);
	$user_chars = getCharsOfUser($user['id']);
	$authorized = isAuthorizedClanfightCoordinator($user['clan'], $user['id'], $clan_challenge_id);


	$slots = array();

	?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
	<input type="hidden" name="as" value=clan/kampf_info> <input
		type="hidden" name="action" value="joinchallenge"> <input
		type="hidden" name="challenge"
		value="<?php echo $clan_challenge_id;?>">
	<table>
		<tr>
			<th colspan="3"><?php echo $other_clan['clanname'];?></th>
		</tr>
		<?php
		for ($i = 1; $i <= $anzahl; $i++) {
			echo '<tr>';
			echo '<td><b>Slot '.$i.'</b></td>';
			if($other_clan_chars[$i] != null) {
				echo '<td align="center">besetzt</td>';
			} else {
				echo '<td align="center">frei</td>';
			}
			echo '</tr>';
		}
		?>
		<tr>
			<th colspan="3"><hr/></th>
		</tr>
		<tr>
			<th colspan="3"><?php echo $user_clan['clanname'];?></th>
		</tr>
		<?php
		for ($i = 1; $i <= $anzahl; $i++) {
			echo '<tr>';
			echo '<td><b>Slot '.$i.'</b></td>';
			if($user_clan_chars[$i] != null) {
				$char = getChar($user_clan_chars[$i]);
				$participant = getParticipatingChar($clan_challenge_id, $user_clan['id'], $user_clan_chars[$i]);
				$char_owner = getOwnerOfChar($char['id']);
				echo '<td align="center">'.generateCharLinkByID($char['id']);
				echo '('.generateUserLinkByID($char_owner['id'], FALSE).')</td>';
				if(isUserOwnerOf($user['id'], $char['id']) && $participant['forced'] == TRUE) {
					echo '<td align="center"><a href="index.php?as=clan/kampf_info&action=joinchallenge&challenge='.$clan_challenge_id.'&char='.$char['id'].'">ok</a> | <a href="index.php?as=clan/kampf_info&action=leavechallenge&challenge='.$clan_challenge_id.'&char='.$char['id'].'">abmelden</a></td>';
				} else if(isUserOwnerOf($user['id'], $char['id']) || $authorized) {
					echo '<td align="center"><a href="index.php?as=clan/kampf_info&action=leavechallenge&challenge='.$clan_challenge_id.'&char='.$char['id'].'">abmelden</a></td>';
				}
			} else {
				echo '<td align="center">frei</td>';
				$slots[] = $i;
			}
			echo '</tr>';
		}
		?>
		<tr>
			<th colspan="3"><hr /></th>
		</tr>
		<tr>
			<th colspan="3">Teilnahme</th>
		</tr>
		<tr>
			<th>Charakter:</th>
			<td align="center"><select id="input" name="char">
					<option value="-1">Charakter ausw&auml;hlen</option>
					<?php
					foreach($user_chars as $char){
						echo '<option value="'.$char['id'].'">'.$char['name'].'</option>';
					}
					?>
			</select>
			</td>
		</tr>
		<tr>
			<th>Slot:</th>
			<td align="center"><select id="input" name="slot">
					<option value="-1">Slot ausw&auml;hlen</option>
					<?php
					foreach($slots as $slot){
						echo '<option value="'.$slot.'">'.$slot.'</option>';
					}
					?>
			</select>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="3"><input id="input" name="submit" type="submit"
				value='anmelden'></td>
		</tr>
		<?php if($authorized) {
			$qry = db_query('SELECT user.id AS user_id, chars.id AS char_id, user.nickname AS user_name, chars.name AS char_name FROM user inner join chars on user.id = chars.besitzer where clan = '.$user['clan'].' and user.id NOT IN (Select besitzer from chars inner join clan_challenge_participants on chars.id = clan_challenge_participants.char_id WHERE clan_challenge_id = '.$clan_challenge_id.' and clan_id = '.$user['clan'].')');
			$users = array();
			while($row = mysqli_fetch_assoc($qry)) {
				if(!isset($users[$row['user_id']])){
					$users[$row['user_id']] = array('user_name' => $row['user_name']);
				}
				$users[$row['user_id']]['chars'][] = array('char_name' => $row['char_name'], 'char_id' => $row['char_id']);
			}

//			print_r($users);

			?>
		<tr>
			<th colspan="3"><hr /></th>
		</tr>
		<tr>
			<th colspan="3">Chars zuweisen von Offline Usern:</th>
		</tr>
		<tr>
			<th>User:</th>
			<td align="center">
			<script type="text/javascript">

			function switchDaSelect(){
				user_select = document.getElementsByName('o_user')[0];
				char_select = document.getElementsByName('o_char')[0];
//				alert(user_select.value);
				char_select.options.length = 1;

				<?php
				foreach($users as $user_id => $user_info){
					$first = TRUE;
					if($user_id != $user['id']) {
//						echo '<option value="'.$user_id.'">'.$user_info['user_name'].'</option>';
						echo 'if(user_select.value == '.$user_id.') {'. PHP_EOL;
						foreach ($user_info['chars'] as $char_info) {
							if($first) {
								echo 'char_select.options[char_select.options.length] = new Option(\''.$char_info['char_name'].'\', \''.$char_info['char_id'].'\', false, true);'. PHP_EOL;
								$first = !$first;
							} else {
								echo 'char_select.options[char_select.options.length] = new Option(\''.$char_info['char_name'].'\', \''.$char_info['char_id'].'\');'. PHP_EOL;
							}
						}
						echo '}'. PHP_EOL;
					}
				}

				?>

			}

			</script>
			<select id="input" name="o_user" onchange="javascript:switchDaSelect()">
					<option value="-1">User ausw&auml;hlen</option>
					<?php
					foreach($users as $user_id => $user_info){
						if($user_id != $user['id'] && !isUserOnline($user_id)) {
							echo '<option value="'.$user_id.'">'.$user_info['user_name'].'</option>';
						}
					}
					?>
			</select>
			</td>
		</tr>

		<tr>
			<th>Charakter:</th>
			<td align="center">
			<select id="input" name="o_char">
					<option value="-1">Charakter ausw&auml;hlen</option>
			</select>
			</td>
		</tr>
				<tr>
			<th>Slot:</th>
			<td align="center"><select id="input" name="oslot">
					<option value="-1">Slot ausw&auml;hlen</option>
					<?php
					foreach($slots as $slot){
						echo '<option value="'.$slot.'">'.$slot.'</option>';
					}
					?>
			</select>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="3"><input id="input" type="submit"
				name="other_submit" value='anmelden'></td>
		</tr>
		<?php } ?>
		<tr>
			<td align="center" colspan="3"><?php echo '<a href="index.php?as=clan/kampf_info">zur&uuml;ck</a>';?>
			</td>
		</tr>
	</table>
</form>
					<?php
}

function displayJoinChallenge($user, $clan_challenge_id, $char_id, $slot) {
	$success = joinChallenge($clan_challenge_id, $user, $char_id, $slot);
	if($success != null) {
		displayErrorMessage(NULL, $success, displayHistoryBackLink());
		return;
	} else {
		echo 'Der Charakter wurde erfoglreich angemeldet.<br><br>';
		echo '<a href="index.php?as=clan/kampf_info&action=showchallenge&challenge='.$clan_challenge_id.'">weiter</a>';
	}
}

function displayLeaveChallenge($user, $clan_challenge_id, $char_id) {
	$user_clan = getClan($user['clan']);
	$open_challenge_ids = getOpenChallengeIDs($user_clan['id']);

	if(!in_array($clan_challenge_id, $open_challenge_ids)) {
		displayErrorMessage(NULL, 'Wollte da jemand schummeln? ;)', displayHistoryBackLink());
		return;
	}

	$success = leaveChallenge($clan_challenge_id, $user, $char_id);
	if($success != null) {
		displayErrorMessage(NULL, $success, displayHistoryBackLink());
		return;
	} else {
		echo 'Der Charakter wurde erfoglreich abgemeldet.<br><br>';
		echo '<a href="index.php?as=clan/kampf_info&action=showchallenge&challenge='.$clan_challenge_id.'">weiter</a>';
	}
}

function displayCancelChallenge($user, $clan_challange_id) {
	if(!is_numeric($clan_challange_id)) {
		displayErrorMessage(NULL, 'Wollte da jemand schummeln? ;)', displayHistoryBackLink());
		return;
	}

	?>
<table>
	<tr>
		<th colspan="2">Clankampf wirklich abbrechen?</th>
	</tr>
	<tr>
		<td align="center"><?php echo '<a href="index.php?as=clan/kampf_info&action=cancelchallenge&challenge='.$clan_challange_id.'">ja</a>';?>
		</td>
		<td align="center"><?php echo '<a href="index.php?as=clan/kampf_info">nein</a>';?>
		</td>
	</tr>
</table>
	<?php
}

function cancelThisChallenge($user, $clan_challange_id) {
	if(!is_numeric($clan_challange_id)) {
		displayErrorMessage(NULL, 'Wollte da jemand schummeln? ;)', displayHistoryBackLink());
		return;
	}

	$success = cancelChallenge($clan_challange_id, $user);
	if($success != null) {
		displayErrorMessage(NULL, $success, displayHistoryBackLink());
		return;
	} else {
		displayClanFightMenu($user);
	}
}

function getFightType($id) {
	switch ($id) {
		case DAVY_BACK_FIGHT:
			return 'Davy Back';
		case SURVIVAL:
			return 'Survival';
		default:
			return 'unbekannt';
	}
}

function displayClanFightMenuWithClanLocked($user) {
	$user_clan = getClan($user['clan']);
	$active_clan_challange_ids = getActiveChallengeIDs($user_clan['id']);
	$old_challenge_ids = getHistoricalChallengeIDs($user_clan['id']);
	?>
<script>
	function showFight(challenge_id){
		window.open("./clan/kampf_anzeige.php?challenge="+challenge_id,"","status=no,hotkeys=no,Height=600,Width=929,scrollbars=yes");
	}
</script>
<div align="center">
	<table width="500" border="0">
		<tr>
			<th height="30" colspan="3" align="center">Herausforderungen</th>
		</tr>
		<tr>
			<th height="30" colspan="3" align="center">Dein Clan ist leider
				dezeit Blockiert ( <?php
				$time = getClanLockedTime($user['clan']);
				if($time >= 86400) { // 24*60*60
					echo 'Voraussetzungen nicht erf&uuml;llt!!';
				} else {
					echo generateTimerHtml('blubb', $time, '<a href=\'\'>Blockade beenden</a>');
				}
				?>)</th>
		</tr>
		<tr>
			<th height="30" colspan="3" align="center">Laufende Herausforderungen</th>
		</tr>
		<?php
		if(count($active_clan_challange_ids) == 0) {
			echo '<td height="20" colspan="3" align="center">Keine</td>';
		} else {
			foreach ($active_clan_challange_ids as $acci) {
				$participating_clan_ids = getParticipatingClanIDs($acci);
				foreach ($participating_clan_ids as $pci)
				if($pci != $user_clan['id']) {
					$challenged_clan = getClan($pci);
					echo '<tr><td align="center"> '.$challenged_clan['clanname'].'</td>';
					//TODO: hier muss javascript fenster aufgerufen werden
					echo '<td align="center"><a href="index.php?as=clan/kampf_info&action=showoldchallenge&challenge='.$acci.'">'.anzeigen.'</a></td></tr>';
				}
			}
		}
		?>
		<tr>
			<th height="30" colspan="5" align="center">Beendete Herausforderungen</th>
		</tr>
		<tr>
		<?php
		if(count($old_challenge_ids) == 0) {
			echo '<td height="20" colspan="5" align="center">Keine</td>';
		} else {
			foreach ($old_challenge_ids as $oci) {
				$participating_clan_ids = getParticipatingClanIDs($oci);
				foreach ($participating_clan_ids as $pci)
				if($pci != $user_clan['id']) {
					$challenged_clan = getClan($pci);
					echo '<tr><td align="center" colspan="2"> '.$challenged_clan['clanname'].'</td>';
					echo '<td align="center" colspan="3"><a href=\'javascript:showFight('.$oci.');\'>'.anzeigen.'</a></td></tr>';
				}
			}
		}
		?>
		</tr>
	</table>
</div>
		<?php
}

function displayClanFightMenu($user) {
	$user_clan = getClan($user['clan']);
	$clans_tmp = getClanFightCapableClanIds();
	foreach ($clans_tmp as $clan_tmp_id) {
		if($user['clan'] != $clan_tmp_id) {
			$clans[] = getClan($clan_tmp_id);
		}
	}
	$active_clan_challange_ids = getActiveChallengeIDs($user_clan['id']);
	$open_challenge_requests = getOpenChallengeRequests($user_clan['id']);
	$open_challenge_ids = getOpenChallengeIDs($user_clan['id']);
	$old_challenge_ids = getHistoricalChallengeIDs($user_clan['id']);

	$authorized = isAuthorizedClanfightCoordinator($user_clan['id'], $user['id']);

	?>
<script>
	function showFight(challenge_id){
		window.open("./clan/kampf_anzeige.php?challenge="+challenge_id,"","status=no,hotkeys=no,Height=600,Width=929,scrollbars=yes");
	}
</script>

<div align="center">
	<table width="500">
	<?php if($authorized) {?>
		<tr>
			<th height="30" colspan="5" align="center">Herausforderungen</th>
		</tr>
		<tr>
			<th align="Left" height="25" colspan="2">Clan herausfordern</th>
			<td colspan="3">
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
					<input type="hidden" name="as" value="clan/kampf_info"> <input
						type="hidden" name="action" value="challengeclan"> <select
						id="input" name="challangedclan_id">
						<option value="-1">Clan ausw&auml;hlen</option>
						<?php
						foreach($clans as $c) {
							echo '<option value="'.$c['id'].'">'.$c['clanname'].'</option>';
						}
						?>
					</select> <input class="input" type="submit" value="herausfordern" />
				</form>
			</td>
		</tr>
		<tr>
			<th align="center" height="25">Clan</th>
			<th align="center" height="25">Datum</th>
			<th align="center" height="25">Typ</th>
			<th align="center" height="25">Anzahl</th>
			<th align="center" height="25">Status</th>
		</tr>
		<tr>
		<?php
		if(count($open_challenge_requests) == 0) {
			echo '<td align ="center" colspan="5" height="20">Keine</td>';
		} else {
			foreach ($open_challenge_requests as $challenge) {
				$requester_clan = getClan($challenge['clan_requester']);
				$challenged_clan = getClan($challenge['clan_challenged']);

				//unterscheidung zwischen herausforder und herausgeforderter
				if($user_clan['id'] == $challenge['clan_requester']) {
					echo '<tr><td align ="center">'.$challenged_clan['clanname'].'</td><td align ="center">'.formatTimestamp($challenge['challenged_time']).'</td>';
					echo '<td align ="center">'.getFightType($challenge['type']).'</td><td align ="center">'.$challenge['anzahl_chars'].'</td>';
					echo '<td align ="center">ausstehend (<a href="index.php?as=clan/kampf_info&action=q_withdrawclanchallenge&clanid='.$challenged_clan['id'].'">zur&uuml;ckziehen</a>)</td></tr>';
				} else {
					echo '<tr><td align ="center">'.$requester_clan['clanname'].'</td><td align ="center">'.formatTimestamp($challenge['challenged_time']).'</td>';
					echo '<td align ="center">'.getFightType($challenge['type']).'</td><td align ="center">'.$challenge['anzahl_chars'].'</td>';
					echo '<td align ="center"><a href="index.php?as=clan/kampf_info&action=acceptclanchallenge&clanid='.$requester_clan['id'].'">annehmen</a> |';
					echo '<a href="index.php?as=clan/kampf_info&action=q_denyclanchallenge&clanid='.$requester_clan['id'].'">ablehnen</a></td></tr>';
				}
			}
		}
		?>
		</tr>
		<?php }?>
		<tr>
			<th height="30" colspan="5" align="center">offene Herausforderungen</th>
		</tr>
		<tr>
		<?php
		if(count($open_challenge_ids) == 0) {
			echo '<td height="20" colspan="5" align="center">Keine</td>';
		} else {
			foreach ($open_challenge_ids as $oci) {
				$participating_clan_ids = getParticipatingClanIDs($oci);
				foreach ($participating_clan_ids as $pci)
				if($pci != $user_clan['id']) {
					$challenged_clan = getClan($pci);
					echo '<tr>';
					echo '<td colspan="2"> '.$challenged_clan['clanname'].'</td>';
					echo '<td align="center" colspan="2"><a href="index.php?as=clan/kampf_info&action=showchallenge&challenge='.$oci.'">'.anzeigen.'</a></td>';
					if($authorized) {
						echo '<td align="center"><a href="index.php?as=clan/kampf_info&action=q_cancelclanchallenge&challenge='.$oci.'">'.abbrechen.'</a></td>';
					}
					echo '</tr>';
				}
			}
		}
		?>
		</tr>
		<tr>
			<th height="30" colspan="5" align="center">Laufende Herausforderungen</th>
		</tr>
		<?php
		if(count($active_clan_challange_ids) == 0) {
			echo '<td height="20" colspan="5" align="center">Keine</td>';
		} else {
			foreach ($active_clan_challange_ids as $acci) {
				$participating_clan_ids = getParticipatingClanIDs($acci);
				foreach ($participating_clan_ids as $pci)
				if($pci != $user_clan['id']) {
					$challenged_clan = getClan($pci);
					echo '<tr><td align="center" colspan="2"> '.$challenged_clan['clanname'].'</td>';
					echo '<td align="center" colspan="3"><a href=\'javascript:showFight('.$acci.');\'>'.anzeigen.'</a></td></tr>';
				}
			}
		}
		?>
		<tr>
			<th height="30" colspan="5" align="center">Beendete Herausforderungen</th>
		</tr>
		<tr>
		<?php
		$count = count($old_challenge_ids);
		if($count == 0) {
			echo '<td height="20" colspan="5" align="center">Keine</td>';
		} else {
			if($count > 20) {
				$max = 20;
			} else {
				$max = $count;
			}

			for ($i = 0; $i < $max; $i++) {
				$participating_clan_ids = getParticipatingClanIDs($old_challenge_ids[$i]);
				$old_challenge = getChallenge($old_challenge_ids[$i]);
				foreach ($participating_clan_ids as $pci)
				if($pci != $user_clan['id']) {
					$challenged_clan = getClan($pci);
					echo '<tr><td align="center"> '.$challenged_clan['clanname'].'</td>';
					echo '<td>' .formatTimestamp($old_challenge['date'] ).'</td>';
					echo '<td align="center" colspan="3"><a href=\'javascript:showFight('.$old_challenge_ids[$i].');\'>'.anzeigen.'</a></td></tr>';
				}
			}
		}
		?>
		</tr>
	</table>
</div>
		<?php
}


if(!isClanLocked($user_ida['clan'])) {
	switch($action) {
		case 'challengeclan':
			displayChallengeClan($user_ida, $challangedclan_id);
			break;
		case 'acceptclanchallenge':
			displayChallengeAccepted($user_ida, $inquirychallenge_clanid);
			break;
		case 'q_denyclanchallenge':
			displayDenyChallenge($inquirychallenge_clanid, DENY_CHALLENGE);
			break;
		case 'q_withdrawclanchallenge':
			displayDenyChallenge($inquirychallenge_clanid, WITHDRAW_CHALLENGE);
			break;
		case 'denychallenge':
			denyChallenge($type, $inquirychallenge_clanid, $user_ida);
			break;
		case 'showchallenge':
			displayShowChallenge($clan_challange_id, $user_ida);
			break;
		case 'joinchallenge':
			displayJoinChallenge($user_ida, $clan_challange_id, $char_id, $slot);
			break;
		case 'leavechallenge':
			displayLeaveChallenge($user_ida, $clan_challange_id, $char_id);
			break;
		case 'q_cancelclanchallenge':
			displayCancelChallenge($user_ida, $clan_challange_id);
			break;
		case 'cancelchallenge':
			cancelThisChallenge($user_ida, $clan_challange_id);
			break;
		default:
			displayClanFightMenu($user_ida);
	}
} else {
	switch($action) {
		default:
			displayClanFightMenuWithClanLocked($user_ida);
	}
}
?>
