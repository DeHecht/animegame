<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include "db.php";
include_once (ROOT_PATH.'/include/sqlwrapper.inc.php');

db_query("UPDATE chars SET status='Frei' WHERE status='Turnier'");
