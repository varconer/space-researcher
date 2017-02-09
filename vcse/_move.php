<?php
define('VCEXE', 1);

header("Content-type: text/html; charset=utf-8");

if (isset($_GET['toX']) && isset($_GET['toY']) && isset($_GET['player']) && isset($_GET['pass']) && isset($_GET['state'])) {
	// verify pass & load vars
	$vcseAuth = false;
	$vcseGameOver = false;
	require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/config.php');
	if ($vcseAuth) {
		// verify fly
		if (
			(
				$dbItems['capacity'][$equipmentKeys['hull']] >= $equipmentCapacity
				&& (abs($dbConfig['y'] - $_GET['toY']) <= 1 || (abs($dbConfig['y'] - $_GET['toY']) <= 2 && $dbConfig['x'] != $_GET['toX'])) 
				&& $dbConfig['y'] != $_GET['toY'] 
				&& abs($dbConfig['x'] - $_GET['toX']) <= 1
			)
			|| $dbConfig['creation']=='1'
		) {
			// calc angle
			$correctShipPosition = true;
			if ($dbConfig['y'] > $_GET['toY']) { // up (all)
				if ($dbConfig['x'] == $_GET['toX']) { // up left
					$dbConfig['angle'] = 300;
				} else { // $dbConfig['x'] < $_GET['toX'] // up & up right
					if ($dbConfig['y'] - $_GET['toY'] == 2) { // up
						$dbConfig['angle'] = 0;
						$correctShipPosition = false;
					} else { // up right
						$dbConfig['angle'] = 60;
					}
				}
			} else { // $dbConfig['y'] < $_GET['toY'] // down (all)
				if ($dbConfig['x'] == $_GET['toX']) { // down right
					$dbConfig['angle'] = 120;
				} else { // $dbConfig['x'] > $_GET['toX'] // down & down left
					if ($_GET['toY'] - $dbConfig['y'] == 2) { // down
						$dbConfig['angle'] = 180;
						$correctShipPosition = false;
					} else { // down left
						$dbConfig['angle'] = 240;
					}
				}
			}
			
			// обнуление положения слайдера магазина
			//db_work("UPDATE `players_vars` SET `value`=0 WHERE `name`='panel_store_group_content' AND `player`='".$_GET['player']."'");
			db_work("UPDATE `players_vars` SET `value`=0 WHERE `name`='panel_store_group_content_2' AND `player`='".$_GET['player']."'");
			
			// up angle
			db_work("UPDATE `players_vars` SET `value`='".$dbConfig['angle']."' WHERE `name`='angle' AND `player`='".$_GET['player']."'");
		
			// up coord
			db_work("UPDATE `players_vars` SET `value`=".$_GET['toX']." WHERE `name`='x' AND `player`='".$_GET['player']."'");
			db_work("UPDATE `players_vars` SET `value`=".$_GET['toY']." WHERE `name`='y' AND `player`='".$_GET['player']."'");
			$dbConfig['x'] = $_GET['toX'];
			$dbConfig['y'] = $_GET['toY'];
			
			if ($dbConfig['creation']=='0') {
				if (!isset($dbConfig['state'])) db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('state', '".$_GET['state']."', '".$_GET['player']."')"); // verify db vars
				
				// calc speed (ms)
				$shipSpeed = 2000 * ($dbItems['durability_max'][$equipmentKeys['engine']] / $dbItems['durability'][$equipmentKeys['engine']]) * ($equipmentWeight / $dbItems['tech'][$equipmentKeys['engine']]);
				
				// fly
				if ($_GET['state'] == "fly") {
					// calc energy
					$dbRadiation2value = 0;
					// map v1.0
					if (!isset($dbRadiation2)) {
						switch ($dbConfig['mode']) {
						case '*':
							$needEnergy = $dbConfig['tensity'] * $dbRadiation[$dbConfig['y']];
							break;
						case '+':
							$needEnergy = $dbConfig['tensity'] + $dbRadiation[$dbConfig['y']];
							break;
						case '-':
							$needEnergy = $dbConfig['tensity'] - $dbRadiation[$dbConfig['y']];
							break;
						}
					// map v2.0 
					} else {
						$dbRadiationValue = $dbRadiation[$dbConfig['y']] + $dbRadiation2[$dbConfig['x']] + (substr($dbConfig['time'], -4, 1) * substr($dbConfig['time'], -3, 1));
						$dbRadiationValue = substr($dbRadiationValue, -1);
						switch ($dbConfig['mode']) {
						case '*':
							$needEnergy = $dbConfig['tensity'] * $dbRadiationValue;
							break;
						case '+':
							$needEnergy = $dbConfig['tensity'] + $dbRadiationValue;
							break;
						case '-':
							$needEnergy = $dbConfig['tensity'] - $dbRadiationValue;
							break;
						}
					}
					$needEnergy = substr($needEnergy, -1);
					$dbConfig['energy'] -= $needEnergy;
										
					// дополнительная коррекция скорости в зависимости от радиации в секторе
					$shipSpeed = ceil($shipSpeed * (1 + ($needEnergy / 10)));
					
					// calc breaking
					$timeLife = array();
					// hull
					$wear = $needEnergy;
					$wear += ceil($dbItems['durability_max'][$equipmentKeys['hull']] / $dbItems['durability'][$equipmentKeys['hull']]);
					//if ($wear > 20) $wear = 20;
					$dbItems['durability'][$equipmentKeys['hull']] -= $wear;
					$timeLife['hull'] = floor($dbItems['durability'][$equipmentKeys['hull']] / $wear);
					if ($dbItems['durability'][$equipmentKeys['hull']] < 0) $vcseGameOver = true;
					db_work("UPDATE `players_items` SET `durability`=".$dbItems['durability'][$equipmentKeys['hull']]." WHERE `pid`=".$dbConfig['hull']);
					// engine
					$wear = ceil(10 * ($equipmentWeight / $dbItems['tech'][$equipmentKeys['engine']]));
					$wear += ceil($dbItems['durability_max'][$equipmentKeys['engine']] / $dbItems['durability'][$equipmentKeys['engine']]);
					//if ($wear > 20) $wear = 20;
					$dbItems['durability'][$equipmentKeys['engine']] -= $wear;
					$timeLife['engine'] = floor($dbItems['durability'][$equipmentKeys['engine']] / $wear);
					if ($dbItems['durability'][$equipmentKeys['engine']] < 0) $vcseGameOver = true;
					db_work("UPDATE `players_items` SET `durability`=".$dbItems['durability'][$equipmentKeys['engine']]." WHERE `pid`=".$dbConfig['engine']);
					// hoarder
					$wear = ceil(10 * ($dbConfig['energy'] / $dbItems['tech'][$equipmentKeys['hoarder']]));
					$wear += ceil($dbItems['durability_max'][$equipmentKeys['hoarder']] / $dbItems['durability'][$equipmentKeys['hoarder']]);
					//if ($wear > 20) $wear = 20;
					$dbItems['durability'][$equipmentKeys['hoarder']] -= $wear;
					$timeLife['hoarder'] = floor($dbItems['durability'][$equipmentKeys['hoarder']] / $wear);
					if ($dbItems['durability'][$equipmentKeys['hoarder']] < 0) $vcseGameOver = true;
					db_work("UPDATE `players_items` SET `durability`=".$dbItems['durability'][$equipmentKeys['hoarder']]." WHERE `pid`=".$dbConfig['hoarder']);
					// other
					$passengersNoPlaceQuantity = $passengersQuantity;
					foreach ($dbItems['id'] as $k => $v) {
						// case
						if ($dbItems['type'][$k] == 'case') {
							$dbItems['durability'][$k] -= $needEnergy;
							db_work("UPDATE `players_items` SET `durability`=".$dbItems['durability'][$k]." WHERE `pid`=".$dbItems['pid'][$k]);
							if ($dbItems['durability'][$k] < 0) {
								unset($dbItems['id'][$k]);
								db_work("DELETE FROM `players_items` WHERE `pid`=".$dbItems['pid'][$k]);
							}
						}
						// cabin
						if ($dbItems['type'][$k] == 'cabin' && $passengersNoPlaceQuantity) {
							$needPlaces = 0;
							if ($passengersNoPlaceQuantity >= $dbItems['tech'][$k]) {
								$needPlaces = $dbItems['tech'][$k];
							} else {
								$needPlaces = $passengersNoPlaceQuantity;
							}
							$dbItems['durability'][$k] -= ($needPlaces * 4) / $dbItems['tech'][$k];
							$passengersNoPlaceQuantity -= $needPlaces;
							db_work("UPDATE `players_items` SET `durability`=".$dbItems['durability'][$k]." WHERE `pid`=".$dbItems['pid'][$k]);
							if ($dbItems['durability'][$k] < 0) {
								unset($dbItems['id'][$k]);
								db_work("DELETE FROM `players_items` WHERE `pid`=".$dbItems['pid'][$k]);
							}
						}
						// гонщик
						/* if ($dbItems['type'][$k] == 'passenger' && $dbItems['subtype'][$k] == 'racer') {
							$dbItems['durability'][$k] -= $needEnergy;
							if ($dbItems['durability'][$k] < 0) {
								$dbItems['durability'][$k] = 0;
								db_work("UPDATE `players_items` SET `price`=0 WHERE `pid`=".$dbItems['pid'][$k]);
							}
							db_work("UPDATE `players_items` SET `durability`=".$dbItems['durability'][$k]." WHERE `pid`=".$dbItems['pid'][$k]);
						} */
					}
					// если не все пассажиры размещены, штраф 500 за каждого
					$dbConfig['energy'] -= $passengersNoPlaceQuantity * 500;
					
					// research
					// up steps
					if (!isset($dbConfig['step1'])) { // verify db vars
						$dbConfig['step4'] = 0;
						$dbConfig['step3'] = 0;
						$dbConfig['step2'] = 0;
						$dbConfig['step1'] = 0;
						$dbConfig['research'] = 0;
						db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('step4', '0', '".$_GET['player']."')");
						db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('step3', '0', '".$_GET['player']."')");
						db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('step2', '0', '".$_GET['player']."')");
						db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('step1', '0', '".$_GET['player']."')");
						db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('research', '0', '".$_GET['player']."')");
					}
					$dbConfig['step4'] = $dbConfig['step3'];
					$dbConfig['step3'] = $dbConfig['step2'];
					$dbConfig['step2'] = $dbConfig['step1'];
					$dbConfig['step1'] = $needEnergy;
					db_work("UPDATE `players_vars` SET `value`=".$dbConfig['step4']." WHERE `name`='step4' AND `player`='".$_GET['player']."'");
					db_work("UPDATE `players_vars` SET `value`=".$dbConfig['step3']." WHERE `name`='step3' AND `player`='".$_GET['player']."'");
					db_work("UPDATE `players_vars` SET `value`=".$dbConfig['step2']." WHERE `name`='step2' AND `player`='".$_GET['player']."'");
					db_work("UPDATE `players_vars` SET `value`=".$dbConfig['step1']." WHERE `name`='step1' AND `player`='".$_GET['player']."'");
					// calc energy for research
					$energyForResearch = 0;
					// v.1
					/* if ($dbConfig['step4']
						&& $dbConfig['step4'] == $dbConfig['step3']
						&& $dbConfig['step4'] == $dbConfig['step2']
					) {
						$energyForResearch = $dbConfig['step4'] * $dbConfig['step4'];
						if ($dbConfig['step4'] == $dbConfig['step1']) {
							$energyForResearch = $energyForResearch * $dbConfig['step4'];
							if ($dbConfig['step4'] < 4) $energyForResearch += 8;
						} else {
							if ($dbConfig['step4'] < 4) $energyForResearch += 6;
						}
					} */
					// v.2
					if ($dbConfig['step3'] == $dbConfig['step2']
						&& $dbConfig['step3'] == $dbConfig['step1']
					) {
						$energyForResearch = $dbConfig['step3'] * $dbConfig['step3'];
						if ($dbConfig['step3'] == $dbConfig['step4']) {
							$energyForResearch = $energyForResearch * $dbConfig['step3'] - ($dbConfig['step3'] * $dbConfig['step3']);
							if ($dbConfig['step3'] < 4) $energyForResearch += 2;
						} else {
							if ($dbConfig['step3'] < 4) $energyForResearch += 6;
						}
					}
					// calc
					$dbConfig['research'] += $energyForResearch;
					
					// set state - fly
					$dbConfig['state'] = "fly";
				// land
				} elseif ($_GET['state'] == "land") {
					// set state - land
					$dbConfig['state'] = "land";
					
					// calc energy and drop research
					$dbConfig['energy'] += $dbConfig['research'];
					$dbConfig['research'] = 0;
				}
				// up state
				db_work("UPDATE `players_vars` SET `value`='".$dbConfig['state']."' WHERE `name`='state' AND `player`='".$_GET['player']."'");

				// calc tensity
				$dbConfig['tensity'] = $dbConfig['tensity'] + $dbConfig['tensity_direct'];
				if ($dbConfig['tensity'] > 8 || $dbConfig['tensity'] < 1) $dbConfig['tensity_direct'] = $dbConfig['tensity_direct'] * -1;
				
				// up tensity
				db_work("UPDATE `players_vars` SET `value`=".$dbConfig['tensity']." WHERE `name`='tensity' AND `player`='".$_GET['player']."'");
				db_work("UPDATE `players_vars` SET `value`=".$dbConfig['tensity_direct']." WHERE `name`='tensity_direct' AND `player`='".$_GET['player']."'");
				
				// up energy and research
				if ($dbConfig['energy']<0) $vcseGameOver = true;
				db_work("UPDATE `players_vars` SET `value`=".$dbConfig['energy']." WHERE `name`='energy' AND `player`='".$_GET['player']."'");
				db_work("UPDATE `players_vars` SET `value`=".$dbConfig['research']." WHERE `name`='research' AND `player`='".$_GET['player']."'");
			} else {
				// скорость для режима создания
				$shipSpeed = 500;
			}

			require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/panel.php');
			
			// JS
			echo "#js#";
			
			// other ships
			if ($dbPlayers) {
				$shipsHtml = '';
				$shipsJs = "var plPos = '';\r\n";
				foreach ($dbPlayers as $plName => $plArr) {
					// update html
					$shipsHtml .= '<img id="player-'.$plName.'" src="/space-researcher/img/hull-'.$plArr['ship_type'].'.png" alt="'.$plName.' ('.$plArr['energy'].')" title="'.$plName.' ('.$plArr['energy'].')" />';
				
					// get coord
					$curX = vcseGetPosX($plArr['x'], $plArr['y']);
					$curY = vcseGetPosY($plArr['y']);
					
					// set coord
					// v1.0
					/* $shipsJs .= 						
						"$('#player-".$plName."').css('left', '".$curX."px');\r\n".
						"$('#player-".$plName."').css('top', '".$curY."px');\r\n"
					; */
					
					// v1.1 // bag при регистрации новых пользователей после запуска space.php
					/* $shipsJs .= 
						"if (plOldLeft".$plName." == 0 || plOldTop".$plName." == 0) {\r\n".
							"plOldLeft".$plName." = ".$curX.";\r\n".
							"plOldTop".$plName." = ".$curY.";\r\n".
							"$('#player-".$plName."').css('left', '".$curX."px');\r\n".
							"$('#player-".$plName."').css('top', '".$curY."px');\r\n".
						"} else {\r\n".
							"$('#player-".$plName."').css('left', plOldLeft".$plName."+'px');\r\n".
							"$('#player-".$plName."').css('top', plOldTop".$plName."+'px');\r\n".
							"$('#player-".$plName."').animate({'left': '".$curX."px', 'top': '".$curY."px'}, 'normal');\r\n".
							"plOldLeft".$plName." = ".$curX.";\r\n".
							"plOldTop".$plName." = ".$curY.";\r\n".
						"}\r\n"
					; */
					
					// v1.2
					if (!isset($dbConfig['player_old_x_'.$plName]) || !isset($dbConfig['player_old_y_'.$plName])) {
						$shipsJs .= 
							"$('#player-".$plName."').css('position', 'absolute');\r\n".
							"$('#player-".$plName."').css('z-index', '12');\r\n".
							"$('#player-".$plName."').css('left', '".$curX."px');\r\n".
							"$('#player-".$plName."').css('top', '".$curY."px');\r\n".
							"$('#player-".$plName."').css('visibility', '".($plArr['state']=="land" ? "hidden" : "visible")."');\r\n"
						;
						db_work("INSERT INTO `players_vars` (`value`, `name`, `player`) VALUES (".$curX.", 'player_old_x_".$plName."', '".$_GET['player']."')");
						db_work("INSERT INTO `players_vars` (`value`, `name`, `player`) VALUES (".$curY.", 'player_old_y_".$plName."', '".$_GET['player']."')");
					} else {
						$shipsJs .= 
							"$('#player-".$plName."').css('position', 'absolute');\r\n".
							"$('#player-".$plName."').css('z-index', '12');\r\n".
							"$('#player-".$plName."').css('left', '".$dbConfig['player_old_x_'.$plName]."px');\r\n".
							"$('#player-".$plName."').css('top', '".$dbConfig['player_old_y_'.$plName]."px');\r\n".
							"$('#player-".$plName."').animate({'left': '".$curX."px', 'top': '".$curY."px'}, 'normal');\r\n".
							"$('#player-".$plName."').css('visibility', '".($plArr['state']=="land" ? "hidden" : "visible")."');\r\n"
						;
						db_work("UPDATE `players_vars` SET `value`=".$curX." WHERE `name`='player_old_x_".$plName."' AND `player`='".$_GET['player']."'");
						db_work("UPDATE `players_vars` SET `value`=".$curY." WHERE `name`='player_old_y_".$plName."' AND `player`='".$_GET['player']."'");
					}
					
					// rotate
					$shipsJs .= "$('#player-".$plName."').rotate(".$plArr['angle'].");\r\n";
				}
				echo "$('#ships').html('".$shipsHtml."');\r\n".$shipsJs;
			}
			
			// my ship
			echo "$('#myship').rotate(".$dbConfig['angle'].");\r\n";
			echo "var correctShipPosition = ".($correctShipPosition?"true":"false").";\r\n";
			echo "var shipSpeed = ".$shipSpeed.";\r\n";
			
			// game over
			if ($vcseGameOver) {
				echo "var vcseGameOver = true;\r\n";
				vcseDeletePlayer($_GET['player']);
			}
			
			//echo "$('#panel').html(\"".$db_log."\");"; // debug
		}
	}
}
?>