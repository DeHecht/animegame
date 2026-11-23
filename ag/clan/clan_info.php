<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH.'/include/clan.inc.php');
include_once(ROOT_PATH.'/include/clan_chat.inc.php');
include_once(ROOT_PATH.'/include/clan_fights.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');

// GET-Section
if(isset($_GET['action'])) {
	$action = $_GET['action'];
} else {
	$action = NULL;
}

//Unkritisch
if(isset($_GET['what'])) {
	$what = $_GET['what'];
} else {
	$what = NULL;
}

// Kritisch (SQL-Injections)
$clan_id = validateUnsignedInteger($_GET['clan_id'], null);
$member = validateUnsignedInteger($_GET['member'], NULL);
$text = validateString($_GET['text']);

$displayMore = true;

if($clan_id == NULL){
	if($user_ida['clan'] != null){
		$clan_id = $user_ida['clan'];
	} else {
		$displayMore = false;
	}
}

if(isset($_GET['cancel'])) {
	$action = NULL;
}


if((isset($_GET['value1']) || isset($_GET['value2']) || isset($_GET['edited'])) && !isset($_GET['cancel'])) {
	switch($what) {
		case 'Banner':
		case 'Homepage':
		case 'Background':
			$value1 = validateURL($_GET['value1']);
			break;
		case 'Info':
			$value1 = encodeNoHTMLWithBB($_GET['value1']);
			break;
		case 'Clanzeichen':
			$value1 = validateString($_GET['value1']);
			$value2 = validateString($_GET['value2']);
			break;
		case 'Leadership':
			$value1 = validateUnsignedInteger($_GET['value1'], NULL);
			$value2 = validateUnsignedInteger($_GET['value2'], NULL);
			break;
		case 'Member':
			$value1 = validateUnsignedIntegerArray($_GET['value1'], NULL); // kicked members
			$value2 = validateUnsignedIntegerArray($_GET['value2'], NULL); // whitelist members
			break;
	}
}

function displayClanProfileReadOnly($clan, $ownclan, $userid){
	$member_qry = db_query('Select id from user where clan = '.$clan['id']);
	$count = mysqli_num_rows($member_qry);
	$member = array();
	while($row = mysqli_fetch_assoc($member_qry)){
		$tmp = generateUserLinkByID($row['id']);
		if($row['id'] == $userid){
			$member[] = $tmp.' (<a href="index.php?as=clan/clan_info&action=fire&member='.$row['id'].'">verlassen</a>)';
		} else{
			$member[] = $tmp;
		}
		if($row['id'] == $clan['leader']){
			$leader = $tmp;
		}
		if($row['id'] == $clan['co_leader']){
			$coleader = $tmp;
		}
	}

	$inv_qry = db_query('SELECT userid, TIMESTAMPDIFF(HOUR, now(), valid) as till FROM user_clan_invitations WHERE clanid = '.$clan['id'].' and valid > now()');
	$invited = array();

	while($row = mysqli_fetch_assoc($inv_qry)) {
		$invited[] = generateUserLinkByID($row['userid']) . ' (noch ' . $row['till'] . 'h gültig)';
	}

	if(count($invited) == 0) {
		$invited[] = 'Keine';
	}

	$openChallengeRequests = getOpenChallengeRequests($clan['id']);
	// okay now we split into challenges we have done and we have received
	foreach($openChallengeRequests as $request) {
		if($request['clan_requester'] != $clan['id']) {
			$openChallengeRequestsDisplay[] = displayClanLink($request['clan_requester']) ;
		} else {
			$openChallengeRequestsDisplay[] = displayClanLink($request['clan_challenged']) ;
		}
	}
?>
<div align="center">
	<table width="100%">
		<tr>
			<th width="100%" height="39" colspan="2" align="center"><?php echo $clan['clanname']; ?>
				Info</th>
		</tr>
		<?php
		if($clan['banner'] != "0"){
			?>
		<tr>
			<td width="100%" height="52" colspan="2" align="center"><img
				src="<?php echo $clan['banner']; ?> " width="468" height="60"></td>
		</tr>
		<?php
		}
		?>
		<tr>
			<th width="20%" align="Left" height="25">Leader:</th>
			<?php
			if($leader == null && $coleader == null){
				if($ownclan) {
					echo '<td rowspan="2"><a href="index.php?as=clan/clan_info&action=putsch">Macht &uuml;bernehmen</a></td>';
				} else if(count($member) > 0){
					echo '<td rowspan="2">Keine Leader</td>';
				} else {
					echo '<td rowspan="2"><a href="index.php?as=clan/clan_info&action=putsch&clan_id='.$clan['id'].'">Clan &uuml;bernehmen</a></td>';
				}
			} else{
				echo '<td>'.$leader.'</td>';
			}
			?>
		</tr>
		<tr>
			<th align="Left" height="25">Co-Leader:</th>
			<?php
			if($leader != null && $coleader != null){
				echo '<td>'.$coleader.'</td>';
			}
			?>
		</tr>
		<tr>
			<th align="Left" height="25">Clanzeichen:</th>
			<td><?php echo $clan['clanz_pre'].' '.$clan['clanz_suff']; ?></td>
		</tr>
		<tr>
			<th align="Left">Member (<?php echo $count.'/'.getMaximumMembers($clan['id']); ?>):</th>
			<td><?php echo join('<br>',$member); ?></td>
		</tr>
		<?php
		if($ownclan && count($invited) > 0) {
			?>
		<tr>
			<th align="Left">Eingeladen:</th>
			<td><?php echo join('<br>', $invited); ?></td>
		</tr>
		<?php
		}
		?>
		<?php
		if(count($openChallengeRequestsDisplay) > 0) {
			?>

		<tr>
			<th align="Left">Herausforderungen:</th>
			<td><?php echo join('<br>', $openChallengeRequestsDisplay); ?></td>
		</tr>
		<?php
		}
		?>
		<tr>
			<th align="Left" height="25">Level:</th>
			<td><?php echo $clan['level']; ?></td>
		</tr>
		<tr>
			<th align="Left" height="25">Erfahrung:</th>
			<td><?php echo $clan['exp'].' / '.getRequiredClanExp($clan['level']); ?>
			</td>
		</tr>
		<tr>
			<th align="Left" height="25">ELO-Wertung:</th>
			<td><?php echo $clan['elo']; ?></td>
		</tr>
		<tr>
			<th width="96" height="25" align="Left">Homepage:</th>
			<?php
			if($clan['homepage'] != 'Keine'){
				echo '<td height="25"><a href="'.$clan['homepage'].'" target=_blank>'.$clan['homepage'].'</a></td>';
			} else{
				echo '<td height="25">Keine</td>';
			}
			?>
		</tr>
		<tr>
			<th align="Left">Info:</th>
			<td><?php echo $clan['info']; ?></td>
		</tr>
		<?php
		if($ownclan) {
			if(isClanNewsletterAvailable($clan['id'])) {
			?>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
					<input type="hidden" name="as" value="clan/clan_info"> <input
						type="hidden" name="action" value="newsletter">
					<table  cellpadding="0" cellspacing="0" width="100%" height="69">
						<tr>
							<th height="18" valign="top" width="603" colspan="2"
								style="background-color: rbga(255,255,255 .7);">Nachricht an alle Clanmember schicken</th>
						</tr>
						<tr>
							<td height="25" valign="top" colspan="2" align="center">&nbsp;<textarea
									class="input" rows="8" name="text" cols="40"></textarea></td>
						</tr>
						<tr>
							<td height="25" valign="top" colspan="2" align="center">&nbsp;<input
								class="input" type="submit" value="abschicken"></td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
		<?php
			}
		else {
			?>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th height="18" valign="top" width="603" colspan="2" class="content">Clannachrichten erst ab Level <?php echo CLAN_LVL_NEWSLETTER;?> verf&uuml;gbar</th>
		</tr>
			<?php
			}
		}
		?>
	</table>
</div>

		<?php
}

function displayClanProfileEditable($clan, $userid){
	$member_qry = db_query('Select id from user where clan = '.$clan['id']);
	$member = array();
	$count = mysqli_num_rows($member_qry);
	while($row = mysqli_fetch_assoc($member_qry)){
		$tmp = generateUserLinkByID($row['id']);
		$kickable = true;

		if($row['id'] == $clan['leader']){
			$leader = $tmp;
			$kickable = false;
		}
		if($row['id'] == $clan['co_leader']){
			$coleader = $tmp;
			$kickable = false;
		}
		if($userid == $row['id'] && $kickable){
			$member[] = $tmp.' (<a href="index.php?as=clan/clan_info&action=fire&member='.$row['id'].'">verlassen</a>)';
		} else{
			$member[] = $tmp;
		}
	}

	$inv_qry = db_query('SELECT userid, TIMESTAMPDIFF(HOUR, now(), valid) as till FROM user_clan_invitations WHERE clanid = '.$clan['id'].' and valid > now()');
	$invited = array();

	while($row = mysqli_fetch_assoc($inv_qry)) {
		$invited[] = generateUserLinkByID($row['userid']) . ' (noch ' . $row['till'] . 'h gültig, <a href="index.php?as=clan/clan_info&action=reject&member='.$row['userid'].'">zurückziehen</a>)';
	}

	if(count($invited) == 0) {
		$invited[] = 'Keine';
	}


	$openChallengeRequests = getOpenChallengeRequests($clan['id']);
	// okay now we split into challenges we have done and we have received
	foreach($openChallengeRequests as $request) {
		if($request['clan_requester'] != $clan['id']) {
			$openChallengeRequestsDisplay[] = displayClanLink($request['clan_requester']) . ' (annehmen)';
		} else {
			$openChallengeRequestsDisplay[] = displayClanLink($request['clan_challenged']) . ' (zur&uuml;ckziehen)';
		}

	}


	?>
<table width="100%">
	<tr>
		<th width="500" height="39" colspan="2" align="center"><?php echo $clan['clanname']; ?>
			Info</th>
	</tr>
	<tr>
		<td width="100%" height="52" colspan="2" align="center"><?php
		if($clan['banner'] != "0"){
			?> <img src="<?php echo $clan['banner']; ?> " width="468" height="60">
			<?php
		}
		?> <br> <a href="index.php?as=clan/clan_info&action=edit&what=Banner">(edit)</a>
		</td>
	</tr>
	<tr>
		<th width="25%" align="Left" height="25">Leader:<a
			href="index.php?as=clan/clan_info&action=edit&what=Leadership">(edit)</a>
		</th>
		<td><?php echo $leader; ?></td>
	</tr>
	<tr>
		<th align="Left" height="25">Co-Leader:<a
			href="index.php?as=clan/clan_info&action=edit&what=Leadership">(edit)</a>
		</th>
		<td><?php echo $coleader; ?></td>
	</tr>
	<tr>
		<th align="Left" height="25">Clanzeichen:<a
			href="index.php?as=clan/clan_info&action=edit&what=Clanzeichen">(edit)</a>
		</th>
		<td><?php echo $clan['clanz_pre'].' '.$clan['clanz_suff']; ?></td>
	</tr>
	<tr>
		<th align="Left">Member (<?php echo $count.'/'.getMaximumMembers($clan['id']); ?>):<br><a
			href="index.php?as=clan/clan_info&action=edit&what=Member">(verwalten)</a></th>
		<td><?php echo join('<br>',$member); ?></td>
	</tr>
	<?php
	if(count($invited) > 0) {
		?>
	<tr>
		<th align="Left">Eingeladen:</th>
		<td><?php echo join('<br>', $invited); ?></td>
	</tr>
	<?php
	}
	?>
	<?php
	if(count($openChallengeRequestsDisplay) > 0) {
		?>
	<tr>
		<th align="Left">Herausforderungen:</th>
		<td><?php echo join('<br>', $openChallengeRequestsDisplay); ?></td>
	</tr>
	<?php
	}
	?>
	<tr>
		<th align="Left" height="25">Level:</th>
		<td><?php echo $clan['level']; ?></td>
	</tr>
	<tr>
		<th align="Left" height="25">Erfahrung:</th>
		<td><?php echo $clan['exp'].' / '.getRequiredClanExp($clan['level']); ?>
		</td>
	</tr>
	<tr>
		<th align="Left" height="25">ELO-Wertung:</th>
		<td><?php echo $clan['elo']; ?></td>
	</tr>
	<tr>
		<th align="Left" height="25">Homepage:<a
			href="index.php?as=clan/clan_info&action=edit&what=Homepage">(edit)</a>
		</th>

		<?php
		if($clan['homepage'] != 'Keine'){
			echo '<td><a href="'.$clan['homepage'].'" target=_blank>'.$clan['homepage'].'</a></td>';
		} else{
			echo '<td>Keine</td>';
		}
		?>
	</tr>
	
	<tr>
		<th align="Left">Info:<a
			href="index.php?as=clan/clan_info&action=edit&what=Info">(edit)</a></th>
		<td><?php echo $clan['info']; ?></td>
	</tr>
	<?php
		if(isClanBackgroundAvailable($clan['id'])) { ?>
		<tr>
		<th align="Left" height="25">
		Background:<a 
			href="index.php?as=clan/clan_info&action=edit&what=Background">(edit)</a> 

		</th>
	</tr>
<?php
}
?>
	<tr>
		<th align="left">Clan aufl&ouml;sen</th>
		<td><a
			href="javascript:if(confirm('Wirklich aufl&ouml;sen? der Clan kann ggf. durch jemand &uuml;bernommen werden!')==true) window.location.href='index.php?as=clan/clan_info&action=kill'">Clan
				aufl&ouml;sen (Achtung! Clans ggf. nur geleert!)</a></td>
	</tr>
		<?php
		if(isClanNewsletterAvailable($clan['id'])) {
		?>

		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
					<input type="hidden" name="as" value="clan/clan_info"> <input
						type="hidden" name="action" value="newsletter">
					<table cellpadding="0" cellspacing="0" width="100%" height="69">
						<tr>
							<th style="background-color: rbga(255,255,255 .7);" height="18" valign="top" width="603" colspan="2"
								class="table1">Nachricht an alle Clanmember schicken</th>
						</tr>
						<tr>
							<td height="25" valign="top" colspan="2" align="center">&nbsp;<textarea
									class="input" rows="8" name="text" cols="40"></textarea></td>
						</tr>
						<tr>
							<td height="25" valign="top" colspan="2" align="center">&nbsp;<input
								class="input" type="submit" value="abschicken"></td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
		<?php
			}
		else {
			?>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th height="18" valign="top" width="603" colspan="2" class="content">Clannachrichten erst ab Level <?php echo CLAN_LVL_NEWSLETTER;?> verf&uuml;gbar</th>
		</tr>
			<?php
			}
		?>
	</table>
		<?php
}

function displayClanProfile($clanid, $userid){
	$clan = getClan($clanid, FALSE);
	$user = getUser($userid, false);

	if($user['clan'] == $clan['id']) {
		// check if there is a clan chat available
		if(getClanChatId('clan_' .$user['clan']) === NULL && isClanChatAvailable($user['clan'])) {
			createChatRoom(array($user['clan']), 'clan_' .$user['clan']);
		}
	}

	if(!$clan) {
		echo displayErrorMessage(NULL, 'Es gibt den Clan mit der ID ' .$clanid.' nicht oder nicht mehr!', displayHistoryBackLink());
		return;
	}
	$editable = $clan['leader'] == $user['id'] || $clan['co_leader'] == $user['id'];
	if(!$editable){
		displayClanProfileReadOnly($clan, $user['clan'] == $clan['id'], $user['id']);
	} else{
		displayClanProfileEditable($clan, $user['id']);
	}

	if($clan['background'] != "0"){	?>
<style type="text/css">
#content
{
    background-image:url('<?php echo $clan['background'] ?>');
    background-repeat:no-repeat;
}
</style>

<?php
		}

}

function setProfile($user, $what, $value1, $value2, $clanid, $rootlvl){
	if($rootlvl < 1) {
		return 'Du hast keine Rechte um diese Aktion auszuf&uuml;hren!';
	}
	switch($what){
		case 'Homepage':
			db_query('UPDATE clan SET homepage = \''.$value1.'\' where id = '.$clanid);
			return NULL;
		
		case 'Clanzeichen':
			if(strlen($value1.$value2) > MAX_CHARS_CLANSIGN) {
				return 'Clanzeichen sind ungültig: Summe aus beiden Teilen darf nicht größer als '.MAX_CHARS_CLANSIGN.' Zeichen sein!';
			}
			db_query('UPDATE clan SET clanz_pre = \''.$value1.'\', clanz_suff = \''.$value2.'\' where id = '.$clanid);
			return NULL;
		case 'Banner':
			db_query('UPDATE clan SET banner = \''.$value1.'\' where id = '.$clanid);
			return NULL;
		case 'Info':
			db_query('UPDATE clan SET Info = \''.$value1.'\' where id = '.$clanid);
			return NULL;
		case 'Background':
		 	if(isClanBackgroundAvailable($clanid)) { db_query('UPDATE clan SET background = \''.$value1.'\' where id = '.$clanid) ;}
			return NULL;
		case 'Leadership':
			$clan = getClan($clanid);

			if($value1 == 0 && $rootlvl == 2){$value1 = 'null';}
			else if($rootlvl == 1) { $value1 = $clan['leader']; }
			if($value2 == 0){$value2 = 'null';}
			// okay, are we allowed to change?? If we have max members, it is not allowed to remove the co_leader

			$clan = getClan($clanid);
			$members = getClanMembersCount($clanid);
			$members_max = getMaximumMembers($clan['id']);

			if($members_max == $members && $clan['leader'] != $clan['co_leader'] && ($value1 == $value2 || $value2 == 'null')) {
				// Okay, actually there are the max amount of users in the clan
				// it is now not possible to change to a leader only clan!!
				return 'Es ist nicht m&ouml;glich den Co-Leader zu &auml;ndern, da der clan die maximale Anzahl Member besitzt!';
			}

			if($rootlvl == 1){ // the co leader may only set the co leader XD
				$sql = 'UPDATE clan SET co_leader = '.$value2.' where id = '.$clanid;
				//echo $sql.'<br>';
				db_query($sql);
			} else if($rootlvl == 2) { // the leader may set the leader and the co leader
				$sql = 'UPDATE clan SET leader = '.$value1.', co_leader = '.$value2.' where id = '.$clanid;
				//				echo $sql.'<br>';
				db_query($sql);
			}
			return NULL;
		case 'Member':
			// okay, now apply the changes!! --> first filter the values of value2 that are in value1
			$value2 = array_diff($value2, $value1);
			$clan = getClan($clanid);

			// now kick some asses ;)
			foreach($value1 as $member) {
				if($clan['leader'] == $member || $clan['coleader'] == $member) {
					// we cannot kick the asses of the leaders!!
					continue;
				}
				fireMember($user, $member);
			}
			updateAuthorizedClanfightCoordinators($clanid, $value2);

			displayEdit($what, $clanid, $rootlvl);
			return NULL;
		default:
			return 'Error!';
	}
}

function displayEdit($what, $clanid, $rootlvl){
	if($rootlvl < 1) {
		$content =  '<tr><td colspan="2">Du hast keine Rechte um diese Aktion auszuf&uuml;hren!</td></tr>';
		$what = 'ERROR';
	}
	$clan = getCLan($clanid);

	$content = '';
	switch($what){
		case 'Homepage':
			$content = '<tr><td colspan="2"><input class="input" name="value1" value="'.$clan['homepage'].'"/></td></tr>';
			break;
		case 'Background':
			$content = '<tr><td colspan="2"><input class="input" name="value1" value="'.$clan['background'].'"/></td></tr>';
			break;
		case 'Clanzeichen':
			$content = '<tr><td>Prefix: <input class="input" name="value1" value="'.$clan['clanz_pre'].'"/></td><td>Suffix: <input class="input" name="value2" value="'.$clan['clanz_suff'].'"/></td></tr>';
			break;
		case 'Banner':
			$content = '<tr><td colspan="2"><input class="input" name="value1" value="'.$clan['banner'].'"/></td></tr>';
			break;
		case 'Leadership':
			$member_qry = db_query('Select id, nickname from user where clan = '.$clanid);
			$member[] = '<option value="0" selected>Niemand</option>';
			$member2[] = '<option value="0" selected>Niemand</option>';
			while($row = mysqli_fetch_assoc($member_qry)){
				if($row['id'] == $clan['leader']) {
					$member[] = '<option value="'.$row['id'].'" selected="selected">'.$row['nickname'].'</option>';
				} else {
					$member[] = '<option value="'.$row['id'].'">'.$row['nickname'].'</option>';
				}

				if($row['id'] == $clan['co_leader']) {
					$member2[] = '<option value="'.$row['id'].'" selected="selected">'.$row['nickname'].'</option>';
				} else {
					$member2[] = '<option value="'.$row['id'].'">'.$row['nickname'].'</option>';
				}

			}
			if($rootlvl == 2){
				$content = '<tr><td>Leader:</td><td><select class="input" name="value1" />'.join('',$member).'</select></td></tr>';
			}
			$content .= '<tr><td>Co-Leader:</td><td><select class="input" name="value2" />'.join('',$member2).'</select></td></tr>';
			break;
		case 'Info':
			$content = '<tr><td colspan="2"><textarea class="input" name="value1" rows="8" cols="40">'.debbcode($clan['info']).'</textarea></td></tr>';
			break;
		case 'Member':
				$content = '<tr><td colspan="2" width="400px">'.PHP_EOL;
				$content .= '<table width="100%">'.PHP_EOL;
				$content .= '<tr><th align="center">Member</th><th align="center" width="75px">Kick</th><th align="center" width="75px">CF Rechte</th></tr>'.PHP_EOL;
				$content .= '<tr><td align="center" colspan="3"><hr /><input type="hidden" name="edited" value="1"></td></tr>'.PHP_EOL;
				$member_qry = db_query('Select id from user where clan = '.$clan['id']);
				$authorized = getAuthorizedClanfightCoordinatorIDs($clanid);
				$leader = NULL;
				$coleader = NULL;
				while($row = mysqli_fetch_assoc($member_qry)){
					$row['nickname'] = generateUserLinkByID($row['id'], false);
					if($row['id'] == $clan['leader']){
						$leader = $row;
					} else if($row['id'] == $clan['co_leader']) {
						$coleader = $row;
					} else {
						$member[] = $row;
					}
				}

				$i = 0;

				// Kay, first display the chief
				if($leader != NULL) {
					$content .= '<tr><td align="center">' . $leader['nickname'] . '</td><td align="center"><input name="value1['.$i.']" type="checkbox" disabled="disabled" value="'. $leader['id'] .'" /></td><td align="center"><input name="value2['.$i.']" type="checkbox" disabled="disabled" checked="checked" value="'. $leader['id'] .'" /></td></tr>'.PHP_EOL;
					$i++;
				}

				if($coleader != null) {
					$content .= '<tr><td align="center">' . $coleader['nickname'] . '</td><td align="center"><input name="value1['.$i.']" type="checkbox" disabled="disabled" value="'. $coleader['id'] .'" /></td><td align="center"><input name="value2['.$i.']" type="checkbox" disabled="disabled" checked="checked" value="'. $coleader['id'] .'" /></td></tr>'.PHP_EOL;
					$i++;
				}

				foreach($member as $memb) {
					if(in_array($memb['id'], $authorized)) {
						$checked = 'checked="checked"';
					} else {
						$checked = '';
					}
					$content .= '<tr><td align="center">' . $memb['nickname'] . '</td><td align="center"><input name="value1['.$i.']" value="'.$memb['id'].'" type="checkbox"/></td><td align="center"><input name="value2['.$i.']" value="'.$memb['id'].'" type="checkbox" '.$checked.'/></td></tr>'.PHP_EOL;
					$i++;
				}

				$content .= '<tr><td align="center" colspan="3"><hr /></td></tr>'.PHP_EOL;
				$content .= '</table>'.PHP_EOL.'</td></tr>';

			break;
		case 'ERROR':
			break;
		default:
			$content = '<tr><td colspan="2">Error '.$what.' unknown!</td></tr>';
			break;
	}

	?>
<form action="index.php" method="GET">
	<input type="hidden" name="as" value="clan/clan_info" /> <input
		type="hidden" name="action" value="edit" /> <input type="hidden"
		name="what" value="<?php echo $what; ?>" />
	<table>
		<tr>
			<th colspan="2"><?php echo $what; ?> &Auml;ndern</th>
		</tr>
		<?php echo $content; ?>
		<tr>
			<td align="center" colspan="2">
				<input class="input" type="submit" value="Edit" />
				&nbsp;
				<button name="cancel" value="1" id="input">Hauptmen&uuml;</button>
			</td>
		</tr>
	</table>
</form>
		<?php
}


if($action !== NULL) { // this is done to not require to reprogramm the whole stuff!

	$rootlvl = 0;
	$clan = getClan($user_ida['clan']);

	if($clan['leader'] == $user_ida['id'] || ($clan['leader'] === null && ($clan['co_leader'] === null || $clan['co_leader'] = $user_ida['id']))) {
		$rootlvl = 2; // leader or putsch!!
	} else if($clan['co_leader'] = $user_ida['id']) {
		$rootlvl = 1; // co_leader
	}

	switch ($action) {
		case 'edit':
			$errorMsg = NULL;
			$row = mysqli_fetch_assoc(db_query('SELECT leader, co_leader from clan where id = '.$user_ida['clan']));
			if($row['leader'] != $user_ida['id'] && $row['co_leader'] != $user_ida['id']){
				displayErrorMessage(NULL, 'Du bist weder Leader noch Co-Leader des Clans!', displayHistoryBackLink());
				$displayMore = false;
			} else if(isset($value1) || isset($value2)){
				$errorMsg = setProfile($user_ida, $what, $value1, $value2, $user_ida['clan'], $rootlvl);
			} else{
				$errorMsg = displayEdit($what, $user_ida['clan'], $rootlvl);
				$displayMore = false;
			}
			if($errorMsg !== NULL) {
				displayErrorMessage(NULL, $errorMsg, displayHistoryBackLink());
				$displayMore = false;
			} else if($what == 'Member') {
				$displayMore = false;
			}

			break;
		case 'fire':
			fireMember($user_ida, $member);
			if($user_ida['id'] == $member) {
				$displayMore = false;
			}
			break;
		case 'putsch':
			if($clan_id == $user_ida['clan']) {
				if(!setMeAsLeader($user_ida)) {
					displayErrorMessage(NULL, 'Konnte den Clan nicht übernehmen ...', displayHistoryBackLink());
					$displayMore = false;
				}
			} else if ($clan_id !== NULL && ($errorMsg = conquerClan($clan_id, $user_ida['id'])) !== NULL) {
				displayErrorMessage(NULL, $errorMsg, displayHistoryBackLink());
				$displayMore = false;
			}
			break;
		case 'kill':
			if(($errorMsg = deleteClan($user_ida['id'])) != NULL) {
				displayErrorMessage(NULL, $errorMsg, displayHistoryBackLink());
			} else {
				displayErrorMessage(NULL, 'Clan erfolgreich aufgel&ouml;st.', '<a href="index.php">weiter</a>');
			}
			$displayMore = false;
			break;
		case 'reject':
			$errorMsg = revokeInvitation($user_ida, $member);
			if($errorMsg !== NULL) {
				echo displayErrorMessage(NULL, $errorMsg, displayHistoryBackLink());
				$displayMore = false;
			} else {
				displayErrorMessage('','Du hast die Einladung erfolgreich zur&uuml;ckgezogen!', '<a href="index.php?as=clan/clan_info">weiter</a>');
				$displayMore = false;
			}
			break;
		case 'newsletter':
			sendClanNewsletter($user_ida, $text);
			$displayMore = false;
			break;
		default:
			break;
	}
}

if($displayMore){
	displayClanProfile($clan_id, $user_ida['id']);
}
?>