<?php
/*
 * Created on 25.03.2009
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

include_once(ROOT_PATH.'/include/config.inc.php');
include_once(ROOT_PATH.'/include/cheater.inc.php');
include_once(ROOT_PATH.'/include/char.inc.php');
include_once(ROOT_PATH.'/include/random.inc.php');

function hasToAuthenticate($user){
	return $user['code'] <= 0;
}

function hasNotDoneAction($user, $count = 1){
	db_query('Update user set code = code + '.$count.' WHERE id = '.$user['id']);
}

function hasDoneAction($user, $count = 1){
	db_query('Update user set code = code - '.$count.' WHERE id = '.$user['id']);
}

function resetUserCounter($user){
	$char_count = count(getCharsOfUser($user['id']));
	// pro Char werden dem User 6-8 Aktionen zugestanden
	db_query('Update user set code = '.($char_count*mt_random_wrapper(6,8)).' where id = '.$user['id']);
}

function checkImageCode($id, $compr_phrase, $user){
	$sql = 'SELECT *, TIMESTAMPDIFF(MINUTE, created, now()) as delay from bot_images WHERE id = \'' . $id . '\'';
//	echo $sql.'<br>';
	$row = mysqli_fetch_assoc(db_query($sql));
	// ID not valid
	if($row){
		// TIMEOUT!
//		echo $row['compressed_phrase'].' '.$compr_phrase.'<br>';
		if($row['delay'] > 5 || strtolower($row['compressed_phrase']) != strtolower($compr_phrase)){
			$sql = 'Insert into bot_image_failures(userid, inserted, valid, used) values('.$user['id'].', \''.$compr_phrase.'\', \''.$row['compressed_phrase'].'\', CURRENT_TIMESTAMP)';
//			echo $sql.'<br>';
			db_query($sql);
			$success = false;
		} else{
			$success = true;
		}
		db_query('DELETE from bot_images WHERE id = \'' . $id . '\'');
	} else{
		$success = false;
	}
	return $success;
}

// Returns the ID of the image 
function createImage($id, $user) {
	$variants = array ('a','b','c','d','e','f','g','h','i','j','k','l','m','n','p','q','r','s','t','u','v','w','x','y','z',	'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	// Variable Länge
	for ($i = 0, $n = mt_random_wrapper(4, 6); $i < $n; $i++) {
		$char = $variants[mt_random_wrapper(0, count($variants)-1)];
		$string .= $char . ' ';
		$comp_str .= $char;
	}
	for (; $i < 6; $i++) {
		$string .= '  ';
	}
	db_query('DELETE from bot_images WHERE id = \'' . $id . '\'');
	db_query('INSERT INTO bot_images(id, phrase, compressed_phrase, created, userid) values(\'' . $id . '\', \'' . $string . '\', \'' . $comp_str . '\', now(), '.$user['id'].')');

	// Aufräumen (Code wurde ignoriert)
	detectNonCodeEnterers();
}

function generateImageID(){
	return $id = md5((mt_rand() * time()));
}

function displayImage($id, $user) {
	if ($id !== NULL) {
		createImage($id,$user);
		$sql = 'SELECT * FROM bot_images WHERE id = \'' . $id . '\'';
//		echo $sql.'<br>';
		$row = mysqli_fetch_assoc(db_query($sql));
		if($row){
			$string = $row['phrase'];
			$variance = true;
		} else{
			$variance = false;
			$string = 'ERROR PLEASE RELOAD!';
		}
	} else{
		$string = 'ERROR PLEASE RELOAD!';
		$variance = false;
	}

	$font_size = 5;
	$width = imagefontwidth($font_size) * strlen($string);
	$height = imagefontheight($font_size) * 2;
	$img = imagecreate($width, $height);
	$bg = imagecolorallocate($img, 225, 225, 225);
	$black = imagecolorallocate($img, 0, 0, 0);
	$len = strlen($string);
	for ($i = 0; $i < $len; $i++) {
		$xpos = $i * imagefontwidth($font_size);
		if($variance){
			$ypos = rand(0, imagefontheight($font_size));
		} else{
			$ypos = imagefontheight($font_size)/2;
		}
		imagechar($img, $font_size, $xpos, $ypos, $string, $black);
		$string = substr($string, 1);
	}
	header("Content-Type: image/png");
	imagepng($img);
	imagedestroy($img);
}
?>
