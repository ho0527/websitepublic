-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2023-09-08 10:41:52
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
-- 資料庫： `50nationalmodulec`
--

-- --------------------------------------------------------

--
-- 資料表結構 `blocklist`
--

CREATE TABLE `blocklist` (
  `id` int(11) NOT NULL,
  `userid` text NOT NULL,
  `from` text NOT NULL,
  `to` text NOT NULL,
  `reason` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `createdat` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `category`
--

INSERT INTO `category` (`id`, `title`, `createdat`, `updatedat`) VALUES
(1, '音樂', '2023-09-05 12:32:17', '2023-09-05 12:32:17'),
(2, '電影', '2023-09-05 12:32:17', '2023-09-05 12:32:17'),
(3, '恐怖', '2023-09-05 12:32:17', '2023-09-05 12:32:17'),
(4, '諷刺', '2023-09-05 12:32:17', '2023-09-05 12:32:17'),
(5, '教育', '2023-09-05 12:32:17', '2023-09-05 12:32:17'),
(6, '愛情', '2023-09-05 12:32:17', '2023-09-05 12:32:17'),
(7, '動作', '2023-09-05 12:32:17', '2023-09-05 12:32:17'),
(8, '西方', '2023-09-05 12:32:17', '2023-09-05 12:32:17'),
(9, '東方', '2023-09-05 12:32:17', '2023-09-05 12:32:17');

-- --------------------------------------------------------

--
-- 資料表結構 `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `userid` text NOT NULL,
  `videoid` text NOT NULL,
  `replyid` text DEFAULT NULL,
  `text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `comment`
--


-- --------------------------------------------------------

--
-- 資料表結構 `like`
--

CREATE TABLE `like` (
  `id` int(11) NOT NULL,
  `userid` text NOT NULL,
  `videoid` text NOT NULL,
  `createdat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `like`
--


-- --------------------------------------------------------

--
-- 資料表結構 `play`
--

CREATE TABLE `play` (
  `id` int(11) NOT NULL,
  `userid` text NOT NULL,
  `videoid` text NOT NULL,
  `createdat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `playlist`
--

CREATE TABLE `playlist` (
  `id` int(11) NOT NULL,
  `userid` text NOT NULL,
  `title` text NOT NULL,
  `videolist` text NOT NULL,
  `createdat` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `playlist`
--


-- --------------------------------------------------------

--
-- 資料表結構 `program`
--

CREATE TABLE `program` (
  `id` int(11) NOT NULL,
  `userid` text NOT NULL,
  `url` text NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `authorizedstart` text NOT NULL,
  `authorizedend` text NOT NULL,
  `createdat` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `programvideolist`
--

CREATE TABLE `programvideolist` (
  `id` int(11) NOT NULL,
  `programid` text NOT NULL,
  `videoid` text NOT NULL,
  `title` text NOT NULL,
  `createdat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` text NOT NULL,
  `password` text NOT NULL,
  `nickname` text NOT NULL,
  `permission` text NOT NULL,
  `accesstoken` text DEFAULT NULL,
  `disabled` text NOT NULL,
  `createdat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `nickname`, `permission`, `accesstoken`, `disabled`, `createdat`) VALUES
(1, 'admin@localhost', 'adminpass', 'admin', 'admin', NULL, 'false', '2023-09-05 10:21:15'),
(2, 'user1@localhost', 'user1pass', 'user1', 'USER', NULL, 'false', '2023-09-05 10:21:15'),
(3, 'user2@localhost', 'user2pass', 'user2', 'USER', NULL, 'false', '2023-09-05 10:21:15'),
(4, 'user3@localhost', 'user3pass', 'user3', 'USER', NULL, 'false', '2023-09-05 10:21:15');

-- --------------------------------------------------------

--
-- 資料表結構 `video`
--

CREATE TABLE `video` (
  `id` int(11) NOT NULL,
  `userid` text NOT NULL,
  `categoryid` text NOT NULL,
  `url` text NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `visibility` text NOT NULL,
  `count` int(11) NOT NULL,
  `duration` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `video`
--

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `blocklist`
--
ALTER TABLE `blocklist`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `like`
--
ALTER TABLE `like`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `play`
--
ALTER TABLE `play`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `playlist`
--
ALTER TABLE `playlist`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `program`
--
ALTER TABLE `program`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `programvideolist`
--
ALTER TABLE `programvideolist`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `video`
--
ALTER TABLE `video`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `blocklist`
--
ALTER TABLE `blocklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `like`
--
ALTER TABLE `like`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `play`
--
ALTER TABLE `play`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `playlist`
--
ALTER TABLE `playlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `program`
--
ALTER TABLE `program`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `programvideolist`
--
ALTER TABLE `programvideolist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `video`
--
ALTER TABLE `video`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
