<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH.'/include/defines.inc.php');
include_once(ROOT_PATH.'/include/usergroup.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');
include_once(ROOT_PATH.'/include/sqlwrapper.inc.php');

defineIfNotDefined('GROUP_COLOR_SUPPORTER', 'gold');
defineIfNotDefined('GROUP_COLOR_GAMEMASTER', 'lime');
defineIfNotDefined('GROUP_COLOR_DEVELOPER', 'orange');
defineIfNotDefined('GROUP_COLOR_CREATIVE', 'red');
defineIfNotDefined('GROUP_COLOR_MODERATOR', 'green');

// Diese Funktion muss in ein Darstellungspackage hinein und muss noch verlegt werden, Gestaltung in css-Datei

/**
 * use generateUserLinkByID instead
 * @deprecated
 */
function displayUserLink($userid, $username = NULL, $clanpre = NULL, $clansu = NULL){
	// backwards compat!!
	return displayUserLinkByID($userid);
}

/**
 * use generateUserNameByID instead
 * @deprecated
 */
function displayUserNameByID($userid, $showClanTags = TRUE){
	return generateUserNameByID($userid, $showClanTags);
}

function generateUserNameByID($userid, $showClanTags = TRUE){
	if($userid === null){return '';}

	$groups = getUserGroups($userid);

	$sql = 'select nickname, user.id as userid, clan.clanz_pre, clan.clanz_suff from user left join clan on user.clan = clan.id where user.id = '.$userid;
	$user_info = mysqli_fetch_assoc(db_query($sql));
	if(isUserInGroup($groups, WERBUNG_AN)){
		if(isUserInGroup($groups, ADMIN)){
			$color = 'style="color:'.GROUP_COLOR_GAMEMASTER.'"';
		} else if(isUserInGroup($groups, ENTWICKLER)){
			$color = 'style="color:'.GROUP_COLOR_DEVELOPER.'"';
		} else if(isUserInGroup($groups, DESIGNER)){
			$color = 'style="color:'.GROUP_COLOR_CREATIVE.'"';
		} else if(isUserInGroup($groups, MODERATOR)){
			$color = 'style="color:'.GROUP_COLOR_MODERATOR.'"';
		} else {
			$color = 'style="color:'.GROUP_COLOR_SUPPORTER.'"';
		}
	} else{
		$color = '';
	}

	$nickname = $user_info['nickname'];
	if($user_info['clanz_pre'] !== NULL && $user_info['clanz_pre'] != '' && $showClanTags) {
		$nickname = '<span style="color:'.$GLOBALS['COLOR_CLAN_TAG'].'">'.$user_info['clanz_pre'] . '</span> ' . $nickname;
	}
	if($user_info['clanz_suff'] !== NULL && $user_info['clanz_suff'] != '' && $showClanTags) {
		$nickname = $nickname . ' <span style="color:'.$GLOBALS['COLOR_CLAN_TAG'].'">' . $user_info['clanz_suff'] . '</span>';
	}

	if($color == ''){
		return $nickname;
	} else{
		return '<span '.$color.'>'.$nickname.'</span>';
	}
}

/**
 * use generateUserLinkByID instead
 * @deprecated
 */
function displayUserLinkByID($userid, $showClanTags = TRUE){
	return generateUserLinkByID($userid, $showClanTags);
}

function generateUserLinkByID($userid, $showClanTags = TRUE){
	return '<a href="index.php?as=info&user_id='.$userid.'">'.displayUserNameByID($userid, $showClanTags).'</a>';
}


function generateAttackLinkByID($attackid, $withColor = TRUE) {
	return '<a href="index.php?as=info/attacken&p=1&at_id='.$attackid.'">'.generateAttackNameByID($attackid, $withColor).'</a>';
}

function generateAttackNameByID($attackid, $withColor = TRUE) {
	if($attackid == null) {
		return '';
	} 
	
	$sql = 'SELECT * from attacken WHERE id = ' .$attackid;
	$qry = db_query($sql);
	$row = mysqli_fetch_assoc($qry);

	$type = $row['type'];
	
	if ( $row['options'] !== NULL ) {
		$array = json_decode($row['options'], true);
		if ( $array !== NULL && isset($array['type'])) {
			$type = $array['type'];
		}
	}

	$qry = db_query('SELECT farbe FROM `attackentyp` where name = \''.$type.'\'');
	$row_color = mysqli_fetch_row($qry);
	$color = 'style="color:'.$row_color[0].'"';

	if($withColor) {
		return '<span '.$color.'>'.$row['name'].'</span>';
	} else {
		return $row['name'];
	}
}

function displayClanLink($clanid, $clanname = NULL){
	if($clanname === NULL) {
		$qry = db_query('SELECT clanname FROM clan WHERE id = ' . $clanid);
		$row = mysqli_fetch_assoc($qry);
		$clanname = $row['clanname'];
	}
	return  '<a href="index.php?as=clan/clan_info&clan_id='.$clanid.'">'.$clanname.'</a>';
}

function displayHistoryBackLink(){
	return '<a href="javascript:history.back()" id="content">zur&uuml;ck</a>';
}

function displayIndexBackLink(){
	return '<a href="index.php">zur&uuml;ck</a>';
}

function generateCharLinkByID($charid, $showAvatar = FALSE, $showClanTags = FALSE) {
	$char = getChar($charid);
	$clan_pre = null;
	$clan_su = null;
	$suffix = null;
	if($showClanTags) {
		$qry = db_query('select clan.clanz_pre, clan.clanz_suff from user left join clan on user.clan = clan.id where user.id = '.$userid);
		$row = mysqli_fetch_assoc($qry);
		$clan_pre = $row['clanz_pre'];
		$clan_su = $row['clanz_suff'];
	}

	if($showAvatar) {
		$suffix = '<br><img src="'.$char['bild'].'" border="0"  width="75" height="75">';
	}

	return '<a href="index.php?as=info&charm=1&char_id='.$charid.'">'.$clan_pre . ' ' . $char['name'] . ' ' . $clan_su . $suffix .'</a>';
}

function displayCharLink($charid, $charname, $img = NULL, $clanpre = NULL, $clansu = NULL){
	if($clanpre !== NULL && $clansu !== NULL){
		$charname = $clanpre.' '.$charname.' '.$clansu;
	}
	if($img === NULL){
		return  '<a href="index.php?as=info&charm=1&char_id='.$charid.'">'.$charname.'</a>';
	} else{
		return  '<a href="index.php?as=info&charm=1&char_id='.$charid.'">'.$charname.'<br><img src="'.$img.'" border="0"  width="75" height="75"></a>';
	}
}

function displayItemLink($tablename, $itemid, $itemname){
	if($itemname != 'Geheim!'){
		return  '<a href="index.php?as=info&charm=2&group='.$tablename.'&item_id='.$itemid.'">'.$itemname.'</a>';
	}
	return $itemname;
}

function displayAuktionsLink($kategorie,$itemnamepart,$itemname,$entriesPerSite,$pageNumber,$maxPrice){
	return '<a href="index.php?as=auktion&kategorie='.$kategorie.'&itemname='.$itemnamepart.'&entries='.$entriesPerSite.'&maxpreis='.$maxPrice.'">Hier Klicken um Auktionen des Items '.$itemname.' anzeigen zu lassen!</a>';
}

// Soll die alte displayPagelinks ersetzen
function displayPagelinksNew($entriesPerPage, $totalEntries, $aktualPage, $url){
	$maxPages = ceil($totalEntries/$entriesPerPage);
	return displayPagelinks($aktualPage, $maxPages, $url);
}

function displayPagelinks($aktualPage, $maxPages, $url){
	// Erstmal die link vorher/nachher
	$vorher = '<td align="left" width="0">vorherige Seite</td>';
	$nachher = '<td align="right" width="0">n&auml;chste Seite</td>';
	$puffer = '';
	$search = array('/###PAGE###/','/###LABEL###/');

	if(0 < $aktualPage){
		$vorher = '<td align="left" width="0">'.preg_replace($search,array($aktualPage-1,'vorherige Seite'),$url).'</td>';
	}
	if($maxPages-1 > $aktualPage){
		$nachher = '<td align="right" width="0">'.preg_replace($search,array($aktualPage+1,'n&auml;chste Seite'),$url).'</td>';
	}

	$normalStep = 1;
	$width=0;
	if($maxPages > 50){
		$normalStep = 10;
		$width = 5;
	} else if($maxPages > 30){
		$normalStep = 5;
		$width = 15;
	}
	$min = $aktualPage-$width+1;
	$max = $aktualPage+$width+1;
	for($i=1;$i<=$maxPages;$i++){
		if($i == 1 || $i%$normalStep == 0 || $i >= $min && $i <= $max){
			$puffer.='<td align="center">'.preg_replace($search,Array($i-1,$i),$url).'</td>';
		}
	}
	return '<table width="100%" cellpadding="0" cellspacing="0"><tr>'.$vorher.$puffer.$nachher.'</tr></table>';
}

function displayMoney($value){
	return number_format($value,0,'','.').' &#165;';
}

function displayCount($value){
	return number_format($value,0,'','.');
}

function generateTimerHtml($timerId, $timeInSeconds, $htmlOnExpire) {
	?>
<span id='timer_<?php echo $timerId; ?>'> <?php
if($timeInSeconds <= 0){
	echo $htmlOnExpire;
} else{
	?> <script type='text/javascript'>
		v=new Date();
		var ht=document.getElementById('timer_<?php echo $timerId; ?>');
		function t(){
			n=new Date();
			s=<?php echo $timeInSeconds; ?>-Math.round((n.getTime()-v.getTime())/1000.);
			m=0;
			h=0;
			if(s<0){
				ht.innerHTML="<?php echo $htmlOnExpire; ?>"
			} else {
				if(s>59){
					m=Math.floor(s/60);s=s-m*60
				}
				if(m>59){
					h=Math.floor(m/60);m=m-h*60
				}
				if(s<10){
					s='0'+s
				}
				if(m<10){
					m='0'+m
				}
				ht.innerHTML=h+':'+m+':'+s
			}
			window.setTimeout('t();',950);
		}
		window.onload=t;
		</script> <?php
}
?> </span>
<?php
}


?>
