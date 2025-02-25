-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2023-10-14 11:26:06
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
-- 資料庫： `53regional`
--

-- --------------------------------------------------------

--
-- 資料表結構 `coffee`
--

CREATE TABLE `coffee` (
  `id` int(11) NOT NULL,
  `picture` longtext NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `cost` double NOT NULL,
  `date` text NOT NULL,
  `link` text NOT NULL,
  `product` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `coffee`
--

INSERT INTO `coffee` (`id`, `picture`, `name`, `description`, `cost`, `date`, `link`, `product`) VALUES
(2, 'image/jpg_IMG_0174.jpg', 'ifehiowehi', 'isnii', 12345678, '2023-02-11 14:24:36', 'fijeiorjipog', '2'),
(3, '', '嗨嗨嗨', ':)', 527, '2023-02-11 14:59:55', 'https://1234.com/index.php', '1'),
(4, '', 'test', '214323424', 1000, '', '', '1'),
(5, '', 'dwew ergfvsdww', 'wefugiuwqegh', 2147483647, '2023-02-22 18:52:01', 'link', '1'),
(6, '', 'feadsfsdaf', '', 12314564, '', '', '14'),
(7, 'image/image-01.jpg', 'edewd', '121321231231', 12321312, '2023-06-20 20:43:46', '1123231231', '1'),
(8, '', '2', '', 2000, '2023-06-24 19:08:10', '', '2'),
(9, '', '464463', '', 444, '2023-06-24 19:09:47', '444', '13'),
(10, '', '', '', 0, '2023-06-24 19:12:20', '', '1'),
(11, '', '', '', 0, '2023-07-20 00:09:52', '', '1'),
(12, '', '123456', '123456879\r\n', 123, '2023-07-23 17:46:12', '4465645', '2'),
(13, 'image/park0314.jpg', 'rferf', 'ryhtdrgrvd', 1111, '2023-08-06 19:18:49', 'link.link', '1'),
(14, '', '', '', 0, '2023-09-05 10:58:58', '', '1'),
(15, 'image/img.png', 'dfghdfgh', 'hgdhfgdhghfhdfggfh', 3456, '2023-09-05 10:59:36', 'dfghgdh', '17'),
(16, '', '', '', 0, '2023-09-12 11:19:52', '', '14'),
(17, 'image/jvnko.jpg', 'asdfsadf', 'hdgyuiftjterhbk,gdyuierbwhnuibdnmwer bqxzcvyuiokbergjaergjnergjergjergjergjergjergjergjergjnergjnergjnergjnergjnergjziybvcerwbnguyiobkjnw4rtiubwrszfovyiu7ghjkbertgfditvy7ughjergdfuityughjwrafsgyuhjwratyuihgerauiyherg', 123412344123, '2023-09-13 10:49:53', 'wrtgerfzxigywhbetdfgilkjwebtdgfbkwhjesfgk,ujsergsiusergiujkensrgvikluwsejruilbjsergiuhsbedfgiusxdfguiyhbdvuygbseruyhgbseilfvbd.com', '1');

-- --------------------------------------------------------

--
-- 資料表結構 `data`
--

CREATE TABLE `data` (
  `id` int(11) NOT NULL,
  `number` text NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `name` text NOT NULL,
  `permission` text NOT NULL,
  `move1` text NOT NULL,
  `move2` text NOT NULL,
  `movetime` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `data`
--

INSERT INTO `data` (`id`, `number`, `username`, `password`, `name`, `permission`, `move1`, `move2`, `movetime`) VALUES
(1, 'admin', '', '', '', '', '登入', '成功', '2023-04-28 23:16:45'),
(2, '未知', '', '', '', '', '登出', '成功', '2023-04-28 23:17:10'),
(3, '未知', '', '', '', '', '登出', '成功', ''),
(4, '0000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-06-18 10:26:02'),
(5, '0000', 'admin', '1234', '超級管理者', '管理者', '登入', '失敗', '2023-06-18 10:26:03'),
(6, '0000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-06-18 10:26:30'),
(7, '0000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-06-18 10:32:42'),
(8, '0000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-06-18 10:34:09'),
(9, '0000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-06-18 15:30:00'),
(10, '0000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-06-18 15:30:59'),
(11, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-06-19 20:14:34'),
(12, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-06-19 20:56:25'),
(13, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-06-20 20:28:30'),
(14, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-06-21 20:16:35'),
(15, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-06-21 22:13:21'),
(16, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-06-24 15:46:59'),
(17, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-06-24 16:35:42'),
(18, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-06-24 18:42:21'),
(19, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-06-24 19:35:59'),
(20, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-07-07 20:33:54'),
(21, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-07-08 11:41:58'),
(22, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-07-09 22:40:44'),
(23, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-07-09 22:42:00'),
(24, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-07-09 22:42:29'),
(25, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-07-09 22:51:18'),
(26, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-07-09 22:51:46'),
(27, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-07-09 22:52:53'),
(28, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-07-19 23:45:26'),
(29, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-07-19 23:46:22'),
(30, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '失敗', '2023-07-19 23:57:48'),
(31, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-07-20 00:08:54'),
(32, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-07-23 17:24:50'),
(33, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-07-23 17:49:03'),
(34, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-07-24 14:57:41'),
(35, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-07-24 14:59:26'),
(36, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-07-24 15:00:01'),
(37, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-08-01 10:27:20'),
(38, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-08-01 10:28:11'),
(39, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '失敗', '2023-08-01 10:31:43'),
(40, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '失敗', '2023-08-01 10:32:17'),
(41, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '失敗', '2023-08-01 10:32:18'),
(42, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '失敗', '2023-08-01 10:33:09'),
(43, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-08-01 10:35:16'),
(44, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-08-01 10:35:32'),
(45, '未知', '', '', '', '', '登出', '成功', '2023-08-01 10:41:52'),
(46, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-08-05 14:51:30'),
(47, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-08-05 14:51:50'),
(48, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-08-07 15:02:10'),
(49, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-08-07 15:18:35'),
(50, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-09-01 14:48:22'),
(51, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-09-01 17:02:07'),
(52, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-09-01 17:08:00'),
(53, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-09-01 17:11:49'),
(54, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-09-05 10:58:21'),
(55, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-09-08 09:40:21'),
(56, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-09-09 15:45:38'),
(57, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-09-09 15:45:49'),
(58, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-09-11 11:23:20'),
(59, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-09-11 11:25:49'),
(60, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-09-13 10:49:03'),
(61, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-09-13 12:56:47'),
(62, '00006', 'test', '1234', 'testa', '一般使用者', '登入', '失敗', '2023-09-13 12:57:33'),
(63, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-09-13 14:19:42'),
(64, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-09-16 15:24:32'),
(65, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '失敗', '2023-09-26 17:07:45'),
(66, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '失敗', '2023-09-26 17:09:43'),
(67, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-09-27 19:31:32'),
(68, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-09-30 11:45:37'),
(69, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-09-30 11:56:04'),
(70, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-10-04 16:34:55'),
(71, '未知', '', '', '', '', '登出', '成功', '2023-10-04 16:43:51'),
(72, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-10-04 18:31:47'),
(73, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-10-04 18:31:54'),
(74, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-10-05 20:22:46'),
(75, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-10-05 20:23:30'),
(76, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-10-05 20:24:20'),
(77, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-10-05 20:25:12'),
(78, '00008', 'a', '1', 'aa', '管理者', '登入', '成功', '2023-10-05 20:25:35'),
(79, '00008', 'a', '1', 'aa', '管理者', '登出', '成功', '2023-10-05 21:06:02'),
(80, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-10-05 21:06:33'),
(81, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-10-05 21:15:40'),
(82, '00000', 'admin', '1234', '超級管理者', '管理者', '登出', '成功', '2023-10-05 21:16:33'),
(83, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-10-06 22:07:19'),
(84, '未知', '', '', '', '', '登出', '成功', '2023-10-06 22:58:42'),
(85, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-10-09 11:41:22'),
(86, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-10-09 11:41:26'),
(87, '未知', '', '', '', '', '登出', '成功', '2023-10-09 19:18:56'),
(88, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-10-09 19:33:48'),
(89, '未知', '', '', '', '', '登出', '成功', '2023-10-09 19:33:54'),
(90, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', 'time'),
(91, '未知', '', '', '', '', '登出', '成功', '2023-10-09 19:36:40'),
(92, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-10-09 19:36:54'),
(93, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-10-11 16:51:44'),
(94, '未知', '', '', '', '', '登出', '成功', '2023-10-11 16:53:39'),
(95, '00000', 'admin', '1234', '超級管理者', '管理者', '登入', '成功', '2023-10-14 10:07:00'),
(96, '未知', '', '', '', '', '登出', '成功', '2023-10-14 10:07:38'),
(97, '未知', '', '', '', '', '登出', '成功', '2023-10-14 10:26:49'),
(98, '未知', '', '', '', '', '登出', '成功', '2023-10-14 10:27:13');

-- --------------------------------------------------------

--
-- 資料表結構 `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `cost` text NOT NULL,
  `date` text NOT NULL,
  `link` text NOT NULL,
  `introduction` text NOT NULL,
  `picture` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `product`
--

INSERT INTO `product` (`id`, `name`, `cost`, `date`, `link`, `introduction`, `picture`) VALUES
(1, 'grid-column: 1/50;grid-row: 3/5;', 'grid-column: 50/100;grid-row: 3/5;', 'grid-column: 50/100;grid-row: 13/15;', 'grid-column: 50/100;grid-row: 16/18;', 'grid-column: 50/100;grid-row: 6/12;', 'grid-column: 1/50;grid-row: 6/18;'),
(2, 'grid-column: 50/100;grid-row: 3/5;', 'grid-column: 50/100;grid-row: 16/18;', 'grid-column: 50/100;grid-row: 13/15;', 'grid-column: 1/50;grid-row: 16/18;', 'grid-column: 50/100;grid-row: 6/12;', 'grid-column: 1/50;grid-row: 3/15;'),
(13, 'grid-column: 50/100;grid-row: 3/5', 'grid-column: 1/50;grid-row: 3/5', 'grid-column: 50/100;grid-row: 16/18', 'grid-column: 50/100;grid-row: 6/8', 'grid-column: 50/100;grid-row: 9/15', 'grid-column: 1/50;grid-row: 6/18'),
(14, 'grid-column: 50/100;grid-row: 12/14', 'grid-column: 50/100;grid-row: 6/8', 'grid-column: 50/100;grid-row: 9/11', 'grid-column: 50/100;grid-row: 3/5', 'grid-column: 1/50;grid-row: 15/21', 'grid-column: 1/50;grid-row: 3/15'),
(15, 'grid-column: 50/100;grid-row: 3/5', 'grid-column: 50/100;grid-row: 6/8', 'grid-column: 50/100;grid-row: 31/33', 'grid-column: 50/100;grid-row: 28/30', 'grid-column: 50/100;grid-row: 9/15', 'grid-column: 50/100;grid-row: 16/28'),
(16, 'grid-column: 50/100;grid-row: 15/17', 'grid-column: 1/50;grid-row: 3/5', 'grid-column: 50/100;grid-row: 28/30', 'grid-column: 50/100;grid-row: 25/27', 'grid-column: 50/100;grid-row: 18/24', 'grid-column: 50/100;grid-row: 3/15'),
(17, 'grid-column: 1/50;grid-row: 15/17', 'grid-column: 1/50;grid-row: 18/20', 'grid-column: 1/50;grid-row: 28/30', 'grid-column: 1/50;grid-row: 31/33', 'grid-column: 1/50;grid-row: 21/27', 'grid-column: 1/50;grid-row: 3/15'),
(18, 'grid-column: 1/50;grid-row: 3/5', 'grid-column: 50/100;grid-row: 6/8', 'grid-column: 1/50;grid-row: 18/20', 'grid-column: 50/100;grid-row: 3/5', 'grid-column: 50/100;grid-row: 9/15', 'grid-column: 1/50;grid-row: 6/18'),
(19, 'grid-column: 50/100;grid-row: 3/5', 'grid-column: 50/100;grid-row: 6/8', 'grid-column: 50/100;grid-row: 9/11', 'grid-column: 50/100;grid-row: 12/14', 'grid-column: 50/100;grid-row: 15/21', 'grid-column: 50/100;grid-row: 22/34');

-- --------------------------------------------------------

--
-- 資料表結構 `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `name` text NOT NULL,
  `number` text NOT NULL,
  `permission` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `name`, `number`, `permission`) VALUES
(1, 'admin', '1234', '超級管理者', '00000', '管理者'),
(7, 'test', '1234', 'testa', '00006', '一般使用者'),
(8, 'b', 'b', 'b', '00007', '一般使用者'),
(9, 'a', '1', 'aa', '00008', '管理者');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `coffee`
--
ALTER TABLE `coffee`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `data`
--
ALTER TABLE `data`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `product`
--
ALTER TABLE `product`
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
-- 使用資料表自動遞增(AUTO_INCREMENT) `coffee`
--
ALTER TABLE `coffee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `data`
--
ALTER TABLE `data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
