-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2023-05-18 15:48:36
-- 伺服器版本： 10.4.27-MariaDB
-- PHP 版本： 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `53nationala17`
--

-- --------------------------------------------------------

--
-- 資料表結構 `log`
--

CREATE TABLE `log` (
  `id` int(11) NOT NULL,
  `1` text NOT NULL,
  `2` text NOT NULL,
  `3` text NOT NULL,
  `4` text NOT NULL,
  `5` text NOT NULL,
  `6` text NOT NULL,
  `7` text NOT NULL,
  `8` text NOT NULL,
  `9` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `log`
--

INSERT INTO `log` (`id`, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`) VALUES
(1, '', '', '', '', '', '', '', '', ''),
(2, '', '', '', '', '', '', '', '', ''),
(3, '', '', '', '', '', '', '', '', ''),
(4, '', '', '', '', '', '', '', '', ''),
(5, '', '', '', '', '', '', '', '', ''),
(6, '', '', '', '', '', '', '', '', ''),
(7, '', '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- 資料表結構 `now`
--

CREATE TABLE `now` (
  `id` int(11) NOT NULL,
  `1` text NOT NULL,
  `2` text NOT NULL,
  `3` text NOT NULL,
  `4` text NOT NULL,
  `5` text NOT NULL,
  `6` text NOT NULL,
  `7` text NOT NULL,
  `8` text NOT NULL,
  `9` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `now`
--

INSERT INTO `now` (`id`, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`) VALUES
(1, '', '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- 資料表結構 `wintime`
--

CREATE TABLE `wintime` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `bot` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `wintime`
--

INSERT INTO `wintime` (`id`, `user`, `bot`) VALUES
(1, 0, 0);

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `now`
--
ALTER TABLE `now`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `wintime`
--
ALTER TABLE `wintime`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `log`
--
ALTER TABLE `log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `now`
--
ALTER TABLE `now`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `wintime`
--
ALTER TABLE `wintime`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
