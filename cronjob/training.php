<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

$test = date("H");
include_once('path.inc.php'); // get the path ;)
include_once (ROOT_PATH.'/include/sqlwrapper.inc.php');
include "db.php";
include_once ('inclu/messagefunctions.inc.php');
include_once (ROOT_PATH.'/include/usergroup.inc.php');
include_once (ROOT_PATH.'/include/defines.inc.php');


if ($test == "02") {
	$i = 0;
}
if ($test == "04") {
	$i = 1;
}
if ($test == "06") {
	$i = 2;
}
if ($test == "08") {
	$i = 3;
}
if ($test == "10") {
	$i = 4;
}
if ($test == "12") {
	//Jeden Tag gibts es was für Wanted
//	$rangliste1 = db_query("SELECT besitzer, name, preis FROM chars order by preis");
//	while($row = mysqli_fetch_array($rangliste1)) {
//		insertUserTickerMessage($row['besitzer'], ' '.$row['preis'].' Geld durch deinen Wanted erhalten');
//		db_query("UPDATE user SET geld= geld + '$row[preis]' WHERE id='$row[besitzer]'");
//		if ($row['preis']>10000){
//			$IP = $row['preis']/1000;
//			db_query("UPDATE user SET pkt= pkt + '$IP' WHERE id='$row[besitzer]'");
//			insertUserTickerMessage($row['besitzer'], ' '.$IP.' IP durch deinen Wanted erhalten, da du eine Grenze überschritten hast');
//		}
//	}
	$i = 5;
}
if ($test == "14") {
	$i = 6;
}
if ($test == "16") {
	$i = 7;
}
if ($test == "18") {
	$i = 8;
}
if ($test == "20") {
	$i = 9;
}
if ($test == "22") {
	$i = 10;
}
if ($test == "00" || $_GET['midnight'] == 1) {
	$i = 11;
}

########################CLAN ITEMS BENUTZEN
if ($i == 11) {
	// Erstmal Arena (auch wenns nix mit Training zu tun hat)
	// select round(einnahmen*0.5/(1+EXP(2-einnahmen/100000))) from arena where einnahmen > 10000

	db_query('Update user_clan SET spende_yesterday = spende_today, spende_today = 0');

	// Kann erstmal bleiben!
	db_query('Update arena set zustand = zustand - 1/4 where mietlasten >= 2 and einnahmen > 0');
	db_query('Update arena set zustand = 0 where zustand < 0'); // No negatives any more
	defineIfNotDefined('TAX_NOOB_MIN_TAX', 0.5, true);
	defineIfNotDefined('TAX_NOOB_MAX_LEVEL', 10, true);
	defineIfNotDefined('TAX_START_TAX', 0.85, true);
	defineIfNotDefined('TAX_REDUCTION', 0.05, true);
	defineIfNotDefined('TAX_FREE_START', 250000, true);
	defineIfNotDefined('TAX_FREE_X', 50000, true);
	defineIfNotDefined('TAX_FREE_Y', 17500, true);

        $NOOB_MIN_TAX = TAX_NOOB_MIN_TAX;
        $NOOB_MAX_LEVEL = TAX_NOOB_MAX_LEVEL;
        $TAX_START=TAX_START_TAX;
        $TAX_REDUCTION=TAX_REDUCTION;
        $FREE_START=TAX_FREE_START;
        $X=TAX_FREE_X;
        $Y=TAX_FREE_Y;
        $noob_rabatt='GREATEST('.$NOOB_MIN_TAX.', least(1, level / '.$NOOB_MAX_LEVEL.'))';
        $steuersatz='('.$TAX_START.'-hausmeister*'.$TAX_REDUCTION.')';
	$freibetrag='(('.$FREE_START.'+(hausmeister*'.$X.')+(hausmeister*hausmeister*'.$Y.')) * (1/'.$noob_rabatt.'))';
        $steuerbetrag='ROUND((einnahmen - '.$freibetrag.')*'.$noob_rabatt.'*'.$steuersatz.')';
        $sql = 'Update arena set mietlasten = mietlasten + 1, steuerlasten = steuerlasten + '.$steuerbetrag.' where '.$steuerbetrag.' > 0';
//	echo $sql.'<br>';
	db_query($sql);

	db_query('Update arena set einnahmen = 0');

	// Inaktive Clans löschen (Clanräume werden automatisch gelöscht durch Fremdschlüssel)
	$sql = 'delete from clan where id NOT IN(SELECT distinct(clan) from user where clan is not null)';
	//echo $sql . '<br>';
	db_query($sql);
	// Räume die Ticker auf (Massivster Datenmüll!!)
	db_query('DELETE FROM clan_ticker WHERE date < TIMESTAMPADD(day, -7, now())');
	db_query('DELETE FROM user_ticker WHERE date < TIMESTAMPADD(day, -7, now())');

	// Benutze Clan items!!
	// Folgende SQL-Anweisung ermittelt mit einer kleinen Hilfsfunktion die Summe der Miete und das Geld des Clans
	$mindestchar = 12; // War vorher 40;
	$sql = 'SELECT cw.id as clan_train, if(count(ch.id)<'.$mindestchar.','.$mindestchar.',count(ch.id)) * miete as mietkosten, c.id as clan, c.geld as geld from clan_ware cw inner join clan_item ci on cw.item_id = ci.id inner join clan c on cw.clan = c.id inner join user u on u.clan = c.id left join chars ch on ch.clan_train = cw.id and ch.besitzer = u.id group by c.id, cw.id order by c.id';
	// SQL-Anweisung, wenn Ungenutzte items nicht beruecksichtigt werden sollen -> 'SELECT ch.clan_train, if(count(ch.id)<40,40,count(ch.id)) * miete as mietkosten, c.id as clan, c.geld as geld from user u inner join clan c on u.clan = c.id inner join chars ch on u.id = ch.besitzer inner join clan_ware cw on cw.clan = c.id inner join clan_item ci on cw.item_id = ci.id and ch.clan_train = cw.id group by c.id, cw.id order by c.id'
//	echo '<br>'.$sql.'<br>';
	$clan_kosten = db_query($sql);

	$clan_raum_array = array();
	while ($row = mysqli_fetch_assoc($clan_kosten)){
		if($clan_raum_array[$row['clan']] === null){
			$clan_raum_array[$row['clan']] = $row;
		} else{
			$clan_raum_array[$row['clan']]['mietkosten'] += $row['mietkosten'];
		}
	}

	$clans = array ();
//	while ($row = mysqli_fetch_assoc($clan_kosten)) {
	foreach($clan_raum_array as $row){
//		print_r($row);
		// Ermittle das Vermoegen und die Useranzahl des Clans!
		if ($row['mietkosten'] > $row['geld']) {
			// Clan is pleite!
			//			echo 'Clan '.$row['clan'].' is Pleite!';
			insertClanTickerMessage($row['clan'], 'Die Miete f&uuml;r die R&auml;ume ('.displayMoney($row['mietkosten']).') konnte nicht bezahlt werden!');
		} else {
			// Clan kann miete bezahlen, also in Trainingsliste aufnehmen
			$clans[] = $row['clan'];
			// Clan die Miete abziehen
			db_query('UPDATE clan SET geld = geld - ' . $row['mietkosten'] . ' WHERE id = ' . $row['clan']);
			insertClanTickerMessage($row['clan'], 'Die Miete f&uuml;r die R&auml;ume ('.displayMoney($row['mietkosten']).') wurde bezahlt!');
		}
	}
	// Ermittle die Kosten fuer die Nutzer (es werden nur Chars beruecksichtigt, die genug TP haben)
	$sql = 'select besitzer, user.geld, sum(1/(1+exp(3-level/12)) * nutzung) as nutzkosten from user inner join chars on chars.besitzer = user.id inner join clan_ware on chars.clan_train = clan_ware.id inner join clan_item on clan_ware.item_id = clan_item.id where user.clan = clan_ware.clan and user.clan IN (' . join(',', $clans) . ') and clan_item.tp <= chars.training_points group by besitzer order by besitzer';
	$user_kosten = db_query($sql);
	//	echo '<br>'.$sql.'<br>';
	$users = array ();
	if (count($clans) != 0) {
		while ($row = mysqli_fetch_assoc($user_kosten)) {
			if ($row['nutzkosten'] > $row['geld']) {
				// User is pleite!
				insertUserTickerMessage($row['besitzer'], 'Die Nutzungsgeb&uuml;hren ('.displayMoney($row['nutzkosten']).') f&uuml;r die R&auml;ume konnte nicht bezahlt werden!');
			} else {
				// User kann Nutzkosten bezahlen, also in Trainingsliste aufnehmen
				$users[] = $row['besitzer'];
				// Clan die Miete abziehen
				db_query('UPDATE user SET geld = geld - ' . $row['nutzkosten'] . ' WHERE id = ' . $row['besitzer']);
				insertUserTickerMessage($row['besitzer'], 'Die Nutzungsgeb&uuml;hren ('.displayMoney($row['nutzkosten']).') f&uuml;r die R&auml;ume wurde bezahlt!');
			}
		}

		$sql = 'Update chars inner join user on chars.besitzer = user.id inner join clan_ware on chars.clan_train = clan_ware.id inner join clan_item on clan_ware.item_id = clan_item.id SET chars.starke = clan_item.staerke + chars.starke, chars.speed = chars.speed + clan_item.geschwindigkeit, chars.verteidigung = chars.verteidigung + clan_item.verteidigung, chars.glueck = chars.glueck + clan_item.glueck, chars.ausdauer = chars.ausdauer + clan_item.ausdauer, chars.training_points = chars.training_points - clan_item.tp WHERE user.clan = clan_ware.clan and chars.besitzer IN(' . join(',', $users) . ') and chars.training_points >= clan_item.tp';
		$check = db_query($sql);

	}
	//	echo '<br>'.$sql.'<br>';
}
#########################EXIT

$char_info = db_query("SELECT training_points, id, training, starke, verteidigung, speed, ausdauer, glueck, mp, hp, level FROM chars");
$x = 0;
while ($row = mysqli_fetch_array($char_info)) {
	$hp = explode(",", $row['hp']);
	$mp = explode(",", $row['mp']);
	$trains = explode(",", $row['training']);
	if (!$trains[$i] || $trains[$i] == 'ausruhen') {
		db_query("UPDATE chars SET hp='$hp[1],$hp[1]', mp='$mp[1],$mp[1]' WHERE id='$row[id]' LIMIT 1");
	} else {

		if ($row[training_points] > 1) {
			if ($trains[$i] == 'hp' OR $trains[$i] == 'mp') {
				if ($trains[$i] == hp) {
					$zu[$x] = $hp[1] + 20;
					$zu[$x] = "$hp[0],$zu[$x]";
				} else if ($trains[$i] == 'mp') {
					$zu[$x] = $mp[1] + 10;
					$zu[$x] = "$mp[0],$zu[$x]";
				}
				//				echo "UPDATE chars SET $trains[$i]='$zu[$x]', training_points=training_points-2 WHERE id='$row[id]' LIMIT 1<br>";
				db_query("UPDATE chars SET $trains[$i]='$zu[$x]', training_points=training_points-4 WHERE id='$row[id]' LIMIT 1");
			} else {
				$event = mysqli_fetch_assoc(db_query('SELECT CURRENT_TIMESTAMP between TIMESTAMP(\'2009-04-25 00:00:00\') and TIMESTAMP(\'2009-05-02 23:59:59\') as betw'));
				$step = 2 + ($event['betw']*3);
				$news2 = $trains[$i];
				$news[$x] = $row[$news2] + $step;
				$step*=2;
				$sql = "UPDATE chars SET $news2='$news[$x]', training_points=training_points-$step WHERE id='$row[id]' LIMIT 1";
				// echo $sql.'<br>'."\n";
				db_query($sql);
			}

		}
	}
	$x++;
}

db_query('DELETE FROM ag_chat where TIMESTAMPDIFF(Hour,zeit,now()) > 48');
db_query('DELETE FROM user_activation where TIMESTAMPDIFF(day, registerdate, now()) > 7');
// Loesche inaktive anons :)
db_query('DELETE FROM anon_chatter WHERE TIMESTAMPDIFF(day, online, now()) > 35'); // 5 Wochen

// Loesche inaktive user!
$sql = 'Select * from user where TIMESTAMPDIFF(week, online_zeit, now()) > 10 and id > 2 and id not in (SELECT user_id FROM user_gruppe_zuordnung WHERE gruppen_id = '.AKTIV_LOESCHSCHUTZ.' AND user_id = id)';


//echo $sql;
$u_qry = db_query($sql);
if (mysqli_num_rows($u_qry) != 0) {
	$user = array ();
	while ($row = mysqli_fetch_assoc($u_qry)) {
		$user[] = $row['id'];
	}
	$users = join(',', $user);
//	echo 'Delete inactive Users ' . $users . '<br>';
	db_query('Delete from user where id in (' . $users . ')');
	db_query('Delete from arena where besitzer in (' . $users . ')');
	db_query('Delete from ware where user in (' . $users . ')');
	db_query('Delete from npc_ware where user in (' . $users . ')');
	db_query('Delete from sp_ware where user in (' . $users . ')');
	db_query('Delete from wochen_ware where user in (' . $users . ')');
	db_query('Delete from user_ticker where userid in (' . $users . ')');
	db_query('Delete from user_rename where userid in (' . $users . ')');
	db_query('Delete from user_clan where userid in (' . $users . ')');
	db_query('Delete from nachricht where besitzer in (' . $users . ')');
	db_query('Delete from ignolist where user in (' . $users . ') or besitzer in (' . $users . ')');

	$char = array ();
	$c_qry = db_query('Select id from chars where besitzer IN (' . $users . ')');
	while ($row = mysqli_fetch_assoc($c_qry)) {
		$char[] = $char['id'];
	}
	if ( count($char) > 0 ) {
		$chars = join(',', $char);
		db_query('Delete from chars where id IN (' . $chars . ')');
		db_query('Delete from lernen where besitzer IN (' . $chars . ')');
		db_query('Delete from quests where charid IN (' . $chars . ')');
		db_query('Delete from quests_fights where charid IN (' . $chars . ')');
		db_query('Delete from quests_rounds where charid IN (' . $chars . ')');
		db_query('Delete from kampf where dbsatz IN (' . $chars . ')');
		db_query('Delete from kampf_list where charakter IN (' . $chars . ')');
		db_query('Delete from char_abholcodes where charid IN (' . $chars . ')');
	}
}


