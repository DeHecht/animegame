<?php
/*
 * Created on 12.07.2008
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once(ROOT_PATH.'/include/random.inc.php');

function createAbholcode($charid){
 	$code = mt_random_wrapper(0, 44*$charid);
 	$code = md5($code.time());
 	db_query('DELETE from char_abholcodes where charid = '.$charid);
 	db_query('INSERT INTO char_abholcodes(charid, code) values('.$charid.', \''.$code.'\')');
}
 
function checkAbholcode($charid, $code){
 	$code_row = mysqli_fetch_assoc(db_query('Select * from char_abholcodes where charid = '.$charid));
 	if($code_row){
 		return $code == $code_row['code'];
 	}
 	return true;
}
?>
