<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

####################KAMPF HP SPIELER 1

if($technick1['type'] == 'normal' AND $technick1['hp'] > 0) {
$k_hp[0] = $k_hp[0] - $technick1['hp'];
}

####################KAMPF HP SPIELER 2

if($technick2['type'] == 'normal' AND $technick2['hp'] > 0) {
$k_hp[1] = $k_hp[1] - $technick2['hp'];
}
?>