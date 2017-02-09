<?php
$vcseBrowser = "unknow";

if (strpos($_SERVER['HTTP_USER_AGENT'], "Gecko") !== false 
	//&& strpos($_SERVER['HTTP_USER_AGENT'], "Chrome") === false 
) {
	$vcseBrowser = "Firefox";
}

if (strpos($_SERVER['HTTP_USER_AGENT'], "Opera") !== false) $vcseBrowser = "Opera";

if (strpos($_SERVER['HTTP_USER_AGENT'], "Chrome") !== false) $vcseBrowser = "Chrome";

if (strpos($_SERVER['HTTP_USER_AGENT'], "IE") !== false) $vcseBrowser = "IE";
?>