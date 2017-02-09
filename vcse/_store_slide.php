<?php
define('VCEXE', 1);

header("Content-type: text/html; charset=utf-8");

if (isset($_GET['player']) && isset($_GET['pass']) && isset($_GET['content_id']) && isset($_GET['direction']) && is_numeric($_GET['direction'])) {
	// verify pass & load vars
	$vcseAuth = false;
	require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/config.php');
	if ($vcseAuth) {
		if ($_GET['content_id'] == 'panel_store_group_content' || $_GET['content_id'] == 'panel_store_group_content_2') {
			$dbResStoreSlight = db_work("SELECT `value` FROM `players_vars` WHERE `name`='".$_GET['content_id']."' AND `player`='".$_GET['player']."'");
			if ($dbResStoreSlight) {
				$dbResStoreSlight['value'][0] += $_GET['direction'];
				if ($dbResStoreSlight['value'][0] < 0) $dbResStoreSlight['value'][0] = 0;
				db_work("UPDATE `players_vars` SET `value`=".$dbResStoreSlight['value'][0]." WHERE `name`='".$_GET['content_id']."' AND `player`='".$_GET['player']."'");
			} else {
				$storeSlightSet = $_GET['direction'];
				if ($storeSlightSet < 0) $storeSlightSet = 0;
				db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('".$_GET['content_id']."', ".$storeSlightSet.", '".$_GET['player']."')");
			}
		}
	}
}
?>