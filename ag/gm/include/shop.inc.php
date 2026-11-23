<?php 
/*
 * Created on 31.05.2011
 *
 * @copyright (c) 2011 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
?>

<?php 
function displayShop($action, $s_id, $info,  $s_name, $s2_name, $s3_name, $s_typ, $preis, $frei, $anzahl){
	if($action == ''){
		$shop_qry = db_query('SELECT * FROM shop order by preis');
		?>
			<table border="1">
				<tr>
					<th align="center" colspan="11">Shop Angebote Übersicht<th>
				</tr>
				<tr>
					<td align="center" colspan="11">
						<a href="<?php echo $_SERVER['PHP_SELF'].'?choose=shop&action=erstellen'; ?>">Angebot erstellen</a>
					</td>
				</tr>
				<tr><td>Name</td><td>ID</td><td>Art</td>
				<td>Preis</td><td>Freischalten</td><td>Anzahl<td>bearbeiten</td></tr>
		<?php
			while($res = mysqli_fetch_assoc($shop_qry)){
				echo '<tr><td>'.$res['item'].'</td><td>'.$res['id'].'</td><td>'.$res['datensatz'].'</td>
					<td>'.$res['preis'].'</td><td>'.$res['preis_req'].'</td><td>'.$res['mal'].'
					</td><td><a href="'.$_SERVER['PHP_SELF'].'?choose=shop&action=edit&s_id='.$res['id'].'">edit</a>|<a href="'.$_SERVER['PHP_SELF'].'?choose=shop&action=delete&s_id='.$res['id'].'">delete</a></td></tr>';
			}
		?>
				<tr>
					<td align="center" colspan="11">
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
					</td>
				</tr>
			</table>
		<?php
	} else if($action == 'edit' || $action == 'erstellen'){
		if($s_name !== NULL){ // Sprich es wurde was geaendert!
			if($action == 'edit'){
				
				if ($s_typ == 'sp_ware'){
					$sql = 'Update shop set item = \''.$s_name.'\', datensatz = \''.$s_typ.'\', preis = '.$preis.', preis_req = '.$frei.', mal = '.$anzahl.', com = \''.$info.'\' where id = '.$s_id.'';
					logaction('Das Angebot für '.$s_name.' wurde editiert!');	
				}else if ($s_typ == 'ware'){
					$sql = 'Update shop set item = \''.$s2_name.'\', datensatz = \''.$s_typ.'\', preis = '.$preis.', preis_req = '.$frei.', mal = '.$anzahl.', com = \''.$info.'\' where id = '.$s_id.'';
					logaction('Das Angebot für '.$s2_name.' wurde editiert!');		
				}else if ($s_typ == 'wochen_ware'){
					$sql = 'Update shop set item = \''.$s3_name.'\', datensatz = \''.$s_typ.'\', preis = '.$preis.', preis_req = '.$frei.', mal = '.$anzahl.', com = \''.$info.'\' where id = '.$s_id.'';
					logaction('Das Angebot für '.$s3_name.' wurde editiert!');
				}		
			
				db_query($sql);
				echo $sql;
				
			} else if($action == 'erstellen'){
				logaction('Das Rezept für '.$s_name.' wurde erstellt!');
				
				if ($s_typ == 'sp_ware'){
					$sql = 'INSERT INTO shop(item, datensatz, preis, preis_req, mal, com) values(\''.$s_name.'\', \''.$s_typ.'\', '.$preis.', '.$frei.', '.$anzahl.', \''.$info.'\')';
				}else if ($s_typ == 'ware'){
					$sql = 'INSERT INTO shop(item, datensatz, preis, preis_req, mal, com) values(\''.$s2_name.'\', \''.$s_typ.'\', '.$preis.', '.$frei.', '.$anzahl.', \''.$info.'\')';					
				}else if ($s_typ == 'wochen_ware'){
					$sql = 'INSERT INTO shop(item, datensatz, preis, preis_req, mal, com) values(\''.$s3_name.'\', \''.$s_typ.'\', '.$preis.', '.$frei.', '.$anzahl.', \''.$info.'\')';
				}
				
				db_query($sql);
				echo $sql;		
			}	
		}		
			if($action == 'edit'){
				$angebot = mysqli_fetch_assoc(db_query('SELECT * FROM shop WHERE id = '.$s_id));
				$aLabel = 'editieren';
			} else{
				$aLabel = 'erstellen';		
	}
		?>
		<form action="" method="POST">
			<input name="choose" value="shop" type="hidden"></input>
			<input name="action" value="<?php echo $action; ?>" type="hidden"></input>
			<input name="s_id" value="<?php echo $s_id; ?>" type="hidden"></input>
			<table>
				<tr>
					<th align="center" colspan="2">Angebot  <?php echo $s_name;  echo $aLabel; ?><th>
				</tr>
				<tr>	
					<td>Rezept ID</td><td><?php echo $angebot['id']; ?></td>
				</tr>
				<tr>			
					<td>Name</td><td><?php echo $angebot['item']; ?></td>
				</tr>
				<tr>	
					<td>Tpy</td>
					<td height="25" width="200" valign="top">
						<select id="input" name="s_typ">
							<option value="sp_ware">SS Item</option>
							<option value="ware">Markt Item</option>
							<option value="wochen_ware">Teufelsfrüchte</option>
						</select>		
				</td>
				</tr>
				<tr>	
					<td>Bei SS Items</td>
					<td height="25" width="200" valign="top">
					<?php  
						$item = db_query('SELECT * FROM sp_item where type !=\'Teufels Frucht\' ORDER BY name ');			
					?>
						<select id="input" name="s_name">
						<option value="">SS Item wählen</option>
					<?php
						while ($row2 = mysqli_fetch_array($item)) {
						echo "<option value='$row2[name]'>$row2[name]";
						}
					?>
						</select>		
					</td>
				</tr>
				<tr>	
					<td>Bei Markt Items</td>
					<td height="25" width="200" valign="top">
					<?php  
						$item = db_query('SELECT * FROM item ORDER BY s_type ');			
					?>
						<select id="input" name="s2_name">
						<option value="">Martk Item wählen</option>
					<?php
						while ($row2 = mysqli_fetch_array($item)) {
						echo "<option value='$row2[name]'>$row2[name]";
						}
					?>
						</select>		
					</td>
				</tr>
				<tr>
					<td>Teufelsfrüchte</td>
					<td height="25" width="200" valign="top">
					<?php  
						$item = db_query('SELECT * FROM wochen_markt ORDER BY item ');			
					?>
						<select id="input" name="s3_name">
						<option value="">Teufelsfrucht wählen</option>
					<?php
						while ($row2 = mysqli_fetch_array($item)) {
						echo "<option value='$row2[item]'>$row2[item]";
						}
					?>
						</select>		
					</td>
				</tr>
				<tr>
					<td>Preis</td><td><input size="10" type="text" class="text" name="preis" value="<?php echo $angebot['preis']; ?>" /></td>
				</tr>
				<tr>
					<td>Freischaltgrenze</td><td><input size="10" type="text" class="text" name="frei" value="<?php echo $angebot['preis_req']; ?>" /></td>
				</tr>
				<tr>
					<td>Anzahl</td><td><input size="10" type="text" class="text" name="anzahl" value="<?php echo $angebot['mal']; ?>" /></td>
				</tr>
				<tr>	
					<td>Kommentar</td><td><input  size="100" type="text" class="text" name="info" value="<?php echo $angebot['com']; ?>" /></td>
				</tr>
						
				<tr>
					<td align="center" colspan="2"><input type="submit" /></td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<a href="<?php echo $_SERVER['PHP_SELF'].'?choose=shop'; ?>">Zur &Uuml;bersicht</a>
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
					</td>
				</tr>
			</table>
		</form>
		<?php		
	} else if($action == 'delete'){
		?>
			<table>
		<?php
		if($s_name !== NULL){
			$sql = 'DELETE FROM shop WHERE id='.$s_id.' ';
			db_query($sql);
			echo $sql;
		?>
				<tr>
					<td align="center" colspan="2">
						Das Angebot für"<?php echo $s_name; ?>" wurde gel&ouml;scht
					</td>
				</tr>
		<?php
		} else{
			$res = mysqli_fetch_assoc(db_query('SELECT * FROM shop WHERE id = '.$s_id));
		?>
				<tr>
					<td align="center" colspan="2">
						Das Angebot "<?php echo $res['item']; ?>" wirklich l&ouml;schen? <a href="<?php echo $_SERVER['PHP_SELF'].'?choose=shop&action=delete&s_id='.$s_id.'&s_name='.$res['item']; ?>">L&ouml;schen best&auml;tigen!</a>
					</td>
				</tr>
		<?php
		}
		?>
				<tr>
					<td align="center" colspan="2">
						<a href="<?php echo $_SERVER['PHP_SELF'].'?choose=shop'; ?>">Zur &Uuml;bersicht</a>
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
					</td>
				</tr>
			</table>
		<?php
		
	}
}

?>