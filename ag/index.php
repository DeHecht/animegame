<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
ob_start();
ignore_user_abort(true);
// einmaliges Hinzufuegen der wichtigen Datei config.php
include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/parse.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');
include_once(ROOT_PATH.'/include/clan_fights.inc.php');
include_once(ROOT_PATH.'/include/config/ads.inc.php');
include_once(ROOT_PATH.'/include/schnell_start.inc.php');
include_once(ROOT_PATH.'/include/messagefunctions.inc.php');


// Fuer den Transit von register_globals auf normal
$name = $user_ida['nickname'];
$passwort = $user_ida['passwort'];
$nnn_nn = mysqli_num_rows(db_query("SELECT id FROM nachricht WHERE besitzer='$user_ida[id]' AND ag='neu'"));
$as = $_REQUEST['as'];
$agb = $_REQUEST['agb'];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $GLOBALS['server_url_short']; ?> | <?php echo $GLOBALS['server_game_title']; ?></title>
<META NAME="description" CONTENT="Ein Browsergame im Anime-Style">
<META NAME="keywords" CONTENT="anime, animegame, browsergame, browser game, onlinegame, online game,  game, games, dragonball, onepiece, zorro, ruffy, vegeta, songoku, goku, dragonball, dragonballz, dragonballgt">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="design/style.css">
<link rel="alternate" type="application/rss+xml" title="RSS" href="rss.php" />
<script type="text/javascript" src="include/java/ag.js"></script>
<script type="text/javascript" src="include/java/overlib.js"></script>
<script type="text/javascript">
if (top != self)
  top.location = self.location;
</script>
<?php echo getFlattrHeadCode(); ?>
</head>

<?php
// Auskommentieren, wenn Framing doch nicht moeglich sein soll!
//<script type="text/javascript">
//if (top != self) {
//  if (window.opera) {
//    window.onload = opera_befreien;
//  } else {
//    top.location = self.location;
//  }
//}
//</script>
?>
</head>
<body>
<a name="befreien" href="index.php" target="_top"></a>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td width="165">
							</td>
						<td>
					<map name="FPMap0">
						<area href="index.php" shape="poly" coords="207, 120, 282, 120, 282, 146, 207, 146" alt="">
						<area href="<?php echo $GLOBALS['forum_url']; ?>" target="_blank" shape="poly" coords="285, 120, 362, 120, 362, 146, 285, 146" alt="">
						<area href="?as=irc" shape="poly" coords="364, 120, 441, 120, 441, 146, 364, 146" alt="">
						<area href='javascript:zeigeKampflisteAn();' shape="poly" coords="443, 120, 520, 120, 520, 146, 443, 146" alt="">
					</map><center>
						<div style="position:relative; top:0px; left:0px;">
							<div style="position:relative; top:0px; left:0px; z-index:1">
								<img border="0" src="design/bilder/logos/logo.jpg" usemap="#FPMap0">
							</div>
							<div style="position:absolute; top:90px; left:225px; z-index:2">
								<a href="http://wiki.animegame.eu" target="_blank"><img border="0" src="design/bilder/buttons/test.jpg" ></a>
							</div>
							<div style="position:absolute; top:90px; left:303px; z-index:2">
								<a href="https://www.animegame.eu/game/blog" target="_blank"><img border="0" src="design/bilder/buttons/blog.jpg" ></a>
							</div>
							<div style="position:absolute; top:90px; left:381px; z-index:2">
								<a href="?as=tutorial"><img border="0" src="design/bilder/buttons/tutorial.png"></a>
							</div>
						</div>

						</div>
					</td>
						<td>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td height="714" width="165" valign="top">
				<table cellpadding="0" cellspacing="0" width="165">
					<tr>
						<td height="453" valign="top" width="7" rowspan="3">&nbsp;</td>
						<td height="173" valign="top" width="146">
						<table cellpadding="0" cellspacing="0" width="146">
							<tr>
								<td height="90" width="6">&nbsp;</td>
								<td height="90" width="134" valign="top">
	<?php @include "inclu/user-controll.php";

	if($name AND $passwort) { ?>
								<table cellpadding="0" cellspacing="0" width="134">
									<tr>
										<td height="16" align="center" class="ueberschrift">Charakter<hr id="hru"></td>
									</tr>

									<tr>
										<td height="28" valign="top"><?php echo $charakter_009; ?> </td>
									</tr>
									<tr>
										<td height="1"></td>
									</tr>

								</table>
								<table cellpadding="0" cellspacing="0" width="134">
									<tr>
										<td height="16" align="center" class="ueberschrift">Allgemein<hr id="hru"></td>
									</tr>
									<tr>
										<td height="28" valign="top"><?php echo $status_009; ?> </td>
									</tr>
									<tr>
										<td height="1"></td>
									</tr>
								</table>
								<table cellpadding="0" cellspacing="0" width="134">
									<tr>
										<td height="16" align="center" class="ueberschrift">Turnier<hr id="hru"></td>
									</tr>
									<tr>
										<td height="28" valign="top"><?php echo $turnier_009; ?> </td>
									</tr>
									<tr>
										<td height="1"></td>
									</tr>
								</table>
								<table cellpadding="0" cellspacing="0" width="134">
									<tr>
										<td height="16" align="center" class="ueberschrift">Clan<hr id="hru"></td>
									</tr>
									<tr>
										<td height="28" valign="top"><?php echo $clan_009; ?> </td>
									</tr>
									<tr>
										<td height="1"></td>
									</tr>
								</table>
								<table cellpadding="0" cellspacing="0" width="134">
									<tr>
										<td height="16" align="center" class="ueberschrift">Info<hr id="hru"></td>
									</tr>
									<tr>
										<td height="28" valign="top"><?php echo $info_009; ?> </td>
									</tr>
									<tr>
										<td height="1"></td>
									</tr>
								</table>
		<?php } else { ?>
								<table cellpadding="0" cellspacing="0" width="134">
									<tr>
										<td height="16" align="right">Nicht Eingeloggt</td>
									</tr>
									<tr>
										<td height="28" valign="top"><?php echo $agc_009; ?> </td>
									</tr>
								</table>
<?php } ?>
								</td>
								<td height="90" width="6">&nbsp;</td>
							</tr>
							<tr>
								<td height="90" width="6">&nbsp;</td>
								<td height="16" width="146" colspan="3">
									<table cellpadding="0" cellspacing="0" width="134">
										<tr>
											<td height="16" align="center" class="ueberschrift">Info<hr id="hru"></td>
										</tr>
										<tr>
											<td height="28" valign="top"><?php echo $info_019; ?></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<table cellpadding="0" cellspacing="0" width="146">
							<tr>
								<td height="50" width="6">&nbsp;</td>
								<td height="22" width="146" colspan="3" class="ueberschrift">
								Affiliates<hr id="hru">
								</td>
							</tr>
							<tr>
								<td width="6">&nbsp;</td>
								<td>
									<table cellspacing="0px" cellpadding="0px" style="position:relative; top:0px; left:6px;">
										<?php include(ROOT_PATH.'/inclu/toplists.inc.php'); ?>
										<tr>
											<td height="1"></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
					</tr>
				</table>
				</td>
				<td valign="top">
				<table width="700" id="content">

					<tr>
						<td height="300" width="5"> </td>
						<td height="300" valign="top">
						<center><table cellpadding="0" cellspacing="0" width=680>
							<tr>
									<?php
										$ids = getOpenChallengeIDs($user_ida['clan']);
										$id_count = count($ids);
										if($id_count > 0) {
											$n_ids = array();
											foreach ($ids as $clan_challenge_id) {
												$alreadyParticipating = false;
												$char_ids = getParticipatingCharIDs($clan_challenge_id, $user_ida['clan']);
												// okay,
												foreach ($char_ids as $slot => $char_id) {
													$char = getChar($char_id);
													if($char['besitzer'] == $user_ida['id']) {
														$alreadyParticipating = true;
														break;
													}
												}
												if(!$alreadyParticipating) {
													$n_ids[] = $clan_challenge_id;
												}
											}
											$id_count = count($n_ids);
										}

										if($nnn_nn!=0) {
											echo '<td colspan="3" align="center" id="pn">';
											echo '**Du hast '.$nnn_nn.' ungelesene Nachrichten. <a href="index.php?as=nachricht" id="pn">Klicke hier um zu deinem Postfach zu gelangen.</a>**</td>';
										}

										if($nnn_nn != 0 && $ids != 0) {
											echo '</tr><tr>';
										}

										if($id_count != 0) {
											echo '<td colspan="3" align="center" id="pn">';
											echo '**Es gibt derzeit '.$id_count.' offene Clan-Kämpfe. <a href="index.php?as=clan/kampf_info" id="pn">Klicke hier um zum Clan-Kampf Menü zu gelangen.</a>**</td>';
										}


									?>
							</tr>
							<tr>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td height="150" valign="top" align="center">
								<table cellpadding="0" cellspacing="0" width="90%">
									<tr>
										<td height="150" width="43">
										</td>
										<td align="center">
											<?php
											if($user_ida) {
												@include "inclu/char.php";
												echo "<hr id='hrc'>";
											}
											else if($as == ""){
											?>
											<table cellpadding="0" cellspacing="0" width="100%" height="100">
												<tr>
													<th class="welcome">
														Willkommen beim AnimeGame!
													</th>
												</tr>
												<tr>
													<td class="content">
														Das Animegame ist ein Browsergame. Um es zu spielen ist also keine Installation von Software notwendig.<br>
														Schl&uuml;pfe in die Rolle deiner One Piece oder Dragonball Helden und nehme an Turnieren, Liga und Kopfgeld-kämpfen teil. Du kannst aber auch Schätze und die Teufelsfrüchte suchen oder dich mit deinen Freunden in Kämpfen messen.<br>
														<a href="index.php?as=anmeldung" class="link">Melde dich noch heute an</a> und zeige, dass ein Held in dir steckt!
													</td>
												</tr>
											</table>
											<?php
											}
											?>
										</td>
										<td height="150" width="43">
										</td>
									</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td height="22" colspan="3"> </td>
							</tr>

					</table>
						<table cellpadding="3" cellspacing="3">

							<tr>
								<td height="20" valign="top">

<?php
		if(isset($as))
		{
			if(file_exists($as.'.php'))
			{

if($as != "irc" AND $as != "login" AND $as != "anmeldung" AND $as != "info/logs" AND $as != "info/agb" AND $as != "pw" AND $as != "aktiv" AND $as != 'impressum' AND $as != 'support' AND $POLOE == 1) {
	echo "Passwort falsch oder Account nicht aktiviert!";
} else {

	if($user_ida['bann'] == "ja") {
		echo "<p align=Center>BANN melden sie sich beim Administrator";
		exit;
	}

	if($user_ida['agb'] == "nein" && $as != "info/agb") {
		if($agb == 'ok'){
			db_query("UPDATE user SET agb='ja' WHERE id='$user_ida[id]' LIMIT 1");
		}
		echo '<form action=index.php method="get"><input type="hidden" name="as" value="'.$as.'"><input name="agb" value="ok" type="hidden">Die Regeln haben sich ver&auml;ndert. Bist du mit den neuen Regeln einverstanden? <a href="index.php?as=info/agb" target="_blank">Hier Klicken um die neuen Regeln zu lesen</a><br><input type="submit" value="Einverstanden"></form>';
		exit;
	}
	include(ROOT_PATH.'/'.$as.'.php');
}
			}
			else
			{
				echo '<b>Error 404:</b> Datei ist nicht vorhanden';
			}
		}
		else
		{
		include(ROOT_PATH.'/news.php');
		}

		?>
</td>
							</tr>

						</table>
						</td>
						<td height="300" width="5"> </td>
					</tr>
					<tr>
						<td height="26" colspan="3">
						</td>
					</tr>
				</table>
				</td>
				<td valign="top" width="10"> </td><td valign="top" align="left">

				<table cellpadding="0" cellspacing="0" width="146">
					<tr>
						<td height="22" width="146" colspan="3" class="ueberschrift">Support<hr id="hru"></td>
					</tr>
					<tr>
						<td height="29" width="6"> </td>
						<td height="29" width="134" valign="top">
							<table cellpadding="0" cellspacing="0" width="134">
								<tr>
									<td height="28" valign="top"><?php echo getFlattrBodyCode(); ?></td>
								</tr>
								<tr>
									<td height="1"></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>

				<table cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="2" height="22" width="146" class="ueberschrift" align="left">
							Chat<hr id="hru" align="left"></td>
					</tr>
					<tr>
						<td height="29" width="6">&nbsp;</td>
						<td height="29" valign="top">
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td height="28" valign="top"><?php 	include 'info/agchat.php'; ?></td>
								</tr>
								<tr>
									<td height="1"></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>

				<table cellpadding="0" cellspacing="0" width="146">
					<tr>
						<td height="22" width="146" colspan="3" class="ueberschrift">
							Partner<hr id="hru"></td>
					</tr>
					<tr>
						<td height="29" width="6"> </td>
						<td height="29" width="134" valign="top">
							<table cellpadding="0" cellspacing="0" width="134">
								<tr>
									<td height="28" valign="top"><?php include "inclu/partner.inc.php"; ?></td>
								</tr>
								<tr>
									<td height="1"></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>


<?php
if($user_ida){
?>
				<br>
				<table cellpadding="0" cellspacing="0" width="146">
					<tr>
						<td height="22" width="146" colspan="3" class="ueberschrift">
							Werbung<hr id="hru"></td>
					</tr>
					<tr>
						<td height="29" width="6"> </td>
						<td height="29" width="134" valign="top">
						<?php echo getVerticalAdvertisement(); ?>
						</td>
					</tr>
				</table>
<?php
}
?>


				<table cellpadding="0" cellspacing="0" width="146">
					<tr>
						<td height="22" width="146" colspan="3" class="ueberschrift">
							Awards<hr id="hru">
						</td>
					</tr>
					<tr>
                                                <td height="29" width="6"> </td>
						<td height="29" width="134" valign="top">
	                                                <table cellpadding="0" cellspacing="0" width="134">
        	                                        	<tr>
                	                                        	<td height="28" valign="top"><?php include "inclu/awards.inc.php"; ?></td>
                        	                                </tr>
                                	                        <tr>
                                        	                	<td height="1"></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>

</td>
				<td rowspan="2"> </td>
			</tr>
		</table>

		</td>
		<td valign="top" width="5"> </td>
	</tr>
</table>
</td>
	</tr>
</table>
<?php
	if(isUserInGroup($usergroups, WERBUNG_AN)) {
		echo getLayerAdvertisement();
	}
	/*if($user_ida['ads'] == 1){
		echo getLayerAdvertisement();
	}*/
?>
</body>
</html>

