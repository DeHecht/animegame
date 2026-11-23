<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once('path.inc.php'); // get the path ;)
include_once (ROOT_PATH . '/include/config.inc.php');
include_once (ROOT_PATH . '/include/char.inc.php');
?>

<SCRIPT language="JavaScript">
var test;
	function last_fight(id){
test = 'last_fight1.php?kampf_id='
test += id;
		var Info = window.open(test,"","status=no,hotkeys=no,Height=600,Width=820,scrollbars=yes");

	}

</SCRIPT>
<?php

?>
<html>



<table border="0" width="100%"  bordercolor="#FFFFFF" bordercolordark="#787878" bordercolorlight="#2F2F2F">
<?php

$ne = time();
$last_fight = db_query("SELECT char1, char2, id FROM top_kampf WHERE dauer < '$ne' ORDER BY id DESC LIMIT 20");
?>
     <tr>
		<td width="761" height="8" colspan="3">

     	<p align="center"><b><font face="Verdana" size="2">
		Letzte 20 K&auml;mpfe</font></b></td>
	</tr>
<?php

while ($row = mysqli_fetch_array($last_fight)) {
	$char1 = getChar($row['char1']);
	$char2 = getChar($row['char2']);
?>
	<tr>
		<td width="301" height="92">
		<p align="center">

     	<font face="Verdana" size="2">
		<img border="0" src="<?php echo $char1['bild']; ?>" width="75" height="75"><strong><br>
		 <?php echo $char1['name']; ?></strong></font></td>
		<td width="101" height="92">
		<p align="center">

     	<font face="Verdana" size="2"><b>

     	vs.<br>&nbsp;<br><?php echo "<a href='javascript:last_fight($row[id])'>Angucken</a>"; ?></td>
		<td width="359" height="92">
		<p align="center">

     	<font face="Verdana" size="2" >
		<img border="0" src="<?php echo $char2['bild']; ?>" width="75" height="75"><strong><br>
		<?php echo $char2['name']; ?></strong></font></td>
	</tr>

<?php

}
?>
      </table>



</body>

</html>
