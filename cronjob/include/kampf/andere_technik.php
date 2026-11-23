<?php
/*
 * Created on 07.03.2009
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
if($k_attacke1_2[$x]) {

	if($k_select1_1[$x] == 1) {	$wert_select1 = $k_hp[0];   $k_auswahl1 = $k_hp1[1]; }
	elseif($k_select1_1[$x] == 2) { $wert_select1 = $k_mp[0];  $k_auswahl1 = $k_mp1[1];  }
	elseif($k_select1_1[$x] == 3) { $wert_select1 = $k_starke[0]+$k_ver[0]+$k_speed[0]+$k_ausdauer[0]+$k_glueck[0];  	$k_auswahl1=$wert_select1;
	}

	if($k_select1_3[$x] == 1) {	$wert_select1_3 = $k_hp[1]; }
	elseif($k_select1_3[$x] == 2) { $wert_select1_3 = $k_mp[1]; }
	elseif($k_select1_3[$x] == 3) { $wert_select1_3 = $k_starke[1]+$k_ver[1]+$k_speed[1]+$k_ausdauer[1]+$k_glueck[1]; }
	elseif($k_select1_3[$x] == 5) { $wert_select1_3 = ($k_auswahl1/100) * 5;  }	////////5%
	elseif($k_select1_3[$x] == 10) { $wert_select1_3 = ($k_auswahl1/100) * 10;  }	////////10%
	elseif($k_select1_3[$x] == 20) { $wert_select1_3 = ($k_auswahl1/100) * 20;  }	////////20%
	elseif($k_select1_3[$x] == 50) { $wert_select1_3 = ($k_auswahl1/100) * 50;  	////////50%
	}

	if($k_select1_2[$x] == 1) {

		if($wert_select1 > $wert_select1_3) {
			$new_tech1 = 0;
		} else {
			$new_tech1 = 1;
		}
	}

	if($k_select1_2[$x] == 2) {
		if($wert_select1 < $wert_select1_3) {
			$new_tech1 = 0;
		} else {
			$new_tech1 = 1;
		}
	}

if($new_tech1 == 1) {
$technick1 = mysqli_fetch_array(db_query("SELECT at_id, aktiv FROM lernen WHERE id='$k_attacke1_2[$x]' LIMIT 10"));
$prozent_technik1 = (20*$technick1['aktiv'])-20;
$technick1 = mysqli_fetch_array(db_query("SELECT * FROM attacken WHERE id='$technick1[at_id]' LIMIT 10"));
}

}


///////////// Technik spieler 2
if($k_attacke2_2[$x]) {
	if($k_select2_1[$x] == 1) {	$wert_select2 = $k_hp[1];  $k_auswahl2 = $k_hp2[1]; }
	elseif($k_select2_1[$x] == 2) { $wert_select2 = $k_mp[1]; $k_auswahl2 = $k_mp2[1];  }
	elseif($k_select2_1[$x] == 3) { $wert_select2 = $k_starke[1]+$k_ver[1]+$k_speed[1]+$k_ausdauer[1]+$k_glueck[1];  $k_auswahl2 = $wert_select2;
	}

	if($k_select2_3[$x] == 1) {	$wert_select2_3 = $k_hp[0]; }
	elseif($k_select2_3[$x] == 2) { $wert_select2_3 = $k_mp[0]; }
	elseif($k_select2_3[$x] == 3) { $wert_select2_3 = $k_starke[0]+$k_ver[0]+$k_speed[0]+$k_ausdauer[0]+$k_glueck[0]; }
	elseif($k_select2_3[$x] == 5) { $wert_select2_3 = ($k_auswahl2/100) * 5;  }	////////5%
	elseif($k_select2_3[$x] == 10) { $wert_select2_3 = ($k_auswahl2/100) * 10;  }	////////10%
	elseif($k_select2_3[$x] == 20) { $wert_select2_3 = ($k_auswahl2/100) * 20;  }	////////20%
	elseif($k_select2_3[$x] == 50) { $wert_select2_3 = ($k_auswahl2/100) * 50;  	////////50%
	}
	if($k_select2_2[$x] == 1) {
		if($wert_select2 > $wert_select2_3) {
			$new_tech2 = 0;
		} else {
			$new_tech2 = 1;
		}
	}

	if($k_select2_2[$x] == 2) {
		if($wert_select2 < $wert_select2_3) {
			$new_tech2 = 0;
		} else {
			$new_tech2 = 1;
		}
	}

if($new_tech2 == 1) {
$technick2 = mysqli_fetch_array(db_query("SELECT at_id, aktiv FROM lernen WHERE id='$k_attacke2_2[$x]' LIMIT 10"));
$prozent_technik2 = (20*$technick2['aktiv'])-20;
$technick2 = mysqli_fetch_array(db_query("SELECT * FROM attacken WHERE id='$technick2[at_id]' LIMIT 10"));
}

}

?>