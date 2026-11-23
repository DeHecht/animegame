<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

// Online-Berechnungen
$online_r = mysqli_fetch_assoc(db_query('Select count(*) as anzahl from user WHERE online_zeit between TIMESTAMPADD(Minute, -15, now()) and now()'));
$online_r = $online_r['anzahl'];

$online_rekord_1_0 = mysqli_fetch_assoc(db_query('SELECT * FROM online WHERE id=1'));

?>
