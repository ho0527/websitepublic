-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2023-10-30 02:44:39
-- 伺服器版本： 10.4.28-MariaDB
-- PHP 版本： 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `50nationalmoduled`
--

-- --------------------------------------------------------

--
-- 資料表結構 `rank`
--

CREATE TABLE `rank` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `time` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `difficulty` text NOT NULL,
  `createtime` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `rank`
--

INSERT INTO `rank` (`id`, `username`, `time`, `score`, `difficulty`, `createtime`) VALUES
(1, 'username', 27, 280, 'normal', '2023-10-28 08:09:58'),
(2, 'username', 95, 2430, 'normal', '2023-10-28 08:12:26'),
(3, 'username', 107, 610, 'normal', '2023-10-28 08:40:40'),
(4, 'username', 79, 90, 'normal', '2023-10-28 08:43:16'),
(5, 'username', 79, 90, 'normal', '2023-10-28 08:43:16'),
(6, 'username', 79, 90, 'normal', '2023-10-28 08:43:16'),
(7, 'username', 79, 90, 'normal', '2023-10-28 08:43:16'),
(8, 'username', 79, 90, 'normal', '2023-10-28 08:43:16'),
(9, 'username', 79, 440, 'normal', '2023-10-28 08:46:24'),
(10, 'username', 79, 440, 'normal', '2023-10-28 08:46:24'),
(11, 'username', 79, 440, 'normal', '2023-10-28 08:46:24'),
(12, 'username', 34, 170, 'normal', '2023-10-28 08:50:08'),
(13, 'username', 31, 320, '31', '2023-10-28 08:55:04'),
(14, 'username', 35, 240, 'normal', '2023-10-28 09:08:25'),
(15, 'username', 64, 1530, 'normal', '2023-10-28 15:14:50');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `rank`
--
ALTER TABLE `rank`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `rank`
--
ALTER TABLE `rank`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
