<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once (ROOT_PATH . '/include/char.inc.php');
include_once (ROOT_PATH . '/include/arena.inc.php');
include_once (ROOT_PATH . '/include/defines.inc.php');

defineIfNotDefined('CHAR_LEVEL_EXP_FACTOR', 1);
defineIfNotDefined('CHAR_MAX_LEVEL', 110);

function beautifyNumericValue($value, $step){
//	RUNDEN(POTENZ(10;ABRUNDEN(MAX(LOG(I3;10);2)))/4;0)
	$tmp = round(pow(10,floor(max(log($value,10),2)))/$step);
	return round($value / $tmp) * $tmp;

}

function calculateRequiredExpChars($level){
	// We have a polynom with grade 10
	$g01 = $level * 1000;
	$g02 = pow($level,2) * 17;
	$g03 = pow($level,3) * 0.01;
	$g04 = pow($level,4) * 0.04;
	// No need for 5
	$g06 = pow($level*$level,6) * pow(10,-18);
	$g07 = pow($level*$level,7) * pow(10,-22);
	$g08 = pow($level*$level,8) * pow(10,-26);
	$g09 = pow($level*$level,9) * pow(10,-30);
	$g10 = pow($level*$level,10) * pow(10,-33);

	$result = $g01 + $g02 + $g03 + $g04 + $g06 + $g07 + $g08 + $g09 + $g10;
	$result = round($result * CHAR_LEVEL_EXP_FACTOR,0);
	return beautifyNumericValue($result, 8);
//	return $g01 + $g02 + $g03 + $g04 + $g06 + $g07 + $g08 + $g09 + $g10;
}

function calculateTrainingPoints($level){
	return round(400/(1+ exp($level *-1/15+3))/2) ;
}

function calculateRequiredExpArena($level){
	$g00 = 250;
	$g01 = 2500 * $level;
	$g02 = 250 * pow($level, 2);
	$g03 = 250 * pow($level, 3);
	$g04 = 750 * pow($level, 4);
	return beautifyNumericValue($g00 + $g01 + $g02 + $g03 + $g04, 8);
}
/**
 *
 * @param $attacken array of 10 arrays with entry (anzahl and a_name)
 * @return unknown_type
 */
function addAttackUsageToChar($char_id, $attacken){
	$char = getChar($char_id, false); // We really want the actual char!!

	// Erstmal die Attacken vermerken!
	for($i=0;$i<count($attacken);$i++){
		// So jetzt nur noch die reqs einpflegen
		if($$attacken[$i]['id'] === NULL) {
			$sql = 'Update lernen set benutzt = benutzt + '.$attacken[$i]['anzahl'].' where name = \''.$attacken[$i]['a_name'].'\' and besitzer = '.$char_id;
		} else {
			$sql = 'Update lernen set benutzt = benutzt + '.$attacken[$i]['anzahl'].' where at_id = \''.$attacken[$i]['id'].'\' and besitzer = '.$char_id;
		}
//		echo $sql.'<br>';
		db_query($sql);
	}

	// Sooo und nun gehts darum ob wir neue Attacken lernen können

	// Ersma die ganzen Infos laden
	$sql = 'SELECT * FROM lernen where besitzer = '.$char_id;
	$qry = db_query($sql);
	while($row = mysqli_fetch_assoc($qry)){
		$lern[$row['at_id']] = $row; // Zum späteren Check ob die Reqs erfüllt sind
		$atkid[] = $row['at_id']; // Zum ausschliessen der Attacken
	}

	// Wir können jetzt schon die Attacken ausschließen, die wir schon können!!
	if(count($atkid) > 0){
		$zusatz = ' AND id NOT IN('.join($atkid,',').')';
	}

	// Lernbare Attacken auswählen
	if(!is_null($char['frucht'])){
		$sql = 'select * from attacken where (Frucht is NULL or Frucht = (Select id from wochen_markt where item = \''.$char['frucht'].'\')) and (rassen != \'\' and rassen != \'0\') and level <= '.$char['level'].$zusatz;
	} else {
		$sql = 'select * from attacken where (Frucht is NULL) and (rassen != \'\' and rassen != \'0\') and level <= '.$char['level'].$zusatz;
	}

//	echo $sql.'<br>';

	$qry = db_query($sql);

	// Das geht leider nicht mit dieser Datenstruktur direkt auszuwählen
	$raceid[] = getCharRaceId($char_id);
	if(!is_null(getFusionRaceId($char_id))){
		$raceid[] = getFusionRaceId($char_id);
	}

	// So und nun arbeite jede Attacke durch die nur im geringsten Lernbar sein könnte ...
	while($row = mysqli_fetch_assoc($qry)) {
		// Erstmal checken ob es eine Fruchtattacke ist (durch die SQL-Anweisung werden alle nicht zutreffenden Früchte ausgeblockt)
		$learnable = false;
		if(!is_null($row['Frucht'])){
			// Ok es is ne Frucht-attacke :) Also können wir sie lernen :)
			$learnable = true;
		} else {
			// Ok, dann müssen wir die Rasse checken :)
			$races = preg_split('#,#', $row['rassen']);
			for($i=0;$i<count($races);$i++){
				for($t=0;$t<count($raceid);$t++){ // Walk through own races (remember: when fused we have two)
					if($races[$i] == $raceid[$t]){
						// Die Rasse passt schonmal!!
						$learnable = true; // Gehen wir mal vom besten Fall aus
						// Da die Rasse gepasst hat können wir uns aus der Schleife verabschieden
						break;
					}
				}
			}
		}

		if($learnable){
			// Haben wir die benötigten Attacken auch oft genug genutzt??
			$r_atck = preg_split('#,#', $row['req_atk']);
			for($u = 0;$u < count($r_atck); $u++){
				if($r_atck[$u] != '0' && $lern[$r_atck[$u]]['benutzt'] < $row['geld']){
					// Passt nicht? dann brauchen wir garnich weiter machen
//					echo 'Der Char kann '.$row['name'].' nicht lernen, weil er die Attacke '.$lern[$r_atck[$u]]['name'].' nur '.$lern[$r_atck[$u]]['benutzt'].' mal eingesetzt hat und nicht '.$row['geld'].'<br>';
					$learnable = false;
					break;
				}
			}
		}

		if($learnable){
			db_query('INSERT lernen SET name=\''.$row['name'].'\', at_id=\''.$row['id'].'\', besitzer=\''.$char_id.'\', aktiv=\'1\'');
			echo '<script language="JavaScript">alert(\''.$char['name'].' hat die Technik '.$row['name'].' gelernt!\');</script>';
		}
	}
}

function addExpToChar($char_id, $n_exp){
	$char = getChar($char_id, false); // We really want the actual char!!
	$char_exp = explode(',', $char['exp']);
	$char_exp[0] += $n_exp;
	$lvls = 0;
	$tp = 0;
	$lp = 0;
	while($char['level'] < CHAR_MAX_LEVEL && $char_exp[0] >= $char_exp[1]){
		// Level UP!!
		$lvls++;
		$char['level']++;

		$char_exp[0] -= $char_exp[1];
		$char_exp[1] = calculateRequiredExpChars($char['level']);
		$tp += calculateTrainingPoints($char['level']);
		$lp += 10;
	}

	if ($char['level'] == CHAR_MAX_LEVEL) {
		$char_exp[0] = 0;
	}

	if($lvls > 0){ // Ist Char um mindestens ein Level gestiegen?
		echo '<script language="JavaScript">alert(\''.$char['name'].' ist um '.$lvls.' Level gestiegen!\');</script>';
		$sql = 'Update chars set exp = \''.$char_exp[0].','.$char_exp[1].'\', lernpunkte = lernpunkte +'.$lp.', training_points = training_points + '.$tp.', level = level + '.$lvls.' where id = '.$char_id;
	} else {
		$sql = 'Update chars set exp = \''.$char_exp[0].','.$char_exp[1].'\' where id = '.$char_id;
	}
//	echo $sql.'<br>';
	db_query($sql);

	$char = getChar($char_id, false); // We really want the actual char!!
}

function addExpToArena($user_id, $n_exp){
	$arena = getArena($user_id);
	$arena_exp = explode(',', $arena['exp']);
	$arena_exp[0] += $n_exp;
	$lvls = 0;
	while($arena_exp[0] >= $arena_exp[1]){
		// Level UP!!
		$lvls++;
		$arena['level']++;

		$arena_exp[0] -= $arena_exp[1];
		$arena_exp[1] = calculateRequiredExpArena($arena['level']);
	}
	if($lvls > 0){
		echo '<script language="JavaScript">alert(\'Arena ist um '.$lvls.' Level gestiegen!\');</script>';
		db_query('Update arena set exp=\''.$arena_exp[0].','.$arena_exp[1].'\', level='.$arena['level'].', skill=skill+'.$lvls.' WHERE besitzer='.$user_id);
	} else{
		db_query('Update arena set exp=\''.$arena_exp[0].','.$arena_exp[1].'\' WHERE besitzer='.$user_id);
	}
}

?>
