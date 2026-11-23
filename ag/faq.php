<?php
/*
 * Created on 03.10.2007
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
/******************************************************************************
* Darstellen der neuen FAQ
*******************************************************************************
* Autor: Pascal Proksch
* Erstellungsdatum: 03.10.2007
* Zuletzt verändert: 03.10.2007
*******************************************************************************/

include_once('path.inc.php'); // get the path ;)
include_once ROOT_PATH.'include/faq.inc.php';
$themes = getTableOfContents('<tr><###ROW###><a href="#thema_###ID###" id="content">','</a></###ROW###></tr>');
$content = getContext('<tr><th><a name="thema_###ID###" id="content">','<tr><td id="content">','</a></th></tr>','</td></tr>');
?>
<table width="100%">
	<tr>
		<th id="content">FAQ : Frequently Asked Questions (h&auml;ufig gestellte Fragen) <th>
	</tr>
	<tr>
		<td>
			<table>
				<?php echo $themes; ?>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table>
				<?php echo $content; ?>
			</table>
		</td>
	</tr>
</table>
<?php


?>