<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH. "/include/random.inc.php");

$uzze = 0;
while ( $uzze < 10 ) {
	$aktion_AV1 [$uzze] = "";
	$aktion_AV2 [$uzze] = "";
	$uzze ++;
}
$ssj_tech_1 = "";

$ssj_tech_2="";
$play1_ausweich="";
$play2_ausweich="";
$k_exp[0] = "";
$X23 = "";
$X13 = "";
$schaden_1="";
$schaden_2="";
$runden_type1=0;
$runden_type2=0;
################## Kaempfer 1

$k_hp1 = explode(",", $chara_1[hp]);
$k_mp1 = explode(",", $chara_1[mp]);

$k_hp1[0] = $k_hp1[1];
$k_mp1[0] = $k_mp1[1];

$k_starke[0] = $chara_1[starke] + $k_rustung[ru_mal]; 
$k_speed[0] = $chara_1[speed];
$k_ver[0] = $chara_1[verteidigung] + $k_rustung[ru_mal];
$k_ausdauer[0] = $chara_1[ausdauer];
$k_glueck[0] = $chara_1[glueck];
$k_attacke1 = explode(",", $chara_1[attacken]);
$k_hp[0] = round($k_hp1[0]);
$k_mp[0] = round($k_mp1[0]);
$k_aufgabe_a_1 = $k_hp1[1] / 100;
$k_aufgabe[0] = $k_aufgabe_a_1 * $chara_1[aufgeben];
######################################Kaempfer2

$k_hp2 = explode(",", $chara_2[hp]);
$k_mp2 = explode(",", $chara_2[mp]);

$k_hp2[0] = $k_hp2[1];
$k_mp2[0] = $k_mp2[1];

$k_starke[1] = $chara_2[starke] + $k_rustung[ru_mal]; 
$k_speed[1] = $chara_2[speed];
$k_ver[1] = $chara_2[verteidigung] + $k_rustung[ru_mal];
$k_ausdauer[1] = $chara_2[ausdauer];
$k_glueck[1] = $chara_2[glueck];
$k_attacke2 = explode(",", $chara_2[attacken]);
$k_hp[1] = round($k_hp2[0]);
$k_mp[1] = round($k_mp2[0]);

$k_aufgabe_a_2 = $k_hp2[1] / 100;
$k_aufgabe[1] = $k_aufgabe_a_2 * $chara_2[aufgeben];

$statChange_Char1 = array();
$statChange_Char2 = array();
$tmp_hp = array();
//globals erstellen für den generic-typ
$GLOBALS['kampf']['k_starke'] = &$k_starke;
$GLOBALS['kampf']['k_speed'] = &$k_speed;
$GLOBALS['kampf']['k_ver'] = &$k_ver;
$GLOBALS['kampf']['k_hp'] = &$k_hp;
$GLOBALS['kampf']['k_hp1'] = &$k_hp1;
$GLOBALS['kampf']['k_hp2'] = &$k_hp2;
$GLOBALS['kampf']['k_mp'] = &$k_mp;
$GLOBALS['kampf']['k_mp1'] = &$k_mp1;
$GLOBALS['kampf']['k_mp2'] = &$k_mp2;
$GLOBALS['kampf']['tmp_hp'] = &$tmp_hp;
$GLOBALS['kampf']['schaden1'] = &$schaden_1;
$GLOBALS['kampf']['schaden2'] = &$schaden_2;
$GLOBALS['kampf']['aussetzen'] = &$aussetzten_runde;

$x=0;

db_query("INSERT liga_kampf SET hp1='$k_hp[0]', hp2='$k_hp[1]', mp1='$k_mp[0]', mp2='$k_mp[1]', schaden1='$schaden_1', schaden2='$schaden_2', attacke1='$technick1[name]', attacke2='$technick2[name]', char1='$chara_1[id]', char2='$chara_2[id]', starke1='$k_starke[0]', starke2='$k_starke[1]', ver1='$k_ver[0]', ver2='$k_ver[1]', speed1='$k_speed[0]', speed2='$k_speed[1]', ausdauer1='$k_ausdauer[0]', ausdauer2='$k_ausdauer[1]', glueck1='$k_glueck[0]', glueck2='$k_glueck[1]'");

while($k_hp[0] > $k_aufgabe[0] AND $k_hp[1] > $k_aufgabe[1] AND $x < 10) {

$technick1 = mysqli_fetch_array(db_query("SELECT at_id, aktiv FROM lernen WHERE id='$k_attacke1[$x]' LIMIT 10"));
$prozent_technik1 = (20*$technick1[aktiv])-20;
$technick1 = mysqli_fetch_array(db_query("SELECT * FROM attacken WHERE id='$technick1[at_id]' LIMIT 10"));

$technick2 = mysqli_fetch_array(db_query("SELECT at_id, aktiv FROM lernen WHERE id='$k_attacke2[$x]' LIMIT 10"));
$prozent_technik2 = (20*$technick2[aktiv])-20;
$technick2 = mysqli_fetch_array(db_query("SELECT * FROM attacken WHERE id='$technick2[at_id]' LIMIT 10"));


///////////////////Hier laedt er den RELOAD der technik wenn die zusammen setzung nicht stimmen sollte
include "include/kampf/andere_technik.php";	/////////////////// Wenn man ne andere technik sich aussuchen will...


$technick1[speed]=$technick1[speed]+(($technick1[speed]/100)*$prozent_technik1);
$technick2[speed]=$technick2[speed]+(($technick2[speed]/100)*$prozent_technik2);

$technick1[verteidigung]=$technick1[verteidigung]+(($technick1[verteidigung]/100)*$prozent_technik1);
$technick2[verteidigung]=$technick2[verteidigung]+(($technick2[verteidigung]/100)*$prozent_technik2);

$technick1[starke]=$technick1[starke]+(($technick1[starke]/100)*$prozent_technik1);
$technick2[starke]=$technick2[starke]+(($technick2[starke]/100)*$prozent_technik2);



$speeds_char1 = ($k_speed[0] + $technick1[speed]);
$speeds_char2 =  ($k_speed[1] + $technick2[speed]);

$speeds_char1_4 = ($k_speed[0] + $technick1[speed]) / 1.5;
$speeds_char2_4 =  ($k_speed[1] + $technick2[speed]) / 1.5;

if($speeds_char1_4 < 1) { $speeds_char1_4 = 1; } 
if($speeds_char2_4 < 1) { $speeds_char2_4 = 1; } 

$speed_char1 = mt_random_wrapper($speeds_char1_4,$speeds_char1);
$speed_char2 = mt_random_wrapper($speeds_char2_4,$speeds_char2);


///////rechnung selber
include "kampf_include.php";

#####################################KAMPF SYSTEM
include "include/kampf/kampf_rechnung.php";



$db_query = mysqli_fetch_array(db_query("SELECT * FROM liga_kampf WHERE (char1='$chara_1[id]' OR char1='$chara_2[id]') AND dauer='0' order by id DESC LIMIT 1"));

db_query("UPDATE liga_kampf SET hp1='$db_query[hp1],$k_hp7[0]', hp2='$db_query[hp2],$k_hp7[1]', mp1='$db_query[mp1],$k_mp7[0]', mp2='$db_query[mp2],$k_mp7[1]', schaden1='$db_query[schaden1],$schaden_1', schaden2='$db_query[schaden2],$schaden_2', attacke1='$db_query[attacke1],$technick1[name]', attacke2='$db_query[attacke2],$technick2[name]', starke1='$db_query[starke1],$k_starke[0]', starke2='$db_query[starke2],$k_starke[1]', ver1='$db_query[ver1],$k_ver[0]', ver2='$db_query[ver2],$k_ver[1]', speed1='$db_query[speed1],$k_speed[0]', speed2='$db_query[speed2],$k_speed[1]', ausdauer1='$db_query[ausdauer1],$k_ausdauer[0]', ausdauer2='$db_query[ausdauer2],$k_ausdauer[1]', glueck1='$db_query[glueck1],$k_glueck[0]', glueck2='$db_query[glueck2],$k_glueck[1]' WHERE id='$db_query[id]'");


$x++;
} 
###########################EXP VERTEILUNG

if($k_hp[0] <= 0 AND $k_hp[1] <= 0) {

	if($k_hp[0] > $k_hp[1]) {
		$k_hp[0] = $k_hp1[1];
	} else {
		$k_hp[1] = $k_hp2[1];
	}
}

$s=0;
while($s < 2) {

if($s == 1) {

if($k_hp[0] > $k_hp[1]) { $X13 = HALLO; } else {  $X23 = HALLO; }
}

if($k_hp[1] < $k_aufgabe[1] OR $X13 == "HALLO") {
$k_exp[1] = 0;
$k_geld[1] = 0;
$k_exp[0] = 0;
$k_geld[0] = 0;



$sieger = $chara_1[id]; 
$verlierer = $chara_2[id]; 

} 

if($k_hp[0] < $k_aufgabe[0] OR $X23 == "HALLO") {

$sieger = $chara_2[id]; 
$verlierer = $chara_1[id]; 


$k_exp[1] = 0;
$k_geld[1] = 0;
$k_exp[0] = 0;
$k_geld[0] = 0;


}

if($k_exp[0]) { $s=2; } else { $s++; }

}
$punkt_sieger = (11-$x) + 500;
$punkt_verlierer = 0;

$dauer_Z = time();

db_query("UPDATE chars SET liga_siege=liga_siege+1, liga_punkte=liga_punkte+'$punkt_sieger' WHERE id='$sieger' LIMIT 1");
db_query("UPDATE chars SET liga_niederlagen=liga_niederlagen+1, liga_punkte=liga_punkte+'$punkt_verlierer' WHERE id='$verlierer' LIMIT 1");

db_query("UPDATE liga_kampf SET exp1='$k_exp[0]', win='$sieger', lose='$verlierer', exp2='$k_exp[1]', geld1='$k_geld[0]', geld2='$k_geld[1]', dauer='$dauer_Z' WHERE id='$db_query[id]'");


