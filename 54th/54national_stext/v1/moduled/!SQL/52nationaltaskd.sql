-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2023-05-13 07:42:12
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

--
-- 傾印資料表的資料 `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `post_id`, `content`, `created_at`) VALUES
(1, 2, 1, 'Test Comments', '2022-05-28 12:28:54');

-- --------------------------------------------------------

--
-- 資料表結構 `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL COMMENT '發布者',
  `content` varchar(300) NOT NULL COMMENT '內文',
  `type` enum('public','only_follow','only_self') NOT NULL COMMENT '貼文的類型',
  `tag` text DEFAULT NULL,
  `location` text DEFAULT NULL,
  `place_lat` decimal(10,5) DEFAULT NULL COMMENT '地點的經度',
  `place_lng` decimal(10,5) DEFAULT NULL COMMENT '地點的緯度',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `posts`
--

INSERT INTO `posts` (`id`, `author_id`, `content`, `type`, `tag`, `location`, `place_lat`, `place_lng`, `created_at`, `updated_at`) VALUES
(1, 1, 'Hello World', 'public', NULL, NULL, NULL, NULL, '2022-05-28 11:27:59', NULL),
(2, 1, 'Taipei 101', 'public', NULL, NULL, 25.03366, 121.56224, '2022-05-28 11:28:50', NULL);

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

--
-- 傾印資料表的資料 `post_images`
--

INSERT INTO `post_images` (`id`, `post_id`, `width`, `height`, `filename`, `created_at`) VALUES
(1, 1, 800, 800, '/img/aonwkdasf.png', '2022-05-28 11:29:43'),
(2, 1, 810, 810, '/img/inwast2l_A.png', '2022-05-28 11:30:10'),
(3, 2, 1920, 1280, '/img/101.png', '2022-05-28 11:30:26');

-- --------------------------------------------------------

--
-- 資料表結構 `post_tags`
--

CREATE TABLE `post_tags` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `post_tags`
--

INSERT INTO `post_tags` (`id`, `post_id`, `tag_id`) VALUES
(1, 2, 3),
(2, 1, 1),
(3, 2, 2);

-- --------------------------------------------------------

--
-- 資料表結構 `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT '標籤名稱',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `tags`
--

INSERT INTO `tags` (`id`, `name`, `created_at`) VALUES
(1, 'Taipei', '2022-05-28 11:31:04'),
(2, 'Taiwan', '2022-05-28 11:31:04'),
(3, '2022', '2022-05-28 12:27:08');

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
(1, 'admin@localhost', '$2y$12$qgQuxp1wGMvFAm93Kk0KpuXxK3KT4fVRW87GPcayWmqfCp4F84jJq Copy', 'admin', '/image/admin.png', '7ba684f146c9445720e4b9a8d6ed775de4804bc2dbe01fb6490b8cce05db9f43', 'ADMIN', '2022-05-28 08:46:32', NULL),
(2, 'provider@localhost', '$2y$11$X7axSOlW5WX7tHHWfoMPveayXu4A9ZIt3YW4WBxwIwPeFU6m7Hepu Copy', 'provider', '/image/test.jpg', NULL, 'USER', '2022-05-28 08:47:09', NULL);

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

--
-- 傾印資料表的資料 `user_follows`
--

INSERT INTO `user_follows` (`id`, `user_id`, `follow_user_id`, `created_at`) VALUES
(1, 1, 2, '2022-05-28 12:28:23');

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
-- 傾印資料表的資料 `user_likes`
--

INSERT INTO `user_likes` (`id`, `user_id`, `post_id`, `created_at`) VALUES
(1, 2, 1, '2022-05-28 12:28:15');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- 資料表索引 `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`);

--
-- 資料表索引 `post_images`
--
ALTER TABLE `post_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- 資料表索引 `post_tags`
--
ALTER TABLE `post_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- 資料表索引 `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `token` (`access_token`);

--
-- 資料表索引 `user_follows`
--
ALTER TABLE `user_follows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `follow_user_id` (`follow_user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- 資料表索引 `user_likes`
--
ALTER TABLE `user_likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `post_images`
--
ALTER TABLE `post_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `post_tags`
--
ALTER TABLE `post_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user_follows`
--
ALTER TABLE `user_follows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user_likes`
--
ALTER TABLE `user_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- 資料表的限制式 `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);

--
-- 資料表的限制式 `post_images`
--
ALTER TABLE `post_images`
  ADD CONSTRAINT `post_images_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`);

--
-- 資料表的限制式 `post_tags`
--
ALTER TABLE `post_tags`
  ADD CONSTRAINT `post_tags_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  ADD CONSTRAINT `post_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`);

--
-- 資料表的限制式 `user_follows`
--
ALTER TABLE `user_follows`
  ADD CONSTRAINT `user_follows_ibfk_1` FOREIGN KEY (`follow_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_follows_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- 資料表的限制式 `user_likes`
--
ALTER TABLE `user_likes`
  ADD CONSTRAINT `user_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  ADD CONSTRAINT `user_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
