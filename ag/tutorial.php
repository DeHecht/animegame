<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once('path.inc.php'); // get the path ;)
include_once (ROOT_PATH . '/include/config.inc.php');
include_once (ROOT_PATH . '/include/parse.inc.php');


$content = mysqli_fetch_row(db_query('SELECT content FROM tutorial WHERE id = 1'))[0];

?>
<table border="0">
<tr><td width="500px"><?php echo encodeNoHTMLWithBB($content); ?></td></tr>
</table>
