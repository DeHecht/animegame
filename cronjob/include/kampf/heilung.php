<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
?>
<?php
####################KAMPF HP SPIELER 1
if($tmp_hp[2] == true) {
	$k_hp[1] = $k_hp[1] + $tmp_hp[1];
	if ($k_hp2[1] < $k_hp[1]) {
		$k_hp[1] = $k_hp2[1];
	}
}


####################KAMPF HP SPIELER 2
if($tmp_hp[3] == true) {
	$k_hp[0] = $k_hp[0] + $tmp_hp[0];
	if ($k_hp1[1] < $k_hp[0]) {
		$k_hp[0] = $k_hp1[1];
	}
}

?>