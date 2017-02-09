<?php
define('VCEXE', 1);

$vcseAuth = false;
$_GET['reload_map'] = 1;
require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/config.php');
// verify auth
if (!$vcseAuth || !$dbConfig) header("Location: index.php");
?>
<!DOCTYPE html> 
<html> 
<head> 
<meta http-equiv="Content-Type" content="text/html; Charset=UTF-8" /> 
<title>Space Researcher</title>
<script src="/space-researcher/vcse/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="/space-researcher/vcse/jQueryRotate.2.2.js" type="text/javascript"></script>
<script src="/space-researcher/vcse/jquery-ui-1.8.9.min.js" type="text/javascript"></script>
<script>
var vcseLang = <?php echo $vcseLang=="eng"?0:1; ?>;
var vcseSectorSizeX = <?php echo $vcseSectorSizeX; ?>;
var vcseSectorSizeY = <?php echo $vcseSectorSizeY; ?>;
<?php
if (isset($player) && isset($pass)) {
	echo "var vcsePlayer='".$player."';\r\n";
	echo "var vcsePass='".$pass."';\r\n";	
}
?>
</script>
<script src="/space-researcher/vcse/func.js" type="text/javascript"></script>
<style>
body {background-color:black;}
#window {position:relative; overflow:hidden;}
#pleer {position:absolute; left:250px; top:0px; z-index:25;}
#content {position:relative; width:800px; height:500px;}
.sector {cursor:pointer; filter:alpha(Opacity=30); opacity:0.3;}
.sector:hover {cursor:pointer; filter:alpha(Opacity=100); opacity:1;}
.planet {cursor:pointer;}
#panel {position:fixed; z-index:16; top:0px; left:0px;}
	#panel_slide {cursor:pointer; position:realtive; z-index:16; width:240px; height:27px; overflow:hidden; background-image:url(/space-researcher/img/panel-show.png);}
	#panel_name {cursor:pointer; position:relative; z-index:16; width:240px; height:64px; overflow:hidden; background-image:url(/space-researcher/img/panel-name.png);}
		#obj_type {position:absolute; left:15px; top:2px; z-index:17; font-size:20pt; color:#f38000; font-family:monospace; font-weight:bold;}
		#obj_name {position:absolute; left:15px; top:28px; z-index:17; font-size:20pt; color:#f38000; font-family:monospace; font-weight:bold;}
		#obj_img {position:absolute; left:171px; top:0px; z-index:17;}
	#panel_store_slider_up {cursor:pointer; width:240px; height:28px; background-image:url(/space-researcher/img/slider-up-store.png);}
	#panel_store_slider_down {cursor:pointer; width:240px; height:28px; background-image:url(/space-researcher/img/slider-down-store.png);}
	#panel_store_group {width:240px; height:130px; overflow:hidden;}
	#panel_store_group_2 {width:240px; height:195px; overflow:hidden;}
		#panel_store_group_content, #panel_store_group_content_2 {position:relative; left:0px; top:0px;}
			#panel_store {position:relative; z-index:16; width:240px; height:65px; overflow:hidden; background-image:url(/space-researcher/img/panel-store.png);}
				#store_cell_1 {position:absolute; left:-5px; top:0px; z-index:17;}
					#store_cell_1_flag {position:absolute; left:-5px; top:0px; z-index:21;}
					#store_cell_1_flag_warning {position:absolute; left:-5px; top:0px; z-index:20;}
					#store_cell_1_bar {position:absolute; left:6px; top:5px; z-index:19;}
				#store_cell_2 {position:absolute; left:54px; top:0px; z-index:17;}
					#store_cell_2_flag {position:absolute; left:54px; top:0px; z-index:21;}
					#store_cell_2_flag_warning {position:absolute; left:54px; top:0px; z-index:20;}
					#store_cell_2_bar {position:absolute; left:65px; top:5px; z-index:19;}
				#store_cell_3 {position:absolute; left:113px; top:0px; z-index:17;}
					#store_cell_3_flag {position:absolute; left:113px; top:0px; z-index:21;}
					#store_cell_3_flag_warning {position:absolute; left:113px; top:0px; z-index:20;}
					#store_cell_3_bar {position:absolute; left:124px; top:5px; z-index:19;}
				#store_cell_4 {position:absolute; left:172px; top:0px; z-index:17;}
					#store_cell_4_flag {position:absolute; left:172px; top:0px; z-index:21;}
					#store_cell_4_flag_warning {position:absolute; left:172px; top:0px; z-index:20;}
					#store_cell_4_bar {position:absolute; left:183px; top:5px; z-index:19;}
					.item {cursor:pointer;}
	#panel_item {cursor:pointer; position:fixed; z-index:20; left:10px; top:10px; border:3px solid #005f00; background-color:#00d43c; font-size:12pt; color:#f38000; font-family:monospace; font-weight:bold; padding:5px;}
		.store_button {border:1px solid #005f00; padding:3px; margin-bottom:1px;}
	#panel_instruments {position:relative; z-index:16; width:240px; height:75px; overflow:hidden; background-image:url(/space-researcher/img/panel.png);}
		#energy {position:absolute; left:15px; top:40px; z-index:17; font-size:20pt; color:#f38000; font-family:monospace; font-weight:bold;}
			#energy_science {font-size:16px;}
		#tensity {cursor:pointer; position:absolute; left:173px; top:15px; z-index:17; font-size:36pt; color:#f38000; font-family:monospace; font-weight:bold;}
		#step4 {position:absolute; left:16px; top:5px; z-index:17; font-size:20pt; color:#f38000; font-family:monospace; font-weight:bold;}
		#step3 {position:absolute; left:58px; top:5px; z-index:17; font-size:20pt; color:#f38000; font-family:monospace; font-weight:bold;}
		#step2 {position:absolute; left:100px; top:5px; z-index:17; font-size:20pt; color:#f38000; font-family:monospace; font-weight:bold;}
		#step1 {position:absolute; left:141px; top:5px; z-index:17; font-size:20pt; color:#f38000; font-family:monospace; font-weight:bold;}
		#timebar {position:absolute; left:6px; top:5px; z-index:17; width:1px; height:4px;}

<?php
// sectors coord
/* for ($line = 1; $line < $vcseLineQuan+1; $line++) {
	for ($col = 1-round(($line-1)/2); $col < $vcseColQuan+1-round(($line-1)/2); $col++) {
		// get coord
		$curX = vcseGetPosX($col, $line);
		$curY = vcseGetPosY($line);
		
		// set coord
		echo '#s-'.$col.'-'.$line.' {position:absolute; z-index:10; top:'.
			$curY.
		'px; left:'.
			$curX.
		'px;}';
		echo "\r\n";
	}
} */
// obj coord
/* if ($dbObj) {
	foreach ($dbObj['id'] as $k => $v) {
		// get coord
		$curX = vcseGetPosX($dbObj['x'][$k], $dbObj['y'][$k]);
		$curY = vcseGetPosY($dbObj['y'][$k]);
		
		// set coord
		echo '#o-'.$dbObj['x'][$k].'-'.$dbObj['y'][$k].' {position:absolute; z-index:11; top:'.
			$curY.
		'px; left:'.
			$curX.
		'px;}';
		echo "\r\n";
	}
} */
// other ships coord
/* if ($dbPlayers) {
	foreach ($dbPlayers as $plName => $plArr) {
		// get coord
		$curX = vcseGetPosX($plArr['x'], $plArr['y']);
		$curY = vcseGetPosY($plArr['y']);
		
		// set coord
		echo '#player-'.$plName.' {position:absolute; z-index:12; top:'.
			$curY.
		'px; left:'.
			$curX.
		'px;}';
		echo "\r\n";
	}
} */
// ship coord
//echo "#myshipmark {position:absolute; z-index:14; top:".vcseGetPosY($dbConfig['y'])."px; left:".vcseGetPosX($dbConfig['x'], $dbConfig['y'])."px;}\r\n";
//echo "#myship {position:absolute; z-index:15; top:".vcseGetPosY($dbConfig['y'])."px; left:".vcseGetPosX($dbConfig['x'], $dbConfig['y'])."px; cursor:pointer;}\r\n";
?>
</style>
</head> 
<body onload="vcseLoad('<?php echo $dbConfig['map']; ?>')"> 
<img id='timebar' src='/space-researcher/img/bar.png' />
<div id="pleer">
	<object type="application/x-shockwave-flash" data="player_mp3_maxi.swf" width="200" height="20">
		<param name="wmode" value="transparent" />
		<param name="movie" value="/space-researcher/player_mp3_maxi.swf" />
		<param name="FlashVars" value="mp3=/space-researcher/audio/J.Blasco_-_Rain_in_the_earth.mp3&amp;autoplay=1&amp;loop=1&amp;showvolume=1" />
	</object>
</div>
<div id="window">
	<div id="content">
		<?php
			require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/content.php');
		?>
	</div>
</div>
</body> 
</html>
<script>
// set window size
/* var windowWidth = $(window).width();
windowWidth = windowWidth - 25;
var windowHeight = $(window).height();
windowHeight = windowHeight - 25;
$("#window").width(windowWidth);
$("#window").height(windowHeight);
$("#content").draggable(); */
// rotate other ships
<?php
/* if ($dbPlayers) {
	foreach ($dbPlayers as $plName => $plArr) {
		echo 
			"var plOldLeft".$plName." = 0;\r\n".
			"var plOldTop".$plName." = 0;\r\n".
			"$('#player-".$plName."').rotate(".$plArr['angle'].");\r\n"
		;
	}
} */
?>
// set ship to center of window
/* vcseShipToCenter();
$('#myship').rotate(<?php //echo $dbConfig['angle']; ?>); */
</script>