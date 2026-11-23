<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

####################KAMPF MPV SPIELER 1

if($technick1['type'] == 'mpv') {
$k_mp[1] = $k_mp[1] - $technick1['mp'];
if($k_mp[1] < 0) { 
$k_mp[1] = 0; 
}
$aktion_AV1[$x] = 'nein';
}
if($technick2['type'] == 'mpv') {
$k_mp[0] = $k_mp[0] - $technick2['mp'];
if($k_mp[0] < 0) { 
$k_mp[0] = 0; 
}
$aktion_AV2[$x] = 'nein';
}
?>