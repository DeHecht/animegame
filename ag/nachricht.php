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
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/messagefunctions.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/usergroup.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$besitzer = validateName($_REQUEST['besitzer']);
$betreff = validateString($_REQUEST['betreff']);
$text = validateString($_REQUEST['text']);
$nachrichten = validateUnsignedIntegerArray($_REQUEST['checky'], 0);
$nummer = validateUnsignedInteger($_REQUEST['nr'], null);
$id = validateUnsignedInteger($_REQUEST['id'], null);
$page = validateUnsignedInteger($_REQUEST['page'], null);
$ignoname = validateName($_REQUEST['ignoname']);

// Unkritisch
$charm = $_REQUEST['charm'];


function deleteFromIgnorelist($userid, $ignoredUserId){ //charm 7
	db_query('DELETE FROM ignolist WHERE user=\''.$ignoredUserId.'\' AND besitzer=\''.$userid.'\' LIMIT 1');

	$user_name = mysqli_fetch_array(db_query('SELECT nickname FROM user WHERE id=\''.$ignoredUserId.'\''));

	$fehler_m = $user_name['nickname'].' wurde erfolgreich aus der Ignoreliste entfernt.';
	$weiter_an = '<a href="index.php?as=nachricht&charm=5">weiter...</a>';

	displayErrorMessage('&Auml;nderungen &uuml;bernommen',$fehler_m,$weiter_an);
}
function insertInIgnorelist($userid, $ignorename){ /// if($charm == 6) {
	$ignore_user = mysqli_fetch_array(db_query('SELECT id FROM user WHERE nickname=\''.$ignorename.'\''));
	$bereits_ignoriert = mysqli_num_rows(db_query('SELECT id FROM ignolist WHERE besitzer='.$userid.' AND user='.$ignore_user['id']));
	$fehler_m = '';
	$weiter_an = '<a href="index.php?as=nachricht&charm=5">weiter...</a>';
	if($bereits_ignoriert) {
		$fehler_m = 'Nachrichten von '.$ignorename.' werden schon ignoriert.';
	} else{
		db_query('INSERT ignolist SET user='.$ignore_user['id'].', besitzer='.$userid);
		$fehler_m = 'Die Nachrichten von '.$ignorename.' werden ab jetzt ignoriert.';
	}
	displayErrorMessage('&Auml;nderungen &uuml;bernommen',$fehler_m,$weiter_an);
 }

function showIngorelist($userid){//if($charm == 5) {

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
	<input type="hidden" name="as" value="nachricht">
	<input type="hidden" name="charm" value="6">
	<table border="0" width="100%" height="166" id="AutoNumber1">
		<tr>
			<th width="336" height="15" colspan="2" align="center">Ignorierlist</th>
		</tr>
		<tr>
			<th width="73" height="15">Name</th>
			<td width="257" height="15"><input id="input" name="ignoname"></input></td>
		</tr>
		<tr>
			<td width="73" height="28"></td>
			<td width="257" height="28"><input id="input" type="submit" value="Ignorieren"></input></td>
		</tr>
		<tr>
			<th width="336" height="47" colspan="2" align="center">Ignorierte Nutzer</th>
		</tr>
<?php
	$user_igno_list = db_query('SELECT u.nickname, u.id FROM ignolist i LEFT JOIN user u ON(u.id=i.user) WHERE i.besitzer='.$userid);
	while($row = mysqli_fetch_assoc($user_igno_list)) {
?>
		<tr>
			<td width="336" height="15" colspan="2" align="center"><?php echo "<a href=index.php?as=nachricht&charm=7&id=".$row['id'].">".$row[nickname]."</a>"; ?></td>
		</tr>
<?php
	}
?>
	</table>
</form>
<?php
}

function displayMessage($userid,$nr){//if($charm == 3) {
	$nachricht = mysqli_fetch_assoc(db_query('SELECT text, betreff, id, von FROM nachricht WHERE id='.$nr.' AND besitzer='.$userid));
	db_query('UPDATE nachricht SET ag=\'alt\' WHERE id='.$nachricht['id']);

?>
<table cellpadding="0" cellspacing="0" width="80%" height="176">
	<tr>
		<td>&nbsp;</td>
		<td height="31">&nbsp;</td>
	</tr>
	<tr>
		<th valign="top" align="center">User</th>
		<td height="25" valign="top" align="center"><?php echo '<a href="index.php?as=info&userage='.$nachricht['von'].'"> '.$nachricht['von'].'</a>'; ?></td>
	</tr>
	<tr>
		<th valign="top" align="center">Betreff</th>
		<td height="25" valign="top" align="center"><?php echo $nachricht['betreff']; ?></td>
	</tr>
	<tr>
		<th valign="top" align="center">Text</th>
		<td height="25" valign="top">
			<table cellpadding="0" cellspacing="0" width="250" border=1 height="25">
				<tr>
					<td width="" height="30"> <?php echo $nachricht['text']; ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<?php
		$betreff=urlencode('Re: '.$nachricht['betreff']);
	?>
	<tr>
		<td width="185" valign="top" align="center">&nbsp;</td>
		<td height="50" width="205" valign="top" align="center"><a href="index.php?as=nachricht&charm=1&besitzer=<?php echo $nachricht[von];?>&betreff=<?php echo $betreff; ?>">Antwort</a></td>
	</tr>
</table>

<?php
}
?>
<?php


function deleteMessage($userid,$nachrichten){
	if(isset($nachrichten) && count($nachrichten) > 0 ){
		$weiter_an = '<a href="index.php?as=nachricht">weiter...</a>';
//		echo implode(',', $nachrichten);
		db_query('DELETE FROM nachricht WHERE besitzer='.$userid.' AND id IN ('.implode(',',$nachrichten).')');
		displayErrorMessage('&Auml;nderungen &uuml;bernommen','Nachrichten Erfolgreich gel&ouml;scht',$weiter_an);
	} else if(isset($nachrichten) && count($nachrichten) == 0) {
                displayErrorMessage(NULL, 'Nachrichten konnten nicht gel&ouml;scht werden, da keine ausgew&auml;hlt wurden.',displayHistoryBackLink());
	} else{
		displayErrorMessage(NULL, 'Nachrichten konnten nicht gel&ouml;scht werden',displayHistoryBackLink());
	}
}


function sendMessageForumlar($userid,$username,$besitzer,$betreff,$text){//	if($charm == 1) {
	$weiter_an = '<a href="index.php?as=nachricht&charm=1">weiter...</a>';
	if(isset($besitzer) && isset($text)) {
		$user_erf = mysqli_fetch_assoc(db_query('SELECT id FROM user WHERE nickname=\''.$besitzer.'\''));
		$igno_user = mysqli_num_rows(db_query('SELECT id FROM ignolist WHERE besitzer='.$user_erf['id'].' AND user='.$userid));

		if($igno_user) {
			displayErrorMessage(NULL,'Fehler, der Nutzer '.$besitzer.' hat dich auf seiner Ignorierliste',$weiter_an);
			return;
		}

		if(!$user_erf['id']) {
			displayErrorMessage(NULL,'Fehler, Nutzer '.$besitzer.' existiert nicht...',$weiter_an);
			return;
		}
		sendMessage($username, $user_erf[id], $betreff, $text);
		displayErrorMessage('&Auml;nderungen &uuml;bernommen','Nachricht erfolgreich verschickt',$weiter_an);
		return;
	}


?>
	<SCRIPT language="JavaScript">

	function check(){
		val = document.getElementById("user").value.toLowerCase();
		if(<?php
			$users = array_merge(getGroupUsers(ADMIN), getGroupUsers(ENTWICKLER));
			// var_dump($users);
			$conditions = array();
			foreach( $users as $user ) {
				$conditions[] = 'val == "'.strtolower($user['nickname']).'"';
			}
			echo join(' || ', $conditions);
		?>){
			document.getElementById('notify').innerHTML	= "Bugreports, Sittinganfragen und andere Adminangelegenheiten bitte nicht via pn verschicken (auch nicht im Forum)!!";
		}else{
			document.getElementById('notify').innerHTML	= "";
		}
	}


	</SCRIPT>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
		<!--- Wird eh im PHP-Skript abgefangen! -->
		<input type="hidden" name="as" value="nachricht"></input>
		<input type="hidden" name="charm" value="1"></input>
		<table cellpadding="0" cellspacing="0" width="50%">
			<tr>
				<td height="31" width="100%" colspan="2" id="notify"><?php
					if(strtolower($besitzer) == 'hecht' || strtolower($besitzer) == 'heucheal' || strtolower($besitzer) == 'senf' || strtolower($besitzer) == 'vendetta'){
						echo 'Bugreports, Sittinganfragen und andere Adminangelegenheiten bitte nicht via pn verschicken (auch nicht im Forum)!!';
					} else{
						echo '&nbsp;';
					}
				?></td>
			</tr>
			<tr>
				<th height="25" width="245" valign="top" align="left">User</th>
				<td height="25" width="271" valign="top" align="center"><input class="input" id="user" name="besitzer" size="25" value="<?php echo $besitzer; ?>" onchange="check()"></input></td>
			</tr>
			<tr>
				<th height="25" width="245" valign="top" align="left">Betreff</th>
				<td height="25" width="271" valign="top" align="center"><input class="input" name="betreff" size="25" value="<?php echo $betreff; ?>" onfocus="check()"></input></td>
			</tr>
			<tr>
				<th height="70" width="245" valign="top" align="left">Text</th>
				<td height="70" width="271" valign="top" align="center"><textarea class="input" rows="8" name="text" cols="40" onfocus="check()"><?php echo $text; ?></textarea></td>
			</tr>
			<tr>
				<td height="25" width="245" valign="top" align="center">&nbsp;</td>
				<td height="25" width="271" valign="top" align="center"><input class="input" type="submit" value="Nachricht verschicken"></input></td>
			</tr>
		</table>
	</form>
<?php
}
function mainPage($userid,$aktualPage){
	if($aktualPage == NULL){
		$aktualPage = 0;
	}
	$nachricht = db_query('SELECT betreff, ag, von, datum, id FROM nachricht WHERE besitzer='.$userid.' order by id DESC LIMIT '.($aktualPage*30).',30');
	$anzahl = mysqli_fetch_assoc(db_query('SELECT count(*) as anzahl from nachricht WHERE besitzer='.$userid));
	$anzahl = ceil($anzahl['anzahl']/30);
?>
<form name="nachrichten" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<input type="hidden" name="as" value="nachricht">
	<input type="hidden" name="charm" value="2">
	<table cellpadding="0" cellspacing="0" width="100%" height="172">
		<tr>
			<th height="44" valign="top" width="100%" colspan="6" align="center"><a href="index.php?as=nachricht&charm=1">Nachrichten Schreiben</a> | <a href="index.php?as=nachricht&charm=5">Ignoreliste</a></th>
		</tr>
		<tr>
			<td height="28" width="30" valign="top" align="center">&nbsp;</td>
			<th height="28" width="98" valign="top" align="center">Betreff</th>
			<th height="28" width="128" valign="top" align="center">Von</th>
			<th height="28" width="128" valign="top" align="center">Datum</th>
		</tr>
<?php
	$t=0;
	while($row = mysqli_fetch_assoc($nachricht)) {
?>
		<tr>
			<td height="40" width="32" valign="top" align="left"><?php echo '<input id="input" name="checky['.$t.']" type="checkbox" value="'.$row[id].'"> ('.$row[ag].')'; ?></td>
			<th height="28" width="98" valign="top" align="center"><?php echo $row['betreff']; ?></th>
			<th height="28" width="128" valign="top" align="center"><?php echo '<a href="index.php?as=nachricht&charm=3&nr='.$row[id].'">'.$row[von].'</a>'; ?></th>
			<th height="28" width="128" valign="top" align="center"><?php echo $row['datum']; ?></th>
		</tr>
<?php
		$t++;
	}
?>
		<tr>
			<td height="28" valign="top" align="left" colspan="5">

			<script language="JavaScript">

				function mark(){
					for(var i=0;i<document.forms["nachrichten"].length;i++){
						document.forms["nachrichten"].elements[i].checked = true;
					}
				}

			</script>

			<input id="input" type="button" name="mark_all" value="alle markieren" onClick="javascript:mark()"/>


			<input id="input" type="submit" value="L&ouml;schen" />
			</td>
		</tr>
		<tr>
			<td colspan="4" width="100%" align="center">
<?php
						echo displayPagelinks($aktualPage,$anzahl,'<a href="'.$_SERVER['PHP_SELF'].'?as=nachricht&page=###PAGE###">###LABEL###</a>');
?>
			</td>
		</tr>
	</table>
</form>
<?php
}

// So und nun die Operationen um die anderen Daten zu fuellen
// Generell werden alle Daten via GET uebergeben!
if(isset($user_ida['id'])){
	if($charm == 1){
		sendMessageForumlar($user_ida['id'],$user_ida['nickname'],$besitzer,$betreff,$text);
	} else if($charm == 2){
		deleteMessage($user_ida['id'],$nachrichten);
	} else if ($charm == 3){
		displayMessage($user_ida['id'],$nummer);
	} else if($charm == 4){
		// Gibts nicht!
		echo 'Hey Cheater!! Versuchs woanders!! :P';
	}else if($charm == 5){
		showIngorelist($user_ida['id']);
	} else if($charm == 6){
		insertInIgnorelist($user_ida['id'], $ignoname);
	} else if($charm == 7){
		deleteFromIgnorelist($user_ida['id'], $id);
	}
		else{
		mainPage($user_ida['id'],$page);
	}
}
?>
