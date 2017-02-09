<?php
defined('VCEXE') or die;

if ($dbObj) {
	foreach ($dbObj['id'] as $k => $v) {
		// get coord
		$curX = vcseGetPosX($dbObj['x'][$k], $dbObj['y'][$k]);
		$curY = vcseGetPosY($dbObj['y'][$k]);
		// show
		echo
			'<img id="o-'.$dbObj['x'][$k].'-'.$dbObj['y'][$k].'" src="/space-researcher/img/'.$dbObj['type'][$k].'-'.$dbObj['subtype'][$k].'.png"'.
			' '.($dbObj['type'][$k]=='planet' && $dbConfig['creation']=='0' ? 'class="planet" onclick="vcseGoTo(\'s-'.$dbObj['x'][$k].'-'.$dbObj['y'][$k].'\','.$dbObj['x'][$k].', '.$dbObj['y'][$k].', \'land\', \'0\');"' : '').
			' alt="'.$dbObj['object'][$k].'"'.
			' title="'.$dbObj['object'][$k].'"'.
			' style="position:absolute; z-index:11; top:'.$curY.'px; left:'.$curX.'px;"'.
			' />'
		;
	}
}
?>