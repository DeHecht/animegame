<?php

if($user_ida['nickname'] != NULL) {
	echo '<iframe src="https://webchat.quakenet.org/?nick='.preg_replace('#\s#', '_', $user_ida['nickname']).'&channels=animegame&uio=d4" width="647" height="600"></iframe>';
} else {
	echo '<iframe src="https://webchat.quakenet.org/?nick=anon_....&channels=animegame&uio=d4" width="647" height="600"></iframe>';
}


?>
