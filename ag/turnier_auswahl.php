<?php 
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once('path.inc.php'); // get the path ;)
include_once(ROOT_PATH.'/include/tournament.inc.php');

?>


<SCRIPT language="JavaScript">
	function kampf(type_id){
		window.open("./turnier2.php?charm=1&type_id="+type_id,"","status=no,hotkeys=no,Height=600,Width=929,scrollbars=yes");
	}
</SCRIPT>

<table cellpadding="0" cellspacing="0" width="100%">
	<!-- MSTableType="layout" -->
	<tr>
		<th valign="top" colspan="3" align="center">Turnier Auswahl</th>
	</tr>
<?php
	$day_translation = array('Sonntags', 'Montags', 'Dienstags', 'Mittwochs', 'Donnerstags', 'Freitags', 'Samstags', 'Sonntags');
	$tournament_types = getTournamentTypes();
	foreach ($tournament_types as $tournament_type) {
		$info = 'Das Turnier ist';
		if ( $tournament_type['day_of_week'] !== NULL ) {
			$info .= ' einmal die Woche, '.$day_translation[$tournament_type['day_of_week']];
		} else if ( $tournament_type['day_of_month'] !== NULL ) {
			$info .= ' immer am '.$tournament_type['day_of_month'].'. jeden Monats';
		} else {
			continue;
		}
		
		$info .= ', um '.$tournament_type['hour_of_day'].' Uhr ('.$tournament_type['competitors'].' Spieler).';
		if ($tournament_type['excl_fusions']) {
			$info .= '<p>Fusionen sind von der Teilnahme ausgeschlossen!</p>';
		} else if ($tournament_type['excl_fusion_particip_count'] > 0) {
			$info .= '<p>Fusionen k&ouml;nnen maximal nur '.$tournament_type['excl_fusion_particip_count'].' Mal teilnehmen!</p>';
		}
		if ($tournament_type['max_level'] !== NULL) {
			$info .= '<p>Das Level ist auf Level '.$tournament_type['max_level'].' begrenzt!</p>';
		}

		if ($tournament_type['excl_winners']) {
			$info .= '<p>Gewinner sind von weiteren Teilnahmen ausgeschlossen!</p>';
		}
		if ($tournament_type['excl_special_chars'] > 0) {
			$info .= '<p>Special Chars sind von der Teilnahme ausgeschlossen!</p>';
		}
		if ($tournament_type['without_equip'] ) {
			$info .= '<p>Ausr&uuml;stungsgegenst&auml;nde werden ignoriert!</p>';
		}
		if ($tournament_type['fruit_chance'] > 0) {
			$info .= '<p>Der Gewinner hat eine '.$tournament_type['fruit_chance'].'% Chance auf eine '.ucfirst($tournament_type['fruit_type']).'frucht.</p>';
		}

		if ($tournament_type['gain'] == TOURNAMENT_GAIN_ANMELDUNG) {
			$info .= '<p>Teilnahme &uuml;ber Anmeldung. Offene Pl&auml;tze werden zuf&auml;llig ausgelost.</p>';
		} else if($tournament_type['gain'] == TOURNAMENT_GAIN_PL) {
			$info .= '<p>Teilnahme &uuml;ber Powerlevel.</p>';
		} else if($tournament_type['gain'] == TOURNAMENT_GAIN_LEVEL) {
			$info .= '<p>Teilnahme &uuml;ber Level.</p>';
		}

		if ($tournament_type['randomize'] > 0) {
			$info .= '<p>Jede Runde werden die Begegnungen neu ausgew&uuml;rfelt!</p>';
		}

?>
	<tr>
		<td colspan=2"><hr /></td>
	</tr>
	<tr>
		<td valign="top" align="center">
			<a href='javascript:kampf("<?php echo $tournament_type['id']; ?>");'>
				<img border="0" src="<?php echo $tournament_type['icon']; ?>" title="<?php echo join(' ', explode('<br>', $info)); ?>" width="150" height="150" />
			</a>
		</td>
		<td align="center"><?php echo $info; ?></td>
	</tr>

<?php
	}
?>
</table>

