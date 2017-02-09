<?php
defined('VCEXE') or die;

// panel ship
// verify db vars
if (!isset($dbConfig['panel'])) {
	$dbConfig['panel'] = 'show';
	db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('panel', '".$dbConfig['panel']."', '".$_REQUEST['player']."')");
}
// hide
if ($dbConfig['panel']=='hide') {
	// slider
	echo "<div id='panel_slide' onclick='vcsePanelSlide(\"panel\", \"\", 0);' alt='".$vcseText[$vcseLang]['show_panel']."' title='".$vcseText[$vcseLang]['show_panel']."'>";
	echo "</div>";
// show
} elseif ($dbConfig['panel']=='show') {
	// name
	echo "<div id='panel_name'>";
		echo "<div onclick='vcsePanelSlide(\"panel\", \"\", 0);' alt='".$vcseText[$vcseLang]['hide_panel']."' title='".$vcseText[$vcseLang]['hide_panel']."'>";
			echo "<div id='obj_type'>".$vcseText[$vcseLang]['astronaut']."</div>";
			echo "<div id='obj_name'>".$_REQUEST['player']."</div>";
		echo "</div>";
		echo "<div id='obj_img'><img onclick='vcseShipToCenter();' alt='".$vcseText[$vcseLang]['search_my_ship']."' title='".$vcseText[$vcseLang]['search_my_ship']."' id='myship_panel' src='/space-researcher/img/hull-".$dbConfig['ship_type'].".png' /></div>";
	echo "</div>";
	
	// inventory
	if ($dbItems) {
		$dbItemsCount = count($dbItems['id']);
		$i=0;
		if ($dbItemsCount > 8) {
			// верхний слайдер
			echo "<div id='panel_store_slider_up' onclick='vcseStoreSlideUp(\"panel_store_group_content\");' /></div>";
			// начало группы панелей товаров
			echo "<div id='panel_store_group'><div id='panel_store_group_content'".(isset($dbConfig['panel_store_group_content'])?" style='top:-".($dbConfig['panel_store_group_content'] * 65)."px;'":"").">";
		}
		// цикл отображения панелей с товарами
		foreach ($dbItems['id'] as $k => $v) {
			$i++;
			if ($i>4) $i=1;
			if ($i==1) echo "<div id='panel_store'>";
				//echo "<div id='store_cell_".$i."'>";
				// alt
				$alt = $dbItems['item'][$k]."\r\n".$dbItems['price'][$k]."e".($dbItems['type'][$k]=="case"?"\r\nx".$dbItems['quantity'][$k]:"");
				// bar
				$bar = round(52 * ($dbItems['durability'][$k] / $dbItems['durability_max'][$k]));
				$barWarning = false;
				if (in_array($k, $equipmentKeys, true) 
					&& isset($timeLife) 
					&& isset($timeLife[$dbItems['type'][$k]])
					&& $timeLife[$dbItems['type'][$k]] <= 10
				) {
					$barWarning = true;
				}
				echo "<img id='store_cell_".$i."_bar' src='/space-researcher/img/bar".($barWarning?"-warning":"").".png' width='".($dbItems['type'][$k]=="permit"?52:$bar)."' height='4' />";
				// flag - set
				if (in_array($k, $equipmentKeys, true)) {
					echo "<img class='item' id='store_cell_".$i."_flag' src='/space-researcher/img/set.png' onclick='vcsePanelSlide(\"panel_item\", \"pid\", ".$dbItems['pid'][$k].");' alt='".$alt."' title='".$alt."' />";
					if ($barWarning) echo "<img class='item' id='store_cell_".$i."_flag_warning' src='/space-researcher/img/set-warning.gif' />";
				}
				// item
				echo "<img class='item' id='store_cell_".$i."' src='/space-researcher/img/".$dbItems['type'][$k]."-".$dbItems['subtype'][$k].".png' onclick='vcsePanelSlide(\"panel_item\", \"pid\", ".$dbItems['pid'][$k].");' alt='".$alt."' title='".$alt."' />";
				//echo "</div>";
			if ($i==4) echo "</div>";
		}
		if ($i!=4) echo "</div>";
		if ($dbItemsCount > 8) {
			// конец группы панелей товаров
			echo "</div></div>";
			// нижний слайдер
			echo "<div id='panel_store_slider_down' onclick='vcseStoreSlideDown(\"panel_store_group_content\", ".($dbItemsCount + 4).");' /></div>";
		}
	}
	
	// objects for creation
	if ($dbConfig['creation']=='1' && $creationObjects) {
		// echo objects
		$i=0;
		foreach ($creationObjects['type'] as $k => $v) {
			$i++;
			if ($i>4) $i=1;
			if ($i==1) echo "<div id='panel_store'>";
				//echo "<div id='store_cell_".$i."'>";
				echo 
					"<img class='item'".
					" id='store_cell_".$i."'".
					" src='/space-researcher/img/".$creationObjects['type'][$k]."-".$creationObjects['subtype'][$k].".png'".
					" onclick='vcseCreate(".$dbConfig['x'].", ".$dbConfig['y'].", \"".$creationObjects['type'][$k]."\", \"".$creationObjects['subtype'][$k]."\", \"".($creationObjects['type'][$k]=='planet'?$creationPlanetName:$creationStarName)."\")'".
					" alt='".$vcseText[$vcseLang]['create']." ".($creationObjects['type'][$k]=='planet'?$vcseText[$vcseLang]['planet2']." ".$creationPlanetName:$vcseText[$vcseLang]['star']." ".$creationStarName)."'".
					" title='".$vcseText[$vcseLang]['create']." ".($creationObjects['type'][$k]=='planet'?$vcseText[$vcseLang]['planet2']." ".$creationPlanetName:$vcseText[$vcseLang]['star']." ".$creationStarName)."' />"
				;
				//echo "</div>";
			if ($i==4) echo "</div>";
		}
		if ($i!=4) echo "</div>";
	}
}

// panel object
if (isset($dbConfig['state']) && $dbConfig['state']=='land') {
	// verify db vars
	if (!isset($dbConfig['panel_object'])) {
		$dbConfig['panel_object'] = 'show';
		db_work("INSERT INTO `players_vars` (`name`, `value`, `player`) VALUES ('panel_object', '".$dbConfig['panel_object']."', '".$_REQUEST['player']."')");
	}
	// hide
	if ($dbConfig['panel_object']=='hide') {
		// slider
		echo "<div id='panel_slide' onclick='vcsePanelSlide(\"panel_object\", \"\", 0);' alt='".$vcseText[$vcseLang]['show_panel']."' title='".$vcseText[$vcseLang]['show_panel']."'>";
		echo "</div>";
	// show
	} elseif ($dbConfig['panel_object']=='show') {
		// obj name
		echo "<div id='panel_name' onclick='vcsePanelSlide(\"panel_object\", \"\", 0);' alt='".$vcseText[$vcseLang]['hide_panel']."' title='".$vcseText[$vcseLang]['hide_panel']."'>";
			echo "<div id='obj_type'>".($dbCurObj['subtype'][0]=='station'?$vcseText[$vcseLang][$dbCurObj['subtype'][0]]:$vcseText[$vcseLang][$dbCurObj['type'][0]])."</div>";
			echo "<div id='obj_name'>".$dbCurObj['object'][0]."</div>";
			echo "<div id='obj_img'><img src='/space-researcher/img/".$dbCurObj['type'][0]."-".$dbCurObj['subtype'][0].".png' /></div>";
		echo "</div>";
		
		// store
		if (isset($dbCurObjItems) && $dbCurObjItems) {
			$dbCurObjItemsCount = count($dbCurObjItems['id']);
			$i=0;
			if ($dbCurObjItemsCount > 12) {
				// верхний слайдер
				echo "<div id='panel_store_slider_up' onclick='vcseStoreSlideUp(\"panel_store_group_content_2\");' /></div>";
				// начало группы панелей товаров
				echo "<div id='panel_store_group_2'><div id='panel_store_group_content_2'".(isset($dbConfig['panel_store_group_content_2'])?" style='top:-".($dbConfig['panel_store_group_content_2'] * 65)."px;'":"").">";
			}
			// цикл отображения панелей с товарами
			foreach ($dbCurObjItems['id'] as $k => $v) {
				// товары
				$i++;
				if ($i>4) $i=1;
				if ($i==1) echo "<div id='panel_store'>";
					//echo "<div id='store_cell_".$i."'>";
					// alt
					$alt = $dbCurObjItems['item'][$k]."\r\n".$dbCurObjItems['price'][$k]."e".($dbCurObjItems['type'][$k]=="case"?"\r\nx".$dbCurObjItems['quantity'][$k]:"");
					// bar
					$bar = round(52 * ($dbCurObjItems['durability'][$k] / $dbCurObjItems['durability_max'][$k]));
					echo "<img id='store_cell_".$i."_bar' src='/space-researcher/img/bar.png' width='".($dbCurObjItems['type'][$k]=="permit"?52:$bar)."' height='4' />";
					// item
					echo "<img class='item' id='store_cell_".$i."' src='/space-researcher/img/".$dbCurObjItems['type'][$k]."-".$dbCurObjItems['subtype'][$k].".png' onclick='vcsePanelSlide(\"panel_item\", \"oid\", ".$dbCurObjItems['oid'][$k].");' alt='".$alt."' title='".$alt."' />";
					//echo "</div>";
				if ($i==4) echo "</div>";
			}
			if ($i!=4) echo "</div>";
			if ($dbCurObjItemsCount > 12) {
				// конец группы панелей товаров
				echo "</div></div>";
				// нижний слайдер
				echo "<div id='panel_store_slider_down' onclick='vcseStoreSlideDown(\"panel_store_group_content_2\", ".$dbCurObjItemsCount.");' /></div>";
			}
		}
	}
}

// panel current item
// show
if (isset($dbCurItem)) {
	echo "<div id='panel_item'>";
		// debug
		/* echo $_REQUEST['item_id_type']."<br/>";
		echo $_REQUEST['item_id']."<br/>"; */
		//print_r($dbCurItem);
		//print_r($dbItems['pid']);
		//echo $itemKey;
		//print_r($equipmentKeys);
		//echo $equipmentWeight.'<br>';
		
		// flag - installed item
		$dbCurItemInstalled = false;
		if ($_REQUEST['item_id_type']=='pid' && $dbCurItem['pid']==$dbItems['pid'][$equipmentKeys[$dbCurItem['type']]]) $dbCurItemInstalled = true;
		
		// echo
		// img
		echo "<img src='/space-researcher/img/".$dbCurItem['type']."-".$dbCurItem['subtype'].".png' align='left' />";
		// type
		echo $vcseText[$vcseLang][$dbCurItem['type']]."<br/>";
		// name
		echo $dbCurItem['item']."<br/>";
		// price
		echo ($dbCurItem['type']=='passenger'?$vcseText[$vcseLang]['fare']:$vcseText[$vcseLang]['price']).": ".$dbCurItem['price']."<br/>";
		if ($_REQUEST['item_id_type']=='oid' && $dbCurItem['type'].'-'.$dbCurItem['subtype'] == 'passenger-racer') {
			// если на гонщик планете - показать условия стоимости оборудования
			echo $vcseText[$vcseLang]['need_equip_cost'].":<br/>".$equipmentCost."(".((10000 * floor($dbCurItem['price'] / 10000)) - 10000)."-".(50000 * floor($dbCurItem['price'] / 10000)).")<br/>";
			//echo $vcseText[$vcseLang]['need_equip_cost'].": ".$dbItems['price'][$equipmentKeys['hull']]."+".$dbItems['price'][$equipmentKeys['engine']]."+".$dbItems['price'][$equipmentKeys['hoarder']]."(".(10000 * floor($dbCurItem['price'] / 10000))."-".(50000 * floor($dbCurItem['price'] / 10000)).")<br/>";
		}
		// weight
		echo $vcseText[$vcseLang]['weight'].": ".$dbCurItem['weight']."<br/>";
		// capacity
		if ($dbCurItem['type']=='hull' && $dbCurItemInstalled) {
			echo "<font style='".($equipmentCapacity>$dbCurItem['capacity']?"color:red;":"")."'>".$vcseText[$vcseLang]['capacity_busy'].": ".$equipmentCapacity."(".$dbCurItem['capacity'].")</font><br/>";
		} else {
			echo $vcseText[$vcseLang]['capacity'].": ".$dbCurItem['capacity']."<br/>";
		}
		// durability
		if ($dbCurItem['type']=='permit') {
			if ($_REQUEST['item_id_type']=='pid') echo $vcseText[$vcseLang]['really_to'].": ".(date("d.m.Y", $dbCurItem['durability']))."<br/>";
		} else {
			echo $vcseText[$vcseLang]['state'].": ".$dbCurItem['durability']."(".$dbCurItem['durability_max'].")<br/>";
		}
		// tech
		if ($dbCurItem['type']=='engine') {
			if ($dbCurItemInstalled) {
				echo $vcseText[$vcseLang]['need_power'].": ".$equipmentWeight."(".$dbCurItem['tech'].")<br/>";
			} else {
				echo $vcseText[$vcseLang]['power'].": ".$dbCurItem['tech']."<br/>";
			}
		}
		if ($dbCurItem['type']=='hoarder') echo $vcseText[$vcseLang]['bulk'].": ".$dbCurItem['tech']."<br/>";
		if ($dbCurItem['type']=='hangar') echo $vcseText[$vcseLang]['hangar_capacity'].": ".$dbCurItem['tech']."<br/>";
		if ($dbCurItem['type']=='passenger' || $dbCurItem['type']=='permit') {
			$objKey = array_search($dbCurItem['tech'], $dbObj['id']);
			if ($objKey !== false) { 
				echo ($dbCurItem['type']=='passenger'?$vcseText[$vcseLang]['destination']:$vcseText[$vcseLang]['colonization']).": ".$dbObj['object'][$objKey]."(".$dbObj['map'][$objKey].")</br>";
			} elseif ($dbTargetObj = db_work("SELECT `object`, `map` FROM `objects` WHERE `id`=".$dbCurItem['tech'])) {
				echo ($dbCurItem['type']=='passenger'?$vcseText[$vcseLang]['destination']:$vcseText[$vcseLang]['colonization']).": ".$dbTargetObj['object'][0]."(".$dbTargetObj['map'][0].")</br>";
			}
		}
		// tech 2
		if ($dbCurItem['type']=='message' && $dbCurItem['subtype']=='race') {
			// получить общую таблицу результатов гонки
			$dbRaceResult = db_work("SELECT * FROM `racers` WHERE `race_oid`=".$dbCurItem['tech2']." ORDER BY `time`");
			if ($dbRaceResult) {
				// получить инфо о старте
				$startRaceObjKey = array_search($dbRaceResult['start_obj'][0], $dbObj['id']);
				echo $vcseText[$vcseLang]['system'].": ".$dbObj['map'][$startRaceObjKey]."<br/>";
				echo $vcseText[$vcseLang]['start_race'].": ".$dbObj['object'][$startRaceObjKey]."<br/>";
				// получить инфо о финише
				$finishRaceObjKey = array_search($dbRaceResult['finish_obj'][0], $dbObj['id']);
				echo $vcseText[$vcseLang]['finish_race'].": ".$dbObj['object'][$finishRaceObjKey]."<br/>";
				// вывод таблицы
				foreach ($dbRaceResult['id'] as $kR => $vR) {
					// подготовка времени
					$raceTimeText = date("i:s", $dbRaceResult['time'][$kR]);
					$raceTimeMsText = round(100 * fmod($dbRaceResult['time'][$kR], 1));
					if ($raceTimeMsText < 10) $raceTimeMsText = '0'.$raceTimeMsText;
					// вывод
					echo ($kR+1).". ".$dbRaceResult['player'][$kR]." ".$raceTimeText.".".$raceTimeMsText;
					// инфо об оборудовании
					if ($dbRaceResult['equip'][$kR]) echo " (".$dbRaceResult['equip'][$kR].")";
					echo "<br/>";
					if ($kR > 8) break;
				}
			}
		}
		// quantity
		if ($dbCurItem['quantity']>1 || $dbCurItem['type']=='case') echo $vcseText[$vcseLang]['quantity'].": ".$dbCurItem['quantity']."<br/>";
		// store
		if ($dbCurObj) {
			if ($_REQUEST['item_id_type']=='pid' 
				&& !$dbCurItemInstalled 
				//&& (!$dbCurObjMyColony || $dbCurItem['type']!='permit') 
			) {
				// кнопка продать
				echo "<div class='store_button' onclick='vcseStore(\"sell\", \"pid\", \"".$dbCurItem['pid']."\", 1);'>".($dbCurItem['type']=='passenger' || ($dbCurObjMyColony && $dbCurItem['type']!='permit' && $dbCurItem['type']!='message')?$vcseText[$vcseLang]['to_land']:$vcseText[$vcseLang]['sell'])."</div>";
				// кнопка продать все
				if ($dbCurItem['type'] == 'case'
					&& $dbCurItem['quantity'] > 1
				) {
					echo "<div class='store_button' onclick='vcseStore(\"sell\", \"pid\", \"".$dbCurItem['pid']."\", ".$dbCurItem['quantity'].");'>".($dbCurObjMyColony?$vcseText[$vcseLang]['to_land_all']:$vcseText[$vcseLang]['sell_all'])."</div>";
				}
			}
			if ($_REQUEST['item_id_type']=='oid' 
				&& ($dbConfig['energy']>=$dbCurItem['price'] || ($dbCurObjMyColony && $dbCurItem['type']!='permit') || $dbCurItem['type'] == 'passenger')
				&& ($dbCurItem['type'] != 'passenger' || $cabinQuantity > $passengersQuantity) 
				&& ( !$dbCurObjFree || $dbCurObjMyColony || ($dbCurItem['type'] != 'factory' && $dbCurItem['type'] != 'hangar') )
				&& ($dbCurItem['type'].'-'.$dbCurItem['subtype'] != 'passenger-racer' || !isset($dbConfig['in_race']) || !$dbConfig['in_race'])
				&& ($dbCurItem['type'].'-'.$dbCurItem['subtype'] != 'passenger-racer' || ($equipmentCost > (10000 * floor($dbCurItem['price'] / 10000) - 10000) && $equipmentCost < 50000 * floor($dbCurItem['price'] / 10000)))
			) {
				// кнопка купить
				echo "<div class='store_button' onclick='vcseStore(\"buy\", \"oid\", \"".$dbCurItem['oid']."\", 1);'>".($dbCurItem['type']=='passenger' || ($dbCurObjMyColony && $dbCurItem['type']!='permit')?$vcseText[$vcseLang]['boarding']:$vcseText[$vcseLang]['buy'])."</div>";
				// подсчет максимального количества для покупки
				$buyMaxQuantity = floor(($dbItems['capacity'][$equipmentKeys['hull']] - $equipmentCapacity) / $dbCurItem['capacity']);
				if ($buyMaxQuantity > $dbCurItem['quantity']) $buyMaxQuantity = $dbCurItem['quantity'];
				if ($dbConfig['energy'] < $dbCurItem['price'] * $buyMaxQuantity && !$dbCurObjMyColony) $buyMaxQuantity = floor($dbConfig['energy'] / $dbCurItem['price']);
				// кнопка купить все
				if ($dbCurItem['type'] == 'case'
					&& $buyMaxQuantity > 1
					//&& ($dbConfig['energy'] >= $dbCurItem['price'] * $buyMaxQuantity || $dbCurObjMyColony) 
				) {
					echo "<div class='store_button' onclick='vcseStore(\"buy\", \"oid\", \"".$dbCurItem['oid']."\", ".$buyMaxQuantity.");'>".($dbCurObjMyColony?$vcseText[$vcseLang]['boarding_all']:$vcseText[$vcseLang]['buy_all'])."</div>";
				}
			}
		}
		// set
		if ((
				$dbCurItem['type']=='hull'
				|| $dbCurItem['type']=='engine'
				|| $dbCurItem['type']=='hoarder'
			) 
			&& $_REQUEST['item_id_type']=='pid' 
			&& !$dbCurItemInstalled
		) {
			echo "<div class='store_button' onclick='vcseSet(\"".$dbCurItem['type']."\", \"".$dbCurItem['subtype']."\", ".$dbCurItem['pid'].");'>".$vcseText[$vcseLang]['set']."</div>";
		}
		// jamp buttons
		if (!$dbCurObj && $dbCurItem['type']=='engine' && $dbCurItemInstalled && strpos($dbCurItem['item'], "Hyper")!==false) {
			if ($dbResMaps = db_work("SELECT `map` FROM `maps`")) {
				foreach ($dbResMaps['map'] as $mapName) {
					if ($mapName==$dbConfig['map']) continue;
					echo "<div class='store_button' onclick='vcseJamp(\"".$mapName."\");'>".$vcseText[$vcseLang]['jamp_to']." `".$mapName."`</div>";
				}
			}
		}
		// view passenger target
		if ($dbCurItem['type']=='passenger' || $dbCurItem['type']=='permit') {
			$dbResTarget = db_work("SELECT `x`, `y`, `map` FROM `objects` WHERE `id`='".$dbCurItem['tech']."'");
			if ($dbResTarget && $dbConfig['map']==$dbResTarget['map'][0]) {
				echo "<div class='store_button' onclick='vcseViewTarget(\"o-".$dbResTarget['x'][0]."-".$dbResTarget['y'][0]."\")'>".($dbCurItem['type']=='passenger'?$vcseText[$vcseLang]['view_passenger_target']:$vcseText[$vcseLang]['view_colony'])."</div>";
			}
		}
		// close
		echo "<div class='store_button' onclick='vcsePanelSlide(\"panel_item\", \"\", 0);'>".$vcseText[$vcseLang]['close']."</div>";
	echo "</div>";
}

// panel instruments
if ($dbConfig['creation']=='0') {
	echo "<div id='panel_instruments'>";
		echo "<div id='energy'>".$dbConfig['energy'].(isset($dbConfig['research'])?"<font id='energy_science'>/".$dbConfig['research']:"")."</font></div>";
		echo "<div id='tensity' onclick='vcseChangeMode();'>".$dbConfig['mode'].$dbConfig['tensity']."</div>";
		if (isset($dbConfig['step4'])) echo "<div id='step4'>".$dbConfig['step4']."</div>";
		if (isset($dbConfig['step3'])) echo "<div id='step3'>".$dbConfig['step3']."</div>";
		if (isset($dbConfig['step2'])) echo "<div id='step2'>".$dbConfig['step2']."</div>";
		if (isset($dbConfig['step1'])) echo "<div id='step1'>".$dbConfig['step1']."</div>";
	echo "</div>";
}

// debug
if (isset($aaa)) {
	echo "<div id='panel_item'>";
		echo $aaa;
	echo "</div>";
}
?>