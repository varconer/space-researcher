<?php
define('VCEXE', 1);

header("Content-type: text/html; charset=utf-8");

if (isset($_GET['player']) && isset($_GET['pass']) && isset($_GET['reload_map'])) {
	// verify pass & load vars
	$vcseAuth = false;
	require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/config.php');
	if ($vcseAuth) {
		require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/sectors.php');
	}	
}
?>