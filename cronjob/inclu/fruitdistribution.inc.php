<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH. "/include/random.inc.php");

function distributeFruit($user_id) {
	$sql = 'SELECT id FROM wochen_markt WHERE art=\'natur\'';
	$qry = db_query($sql);
	$amount = mysqli_num_rows($qry);
	while($fruit = mysqli_fetch_assoc($qry)) {
		$fruits[] = $fruit;
	}

	$choice = mt_random_wrapper(0,$amount-1);
	$sql = 'INSERT wochen_ware SET item='.$fruits[$choice]['id'].', user='.$user_id;
	echo $sql . '<br>';
	db_query($sql);
}

function distributeParamecia($user_id) {
        $sql = 'SELECT id FROM wochen_markt WHERE kategorie=\'Paramecia\'';
        $qry = db_query($sql);
        $amount = mysqli_num_rows($qry);
        while($fruit = mysqli_fetch_assoc($qry)) {
                $fruits[] = $fruit;
        }

        $choice = mt_random_wrapper(0,$amount-1);
        $sql = 'INSERT wochen_ware SET item='.$fruits[$choice]['id'].', user='.$user_id;
        echo $sql . '<br>';
        db_query($sql);
}


?>
