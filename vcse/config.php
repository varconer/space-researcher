<?php
defined('VCEXE') or die;

require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/db_conf.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcfunc/link.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/get_browser.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/lang.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/func.php');

require($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/load_players_vars.php');

$vcseSectorSizeX = 74; // размер избражения сектора по х
$vcseSectorSizeY = 64; // размер избражения сектора по y
$vcseSectorMarginXForNextLine = 56; // отступ по х для следующей линии
$vcseSectorMarginYForNextLine = 33; // отступ по y для следующей линии
$vcweSectorWidthTopSide = 38; // ширина верхнего плеча сектора

$vcseNormalPanelWidth = 130;
$vcseSmallPanelWidth = 70;
$vcseLinePanelWidth = 150;

$vcseNormalPanelHeight = 110;
$vcseLinePanelHeight = 35;
?>