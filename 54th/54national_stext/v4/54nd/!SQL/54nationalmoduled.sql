-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2024-05-10 18:07:38
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
-- 資料庫： `54nationalmoduled`
--

-- --------------------------------------------------------

--
-- 資料表結構 `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL COMMENT '任務id',
  `task_type_id` int(11) NOT NULL COMMENT '任務類型id',
  `user_id` int(11) NOT NULL COMMENT '任務擁有者id',
  `worker_id` int(11) DEFAULT NULL COMMENT '執行此任務的worker id',
  `status` enum('pending','processing','finished','failed','canceled') NOT NULL COMMENT '狀態',
  `result` text DEFAULT NULL COMMENT '結果',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新的時間',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '建立的時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `tasks`
--

INSERT INTO `tasks` (`id`, `task_type_id`, `user_id`, `worker_id`, `status`, `result`, `updated_at`, `created_at`) VALUES
(1, 3, 3, 2, 'finished', 'storage/result/1.jpg', '2024-02-22 13:55:30', '2024-02-22 13:55:30'),
(2, 2, 2, 1, 'processing', NULL, '2024-05-08 13:06:17', '2024-02-22 13:59:12');

-- --------------------------------------------------------

--
-- 資料表結構 `task_inputs`
--

CREATE TABLE `task_inputs` (
  `id` int(11) NOT NULL COMMENT '任務輸入欄位id',
  `task_id` int(11) NOT NULL COMMENT '任務id',
  `name` varchar(255) NOT NULL COMMENT '欄位名稱',
  `type` enum('string','number','boolean') NOT NULL COMMENT '欄位類型',
  `value` text NOT NULL COMMENT '值\r\nBoolean時使用"true"和"false"儲存'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `task_inputs`
--

INSERT INTO `task_inputs` (`id`, `task_id`, `name`, `type`, `value`) VALUES
(1, 1, 'width', 'number', '512'),
(2, 1, 'height', 'number', '512'),
(3, 1, 'prompt', 'string', 'Taipei 101'),
(4, 2, 'code_interpreter', 'boolean', 'true'),
(5, 2, 'text', 'string', 'Hello World !'),
(6, 2, 'max_length', 'number', '128');

-- --------------------------------------------------------

--
-- 資料表結構 `task_types`
--

CREATE TABLE `task_types` (
  `id` int(11) NOT NULL COMMENT '任務類型id',
  `name` varchar(255) NOT NULL COMMENT '任務名稱',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '建立的時間',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '刪除的時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `task_types`
--

INSERT INTO `task_types` (`id`, `name`, `created_at`, `deleted_at`) VALUES
(1, 'very_good_gpt3', '2024-02-22 13:37:03', NULL),
(2, 'gpt345', '2024-02-24 13:37:03', NULL),
(3, 'dall_e_3', '2024-02-26 13:38:03', NULL),
(4, 'gpt2', '2024-02-28 13:38:03', '2024-02-29 13:38:23'),
(5, 'gemini_1_0', '2024-02-22 13:40:59', NULL),
(6, 'testing', '2024-05-05 15:37:08', NULL),
(7, 'testing1', '2024-05-08 13:45:27', NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `task_type_inputs`
--

CREATE TABLE `task_type_inputs` (
  `id` int(11) NOT NULL COMMENT '任務類型輸入id',
  `task_type_id` int(11) NOT NULL COMMENT '任務類型id',
  `name` varchar(255) NOT NULL COMMENT '欄位名稱',
  `type` enum('string','number','boolean') NOT NULL COMMENT '資料類型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `task_type_inputs`
--

INSERT INTO `task_type_inputs` (`id`, `task_type_id`, `name`, `type`) VALUES
(1, 3, 'width', 'number'),
(2, 3, 'height', 'number'),
(3, 3, 'prompt', 'string'),
(4, 4, 'text', 'string'),
(16, 6, 'test_string2', 'string'),
(17, 6, 'test_number2', 'number'),
(18, 6, 'test_bool2', 'boolean'),
(19, 7, 'test1_string', 'string'),
(20, 7, 'test1_number', 'number'),
(21, 7, 'test1_bool', 'boolean'),
(34, 2, 'text', 'string'),
(35, 2, 'max_length', 'number'),
(36, 2, 'code_interpreter', 'boolean'),
(37, 1, 'width', 'string'),
(38, 1, 'height', 'number'),
(39, 1, 'prompt', 'boolean');

-- --------------------------------------------------------

--
-- 資料表結構 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL COMMENT '使用者id',
  `email` varchar(255) NOT NULL COMMENT 'Email',
  `password_hash` varchar(255) NOT NULL COMMENT '密碼hash',
  `nickname` varchar(255) NOT NULL COMMENT '使用者的暱稱',
  `profile_image` varchar(255) NOT NULL COMMENT '頭像',
  `type` enum('ADMIN','USER') NOT NULL COMMENT '類型',
  `access_token` varchar(255) DEFAULT NULL COMMENT 'access token',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '建立的時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `nickname`, `profile_image`, `type`, `access_token`, `created_at`) VALUES
(1, 'admin@web.tw', '$2y$10$XiaFZrY1qYxoyWTHhPNZCuGmcqYwMyntTp5IcBKj8RmSOUjEw/9Yy', 'admin', 'storage/profile_image/admin.jpg', 'ADMIN', '291124ddb96730a006d48d4b74fa37f3a1e040dffbe2549b3e0778e7bbf51eb2', '2024-02-22 13:12:55'),
(2, 'user1@web.tw', '$2y$10$jrrtSeChNzqbSDQUFDvJqO0yx1amIPmrVnFs8esgLbiCfKic0XtSi', 'test', 'storage/profile_image/user1.jpg', 'USER', NULL, '2024-02-22 13:13:33'),
(3, 'user2@web.tw', '$2y$10$qSD0IyzYOdtO1z5i6XIiWek5/F8l9tSZD.lmVTzo1jOgCN8wHwUYq', 'user2', 'storage/profile_image/user2.jpg', 'USER', '11d2fc8d527af9f4a399094dc6c86f6e1b6213db2462e8281d342ced736a8dbb', '2024-02-22 13:14:16'),
(4, 'user3@web.tw', '$2y$10$lgCOITQ/NEreWSRMwcsdL.f7t.QJXNQF9U17tD9sMlX9.bWoJPKeG', 'user3', 'storage/profile_image/user3.jpg', 'USER', 'f65e357fb685e07edb37d770d58d2569d5f3ab9d7efbe9185e9f14a8bda7e5a6', '2024-02-22 13:19:30'),
(5, 'test@web.tw', '$2y$10$yZAUvzZFOjonyYf2Lap.yO0onCADupEC8Dl3MABb4UZh7hCs.a5/e', 'test8', '/storage/images/l1gRVPKsWDNjbOWKZ5Gy6x9UxbhoG0OazM89sVbC.png', 'USER', NULL, '2024-05-05 15:57:04'),
(6, 'testuser@web.tw', '$2y$10$.D9W0awPqILHwuKbQ9iajuaMsea6fmAL6.THUfuOlcC/3O7jo/f9i', 'testuser', '/storage/images/ijpqhSijLEZoZIyaTTF2xo3cVhkhz58kxyXdcbjM.png', 'USER', NULL, '2024-05-08 13:32:18');

-- --------------------------------------------------------

--
-- 資料表結構 `user_quota_transactions`
--

CREATE TABLE `user_quota_transactions` (
  `id` int(11) NOT NULL COMMENT '額度紀錄id',
  `user_id` int(11) NOT NULL COMMENT '使用者id',
  `value` int(11) NOT NULL COMMENT '此次操作改變的數量，例如建立帳號時value為10，新增任務時value為-1',
  `reason` enum('CREATE_USER','RECHARGE','CONSUME') NOT NULL COMMENT '原因',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '建立時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `user_quota_transactions`
--

INSERT INTO `user_quota_transactions` (`id`, `user_id`, `value`, `reason`, `created_at`) VALUES
(1, 2, 10, 'CREATE_USER', '2024-02-22 13:25:37'),
(2, 3, 10, 'CREATE_USER', '2024-02-22 13:25:37'),
(3, 4, 10, 'CREATE_USER', '2024-02-22 13:27:35'),
(4, 4, -1, 'CONSUME', '2024-02-22 13:55:52'),
(5, 5, 10, 'CREATE_USER', '2024-05-05 15:57:04'),
(6, 4, 1, 'RECHARGE', '2024-05-05 16:04:51'),
(7, 6, 10, 'CREATE_USER', '2024-05-08 13:32:18');

-- --------------------------------------------------------

--
-- 資料表結構 `workers`
--

CREATE TABLE `workers` (
  `id` int(11) NOT NULL COMMENT 'worker id',
  `name` varchar(255) NOT NULL COMMENT '名稱',
  `asscess_token` text NOT NULL COMMENT 'asscess token',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '建立的時間',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '刪除的時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `workers`
--

INSERT INTO `workers` (`id`, `name`, `asscess_token`, `created_at`, `deleted_at`) VALUES
(1, 'dall_e_3', 'eztffswhcrqhqfwxbj2ej1nl32543s8d', '2024-02-22 13:49:46', NULL),
(2, 'ec2-west-2', '1648o3knsmv9erfzcptb8u7vsemew7hh', '2024-02-22 13:49:46', '2024-05-07 13:52:45'),
(3, 'ec2-southeast-3', 'huvu114vo3d5xy177accriwrh4um1dru', '2024-02-22 13:50:02', NULL),
(4, 'testing', 'yxqsaexxqdmnzcccyanlrcoqqgljnr', '2024-05-10 10:59:12', NULL),
(5, 'testing2', 'ysa144jwl0vagnbkwh9v106wftp6r4', '2024-05-10 11:19:37', NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `worker_task_types`
--

CREATE TABLE `worker_task_types` (
  `id` int(11) NOT NULL COMMENT 'worker可執行的類型id',
  `worker_id` int(11) NOT NULL COMMENT 'worker id',
  `task_type_id` int(11) NOT NULL COMMENT '任務類型id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `worker_task_types`
--

INSERT INTO `worker_task_types` (`id`, `worker_id`, `task_type_id`) VALUES
(4, 2, 3),
(5, 3, 5),
(6, 3, 1),
(7, 3, 2),
(8, 3, 3),
(9, 4, 1),
(10, 4, 2),
(11, 4, 3),
(21, 5, 1),
(22, 5, 2),
(23, 5, 3);

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_type_id` (`task_type_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `worker_id` (`worker_id`);

--
-- 資料表索引 `task_inputs`
--
ALTER TABLE `task_inputs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- 資料表索引 `task_types`
--
ALTER TABLE `task_types`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `task_type_inputs`
--
ALTER TABLE `task_type_inputs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_type_id` (`task_type_id`);

--
-- 資料表索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- 資料表索引 `user_quota_transactions`
--
ALTER TABLE `user_quota_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- 資料表索引 `workers`
--
ALTER TABLE `workers`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `worker_task_types`
--
ALTER TABLE `worker_task_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_type_id` (`task_type_id`),
  ADD KEY `worker_id` (`worker_id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '任務id', AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `task_inputs`
--
ALTER TABLE `task_inputs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '任務輸入欄位id', AUTO_INCREMENT=7;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `task_types`
--
ALTER TABLE `task_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '任務類型id', AUTO_INCREMENT=8;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `task_type_inputs`
--
ALTER TABLE `task_type_inputs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '任務類型輸入id', AUTO_INCREMENT=40;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '使用者id', AUTO_INCREMENT=7;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user_quota_transactions`
--
ALTER TABLE `user_quota_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '額度紀錄id', AUTO_INCREMENT=8;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `workers`
--
ALTER TABLE `workers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'worker id', AUTO_INCREMENT=6;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `worker_task_types`
--
ALTER TABLE `worker_task_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'worker可執行的類型id', AUTO_INCREMENT=24;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`task_type_id`) REFERENCES `task_types` (`id`),
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`worker_id`) REFERENCES `workers` (`id`);

--
-- 資料表的限制式 `task_inputs`
--
ALTER TABLE `task_inputs`
  ADD CONSTRAINT `task_inputs_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`);

--
-- 資料表的限制式 `task_type_inputs`
--
ALTER TABLE `task_type_inputs`
  ADD CONSTRAINT `task_type_inputs_ibfk_1` FOREIGN KEY (`task_type_id`) REFERENCES `task_types` (`id`);

--
-- 資料表的限制式 `user_quota_transactions`
--
ALTER TABLE `user_quota_transactions`
  ADD CONSTRAINT `user_quota_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- 資料表的限制式 `worker_task_types`
--
ALTER TABLE `worker_task_types`
  ADD CONSTRAINT `worker_task_types_ibfk_1` FOREIGN KEY (`task_type_id`) REFERENCES `task_types` (`id`),
  ADD CONSTRAINT `worker_task_types_ibfk_2` FOREIGN KEY (`worker_id`) REFERENCES `workers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
