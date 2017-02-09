<?php
define('VCEXE', 1);

header("Content-type: text/html; charset=utf-8");

if (isset($_GET['player']) && isset($_GET['pass'])) {
	// verify pass & load vars
	$vcseAuth = false;
	require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/config.php');
	if ($vcseAuth) {
		// calc mode
		switch ($dbConfig['mode']) {
		case '*':
			$dbConfig['mode'] = '+';
			break;
		case '+':
			$dbConfig['mode'] = '-';
			break;
		case '-':
			$dbConfig['mode'] = '*';
			break;
		}
		
		// up mode
		db_work("UPDATE `players_vars` SET `value`='".$dbConfig['mode']."' WHERE `name`='mode' AND `player`='".$_GET['player']."'");

		require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/panel.php');
	}
}
?>