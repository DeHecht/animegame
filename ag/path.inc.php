<?php
//define('ROOT_PATH', ROOT_PATH.'ag

// okay and now define the url ;)

if(!defined('ROOT_PATH')) {
	if(isset($_SERVER['SCRIPT_FILENAME'])) {
		$actual_path = $_SERVER['SCRIPT_FILENAME'];
		$url_path = $_SERVER['SCRIPT_NAME' ];
	} else {
		$actual_path = __FILE__;
		$url_path = NULL;
	}

	// okay this is the script that has included us :) -> lets search for the include/config/ directory
	while(strlen($actual_path) > 0) {
		$actual_path = substr($actual_path,0,strrpos($actual_path,'/'));
		if($url_path != null) {
			$url_path = substr($url_path,0,strrpos($url_path,'/'));
		}
		if(@is_dir($actual_path . '/include/config')) {
			define('ROOT_PATH', $actual_path);
			if($url_path != null) {
				define('ROOT_URL', $url_path);
			} else {
				define('ROOT_URL', '/'); // --> currently no support for cronjobs
			}
		}
	}
}
