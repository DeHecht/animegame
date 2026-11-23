<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
####################SUMMON SPIELER 1
if($runden_summon1) {
	if($runden_summon1 == $x) {

		$user_summon_id = mysqli_fetch_array(db_query("SELECT f.starke, f.speed, f.verteidigung, f.ausdauer, f.hp, f.mp, f.level, m.name FROM user u LEFT JOIN quest_fang f ON(f.id=u.summon) LEFT JOIN quest_monster m ON(m.id=f.monster) WHERE u.id='$chara_1[besitzer]' LIMIT 1"));

		$k_starke[0] = $k_starke[0] - $user_summon_id[starke];
		$k_speed[0] = $k_speed[0] - $user_summon_id[speed];
		$k_ver[0] = $k_ver[0] - $user_summon_id[verteidigung];
		$k_ausdauer[0] = $k_ausdauer[0] - $user_summon_id[ausdauer];
		$k_hp[0] = $k_hp[0] - $user_summon_id[hp];
		$k_mp[0] = $k_mp[0] - $user_summon_id[mp];
     $runden_summon1 = 0;
	}
}


if($technick1[type] == "summon") {

	$k_mp[0] = $k_mp[0] - $technick1[mp];
	$k_hp[0] = $k_hp[0] - $technick1[hp];

  if($k_mp[0] < 0) {
		$schaden_1 = "Summon fehlgeschlagen";
		$aktion_AV1[$x] = "nein";
		$k_mp[0] = 0;
	} else {
		if(!$chara_1[besitzer]) { $chara_1[besitzer] = $charakter[besitzer]; }

		$user_summon_id = mysqli_fetch_array(db_query("SELECT f.starke, f.speed, f.verteidigung, f.ausdauer, f.hp, f.mp, m.level, m.name FROM user u LEFT JOIN quest_fang f ON(f.id=u.summon) LEFT JOIN quest_monster m ON(m.id=f.monster) WHERE u.id='$chara_1[besitzer]' LIMIT 1"));

		if($user_summon_id[level]>$technick1[maxlvl]) {

			$schaden_1 = "$user_summon_id[name] ist zu Stark";
	  		$aktion_AV1[$x] = "nein";
		} else {
			$schaden_1 = "$user_summon_id[name]";
			$runden_summon1 = $x+$technick1[starke];

			$k_starke[0] = $k_starke[0] + $user_summon_id[starke];
			$k_speed[0] = $k_speed[0] + $user_summon_id[speed];
			$k_ver[0] = $k_ver[0] + $user_summon_id[verteidigung];
			$k_ausdauer[0] = $k_ausdauer[0] + $user_summon_id[ausdauer];
			$k_hp[0] = $k_hp[0] + $user_summon_id[hp];
			$k_mp[0] = $k_mp[0] + $user_summon_id[mp];

			$aktion_AV1[$x] = "nein";
	  }
 	}
}


####################KAIOKEN SPIELER 2

if($runden_summon2) {

	if($runden_summon2 == $x) {

		$user_summon_id = mysqli_fetch_array(db_query("SELECT f.starke, f.speed, f.verteidigung, f.ausdauer, f.hp, f.mp, f.level, m.name FROM user u LEFT JOIN quest_fang f ON(f.id=u.summon) LEFT JOIN quest_monster m ON(m.id=f.monster) WHERE u.id='$chara_2[besitzer]' LIMIT 1"));

		$k_starke[1] = $k_starke[1] - $user_summon_id[starke];
		$k_speed[1] = $k_speed[1] - $user_summon_id[speed];
		$k_ver[1] = $k_ver[1] - $user_summon_id[verteidigung];
		$k_ausdauer[1] = $k_ausdauer[1] - $user_summon_id[ausdauer];
		$k_hp[1] = $k_hp[1] - $user_summon_id[hp];
		$k_mp[1] = $k_mp[1] - $user_summon_id[mp];
     $runden_summon2 = 0;
	}

}

if($technick2[type] == "summon") {

		$k_mp[1] = $k_mp[1] - $technick2[mp];
		$k_hp[1] = $k_hp[1] - $technick2[hp];

     if($k_mp[1] < 0) {
			$schaden_1 = "Keinen Schaden";
			$schaden_2 = "Summon fehlgeschlagen";
			$aktion_AV2[$x] = "nein";
			$k_mp[1] = 0;
		} else {

			$user_summon_id = mysqli_fetch_array(db_query("SELECT f.starke, f.speed, f.verteidigung, f.ausdauer, f.hp, f.mp, m.level, m.name FROM user u LEFT JOIN quest_fang f ON(f.id=u.summon) LEFT JOIN quest_monster m ON(m.id=f.monster) WHERE u.id='$chara_2[besitzer]' LIMIT 1"));

	  		if($user_summon_id[level]>$technick2[maxlvl]) {
		  		$schaden_2 = "$user_summon_id[name] ist zu Stark";
	  			$aktion_AV2[$x] = "nein";
			} else {

				$schaden_2 = "$user_summon_id[name]";

		  		$runden_summon2 = $x+$technick2[starke];


		  		$k_starke[1] = $k_starke[1] + $user_summon_id[starke];
				$k_speed[1] = $k_speed[1] + $user_summon_id[speed];
				$k_ver[1] = $k_ver[1] + $user_summon_id[verteidigung];
				$k_ausdauer[1] = $k_ausdauer[1] + $user_summon_id[ausdauer];
				$k_hp[1] = $k_hp[1] + $user_summon_id[hp];
				$k_mp[1] = $k_mp[1] + $user_summon_id[mp];

				$aktion_AV2[$x] = "nein";
 		}
	}
}



