<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH. "/include/random.inc.php");

#####################################KAMPF SYSTEM

// $zufall_pl1 = Attacke p1
// $zufall_pl2 = Attacke p2

// $play1_ausweich = Parade p1
// $play2_ausweich = Parade p2

// A1 (Attacke Spieler1) = 0.7 * S1 / SA + 0.3 * L1 / LA
// P2 (Parade Spieler2) = 0.6 * S2 / SA + 0.4 * L2 / LA

$lucka1 = mt_random_wrapper(0,$k_glueck[0]);
$luckp1 = mt_random_wrapper(0,$k_glueck[0]);

$lucka2 = mt_random_wrapper(0,$k_glueck[1]);
$luckp2 = mt_random_wrapper(0,$k_glueck[1]);

$speedsum = $speed_char1 + $speed_char2;
$lucksum1 = $lucka1 + $luckp2;
$lucksum2 = $lucka2 + $luckp1;

if($lucksum1 == 0){ $lucksum1 = 2; $lucka1 = 1; $luckp2 = 1; }
if($lucksum2 == 0){ $lucksum2 = 2; $lucka2 = 1; $luckp1 = 1; }

$zufall_pl1 = 0.7 * $speed_char1 / $speedsum + 0.3 * $lucka1 / $lucksum1;
$zufall_pl2 = 0.7 * $speed_char2 / $speedsum + 0.3 * $lucka2 / $lucksum2;

$play1_ausweich = 0.6 * $speed_char1 / $speedsum + 0.4 * $luckp1 / $lucksum2;
$play2_ausweich = 0.6 * $speed_char2 / $speedsum + 0.4 * $luckp2 / $lucksum1;

//echo '<br>DEBUG (NICHT ALS BUG MELDEN!!): Speed Char1 '.$speed_char1.', Glueck Char1 = '.$k_glueck[0].', Att-Luck Char1 '.$lucka1.', Par-Luck Char1 '.$luckp1.'<br>';
//echo '<br>DEBUG (NICHT ALS BUG MELDEN!!): ATTACK Char1 '.$zufall_pl1.', PARADE Char1 '.$play1_ausweich.'<br><br>';
//echo '<br>DEBUG (NICHT ALS BUG MELDEN!!): Speed Char2 '.$speed_char2.', Glueck Char2 = '.$k_glueck[1].', Att-Luck Char2 '.$lucka2.', Par-Luck Char1 '.$luckp2.'<br>';
//echo '<br>DEBUG (NICHT ALS BUG MELDEN!!): ATTACK Char2 '.$zufall_pl2.', PARADE Char2 '.$play2_ausweich.'<br><br>';

