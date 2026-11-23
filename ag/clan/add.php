<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once (ROOT_PATH . '/include/config.inc.php');
include_once (ROOT_PATH . '/include/fehlerausgabe.inc.php');
include_once (ROOT_PATH . '/include/designfunctions.inc.php');
include_once (ROOT_PATH . '/include/parse.inc.php');
include_once (ROOT_PATH . '/include/clan.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$clanid = validateUnsignedInteger($_GET['clanid'], null);
$poll1 = validateStringCritical($_GET['poll1']);
$poll2 = validateStringCritical($_GET['poll2']);
$pagenum = validateUnsignedInteger($_GET['pagenum'], null);

// Unkritisch
$action = $_GET['action'];
$user = $user_ida;


if ($action == 'accept') {
	joinClan($user_ida, $clanid);
} else if ($action == 'deny') {
	denyClan($user_ida, $clanid);
} else {

	?>

<form action="index.php" method="GET">
	<input type="hidden" name="as" value="clan/add"> <input type="hidden"
		name="charm" value="1">
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<th height="30" width="585" colspan="5" class="content">Erhaltene
				Claneinladungen</th>
		</tr>
		<tr>
			<th height="30" class="content">Clanname</th>
			<th height="30" colspan="2" class="content">Deadline</th>
			<th height="30" colspan="2" class="content">Reagieren</th>
			<?php
			$sql = 'SELECT * FROM user_clan_invitations WHERE userid = '.$user_ida['id'].' and valid > now();';
			$qry = db_query($sql);
			while($row = mysqli_fetch_assoc($qry)){
				?>


		<tr>
			<td height="30" align="center" class="content"><?php echo displayClanLink($row['clanid']); ?>
			</td>
			<td height="30" align="center" colspan="2" class="content"><?php echo $row['valid']; ?>
			</td>
			<td height="30" align="center" colspan="2" class="content"><a
				href="index.php?as=clan/add&action=accept&clanid=<?php echo $row['clanid']; ?>">Akzeptieren</a>
				| <a
				href="index.php?as=clan/add&action=deny&clanid=<?php echo $row['clanid']; ?>">Ablehnen</a>
			</td>
		</tr>
		<tr>
			<td height="30" colspan="5" class="content"></td>
		</tr>
		<?php

			}

			if (!$poll1) {
				$poll1 = 'clanname';
				$poll2 = 'ASC';
			}
			if ($poll1 == 'clanname' AND $poll2 == 'ASC') {
				$poll['clanname'] = 'DESC';
			} else {
				$poll['clanname'] = 'ASC';
			}
			if ($poll1 == 'leader' AND $poll2 == 'ASC') {
				$poll['leader'] = 'DESC';
			} else {
				$poll['leader'] = 'ASC';
			}
			if ($poll1 == 'geld' AND $poll2 == 'ASC') {
				$poll['geld'] = 'DESC';
			} else {
				$poll['geld'] = 'ASC';
			}
			$ready = 'clan/add&pagenum='.$pagenum;
			?>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th height="30" width="585" colspan="5" class="content">Übersicht
				aller Clans</th>
		</tr>
		<tr>
			<th height="33" width="96" valign="top" class="content"><?php echo '<a href="index.php?as='.$ready.'&poll1=clanname&poll2='.$poll['clanname'].'">Clanname</a>'; ?>
			</th>
			<th height="33" width="128" valign="top" class="content"><?php echo '<a href="index.php?as='.$ready.'&poll1=leader&poll2='.$poll['leader'].'">Leader</a>'; ?>
			</th>
			<th height="33" width="76" valign="top" class="content">Mitglieder</th>
			<th height="33" width="98" valign="top" class="content"><?php echo '<a href="index.php?as='.$ready.'&poll1=geld&poll2='.$poll['geld'].'">Geld</a>'; ?>
			</th>
			<th height="33" width="76" valign="top" class="content">Info</th>
		</tr>
		<?php
		$sql = 'SELECT c.clanz_pre, c.clanz_suff, c.clanname, c.geld, c.info, c.id, c.leader FROM clan c ORDER BY '.$poll1.' '.$poll2.' LIMIT '.($pagenum*20).',20';
		$clan = db_query($sql);
		while ($row = mysqli_fetch_array($clan)) {
			$mitglieder = mysqli_num_rows(db_query('SELECT id FROM user WHERE clan='.$row['id']));
			$z_v = array($row['clan_pre'], $row['clan_suff']);
			?>

		<tr>
			<td height="35" width="96" valign="top" align="center"
				class="content"><?php echo displayClanLink($row['id'], $row['clanname']); ?>
			</td>
			<td height="25" width="128" valign="top" align="center"
				class="content"><?php echo displayUserLinkByID($row['leader']); ?>
			</td>
			<td height="25" width="76" valign="top" align="center"
				class="content"><?php echo $mitglieder; ?>
			</td>
			<td height="25" width="98" valign="top" align="center"
				class="content"><?php echo displayMoney($row['geld']); ?>
			</td>
			<td height="25" valign="top" align="center" class="content"><?php echo $row['info']; ?>
			</td>
		</tr>
		<?php
		// TODO: Seitenzahlen aus Designfunctions benutzen!!!
		}
		$Total = mysqli_num_rows(db_query('SELECT id FROM clan'));
		$url = '<a href="'.$_SERVER['PHP_SELF'].'?as=clan/add&pagenum=###PAGE###">###LABEL###</a>';
		?>
		<tr>
			<td colspan="5" class="content"><?php echo displayPagelinksNew(20, $Total, $pagenum, $url); ?>
			</td>
		</tr>

	</table>
</form>
<?php
}
?>
