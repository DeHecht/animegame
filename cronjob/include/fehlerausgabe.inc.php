<?php
/*
 * Created on 22.08.2007
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
/******************************************************************************
* Diese Funktion stellt eine Fehlerausgabe dar
*******************************************************************************
* Autor: Pascal Proksch
* Erstellungsdatum: 22.08.2007
* Zuletzt verändert: 11.04.2008
*******************************************************************************/
function displayErrorMessage($header = 'Es ist ein Fehler aufgetreten!', $fehler_m,$weiter_an){
?>
<div align="center">
  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%" height="101">
    <tr>
      <th width="586" height="22" colspan="2" align="center" id="content"><?php echo $header; ?></th>
    </tr>
    <tr>
      <th width="586" height="43" colspan="2" align="center" id="content"><?php echo $fehler_m;  ?></th>
    </tr>
    <tr>
      <th width="156" height="31" align="center" id="content"><?php echo $weiter_an; ?></th>
      <td width="424" height="31" id="content">&nbsp;</td>
    </tr>
  </table>
</div>
<?php
}
?>