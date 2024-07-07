-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2023-09-09 15:25:54
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
-- 資料庫： `52nationaltaskd`
--

-- --------------------------------------------------------

--
-- 資料表結構 `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '留言的使用者',
  `post_id` int(11) NOT NULL COMMENT '所屬貼文',
  `content` varchar(300) NOT NULL COMMENT '內容',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL COMMENT '發布者',
  `content` varchar(300) NOT NULL COMMENT '內文',
  `type` enum('public','only_follow','only_self') NOT NULL COMMENT '貼文的類型',
  `tag` text NOT NULL,
  `location` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `post_images`
--

CREATE TABLE `post_images` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL COMMENT '所屬貼文',
  `width` int(11) NOT NULL COMMENT '圖片寬度',
  `height` int(11) NOT NULL COMMENT '圖片高度',
  `filename` varchar(1024) NOT NULL COMMENT '檔案名稱',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `post_tags`
--

CREATE TABLE `post_tags` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT '標籤名稱',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL COMMENT 'Email',
  `password` varchar(255) DEFAULT NULL COMMENT '密碼',
  `nickname` varchar(255) NOT NULL COMMENT '暱稱',
  `profile_image` varchar(1024) NOT NULL,
  `access_token` char(64) DEFAULT NULL COMMENT 'Login Token',
  `type` enum('USER','ADMIN') NOT NULL DEFAULT 'USER' COMMENT '身分',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '建立時間',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='使用者';

--
-- 傾印資料表的資料 `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `nickname`, `profile_image`, `access_token`, `type`, `created_at`, `updated_at`) VALUES
(1, 'admin@web.tw', '$2y$10$btOR.i/pG0bmBv1meoyHk.f/kUA2TDjBvfegy5TftjFFeypU9fyzK', 'admin', 'image/k0hm8SG3oYChzEEFcMbFCAeGdGaD93iqd3bA6IYT.jpg', NULL, 'ADMIN', '2023-09-01 15:00:52', NULL),
(2, 'user@web.tw', '$2y$10$D45MBatqN/Y4NWj58WOYye3zeIjkWeVXQqJdHTAVmYORFJdNiTYMi', 'user', 'image/c2fAQP6njR3ZvieyeUUGASJnwdEm5DeL8yI4gITl.jpg', NULL, 'USER', '2023-09-01 15:01:37', NULL),
(3, 'test@web.tw', '$2y$10$/lIL3YxDtS7ehpH.0QOlpe1eywLnqX6XbpkXrpz8cSnN6LIYnbikO', 'test', 'image/saF5VrhM0xgaQNYmWjzwS6uLzbStbx1Jzjy0dym6.jpg', NULL, 'USER', '2023-09-01 15:03:04', NULL),
(4, 'test2@web.tw', '$2y$10$oFAe6kN4n0RtU0447VDyu.K2qT1XiiUHABpm7l44j0YxijGHE7UmW', 'test2', 'image/kYIwAVPldyloKSZIKOiodIN0wbgJvGivd0Y8APSM.jpg', NULL, 'USER', '2023-09-01 15:04:49', NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `user_follows`
--

CREATE TABLE `user_follows` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `follow_user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `user_likes`
--

CREATE TABLE `user_likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `post_images`
--
ALTER TABLE `post_images`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `post_tags`
--
ALTER TABLE `post_tags`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `user_follows`
--
ALTER TABLE `user_follows`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `user_likes`
--
ALTER TABLE `user_likes`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `post_images`
--
ALTER TABLE `post_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `post_tags`
--
ALTER TABLE `post_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user_follows`
--
ALTER TABLE `user_follows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user_likes`
--
ALTER TABLE `user_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
