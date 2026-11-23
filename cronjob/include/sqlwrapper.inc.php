<?php
/*
 *
 * @copyright (c) 2011 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 * @author hecht
 */

include_once(ROOT_PATH . '/include/config/server.inc.php');


function send_report($report) {
	$backtrace = debug_backtrace();
	@mail($GLOBALS['email_webmaster'],'Bugreport', 'Reporter:'.$_COOKIE['name']."\nFehler:\n".$report."\nBacktrace:\n".print_r($backtrace, true)."\n\n", 'From: Admin'."\n".'Reply-To: '.$GLOBALS['email_webmaster']."\n".'X-Mailer: PHP/'.phpversion());
}

function silent_query($sql) {
	return mysqli_query($GLOBALS['main_db'], $sql);
}

function db_query($sql) {
	$qry = mysqli_query($GLOBALS['main_db'], $sql);
	if(!$qry) {
		send_report('Query failed: '
                                .'<li>errorno='.mysqli_errno($GLOBALS['main_db'])
                                .'<li>error='.mysqli_error($GLOBALS['main_db'])
                                .'<li>query='.$sql);
		die('Ein Fehler ist aufgetreten. Dieser wurde zum Administrator geschickt. Vielen Dank!');
	}
	return $qry;
}

function db_affected_rows() {
	return mysqli_affected_rows($GLOBALS['main_db']);
}

