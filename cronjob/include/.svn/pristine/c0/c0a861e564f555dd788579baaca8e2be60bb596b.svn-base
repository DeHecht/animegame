<?php
/*
 * Created on 07.03.2009
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

if (!function_exists('getVariableName')) {
	function getVariableName($attribute, $pos) {
		switch($attribute) {
			case 's_hp_cur':
			case 'e_hp_cur':
				return 'k_hp';
			case 's_hp_max':
				if($pos == 1) {
					return 'k_hp1';
				} else {
					return 'k_hp2';
				}
			case 'e_hp_max':
				if($pos == 1) {
					return 'k_hp2';
				} else {
					return 'k_hp1';
				}
			case 's_mp_cur':
			case 'e_mp_cur':
				return 'k_mp';
			case 's_mp_max':
				if($pos == 1) {
					return 'k_mp1';
				} else {
					return 'k_mp2';
				}
			case 'mp_max':
				if($pos == 1) {
					return 'k_mp2';
				} else {
					return 'k_mp1';
				}
			case 's_str':
			case 'e_str':
				return 'k_starke';
			case 's_def':
			case 'e_def':
				return 'k_ver';
			case 's_spd':
			case 'e_spd':
				return 'k_speed';
			case 'e_heal':
			case 's_heal':
				return 'tmp_hp';
			default:
				return '';
		}
	}
}

if (!function_exists('getCharVariable')) {
	function getCharVariable($attribute, $pos) {
		switch ($attribute) {
			case 's_hp_max':
			case 'e_hp_max':
			case 's_mp_max':
			case 'e_mp_max':
				return 1;
			default:
				if($attribute[0] == 's') {
					return $pos - 1;
				} else {
					return 2 - $pos;
				}
		}
	}
}

if (!function_exists('getCalculatedValue')) {
	function getCalculatedValue($schaden, $wert, $attWert) {
		if($schaden == '%') {
			return ($attWert * $wert) / 100;
		} else {
			return $wert;
		}
	}
}

if (!function_exists('processStatChanges')) {
	function processStatChanges(&$statChanges, $charPos) {
		foreach ($statChanges as $statKey => &$attacke) {
			//echo '<br>attackenstats: '.count($attacke).'<br>';
			$statsCounter = 0;
			foreach ($attacke as $attKey => &$stat) {
				if(is_array($stat)) {
					//echo '<br>'.print_r($stat);
					//echo '<br>dauer: '.$stat['dauer'].'/'.$stat['maxdauer'];
					//echo '<br>wert: '.$stat['dauer'];
					//echo '<br>operator: ';
					//echo '<br>wert: '.$stat['wert'];
					//echo '<br>variabel-index: '.getCharVariable($stat['attribut'], $charPos);
					//echo '<br>attribut: '.getVariableName($stat['attribut'], $charPos).' ('.$stat['attribut'].')';
					//echo '<br>value: '.$GLOBALS['kampf'][getVariableName($stat['attribut'], $charPos)][getCharVariable($stat['attribut'], $charPos)];
					//echo '<br>zAttribut: '.getVariableName($stat['zattribut'], $charPos).' ('.$stat['zattribut'].')';
					//echo '<br>value: '.$GLOBALS['kampf'][getVariableName($stat['zattribut'], $charPos)][getCharVariable($stat['zattribut'], $charPos)];
					$statsCounter++;
					$attributWert = &$GLOBALS['kampf'][getVariableName($stat['attribut'], $charPos)][getCharVariable($stat['attribut'], $charPos)];
					if($stat['attribut'] != $stat['zattribut']) {
						$zAttributWert = &$GLOBALS['kampf'][getVariableName($stat['zattribut'], $charPos)][getCharVariable($stat['zattribut'], $charPos)];
					} else {
						$zAttributWert = &$attributWert;
					}
					//echo '<br>operator: "'.$stat['operator'].'"';
					if(($stat['wirkung'] == 'always' || $stat['dauer'] == $stat['maxdauer']) && ($stat['unblockbar'] || !$GLOBALS['kampf']['aussetzen'])) {
						switch ($stat['operator']) {
							case '+':
								$stat['akk_schaden'] += getCalculatedValue($stat['formel'], $stat['wert'], $attributWert);
								$zAttributWert += getCalculatedValue($stat['formel'], $stat['wert'], $attributWert);
								break;
							case '-':
								$stat['akk_schaden'] -= getCalculatedValue($stat['formel'], $stat['wert'], $attributWert);
								$zAttributWert -= getCalculatedValue($stat['formel'], $stat['wert'], $attributWert);
								break;
							case '*':
								//vorherigen wert merken um später gegen rechnen zu können
								$tmp_wert = $attributWert;
								//echo 'tmp_wert: '.$tmp_wert.'<br>';
								$zAttributWert *= getCalculatedValue($stat['formel'], $stat['wert'], $attributWert);
								$stat['akk_schaden'] += $attributWert - $tmp_wert;
								break;
							case '/':
								//vorherigen wert merken um später gegen rechnen zu können
								$tmp_wert = $attributWert;
								//echo 'tmp_wert: '.$tmp_wert.'<br>';
								$zAttributWert /= getCalculatedValue($stat['formel'], $stat['wert'], $attributWert);
								$stat['akk_schaden'] += $attributWert - $tmp_wert;
								break;
							default:
								echo '<br>kein operator gefunden!';
						}
					}
					//echo '<br>ber. value: '.$zAttributWert;
					//echo '<br>akk_schaden: '.$stat['akk_schaden'].'<br>';
					//echo 'Varname: '.getVariableName($stat['attribut'], $charPos).'<br>';
					//echo 'Schaden: '.getCalculatedValue($stat['schaden'], $stat['wert'], $attributWert).'<br>';
					//echo '<br>zattribut: '.$stat['zattribut'];

					if(getVariableName($stat['zattribut'], $charPos) == 'tmp_hp') {
						//hier extra den weiteren parameter für healkonter setzen
						$index = 3;
						if($charPos == 1) {
							if($stat['zattribut'][0] == 's') {
								$index = 3;
							} else {
								$index = 2;
							}
						} else {
							if($stat['zattribut'][0] == 's') {
								$index = 2;
							} else {
								$index = 3;
							}
						}
						$GLOBALS['kampf'][getVariableName($stat['zattribut'], $charPos)][$index] = true;
					}


					$stat['dauer']--;
					if($stat['dauer'] < 1) {
						if($stat['wiederherstellbar'] == true) {
							//echo 'resette stat '.$zAttributWert.' um den wert '.$stat['akk_schaden'].'<br>';
							$zAttributWert -= $stat['akk_schaden'];
						}
						$statsCounter--;
						if($charPos == 1) {
							$GLOBALS['kampf']['schaden1'] = $attacke['text'];
						} else {
							$GLOBALS['kampf']['schaden2'] = $attacke['text'];
						}
						unset($attacke[$attKey]);
					}
				}
			}
			//echo 'Statscounter: '.$statsCounter.'<br>';
			//echo 'attackenstats: '.count($attacke).'<br>';
			if($statsCounter == 0) {
				unset($statChanges[$statKey]);
			}
		}
	}
}

################### Spieler 1
//char auf der linken Seite
processStatChanges($statChange_Char1, 1);

################### Spieler 2
//char auf der rechten Seite
processStatChanges($statChange_Char2, 2);
?>