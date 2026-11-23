<?php

include_once("path.inc.php");
include_once(ROOT_PATH. "/include/defines.inc.php");
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
////// MUSS SO BLEIBEN WICHTIG
$aussetzten_runde = "";
//Diese Variablen speichern den Wert um wieviel HP geheilt wird damit wir falls nötig diesen durch Healkonter
//in Schaden umwandeln können
$tmp_hp[0] = 0;
$tmp_hp[1] = 0;
//Diese Werte sind wichtig, damit wir wissen ob eine Heal-Attacke eingesetzt wurde und wir die HP falls sie das
//Maximum überschreiten zurücksetzen müssen
$tmp_hp[2] = false;
$tmp_hp[3] = false;
##################################MP, SSJ Verwandlungen, HP, Koerper Tausch Technicken
include ('include/kampf/majin.php');

if (!$aussetzten_runde) { /////DAS FUER MAJIN ATTACKEN
	include ('include/kampf/atk_wert.php');
	include ('include/kampf/tausch2.php'); //////////////////Muss ganz oben sein wegen technik Tauschen
	include ('include/kampf/gift.php');
	include ('include/kampf/frucht.php');
	include ('include/kampf/runde.php');
	include ('include/kampf/mp.php');
	include ('include/kampf/hp.php');
	include ('include/kampf/hp2.php');
	include ('include/kampf/SSJ.php');
	include ('include/kampf/kaioken.php');
	include ('include/kampf/kaioken2.php');
	include ('include/kampf/copy.php');
	include ('include/kampf/lose.php');
	include ('include/kampf/lose2.php');
	include ('include/kampf/ausdauer.php');
	include ('include/kampf/mpv.php');
	include ('include/kampf/hpmp.php');
	include ('include/kampf/generic_technik.php');
}

include ('include/kampf/generic_rechnung.php');
include ('include/kampf/atk_wert.php');

if (!$aussetzten_runde) { /////DAS FUER MAJIN ATTACKEN
	//Als letztes um alle HP-Attacken zu kontern (in $tmp_hp gespeichert)
	include ('include/kampf/konter_heal.php');
	//Nach Konterheal, damit überstehende HP bleibt
	include ('include/kampf/tausch.php');
	include ('include/kampf/reborn.php');
}
include ('include/kampf/heilung.php');

