<?php
define('VCEXE', 1);

require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/header.php');
$showForm = true;
if (isset($_POST['player']) && isset($_POST['pass']) && isset($_POST['pass2'])) {
	require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/db_conf.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcfunc/link.php');
	$showForm = false;
	// проверка на уникальность имени
	$dbResPlayer = db_work("SELECT `player` FROM `players` WHERE `player`='".$_POST['player']."'");
	if ($dbResPlayer) {
		echo "<p style='color:red'>The player with such name already is</p>"; // такой игрок уже есть
		$showForm = true;
	} elseif ($_POST['pass'] != $_POST['pass2']) {
		echo "<p style='color:red'>Passwords don't coincide</p>"; // пароли не совпадают
		$showForm = true;
	} else {
		// внести в БД инфо о пользователе
		db_work("INSERT INTO `players` (`player`, `pass`) VALUES ('".$_POST['player']."', '".md5($_POST['pass'])."')");
		?>
		<p>Registration is complete successfully</p>
		<p><form action="space.php" method="post">
		<strong><big>Select map</big></strong><br/>
		<input name="player" type="hidden" value="<?php echo $_POST['player']; ?>"/>
		<input name="pass" type="hidden" value="<?php echo $_POST['pass']; ?>"/>
		<select id="map_select" size="1" name="map" onchange="if ($('#map_select :selected').val() == 'new_map') {$('#new_map').show();} else {$('#new_map').hide();}">
			<option value="new_map">create new map</option>
			<?php
			if ($dbResMaps = db_work("SELECT * FROM `maps`")) {
				foreach ($dbResMaps['id'] as $k => $v) {
					echo "<option value='".$dbResMaps['map'][$k]."'>".$dbResMaps['map'][$k]." (".$dbResMaps['size_x'][$k]."/".$dbResMaps['size_y'][$k]."/".$dbResMaps['point_x'][$k]."/".$dbResMaps['point_y'][$k].")</option>";
				}
			}
			?>
		</select><br/>
		<div id="new_map">
			Map name:<br/>
			<input name="map_name" type="text" value=""/><br/>
			Width map:<br/>
			<input name="map_size_x" type="text" value="20"/><br/>
			Height map:<br/>
			<input name="map_size_y" type="text" value="20"/><br/>
			Entrance point X:<br/>
			<input name="map_point_x" type="text" value="7"/><br/>
			Entrance point Y:<br/>
			<input name="map_point_y" type="text" value="10"/><br/>
		</div>
		<input type="submit" value="Game">
		</form></p>
		<?php
	}
}
if ($showForm) { ?>
	<p><form action="register.php" method="post">
	<strong><big>Registration</big></strong><br/>
	Login:<br/>
	<input name="player" type="text" value=""/><br/>
	Password:<br/>
	<input name="pass" type="password" value=""/><br/>
	Confirm password:<br/>
	<input name="pass2" type="password" value=""/><br/>
	<input type="submit" value="Register">
	</form></p>
	<p><button onclick="javascript:location.href='index.php'">Back</button></p>
<?php }
require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/footer.php');
?>