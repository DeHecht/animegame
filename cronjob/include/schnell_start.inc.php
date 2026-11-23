<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once (ROOT_PATH.'/include/char.inc.php');

function renderSchnellleiste($user){

	$Schnell_my_chars = getCharsOfUser($user['id']);
	
	$char_zahl = 0;
	while($my_charz = mysqli_fetch_assoc($Schnell_my_chars)) {
		$POWERLEVEL5 = $my_charz['starke']+$my_charz['verteidigung']+$my_charz['speed']+$my_charz['ausdauer']+$my_charz['glueck'];
		
		
		$CHARAKTER_NAME[] = "Charakter:<br>".$my_charz[name]."";
		$CHARAKTER_LERNPUNKTE[] = "Lernp.: <a href=index.php?as=lernpunkte&char_id=$my_charz[id]>".$my_charz['lernpunkte']."</a>";
		if($user['schnelllink'] == 1) {
			$CHARAKTER_VERWALTUNG[] = "Level: $my_charz[level]";
			$CHARAKTER_TRAINING[] = "Powerlevel: $POWERLEVEL5";
			$CHARAKTER_SCHATZ[] = "Trainingsp.: $my_charz[training_points]";
		}
		
		if($user['schnelllink'] == 2) {
			$CHARAKTER_VERWALTUNG[] = "Verwaltung: <a href=index.php?as=char_profil&char_id=$my_charz[id]>Hier</a>";
			$CHARAKTER_TRAINING[] = "Training: <a href=index.php?as=training&char_id=$my_charz[id]>Hier</a>";
			$CHARAKTER_SCHATZ[] = "Schatzsuche: <a href=schatz.php?char_id=$my_charz[id]>Hier</a>";
		}
		
		if($my_charz['status'] != "Frei") {
			$CHARAKTER_STATUS[] = "<a href=\"index.php?as=abholen&ab=".$my_charz[status]."&char_id=$my_charz[id]\">".$my_charz['status']."</a>";
		} else {
			$CHARAKTER_STATUS[] = "Frei";
		}
		
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

?>

<div id="schnellbox_zu" style="position:absolute; top:0px; left:0px;">
<table border="0" cellpadding="0" cellspacing="0" width="785"
bordercolor="#3B2820" style="border-collapse: collapse"
height="25">
    <tr>
        <td colspan="<?php echo $visiblechars; ?>" width="785" height="13" align="center"><font
        color="#DADADA" size="1" face="Arial"><b>Charakter
        Schnellleiste (</b></font><a href="javascript:inix()"
        onclick="ishowText(1);"><b>&gt;&gt;&gt;&gt;</b></a>)</b></font></p>
        </td>
    </tr>
</table>

</div>



<div id="schnellbox_auf" style="visibility:hidden; position:absolute; top:0px; left:0px;">
<table border="0" cellpadding="0" cellspacing="0" width="785"
bgcolor="#4D3C31" bordercolor="#3B2820"
style="border-collapse: collapse" height="154">
    <tr>
        <td valign="top" colspan="<?php echo $visiblechars; ?>" width="785" height="13" align="center">
        	<font color="#DADADA" size="1" face="Arial"><b>Charakter Schnellleiste (</b></font>
        	<a href="javascript:inix()" onclick="ishowText(2);">
        		<font color="#FFFFFF" size="1" face="Arial"><b>&gt;&gt;&gt;&gt;</b></font>
        	</a>
        	<font color="#FFFFFF" size="1" face="Arial"><b>)</b></font>
        </td>
    </tr>
    <tr>
    	<?php
    	 for($i=0;$i<$visiblechars;$i++){
	        echo '<td width="110" height="15"><font color="#00FF00" size="1" face="Arial"><strong>'.$CHARAKTER_NAME[$i].'</strong></font></td>';
    	 }
    	 ?>
    </tr>
    <tr>
    	<?php
    	 for($i=0;$i<$visiblechars;$i++){
			echo '<td width="110" height="15"><font size="1" face="Arial">'.$CHARAKTER_LERNPUNKTE[$i].'</font></td>';
    	 }
    	 ?>
    </tr>
    <tr>
    	<?php
    	 for($i=0;$i<$visiblechars;$i++){
			echo '<td width="110" height="15"><font size="1" face="Arial">'.$CHARAKTER_VERWALTUNG[$i].'</font></td>';
    	 }
    	 ?>
    </tr>
    <tr>
    	<?php
    	 for($i=0;$i<$visiblechars;$i++){
			echo '<td width="110" height="15"><font size="1" face="Arial">'.$CHARAKTER_TRAINING[$i].'</font></td>';
    	 }
    	 ?>
    </tr>
    <tr>
    	<?php
    	 for($i=0;$i<$visiblechars;$i++){
			echo '<td width="110" height="15"><font size="1" face="Arial">'.$CHARAKTER_SCHATZ[$i].'</font></td>';
    	 }
    	 ?>
    </tr>
    <tr>
    	<?php
    	 for($i=0;$i<$visiblechars;$i++){
			echo '<td width="110" height="15"><font size="1" face="Arial">'.$CHARAKTER_STATUS[$i].'</font></td>';
    	 }
    	 ?>
    </tr>
</table>
</div>

<?php
}
?>