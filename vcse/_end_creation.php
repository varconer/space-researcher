<?php
define('VCEXE', 1);

header("Content-type: text/html; charset=utf-8");

if (isset($_GET['player']) && isset($_GET['pass'])) {
	// verify pass & load vars
	$vcseAuth = false;
	require($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/config.php');
	if ($vcseAuth) {
		// получить данные о разрешении на колонизацию
		$dbItemPermit = db_work("SELECT * FROM `items` WHERE `type`='permit' AND `subtype`='colonization'");
		// на все планеты, свободные для колонизации добавить разрешение на колонизацию
		foreach ($dbObj['id'] as $k => $v) {
			if (substr($dbObj['subtype'][$k], 0, 4) == "free") {
				db_work(
					"INSERT INTO `objects_items` (`object_id`, `item_id`, `durability`, `quantity`, `tech`, `price`) VALUES (".
						$v.", ".
						$dbItemPermit['id'][0].", ".
						$dbItemPermit['durability_max'][0].", ".
						"1, ".
						$v.", ".
						$dbItemPermit['price_max'][0].
					")"
				);
			}
		}
		
		// up
		$dbConfig['creation'] = 0;
		db_work("UPDATE `players_vars` SET `value`='0' WHERE `name`='creation' AND `player`='".$_GET['player']."'");

		require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/panel.php');
		
		echo "#sep#";
		
		require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/sectors.php');
		
		echo "#sep#";
		
		require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/objects.php');
	}
}
?>