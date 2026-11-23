<?php
/*
 * Created on 01.09.2016
 *
 * @copyright (c) 2016 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

function getItemTopicsMap() {
	$qry = db_query('SELECT * from sp_set_topics ORDER BY id');
	$topics_map = array();

        while( $row = mysqli_fetch_assoc($qry) ) {
                $topics_map[$row['id']] = $row;
        }

	return $topics_map;
}

function getItemsMap() {
	$items_map = array();
        $qry = db_query('SELECT * from sp_item WHERE type != \'Teufels Frucht\'');
        while( $row = mysqli_fetch_assoc($qry) ) {
                $row['table_name'] = 'sp_item';
                $items_map['sp_item'][$row['id']] = $row;
        }

        $qry = db_query('SELECT * from item');
        while( $row = mysqli_fetch_assoc($qry)) {
                $row['table_name'] = 'item';
                $items_map['item'][$row['id']] = $row;
        }

        $qry = db_query('SELECT * from wochen_markt');
        while( $row = mysqli_fetch_assoc($qry) ) {
                $row['name'] = $row['item'];
                $row['table_name'] = 'wochen_markt';
                $items_map['wochen_markt'][$row['id']] = $row;
        }
	return $items_map;
}

function item2Name($item) {
	return $item['name'].'('.$item['table_name'].')';
}

function item2Value($item) {
        return $item['id'].'_'.$item['table_name'];
}

function getSetDefinitions() {
        $items_map = getItemsMap(); 
	$topics_map = getItemTopicsMap();

        $qry = db_query('SELECT * from sp_sets order by set_topic_id');
        $sets = array();
        while( $row = mysqli_fetch_assoc($qry) ) {
                $set = array();
                $set['item'] = $items_map[$row['table_name']][$row['item_id']];
                $dep_items = array();
                $qry2 = db_query('SELECT * from sp_set_ingredient WHERE set_id = '.$row['set_id']);
                while( $row2 = mysqli_fetch_assoc($qry2) ) {
                        $dep_items[] = $items_map[$row2['table_name']][$row2['item_id']];
                }
                $set['deps'] = $dep_items;
                $set['id'] = $row['set_id'];
		$set['topic_id'] = $row['set_topic_id'];
		if ( $set['topic_id'] !== NULL ) {
			$set['topic'] = $topics_map[$set['topic_id']]['name'];
		}
                $sets[] = $set;
        }
        return $sets;
}

function get_set_definitions() {
	return getSetDefinitions();
}

function getSetDefinition($set_id) {
	$sets = get_set_definitions();
	foreach ($sets as $set) {
		if ($set['id'] == $set_id) {
			return $set;
		}
	}
	return NULL;
}

function lookupItem($items, $itemIdHash) {
	$pair = explode('_', $itemIdHash, 2);
	return $items[$pair[1]][$pair[0]];
}

function itemEquals($lhs, $rhs) {
	return $lhs['id'] == $rhs['id'] && $lhs['table_name'] == $rhs['table_name'];
}

/**
 * @return, true in case item creation was successful!
 */
function createItemForUser($item, $user_id, $amount = 1) {
	$ware_table=array('item' => 'ware', 'sp_item' => 'sp_ware', 'wochen_markt' => 'wochen_ware');
	
	$table = $ware_table[$item['table_name']];
	if ($table === NULL) {
		$table = $item['table_name'];
	}

	for ($i=0;$i<$amount;++$i) {
		if ( $table == 'ware' ) {
			$ru = $item['s_type']=='Trank'?' ':'1';
			db_query('INSERT ware(user,item_id,ru_mal) values('.$user_id.', '.$item['id'].', \''.$ru.'\')');
		} else {
			db_query('INSERT '.$table.'(user, item) values('.$user_id.', '.$item['id'].')');
		}
	}
	return db_affected_rows() > 0;
}
