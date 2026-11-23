<?php
/*
 *	Created on 12.04.2008
 *	Created by Hecht
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
/*
 *
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// Eine spezielle Semaphore, die sich von gewoehnlichen Semaphoren unterscheidet
// @Returns TRUE if ok, FALSE if not, NULL on error

$GLOBALS['semaphorecount'] = 0;

/**
 * Tries to grab the semaphore
 * @param string $resource the name of the resource
 */
function semaphoreUP($resource){
	// echo 'semaphoreUp('.$resource.')<br>' . PHP_EOL ;
 	if($GLOBALS['semaphorecount'] == 0){
 		ignore_user_abort(true); // Verarbeitung darf NICHT abgebrochen werden!!
//		echo 'DEBUG: kritischer Bereich betreten<br>';
 	}
 	// Loesche alte Ressourcen
 	db_query('DELETE FROM semaphore WHERE TIMESTAMPDIFF(MINUTE, zeit, now()) > 1 and ressource = \''.$resource.'\'');

 	$sql = 'INSERT INTO semaphore(ressource) values(\''.$resource.'\')';
 	silent_query($sql);
 	if(db_affected_rows() > 0){
//		echo 'DEBUG: Semaphore fuer '.$ressource.' belegt!('.db_affected_rows().')<br>';
	 	$GLOBALS['semaphorecount']++;
	 	return true;
 	}
//	echo 'DEBUG:Semaphore fuer '.$ressource.' NICHT belegt!<br>';
 	if($GLOBALS['semaphorecount'] == 0){
//		echo 'DEBUG: Verlassen des kritischen Bereichs<br>';
 		ignore_user_abort(false); // Verarbeitung darf wieder abgebrochen werden!!
 	}
 	return false;
}

// @Returns TRUE if ok, FALSE on Semaphore error, NULL on MYQL Error
function semaphoreDown($resource){
 	$sql = 'DELETE FROM semaphore where ressource = \''.$resource.'\'';
 	silent_query($sql);
 	if(db_affected_rows() == 0){
//		echo 'DEBUG: Semaphore '.$ressource.' war schon freigegeben<br>';
	 	return false;
 	}
//	echo 'DEBUG: Semaphore '.$ressource.' freigegeben<br>';
 	$GLOBALS['semaphorecount']--;
	if($GLOBALS['semaphorecount'] == 0){
//		echo 'DEBUG: kritischer Bereich verlassen<br>';
	 	ignore_user_abort(false); // Verarbeitung darf nun wieder abgebrochen werden!!
	}
	return true;
}

/**
 * Diese Funktion ist nur dazu da um zu pruefen ob ueberhaupt ein Lock besteht,
 * ohne die Intension die Ressource selbst zu belegen!
 * returns true, wenn Semaphore belegt ist, false wenn nicht
 */
function isSemaphoreUP($ressource){
	return mysqli_fetch_assoc(db_query('Select * from semaphore where WHERE TIMESTAMPDIFF(MINUTE, zeit, now()) > 5 and ressource = \''.$ressource.'\'')) != false;
}

function installSemaphoreDatabase(){
	$sql1 = 'DROP TABLE `semaphore`';
	$sql2 = 'CREATE TABLE `semaphore` (' .
			'`ressource` varchar(20)  NOT NULL,' .
			'`zeit` TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,' .
			'PRIMARY KEY(`ressource`),' .
			'INDEX `Zeitindex`(`zeit`)' .
			')' .
			'ENGINE = MYISAM ' .
			'COMMENT = \'Die Tabelle, die hoffentlich alle Zugriffsprobleme loesen wird\'';
	db_query($sql1);
	db_query($sql2);
}

?>
