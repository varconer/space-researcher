<?php
defined('VCEXE') or die;

require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/db_conf.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcfunc/link.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/get_browser.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/lang.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/func.php');

require($_SERVER['DOCUMENT_ROOT'].'/space-researcher/vcse/load_players_vars.php');

$vcseSectorSizeX = 74; // ������ ���������� ������� �� �
$vcseSectorSizeY = 64; // ������ ���������� ������� �� y
$vcseSectorMarginXForNextLine = 56; // ������ �� � ��� ��������� �����
$vcseSectorMarginYForNextLine = 33; // ������ �� y ��� ��������� �����
$vcweSectorWidthTopSide = 38; // ������ �������� ����� �������

$vcseNormalPanelWidth = 130;
$vcseSmallPanelWidth = 70;
$vcseLinePanelWidth = 150;

$vcseNormalPanelHeight = 110;
$vcseLinePanelHeight = 35;
?>