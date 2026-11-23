<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
?>
<?php
if (isUserInGroup($usergroups, AKTIV_CHAT)) {
	include_once (ROOT_PATH . '/include/messagefunctions.inc.php');
	if($user_ida['id'] === NULL){
		$user_ida['id'] = -1;
	}
	$temp = getChatMessages($user_ida['id'], 0, 15);
	$index = strpos($temp, ';');
	echo substr($temp, $index +1);
}
?>

<script type="text/javascript">
function popup (url) {
	fenster = window.open(url, "Popupfenster", "width=500,height=500,resizable=yes");
	fenster.focus();
	return false;
}
</script>
<br>
<a href="ag_popupchat.php" target="_blank" onclick="return popup(this.href);">Hier klicken zum mitreden!!</a>
