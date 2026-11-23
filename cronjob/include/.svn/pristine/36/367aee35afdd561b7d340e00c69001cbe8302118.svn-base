<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

####################KAMPF MP SPIELER 1

if($technick1['type'] == 'normal' AND $technick1['mp'] > 0) {
$k_mp[0] = $k_mp[0] - $technick1['mp'];

if($k_mp[0] < 0) { 
$k_hp[0] = $k_hp[0] + ($k_mp[0] * 10); 
$k_mp[0] = 0; 
}
}
####################KAMPF MP SPIELER 2

if($technick2['type'] == 'normal' AND $technick2['mp'] > 0) {
$k_mp[1] = $k_mp[1] - $technick2['mp'];

if($k_mp[1] < 0) { 
$k_hp[1] = $k_hp[1] + ($k_mp[1] * 10); 
$k_mp[1] = 0; 
}
}
?>