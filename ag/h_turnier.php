<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');

// Einziger Übergabewert (und der ist auch noch kritisch!!)
$page = validateUnsignedInteger($_GET['page'], null);


$online_h = mysqli_fetch_array(db_query('SELECT * FROM online WHERE id='.($page+1)));

$row = mysqli_fetch_assoc(db_query('Select max(id) as maximum from online'));
$maxpages = $row['maximum'];

$url = '<a href="index.php?as=h_turnier&page=###PAGE###">###LABEL###</a>';

$expl = explode(",",$online_h['turnier']);
$exp2 = explode(",",$online_h['turnier_klein']);
$exp3 = explode(",",$online_h['turnier_gross']);
$exp4 = explode(',',$online_h['liga']);

?>
<table cellpadding="0" cellspacing="0" width="100%" height="69">
	<tr>
		<th class="content" colspan="3">Halle der Helden aus Runde <?php echo $maxpages - $page; ?></th>
	</tr>

	<tr>
		<th class="content" height="39" colspan="3">Welt Turnier History</th>
	</tr>
	<tr>
		<th class="content" height="22" width="100" align="center">Turnier</th>
		<th class="content" height="22" width="100" align="center">Sieger</th>
		<th class="content" height="22" width="100" align="center">Trainier</th>
	</tr>
<?php

$x=0;
$z=1;
while($x+1 < count($exp3)) {
?>
	<tr>
		<td class="content" height="25" width="100" align="center"><?php echo $z; ?></td>
		<td class="content" height="25" width="100" align="center"><?php echo $exp3[$x+1]; ?></td>
		<td class="content" height="25" width="100" align="center"><?php echo $exp3[$x+2]; ?></td>
	</tr>
<?php
$x+=2;
$z++;
}
?>


	<tr>
		<th class="content" height="39" colspan="3">Wochen Turnier History</th>
	</tr>
	<tr>
		<th class="content" height="22" width="100" align="center">Turnier</th>
		<th class="content" height="22" width="100" align="center">Sieger</th>
		<th class="content" height="22" width="100" align="center">Trainier</th>
	</tr>
<?php
$x=0;
$z=1;
while($x+1 < count($expl)) {
?>
	<tr>
		<td class="content" height="25" width="100" align="center"><?php echo $z; ?></td>
		<td class="content" height="25" width="100" align="center"><?php echo $expl[$x+1]; ?></td>
		<td class="content" height="25" width="100" align="center"><?php echo $expl[$x+2]; ?></td>
	</tr>

<?php

$x+=2;
$z++;
}
?>
	<tr>
		<th class="content" height="39" colspan="3">Anf&auml;nger Turnier History</th>
	</tr>
	<tr>
		<th class="content" height="22" width="100" align="center">Turnier</th>
		<th class="content" height="22" width="100" align="center">Sieger</th>
		<th class="content" height="22" width="100" align="center">Trainier</th>
	</tr>
<?php

$x=0;
$z=1;
while($x+1 < count($exp2)) {
?>
	<tr>
		<td class="content" height="25" width="100" align="center"><?php echo $z; ?></td>
		<td class="content" height="25" width="100" align="center"><?php echo $exp2[$x+1]; ?></td>
		<td class="content" height="25" width="100" align="center"><?php echo $exp2[$x+2]; ?></td>
	</tr>
<?php
$x+=2;
$z++;
}
?>

	<tr>
		<th class="content" height="39" colspan="3">Liga History</th>
	</tr>
	<tr>
		<th class="content" height="22" width="100" align="center">Saison</th>
		<th class="content" height="22" width="100" align="center">Sieger</th>
		<th class="content" height="22" width="100" align="center">Trainier</th>
	</tr>
<?php
$x=0;
$z=1;
while($x+1 < count($exp4)) {
?>
	<tr>
		<td class="content" height="25" width="100" align="center"><?php echo $z; ?></td>
		<td class="content" height="25" width="100" align="center"><?php echo $exp4[$x+1]; ?></td>
		<td class="content" height="25" width="100" align="center"><?php echo $exp4[$x+2]; ?></td>
	</tr>

<?php
$x+=2;
$z++;
}
?>

	<tr>
		<td align="center" valign="bottom" height="22" width="100%" colspan="3">
			<table width="100%">
				<?php echo displayPagelinks($page, $maxpages, $url); ?>
			</table>
		</td>
	</tr>
</table>
