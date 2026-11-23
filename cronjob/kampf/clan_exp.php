<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
if($exp_rows1[level] == 1) { $new_max_exp1 = 2500; }
if($exp_rows1[level] == 2) { $new_max_exp1 = 7500; }
if($exp_rows1[level] == 3) { $new_max_exp1 = 30000; }
if($exp_rows1[level] == 4) { $new_max_exp1 = 100000; }

if($exp_rows2[level] == 1) { $new_max_exp2 = 2500; }
if($exp_rows2[level] == 2) { $new_max_exp2 = 7500; }
if($exp_rows2[level] == 3) { $new_max_exp2 = 30000; }
if($exp_rows2[level] == 4) { $new_max_exp2 = 100000; }

if($EXP_new1+$exp_rows1[min_exp] >= $exp_rows1[max_exp] AND $exp_rows1[level] != 5) {
db_query("UPDATE clan SET level=level+1, max_exp='$new_max_exp1', member=member+2 WHERE id='$clan1[$iws]'");
}

if($EXP_new2+$exp_rows2[min_exp] >= $exp_rows2[max_exp] AND $exp_rows2[level] != 5) {
db_query("UPDATE clan SET level=level+1, max_exp='$new_max_exp2', member=member+2 WHERE id='$clan2[$iws]'");
}

