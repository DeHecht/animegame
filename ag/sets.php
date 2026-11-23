<?php
/*
 *
 * @copyright (c) 2016 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/erstellfunctions.inc.php');
include_once(ROOT_PATH.'/include/semaphore.inc.php');
include_once(ROOT_PATH.'/include/random.inc.php');
include_once(ROOT_PATH.'/include/items.inc.php');

$charm = $_GET['charm'];
$set = $_GET['set'];

?>
<SCRIPT language="JavaScript">
<!--

function create_set(id, name){
	if (confirm('Do you really want to create the item ' + name + "?" )) {
		window.open('?as=sets&charm=1&set=' + id, '_self');
	}
}


-->
</SCRIPT>
<?php

function get_user_items($user_ida) {
	$items_map = array();
	$qry = db_query('SELECT * from sp_ware where user = '.$user_ida['id']);
        while( $row = mysqli_fetch_assoc($qry) ) {
                $items_map['sp_item'][$row['item']] = $row;
        }

        $qry = db_query('SELECT * from ware where user = '.$user_ida['id']);
        while( $row = mysqli_fetch_assoc($qry)) {
                $items_map['item'][$row['item_id']] = $row;
        }

        $qry = db_query('SELECT * from wochen_ware where user = '.$user_ida['id']);
        while( $row = mysqli_fetch_assoc($qry) ) {
                $row['name'] = $row['item'];
                $items_map['wochen_markt'][$row['item']] = $row;
        }
	return $items_map;
}


function displaySets($user_ida) {
?>
	<table>
		<tr><th colspan="3">Rezepte f&uuml;rs Crafting</th></tr>
<?php
	$sets = get_set_definitions();
	$prev_topic = '#INVALID_TOPIC#';

	$user_items = get_user_items($user_ida);
	foreach( $sets as $set ) {
		$deps = array();
		$enable = true;
		foreach( $set['deps'] as $dep_item )  {
			if ( $user_items[$dep_item['table_name']][$dep_item['id']] !== NULL ) {
				$deps[] = $dep_item['name'];
			} else {
				$deps[] = '<span style="color:red">'.$dep_item['name'].'</span>';
				$enable = false;
			}
		}
		if ($set['topic'] != $prev_topic) {
			$prev_topic = $set['topic'];
			$print_topic = ($set['topic'] === NULL) ? '- Kein Topic -' : $set['topic'];
?>
	<tr>
		<th colspan="3"><hr></th>
	</tr>
	<tr>
		<th colspan="3"><?php echo $print_topic; ?></th>
	</tr>
	<tr>
		<th>Item</th><th width="50%">Zutaten</th><th>Aktion</th>
	</tr>
<?php
		}
?>
		<tr>
			<td><?php echo $set['item']['name']; ?></td>
			<td><?php echo join(',', $deps); ?></td>
			<td><button class="input" type="button" onclick="create_set(<?php echo $set['id']; ?>, '<?php echo $set['item']['name']; ?>');" <?php echo ($enable?'':'disabled="disabled"') ;?>>Erzeugen</button></td>
		</tr>
<?php
	}
?>
	</table>
<?php
}

function worker($charm, $set_id, $user_ida) {
        $sets = get_set_definitions();
        $user_items = get_user_items($user_ida);
	$ware_table=array('item' => 'ware', 'sp_item' => 'sp_ware', 'wochen_markt' => 'wochen_ware');
	foreach ( $sets as $set ) {
		if ($set['id'] == $set_id ) {
			$enable = true;
			foreach( $set['deps'] as $dep_item )  {
				if ( $user_items[$dep_item['table_name']][$dep_item['id']] === NULL ) {
					displayErrorMessage("Item konnte nicht erzeugt werden", "Nicht alle Zutaten vorhanden!", displayHistoryBackLink());
					return;
				}
			}
			foreach( $set['deps'] as $dep_item )  {
				$rowname = 'item';
				if ( $ware_table[$dep_item['table_name']] == 'ware' ) { $rowname = 'item_id'; };
				db_query('DELETE FROM '.$ware_table[$dep_item['table_name']].' WHERE user = '.$user_ida['id'].' and '.$rowname.' = '.$dep_item['id'].' LIMIT 1');
				if (db_affected_rows() == 0) {
					// it failed!! (BUG, currently no roll back for the already deleted items)
                                        displayErrorMessage("Item konnte nicht erzeugt werden", "Nicht alle Zutaten vorhanden! (Error-Code:2)", displayHistoryBackLink());
					return;
				}
				
			}
			if ( $ware_table[$set['item']['table_name']] == 'ware' ) {
				$ru = $set['item']['s_type']=='Trank'?' ':'1';
				db_query('INSERT ware(user,item_id,ru_mal) values('.$user_ida['id'].', '.$set['item']['id'].', \''.$ru.'\')');
			} else {
				db_query('INSERT '.$ware_table[$set['item']['table_name']].'(user, item) values('.$user_ida['id'].', '.$set['item']['id'].')');
			}
			if ( db_affected_rows() > 0 ) {
				echo $set['item']['name'].' wurde erfolgreich erzeugt!';	
			} else {
				echo $set['item']['name'].' konnte nicht erzeugt werden!';	
			}
			displaySets($user_ida);
		}
	}

}


if($charm !== NULL && $set !== NULL){
        worker($charm, $set,$user_ida);
} else{
        displaySets($user_ida);
}

