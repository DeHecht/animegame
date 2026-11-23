<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH.'/include/config/toplists.inc.php');

$toplist_string = '';
if (count($toplist) > 0 ) {
	$toplist_string = join('<br>', $toplist);
}
?>
<div id="toplists"></div>
<script type="text/javascript">
	function showToplists(){
		document.getElementById("toplists").innerHTML = '<?php echo $toplist_string; ?>';
	}
	showToplists();
</script>
