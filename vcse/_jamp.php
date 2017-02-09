<?php
define('VCEXE', 1);

header("Content-type: text/html; charset=utf-8");

if (isset($_GET['player']) && isset($_GET['pass']) && isset($_GET['jamp'])) {
	// verify pass & load vars
	$vcseAuth = false;
	require($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/config.php');
	if ($vcseAuth) {
		// set
		db_work("UPDATE `players_vars` SET `value`='".$_GET['jamp']."' WHERE `name`='map' AND `player`='".$_GET['player']."'");
		unset($dbRadiation2);
		
		require($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/config.php');
		
		// x10 wear engine
		$wear = ceil(10 * ($equipmentWeight / $dbItems['tech'][$equipmentKeys['engine']]));
		$dbItems['durability'][$equipmentKeys['engine']] -= $wear * 10;
		if ($dbItems['durability'][$equipmentKeys['engine']] < 0) $vcseGameOver = true;
		db_work("UPDATE `players_items` SET `durability`=".$dbItems['durability'][$equipmentKeys['engine']]." WHERE `pid`=".$dbConfig['engine']);
		// x10 energy
		$dbConfig['energy'] -= $wear * 10;
		db_work("UPDATE `players_vars` SET `value`=".$dbConfig['energy']." WHERE `name`='energy' AND `player`='".$_GET['player']."'");
	
		require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/content.php');
	}	
}
?>