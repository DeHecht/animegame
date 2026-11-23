<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');


function checkref() {
global $HTTP_REFERER;
if(preg_match('/^http:\/\/(animegame.animeone.info.*|animegame.de.*|www.animegame.de.*|agforum.animeone.info*)/i',$HTTP_REFERER)) {
return true;
} else {
return false;
}}


function fehler($fehler, $text) {
	displayErrorMessage(NULL,$fehler, $text);
}

function gettemplate($template)
{
	if(file_exists(''.$template.'.htm'))
	{
		$file = file(''.$template.'.htm');
        	$template = implode("",$file);
        	$template = str_replace("\"","\\\"",$template);
        }
        else $template='Das Template '.$template.' existiert nicht';
        return $template;
}
?>