<?php
define('VCEXE', 1);

header("Content-type: text/html; charset=utf-8");

if (isset($_GET['player']) && isset($_GET['pass']) && isset($_GET['toX']) && isset($_GET['toY']) && isset($_GET['objType']) && isset($_GET['objSubType']) && isset($_GET['objName'])) {
	// verify pass & load vars
	$vcseAuth = false;
	require($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/config.php');
	if ($vcseAuth && !$dbCurObj) {
		// create
		db_work("INSERT INTO `objects` (`object`, `type`, `subtype`, `x`, `y`, `map`) VALUES ('".$_GET['objName']."', '".$_GET['objType']."', '".$_GET['objSubType']."', ".$_GET['toX'].", ".$_GET['toY'].", '".$dbConfig['map']."')");
		
		// set next name
		if ($_GET['objType']=='planet') {
			$dbConfig['creation_planet_name']++;
			db_work("UPDATE `players_vars` SET `value`='".$dbConfig['creation_planet_name']."' WHERE `name`='creation_planet_name' AND `player`='".$_GET['player']."'");
		} else {
			$dbConfig['creation_star_name']++;
			db_work("UPDATE `players_vars` SET `value`='".$dbConfig['creation_star_name']."' WHERE `name`='creation_star_name' AND `player`='".$_GET['player']."'");
		}

		require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/panel.php');
		
		// js
		echo "#js#";
		
		// add obj
		echo "$('#objects').append('<img id=\"o-".$_GET['toX']."-".$_GET['toY']."\" src=\"/space-researcher/img/".$_GET['objType']."-".$_GET['objSubType'].".png\" alt=\"".$_GET['objName']."\" title=\"".$_GET['objName']."\" />');\r\n";
		// get coord
		$curX = vcseGetPosX($_GET['toX'], $_GET['toY']);
		$curY = vcseGetPosY($_GET['toY']);
		// set coord
		echo "$('#o-".$_GET['toX']."-".$_GET['toY']."').css({'position':'absolute','z-index':'11','left':'".$curX."px','top':'".$curY."px'});\r\n";
		// debug
		//echo "alert('ok');\r\n";
	}
}
?>