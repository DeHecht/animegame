<?php
/*
 * Created on 21.08.2007
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
session_start();

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

/******************************************************************************
* Dieser Skript ist ein Kontrollpanel fuer GM's
*******************************************************************************
* Autor: Pascal Proksch
* Erstellungsdatum: 21.08.2007
* Zuletzt verändert: 03.10.2007
*******************************************************************************/

include_once('../path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config/db.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/bann.inc.php');
include_once(ROOT_PATH.'/include/faq.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/rassen.inc.php');
include_once(ROOT_PATH.'/include/user.inc.php');
include_once(ROOT_PATH.'/include/usergroup.inc.php');


function logaction($message){
	$sql = 'Insert into gm_actions(userid, time, message, ip) values('.$_SESSION['user'].', now(), \''.$message.'\', \''.$_SERVER['REMOTE_ADDR'].'\')';
	db_query($sql);
}

// Include all sub-pages
if ($handle = opendir(ROOT_PATH.'/gm/include')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry[0] != '.') {
		include_once(ROOT_PATH.'/gm/include/'.$entry);
        }
    }
    closedir($handle);
}


function showActionLog(){
	$qry = db_query('Select g.time, u.nickname, g.ip, g.message from gm_actions as g inner join user as u on g.userid = u.id order by time desc LIMIT 100');
	while($row = mysqli_fetch_assoc($qry)){
		echo $row['time'].' '.$row['nickname'].' ('.$row['ip'].'): '.$row['message'].'<br>'."\n";
	}
	echo '<a href="'.$_SERVER['PHP_SELF'].'">Zum Hauptmenu</a>';
}

// Returns bool
function checkLoginData($userid, $password){
	if(checkSessionPasswort($userid, $password)){
		return true;
	} else{
		logout();
		return false;
	}
}

function loginUser($user,$pass){
	if(checkLoginPassword($user,$pass)){
		$test = 'SELECT * from user where nickname = \''.$user.'\'';
		$qry = db_query($test);
		$result = mysqli_fetch_assoc($qry);
		$usergroups = getUserGroups($user);

		if(isUserInGroup($usergroups, ADMIN)) {
			$_SESSION['user'] = $result['id'];
			$_SESSION['username'] = $result['nickname'];
			$_SESSION['password'] = $result['passwort'];
			$_SESSION['usergroups'] = $usergroups;
			echo '<a href="'.$_SERVER['PHP_SELF'].'">Zum Hauptmenu</a>';
			return true;
		}
		return false;
	}
	echo displayHistoryBackLink();
	return false;
}

function displayLogin(){
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
		<table>
			<tr>
				<td>
					<input name="user"></input>
				</td>
			</tr>
			<tr>
				<td>
					<input name="password" type="password"></input>
				</td>
			</tr>
			<tr>
				<td>
					<input type="submit"></input>
				</td>
			</tr>
		</table>
	</form>
	<?php
}

function displayOptions(){
	// FIXME: Hinzufuegen der Includes und der Entry Funktion im Array und wir brauchen
	// in dieser Datei deutlich weniger Copy&Paste!
	
	$entries = array("bilder" => "Bilderschau",
			"faq" => "FAQ bearbeiten",
			"rassen" => "Rassen bearbeiten",
			"arena" => "Arena bearbeiten",
			"attacken" => "Attacken bearbeiten",
			"attackentypen" => "Attackentypen bearbeiten",
			"poll" => "Umfragen bearbeiten",
			"items" => "Items bearbeiten",
			"rezept" => "Rezepte bearbeiten",
			"clan" => "Clan bearbeiten",
			"geheim" => "Geheim bearbeiten",
			"usergroups" => "Nutzergruppen bearbeiten",
			"bann" => "Banns und andere unsch&ouml;ne Sachen",
			"tournament" => "Turniere bearbeiten",
			"qnpc" => "Schatzsuche-Monster bearbeiten",
			"npc" => "NPC bearbeiten",
			"shop" => "Shakkys Shop Angebote bearbeiten",
			"news" => "News bearbeiten",
			"cheat" => "Multiuser und Cheater",
			"log" => "Log ansehen",
			"tutorial" => "Tutorial bearbeiten",
			"defines" => "Konstanten anpassen",
			"present" => "Geschenke verteilen"
	);
	asort($entries, SORT_STRING);
	$entries["logout"] = "Log out";
	
	echo '<table>'."\n";
	foreach($entries as $key => $value) {
		echo '<tr><td><a href="'.$_SERVER['PHP_SELF'].'?choose='.$key.'">'.$value.'</a></td></tr>'."\n";
	}
	echo '</table>'."\n";
}

function logout(){
	session_unset();
}

function notyetimplemented(){
	?>
		<table>
			<tr>
				<td>
					Leider ist diese Funktion noch nicht implementiert!
				</td>
			</tr>
			<tr>
				<td>
					<?php 	echo '<a href="'.$_SERVER['PHP_SELF'].'">Zum Hauptmenu</a>'; ?>
				</td>
			</tr>
		</table>
	<?php
}

if(checkLoginData($_SESSION['user'],$_SESSION['password'])){
	// Funtionen
	if($_REQUEST['choose'] == 'bilder'){
		zeigeBilder($_REQUEST['begin'],$_REQUEST['bildurl'], $_REQUEST['sure'], $_REQUEST['view'], $_REQUEST['letter'], $_REQUEST['date_from'], $_REQUEST['date_until']);
	}	else if($_REQUEST['choose'] == 'logout'){
		logout();
	}	else if($_REQUEST['choose'] == 'attacken'){
		displayAttackenMenu($_REQUEST['action'], $_REQUEST['attackname'], $_REQUEST['attackid'], $_REQUEST['depth'], $_REQUEST['name'], $_REQUEST['starke'], $_REQUEST['verteidigung'], $_REQUEST['speed'], $_REQUEST['hp'], $_REQUEST['mp'], $_REQUEST['rassen'], $_REQUEST['level'], $_REQUEST['geld'], $_REQUEST['options'], $_REQUEST['type'], $_REQUEST['info'], $_REQUEST['req_atk'], $_REQUEST['req_lvl'], $_REQUEST['Frucht'], $_REQUEST['runden'], $_REQUEST['maxlvl'], $_REQUEST);
	}	else if($_REQUEST['choose'] == 'attackentypen') {
		displayAttackenTypen($_REQUEST);
	}	else if($_REQUEST['choose'] == 'bann'){
		displayBannOptions($_REQUEST['action'], $_REQUEST['username'],$_REQUEST['opfer'],$_REQUEST['gamebann'],$_REQUEST['shoutbann'],$_REQUEST['forenbann'],$_REQUEST['anonid']);
	}	else if($_REQUEST['choose'] == 'items'){
		displayItems($_REQUEST['action'], $_REQUEST['task'], $_REQUEST['itemid'], $_REQUEST['itemtable'], $_REQUEST['searchstring'],$_REQUEST['s_itemtable'],$_REQUEST['page'], $_REQUEST['data']);
        } else if($_REQUEST['choose'] == 'rezept') {
		displayRezepte($_REQUEST);
	}	else if($_REQUEST['choose'] == 'faq'){
		displayFaq($_REQUEST['action'], $_REQUEST['faqentry'],$_REQUEST['parent'],$_REQUEST['header'],$_REQUEST['body'], $_REQUEST['child']);
	}	else if($_REQUEST['choose'] == 'qnpc'){
		displayQuestNPC($_REQUEST['action'],$_REQUEST['npc_name'],$_REQUEST['starke'],$_REQUEST['speed'],$_REQUEST['verteidigung'],$_REQUEST['ausdauer'],$_REQUEST['hp'],$_REQUEST['mp'],$_REQUEST['level'],$_REQUEST['orte'],$_REQUEST['id'], $_REQUEST['anzahl'], $_REQUEST['confirm'], $_REQUEST['glueck']);
	}	else if($_REQUEST['choose'] == 'poll'){
		displayPolls($_REQUEST['action'], $_REQUEST['openumfrage'], $_REQUEST['closeumfrage'],$_REQUEST['thema'],$_REQUEST['optionen'],$_REQUEST['text'],$_REQUEST['pollid']);
	}	else if($_REQUEST['choose'] == 'clan'){
		displayClanMenu($_REQUEST['action'],$_REQUEST['raumid'],$_REQUEST['raumname'],$_REQUEST['staerke'],$_REQUEST['verteidigung'],$_REQUEST['glueck'],$_REQUEST['ausdauer'],$_REQUEST['geschwindigkeit'], $_REQUEST['info'], $_REQUEST['req_level'], $_REQUEST['ccfee'], $_REQUEST['data']);
	}	else if($_REQUEST['choose'] == 'drop'){
		displayDrops();
	}	else if($_REQUEST['choose'] == 'news'){
		displayNews($_REQUEST['action'], $_SESSION['username'], $_REQUEST['newsid'], $_REQUEST['header'], $_REQUEST['value']);
	}	else if($_REQUEST['choose'] == 'log'){
		showActionLog();
	}	else if($_REQUEST['choose'] == 'cheat'){
		displayCheater($_REQUEST['action'], $_REQUEST['from'], $_REQUEST['until'], $_REQUEST['user1'], $_REQUEST['user2'], $_REQUEST['tradeid'], $_REQUEST['verify']);
	}	else if($_REQUEST['choose'] == 'npc'){
		displayNPC($_REQUEST['action'], $_REQUEST['task'], $_REQUEST['charid'], $_REQUEST['table'], $_REQUEST['page'], $_REQUEST['data'], $_REQUEST['att_ids']);
	}   else if($_REQUEST['choose'] == 'shop'){
		displayShop($_REQUEST['action'], $_REQUEST['s_id'],$_REQUEST['info'], $_REQUEST['s_name'] , $_REQUEST['s2_name'], $_REQUEST['s3_name'],  $_REQUEST['s_typ'], $_REQUEST['preis'], $_REQUEST['frei'], $_REQUEST['anzahl']);
	}	else if($_REQUEST['choose'] == 'usergroups'){
		displayUserGroups($_REQUEST['action'] ,$_REQUEST['uname'] ,$_REQUEST['ugname'], $_REQUEST['delete'], $_REQUEST['add']);
	}	else if($_REQUEST['choose'] == 'rassen'){
		displayRassen($_REQUEST);
	}	else if($_REQUEST['choose'] == 'geheim'){
		displayGeheim($_REQUEST);
	} else if($_REQUEST['choose'] == 'arena') {
		displayArena($_REQUEST);
	} else if($_REQUEST['choose'] == 'tutorial') {
		displayTutorial($_REQUEST);
	} else if($_REQUEST['choose'] == 'present') {
		displayPresent($_REQUEST);
	} else if($_REQUEST['choose'] == 'defines') {
		displayDefines($_REQUEST);
	} else if($_REQUEST['choose'] == 'tournament'){
		//_POST['name'] anstatt _REQUEST weil schon durch username überschrieben wird
		displayTournamentMenu($_REQUEST);
	}else {
		displayOptions();
	}
} else if(isset($_POST['user'])){
	if(!loginUser($_POST['user'],$_POST['password'])){
		echo 'Login ist fehlgeschlagen (Vielleicht vertippt '.$_POST['user'].'?)!!<br>';
		displayLogin();
	}
} else{
	// Einloggen
	displayLogin();
}

?>
