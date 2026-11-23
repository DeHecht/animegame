<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
if (isset($_POST['nick_name']) and isset($_POST['pw_1'])) {
	include_once(ROOT_PATH . '//include/config/db.inc.php');
	include_once(ROOT_PATH . '//include/user.inc.php');
  include_once(ROOT_PATH . '//include/parse.inc.php');
	
	$nick_name = validateName($_POST['nick_name']);
	$pw_1 = validateString($_POST['pw_1']);
	
	$ip_con = mysqli_fetch_array(db_query("SELECT nickname FROM user WHERE ip='$REMOTE_ADDR' AND nickname !='$nick_name' LIMIT 1"));
	
	if (checkLoginPassword($nick_name, $pw_1)) {
		// MumbleAddon!!
		if(isset($GLOBALS['mumble_db_name'])){
			include_once(ROOT_PATH . '/include/mumble.inc.php');
			insertUser($_POST['nick_name'], $_POST['pw_1']);
		}
		
		setCookies($nick_name,$pw_1);
		header ("Location: index.php"); 
	}
	
}
?>
<form action="index.php?as=login" method="POST">
<div align="center">
  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%" height="25">
    <tr>
      <td width="460" height="25" colspan="2">
      <p align="center"><b>Login System</b></td>
    </tr>
    <tr>
      <td width="159" height="25">
      <p align="center"><b>Nickname</b></td>
      <td width="295" height="25">&nbsp; <input id="input" size="20" name="nick_name"></td>
    </tr>
    <tr>
      <td width="159" height="25">
      <p align="center"><b>Passwort</b></td>
      <td width="295" height="25">&nbsp; <input id="input" size="20" type="password" name="pw_1"></td>
    </tr>
    <tr>
      <td width="159" height="25"></td>
      <td width="295" height="25">&nbsp; <input id="input" type=submit value="login"></td></form>
    </tr>
  </table>
  <?php
  if (isset($_POST['nick_name']) and isset($_POST['pw_1'])) {
	  if (!checkLoginPassword($nick_name, $pw_1)) {
	  	echo '<p style="color:f00;">Username oder Passwort falsch!</p>';
		}
	}
  ?>
</div>