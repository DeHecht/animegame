<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$char_id = validateUnsignedInteger($_GET['char_id'], null);

// Unkritisch
$ab = $_GET['ab'];
$user = $user_ida;


$char_1 = getChar($char_id);
if(!isUserOwnerOf($user_ida['id'],$char_id)) { exit; }

if($char_1['status'] == 'Frei') {
	displayErrorMessage(NULL,'Dein Charakter ist frei!', displayHistoryBackLink());
	exit;
}

if($char_1['besitzer'] != $user['id']) {
	displayErrorMessage(NULL,'Das ist nicht dein Charakter!', displayHistoryBackLink());
	exit;
}

// Ermittle den Abholcode (Eingefuehrt um das nutzen von Bots zu erschweren!)
$code_row = mysqli_fetch_assoc(db_query('Select * from char_abholcodes where charid = '.$char_id));
if($code_row){
	$abholstring = 'char_id='.$char_id.'&code='.$code_row['code'];
} else{
	$abholstring = 'char_id='.$char_id;
}

#######################Kampf
if($ab == "Kampf") {
$kampf_a = mysqli_fetch_array(db_query("SELECT char1, char2, id, dauer FROM kampf WHERE db_satz='$char_id' order by id DESC LIMIT 1"));
$kampf_a1 = getChar($kampf_a['char1']);
$kampf_a2 = getChar($kampf_a['char2']);

$zeit_in_sek = $kampf_a['dauer'] - time();


echo "<SCRIPT language='JavaScript'>";
?>
	function showfight1(){
   		var Info = window.open("./kampf4.php?<?php echo $abholstring; ?>","","status=no,hotkeys=no,Height=600,Width=820,scrollbars=yes");
	}
<?php
echo "</SCRIPT>";
?>
<p align=center id="content"><b> Kampf</b><br>&nbsp; <br>
<?php

echo "<b> $kampf_a1[name] vs. $kampf_a2[name]";
?>
<div align='center' id='timer'>
<?php
	if($zeit_in_sek <= 0){
		echo '<a href="javascript:showfight1()" id="content">anzeigen</a>';
	} else{
?>
<script type='text/javascript'>
	v=new Date();
	var ht=document.getElementById('timer');
	function t(){
		n=new Date();
		s=<?php echo   $zeit_in_sek; ?>-Math.round((n.getTime()-v.getTime())/1000.);
		m=0;
		h=0;
		if(s<0){
			ht.innerHTML="<?php echo "<a href='javascript:showfight1()' id='content'>anzeigen</a>"; ?>"
		} else {
			if(s>59){
				m=Math.floor(s/60);s=s-m*60
			}
			if(m>59){
				h=Math.floor(m/60);m=m-h*60
			}
			if(s<10){
				s='0'+s
			}
			if(m<10){
				m='0'+m
			}
			ht.innerHTML=h+':'+m+':'+s
		}
		window.setTimeout('t();',950);
	}
	window.onload=t;
	</script>
<?php
	}
?>
</div>
<?php
}

#######################Clan Kampf
if($ab == "Clanfight" || $ab == "Turnier") {
	if(isEventToFetchAwaiting($char_id)) {
echo "<SCRIPT language='JavaScript'>";
?>
	function showfight(){

   		var Info = window.open("./event_abholen.php?<?php echo $abholstring.'&type='.$ab; ?>","","status=no,hotkeys=no,Height=600,Width=820,scrollbars=yes");

	}
<?php
echo "</SCRIPT>";

$zeit_in_sek = getEventStatusBlocked($char_id);
?>
<p align=center><b><?php echo $ab; ?></b><br>&nbsp; <br>
<div align='center' id='timer'>
<?php
	if($zeit_in_sek <= 0){
		echo '<a href="javascript:showfight()">anzeigen</a>';
	} else{
?>
<script type='text/javascript'>v=new Date();var ht=document.getElementById('timer');function t(){n=new Date();s=<?php echo   $zeit_in_sek; ?>-Math.round((n.getTime()-v.getTime())/1000.);m=0;h=0;if(s<0){ht.innerHTML="<?php echo "<a href='javascript:showfight()'>Ergebnis zeigen</a>"; ?>"}else{if(s>59){m=Math.floor(s/60);s=s-m*60}if(m>59){h=Math.floor(m/60);m=m-h*60}if(s<10){s='0'+s}if(m<10){m='0'+m}ht.innerHTML=h+':'+m+':'+s}window.setTimeout('t();',950);}window.onload=t;</script>
<?php
	}
?>
</div>
<?php
	} else {
		echo '<p align="center">Im Moment muss der Char vom '.$ab.' noch nicht abgeholt werden!</p>';
	}
}


#######################Schatz Suche Kampf
if($ab == "Schatz Suche") {
$kampf_a = mysqli_fetch_array(db_query('select Timestampdiff(Second,now(),dauer) as dauer, Timestampdiff(Second,now(),cancel) as cancel from quests where charid = '.$char_id));
$zeit_in_sek = $kampf_a['dauer'];
$cancel_zeit_in_sek = $kampf_a['cancel'];

echo "<SCRIPT language='JavaScript'>";
?>
	function showfight(){

   		var Info = window.open("./schatz3.php?<?php echo $abholstring; ?>","","status=no,hotkeys=no,Height=600,Width=820,scrollbars=yes");

	}
<?php
echo "</SCRIPT>";
echo "<p align=center id='content'><b> Schatz Suche </b><br>&nbsp; <br>";
?>
<div align='center' id='timer'>
<?php
	if($zeit_in_sek <= 0){
		echo '<a href="javascript:showfight()" id="content">anzeigen</a>';
	} else{

$jsfunction = "function t() {
			n=new Date();
			s=$zeit_in_sek-Math.round((n.getTime()-v.getTime())/1000.);
			m=0;
			h=0;
			c=$cancel_zeit_in_sek-Math.round((n.getTime()-v.getTime())/1000.);
			if ( s < 0 ) {
				ht.innerHTML=\"<a href='javascript:showfight()' id='content'>anzeigen</a>\";
			} else {
				if ( s > 59 ) {
					m=Math.floor(s/60);
					s=s-m*60;
				}
				if ( m > 59 ) {
					h=Math.floor(m/60);
					m=m-h*60;
				}
				if ( s < 10 ) {
					s='0'+s;
				}
				if ( m < 10 ) {
					m='0'+m;
				}
				ht.innerHTML='Wartezeit zum Abholen: '+h+':'+m+':'+s+'<br /><br />';
				
				if( c < 0 ) {
					ht.innerHTML+='<a href=\"./schatz3.php?$abholstring&abort=cancel\">abbrechen</a>';
				} else {
					m=0;
					h=0;
					s=c;
					if ( s > 59 ) {
						m=Math.floor(s/60);
						s=s-m*60;
					}
					if ( m > 59 ) {
						h=Math.floor(m/60);
						m=m-h*60;
					}
					if ( s < 10 ) {
						s='0'+s;
					}
					if ( m < 10 ) {
						m='0'+m;
					}
					ht.innerHTML+='Wartezeit zum Abbrechen: '+h+':'+m+':'+s+'<br /><br />';
				}
			}
			window.setTimeout('t();',950);
		}
		";
?>
<script type='text/javascript'>v=new Date();var ht=document.getElementById('timer');<?php echo $jsfunction; ?>window.onload=t;</script>
<?php
	}
?>
</div><p align=center>
<?php


}
?>
