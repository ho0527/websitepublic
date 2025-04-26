-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025 年 04 月 25 日 16:36
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
-- 資料庫： `worldskill2022modulec`
--

-- --------------------------------------------------------

--
-- 資料表結構 `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `createtime` datetime NOT NULL,
  `updatetime` datetime NOT NULL,
  `lastlogintime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `createtime`, `updatetime`, `lastlogintime`) VALUES
(1, 'admin1', 'hellouniverse1!', '2025-03-28 02:36:14', '2025-03-28 02:36:14', '2025-03-28 21:15:53'),
(2, 'admin2', 'hellouniverse2!', '2025-03-28 02:37:08', '2025-03-28 02:37:08', '2025-03-28 02:37:08');

-- --------------------------------------------------------

--
-- 資料表結構 `game`
--

CREATE TABLE `game` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `title` text NOT NULL,
  `slug` text NOT NULL,
  `description` text NOT NULL,
  `createtime` text NOT NULL,
  `updatetime` text NOT NULL,
  `deletetime` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `game`
--

INSERT INTO `game` (`id`, `userid`, `title`, `slug`, `description`, `createtime`, `updatetime`, `deletetime`) VALUES
(1, 4, 'Demo Game 1 (updated)', 'demo-game-1', 'This is demo game 1 (updated)', '2023-12-31 21:10:39', '2025-04-25 21:36:39', NULL),
(2, 5, 'Demo Game 2', 'demo-game-2', 'lorem text for this game2', '2023-12-31 21:16:04', '2024-01-01 21:05:25', NULL),
(6, 4, 'My New Game', 'my-new-game', 'This is my newest game, it is awesome', '2025-04-25 21:40:15', '2025-04-25 21:40:15', NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `gameversion`
--

CREATE TABLE `gameversion` (
  `id` int(11) NOT NULL,
  `gameid` int(11) NOT NULL,
  `thumbnailpath` text NOT NULL,
  `gamepath` text NOT NULL,
  `version` int(11) NOT NULL,
  `createtime` datetime NOT NULL,
  `updatetime` datetime DEFAULT NULL,
  `deletetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `gameversion`
--

INSERT INTO `gameversion` (`id`, `gameid`, `thumbnailpath`, `gamepath`, `version`, `createtime`, `updatetime`, `deletetime`) VALUES
(1, 1, '/backend/media/ws2022modulec/demo-game-1/1/thumbnail.png', '/backend/media/ws2022modulec/demo-game-1/1/', 1, '2025-03-28 02:38:26', NULL, NULL),
(2, 1, '/backend/media/ws2022modulec/demo-game-1/2/thumbnail.png', '/backend/media/ws2022modulec/demo-game-1/2/', 2, '2025-03-28 02:39:35', NULL, NULL),
(3, 2, '/backend/media/ws2022modulec/demo-game-2/1/thumbnail.png', '/backend/media/ws2022modulec/demo-game-2/1/', 1, '2025-03-28 02:40:30', NULL, NULL),
(4, 1, '/backend/media/ws2022modulec/demo-game-1/3/thumbnail.png', '/backend/media/ws2022modulec/demo-game-1/3/', 3, '2025-03-28 02:40:48', NULL, NULL),
(5, 1, '/backend/media/ws2022modulec/demo-game-1/4/thumbnail.png', '/backend/media/ws2022modulec/demo-game-1/4/', 4, '2025-03-28 02:41:11', NULL, NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `score`
--

CREATE TABLE `score` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `gameid` int(11) NOT NULL,
  `gameversionid` int(11) NOT NULL,
  `score` double NOT NULL,
  `createtime` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `score`
--

INSERT INTO `score` (`id`, `userid`, `gameid`, `gameversionid`, `score`, `createtime`) VALUES
(1, 1, 1, 1, 10, '2024-01-01 21:08:40'),
(2, 1, 1, 1, 15, '2024-01-01 21:11:44'),
(3, 1, 1, 4, 12, '2024-01-16 21:12:48'),
(4, 4, 2, 3, 3, '2024-01-16 21:13:34'),
(5, 4, 1, 1, 1, '2024-02-09 14:21:35'),
(6, 4, 1, 0, 1000, '2025-04-25 22:22:01');

-- --------------------------------------------------------

--
-- 資料表結構 `token`
--

CREATE TABLE `token` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `token` text NOT NULL,
  `createtime` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `token`
--

INSERT INTO `token` (`id`, `userid`, `token`, `createtime`) VALUES
(8, 4, 'cf4b9c1f5eb31deb9ea41f56faa757b68be9cab8b73f463229df17036bdfa13e44561426', '2023-12-31 18:11:49'),
(9, 1, '13d65aceb53ab1887e7decba787dcad6d8022a39d63e9949b38edfe051a9e03660013272', '2024-01-01 15:54:37'),
(11, 4, 'cf4b9c1f5eb31deb9ea41f56faa757b68be9cab8b73f463229df17036bdfa13e40413224', '2024-01-05 21:30:20'),
(17, 4, 'cf4b9c1f5eb31deb9ea41f56faa757b68be9cab8b73f463229df17036bdfa13e08430676', '2024-02-12 16:42:40'),
(18, 4, 'cf4b9c1f5eb31deb9ea41f56faa757b68be9cab8b73f463229df17036bdfa13e08747351', '2025-04-25 21:33:43'),
(19, 5, '45366849ffd75b4cad725450f0569d11fedbaa6dd747d4480b1d1abb8ba5e30503898819', '2025-04-25 22:32:20');

-- --------------------------------------------------------

--
-- 資料表結構 `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `createtime` datetime NOT NULL,
  `updatetime` datetime NOT NULL,
  `lastlogintime` datetime NOT NULL,
  `blocktime` datetime DEFAULT NULL,
  `blockreason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `createtime`, `updatetime`, `lastlogintime`, `blocktime`, `blockreason`) VALUES
(1, 'player1', 'helloworld1!', '2023-11-09 21:01:35', '2023-11-09 21:01:35', '2023-11-09 21:01:35', NULL, ''),
(2, 'new-player', 'helloworld2!', '2023-12-31 18:06:57', '2023-12-31 18:06:57', '2023-12-31 18:06:57', '2025-03-30 17:47:45', 'You have been blocked by an administrator'),
(3, 'player2', 'helloworld2!', '2023-12-31 18:09:32', '2023-12-31 18:09:32', '2023-12-31 18:09:32', NULL, ''),
(4, 'dev1', 'hellobyte1!', '2023-12-31 18:10:47', '2023-12-31 18:10:47', '2023-12-31 18:10:47', NULL, ''),
(5, 'dev2', 'hellobyte2!', '2023-12-31 18:11:07', '2023-12-31 18:11:07', '2023-12-31 18:11:07', NULL, '');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `gameversion`
--
ALTER TABLE `gameversion`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `score`
--
ALTER TABLE `score`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `token`
--
ALTER TABLE `token`
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
-- 使用資料表自動遞增(AUTO_INCREMENT) `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `game`
--
ALTER TABLE `game`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `gameversion`
--
ALTER TABLE `gameversion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `score`
--
ALTER TABLE `score`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `token`
--
ALTER TABLE `token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
