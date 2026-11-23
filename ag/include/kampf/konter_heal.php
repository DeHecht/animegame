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
if ($technick1['type'] == "konterhp") {
	$k_mp[0] = $k_mp[0] - $technick1['mp'];
	if ($k_mp[0] < 0) {
		$k_mp[0] = 0;
		$schaden_1 = "Keine MP";
		$aktion_AV1[$x] = "nein";
	} else {
		$k_hp[1] -= $tmp_hp[1];
		$schaden_1 = "$tmp_hp[1]";
		$aktion_AV1[$x] = "nein";
		$tmp_hp[2] = false;
	}
}

####################KAMPF HP SPIELER 2

if ($technick2['type'] == "konterhp") {
	$k_mp[1] = $k_mp[1] - $technick2['mp'];
	if ($k_mp[1] < 0) {
		$k_mp[1] = 0;
		$schaden_2 = "Keine MP";
		$aktion_AV2[$x] = "nein";
	} else {
		$k_hp[0] -= $tmp_hp[0];
		$schaden_2 = "$tmp_hp[0]";
		$aktion_AV2[$x] = "nein";
		$tmp_hp[3] = false;
	}
}
?>