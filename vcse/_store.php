<?php
define('VCEXE', 1);

header("Content-type: text/html; charset=utf-8");

if (isset($_GET['player']) && isset($_GET['pass']) && isset($_GET['item_action']) && isset($_GET['item_id_type']) && isset($_GET['item_id']) && isset($_GET['item_quantity'])) {
	// verify pass & load vars
	$vcseAuth = false;
	require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/config.php');
	if ($vcseAuth) {
		$delCurItem = false;
		$reloadPlayersVars = false;
		
		// buy
 		if ($_GET['item_action']=='buy' 
			&& $dbCurItem['quantity'] >= $_GET['item_quantity']
			&& ($dbCurItem['price'] * $_GET['item_quantity'] <= $dbConfig['energy'] || ($dbCurObjMyColony && $dbCurItem['type']!='permit') || $dbCurItem['type']=='passenger')
			&& ($dbCurItem['type'] != 'passenger' || $cabinQuantity > $passengersQuantity)
			&& ($dbCurItem['type'].'-'.$dbCurItem['subtype'] != 'passenger-racer' || !isset($dbConfig['in_race']) || !$dbConfig['in_race'])
			&& ($dbCurItem['type'].'-'.$dbCurItem['subtype'] != 'passenger-racer' || ($equipmentCost > (10000 * floor($dbCurItem['price'] / 10000)) - 10000 && $equipmentCost < 50000 * floor($dbCurItem['price'] / 10000)))
		) { 
			// флаг гонки
			$startRace = false;
			if ($dbCurItem['type'].'-'.$dbCurItem['subtype'] == 'passenger-racer') $startRace = true;
			
			// up energy
			if (($dbCurItem['type']!='passenger' && !$dbCurObjMyColony) || $dbCurItem['type']=='permit') {
				$dbConfig['energy'] -= $dbCurItem['price'] * $_GET['item_quantity'];
				db_work("UPDATE `players_vars` SET `value`=".$dbConfig['energy']." WHERE `name`='energy' AND `player`='".$_GET['player']."'");
			}
			
			// up store
			if (!$startRace) {
				$dbCurItem['quantity'] -=  $_GET['item_quantity'];
				if ($dbCurItem['quantity']) {
					db_work("UPDATE `objects_items` SET `quantity`=".$dbCurItem['quantity']." WHERE `oid`=".$dbCurItem['oid']);
				} else {
					db_work("DELETE FROM `objects_items` WHERE `oid`=".$dbCurItem['oid']);
					$delCurItem = true;
					$reloadPlayersVars = true;
				}
			}
			
			// verify available in inventory
			if ($dbItems) {
				$keyItemInInvetory = array_search($dbCurItem['id'], $dbItems['id']);
			} else {
				$keyItemInInvetory = false;
			}
			if ($dbCurItem['type']!='case' || $keyItemInInvetory === false) {
				// старт гонки
				if ($startRace) {
					// поднять флаг гонки в переменных игрока
					if (isset($dbConfig['in_race'])) {
						db_work("UPDATE `players_vars` SET `value`=1 WHERE `name`='in_race' AND `player`='".$_GET['player']."'");
					} else {
						db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('in_race', 1, '".$_GET['player']."')");
					}
					$dbConfig['in_race'] = 1;
					// подсчет дистанции гонки
					$finishObjKey = array_search($dbCurItem['tech'], $dbObj['id']);
					$startX = vcseGetPosX($dbCurObj['x'][0], $dbCurObj['y'][0]);
					$startY = vcseGetPosY($dbCurObj['y'][0]);
					$finishX = vcseGetPosX($dbObj['x'][$finishObjKey], $dbObj['y'][$finishObjKey]);
					$finishY = vcseGetPosY($dbObj['y'][$finishObjKey]);
					$distanceRace = ceil((abs($finishX - $startX) + abs($finishY - $startY)) / 64);
					//$aaa = $distanceRace.' = abs('.$finishX.' - '.$startX.') + abs('.$finishY.' - '.$startY.')'; // debug
					// назначение прочности гонщика
					//$dbCurItem['durability'] = $distanceRace * 3;
					// получение инфо о игроках в гонке, и если их нет - генерировать
					$dbCurRacers = db_work("SELECT * FROM `racers` WHERE `race_oid`=".$dbCurItem['oid']);
					if (!$dbCurRacers) {
						for ($i=1; $i<=5; $i++) {
							//db_work("INSERT INTO `racers` (`race_oid`, `start_obj`, `finish_obj`, `start_time`, `time`, `player`) VALUES (".$dbCurItem['oid'].", ".$dbCurObj['id'][0].", ".$dbCurItem['tech'].", 0, ".(rand(ceil($distanceRace*0.8), ceil($distanceRace*1.2)) + (rand(0, 100) / 100)).", 'comp".$i."')");
							//db_work("INSERT INTO `racers` (`race_oid`, `start_obj`, `finish_obj`, `start_time`, `time`, `player`) VALUES (".$dbCurItem['oid'].", ".$dbCurObj['id'][0].", ".$dbCurItem['tech'].", 0, ".(round(($distanceRace / 64) * (0.6 + ($i / 10))) + (rand(0, 100) / 100)).", 'comp".$i."')");
							db_work("INSERT INTO `racers` (`race_oid`, `start_obj`, `finish_obj`, `start_time`, `time`, `player`) VALUES (".$dbCurItem['oid'].", ".$dbCurObj['id'][0].", ".$dbCurItem['tech'].", 0, ".(rand(round($distanceRace*0.65), round($distanceRace*0.8)) + (rand(0, 100) / 100)).", 'comp".$i."')");
						}
					}
					// изменение имени игрока у старых результатов в текущей гонке
					db_work("UPDATE `racers` SET `player`='".$_GET['player']." (old)' WHERE `race_oid`=".$dbCurItem['oid']." AND `player`='".$_GET['player']."'");
					// добавление записи о текущей гонке
					db_work("INSERT INTO `racers` (`race_oid`, `start_obj`, `finish_obj`, `start_time`, `player`) VALUES (".$dbCurItem['oid'].", ".$dbCurObj['id'][0].", ".$dbCurItem['tech'].", ".time().", '".$_GET['player']."')");
				}
				
				// up inventory
				db_work("INSERT INTO `players_items` (`item_id`, `durability`, `quantity`, `tech`, `tech2`, `price`, `player`) VALUES (".$dbCurItem['id'].", ".($dbCurItem['type']=="permit"?time()+604800:$dbCurItem['durability']).", ".$_GET['item_quantity'].", ".$dbCurItem['tech'].", ".($startRace?$dbCurItem['oid']:0).", ".$dbCurItem['price'].", '".$_GET['player']."')");
				$reloadPlayersVars = true;
			} else {
				// calc new quantity in inventory
				$dbItems['quantity'][$keyItemInInvetory] += $_GET['item_quantity'];
				// up inventory
				db_work("UPDATE `players_items` SET `quantity`=".$dbItems['quantity'][$keyItemInInvetory]." WHERE `pid`=".$dbItems['pid'][$keyItemInInvetory]);
			}
		}
		
		// sell
 		if ($_GET['item_action']=='sell' && $dbCurItem['quantity'] >= $_GET['item_quantity']) {
			// up energy
			$passengerNoTarget = false;
			if ($dbCurItem['type']!='passenger' || $dbCurItem['tech']==$dbCurObj['id'][0]) {
				if ($dbCurItem['type']=='passenger' || !$dbCurObjMyColony) {
					$dbConfig['energy'] += $dbCurItem['price'] * $_GET['item_quantity'];
					db_work("UPDATE `players_vars` SET `value`=".$dbConfig['energy']." WHERE `name`='energy' AND `player`='".$_GET['player']."'");
				}
			} elseif ($dbCurItem['type']=='passenger') {
				$dbConfig['energy'] -= $dbCurItem['price'] * $_GET['item_quantity'];
				db_work("UPDATE `players_vars` SET `value`=".$dbConfig['energy']." WHERE `name`='energy' AND `player`='".$_GET['player']."'");
				$passengerNoTarget = true;
				// если гонщик, снять флаг гонки
				$dbConfig['in_race'] = 0;
				db_work("UPDATE `players_vars` SET `value`=0 WHERE `name`='in_race' AND `player`='".$_GET['player']."'");
				db_work("DELETE FROM `racers` WHERE `time`=0 AND `player`='".$_GET['player']."'");
			}
			
			// up inventory
			$dbCurItem['quantity'] -= $_GET['item_quantity'];
			if ($dbCurItem['quantity']) {
				db_work("UPDATE `players_items` SET `quantity`=".$dbCurItem['quantity']." WHERE `pid`=".$dbCurItem['pid']);
			} else {
				db_work("DELETE FROM `players_items` WHERE `pid`=".$dbCurItem['pid']);
				$delCurItem = true;
				$reloadPlayersVars = true;
			}
			
			// verify available in store
			if (($dbCurItem['type']!='passenger' || $passengerNoTarget)
				&& $dbCurItem['type']!='message'
				&& $dbCurItem['type'].'-'.$dbCurItem['subtype'] != 'passenger-racer'
			) {
				if ($dbCurObjItems) {
					$keyItemInStore = array_search($dbCurItem['id'], $dbCurObjItems['id']);
				} else {
					$keyItemInStore = false;
				}
				if ($dbCurItem['type']!='case' || $keyItemInStore === false) {
					// up store
					db_work("INSERT INTO `objects_items` (`object_id`, `item_id`, `durability`, `quantity`, `tech`, `price`) VALUES (".$dbCurObj['id'][0].", ".$dbCurItem['id'].", ".$dbCurItem['durability'].", ".$_GET['item_quantity'].", ".$dbCurItem['tech'].", ".(isset($dbCurItem['original_price'])?$dbCurItem['original_price']:$dbCurItem['price']).")");
					$reloadPlayersVars = true;
				} else {
					// calc new quantity in store
					$dbCurObjItems['quantity'][$keyItemInStore] += $_GET['item_quantity'];
					// up store
					db_work("UPDATE `objects_items` SET `quantity`=".$dbCurObjItems['quantity'][$keyItemInStore]." WHERE `oid`=".$dbCurObjItems['oid'][$keyItemInStore]);
				}
			}
		}

		if ($reloadPlayersVars) {
			// обновление товаров на корабле
			$dbItems = db_work(
				"SELECT * FROM `players_items` `a` 
				LEFT JOIN `items` `b` ON `a`.`item_id` = `b`.`id` 
				WHERE `a`.`player`='".$_GET['player']."' 
				ORDER BY `b`.`type`, `a`.`price`"
			);
			// обновление установленного оборудования
			$equipmentKeys = array();
			foreach ($defaultItemsList as $v) {
				$equipmentKeys[$v] = array_search($dbConfig[$v], $dbItems['pid']);
			}
			// корректировка цены в трюме
			foreach ($dbItems['pid'] as $k => $v) {
				vcseDecreasePrice($dbItems, $k, $dbCurObjItems);
			}
			// обновление товаров на объекте
			$dbCurObjItems = db_work(
				"SELECT * FROM `objects_items` `a` 
				LEFT JOIN `items` `b` ON `a`.`item_id` = `b`.`id` 
				WHERE `a`.`object_id`='".$dbCurObj['id'][0]."' 
				ORDER BY `b`.`type`, `a`.`price`"
			);
		}
		if ($delCurItem) unset($dbCurItem);
		
		require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/panel.php');
	}
}
?>