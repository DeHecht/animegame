<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/random.inc.php');


// GET-Section
// Kritisch (SQL-Injections)
$send = validateUnsignedInteger($_GET['send'], null);
$mail = validateString($_GET['email']);

function changePassword($email){
	$emails = mysqli_fetch_assoc(db_query("SELECT nickname, passwort, id FROM user WHERE mail='$email'"));
	if(!$emails['nickname']) {
		return 'E-Mailadresse gibt es nicht';
	}
	$test = 'a'.mt_random_wrapper(1,1000000);
	@mail($email,'Zugangsdaten von animegame.de','Ihre Zugangsdaten,'."\n\n".'Sie lauten:'."\n\n".'Username: '.$emails['nickname']."\n\n".'Passwort: '.$test.' '."\n\n", 'From: Hecht'."\n".'Reply-To: hecht@animegame.eu'."\n".'X-Mailer: PHP/'.phpversion());

	$test2 = md5($test);
	db_query('UPDATE user SET passwort=\''.$test2.'\' WHERE id='.$emails['id']);

	return 'Ihre Daten wurden an '.$email.' geschickt';
}

if($send == 1) {
	echo changePassword($mail);
} else{
	$tmp = '';
}
?>
<div align="center">
  <form action="index.php" method="get">
 	<input type="hidden" name="as" value="pw" />
 	<input type="hidden" name="send" value="1" />
  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="454" height="160">
    <tr>
      <th width="454" height="35" align="center">Passwort vergessen?</th>
    </tr>
    <tr>
      <td width="454" height="63" align="center">
      	<input id="input" name="email" size="30" value="E-Mail Addresse Eingeben">
      </td>
    </tr>
    <tr>
      <td width="454" height="57" align="center">
      	<input id="input" type="submit" value="Passwort zuschicken lassen" size="20">
      </td>
    </tr>
  </table>
</div>
