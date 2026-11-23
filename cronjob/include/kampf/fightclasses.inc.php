<?php
/*
 * Created on 15.04.2008
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
 
// Um die ganzen Fightscheisse modularer zu machen (noch nicht fertig)
class Kaempfer{
	private $charid;
	private $hp, $hpmax;
	private $mp, $mpmax;
	private $speed;
	private $verteidigung;
	private $starke;
	private $ausdauer;
	private $glueck;
	private $aufgeben;

	private $isNPC;

	private $attackenhandler;


	/**
	 * Charid = ID des Chars, Buffs = ob Ruestungsbonus mithinzugerechnet werden soll
	 */
	function __construct($char, $alternative = true, $buffs = true){
		if(is_numeric($char)){ // ID, wenn keine Abfrage vorhanden
			$char = mysqli_fetch_assoc(db_query('SELECT * FROM chars WHERE id='.$char));		
		} else if(!is_array($char)){ // Wenn es weder ID noch Array ist
			return; // breche ab, dann kann nichts mit angefangen werden
		}
		// Nun ist char auf jedenfall was wir brauchen.
		$this->speed = $char['speed'];
		$this->verteidigung = $char['verteidigung'];
		$this->starke = $char['starke'];
		$this->ausdauer = $char['ausdauer'];
		$this->glueck = $char['glueck'];
		$this->aufgeben = $char['aufgeben'];
		$this->charid = $char['id'];
		
		if($buffs == true){ // Ruestungen erlaubt???
			$char_buffs = mysqli_fetch_assoc(db_query('SELECT sum(i.hp) as hp, sum(i.mp) as mp, sum(i.starke) as starke, sum(i.verteidigung) as verteidigung, sum(i.speed) as speed FROM ware w LEFT JOIN item i ON(i.id=w.item_id) WHERE w.id IN ('.$char['kampf_item'].')'));
			$this->starke += $char_buffs['starke'];
			$k_speed[0] = $char['speed'] + $char_buffs['speed'];
			$this->verteidigung += $char_buffs['verteidigung'];
			$k_hp1[0] += $char_buffs['hp'];
			$k_hp1[1] += $char_buffs['hp'];
			$k_mp1[0] += $char_buffs['mp'];
			$k_mp1[1] += $char_buffs['mp'];			
		}

		$this->attackenhandler = new Attackenauswahl($char);
	}
}

class Monster extends Kaempfer{
	
	function __construct($monster){
		if(is_numeric($monster)){
			$qry = db_query('SELECT * FROM quest_monster WHERE id = '.$monster);
			$monster = mysqli_fetch_assoc($qry);
		} else if(!is_array($monster)){
			// Fehler
			return;
		}
		// Zuweisung
	}
}


class Attacke{
	function __construct($attacke){
		if(is_numeric($attacke)){ 
			$qry = db_query('SELECT * FROM attacken WHERE id = '.$attacke);
		} else if(!is_array($attacke)){
			// Fehler!!
			return;
		}
		// Zuweisung
	}
}

class Attackenauswahl{
	private $attacke1;
	private $altervatives;
	private $attacke2;
	function __construct($char){
		if(is_numeric($char)){ // ID, wenn keine Abfrage vorhanden
			$char = mysqli_fetch_assoc(db_query('Select attacken, attacken2, auswahl1, auswahl2, auswahl3 from chars where id = '.$char));
		} else if(!is_array($char)){ // Wenn es weder ID noch Array ist
			return; // breche ab, dann kann nichts mit angefangen werden
		}
		$this->attacke1 = preg_split('#,#',$char['attacken']);
		$this->attacke2 = preg_split('#,#',$char['attacken2']);
		$this->altervatives[0] = preg_split('#,#', $char['auswahl1']);
		$this->altervatives[1] = preg_split('#,#', $char['auswahl2']);
		$this->altervatives[2] = preg_split('#,#', $char['auswahl3']);
		
		// Zwischenschritt ANFANG
		// Nun ein Zwischenschritt weil in den Feldern attacken und attacken2
		$qry = db_query('Select at_id, id from lernen where id IN ('.$char['attacken'].','.$char['attacken2'].')');
		while($row = mysqli_fetch_assoc($qry)){
			$transition[$row['id']] = $row['at_id'];
		}
		for($i=0;$i<count($this->attacke1);$i++){
			$this->attacke1[$i] = $transition[$this->attacke1[$i]];
			$this->attacke2[$i] = $transition[$this->attacke2[$i]];
		}
		unset($transition);
		// Zwischenschritt ENDE!!!
		
		// Lade die Attacken aus der DB und schreibe sie direkt in den Array
		$qry = db_query('Select * from attacken where id IN ('.join(',',$this->attacke1).','.join(',',$this->attacke2).')');
		while($row = mysqli_fetch_assoc($qry)){
			$temp_att[$row['id']] = $row;			
		}
		for($i=0;$i<count($this->attacke1);$i++){
			$this->attacke1[$i] = $temp_att[$this->attacke1[$i]];
			$this->attacke2[$i] = $temp_att[$this->attacke2[$i]];
		}
		// Die Kaempfer sind Kampfbereit!!
	}
	function getAttackValuesOfRound($round){
		$returnarray['attacke1'] = $this->attacke1[$round];
		$returnarray['attacke2'] = $this->attacke2[$round];
		$returnarray['auswahl1'] = $this->altervatives[0][$round];
		$returnarray['auswahl2'] = $this->altervatives[1][$round];
		$returnarray['auswahl3'] = $this->altervatives[2][$round];
		return $returnarray;
	}
}
?>
