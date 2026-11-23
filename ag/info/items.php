<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH.'/include/config.inc.php');
if($item_id) {
$item_info = mysqli_fetch_array(db_query("SELECT * FROM item WHERE id='$item_id' LIMIT 1"));
?>


<div align="center">
  <center>
  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%" height="25">
    <tr>
      <td width="504" height="25" colspan="2">
      <p align="center"><b><?php echo $item_info['name']; ?></b></td>
    </tr>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>Name</b></td>
      <td width="340" height="25">&nbsp;<?php echo $item_info['name']; ?></td>
    </tr>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>St&auml;rke</b></td>
      <td width="340" height="25">&nbsp;<?php echo $item_info['starke']; ?></td>
    </tr>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>Verteidigung</b></td>
      <td width="340" height="25">&nbsp;<?php echo $item_info['verteidigung']; ?></td>
    </tr>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>Geschwindigkeit</b></td>
      <td width="340" height="25">&nbsp;<?php echo $item_info['speed']; ?></td>
    </tr>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>HP</b></td>
      <td width="340" height="25">&nbsp;<?php echo $item_info['hp']; ?></td>
    </tr>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>MP</b></td>
      <td width="340" height="25">&nbsp;<?php echo $item_info['mp']; ?></td>
    </tr>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>Level</b></td>
      <td width="340" height="25">&nbsp;<?php echo $item_info['level']; ?></td>
    </tr>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>Geld</b></td>
      <td width="340" height="25">&nbsp;<?php echo $item_info['geld']; ?></td>
    </tr>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>Type</b></td>
      <td width="340" height="25">&nbsp;<?php echo $item_info['type']; ?></td>
    </tr>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>Info</b></td>
      <td width="340" height="25">&nbsp;<?php echo $item_info['info']; ?></td>
    </tr>
<?php
$rassen_1 = explode(",", $item_info['rassen']);
$runde = 0;
include(ROOT_PATH.'/include/array.php');
while($runde < count($rassen_1)){

	$runden = $runde+1;
$rassen_na = $rassen_1[$runde];
$rassen_name = $rassen_werte[$rassen_na];
?>

    <tr>
      <td width="158" height="25">
      <p align="center"><b>Rassen <?php echo $runden; ?></b></td>
      <td width="340" height="25">&nbsp;<?php echo $rassen_name; ?></td>
    </tr>

<?php
$runde++;
}
?>

  </table>
  </center>
</div>


<?php
exit;
}
?>
<html>

<body>

<div align="center">
  <center>
  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%" height="4">
   

 <tr>
      <td width="504" height="25" colspan="2">
      <p align="center"><b>Alle Attacken</b></td>
    </tr>
<?php
if($pagenum == "" or $pagenum == 1) { $pagenum = 0; }
if($pagenum != "" and $pagenum != 1) { $pagenum = ($pagenum - 1) * 10; }

$item = db_query("SELECT name, id FROM item LIMIT $pagenum, 10");
while($row = mysqli_fetch_array($item)) {
?>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>Name</b></td>
      <td width="340" height="25">&nbsp;<?php echo "<a href=index.php?as=info/items&at_id=$row[id]>$row[name]</a>"; ?></td>
    </tr>

<?php
}


	$Total = mysqli_num_rows(db_query("SELECT id FROM item"));
	$pages = ceil($Total/10);

	$z = 1;
	$i = 0;
	while($pages > $i){

			$seiten .= "&nbsp;<a href='index.php?as=info/items&pagenum=$z'>$z</a>&nbsp;";
		$z++;
		$i++;
}
?>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>Seiten</b></td>
      <td width="340" height="25">&nbsp;<?php echo $seiten; ?></td>
    </tr>

  </table>
  </center>
</div>

</body>

</html>