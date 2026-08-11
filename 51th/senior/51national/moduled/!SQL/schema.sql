-- ==========================================================================
-- 第51屆全國技能競賽 網頁技術 模組D - 房屋交易平台
-- 資料表結構與測試資料
-- 資料表設計依照試題 D.3「資料結構設計」的 ER 圖
-- ==========================================================================

CREATE DATABASE IF NOT EXISTS `worldskill51_moduled`
    DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `worldskill51_moduled`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `ads`;
DROP TABLE IF EXISTS `applications`;
DROP TABLE IF EXISTS `images`;
DROP TABLE IF EXISTS `houses`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------------------------
-- 使用者
-- --------------------------------------------------------------------------
CREATE TABLE `users` (
    `id`       INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `email`    VARCHAR(255) NOT NULL COMMENT 'Email',
    `password` VARCHAR(255) NOT NULL COMMENT '密碼（bcrypt 雜湊）',
    `nickname` VARCHAR(255) NOT NULL COMMENT '暱稱',
    `role`     ENUM('ADMIN','USER') NOT NULL DEFAULT 'USER' COMMENT '身分',
    `token`    CHAR(64) DEFAULT NULL COMMENT '登入 Token（sha256 過的 email）',
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`),
    UNIQUE KEY `users_token_unique` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='使用者';

-- --------------------------------------------------------------------------
-- 房屋
-- --------------------------------------------------------------------------
CREATE TABLE `houses` (
    `id`           INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`      INT(11) UNSIGNED NOT NULL COMMENT '刊登者',
    `title`        VARCHAR(255) NOT NULL COMMENT '標題',
    `description`  TEXT COMMENT '描述',
    `price`        INT(11) NOT NULL COMMENT '價格',
    `square`       MEDIUMINT(9) NOT NULL COMMENT '坪數',
    `room`         MEDIUMINT(9) NOT NULL COMMENT '房數',
    `floor`        MEDIUMINT(9) NOT NULL COMMENT '樓層',
    `total_floor`  MEDIUMINT(9) NOT NULL COMMENT '總樓層',
    `age`          MEDIUMINT(9) NOT NULL COMMENT '屋齡',
    `address`      TEXT COMMENT '地址',
    `published_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '刊登時間',
    PRIMARY KEY (`id`),
    KEY `houses_user_id_index` (`user_id`),
    KEY `houses_price_index` (`price`),
    KEY `houses_square_index` (`square`),
    KEY `houses_room_index` (`room`),
    KEY `houses_age_index` (`age`),
    KEY `houses_published_at_index` (`published_at`),
    CONSTRAINT `houses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='房屋';

-- --------------------------------------------------------------------------
-- 房屋圖片（sort_order 由 0 開始，is_cover 標記封面）
-- --------------------------------------------------------------------------
CREATE TABLE `images` (
    `house_id`   INT(11) UNSIGNED NOT NULL COMMENT '房屋ID',
    `sort_order` INT(11) NOT NULL DEFAULT 0 COMMENT '排序（由 0 開始）',
    `path`       MEDIUMTEXT NOT NULL COMMENT '圖片相對路徑',
    `is_cover`   TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否為封面圖',
    PRIMARY KEY (`house_id`, `sort_order`),
    CONSTRAINT `images_house_id_foreign` FOREIGN KEY (`house_id`) REFERENCES `houses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='房屋圖片';

-- --------------------------------------------------------------------------
-- 精選房屋申請（status 為 NULL 代表審核中）
-- --------------------------------------------------------------------------
CREATE TABLE `applications` (
    `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `house_id`   INT(11) UNSIGNED NOT NULL COMMENT '房屋ID',
    `status`     ENUM('APPROVE','REJECT') DEFAULT NULL COMMENT '審核狀態，NULL 為審核中',
    `applied_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '申請時間',
    PRIMARY KEY (`id`),
    KEY `applications_house_id_index` (`house_id`),
    CONSTRAINT `applications_house_id_foreign` FOREIGN KEY (`house_id`) REFERENCES `houses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='精選房屋申請';

-- --------------------------------------------------------------------------
-- 精選房屋
-- --------------------------------------------------------------------------
CREATE TABLE `ads` (
    `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `house_id`   INT(11) UNSIGNED NOT NULL COMMENT '房屋ID',
    `expired_at` DATETIME NOT NULL COMMENT '到期時間',
    PRIMARY KEY (`id`),
    KEY `ads_house_id_index` (`house_id`),
    CONSTRAINT `ads_house_id_foreign` FOREIGN KEY (`house_id`) REFERENCES `houses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='精選房屋';

-- ==========================================================================
-- 測試資料
-- 密碼皆以 bcrypt 雜湊儲存，對應明碼如下：
--   admin@localhost / adminpass （管理員）
--   user1@localhost ~ user5@localhost / user1pass ~ user5pass （會員）
-- ==========================================================================
INSERT INTO `users` (`id`, `email`, `password`, `nickname`, `role`, `token`) VALUES
(1, 'admin@localhost', '$2y$10$kV7gePekX0j9cZeUBFnq3esZUnnzbIpBgLtWSE1JHRghnkerpd7NK', 'admin', 'ADMIN', NULL),
(2, 'user1@localhost', '$2y$10$BK07hdJB/oD9/yOI4gKDx.XnuoRlGwETqxREhX9gBmkS5JzQFeqy6', 'user1', 'USER', NULL),
(3, 'user2@localhost', '$2y$10$xIgLwitLig0LZzxd1dxLWejZWlHtffW8joCxotPoqip/4GzfPAUGG', 'user2', 'USER', NULL),
(4, 'user3@localhost', '$2y$10$.uGAjPNy.lYFEjVvKsryq.uydSYsU.BJ5aI1YNexs4CbENX35gMhC', 'user3', 'USER', NULL),
(5, 'user4@localhost', '$2y$10$JDLM9mbSwm.AbvpFvbeAcebiZVUyrcWWGSHwopxs7qChuqnASZGDy', 'user4', 'USER', NULL),
(6, 'user5@localhost', '$2y$10$V6M8WCn2FSeNbpZPGBSE0uWStY/0UkzchbGiPBeCTfk9RqxC03phy', 'user5', 'USER', NULL);

-- 房屋測試資料（共 24 筆，足以測試每頁 10 筆的分頁功能）
INSERT INTO `houses`
    (`id`, `user_id`, `title`, `description`, `price`, `square`, `room`, `floor`, `total_floor`, `age`, `address`, `published_at`) VALUES
(1,  2, 'My adorable apartment',    '採光極佳的市區公寓，鄰近捷運站與商圈，生活機能便利。', 10000000, 20,  2, 3,  12, 3,  '桃園市中壢區萬能路 1 號',    '2024-10-05 12:00:00'),
(2,  2, 'Sweet price villa',        '獨棟別墅附庭院與車位，安靜的住宅區，適合家庭居住。',   100000000, 100, 6, 1,  3,  8,  '臺北市大安區信義路四段 8 號', '2024-10-06 09:30:00'),
(3,  3, 'The wonderful apartment',  '三面採光的邊間，格局方正，屋況維持良好。',             16800000, 30,  3, 8,  15, 12, '新北市板橋區文化路一段 88 號', '2024-10-07 15:20:00'),
(4,  3, 'Riverside studio',         '河岸第一排景觀套房，夜景優美，適合單身族。',           6800000,  12,  1, 14, 20, 5,  '新北市新店區環河路 12 號',    '2024-10-08 11:00:00'),
(5,  4, 'Sunny family house',       '雙陽台三房兩廳，學區房，附近有公園與市場。',           12800000, 35,  3, 5,  7,  18, '臺中市西屯區青海路二段 55 號', '2024-10-09 08:45:00'),
(6,  4, 'Downtown penthouse',       '頂樓景觀戶附露臺，可眺望市區天際線。',                 38000000, 60,  4, 18, 18, 2,  '高雄市前金區中正四路 21 號',  '2024-10-10 16:10:00'),
(7,  5, 'Quiet garden flat',        '一樓附庭院，適合毛小孩，社區管理完善。',               9800000,  25,  2, 1,  6,  22, '臺南市東區崇明路 9 號',       '2024-10-11 10:05:00'),
(8,  5, 'Modern loft',              '挑高樓中樓設計，附全套系統家具，可立即入住。',         15600000, 28,  2, 9,  14, 6,  '臺北市內湖區成功路四段 33 號', '2024-10-12 13:40:00'),
(9,  6, 'Classic townhouse',        '傳統透天厝，三層樓格局寬敞，附前後院。',               22000000, 55,  5, 1,  3,  30, '新竹市東區光復路二段 66 號',  '2024-10-13 09:15:00'),
(10, 6, 'Seaview apartment',        '一線海景，窗戶面海無遮蔽，度假首選。',            18800000, 32,  3, 11, 15, 9,  '基隆市中正區中正路 168 號',   '2024-10-14 17:25:00'),
(11, 2, 'Campus corner house',      '緊鄰大學校區，租賃需求穩定，投資自住兩相宜。',         8600000,  18,  2, 4,  7,  25, '桃園市中壢區中大路 300 號',   '2024-10-15 08:00:00'),
(12, 2, 'Metro front residence',    '捷運出口步行 1 分鐘，交通極為便利。',                 25600000, 40,  3, 7,  16, 4,  '臺北市中山區南京東路三段 2 號', '2024-10-16 19:30:00'),
(13, 3, 'Mountain retreat',         '依山而建的度假宅，空氣清新，遠離塵囂。',               7200000,  22,  2, 2,  4,  16, '南投縣埔里鎮中山路三段 7 號', '2024-10-17 07:50:00'),
(14, 3, 'Business district office',  '可住可辦的商辦兩用宅，臨大馬路。',                    31000000, 45,  4, 10, 20, 11, '臺中市南屯區公益路二段 99 號', '2024-10-18 14:05:00'),
(15, 4, 'Cozy small suite',         '小資族首選的精緻套房，總價低易入手。',                 4200000,  8,   1, 6,  12, 20, '高雄市三民區建國二路 5 號',   '2024-10-19 12:35:00'),
(16, 4, 'Elegant duplex',           '樓中樓雙主臥設計，適合兩代同堂。',                     28800000, 52,  4, 12, 14, 7,  '新北市中和區中山路二段 121 號', '2024-10-20 10:20:00'),
(17, 5, 'Park side apartment',      '正對公園第一排，綠意環繞，視野開闊。',                 19800000, 33,  3, 8,  13, 10, '臺北市士林區文林路 45 號',    '2024-10-21 18:00:00'),
(18, 5, 'Renovated old house',      '老屋翻新，管線全換，屋況如新成屋。',                   11500000, 26,  3, 3,  5,  35, '嘉義市西區垂楊路 88 號',      '2024-10-22 09:55:00'),
(19, 6, 'Luxury sky mansion',       '豪宅一層一戶，飯店式管理，門禁森嚴。',                 88000000, 90,  5, 25, 30, 3,  '臺北市信義區松高路 11 號',    '2024-10-23 15:45:00'),
(20, 6, 'Farmhouse with land',      '農舍附大面積土地，適合退休生活。',                     13800000, 48,  4, 1,  2,  14, '宜蘭縣員山鄉八甲路 20 號',    '2024-10-24 08:30:00'),
(21, 2, 'Compact city studio',      '市中心迷你套房，租金投報率佳。',                       3980000,  7,   1, 5,  11, 19, '臺北市萬華區西寧南路 6 號',   '2024-10-25 11:15:00'),
(22, 3, 'Family friendly flat',     '方正三房，鄰近國小與圖書館。',                         14200000, 31,  3, 6,  12, 13, '桃園市桃園區莊敬路一段 77 號', '2024-10-26 16:40:00'),
(23, 4, 'Airport line house',       '機捷沿線新成屋，通勤族最愛。',                         17600000, 34,  3, 9,  15, 1,  '桃園市蘆竹區南崁路 150 號',   '2024-10-27 13:10:00'),
(24, 5, 'Historic street house',    '老街商圈店面住家，人潮絡繹不絕。',                     26500000, 42,  4, 1,  4,  40, '臺南市中西區民權路二段 30 號', '2024-10-28 10:00:00');

-- 房屋圖片：每間房屋 3 張，第一張預設為封面
-- 圖片檔案由 !SQL/generate_images.php 產生於 uploads/ 目錄
INSERT INTO `images` (`house_id`, `sort_order`, `path`, `is_cover`)
SELECT `id`, 0, CONCAT('uploads/sample-', `id`, '-1.png'), 1 FROM `houses`
UNION ALL
SELECT `id`, 1, CONCAT('uploads/sample-', `id`, '-2.png'), 0 FROM `houses`
UNION ALL
SELECT `id`, 2, CONCAT('uploads/sample-', `id`, '-3.png'), 0 FROM `houses`;

-- 精選房屋申請：2 筆審核中、1 筆已同意、1 筆已拒絕
INSERT INTO `applications` (`id`, `house_id`, `status`, `applied_at`) VALUES
(1, 2,  'APPROVE', '2024-10-29 09:00:00'),
(2, 19, 'APPROVE', '2024-10-29 09:30:00'),
(3, 3,  NULL,      '2024-10-30 10:00:00'),
(4, 10, NULL,      '2024-10-30 11:20:00'),
(5, 15, 'REJECT',  '2024-10-30 12:00:00');

-- 精選房屋：由已同意的申請產生，到期時間設為目前時間加 7 天
INSERT INTO `ads` (`id`, `house_id`, `expired_at`) VALUES
(1, 2,  DATE_ADD(NOW(), INTERVAL 7 DAY)),
(2, 19, DATE_ADD(NOW(), INTERVAL 5 DAY));
