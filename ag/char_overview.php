<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

/**
 * Diese Datei wird aufgerufen wenn "Charakter verwalten" ausgewaehlt wurde.
 * TODO: Layout anpassen (keine <center>, <font> und <strong> Tags mehr)
 * TODO: Alle "" Strings in '' Strings umwandeln
 * TODO: Funktioneller Aufruf, damit exit-Tags verschwinden koennen und somit der Chat angezeigt werden kann!!
 */


include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');


$chars = getCharsOfUser($user_ida['id']);
$char_zahl = 0;
foreach($chars as $my_charz){
	$POWERLEVEL5 = $my_charz['starke']+$my_charz['verteidigung']+$my_charz['speed']+$my_charz['ausdauer']+$my_charz['glueck'];
	if($my_charz['fusion']=="ja"){
		$fusi = "<br>Fusionsrasse: ".$my_charz['fusion_rasse'];
	}
	else
	$fusi = "";
	if($my_charz['type']=="Onepiece"){
		$frucht = "<br>Teufelsfrucht: ".$my_charz['frucht'];
	}
	else
	$frucht = "";
	$hptemp = explode(",", $my_charz['hp']);
	if($hptemp['1'] != 0) {
		$hpleft[$char_zahl] = $hptemp['0'] / $hptemp['1'] * 100;
	} else {
		$hpleft[$char_zahl] = 0;
	}
	$mptemp = explode(",", $my_charz['mp']);
	if($mptemp['1']) {
		$mpleft[$char_zahl] = $mptemp['0'] / $mptemp['1'] * 100;
	} else {
		$mpleft[$char_zahl] = 0;
	}
	$exptemp = explode(",", $my_charz['exp']);
	$room = db_query('SELECT ci.name FROM clan_item ci inner join clan_ware cw on ci.id = cw.item_id WHERE cw.id='.$my_charz['clan_train']);
	$clanroom=mysqli_fetch_assoc($room);

	if($clanroom['name']=="")
	$clanroom['name']="kein";

	$temproom="<br><a href=index.php?as=clan/c_ware&char_id=$my_charz[id] id=popup> Trainingsraum: ".$clanroom['name']."</a>";

	
	if($my_charz['status'] == "Schatz Suche")
	$statustemp = "Schatz%20Suche";
	else
	$statustemp = $my_charz['status'];
		

	if($my_charz['kampf_item'] != NULL && $my_charz['kampf_item'] != ',,,,'){
		//		echo 'Problematic Entry = '.$chara_1['kampf_item'].'<br>';
		$char1_buffs = mysqli_fetch_assoc(db_query('SELECT sum(i.hp) as hp, sum(i.mp) as mp, sum(i.starke) as starke, sum(i.verteidigung) as verteidigung, sum(i.speed) as speed FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE w.id IN (' . $my_charz['kampf_item'] . ')'));
	}
	$c_starke = ($char1_buffs['starke']==0)?$my_charz['starke']:($my_charz['starke']).' (+'.$char1_buffs['starke'].')';
	$c_verteidigung = ($char1_buffs['verteidigung']==0)?$my_charz['verteidigung']:($my_charz['verteidigung']).' (+'.$char1_buffs['verteidigung'].')';
	$c_speed = ($char1_buffs['speed']==0)?$my_charz['speed']:($my_charz['speed']).' (+'.$char1_buffs['speed'].')';
	$c_ausdauer = ($char1_buffs['ausdauer']==0)?$my_charz['ausdauer']:($my_charz['ausdauer']).' (+'.$char1_buffs['ausdauer'].')';
	$c_glueck = ($char1_buffs['glueck']==0)?$my_charz['glueck']:($my_charz['glueck']).' (+'.$char1_buffs['glueck'].')';
	
	
	$hp = ($char1_buffs['hp']==0)?("HP: ".$hptemp[0]." / ".$hptemp[1]):("HP: ".$hptemp[0]." / ".$hptemp[1].' (+'.$char1_buffs['hp'].')');
	$mp = ($char1_buffs['mp']==0)?("MP: ".$mptemp[0]." / ".$mptemp[1]):("MP: ".$mptemp[0]." / ".$mptemp[1].' (+'.$char1_buffs['mp'].')');
	//$mp = "MP: ".$mptemp[0]." / ".$mptemp[1].' (+'.$char1_buffs['mp'].')';
	$exp = "Exp: ".$exptemp[0]." / ".$exptemp[1];
	if($my_charz['status'] == 'Frei' ){
		$temp = 'Status: '.$my_charz['status'];
	} elseif($my_charz['status'] == 'Angemeldet' OR $my_charz['status'] == 'Kampf erstellt') {
		$temp = 'Status: '.$my_charz['status'];
	} elseif($my_charz['status'] == 'Kampf') {
		$kampf_a = mysqli_fetch_assoc(db_query("SELECT dauer FROM kampf WHERE db_satz='".$my_charz['id']."' order by id DESC LIMIT 1"));
		$zeit_in_sek[$char_zahl] = $kampf_a['dauer'] - time();
		$temp = '<a href="index.php?as=abholen&ab='.$statustemp.'&char_id='.$my_charz['id'].'" id="popup">Status: '.$my_charz['status'].'</a><br /><span id="timer_'.$char_zahl.'"></span>';
	} elseif($my_charz['status'] == 'Schatz Suche') {
		$kampf_a = mysqli_fetch_array(db_query('select Timestampdiff(Second,now(),dauer) as dauer from quests where charid = '.$my_charz['id']));
		$zeit_in_sek[$char_zahl] = $kampf_a['dauer'];
		$temp = '<a href="index.php?as=abholen&ab='.$statustemp.'&char_id='.$my_charz['id'].'" id="popup">Status: '.$my_charz['status'].'</a><br /><span id="timer_'.$char_zahl.'"></span>';
	} else {
		if(isEventToFetchAwaiting($my_charz['id'])) {
			$zeit_in_sek[$char_zahl] = getEventStatusBlocked($my_charz['id']);
			$temp = '<a href="index.php?as=abholen&ab='.$statustemp.'&char_id='.$my_charz['id'].'" id="popup">Status: '.$my_charz['status'].'</a><br /><span id="timer_'.$char_zahl.'"></span>';
		} else {
			$temp = '<span id="popup">Status: '.$my_charz['status'].'</span>';
		}
	}
	$werte = "St&auml;rke: ".$c_starke."<br>Verteidigung: ".$c_verteidigung."<br>Geschwindigkeit: ".$c_speed."<br>Ausdauer: ".$c_ausdauer."<br>Gl&uuml;ck: ".$c_glueck;
	$lp = "<br><a href=index.php?as=lernpunkte&char_id=".$my_charz['id']." id=popup>Lernpunkte: ".$my_charz['lernpunkte']."</a>";
	$tp = "<a href=index.php?as=training&char_id=".$my_charz['id']." id=popup>Trainingspunkte: ".$my_charz['training_points']."</a>".$temproom;

	//$temp = $statustemp."";
	$temp2 = "Level: ".$my_charz['level']."<br>Powerlevel: $POWERLEVEL5<br>Typ: ".$my_charz['type']."<br>Rasse: ".$my_charz['rasse'].$fusi.$frucht.$lp."<br>$tp<p>$hp<br>$mp<br>$exp<p>$werte";

	$popup[$char_zahl] = $temp.'<br>'.'<span class=overviewcharname>'.$my_charz['name'].'</span><br>'.$temp2;
	$status[$char_zahl]= $my_charz['status'];

	$char_zahl++;
}
$visiblechars = 8;

for($i=$char_zahl;$i<$visiblechars;$i++){
	$CHARAKTER_NAME[$i] = "Kein Charakter";
	$CHARAKTER_VERWALTUNG[$i] = "none";
	$CHARAKTER_LERNPUNKTE[$i] = "none";
	$CHARAKTER_TRAINING[$i] = "none";
	$CHARAKTER_STATUS[$i] = "none";
	$CHARAKTER_SCHATZ[$i] = "none";
}

if(!$chars_bilds['0']) { $chars_bilds['0'] = "bilder/Char.gif"; }
if(!$chars_bilds['1']) { $chars_bilds['1'] = "bilder/Char.gif";  }
if(!$chars_bilds['2']) { $chars_bilds['2'] = "bilder/Char.gif";  }
if(!$chars_bilds['3']) { $chars_bilds['3'] = "bilder/Char.gif";  }
if(!$chars_bilds['4']) { $chars_bilds['4'] = "bilder/Char.gif"; }
if(!$chars_bilds['5']) { $chars_bilds['5'] = "bilder/Char.gif"; }
if(!$chars_bilds['6']) { $chars_bilds['6'] = "bilder/Char.gif"; }
if(!$chars_bilds['7']) { $chars_bilds['7'] = "bilder/Char.gif"; }


?>
<table cellpadding="5" cellspacing="0"
	width="100%" height="0">
	<!-- MSTableType="layout" -->
	<tr>
	<?php
	for($i=0;$i<count($chars) ;$i++)
	{
		if($chars_bilds[$i]!='bilder/Char.gif')
		{

			echo '<td width=25% valign=top id="content" align="center">';
			echo '<img border="0" src="'.$chars_bilds[$i].'" width="75" height="75">';

			echo '<table id="hptable"><tr>';
			if($hpleft[$i] > 1)
			echo '<td id="hptd" width="'.$hpleft[$i].'%"></td>';
			if($hpleft[$i] < 100)
			echo '<td height=5px></td>';
			echo '</tr></table>';
			echo '<table id="mptable"><tr>';
			if($mpleft[$i] > 1)
			echo '<td id="mptd" width="'.$mpleft[$i].'%"></td>';
			if($mpleft[$i] < 100)
			echo '<td height=5px></td>';
			echo '</tr></table>';

			echo $popup[$i].'</a></td>';

			if($i==3){
				echo '</tr></table><hr id="hrc"><table cellpadding="0" cellspacing="0" width="100%" height="0"><tr>';
			}
		}
		if ($status[$i] != 'Frei' AND $status[$i] != 'Angemeldet' AND $status[$i] != 'Kampf erstellt') {
			?>
		<script type='text/javascript'>
					v=new Date();
					var ht<?php echo $i; ?>=document.getElementById('timer_<?php echo $i;?>');
					function t<?php echo $i; ?>(){
						n=new Date();
						s=<?php echo $zeit_in_sek[$i]; ?>-Math.round((n.getTime()-v.getTime())/1000.);
						m=0;
						h=0;
						if(s<0){
							ht<?php echo $i; ?>.innerHTML="beendet"
						}else{
							if(s>59){
								m=Math.floor(s/60);
								s=s-m*60
							}
							if(m>59){
								h=Math.floor(m/60);
								m=m-h*60
							}
							if(s<10){
								s="0"+s
							}
							if(m<10){
								m="0"+m
							}
							ht<?php echo $i; ?>.innerHTML=h+":"+m+":"+s
						}
						window.setTimeout('t<?php echo $i; ?>();',950);
					}
				</script>
				<?php
		}
	}
	?>
		<script type='text/javascript'>
		window.onload = function () {
			<?php
			for($i=0;$i<count($chars) ;$i++) {
				if ($status[$i] != 'Frei' AND $status[$i] != 'Angemeldet' AND $status[$i] != 'Kampf erstellt') {
					?>
					new t<?php echo $i; ?>();
					<?php
				}
			}
			?>
		};
		</script>
	</tr>

</table>
