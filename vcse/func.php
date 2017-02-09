<?php
defined('VCEXE') or die;

function vcseGetPosX ($x, $y) {
	global $vcseSpaceAbsPosX, $vcseSectorMarginXForNextLine, $vcseSectorSizeX, $vcweSectorWidthTopSide;
	return $vcseSpaceAbsPosX+($y-1)*$vcseSectorMarginXForNextLine+($x-1)*($vcseSectorSizeX+$vcweSectorWidthTopSide);
}

function vcseGetPosY ($y) {
	global $vcseSpaceAbsPosY, $vcseSectorMarginYForNextLine;
	return $vcseSpaceAbsPosY+($y-1)*($vcseSectorMarginYForNextLine);
}

function vcseGetObjectNewName($creationObjNames, $objNn) {
	$countObjNames = count($creationObjNames);
	// set obj name
	$creationObjName = "";
	if ($objNn+1 > $countObjNames) {
		$objKey = $objNn+1 - (floor(($objNn+1) / $countObjNames) * $countObjNames);
		$creationObjName = $creationObjNames[$objKey];
	} else {
		$creationObjName = $creationObjNames[$objNn];
	}
	// add obj num
	if ($objNn+1 > $countObjNames || substr($creationObjName, -1) == "-") {
		$creationObjName .= (substr($creationObjName, -1) == "-"?"":"-").ceil(($objNn+1) / $countObjNames);
	}
	return $creationObjName;
}

function vcseDeletePlayer($player) {
	db_work("DELETE FROM `players` WHERE `player`='".$player."'");
	db_work("DELETE FROM `players_items` WHERE `player`='".$player."'");
	db_work("DELETE FROM `players_vars` WHERE `player`='".$player."'");
}
?>