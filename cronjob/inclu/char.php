<?php 
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
?>

<html>

<body>
<?php
if(!$chars_bilds[0]) { $chars_bilds[0] = "bilder/Char.gif"; }
if(!$chars_bilds[1]) { $chars_bilds[1] = "bilder/Char.gif";  }
if(!$chars_bilds[2]) { $chars_bilds[2] = "bilder/Char.gif";  }
if(!$chars_bilds[3]) { $chars_bilds[3] = "bilder/Char.gif";  }
if($chars_status[0] == "Frei") { $chars_status1[0] = "Frei"; }

else { 

$chars_status1[0] = "<a href='index.php?as=abholen&ab=$chars_status[0]&char_id=$chars_ids[0]'>Angucken</a>";
}
if($chars_status[1] == "Frei") { $chars_status1[1] = "Frei"; }

else { 

$chars_status1[1] = "<a href='index.php?as=abholen&ab=$chars_status[1]&char_id=$chars_ids[1]'>Angucken</a>";
}
if($chars_status[2] == "Frei") { $chars_status1[2] = "Frei"; }

else { 

$chars_status1[2] = "<a href='index.php?as=abholen&ab=$chars_status[2]&char_id=$chars_ids[2]'>Angucken</a>";
}
if($chars_status[3] == "Frei") { $chars_status1[3] = "Frei"; }

else { 

$chars_status1[3] = "<a href='index.php?as=abholen&ab=$chars_status[3]&char_id=$chars_ids[3]'>Angucken</a>";
}
if($chars_status[4] == "Frei") { $chars_status1[4] = "Frei"; }

else { 

$chars_status1[4] = "<a href='index.php?as=abholen&ab=$chars_status[4]&char_id=$chars_ids[4]'>Angucken</a>";
}
if($chars_status[5] == "Frei") { $chars_status1[5] = "Frei"; }

else { 

$chars_status1[5] = "<a href='index.php?as=abholen&ab=$chars_status[5]&char_id=$chars_ids[5]'>Angucken</a>";
}
if($chars_status[6] == "Frei") { $chars_status1[6] = "Frei"; }

else { 

$chars_status1[6] = "<a href='index.php?as=abholen&ab=$chars_status[6]&char_id=$chars_ids[6]'>Angucken</a>";
}
if($chars_status[7] == "Frei") { $chars_status1[7] = "Frei"; }

else { 

$chars_status1[7] = "<a href='index.php?as=abholen&ab=$chars_status[7]&char_id=$chars_ids[7]'>Angucken</a>";
}
?>
<table cellpadding="0" cellspacing="0" width="480" height="0">
	<!-- MSTableType="layout" -->
	<tr>
		<td valign="middle">
		<p align="center"><?php echo $chars_names[0]; ?></td>
		<td valign="middle">
		<p align="center"><?php echo $chars_names[1]; ?></td>
		<td valign="middle">
		<p align="center"><?php echo $chars_names[2]; ?></td>
		<td height="23" valign="middle">
		<p align="center"><?php echo $chars_names[3]; ?></td>
	</tr>
	<tr>
		<td valign="top">
		<p align="center">
		<img border="0" src="<?php echo $chars_bilds[0]; ?>" width="79" height="78"></td>
		<td valign="top">
		<p align="center">
		<img border="0" src="<?php echo $chars_bilds[1]; ?>" width="79" height="78"></td>
		<td valign="top">
		<p align="center">
		<img border="0" src="<?php echo $chars_bilds[2]; ?>" width="79" height="78"></td>
		
		<td height="95" valign="top">
		<p align="center">
		<img border="0" src="<?php echo $chars_bilds[3]; ?>" width="79" height="78"></td>
	</tr>
	<tr>
		<td width="120" valign="top">
		&nbsp;&nbsp;
<?php if($chars_hp[0]) { 
echo "
HP: $chars_hp[0] <br>&nbsp;&nbsp;
MP: $chars_mp[0]<br>&nbsp;&nbsp;
Status:  <b>$chars_status1[0]</b> </td>";
} ?> 

		<td width="120" valign="top">
		&nbsp;&nbsp;
<?php if($chars_hp[1]) { 
echo "
HP: $chars_hp[1] <br>&nbsp;&nbsp;
MP: $chars_mp[1]<br>&nbsp;&nbsp;
Status:  <b>$chars_status1[1]</b> </td>";
} ?> 
		<td width="120" valign="top">
		&nbsp;&nbsp;
<?php if($chars_hp[2]) { 
echo "
HP: $chars_hp[2] <br>&nbsp;&nbsp;
MP: $chars_mp[2]<br>&nbsp;&nbsp;
Status:  <b>$chars_status1[2]</b> </td>";
} ?> 		
		<td height="47" width="120" valign="top">
		&nbsp;&nbsp;
<?php if($chars_hp[3]) { 
echo "
HP: $chars_hp[3] <br>&nbsp;&nbsp;
MP: $chars_mp[3]<br>&nbsp;&nbsp;
Status:  <b>$chars_status1[3]</b> </td>";
} ?> 
	</tr>





	<tr>
		<td valign="middle">
		<p align="center"><?php echo $chars_names[4]; ?></td>
		<td valign="middle">
		<p align="center"><?php echo $chars_names[5]; ?></td>
		<td valign="middle">
		<p align="center"><?php echo $chars_names[6]; ?></td>
		<td height="23" valign="middle">
		<p align="center"><?php echo $chars_names[7]; ?></td>
	</tr>
	
	<tr>
		<td width="120" valign="top">
		&nbsp;&nbsp;
<?php if($chars_hp[4]) { 
echo "
HP: $chars_hp[4] <br>&nbsp;&nbsp;
MP: $chars_mp[4]<br>&nbsp;&nbsp;
Status:  <b>$chars_status1[4]</b> </td>";
} ?> 

		<td width="120" valign="top">
		&nbsp;&nbsp;
<?php if($chars_hp[5]) { 
echo "
HP: $chars_hp[5] <br>&nbsp;&nbsp;
MP: $chars_mp[5]<br>&nbsp;&nbsp;
Status:  <b>$chars_status1[5]</b> </td>";
} ?> 
		<td width="120" valign="top">
		&nbsp;&nbsp;
<?php if($chars_hp[6]) { 
echo "
HP: $chars_hp[6] <br>&nbsp;&nbsp;
MP: $chars_mp[6]<br>&nbsp;&nbsp;
Status:  <b>$chars_status1[6]</b> </td>";
} ?> 		
		<td height="25" width="120" valign="top">
		&nbsp;&nbsp;
<?php if($chars_hp[7]) { 
echo "
HP: $chars_hp[7] <br>&nbsp;&nbsp;
MP: $chars_mp[7]<br>&nbsp;&nbsp;
Status:  <b>$chars_status1[7]</b> </td>";
} ?> 
	</tr>
</table>

</body>

</html>