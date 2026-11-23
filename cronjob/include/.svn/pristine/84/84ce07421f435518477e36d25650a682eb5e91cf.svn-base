<?php
/*
 *
 * @copyright (c) 2012 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 * @author hecht
 */

include_once(ROOT_PATH.'/include/semaphore.inc.php');
include_once(ROOT_PATH.'/include/define.inc.php');
include_once(ROOT_PATH . '/include/sqlwrapper.inc.php');

define(TASKING_NO_RESCEDULE, 0);
define(TASKING_5_MIN, 1);
define(TASKING_10_MIN, 2);
define(TASKING_15_MIN, 3);
define(TASKING_30_MIN, 4);
define(TASKING_1_HOUR, 5);
define(TASKING_2_HOUR, 6);
define(TASKING_6_HOUR, 7);
define(TASKING_12_HOUR, 8);
define(TASKING_DAYLY, 9);
define(TASKING_DAY_OF_WEEK, 10);
define(TASKING_DAY_OF_MONTH, 11);
defineIfNotDefined('TASKING_CRONJOB_PATH', '', true); // does not have to be set when having crond-support ;)


/**
 * The method that has to be called by a cronjob!
 */
function tick() {
	$resource = 'cronjob';
	if(semaphoreUP($resource)) { // do not do in parallel
		return;
	}

	$ids = getPassedTaskIds();
	foreach ($ids as $id) {
		executeScript($id);
	}

	semaphoreDown($resource);
}


/**
 * Schedule a task
 *
 * @param string $script_name the name of the script e.g. "generic_tournament.php")
 * @param array $parameters the parameters to use array("key" => "value) style
 * @param long $execution_time the posix timestamp
 * @param int $repeat_mode something like "TASKING_NO_RESCEDULE" or TASKING_5_MIN ...
 * @param bool $catchUpOn should missed tasks be catched up on later? If unsure what you are doing do not use this ;)
 * @param int $task_id the task id that should be updated (if one only wants to update an existant job)
 */
function schedule($script_name, array $parameters, $execution_time, $repeat_mode, $catchUpOn = false, $task_id = NULL) {
	// FIXME: Solve the parameter problem differently
	// as it is not yet clear how to start a multi command transaction do it the dirty way here and solve it in the right way later
	// so please do not hate me Jester and don't tell my mum ;)

	$parameters_string = '';
	foreach($parameters as $key => $value){
		$parameters_string .= $key .'='.$value.' ';
	}

	if($task_id === NULL) {
		db_query('INSERT INTO tasking(script_name, parameter, schedule_time, mode, catchup) values(\''.$script_name.'\', \''.$parameters_string.'\', \''.$execution_time.'\', \''.$repeat_mode.'\', \''.$catchUpOn.'\')');
	} else {
		db_query('UPDATE tasking SET script_name = \''.$script_name.'\' , parameter = \''.$parameters_string.'\', schedule_time = \''.$execution_time.'\', mode = \''.$repeat_mode.'\', catchup = \''.$catchUpOn.'\' WHERE task_id = ' . $task_id);
	}
}


/**
 * Get the task ids of all tasks
 */
function getAllTaskIds() {
	$qry = db_query('SELECT task_id FROM tasking');
	$result = array();
	while($row = mysqli_fetch_row($qry)) {
		$result[] = $row[0];
	}
	return $result;
}

/**
 * Get the task ids of tasks that should have been executed
 */
function getPassedTaskIds($timestamp = NULL) {
	if($timestamp === NULL) {
		$timestamp = time();
	}
	$qry = db_query('SELECT task_id FROM tasking WHERE schedule_time <= ' . $timestamp);
	$result = array();
	while($row = mysqli_fetch_row($qry)) {
		$result[] = $row[0];
	}
	return $result;
}

/**
 * Get the task array
 * @param int $task_id the id of the task
 */
function getTask($task_id) {
	$qry = db_query('SELECT * FROM tasking WHERE task_id = ' . $task_id);
	return mysqli_fetch_assoc($qry);
}

/**
 * removes the task
 * @param int $task_id the id of the task
 */
function deleteTask($task_id) {
	db_query('DELETE FROM tasking WHERE task_id = ' . $task_id);
}


function executeScript($task_id) {
	$task = getTask($task_id);
	if($task === NULL) {
		// the task has been deleted ... do nothing
		return;
	}

	$path = null;
	if(TASKING_CRONJOB_PATH != '') {
		$path = TASKING_CRONJOB_PATH;
	} else {
		$path = ROOT_PATH;
	}

	exec('php5 ' . $path . '/' . $task['script_name'] . ' ' . $task['parameters'], $ausgabe);
	if($ausgabe != NULL && $ausgabe != '') {
		echo $ausgabe;
	}

	$time = $task['schedule_time'];
	switch($task['mode'])
	{
		case TASKING_NO_RESCEDULE:
			deleteTask($task_id);
			return;
		case TASKING_5_MIN:
			$time = $time + 300; // offset of 300 sec (5 min)
			break;
		case TASKING_10_MIN:
			$time = $time + 600; // offset of 600 sec (10 min)
			break;
		case TASKING_15_MIN:
			$time = $time + 900; // offset of 900 sec (15 min)
			break;
		case TASKING_30_MIN:
			$time = $time + 1800; // offset of 1800 sec (30 min)
			break;
		case TASKING_1_HOUR:
			$time = $time + 3600; // offset of 3600 sec (1 h)
			break;
		case TASKING_2_HOUR:
			$time = $time + 7200; // offset of 7200 sec (2 h)
			break;
		case TASKING_6_HOUR:
			$time = $time + 21600; // offset of 21600 sec (6 h)
			break;
		case TASKING_12_HOUR:
			$time = $time + 43200; // offset of 43200 sec (12 h)
			break;
		case TASKING_DAYLY:
			$time = $time + 86400; // offset of 86400 sec (24 h)
			break;
		case TASKING_DAY_OF_WEEK:
			$time = $time + 604800; // offset of 604800 sec (7 days)
			break;
		case TASKING_DAY_OF_MONTH:
			// the way of calculation is somehow more complex ;)
			$time = mktime(date("H", $time), date("i", $time), date("s", $time), date("m", $time)-1, date("d", $time), date("Y", $time));
			break;
	}

	// reschedule !
	schedule($task['script_name'], $task['parameter'], $time, $task['mode'], $task['catchup']);
}

?>