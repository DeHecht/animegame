<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

// Die schlimmste Datei, die es derzeit im Quellcode gibt (Kommentar Hecht)


//////////////Kampf Techniken Erlaubt im kampf
$kampf_ssj = 1;			/////// SSJ ist eine Art technik die werte mal  setzt
$kampf_tausch = 1;		/////// Tausch ist eine Art technik die alle werte vertauscht gegner und sich selbst
$kampf_hp = 2;			/////// HP ist eine technik die es erlaubt sich zu Heilen
$kampf_hpmp = 1;		/////// HPMP ist eine art die HP+MP erhoeht
$kampf_sprit = 1;		/////// spirit ist was mit spirit stones
$kampf_spirit2 = 1;		/////// spirit2 ist was mit spirit stones
$kampf_lose = 1;		/////// Lose ist wie Kaioken nur das man werte verliert
$kampf_kaioken = 1;		/////// Kaioken ist eine technik die werte gibt

/////////ALLE NEU
$kampf_summon = 1;  /////// spirit2 ist was mit spirit stones
$kampf_gift = 1;  /////// Lose ist wie Kaioken nur das man werte verliert
$kampf_schatten = 2;  /////// Kaioken ist eine technik die werte gibt
$kampf_copy = 1;		/////// Man Trasfert werte.
$kampf_kaioken2 = 1;	//////// Man Bekommt sau viel staerke fuer 1runde
$kampf_lose2 = 1;		//////// MP Wegmachen
$kampf_majin = 1;		/////// Jemnand als majin machen.
$kampf_tausch2 = 3;	/////// Attacke Kopieren.

########Energie Anzeige
$k3 = $hp1[1] / 100;
if($hp1[0]  <= $k3 * 99.9) { $energie = 90; }
if($hp1[0]  <= $k3 * 90) { $energie = 90; }
if($hp1[0]  <= $k3 * 80) { $energie = 80; }
if($hp1[0]  <= $k3 * 70) { $energie = 70; }
if($hp1[0]  <= $k3 * 60) { $energie = 60; }
if($hp1[0]  <= $k3 * 50) { $energie = 50; }
if($hp1[0]  <= $k3 * 40) { $energie = 40; }
if($hp1[0]  <= $k3 * 30) { $energie = 30; }
if($hp1[0]  <= $k3 * 20) { $energie = 20; }
if($hp1[0]  <= $k3 * 10) { $energie = 10; }
if($hp1[0] == $hp1[1]) { $energie = 100; }




?>
