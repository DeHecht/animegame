<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH. "/include/random.inc.php");

$frucht_rate = mt_random_wrapper(1,$natur_frucht);

if($frucht_rate ==  1) {  $frucht_name = 15;  }		/////////15=ID fuer Sand Frucht
if($frucht_rate ==  2) {  $frucht_name = 8;  }		/////////8 = ID FueR Feuer Frucht
if($frucht_rate ==  3) {  $frucht_name = 23;  }		/////////23=ID FueR BLITZ FRUCHT
if($frucht_rate ==  4) {  $frucht_name = 24;  }		/////////24= ID FueR KaeLTE FRUCHT
if($frucht_rate ==  5) {  $frucht_name = 6;  }		/////////6= ID FueR SMOKE FRUCHT
if($frucht_rate ==  6) {  $frucht_name = 43;  }		/////////43= ID FueR candy frucht
if($frucht_rate ==  7) {  $frucht_name = 44;  }		/////////44= ID FueR fluessigkeits frucht

db_query("INSERT wochen_ware SET item='$frucht_name', user='$win_user[besitzer]'");
?>