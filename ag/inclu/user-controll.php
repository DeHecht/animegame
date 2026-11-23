<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once (ROOT_PATH . '//include/online.inc.php');
include_once(ROOT_PATH.'//include/designfunctions.inc.php');
include_once (ROOT_PATH . '/include/clan.inc.php');
include_once (ROOT_PATH . '/include/clan_chat.inc.php');


$itemsx1 = mysqli_num_rows(db_query("SELECT id FROM sp_ware WHERE item='1' AND user='$user_ida[id]'"));
$itemsz1 = mysqli_fetch_array(db_query("SELECT id FROM sp_ware WHERE item='1' AND user='$user_ida[id]' LIMIT 1"));

$itemsx2 = mysqli_num_rows(db_query("SELECT id FROM sp_ware WHERE item='2' AND user='$user_ida[id]'"));
$itemsz2 = mysqli_fetch_array(db_query("SELECT id FROM sp_ware WHERE item='2' AND user='$user_ida[id]' LIMIT 1"));

$anmeldungen = mysqli_num_rows(db_query("SELECT id FROM user"));
$db_info = mysqli_num_rows(db_query("SELECT id FROM sp_ware WHERE user='$user_ida[id]' AND item='11'"));

$user_info_test = mysqli_fetch_array(db_query("SELECT geld FROM user WHERE nickname='$name' LIMIT 1"));

//$nnn_nn = mysqli_num_rows(db_query("SELECT id FROM nachricht WHERE besitzer='$user_ida[id]' AND ag='neu'"));
//echo $nnn_nn;
$zeit = date("H:i:s");
$date = date("d.m.y");
$user_info_test2['geld'] = displayMoney($user_info_test['geld']);
?>
<SCRIPT language="JavaScript">

	function markt(){
		var Info = window.open("markt.php","","status=no,hotkeys=no,Height=500,Width=700,scrollbars=yes");
	}

</SCRIPT>
<?php

if ($name AND $passwort) {

	$charakter_009 = "
	 <a href='index.php?as=char'>Charakter Erstellen</a><br>
	 <a href='index.php?as=char_index'>Charakter Verwaltung</a><br>
	 <a href='index.php?as=training'>Charakter Training</a><br>
	 <a href='index.php?as=info/lern'>Meine Techniken</a><br>
	 <a href='index.php?as=lernpunkte'>Lernpunkte</a><br>
	 <a href='index.php?as=schatz_suche'>Schatz Suche</a><br>
	 <a href='index.php?as=arena'>Arena</a><br>
	 <a href='index.php?as=liga_kampf_anzeige'>Meine Liga K&auml;mpfe</a><br>
	 <a href='index.php?as=wanted_kampf_anzeige'>Meine Wanted K&auml;mpfe</a><br>
	";
	$charakter_009 = "<a href=index.php?as=char_overview>Charakter &Uuml;bersicht</a><br>".$charakter_009;


	$training_009 = "";

	$turnier_009 = '';

	$tournaments = db_query('SELECT id, abbrevation FROM tournament_types WHERE gain = 0');
	foreach ($tournaments as $tournament) {
		$turnier_009 = $turnier_009 . '<a href="index.php?as=turnier&type_id='.$tournament['id'].'">'.$tournament['abbrevation'].' Anmeldung</a><br>';
	}

	$turnier_009 = $turnier_009 . '<a href="index.php?as=turnier_auswahl">Turnier Angucken</a><br>';

	if ($user_ida['clan'] == 0) {
		$clan_009 = "
		 <a href=index.php?as=clan/new>Clan Gr&uuml;nden</a><br>
		 <a href=index.php?as=clan/add>Clan Beitreten</a><br>";

	} else {

		if(isClanChatAvailable($user_ida['clan'])) {
			$clan_chat_id = getClanChatId('clan_'.$user_ida['clan']);
			$chat_link = '<a href=\'clan_popupchat.php?clan_chat_id='.$clan_chat_id.'\' target=\'_blank\'>Clan Chat</a><br>';
		} else {
			$chat_link = '';
		}

	$clan_009 = "
		 <a href=index.php?as=clan/clan_info>Mein Clan</a><br>
		 ".$chat_link."
		 <a href=index.php?as=clan/kampf_info>Clan K&auml;mpfe</a><br>
		 <a href=index.php?as=clan/c_markt>Clan Markt</a><br>
		 <a href=index.php?as=clan/c_ware>Clan Item Nutzung</a><br>
		 <a href=index.php?as=clan/spende>Geld Spenden</a><br>
		 <a href=index.php?as=clan/c_ticker>Clan Ticker</a><br>
		 <a href=index.php?as=clan/add>Clan Wechseln</a><br>
		 ";
	}

//	$clan_009 .= "<a href=index.php?as=clan/c_kampf&id=fights>Clan K&auml;mpfe Anzeige</a><br>";

	// Ist user der leader?
//	$chiefs = mysqli_fetch_assoc(db_query('Select co_leader, leader from clan where id = '.$user_ida['clan']));
//	if($chiefs['co_leader'] == $user_ida['id'] || $chiefs['leader'] == $user_ida['id']){
//		$clan_009 .= '<a href=index.php?as=clan/c_kampf>Clan K&auml;mpfe</a><br>';
//	}

	$info_009 = "
	 <a href=index.php?as=auser>Alle User</a><br>
	 <a href=index.php?as=info/attacken>Alle Attacken</a><br>
	 <a href=index.php?as=ranglist>Top 50 User</a><br>
	 <a href=index.php?as=clan/c_ranglist>Top 10 Clan</a><br>
	 <a href=index.php?as=wanted>Top 10 wanted</a><br>
	 <a href=index.php?as=online>Wer ist Online?</a><br>
	 <a href=index.php?as=liga1>Liga 1</a><br>
	 <a href=index.php?as=halloffame>Halle der Helden</a><br>
	 <a href=index.php?as=last_fight>Letzten 20 K&auml;mpfe</a><br>";

	$user_geld = getRelevantMoney($user_ida['id']);
	
	if ($user_info_test['geld'] == $user_geld) {
		$geldstring = $user_info_test2['geld'];
	} else {
		$geldstring = displayMoney($user_geld) . ' (' . $user_info_test2['geld'] . ')';
	}


	$status_009 .= "Geld: $geldstring<br>";

	$status_009 .= '<a href="index.php?as=profil">Profil</a><br>';
	$status_009 .= '<a href="index.php?as=ticker">User Ticker</a><br>';
	$status_009 .= '<a href="index.php?as=nachricht">User Postfach</a><br>';

	$status_009 .= '<a href="index.php?as=item">Meine Items</a><br>';
	$status_009 .= '<a href="index.php?as=sets">Crafting</a><br>';
	$status_009 .= '<a href="javascript:markt();">Markt</a><br>';
	$status_009 .= '<a href="index.php?as=auktion">Auktions Markt</a><br>';
//	$status_009 .= '<a href="index.php?as=shop">Shakkys Abzockbar</a><br>';

	$status_009 .= '<a href="logout.php">Logout</a><br>';

} else {

	$agc_009 = "
	 <a href=index.php?as=anmeldung>Anmeldung</a><br>
	 <a href=index.php?as=login>Login</a><br>
	 <a href=index.php?as=aktiv>Account Aktivieren</a><br>
	 <a href=index.php?as=pw>Passwort Vergessen?</a><br>
	";
}
	$active = mysqli_fetch_assoc(db_query('select count(*) as anzahl from user where online_zeit between TIMESTAMPADD(DAY, -7, now()) and now()'));

	$o_datum = preg_split('# #',$online_rekord_1_0['datum']);
	$info_019 = "
	<a href=\"$GLOBALS[faq_url]\">FAQ</a><br>
	<a href=\"$GLOBALS[noobguide_url]\">Anf&auml;ngerguide</a><br>
	<a href=index.php?as=support>AG Unterst&uuml;tzen</a><br>
	<a href=index.php?as=info/agb target=_blank>Regeln lesen</a><br>
	<a href=index.php?as=umfrage> Umfragen</a><br>
	<a href=index.php?as=&#105;&#109;&#112;&#114;&#101;&#115;&#115;&#117;&#109;>&#73;&#109;&#112;&#114;&#101;&#115;&#115;&#117;&#109;</a><hr id=hru>
	Angemeldet: $anmeldungen <br>" .
	"Aktiv: $active[anzahl]<br>
	Online: $online_r<br>
	Online Rekord: $online_rekord_1_0[anzahl]<br>
	Am:  $o_datum[0]<br>
	Um: $o_datum[1] Uhr<br>";

?>
</ul>
