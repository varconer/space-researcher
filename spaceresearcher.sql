-- phpMyAdmin SQL Dump
-- version 4.4.15.7
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1:3306
-- Время создания: Фев 09 2017 г., 21:29
-- Версия сервера: 5.5.50
-- Версия PHP: 5.4.45

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


--
-- Структура таблицы `items`
--

DROP TABLE IF EXISTS `items`;
CREATE TABLE IF NOT EXISTS `items` (
  `id` int(11) NOT NULL,
  `item` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `subtype` varchar(255) NOT NULL,
  `weight` int(11) NOT NULL,
  `capacity` int(11) NOT NULL,
  `durability_min` int(11) NOT NULL,
  `durability_max` int(11) NOT NULL,
  `quantity_min` int(11) NOT NULL,
  `quantity_max` int(11) NOT NULL,
  `tech_min` int(11) NOT NULL,
  `tech_max` int(11) NOT NULL,
  `new_percent` int(11) NOT NULL,
  `price_min` int(11) NOT NULL,
  `price_max` int(11) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `items`
--

INSERT INTO `items` (`id`, `item`, `type`, `subtype`, `weight`, `capacity`, `durability_min`, `durability_max`, `quantity_min`, `quantity_max`, `tech_min`, `tech_max`, `new_percent`, `price_min`, `price_max`) VALUES
(1, 'Blue', 'case', 'blue', 1, 1, 1000, 1000, 0, 20, 0, 0, 100, 5, 50),
(2, 'Green', 'case', 'green', 2, 2, 500, 500, 0, 50, 0, 0, 100, 20, 200),
(3, 'Red', 'case', 'red', 3, 3, 100, 100, 0, 100, 0, 0, 100, 50, 500),
(4, 'M-10', 'hull', 'm-10', 10, 10, 100, 1000, -3, 3, 0, 0, 50, 200, 2000),
(5, 'M-20', 'hull', 'm-20', 20, 20, 100, 1000, -4, 2, 0, 0, 40, 300, 5000),
(6, 'M-30', 'hull', 'm-30', 30, 30, 100, 1000, -5, 1, 0, 0, 30, 500, 8000),
(7, 'Prime-1', 'engine', 'prime-1', 2, 2, 100, 1000, -3, 3, 30, 30, 50, 150, 1500),
(8, 'Prime-A', 'hoarder', 'prime-a', 2, 2, 100, 1000, -3, 3, 1000, 1000, 50, 100, 1000),
(9, 'Prime-2', 'engine', 'prime-2', 3, 3, 100, 1000, -4, 2, 60, 60, 40, 300, 3500),
(10, 'Prime-3', 'engine', 'prime-3', 4, 4, 100, 1000, -5, 2, 100, 100, 30, 450, 5500),
(11, 'Prime-B', 'hoarder', 'prime-b', 3, 3, 100, 1000, -4, 3, 5000, 5000, 40, 200, 2500),
(12, 'Prime-C', 'hoarder', 'prime-c', 4, 4, 100, 1000, -5, 2, 10000, 10000, 30, 300, 4500),
(13, 'Falcon L', 'hull', 'falcon-l', 8, 15, 100, 2000, -4, 2, 0, 0, 50, 500, 8000),
(14, 'Falcon M', 'hull', 'falcon-m', 16, 30, 100, 2000, -5, 2, 0, 0, 40, 1000, 15000),
(15, 'Falcon S', 'hull', 'falcon-s', 24, 45, 100, 2500, -6, 1, 0, 0, 30, 1500, 25000),
(16, 'Falcon Cargo L', 'hull', 'falcon-cargo-l', 12, 18, 100, 1800, -5, 2, 0, 0, 50, 500, 7500),
(17, 'Falcon Cargo M', 'hull', 'falcon-cargo-m', 24, 38, 100, 1800, -6, 2, 0, 0, 40, 1000, 13500),
(18, 'Falcon Cargo S', 'hull', 'falcon-cargo-s', 36, 58, 100, 2000, -7, 1, 0, 0, 30, 1500, 20000),
(19, 'Falcon Supercargo', 'hull', 'falcon-supercargo', 50, 100, 100, 2500, -8, 1, 0, 0, 30, 3000, 30000),
(20, 'Nano-1', 'engine', 'nano-1', 1, 2, 100, 2000, -3, 2, 50, 50, 50, 400, 6000),
(21, 'Nano-2', 'engine', 'nano-2', 2, 3, 100, 2000, -4, 2, 110, 110, 40, 800, 10000),
(22, 'Nano-3', 'engine', 'nano-3', 3, 4, 100, 2000, -5, 1, 180, 180, 30, 1400, 18000),
(23, 'Nano-A', 'hoarder', 'nano-a', 1, 2, 100, 2000, -3, 2, 8000, 8000, 50, 350, 5000),
(24, 'Nano-B', 'hoarder', 'nano-b', 2, 3, 100, 2000, -4, 2, 15000, 15000, 40, 700, 9000),
(25, 'Nano-C', 'hoarder', 'nano-c', 3, 4, 100, 2000, -5, 1, 25000, 25000, 30, 1400, 15000),
(26, 'Prime-3 Hyper', 'engine', 'prime-3', 6, 6, 100, 1200, -6, 2, 90, 90, 30, 600, 7500),
(27, 'Nano-3 Hyper', 'engine', 'nano-3', 4, 5, 100, 2500, -6, 1, 175, 175, 30, 2000, 25000),
(28, 'Worker', 'passenger', 'worker', 1, 1, 1, 1, -3, 2, 0, 0, 100, 500, 2500),
(29, 'Permit', 'permit', 'colonization', 1, 1, 1, 1, 1, 1, 0, 0, 100, 50000, 50000),
(30, 'Falcon factory', 'factory', 'falcon', 50, 50, 100, 1000, -15, 1, 0, 0, 50, 15000, 75000),
(31, 'M factory', 'factory', 'm', 40, 40, 100, 800, -10, 1, 0, 0, 50, 8000, 40000),
(32, 'Spacebox', 'hangar', 'spacebox', 10, 20, 100, 1000, -8, 2, 100, 100, 50, 2000, 10000),
(33, 'Megabox', 'hangar', 'megabox', 20, 40, 100, 1200, -9, 2, 200, 200, 40, 3800, 18000),
(34, 'Gigabox', 'hangar', 'gigabox', 30, 60, 100, 1400, -10, 2, 300, 300, 30, 5000, 24000),
(35, 'Workers-1', 'cabin', 'workers-1', 3, 3, 100, 1000, -5, 2, 1, 1, 50, 500, 2000),
(36, 'Workers-2', 'cabin', 'workers-2', 6, 6, 100, 1000, -6, 2, 2, 2, 40, 900, 3500),
(37, 'Workers-4', 'cabin', 'workers-4', 9, 9, 100, 1000, -7, 1, 4, 4, 30, 1200, 5000),
(38, 'Manager', 'passenger', 'manager', 1, 1, 1, 1, -5, 2, 0, 0, 100, 2000, 10000),
(39, 'nGrip alpha', 'hull', 'ngrip-alpha', 14, 35, 100, 3000, -8, 1, 0, 0, 50, 2500, 25000),
(40, 'nGrip beta', 'hull', 'ngrip-beta', 28, 75, 100, 3000, -9, 1, 0, 0, 40, 3500, 35000),
(41, 'nGrip gamma', 'hull', 'ngrip-gamma', 40, 120, 100, 3000, -10, 1, 0, 0, 30, 5000, 50000),
(42, 'nGrip Supercargo', 'hull', 'ngrip-gamma-supercargo', 70, 200, 100, 3000, -12, 1, 0, 0, 30, 8000, 80000),
(43, 'nGrip A', 'engine', 'ngrip-a', 6, 5, 100, 2500, -4, 2, 90, 90, 50, 1000, 10000),
(44, 'nGrip AA', 'engine', 'ngrip-aa', 9, 8, 100, 2500, -5, 1, 175, 175, 40, 2000, 20000),
(45, 'nGrip AAA', 'engine', 'ngrip-aaa', 12, 12, 100, 2500, -6, 1, 260, 260, 30, 3000, 30000),
(46, 'nGrip AAA HyperL', 'engine', 'ngrip-aaa-hyper', 8, 8, 100, 3000, -7, 1, 160, 160, 40, 2500, 25000),
(47, 'nGrip AAA Hyper', 'engine', 'ngrip-aaa-hyper', 14, 15, 100, 3000, -8, 1, 250, 250, 30, 3500, 35000),
(48, 'nGrip 1', 'hoarder', 'ngrip-1', 5, 5, 100, 2800, -4, 2, 40000, 40000, 50, 2500, 25000),
(49, 'nGrip 2', 'hoarder', 'ngrip-2', 8, 8, 100, 2800, -5, 1, 60000, 60000, 40, 3500, 35000),
(50, 'nGrip 3', 'hoarder', 'ngrip-3', 10, 10, 100, 2800, -6, 1, 80000, 80000, 30, 5500, 55000),
(51, 'nGrip 2GT', 'hoarder', 'ngrip-2gt', 9, 9, 100, 2000, -7, 1, 75000, 75000, 40, 3000, 30000),
(52, 'nGrip 3GT', 'hoarder', 'ngrip-3gt', 12, 12, 100, 2000, -8, 1, 100000, 100000, 30, 7000, 70000),
(53, 'Nano factory', 'factory', 'nano', 45, 45, 100, 1000, -12, 1, 0, 0, 50, 12000, 65000),
(54, 'Prime factory', 'factory', 'prime', 35, 35, 100, 800, -9, 1, 0, 0, 50, 6000, 30000),
(55, 'nGrip factory', 'factory', 'ngrip', 100, 100, 100, 1500, -16, 1, 0, 0, 50, 30000, 150000),
(56, 'Lighter 500', 'hull', 'lighter-500', 15, 100, 100, 1500, -9, 1, 0, 0, 50, 6000, 60000),
(57, 'Lighter 1000', 'hull', 'lighter-1000', 30, 200, 100, 1600, -10, 1, 0, 0, 40, 12000, 120000),
(58, 'Lighter 2000', 'hull', 'lighter-2000', 50, 350, 100, 1700, -12, 1, 0, 0, 30, 30000, 300000),
(59, 'Lighter 500E', 'engine', 'lighter-500e', 4, 4, 100, 1500, -8, 1, 180, 180, 50, 4000, 40000),
(60, 'Lighter 1000E', 'engine', 'lighter-1000e', 8, 8, 100, 1500, -9, 1, 350, 350, 40, 8000, 80000),
(61, 'Lighter 2000E', 'engine', 'lighter-2000e', 16, 16, 100, 1500, -10, 1, 600, 600, 30, 15000, 150000),
(62, 'Lighter 500H', 'hoarder', 'lighter-500h', 4, 4, 100, 1500, -8, 1, 90000, 90000, 50, 8000, 80000),
(63, 'Lighter 1000H', 'hoarder', 'lighter-1000h', 8, 8, 100, 1500, -9, 1, 190000, 190000, 40, 15000, 150000),
(64, 'Lighter 2000H', 'hoarder', 'lighter-2000h', 16, 16, 100, 1500, -10, 1, 390000, 390000, 30, 25000, 250000),
(65, 'Racer', 'passenger', 'racer', 2, 2, 1, 1, -8, 1, 0, 0, 100, 10000, 50000),
(66, 'Race result', 'message', 'race', 1, 1, 1, 1, 0, 0, 0, 0, 100, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `maps`
--

DROP TABLE IF EXISTS `maps`;
CREATE TABLE IF NOT EXISTS `maps` (
  `id` int(11) NOT NULL,
  `map` varchar(255) NOT NULL,
  `size_x` int(11) NOT NULL,
  `size_y` int(11) NOT NULL,
  `point_x` int(11) NOT NULL,
  `point_y` int(11) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `maps`
--

INSERT INTO `maps` (`id`, `map`, `size_x`, `size_y`, `point_x`, `point_y`) VALUES
(1, 'Milkway', 20, 20, 7, 10);

-- --------------------------------------------------------

--
-- Структура таблицы `maps_vars`
--

DROP TABLE IF EXISTS `maps_vars`;
CREATE TABLE IF NOT EXISTS `maps_vars` (
  `id` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `radiation` int(11) NOT NULL,
  `map` varchar(255) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `maps_vars`
--

INSERT INTO `maps_vars` (`id`, `y`, `radiation`, `map`) VALUES
(1, 1, 3, 'Milkway'),
(2, 2, 0, 'Milkway'),
(3, 3, 9, 'Milkway'),
(4, 4, 9, 'Milkway'),
(5, 5, 2, 'Milkway'),
(6, 6, 7, 'Milkway'),
(7, 7, 1, 'Milkway'),
(8, 8, 7, 'Milkway'),
(9, 9, 4, 'Milkway'),
(10, 10, 5, 'Milkway'),
(11, 11, 5, 'Milkway'),
(12, 12, 6, 'Milkway'),
(13, 13, 0, 'Milkway'),
(14, 14, 8, 'Milkway'),
(15, 15, 9, 'Milkway'),
(16, 16, 5, 'Milkway'),
(17, 17, 5, 'Milkway'),
(18, 18, 9, 'Milkway'),
(19, 19, 0, 'Milkway'),
(20, 20, 1, 'Milkway');

-- --------------------------------------------------------

--
-- Структура таблицы `maps_vars_2`
--

DROP TABLE IF EXISTS `maps_vars_2`;
CREATE TABLE IF NOT EXISTS `maps_vars_2` (
  `id` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `radiation` int(11) NOT NULL,
  `map` varchar(255) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `maps_vars_2`
--

INSERT INTO `maps_vars_2` (`id`, `x`, `radiation`, `map`) VALUES
(1, -9, 6, 'Milkway'),
(2, -8, 8, 'Milkway'),
(3, -7, 4, 'Milkway'),
(4, -6, 4, 'Milkway'),
(5, -5, 1, 'Milkway'),
(6, -4, 1, 'Milkway'),
(7, -3, 8, 'Milkway'),
(8, -2, 6, 'Milkway'),
(9, -1, 3, 'Milkway'),
(10, 0, 9, 'Milkway'),
(11, 1, 9, 'Milkway'),
(12, 2, 0, 'Milkway'),
(13, 3, 7, 'Milkway'),
(14, 4, 0, 'Milkway'),
(15, 5, 9, 'Milkway'),
(16, 6, 3, 'Milkway'),
(17, 7, 7, 'Milkway'),
(18, 8, 2, 'Milkway'),
(19, 9, 2, 'Milkway'),
(20, 10, 6, 'Milkway'),
(21, 11, 1, 'Milkway'),
(22, 12, 4, 'Milkway'),
(23, 13, 2, 'Milkway'),
(24, 14, 4, 'Milkway'),
(25, 15, 5, 'Milkway'),
(26, 16, 1, 'Milkway'),
(27, 17, 5, 'Milkway'),
(28, 18, 3, 'Milkway'),
(29, 19, 9, 'Milkway'),
(30, 20, 7, 'Milkway');

-- --------------------------------------------------------

--
-- Структура таблицы `objects`
--

DROP TABLE IF EXISTS `objects`;
CREATE TABLE IF NOT EXISTS `objects` (
  `id` int(11) NOT NULL,
  `object` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `subtype` varchar(255) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `last_gen` int(11) NOT NULL DEFAULT '0',
  `map` varchar(255) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `objects`
--

INSERT INTO `objects` (`id`, `object`, `type`, `subtype`, `x`, `y`, `last_gen`, `map`) VALUES
(1, 'Alpha', 'planet', 'blue', 7, 10, 0, 'Milkway'),
(2, 'Beta', 'planet', 'blue', 2, 8, 0, 'Milkway'),
(3, 'Gamma', 'planet', 'blue', -4, 14, 0, 'Milkway'),
(4, 'Delta', 'planet', 'blue', -1, 18, 1486664750, 'Milkway'),
(5, 'Lambda', 'planet', 'green', 3, 15, 0, 'Milkway'),
(6, 'Beta-2', 'planet', 'green', 7, 17, 0, 'Milkway'),
(7, 'Gamma-2', 'planet', 'green', 13, 9, 0, 'Milkway'),
(8, 'Delta-2', 'planet', 'free-grey', 15, 1, 0, 'Milkway'),
(9, 'Lambda-2', 'planet', 'brown', 10, 2, 0, 'Milkway'),
(10, 'Alpha-2', 'planet', 'green', 5, 2, 0, 'Milkway'),
(11, 'Beta-3', 'planet', 'free-grey', -1, 5, 0, 'Milkway'),
(12, 'Gamma-3', 'planet', 'brown', 17, 4, 0, 'Milkway'),
(13, 'M-1', 'star', 'yellow', -1, 15, 0, 'Milkway'),
(14, 'M-2', 'star', 'yellow', 6, 5, 0, 'Milkway'),
(15, 'M-3', 'star', 'yellow', 6, 9, 0, 'Milkway'),
(16, 'M-4', 'star', 'yellow', 11, 9, 0, 'Milkway'),
(17, 'Delta-3', 'planet', 'station', 8, 13, 0, 'Milkway'),
(18, 'Lambda-3', 'planet', 'station', 0, 8, 0, 'Milkway'),
(19, 'Alpha-3', 'planet', 'blue', 10, 20, 0, 'Milkway');

-- --------------------------------------------------------

--
-- Структура таблицы `objects_items`
--

DROP TABLE IF EXISTS `objects_items`;
CREATE TABLE IF NOT EXISTS `objects_items` (
  `oid` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `durability` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `tech` int(11) NOT NULL,
  `tech2` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `objects_items`
--

INSERT INTO `objects_items` (`oid`, `object_id`, `item_id`, `durability`, `quantity`, `tech`, `tech2`, `price`) VALUES
(1, 4, 1, 1000, 7, 0, 0, 43),
(2, 4, 2, 500, 33, 0, 0, 103),
(3, 4, 3, 100, 5, 0, 0, 108),
(4, 4, 4, 239, 1, 0, 0, 238),
(5, 4, 4, 1000, 1, 0, 0, 2021),
(6, 4, 4, 197, 1, 0, 0, 227),
(7, 4, 9, 912, 1, 60, 0, 2965),
(8, 4, 9, 304, 1, 60, 0, 445),
(9, 4, 16, 1800, 1, 0, 0, 7378),
(10, 4, 25, 1998, 1, 25000, 0, 15158),
(11, 4, 27, 2268, 1, 175, 0, 20971),
(12, 4, 28, 1, 1, 1, 0, 890),
(13, 4, 28, 1, 1, 2, 0, 836),
(14, 4, 39, 124, 1, 0, 0, 2640),
(15, 4, 44, 662, 1, 175, 0, 2997),
(16, 4, 45, 1646, 1, 260, 0, 13493),
(17, 4, 48, 2800, 1, 40000, 0, 24878),
(18, 8, 29, 1, 1, 8, 0, 50000),
(19, 11, 29, 1, 1, 11, 0, 50000);

-- --------------------------------------------------------

--
-- Структура таблицы `players`
--

DROP TABLE IF EXISTS `players`;
CREATE TABLE IF NOT EXISTS `players` (
  `id` int(11) NOT NULL,
  `player` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `players`
--

INSERT INTO `players` (`id`, `player`, `pass`) VALUES
(1, 'vc', 'c4ca4238a0b923820dcc509a6f75849b');

-- --------------------------------------------------------

--
-- Структура таблицы `players_items`
--

DROP TABLE IF EXISTS `players_items`;
CREATE TABLE IF NOT EXISTS `players_items` (
  `pid` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `durability` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `tech` int(11) NOT NULL,
  `tech2` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `player` varchar(255) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `players_items`
--

INSERT INTO `players_items` (`pid`, `item_id`, `durability`, `quantity`, `tech`, `tech2`, `price`, `player`) VALUES
(1, 4, 1000, 1, 0, 0, 2000, 'vc'),
(2, 7, 1000, 1, 30, 0, 1500, 'vc'),
(3, 8, 1000, 1, 1000, 0, 1000, 'vc');

-- --------------------------------------------------------

--
-- Структура таблицы `players_vars`
--

DROP TABLE IF EXISTS `players_vars`;
CREATE TABLE IF NOT EXISTS `players_vars` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `player` varchar(255) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `players_vars`
--

INSERT INTO `players_vars` (`id`, `name`, `value`, `player`) VALUES
(1, 'x', '9', 'vc'),
(2, 'y', '7', 'vc'),
(3, 'energy', '100', 'vc'),
(4, 'tensity', '1', 'vc'),
(5, 'tensity_direct', '1', 'vc'),
(6, 'mode', '*', 'vc'),
(7, 'panel', 'show', 'vc'),
(8, 'angle', '240', 'vc'),
(9, 'map', 'Milkway', 'vc'),
(10, 'hull', '1', 'vc'),
(11, 'engine', '2', 'vc'),
(12, 'hoarder', '3', 'vc'),
(13, 'creation', '0', 'vc'),
(14, 'creation_planet_name', '15', 'vc'),
(15, 'creation_star_name', '4', 'vc'),
(16, 'ship_type', 'm-10', 'vc'),
(17, 'time', '1486664729', 'vc');

-- --------------------------------------------------------

--
-- Структура таблицы `racers`
--

DROP TABLE IF EXISTS `racers`;
CREATE TABLE IF NOT EXISTS `racers` (
  `id` int(11) NOT NULL,
  `race_oid` int(11) NOT NULL,
  `start_obj` int(11) NOT NULL,
  `finish_obj` int(11) NOT NULL,
  `start_time` int(11) NOT NULL,
  `time` float NOT NULL DEFAULT '0',
  `equip` varchar(255) NOT NULL DEFAULT '',
  `player` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `maps`
--
ALTER TABLE `maps`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `maps_vars`
--
ALTER TABLE `maps_vars`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `maps_vars_2`
--
ALTER TABLE `maps_vars_2`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `objects`
--
ALTER TABLE `objects`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `objects_items`
--
ALTER TABLE `objects_items`
  ADD PRIMARY KEY (`oid`);

--
-- Индексы таблицы `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `players_items`
--
ALTER TABLE `players_items`
  ADD PRIMARY KEY (`pid`);

--
-- Индексы таблицы `players_vars`
--
ALTER TABLE `players_vars`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `racers`
--
ALTER TABLE `racers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=67;
--
-- AUTO_INCREMENT для таблицы `maps`
--
ALTER TABLE `maps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `maps_vars`
--
ALTER TABLE `maps_vars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT для таблицы `maps_vars_2`
--
ALTER TABLE `maps_vars_2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT для таблицы `objects`
--
ALTER TABLE `objects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT для таблицы `objects_items`
--
ALTER TABLE `objects_items`
  MODIFY `oid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT для таблицы `players`
--
ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `players_items`
--
ALTER TABLE `players_items`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `players_vars`
--
ALTER TABLE `players_vars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT для таблицы `racers`
--
ALTER TABLE `racers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
