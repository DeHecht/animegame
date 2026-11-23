<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH. "/include/random.inc.php");

if(!$aktion_AV1[$x]) { 
if($zufall_pl1 > $play2_ausweich) {

$alt_hp = $k_hp[1];

$charsa1_def = $k_ver[1]+$technick2[verteidigung];
$charsa1_4_def = ($k_ver[1]+$technick2[verteidigung]) / 1.1;

if($charsa1_def <= 2) { $charsa1_def = 2; }
if($charsa1_4_def < 1) { $charsa1_4_def = 1; }

$charsa1_def = mt_random_wrapper($charsa1_4_def,$charsa1_def);

$charsa1_str = $k_starke[0]*1.2+$technick1[starke];
$charsa1_4_str = ($k_starke[0]*1.2+$technick1[starke])+1;

if($charsa1_str <= 2) { $charsa1_str = 2; }

if($charsa1_4_str < 1) { $charsa1_4_str = 1; }

$charsa1_str = mt_random_wrapper($charsa1_4_str,$charsa1_str);

$k_hp[1] = round(($k_hp[1] + $charsa1_def - $charsa1_str + mt_random_wrapper(1,20)));
$schaden_1 = $alt_hp - $k_hp[1];
if($alt_hp < $k_hp[1]) { $k_hp[1] = $alt_hp; $schaden_1 = 0; } 
} else {
$schaden_1 = "Keinen Schaden";
}
}
if(!$aktion_AV2[$x]) { 
if($zufall_pl2 > $play1_ausweich) {


$alt_hp = $k_hp[0];

$charsa2_def = $k_ver[0]+$technick1[verteidigung];
$charsa2_4_def = ($k_ver[0]+$technick1[verteidigung]) / 1.1;

if($charsa2_def <= 2) { $charsa2_def = 2; }
if($charsa2_4_def < 1) { $charsa2_4_def = 1; }

$charsa2_def = mt_random_wrapper($charsa2_4_def,$charsa2_def);
$charsa2_str = $k_starke[1]*1.2+$technick2[starke];
$charsa2_4_str = ($k_starke[1] *1.2 +$technick2[starke]) +1;

if($charsa1_str <= 2) { $charsa1_str = 2; }
if($charsa2_4_str < 1) { $charsa2_4_str = 1; }

$charsa2_str = mt_random_wrapper($charsa2_4_str,$charsa2_str);

$k_hp[0] = round(($k_hp[0] + $charsa2_def - $charsa2_str + mt_random_wrapper(1,20)));
$schaden_2 = $alt_hp - $k_hp[0];
if($alt_hp < $k_hp[0]) { $k_hp[0] = $alt_hp; $schaden_2 = 0; } 
} else {
$schaden_2 = "Keinen Schaden";
}}

if(!$technick1[name]) { $technick1[name] = "Normaler Kick"; }
if(!$technick2[name]) { $technick2[name] = "Normaler Kick"; }



if($k_hp[0]<0) { $k_hp7[0] = 0; } else { $k_hp7[0] = $k_hp[0];  }
if($k_mp[0]<0) { $k_mp7[0] = 0; } else { $k_mp7[0] = $k_mp[0];  }
if($k_hp[1]<0) { $k_hp7[1] = 0; } else { $k_hp7[1] = $k_hp[1];  }
if($k_mp[1]<0) { $k_mp7[1] = 0; } else { $k_mp7[1] = $k_mp[1];  }
