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
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/rassen.inc.php');


include_once(ROOT_PATH.'/gfx/gfx_config.php');

// GET-Section
// Kritisch (SQL-Injections)
$info = validateUnsignedInteger($_GET['info'], null);
$id = validateUnsignedInteger($_GET['id'], null);
$anzahl_1 = validateUnsignedInteger($_GET['anzahl_1'], null);
$anzahl_2 = validateUnsignedInteger($_GET['anzahl_2'], null);
$anzahl_3 = validateUnsignedInteger($_GET['anzahl_3'], null);
$anzahl_4 = validateUnsignedInteger($_GET['anzahl_4'], null);
$anzahl_5 = validateUnsignedInteger($_GET['anzahl_5'], null);
$anzahl_6 = validateUnsignedInteger($_GET['anzahl_6'], null);
$anzahl_7 = validateUnsignedInteger($_GET['anzahl_7'], null);
$anzahl_8 = validateUnsignedInteger($_GET['anzahl_8'], null);
$anzahl_9 = validateUnsignedInteger($_GET['anzahl_9'], null);
$anzahl_10 = validateUnsignedInteger($_GET['anzahl_10'], null);
$item_1 = validateUnsignedInteger($_GET['item_1'], null);
$item_2 = validateUnsignedInteger($_GET['item_2'], null);
$item_3 = validateUnsignedInteger($_GET['item_3'], null);
$item_4 = validateUnsignedInteger($_GET['item_4'], null);
$item_5 = validateUnsignedInteger($_GET['item_5'], null);
$item_6 = validateUnsignedInteger($_GET['item_6'], null);
$item_7 = validateUnsignedInteger($_GET['item_7'], null);
$item_8 = validateUnsignedInteger($_GET['item_8'], null);
$item_9 = validateUnsignedInteger($_GET['item_9'], null);
$item_10 = validateUnsignedInteger($_GET['item_10'], null);
$pagenum = validateUnsignedInteger($_GET['pagenum'], null);
$oder = validateStringCritical($_GET['oder']);
$ords = validateStringCritical($_GET['ords']);
$charm = validateUnsignedInteger($_GET['charm']);

$race_types = getRaceTypes();

$race_type_map = array( NULL => array('name' => 'ALL'));

foreach ($race_types as $race_type) {
	$race_type_map[$race_type['id']] = $race_type;
}


if( strlen($ords) > 10 ) {
	unset($ords);
}
if( strlen($oder) > 10 ) {
	unset($oder);
}

// Unkritisch
$as = $_GET['as'];

/////////////////////////Item Info
if($info) {
		$item = mysqli_fetch_array(db_query("SELECT * FROM item WHERE id='$info' and preis>0 LIMIT 1"));
		$head = "$item[name] Info";
		$text .= "St&auml;rke: $item[starke]<br>";
		$text .= "Verteidigung: $item[verteidigung]<br>";
		$text .= "Geschwindigkeit: $item[speed]<br>";
		$text .= "HP: $item[hp]<br>";
		$text .= "MP: $item[mp]<br>";
		$text .= "Type: $item[s_type]<br>";
		$text .= 'Preis: '.displayMoney($item['preis']).'<br>';
		$text .= "Anzahl?: $item[anzahl]<br>";
		$text .= "ab welchen Level?: $item[level]<br><br>";
		$text .= "Info?: $item[info]";

		eval("\$infos.=\"".gettemplate("gfx/info")."\";");
		echo $infos;
		exit;
}

/////////////////////////auf Kauf Button Klicken
if($id == 1) {

if($user_ida['cheater'] == 0) {
	if(false && !checkref()) {
		displayErrorMessage(NULL,'Nicht schummeln!', displayHistoryBackLink());
		exit;
	}
}


//FIXME move to bottom!
if($anzahl_1) { $anzahl .= ",".$anzahl_1."";  $item .= ",".$item_1.""; 	}
if($anzahl_2) { $anzahl .= ",".$anzahl_2."";  $item .= ",".$item_2.""; 	}
if($anzahl_3) { $anzahl .= ",".$anzahl_3."";  $item .= ",".$item_3.""; 	}
if($anzahl_4) { $anzahl .= ",".$anzahl_4."";  $item .= ",".$item_4.""; 	}
if($anzahl_5) { $anzahl .= ",".$anzahl_5."";  $item .= ",".$item_5.""; 	}
if($anzahl_6) { $anzahl .= ",".$anzahl_6."";  $item .= ",".$item_6.""; 	}
if($anzahl_7) { $anzahl .= ",".$anzahl_7."";  $item .= ",".$item_7.""; 	}
if($anzahl_8) { $anzahl .= ",".$anzahl_8."";  $item .= ",".$item_8.""; 	}
if($anzahl_9) { $anzahl .= ",".$anzahl_9."";  $item .= ",".$item_9.""; 	}
if($anzahl_10) { $anzahl .= ",".$anzahl_10."";  $item .= ",".$item_10.""; 	}


$w_item_anzahl = explode(",",$anzahl);
$w_item_id = explode(",",$item);
$u=1;

while($u < count($w_item_anzahl)) {

if($w_item_anzahl[$u]) {
$item = mysqli_fetch_array(db_query("SELECT preis, anzahl, name FROM item WHERE id='$w_item_id[$u]' and preis>0 LIMIT 1"));
$item_preis = $item_preis + ($item['preis'] * $w_item_anzahl[$u]);

	if($item['anzahl'] < $w_item_anzahl[$u]) {
		$fehler = "Fehler! $item[name] Ausverkauft.";
		$link = $back;
		eval("\$fehler1.=\"".gettemplate("gfx/markt_fehler")."\";");
		echo $fehler1;
	exit;
	}
	if($w_item_anzahl[$u] < 0){
		db_query("INSERT INTO cheater values('$user_ida[id]','Versuchte negative Werte in den Markt einzufuegen!',now())");
		$fehler = "NICHT CHEATEN, VERDAMMT NOCHMAL!!!!!";
		$link = $back;
		eval("\$fehler1.=\"".gettemplate("gfx/markt_fehler")."\";");
		echo $fehler1;
	exit;
	}
}

$u++;
}




$user = mysqli_fetch_array(db_query("SELECT geld, item_res FROM user WHERE id='$user_ida[id]'"));

		$item_preis = ($item_preis / 100) * (100 - $user['item_res']);

$user_geld = getRelevantMoney($user_ida['id']);

if ($user_geld < $item_preis) {

		$fehler = "Sie haben nicht genug Geld!";
		$link = $back;
		eval("\$fehler1.=\"".gettemplate("gfx/markt_fehler")."\";");
		echo $fehler1;
exit;
}

$u=1;
while($u < count($w_item_anzahl)) {

	if($w_item_anzahl[$u]) {

		$item = mysqli_fetch_array(db_query("SELECT anzahl, name, s_type FROM item WHERE id='$w_item_id[$u]' and preis>0 LIMIT 1"));
		$t=0;
		while($t < $w_item_anzahl[$u]) {
			if($item['s_type'] != "Trank") { $ru_malss = 1; }
			db_query("INSERT ware SET ru_mal='$ru_malss', item_id='$w_item_id[$u]', user='$user_ida[id]'");
			$t++;
		}

		db_query("UPDATE item SET anzahl=anzahl-'$w_item_anzahl[$u]' WHERE id='$w_item_id[$u]' LIMIT 1");
		$fehler .= "Sie haben $item[name] x $w_item_anzahl[$u] gekauft!<br>";
	}
$u++;
}
db_query("UPDATE user SET geld=geld-'$item_preis' WHERE id='$user_ida[id]' LIMIT 1");
$fehler .= "<p>f&uuml;r $item_preis &#165;";

$link = "<a href=markt.php>weiter...</a>";

		eval("\$fehler1.=\"".gettemplate("gfx/markt_fehler")."\";");
		echo $fehler1;
exit;
}

////ITEMS in der navi

	$filter = '1';
	if(isset($charm)) {
		$filter = '(rassen_type='.$charm.' OR rassen_type IS NULL)';
	}

	if($pagenum == "" or $pagenum == 1)
	{
		$pagenum = 0;
	}
	if($pagenum != "" and $pagenum != 1)
	{
		$pagenum = ($pagenum - 1) * 10;
	}

	if(!$oder) {	$oder = 'level,preis'; }
	if(!$ords) { 	$ords = "Trank"; }
	$item_name = db_query("SELECT * FROM item WHERE preis>0 and $filter AND s_type='$ords' order by $oder ASC LIMIT $pagenum, 10");
	$user = mysqli_fetch_array(db_query("SELECT id, geld, item_res FROM user WHERE id='$user_ida[id]' LIMIT 1"));
	$user_item = db_query("SELECT name, id FROM item WHERE preis>0");

	$GELD = $user['geld'];

	if ($ords == 'Trank') {
		$MAXLENGTH= 2;
		$MAXNUMBER= 98;
	} else {
		$MAXLENGTH= 1;
		$MAXNUMBER= 8;
	}

	$x=0;
	$y=1;
	while($row = mysqli_fetch_array($item_name))
	{
		$ITEM = $row['name'];
		$TYPE = $race_type_map[$row['rassen_type']]['name'];
		$ANZAHL = $row['anzahl'];
		$row['preis'] = $row['preis'] / 100;
		$row['preis'] = $row['preis'] * (100 - $user['item_res']);
		$PREIS = $row['preis'];
		$PREIS1 = 'preis_'.$y;
		$ANZAHL1 = "anzahl_".$y."";
		$ITEM_ID1 = "item_".$y."";
		$ITEM_ID = $row['id'];
		$info_id = $row['id'];

		if($x==1) {
			$FARBE = "#A5B197";
			$x=0;
		} else {
		$x=1;
		$FARBE = "";
		}
		$y++;

	eval("\$ITEMS.=\"".gettemplate("gfx/markt_inhalt")."\";");
	}


////////////STORE

	while($row2 = mysqli_fetch_array($user_item)) {

		$user_items = mysqli_num_rows(db_query("SELECT id FROM ware WHERE item_id='$row2[id]' AND user='$user_ida[id]' LIMIT 100"));
		if($user_items) {
		$STORE_ITEMS .= "".$row2['name']." x ".$user_items."<br>";
		}

	}

	$Total = mysqli_num_rows(db_query("SELECT id FROM item WHERE preis>0 and $filter AND s_type='$ords'"));
	$pages = ceil($Total/10);

	$z = 1;
	$i = 0;
	while($pages > $i)
	{
			$seiten .= "&nbsp;<a href='markt.php?charm=$charm&pagenum=$z&oder=$oder&ords=$ords'>$z</a>&nbsp;";
			$z++;
			$i++;
	}

$HEADER_DATA = '';
foreach ($race_types as $race_type) {
	if (!$race_type['gm_only'] ) {
		$HEADER_DATA .= '<a href="?as=markt&charm='.$race_type['id'].'&oder='.$oder.'&ords='.$ords.'">'.$race_type['name'].'</a> ~ ';
	}
}



eval("\$output=\"".gettemplate("gfx/markt")."\";");
echo $output;
?>
