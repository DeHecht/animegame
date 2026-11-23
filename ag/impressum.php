<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 *//*
 *  * Nur durch draufklicken ist das oeffnen Moeglich :D
 */


function encodeKrypted($string){
	$returnstring = '';
	for($i=0;$i<strlen($string);$i++){
		$char = substr($string, $i, 1);
		$char_ord = ord($char);
		$returnstring .= '&#'.$char_ord.';';
	}
	return $returnstring;
}
?>

<html>
<body>
  <table width="100%" height="0" >
    <tr>
    <br>
      <td width="562" height="0"><b><font size="4">&#65;&#110;&#115;&#99;&#104;&#114;&#105;&#102;&#116;</font></b><p></p>
      <br>
		<p><?php echo encodeKrypted($GLOBALS['admin_realname']); ?><br>
      	<?php echo encodeKrypted($GLOBALS['admin_street']); ?><br>
      	<?php echo encodeKrypted($GLOBALS['admin_plz']); ?></p>
      <b><font size="4">&#75;&#111;&#110;&#116;&#97;&#107;&#116;<p></p></font></b><br>
      Email: <a href="mailto:<?php echo encodeKrypted($GLOBALS['email_webmaster']); ?>"><?php echo encodeKrypted($GLOBALS['email_webmaster']); ?></a><br>
      Internet: <a href="<?php echo encodeKrypted($GLOBALS['server_url_long']); ?>"><?php echo encodeKrypted($GLOBALS['server_url_long']); ?></a>
      <p>&nbsp;</td>
    </tr>
    <tr>
    	<td><font size="4">Externe Links:</td>
    </tr>
    <tr>
 		<td>Der Inhaber &uuml;bernimmt keine Haftung für externe Links. </td>
    </tr>
  </table>
</body>
</html>