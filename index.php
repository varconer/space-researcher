<?php
define('VCEXE', 1);

require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/header.php');
?>
<p><form action="space.php" method="post">
<strong><big>Enter</big></strong><br/>
Login:<br/>
<input name="player" type="text" value=""/><br/>
Password:<br/>
<input name="pass" type="password" value=""/><br/>
<input type="submit" value="Connect">
</form></p>
<p><button onclick="javascript:location.href='register.php'">Register</button></p>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/footer.php');
?>