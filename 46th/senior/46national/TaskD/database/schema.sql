-- =====================================================================
--  第 46 屆全國技能競賽 - 17 網頁設計
--  模組 D - 列車訂票系統   資料庫結構與測試資料
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE IF NOT EXISTS `46_national_moduled`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE `46_national_moduled`;

DROP TABLE IF EXISTS `captcha_answer_region`;
DROP TABLE IF EXISTS `captcha_question`;
DROP TABLE IF EXISTS `booking`;
DROP TABLE IF EXISTS `train_service_day`;
DROP TABLE IF EXISTS `train_stop`;
DROP TABLE IF EXISTS `train`;
DROP TABLE IF EXISTS `train_type`;
DROP TABLE IF EXISTS `station`;
DROP TABLE IF EXISTS `admin_user`;

-- ---------------------------------------------------------------------
-- 車站
-- ---------------------------------------------------------------------
CREATE TABLE `station` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '車站流水號',
    `code`       VARCHAR(32)  NOT NULL                COMMENT '車站英文代碼，做為網址與開放資料的識別字',
    `name`       VARCHAR(32)  NOT NULL                COMMENT '車站中文名稱',
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '建立時間',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_station_code` (`code`) COMMENT '英文代碼不可重複，供網址重寫查詢使用',
    KEY `idx_station_name` (`name`)       COMMENT '以中文名稱排序或搜尋時使用'
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  COMMENT = '車站主檔';

-- ---------------------------------------------------------------------
-- 車種
-- ---------------------------------------------------------------------
CREATE TABLE `train_type` (
    `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT COMMENT '車種流水號',
    `name`       VARCHAR(32)   NOT NULL                COMMENT '車種名稱，例如：區間列車',
    `capacity`   SMALLINT UNSIGNED NOT NULL            COMMENT '乘客承載量，用於計算區間剩餘座位',
    `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '建立時間',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_train_type_name` (`name`) COMMENT '車種名稱不可重複'
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  COMMENT = '車種主檔（區間／快速／磁浮等）';

-- ---------------------------------------------------------------------
-- 列車
-- ---------------------------------------------------------------------
CREATE TABLE `train` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '列車流水號',
    `train_type_id` INT UNSIGNED NOT NULL                COMMENT '所屬車種，對應 train_type.id',
    `code`          VARCHAR(16)  NOT NULL                COMMENT '列車（車次）代碼，不可重複',
    `depart_time`   TIME         NOT NULL                COMMENT '發車站的發車時間，後續各站時刻由此推算',
    `deleted_at`    DATETIME     NULL DEFAULT NULL       COMMENT '軟刪除時間，NULL 表示列車仍在營運；保留紀錄以維持歷史訂票的完整性',
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '建立時間',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_train_code` (`code`)            COMMENT '車次代碼不可重複',
    KEY `idx_train_type` (`train_type_id`)         COMMENT '依車種篩選車次時使用',
    KEY `idx_train_deleted_at` (`deleted_at`)      COMMENT '過濾已刪除列車時使用',
    CONSTRAINT `fk_train_train_type`
        FOREIGN KEY (`train_type_id`) REFERENCES `train_type` (`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  COMMENT = '列車（車次）主檔';

-- ---------------------------------------------------------------------
-- 列車行經車站
-- ---------------------------------------------------------------------
CREATE TABLE `train_stop` (
    `id`                INT UNSIGNED     NOT NULL AUTO_INCREMENT COMMENT '停靠站流水號',
    `train_id`          INT UNSIGNED     NOT NULL COMMENT '所屬列車，對應 train.id',
    `station_id`        INT UNSIGNED     NOT NULL COMMENT '停靠車站，對應 station.id',
    `stop_sequence`     TINYINT UNSIGNED NOT NULL COMMENT '停靠順序，自 1 起算，最多 15 站',
    `travel_minutes`    SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '自前一站行駛到本站所需分鐘數，發車站固定為 0',
    `stop_minutes`      SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '在本站的停留分鐘數，發車站與終點站固定為 0',
    `fare_from_origin`  INT UNSIGNED     NOT NULL DEFAULT 0 COMMENT '自發車站累計至本站的票價，區間票價 = 到達站累計 - 起程站累計',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_train_stop_sequence` (`train_id`, `stop_sequence`) COMMENT '同一列車的停靠順序不可重複',
    UNIQUE KEY `uq_train_stop_station`  (`train_id`, `station_id`)    COMMENT '同一列車不可重複停靠相同車站',
    KEY `idx_train_stop_station` (`station_id`) COMMENT '以車站反查行經列車時使用',
    CONSTRAINT `fk_train_stop_train`
        FOREIGN KEY (`train_id`) REFERENCES `train` (`id`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk_train_stop_station`
        FOREIGN KEY (`station_id`) REFERENCES `station` (`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  COMMENT = '列車行經車站與時刻／票價設定';

-- ---------------------------------------------------------------------
-- 列車行駛星期
-- ---------------------------------------------------------------------
CREATE TABLE `train_service_day` (
    `id`       INT UNSIGNED     NOT NULL AUTO_INCREMENT COMMENT '流水號',
    `train_id` INT UNSIGNED     NOT NULL COMMENT '所屬列車，對應 train.id',
    `weekday`  TINYINT UNSIGNED NOT NULL COMMENT '行駛星期，0=星期日、1=星期一 … 6=星期六',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_train_service_day` (`train_id`, `weekday`) COMMENT '同一列車同一星期只需一筆',
    KEY `idx_train_service_day_weekday` (`weekday`) COMMENT '查詢某日行駛的列車時使用',
    CONSTRAINT `fk_train_service_day_train`
        FOREIGN KEY (`train_id`) REFERENCES `train` (`id`)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  COMMENT = '列車行駛星期（每列車可對應多個星期）';

-- ---------------------------------------------------------------------
-- 訂票紀錄
-- ---------------------------------------------------------------------
CREATE TABLE `booking` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '訂票流水號',
    -- 訂票編號需區分大小寫，故指定 utf8mb4_bin 定序
    `booking_code`     VARCHAR(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
                       COMMENT '訂票編號，12 碼英數字且區分大小寫，不與其他訂票紀錄重複',
    `phone`            VARCHAR(20)  NOT NULL COMMENT '訂票人手機號碼，供查詢與發送簡訊',
    `train_id`         INT UNSIGNED NOT NULL COMMENT '所訂列車，對應 train.id',
    `from_station_id`  INT UNSIGNED NOT NULL COMMENT '起程站，對應 station.id',
    `to_station_id`    INT UNSIGNED NOT NULL COMMENT '到達站，對應 station.id',
    `travel_date`      DATE         NOT NULL COMMENT '乘車日期',
    `depart_at`        DATETIME     NOT NULL COMMENT '起程站的發車時間（訂票當下計算並保留，避免時刻異動影響歷史紀錄）',
    `arrive_at`        DATETIME     NOT NULL COMMENT '到達站的抵達時間（訂票當下計算並保留）',
    `ticket_count`     SMALLINT UNSIGNED NOT NULL COMMENT '車票張數，1~1000',
    `unit_price`       INT UNSIGNED NOT NULL COMMENT '車票單價',
    `total_price`      INT UNSIGNED NOT NULL COMMENT '總票價 = 單價 × 張數',
    `status`           ENUM('BOOKED','CANCELLED') NOT NULL DEFAULT 'BOOKED'
                       COMMENT '訂票狀態：BOOKED 已訂位、CANCELLED 已取消',
    `cancelled_source` ENUM('PASSENGER','ADMIN','TRAIN_REMOVED') NULL DEFAULT NULL
                       COMMENT '取消來源：乘客自行取消、管理員取消、列車被刪除而取消',
    `cancelled_at`     DATETIME     NULL DEFAULT NULL COMMENT '取消時間，未取消為 NULL',
    `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '訂票時間',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_booking_code` (`booking_code`) COMMENT '訂票編號不可重複',
    KEY `idx_booking_phone` (`phone`)             COMMENT '以手機號碼查詢訂票紀錄時使用',
    KEY `idx_booking_train_date` (`train_id`, `travel_date`, `status`)
                                                  COMMENT '計算區間剩餘座位時使用',
    KEY `idx_booking_from_station` (`from_station_id`) COMMENT '統計進站人數時使用',
    KEY `idx_booking_to_station` (`to_station_id`)     COMMENT '統計離站人數時使用',
    KEY `idx_booking_depart_at` (`depart_at`)     COMMENT '判斷是否已發車、以及統計時間分組時使用',
    CONSTRAINT `fk_booking_train`
        FOREIGN KEY (`train_id`) REFERENCES `train` (`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_booking_from_station`
        FOREIGN KEY (`from_station_id`) REFERENCES `station` (`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_booking_to_station`
        FOREIGN KEY (`to_station_id`) REFERENCES `station` (`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  COMMENT = '訂票紀錄';

-- ---------------------------------------------------------------------
-- 後台管理員
-- ---------------------------------------------------------------------
CREATE TABLE `admin_user` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '管理員流水號',
    `username`      VARCHAR(32)  NOT NULL COMMENT '登入帳號',
    `password_hash` VARCHAR(255) NOT NULL COMMENT '以 password_hash() 產生的密碼雜湊，不儲存明文',
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '建立時間',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_admin_user_username` (`username`) COMMENT '帳號不可重複'
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  COMMENT = '後台管理員帳號';

-- ---------------------------------------------------------------------
-- 問答驗證碼題目
-- ---------------------------------------------------------------------
CREATE TABLE `captcha_question` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '題目流水號',
    `image_file` VARCHAR(64)  NOT NULL COMMENT '題目圖片檔名，位於 assets/captcha/',
    `question`   VARCHAR(128) NOT NULL COMMENT '題目敘述，例如：請選擇畫面中所有的汽車',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_captcha_question_image` (`image_file`) COMMENT '一張圖片對應一道題目'
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  COMMENT = '問答驗證碼題目';

-- ---------------------------------------------------------------------
-- 問答驗證碼答案區塊
-- ---------------------------------------------------------------------
CREATE TABLE `captcha_answer_region` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '答案區塊流水號',
    `question_id` INT UNSIGNED NOT NULL COMMENT '所屬題目，對應 captcha_question.id',
    `x`           SMALLINT UNSIGNED NOT NULL COMMENT '答案區塊左上角 X 座標（以圖片原始尺寸為準）',
    `y`           SMALLINT UNSIGNED NOT NULL COMMENT '答案區塊左上角 Y 座標',
    `width`       SMALLINT UNSIGNED NOT NULL COMMENT '答案區塊寬度',
    `height`      SMALLINT UNSIGNED NOT NULL COMMENT '答案區塊高度',
    PRIMARY KEY (`id`),
    KEY `idx_captcha_answer_question` (`question_id`) COMMENT '取出某題所有答案區塊時使用',
    CONSTRAINT `fk_captcha_answer_question`
        FOREIGN KEY (`question_id`) REFERENCES `captcha_question` (`id`)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  COMMENT = '問答驗證碼的正確答案區塊（一題可有多個目標物件）';

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
--  測試資料
-- =====================================================================

INSERT INTO `station` (`id`, `code`, `name`) VALUES
    ( 1, 'KEELUNG',   '基隆'),
    ( 2, 'TAIPEI',    '台北'),
    ( 3, 'BANQIAO',   '板橋'),
    ( 4, 'TAOYUAN',   '桃園'),
    ( 5, 'HSINCHU',   '新竹'),
    ( 6, 'MIAOLI',    '苗栗'),
    ( 7, 'TAICHUNG',  '台中'),
    ( 8, 'CHANGHUA',  '彰化'),
    ( 9, 'YUNLIN',    '雲林'),
    (10, 'CHIAYI',    '嘉義'),
    (11, 'TAINAN',    '台南'),
    (12, 'KAOHSIUNG', '高雄'),
    (13, 'PINGTUNG',  '屏東'),
    (14, 'TAITUNG',   '台東'),
    (15, 'HUALIEN',   '花蓮'),
    (16, 'YILAN',     '宜蘭');

INSERT INTO `train_type` (`id`, `name`, `capacity`) VALUES
    (1, '區間列車', 100),
    (2, '快速列車', 100),
    (3, '磁浮列車', 100);

-- 內建管理員帳號 admin / 1234
INSERT INTO `admin_user` (`id`, `username`, `password_hash`) VALUES
    (1, 'admin', '$2y$10$HPE5.7DpUPEkY/q1IpK87OybMQKhJ3wBceWuDSsp0DW3zT3Fkhuna');

-- ---------------------------------------------------------------------
-- 列車測試資料
-- ---------------------------------------------------------------------
INSERT INTO `train` (`id`, `train_type_id`, `code`, `depart_time`) VALUES
    (1, 1, '1101', '06:00:00'),
    (2, 1, '1102', '08:30:00'),
    (3, 2, '2201', '07:00:00'),
    (4, 2, '2202', '13:20:00'),
    (5, 3, '3301', '09:00:00'),
    (6, 3, '3302', '16:40:00'),
    (7, 2, '2203', '10:15:00'),
    (8, 1, '1103', '18:05:00');

-- 行駛星期（0=日 … 6=六）
INSERT INTO `train_service_day` (`train_id`, `weekday`) VALUES
    -- 1101 每日行駛
    (1,0),(1,1),(1,2),(1,3),(1,4),(1,5),(1,6),
    -- 1102 平日行駛
    (2,1),(2,2),(2,3),(2,4),(2,5),
    -- 2201 每日行駛
    (3,0),(3,1),(3,2),(3,3),(3,4),(3,5),(3,6),
    -- 2202 週末行駛
    (4,0),(4,6),
    -- 3301 每日行駛
    (5,0),(5,1),(5,2),(5,3),(5,4),(5,5),(5,6),
    -- 3302 平日行駛
    (6,1),(6,2),(6,3),(6,4),(6,5),
    -- 2203 每日行駛
    (7,0),(7,1),(7,2),(7,3),(7,4),(7,5),(7,6),
    -- 1103 每日行駛
    (8,0),(8,1),(8,2),(8,3),(8,4),(8,5),(8,6);

-- 行經車站（travel_minutes = 自前一站行駛時間、stop_minutes = 停留時間、fare_from_origin = 累計票價）
INSERT INTO `train_stop`
    (`train_id`, `station_id`, `stop_sequence`, `travel_minutes`, `stop_minutes`, `fare_from_origin`) VALUES
    -- 1101 區間列車：基隆 → 台北 → 板橋 → 桃園 → 新竹 → 苗栗 → 台中
    (1,  1, 1,  0, 0,   0),
    (1,  2, 2, 45, 3,   45),
    (1,  3, 3, 12, 2,   62),
    (1,  4, 4, 25, 3,  100),
    (1,  5, 5, 35, 3,  156),
    (1,  6, 6, 30, 2,  205),
    (1,  7, 7, 40, 0,  270),

    -- 1102 區間列車：台中 → 彰化 → 雲林 → 嘉義 → 台南 → 高雄
    (2,  7, 1,  0, 0,   0),
    (2,  8, 2, 18, 2,   26),
    (2,  9, 3, 32, 3,   72),
    (2, 10, 4, 30, 3,  116),
    (2, 11, 5, 50, 3,  190),
    (2, 12, 6, 45, 0,  256),

    -- 2201 快速列車：台北 → 桃園 → 新竹 → 台中 → 嘉義 → 台南 → 高雄
    (3,  2, 1,  0, 0,   0),
    (3,  4, 2, 22, 2,   66),
    (3,  5, 3, 28, 2,  145),
    (3,  7, 4, 55, 3,  300),
    (3, 10, 5, 48, 3,  435),
    (3, 11, 6, 40, 2,  545),
    (3, 12, 7, 35, 0,  645),

    -- 2202 快速列車：台北 → 宜蘭 → 花蓮 → 台東
    (4,  2, 1,  0, 0,   0),
    (4, 16, 2, 70, 4,  218),
    (4, 15, 3, 95, 4,  480),
    (4, 14, 4,120, 0,  790),

    -- 3301 磁浮列車：台北 → 台中 → 高雄
    (5,  2, 1,  0, 0,   0),
    (5,  7, 2, 55, 5,  700),
    (5, 12, 3, 65, 0, 1490),

    -- 3302 磁浮列車：高雄 → 台南 → 台中 → 台北
    (6, 12, 1,  0, 0,   0),
    (6, 11, 2, 20, 3,  250),
    (6,  7, 3, 45, 5,  790),
    (6,  2, 4, 60, 0, 1490),

    -- 2203 快速列車：高雄 → 屏東 → 台東 → 花蓮
    (7, 12, 1,  0, 0,   0),
    (7, 13, 2, 30, 3,   95),
    (7, 14, 3,110, 4,  430),
    (7, 15, 4, 95, 0,  720),

    -- 1103 區間列車：台北 → 板橋 → 桃園 → 新竹
    (8,  2, 1,  0, 0,   0),
    (8,  3, 2, 12, 2,   17),
    (8,  4, 3, 25, 3,   55),
    (8,  5, 4, 35, 0,  111);

-- ---------------------------------------------------------------------
-- 問答驗證碼題目與答案區塊
-- ---------------------------------------------------------------------
INSERT INTO `captcha_question` (`id`, `image_file`, `question`) VALUES
    (1, '01.jpg', '請選擇畫面中所有的汽車'),
    (2, '02.jpg', '請選擇畫面中所有的交通號誌'),
    (3, '03.jpg', '請選擇畫面中所有的腳踏車'),
    (4, '04.jpg', '請選擇畫面中所有的公車'),
    (5, '05.jpg', '請選擇畫面中所有的樹木'),
    (6, '06.jpg', '請選擇畫面中所有的房屋');

INSERT INTO `captcha_answer_region` (`question_id`, `x`, `y`, `width`, `height`) VALUES
    (1, 60, 250, 130, 72),
    (1, 400, 236, 150, 84),
    (2, 90, 130, 34, 88),
    (2, 300, 145, 30, 78),
    (2, 520, 125, 36, 93),
    (3, 110, 240, 120, 84),
    (3, 430, 248, 110, 77),
    (4, 50, 196, 160, 124),
    (4, 380, 210, 150, 117),
    (5, 70, 150, 90, 130),
    (5, 290, 176, 76, 110),
    (5, 500, 140, 96, 139),
    (6, 60, 170, 120, 120),
    (6, 260, 190, 100, 100),
    (6, 460, 160, 130, 130);
