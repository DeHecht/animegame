<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

$GLOBALS['attack_buffered'] = array ();

function getAttack($attack_id, $buffer = TRUE) {
	if(!is_numeric($attack_id)) {
		// backwards compatibility!!
		return array('name' => 'Normaler Kick');
	}

	if(!isset($GLOBALS['attack_buffered'][$attack_id])) {
		$GLOBALS['attack_buffered'][$attack_id] = mysqli_fetch_assoc(db_query('SELECT * FROM attacken WHERE id = ' . $attack_id));
	}
	return $GLOBALS['attack_buffered'][$attack_id];
}

?>