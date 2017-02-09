<?php
defined('VCEXE') or die;

if ($dbRadiation) {
	for ($line = 1; $line <= $vcseLineQuan; $line++) {
		for ($col = 1-round(($line-1)/2); $col < $vcseColQuan+1-round(($line-1)/2); $col++) {
			// get coord
			$curX = vcseGetPosX($col, $line);
			$curY = vcseGetPosY($line);
			// map v1.0
			$mapSector = $dbRadiation[$line];
			// map v2.0
			if (isset($dbRadiation2)) {
				$mapSector = $mapSector + $dbRadiation2[$col] + (substr($dbConfig['time'], -4, 1) * substr($dbConfig['time'], -3, 1));
				$mapSector = substr($mapSector, -1);
			}
			// show
			echo
				'<img class="sector" id="s-'.$col.'-'.$line.'" src="/space-researcher/img/'.$mapSector.'.png" onclick="vcseGoTo(\'s-'.$col.'-'.$line.'\','.$col.', '.$line.', \'fly\', \''.$dbConfig['creation'].'\');"'.
				' style="position:absolute; z-index:10; top:'.$curY.'px; left:'.$curX.'px;"'.
				//' title="('.$col.' '.$line.')"'.
				' />'
			;
		}
	}
}
?>