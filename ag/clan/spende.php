<?php
/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/designfunctions.inc.php');
include_once(ROOT_PATH.'/include/fehlerausgabe.inc.php');
include_once (ROOT_PATH . '/include/parse.inc.php');
include_once(ROOT_PATH.'/include/user.inc.php');

// GET-Section
// Kritisch (SQL-Injections)
$clan_geld=validateUnsignedInteger($_GET['clan_geld'], null);
// Unkritisch
$charm=$_GET['charm'];

$mindestchar = 12;

$clan = mysqli_fetch_array(db_query("SELECT geld FROM clan WHERE id='$user_ida[clan]' LIMIT 1"));
$user = mysqli_fetch_array(db_query("SELECT nickname, geld FROM user WHERE id='$user_ida[id]' LIMIT 1"));

if ($charm == 1) {
  if($clan_geld <= 0) {
  	db_query("INSERT INTO cheater values('$user_ida[id]','Versuchte mehr weniger als 0 zu Spenden! (Eingabe:".$clan_geld.")',now())");
  	displayErrorMessage(NULL,'Geld darf nicht unter 0 sein!', '<a href="index.php?as=clan/spende">weiter...</a>');
  	exit;
  }
  displayErrorMessage(NULL,'<p>Willst du wirklich "<strong>'.$clan_geld.'</strong> &#165;" an deinen Clan spenden ?</p><p><a href="index.php?as=clan/spende&charm=2&clan_geld='.$clan_geld.'">Ja</a>&nbsp;|&nbsp;<a href="index.php?as=clan/spende">Nein</a></p>','');
  exit;
} elseif($charm == 2) {

// Um Pufferueberlaeufe zu vermeiden, einfach die Laenge der Zeichenkette abfragen, ab der 8ten stelle also uerbe 99 Mio geht nicht aufeinmal!
if(strlen($clan_geld)>8) {
	db_query("INSERT INTO cheater values('$user_ida[id]','Versuchte mehr als 99.999.999 aufeinmal zu Spenden! (Eingabe:".$clan_geld.")',now())");
	displayErrorMessage(NULL,'Es kann nicht mehr als 99.999.999 aufeinmal gespendet werden!', '<a href="index.php?as=clan/spende">weiter...</a>');
	exit;
}

if($clan_geld <= 0) {
	db_query("INSERT INTO cheater values('$user_ida[id]','Versuchte mehr weniger als 0 zu Spenden! (Eingabe:".$clan_geld.")',now())");
	displayErrorMessage(NULL,'Geld darf nicht unter 0 sein!', '<a href="index.php?as=clan/spende">weiter...</a>');
	exit;
}

$user_geld = getRelevantMoney($user_ida['id']);

if ($user_geld < $clan_geld) {
	displayErrorMessage(NULL,'Sie haben nicht genug Geld.', '<a href="index.php?as=clan/spende">weiter...</a>');
	exit;
}

db_query("UPDATE clan SET geld=geld+'$clan_geld' WHERE id='$user_ida[clan]'");
db_query('Update user SET geld=geld-'.$clan_geld.' WHERE id='.$user_ida['id']);
db_query('UPDATE user_clan SET spende = spende + '.$clan_geld.', spende_today = spende_today + '.$clan_geld.' WHERE clanid = '.$user_ida['clan'].' and userid = '.$user_ida['id']);

if(db_affected_rows() == 0){
	db_query('INSERT INTO user_clan(spende,clanid,userid,spende_today) values('.$clan_geld.','.$user_ida['clan'].','.$user_ida['id'].','.$clan_geld.')');
}

displayErrorMessage(NULL,'An Clan Erfolgreich Gespendet', '<a href="index.php?as=clan/spende">weiter...</a>');
exit;
}
?>
<head>
<script type="text/javascript">

<!--

function show_me () {
summe = (parseInt(document.forms[0].elements["clan_geld"].value));
}


var aktiv;

function button_plus(feld,schritt)
{



			nummer=parseInt(document.forms[0].elements[feld].value)+schritt;
			document.forms[0].elements[feld].value=nummer;
			document.forms[0].elements["ges_lern"].value = parseInt(document.forms[0].elements["clan_geld"].value);
}

function button_minus(feld,min,schritt)
{
	if(document.forms[0].elements[feld].value>min)
	{


		nummer=parseInt(document.forms[0].elements[feld].value)-schritt;
		document.forms[0].elements[feld].value=nummer;

		document.forms[0].elements["ges_lern"].value = parseInt(document.forms[0].elements["clan_geld"].value);
	}
}

function start_plus(feld,schritt)
{
	aktiv = setInterval("button_plus('"+feld+"',"+schritt+")",150);
}

function start_minus(feld,min,schritt)
{
	aktiv = setInterval("button_minus('"+feld+"',"+min+","+schritt+")",150);
}

function stopp()
{
	clearInterval(aktiv);
}
-->
</script>
</head>

<?php

//$sql = 'select u.id, item_id, count(c.id) from clan_ware cw inner join user u on cw.clan = u.clan inner join chars c on u.id = c.besitzer where u.clan = '.$user_ida['clan'].' group by u.id, item_id';
//$sql = 'SELECT miete as mietkosten, item_id from clan_ware inner join clan_item on clan_item.id = clan_ware.item_id where clan = '.$user_ida['clan'].' group by clan';
$sql ='SELECT miete, item_id, name, clan_ware.id from clan_ware inner join clan_item on clan_item.id = clan_ware.item_id where clan = '.$user_ida['clan'].' order by item_id asc';
//echo $sql.'<br>';
$qry = db_query($sql);
$clanraum = array();
$clanraum_a = array();
while($row = mysqli_fetch_assoc($qry)){
	$clanraum[$row['id']] = $row;
//	print_r($row);
	$clanraum_a[] = $row;
}
$raumanzahl = count($clanraum_a);
//echo 'Raumanzahl = '.$raumanzahl.'<br>';
$sql = 'SELECT chars.clan_train, count(chars.id) as anzahl from chars inner join user on chars.besitzer = user.id where user.clan = '.$user_ida['clan'].' and chars.clan_train != 0 group by chars.clan_train';
//echo $sql.'<br>';
$qry = db_query($sql);

$clanrauminfo = array();
while($row = mysqli_fetch_assoc($qry)){
	$clanrauminfo[$row['clan_train']] = $row;
}

//print_r($clanrauminfo);

$sql = 'select clan_ware.id as clan_warenid, user.id as userid, count(chars.id) as anzahl, clan.id as clanid from clan inner join clan_ware on clan.id = clan_ware.clan inner join clan_item on clan_item.id = clan_ware.item_id inner join user on clan.id = user.clan left join chars on user.id = chars.besitzer and clan_ware.id = chars.clan_train where clan.id = '.$user_ida['clan'].' group by userid, clan_warenid order by userid, clan_warenid';
//echo $sql.'<br>';
$qry = db_query($sql);

$user_raum = array();
// Anzahl der Chars pro Raum pro User
while($row = mysqli_fetch_assoc($qry)){
	$user_raum[$row['userid']][$row['clan_warenid']] = $row;
}

//print_r($user_raum);

$clans_spend = db_query('SELECT spende, nickname, user.id as userid, spende_today, spende_yesterday FROM user left join user_clan on user.id = user_clan.userid and user.clan = user_clan.clanid WHERE user.clan = '.$user_ida['clan'].' ORDER BY spende DESC');
$member = mysqli_num_rows($clans_spend);

$sql = 'select besitzer, round(sum(1/(1+exp(3-level/12)) * nutzung)) as nutzkosten from user inner join chars on chars.besitzer = user.id inner join clan_ware on chars.clan_train = clan_ware.id inner join clan_item on clan_ware.item_id = clan_item.id where user.clan = clan_ware.clan and user.clan = '.$user_ida['clan'].' group by besitzer order by besitzer';
//echo $sql.'<br>';
$qry = db_query($sql);

$user_nutz = array();
while($row = mysqli_fetch_assoc($qry)){
	$user_nutz[$row['besitzer']] = $row;
}

//print_r($user_nutz);

$clan_kosten = 0;
$clan_untersch = 0;

for($i=0;$i<$raumanzahl;$i++){
	$clan_kosten += $clanraum_a[$i]['miete']*max($mindestchar, $clanrauminfo[$clanraum_a[$i]['id']]['anzahl']);
	if($clanrauminfo[$clanraum_a[$i]['id']]['anzahl'] < $mindestchar){
		$clan_untersch += ($mindestchar - $clanrauminfo[$clanraum_a[$i]['id']]['anzahl']) * $clanraum_a[$i]['miete'];
	}
}

//echo 'Unterschuss: '.$clan_untersch.'<br>';
$cols = 6;

?>


<table width="100%" border="0">
	<tr>
		<th colspan="<?php echo $cols+$raumanzahl; ?>" align="center">Spender Liste</th>
	</tr>
	<tr>
		<th>Member</th>
		<th>Spenden<br>Total</th>
		<th>Spenden<br>Heute</th>
		<th>Spenden<br>Gestern</th>
		<?php
			for($i=0;$i<$raumanzahl;$i++){
				echo '<th>'.join('<br>', explode(' ', $clanraum_a[$i]['name'])).'</th>';
			}
		?>
		<th>Mietgeb&uuml;hren</th>
		<th>Nutzungsgeb&uuml;hren</th>
	</tr>
	<tr>
		<th colspan="<?php echo $cols+$raumanzahl; ?>"><hr></th>
	</tr>
<?php
$summe = 0;
while($row = mysqli_fetch_assoc($clans_spend)) {
?>
	<tr>
		<th align="center"><?php echo $row['nickname']; ?></th>
		<td align="center"><?php echo displayMoney($row['spende']); ?></td>
		<td align="center"><?php echo displayMoney($row['spende_today']); ?></td>
		<td align="center"><?php echo displayMoney($row['spende_yesterday']); ?></td>
<?php
	$summe += $row['spende'];
	$costs = 0;
	for($i=0;$i<$raumanzahl;$i++){
		echo '<td align="center">'.$user_raum[$row['userid']][$clanraum_a[$i]['id']]['anzahl'].'</td>';
		$costs += $user_raum[$row['userid']][$clanraum_a[$i]['id']]['anzahl']*$clanraum_a[$i]['miete'];
	}
	if($clan_untersch != 0){
		echo '<td align="center">'.displayMoney($costs + ($clan_untersch/$member)).' (incl. '.displayMoney($clan_untersch/$member).')</td>';
	} else{
		echo '<td align="center">'.displayMoney($costs).'</td>';
	}
//	$checksumme += $costs + ($clan_untersch/$member);
?>
		<td align="center"><?php echo displayMoney($user_nutz[$row['userid']]['nutzkosten']); ?></td>
	</tr>
<?php
}

?>
	<tr>
		<th colspan="<?php echo $cols+$raumanzahl; ?>"><hr></th>
	</tr>
	<tr>
		<th><?php echo $member; ?> Member</th>
		<th><?php
			$sql = 'SELECT sum(spende) as summe, sum(spende_today) as spende_today, sum(spende_yesterday) as spende_yesterday FROM user_clan WHERE clanid ='.$user_ida['clan'];
			$qry = db_query($sql);
			$row_s = mysqli_fetch_assoc($qry);
			echo displayMoney($row_s['summe']);
		?></th>
		<th><?php echo displayMoney($row_s['spende_today']); ?> </th>
		<th><?php echo displayMoney($row_s['spende_yesterday']); ?> </th>
		<?php
			for($i=0;$i<$raumanzahl;$i++){
				if($clanrauminfo[$clanraum_a[$i]['id']]['anzahl'] < $mindestchar){
					echo '<th>'.$mindestchar.' ('.$clanrauminfo[$clanraum_a[$i]['id']]['anzahl'].')</th>';
				}else{
					echo '<th>'.$clanrauminfo[$clanraum_a[$i]['id']]['anzahl'].'</th>';
				}
			}
		?>
		<th><?php echo displayMoney($clan_kosten); ?> </th>
	</tr>
	</table>
	<br><br>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
	<input type="hidden" name="as" value="clan/spende">
	<input type="hidden" name="charm" value="1">
	<table width="75%" border="0" align="center">
	<tr>
		<td colspan="2" align="center">Clan Geld Spenden (<?php echo displayMoney($clan['geld']); ?>)</td>
	</tr>
	<tr>
		<td align="center">Dein Geld -</td>
		<td align="center"><input id="input" name="ges_lern" type="text" value="0" size="15" readonly></td>
	</tr>
	<tr>
		<td align="center">Clan Geld +</td>
		<td align="center">
			<input id="input" name="clan_geld" type="text" value="0" size="7">
			<input id="input" type="button" name="+2" value=" + " onMouseDown="start_plus('clan_geld',100);" onMouseUp="stopp();">
			<input id="input" type="button" name="-2" value="-" onMouseDown="start_minus('clan_geld','0',100);" onMouseUp="stopp();">
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="center"><input id="input" type=submit value="Geld Spenden"></form></td>
	</tr>
</table>
