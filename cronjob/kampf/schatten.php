<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
####################KAIOKEN SPIELER 1

///////////////Damit es fuer xxx runden bleibt!
if($old_ausweich_wert) {
  if($schatten_runde > $x) {
   	$play1_ausweich=$old_ausweich_wert;
  } else {
     $old_ausweich_wert = "";
     $schatten_runden = "";
  }
}
///////////////ENDE


if($technick1[type] == "schatten") {
$k_mp[0] = $k_mp[0] - $technick1[mp];
$k_hp[0] = $k_hp[0] - $technick1[hp];
if($k_mp[0] < 0) {
$schaden_2 = "Keinen Schaden";
$schaden_1 = "Keine Kraft";
$aktion_AV1[$x] = "nein";
$k_mp[0] = 0;
} else {
///////////////////die rechnung sozusagen.

if(!$old_ausweich_wert) {
	$old_ausweich_wert = $technick1[speed];
  $schatten_runden = $x+$technick1[starke];
}



$play1_ausweich=$old_ausweich_wert;

}}

####################KAIOKEN SPIELER 2

///////////////Damit es fuer xxx runden bleibt!
if($old_ausweich_wert2) {
  if($schatten_runde2 > $x) {
   	$play2_ausweich=$old_ausweich_wert2;
  } else {
     $old_ausweich_wert2 = "";
     $schatten_runden2 = "";
  }
}
///////////////ENDE

if($technick2[type] == "schatten") {
$k_mp[1] = $k_mp[1] - $technick2[mp];
$k_hp[1] = $k_hp[1] - $technick2[hp];
if($k_mp[1] < 0) {
$schaden_1 = "Keinen Schaden";
$schaden_2 = "Keine Kraft";
$aktion_AV2[$x] = "nein";
$k_mp[1] = 0;
} else {


if(!$old_ausweich_wert2) {
	$old_ausweich_wert2 = $technick2[speed];
  $schatten_runden2 = $x+$technick2[starke];
}


$play2_ausweich=$old_ausweich_wert2;

}}
