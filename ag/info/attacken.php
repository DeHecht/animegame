<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once (ROOT_PATH . '//include/config.inc.php');
include_once (ROOT_PATH . '//include/sqlwrapper.inc.php');
include_once (ROOT_PATH . '//include/rassen.inc.php');
include_once (ROOT_PATH . '//include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');


// TODO: Muss noch angepasst werden!

function showAttDetails($at_id) {
	$at_info = mysqli_fetch_array(db_query("SELECT * FROM attacken WHERE id='".$at_id."' LIMIT 1"));
	$req_atk = explode(",", $at_info['req_atk']);
	$req_lvl = explode(",", $at_info['req_lvl']);

	$req_attack = mysqli_fetch_array(db_query("SELECT f.name AS f_name, b.name AS b_name, c.name AS c_name, d.name AS d_name, e.name AS e_name FROM attacken a LEFT JOIN attacken b ON(b.id='".$req_atk[0]."') LEFT JOIN attacken c ON(c.id='".$req_atk[1]."') LEFT JOIN attacken d ON(d.id='".$req_atk[2]."') LEFT JOIN attacken e ON(e.id='".$req_atk[3]."')   LEFT JOIN attacken f ON(f.id='".$req_atk[4]."')  WHERE a.id='".$at_id."'"));
	
	$attacken_type = $at_info['type'];
	
	if ( $at_info['options'] !== NULL ) {
		$array = json_decode($at_info['options'], true);
		if ( $array !== NULL && isset($array['type'])) {
			$attacken_type= $array['type'].' ('.$at_info['type'].')';
		}
	}

	?>
	<div align="center">
  <input type="button" value="Zur&uuml;ck" onClick="history.back()">
  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%" height="25">
    <tr>
      <td width="158" height="25">
      <p align="center"><b>Name</b></td>
      <td width="340" height="25">&nbsp;<?php echo generateAttackNameByID($at_id); ?></td>
    </tr>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>St&auml;rke</b></td>
      <td width="340" height="25">&nbsp;<?php echo $at_info['starke']; ?></td>
    </tr>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>Verteidigung</b></td>
      <td width="340" height="25">&nbsp;<?php echo $at_info['verteidigung']; ?></td>
    </tr>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>Geschwindigkeit</b></td>
      <td width="340" height="25">&nbsp;<?php echo $at_info['speed']; ?></td>
    </tr>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>HP</b></td>
      <td width="340" height="25">&nbsp;<?php echo $at_info['hp']; ?></td>
    </tr>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>MP</b></td>
      <td width="340" height="25">&nbsp;<?php echo $at_info['mp']; ?></td>
    </tr>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>Level</b></td>
      <td width="340" height="25">&nbsp;<?php echo $at_info['level']; ?></td>
    </tr>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>Geld</b></td>
      <td width="340" height="25">&nbsp;<?php echo $at_info['geld']; ?></td>
    </tr>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>Type</b></td>
      <td width="340" height="25">&nbsp;<?php echo $attacken_type; ?></td>
    </tr>
    <tr>
      <td width="158" height="25">
      <p align="center"><b>Info</b></td>
      <td width="340" height="25">&nbsp;<?php echo $at_info['info']; ?></td>
    </tr>
	<?php
	
		$rassen_1 = explode(",", $at_info['rassen']);
		$npc_type = getRaceTypeName('NPC'); // do not remove npc (there is a separate setting for this)

		for($runden = 0; $runden < count($rassen_1); $runden++) {
			$rasse = getRaceById($rassen_1[$runden]);
			if($rasse == null || $rasse['type'] == $npc_type['id']){
				continue;
			}
			$rassen_name = $rasse['name'];
	?>
	
	    <tr>
	      <td width="158" height="25">
	      <p align="center"><b>Rassen</b></td>
	      <td width="340" height="25">&nbsp;<?php echo $rassen_name; ?></td>
	    </tr>
	
	<?php
	
		}
		$x = 0;
		$req_x = 0;
		if ($req_atk[0] > 0) {
			$req_x++;
		}
		if ($req_atk[1] > 0) {
			$req_x++;
		}
		if ($req_atk[2] > 0) {
			$req_x++;
		}
		if ($req_atk[3] > 0) {
			$req_x++;
		}
		if ($req_atk[4] > 0) {
			$req_x++;
		}
		while ($x < $req_x) {
			if ($x == 0) {
				$req_attack1 = $req_attack['b_name'];
			}
			if ($x == 1) {
				$req_attack1 = $req_attack['c_name'];
			}
			if ($x == 2) {
				$req_attack1 = $req_attack['d_name'];
			}
			if ($x == 3) {
				$req_attack1 = $req_attack['e_name'];
			}
			if ($x == 4) {
				$req_attack1 = $req_attack['f_name'];
			}
	
			echo "
		    <tr>
		      <td width=158 height=25>
		      <p align=center><b>Req Technik</b></td>
		      <td width=340 height=25>&nbsp; $req_attack1 $at_info[geld] mal nutzen.</td>
		    </tr>
		";
			$x++;
		}
	?>
	
	  </table>
	</div>
	<?php
}

/*
 * This method generates a sql statement out of the given paramters
 */
function getAttackenSQLStatement($tf='',$rassen='',$type='', $search='', $colums = 'attacken.*') {
	if (!empty($tf) AND is_numeric($tf)) {
		return 'SELECT '.$colums.' FROM `attacken` WHERE Frucht='.$tf;
	} elseif (!empty($search)) {
		return 'SELECT '.$colums.' FROM `attacken` WHERE name LIKE \'%'.$search.'%\' AND info not like \'%npc attacke%\'';
	} elseif (empty($rassen)) {
		$race_type = getRaceTypeById($type);
		$ids = array();
		if($race_type !== NULL) {
			$races = getRacesByType($race_type);
			foreach ($races as $race) {
				$ids[] = 'find_in_set(\''.$race['id'].'\', rassen) != 0';
			}
			return 'SELECT '.$colums.' FROM `attacken` WHERE ('.implode(' OR ', $ids).') AND Frucht is NULL AND info not like \'%npc attacke%\'';
		} elseif ($type == -1) { // tfs
			$condition = getFruchtCondition($fruchttyp);
			return 'SELECT '.$colums.' FROM `attacken` inner join wochen_markt on attacken.frucht = wochen_markt.id WHERE '.$condition.' attacken.info not like \'%npc attacke%\'';
		} else {
			return 'SELECT '.$colums.' FROM `attacken` WHERE info NOT LIKE \'%npc attacke%\'';
		}
	} else {
		return 'SELECT '.$colums.' FROM `attacken` WHERE find_in_set(\''.$rassen.'\', rassen) != 0 AND Frucht is NULL AND info not like \'%npc attacke%\'';
	}
}


function showAtks($linkz,$tf='',$rassen='',$order='',$type='',$pagenum='',$fruchttyp='',$search='') {
	?>
	<table border="1" cellspacing="1" width="500" bgcolor="#FFFFFF"
	bordercolor="#292929" bordercolordark="#C0C0C0"
	bordercolorlight="#C0C0C0">
	    <tr>
	        <td align="center" valign="top" bgcolor="#333333"><font
	        color="#FFFFFF" size="1" face="Verdana"><b> <a href="index.php?as=info/attacken&<?php echo $linkz; ?>&order=name">Name</a></b></font></td>
	        <td align="center" valign="top" bgcolor="#333333"><font
	        color="#FFFFFF" size="1" face="Verdana"><b>HP</b></font></td>
	        <td align="center" valign="top" bgcolor="#333333"><font
	        color="#FFFFFF" size="1" face="Verdana"><b>MP</b></font></td>
	        <td align="center" valign="top" bgcolor="#333333"><font
	        color="#FFFFFF" size="1" face="Verdana"><b><a href="index.php?as=info/attacken&<?php echo $linkz; ?>&order=starke">St&auml;rke</a></b></font></td>
	        <td align="center" valign="top" bgcolor="#333333"><font
	        color="#FFFFFF" size="1" face="Verdana"><b><a href="index.php?as=info/attacken&<?php echo $linkz; ?>&order=verteidigung">Ver.</a></b></font></td>
	        <td align="center" valign="top" bgcolor="#333333"><font
	        color="#FFFFFF" size="1" face="Verdana"><b><a href="index.php?as=info/attacken&<?php echo $linkz; ?>&order=speed">Geschw.</a></b></font></td>
	        <td align="center" valign="top" bgcolor="#333333"><font
	        color="#FFFFFF" size="1" face="Verdana"><b><a href="index.php?as=info/attacken&<?php echo $linkz; ?>&order=level">LvL:</a></b></font></td>
	    </tr>
	
	
	
	<?php
	$sql = getAttackenSQLStatement($tf, $rassen, $type, $search);
	$sql .= ' '.$order.' LIMIT '.($pagenum * 20).', 20 ';
	$attacken = db_query($sql);
	
	while ($row = mysqli_fetch_array($attacken)) {
	?>
	      <tr>
	        <td align="center" valign="top" bgcolor="#333333"><font
	        size="1" face="Verdana">&nbsp;</font><font
	        color="#FFFFFF" size="1" face="Verdana"><b> <?php echo generateAttackLinkByID($row['id']); ?> </b></font></td>
	        <td align="center" valign="top" bgcolor="#333333"><font
	        color="#D1D1D1" size="1" face="Verdana"><b>&nbsp;<?php echo $row['hp']; ?></b></font></td>
	        <td align="center" valign="top" bgcolor="#333333"><font
	        color="#D1D1D1" size="1" face="Verdana"><b>&nbsp;<?php echo $row['mp']; ?></b></font></td>
	        <td align="center" valign="top" bgcolor="#333333"><font
	        color="#D1D1D1" size="1" face="Verdana"><b>&nbsp;<?php echo $row['starke']; ?></b></font></td>
	        <td align="center" valign="top" bgcolor="#333333"><font
	        color="#D1D1D1" size="1" face="Verdana"><b>&nbsp;<?php echo $row['verteidigung']; ?></b></font></td>
	        <td align="center" valign="top" bgcolor="#333333"><font
	        color="#D1D1D1" size="1" face="Verdana"><b>&nbsp;<?php echo $row['speed']; ?></b></font></td>
	        <td align="center" valign="top" bgcolor="#FFFFFF"><font
	        color="#510002" size="1" face="Verdana"><b><?php echo $row['level']; ?></b></font></td>
	    </tr>
	
	
	<?php
	
	}
	
	$row = mysqli_fetch_row(db_query(getAttackenSQLStatement($tf, $rassen, $type, $search, 'count(attacken.id)')));
	$total = $row[0];
	$pages = ceil($total / 20);
	
	$z = 1;
	$i = 0;
	#$rassen++;
	
	while ($pages > $i) {
	
		$seiten .= '&nbsp;<a href="index.php?as=info/attacken&type='.$type.'&pagenum='.$z.'&order_art='.$order_art.'&order='.$last_klick1.'&rassen='.$rassen.'">$z</a>&nbsp;';
		$z++;
		$i++;
	}
	if (!empty($search)) {
	  $url = '<a href="index.php?as=info/attacken&p=1&search='.$search.'&pagenum=###PAGE###&order_art='.$order_art.'&order='.$last_klick1.'&rassen='.$rassen.'&tf='.$tf.'&fruchttyp='.$fruchttyp.'">###LABEL###</a>';
	} else {
		$url = '<a href="index.php?as=info/attacken&type='.$type.'&pagenum=###PAGE###&order_art='.$order_art.'&order='.$last_klick1.'&rassen='.$rassen.'&tf='.$tf.'&fruchttyp='.$fruchttyp.'">###LABEL###</a>';
	}
	
	?>
	<?php
	/*
		<tr>
			<td vAlign="top" align="middle"><font color="#333333">Seiten</font></td>
			<td vAlign="top" bgColor="#333333" colspan="6">
			<font face="Verdana" size="1">&nbsp;</font><font face="Verdana" color="#ffffff" size="1"><b><?php echo $seiten; ?></b></font></td>
		</tr>*/
		echo '<tr><td bgcolor="#333333" colspan="7">'.displayPagelinksNew(20, $total, $pagenum, $url).'</td></tr>';
	?>

  </table>
	<?php
}

function searchAtks() {
	?>
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
	<input type="hidden" name="as" value="info/attacken">
	<input type="hidden" name="p" value="1">
		<table border="1" cellspacing="1" width="500" bgcolor="#F8F8F8"
		bordercolor="#292929" bordercolordark="#C0C0C0"
		bordercolorlight="#C0C0C0">
		  <tr>
		    <td align="center" width="100%" bgcolor="#333333">
		    	<font color="#FFFFFF" size="1" face="Verdana"><b>Suchen: </b></font>
		    	<font size="1" face="Verdana">
		    		<input id="input" name="search">
		    		<input id="input" type=submit value="suche">
		    	</font>
		    </td>
		  </tr>
		</table>
	</form>
	<?php
}

function atksHeaderType() {
	$choseable = array();
	$race_types = getRaceTypes();
	foreach($race_types as $type) {
		if($type['gm_only'] == false) {
			$choseable[] = $type;
		}
	}
	$choseable[] = array('name' => 'Teufelsfr&uuml;chte', 'id' => -1);
	?>
	<tr>
    <td align="center" width="100%" bgcolor="#333333">
    	<span style="color:#FFF;font-family:Verdana;font-size:10;font-weight:bold;">
    		<a href="index.php?as=info/attacken" style="color:#FFF;text-decoration:none;">Alle Attacken</a>
    		<?php 
    		foreach ($choseable as $choice) {
				echo ' | <a href="index.php?as=info/attacken&type='.$choice['id'].'" style="color:#FFF;text-decoration:none;">'.$choice['name'].'</a>';
    		}
    		?>
      </span>
    </td>
  </tr>
	<?php
}

function atksHeaderRasse($type,$link,$fruchttyp) {
	?>
	<tr>
    <td align="center" bgcolor="#333333">
    	<font color="#FFFFFF">&nbsp;</font><font color="#FFFFFF" size="1" face="Verdana"><b>
    	<?php
		
		$links = array();
    	$race_type = getRaceTypeById($type);
    	if($race_type !== NULL) {
    		$races = getRacesByType($race_type);
    		foreach ($races as $race) {
    			$links[] = '<a href="index.php?as=info/attacken&type='.$type.'&'.$link.'&rassen='.$race['id'].'">'.$race['name'].'</a>';
    		}
    	} else if($type == -1) { // TF's
    		$links[] = '<a href="index.php?as=info/attacken&type='.$type.'&fruchttyp=paramecia">Parameciafr&uuml;chte</a>';
    		$links[] = '<a href="index.php?as=info/attacken&type='.$type.'&fruchttyp=zoan">Zoanfr&uuml;chte</a>';
    		$links[] = '<a href="index.php?as=info/attacken&type='.$type.'&fruchttyp=logia">Logiafr&uuml;chte</a>';
    	}
    	echo implode(' - ', $links);
    	

    	?>
	    </b></font><font color="#FFFFFF"></font>
	  </td>
	</tr>
	<?php
	if($type == -1 and isset($fruchttyp)) {
	?>
	<tr>
    		<td align="center" bgcolor="#333333">
        		<font color="#FFFFFF">&nbsp;</font><font color="#FFFFFF" size="1" face="Verdana"><b>

	<?php
	$condition = getFruchtCondition($fruchttyp);
	$tf_query = db_query('SELECT * FROM wochen_markt WHERE '.$condition.' 1 ORDER BY item ASC');
	$first = true;
	while ($tf_array= mysqli_fetch_assoc($tf_query)) {
		if($first) {
			$first = false;
		} else {
			echo ' - ';
		}
		echo '<a href="index.php?as=info/attacken&type='.$type.'&fruchttyp='.$fruchttyp.'&'.$link.'&tf='.$tf_array['id'].'">'.$tf_array['item'].'</a>';
	}
	?>
	            	<b></font><font color="#FFFFFF"></font>
          	</td>
        </tr>
	<?php

	}

}

function atksHeader($link,$type='',$fruchttyp='') {
	?>
	<table border="1" cellspacing="1" width="500" bgcolor="#F8F8F8"
	bordercolor="#292929" bordercolordark="#C0C0C0"
	bordercolorlight="#C0C0C0">
	    <?php
	    atksHeaderType();
	    atksHeaderRasse($type,$link,$fruchttyp);
	    ?>
	</table>
	<?php
}

function getFruchtCondition($fruchttyp) {
	switch (strtolower($fruchttyp)) {
		case 'paramecia':
		case 'zoan':
		case 'logia':
			return ' kategorie = \''.$fruchttyp.'\' AND ';
		default:
			return '';
	}
}

$type= validateInteger($_GET['type']);
$fruchttyp= validateString($_GET['fruchttyp']);
$rassen = validateUnsignedInteger($_GET['rassen']);
$tf = validateUnsignedInteger($_GET['tf']);
$search = validateString($_GET['search']);
$order = validateStringCritical($_GET['order']);
$order_art = validateStringCritical($_GET['order_art']);
$pagenum = validateUnsignedInteger($_GET['pagenum']);
$seiten = validateUnsignedInteger($_GET['seiten']);
$last_klick1 = validateString($_GET['last_klick1']);
$p = validateUnsignedInteger($_GET['p']);
$at_id = validateUnsignedInteger($_GET['at_id']);

if  (!isset($pagenum)) {
	$pagenum= 0;
}
if (!$order_art) {
	$order_art = "asc";
	$last_klick = "id";
}
if (!$last_klick1) {
	$last_klick1 = 'id';
}
if ($order_art == "asc" AND $last_klick1 == $order) {
	$order_art = "desc";
} else {
	if ($order_art == "desc" AND $last_klick1 == $order) {
		$order_art = "asc";
	}
}
$last_klick1 = $order;

if (!empty($rassen)) {
	$rassen= $rassen;
} else {
	$rassen= '';
}
/*if ($rassen AND $rassen1 != ",") {
	$rassen--;
} else {
	$rassen = ",";
	$rassen1 = 0;
}*/
if ($order) {
	$order = "order by $order $order_art";
} else {
	$order = "order by frucht,level,id asc";
}

#$rassen1 = $rassen +1;
#$linkz = "seiten=$seiten&order_art=$order_art&last_klick1=$last_klick1&rassen=$rassen1&teufel=$teufel";
if (!empty($type) AND empty($fruchttyp)) {
  $linkz = 'type='.$type.'&pagenum='.$pagenum.'&order_art='.$order_art.'&last_klick1='.$last_klick1.'&rassen='.$rassen.'&teufel='.$teufel;
} elseif (!empty($type) AND !empty($fruchttyp)) {
  $linkz = 'type='.$type.'&fruchttyp='.$fruchttyp.'&pagenum='.$pagenum.'&order_art='.$order_art.'&last_klick1='.$last_klick1.'&rassen='.$rassen.'&teufel='.$teufel;
} else {
  $linkz = 'pagenum='.$pagenum.'&order_art='.$order_art.'&last_klick1='.$last_klick1.'&rassen='.$rassen.'&teufel='.$teufel;
}
$link = "pagenum=$pagenum&order_art=$order_art&last_klick1=$last_klick1";
if ($p) {
	if ($_GET['search']) {
		$anzahl_atks = mysqli_num_rows(db_query("SELECT id FROM attacken WHERE name LIKE '%".trim($search)."%'"));
	  if ($anzahl_atks == 1) {
			$at_z = mysqli_fetch_array(db_query("SELECT id FROM attacken WHERE name='".trim($search)."' LIMIT 1"));
			if (empty($at_z)) {
			  $at_z = mysqli_fetch_array(db_query("SELECT id FROM attacken WHERE name LIKE '%".trim($search)."%' LIMIT 1"));
			}
			showAttDetails($at_z['id']);
		} else {
			$linkz = 'p=1&search='.trim($search).'&seiten='.$seiten.'&order_art='.$order_art.'&last_klick1='.$last_klick1.'';
			searchAtks();
			atksHeader($link,$type,$fruchttyp);
      showAtks($linkz,$tf,'',$order,$type,$pagenum,$fruchttyp,trim($search));
		}
	} elseif (isset($at_id)) {
	  showAttDetails($at_id);
	}





	exit;
}
?>

	<?php
	searchAtks();
	atksHeader($link,$type,$fruchttyp);
	showAtks($linkz,$tf,$rassen,$order,$type,$pagenum,$fruchttyp);
	?>
  </center>
</div>
