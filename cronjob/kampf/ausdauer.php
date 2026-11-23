<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

if($x > 0) {
################### PLAYER 1


$k_starke[0] = round($k_starke[0] + (($k_ausdauer[0] / 20))-($k_starke[0]/50)); 
$k_speed[0] = round($k_speed[0] + ($k_ausdauer[0] / 10));
$k_ver[0] = round($k_ver[0] + ($k_ausdauer[0] / 10));
$k_ausdauer[0] = round($k_ausdauer[0] - ($k_ausdauer[0] /20));

################### PLAYER 2


$k_starke[1] = round($k_starke[1] + (($k_ausdauer[1] / 20))-($k_starke[1]/50)); 
$k_speed[1] = round($k_speed[1] + ($k_ausdauer[1] / 10));
$k_ver[1] = round($k_ver[1] + ($k_ausdauer[1] / 10));
$k_ausdauer[1] = round($k_ausdauer[1] -  ($k_ausdauer[1] /20));

}

