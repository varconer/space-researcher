<?php
defined('VCEXE') or die;

if ($vcseAuth && isset($_POST['map'])) {
	// generic new map
	$newMap = false;
	if ($_POST['map'] == "new_map" && !db_work("SELECT `id` FROM `maps` WHERE `map`='".$_POST['map']."'")) {
		$_POST['map'] = $_POST['map_name'];
		db_work("INSERT INTO `maps` (`map`, `size_x`, `size_y`, `point_x`, `point_y`) VALUES ('".$_POST['map']."', '".$_POST['map_size_x']."', '".$_POST['map_size_y']."', '".$_POST['map_point_x']."', '".$_POST['map_point_y']."')");
		// map v1.0
		for ($y=1; $y<=$_POST['map_size_y']; $y++) {
			db_work("INSERT INTO `maps_vars` (`y`, `radiation`, `map`) VALUES ('".$y."', '".(mt_rand(0, 9))."', '".$_POST['map']."')");
		}
		// map v2.0
		for ($x = 1-round(($_POST['map_size_y']-1)/2); $x < $_POST['map_size_x']+1; $x++) {
			db_work("INSERT INTO `maps_vars_2` (`x`, `radiation`, `map`) VALUES ('".$x."', '".(mt_rand(0, 9))."', '".$_POST['map']."')");
		}
		$newMap = true;
	// old map
	} else {
		$dbResMap = db_work("SELECT `point_x`, `point_y` FROM `maps` WHERE `map`='".$_POST['map']."'");
		$_POST['map_point_x'] = $dbResMap['point_x'][0];
		$_POST['map_point_y'] = $dbResMap['point_y'][0];
	}
		
	// load default player vars
	db_work("DELETE FROM `players_vars` WHERE `player`='".$_POST['player']."'"); // anti bug
	db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('x', '".$_POST['map_point_x']."', '".$_POST['player']."')");
	db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('y', '".$_POST['map_point_y']."', '".$_POST['player']."')");
	db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('energy', '100', '".$_POST['player']."')");
	//db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('ship_type', 'hull-m10', '".$_POST['player']."')");
	db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('tensity', '1', '".$_POST['player']."')");
	db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('tensity_direct', '1', '".$_POST['player']."')");
	db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('mode', '*', '".$_POST['player']."')");
	db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('panel', 'show', '".$_POST['player']."')");
	db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('angle', '0', '".$_POST['player']."')");
	db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('map', '".$_POST['map']."', '".$_POST['player']."')");
	
	// load default items
	foreach ($defaultItemsList as $v) {
		$dbResDefaultItem = db_work("SELECT * FROM `items` WHERE `type`='".$v."' ORDER BY `tech_max`, `capacity` LIMIT 0,1");
		if ($dbResDefaultItem) {
			db_work("INSERT INTO `players_items` (`item_id`, `durability`, `quantity`, `tech`, `price`, `player`) VALUES (".$dbResDefaultItem['id'][0].", ".$dbResDefaultItem['durability_max'][0].", 1, ".$dbResDefaultItem['tech_max'][0].", ".$dbResDefaultItem['price_max'][0].", '".$_POST['player']."')");
			$dbResLastId = db_work("SELECT `pid` FROM `players_items` WHERE `item_id`=".$dbResDefaultItem['id'][0]." AND `player`='".$_POST['player']."'");
			if ($dbResLastId) {
				db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('".$v."', '".$dbResLastId['pid'][0]."', '".$_POST['player']."')");
			}
		}
	}
	
	// creation mode
	db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('creation', '".($newMap?1:0)."', '".$_POST['player']."')");
	db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('creation_planet_name', 0, '".$_POST['player']."')");
	db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('creation_star_name', 0, '".$_POST['player']."')");
}
?>