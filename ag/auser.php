<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

?><link rel="stylesheet" href="design/style.css" type="text/css">
<?php
include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/auktion_functions.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');

$pagenum = validateUnsignedInteger($_GET['pagenum'], null);
$firstLetter = validateString($_GET['firstLetter']);


if($firstLetter == 'Numeric'){
	$auser = db_query('SELECT * FROM user where SUBSTRING(nickname,1,1) < \'a\' order by nickname ASC LIMIT '.($pagenum*50).', 50');
	$total = mysqli_fetch_assoc(db_query('SELECT count(id) as anzahl FROM user where SUBSTRING(nickname,1,1) < \'a\''));
} else if($firstLetter == null){
	$auser = db_query('SELECT * FROM user order by nickname ASC LIMIT '.($pagenum*50).', 50');
	$total = mysqli_fetch_assoc(db_query('SELECT count(id) as anzahl FROM user'));
} else{
	$auser = db_query('SELECT * FROM user where nickname like \''.$firstLetter.'%\' order by nickname ASC LIMIT '.($pagenum*50).', 50');
	$total = mysqli_fetch_assoc(db_query('SELECT count(id) as anzahl FROM user where nickname like \''.$firstLetter.'%\''));
}

$total = $total['anzahl'];

?>
  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="80%" height="1" align="center">
    <tr>
      <td width="693" height="11" colspan="4">
      <p align="center"><b>Hier findest du alle User</b></td>
    </tr>
    <tr>
		<td colspan="3">
			<table width="100%"><tr>
			<td><a href="index.php?as=auser">Alle</a></td>
			<td><a href="index.php?as=auser&firstLetter=Numeric">0-9</a></td><?php
			for($i=ord('A');$i<=ord('Z');$i++){
				echo '<td><a href="index.php?as=auser&firstLetter='.chr($i).'">'.chr($i).'</a></td>';
			}
		?></tr></table></td>
    </tr>
    <tr>
      <td width="143" height="30" align="center"><b>Nickname</b></td>
      <td width="98" height="30" align="center"><b>ICQ</b></td>
      <td width="157" height="30" align="center"><b>Homepage</b></td>
    </tr>
<?php
while($row = mysqli_fetch_array($auser)) {
if(!$row['icq']) { $row['icq'] = 'Kein ICQ'; }
if(!$row['homepage']) { $row['homepage'] = 'Keine Homepage'; }
?>
    <tr>
      <td width="144" height="30" align="center"><?php echo displayUserLinkByID($row['id'], FALSE); ?></td>
      <td width="99" height="30" align="center"><?php echo $row['icq']; ?></td>
      <td width="156" height="30" align="center"><?php echo '<a href="'.$row['homepage'].'">'.$row['homepage'].'</a>'; ?></td>
    </tr>
<?php
}
?>
    <tr>
      <td width="644" height="22" colspan="3"><?php echo displayPagelinksNew(50, $total, $pagenum, '<a href="index.php?as=auser&firstLetter='.$firstLetter.'&pagenum=###PAGE###">###LABEL###</a>');?></td>
    </tr>
  </table>



