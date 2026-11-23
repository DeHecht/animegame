<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once(ROOT_PATH.'/include/config.inc.php');

$user1 = mysqli_fetch_array(db_query("SELECT id, nickname FROM user WHERE nickname='$name' LIMIT 1"));

$kampf_a = mysqli_fetch_array(db_query("SELECT char1, char2, id, dauer FROM kampf WHERE db_satz='$char_id' LIMIT 1"));
$kampf_a1 = mysqli_fetch_array(db_query("SELECT name, besitzer, id FROM chars WHERE id='$kampf_a[char1]' LIMIT 1"));
$kampf_a2 = mysqli_fetch_array(db_query("SELECT name, besitzer, id FROM chars WHERE id='$kampf_a[char2]' LIMIT 1"));

if($kampf_a1[besitzer] == $user1[id]) {
$user2 = mysqli_fetch_array(db_query("SELECT id, nickname FROM user WHERE id='$kampf_a2[besitzer]'  LIMIT 1"));
} else {
$user2 = mysqli_fetch_array(db_query("SELECT id, nickname FROM user WHERE id='$kampf_a1[besitzer]' LIMIT 1"));
}
if(!$user1 OR !$user2) {
echo "Fehler User wurde nicht gefunden oder der kampf Existiert nicht mehr";
exit;
}

if($chat_nachricht) {
db_query("INSERT kampf_nachricht SET  nachricht='$_POST[chat_nachricht]', user='$user1[id]', charakter='$char_id'");
?>
<meta http-equiv="refresh" content="0.5; URL=<?php echo $PHPSELF; ?>?char_id=<?php echo $char_id; ?>">
<?php
}
$zeit_in_sek = $kampf_a[dauer] - time();
?>
<table cellpadding="0" cellspacing="0" width="400" height="150">
	<!-- MSTableType="layout" -->
	<tr>
		<td height="27" valign="top">
		<p align="center"><?php echo "$kampf_a1[name] vs. $kampf_a2[name]"; 

echo "<SCRIPT language='JavaScript'>";
?>
	function showfight1(){

   		var Info = window.open("../kampf4.php?char_id=<?php echo $char_id; ?>","","status=no,hotkeys=no,Height=600,Width=820,scrollbars=yes");

	}
<?php
echo "</SCRIPT>";
?>

<div align='center' id='timer'>
<script type='text/javascript'>v=new Date();var ht=document.getElementById('timer');function t(){n=new Date();s=<?php echo   $zeit_in_sek; ?>-Math.round((n.getTime()-v.getTime())/1000.);m=0;h=0;if(s<0){ht.innerHTML="<?php echo "<a href='javascript:showfight1()'>Ergebnis zeigen</a>"; ?>"}else{if(s>59){m=Math.floor(s/60);s=s-m*60}if(m>59){h=Math.floor(m/60);m=m-h*60}if(s<10){s='0'+s}if(m<10){m='0'+m}ht.innerHTML=h+':'+m+':'+s}window.setTimeout('t();',950);}window.onload=t;</script> 
</div>

</td>
	</tr>
	<tr>
		<td height="100" valign="top">
<?php
$all_mess = db_query("SELECT n.nachricht, w.nickname FROM kampf_nachricht n LEFT JOIN user w ON(n.user=w.id) WHERE (n.user='$user1[id]' OR n.user='$user2[id]') AND (n.charakter='$kampf_a1[id]' OR n.charakter='$kampf_a2[id]') ORDER BY n.id ASC");
?>
<form action=<?php echo $PHPSELF; ?>?char_id=<?php echo $char_id; ?> method=post>
<SCRIPT Language="JavaScript">
<!--
function scrolldown()
{
	text = document.forms[0].elements["ScrollDown"].createTextRange();
	text.scrollIntoView(false); 
}   
                                          
//-->
</SCRIPT>
<head>
<BODY onMousemove="scrolldown()"> 
<meta http-equiv="refresh" content="15; URL=<?php echo $PHPSELF; ?>?char_id=<?php echo $char_id; ?>">

<textarea rows="6" cols="42" readonly name="ScrollDown">


<?php
while($row = mysqli_fetch_array($all_mess)) {
echo "$row[nickname]: $row[nachricht] \n";
}
?>
</textarea>

</td>
	</tr>
	<tr>
		<td height="23" width="400"><input name="chat_nachricht" size="50"> <input type="submit" value="GO"></td>
	</tr>

	<tr>
		<td height="23" width="400">



</td>
	</tr>



</table>

</body>

</html>