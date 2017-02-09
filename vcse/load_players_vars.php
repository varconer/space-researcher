<?php
defined('VCEXE') or die;

$player = isset($_SESSION['player']) ? $_SESSION['player'] : (isset($_REQUEST['player']) ? $_REQUEST['player'] : '');
$pass = isset($_SESSION['pass']) ? $_SESSION['pass'] : (isset($_REQUEST['pass']) ? $_REQUEST['pass'] : '');

if (!function_exists("vcseGenPrice")) {
	function vcseGenPrice($durability_max, $durability_min, $genDurability, $tech_max, $tech_min, $genTech, $price_max, $price_min) {
		$koof1=1;
		if ($durability_max - $durability_min > 0) {
			$koof1 = ($genDurability - $durability_min) * (1 / ($durability_max - $durability_min));
		}
		$koof2=1;
		if ($tech_max - $tech_min > 0) {
			$koof2 = ($genTech - $tech_min) * (1 / ($tech_max - $tech_min));
		}
		//$koof = ($koof1 + $koof2) / 2;
		$genPrice = $price_min + (($price_max - $price_min) * $koof1 * $koof1 * $koof2);
		
		return $genPrice;
	}
}

if (!function_exists("vcseDecreasePrice")) {
	function vcseDecreasePrice(&$dbItems, $k, &$dbCurObjItems) {
		// ���� ��������� ��������� � �����
		if ($dbItems['type'][$k]=='message') {
			// �������� ��� ����
		// ���� ��������� ��������� �� ����� �� ������, �� ����� ���� �� ������� ����� 5% (���� �� ������� ��� ������ ������, �� ����� ���� ���� ����� 5%)
		} elseif ($dbItems['type'][$k]=='case') {
			if (!$dbCurObjItems) {
				$itemKeyInObj = false;
			} else {
				$itemKeyInObj = array_search($dbItems['id'][$k], $dbCurObjItems['id']);
			}
			if ($itemKeyInObj !== false) {
				$dbItems['price'][$k] = floor($dbCurObjItems['price'][$itemKeyInObj] * 0.95);
			} else {
				$dbItems['price'][$k] = floor($dbItems['price'][$k] * 0.95);
			}
		// ���� ��������� ������������ �� �����, ��������� ��� ���� �� 20%
		} elseif ($dbItems['type'][$k]!='passenger' && $dbItems['type'][$k]!='permit') {
			$dbItems['price'][$k] = vcseGenPrice($dbItems['durability_max'][$k], $dbItems['durability_min'][$k], $dbItems['durability'][$k], $dbItems['tech_max'][$k], $dbItems['tech_min'][$k], $dbItems['tech'][$k], $dbItems['price_max'][$k], $dbItems['price_min'][$k]);			
			$dbItems['original_price'][$k] = floor($dbItems['price'][$k]); // ���������� ������������ ����
			$dbItems['price'][$k] = floor($dbItems['price'][$k] * 0.8);
		// ���� ��������� ����������, ����=0
		} elseif ($dbItems['type'][$k]=='permit') {
			$daysExpire = 0;
			if ($dbItems['durability'][$k] < time()) {
				// ������� �� �����
				$daysExpire = ceil((time() - $dbItems['durability'][$k]) / (60 * 60 * 24));
			}
			$dbItems['original_price'][$k] = ($dbItems['price'][$k] * 2) + ($daysExpire * $dbItems['price'][$k] * 0.02); // ���������� ������������ ���� + ������� �� ����� � ���������� ������
			$dbItems['price'][$k] = 0;
		}
	}
}

// ������ ����������� ������������ ������������
$defaultItemsList = array(
	"hull",
	"engine",
	"hoarder"
);

// �������� ������ ������ ($dbConfig)
$vcseAuth = false;
if ($player && $pass) {
	if (db_work("SELECT `id` FROM `players` WHERE `player`='".$player."' AND `pass`='".md5($pass)."'")) {
		$vcseAuth = true;
		// ���� ����� �����
		if (!db_work("SELECT `id` FROM `players_vars` WHERE `player`='".$player."' LIMIT 5,1")) { // anti bug: 'LIMIT 5,1'
			require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/load_default_players_vars.php'); 
		}
		// �������� ���������� � ������ $dbConfig
		$dbResConfig = db_work("SELECT `name`, `value` FROM `players_vars` WHERE `player`='".$player."'");
		$dbConfig = array();
		if ($dbResConfig) {
			foreach ($dbResConfig['name'] as $k => $v) {
				$dbConfig[$v] = $dbResConfig['value'][$k];
			}
		}
	}
}

if ($vcseAuth) {
	// ���� ������ ($dbItems)
	$dbItems = db_work(
		"SELECT * FROM `players_items` `a` 
		LEFT JOIN `items` `b` ON `a`.`item_id` = `b`.`id` 
		WHERE `a`.`player`='".$player."' 
		ORDER BY `b`.`type`, `a`.`price`"
	);

	// �������� ����� ������������ � ������� $dbItems ($equipmentKeys)
	$equipmentKeys = array();
	foreach ($defaultItemsList as $v) {
		$equipmentKeys[$v] = array_search($dbConfig[$v], $dbItems['pid']);
	}

	// ������� ���� ������� ($equipmentWeight) / ������ ������������ ($equipmentCapacity) / ��������� �������������� ������������ ($equipmentCost) / ���-�� ���������� ($passengersQuantity) / ���-�� ���� ($cabinQuantity)
	$equipmentWeight = 0;
	$equipmentCapacity = 0;
	$equipmentCost = 0; // ���������� ���� ���� �����, ����� ��������� ��������� ����������� �����
	$passengersQuantity = 0;
	$cabinQuantity = 0;
	foreach ($dbItems['id'] as $k => $v) {
		// ������� ���� �������
		$equipmentWeight += $dbItems['quantity'][$k] * $dbItems['weight'][$k];
		// ������� ������ ������������ (������������� ������ � ��������� �� ���������)
		if ($k != $equipmentKeys['hull'] && $dbItems['type'][$k] != "passenger") $equipmentCapacity += $dbItems['quantity'][$k] * $dbItems['capacity'][$k];
		// ������� ���-�� ����������
		if ($dbItems['type'][$k] == "passenger") $passengersQuantity++;
		// ������� ���-�� ����
		if ($dbItems['type'][$k] == "cabin") $cabinQuantity += $dbItems['tech'][$k];
	}

	// ���������� $dbConfig['ship_type'], ���� �� ���������
	if (!isset($dbConfig['ship_type'])) {
		$dbConfig['ship_type'] = $dbItems['subtype'][$equipmentKeys['hull']];
		db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('ship_type', '".$dbConfig['ship_type']."', '".$player."')");
	}

	// �������� ������ ������� ($dbRadiation)
	$dbResSpace = db_work("SELECT `y`, `radiation` FROM `maps_vars` WHERE `map`='".$dbConfig['map']."'");
	if ($dbResSpace) {
		foreach ($dbResSpace['y'] as $k => $v) {
			$dbRadiation[$v] = $dbResSpace['radiation'][$k];
		}
	}
	// �������� ������ ������� ��� map v2.0 ($dbRadiation2)
	$dbResSpace2 = db_work("SELECT `x`, `radiation` FROM `maps_vars_2` WHERE `map`='".$dbConfig['map']."'");
	if ($dbResSpace2) {
		foreach ($dbResSpace2['x'] as $k => $v) {
			$dbRadiation2[$v] = $dbResSpace2['radiation'][$k];
		}
	}
	
	// ���������� $dbConfig['time'], ��� �����
	if (isset($_GET['reload_map'])) {
		$dbConfig['time'] = time();
		db_work("DELETE FROM `players_vars` WHERE `name`='time' AND `player`='".$player."'");
		db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('time', '".$dbConfig['time']."', '".$player."')");
		unset($_GET['reload_map']);
	}

	// ������� ������� ($vcseColQuan, $vcseLineQuan)
	$dbResMap = db_work("SELECT `size_x`, `size_y` FROM `maps` WHERE `map`='".$dbConfig['map']."'");
	$vcseColQuan = $dbResMap['size_x'][0]; // ���-�� ������� � ����� �������
	$vcseLineQuan = $dbResMap['size_y'][0]; // ���-�� ����� � ����� �������

	// �������� ����������� �������� ($dbObj)
	$dbObj = db_work("SELECT * FROM `objects` WHERE `map`='".$dbConfig['map']."'");

	// ���� �� �������, �� ������� ��������� ���� ��� �� ������� ��������� ������� ($dbCurObj)
	$dbCurObj = db_work("SELECT * FROM `objects` WHERE `x`=".(isset($_GET['toX'])?$_GET['toX']:$dbConfig['x'])." AND `y`=".(isset($_GET['toY'])?$_GET['toY']:$dbConfig['y'])." AND `map`='".$dbConfig['map']."'");
	
	// �������� ���������� (�� �������� �� ��� ���� ������)
	$dbItemsPassengers = db_work(
		"SELECT * FROM `players_items` `a` 
		LEFT JOIN `items` `b` ON `a`.`item_id` = `b`.`id` 
		WHERE `a`.`player`='".$player."' AND `b`.`type`='passenger' AND `a`.`tech`='".$dbCurObj['id'][0]."'
		ORDER BY `b`.`type`, `a`.`price`"
	);
	if ($dbItemsPassengers) {
		foreach ($dbItemsPassengers['pid'] as $pK => $pV) {
			// ���� ������ ������ ����
			if ($dbItemsPassengers['subtype'][$pK] == "racer") {
				// ������� ������ ������������
				$equipList = $dbItems['item'][$equipmentKeys['hull']]."/".$dbItems['item'][$equipmentKeys['engine']]."/".$dbItems['item'][$equipmentKeys['hoarder']];
				// ������������� ����� �����
				$dbRaceResultPre = db_work("SELECT `start_time` FROM `racers` WHERE `race_oid`=".$dbItemsPassengers['tech2'][$pK]." AND `player`='".$player."'");
				$raceTime = microtime(true) - $dbRaceResultPre['start_time'][0];
				db_work("UPDATE `racers` SET `time`=".$raceTime.", `equip`='".$equipList."' WHERE `race_oid`=".$dbItemsPassengers['tech2'][$pK]." AND `player`='".$player."'");
				// ����� ���� �����
				$dbConfig['in_race'] = 0;
				db_work("UPDATE `players_vars` SET `value`=0 WHERE `name`='in_race' AND `player`='".$player."'");
				// �������� ����� ������� ����������� �����
				$dbRaceResult = db_work("SELECT * FROM `racers` WHERE `race_oid`=".$dbItemsPassengers['tech2'][$pK]." ORDER BY `time`");
				// ������� ��������������
				$racerKey = array_search($player, $dbRaceResult['player']);
				if ($racerKey == 1) $dbItemsPassengers['price'][$pK] = ceil($dbItemsPassengers['price'][$pK] / 2);
				if ($racerKey == 2) $dbItemsPassengers['price'][$pK] = ceil($dbItemsPassengers['price'][$pK] / 4);
				if ($racerKey > 2) $dbItemsPassengers['price'][$pK] = 0;
				// ������� ��������� � ����������� �����
				$messageRaceId = db_work("SELECT * FROM `items` WHERE `type`='message' AND `subtype`='race'");
				db_work("INSERT INTO `players_items` (`item_id`, `durability`, `quantity`, `tech`, `tech2`, `price`, `player`) VALUES (".$messageRaceId['id'][0].", 1, 1, 0, ".$dbItemsPassengers['tech2'][$pK].", ".$dbItemsPassengers['price'][$pK].", '".$player."')");
				// ��������� ������ �� ������ (�������������� ���������� ����� ������� ��������� � �����)
				$dbItemsPassengers['price'][$pK] = 0;
			}
			// ������ �� ������ � �������� ��������� (�������)
			$dbConfig['energy'] += $dbItemsPassengers['price'][$pK];
			db_work("DELETE FROM `players_items` WHERE `pid`='".$dbItemsPassengers['pid'][$pK]."'");
			$passengersQuantity--;
		}
		// ���������� ������� �� �������
		$dbItems = db_work(
			"SELECT * FROM `players_items` `a` 
			LEFT JOIN `items` `b` ON `a`.`item_id` = `b`.`id` 
			WHERE `a`.`player`='".$player."' 
			ORDER BY `b`.`type`, `a`.`price`"
		);
	}
	
	// ���� �������, ������� ����� ���� �������������� ($dbCurObjFree)
	$dbCurObjFree = false;
	if ($dbCurObj && $dbCurObj['type'][0]=="planet" && substr($dbCurObj['subtype'][0], 0, 4)=="free") $dbCurObjFree = true;
	
	// ���� ����������� ������� ������� ������� ($dbCurObjMyColony)
	$dbCurObjMyColony = false;
	if ($dbCurObjFree) {
		// ��������� �������
		$keysPermit = array_keys($dbItems['type'], "permit");
		if ($keysPermit) {
			foreach ($keysPermit as $keyPermit) {
				if ($dbItems['subtype'][$keyPermit] == "colonization" 
					&& $dbItems['tech'][$keyPermit] == $dbCurObj['id'][0] 
					&& $dbItems['durability'][$keyPermit] > time()
				) {
					$dbCurObjMyColony = true;
				}
			}
		}
	} elseif ($dbCurObj && $dbCurObj['subtype'][0]=="station") {
		// �������
		$dbCurObjMyColony = true;
	}
	

	// ���� � ������� �� ������� ($dbCurObjItems)
	$dbCurObjItems = array();
	if ($dbCurObj) {
		$step = 0;
		do {
			$step++;
			// ������ � ������� �� �������
			$dbCurObjItems = db_work(
				"SELECT * FROM `objects_items` `a` 
				LEFT JOIN `items` `b` ON `a`.`item_id` = `b`.`id` 
				WHERE `a`.`object_id`='".$dbCurObj['id'][0]."' 
				ORDER BY `b`.`type`, `a`.`price`"
			);
			// ���� �������, �� �� ������������ � �� ������������ (����� �� �����)
			if ($dbCurObj['subtype'][0] == 'station') break;
			// ���� �������, ������� ������ ������������ ����������� � �������, � ��� �� ������ ������� � ��������� �������
			if ($dbCurObjFree && $dbCurObjItems) {
				$dbCurObjEquipCapacity = 0; // $dbCurObjEquipCapacity - ����� ���������� � �������
				$dbCurObjHangarsCapacity = 0; // $dbCurObjHangarsCapacity - ����� �������
				//$dbCurObjFactoryPrice = 0; // $dbCurObjFactoryPrice - ��������� ���� �������
				foreach ($dbCurObjItems['id'] as $k => $v) {
					if ($dbCurObjItems['type'][$k] != "factory" && $dbCurObjItems['type'][$k] != "hangar" && $dbCurObjItems['type'][$k] != "passenger") $dbCurObjEquipCapacity += $dbCurObjItems['capacity'][$k];
					if ($dbCurObjItems['type'][$k] == "hangar") $dbCurObjHangarsCapacity += $dbCurObjItems['tech'][$k];
					//if ($dbCurObjItems['type'][$k] == "factory") $dbCurObjFactoryPrice += $dbCurObjItems['price'][$k];
				}
			}
			// �������������� �����
			if ($step>=2) break;
			// ��������� �������, ���� �� ������� �� ��� (��� ������ ����� �� ������� ���������)
			$dbCurObjItemsNeedRefesh = false;
			if (!$dbCurObjItems || time() > $dbCurObj['last_gen'][0]+86400) {
				// del old items
				if ($dbCurObjItems && !$dbCurObjFree) {
					foreach ($dbCurObjItems['oid'] as $k => $v) {
						//if ($dbCurObjItems['type'][$k]!="permit" && $dbCurObjItems['type'][$k]!="factory") {
							db_work("DELETE FROM `objects_items` WHERE `object_id`=".$dbCurObj['id'][0]." AND `oid`=".$v);
						//}
					}
				}
				// gen new items
				$dbItemsForGen = db_work("SELECT * FROM `items`");
				if ($dbItemsForGen) {
					foreach ($dbItemsForGen['id'] as $k => $v) {
						// ���� ���������� - �� ������������
						if ($dbItemsForGen['type'][$k] == "permit") continue;
						// ����������� ��������� �� ��������, ��������������� ��� �����������
						$factoryQuantity = 1;
						$cycleQuantity = 1;
						if ($dbCurObjFree && $dbItemsForGen['type'][$k] != "passenger") {
							// �������� �� ������� ������, ��� ��������� �������� ������
							list($subTypeDetail) = explode("-", $dbItemsForGen['subtype'][$k]);
							$dbCurObjFactory = db_work(
								"SELECT * FROM `objects_items` `a` 
								LEFT JOIN `items` `b` ON `a`.`item_id` = `b`.`id` 
								WHERE `a`.`object_id`='".$dbCurObj['id'][0]."' AND `b`.`type`='factory' AND `b`.`subtype`='".$subTypeDetail."'"
							);
							// ������� ���������� ������� ������� ����
							$factoryQuantity = count($dbCurObjFactory['oid']); 
							if (!$dbCurObjFactory) continue;
							// ������� ���������� ������ (�� ������ ���� ������ ������� ����� � ������� ��������� ���������)
							$curTime = time();
							$cycleQuantity = floor(($curTime - $dbCurObj['last_gen'][0]) / 86400);
							if ($cycleQuantity<1) $cycleQuantity=1;
							if ($cycleQuantity>7) $cycleQuantity=7;
							$factoryQuantity = $factoryQuantity * $cycleQuantity;
						}
						// quantity
						$genQuantity = 0;
						for ($f=1; $f<=$factoryQuantity; $f++) {
							// ���������� ���-�� ��� ������� ������
							$addQuantity = mt_rand($dbItemsForGen['quantity_min'][$k], $dbItemsForGen['quantity_max'][$k]);
							if ($addQuantity>0) $genQuantity += $addQuantity;
						}
						for ($i=1; $i<=$genQuantity; $i++) {
							// durability
							$genNew = mt_rand(0, 100);
							if ($dbItemsForGen['new_percent'][$k] < $genNew) {	
								$genDurability = mt_rand($dbItemsForGen['durability_min'][$k], $dbItemsForGen['durability_max'][$k]);
							} else {
								$genDurability = $dbItemsForGen['durability_max'][$k];
							}
							// tech
							if ($dbItemsForGen['type'][$k] == "passenger") {
								// v0.9
								/* $try = 0;
								do {
									$try++;
									//if ($try>10) break;
									$dbObjCount = count($dbObj);
									$keyRandom = mt_rand(0, $dbObjCount-1);
								} while ($dbCurObj['id'][0] == $dbObj['id'][$keyRandom] || $dbObj['type'][$keyRandom] == "star");
								$genTech = $dbObj['id'][$keyRandom];
								//if ($try>10) $genQuantity = 0; */
								
								// v1.0
								// ������ id �������� (������� ����������) ��� ����������
								$dbPassTargetObj = array();
								foreach ($dbObj['id'] as $idK => $idV) {
									if ($dbObj['type'][$idK]=="planet" && $dbCurObj['id'][0]!=$idV) {
										$dbPassTargetObj[] = $idV;
									}
								}
								// ���������� ����� ����������
								$dbPassTargetObjCount = count($dbPassTargetObj);
								$keyRandom = mt_rand(0, $dbPassTargetObjCount-1);
								$genTech = $dbPassTargetObj[$keyRandom];
							} else {
								$genTech = mt_rand($dbItemsForGen['tech_min'][$k], $dbItemsForGen['tech_max'][$k]);
							}
							// price
							switch ($dbItemsForGen['type'][$k]) {
							case 'case':
							case 'passenger':
								$genPrice = mt_rand($dbItemsForGen['price_min'][$k], $dbItemsForGen['price_max'][$k]);
								break;
							case 'hull':
							case 'engine':
							case 'hoarder':
							case 'factory':
							case 'hangar':
							case 'cabin':
								// v1.0
								/* $koof1=1;
								if ($dbItemsForGen['durability_max'][$k] - $dbItemsForGen['durability_min'][$k] > 0) {
									$koof1 = ($genDurability - $dbItemsForGen['durability_min'][$k]) * (1 / ($dbItemsForGen['durability_max'][$k] - $dbItemsForGen['durability_min'][$k]));
								}
								$koof2=1;
								if ($dbItemsForGen['tech_max'][$k] - $dbItemsForGen['tech_min'][$k] > 0) {
									$koof2 = ($genTech - $dbItemsForGen['tech_min'][$k]) * (1 / ($dbItemsForGen['tech_max'][$k] - $dbItemsForGen['tech_min'][$k]));
								}
								$koof = ($koof1 + $koof2) / 2;
								$genPrice = $dbItemsForGen['price_min'][$k] + ($dbItemsForGen['price_max'][$k] - $dbItemsForGen['price_min'][$k]) * $koof; */
								// v2.0
								$genPrice = vcseGenPrice($dbItemsForGen['durability_max'][$k], $dbItemsForGen['durability_min'][$k], $genDurability, $dbItemsForGen['tech_max'][$k], $dbItemsForGen['tech_min'][$k], $genTech, $dbItemsForGen['price_max'][$k], $dbItemsForGen['price_min'][$k]);
								$genPrice -= mt_rand(0, $genPrice*0.1) - mt_rand(0, $genPrice*0.1);
								break;
							}
							// generate
							if ($genQuantity>0) {
								// ���� �������
								if ($dbCurObjFree) {
									// ���� �� ������� �������, �� ������������
									if ($dbCurObjEquipCapacity + $dbItemsForGen['capacity'][$k] > $dbCurObjHangarsCapacity) continue;
									// �������� ����� ������������� ������ � ������ ��������� �������
									$dbCurObjEquipCapacity += $dbItemsForGen['capacity'][$k];
									// ���� ������������ 'case', � �� ��� ���� � ������ ������� - ������� ��������� ������, � ���������� ��� ��������� ����������� � ������� �����������
									if ($dbItemsForGen['type'][$k]=='case' && $dbCurObjItems) {
										$caseKey = array_search($v, $dbCurObjItems['id']);
										if ($caseKey!==false) {
											$genQuantity += $dbCurObjItems['quantity'][$caseKey];
											db_work("DELETE FROM `objects_items` WHERE `object_id`=".$dbCurObj['id'][0]." AND `item_id`=".$v);
										}
									}
								}
								// �������� �����
								db_work(
									"INSERT INTO `objects_items` (`object_id`, `item_id`, `durability`, `quantity`, `tech`, `price`) VALUES (".
										$dbCurObj['id'][0].", ".
										$v.", ".
										$genDurability.", ".
										($dbItemsForGen['type'][$k]=='case'?$genQuantity:1).", ".
										$genTech.", ".
										$genPrice.
									")"
								);
								// ���� �������, �� ��������� ��������� ������ �� ��� ������������� ������
								if ($dbCurObjFree && $dbCurObjFactory) {
									$wear = $dbItemsForGen['weight'][$k];
									if ($dbItemsForGen['type'][$k]=='case') $wear = $wear * $genQuantity;
									$dbCurObjFactory['durability'][0] = $dbCurObjFactory['durability'][0] - $wear;
									if ($dbCurObjFactory['durability'][0]) {
										db_work("UPDATE `objects_items` SET `durability`=".$dbCurObjFactory['durability'][0]." WHERE `oid`=".$dbCurObjFactory['oid'][0]);
									} else {
										db_work("DELETE FROM `objects_items` WHERE `oid`=".$dbCurObjFactory['oid'][0]);
									}
								}
							}
							if ($dbItemsForGen['type'][$k]=='case') break;
						}
					}
				}
				// set last generation date
				$dbCurObj['last_gen'][0] = time();
				db_work("UPDATE `objects` SET `last_gen`=".$dbCurObj['last_gen'][0]." WHERE `id`=".$dbCurObj['id'][0]);
				$dbCurObjItemsNeedRefesh = true;
				// ����� �������
				if ($dbCurObjFree && $dbCurObjItems) {
					foreach ($dbCurObjItems['id'] as $k => $v) {
						if ($dbCurObjItems['type'][$k] == "hangar") $dbCurObjItems['durability'][$k] -= mt_rand(0, ($dbCurObjEquipCapacity / $dbCurObjHangarsCapacity)*100);
						if ($dbCurObjItems['durability'][$k] < 0) {
							unset($dbCurObjItems['id'][$k]);
							db_work("DELETE FROM `objects_items` WHERE `oid`=".$dbCurObjItems['oid'][$k]);
						}
					}
				}
			}
		} while (!$dbCurObjItems || $dbCurObjItemsNeedRefesh);
	}
	
	// ������������� ���� � �����
	foreach ($dbItems['pid'] as $k => $v) {
		vcseDecreasePrice($dbItems, $k, $dbCurObjItems);
	}
	
	// ������� ��������� �������������� ������������
	$equipmentCost = $dbItems['price'][$equipmentKeys['hull']] + $dbItems['price'][$equipmentKeys['engine']] + $dbItems['price'][$equipmentKeys['hoarder']];

	// �������� ������ ������������� ������, ���� ����� ������ ($dbCurItem)
	if (isset($_REQUEST['item_id_type']) && isset($_REQUEST['item_id']) && $_REQUEST['item_id']) {
		if ($_REQUEST['item_id_type']=='pid') {
			$itemKey = array_search($_REQUEST['item_id'], $dbItems['pid']);
			$dbIt =& $dbItems;
		} elseif ($_REQUEST['item_id_type']=='oid') {
			$itemKey = array_search($_REQUEST['item_id'], $dbCurObjItems['oid']);
			$dbIt =& $dbCurObjItems;
		}
		if (isset($dbIt) && $itemKey!==false) {
			$dbCurItem = array();
			foreach ($dbIt as $k => $v) {
				$dbCurItem[$k] = $v[$itemKey];
			}
			// ���������� ���� �� ������ � �����
			/* if ($dbCurObj && $_REQUEST['item_id_type']=='pid') {
				// ���������� ������������ ����
				$dbCurItem['original_price'] = $dbCurItem['price'];
				// ���� ��������� ��������� �� ����� �� ������, �� ����� ���� �� ������� ����� 5% (���� �� ������� ��� ������ ������, �� ����� ���� ���� ����� 5%)
				if ($dbCurItem['type']=='case') {
					$itemKeyInObj = array_search($dbCurItem['id'], $dbCurObjItems['id']);
					if ($itemKeyInObj !== false) {
						$dbCurItem['price'] = floor($dbCurObjItems['price'][$itemKeyInObj] * 0.95);
					} else {
						$dbCurItem['price'] = floor($dbCurItem['price'] * 0.95);
					}
				// ���� ��������� ������������ �� �����, ��������� ��� ���� �� 5%
				} elseif ($dbCurItem['type']!='passenger' && $dbCurItem['type']!='permit') {
					// v1.0
					//$dbCurItem['price'] = floor($dbCurItem['price'] * 0.95);
					// v2.0
					$dbCurItem['price'] = vcseGenPrice($dbCurItem['durability_max'], $dbCurItem['durability_min'], $dbCurItem['durability'], $dbCurItem['tech_max'], $dbCurItem['tech_min'], $dbCurItem['tech'], $dbCurItem['price_max'], $dbCurItem['price_min']);
					$dbCurItem['price'] = floor($dbCurItem['price'] * 0.95);
				// ���� ��������� ����������
				} elseif ($dbCurItem['type']=='permit') {
					$dbCurItem['price'] = 0;
				}
			} */
		}
	}

	// �������� ������ ������� ($dbPlayers)
	$dbResPlayers = db_work("SELECT `player` FROM `players_vars` WHERE `name`='map' AND `value`='".$dbConfig['map']."' AND `player`!='".$player."'");
	$dbPlayers = array();
	if ($dbResPlayers) {
		foreach ($dbResPlayers['player'] as $pl) {
			$dbResConfig = db_work("SELECT `name`, `value` FROM `players_vars` WHERE `player`='".$pl."'");
			if ($dbResConfig) {
				foreach ($dbResConfig['name'] as $k => $v) {
					$dbPlayers[$pl][$v] = $dbResConfig['value'][$k];
				}
			}
		}
	}

	// �������� �������� �������� ��� �������� ($creationObjects, $creationPlanetNames, $creationPlanetName, $creationStarNames, $creationStarName)
	if ($dbConfig['creation']=='1') {
		// arr objects
		$creationObjects = array(
			"type" => array(
				"planet",
				"planet",
				"planet",
				"planet",
				"planet",
				"star"
			),
			"subtype" => array(
				"brown",
				"blue",
				"green",
				"free-grey",
				"station",
				"yellow"
			),
		);
		// arr planet names
		$creationPlanetNames = array(
			"Alpha",
			"Beta",
			"Gamma",
			"Delta",
			"Lambda"
		);
		// set planet name
		$creationPlanetName = vcseGetObjectNewName($creationPlanetNames, $dbConfig['creation_planet_name']);
		// arr star names
		$creationStarNames = array(
			"M-"
		);
		// set star name
		$creationStarName = vcseGetObjectNewName($creationStarNames, $dbConfig['creation_star_name']);
	}
}
?>