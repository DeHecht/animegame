<?php
/*
 * Created on 24.07.2008
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

function getSpendebarometer($databasename){
	$kosten = 59+3; // Kosten der Server pro Monat (muss manuell bei Aenderung angepasst werden!)
	// <= 2 Monate = Blau
	// 3 Monate = Rot
	// 4-6 Montae = Gelb
	// 7-12 Monate = Gruen
	// ++ = Violett
	
	$qry = db_query('select sum(betrag)+sum(malus) as saldo from '.$databasename.'.umsatz');
	$row = mysqli_fetch_assoc($qry);
	$monate = $row['saldo'] / $kosten;

	$color = 'violet';
	if($monate <= 1.0){
		$color = 'blue';
	} else if($monate <= 3.0){
		$color = 'red';
	} else if($monate <= 6.0){
		$color = 'yellow';
	} else if($monate <= 12.0){
		$color = 'lime';
	}
	
	$monate = min(12, max($monate, 0));
	
	$width = round($monate*100.0/12.0);
	$other = 100-$width;
	if($other > 100){
		$other = 100;
		$width = 0;
	}
	return '<table width="80%" cellspacing="0" cellpadding="0"><tr height="20px"><td width="'.$width.'%" style="background-color:'.$color.';text-align:left;">&nbsp;'.$row['saldo'].' €</td><td style="background-color:black;text-align:right;" width="'.$other.'%">'.($kosten*12).' €&nbsp;</td></tr></table>';
}

function getSpenden($databasename){
	$spendeline = '<table width="100%">';
	$qry = db_query('Select * from '.$databasename.'.umsatz where kommentar like \'%Spende%\' order by datum desc');
	while($row = mysqli_fetch_assoc($qry)){
		$spendeline .= '<tr><th class="content">'. $row['instanz'] . '</th><td class="content" width="50%">'.$row['kommentar'].'</td><td class="content">'.$row['betrag'].' '.$row['waehrung'].'</td><td class="content">'.$row['datum'].'</td></tr>';
	}
	$spendeline .= '</table>';
	return $spendeline;
}

?>
