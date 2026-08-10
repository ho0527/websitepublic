-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2023-08-29 07:53:55
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
-- 資料庫： `46nationaltaskd`
--

-- --------------------------------------------------------

--
-- 資料表結構 `station`
--

CREATE TABLE `station` (
  `id` int(11) NOT NULL,
  `englishname` text NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `station`
--

INSERT INTO `station` (`id`, `englishname`, `name`) VALUES
(1, 'dingpu', '頂埔'),
(2, 'yongning', '永寧'),
(3, 'tucheng', '土城'),
(4, 'haishan', '海山'),
(5, 'fareasternhospital', '亞東醫院'),
(6, 'fuzhong', '府中'),
(7, 'banqiao', '板橋'),
(8, 'xinpu', '新埔	'),
(9, 'longshantemple', '龍山寺');

-- --------------------------------------------------------

--
-- 資料表結構 `stop`
--

CREATE TABLE `stop` (
  `id` int(11) NOT NULL,
  `trainid` text NOT NULL,
  `stationid` text NOT NULL,
  `price` text NOT NULL,
  `arrivetime` text NOT NULL,
  `stoptime` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `stop`
--

INSERT INTO `stop` (`id`, `trainid`, `stationid`, `price`, `arrivetime`, `stoptime`) VALUES
(1, '1', '1', '0', '00:00', '05'),
(2, '1', '2', '20', '00:10', '07'),
(3, '1', '3', '20', '00:20', '03'),
(4, '7', '9', '20', '06:30', '10'),
(5, '7', '7', '40', '06:45', '07'),
(6, '7', '4', '60', '06:56', '03'),
(7, '7', '1', '100', '07:04', '10');

-- --------------------------------------------------------

--
-- 資料表結構 `ticket`
--

CREATE TABLE `ticket` (
  `id` int(11) NOT NULL,
  `trainid` text NOT NULL,
  `typeid` text NOT NULL,
  `startstationid` text NOT NULL,
  `endstationid` text NOT NULL,
  `phone` text NOT NULL,
  `count` text NOT NULL,
  `statu` text NOT NULL,
  `createdate` text NOT NULL,
  `getgodate` text NOT NULL,
  `deletedate` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `train`
--

CREATE TABLE `train` (
  `id` int(11) NOT NULL,
  `traintypeid` text NOT NULL,
  `code` text NOT NULL,
  `week` text NOT NULL,
  `starttime` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `train`
--

INSERT INTO `train` (`id`, `traintypeid`, `code`, `week`, `starttime`) VALUES
(1, '1', 'A0001', '1', '00:00'),
(7, '3', 'C0001', '6', '06:30');

-- --------------------------------------------------------

--
-- 資料表結構 `type`
--

CREATE TABLE `type` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `passenger` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `type`
--

INSERT INTO `type` (`id`, `name`, `passenger`) VALUES
(1, '區間列車', '200'),
(2, '快速列車', '700'),
(3, '磁浮列車', '1200');

-- --------------------------------------------------------

--
-- 資料表結構 `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `user`
--

INSERT INTO `user` (`id`, `username`, `password`) VALUES
(1, 'admin', '1234');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `station`
--
ALTER TABLE `station`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `stop`
--
ALTER TABLE `stop`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `train`
--
ALTER TABLE `train`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `type`
--
ALTER TABLE `type`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `station`
--
ALTER TABLE `station`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `stop`
--
ALTER TABLE `stop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `ticket`
--
ALTER TABLE `ticket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `train`
--
ALTER TABLE `train`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `type`
--
ALTER TABLE `type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
