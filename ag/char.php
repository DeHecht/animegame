<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
/**
 * Diese Datei wird aufgerufen wenn "Charakter erstellen" ausgewaehlt wurde.
 * TODO: Layout anpassen (keine <center>, <font> und <strong> Tags mehr)
 * TODO: Alle "" Strings in '' Strings umwandeln
 * TODO: Funktioneller Aufruf, damit exit-Tags verschwinden koennen und somit der Chat angezeigt werden kann!!
 */
include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/erstellfunctions.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/usergroup.inc.php');
include_once(ROOT_PATH.'/include/exp.inc.php');
include_once(ROOT_PATH.'/include/rassen.inc.php');


// GET-Section
// Kritisch (SQL-Injections)
$char_name = validateName($_GET['char_name']);
$char_1_type = validateString($_GET['char_1_Type']);
$char_bild = validateURL($_GET['char_bild']);
$char_lvl = validateInteger($_GET['char_lvl'], NULL);
$randomize = validateInteger($_GET['random'], NULL);

// Unkritisch
$charm = $_REQUEST['charm'];
$char_type = $_GET['char_type'];

?>
<SCRIPT language="JavaScript">
<!--

function isAPhoneNumber(){
   var s = "" + document.charz.char_name.value; //Umwandlung in eine Zeichenkette
   var zeichen = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_ !" //Gueltige Zeichen
   for (var i=0; i<s.length; i++){
      if (zeichen.indexOf(s.charAt(i))==-1){
         //kein gueltiges Zeichen
         return false;
       }
   }
   return true;
}


-->
</SCRIPT>
<?php

//if($charm == 2) {

// $user_ida, $char_1_Type, $char_name
function handleErstelleCharRequest($user, $type, $name, $bild){
	$race = getRaceById($type);
	$rtype = getRaceTypeById($race['type']);
	if($race == NULL || $race['special'] || $rtype['gm_only'] || $rtype == NULL ) {
		if($race == NULL) {
			displayErrorMessage(NULL,'Rasse kann nicht erstellt werden. Rasse mit ID '.$type.' existiert nicht!',displayHistoryBackLink());
		} elseif( $race['special'] == true ) {
			displayErrorMessage(NULL,'Rasse kann nicht erstellt werden! Sie kann nur durch Dragonball wünsche bekommen werden!',displayHistoryBackLink());
		} elseif( $rtype['gm_only'] == true ) {
			displayErrorMessage(NULL,'Rasse kann nicht erstellt werden! Nur GMs können '.$race['name'].' erstellen!',displayHistoryBackLink());
		} else {
			displayErrorMessage(NULL,'Rasse kann nicht erstellt werden! Wir wissen noch nicht warum!',displayHistoryBackLink());
		}
		return;
	}
	if(erstelleChar($user, $type, $name, $bild)){
		displayErrorMessage('&Auml;nderungen &uuml;bernommen','','<a href="index.php?as=char">weiter</a>');
	}
}

function handleErstelleCharRequestOfTestUser($user, $type, $name, $bild, $lvl, $lp, $hp, $mp, $str, $def, $spd, $end, $lck){
	//stats auf numeric prüfen!
	$race = getRaceById($type);
	if($race == NULL) {
		displayErrorMessage(NULL,'Rasse kann nicht erstellt werden!',displayHistoryBackLink());
		return;
	}
	if(!is_numeric($hp) || !is_numeric($mp) || !is_numeric($str) || !is_numeric($def) || !is_numeric($spd) || !is_numeric($end) || !is_numeric($lck)) {
		displayErrorMessage(NULL,'Ungültige Eingabe bei den Stats!',displayHistoryBackLink());
		return;
	}
	if($lp < ($hp+$mp+$str+$def+$spd+$end+$lck)) {
		displayErrorMessage(NULL,'Es wurden mehr Lernpunkte ausgegeben als eigentlich möglich!',displayHistoryBackLink());
		return;
	}
	$lp -= ($hp+$mp+$str+$def+$spd+$end+$lck);
	if(erstelleChar($user, $type, $name, $bild, $lvl, $lp, $hp, $mp, $str, $def, $spd, $end, $lck)){
		displayErrorMessage('&Auml;nderungen &uuml;bernommen','','<a href="index.php?as=char">weiter</a>');
	}
}

//if($charm !== NULL) {

function handleSecondPhase($type, $name){
	?>
<form action="index.php" method="get">
	<input type="hidden" name="as" value="char"> <input type="hidden"
		name="charm" value="2"> <input type="hidden" name="char_name"
		value="<?php echo $name; ?>">
	<table border="0" cellpadding="0" cellspacing="0"
		style="border-collapse: collapse" bordercolor="#111111" width="100%"
		height="20">
		<tr>
			<th colspan="2" align="center">Charakter erstellen (2/2)</th>
		</tr>
		<tr>
			<th align="left">Rasse:</th>
			<td><select id="input" name="char_1_Type">
			<?php

			$race_type = getRaceTypeById($type);
			if($race_type == NULL || $race_type['gm_only']) {
				echo '<option value="Cheater">Cheater</option>'."\n";
			} else {
				$choices = array();
				$rassen = getRassen();

				foreach($rassen as $rasse) {
					if($rasse['special']) {
						// Normal users may not create special chars here!
						continue;
					}
					if(!isset($choices[$rasse['type']])){
						$choices[$rasse['type']] = array();
					}
					$choices[$rasse['type']][] = array('id' => $rasse['id'], 'name'=>$rasse['name']);
				}

				if(isset($choices[$type])) {
					foreach($choices[$type] as $rasse) {
						echo '<option value="'.$rasse['id'].'">'.$rasse['name'].'</option>'."\n";
					}
				} else {
					echo '<option value="Cheater">Cheater</option>'."\n";
				}
			}

			?>
			</select>
			</td>
		</tr>
		<tr>
			<th align="left">Charakter Bild (75x75):</th>
			<td><input name="char_bild" id="input">
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input id="input" type=submit value="weiter">
			</td>
		</tr>
	</table>
</form>
			<?php
}

function handleSecondPhaseForTester($type, $name){
	?>
<form action="index.php" method="GET">
	<input type="hidden" name="as" value="char"> <input type="hidden"
		name="charm" value="3"> <input type="hidden" name="char_name"
		value="<?php echo $name; ?>">
	<table border="0" cellpadding="0" cellspacing="0"
		style="border-collapse: collapse" bordercolor="#111111" width="100%"
		height="20">
		<tr>
			<th colspan="2" align="center">Charakter erstellen (2/3)</th>
		</tr>
		<tr>
			<th align="left">Rasse:</th>
			<td><select id="input" name="char_1_Type">
                        <?php
                                $choices = array();
                                $rassen = getRassen();

                                foreach($rassen as $rasse) {
                                        if(!isset($choices[$rasse['type']])){
                                                $choices[$rasse['type']] = array();
                                        }
                                        $choices[$rasse['type']][] = array('id' => $rasse['id'], 'name'=>$rasse['name']);
                                }

                        if(isset($choices[$type])) {
                                foreach($choices[$type] as $rasse) {
                                        echo '<option value="'.$rasse['id'].'">'.$rasse['name'].'</option>'."\n";
                                }
                        } else {
                                echo '<option value="Cheater">Cheater</option>'."\n";
                        }

                        ?>

			</select>
			</td>
		</tr>
		<tr>
			<th align="left">Charakter Bild (75x75):</th>
			<td><input name="char_bild" id="input">
			</td>
		</tr>
		<tr>
			<th align="left">Level(1-150):</th>
			<td><input name="char_lvl" id="input">
			</td>
		</tr>
		<tr>
			<th align="left">Zufall:</th>
			<td><input type="checkbox" name="random" value="1">
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input id="input" type=submit value="weiter">
			</td>
		</tr>
	</table>
</form>
			<?php
}

function handleThirdPhaseForTester($type, $name, $char_lvl, $random, $user){
	$type_arr = getRaceById($type);
	if(($char_lvl >= 1 && $char_lvl <= 150) && $char_lvl !== NULL && is_numeric($char_lvl)) {
		$lp = 0;
		for ($i = 1; $i < $char_lvl; $i++) {
			$lp += calculateTrainingPoints($i);
		}
		$lp = round($lp / 2);
		if($random == 1) {
			$tmp_lp = $lp;
			$hp = rand(0, $lp/2);
			$lp -= $hp;
			$mp = rand(0, $lp/2);
			$lp -= $mp;
			$str = rand(0, $lp);
			$lp -= $str;
			$def = rand(0, $lp);
			$lp -= $def;
			$spd = rand(0, $lp);
			$lp -= $spd;
			$lck = rand(0, $lp);
			$lp -= $lck;
			$end = rand(0, $lp);
			$lp -= $end;
			handleErstelleCharRequestOfTestUser($user, $type, $name, '', $char_lvl, $tmp_lp, $hp, $mp, $str, $def, $spd, $end, $lck);
		} else {
			?>
<form action="index.php" method="POST">
	<input type="hidden" name="as" value="char"> <input type="hidden"
		name="charm" value="4"> <input type="hidden" name="char_type"
		value="<?php echo $type; ?>"> <input type="hidden" name="char_name"
		value="<?php echo $name; ?>"> <input type="hidden" name="lvl"
		value="<?php echo $char_lvl; ?>"> <input type="hidden" name="lp"
		value="<?php echo $lp; ?>">
	<table border="0" cellpadding="0" cellspacing="0"
		style="border-collapse: collapse" bordercolor="#111111" width="100%"
		height="20">
		<tr>
			<th colspan="2" align="center">Charakter erstellen (3/3)</th>
		</tr>
		<tr>
			<th align="left">CharakterTyp:</th>
			<td><?php echo $type_arr['name'];?>
			</td>
		</tr>
		<tr>
			<th align="left">Charakterlevel:</th>
			<td><?php echo $char_lvl;?>
			</td>
		</tr>
		<tr>
			<th align="left">verfügbare LP:</th>
			<td><?php echo $lp?></td>
		</tr>
		<tr>
			<th align="left">HP (1LP = 10HP):</th>
			<td><input name="hp" value="0" id="input">
			</td>
		</tr>
		<tr>
			<th align="left">MP (1LP = 5MP):</th>
			<td><input name="mp" value="0" id="input">
			</td>
		</tr>
		<tr>
			<th align="left">St&auml;rke:</th>
			<td><input name="str" value="0" id="input">
			</td>
		</tr>
		<tr>
			<th align="left">Verteidigung:</th>
			<td><input name="def" value="0" id="input">
			</td>
		</tr>
		<tr>
			<th align="left">Geschwindigkeit:</th>
			<td><input name="spd" value="0" id="input">
			</td>
		</tr>
		<tr>
			<th align="left">Ausdauer:</th>
			<td><input name="end" value="0" id="input">
			</td>
		</tr>
		<tr>
			<th align="left">Glück:</th>
			<td><input name="lck" value="0" id="input">
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input id="input" type=submit value="weiter">
			</td>
		</tr>
	</table>
</form>
			<?php 
		}
	} else
			echo 'Ungültiger Levelbereich!';
	}

	function handleFirstPhase($user_daten, $tester){
		?>
<form action="index.php" method="get" name="charz"
	onsubmit="return isAPhoneNumber()">
	<input type="hidden" name="as" value="char"> <input type="hidden"
		name="charm" value="1">
	<table border="0" cellpadding="0" cellspacing="0"
		style="border-collapse: collapse" bordercolor="#111111" width="100%"
		height="25">
		<tr>
			<th colspan="2" align="center">Charakter Erstellen (1/2)</th>
		</tr>
		<tr>
			<th align="center">Charaktername</th>
			<td><input id="input" name="char_name" size="20"></input>
				(Buchstaben)</td>
		</tr>
		<tr>
			<th align="center">Type</th>
			<td><select id="input" name="char_type">
				<?php
					$types = getRassenTypeMapping();
					foreach($types as $type) {
						if(!$type['gm_only'] || $tester) {
							echo '<option value="'.$type['id'].'">'.$type['name'].'</option>'."\n";
						}
					}
				?>
			</select>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input id="input" type="submit" value="weiter"></td>
		</tr>
	</table>
</form>
		<?php
	}

	$user_daten= mysqli_fetch_assoc(db_query("SELECT char_max, id FROM user WHERE nickname='".$_COOKIE['name']."' LIMIT 1"));
	// FIXME: Check if thi works!
        $anzahl_spezialchars = 0;
	$all_chars = getCharsOfUser($user_daten['id']);
	foreach($all_chars as $char) {
		if(!is_numeric($char['rasse']) && $char['rasse'] == "Kaioshin" || $char['rasse'] == "Shichibukai") {
			$anzahl_specialchars++;
			continue;
		} 
		if(is_numeric($char['rasse'])) {
			$rasse = getRaceById($char['rasse']);
			if($rasse != NULL && $rasse['special']){
				$anzahl_specialchars++;
				continue;
			}
		}
		if(!is_numeric($char['fusion_rasse']) && $char['fusion_rasse'] == "Kaioshin" || $char['fusion_rasse'] == "Shichibukai") {
			$anzahl_specialchars++;
			continue;
		}	
		if(is_numeric($char['fusion_rasse'])) {
			$rasse = getRaceById($char['fusion_rasse']);
			if($rasse != NULL && $rasse['special']){
				$anzahl_specialchars++;
				continue;
			}
		}

	}
	
	$check = hasUserFreeCharSlot($user_daten);

	if ($check) {
		if($charm === NULL){
			handleFirstPhase($user_daten, isUserInGroup($usergroups, TESTER));
		} else if($charm == 1){
			if(isUserInGroup($usergroups, TESTER)) {
				handleSecondPhaseForTester($char_type, $char_name);
			} else {
				handleSecondPhase($char_type, $char_name);
			}
		} else if($charm == 2){
			handleErstelleCharRequest($user_ida, $char_1_type, $char_name, $char_bild);
		} else if($charm == 3 && isUserInGroup($usergroups, TESTER)) {
			handleThirdPhaseForTester($char_1_type, $char_name, $char_lvl, $randomize, $user_ida);
		} else if($charm == 4 && isUserInGroup($usergroups, TESTER)) {
			handleErstelleCharRequestOfTestUser($user_ida, $_REQUEST['char_type'], $_REQUEST['char_name'], $_REQUEST['char_bild'], $_REQUEST['lvl'], $_REQUEST['lp'],
		 $_REQUEST['hp'], $_REQUEST['mp'], $_REQUEST['str'], $_REQUEST['def'], $_REQUEST['spd'], $_REQUEST['end'], $_REQUEST['lck']);
		}
	} else {
		$anzahl_charactere = count(getCharsOfUser($user_daten['id']));
		$chars_max = $user_daten['char_max'];
		echo 'Du hast bereits die maximal mögliche Menge an Characteren erstellt, die du erstellen kannst.<br />Nämlich '.$anzahl_charactere.' von '.$chars_max.' Charaktere.';
	}


	?>
