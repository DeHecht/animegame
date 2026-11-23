<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

	include_once(ROOT_PATH.'/include/char.inc.php');
	include_once(ROOT_PATH.'/include/ads.inc.php');

	$char_zahl = 0;
	$chars = getCharsOfUser($user_ida['id']);
	foreach($chars as $my_charz){
		$POWERLEVEL5 = getPowerLevel($my_charz['id']);
		if($my_charz['fusion']=="ja"){
			$fusi = "<br>Fusionsrasse: ".$my_charz['fusion_rasse'];
		} else{
			$fusi = "";
		}
		if($my_charz['type']=="Onepiece"){
			$frucht = "<br>Teufelsfrucht: ".str_replace('"','\\\'',$my_charz['frucht']);
		} else{
			$frucht = "";
		}
		$hptemp = explode(",", $my_charz['hp']);
		$hpleft[$char_zahl] = $hptemp['0'] / $hptemp['1'] * 100;
		$mptemp = explode(",", $my_charz['mp']);
		$mpleft[$char_zahl] = $mptemp['0'] / $mptemp['1'] * 100;
		$exptemp = explode(",", $my_charz['exp']);
		$room = db_query('SELECT ci.name FROM clan_item ci inner join clan_ware cw on ci.id = cw.item_id WHERE cw.id='.$my_charz['clan_train']);
		$clanroom=mysqli_fetch_assoc($room);

		if($clanroom['name']=="")
			$clanroom['name']="kein";

		$temproom="<br><a href=index.php?as=clan/c_ware&char_id=$my_charz[id] id=popup> Trainingsraum: ".$clanroom['name']."</a>";

		$hp = "HP: ".$hptemp['0']." / ".$hptemp['1'];
		if($my_charz['status'] == "Schatz Suche"){
			$statustemp = "Schatz%20Suche";
		} else{
			$statustemp = $my_charz['status'];
		}

		if($my_charz['kampf_item'] != NULL && $my_charz['kampf_item'] != ',,,,'){
	//		echo 'Problematic Entry = '.$chara_1['kampf_item'].'<br>';
			$char1_buffs = mysqli_fetch_assoc(db_query('SELECT sum(i.hp) as hp, sum(i.mp) as mp, sum(i.starke) as starke, sum(i.verteidigung) as verteidigung, sum(i.speed) as speed FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE w.id IN (' . $my_charz['kampf_item'] . ')'));
		}
		$c_starke = $char1_buffs['starke']==0?$my_charz['starke']:($my_charz['starke']).' (+'.$char1_buffs['starke'].')';
		$c_verteidigung = $char1_buffs['verteidigung']==0?$my_charz['verteidigung']:($my_charz['verteidigung']).' (+'.$char1_buffs['verteidigung'].')';
		$c_speed = $char1_buffs['speed']==0?$my_charz['speed']:($my_charz['speed']).' (+'.$char1_buffs['speed'].')';
		$c_ausdauer = $char1_buffs['ausdauer']==0?$my_charz['ausdauer']:($my_charz['ausdauer']).' (+'.$char1_buffs['ausdauer'].')';
		$c_glueck = $char1_buffs['glueck']==0?$my_charz['glueck']:($my_charz['glueck']).' (+'.$char1_buffs['glueck'].')';

		$mp = "MP: ".$mptemp['0']." / ".$mptemp['1'];
		$exp = "Exp: ".$exptemp['0']." / ".$exptemp['1'];
		if($my_charz['status'] == 'Frei' ){
			$temp = 'Status: '.$my_charz['status'];
			$quicklink = "<hr><a href=schatz.php?char_id=$my_charz[id] id=popup>Auf Schatzsuche schicken</a><br><a href=index.php?as=char_profil&char_id=$my_charz[id] id=popup>Attacken einstellen</a>";
		} else if($my_charz['status'] == 'Kampf Erstellt'){
			$temp = 'Status: '.$my_charz['status'];
			$quicklink = '';
		} else if($my_charz['status'] == 'Clanfight' && !isEventToFetchAwaiting($my_charz['id'], EVENT_CLAN_FIGHT)) {
			$temp = 'Status: '.$my_charz['status'];
		} else{
			$temp = "<a href=index.php?as=abholen&ab=".$statustemp."&char_id=".$my_charz['id']." id=popup>Status: ".$my_charz['status']."</a>";
			$quicklink = '';
		}
		$werte = "St&auml;rke: ".$c_starke."<br>Verteidigung: ".$c_verteidigung."<br>Geschwindigkeit: ".$c_speed."<br>Ausdauer: ".$c_ausdauer."<br>Gl&uuml;ck: ".$c_glueck;
		$lp = "<br><a href=index.php?as=lernpunkte&char_id=".$my_charz['id']." id=popup>Lernpunkte: ".$my_charz['lernpunkte']."</a>";
		$tp = "<a href=index.php?as=training&char_id=".$my_charz['id']." id=popup>Trainingspunkte: ".$my_charz['training_points']."</a>".$temproom;

		//$temp = $statustemp."";
		$temp2 = "Level: ".$my_charz['level']."<br>Powerlevel: $POWERLEVEL5<br>Typ: ".$my_charz['type']."<br>Rasse: ".$my_charz['rasse'].$fusi.$frucht.$lp."<br>$tp<p>$hp<br>$mp<br>$exp<p>$werte";

		$popup[$char_zahl] = '<a id=popup href="#" onclick="return overlib(\'<div class=&quot;tooltip_wrap&quot;><div><span class=&quot;status&quot;>'.$temp.'</span><p><span class=&quot;charname&quot;>'.$my_charz['name'].'</span></div><span class=&quot;pl&quot;>'.$temp2.$quicklink.'</div><br></div></div>\',NOCLOSE);" onmouseout="return nd();" class="q3">';
		$chars_bilds[] = $my_charz['bild'];
		$chars_names[] = $my_charz['name'];
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

for($i=0;$i<$visiblechars;$i++){
	if($chars_bilds[$i] == null) {
		$chars_bilds[$i] = 'design/bilder/avatare/Char.gif';
	}
}

// Zeige einen vertikalen Werbebanner an
echo getHorizontalAdvertisement();
?>

<br>
<br>
<table cellpadding="0" cellspacing="0" width="100%" height="0">
	<!-- MSTableType="layout" -->
	<tr>
		<?php
		for($i=0;$i<=3;$i++)
		{
			if($chars_bilds[$i]!='design/bilder/avatare/Char.gif')
			{
				echo '<td width=25% align="center" background-color: rbga(255,255,255 .7);>';
				echo $popup[$i].$chars_names[$i].'</a></td>';
			}
		}
		?>

	</tr>
	<tr>
				<?php
		for($i=0;$i<=3;$i++)
		{
			if($chars_bilds[$i]!='design/bilder/avatare/Char.gif')
			{
				echo '<td width=25% valign="top"><p align="center">';
				echo $popup[$i].'<img border="0" src="'.$chars_bilds[$i].'" width="75" height="75"></td>';
			}
		}
	echo '</tr><tr><td></td></tr><tr>';
	for($i=0;$i<=3;$i++)
	{
		if($chars_bilds[$i]!='design/bilder/avatare/Char.gif')
		{
			echo '<td align="center"><table id="hptable"><tr>';
			if($hpleft[$i] > 1)
				echo '<td id="hptd" width="'.$hpleft[$i].'%"></td>';
			if($hpleft[$i] < 100)
				echo '<td height=5px></td>';
			echo '</tr></table></td>';
		}
	}
	echo '<tr><td></td></tr><tr>';
	for($i=0;$i<=3;$i++)
	{
		if($chars_bilds[$i]!='design/bilder/avatare/Char.gif')
		{
			echo '<td align="center"><table id="mptable"><tr>';
			if($mpleft[$i] > 1)
				echo '<td id="mptd" width="'.$mpleft[$i].'%"></td>';
			if($mpleft[$i] < 100)
				echo '<td height=5px></td>';
			echo '</tr></table></td>';
		}
	}

?>
</table>
<hr id="hrc">
<table cellpadding="0" cellspacing="0" width="100%" height="0">
	<!-- MSTableType="layout" -->
	<tr>
		<?php
		for($i=4;$i<=7;$i++)
		{
			if($chars_bilds[$i]!='design/bilder/avatare/Char.gif')
			{
				echo '<td width=25% align="center" background-color: rbga(255,255,255 .7);>';
				echo $popup[$i].$chars_names[$i].'</td>';
			}
		}
		?>
	</tr>
	<tr>
		<?php
		for($i=4;$i<=7;$i++)
		{
			if($chars_bilds[$i]!='design/bilder/avatare/Char.gif')
			{
				echo '<td valign="top" width=25% align="center">';
				echo $popup[$i].'<img border="0" src="'.$chars_bilds[$i].'" width="75" height="75"></td>';
			}
		}
	echo '</tr><tr><td></td></tr><tr>';
	for($i=4;$i<=7;$i++)
	{
		if($chars_bilds[$i]!='design/bilder/avatare/Char.gif')
		{
			echo '<td align="center"><table id="hptable"><tr>';
			if($hpleft[$i] > 1)
				echo '<td id="hptd" width="'.$hpleft[$i].'%"></td>';
			if($hpleft[$i] < 100)
				echo '<td height=5px></td>';
			echo '</tr></table></td>';
		}
	}
	echo '<tr><td></td></tr><tr>';
	for($i=4;$i<=7;$i++)
	{
		if($chars_bilds[$i]!='design/bilder/avatare/Char.gif')
		{
			echo '<td align="center"><table id="mptable"><tr>';
			if($mpleft[$i] > 1)
				echo '<td id="mptd" width="'.$mpleft[$i].'%"></td>';
			if($mpleft[$i] < 100)
				echo '<td height=5px></td>';
			echo '</tr></table></td>';
		}
	}

?>

</table>
