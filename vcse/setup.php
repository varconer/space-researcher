<?php
defined('VCEXE') or die;

if (!db_work("SELECT `id` FROM `items` LIMIT 0, 1")) {
	if ($sql = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/space-researcher/spaceresearcher.sql')) {
		$sqlArr = explode(";", $sql);
		foreach ($sqlArr as $q) db_work(trim($q));
	}
}
?>