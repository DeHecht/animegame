<?php
/*
 * Created on 07.03.2009
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
?>
<div align="center">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<th colspan="2" align="center">Clan Ticker</th>
		</tr>
		<tr>
			<th align="center" width="200">Datum</th>
			<th align="center">Nachricht</th>
		</tr>
<?php
		$ticks_qry = db_query('SELECT * FROM clan_ticker WHERE clanid ='.$user_ida['clan'].' ORDER BY `date` DESC LIMIT 20');
		while($tick = mysqli_fetch_assoc($ticks_qry)){
			echo '<tr><td align="center">'.$tick['date'].'</td><td>'.$tick['text'].'</td></tr>';
		}		
?>		
	</table>
</div>