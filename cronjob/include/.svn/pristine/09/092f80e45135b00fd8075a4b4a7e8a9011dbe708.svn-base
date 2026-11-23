<?php
/*
 * Created on 22.08.2007
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
/******************************************************************************
* Dieser Skript enthält Funktionen für encodieren und decodieren von Nachrichten
*******************************************************************************
* Autor: Pascal Proksch
* Erstellungsdatum: 22.08.2007
* Zuletzt verändert: 22.08.2007
*******************************************************************************/

function encodeNoHTMLnoBBAlBhed($msg){
	$msg = $msg[1];
	$albhed = array ( 'e' => 'a','p' => 'b','s' => 'c','t' => 'd','i' => 'e','w' => 'f','k' => 'g','n' => 'h','u' => 'i','v' => 'j','g' => 'k','c' => 'l','l' => 'm','r' => 'n','y' => 'o','b' => 'p','x' => 'q','h' => 'r','m' => 's','d' => 't','o' => 'u','f' => 'v','z' => 'w','q' => 'x','a' => 'y','j' => 'z',
					  'E' => 'A','P' => 'B','S' => 'C','T' => 'D','I' => 'E','W' => 'F','K' => 'G','N' => 'H','U' => 'I','V' => 'J','G' => 'K','C' => 'L','L' => 'M','R' => 'N','Y' => 'O','B' => 'P','X' => 'Q','H' => 'R','M' => 'S','D' => 'T','O' => 'U','F' => 'V','Z' => 'W','Q' => 'X','A' => 'Y','J' => 'Z',
					  'ü' => 'ia', 'Ü' => 'Ia', 'ö' => 'ua', 'Ö' => 'Ua', 'ä' => 'ya', 'Ä' => 'Ya', 'ß' => 'cc'
					);
    $in_html = 0;
    $in_bb_brackets = 0;
	for($i=0;$i<strlen($msg);$i++){
		$str = mb_substr($msg,$i,1,'UTF-8');
		if($albhed[$str] != null && $in_html == 0 && $in_bb_brackets == 0){
			$result .= $albhed[$str];
		} else{
			switch($str){
				case '<':
					$in_html++;
					break;
				case '[':
					$in_bb_brackets++;
					break;
				case ']':
					if($in_bb_brackets > 0){
						$in_bb_brackets--;
					}
					break;
				case '>':
					if($in_html > 0){
						$in_html--;
					}
					break;
			}
			$result .= $str;
		}
	}
	return '[text='.$msg.']'.$result.'[/text]';
}


function decodeNoHTMLNoBBAlBhed($msg){
//	Al Bhed: A B C D E F G H I J K L M N O P Q R S T U V W X Y Z
// Deutsch: E P S T I W K N U V G C L R Y B X H M D O F Z Q A J

	$albhed = array ( 'a' => 'e', 'b' => 'p', 'c' => 's', 'd' => 't', 'e' => 'i', 'f' => 'w', 'g' => 'k', 'h' => 'n', 'i' => 'u', 'j' => 'v', 'k' => 'g', 'l' => 'c', 'm' => 'l', 'n' => 'r', 'o' => 'y', 'p' => 'b', 'q' => 'x', 'r' => 'h', 's' => 'm', 't' => 'd', 'u' => 'o', 'v' => 'f', 'w' => 'z', 'x' => 'q', 'y' => 'a', 'z' => 'j',
					  'A' => 'E', 'B' => 'P', 'C' => 'S', 'D' => 'T', 'E' => 'I', 'F' => 'W', 'G' => 'K', 'H' => 'N', 'I' => 'U', 'J' => 'V', 'K' => 'G', 'L' => 'C', 'M' => 'L', 'N' => 'R', 'O' => 'y', 'p' => 'B', 'Q' => 'X', 'R' => 'H', 'S' => 'M', 'T' => 'D', 'U' => 'O', 'V' => 'F', 'W' => 'Z', 'X' => 'Q', 'Y' => 'A', 'Z' => 'J',
);
    $in_html = 0;
    $in_bb_brackets = 0;
	for($i=0;$i<strlen($msg);$i++){
		$str = mb_substr($msg,$i,1,'UTF-8');
		if($albhed[$str] != null && $in_html == 0 && $in_bb_brackets == 0){
			$result .= $albhed[$str];
		} else{
			switch($str){
				case '<':
					$in_html++;
					break;
				case '[':
					$in_bb_brackets++;
					break;
				case ']':
					if($in_bb_brackets > 0){
						$in_bb_brackets--;
					}
					break;
				case '>':
					if($in_html > 0){
						$in_html--;
					}
					break;
			}
			$result .= $str;
		}
	}
	return $result;
}

function encodeNoHTMLWithBB($msg){
	// Erstma das albhed-Tag, da dieses sehr speziell ist!
	$msg = preg_replace_callback('#\[albhed](.*?)\[/albhed]#', 'encodeNoHTMLnoBBAlBhed',$msg);
	$convert = bbcode(encodeNoHTMLNoBB($msg));
	return $convert;
}

function encodeNoHTMLNoBB($msg){
	return addslashes(trim(preg_replace(array('/</','/>/'),array('&lt;','&gt;'),$msg)));
}

function encodeImageUrl($url){
	// Das muster nachdem die Bilderlinks bearbeitet werden
	$search = array('#.*[ ]([a-z]+?://){1}(.*?)#','#<#','#>#') ;
	$replace = array('\1\2','&lt;','&lg;') ;
	$url = preg_replace($search,$replace,$url);
	return $url;
}

function decode($msg){
// Falls mal irgendwas schnell eingefuegt werden muss
	$search = array('#:\)#',
								'#:D#',
								'#:d#',
								'#:o#',
								'#:O#',
								'#:P#',
								'#:p#',
								'#;P#',
								'#;p#',
								'#;\)#',
								'#:\(#',
								'#:\'\(#',
								'#:\$#',
								'#\(h\)#',
								'#\(H\)#',
								'#:s#',
								'#:S#',
								'#xD#',
								'#\^\^#',
								'#\*pirate\*#',
								'#\*nerd\*#'
								);
	$replace = array('<img src="pjirc/img/sourire.gif"></img>',
								'<img src="pjirc/img/content.gif"></img>',
								'<img src="pjirc/img/content.gif"></img>',
								'<img src="pjirc/img/OH-1.gif"></img>',
								'<img src="pjirc/img/OH-2.gif"></img>',
								'<img src="pjirc/img/langue.gif"></img>',
								'<img src="pjirc/img/langue.gif"></img>',
								'<img src="pjirc/img/clin-oeuil-langue.gif"></img>',
								'<img src="pjirc/img/clin-oeuil-langue.gif"></img>',
								'<img src="pjirc/img/clin-oeuil.gif"></img>',
								'<img src="pjirc/img/triste.gif"></img>',
								'<img src="pjirc/img/pleure.gif"></img>',
								'<img src="pjirc/img/rouge.gif"></img>',
								'<img src="pjirc/img/cool.gif"></img>',
								'<img src="pjirc/img/cool.gif"></img>',
								'<img src="pjirc/img/roll-eyes.gif"></img>',
								'<img src="pjirc/img/roll-eyes.gif"></img>',
								'<img src="pjirc/img/sams_teeth1.png"></img>',
								'<img src="pjirc/img/sams_hahaha2.gif"></img>',
								'<img src="pjirc/img/pirate.gif"></img>',
								'<img src="pjirc/img/sams_annoying-nerd.png"></img>'
								);
	return preg_replace($search,$replace,$msg);
}

function checkImageUrl($url){
	// Das muster nachdem die Bilderlinks bearbeitet werden
	$search = array('#.*[ ]([a-z]+?://){1}(.*?)#','#<#','#>#') ;
	$replace = array('\1\2','&lt;','&lg;') ;
	$url = preg_replace($search,$replace,$url);

	if(substr($url,0,4) != 'http' && substr($url,0,3) != 'ftp'){ // Gueltige Links beginnen mit http oder ftp
		return 'Kein g&uuml;ltiger Link!';
	}
	$info = @getimagesize($url);
	if($info[0] == NULL || $info[1] == NULL){
		return 'Bild existiert nicht??';
	} else if($info[0] > 75 || $info[1] > 75){
		echo 'Das Bild '.htmlspecialchars($url).' ist zu gross!!->'.$info[0].'x'.$info[1];
	} else{
		return '';
	}
}


function bbcode($messagetext) {
    $search = array('/\[AMP\]/',
					'/\[ADD\]/',
					'#\[url]([a-z]+?://){1}(.*?)\[/url]#',
                    '#\[url](.*?)\[/url\]#',
                    '#\[url=([a-z]+?://){1}(.*?)\](.*?)\[/url]#',
                    '#\[b](.*?)\[/b\]#',
                    '#\[i](.*?)\[/i\]#',
                    '#\[u](.*?)\[/u\]#',
                    '#\[s](.*?)\[/s\]#',
                    '#\[sub](.*?)\[/sub\]#',
                    '#\[sup](.*?)\[/sup\]#',
                    '#\[color=\#?([0-9a-fA-F]{6})\](.*?)\[/color\]#',
                    '#\[color=([a-zA-Z]+?)\](.*?)\[/color\]#',
                    '#\[text=(.*?)\](.*?)\[/text\]#',
                    '#\[h(\d)\](.*?)\[/h\d\]#',
                    '#\n#'
                    //'#\[img]([a-z]+?://){1}(.*?)\[/img]#',
                    //'#\[img](.*?)\[/img\]#'
                    ) ;

    $replace = array('&amp;',
					'+',
					'<a href="\1\2">$1$2</a>',
                    '<a href=http://\1\2>\1</a>',
                    '<a href="\1\2">\3</a>',
                    '<b>\1</b>',
                    '<i>\1</i>',
                    '<u>\1</u>',
                    '<s>\1</s>',
                    '<sub>\1</sub>',
                    '<sup>\1</sup>',
                    '<span style="color:#\1">\2</span>',
                    '<span style="color:\1">\2</span>',
                    '<span title="\1">\2</span>',
                    '<h\1>\2</h\1>',
                    '<br>'
                    //'<img src="\1\2">',
                    //'<img src="\1\2" />'
                    ) ;
    $messagetext = preg_replace($search, $replace, $messagetext);
    return $messagetext;
}

function debbcode($messagetext){
    $search = array('/&amp;/',
					'/\+/',
                    '#\<a href=\"([a-z]+?://){1}(.*?)\"\>(.*?)\</a\>#',
                    '#\<b\>(.*?)\</b\>#',
                    '#\<i\>(.*?)\</i\>#',
                    '#\<s\>(.*?)\</s\>#',
                    '#\<u\>(.*?)\</u\>#',
                    '#\<sub\>(.*?)\</sub\>#',
                    '#\<sup\>(.*?)\</sup\>#',
                    '#\<span style="color:\#?([0-9a-fA-F]{6})"\>(.*?)</span\>#',
                    '#\<span style="color:([a-zA-Z]+?)"\>(.*?)</span\>#',
                    '#<br>#',
		    '#\<span title="([^"]*?)"\>[^\<]*\</span\>#'
                    //'<img src="\1\2">',
                    //'<img src="\1\2" />'
                    ) ;


    $replace = array('[AMP]',
					'[ADD]',
                    '[url=\1\2]\3[/url]',
                    '[b]\1[/b]',
                    '[i]\1[/i]',
                    '[s]\1[/s]',
                    '[u]\1[/u]',
                    '[sub]\1[/sub]',
                    '[sup]\1[/sup]',
                    '[color=#\1]\2[/color]',
                    '[color=\1]\2[/color]',
                    "\n",
		    '[albhed]\1[/albhed]'
                    //'#\[img]([a-z]+?://){1}(.*?)\[/img]#',
                    //'#\[img](.*?)\[/img\]#'
                    ) ;

    $messagetext = preg_replace($search, $replace, $messagetext);
    return $messagetext;
}

function validateInteger($value, $default=null){
	return is_numeric($value)?$value:$default;
}

function validateIntegerArray($value, $default){
	if($value == null)
		$value = array();

	foreach($value as $i => $v) {
		$value[$i] = validateInteger($v, $default);
	}
	return $value;
}

function validateUnsignedInteger($value, $default=null){
	return !is_numeric($value)||$value<0?$default:$value;
}

function validateUnsignedIntegerArray($value, $default){
	if($value == null)
		$value = array();

	foreach($value as $i => $v) {
		$value[$i] = validateUnsignedInteger($v, $default);
	}
	return $value;
}

function validateString($value){
	return $value != null?encodeNoHTMLNoBB($value):null;
}

function validateStringArray($value){
	if($value == null)
		$value = array();

	foreach($value as $i => $v) {
		$value[$i] = validateString($v);
	}
	return $value;
}

function validateStringCritical($value) {
	if($value == null) {
		return null;
	}
	if(preg_match('#[\\\'"()\]\[\s]#', $value))
		return '';
	$value = validateString($value);
	return preg_replace('#[()\]\[\s]#', '', $value);
}

function validateEmailAddress($mail){
	return validateString($mail);
}

function validateName($name){
	$allowed = "/[^a-z0-9\\040\\-\\_äöü]/i";
	// Beschraenke die Eingabezeichen
	$da_name = trim(addslashes(preg_replace($allowed,'',$name)));
	if(strlen($da_name) < 2){
		return NULL;
	}
	return $da_name;
}

function validateURL($url){
	$str = validateString($url);
	$pos = strpos($str, 'http://');
	$pos_ssl = strpos($str, 'https://');
	if($str != 'design/bilder/avatare/noavart.gif' && ($pos === false || $pos != 0) && ($pos_ssl === false || $pos_ssl != 0)){
		return '';
	} else{
		return $str;
	}
}

function formatDate($dateString) {
	return preg_replace('#([\d]+?)-([\d]+?)-([\d]+?)#', '\3.\2.\1', $dateString);
}

function formatTime($timeString) {
	// Not yet required!
	return $timeString;
}

function formatTimestamp($timestampString) {
	return preg_replace('#([\d]+?)-([\d]+?)-([\d]+?) ([\d:]+?)#', '\3.\2.\1 \4', $timestampString);
}

function formatTimestampShortYear($timestampString) {
	return preg_replace('#[\d]{2}([\d]+?)-([\d]+?)-([\d]+?) ([\d:]+?)#', '\3.\2.\1 \4', $timestampString);
}

?>
