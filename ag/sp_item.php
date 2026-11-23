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
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');
include_once(ROOT_PATH.'/include/random.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$item_id = validateUnsignedInteger($_GET['item_id'], null);
$char1 = validateUnsignedInteger($_REQUEST['char1'], null);
$char2 = validateUnsignedInteger($_REQUEST['char2'], null);
$new_name = validateName($_REQUEST['new_name']);

// Unkritisch
$ab = $_REQUEST['ab'];
$ak = $_REQUEST['ak'];

function useChest($item_id, $user){
		// So nun nutzen
	db_query('DELETE from sp_ware WHERE item='.$item_id.' and user = '.$user['id'].' LIMIT 1');
	if(db_affected_rows() == 1){
		$row = mysqli_fetch_assoc(db_query('SELECT wert FROM sp_item where id = '.$item_id));

		// Calc the treasure
		$treasure = mt_random_wrapper(0,1000) * $row['wert'];
//		$treasure = 0;
		if($treasure == 0){ // Muhahaha -> failed
			$nothin_msg = array('Da war wohl jemand vor dir da!', 'Die Schatztruhe ist voller Steine!', 'Naja, dabei sein is alles, ne??!', 'Diese Schatztruhe war leer, vielleicht klappts ja das nächte Mal');
			displayErrorMessage('Schatztruhe geöffnet','Niete!! ' . $nothin_msg[mt_random_wrapper(0, count($nothin_msg)-1)],'<a href="index.php?as=item">zurück...</a>');
			return;
		} else {
			displayErrorMessage('Schatztruhe geöffnet','GOLD!! Du findest ganze '.$treasure.' ¥','<a href="index.php?as=item">zurück...</a>');
			db_query('UPDATE user SET geld = geld + '.$treasure.' WHERE id = '.$user['id']);
			return;
		}
	} else{
		displayErrorMessage('Item nicht genutzt','Du hast garkeine Schatztruhe mehr!!','<a href="index.php?as=item">zurück...</a>');
		return;
	}
}

function useBohne($item_id, $user){
	$chars = getCharsOfUser($user['id']);
	$required = false;
	foreach($chars as $row){
		$hp = explode(',', $row['hp']);
		$mp = explode(',', $row['mp']);
		if($hp[0] != $hp[1] || $mp[0] != $mp[1]){
			$required = true;
		}
	}

	if(!$required){
		displayErrorMessage('Item nicht genutzt','All deine Chars sind voll geheilt!!','<a href="index.php?as=item">zurück...</a>');
		return;
	}
	// So nun nutzen
	db_query('DELETE from sp_ware WHERE item='.$item_id.' and user = '.$user['id'].' LIMIT 1');

	foreach($chars as $row){
		$hp = explode(',', $row['hp']);
		$mp = explode(',', $row['mp']);
		db_query('UPDATE chars SET hp=\''.$hp[1].','.$hp[1].'\', mp=\''.$mp[1].','.$mp[1].'\' WHERE id='.$row['id']);
	}

	displayErrorMessage('&Auml;nderungen &uuml;bernommen','Item erfolgreich benutzt. Die HP und MP all deiner Chars sind wieder voll hergestellt!!','<a href="index.php?as=item">weiter...</a>');
}

// Die Funktion die die Fusion wirklich durchfuehrt!!
function usePotaraReally($user, $item_id, $char1, $char2, $new_name = ''){
	if($new_name === NULL || $new_name == ''){
		displayErrorMessage(NULL,'Name nicht angegeben',displayHistoryBackLink());
	}

	$char_v_1 = getChar($char1);
	$char_v_2 = getChar($char2);

	if($char_v_1['name'] != $new_name && $char_v_2['name'] != $new_name){ // Der Name der Fusion darf auch der Name eines der Chars sein
		$char_v_3 = mysqli_fetch_array(db_query('SELECT id FROM chars WHERE name=\''.$new_name.'\' LIMIT 1'));
	}

	if($char_v_1['besitzer'] != $user['id'] || $char_v_2['besitzer'] != $user['id']) {
		displayErrorMessage(NULL,$char_v_1['name'].' oder '.$char_v_2['name'].' geh&ouml;rt dir nicht!!',displayHistoryBackLink());
		return;
	}else if($char_v_1['name'] == $char_v_2['name']) {
		displayErrorMessage(NULL,'2x den gleichen Char zu fusioniern ist nicht m&ouml;glich!!',displayHistoryBackLink());
		return;
	}else if($new_name === NULL OR $char_v_3['id'] !== NULL) {
		displayErrorMessage(NULL,'Der Name '.$new_name.' ist schon vorhanden!!',displayHistoryBackLink());
		return;
	}else if($char_v_1['fusion'] != 'nein' || $char_v_2['fusion'] != 'nein') {
		displayErrorMessage(NULL,$char_v_1['name'].' oder '.$char_v_2['name'].' ist schon Fusioniert!!',displayHistoryBackLink());
		return;
	}else if($char_v_1['status'] != 'Frei' || $char_v_2['status'] != 'Frei'){
		displayErrorMessage(NULL,$char_v_1['name'].' oder '.$char_v_2['name'].' ist nicht frei!!',displayHistoryBackLink());
		return;
	}


	$hp1 = explode(',', $char_v_1['hp']);
	$mp1 = explode(',', $char_v_1['mp']);
	$hp2 = explode(',', $char_v_2['hp']);
	$mp2 = explode(',', $char_v_2['mp']);
	$new_hp = ($hp1[1] + $hp2[1]) ;
	$new_mp = ($mp1[1] + $mp2[1]);

	$stats = array( 'name' => '\''.$new_name.'\'',
			'rasse' => $char_v_1['rasse_a']['id'],
			'bild' => '\''.$char_v_1['bild'].'\'',
			'besitzer' => $char_v_1['besitzer'],
			'hp' => '\''.$new_hp.','.$new_hp.'\'',
			'mp' => '\''.$new_mp.','.$new_mp.'\'',
			'exp' => '\''.$char_v_1['exp'].'\'',
			'fusion_rasse' => $char_v_2['rasse_a']['id'],
			'fusion' => '\'ja\'',
			'level' => $char_v_1['level'],
			'ort' => $char_v_1['ort'],
			'clan_train' => $char_v_1['clan_train']
	);

	$sums = array('starke', 'verteidigung', 'speed', 'ausdauer', 'glueck', 'training_points', 'lernpunkte');
	foreach ($sums as $key) {
		$stats[$key] = $char_v_1[$key] + $char_v_2[$key];
	}

	$keys = array();
	$values = array();
	foreach ($stats as $key => $value) {
		$keys[] = $key;
		$values[] = $value;
	}
	
	$sql = 'INSERT INTO chars('.join(',', $keys).') values('.join(',', $values).')';

	// echo $sql.'<br>';

	$qry = db_query($sql);
//	Umkopieren der Techniken

	// Hat das Update geklappt? Wenn nein abbrechen
	if(db_affected_rows() <= 0){
		displayErrorMessage('Schwerwiegender Fehler','Fusion konnte nicht durchgeführt werden, melden sie sich bei einem GM!!',displayHistoryBackLink());
		return;
	}
	// Fetch new charid
	$char3 = mysqli_fetch_row(db_query('SELECT id FROM chars WHERE name = \''.$new_name.'\''))[0];	

	db_query('DELETE from sp_ware WHERE item='.$item_id.' and user = '.$user['id'].' LIMIT 1'); // Potara loeschen!!

	$qry = db_query('select at_id, name, sum(benutzt) as benutzt from lernen where besitzer IN('.$char1.', '.$char2.') GROUP BY at_id;');
	while ($row = mysqli_fetch_assoc($qry)) {
		db_query('INSERT INTO lernen(name, at_id, besitzer, dauer, aktiv, benutzt) values(\''.$row['name'].'\', '.$row['at_id'].', '.$char3.', 0, 1, '.$row['benutzt'].')');
	}

	db_query('DELETE FROM lernen WHERE besitzer IN('.$char1.', '.$char2.')');

	db_query('DELETE FROM chars WHERE id IN('.$char1.', '.$char2.')'); // Char kann erst jetzt gelöscht werden

	displayErrorMessage('&Auml;nderungen &uuml;bernommen','Charakter: '.$new_name.' erfolgreich fusioniert!!','<a href="index.php?as=char_index">weiter...</a>');
	return;
}

function confirmPotaraUse($item_id, $char1, $char2, $new_name){
	$char_v_1 = getChar($char1);
	$char_v_2 = getChar($char2);

	$weiter_an = '<a href="index.php?as=sp_item&ab=1&item_id='.$item_id.'&new_name='.$new_name.'&char1='.$char1.'&char2='.$char2.'&ak=1">Ja</a>' .
			' | <a href="index.php">abbrechen</a>';
	displayErrorMessage('Frage','Wollen sie echt die Chars '.$char_v_1['name'].' + '.$char_v_2['name'].' fusionieren??',$weiter_an);
}

function askForPotaraUse($user, $item_id){
	// Spezielle abfrage in der Char-Tabelle (Koenne aber durch Puffern schneller sein, als diese SQL-Anfrage)
	$all_charakter = db_query('SELECT id, name FROM chars WHERE besitzer='.$user['id'].' and fusion != \'ja\'');
	$options = '';
	while($row = mysqli_fetch_array($all_charakter)) {
		$options.=  '<option value='.$row['id'].'>'.$row['name'].'</option>';
	}
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
		<input type="hidden" name="as" value="sp_item">
		<input type="hidden" name="ab" value="1">
		<input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
		<table width="100%">
			<tr>
				<td>Char1:</td>
				<td><select name="char1" id="input"><?php echo $options; ?></select></td>
			</tr>
			<tr>
				<td>Char2:</td>
				<td><select name="char2" id="input"><?php echo $options; ?></select></td>
			</tr>
			<tr>
				<td colspan="2">
					<input id="input" size="20" name="new_name" value="Neuer Name">
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" id="input" value="fusionieren">
				</td>
			</tr>
		</table>
	</form>
	<?php
}


// This function has been changed (item_id points now on item-class not on an single object)
function useItem($user,$item_id, $ab, $ak, $char1, $char2, $new_name){
	$item = mysqli_fetch_assoc(db_query('SELECT si.name, si.info, si.id, count(sw.item) as anzahl FROM sp_item si LEFT JOIN (Select item from sp_ware where user = '.$user['id'].') sw ON sw.item = si.id WHERE si.id = '.$item_id));

	if($item['anzahl'] <= 0){
		displayErrorMessage('Kann Item nicht nutzen', 'Du hast nicht genügend Items vom Typ '.$item['name'], displayHistoryBackLink());
		return;
	}

	if($ab !== NULL && $item['id'] == 2){ // Bohne
		useBohne($item_id,$user);
	} else if($ab !== NULL && $item['id'] == 1){
		if($ak == 1){
			usePotaraReally($user, $item_id, $char1, $char2, $new_name);
		} else if($char1 !== NULL AND $char2 !== NULL) {
			confirmPotaraUse($item_id, $char1, $char2, $new_name);
		} else{
			askForPotaraUse($user, $item_id);
		}
	} else if($ab !== NULL && ($item['id'] == 404 || $item['id'] == 415)){ // Chests
		useChest($item_id, $user);
	} else {
		display($item_id, $item['name'],$item['info']);
	}
}

function display($item_id, $itemname, $iteminfo){
	displayErrorMessage($itemname, $iteminfo,'<a href="index.php?as=sp_item&ab=1&item_id='.$item_id.'">benutzen</a> | '.displayHistoryBackLink());
}

if(is_numeric($item_id)){ // Die Seite kann nur mit g&uuml;ltiger Itemid aufgerufen werden!!
	useItem($user_ida, $item_id, $ab, $ak, $char1, $char2, $new_name);
} else{
	displayErrorMessage(NULL, 'Itemid ist nicht numerisch!!',displayHistoryBackLink());
}

?>
