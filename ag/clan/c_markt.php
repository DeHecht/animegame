<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once (ROOT_PATH . '/include/parse.inc.php');
include_once(ROOT_PATH.'/include/clan.inc.php');


// GET-Section
// Kritisch (SQL-Injections)
$item_id = validateUnsignedInteger($_GET['item_id'], null);

//Unkritisch
$charm = $_GET['charm'];

if($charm == 3)
{
	$item_info = mysqli_fetch_assoc(db_query("SELECT * FROM clan_item WHERE id='$item_id' LIMIT 1"));
	$user = getClan($user_ida['clan']);

	if($user['leader'] != $user_ida['id'] AND $user['co_leader'] != $user_ida['id']) {
		displayErrorMessage(NULL,'Nur der Leader und der Co-Leader kann Clanitems kaufen!', displayHistoryBackLink());
		exit;
	}

        if($user['level'] < $item_info['req_level']) {
                $fehler_m = 'Euer Clan hat nicht das n&ouml;tige Level';
                displayErrorMessage(NULL,$fehler_m, displayHistoryBackLink());
                exit;
        }


	if($user['geld'] < $item_info['preis'])
	{
		$fehler_m = 'Euer Clan hat nicht genug Geld';
		displayErrorMessage(NULL,$fehler_m, displayHistoryBackLink());
		exit;
	}

	$new_geld = $user['geld'] - $item_info['preis'];

	db_query("UPDATE clan_item SET anzahl=anzahl-1 WHERE id=".$item_info[id]." LIMIT 1");
	db_query("UPDATE clan SET geld='$new_geld' WHERE id=".$user[id]." LIMIT 1");
	db_query("INSERT INTO clan_ware SET item_id='".$item_info[id]."', clan='".$user[id]."'");

	$fehler_m = "$item_name[name] erfolgreich gekauft<br>&nbsp;<br>Geld: $user[geld] - $item_info[preis] = $new_geld";

	displayErrorMessage(NULL,$fehler_m, '<a href="'.$_SERVER['PHP_SELF'].'?as=clan/c_markt">weiter</a>');
	exit;
} else if($charm == 5){
	$user = mysqli_fetch_array(db_query("SELECT leader, co_leader, geld, id FROM clan WHERE id='$user_ida[clan]'"));

	if($user['leader'] != $user_ida['id'] AND $user['co_leader'] != $user_ida['id']) {
		displayErrorMessage(NULL,'Nur der Leader und der Co-Leader kann Clanitems abreissen!', displayHistoryBackLink());
		exit;
	}

	db_query('Delete from clan_ware where clan = '.$user_ida['clan'].' and item_id = '.$item_id);
	if(db_affected_rows() > 0){
		displayErrorMessage(NULL,'Abreissen erfolgreich!', '<a href="'.$_SERVER['PHP_SELF'].'?as=clan/c_markt">weiter</a>');
	} else{
		displayErrorMessage(NULL,'Abreissen ist fehlgeschlagen!', displayHistoryBackLink());

	}

}
if(!$charm)
{
	if(!$oder)
	{
		$oder = preis;
	}
	?>
	<html>

	<body>

	<div align="center">
  	<center>
      	<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%" height="25">
    	<tr>
      		<th colspan="6" align="center">Clan Markt</th>
    	</tr>
    	<tr>
	      	<th align="center">Item</th>
	      	<th align="center">Kaufpreis</th>
	      	<th align="center">Miete*</th>
	      	<th align="center">Nutzung**</th>
	      	<th align="center">TP-Verbrauch</th>
	      	<th align="center">Info</th>
	      	<th align="center">Kaufen</th>
    	</tr>
	<?php
	$item_name = db_query('SELECT * FROM clan_item order by req_level');
	$clan_items_query = db_query('SELECT * FROM clan_ware where clan = '.$user_ida['clan']);
	while($row = mysqli_fetch_assoc($clan_items_query)){
		$c_item[$row['item_id']] = 1;
	}

	$clan = getClan($user_ida['clan']);

	while($row = mysqli_fetch_array($item_name))
	{
		$info = '';
		$info .= $row['staerke']!=0?' +'.$row['staerke'].' Str':'';
		$info .= $row['verteidigung']!=0?' +'.$row['verteidigung'].' Def':'';
		$info .= $row['glueck']!=0?' +'.$row['glueck'].' Lck':'';
		$info .= $row['geschwindigkeit']!=0?' +'.$row['geschwindigkeit'].' Spd':'';
		$info .= $row['ausdauer']!=0?' +'.$row['ausdauer'].' Aus':'';
		?>
		<tr>
      		<td align="center"><?php echo $row['name']; ?></th>
      		<td align="center"><?php echo displayMoney($row['preis']); ?></th>
      		<td align="center"><?php echo displayMoney($row['miete']); ?></td>
      		<td align="center"><?php echo displayMoney($row['nutzung']); ?></td>
      		<td align="center"><?php echo displayCount($row['tp']); ?></td>
      		<td align="center"><?php echo $info; ?></td>
      		<?php
      			if($c_item[$row['id']] >= 1){
      				echo '<td align="center"><a href="javascript:if(confirm(\''.$row['name'].' Wirklich abreissen?\')==true) window.location.href=\'index.php?as=clan/c_markt&charm=5&item_id='.$row['id'].'\'"><img border=0 src="design/bilder/buttons/abreissen.gif" width="42" height="19"></a></td>';
      			} else if($clan['level'] < $row['req_level']){
				echo '<td align="center"><img border=0 src="design/bilder/buttons/lvlup.png" width="42" height="19" title="Level '.$row['req_level'].' required"></td>';
			} else {
     				echo '<td align="center"><a href="javascript:if(confirm(\''.$row['name'].' Wirklich kaufen?\')==true) window.location.href=\'index.php?as=clan/c_markt&charm=3&item_id='.$row['id'].'\'"><img border=0 src="design/bilder/buttons/kaufen.gif" width="42" height="19"></a></td>';
      			}
      		?>
    	</tr>
		<?php

	}
	?>
    	<tr>
	      	<td width="692" height="25" colspan="6">* Miete wird t&auml;glich durch den Clan bezahlt und richtet sich nach der Charanzahl im Clan bei denen aktuell Clantraining eingestellt ist! (es wird mindestens f&uuml;r 12 Chars bezahlt)
			<br>** Die Nutzungsgebühr wird durch den User pro Char bezahlt (allerdings nur wenn der Char dort trainiert und genug TP hat)</td>
    	</tr>
	</table>
	<?php
}
?>
