<?php
/*
 *
 * @copyright (c) 2012 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 * @author jester
 */
include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="utf-8" ?>';

$nachrichten = db_query('SELECT * FROM ff11_news order by id DESC LIMIT 10');

?>
<rss version="2.0">
<channel>
    <title><?php echo $GLOBALS['server_url_short']; ?> News</title>
    <link><?php echo $GLOBALS['server_url_long']; ?></link>
    <description></description>


<?php
    while ($row = mysqli_fetch_assoc($nachrichten))
    {
                $komments = mysqli_fetch_assoc(db_query('SELECT count(id) as anzahl FROM ff11_komments WHERE news_id='.$row['id']));
                $komments = $komments['anzahl'];
	$betreff = preg_replace('#&#', 'und', $row['betreff']);
        $text = $betreff;//htmlspecialchars($row['text']);
        $link = $GLOBALS['server_url_long'].'/index.php?as=news&id='.$row['id'].'';
        $link = htmlentities($link);

?>
    <item>
    <title><?php echo $betreff.' - Antworten ('.$komments.')'; ?></title>
    <link><?php echo $link; ?></link>
    <description><?php  echo $text; ?></description>
    <guid>
        <?php echo $row['id']; ?>
    </guid>
    </item>
<?php
    }
?>
</channel>
</rss>
