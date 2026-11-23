<?php
/*
 * Created on 01.09.2015
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

/**
 * As mt_random does not accept $max < $min this wrapper ensures that this is still possible
 * for the legacy code
 */
function mt_random_wrapper($min, $max) {
	if ($max < $min) {
		return mt_rand( $max, $min );
	}
	return mt_rand( $min, $max );
}
