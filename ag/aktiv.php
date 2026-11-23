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
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/exp.inc.php');
include_once(ROOT_PATH.'/include/messagefunctions.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$nick_name = validateName($_GET['nick_name']);
$serial = validateString($_GET['serial']);

// Unkritisch
$charm = $_GET['charm'];

if($charm) {
	$user_k = mysqli_fetch_assoc(db_query('SELECT * FROM user_activation WHERE username=\''.$nick_name.'\' AND code=\''.$serial.'\''));
	if(!$user_k['username']) {
		displayErrorMessage(NULL,'Account konnte nicht aktiviert werden!', displayHistoryBackLink());
		exit;
	}

#########USER AKTIVIERUNG

	db_query('INSERT INTO user(nickname, mail, passwort, agb) values(\''.$user_k['username'].'\',\''.$user_k['mail'].'\', \''.$user_k['passwort'].'\', \'ja\')');
	$user_ida_nach = mysqli_fetch_array(db_query('SELECT id FROM user WHERE nickname=\''.$nick_name.'\''));
	$sql = 'INSERT arena(besitzer, exp) values (\''.$user_ida_nach['id'].'\', \'0,'.calculateRequiredExpArena(1).'\')';
	db_query($sql);
	db_query('DELETE from user_activation where username = \''.$user_k['username'].'\'');
	displayErrorMessage(NULL,'Account wurde aktiviert!', '<a href="index.php?as=login">weiter</a>');
	sendMessage('Die Animegame Crew', $user_ida_nach['id'], 'Herzlich Willkommen bei AG', 'Herzlich Willkommen beim Animegame,'."\n".' dem Spiel in dem die Welten von One Piece und Dragonball aufeinanderprallen.'."\n\n".'Zu Anfang sieht AnimeGame ziemlich kompliziert aus, deshalb liest Du dir am besten erstmal den Anfängerguide in unserem Wiki durch:'."\n".'[url='.$GLOBALS['noobguide_url'].'][color=yellow]Anfängerguide[/color][/url]'."\n\n".'Wenn danach noch Fragen offen sind, findest Du bestimmt eine Antwort in der FAQ-Abteilung:'."\n".'[url='.$GLOBALS['faq_url'].'][color=red]Häufig gestellte Fragen[/color][/url]'."\n\n".'Falls du danach immer noch Fragen haben solltest, kannst du diese auch gerne im Chat oder im Forum stellen. Die anderen Mitspieler werden dir bestimmt gerne weiterhelfen.'."\n\n".'Viel Spass'."\n".'Das Animegame Team');
	exit;
}
?>
<form action="index.php" method="GET">
	<input type="hidden" name="as" value="aktiv" />
	<input type="hidden" name="charm" value="1" />
<div align="center">
  <center>
  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%" height="25">
    <tr>
      <td width="466" height="25" colspan="2">
      <p align="center"><b>Account Aktivieren</b></td>
    </tr>
    <tr>
      <td width="179" height="25">
      <p align="center"><b>Nickname</b></td>
      <td width="281" height="25">&nbsp;<input id="input" name="nick_name" size="20"></td>
    </tr>
    <tr>
      <td width="179" height="25">
      <p align="center"><b>Serial</b></td>
      <td width="281" height="25">&nbsp;<input id="input" name="serial" size="20"></td>
    </tr>

    <tr>
      <td width="179" height="25"></td>
      <td width="281" height="25">&nbsp;<input id="input" type="submit" value="aktivieren"></form></td>
    </tr>
  </table>
  </center>
</div>