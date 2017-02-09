<?php
define('VCEXE', 1);

header("Content-type: text/html; charset=utf-8");

if (isset($_GET['player']) && isset($_GET['pass']) && isset($_GET['panel']) && isset($_GET['item_id_type']) && isset($_GET['item_id'])) {
	// verify pass & load vars
	$vcseAuth = false;
	require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/config.php');
	if ($vcseAuth) {
		if ($_GET['panel']!='panel_item') {
			// select
			switch ($dbConfig[$_GET['panel']]) {
			case 'hide':
				$dbConfig[$_GET['panel']] = 'show';
				break;
			case 'show':
				$dbConfig[$_GET['panel']] = 'hide';
				break;
			}
			
			// up panel
			db_work("UPDATE `players_vars` SET `value`='".$dbConfig[$_GET['panel']]."' WHERE `name`='".$_GET['panel']."' AND `player`='".$_GET['player']."'");
		}

		require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/panel.php');
	}
}
?>