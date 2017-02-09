<?php
// space sectors
echo '<div id="sectors">';
	require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/sectors.php');
echo '</div>';
// objects
echo '<div id="objects">';
	require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/objects.php');
echo '</div>';
// other ships
echo '<div id="ships">';
if ($dbPlayers) {
	foreach ($dbPlayers as $plName => $plArr) {
		// get coord
		$curX = vcseGetPosX($plArr['x'], $plArr['y']);
		$curY = vcseGetPosY($plArr['y']);
	
		echo 
			'<img id="player-'.$plName.'" '.
			'style="position:absolute; z-index:12; top:'.$curY.'px; left:'.$curX.'px; visibility:'.($plArr['state']=="land" ? "hidden" : "visible").';" '.
			'src="/space-researcher/img/hull-'.$plArr['ship_type'].'.png" alt="'.$plName.' ('.$plArr['energy'].')" title="'.$plName.' ('.$plArr['energy'].')" />'
		;
	}
}
echo '</div>';
// my ship
echo '<img id="myshipmark" style="position:absolute; z-index:14; top:'.vcseGetPosY($dbConfig['y']).'px; left:'.vcseGetPosX($dbConfig['x'], $dbConfig['y']).'px;" src="/space-researcher/img/ship-mark.png" />';
echo '<img id="myship" style="position:absolute; z-index:15; top:'.vcseGetPosY($dbConfig['y']).'px; left:'.vcseGetPosX($dbConfig['x'], $dbConfig['y']).'px; cursor:pointer;" src="/space-researcher/img/hull-'.$dbConfig['ship_type'].'.png" '.($dbConfig['creation']=='0'?'onclick="vcseChangeMode();"':'onclick="vcseEndCreation();"').' alt="'.($dbConfig['creation']=='0'?$player:'end creation').'" title="'.($dbConfig['creation']=='0'?$player:'end creation').'" />';
// target marker
echo '<img id="target" style="position:fixed; z-index:16; top:0px; left:0px; display:none;" src="/space-researcher/img/target.png" />';
// panel
echo '<div id="panel">';
	require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/panel.php');
echo '</div>';
?>