<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */


/**
 * Calculates the elo value out of the given parameters
 * @param int ownElo the own elo value
 * @param int otherElo the others elo value
 * @param int result the result (4:1 = 3, 2:3 = -1)
 * @param int maxResult the maximum value result can have
 */
function calculateElo($ownElo, $otherElo, $result, $maxResult) {
	$erw = 1 / (1 + pow(10, ($otherElo - $ownElo)/400));
	$sw = ($result + $maxResult) / ($maxResult * 2) ;
	return round($ownElo + 50 * ($sw - $erw));
}

/*
echo calculateElo(1000, 1000, 5, 5) . '<br>';
echo calculateElo(1000, 1000, -5,5) . '<br>';
echo calculateElo(900,1100,5,5) . '<br>';
echo calculateElo(900,1100,-5,5) . '<br>';
echo calculateElo(1100,900,5,5) . '<br>';
echo calculateElo(1100,900,-5,5) . '<br>';
echo calculateElo(2000,500,5,5) . '<br>';
echo calculateElo(2000,500,-5,5) . '<br>';

for($i=1000;$i<2000;$i++) {
	if($i == calculateElo($i, 1000, 5, 5)) {
		echo 'ranking that does not provide points against 1000 is ' .$i . '<br>';
		break;
	}
}
*/

?>
