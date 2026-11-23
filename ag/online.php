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
?>
	<table cellpadding="0" cellspacing="0" height="45">
		<tr>
			<th class="content" align="center" colspan="2">Wer ist Online?</th>
		</tr>
		<tr>
			<th class="content" align="center" width="100">User</td>
			<th class="content" align="center" width="100">Letze Aktivit&auml;t</td>
		</tr>
<?php
$online_k = db_query('SELECT nickname, id, TIMESTAMPDIFF(minute, online_zeit, now()) as afk FROM user WHERE online_zeit between TIMESTAMPADD(MINUTE,-15,now()) and now() ORDER BY nickname');
while($row = mysqli_fetch_assoc($online_k)) {
?>
		<tr>
			<td align="center" class="content"><?php echo displayUserLink($row['id'], $row['nickname']); ?></td>
			<td align="center" class="content">vor <?php echo $row['afk']; ?> min</td>
		</tr>
<?php
}
?>
	</table>