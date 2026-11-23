<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/gfx/gfx_config.php');
include_once(ROOT_PATH.'/include/parse.inc.php');

// GET-Section
// Leer

$chara = db_query("SELECT name, liga_siege, liga_niederlagen, bild, id, liga_punkte FROM chars order by liga_punkte DESC LIMIT 50");

$x=1;
while($row = mysqli_fetch_array($chara)) {

$PIC = "<img border=0 src=".$row['bild']." width=".$pic_g31." height=".$pic_g31.">";


if($x <= 5) {
$CHARAKTER = "<a href=index.php?as=info&char_id=".$row['id']."&charm=1>".$row['name']."</a><br>$PIC";
} else {
$CHARAKTER = "<a href=index.php?as=info&char_id=".$row['id']."&charm=1>".$row['name']."</a>";
}
$WIN = $row['liga_siege'];
$LOSE = $row['liga_niederlagen'];
$PUNKTE = number_format($row['liga_punkte'],0,"",".");

eval("\$IN_LIGA1.=\"".gettemplate("gfx/in_liga")."\";");

$x++;
}


eval("\$soutput=\"".gettemplate("gfx/liga1")."\";");
echo $soutput;

?>