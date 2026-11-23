<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

/**
 * Diese Datei wird aufgerufen wenn "Charakter verwalten" ausgewaehlt wurde.
 * TODO: Layout anpassen (keine <center>, <font> und <strong> Tags mehr)
 * TODO: Alle "" Strings in '' Strings umwandeln
 * TODO: Funktioneller Aufruf, damit exit-Tags verschwinden koennen und somit der Chat angezeigt werden kann!!
 */

include_once('path.inc.php'); // get the path ;)
include_once (ROOT_PATH . '/include/config.inc.php');
include_once (ROOT_PATH . '/include/char.inc.php');
include_once (ROOT_PATH . '/include/fehlerausgabe.inc.php');
include_once (ROOT_PATH . '/include/parse.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$char_id = validateUnsignedInteger($_GET['char_id'], null);

// Unkritisch!
$charm = $_GET['charm'];
$as = $_GET['as'];

if ($charm == 3) {
	$char = getChar($char_id);
	$result = deleteChar($user_ida['id'],$char_id);
	if($result === true){
		displayErrorMessage(NULL, 'Der Char '.$char['name']. ' wurde gelöscht', '<a href="index.php?as=char_index">weiter</a>');
	} else{
		displayErrorMessage(NULL, $result, '<a href="index.php?as=char_index">weiter</a>');
	}
	exit;
}
if ($as == "char_index") {
	$as = "char_profil";
}
?>

<div align="center">
  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="90%" height="132">

<?php


$chars = getCharsOfUser($user_ida['id']);
if(count($chars) == 0){
	?><tr><td align="center">Du hast noch keine Chars!</td></tr><?php
} else{
foreach ($chars as $row) {
?>
 <tr>
      <td width="429" height="84" align="center"><?php
?>
      <img border="1" src="<?php echo "$row[bild]"; ?>" width="75" height="75"></td>
    </tr>
    <tr>
      <td width="429" height="44" align="center"><b>Charakter: <?php

	if ($as == "schatz_suche") {
		echo "<a href=schatz.php?char_id=$row[id]>$row[name]</a> | Status: ";
	} else {
		echo "<a href=index.php?as=$as&char_id=$row[id]>$row[name]</a> | Status: ";
	}
	if ($row['status'] == 'Frei') {
		echo "<b>Frei</b> | ";
	} else {
		echo "<a href='index.php?as=abholen&ab=$row[status]&char_id=$row[id]'>$row[status]</a> | ";
	}
	echo '<a href="javascript:if(prompt(\'Mit Namen des zu L&ouml;schenden Chars best&auml;tigen (' . $row['name'] . ')\')==\''.$row['name'].'\') window.location.href=\'index.php?as=char_index&charm=3&char_id=' . $row['id'] . '\'">Charakter l&ouml;schen</a>';
?>

</b></td>
    </tr>
<?php

}
}
?>
  </table>
</div>
