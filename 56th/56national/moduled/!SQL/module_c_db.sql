-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-11-16 16:22:11
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `04_module_c`
--

-- --------------------------------------------------------

--
-- 資料表結構 `albums`
--

CREATE TABLE `albums` (
  `album_id` int(11) NOT NULL,
  `publisher_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `artist` varchar(50) NOT NULL,
  `release_year` year(4) DEFAULT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `albums`
--

INSERT INTO `albums` (`album_id`, `publisher_id`, `title`, `artist`, `release_year`, `genre`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Morning Vibes', 'The Beatles', '2024', 'Relax', 'A relaxing collection of pop hits to start your day.', '2025-11-13 01:41:32', '2025-11-16 09:37:02'),
(2, 1, 'Chill Nights', 'Chris', '2023', 'Lo-Fi', 'Smooth lo-fi tracks perfect for night study sessions.', '2025-11-13 01:41:32', '2025-11-13 01:56:28'),
(3, 1, 'Exodus', 'Bob Marley & The Wailers', '1977', 'Reggae', 'A landmark album defining reggae music for a global audience, blending spiritual and political themes.', '2025-11-16 09:36:47', '2025-11-16 09:36:47'),
(4, 1, 'I Never Loved a Man the Way I Love You', 'Aretha Franklin', '1967', 'Soul', 'The breakthrough album for the Queen of Soul, featuring the iconic anthem \"Respect\".', '2025-11-16 09:36:47', '2025-11-16 10:14:34'),
(5, 1, 'Kind of Blue', 'Miles Davis', '1959', 'Modal Jazz', 'Often cited as the greatest jazz album ever; a masterpiece of modal improvisation. (Note: Modal Jazz, not general Jazz)', '2025-11-16 09:36:47', '2025-11-16 10:14:36'),
(6, 1, 'Live at the Regal', 'B.B. King', '1965', 'Blues', 'Considered one of the greatest live blues recordings, capturing B.B. King\'s electrifying performance.', '2025-11-16 09:36:47', '2025-11-16 09:36:47'),
(7, 1, 'Master of Puppets', 'Metallica', '1986', 'Metal', 'A defining album of the thrash metal genre, known for its complex compositions and dark lyrical content.', '2025-11-16 09:36:47', '2025-11-16 10:14:40'),
(8, 1, 'Ramones', 'Ramones', '1976', 'Punk', 'The debut album that laid the groundwork for punk rock with its fast, minimalist, and aggressive sound.', '2025-11-16 09:36:47', '2025-11-16 10:14:42'),
(9, 1, 'The Freewheelin\' Bob Dylan', 'Bob Dylan', '1963', 'Folk', 'Established Dylan as a seminal songwriter, filled with protest songs and poetic lyricism.', '2025-11-16 09:36:47', '2025-11-16 09:36:47'),
(10, 1, 'Confessions', 'Usher', '2004', 'R&B', 'A dominant R&B album of the 2000s, exploring themes of infidelity and personal turmoil.', '2025-11-16 09:36:47', '2025-11-16 10:14:44'),
(11, 1, 'Mothership Connection', 'Parliament', '1975', 'Funk', 'A definitive P-Funk album, blending deep grooves with Afrofuturist themes and concepts.', '2025-11-16 09:39:01', '2025-11-16 10:14:45');

-- --------------------------------------------------------

--
-- 資料表結構 `labels`
--

CREATE TABLE `labels` (
  `label_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `labels`
--

INSERT INTO `labels` (`label_id`, `name`) VALUES
(1, 'Pop'),
(2, 'Rock'),
(3, 'Hip-Hop'),
(4, 'Electronic'),
(5, 'Jazz'),
(6, 'Classical'),
(7, 'Chill'),
(8, 'Country');

-- --------------------------------------------------------

--
-- 資料表結構 `songs`
--

CREATE TABLE `songs` (
  `song_id` int(11) NOT NULL,
  `album_id` int(11) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `duration_seconds` int(11) DEFAULT NULL,
  `cover_image_path` varchar(255) DEFAULT NULL,
  `is_cover` int(11) NOT NULL DEFAULT 0,
  `lyrics` text DEFAULT NULL,
  `track_order` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `songs`
--

INSERT INTO `songs` (`song_id`, `album_id`, `title`, `duration_seconds`, `cover_image_path`, `is_cover`, `lyrics`, `track_order`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Sunrise Smile', 210, NULL, 0, 'Feel the light, shining bright...', 1, NULL, '2025-11-13 01:41:32', '2025-11-13 01:42:45'),
(2, 1, 'Coffee Break', 185, NULL, 0, 'Take a sip, slow it down...', 2, NULL, '2025-11-13 01:41:32', '2025-11-13 01:42:47'),
(3, 2, 'Midnight Flow', 240, NULL, 0, 'Dreams in rhythm, beats so slow...', 1, NULL, '2025-11-13 01:41:32', '2025-11-13 01:42:51'),
(4, 2, 'Neon Dreams', 200, NULL, 0, 'Lights flash, hearts race...', 2, NULL, '2025-11-13 01:41:32', '2025-11-13 01:42:49'),
(5, 1, 'Beyond the Horizon', 245, NULL, 0, 'Waking up to the sound of silence. A new day is dawning bright.', 3, '2025-11-16 09:45:03', '2025-11-16 09:44:35', '2025-11-16 09:45:06'),
(6, 3, 'First Light', 198, NULL, 0, 'The city sleeps, the air is cold. Waiting for the sun to rise.', 1, NULL, '2025-11-16 09:44:35', '2025-11-16 09:44:35'),
(7, 2, 'Shadows Dance', 212, NULL, 0, 'We were moving in the shadows. Two hearts beating as one in the night.', 3, NULL, '2025-11-16 09:44:35', '2025-11-16 09:44:35'),
(8, 5, 'Electric Dreams', 301, NULL, 0, 'A digital embrace, a fleeting smile. Lost in the code for a little while.', 1, NULL, '2025-11-16 09:44:35', '2025-11-16 09:44:35'),
(9, 1, 'Echoes in the Rain', 220, NULL, 0, 'Every drop a memory. Washing away the pain.', 4, '2025-11-16 09:44:55', '2025-11-16 09:44:35', '2025-11-16 09:44:57'),
(10, 7, 'Starlight', 255, NULL, 0, 'Caught in the glow of a distant star. Wondering where you are.', 1, NULL, '2025-11-16 09:44:35', '2025-11-16 09:44:35'),
(11, 8, 'Ocean Deep', 310, NULL, 0, 'How deep is this ocean of blue? Deeper than I ever knew.', 1, NULL, '2025-11-16 09:44:35', '2025-11-16 09:44:35'),
(12, 2, 'Midnight Run', 188, NULL, 0, 'On the midnight run, til the morning comes. We won\'t stop for anyone.', 4, NULL, '2025-11-16 09:44:35', '2025-11-16 09:44:35'),
(13, 5, 'Afterimage', 273, NULL, 0, 'Just a fading afterimage in my mind. A ghost of what we left behind.', 2, '2025-11-16 09:44:52', '2025-11-16 09:44:35', '2025-11-16 09:44:54'),
(14, 10, 'Fireside', 195, NULL, 0, 'Sitting by the fireside, watching embers glow. The night is long and the air is cold.', 1, NULL, '2025-11-16 09:44:35', '2025-11-16 09:44:35'),
(15, 1, 'The Last Stand', 233, NULL, 0, 'This is the last stand we take. For everything we believe.', 5, NULL, '2025-11-16 09:44:35', '2025-11-16 09:44:35'),
(16, 11, 'Whisperwind', 205, NULL, 0, 'The whisperwind calls my name. A soft refrain, again and again.', 1, NULL, '2025-11-16 09:44:35', '2025-11-16 09:44:35'),
(17, 3, 'Paper Wings', 241, NULL, 0, 'Flying high on paper wings. So close to the sun and other things.', 2, NULL, '2025-11-16 09:44:35', '2025-11-16 09:44:35'),
(18, 8, 'Reflections', 199, NULL, 0, 'Looking at the man in the mirror. He looks back with eyes of sorrow.', 2, '2025-11-16 09:44:43', '2025-11-16 09:44:35', '2025-11-16 09:44:51'),
(19, 5, 'Nightfall', 280, NULL, 0, 'As the nightfall comes. The city lights begin to hum.', 3, NULL, '2025-11-16 09:44:35', '2025-11-16 09:44:35'),
(20, 4, 'Solitude', 264, NULL, 0, 'In this quiet place, I find my solitude. No one else, just me and you.', 1, NULL, '2025-11-16 09:44:35', '2025-11-16 09:44:35');

-- --------------------------------------------------------

--
-- 資料表結構 `song_labels`
--

CREATE TABLE `song_labels` (
  `song_label_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `song_labels`
--

INSERT INTO `song_labels` (`song_label_id`, `song_id`, `label_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 5, 2),
(5, 10, 1),
(6, 3, 7),
(7, 18, 4),
(8, 5, 3),
(9, 12, 8),
(10, 20, 5);

-- --------------------------------------------------------

--
-- 資料表結構 `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','publisher','user') NOT NULL DEFAULT 'user',
  `is_banned` tinyint(1) NOT NULL DEFAULT 0,
  `token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `email`, `role`, `is_banned`, `token`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$12$v1rU4ihh/IoNRh2LV7FwQeOqfetQFiu1MnoPDOpwZLkEejHcQyZVi', 'admin@web.wsa', 'admin', 0, '21232f297a57a5a743894a0e4a801fc3', '2025-11-13 01:41:32', '2025-11-13 02:23:34'),
(2, 'user1', '$2y$12$4xHVT/l/6g1oEpYKy6mUy./ozv19JAbKCDfOnmzeHxmpVvomQCcv.', 'user1@web.wsa', 'user', 0, '24c9e15e52afc47c225b757e7bee1f9d', '2025-11-13 01:41:32', '2025-11-16 00:36:48'),
(3, 'user2', '$2y$12$kxdorH84/9XOs/2BL9.wteP1CbgxaQlrrSMWeK83m0Dj.CNCRy3xe', 'user2@web.wsa', 'user', 0, '7e58d63b60197ceb55a1c487989a3720', '2025-11-13 01:41:32', '2025-11-16 08:41:02'),
(4, 'user3', '$2y$12$p8XKXW9kbIv2tzud54Gm9Ov5oeuUoNsHsEJytlnBOTcJyhan3JWYi', 'user3@web.wsa', 'user', 1, NULL, '2025-11-13 01:41:32', '2025-11-16 08:37:04');

-- --------------------------------------------------------

--
-- 資料表結構 `user_view_logs`
--

CREATE TABLE `user_view_logs` (
  `user_view_log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `user_view_logs`
--

INSERT INTO `user_view_logs` (`user_view_log_id`, `user_id`, `song_id`) VALUES
(1, 1, 10),
(2, 3, 5),
(3, 2, 18),
(4, 1, 10),
(5, 4, 20),
(6, 3, 2),
(7, 1, 7),
(8, 2, 11),
(9, 2, 18),
(10, 4, 1),
(11, 3, 5),
(12, 1, 15),
(13, 4, 9),
(14, 2, 11),
(15, 1, 3),
(16, 3, 19),
(17, 4, 7),
(18, 1, 10),
(19, 3, 14),
(20, 2, 5);

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `albums`
--
ALTER TABLE `albums`
  ADD PRIMARY KEY (`album_id`),
  ADD KEY `publisher_id` (`publisher_id`);

--
-- 資料表索引 `labels`
--
ALTER TABLE `labels`
  ADD PRIMARY KEY (`label_id`);

--
-- 資料表索引 `songs`
--
ALTER TABLE `songs`
  ADD PRIMARY KEY (`song_id`),
  ADD KEY `album_id` (`album_id`);

--
-- 資料表索引 `song_labels`
--
ALTER TABLE `song_labels`
  ADD PRIMARY KEY (`song_label_id`),
  ADD KEY `song_id` (`song_id`),
  ADD KEY `label_id` (`label_id`);

--
-- 資料表索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- 資料表索引 `user_view_logs`
--
ALTER TABLE `user_view_logs`
  ADD PRIMARY KEY (`user_view_log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `song_id` (`song_id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `albums`
--
ALTER TABLE `albums`
  MODIFY `album_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `labels`
--
ALTER TABLE `labels`
  MODIFY `label_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `songs`
--
ALTER TABLE `songs`
  MODIFY `song_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `song_labels`
--
ALTER TABLE `song_labels`
  MODIFY `song_label_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user_view_logs`
--
ALTER TABLE `user_view_logs`
  MODIFY `user_view_log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `albums`
--
ALTER TABLE `albums`
  ADD CONSTRAINT `albums_ibfk_1` FOREIGN KEY (`publisher_id`) REFERENCES `users` (`user_id`);

--
-- 資料表的限制式 `songs`
--
ALTER TABLE `songs`
  ADD CONSTRAINT `songs_ibfk_1` FOREIGN KEY (`album_id`) REFERENCES `albums` (`album_id`);

--
-- 資料表的限制式 `song_labels`
--
ALTER TABLE `song_labels`
  ADD CONSTRAINT `song_labels_ibfk_1` FOREIGN KEY (`song_id`) REFERENCES `songs` (`song_id`),
  ADD CONSTRAINT `song_labels_ibfk_2` FOREIGN KEY (`label_id`) REFERENCES `labels` (`label_id`);

--
-- 資料表的限制式 `user_view_logs`
--
ALTER TABLE `user_view_logs`
  ADD CONSTRAINT `user_view_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_view_logs_ibfk_2` FOREIGN KEY (`song_id`) REFERENCES `songs` (`song_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
