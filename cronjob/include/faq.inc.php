<?php
/*
 * Created on 03.10.2007
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
/******************************************************************************
* Dieser Skript enthält Funktionen für das Darstellen und Verwalten der FAQ.
*******************************************************************************
* Autor: Pascal Proksch
* Erstellungsdatum: 03.10.2007
* Zuletzt verändert: 03.10.2007
*******************************************************************************/

include_once(ROOT_PATH.'/include/config/db.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');

function insertFaqEntry($parent, $header, $body){
	db_query('INSERT INTO `faq_entries` ( `id` , `parent` , `header` , `body` ) VALUES (NULL , \''.$parent.'\', \''.encodeNoHTMLWithBB($header).'\', \''.encodeNoHTMLWithBB($body).'\')');
}

function getAllFaqEntries(){
	$sql = 'Select * from faq_entries order by parent';
	$qry = db_query($sql);
	$returnArray = array();
	$tempArray[0] = & $returnArray;
	while($result = mysqli_fetch_assoc($qry)){
		$tempArray[$result['parent']][$result['id']]['node'] = $result;
		$tempArray[$result['id']] = & $tempArray[$result['parent']][$result['id']];
	}
	return $returnArray;
}

function getTableOfContentsRec($number,$values,$pre,$app){
	$string ='';
	if($values['node']['header'] != null){
		if($values['node']['parent'] == 0){
			$string = preg_replace(array('/###ID###/','/###ROW###/'),array($values['node']['id'], 'tr><td>&nbsp;</td></tr><th align="left" colspan="2"'),$pre).'Thema '.$number.' '.$values['node']['header'].$app;		
		} else{
			$string = preg_replace(array('/###ID###/','/###ROW###/'),array($values['node']['id'], 'td width="10">&nbsp</td><td'),$pre).$number.' '.$values['node']['header'].$app;					
		}
	}
	$keys = array_keys($values);
	$i = 1;
	foreach($keys as $key){
		if(is_numeric($key)){
			$string .= getTableOfContentsRec($number.($i++).'.',$values[$key],$pre,$app);
		}
	}
	return $string;
}

function getTableOfContents($pre,$app){
	$val = getAllFaqEntries();
	$ret = getTableOfContentsRec("",$val,$pre,$app);
	return $ret;
}

function getContextRec($number, $values, $pre1, $pre2, $app1, $app2){
	$string ='';
	if($values['node']['header'] != null){
		$string .= preg_replace('/###ID###/',$values['node']['id'],$pre1).$number.' '.$values['node']['header'].$app1."\n";
	}
	if($values['node']['body'] != null){
		$string .= preg_replace('/###ID###/',$values['node']['id'],$pre2).preg_replace('#\n#','<br>',$values['node']['body']).$app2."\n";
	}
	$keys = array_keys($values);
	$i = 1;
	foreach($keys as $key){
		if(is_numeric($key)){
			$string .= getContextRec($number.($i++).'.',$values[$key],$pre1, $pre2, $app1, $app2);
		}
	}
	return $string;
}

function getContext($pre1, $pre2, $app1, $app2){
	$val = getAllFaqEntries();
	$ret = getContextRec("",$val, $pre1, $pre2, $app1, $app2);
	return $ret;
}

function getEntry($faqentry){
	$sql = 'Select * from faq_entries WHERE id = '.$faqentry;
	$qry = db_query($sql);
	return mysqli_fetch_assoc($qry);
}

function updateEntry($faqentry, $parent,$header,$body){
	$parentpart = $parent!=null?'parent = '.$parent.',':'';
	$sql = 'UPDATE faq_entries SET '.$parentpart.' header = \''.encodeNoHTMLWithBB($header).'\', body = \''.encodeNoHTMLWithBB($body).'\' WHERE id = '.$faqentry;
	db_query($sql);
}

function moveUp($faqentry){
	$entry = getEntry($faqentry);
	$sql = 'Select * from faq_entries WHERE parent = '.$entry['parent'].' AND id < '.$entry['id'].' order by id desc LIMIT 1';
	$qry = db_query($sql);
	$entry2 = mysqli_fetch_assoc($qry);
	if(!$entry2){ // Er ist schon der höchste
		return;
	}
	$sql2 = 'Select * from faq_entries WHERE parent = '.$faqentry;
	$qry2 = db_query($sql2);
	while($row = mysqli_fetch_assoc($qry2)){
		$ids[] = $row['id'];
	}
	db_query('UPDATE faq_entries SET header = \''.$entry2['header'].'\', body = \''.$entry2['body'].'\' where id = '.$entry['id']);
	db_query('UPDATE faq_entries SET header = \''.$entry['header'].'\', body = \''.$entry['body'].'\' where id = '.$entry2['id']);

	db_query('Update faq_entries SET parent = '.$entry['id'].' where parent = '.$entry2['id']);
	db_query('Update faq_entries SET parent = '.$entry2['id'].' where id IN ('.join($ids, ',').')');
}

function moveDown($faqentry){
	$entry = getEntry($faqentry);
	$sql = 'Select * from faq_entries WHERE parent = '.$entry['parent'].' AND id > '.$entry['id'].' order by id asc LIMIT 1';
	$qry = db_query($sql);
	$entry2 = mysqli_fetch_assoc($qry);
	if(!$entry2){ // Er ist schon der höchste
		return;
	}
	$sql2 = 'Select * from faq_entries WHERE parent = '.$faqentry;
	$qry2 = db_query($sql2);
	while($row = mysqli_fetch_assoc($qry2)){
		$ids[] = $row['id'];
	}
	db_query('UPDATE faq_entries SET header = \''.$entry2['header'].'\', body = \''.$entry2['body'].'\' where id = '.$entry['id']);
	db_query('UPDATE faq_entries SET header = \''.$entry['header'].'\', body = \''.$entry['body'].'\' where id = '.$entry2['id']);	

	db_query('Update faq_entries SET parent = '.$entry['id'].' where parent = '.$entry2['id']);
	db_query('Update faq_entries SET parent = '.$entry2['id'].' where id IN ('.join($ids, ',').')');
}

function getChildren($faqentry){
	$sql = 'Select * from faq_entries where parent = '.$faqentry.' order by id asc';
	$qry = db_query($sql);
	while($row = mysqli_fetch_assoc($qry)){
		$ret[] = $row;
	}
	return $ret;
}

?>