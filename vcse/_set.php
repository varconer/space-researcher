<?php
define('VCEXE', 1);

header("Content-type: text/html; charset=utf-8");

if (isset($_GET['player']) && isset($_GET['pass']) && isset($_GET['type']) && isset($_GET['set'])) {
	// verify pass & load vars
	$vcseAuth = false;
	require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/config.php');
	if ($vcseAuth) {
		// set
		$equipmentKeys[$_GET['type']] = array_search($_GET['set'], $dbItems['pid']); 
		db_work("UPDATE `players_vars` SET `value`=".$_GET['set']." WHERE `name`='".$_GET['type']."' AND `player`='".$_GET['player']."'");
		if ($_GET['type']=='hull') db_work("DELETE FROM `players_vars` WHERE `name`='ship_type' AND `player`='".$_GET['player']."'");
	
		require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/panel.php');
	}	
}
?>