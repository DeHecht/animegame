<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

session_start();
$nodisturb = 'YES';
include_once('path.inc.php'); // get the path ;)
include_once (ROOT_PATH . '/include/config.inc.php');
include_once (ROOT_PATH . '/include/messagefunctions.inc.php');
include_once (ROOT_PATH . '/include/defines.inc.php');
include_once(ROOT_PATH.'/include/random.inc.php');

defineIfNotDefined('AG_CHAT_ROWS', 25);
defineIfNotDefined('AG_CHAT_ORDER', 'ASC');

// Die Nachricht muss nicht geparst werden!

// Die ID aber schon

$anon_block = false;
$trueuser = false;


if(isset($user_ida['nickname'])){
	if(!isset($_SESSION['user_ida'])){
		$_SESSION['user_ida'] = $user_ida;
	}
	$trueuser = true;
} else if(isset($_SESSION['user_ida'])){
		$user_ida = $_SESSION['user_ida'];
} else {
	// Session has started, but user is not logged on :(
	if(!isset($_SESSION['anon_ip'])){
		$_SESSION['anon_ip'] = $_SERVER['REMOTE_ADDR'];
	}
	// Wechsel der IP
	if($_SESSION['anon_ip'] != $_SERVER['REMOTE_ADDR']){
		db_query('Update anon_chatter set ip = \''.$_SERVER['REMOTE_ADDR'].'\' where ip = \''.$_SESSION['anon_ip'].'\'');
		$_SESSION['anon_ip'] = $_SERVER['REMOTE_ADDR'];
	}
	$sql = 'Select * from anon_chatter where ip = \''.$_SESSION['anon_ip'].'\'';
	$usr = mysqli_fetch_assoc(db_query($sql));
	while(!$usr){
		silent_query('Insert into anon_chatter(ip, anon_id) values(\''.$_SESSION['anon_ip'].'\', '.mt_random_wrapper(90000,99999).')');
		$usr = mysqli_fetch_assoc(db_query($sql));
	}
	$user_ida['id'] = $usr['anon_id'];
	$user_ida['nickname'] = 'anon_' . $usr['anon_id'];

	$anon_block = $usr['muted']==0?false:true;
	db_query('UPDATE anon_chatter SET online = now() WHERE anon_id = '.$usr['anon_id']);
}

//
if (isset($_GET['send'])) {
	if(!$trueuser && $_REQUEST['myself'] != $user_ida['nickname']){
//		sendChatMessage($user_ida, '***SPAM***'.$_GET['send']);
		echo "ERROR";
	} else {
		if($anon_block === false){
			$send = $_GET['send'];
//			$send = preg_replace(array('#ace #i', '#tod#'), array('SPOIL!!', 'SPOIL!!'), $send);
			sendChatMessage($user_ida, $send, $usergroups);
		}
		echo "OK";
	}
} else if (isset ($_GET['me'])) {
		// Cachen verhindern
		header("Expires: Sat, 05 Nov 2005 00:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		echo getChatMessages($user_ida['id'], $_GET['id'], AG_CHAT_ORDER=='ASC'?TRUE:FALSE, AG_CHAT_ROWS);
} else {
		$messagearray = getChatMessages($user_ida['id'], null, AG_CHAT_ORDER=='ASC'?TRUE:FALSE, AG_CHAT_ROWS);
		$delay = 6500; // 6.5 Sekunden
		$activeTime = 30; // 30 Min

		$aktiv = round($activeTime * 60 * 1000 / $delay);
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="design/styleChat.css" rel="STYLESHEET" type="text/css">
		<script type="text/javascript" src="java/prototype-1.6.0.2.js" ></script>
		<script type="text/javascript">

			var lastmessageid = <?php echo substr($messagearray,0,strpos($messagearray,';')); ?>;
			var receiving = false;
			var aktiv = <?php echo $aktiv ?>;
			var maxMessagesDisplayed = <?php echo AG_CHAT_ROWS; ?>;
			var mode = '<?php echo AG_CHAT_ORDER; ?>';
			/*
				DESC = newest Message on top
				ASC = newest Message at bottom
				has to fit $mode in messagefunctions.inc.php getChatMessages
			*/


			function send(){
				var myAjax = new Ajax.Request("<?php echo $_SERVER['PHP_SELF']; ?>?send="+encodeURIComponent($("blubb").value)+"&myself=<?php echo $user_ida['nickname']; ?>",  { method: 'get', onComplete: sendenfertig });
				$("blubb").value = "";
				aktiv = <?php echo $aktiv ?>;
				return false;
			}

			function sendenfertig(originalRequest ) {
				if(originalRequest.responseText != "OK"){
					alert("Nachricht konnte nicht versendet werden! Chat wird neu gestartet!");
					window.location.reload();
				}

			}
			function anzeigenChat(originalRequest ) {
				var index = originalRequest.responseText.indexOf(";");
				if(index < 1){
					receiving = false;
					return;
				}
				if(!Number(originalRequest.responseText.substring(0,index))){
					receiving = false;
					return;
				} else{
					lastmessageid = originalRequest.responseText.substring(0,index);
				}
				var text = originalRequest.responseText.substring(index+1);
				var text2 = $("nope").innerHTML;
				var newMessages=0, newPos=0, oldMessages=0, oldPos=0;
				// Ermittle Anzahl neuer Nachrichten!
				while(true){
					newPos = text.indexOf("<div>",newPos)+1;
					if(newPos != 0){
						newMessages++;
					} else{
						break;
					}
				}
				// Ermittle die Anzahl der Nachrichten die aktuell dargestellt werden!
				while(true) {
					oldPos = text2.indexOf("<div>",oldPos)+1;
					if(oldPos != 0){
						oldMessages++;
					} else{
						break;
					}
				}
				// okay ... nun ermittle wieviele Nachrichten geloescht werden muessen
				newMessages = oldMessages + newMessages - maxMessagesDisplayed;

				// Loesche alte Nachrichten!
				newPos = 0;
				if(mode == 'ASC') {
					for(i=0;i<newMessages;i++){
						newPos = text2.toLowerCase().indexOf("</div>",newPos)+6;
					}
					$("nope").innerHTML = text2.substring(newPos) + text;
				} else {
					for(i=0;i<newMessages;i++){
						newPos = text2.toLowerCase().lastIndexOf("<div>")-1;
						text2 = text2.substring(0, newPos);
					}
					$("nope").innerHTML = text + text2;
				}

				receiving = false;
				return;
			}

			function loadData(){
				if(--aktiv == 0){
					alert('Du wurdest als Inaktiv erkannt, Ok anklicken um wieder Nachrichten zu empfangen!');
					aktiv = <?php echo $aktiv ?>;
				}
				if(aktiv < 0){
					return;
				}
				if(!receiving){
					receiving = true;
					var myAjax = new Ajax.Request( "<?php echo $_SERVER['PHP_SELF']; ?>?me=blablubb&id="+lastmessageid,  { method: 'get', onComplete: anzeigenChat });
				}
			}

			function activate(){
				document.forms["myForm2"].style.visibility = "hidden";
				return false;
			}

			setInterval("loadData();",<?php echo $delay ?>);

		</script>
	</head>
	<body>
		<form name="myForm" onsubmit="return send();">
			<div id="nope" name="anzeige" style="width: 100%; overflow: overflow:auto;"><?php echo substr($messagearray,strpos($messagearray,';')+1); ?></div>
				<input id="input" type="submit" name="myButton" value="send" style="position:absolute; bottom:0px; left:0px;"/><input id="blubb" name="blabla" style="position:absolute; bottom:0px; left:50px; width: 85%;" />
		</form>
	</body>
</html>

<?php

	}
?>
