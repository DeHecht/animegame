<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once('path.inc.php'); // get the path ;)
include_once (ROOT_PATH . '/include/config.inc.php');
include_once (ROOT_PATH . '/include/parse.inc.php');
include_once (ROOT_PATH . '/include/fehlerausgabe.inc.php');
include_once (ROOT_PATH . '/include/abholcodes.inc.php');
include_once (ROOT_PATH . '/include/designfunctions.inc.php');
include_once (ROOT_PATH . '/include/schatz.inc.php'); // Hilfsfunktionen
include_once (ROOT_PATH . '/include/char.inc.php'); // Hilfsfunktionen
include_once (ROOT_PATH . '/include/semaphore.inc.php');
include_once (ROOT_PATH . '/include/img.inc.php');
include_once (ROOT_PATH . '/gfx/gfx_config.php');

// GET-Section
// Kritisch (SQL-Injections)
$char_id = validateUnsignedInteger($_GET['char_id'], null);
$stadt_name = validateName($_GET['stadt_name']);
$imgid = validateString($_GET['imgid']);
$phrase = validateString($_GET['phrase']);

// Unkritisch
$charm = $_GET['charm'];


// Semaphore
$ressource = 'User:' . $user_ida['id'];

if ($user_ida['id'] === NULL) {
	exit;
}

$quest = db_query('SELECT * FROM quest_ort');
$char = getChar($char_id);
if(!isUserOwnerOf($user_ida['id'], $char_id)){
	displayErrorMessage(NULL, 'Das ist nicht dein Char!!', displayHistoryBackLink());
	exit;
}

$c_hp = preg_split('#,#',$char['hp']);
if($c_hp[0] < 50){
	displayErrorMessage(NULL, 'Dein Char hat zu wenig HP (Mindestens 50)!!', displayHistoryBackLink());
	exit;
}
$auth = true;
if(hasToAuthenticate($user_ida)){
	if(checkImageCode($imgid, $phrase, $user_ida) ){
		resetUserCounter($user_ida);
	} else{
		$id = generateImageID();
		echo '<form action="schatz.php" method="GET">';
		while (list($key, $val) = each($_GET)) {
		    echo '<input type="hidden" name="'.$key.'" value="'.$val.'" />';
		}
		echo '<table width="100%" class="content"><tr><th class="content">Bitte Code eingeben</th></tr><tr><td align="center"><img src="img.php?id='.$id.'"></td></tr><tr><td align="center"><input class="input" name="phrase"/><input name="imgid" value="'.$id.'" type="hidden"/></td></tr><tr><td align="center"><input id="input" type="submit" value="eingeben"></td></tr></table></form>';
		$auth = false;
	}
}
if($auth == false){
	exit;
}

$char_ort = mysqli_fetch_assoc(db_query('Select * from quest_ort where id = '.$char['ort']));
$coords[0] = $char_ort['x_coord'];
$coords[1] = $char_ort['y_coord'];

if ($charm == 1) {
	if (!semaphoreUP($ressource)) { // Blockiere alle Aktionen die der User gleichzeitig ausfuehren will (Ausgaben, Chars etc.) um Problemen vorzubeugen!!
		displayErrorMessage(NULL, 'Verarbeitung gerade in Gange, bitte warten...', displayHistoryBackLink());
		exit;
	}

	$city = mysqli_fetch_assoc(db_query('SELECT * FROM quest_ort WHERE stadt=\'' . $stadt_name . '\''));
	$user_geld = getRelevantMoney($user_ida['id']);
	
	$coords2[0] = $city['x_coord'];
	$coords2[1] = $city['y_coord'];

	$std = max(round(sqrt(pow($coords[0]-$coords2[0],2) + pow($coords[1] - $coords2[1],2))/35),1);
	$kosten = $city['kosten']+round(log($std*0.1+1.25)*$city['kosten'],0);


	if ($user_geld < $kosten) {
		displayErrorMessage(NULL, 'Du hast nicht genug Geld!!', displayHistoryBackLink());
		semaphoreDown($ressource); // Gebe die Useraktionen wieder frei :)
		exit;
	}
	if ($city['reisende'] >= 750) {
		displayErrorMessage(NULL, $stadt_name . ' kann keine Besucher aufnehmen. Maximum von 750 erreicht!!', displayHistoryBackLink());
		semaphoreDown($ressource); // Gebe die Useraktionen wieder frei :)
		exit;
	}
	if ($city['id'] == $char['ort']) {
		displayErrorMessage(NULL, 'Du kannst nicht nach ' . $stadt_name . ' reisen, da du dort schon bist.!!', displayHistoryBackLink());
		semaphoreDown($ressource); // Gebe die Useraktionen wieder frei :)
		exit;
	}
	if ($char['status'] != 'Frei') {
		displayErrorMessage(NULL, 'Dein Char ist grad mit was anderem besch&auml;ftigt (' . $char['status'] . ')!!', displayHistoryBackLink());
		semaphoreDown($ressource); // Gebe die Useraktionen wieder frei :)
		exit;
	}

//	transferMonster();

	$load_monster = getMonster($char['level'], $city['id'], $std, $std*2+2);
	if($load_monster == NULL){
		displayErrorMessage(NULL, 'Dein Char traut sich nicht dorthin zu gehen, versuchs sp&auml;ter nochmal!!', displayHistoryBackLink());
		semaphoreDown($ressource); // Gebe die Useraktionen wieder frei :)
		exit;
	}

	// Change: Ort wird jetzt beim abholen Eingetragen
	db_query('UPDATE chars SET status=\'Schatz Suche\' WHERE id=' . $char_id);

	createAbholcode($char['id']);

	// Eine Schatzsuche kostet 6 Aktionspunkte
	hasDoneAction($user_ida, 6);
	monsterfight_neu($char, $load_monster, $std, $city['id']);
	db_query('UPDATE user SET geld=geld-' . $kosten . ' WHERE id=' . $user_ida['id']);
	displayErrorMessage(NULL, 'Die Reise nach ' . $city['stadt'] . ' kann beginnen sie endet in ' . $std . ' Stunden!!', '<a href=index.php?as=schatz_suche>Weiter..</a>');

	semaphoreDown($ressource); // Gebe die Useraktionen wieder frei :)
	exit;
}
?>
<html>
<head>
	<script language="JavaScript" src="java/quest.js" type="text/javascript"></script>
	<meta http-equiv="Content-Language" content="de">
</head>

<body bgcolor=D0CABA>
<table border = "0">
	<tr>
		<td witdh="200">
			<div id="Textbox" style="visibility:hidden;">
				<form action="schatz.php" method="get">
					<input type="hidden" name="charm" value="1">
					<input type="hidden" name="char_id" value="<?php echo $char_id; ?>">
					<table cellpadding="0" cellspacing="0" width="200">
						<tr>
							<th valign="top" align="center" id="stadt"></th>
						</tr>
						<tr>
							<th valign="top" align="center" id="reisende"></th>
						</tr>
						<tr>
							<th valign="top" align="center" id="monster"></th>
						</tr>
						<tr>
							<th valign="top" align="center" id="kosten"></th>
						</tr>
						<tr>
							<th valign="top" align="center" id="reisedauer"></th>
						</tr>
						<tr>
							<th valign="top" align="center" id="info"></th>
						</tr>
						<tr>
							<th valign="top" align="center" id="input"></th>
						</tr>
					</table>
				</form>
			</div>
		</td>
		<td>

		<div style="position:absolute; top:0px; left:200px; z-index:2">
			<img border="0" src="design/bilder/schatzsuche/onepiece.jpg" width="1274" height="804" />
		</div>

		<?php

		while ($row = mysqli_fetch_array($quest)) {
			$monster = getMonsterCount($row['id']);
			$her_cords = explode(',', $row['coords']);
			$reisende_rows = mysqli_fetch_assoc(db_query('SELECT count(id) as anzahl FROM chars WHERE ort=\'' . $row['id'] . '\''));
			$reisende_rows = $reisende_rows['anzahl'];
			if($row['name_bild'] != NULL){
				echo '<div style="position:absolute; top:'.$row['name_y'].'px; left:'.($row['name_x']+200).'px; z-index:3"><img border="0" src="design/bilder/schatzsuche/'.$row['name_bild'].'" /></div>'."\n";
			}
			echo '<div onmouseover="showText(\''.$row['stadt'].'\',\''.$reisende_rows.'\',\''.$monster['anzahl'].' ('.$monster['arten'].')\','.$row['kosten'].',\''.$row['info'].'\',\''.$coords[0].'\',\''.$coords[1].'\',\''.$row['x_coord'].'\',\''.$row['y_coord'].'\');" href="#" alt="'.$row['stadt'].'" style="position:absolute; top:'.$row['y_coord'].'px; left:'.($row['x_coord']+200).'px; z-index:10">
					<img border="0" src="design/bilder/schatzsuche/'.$row['bild'].'" />
				</div>
				';
		}
?>
		</td>
	</tr>
</table>
</body>
