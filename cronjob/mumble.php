<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once 'db.php';

/**
 * Konfiguration
 */
$serverid = 1;
$mumbledb =  'ag01_murmur';
$ag_db = 'hecht_ag';
$clan_chan_name = 'Clans';
// Ab $min_member_count_create wird ein Raum erstellt
$min_member_count_create = 5;
// Sollte die Anzahl der member auf $max_member_count_delete oder weniger fallen, wird der channel gelöscht!
$max_member_count_delete = 1;


/**
 * Erstmal die ganzen SQL-Befehle definieren!
 */

$res = mysqli_fetch_assoc(db_query('Select max(channel_id) as mx FROM '.$mumbledb.'.channels where server_id = '.$serverid));
$max_channel = $res['mx'];

$res = mysqli_fetch_assoc(db_query('Select max(group_id) as mx FROM '.$mumbledb.'.groups where server_id = '.$serverid));
$max_group = $res['mx'];

$res = mysqli_fetch_assoc(db_query('Select channel_id FROM '.$mumbledb.'.channels where name = \''.$clan_chan_name.'\' and server_id = '.$serverid));
$clan_chan = $res['channel_id'];

$rename_sql = 'UPDATE '.$mumbledb.'.users p inner join '.$ag_db.'.user u on p.user_id = u.id set p.name = u.nickname WHERE p.name != u.nickname and server_id = '.$serverid;
$search_lost_players = 'Select p.user_id FROM '.$mumbledb.'.users as p left join '.$ag_db.'.user as u on p.user_id = u.id where p.user_id > 0 and u.nickname is null and p.server_id = '.$serverid;
$qry = db_query($search_lost_players);

while($row = mysqli_fetch_assoc($qry)){
	$player[] = $row['user_id'];
}

if(count($player) > 0){
	$delete_sql = 'DELETE FROM '.$mumbledb.'.users where user_id IN ('.join(',',$player).') and server_id = '.$serverid;
}
//echo $delete_sql.'<br>';
// Verbinde die Clanchannels mit den Clans

//$clan_channels_sql = 'SELECT distinct(clanname) as name from '.$mumbledb.'.channels c right join '.$ag_db.'.clan cl on c.name = cl.clanname where name is null';
$clan_channels_sql = 'SELECT distinct(clanname) as name, count(u.clan) anzahl, name is not null as existance from '.$mumbledb.'.channels c right join '.$ag_db.'.clan cl on c.name = cl.clanname left join '.$ag_db.'.user u on cl.id = u.clan group by clanname';

$qry = db_query($clan_channels_sql);
$insert_clan_channels = 'INSERT INTO '.$mumbledb.'.channels(server_id, channel_id, parent_id, name, inheritacl) values';
$insert_clan_groups = 'INSERT INTO '.$mumbledb.'.groups(group_id, server_id, name, channel_id, inherit, inheritable) values';
$insert_acl = 'INSERT INTO '.$mumbledb.'.acl(server_id, channel_id, priority, group_name, apply_here, apply_sub, grantpriv, revokepriv) values';

$i=1;
while($row = mysqli_fetch_assoc($qry)){
	$channelid = ($max_channel + $i);
	$groupid = ($max_group + $i);
	if($row['existance'] == 0 && $row['anzahl'] >= $min_member_count_create){
		echo $row['name'].' ist gross genug!<br>';
		if($i==1){
			$insert_clan_channels .= '('.$serverid.', '.$channelid.', '.$clan_chan.', \''.$row['name'].'\',1)';
			$insert_clan_groups .= '('.$groupid.', '.$serverid.', \'member\', '.$channelid.', 1, 1)';
			$insert_acl .= '('.$serverid.','.$channelid.',5,\'member\',1,1,510,0), ('.$serverid.','.$channelid.',6,\'member\',0,1,1,0)';
		} else{
			$insert_clan_channels .= ', ('.$serverid.', '.$channelid.', '.$clan_chan.', \''.$row['name'].'\',1)';
			$insert_clan_groups .= ', ('.$groupid.', '.$serverid.', \'member\', '.$channelid.', 1, 1)';
			$insert_acl .= ', ('.$serverid.','.$channelid.',5,\'member\',1,1,510,0), ('.$serverid.','.$channelid.',6,\'member\',0,1,1,0)';
		}
		$i++;
	} else if($row['existance'] == 1 && $row['anzahl'] <= $max_member_count_delete){
		db_query('DELETE FROM '.$mumbledb.'.channels WHERE parent_id = '.$clan_chan. ' and name = \''.$row['name'].'\' and server_id = '.$serverid);
	}
}

// Delete nonexistant Clan-Channels!
$search_lost_channels = 'select channel_id FROM '.$mumbledb.'.channels left join '.$ag_db.'.clan on name = clanname where clanname is null and parent_id = '.$clan_chan.' and server_id = '.$serverid;
$qry = db_query($search_lost_channels);

while($row = mysqli_fetch_assoc($qry)){
        $channels[] = $row['channel_id'];
}
       
if(count($channels) > 0){
	$remove_clan_channel_sql = 'DELETE FROM '.$mumbledb.'.channels where channel_id IN ('.join(',',$channels).') and server_id = '.$serverid;
}
                 
//echo $remove_clan_channel_sql.'<br>';

$sql_user_select = 'Select p.user_id, ch.name as ch_name, ch2.name as ch2_name, ch.parent_id as ch_parent_id, ch2.parent_id as ch2_parent_id, g.group_id as g_group_id, g2.group_id as g2_group_id from
	( '.$ag_db.'.user u inner join '.$mumbledb.'.users p on
		u.id = p.user_id
	)
	left join '.$ag_db.'.clan cl on
		u.clan = cl.id
	left join '.$mumbledb.'.group_members gm on
		gm.user_id = p.user_id and p.server_id = gm.server_id
	left join '.$mumbledb.'.groups g on
		g.group_id = gm.group_id and g.server_id = p.server_id
	left join '.$mumbledb.'.channels ch on
		ch.channel_id = g.channel_id and p.server_id = ch.server_id
	left join '.$mumbledb.'.channels ch2 on
		ch2.name = cl.clanname and p.server_id = ch2.server_id
	left join '.$mumbledb.'.groups g2 on
		g2.channel_id = ch2.channel_id and g2.server_id = p.server_id
 
 	where not (ch.name is null && ch2.name is null) and (ch.name is null || ch.parent_id = 2) and (ch2.name is null || ch2.parent_id = 2) and (ch.name is null || ch2.name is null or ch.name != ch2.name) and p.server_id = '.$serverid;



/**
 * So nun die Ausfuehrung!
 */


db_query($rename_sql);
if(isset($delete_sql)){
	db_query($delete_sql);
}
if($i!=1){
//	echo $insert_clan_channels.'<br>';
  db_query($insert_clan_channels);
  db_query($insert_clan_groups);
  db_query($insert_acl);
}
// Entferne Channels von Clans die es nicht mehr gibt!
db_query($remove_clan_channel_sql);

// Nun die User :)
$qry = db_query($sql_user_select);

while($row = mysqli_fetch_assoc($qry)){
	// Zu Gruppe hinzufuegen oder aus gruppe entfernen?
	if($row['ch_name'] == NULL){
		// Hinzufuegen
		$sql = 'INSERT INTO '.$mumbledb.'.group_members(group_id, server_id, user_id, addit) values('.$row['g2_group_id'].', '.$serverid.', '.$row['user_id'].',1)';
//		echo $sql.'<br>';
		db_query($sql);
	} else if($row['ch2_name'] == NULL){
		// Entfernen
		$sql = 'DELETE FROM '.$mumbledb.'.group_members where group_id = '.$row['g_group_id'].' and server_id = '.$serverid.' and user_id = '.$row['user_id'];
//		echo $sql.'<br>';
		db_query($sql);
	}
}


?>