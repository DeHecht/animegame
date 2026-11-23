<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once('path.inc.php'); // get the path ;)
include_once (ROOT_PATH . '/include/config/db.inc.php');
include_once (ROOT_PATH . '/include/designfunctions.inc.php');
include_once (ROOT_PATH . '/include/fehlerausgabe.inc.php');
include_once (ROOT_PATH . '/include/parse.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$nick_name = validateName($_GET['nick_name']);
$pw_1 = validateString($_GET['pw_1']);
$pw_2 = validateString($_GET['pw_2']);
$mail = validateEmailAddress($_GET['mail']);

// Unkritisch
$charm = $_GET['charm'];
$cke = $_GET['cke'];
?>

<SCRIPT language="JavaScript">
<!--

function isAPhoneNumber(){
   var s = "" + document.charz.nick_name.value; //Umwandlung in eine Zeichenkette
   var zeichen = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ! " //Gueltige Zeichen
   for (var i=0; i<s.length; i++)
      if (zeichen.indexOf(s.charAt(i))==-1)
         //kein gueltiges Zeichen
         return false
   return true
}
-->
</SCRIPT>

<?php


if ($charm) {

	if ($cke == null) {
		displayErrorMessage(NULL, 'Sie m&uuml;ssen mit den Regeln einverstanden sein', displayHistoryBackLink());
		exit;
	}

	if ($pw_1 != $pw_2) {
		displayErrorMessage(NULL, 'Die Passw&ouml;rter m&uuml;ssen gleich sein', displayHistoryBackLink());
		exit;
	}

	if ($mail == null) {
		displayErrorMessage(NULL, 'Deine Email-adresse ist nicht g&uuml;tig', displayHistoryBackLink());
		exit;

	}

	######USER UEBERPRUEFUNG

	// Entfernen der whitespaces am nickname
	$nick_name = trim($nick_name);

	$sql = 'SELECT nickname,mail FROM user WHERE nickname=\'' . $nick_name . '\' OR mail=\'' . $mail . '\'';
	//echo $sql.'<br>';
	$user_a = mysqli_fetch_assoc(db_query($sql));
	$sql = 'SELECT username,mail FROM user_activation WHERE username=\'' . $nick_name . '\' OR mail=\'' . $mail . '\'';
	//echo $sql.'<br>';
	$user_b = mysqli_fetch_assoc(db_query($sql));

	if ($user_a != FALSE || $user_b != FALSE) {
		if ($user_a['nickname'] == $nick_name || $user_b['username'] == $nick_name) {
			displayErrorMessage(NULL, 'Den Nicknamen ' . $nick_name . ' gibt es schon!', displayHistoryBackLink());
		} else
			if ($user_a['mail'] == $mail || $user_b['mail'] == $mail) {
				displayErrorMessage(NULL, 'Die Emailadresse ' . $mail . ' ist schon vorhanden gibt es schon!', displayHistoryBackLink());
			} else {
				displayErrorMessage(NULL, 'Unbekannter Fehler!', displayHistoryBackLink());
			}
		exit;
	}

	####### ANGABE FEHLER

	if (!$nick_name OR !$pw_1 OR !$pw_2 OR !$mail) {
		displayErrorMessage(NULL, 'Bitte geben sie alles ein', displayHistoryBackLink());
		exit;
	}

	######## Erfolgreich Angemeldet
	$serial = time() * 40000;
	$zeit = time();
	$serial_1 = number_format($serial, 0, '', '-');

	$aktivierungslink = $GLOBALS['server_url_long'] .ROOT_URL.'/index.php?as=aktiv&charm=1&nick_name=' . $nick_name . '&serial=' . $serial_1;
	$inhalt = 'Zugangsdaten,\n\nSie lauten:\n\nUsername: ' . $nick_name . ' \n\nPasswort: ' . $pw_1 . '\n Aktivierungsnummer: ' . $serial_1 . ' \n\n Bitte geben sie die Aktivierungsnummer auf der Seite ein oder klicken sie folgenden Link\n\n' . $aktivierungslink . '\n\nViel Spass beim Spielen!';
	mail($mail, 'Animegame', 'Zugangsdaten,' . "\n\n" . ' Sie lauten: ' . "\n\n" . 'Username: ' . $nick_name . ' ' . "\n" . ' Passwort: ' . $pw_1 . ' ' . "\n\n" . ' Aktivierungsnummer: ' . $serial_1 . ' ' . "\n\n" . ' Bitte geben sie die Aktivierungsnummer innerhalb von 14 Tagen auf der Seite ein oder klicken sie auf folgenden Link' . "\n" . ' ' . $aktivierungslink . '  ' . "\n\n" . 'Viel Spass beim Spielen!', "From: " . "Vegeta" . "\nReply-To: " . $GLOBALS['email_webmaster'] . "\nX-Mailer: PHP/" . phpversion());

	$pw_1 = md5($pw_1);

	db_query('INSERT INTO user_activation(username, code, passwort, mail, registerdate) values(\'' . $nick_name . '\', \'' . $serial_1 . '\', \'' . $pw_1 . '\', \'' . $mail . '\', now())');

	displayErrorMessage(NULL, 'Erfolgreich angemeldet | Schauen sie in ihr Postfach', '<a href="index.php?as=login">weiter</a>');
	exit;
}
?>
<form action="index.php" name="charz" method="GET" onsubmit="return isAPhoneNumber()">
<input type="hidden" name="as" value="anmeldung">
<input type="hidden" name="charm" value="1">
<div align="center">
  <center>
  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%" height="25">
    <tr>
      <th width="512" height="25" colspan="2">
      <p align="center"><b>Anmeldung</b></th>
    </tr>
    <tr>
      <td width="167" height="25">
      <p align="center"><b>Nickname</b></td>
      <td width="339" height="25">&nbsp;<input id="input" name="nick_name" maxlength="20" size="20">(keine Sonderzeichen, max 20 Buchstaben.)</td>
    </tr>
    <tr>
      <td width="167" height="25">
      <p align="center"><b>Passwort</b></td>
      <td width="339" height="25">&nbsp;<input id="input" type=password name="pw_1" size="20"></td>
    </tr>
    <tr>
      <td width="167" height="25">
      <p align="center"><b>Passwort (Wiederholung)</b></td>
      <td width="339" height="25">&nbsp;<input id="input" type=password name="pw_2" size="20"></td>
    </tr>
    <tr>
      <td width="167" height="25">
      <p align="center"><b>E-Mail</b></td>
      <td width="339" height="25">&nbsp;<input id="input" name="mail" size="20"></td>
    </tr>
    <tr>
      <td width="167" height="25">
      <p align="center"><b>Regeln gelesen: </b></td>
      <td width="339" height="25">&nbsp;<input id="input" type="checkbox" name="cke" value="ja"> <a href="index.php?as=info/agb" target="_blank">Regeln lesen</a></td>
    </tr>
    <tr>
      <td width="167" height="25">
      </td>
      <td width="339" height="25">&nbsp;<input id="input" type="submit" value="Anmelden" size="20"></form></td>
    </tr>
  </table>
  </center>
</div>