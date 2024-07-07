-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2023-11-20 11:26:36
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
-- 資料庫： `50regional`
--

-- --------------------------------------------------------

--
-- 資料表結構 `facing`
--

CREATE TABLE `facing` (
  `id` int(11) NOT NULL,
  `projectid` text NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `log`
--

CREATE TABLE `log` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `move` text NOT NULL,
  `movetime` text NOT NULL,
  `ps` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `opinion`
--

CREATE TABLE `opinion` (
  `id` int(11) NOT NULL,
  `userid` text NOT NULL,
  `project_facingid` text NOT NULL,
  `extendid` text NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `fileurl` text NOT NULL,
  `filetype` text NOT NULL,
  `time` text NOT NULL,
  `number` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `plan`
--

CREATE TABLE `plan` (
  `id` int(11) NOT NULL,
  `projectid` text NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `facing_opinionid` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `planfacingopinion`
--

CREATE TABLE `planfacingopinion` (
  `id` int(11) NOT NULL,
  `planid` text NOT NULL,
  `facingid` text NOT NULL,
  `opinionid` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `planscore`
--

CREATE TABLE `planscore` (
  `id` int(11) NOT NULL,
  `userid` text NOT NULL,
  `planid` text NOT NULL,
  `score` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `project`
--

CREATE TABLE `project` (
  `id` int(11) NOT NULL,
  `projectname` text NOT NULL,
  `projectdesciption` text NOT NULL,
  `leader` text NOT NULL,
  `member` text NOT NULL,
  `facingname` text NOT NULL,
  `facingdesciption` text NOT NULL,
  `canpostopinion` text NOT NULL,
  `canplanscore` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `score`
--

CREATE TABLE `score` (
  `id` int(11) NOT NULL,
  `userid` text NOT NULL,
  `opinionid` text NOT NULL,
  `score` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `scoreindex`
--

CREATE TABLE `scoreindex` (
  `id` int(11) NOT NULL,
  `project_facingid` text NOT NULL,
  `index` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `name`) VALUES
(1, 'admin', '1234', '管理者'),
(3, 'user1', '1234', 'tuser1'),
(4, 'user2', '1234', 'tuser2'),
(5, 'user3', '1234', 'tuser3'),
(6, 'user4', '1234', 'tuser4'),
(7, 'user5', '1234', 'tuser5'),
(8, 'user6', '1234', 'tuser6');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `facing`
--
ALTER TABLE `facing`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `opinion`
--
ALTER TABLE `opinion`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `plan`
--
ALTER TABLE `plan`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `planfacingopinion`
--
ALTER TABLE `planfacingopinion`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `planscore`
--
ALTER TABLE `planscore`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `score`
--
ALTER TABLE `score`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `scoreindex`
--
ALTER TABLE `scoreindex`
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
-- 使用資料表自動遞增(AUTO_INCREMENT) `facing`
--
ALTER TABLE `facing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `log`
--
ALTER TABLE `log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `opinion`
--
ALTER TABLE `opinion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `plan`
--
ALTER TABLE `plan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `planfacingopinion`
--
ALTER TABLE `planfacingopinion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `planscore`
--
ALTER TABLE `planscore`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `project`
--
ALTER TABLE `project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `score`
--
ALTER TABLE `score`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `scoreindex`
--
ALTER TABLE `scoreindex`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
